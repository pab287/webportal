<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Masterfile extends MY_Controller {
	public function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("ams");
    }
	
	public function index(){
        $this->core_layout->addJs("js/ams/index.js", true);

		$this->load->view('core/templates/header');
        $this->load->view('masterfile');
        $this->load->view('core/templates/footer');
    }
    
}