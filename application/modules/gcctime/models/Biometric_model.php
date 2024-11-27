<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Biometric_model extends CI_Model {
	private $today;
	private $gcctimeDevices = "gcctimeutility.devices";
	private $gcctimeAttendance = "gcctimeutility.attendance";
	private $gcctimeEvent = "gcctimeutility.event";
	private $staticHookUrl = "https://apps.conyxph.com/web/gcctime/curl_request/hook_attendance_data";
	
	public function __construct(){
		parent::__construct();
		date_default_timezone_set("Asia/Manila");
		$this->today = date("Y-m-d");
		//include(BASEPATH.'libraries/zklibrary.php');
	}
	
	private function getDevice($id=null){
		$tempWhere = array();
		$tempWhere["status"] = 1;
		if($id){ $tempWhere["id"] = $id; }

		$query = $this->db->get_where($this->gcctimeDevices, $tempWhere);
		if($query->num_rows() == 1){
			return $query->row();
		}else{
			return false;
		}
	}
	
	private function getAllDevice(){
		$this->db->from($this->gcctimeDevices);
		$query = $this->db->get();
		
		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}
	
	public function ipIsReachable($ip=null, $port=80){
		if($ip){
			$port = $port ? $port: 80; 
			$url = "{$ip}";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_PORT, $port);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			if($httpcode>=200 && $httpcode<300){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function getStoredData($overrideDate=null, $dateFilter=null){
		$resultset = array();
		
		$dateTime = ($overrideDate)? date("dYm", strtotime($overrideDate)): date("dYm");
		$tempDate = ($overrideDate)? date("dYm", strtotime("+1 day", $overrideDate)): date("dYm", strtotime("+1 day"));

		$filepath = realpath("./uploads/data");
		
		$scanned_directory = array_diff(scandir($filepath), array('..', '.'));
		$files = array();
		if($scanned_directory){
			foreach($scanned_directory as $dfile){
				$filename = explode(".", $dfile);
				if(count($filename) == 2){
					$fname = explode("-", $filename[0]);
					if(count($fname) == 2){
						if($dateTime == $fname[1]){ $files[] = $dfile; }
						if($tempDate == $fname[1]){ $files[] = $dfile; }
					}
				}
			}
		}
		
		if($files){
			$nData = array();
			
			foreach($files as $file){
				$fileUpload = "{$filepath}/{$file}";
				$xfiles = file_get_contents($fileUpload, true);
				$_file = json_decode($xfiles, true); 
				$attendanceLogs = (isset($_file["attendance_log"]) && $_file["attendance_log"])? $_file["attendance_log"]: array();
				$deviceId = (isset($_file["device_id"]) && $_file["device_id"])? $_file["device_id"]: 0;
				if($attendanceLogs){
					$currentDate = ($dateFilter)? Date("Y-m-d", strtotime($dateFilter)): Date("Y-m-d");
					foreach($attendanceLogs as $key => $value){
						$cDate = Date("Y-m-d", strtotime($value[3]));
						if($currentDate == $cDate){
							$value[] = intval($deviceId);
							$nData[] = $value;
						}
					}
				}
			}
			
			if($nData && count($nData) > 0){
				$resultset["response"] = true;
				$resultset["data"] = $nData;
				$resultset["files"] = $files;
				$resultset["message"] = "Data on the textfile has been generated.";
			}else{
				$resultset["response"] = false;
				$resultset["message"] = "No generated data from the textfile!";
			}
		}else{
			$resultset["response"] = false;
			$resultset["message"] = "No files found!";
		}
		
		return $resultset;
	}
	
	private function checkAttendanceData($biometric_id = NULL, $datetime = NULL){
		$query = $this->db->get_where($this->gcctimeAttendance, array("biometric_id"=>$biometric_id,"datetime"=>$datetime));
		if($query->num_rows() == 1){
			return true;
		}else{
			return false;
		}
	}
	
	private function allowDownloadData(){
		$date = date("Y-m-d");
        $this->db->from($this->gcctimeEvent);
		$this->db->where("event_name", "error_download_data");
		$this->db->like("created_at", $date);
		$query = $this->db->get();
		
		if($query->num_rows() >= 0 && $query->num_rows() <= 10){
			return true;
		}else{
			return false;
		}
	}
	
	private function allowSyncData(){
		$date = date("Y-m-d");
        $this->db->from($this->gcctimeEvent);
		$this->db->where("event_name", "error_sync_data");
		$this->db->like("created_at", $date);
		$query = $this->db->get();
		
		if($query->num_rows() >= 0 && $query->num_rows() <= 20){
			return true;
		}else{
			return false;
		}
	}
	
	function insertEvent($event="sync_data"){
		$insert = $this->db->insert($this->gcctimeEvent, array("event_name"=>$event));
		if($insert){ return true; }
		else{ return false; }
	}
	
	
	function biometricSync($alldevice=false, $id=null){
		$resp0 = $this->getBiometricData($alldevice, $id);

		return $resp0;
		if($resp0["status"] == true){
			$resp1 = $this->setBiometricData();
			if($resp1){
				return $resp0["data"];
			}else{
				return $resp0;
			}
		}else{
			return $resp0;
		}

	}
	
	function manualSetBiometricData($date=null, $dateFilter=null){
		$resultset = array();
		$syncData = $this->getStoredData($date, $dateFilter);
		if(isset($syncData["response"]) && $syncData["response"] == true){
			if(isset($syncData["data"]) && $syncData["data"]){
				$counter = 0;
				$syncedCounter = 0;
				$result = array();
				foreach($syncData["data"] as $data){
					if(!$this->checkAttendanceData($data[1],$data[3])){
						$arrData = array();
						$arrData["biometric_id"] = $data[1];
						$arrData["state"] = $data[2];
						$arrData["datetime"] = $data[3];
						$arrData["device_id"] = $data[4];
						
						$inserted = $this->db->insert($this->gcctimeAttendance, $arrData);
						if($inserted){
							$result[] = $arrData;
							$counter++;
						}
					}else{
						$syncedCounter++;
					}						
				}
				
				$resultset["total_added"] = $counter;
				$resultset["total_synced"] = $syncedCounter;
				$resultset["data"] = $result;
			}
		}
		
		return $resultset;
	}
	
	function setBiometricData(){
		$dateX = date("Y-m-d H:i");
		$syncData = $this->getStoredData();
		if(isset($syncData["response"]) && $syncData["response"] == true){
			if(isset($syncData["data"]) && $syncData["data"]){
				$counter = 0;
				$syncedCounter = 0;
				foreach($syncData["data"] as $data){
					if(!$this->checkAttendanceData($data[1],$data[3])){
						$arrData = array();
						$arrData["biometric_id"] = $data[1];
						$arrData["state"] = $data[2];
						$arrData["datetime"] = $data[3];
						$arrData["device_id"] = isset($data[5]) && $data[5] ? intval($data[5]): 0;
						
						$inserted = $this->db->insert($this->gcctimeAttendance, $arrData);
						if($inserted){
							$counter++;
						}
					}else{
						$syncedCounter++;
					}						
				}
				
				if($counter > 0 || $syncedCounter > 0){
					$synced = $this->insertEvent("sync_data");
					if($synced){ $this->core_layout->logNotification("[ {$dateX} ] ~ Sync data successful", "success", "gcctimeV2"); }
				}else{
					$error_sync = $this->insertEvent("error_sync_data");
					if($error_sync){ $this->core_layout->logNotification("[ {$dateX} ] ~ Failed sync, current data is updated", "info", "gcctimeV2"); }
				}				
				return true;
			}else{
				$doSync = $this->allowSyncData();
				if($doSync == true){
					$error_sync = $this->insertEvent("error_sync_data");
					if($error_sync){ $this->core_layout->logNotification("[ {$dateX} ] ~ {$syncData["message"]}", "error", "gcctimeV2"); }
					$this->setBiometricData();
				}else{
					$this->core_layout->logNotification("[ {$dateX} ] ~ Failed sync, data trigger has reached its limit!", "warning", "gcctimeV2");
					return false;
				}
			}
		}else{
			$this->core_layout->logNotification("[ {$dateX} ] ~ Failed sync, {$syncData["message"]}!!", "info", "gcctimeV2");
			return false;
		}
	}
	
	function getBiometricData2($alldevice=false){
		$ipAddress = "192.168.7.15";
		if($ipAddress !== "000.000.0.00"){	
			$zk = new ZKLibrary($ipAddress, 4370);
			 
			$connected = $zk->connect();
			if($connected){
				$zk->disableDevice();							
				$attendance = $zk->getAttendance();
				$zk->enableDevice();
				$zk->disconnect();
				
				var_dump($ipAddress);
				if(isset($attendance) && $attendance){
					var_dump($attendance); 
				}
			}
		}
	}
	
	function getBiometricData($alldevice=false, $id=null){
		include(BASEPATH.'libraries/zklibrary.php');
		$dateX = date("Y-m-d H:i");
		$alldevice = ($alldevice == true && $alldevice == "true")? true: false;
		if($alldevice){
			$collect = $this->getAllDevice();
			if($collect){
				$counter = 0;
				foreach($collect as $cc){
					$ipAddress = $cc->ip_address;
					$port = $cc->port;
					$port = $port? intval($port): 80;
					$allowOverride = ($cc->allow_override == 1)? true: false;

					$isReachable = $this->ipIsReachable($ipAddress, $port);
					if($isReachable == false){
						$tempPort = $port;
						$port = 80;
						$isReachable = $this->ipIsReachable($ipAddress, $port);
						$port = $tempPort;
					}

					if($isReachable || $allowOverride){
						$deviceName = strtolower($cc->device_name);
						$deviceName = preg_replace('/\s+/', '_', $deviceName);
						
						$_port = $port? $port: 4370;
						$zk = new ZKLibrary($ipAddress, $_port);
						 
						$connected = $zk->connect();
						if($connected){
							$zk->disableDevice();							
							$attendance = $zk->getAttendance();
							$zk->enableDevice();
							$zk->disconnect();
						}
						
						if(isset($attendance) && $attendance){
							$nData = array();
							$nData["attendance_log"] = $attendance;
							$nData["device_id"] = $cc->id;
							
							$dateTime = Date("dYm");
							$filepath = realpath("./uploads/data");
							$fileUpload = "{$filepath}/{$deviceName}-{$dateTime}.json";
							
							$handle = fopen($fileUpload, "w");
							$data = json_encode($nData);
							$response = file_put_contents($fileUpload, $data);
							fclose($handle);

							$handlingarr = array("file"=>$fileUpload,"data"=>$data);

							//curl start
							$defaults = array(
								CURLOPT_URL => $this->staticHookUrl,
								CURLOPT_POST => true,
								CURLOPT_POSTFIELDS => $handlingarr,
							);
							$ch = curl_init();
							curl_setopt_array($ch, ($defaults));
							curl_exec($ch);
							//curl end
							
							if($response){
								$counter++;
							}
						}
					}
				} /*** end foreach collect ***/
				
				if($counter){
					$downloaded = $this->insertEvent("download_data");
					if($downloaded){ $this->core_layout->logNotification("[ {$dateX} ] ~ Download data successful", "success", "gcctimeV2"); }
					return array("status"=>true,"curl_req"=>$message,"ret"=>$ret);
				}else{
					$doExport = $this->allowDownloadData();
					if($doExport == true){
						$error_download = $this->insertEvent("error_download_data");
						if($error_download){ $this->core_layout->logNotification("[ {$dateX} ] ~ Failed download data to server", "error", "gcctimeV2"); }
						$this->getBiometricData($alldevice, $id);
					}else{
						$this->core_layout->logNotification("[ {$dateX} ] ~ Failed download, data trigger has reached its limit!", "warning", "gcctimeV2");
						return false;
					}
				}
			}else{
				$this->core_layout->logNotification("[ {$dateX} ] ~ Cannot connect to biometric device!", "error", "gcctimeV2");
				return false;
			} /*** endif collect ***/
		}else{
			$collect = $this->getDevice($id);
			if($collect){
				$ipAddress = $collect->ip_address;
				$port = $collect->port;
				$port = $port? intval($port): 80;
				$allowOverride = ($collect->allow_override == "1")? true: false;
				$isReachable = $this->ipIsReachable($ipAddress, $port);
				if($isReachable == false){
					$tempPort = $port;
					$port = 80;
					$isReachable = $this->ipIsReachable($ipAddress, $port);
					$port = $tempPort;
				}

				if($isReachable || $allowOverride){
					$deviceName = strtolower($collect->device_name);
					$deviceName = preg_replace('/\s+/', '_', $deviceName);

					$_port = $port? $port: 4370;
					$zk = new ZKLibrary($ipAddress, $_port);
					$connected = $zk->connect();
					
					if($connected){
						$zk->disableDevice();
						$attendance = $zk->getAttendance();
						$zk->enableDevice();
						$zk->disconnect();							
					}
					
					if(isset($attendance) && $attendance){
						$nData = array();
						$nData["attendance_log"] = $attendance;
						$nData["device_id"] = $collect->id;
						
						$dateTime = Date("dYm");
						$filepath = realpath("./uploads/data");
						$fileUpload = "{$filepath}/{$deviceName}-{$dateTime}.json";

						$handle = fopen($fileUpload, "w");
						$data = json_encode($nData);
						$response = file_put_contents($fileUpload, $data);
						fclose($handle);

						$handlingarr = array("devicename"=>$deviceName,"data"=>json_encode($nData));

						//curl start
						$defaults = array(
							CURLOPT_URL => $this->staticHookUrl,
							CURLOPT_POST => true,
							CURLOPT_POSTFIELDS => $handlingarr,
						);
						$ch = curl_init();
						curl_setopt_array($ch, ($defaults));
						$ret = curl_exec($ch);
						$message = curl_errno($ch) === CURLE_OK ? 'success' : 'failure';

						// Close handle
						curl_close($ch);
						//curl end
						
						if($response){
							$downloaded = $this->insertEvent("download_data");
							if($downloaded){ $this->core_layout->logNotification("[ {$dateX} ] ~ Download data successful", "success", "gcctimeV2"); }
							return array("status"=>true,"curl_req"=>$message,"ret"=>$ret);
						}else{
							$doExport = $this->allowDownloadData();
							if($doExport == true){
								$error_download = $this->insertEvent("error_download_data");
								if($error_download){ $this->core_layout->logNotification("[ {$dateX} ] ~ Failed download data to server", "error", "gcctimeV2"); }
								$this->getBiometricData($alldevice, $id);
							}else{
								$this->core_layout->logNotification("[ {$dateX} ] ~ Failed download, data trigger has reached its limit!", "error", "gcctimeV2");
								return false;
							}
						}
					}else{
						$this->core_layout->logNotification("[ {$dateX} ] ~ Failed download, No attendance data found!", "error", "gcctimeV2");
						return false;
					}			
				}else{
					$this->core_layout->logNotification("[ {$dateX} ] ~ Failed to connect to biometric device!", "error", "gcctimeV2");
					return false;
				}
			}else{
				$this->core_layout->logNotification("[ {$dateX} ] ~ Cannot connect to biometric device!", "error", "gcctimeV2");
				return false;
			} /*** endif collect ***/
		}
	} /*** endif getBiometricData ***/
}