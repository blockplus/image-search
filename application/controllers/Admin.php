<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    var $session_user;

    function __construct() {
        parent::__construct();

        Utils::no_cache();
        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('auth/login'));
            exit;
        }
        $this->session_user = $this->session->userdata('logged_in');

        $this->load->helper(array('form', 'url')); 
    }

    /*
     * 
     */

    public function index() {
        redirect(base_url('admin/manage'));
    }

    public function manage() {
        $data['title'] = 'Admin - Manage';
        $data['active'] = 'manage';
        $data['session_user'] = $this->session_user;
        /*
         * Load view
         */
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/includes/navbar');
        $this->load->view('admin/manage');
        $this->load->view('admin/includes/footer');
    }

    public function upload() {
        $data['title'] = 'Admin - Upload';
        $data['active'] = 'upload';
        $data['session_user'] = $this->session_user;
        /*
         * Load view
         */
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/includes/navbar');
        $this->load->view('admin/upload');
        $this->load->view('admin/includes/footer');
    }

    public function content() {
        $data['title'] = 'Admin - Content';
        $data['active'] = 'content';
        $data['session_user'] = $this->session_user;

        $this->load->model('content_model');

        if (count($_POST)) {
            $items = array();
            $items['about'] = $_POST['about'];
            $items['policy'] = $_POST['policy'];
            $items['contact'] = $_POST['contact'];
            $data['notif'] = $this->content_model->set_contents();
        } else {
            $rows = $this->content_model->get_contents();
            $items = array();
            foreach ($rows as $row) {
                $type = $row->{'tc_type'};
                $content = $row->{'tc_content'};
                
                $items[$type] = $content;
            }
        }
        
        /*
         * Load view
         */
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/includes/navbar');
        $this->load->view('admin/content', array('items' => $items));
        $this->load->view('admin/includes/footer');
    }

    public function advertise() {
        $data['title'] = 'Admin - Advertise';
        $data['active'] = 'advertise';
        $data['session_user'] = $this->session_user;

        $this->load->model('advertise_model');

        $error = '';
        if (count($_POST)) {
            
            if ($_POST['type'] == 'file') {
                $config = array(
                    'upload_path' => "./uploads/",
                    'allowed_types' => "gif|jpg|png|jpeg",
                    'overwrite' => TRUE,
                    'max_size' => "2048000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "768",
                    'max_width' => "1024"
                );
                $this->load->library('upload', $config);
                if($this->upload->do_upload())
                {
                    $uploaded_data = array('upload_data' => $this->upload->data());

                    $orig_name = $uploaded_data['upload_data']['orig_name'];
                    
                    $filename = time()."_".$uploaded_data['upload_data']['orig_name'];
                    
                    $root_dir = $this->config->item('base_directory');
                    $dst_filename = $root_dir . ADVERTISE_PATH . $filename;
                    $dst_thumb_filename = $root_dir . ADVERTISE_THUMB_PATH . $filename;
                    
                    if (!rename($uploaded_data['upload_data']['full_path'], $dst_filename)) {
                        $error = 'File operation error!';
                    }

                    // make thumb image
                    $ret = $this->imageResize($dst_filename, $dst_thumb_filename, 160);
                    // add to db
                    $insert_data = array(
                        'ta_imagename' => $filename,
                        'ta_info' => $orig_name
                    );

                    $id = $this->advertise_model->insert($insert_data);
                }
                else
                {
                    //$error = array('error' => $this->upload->display_errors());
                    $error = 'Upload error!';
                }
            }
            else if ($_POST['type'] == 'link') {
                $link = $_POST['file'];
                $img=file_get_contents($link);

                $path = explode("?",$link);
                $filename = time()."_".basename($path[0]);

                $root_dir = $this->config->item('base_directory');
                $dst_filename = $root_dir . ADVERTISE_PATH . $filename;
                $dst_thumb_filename = $root_dir . ADVERTISE_THUMB_PATH . $filename;
                file_put_contents($dst_filename,$img);
                    
                // make thumb image
                $ret = $this->imageResize($dst_filename, $dst_thumb_filename, 160);
                // add to db
                $insert_data = array(
                    'ta_imagename' => $filename,
                    'ta_info' => $link
                );
                $id = $this->advertise_model->insert($insert_data);
            }
            else if ($_POST['type'] == 'delete') {
                $id = $_POST['id'];
                $row = $this->advertise_model->get_row($id);
                
                $filename = $row[0]->{'ta_imagename'};
                $root_dir = $this->config->item('base_directory');
                $dst_filename = $root_dir . ADVERTISE_PATH . $filename;
                $dst_thumb_filename = $root_dir . ADVERTISE_THUMB_PATH . $filename;
                if (file_exists($dst_filename)) {
                    unlink($dst_filename);
                }
                if (file_exists($dst_thumb_filename)) {
                    unlink($dst_thumb_filename);
                }
                $this->advertise_model->delete($id);
            }
        }

        // Get data
        $rows = $this->advertise_model->get_rows();
        $items = array();
        foreach ($rows as $row) {
            $item = array();

            $item['id'] = $row->{'ta_id'};
            $item['imagename'] = $row->{'ta_imagename'};
            $item['info'] = $row->{'ta_info'};
            
            $items[] = $item;
        }
        /*
         * Load view
         */
        $data['items'] = $items;
        $data['error'] = $error;
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/includes/navbar');
        $this->load->view('admin/advertise');
        $this->load->view('admin/includes/footer');
    }

    public function imageResize($source_image, $new_image, $image_size){
        //$img_path =  realpath("img")."\\images\\uploaded\\".$imgName.".jpeg";

        // Configuration
        $config['image_library'] = 'gd2';
        $config['source_image'] = $source_image;
        $config['new_image'] = $new_image;
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = $image_size;
        $config['height'] = $image_size;

        // Load the Library
        $this->load->library('image_lib', $config);

        // resize image
        $this->image_lib->resize();
        // handle if there is any problem
        if ( ! $this->image_lib->resize()){
            //die($this->image_lib->display_errors());
            return false;
        }
        
        return true;
    }

}
