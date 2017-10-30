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

    public function manage($offset = 0) {
        $data['title'] = 'Admin - Manage';
        $data['active'] = 'manage';
        $data['session_user'] = $this->session_user;

        $this->load->model('bank_model');

        if (count($_POST) > 0 && isset($_POST['delete'])) {
            $id = $_POST['id'];
            $row = $this->bank_model->get_row($id);

            $filename = $row[0]->{'tb_image'};
            $root_dir = $this->config->item('base_directory');
            $dst_filename = $root_dir . BANK_PATH . $filename;
            $dst_thumb_filename = $root_dir . BANK_THUMB_PATH . $filename;
            if (file_exists($dst_filename)) {
                unlink($dst_filename);
            }
            if (file_exists($dst_thumb_filename)) {
                unlink($dst_thumb_filename);
            }
            $this->bank_model->delete($id);
        }
        else if (count($_POST) > 0 && isset($_POST['edit'])) {
            $id = $_POST['id'];
            $row = $this->bank_model->get_row($id);

            $data['item'] = $row[0];
            $this->load->view('admin/includes/header', $data);
            $this->load->view('admin/includes/navbar');
            $this->load->view('admin/editDialog');
            $this->load->view('admin/includes/footer');
            return;
        }

        // Get data
        $this->load->library('pagination');

        $num_rows = $this->bank_model->get_total_count();
        //pagination settings
        $config['base_url'] = site_url('admin/manage');
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

        $rows = $this->bank_model->get_rows($config['per_page'],$offset);

        /*
         * Load view
         */
        $data['items'] = $rows;
        $data['total_count'] = $this->bank_model->get_total_count();
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/includes/navbar');
        $this->load->view('admin/manage');
        $this->load->view('admin/includes/footer');
    }

    public function edit_item()
    {
        if (count($_POST) > 0 && isset($_POST['save'])) {
            
            $this->load->model('bank_model');

            $update_data = array(
                'tb_id' => $_POST['id'],
                'tb_title' => $_POST['title'], 
                'tb_desc' => $_POST['description'], 
                'tb_url' => $_POST['link']
            );

            $this->bank_model->update($update_data);
            redirect(base_url('admin/manage'));
        }
    }

    public function upload() {
        $data['title'] = 'Admin - Upload';
        $data['active'] = 'upload';
        $data['session_user'] = $this->session_user;

        $this->load->model('bank_model');

        if (count($_POST)) {
            
            if ($_POST['type'] == 'file') {
                $title = $_POST['title'];
                $description = $_POST['description'];
                $link = $_POST['link'];

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
                    $dst_filename = $root_dir . BANK_PATH . $filename;
                    $dst_thumb_filename = $root_dir . BANK_THUMB_PATH . $filename;
                    
                    // make thumb image
                    $ret = $this->imageResize($orig_file, $dst_filename, 320);
                    $ret = $this->imageResize($orig_file, $dst_thumb_filename, 160);
                    if (file_exists($orig_file)) {
                        unlink($orig_file);
                    }

                    // add to db
                    //SELECT `tb_id`, `tb_image`, `tb_title`, `tb_desc`, `tb_url` FROM `tbl_bank` WHERE 1
                    $insert_data = array(
                        'tb_image' => $filename,
                        'tb_title' => $title, 
                        'tb_desc' => $description, 
                        'tb_url' => $link
                    );

                    $id = $this->bank_model->insert($insert_data);

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
            else if ($_POST['type'] == 'batch') {
                $root_dir = $this->config->item('base_directory');
                $filename = $_POST['excel_file'];
                $excel_filename = $root_dir . BATCH_PATH . $filename;

                //load the excel library
                $this->load->library('excel');
                 
                //read file from path
                try {
                    $objPHPExcel = PHPExcel_IOFactory::load($excel_filename);
                } catch (Exception $e) {
                    $notif = array();
                    $notif['message'] = 'Excel file doesn\'t exist !';
                    $notif['type'] = 'warning';
                    $data['notif'] = $notif;

                    $objPHPExcel = false;
                }
                
                if ($objPHPExcel) {
                    //get only the Cell Collection
                    $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                     
                    //extract to a PHP readable array format
                    foreach ($cell_collection as $cell) {
                        $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                        $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                        $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                     
                        //header will/should be in row 1 only. of course this can be modified to suit your need.
                        if ($row == 1) {
                            $header[$row][$column] = $data_value;
                        } else {
                            $arr_data[$row][$column] = $data_value;
                        }
                    }
                     
                    //send the data in an array format
                    $data['header'] = $header;
                    $data['values'] = $arr_data;

                    foreach ($arr_data as $data_row) {
                        $image = $data_row['A']; 
                        $title = $data_row['B']; 
                        $description = $data_row['C']; 
                        $link = $data_row['D']; 
        
                        $root_dir = $this->config->item('base_directory');

                        $orig_file = $root_dir . BATCH_PATH . $image;
                        $orig_name = $image;
                        $filename = time()."_".$orig_name;
                        $dst_filename = $root_dir . BANK_PATH . $filename;
                        $dst_thumb_filename = $root_dir . BANK_THUMB_PATH . $filename;
                        
                        if (!file_exists($orig_file)) {
                            continue;
                        }
                        // make thumb image
                        $ret = $this->imageResize($orig_file, $dst_filename, 320);
                        $ret = $this->imageResize($orig_file, $dst_thumb_filename, 160);
                        if (file_exists($orig_file)) {
                            unlink($orig_file);
                        }

                        // add to db
                        //SELECT `tb_id`, `tb_image`, `tb_title`, `tb_desc`, `tb_url` FROM `tbl_bank` WHERE 1
                        $insert_data = array(
                            'tb_image' => $filename,
                            'tb_title' => $title, 
                            'tb_desc' => $description, 
                            'tb_url' => $link
                        );

                        $id = $this->bank_model->insert($insert_data);

                    }

                    $notif = array();
                    $notif['message'] = 'Batch processed successfully !';
                    $notif['type'] = 'success';
                    $data['notif'] = $notif;
                }
            }
        }
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
                        $notif = array();
                        $notif['message'] = 'File operation error !';
                        $notif['type'] = 'warning';
                        $data['notif'] = $notif;
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
                    $notif = array();
                    $notif['message'] = 'Upload error !';
                    $notif['type'] = 'warning';
                    $data['notif'] = $notif;
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

            if (!isset($notif)) {
                $notif = array();
                $notif['message'] = 'You request was done successfully';
                $notif['type'] = 'success';
                $data['notif'] = $notif;
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
