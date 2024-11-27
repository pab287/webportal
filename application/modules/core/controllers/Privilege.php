<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Privilege extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->authenticate->setModuleAccess("users");
		$this->authenticate->doRedirect();
		$this->load->model("privilege_model", "privilege");
		$this->core_layout->setPageTitle("Privilege");
		$this->core_layout->setBodyClass("configuration");
		$this->core_layout->setPrivilegeName("manage_privilege");
	}
	public function index(){
		$this->load->view('core/templates/header');
		$this->load->view('core/privilege/index');
		$this->load->view('core/templates/footer');
	}
	function add_privilege(){
		$data = $this->privilege->addPrivilege();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function update_privilege(){
		$data = $this->privilege->updatePrivilege();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function remove_privilege(){
		$data = $this->privilege->removePrivilege();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_privilege_data(){
		$data = $this->privilege->getPrivilegeUpdate();
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_privilege_list(){
		$data = $this->privilege->getPrivilegeList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
}
