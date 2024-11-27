<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->authenticate->doRedirect();
		$this->core_layout->setBodyClass("dashboard");
		$this->core_layout->setPrivilegeName("dashboard");
		
		date_default_timezone_set('Asia/Manila');
	}
	public function index(){
		$arrData = array();
		
		$this->load->view('core/templates/header');
		$this->load->view('index', $arrData);
		$this->load->view('core/templates/footer');
	}
}