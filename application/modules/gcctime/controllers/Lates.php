<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lates extends MY_Controller {

	public function __construct()
	{
        parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->load->model("late_model");
		$this->authenticate->doRedirect();
		$this->core_layout->setPrivilegeName("gcctime_lates");
		$this->load->model("Crudv2_model","crud2");
	}

	public function index()
	{
		$this->load->view('core/templates/header');
		$this->load->view('lates/index');
		$this->load->view('core/templates/footer');
	}

	public function getLate()
	{
		$post = $this->input->post();
		$query =  $this->crud->load($post,"gcctimeutility.lates");

		echo json_encode($query);
	}
	public function getcustomLate()
	{
		$post = $this->input->post();
		$query =  $this->crud->load($post,"gcctimeutility.custom_personnel_shift");

		echo json_encode($query);
	}

	public function getCollection(){
		$actions = $this->core_layout->getCurrentActions();
		$resultarray = array();
		$query = $this->crud->getCollection(array(),"gcctimeutility.lates");

		if($query){
			foreach($query as $_query)
			{
				$data = array();

				$data[] = $_query["name"];
				$data[] = $_query["am_start"];
				$data[] = $_query["am_end"];
				$data[] = $_query["pm_start"];
				$data[] = $_query["pm_end"];
				
				$active = '<span class="btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm"><i class="la la-check"></i></span>';
				$inactive = '<span class="btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm"><i class="la la-close"></i></span>';
				$data[] = (isset($_query["status"]) && $_query["status"] == 1)? $active: $inactive;
				
				$_actions = "";
				if(in_array('edit', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='edit_late(".$_query["id"].")' title='Edit'><i class='la la-edit'></i></a>";					
				}
				if(in_array('delete', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btn-delete' data-id='".$_query["id"]."' title='Delete'><i class='la la-trash-o'></i></a>";					
				}
				if(in_array('assign', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btn-dept' data-id='".$_query["id"]."' title='Select Departments'><i class='la la-users'></i></a>";
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill' onclick='schedule_list(".$_query["id"].")' title='Assigned List'><i class='la la-list-ul'></i></a>";					
				}
				
				$data[] = $_actions;
				$resultarray[] = $data;
			}
		}

		echo json_encode(array("data"=>$resultarray));
	}

	public function selectDeptforLate()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("shift_id"=>$post["late_id"]),array("id"=>$post["dept_id"]),"gcctimeutility.department");
		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function deselectDeptforLate()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("shift_id"=>0),array("id"=>$post["dept_id"]),"gcctimeutility.department");
		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function selectLate()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("shift_id"=>$post["shift_id"]),array("id"=>$post["id"]),"gcctimeutility.department");

		if($query){
			$resultarray["status"] = $post["id"];
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function deselectLate()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("shift_id"=>0),array("id"=>$post["id"]),"gcctimeutility.department");

		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function newLate()
	{
		$post = $this->input->post();
		$resultarray = array();

		$query = $this->crud->insert($post,"gcctimeutility.lates");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully saved!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);

	}

	public function updatelate()
	{
		$post = $this->input->post();
		$id = $post["id"];
		unset($post["id"]);
		$resultarray = array();

		$query = $this->crud->update($post,array("id"=>$id),"gcctimeutility.lates");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully updated!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}

	public function deleteLate()
	{
		$post = $this->input->post();

		$query = $this->crud2->delete($post,"gcctimeutility.lates");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully deleted!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}
	public function update_custom_lates()
	{
		$post = $this->input->post();
		$id = $post["id"];
		unset($post["id"]);
		$resultarray = array();

		$query = $this->crud->update($post,array("id"=>$id),"gcctimeutility.custom_personnel_shift");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully updated!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}
	public function get_custom_collection(){
		$data = $this->late_model->getCustomCollection();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	public function get_personnel_items(){
		$data = $this->late_model->getPersonnelItems();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	public function add_custom_lates(){
		$data = $this->late_model->addCustomlates();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
}