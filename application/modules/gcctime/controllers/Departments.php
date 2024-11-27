<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Departments extends MY_Controller {
	public function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->load->model('Attendance_model',"attendance");
		$this->authenticate->doRedirect();
		$this->core_layout->setPrivilegeName("gcctime_department");
	}

	public function index()
	{
		$this->load->view('core/templates/header');
		$this->load->view('departments/index');
		$this->load->view('core/templates/footer');
	}

	public function getCollection(){
		$actions = $this->core_layout->getCurrentActions();

		$resultarray = array();
		$this->db->select("a.*, b.name as late_shift, c.name as undertime_shift");
		$this->db->from("gcctimeutility.departments a");
		$this->db->join("gcctimeutility.lates b", "b.id = a.late_id", "left");
		$this->db->join("gcctimeutility.undertime c","c.id = a.undertime_id", "left");
		$query = $this->db->get();
		
		$departments = $query->result_array();

		if($departments){
			foreach($departments as $_query){
				$_actions = "";
				if(in_array('edit', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' onclick='edit_dept(".$_query["id"].")' title='Edit'><i class='la la-edit'></i></a>";					
				}
				if(in_array('delete', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDelete btn-delete' data-id='".$_query["id"]."' title='Delete'><i class='la la-trash-o'></i></a>";
				}
				if(in_array('assign', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnAssign btn-late-select-person' data-id='".$_query["id"]."' title='Assign Persons'><i class='la la-users'></i></a>";
					
				}
			
				$active = '<span class="btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm"><i class="la la-check"></i></span>';
				$inactive = '<span class="btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm"><i class="la la-close"></i></span>';
				$data = array();
				$data[] = $_query["name"];
				$data[] = (isset($_query["late_shift"]) && $_query["late_shift"])? $_query["late_shift"]: "---";
				$data[] = (isset($_query["undertime_shift"]) && $_query["undertime_shift"])? $_query["undertime_shift"]: "---";
				$data[] = (isset($_query["status"]) && $_query["status"] == 1)? $active: $inactive;
				$data[] = $_actions;
				
				$resultarray[] = $data;
			}
		}

		echo json_encode(array("data"=>$resultarray));
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
		
		$this->db->from("gcctimeutility.departments");
		$this->db->like("name", $search);
		$this->db->order_by($order, $dir);
		$query = $this->db->get();
		$getDepartmentCollection = $query->result_array();
		
		if($getDepartmentCollection){
			foreach($getDepartmentCollection as $_query)
			{
				$data = array();
				switch ($_query["late_id"]) {
					case 0:
						if(in_array('select', $actions)){
							$btn = '<button type="button" class="btn btn-success btn-select-late-trigg" data-id="'.$_query["id"].'"><i class="la la-plus-circle"></i> Select</button>';							
						}else{
							$btn = '---';
						}
					break;
					default:
						if(in_array('deselect', $actions)){
							$btn = '<button type="button" class="btn btn-danger btn-deselect-late-trigg" data-id="'.$_query["id"].'"><i class="la la-minus-circle"></i> Deselect</button>';							
						}else{
							$btn = '---';
						}
					break;
				}

				$data["chkbox"] = '<input type="checkbox" class="checkSingle" data-id="'.$_query["id"].'" name="'.$_query["id"].'">';
				$data["department_name"] = $_query["name"];
				$data["action"] = $btn;

				$resultarray[] = $data;
			}
		}
		echo json_encode(array("data"=>$resultarray));
	}

	public function getDepartmentMinUT()
	{
		$this->core_layout->setPrivilegeName("manage_undertime");
		$actions = $this->core_layout->getCurrentActions();
		
		$resultarray = array();
		$query = $this->crud->getCollection(array(),"gcctimeutility.departments");

		if($query){
			foreach($query as $_query)
			{
				$data = array();

				switch ($_query["undertime_id"]) {
					case 0:
						if(in_array('select', $actions)){
							$btn = '<button type="button" class="btn btn-success btn-select-undertime-trigg" data-id="'.$_query["id"].'"><i class="la la-plus-circle"></i> Select</button>';							
						}else{
							$btn = '---';
						}
					break;
					default:
						if(in_array('deselect', $actions)){
							$btn = '<button type="button" class="btn btn-danger btn-deselect-undertime-trigg" data-id="'.$_query["id"].'"><i class="la la-minus-circle"></i> Deselect</button>';							
						}else{
							$btn = '---';
						}
					break;
				}

				$data[] = '<input type="checkbox" class="checkSingle" data-id="'.$_query["id"].'" name="'.$_query["id"].'">';
				$data[] = $_query["name"];
				$data[] = $btn;

				$resultarray[] = $data;
			}
		}
		echo json_encode(array("data"=>$resultarray));
	}

	public function adddepartment()
	{
		$post = $this->input->post();
		$resultarray = array();

		$query = $this->crud->insert($post,"gcctimeutility.departments");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully saved!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);

	}

	public function updatedepartment()
	{
		$post = $this->input->post();
		$resultarray = array();

		$query = $this->crud->update(array("name"=>$post["name"]),array("id"=>$post["id"]),"gcctimeutility.departments");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully updated!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}

	public function getDepartment()
	{
		$post = $this->input->post();
		$query =  $this->crud->load($post,"gcctimeutility.departments");

		echo json_encode($query);
	}

	public function deleteDepartment()
	{
		$post = $this->input->post();

		$query = $this->crud->delete($post,"gcctimeutility.departments");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully deleted!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}

	public function selectPersonnelforDept()
	{
		$post = $this->input->post();

		$query = $this->crud->update(array("department_id"=>$post["dept_id"]),array("id"=>$post["personnel_id"]),"gcctimeutility.personnel");

		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function deselectPersonnelforDept()
	{
		$post = $this->input->post();

		$query = $this->crud->update(array("department_id"=>0),array("id"=>$post["personnel_id"]),"gcctimeutility.personnel");

		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}
}