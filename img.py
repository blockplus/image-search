import hashlib
import cv2
import numpy
import sqlite3
import pickle
import os
from datetime import datetime

#max number of images in each matrix, for parallel processing
DESC_MAX_LEN = 100000
#sqlite db for persistence
BANK_FILENAME = 'bank.db'
MATCH_THRESHOLD = 130
'''
note the licensing issues with using SURF/SIFT, alternatives are FREAK, BRISK for
feature detection
'''
def get_surf_des(filename):
    f = cv2.imread(filename)
    #hessian threshold 800, 64 not 128
    surf = cv2.SURF(800, extended=False)
    kp, des = surf.detectAndCompute(f, None)
    return kp, des

def get_conn():
    return sqlite3.connect('bank.db')

def get_sim2score(sim):
    max_match = MATCH_THRESHOLD
    total_match = min(max_match, sim)
    return round((total_match) * 100.0 / max_match, 2)

def get_bank_image(thumb_file):
    #return thumb_file
    head, filename = os.path.split(thumb_file)
    dir_name, tail2 = os.path.split(head)
    return os.path.join(dir_name, filename)

def get_main_image(thumb_file):
    head, filename = os.path.split(thumb_file)
    dir_name, tail2 = os.path.split(head)
    return os.path.join(dir_name, filename)

def match(filename1, filename2):
    kp1, to_match1 = get_surf_des(filename1)
    kp2, to_match2 = get_surf_des(filename2)
    
    index_params = dict(algorithm=1,trees=4)
    flann = cv2.FlannBasedMatcher(index_params,dict())
    matches = flann.knnMatch(to_match1, to_match2, k=4)

    count = 0
    for i in xrange(0, len(matches)):
        match = matches[i]
        if match[0].distance < (.6 * match[1].distance):
            count = count + 1
    return count

class _img:
    def __init__(self):
        self.imap = []
        self.r = 0
        self.descs = []
        index_params = dict(algorithm=1,trees=4)
        self.flann = cv2.FlannBasedMatcher(index_params,dict())

    def add_image(self, filename, title, description, link, des=None):
        if des is None:
            kv, des = get_surf_des(filename)
        self.imap.append({
            'index_start' : self.r,
            'index_end' : self.r + des.shape[0] - 1,
            'file_name' : filename,
            'title' : title,
            'description' : description,
            'link' : link
        })
        self.r += des.shape[0]
        #it's really slow to do a vstack every time, so just maintain a list and
        #replicate it as a concatenated numpy ndarray every time. an optimization
        #would be to do a numpy.vstack((self.descs, numpy,array(des))) where self.descs
        #is a numpy.array
        self.descs.append(des)

    def edit_item(self, filename, title, description, link):
        for item in self.imap:
            if item['file_name'] == filename:
                item['title'] = title
                item['description'] = description
                item['link'] = link

    def match(self, filename):
        kp, to_match = get_surf_des(filename)
        img_db = numpy.vstack(numpy.array(self.descs))
        #this should be reversed, need to update distance calculation
        matches = self.flann.knnMatch(img_db, to_match, k=4)

        sim = dict()
        for img in self.imap:
            item = dict()
            item["image"] = img['file_name']
            item["thumb"] = img['file_name']
            item["title"] = img['title']
            item["description"] = img['description']
            item["link"] = img['link']
            item["similarity"] = 0
            sim[img['file_name']] = item
        for i in xrange(0, len(matches)):
            match = matches[i]
            if match[0].distance < (.6 * match[1].distance):
                for img in self.imap:
                    if img['index_start'] <= i and img['index_end'] >= i:
                        sim[img['file_name']]['similarity'] += 1
            
        return sim

    def __len__(self):
        return len(self.descs)

class img:
    def __init__(self):
        self.ims = [_img()]
        self.count = 0

    def get_count(self):
        return self.count

    def add_image(self, filename, title, description, link, des=None):
        self.count += 1
        self.ims[-1].add_image(filename, title, description, link, des=des)
        if len(self.ims[-1]) > DESC_MAX_LEN:
            self.ims.append(_img())
    
    def edit_item(self, filename, title, description, link):
        import multiprocessing.dummy
        p = multiprocessing.dummy.Pool(10)
        def f(instance):
            return instance.edit_item(filename, title, description, link)
        res = p.map(f, [i for i in self.ims])

    def match(self, filename, start, count):
        import multiprocessing.dummy
        p = multiprocessing.dummy.Pool(10)

        def f(instance):
            return instance.match(filename)

        res = p.map(f, [i for i in self.ims])

        sim = dict((k,v) for d in res for (k,v) in d.items())
        sorted_sim = sorted(sim.items(), key=lambda x:x[1]['similarity'], reverse=True)

        sorted_sim = [x[1] for x in sorted_sim]

        sorted_sim = filter(lambda x:x['similarity'] > 5, sorted_sim)
        total_count = len(sorted_sim)

        sorted_sim = sorted_sim[start: start+count]

        for item in sorted_sim:
            item["similarity"] = get_sim2score(item["similarity"])
        
        return sorted_sim, total_count

class persisted_img(img):
    def __init__(self):
        #optimization, should additionally wrap img once more instead, so it works without persistence
        img.__init__(self)
        with get_conn() as conn:
            c = conn.cursor()
            c.execute('''CREATE TABLE IF NOT EXISTS descs
                        (filename, title, description, link, des,kp)
                        ''')
            conn.commit()
            c.execute(
                '''
                SELECT filename, title, description, link, des
                FROM descs
            ''')
            while True:
                row = c.fetchone()
                if not row:
                    break
                filename = row[0]
                title = row[1]
                description = row[2]
                link = row[3]
                des = pickle.loads(str(row[4]))
                print 'img.__init__: loading descriptor for file %s from db' % (filename)
                if des is None:
                    print 'img.__init__: error loading descriptor for %s from db' % (filename)
                    continue
                img.add_image(self, filename, title, description, link, des=des)

    def add_image(self, filename, title, description, link, des=None):
        if des is None:
            kv, des = get_surf_des(filename)
        with get_conn() as conn:
            c = conn.cursor()
            data = sqlite3.Binary(pickle.dumps(des, pickle.HIGHEST_PROTOCOL))
            c.execute('''
                INSERT INTO descs(filename, title, description, link, des) VALUES (?, ?, ?, ?, :data)
                ''',
                [filename, title, description, link, data]
            )
            conn.commit()
        img.add_image(self, filename, title, description, link, des=des)

    def edit_item(self, title, description, link, filename):
        with get_conn() as conn:
            cursor = conn.cursor()
            cursor.execute("""
                UPDATE descs SET title = ? ,description = ?,link = ? WHERE filename= ? 
                """, (title,description,link,filename))
            conn.commit()
        img.edit_item(self, filename, title, description, link)

    def delete_item(self, filename):
        with get_conn() as conn:
            sql = 'DELETE FROM descs WHERE filename=?'
            cursor = conn.cursor()
            cursor.execute(sql, (filename,))
            conn.commit()
            os.remove(filename)
            os.remove(get_main_image(filename))
            self.__init__()

    def can_add(self, tmpfile_thumb, title, description, link):
        with get_conn() as conn:
            c = conn.cursor()
            c.execute(
                '''
                SELECT filename, title, description, link
                FROM descs WHERE title=? and description=? and link=?
            ''', (title, description, link))
            
            while True:
                row = c.fetchone()
                if not row:
                    break

                filename = row[0]
                sim = match(filename, tmpfile_thumb)

                if sim >= MATCH_THRESHOLD:
                    return False
        return True

class BatchItem(object):
    def __init__(self, filename, title, description, link):
        self.filename = filename
        self.title = title
        self.description = description
        self.link = link

    def __str__(self):
        return("BatchItem object:\n"
               "  filename = {0}\n"
               "  title = {1}\n"
               "  description = {2}\n"
               "  link = {3}\n"
               .format(self.filename, self.title, self.description,
                       self.link))

def get_browse_images(start, count):
    items = []
    with get_conn() as conn:
        c = conn.cursor()
        c.execute('''CREATE TABLE IF NOT EXISTS descs
                    (filename, title, description, link, des,kp)
                    ''')
        conn.commit()
        c.execute(
            '''
            SELECT filename, title, description, link
            FROM descs LIMIT ?, ?
        ''', [start, count])
        while True:
            row = c.fetchone()
            if not row:
                break
            filename = row[0]
            title = row[1]
            description = row[2]
            link = row[3]
            item = dict()
            item["image"] = filename
            item["title"] = title
            item["description"] = description
            item["link"] = link
            items.append(item)
    return items
        
def register_user(firstname, lastname, username, password):
    with get_conn() as conn:
        c = conn.cursor()
        c.execute('''CREATE TABLE IF NOT EXISTS users
                    (firstname, lastname, username, password, admin)
                    ''')
        conn.commit()
        c.execute(
            '''
            SELECT username FROM users
            WHERE username = ?
            ''', [username,])

        row = c.fetchone()
        if row:
           return "existing"

        pwd = hashlib.md5( password ).hexdigest()
        c.execute('''
            INSERT INTO users(firstname, lastname, username, password, admin) VALUES (?, ?, ?, ?, 1)
            ''',
            [firstname, lastname, username, pwd]
        )
        conn.commit()
        return "ok"
    return 'error'

def get_login_user_info(username, password):
    with get_conn() as conn:
        c = conn.cursor()
        c.execute('''CREATE TABLE IF NOT EXISTS users
                    (firstname, lastname, username, password, admin)
                    ''')
        conn.commit()
        c.execute(
            '''
            SELECT username, password FROM users
            WHERE username = ?
            ''', [username, ])

        row = c.fetchone()
        if not row:
           return "invalid username", None

        if hashlib.md5( password ).hexdigest() != row[1]:
           return "invalid password", None

        return "ok", username
    return 'error', None

def get_content_from_key(key):
    with get_conn() as conn:
        c = conn.cursor()
        c.execute('''CREATE TABLE IF NOT EXISTS tbl_contents
                    (key, content)
                    ''')
        conn.commit()
        c.execute(
            '''
            SELECT content FROM tbl_contents
            WHERE key = ?
            ''', [key,])
        row = c.fetchone()
        if row:
           return row[0]
    return ''

def save_content_with_key(key, content):
    with get_conn() as conn:
        c = conn.cursor()
        c.execute('''CREATE TABLE IF NOT EXISTS tbl_contents
                    (key, content)
                    ''')
        conn.commit()
        c.execute(
            '''
            SELECT content FROM tbl_contents
            WHERE key = ?
            ''', (key,))
        row = c.fetchone()
        if row:
            c.execute("""
                UPDATE tbl_contents SET content = ? WHERE key= ? 
                """, (content,key))
            conn.commit()
        else:
            c.execute('''
                INSERT INTO tbl_contents(key, content) VALUES (?, ?)
                ''',[key, content])
            conn.commit()
        return True
    return False

def get_contents_all_func():
    with get_conn() as conn:
        c = conn.cursor()
        c.execute('''CREATE TABLE IF NOT EXISTS tbl_contents
                    (key, content)
                    ''')
        conn.commit()
        c.execute(
            '''
            SELECT key, content FROM tbl_contents
            ''')
        result = dict()
        while True:
            row = c.fetchone()
            if not row:
                break
            result[row[0]] = row[1]
        return result
    return None

def conn_add_advertise_image(file, url):
    with get_conn() as conn:
        c = conn.cursor()
        c.execute('''CREATE TABLE IF NOT EXISTS tbl_ads
                    (filename, url, no)
                    ''')
        conn.commit()
        c.execute('''
            INSERT INTO tbl_ads(filename, url) VALUES (?, ?)
            ''',[file, url])
        conn.commit()
        return True
    return False

def get_browse_images_advertise_conn():
    items = []
    with get_conn() as conn:
        c = conn.cursor()
        c.execute('''CREATE TABLE IF NOT EXISTS tbl_ads
                    (filename, url, no)
                    ''')
        conn.commit()
        c.execute(
            '''
            SELECT filename, url, no
            FROM tbl_ads
        ''')
        while True:
            row = c.fetchone()
            if not row:
                break
            filename = row[0]
            url = row[1]
            no = row[2]
            item = dict()
            item["image"] = filename
            item["url"] = url
            item["no"] = no

            items.append(item)
    return items
        
def delete_item_advertise_conn(filename):
    with get_conn() as conn:
        sql = 'DELETE FROM tbl_ads WHERE filename=?'
        cursor = conn.cursor()
        cursor.execute(sql, (filename,))
        conn.commit()
        os.remove(filename)
        os.remove(get_main_image(filename))
        return True
    return False
