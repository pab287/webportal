<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends MY_Controller {

	public function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->load->model("shift_management_model","sm");
		
		$this->load->library('email');
		//$this->load->library('session');
		$this->core_layout->setBodyClass("dashboard dashboard_notification");
		$this->core_layout->setPrivilegeName("gcctime_attendance_nte");
		
		date_default_timezone_set("Asia/Taipei");
	}

	public function index(){
		$this->core_layout->setPrivilegeName("gcctime_attendance_nte");
		$this->load->view('core/templates/header');
		$this->load->view('notification/index');
		$this->load->view('core/templates/footer');
	}

	public function print_nte(){
        $post = $this->input->post();
		$dateToday = date("F d, Y");
		$url = 'http://'.$_SERVER['HTTP_HOST'];
		$company_path_logo = "/web/assets/images/comp_logos";
		$url = $url.$company_path_logo;
		$getEmp = $this->crud->load(array("biometricno"=>$post["biometricno"]),"gccmaster.tblemployees");
		$query = $this->crud->load(array("biometricno"=>$post["biometricno"]),"gcctimeutility.personnel");

		if(strpos(strtoupper($getEmp['company_id']), 'WEGAPLAS') !== false || $getEmp['company_id'] == 9){
			$url = $url."/WEGA.png";
		}else if(strpos(strtoupper($getEmp['company_id']), 'PROXIMA') !== false || $getEmp['company_id'] == 10){
			$url = $url."/PROXIMA.png";
		}else if(strpos(strtoupper($getEmp['company_id']), 'GC&C') !== false || $getEmp['company_id'] == 1){
			$url = $url."/GCC.png";
		}else if(strpos(strtoupper($getEmp['company_id']), 'HOME') !== false || $getEmp['company_id'] == 4){
			$url = $url."/HW.png";
		}else{
			$url = $url."/GCC_inc.png";
		}
		/*switch($getEmp["company_id"]){
			case "WEGAPLAS":
				$url = $url."/WEGA.png";
			break;
			case "PROXIMA":
				$url = $url."/PROXIMA.png";
			break;
			case "GC&C":
				$url = $url."/GCC.png";
			break;
			case "HOMEWORLD": 
				$url = $url."/HW.png";
			break;
			default:
				$url = $url."/GCC_inc.png";
			break;
		}*/
		$company = $this->crud->load(array("code",$getEmp["company_id"]),"gcchris.tblcompanies");
		$resultarray["content"] = $this->load->view("notification/absent-nte-template",array("path_to_image"=>$url,"employee_name"=>$query["name"],"set_date"=>$dateToday,"effectivity_date"=>$dateToday,"company_name"=>$company["description"]),true);
		echo json_encode($resultarray); 
	}

	public function maptest(){
		$this->load->library('googlemaps');

		$config['map_height'] = '750px';
    	$config['map_width'] = '960px';
		$config['center'] = '10.7033969, 122.9734826';
		$config['zoom'] = 'auto';
		$config['map_type'] = 'HYBRID';
		$this->googlemaps->initialize($config);

		$marker = array();
		$marker['position'] = '10.7033969, 122.9734826';
		$this->googlemaps->add_marker($marker);
		$data['map'] = $this->googlemaps->create_map();

		$this->load->view('gps/map', $data);
	}
	

}