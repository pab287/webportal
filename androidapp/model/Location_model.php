<?php
    class Location_model extends Dbase{
        use Logs_maker;


        private function store_logs($post, $emp_id){
            $this->template_content = $post;
            $this->page = "location";
            $this->emp_id = $emp_id;
            return $this->responce;
        }
        
        public function user_data(){
            $date = date("Y-m-d h:i:s");
            $conn = $this->conn("gcctimeutility");
            if(isset($_POST['coordinates'])){
                $coordinates = $_POST['coordinates'];
                $longitude = $coordinates['longitude'];
                $latitude = $coordinates['latitude'];
                $device_id = $_POST['device_id'];
                if($_POST['biometric_id'] != null){
                    $emp_id = $this->getEmpId($device_id);
                    $biometric = $this->getBiometricId($emp_id);  
                }else{
                    $biometric = $_POST['biometric_id'];
                }

                $this->store_logs($_POST, $emp_id);
                $sql = "INSERT INTO gcctimeutility.images(biometric_id, longtitude, latitude, device_id, datetime, address) VALUES (:bio_id , :longti, :latitude, :device_id, :today, :address)";
                $sth = $conn->prepare($sql);
                $sth->bindParam(':bio_id', $biometric);
                $sth->bindParam(':longti', $longitude);
                $sth->bindParam(':latitude', $latitude);
                $sth->bindParam(':device_id', $device_id);
                $sth->bindParam(':today', $date);
                $sth->bindParam(':address', $this->geoaddress($longitude,$latitude));
                $sth->execute();
                if($sth){
                    return 1;
                }else{
                    return 0;
                }
            }
        }

        public function user_dataV2(){
            $date = date("Y-m-d h:i:s");
            $conn = $this->conn("gcctimeutility");

            
            if(isset($_POST['coordinates'])){
                $emp_id = $_POST['emp_id'];
                $biometric = $_POST['biometric_id'];
                $device_id = $_POST['device_id'];
                $coordinates = $_POST['coordinates'];
                // $longitude = $coordinates['longitude'];
                // $latitude = $coordinates['latitude'];
                $longitude = $_POST['longitude'];
                $latitude = $_POST['latitude'];
                
                $is_sync = $_POST['is_sync'];
                $geo_address = $this->geoaddress($latitude,$longitude);
                
                $logs_str = $is_sync=='true' ? 'sync' : 'save';

                $this->store_logs($_POST, $emp_id);
                $sql = "INSERT INTO gcctimeutility.images(biometric_id, longtitude, latitude, device_id, datetime, address) VALUES (:bio_id , :longti, :latitude, :device_id, :today, :address)";
                $sth = $conn->prepare($sql);
                $sth->bindParam(':bio_id', $biometric);
                $sth->bindParam(':longti', $longitude);
                $sth->bindParam(':latitude', $latitude);
                $sth->bindParam(':device_id', $device_id);
                $sth->bindParam(':today', $date);
                $sth->bindParam(':address', $geo_address);
                $sth->execute();
                $lastId = $conn->lastInsertId();

                if($sth){
                    $this->saveLogs("success", "insert", $emp_id, "[Mobile] My Location - ".$logs_str." my current location at ".$geo_address);
                    return 1;
                }else{
                    $this->saveLogs("error", "insert", $emp_id, "[Mobile] My Location - ".$logs_str." my current location at ".$geo_address);
                    return 0;
                }
            }
        }

        public function upload_location_image(){
            $conn = $this->conn("gcctimeutility");
            $date = date("Y-m-d h:i:s");
            $biometric_id = $_POST['biometric_id'];
            $emp_id = $_POST['emp_id'];
            $device_id = $_POST['device_id'];
            $longitude = $_POST['longitude'];
            $latitude = $_POST['latitude'];
            $is_sync = $_POST['is_sync'];
            $img = $_FILES['file']['name'];
            $address = $this->geoaddress($longitude,$latitude);

            $logs_str = $is_sync=='true' ? 'sync to' : 'save to';
            $this->store_logs(array($_FILES, $_POST), $emp_id);

            $sql = "INSERT INTO gcctimeutility.images(image, longtitude, latitude, device_id, biometric_id, datetime, address) 
                    VALUES ('$img', '$longitude', '$latitude', '$device_id', '$biometric_id', '$date', '$address')";
            $sth = $conn->prepare($sql);
            $sth->execute();
            if($sth){
                $temp_file = $_FILES['file']['tmp_name'];
                $storage = dir."/androidapp/storage/";
                $image = $storage . ds . $img;
                
                if(!is_dir($storage)){
                    mkdir($storage, 777);
                }
                if(file_exists($temp_file)){
                    if(move_uploaded_file($temp_file, $image)){
                        $this->saveLogs("success", "upload", $emp_id, "[Mobile] Image Location - ".$logs_str." upload image.");
                        return json_encode(array("data" => true));
                    }else{
                        $this->saveLogs("error", "upload", $emp_id, "[Mobile] Image Location - ".$logs_str." upload image.");
                        return json_encode(array("data" => "move uploaded file is note working"));
                    }
                }else{
                    $this->saveLogs("error", "upload", $emp_id, "[Mobile] Image Location - sync file not found.");
                    return json_encode(array("data" => array("error" => "file not found!")));
                }
            }else{
                $this->saveLogs("error", "upload", $emp_id, "[Mobile] Image Location - sync failed to save.");
                return json_encode(array("data" => false));
            }
        }

        public function get_all_location(){
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.images";
            $allData = $conn->query($sql);
            $allData->execute();
            return json_encode($allData->fetchAll(PDO::FETCH_ASSOC));
        }

        public function travel_order(){
            if(isset($_POST['biometric_id']) && $_POST['biometric_id'] != null){
                $biomet_id = $_POST['biometric_id'];
                $emp_id = $this->get_emp_id($biomet_id);
                $travel_order_id = $this->travel_order_id($emp_id);
                $travel_destination = $this->travel_dest($travel_order_id);
                $TONum = sizeof($travel_destination);
                if($travel_destination != 0){
                    $dateFrom = date('l, F d y h:i:s a', strtotime($travel_destination[0]['date_from']));
                    $dateTo = date('l, F d y h:i:s a', strtotime($travel_destination[0]['date_to']));
                    $coords_from = $travel_destination[0]['coords_from'];
                    $coords_to = $travel_destination[0]['coords_to'];
                    $des_from = $travel_destination[0]['des_from'];
                    $des_to = $travel_destination[0]['des_to'];
                    $des_porpuse = $travel_destination[0]['purpose'];
                    $des_id = $travel_destination[0]['id'];
                    return json_encode(array("to_des" => array(
                        array("coords" => json_decode($coords_from), "title" => $des_from, "description" => $des_porpuse, "date" => $dateFrom, "name" => 'From'),
                        array("coords" => json_decode($coords_to), "title" => $des_to, "description" => $des_porpuse, "date" => $dateTo, "name" => 'To'),
                    ),
                    "travel_order_id" => $travel_order_id, "des_id" => $des_id));
                }else{
                    return 0;
                }
            }
        }

        public function driver_arrival(){
            $datetime = date("Y-m-d H:i:s");
            if(isset($_POST['empID']) && $_POST['empID'] != null){
                $emp_id = $_POST['empID'];
                $travel_des = $_POST['travel_des'];
                $travel_order_id = $_POST['travel_order_id'];
                $conn = $this->conn("gcceforms");
                $sql = "UPDATE gcceforms.travel_destination SET travel_order_status = 1, travel_order_status_date = :today WHERE id = :id";
                $driverTO = $conn->prepare($sql);
                $driverTO->bindParam(":id", $travel_des);
                $driverTO->bindParam(":today", $datetime);
                $driverTO->execute();
                if($driverTO){
                    $travel_destination = $this->travel_dest($travel_order_id, $travel_des);
                    if($travel_destination != 0){
                        $dateFrom = date('l, F d y h:i:s a', strtotime($travel_destination[0]['date_from']));
                        $dateTo = date('l, F d y h:i:s a', strtotime($travel_destination[0]['date_to']));
                        $coords_from = $travel_destination[0]['coords_from'];
                        $coords_to = $travel_destination[0]['coords_to'];
                        $des_from = $travel_destination[0]['des_from'];
                        $des_to = $travel_destination[0]['des_to'];
                        $des_porpuse = $travel_destination[0]['purpose'];
                        $des_id = $travel_destination[0]['id'];

                        if($this->ver_driver($emp_id)){
                            return json_encode(array("to_des" => array(
                                array("coords" => json_decode($coords_from), "title" => $des_from, "description" => $des_porpuse, "date" => $dateFrom, "name" => 'From'),
                                array("coords" => json_decode($coords_to), "title" => $des_to, "description" => $des_porpuse, "date" => $dateTo, "name" => 'To'),
                            ),
                            "travel_order_id" => $travel_order_id, "des_id" => $des_id));
                        }else{
                            return json_encode(array("to_des" => array(
                                array("coords" => json_decode($coords_from), "title" => $des_from, "description" => $des_porpuse, "date" => $dateFrom, "name" => 'From'),
                                array("coords" => json_decode($coords_to), "title" => $des_to, "description" => $des_porpuse, "date" =>  $dateTo, "name" => 'To')
                            ),
                            "travel_order_id" => 0, "des_id" => $des_id));
                        }
                    }else{
                        return 0;
                    }
                }else{
                    return 0;
                }
            }
        }

        public function get_locations(){
            $post = $_POST;
            return $this->log($post);
            
        }

        private function log($post = []){
            $time = date("H:i:s");
            if(!empty($post)){
                if($time > "08:00:00" && $time < "18:00:00"){
                    $storage = dir."/androidapp/storage/logs/location";
                    $temp_date = date('Y-m-d H:i:s');
                    $user_locations = $post['user_location'];
                    $location = json_encode($post['location']);
        
                    if(!is_dir($storage)){
                        mkdir($storage, 777);
                    }
        
                    //Something to write to txt log
                    $log  = "%\n@Date={$temp_date},\n@Employee_id={$post['emp_id']},\n@location={$location},\n@user_locations={$user_locations}\n";
                    //Save string to log, use FILE_APPEND to append.
                    if(file_put_contents(dir.'/androidapp/storage/logs/location/'.$post['emp_id'].'.log', $log, FILE_APPEND)){
                        //$this->app_user_status($post['emp_id'], 2);
                        return 1;
                    }else{
                        //$this->app_user_status($post['emp_id'], 1);
                        return 0;
                    }
                }
            }
        }

        private function has_loa($emp_id){
            $today = date("Y-m-d");
            $conn = $this->conn("gcceforms");
            $sql = "SELECT * FROM gcceforms.loa WHERE employee = :emp_id AND date_to <= :today";
            $data = $conn->prepare($sql);
            $data->bindParam(":emp_id", $emp_id);
            $data->bindParam(":today", $today);
            $data->execute();
            $count = $data->rowCount();
            if($count > 0){
                return true;
            }else{
                return false;
            }
        }

        public function app_user_status($emp_id, $status){
            $conn = $this->conn();
            $sql = "UPDATE gcctimeutility.app_users SET status = :status WHERE emp_id = :emp_id";
            $data = $conn->prepare($sql);
            $data->bindParam(":status", $status);
            $data->bindParam(":emp_id", $emp_id);
            $data->execute();
            if($data){
                return true;
            }else{
                return false;
            }
        }

        public function user_status(){
            if(isset($_POST['emp_id']) && $_POST['emp_id'] != null){
                $emp_id = $_POST['emp_id'];
                $status = $_POST['status'];
                $conn = $this->conn();
                $sql = "UPDATE gcctimeutility.app_users SET status = :status WHERE emp_id = :emp_id";
                $data = $conn->prepare($sql);
                $data->bindParam(":status", $status);
                $data->bindParam(":emp_id", $emp_id);
                $data->execute();
                if($data){
                    return "status updated";
                }else{
                    return "status update failed";
                }
            }
        }

        private function getBiometricId($id){
            $conn = $this->conn();
            $bio = $conn->prepare("SELECT biometricno FROM tblemployees WHERE id = :emp_id");
            $bio->bindParam(":emp_id", $id);
            $bio->execute();
            $data = $bio->fetch(PDO::FETCH_ASSOC);
            return $data['biometricno'];
        }

        private function getEmpId($dev_id){
            $conn = $this->conn("gcctimeutility");
            $bio = $conn->prepare("SELECT emp_id FROM gcctimeutility.app_users WHERE device_id = :device_id");
            $bio->bindParam(":device_id", $dev_id);
            $bio->execute();
            $data = $bio->fetch(PDO::FETCH_ASSOC);
            return $data['emp_id'];
        }

        private function geoaddress($lat = null,$long = null) {
			if($long != null && $lat != null){
				// $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$long."&language=en-EN&sensor=false&key=AIzaSyCTzlKHdtvrOuKv7LEQjW8HVmy1QFFgalM"; // original source code
				// $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$long."&language=en-EN&sensor=false&key=AIzaSyB0P6151i4JuPBG79VhRhaiEzqR4Awmnmw";
				$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$long."&language=en-EN&sensor=false&key=AIzaSyCm_pTwQzhaAKspErhW9ptpubv_ATLrpgE";
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

        private function get_emp_id($biono){
            $conn = $this->conn();
            $sql = "SELECT id FROM tblemployees WHERE biometricno = :bionum";
            $emp_data = $conn->prepare($sql);
            $emp_data->bindParam(":bionum", $biono);
            $emp_data->execute();
            $data = $emp_data->fetch(PDO::FETCH_ASSOC);
            return $data['id'];
        }

        private function travel_order_id($emp_id){
            $datetime = date("Y-m-d H:i:s");
            $conn = $this->conn("gcceforms");
            if($this->ver_driver($emp_id)){
                $sql = "SELECT a.id FROM gcceforms.travel_order AS a LEFT JOIN gcceforms.travel_destination AS b ON a.id = b.travel_order_id WHERE a.driver_id = :emp_id AND b.date_to >= :today GROUP BY a.id";
                $to_data = $conn->prepare($sql);
                $to_data->bindParam(":emp_id", $emp_id);
                $to_data->bindParam(":today", $datetime);
                $to_data->execute();
                $data = $to_data->fetch(PDO::FETCH_ASSOC);
                return $data['id'];
            }else{
                $sql = "SELECT a.travel_order_id FROM gcceforms.travel_personnel AS a LEFT JOIN gcceforms.travel_order AS b ON a.travel_order_id = b.id LEFT JOIN gcceforms.travel_destination AS c ON c.travel_order_id = b.id WHERE c.date_to >= :today AND a.employee_id = :emp_id GROUP BY b.id ORDER BY b.id DESC";
                $to_data = $conn->prepare($sql);
                $to_data->bindParam(":today", $datetime);
                $to_data->bindParam(":emp_id", $emp_id);
                $to_data->execute();
                $data = $to_data->fetch(PDO::FETCH_ASSOC);
                return $data['travel_order_id'];
            }
        }

        private function travel_dest($to_id, $travel_des = ""){
            $datetime = date("Y-m-d H:i:s");
            $status = 0;
            $conn = $this->conn("gcceforms");
            if($travel_des != ""){
                $sql = "SELECT b.id, b.date_from, b.date_to, b.travel_from, b.travel_to, b.des_from, b.des_to, b.purpose, b.coords_from, b.coords_to FROM gcceforms.travel_order as a LEFT JOIN gcceforms.travel_destination as b ON a.id = b.travel_order_id WHERE a.id = :travel_order_id AND b.date_to >= :date_today AND b.travel_order_status = :TO_status AND b.id != :travel_destination_id ORDER BY b.id ASC";
            }else{
                $sql = "SELECT b.id, b.date_from, b.date_to, b.travel_from, b.travel_to, b.des_from, b.des_to, b.purpose, b.coords_from, b.coords_to FROM gcceforms.travel_order as a LEFT JOIN gcceforms.travel_destination as b ON a.id = b.travel_order_id WHERE a.id = :travel_order_id AND b.date_to >= :date_today AND b.travel_order_status = :TO_status ORDER BY b.id ASC";
            }
            $to_des_data = $conn->prepare($sql);
            if($travel_des != ""){
                $to_des_data->bindParam(":travel_destination_id", $travel_des);
            }
            $to_des_data->bindParam(":travel_order_id", $to_id);
            $to_des_data->bindParam(":date_today", $datetime);
            $to_des_data->bindParam(":TO_status", $status);
            $to_des_data->execute();
            $data = $to_des_data->fetchAll(PDO::FETCH_ASSOC);
            $count = $to_des_data->rowCount();
            if($data){
                if($count > 1){
                    $to = array();
                    foreach ($data as $row) {
                        array_push($to, $row);
                    }
                    return $to;
                }else{
                    return $data;
                }
            }else{
                return 0;
            }
        }

        private function ver_driver($emp_id){
            $conn = $this->conn("gcceforms");
            $sql = "SELECT * FROM gcceforms.travel_order WHERE driver_id = :emp_id";
            $ver = $conn->prepare($sql);
            $ver->bindParam(":emp_id", $emp_id);
            $ver->execute();
            $count = $ver->rowCount();
            if($count != 0){
                return true;
            }else{
                return false;
            }
        }

        public function saveLogs($type, $user_action, $user_id, $log_message){
            $conn = $this->conn("gcctimeutility");
            $current_date = date("Y-m-d H:i:s");
            $ip_address = '';

            $sth = $conn->prepare("INSERT INTO gcctimeutility.app_logs_event(`type`, `user_action`, `user_id`, `log_message`, `ip_address`, `created_at`) VALUES ('$type','$user_action','$user_id','$log_message','$ip_address','$current_date')");
            $sth->execute();

            if ($sth) {
                return true;
            } else {
                return false;
            }
        }

        public function update_undefined_location($limit){
            $conn = $this->conn("gcctimeutility");
            $result = array();

            $_limit = $limit ? $limit : 100;

            $query = "SELECT id, longtitude, latitude FROM gcctimeutility.app_attendance WHERE LOWER(address) = 'location undefined' AND longtitude != 0 AND latitude != 0 LIMIT $_limit";
            $q = $conn->prepare($query);
            $q->execute();

            if($q->rowCount() > 0){
                foreach($q->fetchAll(PDO::FETCH_ASSOC) as $key => $rs){
                    $tempAddress = $this->geoaddress($rs['latitude'], $rs['longtitude']);

                    $sql = "UPDATE gcctimeutility.app_attendance SET address = :tempAdd WHERE id = :id";
                    $_q = $conn->prepare($sql);
                    $_q->bindParam(":id", $rs['id']);
                    $_q->bindParam(":tempAdd", $tempAddress);
                    $_q->execute();

                    if($_q){
                        $rs['status'] = true;
                    }else{
                        $rs['status'] = false;
                    }

                    array_push($result, $rs);
                }
            }

            return array('affected_rows' => $q->rowCount(), 'data' => $result);
        }

    }
?>