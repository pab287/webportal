<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Department_model extends CI_Model{
	protected $departmentTable = "gccph_uss.departments";
	
	function __construct(){
		parent::__construct();
		$this->load->model("datatable_model","dt_model");
	}
	
	function addDepartment(){
		$post = $this->input->post();
		$result = array();
		if($post){
			$deptCode = $this->checkDepartmentCode($post);
			if($deptCode){
				$insert = $this->db->insert($this->departmentTable, $post);
				if($insert){
					$result["response"] = true; 
					$result["toastr_msg"] = "Department has been saved.";
				}else{
					$result["response"] = false;
					$result["toastr_msg"] = "Error in saving department!";
				}
			}else{
				$result["response"] = false;
				$result["toastr_msg"] = "Department name already exist!";
			}
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "No available data for saving!";
		}
		
		return $result;
	}
	
	function updateDepartment(){
		$post = $this->input->post();
		$result = array();
		if(isset($post["id"]) && $post["id"]){
			$id = $post["id"]; unset($post["id"]);
			$update = $this->db->update($this->departmentTable, $post, array("id"=>$id));
			if($update){
				$result["response"] = true; 
				$result["toastr_msg"] = "Department has been updated.";
			}else{
				$result["response"] = false;
				$result["toastr_msg"] = "Error in updating department!";
			}
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "No available data for saving!";
		}
		
		return $result;
	}
	
	function removeDepartment(){
		$post = $this->input->post();
		$resultset = array();
		if(isset($post["id"]) && $post["id"]){
			$this->db->delete($this->departmentTable, $post);
			if (!$this->db->affected_rows()) {
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Error! ID [{$post['id']}] not found";
			} else {
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Department has been removed.";
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No available data for removal!";
		}
		
		return $resultset;
	}
	
	function getDepartmentList(){
		$post = $this->input->post();
		if($post){
			$columns = array("id", "code", "description", "status");
			$dir = $post["order"][0]["dir"];
			$order = $columns[$post["order"][0]["column"]];
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$dtDepartment = $this->dt_model->dataTable();
			$dtDepartment->setTable($this->departmentTable);
			$dtDepartment->setParameterFields($columns);
			
			$totalData = $dtDepartment->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $dtDepartment->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $dtDepartment->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $dtDepartment->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
					$nestedData['id'] = $pst->id;
					$nestedData['code'] = $pst->code;
					$nestedData['description'] = $pst->description;
					$nestedData['status'] = $pst->status;
					$data[] = $nestedData;
				}
			}
			$json_data = array(
                    "draw" => intval($draw),  
                    "recordsTotal" => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,   
                    );
            
			return $json_data;
		}else{
			return array(
				"draw"=>1,
				"recordsTotal"=>0,
				"recordsFiltered"=>0,
				"data"=>array(),
				);
		}
	}
	
	function getCurrentDepartments(){
		$resultset = array();
		$this->db->order_by("description", "ASC");
		$query = $this->db->get_where($this->departmentTable, array("status"=>1));
		if($query->num_rows() > 0){
			$data = $query->result();
			$resultset["response"] = true;
			$resultset["data"] = $data;
		}else{
			$resultset["response"] = false;
		}
		
		return $resultset;
	}
	
	private function checkDepartmentCode($data=array()){
		if($data){
			$query = $this->db->get_where($this->departmentTable, array("code"=>$data["code"]));
			if($query->num_rows() == 0){ return true; }
			else{ return false; }
		}else{ return false; }
	}
	
	function getDepartmentData(){
		$resultset = array();
		$post = $this->input->post();
		if($post){
			$query = $this->db->get_where($this->departmentTable, $post);
			if($query->num_rows() == 1){
				$row = $query->row();
				$resultset["response"] = true;
				$resultset["data"] = $row;
			}else{ $resultset["response"] = false; }
		}else{ $resultset["response"] = false; }
		
		return $resultset;
	}
}