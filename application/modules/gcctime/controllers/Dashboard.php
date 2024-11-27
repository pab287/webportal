<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Dashboard extends MY_Controller {
        private $adminPrivilege = false;
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
            $biometricId = $this->getPersonnelBiometricId();
            $data = array();
            $data["logs"] = $this->core_layout->getLogNotification("gcctimev2");
            $data["real_time_attendances"] = $this->attendance_m->getRealTimeAttendances(date("Y-m-d"));
            $data["biometric_id"] = $biometricId;
            $data["admin_privilege"] = $this->adminPrivilege;

            $this->load->view('core/templates/header');
            $this->load->view('gcctime/dashboard/index', $data);
            $this->load->view('core/templates/footer');
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
    }