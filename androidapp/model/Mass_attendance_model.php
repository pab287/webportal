<?php
    class Mass_attendance_model extends Dbase{
        use Logs_maker;

        private function store_logs($post, $emp_id){
            $this->template_content = $post;
            $this->page = "massattendance";
            $this->emp_id = $emp_id;
            return $this->responce;
        }

        public function massAttendance(){
            $time = date("h:i:sa");
            $date = date("Y-m-d");
            if(isset($_FILES['file']) && $_FILES['file'] != null){
                $files = $_FILES['file'];
                $device_id = $_POST['device_id'];
                $device_name = $_POST['device_name'];
                $longitude = $_POST['longitude'];
                $latitude = $_POST['latitude'];
                $biomet = $_POST['biometric_id'];
                $device_user_bio = $_POST['device_user_bio'];
                $conn = $this->conn("gcctimeutility");
                $this->store_logs(array($_FILES, $_POST), $device_user_bio);
                $sql = "INSERT INTO gcctimeutility.app_attendance(biometric_id, date, time, address, longtitude, latitude, device_user_bio) VALUES (:bio_met, :today, :today_time, :addr, :lon, :lat, :device_user_bio)";
                $sth = $conn->prepare($sql);
                $sth->bindParam(':bio_met', $biomet);
                $sth->bindParam(':today', $date);
                $sth->bindParam(':today_time', $time);
                $sth->bindParam(':addr', $this->geoaddress($longitude, $latitude));
                $sth->bindParam(':lon', $longitude);
                $sth->bindParam(':lat', $latitude);
                $sth->bindParam(':today_time', $time);
                $sth->bindParam(':device_user_bio', $device_user_bio);
                $sth->execute();
                if($sth){
                    $mobile_att_id = $conn->lastInsertId();
                    return $this->massAttendanceImage($device_id, $device_name, $files, $mobile_att_id, $device_user_bio);
                }else{
                    return json_encode(array("data" => 0));
                }
            }
        }

        public function get_Employees(){
            $conn = $this->conn();
            $response["employee_array"] = array();

            $sql = "SELECT firstname, lastname, biometricno, id FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC";
            $data = $conn->prepare($sql);
            $data->execute();
            $allEmp = $data->fetchAll(PDO::FETCH_ASSOC);
            foreach ($allEmp as $row) {
                $list = array();
                $list['id'] = $row['id'];
                $list['biometricno'] = $row['biometricno'];
                $list['name'] = utf8_encode($row['firstname']." ".$row['lastname']);
                array_push($response["employee_array"], $list);
            }
            return json_encode($response);
        }

        public function attendanceHistory(){
            $date = date("Y-m-d");
            if(isset($_POST['dev_user_bio']) && $_POST['dev_user_bio'] != null){
                $personel_bio = $_POST['dev_user_bio'];
                $conn = $this->conn("gcctimeutility");
                $sql = "SELECT * FROM gcctimeutility.app_attendance WHERE device_user_bio = :dev_user_bio AND date = :today AND is_fingerprint = 0";
                $all_bio = $conn->prepare($sql);
                $all_bio->bindParam(":dev_user_bio", $personel_bio);
                $all_bio->bindParam(":today", $date);
                $all_bio->execute();
                $biometrics = $all_bio->fetchAll(PDO::FETCH_ASSOC);
                $data["history"] = array();
                foreach($biometrics as $bio){
                    $emp_name = $this->get_all_emp($bio['biometric_id']);
                    array_push($data["history"], array("name" => $emp_name, "time" => $bio['time']));
                }
                return json_encode($data);
            }
        }

        public function get_latest_hist(){
            $biometric_id = $_POST['biometric_no'];
            if(isset($_POST['biometric_no']) && $_POST['biometric_no'] != null){
                if($biometric_id != null){
                    return $this->latest_emp_att($biometric_id);
                }else{
                    return 0;
                }
            }
        }

        private function massAttendanceImage($dev_id, $dev_name, $file, $mobile_att_id, $device_user_bio){
            $img = $file['name'];

            $conn = $this->conn("gcctimeutility");
            $sql = "INSERT INTO gcctimeutility.mass_attendance_images(app_attendance_id, images, device_id, device_name) VALUES (:app_attendance_id, :img_name, :dev_id, :dev_name)";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':app_attendance_id', $mobile_att_id);
            $sth->bindParam(':img_name', $img);
            $sth->bindParam(':dev_id', $dev_id);
            $sth->bindParam(':dev_name', $dev_name);
            $sth->execute();
            if($sth){
                return $this->optimization($file, $device_user_bio);
            }else{
                return 0;
            }
        }

        private function geoaddress($long,$lat) {
			if($long != null && $lat != null){
				// $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$long."&language=en-EN&sensor=false&key=AIzaSyCTzlKHdtvrOuKv7LEQjW8HVmy1QFFgalM";
				$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$long."&language=en-EN&sensor=false&key=AIzaSyB0P6151i4JuPBG79VhRhaiEzqR4Awmnmw";
				$curlData=file_get_contents($url);
				$address = json_decode($curlData);
				if($address->status == "OK"){
					$a=$address->results[0];
					return $a->formatted_address;
				}else{
					return "Location Undefined";
				}
			}else{
				return "N\A";
			}
		}

        private function allAttendance(){
            $time = date("h:i:sa");
            $date = date("Y-m-d");
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT a.biometric_id, b.images FROM gcctimeutility.app_attendance AS a LEFT JOIN gcctimeutility.mass_attendance_images AS b ON a.id = b.app_attendance_id WHERE a.date = :today AND a.time <= :timeToday GROUP BY a.id";
            $data  = $conn->prepare($sql);
            $data->bindParam(":today", $date);
            $data->bindParam(":timeToday", $time);
            $data->execute();

            $allAttendance = $data->fetchAll(PDO::FETCH_ASSOC);
            return json_encode($allAttendance);
        }

        private function optimization($files, $personel_bio){
            $storage = dir."/androidapp/storage/mass_attendance_storage";
            if(!is_dir($storage)){
                mkdir($storage, 777);
            }
            $temp_file = $files['tmp_name'];
            $source_properties = getimagesize($temp_file);
            $image_resource_id = imagecreatefromjpeg($temp_file);
            $target_layer = $this->resize_imagejpg($image_resource_id,$source_properties[0],$source_properties[1]);
            if(file_exists($temp_file)){
                if(imagejpeg($target_layer, $storage . ds . $files['name'], 60)){
                    return $this->latest_emp_att($personel_bio);
                }else{
                    return json_encode(array("res" => 1));
                }
            }else{
                return json_encode(array("res" => 0));
            }
        }

        private function resize_imagejpg($image_resource_id,$width,$height) {
            $target_width =200;
            $target_height =200;
            $target_layer=imagecreatetruecolor($target_width,$target_height);
            imagecopyresampled($target_layer,$image_resource_id,0,0,0,0,$target_width,$target_height, $width,$height);
            return $target_layer;
        }

        private function device_user($unique_id, $biometric_id){
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.personnel WHERE app_user_id = :unique_id AND mobile_name = :dev_name";
            $user_data = $conn->prepare($sql);
            $user_data->bindParam(":unique_id", $unique_id);
            $user_data->bindParam(":biometric_id", $biometric_id);
            $user_data->execute();
            $biometric = $user_data->fetch(PDO::FETCH_ASSOC);
            return $biometric['biometric_id'];
        }

        private function get_all_emp($biomet){
            $conn = $this->conn();
            $sql = "SELECT firstname, lastname FROM tblemployees WHERE biometricno = :bio_num";
            $emp_data = $conn->prepare($sql);
            $emp_data->bindParam(":bio_num", $biomet);
            $emp_data->execute();
            $name = $emp_data->fetch(PDO::FETCH_ASSOC);
            return $name['firstname']." ".$name['lastname'];
        }

        private function latest_emp_att($personel_bio){
            $date = date("Y-m-d");
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.app_attendance WHERE device_user_bio = :dev_user_bio AND date = :today AND is_fingerprint = 0 ORDER BY id DESC LIMIT 5";
            $data = $conn->prepare($sql);
            $data->bindParam(":dev_user_bio", $personel_bio);
            $data->bindParam(":today", $date);
            $data->execute();
            $allData = $data->fetchAll(PDO::FETCH_ASSOC);
            $dataArr = array();
            foreach($allData as $bio){
                $emp_name = $this->get_all_emp($bio['biometric_id']);
                array_push($dataArr, array("name" => $emp_name, "time" => $bio['time']));
            }
            return json_encode($dataArr);
        }

    }
?>