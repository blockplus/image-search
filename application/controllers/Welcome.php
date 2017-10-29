<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    function __construct() {
        parent::__construct();

        Utils::no_cache();
    }
    
    public function index() {
        $data['title'] = 'Image Search - Welcome';
        /*
         * Load view
         */
        $this->load->view('includes/header', $data);
        $this->load->view('welcome/index');
        $this->load->view('includes/footerbar');
        $this->load->view('includes/footer');
    }

}
