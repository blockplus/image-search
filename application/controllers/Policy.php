<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Policy extends CI_Controller {

    function __construct() {
        parent::__construct();

        Utils::no_cache();
    }
    
    public function index() {
        $data['title'] = 'Image Search - Policy';

        $this->load->model('content_model');
        $data['content'] = $this->content_model->get_content('policy');

        /*
         * Load view
         */
        $this->load->view('includes/header', $data);
        $this->load->view('policy/index');
        $this->load->view('includes/footerbar2');
        $this->load->view('includes/footer');
    }

}
