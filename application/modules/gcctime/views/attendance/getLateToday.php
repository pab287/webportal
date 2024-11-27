<?php
	date_default_timezone_set("Asia/Taipei");
	$now = date("F d, Y A");
	$today = date("Y-m-d");

		$begin = new DateTime($today);
		$begin = $begin->format('Y-m-d H:i:s');

		$tom = new DateTime($today);
		$tom = $tom->modify('+1 day'); 

		$newtime = $tom->format('Y-m-d H:i:s');
		$newtime = new DateTime($newtime);
		$newtime = $newtime->modify("-1 second");
		$newtime = $newtime->format('Y-m-d H:i:s');

		$dates = array($begin,$newtime);
		$getAttendanceByDateRange = $this->attendance->getAttendanceByDateRange($dates,array());
			
		/* $Start = date('Y-m-d H:i:s', strtotime($begin));
		$End = date('Y-m-d H:i:s', strtotime($newtime)); */
		//echo "<pre>";
		$resultarray = array(); 
		$checker = array();
		if($getAttendanceByDateRange->num_rows() > 0)
		{
			foreach($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange)
			{
				$checkLate = $this->attendance->checkLate($_getAttendanceByDateRange["biometric_id"],$_getAttendanceByDateRange["datetime"]);
				$data = array();
				if($checkLate){
					$getPersonnel = $this->crud->load(array("biometric_id"=>$_getAttendanceByDateRange["biometric_id"]),"gcctimeutility.personnel");
					$getEmployeeData = $this->crud->load(array("biometricno"=>$_getAttendanceByDateRange["biometric_id"]),"gccmaster.tblemployees");
					$data["biometric_id"] = $_getAttendanceByDateRange["biometric_id"];
					$data["device"] = $_getAttendanceByDateRange["device_id"];
					$data["name"] = $getPersonnel["name"];
					$data["department_id"] = $getEmployeeData["department_id"];
					$data["date"] = date_format(date_create($_getAttendanceByDateRange["datetime"]), "n/d/Y");
					$data["time"] = date_format(date_create($_getAttendanceByDateRange["datetime"]), "h:i A");

					$datetime1 = new DateTime('2018-10-17 1:00:00');
					$datetime2 = new DateTime($_getAttendanceByDateRange["datetime"]);
					$interval = $datetime1->diff($datetime2);

					//$data["minlate"] = $interval->format('%i Mins');

					$getDepartment = $this->crud->load(array("id"=>$getPersonnel["department_id"]),"gcctimeutility.departments");
					$getLate = $this->crud->load(array("id"=>$getDepartment["late_id"]),"gcctimeutility.lates");

					$datestart = date_create($today . " ". $getLate["am_start"] . ":00");
					$dateend = date_create($_getAttendanceByDateRange["datetime"]);

					$diff = date_diff( $datestart, $dateend );

					$data["minlate"] = $diff->format("%i");

					$data["minlate"] += 1;
					$data["minlate"] = $data["minlate"]." Mins";
					//$data["minlate"] = $_getAttendanceByDateRange["datetime"] . "=======".$today . " ". $getLate["am_start"] .":00";

					$resultarray[] = $data;
					$checker[] = date_format(date_create($_getAttendanceByDateRange["datetime"]), "A");
				}
			}
		}

		//var_dump($checker);

		?>


		<p style='font-family: arial,sans-serif; font-size: 16px; color: #333; padding: 0 16px;'>Good day! <br />
		The following employees are tagged <span style="color: red;">LATE</span> today.</p>

		<table width='600' cellspacing='0' cellpadding='5' border='0' align='center'>
			<tr style="border: 1px solid #c3c3c3;">
				<td style="border: 1px solid #c3c3c3;"><strong>Biometric #</strong></td>
				<td style="border: 1px solid #c3c3c3;"><strong>Name</strong></td>
				<td style="border: 1px solid #c3c3c3;"><strong>Date</strong></td>
				<td style="border: 1px solid #c3c3c3;"><strong>Time</strong></td>
				<td style="border: 1px solid #c3c3c3;"><strong>Late (duration)</strong></td>
			</tr>
		<?php
		if($resultarray){
			foreach($resultarray as $_resultarray){
				$AMCheck = date_format(date_create($_resultarray["time"]), "A");
				//if($AMCheck == "AM"){
				?>
					<tr style="border: 1px solid #c3c3c3;">
						<td style="border: 1px solid #c3c3c3;"><?php echo $_resultarray["biometric_id"]; ?></td>
						<td style="border: 1px solid #c3c3c3;"><?php echo $_resultarray["name"]; ?></td>
						<td style="border: 1px solid #c3c3c3;"><?php echo $_resultarray["date"]; ?></td>
						<td style="border: 1px solid #c3c3c3;"><?php echo $_resultarray["time"]; ?></td>
						<td style="border: 1px solid #c3c3c3;"><?php echo $_resultarray["minlate"]; ?></td>
					</tr>
				<?php
				//}
			}
		}

		?>
		</table>
		
		<br />
		<hr style="border: 1px dashed;"> <br />
		<table border="0" align="center">	
			<tr>
				<td>This is a system generated message.</td>
			</tr>
		</table>
		<span style="text-align: center;"></span>

		<?php 
		 /*
			if(in_array("PM",$checker)){


				?>

					<p style='font-family: arial,sans-serif; font-size: 16px; color: #333; padding: 0 16px;'><strong>Late Report as of : <?php echo $now; ?></strong></p>

					<table width='600' cellspacing='0' cellpadding='0' border='0' align='center' style='max-width:600px; width:100%;'>
						<tr>
							<th align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><strong>Name</strong></th>
							<th align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><strong>Device</strong></th>
							<th align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><strong>Time In</strong></th>
							<th align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><strong>Minute/s Late</strong></th>
						</tr>
					<?php
					if($resultarray){
						foreach($resultarray as $_resultarray){
							$PMCheck = date_format(date_create($_resultarray["timein"]), "A");
							if($PMCheck == "PM"){

							?>
								<tr>
									<td align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><?php echo $_resultarray["name"]; ?></td>
									<td align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><?php echo $_resultarray["device"]; ?></td>
									<td align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><?php echo $_resultarray["timein"]; ?></td>
									<td align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><?php echo $_resultarray["minlate"]; ?></td>
								</tr>
							<?php
							}
						}
					}

					?>
					</table>

				<?php
			}*/

		?>