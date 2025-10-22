<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Receive_option_model extends CI_Model{
      protected $tbl_payroll_group = "payroll.payroll_group";
      protected $companyTable = "gcchris.tblcompanies";
      protected $tbl_employees = "gccmaster.tblemployees";
      function __construct(){
        parent::__construct();    
        $this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
        $this->loggedInUsername = $this->loggedinData["username"];
        }

      function getPayslipOptions(){
        $data = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $filtered = (isset($post["filtered"]) && $post["filtered"])? $post["filtered"]: false;
        $filter = (isset($post["ids"]) && count($post["ids"]) > 0 && $filtered)? $post["ids"]: false;
        $dateRange = array(
          'start' => (isset($post["fromDate"]) && $post["fromDate"]) ? date('Y-m-d', strtotime($post["fromDate"])) : null,
          'end' => (isset($post["toDate"]) && $post["toDate"]) ? date('Y-m-d', strtotime($post["toDate"])) : null
      );
        $rowCount = 0;
        $rowData = array();
        $rowData = $this->getPayslipOptionsData($filtered,$filter,$limit, $offset, $sortBy, $sortOrder, $search,$dateRange);
        $rowCount = $this->getPayslipOptionsDataCount($filtered,$filter,$search,$dateRange);
      
        $data["recordsTotal"] = $rowCount;
        $data["recordsFiltered"] = $rowCount;
        $data["data"] = $rowData;
        return $data;
      }

      function getPayslipOptionsData($filtered,$filter,$limit, $offset, $sortBy, $sortOrder, $search = null, $dateRange = null)
      {
          $data = array();
          $filterFields = array('emp.firstname', 'emp.middlename', 'emp.lastname', 'positions.name', 'companies.code');
          $this->db->select("emp.id, UCASE(CONCAT(emp.firstname, ' ', 
    CASE 
        WHEN emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none' THEN ''
        ELSE CONCAT(SUBSTRING(emp.middlename, 1, 1), '. ')
    END,
    emp.lastname, ' ',
    CASE 
        WHEN emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none' THEN ''
        ELSE emp.suffix
    END)) AS employee,
    UCASE(IF(positions.id IS NULL, emp.position, positions.name)) AS position, 
    UCASE(IF(companies.id IS NULL, emp.company_id, companies.code)) AS company,
    IFNULL(opt.is_printed, NULL) AS is_printed, 
    IFNULL(opt.is_telegram, 0) AS is_telegram,
    IFNULL(us.telegram_chat_id, null) as telegram_id,
    IFNULL(opt.is_email, 0) AS is_email,
    IFNULL(us.email, null) as email_id,"
  );
          $this->db->from('tblemployees AS emp');
          $this->db->join('gcchris.tblposition AS positions', 'emp.position = positions.id', 'left');
          $this->db->join('gcchris.tblcompanies AS companies', 'emp.company_id = companies.id','left');
          $this->db->join('gcchris.tblpayslip_options AS opt', 'emp.id = opt.emp_id','left'); 
          $this->db->join('gccmaster.tblusers as us', 'emp.id = us.emp_id','left');
          $this->db->where('emp.employee_status', 'Active');
          // Uncomment this section if you need date range filtering
          /*
          if ($dateRange && $dateRange['start'] && $dateRange['end']) {
              $this->db->where('receiving.received_date >=', $dateRange['start']);
              $this->db->where('receiving.received_date <=', $dateRange['end']);
          }
          */
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
              $this->db->where_in("emp.id", $filter);
            }
            else{
              return $data;
            }
          }
          // Group by the selected columns
          // $this->db->group_by('employee, position, company');
          if ($limit != -1) {
              $this->db->limit($limit, $offset);
          }
          $i = $sortOrder[0]['column'];
          $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
          $query = $this->db->get();
          //var_dump($this->db->last_query());
          if ($query->num_rows() > 0) {
              $data = $query->result();
          }
          return $data;
      }
      

      private function  getPayslipOptionsDataCount($filtered,$filter,$search,$dateRange){
        $filterFields = array('emp.firstname', 'emp.middlename', 'emp.lastname', 'positions.name', 'companies.code');
        $this->db->select("emp.id, UCASE(CONCAT(emp.firstname, ' ', 
        CASE 
            WHEN emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none' THEN ''
            ELSE CONCAT(SUBSTRING(emp.middlename, 1, 1), '. ')
        END,
        emp.lastname, ' ',
        CASE 
            WHEN emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none' THEN ''
            ELSE emp.suffix
        END)) AS employee,
        UCASE(IF(positions.id IS NULL, emp.position, positions.name)) AS position, 
        UCASE(IF(companies.id IS NULL, emp.company_id, companies.code)) AS company,
        IFNULL(opt.is_printed, 1) AS is_printed, 
        IFNULL(opt.is_telegram, 0) AS is_telegram,
        IFNULL(us.telegram_chat_id, null) as telegram_id,
        IFNULL(opt.is_email, 0) AS is_email"
      );
      $this->db->from('tblemployees AS emp');
      $this->db->join('gcchris.tblposition AS positions', 'emp.position = positions.id', 'left');
      $this->db->join('gcchris.tblcompanies AS companies', 'emp.company_id = companies.id','left');
      $this->db->join('gcchris.tblpayslip_options AS opt', 'emp.id = opt.emp_id','left'); 
      $this->db->join('gccmaster.tblusers as us', 'emp.id = us.emp_id','left');
      $this->db->where('emp.employee_status', 'Active');
      //   if ($dateRange && $dateRange['start'] && $dateRange['end']) {
      //     $this->db->where('receiving.received_date >=', $dateRange['start']);
      //     $this->db->where('receiving.received_date <=', $dateRange['end']);
      // }
      // $this->db->group_by('employee, position, company');
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
            $this->db->where_in("emp.id", $filter);
          }
          else{
            return 0;
          }
        }
        $query = $this->db->get();
        return $query->num_rows();
      }

      function updatePayslipOptions() {
        $post = $this->input->post();
        $employeeIds = $this->input->post('employeeIds');
        $emp_id = $this->loggedinData['emp_id'];
        $today = date('Y-m-d H:i:s');
        foreach ($employeeIds as $employeeId){
          $query = $this->db->get_where('gcchris.tblpayslip_options', array('emp_id' => $employeeId));
          $row = $query->row();
          $this->db->where('emp_id', $employeeId);
          if ($row) {
            if (isset($post['isTelegram']) && $post['isTelegram'] !== null) {
                $this->db->update('gcchris.tblpayslip_options', array('is_telegram' => $post['isTelegram'], 'updated_by' => $emp_id, 'updated_at' => $today));
                $this->core_layout->setEventLog("User ".$emp_id. " Updated payslip settings for ".$employeeId,"update", "success", "gcchris", "user");
            }
            if (isset($post['isPrinted']) && $post['isPrinted'] !== null) {
                $this->db->update('gcchris.tblpayslip_options', array('is_printed' => $post['isPrinted'], 'updated_by' => $emp_id, 'updated_at' => $today));
                $this->core_layout->setEventLog("User ".$emp_id. " Updated payslip settings for ".$employeeId,"update", "success", "gcchris", "user");
            }
            if (isset($post['isEmail']) && $post['isEmail'] !== null) {
                $this->db->update('gcchris.tblpayslip_options', array('is_email' => $post['isEmail'], 'updated_by' => $emp_id, 'updated_at' => $today));
                $this->core_layout->setEventLog("User ".$emp_id. " Updated payslip settings for ".$employeeId,"update", "success", "gcchris", "user");
            }
        }
        else {
          $data = array(
              'emp_id' => $employeeId,
              'is_telegram' => isset($post['isTelegram']) ? $post['isTelegram'] : 0,
              'is_printed' => isset($post['isPrinted']) ? $post['isPrinted'] : 1,
              'is_email' => isset($post['isEmail']) ? $post['isEmail'] : 0,
              'created_by'=>$emp_id,
          );
           $this->db->insert('gcchris.tblpayslip_options', $data);
              $this->core_layout->setEventLog("User ".$emp_id. " Updated payslip settings for ".$employeeId,"create", "success", "gcchris", "user");
      }
        }
        return true;
    }

    function selectPayrollGroup(){
      $get = $this->input->get();
      $arrData = array();
      $resultset = array();
      $payrollId = (isset($get["payrollId"]) && $get["payrollId"]) ? $get["payrollId"] : 0;
      $companyId = (isset($get["company_id"]) && $get["company_id"]) ? $get["company_id"] : 0;
      if ($companyId || $companyId == 0) {
          $this->db->select("id, description as text, employee_id");
          $this->db->from($this->tbl_payroll_group);
          $this->db->where("company_id", $companyId);
          $this->db->where("status", 1);
          $this->db->where("is_archived", 0);
          if (isset($get['term']) && $get['term']) {
              $this->db->like("description", $get['term'], "both");
          }
          if (!$payrollId=="All"){
            $this->db->limit(10);
          }
          $this->db->order_by("description", "ASC");
          $qTemp = $this->db->get();
          if ($qTemp->num_rows() > 0) {
              foreach ($qTemp->result() as $kk => $vv) {
                  $tempIds = @unserialize($vv->employee_id);
                  unset($vv->employee_id);
                  // Assuming you have an 'employee_status' column in tbl_employees
                  $this->db->from($this->tbl_employees . ' as emp');
                  $this->db->select('emp.id as employee_id');
                  $this->db->where('emp.employee_status', 'active');
                  $this->db->where_in('emp.id', $tempIds);
  
                  $queryEmployees = $this->db->get();
  
                  if ($queryEmployees->num_rows() > 0) {
                      // Fetch employee ids and add them to $vv or $arrData as needed
                      $employeeIds = array_column($queryEmployees->result_array(), 'employee_id');
                      $vv->employee_ids = $employeeIds;
                  }
  
                  $arrData[$kk] = $vv;
              }
          }
      }
  
      $resultset["results"] = $arrData;
      return $resultset;
  }
  
  

	public function getCompanySelect2Data(){
		$resultset = array();
		$arrData = array();
		$get = $this->input->get();
		$this->db->select("id, description as text");
		$this->db->from($this->companyTable);
		$this->db->where("is_archived", 0);
		$this->db->where("exclude", 0);
		if(isset($get["term"]) && $get["term"]){ $this->db->like("description", $get["term"], "both"); }
		$query = $this->db->get();

		if($query->num_rows() > 0){
			$arrData = $query->result();
		}
		
		$resultset["results"] = $arrData;
		return $resultset;
	}
    

    }