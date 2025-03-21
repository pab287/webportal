<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CI_Model {
    private $gcctimeDevices = "gcctimeutility.devices";
    private $gcctimeLocations = "gcctimeutility.location";
    private $attendance = 'zktime_logs.attendance';
    private $tblEmployee = 'gccmaster.tblemployees';

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Manila");
    }

    public function getActiveDevices(){
        $result = array();

        $this->db->select('a.id as device_id, a.device_name as name, b.name as location');
        $this->db->join($this->gcctimeLocations.' b', 'b.id = a.location_id', 'LEFT');
        $this->db->from($this->gcctimeDevices.' a');
        $this->db->where('a.is_active', 1);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $result = $query->result();
        }

        return $result;
    }

    public function get_attendance_logs_datatable_request(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $device = (isset($post["device"]) && $post["device"]) ? $post["device"] : null;
        $from = (isset($post["filterFrom"]) && $post["filterFrom"]) ? $post["filterFrom"] : null;
        $to = (isset($post["filterTo"]) && $post["filterTo"]) ? $post["filterTo"] : null;

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_item($limit, $offset, $sortBy, $sortOrder, $search, $device, $from, $to);
        $rowCount = $this->get_item_count($search, $device, $from, $to);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        if ($device == 'all') {
            $resultset['devices'] = $device;
        }

        return $resultset;
    }

    function get_item($limit, $offset, $sortBy, $sortOrder, $search = null, $device = null, $from = null, $to = null){
        $result = array();

        $filterfields = array('a.biometricno', 'a.date','b.firstname', 'b.lastname', 'b.middlename', 'CONCAT(b.firstname, " ", b.lastname)', 'b.suffix', 'c.device_name', 'd.name');

        $this->db->select('a.biometricno, DATE_FORMAT(a.date, "%Y-%m-%d") as date, DATE_FORMAT(a.date, "%r") as time, a.date as datetime, a.verify_method, b.firstname, b.lastname, b.middlename, b.suffix, c.device_name, d.name as location_name, b.pic_filename, b.id as employee_id');
        $this->db->join($this->tblEmployee.' as b', 'b.biometricno = a.biometricno', 'LEFT');
        $this->db->join($this->gcctimeDevices.' as c', 'c.id = a.device_id', 'LEFT');
        $this->db->join($this->gcctimeLocations.' as d', 'd.id = c.location_id', 'LEFT');
        $this->db->from($this->attendance.' as a');

        if ($from && $to) {
            $this->db->where('DATE(a.date) >=', $from);
            $this->db->where('DATE(a.date) <=', $to);
        } else {
            $this->db->where('DATE(a.date)', date("Y-m-d"));
        }

        if($device != 'all'){
            $this->db->where('a.device_id', $device);
        }

        if($search){
            $this->db->group_start();
            foreach ($filterfields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];

        if ($sortBy[$i]['data'] == "firstname") {
            $this->db->order_by("IFNULL(b.firstname, a.biometricno)", $sortOrder[0]['dir'], false);
        } else {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();

            foreach($query->result() as $key => $rs){
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $_temp = trim($tempFullname->display_name_1);

                $currentImage = base_url("assets/images/profile/no_image.jpg");
                $imageFile = $rs->pic_filename;
                $imagePath = realpath("uploads/files/images/employee_files/empcode_{$rs->employee_id}/{$imageFile}");
            
                if (file_exists($imagePath) && $imageFile) {
                    $currentImage = base_url("uploads/files/images/employee_files/empcode_{$rs->employee_id}/{$imageFile}");
                }

                $rs->image = $currentImage;
                $rs->employee_name = ($tempFullname->display_name_1 && $_temp) ? $tempFullname->display_name_1 : $rs->biometricno;
                $rs->type = $this->biometric_type($rs->verify_method);

                unset($rs->firstname, $rs->lastname, $rs->middlename, $rs->suffix);

                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $result[] = $v;
            }
        }

        return $result;
    }

    function get_item_count($search = null, $device = null, $from = null, $to = null){
        $result = array();

        $filterfields = array('a.biometricno', 'a.date', 'b.firstname', 'b.lastname', 'b.middlename', 'CONCAT(b.firstname, " ", b.lastname)', 'b.suffix', 'c.device_name', 'd.name');

        $this->db->select('a.biometricno, a.date, a.verify_method, b.firstname, b.lastname, b.middlename, b.suffix, c.device_name, d.name as location_name');
        $this->db->join($this->tblEmployee.' as b', 'b.biometricno = a.biometricno', 'LEFT');
        $this->db->join($this->gcctimeDevices.' as c', 'c.id = a.device_id', 'LEFT');
        $this->db->join($this->gcctimeLocations.' as d', 'd.id = c.location_id', 'LEFT');
        $this->db->from($this->attendance.' as a');
        
        if ($from && $to) {
            $this->db->where('DATE(a.date) >=', $from);
            $this->db->where('DATE(a.date) <=', $to);
        } else {
            $this->db->where('DATE(a.date)', date("Y-m-d"));
        }

        if($device != 'all'){
            $this->db->where('a.device_id', $device);
        }

        if($search){
            $this->db->group_start();
            foreach ($filterfields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
        }

        $query = $this->db->get();

        return $query->num_rows();
    }

    function biometric_type($type) {
        switch ($type) {
            case 1:
                return 'TIME OUT';
                break;
            case 2:
                return 'BREAK IN';
                break;
            case 3:
                return 'BREAK OUT';
                break;
            case 4:
                return 'OT IN';
                break;
            case 5: 
                return 'OT OUT';
                break;
            default:
                return 'TIME IN';
                break;
        }
    }
}