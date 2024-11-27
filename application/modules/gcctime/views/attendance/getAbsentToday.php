<?php

	date_default_timezone_set("Asia/Taipei");
		$now = date("F d, Y A");
		$nowsf = date("F d, Y H:i:s");
		$nows = new DateTime($nowsf);
		//$nows = $nows->format('Y-m-d H:i:s');

		$todays = date("Y-m-d")." 00:00:00";

		$begin = new DateTime($todays);
		$begin = $begin->format('Y-m-d H:i:s');
		//var_dump($todays);
		// echo (new \DateTime())->format('Y-m-d H:i:s');

		$tom = new DateTime($todays);
		$tom = $tom->modify('+1 day'); 

		$newtime = $tom->format('Y-m-d H:i:s');
		$newtime = new DateTime($newtime);
		$newtime = $newtime->modify("-1 second");
		$newtime = $newtime->format('Y-m-d H:i:s');

		//echo $nows."<br>";
		//echo $newtime;

		$content = "";

		$content .= "<p>Good day!</p>";
		$content .= "<p>The names listed below were unable to login to the biometric device and are considered absent.</p>";

		$getAbsentCollection = $this->crud->getCollection(array("updated_at >="=>$todays,"updated_at <="=>$newtime),"gcctimeutility.absent");

		//var_dump($getAbsentCollection);
		$data = array();
		$checker = array();
		if($getAbsentCollection){
			foreach($getAbsentCollection as $_getAbsentCollection){
				$getPersonnel = $this->crud->load(array("biometric_id"=>$_getAbsentCollection["biometric_id"]),"gcctimeutility.personnel");
				$getEmployeeData = $this->crud->load(array("biometricno"=>$_getAbsentCollection["biometric_id"]),"gccmaster.tblemployees");
				$getLOA = "";
				$getLOA = $this->crud->getCollection(array("employee"=>$getEmployeeData["id"]),"gcceforms.loa");
				$newLOAData = "false";

				$catch = array();
				if($getLOA){
					foreach($getLOA as $_getLOA){

						if (substr($_getLOA["date_from"], 11, 2) == "8") {
							$ampm = "AM";
						} else {
							$ampm = "PM";
						}	

						$datefrom = new DateTime();
						//$datefrom = $datefrom->format('Y-m-d H:i:s');

						$dateto = new DateTime($_getLOA["date_to"]);
						//$dateto = $dateto->format('Y-m-d H:i:s');
						
						switch ($_getLOA["type"]) {
							case '1':
							// if(date($getLOA["date_from"],"Y-m-d H:i:s") >= date_format(date_create($today),"Y-m-d H:i:s") && date_format(date_create($getLOA["date_to"]),"Y-m-d H:i:s") >= date_format(date_create($today),"Y-m-d H:i:s")){
							// 	$newLOAData = true;
							// }
							if($nows->getTimestamp() >= $datefrom->getTimestamp() && $nows->getTimestamp() <= $dateto->getTimestamp()){
								$catch["absent_id"] = $_getAbsentCollection["id"];
								$catch["loaid"] = $_getLOA["id"];
								$catch["status"] = "positive";
							}
							break;
							case '2':

								if($nows->getTimestamp() >= $datefrom->getTimestamp() && $nows->getTimestamp() <= $dateto->getTimestamp()){
									if($_getAbsentCollection["meredien"] === $ampm){
									$catch["absent_id"] = $_getAbsentCollection["id"];
									$catch["loaid"] = $_getLOA["id"];
									$catch["status"] = "positive";
									}
								}
								# code...
								break; 
							case '3':
							if($nows->getTimestamp() >= $datefrom->getTimestamp() && $nows->getTimestamp() <= $dateto->getTimestamp()){
								$catch["absent_id"] = $_getAbsentCollection["id"];
								$catch["loaid"] = $_getLOA["id"];
								$catch["status"] = "positive";
							}
								# code...
								break;
							case '4':
							if($nows->getTimestamp() >= $datefrom->getTimestamp() && $nows->getTimestamp() <= $dateto->getTimestamp()){
								$catch["absent_id"] = $_getAbsentCollection["id"];
								$catch["loaid"] = $_getLOA["id"];
								$catch["status"] = "positive";
							}
								# code...
								break;
							default:
								$catch["stat_loaid"] = null;
								break;
						}

					}
				}

				//var_dump($catch);
				$getNewLOA = false;
				$loacontent = "";
				if(in_array("positive", $catch, true)){
					$getNewLOA = $this->crud->load(array("id"=>$catch["loaid"]),"gcceforms.loa");
					$loacontent = $getNewLOA ? "<br/>LOA Reference No: ". $getNewLOA["reference_no"] : "N/A";
				}else{
					$loacontent = "N/A";
				}

				$meredien = date_format(date_create($_getAbsentCollection["updated_at"]),"A");

					$datum = array();
					$datum["name"] = $getPersonnel["name"];
					$datum["department"] = $getEmployeeData["department_id"];
					$datum["content"] = $loacontent;
					$datum["mrdn"] = $meredien;

					$data[] = $datum;
					$checker[] = $meredien;


					
			}
		}

		$content .= "<p style='font-family: arial,sans-serif; font-size: 16px; color: #333; padding: 0 16px;'><strong>Absentee Report as of : ".$now."</strong></p>";


		$content .= "<table width='600' cellspacing='0' cellpadding='0' border='0' align='center' style='max-width:600px; width:100%;'>";
						$content .= "<tr>";
							$content .= "<th align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><strong>Name</strong></th>";
							$content .= "<th align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><strong>Department</strong></th>";
							$content .= "<th align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><strong>LOA/TO Ref.#</strong></th>";
						$content .= "</tr>";

		foreach($data as $_data){
			if($_data["mrdn"] == "AM"){
					$content .= "<tr>";
						$content .= "<td style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'>".$_data["name"]."</td>";
						$content .= "<td style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'>".$_data["department"]."</td>";
						$content .= "<td style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'>".$_data["content"]."</td>";
					$content .= "</tr>";
				}
		}

		$content .= "</table>";

		if(in_array("PM", $checker)){

			$content .= "<p style='font-family: arial,sans-serif; font-size: 16px; color: #333; padding: 0 16px;'><strong>Absentee Report as of : ".$now."</strong></p>";

			$content .= "<table width='600' cellspacing='0' cellpadding='0' border='0' align='center' style='max-width:600px; width:100%; '>";
			$content .= "<tr>";
							$content .= "<th align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><strong>Name</strong></th>";
							$content .= "<th align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><strong>Department</strong></th>";
							$content .= "<th align='left' style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'><strong>LOA/TO Ref.#</strong></th>";
						$content .= "</tr>";


			foreach($data as $_data){
					if($_data["mrdn"] == "PM"){
						$content .= "<tr>";
							$content .= "<td style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'>".$_data["name"]."</td>";
							$content .= "<td style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'>".$_data["department"]."</td>";
							$content .= "<td style='font-family: arial,sans-serif; font-size: 12px; color: #333; padding: 0 16px;border: 1px solid #c3c3c3;'>".$_data["content"]."</td>";
						$content .= "</tr>";
					}
			}
			$content .= "</table>";

		}

	    echo $content;

?>