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
        /*
         * Load view
         */
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/includes/navbar');
        $this->load->view('admin/advertise');
        $this->load->view('admin/includes/footer');
    }

}
