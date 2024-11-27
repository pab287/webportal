<?php

class Travel_order_login_m extends Dbase{
    
	public function user_login(){
		$conn = $this->conn();

		$response = array();

		$username = $_POST['username'];
		$password = md5($_POST['password']);

		$sth = $conn->prepare("SELECT *, p.name as position_name, e.employee_status 
								FROM tblusers AS u
								LEFT JOIN tblemployees AS e 
									ON e.id = u.emp_id
								LEFT JOIN gcchris.tblposition AS p 
									ON p.id = e.position
								where u.username=:usrname and u.password=:pssword");
		$sth->bindParam(':usrname', $username);
		$sth->bindParam(':pssword', $password);
   		$sth->execute();

   		if($sth){
			$count = $sth->rowCount();
	   		if ($count > 0) {

	   			$row = $sth->fetch(PDO::FETCH_ASSOC);

	   			$response['status'] = 'success';
	   			$response['position'] = $row['position_name'];
	   			$response['employee_pic'] = $row['pic_filename'];
				$response['firstname'] = $row['firstname'];
				$response['middlename'] = $row['middlename'];
				$response['lastname'] = $row['lastname'];
				$response['employee_id'] = $row['emp_id'];
				$response['username'] = $row['username'];
				$response['email'] = $row['email'];
				$response['idno'] = $row['idno'];
				$response['biometricno'] = $row['biometricno'];
				$response['employee_status'] = $row['employee_status'];
	   		} else {
	   			$response['status'] = 'account_not_exist';
	   		}
   		} else {
			$response['status'] = 'error_query';
   		}
   		

   		return json_encode($response);
	}


}

?>