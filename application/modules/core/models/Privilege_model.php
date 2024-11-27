<?php
class Privilege_model extends CI_Model{
	private $privilegeTable = "privilege_list";
	
	function __construct(){
		parent::__construct();
	}
	
	function addPrivilege(){
		$post = $this->input->post();
		$result = array();
		if($post){
			$aclName = $this->checkPrivilegeName($post);
			if($aclName){
				$insert = $this->db->insert($this->privilegeTable, $post);
				if($insert){
					$result["response"] = true; 
					$result["toastr_msg"] = "Privilege has been saved.";
				}else{
					$result["response"] = false;
					$result["toastr_msg"] = "Error in saving privilege!";
				}
			}else{
				$result["response"] = false;
				$result["toastr_msg"] = "Privilege name already exist!";
			}
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "No available data for saving!";
		}
		return $result;
	}
	
	function updatePrivilege(){
		$post = $this->input->post();
		$result = array();
		if(isset($post["id"]) && $post["id"]){
			$id = $post["id"]; unset($post["id"]);
			$update = $this->db->update($this->privilegeTable, $post, array("id"=>$id));
			if($update){
				$result["response"] = true; 
				$result["toastr_msg"] = "Privilege has been updated.";
			}else{
				$result["response"] = false;
				$result["toastr_msg"] = "Error in updating privilege!";
			}
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "No available data for saving!";
		}
		
		return $result;
	}
	
	function removePrivilege(){
		$post = $this->input->post();
		$result = array();
		if(isset($post["id"]) && $post["id"]){
			$this->db->delete($this->privilegeTable, array('id'=>$post['id']));
			if (!$this->db->affected_rows()) {
				$result["response"] = false;
				$result["toastr_msg"] = "Error! ID [{$post['id']}] not found";
			} else {
				$result["response"] = true;
				$result["toastr_msg"] = "Privilege has been removed.";
			}
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "No available data for removal!";
		}
		
		return $result;
	}
	
	function getPrivilegeUpdate(){
		$post = $this->input->post();
		$resultset = array();
		if(isset($post["id"]) && $post["id"]){
			$query = $this->db->get_where($this->privilegeTable, $post);
			if($query->num_rows() == 1){
				$resultset["response"] = true;
				$resultset["value"] = $query->row_array();
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}
		return $resultset;
	}
	
	function getPrivilegeList(){
		$post = $this->input->post();
		$json_data = array();
		if($post){
			$columns = array("id", "name", "label", "description", "status");
			$dir = $post["order"][0]["dir"];
			$order = $columns[$post["order"][0]["column"]];
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$dtPrivilege = $this->dt_model->dataTable();
			$dtPrivilege->setTable($this->privilegeTable);
			$dtPrivilege->setParameterFields($columns);
			
			$totalData = $dtPrivilege->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $dtPrivilege->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $dtPrivilege->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $dtPrivilege->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
					$nestedData['id'] = $pst->id;
					$nestedData['name'] = $pst->name;
					$nestedData['label'] = $pst->label;
					$nestedData['description'] = $pst->description;
					$nestedData['status'] = $pst->status;
					$data[] = $nestedData;
				}
			}
			$json_data = array(
				"draw" => intval($draw),  
				"recordsTotal" => intval($totalData),  
				"recordsFiltered" => intval($totalFiltered), 
				"data" => $data,   
				);
		}else{
			$json_data = array(
				"draw"=>1,
				"recordsTotal"=>0,
				"recordsFiltered"=>0,
				"data"=>array(),
				);
		}
		
		return $json_data;
	}
	private function checkPrivilegeName($data=array()){
		if($data){
			$query = $this->db->get_where($this->privilegeTable, array("name"=>$data["name"]));
			if($query->num_rows() == 0){ return true; }
			else{ return false; }
		}else{ return false; }
	}
}