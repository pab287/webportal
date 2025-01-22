<?php

class Hydra_billing_home_m extends Dbase{
    
	public function get_list_accounts(){
		$conn = $this->conn();
		$year = date('Y');
		$month = date('m');
		$previous_month = date('m')-1;

		// fetch accounts
		$response['accounts_array'] = array();
		$sth = $conn->prepare("SELECT *
							   FROM hydra_billing.accounts 
							   ORDER BY `created_at` DESC");
   		$sth->execute();
   		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
   			$list = array();
	 		$list['id'] = $row['id'];
	 		$list['accountno'] = $row['accountno'];
	 		$list['meterno'] = $row['meterno'];
	 		$list['meterno_raw'] = $row['meterno_raw'];
	 		$list['firstname'] = utf8_encode($row['firstname']);
	 		$list['middlename'] = utf8_encode($row['middlename']);
	 		$list['lastname'] = utf8_encode($row['lastname']);
	 		$list['occupation'] = $row['occupation'];
	 		$list['model'] = $row['model'];
	 		$list['block'] = $row['block'];
	 		$list['lot'] = $row['lot'];
	 		$list['phonenumber'] = $row['phonenumber'];
	 		$list['email'] = utf8_encode($row['email']);
	 		$list['subdivision_id'] = $row['subdivision_id'];
	 		$list['street'] = utf8_encode($row['street']);
	 		$list['brgy'] = utf8_encode($row['brgy']);
	 		$list['city'] = utf8_encode($row['city']);
	 		$list['province'] = $row['province'];
	 		$list['status'] = $row['status'];
	 		$list['applicationdate'] = $row['applicationdate'];
	 		$list['activationdate'] = $row['activationdate'];
	 		$list['created_by'] = $row['created_by'];
	 		$list['created_at'] = $row['created_at'];
	 		$list['updated_by'] = $row['updated_by'];
	 		$list['updated_at'] = $row['updated_at'];
	 		$list['is_archive'] = $row['is_archive'];
	 		$list['archived_by'] = $row['archived_by'];
	 		$list['is_disconnected'] = $row['is_disconnected'];
	 		$list['disconnect_date'] = $row['disconnect_date'];
	 		$list['subdivision'] = $this->getSubdivisionName($conn, $row['subdivision_id']);
			array_push($response['accounts_array'], $list);
		}

		// fetch subdivision
		$response['subdivision_array'] = array();
		$sth = $conn->prepare("SELECT * 
							   FROM hydra_billing.subdivision 
							   ORDER BY `date_added` DESC");
   		$sth->execute();
   		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
   			$list = array();
	 		$list['id'] = $row['id'];
	 		$list['name'] = utf8_encode($row['name']);
	 		$list['address'] = utf8_encode($row['address']);
	 		$list['description'] = $row['description'];
	 		$list['meterno'] = $row['meterno'];
	 		$list['meterno_raw'] = $row['meterno_raw'];
	 		$list['created_by'] = $row['created_by'];
	 		$list['updated_by'] = $row['updated_by'];
	 		$list['updated_date'] = $row['updated_date'];
	 		$list['status'] = $row['status'];
	 		$list['date_added'] = $row['date_added'];
			array_push($response['subdivision_array'], $list);
		}

		// fetch previous readings
		$response['readings_array'] = array();
		$sth = $conn->prepare("SELECT id 
							   FROM hydra_billing.accounts 
							   ORDER BY `id` ASC");
   		$sth->execute();
   		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

   			$account_id = $row['id'];

			$sth_readings = $conn->prepare("SELECT * 
											FROM hydra_billing.readings 
											WHERE account_id='$account_id' AND is_archived='0'
											ORDER BY `reading_date` DESC 
											LIMIT 1");
   			$sth_readings->execute();
   			while ($row_ = $sth_readings->fetch(PDO::FETCH_ASSOC)) {
				$checkReading = $this->getAlreadyReading($conn, $account_id, $month, $year, $row_['meterno']);
				$checkReading_count = $checkReading->rowCount();
				$list = array();
		 		$list['id'] = $row_['id'];
		 		$list['meterno'] = $row_['meterno'];
		 		$list['account_id'] = $row_['account_id'];
		 		$list['ref_no'] = $row_['ref_no'];
		 		$list['ref_series'] = $row_['ref_series'];
		 		$list['ref_yr'] = $row_['ref_yr'];
		 		$list['ref_month'] = $row_['ref_month'];
		 		$list['reading_date'] = $row_['reading_date'];
		 		$list['reading'] = $row_['reading'];
		 		$list['status'] = $row_['status'];
		 		$list['created_by'] = $row_['created_by'];
		 		$list['created_at'] = $row_['created_at'];
		 		$list['updated_by'] = $row_['updated_by'];
		 		$list['updated_at'] = $row_['updated_at'];
		 		$list['pic'] = utf8_encode($row_['pic']);
		 		$list['is_billed'] = $row_['is_billed'];
		 		$list['createbill_by'] = $row_['createbill_by'];
		 		$list['createbill_at'] = $row_['createbill_at'];
				$list['isAlreadyReading'] = $checkReading_count;
				array_push($response['readings_array'], $list);
   			}
		}

		// fetch previous readings destribution
		$response['distribution_array'] = array();
		$sth = $conn->prepare("SELECT id 
							   FROM hydra_billing.subdivision 
							   ORDER BY `id` ASC");
   		$sth->execute();
   		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

   			$subdivision_id = $row['id'];

			$sth_destribution = $conn->prepare("SELECT * 
												FROM hydra_billing.distribution WHERE subdivision_id='$subdivision_id' 
												ORDER BY `reading_date` DESC 
												LIMIT 1");
   			$sth_destribution->execute();
   			while ($row_ = $sth_destribution->fetch(PDO::FETCH_ASSOC)) {
				$list = array();
		 		$list['id'] = $row_['id'];
		 		$list['meterno'] = $row_['meterno'];
		 		$list['distribute'] = $row_['distribute'];
		 		$list['reading_date'] = $row_['reading_date'];
		 		$list['subdivision_id'] = $row_['subdivision_id'];
		 		$list['updated_by'] = $row_['updated_by'];
		 		$list['updated_date'] = $row_['updated_date'];
		 		$list['created_by'] = $row_['created_by'];
		 		$list['created_date'] = $row_['created_date'];
		 		$list['subdivision'] = $this->getSubdivisionName($conn, $row_['subdivision_id']);
				array_push($response['distribution_array'], $list);
   			}
		}

   		echo json_encode($response);
	}

	public function save_local_data(){
		$conn = $this->conn();
		$response['response_array'] = array();

		$user_id = $_POST['user_id'];

		// ---------------------------------------------------------------------------- saving for Readings
		$local_data_array = $_POST['local_data_array'];
		$array_local_data = json_decode($local_data_array, true);
		if (count($array_local_data) > 0) {
			foreach ($array_local_data as $row){
				$list = array();

				$local_id = $row['id'];
				$reading = $row['reading'];
				$meterno = $row['meterno'];
				$reading_date = $row['reading_date'];
				$created_at = $row['created_at'];
				$created_by = $row['created_by'];
				$account_id = $row['account_id'];
				$account_name = $row['account_name'];
				$status = "Unbilled";

				$ref_yr = substr($created_at, 2, 2);
			   	$ref_month = substr($created_at, 5, 2);
			   	$ref_series = $this->getSeries($ref_month,$ref_yr);
			   	$reference_no = 'MRR' . $ref_yr . '-' . $ref_month . '-' . $ref_series;

			   	$month = date('m', strtotime($reading_date));
				$year = date('Y', strtotime($reading_date));

				if ($this->checkReading($conn, $account_id, $month, $year, $meterno) > 0) {
					$list['status'] = 'duplicate';
				} else if ($this->getPreviousReading($account_id, $meterno) > $reading) {
					$list['status'] = 'previous_reading_greater';
				} else {

					$sth = $conn->prepare("INSERT INTO hydra_billing.readings(`meterno`, `account_id`, `ref_no`, `reading_date`, `reading`, `created_by`, `created_at`, `ref_yr`, `ref_series`, `ref_month`, `status`) VALUES ('$meterno','$account_id','$reference_no','$reading_date','$reading','$created_by','$created_at','$ref_yr','$ref_series','$ref_month', '$status')");
			   		$sth->execute();
			   		if ($sth) {
			    		$list['status'] = 'success';
	   					$this->saveLogs("success", "insert", $created_by, "[Mobile] Readings - added reading ".$reading." of ".$account_name." thru sync.");
			   		} else {
			   			$list['status'] = 'error';
	   					$this->saveLogs("error", "insert", $created_by, "[Mobile] Readings - added reading ".$reading." of ".$account_name." thru sync.");
			   		}
				}

				$list['local_id'] = $local_id;
				$list['dataOf'] = "readings";

				array_push($response['response_array'], $list);
			}
		}

		// ---------------------------------------------------------------------------- saving for Destribution
		$local_data_array_destribution = $_POST['local_data_array_destribution'];
		$array_local_data_destribution = json_decode($local_data_array_destribution, true);
		if (count($array_local_data_destribution) > 0) {
			foreach ($array_local_data_destribution as $row){
				$list = array();

				$local_id = $row['id'];
				$distribute = $row['distribute'];
				$meterno = $row['meterno'];
				$reading_date = $row['reading_date'];
				$subdivision_id = $row['subdivision_id'];
				$created_by = $row['created_by'];
				$created_date = $row['created_date'];
				$subdivision = $row['subdivision'];

				$month = date('m', strtotime($reading_date));
				$year = date('Y', strtotime($reading_date));

				if ($this->checkDestribution($conn, $subdivision_id, $month, $year, $meterno) > 0) {
					$list['status'] = 'duplicate';
				} else if ($this->getPreviousDestribution($subdivision_id, $meterno) > $distribute) {
					$list['status'] = 'previous_reading_greater';
				} else {

					$sth = $conn->prepare("INSERT INTO hydra_billing.distribution(`meterno`, `distribute`, `subdivision_id`, `created_by`, `created_date`, `reading_date`) 
										   VALUES ('$meterno','$distribute','$subdivision_id','$created_by','$created_date','$reading_date')");
			   		$sth->execute();
			   		if ($sth) {
			   			$list['status'] = 'success';
			   			$this->saveLogs("success", "insert", $created_by, "[Mobile] Distribution - ".$subdivision." added reading of ".$distribute." thru sync");
			   		} else {
			   			$list['status'] = 'error';
			   			$this->saveLogs("error", "insert", $created_by, "[Mobile] Distribution - ".$subdivision." added reading of ".$distribute." thru sync");
			   		}
				}

				$list['local_id'] = $local_id;
				$list['dataOf'] = "destribution";

				array_push($response['response_array'], $list);
			}
		}

		echo json_encode($response);
	}

	private function getSubdivisionName($conn, $subdivision_id){
		$sth = $conn->prepare("SELECT name 
							   FROM hydra_billing.subdivision 
							   WHERE id='$subdivision_id'");
		$sth->execute();
		$result = $sth->fetch();
		return $result["name"] ? $result["name"] : '';
	}

	public function getAlreadyReading($conn, $account_id, $month, $year, $meterno){
		$sth = $conn->prepare("SELECT id, is_billed, account_id 
								FROM hydra_billing.readings 
								WHERE account_id='$account_id' AND meterno='$meterno' AND is_archived='0' AND year(reading_date)='$year' AND month(reading_date)='$month'");
		$sth->execute();
		return $sth;
	}

	private function getSeries($month,$year){
    	$conn = $this->conn();

    	$list = array();

    	$sth = $conn->prepare("SELECT ref_series 
    						   FROM hydra_billing.readings 
    						   WHERE ref_yr='$year' AND ref_month='$month' 
    						   ORDER BY ref_series ASC");
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

    private function getPreviousReading($account_id, $meterno){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT reading 
							   FROM hydra_billing.readings 
							   WHERE account_id='$account_id' AND meterno='$meterno' AND is_archived='0'
							   ORDER BY `reading_date` DESC 
							   LIMIT 1");
		$sth->execute();
		$result = $sth->fetch();
		return $result["reading"] ? $result["reading"] : '0';
	}

	public function checkReading($conn, $account_id, $month, $year, $meterno){
		$sth = $conn->prepare("SELECT id 
							   FROM hydra_billing.readings 
							   WHERE account_id='$account_id' AND meterno='$meterno' AND is_archived='0' AND year(reading_date)='$year' AND month(reading_date)='$month'");
   		$sth->execute();
   		return $sth->rowCount();
	}

	private function getPreviousDestribution($subdivision_id, $meterno){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT distribute 
							   FROM hydra_billing.distribution 
							   WHERE subdivision_id='$subdivision_id' AND meterno='$meterno' 
							   ORDER BY `reading_date` DESC 
							   LIMIT 1");
		$sth->execute();
		$result = $sth->fetch();
		return $result["distribute"] ? $result["distribute"] : '0';
	}

	public function checkDestribution($conn, $subdivision_id, $month, $year, $meterno){
		$sth = $conn->prepare("SELECT id 
							   FROM hydra_billing.distribution 
							   WHERE subdivision_id='$subdivision_id' AND meterno='$meterno' AND year(reading_date)='$year' AND month(reading_date)='$month'");
   		$sth->execute();
   		return $sth->rowCount();
	}

	public function saveLogs($type, $user_action, $user_id, $log_message){
		$conn = $this->conn();
		$current_date = date("Y-m-d H:i:s");
		$ip_address = '';

		$sth = $conn->prepare("INSERT INTO hydra_billing.user_logs_event(`type`, `user_action`, `user_id`, `log_message`, `ip_address`, `created_at`) 
						 	   VALUES ('$type','$user_action','$user_id','$log_message','$ip_address','$current_date')");
		$sth->execute();

		if ($sth) {
			return true;
		} else {
			return false;
		}
	}


} // gcceformsloaModel

?>