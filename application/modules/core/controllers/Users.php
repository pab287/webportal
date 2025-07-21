<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Users extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->authenticate->setModuleAccess("users");
		$this->authenticate->doRedirect();
		$this->load->model("users_model", "users");
		$this->load->model("datatable_model","dt_model");
		
		$this->core_layout->setPageTitle("Users");
		$this->core_layout->setBodyClass("configuration configure_users");
		$this->core_layout->setPrivilegeName("manage_users");
	}
	function index(){
		$this->load->view('core/templates/header');
		$this->load->view('core/users/index');
		$this->load->view('core/templates/footer');
	}
	
	function get_user_list(){
		$data = $this->users->getCurrentUsersList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_user_data(){
		$data = $this->users->getUserData();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function assign_user_role(){
		$data = $this->users->assignUserRole();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_user_list2(){
		$users = $this->users->getUserList2();
		echo "<pre>";
		/* var_dump($users); */
		
		$usersTable = $this->dt_model->dataTable();
		$usersTable->setTable("gccmaster.tblusers");
		$usersTable->setTableAs("users");
		$joinTable = array();
		$joinTable["table"]["gccmaster.tblemployees"] = "employees";
		$joinTable["fields"][] = "employees.id=users.emp_id";
		$usersTable->setJoinTable($joinTable);
		
		/* $joinedFields = array("users.id", "users.emp_id", "users.username", "users.email");
		$usersTable->setParameterFields($joinedFields); */
		$collection = $usersTable->getCollection();
		
		var_dump($collection);
		/* var_dump($usersTable->getJoinedTable()); */
	}

	function get_remittances_number(){
		$this->users->get_remittance_no();
	}
}