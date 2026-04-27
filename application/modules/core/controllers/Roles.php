<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Roles extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->authenticate->setModuleAccess("users");
		$this->authenticate->doRedirect();
		$this->load->model("user_role_model", "user_role");
		$this->core_layout->setBodyClass("configuration");
		$this->core_layout->setPrivilegeName("manage_roles");
		
		$this->core_layout->addJs("js/jstree/jstree.min.js");
		$this->core_layout->addCss("css/jstree/themes/default-dark/style.min.css");
	}

	public function index(){
		$this->load->view('core/templates/header');
		$this->load->view('roles/index');
		$this->load->view('core/templates/footer');
	}

	function add_role(){
		$resultset = array();
		$resultset = $this->user_role->addUserRole();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}
	
	function update_role(){
		$resultset = array();
		$resultset = $this->user_role->updateUserRole();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}
	
	function remove_role(){
		$resultset = array();
		$resultset = $this->user_role->removeUserRole();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}
	function get_roles_list(){
		$data = $this->user_role->getUserRoleList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_role_data(){
		$data = $this->user_role->getRoleUpdate();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_assigned_module_role(){
		$data = $this->user_role->getAssignedModuleRole();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	function set_json_module_role(){
		$data = $this->user_role->setAssignedModuleRole();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_assigned_role(){
		$data = $this->user_role->getAssignedRole();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_json_role(){
		$data = $this->user_role->setAssignedUserRole();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_role_privilege_data(){
		$data = $this->user_role->getRolePrivilegeData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_json_role_actions(){
		$data = $this->user_role->setAssignedUserRoleActions();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function test_roles($id=null){
		$data = $this->user_role->getAssignedUserRoleActionList($id);
		echo "<pre>";
		var_dump($data);
	}

	function clone_user_role(){
		$data = $this->user_role->cloneUserRole();
		$this->output
		->set_content_type('json')
		->set_output(json_encode($data));
	}
}