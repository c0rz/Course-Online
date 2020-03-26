<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model
{
	public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->userTbl = 'account';
    }

	public function getData($params = array()){
		$this->db->select('*');
        $this->db->from($this->userTbl);

        if (array_key_exists("conditions",$params)) {
            foreach($params['conditions'] as $key => $value){
                $this->db->where($key,$value);
            }
        }
		if (array_key_exists("id_akun",$params)) {
            $this->db->where('id_akun',$params['id']);
            $query = $this->db->get();
            $result = $query->row_array();
        } else {
        	if (array_key_exists("start",$params) && array_key_exists("limit",$params)) {
                $this->db->limit($params['limit'],$params['start']);
            } else if (!array_key_exists("start",$params) && array_key_exists("limit",$params)) {
                $this->db->limit($params['limit']);
            }
            if (array_key_exists("returnType",$params) && $params['returnType'] == 'count') {
                $result = $this->db->count_all_results();    
            } else if (array_key_exists("returnType",$params) && $params['returnType'] == 'single') {
                $query = $this->db->get();
                $result = ($query->num_rows() > 0)?$query->row_array():false;
            } else {
            	$query = $this->db->get();
                $result = ($query->num_rows() > 0)?$query->result_array():false;
            }
        }
		return $result;
	}

    public function insert($data){
            $insert = $this->db->insert($this->userTbl, $data);
            return $insert?$this->db->insert_id():false;
    }

    public function update($id_akun, $data){
        $this->db->where('id_akun', $id_akun);
        return $this->db->update('produk', $data);
    }
}