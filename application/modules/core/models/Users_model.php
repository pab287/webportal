<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Users_model extends CI_Model{
	protected $usersTable = "tblusers";
	protected $employeesTable = "tblemployees";
	protected $rolesTable = "user_role";
	function __construct(){
		parent::__construct();
		$this->load->model("access_control_model", "acl_model");
		$this->load->model("datatable_model","dt_model");
	}
	
	public function getUserData(){
		$post = $this->input->post();
		$resultset = array();
		
		if(isset($post["id"]) && $post["id"]){
			$resultset["post"] = $post;
			$select = "users.id, CONCAT(UPPER(TRIM(employees.firstname)), ' ',
            CASE WHEN UPPER(TRIM(employees.middlename)) != 'N/A' AND UPPER(TRIM(employees.middlename)) != 'NONE' AND
                    TRIM(employees.middlename) !='' AND employees.middlename IS NOT NULL
                THEN CONCAT(UPPER(SUBSTR(employees.middlename, 1, 1)), '.') ELSE ''
            END,' ', UPPER(TRIM(employees.lastname)),
            CASE WHEN UPPER(TRIM(employees.suffix)) != 'N/A' AND
                UPPER(TRIM(employees.suffix !='NONE')) AND employees.suffix !='' AND
                employees.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(employees.suffix))) ELSE ''
            END) as employee_name,
			employees.biometricno, employees.employee_status, users.role_id, users.is_important";
			$this->db->select($select);
			$this->db->from("{$this->usersTable} as users");
			$this->db->join("{$this->employeesTable} as employees", "employees.id=users.emp_id");
			
			$where = array();
			$where["users.id"] = $post["id"];
			$this->db->where($where);
			$query = $this->db->get();
			
			if($query->num_rows() > 0){
				$row = $query->row_array();
				
				$queryRoles = $this->db->get_where($this->rolesTable, array("status"=>1));
				$roles = ($queryRoles->num_rows() > 0)? $queryRoles->result_array() : array();
				
				$arrData = array();
				$arrData["row"] = $row;
				$arrData["roles"] = $roles;
				
				$html = $this->load->view("core/users/modal_content/user_content", $arrData, true);
				$resultset["response"] = true;
				$resultset["html"] = $html;
				$resultset["data"] = $row;
			}else{
				$resultset["toastr_msg"] = "error getting data";
				$resultset["response"] = false;
			}
		}else{
			$resultset["toastr_msg"] = "error no data";
			$resultset["response"] = false;
		}
		
		return $resultset;
	}
	
	function assignUserRole(){
		$post = $this->input->post();
		$resultset = array();
		if(isset($post["id"]) && $post["id"]){
			$where = array();
			$where["id"] = $post["id"];
			
			$data = array();
			$data["role_id"] = $post["role_id"];
			/*** $data["is_important"] = isset($post["is_important"]) && $post["is_important"] == 'on'? 1: 0; ***/
			$tempData = $this->core_layout->getUserData($post["id"]);
			$tempName = (object) $tempData;
			$tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No assigned name";
			
			$update = $this->db->update($this->usersTable, $data, $where);
			if($update){
				$resultset["response"] = true;
				$resultset["message"] = "User account role of `{$tempName}` has been updated.";
			}else{
				$resultset["response"] = false;
				$resultset["message"] = "Failed to update user account role of `{$tempName}`!";
			}
		}else{
			$resultset["response"] = false;
			$resultset["message"] = "Error, nothing to update!";
		}
		
		$tempStatus = ($resultset["response"])? "success": "error";
		$this->core_layout->logNotification($resultset["message"], $tempStatus, "users");
		return $resultset;
	}
	public function getCurrentUsersList(){
        $resultset = array();
        $post = $this->input->post();
		$tempLimit = -1;
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : $tempLimit;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
		
        $rowData = $this->currentUsersList($search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->currentUsersListCount($search);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
	}

	protected function currentUsersList($search, $limit, $offset, $sortBy, $sortOrder){
		$queryDB = $this->getCurrentUserListQuery($search);
		if ($limit != -1) { $queryDB->limit($limit, $offset); }
		$queryDB->group_by("user.emp_id");
		if (isset($sortOrder)) {
			$i = $sortOrder[0]['column'];
			$queryDB->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
		} else { $queryDB->order_by('user.id', 'DESC'); }
		$query = $queryDB->get();
		//var_dump($this->db->last_query());
		return $query->result_array();
	}

	protected function currentUsersListCount($search){
		$queryDB = $this->getCurrentUserListQuery($search);
		return $queryDB->get()->num_rows();
	}

	protected function getCurrentUserListQuery($search=null){
		$filterFields = array("user.username", "user.email", "emp.lastname", "emp.firstname", "emp.middlename","emp.biometricno", "role.description",
		"user.telegram_chat_id", "CONCAT(emp.firstname, ' ', emp.lastname)");

		$sqlSelect = "user.id, user.username, user.email, TRIM(UPPER(role.description)) as user_role, TRIM(emp.biometricno) as biometricno,
			CONCAT(UPPER(TRIM(emp.firstname)), ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(UPPER(SUBSTR(emp.middlename, 1, 1)), '.') ELSE ''
            END,' ', UPPER(TRIM(emp.lastname)),
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
            END) as employee_name, emp.employee_status, user.is_important, user.telegram_chat_id";
		$this->db->select($sqlSelect);
		$this->db->from($this->usersTable . " as user");
		$this->db->join($this->employeesTable . " as emp", "emp.id = user.emp_id", "INNER");
		$this->db->join($this->rolesTable . " as role", "role.id = user.role_id", "LEFT");
		$this->db->where("user.is_suspended", 0);
		if (isset($search)) {
			$this->db->group_start();
			foreach ($filterFields as $key => $field) {
				($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
			}
			$this->db->group_end();
		}
		return $this->db;
	}

	public function getUserList(){
		$post = $this->input->post();
		if($post){
			$columns = array("users.id", "employees.biometricno", "employees.lastname", "employees.firstname", "employees.middlename", "users.email", "employees.employee_status", "roles.description");
			
			$dir = $post["order"][0]["dir"];
			$order = $columns[$post["order"][0]["column"]];
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			/*** $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0; ***/
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$usersTable = $this->dt_model->dataTable();
			$usersTable->setTable($this->usersTable);
			$usersTable->setTableAlias("users");
			
			$usersTable->setParameterFields($columns);
			
			$joinTable = array();
			$joinTable["table"][$this->employeesTable] = "employees";
			$joinTable["table"][$this->rolesTable] = "roles";
			$joinTable["fields"][] = "employees.id=users.emp_id";
			$joinTable["fields"][] = "roles.id=users.role_id";
			$joinTable["field_loc"][] = "";
			$joinTable["field_loc"][] = "LEFT";
			
			$usersTable->setJoinTable($joinTable);
			$usersTable->setWhereInField("users.id");
			
			$parameters = array();
			$parameters["users.is_suspended"] = 0;
			
			$usersTable->setWhereParameters($parameters);
			$totalData = $usersTable->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			/*** no pagination infinite scroll ***/
			$limit = 0;
			/*** no pagination infinite scroll ***/
			
			if(empty($searchValue)){
				$posts = $usersTable->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $usersTable->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $usersTable->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
					$biometricNo = $pst->biometricno;
					$email = $pst->email;
					$nestedData['id'] = $pst->id;
					$nestedData['description'] = $pst->description;
					$nestedData['lastname'] = $pst->lastname;
					$nestedData['firstname'] = $pst->firstname;
					$nestedData['middlename'] = $pst->middlename;
					$nestedData['biometricno'] = $biometricNo;
					$nestedData['email'] = $email;
					$nestedData['employee_status'] = $pst->employee_status;
					$data[] = $nestedData;
				}
			}
			$json_data = array(
				"draw" => intval($draw),
				"recordsTotal" => intval($totalData),
				"recordsFiltered" => intval($totalFiltered),
				"data" => $data,
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
	function getUserList2(){
		$this->db->from("{$this->usersTable} as users");
		$this->db->join("{$this->employeesTable} as employee", "employee.id = users.emp_id");
		$this->db->where(array("users.is_suspended"=>0, "employee.employee_status"=>"Active"));
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return false;
		}
	}
	// borrowing prevelage notification
	public function borrow_notif(){
		$session = $this->session->userdata();
		$employee_id = $session["logged_in"]["emp_id"];
		$this->db->select("module_resource");
		$this->db->where("role_id", $employee_id);
		$data = $this->db->get("user_role_acl");
		$borrow_user = $data->row_array();
		if($borrow_user != null){
			if(in_array($employee_id, unserialize($borrow_user['module_resource']))){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function get_remittance_no(){
		echo "<pre>";
		$emp = $this->db->get($this->employeesTable);
		if($emp->num_rows() > 0){
			foreach ($emp->result() as $key => $value) {
				$temp = preg_replace("/[^0-9]/", "", $value->sss_no);
				$tempx = intval($temp);
				var_dump($tempx);
				var_dump($temp."~~~".$tempx);
			}
		}
	}
}