<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Settings_model extends CI_Model{
	protected $coreSetupTable = "core_setup";
	
	function __construct(){
		parent::__construct();
	}
	
	function updateSettings(){
		$post = $this->input->post();
		$resultset = array();
		if($post){
			$this->db->truncate($this->coreSetupTable);
			$insert = $this->db->insert($this->coreSetupTable, $post);
			if($insert){
				$resultset["response"] = TRUE;
				$resultset["toastr_msg"] = "Settings has been updated.";
			}else{
				$resultset["response"] = FALSE;
				$resultset["toastr_msg"] = "Failed to update settings!";
			}
		}else{
			$resultset["toastr_msg"] = "No data found!";
			$resultset["response"] = TRUE;			
		}
		
		return $resultset;
	}
	
	function getSettingsData(){
		$this->db->from($this->coreSetupTable);
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() == 1){
			return $query->row();
		}else{
			return false;
		}
	}
}