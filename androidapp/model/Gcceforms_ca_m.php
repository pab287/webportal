<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Gcceforms_ca_m extends Dbase{

    //123

    public function get_ca_data(){
        $conn = $this->conn();
        $date= date("Y-m-d", strtotime("-1 year", time()));

        $response['ca_array'] = array();
    	$user_id = $_POST['user_id'];
    	$isView_own_request = $_POST['isView_own_request'];
		$dept_id = $_POST['dept_id'];

    	if ($isView_own_request=='view_own_request') {
    		$own_request = "and a.employee='$user_id'";
    	}else if($isView_own_request=='view_by_dept'){
			$own_request = "and b.department_id = '$dept_id'";
		}else {
    		$own_request = "";
    	}

		// if($isView_by_dept == 'true' && $user_id != 1){
		// 	$dept_request = "and b.department_id = '$dept_id'";
		// }else{
		// 	$dept_request = "";
		// }

        $sth = $conn->query("SELECT a.id, a.employee, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt, b.department_id
        					from gcceforms.cash_advance a
        					left join gccmaster.tblemployees b
        					on a.employee = b.id
        					where a.status !='Cancelled' and a.created_dt >='$date' $own_request order by a.id desc");

        					// where a.status !='Cancelled' and a.created_dt >='$date' $own_request $dept_request order by a.id desc");

        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        	
            $fullname = $this->getDisplayName($row);
            $tempFullname = (object) $fullname;
            $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
            $employee_name = utf8_encode($display_name);

            $list['id'] = $row['id'];
            $list['status'] = $row['status'];
            $list['employee'] = $row['employee'];
            $list['reference_no'] = $row['reference_no'];
        	$list['employee_name'] = $employee_name;
        	$list['amt_applied'] = number_format($row['amt_applied'], 2);
        	$list['amt_approved'] = number_format($row['amt_approved'], 2);
        	$list['purpose'] = $row['purpose'];
        	$list['created_dt'] = date('m/d/Y g:i A',strtotime($row['created_dt']));
        	$list['approved_dt'] = date('m/d/Y g:i A',strtotime($row['approved_dt']));
        	array_push($response['ca_array'], $list);
        }

        echo json_encode($response);
    }


    public function get_employee_data(){
		$conn = $this->conn();

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

	    echo json_encode($response);
	}

	function getCompany($conn,$id){
		$sth = $conn->query("SELECT id,description from gcchris.tblcompanies where id='$id' or description='$id'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data;
	}

	function getDepartment($conn,$id){
		$sth = $conn->query("SELECT id,description from gcchris.tbldepartments where id='$id' or description='$id' or code='$id'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data;
	}

	function getPosition($conn,$id){
		$sth = $conn->query("SELECT id,name from gcchris.tblposition where id='$id' or name='$id'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data;
	}

	public function view_ca_details(){
		$conn = $this->conn("gccmaster");

		$response['response_array'] = array();

		$ca_id = $_POST['ca_id'];

		// $sth = $conn->query("SELECT DISTINCT a.*, e.firstname,e.middlename, e.lastname, e.suffix, e.bday, f.filename, e.date_start,
		// 						e.company_id AS emp_company, c.description AS _company, 
		// 						e.department_id AS emp_department, d.description AS _department, 
		// 						e.position AS emp_position, p.name AS _position
		// 					 FROM gcceforms.cash_advance a
		// 					 LEFT JOIN gccmaster.tblemployees e
		// 					 	ON a.employee=e.id
		// 					 LEFT JOIN gcchris.tblcompanies c
		// 					 	ON c.id = e.company_id OR c.description = e.company_id OR c.code = e.company_id
		// 					 LEFT JOIN gcchris.tbldepartments d
		// 					 	ON e.department_id=d.code OR e.department_id=d.id OR d.description = e.department_id
		// 					 LEFT JOIN gcchris.tblposition p
		// 					 	ON e.position=p.name OR p.id = e.position
		// 					 LEFT JOIN gcceforms.ca_attachments f
		// 					 	ON a.id=f.ca_id
		// 					 WHERE a.id='$ca_id'");
		$sth = $conn->query("SELECT DISTINCT a.*, e.firstname,e.middlename, e.lastname, e.suffix, e.bday, e.date_start,
								e.company_id AS emp_company, c.description AS _company, 
								e.department_id AS emp_department, d.description AS _department, 
								e.position AS emp_position, p.name AS _position
							 FROM gcceforms.cash_advance a
							 LEFT JOIN gccmaster.tblemployees e
							 	ON a.employee=e.id
							 LEFT JOIN gcchris.tblcompanies c
							 	ON c.id = e.company_id OR c.description = e.company_id OR c.code = e.company_id
							 LEFT JOIN gcchris.tbldepartments d
							 	ON e.department_id=d.code OR e.department_id=d.id OR d.description = e.department_id
							 LEFT JOIN gcchris.tblposition p
							 	ON e.position=p.name OR p.id = e.position
							 WHERE a.id='$ca_id'");
		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

			$fullname = $this->getDisplayName($row);
            $tempFullname = (object) $fullname;
            $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
            $employee_name = utf8_encode($display_name);

            $company = is_numeric($row['emp_company']) ? $row['_company'] : $row['emp_company'];
			$department = is_numeric($row['emp_department']) ? $row['_department'] : $row['emp_department'];
			$position = is_numeric($row['emp_position']) ? $row['_position'] : $row['emp_position'];

			$recommend_dt = date('F j, Y g:i a', strtotime($row['recommend_dt']));
			$approved_dt = date('F j, Y g:i a', strtotime($row['approved_dt']));
			$cancelled_dt = date('F j, Y g:i a', strtotime($row['cancelled_dt']));
			$created_dt = date('F j, Y g:i a', strtotime($row['created_dt']));
			$disapproved_dt = date('F j, Y g:i a', strtotime($row['disapproved_dt']));

			$employee_id = $row['employee'];

			$list['id'] = $row['id'];
			$list['employee_id'] = $employee_id;
			// $list['filename'] = $row['filename'];

			$list['ca_id'] = $ca_id;
			$list['filename'] = $this->getAttachments($ca_id);
			$list['date_start'] = date('F j, Y', strtotime($row['date_start'])); 
			$list['emp_status'] = $row['emp_status'];
			$list['created_dt'] = $created_dt;
            $list['created_by'] = is_numeric($row['created_by']) ? $this->getUserName($row['created_by']) : $row['created_by'];
            $list['reference_no'] = $row['reference_no'];
			$list['bday'] = date('F j, Y', strtotime($row['bday']));
			$list['employee_name'] = $employee_name;
			$list['company'] = $company."\n".$department."\n".$position;
			$list['amt_applied'] = $row['amt_applied'];
			$list['amt_approved'] = $row['amt_approved'];
			// $list['amt_applied'] = '₱ '.number_format($row['amt_applied'], 0);
			// $list['amt_approved'] = '₱ '.number_format($row['amt_approved'], 0);
			$list['deduct_type'] = $row['deduct_type'];
			$list['amt_to_b_deducted'] = $row['deduct_type']=='percentage' ? $row['amt_to_b_deducted'].'%' : '₱ '.$row['amt_to_b_deducted'];
			$list['purpose'] = $row['purpose'];
			$list['last_vale'] = $this->lastVale($employee_id, $ca_id, $row['created_dt']);
			$list['status'] = $row['status'];
			$list['recommend_remark2'] = $row['recommend_remark2'];
			$list['recommend_dt'] = $recommend_dt;
			$list['recommend_by'] = is_numeric($row['recommend_by']) ? $this->getUserName($row['recommend_by']) : $row['recommend_by'];
			$list['recommend_remarks'] = $row['recommend_remarks'] ? '₱ '.number_format($row['recommend_remarks'], 0) : $row['recommend_remarks'];

			$list['hr_bal_remarks'] = $row['hr_bal_remarks'] ? '₱ '.number_format($row['hr_bal_remarks'], 0) : $row['hr_bal_remarks'];
			$list['hr_bal_by'] = is_numeric($row['hr_bal_by']) ? $this->getUserName($row['hr_bal_by']) : $row['hr_bal_by'];
			$list['hr_bal_dt'] = date('F j, Y g:i a', strtotime($row['hr_bal_dt']));
			$list['hr_remarks'] = $row['hr_remarks'];

			$list['for_posting_by'] = is_numeric($row['for_posting_by']) ? $this->getUserName($row['for_posting_by']) : $row['for_posting_by'];
			$list['for_posting_dt'] = $row['for_posting_dt'] ? date('F j, Y g:i a', strtotime($row['for_posting_dt'])) : $row['for_posting_dt'];
			$list['for_posting_remarks'] = $row['for_posting_remarks'];

			$list['posted_by'] = is_numeric($row['posted_by']) ? $this->getUserName($row['posted_by']) : $row['posted_by'];
			$list['posted_dt'] = $row ['posted_dt'] ? date('F j, Y g:i a', strtotime($row['posted_dt'])) : $row['posted_dt'];
			$list['posted_remarks'] = $row['posted_remarks'];

			$list['undo_awaiting_approval_by'] = is_numeric($row['undo_awaiting_approval_by']) ? $this->getUserName($row['undo_awaiting_approval_by']) : $row['undo_awaiting_approval_by'];
			$list['undo_awaiting_approval_dt'] = $row ['undo_awaiting_approval_dt'] ? date('F j, Y g:i a', strtotime($row['undo_awaiting_approval_dt'])) : $row['undo_awaiting_approval_dt'];
			$list['undo_awaiting_approval_remarks'] = $row['undo_awaiting_approval_remarks'];

			$list['final_approved_by'] = is_numeric($row['final_approved_by']) ? $this->getUserName($row['final_approved_by']) : $row['final_approved_by'];
			$list['final_approved_dt'] = $row ['final_approved_dt'] ? date('F j, Y g:i a', strtotime($row['final_approved_dt'])) : $row['final_approved_dt'];
			$list['final_approved_remarks'] = $row['final_approved_remarks'];

			$list['acctg_bal_by'] = is_numeric($row['acctg_bal_by']) ? $this->getUserName($row['acctg_bal_by']) : $row['acctg_bal_by'];
			$list['acctg_bal_dt'] = date('F j, Y g:i a', strtotime($row['acctg_bal_dt']));
			$list['acctg_bal_remarks'] = $row['acctg_bal_remarks'] ? '₱ '.number_format($row['acctg_bal_remarks'], 0) : $row['acctg_bal_remarks'];
			$list['acctg_bal_remarks2'] = $row['acctg_bal_remarks2'];
			$list['approved_by'] = is_numeric($row['approved_by']) ? $this->getUserName($row['approved_by']) : $row['approved_by'];
			$list['approved_remarks'] = $row['approved_remarks'];
			$list['approved_dt'] = $approved_dt;
			$list['disapproved_by'] = is_numeric($row['disapproved_by']) ? $this->getUserName($row['disapproved_by']) : $row['disapproved_by'];
			$list['disapproved_dt'] = $disapproved_dt;
			$list['disapproved_remarks'] = $row['disapproved_remarks'];
			$list['cancelled_by'] = is_numeric($row['cancelled_by']) ? $this->getUserName($row['cancelled_by']) : $row['cancelled_by'];
			$list['cancelled_dt'] = $cancelled_dt; 
			$list['cancelled_remarks'] = $row['cancelled_remarks'];
			$list['last_edited_by'] = is_numeric($row['last_edited_by']) ? $this->getUserName($row['last_edited_by']) : '';
			$list['last_edited_dt'] = date('F j, Y g:i a', strtotime($row['last_edited_dt']));

			$list['charges'] = $this->getCharges($ca_id);
			$list['acctg_ca_pending'] = $row['acctg_ca_pending'];
			$list['acctg_ca_interest'] = $row['acctg_ca_interest'];
			$list['acctg_sss_loan'] = $row['acctg_sss_loan'];
			$list['acctg_hdmf_loan'] = $row['acctg_hdmf_loan'];
			$list['acctg_outside_loan'] = $row['acctg_outside_loan'];

			$list['acctg_ca_interest_percentage'] = $row['acctg_ca_interest_percentage'];
			array_push($response['response_array'], $list);
		}

	    echo json_encode($response);
	}

	function getAttachments($id){
		$conn = $this->conn();
		$data = array();

		$sth = $conn->query("SELECT filename FROM gcceforms.ca_attachments WHERE ca_id = '$id'");

		$query = $sth->fetchAll(PDO::FETCH_ASSOC);

		foreach($query as $key => $rs){
			array_push($data, $rs['filename']);
		}
		return $data;
	}

	function lastVale($emp,$id,$date){
		$conn = $this->conn();
		$date = date("Y-m-d H:i:s", strtotime($date));
		$data = array();
		$sth = $conn->query("SELECT id,amt_approved FROM gcceforms.cash_advance a where a.employee='$emp' and a.id!='$id' and a.created_dt<'$date' and a.status='Approved' order by a.id desc");
		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			$list = array();
			$list[] = $row['amt_approved'];
			$list[] = $row['id'];
			$data[] = $list;
		}

		if(count($data)>0){
            return $data[0][0];
        }else{
            return 0;
        }
	}

	// public function save_cash_advance(){
	// 	$conn = $this->conn();

	// 	$response['response_array'] = array();

	// 	$date = date('Y-m-d H:i:s');
    //     $year = date('y');
    //     $month = date('m');
    //     $ref_series = $this->getSeries($year,$month);

    //     $reference = 'CA'.$year.'-'.$month.'-'.$ref_series;
    //     $company = $_POST['company'];
    //     $department = $_POST['department'];
    //     $position = $_POST['position'];
    //     $purpose = trim($_POST['purpose']);
    //     // $purpose = mysql_real_escape_string($_POST['purpose']);
    //     $amt_applied = $_POST['amt_applied'];
    //     $amt_to_b_deducted = $_POST['amt_to_b_deducted'];
    //     $deduct_type = $_POST['deduct_type'];
    //     $user_id = $_POST['user_id'];
    //     $employee = $_POST['employee'];
    //     $emp_status = $this->getEmployeeStatus($employee);
    //     $emp_idno = $this->getEmployeeNumber($employee);
    //     $date_employed = $this->getEmployeeDateEmployed($employee);
    //     $created_by = $user_id; // $this->getUserName($user_id)
    //     $created_dt = $date;
    //     $status = 'HR Recommendation Pending';
    //     $amt_approved = '0';
	// 	$uploadPath = $_POST['uploadPath'];
	// 	$email_template_filepath = $_POST['email_template_filepath'];
	// 	$emailSender = json_decode($_POST['emailSender'], true);

    //     $sth = $conn->query("INSERT INTO gcceforms.cash_advance (ref_yr,ref_series,ref_month,reference_no,status,employee,company,department,position,emp_status,emp_idno,date_employed,purpose,amt_applied,amt_approved,amt_to_b_deducted,deduct_type,created_by,created_dt) VALUES ('$year','$ref_series','$month','$reference','$status','$employee','$company','$department','$position','$emp_status','$emp_idno','$date_employed','$purpose','$amt_applied','$amt_approved','$amt_to_b_deducted','$deduct_type','$created_by','$created_dt')");

    //     if ($sth) {

	// 		if (isset($_FILES['file'])) {
	//         	$last_id = $conn->lastInsertId();
	// 			if ($last_id) {
	// 	        	$_temp = $this->uploadFile($last_id, $_FILES['file'], $uploadPath);
	// 	        }else{
	// 				$_temp = 'last id not found';
	// 			}
	// 		}else{
	// 			$_temp = 'no file found';
	// 		}

	// 		// Email sending
	//     	$emp_name = $this->getUserName($employee);
	//     	$recipient = $this->getSupervisorEmail($department);
	//     	if ($recipient) {
	// 	    	$send = $this->email_send($emailSender,$reference,$emp_name,$amt_applied,$purpose,$recipient,$email_template_filepath);
	// 	    	if ($send) {
	// 	    		$list['email_send_status'] = 'success';
	// 	    	} else {
	// 				$list['email_send_status'] = 'error';
	// 	    	}
	//     	}

	// 		$list['status'] = 'success';
	// 	} else {
	// 		$list['status'] = 'error';
	// 	}

	// 	array_push($response['response_array'], $list);

	// 	echo json_encode($response);
	// }

	// public function save_cash_advancev1(){
	// 	$conn = $this->conn();

	// 	$response['response_array'] = array();

	// 	$date = date('Y-m-d H:i:s');
    //     $year = date('y');
    //     $month = date('m');
    //     $ref_series = $this->getSeries($year,$month);

    //     $reference = 'CA'.$year.'-'.$month.'-'.$ref_series;
    //     $company = $_POST['company'];
    //     $department = $_POST['department'];
    //     $position = $_POST['position'];
    //     // $purpose = mysql_real_escape_string($_POST['purpose']);
    //     $purpose = $_POST['purpose'];
    //     $amt_applied = $_POST['amt_applied'];
    //     $amt_to_b_deducted = $_POST['amt_to_b_deducted'];
    //     $deduct_type = $_POST['deduct_type'];
    //     $user_id = $_POST['user_id'];
    //     $employee = $_POST['employee'];
    //     $emp_status = $this->getEmployeeStatus($employee);
    //     $emp_idno = $this->getEmployeeNumber($employee);
    //     $date_employed = $this->getEmployeeDateEmployed($employee);
    //     $created_by = $user_id; // $this->getUserName($user_id)
    //     $created_dt = $date;
    //     $status = 'HR Recommendation Pending';
    //     $amt_approved = '0';
	// 	// $uploadPath = $_POST['uploadPath'];
	// 	// $email_template_filepath = $_POST['email_template_filepath'];
	// 	// $emailSender = json_decode($_POST['emailSender'], true);

		
	// 	$sth = $conn->query("INSERT INTO gcceforms.cash_advance (ref_yr,ref_series,ref_month,reference_no,status,employee,company,department,position,emp_status,emp_idno,date_employed,purpose,amt_applied,amt_approved,amt_to_b_deducted,deduct_type,created_by,created_dt) VALUES ('$year','$ref_series','$month','$reference','$status','$employee','$company','$department','$position','$emp_status','$emp_idno','$date_employed','$purpose','$amt_applied','$amt_approved','$amt_to_b_deducted','$deduct_type','$created_by','$created_dt')");

	// 	if ($sth) {
	// 		$last_id = $conn->lastInsertId();

	// 		if(isset($_FILES) && $_FILES) {
	// 			$uploadPath = '../devapp/uploads';
	// 			foreach($_FILES as $key => $rs){
	// 				$uploadPath = '../devapp/uploads/empcode_'.$employee;
	// 				$_temp = $this->uploadFilev1($last_id, $_FILES[$key], $uploadPath);

	// 				if($_temp){
	// 					$response['upload_status'] = 'uploaded';
	// 				}else{
	// 					$response['upload_status'] = 'not uploaded';
	// 				}
	// 			}
	// 		}else{
	// 			$response['upload_status'] = 'No File Found';
	// 		}

	// 		// $emp_name = $this->getUserName($employee);
	//     	// $recipient = $this->getSupervisorEmail($department);
	//     	// if ($recipient) {
	// 	    // 	$send = $this->email_send($emailSender,$reference,$emp_name,$amt_applied,$purpose,$recipient,$email_template_filepath);
	// 	    // 	if ($send) {
	// 	    // 		$list['email_send_status'] = 'success';
	// 	    // 	} else {
	// 		// 		$list['email_send_status'] = 'error';
	// 	    // 	}
	//     	// }
	// 		$list['status'] = 'success';
	// 	}else{
	// 		$list['status'] = 'error';
	// 	}

	// 	array_push($response['response_array'], $list);

    //     echo json_encode($response);
	// }



	public function baseurl($url){
		$isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
		return $isSecure . $_SERVER['SERVER_NAME'] . "/web/$url/";
	}


	public function recommend_update(){
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');

		$response['response_array'] = array();

		$ca_id = $_POST['ca_id'];
		$user_id = $_POST['user_id'];
		$amt_applied = $_POST['amt_applied'];
		$recommend_remark2 = trim($_POST['recommend_remark2']);
		$recommend_by = $user_id;
		$recommend_dt = $date;
		$status = 'Accounting Balance Pending';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET recommend_by='$recommend_by', recommend_dt='$recommend_dt', amt_applied='$amt_applied', recommend_remark2='$recommend_remark2', status='$status' WHERE id='$ca_id'");

		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function undo_recommend_update(){
		$conn = $this->conn();

		$response['response_array'] = array();

		$ca_id = $_POST['ca_id'];
		$status = 'HR Recommendation Pending';
		$sth = $conn->query("UPDATE gcceforms.cash_advance SET recommend_by='', recommend_dt='', recommend_remark2='', status='$status' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function disapprove_update(){
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');

		$response['response_array'] = array();

		$user_id = $_POST['user_id'];
		$ca_id = $_POST['ca_id'];
		$disapproved_remarks = $_POST['disapproved_remarks'];
		$disapproved_by = $user_id;
		$disapproved_dt = $date;
		$status = 'Disapproved';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET disapproved_by='$disapproved_by', disapproved_dt='$disapproved_dt', disapproved_remarks='$disapproved_remarks', status='$status' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function undo_disapprove_update(){
		$conn = $this->conn();

		$response['response_array'] = array();

		if($_POST['acctg_bal_by']){
            $status_data = "Awaiting Approval";
        }else{
            $status_data = "HR Recommendation Pending";
        }

		$ca_id = $_POST['ca_id'];
		$disapproved_by = '';
		$disapproved_dt = '';
		$disapproved_remarks = '';
		$status = $status_data;

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET disapproved_by='$disapproved_by', disapproved_dt='$disapproved_dt', disapproved_remarks='$disapproved_remarks', status='$status' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function cancel_update(){
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');

		$response['response_array'] = array();

		$ca_id = $_POST['ca_id'];
		$user_id = $_POST['user_id'];
		$cancelled_remarks = $_POST['cancelled_remarks'];
		$cancelled_by = $user_id;
		$cancelled_dt = $date;
		$status = 'Cancelled';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET cancelled_by='$cancelled_by', cancelled_remarks='$cancelled_remarks', cancelled_dt='$cancelled_dt', status='$status' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function set_hr_update(){
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');

		$response['response_array'] = array();

		$ca_id = $_POST['ca_id'];
		$user_id = $_POST['user_id'];
		$recommend_remarks = $_POST['recommend_remarks'];
		$hr_bal_remarks = $_POST['hr_bal_remarks'];
		$hr_remarks = trim($_POST['hr_remarks']);
		// $hr_remarks = mysql_real_escape_string($_POST['hr_remarks']);
		$hr_bal_by = $user_id;
		$hr_bal_dt = $date;
		$status = 'Accounting Balance Pending';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET recommend_remarks='$recommend_remarks', hr_bal_by='$hr_bal_by', hr_bal_dt='$hr_bal_dt', hr_bal_remarks='$hr_bal_remarks',
			hr_remarks='$hr_remarks', status='$status' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function set_acctg_update(){
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');

		$response['response_array'] = array();

		$acctg_bal_remarks = $_POST['acctg_bal_remarks'];
		$acctg_bal_status = $_POST['set_acctg_status'];

		if($acctg_bal_status == "Payroll Balance Pending"){
			$final_remarks = 0.00;
		} else {
			$final_remarks = $acctg_bal_remarks;
		}

		$ca_id = $_POST['ca_id'];
		$user_id = $_POST['user_id'];
		// $acctg_bal_remarks2 = mysql_real_escape_string($_POST['acctg_bal_remarks2']);
		$acctg_bal_remarks2 = trim($_POST['acctg_bal_remarks2']);
		$reference_no = $_POST['reference_no'];
		$purpose = trim($_POST['purpose']);
		// $purpose = mysql_real_escape_string($_POST['purpose']);
		$amt_applied = $_POST['amt_applied'];
		$employee_name = $_POST['employee_name'];
		$acctg_bal_by = $user_id;
		$acctg_bal_dt = $date;
		$acctg_bal_remarks = $final_remarks;
		$status = $acctg_bal_status;
		$emailSender = json_decode($_POST['emailSender'], true);

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET acctg_bal_by='$acctg_bal_by', acctg_bal_dt='$acctg_bal_dt', acctg_bal_remarks='$acctg_bal_remarks', acctg_bal_remarks2='$acctg_bal_remarks2', status='$status' where id='$ca_id'");
		if ($sth) {

			$list['status'] = 'success';

			$email_template_filepath = $_POST['email_template_filepath'];

			if($acctg_bal_status == "Payroll Balance Pending" OR $acctg_bal_status == "HR Balance Pending"){
				$payrollEmail = $this->getPayrollEmail();
				if ($payrollEmail) {
					$send = $this->email_send($emailSender,$reference_no,$employee_name,$amt_applied,$purpose,$payrollEmail,$email_template_filepath);
			    	if ($send) {
			    		$list['email_send_status'] = 'success';
			    	} else {
						$list['email_send_status'] = 'error';
			    	}
				}
			} else {
				$HrEmail = $this->getHREmail();
				if ($HrEmail) {
					$send = $this->email_send($emailSender,$reference_no,$employee_name,$amt_applied,$purpose,$HrEmail,$email_template_filepath,false);
			    	if ($send) {
			    		$list['email_send_status'] = 'success';
			    	} else {
						$list['email_send_status'] = 'error';
			    	}
				}
			}

		} else {
			$list['status'] = 'failed';
		}

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function undo_approval_update(){
		$conn = $this->conn();

		$response['response_array'] = array();

		$ca_id = $_POST['ca_id'];
		$approved_by = '';
		$approved_dt = '';
		$amt_approved = '';
		$approved_remarks = '';
		$status = 'Awaiting Approval';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET approved_by='$approved_by', approved_dt='$approved_dt', amt_approved='$amt_approved', approved_remarks='$approved_remarks', status='$status' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function approve_update(){
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');

		$response['response_array'] = array();

		$ca_id = $_POST['ca_id'];
		$user_id = $_POST['user_id'];
		$amt_approved = $_POST['amt_approved'];
		$approved_remarks = trim($_POST['approved_remarks']);
		// $approved_remarks = mysql_real_escape_string($_POST['approved_remarks']);
		$approved_by = $user_id;
		$approved_dt = $date;
		$status = 'Approved';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET amt_approved='$amt_approved', approved_remarks='$approved_remarks', approved_by='$approved_by', approved_dt='$approved_dt', status='$status' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	function getSupervisorEmail($department){
		$conn = $this->conn();

		$sth = $conn->query("SELECT DISTINCT u.email,d.head from gcchris.tbldepartments as d 
							 left join gccmaster.tblusers as u
							 on d.head_id = u.emp_id
							 where d.id='$department' or d.description='$department' or d.code='$department'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data['email'];
	}

	function getPayrollEmail(){
		$conn = $this->conn();

		$sth = $conn->query("SELECT email,username from gccmaster.tblusers where is_suspended='0' AND email='hrpayroll@gccph.com'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data['email'];
	}

	function getHREmail(){
		$conn = $this->conn();

		$sth = $conn->query("SELECT email,username from gccmaster.tblusers where is_suspended='0' AND email='hr@gccph.com'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data['email'];
	}

	function getEmailModule($module = null){
		if ($module) {
			$conn = $this->conn();
			$sth = $conn->query("SELECT * from gccmaster.email_template where name='$module' and send_email='1'");
			if ($sth->rowCount() == 1) {
				return $sth->fetch();
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	public function set_intrst_prcntge(){
		$conn = $this->conn();
		$date = date('Y-m-d H:i:s');
		$user_id = $_POST['user_id'];
		$set_interest_percentage = $_POST['set_interest_percentage'];
		$ca_id = $_POST['ca_id']; 
		$response['response_array'] = array();
		$acctg_bal_by = $user_id;
		$acctg_bal_dt = $date;
		// $acctg_ca_interest_percentage = $interest_percentage; 
		$sth = $conn->query("UPDATE gcceforms.cash_advance SET acctg_bal_by='$acctg_bal_by', acctg_bal_dt='$acctg_bal_dt', last_edited_by='$user_id',last_edited_dt='$date', acctg_ca_interest_percentage='$set_interest_percentage' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
 
        array_push($response['response_array'], $list);
        echo json_encode($response);
	}

	public function set_acct_balance(){
		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);
		$conn = $this->conn();
		$date = date('Y-m-d H:i:s');
		$response['response_array'] = array();
		$ca_id = $_POST['ca_id'];
		$user_id = $_POST['user_id'];
		$acctg_bal_remarks2 = trim($_POST['acctg_bal_remarks2']);
		// $acctg_bal_remarks2 = mysql_real_escape_string($_POST['acctg_bal_remarks2']);
		// $acctg_bal_remarks = $_POST['acctg_bal_remarks'];
		$acctg_bal_status = $_POST['set_acctg_status'];
		$acctg_ca_pending = $_POST['acctg_ca_pending'];
		$acctg_ca_interest = $_POST['acctg_ca_interest'];
		$acctg_sss_loan = $_POST['acctg_sss_loan'];
		$acctg_hdmf_loan = $_POST['acctg_hdmf_loan'];
		$acctg_outside_loan = $_POST['acctg_outside_loan'];

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET acctg_bal_by='$user_id', last_edited_by='$user_id',last_edited_dt='$date', acctg_bal_dt='$date', acctg_bal_remarks2='$acctg_bal_remarks2', acctg_ca_pending='$acctg_ca_pending', acctg_ca_interest='$acctg_ca_interest',acctg_sss_loan='$acctg_sss_loan', acctg_hdmf_loan='$acctg_hdmf_loan',acctg_outside_loan='$acctg_outside_loan',  status='$acctg_bal_status' where id='$ca_id'");

		if ($sth) {

			$checkCharges = $conn->query("SELECT * FROM gcceforms.ca_charges WHERE ca_id='$ca_id'");
			$checkCharges->execute([$ca_id]);
			$rowCount = $checkCharges->rowCount();
			if ($rowCount > 0) {
				$sth_delete = $conn->query("DELETE FROM gcceforms.ca_charges WHERE ca_id='$ca_id'");
				if($sth_delete){
					foreach ($_POST as $key => $value) {
						if (preg_match('/^description(\d+)$/', $key, $matches)) {
							$index = $matches[1]; 
							$label = $value;
							$amount = $_POST["amount$index"];	
							$sth_oc = $conn->query("INSERT INTO gcceforms.ca_charges (ca_id, description, amount) 
                                        VALUES ('$ca_id', '$label', '$amount')");
						} 
					} 
					$list['status'] = 'success';			
				}

			}else{
				foreach ($_POST as $key => $value) {
					if (preg_match('/^description(\d+)$/', $key, $matches)) {
						$index = $matches[1]; 
						$label = $value;
						$amount = $_POST["amount$index"];		
						$sth_oc = $conn->query("INSERT INTO gcceforms.ca_charges (ca_id, description, amount) 
							VALUES ('$ca_id', '$label', '$amount')
							ON DUPLICATE KEY UPDATE description='$label', amount='$amount'");
							
					}
				}
				foreach ($_POST as $key => $value) {
					if (preg_match('/^description(\d+)$/', $key, $matches)) {
						$index = $matches[1]; 
						$label = $value;
						$amount = $_POST["amount$index"];		
						$sth_oc = $conn->query("INSERT INTO gcceforms.ca_charges_logs (ca_id, description, amount) 
							VALUES ('$ca_id', '$label', '$amount')
							ON DUPLICATE KEY UPDATE description='$label', amount='$amount'");
							
					}
				}
				$list['status'] = 'success';

			}

			

			
		} else {
			$list['status'] = 'failed';
		} 

		array_push($response['response_array'], $list);
		echo json_encode($response);
	}


	public function save_cash_advancev1(){
		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);

		$conn = $this->conn();
		$response['response_array'] = array();
		$date = date('Y-m-d H:i:s');
        $year = date('y');
        $month = date('m');
        $ref_series = $this->getSeries($year,$month);

        $reference = 'CA'.$year.'-'.$month.'-'.$ref_series;
        $company = $_POST['company'];
        $department = $_POST['department'];
        $position = $_POST['position'];
		$purpose = trim($_POST['purpose']);
        $amt_applied = $_POST['amt_applied'];
        $amt_to_b_deducted = $_POST['amt_to_b_deducted'];
        $deduct_type = $_POST['deduct_type'];
        $user_id = $_POST['user_id'];
        $employee = $_POST['employee'];
        $emp_status = $this->getEmployeeStatus($employee);
        $emp_idno = $this->getEmployeeNumber($employee);
        $date_employed = $this->getEmployeeDateEmployed($employee);
        $created_by = $user_id;  
        $created_dt = $date;
        $status = 'HR Recommendation Pending';
        $amt_approved = '0';
 
		$sth = $conn->query("INSERT INTO gcceforms.cash_advance (ref_yr,ref_series,ref_month,reference_no,status,employee,company,department,position,emp_status,emp_idno,date_employed,purpose,amt_applied,amt_approved,amt_to_b_deducted,deduct_type,created_by,created_dt) VALUES ('$year','$ref_series','$month','$reference','$status','$employee','$company','$department','$position','$emp_status','$emp_idno','$date_employed','$purpose','$amt_applied','$amt_approved','$amt_to_b_deducted','$deduct_type','$created_by','$created_dt')");

		if ($sth) {
			$last_id = $conn->lastInsertId();
				if(isset($_FILES) && $_FILES) {
					foreach($_FILES as $key => $rs){
						$uploadPath = '../uploads/files/cash_advance/empcode_'.$employee;
						if (!file_exists($uploadPath)) {
							mkdir($uploadPath, 0777, true);
						}
						$_temp = $this->uploadFilev1($last_id, $_FILES[$key], $uploadPath);
						if($_temp){
							$response['upload_status'] = 'uploaded';
						}else{
							$nstate = false;
							for($ii = 0; $ii<=5; $ii++){
								$ntemp = $this->uploadFilev1($last_id, $rs, $uploadPath);
								if($ntemp){
									$nstate = true;
									break;
								}
							}
							$response['upload_status'] = $nstate ? 'uploaded': 'not uploaded';
						}
					}
				}else{
					$response['upload_status'] = 'No File Found';
				}
			$list['status'] = 'success';
		}else{
			$list['status'] = 'error';
		}

		array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	function uploadFilev1($ca_id, $file, $uploadPath, $id = ''){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		if (!file_exists($uploadPath)) {
			mkdir($uploadPath, 0777, true);
		}

		$realpath = realpath($uploadPath);
 
		if(file_exists($realpath)){
			$moveFile = $realpath.'/'.basename($file['name']);
			if (move_uploaded_file($file["tmp_name"], $moveFile)) {
				$this->saveFile($ca_id, $file['name']);

				if ($file['type'] == 'image/jpeg') { 
					$this->createThumbnail($realpath . '/', $file);
				}
	
				// Check and update file existence
				if ($this->checkFileExist($ca_id)) {
					$this->updateFile($id, $file['name']);
				}

				return true;
			} else {
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function update_cash_advance(){

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$conn = $this->conn();
		$date = date('Y-m-d H:i:s');
		$response['response_array'] = array();

		$user_id = $_POST['user_id'];
		$ca_id = $_POST['ca_id'];
		$company = $_POST['company'];
		$employee = $_POST['employee'];
		$department = $_POST['department'];
		$position = $_POST['position'];
		$purpose = trim($_POST['purpose']);
		$amt_applied = $_POST['amt_applied'];
		$amt_to_b_deducted = $_POST['amt_to_b_deducted'];
		$deduct_type = $_POST['deduct_type'];
		$last_edited_by = $user_id;
		$last_edited_dt = $date;
		$last_id = $conn->lastInsertId();

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET company='$company', employee='$employee', department='$department', position='$position', purpose='$purpose',
							amt_applied='$amt_applied', amt_to_b_deducted='$amt_to_b_deducted', deduct_type='$deduct_type', last_edited_by='$last_edited_by', last_edited_dt='$last_edited_dt' where id='$ca_id'");

			if ($sth) {

					$sth_delete = $conn->query("DELETE FROM gcceforms.ca_attachments WHERE ca_id='$ca_id'");

					if($sth_delete){

						if(isset($_FILES) && $_FILES) {
							// $uploadPath = '../devapp/uploads';
							foreach($_FILES as $key => $rs){
								$uploadPath = '../uploads/files/cash_advance/empcode_'.$employee;
								if (!file_exists($uploadPath)) {
									mkdir($uploadPath, 0777, true);
								}
								$_temp = $this->uploadFilev1($ca_id, $_FILES[$key], $uploadPath);
								if($_temp){
									$response['upload_status'] = 'uploaded';
								}else{
									$nstate = false;
									for($ii = 0; $ii<=5; $ii++){
										$ntemp = $this->uploadFilev1($last_id, $rs, $uploadPath);
										if($ntemp){
											$nstate = true;
											break;
										}
									}
									$response['upload_status'] = $nstate ? 'uploaded': 'not uploaded';
								}
							}
						}else{
							$response['upload_status'] = 'No File Found'; 
						}

						if(isset($_POST) && $_POST){
							foreach ($_POST as $key => $value) {
								if (preg_match('/^attacholdfile(\d+)$/', $key, $matches)) {
									$index = $matches[1];
									$label = $value;
									$sth = $conn->query("INSERT INTO gcceforms.ca_attachments (ca_id,filename) VALUES ('$ca_id','$label')");
									if ($sth) {
										$response['status_post'] = 'status_post_success';
									} else {
										$response['status_post'] = 'status_post_failed';
									} 
								}
							}
						}else{
							$response['status_post'] = 'No File Post';
						}


						$list['status'] = 'success';
					}else{
						$list['status'] = 'failed';
					}

			}else{
				$list['status'] = 'error';
			}
	
			array_push($response['response_array'], $list);
	
			echo json_encode($response);

	}

	public function undo_awaiting_approval(){
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');

		$response['response_array'] = array();

		$user_id = $_POST['user_id'];
		$ca_id = $_POST['ca_id'];
		$undo_awaiting_approval_remarks = $_POST['undo_awaiting_approval_remarks'];
		$undo_awaiting_approval_by = $user_id;
		$undo_awaiting_approval_dt = $date;
		$status = 'Accounting Balance Pending';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET last_edited_by='$undo_awaiting_approval_by',last_edited_dt='$undo_awaiting_approval_dt', undo_awaiting_approval_by='$undo_awaiting_approval_by', undo_awaiting_approval_dt='$undo_awaiting_approval_dt', undo_awaiting_approval_remarks='$undo_awaiting_approval_remarks', status='$status' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}
	public function undo_posting(){
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');

		$response['response_array'] = array();

		$user_id = $_POST['user_id'];
		$ca_id = $_POST['ca_id'];
		$for_posting_remarks = $_POST['for_posting_remarks'];
		$for_posting_by = $user_id;
		$for_posting_dt = $date;
		$status = 'Awaiting Approval';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET for_posting_by='$for_posting_by',  for_posting_dt='$for_posting_dt', for_posting_remarks='$for_posting_remarks', status='$status' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success'; 
        } else { 
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function for_posting(){
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');

		$response['response_array'] = array();
 
		$user_id = $_POST['user_id']; 
		$ca_id = $_POST['ca_id'];
		$for_posting_remarks = $_POST['for_posting_remarks'];
		$for_posting_by = $user_id;
		$for_posting_dt = $date;
		$status = 'For Posting';

		// $sth = $conn->query("UPDATE gcceforms.cash_advance SET last_edited_by='$for_posting_by',last_edited_dt='$for_posting_dt', for_posting_by='$for_posting_by', for_posting_dt='$for_posting_dt', status='$status' where id='$ca_id'");
		$sth = $conn->query("UPDATE gcceforms.cash_advance SET for_posting_by='$for_posting_by',  for_posting_dt='$for_posting_dt', for_posting_remarks='$for_posting_remarks', status='$status' where id='$ca_id'");
		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }
        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function update_posted(){
		$conn = $this->conn();
		$date = date('Y-m-d H:i:s');
		$response['response_array'] = array();
		$user_id = $_POST['user_id']; 
		$ca_id = $_POST['ca_id'];
		$posted_remarks = $_POST['posted_remarks']; 
		$employee = $_POST['employee_id'];
		$posted_by = $user_id;
		$posted_dt = $date;
		$status = 'Posted';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET last_edited_by='$posted_by',last_edited_dt='$posted_dt', posted_by='$posted_by', posted_dt='$posted_dt', posted_remarks='$posted_remarks', status='$status' where id='$ca_id'");

		if ($sth) {
				if(isset($_FILES) && $_FILES) {
					foreach($_FILES as $key => $rs){
						$uploadPath = '../uploads/files/cash_advance/empcode_'.$employee;
						if (!file_exists($uploadPath)) {
							mkdir($uploadPath, 0777, true);
						}
						$_temp = $this->uploadFilev1($ca_id, $_FILES[$key], $uploadPath);
						if($_temp){
							$response['upload_status'] = 'uploaded';
						}else{

							$nstate = false;
							for($ii = 0; $ii<=5; $ii++){
								$ntemp = $this->uploadFilev1($ca_id, $_FILES[$key], $uploadPath);
								if($ntemp){ 
									$nstate = true;
									break;
								}
							}
							$response['upload_status'] = $nstate ? 'uploaded': 'not uploaded';
						}
					}
				}else{
					$response['upload_status'] = 'No File Found'; 
				} 

			$list['status'] = 'success';
		}else{
			$list['status'] = 'error';
		}

        array_push($response['response_array'], $list);
        echo json_encode($response);
	}

	public function undo_posted(){
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');

		$response['response_array'] = array();
 
		$user_id = $_POST['user_id']; 
		$ca_id = $_POST['ca_id'];
		$posted_remarks = $_POST['posted_remarks'];
		$employee = $_POST['user_id'];
		$posted_by = $user_id;
		$posted_dt = $date;
		$status = 'For Posting';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET last_edited_by='$posted_by',last_edited_dt='$posted_dt', posted_by='$posted_by', posted_dt='$posted_dt', posted_remarks='$posted_remarks', status='$status' where id='$ca_id'");

			if($sth){
				$list['status'] = 'success';
			}else{
				$list['failed'] = 'failed';
			}
			
        array_push($response['response_array'], $list);
        echo json_encode($response);
	}

	public function for_final_approval(){
		$conn = $this->conn();
		$date = date('Y-m-d H:i:s');
		$response['response_array'] = array();
		$user_id = $_POST['user_id']; 
		$ca_id = $_POST['ca_id'];
		$final_approved_remarks = $_POST['posted_remarks']; 
		$employee = $_POST['employee_id'];
		$final_approved_by = $user_id;
		$final_approved_dt = $date;
		// $status = 'For Posting';
		$status = 'For Final Approval';

		$sth = $conn->query("UPDATE gcceforms.cash_advance SET last_edited_by='$final_approved_by',last_edited_dt='$final_approved_dt', final_approved_by='$final_approved_by', final_approved_dt='$final_approved_dt', final_approved_remarks='$final_approved_remarks', status='$status' where id='$ca_id'");

		if ($sth) {
				if(isset($_FILES) && $_FILES) {
					foreach($_FILES as $key => $rs){
						$uploadPath = '../uploads/files/cash_advance/empcode_'.$employee;
						if (!file_exists($uploadPath)) {
							mkdir($uploadPath, 0777, true);
						}
						$_temp = $this->uploadFilev1($ca_id, $_FILES[$key], $uploadPath);
						if($_temp){
							$response['upload_status'] = 'uploaded';
						}else{

							$nstate = false;
							for($ii = 0; $ii<=5; $ii++){
								$ntemp = $this->uploadFilev1($ca_id, $_FILES[$key], $uploadPath);
								if($ntemp){ 
									$nstate = true;
									break;
								}
							}
							$response['upload_status'] = $nstate ? 'uploaded': 'not uploaded';
						}
					}
				}else{
					$response['upload_status'] = 'No File Found'; 
				} 

			$list['status'] = 'success';
		}else{
			$list['status'] = 'error';
		}

        array_push($response['response_array'], $list);
        echo json_encode($response);
	}
	public function getCharges($id){
		$result = array();
		$conn = $this->conn();
		$q = "SELECT description, amount FROM gcceforms.ca_charges WHERE ca_id = '$id'";
		$sth = $conn->query($q);
		if($sth){
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		return $result;
	}

	function email_send($emailSender,$referenceNumber,$employee_name,$amount=0,$purpose="",$recipient,$email_template_filepath,$isNewSave=true){
		
		$email_title = "Cash Advance";

		if ($isNewSave) { // New 
			$module = "eforms_ca_new";
        	$content_title = "Cash Advance Application - {$referenceNumber}";

        	// Email template ----------
			ob_start(); 
			include($email_template_filepath.'email_ca_template.php');
			$content = ob_get_contents();
			ob_end_clean();
		} 
		else { // Approval
			$module = "eforms_ca_approval";
        	$content_title = "Cash Advance Approval - {$referenceNumber}";

        	// Email template ----------
			ob_start();
			include($email_template_filepath.'email_ca_approval_template.php');
			$content = ob_get_contents();
			ob_end_clean();
		}


        $emailTo = array();

		if($recipient){ $emailTo = is_array($recipient)? $recipient: array($recipient); }

		$overrideMailer = array();
        if($emailTo){ $overrideMailer["send_to"] = $emailTo; }


        $module_ = $this->getEmailModule($module);
        if ($module_) {

        	$sendTo = unserialize($module_['send_to']);
            $sendCc = unserialize($module_['cc_to']);
            $sendBcc = unserialize($module_['bcc_to']);

            $sendTo = (isset($overrideMailer["send_to"]) && $overrideMailer["send_to"]) ? $overrideMailer["send_to"] : $sendTo;
            $sendCc = (isset($overrideMailer["send_cc"]) && $overrideMailer["send_cc"]) ? $overrideMailer["send_cc"] : $sendCc;
            $sendBcc = (isset($overrideMailer["send_bcc"]) && $overrideMailer["send_bcc"]) ? $overrideMailer["send_bcc"] : $sendBcc;
            
            $sendToData = ($sendTo && is_array($sendTo)) ? implode(",", $sendTo) : "";
            $ccToData = ($sendCc && is_array($sendCc)) ? implode(",", $sendCc) : "";
            $bccToData = ($sendBcc && is_array($sendBcc)) ? implode(",", $sendBcc) : "";

            $sendToData = ($sendToData) ? $sendToData : "jp03@gccph.com";

            $send_email = $this->PHPMailer_($emailSender,$email_title,$content_title,$content,$sendToData,$ccToData,$bccToData);
            if ($send_email) {
            	return true;
            } else {
            	return false;
            }
        } else {
        	return false;
        }
	}

	function PHPMailer_($emailSender,$email_title,$content_title,$content,$recipient,$cc='',$bcc=''){

		$mail = new PHPMailer(true);

		try {
			// Sender info
			$mail->Username = $emailSender['email']; // gcceforms@gmail.com
			$mail->Password = $emailSender['password']; // Sc0t2366
			$mail->setFrom($emailSender['email'], $email_title);
			//$mail->addReplyTo('-----@gmail.com', 'AAA');
			 
			// Add a recipient
			$this->getRecipient($recipient,$mail);
			$this->getCC($cc,$mail);
			$this->getBCC($bcc,$mail);

			//$mail->SMTPDebug = SMTP::DEBUG_SERVER; // comment out this after you debug
		    $mail->isSMTP();
		    $mail->CharSet = "utf8";
			$mail->Host = $emailSender['host'];
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port = 587;
			$mail->SMTPOptions = array(
			    'ssl' => array(
			        'verify_peer' => false,
			        'verify_peer_name' => false,
			        'allow_self_signed' => true
			    )
			);

			$mail->isHTML(true);
			$mail->Subject = $content_title;
			$mail->Body    = $content; 
			 
			if($mail->send()) { 
				return true;
				//echo 'Message has been sent.';
			} else { 
				return false;
			    //echo 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
			} 

		} catch (Exception $e) {
			return false;
		    //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}

	function getRecipient($recipient,$mail){
		if ($recipient) {
			$str = explode(",", $recipient);
			if (count($str) > 1) {
				foreach ($str as $key => $recipient_) {
					$mail->addAddress($recipient_);
				}
			} else {
				$mail->addAddress($recipient);
			}
		}
	}

	function getCC($cc,$mail){
		if ($cc) {
			$str = explode(",", $cc);
			if (count($str) > 1) {
				foreach ($str as $key => $cc_) {
					$mail->addCC($cc_);
				}
			} else {
				$mail->addCC($cc);
			}
		}
	}

	function getBCC($bcc,$mail){
		if ($bcc) {
			$str = explode(",", $bcc);
			if (count($str) > 1) {
				foreach ($str as $key => $bcc_) {
					$mail->addBCC($bcc_);
				}
			} else {
				$mail->addBCC($bcc);
			}
		}
	}

	function replaceFile($conn,$ca_id,$uploadPath,$file){
		$sth = $conn->query("SELECT * from gcceforms.ca_attachments where ca_id='$ca_id'");
		$ca_attach = $sth->fetch(PDO::FETCH_ASSOC);
		if ($ca_attach) {
			$old_file = $uploadPath.$ca_attach['filename'];
			if (file_exists($old_file)) {
			    unlink($old_file); // delete old file
			}

			$old_img_thumb = $uploadPath.'thumbnails/'.$ca_attach['filename'];
			if (file_exists($old_img_thumb)) {
			    unlink($old_img_thumb); // delete old file thumbnail
			}
		}

		$this->uploadFile($ca_id,$file,$uploadPath,false,$ca_attach['id']);
	}

	function uploadFile($ca_id,$file,$uploadPath,$isSaveFile = true,$id = ''){
        if (!file_exists($uploadPath)) {
		    mkdir($uploadPath, 0777, true);
		}

		if (move_uploaded_file($file["tmp_name"], $uploadPath.basename($file["name"]))) {
			$this->saveFile($ca_id, $file['name']);
			// if ($this->checkFileExist($ca_id)) {
			// 	$this->saveFile($ca_id, $file['name']);
			// 	$this->updateFile($id, $file['name']);
			// } else {
			// 	$this->saveFile($ca_id, $file['name']);
			// }

			//$isSaveFile ? $this->saveFile($ca_id, $file['name']) : $this->updateFile($id, $file['name']);

			// create thumbnail if file is image
		    if ($file['type'] == 'image/jpeg') { 
		    	$this->createThumbnail($uploadPath,$file);
		    }
		} else {
		    //echo "Sorry, there was an error uploading your file.";
		}
	}

	function createThumbnail($uploadPath,$file){
		$thumbnail = $uploadPath.'thumbnails/';
		if (!file_exists($thumbnail)) { 
		    mkdir($thumbnail, 0777, true);
		}

		$orig_image = $uploadPath.basename($file["name"]); 
		$dest = $thumbnail.basename($file["name"]); 

		$this->make_thumb($orig_image, $dest,'80');

	}

	function make_thumb($src, $dest, $desired_width) {
		if (!file_exists($src)) {
			die("Error: File does not exist at path: $src");
		}
	
		$source_image = imagecreatefromjpeg($src);
		if (!$source_image) {
			die("Error: Unable to create image from $src. Possible reasons include invalid format or missing permissions.");
		}
	
		$exif = @exif_read_data($src);
		if (!empty($exif['Orientation'])) {
			switch ($exif['Orientation']) {
				case 8:
					$source_image = imagerotate($source_image, 90, 0);
					break;
				case 3:
					$source_image = imagerotate($source_image, 180, 0);
					break;
				case 6:
					$source_image = imagerotate($source_image, -90, 0);
					break;
			}
		}
	
		$width = imagesx($source_image);
		$height = imagesy($source_image);
	
		$desired_height = floor($height * ($desired_width / $width));
	
		$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
	
		imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
	
		if (!imagejpeg($virtual_image, $dest)) {
			die("Error: Unable to save thumbnail to $dest.");
		}
	
		imagedestroy($source_image);
		imagedestroy($virtual_image);
	}
	

	function checkFileExist($ca_id){
		$conn = $this->conn();
		$sth = $conn->query("SELECT id from gcceforms.ca_attachments where ca_id='$ca_id'");
		
		if ($sth) {
			$data = $sth->fetch(PDO::FETCH_ASSOC);
	
			if ($data) {
				return $data['id'];
			}
		}
		
		return false;
	}

	function saveFile($ca_id,$filename){
		$conn = $this->conn();
		$sth = $conn->query("INSERT INTO gcceforms.ca_attachments (ca_id,filename) VALUES ('$ca_id','$filename')");
		return $sth;
	}

	function updateFile($id,$filename){
		$conn = $this->conn();
		$sth = $conn->query("UPDATE gcceforms.ca_attachments SET filename='$filename' WHERE id='$id'");
		return $sth;
	}

	function getEmployeeStatus($employee){
		$conn = $this->conn();
		$sth = $conn->query("SELECT work_status from gccmaster.tblemployees where id='$employee'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data['work_status'];
	}

	function getEmployeeNumber($employee){
		$conn = $this->conn();
		$sth = $conn->query("SELECT idno from gccmaster.tblemployees where id='$employee'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data['idno'];
	}

	function getEmployeeDateEmployed($employee){
		$conn = $this->conn();
		$sth = $conn->query("SELECT date_start from gccmaster.tblemployees where id='$employee'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data['date_start'];
	}

	function getUserName($employee){
		$conn = $this->conn();
		$sth = $conn->query("SELECT firstname,middlename,lastname,suffix from gccmaster.tblemployees where id='$employee'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$fullname = $this->getDisplayName($data);
        $tempFullname = (object) $fullname;
        $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
        return utf8_encode($display_name);
	}

	function getSeries($year,$month){
		$conn = $this->conn();

		$list = array();

		$sth = $conn->query("SELECT ref_series from gcceforms.cash_advance where ref_yr='$year' and ref_month='$month' order by ref_series asc");
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
}

?>