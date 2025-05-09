<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Mobile_accounts extends MY_Controller {
	private $devicesTable = "devices";
	private $attendanceTable = "attendance";
	private $personnelTable = "personnel";
	
	public function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->load->model('core/upload_model', 'adm_upload');
		$this->load->model("Crudv2_model","crud2");
		$this->load->library('email');
		
		
		$this->core_layout->setBodyClass("attendance attendance_masterfile");
		$this->core_layout->setPrivilegeName("attendance_masterfile");
		
		$this->core_layout->addJs("plugins/moment_js/moment.min.js");
		$this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
		$this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
		
		date_default_timezone_set("Asia/Taipei");
	}

	public function index(){
		$this->load->view('core/templates/header');
		$this->load->view("mobile_accounts/index");
		$this->load->view('core/templates/footer');
	}

	public function getaccount()
	{
		$resultarray = array();

		$query = $this->crud2->getCollection(array(),"gcctimeutility.app_users",array("id"=>"DESC"));

		if($query)
		{
			foreach($query as $_query)
			{
				$data = array();
				$data[] =$_query["biometric_no"];
				$data[] =mb_strtoupper($this->get_fullname($_query["emp_id"]));
				$data[] =$_query["device_name"];
				$data[] =$_query["device_id"];
				$data[] =$_query["user_imei"];
				$data[] =($_query["status"] == 1) ? "logged In" : "logged out";
				$resultarray[] = $data;
			}
		}
		echo json_encode(array("data"=>$resultarray));
	}

	public function ajax_edit($id)
	{
		$data = $this->crud2->get_by_id2($id);
		echo json_encode($data);
	}
	
	public function ajax_update()
	{
		
			$data = array(
			'min_long' => $this->input->post('min_long'),
			'min_lat' => $this->input->post('min_lat'),
			'max_long' => $this->input->post('max_long'),
			'max_lat' => $this->input->post('max_lat'));

			
			$this->crud2->update4(array('id' => $this->input->post('id')), $data);
			echo json_encode(array("status" => TRUE));
	}

	private function get_fullname($emp_id){
		// $this->db->select("firstname, lastname, suffix");
		$this->db->select("CONCAT(lastname, CASE WHEN suffix != 'N/A' AND suffix !='NONE' AND suffix !='' AND suffix IS NOT NULL THEN CONCAT(' ', suffix) ELSE ''  END, ', ', firstname, ' ', CASE WHEN middlename != 'N/A' AND middlename != 'NONE' AND middlename !='' AND middlename IS NOT NULL THEN CONCAT(SUBSTR(middlename, 1, 1), '.') ELSE '' END) employee_name");
		$this->db->where("id", $emp_id);
		$data = $this->db->get("gccmaster.tblemployees");
		$name = $data->row_array();

		return isset($name['employee_name']) && $name['employee_name'] ? $name['employee_name'] : 'No Employee Name';
		// if($name['suffix'] != ""){
		// 	return $name['firstname'] . " " . $name['lastname'] . " " . $name['suffix'];
		// }else{
		// 	return $name['firstname'] . " " . $name['lastname'];
		// }
	}

}
