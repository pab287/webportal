<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'libraries/codeigniter-predis/src/Redis.php';

class Curl_request extends MY_Controller {
	private $today;
	private $redis;

	public function __construct(){
		parent::__construct();
		$this->load->model('Attendance_model',"attendance");
		$this->load->model('shift_management_model', "shift_manangement");
		$this->load->model('Biometric_model',"adm_biometric");
		$this->load->model('sms/Contacts_model', 'contacts');
		$this->load->library("email");
		
		date_default_timezone_set("Asia/Taipei");
		$this->today = date("Y-m-d");
		$this->telegramBotToken = "5980215549:AAFzR0QvoupYMO45Q8HAMXwgRElhmnFc9lQ";
		$this->telegramBotUrl = "https://api.telegram.org/bot{$this->telegramBotToken}/sendMessage";
		/*** $this->authenticate->doRedirect(); ***/

		$this->redis = new \CI_Predis\Redis(['serverName' => 'localhost']);
	}
	
	function getScheduledEvent($dateTime=null){
		$response = false;
		$dateStart = date("Y-m-1");
		$dateEnd = date("Y-m-31");
		$dateTime = ($dateTime)? date("Y-m-d H:i", strtotime($dateTime)): date("Y-m-d H:i");
		
		$dateToday = date("Y-m-d", strtotime($dateTime));
		$timeToday = date("H:i", strtotime($dateTime));
		
		$this->db->from("gcctimeutility.shift_schedule_calendar");
		$this->db->where("start_date >=", $dateStart);
		$this->db->where("end_date <=", $dateEnd);
		$query = $this->db->get();
		
		if($query->num_rows() > 0){
			foreach($query->result() as $rs){
				$startDate = ($rs->start_date && $rs->start_date !== "0000-00-00")? date("Y-m-d", strtotime($rs->start_date)): "";
				$endDate = ($rs->end_date && $rs->end_date !== "0000-00-00")? date("Y-m-d", strtotime($rs->end_date)): "";
				if(($startDate && $endDate) && ($dateToday >= $startDate && $dateToday <= $endDate)){
					$startTime = ($rs->start_time && $rs->start_time !== "00:00:00")? date("H:i", strtotime($rs->start_time)): "";
					$endTime = ($rs->end_time && $rs->end_time !== "00:00:00")? date("H:i", strtotime($rs->end_time)): "";
					
					if(($startTime && $endTime) && ($timeToday >= $startTime && $timeToday <= $endTime)){ $response = true; }
					if($rs->start_time == "00:00:00" && $rs->end_time == "00:00:00"){ $response = true; }
				}
			}
		}
		
		return $response;
	}
	
    function test_data(){
		echo "<pre>";
		$this->adm_biometric->getBiometricData2(true);
		
		/* $ipAddress = "192.168.7.15";
		$dd = $this->adm_biometric->ipIsReachable($ipAddress);
		var_dump($dd);
		echo "test"; */
    }
	
	public function syncdata($alldevice=false, $id=null){
		/*** $response = $this->attendance->doSyncData($alldevice); ***/
		$response = $this->adm_biometric->biometricSync($alldevice, $id);
		echo json_encode(array("res"=>$response));
	}
	
	function attendance_sync(){
		return true;
		/*** $response = $this->attendance->cronjob_inject_sync(); 
		var_dump($response); ***/
	}
	
	function attendance_download($alldevice=false){ 
		
		$_weekday = date("l", strtotime($this->today));
		$_weekday = strtolower($_weekday);
		
		$currentDate = date("Y-m-d H:i:s", strtotime($this->today));
		$responseEvent = $this->shift_manangement->getScheduledEvent($currentDate);
		
		if($_weekday !== "sunday" && $responseEvent == false){
			$response = $this->adm_biometric->biometricSync($alldevice);
			var_dump($response);
			if($response){
				$this->core_layout->logNotification("Biometric sync data successful", "success", "gcctimeV2");	
			}else{
				$this->core_layout->logNotification("Biometric sync data failed!", "error", "gcctimeV2");
			}
			
		}
		if($responseEvent == true){
			$this->core_layout->logNotification("No sync data, scheduled event is currently active.", "info", "gcctimeV2");				
		}
	}
	
	public function attendance_report($ampm="AM"){
		$_weekday = date("l", strtotime($this->today));
		$_weekday = strtolower($_weekday);
		$meredien = ($ampm)? $ampm: "AM";
		$currentDate = date("Y-m-d H:i:s", strtotime($this->today));
		$responseEvent = $this->shift_manangement->getScheduledEvent($currentDate);

		$tempResponseLogger = array();
		if($_weekday !== "sunday" && $responseEvent === false){
			$syncResponse = $this->getLastSyncRecord();

			if($syncResponse){
				$createdAt = date("Y-m-d H:i:00", strtotime($syncResponse->created_at));
				$amStart = date("Y-m-d 07:30:00", strtotime($this->today));
				$amEnd = date("Y-m-d 10:00:00", strtotime($this->today));
				
				$pmStart = date("Y-m-d 12:30:00", strtotime($this->today));
				$pmEnd = date("Y-m-d 13:31:00", strtotime($this->today));
				
				$tempStart = $meredien ? $amStart: $pmStart;
				$tempEnd = $meredien ? $amEnd: $pmEnd;

				$meredien = strtoupper($meredien);
				if(($createdAt >= $amStart && $createdAt <= $amEnd) && $meredien == "AM"){
					$responseAM = $this->generateMorningAbsenteeData();
					if($responseAM){
						$this->trigger_email_late($meredien);
						$this->trigger_email_absent($meredien);
					}
				}elseif(($createdAt >= $pmStart && $createdAt <= $pmEnd) && $meredien == "PM"){
					$responsePM = $this->generateAfternoonAbsenteeData();
					if($responsePM){
						$this->trigger_email_late($meredien);
						$this->trigger_email_absent($meredien);
					}
				}else{
					$this->core_layout->logNotification("{$meredien} - Sending of email reports failed, sync data is outdated!", "error", "gcctimeV2");
					$this->sendTelegramMessage("{$meredien} - Sending of email reports failed! [ Created At - {$createdAt} | Start Date - {$tempStart} | End Date - {$tempEnd} ]");
					$tempResponseLogger = $this->sendTelegramMessage("{$meredien} - Sending of email reports failed, sync data is outdated!");
				}
			}else{
				$this->core_layout->logNotification("No last sync record found!", "error", "gcctimeV2");
				$tempResponseLogger = $this->sendTelegramMessage("{$meredien} - No last sync record found!");
			}
		}
		
		if($responseEvent === true){
			$this->core_layout->logNotification("No email reporting for late and absent, scheduled event is currently active.", "info", "gcctimeV2");
		}
		if($tempResponseLogger["response"]){
			$this->core_layout->logNotification($tempResponseLogger["message"], "success", "gcctimeV2");
		}else{
			$this->core_layout->logNotification($tempResponseLogger["message"], "error", "gcctimeV2");
		}
	}
	
	function attendance_afternoon_shift($alldevice=false){
		$_weekday = date("l", strtotime($this->today));
		$_weekday = strtolower($_weekday);
		$meredien = "AM";
		
		if($_weekday !== "sunday"){
			$data = $this->getLastSync();
			
			if($data){
				$syncDate = date("Y-m-d H", strtotime($data->created_at));
				if($syncDate == date("Y-m-d H")){
					$this->generateAfternoonAbsenteeData();	
					$this->trigger_email_late($meredien);
					$this->trigger_email_absent($meredien);
					$this->core_layout->logNotification("Afternoon sync data and attendance log successful.", "success", "gcctimeV2");
				}else{
					$response = $this->attendance->doSyncData($alldevice);
					if($response){
						$this->generateAfternoonAbsenteeData();
						$this->trigger_email_late($meredien);
						$this->trigger_email_absent($meredien);
						$this->core_layout->logNotification("Afternoon sync data and attendance log successful.", "success", "gcctimeV2");
					}else{
						$this->core_layout->logNotification("Afternoon sync data and email sending failed!", "error", "gcctimeV2");
					}
				}
			}else{
				$response = $this->attendance->doSyncData($alldevice);
				if($response){
					$this->generateAfternoonAbsenteeData();
					$this->trigger_email_late($meredien);
					$this->trigger_email_absent($meredien);
					$this->core_layout->logNotification("Afternoon sync data and attendance log successful.", "success", "gcctimeV2");
				}else{
					$this->core_layout->logNotification("Afternoon sync data and email sending failed!", "error", "gcctimeV2");
				}
			}
		}
	}
	function attendance_morning_shift($alldevice=false){
		$_weekday = date("l", strtotime($this->today));
		$_weekday = strtolower($_weekday);
		$meredien = "AM";
		
		if($_weekday !== "sunday"){
			$data = $this->getLastSync();
			if($data){
				$syncDate = date("Y-m-d H", strtotime($data->created_at));
				if($syncDate == date("Y-m-d H")){
					$this->generateMorningAbsenteeData();
					$this->trigger_email_late($meredien);
					$this->trigger_email_absent($meredien);					
					$this->core_layout->logNotification("Morning sync data and attendance log successful.", "success", "gcctimeV2");
				}else{
					$response = $this->attendance->doSyncData($alldevice);
					if($response){
						$this->generateMorningAbsenteeData();
						$this->trigger_email_late($meredien);
						$this->trigger_email_absent($meredien);
						$this->core_layout->logNotification("Morning sync data and attendance log successful.", "success", "gcctimeV2");
					}else{
						$this->core_layout->logNotification("Morning sync data and email sending failed!", "error", "gcctimeV2");
					}
				}
			}else{
				$response = $this->attendance->doSyncData($alldevice);
				if($response){
					$this->generateMorningAbsenteeData();
					$this->trigger_email_late($meredien);
					$this->trigger_email_absent($meredien);
					$this->core_layout->logNotification("Morning sync data and attendance log successful.", "success", "gcctimeV2");
				}else{
					$this->core_layout->logNotification("Morning sync data and email sending failed!", "error", "gcctimeV2");
				}
			}
			
		}
	}
	
	function getLastSync(){
		$this->db->from("event");
		$this->db->where("event_name", "sync_data");
		$this->db->like("created_at", date("Y-m-d"));
		$this->db->order_by("created_at", "ASC");
		$this->db->limit(1);
		$query = $this->db->get();
		
		if($query->num_rows() == 1){
			return $query->row();
		}else{
			return false;
		}
	}
	
	public function syncdata_device($alldevice=false, $id=null){
		$resultset = array();
		/*** $response = $this->attendance->doSyncData($alldevice); ***/
		$response = $this->adm_biometric->biometricSync($alldevice, $id);
		$resultset["response"] = $response;
		echo json_encode($resultset);
	}
	
	function syncdata_test($alldevice=false){
		echo "<pre>";
		var_dump($alldevice);
		$this->adm_biometric->getBiometricData2($alldevice);
	}
	
	public function resend_email(){
		$resultset = array();
		$post = $this->input->post();
		if($post){
			$ampm = date("A");
			
			if(isset($post["late_report"]) && $post["late_report"] == "on"){
				$email_late = $this->shift_manangement->emailLateNotification();
				$_ampm = ($ampm == "AM")? "Morning":"Afternoon";
				
				if($email_late){
					$resultset["late_email"] = true;
					$this->core_layout->logNotification("Manual Re-send, {$_ampm} late email notification has been sent.", "success", "gcctimeV2");
				}else{
					$resultset["late_email"] = false;
					$this->core_layout->logNotification("Manual Re-send, {$_ampm} late email notification sending failed!", "error", "gcctimeV2");
				}
			}
			if(isset($post["absentee_report"]) && $post["absentee_report"] == "on"){
				if($ampm == "AM"){ $this->generateMorningAbsenteeData(); }
				if($ampm == "PM"){ $this->generateAfternoonAbsenteeData(); }
				
				$email_absent = $this->shift_manangement->emailAbsentNotification();
				$_ampm = ($ampm == "AM")? "Morning":"Afternoon";
				
				if($email_absent){
					$resultset["absentee_email"] = true;
					$this->core_layout->logNotification("Manual Re-send, {$_ampm} absentee email notification has been sent.", "success", "gcctimeV2");
				}else{
					$resultset["absentee_email"] = false;
					$this->core_layout->logNotification("Manual Re-send, {$_ampm} absentee email notification sending failed!", "error", "gcctimeV2");
				}
			}
			$resultset["response"] = true;
		}else{
			$resultset["response"] = false;
		}
		
		echo json_encode($resultset);
	}
	
	private function getDevice(){
		$query =  $this->crud->load(array("status"=>1),"gcctimeutility.devices");
		return $query;
	}
	private function checkAttendanceData($biometric_id = NULL, $datetime = NULL){
		$query = $this->crud->load(array("biometric_id"=>$biometric_id,"datetime"=>$datetime),"gcctimeutility.attendance");
		return $query ? TRUE : FALSE;
	}
	function log_event($event = ""){
		$query = $this->crud->insert(array("event_name"=>$event),"gcctimeutility.event");
		return $query ? TRUE : FALSE;
	}
	
	public function getAbsentToday()
	{
		$content = "";
		$getPersonnel = $this->crud->getCollection(array(),"gcctimeutility.personnel");

	     if($getPersonnel){
	     	foreach($getPersonnel as $_getPersonnel){
	     		$getSingleAttendanceByDateRange = $this->attendance->getSingleAttendanceByDateRange($_getPersonnel["biometric_id"],$this->getDateRangeToday());
	     		if(!$getSingleAttendanceByDateRange){
	     			$content .= "<div class='m-list-timeline__item'>
						<span class='m-list-timeline__badge m-list-timeline__badge--success'></span>
						<span class='m-list-timeline__text'>
							".$_getPersonnel["name"]."
						</span>
						<span class='m-list-timeline__time'>
							
						</span>
					</div>";
	     		}
	     	}
	     }

	     echo $content;
	}

	private function trapErrSyncData(){
		//start sync data
		$this->syncdata();
		$today = date("Y-m-d");

		$datecheck = new DateTime($today);

		$getPersonnel = $this->crud->getCollection(array(),"gcctimeutility.personnel");

		if(in_array(("2000-01-01 00:00:00"), $getPersonnel)){
			return FALSE;
		}else{
			return $getPersonnel;
		}
	}
	
	private function am_trigger($alldevice=false){
		$_weekday = date("l", strtotime($this->today));
		$_weekday = strtolower($_weekday);
		$meredien = "AM";
	
		if($_weekday !== "sunday"){
			$response = $this->attendance->doSyncData($alldevice);
			if($response){
				$this->core_layout->logNotification("Morning sync data and attendance log successful.", "success", "gcctimeV2");
				$this->generateMorningAbsenteeData();
				
				$this->trigger_email_late($meredien);
				$this->trigger_email_absent($meredien);
			}else{
				$this->core_layout->logNotification("Morning sync data failed, restart syncing of data!", "error", "gcctimeV2");
				$resyncData = $this->allowResyncData();
				if($resyncData){
					$this->am_trigger();
				}else{
					$this->core_layout->logNotification("Morning resync data limit reached!", "error", "gcctimeV2");
					$this->shift_manangement->resyncDataLimit();
				}
			}
		}
	}
	
	private function generateMorningAbsenteeData_buggy050319($currentDate=null){
		$meredien="AM";
		$absenteeCount = 0;
		$data = $this->attendance->generateAbsenteeRecord($meredien, $currentDate);
		if($data){
			$absenteeCount = count($data);
			if($absenteeCount){
				$this->core_layout->logNotification("Morning sync data, ({$absenteeCount}) absentee records found", "success", "gcctimeV2");				
			}else{
				$this->core_layout->logNotification("Morning sync data, no absentee record", "success", "gcctimeV2");				
			}
			return true;
		}else{
			$this->core_layout->logNotification("No active personnels found!", "error", "gcctimeV2");				
			return false;
		}
	}
	
	private function checkEmployeeShiftStarted($biometric_id=null, $_currentTime=null, $weekday=null, $meredien=null){
		$shiftStarted = false;
		if($biometric_id && $_currentTime && $weekday && $meredien){
			$this->db->select("c.shift_resource");
			$this->db->from("gcctimeutility.personnel a");
			$this->db->join("gccmaster.tblemployees b", "b.biometricno = a.biometric_id OR b.biometricno = a.biometricno");
			$this->db->join("gcctimeutility.shift_schedule_resource c", "c.shift_id = a.shift_id");
			$this->db->where("a.is_active", 1);
			$this->db->where("a.shift_id !=", 0);
			$this->db->group_start();
			$this->db->where("a.biometric_id", $biometric_id);
			$this->db->or_where("a.biometricno", $biometric_id);
			$this->db->group_end();
			$qTemp = $this->db->get();
			if($qTemp->num_rows() == 1){
				$tempResource = @unserialize($qTemp->row()->shift_resource);
				if(is_array($tempResource) && count($tempResource) > 0){
					$this->db->from("gcctimeutility.shift_schedule_list");
					$this->db->where("weekday", $weekday);
					$this->db->where("is_active", 1);
					$this->db->where_in("id", $tempResource);
					$qTempResource = $this->db->get();
					if($qTempResource->num_rows() == 1){
						$tempRow = $qTempResource->row();
						$tempStart = strtolower("{$meredien}_start");
						$tempEnd = strtolower("{$meredien}_end");
						$shiftStart = strtotime(date("Y-m-d")." ".$tempRow->$tempStart);
						$shiftEnd = strtotime(date("Y-m-d")." ".$tempRow->$tempEnd);
						if(($_currentTime >= $shiftStart && $_currentTime <= $shiftEnd) 
						&& ($tempRow->$tempStart !== "00:00:00" && $tempRow->$tempEnd !== "00:00:00")){
							$shiftStarted = true;
						}
					}
				}
			}
		}

		return $shiftStarted;
	}

	private function generateMorningAbsenteeData(){
		$getPersonnel = $this->getActivePersonnels();
		 if($getPersonnel){
			$meredien="AM";
			$absenteeCount = 0;
			$_currentTime = strtotime(Date("Y-m-d H:i"));
			$_currentWeekday = strtolower(Date("l"));
			foreach($getPersonnel as $_getPersonnel){
				$shiftStarted = false;
				$arrData = array();
				$arrData["biometric_id"] = $_getPersonnel["biometric_id"];
				$arrData["meredien"] = $meredien;

				$shiftStarted = $this->checkEmployeeShiftStarted($_getPersonnel["biometric_id"], $_currentTime, $_currentWeekday, $meredien);
				if($shiftStarted){
					$response = $this->getTodayAbsentRecord($arrData);
					$getSingleAttendanceByDateRange = $this->attendance->getSingleAttendanceByDateRange($_getPersonnel["biometric_id"], $this->getDateRangeToday());
					if(!$getSingleAttendanceByDateRange && $response == true){
						if($_getPersonnel["department_id"] !== "0" && $_getPersonnel["shift_id"] !== "0"){
							$added = $this->db->insert("gcctimeutility.absent", $arrData);
							if($added){	$absenteeCount++; }
						}
					}
				}
			}

			if($absenteeCount){
				$this->core_layout->logNotification("Morning sync data, ({$absenteeCount}) absentee records found", "success", "gcctimeV2");				
			}else{
				$this->core_layout->logNotification("Morning sync data, no absentee record", "success", "gcctimeV2");				
			}
			return true;
		}else{
			$this->core_layout->logNotification("No active personnels found!", "error", "gcctimeV2");				
			return false;
		}
	}
	
	private function generateAfternoonAbsenteeData_buggy050319($currentDate = null){
		$meredien="PM";
		$absenteeCount = 0;
		$data = $this->attendance->generateAbsenteeRecord($meredien, $currentDate);
		if($data){
			$absenteeCount = count($data);
			if($absenteeCount){
				$this->core_layout->logNotification("Afternoon sync data, ({$absenteeCount}) absentee records found", "success", "gcctimeV2");				
			}else{
				$this->core_layout->logNotification("Afternoon sync data, no absentee record", "success", "gcctimeV2");				
			}
			return true;
		}else{
			$this->core_layout->logNotification("No active personnels found!", "error", "gcctimeV2");				
			return false;
		}
	}
	
	private function generateAfternoonAbsenteeData(){
		$getPersonnel = $this->getActivePersonnels();
		if($getPersonnel){
			$meredien = "PM";
			$noonTimeRange = $this->getNoonAbsentTimeRange();
			$absenteeCount = 0;
			$_currentTime = strtotime(Date("Y-m-d H:i"));
			$_currentWeekday = strtolower(Date("l"));
			foreach($getPersonnel as $_getPersonnel){
				$shiftStarted = false;
				$arrData = array();
				$arrData["biometric_id"] = $_getPersonnel["biometric_id"];
				$arrData["meredien"] = $meredien;

				$shiftStarted = $this->checkEmployeeShiftStarted($_getPersonnel["biometric_id"], $_currentTime, $_currentWeekday, $meredien);
				if($shiftStarted){
					$response = $this->getTodayAbsentRecord($arrData);
					$getSingleAttendanceByDateRange = $this->attendance->getSingleAttendanceByDateRange($_getPersonnel["biometric_id"], $this->getDateRangeToday());
					if(!$getSingleAttendanceByDateRange && $response == true){
						if($_getPersonnel["department_id"] !== "0" && $_getPersonnel["shift_id"] !== "0"){
							$added = $this->db->insert("gcctimeutility.absent", $arrData);
							if($added){ $absenteeCount++; }
						}
					}
	
					$afternoonAttendance = $this->shift_manangement->getSingleAttendanceByDateRangeData($_getPersonnel["biometric_id"], $noonTimeRange);
					$recordCount = $this->shift_manangement->getPersonnelCount($_getPersonnel["biometric_id"], date("Y-m-d"));
	
					if($response == true && $afternoonAttendance == true && $recordCount == 0){
						if($_getPersonnel["department_id"] !== "0" && $_getPersonnel["shift_id"] !== "0"){
							$added = $this->db->insert("gcctimeutility.absent", $arrData);
							if($added){ $absenteeCount++; }
						}
					}
					
					$noLogout = $this->noonEmployeeNoLogout($_getPersonnel["biometric_id"], date("Y-m-d"));
					if($response == true && $noLogout == true){
						if($_getPersonnel["department_id"] !== "0" && $_getPersonnel["shift_id"] !== "0"){
							$added = $this->db->insert("gcctimeutility.absent", $arrData);
							if($added){ $absenteeCount++; }
						}
					}
				}
			}
			
			if($absenteeCount){
				$this->core_layout->logNotification("Afternoon sync data, ({$absenteeCount}) absentee records found", "success", "gcctimeV2");				
			}else{
				$this->core_layout->logNotification("Afternoon sync data, no absentee record", "success", "gcctimeV2");				
			}
			return true;
		}else{
			$this->core_layout->logNotification("No active personnels found!", "error", "gcctimeV2");
			return false;
		}
	} 
	
	function noonEmployeeNoLogout($biometricId=null, $date=null){
		if($biometricId && $date){
			$startDate = date("Y-m-d 00:00:00", strtotime($date));
			$endDate = date("Y-m-d 13:31:00", strtotime($date));
			
			$this->db->from("gcctimeutility.attendance");
			$this->db->where("biometric_id", $biometricId);
			$this->db->where("datetime >=", $startDate);
			$this->db->where("datetime <=", $endDate);
			$query = $this->db->get();
			
			$count = $query->num_rows();
			if($count == 1 || $count == 0){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	private function allowResendEmail(){
		$this->db->from("gccmaster.log_notification");
		$this->db->where("status", "error");
		$this->db->like("notification", "email");
		$this->db->like("created_at", $this->today);
		
		$query = $this->db->get();
		if($query->num_rows() <= 10){ return true; }
		else{ return false; }
	}
	
	private function allowResyncData(){
		$this->db->from("gccmaster.log_notification");
		$this->db->where("status", "error");
		$this->db->like("notification", "sync data");
		$this->db->like("created_at", $this->today);
		
		$query = $this->db->get();
		if($query->num_rows() <= 10){ return true; }
		else{ return false; }
	}
	
	public function trigger_email_absent($ampm="AM"){
		$email_absent = $this->shift_manangement->emailAbsentNotification();
		$_ampm = ($ampm == "AM")? "Morning":"Afternoon";
		if($email_absent){
			$this->core_layout->logNotification("{$_ampm} absentee email notification has been sent.", "success", "gcctimeV2");
		}else{ 
			$this->core_layout->logNotification("{$_ampm} absentee email notification sending failed!", "error", "gcctimeV2");
			$allowResend = $this->allowResendEmail();
			if($allowResend){
				$this->core_layout->logNotification("{$_ampm} absentee email notification has been resent", "success", "gcctimeV2");
				$this->trigger_email_absent($ampm);
			}else{
				$this->core_layout->logNotification("{$_ampm} absentee email notification resend limit reached!", "error", "gcctimeV2");
				$this->shift_manangement->resendEmailLimit();
			}
		}
	}
	public function trigger_email_late($ampm="AM", $isCustom=false){
		$email_late = $this->shift_manangement->emailLateNotification();
		$_ampm = ($ampm == "AM")? "Morning":"Afternoon";
		
		if($email_late){
			$this->core_layout->logNotification("{$_ampm} late email notification has been sent.", "success", "gcctimeV2");
		}else{
			$this->core_layout->logNotification("{$_ampm} late email notification sending failed!", "error", "gcctimeV2");
			$allowResend = $this->allowResendEmail();
			if($allowResend){
				$this->core_layout->logNotification("{$_ampm} late email notification has been resent", "success", "gcctimeV2");
				$this->trigger_email_late($ampm);
			}else{
				$this->core_layout->logNotification("{$_ampm} late email notification resend limit reached!", "error", "gcctimeV2");
				$this->shift_manangement->resendEmailLimit();
			}
		}
	}
	
	public function nineAMScript($alldevice=false){
		$this->am_trigger($alldevice);
	}
	
	private function getActivePersonnels(){
		$query = $this->db->get_where("gcctimeutility.personnel", array("is_active"=>1));
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return false;
		}
	}
	
	private function getTodayAbsentRecord($arrData=array()){
		$hasNoRecord = false;
		if($arrData){
			$this->db->from("gcctimeutility.absent");
			$this->db->where("biometric_id", $arrData["biometric_id"]);
			$this->db->where("meredien", $arrData["meredien"]);
			$this->db->like("updated_at", date("Y-m-d"));
			$query = $this->db->get();
			$hasNoRecord = ($query->num_rows() == 0)? true: false;
		}
		return $hasNoRecord;
	}

	private function absenteeDelayReport(){
			//$tDate = date('Y-m-d',strtotime("12/08/2018"));
	     	$tDate = date('Y-m-d');
			$tDate=date('Y-m-d', strtotime($tDate));
			//echo $tDate; // echos today! 
			$DateBegin = date('Y-m-d', strtotime("12/07/2018"));
			$DateEnd = date('Y-m-d', strtotime("12/09/2018"));
			echo $tDate;
			if($tDate >= $DateBegin && $tDate <= $DateEnd) {
				return FALSE;
			}else{
				return TRUE;
			}
	}

	private function doublelackDelayReport(){
			//$tDate = date('Y-m-d',strtotime("12/08/2018"));
	     	$tDate = date('Y-m-d');
			$tDate=date('Y-m-d', strtotime($tDate));
			//echo $tDate; // echos today! 
			$DateBegin = date('Y-m-d', strtotime("12/08/2018"));
			$DateEnd = date('Y-m-d', strtotime("12/10/2018"));
			echo $tDate;
			if($tDate >= $DateBegin && $tDate <= $DateEnd) {
				return FALSE;
			}else{
				return TRUE;
			}
	}
	
	private function pm_trigger($alldevice=false){
		$_weekday = date("l", strtotime($this->today));
		$_weekday = strtolower($_weekday);
		$meredien = "PM";
		
		if($_weekday !== "sunday"){
			$response = $this->attendance->doSyncData($alldevice);
			if($response){
				$this->core_layout->logNotification("Afternoon sync data and attendance log successful.", "success", "gcctimeV2");
				$this->generateAfternoonAbsenteeData();
				
				$this->trigger_email_late($meredien);
				$this->trigger_email_absent($meredien);
			}else{
				$this->core_layout->logNotification("Afternoon sync data failed, restart syncing of data!", "error", "gcctimeV2");
				$resyncData = $this->allowResyncData();
				if($resyncData){
					$this->pm_trigger($alldevice);
				}else{
					$this->core_layout->logNotification("Afternoon resync data limit reached!", "error", "gcctimeV2");
					$this->shift_manangement->resyncDataLimit();
				}
			}
		}
	}
	
	public function onethirtyPMScript($alldevice=false){
		$this->pm_trigger($alldevice);
	}
	
	public function getNoonAbsentTimeRange(){
		$start = date("Y-m-d")." "."12:01:00";
		$end = date("Y-m-d")." "."13:31:00";

		return array("start"=>$start,"end"=>$end);
	}

	function getDateRangeToday(){
		$today = date("Y-m-d");

		$begin = new DateTime($today);
		$begin = $begin->format('Y-m-d H:i:s');

		$tom = new DateTime($today);
		$tom = $tom->modify('+1 day');

		$newtime = $tom->format('Y-m-d H:i:s');
		$newtime = new DateTime($newtime);
		$newtime = $newtime->modify("-1 second");
		$newtime = $newtime->format('Y-m-d H:i:s');

		return array("start"=>$begin,"end"=>$newtime);
	}


	public function email_notification(){
		$currentAbsent = $this->attendance->getCurrentAbsent();
		$data = (isset($currentAbsent["check_absent"]) && $currentAbsent["check_absent"])? $currentAbsent["check_absent"]: array();
		
		$config = Array(
	    	'protocol' => 'smtp',
	        'smtp_host' => 'ssl://smtp.gmail.com',
	        'smtp_port' => 465,
	        'smtp_user' => 'gcceforms@gmail.com',
	        'smtp_pass' => 'Sc0t2366',
	        'newline'  => "\r\n", 
	        'mailtype'  => 'html', 
	        'charset'   => 'utf-8',
	    );
		
		$this->email->initialize($config);
		$this->email->from('gcceforms@gmail.com', 'GC&C TIME ATTENDANCE');
		$this->email->to('jp03@gccph.com');
		
		/* $this->email->to('busysanalyst@gccph.com');
	    $this->email->cc('qmsassistant@gccph.com, jp01@gccph.com, jp03@gccph.com'); */
		
		/*** $this->email->to('hr@gccph.com');
		$this->email->cc('hrtimekeeper@gccph.com,hrtimekeeper01@gccph.com,busysanalyst@gccph.com,jp03@gccph.com,cmdumancas@gmail.com'); ***/
		
		$message = "";
		$message .= $this->load->view("templates/email/email-absent_template", array("data"=>$data), true);
		$this->email->subject('Absentee Report'. " - " .date("F d, Y A"));
		$this->email->message($message);
		$result = $this->email->send();
		
		echo $this->email->print_debugger();
	}

	public function testview(){
		$this->load->view("attendance/getLateToday");
	}

	public function test_late_email(){
		// To check late date
		$ampm = date("A");
		$currentLate = $this->shift_manangement->getCurrentLate();
		$state = (isset($currentLate["current_state"]) && $currentLate["current_state"]) ? $currentLate["current_state"] : $ampm;

		// $currentState = ($state) ? strtolower($state) : strtolower($ampm);
		$currentState = "pm";
		$data = (isset($currentLate["checklate_{$currentState}"]) && $currentLate["checklate_{$currentState}"]) ? $currentLate["checklate_{$currentState}"] : array();

		echo "<pre>";
		print_r($data);
		echo "</pre>";
		die();

		// // =============================================================
		// // View late email template

		// /**
		//  * station_title is came from the looped data key per station
		//  * data is the array data per station
		//  * static data here is for testing purposes only
		//  */

		// $arrData = array(
		// 	"station_title" => "GC&C BATA", 
		// 	"data" => ["GC&C BATA" => $data["GC&C BATA"]], 
		// 	"state" => $currentState
		// );

		// return $this->load->view("templates/email/email-late_template", $arrData);
		// die();
		// =============================================================

		// To trigge late email
		// return $this->shift_manangement->emailLateNotification();
		// die();
	}

	public function test_email($attendance, $date, $ampm) {
		if ($attendance == 'late') {
			$currentLate = $this->shift_manangement->test_getCurrentLate($date);

			$currentState = $ampm;
			$data = (isset($currentLate["checklate_{$currentState}"]) && $currentLate["checklate_{$currentState}"]) ? $currentLate["checklate_{$currentState}"] : array();

			echo "<pre>";
			print_r($data);
			echo "</pre>";

			return $this->shift_manangement->test_emailLateNotification($date, $ampm);
		}

		if ($attendance == 'absent') {
			$currentAbsent = $this->shift_manangement->test_getCurrentAbsent($date, $ampm);

			echo "<pre>";
			print_r($currentAbsent);
			echo "</pre>";

			return $this->shift_manangement->test_emailAbsentNotification($date, $ampm);
		}
	}

	public function test_absent_email(){
		// To check absent date
		// $meridiem = "am"; // AM or PM, get the meridiem from currentAbsent
		$currentAbsent = $this->shift_manangement->getCurrentAbsent();

		echo "<pre>";
		print_r($currentAbsent);
		echo "</pre>";

		die();

		// $data = (isset($currentAbsent["check_absent"]) && $currentAbsent["check_absent"]) ? $currentAbsent["check_absent"] : array();

		// echo "<pre>";
		// print_r($data["am"]);
		// echo "</pre>";

		// die();
		
		// $arrData = array(
		// 	"station_title" => "TCD", 
		// 	"meridiem" => $meridiem,
		// 	"data" => $data[$meridiem]['GC&C BATA'], 
		// );

		// return $this->load->view("templates/email/email-absent_template", $arrData);
		// die();

		// To trigge absent email
		return $this->shift_manangement->emailAbsentNotification();
		// die();
	}
	
	public function email_lateNotification(){
		$currentLate = $this->attendance->getCurrentLate();
		$state = (isset($currentLate["current_state"]) && $currentLate["current_state"])? $currentLate["current_state"]: "";
		
		$currentState = ($state)? strtolower($state): "";
		$data = (isset($currentLate["checklate_{$currentState}"]) && $currentLate["checklate_{$currentState}"])? $currentLate["checklate_{$currentState}"]: array();
		
		$config = Array(
	    	'protocol' => 'smtp',
	        'smtp_host' => 'ssl://smtp.gmail.com',
	        'smtp_port' => 465,
	        'smtp_user' => 'gcceforms@gmail.com',
	        'smtp_pass' => 'Sc0t2366',
	        'newline'  => "\r\n", 
	        'mailtype'  => 'html', 
	        'charset'   => 'utf-8',
	    );

	    $this->email->initialize($config);
		$this->email->from('gcceforms@gmail.com', 'GC&C TIME ATTENDANCE');
	    $this->email->to('jp03@gccph.com');
		
		/* $this->email->to('busysanalyst@gccph.com');
		$this->email->cc('qmsassistant@gccph.com, jp01@gccph.com, jp03@gccph.com'); */
		
	    /*** $this->email->to('hr@gccph.com');
		$this->email->cc('hrtimekeeper@gccph.com,hrtimekeeper01@gccph.com,busysanalyst@gccph.com,jp03@gccph.com,cmdumancas@gmail.com'); ***/
		
		$message = "";
	    $message .= $this->load->view("templates/email/email-late_template", array("data"=>$data, "state"=>$state), true);
		
		$this->email->subject('Late Report'. " - " .date("F d, Y"));
	    $this->email->message($message);
	    $result = $this->email->send();
		
		echo $this->email->print_debugger();
	}
		
	//code for generating email for lacking and double entry
	function email_daily_report(){
		$_weekday = date("l", strtotime($this->today));
		$_weekday = strtolower($_weekday);
		
		$currentDate = date("Y-m-d H:i:s", strtotime($this->today));
		$responseEvent = $this->shift_manangement->getScheduledEvent($currentDate);
		
		if($_weekday !== "sunday" && $responseEvent == false){
			$email_lacking = $this->shift_manangement->emailLackingDoubleEntryNotification();
			if($email_lacking){ $this->core_layout->logNotification("Lacking entry email notification has been sent.", "success", "gcctimeV2"); }
			else{ $this->core_layout->logNotification("Lacking entry email notification sending failed!", "error", "gcctimeV2"); }
			
			$email_undertime = $this->shift_manangement->emailUndertimeNotification();
			if($email_undertime){ $this->core_layout->logNotification("Undertime email notification has been sent.", "success", "gcctimeV2"); }
			else{ $this->core_layout->logNotification("Undertime email notification sending failed!", "error", "gcctimeV2"); }
			
			$this->getUploadedFiles();
		}
		
		if($responseEvent == true){
			$this->core_layout->logNotification("No email reporting for underime, lacking and double entries - scheduled event is currently active.", "info", "gcctimeV2");				
		}
	}
	
	function resend_email_daily_report($date=null){
		if($date){ $this->today = date("Y-m-d", strtotime($date)); }
		
		$_weekday = date("l", strtotime($this->today));
		$_weekday = strtolower($_weekday);
		
		$currentDate = date("Y-m-d H:i:s", strtotime($this->today));
		$responseEvent = $this->shift_manangement->getScheduledEvent($currentDate);
		
		if($_weekday !== "sunday" && $responseEvent == false){
			$email_lacking = $this->shift_manangement->emailLackingDoubleEntryNotification($date);
			if($email_lacking){ $this->core_layout->logNotification("Lacking entry email notification has been sent.", "success", "gcctimeV2"); }
			else{ $this->core_layout->logNotification("Lacking entry email notification sending failed!", "error", "gcctimeV2"); }
			
			$email_undertime = $this->shift_manangement->emailUndertimeNotification($date);
			if($email_undertime){ $this->core_layout->logNotification("Undertime email notification has been sent.", "success", "gcctimeV2"); }
			else{ $this->core_layout->logNotification("Undertime email notification sending failed!", "error", "gcctimeV2"); }
			
			$this->getUploadedFiles();
		}
		
		if($responseEvent == true){
			$this->core_layout->logNotification("No email reporting for underime, lacking and double entries - scheduled event is currently active.", "info", "gcctimeV2");				
		}
	}
	
	function syncPersonnels(){
		echo "<pre>";
		include BASEPATH.'\libraries\zklibrary.php';
		$deviceCollection = $this->getdevices();
		$data = array();
		if(count($deviceCollection) > 0){
			foreach($deviceCollection as $_deviceCollection){
				//zk include lib
				$zk = new ZKLibrary($_deviceCollection["ip_address"], 4370);

				//zk init function
				$zk->connect();
				$zk->disableDevice();
				$users = $zk->getUser();

				$resultarray = array();

				foreach($users as $key=>$user){
					$data = array();
					$data["biometric_id"] = $user[0];
					$data["biometricno"] = $user[0]; /*** new field on gcctimeutility.personnel biometricno 122718 ***/
					$data["name"] = $user[1];
					$data["role"] = $user[2];
					
					//$toAdd = $this->checkPersonnelExist($user[0]);
					if($toAdd){
						$insert = $this->crud->insert($data,"gcctimeutility.personnel");				
					}
				}

				$zk->enableDevice();
				$zk->disconnect();

				$data[$_deviceCollection["device_name"]] = $users;
			}
		}
	}

	function syncAttendanceDataMultiDev(){
		echo "<pre>";
		include BASEPATH.'\libraries\zklibrary.php';
		$deviceCollection = $this->getdevices();
		$data = array();
		if(count($deviceCollection) > 0){
			foreach($deviceCollection as $_deviceCollection){

				$zk = new ZKLibrary($_deviceCollection["ip_address"], 4370);
		 
				$zk->connect();
				$zk->disableDevice();

				$attendance = $zk->getAttendance();

				sleep(1);
				foreach($attendance as $key => $val)
				{
					if(!$this->checkAttendanceData($val[1],$val[3])){
						$insertdata = $this->crud->insert(array("biometric_id"=>$val[1],"state"=>$val[2],"datetime"=>$val[3],"device_id"=>$_deviceCollection["id"]),"gcctimeutility.attendance");
					}
				}

				$zk->enableDevice();
				$zk->disconnect();

				$data[$_deviceCollection["device_name"]] = $attendance;
				
			}
		}
	}
	
	function getdevices(){
		$getDeviceCollection = $this->crud->getCollection(array(),"gcctimeutility.devices");
		return count($getDeviceCollection) > 0? $getDeviceCollection : array();
	}

	function getDeviceById($id = NULL){
		$query = $this->crud->load(array("id"=>$id),"gcctimeutility.devices");
		return $query ? $query : false;
	}
	
	private function getUploadedFiles(){
		$dir    = realpath('uploads/data');
		$files = array_diff( scandir( $dir ), Array(".",".."));
		foreach($files as $file){
			$currentFile = $dir."/".$file;
			$dtx = date("Y-m-d", strtotime("-1 day"));
			
			if(file_exists($currentFile)){
				$dt = date("Y-m-d", filemtime($currentFile));
				if($dt == $dtx){
					$fileData = file_get_contents($currentFile, false);
					$json = json_decode($fileData, true);
					if(isset($json["attendance_log"]) && $json["attendance_log"]){
						$countLogs = count($json["attendance_log"]);
						$deviceId = (isset($json["device_id"]) && $json["device_id"])? intval($json["device_id"]): 0;
						$this->core_layout->logNotification("Total sync data count of ({$countLogs}) records on device id #{$deviceId} as of {$dtx}", "success", "gcctimeV2");
					}
				}
			}
		}
	}
	
	function getLastSyncRecord(){
		$this->db->from("gcctimeutility.event");
		$this->db->where("event_name", "sync_data");
		$this->db->order_by("created_at", "DESC");
		$this->db->limit(1);
		$query = $this->db->get();
		
		if($query->num_rows() == 1){
			return $query->row();
		}else{
			return false;
		}
	}
	
	function update_attendance_data(){
		$date = date("Y-m-d", strtotime("2020-10-30"));
		$dateFilter = date("Y-m-d", strtotime("2020-10-29"));
		
		$data = $this->adm_biometric->manualSetBiometricData($date, $dateFilter);
		echo "<pre>";
		var_dump($date);
		var_dump($dateFilter);
		var_dump($data);
		echo "</pre>";
	}
	
	function absentee_record(){
		echo "<pre>";
		/* $id = "2518"; */
		/* $id = "3513"; */
		$id = "57";
		$date = date("Y-m-d", strtotime("2019-06-11"));
		
		/* $data = $this->shift_manangement->getAvailableLoaV2($id, $date);  */
		$data = $this->shift_manangement->getEmployeeTo($id, $date); 
		var_dump($data);
		
		/* $date = date("Y-m-d", strtotime("2019-05-15"));
		$data = $this->attendance->generateAbsenteeRecord("PM", $date);
		if($data){
			var_dump($data);
		} */
	}
	
	function test_data_schedule(){
		echo "<pre>";
		$currentDate = date("Y-m-d H:i:s", strtotime("2019-06-05"));
		$responseEvent = $this->shift_manangement->getScheduledEvent($currentDate);
		var_dump($responseEvent);
	}
	
	function test_undertime_data(){
		$data = $this->shift_manangement->getCurrentUndertime();
		echo "<pre>";
		var_dump($data);
		echo "</pre>";
	}
	function test_lacking_data(){
		// $cdate = date("Y-m-d", strtotime("2019-07-13"));
		$cdate = date("Y-m-d");
		$data = $this->shift_manangement->getLackingDoubleEntries($cdate);
		
		echo "<pre>";
		var_dump($data);
		echo "</pre>";
	}
	
	function resend_undertime_entry(){
		$entry = $this->shift_manangement->emailUndertimeNotification();
		if($entry){
			echo "email undetime has been sent!";
		}
	}
	
	function resend_lacking_double_entry(){
		$entry = $this->shift_manangement->emailLackingDoubleEntryNotification();
		if($entry){
			echo "email lacking and double entry has been sent!";
		}
	}

	function custom_trigger_event($ampm=null){
		if($ampm){
			$ampm = strtoupper($ampm);
			$_weekday = date("l", strtotime($this->today));
			$_weekday = strtolower($_weekday);
			$meredien = ($ampm)? $ampm: "AM";
			$currentDate = date("Y-m-d H:i:s", strtotime($this->today));
			$responseEvent = $this->shift_manangement->getScheduledEvent($currentDate);
			if($_weekday !== "sunday" && $responseEvent == false){
				$meredien = strtoupper($meredien);
				if($meredien == "AM"){
					$responseAM = $this->generateMorningAbsenteeData();
					if($responseAM){
						$this->trigger_email_late($meredien, true);
						$this->trigger_email_absent($meredien, true);						
					}else{
						$this->core_layout->logNotification("Sending of email reports failed, morning data not found!", "error", "gcctimeV2");
					}
				}

				if($meredien == "PM"){
					$responsePM = $this->generateAfternoonAbsenteeData();
					if($responsePM){
						$this->trigger_email_late($meredien, true);
						$this->trigger_email_absent($meredien, true);						
					}else{
						$this->core_layout->logNotification("Sending of email reports failed, afternoon data not found!", "error", "gcctimeV2");
					}
				}
			}
			
			if($responseEvent == true){
				$this->core_layout->logNotification("No email reporting for late and absent, scheduled event is currently active.", "info", "gcctimeV2");				
			}

		}
	}

	
	function hook_attendance_data(){
		$data = $this->attendance->hookAttendanceData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
	}
	
	function set_attendance_punches($tempAttendance=false){
		$post = $this->input->post();
		$data = $this->attendance->setAttendancePunches($tempAttendance);

		$resultset = array();
		$resultset["response"] = $data;
		if($data){ 
			$post["device_name"] = "No device name";
			if(isset($post["device_id"]) && $post["device_id"]){
				$tempDevice = $this->db->get_where("gcctimeutility.devices", array("id"=>$post["device_id"]));
				if($tempDevice->num_rows() === 1){ $post["device_name"] = $tempDevice->row()->device_name; }
			}
			$resultset["telegram_response"] = $this->sendTelegramPunchesByBiometricId($post);
		}

        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($resultset));
	} 

	function send_sms_absent(){
		$_weekday = date("l", strtotime($this->today));
		$_weekday = strtolower($_weekday);

		$currentDate = date("Y-m-d H:i:s", strtotime($this->today));
		$responseEvent = $this->shift_manangement->getScheduledEvent($currentDate);
		$messageContent = "No message content!";

		if($_weekday !== "sunday" && $responseEvent == false){
			$syncResponse = $this->getLastSyncRecord();
			if($syncResponse){
				$ctr = 0;
				$temp = array();
				$current_mrdn = date("a");
				$meredien = strtoupper($current_mrdn);
				$current_date = date("M d, Y");
				$response = false;

				$createdAt = date("Y-m-d H:i:00", strtotime($syncResponse->created_at));
				$amStart = date("Y-m-d 07:30:00", strtotime($this->today));
				$amEnd = date("Y-m-d 10:00:00", strtotime($this->today));
				
				$pmStart = date("Y-m-d 12:30:00", strtotime($this->today));
				$pmEnd = date("Y-m-d 13:31:00", strtotime($this->today));
				
				if(($createdAt >= $amStart && $createdAt <= $amEnd) && $meredien == "AM"){
					$response = $this->generateMorningAbsenteeData();
				}else if(($createdAt >= $pmStart && $createdAt <= $pmEnd) && $meredien == "PM"){
					$response = $this->generateAfternoonAbsenteeData();
				}else{
					$this->core_layout->logNotification("Sending of sms reports failed, synced data is outdated!", "error", "gcctimeV2");
					$messageContent = "<p>Sending of sms reports failed, synced data is outdated!</p>";
				}

				if($response){
					$currentAbsent = $this->shift_manangement->getCurrentAbsent();
					$arrNames = array();
					if(isset($currentAbsent["check_absent"][$current_mrdn])){
						foreach($currentAbsent["check_absent"][$current_mrdn] as $row){
							$name = $row["name"];
							$biometricno = $row["biometricno"];
							$mobile_no = $this->getEmployeeMobileNo($biometricno);
							$content = $row["content"];
							if($content == "" || $content == "N/A" || $content == "n/a"){
								$time = $current_mrdn == 'am' ? "morning shift" : "afternoon shift";
								$msg = "This is to inform that you forgot to time-in for your ". $time." at ".$current_date .". No advisories? Text OFF to this number. GC&C Cares";
								$send = $this->contacts->sendSMS($mobile_no, $msg);
								if($send){
									if($name){ $arrNames[] = strtoupper($name)." [ {$mobile_no} ]"; }
									else if($biometricno){ $arrNames[] = strtoupper($biometricno)." [ {$mobile_no} ]"; }
									$ctr++;
								}
							}
						}
					}
	
					$full_date = date("M d, Y h:i A");
					$messageContent = "<p>Sms notification as of {$full_date} has been sent with a total of {$ctr} employee(s) with mobile number / no loa or to reference #</p>";
					if(count($arrNames) > 0){
						$messageContent .= "<p>The following employee(s) has been sent sms notification:</p>";
						$messageContent .= "<ol>";
						foreach ($arrNames as $key => $value) { $messageContent .= "<li>{$value}</li>"; }
						$messageContent .= "</ol>";
					}
				}
			}
		}

		$this->sms_absent_send_email($messageContent);
		echo $messageContent;
	}

	function getEmployeeMobileNo($biometricno){
		$this->db->select("mobile_no");
		$this->db->from("gccmaster.tblemployees");
		$this->db->where('biometricno', $biometricno);
		return $this->db->get()->row_array()["mobile_no"];
	}

	function sms_absent_send_email($content){
        if($content){
			$module = "sms_absent_report";
			$email_title = "SMS Absent Report";
			$content_title = "SMS Absent Report";
			
			if($content){
				$sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
				if($sent){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
    }

	function get_punchout_report($email=false){
		$currentDate = date("Y-m-d");
		$data = $this->attendance->getAttendancePunchoutReport($currentDate);
		if(is_array($data) && count($data) > 0){
            $messageContent = "";
            $messageContent .= $this->load->view("gcctime/templates/email/email-punchout_template", 
				array("data"=>$data, "date"=>$currentDate), 
				true);

            if($email){
                $module = "gcctime_punchout_summary";
                $email_title = "Punch Out Report";
                $content_title = "Daily Punch Out Report";
                $content = $messageContent;
                
                if($content){
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                    if($sent){
                        echo $content;
                    }else{
                        echo "Failed Sending Email!";
                        show_error($this->email->print_debugger()); 
                    }
                }else{
                    echo "No email content";
                }
            }else{
                echo $messageContent;
            }
		}else{
			return false;
		}
	}

	public function test_telegram_msg(){
		//$response = $this->sendTelegramMessage("this is a test telegram bot sending. please disregard!");
		$temp = array("biometricno"=>10472, "date"=>"2023-06-08 17:52:00", "verify_method"=>1);
		$response = $this->sendTelegramPunchesByBiometricId($temp);
		echo "<pre>";
		var_dump($response);
	}


	private function sendTelegramMessage($msg=null, $alterChatId=null) {
		$resultset = array();
		if($msg){
			$this->db->where("module","gcctime");
            $this->db->order_by("created_at","DESC");
			$this->db->limit(1);
            $telegram = $this->db->get("gcceforms.telegram_config");
			if($telegram->num_rows() === 1){
				$row = $telegram->row();
				if($row->chat_id && $row->telegram_bot_token){
					$tempTelegramBotToken = $row->telegram_bot_token;
					$tempChatId = $alterChatId ? $alterChatId: $row->chat_id;

					$telegramBotUrl = "https://api.telegram.org/bot{$tempTelegramBotToken}/sendMessage";

					$rawData = array();
					$rawData["chat_id"] = $tempChatId;
					$rawData["text"] = $msg;
					$rawData["parse_mode"] = "html";
		
					$sendOptions = array(
						"http"=>array(
							'method'=>'POST',
							'header'=>"Content-Type:application/x-www-form-urlencoded\r\n",
							'content'=>http_build_query($rawData),
							'ignore_errors'=>true,
						),
					);

					$context = stream_context_create($sendOptions);
					$getContents = file_get_contents($telegramBotUrl, false, $context);
					$response = json_decode($getContents, true);
					if(isset($response["ok"]) && $response["ok"]){
						$resultset["response"] = true;
						$resultset["message"] = "Telegram data has been sent successfully.";
						$resultset["result"] = $response["result"];
					}else{
						$resultset["response"] = false;
						$resultset["message"] = "Failed to send telegram data!!";
					}
				}else{
					$resultset["response"] = false;
					$resultset["message"] = "Telegram configuration, chat id or bot token not found!!";
				}
			}else{
				$resultset["response"] = false;
				$resultset["message"] = "Telegram configuration not found!";
			}
		}else{
			$resultset["response"] = false;
			$resultset["message"] = "Telegram notification, no message found!";
		}
		return $resultset;
	}

	private function sendTelegramPunchesByBiometricId($tempPost=array()){
		$resultset = array();
		$resultset["chat_id"] = 0;
		$telegramResent = isset($tempPost["is_send"]) && intval($tempPost["is_send"]) === 2;
		if(isset($tempPost["biometricno"], $tempPost["date"]) && $tempPost["biometricno"] && $tempPost["date"]){
			$biometricId = trim($tempPost["biometricno"]);
			$timeLog = strtoupper(date("D, M d, Y h:i A", strtotime(trim($tempPost["date"]))));
			$verifyMethod = "TIME IN";
			$deviceName = isset($tempPost["device_name"]) && $tempPost["device_name"] ? trim($tempPost["device_name"]): "No Device Name";

			switch (intval($tempPost["verify_method"])) {
				case 1: $verifyMethod = "TIME OUT"; break;
				case 4: $verifyMethod = "OVERTIME IN"; break;
				case 5: $verifyMethod = "OVERTIME OUT"; break;
				default: $verifyMethod = "TIME IN"; break;
			}

			$this->db->select("CONCAT(UPPER(a.firstname), ' ',
			CASE WHEN UPPER(TRIM(a.middlename)) != 'N/A' AND UPPER(TRIM(a.middlename)) != 'NONE' AND
					TRIM(a.middlename) !='' AND a.middlename IS NOT NULL
				THEN CONCAT(SUBSTR(a.middlename, 1, 1), '.') ELSE ''
			END,' ', UPPER(a.lastname),
			CASE WHEN UPPER(TRIM(a.suffix)) != 'N/A' AND
				UPPER(TRIM(a.suffix !='NONE')) AND a.suffix !='' AND
					a.suffix IS NOT NULL THEN CONCAT(' ', a.suffix) ELSE ''
			END) as employee_name, b.telegram_chat_id");
			$this->db->from("gccmaster.tblemployees as a");
			$this->db->join("gccmaster.tblusers as b", "b.emp_id = a.id");
			$this->db->where("a.biometricno", $biometricId);
			$this->db->where("b.is_suspended", 0);
			$this->db->where("a.employee_status", "Active");
			$qTemp = $this->db->get();
			if($qTemp->num_rows() === 1){
				$tempChatId = $qTemp->row()->telegram_chat_id;
				$employeeName = strtoupper($qTemp->row()->employee_name);
				if($tempChatId){
					$message = "";
					if($telegramResent){
						$tempDate = date("D, M d, Y h:i A");
						$message = "Sorry for the inconvenience as of today `{$tempDate}` for the delayed message.\n\n";
					}
					$tempMessage = "{$message}<b>{$employeeName}</b>\nDateTime: {$timeLog}\nBiometric#: {$biometricId}\nVerifyMethod: {$verifyMethod}\nDeviceName: {$deviceName}";
					$resultset = $this->sendTelegramMessage($tempMessage, $tempChatId);
					$resultset["chat_id"] = $tempChatId;
				}else{
					$resultset["response"] = false;
					$resultset["message"] = "Biometric# {$biometricId}, chat Id not found!";
				}
			}else{
				$resultset["response"] = false;
				$resultset["message"] = "Biometric# {$biometricId} not found!!";
			}
		}else{
			$resultset["response"] = false;
			$resultset["message"] = "No post found!!";
		}

		return $resultset;
	}

	public function sync_active_devices(){
		$results = $this->adm_biometric->getStoredData();
		if($results["response"] && isset($results["files"]) && count($results["files"]) > 0){
			echo "<h3>Related Files</h3>";
			foreach ($results["files"] as $key => $vv) {
				echo "<p style='font-weight: bold;padding: 0px; margin: 5px 0px;'>{$vv}</p>";
			}
			echo "<hr>";
		}
		echo "<table width='50%'>";
		echo "<thead>
			<tr>
				<th align='left'>#</th>
				<th align='left' style='text-transform: uppercase;'>Employee Name</th>
				<th align='left' style='text-transform: uppercase;'>Date & Time</th>
			</tr>
		</thead>";
		echo "<tbody>";
		if($results["response"]){
			$dateTime = array();
			foreach ($results["data"] as $key => $row) { $dateTime[$key] = $row[3]; }
			array_multisort($dateTime, SORT_DESC, $results["data"]);

			foreach ($results["data"] as $key => $value) {
				$this->db->select("UCASE(CONCAT(lastname,
				CASE WHEN suffix != 'N/A' AND suffix !='NONE' AND suffix !='' AND suffix IS NOT NULL THEN CONCAT(' ', suffix) ELSE ''  END, ', ',
				firstname, ' ', CASE WHEN middlename != 'N/A' AND middlename != 'NONE'
				AND middlename !='' AND middlename IS NOT NULL THEN CONCAT(SUBSTR(middlename, 1, 1), '.') ELSE '' END)) as employee_name");
				$qTemp = $this->db->get_where("gccmaster.tblemployees", array("biometricno"=>$value[1]));
				$empName = $qTemp->num_rows() == 1 ? $qTemp->row()->employee_name : strtoupper('No Assigned Name');

				$tempTime = date("H:i", strtotime($value[3]));
				$tempCondition = ($tempTime >= date("H:i", strtotime("08:01")) && $tempTime <= date("H:i", strtotime("12:00")) ||
				$tempTime >= date("H:i", strtotime("13:01")) && $tempTime <= date("H:i", strtotime("15:00")));

				$tempStyle = $tempCondition ? "style='color: red; font-weight: bold;'": "";
				$tempStyle = $empName == "NO ASSIGNED NAME" ? "style='color: blue; font-weight: 500;'": $tempStyle;

				$tempDateRendered = Date("Y-m-d h:i A", strtotime($value[3]));

				echo "<tr {$tempStyle}>";
				echo "<td>{$value[1]}</td>
					<td>{$empName}</td>
					<td align='left'>{$tempDateRendered}</td>";
				echo "</tr>";
			}
		}else{
			echo "<tr><td colspan='2'>No Data Found!</td></tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}

	function get_all_files_monthly(){
		$currentDay = date("Y-m-d");
		$previousMonth = date("Y-m-d", strtotime("-1 month"));
	
		$startDate = date_create($previousMonth);
		$endDate = date_create($currentDay);
		$diff = date_diff($startDate, $endDate);
		
		if($diff->days > 0){
			$_tempArrDates = [];
			$tempArrDates = [];
			$tempDays = $diff->days;
			for($ii=1; $ii<=$tempDays; $ii++){
				$_currentDateFormat = date("Y-m-d", strtotime("+{$ii} days", strtotime($previousMonth)));
				$_tempArrDates[] = $_currentDateFormat;

				$currentDateFormat = date("dYm", strtotime("+{$ii} days", strtotime($previousMonth)));
				$tempArrDates[] = $currentDateFormat;
			}

			if(is_array($tempArrDates) && count($tempArrDates) > 0){
				$filepath = realpath("./uploads/data");
				$scanned_directory = array_diff(scandir($filepath), array('..', '.'));
				$files = array();
				if($scanned_directory){
					foreach($scanned_directory as $dfile){
						$filename = explode(".", $dfile);
						if(count($filename) == 2){
							$fname = explode("-", $filename[0]);
							if(count($fname) == 2){
								$tempData = trim($fname[1]);
								if(in_array($tempData, $tempArrDates)){
									$files[] = $dfile;
								}
							}
						}
					}
				}

				if($files){
					$nData = array();
					$tempIds = array();
					foreach($files as $file){
						$fileUpload = "{$filepath}/{$file}";
						$xfiles = file_get_contents($fileUpload, true);
						$_file = json_decode($xfiles, true);
						$attendanceLogs = (isset($_file["attendance_log"]) && $_file["attendance_log"])? $_file["attendance_log"]: array();
						$deviceId = (isset($_file["device_id"]) && $_file["device_id"])? $_file["device_id"]: 0;
						if($attendanceLogs){
							$chunkLogs = array_chunk($attendanceLogs, 2000, true);
							if(isset($chunkLogs) && count($chunkLogs) > 0){
								foreach($chunkLogs as $logs){
									foreach ($logs as $key => $value) {
										if(isset($value[3]) && $value[3]){
											$cDate = Date("Y-m-d", strtotime($value[3]));
											$tempValue = $value[1]."_".strtotime(trim($value[3]));
											if(in_array($cDate, $_tempArrDates) && !in_array($tempValue, $tempIds)){
												$tempDateFormat = date("Y-m-d H:i:s", strtotime($value[3]));
												$tempWhere = array(
													"biometric_id"=>trim($value[1]),
													"state"=>trim($value[2]),
													"datetime"=>$tempDateFormat,
													"device_id"=>intval($deviceId),
												);

												$qTempFilter = $this->db->get_where("gcctimeutility.attendance", $tempWhere);
												if($qTempFilter->num_rows() == 0){ $nData[] = $tempWhere; }
												$tempIds[] = $tempValue;
												$this->db->reset_query();
											}
										}
									}
								}
							}
						}
					}

					if(is_array($nData) && count($nData) > 0){
						$batchInsert = $this->db->insert_batch("gcctimeutility.attendance", $nData);
						if($batchInsert){
							echo "A total of `{$batchInsert}` has been added to the attendance/s.";
						}else{
							echo "No data to insert!";
						}
					}else{
						echo "No data found !!!!";
					}
				}else{
					echo "No data files to insert data!!";
				}
			}else{
				echo "No data found !! !!";
			}
		}else{
			echo "No data found !!! !!!";
		}
	}

	public function get_employee_data_by_ids(){
		$post = $this->input->post();
		$responseData = array();
		if(isset($post["biometricno"]) && $post["biometricno"]){
			$arrIds = explode(",", $post["biometricno"]);
			$this->db->select("biometricno, idno, lastname, firstname, middlename, suffix, pic_filename, employee_status");
			$this->db->where_in("biometricno", $arrIds);
			$qTemp = $this->db->get("gccmaster.tblemployees");
			if($qTemp->num_rows() > 0){ $responseData = $qTemp->result_array(); }
		}
		echo json_encode($responseData);
	}

	public function get_all_employee_data($token=null){
		$currentDate = date("Ymd");
		$resultset = array();

		if($token === $currentDate){
			$this->db->select("biometricno, UPPER(lastname) as lastname, UPPER(firstname) as firstname, UPPER(middlename) as middlename, UPPER(suffix) as suffix, pic_filename, mobile_no as mobileno, id as employee_id");
			$this->db->from("gccmaster.tblemployees");
			$this->db->where("biometricno !=", "N/A");
			$this->db->where("biometricno !=", "NONE");
			$this->db->where("biometricno !=", NULL);
			$this->db->where("biometricno >", 0);
			$query = $this->db->get();

			$resultset["response"] = true;
			$resultset["data"] = $query->result();
		}else{
			$resultset["response"] = false;
		}

		echo json_encode($resultset);
	}

	function scheduled_cache_flush(){
		$this->redis->flushall();
	}
	
	public function get_app_attendance_records(){
		$this->attendance->syncAttendanceApp();
	}

	public function get_sycned_logs($weekly = 0){
		$timeParams = $weekly == 1 ? "-1 week" : "-2 day";
		$date = date("Y-m-d");
		$weekAgo = date("Y-m-d", strtotime($timeParams));
		$period = new DatePeriod(new DateTime($weekAgo), new DateInterval('P1D'), new DateTime($date));
		$filepath = realpath("./uploads/data");
		$files = $this->getScannedDirectoryFiles($filepath, $period);
		if(is_array($files) && !empty($files)){
			$nData = array();
			foreach($files as $file){
				$fileUpload = "{$filepath}/{$file}";
				$xfiles = file_get_contents($fileUpload, true);
				$_file = json_decode($xfiles, true);
				$attendanceLogs = (isset($_file["attendance_log"]) && $_file["attendance_log"])? $_file["attendance_log"]: array();
				$deviceId = (isset($_file["device_id"]) && $_file["device_id"])? $_file["device_id"]: 0;
				if($attendanceLogs){
					foreach ($period as $date) {
						$currentDate = date("Y-m-d", strtotime($date->format("Y-m-d")));
						foreach($attendanceLogs as $value){
							$attDate = date("Y-m-d", strtotime($value[3]));
							if($attDate === $currentDate){
								$value[] = $deviceId;
								$nData[] = $value;
							}
						}
					}
				}
			}

			if(is_array($nData) && !empty($nData)){
				$addedRecord = array();
				$newData = $this->uniqueBiometricArray($nData);
				if(is_array($newData) && !empty($newData)){
					foreach ($newData as $nValue) {
						$attRecord = new stdClass();
						$attRecord->biometric_id = $nValue[1];
						$attRecord->state = $nValue[2];
						$attRecord->datetime = $nValue[3];
						$attRecord->verify_method = $nValue[4];
						$attRecord->device_id = $nValue[5];
						$attRecord->is_custom = 2;
						$responseResult = $this->setAttendanceDeviceRecord($attRecord);
						if($responseResult){
							$addedRecord[] = $attRecord;
						}
					}
				}

				if(is_array($addedRecord) && !empty($addedRecord)){
					$isWeekly = $weekly == 1 ? "weekly" : "daily";
					$this->sendTelegramMessage("<b>Biometric Device Record Sync</b>\n\nDevice record sync `{$isWeekly}`, successfully added `".count($addedRecord)."` unsynced attendance records.");
					return $addedRecord;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	protected function getScannedDirectoryFiles($filepath, $period){
		$files = array();
		if($filepath){
			$scanned_directory = array_diff(scandir($filepath), array('..', '.'));
			$files = $this->getFilesInDirectoryByDatePeriod($scanned_directory, $period);
		}
		return $files;
	}

	protected function getFilesInDirectoryByDatePeriod(array $directory, DatePeriod $period): array {
		$files = array();
		foreach ($directory as $file) {
			$filenameParts = explode('.', $file);
			if (count($filenameParts) === 2) {
				$filenameParts = explode('-', $filenameParts[0]);
				if (count($filenameParts) === 2) {
					foreach ($period as $date) {
						$dateString = $date->format('dYm');
						if ($filenameParts[1] === $dateString) {
							$files[] = $file;
						}
					}
				}
			}
		}
		return $files;
	}

	protected function uniqueBiometricArray(array $data): array {
		$unique = [];
		$result = [];
		if(is_array($data) && !empty($data)){
			foreach ($data as $row) {
				$key = $row[1] . '_' . $row[3];
				if (!isset($unique[$key])) {
					$unique[$key] = true;
					$result[] = $row;
				}
			}
		}
		return $result;
	}

	protected function setAttendanceDeviceRecord($att = null){
        $resultResponse = false;
        if ($att->biometric_id) {
            $date = (new DateTime($att->datetime))->format('Y-m-d');
            $time = (new DateTime($att->datetime))->format('H:i');
            $maxPayrollDate = $this->getPayrollMaxDate($att->biometric_id);
            if ($maxPayrollDate !== false && strtotime($date) > strtotime($maxPayrollDate)) {
				$this->db->select("id");
                $this->db->where('biometric_id', $att->biometric_id);
                $this->db->where('DATE(`datetime`)', $date);
                $this->db->like('TIME(datetime)', $time, 'after');
                $existingRecord = $this->db->get('gcctimeutility.attendance');

                if ($existingRecord->num_rows() === 0) {
                    $resultResponse = $this->db->insert('gcctimeutility.attendance', [
                        'biometric_id' => $att->biometric_id,
                        'state' => $att->state,
                        'datetime' => $att->datetime,
                        'verify_method' => $att->verify_method,
                        'device_id' => $att->device_id,
                        'is_custom' => $att->is_custom,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
        return $resultResponse;
    }

    protected function getPayrollMaxDate($biometric_id=null){
        if($biometric_id){
            $this->db->select("MAX(ps.date_end) as max_date");
            $this->db->from("gccmaster.tblemployees emp");
            $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = emp.id AND ps.posted = 1", "LEFT");
            $this->db->where("emp.biometricno", $biometric_id);
            $this->db->group_by("emp.id");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() == 1){ return $qTemp->row()->max_date; }
            else{ return false; }
        }else{ return false; }
        
    }

	public function test_generateMorningAbsenteeData(){
		$getPersonnel = $this->getActivePersonnels();
		if($getPersonnel){
			$meredien="AM";
			$absenteeCount = 0;
			$_currentTime = strtotime(Date("Y-m-d H:i"));
			$_currentWeekday = strtolower(Date("l"));

			// echo "<pre>";
			// var_dump($meredien, $absenteeCount, $_currentTime, $_currentWeekday);
			// echo "</pre>";
			// die();
			
			foreach($getPersonnel as $_getPersonnel){
				$shiftStarted = false;
				$arrData = array();
				$arrData["biometric_id"] = $_getPersonnel["biometric_id"];
				$arrData["meredien"] = $meredien;

				$shiftStarted = $this->checkEmployeeShiftStarted($_getPersonnel["biometric_id"], $_currentTime, $_currentWeekday, $meredien);

				if($shiftStarted){
					$response = $this->getTodayAbsentRecord($arrData);
					
					$getSingleAttendanceByDateRange = $this->attendance->getSingleAttendanceByDateRange($_getPersonnel["biometric_id"], $this->getDateRangeToday());
					if(!$getSingleAttendanceByDateRange && $response == true){
						if($_getPersonnel["department_id"] !== "0" && $_getPersonnel["shift_id"] !== "0"){
							$added = $this->db->insert("gcctimeutility.absent", $arrData);
							if($added){	$absenteeCount++; }
						}
					}
				}
			}

			if($absenteeCount){
				$this->core_layout->logNotification("Morning sync data, ({$absenteeCount}) absentee records found", "success", "gcctimeV2");				
			}else{
				$this->core_layout->logNotification("Morning sync data, no absentee record", "success", "gcctimeV2");				
			}
			return true;
		}else{
			$this->core_layout->logNotification("No active personnels found!", "error", "gcctimeV2");				
			return false;
		}
	}

	public function test_forceAllAbsentToday(){
		$getPersonnel = $this->getActivePersonnels();
		$absenteeCount = 0;

		foreach ($getPersonnel as $person) {
			if($person["department_id"] !== "0" && $person["shift_id"] !== "0"){

				$arrData = [
					"biometric_id" => $person["biometric_id"],
					"meredien"     => "PM"
				];

				if ($this->getTodayAbsentRecord($arrData)) {
					if ($this->db->insert("gcctimeutility.absent", $arrData)) {
						$absenteeCount++;
					}
				}
			}
		}

		$this->core_layout->logNotification(
			"TEST MODE: Forced {$absenteeCount} absentees",
			"warning",
			"gcctimeV2"
		);
	}

}