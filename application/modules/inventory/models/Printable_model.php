<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Printable_model extends CI_Model{
	protected $uomTable = "uom";
	protected $itemsTable = "items";
	protected $receivingTable = "receiving";
	protected $receivingContentTable = "receiving_contents";
	protected $issuanceTable = "issuance";
	protected $issuanceContentTable = "issuance_contents";
	
	function __construct(){
		parent::__construct();
	}
	
	function getReceivingDataById($id=null){
		if($id){
			//$query = $this->db->query("SELECT * FROM receiving LEFT JOIN tblemployees ON receiving.created_by = tblemployees.id WHERE tblemployees.id = {$id}");
			$query = $this->db->get_where($this->receivingTable, array("id"=>$id));
			if($query->num_rows() == 1){
				$row = $query->row();
				
				$userData = $this->core_layout->getUserData($row->created_by);
				$displayName = (isset($userData["display_name_1"]) && $userData["display_name_1"])? $userData["display_name_1"]: "";
				$row->received_by = $displayName;
				
				return $row;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function getReceivingItemById($id=null){
		if($id){
			$this->db->select("a.qty,FORMAT(a.unit_price,2) as unit_price, b.name, b.sku, c.uom_code");
			$this->db->from("{$this->receivingContentTable} a");
			$this->db->join("{$this->itemsTable} b", "b.id = a.item", "left");
			$this->db->join("{$this->uomTable} c", "c.id = b.unit", "left");
			$this->db->where("a.receiving_id", $id);
			$query = $this->db->get();
			
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function getIssuanceDataById($id=null){
		if($id){
			//$query = $this->db->get_where($this->issuanceTable, array("id"=>$id));
			$query = $this->db->query("SELECT issuance.*, contractors.name FROM issuance LEFT JOIN contractors ON issuance.issued_to = contractors.id WHERE issuance.id = {$id}");
			if($query->num_rows() == 1){
				$row = $query->row();
				
				$userData = $this->core_layout->getUserData($row->created_by);
				$displayName = (isset($userData["display_name_1"]) && $userData["display_name_1"])? $userData["display_name_1"]: "";
				$row->issued_by = $displayName;
				
				return $row;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function getIssuanceItemById($id=null){
		if($id){
			$this->db->select("a.qty, a.purpose, b.name, b.sku, c.uom_code");
			$this->db->from("{$this->issuanceContentTable} a");
			$this->db->join("{$this->itemsTable} b", "b.id = a.item", "left");
			$this->db->join("{$this->uomTable} c", "c.id = b.unit", "left");
			$this->db->where("a.issuance_id", $id);
			$query = $this->db->get();
			
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}