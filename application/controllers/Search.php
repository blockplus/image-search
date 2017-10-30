<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    function __construct() {
        parent::__construct();

        Utils::no_cache();
    }
    
    public function index($offset = 0) {
        $data['title'] = 'Image Search - Search';
        $data['origin'] = array();
        $data['items'] = array();
        $data['search_image_count'] = 0;
        $data['match_count'] = 0;

        $this->load->model('advertise_model');
        $rows = $this->advertise_model->get_rows();
        $data['advertise_items'] = $rows;

        $root_dir = $this->config->item('base_directory');
        if (count($_POST) > 0) {
            $search_url = $_POST['search_url'];
            if (substr($search_url, 0, 4) == 'http') {
                // Search with url
                $link = $search_url;
                $img=file_get_contents($link);

                $path = explode("?",$link);
                $filename = time()."_".basename($path[0]);

                $dst_filename = $root_dir . SEARCH_PATH . $filename;
                $dst_thumb_filename = $root_dir . SEARCH_THUMB_PATH . $filename;
                file_put_contents($dst_filename,$img);
                    
                // make thumb image
                $ret = $this->imageResize($dst_filename, $dst_filename, 320);
                $ret = $this->imageResize($dst_filename, $dst_thumb_filename, 160);

                $data['origin'] = array(
                    'image' => $filename,
                    'filename' => $search_url
                );

                $notif = array();
                $notif['message'] = 'Uploaded successfully !';
                $notif['type'] = 'success';
                $data['notif'] = $notif;
            } else {
                // Search by file
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
                    $orig_file = $uploaded_data['upload_data']['full_path'];

                    $orig_name = $uploaded_data['upload_data']['orig_name'];
                    $filename = time()."_".$uploaded_data['upload_data']['orig_name'];
                    $root_dir = $this->config->item('base_directory');
                    $dst_filename = $root_dir . SEARCH_PATH . $filename;
                    $dst_thumb_filename = $root_dir . SEARCH_THUMB_PATH . $filename;
                    
                    // make thumb image
                    $ret = $this->imageResize($orig_file, $dst_filename, 320);
                    $ret = $this->imageResize($orig_file, $dst_thumb_filename, 160);
                    if (file_exists($orig_file)) {
                        unlink($orig_file);
                    }

                    $data['origin'] = array(
                        'image' => $filename,
                        'filename' => $search_url
                    );
                    $notif = array();
                    $notif['message'] = 'Uploaded successfully !';
                    $notif['type'] = 'success';
                    $data['notif'] = $notif;
                }
                else
                {
                    $notif = array();
                    $notif['message'] = 'Upload error !';
                    $notif['type'] = 'warning';
                    $data['notif'] = $notif;
                }
            }
        }

        // $data['search_image_count'] = 0;
        $fi = new FilesystemIterator($root_dir . SEARCH_PATH, FilesystemIterator::SKIP_DOTS);
        $data['search_image_count'] = iterator_count($fi) - 1;

        // Get data
        $this->load->library('pagination');

        $num_rows = 10;//$this->bank_model->get_total_count();
        //pagination settings
        $config['base_url'] = site_url('search/');
        $config['total_rows'] = $num_rows;
        $config['per_page'] = "10";
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config["num_links"] = floor($choice);

        //config for bootstrap pagination class integration
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        $data['page'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $this->pagination->initialize($config);
        /*
         * Load view
         */
        $this->load->view('includes/header', $data);
        $this->load->view('search/index');
        $this->load->view('includes/footer');
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
        $this->image_lib->initialize($config);
        
        // resize image
        $this->image_lib->resize();
        // handle if there is any problem
        if ( ! $this->image_lib->resize()){
            return false;
        }
        return true;
    }
}
