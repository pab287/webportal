<?php
    class Time_in_out_model extends Dbase{
        use Logs_maker;

        private function store_logs($post, $emp_id){
            $this->template_content = $post;
            $this->page = "timeInOut";
            $this->emp_id = $emp_id;
            return $this->responce;
        }

        private function store_logs_spam($post, $emp_id){
            $this->template_content = $post;
            $this->page = "offlineTrackingRecords";
            $this->emp_id = $emp_id;
            return $this->responce;
        }

        public function user_sites(){
            if(isset($_POST['biometric_id']) && $_POST['biometric_id'] != null){
                $lat = $_POST['latitude'];
                $lon = $_POST['longitude'];
                
                $biometric_id = $_POST['biometric_id'];
                $personnel_id = $this->personnel_id($biometric_id);
                $sites_id = $this->sites_location_id($personnel_id);
                if($sites_id != 0){
                    $locations = $this->polygon_geofence($sites_id, $lat, $lon);
                    return $locations;
                }else{
                    $travel_order = $this->location_coordinates($biometric_id, $lat, $lon);
                    return $travel_order;
                }
            }
        }

        public function time_log(){
            $time = date("H:i:s");
            $date = date("Y-m-d");
            if(isset($_POST['biometric_id']) && $_POST['biometric_id'] != null){
                $emp_id = $_POST['emp_id'];
                $bio_num = $_POST['biometric_id'];
                $time_status = $_POST['time_status'];
                $coords = $_POST['coords'];
                // $coords = isset($_POST['coords']) && $_POST['coords'] ? (array) json_decode($_POST['coords']) : array('latitude' => null, 'longitude' => null);

                $logs_action = '';
                if($time_status==='in'){ $logs_action = 'time in'; } 
                else if($time_status==='out'){ $logs_action = 'time out'; } 
                else { $logs_action = $time_status; }
                
                if($coords['latitude'] != null && $coords['longitude'] != null){
                    $latitude = $coords['latitude'];
                    $longitude = $coords['longitude'];
                    $conn = $this->conn("gcctimeutility");
                    $personnel_id = $this->personnel_id($bio_num);
                    $sites_id = $this->sites_location_id($personnel_id);
                    $stats = 1;

                    $this->store_logs($_POST, $personnel_id);
                    $max_time = date('H:i:s', strtotime($this->setInterval($bio_num). "+ 1 minute"));
                    if($this->setInterval($bio_num) == null){
                        if(count($sites_id) != 0){
                            $location = $this->polygon_geofence($sites_id, $latitude, $longitude);
                            $travel_order = $this->location_coordinates($bio_num, $latitude, $longitude);
                            $sql = "INSERT INTO gcctimeutility.app_attendance(biometric_id, time, date, address, longtitude, latitude, is_fingerprint, time_status) VALUES (:bio, :time, :date, :address, :lon, :lat, :is_finger, :time_status)";
                            $data = $conn->prepare($sql);
                            $data->bindParam(':bio', $bio_num);
                            $data->bindParam(':time', $time);
                            $data->bindParam(':date', $date);
                            $data->bindParam(':address', $this->geoaddress($longitude,$latitude));
                            $data->bindParam(':lon', $longitude);
                            $data->bindParam(':lat', $latitude);
                            $data->bindParam(':is_finger', $stats);
                            $data->bindParam(':time_status', $time_status);
                            $data->execute();
                            $insertedID = $conn->lastInsertId();
                            if($data){
                                $resp = 0;
                                if($location != 0){
                                    $resp = $location;
                                }else if($travel_order != 0){
                                    $resp = $travel_order;
                                }else{
                                    $resp = 2;
                                }
                                $this->saveLogs("success", $logs_action, $emp_id, "[Mobile] Attendance - user ".$logs_action.".");
                                return json_encode($this->user_logs($bio_num, $date, $time, $resp, $insertedID));
                            }else{
                                $this->saveLogs("error", $logs_action, $emp_id, "[Mobile] Attendance - user tried to ".$logs_action." with error in saving.");
                                return 3;
                            }
                        }else{
                            $this->saveLogs("error", $logs_action, $emp_id, "[Mobile] Attendance - user tried to ".$logs_action." with no site location.");
                            return 0;
                        }
                    }else{
                        if($max_time < $time){
                            if(count($sites_id) != 0){
                                $location = $this->polygon_geofence($sites_id, $latitude, $longitude);
                                $travel_order = $this->location_coordinates($bio_num, $latitude, $longitude);
                                $sql = "INSERT INTO gcctimeutility.app_attendance(biometric_id, time, date, address, longtitude, latitude, is_fingerprint, time_status) VALUES (:bio, :time, :date, :address, :lon, :lat, :is_finger, :time_status)";
                                $data = $conn->prepare($sql);
                                $data->bindParam(':bio', $bio_num);
                                $data->bindParam(':time', $time);
                                $data->bindParam(':date', $date);
                                $data->bindParam(':address', $this->geoaddress($longitude,$latitude));
                                $data->bindParam(':lon', $longitude);
                                $data->bindParam(':lat', $latitude);
                                $data->bindParam(':is_finger', $stats);
                                $data->bindParam(':time_status', $time_status);
                                $data->execute();
                                $insertedID = $conn->lastInsertId();
                                if($data){
                                    $this->log($bio_num, $longitude, $latitude);
                                    $resp = 0;
                                    if($location != 0){
                                        $resp = $location;
                                    }else if($travel_order != 0){
                                        $resp = $travel_order;
                                    }else{
                                        $resp = 2;
                                    }
                                    $this->saveLogs("success", $logs_action, $emp_id, "[Mobile] Attendance - user ".$logs_action.".");
                                    return json_encode($this->user_logs($bio_num, $date, $time, $resp, $insertedID, $time));
                                }else{
                                    $this->saveLogs("error", $logs_action, $emp_id, "[Mobile] Attendance - user tried to ".$logs_action." with error in saving.");
                                    return 3;
                                }
                            }else{
                                $this->saveLogs("error", $logs_action, $emp_id, "[Mobile] Attendance - user tried to ".$logs_action." with no site location.");
                                return 0;
                            }
                        }else{
                            $this->saveLogs("error", $logs_action, $emp_id, "[Mobile] Attendance - user tried to ".$logs_action." again while has 1 minute interval.");
                            return json_encode($this->user_logs($bio_num, $date, $time, 4));
                        }
                    }
                }
                
            }
        }

        public function time_log_spam(){
            $time = date("H:i:s");
            $date = date("Y-m-d");

            if(isset($_POST['biometric_id']) && $_POST['biometric_id'] != null){
                $emp_id = $_POST['emp_id'];
                $bio_num = $_POST['biometric_id'];
                $time_status = $_POST['time_status'];
                $coords = $_POST['coords'];
                // $firebase_token = $_POST['firebaseToken'];

                $logs_action = '';
                if($time_status==='in'){ $logs_action = 'time in'; } 
                else if($time_status==='out'){ $logs_action = 'time out'; } 
                else { $logs_action = $time_status; }

                if($coords['latitude'] != null && $coords['longitude'] != null){
                    $latitude = $coords['latitude'];
                    $longitude = $coords['longitude'];
                    $conn = $this->conn("gcctimeutility");
                    $personnel_id = $this->personnel_id($bio_num);
                    $sites_id = $this->sites_location_id($personnel_id);

                    $stats = 1;
                    $this->store_logs_spam($_POST, $personnel_id);
                    $max_time = date('H:i:s', strtotime($this->setInterval_spam($bio_num). "+ 1 minute"));
                    if($this->setInterval_spam($bio_num) == null){
                        if(count($sites_id) != 0){
                            $location = $this->polygon_geofence($sites_id, $latitude, $longitude);
                            $travel_order = $this->location_coordinates($bio_num, $latitude, $longitude);
                            // $sql = "INSERT INTO app_attendance_spam(biometric_id, time, date, address, longtitude, latitude, is_fingerprint, time_status, firebase_token) VALUES (:bio, :time, :date, :address, :lon, :lat, :is_finger, :time_status, :firebase_token)";
                            $sql = "INSERT INTO gcctimeutility.app_attendance_spam(biometric_id, time, date, address, longtitude, latitude, is_fingerprint, time_status) VALUES (:bio, :time, :date, :address, :lon, :lat, :is_finger, :time_status)";
                            $data = $conn->prepare($sql); 
                            $data->bindParam(':bio', $bio_num);
                            $data->bindParam(':time', $time);
                            $data->bindParam(':date', $date);
                            $data->bindParam(':address', $this->geoaddress($longitude,$latitude));
                            $data->bindParam(':lon', $longitude);
                            $data->bindParam(':lat', $latitude);
                            $data->bindParam(':is_finger', $stats);
                            $data->bindParam(':time_status', $time_status);
                            // $data->bindParam(':firebase_token', $firebase_token);
                            $data->execute();
                            $insertedID = $conn->lastInsertId();
                            if($data){
                                $resp = 0;
                                if($location != 0){
                                    $resp = $location;
                                }else if($travel_order != 0){
                                    $resp = $travel_order;
                                }else{
                                    $resp = 2;
                                }

                                $arr = [
                                    // 'in_range' => isset($_POST['in_range']) && $_POST['in_range'] === true ? 1 : 0,
                                    'in_range' => $_POST['in_range'],
                                    'bio' => $bio_num,
                                    'last_id' => $insertedID,
                                ];

                                $query = "UPDATE gcctimeutility.app_attendance_spam SET in_range = :in_range WHERE biometric_id = :bio AND id = :last_id";
                                $_data = $conn->prepare($query);
                                
                                if($_data->execute($arr)){
                                    $this->saveLogs("success", $logs_action, $emp_id, "[Mobile] Attendance - user ".$logs_action.".");
                                    return json_encode($this->user_logs_spam($bio_num, $date, $time, $resp, $insertedID, $time));
                                }

                            }else{
                                $this->saveLogs("error", $logs_action, $emp_id, "[Mobile] Attendance - user tried to ".$logs_action." with error in saving.");
                                return 3;
                            }
                        }else{
                            $this->saveLogs("error", $logs_action, $emp_id, "[Mobile] Attendance - user tried to ".$logs_action." with no site location.");
                            return 0;
                        }
                    }else{
                        if($max_time < $time){
                            if(count($sites_id) != 0){
                                $location = $this->polygon_geofence($sites_id, $latitude, $longitude);
                                $travel_order = $this->location_coordinates($bio_num, $latitude, $longitude);
                                // $sql = "INSERT INTO app_attendance_spam(biometric_id, time, date, address, longtitude, latitude, is_fingerprint, time_status, firebase_token) VALUES (:bio, :time, :date, :address, :lon, :lat, :is_finger, :time_status, :firebase_token)";
                                $sql = "INSERT INTO gcctimeutility.app_attendance_spam(biometric_id, time, date, address, longtitude, latitude, is_fingerprint, time_status) VALUES (:bio, :time, :date, :address, :lon, :lat, :is_finger, :time_status)";
                                $data = $conn->prepare($sql);
                                $data->bindParam(':bio', $bio_num);
                                $data->bindParam(':time', $time);
                                $data->bindParam(':date', $date);
                                $data->bindParam(':address', $this->geoaddress($longitude,$latitude));
                                $data->bindParam(':lon', $longitude);
                                $data->bindParam(':lat', $latitude);
                                $data->bindParam(':is_finger', $stats);
                                $data->bindParam(':time_status', $time_status);
                                // $data->bindParam(':firebase_token', $firebase_token);
                                $data->execute();
                                $insertedID = $conn->lastInsertId();
                                if($data){
                                    $this->log($bio_num, $longitude, $latitude);
                                    $resp = 0;
                                    if($location != 0){
                                        $resp = $location;
                                    }else if($travel_order != 0){
                                        $resp = $travel_order;
                                    }else{
                                        $resp = 2;
                                    }
                                    
                                    // $this->saveLogs("success", $logs_action, $emp_id, "[Mobile] Attendance - user ".$logs_action.".");

                                    $arr = [
                                        // 'in_range' => isset($_POST['in_range']) && $_POST['in_range'] == true ? 1 : 0,
                                        'in_range' => $_POST['in_range'],
                                        'bio' => $bio_num,
                                        'last_id' => $insertedID,
                                    ];

                                    $query = "UPDATE gcctimeutility.app_attendance_spam SET in_range = :in_range WHERE biometric_id = :bio AND id = :last_id";
                                    $_data = $conn->prepare($query);
                                    // $_data->execute($arr);
                                
                                    if($_data->execute($arr)){
                                        $this->saveLogs("success", $logs_action, $emp_id, "[Mobile] Attendance - user ".$logs_action.".");
                                        return json_encode($this->user_logs_spam($bio_num, $date, $time, $resp, $insertedID, $time));
                                    }
                                }else{
                                    $this->saveLogs("error", $logs_action, $emp_id, "[Mobile] Attendance - user tried to ".$logs_action." with error in saving.");
                                    return 3;
                                }
                            }else{
                                $this->saveLogs("error", $logs_action, $emp_id, "[Mobile] Attendance - user tried to ".$logs_action." with no site location.");
                                return 0;
                            }
                        }else{
                            $this->saveLogs("error", $logs_action, $emp_id, "[Mobile] Attendance - user tried to ".$logs_action." again while has 1 minute interval.");
                            return json_encode($this->user_logs_spam($bio_num, $date, $time, 4));
                        }
                    }
                }else{
                    return 4;
                }
            }
        }

        public function all_logs(){
            if(isset($_POST['biometric_id']) && $_POST['biometric_id'] != null){
                $biometric_id = $_POST['biometric_id'];
                $date = $_POST['date'];
                return json_encode($this->user_logs($biometric_id, $date));
            }
        }

        public function get_all_time_in_out(){
            if($_POST['device_id']){
                $dev_id = $_POST['device_id'];
                $bio_num = $this->personnel_bio($dev_id);
                $sql = "SELECT * FROM gcctimeutility.app_attendance WHERE biometric_id = :bio_num";
                $data = $conn->prepare($sql);
                $data->bindParam(':bio_num', $bio_num);
                $data->execute();
                if($data->rowCount() != 0){
                    return 1;
                }else{
                    return 0;
                }
            }
        }

        public function date_calendar(){
            $conn = $this->conn("gcctimeutility");
            if(isset($_POST['biometric_id'])){
                $createDate = date_create($_POST['date']);
                $date = date_format($createDate,"Y-m-d");
                $bio_num = $_POST['biometric_id'];
                $sql = "SELECT * FROM gcctimeutility.app_attendance WHERE biometric_id = :bio_num and date = :search_date";
                $data = $conn->prepare($sql);
                $data->bindParam(':bio_num', $bio_num);
                $data->bindParam(':search_date', $date);
                $data->execute();
                if($data->rowCount() != 0){
                    return json_encode($this->user_logs($bio_num, $date));
                }else{
                    return 0;
                }
            }
        }

        public function send_remarks(){
            $conn = $this->conn("gcctimeutility");
            if(isset($_POST['biometric_id']) && $_POST['biometric_id'] != null){
                $bio_num = $_POST['biometric_id'];
                $remarks = $_POST['remarks_text'];
                $id = $_POST['remarks_id'];
                $insert = "UPDATE gcctimeutility.app_attendance SET remarks = :remarks_text WHERE biometric_id = :biometric AND id = :remarks_id";
                $data = $conn->prepare($insert);
                $data->bindParam("biometric", $bio_num);
                $data->bindParam("remarks_text", $remarks);
                $data->bindParam("remarks_id", $id);
                $data->execute();
                if($data){
                    return 1;
                }else{
                    return 0;
                }
            }
        }

        public function time_log_offline(){
            if(isset($_POST['tbl_name']) && $_POST['tbl_name'] != null){
                $arr_data = [];
                $location = json_decode($_POST['coords']);
                $arr_data['emp_id'] = $_POST['emp_id'];
                $arr_data['biometric_id'] = $_POST['biometric_id'];
                $arr_data['time_status'] = $_POST['time_status'];
                $arr_data['longtitude'] = $location->longitude;
                $arr_data['latitude'] = $location->latitude;
                $this->store_logs($_POST, $this->personnel_id($_POST['biometric_id']));
                if($_POST['date'] != "" && $_POST['date'] != null){
                    $datetime = explode(" ", $_POST['date']);
                    $arr_data['date'] = $datetime[0];
                    $arr_data['time'] = $datetime[1];
                }
                $arr[] = $arr_data;
                if(!$this->check_attendance($datetime[1], $datetime[0])){
                    return $this->insert_logs($arr_data);
                }
            }
        }

        public function time_log_offline_spam(){
            // return $_POST['coords']. ' tbl_name ->'.$_POST['tbl_name'];
            if(isset($_POST['tbl_name']) && $_POST['tbl_name'] != null){
                $arr_data = [];
                $location = json_decode($_POST['coords']);
                $arr_data['emp_id'] = $_POST['emp_id'];
                $arr_data['biometric_id'] = $_POST['biometric_id'];
                $arr_data['time_status'] = $_POST['time_status'];
                $arr_data['longtitude'] = $location->longitude;
                $arr_data['latitude'] = $location->latitude;
                // $arr_data['firebase_token'] = $_POST['firebase_token'];
                $this->store_logs_spam($_POST, $this->personnel_id($_POST['biometric_id']));
                if($_POST['date'] != "" && $_POST['date'] != null){
                    $datetime = explode(" ", $_POST['date']);
                    $arr_data['date'] = $datetime[0];
                    $arr_data['time'] = $datetime[1];
                }
                $arr[] = $arr_data;
                if(!$this->check_attendance_spam($datetime[1], $datetime[0])){
                    return $this->insert_logs_spam($arr_data);
                }
            }
        }

        private function setInterval($bionum){
            $date = date("Y-m-d");
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT time FROM gcctimeutility.app_attendance WHERE biometric_id = :bio_num AND date = :today  ORDER BY time DESC LIMIT 1";
            $data = $conn->prepare($sql);
            $data->bindParam(":bio_num", $bionum);
            $data->bindParam(":today", $date);
            $data->execute();
            $time = $data->fetch(PDO::FETCH_ASSOC);
            return $time['time'];
        }

        private function setInterval_spam($bionum){
            $date = date("Y-m-d");
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT time FROM gcctimeutility.app_attendance_spam WHERE biometric_id = :bio_num AND date = :today  ORDER BY time DESC LIMIT 1";
            $data = $conn->prepare($sql);
            $data->bindParam(":bio_num", $bionum);
            $data->bindParam(":today", $date);
            $data->execute();
            $time = $data->fetch(PDO::FETCH_ASSOC);
            return $time['time'];
        }

        private function check_attendance($time, $date){
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.app_attendance WHERE time = :time_attendance AND date = :today";
            $data = $conn->prepare($sql);
            $data->bindParam(":time_attendance", $time);
            $data->bindParam(":today", $date);
            $data->execute();
            $count = $data->rowCount();
            if($count > 0){
                $resp = true;
            }else{
                $resp = false;
            }
            return $resp;
        }

        private function check_attendance_spam($time, $date){
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.app_attendance_spam WHERE time = :time_attendance AND date = :today";
            $data = $conn->prepare($sql);
            $data->bindParam(":time_attendance", $time);
            $data->bindParam(":today", $date);
            $data->execute();
            $count = $data->rowCount();
            if($count > 0){
                $resp = true;
            }else{
                $resp = false;
            }
            return $resp;
        }

        private function insert_logs($arr = []){
            $response['response_array'] = array();
            $list = array();
            $emp_id = $arr['emp_id'];
            $bio_num = $arr['biometric_id'];
            $time = $arr['time'];
            $date = $arr['date'];
            $latitude = $arr['latitude'];
            $longitude = $arr['longtitude'];
            $time_status = $arr['time_status'];
            $stats = 1;
            if($arr['latitude'] != null && $arr['longtitude'] != null){
                $conn = $this->conn("gcctimeutility");
                $sql = "INSERT INTO gcctimeutility.app_attendance(biometric_id, time, date, address, longtitude, latitude, time_status, is_fingerprint) VALUES (:bio, :time, :date, :address, :lon, :lat, :time_status, :is_finger)";
                $data = $conn->prepare($sql);
                $data->bindParam(':bio', $bio_num);
                $data->bindParam(':time', $time);
                $data->bindParam(':date', $date);
                $data->bindParam(':address', $this->geoaddress($longitude,$latitude));
                $data->bindParam(':lon', $longitude);
                $data->bindParam(':lat', $latitude);
                $data->bindParam(':time_status', $time_status);
                $data->bindParam(':is_finger', $stats);
                $data->execute();
                if ($data) {
                    $last_id = $conn->lastInsertId();
                    $list['cloud_id'] = $last_id;
                    $list['status'] = true;

                    $logs_action = '';
                    if($time_status==='in'){ $logs_action = 'time in'; } 
                    else if($time_status==='out'){ $logs_action = 'time out'; } 
                    else { $logs_action = $time_status; }
                    $this->saveLogs("success", $logs_action, $emp_id, "[Mobile] Attendance - user ".$logs_action." thru offline to online sync.");
                    $this->log($bio_num, $longitude, $latitude);

                } else {
                    $list['status'] = false;
                }
            } else {
                $list['status'] = false;
            }
            
            array_push($response['response_array'], $list);

            return json_encode($response);
        }

        private function insert_logs_spam($arr = []){
            $response['response_array'] = array();
            $list = array();
            $emp_id = $arr['emp_id'];
            $bio_num = $arr['biometric_id'];
            $time = $arr['time'];
            $date = $arr['date'];
            $latitude = $arr['latitude'];
            $longitude = $arr['longtitude'];
            $time_status = $arr['time_status'];
            // $firebase_token = $arr['firebase_token'];
            // $stats = 1;
            if($arr['latitude'] != null && $arr['longtitude'] != null){
                $conn = $this->conn("gcctimeutility");
                // $sql = "INSERT INTO app_attendance_spam(biometric_id, time, date, address, longtitude, latitude, time_status, is_fingerprint, firebase_token) VALUES (:bio, :time, :date, :address, :lon, :lat, :time_status, :is_finger, :firebase_token)";
                $sql = "INSERT INTO gcctimeutility.app_attendance_spam(biometric_id, time, date, address, longtitude, latitude, time_status) VALUES (:bio, :time, :date, :address, :lon, :lat, :time_status)";
                $data = $conn->prepare($sql);
                $data->bindParam(':bio', $bio_num);
                $data->bindParam(':time', $time);
                $data->bindParam(':date', $date);
                $data->bindParam(':address', $this->geoaddress($longitude,$latitude));
                $data->bindParam(':lon', $longitude);
                $data->bindParam(':lat', $latitude);
                $data->bindParam(':time_status', $time_status);
                // $data->bindParam(':is_finger', $stats);
                // $data->bindParam(':firebase_token', $firebase_token);
                $data->execute();
                if ($data) {
                    $last_id = $conn->lastInsertId();
                    $list['cloud_id'] = $last_id;
                    $list['status'] = true;

                    $logs_action = '';
                    if($time_status==='in'){ $logs_action = 'time in'; } 
                    else if($time_status==='out'){ $logs_action = 'time out'; } 
                    else { $logs_action = $time_status; }
                    $this->saveLogs("success", $logs_action, $emp_id, "[Mobile] Attendance - user ".$logs_action." thru offline to online sync.");
                    $this->log($bio_num, $longitude, $latitude);

                    $arr = [
                        // 'in_range' => isset($_POST['in_range']) && $_POST['in_range'] === true ? 1 : 0,
                        'in_range' => $_POST['in_range'],
                        'bio' => $bio_num,
                        'last_id' => $last_id,
                    ];

                    $query = "UPDATE gcctimeutility.app_attendance_spam SET in_range = :in_range WHERE biometric_id = :bio AND id = :last_id";
                    $_data = $conn->prepare($query);
                    $_data->execute($arr);

                    // $query = "UPDATE app_attendance_spam SET in_range = :in_range WHERE id = :last_id";
                    // $_data = $conn->prepare($query);

                    // if(isset($_POST['in_range']) && $_POST['in_range'] == true){
                    //     $_data->bindParam(":in_range", 1);
                    // }else{
                    //     $_data->bindParam(":in_range", 1);
                    // }
                    // $_data->bindParam(":last_id", $last_id);
                    // $_data->execute();

                    // if($_data){
                    //     return 1;
                    // }else{
                    //     return 0;
                    // }

                } else {
                    $list['status'] = false;
                }
            } else {
                $list['status'] = false;
            }
            
            array_push($response['response_array'], $list);

            return json_encode($response);
        }

        private function personnel_bio($device_id){
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT biometric_no FROM gcctimeutility.app_users WHERE device_id = :device";
            $data = $conn->prepare($sql);
            $data->bindParam(':device', $device_id);
            $data->execute();
            if($data->rowCount() != 0){
                $biometric = $data->fetch(PDO::FETCH_ASSOC);
                return $biometric['biometric_no'];
            }else{
                return 0;
            }
        }

        private function personnel_id($bio_id){
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT id as person_id FROM gcctimeutility.personnel WHERE biometric_id = :bioId";
            $data = $conn->prepare($sql);
            $data->bindParam(':bioId', $bio_id);
            $data->execute();
            if($data->rowCount() != 0){
                $person_id = $data->fetch(PDO::FETCH_ASSOC);
                return $person_id['person_id'];
            }
        }

        private function sites_location_id($personel_id){
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.personnel_locations WHERE personnel_id = :personel_id";
            $data = $conn->prepare($sql);
            $data->bindParam(':personel_id', $personel_id);
            $data->execute();
            $count = $data->rowCount();
            if($count != 0){
                $location = array();
                while ($row = $data->fetch(PDO::FETCH_ASSOC))
                {
                    array_push($location, $row['site_location_id']);
                }
                return $location;
            }else{
                return 0;
            }
        }

        private function polygon_geofence($sites_id, $lat, $lon){
            if(count($sites_id) > 0){
                $response = 0;
                $res_count = [];
                foreach($sites_id as $ids){
                    $conn = $this->conn("gcctimeutility");
                    $sql = "SELECT * FROM gcctimeutility.app_location_sites WHERE id = :site_id";
                    $data = $conn->prepare($sql);
                    $data->bindParam(':site_id', $ids);
                    $data->execute();
                    $site = $data->rowCount();
                    if($site != 0){
                        $allData = $data->fetch(PDO::FETCH_ASSOC);
                        $geofenctCoords = unserialize($allData['geofence_polygon']);
                        if($this->getInsideParam($geofenctCoords, $lat, $lon)){
                            $res_count[] = 1;
                        }
                    }
                }
                if(count($res_count) != 0){
                    $response = 1;
                }
                return $response;
            }
            
        }

        private function getInsideParam($arra_coords, $lat, $lon){
            $arrCoords = [];
            foreach($arra_coords as $coords){
                $arrCoords[] = array($coords['lat'], $coords['lng']);
            }
            $isInside = false;
            $temp = array("lat"=>$lat, "long"=>$lon);

            // var_dump($arrCoords);

            $lastPoint = $arrCoords[count($arrCoords) - 1];

            $x = $temp["long"];
            foreach ($arrCoords as $key => $item){
                $x1 = $lastPoint[1];
                $x2 = $item[1];
                $dx = $x2 - $x1;
                if (abs($dx) > 180){
                    if($x > 0){
                        while($x1 < 0){
                            $x1 += 360;
                        }
                        while($x2 < 0){
                            $x2 += 360;
                        }
                    }else{
                        while($x1 > 0){
                            $x1 -= 360;
                        }
                        while($x2 > 0){
                            $x2 -= 360;
                        }
                    }
                    $dx = $x2 - $x1;
                }

                if (($x1 <= $x && $x2 > $x) || ($x1 >= $x && $x2 < $x)){
                    $grad = ($item[0] - $lastPoint[0]) / $dx;
                    $intersectAtLat = $lastPoint[0] + (($x - $x1) * $grad);
                    $longDiff = $x - $x1;
                    $xGrad = $longDiff * $grad;

                    if ($intersectAtLat > $temp["lat"]){
                        $isInside = !$isInside;
                    }
                }

                $lastPoint = $item;
            }

            return $isInside;
        }

        private function location_coordinates($biometric, $lat, $lon){
            $datetime = date("Y-m-d H:i:s");
            $emp_id = $this->get_emp_id($biometric);
            $travel_order_id = $this->trave_order_id($emp_id);
            if($travel_order_id != null){
                $conn = $this->conn("gcceforms");
                $sql = "SELECT coords_from, coords_to FROM gcceforms.travel_destination WHERE travel_order_id = :travel_order_id and date_to >= :date_to";
                $travel = $conn->prepare($sql);
                $travel->bindParam(":travel_order_id", $travel_order_id);
                $travel->bindParam(":date_to", $datetime);
                $travel->execute();
                $count = $travel->rowCount();
                if($count == 1){
                    $data = $travel->fetch(PDO::FETCH_ASSOC);
                    $from = json_decode($data['coords_from']);
                    $to = json_decode($data['coords_to']);
                    $maxlat = $from->lat  + 0.006;
                    $minlat = $from->lat  - 0.006;
                    $maxlon = $from->lng  + 0.0006;
                    $minlon = $from->lng  - 0.0006;
                    $toMaxlat = $to->lat  + 0.006;
                    $toMinlat = $to->lat  - 0.006;
                    $toMaxlon = $to->lng  + 0.0006;
                    $toMinlon = $to->lng  - 0.0006;
                    if($maxlat >= $lat || $minlat <= $lat && $maxlon >= $lon || $minlon <= $lon){
                        return 1;
                    }else if($toMaxlat >= $lat || $toMinlat <= $lat && $toMaxlon >= $lng || $toMinlon <= $lng){
                        return 1;
                    }else{
                        return 0;
                    }
                }else{
                    return 0;
                }
            }else{
                return 0;
            }
            
        }

        private function user_logs($bio_id, $current_date = "", $current_time = "", $geofence = 0, $id = 0){
            if($current_date != ""){
                $date = $current_date;
            }else{
                $date = date("Y-m-d");
            }
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.app_attendance WHERE biometric_id = :bio_id and date = :date ORDER BY updated_at";
            $all_logs = array();
            $data = $conn->prepare($sql);
            $data->bindParam(":bio_id", $bio_id);
            $data->bindParam(":date", $date);
            $data->execute();
            $count = $data->rowCount();
            $num = 0;

            // rework for shif schedule
            while($row = $data->fetch(PDO::FETCH_ASSOC)){
                $num++;
                $style = "";
                if($num % 2 == 0){
                    if($row['time'] > '12:00:00' && $row['time'] < '13:00:00' || $row['time'] > '18:00:00'){
                        $style = "logout";
                    }else if($row['time'] > '08:00' && $row['time'] < '12:00' || $row['time'] > '13:00' && $row['time'] < '18:00'){
                        $style = "undertime";
                    }
                }else{
                    if($row['time'] <= '08:00:00' || $row['time'] > '12:00:00' && $row['time'] < '13:00:00'){
                        $style = "login";
                    }else if($row['time'] >= '08:00:00' && $row['time'] < '12:00:00' || $row['time'] > '13:00:00' && $row['time'] < '18:00:00'){
                        $style = "late";
                    }
                }
                array_push($all_logs, array("logTime" => $row['time'], "date" => $row['date'], "time" => date('h:i:s a',strtotime($row['time'])), "style" => $style, "status" => $row['time_status']));
            }
            // end of rework for shift schedule
            return array("logs" => $all_logs, "num" => $count, "status" => $geofence, "insertId" => $id, "date" => $date.' '.$current_time);
        }

        private function user_logs_spam($bio_id, $current_date = "", $current_time = "", $geofence = 0, $id = 0){
            if($current_date != ""){
                $date = $current_date;
            }else{
                $date = date("Y-m-d");
            }
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.app_attendance_spam WHERE biometric_id = :bio_id and date = :date ORDER BY updated_at";
            $all_logs = array();
            $data = $conn->prepare($sql);
            $data->bindParam(":bio_id", $bio_id);
            $data->bindParam(":date", $date);
            $data->execute();
            $count = $data->rowCount();
            $num = 0;

            // rework for shif schedule
            while($row = $data->fetch(PDO::FETCH_ASSOC)){
                $num++;
                $style = "";
                if($num % 2 == 0){
                    if($row['time'] > '12:00:00' && $row['time'] < '13:00:00' || $row['time'] > '18:00:00'){
                        $style = "logout";
                    }else if($row['time'] > '08:00' && $row['time'] < '12:00' || $row['time'] > '13:00' && $row['time'] < '18:00'){
                        $style = "undertime";
                    }
                }else{
                    if($row['time'] <= '08:00:00' || $row['time'] > '12:00:00' && $row['time'] < '13:00:00'){
                        $style = "login";
                    }else if($row['time'] >= '08:00:00' && $row['time'] < '12:00:00' || $row['time'] > '13:00:00' && $row['time'] < '18:00:00'){
                        $style = "late";
                    }
                }
                array_push($all_logs, array("logTime" => $row['time'], "date" => $row['date'], "time" => date('h:i:s a',strtotime($row['time'])), "style" => $style, "status" => $row['time_status']));
            }
            // end of rework for shift schedule
            return array("logs" => $all_logs, "num" => $count, "status" => $geofence, "insertId" => $id, "date" => $date.' '.$current_time);
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

        private function get_emp_id($biono){
            $conn = $this->conn();
            $sql = "SELECT id FROM tblemployees WHERE biometricno = :bionum";
            $emp_data = $conn->prepare($sql);
            $emp_data->bindParam(":bionum", $biono);
            $emp_data->execute();
            $data = $emp_data->fetch(PDO::FETCH_ASSOC);
            return $data['id'];
        }

        private function trave_order_id($emp_id){
            $conn = $this->conn("gcceforms");
            $sql = "SELECT travel_order_id FROM gcceforms.travel_personnel WHERE employee_id = :emp_id";
            $to_data = $conn->prepare($sql);
            $to_data->bindParam(":emp_id", $emp_id);
            $to_data->execute();

            $data = $to_data->fetch(PDO::FETCH_ASSOC);
            return $data['travel_order_id'];
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

        function fetch_user_attendance(){
            $conn = $this->conn("gcctimeutility");
            $biometric_id = $_POST['biometric_id'];
            $array = array();
            $sth = $conn->prepare("SELECT id, time_status, biometric_id, latitude, longtitude, date, time
                                   FROM gcctimeutility.app_attendance 
                                   WHERE biometric_id='$biometric_id'");
            $sth->execute();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list = array();
                $list['is_sync'] = 'true';
                $list['cloud_id'] = $row['id'];
                $list['status'] = $row['time_status'];
                $list['biometric'] = $row['biometric_id'];
                $list['location'] = $row['latitude'].', '.$row['longtitude'];
                $list['date'] = $row['date'].' '.$row['time'];
                array_push($array, $list);
            }
            return json_encode($array);
        }

        private function log($biometric_id, $lon, $lat){
            $storage = dir."/androidapp/storage/logs/timeInOut/";
            $date = $datetime = date("Y-m-d H:i:s");
            if(!is_dir($storage)){
                mkdir($storage, 777);
            }

            //Something to write to txt log
            $log  = "Date: {$date}\nUser: {$biometric_id}\nCoordinates: lat:{$lat},lon:{$lon}\n\n";
            //Save string to log, use FILE_APPEND to append.
            file_put_contents($storage.''.$biometric_id.'.log', $log, FILE_APPEND);
        }

        function getLocation(){
            $post = $_POST;
            $resultset = array();
            $arrData = array();
            $location = array();

            // personnel
            // 
            if(isset($post['biometricno']) && $post['biometricno']){
                $bio = $post['biometricno'];
                $conn = $this->conn("gcctimeutility");
                // SELECT c.geofence_polygon FROM personnel AS a LEFT JOIN personnel_locations AS b ON b.personnel_id = a.id LEFT JOIN app_location_sites AS c ON c.id = b.site_location_id WHERE a.biometric_id = 10838 OR a.biometricno = 10838;
                $sql = "SELECT c.geofence_polygon FROM gcctimeutility.personnel AS a LEFT JOIN gcctimeutility.personnel_locations AS b ON b.personnel_id = a.id LEFT JOIN gcctimeutility.app_location_sites AS c ON c.id = b.site_location_id WHERE a.biometric_id = '$bio' OR a.biometricno = '$bio'";
                $allData = $conn->prepare($sql);
                // $allData->bindParam(':biometric_id', $bio);
                // $allData->bindParam(':biono', $bio);
                $allData->execute();
                $count = $allData->rowCount();

                if($count != 0){
                    $row = $allData->fetchAll(PDO::FETCH_ASSOC);
                    $geolocation = @unserialize($row['geofence_polygon']);

                    foreach($row as $key => $rs){
                        // $geolocation = @unserialize($rs['geofence_polygon']);

                        $rs = $this->changeGeoKey($rs['geofence_polygon']);

                        $arrData[$key] = $rs;

                    }

                    foreach ($arrData as $k => $v) {
                        $resultset[] = $v;
                    }

                    // $json = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $geolocation);
                    return json_encode($resultset);
                }else{
                    return 'No assigned Location';
                    // return json_encode(array("geolocation" => 'No assigned Location'));
                }
            }else{
                return 'No biometric found.';
            }
        }

        public function changeGeoKey($location){

            $geolocation = @unserialize($location);
            
            foreach($geolocation as $key => $row){
                $geolocation[$key]['latitude'] = $geolocation[$key]['lat'];
                $geolocation[$key]['longitude'] = $geolocation[$key]['lng'];

                unset($geolocation[$key]['lat'], $geolocation[$key]['lng']);
            }

            return $geolocation;
        }
    }
?>