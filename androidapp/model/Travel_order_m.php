<?php

class Travel_order_m extends Dbase{
    
	// public function fetch_travel_order(){
		// 	$conn = $this->conn();
		// 	$current_date = date('Y-m-d');
		// 	$date = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));

		// 	$response = array();

		// 	$sth = $conn->prepare("SELECT a.*, b.destination, b.accomplished as destination_accomplished
		// 						   FROM gcceforms.travel_order a
		// 						   LEFT JOIN gcceforms.travel_destination b
		// 						   ON a.id = b.travel_order_id
		// 						   WHERE a.status != 'Cancelled' AND a.status = 'Approved' AND DATE(b.date_from) >= '$date'
		// 						   GROUP BY a.reference_no
		// 						   ORDER BY a.reference_no DESC");
		// 	$sth->execute();

		// 	if ($sth->rowCount() > 0) {

		// 		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

		//    			$list = array();
		//    			$list['id'] = $row['id'];
		//    			$list['reference_no'] = $row['reference_no'];
		//    			$list['accomplished'] = $row['accomplished'];
		//    			$list['company'] = $row['company'];
		//    			$list['department'] = $row['department'];
		//    			$list['status'] = $row['status'];
		//    			$list['type'] = $row['type'];
		//    			$list['station'] = $row['station'];
		//    			$list['is_commute'] = $row['is_commute'];
		//    			$list['is_personal'] = $row['is_personal'];
		//    			$list['is_service'] = $row['is_service'];
		//    			$list['is_hitch'] = $row['is_hitch'];
		//    			$list['is_others'] = $row['is_others'];
		//    			$list['created_by'] = $row['created_by'];
		//    			$list['last_edited_by'] = $row['last_edited_by'];
		//    			$list['approved_by'] = $row['approved_by'];
		//    			$list['personnelList'] = $this->getPersonnelList($row['id']);
		//    			$list['destinationList'] = $this->getDestinationList($row['id']);

		//    			if($row['is_service'] == "1" || $row['is_hitch'] == "1"){
		//    				$driver = $this->getEmployeeDetails($row['driver_id']);
		//    				if(count($driver) > 0){
		//    					$list['driver'] = $driver['firstname'].' '.$driver['lastname'];
		//    				} else {
		//    					$list['driver'] = "";
		//    				}

		//    				$vehicle = $this->getVehicleDetails($row['vehicle_id']);
		//    				if(count($vehicle) > 0){
		//    					$list['vehicle'] = $vehicle['plateno'].' '.$vehicle['name'];
		//    				} else {
		//    					$list['vehicle'] = "";
		//    				}
		//    			}

		// 			array_push($response, $list);
		// 		}
		// 	}
			

		// 	echo json_encode($response);
	// }

	public function fetch_travel_order(){
		$conn = $this->conn();
		$current_date = date('Y-m-d');
		$date = date('Y-m-d', strtotime('-6 months', strtotime($current_date)));
		$user_id = $_POST['user_id'];
		$isView_own_request = $_POST['isView_own_request'];
		
		if ($isView_own_request=='true' && $user_id!=1) {
    		$own_request = "and a.created_id='$user_id'";
    	} else{
    		$own_request = "";
    	}

 		$response = array();
		$sth = $conn->prepare("SELECT a.*, a.company,  a.id, a.reference_no,a.accomplished, a.company, a.status,a.created_by, 
		tod.destination, tod.purpose, tod.requested_by, tod.destination, tod.travel_order_id, a.created_dt, top.employee_id, toe.firstname, toe.lastname
		FROM gcceforms.travel_order a
		LEFT JOIN gcceforms.travel_destination tod ON tod.travel_order_id = a.id
		LEFT JOIN gcceforms.travel_personnel top ON top.travel_order_id = a.id
		LEFT JOIN gccmaster.tblemployees toe ON toe.id = top.employee_id
		WHERE a.status != 'Cancelled' AND DATE(a.created_dt) >= '$date' $own_request
		GROUP BY a.reference_no
		ORDER BY a.reference_no DESC");

   		$sth->execute();
   		if ($sth->rowCount() > 0) {
   			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	   			$list = array();
	   			$list['id'] = $row['id'];
	   			$list['requested_by'] = $row['requested_by'];
	   			$list['travel_order_id'] = $row['travel_order_id'];
	   			$list['purpose'] = $row['purpose'];
	   			$list['company'] = $row['company'];
	   			$list['created_dt'] = $row['created_dt'];
	   			$list['created_by'] = $row['created_by'];
	   			$list['destination'] = $this->getDestinations($row['travel_order_id']);
	   			$list['status'] = $row['status'];
	   			$list['reference_no'] = $row['reference_no'];
	   			$list['firstname'] = $row['firstname'];
	   			$list['lastname'] = $row['lastname'];
	   			$list['accomplished'] = $row['accomplished'];
				array_push($response, $list);
   			}
   		}
   		echo json_encode($response);
	}

	public function fetch_to_view(){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$conn = $this->conn();
		$current_date = date('Y-m-d');
		$to_id = $_POST['to_id'];

		// var_dump(to_id); 		-- WHERE a.status != 'Cancelled' AND DATE(a.created_dt) >= '$date' $own_request

		$response = array();
		$sth = $conn->query("SELECT a.station, a.type, a.company,a.accomplished, a.department, a.id, a.reference_no, a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks, a.accomplishment_dt, toe.firstname AS employee_firstname, toe.lastname AS employee_lastname, a.created_dt AS order_created_dt, a.created_by, a.disapproved_dt, a.disapproved_by, a.approved_remarks, a.approved_by, a.approved_dt, a.last_edited_by, a.last_edited_dt, top.employee_id, a.disapproved_remarks, a.cancelled_by, a.cancelled_dt, a.cancelled_remarks, a.accomplishment_remarks, a.created_id, a.last_edited_id, a.accomplished_by, a.unaccomplished_remarks, a.approved_recommend_by_id, a.approved_recommend_by, a.approved_recommend_remarks, a.approved_recommend_date, tod.date_from, tod.date_to, tod.date_special, tod.remarks AS tod_remarks, tod.travel_from, tod.travel_to, tod.coords_to, tod.travel_order_status, tod.travel_order_status_date, tod.accomplished AS tod_accomplished, tod.destination, tod.requested_by, tod.travel_order_id AS tod_travel_order_id
        FROM gcceforms.travel_order a
        LEFT JOIN gcceforms.travel_destination tod ON tod.travel_order_id = a.id
        LEFT JOIN gcceforms.travel_personnel top ON top.travel_order_id = a.id
        LEFT JOIN gccmaster.tblemployees toe ON toe.id = top.employee_id
        WHERE a.status != 'Cancelled' 
        AND a.id = '$to_id' 
        AND DATE(a.created_dt) >= '2024-06-19'  
        GROUP BY a.reference_no
        ORDER BY a.reference_no DESC
    ");

   		if ($sth->rowCount() > 0) {
   			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	   			$list = array();				   
				   $list['final_accomplished'] = $row['accomplished'];
				   $list['station'] = $row['station'];
				   $list['type'] = $row['type'];
				   $list['company'] = $row['company'];
				   $list['department'] = $row['department'];
				   $list['id'] = $row['id'];
				   $list['reference_no'] = $row['reference_no'];
				   $list['driver'] = $row['driver'];
				   $list['status'] = $row['status'];
				   $list['vehicle_id'] = $row['vehicle_id'];
				   $list['driver_id'] = $row['driver_id'];
				   $list['is_service'] = $row['is_service'];
				   $list['is_hitch'] = $row['is_hitch'];
				   $list['is_commute'] = $row['is_commute'];
				   $list['is_personal'] = $row['is_personal'];
				   $list['is_others'] = $row['is_others'];
				   $list['others_remarks'] = $row['others_remarks'];
				   $list['accomplishment_dt'] = $row['accomplishment_dt'];
				   $list['employee_firstname'] = $row['employee_firstname'];
				   $list['employee_lastname'] = $row['employee_lastname'];
				   $list['order_created_dt'] = $row['order_created_dt'];
				   $list['created_by'] = $row['created_by'];
				   $list['disapproved_dt'] = $row['disapproved_dt'];
				   $list['disapproved_by'] = $row['disapproved_by'];
				   $list['approved_remarks'] = $row['approved_remarks'];
				   $list['approved_by'] = $row['approved_by'];
				   $list['approved_dt'] = $row['approved_dt'];
				   $list['last_edited_by'] = $row['last_edited_by'];
				   $list['last_edited_dt'] = $row['last_edited_dt'];
				   $list['employee_id'] = $row['employee_id'];
				   $list['disapproved_remarks'] = $row['disapproved_remarks'];
				   $list['cancelled_by'] = $row['cancelled_by'];
				   $list['cancelled_dt'] = $row['cancelled_dt'];
				   $list['cancelled_remarks'] = $row['cancelled_remarks'];
				   $list['accomplishment_remarks'] = $row['accomplishment_remarks'];
				   $list['created_id'] = $row['created_id'];
				   $list['last_edited_id'] = $row['last_edited_id'];
				   $list['accomplished_by'] = $row['accomplished_by'];
				   $list['accomplished_by_name'] = $this->getempName($row['accomplished_by']);
				   $list['unaccomplished_remarks'] = $row['unaccomplished_remarks'];

				   $list['approved_recommend_by_id'] = $this->getempName($row['approved_recommend_by_id']);

				   $list['approved_recommend_by'] = $row['approved_recommend_by'];
				   $list['approved_recommend_remarks'] = $row['approved_recommend_remarks'];
				   $list['approved_recommend_date'] = $row['approved_recommend_date'];
				   $list['date_from'] = $row['date_from'];
				   $list['date_to'] = $row['date_to'];
				   $list['date_special'] = $row['date_special'];
				   $list['tod_remarks'] = $row['tod_remarks'];
				   $list['travel_from'] = $row['travel_from'];
				   $list['travel_to'] = $row['travel_to'];
				   $list['coords_to'] = $row['coords_to'];
				   $list['travel_order_status'] = $row['travel_order_status'];
				   $list['travel_order_status_date'] = $row['travel_order_status_date'];
				   $list['tod_accomplished'] = $row['tod_accomplished'];
				   $list['list_destination'] = $this->destinationViewList($row['tod_travel_order_id']);
				   $list['requested_by'] = $row['requested_by'];
				   $list['tod_travel_order_id'] = $row['tod_travel_order_id'];
				   $list['personnels'] = $this->personnelList($row['tod_travel_order_id']);
				   
				   $vehicle = $this->getVehicleDetails($row['vehicle_id']);
		   				if(count($vehicle) > 0){
		   					$list['vehicle'] = $vehicle['plateno'].' '.$vehicle['name'];
		   				} else {
		   					$list['vehicle'] = "";
		   				}
				array_push($response, $list);
   			}
   		}

   		echo json_encode($response);
		
	}

	function destinationViewList($id){
		$data = array();
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT id, destination, date_from, date_to, accomplished,travel_order_id,des_from,des_to,requested_by,purpose,distance_traveled,source_link,date_special,instructions,remarks,travel_from,travel_to,coords_from,coords_to,travel_order_status,travel_order_status_date
							   FROM gcceforms.travel_destination
							   WHERE travel_order_id = :id");
		$sth->bindParam(':id', $id, PDO::PARAM_INT);
		$sth->execute();
		if ($sth->rowCount() > 0) {
   			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$data[] = [
					"des_from" => $row["des_from"],
					"des_to" => $row["des_to"],
					"destination" => $row["destination"],
					"requested_by" => $this->getempName($row["requested_by"]),
					"purpose" => $row["purpose"],
					"distance_traveled" => $row["distance_traveled"],
					"source_link" => $row["source_link"],
					"date_from" => $row["date_from"],
					"date_to" => $row["date_to"],
					"date_special" => $row["date_special"],
					"instructions" => $row["instructions"],
					"remarks" => $row["remarks"],
					"travel_from" => $row["travel_from"],
					"travel_to" => $row["travel_to"],
					"coords_from" => $row["coords_from"],
					"coords_to" => $row["coords_to"],
					"travel_order_status" => $row["travel_order_status"],
					"travel_order_status_date" => $row["travel_order_status_date"],
					"accomplished" => $row["accomplished"],
					"id" => $row["id"]
				];

   			}
   		}
   		return $data;
	}

	function getDestinations($id){
		$data = array();
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT  destination FROM gcceforms.travel_destination WHERE travel_order_id = :id");
		$sth->bindParam(':id', $id, PDO::PARAM_INT);
		$sth->execute();
		if ($sth->rowCount() > 0) {
   			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$data[] = $row;
   			}
   		}
   		return $data;
	}


	function getempName($id){ 
		$data = [];
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT id, lastname, firstname, middlename
							   FROM gccmaster.tblemployees
							   WHERE id = :id");
		$sth->bindParam(':id', $id, PDO::PARAM_INT);
		$sth->execute();
		if($sth->rowCount() > 0){
			$row = $sth->fetch(PDO::FETCH_ASSOC);
			return  $row["firstname"] . ' ' . $row["middlename"] . ' ' . $row["lastname"];
		}
	}


	function getVehicleDetails($id){
		$data = array(); 
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT plateno, name, id
							   FROM gccasset.vehicles
							   WHERE id = '$id'");
   		$sth->execute();
   		if ($sth->rowCount() > 0) {
   			$row = $sth->fetch(PDO::FETCH_ASSOC);
   			$data["id"] = $row["id"];
   			$data["plateno"] = $row["plateno"];
   			$data["name"] = $row["name"];
   		}
   		return $data;
	}

	function personnelList($id){
		$data = [];  
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT employee_id, travel_order_id
							   FROM gcceforms.travel_personnel
							   WHERE travel_order_id = :id");
		$sth->bindParam(':id', $id, PDO::PARAM_INT);
		$sth->execute();
		if ($sth->rowCount() > 0) {
			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$employeeData = $this->getPersonnelNames($row["employee_id"]);
				$data[] = [
					"employee_id" => $employeeData["id"],
					"name" => $employeeData["name"],
					"position" => $employeeData["position"],
				];
			}
		} 
		return $data;  
	}


	public function to_recommend(){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');
		$to_id = $_POST['to_id'];
		$to_remarks = trim($_POST['remarks']);
		$to_status = $_POST['status'];
		$to_emp_id = $_POST['emp_id'];
		$to_emp_name = $this->getempName($_POST['emp_id']);

		$response['response_array'] = array();

		$sth = $conn->query("UPDATE gcceforms.travel_order SET status='$to_status',approved_recommend_by_id='$to_emp_id', approved_recommend_remarks='$to_remarks', approved_recommend_date='$date', approved_recommend_by ='$to_emp_name' WHERE id='$to_id'");

		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function to_undo_approval(){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');
		$to_id = $_POST['to_id'];
		$to_status = $_POST['status'];

		$response['response_array'] = array();

		$sth = $conn->query("UPDATE gcceforms.travel_order SET status='$to_status' WHERE id='$to_id'");

		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function to_undo_disapproval(){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL); 
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');
		$to_id = $_POST['to_id'];
		$to_status = $_POST['status'];

		$response['response_array'] = array();

		$sth = $conn->query("UPDATE gcceforms.travel_order SET status='$to_status' WHERE id='$to_id'");

		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}


	public function to_cancel(){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');
		$to_id = $_POST['to_id'];
		$to_status = $_POST['status'];
		$to_remarks = trim($_POST['remarks']);
		$cancelled_by = $this->getempName($_POST['user_id']);

		$response['response_array'] = array();

		$sth = $conn->query("UPDATE gcceforms.travel_order SET status='$to_status', cancelled_dt='$date', cancelled_by='$cancelled_by', cancelled_remarks='$to_remarks' WHERE id='$to_id'");

		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	public function to_disapprove(){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');
		$to_id = $_POST['to_id'];
		$to_status = $_POST['status'];
		$to_remarks = trim($_POST['remarks']);
		$disapproved_by = $this->getempName($_POST['user_id']);

		$response['response_array'] = array();

		$sth = $conn->query("UPDATE gcceforms.travel_order SET status='$to_status', disapproved_dt='$date', disapproved_by='$disapproved_by', disapproved_remarks='$to_remarks' WHERE id='$to_id'");

		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}
	public function to_undo_recommendation(){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$conn = $this->conn();

		$date = date('Y-m-d H:i:s');
		$to_id = $_POST['to_id'];
		$to_status = $_POST['status'];

		$response['response_array'] = array();

		$sth = $conn->query("UPDATE gcceforms.travel_order SET status='$to_status' WHERE id='$to_id'");

		if ($sth) {
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);

        echo json_encode($response);
	}

	function getPersonnelNames($id){ 
		$data = [];
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT id, lastname, firstname, position
							   FROM gccmaster.tblemployees
							   WHERE id = :id");
		$sth->bindParam(':id', $id, PDO::PARAM_INT);
		$sth->execute();
		if($sth->rowCount() > 0){
			$row = $sth->fetch(PDO::FETCH_ASSOC);
			$data["id"] = $row["id"];
			$data["name"] = $row["firstname"] . ' ' . $row["lastname"];
			$data["position"] = $this->getPosition($row["position"]);
		}
		return $data;
	}


	// public function update_destination_status(){
	// 	$conn = $this->conn();
	// 	$response = array();

	// 	$id = $_POST["id"];
	// 	$created_by = $_POST["created_by"];
	// 	$destination_id = $_POST["destination_id"];
	// 	$destination = $_POST["destination"];
	// 	$reference_no = $_POST["reference_no"];
	// 	$driver = $_POST["driver"];
	// 	$vehicle = $_POST["vehicle"];

	// 	$sth = $conn->prepare("UPDATE gcceforms.travel_destination SET accomplished='1' WHERE id='$destination_id'");
   	// 	$sth->execute();

   	// 	if($sth){
   	// 		$response['status'] = true;
   	// 		$response['message'] = 'Successfully saved!';
	// 		$this->accomplishTravelOrder($id);
   	// 		$this->sendTelegramToPersonnelHead($id, $reference_no, $destination, $driver, $vehicle);
   	// 	} else {
   	// 		$response['status'] = false;
   	// 		$response['message'] = 'Query error!';
   	// 	}

	// 	return json_encode($response);
	// }

	function accomplishTravelOrder($id){
		$date = date('Y-m-d H:i:s');
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT id
							   FROM gcceforms.travel_destination
							   WHERE travel_order_id = '$id' AND accomplished = '0'");
		$sth->execute();
		if($sth->rowCount() === 0){
			$sth = $conn->prepare("UPDATE gcceforms.travel_order SET accomplished='1', accomplishment_dt='$date' WHERE id='$id'");
   			$sth->execute();
		}
	}

	function getDestinationList($id){
		$data = array();
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT id, destination, date_from, date_to, accomplished
							   FROM gcceforms.travel_destination
							   WHERE travel_order_id = '$id'");
   		$sth->execute();
   		if ($sth->rowCount() > 0) {
   			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
   				$list = array();
	   			$list["id"] = $row["id"];
	   			$list["destination"] = $row["destination"];
	   			$list["date_from"] = $row["date_from"];
	   			$list["date_to"] = $row["date_to"];
	   			$list["accomplished"] = $row["accomplished"];
				array_push($data, $list);
   			}
   		}
   		return json_encode($data);
	}

	function getPersonnelList($id){
		$data = array();
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT b.firstname, b.lastname, b.middlename, b.suffix, b.id as id, b.position, b.department_id
							   FROM gcceforms.travel_personnel a
							   LEFT JOIN gccmaster.tblemployees b
							   ON b.id = a.employee_id
							   WHERE a.travel_order_id = '$id'");
   		$sth->execute();
   		if ($sth->rowCount() > 0) {
   			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
   				$list = array();
	   			$list["id"] = $row["id"];
	   			$list["department_id"] = $row["department_id"];
	   			$list["personnel"] = $row["firstname"]. ' ' .$row["lastname"];
   				$list["position"] = is_numeric($row["position"]) ? $this->getPosition($row["position"]) : $row["position"];
				array_push($data, $list);
   			}
   		}
   		return json_encode($data);
	}

	function getEmployeeDetails($id){
		$data = array();
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT firstname, middlename, lastname, id
							   FROM gccmaster.tblemployees
							   WHERE id = '$id'");
   		$sth->execute();
   		if ($sth->rowCount() > 0) {
   			$row = $sth->fetch(PDO::FETCH_ASSOC);
   			$data["id"] = $row["id"];
   			$data["middlename"] = $row["middlename"];
   			$data["lastname"] = $row["lastname"];
   			$data["firstname"] = $row["firstname"];
   		}
   		return $data;
	}

	function listPersonels($id){
		$data = array();
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT firstname, middlename, lastname, id
							   FROM gccmaster.tblemployees
							   WHERE id = '$id'");
   		$sth->execute();
   		if ($sth->rowCount() > 0) {
   			$row = $sth->fetch(PDO::FETCH_ASSOC);
   			$data["id"] = $row["id"];
   			$data["middlename"] = $row["middlename"];
   			$data["lastname"] = $row["lastname"];
   			$data["firstname"] = $row["firstname"];
   		}
   		return $data;
	}

	function getPosition($id){
		$data = array();
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT name
							   FROM gcchris.tblposition
							   WHERE id = '$id'");
   		$sth->execute();
   		$row = $sth->fetch(PDO::FETCH_ASSOC);
   		return isset($row["name"]) ? $row["name"] : "";
	}


	public function test(){
		return $this->accomplishTravelOrder('42104');
	}


	public function to_accomplish(){
		$conn = $this->conn();
		$date_accomplish = $_POST['date'];
		$date = date('Y-m-d H:i:s');
		$remarks = trim($_POST['remarks']);
		// $emp_id = $this->getempName($_POST['emp_id']);
		$emp_id = $_POST['emp_id'];
		$status = $_POST['status'];
		$to_id = $_POST['to_id'];
		$accomlish_id = $_POST['accomplishID'];
		$response = array();
		$list = array();
		$sth = $conn->query("UPDATE gcceforms.travel_order SET accomplishment_dt='$date', unaccomplished_remarks='$remarks' WHERE id='$to_id'");
		if($sth){
			$sth_des = $conn->query("UPDATE gcceforms.travel_destination SET accomplished='1' WHERE id='$accomlish_id'");
			if($sth_des){
				$sth_c = $conn->prepare("SELECT COUNT(*) as count FROM gcceforms.travel_destination WHERE accomplished != '1' AND travel_order_id = :to_id");
				$sth_c->bindParam(':to_id', $to_id, PDO::PARAM_INT);
				$sth_c->execute();
				$row = $sth_c->fetch(PDO::FETCH_ASSOC);
	
				if ($row['count'] == 0) {
					$sth_des_c = $conn->query("UPDATE gcceforms.travel_order SET accomplished='1', accomplished_by='$emp_id' WHERE id='$to_id'");
					if($sth_des_c){
						$list['status'] = 'success';
					} else {
						$list['status'] = 'Failed to update travel order accomplishment status';
					}
				} else {
					$list['status'] = 'Success Updating Travel Order Destination';
				}
			} else {
				$list['status'] = 'Failed to update travel destination';
			}
		} else {
			$list['status'] = 'Failed to update travel order';
		}
		$response['response_array'][] = $list;
		return json_encode($response);
	}


	public function to_accomplish_all(){
		$conn = $this->conn();
		$date_accomplish = $_POST['date'];
		$date = date('Y-m-d H:i:s');
		$emp_id = $_POST['emp_id'];
		$status = $_POST['status'];
		$to_id = $_POST['to_id'];
		$accomlish_id = $_POST['accomplishID'];
		$response = array();
		$list = array();
		$sth = $conn->query("UPDATE gcceforms.travel_order SET accomplishment_dt='$date',accomplished='1', accomplished_by='$emp_id' WHERE id='$to_id'");
		if($sth){
			$sth_des = $conn->query("UPDATE gcceforms.travel_destination SET accomplished = '1' WHERE travel_order_id = '$to_id'");
			if($sth_des){
				$list['status'] = 'success';
			}else{
				$list['status'] = 'Failed to Update Travel Order Accomplishment';
			}
		} else {
			$list['status'] = 'Failed to update travel order';
		}
		$response['response_array'][] = $list;
		return json_encode($response);
	}

	public function to_undo_accomplish(){
		$conn = $this->conn();
		$date_reset = "0000-00-00 00:00:00";
		$date = date('Y-m-d H:i:s');
		$emp_id = $_POST['emp_id'];
		$status = $_POST['status'];
		$to_id = $_POST['to_id'];
		$response = array();
		$list = array();

		$sth = $conn->query("UPDATE gcceforms.travel_order SET status='$status', accomplishment_dt='$date_reset', accomplished='0', unaccomplished_remarks='', accomplished_by='$emp_id' WHERE id='$to_id'");
		if($sth){
			$sth_des = $conn->query("UPDATE gcceforms.travel_destination SET accomplished = '0' WHERE travel_order_id = '$to_id'");
			if($sth_des){
				$list['status'] = 'success';
			}else{
				$list['status'] = 'Failed to Update Travel Order Undo Accomplishment';
			}
		} else {
			$list['status'] = 'Failed to update travel order';
		}
		$response['response_array'][] = $list;
		return json_encode($response);
		

	}

	public function to_approved(){ 
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$conn = $this->conn();
		$date = date('Y-m-d H:i:s');
		$to_id = $_POST['to_id'];
		$to_remarks = trim($_POST['remarks']);
		$to_status = $_POST['status'];
		$to_emp_name = $this->getempName($_POST['emp_id']);
		$response['response_array'] = array();
		$sth = $conn->query("UPDATE gcceforms.travel_order SET status='$to_status', approved_remarks='$to_remarks', approved_dt='$date', approved_by ='$to_emp_name' WHERE id='$to_id'");
		if ($sth) {
			$telegram = $this->sendTelegram($to_id);
			$list['status'] = 'success';
        } else {
			$list['status'] = 'failed';
        }

        array_push($response['response_array'], $list);
        echo json_encode($response);
	}

	public function sendTelegram($id) {
		$conn = $this->conn();
	
		$sth = $conn->prepare("SELECT * FROM gcceforms.travel_order WHERE id = :id");
		$sth->bindParam(':id', $id, PDO::PARAM_INT);
		$sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
	
		$vehicle_details = '';
	
		if ($row['is_service'] == 1 || $row['is_hitch'] == 1) {
			$vehicle_name = $this->vehicle_details($row['vehicle_id']);
			$veh_name = strtoupper($vehicle_name['name']) . " | " . strtoupper($vehicle_name['plateno']);
			$vehicle_details = '<b>VEHICLE</b>: ' . $veh_name . chr(10) . '<b>DRIVER</b>: ' . strtoupper($row['driver']) . chr(10) . chr(10);
		} elseif ($row['is_commute'] == 1) {
			$vehicle_details = '<b>VEHICLE</b>: COMMUTE' . chr(10);
		} elseif ($row['is_personal'] == 1) {
			$vehicle_details = '<b>VEHICLE</b>: PERSONAL VEHICLE' . chr(10);
		} elseif ($row['is_others'] == 1) {
			if (empty($row['others_remarks'])) {
				$vehicle_details = "".chr(10);
			} else {
				$vehicle_details = '<b>REMARKS</b>: ' . strtoupper($row['others_remarks']) . chr(10) . chr(10);
			}
		}
		
		$destination = $this->getDestinationById($id); 
		$personnel = $this->getPersonnelById($id);
		$dest = implode("=", (array)$destination['telegram']);
		$pers = implode("\n- ", $personnel);
		$pers = strtoupper($pers);
		$telegram_msg = '';
		$telegram_msg .= '<b>TO #</b>: ' . $row['reference_no'] . chr(10);
		$telegram_msg .= '<b>FILE: </b>' . strtoupper($row['company']) . chr(10);
		$telegram_msg .= '<b>PREP BY: </b>' . strtoupper($row['created_by']) . chr(10);
		$telegram_msg .= '<b>PERSONNEL: </b>' . "\n- " . $pers . chr(10);
		$telegram_msg .= $vehicle_details; 
		$telegram_msg .= str_replace("=", "", $dest);
		if ($this->telegram_config_if_exist('travel_order', 'count') > 0) {
			$this->telegram($telegram_msg);
		}
	}

	function getDestinationById($id) {
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT *
							   FROM gcceforms.travel_destination
							   WHERE travel_order_id = :id");
		$sth->bindParam(':id', $id, PDO::PARAM_INT);
		$sth->execute();
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
	
		$array_response = array();
		$telegram = "";
		$sms = "";
	
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$tg_date_from = date_format(date_create($row['date_from']), "F j, Y g:i a");
				$tg_date_to = date_format(date_create($row['date_to']), "F j, Y g:i a");
	
				$telegram_remarks = !empty(trim($row['remarks'])) ? '<b>REMARKS: </b>' . strtoupper($row['remarks']) . chr(10) : '';
	
				$telegram .= '<b>DESTINATION: </b>' . strtoupper($row['travel_from']) . ' - ' . strtoupper($row['travel_to']) . chr(10) .
							 '<b>DATE: </b>' . $tg_date_from . ' - ' . $tg_date_to . chr(10) .
							 $telegram_remarks .
							 '<b>REQ BY: </b>' . strtoupper($this->getempName($row['requested_by'])) . chr(10) .
							 '<b>PURPOSE: </b>' . strtoupper($row['purpose']) . chr(10) . chr(10) . "=";
				$sms .= '- ' . strtoupper($row['travel_from']) . ' - ' . strtoupper($row['travel_to']) . chr(10) .
						'  DATE: ' . $tg_date_from . ' - ' . $tg_date_to . chr(10);
			}
		}
		
		$array_response['telegram'] = $telegram;
		$array_response['sms'] = $sms;
	
		return $array_response;
	}

	function getPersonnelById($id) { 
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT * FROM gcceforms.travel_personnel WHERE travel_order_id = :id");
		$sth->bindParam(':id', $id, PDO::PARAM_INT);
		$sth->execute();

		$travel_personnel = array();

		if($sth->rowCount() > 0){
			foreach($sth->fetchAll(PDO::FETCH_ASSOC) as $row ){
				$emp_id = $this->getPersonnelNamesv1($row['employee_id']);
				array_push($travel_personnel, $emp_id);
			}
		}
		return $travel_personnel;
	}

	function getPersonnelNamesv1($id){ 
		$data = [];
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT id, lastname, firstname, position,middlename,suffix
							   FROM gccmaster.tblemployees
							   WHERE id = :id");
		$sth->bindParam(':id', $id, PDO::PARAM_INT); 
		$sth->execute();
		if($sth->rowCount() > 0){
			$row = $sth->fetch(PDO::FETCH_ASSOC);
			$data = trim($row["firstname"] . ' ' . $row["middlename"] . ' ' . $row["lastname"] . ' ' . $row['suffix']);
		}
		return $data;
	}
	

	function vehicle_details($veh) {
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT * FROM gccasset.vehicles WHERE id = :id");
		$sth->bindParam(':id', $veh, PDO::PARAM_INT);
		$sth->execute();
	
		$data = $sth->fetch(PDO::FETCH_ASSOC);
	
		return $data !== false ? $data : null;
	}


	public function telegram_config_if_exist($module, $data) {
		$conn = $this->conn();
	
		$query = "SELECT * FROM gcceforms.telegram_config 
				  WHERE module = :module 
				  ORDER BY created_at DESC";
		$sth = $conn->prepare($query);
		$sth->bindParam(':module', $module, PDO::PARAM_STR);
		$sth->execute();
	
		$details = $sth->fetch(PDO::FETCH_ASSOC);
		$count = $sth->rowCount();
	
		if ($data == 'count') {
			return $count;
		} else {
			return $details;
		}
	}


	public function telegram($msg) {

			$data = $this->telegram_config_if_exist('travel_order', 'details');
			$telegrambot = $data['telegram_bot_token']; 
			$telegramchatid = $data['chat_id'];
	
			$url = 'https://api.telegram.org/bot' . $telegrambot . '/sendMessage';
			$data = array(
				'chat_id' => $telegramchatid,
				'text' => $msg,
				'parse_mode' => 'html'
			);
	
			$options = array(
				'http' => array(
					'method' => 'POST',
					'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
					'content' => http_build_query($data),
					'ignore_errors' => true,
				),
			);
	
			$context = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
	
			return $result;

	}
	

	// function sendTelegramToPersonnelHeads($id, $telegram_msg) {
	// 		$conn = $this->conn(); 
	// 		$stmt = $conn->prepare("SELECT * FROM gcceforms.travel_personnel WHERE travel_order_id = :id");
	// 		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	// 		$stmt->execute();	

	// 		$personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	// 		if (count($personnel) > 0) {
	// 			foreach ($personnel as $row) {
	// 				$head_id = $this->getTelegramId($row['employee_id']); 
	// 				if ($head_id != 2) {
	// 					$this->telegram_dept_heads($telegram_msg, $head_id);
	// 				}
	// 			}
	// 		}
		
	// }
	

	// function sendDepartmentHeadEmail($emp_id, $to_id) {
	// 	if ($this->checkUserIsHead($emp_id) == 0) {
	// 			$head_id = $this->getEmployeeDepartmentHead($emp_id);
	
	// 			$conn = $this->conn();
				
	// 			$stmt = $conn->prepare("SELECT email FROM gccmaster.tblemployees WHERE id = :head_id");
	// 			$stmt->bindParam(':head_id', $head_id, PDO::PARAM_INT);
	// 			$stmt->execute();
				
	// 			$result = $stmt->fetch(PDO::FETCH_ASSOC);
	
	// 			if ($result && isset($result['email'])) {
	// 				$email = $result['email'];
	// 				$this->sendEmail(true, $email, $to_id); 
	// 			}
	// 	}
	// }
	
	// 	function getEmployeeDepartmentHead($emp_id){
    //         $this->db->select("b.head_id");
    //         $this->db->from("gccmaster.tblemployees a");
    //         $this->db->join("gcchris.tbldepartments b", "b.id = a.department_id", "LEFT");
    //         $this->db->where("a.id", $emp_id);
    //         return $this->db->get()->row_array()['head_id'];
    //     }




	// 	function getEmployeeDepartmentHead($emp_id) {
	// 			$conn = $this->conn(); 
	// 			$stmt = $conn->prepare("
	// 				SELECT b.head_id
	// 				FROM gccmaster.tblemployees a
	// 				LEFT JOIN gcchris.tbldepartments b ON b.id = a.department_id
	// 				WHERE a.id = :emp_id
	// 			");		
	// 			$stmt->bindParam(':emp_id', $emp_id, PDO::PARAM_INT);
	// 			$stmt->execute();		
	// 			$result = $stmt->fetch(PDO::FETCH_ASSOC);		
	// 			return $result ? $result['head_id'] : null;
	// 	}
		
		
    //     public function telegram_dept_heads($msg,$dept_head_chat_id) {
    //         $telegram_data = $this->telegram_config_if_exist('travel_order', 'data');
    //         $telegramchatid= $dept_head_chat_id;
    //         $url='https://api.telegram.org/bot'.$telegram_data->telegram_bot_token.'/sendMessage';$data=array('chat_id'=>$telegramchatid,'text'=>$msg,'parse_mode'=>'html');
    //         $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),'ignore_errors'=>true),);
    //         $context=stream_context_create($options);
    //         $result=file_get_contents($url,false,$context);
    //         return $result;
    //     }


	// 	public function telegram_dept_heads($msg, $dept_head_chat_id) {

	// 			$telegram_data = $this->telegram_config_if_exist('travel_order', 'details');	
	// 			$telegramchatid = $dept_head_chat_id;
	// 			$url = 'https://api.telegram.org/bot' . $telegram_data['telegram_bot_token'] . '/sendMessage';
	// 			$data = array(
	// 				'chat_id' => $telegramchatid,
	// 				'text' => $msg,
	// 				'parse_mode' => 'html'
	// 			);
	// 			$options = array(
	// 				'http' => array(
	// 					'method' => 'POST',
	// 					'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
	// 					'content' => http_build_query($data),
	// 					'ignore_errors' => true,
	// 				),
	// 			);
	// 			$context = stream_context_create($options);
	// 			$result = file_get_contents($url, false, $context);		
	// 			return $result;
	// 	}


    //     function checkUserIsHead($head_id){

	// 		$conn = $this->conn(); 
	// 		$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM gcchris.tbldepartments WHERE head_id = :head_id");
	// 		$stmt->bindParam(':head_id', $head_id, PDO::PARAM_INT);
	// 		$stmt->execute();
	// 		$result = $stmt->fetch(PDO::FETCH_ASSOC);
	// 		return $result['count'];
	// 	}
		
	// 	public function getTelegramId($emp_id) {
	// 			$conn = $this->conn(); 
	// 			$stmt = $conn->prepare("SELECT department_id FROM gccmaster.tblemployees WHERE id = :emp_id");
	// 			$stmt->bindParam(':emp_id', $emp_id, PDO::PARAM_INT);
	// 			$stmt->execute();
	// 			$employee = $stmt->fetch(PDO::FETCH_ASSOC);
	// 			$department = $employee['department_id'];

	// 			if (is_numeric($department)) {
	// 				$stmt = $conn->prepare("SELECT head_id FROM gcchris.tbldepartments WHERE id = :department_id");
	// 				$stmt->bindParam(':department_id', $department, PDO::PARAM_INT);
	// 			} else {
	// 				$stmt = $conn->prepare("SELECT head_id FROM gcchris.tbldepartments WHERE description = :description");
	// 				$stmt->bindParam(':description', $department, PDO::PARAM_STR);
	// 			}
	// 			$stmt->execute();
	// 			$department_info = $stmt->fetch(PDO::FETCH_ASSOC);
	// 			$department_head = $department_info['head_id'];
		
	// 			$stmt = $conn->prepare("SELECT telegram_chat_id FROM gccmaster.tblusers WHERE emp_id = :emp_id");
	// 			$stmt->bindParam(':emp_id', $department_head, PDO::PARAM_INT);
	// 			$stmt->execute();
	// 			$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
	// 			return $user_info ? $user_info['telegram_chat_id'] : null;

	// 	}
		

	// 	public function travel_order_details($id) {

	// 			$arrData = array();
	// 			$newPersonnel = array();
	// 			$newDestination = array();
	// 			$conn = $this->conn(); 		
	// 			$stmt = $conn->prepare("SELECT * FROM gcceforms.travel_order WHERE id = :id");
	// 			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	// 			$stmt->execute();
	// 			$travel_order_data = $stmt->fetch(PDO::FETCH_OBJ);

	// 			if ($travel_order_data->vehicle_id == "0") {
	// 				$plateno = "";
	// 			} else {
	// 				if ($travel_order_data->vehicle_id) {
	// 					$vehicle_data = $this->vehicle_details($travel_order_data->vehicle_id);
	// 					$gen_code = $vehicle_data->gen_code;
	// 					$plateno = $gen_code . " | " . $vehicle_data->plateno . " | " . $vehicle_data->name;
	// 				} else {
	// 					$plateno = $travel_order_data->vehicle_id;
	// 				}
	// 			}
		
	// 			$travel_order_data->plateno = $plateno;
		
	// 			if ($travel_order_data->accomplished_by > 0) {
	// 				$travel_order_data->accomplished_by_name = $this->getPersonnelName($travel_order_data->accomplished_by);
	// 			}
		
	// 			$stmt = $conn->prepare("SELECT * FROM gcceforms.travel_personnel WHERE travel_order_id = :id");
	// 			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	// 			$stmt->execute();
	// 			$personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
	// 			foreach ($personnel as $personnels) {
	// 				$personnels['employee'] = $this->getPersonnelName($personnels['employee_id']);
	// 				$newPersonnel[] = $personnels;
	// 			}
		
	// 			$stmt = $conn->prepare("SELECT * FROM gcceforms.travel_destination WHERE travel_order_id = :id");
	// 			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	// 			$stmt->execute();
	// 			$destination = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
	// 			foreach ($destination as $destinations) {
	// 				$destinations['requested_by_name'] = $this->getPersonnelName($destinations['requested_by']);
	// 				$destinations['date_from'] = date("M d, Y g:i A", strtotime($destinations['date_from']));
	// 				$destinations['date_to'] = date("M d, Y g:i A", strtotime($destinations['date_to']));
	// 				$newDestination[] = $destinations;
	// 			}
		
	// 			$end_travel_order_time = end($destination);
		
	// 			$travel_order_data->duration = date("M d, Y g:i A", strtotime($destination[0]['date_from'])) . " - " . date("M d, Y g:i A", strtotime($end_travel_order_time['date_to']));
		
	// 			$arrData['data'] = $travel_order_data;
	// 			$arrData['personnel'] = $newPersonnel;
	// 			$arrData['destination'] = $newDestination;
		
	// 			return $arrData;

	// 	}

	// 	function checkEmail($email) {
	// 		$findAt = strpos($email, '@');
	// 		$findDot = strpos($email, '.');
			
	// 		// Ensure both '@' and '.' exist and that '.' comes after '@'
	// 		return ($findAt !== false && $findDot !== false && $findDot > $findAt);
	// 	}


	// 	public function sendEmail($email, $email_address, $id) {
	// 		if ($this->checkEmail($email_address)) {
	// 			// Fetch travel order details using PDO
	// 			$travelOrderDetails = $this->travel_order_details($id);
	// 			$data = $travelOrderDetails['data'];
	// 			$data->destination = $travelOrderDetails['destination'];
	// 			$data->personnel = $travelOrderDetails['personnel'];
		
	// 			// Handle vehicle details
	// 			if ($data->vehicle_id != 0 && !empty($data->vehicle_id)) {
	// 				$vehicle_data = $this->vehicle_details($data->vehicle_id);
	// 			} else {
	// 				if ($data->is_commute == 1) {
	// 					$vehicle_data = "Commute";
	// 					$data->plateno = "Not Available";
	// 					$data->driver = "Commute";
	// 				} else if ($data->is_personal == 1) {
	// 					$vehicle_data = "Personal";
	// 					$data->plateno = "Not Available";
	// 					$data->driver = "Personal Vehicle";
	// 				} else if ($data->is_others == 1) {
	// 					$vehicle_data = "Others";
	// 					$data->plateno = "Not Available";
	// 					$data->driver = "Not Available";
	// 				}
	// 			}
		
	// 			if ($data) {
	// 				ob_start();
	// 				include "try/email_to_request_template.php"; 
	// 				$messageContent = ob_get_clean();
	// 	            // include "views/eforms/email_templates/email_to_request_template.php"; 
	// 				// Adjust path if necessary

	// 				if ($email) {
	// 					$module = "eforms_to_request";
	// 					$email_title = "Travel Order Request";
	// 					$content_title = "Travel Order Statement";
	// 					$content = $messageContent;
		
	// 					$overrideMailer = array();
	// 					$overrideMailer["send_to"] = array($email_address);
	// 					if ($content) {
	// 						$sent = $this->send_email($module, $email_title, $content_title, $content, $overrideMailer);
	// 						return $sent ? true : false;
	// 					} else {
	// 						return false;
	// 					}
	// 				} else {
	// 					echo $messageContent;
	// 				}
	// 			}
	// 		} else {
	// 			return false;
	// 		}
	// 	}
		
	

////////////////////////////////////////////
}

?>