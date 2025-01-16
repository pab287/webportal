<?php defined('BASEPATH') || exit('No direct script access allowed');
class Department_model extends CI_Model{
	protected $departmentTable = "gcchris.tbldepartments";
	protected $archivedTable = "gccmaster.archived_items";
	protected $employeeTable = "gccmaster.tblemployees";
	
	function __construct(){
		parent::__construct();
		$this->load->model("hris/employee_model", "adm_employee");
		$this->load->model("core/datatable_model","dt_model");
		$this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
        $this->loggedInUsername = $this->loggedinData["username"];
    }

    function getDepartmentDatatableRequest(){
        $post = $this->input->post();
		if($post){
			$orderx = (isset($post["order"]) && $post["order"])? $post["order"]: false;
			$columns = array("code", "description", "head", "id", "is_archived");
			$dir = "DESC";
			$order = "id";
			if($orderx){
				$dir = $orderx[0]["dir"];
				$order = $columns[$orderx[0]["column"]];
			}
				
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$dtTemp = $this->dt_model->dataTable();
			$dtTemp->setTable($this->departmentTable);
			$dtTemp->setParameterFields($columns);
			
			$parameters = array();
			$parameters["is_archived"] = (isset($post['is_archived']) && $post['is_archived'] == 1) ? 1 : 0;
			
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
					$nestedData = array();
					$nestedData['id'] = $pst->id;
					$nestedData['code'] = $pst->code;
					$nestedData['description'] = $pst->description;
					$nestedData['head'] = $pst->head;
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
    
    function getDepartmentModalContent($content="add"){
		$resultset = array();
		$html = "";
		$arrData = array();

        $employeeList = $this->adm_employee->getCurrentEmployees();
        
		if($content == "add"){
			$html = $this->load->view("hris/masterfile/department/modals/add_content", array("employee_list"=>$employeeList), true);
		}
		if($content == "edit"){
			$post = $this->input->post();
			if(isset($post) && $post){
				unset($post["csrf_token"]);
				$tempCompany = $this->db->get_where($this->departmentTable, $post);
				if($tempCompany->num_rows() == 1){
					$arrData = $tempCompany->row();
				}
			}
			$html = $this->load->view("hris/masterfile/department/modals/edit_content", array("data"=>$arrData, "employee_list"=>$employeeList), true);
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
    
    function setModalDepartment(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			unset($post["csrf_token"]);
            $tempHeadName = "No Assigned Name";
            $employee = $this->db->get_where($this->employeeTable, array("id"=>$post["head_id"]));
            if($employee->num_rows() == 1){
                $row = $employee->row_array();
                $fullname = $this->core_layout->getDisplayName($row);
                $tempFullname = (object) $fullname;
                $tempHeadName = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
            }

            $post["head"] = $tempHeadName;
            $post["add_date"] = date("Y-m-d H:i:s");
            $post["add_by"] = $session["emp_id"];
			if(isset($post['require_clearance']) && $post['require_clearance']  == 'on'){
				$post['require_clearance'] = 1;
			}
			$allow = $this->checkDepartmentCode($post);
			if($allow){
				$insert = $this->db->insert($this->departmentTable, $post);
				if($insert){
					$resultset["response"] = true;
					$resultset["toastr_msg"] = "Department data has been added.";
					$this->core_layout->setEventLog("User added new department: <strong>".$post['description']."</strong>","insert", "success", "gcchris", "user");
				}else{
					$resultset["response"] = false;
					$resultset["toastr_msg"] = "Failed saving department data!";
					$this->core_layout->setEventLog("User failed inserting new department: <strong>".$post['description']."</strong>","insert", "error", "gcchris", "system");
				}
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Department code already exist!";
				$this->core_layout->setEventLog("User failed inserting existing department: <strong>".$post['code']."</strong>","insert", "error", "gcchris", "system");
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Department masterfile - Error, No post data found.","insert", "error", "gcchris", "system");
		}
        
        return $resultset;
    }
	
	function updateModalDepartment(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			$id = $post["id"];
			unset($post["csrf_token"], $post["id"]);

			$tempHeadName = "No Assigned Name";
            $employee = $this->db->get_where($this->employeeTable, array("id"=>$post["head_id"]));
            if($employee->num_rows() == 1){
                $row = $employee->row_array();
                $fullname = $this->core_layout->getDisplayName($row);
                $tempFullname = (object) $fullname;
                $tempHeadName = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
            }

            $post["head"] = $tempHeadName;
			$post["update_date"] = date("Y-m-d H:i:s");
            $post["update_by"] = $session["emp_id"];
			$currentDeptData = $this->getDepartmentData($id);
			if(isset($post['require_clearance']) && $post['require_clearance']  == 'on'){
				$post['require_clearance'] = 1;
			}else{
				$post['require_clearance'] = 0;
			}
			$update = $this->db->update($this->departmentTable, $post, array("id"=>$id));
			if($update){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Department data has been updated.";
				unset($post['update_date']); 
                unset($post['update_by']);
				$changes = $this->logChanges($currentDeptData,$post);
				$resultset['changes'] = $changes;
				$this->core_layout->setEventLog("User updated department: <strong>".$post['description']."</strong> ".$changes,"update", "success", "gcchris", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed updating department data!";
				$this->core_layout->setEventLog("User failed updating department: <strong>".$post['description']."</strong>","update", "error", "gcchris", "system");
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Department masterfile - Error, No post data found.","update", "error", "gcchris", "system");
		}
        
        return $resultset;
	}

	function removeCurrentDepartment(){
		$resultset = array();
		$post = $this->input->post();
		$currentDeptData = $this->getDepartmentData($post["id"]);
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$updated = $this->db->update($this->departmentTable, array("is_archived"=>1), $post);
			if($updated){
				$session = $this->core_layout->getCurrentSession();
				$data = array(
					"archived_table"=>$this->departmentTable,
					"archived_id"=>$post["id"],
					"archived_by"=>$session["emp_id"]
				);

				$this->db->insert($this->archivedTable, $data);
					
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Department has been removed.";
				$this->core_layout->setEventLog("User has archived department: <strong>".$currentDeptData->description."</strong>","archive", "success", "gcchris", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to remove department!";
				$this->core_layout->setEventLog("User has failed archiving department: <strong>".$currentDeptData->description."</strong>","archive", "error", "gcchris", "system");
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Department masterfile - Error, No post data found.","archive", "error", "gcchris", "system");
		}

		return $resultset;
	}

    private function checkDepartmentCode($data=array()){
		if($data){
			$query = $this->db->get_where($this->departmentTable, array("code"=>$data["code"]));
			return $query->num_rows() == 0 ? true : false;
		}else{ return false; }
	}

	public function getDepartmentSelect2Data(){
		$resultset = array();
		$arrData = array();

		$get = $this->input->get();
		$this->db->select("id, description as text");
		$this->db->from($this->departmentTable);
		$this->db->where("is_archived", 0);
		if(isset($get["term"]) && $get["term"]){ $this->db->like("description", $get["term"], "both"); }
		$query = $this->db->get();

		if($query->num_rows() > 0){
			$arrData = $query->result();
		}
		
		$resultset["results"] = $arrData;
		return $resultset;
	}

	public function getDepartment(){
		$get = $this->input->get();
		$sql = "id, CASE WHEN code = description THEN description ELSE CONCAT(code, ' | ', description) END as text";
		$filterFields = array("code", "description");
		$this->db->select($sql);
		$this->db->from($this->departmentTable);

		if($get && isset($get['term'])){
			foreach($filterFields as $key => $field){
                if($key == 0){
					$this->db->like($field, $get['term'], "both");
				}else{
					$this->db->or_like($field, $get['term'], "both");
				}
            }
		}

		$this->db->where('is_archived', 0);
		$this->db->group_by('head_id');
		$this->db->order_by('code', 'asc');
		$query = $this->db->get();
		return  array(
			"results" => $query->result()
		);
	}

	public function select2DepartmentData(){
        $this->db->select("departments.id, UPPER(CONCAT(departments.`code`,' | ', departments.`description`)) `text`, departments.*");
        $this->db->order_by("`code`", "ASC");
        $results = $this->db->get("gcchris.tbldepartments departments")->result();
        return $results;
    }

	function restoreCurrentDepartment(){
		$resultset = array();
		$post = $this->input->post();
		$currentDeptData = $this->getDepartmentData($post["id"]);
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$updated = $this->db->update($this->departmentTable, array("is_archived"=>0), $post);
			if($updated){
				// $session = $this->core_layout->getCurrentSession();
				// $data = array(
				// 	"archived_table"=>$this->departmentTable,
				// 	"archived_id"=>$post["id"],
				// 	"archived_by"=>$session["emp_id"]
				// );

				// $this->db->insert($this->archivedTable, $data);
					
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Department has been restored.";
				$this->core_layout->setEventLog("User has restored department: <strong>".$currentDeptData->description."</strong>","restore", "success", "gcchris", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to restore department!";
				$this->core_layout->setEventLog("User has failed restoring department: <strong>".$currentDeptData->description."</strong>","restore", "error", "gcchris", "user");
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Department masterfile - Error, No post data found.","restore", "error", "gcchris", "user");
		}

		return $resultset;
	}

	private function getDepartmentData($id) {
		$this->db->select("*");
		$this->db->from($this->departmentTable);
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