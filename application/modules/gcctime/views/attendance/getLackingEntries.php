 <style>
#data {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

#data td, #data th {
    border: 1px solid #ddd;
    padding: 8px;
}

#data tr:nth-child(even){background-color: #f2f2f2;}

#data tr:hover {background-color: #ddd;}

#data th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
}
</style>
<?php	
	$yesterday = date('Y-m-d',strtotime("-1 days"));
	//$yesterday = date('2018-10-31');
	$query = $this->attendance->getyesterdayAttendance($yesterday);

	/*var_dump($query);

	die;*/
	$resultarray = array();
	if($query){
		foreach($query as $_query){
			$data = array();
			$getPersonnelData = $this->crud->load(array("biometric_id"=>$_query["biometric_id"]),"gcctimeutility.personnel");
			$personnelAttendanceData = $this->attendance->getPersonnelAttendanceByDate($yesterday,$_query["biometric_id"]);
			//var_dump($personnelAttendanceData);
			if(count($personnelAttendanceData) < 4){
				$data["cat"] = "lack";
				$data["biometric_id"] = $_query["biometric_id"];
			}else if(count($personnelAttendanceData) > 4){
				$data["cat"] = "double";
				$data["biometric_id"] = $_query["biometric_id"];
			}

			$resultarray[] = $data;

		}
	}

/*var_dump($resultarray);
die;*/
?>


<center><h2>Lacking</h2></center>
<table id="data">
	<tr>
		<th>Biometric #</th>
		<th>Name</th>
		<th>Time IN/OUT</th>
		<th>Count</th>
	</tr>
	<?php 
		if($resultarray){
			foreach($resultarray as $_resultarray){
				if(count($_resultarray) > 0){
				$getPersonnelData = $this->crud->load(array("biometric_id"=>$_resultarray["biometric_id"]),"gcctimeutility.personnel");
				$personnelAttendanceData = $this->attendance->getPersonnelAttendanceByDate($yesterday,$_resultarray["biometric_id"]);
					if($_resultarray["cat"] == "lack"){
						?>
							<tr>
								<td><?php echo $_resultarray["biometric_id"];?></td>
								<td><?php echo $getPersonnelData["name"];?></td>
								<td>
									<table>
										<tr>
											<?php
												foreach($personnelAttendanceData as $_personnelAttendanceData){
													?>
														<td><?php echo date_format(date_create($_personnelAttendanceData["datetime"]), "h:i A"); ?></td>
													<?php
												}
											?>
										</tr>
									</table>
								</td>
								<td><?php echo count($personnelAttendanceData);?></td>
							</tr>
						<?php
					}
				}
			}
		}
	?>
</table>
<br>
<br>
<hr>

<center><h2>Double Entry</h2></center>
<table id="data">
	<tr>
		<th>Biometric #</th>
		<th>Name</th>
		<th>Time IN/OUT</th>
		<th>Count</th>
	</tr>
	<?php 
		if($resultarray){
			foreach($resultarray as $_resultarray){
				if(count($_resultarray) > 0){
				$getPersonnelData = $this->crud->load(array("biometric_id"=>$_resultarray["biometric_id"]),"gcctimeutility.personnel");
				$personnelAttendanceData = $this->attendance->getPersonnelAttendanceByDate($yesterday,$_resultarray["biometric_id"]);
					if($_resultarray["cat"] == "double"){
						?>
							<tr>
								<td><?php echo $_resultarray["biometric_id"];?></td>
								<td><?php echo $getPersonnelData["name"];?></td>
								<td>
									<table>
										<tr>
											<?php
												foreach($personnelAttendanceData as $_personnelAttendanceData){
													?>
														<td><?php echo date_format(date_create($_personnelAttendanceData["datetime"]), "h:i A"); ?></td>
													<?php
												}
											?>
										</tr>
									</table>
								</td>
								<td><?php echo count($personnelAttendanceData);?></td>
							</tr>
						<?php
					}
				}
			}
		}
	?>
</table>
