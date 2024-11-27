<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Department extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("eff/department_model");
		$this->core_layout->setPageTitle("Employee Feedback");
		$this->core_layout->setCrumbTitle("Employee Feedback");
	}
	
	function index(){
		$this->load->view('core/templates/header');
		$this->load->view('configuration/department/index');
		$this->load->view('core/templates/footer');
	}
	function get_department_list(){
		$data = $this->department_model->getDepartmentList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function add_department(){
		$data = $this->department_model->addDepartment();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function update_department(){
		$data = $this->department_model->updateDepartment();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function remove_department(){
		$data = $this->department_model->removeDepartment();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_department_data(){
		$data = $this->department_model->getDepartmentData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_department_items(){
		$data = $this->department_model->getCurrentDepartments();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
}