<?php
    class Login_model extends Dbase{

        public function __construct(){
            // $this->load->model("Hris_model", 'hris');
            $this->hrisModel = new Hris_model();

            // var_dump($this->hris->getEmployee(1762));
            // die();
        }

        public function change_time(){
            $post = $_POST;
            if(isset($post['emp_id']) && $post['emp_id'] != null){
                $emp_id = $post['emp_id'];
                $localTimeDate = $_POST['localTimeDate'];

                $conn = $this->conn("gcctimeutility");
                $sql = "UPDATE gcctimeutility.app_users SET change_local_time = :localDateTime WHERE emp_id = :emp_id";
                $sth = $conn->prepare($sql);
                $sth->bindParam(':emp_id', $emp_id);
                $sth->bindParam(':localDateTime', $localTimeDate);
                $sth->execute();
                if($sth){
                    return 1;
                }else{
                    return 0;
                }
            }
        }

        public function getAllData(){
            if(isset($_POST['unique_id'])){
                $imei = $_POST['unique_id'];
                $conn = $this->conn("gcctimeutility");
                $sql = "SELECT * FROM gcctimeutility.personnel as a LEFT JOIN gcctimeutility.app_users as b ON a.biometric_id = b.biometric_no WHERE a.app_user_id = :unique_id";
                $allData = $conn->prepare($sql);
                $allData->bindParam(':unique_id', $unique_id);
                $allData->execute();
                $count = $allData->rowCount();
                if($count != 0){
                    $status = $allData->fetch(PDO::FETCH_ASSOC);
                    if($this->count_user_app($imei, $status['biometric_id'])){
                        return json_encode(array("num" => 1, "data" => $status));
                    }else{
                        return json_encode(array("num" => 0));
                    }
                }else{
                    return json_encode(array("num" => 0));
                }
            }else{
                return json_encode(array("num" => 0));
            }
        }
    
        public function verifying(){
            if(isset($_POST['username']) && isset($_POST['password'])){
                $username = $_POST['username'];
                $password = md5($_POST['password']);
                $post_imei = $_POST['unique_id'];
                $device_id  = $_POST['device_id'];
                $device_name  = str_replace(" ", "_", $_POST['device_name']);
                $conn = $this->conn("gccmaster");
                $sth = $conn->prepare('SELECT a.biometricno, a.id, a.firstname, b.username, b.emp_id, a.biometricno,
                                              a.idno, c.name as position_name, a.pic_filename, a.lastname
                                       FROM tblemployees AS a
                                       LEFT JOIN tblusers AS b
                                       ON a.id = b.emp_id
                                       LEFT JOIN gcchris.tblposition AS c
                                       ON c.id = a.position OR c.name = a.position
                                       WHERE a.biometricno = :usern OR b.username = :usern AND b.password = :passw');
                $sth->bindParam(':usern', $username);
                $sth->bindParam(':passw', $password);
                $sth->execute();
                $count = $sth->rowCount();
                $emp_data = $sth->fetch(PDO::FETCH_ASSOC);
                if($count != 0){
                    if($this->ver_imei($emp_data['biometricno'], $post_imei) != 0){
                        $imei = $this->ver_imei($emp_data['biometricno'], $post_imei);
                    }else{
                        $imei = $this->add_imei($emp_data['biometricno'], $post_imei);
                    }
                    $this->log($post_imei, $imei, $emp_data['id']);
                    if($this->ver_user_account($imei, $emp_data['biometricno']) != true){
                        if($this->ver_unique_id($imei, $emp_data['biometricno']) != true){
                            $this->add_unique_id($imei, $emp_data['biometricno']);
                            if($this->count_appUsers($imei, $emp_data['biometricno'])){
                                if($this->count_user_app($imei, $emp_data['id'])){
                                    return $this->updateAppUsers($device_id, $device_name, $imei, $emp_data);
                                }else{
                                    return $this->insertAppUsers($device_id, $device_name, $imei, $emp_data);
                                }
                            }else{
                                if($this->is_unique_id_equal($imei, $emp_data['biometricno'])){
                                    return json_encode(array("emp_id" => $emp_data['id'], "imei" => $imei, "biometric_id" => $emp_data['biometricno'], 
                                        "firstname" => $emp_data['firstname'], "idno" => $emp_data['idno'], "position_name" => $emp_data['position_name'], 
                                        "pic_filename" => $emp_data['pic_filename'], "lastname" => $emp_data['lastname']));
                                }else{
                                    return 4;
                                }
                            }
                        }else{
                            return 3;
                        }
                    }else{
                        return 2;
                    }
                }else{
                    $this->log($post_imei, "", 0);
                    return 0;
                }
            }
        }

        public function verifyingV2(){
            $conn = $this->conn("gccmaster");
            $array_response["data_array"] = array();
            $response = array();

            $username = $_POST['username'];
            $password = md5($_POST['password']);
            $unique_id = $_POST['unique_id'];
            $device_id  = $_POST['device_id'];
            $device_name  = str_replace(" ", "_", $_POST['device_name']);

            $sth = $conn->prepare('SELECT a.biometricno, a.id, a.firstname, b.username, b.emp_id, a.biometricno,
                                          a.idno, c.name as position_name, a.pic_filename, a.lastname,
                                          IF(a.company_id IS NULL, "No Company", d.code) as company,
                                          IF(a.department_id IS NULL, "No Department", e.code)  as department
                                   FROM tblemployees AS a
                                   LEFT JOIN tblusers AS b
                                   ON a.id = b.emp_id
                                   LEFT JOIN gcchris.tblposition AS c
                                   ON c.id = a.position OR c.name = a.position
                                   LEFT JOIN gcchris.tblcompanies AS d
                                   ON d.id = a.company_id
                                   LEFT JOIN gcchris.tbldepartments AS e
                                   ON e.id = a.department_id OR e.description = a.department_id
                                   WHERE a.biometricno = :usern OR b.username = :usern AND b.password = :passw');
            $sth->bindParam(':usern', $username);
            $sth->bindParam(':passw', $password);
            $sth->execute();
            $count = $sth->rowCount();
            $emp_data = $sth->fetch(PDO::FETCH_ASSOC);

            if ($count > 0) {

                $app_user_id = md5($unique_id.$emp_data['id']);
                $checkUser = $this->checkUserExist($emp_data['id'], $device_name, $device_id, $app_user_id, $unique_id, $emp_data['biometricno']);

                if ($checkUser === 'grant_access') {

                    $this->saveLogs("success", "sign in", $emp_data['id'], "[Mobile] User sign in");
                    $this->updateUserStatus($emp_data['id'], $app_user_id, $unique_id, $device_id, $device_name);

                    $location = $this->getLocation($emp_data['biometricno']);
                    $last_log = $this->get_last_timelog($emp_data['biometricno']);

                    $response["status"] = true;
                    $response["attendance_data"] = $this->fetchUserAttendance($emp_data['biometricno'], $emp_data['id']);
                    $response["user_data"] = array("emp_id" => $emp_data['id'],
                                                   "app_user_id" => $app_user_id,
                                                   "biometric_id" => $emp_data['biometricno'],
                                                   "firstname" => $emp_data['firstname'],
                                                   "idno" => $emp_data['idno'],
                                                   "position_name" => $emp_data['position_name'],
                                                   "pic_filename" => $emp_data['pic_filename'],
                                                   "lastname" => $emp_data['lastname'],
                                                   "device_name" => $device_name,
                                                   "device_id" => $device_id,
                                                   "unique_id" => $unique_id,
                                                   'geolocation' => $location,
                                                   'company' => $emp_data['company'],
                                                   'department' => $emp_data['department'],
                                                   'last_log' => $last_log
                                                );
                } else {
                    $response["status"] = false;
                    $response["msg"] = $checkUser;
                }
               
            } else {
                $response["status"] = false;
                $response["msg"] = "Username and password is incorrect.";
            }

            array_push($array_response["data_array"], $response);

            return json_encode($array_response);
        }

        function getLocation($bio){
            $resultset = array();
            $arrData = array();
            $location = array();
            // personnel
            // 
            if($bio){
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

        function fetchUserAttendance($biometric_id, $emp_id){
            $conn = $this->conn("gcctimeutility");
            $array = array();
            $sth = $conn->prepare("SELECT id, time_status, biometric_id, latitude, longtitude, date, time
                                   FROM gcctimeutility.app_attendance 
                                   WHERE biometric_id='$biometric_id'");
            $sth->execute();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list = array();
                $list['is_sync'] = 'true';
                $list['emp_id'] = $emp_id;
                $list['cloud_id'] = $row['id'];
                $list['status'] = $row['time_status'];
                $list['biometric'] = $row['biometric_id'];
                $list['location'] = $row['latitude'].', '.$row['longtitude'];
                $list['date'] = $row['date'].' '.$row['time'];
                array_push($array, $list);
            }
            return $array;
        }

        function checkUserExist($emp_id, $device_name, $device_id, $app_user_id, $unique_id, $biometricno){
            $conn = $this->conn("gcctimeutility");
            $sth = $conn->prepare("SELECT id 
                                   FROM gcctimeutility.app_users 
                                   WHERE emp_id='$emp_id'");
            $sth->execute();

            if ($sth->rowCount() === 0) {
                $userApp = $conn->prepare("INSERT INTO gcctimeutility.app_users(`emp_id`, `device_name`, `device_id`, `app_user_id`, `user_imei`, `biometric_no`, `status`) 
                                           VALUES ('$emp_id', '$device_name', '$device_id', '$app_user_id', '$unique_id', '$biometricno', 1)");
                $userApp->execute();
                if($userApp){
                    return 'grant_access';
                }else{
                    $msg = 'Error on the server. Please contact system administrator.';
                    $msg_logs = '[Mobile] Error on saving user credentials.';
                    $this->saveLogs("error", "sign in", $emp_id, $msg_logs);
                    return $msg;
                }
            } else {
                if ($this->checkUserLoginToTheirDevice($unique_id, $emp_id) > 0) {
                    return 'grant_access';
                } else {
                    $check = $this->checkUserLoginToOtherDevice($unique_id, $emp_id);
                    $row = $check->fetch(PDO::FETCH_ASSOC);
                    if ($check->rowCount() > 0) {
                        $msg = 'Account is currently sign in at '.$row['device_name'].' device id of '.$row['device_id'].', you need to sign out before sign in to other devices.';
                        $msg_logs = '[Mobile] Account is currently sign in at '.$row['device_name'].' device id of '.$row['device_id'].', User tried to sign-in in this device '.$device_name.'.';
                        $this->saveLogs("error", "sign in", $emp_id, $msg_logs);
                        return $msg;
                    } else {
                        return 'grant_access';
                    }
                }
            }
        }

        public function updateUserStatus($emp_id, $app_user_id, $unique_id, $device_id, $device_name){
            $conn = $this->conn("gcctimeutility");
            $log = $conn->prepare("UPDATE gcctimeutility.app_users 
                                   SET status = 2, app_user_id = '$app_user_id', user_imei = '$unique_id', device_id = '$device_id', device_name = '$device_name'
                                   WHERE emp_id = '$emp_id'");
            $log->execute();
        }

        function checkUserLoginToTheirDevice($unique_id, $emp_id){
            $conn = $this->conn("gcctimeutility");
            $sth = $conn->prepare("SELECT id 
                                   FROM gcctimeutility.app_users 
                                   WHERE user_imei='$unique_id' AND emp_id='$emp_id'");
            $sth->execute();
            return $sth->rowCount();
        }

        function checkUserLoginToOtherDevice($unique_id, $emp_id){
            $conn = $this->conn("gcctimeutility");
            $sth = $conn->prepare("SELECT device_id, device_name
                                   FROM gcctimeutility.app_users 
                                   WHERE user_imei!='$unique_id' AND emp_id='$emp_id' AND status='2'");
            $sth->execute();
            return $sth;
        }

        public function check_login_status(){
            $emp_id = $_POST["emp_id"];
            $conn = $this->conn("gcctimeutility");
            $sth = $conn->prepare("SELECT id 
                                   FROM gcctimeutility.app_users 
                                   WHERE emp_id='$emp_id' AND status='2'");
            $sth->execute();
            return $sth->rowCount();
        }

        public function deviceVer(){
            if(isset($_POST["emp_id"])){
                $device_name = str_replace(" ", "_", $_POST["device_name"]);
                $device_id = $_POST["device_id"];
                $emp_id = $_POST["emp_id"];
                $imei = $_POST["imei"];
                $unique_id = md5($_POST['unique_id']);
                $this->log($_POST['unique_id'], $unique_id, $emp_id);
                $conn = $this->conn("gcctimeutility");
                if($this->count_user_app($imei, $emp_id) != true){
                    $sql = "INSERT INTO gcctimeutility.app_users(`emp_id`, `device_name`, `device_id`, `app_user_id`, `user_imei`, `status`) VALUES (:emp_id, :device_name, :device_id, :unique_id, :user_imei, 1)";
                    $userApp = $conn->prepare($sql);
                    $userApp->bindParam(':emp_id', $emp_id);
                    $userApp->bindParam(':device_name', $device_name);
                    $userApp->bindParam(':device_id', $device_id);
                    $userApp->bindParam(':unique_id', $unique_id, PDO::PARAM_STR);
                    $userApp->bindParam(':user_imei', $imei);
                    $userApp->execute();
                    if($userApp){
                        return 1;
                    }else{
                        return 0;
                    }
                }else{
                    if($this->update_device_personnel($device_id, $device_name, $imei)){
                        $sql = "UPDATE gcctimeutility.app_users SET device_name = :device_name, device_id = :device_id, app_user_id = :unique_id, status = 1 WHERE emp_id = :emp_id";
                        $userApp = $conn->prepare($sql);
                        $userApp->bindParam(':emp_id', $emp_id);
                        $userApp->bindParam(':device_name', $device_name);
                        $userApp->bindParam(':unique_id', $unique_id, PDO::PARAM_STR);
                        $userApp->bindParam(':device_id', $device_id);
                        $userApp->execute();
                        if($userApp){
                            return 1;
                        }else{
                            return 0;
                        }
                    }
                }
            }
        }

        public function logout(){
            if(isset($_POST['emp_id'])){
                $emp_id = $_POST['emp_id'];
                $conn = $this->conn("gcctimeutility");
                $sql = "UPDATE gcctimeutility.app_users SET status = 1 WHERE emp_id = :emp_id";
                $log = $conn->prepare($sql);
                $log->bindParam(':emp_id', $emp_id);
                $ver = $log->execute();
                if($ver){
                    $this->saveLogs("success", "sign out", $emp_id, "[Mobile] User sign out");
                    return true;
                }else{
                    $this->saveLogs("error", "sign out", $emp_id, "[Mobile] User sign out");
                    return false;
                }
            }
        }

        public function get_bio(){
            if(isset($_POST['unique_id']) && $_POST['unique_id'] != null){
                $unique_id = md5($_POST['unique_id']);
                $device_id = $_POST['device_id'];
                $device_name = $_POST['device_name'];
                $conn = $this->conn("gcctimeutility");
                $select = "SELECT * FROM gcctimeutility.personnel WHERE app_user_id = :app_user_id";
                $getDevUser = $conn->prepare($select);
                $getDevUser->bindParam(":app_user_id", $unique_id);
                $getDevUser->execute();
                $emp_data = $getDevUser->fetch(PDO::FETCH_ASSOC);
                if($getDevUser->rowCount() > 0){
                    if($this->add_unique_id($unique_id, $emp_data['biometric_id'])){
                        return json_encode($emp_data);
                    }else{
                        return 4;
                    }
                }else{
                    return 0;
                }
            }
        }

        public function server_time(){
            $date = date("Y-m-d H:i:s");
            if(isset($_POST['datetime'])){
                $datetime = $_POST['datetime'];
                if($datetime == $date){
                    return 1;
                }else{
                    return 0;
                }
            }
        }

        private function ver_imei($biomet_id, $imei){
            $conn = $this->conn("gcctimeutility");
            $select = "SELECT * FROM gcctimeutility.personnel WHERE biometric_id = :biometric_no";
            $ver = $conn->prepare($select);
            $ver->bindParam(":biometric_no", $biomet_id);
            $ver->execute();
            $resp = $ver->fetch(PDO::FETCH_ASSOC);
            if($ver->rowCount() > 0){
                return $resp['user_imei'];
            }else{
                return 0;
            }
        }

        private function add_imei($biomet_id, $imei){
            $conn = $this->conn();
            $update = "UPDATE gcctimeutility.personnel SET user_imei = :generate WHERE biometric_id = :biometric";
            $upd = $conn->prepare($update);
            $upd->bindParam(":generate", $imei);
            $upd->bindParam(":biometric", $biomet_id);
            $upd->execute();
            if($upd){
                return $imei;
            }else{
                return 0;
            }
        }

        private function log($unique_id, $temp_str, $emp_id){
            $storage = dir."/androidapp/storage/logs/";
            $temp_date = date('Y-m-d H:i:s');
            $emp_explode = explode("/", $unique_id);
            $serialize_temp = json_encode($emp_explode);

            if(!is_dir($storage)){
                mkdir($storage, 777);
            }

            //Something to write to txt log
            $log  = "\n{$temp_date}\n{$unique_id}\n{$temp_str}\n{$serialize_temp}\n";
            //Save string to log, use FILE_APPEND to append.
            file_put_contents(dir.'./androidapp/storage/logs/'.$emp_id.'.log', $log, FILE_APPEND);
        }

        private function is_unique_id_equal($unique_id, $biometric_id){
            $conn = $this->conn("gcctimeutility");
            $select = "SELECT user_imei FROM gcctimeutility.personnel WHERE biometric_id = :biometric_id";
            $check = $conn->prepare($select);
            $check->bindParam(":biometric_id", $biometric_id);
            $check->execute();
            $data = $check->fetch(PDO::FETCH_ASSOC);
            if($data['user_imei'] == $unique_id){
                return true;
            }else{
                return false;
            }
        }

        private function ver_unique_id($unique_id, $biometric_id){
            $conn = $this->conn("gcctimeutility");
            $select = "SELECT * FROM gcctimeutility.app_users WHERE user_imei = :unique_id AND biometric_no != :biometric_no";
            $ver = $conn->prepare($select);
            $ver->bindParam(":biometric_no", $biometric_id);
            $ver->bindParam(":unique_id", $unique_id);
            $ver->execute();
            if($ver->rowCount() > 0){
                return true;
            }else{
                return false;
            }
        }

        private function ver_user_account($unique_id, $biometric_id){
            $conn = $this->conn("gcctimeutility");
            $select = "SELECT * FROM gcctimeutility.app_users WHERE biometric_no = :biometric_no AND user_imei != :unique_id";
            $ver = $conn->prepare($select);
            $ver->bindParam(":biometric_no", $biometric_id);
            $ver->bindParam(":unique_id", $unique_id);
            $ver->execute();
            if($ver->rowCount() > 0){
                return true;
            }else{
                return false;
            }
        }

        private function update_device_personnel($device_id, $device_name, $imei){
            $conn = $this->conn("gcctimeutility");
            $update = "UPDATE gcctimeutility.personnel SET mobile_name = :device_name, mobile_id = :device_id WHERE user_imei = :user_imei";
            $bio = $conn->prepare($update);
            $bio->bindParam(":device_id", $device_id);
            $bio->bindParam(":device_name", $device_name);
            $bio->bindParam(":user_imei", $imei);
            $bio->execute();
            if($bio){
                return true;
            }else{
                return false;
            }
        }

        private function add_unique_id($unique_id, $biometric_id){
            $conn = $this->conn("gcctimeutility");
            $update = "UPDATE gcctimeutility.personnel SET user_imei = :unique_id WHERE biometric_id = :biometric";
            $bio = $conn->prepare($update);
            $bio->bindParam(":unique_id", $unique_id);
            $bio->bindParam(":biometric", $biometric_id);
            $bio->execute();
            if($bio){
                return true;
            }else{
                return false;
            }
        }

        private function getEmpId($biometric_id){
            $conn = $this->conn();
            $bio = $conn->prepare("SELECT id FROM tblemployees WHERE biometricno = :biometricno");
            $bio->bindParam(":biometricno", $biometric_id);
            $bio->execute();
            $data = $bio->fetch(PDO::FETCH_ASSOC);
            return $data['id'];
        }

        private function updateAppUsers($device_id, $device_name, $imei, $emp_data){
            $connect = $this->conn("gcctimeutility");
            $update = "UPDATE gcctimeutility.app_users SET status = 1 WHERE device_id = :device_id AND device_name = :device_name AND emp_id = :emp_id AND biometric_no = :biometric AND user_imei = :imei";
            $stats = $connect->prepare($update);
            $stats->bindParam(':device_id', $device_id);
            $stats->bindParam(':device_name', $device_name);
            $stats->bindParam(':emp_id', $emp_data['id']);
            $stats->bindParam(':biometric', $emp_data['biometricno']);
            $stats->bindParam(':imei', $imei);
            $stats->execute();
            if($stats){
                return json_encode(array("emp_id" => $emp_data['id'], "imei" => $imei, "biometric_id" => $emp_data['biometricno'], "firstname" => $emp_data['firstname'], 
                                         "idno" => $emp_data['idno'], "position_name" => $emp_data['position_name'], "pic_filename" => $emp_data['pic_filename'], 
                                         "lastname" => $emp_data['lastname']));
            }else{
                return 0;
            }
        }

        private function insertAppUsers($device_id, $device_name, $imei, $emp_data){
            $connect = $this->conn("gcctimeutility");
            $update = "INSERT INTO gcctimeutility.app_users(device_id, device_name, emp_id, biometric_no, user_imei, status ) VALUES ( :device_id, :device_name, :emp_id, :biometric, :imei, 1 )";
            $stats = $connect->prepare($update);
            $stats->bindParam(':device_id', $device_id);
            $stats->bindParam(':device_name', $device_name);
            $stats->bindParam(':emp_id', $emp_data['id']);
            $stats->bindParam(':biometric', $emp_data['biometricno']);
            $stats->bindParam(':imei', $imei);
            $stats->execute();
            if($stats){
                return json_encode(array("emp_id" => $emp_data['id'], "imei" => $imei, "biometric_id" => $emp_data['biometricno'], "firstname" => $emp_data['firstname'], 
                                         "idno" => $emp_data['idno'], "position_name" => $emp_data['position_name'], "pic_filename" => $emp_data['pic_filename'], 
                                         "lastname" => $emp_data['lastname']));
            }else{
                return 0;
            }
        }

        private function count_user_app($unique_id, $emp_id){
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.app_users WHERE user_imei = :unique_id AND emp_id = :emp_id";
            $data = $conn->prepare($sql);
            $data->bindParam(':emp_id', $emp_id);
            $data->bindParam(':unique_id', $unique_id);
            $data->execute();
            if($data->rowCount() != 0){
                return true;
            }else{
                return false;
            }
        }

        private function count_appUsers($imei,$bionum){
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.personnel WHERE user_imei = :imei AND biometric_id = :bio_num";
            $data = $conn->prepare($sql);
            $data->bindParam(':bio_num', $bionum);
            $data->bindParam(':imei', $imei);
            $data->execute();
            if($data->rowCount() != 0){
                return true;
            }else{
                return false;
            }
        }

        private function getDeviceId($dev_id, $emp_id){
            $conn = $this->conn("gcctimeutility");
            $sql = "SELECT * FROM gcctimeutility.app_users WHERE device_id = :device_id and emp_id = :emp_id";

            $data = $conn->prepare($sql);
            $data->bindParam(':device_id', $device_id);
            $data->bindParam(':emp_id', $emp_id);
            $data->execute();
            if($data->rowCount() != 0){
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
            $all_logs = array();
            $data = $conn->prepare($sql);
            $data->bindParam(":bio_id", $bio_id);
            $data->execute();
            $count = $data->rowCount();

            if($count > 0){
                $result = $data->fetch(PDO::FETCH_ASSOC);
            }

            return $result;
        }

        public function verifyWeb(){
            $result = array();
            $post = $_POST;
            $image = './assets/images/profile/no_image.jpg';

            $conn = $this->conn('gccmaster');

            $sql = 'SELECT a.*, b.id as emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.department_id, c.code as company, d.code as department, b.pic_filename, a.is_suspended
                FROM tblusers as a
                LEFT JOIN tblemployees as b ON b.id = a.emp_id
                LEFT JOIN gcchris.tblcompanies as c ON c.id = b.company_id
                LEFT JOIN gcchris.tbldepartments as d ON d.id = b.department_id
                WHERE a.username = :username AND a.password = :pass
                LIMIT 1';
            
            $query = $conn->prepare($sql);
            $query->bindParam(':username', $post['username']);
            $query->bindParam(':pass', MD5($post['password']));
            $query->execute();


            if($query->rowCount() > 0){
                $row = $query->fetch(PDO::FETCH_ASSOC);

                $tempFile = "../uploads/files/images/employee_files/empcode_{$row[emp_id]}/thumbnails/{$row[pic_filename]}";

                if(file_exists(realpath(dirname($tempFile)))){
                    $image = "/uploads/files/images/employee_files/empcode_{$row[emp_id]}/thumbnails/{$row[pic_filename]}";
                }

                $row['pic_filename'] = $image;
                $row['employee_data'] = $this->hrisModel->getEmployee($row['emp_id']);

                $result['response'] = TRUE;
                $result['data'] = $row;
            }else{
                $result['response'] = FALSE;
                $result['data'] = array();
            }

            return json_encode($result);
        }
    }
?>