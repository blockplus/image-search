<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Content_model extends CI_Model {

    var $tc_id   = '';
    var $tc_type = '';
    var $tc_content    = '';
    private $table = "tbl_contents";

    function __construct() {
        parent::__construct();
    }

    /*
     * 
     */
    function get_row($id)
    {
        $query = $this->db->get_where($this->table, array('tc_id' => $id));
        return $query->result();
    }
    
    function get_rows()
    {
        $query = $this->db->get_where($this->table);
        return $query->result();
    }
    
    function get_content($type)
    {
        $sql = "SELECT tc_content FROM tbl_contents WHERE tc_type=" . $this->db->escape($type);
        $query = $this->db->query($sql);
        $result = $query->result();

        return $result[0] -> {'tc_content'};
    }
    
    function get_contents($select='tc_type, tc_content')
    {
        $query = $this->db->select($select);
        $query = $this->db->get_where($this->table);
        return $query->result();
    }

    public function set_contents() {
        $types = array("about", "policy", "contact");

        foreach ($types as $type) {
            $sql = "UPDATE tbl_contents SET tc_content = ". $this->db->escape($_POST[$type]) . " WHERE tc_type=" . $this->db->escape($type);

            $this->db->query($sql);
        }
        
        $notif = array();
        $notif['message'] = 'Saved successfully';
        $notif['type'] = 'success';
        unset($_POST);
    
        return $notif;
    }

}