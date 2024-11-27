<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Timesheet_cron extends MY_Controller {
    private $date;
    private $logged_in_user;

    public function __construct() {
        parent::__construct();
        $this->load->model('Timesheet_model', 'ts_model');
        $this->date = new DateTime("now", new DateTimeZone("Asia/Manila"));
        date_default_timezone_set("Asia/Manila");
        $this->logged_in_user = $this->session->userdata("logged_in");
    }

    public function create($is_custom = 0) {
        // $is_custom = identifier if attendance record is direct from device or manual e.g. imported
        $today = new DateTime($this->date->format("Y-m-d"));
        $previous_date = $today->modify('-1 day')->format("Y-m-d");

        $post = $this->input->post();
        $postObj = $this->arrayToStdClass($post);

        // check if has date data in request then use date from request
        if (!empty($postObj) && property_exists($postObj, "date")) {
            $previous_date = $postObj->date;
        }

        $data = $this->ts_model->create($previous_date, $is_custom);
        echo json_encode($data);
    }

    public function create_multiple($is_custom = 0, $generated_manually = 0) {
        ini_set('max_execution_time', 7200);
        $post = $this->input->post();
        $postObj = $this->arrayToStdClass($post);
        $startDate = null;
        $endDate = null;
        $errors = array();
        $employees = array();

        if (property_exists($postObj, "employees")) {
            $employees = array_map(function ($employee) {
                return $employee;
            }, $postObj->employees);
        }

        if(isset($postObj->grouped_employees) && $postObj->grouped_employees){
         $employees = explode(",", $postObj->grouped_employees);
        }

        // check if has date data in request then use date from request
        if (!empty($postObj) && property_exists($postObj, "dateStart") && property_exists($postObj, "dateEnd")) {
            $interval = DateInterval::createFromDateString('1 day');

            $dateStart = new DateTime($postObj->dateStart);
            $dateEnd = new DateTime($postObj->dateEnd);
            $dateEnd->modify("+1 day");

            $period = new DatePeriod($dateStart, $interval, $dateEnd);
            foreach ($period as $dt) {
                $date = $dt->format("Y-m-d");
                $created = $this->ts_model->create($date, $is_custom, $employees, NULL, $generated_manually);
                $this->ts_model->updateTimesheetHoliday($date);
                if (intval($created["k"]) === 2) {
                    array_push($errors, $created);
                }
            }
        }

        if (property_exists($postObj, "remarks")) {
            $logged_in_user_complete_name = $this->logged_in_user["firstname"] . " " . $this->logged_in_user["lastname"];
            $filename = date("Y-m-d") . ".txt";
            $path = "sys_gen_logs/generate_manually";
            $data = "Generated manually BY: $logged_in_user_complete_name" . " at " . date("H:i:s");
            $data .= "||FROM: " . date("Y-m-d", strtotime($postObj->dateStart));
            $data .= ", TO: " . date("Y-m-d", strtotime($postObj->dateEnd));
            $data .= ", REMARKS: " . $postObj->remarks . ".\n";
            $this->core_layout->create_txt_log_file($path, $filename, $data, true);
        }

        echo json_encode($errors);
    }

    private function arrayToStdClass($array) {
        return json_decode(json_encode($array));
    }

    function get_attendance_log() {
        $post = $this->arrayToStdClass($this->input->post());
        $start = $post->selectedStartDate;
        $end = $post->selectedEndDate;

        $emp_id = $this->session->userdata("logged_in")["emp_id"];
        $biometricno = $this->db->select("personnel.biometric_id biometricno")
            ->join("gcctimeutility.personnel personnel", "personnel.biometricno = emp.biometricno", "inner")
            ->where("emp.id", $emp_id)
            ->get("gccmaster.tblemployees emp")
            ->row("biometricno");

        /*$data = $this->db
            ->select("*, DATE(`date`) header")
            ->where("biometricno", $biometricno)
            ->where("DATE(`date`) >=", $start)
            ->where("DATE(`date`) <=", $end)
            ->get("zktime_logs.attendance")->result();*/

        $this->db
            ->select("attendance.`date`, attendance.biometricno, attendance.device_state, attendance.device_id, 
                        devices.ip_address ip, devices.device_name device, DATE(attendance.`date`) header")
            ->join("gcctimeutility.devices devices", "devices.id = attendance.device_id", "INNER")
            ->where("attendance.biometricno", $biometricno)
            ->where("DATE(attendance.`date`) >=", $start)
            ->where("DATE(attendance.`date`) <=", $end)
            ->group_by("attendance.`date`");
        $zktime_attendance_query = $this->db->get_compiled_select("zktime_logs.attendance attendance");

        $this->db
            ->select("attendance.`datetime` `date`, attendance.biometric_id biometricno, 
                        attendance.state device_state, devices.id device_id,
                        devices.ip_address ip, devices.device_name device,
                        DATE(attendance.`datetime`) header")
            ->join("gcctimeutility.devices devices", "devices.id = attendance.device_id", "INNER")
            ->where("attendance.biometric_id", $biometricno)
            ->where("DATE(attendance.`datetime`) >=", $start)
            ->where("DATE(attendance.`datetime`) <=", $end)
            ->group_by("attendance.`datetime`");
        $gcctimeutility_attendance_query = $this->db->get_compiled_select("gcctimeutility.`attendance` attendance");

        $union_query = $this->db->query("(" . $zktime_attendance_query . ") UNION (" . $gcctimeutility_attendance_query . ") ORDER BY `date` ASC")->result();

        $resultSet = array(
            "data" => $union_query,
            "sql" => $this->db->last_query(),
            "biometricno" => $biometricno
        );
        echo json_encode($resultSet);
    }
}

/* End of file Timesheet_cron.php */
/* Location: ./application/controllers/Timesheet_cron.php */