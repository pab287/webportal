<?php
    class Gcctime_app_m extends Dbase{
        use Logs_maker;

        // Author: Rockefeller
        // Date: 27/08/2025

        public function gcctimeEformLogin() {
            $r = $_POST;
            $required = ['username', 'password', 'unique_id', 'device_id', 'device_name'];
            $msg = "";
            foreach ($required as $key) {
                if (empty($r[$key])) {
                    return json_encode(["status" => false, "msg" => ucfirst(str_replace("_", " ", $key)) . " is missing."]);
                }
            }
        
            $conn = $this->conn("gccmaster");
            $username = $r['username'];
            $password = md5($r['password']);
            $unique_id = $r['unique_id'];
            $device_id = $r['device_id'];
            $device_name = str_replace(" ", "_", $r['device_name']);
        
            $sql = "SELECT a.biometricno, a.id, a.firstname, b.username, b.emp_id, b.is_suspended,
                        a.idno, c.name AS position_name, a.pic_filename, a.lastname,
                        IFNULL(d.code, 'No Company') AS company,
                        IFNULL(e.code, 'No Department') AS department, a.level
                    FROM tblemployees a
                    LEFT JOIN tblusers b ON a.id = b.emp_id
                    LEFT JOIN gcchris.tblposition c ON c.id = a.position OR c.name = a.position
                    LEFT JOIN gcchris.tblcompanies d ON d.id = a.company_id
                    LEFT JOIN gcchris.tbldepartments e ON e.id = a.department_id OR e.description = a.department_id
                    WHERE (a.biometricno = :usern OR b.username = :usern) AND b.password = :passw";
        
            $sth = $conn->prepare($sql);
            $sth->execute([':usern' => $username, ':passw' => $password]);
            $emp_data = $sth->fetch(PDO::FETCH_ASSOC);
        
            if ($emp_data) {
                $app_user_id = md5($unique_id . $emp_data['id']);
                $check = $this->checkUserExist($emp_data['id'], $device_name, $device_id, $app_user_id, $unique_id, $emp_data['biometricno']);
        
                if ($check === 'grant_access') {
                    $this->saveLogs("success", "sign in", $emp_data['id'], "[Mobile] User sign in");
                    $this->updateUserStatus($emp_data['id'], $app_user_id, $unique_id, $device_id, $device_name);
                    return json_encode([
                        "status" => true,
                        "user_data" => [
                            "emp_id" => $emp_data['id'],
                            "app_user_id" => $app_user_id,
                            "biometric_id" => $emp_data['biometricno'],
                            "firstname" => $emp_data['firstname'],
                            "lastname" => $emp_data['lastname'],
                            "idno" => $emp_data['idno'],
                            "position_name" => $emp_data['position_name'],
                            "pic_filename" => $emp_data['pic_filename'],
                            "lvl_ranking" => $emp_data['level'],
                            "device_name" => $device_name,
                            "device_id" => $device_id,
                            "unique_id" => $unique_id,
                            "company" => $emp_data['company'],
                            "department" => $emp_data['department'],
                            "is_suspended" => $emp_data['is_suspended'],
                        ]
                    ]);
                }
                $msg = $check;
            }else{
                $msg = "Username or password is incorrect.";
            }
            return json_encode(["status" => false, "msg" => $msg]);
        }
        

        public function isSignoutv311() {
            $emp_id = $_POST['emp_id'] ?? null;
            $token = $_POST['token'] ?? null;
            $msg = "";
            $status = false;
            $proceed = true;

            $validate_token = $this->checkToken($emp_id, $token);

            if (!$validate_token) {
                $this->saveLogs("error", "sign out", 0, "[Mobile] User sign out failed - missing token");
                $msg = "Invalid token to sign out.";
                $proceed = false;
            }
            if (!$emp_id) {
                $this->saveLogs("error", "sign out", 0, "[Mobile] User sign out failed - missing emp_id");
                $msg = "Employee ID not found to sign out.";
                $proceed = false;
            }

            if($proceed){
                $conn = $this->conn("gcctimeutility");
                $stmt = $conn->prepare("UPDATE gcctimeutility.app_users SET status = 1 WHERE emp_id = :emp_id");
                $stmt->bindParam(':emp_id', $emp_id);
    
                if ($stmt->execute()) {
                    $this->saveLogs("success", "sign out", $emp_id, "[Mobile] User signed out");
                    $msg = "Successfully signed out.";
                    $status = true;
                }
            }
            

            return json_encode(["status" => $status, "msg" => $msg]);

        }

        
        public function portalModules(){
            $conn = $this->conn();
            
            if(!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
                return json_encode(["status" => false, "msg" => "Employee ID not found."]);
            }

            $employee_id = $_POST['employee_id'];

            $sth = $conn->query("SELECT u.role_id,u.emp_id,e.employee_status FROM tblusers as u
                                LEFT JOIN tblemployees AS e 
                                ON e.id = u.emp_id
                                WHERE emp_id='$employee_id'");
            $data = $sth->fetch(PDO::FETCH_ASSOC);
            $count = $sth->rowCount();

            if($count == 0) {
                return json_encode(["status" => false, "msg" => "Employee ID not found. No Module to provide."]);
            }

            $role_id = $data['role_id'];
            $list['portal_modules'] = $this->getPortalModules($role_id);
            return json_encode($list);
        }

        public function userPrivilege(){
            $conn = $this->conn();
            
            if(!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
                return json_encode(["status" => false, "msg" => "Employee ID not found."]);
            }

            $employee_id = $_POST['employee_id'];

            $sth = $conn->query("SELECT u.role_id,u.emp_id,e.employee_status FROM tblusers as u
                                LEFT JOIN tblemployees AS e 
                                ON e.id = u.emp_id
                                WHERE emp_id='$employee_id'");
            $data = $sth->fetch(PDO::FETCH_ASSOC);
            $count = $sth->rowCount();
            if($count == 0) {
                return json_encode(["status" => false, "msg" => "Employee ID not found. No Privilege to provide."]);
            }

            $role_id = $data['role_id'];
            $list['privilege'] = $this->getPrivilege($role_id);
            return json_encode($list);

        }

        public function getGeofence() {
            if (empty($_POST['biometricno'])) {
                return json_encode(["message" => "No biometric found", "status" => false]);
            }
        
            $bio = $_POST['biometricno'];
            $conn = $this->conn("gcctimeutility");
        
            $sql = "SELECT c.id, c.site_name, c.geofence_polygon, c.latitude, c.longtitude 
                    FROM gcctimeutility.personnel a
                    LEFT JOIN gcctimeutility.personnel_locations b ON b.personnel_id = a.id
                    LEFT JOIN gcctimeutility.app_location_sites c ON c.id = b.site_location_id
                    WHERE a.biometric_id = :bio OR a.biometricno = :bio";
        
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':bio', $bio);
            $stmt->execute();
        
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$rows) {
                return json_encode(["message" => "No assigned Location", "status" => false]);
            }
        
            $resultset = array_filter(array_map(function ($row) {
                if (empty($row['geofence_polygon'])) return null;
                return [
                    "data" => [
                        "id" => $row["id"],
                        "site_name" => $row["site_name"],
                        "latitude" => $row["latitude"],
                        "longtitude" => $row["longtitude"],
                    ],
                    "geofence_polygon" => $this->changeGeoKey($row["geofence_polygon"])
                ];
            }, $rows));
        
            if (!$resultset) {
                return json_encode(["message" => "No assigned location", "status" => false]);
            }
            return json_encode(["message" => "Success", "sitelocation" => array_values($resultset), "status" => true]);
        }

        public function fetchAttendance() {

            $emp_id = $_POST['emp_id'];
            $biometric_id = $_POST['biometric_id'];

            if(!$this->userExists($biometric_id, $emp_id)){
                return json_encode([
                    "status" => false,
                    "msg" => "Parameters does not match, user not found."
                ]);
            };

            $conn = $this->conn("gcctimeutility");
            $connzkt = $this->conn("zktime_logs");
            $array = array();
            
            $appAttendance = $conn->prepare("SELECT id, state, time_status, biometric_id, latitude, longtitude, date, time
                                    FROM gcctimeutility.app_attendance 
                                    WHERE biometric_id = :biometric_id
                                    AND MONTH(date) = MONTH(CURRENT_DATE())
                                    AND YEAR(date) = YEAR(CURRENT_DATE())");
            
            $appAttendance->bindParam(':biometric_id', $biometric_id, PDO::PARAM_STR);
            $appAttendance->execute();
        
            while ($row = $appAttendance->fetch(PDO::FETCH_ASSOC)) {
                $list = array();
                $list['is_sync'] = 'true';
                $list['emp_id'] = $emp_id;
                $list['cloud_id'] = $row['id'];
                $list['status'] = $row['time_status'];
                $list['biometric'] = $row['biometric_id'];
                $list['location'] = $row['latitude'] . ', ' . $row['longtitude'];
                $list['date'] = $row['date'] . ' ' . $row['time'];
                $list['state'] = ($row['state'] === '' || $row['state'] === '0') ? 'in' : 'out';
                $list['log_device'] = 'app';
                array_push($array, $list);
            }

            $bioAttendance = $connzkt->prepare("SELECT id, date, biometricno, verify_method
                                        FROM attendance
                                        WHERE biometricno = :biometric_id
                                        AND MONTH(date) = MONTH(CURRENT_DATE())
                                        AND YEAR(date) = YEAR(CURRENT_DATE())");
            
            $bioAttendance->bindParam(':biometric_id', $biometric_id, PDO::PARAM_STR);
            $bioAttendance->execute();
        
            while ($row = $bioAttendance->fetch(PDO::FETCH_ASSOC)) {
                $list = array();
                $list['is_sync'] = 'true';
                $list['emp_id'] = $emp_id;
                $list['cloud_id'] = $row['id'];
                $list['status'] = $this->verifyMethodChecker($row['verify_method']);
                $list['biometric'] = $row['biometricno'];
                $list['location'] = '';
                $list['date'] = $row['date'];
                $list['state'] = 'in';
                $list['log_device'] = 'bio';
                array_push($array, $list);
            }
            return json_encode($array);
        }

        public function timeLogOffline() {
            $rawInput = $_POST['logs'];
            $decoded = json_decode(urldecode($rawInput), true);
        
            if ($decoded === null) {
                return json_encode(["status" => false,"msg" => "JSON Decode Error: " . json_last_error_msg()]);
            }
        
            if (!isset($decoded['logs']) || !is_array($decoded['logs'])) {
                return json_encode(["error" => "Invalid or missing logs array"]);
            }
        
            $logs = $decoded['logs'];
            $response['data'] = [];
            $conn = $this->conn("gcctimeutility");
        
            $successCount = 0;
            $failedCount = 0;
        
            foreach ($logs as $log) {
                $list = [];
        
                $location = json_decode($log['location'], true);
                $longitude = $location['longitude'];
                $latitude = $location['latitude'];
        
                $emp_id = $log['emp_id'];
                $bio_num = $log['biometric'];
                $date_time = explode(" ", $log['date']);
                $date = $date_time[0];
                $time = $date_time[1];
                $time_status = $log['status'];
                $stats = 1;
                $address = $this->geoaddress($longitude, $latitude);

                $geo_status = (isset($log['state']) && $log['state'] === "in") ? 0 : 1;
                $timeStatus = $this->timeStatusString($time_status);
                if (!isset($log['state']) || ($log['state'] !== "in" && $log['state'] !== "out")) {
                    $remarks_state = "The GCCtime app version in use is outdated.";
                }

                $remarks = isset($log['remarks']) ? $log['remarks'] : $remarks_state;

                $date_time = $date . " " . $time;
        
                if ($latitude !== null && $longitude !== null) {

                    $checkSql = "SELECT COUNT(*) FROM gcctimeutility.app_attendance
                                WHERE biometric_id = :bio AND date = :date AND time = :time AND state = :state";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->bindParam(':bio', $bio_num);
                    $checkStmt->bindParam(':date', $date);
                    $checkStmt->bindParam(':time', $time);
                    $checkStmt->bindParam(':state', $geo_status);
                    $checkStmt->execute();
                    $exists = $checkStmt->fetchColumn();

                    if ($exists > 0) {
                        $list['status'] = false;
                        $list['error'] = 'Duplicate entry';
                        $failedCount++;
                    } else {

                        $sql = "INSERT INTO gcctimeutility.app_attendance
                                (biometric_id, state, time, date, address, longtitude, latitude, remarks, time_status, is_fingerprint)
                                VALUES (:bio, :state, :time, :date, :address, :lon, :lat,:remarks, :time_status, :is_finger)";
                        $data = $conn->prepare($sql);
                        $data->bindParam(':bio', $bio_num);
                        $data->bindParam(':state', $geo_status);
                        $data->bindParam(':time', $time);
                        $data->bindParam(':date', $date);
                        $data->bindParam(':address', $address);
                        $data->bindParam(':lon', $longitude);
                        $data->bindParam(':lat', $latitude);
                        $data->bindParam(':remarks', $remarks);
                        $data->bindParam(':time_status', $time_status);
                        $data->bindParam(':is_finger', $stats);
            
                        if ($data->execute()) {
                            $list['status'] = true;
                            $list['cloud_id'] = $conn->lastInsertId();
                            $successCount++;
                            $logs_action = ($time_status === 'in' || $time_status === 'out') ? "time $time_status" : $time_status;
                            $this->saveLogs("success", $logs_action, $emp_id, "[Mobile] Attendance - user $logs_action thru offline to online sync.");
                            $this->log($bio_num, $longitude, $latitude);
                            $this->getSupervisorManager($emp_id, $remarks, $timeStatus, $geo_status, $date_time, $bio_num, $latitude, $longitude);
                        } else {
                            $list['status'] = false;
                            $failedCount++;
                        }
                    }
                } else {
                    $list['status'] = false;
                    $failedCount++;
                }
                array_push($response['data'], $list);
            }
            $response['summary'] = ['success' => $successCount,'failed' => $failedCount];
            return json_encode($response);
        }

        public function timeLog() {
            $date = date("Y-m-d");
            $time = date("H:i:s");
            $dateTime = "$date $time";
            $logs_action = 'time log';
            $msg = "";
            $status = 2;
            
            $required = ['biometric_id', 'emp_id', 'coords'];
            $missing = array_filter($required, fn($f) => empty($_POST[$f]));
            if ($missing) {
                $status = 0;
                $msg = "Missing or empty field(s): " . implode(", ", $missing);
            }
        
            $bio = $_POST['biometric_id'];
            $emp = $_POST['emp_id'];
            $time_status = isset($_POST['time_status']) ? $_POST['time_status'] : '';

            if (empty($time_status)) {
                $status = 0;
                $msg = "Missing or empty field(s): time status.";
            }

            $remarks = $_POST['remarks'] ?? '';
            $coords = json_decode($_POST['coords'], true);
        
            if (!isset($coords['latitude'], $coords['longitude'])) {
                $this->saveLogs("error", $logs_action, $emp, "[Mobile] No coordinates");
                $status = 0;
                $msg = "Coordinates not found.";
            }
        
            if (isset($_POST['state'])) {
                if ($_POST['state'] === "in") {
                    $geo_status = 0;
                } elseif ($_POST['state'] === "out") {
                    $geo_status = 1;
                } else {
                    $geo_status = 2;
                }
            } else {
                $geo_status = 2;
            }
        
            if ($geo_status === 1 && empty($remarks)) {
                $status = 0;
                $msg = "Missing or empty field(s): remarks.";
            }
        
            if (!in_array($_POST['state'] ?? '', ["in", "out"])) {
                $remarks = "The GCCtime app version in use is outdated.";
            }
        
            $latitude = $coords['latitude']?? '';
            $longitude = $coords['longitude']?? '';
            $personnel_id = $this->personnel_id($bio);
            $sites_id = $this->sites_location_id($personnel_id);
            $interval = $this->setInterval($bio);
            $max_time = date('H:i:s', strtotime("$interval +1 minute"));
        
            if (!$sites_id) {
                $this->saveLogs("error", $logs_action, $emp, "[Mobile] No site location");
                $status = 0;
                $msg = "No Site Location Found.";
            }
        
            if ($interval && $max_time >= $time) {
                $status = 4;
            }

            if ($status === 0 || $status === 4) {
                if ($status === 4) {
                    $msg = "Re-logged within 1-minute interval";
                }
                $this->saveLogs("error", $logs_action, $emp, "[Mobile] $msg");
                return json_encode(["status" => $status, "msg" => $msg]);
            }
        
            $this->store_logs($_POST, $personnel_id);
            $data = [
                'biometric_id' => $bio,
                'state' => $geo_status,
                'time' => $time,
                'date' => $date,
                'address' => $this->geoaddress($longitude, $latitude),
                'longitude' => $longitude,
                'latitude' => $latitude,
                'is_fingerprint' => 1,
                'remarks' => $remarks,
                'time_status' => $time_status
            ];
        
            $insertedID = $this->addAppAttendanceRecord($data);
        
            if (!$insertedID) {
                $this->saveLogs("error", $logs_action, $emp, "[Mobile] Error saving record");
                $status = 3;
                $msg = "Error in saving $logs_action";
            }

            $this->getSupervisorManager($emp, $remarks, $this->timeStatusString($time_status), $geo_status, $dateTime, $bio, $latitude, $longitude);
            $this->log($bio, $longitude, $latitude);
            $this->saveLogs("success", $logs_action, $emp, "[Mobile] $logs_action");
            return json_encode($this->user_logs($bio, $date, $time, $status, $insertedID));
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
            return array("logs" => $all_logs, "num" => $count, "status" => $geofence, "insertId" => $id, "date" => $date.' '.$current_time);
        }

        protected function addAppAttendanceRecord(array $params){
            $resultId = 0;
            if (isset(
                $params['biometric_id'],
                $params['state'],
                $params['time'],
                $params['date'],
                $params['address'],
                $params['longitude'],
                $params['latitude'],
                $params['is_fingerprint'],
                $params['remarks'],
                $params['time_status']
            )) {
                $conn = $this->conn('gcctimeutility');
                $stmt = $conn->prepare('
                    INSERT INTO gcctimeutility.app_attendance
                    (biometric_id, state, time, date, address, longtitude, latitude, is_fingerprint, remarks, time_status)
                    VALUES
                    (:biometric_id, :state, :time, :date, :address, :longtitude, :latitude, :is_fingerprint, :remarks, :time_status)
                ');

                $stmt->bindParam(':biometric_id', $params['biometric_id']);
                $stmt->bindParam(':state', $params['state']);
                $stmt->bindParam(':time', $params['time']);
                $stmt->bindParam(':date', $params['date']);
                $stmt->bindParam(':address', $params['address']);
                $stmt->bindParam(':longtitude', $params['longitude']);
                $stmt->bindParam(':latitude', $params['latitude']);
                $stmt->bindParam(':is_fingerprint', $params['is_fingerprint']);
                $stmt->bindParam(':remarks', $params['remarks']);
                $stmt->bindParam(':time_status', $params['time_status']);
                $stmt->execute();
                $resultId = $conn->lastInsertId();
            }
            return $resultId;
        }

        protected function addAppAttendanceRecordv311(array $params){
            $resultId = 0;
            if (isset(
                $params['biometric_id'],
                $params['state'],
                $params['time'],
                $params['date'],
                $params['address'],
                $params['longitude'],
                $params['latitude'],
                $params['is_fingerprint'],
                $params['time_status'],
                $params['location_id'],
                $params['polygon'],
                $params['to_id'],
                $params['to_ref'],
            )) {
                $conn = $this->conn('gcctimeutility');
                $stmt = $conn->prepare('
                    INSERT INTO gcctimeutility.app_attendance
                    (biometric_id, state, time, date, address, longtitude, latitude, is_fingerprint, time_status,location_id, polygon, to_id, to_ref)
                    VALUES
                    (:biometric_id, :state, :time, :date, :address, :longtitude, :latitude, :is_fingerprint, :time_status, :location_id, :polygon, :to_id, :to_ref)
                ');

                $stmt->bindParam(':biometric_id', $params['biometric_id']);
                $stmt->bindParam(':state', $params['state']);
                $stmt->bindParam(':time', $params['time']);
                $stmt->bindParam(':date', $params['date']);
                $stmt->bindParam(':address', $params['address']);
                $stmt->bindParam(':longtitude', $params['longitude']);
                $stmt->bindParam(':latitude', $params['latitude']);
                $stmt->bindParam(':is_fingerprint', $params['is_fingerprint']);
                $stmt->bindParam(':time_status', $params['time_status']);
                $stmt->bindParam(':location_id', $params['location_id']);
                $stmt->bindParam(':polygon', $params['polygon']);
                $stmt->bindParam(':to_id', $params['to_id']);
                $stmt->bindParam(':to_ref', $params['to_ref']);
                $stmt->execute();
                $resultId = $conn->lastInsertId();
            }
            $this->detectPolygonsNearPin($params['biometric_id'], $resultId, $params['latitude'], $params['longitude']);
            return $resultId;
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
            return  $time ? $time['time']: NULL;
        }

        private function store_logs($post, $emp_id){
            $this->template_content = $post;
            $this->page = "timeInOut";
            $this->emp_id = $emp_id;
            return $this->responce;
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

        function getUserName($id) {
            $conn = $this->conn('gccmaster');
            $sql = "SELECT CONCAT(lastname, CASE WHEN suffix != 'N/A' AND suffix !='NONE' AND suffix !='' AND suffix IS NOT NULL THEN CONCAT(' ', suffix) ELSE ''  END, ', ',
                firstname, ' ', CASE WHEN middlename != 'N/A' AND middlename != 'NONE'
                AND middlename !='' AND middlename IS NOT NULL THEN CONCAT(SUBSTR(middlename, 1, 1), '.') ELSE '' END) employee_name
                FROM gccmaster.tblemployees WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return $data['employee_name'];
            } else {
                return '';
            }
        }

        function getEmployeeDetails($id) {
            $conn = $this->conn("gccmaster");
            $sql = "SELECT e.id AS emp_id,
                        IFNULL(e.company_phone_no, e.mobile_no) AS mobile_no,
                        u.email,
                        u.telegram_chat_id,
                        CONCAT(e.firstname, ' ', e.lastname) AS fullname,
                        e.allow_sms_notification
                    FROM gccmaster.tblemployees AS e
                    LEFT JOIN gccmaster.tblusers u ON u.emp_id = e.id
                    WHERE e.id = :id";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        private function log($biometric_id, $lon, $lat){
            
            $storage = dir."/androidapp/storage/logs/timeInOut/";
            $date = $datetime = date("Y-m-d H:i:s");

            if(!is_dir($storage)){
                mkdir($storage, 777);
            }

            $log  = "Date: {$date}\nUser: {$biometric_id}\nCoordinates: lat:{$lat},lon:{$lon}\n\n";
            file_put_contents($storage.''.$biometric_id.'.log', $log, FILE_APPEND);
        }

        function sms_settings() {
            $conn = $this->conn("gccsms");

            $sql = "SELECT modem, sms_ip, sms_port, sms_user, sms_pass, department_id, exclude
                    FROM gccsms.tblsms
                    WHERE is_connected = 1 AND sms_user = 'VOP'";

            $stmt = $conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        function sendSMS($data, $remarks, $time_status, $date_time, $fname, $geo_status) {

            $smsList = $this->sms_settings();
            $name = strtoupper($fname);
            $originalDate = $date_time;
            $date = new DateTime($originalDate);
            $timeStatus = strtoupper($time_status);
            $formatted = $date->format("Y F d, h:iA");

            if (!$smsList || !isset($smsList[0])) {
                return false;
            }

            $geo_msg = 'punch outside the assigned site location';
            if($geo_status == 2){
                $geo_msg ="using outdated GCCtime app version";
            }

            $is_allow_sms = $data['allow_sms'];
            
            if($is_allow_sms == "1"){

                $sms = $smsList[0];

                $response[] = array();

                if ($sms && $data) {
                    $phone = $data['head_no'];
                    $msg = "GCC Time Log Notification!\n\nThe user $name\nrecorded $timeStatus $geo_msg on\n$formatted.\n\nRemarks: $remarks\n\nThis is an automated message. Please do not reply.";
            
                    if (substr($phone, 0, 1) === '9') {
                        $phone = '0' . $phone;
                    }

                    $user = $sms['sms_user'];
                    $password = $sms['sms_pass'];
                    $playsms_url = "https://" . $sms['sms_ip'] . ":" . $sms['sms_port'] . "/index.php?app=ws";
                    
                    $url = '&u=' . $user;
                    $url .= '&h=' . $password;
                    $url .= '&op=pv';
                    $url .= '&smsc=' . $sms['modem'];
                    $url .= '&to=' . $phone;
                    $url .= '&msg=' . urlencode($msg);

                    $urltouse = $playsms_url . $url;

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $urltouse);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    curl_close($ch);
                }
            }
        }

        function getSupervisorManager($emp_id, $remarks, $time_status, $geo_status, $date_time, $bio_num, $latitude, $longitude) {

            if ($geo_status == 1 || $geo_status == 2) {
                $name = $this->getUserName($emp_id);
                $conn = $this->conn("gcchris");
                $sql = "SELECT e.supervisor_meta, e.tl_supervisory, d.head_id
                        FROM gccmaster.tblemployees e
                        LEFT JOIN gcchris.tbldepartments d ON d.id = e.department_id
                        WHERE e.id = :id";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $emp_id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$result) return [];

                if (empty($result['head_id']) && !empty($result['supervisor_meta'])) {
                    $supervisorMeta = @unserialize($result['supervisor_meta']);

                    if ($result['tl_supervisory'] == 1 && is_array($supervisorMeta)) {
                        if (!empty($supervisorMeta['supervisory'])) {
                            $supervisoryDetails = $this->getEmployeeDetails($supervisorMeta['supervisory']);
                            if ($supervisoryDetails) {
                                $result['head_no'] = $supervisoryDetails['mobile_no'];
                                $result['allow_sms'] = $supervisoryDetails['allow_sms_notification'];
                                $result['emp_id'] = $supervisoryDetails['emp_id'];
                                $result['head_telegram_chat_id'] = $supervisoryDetails['telegram_chat_id'];
                                $result['user_teleg_id'] = $this->get_chatId_teleg($emp_id);
                            }
                        }

                        if (!empty($supervisorMeta['managerial'])) {
                            $managerialDetails = $this->getEmployeeDetails($supervisorMeta['managerial']);
                            if ($managerialDetails) {
                                $result['head_no'] = $managerialDetails['mobile_no'];
                                $result['emp_id'] = $managerialDetails['emp_id'];
                                $result['allow_sms'] = $supervisoryDetails['allow_sms_notification'];
                                $result['head_telegram_chat_id'] = $managerialDetails['telegram_chat_id'];
                                $result['user_teleg_id'] = $this->get_chatId_teleg($emp_id);
                            }
                        }
                    }
                } else {
                    $headDetails = $this->getEmployeeDetails($result['head_id']);
                    if ($headDetails) {
                        $result['head_no'] =  $headDetails['mobile_no'];
                        $result['allow_sms'] = $headDetails['allow_sms_notification'];
                        $result['head_telegram_chat_id'] = $headDetails['telegram_chat_id'];
                        $result['emp_id'] = $headDetails['emp_id'];
                        $result['user_teleg_id'] = $this->get_chatId_teleg($emp_id);
                    }
                }
                
                // for payroll
                $data_teleg = $this->telegram_config_if_exist('gcctime_new');

                if($data_teleg['telegram_chat_id'] != null && $data_teleg['telegram_chat_id'] != $result['head_telegram_chat_id']){
                    $temp['head_telegram_chat_id'] = $data_teleg['telegram_chat_id'];
                    $this->telegram($temp, $remarks, $time_status, $date_time, $name, $bio_num, $latitude, $longitude, $geo_status);
                }
                
                $this->telegram($result, $remarks, $time_status, $date_time, $name, $bio_num, $latitude, $longitude, $geo_status);
                $this->sendSMS($result, $remarks, $time_status, $date_time, $name, $geo_status);
            }
        }

        public function telegram_config_if_exist($module) {
            $conn = $this->conn();

            $query = "SELECT telegram_bot_token, telegram_chat_id FROM gcceforms.telegram_config 
                        WHERE module = :module";
            $sth = $conn->prepare($query);
            $sth->bindParam(':module', $module, PDO::PARAM_STR);
            $sth->execute();

            $details = $sth->fetch(PDO::FETCH_ASSOC);

            return $details;

        }

        function get_chatId_teleg($id){
            $conn = $this->conn("gccmaster");
            $sql = "SELECT telegram_chat_id FROM gccmaster.tblusers WHERE emp_id = :emp_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':emp_id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['telegram_chat_id'];
        }

        public function telegram($data_res, $remarks, $time_status, $date_time, $name, $bio_num, $latitude, $longitude, $geo_status) {

                $geo_msg = ', outside the assigned site location.';

                if($geo_status == 2){
                    $geo_msg = "";
                }

                $data_teleg = $this->telegram_config_if_exist('gcctime_new');
                $telegrambot = $data_teleg['telegram_bot_token']; 
                $telegramchatid = !empty($data_res['head_telegram_chat_id']) ? $data_res['head_telegram_chat_id'] : $data_res['user_teleg_id'];

                $mapUrl = "https://www.google.com/maps?q={$latitude},{$longitude}";

                $telegram_msg = '';
                $telegram_msg  = "<b>Name</b>: " . strtoupper($name) . "\n";
                $telegram_msg .= "<b>DateTime</b>: " . strtoupper($date_time) . "\n";
                $telegram_msg .= "<b>Biometric #</b>: " . strtoupper($bio_num) . "\n";
                // $telegram_msg .= "<b>VerifyMethod</b>: " . "<b>". strtoupper($time_status)."</b>" . "$geo_msg\n";
                $telegram_msg .= "<b>VerifyMethod</b>: " . "<b>". strtoupper($time_status)."</b>\n";
                $telegram_msg .= "<b>Remarks</b>: " . strtoupper($remarks)."\n\n";
                $telegram_msg .= "<a href='$mapUrl'><b>View Location on Map</b></a>";

                $url = "https://api.telegram.org/bot$telegrambot/sendMessage";

                $data = [
                    'chat_id' => $telegramchatid,
                    'text' => $telegram_msg,
                    'parse_mode' => 'HTML'
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
        }

        function timeStatusString($time_status) {
            switch ($time_status) {
                case 'in':
                    return "Time-In";
                case 'out':
                    return "Time-Out";
                case 'overtime-in':
                    return "Overtime-In";
                case 'overtime-out':
                    return "Overtime-Out";
                default:
                    return "Unknown";
            }
        }

        private function geoaddress($long,$lat) {
            if($long != null && $lat != null){
                $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$long."&language=en-EN&sensor=false&key=".$_ENV['PROD_MAP_KEY'];
                
                $curlData=file_get_contents($url);
                $address = json_decode($curlData);
                if($address->status == "OK" && $address->results[0]->formatted_address != null){
                    return $address->results[0]->formatted_address;
                }else{
                    return "Location Undefined";
                }
            }else{
                return "N\A";
            }
        }

        function userExists($bio, $emp) {
            $conn = $this->conn("gcctimeutility");
            $sth = $conn->prepare('SELECT * FROM `app_users` 
                                    WHERE `biometric_no` = :bio AND `emp_id` = :emp 
                                    ORDER BY `emp_id` DESC LIMIT 1');
            $sth->bindParam(':bio', $bio);
            $sth->bindParam(':emp', $emp);
            $sth->execute();    
            $user = $sth->fetch(PDO::FETCH_ASSOC);
            return $user ?: false;
        }

        function getPrivilege($role_id){
            $arrData = array();

            $query = $this->get_user_role_acl($role_id);
            if ($query->rowCount() > 0) {
                
                $privilege = unserialize($query->fetch()['privilege_resource']);
                if ($privilege) {
                    foreach ($privilege as $vv) {
                        $isNode = strpos($vv, "-");
                        if ($isNode == true) {
                            $dd = explode("-", $vv);
                            if (count($dd) == 2) {
                                $aclId = $dd[0];
                                $privilegeId = $dd[1];

                                $acl = $this->get_access_control_list($aclId);
                                if ($acl->rowCount() > 0) {
                                    $rowAcl = $acl->fetch();

                                    $privilegeData = $this->get_privilege_list($privilegeId);
                                    if ($privilegeData->rowCount() == 1) {
                                        $rowPriv = $privilegeData->fetch()['name'];
                                        $privName = strtolower($rowPriv);
                                        $arrData[$rowAcl['name']][] = $privName;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $arrData;
        }

        function get_access_control_list($id){
            $conn = $this->conn();
            $sth = $conn->prepare("SELECT name from access_control_list where id='$id'");
            $sth->execute();
            return $sth;
        }

        function get_privilege_list($id){
            $conn = $this->conn();
            $sth = $conn->prepare("SELECT name from privilege_list where id='$id'");
            $sth->execute();
            return $sth;
        }

        function get_user_role_acl($role_id){
            $conn = $this->conn();
            $sth = $conn->prepare("SELECT * from user_role_acl where role_id='$role_id'");
            $sth->execute();
            return $sth;
        }

        function getModules($module,$parent_id){
            $conn = $this->conn();
            $sth = $conn->prepare("SELECT * from modules where $module parent_id='$parent_id' and is_active='1' and status='1' order by sort asc");
            $sth->execute();
            return $sth;
        }

        function getPortalModules($roleId){
            $data = array();
            $moduleId = array();
            $modules = array();
            $moduleResource = array();
            if($roleId || $roleId == "0"){
                $module = $this->getModules('','0');
                if ($module->rowCount() > 0) {
                    while ($rs = $module->fetch(PDO::FETCH_OBJ)) {
                        $moduleId[] = $rs->id;
                        $modules[] = $rs;
                    }
                }
                $userRole = $this->get_user_role_acl($roleId);
                if($userRole->rowCount() == 1){
                    $row = $userRole->fetch();
                    $moduleColumn = isset($row['module_resource']) ? unserialize($row['module_resource']) : array();
                    $moduleResource = is_array($moduleColumn)? $moduleColumn: array();
                }
            }
            $data["status"] = true;
            $data["message"] = "Modules fetched successfully.";
            $data["module_id"] = $moduleId;
            $data["modules"] = $modules;
            $data["module_resource"] = $moduleResource;
            return $data;
        }

        public function checkUserExist($emp_id, $device_name, $device_id, $app_user_id, $unique_id, $biometricno){
            $return_msg = '';
            $conn = $this->conn("gcctimeutility");
            $sth = $conn->prepare("SELECT id FROM gcctimeutility.app_users WHERE emp_id='$emp_id'");
            $sth->execute();

            if ($sth->rowCount() === 0) {
                $userApp = $conn->prepare("INSERT INTO gcctimeutility.app_users(`emp_id`, `device_name`, `device_id`, `app_user_id`, `user_imei`, `biometric_no`, `status`)
                    VALUES ('$emp_id', '$device_name', '$device_id', '$app_user_id', '$unique_id', '$biometricno', 1)");
                $userApp->execute();
                if($userApp){
                    $return_msg = 'grant_access';
                }else{
                    $msg = 'Error on the server. Please contact system administrator.';
                    $msg_logs = '[Mobile] Error on saving user credentials.';
                    $this->saveLogs("error", "sign in", $emp_id, $msg_logs);
                    $return_msg = $msg;
                }
            } else {
                if ($this->checkUserLoginToTheirDevice($unique_id, $emp_id) > 0) {
                    $return_msg = 'grant_access';
                } else {
                    $check = $this->checkUserLoginToOtherDevice($unique_id, $emp_id);
                    $row = $check->fetch(PDO::FETCH_ASSOC);
                    if ($check->rowCount() > 0) {
                        $msg = 'Account is currently sign in at '.$row['device_name'].' device id of '.$row['device_id'].', you need to sign out before sign in to other devices.';
                        $msg_logs = '[Mobile] Account is currently sign in at '.$row['device_name'].' device id of '.$row['device_id'].', User tried to sign-in in this device '.$device_name.' with device IMEI '.$unique_id.'.';
                        $this->saveLogs("error", "sign in", $emp_id, $msg_logs);
                        $return_msg = $msg;
                    } else { $return_msg = 'grant_access'; }
                }
            }

            return $return_msg;
        }

        protected function checkUserLoginToTheirDevice($unique_id, $emp_id){
            $conn = $this->conn("gcctimeutility");
            $sth = $conn->prepare("SELECT id FROM gcctimeutility.app_users WHERE user_imei='$unique_id' AND emp_id='$emp_id'");
            $sth->execute();
            return $sth->rowCount();
        }

        public function saveLogs($type, $user_action, $user_id, $log_message){
            $conn = $this->conn("gcctimeutility");
            $current_date = date("Y-m-d H:i:s");
            $ip_address = '';

            $sth = $conn->prepare("INSERT INTO gcctimeutility.app_logs_event(`type`, `user_action`, `user_id`, `log_message`, `ip_address`, `created_at`)
                VALUES ('$type','$user_action','$user_id','$log_message','$ip_address','$current_date')");
            $sth->execute();
            return $sth->rowCount() > 0;
        }

        protected function checkUserLoginToOtherDevice($unique_id, $emp_id){
            $conn = $this->conn("gcctimeutility");
            $sth = $conn->prepare("SELECT device_id, device_name FROM gcctimeutility.app_users WHERE user_imei!='$unique_id' AND emp_id='$emp_id' AND status='2'");
            $sth->execute();
            return $sth;
        }

        public function updateUserStatus($emp_id, $app_user_id, $unique_id, $device_id, $device_name){
            $conn = $this->conn("gcctimeutility");
            $log = $conn->prepare("UPDATE gcctimeutility.app_users
                SET status = 2, app_user_id = '$app_user_id', user_imei = '$unique_id', device_id = '$device_id', device_name = '$device_name'
                WHERE emp_id = '$emp_id'");
            $log->execute();
        }

        public function changeGeoKey($location){
            $geolocation = @unserialize($location);
            if($geolocation){
                foreach($geolocation as $key => $row){
                    $geolocation[$key]['latitude'] = $geolocation[$key]['lat'];
                    $geolocation[$key]['longitude'] = $geolocation[$key]['lng'];
                    unset($geolocation[$key]['lat'], $geolocation[$key]['lng']);
                }
            }
            return $geolocation;
        }

        public function get_last_timelog($bio_id){
            $result = array();
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT time, date, time_status FROM gcctimeutility.app_attendance WHERE biometric_id = :bio_id ORDER BY updated_at DESC LIMIT 1";
            $data = $conn->prepare($sql);
            $data->bindParam(":bio_id", $bio_id);
            $data->execute();
            $count = $data->rowCount();
            if($count > 0){ $result = $data->fetch(PDO::FETCH_ASSOC); }
            return $result;
        }

        function fetchUserAttendance($biometric_id, $emp_id) {

            $conn = $this->conn("gcctimeutility");
            $connzkt = $this->conn("zktime_logs");
            $array = array();
            
            $appAttendance = $conn->prepare("SELECT id, state, time_status, biometric_id, latitude, longtitude, date, time
                                    FROM gcctimeutility.app_attendance 
                                    WHERE biometric_id = :biometric_id
                                    AND MONTH(date) = MONTH(CURRENT_DATE())
                                    AND YEAR(date) = YEAR(CURRENT_DATE())");
            
            $appAttendance->bindParam(':biometric_id', $biometric_id, PDO::PARAM_STR);
            $appAttendance->execute();
        
            while ($row = $appAttendance->fetch(PDO::FETCH_ASSOC)) {
                $list = array();
                $list['is_sync'] = 'true';
                $list['emp_id'] = $emp_id;
                $list['cloud_id'] = $row['id'];
                $list['status'] = $row['time_status'];
                $list['biometric'] = $row['biometric_id'];
                $list['location'] = $row['latitude'] . ', ' . $row['longtitude'];
                $list['date'] = $row['date'] . ' ' . $row['time'];
                $list['state'] = ($row['state'] === '' || $row['state'] === '0') ? 'in' : 'out';
                $list['log_device'] = 'app';
                array_push($array, $list);
            }

            $bioAttendance = $connzkt->prepare("SELECT id, date, biometricno, verify_method
                                        FROM attendance
                                        WHERE biometricno = :biometric_id
                                        AND MONTH(date) = MONTH(CURRENT_DATE())
                                        AND YEAR(date) = YEAR(CURRENT_DATE())");
            
            $bioAttendance->bindParam(':biometric_id', $biometric_id, PDO::PARAM_STR);
            $bioAttendance->execute();
        
            while ($row = $bioAttendance->fetch(PDO::FETCH_ASSOC)) {
                $list = array();
                $list['is_sync'] = 'true';
                $list['emp_id'] = $emp_id;
                $list['cloud_id'] = $row['id'];
                $list['status'] = $this->verifyMethodChecker($row['verify_method']);
                $list['biometric'] = $row['biometricno'];
                $list['location'] = '';
                $list['date'] = $row['date'];
                $list['state'] = 'in';
                $list['log_device'] = 'bio';
                array_push($array, $list);
            }
            return $array;
        }

        function verifyMethodChecker($status){
            switch ($status) {
                case '1':
                    return 'out';
                    break;
                case '2':
                    return 'break-in';
                    break;
                case '3':
                    return 'break-out';
                    break;
                case '4':
                    return 'overtime-in';
                    break;
                case '5':
                    return 'overtime-out';
                    break;
                default:
                    return 'in';
            }
        }



        private function getPolygon($id) {
            if (empty($id)) {
                return serialize([]);
            }
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT geofence_polygon FROM gcctimeutility.app_location_sites WHERE id = :id";
            $data = $conn->prepare($sql);
            $data->bindParam(":id", $id);
            $data->execute();
            $row = $data->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['geofence_polygon'] : serialize([]);
        }

        private function allowAppUser($id) {
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT allow_app_user FROM gcctimeutility.app_users WHERE emp_id = :emp_id";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':emp_id', $id);
            $sth->execute();
            $data = $sth->fetch(PDO::FETCH_ASSOC);
        
            if (!$data) {
                return false;
            }
        
            if (isset($data['allow_app_user']) && $data['allow_app_user'] == 0) {
                return false;
            }
        
            return true;
        }

        private function userExistsApp($emp) {
            $conn = $this->conn("gcctimeutility");
            $sth = $conn->prepare('SELECT emp_id FROM app_users WHERE emp_id = :emp LIMIT 1');
            $sth->bindParam(':emp', $emp);
            $sth->execute();
            return $sth->fetchColumn() ?: false;
        }

        public function invalidActLogv311(){
            $emp = isset($_POST['emp_id']) ? $_POST['emp_id'] : '';
            $status = isset($_POST['status']) ? $_POST['status'] : '';
            $msg = isset($_POST['msg']) ? $_POST['msg'] : '';
            $token  = isset($_POST['token']) ? $_POST['token'] : '';
        
            $validate_token = $this->checkToken($emp, $token);
        
            if (empty($emp) || empty($status) || empty($msg) || !$validate_token) {
                return json_encode([
                    "status" => false,
                    "msg" => empty($emp) || empty($status) || empty($msg)
                        ? "Missing or empty field(s)"
                        : "Invalid token to log."
                ]);
            }
        
            if ($this->saveLogs("error", $status, $emp, "[Mobile] $msg")) {
                return json_encode(["status" => true, "msg" => "Successfully logged"]);
            } else {
                return json_encode(["status" => false, "msg" => "Failed to log"]);
            }
        }


        public function offlineInvalidActLogv311(){
            $logs = isset($_POST['logs']) ? $_POST['logs'] : '';
            $emp_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : '';
            $token = isset($_POST['token']) ? $_POST['token'] : '';
            $validate_token = $this->checkToken($emp_id, $token);
            $status = false;
            $proceed = true;
            $msg = "Invalid log data.";
            
            if(empty($logs)){
                $proceed = false;
                $msg = "Missing data in logs parameter";
            }

            if(empty($token)){
                $proceed = false;
                $msg = "No token found.";
            }
            if(empty($emp_id)){
                $proceed = false;
                $msg = "No employee ID parameter found.";
            }

            $userExist = $this->userExistsApp($emp_id);
            if(!$userExist){
                $proceed = false;
                $msg = "User not found.";
            }

            if (!$validate_token) {
                $this->saveLogs("error", "sign out", 0, "[Mobile] User sign out failed - missing emp_id");
                $msg = "Invalid token.";
                $proceed = false;
            }

            if($proceed){
                $status = true;
                $decoded = json_decode(urldecode($logs), true);
                if (isset($decoded['logs']) && is_array($decoded['logs'])) {
                    foreach ($decoded['logs'] as $log) {
                        $emp = $log['emp_id'] ?? 'unknown';
                        $status = $log['status'] ?? 'unknown';
                        $msg = $log['msg'] ?? 'no message';
                        $this->saveLogs("error", $status, $emp, "[Mobile] $msg");
                    }
                    $msg = "Successfully logged";
                    $status = true;
                } else {
                    $msg = "Invalid logs format";
                    $status = false;
                }
                return json_encode(["status" => $status, "msg" => $msg]);
            }

            return json_encode(["status" => $status, "msg" => $msg]);
            
        }


        public function timeLogOfflinev311() {

            $emp_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : null;
            $token = $_POST['token'] ?? null;
            $rawInput = $_POST['logs'];
            $msg = "";
            $response['data'] = [];
            $proceed = true;
            $decoded = json_decode(urldecode($rawInput), true);

            $validate_token = $this->checkToken($emp_id, $token);
            if (!$validate_token) {
                $proceed = false;
                $msg = "Invalid token to sync attendance.";
            }

            $userExist = $this->userExistsApp($emp_id);
            if(!$userExist){
                $proceed = false;
                $msg = "Parameters does not match, user not found.";
            }

            $isAllowed = $this->allowAppUser($emp_id);
            if (!$isAllowed) {
                $proceed = false;
                $msg = "You are not eligible to use the app. Please contact your department head for access.";
            }
            if ($decoded === null) {
                $proceed = false;
                $msg = "Invalid JSON format." . json_last_error_msg();
            }
            
        
            if (!isset($decoded['logs']) || !is_array($decoded['logs'])) {
                $proceed = false;
                $msg = "Invalid or missing logs array";
            }

            if($proceed){
                $logs = $decoded['logs'];
                $conn = $this->conn("gcctimeutility");
                $successCount = 0;
                $failedCount = 0;
            
                foreach ($logs as $log) {
                    $list = [];
                    $resultId = 0;
                    $location = json_decode($log['location'], true);
                    $longitude = $location['longitude'];
                    $latitude = $location['latitude'];
                    $site_id = isset($log['location_id']) ? $log['location_id'] : '0';
                    $polygon = $this->getPolygon($site_id);
                    $emp_id = $log['emp_id'];
                    $bio_num = $log['biometric'];
                    $date_time = explode(" ", $log['date']);
                    $date = $date_time[0];
                    $time = $date_time[1];
                    $time_status = $log['status'];
                    $stats = 1;
                    $address = $this->geoaddress($longitude, $latitude);

                    $geo_status = (isset($log['state']) && $log['state'] === "in") ? 0 : 1;
                    $timeStatus = $this->timeStatusString($time_status);
                    if (!isset($log['state']) || ($log['state'] !== "in" && $log['state'] !== "out")) {
                        $remarks_state = "The GCCtime app version in use is outdated.";
                    }

                    $remarks = isset($log['remarks']) ? $log['remarks'] : $remarks_state;

                    $date_time = $date . " " . $time;
            
                    if ($latitude !== null && $longitude !== null) {

                        $checkSql = "SELECT COUNT(*) FROM gcctimeutility.app_attendance
                                    WHERE biometric_id = :bio AND date = :date AND time = :time AND state = :state";
                        $checkStmt = $conn->prepare($checkSql);
                        $checkStmt->bindParam(':bio', $bio_num);
                        $checkStmt->bindParam(':date', $date);
                        $checkStmt->bindParam(':time', $time);
                        $checkStmt->bindParam(':state', $geo_status);
                        $checkStmt->execute();
                        $exists = $checkStmt->fetchColumn();

                        if ($exists > 0) {
                            $list['status'] = false;
                            $list['msg'] = 'Duplicate entry';
                            $failedCount++;
                        } else {

                            $sql = "INSERT INTO gcctimeutility.app_attendance
                                    (biometric_id, state, time, date, address, longtitude, latitude, remarks, time_status, is_fingerprint, location_id, polygon)
                                VALUES (:bio, :state, :time, :date, :address, :lon, :lat,:remarks, :time_status, :is_finger, :location_id, :poly)";
                            $data = $conn->prepare($sql);
                            $data->bindParam(':bio', $bio_num);
                            $data->bindParam(':state', $geo_status);
                            $data->bindParam(':time', $time);
                            $data->bindParam(':date', $date);
                            $data->bindParam(':address', $address);
                            $data->bindParam(':lon', $longitude);
                            $data->bindParam(':lat', $latitude);
                            $data->bindParam(':remarks', $remarks);
                            $data->bindParam(':time_status', $time_status);
                            $data->bindParam(':is_finger', $stats);
                            $data->bindParam(':location_id', $site_id);
                            $data->bindParam(':poly', $polygon);
                
                            if ($data->execute()) {
                                $list['status'] = true;
                                $list['msg'] = 'Success';
                                $successCount++;
                                $logs_action = ($time_status === 'in' || $time_status === 'out') ? "time $time_status" : $time_status;
                                $this->saveLogs("success", $logs_action, $emp_id, "[Mobile] Attendance - user $logs_action thru offline to online sync.");
                                $this->log($bio_num, $longitude, $latitude);
                                $this->getSupervisorManager($emp_id, $remarks, $timeStatus, $geo_status, $date_time, $bio_num, $latitude, $longitude);
                            } else {
                                $list['status'] = false;
                                $failedCount++;
                            }
                            $resultId = $conn->lastInsertId();
                            $this->detectPolygonsNearPin($bio_num, $resultId, $latitude, $longitude);

                        }
                    } else {
                        $list['status'] = false;
                        $failedCount++;
                    }
                    array_push($response['data'], $list);
                }
                $response['summary'] = ['success' => $successCount,'failed' => $failedCount];
            }else{
                array_push($response['data'], ["status" => false, "msg" => $msg]);
            }

            return json_encode($response);
        }


        private function getToken($emp_id) {
            $timestamp = date('YmdHis');
            $token = md5($emp_id . $timestamp);
        
            $conn = $this->conn("gccmaster");
            $sql = "UPDATE gccmaster.tblusers 
                    SET mobile_token = :token
                    WHERE emp_id = :emp_id";
        
            $sth = $conn->prepare($sql);
            $sth->bindParam(':token', $token);
            $sth->bindParam(':emp_id', $emp_id);
        
            if ($sth->execute()) {
                return $token;
            }
        
            return false;
        }


        private function checkToken($emp_id, $token) {
            $conn = $this->conn("gccmaster");
            $sql = "SELECT mobile_token FROM gccmaster.tblusers WHERE emp_id = :emp_id";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':emp_id', $emp_id);
            $sth->execute();
            $data = $sth->fetch(PDO::FETCH_ASSOC);
        
            return ($data && $data['mobile_token'] === $token) ? true : false;
        }

        private function checkEmployeeId($emp_id) {
            $conn = $this->conn("gccmaster");
            $sql = "SELECT emp_id FROM gccmaster.tblusers WHERE emp_id = :emp_id";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':emp_id', $emp_id);
            $sth->execute();
            return ($sth->rowCount() > 0) ? true : false;
        }

        private function checkEmployeeLock($emp_id) {
            $conn = $this->conn("gccmaster");
            $sql = "SELECT lockout FROM gccmaster.tblusers WHERE emp_id = :emp_id";
            $sth = $conn->prepare($sql);
            $sth->bindParam(':emp_id', $emp_id);
            $sth->execute();
            $data = $sth->fetch(PDO::FETCH_ASSOC);
            if ($data && $data['lockout'] == 0) {
                return false;
            }
            return true;
        }

        

        public function timeLogv311() {
            $date = date("Y-m-d");
            $time = date("H:i:s");
            $dateTime = "$date $time";
            $logs_action = 'time log';
            $msg = "";
            $status = 2;
            $token = isset($_POST['token']) ? $_POST['token'] : '';
            $required = ['biometric_id', 'emp_id', 'coords'];
            $missing = array_filter($required, fn($f) => empty($_POST[$f]));
            if ($missing) {
                $status = 0;
                $msg = "Missing or empty field(s): " . implode(", ", $missing);
            }
        
            $bio = $_POST['biometric_id'];
            $emp = $_POST['emp_id'];

            $tokenStatus = $this->checkToken($emp, $token);


            $time_status = isset($_POST['time_status']) ? $_POST['time_status'] : '';
            $site_id = isset($_POST['location_id']) ? $_POST['location_id'] : 0;
            $polygon = $this->getPolygon($site_id);
            if (empty($time_status)) {
                $status = 0;
                $msg = "Missing or empty field(s): time status.";
            }

            $coords = json_decode($_POST['coords'], true);
        
            if (!isset($coords['latitude'], $coords['longitude'])) {
                $this->saveLogs("error", 'time '.$time_status, $emp, "[Mobile] No coordinates");
                $status = 0;
                $msg = "Coordinates not found.";
            }
        
            if (isset($_POST['state'])) {
                if ($_POST['state'] === "in") {
                    $geo_status = 0;
                } elseif ($_POST['state'] === "out") {
                    $geo_status = 1;
                } else {
                    $geo_status = 2;
                }
            } else {
                $geo_status = 2;
            }

            $isAllowed = $this->allowAppUser($emp);
        
            $latitude = $coords['latitude']?? '';
            $longitude = $coords['longitude']?? '';
            $personnel_id = $this->personnel_id($bio);
            $sites_id = $this->sites_location_id($personnel_id);
            $interval = $this->setInterval($bio);
            $max_time = date('H:i:s', strtotime("$interval +1 minute"));
        
            if (!$sites_id) {
                $this->saveLogs("error", 'time '.$time_status, $emp, "[Mobile] No site location");
            }
        
            if ($interval && $max_time >= $time) {
                $status = 4;
            }
            
            $has_travel_order = $this->getEmployeeTO($emp, $date);

            $currentDateTime = strtotime("$date $time");
            $isWithinTravelOrder = false;
            $to_ref = '';
            $to_id = 0;
            
            foreach ($has_travel_order as $order) {
                $dateFrom = strtotime($order['date_from'] . ' -30 minutes');
                $dateTo   = strtotime($order['date_to']   . ' +30 minutes');
            
                if ($currentDateTime >= $dateFrom && $currentDateTime <= $dateTo) {
                    $isWithinTravelOrder = true;
                    $to_ref = $order['reference_no'];
                    $to_id  = $order['id'];
                    break;
                }
            }

            if (!$isWithinTravelOrder && ($geo_status === 1 || $geo_status === 2)) {
                $status = 5;
            }

            if ($status === 0 || !$isAllowed || $status === 4 || !$tokenStatus || $status === 5) {
                if ($status === 4) {
                    $msg = "Re-logged within 1-minute interval";
                }
                if (!$isAllowed) {
                    $status = 3;
                    $msg = "You are not eligible to use the app. Please contact your department head for access.";
                }
                if (!$tokenStatus) {
                    $status = 3;
                    $msg = "Invalid token to proceed time log request.";
                }
                if($status === 5 && !empty($has_travel_order)){
                    $msg = "Your travel order is not valid at this time.\nYou may only log outside the assigned site location within the approved Travel Order date and time.\n\n30 minutes before the start (date and time) and 30 minutes after the end (date and time) of the travel order.";
                }
                if($status === 5 && empty($has_travel_order)){
                    $msg = "You don't have privilege to punch outside the assigned site location.";
                }

                $this->saveLogs("error", 'time '.$time_status, $emp, "[Mobile] $msg");
                return json_encode(["status" => $status, "msg" => $msg]);
            }
            $this->store_logs($_POST, $personnel_id);
            $data = [
                'biometric_id' => $bio,
                'state' => $geo_status,
                'time' => $time,
                'date' => $date,
                'address' => $this->geoaddress($longitude, $latitude),
                'longitude' => $longitude,
                'latitude' => $latitude,
                'is_fingerprint' => 1,
                'time_status' => $time_status,
                'location_id' => $site_id,
                'polygon' => $polygon,
                'to_id' => $to_id,
                'to_ref' => $to_ref,
            ];

            $insertedID = $this->addAppAttendanceRecordv311($data);
        
            if (!$insertedID) {
                $this->saveLogs("error", 'time '.$time_status, $emp, "[Mobile] Error saving record");
                $status = 3;
                $msg = "Error in saving 'time '.$time_status";
            }else{
                $isWithinTravelOrder ? $msg = "has been successfully saved.\n\nTravel Order Ref #: $to_ref " : $msg = "has been successfully saved.";
            }

            $this->getSupervisorManager($emp, "Outside Assigned Site Location", $this->timeStatusString($time_status), $geo_status, $dateTime, $bio, $latitude, $longitude);
            $this->log($bio, $longitude, $latitude);
            $this->saveLogs("success", 'time '.$time_status, $emp, "[Mobile] $logs_action");
            return json_encode($this->user_logsv311($bio, $date, $time, $status, $insertedID, $msg));

        }


        
        private function user_logsv311($bio_id, $current_date = "", $current_time = "", $geofence = 0, $id = 0, $msg){
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
            return array("logs" => $all_logs, "num" => $count,"msg" => $msg, "status" => $geofence, "insertId" => $id, "date" => $date.' '.$current_time);
        }





        public function getGeofencev311() {
            $status = true;
            $msg = "Success";
            $token = isset($_POST['token']) ? $_POST['token'] : '';
            $emp_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : '';
            $bio = isset($_POST['biometricno']) ? $_POST['biometricno'] : '';
            $proceed = true;
            $isSuspended = $this->isSuspended($emp_id, $token);

            if (empty($token) || empty($emp_id) || empty($bio)) {
                $status = false;
                $msg = "Missing or empty field(s)";
                $proceed = false;
            }

            $isAllowed = $this->allowAppUser($emp_id);
            if (!$isAllowed) {
                $status = false;
                $msg = "You are not eligible to use the app. Please contact your department head for access.";
                $proceed = false;
            }

            if (!$this->checkToken($emp_id, $token)) {
                $status = false;
                $msg = "Invalid token.";
                $proceed = false;
            }

            if (!$this->checkEmployeeId($emp_id)) {
                $status = false;
                $msg = "Invalid employee ID.";
                $proceed = false;
            }

            if ($isSuspended) {
                $status = false;
                $msg = "Your Account is suspended.";
                $proceed = false;
            }


            
        if($proceed){
            $bio = $_POST['biometricno'];
            $conn = $this->conn("gcctimeutility");
        
            $sql = "SELECT c.id, c.site_name, c.geofence_polygon, c.latitude, c.longtitude 
                    FROM gcctimeutility.personnel a
                    LEFT JOIN gcctimeutility.personnel_locations b ON b.personnel_id = a.id
                    LEFT JOIN gcctimeutility.app_location_sites c ON c.id = b.site_location_id
                    WHERE a.biometric_id = :bio OR a.biometricno = :bio";
        
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':bio', $bio);
            $stmt->execute();
        
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$rows) {
                $status = false;
                $msg = "No assigned Location";
            }
        
            $resultset = array_filter(array_map(function ($row) {
                if (empty($row['geofence_polygon'])){return null;}
                return [
                    "data" => [
                        "id" => $row["id"],
                        "site_name" => $row["site_name"],
                        "latitude" => $row["latitude"],
                        "longtitude" => $row["longtitude"],
                    ],
                    "geofence_polygon" => $this->changeGeoKey($row["geofence_polygon"])
                ];
            }, $rows));
        
            if (!$resultset) {
                $msg = "No assigned location";
                $status = false;
            }

            return json_encode(["message" => $msg, "sitelocation" => array_values($resultset), "status" => $status]);

        }

            return json_encode(["message" => $msg, "status" => $status]);

        }



        public function gcctimeLoginv311() {
            $r = $_POST;
            $required = ['username', 'password', 'unique_id', 'device_id', 'device_name'];
            $msg = "Something went wrong. Please ask the administrator for assistance.";
            $proceed = true;
            $status = false;
            
            foreach ($required as $key) {
                if (empty($r[$key])) {
                    return json_encode(["status" => $status, "msg" => ucfirst(str_replace("_", " ", $key)) . " is missing."]);
                }
            }

            $conn = $this->conn("gccmaster");
            $username = $r['username'];
            $password = md5($r['password']);
            $unique_id = $r['unique_id'];
            $device_id = $r['device_id'];
            $device_name = str_replace(" ", "_", $r['device_name']);
        
            $sql = "SELECT a.biometricno, a.id, a.firstname, b.username, b.emp_id, b.is_suspended,
                        a.idno, c.name AS position_name, a.pic_filename, a.lastname,a.company_id, a.department_id, b.reset_pin,
                        IFNULL(d.code, 'No Company') AS company,
                        IFNULL(e.code, 'No Department') AS department, a.level
                    FROM tblemployees a
                    LEFT JOIN tblusers b ON a.id = b.emp_id
                    LEFT JOIN gcchris.tblposition c ON c.id = a.position OR c.name = a.position
                    LEFT JOIN gcchris.tblcompanies d ON d.id = a.company_id
                    LEFT JOIN gcchris.tbldepartments e ON e.id = a.department_id OR e.description = a.department_id
                    WHERE (a.biometricno = :usern OR b.username = :usern) AND b.password = :passw";
        
            $sth = $conn->prepare($sql);
            $sth->execute([':usern' => $username, ':passw' => $password]);
            $emp_data = $sth->fetch(PDO::FETCH_ASSOC);

            if (!$emp_data) {
                $msg = "Username or password is incorrect.";
                $proceed = false;
            }else{
                $assignedLocation = $this->checkAssignedLocation($emp_data['biometricno']);
                $lockoutUser = $this->checkEmployeeLock($emp_data['id']);
                if($proceed){
                    $app_user_id = md5($unique_id . $emp_data['id']);
                    $token = (string) $this->getToken($emp_data['id']);
                    $isSuspended = $this->isSuspended($emp_data['id'], $token);
                    if ($isSuspended) {
                        $msg = "Your Account is suspended.";
                        $proceed = false;
                    }
                    if (!$assignedLocation){
                        $msg = "No assigned Location.\nPlease contact HR for assistance.";
                        if(empty($emp_data['biometricno'])){
                            $msg = "No Biometric Number.\nPlease contact HR for assistance.";
                        }
                        $proceed = false;
                    }
                    if ($lockoutUser){
                        $msg = "This user account is locked.\nPlease contact HR or IT for assistance.";
                        $proceed = false;
                    }
                    if($proceed){
                        $check = $this->checkUserExist($emp_data['id'], $device_name, $device_id, $app_user_id, $unique_id, $emp_data['biometricno']);
                        $isAllowed = $this->allowAppUser($emp_data['id']);
                        if (!$isAllowed) {
                            $msg = "You are not eligible to use the app. Please contact your department head for access.";
                            return json_encode(["status" => false, "msg" => $msg]);
                        }
                        if ($check === 'grant_access') {
                            $this->saveLogs("success", "sign in", $emp_data['id'], "[Mobile] User sign in");
                            $this->updateUserStatus($emp_data['id'], $app_user_id, $unique_id, $device_id, $device_name);
                            if($proceed){
                                return json_encode([
                                    "status" => true,
                                    "msg" => "Success",
                                    "user_data" => [
                                        "emp_id" => $emp_data['id'],
                                        "app_user_id" => $app_user_id,
                                        "biometric_id" => $emp_data['biometricno'],
                                        "firstname" => $emp_data['firstname'],
                                        "lastname" => $emp_data['lastname'],
                                        "idno" => $emp_data['idno'],
                                        "position_name" => $emp_data['position_name'],
                                        "pic_filename" => $emp_data['pic_filename'],
                                        "lvl_ranking" => $emp_data['level'],
                                        "device_name" => $device_name,
                                        "device_id" => $device_id,
                                        "company_id" => $emp_data['company_id'],
                                        "department_id" => $emp_data['department_id'],
                                        "unique_id" => $unique_id,
                                        "company" => $emp_data['company'],
                                        "department" => $emp_data['department'],
                                        "is_suspended" => $emp_data['is_suspended'],
                                        "token" => $token,
                                        "is_allowed_app_user" => $isAllowed,
                                        "pin" => $emp_data['reset_pin']
                                    ]
                                ]);
                            }
                        }
                        $msg = $check;
                    }
                }
            }
            return json_encode(["status" => false, "msg" => $msg]);
        }


        public function isSuspended($id, $token) {
            $status = false;
            $proceed = true;

            if (!$token || !$id || (!$this->checkToken($id, $token))) {
                $proceed = false;
            }

            if($proceed){
                $stmt = $this->conn("gccmaster")->prepare(
                    "SELECT is_suspended FROM gccmaster.tblusers WHERE emp_id = :emp_id"
                );
                $stmt->execute([':emp_id' => $id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($data['is_suspended'] == 1) {
                    $status = true;
                }
            }
            return $status;
        }

        private function locationId($id) {
            $stmt = $this->conn("gcctimeutility")->prepare(
                "SELECT site_name 
                FROM gcctimeutility.app_location_sites 
                WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
            return $data ? $data['site_name'] : "No Location Found";
        }
        

        public function fetchAttendancev311() {

            $emp_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : '';
            $biometric_id = isset($_POST['biometric_id']) ? $_POST['biometric_id'] : '';
            $token = isset($_POST['token']) ? $_POST['token'] : '';
            
            $validate_token = $this->checkToken($emp_id, $token);

            if(!$this->userExists($biometric_id, $emp_id) || !$validate_token){
                return json_encode([
                    "status" => false,
                    "msg" => !$validate_token ? "Invalid token." : "Parameters does not match, user not found."
                ]);
            }

            $conn = $this->conn("gcctimeutility");
            $connzkt = $this->conn("zktime_logs");
            $array = array();
            
            $appAttendance = $conn->prepare("SELECT id, state, time_status, biometric_id, latitude, longtitude, date, time, location_id, to_ref, polygon
                                    FROM gcctimeutility.app_attendance
                                    WHERE biometric_id = :biometric_id AND date >= CURDATE() - INTERVAL 30 DAY");
            
            $appAttendance->bindParam(':biometric_id', $biometric_id, PDO::PARAM_STR);
            $appAttendance->execute();
        
            while ($row = $appAttendance->fetch(PDO::FETCH_ASSOC)) {
                $list = array();
                $list['is_sync'] = 'true';
                $list['emp_id'] = $emp_id;
                $list['cloud_id'] = $row['id'];
                $list['status'] = $row['time_status'];
                $list['biometric'] = $row['biometric_id'];
                $list['location'] = $row['latitude'] . ', ' . $row['longtitude'];
                $list['date'] = $row['date'] . ' ' . $row['time'];
                $list['state'] = ($row['state'] === '' || $row['state'] === '0') ? 'in' : 'out';
                $list['log_device'] = 'app';
                $list['site_name'] = $this->locationId($row['location_id']);
                $list['to_ref'] = $row['to_ref'];
                $list['polygon'] = unserialize($row['polygon']) ? unserialize($row['polygon']) : '';
                array_push($array, $list);
            }

            $bioAttendance = $connzkt->prepare("SELECT id, date, biometricno, verify_method
                                        FROM attendance
                                        WHERE biometricno = :biometric_id AND date >= CURDATE() - INTERVAL 30 DAY");
            
            $bioAttendance->bindParam(':biometric_id', $biometric_id, PDO::PARAM_STR);
            $bioAttendance->execute();
        
            while ($row = $bioAttendance->fetch(PDO::FETCH_ASSOC)) {
                $list = array();
                $list['is_sync'] = 'true';
                $list['emp_id'] = $emp_id;
                $list['cloud_id'] = $row['id'];
                $list['status'] = $this->verifyMethodChecker($row['verify_method']);
                $list['biometric'] = $row['biometricno'];
                $list['location'] = '';
                $list['date'] = $row['date'];
                $list['state'] = 'in';
                $list['log_device'] = 'bio';
                $list['site_name'] = '';
                $list['to_ref'] = '';
                $list['polygon'] = '';
                array_push($array, $list);
            }
            return json_encode($array);
        }


        public function appVersionv311() {
            $status = true;
            $msg = "";
            $proceed = true;
            $appVersion = "";
            $appUrl = "";

            $token = $_POST['token'] ?? null;

            $emp_id = $_POST['emp_id'] ?? null;
            $validate_token = $this->checkToken($emp_id, $token);
            $isAllowed = $this->allowAppUser($emp_id);

            if (!isset($_POST['app_version']) || !isset($_POST['app_name'])) {
                $status = false;
                $msg = "Missing required parameters";
                $proceed = false;
            }

            if (!$validate_token) {
                $status = false;
                $msg = "Invalid Token to proceed the request.";
                $proceed = false;
            }
            
            if(!$validate_token && $isAllowed){
                $status = true;
                $proceed = true;
            }

            if (!$isAllowed) {
                $proceed = false;
                $msg = "You are not eligible to use the app. Please contact your department head for access.";
            }

            if ($proceed) {
                $appversion = $_POST['app_version'];
                $appname = $_POST['app_name'];

                $conn = $this->conn("gcctimeutility");
                
                $sql = "SELECT * FROM gcctimeutility.app_version 
                        WHERE app_name = :appname AND app_version = :appversion
                        ORDER BY released_dt DESC 
                        LIMIT 1";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':appname', $appname, PDO::PARAM_STR);
                $stmt->bindParam(':appversion', $appversion, PDO::PARAM_STR);
                $stmt->execute();
                
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                

                if ($row) {
                    if ($row['app_version'] == $appversion && $row['is_latest'] == 1) {
                        $status = true;
                        $msg = "GCCTIME Application is up to date";
                        $appVersion = $row['app_version'];
                        $appUrl = $row['app_url'];
                    } else {
                        $status = false;
                        $msg = "GCCTIME Application is outdated. Please update to the latest version.";
                        $appVersion = $row['app_version'];
                        $appUrl = $row['app_url'];
                    }
                } else {
                    $appVersion = "1.0.0";
                    $status = false;
                    $msg = "GCCTIME Application is outdated, Please update your GCCTIME Application to the latest version for better performance and features.";
                }
            }
                
            return json_encode(["status" => $status, "msg" => $msg, "app_version" => $appVersion, "app_url" => $appUrl]);

        }

        /*** ------------------------------------------100 meter radius polygon near pin ------------------------------------------------***/
        // Haversine formula to compute distance in meters
        function haversineDistance($lat1, $lng1, $lat2, $lng2) {
            $earthRadius = 6371000; // meters
            $dLat = deg2rad($lat2 - $lat1);
            $dLng = deg2rad($lng2 - $lng1);

            $a = sin($dLat / 2) * sin($dLat / 2) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                sin($dLng / 2) * sin($dLng / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            return $earthRadius * $c;
        }

        // Ray casting algorithm to check if point is inside polygon
        function pointInPolygon($point, $polygon) {
            $x = $point['lng'];
            $y = $point['lat'];
            $inside = false;
            $n = count($polygon);

            for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
                $xi = $polygon[$i]['lng']; $yi = $polygon[$i]['lat'];
                $xj = $polygon[$j]['lng']; $yj = $polygon[$j]['lat'];

                $intersect = (($yi > $y) != ($yj > $y)) &&
                            ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi + 0.0) + $xi);

                if ($intersect){ $inside = !$inside; }
            }

            return $inside;
        }

            public function detectPolygonsNearPin($id, $attId, float $lat, float $lng) {
                $radius = 100;
                $pin = ["lat" => $lat, "lng" => $lng];
            
                $pol1 = $this->getSiteLocationv311($id);
                if (!$pol1) {
                    return false;
                }
            
                $polygons = [];
                foreach ($pol1 as $poly) {
                    $polygonData = @unserialize($poly['geofence_polygon']);
                    if ($polygonData && is_array($polygonData)) {
                        $polygons[] = $polygonData;
                    }
                }
            
                $detected = [];
                foreach ($polygons as $polygon) {
                    $isWithin = false;
            
                    if ($this->pointInPolygon($pin, $polygon)) {
                        $isWithin = true;
                    } else {
                        foreach ($polygon as $vertex) {
                            $distance = $this->haversineDistance(
                                floatval($pin['lat']), floatval($pin['lng']),
                                floatval($vertex['lat']), floatval($vertex['lng'])
                            );
                            if ($distance <= $radius) {
                                $isWithin = true;
                                break;
                            }
                        }
                    }
            
                    if ($isWithin) {
                        $detected[] = $polygon;
                    }
                }
            
                if (!empty($detected)) {
                    $this->insertAttGeoLog(serialize($detected), $attId);
                }
            
                return $detected;
            }
            
            
            

        function getSiteLocationv311($bio) {
            
            $conn = $this->conn("gcctimeutility");
        
            $sql = "SELECT c.geofence_polygon
                    FROM gcctimeutility.personnel a
                    LEFT JOIN gcctimeutility.personnel_locations b ON b.personnel_id = a.id
                    LEFT JOIN gcctimeutility.app_location_sites c ON c.id = b.site_location_id
                    WHERE a.biometric_id = :bio OR a.biometricno = :bio";
        
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':bio', $bio);
            $stmt->execute();
        
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) {
                return false;
            }

            return $rows;

        }

        function insertAttGeoLog($serialized, $attId) {
            $conn = $this->conn('gcctimeutility');
            $stmt = $conn->prepare('
                INSERT INTO gcctimeutility.attendance_geofence_log
                (attendance_id, geofence)
                VALUES
                (:attendance_id, :geofence)
            ');
            $stmt->bindParam(':attendance_id', $attId);
            $stmt->bindParam(':geofence', $serialized);
            $stmt->execute();
        }
        

        

        private function checkAssignedLocation($bio) {

            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT c.id, c.site_name, c.geofence_polygon, c.latitude, c.longtitude 
                    FROM gcctimeutility.personnel a
                    LEFT JOIN gcctimeutility.personnel_locations b ON b.personnel_id = a.id
                    LEFT JOIN gcctimeutility.app_location_sites c ON c.id = b.site_location_id
                    WHERE a.biometric_id = :bio OR a.biometricno = :bio";
        
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':bio', $bio);
            $stmt->execute();
        
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$rows) {
                $status = false;
                $msg = "No assigned Location";
            }
        
            $resultset = array_filter(array_map(function ($row) {
                if (empty($row['geofence_polygon'])){return null;}
                return [
                    "data" => [
                        "id" => $row["id"],
                        "site_name" => $row["site_name"],
                        "latitude" => $row["latitude"],
                        "longtitude" => $row["longtitude"],
                    ],
                    "geofence_polygon" => $this->changeGeoKey($row["geofence_polygon"])
                ];
            }, $rows));
        
            if (!$resultset) {
                return false;
            }

            return true;

        }

        // Travel order

    private function getEmployeeTO($employeeId = null, $date=null){
        $resultset = array();
        if($employeeId && $date){
        $arrTravelId = $this->getTravelOrderDriverById($employeeId);
        $arrPersonnelId = $this->getTravelOrderPersonnelById($employeeId);
        $travelIds = array_merge($arrTravelId, $arrPersonnelId);
        if(is_array($travelIds) && !empty($travelIds)){
            $travelIds = array_unique($travelIds);
            $tempDate = date('Y-m-d', strtotime($date));
            $destinations = $this->getTravelOrderDestination($travelIds, $tempDate);
            if(is_array($destinations) && !empty($destinations)){
            $ids = array_column($destinations, 'id');
            $uniqueIds = array_unique($ids);
            $cData = array_intersect_key($destinations, $uniqueIds);
            $resultset = array_values($cData);
            }
        }
        }
        return $resultset;
    }

        
    public function getEmployeeTravelOrder(){
        $employeeId = isset($_POST['emp_id']) ? $_POST['emp_id'] : null;
        $token = isset($_POST['token']) ? $_POST['token'] : null;
        $date = isset($_POST['date']) ? $_POST['date'] : null;
        $validate_token = $this->checkToken($employeeId, $token);

        if (!$validate_token) {
            $this->saveLogs("error", "travel", $employeeId, "[Mobile] missing token");
            $msg = "Invalid token to get travel order.";
            return json_encode(['status' => false, 'msg'=> $msg]);
        }

        $resultset = array();
        if($employeeId && $date){
        $arrTravelId = $this->getTravelOrderDriverById($employeeId);
        $arrPersonnelId = $this->getTravelOrderPersonnelById($employeeId);
        $travelIds = array_merge($arrTravelId, $arrPersonnelId);
            if(is_array($travelIds) && !empty($travelIds)){
                $travelIds = array_unique($travelIds);
                $tempDate = date('Y-m-d', strtotime($date));
                $destinations = $this->getTravelOrderDestination($travelIds, $tempDate);
                if(is_array($destinations) && !empty($destinations)){
                $ids = array_column($destinations, 'id');
                $uniqueIds = array_unique($ids);
                $cData = array_intersect_key($destinations, $uniqueIds);
                $resultset = array_values($cData);
                }
            }
        }
        return json_encode($resultset);
    }

    protected function getTravelOrderDestination($travelIds = array(), $tempDate = null){
        $arrData = array();
        if(is_array($travelIds) && !empty($travelIds) && $tempDate){
        $whereIn = implode(',', $travelIds);
        $conn = $this->conn();
        $sth = $conn->prepare("SELECT t.id, t.reference_no, t.created_dt, td.date_from, td.date_to, t.company, t.department, td.purpose, td.destination
        FROM gcceforms.travel_destination as td
        INNER JOIN gcceforms.travel_order as t ON td.travel_order_id = t.id
        WHERE t.id IN (:id) OR (DATE(td.date_from) <= :_date AND DATE(td.date_to) >= :_date)
        OR (DATE(td.date_from) >= :_date AND DATE(td.date_to) <= :_date)
        AND t.status = 'Approved'");
        $sth->bindParam(':id', $whereIn, PDO::PARAM_INT);
        $sth->bindParam(':_date', $tempDate, PDO::PARAM_STR);
        $sth->execute();
        if($sth->rowCount() > 0){
            foreach($sth->fetchAll(PDO::FETCH_ASSOC) as $row ){
            if($this->insertTravelData($row, $travelIds, $tempDate)){ $arrData[] = $row; }
            }
        }
        }
        return $arrData;
    }

    protected function insertTravelData($row = array(), $travelIds = array(), $tempDate = null){
        if(in_array($row['id'], $travelIds)){
            $dtFrom = date('Y-m-d', strtotime($row['date_from']));
            $dtTo = date('Y-m-d', strtotime($row['date_to']));
            if(strtotime($tempDate) >= strtotime($dtFrom) && strtotime($tempDate) <= strtotime($dtTo)){
                return $row;
            }
        }
    }

    protected function getTravelOrderDriverById($id=0) {
        $arrIds = array();
        $conn = $this->conn();
        $sth = $conn->prepare("SELECT id FROM gcceforms.travel_order WHERE driver_id = :id AND status = 'Approved'");
        $sth->bindParam(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        if($sth->rowCount() > 0){
        foreach($sth->fetchAll(PDO::FETCH_ASSOC) as $row ){ $arrIds[] = intval($row['id']); }
        }
        return $arrIds;
    }

    protected function getTravelOrderPersonnelById($id=0) {
        $arrIds = array();
        $conn = $this->conn();
        $sth = $conn->prepare("SELECT tp.travel_order_id FROM gcceforms.travel_personnel as tp
        INNER JOIN gcceforms.travel_order as `to` ON tp.travel_order_id = to.id
        WHERE tp.employee_id = :id AND to.status = 'Approved'");
        $sth->bindParam(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        if($sth->rowCount() > 0){
        foreach($sth->fetchAll(PDO::FETCH_ASSOC) as $row ){ $arrIds[] = intval($row['travel_order_id']); }
        }
        return $arrIds;
    }

    }

?>