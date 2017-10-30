<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Content_model extends CI_Model {

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

    function insert($data) {
        // Inserting into your table
        $this->db->insert($this->table, $data);
        $idOfInsertedData = $this->db->insert_id();
        return $idOfInsertedData;
    }

    function delete($id){
        $this->db->where('tc_id', $id);
        $this->db->delete($this->table); 
    }

    function update($data) {
        $this->db->where('tc_id',$data['tc_id']);
        $this->db->update($this->table, $data);
    }
    function get_content($type)
    {
        $sql = "SELECT tc_content FROM tbl_contents WHERE tc_type=" . $this->db->escape($type);
        $query = $this->db->query($sql);
        $result = $query->result();

        return @$result[0] ? $result[0] -> {'tc_content'} : '';
    }
}