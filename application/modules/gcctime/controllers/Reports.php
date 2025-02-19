<?php defined('BASEPATH') || exit('No direct script access allowed');

    class Reports extends MY_Controller {
        public function __construct() {
            parent::__construct();
            $this->load->library('email');
            $this->authenticate->setModuleAccess("time");
            $this->authenticate->doRedirect();
            $this->core_layout->setBodyClass("reports");
            $this->core_layout->setPrivilegeName("gcctime_reports");
            $this->load->model('Attendance_model', "attendance");
            $this->load->model('shift_management_model', 'shift_management');
            $this->load->model('biometric_model', 'adm_biometric');
            $this->load->model('Late_model', 'late_model');
            $this->load->model('Shift_management_model', 'shift_mgmt_model');
            $this->load->model('Reports_model', 'reports');
        }

        public function index() {
            $this->core_layout->setPrivilegeName("gcctime_reports");
            $this->core_layout->addJs("js/time/reports/reports.script.js", TRUE);

            $this->load->view('core/templates/header');
            $this->load->view('reports/index');
            $this->load->view('core/templates/footer');
        }

        function get_absentee_collection() {
            $post = $this->input->post();

            $resultset = array();
            $absentData = array();
            $data = array();

            if ($post) {
                $this->db->select("a.*, b.name");
                $this->db->from("gcctimeutility.absent a");
                $this->db->join("gcctimeutility.personnel b", "b.biometric_id = a.biometric_id", "left");
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $rs) {
                        $name = ($rs->name) ? $rs->name : $rs->biometric_id;

                        $cdate = $rs->updated_at;
                        $ampm = date("A", strtotime($cdate));

                        $ndate = explode(" ", $cdate);
                        if (count($ndate) == 2) {
                            $dbDate = date("Y-m-d", strtotime($ndate[0]));
                            $fromDate = date("Y-m-d", strtotime($post["from_date"]));
                            $toDate = date("Y-m-d", strtotime($post["to_date"]));

                            $_weekday = date("l", strtotime($dbDate));
                            $_weekday = strtolower($_weekday);

                            $hasAttendance = array();

                            if (($dbDate >= $fromDate) && ($dbDate <= $toDate) && ($_weekday !== "sunday")) {
                                $amAttendance = 0;
                                $pmAttendance = 0;
                                $this->db->from("gcctimeutility.attendance");
                                $this->db->where("biometric_id", $rs->biometric_id);
                                $this->db->like("datetime", $dbDate);

                                $queryAtts = $this->db->get();

                                /*** $dd = $this->attendance->getAttendanceByDateRange(array($dbDate, $dbDate), $rs->biometric_id); ***/
                                /*** for testing ***/

                                if ($queryAtts->num_rows() > 0) {
                                    foreach ($queryAtts->result() as $rs) {
                                        $ampmx = date("A", strtotime($rs->datetime));
                                        if ($ampm == $ampmx && $ampmx == "AM") {
                                            $amAttendance++;
                                        }
                                        if ($ampm == $ampmx && $ampmx == "PM") {
                                            $pmAttendance++;
                                        }
                                    }
                                }

                                /*** for testing ***/

                                if ($queryAtts->num_rows() == 0 && $queryAtts->num_rows() < 2) {
                                    $rs->current_date = $dbDate;
                                    $rs->attendance_count = $queryAtts->num_rows();

                                    $absentData[$rs->biometric_id]["name"] = $name;
                                    $absentData[$rs->biometric_id]["from_date"] = $fromDate;
                                    $absentData[$rs->biometric_id]["to_date"] = $toDate;
                                    $absentData[$rs->biometric_id]["attendance_count"] = $queryAtts->num_rows();
                                    $absentData[$rs->biometric_id][$dbDate][] = $rs->updated_at;
                                }
                            }
                        }
                    }
                }
            }

            if ($absentData) {
                foreach ($absentData as $key => $value) {
                    $wholeDay = 0;
                    $halfDay = 0;

                    $totalHD = 0;
                    $totalAbsent = 0;

                    $row = array();
                    $row["biometric_id"] = $key;
                    $row["name"] = $value["name"];
                    $row["from_date"] = $value["from_date"];
                    $row["to_date"] = $value["to_date"];

                    $dates = "";
                    foreach ($value as $kk => $vv) {
                        if (is_array($vv)) {
                            $color = "danger";
                            if (count($vv) >= 2) {
                                $color = "danger";
                                $wholeDay++;
                            }
                            if (count($vv) == 1) {
                                $color = "warning";
                                $halfDay++;
                            }
                            $dates .= "<span class='m-menu__link-badge m-custom-badge'><span class='m-badge m-badge--{$color} m-badge--wide'>{$kk}</span></span>";
                        }
                    }

                    if ($halfDay > 0) {
                        for ($i = 0; $i < $halfDay; $i++) {
                            $totalHD += 0.5;
                        }
                    }
                    $totalAbsent = floatval($wholeDay) + floatval($totalHD);


                    $row["dates"] = $dates;
                    $row["whole_day"] = $wholeDay;
                    $row["half_day"] = $totalHD;
                    $row["total_absent"] = $totalAbsent;
                    $data[] = $row;
                }
            }

            $resultset["data"] = $data;

            echo json_encode($resultset);
        }

        public function absent_template() {
            $this->load->view("attendance/getAbsentToday");
        }

        public function nte_template() {
            $data = array();
            $data["path_to_image"] = base_url("assets/img/logo.png");
            $data["employee_name"] = "No Assigned Employee Name";
            $data["set_date"] = date("F d, Y");
            $data["effectivity_date"] = date("F d, Y");
            $data["company_name"] = "GC&C Group of Companies";

            $this->load->view("templates/email/email-absent_nte_template", $data);
        }

        public function absent_template2() {
            /* $currentAbsent = $this->attendance->getCurrentAbsent(); */
            $currentAbsent = $this->shift_management->getCurrentAbsent();
            $data = (isset($currentAbsent["check_absent"]) && $currentAbsent["check_absent"]) ? $currentAbsent["check_absent"] : array();
            /*** $data = array(); ***/
            $this->load->view("templates/email/email-absent_template", array("data" => $data));
        }

        public function late_template() {
            $this->load->view("attendance/getLateToday");
        }

        public function late_template2() {
            /* $currentLate = $this->attendance->getCurrentLate(); */
            $currentLate = $this->shift_management->getCurrentLate();
            $state = (isset($currentLate["current_state"]) && $currentLate["current_state"]) ? $currentLate["current_state"] : "";
            $currentState = ($state) ? strtolower($state) : "";
            $currentState = "pm";
            /* var_dump($state);
            var_dump($currentState);
            var_dump($currentLate["check_late_pm"]); */

            $data = (isset($currentLate["checklate_{$currentState}"]) && $currentLate["checklate_{$currentState}"]) ? $currentLate["checklate_{$currentState}"] : array();

            $this->load->view("templates/email/email-late_template", array("data" => $data, "state" => $state));
        }

        public function lacking_template2() {
            $lackDoubleEntry = $this->attendance->getLackingDoubleEntries();
            $this->load->view("templates/email/email-lacking_template", $lackDoubleEntry);
        }

        public function lacking_template() {
            $this->load->view("attendance/getLackingEntries");
        }

        public function send_email2() {
            $currentAbsent = $this->attendance->getCurrentAbsent();
            $data = (isset($currentAbsent["check_absent"]) && $currentAbsent["check_absent"]) ? $currentAbsent["check_absent"] : array();

            $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.gmail.com',
                'smtp_port' => 465,
                'smtp_user' => 'gcceforms@gmail.com',
                'smtp_pass' => 'Sc0t2366',
                'newline' => "\r\n",
                'mailtype' => 'html',
                'charset' => 'utf-8',
            );

            $this->email->initialize($config);
            $this->email->from('gcceforms@gmail.com', 'GC&C eForms');
            $this->email->to('jp01@gccph.com');
            $this->email->cc('june1987paul@gmail.com, qmsassistant@gccph.com, busysanalyst@gccph.com, jp03@gccph.com');


            $message = "";
            $message .= $this->load->view("templates/email/email-absent_template", array("data" => $data), true);
            $this->email->subject('Absentee Report' . " - " . date("F d, Y A"));
            $this->email->message($message);
            $result = $this->email->send();
        }

        public function send_email() {
            $currentLate = $this->attendance->getCurrentLate();
            $state = (isset($currentLate["current_state"]) && $currentLate["current_state"]) ? $currentLate["current_state"] : "";

            $currentState = ($state) ? strtolower($state) : "am";
            $data = (isset($currentLate["checklate_{$currentState}"]) && $currentLate["checklate_{$currentState}"]) ? $currentLate["checklate_{$currentState}"] : array();

            $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.gmail.com',
                'smtp_port' => 465,
                'smtp_user' => 'gcceforms@gmail.com',
                'smtp_pass' => 'Sc0t2366',
                'newline' => "\r\n",
                'mailtype' => 'html',
                'charset' => 'utf-8',
            );

            $this->email->initialize($config);
            $this->email->from('gcceforms@gmail.com', 'GC&C eForms');
            $this->email->to('jp01@gccph.com');
            $this->email->cc('june1987paul@gmail.com, qmsassistant@gccph.com, busysanalyst@gccph.com, jp03@gccph.com');

            $message = "";
            $message .= $this->load->view("templates/email/email-late_template", array("data" => $data, "state" => $state), true);

            $this->email->subject('Late Report' . " - " . date("F d, Y"));
            $this->email->message($message);
            /* $this->email->send();  */
            $result = $this->email->send();

            /* echo $this->email->print_debugger(); */
        }

        function getLastSyncDate() {
            $last = $this->db->order_by('id', "desc")->limit(1)->get('event');
            if ($last->num_rows() > 0) {
                $row = $last->row();
                $dateCreated = date_create($row->created_at);
                return date_format($dateCreated, "Y-m-d");
            } else {
                return date("Y-m-d");
            }
        }

        function test_absentee_date() {
            $employeeRecords = array();
            $data = $this->shift_management->getPersonalAbsentByDate();
            if ($data) {
                $arrData = array();
                if (isset($data["absentee"]) && $data["absentee"]) {
                    foreach ($data["absentee"] as $key => $absentee) {
                        $currentDate = date("Ymd", strtotime($absentee["date"]));
                        $employee_name = trim($absentee["name"]);

                        $currentData = array();
                        $currentData["date"] = $absentee["date"];
                        $currentData["mrdn"] = $absentee["mrdn"];

                        $noLoaTo = (isset($absentee["content"]) && $absentee["content"] == "N/A") ? true : false;
                        if ($noLoaTo == true) {
                            if ($absentee["biometricno"]) {
                                $arrData[$absentee["biometricno"]]["biometricno"] = $absentee["biometricno"];
                                $arrData[$absentee["biometricno"]]["employee_name"] = $employee_name;
                                $arrData[$absentee["biometricno"]]["records"][$currentDate][] = $currentData;
                            }
                        }
                    }
                }

                if ($arrData) {
                    foreach ($arrData as $key => $personnel) {
                        $counter = 0;
                        $row = array();
                        $row["biometricno"] = $personnel["biometricno"];
                        $row["name"] = $personnel["employee_name"];
                        $actions = "";
                        if (isset($personnel["records"]) && $personnel["records"]) {
                            foreach ($personnel["records"] as $kk => $vv) {
                                $ccc = trim($kk);
                                $ddd = date("Y-m-d", strtotime($kk));

                                $query = $this->db->get_where("gcctimeutility.absent_nte", array("biometric_id" => $personnel["biometricno"], "coded_date" => $ccc, "is_released" => 1));
                                $noNteRecord = ($query->num_rows() == 0) ? true : false;

                                if ($noNteRecord) {
                                    if (count($vv) == 2) {
                                        $actions .= "<button type='button' class='btn-danger btn btn-sm' data-key='{$kk}' onclick='getNteRecord({$ccc},{$personnel['biometricno']})'>{$ddd}</button> ";
                                    }
                                    if (count($vv) == 1) {
                                        $actions .= "<button type='button' class='btn-warning btn btn-sm' data-key='{$kk}' onclick='getNteRecord({$ccc},{$personnel['biometricno']})'>{$ddd}</button> ";
                                    }
                                } else {
                                    $actions .= "<button type='button' class='btn-primary btn btn-sm' data-key='{$kk}' onclick='getNteRecordReleased({$ccc},{$personnel['biometricno']})'>{$ddd}</button> ";
                                }
                                $counter++;
                            }
                        }

                        $row["dates"] = $actions;
                        $row["count"] = $counter;
                        $employeeRecords[] = $row;
                    }
                }
            }

            $resultset = array();
            $resultset["data"] = $employeeRecords;
            echo json_encode($resultset);
        }

        function get_absentee_data() {
            $session = $this->core_layout->getCurrentSession();
            $post = $this->input->post();
            $resultset = array();
            if ($post) {
                $startDate = date("Y-m-d", strtotime($post["code"]));
                $endDate = date("Y-m-d", strtotime("+1 day", strtotime($post["code"])));
                $biometricno = $post["biometricno"];

                $data = $this->shift_management->getPersonalAbsentByDate($startDate, $endDate, $biometricno);
                if (isset($data["absentee"]) && $data["absentee"]) {
                    $datax = array();
                    foreach ($data["absentee"] as $absentee) {
                        $currentDate = date("Ymd", strtotime($absentee["date"]));
                        $biometricno = $absentee["biometricno"];
                        $datax["date"] = $currentDate;
                        $datax["biometricno"] = $biometricno;
                        $datax["employee_name"] = $absentee["name"];
                    }

                    $resultset["response"] = true;
                    $resultset["html"] = $this->load->view("attendance/modals/nte", $datax, true);
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            echo json_encode($resultset);
        }

        function set_absentee_data() {
            $post = $this->input->post();
            $session = $this->core_layout->getCurrentSession();
            $resultset = array();

            if ($post) {
                $data = array();
                $data["biometric_id"] = $post["biometricno"];
                $data["coded_date"] = $post["code"];
                $data["is_released"] = 1;
                $data["released_by"] = $session["id"];

                $inserted = $this->db->insert("gcctimeutility.absent_nte", $data);
                if ($inserted) {
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            echo json_encode($resultset);
        }

        function test() {
            echo "<pre>";
            $late = $this->shift_management->getCurrentLateTest();
            var_dump($late);
        }

        function test_absentee() {
            echo "<pre>";
            $absentee = $this->shift_management->getCurrentAbsentV2();
            var_dump($absentee);
        }

        function test_biometric() {
            echo "<pre>";
            $data = $this->adm_biometric->getBiometricDataTest(false);
            var_dump($data);
        }

        function get_undertime() {
            $currentUndertime = $this->shift_management->getCurrentUndertime();
            $data = array();
            $data["am"] = (isset($currentUndertime["check_undertime_am"]) && $currentUndertime["check_undertime_am"]) ? $currentUndertime["check_undertime_am"] : array();
            $data["pm"] = (isset($currentUndertime["check_undertime_pm"]) && $currentUndertime["check_undertime_pm"]) ? $currentUndertime["check_undertime_pm"] : array();

            $arrData = array();
            $arrData["data"] = $data;

            $this->load->view("templates/email/email-undertime_template", array("data" => $data));
        }

        function absentee_collection() {
            echo "<pre>";
            $absenteeId = array();
            $personnels = $this->getActivePersonnels();
            if ($personnels) {
                foreach ($personnels as $personnel) {
                    $biometric_id = $personnel->biometric_id;
                    $data = $this->getActiveAttendance($biometric_id);
                    if ($data) {

                        /* var_dump($personnel->name);
                        var_dump($data); */
                    } else {
                        $absenteeId[] = $biometric_id;
                    }
                }
            }
            var_dump($absenteeId);
        }

        function getActiveAttendance($biometric_id = null, $currentDate = null) {
            if ($biometric_id) {
                $ccdate = ($currentDate) ? date("Y-m-d", strtotime($currentDate)) : date("Y-m-d");
                $this->db->from("gcctimeutility.attendance");
                $this->db->where("biometric_id", $biometric_id);
                $this->db->like("datetime", $ccdate, "both");
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $result = $query->result();
                    return $result;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        private function getActivePersonnels() {
            $query = $this->db->get_where("gcctimeutility.personnel", array("is_active" => 1));
            if ($query->num_rows() > 0) {
                return $query->result();
            } else {
                return false;
            }
        }

        public function get_stored_data($date = null, $dateFilter = null) {
            if ($date) {
                $date = date("Y-m-d", strtotime($date));
                var_dump($date);
                $dateFilter = ($dateFilter) ? date("Y-m-d", strtotime($dateFilter)) : null;

                $result = $this->adm_biometric->manualSetBiometricData($date, $dateFilter);
                echo "<pre>";
                var_dump($result);
            }
        }

        public function test_biometric_device() {
            echo "<pre>";
            $ip = "192.168.7.15";
            $isReachable = $this->adm_biometric->ipIsReachable($ip, 80);
            var_dump($isReachable);

            $url = "{$ip}:80";
            $ch = curl_init($url);
            /* curl_setopt($ch, CURLOPT_PORT, $port); */
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            var_dump($httpcode);

            if ($httpcode >= 200 && $httpcode < 300) {

                return true;
            } else {
                return false;
            }
        }

        function undertime_test() {
            $data = $this->shift_management->getCurrentUndertimeV2();
            echo "<pre>";
            var_dump($data);
        }

        function get_late_report() {
            $post = $this->arrayToStdClass($this->input->post());
            $start_date = $post->startDate;
            $end_date = $post->endDate;
            $department = isset($post->department) ? $post->department : null;
            $company = isset($post->company) ? $post->company : null;

            echo json_encode($this->late_model->getLateReport($start_date, $end_date, $department, $company));
        }

        function get_absentee_report() {
            $post = $this->arrayToStdClass($this->input->post());
            $start_date = $post->startDate;
            $end_date = $post->endDate;
            $department = isset($post->department) ? $post->department : null;
            $company = isset($post->company) ? $post->company : null;

            echo json_encode($this->late_model->getAbsenteeReport($start_date, $end_date, $department, $company));
        }

        private function arrayToStdClass($array) {
            return json_decode(json_encode($array));
        }

        public function test2() {
            $x = $this->shift_mgmt_model->emailAbsentNotification();
            echo json_encode($x);
        }
        
        function get_company_collection(){
            $data = $this->attendance->selectCompany();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_dept_collection(){
            $data = $this->attendance->getDepartmentCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function attendance_logs() {
            $arrData = array();

            $arrData['devices'] = $this->reports->getActiveDevices();

            $this->core_layout->setPrivilegeName("gcctime_logs");
            $this->core_layout->addJs("js/time/reports/attendance.script.js", TRUE, $arrData);

            $this->load->view('core/templates/header');
            $this->load->view('reports/attendance_logs');
            $this->load->view('core/templates/footer');
        } 

        function get_attendance_logs_datatable_request() {
            $data = $this->reports->get_attendance_logs_datatable_request();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
    }
