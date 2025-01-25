<?php defined('BASEPATH') OR exit('No direct script access allowed');
    require_once APPPATH . 'libraries/codeigniter-predis/src/Redis.php';

    class Dashboard extends MY_Controller {
        private $adminPrivilege = false;
        private $redis;

        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("time");
            $this->authenticate->doRedirect();
            $this->core_layout->setPrivilegeName("gcctime_dashboard");
            $this->user_data = $this->session->userdata("logged_in");
            $this->load->model("Attendance_model", "attendance_m");
            $privileges = $this->core_layout->getCurrentActions();
            if(is_array($privileges) && in_array("administrator_privilege", $privileges)){ $this->adminPrivilege = true; }
            date_default_timezone_set("Asia/Manila");
            $this->load->model('Dashboard_model', 'dashboard_m');

            $this->redis = new \CI_Predis\Redis(['serverName' => 'localhost']);
        }

        public function index() {
            /*** disable node js response temporarily ***/
            /*** $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $domainName = $_SERVER['HTTP_HOST'] . '/';
            $url =  $protocol . $domainName;
            $arrData = array("url"=>$url, "port"=>3000);
            
            $this->core_layout->addJs("js/socket.io.js");
            $this->core_layout->addJs("js/time/dashboard/socket.script.js", true, $arrData); ***/
             /*** disable node js response temporarily ***/
             // $data["logs"] = $this->core_layout->getLogNotification("gcctimev2");
             // $data["real_time_attendances"] = $this->attendance_m->getRealTimeAttendances(date("Y-m-d"));
            
            $biometricId = $this->getPersonnelBiometricIdv2();
            $roleId = $this->authenticate->getRoleId();

            $last = $this->db->where("event_name", "sync_data")->order_by('id',"desc")->limit(1)->get('gcctimeutility.event')->row();

            $createdAt = (isset($last->created_at) && $last->created_at)? $last->created_at: date("Y-m-d");
            $currentDate = date_format(date_create($createdAt),"F d, Y");
            $yesterdayDate = date("F d, Y", strtotime("-1 days", strtotime($currentDate)));
            $personalDate = date_format(date_create($createdAt),"F 1 - d, Y");

            $data = array();
            $data["biometric_id"] = $biometricId;
            $data["admin_privilege"] = $this->adminPrivilege;
            $data['roleId'] = $roleId;
            $data['createdAt'] = $createdAt;
            $data['currentDate'] = $currentDate;
            $data['yesterdayDate'] = $yesterdayDate;
            $data['personalDate'] = $personalDate;

            $this->core_layout->addJs("js/time/dashboard/index.js", true, $data);

            $this->load->view('core/templates/header');
            $this->load->view('gcctime/dashboard/index', $data);
            $this->load->view('core/templates/footer');
        }

        private function getPersonnelBiometricIdv2(){
            $biometric_id = 0;
            $data = $this->session->userdata("logged_in");

            if ($data['emp_id']) {
                $this->db->select("biometricno");
                $this->db->where("id", $data['emp_id']);
                $query = $this->db->get("gccmaster.tblemployees");

                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $biometric_id = ($row->biometricno) ? $row->biometricno : 0;
                }
            }

            return $biometric_id;
        }

        private function getPersonnelBiometricId() {
            $biometric_id = 0;
            $data = $this->session->userdata("logged_in");
            $empId = $this->core_layout->getCurrentEmployeeId();
            if ($empId) {
                $this->db->select("b.biometric_id");
                $this->db->from("gccmaster.tblemployees a");
                $this->db->join("gcctimeutility.personnel b", "b.biometricno = a.biometricno");
                $this->db->where("a.id", $empId);
                $query = $this->db->get();

                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $biometric_id = ($row->biometric_id) ? $row->biometric_id : 0;
                }
            }

            return $biometric_id;
        }

        public function get_real_time_attendances() {
            echo json_encode($this->attendance_m->getRealTimeAttendances(date("Y-m-d")));
        }

        public function get_activity_logs(){
            $data = $this->dashboard_m->getLogNotification("gcctimev2");
            ksort($data);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        /** reworked dashboard requests */
        public function get_real_time_attendancesv2() {
            try{
                $todays_attendance = $this->redis->get("all_today_cache");
            } catch (Exception $e) {
                $todays_attendance = NULL;
            }

            if (!$todays_attendance) {
                $data = $this->dashboard_m->getRealTimeAttendances(date("Y-m-d"));

                if (!empty($data)) {
                    try {
                        // $cached_records = serialize($data);
                        // $this->redis->set("all_today_cache", $cached_records);
                    } catch (Exception $e) {
                        $data['no_cache'] = $e->getMessage();
                    }
                }
            } else {
                $cached = @unserialize($todays_attendance) ? @unserialize($todays_attendance) : array();
                $data = $cached;
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_late_today_dashboard() {
            try {
                $lateCache = $this->redis->get("all_late_cache");
            } catch (Exception $e) {
                $lateCache = NULL;
            }

            if (!$lateCache) {
                $late = $this->dashboard_m->getCurrentLate();
                unset($late["current_state"]);
    
                $am = isset($late['checklate_am']) ? array_column($late['checklate_am'], 'time') : $late['checklate_am'] = array();
                $pm = isset($late['checklate_pm']) ? array_column($late['checklate_pm'], 'time') : $late['checklate_pm'] = array();
    
                array_multisort($am, SORT_DESC, $late['checklate_am']);
                array_multisort($pm, SORT_DESC, $late['checklate_pm']);

                if(!empty($late['checklate_am']) || !empty($late['checklate_pm'])) {
                    try {
                        $cached_records = serialize($late);
                        $this->redis->set("all_late_cache", $cached_records);
                    } catch(Exception $e) {
                        $late['no_cache'] = $e->getMessage();
                    }
                }
            } else {
                $cached = @unserialize($lateCache) ? @unserialize($lateCache) : array();
                $late = $cached;
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($late));
        }

        public function get_absent_today_dashboard() {
            try {
                $absentCache = $this->redis->get("all_absent_cache");
            } catch (Exception $e) {
                $absentCache = NULL;
            }

            if (!$absentCache) {
                $absentList = array();
                $absentList = $this->dashboard_m->getCurrentAbsentDashboard();
                $absentList['currentDate'] = (isset($post["current_date"]) && $post["current_date"]) ? date("Y-m-d", strtotime($post["current_date"])) : date("Y-m-d");
                $absentList['previousDate'] = date("Y-m-d", strtotime("-1 day"));
                $absentList['meridian'] = date("A");

                $am = isset($absentList['check_absent']['am']) ? array_column($absentList['check_absent']['am'], 'name') : $absentList['check_absent']['am'] = array();
                $pm = isset($absentList['check_absent']['pm']) ? array_column($absentList['check_absent']['pm'], 'name') : $absentList['check_absent']['pm'] = array();

                array_multisort($am, SORT_DESC, $absentList['check_absent']['am']);
                array_multisort($pm, SORT_DESC, $absentList['check_absent']['pm']);

                if (!empty($absentList['check_absent'])) {
                    try {
                        $cached_records = serialize($absentList); //serialized data to be save as string
                        $this->redis->set("all_absent_cache", $cached_records);
                    } catch (Exception $e) {
                        $absentList['no_cache'] = $e->getMessage();
                    }
                }
            } else{
                $absentList = @unserialize($absentCache) ? @unserialize($absentCache) : array();
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($absentList));
        }

        public function get_undertime_today() {
            try {
                $undertimeCache = $this->redis->get("undertime_cache");
            } catch (Exception $e) {
                $undertimeCache = NULL;
            }
            
            if (!$undertimeCache) {
                $undertime = $this->dashboard_m->getCurrentTodayUndertime();
                $undertimeList = array();
                
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

                if (!empty($undertimeList)) {
                    try {
                        $cached_records = serialize($undertimeList);
                        $this->redis->set("undertime_cache", $cached_records);
                    } catch (Exception $e) {
                        $undertimeList['no_cache'] = $e->getMessage();
                    }
                }
            } else {
                $cached = @unserialize($undertimeCache) ? @unserialize($undertimeCache) : array();
                $undertimeList = $cached;
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($undertimeList));
        }

        function get_double_yesterday() {
            try {
                $double = $this->redis->get("double_cache");
            } catch (Exception $e) {
                $double = NULL;
            }

            if (!$double) {
                $entry = $this->dashboard_m->getLackingDoubleEntries(null, 'double');
                
                if (!empty($entry["double_entry"])) {
                    try{
                        $cached_records = serialize($entry);
                        $this->redis->set("double_cache", $cached_records);
                    } catch(Exception $e) {
                        $entry['no_cache'] = $e->getMessage();
                    }
                }
            } else {
                $cached = @unserialize($double) ? @unserialize($double) : array();
                $entry = $cached;
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($entry));
        }

        function get_lacking_yesterday() {
            try {
                $lacking = $this->redis->get("lacking_cache");
            } catch (Exception $e) {
                $lacking = NULL;
            }

            if (!$lacking) {
                $entry = $this->dashboard_m->getLackingDoubleEntries(null, 'lacking');

                if (!empty($entry["lacking_entry"])) {
                    try {
                        $cached_records = serialize($entry);
                        $this->redis->set("lacking_cache", $cached_records);
                    } catch (Exception $e) {
                        $entry['no_cache'] = $e->getMessage();
                    }
                }
            } else {
                $cached = @unserialize($lacking) ? @unserialize($lacking) : array();
                $entry = $cached;
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($entry));
        }

        public function get_personnel_today($biometric_id = null) {
            $cache_name = "{$biometric_id}_today_personal_cache";

            try {
                $personalToday = $this->redis->get($cache_name);
            } catch (Exception $e) {
                $personalToday = NULL;
            }

            if (!$personalToday) {
                $data = $this->dashboard_m->getPersonalLate($biometric_id);

                if (!empty($data["late"])) {
                    try {
                        $cache = serialize($data);
                        $this->redis->set($cache_name, $cache);
                    } catch ( Exception $e) {
                        $data['no_cache'] = $e->getMessage();
                    }
                }
            }else {
                $cached = @unserialize($personalToday) ? @unserialize($personalToday) : array();
                $data = $cached;
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_personnel_absent($biometric_id = null){
            $cache_name = "{$biometric_id}_absent_personal_cache";

            try {
                $personalAbsent = $this->redis->get($cache_name);
            } catch (Exception $e) {
                $personalAbsent = NULL;
            }

            if (!$personalAbsent) {
                $data = $this->dashboard_m->getPersonalAbsent($biometric_id);

                if(!empty($data["absentee"])){
                    try {

                        $cache = serialize($data);
                        $this->redis->set($cache_name, $cache);
                    } catch (Exception $e) {
                        $data['no_cache'] = $e->getMessage();
                    } 
                }
            }else {
                $cached = @unserialize($personalAbsent) ? @unserialize($personalAbsent) : array();
                $data = $cached;
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        
        public function get_personnel_undertime($biometric_id = null){
            $cache_name = "{$biometric_id}_undertime_personal_cache";

            try {
                $personalUnder = $this->redis->get($cache_name);
            } catch (Exception $e) {
                $personalUnder = NULL;
            }

            if (!$personalUnder) {
                $data = $this->dashboard_m->getPersonalUndertime($biometric_id);

                if (!empty($data["undertime"])) {
                    try {
                        $cache = serialize($data);
                        $this->redis->set($cache_name, $cache);
                    } catch (Exception $e) {
                        $data['no_cache'] = $e->getMessage();
                    }
                }
            }else {
                $cached = @unserialize($personalUnder) ? @unserialize($personalUnder) : array();
                $data = $cached;
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        
        public function get_personal_lacking_entry($biometric_id = null){
            $cache_name = "{$biometric_id}_lacking_personal_cache";

            try {
                $personalLacking = $this->redis->get($cache_name);
            } catch (Exception $e) {
                $personalLacking = NULL;
            }

            if (!$personalLacking) {
                $data = $this->dashboard_m->getPersonalLackingDoubleEntries($biometric_id, 'lacking');
    
                $lacking = isset($data["lacking_entry"]) ? array_column($data["lacking_entry"], 'date') : $data["lacking_entry"] = array();
                array_multisort($lacking, SORT_DESC, $data['lacking_entry']);

                if (!empty($data["lacking_entry"])) {
                    try {
                        $cache = serialize($data);
                        $this->redis->set($cache_name, $cache);
                    } catch (Exception $e) {
                        $data['no_cache'] = $e->getMessage();
                    }
                }
            }else {
                $cached = @unserialize($personalLacking) ? @unserialize($personalLacking) : array();
                $data = $cached;
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_personal_double_entry($biometric_id = null){
            $cache_name = "{$biometric_id}_double_personal_cache";

            try {
                $personalDouble = $this->redis->get($cache_name);
            } catch (Exception $e) {
                $personalDouble = NULL;
            }

            if (!$personalDouble) {
                $data = $this->dashboard_m->getPersonalLackingDoubleEntries($biometric_id, 'double');

                $double = isset($data["double_entry"]) ? array_column($data["double_entry"], 'date') : $data["double_entry"] = array();
                array_multisort($double, SORT_DESC, $data['double_entry']);

                if (!empty($data["double_entry"])) {
                    try {
                        $cache = serialize($data);
                        $this->redis->set($cache_name, $cache);
                    } catch (Exception $e) {
                        $data['no_cache'] = $e->getMessage();
                    }
                }
            }else {
                $cached = @unserialize($personalDouble) ? @unserialize($personalDouble) : array();
                $data = $cached;
            }

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function scheduled_cache_flush(){
            $now = date('Y-m-d');
            $date = date('Y-m-d H:i');

            $this->redis->flushall();
        }
    }