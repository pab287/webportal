<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Attendance extends MY_Controller {
        private $devicesTable = "gcctimeutility.devices";
        private $attendanceTable = "gcctimeutility.attendance";
        private $personnelTable = "gcctimeutility.personnel";
        private $eventTable = "gcctimeutility.event";

        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("time");
            $this->authenticate->doRedirect();
            $this->load->model('Attendance_model', "attendance");
            $this->load->model('shift_management_model', 'shift_management');
            $this->load->model('core/upload_model', 'adm_upload');
            $this->core_layout->setPrivilegeName("gcctime_attendance");
            $this->load->library('email');
            $this->load->library('session');


            $this->core_layout->addJs("plugins/moment_js/moment.min.js");
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");

            date_default_timezone_set("Asia/Taipei");
        }

        public function index() {
            $this->core_layout->setPrivilegeName("gcctime_attendance");

            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");

            $arrData = array();
            $arrData["device"] = $this->getDevice();
            $arrData["assigned_device"] = $this->getAssignedDevice();
            $this->load->view('core/templates/header');
            $this->load->view('attendance/index', $arrData);
            $this->load->view('core/templates/footer');
        }

        public function mobile_attendance() {
            $this->core_layout->setPrivilegeName("gcctime_attendance_mobile");
            $arrData = array("companies" => $this->attendance->select2CompanyData());
            $this->core_layout->addExternalJs("https://maps.googleapis.com/maps/api/js?key=" . $_ENV['PROD_MAP_KEY']."&libraries=geometry,marker&loading=async", true);
            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
            $this->core_layout->addJs("js/time/reports/mobile_attendance.script.js", true, $arrData);
            $this->load->view('core/templates/header');
            $this->load->view('attendance/mobile_attendance');
            $this->load->view('core/templates/footer');
        }

        function upload_attendance_file() {
            $resultset = array();

            $uploadPath = realpath('./uploads/files/csv/');
            if(!file_exists($uploadPath)){
                $mkdir = mkdir($uploadPath, 0777, true);
            }

            $config = array();
            $config['upload_path'] = './uploads/files/csv/';
            $config['allowed_types'] = 'csv';
            $config['max_size'] = 1000000;

            $session = $this->core_layout->getCurrentSession();
            $resultset = $this->adm_upload->uploadFile($config);
            if (isset($resultset["response"]) && $resultset["response"]) {
                $file = (isset($resultset["files"][0]["file_name"]) && $resultset["files"][0]["file_name"]) ? $resultset["files"][0]["file_name"] : "";
                if ($file) {
                    $fileExist = realpath("uploads/files/csv/{$file}");
                    if ($fh = fopen($fileExist, 'r')) {
                        $addedCount = 0;
                        $existCount = 0;
                        $count = 0;
                        while (!feof($fh) && $row = fgetcsv($fh)) {
                            $queryPersonnel = $this->db->get_where($this->personnelTable, array("biometric_id" => trim($row[0])));
                            $name = trim($row[0]);

                            if ($queryPersonnel->num_rows() == 1) {
                                $qrow = $queryPersonnel->row();
                                $name = strtoupper($qrow->name);
                            }

                            $rowData = array();
                            $rowData["id"] = trim($row[0]);
                            $rowData["name"] = $name;
                            $rowData["datetime"] = date("Y-m-d H:i:s", strtotime(trim($row[1])));

                            $resultset["uploaded_data"][] = $rowData;
                        }
                    }
                }
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($resultset));
        }

        function upload_attendance() {
            $post = $this->input->post();
            $resultset = array();

            if (isset($post["full_path"]) && $post["full_path"]) {
                $added_attendance = 0;
                $failed_attendance = 0;

                $file = fopen($post["full_path"], "r");
                $c = 0;
                while (!feof($file) && $row = fgetcsv($file)) {
                    if ($c != 0) {
                        $rowData = array();
                        $biometric_id = trim($row[0]);
                        $datetime = date("Y-m-d H:i:s", strtotime(trim($row[1])));

                        $ccdate = date("Y-m-d H:i", strtotime($datetime));

                        $where = array();
                        $where["biometric_id"] = $biometric_id;

                        $this->db->from($this->attendanceTable);
                        $this->db->where($where);
                        $this->db->like("datetime", $ccdate);

                        $query = $this->db->get();

                        if ($query->num_rows() == 0) {
                            $data = array();
                            $data["datetime"] = $datetime;
                            $data["biometric_id"] = $biometric_id;
                            $data["device_id"] = (isset($post["device_id"]) && $post["device_id"]) ? intval($post["device_id"]) : 0;
                            $data["state"] = 1;

                            $added = $this->db->insert($this->attendanceTable, $data);
                            if ($added) {
                                $added_attendance++;
                            }
                        } else {
                            $failed_attendance++;
                        }
                    }

                    $c++;
                }

                if ($added_attendance > 0) {
                    if (isset($post["device_id"]) && $post["device_id"]) {
                        $queryDevice = $this->db->get_where($this->devicesTable, array("id" => $post["device_id"]));
                        if ($queryDevice->num_rows() == 1) {
                            $row = $queryDevice->row();
                            $this->db->insert($this->eventTable, array("event_name" => "sync_data", "location_id" => $row->location_id));
                        }
                    }

                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Attendance total upload of {$added_attendance} record(s), failed attendance upload of {$failed_attendance} record(s).";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed attendance upload of {$failed_attendance} record(s).";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data, empty file upload!";
            }

            echo json_encode($resultset);
        }

        function getAssignedDevice() {
            $resultset = array();
            $arrData = array();

            $this->db->select("id, device_name");
            $query = $this->db->get($this->devicesTable);
            if ($query->num_rows() > 0) {
                $arrData = $query->result();
            }
            $resultset["devices"] = $arrData;

            return $resultset;
        }

        public function personal_record() {
            $this->load->view('core/templates/header');
            $this->load->view('attendance/record');
            $this->load->view('core/templates/footer');
        }

        public function nte_record() {
            $this->core_layout->setBodyClass("attendance nte_record");
            $this->core_layout->setPrivilegeName("gcctime_attendance_nte");

            $this->load->view('core/templates/header');
            $this->load->view('attendance/nte_record/index');
            $this->load->view('core/templates/footer');
        }

        private function getUserdata() {
            $userdata = $this->session->userdata("logged_in");
            return isset($userdata) ? $userdata : FALSE;
        }

        function get_current_attendance() {
            $resultset = array();
            $attendance = $this->attendance->getCurrentAttendance();
            $actions = $this->core_layout->getCurrentActions();

            if ($attendance) {
                $arrData = array();
                foreach ($attendance as $rs) {
                    $biometric_id = $rs["biometric_id"];
                    $datetime = $rs["datetime"];
                    $deviceName = $rs["state"] == 2 && $rs["is_custom"] == 2 ? "Gcctime Application" : $rs["device_name"];
                    $highlight = "";
                    $isLate = $this->shift_management->getSingleAttendanceLate($biometric_id, $datetime);
                    $isUndertime = $this->shift_management->getSingleAttendanceUndertime($biometric_id, $datetime);
                    $isDoubleEntry = $this->shift_management->getSingleAttendanceDoubleEntry($biometric_id, $datetime);

                    if ($highlight == "" && $isLate) {
                        $highlight = "late";
                    }

                    if ($highlight == "" && $isUndertime) {
                        $highlight = "undertime";
                    }

                    if ($highlight == "" && $isDoubleEntry) {
                        $highlight = "double_entry";
                    }

                    $row = array();
                    $row[] = $rs["biometric_id"];

                    $cDate = "---";
                    $cTime = "---";

                    $currentDateTime = $rs["datetime"];
                    if ($currentDateTime) {
                        $splitDateTime = explode(" ", $currentDateTime);
                        if (count($splitDateTime) == 2) {
                            $cDate = $splitDateTime[0];
                            $cTime = $splitDateTime[1];
                        }
                    }

                    $row[] = $cDate;
                    $row[] = $cTime;

                    $row[] = $rs["name"];
                    $row[] = $deviceName;

                    $textColor = '';

                    switch ($highlight):
                        case 'late':
                            $textColor = 'text-white';
                            break;
                        case 'undertime':
                            $textColor = 'text-white';
                            break;
                        case 'double_entry':
                            $textColor = 'text-white';
                            break;
                        default:
                            $textColor = '';
                            break;
                    endswitch;

                    $_actions = '';
                    if (in_array('edit', $actions)) {
                        $_actions .= '<a href="javascript:void(0);" ';
                        /* $_actions .= 'data-id="'.$rs["id"].'" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btn-modal-edit btn-sm" title="View ">'; */
                        $_actions .= 'data-id="' . $rs["id"] . '" class="btn btn-accent m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air btn-modal-edit btn-sm" data-toggle="tooltip" title="Edit Attendance">';
                        $_actions .= '<i class="la la-edit ' . $textColor . '"></i></a>';
                    }

                    $row[] = $_actions;
                    $row[] = $highlight;
                    $arrData[] = $row;
                }
                $resultset["data"] = $arrData;
            } else {
                $resultset["data"] = array();
            }

            echo json_encode($resultset);
        }

        function getattendance() {
            $actions = $this->core_layout->getCurrentActions();
            $getAttendanceCollection = $this->crud->getCollection(array(), "gcctimeutility.attendance");
            $resultarray = array();
            if ($getAttendanceCollection) {
                foreach ($getAttendanceCollection as $_getAttendanceCollection) {
                    $getPersonnel = $this->crud->load(array("biometric_id" => $_getAttendanceCollection["biometric_id"]), "gcctimeutility.personnel");
                    $getDevice = $this->crud->load(array("id" => $_getAttendanceCollection["device_id"]), "gcctimeutility.devices");
                    $data = array();
                    $data[] = $_getAttendanceCollection["biometric_id"];
                    $data[] = $getPersonnel["name"];
                    $data[] = $getDevice["device_name"];

                    $cDate = "---";
                    $cTime = "---";

                    $currentDateTime = $_getAttendanceCollection["datetime"];
                    if ($currentDateTime) {
                        $splitDateTime = explode(" ", $currentDateTime);
                        if (count($splitDateTime) == 2) {
                            $cDate = $splitDateTime[0];
                            $cTime = $splitDateTime[1];
                        }
                    }

                    $data[] = $cDate;
                    $data[] = $cTime;

                    $_actions = '';
                    if (in_array('edit', $actions)) {
                        $_actions .= "<span style='overflow: visible; width: 110px;'>";
                        $_actions .= "<a href='javascript:void(0);' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btn-edit-attendance data-id='" . $_getAttendanceCollection["id"] . "'. title='Edit'>";
                        $_actions .= "<i class='la la-edit'></i></a></span>";
                    }

                    $data[] = $_actions;

                    $resultarray[] = $data;
                }
            }

            echo json_encode(array("data" => $resultarray));
        }

        private function getDevice() {
            $query = $this->crud->load(array("status" => 1), "gcctimeutility.devices");

            return $query;
        }

        public function syncdata() {
            $response = $this->attendance->doSyncData();
        }

        public function syncdata_old012219() {
            $_weekday = date("l", strtotime($this->today));
            $_weekday = strtolower($_weekday);

            if ($_weekday !== "sunday") {
                $device = $this->getDevice();
                include BASEPATH . '\libraries\zklibrary.php';

                $zk = new ZKLibrary($device["ip_address"], 4370);

                $zk->connect();
                $zk->disableDevice();
                $attendance = $zk->getAttendance();
                $zk->enableDevice();
                $zk->disconnect();
                $this->log_event("sync_data");

                if ($attendance) {
                    $nData = array();
                    $currentDate = Date("Y-m-d");

                    foreach ($attendance as $atts) {
                        $loggedDate = Date("Y-m-d", strtotime($atts[3]));
                        if ($currentDate == $loggedDate) {
                            $nData[] = $atts;
                        }
                    }

                    if ($nData) {
                        foreach ($nData as $data) {
                            $biometric_id = $data[1];
                            $state = $data[2];
                            $datetime = $data[3];

                            if (!$this->checkAttendanceData($biometric_id, $datetime)) {
                                $this->db->insert("gcctimeutility.attendance", array("biometric_id" => $data[1], "state" => $data[2], "datetime" => $data[3]));
                            }
                        }
                    }
                }
            }
        }

        public function searchrange() {
            $post = $this->input->post();
            $resultarray = array();
            $dates = explode(" - ", $post["daterange"]);

            $getAttendanceByDateRange = $this->attendance->getAttendanceByDateRange($dates, $post["biometric_id"]);


            if ($getAttendanceByDateRange->num_rows() > 0) {
                foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                    $data = array();
                    $getPersonnel = $this->crud->load(array("biometric_id" => $_getAttendanceByDateRange["biometric_id"]), "gcctimeutility.personnel");
                    $data[] = $_getAttendanceByDateRange["biometric_id"];
                    $data[] = $getPersonnel["name"];
                    $data[] = $_getAttendanceByDateRange["state"];
                    $data[] = $_getAttendanceByDateRange["datetime"];

                    $resultarray[] = $data;

                }
            }

            echo json_encode($resultarray);

        }

        private function checkAttendanceData($biometric_id = NULL, $datetime = NULL) {
            $query = $this->crud->load(array("biometric_id" => $biometric_id, "datetime" => $datetime), "gcctimeutility.attendance");
            return $query ? TRUE : FALSE;
        }

        public function searchrangetest() {
            $actions = $this->core_layout->getCurrentActions();
            $resultarray = array();
            $post = $this->input->post();
            $dates = explode(" - ", $post["daterange"]);

            $begin = new DateTime($dates[0]);
            $end = new DateTime($dates[1]);
            $end = $end->modify('+1 day');

            $interval = new DateInterval('P1D');
            $period = new DatePeriod($begin, $interval, $end);

            foreach ($period as $key => $value) {
                //$date = $value->format('Y-m-d H:i:s');
                $date = new DateTime($value->format("Y-m-d H:i:s"));
                $datgd = $date->format('Y-m-d H:i:s');
                $newdate = $date->modify('+1 day');
                $newdate = $newdate->format('Y-m-d H:i:s');
                $newtime = new DateTime($newdate);
                $newtime = $newtime->modify("-1 second");
                $newtime = $newtime->format('Y-m-d H:i:s');

                //$biometric_id = NULL;
                if (isset($post["biometric_id"]) && $post["biometric_id"]) {
                    $biometric_id = $post["biometric_id"];
                } else {
                    $biometric_id = NULL;
                }

                //var_dump($biometric_id);

                $dates = array($datgd, $newtime);

                $query = $this->attendance->getAttendanceByDateRange($dates, $biometric_id);

                /*** copy this code ***/
                $arrData = $query->result_array();
                $biometric_id = array_column($arrData, "biometric_id");
                $datetime = array_column($arrData, "datetime");

                array_multisort($biometric_id, SORT_ASC, $datetime, SORT_ASC, $arrData);

                if (count($arrData) > 0) {
                    foreach ($arrData as $_arrData) {

                        $data = array();
                        $getPersonnel = $this->crud->load(array("biometric_id" => $_arrData["biometric_id"]), "gcctimeutility.personnel");

                        $biometric_id = $_arrData["biometric_id"];
                        $datetime = $_arrData["datetime"];

                        $highlight = "";
                        $isLate = $this->shift_management->getSingleAttendanceLate($biometric_id, $datetime);
                        $isUndertime = $this->shift_management->getSingleAttendanceUndertime($biometric_id, $datetime);
                        $isDoubleEntry = $this->shift_management->getSingleAttendanceDoubleEntry($biometric_id, $datetime);

                        if ($highlight == "" && $isLate) {
                            $highlight = "late";
                        }

                        if ($highlight == "" && $isUndertime) {
                            $highlight = "undertime";
                        }

                        if ($highlight == "" && $isDoubleEntry) {
                            $highlight = "double_entry";
                        }

                        $textColor = '';
                        switch ($highlight):
                            case 'late':
                                $textColor = 'text-white';
                                break;
                            case 'undertime':
                                $textColor = 'text-white';
                                break;
                            case 'double_entry':
                                $textColor = 'text-white';
                                break;
                            default:
                                $textColor = '';
                                break;
                        endswitch;

                        $data[] = $_arrData["biometric_id"];

                        $cDate = "---";
                        $cTime = "---";

                        $currentDateTime = $_arrData["datetime"];
                        if ($currentDateTime) {
                            $splitDateTime = explode(" ", $currentDateTime);
                            if (count($splitDateTime) == 2) {
                                $cDate = $splitDateTime[0];
                                $cTime = $splitDateTime[1];
                            }
                        }

                        $data[] = $cDate;
                        $data[] = $cTime;

                        $data[] = $getPersonnel["name"];
                        $data[] = (isset($_arrData["device_name"]) && $_arrData["device_name"]) ? $_arrData["device_name"] : "";


                        $_actions = '';
                        if (in_array('edit', $actions)) {
                            $_actions .= '<a href="javascript:void(0);" data-id="' . $_arrData["id"] . '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btn-modal-edit" title="View ">';
                            $_actions .= '<i class="la la-edit ' . $textColor . '"></i></a>';
                        }

                        $data[] = $_actions;
                        $data[] = $highlight;

                        $resultarray[] = $data;
                    }
                }
            }

            echo json_encode($resultarray);
        }

        function checkLate($biometric_id = NULL, $date = NULL) {
            $resultarray = array();
            $getPersonnel = $this->crud->load(array("biometric_id" => $biometric_id), "gcctimeutility.personnel");
            $getDepartment = $this->crud->load(array("id" => $getPersonnel["department_id"]), "gcctimeutility.department");
            $getShift = $this->crud->load(array("id" => $getDepartment["shift_id"]), "gcctimeutility.shifts");

            $now = date_format(date_create($date), "H:i");

            $am_start = date_format(date_create($getShift["am_start"]), "H:i");
            $am_end = date_format(date_create($getShift["am_end"]), "H:i");

            $pm_start = date_format(date_create($getShift["pm_start"]), "H:i");
            $pm_end = date_format(date_create($getShift["pm_end"]), "H:i");

            $shift = "";
            if ($am_start > $am_end) {
                if ($now >= $am_start || $now < $am_end) {
                    $shift = "shift";
                }
            } else if ($now >= $am_start && $now <= $am_end) {
                $shift = "shift";
            }

            if ($pm_start > $pm_end) {
                if ($now >= $pm_start || $now < $pm_end) {
                    $shift = "shift";
                }
            } else if ($now >= $pm_start && $now <= $pm_end) {
                $shift = "shift";
            }

            return $shift;

        }

        function checkUndertime($biometric_id = NULL, $date = NULL) {
            $resultarray = array();
            $getPersonnel = $this->crud->load(array("biometric_id" => $biometric_id), "gcctimeutility.personnel");
            $getDepartment = $this->crud->load(array("id" => $getPersonnel["department_id"]), "gcctimeutility.department");
            $getUndertime = $this->crud->load(array("id" => $getDepartment["undertime_id"]), "gcctimeutility.undertime");

            $now = date_format(date_create($date), "H:i");

            $am_start = date_format(date_create($getUndertime["am_start"]), "H:i");
            $am_end = date_format(date_create($getUndertime["am_end"]), "H:i");

            $pm_start = date_format(date_create($getUndertime["pm_start"]), "H:i");
            $pm_end = date_format(date_create($getUndertime["pm_end"]), "H:i");

            $undertime = "";
            if ($am_start > $am_end) {
                if ($now >= $am_start || $now < $am_end) {
                    $undertime = "undertime";
                }
            } else if ($now >= $am_start && $now <= $am_end) {
                $undertime = "undertime";
            }

            if ($pm_start > $pm_end) {
                if ($now >= $pm_start || $now < $pm_end) {
                    $undertime = "undertime";
                }
            } else if ($now >= $pm_start && $now <= $pm_end) {
                $undertime = "undertime";
            }

            return $undertime;

        }

        function checkDoubleEntry($biometric_id = NULL, $date = NULL) {
            $this->db->from("gcctimeutility.attendance");
            $this->db->where("biometric_id", $biometric_id);
            $this->db->like("datetime", $date, "booth");
            $query = $this->db->get();
            if ($query->num_rows() > 1) {
                return true;
            } else {
                return false;
            }
        }

        public function shift_modal() {
            $this->load->view("attendance/modals/shifts");
        }

        public function updatelatesettings() {
            $post = $this->input->post();

            var_dump($post);
        }

        public function departments_modal() {
            $resultarray = array();
            $content = "";
            $content .= $this->load->view("attendance/modals/departments", NULL, TRUE);

            echo json_encode(array("data" => $content));
        }

        public function adddepartment() {
            $post = $this->input->post();
            $post["status"] = 1;
            $resultarray = array();
            $query = $this->crud->insert($post, "gcctimeutility.department");

            if ($query) {
                $resultarray["status"] = TRUE;
                $resultarray["message"] = "Department successfully saved!";
            } else {
                $resultarray["status"] = False;
                $resultarray["message"] = "Error processing data!";
            }

            echo json_encode($resultarray);
        }

        public function getPersonnelToday($biometric_id = null) {
            $content = "";
            $content .= "<div style='padding: 0 0 5px 0 !important; margin: 0 auto;' class='m-list-timeline__item'>";
            $content .= "<span class='m-list-timeline__text text-left'><strong>DATE</strong></span>";
            $content .= "<span class='m-list-timeline__text text-right'><strong>TIME</strong></span>";
            $content .= "<span class='m-list-timeline__text text-right' style='padding: 0;'><strong>DURATION</strong></span></div>";

            $late = $this->shift_management->getPersonalLate($biometric_id);
            if ($late) {
                if (isset($late["lates"]) && $late["lates"]) {
                    $content .= "<div class='m-list-items-data_item'>";
                    foreach ($late["lates"] as $lates) {
                        $date = date("F d, Y", strtotime($lates["date"]));
                        $time = $lates["time"];
                        $duration = $lates["minlate"];

                        $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>";
                        $content .= "<span class='m-list-timeline__text'><small>{$date}</small></span>";
                        $content .= "<span class='m-list-timeline__text text-right'><small>{$time}</small></span>";
                        $content .= "<span class='m-list-timeline__text text-right'><small>{$duration}</small></span>";
                        $content .= "</div>";
                    }
                    $content .= "</div>";
                }
            }

            echo $content;
        }

        public function testdata() {
            $id = "547";
            $date = date("Y-m-d", strtotime("2019-03-21"));
            $date0 = date("Y-m-d H:i", strtotime($date));
            $date1 = date("Y-m-d H:i", strtotime("+1 day", strtotime($date)));
            $date1 = date("Y-m-d H:i", strtotime("-1 minute", strtotime($date1)));

            $this->db->from("gcceforms.loa");
            $this->db->where("employee", $id);
            $this->db->where("date_from >=", $date0);
            $this->db->where("date_to <=", $date1);
            $query = $this->db->get();

            var_dump($query->row());
            var_dump($date0);
            var_dump($date1);
        }

        public function getPersonnelAbsent($biometric_id = null) {
            $content = "";
            $content .= "<div style='padding: 0 0 5px 0 !important; margin: 0 auto;' class='m-list-timeline__item'>";
            $content .= "<span class='m-list-timeline__text'><strong>DATE</strong></span>";
            $content .= "<span class='m-list-timeline__text text-right' style='width: 70%;'><strong>REFERENCE #</strong></span>";
            $content .= "<span class='m-list-timeline__text text-right' style='padding: 0;'><strong>AM/PM</strong></span></div>";

            $absent = $this->shift_management->getPersonalAbsent($biometric_id);
            if ($absent) {
                if (isset($absent["absentee"]) && $absent["absentee"]) {
                    $content .= "<div class='m-list-items-data_item'>";
                    foreach ($absent["absentee"] as $absentee) {
                        $date = date("F d, Y", strtotime($absentee["date"]));
                        $time = (isset($absentee["content"]) && $absentee["content"]) ? $absentee["content"] : "---";
                        $duration = $absentee["mrdn"];

                        $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>";
                        $content .= "<span class='m-list-timeline__text'><small>{$date}</small></span>";
                        $content .= "<span class='m-list-timeline__text text-right' style='width: 70%;'><small>{$time}</small></span>";
                        $content .= "<span class='m-list-timeline__text text-right'><small>{$duration}</small></span>";
                        $content .= "</div>";
                    }
                    $content .= "</div>";
                }
            }

            echo $content;
        }

        public function getLateToday() {
            $content = "";
            $content .= "<div style='padding: 0 0 5px 0 !important; margin: 0 auto;' class='m-list-timeline__item'>";
            $content .= "<span class='m-list-timeline__text text-left'><strong>EMPLOYEE NAME</strong></span>";
            $content .= "<span class='m-list-timeline__time text-right' style='width: 25%;'><strong>TIME</strong></span>";
            $content .= "<span class='m-list-timeline__time text-right' style='width: 25%; padding: 0 5px 0 0;'><strong>DATE</strong></span></div>";

            $late = $this->shift_management->getCurrentLate();
            if ($late) {
                $lateList = array();
                if (isset($late["checklate_pm"]) && $late["checklate_pm"]) {
                    $content .= "<div class='m-list-items-data_item'>";

                    foreach ($late["checklate_pm"] as $late_pm) {
                        $name = strtoupper($late_pm["name"]);
                        $date = date("Y-m-d", strtotime($late_pm["date"]));
                        $time = $late_pm["time"];
                        $state = (isset($late_am["state"]) && $late_am["state"]) ? $late_am["state"] : "normal";

                        $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>";
                        if ($state == "exceed") {
                            $content .= "<span class='m-list-timeline__text'><small><strong>{$name}</strong></small></span>";
                            $content .= "<span class='m-list-timeline__text text-right' style='width: 25%;'><small><strong>{$time}</strong></small></span>";
                            $content .= "<span class='m-list-timeline__text text-right' style='width: 25%;'><small><strong>{$date}</strong></small></span>";
                        } else {
                            $content .= "<span class='m-list-timeline__text'><small>{$name}</small></span>";
                            $content .= "<span class='m-list-timeline__text text-right' style='width: 25%;'><small>{$time}</small></span>";
                            $content .= "<span class='m-list-timeline__text text-right' style='width: 25%;'><small>{$date}</small></span>";
                        }
                        $content .= "</div>";
                    }
                    $content .= "</div>";
                }

                if (isset($late["checklate_am"]) && $late["checklate_am"]) {
                    $content .= "<div class='m-list-items-data_item-am'>";

                    foreach ($late["checklate_am"] as $late_am) {
                        $name = strtoupper($late_am["name"]);
                        $date = date("Y-m-d", strtotime($late_am["date"]));
                        $time = $late_am["time"];
                        $state = (isset($late_am["state"]) && $late_am["state"]) ? $late_am["state"] : "normal";

                        $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>";
                        if ($state == "exceed") {
                            $content .= "<span class='m-list-timeline__text'><small><strong>{$name}</strong></small></span>";
                            $content .= "<span class='m-list-timeline__text text-right' style='width: 25%;'><small><strong>{$time}</strong></small></span>";
                            $content .= "<span class='m-list-timeline__text text-right' style='width: 25%;'><small><strong>{$date}</strong></small></span>";
                        } else {
                            $content .= "<span class='m-list-timeline__text'><small>{$name}</small></span>";
                            $content .= "<span class='m-list-timeline__text text-right' style='width: 25%;'><small>{$time}</small></span>";
                            $content .= "<span class='m-list-timeline__text text-right' style='width: 25%;'><small>{$date}</small></span>";
                        }
                        $content .= "</div>";
                    }

                    $content .= "</div>";
                }
            }

            echo $content;
        }

        public function getLateToday_oldcode_122218() {
            /* $today = date("Y-m-d"); */
            $today = $this->attendance->getLastSyncDate();

            $begin = new DateTime($today);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            $dates = array($begin, $newtime);
            $getAttendanceByDateRange = $this->attendance->getAttendanceByDateRange($dates, array());

            $Start = date('Y-m-d H:i:s', strtotime($begin));
            $End = date('Y-m-d H:i:s', strtotime($newtime));
            $content = "";
            if ($getAttendanceByDateRange->num_rows() > 0) {
                foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                    $checkLate = $this->checkLate($_getAttendanceByDateRange["biometric_id"], $_getAttendanceByDateRange["datetime"]);
                    if ($checkLate) {
                        $_time = date_format(date_create($_getAttendanceByDateRange["datetime"]), "H:i A");
                        $_date = date_format(date_create($_getAttendanceByDateRange["datetime"]), "Y-m-d");

                        $getPersonnel = $this->crud->load(array("biometric_id" => $_getAttendanceByDateRange["biometric_id"]), "gcctimeutility.personnel");
                        $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>
										<span class='m-list-timeline__badge m-list-timeline__badge--success'></span>
										<span class='m-list-timeline__text'>
											<small>" . $getPersonnel["name"] . "</small>
										</span>
										<span class='m-list-timeline__time' style='width: 135px;'>
											<label>{$_time}</label>
											<label>{$_date}</label>
										</span>
									</div>";

                    }
                }
            }
            echo $content;
        }

        public function getPersonnelUndertime($biometric_id) {
            $content = "";
            $content .= "<div style='padding: 0 0 5px 0 !important; margin: 0 auto;' class='m-list-timeline__item'>";
            $content .= "<span class='m-list-timeline__text'><strong>DATE</strong></span>";
            $content .= "<span class='m-list-timeline__text text-right' style='width: 70%;'><strong>REFERENCE #</strong></span>";
            $content .= "<span class='m-list-timeline__text text-right' style='padding: 0;'><strong>AM/PM</strong></span></div>";

            $undertime = $this->shift_management->getPersonalUndertime($biometric_id);
            if ($undertime) {
                if (isset($undertime["undertime"]) && $undertime["undertime"]) {
                    $content .= "<div class='m-list-items-data_item'>";
                    foreach ($undertime["undertime"] as $undertime) {
                        $date = date("F d, Y", strtotime($undertime["date"]));
                        $time = (isset($undertime["content"]) && $undertime["content"]) ? $undertime["content"] : "---";
                        $duration = $undertime["mrdn"];

                        $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>";
                        $content .= "<span class='m-list-timeline__text'><small>{$date}</small></span>";
                        $content .= "<span class='m-list-timeline__text text-right' style='width: 70%;'><small>{$time}</small></span>";
                        $content .= "<span class='m-list-timeline__text text-right'><small>{$duration}</small></span>";
                        $content .= "</div>";
                    }
                    $content .= "</div>";
                }
            }

            echo $content;
        }

        public function getUndertimeToday() {
            $undertime = $this->shift_management->getCurrentUndertime();
            $undertimeList = array();
            $content = "";

            if ($undertime) {
                if (isset($undertime["check_undertime_pm"]) && $undertime["check_undertime_pm"]) {
                    foreach ($undertime["check_undertime_pm"] as $undertime_pm) {
                        $undertime_pm["date"] = date("Y-m-d", strtotime($undertime_pm["date"]));
                        $undertimeList[] = $undertime_pm;
                    }
                }
                if (isset($undertime["check_undertime_am"]) && $undertime["check_undertime_am"]) {
                    foreach ($undertime["check_undertime_am"] as $undertime_am) {
                        $undertime_am["date"] = date("Y-m-d", strtotime($undertime_am["date"]));
                        $undertimeList[] = $undertime_am;
                    }
                }

            }

            if ($undertimeList) {
                foreach ($undertimeList as $value) {
                    $name = htmlentities($value["name"]);
                    $loaContent = $value["content"];
                    $mrdn = $value["mrdn"];

                    $content .= "<div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>";
                    $content .= "<span class='m-widget6__text' style='width: 50%;'><small>" . strtoupper(htmlentities($name)) . "</small></span>";
                    $content .= "<span class='m-widget6__text text-center' style='width: 30%;'><small>{$loaContent}</small></span>";
                    $content .= "<span class='m-widget6__text m-widget__text-logged-time m--align-right'><small>{$mrdn}</small></span>";
                    $content .= "</div>";
                }
            }

            echo $content;
        }

        public function getUndertimeToday_oldcode_122218() {
            /* $today = date("Y-m-d"); */
            $today = $this->attendance->getLastSyncDate();

            $begin = new DateTime($today);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            $dates = array($begin, $newtime);
            $getAttendanceByDateRange = $this->attendance->getAttendanceByDateRange($dates, array());

            $Start = date('Y-m-d H:i:s', strtotime($begin));
            $End = date('Y-m-d H:i:s', strtotime($newtime));
            $content = "";

            if ($getAttendanceByDateRange->num_rows() > 0) {
                foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                    $checkUndertime = $this->checkUndertime($_getAttendanceByDateRange["biometric_id"], $_getAttendanceByDateRange["datetime"]);
                    if ($checkUndertime) {
                        $_time = date_format(date_create($_getAttendanceByDateRange["datetime"]), "H:i A");
                        $_date = date_format(date_create($_getAttendanceByDateRange["datetime"]), "Y-m-d");

                        $getPersonnel = $this->crud->load(array("biometric_id" => $_getAttendanceByDateRange["biometric_id"]), "gcctimeutility.personnel");
                        $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item' class='m-list-timeline__item'>
										<span class='m-list-timeline__badge m-list-timeline__badge--success'></span>
										<span class='m-list-timeline__text'>
											<small>" . $getPersonnel["name"] . "</small>
										</span>
										<span style='width: 135px;' class='m-list-timeline__time'>
											<label>{$_time}</label>
											<label>{$_date}</label>
										</span>
									</div>";

                    }
                }
            }
            echo $content;
        }

        public function getAbsentToday() {
            $absentList = array();
            $post = $this->input->post();

            $currentDate = (isset($post["current_date"]) && $post["current_date"]) ? date("Y-m-d", strtotime($post["current_date"])) : date("Y-m-d");
            $previousDate = date("Y-m-d", strtotime("-1 day"));


            $content = "";

            $content .= "<div style='padding: 0 0 5px 0 !important; margin: 0 auto;' class='m-list-timeline__item'>";
            $content .= "<span class='m-list-timeline__text'><strong>EMPLOYEE NAME</strong></span>";
            $content .= "<span class='m-list-timeline__text text-left' style='width: 60%;'><strong>LOA/TO REFERENCE #</strong></span>";
            $content .= "<span style='width: 15%; padding: 0;' class='m-list-timeline__time text-right'><strong>AM/PM</strong></span></div>";

            $meridian = date("A");

            $absent = $this->shift_management->getCurrentAbsent();
            if (isset($absent["check_absent"]) && $absent["check_absent"]) {
                $_absent = $absent["check_absent"];
                if ($currentDate == $previousDate) {
                    if (isset($_absent["pm"]) && $_absent["pm"]) {
                        $content .= "<div class='m-list-items-data_item'>";
                        foreach ($_absent["pm"] as $data) {
                            $name = strtoupper(htmlentities($data["name"]));
                            $loaContent = (isset($data['content']) && $data['content']) ? $data['content'] : "N/A";
                            $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'><span class='m-list-timeline__text'>";
                            $content .= "<small>{$name}</small></span>";
                            $content .= "<span class='m-list-timeline__text' style='width: 60%;'><small>{$loaContent}</small></span>";
                            $content .= "<span style='width: 15%;' class='m-list-timeline__time'>{$data['mrdn']}</span></div>";
                        }
                        $content .= "</div>";
                    }

                    if (isset($_absent["am"]) && $_absent["am"]) {
                        $content .= "<div class='m-list-items-data_item-am'>";
                        foreach ($_absent["am"] as $data) {
                            $name = strtoupper(htmlentities($data["name"]));
                            $loaContent = (isset($data['content']) && $data['content']) ? $data['content'] : "N/A";
                            $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'><span class='m-list-timeline__text'>";
                            $content .= "<small>{$name}</small></span>";
                            $content .= "<span class='m-list-timeline__text' style='width: 60%;'><small>{$loaContent}</small></span>";
                            $content .= "<span style='width: 15%;' class='m-list-timeline__time'>{$data['mrdn']}</span></div>";
                        }
                        $content .= "</div>";
                    }
                } else {
                    if ($meridian == "AM") {
                        if (isset($_absent["am"]) && $_absent["am"]) {
                            $content .= "<div class='m-list-items-data_item-am'>";
                            foreach ($_absent["am"] as $data) {
                                $name = strtoupper(htmlentities($data["name"]));
                                $loaContent = (isset($data['content']) && $data['content']) ? $data['content'] : "N/A";
                                $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'><span class='m-list-timeline__text'>";
                                $content .= "<small>{$name}</small></span>";
                                $content .= "<span class='m-list-timeline__text' style='width: 60%;'><small>{$loaContent}</small></span>";
                                $content .= "<span style='width: 15%;' class='m-list-timeline__time'>{$data['mrdn']}</span></div>";
                            }
                            $content .= "</div>";
                        }
                    }

                    if ($meridian == "PM") {
                        if (isset($_absent["pm"]) && $_absent["pm"]) {
                            $content .= "<div class='m-list-items-data_item'>";
                            foreach ($_absent["pm"] as $data) {
                                $name = strtoupper(htmlentities($data["name"]));
                                $loaContent = (isset($data['content']) && $data['content']) ? $data['content'] : "N/A";
                                $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'><span class='m-list-timeline__text'>";
                                $content .= "<small>{$name}</small></span>";
                                $content .= "<span class='m-list-timeline__text' style='width: 60%;'><small>{$loaContent}</small></span>";
                                $content .= "<span style='width: 15%;' class='m-list-timeline__time'>{$data['mrdn']}</span></div>";
                            }
                            $content .= "</div>";
                        }

                        if (isset($_absent["am"]) && $_absent["am"]) {
                            $content .= "<div class='m-list-items-data_item-am'>";
                            foreach ($_absent["am"] as $data) {
                                $name = strtoupper(htmlentities($data["name"]));
                                $loaContent = (isset($data['content']) && $data['content']) ? $data['content'] : "N/A";
                                $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'><span class='m-list-timeline__text'>";
                                $content .= "<small>{$name}</small></span>";
                                $content .= "<span class='m-list-timeline__text' style='width: 60%;'><small>{$loaContent}</small></span>";
                                $content .= "<span style='width: 15%;' class='m-list-timeline__time'>{$data['mrdn']}</span></div>";
                            }
                            $content .= "</div>";
                        }
                    }
                }

            }

            echo $content;
        }

        public function getAbsentToday_oldcode_122218() {
            /* $now = date("Y-m-d H:i:s"); */
            /* $todays = date("Y-m-d"); */

            $now = $this->attendance->getLastSyncDate(true);
            $todays = $this->attendance->getLastSyncDate();

            $nows = new DateTime($now);
            $content = "";

            $this->db->select("*");
            $this->db->from("gcctimeutility.absent");
            $this->db->like("updated_at", $todays);
            $this->db->order_by("id", "DESC");
            $queryget = $this->db->get();
            $getAbsentCollection = $queryget->result_array();

            if ($getAbsentCollection) {
                foreach ($getAbsentCollection as $_getAbsentCollection) {
                    $getPersonnel = $this->crud->load(array("biometric_id" => $_getAbsentCollection["biometric_id"]), "gcctimeutility.personnel");
                    $getEmployeeData = $this->crud->load(array("biometricno" => $_getAbsentCollection["biometric_id"]), "gccmaster.tblemployees");
                    $getLOA = "";
                    $getLOA = $this->crud->getCollection(array("employee" => $getEmployeeData["id"]), "gcceforms.loa");
                    $newLOAData = "false";

                    $catch = array();
                    if ($getLOA) {
                        foreach ($getLOA as $_getLOA) {
                            $ampm = "";
                            $ampm = date_format(date_create($_getLOA["date_from"]), "A");

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
                    if (in_array("positive", $catch)) {
                        $getNewLOA = $this->crud->load(array("id" => $catch["loaid"]), "gcceforms.loa");
                        /*** $loacontent = $getNewLOA ? "LOA Reference No: ". $getNewLOA["reference_no"] : ""; ***/
                        $loacontent = $getNewLOA ? $getNewLOA["reference_no"] : "";
                    }


                    $content .= "<div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>
								<span class='m-list-timeline__badge m-list-timeline__badge--success'></span>
								<span class='m-list-timeline__text'>
									<small>" . htmlentities($getPersonnel["name"]) . "</small>
								</span>
								<span class='m-list-timeline__text'><small>" . $loacontent . "</small></span>
								<span style='width: 80px;' class='m-list-timeline__time'>
									" . $_getAbsentCollection["meredien"] . "
								</span>
							</div>";
                }
            }

            $content = "";
            /*** please comment this after 12/10/18 TY ***/
            echo $content;
        }

        function getPersonnelLackingDoubleEntry($biometric_id = null) {
            $resultset = array();
            $entry = $this->shift_management->getPersonalLackingDoubleEntries($biometric_id);
            if (isset($entry["lacking_entry"]) && $entry["lacking_entry"]) {
                $content = "";
                foreach ($entry["lacking_entry"] as $lacking) {
                    $date = (isset($lacking["date"]) && $lacking["date"]) ? date("F, d Y", strtotime($lacking["date"])) : date("F d, Y");
                    $total = (isset($lacking["time_count"]) && $lacking["time_count"]) ? intval($lacking["time_count"]) : 0;

                    $loggedTime = "";
                    if (isset($lacking["logged_time_record"]) && $lacking["logged_time_record"]) {
                        foreach ($lacking["logged_time_record"] as $timeLog) {
                            $dt = date_format(date_create($timeLog), "h:i A");
                            $loggedTime .= "<label>{$dt}</label>";
                        }
                    }

                    $content .= "<div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>				 
					<span class='m-widget6__text' style='width: 25%;'><small>{$date}</small></span>
					<span class='m-widget6__text text-center' style='width: 2%;'><small>{$total}</small></span>
					<span class='m-widget6__text m-widget__text-logged-time m--align-right'><small>{$loggedTime}</small></span>					 
				</div>";
                }
                $resultset["lacking_entry"] = $content;
            }

            if (isset($entry["double_entry"]) && $entry["double_entry"]) {
                $content = "";
                foreach ($entry["double_entry"] as $double) {
                    $date = (isset($double["date"]) && $double["date"]) ? date("F, d Y", strtotime($double["date"])) : date("F d, Y");
                    $total = (isset($double["time_count"]) && $double["time_count"]) ? intval($double["time_count"]) : 0;

                    $loggedTime = "";
                    if (isset($double["logged_time_record"]) && $double["logged_time_record"]) {
                        foreach ($double["logged_time_record"] as $timeLog) {
                            $dt = date_format(date_create($timeLog), "h:i A");
                            $loggedTime .= "<label>{$dt}</label>";
                        }
                    }

                    $content .= "<div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>				 
					<span class='m-widget6__text' style='width: 25%;'><small>{$date}</small></span>
					<span class='m-widget6__text text-center' style='width: 2%;'><small>{$total}</small></span>
					<span class='m-widget6__text m-widget__text-logged-time m--align-right'><small>{$loggedTime}</small></span>					 
				</div>";
                }
                $resultset["double_entry"] = $content;
            }

            echo json_encode($resultset);
        }

        function getLackingYesterday() {
            $content = "";
            $entry = $this->shift_management->getLackingDoubleEntries();
            if (isset($entry["lacking_entry"]) && $entry["lacking_entry"]) {
                foreach ($entry["lacking_entry"] as $lacking) {
                    $name = (isset($lacking["employee_name"]) && $lacking["employee_name"]) ? $lacking["employee_name"] : $lacking["biometric_id"];
                    $total = (isset($lacking["time_count"]) && $lacking["time_count"]) ? intval($lacking["time_count"]) : 0;
                    $reference_no = (isset($lacking["reference_no"]) && $lacking["reference_no"]) ? $lacking["reference_no"] : "N/A";

                    $loggedTime = "";
                    if (isset($lacking["logged_time_record"]) && $lacking["logged_time_record"]) {
                        foreach ($lacking["logged_time_record"] as $timeLog) {
                            $dt = date_format(date_create($timeLog), "h:i A");
                            /*** $loggedTime .= "<label>{$dt}</label>"; ***/
                            $loggedTime .= '<span class="m-menu__link-badge"><span class="m-badge m-badge--accent m-badge--wide">' . $dt . '</span></span>';
                        }
                    }

                    $content .= "<div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>				 
					<span class='m-widget6__text' style='width: 20%;'><small>" . strtoupper(htmlentities($name)) . "</small></span>
					<span class='m-widget6__text text-center' style='width: 25%;'><small>{$reference_no}</small></span>
					<span class='m-widget6__text text-center' style='width: 2%;'><small>{$total}</small></span>
					<span class='m-widget6__text m-widget__text-logged-time m--align-right'><small>{$loggedTime}</small></span>					 
				</div>";
                }
            }

            echo $content;
        }

        function getLackingYesterday_oldcode_122218() {
            $todays = $this->attendance->getLastSyncDate();

            $yesterday = date('Y-m-d', strtotime("-1 days", strtotime($todays)));
            $query = $this->attendance->getYesterdayAttendance($yesterday);

            $content = "";
            if ($query) {
                foreach ($query as $_query) {
                    $data = array();
                    $personnelAttendanceData = $this->attendance->getPersonnelAttendanceByDate($yesterday, $_query["biometric_id"]);
                    $getPersonnelData = $this->crud->load(array("biometric_id" => $_query["biometric_id"]), "gcctimeutility.personnel");

                    $loggedTime = "";
                    foreach ($personnelAttendanceData as $index => $datax) {
                        $dateTime = date_format(date_create($datax["datetime"]), "h:i A");
                        $loggedTime .= "<label>{$dateTime}</label>";
                    }

                    $total = count($personnelAttendanceData);

                    if (count($personnelAttendanceData) < 4) {
                        $name = (isset($getPersonnelData["name"]) && $getPersonnelData["name"]) ? trim($getPersonnelData["name"]) : $_query["biometric_id"];
                        $content .= "<div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>				 
									<span class='m-widget6__text' style='width: 12%;'><small>" . htmlentities($name) . "</small></span>
									<span class='m-widget6__text text-center' style='width: 2%;'><small>{$total}</small></span>
									<span class='m-widget6__text m-widget__text-logged-time m--align-right'><small>{$loggedTime}</small></span>					 
								</div>";
                    }
                }
            }

            echo $content;
        }

        function getDoubleYesterday() {
            $content = "";
            $entry = $this->shift_management->getLackingDoubleEntries();

            if (isset($entry["double_entry"]) && $entry["double_entry"]) {
                foreach ($entry["double_entry"] as $lacking) {
                    $name = (isset($lacking["employee_name"]) && $lacking["employee_name"]) ? $lacking["employee_name"] : $lacking["biometric_id"];
                    $total = (isset($lacking["time_count"]) && $lacking["time_count"]) ? intval($lacking["time_count"]) : 0;

                    $loggedTime = "";
                    if (isset($lacking["logged_time_record"]) && $lacking["logged_time_record"]) {
                        foreach ($lacking["logged_time_record"] as $timeLog) {
                            $dt = date_format(date_create($timeLog), "h:i A");
                            /*** $loggedTime .= "<label>{$dt}</label>"; ***/
                            $loggedTime .= '<span class="m-menu__link-badge"><span class="m-badge m-badge--accent m-badge--wide">' . $dt . '</span></span>';
                        }
                    }

                    $content .= "<div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>				 
					<span class='m-widget6__text' style='width: 25%;'><small>" . strtoupper(htmlentities($name)) . "</small></span>
					<span class='m-widget6__text text-center' style='width: 2%;'><small>{$total}</small></span>
					<span class='m-widget6__text m-widget__text-logged-time m--align-right'><small>{$loggedTime}</small></span>					 
				</div>";
                }
            }

            echo $content;
        }

        function getDoubleYesterday_oldcode_122218() {
            $todays = $this->attendance->getLastSyncDate();

            $yesterday = date('Y-m-d', strtotime("-1 days", strtotime($todays)));
            $query = $this->attendance->getYesterdayAttendance($yesterday);

            $content = "";
            if ($query) {
                foreach ($query as $_query) {
                    $data = array();
                    $personnelAttendanceData = $this->attendance->getPersonnelAttendanceByDate($yesterday, $_query["biometric_id"]);
                    $getPersonnelData = $this->crud->load(array("biometric_id" => $_query["biometric_id"]), "gcctimeutility.personnel");

                    $loggedTime = "";
                    foreach ($personnelAttendanceData as $index => $datax) {
                        $dateTime = date_format(date_create($datax["datetime"]), "h:i A");
                        $loggedTime .= "<label>{$dateTime}</label>";
                    }

                    $total = count($personnelAttendanceData);

                    if (count($personnelAttendanceData) > 4) {
                        $name = (isset($getPersonnelData["name"]) && $getPersonnelData["name"]) ? trim($getPersonnelData["name"]) : $_query["biometric_id"];
                        $content .= "<div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>				 
									<span class='m-widget6__text' style='width: 12%;'><small>" . htmlentities($name) . "</small></span>
									<span class='m-widget6__text text-center' style='width: 2%;'><small>{$total}</small></span>
									<span class='m-widget6__text m-widget__text-logged-time m--align-right'><small>{$loggedTime}</small></span>					 
								</div>";
                    }
                }
            }

            echo $content;
        }

        public function getAbsentTodayEmail() {

        }

        function getDateRangeToday() {
            $today = date("Y-m-d");

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

        function log_event($event = "") {
            $query = $this->crud->insert(array("event_name" => $event), "gcctimeutility.event");

            return $query ? TRUE : FALSE;
        }

        public function email_notification() {
            $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.gmail.com',
                'smtp_port' => 465,//25 465
                'smtp_user' => 'gcceforms@gmail.com',
                'smtp_pass' => 'Sc0t2366',
                'newline' => "\r\n",
                'mailtype' => 'html',
                'charset' => 'utf-8' //iso-8859-1
            );

            $this->email->initialize($config);
            $this->email->from('gcceforms@gmail.com', 'GC&C eForms');
            $this->email->to('jp03@gccph.com');
            //$this->email->cc('jp01@gccph.com');

            $message = "";
            $message .= $this->load->view("attendance/getAbsentToday", NULL, true);

            $this->email->subject('Absentee Report' . " -" . date("F d, Y"));
            $this->email->message(nl2br($message));
            $this->email->send();
            $result = $this->email->send();

            echo $this->email->print_debugger();

        }

        public function addAttendance() {
            $userdata = $this->session->userdata("logged_in");

            $data = array();
            $post = $this->input->post();
            $resultarray = array();
            $checkres = array();

            if (count($post["datetime"]) > 0) {
                foreach ($post["datetime"] as $datetime) {

                    $data["biometric_id"] = $post["biometric_id"];
                    $data["datetime"] = $datetime;
                    $data["status"] = 1;
                    // $data["created_by"] = $userdata["emp_id"];

                    $query = $this->crud->insert($data, "gcctimeutility.attendance_temp");
                    $attendance_temp_id = $this->db->insert_id();
                    $insert_remarks = $this->crud->insert(array("attendance_temp_id" => $attendance_temp_id, "remarks" => $post["remarks"], "status" => 1), "gcctimeutility.attendance_remarks");

                    $checkres[] = $query ? 0 : 1;

                }
            }

            if (in_array(0, $checkres)) {
                $resultarray["message"] = "Saved successfully!";
                $resultarray["status"] = TRUE;
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["message"] = "Error processing request!";
            }

            echo json_encode($resultarray);

        }

        public function editAttendance() {
            $resultarray = array();
            $post = $this->input->post();
            $userdata = $this->getUserdata();

            $query = $this->crud->update(array("datetime" => $post["datetime"], "is_custom" => 1, "is_custom_by" => $userdata["id"], "updated_at" => date("Y-m-d H:i:s")), array("id" => $post["id"]), "gcctimeutility.attendance");

            if ($query) {
                $resultarray["message"] = "Updated successfully!";
                $resultarray["status"] = TRUE;
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["message"] = "Error processing request!";
            }

            echo json_encode($resultarray);
        }

        public function updateAttendanceData() {
            $resultarray = array();
            $post = $this->input->post();

            $query = $this->crud->update(array("datetime" => $post["datetime"]), array("id" => $post["id"]), "gcctimeutility.attendance");

            if ($query) {
                $resultarray["message"] = "Updated successfully!";
                $resultarray["status"] = TRUE;
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["message"] = "Error processing request!";
            }

            echo json_encode($resultarray);

        }

        public function getAttendanceData() {
            $post = $this->input->post();
            $resultarray = array();

            $query = $this->crud->load($post, "gcctimeutility.attendance");

            if ($query) {
                $getPersonnelData = $this->crud->load(array("biometric_id" => $query["biometric_id"]), "gcctimeutility.personnel");

                $resultarray["status"] = TRUE;
                $resultarray["name"] = $getPersonnelData["name"];
                $resultarray["datetime"] = $query["datetime"];
                $resultarray["id"] = $query["id"];
            } else {
                $resultarray["status"] = FALSE;
            }

            echo json_encode($resultarray);

        }

        /**** test ****/

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

        function get_stored_data() {
            echo "<pre>";
            $datetime = date("Y-m-d", strtotime("2019-4-2"));
            $dd = $this->getStoredDataByDate($datetime);
            if ($dd) {
                $counter = 0;
                foreach ($dd as $row) {
                    $query = $this->db->get_where("gcctimeutility.attendance", array("biometric_id" => $row[1], "datetime" => $row[3]));
                    if ($query->num_rows() == 0) {
                        var_dump($row);
                        $counter++;
                    }
                }
                var_dump($counter);
            }

        }

        public function generate_attendance_record(){
            $this->core_layout->setPrivilegeName("gcctime_generate_attendance_record");

            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");

            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
            
            $this->core_layout->addJs("js/dataTables.buttons.min.js", true);
            $this->core_layout->addJs("js/buttons.flash.min.js", true);
            $this->core_layout->addJs("js/jszip.min.js", true);
            $this->core_layout->addJs("js/pdfmake.min.js", true);
            $this->core_layout->addJs("js/vfs_fonts.js", true);
            $this->core_layout->addJs("js/buttons.html5.min.js", true);
            $this->core_layout->addJs("js/buttons.print.min.js", true);
            $this->core_layout->addCss("css/buttons.dataTables.min.css", true);

            $arrData = array();
            $this->load->view('core/templates/header');
            $this->load->view('attendance/generate_attendance_record', $arrData);
            $this->load->view('core/templates/footer');
        }

        public function generate_attendance_logs($alteredDate = null){
            $this->load->model("timesheet_model", "ts_model");
            $resultset = [];
            //$alteredDate = $alteredDate ?? "2025-10-10";
            $alteredDate = $alteredDate ?? date("Y-m-d");
            $searchDate = $alteredDate;

            if (!isset($_FILES['files']['name']) || $_FILES['files']['name'] == '') {
                $resultset["response"] = false;
                $resultset["message"] = "No file uploaded.";
                echo json_encode($resultset);
                return;
            }

            // Validate file extension
            $fileExt = strtolower(pathinfo($_FILES['files']['name'], PATHINFO_EXTENSION));
            if ($fileExt !== 'dat') {
                $resultset["response"] = false;
                $resultset["message"] = "Invalid File Format";
                echo json_encode($resultset);
                return;
            }

            $filename = $_FILES['files']['tmp_name'];
            $dateIndex = [];
            $dates = [];

            if (($handle = fopen($filename, "r")) !== false) {
                while (($line = fgets($handle)) !== false) {
                    $line = trim($line, "\" \n\r\t");
                    if (empty($line)){ continue; }
                    $parts = explode(',', $line);
                    if(!empty($parts) && count($parts) > 1){
                        $empId = $parts[0];
                        $timestamp = $parts[2];
                        $datePart = date('Y-m-d', strtotime($timestamp));
                        $dateIndex[$datePart][] = [$empId, $timestamp];
                        if(!in_array($datePart, $dates)){
                            $dates[] = $datePart;
                        }
                    } elseif(!empty($parts) && count($parts) == 1){
                        $nextLine = trim($parts[0], "\" \n\r\t");
                        $nextParts = explode("\t", $nextLine);

                        $empId = $nextParts[0];
                        $timestamp = $nextParts[1];
                        $datePart = date('Y-m-d', strtotime($timestamp));
                        $dateIndex[$datePart][] = [$empId, $timestamp];
                        if(!in_array($datePart, $dates)){
                            $dates[] = $datePart;
                        }
                    }
                }

                fclose($handle);
            }

            // Logs found for selected date
            $dailyLogs = $dateIndex[$searchDate] ?? [];

            // Build structured logs
            $structured = [];
            foreach ($dailyLogs as $row) {
                [$empId, $ts] = $row;
                if (!isset($structured[$empId])) { $structured[$empId] = []; }
                // If last entry for employee has only 1 timestamp → append as OUT
                $lastIndex = count($structured[$empId]) - 1;
                if ($lastIndex >= 0 && count($structured[$empId][$lastIndex]) === 1) {
                    $structured[$empId][$lastIndex][] = $ts;
                } else {
                    // Create new IN (or standalone) record
                    $structured[$empId][] = [$ts];
                }
            }

            $rawData = [];
            $isLateCtr = 0;
            if(!empty($structured)){
                $searchDate = date("Y-m-d", strtotime($searchDate));
                foreach ($structured as $bionum => $logs) {
                    if(isset($logs[0]) && !empty($logs[0])){
                        $firstShiftLogs = $logs[0];
                        $this->db->select("UCASE(
                            TRIM(
                                CONCAT(
                                    emp.firstname,
                                    IF(emp.middlename IS NOT NULL AND emp.middlename != '', CONCAT(' ', LEFT(emp.middlename,1), '.'), ''),
                                    ' ',
                                    emp.lastname,
                                    IF(emp.suffix IS NOT NULL AND emp.suffix != '' AND emp.suffix NOT IN ('N/A','NONE'),
                                    CONCAT(' ', emp.suffix),
                                    ''
                                    )
                                )
                            )
                        ) AS employee_name, UPPER(comp.code) as company, UPPER(dept.code) as department, pn.shift_id, pn.is_flexi, emp.id");
                        $this->db->from("gccmaster.tblemployees as emp");
                        $this->db->join("gcchris.tblcompanies as comp", "comp.id = emp.company_id", "LEFT");
                        $this->db->join("gcchris.tbldepartments as dept", "dept.id = emp.department_id", "LEFT");
                        $this->db->join("gcctimeutility.personnel as pn", "pn.biometric_id = emp.biometricno OR pn.biometricno = emp.biometricno", "left");
                        $this->db->where("emp.biometricno", $bionum);
                        $qData = $this->db->get();
                        if($qData->num_rows() == 1){
                            $rowData = $qData->row();
                            $schedule = $this->ts_model->getCurrentShiftSchedule($searchDate, $rowData);
                            $shiftScheduleTime = date("Y-m-d H:i:s", strtotime($searchDate . " ". $schedule->schedule->am_start));
                            $logtime = date("Y-m-d H:i:s", strtotime($firstShiftLogs[0]));

                            $attRecord = new stdClass();
                            $attRecord->biometricno = $bionum;
                            $attRecord->employee_name = $rowData->employee_name;
                            $attRecord->company = $rowData->company;
                            $attRecord->department = $rowData->department;
                            $attRecord->log_time = $logtime;
                            $attRecord->shift_start = $shiftScheduleTime;
                            $attRecord->is_late = false;
                            if($rowData->is_flexi == 0 || $rowData->is_flexi == 2){
                                $attRecord->is_late = strtotime($logtime) > strtotime($shiftScheduleTime);
                                if($attRecord->is_late){ $isLateCtr++; }
                            } elseif ($rowData->is_flexi == 1 || $rowData->is_flexi == 3){
                                $plus30 = date("Y-m-d H:i:s", strtotime("+30 minutes", strtotime($shiftScheduleTime)));
                                $attRecord->is_late = strtotime($logtime) > strtotime($plus30);
                                if($attRecord->is_late){ $isLateCtr++; }
                            }
                            $rawData[] = $attRecord;
                        }
                        
                    }
                }
            }

            usort($rawData, function($a, $b) {
                return strcasecmp($a->employee_name, $b->employee_name);
            });

            $resultset["dates"] = $dates;
            $resultset["logs"] = $rawData;
            $resultset["count"] = count($rawData);
            $resultset["late_ctr"] = $isLateCtr;
            if(!empty($rawData)){
                $resultset["response"] = true;
                $resultset["message"] = "Success";
            }else{
                $resultset["response"] = false;
                $resultset["message"] = "No logs found";
            }

            echo json_encode($resultset);
        }


        public function ___generate_attendance_logs($alteredDate=null){
            $resultset = array();
            $alteredDate = $alteredDate ?? "2025-11-11";
            $searchDate = $alteredDate ?? date("Y-m-d");
            if (isset($_FILES['files']['name']) && $_FILES['files']['name'] != '') {
                $fileName = $_FILES['files']['name'];
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                if(strtolower($fileExt) !== 'dat') {
                    $resultset["response"] = false;
                    $resultset["message"] = "Invalid File Format";
                }else{
                    $filename = $_FILES['files']['tmp_name'];
                    $batchSize  = 500;
                    $filtered = [];

                    if(($handle = fopen($filename, "r")) !== false) {
                        while (($line = fgets($handle)) !== false) {
                            $line = trim($line, "\" \n\r\t");
                            if(empty($line)){ continue; }

                            $parts = explode(',', $line);
                            if (!isset($parts[0]) || !is_numeric($parts[0])){ continue; }
                            if (!isset($parts[2]) || !preg_match('/^\d{4}\/\d{2}\/\d{2}/', $parts[2])){ continue; }
                            $datePart = date('Y-m-d', strtotime($parts[2]));
                            if (!isset($dateIndex[$datePart])) {
                                $dateIndex[$datePart] = [];
                            }
                            $filteredParts = [$parts[0], $parts[2]];
                            $dateIndex[$datePart][] = $filteredParts;
                        }
                        fclose($handle);
                    }

                    $results = $dateIndex[$searchDate] ?? [];
                    $batches = array_chunk($results, $batchSize);
                    foreach ($batches as $batch) {
                        foreach ($batch as $row) {
                            $filtered[] = $row;
                        }
                    }

                    $resultset["response"] = true;
                    $resultset["message"] = "Success";
                    $resultset["file"] = $filtered;
                }
            }

            echo json_encode($resultset);
        }

        function geneate_attendance_log_file(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post["filename"]) && $post["filename"]){
                $arrData = array();

                $tempFilename = $post["filename"];
                $tempFilename = "./uploads/data/{$tempFilename}";
                $file = file_get_contents($tempFilename, true);
                $tempData = json_decode($file, true);
                if(is_array($tempData["attendance_log"]) && count($tempData["attendance_log"]) > 0){
                    foreach ($tempData["attendance_log"] as $key => $value) {
                        $qSearch = $this->db->get_where("gcctimeutility.attendance", array("biometric_id"=>$value[1], "datetime"=>$value[3]));
                        if($qSearch->num_rows() == 0){
                            $arrData[] = array(
                                "biometric_id"=>$value[1], 
                                "state"=>$value[2], 
                                "datetime"=>$value[3], 
                                "device_id"=>$tempData["device_id"]
                            );
                        }
                    }
                }

                $resultset["response"] = true;
                $resultset["data"] = $arrData;
                $resultset["device_id"] = $tempData["device_id"];
            }else{
                $resultset["response"] = false;
            }

            echo json_encode($resultset);
        }

        function get_attendance_log_files(){
            $resultset = array();
            $arrFiles = array();
            $dates = array();

            $qTemp = $this->db->get_where("gcctimeutility.devices", array("status"=>1, "is_active"=>1));
            if($qTemp->num_rows() == 1){
                $tempRow = $qTemp->row();
                $tempDeviceName = str_replace(" ","_", trim(strtolower($tempRow->device_name)));
                if ($handle = opendir('./uploads/data')) {
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != "..") {
                            $temp = explode("-", $entry);
                            if(count($temp) == 2){
                                $tempDate = explode(".", $temp[1]);
                                if(count($tempDate) == 2){
                                    $tempDay = substr($tempDate[0], 0, 2);
                                    $tempYear = substr($tempDate[0], 2, 4);
                                    $tempMonth = substr($tempDate[0], -2);
                                
                                    $currentDate = date("Y/m/d", strtotime("{$tempYear}-{$tempMonth}-{$tempDay}"));
                                    if($temp[0] == $tempDeviceName){ 
                                        $arrFiles[] = array("id"=>$entry, "text"=>"Attendance Log {$currentDate}", "date"=>$currentDate);
                                    }
                                }
                            }
                        }
                    }
                    closedir($handle);
                }
            }
            if(count($arrFiles) > 0){
                foreach ($arrFiles as $key => $value) {
                    $dates[$key] = $value["date"];
                }
            }
            array_multisort($dates, SORT_DESC, $arrFiles);
            $resultset["results"] = $arrFiles;

            echo json_encode($resultset);
        }
        /**** test ****/

        function sync_app_attendance(){
            $attend = $this->attendance->syncAppAttendance();
            echo $attend;
            
        }

        public function get_app_attendance(){
            $attend = $this->attendance->syncAttendanceApp();
            echo $attend;
        }

        public function select_employee(){
            $data = $this->attendance->selectEmployee();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function select_payroll_group() {
            $data = $this->attendance->selectPayrollGroup();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_payroll_group_multiple() {
            $data = $this->attendance->getPayrollGroupMultiple();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_mobile_attendance_list() {
            $data = $this->attendance->getMobileAttendanceList();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_mobile_attendance_data() {
            $data = $this->attendance->getMobileAttendanceDataRecord();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
    }