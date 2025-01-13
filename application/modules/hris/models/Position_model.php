<?php defined('BASEPATH') || exit('No direct script access allowed');
class Position_model extends CI_Model{
	protected $positionTable = "gcchris.tblposition";
	protected $archivedTable = "gccmaster.archived_items";
	protected $employeeTable = "gccmaster.tblemployees";
	protected $departmentTable = "gcchris.tbldepartments";
	
	function __construct(){
		parent::__construct();
		$this->load->model("hris/employee_model", "adm_employee");
		$this->load->model("core/datatable_model","dt_model");
		$this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
        $this->loggedInUsername = $this->loggedinData["username"];
    }

    function getPositionDatatableRequest(){
        $post = $this->input->post();
		if($post){
			$orderx = (isset($post["order"]) && $post["order"])? $post["order"]: false;
			$columns = array("position.name", "position.type", "position.id", "position.is_archived", "employees.lastname", "employees.firstname", "employees.middlename", "employees.suffix");
			$dir = "DESC";
			$order = "position.id";
			if($orderx){
				$dir = $orderx[0]["dir"];
				$order = $columns[$orderx[0]["column"]];
			}
				
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$dtTemp = $this->dt_model->dataTable();
            $dtTemp->setTable($this->positionTable);
            $dtTemp->setTableAlias("position");

			$dtTemp->setParameterFields($columns);
            
            $joinTable = array();
			$joinTable["table"][$this->employeeTable] = "employees";
			$joinTable["fields"][] = "employees.id=position.created_by";
			$joinTable["field_loc"][] = "LEFT";
			
			// $joinTable["table"][$this->departmentTable] = "department";
			// $joinTable["fields"][] = "department.id = position.department_id";
			// $joinTable["field_loc"][] = "LEFT";
			
			$dtTemp->setJoinTable($joinTable);
            $dtTemp->setWhereInField("position.id");
            
			$parameters = array();
			$parameters["position.is_archived"] = (isset($post['is_archived']) && $post['is_archived'] == 1) ? 1 : 0;
			
			$dtTemp->setWhereParameters($parameters);

			$totalData = $dtTemp->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){
				$posts = $dtTemp->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $dtTemp->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $dtTemp->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
                    $tempRs = (array) $pst;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $pst->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
					// if ($pst->description == null){
					// 	$pst->description = "No assigned department";
					// }
					$nestedData = array();
					$nestedData['id'] = $pst->id;
					$nestedData['name'] = $pst->name;
					$nestedData['type'] = $pst->type;
					// $nestedData['description'] = $pst->description;
					$nestedData['created_by'] = $pst->display_name;
					$data[] = $nestedData;
				}
			}
			return array(
                    "draw" => intval($draw),  
                    "recordsTotal" => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,  
                    );
		}else{
			return array(
				"draw"=>1,
				"recordsTotal"=>0,
				"recordsFiltered"=>0,
				"data"=>array(),
				);
		}
    }

    function getPositionModalContent($content="add"){
		$resultset = array();
		$html = "";
        $arrData = array();
        
		if($content == "add"){
			$html = $this->load->view("hris/masterfile/position/modals/add_content", null, true);
        }
        
		if($content == "edit"){
			$post = $this->input->post();
			if(isset($post) && $post){
				unset($post["csrf_token"]);
				$tempCompany = $this->db->get_where($this->positionTable, $post);
				if($tempCompany->num_rows() == 1){
					$arrData = $tempCompany->row();
				}
			}
			$html = $this->load->view("hris/masterfile/position/modals/edit_content", array("data"=>$arrData), true);
		}

		if($html){
			$resultset["response"] = true;
			$resultset["html"] = $html;
			$resultset["data"] = $arrData;
		}else{
			$resultset["response"] = false;
		}
		
		return $resultset;
    }


    function setModalPosition(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
            unset($post["csrf_token"]);
            
            $post["created_dt"] = date("Y-m-d H:i:s");
            $post["created_by"] = $session["emp_id"];

			$allow = $this->checkPositionName($post);
			if($allow){
				$insert = $this->db->insert($this->positionTable, $post);
				if($insert){
					$resultset["response"] = true;
					$resultset["toastr_msg"] = "Position data has been added.";
					$this->core_layout->setEventLog("User added new position: <strong>".$post['name']."</strong>","insert", "success", "gcchris", "user");
				}else{
					$resultset["response"] = false;
					$resultset["toastr_msg"] = "Failed saving position data!";
					$this->core_layout->setEventLog("User failed adding new position","insert", "error", "gcchris", "system");
				}
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Position name already exist!";
				$this->core_layout->setEventLog("User failed inserting existing position","insert", "error", "gcchris", "system");
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Position masterfile - Error, No post data found.","insert", "error", "gcchris", "system");
		}
        
        return $resultset;
    }

    function updateModalPosition(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			$id = $post["id"];
            unset($post["csrf_token"], $post["id"]);
            
			$post["modify_dt"] = date("Y-m-d H:i:s");
            $post["modify_by"] = $session["emp_id"];
			$currentPositionData = $this->getPositionData($id);
			$update = $this->db->update($this->positionTable, $post, array("id"=>$id));
			unset($post["modify_by"],$post["modify_dt"]);
			if($update){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Position data has been updated.";
				$changes = $this->logChanges($currentPositionData ,$post);
				$this->core_layout->setEventLog("User updated position: <strong>".$currentPositionData->name."</strong> ".$changes,"update", "success", "gcchris", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed updating position data!";
				$this->core_layout->setEventLog("User failed updating position. <strong>$currentPositionData->name</strong>","update", "error", "gcchris", "system");
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Position masterfile - Error, No post data found.","update", "error", "gcchris", "system");
		}
        
        return $resultset;
    }
    
    function removeCurrentPosition(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$updated = $this->db->update($this->positionTable, array("is_archived"=>1), $post);
			$currentPositionData = $this->getPositionData($post["id"]);
			if($updated){
				$session = $this->core_layout->getCurrentSession();
				$data = array(
					"archived_table"=>$this->positionTable,
					"archived_id"=>$post["id"],
					"archived_by"=>$session["emp_id"]
				);

				$this->db->insert($this->archivedTable, $data);

				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Position has been removed.";
				$this->core_layout->setEventLog("User has archived position: <strong>".$currentPositionData->name."</strong>","archive", "success", "gcchris", "user");
				
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to remove position!";
				$this->core_layout->setEventLog("User has failed archiving position: <strong>".$currentPositionData->name."</strong>","archive", "error", "gcchris", "system");
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Position masterfile - Error, No post data found.","archive", "error", "gcchris", "system");
		}

		return $resultset;
    }
    
    private function checkPositionName($data=array()){
		if($data){
			$query = $this->db->get_where($this->positionTable, array("name"=>$data["name"]));
			return $query->num_rows() == 0 ? true : false;
		}else{ return false; }
	}

	public function getPositionSelect2Data(){
		$resultset = array();
		$arrData = array();

		$get = $this->input->get();
		$this->db->select("id, name as text");
		$this->db->from($this->positionTable);
		$this->db->where("is_archived", 0);
		if(isset($get["term"]) && $get["term"]){ $this->db->like("name", $get["term"], "both"); }
		$query = $this->db->get();

		if($query->num_rows() > 0){
			$arrData = $query->result();
		}
		
		$resultset["results"] = $arrData;
		return $resultset;
	}

	function getPositionJobDescriptionQ($id=null){
		$resultset = array();
		if($id){
			$this->db->select("job_desc as job_description, qualification");
			$query = $this->db->get_where($this->positionTable, array("id"=>$id));
			if($query->num_rows() == 1){
				$row = $query->row();
				
				$row->job_description = strip_tags($row->job_description);
				$row->qualification = strip_tags($row->qualification);

				$resultset["response"] = true;
				$resultset["data"] = $row;
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}
		return $resultset;
	}

	public function getPositionSelect2DataByDepartmentId(){
		$resultset = array();
		$arrData = array();
		$get = $this->input->get();
		if(isset($get["department_id"]) && $get["department_id"]){
			$this->db->select("id, name as text");
			$this->db->from($this->positionTable);
			$this->db->where("is_archived", 0);
			$this->db->where("department_id", $get["department_id"]);
			if(isset($get["term"]) && $get["term"]){ $this->db->like("name", $get["term"], "both"); }
			$this->db->order_by("name", "ASC");
			$query = $this->db->get();	
			if($query->num_rows() > 0){ $arrData = $query->result(); }
		}
		$resultset["results"] = $arrData;
		return $resultset;
	}

	public function select2PositionData(){
		$this->db->select("positions.id, TRIM(UPPER(positions.`name`)) as `text`, positions.*");
		$this->db->where("positions.is_archived", 0);
		$this->db->group_start();
		$this->db->where("positions.name !=", NULL);
		$this->db->where("positions.name !=", "");
		$this->db->group_end();
        $this->db->order_by("TRIM(positions.`name`)", "ASC");
        $this->db->group_by("TRIM(positions.`name`)");
        $results = $this->db->get("gcchris.tblposition positions")->result();
        return $results;
	}

	function restoreCurrentPosition(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$updated = $this->db->update($this->positionTable, array("is_archived"=>0), $post);
			$currentPositionData = $this->getPositionData($post["id"]);
			if($updated){
				// $session = $this->core_layout->getCurrentSession();
				// $data = array(
				// 	"archived_table"=>$this->positionTable,
				// 	"archived_id"=>$post["id"],
				// 	"archived_by"=>$session["emp_id"]
				// );

				// $this->db->insert($this->archivedTable, $data);

				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Position has been restored.";
				$this->core_layout->setEventLog("User has restored position: <strong>".$currentPositionData->name."</strong>","restore", "success", "gcchris", "user");
				
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to restore position!";
				$this->core_layout->setEventLog("User has failed restoring position: <strong>".$currentPositionData->name."</strong>","restore", "error", "gcchris", "system");
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Position masterfile - Error, No post data found.","restore", "error", "gcchris", "system");
		}

		return $resultset;
    }

	private function getPositionData($id) {
		$this->db->select("*");
		$this->db->from($this->positionTable);
		$this->db->where('id', $id);
		$query = $this->db->get(); 
		return $query->row();
	}

	private function logChanges($currentData, $newData) {
		$changes = array();
		$changesString = '';
		foreach ($currentData as $field => $value) {
			if (isset($newData[$field]) && $newData[$field]!= $value) {
				$changes[$field] = array(
					'old' => $value,
					'new' => $newData[$field]
				);
			}
		}
		foreach ($changes as $field => $change) {
			$changesString.= " Field: $field, from: <strong>$change[old]</strong>, to: <strong>$change[new]</strong>\n";
		}
		return $changesString;
	}

	
}