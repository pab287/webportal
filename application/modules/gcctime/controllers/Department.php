<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Department extends MY_Controller {
	public function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->load->model('Attendance_model',"attendance");
		$this->authenticate->doRedirect();
		$this->core_layout->setPrivilegeName("gcctime_department");
	}

	public function getLoggedinUser(){
		$userdata = $this->core_layout->getUserId();

		return $userdata;
	}

	public function index()
	{
		$this->load->view('core/templates/header');
		$this->load->view('departments/index');
		$this->load->view('core/templates/footer');
	}

	public function getCollection()
	{
		$resultarray = array();
		$this->db->select('*');
		$this->db->from('gcctimeutility.department');
		$query = $this->db->get();

		if($query->num_rows() > 0){
			foreach($query->result_array() as $_query){
				$data = array();
				$getDepartmentHead = $this->crud->load(array("id"=>$_query["head_id"]),"gcctimeutility.personnel");
				if($getDepartmentHead){
					$departmentHead = $getDepartmentHead["name"];
				}else{
					$departmentHead = "N/A";
				}

				$data[] = $_query["description"];
				$data[] = $departmentHead;
				$data[] = "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='edit_dept(".$_query['id'].")' title='Edit'><i class='la la-edit'></i></a><a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnAssign btn-late-select-person' data-id='".$_query["id"]."' title='Assign Persons'><i class='la la-users'></i></a><a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill ' onclick='schedule_list(".$_query["id"].")' title='Assigned List'><i class='la la-list'></i></a>";

				$resultarray[] = $data;
			}
		}

		echo json_encode(array("data"=>$resultarray));

	}

	public function getDepartment()
	{
		$post = $this->input->post();
		$query =  $this->crud->load($post,"gcctimeutility.department");

		echo json_encode($query);
	}

	public function processNew(){
		$post = $this->input->post();
		$resultarray = array();
		$userdata = $this->core_layout->getUserId();
		$post["add_by"] = $userdata;

		$query = $this->crud->insert($post,"gcctimeutility.department");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully saved!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}

	public function processEdit()
	{
		$post = $this->input->post();
		$resultarray = array();
		$userdata = $this->core_layout->getUserId();

		$query = $this->crud->update(array("description"=>$post["description"],"head_id"=>$post["head_id"],"update_by"=>$userdata),array("id"=>$post["id"]),"gcctimeutility.department");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully updated!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}

	public function getDepartmentMin()
	{
		$actions = $this->core_layout->getCurrentActions();
		$post = $this->input->post();
		$resultset = array();
		$resultarray = array();
		
		$search = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
		$order = (isset($post["order"][0]["column"]) && $post["order"][0]["column"])? $post["order"][0]["column"]: "name";
		$dir = (isset($post["order"][0]["dir"]) && $post["order"][0]["dir"])? $post["order"][0]["dir"]: "ASC";
		
		$this->db->from("gcctimeutility.department");
		$this->db->like("description", $search);
		$this->db->order_by($order, $dir);
		$query = $this->db->get();
		$getDepartmentCollection = $query->result_array();
		
		if($getDepartmentCollection){
			foreach($getDepartmentCollection as $_query)
			{
				$data = array();
				switch ($_query["shift_id"]) {
					case 0:
						if(in_array('select', $actions)){
							$btn = '<button type="button" class="btn btn-success btn-select-shift-trigg" onclick="select_dep('."'".$_query["id"]."','".$_query["shift_id"]."'".')"><i class="la la-plus-circle"></i> Select</button>';							
						}else{
							$btn = '---';
						}
					break;
					default:
						if(in_array('deselect', $actions)){
							$btn = '<button type="button" class="btn btn-danger btn-deselect-shift-trigg" onclick="deselect_dep('.$_query["id"].')"><i class="la la-minus-circle"></i> Deselect</button>';							
						}else{
							$btn = '---';
						}
					break;
				}
					$data["chkbox"] = '<input type="checkbox" class="checkSingle" data-id="'.$_query["id"].'" name="'.$_query["id"].'">';
					$data["department_name"] = $_query["description"];
					$data["action"] = $btn;

				$resultarray[] = $data;
			}
		}
		echo json_encode(array("data"=>$resultarray));
	}
	public function getDepartmentAssigned()
	{
		$actions = $this->core_layout->getCurrentActions();
		$post = $this->input->post();
		$resultset = array();
		$resultarray = array();
		
		$search = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
		$order = (isset($post["order"][0]["column"]) && $post["order"][0]["column"])? $post["order"][0]["column"]: "description";
		$dir = (isset($post["order"][0]["dir"]) && $post["order"][0]["dir"])? $post["order"][0]["dir"]: "ASC";
		
		$this->db->from("gcctimeutility.department");
		$this->db->like("description", $search);
		$this->db->order_by($order, $dir);
		$query = $this->db->get();
		$getDepartmentCollection = $query->result_array();
		
		if($getDepartmentCollection){
			foreach($getDepartmentCollection as $_query)
			{
				$data = array();
				switch ($_query["shift_id"]) {
					case 0:
						
					break;
					default:
					$data["department_name"] = $_query["description"];
					$resultarray[] = $data;	
					break;
				}

			}
		}
		echo json_encode(array("data"=>$resultarray));
	}
	public function getDepartmentMinUT()
	{
		$this->core_layout->setPrivilegeName("manage_undertime");
		$actions = $this->core_layout->getCurrentActions();
		
		$resultarray = array();
		$query = $this->crud->getCollection(array(),"gcctimeutility.department");

		if($query){
			foreach($query as $_query)
			{
				$data = array();

				switch ($_query["undertime_id"]) {
					case 0:
						
							$btn = '<button type="button" class="btn btn-success btn-select-undertime-trigg" data-id="'.$_query["id"].'"><i class="la la-plus-circle"></i> Select</button>';							
						
					break;
					default:
						
							$btn = '<button type="button" class="btn btn-danger btn-deselect-undertime-trigg" data-id="'.$_query["id"].'"><i class="la la-minus-circle"></i> Deselect</button>';							
						
					break;
				}

				$data[] = '<input type="checkbox" class="checkSingle" data-id="'.$_query["id"].'" name="'.$_query["id"].'">';
				$data[] = $_query["description"];
				$data[] = $btn;

				$resultarray[] = $data;
			}
		}
		echo json_encode(array("data"=>$resultarray));
	}
	public function getAssignedMinUT()
	{
		$this->core_layout->setPrivilegeName("manage_undertime");
		$actions = $this->core_layout->getCurrentActions();
		
		$resultarray = array();
		$query = $this->crud->getCollection(array(),"gcctimeutility.department");

		if($query){
			foreach($query as $_query)
			{
				$data = array();

				switch ($_query["undertime_id"]) {
					case 0:
						
											
						
					break;
					default:
						
					$data[] = $_query["description"];
					$resultarray[] = $data;
					break;
				}

			
				
			}
		}
		echo json_encode(array("data"=>$resultarray));
	}
	
}