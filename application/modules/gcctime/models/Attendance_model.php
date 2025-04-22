<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Attendance_model extends CI_Model {
        private $today;

        public function __construct() {
            parent::__construct();
            $this->load->model("gcctime/Biometric_model", "biometric");
            date_default_timezone_set("Asia/Manila");
            $this->today = date("Y-m-d");
        }

        public function getAttendanceByDateRange($dates = array(), $biometric_id) {
            //$query = $this->db->query("SELECT * FROM `attendance` WHERE `datetime` BETWEEN '{$dates[0]}' AND '{$dates[1]}' ORDER BY 'datetime' DESC;");

            $this->db->select("a.*, b.device_name, b.status");
            $this->db->from("gcctimeutility.attendance a");
            $this->db->join("gcctimeutility.devices b", "b.id = a.device_id", "left");
            $this->db->where('a.datetime >=', date_format(date_create($dates[0]), "Y-m-d H:i:s"));
            $this->db->where('a.datetime <=', date_format(date_create($dates[1]), "Y-m-d H:i:s"));

            if ($biometric_id) {
                $this->db->where("a.biometric_id", $biometric_id);
            }
            $this->db->order_by("a.datetime", "DESC");

            $query = $this->db->get();

            return $query;

        }

        public function getSingleAttendanceByDateRange($biometric_id = NULL, $dates = array()) {
            //$query = $this->db->query("SELECT * FROM `attendance` WHERE `datetime` BETWEEN '{$dates[0]}' AND '{$dates[1]}' ORDER BY 'datetime' DESC;");

            $this->db->select("*");
            $this->db->from("gcctimeutility.attendance");
            $this->db->where("biometric_id", $biometric_id);
            $this->db->where('datetime >=', date_format(date_create($dates["start"]), "Y-m-d H:i:s"));
            $this->db->where('datetime <=', date_format(date_create($dates["end"]), "Y-m-d H:i:s"));
            $query = $this->db->get();

            return $query->row_array();

        }

        public function getCurrentAttendance() {
            $currentDate = $this->getLastSyncDate();

            var_dump($currentDate);
            $this->db->select("a.biometric_id, a.device_id, a.datetime, a.state, a.is_custom, b.device_name, b.status, c.name");
            $this->db->from("gcctimeutility.attendance a");
            $this->db->join("gcctimeutility.devices b", "b.id = a.device_id", "left");
            $this->db->join("gcctimeutility.personnel c", "c.biometric_id = a.biometric_id", "left");
            $this->db->like("datetime", $currentDate);

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                return $query->result_array();
            } else {
                return false;
            }
        }

        function checkLate($biometric_id = NULL, $date = NULL) {
            date_default_timezone_set("Asia/Taipei");
            $resultarray = array();
            $getPersonnel = $this->crud->load(array("biometric_id" => $biometric_id), "gcctimeutility.personnel");
            $getDepartment = $this->crud->load(array("id" => $getPersonnel["department_id"]), "gcctimeutility.department");
            $getLate = $this->crud->load(array("id" => $getDepartment["shift_id"]), "gcctimeutility.shifts");

            $now = date_format(date_create($date), "H:i");

            $am_start = date_format(date_create($getLate["am_start"]), "H:i");
            $am_end = date_format(date_create($getLate["am_end"]), "H:i");

            $pm_start = date_format(date_create($getLate["pm_start"]), "H:i");
            $pm_end = date_format(date_create($getLate["pm_end"]), "H:i");

            $late = "";
            if ($am_start > $am_end) {
                if ($now >= $am_start || $now < $am_end) {
                    $late = "late";
                }
            } else if ($now >= $am_start && $now <= $am_end) {
                $late = "late";
            }

            if ($pm_start > $pm_end) {
                if ($now >= $pm_start || $now < $pm_end) {
                    $late = "late";
                }
            } else if ($now >= $pm_start && $now <= $pm_end) {
                $late = "late";
            }

            return $late;
        }

        public function getCurrentLate() {
            $resultset = array();
            date_default_timezone_set("Asia/Taipei");

            $today = date("Y-m-d");
            $currentDateToday = date("Y-m-d H:i:s");
            $timeToday = date_format(date_create($currentDateToday), "h:i:s A");

            $begin = new DateTime($today);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            $dates = array($begin, $newtime);
            $getAttendanceByDateRange = $this->getAttendanceByDateRange($dates, array());
            if ($getAttendanceByDateRange->num_rows() > 0) {
                $_AMCheck = date_format(date_create($timeToday), "A");
                foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                    $checkLate = $this->checkLate($_getAttendanceByDateRange["biometric_id"], $_getAttendanceByDateRange["datetime"]);
                    $data = array();
                    if ($checkLate) {
                        $getPersonnel = $this->crud->load(array("biometric_id" => $_getAttendanceByDateRange["biometric_id"]), "gcctimeutility.personnel");
                        $getEmployeeData = $this->crud->load(array("biometricno" => $_getAttendanceByDateRange["biometric_id"]), "gccmaster.tblemployees");

                        $getDepartment = $this->crud->load(array("id" => $getPersonnel["department_id"]), "gcctimeutility.department");
                        $getLate = $this->crud->load(array("id" => $getDepartment["shift_id"]), "gcctimeutility.shifts");

                        $datestart = date_create($today . " " . $getLate["am_start"] . ":00");
                        $dateend = date_create($_getAttendanceByDateRange["datetime"]);

                        $diff = date_diff($datestart, $dateend);

                        $data["biometric_id"] = $_getAttendanceByDateRange["biometric_id"];
                        $data["device"] = $_getAttendanceByDateRange["device_id"];
                        $data["name"] = $getPersonnel["name"];
                        $data["department_id"] = (isset($getEmployeeData["department_id"]) && $getEmployeeData["department_id"]) ? $getEmployeeData["department_id"] : "0";
                        $data["date"] = date_format(date_create($_getAttendanceByDateRange["datetime"]), "n/d/Y");
                        $data["time"] = date_format(date_create($_getAttendanceByDateRange["datetime"]), "h:i A");
                        $data["minlate"] = $diff->format("%i");

                        $data["minlate"] += 1;
                        $data["minlate"] = $data["minlate"] . " Mins";

                        $getTime = date_format(date_create($_getAttendanceByDateRange["datetime"]), "h:i:s A");
                        $AMCheck = date_format(date_create($getTime), "A");

                        if ($AMCheck == "AM") {
                            $resultset["checklate_am"][] = $data;
                            /* $resultset["current_state"] = "AM"; */
                        }
                        if ($AMCheck == "PM") {
                            $resultset["checklate_pm"][] = $data;
                            /* $resultset["current_state"] = "PM"; */
                        }
                    }
                }

                if ($_AMCheck == "AM") {
                    $resultset["current_state"] = $_AMCheck;
                }
                if ($_AMCheck == "PM") {
                    $resultset["current_state"] = $_AMCheck;
                }
            }

            return $resultset;
        }

        function getCurrentAbsent() {
            $resultset = array();
            date_default_timezone_set("Asia/Taipei");

            $now = date("F d, Y A");
            $nowsf = date("F d, Y H:i:s");
            $nows = new DateTime();

            $now = $this->attendance->getLastSyncDate(true);
            $todays = $this->attendance->getLastSyncDate();

            //$todays = date("Y-m-d")." 00:00:00";

            $begin = new DateTime($todays);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($todays);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            //$getAbsentCollection = $this->crud->getCollection(array("updated_at >="=>$todays,"updated_at <="=>$newtime),"gcctimeutility.absent");

            $this->db->select("*");
            $this->db->from("gcctimeutility.absent");
            $this->db->like("updated_at", $todays);
            $this->db->order_by("id", "DESC");
            $queryget = $this->db->get();
            $getAbsentCollection = $queryget->result_array();

            $data = array();
            $checker = array();
            if ($getAbsentCollection) {
                foreach ($getAbsentCollection as $_getAbsentCollection) {
                    $getPersonnel = $this->crud->load(array("biometric_id" => $_getAbsentCollection["biometric_id"]), "gcctimeutility.personnel");
                    $getEmployeeData = $this->crud->load(array("biometricno" => $_getAbsentCollection["biometric_id"], "employee_status" => "Active"), "gccmaster.tblemployees");
                    $getDept = $this->crud->crud->load(array("id" => $getPersonnel["department_id"]), "gcctimeutility.department");
                    $getLOA = "";
                    $getLOA = $this->crud->getCollection(array("employee" => $getEmployeeData["id"]), "gcceforms.loa", array("id" => "DESC"));
                    $newLOAData = "false";

                    $catch = array();
                    if ($getLOA) {
                        foreach ($getLOA as $_getLOA) {

                            if (substr($_getLOA["date_from"], 11, 2) == "8") {
                                $ampm = "AM";
                            } else {
                                $ampm = "PM";
                            }

                            $datefrom = new DateTime();
                            $dateto = new DateTime($_getLOA["date_to"]);

                            switch ($_getLOA["type"]) {
                                case '1':
                                    if ($nows->getTimestamp() >= $datefrom->getTimestamp() && $nows->getTimestamp() <= $dateto->getTimestamp()) {
                                        $catch["loaid"] = $_getLOA["id"];
                                        $catch["status"] = "positive";
                                    }
                                    break;
                                case '2':

                                    if ($nows->getTimestamp() >= $datefrom->getTimestamp() || $nows->getTimestamp() <= $dateto->getTimestamp()) {
                                        if ($_getAbsentCollection["meredien"] == $ampm) {
                                            $catch["loaid"] = $_getLOA["id"];
                                            $catch["status"] = "positive";
                                        }
                                    }
                                    break;
                                case '3':

                                    if (date_format(date_create($_getLOA["date_from"]), "Y-m-d") == date('Y-m-d')) {
                                        $catch["loaid"] = $_getLOA["id"];
                                        $catch["status"] = "positive";
                                    }
                                    break;
                                case '4':
                                    if ($nows->getTimestamp() >= $datefrom->getTimestamp() && $nows->getTimestamp() <= $dateto->getTimestamp()) {
                                        $catch["loaid"] = $_getLOA["id"];
                                        $catch["status"] = "positive";
                                    }
                                    break;
                                default:
                                    $catch["stat_loaid"] = null;
                                    break;
                            }

                        }
                    }

                    $getNewLOA = false;
                    $loacontent = "";
                    if (in_array("positive", $catch, true)) {
                        $getNewLOA = $this->crud->load(array("id" => $catch["loaid"]), "gcceforms.loa");
                        /*** $loacontent = $getNewLOA ? "<br/>LOA Reference No: ". $getNewLOA["reference_no"] : "N/A"; ***/
                        $loacontent = $getNewLOA ? $getNewLOA["reference_no"] : "N/A";
                    } else {
                        $loacontent = "N/A";
                    }

                    $meredien = date_format(date_create($_getAbsentCollection["updated_at"]), "A");

                    $datum = array();
                    $datum["biometricno"] = $getPersonnel["biometric_id"];
                    $datum["name"] = $getPersonnel["name"];
                    $dept = $getDept ? $getDept["description"] : "N/A";
                    $datum["department"] = $dept;
                    $datum["content"] = $loacontent;
                    $datum["mrdn"] = $meredien;

                    $ampm = strtolower($meredien);
                    $data[$ampm][] = $datum;
                }
            }

            $resultset["check_absent"] = $data;

            $resultset["check_absent"] = $data;

            return $resultset;
        }

        function getYesterdayAttendance($date) {
            $this->db->select("*");
            $this->db->from("gcctimeutility.attendance");
            $this->db->like("datetime", $date);
            $this->db->order_by("biometric_id", "asc");
            $this->db->order_by("datetime", "asc");
            $this->db->group_by('biometric_id');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                return $query->result_array();
            } else {
                return false;
            }

        }

        function getPersonnelAttendanceByDate($date = NULL, $biometricno = NULL) {

            if (isset($date) && isset($biometricno)) {
                $this->db->select("*");
                $this->db->from("gcctimeutility.attendance");
                $this->db->like("datetime", $date);
                $this->db->where("biometric_id", $biometricno);
                $this->db->order_by("datetime", "asc");
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    return $query->result_array();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getCountIdAttendance($date) {
            $this->db->select("*");
            $this->db->from("gcctimeutility.attendance");
            $this->db->like("datetime", $date);
            $this->db->order_by("biometric_id", "asc");
            $this->db->order_by("datetime", "asc");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $ids = array();
                foreach ($query->result_array() as $rs) {
                    $bioId = $rs["biometric_id"];
                    if (isset($ids[$bioId]) && $ids[$bioId]) {
                        $ids[$bioId] += 1;
                    } else {
                        $ids[$bioId] = 1;
                    }
                }

                if ($ids) {
                    return $ids;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getLastSyncDate($dateTime = false) {
            $last = $this->db
                ->where('event_name', 'sync_data')
                ->order_by('id', "desc")
                ->limit(1)
                ->get('gcctimeutility.event');

            if ($last->num_rows() > 0) {
                $row = $last->row();
                $dateCreated = date_create($row->created_at);

                if ($dateTime) {
                    return date_format($dateCreated, "Y-m-d H:i:s");
                } else {
                    return date_format($dateCreated, "Y-m-d");
                }
            } else {
                if ($dateTime) {
                    return date("Y-m-d H:i:s");
                } else {
                    return date("Y-m-d");
                }
            }
        }

        function getLackingDoubleEntries() {
            $resultset = array();
            date_default_timezone_set("Asia/Taipei");

            $yesterday = date('Y-m-d', strtotime("-1 days"));
            $query = $this->getYesterdayAttendance($yesterday);

            if ($query) {
                foreach ($query as $_query) {
                    $data = array();
                    $personnelAttendanceData = $this->getPersonnelAttendanceByDate($yesterday, $_query["biometric_id"]);
                    $getPersonnelData = $this->crud->load(array("biometric_id" => $_query["biometric_id"]), "gcctimeutility.personnel");

                    $loggedTime = "<table cellpadding='2' cellspacing='2' border='1' bordercolor='#333333' bgcolor='#ffffff'>";
                    $loggedTime .= "<tbody><tr>";
                    foreach ($personnelAttendanceData as $index => $datax) {
                        $dateTime = date_format(date_create($datax["datetime"]), "h:i A");
                        $loggedTime .= "<td align='center' style='color: #333333; font-size: 12px; font-family: Work Sans, Calibri, sans-serif; line-height: 18px; padding: 2px 5px; text-align: center;'>{$dateTime}</td>";
                    }
                    $loggedTime .= "</tr></tbody>";
                    $loggedTime .= "</table>";

                    $total = count($personnelAttendanceData);

                    if (count($personnelAttendanceData) < 4) {
                        $data = array();
                        $data["biometric_id"] = $_query["biometric_id"];
                        $data["employee_name"] = (isset($getPersonnelData["name"]) && $getPersonnelData["name"]) ? trim($getPersonnelData["name"]) : $_query["biometric_id"];
                        $data["logged_time"] = $loggedTime;
                        $data["time_count"] = $total;
                        $resultset["lacking_entry"][] = $data;
                    } else if (count($personnelAttendanceData) > 4) {
                        $data = array();
                        $data["biometric_id"] = $_query["biometric_id"];
                        $data["employee_name"] = (isset($getPersonnelData["name"]) && $getPersonnelData["name"]) ? trim($getPersonnelData["name"]) : $_query["biometric_id"];
                        $data["logged_time"] = $loggedTime;
                        $data["time_count"] = $total;
                        $resultset["double_entry"][] = $data;
                    }
                }
            }

            return $resultset;
        }

        /*** new code 012219 ***/
        private function getDevice() {
            $query = $this->crud->load(array("status" => 1), "gcctimeutility.devices");
            return $query;
        }

        private function getAllDevice() {
            $this->db->from("gcctimeutility.devices");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                return $query->result();
            } else {
                return false;
            }
        }

        private function log_event($event = "") {
            $query = $this->crud->insert(array("event_name" => $event), "gcctimeutility.event");
            return $query ? TRUE : FALSE;
        }

        private function checkAttendanceData($biometric_id = NULL, $datetime = NULL) {
            $query = $this->crud->load(array("biometric_id" => $biometric_id, "datetime" => $datetime), "gcctimeutility.attendance");
            return $query ? TRUE : FALSE;
        }

        public function ipIsReachable($ip = null) {
            if ($ip) {
                $url = "{$ip}";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpcode >= 200 && $httpcode < 300) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getBiometricData($alldevice = false) {
            include(BASEPATH . 'libraries/zklibrary.php');

            if ($alldevice) {
                $collect = $this->getAllDevice();
                if ($collect) {
                    $count = 0;
                    foreach ($collect as $cc) {
                        $ipAddress = $cc->ip_address;
                        $isReachable = $this->ipIsReachable($ipAddress);
                        if ($isReachable) {
                            $deviceName = strtolower($cc->device_name);
                            $deviceName = preg_replace('/\s+/', '_', $deviceName);

                            $zk = new ZKLibrary($ipAddress, 4370);

                            $zk->connect();
                            $zk->disableDevice();
                            $attendance = $zk->getAttendance();
                            $zk->enableDevice();
                            $zk->disconnect();

                            if ($attendance) {
                                $nData = array();
                                $nData["attendance_log"] = $attendance;
                                $nData["device_id"] = $cc->id;

                                /* $response = $this->storeData($nData, $deviceName); */
                                $response = $this->storeAttendanceData($nData, $deviceName);
                                if ($response) {
                                    $this->log_event("sync_data");
                                    $count++;
                                }
                            }
                        }
                    }

                    if ($count > 0) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                $device = $this->getDevice();
                if (isset($device, $device["ip_address"]) && $device && $device["ip_address"]) {
                    $ipAddress = $device["ip_address"];
                    $isReachable = $this->ipIsReachable($ipAddress);
                    if ($isReachable) {
                        $deviceName = strtolower($device["device_name"]);
                        $deviceName = preg_replace('/\s+/', '_', $deviceName);

                        $zk = new ZKLibrary($ipAddress, 4370);

                        $zk->connect();
                        $zk->disableDevice();
                        $attendance = $zk->getAttendance();
                        $zk->enableDevice();
                        $zk->disconnect();

                        if ($attendance) {
                            $nData = array();
                            $nData["attendance_log"] = $attendance;
                            $nData["device_id"] = $device["id"];

                            /* $response = $this->storeData($nData, $deviceName); */
                            $response = $this->storeAttendanceData($nData, $deviceName);
                            if ($response) {
                                $this->log_event("sync_data");
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }

                    }
                } else {
                    return false;
                }

            }
        }

        function insertEvent($event = "sync_data") {
            $insert = $this->db->insert("gcctimeutility.event", array("event_name" => $event));
            if ($insert) {
                return true;
            } else {
                return false;
            }
        }

        function getEventCount($event = "sync_data", $date = null) {
            $date = ($date) ? $date : date("Y-m-d");
            $this->db->from("gcctimeutility.event");
            $this->db->where("event_name", $event);
            $this->db->like("created_at", $date);
            $query = $this->db->get();

            return $query->num_rows();
        }

        function storeAttendanceData($data = array(), $deviceName = "storeData") {
            if ($data) {
                $dateTime = Date("dYm");
                $dateTimeX = Date("Y-m-d H:i");
                $filepath = realpath("./uploads/data");
                $fileUpload = "{$filepath}/{$deviceName}-{$dateTime}.json";

                if (file_exists($fileUpload)) {
                    unlink($fileUpload);
                } else {
                    $handle = fopen($fileUpload, "w");
                    if ($handle) {
                        $data = json_encode($data);
                        $response = file_put_contents($fileUpload, $data);
                        fclose($handle);

                        if ($response) {
                            return true;
                        } else {
                            $event = $this->insertEvent("error_store_data");
                            if ($event) {
                                $eventCount = $this->getEventCount("error_store_data");
                                if ($eventCount <= 10) {
                                    $this->core_layout->logNotification("[{$dateTimeX}] - Failed to insert data on the created file for attendance data!", "error", "gcctimeV2");
                                    $this->storeAttendanceData($data, $deviceName);
                                }
                            }
                        }
                    } else {
                        $event = $this->insertEvent("error_store_data");
                        if ($event) {
                            $eventCount = $this->getEventCount("error_store_data");
                            if ($eventCount <= 15) {
                                $this->core_layout->logNotification("[{$dateTimeX}] - Failed to create file for attendance data! [{$dateTimeX}]", "error", "gcctimeV2");
                                $this->storeAttendanceData($data, $deviceName);
                            }
                        }
                    }
                }
            } else {
                return false;
            }
        }

        function storeData($data = array(), $deviceName = "storeData") {
            if ($data) {
                $dateTime = Date("dYm");
                $filepath = realpath("./uploads/data");
                $fileUpload = "{$filepath}/{$deviceName}-{$dateTime}.json";

                $handle = fopen($fileUpload, "w");
                $data = json_encode($data);
                $response = file_put_contents($fileUpload, $data);
                fclose($handle);

                if ($response) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getStoredData() {
            $dateTime = Date("dYm");
            $filepath = realpath("./uploads/data");
            $nData = array();

            $scanned_directory = array_diff(scandir($filepath), array('..', '.'));
            $files = array();
            if ($scanned_directory) {
                foreach ($scanned_directory as $dfile) {
                    $filename = explode(".", $dfile);
                    if (count($filename) == 2) {
                        $fname = explode("-", $filename[0]);
                        if (count($fname) == 2) {
                            if ($dateTime == $fname[1]) {
                                $files[] = $dfile;
                            }
                        }
                    }
                }
            }

            if ($files) {
                foreach ($files as $file) {
                    $fileUpload = "{$filepath}/{$file}";
                    $file = file_get_contents($fileUpload, true);
                    $file = json_decode($file, true);
                    $attendanceLogs = (isset($file["attendance_log"]) && $file["attendance_log"]) ? $file["attendance_log"] : array();
                    $deviceId = (isset($file["device_id"]) && $file["device_id"]) ? $file["device_id"] : 0;
                    if ($attendanceLogs) {
                        $currentDate = Date("Y-m-d");
                        foreach ($attendanceLogs as $key => $value) {
                            $cDate = Date("Y-m-d", strtotime($value[3]));
                            if ($currentDate == $cDate) {
                                $value[] = intval($deviceId);
                                $nData[] = $value;
                            }
                        }
                    }
                }
            }

            return $nData;
        }

        public function doSyncData($alldevice = false) {
            $_weekday = date("l", strtotime($this->today));
            $_weekday = strtolower($_weekday);
            $dateTimeX = Date("Y-m-d H:i");

            if ($_weekday !== "sunday") {
                $response = $this->getBiometricData($alldevice);
                if ($response) {
                    $syncData = $this->getStoredData();
                    if ($syncData) {
                        foreach ($syncData as $data) {
                            if (!$this->checkAttendanceData($data[1], $data[3])) {
                                $arrData = array();
                                $arrData["biometric_id"] = $data[1];
                                $arrData["state"] = $data[2];
                                $arrData["datetime"] = $data[3];
                                $arrData["device_id"] = $data[4];

                                $this->db->insert("gcctimeutility.attendance", $arrData);
                            }
                        }
                        $this->core_layout->logNotification("Sync data to attendance table successful.", "success", "gcctimeV2");
                        return true;
                    } else {
                        $this->core_layout->logNotification("[{$dateTimeX}] - Sync data failed!", "error", "gcctimeV2");
                        return false;
                    }
                } else {
                    $this->core_layout->logNotification("[{$dateTimeX}] - Failed to get biometric data!", "error", "gcctimeV2");
                    return false;
                }
            } else {
                $this->core_layout->logNotification("Failed to sync data, its sunday today.. skip! skip!", "error", "gcctimeV2");
                return false;
            }
        }

        /*** new code 012219 ***/

        function cronjob_inject_sync() {
            $_weekday = date("l", strtotime($this->today));
            $_weekday = strtolower($_weekday);

            $dateX = date("Y-m-d H:i");

            if ($_weekday !== "sunday") {
                $syncData = $this->getStoredData();
                if ($syncData) {
                    $counter = 0;
                    foreach ($syncData as $data) {
                        if (!$this->checkAttendanceData($data[1], $data[3])) {
                            $arrData = array();
                            $arrData["biometric_id"] = $data[1];
                            $arrData["state"] = $data[2];
                            $arrData["datetime"] = $data[3];
                            $arrData["device_id"] = $data[4];

                            $inserted = $this->db->insert("gcctimeutility.attendance", $arrData);
                            if ($inserted) {
                                $counter++;
                            }
                        }
                    }
                    if ($counter > 0) {
                        $save = $this->insertEvent("sync_data");
                        if ($save) {
                            $this->core_layout->logNotification("[ {$dateX} ] ~ Sync data successful", "success", "gcctimeV2");
                        }
                        return true;
                    } else {
                        $this->core_layout->logNotification("[ {$dateX} ] ~ Failed sync, current data is updated", "error", "gcctimeV2");
                        return false;
                    }
                } else {
                    $doSync = $this->allowSyncData();
                    if ($doSync == true) {
                        $save = $this->insertEvent("error_sync_data");
                        if ($save) {
                            $this->core_layout->logNotification("[ {$dateX} ] ~ Failed sync data from downloaded textfile", "error", "gcctimeV2");
                        }
                        $this->cronjob_inject_sync();
                    } else {
                        $this->core_layout->logNotification("[ {$dateX} ] ~ Failed sync, data trigger has reached its limit!", "error", "gcctimeV2");
                        return false;
                    }
                }
            }
        }

        function allowDownloadData() {
            $date = date("Y-m-d");
            $this->db->from("gcctimeutility.event");
            $this->db->where("event_name", "error_download_data");
            $this->db->like("created_at", $date);
            $query = $this->db->get();
            if ($query->num_rows() >= 0 && $query->num_rows() <= 10) {
                return true;
            } else {
                return false;
            }
        }

        function allowSyncData() {
            $date = date("Y-m-d");
            $this->db->from("gcctimeutility.event");
            $this->db->where("event_name", "error_download_data");
            $this->db->like("created_at", $date);
            $query = $this->db->get();
            if ($query->num_rows() >= 0 && $query->num_rows() <= 20) {
                return true;
            } else {
                return false;
            }
        }

        function cronjob_inject($alldevice = false) {
            $_weekday = date("l", strtotime($this->today));
            $_weekday = strtolower($_weekday);
            $dateX = date("Y-m-d H:i");

            if ($_weekday !== "sunday") {
                include(BASEPATH . 'libraries/zklibrary.php');
                if ($alldevice) {
                    $collect = $this->getAllDevice();
                    if ($collect) {
                        $count = 0;
                        foreach ($collect as $cc) {
                            $ipAddress = $cc->ip_address;
                            $isReachable = $this->ipIsReachable($ipAddress);
                            if ($isReachable) {
                                $deviceName = strtolower($cc->device_name);
                                $deviceName = preg_replace('/\s+/', '_', $deviceName);

                                $zk = new ZKLibrary($ipAddress, 4370);

                                $zk->connect();
                                $zk->disableDevice();
                                $attendance = $zk->getAttendance();
                                $zk->enableDevice();
                                $zk->disconnect();

                                if ($attendance) {
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
                                    /*** chmod($fileUpload, 0777); ***/

                                    if ($response) {
                                        $count++;
                                    }
                                }
                            }
                        }

                        if ($count > 0) {
                            $save = $this->insertEvent("download_data");
                            if ($save) {
                                $this->core_layout->logNotification("[ {$dateX} ] ~ Download data successful", "success", "gcctimeV2");
                            }
                            return true;
                        } else {
                            $doExport = $this->allowDownloadData();
                            if ($doExport == true) {
                                $save = $this->insertEvent("error_download_data");
                                if ($save) {
                                    $this->core_layout->logNotification("[ {$dateX} ] ~ Failed download data to server", "error", "gcctimeV2");
                                }
                                $this->cronjob_inject($alldevice);
                            } else {
                                $this->core_layout->logNotification("[ {$dateX} ] ~ Failed download, data trigger has reached its limit!", "error", "gcctimeV2");
                                return false;
                            }
                        }
                    }
                } else {
                    $device = $this->getDevice();
                    if (isset($device, $device["ip_address"]) && $device && $device["ip_address"]) {
                        $ipAddress = $device["ip_address"];
                        $isReachable = $this->ipIsReachable($ipAddress);
                        if ($isReachable) {
                            $deviceName = strtolower($device["device_name"]);
                            $deviceName = preg_replace('/\s+/', '_', $deviceName);

                            $zk = new ZKLibrary($ipAddress, 4370);

                            $zk->connect();
                            $zk->disableDevice();
                            $attendance = $zk->getAttendance();
                            $zk->enableDevice();
                            $zk->disconnect();

                            if ($attendance) {
                                $nData = array();
                                $nData["attendance_log"] = $attendance;
                                $nData["device_id"] = $device["id"];

                                $dateTime = Date("dYm");
                                $filepath = realpath("./uploads/data");
                                $fileUpload = "{$filepath}/{$deviceName}-{$dateTime}.json";

                                $handle = fopen($fileUpload, "w");
                                $data = json_encode($nData);
                                $response = file_put_contents($fileUpload, $data);
                                fclose($handle);
                                /*** chmod($fileUpload, 0777); ***/

                                if ($response) {
                                    $save = $this->insertEvent("download_data");
                                    if ($save) {
                                        $this->core_layout->logNotification("[ {$dateX} ] ~ Download data successful", "success", "gcctimeV2");
                                    }
                                    return true;
                                } else {
                                    $doExport = $this->allowDownloadData();
                                    if ($doExport == true) {
                                        $save = $this->insertEvent("error_download_data");
                                        if ($save) {
                                            $this->core_layout->logNotification("[ {$dateX} ] ~ Failed download data to server", "error", "gcctimeV2");
                                        }
                                        $this->cronjob_inject($alldevice);
                                    } else {
                                        $this->core_layout->logNotification("[ {$dateX} ] ~ Failed download, data trigger has reached its limit!", "error", "gcctimeV2");
                                        return false;
                                    }
                                }
                            } else {
                                return false;
                            }

                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }

        function getStoredDataByDate($_dateTime = null) {
            $dateTime = Date("dYm", strtotime($_dateTime));
            $filepath = realpath("./uploads/data");
            $nData = array();

            $scanned_directory = array_diff(scandir($filepath), array('..', '.'));
            $files = array();
            if ($scanned_directory) {
                foreach ($scanned_directory as $dfile) {
                    $filename = explode(".", $dfile);
                    if (count($filename) == 2) {
                        $fname = explode("-", $filename[0]);
                        if (count($fname) == 2) {
                            if ($dateTime == $fname[1]) {
                                $files[] = $dfile;
                            }
                        }
                    }
                }
            }

            if ($files) {
                foreach ($files as $file) {
                    $fileUpload = "{$filepath}/{$file}";
                    $xfiles = file_get_contents($fileUpload, true);
                    $_file = json_decode($xfiles, true);
                    $attendanceLogs = (isset($_file["attendance_log"]) && $_file["attendance_log"]) ? $_file["attendance_log"] : array();
                    $deviceId = (isset($_file["device_id"]) && $_file["device_id"]) ? $_file["device_id"] : 0;
                    if ($attendanceLogs) {
                        $currentDate = Date("Y-m-d", strtotime($_dateTime));
                        $previousDate = Date("Y-m-d", strtotime("-1 day", strtotime($_dateTime)));
                        foreach ($attendanceLogs as $key => $value) {
                            $cDate = Date("Y-m-d", strtotime($value[3]));
                            if ($previousDate == $cDate) {
                                $value[] = intval($deviceId);
                                $nData[] = $value;
                            }
                            if ($currentDate == $cDate) {
                                $value[] = intval($deviceId);
                                $nData[] = $value;
                            }
                        }
                    }
                }
            }

            return $nData;
        }

        /*** 0523019 @pab ** */

        public function generateAbsenteeRecord($meredien = "AM", $currentDate = NULL) {
            $data = array();
            $getPersonnel = $this->getActivePersonnels();
            if ($getPersonnel) {
                $absenteeCount = 0;
                $noonTimeRange = $this->getNoonAbsentTimeRange($currentDate);

                foreach ($getPersonnel as $_getPersonnel) {
                    $includeAbsenteeRecord = false;

                    $biometricId = $_getPersonnel["biometric_id"];

                    $arrData = array();
                    $arrData["biometric_id"] = $biometricId;
                    $arrData["meredien"] = $meredien;

                    $response = $this->getTodayAbsentRecord($arrData, $currentDate);
                    $hasNoAttendanceByDateRange = $this->hasNoAttendanceByDateRange($biometricId, $this->getDateRangeToday());

                    $afternoonAttendance = $this->shift_manangement->getSingleAttendanceByDateRangeData($biometricId, $noonTimeRange);
                    $recordCount = $this->shift_manangement->getPersonnelCount($biometricId, $currentDate);
                    $personnelAttendance = $this->getPersonnelAttendance($biometricId, $currentDate, $meredien);
                    $noNoonEmployeeLogout = $this->noonEmployeeNoLogout($biometricId, $currentDate);

                    if ($response && $hasNoAttendanceByDateRange) {
                        $includeAbsenteeRecord = true;
                    }
                    if ($response && $personnelAttendance == false && $recordCount == 0) {
                        $includeAbsenteeRecord = true;
                    }
                    if ($response && (($meredien == "pm" || $meredien == "PM") && $afternoonAttendance == true && $recordCount == 0)) {
                        $includeAbsenteeRecord = true;
                    }
                    if ($response && $noNoonEmployeeLogout && ($meredien == "pm" || $meredien == "PM")) {
                        $includeAbsenteeRecord = true;
                    }

                    if ($includeAbsenteeRecord == true && ($_getPersonnel["department_id"] !== "0" && $_getPersonnel["shift_id"] !== "0")) {
                        $data[] = $arrData;
                    }
                }
            }

            return $data;
        }

        function noonEmployeeNoLogout($biometricId = null, $date = null) {
            if ($biometricId && $date) {
                $startDate = date("Y-m-d 00:00:00", strtotime($date));
                $endDate = date("Y-m-d 13:31:00", strtotime($date));

                $this->db->from("gcctimeutility.attendance");
                $this->db->where("biometric_id", $biometricId);
                $this->db->where("datetime >=", $startDate);
                $this->db->where("datetime <=", $endDate);
                $query = $this->db->get();

                $count = $query->num_rows();
                if ($count == 1 || $count == 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getPersonnelAttendance($biometric_id = null, $datetime = null, $ampm = "AM") {
            $flag = false;

            if ($biometric_id && $datetime) {
                $this->db->from("gcctimeutility.attendance");
                $this->db->where("biometric_id", $biometric_id);
                $this->db->like("datetime", $datetime);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $result = $query->result();

                    foreach ($result as $rs) {
                        $meredien = date("A", strtotime($rs->datetime));
                        if ($meredien == $ampm) {
                            $flag = true;
                        }
                    }
                }
            }
            return $flag;
        }

        private function hasNoAttendanceByDateRange($biometric_id = null, $arrDate = array()) {
            $this->db->from("gcctimeutility.attendance");
            $this->db->where("biometric_id", $biometric_id);
            $this->db->where('datetime >=', date_format(date_create($arrDate["start"]), "Y-m-d H:i:s"));
            $this->db->where('datetime <=', date_format(date_create($arrDate["end"]), "Y-m-d H:i:s"));
            $query = $this->db->get();

            if ($query->num_rows() == 0) {
                return true;
            } else {
                return false;
            }
        }

        private function getActivePersonnels() {
            $query = $this->db->get_where("gcctimeutility.personnel", array("is_active" => 1));
            if ($query->num_rows() > 0) {
                return $query->result_array();
            } else {
                return false;
            }
        }

        public function getNoonAbsentTimeRange($currentDate = NULL) {
            $currentDate = ($currentDate) ? $currentDate : date("Y-m-d");
            $start = date("Y-m-d 12:01:00", strtotime($currentDate));
            $end = date("Y-m-d 13:31:00", strtotime($currentDate));

            return array("start" => $start, "end" => $end);
        }

        private function getTodayAbsentRecord($arrData = array(), $currentDate = NULL) {
            $currentDate = ($currentDate) ? $currentDate : date("Y-m-d");
            $hasNoRecord = false;
            if ($arrData) {
                $this->db->from("gcctimeutility.absent");
                $this->db->where("biometric_id", $arrData["biometric_id"]);
                $this->db->where("meredien", $arrData["meredien"]);
                $this->db->like("updated_at", $currentDate);
                $query = $this->db->get();
                $hasNoRecord = ($query->num_rows() == 0) ? true : false;
            }
            return $hasNoRecord;
        }

        function getDateRangeToday($currentDate = NULL) {
            $currentDate = ($currentDate) ? $currentDate : date("Y-m-d");
            $today = $currentDate;

            $begin = new DateTime($today);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            return array("start" => $begin, "end" => $newtime);
        }

        /*** 0523019 @pab ** */
        public function getApprovalAttendance() {
            $sql = "SELECT attendance.id,attendance.biometric_id,attendance.datetime,attendance.approval_status,personnel.name,attendance_remarks.remarks FROM gcctimeutility.attendance LEFT JOIN gcctimeutility.personnel ON attendance.biometric_id = personnel.biometricno LEFT JOIN attendance_remarks ON attendance.id = attendance_remarks.attendance_id WHERE attendance.is_custom != 0";

            $query = $this->db->query("SELECT attendance_temp.id,attendance_temp.biometric_id,attendance_temp.datetime,attendance_temp.status,(SELECT remarks FROM gcctimeutility.attendance_remarks WHERE attendance_temp_id = attendance_temp.id ORDER BY id DESC LIMIT 1 ) AS remarks,personnel.name FROM gcctimeutility.attendance_temp LEFT JOIN gcctimeutility.personnel ON personnel.biometricno = attendance_temp.biometric_id");


            //$query = $this->db->query($sql);

            return $query ? $query->result_array() : false;
        }

        function getRealTimeAttendances($date = null, $multiple = 0) {
            $search = $this->input->post('search');
            if (!empty($date)) {
                $this->db->where("DATE(attendance.`date`)", $date);
            }
            $this->db->like('CONCAT(emp.lastname, emp.firstname)', $search, 'BOTH');
            $this->db->select("attendance.`date`, attendance.biometricno, attendance.device_state, attendance.device_id, 
                               devices.ip_address ip, devices.device_name device,
                               DATE(attendance.`date`) header,
                               UCASE(CONCAT(emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE' 
                                   AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END, ' ',
                                   emp.lastname, CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN 
                                   CONCAT(' ', emp.suffix) ELSE ''  END)) `employee_name`");
            $this->db->join("gcctimeutility.devices devices", "devices.id = attendance.device_id", "inner");
            $this->db->join("gcctimeutility.personnel personnel", "personnel.biometric_id = attendance.biometricno", "inner");
            $this->db->join("gccmaster.tblemployees emp", "emp.biometricno = personnel.biometricno", "inner");
            $this->db->group_by("attendance.date, attendance.biometricno");
            $zktime_logs_query = $this->db->get_compiled_select("`zktime_logs`.`attendance` attendance");

            $this->db->reset_query();

            if (!empty($date)) {
                $this->db->where("DATE(attendance.`datetime`)", $date);
            }
            $this->db->like('CONCAT(emp.lastname, emp.firstname)', $search, 'BOTH');
            $this->db->select("attendance.`datetime` `date`, attendance.biometric_id biometricno, 
                               attendance.state device_state, devices.id device_id,
                               devices.ip_address ip, devices.device_name device,
                               DATE(attendance.`datetime`) header,
                               UCASE(CONCAT(emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE' 
                                      AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END, ' ',
                                      emp.lastname, CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN 
                                      CONCAT(' ', emp.suffix) ELSE ''  END)) `employee_name`");
            $this->db->join("gcctimeutility.devices devices", "devices.id = attendance.device_id", "inner");
            $this->db->join("gcctimeutility.personnel personnel", "personnel.biometric_id = attendance.biometric_id", "inner");
            $this->db->join("gccmaster.tblemployees emp", "emp.biometricno = personnel.biometricno", "inner");
            $this->db->group_by("attendance.datetime, attendance.biometric_id");
            $gcctime_attendance_query = $this->db->get_compiled_select("gcctimeutility.`attendance` attendance");

            $resultSet['data'] = $this->db->query("SELECT `date`, `biometricno`, `device_state`, `device_id`, `ip`, `device`, `employee_name`
                FROM(($zktime_logs_query) UNION ALL($gcctime_attendance_query) ORDER BY `date` DESC) as x GROUP BY `date`, `biometricno` ORDER BY `date` DESC")->result();
            $resultSet['sql'] = $this->db->last_query();
            return $resultSet['data'];
        }

        function setAttendancePunches($tempAttendance=false){
            $post = $this->input->post();
            if(isset($post) && $post){
                $props = array("biometricno"=>"biometric_id", 
                "device_id"=>"device_id", 
                "verify_method"=>"verify_method", 
                "device_state"=>"state",
                "date"=>"datetime");

                $attendanceTable = $tempAttendance ? "zktime_logs.attendance_temp_receiver": "zktime_logs.attendance";
                $added = $this->db->insert($attendanceTable, $post);
                if($added){
                    if($tempAttendance == false){
                        $data = array();
                        $data["timelog_id"] = $this->db->insert_id();
                        foreach ($props as $key => $value) { $data[$value] = $post[$key]; }
                        $queryTimeLog = $this->db->get_where("gcctimeutility.attendance", array("biometric_id"=>$post["biometricno"], "datetime"=>$post["date"]));
                        if($queryTimeLog->num_rows() == 0){
                            $addedTimeLog = $this->db->insert("gcctimeutility.attendance", $data);
                            if($addedTimeLog){ return true; }
                            else{ return false; }
                        }else{
                            return false;
                        }
                    }else{
                        return true;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }

        function hookAttendanceData(){
            $post = $this->input->post();
            $dateTime = Date("dYm");
            $filepath = realpath("./uploads/data");
            $fileUpload = "{$filepath}/{$post["devicename"]}-{$dateTime}.json";
            
            $handle = fopen($fileUpload, "w");
            file_put_contents($fileUpload, $post["data"]);
            fclose($handle);

            $this->biometric->setBiometricData();
            return $post;
        }

        function syncAppAttendance(){
            $post = $this->input->post();
            if(isset($post['date_from']) && $post['date_from'] != null && isset($post['date_to']) && $post['date_to'] != null){
                $date_from = $post['date_from'];
                $date_to = $post['date_to'];
            }else{
                $date_from = date("Y-m-d");
                $date_to = date("Y-m-d");
            }
            $this->db->select("biometric_id, CONCAT(date,' ', time) as datetime, IF(id > 0, 2, 2) as is_custom");
            $this->db->where("date >=", $date_from);
            $this->db->where("date <=", $date_to);
            $app_attendance = $this->db->get("gcctimeutility.app_attendance");
            
            if($app_attendance->num_rows() > 0){
                $insert = array();
                
                foreach($app_attendance->result_array() as $app_data){
                    $this->db->where("datetime >=", $date_from);
                    $this->db->where("biometric_id", $app_data['biometric_id']);
                    $this->db->order_by("id", "DESC");
                    $attendance = $this->db->get('gcctimeutility.attendance');
                    if($attendance->num_rows() > 0){
                        // echo $attendance->num_rows();
                        foreach($attendance->result_array() as $app){
                            $a = array();
                            
                            // echo $app_data['datetime'] +" and "+ date('Y-m-d H:i:s', strtotime("+1 minutes", strtotime($app['datetime'])));
                            if( $app_data['datetime'] != $app['datetime'] && $app_data['datetime'] > date('Y-m-d H:i:s', strtotime("+1 minutes", strtotime($app['datetime']))) || $app_data['datetime'] != $app['datetime'] && $app_data['datetime'] < date('Y-m-d H:i:s', strtotime("+1 minutes", strtotime($app['datetime'])))){
                                // echo $app_data['biometric_id'] . " and " . $app['biometric_id']. " / ";
                                // echo $app_data['datetime'] . " and " . $app['datetime']. "<br>";
                                // var_dump($app_data['datetime'] > date('Y-m-d H:i:s', strtotime("+1 minutes", strtotime($app['datetime']))));
                                
                                // echo $this->check_merge_attendance($app_data['datetime'], $app_data['biometric_id']) ."<br>";
                                if($this->check_merge_attendance($app_data['datetime'], $app_data['biometric_id']) != true){
                                    $this->merge_attendance($app_data);
                                    $a['ins'] = 1;
                                    
                                }
                            }
                            $insert[] = $a;
                            
                        }
                    }else{
                        if($this->check_merge_attendance($app_data['datetime'], $app_data['biometric_id']) != true){
                            $this->merge_attendance($app_data);
                        }
                    }
                }
                return count($insert[0]);
            }else{
                return 0;
            }
            
        }


        public function syncAttendanceApp($dateFrom=null, $dateTo=null){
            $tempDateFrom = $dateFrom ? date("Y-m-d", strtotime($dateFrom)) : date("Y-m-d", strtotime("-1 day"));
            $tempDateTo = $dateTo ? date("Y-m-d", strtotime($dateTo)) : date("Y-m-d", strtotime("+1 day", strtotime($tempDateFrom)));
            if($tempDateFrom && $tempDateTo){
                $this->db->select("biometric_id, CONCAT(date,' ', time) as datetime,
                    IF(id > 0, 2, 2) as is_custom, IF(id > 0, 2, 2) as state,
                    CASE
                    WHEN `time_status` = 'in' THEN 0
                    WHEN `time_status` = 'out' THEN 1
                    WHEN `time_status` = 'overtime in' THEN 4
                    WHEN `time_status` = 'overtime out' THEN 5
                    ELSE 0 END as verify_method");
                $this->db->where("DATE(`date`) >=", $tempDateFrom);
                $this->db->where("DATE(`date`) <=", $tempDateTo);
                $this->db->group_by("biometric_id, date, time");
                $this->db->order_by("date, time", "ASC");
                $appAttRecord = $this->db->get("gcctimeutility.app_attendance");
                if($appAttRecord->num_rows() > 0){
                    foreach ($appAttRecord->result() as $att) {
                        $this->setAttendanceAppRecord($att);
                    }
                }
            }
        }

        protected function setAttendanceAppRecord($att = null){
            $resultResponse = false;
            if ($att->biometric_id) {
                $date = (new DateTime($att->datetime))->format('Y-m-d');
                $time = (new DateTime($att->datetime))->format('H:i');
                $maxPayrollDate = $this->getPayrollMaxDate($att->biometric_id);

                if ($maxPayrollDate !== false && strtotime($date) > strtotime($maxPayrollDate)) {
                    $this->db->where('biometric_id', $att->biometric_id);
                    $this->db->where('DATE(`datetime`)', $date);
                    $this->db->like('TIME(datetime)', $time, 'both');
                    $existingRecord = $this->db->get('gcctimeutility.attendance');

                    if ($existingRecord->num_rows() === 0) {
                        $resultResponse = $this->db->insert('gcctimeutility.attendance', [
                            'biometric_id' => $att->biometric_id,
                            'state' => $att->state,
                            'datetime' => $att->datetime,
                            'verify_method' => $att->verify_method,
                            'is_custom' => $att->is_custom,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
            return $resultResponse;
        }

        protected function getPayrollMaxDate($biometric_id=null){
            if($biometric_id){
                $this->db->select("MAX(ps.date_end) as max_date");
                $this->db->from("gccmaster.tblemployees emp");
                $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = emp.id AND ps.posted = 1", "LEFT");
                $this->db->where("emp.biometricno", $biometric_id);
                $this->db->group_by("emp.id");
                $qTemp = $this->db->get();
                if($qTemp->num_rows() == 1){ return $qTemp->row()->max_date; }
                else{ return false; }
            }else{ return false; }
            
        }

        private function merge_attendance($app_attendance){
            $this->db->set($app_attendance);
            $attendance = $this->db->insert('gcctimeutility.attendance');
        }

        private function check_merge_attendance($datetime, $biometric_id){
            $this->db->where("datetime", $datetime);
            $this->db->where("biometric_id", $biometric_id);
            $num = $this->db->get('gcctimeutility.attendance')->num_rows();
            if($num > 0){
                return true;
            }else{
                return false;
            }
        }

        function getAttendancePunchoutReport($currentDate=null){
            $arrData = array();
            if($currentDate){
                $skipTime = "18:00:00";
                $skipDateTime = date("Y-m-d H:i:s", strtotime($currentDate." ".$skipTime));
                $currentWeekDay = strtolower(date('l', strtotime($currentDate)));
                $this->db->from("gcctimeutility.personnel as a");
                $this->db->join("gcctimeutility.shift_schedule_resource as c", "c.shift_id = a.shift_id");
                $this->db->where("a.is_active", 1);
                $this->db->where("a.shift_id >", 0);
                $qTemp = $this->db->get();
                
                $arrDates = array();
                if($qTemp->num_rows() > 0){
                    foreach ($qTemp->result() as $key => $value) {
                        $shiftResource = @unserialize($value->shift_resource);
                        if(is_array($shiftResource) && count($shiftResource) > 0){
                            $this->db->from("gcctimeutility.shift_schedule_list");
                            $this->db->where("weekday", $currentWeekDay);
                            $this->db->where_in("id", $shiftResource);
                            $qSchedule = $this->db->get();
                            if($qSchedule->num_rows() == 1){
                                $row = $qSchedule->row();
                                if(intval(strtotime($row->am_end)) > 0 && intval(strtotime($row->pm_end))){
                                    $tempEndDate = date("Y-m-d H:i:s", strtotime($currentDate." ".$row->pm_end));
                                    if(!in_array($tempEndDate, $arrDates)){
                                        $arrDates[] = $tempEndDate;
                                    }
                                }
                            }
                        }
                    }
                }
                if(is_array($arrDates) && count($arrDates) > 0){
                    sort($arrDates);
                    foreach ($arrDates as $date) {
                        if($date !== $skipDateTime){
                            $nextDate = date("Y-m-d H:i:s", strtotime("+16 minutes", strtotime($date)));
                            $this->db->select("b.biometricno, UPPER(CONCAT(TRIM(b.lastname),
                            CASE WHEN TRIM(b.suffix) != 'N/A' AND TRIM(b.suffix) !='NONE' AND TRIM(b.suffix) !='' AND TRIM(b.suffix) IS NOT NULL THEN 
                                CONCAT(' ', TRIM(b.suffix)) ELSE ''  END, ', ',
                                TRIM(b.firstname), ' ', 
                            CASE WHEN TRIM(b.middlename) != 'N/A' AND TRIM(b.middlename) != 'NONE' AND TRIM(b.middlename) !='' AND TRIM(b.middlename) IS NOT NULL THEN 
                                CONCAT(TRIM(SUBSTR(b.middlename, 1, 1)), '.') ELSE '' END)) as employee_name,
                            UPPER(TRIM(IFNULL(d.description, b.department_id))) as department, a.datetime");
                            $this->db->from("gcctimeutility.attendance as a");
                            $this->db->join("gccmaster.tblemployees as b", "b.biometricno = a.biometric_id");
                            $this->db->join("gcctimeutility.personnel as c", "c.biometric_id = b.biometricno OR c.biometricno = b.biometricno");
                            $this->db->join("gcchris.tbldepartments as d", "d.id = b.department_id", "LEFT");
                            $this->db->group_start();
                            $this->db->where("a.datetime >=", $date);
                            $this->db->where("a.datetime <=", $nextDate);
                            $this->db->group_end();
                            $this->db->group_by("a.biometric_id");
                            $this->db->order_by("a.datetime", "ASC");
                            $qAttendance = $this->db->get();
                            if($qAttendance->num_rows() > 0){
                                foreach ($qAttendance->result() as $key => $entry) {
                                    $arrData[] = $entry;
                                }
                            }
                        }
                    }
                }
            }
            
            return $arrData;
        }

        function selectCompany()
        {
            $get = $this->input->get();
            $this->db->select("companies.id, companies.`code` `text`, companies.*");
            if(isset($get['q'])){
                $this->db->like("`code`", $get['q'], "BOTH");
            }
            $this->db->order_by("`code`", "ASC");
            $results = $this->db->get("gcchris.tblcompanies companies")->result();
            return array("results" => $results, "sql" => $this->db->last_query());
        }

        function getDepartmentCollection() {
            $data = array();
            $q = isset($_GET['q']) ? $_GET['q'] : "";

            $this->db->like("CONCAT(description, code)", $q, "both");
            $data['results'] = $this->db->get("gcchris.tbldepartments")->result();
            return $data;
        }
    }