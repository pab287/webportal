<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Attendance_approval extends MY_Controller {
	public function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->load->model('Attendance_model',"attendance");
		$this->core_layout->setPrivilegeName("gcctime_attendance_approval");
		// $this->core_layout->setBodyClass("attendance attendance_approval");
		// $this->core_layout->setPrivilegeName("attendance_approval");
	}

	public function index(){

		$this->load->view('core/templates/header');
		$this->load->view('attendance/approval/index');
		$this->load->view('core/templates/footer');
	}

	private function getUserdata(){
		$userdata = $this->session->userdata("logged_in");
		return isset($userdata) ? $userdata : FALSE;
	}

	public function getData(){
		$collection = $this->attendance->getApprovalAttendance();
		$resultarray = array();

		if($collection){
			foreach($collection as $_collection){
				$data = array();

				$data[] = $_collection['biometric_id'];

				$currentDateTime = $_collection["datetime"];
				if($currentDateTime){
					$splitDateTime = explode(" ", $currentDateTime);
					if(count($splitDateTime) == 2){
						$cDate = $splitDateTime[0];
						$cTime = $splitDateTime[1];
					}
				}
				switch ($_collection['status']) {
					case 1:
						$status = "Pending";
					break;
					case 0:
						$status = "Disapproved";
					break;
					case 2:
						$status = "Approved";
					break;
					default:
						$status = "";
						break;
				}

				if($_collection["status"] == 1){
					$btn = "<button class='btn btn-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air btnprocess btn-sm' title='Remove' data-trigg='disapprove' data-id='".$_collection['id']."'><i class='fa fa-remove'></i></button>  <button class='btn btn-accent m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air btnprocess btn-sm' title='Approve' data-trigg='approve' data-id='".$_collection['id']."'><i class='fa fa-check'></i></button>";
				}else{
					$btn = "--";
				}
				
				$data[] = $cDate;
				$data[] = $cTime;
				$data[] = $_collection['name'];
				$data[] = $status;
				$data[] = $_collection['remarks'];
				$data[] = $btn;

				$resultarray[] = $data;

			}
		}
		echo json_encode(array("data"=>$resultarray)); 
	}

	public function process(){
		$post = $this->input->post();
		$resultarray = array();
		$userdata = $this->getUserdata();
		
		$query = $this->db->query("SELECT * FROM gcctimeutility.attendance_temp WHERE id = {$post['id']}");
		$getAttendanceTemp = $query->row_array();
		if(isset($post["trigg"])){
			if($post["trigg"] == "approve"){
				//get attendance temp data
				
				//insert attendance data
				$insertAttendanceData = $this->db->query("INSERT INTO gcctimeutility.attendance (`biometric_id`,`state`,`device_id`,`datetime`,`is_custom`,`temp_id`,`approved_by`,`approval_status`) VALUES ('{$getAttendanceTemp['biometric_id']}',1,0,'{$getAttendanceTemp['datetime']}',1,'{$getAttendanceTemp['id']}','{$userdata["id"]}',2)");

				//update attendance temp
				$updateAttendanceTemp = $this->db->query("UPDATE gcctimeutility.attendance_temp SET status = 2, updated_by = {$userdata["id"]} WHERE id = {$getAttendanceTemp['id']}");

				if($updateAttendanceTemp){ 
					$resultarray["status"] = TRUE;
					$resultarray["msg"] = "Data successfully approved.";
				}
			}else{

				//update attendance temp
				$updateAttendanceTemp = $this->db->query("UPDATE gcctimeutility.attendance_temp SET status = 0, updated_by = {$userdata["id"]} WHERE id = {$getAttendanceTemp['id']}");

				//insert disapproved remarks
				$insertDisapprovedRemarks = $this->db->query("INSERT INTO gcctimeutility.attendance_remarks (`attendance_temp_id`,`remarks`,`status`) VALUES ({$post['id']},'{$post['remarks']}',0)");


				if($insertDisapprovedRemarks){
					$resultarray["status"] = TRUE;
					$resultarray["msg"] = "Data successfully approved.";
				}
			}
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["msg"] = "trigg is not set.";
		}

		echo json_encode($resultarray);
	}

	public function basdak(){
		$collection = $this->attendance->getApprovalAttendance();

		var_dump($collection->result_array());
	}

	private function insert_remarks(){
		$insert_remarks = $this->crud->insert(array("attendance_id"=>$attendance_id,"remarks"=>$post["remarks"],"type"=>"add"),"gcctimeutility.attendance_remarks");
	}

}