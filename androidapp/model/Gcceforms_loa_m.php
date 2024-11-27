<?php

class Gcceforms_loa_m extends Dbase{

	public function test_function(){

		
		$this->setNotification('9', 'Test name', 'Test leave', '10644', 'Test reference no.', '1170');

		// $test_array = array("ExponentPushToken[zqxnjiICdNK_9Z1447jW_b]", "ExponentPushToken[WAVxteGLy5tyTsKu9hYCoA]");
		// foreach ($test_array as $token) {
		// 	$this->sendNotification($token, $name, $nature_leave, $loa_id);
		// }

		//echo $this->getSupervisorToken('it', '462');
	}

	// public function get_loa_data(){
	// 	var_dump($_POST);
	// }


	public function get_loa_data(){
		$conn = $this->conn('gcceforms');
		$isView_own_request = $_POST['isView_own_request'];
		$employee_id = $_POST['employee_id'];
		$dept_id = $_POST['dept_id'];
		$datetime_format = 'm/d/Y g:i A';
		$response['data_array'] = array();
		$temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);

		if($isView_own_request == 'view_own_request'){
			$sth = $conn->prepare("SELECT DISTINCT l.*, l.id as loa_id, 
									e.id as emp_id, e.position as position_emp, e.firstname, e.middlename, e.lastname, e.suffix
									FROM gcceforms.loa AS l
									LEFT JOIN gccmaster.tblemployees AS e 
									ON l.employee = e.id
									where l.employee = :emp_id and l.date_from >= :check_ and l.status != 'Cancelled' order by reference_no desc");
			$sth->bindParam(':emp_id', $employee_id);
			$sth->bindParam(':check_', $check);
		}
		if($isView_own_request == 'view_by_dept'){
			$sth = $conn->prepare("SELECT DISTINCT l.*, l.id as loa_id, 
									e.id as emp_id, e.position as position_emp, e.firstname, e.middlename, e.lastname, e.suffix, e.department_id
									FROM gcceforms.loa AS l
									LEFT JOIN gccmaster.tblemployees AS e 
									ON l.employee = e.id
									where l.employee = :dep_id and l.date_from >= :check_ and l.status != 'Cancelled' order by reference_no desc");
			$sth->bindParam(':dep_id', $dept_id);
			$sth->bindParam(':check_', $check);
		}
		if($isView_own_request == 'default'){
			$sth = $conn->prepare("SELECT DISTINCT l.*, l.id as loa_id, 
								e.id as emp_id, e.position as position_emp, e.firstname, e.middlename, e.lastname, e.suffix
								FROM gcceforms.loa AS l
								LEFT JOIN gccmaster.tblemployees AS e 
								ON l.employee = e.id
								where l.date_from >= :check_ and l.status != 'Cancelled' order by reference_no desc");
			$sth->bindParam(':check_', $check);
		}

   		$sth->execute();

   		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

   			$fullname = $this->getDisplayName($row);
            $tempFullname = (object) $fullname;
            $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
            $employee_name = utf8_encode($display_name);

   			$position_ = $this->getPosition($conn,$row['position']);

   			$position = utf8_encode(is_numeric($row['position']) ? $position_['name'] : $row['position']);

   			$list['company'] = $row['company'];
			$list['employee_temp'] = $employee_name;
			$list['position'] = $position;
			$list['department'] = $row['department']; 
			$list['status'] = $row['status'];
			$list['nature'] = $row['nature']; 
			$list['reason'] = utf8_encode($row['reason']);
			$list['reference_no'] = $row['reference_no'];
			$list['id'] = $row['loa_id'];
			//$list['created_by'] = utf8_encode(ucwords(strtolower($this->getSpecificEmployeeName($row['created_by']))));
			//$list['created_dt'] = date("F j, Y, g:i a", strtotime($row['created_dt']));
			//$list['last_edited_by'] = utf8_encode(ucwords(strtolower($this->getSpecificEmployeeName($row['last_edited_by']))));
			//$list['last_edited_dt'] = date("F j, Y, g:i a", strtotime($row['last_edited_dt']));
			$list['employee'] = $row['employee'];
			$list['address'] = utf8_encode($row['address']);
			$list['phone'] = $row['phone'];

			$date_from = $row['date_from'];
			$date_to = $row['date_to'];
			$list['duration'] = $this->getDuration($row['type'],$date_from,$date_to);

			if ($row['type'] == '1') { // Undertime --------------------------------
				$list['type'] = 'Undertime';
				$list['date_time_formatted'] = date($datetime_format, strtotime($date_from))." - ".date($datetime_format, strtotime($date_to));
				$list['date_time_raw'] = $date_from." - ".$date_to;
			} else if ($row['type'] == '2') { // Half Day --------------------------------
				$list['type'] = 'Half Day';
				$list['date_time_formatted'] = date($datetime_format, strtotime($date_from))." - ".date($datetime_format, strtotime($date_to));
				$list['date_time_raw'] = $date_from." - ".$date_to;
			} else if ($row['type'] == '3') { // Whole Day --------------------------------
				$list['type'] = 'Whole Day';
				$list['date_time_formatted'] = date('m/d/Y', strtotime($date_from))." - ".$date_to;
				$list['date_time_raw'] = $date_from." - ".$date_to;
			} else if ($row['type'] == '4') { // Others --------------------------------
				$list['type'] = 'Others';
				$list['date_time_formatted'] = date($datetime_format, strtotime($date_from))." - ".date($datetime_format, strtotime($date_to));
				$list['date_time_raw'] = $date_from." - ".$date_to;
			}
			
			array_push($response['data_array'], $list);
   		}

		echo json_encode($response);
	}

	function setNotification($department, $name, $nature_leave, $loa_id, $ref_no, $user_id){
		$supervisor = $this->getSupervisorToken($department, $user_id);
		if ($supervisor == 'supervisor') {
			$this->sendNotificationAllSupervisor($name, $nature_leave, $loa_id, $ref_no);
		} else if ($supervisor == 'no_supervisor') {
			$this->sendNotificationOthers($name, $nature_leave, $loa_id, $ref_no);
		} else {
			$this->sendNotification($supervisor, $name, $nature_leave, $loa_id, $ref_no);
		}
	}

	function sendNotificationOthers($name, $nature_leave, $loa_id, $ref_no){
		$array_department = array("HRD");
		foreach ($array_department as $departm) {

			$others = $this->getSupervisorToken($departm);
			if ($others != 'no_supervisor') {
				$this->sendNotification($others, $name, $nature_leave, $loa_id, $ref_no);
			}
		}
	}

	function sendNotificationAllSupervisor($name, $nature_leave, $loa_id, $ref_no){
		$conn = $this->conn();
		$sth = $conn->query("SELECT DISTINCT u.mobile_token, d.head_id, u.username
							 FROM gcchris.tbldepartments AS d 
							 LEFT JOIN gccmaster.tblusers AS u
							 ON d.head_id = u.emp_id
							 WHERE (d.description!='' OR d.code!='') AND u.mobile_token!=''");
		while ($row = $sth->fetch()) {
			$this->sendNotification($row, $name, $nature_leave, $loa_id, $ref_no);
		}
	}

	function getSupervisorToken($department, $user_id = null){
		$conn = $this->conn();
		$sth = $conn->query("SELECT u.mobile_token, u.emp_id, u.username 
							 FROM gcchris.tbldepartments AS d 
							 LEFT JOIN gccmaster.tblusers AS u
							 ON d.head_id = u.emp_id 
							 WHERE d.id='$department' OR d.description='$department' OR d.code='$department'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$check_if_user_has_supervisor = $sth->rowCount() > 0 ? $data : 'no_supervisor';

		$check_if_user_is_supervisor = $user_id==$data['emp_id'] ? 'supervisor': $check_if_user_has_supervisor;

		return $check_if_user_is_supervisor;
	}

	function sendNotification($data, $name, $nature_leave, $loa_id, $ref_no){

		if (is_array($data)) {
			$payload = array(
		        'to' => $data['mobile_token'],
		        'sound' => 'default',
		        'title' => 'GC&C EFORMS - LEAVE OF ABSENCE',
		        'body' => strtoupper($name.' has filed '.$nature_leave.' with reference # '.$ref_no),
		        'data' => ["loa_id" => $loa_id]
		    );

			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://exp.host/--/api/v2/push/send",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => json_encode($payload),
			  CURLOPT_HTTPHEADER => array(
			    "Accept: application/json",
			    "Accept-Encoding: gzip, deflate",
			    "Content-Type: application/json",
			    "cache-control: no-cache",
			    "host: exp.host"
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			// if ($err) {
			//   echo "cURL Error #:" . $err;
			// } else {
			//   echo $response;
			// }
		}
	}
    
	public function get_employee_data(){
		$conn = $this->conn("gccmaster");

    	$response['employee_array'] = array();

    	$user_id = $_POST['user_id'];
    	$isView_own_request = $_POST['isView_own_request'];

    	if ($isView_own_request=='true' && $user_id!=1) {
    		$own_request = "and id='$user_id'";
    	} else {
    		$own_request = "";
    	}

    	$sth = $conn->query("SELECT * FROM gccmaster.tblemployees where employee_status='Active' $own_request order by firstname asc");
   		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

   			$fullname = $this->getDisplayName($row);
            $tempFullname = (object) $fullname;
            $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
            $employee_name = utf8_encode($display_name);

            $company_ = $this->getCompany($conn,$row['company_id']);
            $department_ = $this->getDepartment($conn,$row['department_id']);
            $position_ = $this->getPosition($conn,$row['position']);

            // If position name exist in the DB return id. Else return still the same name.
            $check_null_company = utf8_encode($company_['id'] ? $company_['id'] : $row['company_id']);
            $check_null_department = utf8_encode($department_['id'] ? $department_['id'] : $row['department_id']);
            $check_null_position = utf8_encode($position_['id'] ? $position_['id'] : $row['position']);

	 		$list['id'] = $row['id'];
	 		$list['employee_name'] = $employee_name;
			$list['_company_id'] = is_numeric($row['company_id']) ? $row['company_id'] : $check_null_company;
			$list['_department_id'] = is_numeric($row['department_id']) ? $row['department_id'] : $check_null_department;
			$list['_position_id'] = is_numeric($row['position']) ? $row['position'] : $check_null_position;
			$list['company_desc'] = utf8_encode(is_numeric($row['company_id']) ? $company_['description'] : $row['company_id']);
			$list['department_desc'] = utf8_encode(is_numeric($row['department_id']) ? $department_['description'] : $row['department_id']);
			$list['position_name'] = utf8_encode(is_numeric($row['position']) ? $position_['name'] : $row['position']);
			array_push($response['employee_array'], $list);
	    }

	    // invalid reason
	    $response['reason_array'] = array();
	    $sth = $conn->query("SELECT reason FROM gcceforms.loa_invalid_reason");
   		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

   			$list_['reason'] = $row['reason'];
   			array_push($response['reason_array'], $list_);
   		}

	    echo json_encode($response);
	}

	function getCompany($conn,$id){
		$sth = $conn->query("SELECT id,description from gcchris.tblcompanies where id='$id' or description='$id'");
		$data = $sth ? $sth->fetch(PDO::FETCH_ASSOC) : array();
		return $data;
	}

	function getDepartment($conn,$id){
		$sth = $conn->query("SELECT id,description from gcchris.tbldepartments where id='$id' or description='$id' or code='$id'");
		$data = $sth ? $sth->fetch(PDO::FETCH_ASSOC) : array();
		return $data;
	}

	function getPosition($conn,$id){
		$sth = $conn->query("SELECT id,name from gcchris.tblposition where id='$id' or name='$id'");
		$data = $sth ? $sth->fetch(PDO::FETCH_ASSOC) : array();
		return $data;
	}

	public function view_loa_details(){
		$conn = $this->conn('gcceforms');

		$loa_id = $_POST['loa_id'];

		$datetime_format = 'm/d/Y g:i A';

		$response['data_array'] = array();

		$sth = $conn->prepare("SELECT l.*,l.id as loa_id,e.id as emp_id, e.position as position_emp, e.firstname, e.middlename, e.lastname, e.suffix
							   FROM gcceforms.loa AS l
							   LEFT JOIN gccmaster.tblemployees AS e 
							   	  ON l.employee = e.id
							   where l.id = :loa_id");
		$sth->bindParam(':loa_id', $loa_id);
   		$sth->execute();

   		$data = $sth->fetch(PDO::FETCH_ASSOC);

   		$fullname = $this->getDisplayName($data);
        $tempFullname = (object) $fullname;
        $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
        $employee_name = utf8_encode($display_name);

     	$company_ = $this->getCompany($conn,$data['company']);
      	$department_ = $this->getDepartment($conn,$data['department']);
        $position_ = $this->getPosition($conn,$data['position']);

		$company = is_numeric($data['company']) ? $company_['description'] : $data['company'];
		$department = is_numeric($data['department']) ? $department_['description'] : $data['department'];
		$position = is_numeric($data['position']) ? $position_['name'] : $data['position'];

   		$list['company'] = $company;
		$list['department'] = $department; 
		$list['position'] = $position;
		$list['employee_temp'] = $employee_name;
		$list['status'] = $data['status'];
		$list['nature'] = $data['nature']; 
		$list['reason'] = utf8_encode($data['reason']);
		$list['reference_no'] = $data['reference_no'];
		$list['id'] = $data['loa_id'];
		$list['created_by'] = utf8_encode($this->getSpecificEmployeeName($data['created_by']));
		$list['created_dt'] = date("F j, Y, g:i a", strtotime($data['created_dt']));
		$list['last_edited_by'] = utf8_encode($this->getSpecificEmployeeName($data['last_edited_by']));
		$list['last_edited_dt'] = date("F j, Y, g:i a", strtotime($data['last_edited_dt']));
		$list['employee'] = $data['employee'];
		$list['address'] = utf8_encode($data['address']);
		$list['phone'] = $data['phone'];
		$list['approved_by'] = utf8_encode($this->getSpecificEmployeeName($data['approved_by']));
		$list['approved_dt'] = date("F j, Y, g:i a", strtotime($data['approved_dt']));
		$list['approved_remarks'] = $data['approved_remarks'];
		$list['disapproved_by'] = utf8_encode($this->getSpecificEmployeeName($data['disapproved_by']));
		$list['disapproved_dt'] = date("F j, Y, g:i a", strtotime($data['disapproved_dt']));
		$list['disapproved_remarks'] = $data['disapproved_remarks'];
		$list['cancelled_by'] = utf8_encode($this->getSpecificEmployeeName($data['cancelled_by']));
		$list['cancelled_dt'] = date("F j, Y, g:i a", strtotime($data['cancelled_dt']));
		$list['cancelled_remarks'] = $data['cancelled_remarks'];
		$list['hr_noted_by'] = utf8_encode($this->getSpecificEmployeeName($data['hr_noted_by']));
		$list['hr_noted_dt'] = date("F j, Y, g:i a", strtotime($data['hr_noted_dt']));
		$list['hr_noted_remarks'] = $data['hr_noted_remarks'];
		$list['hr_noted_pay'] = $data['hr_noted_pay'];

		$date_from = $data['date_from'];
		$date_to = $data['date_to'];
		$list['duration'] = $this->getDuration($data['type'],$date_from,$date_to);

		if ($data['type'] == '1') { // Undertime --------------------------------
			$list['type'] = 'Undertime';
			$list['date_time_formatted'] = date($datetime_format, strtotime($date_from))." - ".date($datetime_format, strtotime($date_to));
			$list['date_time_raw'] = $date_from." - ".$date_to;
		} else if ($data['type'] == '2') { // Half Day --------------------------------
			$list['type'] = 'Half Day';
			$list['date_time_formatted'] = date($datetime_format, strtotime($date_from))." - ".date($datetime_format, strtotime($date_to));
			$list['date_time_raw'] = $date_from." - ".$date_to;
		} else if ($data['type'] == '3') { // Whole Day --------------------------------
			$list['type'] = 'Whole Day';
			$list['date_time_formatted'] = date('m/d/Y', strtotime($date_from))." 00:00:00 - ".$date_to;
			$list['date_time_raw'] = $date_from." - ".$date_to;
		} else if ($data['type'] == '4') { // Others --------------------------------
			$list['type'] = 'Others';
			$list['date_time_formatted'] = date($datetime_format, strtotime($date_from))." - ".date($datetime_format, strtotime($date_to));
			$list['date_time_raw'] = $date_from." - ".$date_to;
		}

		array_push($response['data_array'], $list);
		echo json_encode($response);
	}

	public function approve_loa(){
		$conn = $this->conn('gcceforms');

		date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');

        $loa_id = $_POST['loa_id'];
        $employee_id = $_POST['employee_id'];
        $approved_remarks = $_POST['remarks'];
        $status = 'Approved';
        $approved_by = $employee_id;
        $approved_dt = $date;

        $sth = $conn->prepare("UPDATE gcceforms.loa SET `approved_by`=:approved_by,`status`=:status,`approved_dt`=:approved_dt,`approved_remarks`=:approved_remarks WHERE id=:loa_id");
        $sth->bindParam(':loa_id', $loa_id);
        $sth->bindParam(':status', $status);
        $sth->bindParam(':approved_dt', $approved_dt);
        $sth->bindParam(':approved_by', $approved_by);
        $sth->bindParam(':approved_remarks', $approved_remarks);
        $sth->execute();

     	$response['response_array'] = array();

        if ($sth) {
        	$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function disapprove_loa(){
		$conn = $this->conn('gcceforms');

		date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');

        $loa_id = $_POST['loa_id'];
        $employee_id = $_POST['employee_id'];
        $disapproved_remarks = $_POST['remarks'];
        $status = 'Disapproved';
        $disapproved_by = $employee_id;
        $disapproved_dt = $date;

        $sth = $conn->prepare("UPDATE gcceforms.loa SET `disapproved_by`=:disapproved_by,`status`=:status,`disapproved_dt`=:disapproved_dt,`disapproved_remarks`=:disapproved_remarks WHERE id=:loa_id");
        $sth->bindParam(':loa_id', $loa_id);
        $sth->bindParam(':status', $status);
        $sth->bindParam(':disapproved_dt', $disapproved_dt);
        $sth->bindParam(':disapproved_by', $disapproved_by);
        $sth->bindParam(':disapproved_remarks', $disapproved_remarks);
        $sth->execute();

     	$response['response_array'] = array();

        if ($sth) {
        	$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function cancel_loa(){
		$conn = $this->conn('gcceforms');

		date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');

        $loa_id = $_POST['loa_id'];
        $employee_id = $_POST['employee_id'];
        $cancelled_remarks = $_POST['remarks'];
        $status = 'Cancelled';
        $cancelled_by = $employee_id;
        $cancelled_dt = $date;

        $sth = $conn->prepare("UPDATE gcceforms.loa SET `cancelled_by`=:cancelled_by,`status`=:status,`cancelled_dt`=:cancelled_dt,`cancelled_remarks`=:cancelled_remarks WHERE id=:loa_id");
        $sth->bindParam(':loa_id', $loa_id);
        $sth->bindParam(':status', $status);
        $sth->bindParam(':cancelled_dt', $cancelled_dt);
        $sth->bindParam(':cancelled_by', $cancelled_by);
        $sth->bindParam(':cancelled_remarks', $cancelled_remarks);
        $sth->execute();

     	$response['response_array'] = array();

        if ($sth) {
        	$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function undo_cancel_loa(){
		$conn = $this->conn('gcceforms');

        $loa_id = $_POST['loa_id'];
        $status = 'Pending';
        $cancelled_by = '';
        $cancelled_dt = '';
        $cancelled_remarks = '';

        $sth = $conn->prepare("UPDATE gcceforms.loa SET `cancelled_by`=:cancelled_by,`status`=:status,`cancelled_dt`=:cancelled_dt,`cancelled_remarks`=:cancelled_remarks WHERE id=:loa_id");
        $sth->bindParam(':loa_id', $loa_id);
        $sth->bindParam(':status', $status);
        $sth->bindParam(':cancelled_dt', $cancelled_dt);
        $sth->bindParam(':cancelled_by', $cancelled_by);
        $sth->bindParam(':cancelled_remarks', $cancelled_remarks);
        $sth->execute();

     	$response['response_array'] = array();

        if ($sth) {
        	$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	} 

	public function undo_disapprove_loa(){
		$conn = $this->conn('gcceforms');

        $loa_id = $_POST['loa_id'];
        $status = 'Pending';
        $disapproved_by = '';
        $disapproved_dt = '';
        $disapproved_remarks = '';

        $sth = $conn->prepare("UPDATE gcceforms.loa SET `disapproved_by`=:disapproved_by,`status`=:status,`disapproved_dt`=:disapproved_dt,`disapproved_remarks`=:disapproved_remarks WHERE id=:loa_id");
        $sth->bindParam(':loa_id', $loa_id);
        $sth->bindParam(':status', $status);
        $sth->bindParam(':disapproved_dt', $disapproved_dt);
        $sth->bindParam(':disapproved_by', $disapproved_by);
        $sth->bindParam(':disapproved_remarks', $disapproved_remarks);
        $sth->execute();

     	$response['response_array'] = array();

        if ($sth) {
        	$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function undo_approve_loa(){
		$conn = $this->conn('gcceforms');

        $loa_id = $_POST['loa_id'];
        $status = 'Pending';
        $approved_by = '';
        $approved_dt = '';
        $approved_remarks = '';

        $sth = $conn->prepare("UPDATE gcceforms.loa SET `approved_by`=:approved_by,`status`=:status,`approved_dt`=:approved_dt,`approved_remarks`=:approved_remarks WHERE id=:loa_id");
        $sth->bindParam(':loa_id', $loa_id);
        $sth->bindParam(':status', $status);
        $sth->bindParam(':approved_dt', $approved_dt);
        $sth->bindParam(':approved_by', $approved_by);
        $sth->bindParam(':approved_remarks', $approved_remarks);
        $sth->execute();

     	$response['response_array'] = array();

        if ($sth) {
        	$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function undo_note_loa(){
		$conn = $this->conn('gcceforms');

        $loa_id = $_POST['loa_id'];
        $status = 'Approved';
        $hr_noted_by = '';
        $hr_noted_dt = '';
        $hr_noted_remarks = '';
        $hr_noted_pay = '';

        $sth = $conn->prepare("UPDATE gcceforms.loa SET `hr_noted_by`=:hr_noted_by,`status`=:status,`hr_noted_dt`=:hr_noted_dt,`hr_noted_remarks`=:hr_noted_remarks,`hr_noted_pay`=:hr_noted_pay WHERE id=:loa_id");
        $sth->bindParam(':loa_id', $loa_id);
        $sth->bindParam(':status', $status);
        $sth->bindParam(':hr_noted_dt', $hr_noted_dt);
        $sth->bindParam(':hr_noted_by', $hr_noted_by);
        $sth->bindParam(':hr_noted_remarks', $hr_noted_remarks);
        $sth->bindParam(':hr_noted_pay', $hr_noted_pay);
        $sth->execute();

     	$response['response_array'] = array();

        if ($sth) {
        	$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function note_loa(){
		$conn = $this->conn('gcceforms');

		date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');

        $loa_id = $_POST['loa_id'];
        $employee_id = $_POST['employee_id'];
        $hr_noted_remarks = $_POST['remarks'];
        $hr_noted_pay = $_POST['hr_noted_pay'];
        $status = 'HR Noted';
        $hr_noted_by = $employee_id;
        $hr_noted_dt = $date;

        $sth = $conn->prepare("UPDATE gcceforms.loa SET `hr_noted_by`=:hr_noted_by,`status`=:status,`hr_noted_dt`=:hr_noted_dt,`hr_noted_remarks`=:hr_noted_remarks,`hr_noted_pay`=:hr_noted_pay WHERE id=:loa_id");
        $sth->bindParam(':loa_id', $loa_id);
        $sth->bindParam(':status', $status);
        $sth->bindParam(':hr_noted_dt', $hr_noted_dt);
        $sth->bindParam(':hr_noted_by', $hr_noted_by);
        $sth->bindParam(':hr_noted_remarks', $hr_noted_remarks);
        $sth->bindParam(':hr_noted_pay', $hr_noted_pay);
        $sth->execute();

     	$response['response_array'] = array();

        if ($sth) {
        	$list['status'] = 'success';
        } else { 
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

    public function save_loa(){
		$conn = $this->conn('gcceforms');

		date_default_timezone_set('Asia/Singapore');
    	$date = date('Y-m-d H:i:s');

	    $ref_yr = substr($date, 2, 2);
	   	$ref_month = substr($date, 5, 2);
	   	$ref_series = $this->getSeries($ref_month,$ref_yr);
	   	$reference_no = 'LOA' . $ref_yr . '-' . $ref_month . '-' . $ref_series;

	   	$name = $_POST['name'];
	   	$employee = $_POST['employee'];
	   	$company = $_POST['company'];
	   	$department = $_POST['department'];
	   	$position = $_POST['position'];
	   	$nature = $_POST['nature'];
	   	$address = $_POST['address'];
	   	$reason = $_POST['reason'];
	   	$phone = $_POST['phone'];
	   	$date_from = $_POST['date_from'];
	   	$date_to = $_POST['date_to'];
	   	$status = 'Pending';
	   	$type = $_POST['type'];
	   	$created_by = $_POST['created_by']; 
	   	$created_dt = $date;
	   	$selectedDropDownPicker = $_POST['selectedDropDownPicker'];
	   	$_department_id = $_POST['_department_id'];

	   	if ($type == '1') {
	   		$from = $date_from;
	   		$to = explode(" ",$date_from)[0].' '.$date_to;
	   	} else if ($type == '2') {

	   		if ($selectedDropDownPicker == 'AM') {
	   			$from = $date_from.' 08:01:00';
	   			$to = $date_from.' 12:00:00';
	   		} else {
	   			$from = $date_from.' 13:01:00';
	   			$to = $date_from.' 17:00:00';
	   		}
	   		
	   	} else if ($type == '3') {
	   		$from = $date_from.' 00:00:00';
	   		$to = '0000-00-00 00:00:00';
	   	} else if ($type == '4') {
	   		$from = $date_from;
	   		$to = $date_to;
	   	}

	   	$sth = $conn->prepare("INSERT INTO gcceforms.loa(`ref_yr`, `ref_series`, `ref_month`, `reference_no`, `employee`, `company`, `department`, `position`, `nature`, `address`, `reason`, `phone`, `date_from`, `date_to`, `status`, `type`, `created_by`, `created_dt`) VALUES (:ref_yr,:ref_series,:ref_month,:reference_no,:employee,:company,:department,:position,:nature,:address,:reason,:phone,:from_,:to_,:status,:type,:created_by,:created_dt)");
		$sth->bindParam(':ref_yr', $ref_yr);
		$sth->bindParam(':ref_series', $ref_series);
		$sth->bindParam(':ref_month', $ref_month);
		$sth->bindParam(':reference_no', $reference_no);
		$sth->bindParam(':employee', $employee);
		$sth->bindParam(':company', $company);
		$sth->bindParam(':department', $department);
		$sth->bindParam(':position', $position);
		$sth->bindParam(':nature', $nature);
		$sth->bindParam(':address', $address);
		$sth->bindParam(':reason', $reason);
		$sth->bindParam(':phone', $phone);
		$sth->bindParam(':from_', $from);
		$sth->bindParam(':to_', $to);
		$sth->bindParam(':status', $status);
		$sth->bindParam(':type', $type);
		$sth->bindParam(':created_by', $created_by);
		$sth->bindParam(':created_dt', $created_dt);
   		$sth->execute();

	   	$response['array_response'] = array();

	   	if ($sth) {
	   		$last_id = $conn->lastInsertId();
	   		// $this->setNotification($_department_id, $name, $nature, $last_id, $reference_no, $created_by);
	   		$list_['status'] = 'success';
	   	} else {
	   		$list_['status'] = 'Something Went Wrong...';
	   	} 

		array_push($response['array_response'], $list_);
	   	echo json_encode($response);
    }

    public function update_loa(){
    	$conn = $this->conn('gcceforms');

    	date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');

        $loa_id = $_POST['loa_id'];
    	$employee = $_POST['employee'];
    	$company = $_POST['company'];
    	$department = $_POST['department'];
    	$position = $_POST['position'];
    	$nature = $_POST['nature'];
    	$address = $_POST['address'];
    	$reason = $_POST['reason'];
    	$phone = $_POST['phone'];
    	$date_from = $_POST['date_from'];
    	$date_to = $_POST['date_to'];
    	$type = $_POST['type'];
    	$selectedDropDownPicker = $_POST['selectedDropDownPicker'];
    	$last_edited_by = $_POST['last_edited_by'];

    	if ($type == '1') {
	   		$from = $date_from;
	   		$to = explode(" ",$date_from)[0].' '.$date_to;
	   	} else if ($type == '2') {

	   		if ($selectedDropDownPicker == 'AM') {
	   			$from = $date_from.' 08:01:00';
	   			$to = $date_from.' 12:00:00';
	   		} else {
	   			$from = $date_from.' 13:01:00';
	   			$to = $date_from.' 17:00:00';
	   		}
	   		
	   	} else if ($type == '3') {
	   		$from = $date_from.' 00:00:00';
	   		$to = '0000-00-00 00:00:00';
	   	} else if ($type == '4') {
	   		$from = $date_from;
	   		$to = $date_to;
	   	}

    	$sth = $conn->prepare("UPDATE gcceforms.loa SET `employee`=:employee,`company`=:company,`department`=:department,`position`=:position,`nature`=:nature,`address`=:address,`reason`=:reason,`phone`=:phone,`date_from`=:date_from,`date_to`=:date_to,`type`=:type,`last_edited_by`=:last_edited_by,`last_edited_dt`=:last_edited_dt WHERE id=:loa_id");
   		$sth->bindParam(':employee', $employee);
   		$sth->bindParam(':company', $company);
   		$sth->bindParam(':department', $department);
   		$sth->bindParam(':position', $position);
   		$sth->bindParam(':nature', $nature);
   		$sth->bindParam(':address', $address);
   		$sth->bindParam(':reason', $reason);
   		$sth->bindParam(':phone', $phone);
   		$sth->bindParam(':date_from', $from);
   		$sth->bindParam(':date_to', $to);
   		$sth->bindParam(':type', $type);
   		$sth->bindParam(':last_edited_by', $last_edited_by);
   		$sth->bindParam(':last_edited_dt', $date);
   		$sth->bindParam(':loa_id', $loa_id);
   		$sth->execute();

 		$response['array_response'] = array();

    	if ($sth) {
    		$list['status'] = 'success'; 
    	} else {
			$list['status'] = 'failed';
    	}

		array_push($response['array_response'], $list);
    	echo json_encode($response);
    }

    private function getSpecificEmployeeName($created_by){
   		$conn = $this->conn();
        $sth = $conn->prepare("SELECT firstname,middlename,lastname FROM tblemployees WHERE id = :created_by");
        $sth->bindParam(":created_by", $created_by);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        return $data['firstname'].' '.($data['middlename'] ? $data['middlename'][0].'.' : '').' '.$data['lastname'];
	}

    private function getDuration($type, $date_from, $date_to){

		if ($type == '1') { // Undertime --------------------------------

			$start_date = new DateTime($date_from); // test 2016-07-14 10:00:00
			$since_start = $start_date->diff(new DateTime($date_to)); // test 2016-07-15 16:00:00

			$hrs = '';
			if ($since_start->h > 0) {

				$hrs_name = ' hour';
				if ($since_start->h > 1) {
					$hrs_name = ' hours';
				}
				$hrs = $since_start->h.$hrs_name;
			}

			$min = '';
			if ($since_start->i > 0) {
				$min = $since_start->i.' min';
			}

			$duration = $hrs.' '.$min;

		} else if ($type == '2') { // Half Day --------------------------------
			$duration = '4 hours';
		} else if ($type == '3') { // Whole Day --------------------------------
			$duration = '8 hours';
		} else if ($type == '4') { // Others --------------------------------
	
			$start_date = new DateTime($date_from);
			$since_start = $start_date->diff(new DateTime($date_to));

			$hrs = '';
			if ($since_start->h > 0) {

				$hrs_name = ' hour';
				if ($since_start->h > 1) {
					$hrs_name = ' hours';
				}
				$hrs = $since_start->h.$hrs_name;
			}

			$min = '';
			if ($since_start->i > 0) {
				$min = $since_start->i.'min';
			}

			$hrs_name = ' day';
			if ($since_start->d > 1) {
				$hrs_name = ' days';
			}
			$day = $since_start->d.$hrs_name;

			$duration = $day.' '.$hrs.' '.$min;
		}
		return $duration;
	}

    private function getSeries($month,$year){
    	$conn = $this->conn('gcceforms');

    	$list = array();

    	$sth = $conn->prepare("SELECT ref_series from gcceforms.loa where ref_yr=:year and ref_month=:month order by ref_series asc");
		$sth->bindParam(':year', $year);
		$sth->bindParam(':month', $month);
   		$sth->execute();

   		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			array_push($list, $row['ref_series']);
   		}

   		if (sizeof($list) > 0) {
	        foreach ($list as $arr) {
	            $x = $arr;
	        }
	        $series = intval($x) + 1;
	        if (strlen($series) == 1) {
	            $series = '000' . $series;
	        } else if (strlen($series) == 2) {
	            $series = '00' . $series;
	        } else if (strlen($series) == 3) {
	            $series = '0' . $series;
	        } else {
	            $series = $series;
	        }
	    } else {
	        $series = '0001';
	    }

	    return $series;
    }

    function getDisplayName($arrData = array()){
	    if ($arrData) {
	        $lastname = $arrData["lastname"];
	        $firstname = $arrData["firstname"];
	        $middlename = strtoupper($arrData["middlename"]);
	        $suffix = strtoupper($arrData["suffix"]);

	        $nSuffix = "";
	        $nMiddleName = "";

	        if ($suffix !== "" && ($suffix !== "N/A" && $suffix !== "NONE")) {
	            $nSuffix = $suffix;
	        }
	        if ($middlename !== "" && ($middlename !== "N/A" && $middlename !== "NONE")) {
	            $nMiddleName = $middlename;
	        }

	        $displayName1 = "";
	        $displayName2 = "";

	        $nMiddleName = trim($nMiddleName);
	        $nMiddleName = substr($nMiddleName, 0, 1);
	        $nMiddleName = ($nMiddleName) ? "{$nMiddleName}." : "";
	        if ($nMiddleName && $nSuffix) {
	            $displayName1 = "{$lastname}, {$firstname} {$nMiddleName} {$nSuffix}";
	            $displayName2 = "{$firstname} {$nMiddleName} {$lastname} {$nSuffix}";
	        } else if ($nSuffix) {
	            $displayName1 = "{$lastname}, {$firstname} {$nSuffix}";
	            $displayName2 = "{$firstname} {$lastname} {$nSuffix}";
	        } else if ($nMiddleName) {
	            $displayName1 = "{$lastname}, {$firstname} {$nMiddleName}";
	            $displayName2 = "{$firstname} {$nMiddleName} {$lastname}";
	        } else {
	            $displayName1 = "{$lastname}, {$firstname}";
	            $displayName2 = "{$firstname} {$lastname}";
	        }

	        $displayName1 = strtoupper($displayName1);
	        $displayName2 = strtoupper($displayName2);

	        $data = array();
	        $data["display_name_0"] = $displayName1;
	        $data["display_name_1"] = $displayName2;

	        return $data;
	    } else {
	        return false;
	    }
    }

} // gcceformsloaModel

?>