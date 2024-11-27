<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Tms extends MY_Controller {
	function __construct(){
    parent::__construct();
    $this->authenticate->setModuleAccess("tms");
    $this->authenticate->doRedirect();

    $this->core_layout->setBodyClass("tms");
		$this->core_layout->setBodyClass("tms");
		$this->core_layout->setPrivilegeName("tms");
  }

  function index(){
    $arrData = array();
    $this->core_layout->setPagetitle("TMS - Dashboard");
    
    $this->load->view('core/templates/header');
		$this->load->view('tms/index', $arrData);
		$this->load->view('core/templates/footer');
  }
}