from PIL import Image
import urllib
import os
from os import listdir
from os.path import isfile, join
import traceback
import json
import uuid
import re
import tempfile
import wand.image
import wand.display
import wand.exceptions
from xlrd import open_workbook
from flask import Flask,render_template, send_from_directory, request, session
application = Flask(__name__, static_url_path='')

#local stuff
from img import persisted_img, BatchItem, get_browse_images, register_user, get_login_user_info, save_content_with_key, get_content_from_key, get_contents_all_func, conn_add_advertise_image, get_browse_images_advertise_conn, delete_item_advertise_conn

im = persisted_img()

BANK_PATH = 'static/bank'
BANK_THUMB_PATH = join(BANK_PATH,'thumb')

EXCEL_PATH = 'batch_images'
BATCH_IMAGE_PATH = 'batch_images'

SEARCH_IMAGE_PATH = 'static/search'
SEARCH_IMAGE_THUMB_PATH = 'static/search/thumb'

AD_IMAGE_PATH = 'static/advertise'
AD_IMAGE_THUMB_PATH = 'static/advertise/thumb'

def check_make_dir(directory):
    if not os.path.exists(directory):
        os.makedirs(directory)

def get_images(path):
    #this isn't very robust, oh well
    return filter(
        lambda x : re.search('\.(jpg|jpeg|png)', x.lower()) != None,
        [join(path, f) for f in listdir(path) if isfile(join(path,f))]
    )

def get_thumb_from_image(image):
    path, filename = os.path.split(image)
    thumb = join(BANK_THUMB_PATH, filename)
    return thumb

def get_bank_images():
    return get_images(BANK_PATH)

def get_thumb_images():
    return get_images(BANK_THUMB_PATH)

def get_ratio_size(w, h, size):
    if w > h:
        w0 = size
        h0 = size * h / w
    else:
        h0 = size
        w0 = size * w / h
    return [w0, h0]

def get_ratio_size_big(w, h):
    return get_ratio_size(w, h, 320)

def get_ratio_size_small(w, h):
    return get_ratio_size(w, h, 160)


@application.route('/templates/<path:path>')
def send_templates(path):
    return send_from_directory('templates', path)

@application.route('/static/<path:path>')
def send_static_files(path):
    return send_from_directory('static/', path)

@application.route('/')
def index():
    return render_template('index.htm')

@application.route('/search_image', methods=['POST'])
def search_image():
    if request.method == 'POST':
        print request.files
        file = request.files['file']
        if file:
            tmpfile = join(
                tempfile.gettempdir(),
                file.name
            )

            guid = str(uuid.uuid4().get_hex().upper()[0:12]) + '.jpg'
            
            dstfile = join(
                SEARCH_IMAGE_PATH,
                guid
            )
            dstfile_thumb = join(
                SEARCH_IMAGE_THUMB_PATH,
                guid
            )
            file.save(tmpfile)
            
            try:
                with wand.image.Image(filename=tmpfile) as img:
                    w = img.width
                    h = img.height
                    [w0, h0] = get_ratio_size_big(w, h)
                    img.resize(w0, h0)
                    img.save(filename=dstfile)
                    [w0, h0] = get_ratio_size_small(w, h)
                    img.resize(w0, h0)
                    img.save(filename=dstfile_thumb)
                    
                    matches, total_count = im.match(dstfile_thumb, 0, 10)

                    origin = dict()
                    origin['image'] = dstfile_thumb
                    origin['filename'] = file.filename

                    res = dict()
                    res['matches'] = matches
                    res['origin'] = origin
                    res['total_count'] = total_count
                    
                    return json.dumps(res)
            except:
                traceback.print_exc()
    return '', 400

@application.route('/search_url', methods=['POST'])
def search_url():
    if request.method == 'POST':
        if request.form.get('url'):
            url_image = request.form.get('url')
        else:
            return None, 201
        
        tmpfile = join(
                tempfile.gettempdir(),
                "url.jpg"
            )

        guid = str(uuid.uuid4().get_hex().upper()[0:12]) + '.jpg'
        dstfile = join(
            SEARCH_IMAGE_PATH,
            guid
        )
        dstfile_thumb = join(
            SEARCH_IMAGE_THUMB_PATH,
            guid
        )
        #urllib.urlretrieve(url, tmpfile)
        #URL = 'http://www.w3schools.com/css/trolltunga.jpg'

        resource = urllib.urlopen(url_image)
        output = open(tmpfile,"wb")
        output.write(resource.read())
        output.close()

        try:
            with wand.image.Image(filename=tmpfile) as img:
                w = img.width
                h = img.height
                [w0, h0] = get_ratio_size_big(w, h)
                img.resize(w0, h0)
                img.save(filename=dstfile)
                [w0, h0] = get_ratio_size_small(w, h)
                img.resize(w0, h0)
                img.save(filename=dstfile_thumb)
                
                matches, total_count = im.match(dstfile_thumb, 0, 10)

                origin = dict()
                origin['image'] = dstfile_thumb
                origin['filename'] = url_image

                res = dict()
                res['matches'] = matches
                res['origin'] = origin
                res['total_count'] = total_count
                return json.dumps(res)
        except:
            traceback.print_exc()
            pass
    return '', 400

@application.route('/search_image_page', methods=['POST'])
def search_image_page():
    if request.method == 'POST':
        if request.form.get('image'):
            image = request.form.get('image')
        else:
            return None, 201
        if request.form.get('page'):
            page = int(request.form.get('page'))
        else:
            page = 1
            return None, 201
        if page < 1:
            page = 1
        page_size = 10
        limit = page * page_size
        if not isfile(image):
            return None, 201
        try:
            matches, total_count = im.match(image, limit-page_size, page_size)
            res = dict()
            res['matches'] = matches
            res['total_count'] = total_count
            
            return json.dumps(res)
        except:
            traceback.print_exc()
    return '', 400


@application.route('/browse_images', methods=['GET', 'POST'])
def browse_images():
    page_size = 10
    if request.method == 'POST':
        if request.form.get('page'):
            try:
                page = int(request.form.get('page'))
            except ValueError:
                page = 1
        if page < 1:
            page = 1
        limit = page * page_size
        images = get_browse_images(limit-page_size, page_size);
        return json.dumps({
            'count' : im.get_count(),
            'images' : images
            })
    return '', 400

@application.route('/get_ads_image_bank_count', methods=['GET', 'POST'])
def get_ads_image_bank_count():
    ads_images = get_browse_images_advertise_conn()
    return json.dumps({
            'count' : im.get_count(),
            'ads_images' : ads_images
            }), 200

@application.route('/edit_item', methods=['GET', 'POST'])
def edit_item():
    if request.method == 'POST':
        if request.form.get('image'):
            image = request.form.get('image')
        else:
            return '', 400

        if request.form.get('title'):
            title = request.form.get('title')
        else:
            title = ''
        if request.form.get('description'):
            description = request.form.get('description')
        else:
            description = ''
        if request.form.get('link'):
            link = request.form.get('link')
        else:
            link = ''
        im.edit_item(title, description, link, image)
        return '', 200
    return '', 400

@application.route('/delete_item', methods=['GET', 'POST'])
def delete_item():
    if request.method == 'POST':
        if request.form.get('image'):
            image = request.form.get('image')
        else:
            return '', 400
        im.delete_item(image)
        return '', 200
    return '', 400

@application.route('/add_image', methods=['POST'])
def add_image():
    if request.method == 'POST':
    	if request.form.get('title'):
            title = request.form.get('title')
        else:
            title = ""

        if request.form.get('description'):
            description = request.form.get('description')
        else:
            description = ""

        if request.form.get('link'):
            link = request.form.get('link')
        else:
            link = ""

        file = request.files['file']

        if file:
            tmpfile = join(
                tempfile.gettempdir(),
                file.name
            )
            guid = str(uuid.uuid4().get_hex().upper()[0:12]) + '.jpg'
            dstfile = join(
                BANK_PATH,
                guid
            )
            dstfile_thumb = join(
                BANK_THUMB_PATH,
                guid
            )
            file.save(tmpfile)
            try:
                with wand.image.Image(filename=tmpfile) as img:
                    w = img.width
                    h = img.height
                    [w0, h0] = get_ratio_size_big(w, h)
                    img.resize(w0, h0)
                    img.save(filename=dstfile)
                    [w0, h0] = get_ratio_size_small(w, h)
                    img.resize(w0, h0)
                    img.save(filename=dstfile_thumb)
                    add_flag = im.can_add(dstfile_thumb, title, description, link)
                    if add_flag:
                        im.add_image(dstfile_thumb, title, description, link)
                        return 'ok', 200
                    else:
                        os.remove(dstfile)
                        os.remove(dstfile_thumb)
                        return 'existing', 200
            except wand.exceptions.MissingDelegateError:
                return 'input is not a valid image', 500
    return '', 400

@application.route('/batch_images', methods=['POST'])
def batch_images():
    if request.form.get('excel_name'):
        excel_name = request.form.get('excel_name')
    else:
        return '', 400

    excel_path = os.path.join(EXCEL_PATH, excel_name)
    
    print excel_path

    if not isfile(excel_path):
        return '', 400

    wb = open_workbook(excel_path)

    for sheet in wb.sheets():
        number_of_rows = sheet.nrows
        number_of_columns = sheet.ncols

        rows = []
        for row in range(1, number_of_rows):
            values = []
            for col in range(number_of_columns):
                value  = (sheet.cell(row,col).value)
                try:
                    value = str(value)
                except ValueError:
                    value = ""
                finally:
                    values.append(value)
            item = BatchItem(*values)

            if not item.filename:
                continue

            image_file = join(
                BATCH_IMAGE_PATH,
                item.filename
            )
            if not isfile(image_file):
                continue

            guid = str(uuid.uuid4().get_hex().upper()[0:12]) + '.jpg'
            dstfile = join(
                BANK_PATH,
                guid
            )
            dstfile_thumb = join(
                BANK_THUMB_PATH,
                guid
            )
            try:
                with wand.image.Image(filename=image_file) as img:
                    w = img.width
                    h = img.height
                    [w0, h0] = get_ratio_size_big(w, h)
                    img.resize(w0, h0)
                    img.save(filename=dstfile)
                    [w0, h0] = get_ratio_size_small(w, h)
                    img.resize(w0, h0)
                    img.save(filename=dstfile_thumb)
                    add_flag = im.can_add(dstfile_thumb, item.title, item.description, item.link)
                    if add_flag:
                        im.add_image(dstfile_thumb, item.title, item.description, item.link)
                        os.remove(image_file)
                    else:
                        os.remove(dstfile)
                        os.remove(dstfile_thumb)
            except wand.exceptions.MissingDelegateError:
                continue
    return '', 200

@application.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        if request.form.get('username'):
            username = request.form.get('username')
        else:
            return '', 400
        if request.form.get('password'):
            password = request.form.get('password')
        else:
            return '', 400

        error, user = get_login_user_info(username, password)

        result = dict()
        result['error'] = error
        result['user'] = user
        
        if error == "ok":
            session['user'] = user
        return json.dumps(result), 200
    return '', 400

@application.route('/logout', methods=['GET', 'POST'])
def logout():
    session.pop('user', None)
    return '', 200

@application.route('/register', methods=['POST'])
def register():
    if request.method == 'POST':
        if request.form.get('firstname'):
            firstname = request.form.get('firstname')
        else:
            return '', 400
        if request.form.get('lastname'):
            lastname = request.form.get('lastname')
        else:
            return '', 400
        if request.form.get('username'):
            username = request.form.get('username')
        else:
            return '', 400
        if request.form.get('password'):
            password = request.form.get('password')
        else:
            return '', 400

        result = register_user(firstname, lastname, username, password)
        return result, 200
    return '', 400

@application.route('/save_content', methods=['GET', 'POST'])
def save_content():
    if request.method == 'POST':
        if request.form.get('content'):
            content = request.form.get('content')
        else:
            content = ''

        if request.form.get('key'):
            key = request.form.get('key')
        else:
            return '', 400

        save_content_with_key(key, content)
        return '', 200
    return '', 400

@application.route('/get_contents_all', methods=['GET', 'POST'])
def get_contents_all():
    if request.method == 'POST':
        contents = get_contents_all_func()
        return json.dumps(contents), 200
    return '', 400

@application.route('/get_content', methods=['GET', 'POST'])
def get_content():
    if request.method == 'POST':
        if request.form.get('key'):
            key = request.form.get('key')
        else:
            return '', 200

        content = get_content_from_key(key)
        return content, 200
    return '', 400

@application.route('/add_advertise_file', methods=['POST'])
def add_advertise_file():
    if request.method == 'POST':
        print request.files
        file = request.files['file']
        if file:
            tmpfile = join(
                tempfile.gettempdir(),
                file.name
            )

            guid = str(uuid.uuid4().get_hex().upper()[0:12]) + '.jpg'
            
            dstfile = join(
                AD_IMAGE_PATH,
                guid
            )
            dstfile_thumb = join(
                AD_IMAGE_THUMB_PATH,
                guid
            )
            file.save(tmpfile)
            try:
                with wand.image.Image(filename=tmpfile) as img:
                    w = img.width
                    h = img.height
                    img.resize(w, h)
                    img.save(filename=dstfile)
                    [w0, h0] = get_ratio_size_small(w, h)
                    img.resize(w0, h0)
                    img.save(filename=dstfile_thumb)
                    conn_add_advertise_image(dstfile_thumb, dstfile)
                    return 'ok', 200
            except:
                traceback.print_exc()
    return '', 400

@application.route('/add_advertise_url', methods=['POST'])
def add_advertise_url():
    if request.method == 'POST':
        if request.form.get('url'):
            url_image = request.form.get('url')
        else:
            return None, 201
        
        tmpfile = join(
                tempfile.gettempdir(),
                "url.jpg"
            )

        guid = str(uuid.uuid4().get_hex().upper()[0:12]) + '.jpg'
        dstfile = join(
            AD_IMAGE_PATH,
            guid
        )
        dstfile_thumb = join(
            AD_IMAGE_THUMB_PATH,
            guid
        )
        #urllib.urlretrieve(url, tmpfile)
        #URL = 'http://www.w3schools.com/css/trolltunga.jpg'
        resource = urllib.urlopen(url_image)
        output = open(tmpfile,"wb")
        output.write(resource.read())
        output.close()
        try:
            with wand.image.Image(filename=tmpfile) as img:
                w = img.width
                h = img.height
                img.resize(w, h)
                img.save(filename=dstfile)
                [w0, h0] = get_ratio_size_small(w, h)
                img.resize(w0, h0)
                img.save(filename=dstfile_thumb)
                conn_add_advertise_image(dstfile_thumb, url_image)
                return 'ok', 200
        except:
            traceback.print_exc()
            pass
    return '', 400

@application.route('/browse_images_advertise', methods=['GET', 'POST'])
def browse_images_advertise():
    if request.method == 'POST':
        images = get_browse_images_advertise_conn();
        return json.dumps({
            'images' : images
            }), 200
    return '', 400

@application.route('/delete_image_advertise', methods=['GET', 'POST'])
def delete_image_advertise():
    if request.method == 'POST':
        if request.form.get('image'):
            image = request.form.get('image')
        else:
            return '', 400
        delete_item_advertise_conn(image)
        return '', 200
    return '', 400

@application.route('/load_global_data', methods=['GET', 'POST'])
def load_global_data():
    ads_images = get_browse_images_advertise_conn()
    bank_count = im.get_count()
    contents = get_contents_all_func()
    return json.dumps({
            'bank_count' : bank_count,
            'ads_images' : ads_images,
            'contents' : contents
            }), 200

if __name__ == '__main__':
    check_make_dir(BANK_PATH)
    check_make_dir(BANK_THUMB_PATH)
    check_make_dir(EXCEL_PATH)
    check_make_dir(BATCH_IMAGE_PATH)
    check_make_dir(SEARCH_IMAGE_PATH)
    check_make_dir(SEARCH_IMAGE_THUMB_PATH)
    check_make_dir(AD_IMAGE_PATH)
    check_make_dir(AD_IMAGE_THUMB_PATH)
    application.secret_key = 'A0Zr98j/3yX R~XHH!jmN]LWX/,?RT'
    application.run(host= '0.0.0.0', threaded=True)
