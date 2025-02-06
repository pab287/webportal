<?php

class Hydra_billing_readings_m extends Dbase{
    
	public function get_customer_details(){
		$conn = $this->conn();
		$current_date = date('Y-m-d');
		$year = date('Y');
		$month = date('m');

		$response['account_details'] = array();
		$meterno = $_POST["meterno"];

		try {
			$sth = $conn->prepare("SELECT a.*, b.name as subdivision
								   FROM hydra_billing.accounts a
								   LEFT JOIN hydra_billing.subdivision b
								   ON a.subdivision_id = b.id
								   WHERE a.meterno_raw = :meterno");
			$sth->bindParam(':meterno', $meterno);
			$sth->execute();

			if ($sth->rowCount() > 0) {

				$row = $sth->fetch(PDO::FETCH_ASSOC);
 
				$checkReading = $this->checkReading($conn, $row['id'], $month, $year, $meterno);
				
				if ($checkReading && !empty($checkReading)) {
					$checkReading_id = $checkReading[0]['id'];
					$checkReading_is_billed = $checkReading[0]['is_billed'];
					$checkReading_count = 1;
					$checkReading_account_id = $checkReading[0]['account_id'];
				} else {
					$checkReading_id = 0;
					$checkReading_is_billed = 0;
					$checkReading_count = 0;
					$checkReading_account_id = 0;
				}

				$list['status'] = "exist";
				$list['id'] = $row['id'];
				$list['model'] = ucfirst($row['model']);
				$list['account_status'] = $row['status'];
				$list['accountno'] = $row['accountno'];
				$list['meterno'] = $row['meterno'];
				$list['current_date'] = $current_date;
				$list['subdivision'] = $row['subdivision'];
				$list['name'] = strtoupper(utf8_encode($row['firstname']." ".$row['lastname']));
				$list['previous_reading'] = $this->getPreviousReading($row['id'], $meterno);
				$list['reading_id'] = $checkReading_id;
				$list['is_billed'] = $checkReading_is_billed;
				$list['isAlreadyReading'] = $checkReading_count;
 
				if ($checkReading_is_billed == 1) {
					$billing = $this->getBilling($checkReading_id, $checkReading_account_id);
					$list['previous'] = $billing["previous"];
					$list['current'] = $billing["current"];
					$list['rate'] = $billing["rate"];
					$list['usage'] = $billing["usage"];
					$list['total_charges'] = $billing["total_charges"];
					$list['actual_current_bill'] = $billing["actual_current_bill"];
					$list['billing_date'] = $billing["billing_date"];
					$list['due_date'] = $billing["due_date"];
					$list['over_payment'] = $billing["over_payment"];
					$list['reconnectionFee'] = $billing["reconnectionFee"];
					$list['total_penalty'] = $billing["total_penalty"];
					$list['total_balance'] = $billing["total_balance"];
					$list['total_amount_due'] = $billing["total_amount_due"];
					$list['ref_no'] = $billing["ref_no"];
					$list['bill_id'] = $billing["bill_id"];
				}
			} else {
				$list['status'] = "not exist";
			}
		} catch (PDOException $e) {
			$list['status'] = 'error';
			$list['message'] = 'Error: ' . $e->getMessage();
		}

		array_push($response['account_details'], $list);
   		echo json_encode($response);
	}

	private function getBilling($reading_id,$account_id){
		$current_date = date("Y-m-d");
		$penalties = $this->getPenalties();
		$overdue_charges = 0;

		$conn = $this->conn();
		$sth = $conn->prepare("SELECT a.reading_id, a.previous, a.current, a.rate, a.usage, a.total_charges, a.actual_current_bill, concat(a.billing_from,' - ',a.billing_to) as billing_date, a.due_date, b.is_disconnected, a.ref_no, a.id
			  				   FROM hydra_billing.bills a
			  				   LEFT JOIN hydra_billing.accounts b
			  				   ON a.account_id = b.id
							   WHERE a.reading_id='$reading_id' AND a.account_id='$account_id' AND a.status='1'
							   ORDER BY a.billing_to DESC");
		$sth->execute();
		$row = $sth->fetch();

		$reconnection_fee = $this->getReconnectionFee();
		$over_payment = $this->computeOverPayment($account_id);
		$balance_last_bill = $this->computeBalanceLastBill($account_id);

		$reconnectionFee = $row['is_disconnected']==1 ? $reconnection_fee : '0.00';

		if ($current_date > $row['due_date']) {
			if ($penalties['type'] == 'percentage') {
				$overdue_charges = ($penalties['amount'] / 100) * $row['total_charges'];
			}
		}

		$total_amount_due = ($row["total_charges"] + $balance_last_bill['total_balance'] + $overdue_charges + $balance_last_bill['total_penalty'] + $reconnectionFee) - $over_payment;
	
		$list = array();
		$list['reading_id'] = $row['reading_id'];
		$list['bill_id'] = $row["id"];
		$list['ref_no'] = $row["ref_no"];
		$list['previous'] = number_format($row["previous"],2,'.','');
		$list['current'] = number_format($row["current"],2,'.','');
		$list['rate'] = $row["rate"];
		$list['usage'] = number_format($row["usage"],2,'.','');
		$list['total_charges'] = number_format($row["total_charges"],2,'.','');
		$list['actual_current_bill'] = number_format($row["actual_current_bill"],2,'.','');
		$list['billing_date'] = $row["billing_date"];
		$list['due_date'] = $row["due_date"];
        $list["reconnectionFee"] = number_format($reconnectionFee, 2,'.','');
		$list['overdue_charges'] = $overdue_charges;
		$list['over_payment'] = $over_payment;
        $list["total_penalty"] = number_format($overdue_charges, 2,'.',''); // Total penalty of current bill only
        $list["total_balance"] = number_format($balance_last_bill['total_balance'] + $balance_last_bill['total_penalty'], 2,'.','');
		$list['formula'] = $row["total_charges"] . " + " . $balance_last_bill['total_balance'] . " + " . $overdue_charges . " + " .  $reconnectionFee . " + " .  $balance_last_bill['total_penalty'] . " - " . $over_payment;
        $list["total_amount_due"] = number_format($total_amount_due, 2,'.','');
		return $list;
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
		return (is_array($result) && isset($result["reading"])) ? $result["reading"] : '0';
	}

	private function getPreviousReadingEdit($account_id, $reading_date, $meterno_raw){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT reading 
							   FROM hydra_billing.readings 
							   WHERE account_id='$account_id' AND reading_date<'$reading_date' AND meterno='$meterno_raw' AND is_archived='0'
							   ORDER BY `reading_date` DESC 
							   LIMIT 1");
		$sth->execute();
		$result = $sth->fetch();
		return $result["reading"] ? $result["reading"] : '0';
	}

	public function fetch_history(){
		$conn = $this->conn();
		$current_m = date('m');
		$current_y = date('Y');

		$response['reading_details'] = array();

		$user_id = $_POST['user_id'];

		$sth = $conn->prepare("SELECT a.reading_date, a.ref_no, a.id, a.created_at, a.account_id, a.reading, b.accountno, b.meterno, b.meterno_raw, b.firstname,
									  b.lastname, b.model, a.is_billed, c.name as subdivision
							   FROM hydra_billing.readings a 
							   LEFT JOIN hydra_billing.accounts b
							   ON a.account_id = b.id
							   LEFT JOIN hydra_billing.subdivision c
							   ON b.subdivision_id = c.id
							   WHERE a.created_by='$user_id' AND a.meterno = b.meterno_raw AND a.is_archived='0' AND month(a.reading_date)='$current_m' AND year(a.reading_date)='$current_y'
							   ORDER BY a.ref_no DESC");
   		$sth->execute();

   		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
   			$list = array();
	 		$list['id'] = $row['id'];
	 		$list['ref_no'] = $row['ref_no'];
	 		$list['meterno'] = $row['meterno'];
	 		$list['previous_reading'] = $this->getPreviousReadingEdit($row['account_id'],$row['reading_date'],$row['meterno_raw']);
	 		$list['account'] = strtoupper(utf8_encode($row['firstname'].' '.$row['lastname']));
	 		$list['created_at'] = date("F j, Y, g:i A", strtotime($row['created_at']));
	 		$list['reading_date'] = $row['reading_date'];
	 		$list['reading'] = number_format($row["reading"],2,'.','');
	 		$list['model'] = ucfirst($row['model']);
	 		$list['subdivision'] = $row['subdivision'];
	 		$list['accountno'] = $row['accountno'];
	 		$list['account_id'] = $row['account_id'];
	 		$list['is_billed'] = $row['is_billed']=='1' ? true : false;
			array_push($response['reading_details'], $list);
		}

   		echo json_encode($response);
	}

	public function fetch_billing(){
		$response['billing_details'] = array();
		$billing = $this->getBilling($_POST["id"], $_POST["account_id"]);

		$list = array();
		$list['previous'] = $billing["previous"];
		$list['current'] = $billing["current"];
		$list['rate'] = $billing["rate"];
		$list['usage'] = $billing["usage"];
		$list['total_charges'] = $billing["total_charges"];
		$list['billing_date'] = $billing["billing_date"];
		$list['due_date'] = $billing["due_date"];
		$list['over_payment'] = $billing["over_payment"];
		$list['reconnectionFee'] = $billing["reconnectionFee"];
	 	$list['total_penalty'] = $billing["total_penalty"];
	 	$list['total_balance'] = $billing["total_balance"];
	 	$list['total_amount_due'] = $billing["total_amount_due"];
	 	$list['ref_no'] = $billing["ref_no"];
	 	$list['bill_id'] = $billing["bill_id"];
		array_push($response['billing_details'], $list);
		echo json_encode($response);
	}

	public function checkReading($conn, $accountId, $month, $year, $meterno) {
		try {
			$stmt = $conn->prepare("SELECT id, is_billed, account_id
				FROM hydra_billing.readings
				WHERE account_id = :account_id AND meterno = :meterno AND is_archived = 0 AND YEAR(reading_date) = :year AND MONTH(reading_date) = :month");
			
			$stmt->execute([
				':account_id' => $accountId,
				':meterno' => $meterno,
				':year' => $year,
				':month' => $month,
			]);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error: " . $e->getMessage();
			return false;
		}
	}

	public function save_reading(){
		$conn = $this->conn();
		$response['response_array'] = array();

		$current_date = date("Y-m-d H:i:s");
		$ref_yr = substr($current_date, 2, 2);
	   	$ref_month = substr($current_date, 5, 2);

	   	$ref_series = $this->getSeries($ref_month,$ref_yr,"hydra_billing.readings");
	   	$reference_no = 'MRR' . $ref_yr . '-' . $ref_month . '-' . $ref_series;

		// Check post data first
		if (empty($_POST) || !isset($_POST['account_id']) || $_POST['account_id'] == "" ||
			!isset($_POST['meterno']) || $_POST['meterno'] == "" ||
			!isset($_POST['reading_date']) || $_POST['reading_date'] == "" ||
			!isset($_POST['reading']) || $_POST['reading'] == "" ||
			!isset($_POST['created_by']) || $_POST['created_by'] == "") {
			
			$list['status'] = false;
			$list['message'] = "POST data is empty or missing required fields";
			array_push($response['response_array'], $list);
			echo json_encode($response);
			return; // Exit the function to prevent further execution
		}

		$account_id = $_POST['account_id'];
		$meterno = $_POST['meterno'];
		$reading_date = $_POST['reading_date'];
		$reading = $_POST['reading'];
		$created_by = $_POST['created_by'];

		$accnt_name = $this->getAccountName($account_id);
		if ($accnt_name === null) {
			$list['status'] = false;
			$list['message'] = 'Account name not found';
			array_push($response['response_array'], $list);
			echo json_encode($response);
			return;
		}

		$created_at = $current_date;
		$status = "Unbilled";

		$month = date('m', strtotime($reading_date));
		$year = date('Y', strtotime($reading_date));

		try {
			$existingReading = $this->checkReading($conn, $account_id, $month, $year, $meterno);

			if ($existingReading && count($existingReading) > 0) {

				// Duplicate reading
				$list['status'] = false;
				$list['message'] = 'Duplicate reading | This user already have a reading for this month';
				$this->saveLogs("error", "insert", $created_by, "[Mobile] Readings - tried to add duplicate reading of " . $accnt_name);

			} elseif ($this->getPreviousReading($account_id, $meterno) > $reading) {

				// Previous reading is greater than current reading
				$list['status'] = false;
				$list['message'] = 'Current reading must be greater than previous reading';
			} else {

				$sth = $conn->prepare("INSERT INTO hydra_billing.readings(`account_id`, `meterno`, `ref_no`, `reading_date`, `reading`, `created_by`, `created_at`, `ref_yr`, `ref_series`, `ref_month`, `status`) VALUES (:account_id, :meterno, :reference_no, :reading_date, :reading, :created_by, :created_at, :ref_yr, :ref_series, :ref_month, :status)");

				$sth->bindParam(':account_id', $account_id);
				$sth->bindParam(':meterno', $meterno);
				$sth->bindParam(':reference_no', $reference_no);
				$sth->bindParam(':reading_date', $reading_date);
				$sth->bindParam(':reading', $reading);
				$sth->bindParam(':created_by', $created_by);
				$sth->bindParam(':created_at', $created_at);
				$sth->bindParam(':ref_yr', $ref_yr);
				$sth->bindParam(':ref_series', $ref_series);
				$sth->bindParam(':ref_month', $ref_month);
				$sth->bindParam(':status', $status);

				if ($sth->execute()) {
					$reading_id = $conn->lastInsertId();
					$list['reading_id'] = $reading_id;
					$list['status'] = true;
					$this->saveLogs("Success", "insert", $created_by, "[Mobile] Readings - added reading " . $reading . " of " . $accnt_name);
				} else {
					$list['status'] = false;
					$this->saveLogs("Error", "insert", $created_by, "[Mobile] Readings - added reading " . $reading . " of " . $accnt_name);
				}
			}
		} catch (PDOException $e) {
			$list['status'] = false;
			$list['message'] = "Error: " . $e->getMessage();
        	$this->saveLogs("error", "insert", $created_by, "[Mobile] Readings - error adding reading " . $reading . " of " . $accnt_name . " - " . $e->getMessage());
		}

		array_push($response['response_array'], $list);
		echo json_encode($response);
	}

	public function update_reading(){
		$conn = $this->conn();
		$current_date = date("Y-m-d H:i:s");
		$response['response_array'] = array();

		$id = $_POST['id'];
		$reading_date = $_POST['reading_date'];
		$reading = $_POST['reading'];
		$updated_by = $_POST['updated_by'];
		$updated_at = $current_date;

		$sth = $conn->prepare("UPDATE hydra_billing.readings 
							   SET `reading_date`='$reading_date',`reading`='$reading',`updated_by`='$updated_by',`updated_at`='$updated_at' 
							   WHERE id='$id'");
		$sth->execute();

		if ($sth) {
   			$list['status'] = 'success';
   			$this->saveLogs("success", "update", $updated_by, "[Mobile] Readings - update ".$reading);
   		} else {
   			$list['status'] = 'error';
   			$this->saveLogs("error", "update", $updated_by, "[Mobile] Readings - update ".$reading);
   		}

		array_push($response['response_array'], $list);
		echo json_encode($response);
	}

	private function getSeries($month,$year,$query){
    	$conn = $this->conn();

    	$list = array();

    	$sth = $conn->prepare("SELECT ref_series 
							   FROM $query 
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

	public function getAccountName($account_id){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT CONCAT(firstname,' ',lastname) AS account_name
							   FROM hydra_billing.accounts 
							   WHERE id = :account_id");
		$sth->bindParam(':account_id', $account_id);
		$sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);

		if ($row) {
			return utf8_encode($row['account_name']);
		} else {
			return "No name";
		}
	}

	function checkNum($number) {
	  if($number>1) {
	    throw new Exception("Value must be 1 or below");
	  }
	  return true;
	}

	public function updateReadingStatus($status,$is_billed,$reading_id){
		$conn = $this->conn();
		$sth = $conn->prepare("UPDATE hydra_billing.readings 
							   SET status='$status', is_billed='$is_billed' 
							   WHERE id='$reading_id'");
		$sth->execute();
	}

	public function generate_bill(){
		$conn = $this->conn();
		$current_date = date("Y-m-d H:i:s");
		$response['response_array'] = array();
		$list = array();

		$reading_id = $_POST["reading_id"];
		$user_id = $_POST["user_id"];
		$ref_yr = substr($current_date, 2, 2);
	   	$ref_month = substr($current_date, 5, 2);
	   	$ref_series = $this->getSeries($ref_month,$ref_yr,"hydra_billing.bills");
	   	$reference_no = 'BHBR' . $ref_yr . '-' . $ref_month . '-' . $ref_series;
	   	$status = 1;

		try {
			if (isset($_POST['reading_id']) && $_POST['reading_id'] != "" && $_POST['reading_id'] != 0 && isset($_POST['user_id']) && $_POST['user_id'] != "" && $_POST['user_id'] != 0) {
				$billingDetails = $this->getBillingDetails($reading_id);
				$billing_from = $billingDetails["billing_from"];
				$billing_to = $billingDetails["billing_to"];
				$due_date = $billingDetails["due_date"];
				$prev_reading_id = $billingDetails["prev_reading_id"];
				$account_id = $billingDetails["account_id"];
				$current = $billingDetails["current"];
				$previous = $billingDetails["previous"];
				$totalUsage = $billingDetails["usage"];
				$rate = $billingDetails["rate"];
				$charges = $billingDetails["total_charges"];
				$model = $billingDetails["model"];
				$name = $billingDetails["name"];
				$subdivision = $billingDetails["subdivision"];
				$over_payment = $billingDetails["over_payment"];
				$reconnectionFee = $billingDetails["reconnectionFee"];
				$total_balance = $billingDetails["total_balance"];
				$actual_current_bill = $billingDetails["actual_current_bill"];
				$total_penalty = $billingDetails["total_penalty"];
				$total_amount_due = $billingDetails["total_amount_due"];
				$reading_ref_no = $billingDetails["reading_ref_no"];
				$current_reading_date = $billingDetails["current_reading_date"];
		
				if (!$rate) {
					$list["status"] = false;
					$list["message"] = "No data found in Setup - Rate";
				} elseif (!$billingDetails["dayOf_cutOff"]) {
					$list["status"] = false;
					$list["message"] = "No data found in Setup - Cut off period";
				} elseif (!$billingDetails["dayOf_dueDate"]) {
					$list["status"] = false;
					$list["message"] = "No data found in Setup - Due date";
				} elseif ($this->checkClientIsBilled($account_id, $current_reading_date) > 0) {
					$list["status"] = false;
					$list["message"] = "This account is already billed in this month";
				} elseif ($billingDetails["account_status"] == 0) {
					$list["status"] = false;
					$list["message"] = "This account was inactive, please contact the finance officer.";
				} elseif ($billingDetails["account_is_archive"] == 1) {
					$list["status"] = false;
					$list["message"] = "This account was archived, please contact the finance officer.";
				} else {
					if (isset($_POST['reading_id']) && $_POST['reading_id'] != "" && $_POST['reading_id'] != 0 && isset($_POST['user_id']) && $_POST['user_id'] != "" && $_POST['user_id'] != 0) {
						$sth = $conn->prepare("INSERT INTO hydra_billing.bills(`billing_from`, `billing_to`, `due_date`, `ref_no`, `ref_series`, `ref_yr`, `ref_month`, `created_by`, `created_at`, `status`, `reading_id`, `prev_reading_id`, `account_id`, `current`, `previous`, `usage`, `rate`, `total_charges`, `actual_current_bill`)
						VALUES (:billing_from, :billing_to, :due_date, :reference_no, :ref_series, :ref_yr, :ref_month, :user_id, :current_date, :status, :reading_id, :prev_reading_id, :account_id, :current, :previous, :totalUsage, :rate, :charges, :actual_current_bill)");
		
						$sth->bindParam(':billing_from', $billing_from);
						$sth->bindParam(':billing_to', $billing_to);
						$sth->bindParam(':due_date', $due_date);
						$sth->bindParam(':reference_no', $reference_no);
						$sth->bindParam(':ref_series', $ref_series);
						$sth->bindParam(':ref_yr', $ref_yr);
						$sth->bindParam(':ref_month', $ref_month);
						$sth->bindParam(':user_id', $user_id);
						$sth->bindParam(':current_date', $current_date);
						$sth->bindParam(':status', $status);
						$sth->bindParam(':reading_id', $reading_id);
						$sth->bindParam(':prev_reading_id', $prev_reading_id);
						$sth->bindParam(':account_id', $account_id);
						$sth->bindParam(':current', $current);
						$sth->bindParam(':previous', $previous);
						$sth->bindParam(':totalUsage', $totalUsage);
						$sth->bindParam(':rate', $rate);
						$sth->bindParam(':charges', $charges);
						$sth->bindParam(':actual_current_bill', $actual_current_bill);
		
						if ($sth->execute()) {
							$bill_id = $conn->lastInsertId();
							$this->updateReadingStatus("Billed", "1", $reading_id);
							$this->saveLogs("success", "insert", $user_id, "[Mobile] Readings - Generate bill of " . $reading_ref_no);
		
							$list["status"] = true;
							$list["message"] = "Successfully generate bill.";
							$list['model'] = $model;
							$list['bill_id'] = $bill_id;
							$list['name'] = strtoupper($name);
							$list['subdivision'] = $subdivision;
							$list['previous'] = $previous;
							$list['current'] = $current;
							$list['rate'] = $rate;
							$list['usage'] = $totalUsage;
							$list['total_charges'] = $charges;
							$list['actual_current_bill'] = $actual_current_bill;
							$list['billing_date'] = $billing_from . ' - ' . $billing_to;
							$list['due_date'] = $due_date;
							$list['over_payment'] = $over_payment;
							$list['reconnectionFee'] = $reconnectionFee;
							$list['total_balance'] = $total_balance;
							$list['total_penalty'] = $total_penalty;
							$list['total_amount_due'] = $total_amount_due;
							$list['ref_no'] = $reference_no;
						} else {
							$list["status"] = false;
							$list["message"] = "Query error, Failed to save.";
							$this->saveLogs("error", "insert", $user_id, "[Mobile] Readings - Generate bill of " . $reading_ref_no);
						}
					} else {
						$list["status"] = false;
						$list["message"] = "Theres a problem in second validation of user id or reading id";
					}
				}
			} else {
				$list["status"] = false;
				$list["message"] = "Theres a problem in first validation of user id or reading id";
			}
		} catch (Exception $e) {
			$list["status"] = false;
			$list["message"] = "Error: " . $e->getMessage();
			$this->saveLogs("Error", "insert", $user_id, "[Mobile] Readings - Generate bill of " . $reading_ref_no . " - " . $e->getMessage());
		}
        
        array_push($response['response_array'], $list);
	   	echo json_encode($response);
	}

	private function getBillingDetails($reading_id){
		$list = array();
		$reconnection_fee = $this->getReconnectionFee();
		$dayOf_cutOff = $this->getAppliedCutOff();
        $dayOf_dueDate = $this->getAppliedDueDate();
        $rate = $this->getAppliedRate();

        $currentReading = $this->getCurrentReadingDetails($reading_id);
        $account_id = $currentReading['account_id'];
        $meterno = $currentReading['meterno'];
        $current_reading = $currentReading['reading'];
        $current_reading_date = $currentReading['reading_date'];

        $previousReading = $this->getPreviousReadingDetails($account_id, $meterno, $current_reading_date);

		if ($previousReading) {
			$prev_reading_id = $previousReading['reading_id'];
			$prev_reading = $previousReading['reading'];
			$prev_reading_date = $previousReading['reading_date'];
		} else {
			$prev_reading_id = 0;
			$prev_reading = 0.00;
			$prev_reading_date = '0000-00-00';
		}

        $totalUsage = $this->computeTotalUsage($current_reading, $prev_reading);
        $charges = $this->computeTotalCharges($rate, $totalUsage);

		$actual_current_bill = $this->computeActualCurrentBill($rate, $totalUsage);
        $over_payment = $this->computeOverPayment($account_id);
        $balance_last_bill = $this->computeBalanceLastBill($account_id);
        
        $billing_from = $this->getBillingDateFrom($prev_reading_date, $current_reading_date, $dayOf_cutOff);
        $billing_to = date('Y-m-d', strtotime($current_reading_date));
        $due_date = date('Y-m-d', strtotime("+".$dayOf_dueDate." day", strtotime($billing_to)));

        $reconnectionFee = $currentReading['is_disconnected']==1 ? $reconnection_fee : '0.00';
        $total_amount_due = ($charges + $balance_last_bill['total_penalty'] + $balance_last_bill['total_balance'] + $reconnectionFee) - $over_payment;

        $list["current_reading_date"] = $current_reading_date;
        $list["reading_ref_no"] = $currentReading["reading_ref_no"];
        $list["account_status"] = $currentReading["account_status"];
        $list["account_is_archive"] = $currentReading["account_is_archive"];
        $list["dayOf_cutOff"] = $dayOf_cutOff;
        $list["dayOf_dueDate"] = $dayOf_dueDate;
        $list["name"] = $currentReading["name"];
        $list["is_billed"] = $currentReading["is_billed"];
        $list["model"] = $currentReading["model"];
        $list["subdivision"] = $currentReading["subdivision"];
	   	$list["billing_from"] = $billing_from;
        $list["billing_to"] = $billing_to;
        $list["due_date"] = $due_date;
	   	$list["prev_reading_id"] = $prev_reading_id;
	   	$list["account_id"] = $account_id;
	   	$list["current"] = number_format($current_reading, 2,'.','');
        $list["previous"] = number_format($prev_reading, 2,'.','');
        $list["usage"] = number_format($totalUsage, 2,'.','');
	   	$list["rate"] = number_format($rate, 2,'.','');
        $list["total_charges"] = number_format($charges, 2,'.','');
		$list["actual_current_bill"] = number_format($actual_current_bill, 2,'.','');
        $list["over_payment"] = number_format($over_payment, 2,'.','');
        $list["total_penalty"] = number_format($balance_last_bill['total_penalty'], 2,'.','');
        $list["total_balance"] = number_format($balance_last_bill['total_balance'], 2,'.','');
        $list["total_amount_due"] = number_format($total_amount_due, 2,'.','');
        $list["reconnectionFee"] = number_format($reconnectionFee, 2,'.','');
        return $list;
	}

	function checkClientIsBilled($account_id, $current_reading_date){
        $current_m = date('m', strtotime($current_reading_date));
        $current_y = date('Y', strtotime($current_reading_date));
        $conn = $this->conn();
		$sth = $conn->prepare("SELECT id 
							   FROM hydra_billing.bills 
							   WHERE account_id='$account_id' AND status='1' AND YEAR(billing_to)='$current_y' AND MONTH(billing_to)='$current_m'");
		$sth->execute();
		return $sth->rowCount();
    }

	// current_y & current_m added in the condition for not include the current billing.
	private function computeBalanceLastBill($account_id) {
		try {
			$current_bill_id = $this->get_current_bill_id($account_id);
			$array = array();
			$conn = $this->conn();
			$current_date = date("Y-m-d");
			$current_y = date("Y");
			$current_m = date("m");
			$penalties = $this->getPenalties();
			$total_balance = 0;
			$total_penalty = 0;
			$sth = $conn->prepare("SELECT id, total_charges, due_date, billing_to
								   FROM hydra_billing.bills
								   WHERE account_id = :account_id
								   AND is_paid = '0'
								   AND status = '1'
								   AND id != :current_bill_id
								   AND (month(billing_to) != :current_m
								   OR year(billing_to) != :current_y)");
			$sth->bindParam(':current_bill_id', $current_bill_id, PDO::PARAM_INT);
			$sth->bindParam(':account_id', $account_id, PDO::PARAM_INT);
			$sth->bindParam(':current_m', $current_m, PDO::PARAM_INT);
			$sth->bindParam(':current_y', $current_y, PDO::PARAM_INT);
			$sth->execute();
			while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				$bill_payments = $this->computeBillsPaid($row['id']);
				$overdue_charges = 0;

				if ($current_date > $row['due_date']) {
					if ($penalties['type'] == 'percentage') {
						$overdue_charges = ($penalties['amount'] / 100) * $row['total_charges'];
					} else {
						$overdue_charges = $penalties['amount'];
					}
				}

				$total = $row['total_charges'] - $bill_payments;

				$total_penalty += $overdue_charges;
				$total_balance += $total;
			}
	
			$array['total_penalty'] = $total_penalty;
			$array['total_balance'] = $total_balance;
			return $array;
		} catch (PDOException $e) {
			// Handle the exception
			$err = error_log("Error in computeBalanceLastBill: " . $e->getMessage());
			return array('total_penalty' => 0, 'total_balance' => 0, 'err_msgg' => $err); // or handle it in a way that makes sense for your application
		}
	}

	public function get_current_bill_id($account_id) {
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT id 
							   FROM hydra_billing.bills 
							   WHERE account_id = :account_id 
							   ORDER BY billing_to DESC 
							   LIMIT 1");
		$sth->bindParam(':account_id', $account_id, PDO::PARAM_INT);
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		return $result ? $result['id'] : null;
	}

	public function computeBillsPaid($bill_id){
		try {
			$conn = $this->conn();
			$arrData = array();
	
			$sth = $conn->prepare("SELECT * FROM hydra_billing.payments WHERE bill_id = :bill_id AND is_archive = 0");
			$sth->bindParam(':bill_id', $bill_id, PDO::PARAM_INT);
			$sth->execute();
	
			$partialAmount = 0;
			while ($tempData = $sth->fetch(PDO::FETCH_ASSOC)) {
				$actual_amount = $tempData['received_amount'];
				$partialAmount += $actual_amount;
			}
			return $partialAmount;
		} catch (PDOException $e) {
			// Handle the exception
			error_log("Error in computeBillsPaid: " . $e->getMessage());
			return 0; // or handle it in a way that makes sense for your application
		}
	}

	public function update_print(){
		$conn = $this->conn();
		$response['response_array'] = array();
		$list = array();
		$id = $_POST["bill_id"];
		$user_id = $_POST["user_id"];
		$ref_no = $_POST["ref_no"];
        $print_count = $this->fetchPrintCount($id);
        $print_count = $print_count + 1;
        $sth = $conn->prepare("UPDATE hydra_billing.bills 
        					   SET print_count='$print_count'
        					   WHERE id='$id'");
        $sth->execute();
        if ($sth) {
        	$list["status"] = true;
   			$this->saveLogs("success", "print", $user_id, "[Mobile] Billing - print ".$ref_no);
        } else {
        	$list["status"] = false;
        }

        array_push($response['response_array'], $list);
	   	echo json_encode($response);
	}

	private function fetchPrintCount($id){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT print_count
							   FROM hydra_billing.bills 
							   WHERE id='$id'");
		$sth->execute();
		return $sth->fetch()["print_count"];
	}

	private function computeOverPayment($account_id){
		try {
			$conn = $this->conn();
			$sth = $conn->prepare("SELECT SUM(received_amount - net_payment) as balance, SUM(balance_covered) as balance_covered 
								   FROM hydra_billing.payments
								   WHERE account_id = :account_id AND is_archive = '0'");
			$sth->bindParam(':account_id', $account_id, PDO::PARAM_INT);
			$sth->execute();
			$result = $sth->fetch(PDO::FETCH_ASSOC);
			$balance = (float)$result['balance'] - (float)$result['balance_covered'];
			return ($balance < 0) ? number_format(0, 2, '.', '') : number_format($balance, 2, '.', '');
		} catch (PDOException $e) {
			// Handle the exception
			error_log("Error in computeOverPayment: " . $e->getMessage());
			return number_format(0, 2, '.', ''); // or handle it in a way that makes sense for your application
		}
	}

	private function getPenalties(){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT amount, type 
							   FROM hydra_billing.penalties 
							   LIMIT 1");
		$sth->execute();
		return $sth->fetch();
	}

	private function getReconnectionFee(){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT amount 
							   FROM hydra_billing.reconnection_fee 
							   LIMIT 1");
		$sth->execute();
		$result = $sth->fetch();
		return $result["amount"] ? $result["amount"] : '0.00';
	}

	private function getAppliedCutOff(){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT day 
							   FROM hydra_billing.cut_off_period 
							   LIMIT 1");
		$sth->execute();
		$result = $sth->fetch();
		return $result["day"] ? $result["day"] : '';
	}

	private function getAppliedDueDate(){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT day 
							   FROM hydra_billing.due_date 
							   LIMIT 1");
		$sth->execute();
		$result = $sth->fetch();
		return $result["day"] ? $result["day"] : '';
	}

	private function getAppliedRate(){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT rate 
							   FROM hydra_billing.rate 
							   LIMIT 1");
		$sth->execute();
		$result = $sth->fetch();
		return $result["rate"] ? $result["rate"] : '';
	}

	private function getCurrentReadingDetails($reading_id){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT a.reading_date, a.reading, a.account_id, a.is_billed, a.meterno, concat(b.firstname,' ',b.lastname) as name, b.model, c.name as subdivision, b.is_disconnected,
									  b.status as account_status, b.is_archive as account_is_archive, a.ref_no as reading_ref_no
							   FROM hydra_billing.readings a
							   LEFT JOIN hydra_billing.accounts b
							   ON a.account_id=b.id
							   LEFT JOIN hydra_billing.subdivision c
							   ON b.subdivision_id=c.id
							   WHERE a.id='$reading_id' AND a.is_archived='0'");
		$sth->execute();
		return $sth->fetch();
	}

	private function getPreviousReadingDetails($account_id,$meterno,$reading_date){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT id as reading_id, reading_date, reading
							   FROM hydra_billing.readings
							   WHERE account_id = :account_id AND meterno = :meterno AND reading_date < :reading_date AND is_archived = '0'
							   ORDER BY reading_date DESC
							   LIMIT 1");
		$sth->bindParam(':account_id', $account_id);
		$sth->bindParam(':meterno', $meterno);
		$sth->bindParam(':reading_date', $reading_date);
		$sth->execute();
		return $sth->fetch(PDO::FETCH_ASSOC);
	}

	private function computeTotalUsage($reading, $prev_reading){
		$usage = (float)$reading - (float)$prev_reading;
        return number_format((float)$usage, 2,'.','');
	}

	private function computeTotalCharges($rate, $usage){
        if($usage > 11.46) {
            $charges = (float)$usage * (float)$rate;
            return number_format((float)$charges, 2,'.','');
        }
        return $_ENV['HYDRA_MINIMUM_BILLING_AMOUNT'];
    }

	private function computeActualCurrentBill($rate, $usage){
		$charges = (float)$usage * (float)$rate;
		return number_format((float)$charges, 2,'.','');
	}

    function getBillingDateFrom($previousReadingDate, $currentReadingDate, $dayOf_cutOff){
        if($previousReadingDate){
            $countDiff = $this->countMonthDiff($previousReadingDate, $currentReadingDate);
            if($currentReadingDate > $previousReadingDate && $countDiff == 0){
                return $previousReadingDate;
            }
        }
        $date_from = date('Y-m', strtotime("-1 month", strtotime($currentReadingDate))).'-'.$dayOf_cutOff;
        return $date_from;
    }

    function countMonthDiff($prev_date, $current_date){
        $d1 = new DateTime($current_date);
        $d2 = new DateTime($prev_date);
        $Months = $d2->diff($d1);
        return (($Months->y) * 12) + ($Months->m);
    }

}

?>