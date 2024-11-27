<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Profile extends MY_Controller {
	function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->core_layout->setBodyClass("dashboard");
		$this->load->model("shift_management_model","shift_management");
	}
	function index(){
		$arrData = array();
		$userProfile = $this->authenticate->getUserProfile();
		if($userProfile){
			$arrData["userProfile"] = $userProfile;
		}
		if($this->session->userdata('logged_in')){
			$this->load->view('core/templates/header');
			$this->load->view('dashboard/profile', $arrData);
			$this->load->view('core/templates/footer');					
		}else{
			redirect('login_controller', 'refresh');
		}
	}
	
	function get_attendance_collection($param=null){
        $data = array();
		$session_data = $this->session->userdata('logged_in');

		$emp_id = $session_data['emp_id'];
        $this->db->select("a.datetime, a.device_id, a.biometric_id");
        $this->db->from("gcctimeutility.attendance a");
        $this->db->join('gccmaster.tblemployees b', 'b.biometricno = a.biometric_id',"left");
        $this->db->where("b.id",$emp_id);
        $query = $this->db->get();

		if($query->num_rows() > 0){
			foreach($query->result_array() as $_query){
				$i = explode(" ",$_query["datetime"]);
				$attendanceDate = $_query["datetime"];
					$biometric_id = $_query["biometric_id"];
					$device_id = $_query["device_id"];
			  
					$date = $i[0];
					$time = $i[1];
					$device = ($_query["device_id"] == 2)?"Biometric Device":"";
					$row = array();
					$row[] = $date;
					$row[] = $time;
					
				
				if($param == "late"){
					$result = $this->shift_management->getCurrentLateByBiometricNo($biometric_id, $device_id, $attendanceDate); 
					if(isset($result["response"]) && $result["response"] == true){ 
						
						$ampm = date("A",strtotime($attendanceDate));
						$ampm = strtolower($ampm);
						$_data= $result[$ampm][$biometric_id];
						$row[] = $_data["minlate"];
						$row[] = $device;
						$data[] = $row; 
					}
				}else{
					
					$row[] = $device;
					$data[] = $row;
				}
			}
		}
		echo json_encode(array("data"=>$data));         
    }
	
	function get_attendance_collection2(){
        $data = array();
        $post = $this->input->post();
		$session_data = $this->session->userdata('logged_in');
		$emp_id = (isset($session_data['emp_id']) && $session_data['emp_id'])? $session_data['emp_id']: 0;
        $daterange = (isset($post["daterange"]) && $post["daterange"])? $post["daterange"]: "";
       
        
			$o = explode("-",$daterange);
			$date1 = $o[0];
			$date2 = $o[1];
            
			$start = date("Y-m-d h:i:s", strtotime($date1));
			$end = date("Y-m-d h:i:s", strtotime($date2));

        
			$session_data = $this->session->userdata('logged_in');
			$emp_id = $session_data['emp_id'];
			$this->db->select("a.datetime, a.device_id, a.biometric_id");
			$this->db->from("gcctimeutility.attendance a");
			$this->db->join('gccmaster.tblemployees b', 'b.biometricno = a.biometric_id',"left");
			$this->db->where("b.id",$emp_id );
			$this->db->where("datetime >=", $start);
			$this->db->where("datetime <=", $end);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				foreach($query->result_array() as $_query){
						$i = explode(" ",$_query["datetime"]);
						$attendanceDate = $_query["datetime"];
                        $biometric_id = $_query["biometric_id"];
                        $device_id = $_query["device_id"];
                        $date = $i[0];
						$time = $i[1];
						$device = ($_query["device_id"] == 2)?"Biometric Device":"";
						$row = array();
						$row[] = $date;
						$row[] = $time;
                         $row[] = $device;
                    $data[] = $row;
                    
                   
					}
				}   
		echo json_encode(array("data"=>$data));  
    }
	
	function get_late_collection(){
        $data = array();
        $post = $this->input->post();
		$session_data = $this->session->userdata('logged_in');
		$emp_id = (isset($session_data['emp_id']) && $session_data['emp_id'])? $session_data['emp_id']: 0;
        $daterange = (isset($post["daterange"]) && $post["daterange"])? $post["daterange"]: "";
       
        
			$o = explode("-",$daterange);
			$date1 = $o[0];
			$date2 = $o[1];
            
			$start = date("Y-m-d h:i:s", strtotime($date1));
			$end = date("Y-m-d h:i:s", strtotime($date2));

        
			$session_data = $this->session->userdata('logged_in');
			$emp_id = $session_data['emp_id'];
			$this->db->select("a.datetime, a.device_id, a.biometric_id");
			$this->db->from("gcctimeutility.attendance a");
			$this->db->join('gccmaster.tblemployees b', 'b.biometricno = a.biometric_id',"left");
			$this->db->where("b.id",$emp_id );
			$this->db->where("datetime >=", $start);
			$this->db->where("datetime <=", $end);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				foreach($query->result_array() as $_query){
						$i = explode(" ",$_query["datetime"]);
						$attendanceDate = $_query["datetime"];
                        $biometric_id = $_query["biometric_id"];
                        $device_id = $_query["device_id"];
                        $date = $i[0];
						$time = $i[1];
						$device = ($_query["device_id"] == 2)?"Biometric Device":"";
						$row = array();
						$row[] = $date;
						$row[] = $time;
                         $result = $this->shift_management->getCurrentLateByBiometricNo($biometric_id, $device_id, $attendanceDate); 
                        if(isset($result["response"]) && $result["response"] == true){ 
                            $ampm = date("A",strtotime($attendanceDate));
                            $ampm = strtolower($ampm);
                            $_data= $result[$ampm][$biometric_id];
                            $row[] = $_data["minlate"];
                            $row[] = $device;
                            $data[] = $row; 
                        }
                  
					}
				}   
		echo json_encode(array("data"=>$data));  
    }
}