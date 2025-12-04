<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Module extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->authenticate->setModuleAccess("users");
		$this->authenticate->doRedirect();
		$this->load->model("core/module_model");
		$this->core_layout->setPrivilegeName("manage_modules");
	}
	function index(){
		$this->render_view("configuration/modules");
	}

	function add_module(){
		$resultset = array();
		$resultset = $this->module_model->addModule();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}
	
	function get_current_module(){
		$resultset = array();
		$resultset = $this->module_model->getCurrentModule();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}

	function update_module(){
		$resultset = array();
		$resultset = $this->module_model->updateModule();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}

	function remove_module(){
		$resultset = array();
		$resultset = $this->module_model->removeModule();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}

	function get_module_list(){
		$resultset = array();
		$resultset = $this->module_model->getModuleList();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}

	function get_moduletree_list(){
		$resultset = array();
		$resultset = $this->module_model->getModuleTreeList();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}

	function get_json_module(){
		$resultset = array();
		$resultset = $this->module_model->getJsonModule();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}

	function get_assigned_acl_module(){
		$resultset = array();
		$resultset = $this->module_model->getAssignedAclModule();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}
	
	function set_module_json_actions(){
		$resultset = array();
		$resultset = $this->module_model->setModuleJsonActions();
		
		$this->output
		->set_content_type('json')
		->set_output(json_encode($resultset));
	}
	
	function get_module_tree_root(){
		$resultset = array();
		$resultset = $this->module_model->getModuleTreeRoot();
		
		$this->output
		->set_content_type('json')
		->set_output(json_encode($resultset));
	}

	public function set_allowed_ip(){
		$resultset = array();
		$resultset = $this->module_model->setAllowedIp();
		
		$this->output
		->set_content_type('json')
		->set_output(json_encode($resultset));
	}


}