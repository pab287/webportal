<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cash_advance_m extends CI_Model {
    protected $eformsTable = "gcceforms";
    private $current_action =  array();
    private $user_data = array();
    private $cashAdvanceTable = "gcceforms.cash_advance";
    private $employeeTable = "gccmaster.tblemployees";
    private $chargesTable = "gcceforms.ca_charges";

    protected $dateTime;
    public function __construct()
	{
        parent::__construct();
        $this->core_layout->setPrivilegeName("ca_masterfile");
        $this->user_data = $this->session->userdata("logged_in");
        $this->current_action = $this->core_layout->getCurrentActions();
        $this->load->model("core/upload_model", "file_upload");
        $this->load->model("sms/contacts_model","contacts");
        date_default_timezone_set('Asia/Singapore');
        $this->dateTime = date("Y-m-d H:i:s");
    }
    

    function getDatatableRequest(){
     
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"1", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        $status = (isset($post['status']) && $post['status']) ? ucwords(str_replace('_', ' ', $post['status'])) : null;

        $rowCount = 0;
        $rowData = array();
        $view_own_request = (in_array("view_own_request", $this->current_action)) ? true : false;
        $view_by_dept = (in_array("view_by_dept", $this->current_action)) ? true : false;
        $view_by_company = (in_array("view_by_company", $this->current_action)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        if(!$search){
            $rowData = $this->get_all_post($view_own_request, $query_builder, $limit, $offset, $sortBy, $sortOrder, $status, $view_by_dept, $view_by_company, $companyDescription); //added status for dashboard notification
            $rowCount = $this->get_all_post_count($view_own_request, $query_builder, $status, $view_by_dept, $view_by_company, $companyDescription);
        }

        if($search){
            $rowData = $this->get_searched_item($view_own_request, $query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $status, $view_by_dept, $view_by_company, $companyDescription);
            $rowCount = $this->get_searched_item_count($view_own_request, $query_builder, $search, $status, $view_by_dept, $view_by_company, $companyDescription);
            
            if(!empty($search)){
                $this->core_layout->setEventLog("Cash Advance Masterfile - Searched `".$search."`.","search", "success", "gcceforms", "user");
            }
            if(!empty($query_builder)){
                $this->core_layout->setEventLog("Cash Advance Masterfile - Generate masterfile through query builder `".$query_builder."`.","generate", "success", "gcceforms", "user");
            }
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function cCharges($id){
        $result = array();
        // $this->db->select("description, amount");
        $this->db->select("description, FORMAT(amount, 2) as amount");
        $this->db->from("gcceforms.ca_charges");
        $this->db->where("ca_id", $id);
        $this->db->where("amount !=", '0.00');
        $query = $this->db->get();
        return $query->result_array();
    }

    private function get_all_post($view, $query_builder=null, $limit=10, $offset=0, $sortBy, $sortOrder, $status = null, $view_dept, $view_by_company = false, $companyDescription = null){
        $date= date("Y-m-d", strtotime("-1 year", time()));
        $sql = "a.id, a.employee, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt, b.position as empPosition";

        $this->db->select($sql);
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
        $this->db->where("a.status !=","Cancelled");  
        $this->db->where("a.created_dt >=", $date);

        if($status){
            $this->db->where("a.status", $status);
        }

        if($view && ($this->user_data['emp_id']!=1)){
            $this->db->where('a.employee', $this->user_data['emp_id']);
        }

        if($view_dept && ($this->user_data['emp_id']!=1)){
            $this->db->where('b.department_id', $this->user_data['department']);
        }

        if ($view_by_company) {
            $this->db->where('b.company_id', (int)$this->user_data['company']);

            if ($companyDescription) {
                $this->db->where('a.company', $companyDescription);
            }
        }

        if($query_builder){
            $this->db->where($query_builder);
        }

        if($status){
            $this->db->where("a.status", $status);
        }
            
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $tempRs = (array) $rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                $rs->amt_applied =  number_format($rs->amt_applied, 2);
                $rs->amt_approved =  ($rs->status == 'Approved') ? number_format($rs->amt_approved, 2) : "--";
                if(is_numeric($rs->empPosition)){
                    $rs->position = $this->getPosition($rs->empPosition);
                }else{
                    $rs->position = $rs->empPosition;
                }
                //$rs->position = $this->getPosition($rs->empPosition);
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

    private function get_all_post_count($view, $query_builder=null, $status = null, $view_dept, $view_by_company = false, $companyDescription = null){
        $date= date("Y-m-d", strtotime("-1 year", time()));
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
        $this->db->where("a.created_dt >=", $date);
        $this->db->where("a.status !=","Cancelled");

        if($status){
            $this->db->where("a.status", $status);
        }

        if($view && ($this->user_data['emp_id']!=1)){
            $this->db->where('a.employee', $this->user_data['emp_id']);
        }  

        if($view_dept && ($this->user_data['emp_id']!=1)){
            $this->db->where('b.department_id', $this->user_data['department']);
        }

        if ($view_by_company) {
            $this->db->where('b.company_id', $this->user_data['company']);

            if ($companyDescription) {
                $this->db->where('a.company', $companyDescription);
            }
        }

        if($query_builder){
            $this->db->where($query_builder);
        }

        if($status){
            $this->db->where("a.status", $status);
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_item($view, $query_builder=null, $search=null, $limit=10, $offset=0, $sortBy, $sortOrder, $status = null, $view_dept, $view_by_company = false, $companyDescription = null){
        $date= date("Y-m-d", strtotime("-1 year", time()));
        if($search){
            $sql = "a.id, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt, b.position as empPosition";
            $filterFields = array("a.id", "a.status", "a.reference_no", "b.firstname", "b.lastname", "a.amt_applied", "a.purpose", "a.amt_approved", "a.created_dt", "a.approved_dt");
            $this->db->select($sql);
            $this->db->from("gcceforms.cash_advance a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            $this->db->where("a.status !=","Cancelled"); 
            $this->db->where("a.created_dt >=", $date);
            
            if($status){
                $this->db->where("a.status", $status);
            }

            if($view && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.employee', $this->user_data['emp_id']);
            }

            if($view_dept && ($this->user_data['emp_id']!=1)){
                $this->db->where('b.department_id', $this->user_data['department']);
            }

            if ($view_by_company) {
                $this->db->where('b.company_id', (int)$this->user_data['company']);

                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            }

            if($query_builder){
                $this->db->where($query_builder);
            }

            if($status){
                $this->db->where("a.status", $status);
            }
                
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); $this->db->or_like("CONCAT(b.firstname,' ',b.lastname )", $search, "both");}
            }
            $this->db->group_end();
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $key => $rs){
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $rs->amt_applied =  number_format($rs->amt_applied, 2);
                    $rs->amt_approved =  ($rs->status == 'Approved') ? number_format($rs->amt_approved, 2) : "--";
                    if(is_numeric($rs->empPosition)){
                        $rs->position = $this->getPosition($rs->empPosition);
                    }else{
                        $rs->position = $rs->empPosition;
                    }
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
        }else{
            return array();
        }
    }

    private function get_searched_item_count($view, $query_builder=null, $search=null, $status = null, $view_dept, $view_by_company = false, $companyDescription = null){
        $date= date("Y-m-d", strtotime("-1 year", time()));
        $rowCount = 0;
        if($search){
            $sql = "a.id, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt";
            $filterFields = array("a.id", "a.status", "a.reference_no", "b.firstname", "b.lastname", "a.amt_applied", "a.purpose", "a.amt_approved", "a.created_dt", "a.approved_dt");
            $this->db->select($sql);
            $this->db->from("gcceforms.cash_advance a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            $this->db->where("a.created_dt >=", $date);
            $this->db->where("a.status !=","Cancelled");  
            
            if($status){
                $this->db->where("a.status", $status);
            }

            if($view && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.employee', $this->user_data['emp_id']);
            }

            if($view_dept && ($this->user_data['emp_id']!=1)){
                $this->db->where('b.department_id', $this->user_data['department']);
            }

            if ($view_by_company) {
                $this->db->where('b.company_id', $this->user_data['company']);

                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            }
            
            if($query_builder){
                $this->db->where($query_builder);
            }

            if($status){
                $this->db->where("a.status", $status);
            }

            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); $this->db->or_like("CONCAT(b.firstname,' ',b.lastname )", $search, "both");}
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    function getEmployee(){
        $get = $this->input->get();
        $resultarray = array();
        // if(isset($get['q'])){
        //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE (employee_status='Active') AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%') ORDER BY firstname ASC LIMIT 10");
        // }else{
        //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC LIMIT 10");
        // }
        $view_by_company = (in_array("view_by_company", $this->current_action)) ? true : false;

        $this->db->select('id, firstname, lastname, middlename, suffix');
        $this->db->from('gccmaster.tblemployees');
        $this->db->where('employee_status', 'Active');

        if (isset($get['q'])) {
            $this->db->group_start();
                $this->db->like('firstname', $get['q'], 'both');
                $this->db->or_like('lastname', $get['q'], 'both');
            $this->db->group_end();
        }

        if ($view_by_company) {
            $this->db->where('company_id', $this->user_data['company']);
        }

        $this->db->order_by('firstname', 'ASC');
        $this->db->limit(10);
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();

                // checking of employee if blacklisted in cash advance
                $check_blacklisted = $this->db->get_where('gcceforms.ca_blacklisted', array('emp_id' => $_query['id'], 'is_removed' => 0))->row();

                $tempRs = (array) $_query;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
    
                if($check_blacklisted == null && $check_blacklisted == ''){
                    $data["id"] = $_query["id"];
                    $data["text"] = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $resultarray[] = $data;
                }
            }
        }
        return array("results"=>$resultarray);
    }

    function getEmployeeDetail(){
        // $datap = implode($this->input->get());
        $get = $this->input->get();
        $id = $get['data'];
        $resultarray = array();
        $query = $this->db->query("SELECT id, company_id, department_id, position, work_status, work_mode, employee_status, idno, date_start FROM gccmaster.tblemployees WHERE id=$id");
        if($query->num_rows() > 0){
            $row = $query->row_array();

            if(is_numeric($row["company_id"])){
                $company = $this->db->query("SELECT description FROM gcchris.tblcompanies WHERE id=$row[company_id]")->row_array();
                if($company["description"] == ""){
                    $result["company"] = "N/A";
                }else{
                    $result["company"] = $company["description"];
                }
            }else{
                $result["company"] = $row["company_id"];
            }

            if(is_numeric($row["department_id"])){
                $emp_department = $this->db->query("SELECT description FROM gcchris.tbldepartments WHERE id=$row[department_id]")->row_array();
                if($emp_department["description"] == ""){
                    $result["department"] = "N/A";
                }else{
                    $result["department"] = $emp_department["description"];
                }
            }else{
                $result["department"] = $row["department_id"];
            }

            if(is_numeric($row["position"])){
                $emp_position = $this->db->query("SELECT name FROM gcchris.tblposition WHERE id=$row[position]")->row_array();
                if($emp_position["name"] == ""){
                    $result["position"] = "N/A";
                }else{
                    $result["position"] = $emp_position["name"];
                }
            }else{
                $result["position"] = $row["position"];
            }

            $resultarray["company_details"] = $result["company"]."\n".$result["department"]."\n".$result["position"];
            $resultarray['empData'] = $row;
        }
        return $resultarray;
    }

    public function saveCashAdvance(){
        $post = $this->input->post();
        $year = date('y');
        $month = date('m');

        $lastRefSeries = $this->getLastRefSeries($year, $month);
        $refSeries = $lastRefSeries ? (int)$lastRefSeries + 1 : 1;
        $refSeries = str_pad($refSeries, 4, '0', STR_PAD_LEFT);

        $x = explode("\n", $post["company"]);
        $company_x = rtrim($x['0']);
        $department_x = rtrim($x['1']);
        $position_x = rtrim($x['2']);
        $referenceCode =  'CA'.$year.'-'.$month.'-'.$refSeries;
        $emp = $post["employee"];
        $purpose = ($post["purpose"] == 'Others') ? trim($post["other_purpose"]): trim($post["purpose"]);
        $data = array(
                'ref_yr' => $year,
				'ref_series' => $refSeries,
				'ref_month' => $month,
				'reference_no' => $referenceCode,
				'status' => 'HR Recommendation Pending',
                'employee' => $post["employee"],
                'company' => $company_x,
				'department' => $department_x,
                'position' => $position_x,
                'emp_status' => $this->getEmployeeStatus($this->input->post('employee')),
                'emp_idno' => $this->getEmployeeNumber($this->input->post('employee')),
                'date_employed' => $this->getEmployeeDateEmployed($this->input->post('employee')),
                'purpose' => $purpose,
                'amt_applied' => $post["amt_applied"],
                'amt_approved' => "0",
                'amt_to_b_deducted' => $post["amt_deduct"],
                'deduct_type' => $post["deduct_type"],
                'created_by' => $this->getDisplayName(),
                'created_dt' => $this->dateTime
        );
        $q = $this->db->insert('gcceforms.cash_advance', $data);
        $data_id = $this->db->insert_id();
        foreach ($post['file_path'] as $path){
            $fromPath = dirname($path);
            $toPath = "./uploads/files/cash_advance/empcode_{$emp}";
            $fileName = basename($path);
            if (!file_exists(realpath($toPath))) { mkdir($toPath, 0777, true); }
            $moveUploaded = $this->file_upload->moveUploadedFile($fileName,$fromPath,$toPath, true);
            $thumbnailPath = "$fromPath/thumbnails/$fileName";
            if (file_exists($thumbnailPath)) { unlink($thumbnailPath); }
            if($moveUploaded){ $this->saveFile($data_id, $fileName); }
        }

        $msgEmployeeName = "";
        if($emp != $this->core_layout->getCurrentEmployeeId()){
            $employeeName = $this->getCurrentEmployeeName($emp);
            $msgEmployeeName = " for employee `". $employeeName."`";
        }

        if($q){
            $this->core_layout->setEventLog("Cash Advance Masterfile - Added new Cash Advance with a CA. No. of `".$referenceCode."`".$msgEmployeeName."." ,"insert", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Cash Advance Masterfile - Failed to add new Cash Advance with a CA. No. of ".$referenceCode."`".$msgEmployeeName."." ,"insert", "error", "gcceforms", "system");
        }

        if($data_id){
            $empName = $this->getEmpName($emp);
            $recipient = $this->getSupervisorEmail($department_x);
            $this->email_send($empName, $referenceCode, $post["amt_applied"], $post["purpose"], $recipient);
        }
        return true;
    }

    function getSupervisorEmail($department_x){
        $this->db->select('head_id,head');
        $this->db->from('gcchris.tbldepartments');
        $this->db->where('description', $department_x);
        $query_dept = $this->db->get();
		$head_id = $query_dept->row_array()['head_id'];

        $this->db->select('email');
        $this->db->from('gccmaster.tblusers');
        $this->db->where('emp_id',$head_id);
        $get_email = $this->db->get();
        return $get_email->row_array()['email'];

    }

    function getDisplayName(){
        $this->db->select("firstname, middlename, lastname, suffix");
        $this->db->from("gccmaster.tblemployees");
        $this->db->where("id", $this->user_data['emp_id']);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $tempRs = (array) $rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                $arrData[$key] = $rs;
            }
       
            return $arrData[0]->display_name;
        }else{
            return array();
        }

    }

    function getEmpName($id){
        $this->db->select("id, firstname, middlename, lastname, suffix");
        $this->db->from("gccmaster.tblemployees");
        $this->db->where("id", $id);
        $this->db->limit(1);

        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $tempRs = (array) $rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                $arrData[$key] = $rs;
            }
            return $arrData[0]->display_name;
        }else{
            return "";
        }

    }

    function email_send($employeeName, $referenceNumber, $amount=0, $purpose="", $recipient){
        $resultset = array();
        $emailTo = array();

        $employeeName = mb_strtoupper($employeeName);
        if($recipient){ $emailTo = is_array($recipient)? $recipient: array($recipient); }

        /*** emailTo = $this->sendEmailCompanyTo($companyTo);
        f($recipient == NULL){ $emailTo = "jp04@gccph.com";
        }else{ $emailTo = $recipient; } ***/

        $data = array("employee_name"=>$employeeName, "amount"=>$amount, "purpose"=>$purpose);
        $message = "";
        $message .= $this->load->view("eforms/email_templates/email_ca_template", $data, true);
     
        $module = "eforms_ca_new";
        $email_title = "Cash Advance";
        $content_title = "Cash Advance Application - {$referenceNumber}";
        $content = $message;
        
        $overrideMailer = array();
        /*** $overrideMailer["email_user"] = "gccphtest@gmail.com";
        $overrideMailer["email_pass"] = "developer"; ***/
        if($emailTo){ $overrideMailer["send_to"] = $emailTo; }
        
        $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
        if($sent){
            $resultset["status"] = true;
            //$resultset["content"] = $up;
            //$resultset["last_id"] = $insert_id;
            $resultset["ref_no"] = $referenceNumber;
            $resultset["employee"] = $employeeName;
            //$resultset["email_to"] = $emailTo;
            
            $resultset["toastr_msg"] = "Cash Advance saved, email has been sent";
            $resultset["toastr_status"] = "success";
            
            $this->core_layout->logNotification("Cash Advance saved, email has been sent", "success");
        }else{
            $resultset["status"] = false;
            $resultset["toastr_msg"] = "Cash Advance saved, failed to send email!";
            $resultset["toastr_status"] = "error";
            
            $this->core_layout->logNotification("Cash Advance saved, failed to send email!", "error");
        }
        return $resultset;
    }

    protected function email_send_approval($employeeName=null, $referenceNumber=null, $amount=0, $purpose="", $recipient=null){
        $resultset = array();
        $emailTo = array();
        $employeeName = mb_strtoupper($employeeName);
        if($recipient){ $emailTo = is_array($recipient)? $recipient: array($recipient); }

        /*** emailTo = $this->sendEmailCompanyTo($companyTo);
        f($recipient == NULL){ $emailTo = "jp04@gccph.com";
        }else{ $emailTo = $recipient; } ***/

        $data = array("employee_name"=>$employeeName, "amount"=>$amount, "purpose"=>$purpose);
        $message = "";
        $message .= $this->load->view("eforms/email_templates/email_ca_approval_template", $data, true);
     
        $module = "eforms_ca_approval";
        $email_title = "Cash Advance";
        $content_title = "Cash Advance Approval - {$referenceNumber}";
        $content = $message;
        
        $overrideMailer = array();
        /*** $overrideMailer["email_user"] = "gccphtest@gmail.com";
        $overrideMailer["email_pass"] = "developer"; ***/
        if($emailTo){ $overrideMailer["send_to"] = $emailTo; }
        
        $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
        if($sent){
            $resultset["status"] = true;
            //$resultset["content"] = $up;
            //$resultset["last_id"] = $insert_id;
            $resultset["ref_no"] = $referenceNumber;
            $resultset["employee"] = $employeeName;
            //$resultset["email_to"] = $emailTo;
            
            $resultset["toastr_msg"] = "Cash Advance saved, email has been sent";
            $resultset["toastr_status"] = "success";
            
            $this->core_layout->logNotification("Cash Advance saved, email has been sent", "success");
        }else{
            $resultset["status"] = false;
            $resultset["toastr_msg"] = "Cash Advance saved, failed to send email!";
            $resultset["toastr_status"] = "error";
            
            $this->core_layout->logNotification("Cash Advance saved, failed to send email!", "error");
        }
        return $resultset;
    }

    function email_send_approve($employeeName, $referenceNumber, $amount=0, $purpose="", $recipient = null){
        $resultset = array();
        $emailTo = array();
        $employeeName = mb_strtoupper($employeeName);
        //if($recipient){ $emailTo = is_array($recipient)? $recipient: array($recipient); }
        if($recipient){
            $emailTo = $recipient;
        }else{
            $emailTo = '';
        }

        $data = array("employee_name"=>$employeeName, "amount"=>$amount, "purpose"=>$purpose);
        $message = "";
        $message .= $this->load->view("eforms/email_templates/email_ca_approve_template", $data);
     
        $module = "eforms_ca_approved";
        $email_title = "Cash Advance";
        $content_title = "Cash Advance Approved - {$referenceNumber}";
        $content = $message;
        
        $overrideMailer = array();
        if($emailTo){ $overrideMailer["send_to"] = $emailTo; }
        
        $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
        if($sent){
            $resultset["status"] = true;
            $resultset["ref_no"] = $referenceNumber;
            $resultset["employee"] = $employeeName;
            
            $resultset["toastr_msg"] = "Cash Advance saved, email has been sent";
            $resultset["toastr_status"] = "success";
            
            $this->core_layout->logNotification("Cash Advance saved, email has been sent", "success");
        }else{
            $resultset["status"] = false;
            $resultset["toastr_msg"] = "Cash Advance saved, failed to send email!";
            $resultset["toastr_status"] = "error";
            
            $this->core_layout->logNotification("Cash Advance saved, failed to send email!", "error");
        }
        return $resultset;
    }

    function saveFile($id, $name){
        $data = array(
            'ca_id' => $id,
            'filename' => $name,
        );
        return $this->db->insert('gcceforms.ca_attachments', $data);
    }

    protected function getLastRefSeries($year, $month){
		$this->db->select('ref_series');
		$this->db->from('gcceforms.cash_advance');
		$this->db->where('ref_yr',$year);
		$this->db->where('ref_month',$month);
		$this->db->order_by('ref_series','asc');
		$query = $this->db->get();
		return $query->result();
    }
    
    function getCompany($company){
        $this->db->select('id,description');
        $this->db->from('gcchris.tblcompanies');
        $this->db->where('id', $company);
        $query = $this->db->get();
		return $query->row_array()['description'];
    }

    function getDepartment($department){
        $this->db->select('id,description');
        $this->db->from('gcchris.tbldepartments');
        $this->db->where('id', $department);
        $query = $this->db->get();
		return $query->row_array()['description'];
    }

    function getDepartmentID($department){
        $this->db->select('id,description');
        $this->db->from('gcchris.tbldepartments');
        $this->db->where('description', $department);
        $query = $this->db->get();
		return $query->row_array()['id'];
    }

    function getPosition($position){
        $this->db->select('id,name');
        $this->db->from('gcchris.tblposition');
        $this->db->where('id', $position);
        $query = $this->db->get();
		return $query->row_array()['name'];
    }

    function getEmployeeStatus($employee){
        $this->db->select('work_status');
        $this->db->from('gccmaster.tblemployees');
        $this->db->where('id',$employee);
        $query = $this->db->get();
		return $query->row_array()["work_status"];
    }

    function getEmployeeNumber($employee){
        $this->db->select('idno');
        $this->db->from('gccmaster.tblemployees');
        $this->db->where('id',$employee);
        $query = $this->db->get();
		return $query->row_array()["idno"];
    }

    function getEmployeeDateEmployed($employee){
        $this->db->select('date_start');
        $this->db->from('gccmaster.tblemployees');
        $this->db->where('id',$employee);
        $query = $this->db->get();
		return $query->row_array()["date_start"];
    }

    function getCashAdvanceDetail($id){
        $this->db->select("a.*, b.firstname, b.middlename, b.lastname, b.suffix, b.bday, c.code AS c_code, d.code AS d_code, e.name AS p_code, GROUP_CONCAT(f.filename) AS filenames");
        $this->db->from('gcceforms.cash_advance a');
        $this->db->join('gccmaster.tblemployees b','a.employee=b.id','left');
        $this->db->join('gcchris.tblcompanies c','b.company_id=c.code','left');
        $this->db->join('gcchris.tbldepartments d','b.department_id=d.code','left');
        $this->db->join('gcchris.tblposition e','b.position=e.name','left');
        $this->db->join('gcceforms.ca_attachments f','a.id=f.ca_id','left');
        $this->db->where('a.id',$id);
        $this->db->group_by('a.id');
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $attachments=array();

                $rs->company = (is_numeric($rs->company)) ? $this->getCompany($rs->company) : $rs->company;
                $rs->department = (is_numeric($rs->department)) ? $this->getDepartment($rs->department) : $rs->department;
                $rs->position = (is_numeric($rs->position)) ? ucwords($this->getPosition($rs->position)) : ucwords($rs->position);

                // if(is_numeric($rs->company)){
                //     $rs->company =  $this->getCompany($rs->company);
                // }else{
                //     $rs->company = $rs->company;
                // }
                // if(is_numeric($rs->department)){
                //     $rs->department = $this->getDepartment($rs->department);
                // }else{
                //     $rs->department = $rs->department;
                // }
                // if(is_numeric($rs->position)){
                //     $rs->position = ucwords($this->getPosition($rs->position));
                // }else{
                //     $rs->position = ucwords($rs->position);
                // }
                $tempRs = (array) $rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                if($rs->status=="HR Recommendation Pending"){
                    $rs->status = "Sup Recommendation";
                }
                $rs->last_vale = "₱ ".number_format($this->lastVale($rs->employee,$id, $rs->created_dt), 2);
                $rs->amt_applied =  "₱ ".number_format($rs->amt_applied, 2);
                
                $rs->amt_approved =  "₱ ".number_format($rs->amt_approved, 2);
                /*** @malvin ngaa naka number format ang remarks.. sabta ko b! ***/
                if($rs->recommend_remarks && is_numeric($rs->recommend_remarks)){
                    $rs->recommend_remarks =  "₱ ".number_format($rs->recommend_remarks, 2);
                }
                if($rs->hr_bal_remarks && is_numeric($rs->hr_bal_remarks)){
                    $rs->hr_bal_remarks =  "₱ ".number_format($rs->hr_bal_remarks, 2);
                }
                if($rs->acctg_bal_remarks && is_numeric($rs->acctg_bal_remarks)){
                    $rs->acctg_bal_remarks =  "₱ ".number_format($rs->acctg_bal_remarks, 2);
                }
                if($rs->deduct_type != "percentage" && is_numeric($rs->amt_to_b_deducted)){
                    $rs->amt_to_b_deducted =  "₱ ".number_format($rs->amt_to_b_deducted, 2);
                }
                /*** @malvin ngaa naka number format ang remarks.. sabta ko b! ***/

                $rs->acctg_ca_pending_formatted =  "₱ ".number_format($rs->acctg_ca_pending, 2, ".", ",");
                $rs->acctg_ca_interest_formatted =  "₱ ".number_format($rs->acctg_ca_interest, 2, ".", ",");
                $rs->acctg_sss_loan_formatted =  "₱ ".number_format($rs->acctg_sss_loan, 2, ".", ",");
                $rs->acctg_hdmf_loan_formatted =  "₱ ".number_format($rs->acctg_hdmf_loan, 2, ".", ",");
                $rs->acctg_outside_loan_formatted =  "₱ ".number_format($rs->acctg_outside_loan, 2, ".", ",");
            
                $rs->ocharge= $this->cCharges($id);

                if (!empty($rs->filenames)) {
                    $filenamesArray = explode(',', $rs->filenames); 
                    foreach ($filenamesArray as $filename) {
                        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        $path = "uploads/files/cash_advance/empcode_{$rs->employee}";
                        $url = base_url($path. '/'. $filename);
                        $thumbnail = base_url($path. "/thumbnails/" . $filename);
                        if ($extension === 'pdf') {
                            $thumbnail = base_url("assets/images/file_icons/pdf.svg");
                        }
                        $attachmentInfo = array(
                            'extension' => $extension,
                            'filename' => $filename,
                            'path' => $path. "/". $filename,
                            'image_url' => $url,
                            'thumbnail' => $thumbnail,
                        );
                        $attachments[] = $attachmentInfo;
                    }
                }
                $rs->attachments = $attachments;
                $arrData[$key] = $rs;
            }
            $data = array();
            foreach($arrData as $k=>$v){
                $v->company_details = $v->company."\n".$v->department."\n".$v->position;
                if($v->last_edited_by==null || $v->last_edited_by==""){
                    $v->last_edited_by = "N/A";
                }
                $data[] = $v;
            }
            return $data[0];
        }else{
            return array();
        }
    }

    public function lastVale($emp,$id, $date){
        $date = date("Y-m-d H:i:s", strtotime($date));
   
		$list = $this->viewLastVale($emp,$id,$date);	
		$data = array();
		
		foreach($list as $arr) {
			$row = array();
            $row[] = $arr->amt_approved;
            $row[] = $arr->id;
			$data[] = $row;
        }
        
        if(count($data)>0){
            return $data[0][0];
        }else{
            return 0;
        }


	}

    function viewLastVale($emp,$id,$date){
        $this->db->from('gcceforms.cash_advance a');
		$this->db->where('a.employee',$emp);
		$this->db->where('a.id != ',$id);
		$this->db->where('a.created_dt <',$date);
		$this->db->where('a.status','Approved');
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		return $query->result();
    }

    function getCashAdvanceContent($id){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowData = array();
        $getEmployee = $this->getEmpID($id);
        $rowData = $this->get_cashadvance_content($getEmployee->employee, $sortBy, $sortOrder);
     
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function getEmpID($id){
        $this->db->select("employee");
        $this->db->from("gcceforms.cash_advance");
        $this->db->where("id", $id);
        $query = $this->db->get();
        return $query->row();
    }

    private function get_cashadvance_content($id, $sortBy, $sortOrder){
        $sql = "id, reference_no, purpose, amt_approved, approved_dt";

        $this->db->select($sql);
        $this->db->from("gcceforms.cash_advance");
        $this->db->where('employee', $id);
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $ca_status = $this->get_ca_status_details($rs->reference_no);
                $get_active = $this->getca_ActiveBalance($rs->reference_no);
                if($ca_status->reference != ''){
                    $rs->active = $ca_status->active;
                    $rs->paid = $ca_status->paid;
                    $rs->is_paid = intval($ca_status->loan_amount) == 1;
                    $total = $get_active->loan_amount - $get_active->amount_due;
                    $rs->activebal = floatval($total) > 0;
                    $rs->rembalance = floatval($total) > 0 ? number_format($total, 2) : number_format(0, 2);
                }else{
                    $rs->reference = 'no reference no';
                    $rs->activebal = false;
                    $rs->rembalance = 0;
                    
                }
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

    function getca_ActiveBalance($data){
        $this->db->select('SUM(b.amount_due) as amount_due, GROUP_CONCAT(b.amount_due) as temp_amt, a.amount as loan_amount');
        $this->db->from('gcchris.loans a');
        $this->db->join('payroll.payroll_sheet_loan_payments b', 'b.loan_id = a.id', 'left');
        $this->db->where('reference', $data);
        $query = $this->db->get();
        return $query->row();

    }

    function get_ca_status_details($data){
        $this->db->select('a.active, a.paid, IF(SUM(b.amount_due) >= a.amount, 1, 0) as loan_amount, a.reference');
        $this->db->from('gcchris.loans a');
        $this->db->join('payroll.payroll_sheet_loan_payments b', 'b.loan_id = a.id', 'left');
        $this->db->where('reference', $data);
        $query = $this->db->get();
        return $query->row();
    }

    function updateCashAdvance($id){
       $post = $this->input->post();
        $x = explode("\n", $this->input->post('company'));
        $company = rtrim($x['0']);
        $department = rtrim($x['1']);
        $position = rtrim($x['2']);
        $rs = $this->user_data;
        $tempRs = (array) $rs;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object) $fullname;
        $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
        $amt_deduct =  str_replace( ',', '', explode('₱', $this->input->post('amt_deduct')));
        $deduct_type = $this->input->post('deduct_type');
        if($deduct_type=="percentage"){
            $amt_deduct_val = $amt_deduct[0];
            $amt_deduct_val = str_replace('%', '', $amt_deduct_val);
            $amt_deduct_val = doubleval($amt_deduct_val);
        }else{
            $amt_deduct_val = $amt_deduct[1];
        }
        $date = date('Y-m-d H:i:s');
        $data = array(
            'employee' => $this->input->post('employee'),
            'company' => $company,
            'department' => $department,
            'position' => $position,
            'purpose' => $this->input->post('purpose'),
            'amt_applied' => $this->input->post('amt_applied'),
            'amt_to_b_deducted' => $amt_deduct_val,
            'deduct_type' => $this->input->post('deduct_type'),
            'last_edited_by' => $this->getDisplayName(),
            'last_edited_dt' => $date
        );
        if($id){
            $idsToRemove = [];
            $currentCashAdvanceData = $this->getCashAdvanceById($id);
            $currentCashAdvanceData->attachments = $this->getAttachmentsById($id);
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);
            $emp = $this->input->post('employee');
            if($query){
                $attachments=array();
                $toPath = "uploads/files/cash_advance/empcode_{$emp}";
                foreach ($post['file_path'] as $path){
                    $fromPath = dirname($path);
                    $fileName = basename($path);
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    if (!file_exists(realpath($toPath))) { mkdir($toPath, 0777, true); }
                    if(!file_exists(realpath($toPath. '/'. $fileName))){
                        $moveUploaded = $this->file_upload->moveUploadedFile($fileName,$fromPath,$toPath, true);
                        if (!$moveUploaded) {
                            $notMoved[] = $fileName;
                        }
                        if ($fileExtension!== 'pdf'){
                            unlink(realpath("$fromPath/thumbnails/$fileName"));
                        }
                    }
                    $attachments[] = $fileName;
                }

                $data['attachments'] = $attachments;
                foreach ($currentCashAdvanceData->attachments as $item) {
                    $fileExtension = strtolower(pathinfo($item->filename, PATHINFO_EXTENSION));
                    if (!in_array($item->filename, $data['attachments'])) {
                        $idsToRemove[] = $item->id;
                        unlink("$toPath/$item->filename");
                        if ($fileExtension!== 'pdf') {
                        unlink("$toPath/thumbnails/$item->filename");
                        }
                    }
                }
                if (!empty($idsToRemove)) {
                    $this->db->where_in('id', $idsToRemove);
                    $this->db->delete('gcceforms.ca_attachments');
                }
                foreach ($data['attachments'] as $attachment) {
                    if (!array_filter($currentCashAdvanceData->attachments, function($item) use ($attachment) {
                        return $item->filename === $attachment;
                    })) {
                        $this->saveFile($id, $attachment);
                    }
                }
                $currentCashAdvanceData->attachments = array_map(function($item) {
                    return $item->filename;
                }, $currentCashAdvanceData->attachments);
                $changes = $this->logChanges($currentCashAdvanceData, $data);
                $this->core_layout->setEventLog("Cash Advance Masterfile - Cash Advance ID: ".$id." Updated ".$changes,"update", "success", "gcceforms", "user");
                return $query;
            }
        }
    }
    

    function recommendDetails($id){
        $this->db->select("a.*, CONCAT(b.firstname,' ',b.lastname) AS employee_name");
        $this->db->from('gcceforms.cash_advance a');
        $this->db->join('gccmaster.tblemployees b','a.employee=b.id','left');
        $this->db->where('a.id',$id);
        $query = $this->db->get();
        return $query->row();
    }

    public function recommendUpdate($id){
        if($id){
            $post = $this->input->post();
            $amtApplied = (float) str_replace(['₱', ','], '', $post['amt_applied']);
            $employeeName = $this->getCurrentEmployeeName($post['employee']);
            $trimmedRemarks = trim($post['recommend_remark2']);
            $data = array(
                'recommend_by' => $this->getCurrentEmployeeName(),
                'recommend_dt' => $this->dateTime,
                'employee' => $post['employee'],
                'amt_applied' => $amtApplied,
                'recommend_remark2' => $trimmedRemarks,
                'status' => "Accounting Balance Pending",
            );
            
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);
            if($query){
                $messageRemarks = $trimmedRemarks ? " with a remarks of `".$trimmedRemarks."`" : "";
                $this->core_layout->setEventLog("Cash Advance Masterfile - User has recommended the cash advance of employee `".$employeeName."`".$messageRemarks." and was moved to `Payroll Balance Pending` Status.","update", "success", "gcceforms", "user");
                return $query;
            }else{ return false; }
        }else{ return false; }
    }

    public function undoRecommendUpdate($id){
        $response = false;
        if($id){
            $this->db->select("id, employee");
            $this->db->from('gcceforms.cash_advance');
            $this->db->where('id', $id);
            $query = $this->db->get();
            if($query->num_rows() == 1){
                $row = $query->row();
                $employeeName = $this->getCurrentEmployeeName($row->employee);
                $data = array(
                    'recommend_by' => "",
                    'recommend_dt' => "",
                    'recommend_remark2' => "",
                    'status' => "HR Recommendation Pending",
                );

                $updateRecommendation = $this->db->update('gcceforms.cash_advance', $data, array("id" => $row->id));
                if($updateRecommendation){
                    $this->core_layout->setEventLog("Cash Advance Masterfile - User has undo the cash advance recommendation of employee `".$employeeName."` and was moved to `Sup Recommendation` Status.","update", "success", "gcceforms", "user");
                    $response = $updateRecommendation;
                }
            }
        }
        return $response;
    }

    public function disapproveUpdate($id){
        $post = $this->input->post();
        $trimmedRemarks = trim($post['disapproved_remarks']);
        $response = false;
        if($id){
            $this->db->select("id, employee");
            $this->db->from('gcceforms.cash_advance');
            $this->db->where('id', $id);
            $query = $this->db->get();
            if($query->num_rows() == 1){
                $row = $query->row();
                $employeeName = $this->getCurrentEmployeeName($row->employee);
                $data = array(
                    'disapproved_by' => $this->getCurrentEmployeeName(),
                    'disapproved_dt' => $this->dateTime,
                    'disapproved_remarks' => $trimmedRemarks,
                    'status' => "Disapproved",
                );
                
                $this->db->where('cash_advance.id', $id);
                $query = $this->db->update('gcceforms.cash_advance', $data);
                if($query){
                    $messageRemarks = $trimmedRemarks ? " and with a reason of `".$trimmedRemarks."`" : "";
                    $this->core_layout->setEventLog("Cash Advance Masterfile - User has disapprove the cash advance of employee `".$employeeName."`".$messageRemarks." and was moved to `Disapproved` Status.","update", "success", "gcceforms", "user");
                    $response = $query;
                }
            }
        }
        return $response;
    }

    public function undoDisapprovalUpdate($id){
        if($id){
            $caDetails = $this->getCashAdvanceDetail($id);
            $caStatus = $caDetails->acctg_bal_by ? "Awaiting Approval" : "HR Recommendation Pending";
            $logStatus = $caStatus == "HR Recommendation Pending" ? "Sup Recommendation" : "Awaiting Approval";
            $employeeName = $this->getCurrentEmployeeName($caDetails->employee);
            $data = array(
                'disapproved_by' => "",
                'disapproved_dt' => "",
                'disapproved_remarks' => "",
                'status' =>  $caStatus,
            );
            $this->db->where('id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);
            if($query){
                $this->core_layout->setEventLog("Cash Advance Masterfile - User has undo the cash advance disapprove status of employee `".$employeeName."` as was moved to `".$logStatus."` Status.","update", "success", "gcceforms", "user");
                return $query;
            }else{ return false; }
        }else{ return false; }
    }

    function cancelUpdate($id){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'cancelled_by' => $this->getDisplayName(),
            'cancelled_dt' => $date,
            'cancelled_remarks' => $this->input->post('cancelled_remarks'),
            'status' => "Cancelled",
        );
        if($id){
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);

            $ca_details = $this->getCaDetails($id);
            $loan_data = array('active' => 3, 'remarks' => "[System Generated:Cancelled Cash Advance form CA Module]");
            $this->db->where('reference', $ca_details->reference_no);
            $this->db->update('gcchris.loans', $loan_data);

            if($query){
                $this->core_layout->setEventLog("Cash Advance Masterfile - User cancelled the CA of employee `".$id."` with a remarks of".$data['cancelled_remarks'].".","update", "success", "gcceforms", "user");
                return $query;
            }
        }
    }
    function setHrUpdate($id){
        $this->input->post();
        $recommend_remarks =  str_replace( ',', '', $this->input->post('recommend_remarks'));
        $hr_bal_remarks =  str_replace( ',', '', $this->input->post('hr_bal_remarks'));
        $date = date('Y-m-d H:i:s');
        $data = array(
            'recommend_remarks' => $recommend_remarks,
            'hr_bal_by' => $this->getDisplayName(),
            'hr_bal_dt' => $date,
            'hr_bal_remarks' => $hr_bal_remarks,
            'hr_remarks' => $this->input->post('hr_remarks'),
            'status' => "Accounting Balance Pending",
        );
        if($id){
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);
            if($query){
                $this->core_layout->setEventLog("Cash Advance Masterfile - User set payroll balance of employee `".$id."` with a allowable of `".$data['recommend_remarks']."` and HR Remarks of `".$data['hr_remarks']."`.","update", "success", "gcceforms", "user");
                return $query;
            }
        }
    }
    public function setAcctgUpdate($id){
        $post = $this->input->post();

        $arrKeys = array("cash_advance_pending", "cash_advance_interest", "cash_advance_balance", "sss_loan", "hdmf_loan", "outside_loan");
        foreach ($arrKeys as $key) {
            if(isset($post[$key]) && $post[$key]){ $post[$key] = trim(trim(str_replace( ',', '', $post[$key]), '₱')); }
        }

        $acctg_bal_remarks =  isset($post['acctg_bal_remarks2']) && is_numeric($post['acctg_bal_remarks2']) ? trim(str_replace( ',', '', $post['acctg_bal_remarks2'])) : $post['acctg_bal_remarks2'];
        $acctg_bal_status = $post['set_acctg_status'];
        if($acctg_bal_status == "Payroll Balance Pending"){ $final_remarks = 0.00; }
        else{ $final_remarks = $acctg_bal_remarks; }
        $post["acctg_bal_remarks2"] = is_numeric($acctg_bal_remarks) ? '₱ ' . number_format($acctg_bal_remarks, 2, '.', ',') : $post["acctg_bal_remarks2"];

        $date = date('Y-m-d H:i:s');
        $data = array(
            'acctg_bal_by' => $this->getDisplayName(),
            'acctg_bal_dt' => $date,
            'acctg_bal_remarks' => $final_remarks,
            'acctg_bal_remarks2' => $post["acctg_bal_remarks2"],
            'acctg_ca_interest' => $post["cash_advance_interest"],
            'acctg_ca_pending' => $post["cash_advance_pending"],
            'acctg_sss_loan' => $post["sss_loan"],
            'acctg_hdmf_loan' => $post['hdmf_loan'],
            'acctg_outside_loan' => $post['outside_loan'],
            'status' => $acctg_bal_status,
        );
        
        if(isset($post["cash_advance_balance"]) && $post["cash_advance_balance"]){ $data["acctg_ca_balance"] = $post["cash_advance_balance"]; }
        if(isset($post["other_charges"]) && $post["other_charges"]){ $data["acctg_other_charges"] = $post["other_charges"]; }

        if($id){
            $this->db->where('cash_advance.id', $id);
            $update = $this->db->update('gcceforms.cash_advance', $data);

            $this->db->reset_query();

            $this->db->select('employee, reference_no, amt_applied, purpose, status');
            $this->db->from('gcceforms.cash_advance');
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->get();

            $employeeName = $this->getCurrentEmployeeName($query->row()->employee);

		    $emp_details = $query->row_array();
            $emp_name = $this->getEmpName($emp_details['employee']);
            $status = $emp_details['status'];

            if($status == "Payroll Balance Pending" || $status == "HR Balance Pending"){
                //send email to miss grace
                $this->db->select('email,username');
                $this->db->from('gccmaster.tblusers');
                $this->db->where("is_suspended=0 AND email='hrpayroll@gccph.com'");
                $email = $this->db->get();
                $hr_email = $email->row_array();
                $this->email_send($emp_name, $emp_details['reference_no'], $emp_details['amt_applied'], $emp_details['purpose'], $hr_email['email']);
            } elseif ($status == 'Awaiting Approval'){
                //send email to sir jes
                $this->db->select('email');
                $this->db->from('gccmaster.tblusers');
                /*** $this->db->where("is_suspended=0 AND email='hr@gccph.com'"); ***/
                $this->db->where("is_suspended", 0);
                $this->db->group_start();
                $this->db->where("email","hr@gccph.com");
                $this->db->or_where("email","assthrman@gccaggregates.com");
                $this->db->group_end();
                $this->db->order_by("id", "DESC");
                $this->db->limit(1);
                $email = $this->db->get();
                if ($email->num_rows() == 1) {
                    $hr_email = $email->row()->email;
                    if($hr_email){
                        $this->email_send_approval($emp_name, $emp_details['reference_no'], $emp_details['amt_applied'], $emp_details['purpose'], $hr_email);
                    }
                }
            }else{
                //send email to miss grace
                $this->db->select('email,username');
                $this->db->from('gccmaster.tblusers');
                $this->db->where("is_suspended=0 AND email='paymaster@gccaggregates.com'");
                $email = $this->db->get();
                $hr_email = $email->row_array();
                //$this->email_send_approve($emp_name, $emp_details['reference_no'], $emp_details['amt_applied'], $emp_details['purpose'], $hr_email['email']);
                $this->email_send_approve($emp_name, $emp_details['reference_no'], $emp_details['amt_applied'], $emp_details['purpose'], 'juniordeveloper02@gccaggregates.com');
            }
            if($update){
                $tempLogData = array();
                if(isset($post['oc_label']) && $post['oc_label']){
                    foreach($post['oc_label'] as $key => $value) {
                        $tempLogData[] = "Other Charges of {$post['oc_label'][$key]}: {$post['oc_amount'][$key]}";
                    }
                    // $this->core_layout->setEventLog("Cash Advance Masterfile - User set accounting balance of employee `".$id."` with a remarks of `".$data['acctg_bal_remarks2']."` and status of `".$acctg_bal_status."`.","update", "success", "gcceforms", "user");
                    $this->db->select('ca_id');
                    $this->db->where('ca_id', $id);
                    $check_data = $this->db->get('gcceforms.ca_charges');
                    
                    if($check_data->num_rows() > 0) {
                        $this->db->where('ca_id', $id);
                        $delete = $this->db->delete('gcceforms.ca_charges');
                        if($delete) {
                            foreach($post['oc_label'] as $key => $value) {
                                $ca_label = $post["oc_label"][$key];
                                $ca_amount = $post["oc_amount"][$key];
                                $ca_data = array(
                                    'ca_id' => $id,
                                    'description' => $ca_label,
                                    'amount' => $ca_amount,
                                );
                                $this->db->insert('gcceforms.ca_charges', $ca_data);
                                $this->db->insert('gcceforms.ca_charges_logs', $ca_data);
                            }
                        }
                    } else {
                        foreach($post['oc_label'] as $key => $value) {
                            $ca_label = $post["oc_label"][$key];
                            $ca_amount = $post["oc_amount"][$key];
        
                            $ca_data = array(
                                'ca_id' => $id,
                                'description' => $ca_label,
                                'amount' => $ca_amount,
                            );
                            $this->db->insert('gcceforms.ca_charges', $ca_data);
                            $this->db->insert('gcceforms.ca_charges_logs', $ca_data);
                        }
                    }
                }else{
                    $this->db->where('ca_id', $id);
                    $this->db->delete('gcceforms.ca_charges');
                }
                $implodedLogs = implode(", ", $tempLogData);
                $tempImplodedLogs = is_array($tempLogData) && !empty($tempLogData) ? "`".$implodedLogs."`" : "";
                $this->core_layout->setEventLog("User cash advance status has been set to `Accounting Balance` for employee `".$employeeName."` with a remarks of `".$data['acctg_bal_remarks2']."` and status of `".$acctg_bal_status."`. Cash Advance Balance Pending: `".$data['acctg_ca_pending']."` Cash Advance Interest: `".$data['acctg_ca_interest']."` SSS: `".$data['acctg_sss_loan']."` HDMF Loan: `".$data['acctg_hdmf_loan']."` HDMF Loan: `".$data['acctg_outside_loan']."`".$tempImplodedLogs.".","update", "success", "gcceforms", "user");
            }
            return $update;
        }
    }

    public function approveUpdate($id){
        $response = false;
        if($id){
            $post = $this->input->post();
            $trimmedRemarks = trim($post['approved_remarks']);
            $amtApproved = (float) str_replace(['₱', ','], '', $post['amt_approved']);
            $data = array(
                'approved_by' => $this->getCurrentEmployeeName(),
                'approved_dt' => $this->dateTime,
                'amt_approved' => $amtApproved,
                'approved_remarks' => $trimmedRemarks,
                'status' => "Approved",
            );
            
            $caDetails = $this->getCaDetails($id);
            $employeeName = $this->getCurrentEmployeeName($caDetails->employee);
            $row = $this->db->get_where('gccmaster.tblemployees', array('id' => $caDetails->employee))->row();
            $phoneNo = $row->mobile_no;
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);
            if($query){
                $messageRemarks = $trimmedRemarks !== "" ? " with a remarks of `".$trimmedRemarks."`" : "";

                $this->sendTelegram($id);
                $sendMsgNotification = $this->sendSMSNotification($id, $phoneNo);
                if($sendMsgNotification !== false){
                    $this->core_layout->setEventLog("Cash Advance Masterfile - User approved CA of employee `".$employeeName."`".$messageRemarks.". message sent","update", "success", "gcceforms", "user");
                } else {
                    $this->core_layout->setEventLog("Cash Advance Masterfile - User approved CA of employee `".$employeeName."`".$messageRemarks.". message not sent!!", "update", "error", "gcceforms", "system");
                }
                $this->core_layout->setEventLog("Cash Advance Masterfile - User approved CA of employee `".$employeeName."`".$messageRemarks.".", "update", "success", "gcceforms", "user");
                $response = $query;
            }
        }
        
        return $response;
    }

    function getCaDetails($id){
        $this->db->select("*");
        $this->db->from('gcceforms.cash_advance');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function undoApprovalUpdate($id){
        $response = false;
        if($id){
            $caDetails = $this->getCaDetails($id);
            $employeeName = $this->getCurrentEmployeeName($caDetails->employee);
            $data = array(
                'approved_by' => "",
                'approved_dt' => "",
                'amt_approved' => "",
                'approved_remarks' => "",
                'status' => "Awaiting Approval",
            );
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);
            if($query){
                $this->core_layout->setEventLog("Cash Advance Masterfile - User has updated the cash advance status into `Awaiting Approval` after ticking `Undo Approval` for the employee `".$employeeName."` with reference no `".$caDetails->reference_no."`.","update", "success", "gcceforms", "user");
                $response = $query;
            }
        }
        return $response;
    }

    function printCashAdvanceDetails($id){
        $ids= implode($id);
        $this->db->select("a.*, CONCAT(b.firstname,' ',b.lastname) AS employee_name,b.idno,b.date_start , b.bday, CONCAT(c.code, '\n', d.code, '\n', e.name) AS company_con");
        $this->db->from('gcceforms.cash_advance a');
        $this->db->join('gccmaster.tblemployees b','a.employee=b.id','left');
        $this->db->join('gcchris.tblcompanies c','a.company=c.id','left');
        $this->db->join('gcchris.tbldepartments d','a.department=d.id','left');
        $this->db->join('gcchris.tblposition e','a.position=e.id','left');
        $this->db->where('a.id',$ids);
        $query = $this->db->get();
        return $query->row();
    }

    function archiveList(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"1", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowCount = 0;
        $rowData = array();
        $view_own_request = (in_array("view_own_request", $this->current_action)) ? true : false;
        $view_by_company = (in_array("view_by_company", $this->current_action)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        if(!$search){
            $rowData = $this->get_all_post_archive($view_own_request, $limit, $offset, $sortBy, $sortOrder, $view_by_company, $companyDescription);
            $rowCount = $this->get_all_post_archive_count($view_own_request, $view_by_company, $companyDescription);
        }

        if($search){
            $rowData = $this->get_searched_item_archive($view_own_request, $search, $limit, $offset, $sortBy, $sortOrder, $view_by_company, $companyDescription);
            $rowCount = $this->get_searched_item_archive_count($view_own_request, $search, $view_by_company, $companyDescription);

            if(!empty($search)){
                $this->core_layout->setEventLog("Cash Advance Masterfile - Searched `".$search."`.","search", "success", "gcceforms", "user");
            }
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_post_archive($view, $limit=10, $offset=0, $sortBy, $sortOrder, $view_by_company = false, $companyDescription = null){
        $sql = "a.id, a.employee, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt";
        $date= date("Y-m-d", strtotime("-1 year"));
        $this->db->select($sql);
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
        
        if($view && ($this->user_data['emp_id']!=1)){
            $this->db->where('a.employee', $this->user_data['emp_id']);
        }

        if ($view_by_company) {
            $this->db->group_start();
                $this->db->where('b.company_id', (int)$this->user_data['company']);

                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            $this->db->group_end();
        }

        $this->db->group_start();
            $this->db->where("a.created_dt <=", $date);
            $this->db->or_where_in("a.status","Cancelled");
        $this->db->group_end();  
           
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $tempRs = (array) $rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                $rs->amt_applied =  number_format($rs->amt_applied, 2);
                $rs->amt_approved =  number_format($rs->amt_approved, 2);
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

    private function get_all_post_archive_count($view, $view_by_company = false, $companyDescription = null){
        $date= date("Y-m-d", strtotime("-1 year"));
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
        
        if($view && ($this->user_data['emp_id']!=1)){
            $this->db->where('a.employee', $this->user_data['emp_id']);
        }

        if ($view_by_company) {
            $this->db->group_start();
                $this->db->where('b.company_id', (int)$this->user_data['company']);

                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            $this->db->group_end();
        }

        $this->db->group_start();
            $this->db->where("a.created_dt <=", $date);
            $this->db->or_where_in("a.status","Cancelled");
        $this->db->group_end();

        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_item_archive($view, $search=null, $limit=10, $offset=0, $sortBy, $sortOrder, $view_by_company = false, $companyDescription = null){
        $date= date("Y-m-d", strtotime("-1 year"));
        if($search){
            $sql = "a.id, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt";
            $filterFields = array("a.id", "a.status", "a.reference_no", "b.firstname", "b.lastname", "a.amt_applied", "a.purpose", "a.amt_approved", "a.created_dt", "a.approved_dt");
            $this->db->select($sql);
            $this->db->from("gcceforms.cash_advance a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            
            if($view && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.employee', $this->user_data['emp_id']);
            }

            if ($view_by_company) {
                $this->db->group_start();
                    $this->db->where('b.company_id', (int)$this->user_data['company']);
    
                    if ($companyDescription) {
                        $this->db->where('a.company', $companyDescription);
                    }
                $this->db->group_end();
            }

            $this->db->group_start();
                $this->db->where("(a.created_dt <= '$date' OR a.status = 'Cancelled')");
            $this->db->group_end();
              
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); $this->db->or_like("CONCAT(b.firstname,' ',b.lastname )", $search, "both");}
            }
            $this->db->group_end();
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
    
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $key => $rs){
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $rs->amt_applied =  number_format($rs->amt_applied, 2);
                    $rs->amt_approved =  number_format($rs->amt_approved, 2);
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
        }else{
            return array();
        }
    }

    private function get_searched_item_archive_count($view, $search=null, $view_by_company = false, $companyDescription = null){
        $date= date("Y-m-d", strtotime("-1 year"));
        $rowCount = 0;
        if($search){
            $sql = "a.id, a.status, a.reference_no, CONCAT(b.firstname,' ',b.lastname) AS name, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt";
            $filterFields = array("a.id", "a.status", "a.reference_no", "b.firstname", "b.lastname", "a.amt_applied", "a.purpose", "a.amt_approved", "a.created_dt", "a.approved_dt");
            $this->db->select($sql);
            $this->db->from("gcceforms.cash_advance a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            
            if($view && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.employee', $this->user_data['emp_id']);
            }

            if ($view_by_company) {
                $this->db->group_start();
                    $this->db->where('b.company_id', (int)$this->user_data['company']);
    
                    if ($companyDescription) {
                        $this->db->where('a.company', $companyDescription);
                    }
                $this->db->group_end();
            }

            $this->db->where("(a.created_dt <= '$date' OR a.status = 'Cancelled')");
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); $this->db->or_like("CONCAT(b.firstname,' ',b.lastname )", $search, "both");}
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }
    function getDaily(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowCount = 0;
        $rowData = array();
       
            $rowData = $this->get_all_post_daily($limit, $offset, $sortBy, $sortOrder);
           
       

        $totalNotFiltered = $rowCount;

        
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_post_daily($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        
        $check = date('Y-m-d');
        $sql = "a.id, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose";
       
        $this->db->select($sql);
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
        $this->db->like('a.created_dt', $check);
        $this->db->limit($limit, $offset);
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $tempRs = (array) $rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
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
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowCount = 0;
        $rowData = array();
       
            $rowData = $this->get_all_post_weekly($limit, $offset, $sortBy, $sortOrder);
           
       

        $totalNotFiltered = $rowCount;

        
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_post_weekly($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        
        $check =date('Y-m-d',strtotime("-7 days"));
        $sql = "a.id, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose";
       
        $this->db->select($sql);
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
        $this->db->where('a.created_dt >= ', $check);
        $this->db->limit($limit, $offset);

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $tempRs = (array) $rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
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
    function m_get_cash_analytics_for_dashboard(){
        $this->db->select("a.status, COUNT(a.id) AS count");  
        $this->db->from("gcceforms.cash_advance a");
        $this->db->where("a.status !=","");
        //hide payraol balance pending
        $this->db->where("a.status !=","Payroll Balance Pending");
        $this->db->where("a.status !=","HR Balance Pending");
        $this->db->group_by("a.status");
        //original code
        $this->db->order_by("FIELD(a.status, 'Hr Recommendation Pending', 'Accounting Balance Pending', 'Awaiting Approval', 'Approved', 'Disapproved', 'Cancelled')");
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                
                $arrData[$key] = $rs;
            }
            //unset($arrData[0]);
            return json_decode(json_encode($arrData));
        }else{
            return array();
        }
    }

    function getFileContent($id){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"1", "dir"=>"desc"));
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowData = array();
        $rowData = $this->getUploadedCashAdvance($id, $sortBy, $sortOrder);
     
        $resultset["data"] = $rowData;

        return $resultset;
    }

 
    protected function getUploadedCashAdvance($id, $sortBy, $sortOrder){
        $arrData = array();
        $this->db->select("b.*, a.id, a.employee");
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gcceforms.ca_attachments b", "a.id = b.ca_id", "LEFT");
        $this->db->where('b.ca_id', $id);

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result() as $key => $rs){
                $thumbnailPath = realpath("./uploads/files/cash_advance/empcode_{$rs->employee}/thumbnails/{$rs->filename}");
                $filePath = realpath("./uploads/files/cash_advance/empcode_{$rs->employee}/{$rs->filename}");
                $rs->has_file = file_exists($filePath);
                $rs->has_thumbnail = file_exists($thumbnailPath);
                $rs->emp_id = $rs->employee;
                if($rs->filename){ $rs->ext = explode(".", $rs->filename)[1]; }
                $arrData[$key] = $rs;
            }
        }

        return $arrData;
    }

    // cash advance original file attachment
    function uploadFile(){
        $resultset = array();
        $post = $this->input->post();
        $employeeId = $this->user_data['emp_id'];
        if(isset($post['emp_id'])){

            $filePath = "./uploads/files/cash_advance/empcode_{$post['emp_id']}";

            $createFilePath = false;

            if (!file_exists($filePath)) {
                $mkdir = mkdir($filePath, 0777, true);
                if ($mkdir){ $createFilePath = true; }
            }else{ $createFilePath = true; }

            if($createFilePath == false){
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                $resultset["toastr_state"] = "warning";
            }else{
                $config = array();
                $config['upload_path']          = $filePath;
                $config['allowed_types']        = '*';
                $config['max_size']             = 100000;
                $config['create_thumbnail']     = true;
                
                $session = $this->core_layout->getCurrentSession();
                $data = $this->file_upload->uploadFile($config);
            
                if($data["response"] == true){
                    $files = $data["files"][0];
                    $filename = $files["file_name"];
                    $ext = explode(".",  $filename);
                    if($filename){
                        $resultset["response"] = true;
                        $resultset["added_file"] = base_url("uploads/files/cash_advance/empcode_{$post['emp_id']}/{$filename}");
                        $resultset["render_file"] = $filename;
                        $resultset["toastr_msg"] = "Upload file successful.";
                        $resultset["toastr_state"] = "success";
                        $resultset["extension"] = $ext[1];
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "File upload failed!";
                    $resultset["toastr_state"] = "error";
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "User data not found!";
            $resultset["toastr_state"] = "error";
        }
        return $resultset;
    }
    // cash advance original file attachment

    function approvalList(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"1", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->get_all_approval($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_approval_count();
        }

        if($search){
            $rowData = $this->get_searched_approval($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_approval_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_approval($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        $date= date("Y-m-d", strtotime("-1 year"));
        $sql = "a.id, a.employee, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt";

        $this->db->select($sql);
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
        $this->db->where("a.created_dt >=", $date);
        $this->db->where("a.status !=","Cancelled");  
        $this->db->where("a.status","Awaiting Approval");  
     
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $tempRs = (array) $rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
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

    private function get_all_approval_count(){
        $date= date("Y-m-d", strtotime("-1 year"));
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
        $this->db->where("a.created_dt >=", $date);
        $this->db->where("a.status !=","Cancelled");  
        $this->db->where("a.status","Awaiting Approval");  
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_approval($search=null, $limit=10, $offset=0, $sortBy, $sortOrder){
        $date= date("Y-m-d", strtotime("-1 year"));
        if($search){
            $sql = "a.id, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt";
            $filterFields = array("a.id", "a.status", "a.reference_no", "b.firstname", "b.lastname", "a.amt_applied", "a.purpose", "a.amt_approved", "a.created_dt", "a.approved_dt");
            $this->db->select($sql);
            $this->db->from("gcceforms.cash_advance a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            //$this->db->where("(a.status!='Cancelled' AND a.created_dt>='$date' AND a.status='Awaiting Approval')");
            $this->db->where("a.status",'Awaiting Approval');  
            $this->db->where("a.created_dt >=", $date);
            $this->db->where("a.status !=","Cancelled");  
 
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); $this->db->or_like("CONCAT(b.firstname,' ',b.lastname )", $search, "both");}
            }
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $key => $rs){
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
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
        }else{
            return array();
        }
    }

    private function get_searched_approval_count($search=null){
        $date= date("Y-m-d", strtotime("-1 year"));
        $rowCount = 0;
        if($search){
            $sql = "a.id, a.status, a.reference_no, CONCAT(b.firstname,' ',b.lastname) AS name, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt";
            $filterFields = array("a.id", "a.status", "a.reference_no", "b.firstname", "b.lastname", "a.amt_applied", "a.purpose", "a.amt_approved", "a.created_dt", "a.approved_dt");
            $this->db->select($sql);
            $this->db->from("gcceforms.cash_advance a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            $this->db->where("a.created_dt >=", $date);
            $this->db->where("a.status !=","Cancelled");  
            $this->db->where("a.status","Awaiting Approval");  
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); $this->db->or_like("CONCAT(b.firstname,' ',b.lastname )", $search, "both");}
            }
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    private function sendEmailCompanyTo($companyTo = null) {
        $email = "cfo@gccph.com, costaccountant@gccph.com, proxfin@gccph.com, gccfin@gccph.com";
        if (is_numeric($companyTo)) {
            $this->db->select('code');
            $this->db->from("gcchris.tblcompanies");
            $this->db->where("id", $companyTo);
            $query = $this->db->get();
            $data = $query->row_array();
            $companyTo = $data['code'];
        }

        if ($companyTo) {
            $companyTo = strtoupper($companyTo);
            if ($companyTo == "GC&C" || $companyTo == "GC&C, INC.") {
                $email = "financeasst02@gccph.com";
            }
            if ($companyTo == "PROXIMA") {
                $email = "proxfinasst02@gccph.com";
            }
            if ($companyTo == "HOMEWORLD" || $companyTo == "HOME WORLD CONSTRUCTION") {
                $email = "proxfinasst01@gccph.com";
            }
            if ($companyTo == "WEGAPLAS" || $companyTo == "WEGAPLAS CORPORATION") {
                $email = "proxfinasst01@gccph.com";
            }
            if ($companyTo == "CROSSCAP" || $companyTo == "CROSSCAP FINANCING") {
                $email = "cfo@gccph.com";
            }
            if ($companyTo == "MCDS") {
                $email = "financeasst02@gccph.com";
            }
            if ($companyTo == "MTAG" || $companyTo == "MOUNTAIN AGGREGATES") {
                $email = "financeasst02@gccph.com";
            }
            if ($companyTo == "ADVERCOM" || $companyTo == "ADVERCOM MEDIA GROUP CO.") {
                $email = "gccfin@gccph.com";
            }
            if ($companyTo == "HOUSEHOLD") {
                $email = "financeasst02@gccph.com";
            }
            if ($companyTo == "ESTANCIA") {
                $email = "financeasst02@gccph.com, gccfin@gccph.com";
            }
            if ($companyTo == "RETIREE") {
                $email = "proxfinasst02@gccph.com, proxfinasst01@gccph.com, kttorres@gccph.com, financeasst02@gccph.com";
            }
            if ($companyTo == "CONTRACTOR") {
                $email = "proxfin@gccph.com, costaccountant@gccph.com";
            }
        }
        
        $tempEmail = explode(",", $email);
        $tempEmail = array_map("trim", $tempEmail);
        return $tempEmail;
    }
    
    private function sendEmailCompanyCcc($companyTo = null) {
        $email = "";
        if (is_numeric($companyTo)) {
            $this->db->select('code');
            $this->db->from("gcchris.tblcompanies");
            $this->db->where("id", $companyTo);
            $query = $this->db->get();
            $data = $query->row_array();
            $companyTo = $data['code'];
        }

        if ($companyTo) {
            $companyTo = strtoupper($companyTo);
            if ($companyTo == "GC&C" || $companyTo == "GC&C, INC.") {
                $email = "cfo@gccph.com, gccfin@gccph.com";
            }
            if ($companyTo == "MTAG" || $companyTo == "MOUNTAIN AGGREGATES") {
                $email = "cfo@gccph.com, gccfin@gccph.com";
            }
            if ($companyTo == "MONEYMILL" || $companyTo == "MONEY MILL FINANCING") {
                $email = "cfo@gccph.com, gccfin@gccph.com";
            }
            if ($companyTo == "HOUSEHOLD") {
                $email = "cfo@gccph.com, gccfin@gccph.com";
            }
            if ($companyTo == "PROXIMA") {
                $email = "cfo@gccph.com, proxfin@gccph.com";
            }
            if ($companyTo == "HOMEWORLD" || $companyTo == "HOME WORLD CONSTRUCTION") {
                $email = "cfo@gccph.com, costaccountant@gccph.com";
            }
            if ($companyTo == "WEGAPLAS" || $companyTo == "WEGAPLAS CORPORATION") {
                $email = "cfo@gccph.com, costaccountant@gccph.com";
            }
            if ($companyTo == "CONTRACTOR") {
                $email = "cfo@gccph.com";
            }
            if ($companyTo == "ADVERCOM" || $companyTo == "ADVERCOM MEDIA GROUP CO.") {
                $email = "cfo@gccph.com";
            }
            if ($companyTo == "ESTANCIA") {
                $email = "cfo@gccph.com";
            }
        }

        if($email){
            $tempEmail = explode(",", $email);
            $tempEmail = array_map("trim", $tempEmail);
            return $tempEmail;
        }else{
            return false;
        }
    }
    function get_ca_accounting_details($id){
        $this->db->select("hr_bal_remarks");
        $this->db->from("gcceforms.cash_advance");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row_array();
        return $data['hr_bal_remarks'];
    }

    function getCADetail($id){
        $post = $this->input->post();
        $result = array();
        $this->db->select("remarks, reference");
        $this->db->from("gcchris.loans");
        $this->db->where("reference", $post['ref']);
        $query = $this->db->get();
        $data = $query->row_array();
        $result['data'] = $data;
        $result['pay_count'] = $this->getEmployeeLoanPaymentHistory($data['reference'])['count'];
        return $result;
    }

    function getEmployeeLoanPaymentHistory($ref) {
        $resultSet = array();

        $this->db->select('id');
        $this->db->where('reference', $ref);
        $query = $this->db->get('gcchris.loans');
        $q = $query->row_array();

        $this->db->select("psloanpayments.*, ps.date_start, ps.date_end, ps.posted_by, ps.posted_at, emp.firstname, emp.lastname");
        $this->db->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id", "INNER");
        $this->db->join("gccmaster.tblemployees emp", "ps.posted_by = emp.id", "LEFT");
        $this->db->where("psloanpayments.loan_id", $q['id']);
        $this->db->where("ps.posted", 1);
        $resultSet["data"] = $this->db->get("payroll.payroll_sheet_loan_payments psloanpayments")->result();
        $resultSet['count'] = $this->db->get("payroll.payroll_sheet_loan_payments psloanpayments")->num_rows();
        return $resultSet;
    }
    function getPayrollPendings(){
        $result = array();
        $rowData = $this->get_payroll_pending();
        $rowCount = $this->get_payroll_pending_count();

        $result['data'] = $rowData;
        $result['count'] = $rowCount;

        return $result;
    }
    function get_payroll_pending(){
        $result = array();
        $date= date("Y-m-d", strtotime("-1 year"));
        $this->db->select('a.id as ca_id, UPPER(CONCAT(b.firstname, " ", b.lastname)) as fullname, a.status as ca_status, a.position as pst, a.reference_no as ca_ref, a.department as dept, FORMAT(a.amt_applied, 2) as amt, a.created_dt as created');
        $this->db->from('gcceforms.cash_advance a');
        $this->db->join('gccmaster.tblemployees b', 'b.id = a.employee', 'left');
        $this->db->where('a.status', 'Payroll Balance Pending');
        $this->db->where('DATE(a.created_dt) >=', $date);
        $this->db->group_by('a.id');
        $this->db->limit('10');

        $query = $this->db->get();
        //var_dump($this->db->last_query());
        $q = $query->result();
        $result = $q;
        return $result;
    }

    function get_payroll_pending_count(){
        $date= date("Y-m-d", strtotime("-1 year"));
        $this->db->from('gcceforms.cash_advance');
        $this->db->where('status', 'Payroll Balance Pending');
        $this->db->where('DATE(created_dt) >=', $date);

        $query = $this->db->get();
        $q = $query->result();
        $result = $query->num_rows();

        return $result;
    }

    function getPendingDatatableRequest($type){
     
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"1", "dir"=>"asc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();

        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->get_pending_all_post($type, $query_builder, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_pending_all_post_count($type, $query_builder);
        }

        if($search){
            $rowData = $this->get_pending_searched_item($type, $query_builder, $search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_pending_searched_item_count($type, $query_builder, $search);
            
            if(!empty($search)){
                $this->core_layout->setEventLog("Cash Advance Masterfile - Searched `".$search."`.","search", "success", "gcceforms", "user");
            }
            if(!empty($query_builder)){
                $this->core_layout->setEventLog("Cash Advance Masterfile - Generate masterfile through query builder `".$query_builder."`.","generate", "success", "gcceforms", "user");
            }
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_pending_all_post($type, $query_builder=null, $limit=10, $offset=0, $sortBy, $sortOrder){
        $getPrevilage = $this->core_layout->personal_roles_for_notif();
        $company = $this->user_data['company'];

        $date= date("Y-m-d", strtotime("-1 year", time()));
        $sql = "a.id, a.employee, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt, a.position as pst";

        $this->db->select($sql);
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
        if($type == 'pyrll'){
            $this->db->where("a.status","Payroll Balance Pending");
        }else if($type == 'acctg'){
            $this->db->where("a.status","Accounting Balance Pending");
        }else{
            $this->db->where("a.status","Awaiting Approval");
        }
        $this->db->where("a.created_dt >=", $date);
        if($query_builder){
            $this->db->where($query_builder);
        }

        if(in_array('ca_acctg_fo_notif', $getPrevilage)){
            $this->db->where('b.company_id', $company);
        }
            
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $tempRs = (array) $rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                $rs->position = $rs->pst;
                $rs->amt_applied =  number_format($rs->amt_applied, 2);
                $rs->amt_approved =  number_format($rs->amt_approved, 2);
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

    private function get_pending_all_post_count($type, $query_builder=null){
        $getPrevilage = $this->core_layout->personal_roles_for_notif();
        $company = $this->user_data['company'];

        $date= date("Y-m-d", strtotime("-1 year", time()));
        $this->db->from("gcceforms.cash_advance a");
        $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
        $this->db->where("a.created_dt >=", $date);
        if($type == 'pyrll'){
            $this->db->where("a.status","Payroll Balance Pending");
        }else if($type == 'acctg'){
            $this->db->where("a.status","Accounting Balance Pending");
        }else{
            $this->db->where("a.status","Awaiting Approval");
        } 
        if($query_builder){
            $this->db->where($query_builder);
        }

        if(in_array('ca_acctg_fo_notif', $getPrevilage)){
            $this->db->where('b.company_id', $company);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    function loan_activated_email($employeeName, $referenceNumber, $amount=0, $purpose="", $recipient = null){
        $resultset = array();
        $emailTo = array();
        $employeeName = mb_strtoupper($employeeName);
        //if($recipient){ $emailTo = is_array($recipient)? $recipient: array($recipient); }
        if($recipient){
            $emailTo = $recipient;
        }else{
            $emailTo = '';
        }

        $data = array("employee_name"=>$employeeName, "amount"=>$amount, "purpose"=>$purpose);
        $message = "";
        $message .= $this->load->view("eforms/email_templates/email_ca_loan_deduction", $data, true);
     
        $module = "eforms_ca_activated";
        $email_title = "Cash Advance Loan";
        $content_title = "Cash Advance Loan - {$referenceNumber}";
        $content = $message;
        
        $overrideMailer = array();
        if($emailTo){ $overrideMailer["send_to"] = $emailTo; }
        
        $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
        if($sent){
            $resultset["status"] = true;
            $resultset["ref_no"] = $referenceNumber;
            $resultset["employee"] = $employeeName;
            
            $resultset["toastr_msg"] = "Loan activated, email has been sent";
            $resultset["toastr_status"] = "success";
            
            $this->core_layout->logNotification("Loan activated, email has been sent", "success");
        }else{
            $resultset["status"] = false;
            $resultset["toastr_msg"] = "Loan activated, failed to send email!";
            $resultset["toastr_status"] = "error";
            
            $this->core_layout->logNotification("Loan activated, failed to send email!", "error");
        }
        return $resultset;
    }

    function getDeptHead(){
        $user_id = $this->userdata['emp_id'];
        $this->db->select('a.head_id, a.code, b.id');
        $this->db->from('gcchris.tbldepartments a');
        $this->db->join('gccmaster.tblemployees b', 'a.head_id = b.id', 'left');
        $this->db->where('b.employee_status', 'Active');
        $this->db->where('a.code', 'payroll');
        $this->db->where('a.head_id', $user_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_pending_searched_item($type,$query_builder=null, $search=null, $limit=10, $offset=0, $sortBy, $sortOrder){
        $date= date("Y-m-d", strtotime("-1 year", time()));
        if($search){
            $getPrevilage = $this->core_layout->personal_roles_for_notif();
            $company = $this->user_data['company'];

            $sql = "a.id, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt, a.position as pst";
            $filterFields = array("a.id", "a.status", "a.reference_no", "b.firstname", "b.lastname", "a.amt_applied", "a.purpose", "a.amt_approved", "a.created_dt", "a.approved_dt");
            $this->db->select($sql);
            $this->db->from("gcceforms.cash_advance a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            if($type == 'pyrll'){
                $this->db->where("a.status","Payroll Balance Pending");
            }else if($type == 'acctg'){
                $this->db->where("a.status","Accounting Balance Pending");
            }else{
                $this->db->where("a.status","Awaiting Approval");
            }
            $this->db->where("a.created_dt >=", $date);
            if($query_builder){
                $this->db->where($query_builder);
            }

            if(in_array('ca_acctg_fo_notif', $getPrevilage)){
                $this->db->where('b.company_id', $company);
            }
                
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); $this->db->or_like("CONCAT(b.firstname,' ',b.lastname )", $search, "both");}
            }
            $this->db->group_end();
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $key => $rs){
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $rs->position = $rs->pst;
                    $rs->amt_applied =  number_format($rs->amt_applied, 2);
                    $rs->amt_approved =  number_format($rs->amt_approved, 2);
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
        }else{
            return array();
        }
    }

    private function get_pending_searched_item_count($type, $query_builder=null, $search=null){
        $date= date("Y-m-d", strtotime("-1 year", time()));
        $rowCount = 0;
        if($search){
            $getPrevilage = $this->core_layout->personal_roles_for_notif();
            $company = $this->user_data['company'];

            $sql = "a.id, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt";
            $filterFields = array("a.id", "a.status", "a.reference_no", "b.firstname", "b.lastname", "a.amt_applied", "a.purpose", "a.amt_approved", "a.created_dt", "a.approved_dt");
            $this->db->select($sql);
            $this->db->from("gcceforms.cash_advance a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            $this->db->where("a.created_dt >=", $date);
            if ($type == 'pyrll'){
                $this->db->where("a.status","Payroll Balance Pending");
            } elseif ($type == 'acctg'){
                $this->db->where("a.status","Accounting Balance Pending");
            } else {
                $this->db->where("a.status","Awaiting Approval");
            }
            if($query_builder){
                $this->db->where($query_builder);
            }

            if(in_array('ca_acctg_fo_notif', $getPrevilage)){
                $this->db->where('b.company_id', $company);
            }

            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); $this->db->or_like("CONCAT(b.firstname,' ',b.lastname )", $search, "both");}
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    public function posting_update($id){
        $resultset = array();
        if($id){
            $caDetails = $this->db->get_where("gcceforms.cash_advance", array('id' => $id))->row();
            $employeeName = $this->getCurrentEmployeeName($caDetails->employee);
            $data = array(
                'for_posting_by' => $this->getCurrentEmployeeName(),
                'for_posting_dt' => $this->dateTime,
                'status' => 'For Posting'
            );
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);

            if($query){
                $this->core_layout->setEventLog("Cash Advance Masterfile - User has updated the cash advance status into `For Posting` of the employee `".$employeeName."` with reference number `".$caDetails->reference_no."`.","update", "success", "gcceforms", "user"); 
                $resultset["toastr_msg"] = "Cash Advance Posted!";
                $resultset["toastr_status"] = true;
            }else{
                $resultset["toastr_msg"] = "Failed to Post Cash Advance!";
                $resultset["toastr_status"] = false;
            }
        }else{
            $resultset["toastr_msg"] = "Failed to Post Cash Advance, no data found!";
            $resultset["toastr_status"] = false;
        }
        return $resultset;
    }

    public function undo_for_posting($id){
        $resultset = array();
        if($id){
            $post = $this->input->post();
            $trimmedRemarks = trim($post['undo_posting_remark']);
            $caDetails = $this->db->get_where("gcceforms.cash_advance", array('id' => $id))->row();
            $employeeName = $this->getCurrentEmployeeName($caDetails->employee);

            $data = array(
                'for_posting_by' => $this->getCurrentEmployeeName(),
                'for_posting_dt' => $this->dateTime,
                'for_posting_remarks' => $trimmedRemarks,
                'status' => 'Awaiting Approval'
            );
            
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);

            if($query){
                $messageRemarks = $trimmedRemarks ? " and with a reason of `".$trimmedRemarks."`" : "";
                $this->core_layout->setEventLog("Cash Advance Masterfile - User has updated the cash advance status into `Awaiting Approval` after ticking `Undo Posting` for the employee `".$employeeName."` with reference number `".$caDetails->reference_no."`".$messageRemarks.".","update", "success", "gcceforms", "user"); 
                $resultset["toastr_msg"] = "Cash Advance Undo Posted!";
                $resultset["toastr_status"] = true;
            }else{
                $resultset["toastr_msg"] = "Failed to undo Post Cash Advance";
                $resultset["toastr_status"] = false;
            }
        }else{
            $resultset["toastr_msg"] = "Failed to undo Post Cash Advance, no data found!";
            $resultset["toastr_status"] = false;
        }
        return $resultset;

    }

    public function posted_update($id){
        $resultset = array();
        if($id){
            $post = $this->input->post();
            $trimmedRemarks = trim($post['posted_remarks']);
            $caDetails = $this->db->get_where("gcceforms.cash_advance", array('id' => $id))->row();
            $employeeName = $this->getCurrentEmployeeName($caDetails->employee);
            if(isset($_FILES['file'])){ $this->upload_file($id, $caDetails->employee); }
            $data = array(
                'posted_by' => $this->getCurrentEmployeeName(),
                'posted_dt' => $this->dateTime,
                'posted_remarks' => $trimmedRemarks,
                'status' => 'Posted'
            );
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);
            if($query){
                $messageRemarks = $trimmedRemarks ? " and with a remarks of `".$trimmedRemarks."`" : "";
                $this->core_layout->setEventLog("Cash Advance Masterfile - User has updated the cash advance status into `Posted` of the employee `".$employeeName."` with reference number `".$caDetails->reference_no."`".$messageRemarks.".","update", "success", "gcceforms", "user");
                $resultset["toastr_msg"] = "Cash Advance Posted!";
                $resultset["toastr_status"] = true;
            }else{
                $resultset["toastr_msg"] = "Failed to Post Cash Advance!";
                $resultset["toastr_status"] = false;
            }
        }else{
            $resultset["toastr_msg"] = "Failed to Post Cash Advance, no data found!";
            $resultset["toastr_status"] = false;
        }

        return $resultset;
    }

    public function undo_posted($id){
        $resultset = array();
        if($id){
            $post = $this->input->post();
            $trimmedRemarks = trim($post['undo_posted_remark']);
            $caDetails = $this->db->get_where("gcceforms.cash_advance", array('id' => $id))->row();
            $employeeName = $this->getCurrentEmployeeName($caDetails->employee);
            $data = array(
                'posted_by' => $this->getCurrentEmployeeName(),
                'posted_dt' => $this->dateTime,
                'posted_remarks' => $trimmedRemarks,
                'status' => 'For Posting'
            );
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);
            if($query){
                $messageRemarks = $trimmedRemarks ? " and with a reason of `".$trimmedRemarks."`" : "";
                $this->core_layout->setEventLog("Cash Advance Masterfile - User has updated the cash advance status into `For Posting` after ticking `Undo Posted` for the employee `".$employeeName."` with reference number `".$caDetails->reference_no."`".$messageRemarks.".","update", "success", "gcceforms", "user");
                $resultset["toastr_msg"] = "Cash Advance Undo Posted!";
                $resultset["toastr_status"] = true;
            }else{
                $resultset["toastr_msg"] = "Failed to undo Post Cash Advance";
                $resultset["toastr_status"] = false;
            }
        }else{
            $resultset["toastr_msg"] = "Failed to Undo Post Cash Advance, no data found!";
            $resultset["toastr_status"] = false;
        }

        return $resultset;
    }

    public function final_approval_update($id){
        $resultset = array();
        if($id){
            $post = $this->input->post();
            $trimmedRemarks = isset($post['final_remarks']) ? trim($post['final_remarks']): null;
            $caDetails = $this->db->get_where("gcceforms.cash_advance", array('id' => $id))->row();
            $employeeName = $this->getCurrentEmployeeName($caDetails->employee);
            if(isset($_FILES['file'])){ $this->upload_file($id, $caDetails->employee); }
            $data = array(
                'final_approved_by' => $this->getCurrentEmployeeName(),
                'final_approved_dt' => $this->dateTime,
                'final_approved_remarks' => $trimmedRemarks,
                'status' => 'For Final Approval'
            );
            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);
            if($query){
                $messageRemarks = $trimmedRemarks ? " and with a remarks of `".$trimmedRemarks."`" : "";
                $this->core_layout->setEventLog("Cash Advance Masterfile - User has updated the cash advance status into `For Final Approval` of the employee `".$employeeName."` with reference number `".$caDetails->reference_no."`".$messageRemarks.".","update", "success", "gcceforms", "user");
                $resultset["toastr_msg"] = "Cash Advance For Final Approval!";
                $resultset["toastr_status"] = true;
            }else{
                $resultset["toastr_msg"] = "Failed to For Final Approval of Cash Advance!";
                $resultset["toastr_status"] = false;
            }
        }else{
            $resultset["toastr_msg"] = "Failed to For Final Approval of Cash Advance, no data found!";
            $resultset["toastr_status"] = false;
        }
        return $resultset;
    }


    protected function upload_file($id, $emp){
        $resultset = array();
        if($id){
            $filePath = "./uploads/files/cash_advance/empcode_".$emp;
            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
            }

            $config['upload_path'] = $filePath;
            $config['allowed_types'] = '*';
            $config['max_size'] = 100000;
            $config['file_name'] = $_FILES['file']['name'];

            $this->upload->initialize($config);

            if ($this->upload->do_upload('file')) {
                $upload_data = $this->upload->data();
                $filename = $upload_data['file_name'];
                if($filename){
                    /*** create thumbnail ***/
                    $created = $this->file_upload->createThumbnailPathFolder($filePath);
                    if($created){ $this->file_upload->resizeImage($filename, $filePath); }
                    /*** create thumbnail ***/
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Upload file successful.";
                    $resultset["toastr_state"] = "success";
                    $this->saveFile($id, $filename);
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "File upload to specific path failed!";
                    $resultset["toastr_state"] = "error";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "File upload failed!";
                $resultset["toastr_state"] = "error";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "User data not found!";
            $resultset["toastr_state"] = "error";
        }

        return $resultset;
    }

    protected function sendTelegram($id){
        $msg = "";
        if($id){
            $details = $this->db->get_where("gcceforms.cash_advance", array('id' => $id))->row();
            $emp = $this->core_layout->getEmployeeData($details->employee);

            $amount_deducted = $details->deduct_type == 'fixed' ? number_format($details->amt_to_b_deducted, 2) : $details->amt_to_b_deducted;

            $msg .= '<b>Cash Advance #</b>: '.strtoupper($details->reference_no).chr(10);
            $msg .= "<b>Employee</b>: ".strtoupper($emp['display_name_0']).chr(10);
            $msg .= "<b>Company</b>: ".strtoupper($details->company).chr(10);
            $msg .= "<b>Department</b>: ".strtoupper($details->department).chr(10);
            $msg .= "<b>Position</b>: ".strtoupper($details->position).chr(10);
            $msg .= "<b>Status</b>: ".$details->status.chr(10);
            $msg .= "<b>Amount Applied</b>: P".number_format($details->amt_applied, 2).chr(10);
            $msg .= "<b>Amount Approved</b>: P".number_format($details->amt_approved, 2).chr(10);
            $msg .= "<b>Amount to be Deducted</b>: ". ($details->deduct_type == 'fixed' ? "P" : "") . $amount_deducted . ($details->deduct_type == 'percentage' ? "%" : "").chr(10);
            $msg .= "<b>Purpose</b>: ".strtoupper($details->purpose).chr(10);
            $msg .= "<b>Date Created</b>: ".strtoupper(date("F d, Y", strtotime($details->created_dt))).chr(10);
            $msg .= "<b>Date Approved</b>: ".strtoupper(date("F d, Y", strtotime($details->approved_dt))).chr(10);
            $msg .= "<b>Approved By</b>: ".strtoupper($details->approved_by).chr(10);
            $msg .= "<b>Approved Remarks</b>: ".strtoupper($details->approved_remarks).chr(10);

            if($this->telegram_config_if_exist('cash_advance', 'count') > 0){
				$this->telegram($msg);
			}
        }

        return true;
    }

    public function telegram_config_if_exist($module, $data){
		$this->db->where("module",$module);
		$telegram_details = $this->db->get("gcceforms.telegram_config");
		$details = $telegram_details->row();
		$count = $telegram_details->num_rows();
		if($data == 'count'){
			return $count;
		}else{
			return $details;
		}
	}

    public function telegram($msg){
		try {
			$data = $this->telegram_config_if_exist('cash_advance', 'data');
			if($data){
				$telegrambot=$data->telegram_bot_token;
				$telegramchatid= $data->chat_id;
				$url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';$data=array('chat_id'=>$telegramchatid,'text'=>$msg,'parse_mode'=>'html');
				$options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),'ignore_errors'=>true),);
				$context=stream_context_create($options);
				$result=file_get_contents($url,false,$context);
				return $result;
			}else{
				return false;
			}
			
		} catch (Exception $e) {
			return false;
		}
	}

    function undo_awaiting_approval($id){
        $post = $this->input->post();
        $date = date('Y-m-d H:i:s');
        $resultset = array();

        $data = array(
            'undo_awaiting_approval_by' => $this->getDisplayName(),
            'undo_awaiting_approval_dt' => $date,
            'undo_awaiting_approval_remarks' => $post['undo_awaiting_remark'],
            'status' => 'Accounting Balance Pending'
        );

        if($id){
            $empId = $this->db->get_where("gcceforms.cash_advance", array('id' => $id))->row();

            $this->db->where('cash_advance.id', $id);
            $query = $this->db->update('gcceforms.cash_advance', $data);

            if($query){
                $this->core_layout->setEventLog("Cash Advance Masterfile - User undo Awaiting Approval of Cash Advance with id `".$id."` of employee `".$empId->employee."`.","update", "success", "gcceforms", "user"); 
                $resultset["toastr_msg"] = "Cash Advance Undo Awaiting Approval!";
                $resultset["toastr_status"] = true;
            }else{
                $resultset["toastr_msg"] = "Failed to undo Awaiting Approval Cash Advance";
                $resultset["toastr_status"] = false;
            }

        }
        return $resultset;

    }

    function get_blacklisted(){
        $resultset = array();
        $post = $this->input->post();

        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

        $rowCount = 0;
        $rowData = array();

        $view_by_company = (in_array("view_by_company", $this->current_action)) ? true : false;

        $rowData = $this->get_blacklisted_item($limit, $offset, $sortBy, $sortOrder, $search, $view_by_company);
        $rowCount = $this->get_blacklisted_item_count($search, $view_by_company);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_blacklisted_item($limit, $offset, $sortBy, $sortOrder, $search, $view_by_company = false){
        $filterFields = array('b.firstname', 'b.lastname', 'c.firstname', 'c.lastname');
        $resultset = array();
        $arrData = array();

        $this->db->select("a.*, UPPER(CONCAT(b.lastname,
        CASE WHEN UPPER(TRIM(b.suffix)) != 'N/A' AND
            UPPER(TRIM(b.suffix !='NONE')) AND b.suffix !='' AND
            b.suffix IS NOT NULL THEN CONCAT(' ', b.suffix) ELSE ''
        END, ', ', b.firstname, ' ',
        CASE WHEN UPPER(TRIM(b.middlename)) != 'N/A' AND UPPER(TRIM(b.middlename)) != 'NONE' AND
                TRIM(b.middlename) !='' AND b.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(b.middlename, 1, 1), '.') ELSE ''
        END)) as employee_name, UPPER(CONCAT(c.lastname,
        CASE WHEN UPPER(TRIM(c.suffix)) != 'N/A' AND
            UPPER(TRIM(c.suffix !='NONE')) AND c.suffix !='' AND
            c.suffix IS NOT NULL THEN CONCAT(' ', c.suffix) ELSE ''
        END, ', ', c.firstname, ' ',
        CASE WHEN UPPER(TRIM(c.middlename)) != 'N/A' AND UPPER(TRIM(c.middlename)) != 'NONE' AND
                TRIM(c.middlename) !='' AND c.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(c.middlename, 1, 1), '.') ELSE ''
        END)) as added_by");
        $this->db->from('gcceforms.ca_blacklisted a');
        $this->db->join('gccmaster.tblemployees b', 'b.id = a.emp_id', 'LEFT');
        $this->db->join('gccmaster.tblemployees c', 'c.id = a.added_by', 'LEFT');
        $this->db->where('b.employee_status', 'Active');
        $this->db->where('a.is_removed', 0);

        if ($view_by_company) {
            $this->db->where('b.company_id', $this->user_data['company']);
        }

        if($search){
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

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        // $this->db->group_by('a.id');
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result() as $key => $rs){
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $resultset[] = $v;
            }
        }
        return $resultset;
    }

    function get_blacklisted_item_count($search, $view_by_company = false){
        $filterFields = array('b.firstname', 'b.lastname', 'c.firstname', 'c.lastname');

        $this->db->select("a.*, UPPER(CONCAT(b.lastname,
        CASE WHEN UPPER(TRIM(b.suffix)) != 'N/A' AND
            UPPER(TRIM(b.suffix !='NONE')) AND b.suffix !='' AND
            b.suffix IS NOT NULL THEN CONCAT(' ', b.suffix) ELSE ''
        END, ', ', b.firstname, ' ',
        CASE WHEN UPPER(TRIM(b.middlename)) != 'N/A' AND UPPER(TRIM(b.middlename)) != 'NONE' AND
                TRIM(b.middlename) !='' AND b.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(b.middlename, 1, 1), '.') ELSE ''
        END)) as employee_name, UPPER(CONCAT(c.lastname,
        CASE WHEN UPPER(TRIM(c.suffix)) != 'N/A' AND
            UPPER(TRIM(c.suffix !='NONE')) AND c.suffix !='' AND
            c.suffix IS NOT NULL THEN CONCAT(' ', c.suffix) ELSE ''
        END, ', ', c.firstname, ' ',
        CASE WHEN UPPER(TRIM(c.middlename)) != 'N/A' AND UPPER(TRIM(c.middlename)) != 'NONE' AND
                TRIM(c.middlename) !='' AND c.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(c.middlename, 1, 1), '.') ELSE ''
        END)) as added_by");
        $this->db->from('gcceforms.ca_blacklisted a');
        $this->db->join('gccmaster.tblemployees b', 'b.id = a.emp_id', 'LEFT');
        $this->db->join('gccmaster.tblemployees c', 'c.id = a.added_by', 'LEFT');
        $this->db->where('b.employee_status', 'Active');
        $this->db->where('a.is_removed', 0);

        if ($view_by_company) {
            $this->db->where('b.company_id', $this->user_data['company']);
        }

        if($search){
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

    function getForBlacklistEmployee(){
        $get = $this->input->get();
        $resultarray = array();
        $arrData = array();

        $filterFields = array('firstname', 'lastname');

        $this->db->select('id, firstname, lastname, middlename, suffix');
        $this->db->from('gccmaster.tblemployees');
        $this->db->where('employee_status', 'Active');

        if(isset($get['q'])){
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $get['q'], "both");
                } else {
                    $this->db->or_like($field, $get['q'], "both");
                }
            }
            $this->db->group_end();
        }

        $this->db->order_by('firstname', 'ASC');
        $this->db->limit('10');

        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result() as $key => $rs){
                $tempRs = (array) $rs;

                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;

                $rs->id = $rs->id;
                $rs->text = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";

                unset($rs->firstname, $rs->lastname, $rs->middlename, $rs->suffix);

                $check_blacklisted = $this->db->get_where('gcceforms.ca_blacklisted', array('emp_id' => $rs->id, 'is_removed' => 0))->row();
                if($check_blacklisted == null && $check_blacklisted == ''){
                    $arrData[$key] = $rs;
                }
            }

            foreach ($arrData as $k => $v) {
                $resultarray[] = $v;
            }
        }

        return array("results"=>$resultarray);
    }

    function set_blacklist_emp(){
        $status = false;
        $post = $this->input->post();
        $result = array();
        $temp = array();

        foreach($post['emp'] as $row){
            $temp[] = $row['id'];
            $data = array(
                'emp_id' => $row['id'],
                'remarks' => $post['remark'],
                'added_dt' => date('Y-m-d H:i:s'),
                'added_by' => $this->user_data['emp_id']
            );

            $query = $this->db->insert('gcceforms.ca_blacklisted', $data);
            if($query){
                $status = true;
            }
        }

        if($status){
            $result['state'] = true;
            $result['message'] = 'Successfully saved the blacklisted employee(s).';
            $this->core_layout->setEventLog("Cash Advance Masterfile - User Blacklisted employee(s) in Cash Advance with employee id(s) of `".implode(', ', $temp)."`","insert", "success", "gcceforms", "user"); 
        }else{
            $result['state'] = false;
            $result['message'] = 'Error when saving of blacklisted employee(s)';
            $this->core_layout->setEventLog("Cash Advance Masterfile - User failed to blacklist employee(s) in Cash Advance with employee id(s) of `".implode(', ', $temp)."`","insert", "error", "gcceforms", "system"); 
        }

        return $result;
    }

    function remove_blacklist_emp(){
        $post = $this->input->post();
        $result = array();

        $data = array(
            'is_removed' => 1,
            'removed_dt' => date('Y-m-d H:i:s'),
            'removed_by' => $this->user_data['emp_id'],
            'remove_remarks' => $post['remark']
        );

        $this->db->where('id', $post['id']);
        $query = $this->db->update('gcceforms.ca_blacklisted', $data);

        if($query){
            $result['state'] = true;
            $result['message'] = 'Successfully removed the blacklisted employee.';
            $this->core_layout->setEventLog("Cash Advance Masterfile - User Removed the Blacklisted employee in Cash Advance with employee id of `".$post['id']."`","update", "success", "gcceforms", "user");
        }else{
            $result['state'] = false;
            $result['message'] = 'Error when removing of blacklisted employee.';
            $this->core_layout->setEventLog("Cash Advance Masterfile - User failed to remove the blacklist employee in Cash Advance with employee id of `".$post['id']."`","update", "error", "gcceforms", "system"); 
        }

        return $result;
    }

    public function setInterestPercentage(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post["id"], $post["interest_percentage"]) && $post["id"]){
            $logData = $this->getCaEmployeeReference($post["id"]);
            $referenceNo = isset($logData["reference_no"]) && $logData["reference_no"] ? $logData["reference_no"]: "Undefined";
            $employeeName = isset($logData["employee_name"]) && $logData["employee_name"] ? $logData["employee_name"]: "No Assigned Name";
            $percentage = isset($post["interest_percentage"]) && $post["interest_percentage"] ? $post["interest_percentage"]: null;

            $udpated = $this->db->update("gcceforms.cash_advance", array("acctg_ca_interest_percentage"=>$post["interest_percentage"]), array("id"=>$post["id"]));
            if($udpated && $this->db->affected_rows() === 1){
                $resultset["response"] = true;
                $resultset["toastr_msg"] = "Cash advance interest percentage of `{$percentage}%` with the reference # `{$referenceNo}` for employee `{$employeeName}` and has been added/updated successfully.";
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to add/update the cash advance interest percentage of `{$percentage}%` for employee `{$employeeName}` with the reference # `{$referenceNo}`!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }
        
        $notification = $resultset["toastr_msg"];
        $response = $resultset["response"] ? "success": "error";
        $this->core_layout->setEventLog($notification, "add/update", $response, $this->eformsTable, "user");

        return $resultset;
    }

    public function getCaEmployeeReference($id=null){
        $arrData = array();
        if($id){
            $this->db->select("ca.reference_no, UPPER(CONCAT(TRIM(emp.lastname), ', ', TRIM(emp.firstname),
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                 THEN CONCAT(' ', SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END,' ', 
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                 UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                 emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END)) as employee_name");
            $this->db->join("gccmaster.tblemployees as emp", "emp.id = ca.employee", "LEFT");
            $qTemp = $this->db->get_where("gcceforms.cash_advance as ca", array("ca.id"=>$id));
            if($qTemp->num_rows() === 1){
                $arrData = $qTemp->row_array();
            }
        }
        return $arrData;
    }

    public function tempUploadFile() {
        $post = $this->input->post();
        $resultset = array();
        if (isset($post['id']) && $post['id']) {
            $imagesPath = "./uploads/files/images/cash_advance/temporary/emp_{$post['id']}";
            $createFilePath = false;
            if (!file_exists($imagesPath)) {
                $mkdir = mkdir($imagesPath, 0777, true);
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
                $config['upload_path'] = $imagesPath;
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|PNG|JPG|JPEG|PDF';
                $config['max_size'] = 100000;
                $config['create_thumbnail'] = true;

                $data = $this->file_upload->uploadFile($config);
                if ($data["response"] === true) {
                    $thumbnailpath = "uploads/files/images/cash_advance/temporary/emp_{$post['id']}/thumbnails";
                    if (!file_exists(realpath($thumbnailpath))) {
                        mkdir($thumbnailpath, 0777, true);
                    }
                    $files = is_array($data["files"]) && count($data["files"]) > 0 ? $data["files"][0] : $data["files"];
                    $filename = is_array($data["files"]) && count($data["files"]) > 0 ? $data["files"][0]["file_name"]: $data["files"];
                    if ($filename) {
                        $dirpath = $imagesPath;
                        $this->saveThumbnail($dirpath . "/" . $filename, $thumbnailpath . "/" . $filename);
                        $resultset["response"] = true;
                        $resultset["file_path"] = $imagesPath."/".$filename;
                        $resultset["added_image"] = base_url("uploads/files/images/cash_advance/temporary/temp_{$post['id']}/{$filename}");
                        $resultset["temp_image"] = "{$filename}";
                        $resultset["file_type"] = $files["file_type"];
                        $resultset["is_image"] = $files["is_image"];
                        $resultset["svg_icon"] = base_url("assets/images/file_icons/pdf.svg");
                        $resultset["thumbnail"] = base_url("{$thumbnailpath}/{$filename}");
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
            $resultset["toastr_msg"] = "Employee data not found! Select Employee first";
            $resultset["toastr_state"] = "error";
        }

        return $resultset;
    }

    function saveThumbnail($source, $target) {
        $config = array(
            'image_library' => 'gd2',
            'source_image' => $source,
            'new_image' => $target,
            'maintain_ratio' => true,
            'create_thumb' => true,
            'thumb_marker' => '',
            'width' => 75,
            'height' => 75
        );
        $this->image_lib->initialize($config);
        $this->image_lib->resize();
        $this->image_lib->clear();
    }

    private function logChanges($currentData, $newData) {
            if (is_object($currentData)) {
                $currentData = get_object_vars($currentData);
            }
            if (is_object($newData)) {
                $newData = get_object_vars($newData);
            }
            $changes = array();
            $changesString = '';
            foreach ($currentData as $field => $value) {
                if (isset($newData[$field]) && $newData[$field]!= $value) {
                    $changes[$field] = array(
                        'old' => $value,
                        'new' => $newData[$field]
                    );
                }
            }
            foreach ($changes as $field => $change) {
                if ($field != 'attachments'){
                    $changesString.= " Field: $field, from: ". $change['old']. ", to: ". $change['new']. "\n";
                }
            }
            if (isset($newData['attachments'])) {
                sort($newData['attachments']);
                sort($currentData['attachments']);
                if (empty($newData['attachments'])) {
                    $diff = array_diff($currentData['attachments'], $newData['attachments']);
                } else {
                    $diff = array_diff($newData['attachments'], $currentData['attachments']);
                }
                if (!empty($diff)) {
                    $changesString.= " Field: attachments, from: ' ". implode(',', $currentData['attachments']). " ', to: '". implode(',', $newData['attachments']). "'\n";
                }
            } 
            return $changesString;
        }

        private function getCashAdvanceById($id){
            $this->db->select("*");
            $this->db->from("gcceforms.cash_advance");
            $this->db->where('id', $id);
            $query = $this->db->get(); 
            $results = $query->row(); 
            $this->db->reset_query();
            return $results;
        }

        private function getAttachmentsById($id){
            $this->db->select("*");
            $this->db->from("gcceforms.ca_attachments");
            $this->db->where('ca_id', $id);
            $query = $this->db->get(); 
            $results = $query->result();
            $this->db->reset_query();
            return $results;
        }

        function tempUpdateUploadFiles() {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post['id']) && $post['id']) {
                $imagesPath = "uploads/files/images/cash_advance/temporary/ca_{$post['id']}";
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
                    $config['allowed_types'] = 'jpg|jpeg|png|pdf|PNG|JPG|JPEG|PDF';
                    $config['max_size'] = 100000;
                    $config['create_thumbnail'] = true;
    
                    $data = $this->file_upload->uploadFile($config);
                    if ($data["response"] == true) {
                        $thumbnailpath = "uploads/files/images/cash_advance/temporary/ca_{$post['id']}/thumbnails";
                        if (!file_exists(realpath($thumbnailpath))) {
                            mkdir($thumbnailpath, 0777, true);
                        }
                        $files = is_array($data["files"]) && count($data["files"]) > 0 ? $data["files"][0] : $data["files"];
                        $filename = is_array($data["files"]) && count($data["files"]) > 0 ? $data["files"][0]["file_name"]: $data["files"];
                        if ($filename) {
                            $dirpath = $imagesPath;
                            $s = $this->saveThumbnail($dirpath . "/" . $filename, $thumbnailpath . "/" . $filename);
                            $resultset["response"] = true;
                            $resultset["file_path"] = $imagesPath."/".$filename;
                            $resultset["added_image"] = base_url("uploads/files/images/cash_advance/temporary/ca_{$post['id']}/{$filename}");
                            $resultset["temp_image"] = "{$filename}";
                            $resultset["file_type"] = $files["file_type"];
                            $resultset["is_image"] = $files["is_image"];
                            $resultset["svg_icon"] = base_url("assets/images/file_icons/pdf.svg");
                            $resultset["thumbnail"] = base_url("{$thumbnailpath}/{$filename}");
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
                $resultset["toastr_msg"] = "Employee data not found! Select Employee first";
                $resultset["toastr_state"] = "error";
            }
    
            return $resultset;
        }

        protected function sendSMSNotification($id, $phone){
            $this->db->select("reference_no, amt_approved, employee");
            $details = $this->db->get_where("gcceforms.cash_advance", array('id' => $id))->row();
            $amount = '₱' . number_format($details->amt_approved, 2);
            $name = strtoupper($this->getEmpName($details->employee));
            $referenceNumber = $details->reference_no;

            $date = date('F j, Y');
            $msg = "Hi $name, your cash advance request of {$amount} has been approved on {$date}.\nThe amount will be released to your account within 4-7 working days upon approval. For any questions, please contact your department in-charge in cash advance processing.\nThis is a system-generated message please do not reply to this number. Thank you!\nGC&C CARES";

            $smsResponse = $this->contacts->sendSMS($phone, $msg);
            $isSentResponse = isset($smsResponse["data"]) && $smsResponse["data"] !== false;
            if($isSentResponse){
                $this->core_layout->setEventLog("Sent SMS to `{$name}` notification for cash advance reference number `{$referenceNumber}`.","add", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed in sending SMS to `{$name}` notification for cash advance reference number `{$referenceNumber}`.","add", "error", "gcceforms", "system");
            }
            return $isSentResponse;
        }

        public function undoForFinal($id){
            $resultset = array();
            if($id){
                $post = $this->input->post();
                $trimmedRemarks = isset($post['approved_remarks']) ? trim($post['approved_remarks']): null;
                $caDetails = $this->db->get_where("gcceforms.cash_advance", array('id' => $id))->row();
                $employeeName = $this->getCurrentEmployeeName($caDetails->employee);
                $data = array(
                    'final_approved_by' => $this->getCurrentEmployeeName(),
                    'final_approved_dt' => $this->dateTime,
                    'final_approved_remarks' => $trimmedRemarks,
                    'status' => 'Awaiting Approval'
                );
                $this->db->where('cash_advance.id', $id);
                $query = $this->db->update('gcceforms.cash_advance', $data);
    
                if($query){
                    $messageRemarks = $trimmedRemarks ? " and with a reason of `".$trimmedRemarks."`" : "";
                    $this->core_layout->setEventLog("Cash Advance Masterfile - User has updated the cash advance status into `Awaiting Approval` after ticking `Undo For Final Approval` for the employee `".$employeeName."` with reference number `".$caDetails->reference_no."`".$messageRemarks.".","update", "success", "gcceforms", "user"); 
                    $resultset["toastr_msg"] = "Cash Advance Undo For Final Approval!";
                    $resultset["toastr_status"] = true;
                }else{
                    $resultset["toastr_msg"] = "Failed to undo For Final Approval Cash Advance";
                    $resultset["toastr_status"] = false;
                }
            }else{
                $resultset["toastr_msg"] = "Failed to undo For Final Approval Cash Advance, no data found!";
                $resultset["toastr_status"] = false;
            }
            return $resultset;
        }

        public function getCashAdvanceReport(){
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $post = $this->input->post();
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
            $dateRange = (isset($post["dateRange"]) && $post["dateRange"]) ? $post["dateRange"] : null;
            if($dateRange == null){
                $resultset["recordsTotal"] = 0;
                $resultset["recordsFiltered"] =  0;
                $resultset["data"] = [];
                return $resultset;
            }
            $rowData = $this->getCashAdvanceReportData($search, $limit, $offset, $sortBy, $sortOrder,$dateRange);
            $total = $this->getCashAdvanceReportDataCount($search,$dateRange);
            $resultset["recordsTotal"] = $total;
            $resultset["recordsFiltered"] =  $total;
            $resultset["data"] = isset($rowData) && $rowData ? $rowData: array();
            return $resultset;
        }

        private function getCashAdvanceReportData($search, $limit, $offset, $sortBy, $sortOrder,$dateRange){
            $filterFields = array("ca.id");
            $this->db->select("ca.id,ca.company, ca.purpose, ca.department, ca.position, ca.approved_by, ca.approved_dt, ca.acctg_sss_loan as sss_loan, ca.acctg_hdmf_loan as hdmf_loan, created_dt as date_created, ca.amt_approved, ca.acctg_outside_loan as med_loan,
                CASE 
                    WHEN LENGTH(e.middlename) > 1 THEN CONCAT(e.firstname, ' ', SUBSTRING(e.middlename, 1, 1), '. ', e.lastname)
                    ELSE CONCAT(e.firstname, ' ', e.middlename, ' ', e.lastname)
                END AS name,
                e.firstname as firstname,
                e.lastname as lastname,
                SUM(c.amount) AS total_charges,
            ");

            $this->db->from($this->cashAdvanceTable. ' as ca');
            $this->db->join($this->employeeTable. ' as e', 'ca.employee = e.id', 'left');
            $this->db->join($this->chargesTable. ' as c', 'ca.id = c.ca_id', 'left');
            $this->db->where('status', 'Approved');
            if ($dateRange) {
                list($startDate, $endDate) = explode('|', $dateRange);
                $this->db->where("DATE(ca.approved_dt) BETWEEN '$startDate' AND '$endDate'");
            }
            $this->db->group_by('ca.id');
            if(isset($search)){
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
            // if ($limit != -1) {
            //     $this->db->limit($limit, $offset);
            // }
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();
            return $query->result_array();
        }

        private function getCashAdvanceReportDataCount($search,$dateRange){
            $filterFields = array("ca.id");
            $this->db->where('status', 'Approved');
            $this->db->from($this->cashAdvanceTable. ' as ca');
            $this->db->join($this->employeeTable. ' as e', 'ca.employee = e.id', 'left');
            if ($dateRange) {
                list($startDate, $endDate) = explode('|', $dateRange);
                $this->db->where("DATE(ca.approved_dt) BETWEEN '$startDate' AND '$endDate'");
            }
            if(isset($search)){
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

        public function exportReport($type){
            $post = $this->input->post();
            $filter="";
            $dateRange = (isset($post["dateRange"]) && $post["dateRange"]) ? $post["dateRange"] : null;
            if ($dateRange) {
                list($startDate, $endDate) = explode('|', $dateRange);
                $startDate = trim($startDate);
                $endDate = trim($endDate);
                $startTimestamp = strtotime($startDate);
                $endTimestamp = strtotime($endDate);
                $filter .= " with date range from: <strong>".date('M d, Y', $startTimestamp)."</strong> to <strong>".date('M d, Y', $endTimestamp)."</strong>";
            }
           
            return $this->core_layout->setEventLog("Cash Advance Report exported using <strong>$type</strong>.".$filter." total result(s): ".$post['total'], "generate", "success", "gcceforms", "user");
        }

        public function released($id){
            $resultset = array();
            if($id){
                $post = $this->input->post();
                $trimmedRemarks = isset($post['released_remarks']) ? trim($post['released_remarks']): null;
                $trimmedDn = isset($post['dn_number']) ? trim($post['dn_number']): null;
                $trimmedVoucherRef = isset($post['voucher_ref']) ? trim($post['voucher_ref']): null;
                $caDetails = $this->getCaDetails($id);
                $employeeName = $this->getCurrentEmployeeName($caDetails->employee);
                $temp = array(
                    'status' => 'Released',
                    'released_by' => $this->getCurrentEmployeeName(),
                    'released_dt' => $this->dateTime,
                    'dn_no' => $trimmedDn,
                    'voucher_reference_no' => $trimmedVoucherRef,
                    'released_remarks' => $trimmedRemarks
                );

                $this->db->where('id', $id);
                $query = $this->db->update($this->cashAdvanceTable, $temp);
                if ($query) {
                    $messageRemarks = $trimmedRemarks ? " and with a remarks of `".$trimmedRemarks."`" : "";
                    $this->core_layout->setEventLog("Cash Advance for the employee `".$employeeName."` with reference no `".$caDetails->reference_no."` has been `RELEASED`".$messageRemarks.".", "insert", "success", "gcceforms", "user");
                    $this->releaseLoanCashAdvance($id);
                    $resultset['state'] = true;
                    $resultset['msg'] = 'Cash Advance is succefully Released!';
                } else {
                    $resultset['state'] = false;
                    $resultset['msg'] = 'Failed to Released Cash Advance';
                }
            }else{
                $resultset['state'] = false;
                $resultset['msg'] = 'No data found!';
            }

            return $resultset;
        }

        protected function releaseLoanCashAdvance($id=null){
            if($id){
                $ca_details = $this->getCaDetails($id);
                $employeeName = $this->getCurrentEmployeeName($ca_details->employee);
                $this->db->select('active');
                $this->db->from("gcchris.loans");
                $this->db->where("reference", $ca_details->reference_no);
                $q = $this->db->get();
                if ($q->num_rows() > 0) {
                    $rows = $q->row();
                    $status = $rows->active;
                    $tempRemarks = "[System Generated:Updated Cash Advance form CA Module]";
                    $approvedRemarks = $ca_details->approved_remarks;
                    if(isset($approvedRemarks) && $approvedRemarks){
                        $tempRemarks = "{$tempRemarks}, {$approvedRemarks}";
                    }
                    $loan_data = array('active' => $status, 'remarks' => $tempRemarks );
                    $this->db->where('reference', $ca_details->reference_no);
                    $loanUpdated = $this->db->update('gcchris.loans', $loan_data);
                    if($loanUpdated){
                        $msg = "Payroll Loan - Cash Advance loan with the reference no: `".$ca_details->reference_no."` for employee `".$employeeName."` was set to `".$status."` status.";
                        $this->core_layout->setEventLog($msg,"update", "success", "gcceforms", "user");
                    }else{
                        $msg = "Failed to set Payroll Loan - Cash Advance loan with the reference no: `".$ca_details->reference_no."` for employee `".$employeeName."` to `".$status."` status.";
                        $this->core_layout->setEventLog($msg, "update", "error", "gcceforms", "system");
                    }
                } else { $this->setCaLoanData($id); }
            }else{
                return false;
            }
        }

        protected function setCaLoanData($id=null){
            if($id){
                $ca_details = $this->getCaDetails($id);
                $employeeName = $this->getCurrentEmployeeName($ca_details->employee);
                $caInterestPercentage = $ca_details->acctg_ca_interest_percentage ? floatval($ca_details->acctg_ca_interest_percentage): 0.00;
                $loan_data = array(
                    'emp_id' => $ca_details->employee,
                    'loan_id' => 1,
                    'reference_id'=> $id,
                    'reference' => $ca_details->reference_no,
                    'amount' => $ca_details->amt_approved,
                    'deduction_type' => strtolower($ca_details->deduct_type) == 'percentage' ? 0 : 1,
                    'fixed_deduction_amt' => strtolower($ca_details->deduct_type) == 'fixed' ? $ca_details->amt_to_b_deducted : 0.00,
                    'percentage' => strtolower($ca_details->deduct_type) == 'percentage' ? $ca_details->amt_to_b_deducted : 0.00,
                    'interest_percentage' => $caInterestPercentage,
                    'active' => 0,
                    'created_by' => 0,
                    'created_at' => $this->dateTime,
                    'is_archived' => 0,
                    'archived_by' => 0,
                    'remarks' => "[System Generated:New Cash Advance form CA Module], {$ca_details->approved_remarks}"
                );

                $forPayrollLoan = $this->db->insert('gcchris.loans', $loan_data);
                if($forPayrollLoan){
                    $msg = "Cash Advance Masterfile - Cash Advance loan is automatically added to payroll deduction with the reference no: `".$ca_details->reference_no."` for employee `".$employeeName."` and was set to `Suspended` status.";
                    $this->core_layout->setEventLog($msg,"insert", "success", "gcceforms", "user");
                }else{
                    $msg = "Cash Advance Masterfile - Cash Advance loan failed to add to payroll deduction with the reference no: `".$ca_details->reference_no."` for employee `".$employeeName."`.";
                    $this->core_layout->setEventLog($msg,"insert", "error", "gcceforms", "system");
                }
            }else{ return false; }
        }

        /** get employee name function **/
        protected function getCurrentEmployeeName($empId=null){
            $tempId = $empId ? $empId : $this->core_layout->getCurrentEmployeeId();
            if ($tempId === null) return "";
            
            $this->db->select("UPPER(
                CONCAT(
                    firstname,
                    ' ',
                    CASE
                    WHEN UPPER(TRIM(middlename)) NOT IN ('N/A', 'NONE')
                        AND TRIM(middlename) != ''
                        AND middlename IS NOT NULL
                    THEN CONCAT(SUBSTRING(middlename, 1, 1), '. ')
                    ELSE ''
                    END,
                    lastname,
                    CASE
                    WHEN UPPER(TRIM(suffix)) NOT IN ('N/A', 'NONE')
                        AND TRIM(suffix) != ''
                        AND suffix IS NOT NULL
                    THEN CONCAT(' ', suffix)
                    ELSE ''
                    END
                )
            ) AS employee_name", false);
            $this->db->from("gccmaster.tblemployees");
            $this->db->where("id", $tempId);
            $query = $this->db->get();
            if($query->num_rows() === 1){ return $query->row()->employee_name; }
            else{ return ""; }
        }
        /** get employee name function **/
}