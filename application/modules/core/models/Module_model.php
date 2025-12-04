<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Module_model extends CI_Model{
	protected $moduleTable = "modules";
	protected $allowedModulesTable = "allowed_modules";
	protected $modulePrivilegeTable = "module_privilege";
	protected $archiveTable = "archived_items";

	protected $tokenName, $tokenHash;
	
	function __construct(){
		parent::__construct();
		$this->tokenName = $this->security->get_csrf_token_name();
		$this->tokenHash = $this->security->get_csrf_hash();
		
		$this->load->model("core/datatable_model","dt_model");
		$this->load->model("core/access_control_model","acl_model");
	}
	
	function getModuleTreeRoot(){
		$resultset = array();
		$id = $this->authenticate->getRoleId();
		$query = $this->db->get_where("user_role_acl", array("role_id"=>$id));
		if($query->num_rows() > 0){
			$row = $query->row();
			$moduleResource = unserialize($row->module_resource);
			if($moduleResource && count($moduleResource) > 0){
				$rootModule = $moduleResource[0];
				$query = $this->db->get_where($this->moduleTable, array("id"=>$rootModule, "is_active"=> 1, "status"=>1));
				if($query->num_rows() == 1){
					$row = $query->row();
					$resultset["response"] = true;
					$resultset["component_id"] = $row->id;
					$resultset["component_file"] = $row->url;
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
	
	function addModule(){
		$post = $this->input->post();
		$result = array();
		$result["{$this->tokenName}"] = $this->tokenHash;
		
		if($post){
			$tempName = $this->checkTempName($post);
			if($tempName){
				unset($post["csrf_token"]);
				$session = $this->core_layout->getCurrentSession();
				$post["created_by"] = $session["id"];
			
				if(!isset($post["module_code"])){ $post["module_code"] = $this->core_layout->generateCode(); }
				$insert = $this->db->insert($this->moduleTable, $post);
				if($insert){
					$result["response"] = true; 
					$result["toastr_msg"] = "Module has been saved.";
				}else{
					$result["response"] = false;
					$result["toastr_msg"] = "Error in saving module!";
				}
			}else{
				$result["response"] = false;
				$result["toastr_msg"] = "Module name already exist!";
			}
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "No available data to save!";
		}
		
		return $result;
	}
	
	function updateModule(){
        $post = $this->input->post();
        $resultset = array();
		
        if(isset($post) && $post){
			$where = array();
			$where["id"] = $post["id"];
			$currentName = $post["current_name"];
			unset($post["csrf_token"], $post["id"], $post["current_name"]);
			
			$allowUpdate = true;
			if($currentName !== $post["name"]){
				$allowUpdate = $this->checkTempName($post);
			}

			if($allowUpdate){
				$session = $this->core_layout->getCurrentSession();
				$post["updated_by"] = $session["id"];
				$post["updated_at"] = date("Y-m-d H:i:s");
	
				$update = $this->db->update($this->moduleTable, $post, $where);
				if($update){
					$resultset["response"] = true;
					$resultset["toastr_msg"] = "Module has been updated.";
				}else{
					$resultset["response"] = false;
					$resultset["toastr_msg"] = "Error in updating module!";
				}
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Module name `{$post["name"]}` already exist!";
			}
			
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No available data to update!";
        }

        return $resultset;
	}

	function removeModule(){
        $post = $this->input->post();
        $resultset = array();

        if(isset($post) && $post){
            $tempId = $post["id"];
            unset($post["csrf_token"], $post["id"]);

            $where = array();
            $where["id"] = $tempId;

            $update = $this->db->update($this->moduleTable, array("status"=>0), $where);
            if($update){
                $findQuery = $this->db->get_where($this->archiveTable, array("archived_table"=>$this->moduleTable, "archived_id"=>$tempId));
                if($findQuery->num_rows() == 0){
                    $session = $this->core_layout->getCurrentSession();
                    $data = array(
                        "archived_table"=>$this->moduleTable,
                        "archived_id"=>$tempId,
                        "archived_by"=>$session["id"]
                    );

                    $this->db->insert($this->archiveTable, $data);
                }

                $resultset["response"] = true;
                $resultset["toastr_msg"] = "Module has been removed.";
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to removed module!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No data found!";
        }

        return $resultset;
	}
	
	function getModuleList(){
		$post = $this->input->post();
		if($post){
			$columns = array("id", "name", "label", "description", "url", "identifier", "is_active", "status");
			$dir = $post["order"][0]["dir"];
			$order = $columns[$post["order"][0]["column"]];
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			
			$dtAccessControl = $this->dt_model->dataTable();
			$dtAccessControl->setTable($this->moduleTable);
			$dtAccessControl->setParameterFields($columns);
			
			$parameters = array();
            $parameters["status"] = 1;

			$dtAccessControl->setWhereParameters($parameters);
			
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
					$nestedData['label'] = $pst->label;
					$nestedData['description'] = $pst->description;
					$nestedData['url'] = $pst->url;
					$nestedData['identifier'] = $pst->identifier;
					$nestedData['is_active'] = $pst->is_active;
					$data[] = $nestedData;
				}
			}
			$json_data = array(
                    "draw" => intval($draw),  
                    "recordsTotal" => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data" => $data,
					"{$this->tokenName}" => $this->tokenHash
                    );
            
			return $json_data;
		}else{
			return array(
				"draw"=>1,
				"recordsTotal"=>0,
				"recordsFiltered"=>0,
				"data"=>array(),
				"{$this->tokenName}" => $this->tokenHash
				);
		}
	}
	
	function getJsonModule(){
		$post = $this->input->post();
		$result = array();
		if(isset($post["nodes"]) && $post["nodes"]){
			$jsonData = json_decode($post["nodes"], true);
			$arrData = array();
			
			$sort = array();
			$sortParent = 1;
			$sortChild = 1;
			
			foreach($jsonData as $key => $value){
				$where = array();
				$where["id"] = intval($value["id"]);
				
				$data = array();
				$data["parent_id"] = intval($value["parent"]);
				$data["sort"] = (intval($value["parent"]) == 0)? intval($sortParent): intval($sortChild);
				$this->db->update($this->moduleTable, $data, $where);
				
				if(intval($value["parent"]) == 0){
					$sortChild = 1;
					$sortParent++;
				}else{ $sortChild ++; }
			}
			$result["response"] = true;
			$result["toastr_msg"] = "Module tree list update successful.";
		}else{
			$result["response"] = false;
			$result["toastr_msg"] = "Error, no data found!";
		}
		
		return $result;
	}
	
	function getModuleTreeList(){
		$arrData = $this->moduleTreeJson();
		$html = $this->load->view("core/module/modal_content/list", null, true);
		
		$result = array();
		$result["response"] = true;
		$result["data"] = $arrData;
		$result["html"] = $html;
		
		return $result;
	}
	
	function moduleTreeJson(){
		$arrData = array();
		$this->db->where("is_active", 1);
		$this->db->where("status", 1);
		$this->db->order_by("sort", "ASC");
		$query = $this->db->get($this->moduleTable);
		
		if($query->num_rows() > 0){
			foreach($query->result_array() as $rs){
				$flag = false;
				$data = array();
				$data["id"] = $rs["id"];
				$data["type"] = ($rs["parent_id"])? "child":"root";
				$data["parent"] = ($rs["parent_id"])? $rs["parent_id"]: "#";
				$data["text"] = $rs["label"];
				
				$parentId = intval($rs["parent_id"]);
				if($parentId !== 0){
					$flag = $this->checkParentExist($parentId);
				}else{ $flag = true; }
				if($flag){ $arrData[] = $data; }
			}
		}
		return $arrData;
	}
		
	function getModuleData($includes=array(), $isActive=0, $parentId=0, $sortOrder="ASC"){
		$filter = ($includes)? implode(",", $includes): "*";
		$where = array();
		$where["parent_id"] = $parentId;
		$where["is_active"] = $isActive;
		$where["status"] = 1;
		
		$this->db->select($filter);
		$this->db->from($this->moduleTable);
		$this->db->where($where);
		$this->db->order_by("sort", $sortOrder);
		$query = $this->db->get();
		
		if($query->num_rows() > 0){ return $query->result_array(); }
		else{ return false; }
	}
	
	function getModuleMenu($includes=array(), $isActive=0, $sort="ASC"){
		$arrData = array();
		$parent = $this->getModuleData($includes, $isActive, 0, $sort);
		if($parent){
			foreach($parent as $pRs){
				$children = $this->getModuleData($includes, $isActive, $pRs["id"], $sort);
				if($children){ $pRs["children"] = $children; }
				else{ $pRs["children"] = array(); }
				$arrData[] = $pRs;
			}			
		}
		
		return $arrData;
	}
	
	function modulesJson(){
		$arrData = array();
		$this->db->where("is_active", 1);
		$this->db->where("status", 1);
		$this->db->order_by("sort", "ASC");
		$query = $this->db->get($this->moduleTable);
		
		if($query->num_rows() > 0){
			foreach($query->result_array() as $rs){
				$flag = false;
				$data = array();
				$data["id"] = $rs["id"];
				$data["type"] = ($rs["parent_id"])? "child":"root";
				$data["parent"] = ($rs["parent_id"])? $rs["parent_id"]: "#";
				$data["text"] = $rs["label"];
				
				$parentId = intval($rs["parent_id"]);
				if($parentId !== 0){
					$flag = $this->checkParentExist($parentId);
				}else{ $flag = true; }
				if($flag){ $arrData[] = $data; }
			}
		}
		return $arrData;
	}

	function checkParentExist($parentId=null){
		if($parentId){
			$query = $this->db->get_where($this->moduleTable, array("is_active"=>1, "status"=>1, "id"=>$parentId));
			if($query->num_rows() > 0){
				return true;						
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	private function checkTempName($data=array()){
		if($data){
			$query = $this->db->get_where($this->moduleTable, array("name"=>$data["name"], "status"=>1));
			if($query->num_rows() == 0){ return true; }
			else{ return false; }
		}else{ return false; }
	}
	
	private function getTempName($data=array()){
		if(isset($data["name"]) && $data["name"]){
			$data = $data["name"];
			$new_data = str_replace("'", "", $data);
			$new_data = preg_replace('/[^\p{L}\p{N}]/u', '_', $new_data);
			return strtolower($new_data);
		}else{
			return false;
		}
	}

	public function getCurrentModule(){
		$post = $this->input->post();
        $resultset = array();

        if(isset($post) && $post){
            unset($post["csrf_token"]);
            $query = $this->db->get_where($this->moduleTable, array("id"=>$post["id"], "status"=>1));
            if($query->num_rows() == 1){
                $resultset["response"] = true;
                $resultset["row"] = $query->row();
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }

        return $resultset;
	}

	public function getAssignedAclModule(){
		$post = $this->input->post();
		$resultset = array();

		if(isset($post["id"]) && ($post["id"] || $post["id"] == "0")){
			unset($post["csrf_token"]);

			$arrData = array();
			$this->db->from($this->moduleTable);
			$this->db->where("id", $post["id"]);
			$this->db->where("status", 1);
			$query = $this->db->get();

			$row = ($query->num_rows() > 0)? $query->row_array(): array();
			$html = $this->load->view("core/module/modal_content/list_action", array("row"=>$row), true);
			
			$module_id = $this->getAssignedAclModuleList($post["id"]);
			$data = $this->acl_model->aclJson();
			$allowedIp = $this->getAllowedModuleIps($post["id"]);

			$resultset["response"] = true;
			$resultset["html"] = $html;
			$resultset["data"] = $data;
			$resultset["module_id"] = $module_id;
			$resultset["allowed_ip"] = $allowedIp;
		}else{
			$resultset["response"] = false;		
		}
		
		return $resultset;
	}

	function getAssignedAclModuleList($id=null){
		if($id || $id == "0"){
			$query = $this->db->get_where($this->modulePrivilegeTable, array("module_id"=>$id));
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

	protected function moduleResourceExist($id=null){
		if($id){
			$query = $this->db->get_where($this->modulePrivilegeTable, array("module_id"=>$id));
			if($query->num_rows() == 1){
				$row = $query->row_array();
				return $row;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function setModuleJsonActions(){
		$post = $this->input->post();
		$resultset = array();
		
		if(isset($post["nodes"], $post["id"]) && ($post["nodes"] && $post["id"])){
			$id = $post["id"];
			$nodes = json_decode($post["nodes"], true);
			if($nodes){
				$ids = array();
				foreach($nodes as $node){
					$selected = $node["state"]["selected"];
					if($selected){
						$ids[] = intval($node["id"]);
					}
				}
				
				$moduleExist = $this->moduleResourceExist($id);
				if($moduleExist){
					$where = array();
					$where["id"] = $moduleExist["id"];
					
					$arrData = array();
					$arrData["module_resource"] = serialize($ids);
					$update = $this->db->update($this->modulePrivilegeTable, $arrData, $where);
					if($update){
						$resultset["response"] = true;
						$resultset["toastr_msg"] = "Module privilege has been updated.";
					}else{
						$resultset["toastr_msg"] = "Data update failed!";
						$resultset["response"] = false;
					}
				}else{
					$arrData = array();
					$arrData["module_id"] = $id;
					$arrData["module_resource"] = serialize($ids);
					
					$saved = $this->db->insert($this->modulePrivilegeTable, $arrData);
					if($saved){
						$resultset["toastr_msg"] = "Module privilege has been saved.";
						$resultset["response"] = true;
					}else{
						$resultset["toastr_msg"] = "Saving of data failed!";
						$resultset["response"] = false;
					}
				}
				
			}else{
				$resultset["toastr_msg"] = "No available nodes!";			
				$resultset["response"] = false;				
			}
		}else{
			$resultset["toastr_msg"] = "No data found!";
			$resultset["response"] = false;
		}
		
		return $resultset;
	}

	public function setAllowedIp(){
		$post = $this->input->post();
		$response = false;
		if(isset($post["module_id"]) && $post["module_id"]){
			unset($post["csrf_token"]);
			$post["allowed_ip"] = serialize($post["allowed_ip"]);
			$allMods = $this->db->get_where($this->allowedModulesTable, array("module_id"=>$post["module_id"]));
			if($allMods->num_rows() === 1){
				unset($post["module_id"]);
				$response = $this->db->update($this->allowedModulesTable, $post, array("id"=>$allMods->row()->id));
			}else{
				$response = $this->db->insert($this->allowedModulesTable, $post);
			}
		}
		return array("response"=>$response);
	}

	protected function getAllowedModuleIps($id=null){
		$arrIps = array();
		if($id){
			$query = $this->db->get_where($this->allowedModulesTable, array("module_id"=>$id));
			if($query->num_rows() == 1){
				$row = $query->row_array();
				$arrIps = @unserialize($row["allowed_ip"]) ?? array();
			}
		}
		return $arrIps;
	}
}