<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Overtime_m extends CI_Model {
    protected $eformsOvertimeTable = "gcceforms.overtime";
    protected $tbl_payroll_group = "payroll.payroll_group";
    protected $companyTable = "gcchris.tblcompanies";
    protected $tbl_employees = "gccmaster.tblemployees";
    protected $employeeTable = "gccmaster.tblemployees";

    private $current_action =  array();
    private $user_data = array();

    public function __construct()
	{
        parent::__construct();
        $this->core_layout->setPrivilegeName("overtime_masterfile");
        $this->current_action = $this->core_layout->getCurrentActions();
        $this->load->model("core/upload_model", "file_upload");
        $this->load->model("gcctime/timesheet_model", "ts_model");
        $this->user_data = $this->session->userdata("logged_in");
        date_default_timezone_set('Asia/Singapore');
        $this->load->library('image_lib');
    }

    private function format_name($id){
        $tempRs = (array) $this->getEmployeeName($id);
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object) $fullname;
        return isset($tempFullname->display_name_1) ? $tempFullname->display_name_1: "No Assigned Name";
    }

    function getAnalyticsForDashboard(){
        $this->db->select("a.status, COUNT(a.id) AS count");
        $this->db->from("gcceforms.overtime a");
        $this->db->where("a.status !=","");
        $this->db->group_by("a.status");
        $this->db->order_by("FIELD(a.status, 'Pending', 'Approved', 'Disapproved', 'Cancelled')");
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $arrData[$key] = $rs;
            }

            return json_decode(json_encode($arrData));
        }else{
            return array();
        }
    }

    function getDaily(){
        $resultset = array();
        $post = $this->input->post();
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: "";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_all_post_daily($limit, $offset, $sortBy, $sortOrder);
        $totalNotFiltered = $rowCount;

        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_post_daily($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        $check = date('Y-m-d');
        $this->db->select("a.*, b.firstname");
        $this->db->from('gcceforms.overtime a');
        $this->db->join('gccmaster.tblemployees b', 'a.employee = b.id', 'LEFT');
        $this->db->like('a.created_at', $check);
        $this->db->limit($limit, $offset);

        empty($sortOrder) ? $i=0 : $i = $sortOrder[0]['column'];
        empty($sortOrder) ? $this->db->order_by("a.created_at", "desc") : $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $rs->display_employee = $this->format_name($rs->employee);
                $rs->display_employee = "<span><small>".$rs->company."</small><br><b>".$rs->display_employee."</b><br><small>".$rs->position."</small></span>";
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }
            return $data;
        }else{
            return array();
        }
    }

    function getWeekly(){
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: "";

        $rowCount = 0;
        $rowData = array();
        $rowData = $this->get_all_post_weekly($limit, $offset, $sortBy, $sortOrder);

        $totalNotFiltered = $rowCount;
        $resultset["data"] = $rowData;
        return $resultset;
    }

    private function get_all_post_weekly($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        $check =date('Y-m-d',strtotime("-7 days"));
        $this->db->select("a.*,b.firstname");
        $this->db->from('gcceforms.overtime a');
        $this->db->join('gccmaster.tblemployees b', 'a.employee = b.id', 'LEFT');
        $this->db->where('a.created_at >= ', $check);
        $this->db->limit($limit, $offset);

        empty($sortOrder) ? $i=0 : $i = $sortOrder[0]['column'];
        empty($sortOrder) ? $this->db->order_by("a.created_at", "desc") : $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $rs->display_employee = $this->format_name($rs->employee);
                $rs->display_employee = "<span><small>".$rs->company."</small><br><b>".$rs->display_employee."</b><br><small>".$rs->position."</small></span>";
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }
            return $data;
        }else{
            return array();
        }
    }

    function overtimeMasterfile() {
        $rowCount = 0;
        $rowData = array();
        $resultset = array();

        $post = $this->input->post();

        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "";
        $filtered = (isset($post["filtered"]) && $post["filtered"])? $post["filtered"]: false;
        $filter = (isset($post["ids"]) && count($post["ids"]) > 0 && $filtered)? $post["ids"]: false;
        $qBuilder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'] && $filtered)? $post["query_builder"]['sql']: false;
        $status = (isset($post['status']) && $post['status']) ? ucwords($post['status']) : null; //clicked in portal dashboard

        if($search){
            $this->core_layout->setEventLog("Overtime Masterfile - Search {$post["search"]['value']} in datatable.", "search", "success", "gcceforms", "user");
        }

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        $rowData = $this->masterfile_list($filtered,$filter,$search, $limit, $offset, $sortBy, $sortOrder, $qBuilder, $status, $view_by_company, $companyDescription);
        $rowCount = $this->masterfile_count($filtered,$filter,$search, $qBuilder, $status, $view_by_company, $companyDescription);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function masterfile_list($filtered,$filter,$search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $qBuilder=null, $status = null, $view_by_company = false, $companyDescription = null) {
        // $filterFields = array("a.id", "a.status", "a.purpose","a.company", "a.department", "a.reference_no", "a.position", "a.created_at", "b.firstname", "b.lastname", "b.middlename", "b.suffix");
        $filterFields = array("a.id", "a.status", "a.purpose","a.company", "a.department", "a.reference_no", "a.position", "a.created_at", "b.firstname", "b.lastname");
        $this->db->select("a.id, a.reference_no, a.status, a.purpose, a.date_from, a.date_to, a.company, a.employee, a.department, a.created_at, a.position");
        $this->db->from('gcceforms.overtime a');
        $this->db->join('gccmaster.tblemployees b', 'a.employee = b.id', 'LEFT');

        if(in_array("view_own_request", $this->current_action)){
            $tempSession = $this->core_layout->getCurrentSession();
            $tempSession = (object) $tempSession;
            $this->db->group_start();
            $this->db->where("a.employee", $tempSession->emp_id);
            $this->db->or_where("a.created_by", $tempSession->emp_id);
            $this->db->group_end();
        }

        if($status){
            $this->db->where('a.status', $status);
        }
        $this->db->where_not_in('a.status', "Cancelled");

        if ($view_by_company) {
            $this->db->where('b.company_id', (int)$this->user_data['company']);

            if ($companyDescription) {
                $this->db->where('a.company', $companyDescription);
            }
        }

        if($qBuilder){ $this->db->where($qBuilder); }
        if ($search) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
        }
        if($filtered=="true"){
            if (is_array($filter)) {
                $chucked = array_chunk($filter, 100);
                $this->db->group_start();
                foreach ($chucked as $value) {
                    $this->db->or_where_in("a.id", $value);
                }
                $this->db->group_end();
            }
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        empty($sortOrder) ? $i=0 : $i = $sortOrder[0]['column'];
        empty($sortOrder) ? $this->db->order_by("a.created_at", "desc") : $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $time1 = date_create($rs->date_from);
                $time2 = date_create($rs->date_to);
                $time_diff = date_diff($time1, $time2);
                // $tempHr = $time_diff->h; //commented because it returns 0 when in 24hrs
                // $tempMn = $time_diff->i;
                $totalMinutes = ($time_diff->days * 24 * 60) + ($time_diff->h * 60) + $time_diff->i;

                $tempHr = intdiv($totalMinutes, 60);
                $tempMn = $totalMinutes % 60;

                $tempHrLabel = ($tempHr == 1)? "Hour": "Hours";
                $tempMnLabel = ($tempMn == 1)? "Minute": "Minutes";
                $tempDuration = "{$tempHr} {$tempHrLabel} {$tempMn} {$tempMnLabel}";
                if($tempHr > 0 && $tempMn > 0){
                    $tempDuration = "{$tempHr} {$tempHrLabel} {$tempMn} {$tempMnLabel}";
                }elseif($tempHr > 0 && $tempMn == 0){
                    $tempDuration = "{$tempHr} {$tempHrLabel}";
                }elseif($tempHr == 0 && $tempMn > 0){
                    $tempDuration = "{$tempMn} {$tempMnLabel}";
                }
                $rs->time_diff = $time_diff;
                $rs->display_employee = $this->format_name($rs->employee);
                $rs->display_employee = "<span><b>".$rs->display_employee."</b><br>".$rs->position."</span>";
                $rs->display_details = "<span><b>".$rs->company."</b><br>".$rs->department."</span>";
                $rs->purpose = str_replace("\n","<br>",$rs->purpose);
                $year = date("y",strtotime($rs->created_at));
                $month = date("m",strtotime($rs->created_at));
                $rs->reference_no = $rs->reference_no;
                $rs->duration = $tempDuration;
                $arrData[$key] = $rs;
            }
            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
            return $data;
        } else {
            return array();
        }
    }
    
    private function masterfile_count($filtered,$filter,$search=null, $qBuilder=null, $status = null, $view_by_company = false, $companyDescription = null) {
        $filterFields = array("a.id", "a.status", "a.purpose","a.company", "a.department", "a.position", "b.firstname", "b.lastname");
        $this->db->select("a.id, a.reference_no, a.status, a.purpose, a.date_from, a.date_to, a.company, a.employee, a.department, a.created_at, a.position");
        $this->db->from('gcceforms.overtime a');
        $this->db->join('gccmaster.tblemployees b', 'a.employee = b.id', 'LEFT');

        if(in_array("view_own_request", $this->current_action)){
            $tempSession = $this->core_layout->getCurrentSession();
            $tempSession = (object) $tempSession;
            $this->db->group_start();
            $this->db->where("a.employee", $tempSession->emp_id);
            $this->db->or_where("a.created_by", $tempSession->emp_id);
            $this->db->group_end();
        }

        if($status){
            $this->db->where('a.status', $status);
        }
        
        $this->db->where_not_in('a.status', "Cancelled");

        if ($view_by_company) {
            $this->db->where('b.company_id', (int)$this->user_data['company']);

            if ($companyDescription) {
                $this->db->where('a.company', $companyDescription);
            }
        }

        if($qBuilder){ $this->db->where($qBuilder); }
        if ($search) {
          $this->db->group_start();
          foreach ($filterFields as $key => $field) {
              if ($key == 0) {
                  $this->db->like($field, $search, "both");
              } else {
                  $this->db->or_like($field, $search, "both");
              }
          }
          $this->db->group_end();
      }
      if($filtered=="true"){
        if (is_array($filter)) {

            $chucked = array_chunk($filter, 100);
            $this->db->group_start();
            foreach ($chucked as $value) {
                $this->db->or_where_in("a.id", $value);
            }
            $this->db->group_end();
        }
      }

        $query = $this->db->get();
        $count = $query->num_rows();
        return $count;
    }

    function get_series($year, $month, $data) {
        $this->db->select('*');
        $this->db->from('gcceforms.overtime');
        $this->db->where('ref_yr', $year);
        $this->db->where('ref_month', $month);
        if($data == "old"){
            $this->db->order_by('ref_series','asc');
            $query = $this->db->get();
            return $query->num_rows();
        }else{
            $this->db->order_by('ref_series','asc');
            $query = $this->db->get();
            return $query->result();
        }
       
    }

    function generateReferenceDetails(){
        $query = $this->db->order_by("created_at","desc")->get_where($this->eformsOvertimeTable);
        foreach($query->result() as $overtime){
            $date = $overtime->created_at;
            $year = substr($date, 2, 2);
            $month = substr($date, 5, 2);
            $arrData = array(
                "ref_yr" => $year,
                "ref_month" => $month
            );
            $this->db->where("id", $overtime->id);
            $update = $this->db->update($this->eformsOvertimeTable, $arrData);
        }
        return $update;
    }

    function generateReferenceNoExisting(){
        $update = false;
        $query = $this->db->order_by("created_at","desc")->group_by("MONTH(created_at)","YEAR(created_at)")->get_where($this->eformsOvertimeTable);
        $data = array();
        foreach($query->result() as $overtime){
            $date = $overtime->created_at;
            $year = substr($date, 2, 2);
            $month = substr($date, 5, 2);
            $series = $this->get_series($year, $month, "old");
            for($i=$series; $i>=1; --$i){
                $ref_series = intval($i);
                if (strlen($ref_series) == 1) {
                    $ref_series = '000' . $ref_series;
                } else if (strlen($ref_series) == 2) {
                    $ref_series = '00' . $ref_series;
                } else if (strlen($ref_series) == 3) {
                    $ref_series = '0' . $ref_series;
                } else {
                    $ref_series = $ref_series;
                }
                $reference = "OT" .$year. "-" .$month. "-" .$ref_series;
                $data[] = $reference."=".$ref_series;   
            }
        }
    
        foreach(array_reverse($data) as $index => $val){
            $ref = explode("=", $val);
            $reference_no = $ref[0];
            $ref_series = $ref[1];

            $index = $index + 1;
            $update = $this->db->query("UPDATE gcceforms.overtime SET reference_no='".$reference_no."', ref_series='".$ref_series."' WHERE `id` = '".$index."' ORDER BY id ASC");
            $hey[] = $data;
        }


        return $update;
    }
    
    function OTReference(){
        $a = $this->generateReferenceNoExisting();
        // $query = $this->db->order_by("created_at","DESC")->get_where($this->eformsOvertimeTable);
        // foreach($a as $b){
        //     $c = $b['reference'];
        //     $d = $b['id'];
        //     $this->db->query("UPDATE gcceforms.overtime SET `reference_no`='$c' WHERE `id`='$d' ");
        // }

        return $a;
    }

    function overtimeArchive() {
        $rowCount = 0;
        $rowData = array();
        $resultset = array();
        $post = $this->input->post();

        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "";

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        $rowData = $this->archive_list($search, $limit, $offset, $sortBy, $sortOrder, $view_by_company, $companyDescription);
        $rowCount = $this->archive_count($search, $view_by_company, $companyDescription);
        if($search){
            $this->core_layout->setEventLog("Archived Overtime - Search {$search} in datatable.", "search", "success", "gcceforms", "user");
        }
        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function archive_list($search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $view_by_company = false, $companyDescription = null) {
        $filterFields = array("a.id", "status", "a.purpose","a.company", "a.department", "a.position", "b.firstname", "b.lastname", "b.middlename", "b.suffix", "a.reference_no");
        $this->db->select("a.id, a.reference_no, a.status, a.purpose, a.date_from, a.date_to, a.company, a.employee, a.department, a.created_at, a.position");
        $this->db->from('gcceforms.overtime a');
        $this->db->join('gccmaster.tblemployees b', 'a.employee = b.id', 'LEFT');
        $this->db->where('status', "Cancelled");

        if ($view_by_company) {
            $this->db->where('b.company_id', $view_by_company);

            if ($companyDescription) {
                $this->db->where('a.company', $companyDescription);
            }
        }

        if(isset($search)){
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                    $this->db->or_like("CONCAT(b.firstname,' ',b.lastname )", $search, "both");
                }
            }
            $this->db->group_end();
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        empty($sortOrder) ? $i=0 : $i = $sortOrder[0]['column'];
        empty($sortOrder) ? $this->db->order_by("a.created_at", "desc") : $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $rs->display_employee = $this->format_name($rs->employee);
                $rs->display_employee = "<span><b>".$rs->display_employee."</b><br>".$rs->position."</span>";
                $rs->display_details = "<span><b>".$rs->company."</b><br>".$rs->department."</span>";
                $rs->purpose = str_replace("\n","<br>",$rs->purpose);
                $arrData[$key] = $rs;
            }
            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
            return $data;
        } else {
            return array();
        }
    }

    private function archive_count($search=null, $view_by_company = false, $companyDescription = null) {
        $filterFields = array("a.id", "status", "a.purpose","a.company", "a.department", "a.position", "b.firstname", "b.lastname", "b.middlename", "b.suffix");
        $this->db->select("a.id, a.reference_no, a.status, a.purpose, a.date_from, a.date_to, a.company, a.employee, a.department, a.created_at, a.position");
        $this->db->from('gcceforms.overtime a');
        $this->db->join('gccmaster.tblemployees b', 'a.employee = b.id', 'LEFT');
        $this->db->where('status', "Cancelled");

        if ($view_by_company) {
            $this->db->where('b.company_id', $view_by_company);

            if ($companyDescription) {
                $this->db->where('a.company', $companyDescription);
            }
        }

        if(isset($search)){
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                    $this->db->or_like("CONCAT(b.firstname,' ',b.lastname )", $search, "both");
                }
            }
            $this->db->group_end();
        }

        $query = $this->db->get();
        $count = $query->num_rows();
        return $count;
    }

    function getEmployee(){
      $post = $this->input->get();
      $resultarray = array();
      $privilege = $this->core_layout->getCurrentActions();

      $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;

      $this->db->select('id, firstname, lastname, middlename, suffix');
      $this->db->from('gccmaster.tblemployees');
      $this->db->where('employee_status', 'Active');

      if(isset($post['q'])){
        $this->db->group_start();
        $this->db->like('firstname', $post['q']);
        $this->db->or_like('lastname', $post['q']);
        $this->db->group_end();
      }

      if ($view_by_company) {
        $this->db->where('company_id', $this->user_data['company']);
     }

      if(isset($post['company']) && !empty($post['company'])){
          $this->db->where('company_id', $post['company']);
      }

      $this->db->order_by('firstname', 'ASC');
      $this->db->limit(10);
      $query = $this->db->get();
      if($query->num_rows() > 0){
          foreach($query->result_array() as $_query){
              $data = array();
              $tempRs = (array) $_query;
              $fullname = $this->core_layout->getDisplayName($tempRs);
              $tempFullname = (object) $fullname;
              $data["id"] = $_query["id"];
              $data["text"] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
              $resultarray[] = $data;
          }
      }
      return array("results" => $resultarray);
  }
  

    function getCompanyList(){
        $get = $this->input->get();
        $resultarray = array();
        //   if(isset($get['q'])){ 
        //     $query = $this->db->query("SELECT id, description FROM gcchris.tblcompanies WHERE is_archived = 0 AND description LIKE '%{$get['q']}%' LIMIT 10");
        //   }else{
        //     $query = $this->db->query("SELECT id, description FROM gcchris.tblcompanies WHERE is_archived = 0 LIMIT 10");
        //   }

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;

        $this->db->select("id, description");
        $this->db->from("gcchris.tblcompanies");
        $this->db->where("is_archived", 0);
        $this->db->where("exclude", 0);

        if ($view_by_company) {
            $this->db->where('id', $this->user_data['company']);
        }

        if (isset($get['q'])) {
            $this->db->like("description", $get['q'], "both");
        }
        $this->db->limit(10);
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $tempRs = (array) $_query;
                $data["id"] = $_query["id"];
                $data["text"] = $_query["description"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function getEmployeeDepartmentHead(){
        $get = $this->input->get();
        $resultarray = array();
        $this->db->select("a.id, a.firstname, a.lastname, a.middlename, a.suffix");
        $this->db->from("gccmaster.tblemployees a");
        $this->db->join("gcchris.tbldepartments b","b.head_id = a.id", "LEFT");
        $this->db->where("a.employee_status", "Active");
        if(isset($get['q'])){
            $this->db->group_start();
            $this->db->like("a.firstname", $get['q'], "both");
            $this->db->or_like("a.lastname", $get['q'], "both");
            $this->db->group_end();
        }
        $this->db->limit(10);
        $this->db->group_by("a.id");
        $this->db->order_by("a.firstname", "ASC");
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $tempRs = (array) $_query;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;

                $data["id"] = $_query["id"];
                $data["text"] = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    public function getEmployeeDetail(){
        $get = $this->input->get();
        $id = $get['data'];
        $resultarray = array();
        $this->db->select("emp.id, IF(comp.id IS NULL, emp.company_id, comp.description) as company,
        IF(dept.id IS NULL, emp.department_id, dept.description) as department,
        IF(pos.id IS NULL, emp.position, pos.name) as position,
        UPPER(CONCAT(IF(comp.id IS NULL, emp.company_id, comp.description), '\n',
        IF(dept.id IS NULL, emp.department_id, dept.description), '\n',
        IF(pos.id IS NULL, emp.position, pos.name))) as details, MAX(ps.date_end) as max_date");
        $this->db->from("gccmaster.tblemployees emp");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = emp.company_id", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = emp.department_id", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = emp.position", "LEFT");
        $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = emp.id AND ps.posted = 1", "LEFT");
        $this->db->where("emp.id", $id);
        $this->db->limit(1);
        $queryDetails = $this->db->get();
        if($queryDetails->num_rows() == 1){ $resultarray = $queryDetails->row_array(); }
        return $resultarray;
    }

    private function getEmployeeName($id) {
        $this->db->select("id, firstname, middlename, lastname, suffix");
        $this->db->from("gccmaster.tblemployees");
        $this->db->where("id", $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    private function getCompany($id) {
        $this->db->select("id, description");
        $this->db->from("gcchris.tblcompanies");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return strtoupper($data->description);
    }

    private function getDepartment($id) {
        $this->db->select("id, description");
        $this->db->from("gcchris.tbldepartments");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->description;
    }

    private function getPosition($id) {
        $this->db->select("id, name");
        $this->db->from("gcchris.tblposition");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->name;
    }

    function generateOvertimeReferenceNumber($type=1){
        $tempType = $type == 1 ? "new": "old";
        $date = date('Y-m-d H:i:s');
        $year = substr($date, 2, 2);
        $month = substr($date, 5, 2);
        $series = '';

        $list = $this->overtime->get_series($year, $month, $tempType);
        if (sizeof($list) > 0) {
            foreach ($list as $arr) {
                $x = $arr->ref_series;
            }
            $series = intval($x) + 1;
            if (strlen($series) == 1) {
                $series = '000' . $series;
            } else if (strlen($series) == 2) {
                $series = '00' . $series;
            } else if (strlen($series) == 3) {
                $series = '0' . $series;
            } else {
                $series = $series;
            }
        } else {
            $series = '0001';
        }

        $arrData = new stdClass();
        $arrData->reference_no = "OT{$year}-{$month}-{$series}";
        $arrData->year = $year;
        $arrData->month = $month;
        $arrData->series = $series;

        return $arrData;
    }

    function getOvertimeReference($id){
        $this->db->select("reference_no");
        return $this->db->get_where("gcceforms.overtime", array("id"=>$id))->row('reference_no');
    }

    function saveOvertime(){
        $current_action = $this->core_layout->getCurrentActions();
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $year = substr($date, 2, 2);
        $month = substr($date, 5, 2);
        $attachment = ($this->input->post('attachment_image') !== '' && $this->input->post('attachment_image')) ? $this->input->post('attachment_image'): array();
        $attachment = serialize($attachment);

        $this->db->select('reference_no, date_from, date_to, purpose, status');
        $this->db->where('DATE(date_from)', date('Y-m-d', strtotime($this->input->post('date_from'))));
        $this->db->where('employee', $this->input->post('employee'));
        $this->db->where('status !=', 'Cancelled');
        $this->db->from('gcceforms.overtime');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $resultset['state'] = false;
            $resultset['ot_data'] = $query->row();
            $resultset['message'] = "You already have an overtime application for this date. Only one overtime is allowed per day.";

            return $resultset;
        }

        $list = $this->overtime->get_series($year, $month, "new");
        $series = '';
        if (sizeof($list) > 0) {
            foreach ($list as $arr) {
                $x = $arr->ref_series;
            }
            $series = intval($x) + 1;
            if (strlen($series) == 1) {
                $series = '000' . $series;
            } else if (strlen($series) == 2) {
                $series = '00' . $series;
            } else if (strlen($series) == 3) {
                $series = '0' . $series;
            } else {
                $series = $series;
            }
        } else {
            $series = '0001';
        }

        if((in_array("approve_action", $this->current_action))){
            $data = array(
                'ref_yr' => $year,
                'ref_series' => $series,
                'ref_month' => $month,
                'reference_no' => 'OT' . $year . '-' . $month . '-' . $series,
                'status' => 'Pending',
                'employee' => $this->input->post('employee'),
                'company' => $this->input->post('company'),
                'department' => $this->input->post('department'),
                'position' => $this->input->post('position'),
                'requested_by' => $this->input->post('requested_by'),
                'requested_at' => $date,
                'requested_remarks' => $this->input->post('remarks'),
                'purpose' => $this->input->post('purpose'),
                'date_from' => $this->input->post('date_from'),
                'date_to' => $this->input->post('date_to'),
                'created_by' => $this->user_data['emp_id'],
                'created_at' => $date,
                'attachment_image' => $attachment,
                'status' => 'Approved',
                'approved_by' => $this->user_data['emp_id'],
                'approved_at' => $date,
            );
        }else{
            $data = array(
                'ref_yr' => $year,
                'ref_series' => $series,
                'ref_month' => $month,
                'reference_no' => 'OT' . $year . '-' . $month . '-' . $series,
                'status' => 'Pending',
                'employee' => $this->input->post('employee'),
                'company' => $this->input->post('company'),
                'department' => $this->input->post('department'),
                'position' => $this->input->post('position'),
                'requested_by' => $this->input->post('requested_by'),
                'requested_at' => $date,
                'requested_remarks' => $this->input->post('remarks'),
                'purpose' => $this->input->post('purpose'),
                'date_from' => $this->input->post('date_from'),
                'date_to' => $this->input->post('date_to'),
                'created_by' => $this->user_data['emp_id'],
                'created_at' => $date
            );
        }

        $this->db->insert('gcceforms.overtime', $data);
        $data_id = $this->db->insert_id();

        if($data_id){
            $resultset['state'] = true;
            $resultset['message'] = ((in_array("approve_action", $this->current_action))) ? "Overtime request saved and approved!" : "Overtime request saved!";
            $msg = ((in_array("approve_action", $this->current_action))) ? "New Overtime - Approved overtime request {$this->getOvertimeReference($data_id)}" : "New Overtime - Add overtime request {$this->getOvertimeReference($data_id)}";
            $this->core_layout->setEventLog($msg, "add", "success", "gcceforms", "user");
        }else{
            $resultset['state'] = false;
            $resultset['message'] = "Error saving data!";
        }

        return $resultset;
    }

    function getReports(){
        $rowCount = 0;
        $rowData = array();
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "";
        $company = (isset($post["company"]) && $post["company"]) ? $post["company"] : null;
        $payroll_group = (isset($post["payroll_group"]) && $post["payroll_group"]) ? $post["payroll_group"] : null;
        $dateRange = (isset($post["dateRange"]) && $post["dateRange"]) ? $post["dateRange"] : null;

        $rowData = $this->getReportsData($search, $limit, $offset, $sortBy, $sortOrder,$company,$dateRange,$payroll_group);
        $rowCount = $this->getReportsDataCount($search,$company,$dateRange,$payroll_group);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;
        return $resultset;
    }

    function getReportsData($search = null, $limit, $offset, $sortBy, $sortOrder,$company,$dateRange,$payroll_group){
        $filterFields = array('');
        $employee_ids = array();

        if($payroll_group !== null && $payroll_group !== "all"){
            $this->db->select('employee_id');
            $this->db->from('payroll.payroll_group');
            $this->db->where('payroll_group.id', $payroll_group);
            $payroll_group_query = $this->db->get();

            if ($payroll_group_query->num_rows() >   0) {
                $payroll_group_result = $payroll_group_query->row();
                $employee_ids = unserialize($payroll_group_result->employee_id);
            }
            $this->db->reset_query();
        }

        $data=array();
        $this->db->select("a.*, CONCAT(b.firstname, ' ', b.middlename, ' ', b.lastname, ' ', b.suffix) as emp_name");
        $this->db->from('gcceforms.overtime a');
        $this->db->join('gccmaster.tblemployees b', 'a.employee = b.id', 'LEFT');
        $this->db->where('a.status','Approved');

        if ($dateRange !== null) {
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];
            $this->db->where('DATE(a.date_from) >=', $startDate);
            $this->db->where('DATE(a.date_to) <=', $endDate);
        }

        if($company !== null){
            $this->db->where('b.company_id',$company);
        }

        if($payroll_group !== null && $payroll_group !== "all"){
            $this->db->where_in('a.employee', $employee_ids);
        }

        if ($search) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
        }

        if ($limit != -1) {
        $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $data = $query->result();
        }

        return $data;
    }

    function getReportsDataCount($search = null,$company,$dateRange,$payroll_group){
        $check = date('Y-m-d');
        $data = array();
        $filterFields = array('');

        if ($payroll_group !== null && $payroll_group !== "all") {
            $this->db->select('employee_id');
            $this->db->from('payroll.payroll_group');
            $this->db->where('payroll_group.id', $payroll_group);
            $payroll_group_query = $this->db->get();

            if ($payroll_group_query->num_rows() >   0) {
                $payroll_group_result = $payroll_group_query->row();
                $employee_ids = unserialize($payroll_group_result->employee_id);
                
            }
            $this->db->reset_query();
        }


        $this->db->select("a.*");
        $this->db->from('gcceforms.overtime a');
        $this->db->join('gccmaster.tblemployees b', 'a.employee = b.id', 'LEFT');
        $this->db->where('a.status','Approved');

        if($dateRange !== null){
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];
            $this->db->where('a.date_from >=', $startDate);
            $this->db->where('a.date_to <=', $endDate);
        }

        if($company !== null){
            $this->db->where('b.company_id',$company);
        }

        if ($search) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
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

    public function getOvertimeRequestDetails($id){
        $resultarray = array();
        $this->db->select("ot.id, ot.employee, ot.reference_no, ot.purpose, ot.attachment_image, ot.status, ot.date_from, ot.date_to,
            ot.actual_time_start, ot.actual_time_end, ot.actual_time_work, ot.is_imported,
            ot.created_at, ot.updated_at, ot.requested_at, ot.approved_at, ot.disapproved_at, ot.cancelled_at, ot.requested_remarks, ot.requested_by,
            IF(comp.id IS NULL, emp.company_id, comp.description) as company,
            IF(dept.id IS NULL, emp.department_id, dept.description) as department,
            IF(pos.id IS NULL, emp.position, pos.name) as position,
            UPPER(CONCAT(IF(comp.id IS NULL, emp.company_id, comp.description), '\n',
            IF(dept.id IS NULL, emp.department_id, dept.description), '\n',
            IF(pos.id IS NULL, emp.position, pos.name))) as details, MAX(ps.date_end) as max_date,
            UPPER(TRIM(CONCAT(emp.firstname, ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END,' ', emp.lastname,
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END))) as display_name,
            UPPER(TRIM(CONCAT(req.firstname, ' ',
            CASE WHEN UPPER(TRIM(req.middlename)) != 'N/A' AND UPPER(TRIM(req.middlename)) != 'NONE' AND
                    TRIM(req.middlename) !='' AND req.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(req.middlename, 1, 1), '.') ELSE ''
            END,' ', req.lastname,
            CASE WHEN UPPER(TRIM(req.suffix)) != 'N/A' AND
                UPPER(TRIM(req.suffix !='NONE')) AND req.suffix !='' AND
                req.suffix IS NOT NULL THEN CONCAT(' ', req.suffix) ELSE ''
            END))) as display_requested_by,
            UPPER(TRIM(CONCAT(crt.firstname, ' ', CASE WHEN UPPER(TRIM(crt.middlename)) != 'N/A' AND UPPER(TRIM(crt.middlename)) != 'NONE' AND
            TRIM(crt.middlename) !='' AND crt.middlename IS NOT NULL THEN CONCAT(SUBSTR(crt.middlename, 1, 1), '.') ELSE '' END,' ', crt.lastname,
            CASE WHEN UPPER(TRIM(crt.suffix)) != 'N/A' AND UPPER(TRIM(crt.suffix !='NONE')) AND crt.suffix !='' AND crt.suffix IS NOT NULL THEN
            CONCAT(' ', crt.suffix) ELSE '' END))) as created_by,
            UPPER(TRIM(CONCAT(upd.firstname, ' ', CASE WHEN UPPER(TRIM(upd.middlename)) != 'N/A' AND UPPER(TRIM(upd.middlename)) != 'NONE' AND
            TRIM(upd.middlename) !='' AND upd.middlename IS NOT NULL THEN CONCAT(SUBSTR(upd.middlename, 1, 1), '.') ELSE '' END,' ', upd.lastname,
            CASE WHEN UPPER(TRIM(upd.suffix)) != 'N/A' AND UPPER(TRIM(upd.suffix !='NONE')) AND upd.suffix !='' AND upd.suffix IS NOT NULL THEN
            CONCAT(' ', upd.suffix) ELSE '' END))) as updated_by,
            IF(ot.approved_by > 0, UPPER(TRIM(CONCAT(appr.firstname, ' ', CASE WHEN UPPER(TRIM(appr.middlename)) != 'N/A' AND UPPER(TRIM(appr.middlename)) != 'NONE' AND
            TRIM(appr.middlename) !='' AND appr.middlename IS NOT NULL THEN CONCAT(SUBSTR(appr.middlename, 1, 1), '.') ELSE '' END,' ', appr.lastname,
            CASE WHEN UPPER(TRIM(appr.suffix)) != 'N/A' AND UPPER(TRIM(appr.suffix !='NONE')) AND appr.suffix !='' AND appr.suffix IS NOT NULL THEN
            CONCAT(' ', appr.suffix) ELSE '' END))), 'N/A') as approved_by,
            IF(ot.disapproved_by > 0, UPPER(TRIM(CONCAT(dis.firstname, ' ', CASE WHEN UPPER(TRIM(dis.middlename)) != 'N/A' AND UPPER(TRIM(dis.middlename)) != 'NONE' AND
            TRIM(dis.middlename) !='' AND dis.middlename IS NOT NULL THEN CONCAT(SUBSTR(dis.middlename, 1, 1), '.') ELSE '' END,' ', dis.lastname,
            CASE WHEN UPPER(TRIM(dis.suffix)) != 'N/A' AND UPPER(TRIM(dis.suffix !='NONE')) AND dis.suffix !='' AND dis.suffix IS NOT NULL THEN
            CONCAT(' ', dis.suffix) ELSE '' END))), 'N/A') as disapproved_by,
            IF(ot.cancelled_by > 0, UPPER(TRIM(CONCAT(canc.firstname, ' ', CASE WHEN UPPER(TRIM(canc.middlename)) != 'N/A' AND UPPER(TRIM(canc.middlename)) != 'NONE' AND
            TRIM(canc.middlename) !='' AND canc.middlename IS NOT NULL THEN CONCAT(SUBSTR(canc.middlename, 1, 1), '.') ELSE '' END,' ', canc.lastname,
            CASE WHEN UPPER(TRIM(canc.suffix)) != 'N/A' AND UPPER(TRIM(canc.suffix !='NONE')) AND canc.suffix !='' AND canc.suffix IS NOT NULL THEN
            CONCAT(' ', canc.suffix) ELSE '' END))), 'N/A') as cancelled_by");
        $this->db->from("gcceforms.overtime ot");
        $this->db->join("gccmaster.tblemployees emp", "emp.id = ot.employee", "INNER");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = emp.company_id", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = emp.department_id", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = emp.position", "LEFT");
        $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = emp.id AND ps.posted = 1", "LEFT");
        $this->db->join("gccmaster.tblemployees req", "req.id = ot.requested_by", "LEFT");
        $this->db->join("gccmaster.tblemployees crt", "crt.id = ot.created_by", "LEFT");
        $this->db->join("gccmaster.tblemployees upd", "upd.id = ot.updated_by", "LEFT");
        $this->db->join("gccmaster.tblemployees appr", "appr.id = ot.approved_by", "LEFT");
        $this->db->join("gccmaster.tblemployees dis", "dis.id = ot.disapproved_by", "LEFT");
        $this->db->join("gccmaster.tblemployees canc", "canc.id = ot.cancelled_by", "LEFT");
        $this->db->where("ot.id", $id);
        $this->db->limit(1);
        $queryDetails = $this->db->get();
        if($queryDetails->num_rows() == 1){
            $rawData = $queryDetails->row();
            $tempMaxDate = $rawData->max_date ? strtotime("+1 day", strtotime(trim($rawData->max_date))): null;
            $tempDateFrom = $rawData->date_from ? strtotime(trim($rawData->date_from)): null;
            $validOTDates = $tempMaxDate == null || ($tempMaxDate && $tempDateFrom) && $tempDateFrom > $tempMaxDate;
            
            $resultarray = $queryDetails->row_array();
            $resultarray["valid_ot_dates"] = $validOTDates;
            $resultarray["images"] = array();
            $resultarray["has_attachment"] = false;

            $resultarray["print_purpose"] = nl2br($resultarray["purpose"]);
            $resultarray["purpose"] = str_replace("\n",", ",str_replace("-","", $resultarray["purpose"]));

            $resultarray['approved_by'] = ($resultarray['approved_by'] == 'N/A' && $resultarray['approved_at'] != '0000-00-00 00:00:00') ? '[ System Generated Approval ]' : 'N/A';

            $stdResult = (object) $resultarray;
            $tempImage = isset($stdResult->attachment_image) ? unserialize($stdResult->attachment_image) : array();

            if (is_array($tempImage) && count($tempImage) > 0) {
                $resultarray['has_attachment'] = true;
                $images = array();
                foreach ($tempImage as $imagePath) {
                    $imageParts = explode('/', $imagePath);
                    if (count($imageParts) === 2) {
                        $thumbnail = "{$imageParts[0]}/thumbnails/{$imageParts[1]}";
                        $filename = $imageParts[1];
                        $realImagePath = realpath("uploads/files/images/overtime/{$imagePath}");
                        $isFileExist = file_exists($realImagePath);
                        $images[] = array(
                            'filename' => $filename,
                            'image' => base_url("uploads/files/images/overtime/{$imagePath}"),
                            'thumbnail' => base_url("uploads/files/images/overtime/{$thumbnail}"),
                            'file_exists' => $isFileExist
                        );
                    }
                }
                $resultarray['images'] = $images;
            }
        }

        return $resultarray;
    }

    function updateOvertime($id){
        $this->input->post();

        $this->db->select('reference_no, date_from, date_to, purpose, status');
        $this->db->where('DATE(date_from)', date('Y-m-d', strtotime($this->input->post('date_from'))));
        $this->db->where('employee', $this->input->post('employee'));
        $this->db->where('id != ', $id);
        $this->db->where('status !=', 'Cancelled');
        $this->db->from('gcceforms.overtime');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $resultset['state'] = false;
            $resultset['ot_data'] = $query->row();
            $resultset['message'] = "You already have an overtime application for this date. Only one overtime is allowed per day.";

            return $resultset;
        }
        
        $date = date('Y-m-d H:i:s');
        $data = array(
                'employee' => $this->input->post('employee'),
                'company' => $this->input->post('company'),
                'department' => $this->input->post('department'),
                'position' => $this->input->post('position'),
                'requested_by' => $this->input->post('requested_by'),
                'requested_remarks' => $this->input->post('remarks'),
                'purpose' => $this->input->post('purpose'),
                'date_from' => $this->input->post('date_from'),
                'date_to' => $this->input->post('date_to'),
                'updated_by' => $this->user_data['emp_id'],
                'updated_at' => $date
        );

        $this->db->where('overtime.id', $id);
        $result = $this->db->update('gcceforms.overtime', $data);

        if($result){
            $resultset['state'] = true;
            $resultset['message'] = "Overtime request updated!";
        }else{
            $resultset['state'] = false;
            $resultset['message'] = "Error saving data!";
        }

        return $resultset;
    }

    function approveOvertime($id){
        $post = $this->input->post();
        $attachment = (isset($post["attachment_image"]) && $post["attachment_image"])? $post["attachment_image"]: array();
        $attachment = serialize($attachment);

        $this->db->select("id, employee, date_from");
        $row = $this->db->get_where("gcceforms.overtime", array("id" => $id))->row();
        $ot_date = date("Y-m-d", strtotime($row->date_from));
        $emp_id  = $row->employee;
        $this->db->reset_query();

        $date = date('Y-m-d H:i:s');
        $data = array(
				'status' => 'Approved',
                'approved_by' => $this->user_data['emp_id'],
                'approved_at' => $date,
                'attachment_image' => $attachment,
        );

        $this->db->where('overtime.id', $id);
        $result = $this->db->update('gcceforms.overtime', $data);

        if ($result) {
            $created = $this->ts_model->create($ot_date, 0, array($emp_id), NULL, 1);
            $resultset['state'] = true;
            $resultset['message'] = "Overtime request approved!";
            $resultset["created"] = $created;
            $message = "View Overtime - Approve overtime request {$this->getOvertimeReference($id)}.";
            $type = "success";
            $table = "user"; 
        } else {
            $resultset['state'] = false;
            $resultset['message'] = "Error updating form!";
            $message = "View Overtime - Failed approve overtime request {$this->getOvertimeReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);

        return $resultset;
    }

    function undoApproveOvertime($id){
        $this->input->post();
        $data = array(
				'status' => 'Pending',
                'approved_by' => "",
                'approved_at' => ""
        );

        $this->db->select("id, employee, date_from");
        $row = $this->db->get_where("gcceforms.overtime", array("id" => $id))->row();
        $ot_date = date("Y-m-d", strtotime($row->date_from));
        $emp_id  = $row->employee;
        $this->db->reset_query();

        $this->db->where('overtime.id', $id);
        $result = $this->db->update('gcceforms.overtime', $data);

        if($result){
            $created = $this->ts_model->create($ot_date, 0, array($emp_id), NULL, 1);
            $resultset['state'] = true;
            $resultset['message'] = "Undo approval of overtime request";
            $resultset["created"] = $created;
            $message = "View Overtime - Undo approve overtime request {$this->getOvertimeReference($id)}.";
            $type = "success";
            $table = "user";
        }else{
            $resultset['state'] = false;
            $resultset['message'] = "Error updating form!";
            $message = "View Overtime - Failed undo approve overtime request {$this->getOvertimeReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);

        return $resultset;
    }

    function disapproveOvertime($id){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
				'status' => 'Disapproved',
                'disapproved_by' => $this->user_data['emp_id'],
                'disapproved_at' => $date
        );

        $this->db->where('overtime.id', $id);
        $result = $this->db->update('gcceforms.overtime', $data);

        if($result){
            $resultset['state'] = true;
            $resultset['message'] = "Overtime request disapproved!";
            $message = "View Overtime - Disapprove overtime request {$this->getOvertimeReference($id)}.";
            $type = "success";
            $table = "user";
        }else{
            $resultset['state'] = false;
            $resultset['message'] = "Error updating form!";
            $message = "View Overtime - Failed disapprove overtime request {$this->getOvertimeReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);

        return $resultset;
    }

    function undoDisapproveOvertime($id){
        $this->input->post();
        $data = array(
				'status' => 'Pending',
                'disapproved_by' => "",
                'disapproved_at' => ""
        );

        $this->db->where('overtime.id', $id);
        $result = $this->db->update('gcceforms.overtime', $data);

        if($result){
            $resultset['state'] = true;
            $resultset['message'] = "Undo disapproval of overtime request";
            $message = "View Overtime - Undo disapprove overtime request {$this->getOvertimeReference($id)}.";
            $type = "success";
            $table = "user";
        }else{
            $resultset['state'] = false;
            $resultset['message'] = "Error updating form!";
            $message = "View Overtime - Failed undo disapprove overtime request {$this->getOvertimeReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
        return $resultset;
    }

    function select2CompanyData(){
        $this->db->select("companies.id, companies.`code` `text`, companies.*");
        $this->db->where("is_archived", 0);
        $this->db->where("exclude", 0);
        $this->db->order_by("`code`", "ASC");
        $results = $this->db->get("gcchris.tblcompanies companies")->result();
        return $results;
    }

    function selectPayrollGroup(){
        $get = $this->input->get();
        $arrData = array();
        $resultset = array();
        $companyId = (isset($get["company_id"]) && $get["company_id"])? $get["company_id"]: 0;
        if($companyId || $companyId == 0){
            $this->db->select("id, description as text, employee_id");
            $this->db->from($this->tbl_payroll_group);
            $this->db->where("company_id", $companyId);
            $this->db->where("status", 1);
            $this->db->where("is_archived", 0);
            if (isset($get['term']) && $get['term']) {
                $this->db->like("description", $get['term'], "both");
            }
            $this->db->limit(10);
            $this->db->order_by("description", "ASC");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() > 0){
                foreach($qTemp->result() as $kk => $vv){
                    $employees = array();
                    $tempIds = @unserialize($vv->employee_id);
                    unset($vv->employee_id);
                    $this->db->from($this->tbl_employees);
                    $this->db->where_in("id", $tempIds);
                    $this->db->order_by("lastname","ASC");
                    $qTempEmp = $this->db->get();
                    if($qTempEmp->num_rows() > 0){
                        foreach($qTempEmp->result() as $rs){
                            $tempRs = (array) $rs;
                            $tempName = $this->core_layout->getDisplayName($tempRs);
                            $tempName = isset($tempName["display_name_1"]) && $tempName["display_name_1"] ? $tempName["display_name_1"]: "No assigned name";
                            $employees[] = array(
                                "id"=>$rs->id,
                                "text"=>$tempName,
                            );
                        }
                    }
                    $vv->employees = $employees;
                    $arrData[$kk] = $vv;
                }
            }
        }

        $resultset["results"] = $arrData;
        return $resultset;
    }

    function selectPayrollGroupMultiple(){
        $post = $this->input->post();
        $resultset = array();
        $employees = array();
        if(isset($post["group_id"]) && $post["group_id"]){
            $ids = $post["group_id"];
            $tempIdx = array();
            $this->db->select("employee_id");
            $this->db->from($this->tbl_payroll_group);
            $this->db->where("status", 1);
            $this->db->where("is_archived", 0);
            $this->db->where_in("id", $ids);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                foreach ($q->result() as $key => $value) {
                    $idx = @unserialize($value->employee_id);
                    if(is_array($idx) && count($idx) > 0){
                        foreach ($idx as $kk => $vv) {
                            if(!in_array($vv, $tempIdx)){ $tempIdx[] = $vv; }
                        }
                    }
                }
            }

            if(is_array($tempIdx) && count($tempIdx) > 0){
                $this->db->from($this->tbl_employees);
                $this->db->where_in("id", $tempIdx);
                $this->db->order_by("lastname", "ASC");
                $qTempEmp = $this->db->get();
                if($qTempEmp->num_rows() > 0){
                    foreach($qTempEmp->result() as $rs){
                        $tempRs = (array) $rs;
                        $tempName = $this->core_layout->getDisplayName($tempRs);
                        $tempName = isset($tempName["display_name_1"]) && $tempName["display_name_1"] ? $tempName["display_name_1"]: "No assigned name";
                        $employees[] = array(
                            "id"=>$rs->id,
                            "text"=>$tempName,
                        );
                    }
                }
            }
            $resultset["response"] = true;
            $resultset["data"] = $employees;
        }else{
            $resultset["response"] = false;
        }
        
        return $resultset;
    }

    function selectEmployee(){
        $get = $this->input->get();
        $resultarray = array();
        $employee_ids = array();
        $companyIds = (isset($get["company_id"]) && $get["company_id"])? $get["company_id"]: null;
        $search = (isset($get["q"]) && $get["q"])? $get["q"]: null;
        $payroll_group = (isset($get["payroll_group"]) && $get["payroll_group"]) ? $get["payroll_group"] : null;
        if($payroll_group !== null && $payroll_group !== "all"){
            $this->db->select('employee_id');
            $this->db->from('payroll.payroll_group');
            $this->db->where('payroll_group.id', $payroll_group);
            $payroll_group_query = $this->db->get();
            if ($payroll_group_query->num_rows() >   0) {
                $payroll_group_result = $payroll_group_query->row();
                $employee_ids = unserialize($payroll_group_result->employee_id);
            }
            $this->db->reset_query();
        }

        $this->db->select("a.id, a.firstname, a.lastname, a.middlename, a.suffix");
        $this->db->from("gccmaster.tblemployees a");
        $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id", "LEFT");
        $this->db->where("a.employee_status", "Active"); 
        if(isset($companyIds) && $companyIds !== null){
            $this->db->where("b.id", $companyIds);
        }
        if($payroll_group != null && $payroll_group !== "all"){
            $this->db->where_in('a.id', $employee_ids);
        }
        if (isset($search) &&  $search) {
            $this->db->group_start();
            $this->db->like("a.firstname",  $search, "both");
            $this->db->or_like("a.lastname",  $search, "both");
            $this->db->limit(10);
            $this->db->group_end();
        }

        // $this->db->order_by("trim(a.firstname)", "ASC");
        $query = $this->db->get();


        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["firstname"] . " " . $_query["middlename"] . " " . $_query["lastname"];
                if ($_query["suffix"] != "N/A" && $_query["suffix"] != "none") {
                    $data["text"] .= " " . $_query["suffix"];
                }
                $resultarray[] = $data;
            }

        }
        return array("results" => $resultarray);
    }

    function cancelOvertime($id){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
                'status' => 'Cancelled',
                'cancelled_remarks' => $this->input->post('cancelled_remarks'),
                'cancelled_by' => $this->user_data['emp_id'],
                'cancelled_at' => $date
        );

        $this->db->where('overtime.id', $id);
        $result = $this->db->update('gcceforms.overtime', $data);

        if($result){
            $resultset['state'] = true;
            $resultset['message'] = "Overtime request cancelled!";
            $message = "View Overtime - Cancel overtime request {$this->getOvertimeReference($id)}.";
            $type = "success";
            $table = "user";
        }else{
            $resultset['state'] = false;
            $resultset['message'] = "Error updating form!";
            $message = "View Overtime - Failed cancel overtime request {$this->getOvertimeReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
        return $resultset;
    }

    function advancedSearchRequest(){
        $resultset = array();
        $resultset["has_error"] = false;
        $post = $this->input->post();
        if(isset($post) && $post){
            $arrIds = array();
            $this->db->select('overtime.*, employeetbl.firstname, employeetbl.lastname, employeetbl.middlename, employeetbl.company_id');
            $this->db->from($this->eformsOvertimeTable);
            $this->db->join($this->employeeTable .' as employeetbl', 'employeetbl.id = overtime.employee', 'left');

            if(isset($post["status"]) && $post["status"]){
                $this->db->where("overtime.status", $post["status"]);
            } else {
                $this->db->where("overtime.status != 'Cancelled'");
            }

            if(isset($post["employee"]) && $post["employee"]){
               $this->db->where("employeetbl.id", $post["employee"]);
            }

            if(isset($post["company"]) && $post["company"]){
                $this->db->where("employeetbl.company_id", $post["company"]);
            }

            if (isset($post["date_time"]) && $post["date_time"]) {
                list($startDate, $endDate) = explode(" - ", $post["date_time"]);
                $startDate = date("Y-m-d", strtotime($startDate));
                $endDate = date("Y-m-d", strtotime($endDate));
                $this->db->where("overtime.date_from BETWEEN '$startDate' AND '$endDate'");
                $this->db->where("overtime.date_to BETWEEN '$startDate' AND '$endDate'");
            }

            $query = $this->db->get();
            $result = $query->result();

            if (is_array($result) && count($result) > 0) {
                foreach ($result as $key => $value) {
                    if ($value->id !== null && !in_array($value->id, $arrIds)) {
                        $arrIds[] = $value->id;
                    }
                }
            }

            $resultset["response"] = true;
            $resultset["ids"] = $arrIds;
            if(empty($arrIds)){
                $resultset["has_error"] = true;
                $resultset["toastr_msg"] = "Filtered search data not found!";
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function tempUploadFile() {
        $resultset = array();
        $session = $this->core_layout->getCurrentSession();

        if (isset($session["emp_id"]) && $session["emp_id"]) {
            $imagesPath = "./uploads/files/images/overtime/temp_{$session["emp_id"]}";

            $createFilePath = false;

            if (!file_exists($imagesPath)) {
                $mkdir = mkdir($imagesPath, 0777, true);
                if ($mkdir) {
                    $createFilePath = true;
                }
            } else {
                $createFilePath = true;
            }

            if ($createFilePath == false) {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                $resultset["toastr_state"] = "warning";
            } else {
                $config = array();
                $config['upload_path'] = $imagesPath;
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG';
                $config['max_size'] = 10000;
                $config['create_thumbnail'] = true;

                $data = $this->file_upload->uploadFile($config);
                if ($data["response"] == true) {
                    $thumbnailpath = "./uploads/files/images/overtime/temp_{$session["emp_id"]}/thumbnails";

                    if (!file_exists(realpath($thumbnailpath))) {
                        mkdir($thumbnailpath, 0777, true);
                    }
                    $files = $data["files"][0];
                    $filename = $files["file_name"];
                    if ($filename) {
                        $dirpath = $imagesPath;
                        $s = $this->saveThumbnail($dirpath . "/" . $filename, $thumbnailpath . "/" . $filename);

                        $resultset["response"] = true;

                        $resultset["added_image"] = base_url("uploads/files/images/overtime/temp_{$session["emp_id"]}/{$filename}");
                        $resultset["temp_image"] = "{$filename}";

                        $resultset["toastr_msg"] = "Upload image successful.";
                        $resultset["toastr_state"] = "success";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Image upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Image upload failed!";
                    $resultset["toastr_state"] = "error";
                }
            }

        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Employee data not found!";
            $resultset["toastr_state"] = "error";
        }

        return $resultset;
    }

    //here temp upload
    function tempUploadCsvFile() {
        $resultset = array();
        $session = $this->core_layout->getCurrentSession();
        $_isRecorded = array();

        if (isset($session["emp_id"]) && $session["emp_id"]) {
            $filePath = "./uploads/files/csv/overtime/temp_{$session["emp_id"]}";
            $jsonFileName = null;

            $tempInputName = array_keys($_FILES);
            $createFilePath = false;

            if (!file_exists($filePath)) {
                $mkdir = mkdir($filePath, 0777, true);
                if ($mkdir) {
                    $createFilePath = true;
                }
            } else {
                $createFilePath = true;
            }

            if ($createFilePath === false) {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                $resultset["toastr_state"] = "warning";
            } else {
                $config = array();
                $config['upload_path'] = $filePath;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = 1000000;
                $config['create_thumbnail'] = false;

                $invalidCtr = 0;
                $validCtr = 0;
                if(isset($tempInputName) && count($tempInputName) == 1){ $config["input_field"] = $tempInputName[0]; }
                $data = $this->file_upload->uploadFile($config);
                if ($data["response"] === true) {
                    $files = $data["files"][0];
                    $filename = $files["file_name"];
                    if(file_exists($files["full_path"])){
                        $currentFile = $files["full_path"];
                        $tempIndex = 0;
                        $arrData = array();
                        $bioNotFound = array();

                        if (($handle = fopen($currentFile, "r")) !== false) {
                            while (($data = fgetcsv($handle, 100000, ",")) !== false) {
                                if($tempIndex !== 0){
                                    $tempDatax = array();
                                    $filteredData = array_filter($data);
                                    if(is_array($filteredData) && !empty($filteredData) && count($filteredData) == 5){
                                        $biometricNo = trim($filteredData[0]);
                                        $dateFrom = trim($filteredData[1]);
                                        $dateTo = trim($filteredData[2]);
                                        $approvedDate = trim($filteredData[3]);
                                        $purpose = trim(utf8_encode($filteredData[4]));

                                        $isRecorded = false;
                                        $displayName = "No assigned name";
                                        $isValid = true;

                                        $this->db->select("emp.id, UPPER(TRIM(CONCAT(emp.firstname, ' ',
                                            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                                                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                                                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                                            END,' ', emp.lastname,
                                            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                                                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                                                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                                            END))) as employee_name, emp.biometricno, MAX(ps.date_end) as max_date");
                                        $this->db->from("gccmaster.tblemployees emp");
                                        $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = emp.id AND ps.posted = 1", "LEFT");
                                        $this->db->where(array("emp.biometricno"=>$biometricNo, "emp.employee_status"=>"Active"));
                                        $this->db->limit(1);
                                        $qTempEmployee = $this->db->get();
                                        $empRecordCount = $qTempEmployee->num_rows();

                                        $isValidEmployee = false;
                                        if($empRecordCount == 1){
                                            $row = $qTempEmployee->row();
                                            if(intval($row->id) > 0){
                                                $isValidEmployee = true;
                                                $displayName = $row->employee_name ? $row->employee_name : "No assigned name";

                                                if (date('Y-m-d', strtotime($dateFrom)) <= date('Y-m-d', strtotime($dateTo))) { //checks if date start is less than the date end
                                                    $validOTEndDate = date('Y-m-d', strtotime($filteredData[1].' +1 day')); //added 1 day to date start to prevent extensive date to
                                                    if (date('Y-m-d', strtotime($dateTo)) <= date('Y-m-d', strtotime($validOTEndDate))) { //checks if the date end is correct based on the added 1 day to the date start
                                                        $isValid = strtotime(trim($filteredData[1])) > strtotime(trim($row->max_date));
                                                    } else {
                                                        $isValid = false;
                                                    }
                                                } else {
                                                    $isValid = false;
                                                }

                                                $qSearchOt = $this->db->get_where("gcceforms.overtime",
                                                    array(
                                                        "employee"=>$row->id,
                                                        "date_from"=>date("Y-m-d H:i:s", strtotime($dateFrom)),
                                                        "date_to"=>date("Y-m-d H:i:s", strtotime($dateTo)),
                                                        "status"=>"Approved",
                                                    )
                                                );
                                                if($qSearchOt->num_rows() > 0){
                                                    $reference_no = array();
                                                    foreach ($qSearchOt->result() as $value) { $reference_no[] = $value->reference_no; }
                                                    $isRecorded = true;
                                                    $_isRecorded[] = array(
                                                        "emp_id"=>$row->id, 
                                                        "reference_no"=>$reference_no, 
                                                        "display_name"=>$displayName, 
                                                        "date_from"=>date("Y-m-d H:i:s", strtotime($dateFrom)), 
                                                        "date_to"=>date("Y-m-d H:i:s", strtotime($dateTo)), 
                                                    );
                                                }
                                            }
                                        }else{
                                            if($filteredData[0]){ $bioNotFound[] = $biometricNo; }
                                        }

                                        $employeeExist = $empRecordCount == 1;

                                        $tempDatax["emp_id"] = ($empRecordCount == 1)? $qTempEmployee->row()->id: 0;
                                        $tempDatax["display_name"] = $displayName;
                                        $tempDatax["biometricno"] = $biometricNo;
                                        $tempDatax["date_from"] = $dateFrom;
                                        $tempDatax["date_to"] = $dateTo;
                                        $tempDatax["approved_date"] = $approvedDate;
                                        $tempDatax["purpose"] = $purpose;
                                        $tempDatax["is_existing"] = $empRecordCount;
                                        $tempDatax["is_valid"] = $isValid;
                                        if($isRecorded === false && $employeeExist && $isValidEmployee){
                                            $arrData[] = $tempDatax;
                                        }

                                        if($isValid === false && $empRecordCount == 1 && $isRecorded === false && $isValidEmployee === false){ $invalidCtr++; }
                                        elseif($isValid === true && $empRecordCount == 1 && $isRecorded === false && $isValidEmployee){ $validCtr++; }
                                    }
                                }
                                $tempIndex++;
                            }
                            fclose($handle);
                        }

                        if(is_array($arrData) && !empty($arrData)){
                            $tempJson = json_encode(array("data" => $arrData));
                            $dateToday = Date("Ymd");
                            $jsonFileName = "temp_{$session["emp_id"]}_{$dateToday}.json";
                            $tempJsonFile = "{$filePath}/{$jsonFileName}";
                            $myJsonFile = fopen($tempJsonFile, "w");
                            fwrite($myJsonFile, $tempJson);
                            fclose($myJsonFile);
                        }
                    }
                    if ($filename) {
                        if($jsonFileName){
                            $resultset["response"] = true;
                            $resultset["added_file"] = base_url("uploads/files/csv/overtime/temp_{$session["emp_id"]}/{$filename}");
                            $resultset["added_json_file"] = base_url("uploads/files/csv/overtime/temp_{$session["emp_id"]}/{$jsonFileName}");
                            $resultset["json_file"] = "{$jsonFileName}";
                            $resultset["temp_file"] = "{$filename}";
    
                            $resultset["toastr_msg"] = "Upload file successful.";
                            $resultset["toastr_state"] = "success";
                            $resultset["biometric_not_found"] = is_array($bioNotFound) && !empty($bioNotFound) ? implode(", ", $bioNotFound): null;
                            $resultset["invalid_ctr"] = $invalidCtr;
                            $resultset["valid_ctr"] = $validCtr;
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "No data found!";
                            $resultset["toastr_state"] = "error";

                            if(!empty($_isRecorded)){
                                $resultset["toastr_msg"] = "Employee(s) overtime is already recorded on the module!";
                                $resultset["employee_record"] = $_isRecorded;
                                $resultset["toastr_state"] = "warning";
                            }
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "File upload failed!";
                    $resultset["toastr_state"] = "error";
                    $resultset["data"] = $data;
                }
            }
        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Employee data not found!";
            $resultset["toastr_state"] = "error";
        }

        return $resultset;
    }

    function getCurrentUploadedFile(){
        $resultset = array();
        $session = $this->core_layout->getCurrentSession();
        if(isset($session["emp_id"]) && $session["emp_id"]){
            $arrData = array();
            $imagesPath = "./uploads/files/images/overtime/temp_{$session["emp_id"]}";
            $thumbnailpath = "./uploads/files/images/overtime/temp_{$session["emp_id"]}/thumbnails";
            if(file_exists($imagesPath)){
                $tempFiles = scandir($imagesPath);
                if(is_array($tempFiles) && count($tempFiles) > 0){
                    foreach ($tempFiles as $key => $value) {
                        if($value !== "thumbnails" && $value !== "." && $value !== ".."){
                            $realpath = realpath("uploads/files/images/overtime/temp_{$session['emp_id']}/{$value}");
                            $created_date = date('Y-m-d', filemtime($realpath));
                            $added_date = date('Y-m-d', strtotime($created_date . ' + 5 days'));
                            $now = date('Y-m-d');
                            
                            $tempRow = array();
                            $tempRow["filename"] = $value;
                            $tempRow["current_image"] = "temp_{$session['emp_id']}/{$value}";
                            $tempRow["image"] = base_url("uploads/files/images/overtime/temp_{$session['emp_id']}/{$value}");
                            $tempRow["thumbnail"] = base_url("uploads/files/images/overtime/temp_{$session['emp_id']}/thumbnails/{$value}");
                            $tempRow['created_date'] = date('Y-m-d h:i:s A', filemtime($realpath));
                            
                            if($added_date >= $now){
                                $arrData[] = $tempRow;
                            }
                        }
                    }
                }
            }

            if($arrData && count($arrData) > 0){
                $resultset["response"] = true;
                $resultset["rows"] = $arrData;
                $resultset["count"] = count($arrData);
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }

        return $resultset;
    }

    function saveThumbnail($source, $target) {
        $config = array(
            'image_library' => 'gd2',
            'source_image' => $source,
            'new_image' => $target,
            'maintain_ratio' => TRUE,
            'create_thumb' => TRUE,
            'thumb_marker' => '',
            'width' => 75,
            'height' => 75
        );
        $this->image_lib->initialize($config);
        $this->image_lib->resize();
        $this->image_lib->clear();
    }

    public function importApprovedOvertime(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $date = date('Y-m-d H:i:s');
            $year = substr($date, 2, 2);
            $month = substr($date, 5, 2);
            $session = $this->core_layout->getCurrentSession();
            if (isset($session["emp_id"], $post["json_file"]) && $session["emp_id"] && $post["json_file"]) {
                $filePath = "./uploads/files/csv/overtime/temp_{$session["emp_id"]}";
                $tempFile = "{$filePath}/{$post["json_file"]}";
                
                if(file_exists($tempFile)){
                    $fileContent = file_get_contents($tempFile);
                    $arrData = json_decode($fileContent, true);
                    if(isset($arrData["data"]) && $arrData["data"] && is_array($arrData["data"]) && count($arrData["data"]) > 0){
                        $ctrUploaded = 0;
                        foreach ($arrData["data"] as $value) {
                            $rs = (object) $value;
                            if(intval($rs->is_existing) == 1 && $rs->is_valid === true){
                                $sqlSelect = "a.id as employee, UPPER(IFNULL(b.description, a.company_id)) as company,
                                UPPER(IFNULL(c.description, a.department_id)) as department,
                                UPPER(IFNULL(d.name, a.position)) as position, MAX(ps.date_end) as max_date";
                                $this->db->select($sqlSelect);
                                $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id OR b.description = a.company_id OR b.code = a.company_id", "LEFT");
                                $this->db->join("gcchris.tbldepartments c", "c.id = a.department_id OR c.description = a.department_id OR c.code = a.department_id", "LEFT");
                                $this->db->join("gcchris.tblposition d", "d.id = a.position OR d.name = a.position", "LEFT");
                                $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = a.id AND ps.posted = 1", "LEFT");
                                $this->db->group_by("a.id");
                                $qTemp = $this->db->get_where("gccmaster.tblemployees a", array("a.id"=>$rs->emp_id, "a.employee_status"=>"Active"));
                                if($qTemp->num_rows() == 1){
                                    $list = $this->overtime->get_series($year, $month, "new");
                                    $series = '';
                                    if (sizeof($list) > 0) {
                                        foreach ($list as $arr) { $x = $arr->ref_series; }
                                        $series = intval($x) + 1;
                                        if (strlen($series) == 1) { $series = '000' . $series; }
                                        elseif (strlen($series) == 2) { $series = '00' . $series; }
                                        elseif (strlen($series) == 3) { $series = '0' . $series; }
                                    } else { $series = '0001'; }
                                    $referenceNo = "OT{$year}-{$month}-{$series}";

                                    $currentRow = $qTemp->row();
                                    $currentRow->ref_yr = $year;
                                    $currentRow->ref_series = $series;
                                    $currentRow->ref_month = $month;
                                    $currentRow->reference_no = $referenceNo;
                                    $currentRow->purpose = $rs->purpose;
                                    $currentRow->date_from = date("Y-m-d H:i:s", strtotime($rs->date_from));
                                    $currentRow->date_to = date("Y-m-d H:i:s", strtotime($rs->date_to));
                                    $currentRow->created_by = $session["emp_id"];
                                    $currentRow->created_at = date("Y-m-d H:i:s");
                                    $currentRow->requested_by = isset($post["approved_by"]) ? $post["approved_by"] : 0;
                                    $currentRow->requested_at = date("Y-m-d H:i:s", strtotime($rs->approved_date));
                                    $currentRow->approved_by = isset($post["approved_by"]) ? $post["approved_by"] : 0;
                                    $currentRow->approved_at = date("Y-m-d H:i:s", strtotime($rs->approved_date));
                                    $currentRow->status = "Approved";
                                    $currentRow->attachment_image = isset($post["attachment_image"]) ? serialize($post["attachment_image"]) : "";
                                    $currentRow->is_imported = 1;

                                    $isValidDate = strtotime(trim($rs->date_from)) > strtotime(trim($currentRow->max_date));
                                    unset($currentRow->max_date);

                                    $tempWhere = array();
                                    $tempWhere["employee"] = $rs->emp_id;
                                    $tempWhere["date_from"] = date("Y-m-d H:i:s", strtotime($rs->date_from));
                                    $tempWhere["date_to"] = date("Y-m-d H:i:s", strtotime($rs->date_to));
                                    $tempWhere["status"] = "Approved";
                                    $checkExisting = $this->db->get_where("gcceforms.overtime", $tempWhere);
                                    if($isValidDate && $checkExisting->num_rows() == 0){
                                        $added = $this->db->insert("gcceforms.overtime", $currentRow);
                                        if($added){ $ctrUploaded++; }
                                    }
                                }
                            }
                        }

                        if($ctrUploaded > 0){
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "Overtime record(s) has been imported and was added.";

                            $uploadedAttachment = implode(', ', $post['attachment_image']);
                            $this->core_layout->setEventLog("New Overtime - Imported {$uploadedAttachment} to {$filePath}.", "import", "success", "gcceforms", "user");
                            
                        }else{
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "Employee record(s) not found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Empty record(s), no data to import!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "File not found, no data to import!";
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }

    function exportData($export){
        if($export == 1){
            $this->core_layout->setEventLog("Overtime Masterfile - Export excel file of Overtime Masterfile.","export", "success", "gcceforms", "user");
        }elseif($export == 2){
            $this->core_layout->setEventLog("Overtime Masterfile - Export csv file of Overtime Masterfile.","export", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Overtime Masterfile - Export pdf file of Overtime Masterfile.","export", "success", "gcceforms", "user");
        }
    }

    function exportDataArchive($export){
        if($export == 1){
            $this->core_layout->setEventLog("Archived Overtime - Export excel file of Archived Overtime.","export", "success", "gcceforms", "user");
        }elseif($export == 2){
            $this->core_layout->setEventLog("Archived Overtime - Export csv file of Archived Overtime.","export", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Archived Overtime - Export pdf file of Archived Overtime.","export", "success", "gcceforms", "user");
        }
    }

    function massUpdate(){
        $result = array();
        $post = $this->input->post();

        $selectedOvertime = explode(',', $post['selected']);

        $data = array(
            'date_from' => $post['date_from'],
            'date_to' => $post['date_to'],
            'updated_by' => $this->user_data['emp_id'],
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->where_in('id', $selectedOvertime);
        $update = $this->db->update($this->eformsOvertimeTable, $data);

        if($update){
            $result['state'] = true;
            $result['msg'] = 'Updated Selected Overtime';
            $this->core_layout->setEventLog("Mass Update Overtime - Mass Updated Selected Overtime with ids of {$post['selected']}", "update", "success", "gcceforms", "user");
        }else{
            $result['state'] = false;
            $result['msg'] = 'Failed to update the selected overtime.';
            $this->core_layout->setEventLog("Mass Update Overtime - Failed to update Selected Overtime with ids of {$post['selected']}", "update", "error", "gcceforms", "system");
        }

        return $result;
    }

    function massApprove(){
        $result = array();
        $post = $this->input->post();

        $selectedOvertime = explode(',', $post['selected']);

        $data = array(
            'status' => 'Approved',
            'approved_by' => $post['approved_by'],
            'approved_at' => date('Y-m-d H:i:s')
        );

        $this->db->where_in('id', $selectedOvertime);
        $update = $this->db->update($this->eformsOvertimeTable, $data);

        if($update){
            $result['state'] = true;
            $result['msg'] = 'Approved Selected Overtime';
            $this->core_layout->setEventLog("Mass Approve Overtime - Mass Approve Selected Overtime with ids of {$post['selected']}", "update", "success", "gcceforms", "user");
        }else{
            $result['state'] = false;
            $result['msg'] = 'Failed to update the selected overtime.';
            $this->core_layout->setEventLog("Mass Approve Overtime - Failed to approve Selected Overtime with ids of {$post['selected']}", "update", "error", "gcceforms", "system");
        }

        return $result;
    }
    
    function massDispprove(){
        $result = array();
        $post = $this->input->post();

        $selectedOvertime = explode(',', $post['selected']);

        $data = array(
            'status' => 'Disapproved',
            'disapproved_by' => $post['disapproved_by'],
            'disapproved_at' => date('Y-m-d H:i:s')
        );

        $this->db->where_in('id', $selectedOvertime);
        $update = $this->db->update($this->eformsOvertimeTable, $data);

        if($update){
            $result['state'] = true;
            $result['msg'] = 'Dispproved Selected Overtime';
            $this->core_layout->setEventLog("Mass Disapprove Overtime - Mass Approve Selected Overtime with ids of {$post['selected']}", "update", "success", "gcceforms", "user");
        }else{
            $result['state'] = false;
            $result['msg'] = 'Failed to update the selected overtime.';
            $this->core_layout->setEventLog("Mass Disapprove Overtime - Failed to disapprove Selected Overtime with ids of {$post['selected']}", "update", "error", "gcceforms", "system");
        }

        return $result;
    }
}