<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

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
        $data['title'] = 'Dashboard';
        
        
        $data['session_user'] = $this->session_user;
        /*
         * Load view
         */
        $this->load->view('includes/header', $data);
        $this->load->view('includes/navbar');
        $this->load->view('dashboard/index');
        $this->load->view('includes/footer');
    }

}
