<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Undertime extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->core_layout->setPrivilegeName("gcctime_undertime");
		$this->load->model("Crudv2_model","crud2");
	}

	public function index()
	{
		$this->load->view('core/templates/header');
		$this->load->view('undertime/index');
		$this->load->view('core/templates/footer');
	}

	public function getUndertime()
	{
		$post = $this->input->post();
		$query =  $this->crud->load($post,"gcctimeutility.undertime");

		echo json_encode($query);
	}

	public function getCollection(){
		$actions = $this->core_layout->getCurrentActions();
		$resultarray = array();
		$query = $this->crud->getCollection(array(),"gcctimeutility.undertime");

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
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='edit_undertime(".$_query["id"].")' title='Edit'><i class='la la-edit'></i></a>";					
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

	public function newUndertime()
	{
		$post = $this->input->post();
		$resultarray = array();

		$query = $this->crud->insert($post,"gcctimeutility.undertime");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully saved!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}

	public function updateundertime()
	{
		$post = $this->input->post();
		$id = $post["id"];
		unset($post["id"]);
		$resultarray = array();
               
		$query = $this->crud->update($post,array("id"=>$id),"gcctimeutility.undertime");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully updated!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}

	public function selectDeptforUndertime()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("undertime_id"=>$post["undertime_id"]),array("id"=>$post["dept_id"]),"gcctimeutility.department");
		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function deselectDeptforUndertime()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("undertime_id"=>0),array("id"=>$post["dept_id"]),"gcctimeutility.department");
		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function deleteUndertime()
	{
		$post = $this->input->post();

		$query = $this->crud2->delete($post,"gcctimeutility.undertime");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully deleted!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}

}
