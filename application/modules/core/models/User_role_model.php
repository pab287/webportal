<?php defined('BASEPATH') OR exit('No direct script access allowed');
class User_role_model extends CI_Model{
	protected $moduleTable = "modules";
	protected $modulePrivilegeTable = "module_privilege";
	protected $roleTable = "user_role";
	protected $roleAclTable = "user_role_acl";
	protected $aclTable = "access_control_list";
	protected $aclPrivTable = "acl_privilege";
	protected $privilegeListTable = "privilege_list";
	
	function __construct(){
		parent::__construct();
		$this->load->model("module_model", "module_model");
		$this->load->model("access_control_model", "acl_model");
		$this->load->model("datatable_model","dt_model");
	}
	
	function addUserRole(){
		$post = $this->input->post();
		if($post){
			$aclName = $this->checkAclName($post);
			if($aclName){
				if(!isset($post["role_code"])){ $post["role_code"] = $this->core_layout->generateCode(); }
				$insert = $this->db->insert($this->roleTable, $post);
				if($insert){
					$result["response"] = true; 
					$result["toastr_msg"] = "User role has been saved.";
				}else{
					$result["response"] = false;
					$result["toastr_msg"] = "Error in saving user role!";
				}
			}else{
				$result["response"] = false;
				$result["toastr_msg"] = "User role name already exist!";
			}
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "No available data for saving!";
		}
		
		return $result;
	}
	
	function updateUserRole(){
		$post = $this->input->post();
		$result = array();
		if(isset($post["id"]) && $post["id"] || $post["id"] == "0"){
			$id = $post["id"]; unset($post["id"]);
			$tempRole = $this->db->get_where($this->roleTable, array("id"=>$id));
			$roleDescription = ($tempRole->num_rows() == 1)? strtoupper($tempRole->row()->description): "NO ROLE";

			$update = $this->db->update($this->roleTable, $post, array("id"=>$id));
			if($update){
				$result["response"] = true; 
				$result["toastr_msg"] = "User role details of `{$roleDescription}` has been updated.";
			}else{
				$result["response"] = false;
				$result["toastr_msg"] = "Error updating the user role details of `{$roleDescription}`!";
			}
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "No available data for saving!";
		}
		
		$tempStatus = ($result["response"])? "success": "error";
		$this->core_layout->logNotification($result["toastr_msg"], $tempStatus, "users");

		return $result;
	}
	
	function removeUserRole(){
		$post = $this->input->post();
		$result = array();
		if(isset($post["id"]) && $post["id"] || $post["id"] == "0"){
			$this->db->delete($this->roleTable, array('id'=>$post['id']));
			if (!$this->db->affected_rows()) {
				$result["response"] = false;
				$result["toastr_msg"] = "Error! ID [{$post['id']}] not found";
			} else {
				$result["response"] = true;
				$result["toastr_msg"] = "User role has been removed.";
			}
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "No available data for removal!";
		}
		
		return $result;
	}
	
	function getUserRoleList(){
		$post = $this->input->post();
		if($post){
			$columns = array("id", "name", "description", "status");
			$dir = $post["order"][0]["dir"];
			$order = $columns[$post["order"][0]["column"]];
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$dtAccessControl = $this->dt_model->dataTable();
			$dtAccessControl->setTable($this->roleTable);
			$dtAccessControl->setParameterFields($columns);
			
			$totalData = $dtAccessControl->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $dtAccessControl->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $dtAccessControl->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $dtAccessControl->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
					$nestedData['id'] = $pst->id;
					$nestedData['name'] = $pst->name;
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

	function getAssignedModuleRole(){
		$post = $this->input->post();
		$resultset = array();

		if(isset($post["id"]) && ($post["id"] || $post["id"] == "0")){
			unset($post["csrf_token"]);
			$query = $this->db->get_where($this->roleTable, $post);
			if($query->num_rows() > 0){
				$row = $query->row_array();

				$roles = $this->getAssignedUserRoleModuleList($post["id"]);
				$aclData = $this->module_model->modulesJson();
				$html = $this->load->view("core/roles/modal_content/list_module_actions", array("row"=>$row), true);

				$resultset["html"] = $html;
				$resultset["post"] = $post;
				$resultset["data"] = $aclData;
				$resultset["role_id"] = $roles;
				$resultset["response"] = true;
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;			
		}
		
		return $resultset;
	}

	function setAssignedModuleRole(){
		$post = $this->input->post();
		$resultset = array();
		if(isset($post["nodes"], $post["id"]) && $post["nodes"] && $post["id"] || $post["id"] == "0"){
			$idx = $post["id"];
			$tempRole = $this->db->get_where($this->roleTable, array("id"=>$post["id"]));
			$roleDescription = ($tempRole->num_rows() == 1)? strtoupper($tempRole->row()->description): "NO ROLE";
			$nodes = json_decode($post["nodes"], true);
			$ids = array();
			foreach($nodes as $key => $value){
				$selected = $value["state"]["selected"];
				$parentId = intval($value["parent"]);
				$id = intval($value["id"]);
				
				if($parentId !== 0 && $selected && !in_array($parentId, $ids)){ $ids[] = $parentId; }
				if($selected){ $ids[] = $id; }
			}
			
			$ids = array_unique($ids);

			$setRole = $this->setUserRoleModule($idx, $ids);
			if($setRole){
				$resultset["toastr_msg"] = "Assigning of modules for `{$roleDescription}` has been updated.";
				$resultset["response"] = true;				
			}else{
				$resultset["toastr_msg"] = "Error assigning of modules for `{$roleDescription}`!";
				$resultset["response"] = false;	
			}
		}else{
			$resultset["toastr_msg"] = "Failed to assign modules for `{$roleDescription}`!";
			$resultset["response"] = false;
		}
		
		$tempStatus = ($resultset["response"])? "success": "error";
		$this->core_layout->logNotification($resultset["toastr_msg"], $tempStatus, "users");

		return $resultset;
	}

	function setUserRoleModule($id=null, $ids=array()){
		if($id || $id == "0"){
			$query = $this->db->get_where($this->roleTable, array("id"=>$id, "is_active"=>1));
			if($query->num_rows() > 0){
				$row = $query->row_array();
				$arrData = array();
				$arrData["role_id"] = $row["id"];
				$arrData["name"] = $row["name"];
				$arrData["module_resource"] = serialize($ids);
				
				$querySearch = $this->db->get_where($this->roleAclTable, array("role_id"=>$row["id"]));
				if($querySearch->num_rows() == 1){
					$rowSearch = $querySearch->row_array();
					$where = array();
					$where["id"] = $rowSearch["id"];
					
					$nData = array();
					$nData["module_resource"] = $arrData["module_resource"];
					$update = $this->db->update($this->roleAclTable, $nData, $where);
					if($update){ return true; }
					else{ return false; }
				}else{
					$saved = $this->db->insert($this->roleAclTable, $arrData);
					if($saved){ return true; }
					else{ return false; }
				}
			}else{
				return false;
			}	
		}else{
			return false;
		}
	}
	
	function getAssignedUserRoleModuleList($id=null){
		if($id || $id == "0"){
			$query = $this->db->get_where($this->roleAclTable, array("role_id"=>$id));
			if($query->num_rows() == 1){
				$row = $query->row_array();
				return unserialize($row["module_resource"]);
			}else{
				return array();
			}
		}else{
			return array();
		}
	}

	function getRoleUpdate(){
		$post = $this->input->post();
		$resultset = array();
		if(isset($post["id"]) && $post["id"] || $post["id"] == "0"){
			$query = $this->db->get_where($this->roleTable, $post);
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

	function getAssignedRole(){
		$post = $this->input->post();
		$resultset = array();
		if(isset($post["id"]) && $post["id"] || $post["id"] == "0"){
			$query = $this->db->get_where($this->roleTable, $post);
			if($query->num_rows() > 0){
				$includes = array("id", "name", "label", "url", "icon", "identifier");
				$arrData["row"] = $query->row_array();
				
				$roles = $this->getAssignedUserRoleList($post["id"]);
				$aclData = $this->acl_model->accessControlJson($post["id"], $roles);
				$html = $this->load->view("core/roles/modal_content/list", $arrData, true);
				
				$resultset["html"] = $html;
				$resultset["data"] = $aclData;
				$resultset["role_id"] = $roles;
				$resultset["response"] = true;
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;			
		}
		
		return $resultset;
	}	
	function setAssignedUserRole(){
		$post = $this->input->post();
		$resultset = array();
		if(isset($post["nodes"], $post["id"]) && $post["nodes"] && $post["id"] || $post["id"] == "0"){
			$idx = $post["id"];
			$tempRole = $this->db->get_where($this->roleTable, array("id"=>$post["id"]));
			$roleDescription = ($tempRole->num_rows() == 1)? strtoupper($tempRole->row()->description): "NO ROLE";

			$nodes = json_decode($post["nodes"], true);
			$ids = array();
			foreach($nodes as $key => $value){
				$selected = $value["state"]["selected"];
				$parentId = intval($value["parent"]);
				$id = $value["id"];
				
				if($parentId !== 0 && $selected && !in_array($parentId, $ids)){ $ids[] = $parentId; }
				if($selected){ $ids[] = $id; }
			}
			
			$ids = array_unique($ids);

			$setRole = $this->setUserRoleAcl($idx, $ids);
			if($setRole){
				$resultset["toastr_msg"] = "Assigning of access control for `{$roleDescription}` has been updated.";
				$resultset["response"] = true;				
			}else{
				$resultset["toastr_msg"] = "Error assigning of access control for `{$roleDescription}`!";
				$resultset["response"] = false;	
			}
		}else{
			$resultset["toastr_msg"] = "Failed to assign access control for `{$roleDescription}`!";
			$resultset["response"] = false;
		}
		
		$tempStatus = ($resultset["response"])? "success": "error";
		$this->core_layout->logNotification($resultset["toastr_msg"], $tempStatus, "users");
		return $resultset;
	}
	
	function setAssignedUserRoleActions(){
		$post = $this->input->post();
		$resultset = array();
		if(isset($post["nodes"], $post["id"]) && $post["nodes"] && ($post["id"] || $post["id"] == "0")){
			$idx = $post["id"];
			$tempRole = $this->db->get_where($this->roleTable, array("id"=>$post["id"]));
			$roleDescription = ($tempRole->num_rows() == 1)? strtoupper($tempRole->row()->description): "NO ROLE";

			$nodes = json_decode($post["nodes"], true);
			$ids = array();
			foreach($nodes as $key => $value){
				$selected = $value["state"]["selected"];
				$parentId = intval($value["parent"]);
				$id = $value["id"];
				
				if($parentId !== 0 && $selected && !in_array($parentId, $ids)){ $ids[] = $parentId; }
				if($selected){ $ids[] = $id; }
			}
			
			$ids = array_unique($ids);
			$setRole = $this->setUserRoleAclActions($idx, $ids);
			if($setRole){
				$resultset["toastr_msg"] = "Assigning of user privilege for `{$roleDescription}` has been updated.";
				$resultset["response"] = true;				
			}else{
				$resultset["toastr_msg"] = "Error assigning of user privilege for `{$roleDescription}`!";
				$resultset["response"] = false;	
			}
		}else{
			$resultset["toastr_msg"] = "Failed to assign user privilege for `{$roleDescription}`!";
			$resultset["response"] = false;
		}
		
		$tempStatus = ($resultset["response"])? "success": "error";
		$this->core_layout->logNotification($resultset["toastr_msg"], $tempStatus, "users");
		return $resultset;
	}
	function setUserRoleAclActions($id=null, $ids=array()){
		if($id || $id=="0"){
			$query = $this->db->get_where($this->roleTable, array("id"=>$id, "status"=>1));
			if($query->num_rows() > 0){
				$row = $query->row_array();
				$arrData = array();
				$arrData["privilege_resource"] = serialize($ids);
				
				$whereData = array();
				$whereData["role_id"] = $row["id"];
				
				$update = $this->db->update($this->roleAclTable, $arrData, $whereData);
				if($update){ return true; }
				else{ return false; }
			}else{ return false; }	
		}else{ return false; }
	}
	
	function setUserRoleAcl($id=null, $ids=array()){
		if($id || $id == "0"){
			$query = $this->db->get_where($this->roleTable, array("id"=>$id, "status"=>1));
			if($query->num_rows() > 0){
				$row = $query->row_array();
				$arrData = array();
				$arrData["role_id"] = $row["id"];
				$arrData["name"] = $row["name"];
				$arrData["role_resource"] = serialize($ids);
				
				$querySearch = $this->db->get_where($this->roleAclTable, array("role_id"=>$row["id"]));
				if($querySearch->num_rows() == 1){
					$rowSearch = $querySearch->row_array();
					$where = array();
					$where["id"] = $rowSearch["id"];
					
					$nData = array();
					$nData["role_resource"] = $arrData["role_resource"];
					$update = $this->db->update($this->roleAclTable, $nData, $where);
					if($update){ return true; }
					else{ return false; }
				}else{
					$saved = $this->db->insert($this->roleAclTable, $arrData);
					if($saved){ return true; }
					else{ return false; }
				}
			}else{
				return false;
			}	
		}else{
			return false;
		}
	}
	
	function getRolePrivilegeData(){
		$post = $this->input->post();
		$resultset = array();
		if(isset($post["id"]) && ($post["id"] || $post["id"] == "0")){
			$query = $this->db->get_where($this->roleTable, array("id"=>$post["id"]));
			if($query->num_rows() == 1){
				$row = $query->row_array();
				if($row){
					$arrData = array();
					$arrData["row"] = $row;
					
					$html = $this->load->view("core/roles/modal_content/list_actions", $arrData, true);
					$actions = $this->getAssignedUserRoleActionList($post["id"]);
					$data = $this->getRoleResource($post["id"], $actions);
					$resultset["html"] = $html;
					$resultset["data"] = $data;
					$resultset["role_id"] = $actions;
					$resultset["response"] = true;
				}else{					
					$resultset["response"] = false;
				}
			}else{
			$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}
		
		return $resultset;
	}
	
	function getRoleResource($id=null, $roleIds=array()){
		$arrData = array();
		if($id || $id == "0"){
			$query = $this->db->get_where($this->roleAclTable, array("role_id"=>$id));
			if($query->num_rows() == 1){
				$row = $query->row_array();
				$roleResource = unserialize($row["role_resource"]);
				$moduleResource = unserialize($row["module_resource"]);
				$arrModules = array();
				if($moduleResource){
					foreach ($moduleResource as $key => $tempId) {
						$this->db->select("a.label, b.module_resource");
						$this->db->from($this->moduleTable." a");
						$this->db->join($this->modulePrivilegeTable." b", "b.module_id = a.id");
						$this->db->where(array("a.id"=>$tempId, "a.is_active"=>1, "a.status"=>1));
						$tempModule = $this->db->get();
						if($tempModule->num_rows() == 1){
							$tempRow = $tempModule->row();
							$tempResource = unserialize($tempRow->module_resource);
							foreach ($tempResource as $key => $value) {
								$arrModules[$value] = strtoupper($tempRow->label);
							}
						}
					}
				}

				if($roleResource){
					foreach($roleResource as $id){
						$aclPage = explode("-", $id);
						if(count($aclPage) == 2){
							$aclId = $aclPage[1];
							if($aclId){
								$queryAcl = $this->db->get_where($this->aclTable, array("id"=>$aclId, "is_active"=>1));
								if($queryAcl->num_rows() == 1){
									$tempRow = $queryAcl->row();
									$row = $queryAcl->row_array();
									$tempModuleAcl = (isset($arrModules[$tempRow->id]) && $arrModules[$tempRow->id])? $arrModules[$tempRow->id]: "No Module";
									
									$data = array();
									$data["id"] = $row["id"];
									$data["text"] = $tempModuleAcl." - ".$row["label"];
									$data["parent"] = "#";
									$data["a_attr"]["class"] = "no_checkbox";
									
									$queryAclPriv = $this->db->get_where($this->aclPrivTable, array("acl_id"=>$row["id"]));
									if($queryAclPriv->num_rows() == 1){
										$rowAclPriv = $queryAclPriv->row_array();
										$aclResource = unserialize($rowAclPriv["acl_resource"]);
										if($aclResource){
											$arrData[] = $data;
											foreach($aclResource as $idx){
												$queryList = $this->db->get_where($this->privilegeListTable, array("id"=>$idx, "status"=>1));
												if($queryList->num_rows() == 1){
													$rowx = $queryList->row_array();
													$datax = array();
													$tempItemIdx = $row["id"]."-".$rowx["id"];
													$tempStatex = (is_array($roleIds) && in_array($tempItemIdx, $roleIds, true))? true: false;
													
													$datax["id"] = $tempItemIdx;
													$datax["text"] = $rowx["label"];
													$datax["parent"] = $row["id"];
													$datax["state"]["selected"] = $tempStatex;
													$arrData[] = $datax;
												}
											}
										}
									}
								}

							}
						}
					}
				}
			}
		}
		return $arrData;
	}
	
	function getAssignedUserRoleList($id=null){
		if($id || $id == "0"){
			$query = $this->db->get_where($this->roleAclTable, array("role_id"=>$id));
			if($query->num_rows() == 1){
				$row = $query->row_array();
				return unserialize($row["role_resource"]);
			}else{
				return array();
			}
		}else{
			return array();
		}
	}
	
	function getAssignedUserRoleActionList($id=null){
		if($id || $id=="0"){
			$query = $this->db->get_where($this->roleAclTable, array("role_id"=>$id));
			if($query->num_rows() == 1){
				$row = $query->row_array();
				return unserialize($row["privilege_resource"]);
			}else{
				return array();
			}
		}else{
			return array();
		}
	}
	private function checkAclName($data=array()){
		if($data){
			$query = $this->db->get_where($this->roleTable, array("name"=>$data["name"]));
			if($query->num_rows() == 0){ return true; }
			else{ return false; }
		}else{ return false; }
	}

	public function cloneUserRole(){
		$post = $this->input->post();
		$result = array();
		$post["name"] = strtolower($post["name"]."_".date("dYmHis"));
		$post["description"] = strtolower($post["description"]);
		$this->db->select("module_resource, role_resource, privilege_resource");
		$role = $this->db->get_where($this->roleAclTable, array("role_id"=>$post["id"]));
		if($role->num_rows() === 1){
			$rowAcl = $role->row_array();
			unset($post["id"]);

			$aclName = $this->checkAclName($post);
			if($aclName){
				if(!isset($post["role_code"])){ $post["role_code"] = $this->core_layout->generateCode(); }
				$post["is_active"] = 1;
				$post["status"] = 1;
				$insert = $this->db->insert($this->roleTable, $post);
				if($insert && $this->db->affected_rows() > 0){
					$lastId = $this->db->insert_id();
					$rowAcl["role_id"] = $lastId;
					$rowAcl["name"] = $post["name"];
					$rowAcl["is_active"] = 1;
					$this->db->insert($this->roleAclTable, $rowAcl);
					$result["response"] = true; 
					$result["toastr_msg"] = "User role has been clone successfully.";
				}else{
					$result["response"] = false;
					$result["toastr_msg"] = "Error in saving user role, user role description `".$post["description"]."`!!";
				}
			}else{
				$result["response"] = false;
				$result["toastr_msg"] = "Error in saving user role, user role `".$post["name"]."` already exist!";
			}
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "Error in saving user role, user role role description `".$post["description"]."` already exist!";
		}

		return $result;
	}
}