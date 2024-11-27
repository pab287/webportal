<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Warehouse_model extends CI_Model {
    protected $eformsTable = "gcceforms";
	public function __construct(){
		parent::__construct();
    }

    private function getUserdata(){
        return $this->session->userdata('logged_in');
     }

    function createNew(){
        $resultarray = array();
        $post = $this->input->post();
        $post["created_by"] = $this->getUserdata()['emp_id'];
        $post["status"] = 1;
        $query = $this->db->insert('inventory_core.warehouses', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Warehouse successfully created.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error creating warehouse.";
        }

        return $resultarray;
    }

    function getWarehouseCollection(){
        $resultarray = array();

        $this->db->select("*");
        $this->db->from("inventory_core.warehouses");
        $this->db->where('status',1);
        $query = $this->db->get();

        return array("data"=>$query->result_array());
    }

}