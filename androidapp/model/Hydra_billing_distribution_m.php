<?php

class Hydra_billing_distribution_m extends Dbase{
    
	public function get_subdivision_details(){
		$conn = $this->conn();
		$current_date = date('Y-m-d');
		$response['subdivision_details'] = array();
		$meterno = $_POST['meterno'];

		$sth = $conn->prepare("SELECT * 
							   FROM hydra_billing.subdivision 
							   WHERE meterno_raw='$meterno'");
   		$sth->execute();

   		if ($sth->rowCount() > 0) {
   			$row = $sth->fetch(PDO::FETCH_ASSOC);
		    $list['status'] = "exist";
	 		$list['id'] = $row['id'];
	 		$list['name'] = ucfirst($row['name']);
	 		$list['address'] = utf8_encode($row['address']);
	 		$list['subdivision_status'] = $row['status'];
	 		$list['description'] = $row['description'];
	 		$list['meterno'] = $row['meterno'];
	 		$list['previous_distribute'] = $this->getPreviousDistribute($row['id']);
	 		$list['isAlreadyDistribute'] = $this->checkDistribute($conn, $row['id']);
   		} else {
			$list['status'] = "not exist";
   		}

		array_push($response['subdivision_details'], $list);
   		echo json_encode($response);
	}

	public function checkDistribute($conn, $subdivision_id){
		$current_year = date('Y');
		$current_month = date('m');
		$sth = $conn->prepare("SELECT id 
							   FROM hydra_billing.distribution 
							   WHERE subdivision_id='$subdivision_id' AND year(reading_date)='$current_year' AND month(reading_date)='$current_month'");
   		$sth->execute();
   		return $sth->rowCount();
	}

	private function getPreviousDistribute($subdivision_id){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT distribute 
							   FROM hydra_billing.distribution 
							   WHERE subdivision_id='$subdivision_id' 
							   ORDER BY `reading_date` DESC 
							   LIMIT 1");
		$sth->execute();
		$result = $sth->fetch();
		return $result["distribute"] ? $result["distribute"] : '0';
	}

	private function getPreviousDistributeEdit($subdivision_id, $reading_date){
		$conn = $this->conn();
		$sth = $conn->prepare("SELECT distribute 
							   FROM hydra_billing.distribution 
							   WHERE subdivision_id='$subdivision_id' AND reading_date<'$reading_date' 
							   ORDER BY `reading_date` DESC 
							   LIMIT 1");
		$sth->execute();
		$result = $sth->fetch();
		return $result["distribute"] ? $result["distribute"] : '0';
	}

	public function save_distribution(){
		$conn = $this->conn();
		$current_date = date("Y-m-d H:i:s");
		$response['response_array'] = array();

		$subdivision_id = $_POST['subdivision_id'];
		$reading_date = $_POST['reading_date'];
		$distribute = $_POST['distribute'];
		$meterno = $_POST['meterno'];
		$created_by = $_POST['created_by'];
		$created_at = $current_date;

		$sth = $conn->prepare("INSERT INTO hydra_billing.distribution(`meterno`, `distribute`, `subdivision_id`, `created_by`, `created_date`, `reading_date`) 
							   VALUES ('$meterno','$distribute','$subdivision_id','$created_by','$created_at','$reading_date')");
   		$sth->execute();

   		if ($sth) {
   			$list['status'] = 'success';
   			$this->saveLogs("success", "insert", $created_by, "[Mobile] Distribution - added ".$distribute);
   		} else {
   			$list['status'] = 'error';
   			$this->saveLogs("error", "insert", $created_by, "[Mobile] Distribution - added ".$distribute);
   		}

		array_push($response['response_array'], $list);
		echo json_encode($response);
	}

	public function fetch_history(){
		$conn = $this->conn();
		$response['distribution_details'] = array();
		$current_m = date('m');
		$current_y = date('Y');

		$user_id = $_POST['user_id'];

		$sth = $conn->prepare("SELECT a.*, b.name as subdivision
							   FROM hydra_billing.distribution a
							   LEFT JOIN hydra_billing.subdivision b
							   ON a.subdivision_id = b.id
							   WHERE a.created_by='$user_id' AND a.is_archive='0' AND a.meterno = b.meterno_raw AND month(a.created_date)='$current_m' AND year(a.created_date)='$current_y'
							   ORDER BY a.id DESC");
   		$sth->execute();

   		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	 		$list['id'] = $row['id'];
	 		$list['distribute'] = number_format($row["distribute"],2);
	 		$list['subdivision'] = $row['subdivision'];
	 		$list['reading_date'] = $row['reading_date'];
	 		$list['previous_distribute'] = $this->getPreviousDistributeEdit($row['subdivision_id'],$row['reading_date']);
	 		$list['created_time'] = date("F j, Y, g:i A", strtotime($row['created_date']));
			array_push($response['distribution_details'], $list);
		}

   		echo json_encode($response);
	}

	public function update_distribution(){
		$conn = $this->conn();
		$current_date = date("Y-m-d H:i:s");
		$response['response_array'] = array();

		$id = $_POST['id'];
		$reading_date = $_POST['reading_date'];
		$subdivision = $_POST['subdivision'];
		$distribute = $_POST['distribute'];
		$updated_by = $_POST['updated_by'];
		$updated_at = $current_date;

		$sth = $conn->prepare("UPDATE hydra_billing.distribution 
							   SET `distribute`='$distribute',`updated_by`='$updated_by',`updated_date`='$updated_at',`reading_date`='$reading_date' 
							   WHERE id='$id'");
   		$sth->execute();

   		if ($sth) {
   			$list['status'] = 'success';
   			$this->saveLogs("success", "update", $updated_by, "[Mobile] Distribution - update into ".$distribute." subdivision of ".$subdivision);
   		} else {
   			$list['status'] = 'error';
   			$this->saveLogs("error", "update", $updated_by, "[Mobile] Distribution - update into ".$distribute." subdivision of ".$subdivision);
   		}

		array_push($response['response_array'], $list);
		echo json_encode($response);
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

}

?>