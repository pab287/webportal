<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Is_model extends CI_Model {
    protected $eformsTable = "gcceforms";
	public function __construct(){
		parent::__construct();
    }

    function getDBset(){
       $warehouse_id =  $this->session->userdata("warehouseid");
       $this->db->select("*");
       $this->db->from("inventory_core.warehouses");
       $this->db->where("id",$warehouse_id);
       $query =  $this->db->get();
       return $query ? $query->row_array()["database"] : false;
    }

}