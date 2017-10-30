<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bank_model extends CI_Model {
    //SELECT `tb_id`, `tb_image`, `tb_title`, `tb_desc`, `tb_url` FROM `tbl_bank` WHERE 1
    private $table = "tbl_bank";

    function __construct() {
        parent::__construct();
    }

    /*
     * 
     */
    function get_row($id)
    {
        $query = $this->db->get_where($this->table, array('tb_id' => $id));
        return $query->result();
    }
    
    function get_rows($per_page, $offset)
    {
        $query = $this->db->get($this->table, $per_page, $offset);

        return $query->result();
    }

    function insert($data) {
        // Inserting into your table
        $this->db->insert($this->table, $data);
        $idOfInsertedData = $this->db->insert_id();
        return $idOfInsertedData;
    }

    function update($data) {
        $update_data = array(
                'tb_id' => $_POST['id'],
                'tb_title' => $_POST['title'], 
                'tb_desc' => $_POST['description'], 
                'tb_url' => $_POST['link']
            );
        // Inserting into your table
        $sql = "UPDATE tbl_bank SET ";
        $sql .= " tb_title=" . $this->db->escape($data['tb_title']);
        $sql .= ", tb_desc=" . $this->db->escape($data['tb_desc']);
        $sql .= ", tb_url=" . $this->db->escape($data['tb_url']);
        $sql .= " WHERE tb_id=" . $this->db->escape($data['tb_id']);

        $this->db->query($sql);
    }

    function delete($id){
        $this->db->where('tb_id', $id);
        $this->db->delete($this->table); 
    }

    function get_total_count()
    {
        $sql = "SELECT count(*) AS total FROM tbl_bank";
        $res = $this->db->query($sql);
        $row = $res->row();
        return $row->{'total'};
    }
}