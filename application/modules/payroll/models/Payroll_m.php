<?php defined('BASEPATH') || exit('No direct script access allowed');
class Payroll_m extends CI_Model{
    protected $tbl_employees = "gccmaster.tblemployees";
    protected $tbl_tblcompanies = "gcchris.tblcompanies";
    protected $tbl_tblposition = "gcchris.tblposition";

    protected $tbl_attendance = "gcctimeutility.attendance";
    protected $tbl_personnel = "gcctimeutility.personnel";
    protected $tbl_personnel_location = "gcctimeutility.personnel_locations";
    protected $tbl_shift_schedule = "gcctimeutility.shift_schedule";
    protected $tbl_shift_schedule_resource = "gcctimeutility.shift_schedule_resource";
    protected $tbl_shift_schedule_list = "gcctimeutility.shift_schedule_list";
    protected $tbl_timesheet = "gcctimeutility.timesheet";
    protected $tbl_loa = "gcceforms.loa";
    protected $tbl_time_adjustments = "gcctimeutility.time_adjustments";
    protected $tbl_time_adjustments_meta = "gcctimeutility.time_adjustments_meta";
    protected $tbl_TO = "gcceforms.travel_order";
    protected $tbl_time_adjustments_shift_schedule = "gcctimeutility.time_adjustments_shift_schedule";
    protected $tbl_time_adjustments_eform_refs = "gcctimeutility.time_adjustments_eform_refs";
    protected $tbl_time_adjustments_overtime = "gcctimeutility.time_adjustments_overtime";

    protected $tbl_overtime = "gcceforms.overtime";
    protected $tbl_timesheet_overtime = "gcctimeutility.timesheet_overtime";
    protected $tbl_timesheet_imports = "gcctimeutility.timesheet_imports";

    protected $tbl_time_parameters = "gcctimeutility.time_parameters";
    protected $tbl_timesheet_excluded_employees = "gcctimeutility.timesheet_excluded_employees";
    protected $tbl_payroll_sheet = "payroll.payroll_sheet";
    protected $tbl_ps_logs = "payroll.payroll_sheet_logs";
    protected $tbl_payroll_group = "payroll.payroll_group";
    protected $tbl_ps_loan_payments = "payroll.payroll_sheet_loan_payments";
    protected $tbl_ps_loans = "payroll.loans";
    protected $tbl_ps_custom_adjustments = "payroll.payroll_sheet_custom_adjustments";
    protected $tbl_ps_created_adjustments = "payroll.payroll_sheet_created_adjustments";
    protected $tbl_hris_loans = "gcchris.loans";
    protected $tbl_ps_signatory = "payroll.ps_signatory";
    protected $tbl_temp_signatory = "payroll.temp_ps_signatory";
    protected $tbl_ps_week_counter = "payroll.ps_monthly_week_count";
    protected $tbl_ps_incentive_type = "payroll.payroll_type_incentive";
    protected $tbl_ps_remittance_parameters = "payroll.remittance_parameters";
    protected $tbl_ps_payout_schedule = "payroll.payout_schedule";
    protected $tbl_ps_regular_ndiff = "payroll.employee_regular_ndiff";

    protected $tbl_timesheet_monthly_employees = "gcctimeutility.timesheet_monthly_employees";
    protected $tbl_ps_employee_regular_ndiff = "payroll.employee_regular_ndiff";
    protected $tbl_payroll_group_transfer = "payroll.payroll_group_transfer";

    public function __construct(){
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        $this->load->model("gcctime/timesheet_model", "ts_model");
        $this->load->model("payroll/reports_m", "reports");
        $this->load->model('eforms/Cash_advance_m', "ca");
        date_default_timezone_set("Asia/Manila");
    }

    private function format_name($data)
    {
        $tempRs = (array)$data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        return $display_name;
    }

    private function getCompany($id)
    {
        $this->db->select("id, description");
        $this->db->from("gcchris.tblcompanies");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->description;
    }

    private function getDepartment($id)
    {
        $this->db->select("id, description");
        $this->db->from("gcchris.tbldepartments");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->description;
    }

    private function getPosition($id)
    {
        $this->db->select("id, name");
        $this->db->from("gcchris.tblposition");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->name;
    }

    public function selectEmployeePayrollSummary(){
        $get = $this->input->get();
        $resultarray = array();
        $companyIds = (isset($get["company_ids"]) && $get["company_ids"])? $get["company_ids"]: array();
        $this->db->select("a.id, a.firstname, a.lastname, a.middlename, a.suffix");
        $this->db->from("gccmaster.tblemployees a");
        $this->db->join("payroll.payroll_sheet b", "b.emp_id = a.id", "LEFT");
        $this->db->join("gcchris.tblcompanies c", "c.id = b.company_id" ,"LEFT");
        /*** $this->db->where("a.employee_status", "Active"); ***/
        if(is_array($companyIds) && count($companyIds) > 0){ $this->db->where_in("b.company_id", $companyIds); }
        if(isset($get["company_ids"]) && !is_array($get["company_ids"]) && $get["company_ids"]){
            $this->db->where("b.company_id", $get["company_ids"]);
        }
        
        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
            $this->db->like("a.firstname", $get['q'], "both");
            $this->db->or_like("a.lastname", $get['q'], "both");
            $this->db->group_end();
        }
        $this->db->group_by("a.id");
        $this->db->limit(10);
        $this->db->order_by("a.firstname", "ASC");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $display_employee = $this->format_name($_query);

                $data["id"] = $_query["id"];
                $data["text"] = $display_employee;
                $resultarray[] = $data;
            }
        }

        return array("results" => $resultarray);
    }

    function selectIncentiveType(){
        $resultset = array();
        $get = $this->input->get();
        $sqlSelect = "id, description as text, DATE_FORMAT(date_from , '%M %d') as dt_from, DATE_FORMAT(date_to , '%M %d') as dt_to, date_from, date_to, name";
        $this->db->select($sqlSelect);
        $this->db->from($this->tbl_ps_incentive_type);
        $this->db->where("is_active", 1);
        $this->db->where("is_archived", 0);
        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
            $this->db->like("code", $get['q'], "both");
            $this->db->or_like("description", $get['q'], "both");
            $this->db->group_end();
        }
        $this->db->limit(10);
        $this->db->order_by("trim(description)", "ASC");
        $query = $this->db->get();
        $resultset["results"] = $query->result();
        return $resultset;
    }

    function selectEmployee($type=null)
    {
        $get = $this->input->get();
        $resultarray = array();
        $companyIds = (isset($get["company_ids"]) && $get["company_ids"])? $get["company_ids"]: array();
        //$this->db->select("a.id, trim(a.firstname) as firstname, a.lastname, a.middlename, a.suffix");

        $this->db->select("a.id, UPPER(TRIM(CONCAT(a.firstname, ' ',
                CASE WHEN UPPER(TRIM(a.middlename)) != 'N/A' AND UPPER(TRIM(a.middlename)) != 'NONE' AND
                        TRIM(a.middlename) !='' AND a.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(a.middlename, 1, 1), '.') ELSE ''
                END,' ', a.lastname,
                CASE WHEN UPPER(TRIM(a.suffix)) != 'N/A' AND
                    UPPER(TRIM(a.suffix !='NONE')) AND a.suffix !='' AND
                        a.suffix IS NOT NULL THEN CONCAT(' ', a.suffix) ELSE ''
                END))) as text");
        $this->db->from("gccmaster.tblemployees a");
        $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id", "LEFT");
        
        if($type !== 'all' && $type === null){
            $this->db->where("a.employee_status", "Active");
        } elseif ($type !== 'all' && $type !== null) {
            $this->db->where("a.employee_status", $type);
        }

        if(is_array($companyIds) && count($companyIds) > 0){ $this->db->where_in("b.id", $companyIds); }
        if(isset($get["company_ids"]) && !is_array($get["company_ids"]) && $get["company_ids"]){
            $this->db->where("b.id", $get["company_ids"]);
        }

        $tempLimit = 10;
        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
            $this->db->like("a.firstname", $get['q'], "both");
            $this->db->or_like("a.lastname", $get['q'], "both");
            $this->db->or_like("CONCAT(a.firstname, ' ', a.lastname)", $get['q'], "both");
            $this->db->or_like("CONCAT(a.firstname, ' ', CONCAT(SUBSTR(a.middlename, 1, 1), '.'), ' ', a.lastname)", $get['q'], "both");
            $this->db->group_end();
            $tempLimit = 20;
        }
        $this->db->limit($tempLimit);
        $this->db->order_by("trim(a.firstname)", "ASC");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            /*** foreach ($query->result_array() as $_query) {
                $data = array();
                $display_employee = $this->format_name($_query);

                $data["id"] = $_query["id"];
                $data["text"] = $display_employee;
                $resultarray[] = $data;
            } ***/
           $resultarray = $query->result();
        }
        return array("results" => $resultarray);
    }

    function selectCompany()
    {
        $get = $this->input->get();
        $this->db->select("companies.id, companies.`code` `text`, companies.*");
        if(isset($get['q'])){
            $this->db->like("`code`", $get['q'], "BOTH");
        }
        $this->db->where("is_archived", 0);
        $this->db->where("exclude", 0);
        $this->db->order_by("`code`", "ASC");
        $results = $this->db->get("gcchris.tblcompanies companies")->result();
        return array("results" => $results, "sql" => $this->db->last_query());
    }

    public function select2CompanyData($companyColumn='code'){
        $this->db->select("companies.id, companies.`{$companyColumn}` `text`, companies.*");
        $this->db->where("is_archived", 0);
        $this->db->where("exclude", 0);
        $this->db->order_by("`{$companyColumn}`", "ASC");
        return $this->db->get("gcchris.tblcompanies companies")->result();

    }

    function select2PayoutScheduleData(){
        $this->db->select("id, name as text, occurrence");
        $this->db->order_by("id", "ASC");
        $results = $this->db->get("payroll.payout_schedule")->result();
        return $results;
    }

    function select2IncentiveTypeData(){
        $sqlSelect = "id, description as text, DATE_FORMAT(date_from , '%M %d') as dt_from, DATE_FORMAT(date_to , '%M %d') as dt_to, date_from, date_to, name";
        $this->db->select($sqlSelect);
        $this->db->from($this->tbl_ps_incentive_type);
        $this->db->where("is_active", 1);
        $this->db->where("is_archived", 0);
        $this->db->order_by("trim(description)", "ASC");
        $query = $this->db->get();
        $results = $query->result();
        
        return $results;
    }

    function selectDepartment()
    {
        $get = $this->input->get();
        $q = isset($get['q']) ? $get['q'] : '';

        $this->db->select("departments.id, departments.`description` `text`, departments.*");
        $this->db->where("departments.is_archived", 0);
        $this->db->where("departments.description !=", "");
        $this->db->like("`code`", $q, "BOTH");
        $this->db->order_by("`code`", "ASC");
        $results = $this->db->get("gcchris.tbldepartments departments")->result();
        return array("results" => $results, "sql" => $this->db->last_query());
    }

    private function getHolidays($date_from, $date_to)
    {
        $this->db->select("start_date, end_date, start_time, end_time");
        $this->db->from("gcchris.tblholidays");
        $this->db->where("classification", "Regular");
        $this->db->group_start();
        $this->db->where('start_date BETWEEN "' . date('Y-m-d', strtotime($date_from)) . '" AND "' . date('Y-m-d', strtotime($date_to)) . '"', NULL, FALSE);
        $this->db->or_where('end_date BETWEEN "' . date('Y-m-d', strtotime($date_from)) . '" AND "' . date('Y-m-d', strtotime($date_to)) . '"', NULL, FALSE);
        $this->db->group_end();
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $rs) {
                $date_from = date('Y-m-d', strtotime($date_from));
                $date_to = date('Y-m-d', strtotime($date_to));

                $start_days = strtotime($rs->start_date);
                $end_days = strtotime($rs->end_date);

                $diff_days = (($end_days - $start_days) / 86400) + 1;

                if ($rs->start_date == $rs->end_date) {
                    $rs->days = 1;
                } else {
                    if ($rs->start_date < $date_from) {
                        $from_days = strtotime($date_from);
                        $days = ($start_days - $from_days) / 86400;
                        $rs->days = $diff_days - $days;
                    } else if ($rs->end_date > $date_to) {
                        $to_days = strtotime($date_to);
                        $days = ($end_days - $to_days) / 86400;
                        $rs->days = $diff_days - $days;
                    } else {
                        $rs->days = $diff_days;
                    }
                }
                $result[] = $rs;
            }
            $sum = 0;
            for ($i = 0; $i < count($result); $i++) {
                $sum += $result[$i]->days;
            }
            return $sum;
        } else {
            return 0;
        }
    }

    private function getSalary($id)
    {
        $this->db->select("sal_rate, emp_id");
        $this->db->from("gcchris.tblsalaries");
        $this->db->where("emp_id", $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    private function computeSalary($id)
    {
        $sal_rate = $this->getSalary($id);
        $current_rate = end($sal_rate)['sal_rate'];
        $rate = str_replace(",", "", $current_rate);
        $total_rate = ($rate) ? $rate : 0;

        return $total_rate;
    }

    private function computeUndertime($basic, $ut, $late, $days)
    {
        $ut = ($ut == 0) ? 1 : $ut;
        $late = ($late == 0) ? 1 : $late;

        $undertime = $basic / (480 * $late);
        return $days != 0 ? number_format($undertime, 2) : 0;
    }

    private function computeCola($cola, $ut, $late, $days)
    {
        $ut = ($ut == 0) ? 1 : $ut;
        $late = ($late == 0) ? 1 : $late;

        $undertime = $cola / (480 * $late);
        return $days != 0 ? number_format($undertime * $days, 2) : 0;
    }

    private function computeAmount($basic, $days, $ut)
    {
        $amount = (floatval($basic) * $days) - $ut;
        $amount = number_format($amount, 2);
        return $amount;
    }

    private function getAllowance($id)
    {
        $this->db->select("SUM(rate) as allowance");
        $this->db->from("gcchris.allowances");
        $this->db->where("emp_id", $id);
        $query = $this->db->get();
        return $query->row_array()['allowance'];
    }

    function payrollSheetMasterfile()
    {
        $rowCount = 0;
        $rowData = array();
        $resultset = array();
        $post = $this->input->post();
        $date_today = date('Y-m-d');

        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
        $employee = (isset($post["employee"]) && $post["employee"]) ? $post["employee"] : null;
        $company = (isset($post["company"]) && $post["company"]) ? $post["company"] : null;
        $date_from = (isset($post["date_from"]) && $post["date_from"]) ? $post["date_from"] : $date_today;
        $date_to = (isset($post["date_to"]) && $post["date_to"]) ? $post["date_to"] : $date_today;

        $rowData = $this->payroll_sheet_masterfile_list($employee, $company, $date_from, $date_to, $search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->payroll_sheet_masterfile_count($employee, $company, $date_from, $date_to, $search);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function payroll_sheet_masterfile_list($employee, $company, $date_from, $date_to, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder)
    {
        $filterFields = array("a.firstname", "a.lastname");
        $this->db->select("a.id, a.firstname, a.lastname, a.middlename, a.suffix, a.company_id, a.department_id, a.position, a.basic_rate, a.payout_sched, a.payroll_type, c.date, c.total_time_rendered, SUM(c.total_time_rendered) days, SUM(c.total_ut) ut, SUM(c.total_late) late");
        $this->db->from('gccmaster.tblemployees a');
        $this->db->join('gcctimeutility.timesheet c', 'a.id = c.emp_id', 'LEFT');

        !empty($employee) ? $this->db->where_in("a.id", $employee) : $employee;
        !empty($company) ? $this->db->where_in("a.company_id", $company) : $company;

        if (isset($date_from) && isset($date_to)) {
            $this->db->group_start();
            $this->db->where("c.date >=", $date_from);
            $this->db->where("c.date <=", $date_to);
            $this->db->group_end();
        }

        /*** $this->db->where("a.employee_status", 'Active'); ***/
        $this->db->where("c.verified", '1');
        $this->db->group_by("a.id", "desc");
        if (isset($search)) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }

        if (isset($sortOrder)) {
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        } else {
            $this->db->order_by('a.lastname', 'asc');
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $rs->checkbox = "<input type='checkbox' name='check_payroll'>";
                $display_employee = $this->format_name($rs);
                $rs->company = (is_numeric($rs->company_id)) ? $this->getCompany($rs->company_id) : $rs->company_id;
                $rs->department = (is_numeric($rs->department_id)) ? $this->getDepartment($rs->department_id) : $rs->department_id;
                $rs->position = (is_numeric($rs->position)) ? $this->getPosition($rs->position) : $rs->position;
                $rs->display_employee = "<span><small>" . $rs->company . "</small><br><b>" . $display_employee . "</b><br><small>" . $rs->position . "</small></span>";

                //days calc
                $days = floatval($rs->days) / 60;
                $days = floatval($days) / 8;

                //allowance calc
                $colaT = 0;
                $cola = $this->getAllowance($rs->id) ? $this->getAllowance($rs->id) : 0;

                //check payroll type
                if ($rs->payroll_type == "daily"):
                    $basic_rate = $rs->basic_rate;
                else:
                    $basic_rate = $rs->basic_rate * 12 / 314;
                    $cola = $cola * 12 / 314;
                endif;

                $colaT = $cola * $days;

                $rs->rate = $basic_rate;
                $rs->sal_rate = number_format($rs->rate, 2);
                $rs->days = round($days);
                $rs->holiday = $this->getHolidays($date_from, $date_to);
                $rs->ut = ($rs->late != 0) ? $this->computeUndertime($rs->rate, $rs->ut, $rs->late, $rs->days) : 0;
                $colaAmount = ($rs->late != 0) ? $this->computeCola($cola, $rs->ut, $rs->late, $rs->days) : 0;
                $rs->amount = $this->computeAmount($rs->rate, $rs->days, $rs->ut);

                $rs->ot_hrs = 0;
                $rs->ot_amount = 0;
                $rs->nd_hrs = 0;
                $rs->nd_amount = 0;
                $rs->other_adj = 0;
                //$ecola = $cola;
                $rs->ecola = number_format($colaT - $colaAmount, 2);

                //gross computation
                $gross = 0;
                $gross = (floatval($rs->rate) * $rs->days) - $rs->ut + $rs->ot_amount + $rs->other_adj + $rs->ecola;
                $rs->gross_pay = number_format($gross, 2);

                $rs->sss = 0;
                $rs->n = 0;
                $rs->pagibig = 0;
                $rs->tax = 0;
                $rs->loans = 0;
                // $rs->net = number_format( (floatval($rs->rate) * $rs->days ) - $rs->ut + $rs->ot_amount + $rs->other_adj, 2);
                $rs->net = $rs->gross_pay;
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

    private function payroll_sheet_masterfile_count($employee, $company, $date_from, $date_to, $search = null)
    {
        $filterFields = array("a.firstname", "a.lastname");
        $this->db->select("a.id, a.firstname, a.lastname, a.middlename, a.suffix, a.company_id, a.department_id, a.position, c.date, c.total_time_rendered, SUM(c.total_time_rendered) days, SUM(c.total_ut) ut, SUM(c.total_late) late");
        $this->db->from('gccmaster.tblemployees a');
        $this->db->join('gcctimeutility.timesheet c', 'a.id = c.emp_id', 'LEFT');

        !empty($employee) ? $this->db->where_in("a.id", $employee) : $employee;
        !empty($company) ? $this->db->where_in("a.company_id", $company) : $company;

        if (isset($date_from) && isset($date_to)) {
            $this->db->where("c.date >=", $date_from);
            $this->db->where("c.date <=", $date_to);
        }

        /*** $this->db->where("a.employee_status", 'Active'); ***/
        $this->db->group_by("a.id", "desc");
        if (isset($search)) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }

        $query = $this->db->get();
        $count = $query->num_rows();
        return $count;
    }

    function calculateSSS_old($gross)
    {
        $target = 0;
        $target = $gross * 2;
        $sssrange = $this->getSSSRange($target);
        //return $target;

    }

    function getSSSRange($target)
    {
        $query = $this->db->sql("SELECT * FROM payroll.sss_table WHERE `from` ");
        return $query->result_array();
    }

    function payrollMasterfile()
    {
        $rowCount = 0;
        $rowData = array();
        $resultset = array();
        $date_today = date('Y-m-d');
        $post = $this->input->post();

        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;

        $rowData = $this->payroll_masterfile_list($search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->payroll_masterfile_count($search);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function payroll_masterfile_list($search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder)
    {
        $filterFields = array("a.company");
        $this->db->select("*");
        $this->db->from('payroll.payroll a');
        if (isset($search)) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }

        if (isset($sortOrder)) {
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        } else {
            $this->db->order_by('a.id', 'desc');
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
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

    private function payroll_masterfile_count($search = null)
    {
        $filterFields = array("a.company");
        $this->db->select("*");
        $this->db->from('payroll.payroll a');

        if (isset($search)) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }

        $query = $this->db->get();
        $count = $query->num_rows();
        return $count;
    }

    function updateEmployeePayrollData(){
        $post = $this->input->post();
        $resultarray = array();
        $hasApprovingAuthority = json_decode($post["approving_authority"]);

        /*** edited contents logging ***/
        $postBasicRate = isset($post['basic_rate']) && $post['basic_rate'] ? floatval($post['basic_rate']): 0;

        $tempEmployeeData = (object) $this->core_layout->getEmployeeData($post['id']);
        $editedContent = array();
        $fromContent = array();
        $tempKeys = array("payroll_type"=>"Payroll Type", "payout_sched"=>"Payout Schedule", "basic_rate"=>"Basic Rate");

        $this->db->select("emp.payroll_type, emp.basic_rate, ps.name as payout_sched");
        $this->db->join("payroll.payout_schedule as ps", "ps.id = emp.payout_sched", "LEFT");
        $qTemp = $this->db->get_where("gccmaster.tblemployees as emp", array("emp.id"=>$post['id']));
        if($qTemp->num_rows() === 1){
            $row = $qTemp->row();
            if(isset($post['payroll_type_desc']) && $post['payroll_type_desc'] || 
                (($row->payroll_type && $post['payroll_type_desc']) && $row->payroll_type !== $post['payroll_type_desc'])){
                $editedContent["payroll_type"] = $post['payroll_type_desc']; 
                $fromContent["payroll_type"] = $row->payroll_type;
            }
            if(isset($post['payout_sched_desc']) && $post['payout_sched_desc'] || 
                (($row->payout_sched && $post['payout_sched_desc']) && $row->payout_sched !== strtolower($post['payout_sched_desc']))){ 
                $editedContent["payout_sched"] = $post['payout_sched_desc']; 
                $fromContent["payout_sched"] = $row->payout_sched;
            }

            $currentBasicRate = $row->basic_rate ? floatval($row->basic_rate): 0;
            if($currentBasicRate !== $postBasicRate){
                $editedContent["basic_rate"] = number_format($post['basic_rate'], 2, ".", ","); 
                $fromContent["basic_rate"] = number_format($row->basic_rate, 2, ".", ",");
            }
        }
        $this->db->reset_query();
        /*** edited contents logging ***/

        /*** for approval ***/
        $forApproval = array();
        foreach ($editedContent as $key => $value) {
            if($fromContent[$key]){
                $isNumeric = false;
                if(is_numeric($value)){
                    $isNumeric = true;
                    $value = floatval($value);
                    $fromContent[$key] = floatval($fromContent[$key]);
                }

                if($isNumeric && $value > 0 && $fromContent[$key] > 0 && $value !== $fromContent[$key]){
                    $forApproval[$key] = $value;
                }else if($isNumeric === false && $value !== $fromContent[$key]){
                    $forApproval[$key] = $value;
                }
            }
        }

        $tempData = array();
        foreach ($tempKeys as $key => $value) {
            if((!isset($forApproval[$key]) || $hasApprovingAuthority) && isset($editedContent[$key])){ $tempData[$key] = $post[$key]; }
        }

        /*** for approval ***/
        /*** $data = array(
         'payroll_type' => $post['payroll_type'],
         'basic_rate' => $post['basic_rate'],
         'payout_sched' => $post['payout_sched'],
        ); ***/
        
        $dbId = $post['id'];
        $updated = false;
        $forApprovalData = is_array($forApproval) && count($forApproval) > 0;
        $tempEmployeeName = $tempEmployeeData->display_name_1 ? $tempEmployeeData->display_name_1: "No employee name";
        
        $cancelApprovalWhere = array("unique_id"=>$dbId, "table_id"=>$dbId, "database_table"=>"gccmaster.tblemployees", "module"=>"hris", "is_approved"=>0);

        if(is_array($tempData) && count($tempData) > 0){
            $this->db->where('id', $post['id']);
            $tempUpdate = $this->db->update('tblemployees', $tempData);
            $updated = $tempUpdate && $this->db->affected_rows() > 0;
        }
        
        $coreHistoryLog = $this->core_layout->coreHistoryLogs();
        $coreHistoryLog->setHistoryLogModule("hris");
        $coreHistoryLog->setHistoryLogTableName("gccmaster.tblemployees");
        $coreHistoryLog->setHistoryLogTableFieldId($dbId);
        $coreHistoryLog->setHistoryLogEmployeeId($dbId);

        if($updated && $this->db->trans_status() === TRUE){
            $resultarray["status"] = TRUE;
            $resultarray["response"] = "Data successfully updated!";
            if(is_array($tempData) && count($tempData) > 0){
                foreach ($tempData as $key => $value) {
                    if(isset($tempKeys[$key]) && $tempKeys[$key]){
                        $approvalDate = date("Y-m-d H:i:s");
                        $cancelApprovalWhere["table_field"] = $key;
                        $this->db->update("gccmaster.field_value_approval", 
                        array("is_approved"=>3, "approval_by"=>$this->core_layout->getCurrentEmployeeId(), "approval_at"=>$approvalDate),
                        $cancelApprovalWhere);
                        $this->db->reset_query();
                        
                        $nKey = $tempKeys[$key];
                        if(isset($tempData[$key]) && $tempData[$key]){
                            $fromValue = isset($fromContent[$key]) && $fromContent[$key] ? $fromContent[$key]: null;
                            $toValue = isset($editedContent[$key]) && $editedContent[$key] ? $editedContent[$key]: null;
                            
                            $logMessage = $fromValue ? 
                                "Employee named `$tempEmployeeName` with payroll data field `$nKey` has been updated from `$fromValue` to `$toValue`.": 
                                "Employee named `$tempEmployeeName` with payroll data field `$nKey` has been updated into `$toValue`.";
                            $coreHistoryLog->setEventLog($logMessage, $fromValue ? "update": "insert", "success", "gcchris", "user");
                            $coreHistoryLog->saveLoggedEventHistory();
                        }
                    }
                }
            }
        }else{
            $resultarray["status"] = FALSE;
            if($forApprovalData){
                $resultarray["response"] = "Updating of payroll data is currently for approval status!";
            }else{
                $resultarray["response"] = "Error processing request!";
                $coreHistoryLog->setEventLog("Failed to update payroll data of employee named `$tempEmployeeName`.","update", "error", "gcchris", "user");
                $coreHistoryLog->saveLoggedEventHistory();
            }
        }

        $resultarray["for_approval"] = false;
        $resultarray["approval_email_notification"] = null;
        if(is_array($forApproval) && count($forApproval) > 0 && $hasApprovingAuthority === false){
            $forApprovalFields = array();
            $forApprovalIds = array();
            foreach ($forApproval as $key => $value) {
                $originalValue = isset($post[$key]) && $post[$key] ? $post[$key]: null;
                $tempDescription = isset($tempKeys[$key]) && $tempKeys[$key] ? $tempKeys[$key]: "No description";
                $_tempData = array("unique_id"=>$dbId,"module"=>"hris", "field_description"=>$tempDescription, 
                "database_table"=>"gccmaster.tblemployees", "table_id"=>$dbId, 
                "table_field"=>$key, "table_value"=>$value, "original_value"=>$originalValue,
                "created_by"=>$this->core_layout->getCurrentEmployeeId(), "created_at"=>date("Y-m-d H:i:s"));
                
                $added = $this->db->insert("gccmaster.field_value_approval", $_tempData);
                if($added){
                    $forApprovalIds[] = $this->db->insert_id();
                    $forApprovalFields[] = $tempDescription;
                    $fromValue = $fromContent[$key];
                    $toValue = $value;

                    $logMessage = $fromValue ? 
                            "Employee named `$tempEmployeeName` with payroll data field `$tempDescription` and value of from `$fromValue` to `$toValue` is for approval status.": 
                            "Employee named `$tempEmployeeName` with payroll data field `$tempDescription` and value of `$toValue` is for approval status.";
                        $coreHistoryLog->setEventLog($logMessage, $fromValue ? "update": "insert", "success", "gcchris", "user");
                        $coreHistoryLog->saveLoggedEventHistory();
                }
            }

            $emailSent = false;
            if(is_array($forApprovalIds) && count($forApprovalIds) > 0){
                $this->db->select("field_description, table_value");
                $this->db->from("gccmaster.field_value_approval");
                $this->db->where("is_approved", 0);
                $this->db->where_in("id", $forApprovalIds);
                $queryApprovals = $this->db->get();
                if($queryApprovals->num_rows() > 0){
                    $createdEmployeeData = (object) $this->core_layout->getEmployeeData($this->core_layout->getCurrentEmployeeId());
                    $createdEmployeeName = $createdEmployeeData->display_name_1 ? $createdEmployeeData->display_name_1: "No employee name";

                    $emailData = array("date"=>$transactionDate, "list_header"=>"Payroll Information",
                        "employee_name"=>$tempEmployeeName, "created_by"=>$createdEmployeeName, 
                        "raw_data"=>$queryApprovals->result(), "count"=>$queryApprovals->num_rows());

                    $htmlContent = $this->load->view("hris/email_templates/email-for_approval", $emailData, true);
                    $overrideMailer = array();
                    $module = "hris_payroll_approval";
                    $email_title = "HRIS - Payroll Approval";
                    $content_title = "Payroll Information Approval For ".$tempEmployeeName;
                    try{
                        $sent = $this->core_layout->send_email($module, $email_title, $content_title, $htmlContent, $overrideMailer);
                        if($sent){ $emailSent = true; }
                    }catch(Exception $e){
                        $resultarray["approval_email_notification"] = $e->getMessage();
                    }
                }
            }

            $explodedApproval = implode(", ", $forApprovalFields);
            $type = count($forApprovalFields) > 1 ? "are": "is";
            $resultarray["for_approval"] = true;
            $resultarray["approval_notification"] = "The following field(s) `{$explodedApproval}` {$type} for approval status.";
        }

        return $resultarray;
    }

    /* START NEW PAYROLL SHEET FUNCTION 11/13/2020*/
    private function getSettings()
    {
        $settings_array = $this->db->get("payroll.settings")->result();
        $settings = array_reduce($settings_array,
            function ($carry, $obj) {
                $key = $obj->setting_name;
                $carry[$key] = $obj;
                return $carry;
            }, []);

        return (object) $settings;
    }

    function getRemittanceParameters($as_object = 1)
    {
        $parameters_array = $this->db->get("payroll.remittance_parameters")->result();

        if (intval($as_object) === 1) {
            $parameters = array_reduce($parameters_array,
                function ($carry, $obj) {
                    $key = strtolower($obj->remittance_code);
                    $carry[$key] = $obj;
                    return $carry;
                }, []);

            return (object)$parameters;
        }

        return $parameters_array;
    }

    public function generatePayrollSheet($start, $end, $posted_data){
        $this->storeGeneratedPsHistory($start, $end, $posted_data);
        $employee_ids = isset($posted_data["employees"]) ? $posted_data["employees"] : null;
        $payout_sched = $posted_data["payout_schedule"];
        $company = $this->db->where("id", $posted_data["company"])->get("gcchris.tblcompanies")->row();
        $employees = $this->getEmployees($payout_sched, 'active', $employee_ids, $company);
        
        $arrMonthlyEmployeeIds = array();
        if(is_array($employee_ids) && count($employee_ids) > 0){
            $this->db->select("emp_id");
            $this->db->from($this->tbl_timesheet_monthly_employees);
            $this->db->where_in("emp_id", $employee_ids);
            $qMonthlyEmployees = $this->db->get();
            if($qMonthlyEmployees->num_rows() > 0){
                foreach ($qMonthlyEmployees->result() as $_employee) {
                    $arrMonthlyEmployeeIds[] = $_employee->emp_id;
                }
            }
            $this->db->reset_query();
        }

        $filterHistory = (object) $this->core_layout->getCurrentSession();
        $employees["filtered_history"] = isset($filterHistory->ps_history) ? $filterHistory->ps_history : array();
        
        $month_name = strtolower(date('F', strtotime($posted_data["pay_date"])));
        
        $year = date('Y', strtotime($start));
        $tempYear0 = date('Y', strtotime($start));
        $tempYear1 = date('Y', strtotime($end));

        $tempMax = max(array($tempYear0, $tempYear1));
        $year = $tempMax ? $tempMax: $year;

        $payout_sequence = $posted_data["payout_sequence"];

            $payrollMonthlyWeekCount = $this->generatePayrollMonthlyWeekCount($year);

            $remittance_parameters = $this->getRemittanceParameters();
            $settings = $this->getSettings(); // payroll.settings table

            $payout_sched_remittance_sched = unserialize($this->db
                ->where("id", $payout_sched)
                ->get("payroll.payout_schedule")
                ->row("remittance_sched"));
            $min_remittance_sched = min($payout_sched_remittance_sched);
            $max_remittance_sched = max($payout_sched_remittance_sched);

            $should_deduct_remittances = in_array($payout_sequence, $payout_sched_remittance_sched);
            $is_last_remittance_schedule = intval($payout_sequence) === intval($max_remittance_sched);
            $remittance_sched_ctr = count($payout_sched_remittance_sched);
            
            $minutes_per_day = $settings->minutes_in_a_day->setting_value;
            $sss_contribution_basis = $settings->sss_contribution_basis->setting_value;

            $default_work_days_in_a_year = $company->work_days_in_year;
            $working_days_in_a_month = floor($default_work_days_in_a_year / 12);

            // for CA automated loan activation
            if(!empty($posted_data['emp_with_loans'])){
                $for_with_loans = array();
                foreach(explode('&', $posted_data['emp_with_loans']) as $values){
                    $with_loans = explode('=', $values);
                    $data[] = $with_loans[1];
                    $for_with_loans = $data;
                }
                $cash_advance_loan = $this->setCAStatus($for_with_loans, $posted_data['pay_date'], $posted_data['date_range']);
                if($cash_advance_loan['state']){
                    $employees['caStatus'] = $cash_advance_loan['state'];
                    $employees['caStatus1'] = $cash_advance_loan['response'];
                }
            }

            foreach ($employees["data"] as $employee) {
                $isMonthlyPaidEmployee = in_array($employee->id, $arrMonthlyEmployeeIds);
                $employee->is_monthly_paid = $isMonthlyPaidEmployee;
                /*** $atemp = new stdClass(); ***/
                $otTemp = [];
                $tempDateStarted = $employee->date_start ? date("Y-m-d", strtotime($employee->date_start)): null;

                $contAcctNumber = $this->getContributionDeductionAccountNumber($employee->id);
                $noContAcctNumber = array();
                foreach ($contAcctNumber as $key => $value) { if($value == false){ $noContAcctNumber[] = $key; } }
                $employee->cont_acctno = $contAcctNumber;
                $employee->no_cont_acctno = $noContAcctNumber;
                
                $employee->remittance_sched_ctr = $remittance_sched_ctr;
                $daily = 0;
                $per_minute = 0;
                $days_in_range = array();
                $ewd = 0; // expected working days
                
                $monthlyRate = 0;
                $isMonthlyPaid = false;
                $excludePaidHolidayMinutes = 0;

                $employee->monthly_week_count = $payrollMonthlyWeekCount;
                /*** if ($employee->payroll_type === 'daily') { ***/
                if ($employee->payroll_type !== 'monthly') {
                    $daily = $employee->basic_rate;
                    $daily = round(floatval($daily), 2);
                    $employee->rate = $daily;
                } elseif ($employee->payroll_type === 'monthly') {
                    /**** rate * 12 months / 314 working days in a year ****/
                    /*** $daily = $employee->basic_rate / $working_days_in_a_month; ***/
                    
                    $daily = ($employee->basic_rate * 12) / $default_work_days_in_a_year;
                    $daily = round(floatval($daily), 2);
                    $employee->rate = $employee->basic_rate;
                    $monthlyRate = isset($employee->basic_rate) && floatval($employee->basic_rate) ? floatval($employee->basic_rate) : $monthlyRate;
                    if(isset($employee->payout_sched_name) && $employee->payout_sched_name == "semi-monthly" && $monthlyRate > 0){ $monthlyRate = $monthlyRate / 2; }
                    $isMonthlyPaid = true;
                }
                $employee->default_working_days = $default_work_days_in_a_year;
                $employee->monthly_paid = $isMonthlyPaid;
                $per_minute = $daily / $minutes_per_day;
                if($employee->payroll_type == "hourly"){
                    $per_minute = $daily / 60;
                }

                /*** $atemp->daily = $daily;
                $atemp->minutes_per_day = $minutes_per_day;
                $atemp->per_minute = $per_minute;
                $atemp->per_hour = $per_minute * 60; ***/

                $shift_resource_id = unserialize($employee->shift_resource);
                $schedules = $this->db
                    ->where_in("id", $shift_resource_id)
                    ->get($this->tbl_shift_schedule_list)
                    ->result();

                $schedules_obj = array_reduce($schedules,
                    function ($carry, $obj) {
                        $key = $obj->weekday;
                        $carry[$key] = $obj;
                        return $carry;
                    }, []);
                $employee->schedules_obj = $schedules_obj;

                $interval = DateInterval::createFromDateString('1 day');
                $dateStart = new DateTime($start);
                $dateEnd = new DateTime($end);
                $dateEnd->modify("+1 day");

                $period = new DatePeriod($dateStart, $interval, $dateEnd);
                $tempDates = array();
                $tempAlteredDates = new stdClass();

                $arrTempSchedule = array();
                $monthCounter = array();
                $tempShiftRecords = array();
                $customShiftTaggedRestDay = array();
                foreach ($period as $dt) {
                    $day = strtolower($dt->format("l"));
                    $tempDate = $dt->format("Y-m-d");

                    $tempMonthNum = intval($dt->format("m"));
                    if(isset($monthCounter[$tempMonthNum]) && intval($monthCounter[$tempMonthNum]) > 0){ $monthCounter[$tempMonthNum]++; }
                    else{ $monthCounter[$tempMonthNum] = 1; }

                    $_tempSchedules = array();
                    $schedule = new stdClass();
                    
                    /*** use condition to determine ewd(estimated working days) in a cut off based on schedule ***/
                    if (isset($schedules_obj[$day])) {
                        $_hasShiftSchedule = true;
                        $shiftSchedule = $this->ts_model->getCustomizedShiftScheduleByDate($tempDate, $employee->id);
                        $schedule = $schedules_obj[$day];
                        $_tempSchedules["current_date"] = array("date"=>$tempDate, "schedule"=>$schedule, "weekday"=>$day);
                        $propShift = array("am_start", "am_end", "pm_start", "pm_end");
                        $schedule->altered_shift_schedule = new stdClass();
                        $schedule->props = $propShift;
                        $md5Date = md5($tempDate);
                        $isAlteredShift = false;

                        if(isset($shiftSchedule->has_shift) && $shiftSchedule->has_shift == 1){
                            $tempSchedule = $shiftSchedule->schedule;
                            $altered_shift_schedule = new stdClass();
                            foreach ($propShift as $key => $value) {
                                $altered_shift_schedule->$value = $schedule->$value;
                                if(isset($tempSchedule->$value) && $tempSchedule->$value && ($tempSchedule->$value !== $schedule->$value)){
                                    $altered_shift_schedule->$value = $tempSchedule->$value;
                                    $isAlteredShift = true;
                                }
                            }
                            $tempAlteredDates->$md5Date = new stdClass();
                            $tempAlteredDates->$md5Date->date = $tempDate;
                            $tempAlteredDates->$md5Date->weekday = $day;
                            $tempAlteredDates->$md5Date->shift_schedule = $altered_shift_schedule;
                            $tempAlteredDates->$md5Date->altered_shift = $isAlteredShift;
                            /*** $schedule->altered_shift_schedule->$md5Date = $altered_shift_schedule; ***/
                        }
                        if(isset($shiftSchedule->has_shift) && intval($shiftSchedule->has_shift) == 0){
                            $customShiftTaggedRestDay[$md5Date]["is_rest_day"] = true;
                            $_hasShiftSchedule = false;
                        }

                        if($_hasShiftSchedule){
                            if (($schedule->am_start !== null && $schedule->am_end !== null)
                                || ($schedule->pm_start !== null && $schedule->pm_end !== null)) {
                                $ewd += 1;
                                if($tempDateStarted && $tempDate >= $tempDateStarted && !in_array($tempDate, $tempDates)){
                                    $tempDates[] = $tempDate;
                                }
                                if($tempDateStarted && $tempDate < $tempDateStarted && $isMonthlyPaid && !in_array($tempDate, $tempDates)){
                                    $tempDates[] = $tempDate;
                                }
                            }
                        }
                        $schedules_obj[$day] = $schedule;
                        $_tempSchedules["updated_date"] = array("date"=>$tempDate, "schedule"=>$schedule, "weekday"=>$day);
                        if($_hasShiftSchedule){
                            $tempShiftRecords[] = $tempDate; 
                        }
                    }
                    $arrTempSchedule[] = $_tempSchedules;
                }

                /*** if weekly paid employees ***/
                if(intval($payout_sched) === 3){
                    /*** month updater start ***/
                    $maxs = array_keys($monthCounter, max($monthCounter));

                    if(isset($maxs[0]) && $maxs[0]){ 
                        $tempMonthName  = DateTime::createFromFormat('!m', $maxs[0]);
                        $month_name = strtolower($tempMonthName->format('F')); 
                        /*** $month_name = strtolower(date("F", mktime(null, null, null, $maxs[0])));  ***/
                    }
                    /*** month updater end ***/
                }
                /*** if weekly paid employees ***/
                
                $employee->temp_schedule = $arrTempSchedule;

                /** Weekly Month Week Count **/
                $employee->week_counter = 0;
                $getTempNumWeeks = $this->db->get_where($this->tbl_ps_week_counter, array("year"=>$year, "month_name"=>$month_name));
                if($getTempNumWeeks->num_rows() == 1){
                    $_weeks = $getTempNumWeeks->row()->week_count;
                    if($_weeks > 0){ $employee->week_counter = intval($_weeks); }
                }
                
                /** Weekly Month Week Count **/

                $currentPayrollContributions = $this->getCurrentContributionDeduction($employee->id, $month_name, $year);

                $payroll_sheet_row = $this->db
                    ->where(array(
                        "month_name" => $month_name,
                        "year" => $year,
                        "payroll_sched" => $employee->payout_sched,
                        "payroll_seq" => $payout_sequence,
                        "emp_id" => $employee->id,
                        "is_bonus" => 0,
                    ))
                    ->get("payroll.payroll_sheet")
                    ->row();

                if(isset($employee->payroll_type, $employee->payout_sched_name, $employee->basic_rate) && $employee->payout_sched_name == "weekly" 
                    && $employee->payroll_type === 'monthly' && floatval($employee->basic_rate) > 0 && $isMonthlyPaid){
                    $getNumWeeks = $this->db->get_where($this->tbl_ps_week_counter, array("year"=>$year, "month_name"=>$month_name));
                    if($getNumWeeks->num_rows() == 1){
                        $weeks = $getNumWeeks->row()->week_count;
                        if($weeks > 0){
                            $monthlyRate = floatval($employee->basic_rate) / intval($weeks);
                        }
                    }
                }

                /** night diff switch here **/
                $allowNdiff = $this->db->get_where($this->tbl_ps_employee_regular_ndiff, array("employee_id"=>$employee->id, "allow_ndiff"=>1));
                $allowRegularNightDiff = $allowNdiff->num_rows() === 1;
                /** night diff switch here **/

                /* CALCULATION */
                $basic_rate = 0;
                $basic_rate_total = 0;
                $employee->daily = $daily;
                $tempMonthlyBasic = $daily * $working_days_in_a_month;
                /*** atered monthly rate ***/
                if($isMonthlyPaid){ $tempMonthlyBasic = $employee->basic_rate; }
                /*** atered monthly rate ***/
                $employee->monthly_basic = number_format($tempMonthlyBasic, '2', '.', '');
                $employee->per_minute = $per_minute;
                $employee->payroll_sheet_row = $payroll_sheet_row;

                $total_minutes = 0;
                $total_holiday_minutes = 0;
                $total_late_minutes = 0;
                $total_late_amount = 0;
                $total_ut_minutes = 0;
                $total_ut_amount = 0;
                
                $employee->deduct_allowance_days = 0;
                $temp_unrendered_minutes = 0;
                $unrendered_minutes = 0;
                $undertime_minutes = 0;
                $undertime = 0;

                $ot_minutes = 0;
                $ot_amount = 0;
                $ot_ndiff_minutes = 0;
                $ot_ndiff_amount = 0;

                $total_ndiff_minutes = 0;
                $total_ndiff_amount = 0;
                $total_ot_allowance_minutes = 0;

                $holiday_minutes = 0;
                $holiday = 0;

                $late_minutes = 0;
                $undertime_only_minutes = 0;
                $rendered_days_worked = 0;
                $half_day_absent = 0;
                $wholeDayAbsent = 0;
                $unpaidHoliday = 0;
                $unpaid_holiday_minutes = 0;
                $unpaid_holiday_amount = 0;
                $monthly_paid_holiday_amount = 0;

                if($isMonthlyPaidEmployee === false){
                    $timesheet = $this->db
                        ->where("ts.date >=", $start)
                        ->where("ts.date <=", $end)
                        ->where("ts.emp_id", $employee->id)
                        ->where("ts.verified", 1)
                        ->get($this->tbl_timesheet . " ts")
                        ->result();
                    $employee->timesheet = $timesheet;
                    $employee->rest_day = 0;
    
                    $tempExistingDates = array();
                    $tempIsPaidHoliday = array();
                    foreach ($timesheet as $index => $ts) {
                        $tempTs = new stdClass();
                        $tempOT = new stdClass();

                        $ts->minutes_daily = 0;
                        $ts->is_rest_day = 0;
                        $ts->total_late_amount = 0;
                        $ts->total_late_amount = 0;
                        $ts->total_late_minutes = 0;
                        $ts->total_ut_amount = 0;
                        $ts->total_ut_minutes = 0;
                        $ts->total_accredited_ot_hrs_amount = 0;
                        $ts->total_accredited_ndiff_ot_hrs_amount = 0;

                        $ts->total_allowance_ot_hrs_minutes = 0;

                        $ts->regular_ndiff_minutes = 0;
                        $ts->regular_ndiff_amount = 0;
    
                        $ts->holiday_minutely = 0;
                        $ts->holiday_amount = 0;
                        $ts->holiday_gross_minutely = 0;
                        $ts->holiday_gross_amount = 0;
    
                        $ts->schedule = null;
                        if(in_array($ts->date, $tempDates) && !in_array($ts->date, $tempExistingDates)){
                            $tempExistingDates[] = $ts->date;
                        }
    
                        if (isset($schedules_obj[$ts->weekday])) {
                            $_schedule = $schedules_obj[$ts->weekday];
                            $has_altered_shift = false;
                            $_temp_current_schedule = new stdClass();
                            $md5Date = md5($ts->date);
                            if(isset($tempAlteredDates->$md5Date) && $tempAlteredDates->$md5Date){
                                $propShift = $_schedule->props;
                                if($tempAlteredDates->$md5Date->altered_shift === true){
                                    $alteredShift = $tempAlteredDates->$md5Date->shift_schedule;
                                    foreach ($propShift as $vvx) {
                                        $_temp_current_schedule->$vvx = $_schedule->$vvx;
                                        if(isset($alteredShift->$vvx) && $alteredShift->$vvx){
                                            $_temp_current_schedule->$vvx = $alteredShift->$vvx;
                                        }
                                    }
                                    $has_altered_shift = true;
                                }
                            }
    
                            // check if rest day or no shift then mark as rest day
                            $payrate_setting = $this->getPayrateSetting("regular");
                            $_temp_ploted_schedule = $_schedule;
                            if($has_altered_shift){
                                if ($_temp_current_schedule->am_start === null &&
                                    $_temp_current_schedule->am_end === null &&
                                    $_temp_current_schedule->pm_start === null &&
                                    $_temp_current_schedule->pm_end === null) {
                                    $payrate_setting = $this->getPayrateSetting("rest day");
                                        $ts->is_rest_day = 1;
                                }
                                $_temp_ploted_schedule = $_temp_current_schedule;
                            }else{
                                if ($_schedule->am_start === null &&
                                $_schedule->am_end === null &&
                                $_schedule->pm_start === null &&
                                $_schedule->pm_end === null) {
                                $payrate_setting = $this->getPayrateSetting("rest day");
                                    $ts->is_rest_day = 1;
                                }
                            }

                            if(isset($customShiftTaggedRestDay[$md5Date]) && $customShiftTaggedRestDay[$md5Date]["is_rest_day"] === true){
                                $payrate_setting = $this->getPayrateSetting("rest day");
                                $ts->is_rest_day = 1;
                            }
                            
                            $ts->schedule = $_temp_ploted_schedule;
    
                            $minutesDaily = $this->getTotalMunitesDaily($ts);
                            $ts->minutes_daily = $minutesDaily;
    
                            $payrateRegular = isset($payrate_setting->regular_rate) && $payrate_setting->regular_rate ? $payrate_setting->regular_rate : 1;
    
                            $ts->per_minute = $per_minute;
                            $ts->payrate_regular = $payrateRegular;
    
                            $minutely = $per_minute * $payrateRegular;
                            /*** $tempRatex = doubleval($ts->total_time_rendered) > 0 ? ($minutely * $minutes_per_day) : 0; ***/
                            $tempRatex = doubleval($ts->total_time_rendered) > 0 ? ($minutely * $ts->total_time_rendered) : 0;
                            $tempRatexx = doubleval($ts->total_time_rendered) > 0 ? ($minutely * $minutes_per_day) : 0;
    
                            $tempTs->actual_minutes = $ts->total_time_rendered;
                            $tempTs->actual_hours = $ts->total_time_rendered / 60;
                            $tempTs->actual_hours_decimal = number_format($tempTs->actual_hours, 2);

                            $basic_rate += $tempRatex;
                            $basic_rate_total += $tempRatexx;
    
                            $ts->daily_rate = $tempRatex;
                            $ts->minutely = $minutely;
                            $ts->minutes_per_day = $minutes_per_day;
                            $ts->minutely_amount = floatval($ts->total_time_rendered) > 0 ? $minutely * 60 : 0;
    
                            $ts->total_late_amount = floatval($ts->total_time_rendered) > 0 ? $ts->total_late * $minutely : 0;
                            $ts->total_late_minutes = floatval($ts->total_time_rendered) > 0 ? $ts->total_late : 0;
    
                            $ts->total_ut_amount = floatval($ts->total_time_rendered) > 0 ? $ts->total_ut * $minutely : 0;
                            $ts->total_ut_minutes = floatval($ts->total_time_rendered) > 0 ? $ts->total_ut : 0;
    
    
                            // START OVERTIME CALCULATION HERE
                            $tempPayrateSettings = intval($ts->payrate_id) > 0 ? $this->getPayrateSettingById($ts->payrate_id) : $payrate_setting;
                            $tempOvertime = $this->getOvertimeAmountDaily($ts, $tempPayrateSettings);
                            if($tempOvertime && count(get_object_vars($tempOvertime)) > 0){
                                $ts = (object)array_merge((array)$ts, (array)$tempOvertime);
                            }
                            // END OVERTIME CALCULATION HERE
                            // START REGULAR NIGHTDIFF CALCULATION
                            if($allowRegularNightDiff){
                                $tempRegularNdiff = $this->getRegularNightDiffAmountDaily($ts, $tempPayrateSettings);
                                if($tempRegularNdiff && count(get_object_vars($tempRegularNdiff)) > 0){
                                    $ts = (object) array_merge((array) $ts, (array) $tempRegularNdiff);
                                }
                            }
                            // END REGULAR NIGHTDIFF CALCULATION

                            if(intval($ts->is_holiday) !== 0){
                                $includeHolidayBasic = false;
                                if(isset($ts->paid_holiday) && intval($ts->paid_holiday) === 1 && intval($ts->total_time_rendered) === 0){
                                    $ts->minutely_amount = $minutely * 60;
                                    $includeHolidayBasic = true;
                                }

                                $tempHolidayTimesheet = $this->getHolidayAmountDaily($ts);
                                if($tempHolidayTimesheet && count(get_object_vars($tempHolidayTimesheet)) > 0){
                                    $ts = (object)array_merge((array)$ts, (array)$tempHolidayTimesheet);
                                    $md5Date = md5($ts->date);
                                    $tempIsPaidHoliday[$md5Date] = intval($ts->paid_holiday) == 1;
    
                                    if(floatval($ts->total_time_rendered) > 0 && isset($ts->paid_holiday) && intval($ts->paid_holiday) == 1){
                                        $excludePaidHolidayMinutes += floatval($ts->total_time_rendered);
                                    }

                                    $tempTotalRendered = intval($ts->am_time_rendered) + intval($ts->pm_time_rendered);
                                    $hasRenderedShift = intval($tempTotalRendered) > 0 && (intval($ts->am_time_rendered) > 0 || intval($ts->pm_time_rendered) > 0) || $includeHolidayBasic;

                                    if($hasRenderedShift && $ts->holiday_amount > 0){
                                        if(floatval($ts->total_time_rendered) > 0 && $excludePaidHolidayMinutes >= floatval($ts->total_time_rendered)){
                                            $excludePaidHolidayMinutes -= floatval($ts->total_time_rendered);
                                        }
                                        $basic_rate += $ts->holiday_amount;
                                        $basic_rate_total += $ts->holiday_amount;
                                    }
                                }
                            }
                        }else{
                            $payrate_setting = $this->getPayrateSetting("rest day");
                            $ts->is_rest_day = 1;
                            
                            $payrateRestDay = isset($payrate_setting->regular_rate) && $payrate_setting->regular_rate ? $payrate_setting->regular_rate : 1;
    
                            $ts->per_minute = $per_minute;
                            $ts->payrate_regular = $payrateRestDay;
    
                            $minutely = $per_minute * $payrateRestDay;
                            $tempRatex = doubleval($ts->total_time_rendered) > 0 ? ($minutely * $ts->total_time_rendered) : 0;
                            $tempRatexx = doubleval($ts->total_time_rendered) > 0 ? ($minutely * $minutes_per_day) : 0;
    
                            $tempTs->actual_minutes = $ts->total_time_rendered;
                            $tempTs->actual_hours = $ts->total_time_rendered / 60;
                            $tempTs->actual_hours_decimal = number_format($tempTs->actual_hours, 2);

                            $basic_rate += $tempRatex;
                            $basic_rate_total += $tempRatexx;
    
                            $ts->minutely = $minutely;
                            $ts->minutes_per_day = $minutes_per_day;
                            $ts->minutely_amount = floatval($ts->total_time_rendered) > 0 ? $minutely * 60: 0;
    
                            // START OVERTIME CALCULATION HERE
                            $tempPayrateSettings = intval($ts->payrate_id) > 0 ? $this->getPayrateSettingById($ts->payrate_id) : $payrate_setting;
                            $tempOvertime = $this->getOvertimeAmountDaily($ts, $tempPayrateSettings);
                            if($tempOvertime && count(get_object_vars($tempOvertime)) > 0){
                                $ts = (object)array_merge((array)$ts, (array)$tempOvertime);
                            }
                            // END OVERTIME CALCULATION HERE
                            // START REGULAR NIGHTDIFF CALCULATION
                            if($allowRegularNightDiff){
                                $tempRegularNdiff = $this->getRegularNightDiffAmountDaily($ts, $tempPayrateSettings);
                                if($tempRegularNdiff && count(get_object_vars($tempRegularNdiff)) > 0){
                                    $ts = (object) array_merge((array) $ts, (array) $tempRegularNdiff);
                                }
                            }
                            // END REGULAR NIGHTDIFF CALCULATION
                            if(intval($ts->is_holiday) !== 0){
                                $includeHolidayBasic = false;
                                if(isset($ts->paid_holiday) && intval($ts->paid_holiday) === 1 && intval($ts->total_time_rendered) === 0){
                                    $ts->minutely_amount = $minutely * 60;
                                    $includeHolidayBasic = true;
                                }
                                $tempHolidayTimesheet = $this->getHolidayAmountDaily($ts);
                                if($tempHolidayTimesheet && count(get_object_vars($tempHolidayTimesheet)) > 0){
                                    $ts = (object)array_merge((array)$ts, (array)$tempHolidayTimesheet);
                                    $md5Date = md5($ts->date);
                                    $tempIsPaidHoliday[$md5Date] = intval($ts->paid_holiday) == 1;
    
                                    if(floatval($ts->total_time_rendered) > 0 && isset($ts->paid_holiday) && intval($ts->paid_holiday) == 1){
                                        $excludePaidHolidayMinutes += floatval($ts->total_time_rendered);
                                    }

                                    $tempTotalRendered = intval($ts->am_time_rendered) + intval($ts->pm_time_rendered);
                                    $hasRenderedShift = intval($tempTotalRendered) > 0 && (intval($ts->am_time_rendered) > 0 || intval($ts->pm_time_rendered) > 0) || $includeHolidayBasic;

                                    if($hasRenderedShift && $ts->holiday_amount > 0){
                                        if(floatval($ts->total_time_rendered) > 0 && $excludePaidHolidayMinutes >= floatval($ts->total_time_rendered)){
                                            $excludePaidHolidayMinutes -= floatval($ts->total_time_rendered);
                                        }
                                        $basic_rate += $ts->holiday_amount;
                                        $basic_rate_total += $ts->holiday_amount;
                                    }
                                }
                            }
                        }
    
                        $total_minutes += $ts->total_time_rendered;
        
                        $tempOT->id = $ts->id;
                        $tempOT->date = $ts->date;
                        $tempOT->weekday = $ts->weekday;
                        $tempOT->shift_record = array();
                        $tempOT->shift_record["custom_shift_id"] = $ts->custom_shift_id;
                        $tempOT->shift_record["has_shift"] = $ts->has_shift;
                        $tempOT->shift_record["is_rest_day"] = $ts->is_rest_day;
                        $tempOT->shift_record["is_holiday"] = $ts->is_holiday;
                        $tempOT->ot_minutely = $ts->ot_minutely;
                        $tempOT->ot_ndiff_minutely = $ts->ot_ndiff_minutely;
                        $tempOT->total_accredited_ot_hrs = $ts->total_accredited_ot_hrs;
                        $tempOT->total_accredited_ndiff_ot_hrs = $ts->total_accredited_ndiff_ot_hrs;
                        $tempOT->total_accredited_ot_hrs_amount = $ts->total_accredited_ot_hrs_amount;
                        $tempOT->total_accredited_ndiff_ot_hrs_amount = $ts->total_accredited_ndiff_ot_hrs_amount;
                        $otTemp[$ts->id] = $tempOT;
    
                        $timesheet[$index] = $ts;
                    }

                    $existingTsDates = array_reduce($timesheet, function ($carry, $item) {
                        return $carry ? $carry.",".$item->date : $item->date;
                    });
                    $timesheetRenderedDates = explode(",", $existingTsDates);
                    
                    $hasShiftDates = array();
                    if(is_array($tempShiftRecords) && !empty($tempShiftRecords)){
                        foreach ($tempShiftRecords as $shiftDate) {
                            if(is_array($timesheetRenderedDates) && !empty($timesheetRenderedDates) && !in_array($shiftDate, $timesheetRenderedDates)){
                                $hasShiftDates[] = $shiftDate;
                            }
                        }
                    }
                    
                    if(is_array($tempExistingDates) && !empty($tempExistingDates)){
                        foreach ($tempExistingDates as $value) {
                            $tempMd5Date = md5($value);
                            $isPaidHolidayDate = isset($tempIsPaidHoliday[$tempMd5Date]) ? $tempIsPaidHoliday[$tempMd5Date]: false;
    
                            $holidayResponse = (object) $this->ts_model->getCurrentDateIsHoliday($value);
                            if ($holidayResponse->is_holiday && $holidayResponse->classification === 'Regular Holiday'
                            && ($employee->payroll_type !== 'Monthly' && $isPaidHolidayDate)) {
                                $employee->deduct_allowance_days++;
                            }
    
                            $index = array_search($value, $tempDates);
                            if($index !== false){ unset($tempDates[$index]); }
                        }
                    }
                    
                    if(is_array($tempDates) && !empty($tempDates)){
                        foreach ($tempDates as $key => $value) {
                            $holidayResponse = (object) $this->ts_model->getCurrentDateIsHoliday($value);
                            if ($holidayResponse->is_holiday && strtolower($holidayResponse->classification) === 'special non-working holiday'
                            && (strtolower($employee->payroll_type) !== 'monthly')) {
                                unset($tempDates[$key]);
                            }
                        }
                    }
    
                    $tempDates = array_unique(array_merge($tempDates, $hasShiftDates));
                    $temp_unrendered_data = $this->getTotalUnrenderedMinutes($schedules_obj, $tempAlteredDates, $tempDates);
                    $temp_unrendered_data = (object) $temp_unrendered_data;

                    $temp_unrendered_minutes = $temp_unrendered_data->total_minutes;
                    $wholeDayAbsent = $temp_unrendered_data->absent_days;
                    $unpaidHoliday = $temp_unrendered_data->unpaid_holiday;
                    
                    if(is_array($unpaidHoliday) && !empty($unpaidHoliday)){
                        $unpaid_holiday_minutes = array_reduce($unpaidHoliday, function ($carry, $item) {
                            return $carry + $item["total_minutes"];
                        }, 0);
                    }
                    
                    $unpaid_holiday_amount = floatval($unpaid_holiday_minutes) > 0 ? floatval($unpaid_holiday_minutes) * $per_minute : 0;

                    $unrendered_minutes = array_reduce($timesheet, function ($carry, $item) {
                        $tempTotalTimeRendered = intval($item->total_time_rendered);
                        $tempMinutesDaily = (intval($item->paid_holiday) == 1)? $tempTotalTimeRendered : $item->minutes_daily;
                        $totalUndertime = $tempMinutesDaily - $tempTotalTimeRendered;
                        $totalUndertime = $totalUndertime > 0 ? $totalUndertime: 0;
                        return $carry + $totalUndertime;
                    }, 0);
    
                    $holiday_minutes = array_reduce($timesheet, function ($carry, $item) {
                        return $carry + $item->holiday_minutely;
                    }, 0);
    
                    $holiday = array_reduce($timesheet, function ($carry, $item) {
                        return $carry + $item->holiday_amount;
                    }, 0);
    
                    $late_minutes = array_reduce($timesheet, function ($carry, $item) {
                        return $carry + $item->total_late_minutes;
                    }, 0);
    
                    $undertime_only_minutes = array_reduce($timesheet, function ($carry, $item) {
                        $temp_undertime = (floatval($item->total_ut_minutes) > 0 && floatval($item->total_ut_minutes) < 240)? floatval($item->total_ut_minutes): 0;
                        return $carry + $temp_undertime;
                    }, 0);
    
                    $half_day_absent = array_reduce($timesheet, function ($carry, $item) {
                        $tempCtr = floatval($item->total_ut_minutes) >= 240 && floatval($item->total_ut_minutes) <= 300 ? 1: 0;
                        return $carry + $tempCtr;
                    }, 0);
                    
                    $whole_day_absent = array_reduce($timesheet, function ($carry, $item) {
                        $tempCtr = floatval($item->total_ut_minutes) >= 480 ? 1: 0;
                        return $carry + $tempCtr;
                    }, 0);
                    $wholeDayAbsent += $whole_day_absent;
    
                    $rendered_days_worked = array_reduce($timesheet, function ($carry, $item) {
                        $temp_worked_day = (floatval($item->total_time_rendered) > 0)? 1: 0;
                        return $carry + $temp_worked_day;
                    }, 0);
    
                    $undertime_minutes = array_reduce($timesheet, function ($carry, $item) {
                        return $carry + ($item->total_ut_minutes + $item->total_late_minutes);
                    }, 0);
    
                    $undertime = array_reduce($timesheet, function ($carry, $item) {
                        return $carry + ($item->total_ut_amount + $item->total_late_amount);
                    }, 0);
    
                    $ot_minutes = array_reduce($timesheet, function ($carry, $item) {
                            return $carry + $item->total_accredited_ot_hrs;
                        }, 0) * 60; // *60 to convert into minutes
    
                    $ot_amount = array_reduce($timesheet, function ($carry, $item) {
                        return $carry + $item->total_accredited_ot_hrs_amount;
                    }, 0);
    
                    $ot_ndiff_minutes = array_reduce($timesheet, function ($carry, $item) {
                            return $carry + $item->total_accredited_ndiff_ot_hrs;
                        }, 0) * 60; // *60 to convert into minutes
    
                    $ot_ndiff_amount = array_reduce($timesheet, function ($carry, $item) {
                        return $carry + $item->total_accredited_ndiff_ot_hrs_amount;
                    }, 0);

                    $total_ndiff_minutes = array_reduce($timesheet, function ($carry, $item) {
                        return $carry + $item->regular_ndiff_minutes;
                    }, 0);

                    $total_ndiff_amount = array_reduce($timesheet, function ($carry, $item) {
                        return $carry + $item->regular_ndiff_amount;
                    }, 0);

                    $total_ot_allowance_minutes = array_reduce($timesheet, function ($carry, $item) {
                        return $carry + $item->total_allowance_ot_hrs_minutes;
                    }, 0);

                    $monthly_paid_holiday_amount = array_reduce($timesheet, function ($carry, $item) {
                        $tempTotalRendered = intval($item->am_time_rendered) + intval($item->pm_time_rendered);
                        $hasRenderedShift = intval($tempTotalRendered) > 0 && (intval($item->am_time_rendered) > 0 || intval($item->pm_time_rendered) > 0);
                        $holidayPaid = $hasRenderedShift && $item->holiday_amount > 0 ? $item->holiday_amount: 0;
                        return $carry + $holidayPaid;
                    }, 0);
                    
                } // end of is monthly paid FALSE

                /*** hourly unrendred minutes ***/
                if($employee->payroll_type == "hourly"){
                    $temp_unrendered_minutes = 0;
                    $unrendered_minutes = 0;
                    $undertime_minutes = 0;
                    $undertime = 0;
                }
                /*** hourly unrendred minutes ***/

                $total_unrendered_minutes = $unrendered_minutes + $temp_unrendered_minutes;
                $total_unrendered_amount = $total_unrendered_minutes * $per_minute;

                $target_minutes_worked = $total_minutes + $total_unrendered_minutes;
                $target_hours_worked = $target_minutes_worked / 60;

                $employee->target_minutes_worked = $target_minutes_worked;
                $employee->target_hours_worked = $target_hours_worked;

                /*** $days_worked = ceil($total_minutes / $minutes_per_day); ***/
                $employee->paid_holiday_minutes = $excludePaidHolidayMinutes;
                $excude_holiday_days_worked = 0;

                if($total_minutes > 0 && $excludePaidHolidayMinutes > 0){
                    $total_holiday_minutes = $total_minutes - $excludePaidHolidayMinutes;
                    $excude_holiday_days_worked = $total_holiday_minutes / $minutes_per_day;
                    $temp_holiday_days_worked = floor($excude_holiday_days_worked * 100) / 100;
                    $isFloatHoliday = is_float($excude_holiday_days_worked);
                    $excude_holiday_days_worked = ($isFloatHoliday === true)? $temp_holiday_days_worked: $excude_holiday_days_worked;
                }

                if($isMonthlyPaidEmployee && $total_minutes === 0){
                    $total_minutes = $ewd * 480;
                    $target_minutes_worked = $total_minutes + $total_unrendered_minutes;
                    $employee->target_minutes_worked = $target_minutes_worked;
                    $target_hours_worked = $target_minutes_worked / 60;
                    $employee->target_hours_worked = $target_hours_worked;
                }

                $days_worked = $total_minutes / $minutes_per_day;
                $temp_days_worked = floor($days_worked * 100) / 100;

                $isFloat = is_float($days_worked);
                $days_worked = ($isFloat === true)? $temp_days_worked: $days_worked;
                
                $employee->days_worked = $days_worked;

                if($isMonthlyPaid && $monthlyRate > 0 && $days_worked > 0){
                    $basic_rate = $monthlyRate - $total_unrendered_amount;
                    $basic_rate_total = $monthlyRate - $total_unrendered_amount;

                    $basic_rate += $monthly_paid_holiday_amount;
                    $basic_rate_total += $monthly_paid_holiday_amount;
                }
                
                $employee->basic_rate = $basic_rate;
                $employee->basic_rate_total = $basic_rate_total;
                $employee->undertime = $undertime;
                $employee->undertime_minutes = $undertime_minutes;

                $employee->total_unrendered_minutes = $total_unrendered_minutes;
                $employee->total_unrendered_amount = $total_unrendered_amount;

                $employee->ewd = $ewd;
                $allowances = $this->getEmployeeAllowances($employee, $working_days_in_a_month, $minutes_per_day, $target_minutes_worked, $total_unrendered_minutes, 1);

                $allowance_per_minute = array_reduce($allowances, function ($carry, $item) {
                    return $carry + $item->allowance_per_minute;
                });
                
                $allowance_per_minute = $allowance_per_minute ?? 0;
                $total_ot_allowance_amount = $total_ot_allowance_minutes * $allowance_per_minute;

                /*** $employee_allowance = array_reduce($allowances, function ($carry, $item) {
                    return $carry + (intval($item->is_active) === 0 ? 0 : $item->allowance_net);
                }, 0);
                $temp_total_allowance = isset($payroll_sheet_row) && intval($payroll_sheet_row->posted) === 1 ? 
                    (isset($payroll_sheet_row) && !empty($payroll_sheet_row) ? $payroll_sheet_row->total_allowances : 0) : $employee_allowance; ***/

                $employee->allowances = $allowances;
                $employeeAllowanceTotal = isset($payroll_sheet_row) && $payroll_sheet_row->posted === 1
                    ? $payroll_sheet_row->total_allowances ?? 0
                    : array_reduce($allowances, function ($carry, $allowance) {
                        return $carry + (intval($allowance->is_active) === 0 ? 0 : $allowance->allowance_net);
                    }, 0);

                $employee->total_allowance = $employeeAllowanceTotal;

                $employee->total_ot_allowance_minutes = $total_ot_allowance_minutes;
                $employee->total_ot_allowance_amount = $total_ot_allowance_amount;

                $employeeRate = $employee->rate + 0;
                $employeeRate = is_float($employeeRate) ? floatval($employeeRate): intval($employeeRate);

                $tempBasicRate = floatval($basic_rate);
                $tempAllowance = floatval($employeeAllowanceTotal);
                $tempOtndiff = floatval($ot_amount) + floatval($ot_ndiff_amount);
                $totalNightDifferential = floatval($total_ndiff_amount);
                $totalOtAllowance = floatval($total_ot_allowance_amount);

                $earnings = $tempBasicRate + $tempAllowance + $tempOtndiff + $totalNightDifferential + $totalOtAllowance;

                $employee->earnings = $earnings;
                $gross_pay = $earnings;
                $_gross_pay = $gross_pay;
                
                /** do not remove for _{$sss_contribution_basis}  parameter **/
                $_basic_rate = $tempBasicRate;
                /** do not remove for _{$sss_contribution_basis}  parameter **/

                $tempParameter = "_{$sss_contribution_basis}";
                $sssContributionBasis = ${$tempParameter};
                $sssContributionBasis = $sssContributionBasis ? $sssContributionBasis: $gross_pay;
                $employee->temp_sss_contribution_basis = $sssContributionBasis;

                $temp_taxable_income = 0;
                if(intval($remittance_parameters->tax->status) == 1){
                    $qTaxSwitch = $this->db->get_where("payroll.settings", array("setting_name"=>"fixed_tax_monthly_income_switch", "setting_value"=>"1"));
                    $qSettings = $this->db->get_where("payroll.settings", array("setting_name"=>"fixed_tax_monthly_income"));
                    if($qSettings->num_rows() == 1 && $qTaxSwitch->num_rows() == 1){
                        $tempValue = $qSettings->row()->setting_value;
                        if($tempValue && floatval($tempValue) > 0 && floatval($employeeRate) > floatval($tempValue)){
                            $temp_taxable_income = $tempValue;
                        }
                    }
                }
                $employee->temp_taxable_income = $temp_taxable_income;
                $alteredTaxableIncome = false;
                if($temp_taxable_income > 0){
                    $parameters = array(
                        "switch"=>$remittance_parameters->tax->status,
                        "gross_pay"=> $gross_pay,
                        "remittance_sched_ctr"=> $remittance_sched_ctr,
                        "is_last_remittance_schedule"=> $is_last_remittance_schedule,
                        "month_name"=> $month_name,
                        "year"=> $year,
                        "sss_class"=> $company->sss_class,
                        "payout_sched"=> $employee->payout_sched,
                        "payroll_type"=> $employee->payroll_type,
                        "emp_id"=> $employee->id,
                        "contribution_acct_no"=>$contAcctNumber,
                    );

                    $tempTax = $this->generateIncomeTaxCalculator($temp_taxable_income, $parameters);

                    $employee->tax = $tempTax;
                    $alteredTaxableIncome = true;
                }

                $temp_has_sss_no = (isset($contAcctNumber->sss_no) && $contAcctNumber->sss_no)? true: false;
                $temp_has_phealth_no = (isset($contAcctNumber->phealth_no) && $contAcctNumber->phealth_no)? true: false;
                $temp_has_pagibig_no = (isset($contAcctNumber->pagibig_no) && $contAcctNumber->pagibig_no)? true: false;
                /**$temp_has_tin_no = (isset($contAcctNumber->tin_no) && $contAcctNumber->tin_no)? true: false; **/
                
                /* GET REMITTANCES OR MANDATORY GOVERNMENT BENEFITS/DEDUCTIONS */
                $employee->hdmf = $this->getHdmf($remittance_sched_ctr, $remittance_parameters->hdmf->status, $gross_pay, $temp_has_pagibig_no);
                $employee->phic = $this->calculatePhilHealth($remittance_sched_ctr, $employee->monthly_basic, $remittance_parameters->phic->status, $gross_pay, $temp_has_phealth_no);
                $employee->sss = $this->calculateSss(array(
                    "gross_pay" => $sssContributionBasis,
                    "switch" => $remittance_parameters->sss->status,
                    "remittance_parameters" => $remittance_parameters,
                    "min_remittance_sched" => $min_remittance_sched,
                    "max_remittance_sched" => $max_remittance_sched,
                    "should_deduct_remittances" => $should_deduct_remittances,
                    "is_last_remittance_schedule" => $is_last_remittance_schedule,
                    "month_name" => $month_name,
                    "year" => $year,
                    "payout_sched" => $employee->payout_sched,
                    "emp_id" => $employee->id,
                    "has_sss_no" => $temp_has_sss_no,
                    "contribution_basis" => $sss_contribution_basis,
                ), $company->sss_class);

                if($alteredTaxableIncome === false){
                    $parameters = array(
                        "switch"=>$remittance_parameters->tax->status,
                        "gross_pay"=> $gross_pay,
                        "remittance_sched_ctr"=> $remittance_sched_ctr,
                        "is_last_remittance_schedule"=> $is_last_remittance_schedule,
                        "month_name"=> $month_name,
                        "year"=> $year,
                        "sss_class"=> $company->sss_class,
                        "payout_sched"=> $employee->payout_sched,
                        "payroll_type"=> $employee->payroll_type,
                        "emp_id"=> $employee->id,
                        "contribution_acct_no"=>$contAcctNumber,
                    );

                    $tempTax = $this->generateIncomeTaxCalculator($employeeRate, $parameters);
                    $updatedTaxableIncome = $tempTax->taxable_income;
                    unset($tempTax->taxable_income);
                    $taxable_income_formatted = number_format($updatedTaxableIncome, 2, '.', '');
                    $employee->taxable_income = $taxable_income_formatted;

                    $employee->tax = $tempTax;
                }

                /*** condition for remittances deduction to grossPay ***/
                $remittancesRecord = new stdClass();
                $remittancesRecord->tax_ee = 0;
                $remittancesRecord->sss_ee = 0;
                $remittancesRecord->sss_er = 0;
                $remittancesRecord->sss_prov_ee = 0;
                $remittancesRecord->sss_prov_er = 0;
                $remittancesRecord->hdmf_ee = 0;
                $remittancesRecord->phic_ee = 0;

                if(isset($employee->tax->ee) && $employee->tax->ee){
                    $currentTax = $employee->tax->ee;
                    if($currentTax <= $_gross_pay){
                        $remittancesRecord->tax_ee = $currentTax;
                        $_gross_pay = $_gross_pay - $currentTax;
                    }
                }

                if(isset($employee->sss->ee) && $employee->sss->ee){
                    $currentSssEe = $employee->sss->ee;
                    $currentSssEr = $employee->sss->er;
                    $currentSssProvidentEe = isset($employee->sss->provident->ee) && $employee->sss->provident->ee ? $employee->sss->provident->ee: 0;
                    $currentSssProvidentEr = isset($employee->sss->provident->er) && $employee->sss->provident->er ? $employee->sss->provident->er: 0;
                    
                    $totalSssContribution = 0;
                    $totalSssContribution = $currentSssEe + $currentSssProvidentEe;
                    if($totalSssContribution <= $_gross_pay){
                        $remittancesRecord->sss_ee = $currentSssEe;
                        $remittancesRecord->sss_er = $currentSssEr;
                        $remittancesRecord->sss_prov_ee = $currentSssProvidentEe;
                        $remittancesRecord->sss_prov_er = $currentSssProvidentEr;
                        $_gross_pay = $_gross_pay - $totalSssContribution;
                    }
                }

                if(isset($employee->hdmf->ee) && $employee->hdmf->ee){
                    $currentHdmf = $employee->hdmf->ee;
                    if($currentHdmf <= $_gross_pay){
                        $remittancesRecord->hdmf_ee = $currentHdmf;
                        $_gross_pay = $_gross_pay - $currentHdmf;
                    }
                }

                if(isset($employee->phic->ee) && $employee->phic->ee){
                    $currentPhic = $employee->phic->ee;
                    if($currentPhic <= $_gross_pay){
                        $remittancesRecord->phic_ee = $currentPhic;
                        $_gross_pay = $_gross_pay - $currentPhic;
                    }
                }
                /*** condition for remittances deduction to grossPay ***/
                

                $tempSSSContribution = 0;
                $providentEE = isset($remittancesRecord->sss_prov_ee) && $remittancesRecord->sss_prov_ee ?
                    $remittancesRecord->sss_prov_ee: 0;
                $providentER = isset($remittancesRecord->sss_prov_ee) && $remittancesRecord->sss_prov_ee ?
                    $remittancesRecord->sss_prov_er: 0;

                $tempSSSContribution = $remittancesRecord->sss_ee + $providentEE;
                $employee->total_govt_remittances = ($remittancesRecord->hdmf_ee + $remittancesRecord->phic_ee + $tempSSSContribution + $remittancesRecord->tax_ee);

                $employee->gross_pay = number_format($gross_pay, 2, '.', '');

                $loans =  $this->getEmployeeLoans($employee->id, $gross_pay, 0, $_gross_pay, true);
                $employee->loans = $loans;

                $postedPayrollSheetRecord = isset($payroll_sheet_row) && !empty($payroll_sheet_row) && intval($payroll_sheet_row->posted) === 1;
                $employee->total_loans = $postedPayrollSheetRecord ? $payroll_sheet_row->total_loans : 0;
                $employee->total_loans_interest = $postedPayrollSheetRecord ? $payroll_sheet_row->total_loans_interest : 0;
                $employee->sss_loan = $postedPayrollSheetRecord ? $payroll_sheet_row->sss_loan : 0;
                $employee->hdmf_loan = $postedPayrollSheetRecord ? $payroll_sheet_row->hdmf_loan : 0;

                if(is_array($loans) && count($loans) > 0 && $postedPayrollSheetRecord === false && (floatval($_gross_pay) > 0)){ // added additional checker that for $_gross_pay to prevent deduction to no earners employees
                    foreach ($loans as $loan) {
                        if($loan->active == 1 && $loan->loan_type == 0){
                            /*** loan internal ***/
                            if(floatval($loan->amount_due) > 0 && $_gross_pay >= $loan->amount_due && intval($loan->zero_netpay) == 0){
                                $employee->total_loans += $loan->amount_due;
                                $_gross_pay = $_gross_pay - $loan->amount_due;
                            }
                            /*** loan internal ***/

                            /*** loan interest ***/
                            if(floatval($loan->interest_amount) > 0 && $_gross_pay >= $loan->interest_amount && intval($loan->zero_netpay) == 0){
                                $employee->total_loans_interest += $loan->interest_amount;
                                $_gross_pay = $_gross_pay - $loan->interest_amount;
                            }
                            /*** loan interest ***/
                        }
                    }

                    /*** loans external ***/
                    /*** loans sss ***/
                    foreach ($loans as $loan) {
                        if($loan->active == 1 && ($loan->loan_type == 1 && intval($loan->loan_class) == 1) && intval($loan->zero_netpay) == 0 &&
                        (floatval($loan->amount_due) > 0 && $_gross_pay >= $loan->amount_due)){
                            $employee->sss_loan += $loan->amount_due;
                            $_gross_pay = $_gross_pay - $loan->amount_due;
                        }
                    }
                    /*** loans sss ***/
                    /*** loans hdmf ***/
                    foreach ($loans as $loan) {
                        if($loan->active == 1 && ($loan->loan_type == 1 && intval($loan->loan_class) == 2) && intval($loan->zero_netpay) == 0 &&
                        (floatval($loan->amount_due) > 0 && $_gross_pay >= $loan->amount_due)){
                            $employee->hdmf_loan += $loan->amount_due;
                            $_gross_pay = $_gross_pay - $loan->amount_due;
                        }
                    }
                    /*** loans hdmf
                    /*** loans external ***/
                }
                
                $toDeductLoans = 0;
                $toDeductLoans = ($employee->total_loans + $employee->total_loans_interest) + $employee->sss_loan + $employee->hdmf_loan;
                /*** $toDeductLoans = ($employee->total_loans + $employee->total_loans_interest) + $sssLoans + $hdmfLoans; ***/
                
                $net_pay = $gross_pay - ($employee->total_govt_remittances + $toDeductLoans);
                $employee->temp_net_pay = $net_pay;
                $employee->net_pay = number_format($net_pay, 2, '.', '');

                $days_worked = $excude_holiday_days_worked > 0 ? $excude_holiday_days_worked: $days_worked;
                if($days_worked > 0){
                    $displayName = (object) $this->core_layout->getDisplayName((array) $employee);
                    if(count(get_object_vars($displayName)) > 0){
                        $tempLate = "---";
                        $tempUndertime = "---";
                        $employeeName = isset($displayName->display_name_0) && $displayName->display_name_0? strtoupper($displayName->display_name_0): "NO ASSIGNED NAME";
                        $lateHours = floatval($late_minutes) / 60;
                        if($lateHours > 1){
                            $temp = explode(".", $lateHours);
                            if(count($temp) == 2){
                                $tempLate = "";
                                $hrs = "hr";
                                $mins = "min";
                                if(isset($temp[0]) && intval($temp[0]) > 0){
                                    if(intval($temp[0]) > 1){ $hrs = "hrs"; }
                                    $tempLate .= "{$temp[0]} {$hrs} ";
                                }
                                if(isset($temp[1]) && intval($temp[1]) > 0){
                                    $tempMin = floatval("0.{$temp[1]}");
                                    $xmin = ceil($tempMin * 60);
                                    if($xmin == 60){
                                        $temp[0] = intval($temp[0]) + 1;
                                        $hrs = intval($temp[0]) > 1 ? "hrs": "hr";
                                        $tempLate = "{$temp[0]} {$hrs}";
                                    }else{
                                        if($xmin > 1){ $mins = "mins"; }
                                        $tempLate .= "{$xmin} {$mins}";
                                    }
                                }
                            }else{
                                $tempLate = "";
                                $hrs = "hr";
                                if(isset($temp[0]) && intval($temp[0]) > 0){
                                    if(intval($temp[0]) > 1){ $hrs = "hrs"; }
                                    $tempLate .= "{$temp[0]} {$hrs} ";
                                }
                            }
                        }else{
                            if($lateHours > 0){
                                $mins = "min";
                                $tempMin = floatval($lateHours);
                                $xmin = ceil($tempMin * 60);
                                if($xmin == 60){
                                    $tempLate = "1 hr";
                                }else{
                                    if($xmin > 1){ $mins = "mins"; }
                                    $tempLate = "{$xmin} {$mins}";
                                }
                            }
                        }
                        $lateHours = round($lateHours, 2);

                        $undertimeHours = floatval($undertime_only_minutes) / 60;
                        if($undertimeHours > 1){
                            $temp = explode(".", $undertimeHours);
                            if(count($temp) == 2){
                                $tempUndertime = "";
                                $hrs = "hr";
                                $mins = "min";
                                if(isset($temp[0]) && intval($temp[0]) > 0){
                                    if(intval($temp[0]) > 1){ $hrs = "hrs"; }
                                    $tempUndertime .= "{$temp[0]} {$hrs} ";
                                }
                                if(isset($temp[1]) && intval($temp[1]) > 0){
                                    $tempMin = floatval("0.{$temp[1]}");
                                    $xmin = ceil($tempMin * 60);
                                    if($xmin == 60){
                                        $temp[0] = intval($temp[0]) + 1;
                                        $hrs = intval($temp[0]) > 1 ? "hrs": "hr";
                                        $tempUndertime = "{$temp[0]} {$hrs}";
                                    }else{
                                        if($xmin > 1){ $mins = "mins"; }
                                        $tempUndertime .= "{$xmin} {$mins}";
                                    }
                                }
                            }else{
                                $tempUndertime = "";
                                $hrs = "hr";
                                if(isset($temp[0]) && intval($temp[0]) > 0){
                                    if(intval($temp[0]) > 1){ $hrs = "hrs"; }
                                    $tempUndertime .= "{$temp[0]} {$hrs} ";
                                }
                            }
                        }else{
                            if($undertimeHours > 0){
                                $mins = "min";
                                $tempMin = floatval($undertimeHours);
                                $xmin = ceil($tempMin * 60);
                                if($xmin == 60){
                                    $tempUndertime = "1 hr";
                                }else{
                                    if($xmin > 1){ $mins = "mins"; }
                                    $tempUndertime = "{$xmin} {$mins}";
                                }
                            }
                        }

                        $undertimeHours = round($undertimeHours, 2);
                        $tempRenderedDaysWorked = $rendered_days_worked;
                        
                        if($half_day_absent > 0){
                            $tempHalfDay = $half_day_absent * 0.5;
                            $wholeDayAbsent += $tempHalfDay;
                            $tempRenderedDaysWorked = $tempRenderedDaysWorked - $tempHalfDay;
                        }
                        
                        $temp_undertime_records = array(
                            "emp_id"=>$employee->id,
                            "employee_name"=>$employeeName,
                            "late_hours"=> $tempLate,
                            "undertime_hours"=> $tempUndertime,
                            "absences" => $wholeDayAbsent,
                            "unpaid_holiday"=>$unpaidHoliday,
                            "ewd"=>$ewd
                        );
                        $employees["undertime_records"][] = $temp_undertime_records;
                    }
                }
                
                $data = array(
                    "date_start" => $start,
                    "date_end" => $end,
                    "pay_date" => date('Y-m-d', strtotime($posted_data["pay_date"])),
                    "ewd" => $ewd,
                    "month_name" => $month_name,
                    "year" => $year,
                    "payroll_sched" => $employee->payout_sched,
                    "payroll_seq" => $payout_sequence,
                    "emp_id" => $employee->id,
                    "company_id" => $employee->company_id,
                    "rate" => $employee->rate,
                    "payroll_type" => $employee->payroll_type,
                    "daily" => $daily,
                    "no_of_days" => $days_worked,
                    "total_minutes_worked" => $total_minutes,
                    "basic_rate" => $basic_rate,
                    "total_allowances" => $employee->total_allowance,
                    "total_holiday_minutes" => $holiday_minutes,
                    "total_holiday_amount" => $holiday,
                    "unpaid_holiday_minutes" => $unpaid_holiday_minutes,
                    "unpaid_holiday_amount" => $unpaid_holiday_amount,
                    "ut_minutes" => $total_ut_minutes,
                    "ut_amount" => $total_ut_amount,
                    "late_minutes" => $total_late_minutes,
                    "late_amount" => $total_late_amount,
                    "total_undertime_minutes" => $undertime_minutes,
                    "total_undertime_amount" => $undertime,
                    "total_unrendered_minutes" => $total_unrendered_minutes,
                    "total_unrendered_amount" => $total_unrendered_amount,
                    "ot_minutes" => $ot_minutes,
                    "ot_amount" => $ot_amount,
                    "ot_ndiff_minutes" => $ot_ndiff_minutes,
                    "ot_ndiff_amount" => $ot_ndiff_amount,
                    "ot_allowance_minutes" => $total_ot_allowance_minutes,
                    "ot_allowance_amount" => $total_ot_allowance_amount,
                    "total_ndiff_minutes" => $total_ndiff_minutes,
                    "total_ndiff_amount" => $total_ndiff_amount,
                    "gross_pay" => $gross_pay,
                    "sss" => $remittancesRecord->sss_ee,
                    "sss_er" => $remittancesRecord->sss_er,
                    "sss_prov" => $providentEE,
                    "sss_prov_er" => $providentER,
                    "ph" => $remittancesRecord->phic_ee,
                    "hdmf" => $remittancesRecord->hdmf_ee,
                    "tax" => $remittancesRecord->tax_ee,
                    "total_loans" => $employee->total_loans,
                    "total_loans_interest" => $employee->total_loans_interest,
                    "sss_loan" => $employee->sss_loan,
                    "hdmf_loan" => $employee->hdmf_loan,
                    "net_pay" => $net_pay,
                    "is_monthly_paid" => $isMonthlyPaidEmployee,
                );

                $payroll_sheet_id = null;
                if ($payroll_sheet_row && count(get_object_vars($payroll_sheet_row)) > 0) {
                    if (intval($payroll_sheet_row->posted) === 0) {
                        $data["updated_at"] = date("Y-m-d H:i:s");
                        $data["updated_by"] = $this->core_layout->getCurrentEmployeeId();
                        $this->db->where("id", $payroll_sheet_row->id)->update("payroll.payroll_sheet", $data);
                    }
                    $payroll_sheet_id = $payroll_sheet_row->id;
                } else {
                    if(is_array($data) && !empty($data)){
                        $data["created_at"] = date("Y-m-d H:i:s");
                        $data["created_by"] = $this->core_layout->getCurrentEmployeeId();
                    }
                    $this->db->insert("payroll.payroll_sheet", $data);
                    $payroll_sheet_id = $this->db->insert_id();
                }

                // START CALCULATE CUSTOM ADJUSTMENTS
                $payroll_sheet = $this->db->where("id", $payroll_sheet_id)->get("payroll.payroll_sheet")->row();

                $_total_basic_rate = $payroll_sheet->basic_rate;
                $_total_allowances = $payroll_sheet->total_allowances;
                $_total_ot_ndiff = $payroll_sheet->ot_amount + $payroll_sheet->ot_ndiff_amount;
                $_total_night_diff = $payroll_sheet->total_ndiff_amount;
                $_total_ot_allowance = $payroll_sheet->ot_allowance_amount;

                $gross_pay = $_total_basic_rate + $_total_allowances + $_total_ot_ndiff + $_total_night_diff + $_total_ot_allowance;

                $custom_adjustments = $this->db->where("payroll_sheet_id", $payroll_sheet_id)
                    ->get("payroll.payroll_sheet_custom_adjustments");

                $tempGrossDeduction = 0;
                if($custom_adjustments->num_rows() > 0){
                    foreach ($custom_adjustments->result() as $row) {
                        if (intval($row->cadj_type) === 0) {
                            $tempGrossDeduction += $row->amount;
                        } else {
                            $gross_pay += $row->amount;
                        }
                    }
                }
                $_gross_pay = $gross_pay;
                
                $sssContributionBasis = ${$tempParameter};
                $sssContributionBasis = $sssContributionBasis ? $sssContributionBasis: $gross_pay;

                $tempCurrentSSSDeduction = $remittancesRecord->sss_ee;
                $tempCurrentHDMFDeduction = $remittancesRecord->hdmf_ee;
                $tempCurrentPHICDeduction = $remittancesRecord->phic_ee;
                $tempCurrentTAXDeduction = $remittancesRecord->tax_ee;

                $updatedHDMF = $this->getHdmf($remittance_sched_ctr, $remittance_parameters->hdmf->status, $gross_pay, $temp_has_pagibig_no);
                $updatedPHIC = $this->calculatePhilHealth($remittance_sched_ctr, $employee->monthly_basic, $remittance_parameters->phic->status, $gross_pay, $temp_has_phealth_no);

                $updateSSS = $this->calculateSss(array(
                    "gross_pay" => $sssContributionBasis,
                    "switch" => $remittance_parameters->sss->status,
                    "remittance_parameters" => $remittance_parameters,
                    "min_remittance_sched" => $min_remittance_sched,
                    "max_remittance_sched" => $max_remittance_sched,
                    "should_deduct_remittances" => $should_deduct_remittances,
                    "is_last_remittance_schedule" => $is_last_remittance_schedule,
                    "month_name" => $month_name,
                    "year" => $year,
                    "payout_sched" => $employee->payout_sched,
                    "emp_id" => $employee->id,
                    "has_sss_no" => $temp_has_sss_no,
                    "contribution_basis"=>$sss_contribution_basis,
                ), $company->sss_class);

                if($alteredTaxableIncome === false){
                    $parameters = array(
                        "switch"=>$remittance_parameters->tax->status,
                        "gross_pay"=> $gross_pay,
                        "remittance_sched_ctr"=> $remittance_sched_ctr,
                        "is_last_remittance_schedule"=> $is_last_remittance_schedule,
                        "month_name"=> $month_name,
                        "year"=> $year,
                        "sss_class"=> $company->sss_class,
                        "payout_sched"=> $employee->payout_sched,
                        "payroll_type"=> $employee->payroll_type,
                        "emp_id"=> $employee->id,
                        "contribution_acct_no"=>$contAcctNumber,
                    );

                    $tempTax = $this->generateIncomeTaxCalculator($employeeRate, $parameters);
                    $updatedTaxableIncome = $tempTax->taxable_income;
                    unset($tempTax->taxable_income);
                    $taxable_income_formatted = number_format($updatedTaxableIncome, 2, '.', '');
                    $employee->taxable_income = $taxable_income_formatted;

                    $employee->tax = $tempTax;
                }

                $_remittancesRecord = new stdClass();
                $_remittancesRecord->sss_ee = 0;
                $_remittancesRecord->sss_er = 0;
                $_remittancesRecord->sss_prov_ee = 0;
                $_remittancesRecord->sss_prov_er = 0;

                $_remittancesRecord->hdmf_ee = 0;
                $_remittancesRecord->phic_ee = 0;
                $_remittancesRecord->tax_ee = 0;

                if(isset($updateSSS->ee) && $updateSSS->ee){
                    $currentSssEe = $updateSSS->ee;
                    $currentSssEr = $updateSSS->er;
                    $currentSssProvidentEe = isset($updateSSS->provident->ee) && $updateSSS->provident->ee ? $updateSSS->provident->ee: 0;
                    $currentSssProvidentEr = isset($updateSSS->provident->er) && $updateSSS->provident->er ? $updateSSS->provident->er: 0;
                    
                    $totalSssContribution = 0;
                    $totalSssContribution = $currentSssEe + $currentSssProvidentEe;
                    if($totalSssContribution <= $_gross_pay){
                        $_remittancesRecord->sss_ee = $currentSssEe;
                        $_remittancesRecord->sss_er = $currentSssEr;
                        $_remittancesRecord->sss_prov_ee = $currentSssProvidentEe;
                        $_remittancesRecord->sss_prov_er = $currentSssProvidentEr;
                        $_gross_pay = $_gross_pay - $totalSssContribution;
                    }
                }

                if(isset($updatedHDMF->ee) && $updatedHDMF->ee){
                    $currentHdmf = $updatedHDMF->ee;
                    if($currentHdmf <= $_gross_pay){
                        $_remittancesRecord->hdmf_ee = $currentHdmf;
                        $_gross_pay = $_gross_pay - $currentHdmf;
                    }
                }

                if(isset($updatedPHIC->ee) && $updatedPHIC->ee){
                    $currentPhic = $updatedPHIC->ee;
                    if($currentPhic <= $_gross_pay){
                        $_remittancesRecord->phic_ee = $currentPhic;
                        $_gross_pay = $_gross_pay - $currentPhic;
                    }
                }

                if(isset($employee->tax->ee) && $employee->tax->ee){
                    $currentTax = $employee->tax->ee;
                    if($currentTax <= $_gross_pay){
                        $_remittancesRecord->tax_ee = $currentTax;
                        $_gross_pay = $_gross_pay - $currentTax;
                    }
                }

                $updatedProvidentEE = (isset($_remittancesRecord->sss_prov_ee) && $_remittancesRecord->sss_prov_ee)?
                    $_remittancesRecord->sss_prov_ee: 0;
                $updatedProvidentER = (isset($_remittancesRecord->sss_prov_er) && $_remittancesRecord->sss_prov_er)?
                    $_remittancesRecord->sss_prov_er: 0;
                
                if($_remittancesRecord->sss_ee !== $tempCurrentSSSDeduction && $tempCurrentSSSDeduction > 0){
                    $employee->total_govt_remittances = $employee->total_govt_remittances - $tempCurrentSSSDeduction;
                    $employee->total_govt_remittances = $employee->total_govt_remittances + $_remittancesRecord->sss_ee;
                }

                if($updatedProvidentEE !== $providentEE && $updatedProvidentEE > 0){
                    $employee->total_govt_remittances = $employee->total_govt_remittances - $providentEE;
                    $employee->total_govt_remittances = $employee->total_govt_remittances + $updatedProvidentEE;
                }

                if($_remittancesRecord->hdmf_ee !== $tempCurrentHDMFDeduction && $tempCurrentHDMFDeduction > 0){
                    $employee->total_govt_remittances = $employee->total_govt_remittances - $tempCurrentHDMFDeduction;
                    $employee->total_govt_remittances = $employee->total_govt_remittances + $_remittancesRecord->phic_ee;
                }

                if($_remittancesRecord->phic_ee !== $tempCurrentPHICDeduction && $tempCurrentPHICDeduction > 0){
                    $employee->total_govt_remittances = $employee->total_govt_remittances - $tempCurrentPHICDeduction;
                    $employee->total_govt_remittances = $employee->total_govt_remittances + $_remittancesRecord->phic_ee;
                }

                if($_remittancesRecord->tax_ee !== $tempCurrentTAXDeduction && $tempCurrentTAXDeduction > 0){
                    $employee->total_govt_remittances = $employee->total_govt_remittances - $tempCurrentTAXDeduction;
                    $employee->total_govt_remittances = $employee->total_govt_remittances + $_remittancesRecord->phic_ee;
                }


                $tempDeductions = 0;
                $tempWhere = array(
                    "payroll_sheet_id"=>$payroll_sheet_id,
                    "status"=>1
                );
                $created_adjustments = $this->db->where($tempWhere)
                ->get("payroll.payroll_sheet_created_adjustments");
                if($created_adjustments->num_rows() > 0){
                    foreach ($created_adjustments->result() as $key => $value) {
                        $tempAmount = floatval($value->amount);
                        if (intval($value->adj_type) === 0) {
                            $tempDeductions -= $tempAmount;
                        } else {
                            $tempDeductions += $tempAmount;
                        }
                    }
                }

                // suspends employee active loans when the gross pay is 0 when the generated payrollsheet is not posted
                // commented source code to disable suspending no earners loan
                // if (floatval($_gross_pay) <= 0 && floatval($gross_pay) <= 0 && intval($payroll_sheet_row->posted) === 0) {
                //     $this->suspendNoEarnersLoans($employee->id);
                // }
                // commented source code to disable suspending no earners loan

                if (floatval($_gross_pay) <= 0 && floatval($gross_pay) <= 0 && (!isset($payroll_sheet_row) || intval($payroll_sheet_row->posted) === 0)) {
                    $this->notifSuspended($employee->id);
                }

                $loans = (floatval($_gross_pay) <= 0 && floatval($gross_pay) <= 0)
                    ? $this->getEmployeeActiveLoansNotPaid($employee->id, $gross_pay, 0, $_gross_pay, true) //get all active employee loans that is not still paid
                    : $this->getEmployeeLoans($employee->id, $gross_pay, 0, $_gross_pay, true);

                $postedPayrollSheetRecord = isset($payroll_sheet_row) && !empty($payroll_sheet_row) && intval($payroll_sheet_row->posted) === 1;
                $updatedTotalLoans = $postedPayrollSheetRecord ? $payroll_sheet_row->total_loans : 0;
                $updatedTotalLoansInterest = $postedPayrollSheetRecord ? $payroll_sheet_row->total_loans_interest : 0;
                $updatedSSSLoans = $postedPayrollSheetRecord ? $payroll_sheet_row->sss_loan : 0;
                $updatedHDMFLoans = $postedPayrollSheetRecord ? $payroll_sheet_row->hdmf_loan : 0;

                $loanId = array();
                if(is_array($loans) && count($loans) > 0 && $postedPayrollSheetRecord === false && floatval($_gross_pay) > 0){ //added checker for gross pay to prevent running the loans foreach
                    foreach ($loans as $loan) {
                        if($loan->active == 1 && $loan->loan_type == 0){
                            /*** loan internal ***/
                            /*** if(floatval($loan->amount_due) > 0 && $_gross_pay >= $loan->amount_due && intval($loan->zero_netpay) == 0){ ***/
                            if(floatval($loan->amount_due) > 0 && round($_gross_pay, 2) >= round($loan->amount_due, 2)){
                                $updatedTotalLoans += $loan->amount_due;
                                $_gross_pay = $_gross_pay - $loan->amount_due;
                                $loanId[] = $loan->id;
                            }
                            /*** loan internal ***/

                            /*** loan interest ***/
                            /*** if(floatval($loan->interest_amount) > 0 && $_gross_pay >= $loan->interest_amount && intval($loan->zero_netpay) == 0){ ***/
                            if(floatval($loan->interest_amount) > 0 && round($_gross_pay, 2) >= round($loan->interest_amount, 2)){
                                $updatedTotalLoansInterest += $loan->interest_amount;
                                $_gross_pay = $_gross_pay - $loan->interest_amount;
                                $loanId[] = $loan->id;
                            }
                            /*** loan interest ***/
                        }
                    }

                    /*** loans external ***/
                    /*** loans sss ***/
                    foreach ($loans as $loan) {
                        /*** if($loan->active == 1 && ($loan->loan_type == 1 && intval($loan->loan_class) == 1) &&
                        (floatval($loan->amount_due) > 0 && $_gross_pay >= $loan->amount_due && intval($loan->zero_netpay) == 0)){ ***/
                        if($loan->active == 1 && ($loan->loan_type == 1 && intval($loan->loan_class) == 1) &&
                        (floatval($loan->amount_due) > 0 && round($_gross_pay, 2) >= round($loan->amount_due, 2))){
                            $updatedSSSLoans += $loan->amount_due;
                            $_gross_pay = $_gross_pay - $loan->amount_due;
                            $loanId[] = $loan->id;
                        }
                    }
                    /*** loans sss ***/
                    /*** loans hdmf ***/
                    foreach ($loans as $loan) {
                        /*** if($loan->active == 1 && ($loan->loan_type == 1 && intval($loan->loan_class) == 2) &&
                        (floatval($loan->amount_due) > 0 && $_gross_pay >= $loan->amount_due && intval($loan->zero_netpay) == 0)){ ***/
                        if($loan->active == 1 && ($loan->loan_type == 1 && intval($loan->loan_class) == 2) &&
                        (floatval($loan->amount_due) > 0 && round($_gross_pay, 2) >= round($loan->amount_due, 2))){
                            $updatedHDMFLoans += $loan->amount_due;
                            $_gross_pay = $_gross_pay - $loan->amount_due;
                            $loanId[] = $loan->id;
                        }
                    }
                    /*** loans hdmf
                    /*** loans external ***/
                }

                $updatedToDeductLoans = 0;
                $updatedToDeductLoans = ($updatedTotalLoans + $updatedTotalLoansInterest) + $updatedSSSLoans + $updatedHDMFLoans;
                /*** updated loans section ***/

                if (floatval($_gross_pay) > 0 && floatval($gross_pay) > 0 && (!isset($payroll_sheet_row) || intval($payroll_sheet_row->posted) === 0)) {
                    $this->notifSuspended($employee->id, $loanId);
                }

                if (!isset($payroll_sheet_row) || intval($payroll_sheet_row->posted) === 0) {
                    $tempDeductions = $employee->total_govt_remittances + ($tempDeductions);
                    $tempGrossDeduction = $gross_pay - $tempGrossDeduction;
                    $net_pay = $tempGrossDeduction - ($tempDeductions + $updatedToDeductLoans);

                    $employee->net_pay_computed = $net_pay;

                    $updatePayrollSheetData = array(
                        "gross_pay" => $gross_pay,
                        "net_pay" => $net_pay,
                        "sss"=>$_remittancesRecord->sss_ee,
                        "sss_er"=>$_remittancesRecord->sss_er,
                        "sss_prov"=>$updatedProvidentEE,
                        "sss_prov_er"=>$updatedProvidentER,
                        "ph" => $_remittancesRecord->phic_ee,
                        "hdmf" => $_remittancesRecord->hdmf_ee,
                        "tax" => $_remittancesRecord->tax_ee,
                        "total_loans"=>$updatedTotalLoans,
                        "total_loans_interest"=>$updatedTotalLoansInterest,
                        "sss_loan"=>$updatedSSSLoans,
                        "hdmf_loan"=>$updatedHDMFLoans,
                    );

                    $this->db->where("id", $payroll_sheet_id)->update("payroll.payroll_sheet", $updatePayrollSheetData);

                    $this->db->select("SUM(amount_due) as amount_due");
                    $totalLoansAmount = $this->db->get_where("payroll.payroll_sheet_loan_payments", array("payroll_sheet_id"=>$payroll_sheet_id))->row();
                    $allowResetLoans = ($updatedToDeductLoans >= 0 && $totalLoansAmount->amount_due >= 0) && $totalLoansAmount->amount_due != $updatedToDeductLoans;

                    
                    if (count($loans) <= 0) {
                        $this->db->where("payroll_sheet_id", $payroll_sheet_id)->delete("payroll.payroll_sheet_loan_payments");
                    } else {
                        if($allowResetLoans){ $this->db->where("payroll_sheet_id", $payroll_sheet_id)->delete("payroll.payroll_sheet_loan_payments"); }
                        
                        $finalLoanInterest = 0;
                        foreach ($loans as $loan) {
                            if(is_array($loanId) && in_array($loan->id, $loanId)){

                                $isZeroNetPay = intval($loan->zero_netpay) == 1;
                                if(floatval($loan->amount_due) > 0){
                                    $loan_data = array(
                                        "payroll_sheet_id" => $payroll_sheet_id,
                                        "loan_id" => $loan->id,
                                        "amount_due" => $loan->amount_due
                                    );
        
                                    $loan_row = $this->db
                                        ->where("loan_id", $loan->id)
                                        ->where("payroll_sheet_id", $payroll_sheet_id)
                                        ->get("payroll.payroll_sheet_loan_payments")
                                        ->row();
                                    
                                    if (intval($loan->active) !== 1 && $isZeroNetPay) {
                                        if (!empty($loan_row)) {
                                            $this->db->where("id", $loan_row->id)->delete("payroll.payroll_sheet_loan_payments");
                                        }
                                    } else {
                                        if (!empty($loan_row)) {
                                            unset($loan_data["payroll_sheet_id"], $loan_data["loan_id"]);
                                            $this->db->where("id", $loan_row->id)->update("payroll.payroll_sheet_loan_payments", $loan_data);
                                        } else {
                                            $this->db->insert("payroll.payroll_sheet_loan_payments", $loan_data);
                                        }
                                    }
    
                                    /*** hotfix issue zero out ***/
                                    $loanExist = $this->db->get_where("payroll.payroll_sheet_loan_payments", array("loan_id"=>$loan->id, "payroll_sheet_id"=>$payroll_sheet_id));
                                    if(intval($loan->active) === 1 && $isZeroNetPay && $loanExist->num_rows() == 0){
                                        $this->db->insert("payroll.payroll_sheet_loan_payments", $loan_data);
                                    }
                                    /*** hotfix issue zero out ***/
                                }else{
                                    /*** for zero amount due ***/
                                    if(floatval($loan->amount_due) <= 0){
                                        $getZeroLoan = $this->db
                                        ->where("loan_id", $loan->id)
                                        ->where("payroll_sheet_id", $payroll_sheet_id)
                                        ->get("payroll.payroll_sheet_loan_payments");
                                        if($getZeroLoan->num_rows() > 0){
                                            foreach ($getZeroLoan->result() as $zeroLoan) {
                                                $this->db->where("id", $zeroLoan->id)->delete("payroll.payroll_sheet_loan_payments");
                                            }
                                        }
                                    }
                                    /*** for zero amount due ***/
                                }
                            }

                            $tempBalance = floatval($loan->amount) - floatval($loan->total_amount_paid);
                            $isFinalPayment = $tempBalance > 0 && $tempBalance <= $loan->amount_due ? true : false;

                            /*** loan amount interest ***/
                            $lastInterestCharge = intval($loan->last_interest_charge) === 1;
                            $this->db->select("GROUP_CONCAT(DISTINCT id) as int_id, SUM(amount_due) as total_amount_due");
                            $unpaidLoansInterest = $this->db->get_where("payroll.payroll_sheet_loan_interest_payments", array("loan_id"=>$loan->id, "is_active"=>0));
                            $hasUnpaidLoanInterest = $unpaidLoansInterest->num_rows() === 1;

                            $_tempLoans = $this->db->get_where("payroll.payroll_sheet_loan_payments", array("loan_id"=>$loan->id));
                            $_runningLoans = $_tempLoans->num_rows() > 0;

                            if($_runningLoans && floatval($loan->interest_amount) > 0){
                                $loan_data = array(
                                    "payroll_sheet_id" => $payroll_sheet_id,
                                    "loan_id" => $loan->id,
                                    "interest_percentage" => $loan->interest_percentage,
                                    "amount_due" => $loan->interest_amount,
                                    "is_active" => intval($loan->active),
                                    "total_interest_amount" => intval($loan->active) === 1 ? $loan->interest_amount: 0,
                                );

                                $loan_row = $this->db
                                    ->where("loan_id", $loan->id)
                                    ->where("payroll_sheet_id", $payroll_sheet_id)
                                    ->get("payroll.payroll_sheet_loan_interest_payments")
                                    ->row();
    
                                if (intval($loan->active) === 2 || intval($loan->paid) === 1) {
                                    if (!empty($loan_row)) {
                                        $this->db->where("id", $loan_row->id)->delete("payroll.payroll_sheet_loan_interest_payments");
                                    }
                                } else {
                                    if (!empty($loan_row)) {
                                        unset($loan_data["payroll_sheet_id"], $loan_data["loan_id"]);
                                        $updatedInterest = $this->db->where("id", $loan_row->id)->update("payroll.payroll_sheet_loan_interest_payments", $loan_data);
                                        if($updatedInterest && intval($loan->active) === 1){ $finalLoanInterest += floatval($loan->interest_amount); }
                                    } else {
                                        $addedInterest = $this->db->insert("payroll.payroll_sheet_loan_interest_payments", $loan_data);
                                        if($addedInterest && intval($loan->active) === 1){ $finalLoanInterest += floatval($loan->interest_amount); }
                                    }
                                }
                                $this->db->reset_query();

                                $activeLoanInterest = $this->db->get_where("payroll.payroll_sheet_loan_interest_payments", 
                                array("payroll_sheet_id"=>$payroll_sheet_id, "loan_id" => $loan->id, "is_active"=>1));

                                if($activeLoanInterest->num_rows() === 1){
                                    $tempRowInt = $activeLoanInterest->row();

                                    $this->db->select("GROUP_CONCAT(DISTINCT id) as int_id, SUM(amount_due) as total_amount_due");
                                    $getCurrentLoansInterests = $this->db->get_where("payroll.payroll_sheet_loan_interest_payments", 
                                    array("loan_id" => $loan->id, "is_active"=>2, "interest_id"=>$tempRowInt->id));

                                    if($getCurrentLoansInterests->num_rows() === 1 && $getCurrentLoansInterests->row()->int_id !== null){
                                        $hasUnpaidLoanInterest = $getCurrentLoansInterests->num_rows() === 1;
                                        $unpaidLoansInterest = $getCurrentLoansInterests;
                                    }

                                    if($hasUnpaidLoanInterest){
                                        $unpaidRowInt = $unpaidLoansInterest->row();
                                        $triggerUnpaidState = ($lastInterestCharge === false && $isFinalPayment === false)
                                        || ($lastInterestCharge === true && $isFinalPayment === true) ? true: false;

                                        if($lastInterestCharge){
                                            $rowIds = explode(",", $unpaidRowInt->int_id);
                                            if(is_array($rowIds) && count($rowIds) > 0){
                                                $this->db->where_in("id", $rowIds);
                                                $this->db->update("payroll.payroll_sheet_loan_interest_payments", array("is_active"=>0, "interest_id"=>0));
                                            }
                                        }

                                        if($triggerUnpaidState){
                                            $totalInterestAmount = floatval($unpaidRowInt->total_amount_due) + floatval($tempRowInt->amount_due);
                                            $updatedRowInt = $this->db->update("payroll.payroll_sheet_loan_interest_payments",
                                                array("total_interest_amount"=>$totalInterestAmount),
                                                array("id"=>$tempRowInt->id));
    
                                            if($updatedRowInt && $this->db->affected_rows() === 1){
                                                if(intval($loan->active) === 1){ $finalLoanInterest += floatval($unpaidRowInt->total_amount_due); }
    
                                                $rowIds = explode(",", $unpaidRowInt->int_id);
                                                if(is_array($rowIds) && count($rowIds) > 0){
                                                    $this->db->where_in("id", $rowIds);
                                                    $this->db->update("payroll.payroll_sheet_loan_interest_payments", array("is_active"=>2, "interest_id"=>$tempRowInt->id));
                                                }
                                            }
                                        }
                                    }
                                }
                                $this->db->reset_query();
                            }
                            /*** loan amount interest ***/
                        }

                        $_updatedToDeductLoans = 0;
                        $_updatedToDeductLoans = ($updatedTotalLoans + $finalLoanInterest) + $updatedSSSLoans + $updatedHDMFLoans;
                        $_netPay = $tempGrossDeduction - ($tempDeductions + $_updatedToDeductLoans);
                        $employee->net_pay_computed = $_netPay;

                        $_updatePayrollSheetData = array("net_pay" => $_netPay, "total_loans_interest"=>$finalLoanInterest);
                        $this->db->where("id", $payroll_sheet_id)->update("payroll.payroll_sheet", $_updatePayrollSheetData);
                    }

                    $this->db->reset_query();

                    /*** check zero balance loans and update ***/
                    $suspendedLoans = $this->getEmployeeLoans($employee->id, $gross_pay, 0);
                    if($suspendedLoans > 0){
                        foreach ($suspendedLoans as $loan) {
                            if(intval($loan->active) == 0){
                                $qLoans = $this->db
                                ->where("loan_id", $loan->id)
                                ->get("payroll.payroll_sheet_loan_payments");
                                if($qLoans->num_rows() > 0){
                                    $amount_paid = 0;
                                    foreach ($qLoans->result() as $key => $value) { $amount_paid += $value->amount_due; }
                                    $tempLoanAmount = round($loan->amount, 2);
                                    $tempAmountPaid = round($amount_paid, 2);
                                    $tempBalance = $tempLoanAmount - $tempAmountPaid;
                                    if($tempLoanAmount === $tempAmountPaid || $tempBalance <= 0){
                                        $this->db->update("gcchris.loans", array("paid"=>1, "active"=>2), array("id"=>$loan->id));
                                    }
                                }
                            }
                        }
                    }
                    /*** check zero balance loans and update ***/
                    /*** check paid balance loans and update ***/
                    $paidLoans = $this->getEmployeeLoans($employee->id, $gross_pay, 1);
                    if($paidLoans > 0){
                        foreach ($paidLoans as $loan) {
                            $qLoans = $this->db
                                ->where("loan_id", $loan->id)
                                ->get("payroll.payroll_sheet_loan_payments");
                            if($qLoans->num_rows() > 0){
                                $amount_paid = 0;
                                foreach ($qLoans->result() as $key => $value) { $amount_paid += $value->amount_due; }
                                if(floatval($loan->amount) !== $amount_paid
                                    || (floatval($loan->total_amount_paid) == 0 || floatval($loan->amount_due) > 0)){
                                    $paidState = array("paid"=>0);
                                    if(intval($loan->active) == 2){ $paidState["active"] = 0; }

                                    $this->db->update("gcchris.loans", $paidState, array("id"=>$loan->id));
                                }
                            }else{
                                $this->db->update("gcchris.loans", array("paid"=>0, "active"=>0), array("id"=>$loan->id));
                            }
                        }
                    }
                    /*** check paid balance loans and update ***/

                    /*** check allowances update ***/
                    if (count($employee->allowances) <= 0) {
                        $this->db->where("payroll_sheet_id", $payroll_sheet_id)->delete("payroll.payroll_sheet_allowances");
                    } else {
                        foreach ($employee->allowances as $allowance) {
                            $allowance_data = array(
                                "payroll_sheet_id" => $payroll_sheet_id,
                                "emp_allowance_id" => $allowance->id,
                                "rate" => $allowance->rate,
                                "frequency" => $allowance->frequency,
                                "allowance_total" => $allowance->allowance_total,
                                "undertime_deduction" => $allowance->undertime_deduction,
                                "allowance_net" => $allowance->allowance_net
                            );

                            $allowance_row = $this->db
                                ->where("emp_allowance_id", $allowance->id)
                                ->where("payroll_sheet_id", $payroll_sheet_id)
                                ->get("payroll.payroll_sheet_allowances")
                                ->row();

                            if (intval($allowance->is_active) === 0) {
                                if (!empty($allowance_row)) {
                                    $this->db->where("id", $allowance_row->id)->delete("payroll.payroll_sheet_allowances");
                                }
                            } else {
                                if (!empty($allowance_row)) {
                                    unset($allowance_data["payroll_sheet_id"], $allowance_data["emp_allowance_id"]);
                                    $this->db->where("id", $allowance_row->id)->update("payroll.payroll_sheet_allowances", $allowance_data);
                                } else {
                                    $this->db->insert("payroll.payroll_sheet_allowances", $allowance_data);
                                }
                            }
                        }
                    }

                    /*** check allowances update ***/
                }
                // END CALCULATE CUSTOM ADJUSTMENTS

                $providentKeys = array("sss_prov"=>"sss");

                foreach ($remittance_parameters as $key => $value) {
                    // do not save if 0 or off in remittance parameters to save db space
                    $remittance_row = $this->db
                        ->where("payroll_sheet_id", $payroll_sheet_id)
                        ->where("remittance_code", $key)
                        ->get("payroll.payroll_sheet_remittances")
                        ->row();

                    $temp_ee = isset($employee->$key->ee) && $employee->$key->ee ? $employee->$key->ee: 0;
                    $temp_er = isset($employee->$key->er) && $employee->$key->er ? $employee->$key->er: 0;
                    $temp_total = isset($employee->$key->total) && $employee->$key->total ? $employee->$key->total: 0;

                    if(isset($providentKeys[$key]) && $providentKeys[$key]){
                        $tempKeyx = $providentKeys[$key];
                        $sssProvident = $employee->$tempKeyx->provident;
                        $temp_ee = isset($sssProvident->ee) && $sssProvident->ee ? $sssProvident->ee: 0;
                        $temp_er = isset($sssProvident->er) && $sssProvident->er ? $sssProvident->er: 0;
                        $temp_total = isset($sssProvident->total) && $sssProvident->total ? $sssProvident->total: 0;
                    }

                    $remittances_data = array(
                        "payroll_sheet_id" => $payroll_sheet_id,
                        "remittance_code" => $key,
                        "ee" => $temp_ee,
                        "er" => $temp_er,
                        "total" => $temp_total,
                    );

                    if (intval($value->status) === 1) {
                        if (!empty($remittance_row)) {
                            unset($remittances_data["payroll_sheet_id"], $remittances_data["remittance_code"]);

                            $this->db->where("id", $remittance_row->id)
                                ->update("payroll.payroll_sheet_remittances", $remittances_data);
                        } else {
                            $this->db->insert("payroll.payroll_sheet_remittances", $remittances_data);
                        }
                    } else {
                        if (!empty($remittance_row)) {
                            $this->db->where("id", $remittance_row->id)->delete("payroll.payroll_sheet_remittances");
                        }
                    }
                }
                /*** $employee->temp_computation = $atemp; ***/
                $employee->overtime = $otTemp;
                $employee->contributions = $currentPayrollContributions;

                $this->db->reset_query();
            }

            if(count($employees["data"]) > 0){
                $ctr = count($employees["data"]);
                $tempPayDate = date("F d, Y", strtotime($posted_data["pay_date"]));
                $coverageDateFrom = date("F d, Y", strtotime($start));
                $coverageDateTo = date("F d, Y", strtotime($end));
    
                $logMessage = "A total of {$ctr} employee(s) has been generated in the payroll sheet for the company `{$company->code}` with a pay date `{$tempPayDate}` and a coverage date from `{$coverageDateFrom}` to `{$coverageDateTo}`.";
                $this->core_layout->setEventLog($logMessage, "generate", "success", "payroll");
            }else{
                $logMessage = "No record(s) to generate on the payroll sheet page!";
                $this->core_layout->setEventLog($logMessage, "generate", "success", "payroll", "system");
            }

        return $employees;
    }

    function generatePayrollSheetIncentive($start, $end, $posted_data)
    {
        /***  date range checker ***/
        $bonusCode = isset($posted_data["bonus_code"]) && $posted_data["bonus_code"] ? strtoupper($posted_data["bonus_code"]): null;
        if($bonusCode){ $bonusCode = str_replace("_", " ", $bonusCode); }

        $temp_payDate = strtotime(date("Y-m-d", strtotime($posted_data["pay_date"])));
        $currentYear = date("Y", strtotime($posted_data["pay_date"]));
        
        /*** modified code here ***/
        $previousYear = strtotime($start) >= strtotime($end);
        $tempYear = $previousYear ? date("Y", strtotime("-1 year", strtotime($currentYear))): $currentYear;
        /*** modified code here ***/

        $start = date("{$tempYear}-m-d", strtotime($start));
        $end = date("{$currentYear}-m-d", strtotime($end));
        
        $tempFromDate = strtotime($start);
        $tempToDate = strtotime($end);

        if($tempFromDate > $temp_payDate){
            $tempYear = date("Y", strtotime("-1 year", strtotime($posted_data["pay_date"])));
            $start = date("{$tempYear}-m-d", strtotime($start));
        }

        /***  date range checker ***/

        $employee_ids = isset($posted_data["employees"]) ? $posted_data["employees"] : null;
        $payout_sched = $posted_data["payout_schedule"];
        $company = $this->db->where("id", $posted_data["company"])->get("gcchris.tblcompanies")->row();
        $employees = $this->getEmployees($payout_sched, 'active', $employee_ids, $company);
        $month_name = strtolower(date('F', strtotime($posted_data["pay_date"])));
        
        $year = date('Y', strtotime($start));
        $tempYear0 = date('Y', strtotime($start));
        $tempYear1 = date('Y', strtotime($end));

        $tempMax = max(array($tempYear0, $tempYear1));

        $year = $tempMax ? $tempMax: $year;

        $tempDtStart = $start;
        $tempDtEnd = $end;
        $monthStart = strtolower(date('F', strtotime($tempDtStart)));
        $monthStartNum = date('m', strtotime($tempDtStart));

        $monthEnd = strtolower(date('F', strtotime($tempDtEnd)));
        $monthEndNum = date('m', strtotime($tempDtEnd));

        $alteredStartDate = $tempDtStart;
        $alteredEndDate = $tempDtEnd;
        /*** weekly generate week start and end date
         * 
         * $qStartDate = $this->db->get_where($this->tbl_ps_week_counter, array("year"=>$year, "month_name"=>$monthStart));
        if($qStartDate->num_rows() == 1){
            $qStartRow = $qStartDate->row();
            $tempStartWeeks = @unserialize($qStartRow->weeks);
            if(is_array($tempStartWeeks) && count($tempStartWeeks) > 0){
                $firstWeek = reset($tempStartWeeks);
                $firstDay = reset($firstWeek);
                if($firstDay){
                    $alteredStartDate = date("Y-m-d", strtotime("{$year}-{$monthStartNum}-{$firstDay}"));
                }
            }
        }

        $qEndDate = $this->db->get_where($this->tbl_ps_week_counter, array("year"=>$year, "month_name"=>$monthEnd));
        if($qEndDate->num_rows() == 1){
            $qEndRow = $qEndDate->row();
            $tempEndingWeeks = @unserialize($qEndRow->weeks);
            if(is_array($tempEndingWeeks) && count($tempEndingWeeks) > 0){
                $lastWeek = end($tempEndingWeeks);
                $lastDay = end($lastWeek);
                if($lastDay){
                    $alteredEndDate = date("Y-m-d", strtotime("{$year}-{$monthEndNum}-{$lastDay}"));
                }
            }
        * } 
        *
        weekly generate week start and end date ***/

        $datetime1 = new DateTime($tempDtStart);
        $datetime2 = new DateTime($tempDtEnd);
        $difference = $datetime1->diff($datetime2);

        $days = 0;
        $employees["coverage_date"] = str_replace("-","/", $tempDtStart)." - ".str_replace("-","/", $tempDtEnd);
        foreach ($employees["data"] as $employee) {
            $_currentDateStart = $tempDtStart;
            $_currentDateEnd = $tempDtEnd;

            /*** if(strtolower($employee->payout_sched_name) === "weekly"){
                $_currentDateStart = $alteredStartDate;
                $_currentDateEnd = $alteredEndDate;
            } ***/

            $basicRate = $employee->basic_rate;
            $employee->rate = round(floatval($basicRate), 2);

            $employee->basic_rate = 0;
            $employee->no_of_days = 0;

            $this->db->select("rate, round(sum(basic_rate), 2) as basic_rate, round(sum(no_of_days), 2) as no_of_days, GROUP_CONCAT(DISTINCT id) as group_id");
            $this->db->from($this->tbl_payroll_sheet);
            $this->db->where("emp_id", $employee->id);
            $this->db->where("posted", 1);
            $this->db->where("is_bonus", 0);
            $this->db->group_start();
            $this->db->where("DATE(`date_start`) >=", $_currentDateStart);
            $this->db->where("DATE(`date_end`) <=", $_currentDateEnd);

            /*** start exclude function for bonus
             * $this->db->or_where("DATE(`pay_date`) BETWEEN '{$_currentDateStart}' AND '{$_currentDateEnd}'"); 
             * start exclude function for bonus ***/

            $this->db->group_end();
            $this->db->group_by("emp_id");
            $query = $this->db->get();

            if($query->num_rows() == 1){
                $tempRow = $query->row();
                $tempBasicRate = floatval($tempRow->basic_rate);
                $tempHalfRate = round($tempBasicRate / 12, 2);
                $employee->temp_basic_rate = $tempBasicRate;
                $employee->temp_rate = $tempHalfRate;

                $employee->row_data = $tempRow;
                $employee->ps_id = $tempRow->group_id;
                $employee->basic_rate = floatval($tempRow->basic_rate) > 0 ? round(floatval($tempRow->basic_rate) / 12, 2): 0;
                $employee->no_of_days = $tempRow->no_of_days;
            }

            $data = array(
                "date_start" => $tempDtStart,
                "date_end" => $tempDtEnd,
                "pay_date" => date('Y-m-d', strtotime($posted_data["pay_date"])),
                "month_name" => $month_name,
                "year" => $year,
                "payroll_sched" => $employee->payout_sched,
                "payroll_seq" => 1,
                "emp_id" => $employee->id,
                "company_id" => $employee->company_id,
                "rate" => $employee->rate,
                "payroll_type" => $employee->payroll_type,
                "no_of_days"=>$employee->no_of_days,
                "basic_rate" => $employee->basic_rate,
                "gross_pay" => $employee->basic_rate,
                "net_pay" => $employee->basic_rate,
                "is_bonus"=>1
            );
            if($bonusCode){ $data["bonus_code"] = $bonusCode; }

            $payroll_sheet_id = null;

            $payroll_sheet_exist = $this->db->where(array(
                    "month_name" => $month_name,
                    "year" => $year,
                    "payroll_sched" => $employee->payout_sched,
                    "payroll_seq" => 1,
                    "emp_id" => $employee->id,
                    "is_bonus" => 1,
                ))->get("payroll.payroll_sheet");

            if($payroll_sheet_exist->num_rows() == 0){
                if(is_array($data) && count($data) > 0){ $data["created_at"] = date("Y-m-d H:i:s"); }
                $this->db->insert("payroll.payroll_sheet", $data);
                $payroll_sheet_id = $this->db->insert_id();
            }else{
                $payroll_sheet_row = $payroll_sheet_exist->row();
                if (intval($payroll_sheet_row->posted) === 0) {
                    $gross_pay = $payroll_sheet_row->basic_rate;
                    $net_pay = $payroll_sheet_row->basic_rate;

                    $employee->gross_pay = $gross_pay;
                    $employee->temp_net_pay = $net_pay;

                    $tempGrossDeduction = 0;
                    $custom_adjustments = $this->db->where("payroll_sheet_id", $payroll_sheet_row->id)
                        ->get("payroll.payroll_sheet_custom_adjustments");
                        
                    if($custom_adjustments->num_rows() > 0){
                        foreach ($custom_adjustments->result() as $row) {
                            if (intval($row->cadj_type) === 0) { $tempGrossDeduction += $row->amount; } 
                            else { $gross_pay += $row->amount; }
                        }
                    }

                    $employee->custom_adjustments = $custom_adjustments->result();
                    $employee->temp_gross_deduction = $tempGrossDeduction;
                    
                    if(floatval($tempGrossDeduction) > 0){
                        $tempGrossDeduction = $gross_pay - $tempGrossDeduction;
                        $gross_pay = $tempGrossDeduction;
                    }

                    $net_pay = $gross_pay;
                    $employee->net_pay = $net_pay;

                    $data["gross_pay"] = $gross_pay;
                    $data["net_pay"] = $net_pay;

                    $this->db->where("id", $payroll_sheet_row->id)->update("payroll.payroll_sheet", $data);
                }
                $payroll_sheet_id = $payroll_sheet_row->id;
            }
            
            $employee->payroll_sheet_id = $payroll_sheet_id;
        }

        if(count($employees["data"]) > 0){
            $ctr = count($employees["data"]);
            $tempPayDate = date("F d, Y", strtotime($posted_data["pay_date"]));
            $coverageDateFrom = date("F d, Y", strtotime($tempDtStart));
            $coverageDateTo = date("F d, Y", strtotime($tempDtEnd));

            $logMessage = "A total of {$ctr} employee(s) has been generated in the payroll sheet incentive for the company `{$company->code}` with a pay date `{$tempPayDate}` and a coverage date from `{$coverageDateFrom}` to `{$coverageDateTo}`.";
                $this->core_layout->setEventLog($logMessage, "generate", "success", "payroll");
        }else{
            $logMessage = "No record(s) to generate on the payroll sheet incentive page!";
            $this->core_layout->setEventLog($logMessage, "generate", "success", "payroll", "system");
        }
        return $employees;
    }

    protected function generateIncomeTaxCalculator($temp_taxable_income, $parameters = array()){
        $tempTax = new stdClass();
        $tempTax->ee = 0;
        $tempTax->er = 0;
        $tempTax->total = 0;
        $tempTax->taxable_income = $temp_taxable_income;

        $parameters = (object) $parameters;
        $_contAcctNumber = $parameters->contribution_acct_no;
        $has_sss_no = (isset($_contAcctNumber->sss_no) && $_contAcctNumber->sss_no)? true: false;
        $has_phealth_no = (isset($_contAcctNumber->phealth_no) && $_contAcctNumber->phealth_no)? true: false;
        $has_pagibig_no = (isset($_contAcctNumber->pagibig_no) && $_contAcctNumber->pagibig_no)? true: false;
        $has_tin_no = (isset($_contAcctNumber->tin_no) && $_contAcctNumber->tin_no)? true: false;

        if($temp_taxable_income > 0 && $parameters->switch == "1"){
            $alteredTaxableDeduction = $this->getFixedTaxableDeduction($parameters->emp_id);
            
            $hasPreviousDeduction = new stdClass();
            $hasPreviousDeduction->sss = 0;
            $hasPreviousDeduction->sss_er = 0;
            $hasPreviousDeduction->tax = 0;
            $prev_deduction = $this->db
                    ->select("SUM(sss) as total_sss, SUM(sss_er) as total_sss_er, SUM(tax) as tax_total")
                    ->where("month_name", $parameters->month_name)
                    ->where("year", $parameters->year)
                    ->where("payroll_sched", $parameters->payout_sched)
                    ->where("emp_id", $parameters->emp_id)
                    ->where("posted", 1)
                    ->where("is_bonus", 0)
                    ->group_by("emp_id")
                    ->get("payroll.payroll_sheet");
            if($prev_deduction->num_rows() > 0){
                $hasPreviousDeduction->sss = floatval($prev_deduction->row()->total_sss);
                $hasPreviousDeduction->sss_er = floatval($prev_deduction->row()->total_sss_er);
                $hasPreviousDeduction->tax = floatval($prev_deduction->row()->tax_total);
            }
            $this->db->reset_query();

            $adj_prev_deduction = $this->db
                    ->select("SUM(psa.amount) as tax_total")
                    ->join("payroll.payroll_sheet_created_adjustments as psa", "psa.payroll_sheet_id = ps.id AND psa.particulars = 'TAX' AND psa.adj_type = 1", "INNER")
                    ->where("ps.month_name", $parameters->month_name)
                    ->where("ps.year", $parameters->year)
                    ->where("ps.payroll_sched", $parameters->payout_sched)
                    ->where("ps.emp_id", $parameters->emp_id)
                    ->where("ps.posted", 1)
                    ->where("ps.is_bonus", 0)
                    ->group_by("ps.emp_id")
                    ->get("payroll.payroll_sheet as ps");
            if($adj_prev_deduction->num_rows() > 0){
                $hasPreviousDeduction->tax += floatval($adj_prev_deduction->row()->tax_total);
            }
            $this->db->reset_query();

            $tempHdmf = $this->getHdmf($parameters->remittance_sched_ctr, 1, $temp_taxable_income, $has_pagibig_no);
            $tempPhic = $this->calculatePhilHealth($parameters->remittance_sched_ctr, $temp_taxable_income, 1, $parameters->gross_pay, $has_phealth_no);
            $tempSss = $this->calculateSss(array(
                "gross_pay" => $temp_taxable_income,
                "override" => true,
                "has_sss_no" => $has_sss_no,
            ), $parameters->sss_class);

            $tempDeduction = $tempHdmf->ee + $tempPhic->ee + $tempSss->ee;
            $tempTaxableIncome = $temp_taxable_income - $tempDeduction;
            $tempTax = $this->calculateTax($tempTaxableIncome, $parameters->payroll_type, $parameters->switch, $parameters->gross_pay, $has_tin_no);
            $tempTax->taxable_income = $tempTaxableIncome;
            $tempTax->temp_taxable_income = $temp_taxable_income;
            $tempTax->previous_deduction = $hasPreviousDeduction;
            $tempTax->temp_deduction = $tempDeduction;
            $tempTax->arr_deductions = array("hdmf"=>$tempHdmf->ee, "phic"=>$tempPhic->ee, "sss"=>$tempSss->ee);
            $tempTax->original_tax = array("ee"=>$tempTax->ee, "er"=>$tempTax->er, "total"=>$tempTax->total);
            $tempTax->alter_taxable_deduction = $alteredTaxableDeduction > 0;
            if($alteredTaxableDeduction > 0){ $tempTax->ee = $alteredTaxableDeduction; }

            if(($hasPreviousDeduction->tax == 0 && $parameters->is_last_remittance_schedule === false) ||
            ($hasPreviousDeduction->tax > 0 && $parameters->is_last_remittance_schedule === true)){
                $tempTax->ee = floatval($tempTax->ee);
                $tempTax->er = floatval($tempTax->er);
                $tempTax->total = floatval($tempTax->total);
                
                if ($tempTax->ee > 0) {
                    $halfEe = $tempTax->ee / 2;
                    if ($hasPreviousDeduction->tax > 0 && $hasPreviousDeduction->tax <= $tempTax->ee && $halfEe != $hasPreviousDeduction->tax) {
                        $tempTax->ee = $tempTax->ee - $hasPreviousDeduction->tax;
                    } else {
                        $tempTax->ee = $halfEe;
                    }
                }
                if ($tempTax->er > 0){ $tempTax->er = $tempTax->er / 2; }

                $tempTax->ee = round($tempTax->ee, 2);
                $tempTax->er = round($tempTax->er, 2);
                $tempTax->total = round($tempTax->total, 2);

                $tempTax->total = $tempTax->ee + $tempTax->er;
                $tempTax->total = round($tempTax->total, 2);
            }
        }

        return $tempTax;
    }

    protected function getOvertimeAmountDaily($timesheet=array(), $payrate_setting=array()){
        if(($timesheet && count(get_object_vars($timesheet)) > 0) && ($payrate_setting && count(get_object_vars($payrate_setting)) > 0)){
            $ot_minutely = $timesheet->per_minute * (isset($payrate_setting) ? $payrate_setting->ot_rate : 1);

            $tempTotalOvertimeHours = floatval($timesheet->total_accredited_ot_hrs) + floatval($timesheet->total_accredited_ndiff_ot_hrs);
            $tempNightDiffHours = floatval($timesheet->total_accredited_ndiff_ot_hrs);

            $timesheet->ot_minutely = floatval($tempTotalOvertimeHours) > 0 ? floatval($tempTotalOvertimeHours) * $ot_minutely: 0;
            $timesheet->total_accredited_ot_hrs_amount = ($tempTotalOvertimeHours * 60) * $ot_minutely;
            
            $ot_ndiff_minutely = $timesheet->per_minute * (isset($payrate_setting) ? $payrate_setting->ot_night_diff_rate : 0.1);
            $timesheet->ot_ndiff_minutely = floatval($tempNightDiffHours) > 0 ? floatval($tempNightDiffHours) * $ot_ndiff_minutely: 0;
            $timesheet->total_accredited_ndiff_ot_hrs_amount = ($tempNightDiffHours * 60) * $ot_ndiff_minutely;

            if($tempTotalOvertimeHours >= 1 && ($timesheet->is_rest_day == 1 || $timesheet->has_shift == 0 || $timesheet->is_holiday == 1)){
                $timesheet->total_allowance_ot_hrs_minutes = $tempTotalOvertimeHours * 60;
            }
        }
        return $timesheet;
    }

    protected function getRegularNightDiffAmountDaily($timesheet=array(), $payrate_setting=array()){
        if(($timesheet && count(get_object_vars($timesheet)) > 0) && ($payrate_setting && count(get_object_vars($payrate_setting)) > 0)){
            $night_diff_minutely = $timesheet->per_minute * (isset($payrate_setting) ? $payrate_setting->night_diff_rate : 0.1);
            $timesheet->night_diff_minutely = $night_diff_minutely;
            $totalRegularNightDiffMinutes = floatval($timesheet->total_ndiff_rendered);
            $timesheet->regular_ndiff_minutes = $totalRegularNightDiffMinutes;
            $timesheet->regular_ndiff_amount = $totalRegularNightDiffMinutes * $night_diff_minutely;
        }
        return $timesheet;
    }

    protected function getHolidayAmountDaily($timesheet=array()){
        if($timesheet && count(get_object_vars($timesheet)) > 0 && isset($timesheet->paid_holiday) && intval($timesheet->paid_holiday) == 1){
            $rowPayrate = $this->getPayrateSettingById($timesheet->payrate_id);
            $isPaidHoliday = isset($timesheet->paid_holiday) && intval($timesheet->paid_holiday) === 1;

            $tempHolidayRate = (isset($rowPayrate->regular_rate, $rowPayrate->is_holiday)
                && intval($rowPayrate->is_holiday) == 1 && $rowPayrate->regular_rate
                && intval($rowPayrate->is_holiday) == 1 && $isPaidHoliday === false) ? $rowPayrate->regular_rate: 1;
            /*** $_holiday_minutes = $timesheet->minutely * ($timesheet->minutes_per_day / 60); ***/
            $_holiday_minutes = floatval($timesheet->minutes_per_day);
            $_holiday_amount = $timesheet->minutely_amount * ($timesheet->minutes_per_day / 60);
            $deductUtMinutes = 0;
            $deductUtAmount = 0;

            if($timesheet->is_holiday && ($timesheet->total_accredited_ot_hrs !== null && floatval($timesheet->total_accredited_ot_hrs) > 0)
            || ($timesheet->total_accredited_ndiff_ot_hrs !== null && floatval($timesheet->total_accredited_ndiff_ot_hrs) > 0)){
                $tempPayrate = (isset($timesheet->payrate_regular) && floatval($timesheet->payrate_regular) > 0)? floatval($timesheet->payrate_regular): 0;
                $tempMinutely = (isset($timesheet->minutely) && floatval($timesheet->minutely) > 0)? floatval($timesheet->minutely): 0;
                $holidayMinute = $tempMinutely * $tempPayrate;
                $_temp_holiday_minutes = $holidayMinute * 60;

                $_temp_holiday_amount = $_temp_holiday_minutes * ($timesheet->minutes_per_day / 60);

                $deductUtMinutes = $_temp_holiday_minutes;
                $deductUtAmount = $_temp_holiday_amount;
            }

            $timesheet->holiday_minutely = $_holiday_minutes;
            $timesheet->holiday_amount = $_holiday_amount;

            /*** $timesheet->holiday_gross_minutely = $_holiday_minutes;
            $timesheet->holiday_gross_amount = $_holiday_amount; ***/

            if(floatval($tempHolidayRate) > 1){ $tempHolidayRate = floatval($tempHolidayRate) - 1; }
            $tempMinutely = $timesheet->holiday_minutely * floatval($tempHolidayRate);
            $tempAmount = $timesheet->holiday_amount * floatval($tempHolidayRate);

            $timesheet->holiday_minutely = $tempMinutely;
            $timesheet->holiday_amount = $tempAmount;
            $timesheet->deduct_ut_minutes = $deductUtMinutes;
            $timesheet->deduct_ut_amount = $deductUtAmount;
        }

        return $timesheet;
    }

    protected function getTotalMunitesDaily($ts=array(), $date=null){
        $total_minutes = 0;
        if($ts && count(get_object_vars($ts)) > 0){
            $amMinutes = 0;
            $pmMinutes = 0;
            $date = ($date)? $date: $ts->date;
            $amStart = date("Y-m-d H:i", strtotime($date." ".$ts->shift_am_start));
            $amEnd = date("Y-m-d H:i", strtotime($date." ".$ts->shift_am_end));
            $pmStart = date("Y-m-d H:i", strtotime($date." ".$ts->shift_pm_start));
            $pmEnd = date("Y-m-d H:i", strtotime($date." ".$ts->shift_pm_end));

            /*** altered has next day shift ***/
            $chasNextDayShift = false;
            if(($ts->shift_am_start && $ts->shift_am_end) && strtotime($ts->shift_am_end) < strtotime($ts->shift_am_start)){
                $c_shiftDate_00 = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $amEnd = date("Y-m-d H:i", strtotime($c_shiftDate_00." ".$ts->shift_am_end));
                $chasNextDayShift = true;
            }

            if(($ts->shift_am_end && $ts->shift_pm_start) && (strtotime($ts->shift_pm_start) < strtotime($ts->shift_am_end) || $chasNextDayShift)){
                $c_shiftDate_01 = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $pmStart = date("Y-m-d H:i", strtotime($c_shiftDate_01." ".$ts->shift_pm_start));
                $chasNextDayShift = true;
            }

            if(($ts->shift_pm_start && $ts->shift_pm_end) && (strtotime($ts->shift_pm_end) < strtotime($ts->shift_pm_start) || $chasNextDayShift)){
                $c_shiftDate_02 = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $pmEnd = date("Y-m-d H:i", strtotime($c_shiftDate_02." ".$ts->shift_pm_end));
            }
            /*** altered has next day shift ***/

            $dateAmStart = new DateTime($amStart);
            $dateAmEnd = new DateTime($amEnd);

            $datePmStart = new DateTime($pmStart);
            $datePmEnd = new DateTime($pmEnd);

            $tempASH = $dateAmStart->format('H');
            $tempASM = $dateAmStart->format('i');
            $tempAEH = $dateAmEnd->format('H');
            $tempAEM = $dateAmEnd->format('i');

            $tempPSH = $datePmStart->format('H');
            $tempPSM = $datePmStart->format('i');
            $tempPEH = $datePmEnd->format('H');
            $tempPEM = $datePmEnd->format('i');

            if(intval($tempASH) > 0 && intval($tempAEH) > 0){
                $amMinutes = intval($tempAEH) - intval($tempASH);
                $amMinutes = $amMinutes * 60;
                if(intval($tempASM) > 0 && intval($tempAEM) > 0){
                    /*** altered computation ***/
                    $tempASEM = intval($tempASM) + intval($tempAEM);
                    $remainderASEM = $tempASEM % 60;
                    if($remainderASEM > 0){ $amMinutes = $amMinutes + intval($remainderASEM) - 60; }
                    /*** altered computation ***/
                    /*** $amMinutes = $amMinutes + (intval($tempASM) + intval($tempAEM)); ***/
                }
            }

            if(intval($tempPSH) > 0 && intval($tempPEH) > 0){
                $pmMinutes = intval($tempPEH) - intval($tempPSH);
                $pmMinutes = $pmMinutes * 60;
                if(intval($tempPSM) > 0 && intval($tempPEM) > 0){
                    /*** altered computation ***/
                    $tempPSEM = intval($tempPEM) + intval($tempPSM);
                    $remainderPSEM = $tempPSEM % 60;
                    if($remainderPSEM > 0){ $pmMinutes = $pmMinutes + intval($remainderPSEM) - 60; }
                    /*** altered computation ***/
                    /*** $pmMinutes = $pmMinutes + (intval($tempPEM) + intval($tempPSM)); ***/
                }
            }

            $totalTempMinutes = $amMinutes + $pmMinutes;
            $total_minutes = $totalTempMinutes;
        }

        return $total_minutes;
    }

    function getTotalUnrenderedMinutes($schedules_obj=array(), $tempAlteredDates=array(), $tempDates=array()){
        $arrData = array();
        $total_minutes = 0;
        $absent_days = 0;
        $unpaid_holiday = array();
        if($tempDates && count($tempDates) > 0){
            foreach ($tempDates as $key => $tempDatex) {
                $dtx = new DateTime($tempDatex);
                $weekday = strtolower($dtx->format("l"));
                $tempDate = $dtx->format("Y-m-d");

                /*** holiday absent tracker ***/
                $holidayResponse = (object) $this->ts_model->getCurrentDateIsHoliday($tempDate);
                $isHoliday = $holidayResponse->is_holiday;
                /*** holiday absent tracker ***/
                
                $md5Date = md5($tempDate);
                if(isset($schedules_obj[$weekday]) && $schedules_obj[$weekday]){
                    $_schedule = $schedules_obj[$weekday];
                    if($_schedule && count(get_object_vars($_schedule)) > 0){
                        $amMinutes = 0;
                        $pmMinutes = 0;

                        $amStart = date("Y-m-d H:i", strtotime($tempDate." ".$_schedule->am_start));
                        $amEnd = date("Y-m-d H:i", strtotime($tempDate." ".$_schedule->am_end));
                        $pmStart = date("Y-m-d H:i", strtotime($tempDate." ".$_schedule->pm_start));
                        $pmEnd = date("Y-m-d H:i", strtotime($tempDate." ".$_schedule->pm_end));

                        /*** altered has next day shift ***/
                        $hasNextDayShift = false;
                        if(($_schedule->am_start && $_schedule->am_end) && strtotime($_schedule->am_end) < strtotime($_schedule->am_start)){
                            $_shiftDate_00 = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                            $amEnd = date("Y-m-d H:i", strtotime($_shiftDate_00." ".$_schedule->am_end));
                            $hasNextDayShift = true;
                        }

                        if(($_schedule->am_end && $_schedule->pm_start) && (strtotime($_schedule->pm_start) < strtotime($_schedule->am_end) || $hasNextDayShift)){
                            $_shiftDate_01 = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                            $pmStart = date("Y-m-d H:i", strtotime($_shiftDate_01." ".$_schedule->pm_start));
                            $hasNextDayShift = true;
                        }

                        if(($_schedule->pm_start && $_schedule->pm_end) && (strtotime($_schedule->pm_end) < strtotime($_schedule->pm_start) || $hasNextDayShift)){
                            $_shiftDate_02 = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                            $pmEnd = date("Y-m-d H:i", strtotime($_shiftDate_02." ".$_schedule->pm_end));
                        }
                        /*** altered has next day shift ***/

                        /*** altered shift schedule ***/
                        if(isset($tempAlteredDates->$md5Date) && $tempAlteredDates->$md5Date){
                            if($tempAlteredDates->$md5Date->altered_shift == true){
                                $tempShiftSchedule = $tempAlteredDates->$md5Date->shift_schedule;
                                $amStart = date("Y-m-d H:i", strtotime($tempDate." ".$tempShiftSchedule->am_start));
                                $amEnd = date("Y-m-d H:i", strtotime($tempDate." ".$tempShiftSchedule->am_end));
                                $pmStart = date("Y-m-d H:i", strtotime($tempDate." ".$tempShiftSchedule->pm_start));
                                $pmEnd = date("Y-m-d H:i", strtotime($tempDate." ".$tempShiftSchedule->pm_end));
                                /*** altered has next day shift ***/
                                $chasNextDayShift = false;
                                if(($tempShiftSchedule->am_start && $tempShiftSchedule->am_end) && strtotime($tempShiftSchedule->am_end) < strtotime($tempShiftSchedule->am_start)){
                                    $c_shiftDate_00 = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                                    $amEnd = date("Y-m-d H:i", strtotime($c_shiftDate_00." ".$tempShiftSchedule->am_end));
                                    $chasNextDayShift = true;
                                }
        
                                if(($tempShiftSchedule->am_end && $tempShiftSchedule->pm_start) && (strtotime($tempShiftSchedule->pm_start) < strtotime($tempShiftSchedule->am_end) || $chasNextDayShift)){
                                    $c_shiftDate_01 = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                                    $pmStart = date("Y-m-d H:i", strtotime($c_shiftDate_01." ".$tempShiftSchedule->pm_start));
                                    $chasNextDayShift = true;
                                }
        
                                if(($tempShiftSchedule->pm_start && $tempShiftSchedule->pm_end) && (strtotime($tempShiftSchedule->pm_end) < strtotime($tempShiftSchedule->pm_start) || $chasNextDayShift)){
                                    $c_shiftDate_02 = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                                    $pmEnd = date("Y-m-d H:i", strtotime($c_shiftDate_02." ".$tempShiftSchedule->pm_end));
                                }
                                /*** altered has next day shift ***/
                            }
                        }

                        /*** altered shift schedule ***/

                        $dateAmStart = new DateTime($amStart);
                        $dateAmEnd = new DateTime($amEnd);

                        $datePmStart = new DateTime($pmStart);
                        $datePmEnd = new DateTime($pmEnd);

                        $tempASH = $dateAmStart->format('H');
                        $tempASM = $dateAmStart->format('i');
                        $tempAEH = $dateAmEnd->format('H');
                        $tempAEM = $dateAmEnd->format('i');

                        $tempPSH = $datePmStart->format('H');
                        $tempPSM = $datePmStart->format('i');
                        $tempPEH = $datePmEnd->format('H');
                        $tempPEM = $datePmEnd->format('i');

                        if(intval($tempASH) > 0 && intval($tempAEH) > 0){
                            $amMinutes = intval($tempAEH) - intval($tempASH);
                            $amMinutes = $amMinutes * 60;
                            if(intval($tempASM) > 0 && intval($tempAEM) > 0){
                                /*** altered computation ***/
                                $tempASEM = intval($tempASM) + intval($tempAEM);
                                $remainderASEM = $tempASEM % 60;
                                if($remainderASEM > 0){ $amMinutes = $amMinutes + intval($remainderASEM) - 60; }
                                /*** altered computation ***/
                                /*** $amMinutes = $amMinutes + (intval($tempASM) + intval($tempAEM)); ***/
                            }
                        }

                        if(intval($tempPSH) > 0 && intval($tempPEH) > 0){
                            $pmMinutes = intval($tempPEH) - intval($tempPSH);
                            $pmMinutes = $pmMinutes * 60;
                            if(intval($tempPSM) > 0 && intval($tempPEM) > 0){
                                /*** altered computation ***/
                                $tempPSEM = intval($tempPSM) + intval($tempPEM);
                                $remainderPSEM = $tempPSEM % 60;
                                if($remainderPSEM > 0){ $pmMinutes = $pmMinutes + intval($remainderPSEM) - 60; }
                                /*** altered computation ***/
                                /*** $pmMinutes = $pmMinutes + (intval($tempPEM) + intval($tempPSM)); ***/
                            }
                        }
                        
                        /*** altered negative checker ***/
                        if($amMinutes != 0 && $amMinutes < 0 && ($amStart && $amEnd)){
                            $tempAmRecord = strtotime($amEnd) - strtotime($amStart);
                            $amMinutes = $tempAmRecord / 60;
                        }
                        
                        if($pmMinutes != 0 && $pmMinutes < 0 && ($pmStart && $pmEnd)){
                            $tempPmRecord = strtotime($pmEnd) - strtotime($pmStart);
                            $pmMinutes = $tempPmRecord / 60;
                        }
                        /*** altered negative checker ***/

                        $total_row_minutes = $amMinutes + $pmMinutes;
                        
                        /*** unpaid holiday absent static 480 minutes ***/
                        $actualTotalRowMinutes = $total_row_minutes;
                        if($isHoliday && $total_row_minutes > 480){ $total_row_minutes = 480;  }
                        if($isHoliday){
                            $unpaid_holiday[] = array("date"=>$tempDate, "total_minutes"=>$total_row_minutes, "actual_minutes"=>$actualTotalRowMinutes);
                        }
                        /*** unpaid holiday absent static 480 minutes ***/

                        $total_minutes += $total_row_minutes;
                        if(floatval($total_row_minutes) >= 480){ 
                            $absent_days++; 
                        }
                    }
                }
            }
        }

        $arrData["total_minutes"] = $total_minutes;
        $arrData["absent_days"] = $absent_days;
        $arrData["unpaid_holiday"] = $unpaid_holiday;
        return $arrData;
    }

    private function getPayrateSetting($particular)
    {
        return $this->db->where("particulars", $particular)->get("payroll.payrate_settings")->row();
    }

    private function getPayrateSettingById($id=null)
    {
        return $this->db->where("id", $id)->get("payroll.payrate_settings")->row();
    }

    function getPayrollSheet($start, $end, $show_posted="_all")
    {
        $result = array();
        $posted_data = $this->input->post();
        $paydate = date("Y-m-d", strtotime($posted_data["pay_date"]));
        $employee_ids = isset($posted_data["employees"]) ? $posted_data["employees"] : null;
        $payout_sched = $posted_data["payout_schedule"];
        $payout_sequence = isset($posted_data["payout_sequence"]) && $posted_data["payout_sequence"] ? $posted_data["payout_sequence"]: null;

        $settings = $this->getSettings();
        
        $dir = "ASC";
        $order = "emp.lastname, emp.firstname";
        $company = $this->db->where("id", $posted_data["company"])->get("gcchris.tblcompanies")->row();

        if (!empty($employee_ids)) {
            $this->db->where_in("emp.id", $employee_ids);
        }

        $sqlSelect = "ps.*, emp.lastname, emp.firstname, emp.middlename, emp.suffix, emp.idno, IF(position.name IS NULL, emp.position, position.name) as position,
            GROUP_CONCAT(DISTINCT(CONCAT(custom_adjustments.particulars,'||',custom_adjustments.amount, '||', custom_adjustments.cadj_type))) custom_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(created_adjustments.particulars,'||',created_adjustments.amount, '||', created_adjustments.adj_type, '||', created_adjustments.status))) created_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(ps_loan.code,'||',psl_payment.amount_due, '||', ps_loan.loan_class))) sss_hdmf_loan_deduction, 
            IFNULL(ps_allowance.rate, 0) as allowance_rate";

        $this->db->select($sqlSelect);
        /*** updated filtering by employee ids ***/
        if (empty($employee_ids)) {
            $this->db->where("ps.date_start", $start);
            $this->db->where("ps.date_end", $end);
        }
        /*** updated filtering by employee ids ***/
        $this->db->where("ps.pay_date", $paydate);
        $this->db->where("ps.payroll_sched", $payout_sched);
        
        if($payout_sequence){
            $this->db->where("ps.payroll_seq", $payout_sequence);
        }
        /*** show all generated ***/
        if(isset($settings->enable_zero_netpay) && intval($settings->enable_zero_netpay->setting_value) == 0){ $this->db->where("ps.no_of_days !=", 0);  }
        /*** show all generated ***/

        /*** start bonus filter ***/
        $this->db->where("ps.is_bonus", 0);
        $this->db->where("ps.ewd !=", 0);
        /*** end bonus filter ***/
        
        $isPosted = null;
        switch ($show_posted) {
            case 'posted': $isPosted = 1; break;
            case 'unposted': $isPosted = 0; break;
            default: $isPosted = null; break;
        }

        if(is_null($isPosted) == false && is_numeric($isPosted)){
            $this->db->where("ps.posted", $isPosted);
        }

        if (!empty($company)) {
            $this->db->where("ps.company_id", $company->id);
        }
        /*** if (!empty($company)) {
            $this->db->group_start();
            $this->db->where_in("company_id", array($company->id, $company->description, $company->code));
            $this->db->group_end();
        }     ***/   

        $this->db->join("gccmaster.tblemployees emp", "emp.id = ps.emp_id", "INNER");
        $this->db->join("payroll.payroll_sheet_custom_adjustments custom_adjustments", "custom_adjustments.payroll_sheet_id = ps.id", "LEFT");
        $this->db->join("payroll.payroll_sheet_created_adjustments created_adjustments", "created_adjustments.payroll_sheet_id = ps.id", "LEFT");
        $this->db->join("payroll.payroll_sheet_loan_payments psl_payment", "psl_payment.payroll_sheet_id = ps.id", "LEFT");
        $this->db->join("gcchris.loans hr_loans", "hr_loans.id = psl_payment.loan_id", "LEFT");
        $this->db->join("payroll.loans ps_loan", "ps_loan.id = hr_loans.loan_id", "LEFT");
        $this->db->join("payroll.payroll_sheet_loan_interest_payments psli_payment", "psli_payment.payroll_sheet_id = ps.id AND psli_payment.is_active = 1", "LEFT");
        $this->db->join("gcchris.loans hri_loans", "hri_loans.id = psli_payment.loan_id", "LEFT");
        $this->db->join("payroll.loans psi_loan", "psi_loan.id = hri_loans.loan_id AND psi_loan.loan_class = 0", "LEFT");
        $this->db->join("gcchris.allowances ps_allowance", "ps_allowance.emp_id = emp.id AND ps_allowance.is_active = 1 AND ps_allowance.is_archived = 0", "LEFT");
        $this->db->join("gcchris.tblposition position", "position.id = emp.position", "LEFT");
        $this->db->group_by("ps.id");
        $this->db->order_by($order, $dir);
        $query = $this->db->get("payroll.payroll_sheet ps");
        $last_query = $this->db->last_query();

        if ($query->num_rows() > 0) {
            foreach( $query->result() as $row ) {
                $max_date = $this->getPayrollMaxDate($row->emp_id);
                $row->has_latest_payroll = date('Y-m-d', strtotime($max_date)) > date('Y-m-d', strtotime($row->date_end)) ? 1 : 0;
                $result[] = $row;
            }
        }

        return array(
            "data" => $result,
            "sql" => $last_query,
        );
    }

    function getPayrollSheetIncentive($start, $end, $show_posted="_all")
    {
        $posted_data = $this->input->post();
        $paydate = date("Y-m-d", strtotime($posted_data["pay_date"]));

        /***  date range checker ***/
        $temp_payDate = strtotime(date("Y-m-d", strtotime($posted_data["pay_date"])));
        $currentYear = date("Y", strtotime($posted_data["pay_date"]));
        
        /*** modified code here ***/
        $previousYear = strtotime($start) >= strtotime($end);
        $tempYear = $previousYear ? date("Y", strtotime("-1 year", strtotime($currentYear))): $currentYear;
        /*** modified code here ***/

        $start = date("{$tempYear}-m-d", strtotime($start));
        $end = date("{$currentYear}-m-d", strtotime($end));
        
        $tempFromDate = strtotime($start);

        if($tempFromDate > $temp_payDate){
            $tempYear = date("Y", strtotime("-1 year", strtotime($posted_data["pay_date"])));
            $start = date("{$tempYear}-m-d", strtotime($start));
        }
        /***  date range checker ***/

        $employee_ids = isset($posted_data["employees"]) ? $posted_data["employees"] : null;
        $payout_sched = $posted_data["payout_schedule"];
        $dir = "ASC";
        $order = "emp.lastname, emp.firstname";
        $company = $this->db->where("id", $posted_data["company"])->get("gcchris.tblcompanies")->row();

        if (!empty($employee_ids)) {
            $this->db->where_in("emp.id", $employee_ids);
        }

        $sqlSelect = "ps.*, emp.lastname, emp.firstname, emp.middlename, emp.suffix, emp.idno, IF(position.name IS NULL, emp.position, position.name) as position,
            GROUP_CONCAT(DISTINCT(CONCAT(custom_adjustments.particulars,'||',custom_adjustments.amount, '||', custom_adjustments.cadj_type))) custom_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(created_adjustments.particulars,'||',created_adjustments.amount, '||', created_adjustments.adj_type, '||', created_adjustments.status))) created_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(ps_loan.code,'||',psl_payment.amount_due, '||', ps_loan.loan_class))) sss_hdmf_loan_deduction, IFNULL(ps_allowance.rate, 0) as allowance_rate";

        $this->db->select($sqlSelect);
        /*** updated filtering by employee ids ***/
        if (empty($employee_ids)) {
            $this->db->where("ps.date_start", $start);
            $this->db->where("ps.date_end", $end);
        }
        /*** updated filtering by employee ids ***/
        $this->db->where("ps.pay_date", $paydate);
        $this->db->where("ps.payroll_sched", $payout_sched);

        /*** show all generated ***/
        if(isset($settings->enable_zero_netpay) && intval($settings->enable_zero_netpay->setting_value) == 0){ $this->db->where("ps.no_of_days !=", 0);  }
        /*** $this->db->where("ps.no_of_days !=", 0); ***/
        /*** show all generated ***/

        /*** start bonus filter ***/
        $this->db->where("ps.ewd", 0);
        $this->db->where("ps.is_bonus", 1);
        /*** end bonus filter ***/

        $isPosted = null;
        switch ($show_posted) {
            case 'posted': $isPosted = 1; break;
            case 'unposted': $isPosted = 0; break;
            default: $isPosted = null; break;
        }

        if(is_null($isPosted) == false && is_numeric($isPosted)){
            $this->db->where("ps.posted", $isPosted);
        }

        if (!empty($company)) {
            $this->db->where("ps.company_id", $company->id);
        }

        $this->db->join("gccmaster.tblemployees emp", "emp.id = ps.emp_id", "INNER");
        $this->db->join("payroll.payroll_sheet_custom_adjustments custom_adjustments", "custom_adjustments.payroll_sheet_id = ps.id", "LEFT");
        $this->db->join("payroll.payroll_sheet_created_adjustments created_adjustments", "created_adjustments.payroll_sheet_id = ps.id", "LEFT");
        $this->db->join("payroll.payroll_sheet_loan_payments psl_payment", "psl_payment.payroll_sheet_id = ps.id", "LEFT");
        $this->db->join("gcchris.loans hr_loans", "hr_loans.id = psl_payment.loan_id", "LEFT");
        $this->db->join("payroll.loans ps_loan", "ps_loan.id = hr_loans.loan_id", "LEFT");
        $this->db->join("gcchris.allowances ps_allowance", "ps_allowance.emp_id = emp.id AND ps_allowance.is_active = 1 AND ps_allowance.is_archived = 0", "LEFT");
        $this->db->join("gcchris.tblposition position", "position.id = emp.position", "LEFT");
        $this->db->group_by("ps.id");
        $this->db->order_by($order, $dir);
        $query = $this->db->get("payroll.payroll_sheet ps");
        return array(
            "data" => $query->result(),
            "sql" => $this->db->last_query(),
        );
    }

    function generatePayrollSummary(){
        $resultset = array();
        $post = $this->input->post();
        $employee_ids = isset($post["employees"]) ? $post["employees"] : array();
        if(isset($post) && $post){
            $tempGroup = "PAY DATE";
            $arrGroup = array(1=>"PAY DATE", 2=>"MONTH", 3=>"YEAR", 4=>"INCENTIVE");
            $group = (isset($post["group"]) && $post["group"])? intval($post["group"]): 1;

            /*** $includedBonus = false; ***/
            $arrCompany = array();
            $employeeIds = array();
            $isWeeklyEmployees = array();

            $empCollectionIds = array();

            $tempStartDate = null;
            $tempEndDate = null;
            $hasDataFilter = false;
            $tempGroup = $arrGroup[$group];
            $tempArrFilter = array();
            $tempArrFilter["filter_by"] = $tempGroup;
            $isMonthlyFilter = false;
            
            /*** altered code section start ***/
            $isMonthFilter = isset($post["filter_month"], $post["filter_year"]) || strtolower($tempGroup) === "month";
            /*** altered code section end ***/

            if(isset($post["thirteenth_month"])){
                $tempArrFilter["bonus"] = $post["thirteenth_month"];
            }else{
                $tempArrFilter["bonus"] = "off";
            }
            if(isset($post["company"]) && is_array($post["company"]) && count($post["company"]) > 0){
                $this->db->select("description");
                $this->db->from("gcchris.tblcompanies");
                $this->db->where_in("id", $post["company"]);
                $qTemp = $this->db->get();
                if($qTemp->num_rows() > 0){
                    foreach ($qTemp->result() as $key => $value) {
                        if($value->description){
                            $arrCompany[] = strtoupper($value->description);
                        }
                    }
                    $tempArrFilter["companies"] = $arrCompany;
                }
            }

            if(isset($post["payroll_group"]) && is_array($post["payroll_group"]) && count($post["payroll_group"]) > 0){
                $this->db->select("employee_id");
                $this->db->from($this->tbl_payroll_group);
                $this->db->where("status", 1);
                $this->db->where("is_archived", 0);
                $this->db->where_in("id", $post["payroll_group"]);
                $psGroup = $this->db->get();
                if($psGroup->num_rows() > 0){
                    foreach ($psGroup->result() as $key => $value) {
                        $employees = @unserialize($value->employee_id);
                        foreach ($employees as $kk => $id) {
                            if(is_array($employee_ids) && !in_array($id, $employee_ids)){
                                $employee_ids[] = $id;
                            }
                        }
                    }
                }
                $empCollectionIds = array_unique($employee_ids);
            }
            
            $this->db->select("a.id");
            $this->db->from("gccmaster.tblemployees a");
            if(isset($employee_ids) && $employee_ids){
                $hasDataFilter = true;
                $this->db->where_in("a.id", $employee_ids);
            }
            
            $this->db->group_by("a.id");
            $queryTemp = $this->db->get();
            if($queryTemp->num_rows() > 0 && $hasDataFilter == true){
                foreach ($queryTemp->result() as $key => $value) {
                    if(!in_array($value->id, $empCollectionIds)){ $empCollectionIds[] = $value->id; }
                }
            }

            $this->db->select("a.id, c.name as payout_schedule_name");
            $this->db->from("gccmaster.tblemployees a");
            /*** updated function for employees with interchanging companies ***/
            $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = a.id", "INNER");
            $this->db->join("gcchris.tblcompanies b", "b.id = ps.company_id", "LEFT");
            /*** updated function for employees with interchanging companies ***/
            /*** $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id OR b.description = a.company_id OR b.code = a.company_id", "LEFT"); ***/
            $this->db->join("payroll.payout_schedule c", "c.id = a.payout_sched", "INNER");
            $this->db->where("b.id", $post["company"]);
            if(isset($empCollectionIds) && $empCollectionIds){
                $this->db->where_in("a.id", $empCollectionIds);
            }
            $this->db->group_by("a.id");
            $queryTemp = $this->db->get();
            if($queryTemp->num_rows() > 0){
                foreach ($queryTemp->result() as $key => $value) {
                    /*** altered code section start ***/
                    $tempPayoutSchedule = strtolower($value->payout_schedule_name);
                    if($isMonthFilter){
                        $monthIndex = $post["filter_month"];
                        $tempYear = $post["filter_year"];
                        $monthName = date('F', mktime(0, 0, 0, $monthIndex, 1, $tempYear));
                        $monthName = strtolower($monthName);
                        
                        $this->db->select("GROUP_CONCAT(payroll_sched) as payroll_schedule");
                        $this->db->order_by("id", "ASC");
                        $tempData = $this->db->get_where("payroll.payroll_sheet", 
                            array("posted"=>1, "month_name"=>$monthName, "year"=>$tempYear, 
                            "company_id"=>$post["company"], "emp_id"=>$value->id));
                        
                        $this->db->reset_query();
                        if($tempData->num_rows() == 1){
                            $payrollSchedule = explode(",", $tempData->row()->payroll_schedule);
                            $arrCountValues = array_count_values($payrollSchedule);
                            $tempArrValue = array_keys($arrCountValues, max($arrCountValues));
                            if(count($tempArrValue) == 1){
                                $payoutSchedule = reset($tempArrValue);
                                if($payoutSchedule){
                                    $qTempRow = $this->db->get_where("payroll.payout_schedule", array("id"=>$payoutSchedule));
                                    $this->db->reset_query();

                                    if($qTempRow->num_rows() == 1){
                                        $tempPayoutSchedule = strtolower($qTempRow->row()->name);
                                    }
                                }
                            }

                        }
                    }
                    
                    if($value->payout_schedule_name !== $tempPayoutSchedule){ $value->payout_schedule_name = $tempPayoutSchedule; }
                    /*** altered code section end ***/

                    if(strtolower($value->payout_schedule_name) !== "weekly" 
                        && !in_array($value->id, $employeeIds)){
                        $employeeIds[] = $value->id;
                    }else if(strtolower($value->payout_schedule_name) === "weekly" 
                        && !in_array($value->id, $isWeeklyEmployees)){
                        $isWeeklyEmployees[] = $value->id;
                    }
                }
            }

            if(isset($post["date_range"]) && $post["date_range"]){
                $dates = explode("-", $post["date_range"]);
                if(is_array($dates) && count($dates) == 2){
                    foreach ($dates as $key => $date) {
                        $tempDate = date("Y-m-d", strtotime(trim($date)));
                        $dates[$key] = $tempDate;
                    }
                    $tempStartDate = $dates[0];
                    $tempEndDate = $dates[1];
                }
                if(isset($post["filter_incentive"]) && $post["filter_incentive"] && $tempStartDate > $tempEndDate && !isset($post['incentive_year'])){
                    $tempStartDate = date("Y-m-d", strtotime("-1 year", strtotime($tempStartDate)));
                    /*** $includedBonus = true; ***/
                }else if(isset($post["filter_incentive"]) && $post["filter_incentive"] && isset($post['incentive_year'])){
                    $tempStartDate = date($post['incentive_year']."-m-d", strtotime($tempStartDate));
                    $tempEndDate = date($post['incentive_year']."-m-d", strtotime($tempEndDate));

                    if(strtotime($tempStartDate) > strtotime($tempEndDate)){ $tempStartDate = date("Y-m-d", strtotime("-1 year", strtotime($tempStartDate))); }
                    /*** $includedBonus = true; ***/
                }else{
                    $tempStartDate = date("Y-m-d", strtotime($tempStartDate));
                    $tempEndDate = date("Y-m-d", strtotime($tempEndDate));
                    /*** $includedBonus = false; ***/
                }
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("{$post['filter_year']}-01-01", strtotime("{$post["filter_year"]}"));
                $tempEndDate = date("{$post['filter_year']}-12-31", strtotime("{$post["filter_year"]}"));
            }

            if($tempStartDate && $tempEndDate){
                $xDateFrom = date("F d, Y", strtotime($tempStartDate));
                $xDateTo = date("F d, Y", strtotime($tempEndDate));
                $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");

                $weeklyPsIds = array();
                $employeePsIds = array();

                if(is_array($isWeeklyEmployees) && count($isWeeklyEmployees) > 0){
                    $responseWeeklyRange = $this->reports->generateWeeklyMonthRange($tempStartDate, $tempEndDate);
                    if($responseWeeklyRange){
                        /*** payroll incentive fix ***/
                        /*** 
                         * $_tempStartDate = $responseWeeklyRange["date_start"];
                         * $_tempEndDate = $responseWeeklyRange["date_end"];
                         * $_tempPayDateStart = $responseWeeklyRange["paydate_start"];
                         * $_tempPayDateEnd = $responseWeeklyRange["paydate_end"]; 
                        ***/
                        /*** payroll incentive fix ***/

                        $this->db->select("id");
                        $this->db->from("payroll.payroll_sheet");
                        $this->db->where("posted", 1);
                        $this->db->where("is_bonus", 0);
                        /*** if($includedBonus === false){ $this->db->where("is_bonus", 0); } ***/
                        $this->db->group_start();
                        /*** payroll incentive fix ***/
                        /*** $this->db->where("DATE(date_start) >=", $_tempStartDate);
                        $this->db->where("DATE(date_end) <=", $_tempEndDate); ***/
                        /*** payroll incentive fix ***/
                        $this->db->where("DATE(date_start) >=", $tempStartDate);
                        $this->db->where("DATE(date_end) <=", $tempEndDate);
                        $this->db->group_end();
                        $this->db->where_in("emp_id", $isWeeklyEmployees);
                        if(isset($post["company"]) && $post["company"]){
                            $this->db->where("company_id", $post["company"]);
                        }
                        $this->db->order_by("emp_id", "ASC");
                        $queryWeekly = $this->db->get();
                        if($queryWeekly->num_rows() > 0){
                            foreach ($queryWeekly->result() as $key => $value) {
                                if(!in_array($value->id, $weeklyPsIds)){
                                    $weeklyPsIds[] = $value->id;
                                }
                            }
                        }
                    }
                }

                if(is_array($employeeIds) && count($employeeIds) > 0){
                    $this->db->select("id");
                    $this->db->from("payroll.payroll_sheet");
                    $this->db->where("posted", 1);
                    $this->db->where("is_bonus", 0);
                    /*** if($includedBonus === false){ $this->db->where("is_bonus", 0); } ***/
                    $this->db->group_start();
                    $this->db->where("DATE(date_start) >=", $tempStartDate);
                    $this->db->where("DATE(date_end) <=", $tempEndDate);
                    /*** payroll incentive fix ***/
                    /*** $this->db->or_where("DATE(pay_date) >=", $tempStartDate);
                    $this->db->where("DATE(pay_date) <=", $tempEndDate); ***/
                    /*** payroll incentive fix ***/
                    $this->db->group_end();
                    $this->db->where_in("emp_id", $employeeIds);
                    if(isset($post["company"]) && $post["company"]){
                        $this->db->where("company_id", $post["company"]);
                    }
    
                    $query = $this->db->get();
                    $tempSql = $this->db->last_query();
                    if($query->num_rows() > 0){
                        foreach ($query->result() as $key => $value) {
                            if(!in_array($value->id, $employeePsIds)){
                                $employeePsIds[] = $value->id;
                            }
                        }
                    }
                }

                if((is_array($employeePsIds) && count($employeePsIds) > 0) || (is_array($weeklyPsIds) && count($weeklyPsIds) > 0)){
                    $employeePsIds = array_unique(array_merge($employeePsIds, $weeklyPsIds));
                    if(is_array($employeePsIds) && count($employeePsIds) > 0){
                        $employeePsIds = array_map("intval", $employeePsIds);
                        $resultset["response"] = true;
                        $resultset["data"] = $employeePsIds;
                        $resultset["is_weekly_employees"] = $isWeeklyEmployees;
                        $resultset["filters"] = $tempArrFilter;
                        $resultset["toastr_msg"] = "Posted payroll entries found.";
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "No payroll entries available!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Filter search, no data found!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No filtered dates!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }
        // return $this->db->last_query();
        return $resultset;
    }

    private function getEmployeeLoans($emp_id, $gross_pay, $status=0, $remainingGrossPay=0, $zeroNet=false)
    {
        $this->db->select("a.*, pl.loan_type, pl.code, pl.loan_class");
        $this->db->join("payroll.loans as pl", "pl.id = a.loan_id");
        $this->db->where("a.emp_id", $emp_id);
        $this->db->where("a.paid", $status);
        $this->db->where("a.is_archived", 0);
        $this->db->order_by("pl.loan_type", "ASC");
        /*** $this->db->where("a.active", 1); ***/
        $loans = $this->db->get("gcchris.loans a")->result();

        foreach ($loans as $loan) {
            $total_amount_paid = $this->db
                ->select_sum("psloanpayments.amount_due")
                ->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id")
                ->where("ps.posted", 1)
                ->where("emp_id", $emp_id)
                ->where("loan_id", $loan->id)
                ->get("payroll.payroll_sheet_loan_payments psloanpayments")
                ->row("amount_due");

            $loan->total_amount_paid = round($total_amount_paid, 2);
            $balance = floatval(round($loan->amount,2)) - floatval(round($total_amount_paid, 2));
            $loan->amount_due = $balance > 0 ? $balance : 0;
            $loan->interest_amount = 0;
            $loan->zero_netpay = 0;
            $toDeduct = false;
            
            
            if($balance > 0 /*** && $remainingGrossPay >= $balance ***/){
                if (intval($loan->deduction_type) === 0) {
                    $percentage = $loan->percentage / 100;
                    $amount_due = $gross_pay * $percentage;
                    if(doubleval($balance) > doubleval($amount_due)){ $toDeduct = true; }

                    $amount_due = doubleval($balance) > doubleval($amount_due) ? $amount_due : $balance;
                    $loan->amount_due = $amount_due;

                } else {
                    if(doubleval($balance) > doubleval($loan->fixed_deduction_amt)){ $toDeduct = true; }
                    $amount_due = doubleval($balance) > doubleval($loan->fixed_deduction_amt) ? $loan->fixed_deduction_amt : $balance;
                    $loan->amount_due = floatval($amount_due);
                }

                if (floatval($loan->interest_percentage) > 0) {
                    $intPercentage = $loan->interest_percentage / 100;
                    $amountToDeduct = floatval($loan->amount) * $intPercentage;
                    $loan->interest_amount = $amountToDeduct;
                }

                /*** $remainingGrossPay = $remainingGrossPay - $balance; ***/
            }
            /*** else if($zeroNet && $balance > 0 && $balance > $remainingGrossPay){
                $loan->zero_netpay = 1;
            } ***/

           $nBalance = floatval($loan->amount_due);
            if($nBalance > 0 && $remainingGrossPay >= $nBalance){
                $remainingGrossPay = $remainingGrossPay - $nBalance;
            }else if($zeroNet && $nBalance > 0 && $nBalance > $remainingGrossPay && $toDeduct == false){
                $loan->zero_netpay = 1;
            }
        }

        return $loans;
    }

    private function getHdmf($remittance_sched_ctr, $switch = 1, $gross_pay = 0, $hasHDMF=true)
    {
        $remittance_sched_ctr = 1; /*** alter remittance schedule counter ***/
        $hdmf = new StdClass();
        $hdmf->ee = 0;
        $hdmf->er = 0;
        $hdmf->total = 0;

        if (intval($gross_pay) <= 0 || $hasHDMF == false) {
            return $hdmf;
        }

        if (intval($switch) === 0) {
            return $hdmf;
        }

        return $this->db
            ->select("(CAST(ee / $remittance_sched_ctr AS DECIMAL(10,2))) ee,
                          (CAST(er / $remittance_sched_ctr AS DECIMAL(10,2))) er,
                          CAST((ee / $remittance_sched_ctr) + (er / $remittance_sched_ctr) AS DECIMAL(10,2)) total")
            ->get("payroll.hdmf_amount")->row();
    }

    private function calculatePhilHealth($remittance_sched_ctr, $basic = 0, $switch = 1, $gross_pay = 0, $hasPHIC = true)
    {
        $remittance_sched_ctr = 1; /*** alter remittance schedule counter ***/

        $bracket = $this->db->where("`max` >=", $basic)
            ->where("`min` <=", $basic)
            ->get("payroll.philhealth_table")
            ->row();

        $phic = new StdClass();
        $phic->ee = 0;
        $phic->er = 0;
        $phic->total = 0;
        $remittance = 0;

        if (intval($gross_pay) <= 0 || $hasPHIC == false) {
            return $phic;
        }

        if (intval($switch) === 0) {
            return $phic;
        }

        if (!empty($bracket)) {
            $percentage = $bracket->percentage / 100;
            $ee_percentage = $bracket->ee / 100; // employee percentage
            $er_percentage = $bracket->er / 100; // employer percentage

            if (empty($bracket->fixAmount)) {
                $remittance = $basic * $percentage;
            } else {
                $remittance = $bracket->fixAmount;
            }

            /*** $remittance_sched_ctr default value [ 2 ] ***/
            $phic->ee = number_format(($remittance * $ee_percentage) / $remittance_sched_ctr, 2, '.', '');
            $phic->er = number_format(($remittance * $er_percentage) / $remittance_sched_ctr, 2, '.', '');
            $phic->total = $phic->ee + $phic->er;
        }

        return $phic;
    }

    private function calculateSss($args = array(), $sss_class = 1)
    {
        $args_obj = (object)$args;
        $contributionBasis = isset($args_obj->contribution_basis) ? strtolower($args_obj->contribution_basis): "gross_pay";

        $providentSwitch = 0;
        if(isset($args_obj->remittance_parameters) && $args_obj->remittance_parameters){
            $remittance_parameters = $args_obj->remittance_parameters;
            $providentSwitch = isset($remittance_parameters->sss_prov->status) &&
            $remittance_parameters->sss_prov->status?
            $remittance_parameters->sss_prov->status: 0;
        }

        $sssProvident = new stdClass();
        $sssProvident->ee = 0;
        $sssProvident->er = 0;
        $sssProvident->total = 0;

        $sss = new StdClass();
        $sss->ee = 0;
        $sss->er = 0;
        $sss->provident = $sssProvident;
        $sss->total = 0;

        $gross_pay = $args_obj->gross_pay;
        $sss->gross_pay = $gross_pay;
        $sss->updated_gross_pay = $gross_pay;
        $sss->contribution_basis = $contributionBasis;

        $hasSSSNo = true;
        $hasSSSNo = isset($args_obj->has_sss_no) ? $args_obj->has_sss_no: $hasSSSNo;
        $sss->has_sss_no = $hasSSSNo;

        if (intval($gross_pay) <= 0 || $hasSSSNo == false) {
            return $sss;
        }

        if(isset($args_obj->override) && $args_obj->override == true){
            $sqlSelect = "(ee + ec_ee + prov_ee) as ee_total,
            (er + ec_er + prov_er) as er_total,
            prov_ee, prov_er";

            $query = $this->db
            ->select($sqlSelect, FALSE)
            ->group_start()
            ->where("`to` >=", $gross_pay)
            ->where("`from` <=", $gross_pay)
            ->group_end()
            ->where("classification", $sss_class)
            ->where("status", 1)
            ->get("payroll.sss_table");

            if($query->num_rows() == 1){
                $bracket = $query->row();
                $sss->ee = $bracket->ee_total;
                $sss->er = $bracket->er_total;
                $sss->total = $sss->ee + $sss->er;

                $sssProvident->ee = $bracket->prov_ee;
                $sssProvident->er = $bracket->prov_er;
                $sssProvident->total = $sssProvident->ee + $sssProvident->er;
                $sss->provident = $sssProvident;
                $sss->updated_gross_pay = $gross_pay;
            }

            return $sss;
        }

        if (intval($args_obj->switch) === 1) {
            /*** alter full remittance ***/
            $args_obj->should_deduct_remittances = true;
            $args_obj->is_last_remittance_schedule = true;
            /*** alter full remittance ***/
            if ($args_obj->should_deduct_remittances) {
                if ($args_obj->is_last_remittance_schedule) {
                    $sssTotal = 0;
                    $sssERTotal = 0;
                    $sssProvTotal = 0;
                    $sssProvERTotal = 0;

                    $sqlSelect = "SUM(IFNULL({$contributionBasis}, 0)) total_gross_pay,
                    SUM(IFNULL(sss,0)) total_sss, SUM(IFNULL(sss_er, 0)) total_sss_er,
                    SUM(IFNULL(sss_prov, 0)) total_sss_prov, SUM(IFNULL(sss_prov_er, 0)) total_sss_prov_er";

                    $qPreviousRemittances = $this->db
                        ->select($sqlSelect, FALSE)
                        ->where("month_name", $args_obj->month_name)
                        ->where("year", $args_obj->year)
                        ->where("payroll_sched", $args_obj->payout_sched)
                        ->where("emp_id", $args_obj->emp_id)
                        ->where("posted", 1)
                        ->where("is_bonus", 0)
                        ->group_by("emp_id")
                        ->get("payroll.payroll_sheet");

                    if($qPreviousRemittances->num_rows() == 1){
                        $prev_sss_remittances = $qPreviousRemittances->row();
                        $sssTotal = $prev_sss_remittances->total_sss;
                        $sssERTotal = $prev_sss_remittances->total_sss_er;
                        $sssProvTotal = $prev_sss_remittances->total_sss_prov;
                        $sssProvERTotal = $prev_sss_remittances->total_sss_prov_er;

                        $gross_pay += $prev_sss_remittances->total_gross_pay;
                    }

                    $qTempBracket = $this->db
                        ->select("ee, er, prov_ee, prov_er", FALSE)
                        ->where("`to` >=", $gross_pay)
                        ->where("`from` <=", $gross_pay)
                        ->where("classification", $sss_class)
                        ->where("status", 1)
                        ->limit(1)
                        ->order_by("id", "DESC")
                        ->get("payroll.sss_table");

                    if($qTempBracket->num_rows() == 1){
                        $bracket = $qTempBracket->row();

                        $sss->ee = $bracket->ee - $sssTotal;
                        $sss->ee = $sss->ee <= 0 ? 0 : $sss->ee;

                        $sss->er = $bracket->er - $sssERTotal;
                        $sss->er = $sss->er <= 0 ? 0 : $sss->er;

                        $sss->total = $sss->ee + $sss->er;

                        if($providentSwitch == 1){
                            $sssProvident->ee = $bracket->prov_ee - $sssProvTotal;
                            $sssProvident->ee = $sssProvident->ee <= 0 ? 0: $sssProvident->ee;

                            $sssProvident->er = $bracket->prov_er - $sssProvERTotal;
                            $sssProvident->er = $sssProvident->er <= 0 ? 0: $sssProvident->er;

                            $sssProvident->total = $sssProvident->ee + $sssProvident->er;
                            $sss->provident = $sssProvident;
                        }

                        $sss->updated_gross_pay = $gross_pay;
                    }

                    return $sss;
                } else {
                    $query = $this->db
                        ->select("ee, er, (ee + er) total, prov_ee, prov_er, (prov_ee + prov_er) prov_total, $gross_pay gross_pay", FALSE)
                        ->where("`to` >=", $gross_pay)
                        ->where("`from` <=", $gross_pay)
                        ->where("classification", $sss_class)
                        ->where("status", 1)
                        ->get("payroll.sss_table");

                    if($query->num_rows() == 1){
                        $bracket = $query->row();
                        $sss->ee = $bracket->ee;
                        $sss->er = $bracket->er;
                        $sss->total = $bracket->total;

                        if($providentSwitch == 1){
                            $sssProvident->ee = $bracket->prov_ee;
                            $sssProvident->er = $bracket->prov_er;
                            $sssProvident->total = $bracket->prov_total;
                            $sss->provident = $sssProvident;
                        }

                        $sss->updated_gross_pay = $gross_pay;
                    }

                    return $sss;
                }

            }
        }

        return $sss;
    }


    public function calculateTax($taxable_income, $payroll_schedule, $switch = 1, $gross_pay = 0, $hasTinNo = true){
        $tax = new StdClass();
        $tax->ee = 0;
        $tax->er = 0;
        $tax->total = 0;

        if (intval($gross_pay) <= 0 || $hasTinNo === false) {
            return $tax;
        }

        if (intval($switch) === 0) {
            return $tax;
        }

        $this->db->where("payroll_sched", $payroll_schedule);
        $this->db->where("cr_to >=", $taxable_income);
        $this->db->where("cr_from <=", $taxable_income);
        $bracket = $this->db->get("payroll.tax_table")->row();

        if (!empty($bracket)) {
            $percentage = $bracket->pwt_percentage / 100;
            $initial = $bracket->pwt_initial;
            if(floatval($taxable_income) > floatval($bracket->cr_from)){
                $taxable_income = floatval($taxable_income) - floatval($bracket->cr_from);
            }
            $tax->ee = number_format(($taxable_income * $percentage) + $initial, 2, '.', '');
            $tax->total = $tax->ee;
            $tax->switch = $switch;
            $tax->bracket = $bracket;
        }

        return $tax;
    }

    private function getEmployeeAllowances($employee, $working_days_in_a_month, $minutes_per_day, $target_minutes_worked = 0,
        $total_unrendered_minutes = 0, $is_active = null){
        $allowances = $this->db
            ->select("hris_allowance.*, payroll_allowance.code, payroll_allowance.allowance_name")
            ->where("hris_allowance.emp_id", $employee->id)
            ->where("hris_allowance.is_active", 1)
            ->where("hris_allowance.is_archived", 0)
            ->join("payroll.allowance payroll_allowance", "payroll_allowance.id = hris_allowance.allowance_id", "INNER")
            ->get("gcchris.allowances hris_allowance")
            ->result();

        if($employee->deduct_allowance_days > 0){
            $temp_target_days = $target_minutes_worked / $minutes_per_day;
            $temp_target_days -= $employee->deduct_allowance_days;
            $target_minutes_worked = $temp_target_days * $minutes_per_day;
        }

        foreach ($allowances as $allowance) {
            $tempRate = floatval($allowance->rate);

            $working_days_in_a_month = floatval($working_days_in_a_month);
            $minutes_per_day = floatval($minutes_per_day);

            /*** $days_worked = floatval($days_worked);
            $tempTotalMunites = $days_worked * $minutes_per_day; ***/

            $allowance_per_day = $allowance->frequency === 'day' ? $tempRate : ($tempRate / $working_days_in_a_month);
            $allowance_per_day = floatval($allowance_per_day);
            $allowance_per_minute = $allowance_per_day / $minutes_per_day;

            $allowance_per_minute_decimal = number_format($allowance_per_minute, 2);

            $allowance_minutes_total = $target_minutes_worked * $allowance_per_minute;

            if(isset($employee->monthly_paid) && $employee->monthly_paid){
                if(isset($employee->payout_sched_name) && 
                $employee->payout_sched_name == "monthly" && 
                $allowance->frequency == 'month'){ 
                    $allowance_per_minute = ($tempRate * 12) / $employee->default_working_days;
                    $allowance_per_minute = $allowance_per_minute / 8 / 60;
                    $allowance_minutes_total = $tempRate;
                }
            }

            if(isset($employee->monthly_paid) && $employee->monthly_paid){
                if(isset($employee->payout_sched_name) && 
                $employee->payout_sched_name == "semi-monthly" && 
                $allowance->frequency !== 'day'){ 
                    $allowance_per_minute = ($tempRate * 12) / $employee->default_working_days;
                    $allowance_per_minute = $allowance_per_minute / 8 / 60;
                    $tempRate = $tempRate / 2;
                    $allowance_minutes_total = $tempRate;
                }
            }

            if(isset($employee->payout_sched_name) && $employee->payout_sched_name == "weekly" && 
            $allowance->frequency == "month"){
                $allowance_per_day = ($tempRate * 12) / $employee->default_working_days;
                $allowance_per_day = floatval($allowance_per_day);
                $allowance_per_minute = $allowance_per_day / 8 / 60;
                $tempRate = $tempRate / intval($employee->week_counter);
                $allowance_minutes_total = $tempRate;
            }

            $allowance_total = $allowance_minutes_total;

            $allowance_minutes_deduction = $total_unrendered_minutes * $allowance_per_minute;
            $undertime_deduction = $allowance_minutes_deduction;
            $undertime_deduction_decimal = number_format($undertime_deduction, 2);

            /*** $undertime_deduction_decimal = floatval($undertime_deduction_decimal); ***/
            /*** $allowance_total = $days_worked * $allowance_per_day;

            $undertime_deduction = $allowance_per_minute * $undertime_minutes;
            $undertime_deduction_decimal = floatval($allowance_per_minute_decimal) * $undertime_minutes;

            $allowance->total_minutes = $tempTotalMunites;
            $allowance->total_hrs_worked = $tempTotalMunites / 60; ***/

            $allowance->allowance_total = $allowance_total;
            $allowance->undertime_deduction = $undertime_deduction;
            $allowance->undertime_deduction_decimal = $undertime_deduction_decimal;

            $allowanceNet = $allowance_total - $undertime_deduction;
            $totalAllowanceNet = $allowanceNet >= 0 && $target_minutes_worked > $total_unrendered_minutes ? $allowanceNet : 0;
            $allowance->allowance_net = $totalAllowanceNet;

            $decimalAllowanceNet = floatval($allowance_total) - floatval($undertime_deduction_decimal);
            $totalDecimalAllowanceNet = $decimalAllowanceNet >= 0 && $target_minutes_worked > $total_unrendered_minutes ? $decimalAllowanceNet : 0;
            $allowance->allowance_net_decimal = $totalDecimalAllowanceNet;

            $allowance->wdam = $working_days_in_a_month;
            $allowance->apd = $allowance_per_day;
            $allowance->mpd = $minutes_per_day;
            $allowance->allowance_per_minute = $allowance_per_minute;
            $allowance->allowance_per_minute_decimal = $allowance_per_minute_decimal;
            $allowance->allowance_per_hour = $allowance_per_minute * 60;
            $allowance->target_minutes_worked = $target_minutes_worked;
            $allowance->total_unrendered_minutes = $total_unrendered_minutes;
            /*** $allowance->undertime_minutes = $undertime_minutes;
            $allowance->days_worked = $days_worked; ***/
        }

        return $allowances;
    }

    private function getEmployees($payout_sched, $employee_status = 'active', $id = array(), $company = array()){
        $this->db->select("emp.id, emp.lastname, emp.firstname, emp.middlename, emp.suffix,
                               emp.work_status, emp.payroll_type, emp.basic_rate, emp.payout_sched, emp.date_start,
                               sched_resource.shift_resource, payout_schedule.name payout_sched_name, 
                               IFNULL(comp.id, 0) as company_id, 
                               UPPER(CONCAT(emp.lastname, ', ', emp.firstname, 
                               CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                                       TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                                    THEN CONCAT(' ', SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                               END,' ', 
                               CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                                    emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                               END)) as fullname, UPPER(GROUP_CONCAT(DISTINCT TRIM(personnel_location.location_name) 
                                ORDER BY location_name ASC SEPARATOR ', ')) as site_location, 
                                IF(position.name IS NULL, emp.position, position.name) as position");

        $this->db->where("LCASE(emp.employee_status)", $employee_status);
        $this->db->where("payout_sched", $payout_sched);

        if (!empty($id)) {
            $this->db->where_in("emp.id", $id);
        }

        if (!empty($company)) {
            $this->db->group_start();
            $this->db->where_in("emp.company_id", array($company->id, $company->description, $company->code));
            $this->db->group_end();
        }

        $joinArray = array(
            array(
                "table" => $this->tbl_personnel . " personnel",
                "condition" => "personnel.biometricno = emp.biometricno",
                "option" => "INNER"
            ),
            array(
                "table" => $this->tbl_shift_schedule_resource . " sched_resource",
                "condition" => "sched_resource.shift_id = personnel.shift_id",
                "option" => "INNER"
            ),
            array(
                "table" => "payroll.payout_schedule payout_schedule",
                "condition" => "payout_schedule.id = emp.payout_sched",
                "option" => "INNER"
            ),
            array(
                "table" => $this->tbl_tblcompanies . " comp",
                "condition" => "comp.id = emp.company_id OR comp.code = emp.company_id OR comp.description = emp.company_id",
                "option" => "LEFT"
            ),
            array(
                "table" => $this->tbl_personnel_location . " personnel_location",
                "condition" => "personnel_location.personnel_id = personnel.id",
                "option" => "LEFT"
            ),
            array(
                "table" => $this->tbl_tblposition . " position",
                "condition" => "position.id = emp.position",
                "option" => "LEFT"
            ),
        );

        foreach ($joinArray as $join) {
            $this->db->join($join["table"], $join["condition"], $join["option"]);
        }
        $this->db->order_by("emp.lastname, emp.firstname" , "ASC");
        $this->db->group_by("emp.id");
        $tempQuery = $this->db->get($this->tbl_employees . " emp");
        $result_data = array();
        if($tempQuery->num_rows() > 0){ $result_data =  $tempQuery->result(); }

        return array(
            "data" => $result_data,
            "sql" => null, /*** $this->db->last_query() ***/
        );
    }

    public function getPayoutSchedule()
    {
        $resultSet = array();
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        $this->db->like("`name`", $q, "BOTH");
        $this->db->select("id, `name` text");
        $resultSet["results"] = $this->db->get("payroll.payout_schedule")->result();

        return $resultSet;
    }

    function getPayoutScheduleOccurrence($payout_schedule_id)
    {
        $this->db->where("id", $payout_schedule_id);
        $occurrences = $this->db->get("payroll.payout_schedule")->row("occurrence");
        $occurrences_ordinal = array();
        $locale = 'en_US';
        $nf = new NumberFormatter($locale, NumberFormatter::ORDINAL);

        foreach (range(1, $occurrences) as $number) {
            array_push($occurrences_ordinal, array("id" => $number, "text" => $nf->format($number)));
        }

        return $occurrences_ordinal;
    }

    function updateRemittanceParameters()
    {
        $post = (object)$this->input->post();
        $ids = $post->id;
        $resultSet = array();

        $this->db->trans_begin();
        $tempUpdatedParameters = array();

        foreach ($ids as $key => $id) {
            $_var = "status_" . $id;
            $status = isset($post->$_var) ? 1 : 0;
            $this->db->set("status", $status);
            $this->db->where("id", $id);
            $updated = $this->db->update("payroll.remittance_parameters");
            if($updated && $this->db->affected_rows() > 0){
                $arrItem = $this->db->get_where("payroll.remittance_parameters", array("id"=>$id));
                if($arrItem->num_rows() === 1){
                    $tempRow = $arrItem->row();
                    $tempStatus = $status === 1 ? "ACTIVE": "INACTIVE";
                    $tempUpdatedParameters[] = $tempRow;

                    $tempRemittance = strtoupper($tempRow->remittance_name);
                    $logMessage = "Payroll sheet parameter for remittance named `{$tempRemittance}` has been set to `{$tempStatus}` status.";
                    $this->core_layout->setEventLog($logMessage, "update", "success", "payroll");
                }

            }
        }

        $resultSet["success"] = $this->db->trans_status();

        if ($this->db->trans_status() === TRUE) {
            $resultSet["message"] = "Remittance parameters successfully updated.";
            $resultSet["title"] = "Successfully updated";
            $resultSet["toast"] = "success";
            $resultSet["updated_parameters"] = $tempUpdatedParameters;
            $resultSet["updated_count"] = count($tempUpdatedParameters);
            $this->db->trans_commit();
        } else {
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["title"] = "Error occurred";
            $resultSet["toast"] = "error";
            $this->db->trans_rollback();
        }

        return $resultSet;
    }

    function manageCreatedAdjustments($mode)
    {
        $post = (object) $this->input->post();
        $post->adj_type = floatval($post->amount) < 0 ? 0 : 1;
        $post->amount = abs(str_replace(",", "", $post->amount));
        $post->particulars = strtoupper($post->particulars);
        $temp_particulars = $post->particulars;
        $temp_ps_id = $post->payroll_sheet_id;
        $id = $post->id;
        $user = (object)$this->core_layout->getUserLoggedIn();
        unset($post->id);

        $resultSet = array();
        $isParticularExist = true;
        $gross_pay = 0;

        $this->db->trans_begin();
        $payroll_sheet = $this->db->where("id", $post->payroll_sheet_id)->get("payroll.payroll_sheet")->row();
        $changes = false;

        if ($mode === "add") {
            $particularExist = $this->db->get_where("payroll.payroll_sheet_created_adjustments",
                array(
                    "payroll_sheet_id"=>$temp_ps_id,
                    "particulars"=>$temp_particulars
                )
            );

            if($particularExist->num_rows() == 0){
                $isParticularExist = false;
                $post->created_by = $user->employee_id;
                $changes = $this->db->insert("payroll.payroll_sheet_created_adjustments", $post);
            }
        } else if ($mode === 'edit') {
            $getCurrentAdjustment = $this->db->get_where("payroll.payroll_sheet_created_adjustments", array("id"=>$id));
            if($getCurrentAdjustment->num_rows() == 1){
                $currentRow = $getCurrentAdjustment->row();
                $allowUpdate = false;
                if($currentRow->particulars == $temp_particulars){ $allowUpdate = true; }
                else{
                    $particularExist = $this->db->get_where("payroll.payroll_sheet_created_adjustments",
                        array(
                            "payroll_sheet_id"=>$temp_ps_id,
                            "particulars"=>$temp_particulars
                        )
                    );
                    if($particularExist->num_rows() == 0){
                        $allowUpdate = true;
                    }
                }

                if($allowUpdate){
                    $isParticularExist = false;
                    $this->db->where('id', $id);
                    $post->updated_by = $user->employee_id;
                    $post->updated_at = date("Y-m-d H:i:s");
                    $changes = $this->db->update("payroll.payroll_sheet_created_adjustments", $post);
                }
            }
        }

        if($isParticularExist == false){
            if ($this->db->trans_status() === TRUE) {
                if ($changes) {
                    $resultSet["title"] = $mode === "add" ? "Payroll sheet adjustment saved." : "Payroll sheet adjustment updated.";
                    $resultSet["message"] = $mode === "add" ? "Payroll sheet adjustment `{$temp_particulars}` has been added." : "Payroll sheet adjustment `{$temp_particulars}` has been updated.";
                    $resultSet["toast"] = "success";
                    $resultSet["success"] = $this->db->trans_status();

                    $psLog = array();
                    $psLog["payroll_sheet_id"] = $temp_ps_id;
                    $psLog["logs"] = $resultSet["message"];
                    $psLog["is_posted"] = (isset($payroll_sheet->posted) && intval($payroll_sheet->posted) == 1)? intval($payroll_sheet->posted): 0;
                    $this->createPayrollSheetLogs($psLog);
                }
                $this->db->trans_commit();
            } else {
                $resultSet["title"] = "An Error occurred";
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["toast"] = "error";
                $resultSet["success"] = false;
                $this->db->trans_rollback();
            }
        }else{
            $resultSet["title"] = $mode === "add" ? "New payroll sheet adjustment type" : "Payroll sheet adjustment type updated";
            $resultSet["message"] = $mode === "add" ? "Payroll sheet adjustment type already exist!" : "Payroll sheet adjustment type already exist and was in used!";
            $resultSet["toast"] = "error";
            $resultSet["success"] = false;
        }

        $resultSet["payroll_sheet"] = $gross_pay;

        return $resultSet;
    }

    function manageCustomAdjustments($mode)
    {
        $post = (object)$this->input->post();
        $post->cadj_type = floatval($post->amount) < 0 ? 0 : 1;
        $post->amount = abs(str_replace(",", "", $post->amount));
        $post->particulars = strtoupper($post->particulars);
        $id = $post->id;
        $user = (object)$this->core_layout->getUserLoggedIn();
        unset($post->id);

        $resultSet = array();

        $this->db->trans_begin();

        $payroll_sheet = $this->db->where("id", $post->payroll_sheet_id)->get("payroll.payroll_sheet")->row();
        $changes = false;

        if ($mode === "add") {
            $post->created_by = $user->employee_id;
            $changes = $this->db->insert("payroll.payroll_sheet_custom_adjustments", $post);
        } else if ($mode === 'edit') {
            $this->db->where('id', $id);
            $post->updated_by = $user->employee_id;
            $post->updated_at = date("Y-m-d H:i:s");
            $changes = $this->db->update("payroll.payroll_sheet_custom_adjustments", $post);
        }

        $resultSet["success"] = $this->db->trans_status();
        if ($this->db->trans_status() === TRUE) {
            $resultSet["title"] = $mode === "add" ? "Custom Adjustment saved." : "Custom Adjustment updated.";
            $resultSet["message"] = $mode === "add" ? "Adjustment was successfully saved." : "Adjustment was successfully updated.";
            $resultSet["toast"] = "success";

            // START CUSTOM ADJUSTMENT CALCULATION APPLIED TO GROSS AND NET PAY
            if ($changes) {
                $gross_pay = ($payroll_sheet->basic_rate - $payroll_sheet->total_undertime_amount) +
                    $payroll_sheet->total_allowances + ($payroll_sheet->ot_amount + $payroll_sheet->ot_ndiff_amount);
                $custom_adjustments = $this->db->where("payroll_sheet_id", $post->payroll_sheet_id)
                    ->get("payroll.payroll_sheet_custom_adjustments")
                    ->result();
                foreach ($custom_adjustments as $row) {
                    if (intval($row->cadj_type) === 0) {
                        $gross_pay -= $row->amount;
                    } else {
                        $gross_pay += $row->amount;
                    }
                }

                $net_pay = $gross_pay - ($payroll_sheet->sss + $payroll_sheet->ph +
                        $payroll_sheet->hdmf + $payroll_sheet->tax + $payroll_sheet->total_loans);
                $this->db->where("id", $post->payroll_sheet_id)->update("payroll.payroll_sheet", array("gross_pay" => $gross_pay, "net_pay" => $net_pay));
            }
            // END CUSTOM ADJUSTMENT CALCULATION APPLIED TO GROSS AND NET PAY
            $this->db->trans_commit();
        } else {
            $resultSet["title"] = "An Error occurred";
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["toast"] = "error";
            $this->db->trans_rollback();
        }

        $resultSet["payroll_sheet"] = $gross_pay;

        return $resultSet;
    }

    function getCustomAdjustmentParticulars()
    {
        return array(
            "suggestions" => $this->db
                ->select("particulars `value`, particulars `data`")
                ->group_by("particulars")
                ->get("payroll.payroll_sheet_custom_adjustments")
                ->result()
        );
    }

    function getPayrollSheetCustomAdjustments($payroll_sheet_id)
    {
        $data = array();
        $this->db->select("ps_cadj.*, CONCAT(emp.firstname, ' ',
                                    CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE' AND
                                              emp.middlename !='' AND emp.middlename IS NOT NULL
                                         THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                                    END,' ', emp.lastname,
                                    CASE WHEN emp.suffix != 'N/A' AND
                                              emp.suffix !='NONE' AND emp.suffix !='' AND
                                              emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                                    END) `_created_by`");
        $this->db->join("gccmaster.tblemployees emp", "emp.id = ps_cadj.created_by", "INNER");
        $this->db->where("ps_cadj.payroll_sheet_id", $payroll_sheet_id);
        $query = $this->db->get("payroll.payroll_sheet_custom_adjustments ps_cadj");

        $data['data'] = $query->result();
        return $data;
    }

    function getPayrollSheetCreatedAdjustments($payroll_sheet_id)
    {
        $data = array();
        $this->db->select("ps_adj.*, CONCAT(emp.firstname, ' ',
                                    CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE' AND
                                              emp.middlename !='' AND emp.middlename IS NOT NULL
                                         THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                                    END,' ', emp.lastname,
                                    CASE WHEN emp.suffix != 'N/A' AND
                                              emp.suffix !='NONE' AND emp.suffix !='' AND
                                              emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                                    END) `_created_by`,
                                    UPPER(IFNULL(rp.remittance_name, ps_adj.particulars)) as particulars");
        $this->db->join("gccmaster.tblemployees emp", "emp.id = ps_adj.created_by", "INNER");
        $this->db->join("payroll.remittance_parameters rp", "rp.remittance_code = ps_adj.particulars", "LEFT");
        $this->db->where("ps_adj.payroll_sheet_id", $payroll_sheet_id);
        $query = $this->db->get("payroll.payroll_sheet_created_adjustments ps_adj");

        $data['data'] = $query->result();
        return $data;
    }

    function getCreatedAdjustment($adj_id)
    {
        return $this->db->where("id", $adj_id)->get("payroll.payroll_sheet_created_adjustments")->row();
    }

    function getCustomAdjustment($cadj_id)
    {
        return $this->db->where("id", $cadj_id)->get("payroll.payroll_sheet_custom_adjustments")->row();
    }

    function deleteCreatedAdjustments($adj_id){
        $resultSet = array();

        $created_adjustment = $this->db->where("id", $adj_id)
            ->get("payroll.payroll_sheet_created_adjustments")
            ->row();

        $payroll_sheet_id = $created_adjustment->payroll_sheet_id;
        $temp_particulars = (isset($created_adjustment->particulars) && $created_adjustment->particulars)? $created_adjustment->particulars: "NO ADJUSTMENT TYPE";
        $temp_payroll_sheet = $this->getCurrentPayrollSheetById($payroll_sheet_id);
        $this->db->where("id", $adj_id);
        $deleted = $this->db->delete("payroll.payroll_sheet_created_adjustments");

        $resultSet["success"] = $deleted;
        if ($deleted) {
            $resultSet["title"] = "Deleted Successfully";
            $resultSet["message"] = "Payroll sheet adjustment `{$temp_particulars}` has been removed.";
            $resultSet["toast"] = "success";

            $psLog = array();
            $psLog["payroll_sheet_id"] = $payroll_sheet_id;
            $psLog["logs"] = $resultSet["message"];
            $psLog["is_posted"] = (isset($temp_payroll_sheet->posted) && intval($temp_payroll_sheet->posted) == 1)? intval($temp_payroll_sheet->posted): 0;
            $this->createPayrollSheetLogs($psLog);
        } else {
            $resultSet["title"] = "Error Occurred";
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["toast"] = "error";
        }

        return $resultSet;
    }

    function deleteCustomAdjustments($cadj_id)
    {
        $resultSet = array();

        $custom_adjustment = $this->db->where("id", $cadj_id)
            ->get("payroll.payroll_sheet_custom_adjustments")
            ->row();
        $payroll_sheet_id = $custom_adjustment->payroll_sheet_id;

        $this->db->where("id", $cadj_id);
        $deleted = $this->db->delete("payroll.payroll_sheet_custom_adjustments");

        $resultSet["success"] = $deleted;
        if ($deleted) {
            $resultSet["title"] = "Deleted Successfully";
            $resultSet["message"] = "Custom adjustment was successfully deleted.";
            $resultSet["toast"] = "success";

            // START CALCULATION/UPDATE CUSTOM ADJUSTMENT
            $payroll_sheet = $this->db->where("id", $payroll_sheet_id)->get("payroll.payroll_sheet")->row();
            $gross_pay = ($payroll_sheet->basic_rate - $payroll_sheet->total_undertime_amount) +
                $payroll_sheet->total_allowances + ($payroll_sheet->ot_amount + $payroll_sheet->ot_ndiff_amount);
            $custom_adjustments = $this->db->where("payroll_sheet_id", $payroll_sheet_id)
                ->get("payroll.payroll_sheet_custom_adjustments")
                ->result();
            foreach ($custom_adjustments as $row) {
                if (intval($row->cadj_type) === 0) {
                    $gross_pay -= $row->amount;
                } else {
                    $gross_pay += $row->amount;
                }
            }

            $net_pay = $gross_pay - ($payroll_sheet->sss + $payroll_sheet->ph +
                    $payroll_sheet->hdmf + $payroll_sheet->tax + $payroll_sheet->total_loans);
            $this->db->where("id", $payroll_sheet_id)->update("payroll.payroll_sheet", array("gross_pay" => $gross_pay, "net_pay" => $net_pay));
            // END CALCULATION/UPDATE CUSTOM ADJUSTMENT
        } else {
            $resultSet["title"] = "Error Occurred";
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["toast"] = "error";
        }

        return $resultSet;
    }

    /* END NEW PAYROLL SHEET FUNCTION 11/13/2020*/

    function postPayrollSheet()
    {
        $this->db->trans_begin();
        $user = (object)$this->core_layout->getUserLoggedIn();

        $resultSet = array();
        $post = (object)$this->input->post();
        $this->db->where_in("id", $post->selected);
        $this->db->set("posted", 1);
        $this->db->set("posted_by", $user->employee_id);
        $this->db->set("posted_at", date("Y-m-d H:i:s"));
        $updated = $this->db->update("payroll.payroll_sheet");
        if($updated){
            $this->db->select("UPPER(TRIM(CONCAT(a.firstname, ' ',
                CASE WHEN UPPER(TRIM(a.middlename)) != 'N/A' AND UPPER(TRIM(a.middlename)) != 'NONE' AND
                        TRIM(a.middlename) !='' AND a.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(a.middlename, 1, 1), '.') ELSE ''
                END,' ', a.lastname,
                CASE WHEN UPPER(TRIM(a.suffix)) != 'N/A' AND
                    UPPER(TRIM(a.suffix !='NONE')) AND a.suffix !='' AND
                        a.suffix IS NOT NULL THEN CONCAT(' ', a.suffix) ELSE ''
                END))) as employee_name, DATE_FORMAT(b.pay_date, '%M %d, %Y') as pay_date, 
                DATE_FORMAT(b.date_start, '%M %d, %Y') as date_start, 
                DATE_FORMAT(b.date_end, '%M %d, %Y') as date_end");
            $this->db->from($this->tbl_employees." a");
            $this->db->join($this->tbl_payroll_sheet." b", "b.emp_id = a.id");
            $this->db->where_in("b.id", $post->selected);
            $queryPayroll = $this->db->get();
            if($queryPayroll->num_rows() > 0){
                foreach ($queryPayroll->result() as $key => $value) {
                    $logMessage = "Payroll sheet data of `{$value->employee_name}` with pay date of `{$value->pay_date}` and a coverage date from `{$value->date_start}` to `{$value->date_end}` has been set to `POSTED` status.";
                    $this->core_layout->setEventLog($logMessage, "update", "success", "payroll");
                }
            }
        }

        foreach ($post->selected as $selected) {
            $loans = $this->db
                ->where("payroll_sheet_id", $selected)
                ->get("payroll.payroll_sheet_loan_payments");
            if($loans->num_rows() > 0){
                foreach($loans->result() as $kkk => $vvv){
                    $loan_id = $vvv->loan_id;
                    if (!empty($loan_id)) {
                        $total_amount_paid = $this->db
                            ->select_sum("psloanpayments.amount_due")
                            ->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id")
                            ->where("ps.posted", 1)
                            ->where("loan_id", $loan_id)
                            ->get("payroll.payroll_sheet_loan_payments psloanpayments")
                            ->row("amount_due");

                        $loan = $this->db->where("id", $loan_id)->get("gcchris.loans")->row();

                        if (floatval($total_amount_paid) >= floatval($loan->amount)) {
                            $this->db->where("id", $loan_id)->update("gcchris.loans", array("paid" => 1));
                        }
                    }
                }
            }
        }

        if ($this->db->trans_status() === TRUE) {
            $resultSet["title"] = "Posting Successful";
            $resultSet["message"] = "Selected Payroll sheet was successfully posted.";
            $resultSet["toast"] = "success";
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
            $resultSet["title"] = "Error Occurred";
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["toast"] = "success";
        }

        return $resultSet;
    }

    function getEmployeeTimesheet($emp_id, $start_date, $end_date)
    {
        $sqlSelect = "ts.*, IFNULL(ts.total_accredited_ndiff_ot_hrs, 0) as total_accredited_ndiff_ot_hrs,
        IFNULL(ts.total_accredited_ot_hrs, 0) as total_accredited_ot_hrs";

        $this->db->select($sqlSelect);
        $this->db->where("ts.emp_id", $emp_id);
        $this->db->where("ts.verified", 1);
        $this->db->where("ts.date >=", date("Y-m-d", strtotime($start_date)));
        $this->db->where("ts.date <=", date("Y-m-d", strtotime($end_date)));
        $query = $this->db->get("gcctimeutility.timesheet ts");

        $interval = DateInterval::createFromDateString('1 day');
        $dateStart = new DateTime($start_date);
        $dateEnd = new DateTime($end_date);
        $period = new DatePeriod($dateStart, $interval, $dateEnd);

        $tempFields = array();
        foreach ($query->field_data() as $key => $value) {
            $tempFields[] = $value->name;
        }

        $data = $query->result_array();
        $inDates = array();
        foreach ($query->result() as $key => $value) {
            $inDates[] = $value->date;
        }

        $schedules = array();
        $this->db->select("c.shift_resource");
        $this->db->from($this->tbl_employees." a");
        $this->db->join($this->tbl_personnel." b", "b.biometric_id = a.biometricno OR b.biometricno = a.biometricno");
        $this->db->join($this->tbl_shift_schedule_resource." c", "c.shift_id = b.shift_id");
        $this->db->where("a.id", $emp_id);
        $qTemp = $this->db->get();

        if($qTemp->num_rows() == 1){
            $employee = $qTemp->row();
            $shift_resource_id = unserialize($employee->shift_resource);
            $schedules = $this->db
                ->where_in("id", $shift_resource_id)
                ->get($this->tbl_shift_schedule_list)
                ->result();
        }


        $schedules_obj = array_reduce($schedules,
            function ($carry, $obj) {
                $key = $obj->weekday;
                $carry[$key] = $obj;
                return $carry;
            }, []);


        $arrHolidays = array();
        foreach ($period as $dt) {
            $weekday = strtolower($dt->format("l"));
            $date = $dt->format("Y-m-d");
            $is_holiday = (object)$this->ts_model->getCurrentDateIsHoliday($date);
            $temp = $date;
            //check if dates holiday
            if($is_holiday->is_holiday){
                if(!in_array($temp, $arrHolidays)){ $arrHolidays[] = $temp; }
            }
            // end of code

            
            if(isset($schedules_obj[$weekday]) && $schedules_obj[$weekday]){
                $_schedule = $schedules_obj[$weekday];
                if(!in_array($date, $inDates)){
                    $am_shift_only = (($_schedule->am_start !== null && $_schedule->am_end !== null) && ($_schedule->pm_start === null && $_schedule->pm_end === null));
                    $pm_shift_only = (($_schedule->am_start === null && $_schedule->am_end === null) && ($_schedule->pm_start !== null && $_schedule->pm_end !== null));
                    $isWholeDay = ($am_shift_only == false && $pm_shift_only == false);
                    $hasShift = false;
                    if($isWholeDay){ $hasShift = true; }
                    else if($am_shift_only){ $hasShift = true; }
                    else if($pm_shift_only){ $hasShift = true; }

                    if($hasShift){
                        $tempRowData = new stdClass();
                        foreach ($tempFields as $key => $value) {
                            if($value == "emp_id"){ 
                                $tempRowData->{$value} = $emp_id; 
                            }
                            else if($value == "date"){ 
                                $tempRowData->{$value} = $date; 
                            }
                            else if($value == "weekday"){ 
                                $tempRowData->{$value} = $weekday; 
                            }
                            else{ 
                                // return 1 if holiday, 0 if not
                                if($value == "is_holiday" && is_array($arrHolidays) && !empty($arrHolidays) && in_array($temp, $arrHolidays)){ 
                                    $tempRowData->{$value} = 1;
                                }else{
                                    // if($value == "is_holiday" && $tempRowData->{$value} === null){
                                    //     $tempRowData->{$value} = 0; 
                                    // }else{
                                        $tempRowData->{$value} = null; 
                                    // }
                                }
                                //end of code
                            }
                        }
                        $data[] = $tempRowData;
                    }
                }
            }else{
                if(!in_array($date, $inDates)){
                    $tempRowData2 = new stdClass();
                    foreach ($tempFields as $key => $value) {
                        if($value == "date"){ 
                            $tempRowData2->{$value} = $date; 
                        }else if($value == "weekday"){ 
                            $tempRowData2->{$value} = $weekday; 
                        }else{
                            $tempRowData2->{$value} = null; 
                        }
                    }
                    $data[] = $tempRowData2;
                }
            }
        }

        return array(
            "data" => $data,
            "fields" => $tempFields,
            "sql" => $this->db->last_query(),
        );
    }

    function getPayRateSettings()
    {
        $this->db->order_by("id", "ASC");
        return $this->db->get("payroll.payrate_settings")->result();
    }

    function updatePayRateSetting()
    {
        $post = (object)$this->input->post();
        $id = $post->id;
        unset($post->id);
        $setting = $this->db->where("id", $id)->get("payroll.payrate_settings")->row();

        if (floatval($setting->regular_rate) === floatval($post->regular_rate) &&
            floatval($setting->night_diff_rate) === floatval($post->night_diff_rate) &&
            floatval($setting->ot_rate) === floatval($post->ot_rate) &&
            floatval($setting->ot_night_diff_rate) === floatval($post->ot_night_diff_rate)) {
            return false;
        }

        $this->db->where("id", $id);
        $this->db->set($post);
        $updated = $this->db->update("payroll.payrate_settings");
        if($updated && $this->db->affected_rows() == 1){
            foreach ($post as $key => $value) {
                if(isset($setting->{$key}) && $setting->{$key} && $setting->{$key} !== $value){
                    $particular = strtoupper(trim($setting->particulars));
                    $tempKey = strtoupper(trim(str_replace("_", " ", $key)));
                    $logMessage = "Payroll sheet pay rate settings for `{$particular}` with parameter named `{$tempKey}`has been set to `{$value}`.";
                    $this->core_layout->setEventLog($logMessage, "update", "success", "payroll");
                }
            }
            return true;
        }else{
            return false;
        }
    }

    public function confirmUndoPosting($payroll_sheet_id)
    {
        $employee = $this->input->post("employee");
        $this->db->where("id", $payroll_sheet_id);
        $this->db->set("posted", 0);
        if ($this->db->update("payroll.payroll_sheet")) {
            $this->db->reset_query();
            $queryUnPosted = $this->db->get_where("payroll.payroll_sheet", array("id"=>$payroll_sheet_id, "posted"=>0));
            if($queryUnPosted->num_rows() == 1){
                $tempRow = $queryUnPosted->row();
                /*** check paid balance loans and update ***/
                $Paidloans = $this->getEmployeeLoans($tempRow->emp_id, $tempRow->gross_pay, 1);
                if($Paidloans > 0){
                    foreach ($Paidloans as $loan) {
                        $qLoans = $this->db
                            ->where("loan_id", $loan->id)
                            ->get("payroll.payroll_sheet_loan_payments");
                        if($qLoans->num_rows() > 0){
                            $amount_paid = 0;
                            foreach ($qLoans->result() as $key => $value) { $amount_paid += $value->amount_due; }
                            if(floatval($loan->amount) !== $amount_paid 
                                || (floatval($loan->total_amount_paid) == 0 || floatval($loan->amount_due) > 0)){
                                $paidState = array("paid"=>0);
                                if(intval($loan->active) == 2){ $paidState["active"] = 0; }

                                $this->db->update("gcchris.loans", $paidState, array("id"=>$loan->id));
                            }
                        }else{
                            $this->db->update("gcchris.loans", array("paid"=>0, "active"=>0), array("id"=>$loan->id));
                        }
                    }
                }
                /*** check paid balance loans and update ***/
            }

            /*** *code here **/
            $this->db->select("UPPER(TRIM(CONCAT(a.firstname, ' ',
                CASE WHEN UPPER(TRIM(a.middlename)) != 'N/A' AND UPPER(TRIM(a.middlename)) != 'NONE' AND
                        TRIM(a.middlename) !='' AND a.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(a.middlename, 1, 1), '.') ELSE ''
                END,' ', a.lastname,
                CASE WHEN UPPER(TRIM(a.suffix)) != 'N/A' AND
                    UPPER(TRIM(a.suffix !='NONE')) AND a.suffix !='' AND
                        a.suffix IS NOT NULL THEN CONCAT(' ', a.suffix) ELSE ''
                END))) as employee_name, DATE_FORMAT(b.pay_date, '%M %d, %Y') as pay_date, 
                DATE_FORMAT(b.date_start, '%M %d, %Y') as date_start, 
                DATE_FORMAT(b.date_end, '%M %d, %Y') as date_end");
            $this->db->from($this->tbl_employees." a");
            $this->db->join($this->tbl_payroll_sheet." b", "b.emp_id = a.id");
            $this->db->where("b.id", $payroll_sheet_id);
            $queryPayroll = $this->db->get();
            if($queryPayroll->num_rows() > 0){
                foreach ($queryPayroll->result() as $key => $value) {
                    $logMessage = "Payroll sheet data of `{$value->employee_name}` with pay date of `{$value->pay_date}` and a coverage date from `{$value->date_start}` to `{$value->date_end}` has been set to `UNPOSTED` status.";
                    $this->core_layout->setEventLog($logMessage, "update", "success", "payroll");
                }
            }

            return array(
                "success" => true,
                "message" => "Posted payroll sheet of <span class='m--font-boldest'>" . ucwords(strtolower($employee)) . "</span> was successfully undone.",
                "title" => "Payroll sheet undone",
                "toast" => "success"
            );
        }

        return array(
            "success" => false,
            "message" => $this->db->error()["message"],
            "title" => "Error Occurred",
            "toast" => "error"
        );
    }

    public function getPostedPayrollSheetYears()
    {
        $arrData = array();
        $this->db->select('`year` id, `year` `text`');
        $this->db->group_by('year');
        $this->db->order_by('year', 'asc');
        $qTemp = $this->db->get('payroll.payroll_sheet');
        $currentDate = array("id"=>date("Y"), "text"=>date("Y"));
        if($qTemp->num_rows() > 0){
            $tempYear = array();
            $tempArray = $qTemp->result_array();
            foreach ($qTemp->result() as $key => $value) {
                $tempYear[] = $value->id;
            }
            if(!in_array(date("Y"), $tempYear)){
                array_push($tempArray, $currentDate);
            }
            $arrData = $tempArray;
        }else{
            $arrData = $currentDate;
        }

        $data['results'] = $arrData;

        return $data;
    }

    public function getPostedPayrollSheetYearsData($entryDate = null)
    {
        $arrData = array();
        $this->db->select('`year` id, `year` `text`');
        if($entryDate){ $this->db->where('pay_date >=', $entryDate); }
        $this->db->group_by('year');
        $this->db->order_by('year', 'desc');
        $qTemp = $this->db->get('payroll.payroll_sheet');
        $currentDate = array("id"=>date("Y"), "text"=>date("Y"));
        if($qTemp->num_rows() > 0){
            $tempYear = array();
            $tempArray = $qTemp->result_array();
            foreach ($qTemp->result() as $key => $value) {
                $tempYear[] = $value->id;
            }
            if(!in_array(date("Y"), $tempYear)){
                array_push($tempArray, $currentDate);
            }
            $arrData = $tempArray;
        }else{
            $arrData = $currentDate;
        }

        return $arrData;
    }

    function getPayrollHistory()
    {
        $post = $this->input->post();
        $group_by = $post['group_by'];
        $group_by = (isset($post['group_by']) && $post['group_by'])? $post['group_by']: 1;
        $month = (isset($post['month']) && $post['month'])? $post['month']: null;
        $year = (isset($post['year']) && $post['year'])? $post['year']: null;
        $range = (isset($post['range']) && $post['range'])? $post['range']: false;
        $by_date_range = (isset($post['by_date_range']) && $post['by_date_range'] == "true")? true: false;

        $this->db->select("ps.id,
                           ps.date_start,
                           ps.date_end,
                           ps.pay_date,
                           DATE_FORMAT(ps.pay_date, '%m') pay_month,
                            IFNULL(SUM((SELECT ps_rmt.ee FROM payroll.payroll_sheet_remittances ps_rmt WHERE
                                  ps_rmt.payroll_sheet_id = ps.id AND ps_rmt.remittance_code='sss')), 0) sss_ee,
                            IFNULL(SUM((SELECT ps_rmt.er FROM payroll.payroll_sheet_remittances ps_rmt WHERE
                                  ps_rmt.payroll_sheet_id = ps.id AND ps_rmt.remittance_code='sss')), 0) sss_er,
                            IFNULL(SUM((SELECT ps_rmt.ee FROM payroll.payroll_sheet_remittances ps_rmt WHERE
                                  ps_rmt.payroll_sheet_id = ps.id AND ps_rmt.remittance_code='phic')), 0) phic_ee,
                            IFNULL(SUM((SELECT ps_rmt.er FROM payroll.payroll_sheet_remittances ps_rmt WHERE
                                  ps_rmt.payroll_sheet_id = ps.id AND ps_rmt.remittance_code='phic')), 0) phic_er,
                            IFNULL(SUM((SELECT ps_rmt.ee FROM payroll.payroll_sheet_remittances ps_rmt WHERE
                                  ps_rmt.payroll_sheet_id = ps.id AND ps_rmt.remittance_code='hdmf')), 0) hdmf_ee,
                            IFNULL(SUM((SELECT ps_rmt.er FROM payroll.payroll_sheet_remittances ps_rmt WHERE
                                  ps_rmt.payroll_sheet_id = ps.id AND ps_rmt.remittance_code='hdmf')), 0) hdmf_er,
                            IFNULL(SUM((SELECT ps_rmt.total FROM payroll.payroll_sheet_remittances ps_rmt WHERE
                                  ps_rmt.payroll_sheet_id = ps.id AND ps_rmt.remittance_code='tax')), 0) tax_total,
                           CAST(SUM(ps.net_pay) AS DECIMAL(10, 2)) total_net_pay");

        if($by_date_range == true){
            $tempDate = explode(" - ", $range);
            if(count($tempDate) == 2){
                $fromDate = date("Y-m-d", strtotime($tempDate[0]));
                $toDate = date("Y-m-d", strtotime($tempDate[1]));
                $this->db->group_start();
                $this->db->where("ps.pay_date >=", $fromDate);
                $this->db->where("ps.pay_date <=", $toDate);
                $this->db->group_end();
            }
        }
        if($by_date_range == false){
            if($month && $year){
                $this->db->group_start();
                $this->db->where("MONTH(ps.pay_date)", $month);
                $this->db->where("YEAR(ps.pay_date)", $year);
                $this->db->group_end();
            }
        }

        switch (intval($group_by)) {
            case 1: $this->db->group_by('ps.pay_date'); break;
            case 2: $this->db->group_by('MONTH(ps.pay_date), YEAR(ps.pay_date)'); break;
            case 3: $this->db->group_by('YEAR(ps.pay_date)'); break;
            default: $this->db->group_by('ps.pay_date'); break;
        }

        $query = $this->db->get('payroll.payroll_sheet ps');
        $resultSet['data'] = $query->result();

        return $resultSet;
    }

    function createdApprovalAdjustments(){
        $post = (object) $this->input->post();
        $resultSet = array();
        if(isset($post) && $post){
            $tempStatus = $post->status;
            $post->status = (isset($post->status) && ($post->status == 3 || $post->status == 4))? 0: $post->status;
            $tempWhere = array("id"=>$post->id);
            unset($post->id);
            $tempQuery = $this->db->get_where("payroll.payroll_sheet_created_adjustments", $tempWhere);
            if($tempQuery->num_rows() == 1){
                $allowAdjustmentApproval = true;
                $tempRow = $tempQuery->row();
                $temp_particulars = (isset($tempRow->particulars) && $tempRow->particulars)? $tempRow->particulars: "NO ADJUSTMENT TYPE";
                $payroll_sheet = $this->getCurrentPayrollSheetById($tempRow->payroll_sheet_id);

                if($tempRow->adj_type == "1"){
                    $_grossPay = floatval($payroll_sheet->gross_pay);
                    $arrDeductions = array("sss", "sss_prov", "ph", "hdmf", "tax", "total_loans", "interest_amount", "sss_loan",  "hdmf_loan");
                    foreach ($arrDeductions as $arrKey) {
                        $tempAmount = isset($payroll_sheet->{$arrKey}) && $payroll_sheet->{$arrKey} ? floatval($payroll_sheet->{$arrKey}): 0;
                        if($_grossPay >= $tempAmount){
                            $_grossPay = $_grossPay - $tempAmount;
                        }
                    }

                    if(round($tempRow->amount, 2) > round($_grossPay, 2) && intval($tempStatus) == 1){ $allowAdjustmentApproval = false; }
                }

                $post->approval_by = $this->core_layout->getCurrentEmployeeId();
                $post->approval_at = date("Y-m-d H:i:s");
                if($allowAdjustmentApproval){
                    $updated = $this->db->update("payroll.payroll_sheet_created_adjustments", $post, $tempWhere);
                    if($updated && $this->db->affected_rows() > 0){
                        switch ($tempStatus) {
                            case 1:
                                $resultSet["title"] = "Adjustment Approval";
                                $resultSet["message"] = "Adjustment `{$temp_particulars}` approval has been made.";
                            break;
                            case 2:
                                $resultSet["title"] = "Adjustment Disapproval";
                                $resultSet["message"] = "Adjustment `{$temp_particulars}` disapproval has been made.";
                            break;
                            case 3:
                                $resultSet["title"] = "Adjustment Undo Approval";
                                $resultSet["message"] = "Adjustment `{$temp_particulars}` undo approval has been made.";
                                break;
                            case 4:
                                $resultSet["title"] = "Adjustment Undo Disapproval";
                                $resultSet["message"] = "Adjustment `{$temp_particulars}` undo disapproval has been made.";
                            break;
                            default:
                                $resultSet["title"] = "Adjustment Approval";
                                $resultSet["message"] = "Adjustment `{$temp_particulars}` approval has been made.";
                            break;
                        }
                        $resultSet["toast"] = "success";
                        $resultSet["success"] = true;
    
                        $psLog = array();
                        $psLog["payroll_sheet_id"] = $tempRow->payroll_sheet_id;
                        $psLog["logs"] = $resultSet["message"];
                        $psLog["is_posted"] = (isset($payroll_sheet->posted) && intval($payroll_sheet->posted) == 1)? intval($payroll_sheet->posted): 0;
                        $this->createPayrollSheetLogs($psLog);
                    }else{
                        switch ($tempStatus) {
                            case 1:
                                $resultSet["title"] = "Adjustment Approval";
                                $resultSet["message"] = "Failed to approve `{$temp_particulars}` payroll sheet adjustment!";
                            break;
                            case 2:
                                $resultSet["title"] = "Adjustment Disapproval";
                                $resultSet["message"] = "Failed to disapprove `{$temp_particulars}` payroll sheet adjustment!";
                            break;
                            case 3:
                                $resultSet["title"] = "Adjustment Undo Approval";
                                $resultSet["message"] = "Failed to undo approve `{$temp_particulars}` payroll sheet adjustment!";
                                break;
                            case 4:
                                $resultSet["title"] = "Adjustment Undo Disapproval";
                                $resultSet["message"] = "Failed to undo disapprove `{$temp_particulars}` payroll sheet adjustment!";
                            break;
                            default:
                                $resultSet["title"] = "Adjustment Approval";
                                $resultSet["message"] = "Failed to approve `{$temp_particulars}` payroll sheet adjustment!";
                            break;
                        }
                        $resultSet["toast"] = "error";
                        $resultSet["success"] = false;
                    }
                }else{
                    $resultSet["title"] = "Adjustment Approval";
                    $resultSet["message"] = "Failed to approve `{$temp_particulars}` payroll sheet adjustment, total gross pay with current deductions did not meet the required remaining payroll balance!";
                    $resultSet["toast"] = "warning";
                    $resultSet["success"] = false;
                }
            }else{
                switch ($tempStatus) {
                    case 1: $resultSet["title"] = "Adjustment Approval"; break;
                    case 2: $resultSet["title"] = "Adjustment Disapproval"; break;
                    case 3: $resultSet["title"] = "Adjustment Undo Approval"; break;
                    case 4: $resultSet["title"] = "Adjustment Undo Disapproval"; break;
                    default: $resultSet["title"] = "Adjustment Approval"; break;
                }
                $resultSet["message"] = "Payroll sheet adjustment data not found!";
                $resultSet["toast"] = "error";
                $resultSet["success"] = false;
            }
        }else{
            $resultSet["title"] = "An Error occurred";
            $resultSet["message"] = "No post data found!";
            $resultSet["toast"] = "error";
            $resultSet["success"] = false;
        }
        return $resultSet;
    }

    function createPayrollSheetLogs($data=array()){
        $response = false;
        $data["created_by"] = $this->core_layout->getCurrentEmployeeId();
        $added = $this->db->insert($this->tbl_ps_logs, $data);
        if($added){ $response = true; }
        return $response;
    }

    function getCurrentPayrollSheetById($id=null){
        $arrData = array();
        if($id){
            $tempQuery = $this->db->get_where($this->tbl_payroll_sheet, array("id"=>$id));
            if($tempQuery->num_rows() == 1){
                $arrData = $tempQuery->row();
            }
        }
        return $arrData;
    }

    function generatePayrollPayslip(){
        $resultset = array();
        $post = $this->input->post();
        $privilege = $this->core_layout->getCurrentActions();
        $hasViewByCompany = (in_array('view_by_company', $privilege)) ? true : false;

        if(isset($post) && $post){
            $tempPayDate = date("Y-m-d", strtotime($post["pay_date"]));
            $post["pay_date"] = date("Y/m/d", strtotime($post["pay_date"]));
            $employee_ids = isset($post["employees"]) ? $post["employees"] : array();
            $isBonus = isset($post["is_bonus"]) && $post["is_bonus"] ? intval($post["is_bonus"]): 0;

            if(isset($post["payroll_group"]) && is_array($post["payroll_group"]) && count($post["payroll_group"]) > 0){
                $this->db->select("employee_id");
                $this->db->from($this->tbl_payroll_group);
                $this->db->where("status", 1);
                $this->db->where("is_archived", 0);
                $this->db->where_in("id", $post["payroll_group"]);
                $psGroup = $this->db->get();
                if($psGroup->num_rows() > 0){
                    foreach ($psGroup->result() as $key => $value) {
                        $employees = @unserialize($value->employee_id);
                        foreach ($employees as $kk => $id) {
                            if(is_array($employee_ids) && !in_array($id, $employee_ids)){
                                $employee_ids[] = $id;
                            }
                        }
                    }
                }
                $employee_ids = array_unique($employee_ids);
            } else {
                // added to get all employees by payroll group assigned if no employees are selected
                if ($hasViewByCompany && empty($employee_ids)) {
                    $employee_ids = $this->getEmployeesByPrivilege($this->user_data['emp_id'], $post["company"], $privilege);
                }
            }

            $company = $this->db->where("id", $post["company"])->get("gcchris.tblcompanies")->row();
            $post["company_description"] = (isset($company->description) && $company->description)? $company->description: "---";

            /*** $sqlSelect = "GROUP_CONCAT(DISTINCT(ps.id)) as ps_ids";
            $this->db->select($sqlSelect); ***/
            $this->db->select("ps.id");
            $this->db->where("ps.pay_date", $tempPayDate);

            /*** show all generated ***/
            if(isset($settings->enable_zero_netpay) && intval($settings->enable_zero_netpay->setting_value) == 0){ $this->db->where("ps.no_of_days !=", 0);  }
            /*** $this->db->where("ps.no_of_days !=", 0); ***/
            /*** show all generated ***/

            $this->db->where("ps.posted", 1);
            $this->db->where("ps.is_bonus", $isBonus);
            
            if (!empty($employee_ids)) { 
                $this->db->where_in("emp.id", $employee_ids); 
            } else { 
                // added to prevent generating all employees if view by company privilege is enabled
                if ($hasViewByCompany) { $this->db->where("emp.id", 0); }
            }

            if (!empty($company)) {
                $this->db->where("ps.company_id", $company->id);
            }

            /*** if (!empty($company)) {
                $this->db->group_start();
                $this->db->where_in("company_id", array($company->id, $company->description, $company->code));
                $this->db->group_end();
            } ***/

            $this->db->join("gccmaster.tblemployees emp", "emp.id = ps.emp_id");
            $this->db->join("payroll.payroll_sheet_custom_adjustments custom_adjustments", "custom_adjustments.payroll_sheet_id = ps.id", "LEFT");
            $this->db->join("payroll.payroll_sheet_created_adjustments created_adjustments", "created_adjustments.payroll_sheet_id = ps.id", "LEFT");
            $this->db->group_by("ps.id");

            $query = $this->db->get("payroll.payroll_sheet ps");
            $resultset["sql"] = $this->db->last_query();

            if($query->num_rows() > 0){
                $ids = array();
                foreach ($query->result() as $key => $value) {
                    if(!in_array($value->id, $ids)){
                        $ids[] = $value->id;
                    }
                    /*** $tempIds = explode(",", trim($value->ps_ids));
                    $ids = array_merge($ids, $tempIds); ***/
                }
                $ids = array_unique($ids);

                $resultset["response"] = true;
                $resultset["ps_id"] = $ids;
                $resultset["count"] = count($ids);
                $resultset["post_data"] = $post;
                $resultset["toastr_msg"] = "Payslip data has been generated successfully.";
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to generate payslip data!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;

    }

    public function getPayrollPayslipTableRequest(){
        $resultset = array();
        $post = $this->input->post();
        $data = array();
        if(isset($post) && $post){
            $settings = $this->getSettings();
            $ps_ids = isset($post["ps_ids"]) && is_array($post["ps_ids"])? $post["ps_ids"] : array();
            if($ps_ids && count($ps_ids) > 0){
                $dir = "ASC";
                $order = "emp.lastname";
                $sqlSelect = "ps.*, emp.lastname, emp.firstname, emp.middlename, emp.suffix,
                    GROUP_CONCAT(DISTINCT(CONCAT(custom_adjustments.particulars,'||',custom_adjustments.amount, '||', custom_adjustments.cadj_type))) custom_adjustments,
                    GROUP_CONCAT(DISTINCT(CONCAT(created_adjustments.particulars,'||',created_adjustments.amount, '||', created_adjustments.adj_type, '||', created_adjustments.status))) created_adjustments,
                    GROUP_CONCAT(DISTINCT(CONCAT(ps_loan.code,'||',psl_payment.amount_due, '||', ps_loan.loan_class))) sss_hdmf_loan_deduction,
                    IFNULL(po.is_telegram, 0) as is_telegram";
                $this->db->select($sqlSelect);
                /*** show all generated ***/
                if(isset($settings->enable_zero_netpay) && intval($settings->enable_zero_netpay->setting_value) == 0){ $this->db->where("ps.no_of_days !=", 0);  }
                /*** $this->db->where("ps.no_of_days !=", 0); ***/
                /*** show all generated ***/
                $this->db->where("ps.posted", 1);
                $this->db->where_in("ps.id", $ps_ids);

                $this->db->join("gccmaster.tblemployees emp", "emp.id = ps.emp_id");
                $this->db->join("payroll.payroll_sheet_custom_adjustments custom_adjustments", "custom_adjustments.payroll_sheet_id = ps.id", "LEFT");
                $this->db->join("payroll.payroll_sheet_created_adjustments created_adjustments", "created_adjustments.payroll_sheet_id = ps.id", "LEFT");
                $this->db->join("gcchris.tblpayslip_options as po","po.emp_id = emp.id","LEFT");

                /** added for breaking down cash advance and loans; sss_hdmf_loan_deduction */ 
                $this->db->join("payroll.payroll_sheet_loan_payments psl_payment", "psl_payment.payroll_sheet_id = ps.id", "LEFT");
                $this->db->join("gcchris.loans hr_loans", "hr_loans.id = psl_payment.loan_id", "LEFT");
                $this->db->join("payroll.loans ps_loan", "ps_loan.id = hr_loans.loan_id", "LEFT");
                /** added for breaking down cash advance and loans; sss_hdmf_loan_deduction */ 

                $this->db->group_by("ps.id");
                $this->db->order_by($order, $dir);

                $query = $this->db->get("payroll.payroll_sheet ps");
                $resultset["sql"] = $this->db->last_query();

                if($query->num_rows() > 0){
                    $data = $query->result();
                }
            }
        }

        $resultset["data"] = $data;
        return $resultset;
    }

    public function getCurrentPayrollPayslip($id=null){
        $resultset = array();
        if($id){
            $sqlSelect = "ps.*, DATE_FORMAT(ps.date_start, '%m/%d/%y') as date_start, DATE_FORMAT(ps.date_end, '%m/%d/%y') as date_end, DATE_FORMAT(ps.pay_date, '%m/%d/%y') as pay_date, emp.payroll_type, emp.basic_rate,
            IFNULL(comp.description, emp.company_id) as company_description, IFNULL(dept.description, emp.department_id) as department_description,
            IFNULL(pos.name, emp.position) as position_description, GROUP_CONCAT(DISTINCT(loan_payments.id)) as loan_payments,
            GROUP_CONCAT(DISTINCT(CONCAT(custom_adjustments.particulars,'||',custom_adjustments.amount, '||', custom_adjustments.cadj_type))) custom_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(created_adjustments.particulars,'||',created_adjustments.amount, '||', created_adjustments.adj_type, '||', created_adjustments.status))) created_adjustments,
            IFNULL(psa.allowance_total, 0) as psa_total, IFNULL(psa.undertime_deduction, 0) as psa_deduction, ps.basic_rate as basic_pay, ps.total_holiday_amount, ps.total_holiday_minutes";

            $this->db->select($sqlSelect);
            $this->db->join("gccmaster.tblemployees as emp", "emp.id = ps.emp_id");
            $this->db->join("gcchris.tblcompanies as comp", "comp.id = emp.company_id OR comp.code = emp.company_id", "LEFT");
            $this->db->join("gcchris.tbldepartments as dept", "dept.id = emp.department_id OR dept.description = emp.department_id", "LEFT");
            $this->db->join("gcchris.tblposition as pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
            $this->db->join("payroll.payroll_sheet_custom_adjustments custom_adjustments", "custom_adjustments.payroll_sheet_id = ps.id", "LEFT");
            $this->db->join("payroll.payroll_sheet_created_adjustments created_adjustments", "created_adjustments.payroll_sheet_id = ps.id AND created_adjustments.status = 1", "LEFT");
            $this->db->join("payroll.payroll_sheet_loan_payments loan_payments", "loan_payments.payroll_sheet_id = ps.id", "LEFT");
            $this->db->join("payroll.payroll_sheet_allowances psa", "psa.payroll_sheet_id = ps.id", "LEFT");
            $qTemp = $this->db->get_where("payroll.payroll_sheet as ps", array("ps.id"=>$id, "ps.posted"=>1));
            if($qTemp->num_rows() == 1){
                $tempDeductionIndexes = array("sss", "sss_prov", "hdmf", "ph", "tax", "total_loans");

                $tempRow = $qTemp->row();
                $tempData = $this->core_layout->getEmployeeData($tempRow->emp_id);
                $tempData = (object) $tempData;
                $displayName = (isset($tempData->display_name_0) && $tempData->display_name_0)? $tempData->display_name_0: "No assigned name";

                $tempRow->employee_name = $displayName;
                $tempRow->biometricno = isset($tempData->biometricno) && $tempData->biometricno ? $tempData->biometricno: "---";
                $tempRow->idno = isset($tempData->idno) && $tempData->idno ? $tempData->idno: "---";
                /*** start computation ***/
                $_target_hours_worked = ($tempRow->total_minutes_worked + $tempRow->total_unrendered_minutes) / 60;
                $tempRow->target_hours = $_target_hours_worked;

                $unpaidHolidayHrs = floatval($tempRow->unpaid_holiday_minutes) > 0 ? floatval($tempRow->unpaid_holiday_minutes) / 60: 0;
                if($unpaidHolidayHrs > 0){
                    $tempRow->target_hours -= $unpaidHolidayHrs;
                }
                
                if($tempRow->total_holiday_minutes > 0){
                    $tempHolidayHrs = $tempRow->total_holiday_minutes / 60;
                    $tempRow->target_hours -= $tempHolidayHrs;
                }

                $tempRow->target_hours = round($tempRow->target_hours, 2);
                $tempRow->hours_worked = $tempRow->total_minutes_worked / 60;
                $tempRow->hours_worked = round($tempRow->hours_worked, 2);
                $tempEwd = $_target_hours_worked / 8;

                /*** $tempEwd = is_float($tempEwd) ? ceil($tempEwd): $tempEwd; ***/
                $tempRow->ewd = $tempEwd;
                $tempRow->ewd_decimal = $tempEwd;

                $tempRow->target_payrate = $tempRow->daily * $tempRow->ewd;

                if(strtolower($tempRow->payroll_type) == "monthly"){
                    $tempRow->target_payrate = (intval($tempRow->payroll_sched) == 2)? $tempRow->basic_rate / 2: $tempRow->basic_rate;
                }

                /*** if(floatval($tempRow->unpaid_holiday_amount) > 0 && floatval($tempRow->target_payrate) >= floatval($tempRow->unpaid_holiday_amount)){
                    $tempRow->target_payrate -= $tempRow->unpaid_holiday_amount;
                } ***/

                if(floatval($tempRow->total_unrendered_minutes) > 0){
                    $totalUnrenderedMinutes = floatval($tempRow->total_unrendered_minutes);
                    $toDeduct = floatval($totalUnrenderedMinutes) - floatval($tempRow->total_undertime_minutes);
                    $inDays = (floatval($toDeduct) / 60) / 8;
                    if(floatval($tempRow->ewd) > $inDays){ $tempRow->ewd -= $inDays; }
                    if(floatval($tempRow->ewd_decimal) > $inDays){ $tempRow->ewd_decimal -= $inDays; }
                }

                if(floatval($tempRow->unpaid_holiday_minutes) > 0 && $tempRow->total_unrendered_minutes >= $tempRow->unpaid_holiday_minutes){
                    $tempRow->total_unrendered_minutes -= $tempRow->unpaid_holiday_minutes;
                }

                if(floatval($tempRow->unpaid_holiday_amount) > 0 && $tempRow->total_unrendered_amount >= $tempRow->unpaid_holiday_amount){
                    $tempRow->total_unrendered_amount -= $tempRow->unpaid_holiday_amount;
                }
                
                if($tempRow->ewd != floatval($tempRow->no_of_days)){
                    $_days = $tempRow->no_of_days;
                    $dailyMinutes = 480;

                    $minutes = $_days * $dailyMinutes;
                    $roundedMinutes = ceil($minutes / 5) * 5;
                    if(floatval($tempRow->total_minutes_worked) > $roundedMinutes){
                        $tempRow->ewd = $tempRow->no_of_days;
                        $tempRow->ewd_decimal = $tempRow->no_of_days;
                        $tempRow->target_hours = round($roundedMinutes / 60, 2);
                    }
                }

                if(intval($tempRow->is_bonus) == 1){
                    $tempRow->target_payrate = $tempRow->basic_pay;
                    $tempRow->ewd = $tempRow->no_of_days;
                    $tempRow->ewd_decimal = $tempRow->no_of_days;
                }
                
                $tempRow->basic_pay = number_format($tempRow->basic_pay, 2, ".", ",");
                $tempRow->ewd = round($tempRow->ewd, 2);
                $tempRow->ewd_decimal = round($tempRow->ewd_decimal, 2);

                $tempRow->target_payrate = number_format($tempRow->target_payrate, 2, ".", ",");
                $absent_hours = ($tempRow->total_unrendered_minutes - $tempRow->total_undertime_minutes) / 60;
                $tempRow->absent_hours = number_format($absent_hours, 2, ".", ",");
                $undertime_hours = $tempRow->total_undertime_minutes / 60;
                $tempRow->undertime_hours = number_format($undertime_hours, 2, ".", ",");
                $tempRow->total_unrendered_amount = number_format($tempRow->total_unrendered_amount, 2, ".", ",");
                $tempRow->unpaid_holiday_amount = number_format($tempRow->unpaid_holiday_amount, 2, ".", ",");
                $unpaidHolidayHours = $tempRow->unpaid_holiday_minutes / 60;
                $tempRow->unpaid_holiday_hours = number_format($unpaidHolidayHours, 2, ".", ",");
                $tempRow->unpaid_holiday_minutes = number_format($tempRow->unpaid_holiday_minutes, 2, ".", ",");

                /*** added holiday pay */
                $tempRow->total_holiday_amount = number_format($tempRow->total_holiday_amount, 2, '.', ',');
                $holiday_hours = $tempRow->total_holiday_minutes / 60;
                $tempRow->holiday_hours = number_format($holiday_hours, 2, ".", ",");
                /*** added holiday pay */

                $overtime_ndiff_hours = $tempRow->ot_ndiff_minutes / 60;
                $tempRow->ot_ndiff_hours = floatval($overtime_ndiff_hours) > 0 ?
                    is_float($overtime_ndiff_hours)? number_format($overtime_ndiff_hours, 2, ".", ","): $overtime_ndiff_hours
                    : $overtime_ndiff_hours;

                $overtime_hours = $tempRow->ot_minutes / 60;
                
                $tempRow->total_ot_hours = floatval($overtime_hours) > 0 ?
                    is_float($overtime_hours)? number_format($overtime_hours, 2, ".", ","): $overtime_hours + $tempRow->ot_ndiff_hours
                : $overtime_hours + $tempRow->ot_ndiff_hours;

                $tempRow->ot_hours = is_float($overtime_hours)? number_format($overtime_hours, 2, ".", ","): $overtime_hours;
            
                $tempRow->loans = array();
                $tempRow->adjustment_earnings = array();
                $tempRow->adjustment_deductions = array();
                $tempRow->loan_count = 0;
                $tempRow->adjustment_e_count = 0;
                $tempRow->adjustment_d_count = 0;

                /*** end computation ***/
                if($tempRow->created_adjustments){
                    $tempCreatedAdjustments = explode(",", $tempRow->created_adjustments);
                    if(is_array($tempCreatedAdjustments) && count($tempCreatedAdjustments) > 0){
                        foreach ($tempCreatedAdjustments as $kk => $vv) {
                            $tempItem = explode("||", $vv);
                            if(is_array($tempItem) && count($tempItem) == 4){
                                $tempKey = strtolower($tempItem[0]);
                                /* update to detect phic adjustment as ph */
                                if($tempKey == 'phic'){ $tempKey = 'ph'; }
                                /* end of update */
                                $tempValue = $tempItem[1];
                                $tempType = $tempItem[2];
                                $tempKey = $tempKey == "loan" ? "total_loans": $tempKey;

                                if(isset($tempRow->{$tempKey}) && floatval($tempRow->{$tempKey}) > 0){
                                    if(intval($tempType) == 1){
                                        $tempRow->{$tempKey} = $tempRow->{$tempKey} + $tempValue;
                                    }else{
                                        $tempRow->{$tempKey} = $tempRow->{$tempKey} - $tempValue;
                                    }
                                }else{
                                    $tempRow->{$tempKey} = $tempValue;
                                    if(!in_array($tempKey, $tempDeductionIndexes)){ $tempDeductionIndexes[] = $tempKey; }
                                }
                            }
                        }
                    }
                }

                if($tempRow->custom_adjustments){
                    $tempCustomAdjustments = explode(",", $tempRow->custom_adjustments);
                    if(is_array($tempCustomAdjustments) && count($tempCustomAdjustments) > 0){
                        foreach ($tempCustomAdjustments as $kk => $vv) {
                            $tempItem = explode("||", $vv);
                            if(is_array($tempItem) && count($tempItem) == 3){
                                $tempAdjustment = new stdClass();
                                $tempKey = strtolower($tempItem[0]);
                                $tempValue = $tempItem[1];
                                $tempType = $tempItem[2];
                                $tempAdjustment->label = $tempKey;
                                $tempAdjustment->value = $tempValue;
                                $tempAdjustment->display_value = number_format($tempValue, 2, ".", ",");
                                $tempAdjustment->adj_type = $tempType;

                                if(intval($tempType) == 1){
                                    $tempRow->adjustment_earnings[] = $tempAdjustment;
                                }else{
                                    $tempRow->adjustment_deductions[] = $tempAdjustment;
                                }
                            }
                        }
                    }
                }

                $deductInternalLoans = 0;

                if($tempRow->loan_payments){
                    $tempLoanPayments = explode(",", $tempRow->loan_payments);
                    if(is_array($tempLoanPayments) && count($tempLoanPayments) > 0){

                        /** reworked query for all loans to add up all same loan_name in payslip */
                        $this->db->select("c.loan_name, ROUND(SUM(a.amount_due), 2) as amount_due, c.loan_type");
                        $this->db->join("gcchris.loans b", "b.id = a.loan_id AND (b.active != 2 OR b.paid = 0)");
                        $this->db->join("payroll.loans c", "c.id = b.loan_id");
                        $this->db->where_in('a.id', $tempLoanPayments);
                        $this->db->group_by('c.loan_name');
                        $qTempLoans = $this->db->get("payroll.payroll_sheet_loan_payments a");

                        if($qTempLoans->num_rows() > 0){
                            foreach($qTempLoans->result() as $loanRow){
                                $tempLoan = floatval($loanRow->amount_due);
                                $loanRow->loan_name = strtoupper($loanRow->loan_name);
                                if($tempLoan > 0 && $loanRow->loan_type == "0"){ $deductInternalLoans += $tempLoan; }

                                $loanRow->loan_name = strtoupper($loanRow->loan_name);
                                $loanRow->amount_due = number_format($loanRow->amount_due, 2, ".", ",");
                                $tempRow->loans[] = $loanRow;
                            }
                        }
                        /** reworked query for all loans to add up all same loan_name in payslip */

                        /** original source code for getting all loans that displays duplicate charges but have different amount in payslip */
                        // foreach ($tempLoanPayments as $kk => $vv) {
                        //     if($vv){
                        //         $this->db->select("c.loan_name, a.amount_due, c.loan_type");
                        //         /*** $this->db->join("gcchris.loans b", "b.id = a.loan_id AND b.active = 1"); ***/
                        //         $this->db->join("gcchris.loans b", "b.id = a.loan_id AND (b.active != 2 OR b.paid = 0)");
                        //         $this->db->join("payroll.loans c", "c.id = b.loan_id");
                        //         $qTempLoans = $this->db->get_where("payroll.payroll_sheet_loan_payments a", array("a.id"=>$vv));
                        //         if($qTempLoans->num_rows() == 1){
                        //             $loanRow = $qTempLoans->row();
                        //             $tempLoan = floatval($loanRow->amount_due);
                        //             if($tempLoan > 0 && $loanRow->loan_type == "0"){ $deductInternalLoans += $tempLoan; }

                        //             $loanRow->loan_name = strtoupper($loanRow->loan_name);
                        //             $loanRow->amount_due = number_format($loanRow->amount_due, 2, ".", ",");
                        //             $tempRow->loans[] = $loanRow;
                        //         }
                        //     }
                        // }
                        /** original source code for getting all loans to display in payslip */
                    }
                }

                $tempRow->adjustment_e_count = count($tempRow->adjustment_earnings);
                $tempRow->adjustment_d_count = count($tempRow->adjustment_deductions);
                $tempRow->loan_count = count($tempRow->loans);
                $tempRow->deductions = 0;
                $tempDeductionValue = 0;
                $_tempLoanValue = 0;

                if(isset($tempDeductionIndexes) && count($tempDeductionIndexes) > 0){
                    foreach ($tempDeductionIndexes as $key => $item) {
                        $tempValue = floatval($tempRow->{$item});
                        if($tempValue > 0){
                            $tempRow->{$item} = number_format($tempRow->{$item}, 2, ".", ",");
                            // $tempDeductionValue += $tempValue; //commented out for total sum of deduction and loans

                            // seperated total internal loans and total deductions
                            if($item == 'total_loans'){
                                $_tempLoanValue += $tempValue;
                            }else{
                                $tempDeductionValue += $tempValue;
                            }
                            // seperated total loans and total deductions
                        }

                        /*** for external loans start ***/
                        $tempItem = "{$item}_loan";
                        if(isset($tempRow->{$tempItem}) && floatval($tempRow->{$tempItem}) !== 0){
                            $tempLoanValue = floatval($tempRow->{$tempItem});
                            // $tempDeductionValue += $tempLoanValue; //commented out for total sum of deduction and loans
                            $_tempLoanValue += $tempLoanValue;
                        }
                        /*** for external loans end ***/
                    }
                }

                if(is_array($tempRow->adjustment_deductions) && count($tempRow->adjustment_deductions) > 0){
                    foreach($tempRow->adjustment_deductions as $kk => $vv) {
                        if(intval($vv->adj_type) == 0){
                            $tempDeductionValue += floatval($vv->value);
                        }
                    }
                }

                if($deductInternalLoans > 0){
                    $tempLoansx = str_replace(",", "", $tempRow->total_loans);
                    $tempLoansx = floatval($tempLoansx);
                    if($tempLoansx >= $deductInternalLoans){
                        $tempTotalLoanx = $tempLoansx - $deductInternalLoans;
                        $tempRow->total_loans = number_format($tempTotalLoanx, 2, ".", ",");
                    }
                }

                $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                $tempNet = number_format($tempRow->net_pay, 2, ".", "");
                $tempRow->temp_net = $tempNet;
                $tempRow->net_to_text = $f->format($tempNet);

                $xnet = explode(".", $tempNet);
                if(is_array($xnet) && count($xnet) > 0){
                    $tempCents = "";
                    $tempDecimal = end($xnet);
                    if(intval($tempDecimal) > 1){ $tempCents = " centavos"; }
                    else if(intval($tempDecimal) == 1){ $tempCents = " centavo"; }

                    $tempNetx = 0;
                    $tempLabel = array();

                    foreach ($xnet as $key => $value) {
                        if(floatval($value) > 0){
                            $tempLabel[] = $f->format($value);
                            if($tempDecimal !== $value){
                               $tempNetx += intval($value);
                            }
                        }
                    }
                    if(is_array($tempLabel) && count($tempLabel) > 0){
                        $tempPesoSign = "";
                        if(intval($tempNetx) > 1){ $tempPesoSign = " pesos"; }
                        else if(intval($tempNetx) == 1){ $tempPesoSign = " peso"; }
                        $tempEndValue = end($tempLabel);
                        foreach ($tempLabel as $kk => $vv) {
                            if($vv !== $tempEndValue){
                                $tempLabel[$kk] = $vv.$tempPesoSign;
                            }
                        }
                    }
                    $tempRow->net_xx = $tempNetx;
                    $tempRow->net_to_text = implode(' and ', $tempLabel);
                    if(count($tempLabel) > 1){ $tempRow->net_to_text .= $tempCents; }
                }
                $tempRow->net_to_text = strtoupper($tempRow->net_to_text);

                $tempRow->deductions = number_format($tempDeductionValue, 2, ".", ",");
                $tempRow->basic_rate = number_format($tempRow->basic_rate, 2, ".", ",");
                $tempRow->gross_pay = number_format($tempRow->gross_pay, 2, ".", ",");
                $tempRow->net_pay = number_format($tempRow->net_pay, 2, ".", ",");
                $tempRow->totalLoan = number_format($_tempLoanValue, 2, ".", ",");

                $resultset["response"] = true;
                $resultset["data"] = $tempRow;
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    public function getEmpId($psIds) {
      $this->db->select("ps.id,ps.emp_id,opt.is_printed");
      $this->db->from("payroll.payroll_sheet as ps");
      $this->db->join("gcchris.tblpayslip_options AS opt", "ps.emp_id = opt.emp_id", "left");
      $this->db->where_in("ps.id", $psIds);
      $query = $this->db->get();
      $result = $query->result();
      $id = array();
      foreach ($result as $row) {
          if ($row->is_printed === NULL) {
              $data = array(
                  'emp_id' => $row->emp_id,
                  'is_printed' => 1
              );
              $this->db->insert('gcchris.tblpayslip_options', $data);
              $id[] = $row->id;
          }
          else if($row->is_printed === "1"){
            $id[] = $row->id;
          }
      }
      return $id;
  }
  

    public function setPrintablePayslip($ids=array()){
        $resultset = array();
        $post = $this->input->post();
        if(isset($ids) && $ids){ $post["ids"] = $ids; }
        if(isset($post) && $post){
            if(isset($post["ids"]) && is_array($post["ids"]) && count($post["ids"]) > 0){
                $arrData = array();
                $forPrint = $this->getEmpId($post["ids"]);
                foreach ($forPrint as $id) {
                    $result = (object) $this->getCurrentPayrollPayslip($id);
                    if($result->response === true){
                        $arrData[] = $result->data;
                    }
                }
                if(is_array($arrData) && !empty($arrData)){
                    $html = $this->load->view("payroll/payroll/printable/print_content", array("data"=>$arrData), true);
                    $resultset["response"] = true;
                    $resultset["html"] = $html;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    private function createMessageV1($arrData){
        $msg = "";
        $msg .="PAY PERIOD: ".$arrData[0]->date_start." - ".$arrData[0]->date_end."\n";
        $msg .= "EMPLOYEE NAME: " . strtoupper($arrData[0]->employee_name) . "\n";
        $msg .= "ID NUMBER: " . $arrData[0]->idno . "\n";
        $msg .= "POSITION: " . strtoupper($arrData[0]->position_description) . "\n";
        $msg .= "DEPARTMENT : " . strtoupper($arrData[0]->department_description) . "\n";
        $msg .= "----------------------------------------------\n";
        $msg .= (intval($arrData[0]->is_bonus) == 1 && $arrData[0]->bonus_code ? "BONUS / {$arrData[0]->bonus_code}" : "BASIC PAY :") . "\t";
        $msg .= $arrData[0]->target_payrate . "\n";
        $msg .= "REG DAYS: " . $arrData[0]->ewd_decimal . "\t";
        $msg .= "\nREG HOURS: " . $arrData[0]->target_hours . "\n";
        $msg .= "----------------------------------------------\n";

        if(intval($arrData[0]->is_bonus) == 0) {
            $msg .= "LATES/ABSENCES: " . $arrData[0]->total_unrendered_amount . "\n";
            $msg .= "ABSENT HRS: " . $arrData[0]->absent_hours . "\t";
            $msg .= "\nUT HRS: " . $arrData[0]->undertime_hours . "\n";
            $msg .= "----------------------------------------------\n";
            if(floatval($arrData[0]->unpaid_holiday_amount) > 0){
                $unpaidDays = floatval($arrData[0]->unpaid_holiday_hours) / 8;
                $msg .= "UNPAID HOLIDAY: " . $arrData[0]->unpaid_holiday_amount . "\n";
                $msg .= "DAYS: " . $unpaidDays . "\t";
                $msg .= "\nHRS: " . $arrData[0]->unpaid_holiday_hours . "\n";
                $msg .= "----------------------------------------------\n";
            }
        }

        $msg .= "ALLOWANCES: " . $arrData[0]->total_allowances . "\n";
        
        if($arrData[0]->ot_amount > 0) {
            $msg .= "OVERTIME: ";
            if($arrData[0]->ot_ndiff_amount > 0) {
                $msg .= number_format($arrData[0]->ot_amount + $arrData[0]->ot_ndiff_amount, 2) . "\n";
            } else {
                $msg .= $arrData[0]->ot_amount . "\n";
            }
            $msg .= "OT HRS: " . number_format($arrData[0]->ot_hours, 2) . "\t";
            $msg .= "NDIFF HRS: " . number_format($arrData[0]->ot_ndiff_hours, 2) . "\n";
        }
        
        if($arrData[0]->adjustment_e_count > 0) {
            $msg .= "ADJUSTMENTS:\n";
            foreach ($arrData[0]->adjustment_earnings as $kk => $vv) {
                $msg .= strtoupper($vv->label) . ": " . $vv->display_value . "\n";
            }
        }

        $msg .= "----------------------------------------------\n";
        $msg .= "GROSS PAY: " . $arrData[0]->gross_pay . "\n";

        if(intval($arrData[0]->is_bonus) == 0) {
            $msg .= "----------------------------------------------\n";
            $msg .= "DEDUCTIONS:\n\n";

            if($arrData[0]->sss && floatval($arrData[0]->sss) > 0) {
                $msg .= "SSS: " . $arrData[0]->sss . "\n";
            }

            if($arrData[0]->sss_prov && floatval($arrData[0]->sss_prov) > 0) {
                $msg .= "SSS PROVIDENT: " . $arrData[0]->sss_prov . "\n";
            }

            if($arrData[0]->ph && floatval($arrData[0]->ph) > 0) {
                $msg .= "PHILHEALTH: " . $arrData[0]->ph . "\n";
            }

            if($arrData[0]->hdmf && floatval($arrData[0]->hdmf) > 0) {
                $msg .= "HDMF: " . $arrData[0]->hdmf . "\n";
            }

            if($arrData[0]->tax && floatval($arrData[0]->tax) > 0) {
                $msg .= "TAX: " . $arrData[0]->tax . "\n";
            }

            if($arrData[0]->total_loans && floatval($arrData[0]->total_loans) > 0) {
                $msg .= "LOAN: " . $arrData[0]->total_loans . "\n";
            }

            if($arrData[0]->total_loans && (floatval($arrData[0]->total_loans) > 0 || floatval($arrData[0]->sss_loan) > 0 || floatval($arrData[0]->hdmf_loan) > 0 || count($arrData[0]->loans) > 0)) {
                foreach ($arrData[0]->loans as $kk => $vv) {
                    if($vv->amount_due > 0){
                        $msg .= $vv->loan_name . ": " . $vv->amount_due . "\n";
                    }
                }
            }

            if($arrData[0]->adjustment_d_count && count($arrData[0]->adjustment_d_count) > 0) {
                foreach ($arrData[0]->adjustment_deductions as $kk => $vv) {
                $msg .= strtoupper($vv->label) . ": " . $vv->display_value . "\n";
                }
            }

            if(floatval($arrData[0]->deductions) > 0) {
                $msg .= "TOTAL LOANS & DEDUCTIONS: " . $arrData[0]->deductions . "\n";
            }
            
            $msg .= "----------------------------------------------\n";
        }

        $msg .= "NET PAY: " . $arrData[0]->net_pay . "\n";
        return $msg;
    }
    
    private function createMessage($arrData){
        $_temp_total_others = 0; $_tempDeductions = 0; $temp_totalLoans = 0;

        $msg = "";
        $msg .="PAY PERIOD: ".$arrData[0]->date_start." - ".$arrData[0]->date_end."\n";
        $msg .= "EMPLOYEE NAME: " . strtoupper($arrData[0]->employee_name) . "\n";
        $msg .= "ID NUMBER: " . $arrData[0]->idno . "\n";
        $msg .= "POSITION: " . strtoupper($arrData[0]->position_description) . "\n";
        $msg .= "DEPARTMENT : " . strtoupper($arrData[0]->department_description) . "\n";
        $msg .= "----------------------------------------------\n";
        $msg .= (intval($arrData[0]->is_bonus) == 1 && $arrData[0]->bonus_code ? "BONUS / {$arrData[0]->bonus_code}" : "BASIC PAY :") . "\t";
        $msg .= $arrData[0]->target_payrate . "\n";
        $msg .= "REG DAYS: " . $arrData[0]->ewd_decimal . "\t";
        $msg .= "\nREG HOURS: " . $arrData[0]->target_hours . "\n";
        
        if(floatval($arrData[0]->holiday_hours) > 0){
            $msg .= "HOLIDAY PAY: " . $arrData[0]->total_holiday_amount . "\t";
            $msg .= "\nHOLIDAY HRS: " . $arrData[0]->holiday_hours . "\n";
        }

        $msg .= "----------------------------------------------\n";

        if(intval($arrData[0]->is_bonus) == 0) {
            $msg .= "LATES/ABSENCES: " . $arrData[0]->total_unrendered_amount . "\n";
            $msg .= "ABSENT HRS: " . $arrData[0]->absent_hours . "\t";
            $msg .= "\nUT HRS: " . $arrData[0]->undertime_hours . "\n";
            $msg .= "----------------------------------------------\n";
        }

        $msg .= "ALLOWANCES: " . number_format($arrData[0]->total_allowances, 2) . "\n";
        
        if($arrData[0]->ot_amount > 0) {
            $msg .= "OVERTIME: ";
            if($arrData[0]->ot_ndiff_amount > 0) {
                $msg .= number_format($arrData[0]->ot_amount + $arrData[0]->ot_ndiff_amount, 2) . "\n";
            } else {
                $msg .= $arrData[0]->ot_amount . "\n";
            }
            $msg .= "OT HRS: " . number_format($arrData[0]->ot_hours, 2) . "\t";
            $msg .= "NDIFF HRS: " . number_format($arrData[0]->ot_ndiff_hours, 2) . "\n";
        }
        
        if($arrData[0]->adjustment_e_count > 0) {
            $msg .= "ADJUSTMENTS:\n";
            foreach ($arrData[0]->adjustment_earnings as $kk => $vv) {
                $msg .= strtoupper($vv->label) . ": " . $vv->display_value . "\n";
            }
        }

        $msg .= "----------------------------------------------\n";
        $msg .= "GROSS PAY: " . $arrData[0]->gross_pay . "\n";

        if(intval($arrData[0]->is_bonus) == 0) {
            if(floatval($arrData[0]->sss) != 0 || floatval($arrData[0]->sss_prov) != 0 || floatval($arrData[0]->ph) != 0 || floatval($arrData[0]->hdmf) != 0 || floatval($arrData[0]->tax) != 0 || floatval($arrData[0]->sss_loan) != 0 || floatval($arrData[0]->hdmf_loan) != 0) {
                $msg .= "----------------------------------------------\n";
                $msg .= "DEDUCTIONS:\n\n";
    
                if($arrData[0]->sss && floatval($arrData[0]->sss) > 0) {
                    $msg .= "SSS: " . $arrData[0]->sss . "\n";
                }
    
                if($arrData[0]->sss_prov && floatval($arrData[0]->sss_prov) > 0) {
                    $msg .= "SSS PROVIDENT: " . $arrData[0]->sss_prov . "\n";
                }
    
                if($arrData[0]->ph && floatval($arrData[0]->ph) > 0) {
                    $msg .= "PHILHEALTH: " . $arrData[0]->ph . "\n";
                }
    
                if($arrData[0]->hdmf && floatval($arrData[0]->hdmf) > 0) {
                    $msg .= "HDMF: " . $arrData[0]->hdmf . "\n";
                }
    
                if($arrData[0]->tax && floatval($arrData[0]->tax) > 0) {
                    $msg .= "TAX: " . $arrData[0]->tax . "\n";
                }
    
                if($arrData[0]->deductions && floatval($arrData[0]->deductions) > 0){
                    if($arrData[0]->sss && floatval($arrData[0]->sss) > 0) {
                        $_tempDeductions += floatval($arrData[0]->sss);
                    }
                    if($arrData[0]->sss_prov && floatval($arrData[0]->sss_prov) > 0) {
                        $_tempDeductions += floatval($arrData[0]->sss_prov);
                    }
                    if($arrData[0]->ph && floatval($arrData[0]->ph) > 0) {
                        $_tempDeductions += floatval($arrData[0]->ph);
                    }
                    if($arrData[0]->hdmf && floatval($arrData[0]->hdmf) > 0) {
                        $_tempDeductions += floatval($arrData[0]->hdmf);
                    }
                    if($arrData[0]->tax && floatval($arrData[0]->tax) > 0) {
                        $_tempDeductions += floatval($arrData[0]->tax);
                    }
                    

                    $msg .= "TOTAL DEDUCTIONS: ". number_format($_tempDeductions, 2) . "\n";
                }
            }

            if($arrData[0]->total_loans && (floatval($arrData[0]->total_loans) > 0 || (is_array($arrData[0]->loans) && count($arrData[0]->loans) > 0))){
                $created_adjustment = $arrData[0]->created_adjustments;

                $msg .= "----------------------------------------------\n";

                $msg .= "LOANS:\n\n";

                foreach ($arrData[0]->loans as $kk => $vv) {
                    if($vv->amount_due > 0){
                        if (strtolower($vv->loan_name) == 'charges' || strtolower($vv->loan_name) == 'under deduction' || strtolower($vv->loan_name) == 'medical loan') {

                            $_data = array(
                                'label' => $vv->loan_name,
                                'display_value' => $vv->amount_due
                            );

                            $arrData[0]->adjustment_deductions[] = (object) $_data;
                            $arrData[0]->adjustment_d_count++;

                            unset($arrData[0]->loans[$kk]);
                        }

                        if (strtolower($vv->loan_name) != 'charges' && strtolower($vv->loan_name) != 'under deduction' && strtolower($vv->loan_name) != 'medical loan') {
                            $temp_amountDue = floatval(preg_replace('/[^\d\.\-]/', '', $vv->amount_due));

                            if (isset($created_adjustment) && $created_adjustment) {
                                $_tempCreated = explode(",", $created_adjustment);
                                $tempAdj = 0;

                                foreach ($_tempCreated as $_key => $_value) {
                                    $temp_adjustment = explode("||", $_value);
                                    $adj_type = isset($temp_adjustment[2]) ? intval($temp_adjustment[2]) : 0;
                                    $temp_status = isset($temp_adjustment[3]) ? intval($temp_adjustment[3]) : 0;

                                    $_temp = floatval(preg_replace('/[^\d\.\-]/', '', $vv->amount_due));

                                    $_temp = $adj_type == 1 ? floatval($temp_amountDue) + floatval($temp_adjustment[1]) : floatval($temp_amountDue) - floatval($temp_adjustment[1]);
                                    $tempAdj = $_temp;

                                    $temp_amountDue = ($temp_adjustment[0] == "LOAN" && $temp_status === 1) ? $_temp : $temp_amountDue;

                                    if (isset($vv->loan_name) && strtolower($vv->loan_name) == 'cash advance') {
                                        if ($temp_adjustment[0] == "LOAN" && $temp_status === 1) {
                                            $arrData[0]->loans[$kk]->amount_due = $temp_amountDue;
                                        }
                                    } else {
                                        if ($temp_adjustment[0] == "LOAN" && $temp_status === 1) {
                                            $loan = array_column($arrData[0]->loans, 'loan_name');
                                            
                                            if (!in_array('CASH ADVANCE', $loan)) {
                                                $arrData[0]->loans[] = (object) array(
                                                    'loan_name' => 'CASH ADVANCE',
                                                    'amount_due' => $temp_adjustment[1],
                                                    'loan_type' => $adj_type
                                                );
                                            }
                                        }
                                    }
                                }
                            }

                            $arrData[0]->loans[$kk]->amount_due = floatval(preg_replace('/[^\d\.\-]/', '', $vv->amount_due));
                        }
                    }
                }

                foreach ($arrData[0]->loans as $kk => $vv) {
                    if($vv->amount_due > 0){
                        $msg .= $vv->loan_name . ": " . number_format($vv->amount_due, 2) . "\n";
                        $temp_totalLoans += floatval(preg_replace('/[^\d\.\-]/', '', $vv->amount_due));
                    }
                }

                $msg .= "TOTAL LOANS: ". number_format($temp_totalLoans, 2) . "\n";
            }

            if($arrData[0]->total_loans_interest && floatval($arrData[0]->total_loans_interest) > 0){
                $msg .= "----------------------------------------------\n";
                $msg .= "TOTAL LOANS INTEREST: ".$arrData[0]->total_loans_interest;
            }

            if(is_numeric($arrData[0]->adjustment_d_count) && intval($arrData[0]->adjustment_d_count) > 0){

                $msg .= "----------------------------------------------\n";

                $msg .= "OTHERS:\n\n";

                foreach ($arrData[0]->adjustment_deductions as $kk => $vv) {
                    $msg .= strtoupper($vv->label) . ": " . $vv->display_value . "\n";

                    $_temp_total_others += floatval(preg_replace('/[^\d\.\-]/', '', $vv->display_value));
                }

                $msg .= "TOTAL OTHER DEDUCTIONS: ". number_format($_temp_total_others, 2) . "\n";
            }

            if($arrData[0]->total_loans && (floatval($arrData[0]->total_loans) > 0 || (is_array($arrData[0]->loans) && count($arrData[0]->loans) > 0))) {
                $msg .= "----------------------------------------------\n";
                $overall_total_loans = floatval($_tempDeductions) + floatval($arrData[0]->total_loans_interest) + floatval($temp_totalLoans) + floatval($_temp_total_others);

                $msg .= "TOTAL LOANS & DEDUCTIONS: " . number_format($overall_total_loans, 2) . "\n";
            }
            
            $msg .= "----------------------------------------------\n";
        }

        $msg .= "NET PAY: " . $arrData[0]->net_pay . "\n";
        return $msg;
    }


    public function sendTelegram(){
      $post = $this->input->post();
      $resultset = array();
    
      foreach ($post["payslipId"] as $id) {
        $arrData = array();
        $psData = $this->getSelectedTelegram($id);
        if (!empty($psData) && isset($psData[0])) {
          $result = (object) $this->getCurrentPayrollPayslip($psData[0]->payslip_id);
    
          if ($result->response === true) {
            $arrData[] = $result->data;
            $data = $this->telegram_config_if_exist('payroll_payslip', 'data');
    
            if ($data) {
              $message = $this->createMessage($arrData);
              $telegrambot = $data->telegram_bot_token;
              $telegramchatid = $psData[0]->telegram_id;
              $url = 'https://api.telegram.org/bot' . $telegrambot . '/sendMessage';
              $data = array('chat_id' => $telegramchatid, 'text' => $message, 'parse_mode' => 'html');
              $options = array(
                'http' => array(
                  'method' => 'POST',
                  'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
                  'content' => http_build_query($data),
                  'ignore_errors' => true
                ),
              );
              $context = stream_context_create($options);
              $result = file_get_contents($url, false, $context);
              $resultset[] = array("payslip_id" => $id, "response" => true);
            }
          }
        }
      }
      return $resultset;
    }

    public function sendEmail(){
      $post = $this->input->post();
      $resultset = array();
      foreach ($post["payslipId"] as $key => $id) {
        $emailSent = false;
        $psData = $this->getSelectedEmail($id);
        if (!empty($psData) && isset($psData[0])) {
          $result = (object) $this->getCurrentPayrollPayslip($psData[0]->payslip_id);
          if ($result->response == true) {
            $tempEmployeeName = $result->data->employee_name;
            $module = "payroll_payslip";
            $email_title = "PAYROLL - Payslip Notification";
            $content_title = "Payroll Notification For ".$tempEmployeeName;
            $htmlContent = $this->load->view("payroll/payroll/email_template/payslipEmail",array('result' => $result),true);
            $overrideMailer = array();
            $overrideMailer["send_to"] = array($psData[0]->email);
            try{
                $sent = $this->core_layout->send_email($module, $email_title, $content_title, $htmlContent, $overrideMailer);
                if ($sent){ $emailSent = true; 
                $resultset[] = array("payslip_id" => $id, "response" => true);
                } else {
                $resultset[] = array("payslip_id" => $id, "response" => false);
                }
            }catch(Exception $e){
                $resultset["payslip_email_notification"] = $e->getMessage();
            }
          }
        }
      }
    return $resultset;
    }

    public function testEmail($id){
      $result = (object) $this->getCurrentPayrollPayslip($id);
      $this->load->view("payroll/payroll/email_template/payslipEmail",array('result' => $result));
    }  

    private function getSelectedTelegram($id){
      $data = array();
      $this->db->select("ps.id as payslip_id,ps.emp_id as emp_id, IFNULL(po.is_telegram, 0) as is_telegram, usr.telegram_chat_id as telegram_id");
      $this->db->from("payroll.payroll_sheet as ps");
      $this->db->join("gccmaster.tblemployees as emp", "emp.id = ps.emp_id", "LEFT");
      $this->db->join("gcchris.tblpayslip_options as po", "po.emp_id = emp.id", "LEFT");
      $this->db->join("gccmaster.tblusers as usr", "usr.emp_id = emp.id", "LEFT");
      $this->db->where("ps.id", $id);
      $this->db->where("po.is_telegram", 1);
      $query = $this->db->get();

      if ($query->num_rows() > 0) {
          $data = $query->result();
      }

      
      return $data;
  }

  private function getSelectedEmail($id){
    $data = array();
    $this->db->select("ps.id as payslip_id,ps.emp_id as emp_id, IFNULL(po.is_email, 0) as is_email, usr.email as email");
    $this->db->from("payroll.payroll_sheet as ps");
    $this->db->join("gccmaster.tblemployees as emp", "emp.id = ps.emp_id", "LEFT");
    $this->db->join("gcchris.tblpayslip_options as po", "po.emp_id = emp.id", "LEFT");
    $this->db->join("gccmaster.tblusers as usr", "usr.emp_id = emp.id", "LEFT");
    $this->db->where("ps.id", $id);
    $this->db->where("po.is_email", 1);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        $data = $query->result();
    }
    return $data;
  }
  
  private function telegram_config_if_exist($module, $data){
		$this->db->where("module",$module);
		$telegram_details = $this->db->get("payroll.telegram_config");
		$details = $telegram_details->row();
		$count = $telegram_details->num_rows();
		if($data == 'count'){
			return $count;
		}else{
			return $details;
		}
	}  

    public function setPrintablePayslipAknowledgement($ids=array()){
        $resultset = array();
        $post = $this->input->post();
        if(isset($ids) && $ids){ $post["ids"] = $ids; }
        if(isset($post) && $post){
            if(isset($post["ids"]) && is_array($post["ids"]) && count($post["ids"]) > 0){
                $arrData = array();
                foreach ($post["ids"] as $id) {
                    $result = (object) $this->getCurrentPayrollPayslip($id);
                    if($result->response === true){
                        $arrData[] = $result->data;
                    }
                }
                if(is_array($arrData) && !empty($arrData)){
                    $html = $this->load->view("payroll/payroll/printable/print_aknowledgement_content", array("data"=>$arrData), true);
                    $resultset["response"] = true;
                    $resultset["html"] = $html;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    public function setPrintablePayslipNetpay($ids=array()){
        $resultset = array();
        $post = $this->input->post();
        if(isset($ids) && $ids){ $post["ids"] = $ids; }
        if(isset($post) && $post){
            if(isset($post["ids"]) && is_array($post["ids"]) && count($post["ids"]) > 0){
                $parameters = $post["paramaters"];
                $otherData = array();
                $queryNeyTotal = $this->db
                    ->select("SUM(net_pay) as grand_total_netpay, GROUP_CONCAT(DISTINCT(pay_date)) as pay_date")
                    ->from($this->tbl_payroll_sheet)
                    ->where("posted", 1)
                    ->where_in("id", $post["ids"])
                    ->get();
                if($queryNeyTotal->num_rows() == 1){ $otherData = $queryNeyTotal->row(); }
                $_otherData = array_merge($parameters, (array) $otherData);
                $_otherData = (object) $_otherData;
                
                $arrData = array();
                foreach ($post["ids"] as $key => $id) {
                    $result = (object) $this->getCurrentPayrollPayslip($id);
                    if($result->response === true){
                        $arrData[] = $result->data;
                    }
                }
                if(is_array($arrData) && !empty($arrData)){
                    $html = $this->load->view("payroll/payroll/printable/print_netpay_total_content", array("data"=>$arrData, "other_data"=>$_otherData), true);
                    $resultset["response"] = true;
                    $resultset["html"] = $html;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
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

    function getPayrollGroupMultiple(){
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

    function getPayrollSettings(){
        $resultset = array();
        $this->core_layout->setPrivilegeName("payroll_sheet");
        $actions = $this->core_layout->getCurrentActions();
        $resultset["admin_access"] = in_array("approving_authority", $actions) ? true: false;
        
        $qTemp = $this->db->get("payroll.settings");
        if($qTemp->num_rows() > 0){
            $resultset["response"] = true;
            $resultset["data"] = $qTemp->result();
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }
    function updatePayrollSettings(){
        $post = $this->input->post();
        $resultset = array();
        if(isset($post) && $post){
            $updateCount = 0;
            if(isset($post["ftmi_prop_switch"]) && $post["ftmi_prop_switch"]){
                $tempStatus = $post["ftmi_prop_switch"] == "true"? true: false;
                unset($post["ftmi_prop_switch"]);
                if($tempStatus == false){ $post["fixed_tax_monthly_income_switch"] = "0"; }
            }

            foreach($post as $kk => $vv){
                $tempWhere = array("setting_name"=>$kk);
                $tempData = array("setting_value"=>$vv);
                $updated = $this->db->update("payroll.settings", $tempData, $tempWhere);
                if($updated && $this->db->affected_rows() > 0){
                    $qSettings = $this->db->get_where("payroll.settings", $tempWhere);
                    if($qSettings->num_rows() === 1){
                        $tempRow = $qSettings->row();
                        $tempValue = $tempRow->setting_value;
                        if($tempRow->setting_name === "fixed_tax_monthly_income_switch"){
                            $tempValue = intval($tempValue) === 1 ? "ACTIVE": "INACTIVE";
                        }
                        $tempDescription = strtoupper($tempRow->setting_description);
                        $logMessage = "Payroll sheet settings for  of `{$tempDescription}` has been set to `{$tempValue}`.";
                        $this->core_layout->setEventLog($logMessage, "update", "success", "payroll");   
                    }
                    $updateCount++;
                }
            }
            $resultset["response"] = $updateCount > 0;
            $resultset["taostr_msg"] = "Payroll setting(s) has been updated.";
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function updateSssContributionBasis(){
        $post = $this->input->post();
        $resultset = array();
        if(isset($post) && $post){
            $updateCount = 0;
            foreach($post as $kk => $vv){
                $tempWhere = array("setting_name"=>$kk);
                $tempData = array("setting_value"=>$vv);
                $updated = $this->db->update("payroll.settings", $tempData, $tempWhere);
                if($updated && $this->db->affected_rows() > 0){
                    $tempName = strtoupper(str_replace("_", " ", $kk));
                    $tempValue = strtoupper(str_replace("_", " ", $vv));
                    $logMessage = "Payroll sheet sss contribution settings for `{$tempName}` has been set to `{$tempValue}`.";
                    $this->core_layout->setEventLog($logMessage, "update", "success", "payroll");
                    $updateCount++;
                }
            }
            $resultset["response"] = $updateCount > 0;
            $resultset["taostr_msg"] = "Payroll setting(s) has been updated.";
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function updateZeroNetpayParameters(){
        $post = $this->input->post();
        $resultset = array();

        $post["enable_zero_netpay"] = isset($post["enable_zero_netpay"]) && $post["enable_zero_netpay"] ? intval($post["enable_zero_netpay"]): 0;
        if(isset($post) && $post){
            $tempValue = $post["enable_zero_netpay"] ;
            $updated = $this->db->update("payroll.settings", array("setting_value"=>$tempValue), array("setting_name"=>"enable_zero_netpay"));
            if($updated && $this->db->affected_rows() > 0){
                $nValue = $tempValue == 1 ? "enabled": "disabled";
                $logMessage = "Payroll sheet zero netpay settings has been `{$nValue}`.";
                $this->core_layout->setEventLog($logMessage, "update", "success", "payroll");

                $resultset["response"] = true;
                $resultset["taostr_msg"] = "Payroll zero netpay setting(s) has been updated.";
            }else{
                $resultset["response"] = false;
                $resultset["taostr_msg"] = "Updating of payroll zero netpay setting(s) failed!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["taostr_msg"] = "No post data found!";
        }

        return $resultset;
    }

    function getContributionDeductionAccountNumber($empId=null){
        $props = array("phealth_no", "pagibig_no", "sss_no", "tin_no");
        $tempStdProps = new stdClass();
        foreach ($props as $key => $value) { $tempStdProps->$value = false; }
        if($empId){
            $sqlSelect = implode(",", $props);
            $this->db->select($sqlSelect);
            $this->db->from($this->tbl_employees);
            $this->db->where("id", $empId);
            $qTemp = $this->db->get();
            if($qTemp->num_rows() == 1){
                $tempRow = $qTemp->row();
                foreach ($props as $kk => $vv) {
                    $temp = preg_replace("/[^0-9]/", "", $tempRow->$vv);
                    $xtemp = intval($temp);
                    if($xtemp > 0){ $tempStdProps->$vv = true; }
                }
            }
        }
        
        return $tempStdProps;
    }

    function getCurrentContributionDeduction($employeeId=0, $month=null, $year=null){
        $tempData = array();
        if($employeeId && $month && $year){
            $params = $this->db->get_where($this->tbl_ps_remittance_parameters, array("status"=>1));
            if($params->num_rows() > 0){
                $adjParams = array();
                $payrollParams = array("a.id", "a.date_start", "a.date_end", "a.pay_date", "a.payroll_seq", "a.basic_rate", "a.gross_pay");
                $tempParams = array();
                foreach ($params->result() as $key => $value) {
                    $tempParams[] = $value->remittance_code == "PHIC" ? "a.ph": "a.".strtolower($value->remittance_code);
                    $adjParams[] = $value->remittance_code;
                }
                $this->db->reset_query();

                $newParams = array_merge($payrollParams, $tempParams);
                $sqlSelect = implode(",", $newParams);
                $sqlSelect .= ",GROUP_CONCAT(DISTINCT(CONCAT(b.particulars,'||',b.amount, '||', b.adj_type))) as created_adjustments";
                $this->db->select($sqlSelect);
                $this->db->from($this->tbl_payroll_sheet." a");
                $this->db->join($this->tbl_ps_created_adjustments." b", "b.payroll_sheet_id = a.id AND b.status = 1");
                $this->db->join($this->tbl_employees." c", "c.id = a.emp_id");
                $this->db->where(array(
                    "a.emp_id"=>$employeeId, 
                    "a.month_name"=>$month, 
                    "a.year"=>$year, 
                    "a.posted"=>1, 
                    "c.employee_status"=>"Active"));
                $this->db->where_in("b.particulars", $adjParams);
                $this->db->group_by("a.id");
                $this->db->order_by("c.lastname", "ASC");
                $tempPsQuery = $this->db->get();

                if($tempPsQuery->num_rows() > 0){
                    $tempFields = $tempPsQuery->list_fields();
                    $tempFields = array_diff($tempFields, array("id", "created_adjustments"));
                    $currentFields = array_merge(array("employee_name"), $tempFields);

                    $resultData = array();
                    foreach ($tempPsQuery->result() as $key => $row) {
                        $row->basic_rate = number_format($row->basic_rate, 2, ".", ",");
                        $row->gross_pay = number_format($row->gross_pay, 2, ".", ",");
                        
                        $createdAdjustment = $row->created_adjustments;
                        unset($row->created_adjustments);
                        if($createdAdjustment){
                            $tempAdjustments = explode(",", $createdAdjustment);
                            if(is_array($tempAdjustments) && count($tempAdjustments) > 0){
                                foreach ($tempAdjustments as $key1 => $value1) {
                                    $xx1 = explode("||", $value1);
                                    if(is_array($xx1) && count($xx1) == 3){
                                        $tempKey = strtolower($xx1[0]);
                                        $tempValue = floatval($xx1[1]);
                                        $tempOperand = intval($xx1[2]);
                                        if($tempKey){
                                            $tempKey = $tempKey == "phic" ? "ph": $tempKey;
                                            $tempAmount = floatval($row->{$tempKey});
                                            if($tempOperand == 0){
                                                if(isset($row->{$tempKey})){ $tempAmount -= $tempValue; }
                                            }else{ 
                                                if(isset($row->{$tempKey})){ $tempAmount += $tempValue; }
                                            }

                                            $row->{$tempKey} = $tempAmount;
                                        }
                                    }
                                }
                            }
                        }

                        if(count($adjParams) > 0){
                            foreach ($adjParams as $value) {
                                $value = strtolower($value) == "phic" ? "ph": strtolower($value);
                                $row->{$value} = number_format($row->{$value}, 2, ".", ",");
                            }
                        }
                        $resultData[$key] = $row;

                        if(count($resultData) > 0){
                            $tempData["data"] = $resultData;
                            $tempData["column_fields"] = $currentFields;
                        }
                    }
                }
            }
        }

        return $tempData;
    }

    function generateEmployeesNoContAcct(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post["employee_account"]) && is_array($post["employee_account"]) && count($post["employee_account"]) > 0){
            $arrData = array();
            $propsAcctNo = array("sss_no"=>"SSS #", "phealth_no"=>"PHILHEALTH #", "pagibig_no"=>"PAGIBIG #", "tin_no"=>"TIN #");
            foreach ($post["employee_account"] as $key => $value) {
                $tempRs = (object) $value;
                $tempRecord = $this->core_layout->getEmployeeData($tempRs->id);
                $tempName = (object) $tempRecord;
                $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1) ? strtoupper($tempName->display_name_1): strtoupper("No assigned name");
                $tempRow = new stdClass();
                $tempRow->name = $tempName;
                $contAcct = array();
                if(isset($tempRs->no_acct) && $tempRs->no_acct){
                    foreach ($tempRs->no_acct as $kk => $vv) {
                        $tempDescription = $propsAcctNo[$vv];
                        $contAcct[] = $tempDescription;
                    }
                }
                $tempRow->contribution_account = $contAcct;
                $arrData[$tempRs->id] = $tempRow;

            }
            $resultset["response"] = true;
            $resultset["data"] = $arrData;
            $resultset["count"] = count($arrData);
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    public function updateEmployeeCompany(){
        $updatedEmployeeRecord = array();
        $this->db->select("ps.emp_id, IFNULL(comp.id, 0) as company_id");
        $this->db->from("payroll.payroll_sheet ps");
        $this->db->join("gccmaster.tblemployees emp", "emp.id = ps.emp_id");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = emp.company_id OR comp.code = emp.company_id OR comp.description = emp.company_id", "LEFT");
        $this->db->where("ps.company_id", 0);
        $this->db->group_by("ps.emp_id");
        $qTemp = $this->db->get();
        if($qTemp->num_rows() > 0){
            foreach ($qTemp->result() as $key => $value) {
                $data = array("company_id"=>$value->company_id);
                $where = array("emp_id"=>$value->emp_id);
                $updated = $this->db->update("payroll.payroll_sheet", $data, $where);
                if($updated && $this->db->affected_rows() > 0){
                    $updatedEmployeeRecord[] = $value->emp_id;
                }
            }
        }
        return $updatedEmployeeRecord;
    }

    function createPrintableSignatory(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $arrMetaValue = array();
            $tempLabel = $post["label"];
            $tempValue = $post["value"];
            unset($post["label"], $post["value"]);

            if(isset($tempLabel) && is_array($tempLabel) && count($tempLabel) > 0){
                foreach ($tempLabel as $key => $value) {
                    $rowMeta = array();
                    $label = $value ? strtoupper(trim($value)): $value;
                    $empName = isset($tempValue[$key]) && $tempValue[$key] ? $tempValue[$key]: "";
                    $empName = $empName ? strtoupper(trim($empName)): $empName;

                    $rowMeta["label"] = $label;
                    $rowMeta["value"] = $empName;
                    $arrMetaValue[$key] = $rowMeta;
                }
            }
            
            $qSearch = $this->db->get_where($this->tbl_ps_signatory, array("company_id"=>$post["company_id"], "type"=>$post["type"]));
            if($qSearch->num_rows() == 0){
                $post["meta_field"] = serialize($arrMetaValue);
                $post["created_by"] = $this->core_layout->getCurrentEmployeeId();
                $post["created_at"] = date("Y-m-d H:i:s");

                $add = $this->db->insert($this->tbl_ps_signatory, $post);
                if($add){
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Signatory data has been added.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to add signatory data!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to add signatory data, signatory company and type already exist!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }

    function updatePrintableSignatory(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $tempWhere = array();
            $tempWhere["id"] = $post["id"];

            $arrMetaValue = array();
            $tempLabel = $post["label"];
            $tempValue = $post["value"];
            unset($post["label"], $post["value"], $post["id"]);

            if(isset($tempLabel) && is_array($tempLabel) && count($tempLabel) > 0){
                foreach ($tempLabel as $key => $value) {
                    $rowMeta = array();
                    $label = $value ? strtoupper(trim($value)): $value;
                    $empName = isset($tempValue[$key]) && $tempValue[$key] ? $tempValue[$key]: "";
                    $empName = $empName ? strtoupper(trim($empName)): $empName;

                    $rowMeta["label"] = $label;
                    $rowMeta["value"] = $empName;
                    $arrMetaValue[$key] = $rowMeta;
                }
            }

            $post["meta_field"] = serialize($arrMetaValue);
            $post["updated_by"] = $this->core_layout->getCurrentEmployeeId();
            $post["updated_at"] = date("Y-m-d H:i:s");
            
            $updated = $this->db->update($this->tbl_ps_signatory, $post, $tempWhere);
            if($updated){
                $resultset["response"] = true;
                $resultset["toastr_msg"] = "Signatory data has been updated.";
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to update signatory data!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }
        return $resultset;
    }

    function getPayrollSignatory(){
        $rowCount = 0;
        $rowData = array();
        $resultset = array();
        $post = $this->input->post();

        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
        $filteredId = (isset($post["ids"]) && $post["ids"]) ? $post["ids"] : array();
        $clearTable = (isset($post["clear_table"]) && $post["clear_table"] == "true") ? true : false;

        $rowData = $this->printableSignatoryList($search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->printableSignatoryListCount($search);
        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = isset($rowData["data"]) && $rowData["data"]? $rowData["data"]: array();

        return $resultset;
    }

    function printableSignatoryList($search, $limit, $offset, $sortBy, $sortOrder){
        $filterFields = array("b.code", "a.meta_field");
        $sqlSelect = "a.id, b.code as company, IF(a.type = 1, 'Payroll Sheet', 'Reports') as type, a.meta_field";
        $this->db->select($sqlSelect);
        $this->db->from('payroll.ps_signatory a');
        $this->db->join('gcchris.tblcompanies b', 'b.id = a.company_id');
        if (isset($search)) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }
        $this->db->group_by("a.id");
        if (isset($sortOrder)) {
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        } else {
            $this->db->order_by('b.code', 'asc');
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $data = array();
            foreach ($query->result() as $key => $item) {
                $metaField = @unserialize($item->meta_field);
                if(is_array($metaField) && count($metaField) > 0){
                    $item->meta_field = $metaField;
                }else{
                    $item->meta_field = array();
                }
                $data[$key] = $item;                
            }

            $resultset = array();
            $resultset["data"] = $data;
            return $resultset;
        } else {
            return array();
        }
    }

    function printableSignatoryListCount($search){
        $count = 0;
        $filterFields = array("b.code", "a.meta_field");
        $sqlSelect = "a.id, b.code as company, IF(a.type = 1, 'Payroll Sheet', 'Reports') as type, a.meta_field";
        $this->db->select($sqlSelect);
        $this->db->from('payroll.ps_signatory a');
        $this->db->join('gcchris.tblcompanies b', 'b.id = a.company_id');
        if (isset($search)) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }
        $this->db->group_by("a.id");

        $query = $this->db->get();
        $count = $query->num_rows();

        return $count;
    }

    public function getCurrentSignatory($id=null){
        $resultset = array();
        if($id){
            $this->db->select("a.*, b.code as company_description");
            $this->db->join($this->tbl_tblcompanies." b", "b.id = a.company_id");
            $qTemp = $this->db->get_where($this->tbl_ps_signatory." a", array("a.id"=>$id));
            if($qTemp->num_rows() == 1){
                $tempRow = $qTemp->row();
                $metaField = @unserialize($tempRow->meta_field);
                if(is_array($metaField) &&  count($metaField) > 0){ $metaField = $metaField; }
                else{ $metaField = array(); }
                $tempRow->meta_field = $metaField;
                $resultset["response"] = true;
                $resultset["data"] = $tempRow;
                $resultset["count"] = count($metaField);
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    public function getCurrentSignatoryByCompanyAndType($id=null, $type=null){
        $resultset = array();
        if($id && $type){
            $userId = $this->core_layout->getCurrentEmployeeId();
            $this->db->select("a.*, a.id as signatory_id, IFNULL(b.id, 0) as id, b.meta_field as altered_field, b.user_id");
            $this->db->from($this->tbl_ps_signatory." a");
            $this->db->join($this->tbl_temp_signatory." b", "b.signatory_id = a.id", "LEFT");
            $this->db->where("a.company_id", $id);
            $this->db->where("a.type", $type);
            /*** $this->db->or_where("b.user_id", $userId); ***/
            $this->db->limit(1);
            $this->db->order_by("a.id", "DESC");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() == 1){
                $tempRow = $qTemp->row();
                $tempRow->user_id = $tempRow->user_id? $tempRow->user_id: $userId;

                $metaField = @unserialize($tempRow->meta_field);
                $alteredField = @unserialize($tempRow->altered_field);
                if(is_array($metaField) &&  count($metaField) > 0){
                    foreach ($metaField as $key => $value) {
                        $metaField[$key]["is_active"] = isset($metaField[$key]["value"]) && $metaField[$key]["value"] ? true: false;
                    }
                    $metaField = $metaField;
                }
                else{ $metaField = array(); }

                $md5metaFields = md5(serialize($metaField));
                $md5alteredFields = md5(serialize($alteredField));

                $tempRow->_meta_field = $md5metaFields;
                $tempRow->_altered_field = $md5alteredFields;

                $tempRow->allow_reset = true;
                if($md5metaFields == $md5alteredFields){
                    $tempRow->allow_reset = false;
                }else if(is_array($alteredField) && count($alteredField) == 0){
                    $tempRow->allow_reset = false;
                }else if(intval($tempRow->id) == 0){
                    $tempRow->allow_reset = false;
                }

                if(is_array($alteredField) && count($alteredField) > 0){
                    $metaField = $alteredField;
                }
                $tempRow->meta_field = $metaField;
                $resultset["response"] = true;
                $resultset["data"] = $tempRow;
                $resultset["count"] = count($metaField);
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function getCurrentUpdatedSignatory($id=null){
        $data = array();
        if($id){
            $this->db->select("b.*, b.id as signatory_id, a.id, a.meta_field as altered_field, a.user_id");
            $this->db->join($this->tbl_ps_signatory." b", "b.id = a.signatory_id");
            $qtemp = $this->db->get_where($this->tbl_temp_signatory." a", array("a.id"=>$id));
            if($qtemp->num_rows() == 1){
                $tempRow = $qtemp->row();
                $metaField = @unserialize($tempRow->meta_field);
                $alteredField = @unserialize($tempRow->altered_field);
                if(is_array($metaField) &&  count($metaField) > 0){
                    foreach ($metaField as $key => $value) {
                        $metaField[$key]["is_active"] = isset($metaField[$key]["value"]) && $metaField[$key]["value"] ? true: false;
                    }
                    $metaField = $metaField;
                }
                else{ $metaField = array(); }

                $md5metaFields = md5(serialize($metaField));
                $md5alteredFields = md5(serialize($alteredField));

                $tempRow->_meta_field = $md5metaFields;
                $tempRow->_altered_field = $md5alteredFields;

                $tempRow->allow_reset = true;
                if($md5metaFields == $md5alteredFields){
                    $tempRow->allow_reset = false;
                }else if(is_array($alteredField) && count($alteredField) == 0){
                    $tempRow->allow_reset = false;
                }

                if(is_array($alteredField) && count($alteredField) > 0){
                    $metaField = $alteredField;
                }
                $tempRow->meta_field = $metaField;
                $data = $tempRow;
            }
        }
        return $data;
    }
    
    function resetPrintableSignatories(){
        $resultset = array();
        $post = $this->input->post();
        $userId = $this->core_layout->getCurrentEmployeeId();
        if(isset($post) && $post){
            $data = array();
            $data["meta_field"] = serialize(array());
            $data["updated_by"] = $userId;
            $data["updated_at"] = date("Y-m-d H:i:s");
            $updated = $this->db->update($this->tbl_temp_signatory, $data, $post);
            if($updated){
                $where = array("a.id"=>$post["id"]);
                $this->db->select("b.*, b.id as signatory_id, a.id, a.meta_field as altered_field, a.user_id");
                $this->db->join($this->tbl_ps_signatory." b", "b.id = a.signatory_id");
                $qTemp = $this->db->get_where($this->tbl_temp_signatory." a", $where);
                if($qTemp->num_rows() == 1){
                    $tempRow = $qTemp->row();
                    $metaField = @unserialize($tempRow->meta_field);
                    $alteredField = @unserialize($tempRow->altered_field);
                    if(is_array($metaField) &&  count($metaField) > 0){
                        foreach ($metaField as $key => $value) {
                            $metaField[$key]["is_active"] = isset($metaField[$key]["value"]) && $metaField[$key]["value"] ? true: false;
                        }
                        $metaField = $metaField;
                    }
                    else{ $metaField = array(); }

                    $md5metaFields = md5(serialize($metaField));
                    $md5alteredFields = md5(serialize($alteredField));

                    $tempRow->_meta_field = $md5metaFields;
                    $tempRow->_altered_field = $md5alteredFields;

                    $tempRow->allow_reset = true;
                    if($md5metaFields == $md5alteredFields){
                        $tempRow->allow_reset = false;
                    }else if(is_array($alteredField) && count($alteredField) == 0){
                        $tempRow->allow_reset = false;
                    }

                    if(is_array($alteredField) && count($alteredField) > 0){
                        $metaField = $alteredField;
                    }
                    $tempRow->meta_field = $metaField;

                    $resultset["response"] = true;
                    $resultset["data"] = $tempRow;
                    $resultset["count"] = count($metaField);
                }else{
                    $resultset["response"] = false;
                }
            }else{

                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function updatePrintableSignatories(){
        $resultset = array();
        $post = $this->input->post();
        $userId = $this->core_layout->getCurrentEmployeeId();
        if(isset($post) && $post){
            $tempId = (isset($post["id"]) && intval($post["id"]) > 0) ? intval($post["id"]): 0;
            if(isset($post["value"]) && $post["value"]){
                $tempValues = $post["value"];
                unset($post["id"], $post["value"]);

                $temp = $this->db->get_where($this->tbl_ps_signatory, array("id"=>$post["signatory_id"]));
                if($temp->num_rows() == 1){
                    $row = $temp->row();
                    $metaField = @unserialize($row->meta_field);
                    if(is_array($metaField) && count($metaField) > 0){
                        foreach ($metaField as $key => $value) {
                            $tempValuex = (object) $value;
                            $tempMetaField = array();
                            $tempMetaField["label"] = $tempValuex->label;
                            $tempMetaField["value"] = $tempValuex->value;
                            $tempMetaField["is_active"] = true;
                            if(isset($tempValues[$key]) && $tempValues[$key]){ $tempMetaField["value"] = $tempValues[$key]; }
                            else{ $tempMetaField["is_active"] = false; }
                            $metaField[$key] = $tempMetaField;
                        }
                    }
                    $newMetaField = serialize($metaField);
                    $post["meta_field"] = $newMetaField;

                    if($tempId){
                        $tempWhere = array();
                        $tempWhere["id"] = $tempId;
                        $post["updated_by"] = $userId;
                        $post["updated_at"] = date("Y-m-d H:i:s");
                        $updated = $this->db->update($this->tbl_temp_signatory, $post, $tempWhere);
                        if($updated){
                            $updatedSignatory = $this->getCurrentUpdatedSignatory($tempId);
                            $resultset["response"] = true;
                            $resultset["data"] = $updatedSignatory;
                        }
                    }else{
                        $post["created_by"] = $userId;
                        $post["created_at"] = date("Y-m-d H:i:s");
                        $added = $this->db->insert($this->tbl_temp_signatory, $post);
                        if($added){
                            $tempId = $this->db->insert_id();
                            $updatedSignatory = $this->getCurrentUpdatedSignatory($tempId);
                            $resultset["response"] = true;
                            $resultset["data"] = $updatedSignatory;
                        }
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "No signatories found!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No signatories left, please leave atleast 1 signatory!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }


    function monthlyPaidBonusChecker($emp_id, $date_start, $date_end){
        $checker = array();
        $result = $this->db->get_where("payroll.payroll_sheet", array("emp_id"=>$emp_id, "date_start"=>$date_start, "date_end"=>$date_end, "is_bonus"=>1, "posted"=>1));
        $checker['data'] = $result->row();
        $checker['date_start'] = $result->row('date_start');
        $checker['date_end'] = $result->row('date_end');
        $checker['count'] = $result->num_rows();

        return $checker;
    }

    function getBonusCutOff($emp_id, $date_start, $date_end){
        $this->db->select("date_start, date_end");
        $this->db->from("payroll.payroll_sheet");
        $this->db->where("posted", 1);
        $this->db->where("emp_id", $emp_id);
        $this->db->where("date_start >=", $date_start);
        $this->db->where("date_end <=", $date_end);
        $data = $this->db->get();
        $result = array();
        foreach($data->result_array() as $data_temp){
            $result[] = $data_temp['date_start'];
            $result[] = $data_temp['date_end'];
        }
        return $result;
    }

    function getAllPaidDates($emp_id){
        $this->db->select("pay_date");
        $this->db->from("payroll.payroll_sheet");
        $this->db->where("posted", 1);
        $this->db->where("emp_id", $emp_id);
        $this->db->group_by("pay_date");
        $data = $this->db->get();
        $pay_dates_arr = array();
        foreach($data->result_array() as $pay_dates){
            $pay_dates_arr[] = $pay_dates['pay_date'];
        }
       
        return $pay_dates_arr;
    }

    // function to display payroll summary datatable
    function getPayrollSummaryRequest(){
        $rowCount = 0;
        $rowData = array();
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
        $filteredId = (isset($post["ids"]) && $post["ids"]) ? $post["ids"] : array();
        $clearTable = (isset($post["clear_table"]) && $post["clear_table"] == "true") ? true : false;
        $filters = (isset($post["filters"]) && $post["filters"]) ? $post["filters"] : array();

        $rowData = $this->payrollSummaryReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder, $filters);
        $rowCount = $this->payrollSummaryReportListCount($filteredId, $search);

        $totalNotFiltered = $rowCount;

        if($clearTable == true){
            $rowCount = 0;
            $rowData = array();
        }

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = isset($rowData["data"]) && $rowData["data"]? $rowData["data"]: array();
        $resultset["clear_table"] = $clearTable;
        return $resultset;
    }
    // end function

    // function to display data for datatable of payroll summary request
    function payrollSummaryReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder, $filters){
        if(is_array($filteredId) && count($filteredId) > 0){
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.idno,
            a.gross_pay as gross_pay,  a.basic_rate as basic_rate, a.pay_date as pay_date, b.biometricno, a.rate, a.date_start, a.date_end, IFNULL(c.name, 'semi-monthly') as payout_schedule";

            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id', "LEFT");
            $this->db->join('payroll.payout_schedule c', 'c.id = b.payout_sched', "LEFT");

            $filteredIdChunk = array_chunk($filteredId, 25);
            $this->db->group_start();
            foreach ($filteredIdChunk as $filteredIds) {
                $this->db->or_where_in("a.id", $filteredIds);
            }
            $this->db->group_end();

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
        
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $this->db->order_by("b.lastname, b.id, a.pay_date","asc");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $data = array();
                foreach ($query->result() as $key => $item) {
                    $tempGrandtotal = $item->basic_rate;
                    if((isset($filters['filter_by']) && $filters['filter_by'] == 'INCENTIVE') || (isset($filters['bonus']) && $filters['bonus'] == 'on')){
                        $coverage = explode("-", $filters['coverage_date']);
                        $coverage_start = date("Y-m-d", strtotime($coverage[0]));
                        $coverage_end = date("Y-m-d", strtotime($coverage[1]));

                        $tempGrandtotal = $item->basic_rate / 12;

                        $date_start = $this->monthlyPaidBonusChecker($item->emp_id, $coverage_start, $coverage_end);
                        $date_end = $this->monthlyPaidBonusChecker($item->emp_id, $coverage_start, $coverage_end);

                        $payCoverageDate = date("m/d/Y", strtotime($date_start['date_start']))." - ".date("m/d/Y", strtotime($date_start['date_end']));
                        if($this->monthlyPaidBonusChecker($item->emp_id, $coverage_start, $coverage_end)['count'] > 0){
                        $paydates_of_bonus = $this->getBonusCutOff($item->emp_id, $date_start['date_start'], $date_end['date_end']);
                            if(in_array($item->date_start, $paydates_of_bonus) || in_array($item->date_end, $paydates_of_bonus)){
                                $item->pay_date = date("m/d/Y", strtotime($item->pay_date))." | "."<span class='m--font-success fa fa-check'><b></b></span>";
                            }else{
                                $item->pay_date = date("m/d/Y", strtotime($item->pay_date))." | "."<span class='m--font-danger fa fa-times mr-3'><b></b></span>".$payCoverageDate;
                            } 
                        }else{ $item->pay_date = date("m/d/Y", strtotime($item->pay_date))." | "."<span class='m--font-danger fa fa-times'><b></b></span>"; }
                    }else{
                        $item->pay_date = date("m/d/Y", strtotime($item->pay_date));
                    }
                    
                    $tempRecord = (object) $this->core_layout->getEmployeeData($item->emp_id);
                    $tempName = (isset($tempRecord->display_name_1) && $tempRecord->display_name_1)? $tempRecord->display_name_1: "No assigned name";
                    if($item->idno){ $idno = $item->idno; }
                    else{ $idno = "N/A"; }

                    $item->grandtotal = $tempGrandtotal;
                    $item->employee_name = $idno." - ".$tempName." | <b>".$item->payout_schedule."</b>";
                    $item->emp_id = $item->emp_id;
                    
                    $data[$key] = $item;
                }

                $resultset = array();
                $resultset["data"] = $data;
            
                return $resultset;
            } else {
                return array();
            }
        }else{
            return array();
        }
    }
    // end function

    // function to display number of entries of requested data of payroll summary
    function payrollSummaryReportListCount($filteredId, $search){
        $count = 0;
        if(is_array($filteredId) && count($filteredId) > 0){
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.idno,
            a.gross_pay as gross_pay,  a.basic_rate as basic_rate, a.pay_date as pay_date, b.biometricno, a.rate, a.date_start, a.date_end";

            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id', "LEFT");

            $filteredIdChunk = array_chunk($filteredId, 25);
            $this->db->group_start();
            foreach ($filteredIdChunk as $filteredIds) {
                $this->db->or_where_in("a.id", $filteredIds);
            }
            $this->db->group_end();

            $this->db->where("a.posted", 1);
            $this->db->group_by("a.id", "desc");
            $this->db->order_by('b.lastname', 'asc');
            $this->db->order_by("a.date_end","asc");
            $query = $this->db->get();
            $count = $query->num_rows();
        }

        return $count;
    }
    // end function

    function generatePayrollMonthlyWeekCount($year=null){
        $resultset = array();
        if($year){
            $temp_record = array();
            for($i=1; $i<=12; $i++){
                $nData = array();
                $monthName = date('F', mktime(0, 0, 0, $i, 1, $year));
                $monthName = strtolower($monthName);
                $d=cal_days_in_month(CAL_GREGORIAN,$i,$year);
                $weeks = $this->get_weeks($year, $i);
    
                $_prevIndex = $i - 1;
                $prevMonthIndex = $_prevIndex >= 1 ? $_prevIndex: 12;
                $prevYearIndex = $_prevIndex >= 1 ? intval($year): intval($year) - 1;
    
                $prevMonthWeeks = $this->get_weeks($prevYearIndex, $prevMonthIndex);
                $endPreviousMonthCount = count(end($prevMonthWeeks));
    
                $_nextIndex = $i + 1;
                $nextMonthIndex = $_nextIndex <= 12 ? $_nextIndex: 1;
                $nextYearIndex = $_nextIndex <= 12 ? intval($year): intval($year) + 1;
    
                $nextMonthWeeks = $this->get_weeks($nextYearIndex, $nextMonthIndex);
                $firstNextMonthCount = count(current($nextMonthWeeks));
    
                $tempKeys = array_keys($weeks);
                $nthKeyFirst = current($tempKeys);
                $nthKeyLast = end($tempKeys);
                $ctrWeeks = 0;
                $tempWeeks = array();
                foreach ($weeks as $key => $value) {
                    if($key == $nthKeyFirst){
                        $currentKey0 = count($weeks[$key]);
                        if($currentKey0 >= $endPreviousMonthCount){
                            $tempWeeks[$key] = $value;
                            $ctrWeeks++;
                        }
                    }else if($key == $nthKeyLast){
                        $currentKey1 = count($weeks[$key]);
                        if($currentKey1 >= $firstNextMonthCount){
                            $tempWeeks[$key] = $value;
                            $ctrWeeks++;
                        }
                    }else{
                        $tempWeeks[$key] = $value;
                        $ctrWeeks++;
                    }
                }

                $qTempRecord = $this->db->get_where($this->tbl_ps_week_counter, array("year"=>$year, "month_name"=>$monthName, "week_count"=>$ctrWeeks));
                if($qTempRecord->num_rows() == 0){
                    $added = $this->db->insert($this->tbl_ps_week_counter, array("year"=>$year, "month_name"=>$monthName, "week_count"=>$ctrWeeks, "weeks"=>serialize($tempWeeks)));
                    if($added){
                        $nData["type"] = "added";
                        $nData["month_name"] = $monthName;
                        $nData["year"] = $year;
                    }
                }else{
                    $nData["type"] = "existing";
                    $nData["month_name"] = $monthName;
                    $nData["year"] = $year;
                }
                $temp_record[] = $nData;
            }
            $resultset["response"] = true;
            $resultset["data"] = $temp_record;
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function get_weeks($year, $month){
        $days_in_month = date("t", mktime(0, 0, 0, $month, 1, $year));
        $weeks_in_month = 1;
        $weeks = array();
        for ($day=1; $day<=$days_in_month; $day++) {
            $week_day = date("w", mktime(0, 0, 0, $month, $day, $year));
            $weeks[$weeks_in_month][$week_day] = $day;
            if ($week_day == 6) {
                $weeks_in_month++;
            }
        }
        return $weeks;
    }

    function updatePayrollsheetPrintedStatus(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $whereInIds = $post["printed_id"];
            if(is_array($whereInIds) && count($whereInIds) > 0){
                $data = array();
                $data["printed_payslip"] = 1;
                $data["printed_payslip_by"] = $this->core_layout->getCurrentEmployeeId();
                $data["printed_payslip_date"] = date("Y-m-d H:i:s");

                $this->db->where_in("id", $whereInIds);
                $updated = $this->db->update($this->tbl_payroll_sheet, $data);
                if($updated && $this->db->affected_rows() > 0){
                    $resultset["response"] = true;
                    $resultset["data"] = $post;
                    $resultset["affected_rows"] = $this->db->affected_rows();
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    // for activation of cash advance loan
    function setCAStatus($id, $pay_date, $generatedDate){
        $result = array();
        if($id){
            $ctrUpdatedLoans = 0;
            foreach($id as $emp_id){
                $emp_name = $this->core_layout->getEmployeeData($emp_id)['display_name_1'];
                $data = array('active' => 1);
    
                $this->db->select('a.approved_dt, a.reference_no, a.amt_applied, a.purpose, b.id as loan_type_id');
                $this->db->from("gcceforms.cash_advance a");
                $this->db->join('gcchris.loans b', 'b.reference = a.reference_no');
                $this->db->where('a.employee', $emp_id);
                $this->db->where('b.active', 0);
                $this->db->order_by('a.created_dt', 'desc');
                $this->db->limit(1);
    
                $query = $this->db->get();
                if($query->num_rows() > 0){
                    $rows = $query->row();
                    $this->db->where('id', $rows->loan_type_id);
                    $query = $this->db->update('gcchris.loans', $data);
                    if($query && $this->db->affected_rows() > 0){
                        $logMessage = "User activated cash advance loan `{$rows->reference_no}` with pay date of `{$pay_date}` and Generated Payroll Sheet of `{$generatedDate}`";
                        $this->core_layout->setEventLog($logMessage, "activate", "success", "payroll");
                        $ctrUpdatedLoans++;
                        /*** 
                         * to be removed
                         * $this->ca->loan_activated_email($emp_name, $rows->reference_no, $rows->amt_applied, $rows->purpose, "juniordeveloper02@gccaggregates.com");
                         * to be removed
                        ***/
                    }
                }
            }

            if($ctrUpdatedLoans > 0){
                $result['response'] = 'Loan/s has been activated';
                $result['state'] = true;
            }else{
                $result['response'] = 'No loan/s to activate!';
                $result['state'] = false;
            }
        }else{
            $result['response'] = 'No loan data found!';
            $result['state'] = false;
        }

        return $result;
    }

    // for getting employees with available cash advance within the last cut off
    function getEmpWithLoans($filterOption=array()){
        $get = $this->input->get();
        if(count($filterOption) > 0){ $get = $filterOption; }

        $state = false;
        $results = array();
        $rowCount = 0;
        $isPosted = false;

        /*** md5 key filters ***/
        $tempFilter = $get;
        unset($tempFilter["_"]);
        $tempFilter["employees"] = isset($tempFilter["employees"]) ? implode('|', $tempFilter["employees"]) : "";
        $trimmed_array = array_map('trim', $tempFilter);
        $implodedFilters = implode('||',$trimmed_array);
        $md5KeyFilter = md5($implodedFilters);
        /*** md5 key filters ***/

        $emps = $this->get_emp_with_ca($get);
        if(!empty($emps['result']["emp_id"])){
            foreach($emps['result']["emp_id"] as $emp){
                $data = array();
                $data['id'] = $emp;
                $data['employee'] = $this->core_layout->getEmployeeData($emp)['display_name_1'];
                $data['previous_data'] = array();
                $data['has_previous_data'] = false;
                $data['is_posted'] = false;
                $data["current_loan"] = isset($emps["result"]["raw_data"][$emp]) && $emps["result"]["raw_data"][$emp] ? 
                    $emps["result"]["raw_data"][$emp]: array();

                $tempAmount = isset($data["current_loan"]->amount) && $data["current_loan"]->amount ? $data["current_loan"]->amount: 0;
                if(floatval($tempAmount) > 0){
                    $tempPercentage = $data["current_loan"]->percentage;
                    $this->db->select('d.id as ps_id, b.id as loan_id, a.approved_dt, IFNULL(ROUND(SUM(c.amount_due), 2), 0) as amt_paid, b.amount as loan_amt, b.percentage, d.posted');
                    $this->db->from("gcceforms.cash_advance a");
                    $this->db->join('gcchris.loans b', 'b.reference = a.reference_no', 'left');
                    $this->db->join('payroll.payroll_sheet_loan_payments c', 'c.loan_id = b.id', 'inner');
                    $this->db->join('payroll.payroll_sheet d', 'd.id = c.payroll_sheet_id AND d.posted = 1', 'inner');
                    $this->db->where('a.employee', $emp);
                    $this->db->where('b.active', 1);
                    $this->db->where('b.paid', 0);
                    $this->db->where('d.printed_payslip !=', 1);
                    $this->db->order_by('created_dt', 'desc');
                    $this->db->limit(1);
                    $queryActiveCA = $this->db->get();
                    if($queryActiveCA->num_rows() == 1){
                        $tempRow = $queryActiveCA->row();
                        $tempRow->balance_amt = 0;
                        $tempBalance = floatval($tempRow->loan_amt) - floatval($tempRow->amt_paid);
                        $tempRow->balance_amt = round($tempBalance, 2);
                        $tempRow->balance_amt_formatted = number_format(round($tempBalance, 2), 2, ".", ",");
    
                        $equalPercentage = $tempRow->percentage == $tempPercentage;
                        if($tempBalance > 0 && $equalPercentage){
                            $data['previous_data'] = $tempRow;
                            $data['has_previous_data'] = true;
                        }
                        $isPosted = $tempRow->posted === 1;
                        $data['is_posted'] = $isPosted;
                    }

                    $this->db->reset_query();
                    $results[] = $data;
                }
            }
            $state = true;
        }

        $rowCount = count($results);
        return array(
            'results' => $results, 
            'state' => $state, 
            'count' => $rowCount, 
            'show_modal' => $rowCount > 0 && $isPosted == false,
            'filter_option' => $get,
            'md5_key_filter' => $md5KeyFilter,
        );
    }

    function get_emp_with_ca($get){
        $result = array();
        $data = array();
        $employee_ids = isset($get["employees"]) ? $get["employees"] : array();
        $sched = $get["payout_schedule"];
        $company = $this->db->where("id", $get["company"])->get("gcchris.tblcompanies")->row();
        $pay_date = $get['pay_date'];
        $range = $get['date_range'];
        
        $dates = explode('-', $range);
        $date_start = date('Y-m-d', strtotime($dates[0]));
        $date_end = date('Y-m-d', strtotime($dates[1]));
        $tempPayDate = date('Y-m-d', strtotime($pay_date));

        $this->db->select("emp_id");
        $this->db->from($this->tbl_payroll_sheet);
        $this->db->where("date_start", $date_start);
        $this->db->where("date_end", $date_end);
        $this->db->where("pay_date", $tempPayDate);
        $this->db->where("payroll_sched", $sched);
        $this->db->where("posted", 0);
        $this->db->where("is_bonus", 0);
        $this->db->where("is_archived", 0);
        if(isset($employee_ids) && is_array($employee_ids) && count($employee_ids) > 0){
            $this->db->where_in("emp_id", $employee_ids);
        }
        $queryEmployees = $this->db->get();
        if($queryEmployees->num_rows() > 0){
            $employee_ids = array();
            foreach ($queryEmployees->result() as $psData) {
                $employee_ids[] = trim($psData->emp_id);
            }
        }
        $this->db->reset_query();
        
        $employees = $this->getEmployees($sched, 'active', $employee_ids, $company);
        foreach($employees['data'] as $emp){
            $this->db->select('b.id as loan_id, a.approved_dt, b.amount, b.percentage, IFNULL(COUNT(c.id), 0) as merged_count');
            $this->db->from("gcceforms.cash_advance a");
            $this->db->join('gcchris.loans b', 'b.reference = a.reference_no', 'left');
            $this->db->join('gcchris.loans c', 'c.merged_id = b.id', 'left');
            $this->db->where('a.employee', $emp->id);
            $this->db->where('b.active', 0);
            $this->db->where('b.is_archived', 0);
            $this->db->order_by('created_dt', 'desc');
            $this->db->limit(1);

            $query = $this->db->get();
            $z = $this->db->last_query();
            if($query->num_rows() > 0){
                $rows = $query->row();
                $rows->amount_formatted = number_format($rows->amount, 2, ".", ",");

                if($rows->approved_dt !== '0000-00-00 00:00:00'){
                    $to_date = date('Y-m-d', strtotime($rows->approved_dt));
    
                    $this->db->from('payroll.payroll_sheet');
                    $this->db->where('emp_id', $emp->id);
                    $this->db->where('posted', 1);
                    $this->db->where('printed_payslip !=', 1);
                    $this->db->where('is_bonus', 0);
                    $this->db->where('is_archived', 0);
    
                    if($sched == 3){
                        $_start = date('Y-m-d', strtotime('Last Sunday' . $to_date));
                        $_end = date('Y-m-d', strtotime('Next Saturday' . $to_date));
                        $this->db->where("DATE(date_start)", $_start);
                        $this->db->where("DATE(date_end)", $_end);
                    }
    
                    $this->db->order_by("DATE(pay_date)", 'desc');
                    $this->db->limit(1);
    
                    $q = $this->db->get();
                    if($q->num_rows() > 0){
                        $tempId = 0;
                        $row = $q->row();

                        $this->db->select('SUM(amount_due) as amount');
                        $this->db->where('loan_id', $rows->loan_id);
                        $this->db->where('payroll_sheet_id', $row->id);
                        $get_payment = $this->db->get('payroll.payroll_sheet_loan_payments')->row();

                        if($sched == 3){
                            $last_date = date('Y-m-d', strtotime($_end . '+14 day'));
                            // for checking cash advance loan within cut off or after 2 payouts and suspended cash advance status
                            if(($last_date >= $date_start) && ($last_date <= $date_end) || ($get_payment->amount == 0 && $last_date <= $date_start && $last_date <= $date_end) || ($get_payment->amount != 0 && $last_date <= $date_start && $last_date <= $date_end)){
                                $tempId = $row->emp_id;
                            }
                        }elseif($sched == 2){
                            // for checking cash advance loan within cut off or after 2 payouts and suspended cash advance status
                            if(($to_date >= $row->date_start) && ($to_date <= $row->date_end) || ($get_payment->amount == 0 && $to_date <= $row->date_start && $to_date <= $row->date_end) || ($get_payment->amount != 0 && $to_date <= $row->date_start && $to_date <= $row->date_end)){
                                $tempId = $row->emp_id;
                            }
                        }else{
                            $tempId = $row->emp_id;
                        }

                        if($tempId){
                            $result["emp_id"][] = $tempId;
                            $result["raw_data"][$tempId] = $rows;
                        }
                    }
                }
            }
        }
        return array('result' => $result);
    }

    public function ifWithNbiPoliceClr($emp_id){
        $this->db->select("*");
        $this->db->from("gcchris.tbldocuments");
        $this->db->where("emp_id", $emp_id);
        $this->db->group_start();
        $this->db->where("doc_type", "Police Clearance");
        $this->db->or_where("doc_type", "NBI Clearance");
        $this->db->group_end();
        return $this->db->get()->num_rows();
    }
    // for getting employees with available cash advance within the last cut off
    
    public function storeGeneratedPsHistory($start, $end, $data){
        $userData = $this->session->userdata("logged_in");
        if(!isset($userData["ps_history"])) $userData["ps_history"] = array();
        $tempPsHistory = $userData["ps_history"];

        $data["date_start"] = $start;
        $data["date_end"] = $end;

        $data["group_count"] = 0;
        $data["group_collection"] = array();
        if(isset($data["payroll_group"]) && is_array($data["payroll_group"]) && count($data["payroll_group"]) > 0){
            sort($data["payroll_group"]); 
            $this->db->select("id, description as text");
            $this->db->from($this->tbl_payroll_group." pg");
            $this->db->where_in("id", $data["payroll_group"]);
            $psGroup = $this->db->get();
            $groupRowCount = $psGroup->num_rows();
            $data["group_count"] = $groupRowCount;
            if($groupRowCount > 0){ $data["group_collection"] = $psGroup->result(); }
            $this->db->reset_query();
        }else{
            $data["payroll_group"] = array();
            
        }
        
        $data["employees_count"] = 0;
        $data["employees_collection"] = array();
        if(isset($data["employees"]) && is_array($data["employees"]) && count($data["employees"]) > 0){
            sort($data["employees"]); 
            $this->db->select("id, UPPER(CONCAT(lastname,
            CASE WHEN UPPER(TRIM(suffix)) != 'N/A' AND
                UPPER(TRIM(suffix !='NONE')) AND suffix !='' AND
                suffix IS NOT NULL THEN CONCAT(' ', suffix) ELSE ''
            END, ', ', firstname, ' ',
            CASE WHEN UPPER(TRIM(middlename)) != 'N/A' AND UPPER(TRIM(middlename)) != 'NONE' AND
                    TRIM(middlename) !='' AND middlename IS NOT NULL
                THEN CONCAT(SUBSTR(middlename, 1, 1), '.') ELSE ''
            END)) as text");
            $this->db->from($this->tbl_employees);
            $this->db->where_in("id", $data["employees"]);
            $psEmployees = $this->db->get();
            $empRowCount = $psEmployees->num_rows();
            $data["employees_count"] = $empRowCount;
            if($empRowCount > 0){ $data["employees_collection"] = $psEmployees->result(); }
            $this->db->reset_query();
        }else{ $data["employees"] = array(); }

        $data["company_code"] = NULL;
        $data["company_collection"] = array();
        if(isset($data["company"]) && $data["company"]){
            $this->db->select("id, code, description, company_address");
            $this->db->from($this->tbl_tblcompanies);
            $this->db->where("id", $data["company"]);
            $psCompany = $this->db->get();
            if($psCompany->num_rows() == 1){ 
                $data["company_code"] = $psCompany->row()->code; 
                $data["company_collection"] = $psCompany->row();
            }
            $this->db->reset_query();
        }else{ $data["company"] = 0; }
        
        $data["payout_description"] = NULL;
        if(isset($data["payout_schedule"]) && $data["payout_schedule"]){
            $this->db->select("name");
            $this->db->from($this->tbl_ps_payout_schedule);
            $this->db->where("id", $data["payout_schedule"]);
            $psPayout = $this->db->get();
            if($psPayout->num_rows() == 1){ $data["payout_description"] = $psPayout->row()->name; }
            $this->db->reset_query();
        }else{ $data["payout_schedule"] = 0; }

        $tempPsHistory[] = $data;
        $tempPsHistory = array_unique($tempPsHistory, SORT_REGULAR);
        $userData["ps_history"] = $tempPsHistory;
        $this->session->set_userdata('logged_in', $userData);
        return $this->session;
    }

    public function mergeEmployeeCaLoan(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post["loan_type_id"]) && $post["loan_type_id"]){
            $this->db->select("emp_id, loan_id, reference_id, reference, deduction_type, fixed_deduction_amt, percentage");
            $queryTemp = $this->db->get_where($this->tbl_hris_loans, array("id"=>$post["loan_type_id"], "active"=>0, "paid"=>0));
            if($queryTemp->num_rows() === 1){
                $tempData = $queryTemp->row();
                $tempData->amount = $post["amount"];
                $tempData->active = 0;
                $tempData->remarks = "Merged cash advance";
                $tempData->created_by = $this->core_layout->getCurrentEmployeeId();
                $tempData->created_at = date("Y-m-d H:i:s");

                $added = $this->db->insert($this->tbl_hris_loans, $tempData);
                if($added){
                    $lastInsertedId = $this->db->insert_id();
                    $this->db->where_in("id", $post["loan_id"]);
                    /*** loan status active 3 is cancelled status ***/
                    $updated = $this->db->update($this->tbl_hris_loans, array("merged_id"=> $lastInsertedId, "active"=>3));
                    /*** loan status active 3 is cancelled status ***/
                    $isUpdated = $this->db->affected_rows() > 0;
                    $resultset["response"] = true;
                    $resultset["ca_updated"] = $isUpdated;
                    $resultset["filtered_data"] = $this->getEmpWithLoans($post["filter_option"]);
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }

        return $resultset;
    }

    public function checkingPayrollSheetData($filterOption=array()){
        $post = is_array($filterOption) && count($filterOption) > 0 ? $filterOption: $this->input->post();

        $resultset = array();
        $arrExistingPs = array();

        // $employee_ids = $post["employees"]; // commented original source code that causing error when generating all employees of the company
        $employee_ids = isset($post["employees"]) && $post['employees'] ? $post['employees'] : null;
        $companyId = $post["company"];
        $coverageDate = explode(" - ", $post["date_range"]);
        $dateStart = date("Y-m-d", strtotime($coverageDate[0]));
        $dateEnd = date("Y-m-d", strtotime($coverageDate[1]));
        $payDate = date("Y-m-d", strtotime($post["pay_date"]));

        $company = $this->db->where("id", $post["company"])->get("gcchris.tblcompanies")->row();
        $payout_sched = $post["payout_schedule"];
        $payout_seq = $post["payout_sequence"];

        $temp_month_name = strtolower(date('F', strtotime($dateEnd)));
        $month_name = strtolower(date('F', strtotime($payDate)));
        
        $month_name = $month_name != $temp_month_name ? $temp_month_name : $month_name;
        
        $year = date('Y', strtotime($dateStart));
        $tempYear0 = date('Y', strtotime($dateStart));
        $tempYear1 = date('Y', strtotime($dateEnd));

        $tempMax = max(array($tempYear0, $tempYear1));
        $year = $tempMax ? $tempMax: $year;

        $existingPayrollSheet = array();

        $employees = $this->getEmployees($payout_sched, 'active', $employee_ids, $company);
        if(is_array($employees['data']) && count($employees['data']) > 0){
            foreach($employees['data'] as $emp){
                $employeeFullname = trim($emp->fullname);

                $filterArrx = array(
                    "emp_id"=>$emp->id,
                    "company_id"=>$companyId,
                    "month_name" => $month_name,
                    "year" => $year,
                    "payroll_sched" => $emp->payout_sched,
                    "payroll_seq" => $payout_seq,
                    "is_bonus"=>0,
                );

                $qPayrollx = $this->db
                ->select("ps.id, ps.pay_date, ps.date_start, ps.date_end, ps.payroll_sched, ps.payroll_seq, 
                    UPPER('$employeeFullname') as employee_name, UPPER(psched.name) as payroll_schedule, ps.posted")
                ->join("payroll.payout_schedule as psched", "psched.id = ps.payroll_sched")
                ->get_where("payroll.payroll_sheet as ps", $filterArrx);

                if($qPayrollx->num_rows() > 0){
                    foreach ($qPayrollx->result() as $psx) { 
                        $tempTagx = array();
                        if(intval($psx->payroll_sched) != intval($payout_sched)){ $tempTagx[] = "payout_schedule"; }
                        if(intval($psx->payroll_seq) != intval($payout_seq)){ $tempTagx[] = "payout_sequence"; }

                        if(strtotime($psx->pay_date) != strtotime($payDate)){ $tempTagx[] = "pay_date"; }
                        if(strtotime($psx->date_start) != strtotime($dateStart) && strtotime($psx->date_end) != strtotime($dateEnd)){ $tempTagx[] = "coverage_date"; }
                        $psx->tag = $tempTagx;

                        if(intval($psx->posted) == 0){
                            $flagInjectArr = false;
                            if(strtotime($psx->pay_date) != strtotime($payDate) || 
                                (strtotime($psx->date_start) != strtotime($dateStart) && strtotime($psx->date_end) != strtotime($dateEnd))){
                                    $flagInjectArr = true;
                            }else if(intval($psx->payroll_sched) != intval($payout_sched) || 
                                intval($psx->payroll_seq) != intval($payout_seq)){
                                    $flagInjectArr = true;
                            }

                            if($flagInjectArr){ $arrExistingPs[] = $psx; }
                        }else{
                            if($psx->pay_date != $payDate || ($psx->date_start != $dateStart && $psx->date_end != $dateEnd)){
                                $existingPayrollSheet[] = $psx;
                            }
                        }
                    }
                }else{
                    $filterArr = array(
                        "emp_id"=>$emp->id,
                        "company_id"=>$companyId,
                        "date_start"=>$dateStart,
                        "date_end"=>$dateEnd,
                        "pay_date"=>$payDate,
                        "is_bonus"=>0,
                        "posted"=>0,
                    );
                    
                    $qPayroll = $this->db
                    ->select("ps.id, ps.pay_date, ps.date_start, ps.date_end, ps.payroll_sched, ps.payroll_seq, 
                        UPPER('$employeeFullname') as employee_name, UPPER(psched.name) as payroll_schedule, ps.posted")
                    ->join("payroll.payout_schedule as psched", "psched.id = ps.payroll_sched")
                    ->get_where("payroll.payroll_sheet as ps", $filterArr);
                    if($qPayroll->num_rows() > 0){
                        foreach ($qPayroll->result() as $ps) {
                            $tempTag = array();
                            if(intval($ps->payroll_sched) != intval($payout_sched)){ $tempTag[] = "payout_schedule"; }
                            if(intval($ps->payroll_seq) != intval($payout_seq)){ $tempTag[] = "payout_sequence"; }
                            $ps->tag = $tempTag;
    
                            if(intval($ps->payroll_sched) != intval($payout_sched) || 
                            intval($ps->payroll_seq) != intval($payout_seq)){
                                $arrExistingPs[] = $ps;
                            }
                        }
                    }
                }
            }
        }

        if(is_array($existingPayrollSheet) && count($existingPayrollSheet) > 0){
            $resultset["response"] = true;
            $resultset["data"] = $existingPayrollSheet;
            $resultset["count"] = count($existingPayrollSheet);
            $resultset["filter"] = $post;
            $resultset["conflict_payroll_sheet"] = 1;
        }else if(is_array($arrExistingPs) && count($arrExistingPs) > 0){
            $resultset["response"] = true;
            $resultset["data"] = $arrExistingPs;
            $resultset["count"] = count($arrExistingPs);
            $resultset["filter"] = $post;
            $resultset["conflict_payroll_sheet"] = 2;
        }else{
            $resultset["response"] = false;
        }

        return $resultset;
    }

    public function updateExistingPayrollSheetData(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post["existing_id"]) && is_array($post["existing_id"]) && count($post["existing_id"]) > 0){
            $existingIds = $post["existing_id"];
            $coverageDates = $post["coverage_date"];
            $filterOptions = json_decode($post["filter_option"], true);
            unset($post["existing_id"], $post["filter_option"], $post["coverage_date"]);
            
            if(isset($post["pay_date"]) && $post["pay_date"]){
                $post["pay_date"] = date("Y-m-d", strtotime($post["pay_date"]));
            }

            if(isset($coverageDates) && $coverageDates){
                $coverageDate = explode("-", $coverageDates);
                if(is_array($coverageDate) && count($coverageDate) == 2){
                    $post["date_start"] = date("Y-m-d", strtotime(trim($coverageDate[0])));
                    $post["date_end"] = date("Y-m-d", strtotime(trim($coverageDate[1])));
                }
            }

            if(isset($post["payroll_sched"], $post["payroll_seq"], $post["pay_date"], $post["date_start"], $post["date_end"]) && 
                ($post["payroll_sched"] || $post["payroll_seq"] || $post["pay_date"] || $post["date_start"] || $post["date_end"])){
                if(is_array($existingIds) && count($existingIds) > 0){
                    $ctrUpdated = false;
                    foreach ($existingIds as $idx) {
                        if($idx){
                            $this->db->select("ps.id, ps.payroll_seq, ps.payroll_sched, psched.name as payout_schedule, ps.pay_date, ps.date_start, ps.date_end");
                            $this->db->join("payroll.payout_schedule psched", "psched.id = ps.payroll_sched", "LEFT");
                            $prevRec = $this->db->get_where("payroll.payroll_sheet ps", array("ps.id"=>$idx));
                            if($prevRec->num_rows() == 1){
                                $prevRecData = $prevRec->row();

                                $updated = $this->db->update("payroll.payroll_sheet", $post, array("id"=>$prevRecData->id));
                                if($updated && $this->db->affected_rows() > 0){
                                    $this->db->select("ps.payroll_seq, ps.payroll_sched, UPPER(TRIM(CONCAT(emp.firstname, ' ',
                                    CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                                            TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                                        THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                                    END,' ', emp.lastname,
                                    CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                                        UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                                        emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                                    END))) as employee_name, psched.name as payout_schedule, ps.pay_date, ps.date_start, ps.date_end");
                                    $this->db->from("payroll.payroll_sheet ps");
                                    $this->db->join("gccmaster.tblemployees emp", "emp.id = ps.emp_id", "INNER");
                                    $this->db->join("payroll.payout_schedule psched", "psched.id = ps.payroll_sched", "LEFT");
                                    $this->db->where("ps.id", $prevRecData->id);
                                    $psLogs = $this->db->get();
                                    
                                    $logsMessage = "";
                                    if($psLogs->num_rows() == 1){
                                        $rowLogs = $psLogs->row();
                                        $hasPrevLogs = false;

                                        if(strtotime($rowLogs->pay_date) != strtotime($prevRecData->pay_date)){
                                            $logsMessage .= "Payroll sheet data pay date has been updated from `{$prevRecData->pay_date}` to `{$rowLogs->pay_date}`";
                                            $hasPrevLogs = true;
                                        }

                                        if(strtotime($rowLogs->date_start) != strtotime($prevRecData->date_start)){
                                            $logsMessage .= $hasPrevLogs ? " and payroll ": "Payroll sheet data ";
                                            $logsMessage .= "date start has been updated from `{$prevRecData->date_start}` to `{$rowLogs->date_start}`";
                                            $hasPrevLogs = true;
                                        }

                                        if(strtotime($rowLogs->date_end) != strtotime($prevRecData->date_end)){
                                            $logsMessage .= $hasPrevLogs ? " and payroll ": "Payroll sheet data ";
                                            $logsMessage .= "date end has been updated from `{$prevRecData->date_end}` to `{$rowLogs->date_end}`";
                                            $hasPrevLogs = true;
                                        }

                                        if(intval($rowLogs->payroll_sched) != intval($prevRecData->payroll_sched)){
                                            $logsMessage .= $hasPrevLogs ? " and payroll ": "Payroll sheet data ";
                                            $logsMessage .= "payout schedule has been updated from `{$prevRecData->payout_schedule}` to `{$rowLogs->payroll_sched}`";
                                            $hasPrevLogs = true;
                                        }
                                        if(intval($rowLogs->payroll_seq) != intval($prevRecData->payroll_seq)){
                                            $ordinal = new NumberFormatter('en_US', NumberFormatter::ORDINAL);
                                            $fromSeq = $ordinal->format($prevRecData->payroll_seq);
                                            $toSeq = $ordinal->format($rowLogs->payroll_seq);
                                            $logsMessage .= $hasPrevLogs ? " and payroll ": "Payroll sheet data ";
                                            $logsMessage .= "sequence has been updated from `{$fromSeq}` to `{$toSeq}`";
                                        }

                                        if($logsMessage){ $logsMessage .= " of employee named `{$rowLogs->employee_name}.`"; }
                                    }
                                    
                                    if($logsMessage){ $this->core_layout->setEventLog($logsMessage, "update", "success", "payroll"); }
                                    $ctrUpdated = true; 
                                }
                            }

                        }
                    }
                }

                if($ctrUpdated){
                    $psData = $this->checkingPayrollSheetData($filterOptions);
                    $resultset["payroll_sheet_data"] = $psData;
                    $resultset["response"] = true;
                }else{ 
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }

        return $resultset;
    }

    function selectPayrollGroupPayslip($privilege = array()){
        $get = $this->input->get();
        $arrData = array();
        $resultset = array();
        $companyId = (isset($get["company_id"]) && $get["company_id"])? $get["company_id"]: 0;
        $getAllAssignedPayrollGroup = array();
        $hasViewByCompany = (in_array('view_by_company', $privilege)) ? true : false;

        if ($hasViewByCompany) {
            $getAllAssignedPayrollGroup = $this->getAssignedPayrollGroup($this->user_data['emp_id'], $companyId);
            $this->db->reset_query();
        }

        if($companyId || $companyId == 0){
            $this->db->select("id, description as text, employee_id, assigned_employee_id");
            $this->db->from($this->tbl_payroll_group);
            $this->db->where("company_id", $companyId);
            $this->db->where("status", 1);
            $this->db->where("is_archived", 0);

            if (isset($get['term']) && $get['term']) {
                $this->db->like("description", $get['term'], "both");
            }

            if ($hasViewByCompany) {
                if (!empty($getAllAssignedPayrollGroup)) {
                    $this->db->where_in("id", $getAllAssignedPayrollGroup);
                } else {
                    $this->db->where("id", 0);
                }
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
        $resultSet['results']['term'] = isset($get['term']) ? $get['term'] : '';
        return $resultset;
    }

    public function getAssignedPayrollGroup($id, $companyId){
        $result = array();

        $this->db->select('id, assigned_employee_id');
        $this->db->from($this->tbl_payroll_group);
        $this->db->where("company_id", $companyId);
        $this->db->where("status", 1);
        $this->db->where("is_archived", 0);
        $this->db->order_by("description", "ASC");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $tempIds = @unserialize($row->assigned_employee_id);

                if (is_array($tempIds)) {
                    if (in_array($id, $tempIds)) {
                        $result[] = $row->id;
                    }
                }
            }
        }

        return $result;
    }

    public function selectEmployeeByPrivileges($privilege = array()){
        $get = $this->input->get();
        $resultarray = array();
        $empsInPrivilege = array();
        $companyId = (isset($get["company_id"]) && $get["company_id"])? $get["company_id"]: 0;
        $hasViewByCompany = (in_array('view_by_company', $privilege)) ? true : false;

        if ($hasViewByCompany) {
            $empsInPrivilege = $this->getEmployeesByPrivilege($this->user_data['emp_id'], $companyId, $privilege);
        }

        $this->db->select("a.id, trim(a.firstname) as firstname, a.lastname, a.middlename, a.suffix");
        $this->db->from("gccmaster.tblemployees a");
        $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id", "LEFT");
        $this->db->where("a.employee_status", "Active"); 
        if(isset($get["company_ids"]) && !is_array($get["company_ids"]) && $get["company_ids"]){
            $this->db->where("b.id", $get["company_ids"]);
        }

        if (isset($get['company_id']) && $get['company_id']) {
            $this->db->where("b.id", $get['company_id']);
        }

        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
            $this->db->like("a.firstname", $get['q'], "both");
            $this->db->or_like("a.lastname", $get['q'], "both");
            $this->db->group_end();
        }

        if ($hasViewByCompany) {
            if (!empty($empsInPrivilege)) {
                $this->db->where_in('a.id', $empsInPrivilege);
            } else { $this->db->where('a.id', 0); }
        }

        $this->db->limit(10);
        $this->db->order_by("trim(a.firstname)", "ASC");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $display_employee = $this->format_name($_query);

                $data["id"] = $_query["id"];
                $data["text"] = $display_employee;
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    public function getEmployeesByPrivilege($id = 0, $companyId = 0, $privilege = array()){
        $result = array();
        $hasViewByCompany = (in_array('view_by_company', $privilege)) ? true : false;
        $getAllAssignedPayrollGroup = $this->getAssignedPayrollGroup($this->user_data['emp_id'], $companyId);
        $this->db->reset_query();

        $this->db->select("employee_id");
        $this->db->from($this->tbl_payroll_group);
        $this->db->where("company_id", $companyId);
        $this->db->where("status", 1);
        $this->db->where("is_archived", 0);

        if ($hasViewByCompany) {
            if (!empty($getAllAssignedPayrollGroup)) { 
                $this->db->where_in("id", $getAllAssignedPayrollGroup);
            } else {
                $this->db->where("id", 0);
            }
        }

        $this->db->order_by("description", "ASC");
        $qTemp = $this->db->get();

        if($qTemp->num_rows() > 0){
            foreach($qTemp->result() as $vv){
                $tempIds = @unserialize($vv->employee_id);
                $result[] = $tempIds;
            }
        }

        return array_merge(...$result); //merge arrays into one 1 array
    }

    protected function getFixedTaxableDeduction($id = 0){
        if($id){
            $this->db->select("taxable_amount");
            $this->db->from("payroll.fixed_taxable_deduction");
            $this->db->where("employee_id", $id);
            $this->db->where("is_active", 1);
            $taxable = $this->db->get();
            if ($taxable->num_rows() == 1){
                return floatval($taxable->row()->taxable_amount);
            } else { return 0; }
        } else { return 0; }
    }
    
    public function setPrintablePayslipOthers($ids=array()){
        $resultset = array();
        $post = $this->input->post();
        if(isset($ids) && $ids){ $post["ids"] = $ids; }
        if(isset($post) && $post){
            if(isset($post["ids"]) && is_array($post["ids"]) && count($post["ids"]) > 0){
                $arrData = array();
                $forPrint = $this->getEmpId($post["ids"]);
                foreach ($forPrint as $id) {
                    $result = (object) $this->getCurrentPayrollPayslip($id);
                    if($result->response === true){
                        $arrData[] = $result->data;
                    }
                }
                if(is_array($arrData) && !empty($arrData)){
                    $html = $this->load->view("core/templates/printable/header", null, true);
                    $html .= $this->load->view("payroll/payroll/printable/print_content_others", array("data"=>$arrData), true);
                    $html .= $this->load->view("core/templates/printable/footer", null, true);
                    $resultset["response"] = true;
                    $resultset["html"] = $html;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    public function suspendNoEarnersLoans($id){
        $this->db->where('emp_id', $id);
        $this->db->where('active', 1);
        $this->db->where('paid', 0);
        $this->db->where("is_archived", 0);
        $query = $this->db->update($this->tbl_hris_loans, array('active' => 0));

        if ($query) {
            $msg = "System Generated: Active loan(s) of employee `$id` is automatically suspended.";
            $this->core_layout->setEventLog($msg, "update", "success", "payroll");
        }

        return $query;
    }

    public function getEmployeeActiveLoansNotPaid($id, $gross_pay, $status=0, $remainingGrossPay=0, $zeroNet=false) {
        $this->db->select("a.*, pl.loan_type, pl.code, pl.loan_class");
        $this->db->join("payroll.loans as pl", "pl.id = a.loan_id");
        $this->db->where("a.emp_id", $id);
        $this->db->where("a.paid", 0);
        $this->db->where("a.is_archived", 0);
        $this->db->order_by("pl.loan_type", "ASC");
        $this->db->where("a.active", 1);
        $loans = $this->db->get("gcchris.loans a")->result();

        foreach ($loans as $loan) {
            $total_amount_paid = $this->db
                ->select_sum("psloanpayments.amount_due")
                ->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id")
                ->where("ps.posted", 1)
                ->where("emp_id", $id)
                ->where("loan_id", $loan->id)
                ->get("payroll.payroll_sheet_loan_payments psloanpayments")
                ->row("amount_due");

            $loan->total_amount_paid = round($total_amount_paid, 2);
            $balance = floatval(round($loan->amount,2)) - floatval(round($total_amount_paid, 2));
            $loan->amount_due = $balance > 0 ? $balance : 0;
            $loan->interest_amount = 0;
            $loan->zero_netpay = 0;
            $toDeduct = false;
            
            if($balance > 0 /*** && $remainingGrossPay >= $balance ***/){
                if (intval($loan->deduction_type) === 0) {
                    $percentage = $loan->percentage / 100;
                    $amount_due = $gross_pay * $percentage;
                    if(doubleval($balance) > doubleval($amount_due)){ $toDeduct = true; }

                    $amount_due = doubleval($balance) > doubleval($amount_due) ? $amount_due : $balance;
                    $loan->amount_due = $amount_due;

                } else {
                    if(doubleval($balance) > doubleval($loan->fixed_deduction_amt)){ $toDeduct = true; }
                    $amount_due = doubleval($balance) > doubleval($loan->fixed_deduction_amt) ? $loan->fixed_deduction_amt : $balance;
                    $loan->amount_due = floatval($amount_due);
                }

                if (floatval($loan->interest_percentage) > 0) {
                    $intPercentage = $loan->interest_percentage / 100;
                    $amountToDeduct = floatval($loan->amount) * $intPercentage;
                    $loan->interest_amount = $amountToDeduct;
                }

                /*** $remainingGrossPay = $remainingGrossPay - $balance; ***/
            }
            /*** else if($zeroNet && $balance > 0 && $balance > $remainingGrossPay){
                $loan->zero_netpay = 1;
            } ***/

            $nBalance = floatval($loan->amount_due);
            if($nBalance > 0 && $remainingGrossPay >= $nBalance){
                $remainingGrossPay = $remainingGrossPay - $nBalance;
            }else if($zeroNet && $nBalance > 0 && $nBalance > $remainingGrossPay && $toDeduct == false){
                $loan->zero_netpay = 1;
            }
        }

        return $loans;
    }

    public function undo_printed_payroll_sheet(){
        $post = $this->input->post();
        $data = array();
        $date_start = 'No date start';
        $date_end = 'No date end';
        $pay_date = 'No pay date';
        $emp_id = $post['emp_id'] ?? null;
        $reason = $post['reason'] ?? 'No reason provided';

        if(isset($post["id"]) && $post["id"]){
            $maxDate = $this->getPayrollMaxDate($post["emp_id"]);
            $payrollDate = $this->db->select("b.pay_date, b.date_start, b.date_end, UPPER(TRIM(CONCAT(a.firstname, ' ',
                CASE WHEN UPPER(TRIM(a.middlename)) != 'N/A' AND UPPER(TRIM(a.middlename)) != 'NONE' AND
                        TRIM(a.middlename) !='' AND a.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(a.middlename, 1, 1), '.') ELSE ''
                END,' ', a.lastname,
                CASE WHEN UPPER(TRIM(a.suffix)) != 'N/A' AND
                    UPPER(TRIM(a.suffix !='NONE')) AND a.suffix !='' AND
                        a.suffix IS NOT NULL THEN CONCAT(' ', a.suffix) ELSE ''
                END))) as employee_name")
                ->join("gccmaster.tblemployees as a", "a.id = b.emp_id", "LEFT")
                ->from("payroll.payroll_sheet as b")
                ->where("b.id", $post["id"])
                ->where('b.emp_id', $post['emp_id'])
                ->where('b.printed_payslip', 1)
                ->where('b.posted', 1)
                ->get()->row();
                
            if ($payrollDate) {
                $date_start = date("Y-m-d", strtotime($payrollDate->date_start));
                $date_end = date("Y-m-d", strtotime($payrollDate->date_end));
                $pay_date = date("Y-m-d", strtotime($payrollDate->pay_date));
                $employee_name = $payrollDate->employee_name ?? 'No employee name';

                if($maxDate && strtotime($maxDate) > strtotime($date_end)){
                    $data["response"] = false;
                    return $data;
                }
            }

            $this->db->where("id", $post["id"]);
            $this->db->where('emp_id', $post['emp_id']);
            $this->db->where('printed_payslip', 1);
            $this->db->where('posted', 1);
            $updated = $this->db->update("payroll.payroll_sheet", array("printed_payslip"=>0));
            if($updated && $this->db->affected_rows() > 0){
                $data["response"] = true;

                $this->core_layout->setEventLog("Payroll sheet of employee name `$employee_name` with coverage date of `{$date_start} - {$date_end}` and pay date `{$pay_date}` with reason of `{$reason}` has been set to undone printable status.", "update", "success", "payroll");
            }else{
                $data["response"] = false;
                $this->core_layout->setEventLog("Failed to undo printable status of employee name `$employee_name` with coverage date of `{$date_start} - {$date_end}` and pay date `{$pay_date}` with reason of `{$reason}`.", "update", "success", "payroll");
            }
        }else{
            $data["response"] = false;
        }

        return $data;
    }

    protected function getPayrollMaxDate($id=null){
        if($id){
            $this->db->select("MAX(ps.date_end) as max_date");
            $this->db->from($this->tbl_employees." emp");
            $this->db->join($this->tbl_payroll_sheet." ps", "ps.emp_id = emp.id AND ps.posted = 1", "LEFT");
            $this->db->where("emp.id", $id);
            $this->db->group_by("emp.id");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() == 1){ return $qTemp->row()->max_date; }
            else{ return false; }
        }else{ return false; }
        
    }

    public function check_printed_payslip(){
        $_post = $this->input->post();
        $result = array();

        if (isset($_post) && !empty($_post)) {
            $post = (object) $_post;
            if(isset($post->date_range) && $post->date_range){
                $date_range = explode("-", $post->date_range);
                $start = date('Y-m-d', strtotime(trim($date_range[0])));
                $end = date('Y-m-d', strtotime(trim($date_range[1])));
            }

            $employee_ids = isset($post->employees) ? $post->employees : null;
            $payout_sched = $post->payout_schedule;
            $sequence = $post->payout_sequence;
            $company_id = $post->company;
            $maxDate = null;
            $pay_date = date("Y-m-d", strtotime($post->pay_date));
            $countPrinted = 0;

            $this->db->select("MAX(ps.date_end) as max_date");
            $this->db->from($this->tbl_employees." emp");
            $this->db->join($this->tbl_payroll_sheet." ps", "ps.emp_id = emp.id AND ps.posted = 1", "LEFT");
            $this->db->where_in("emp.id", $employee_ids);
            $this->db->group_by("emp.id");
            $qTemp = $this->db->get();

            $this->db->reset_query();

            if ($qTemp->num_rows() > 0) {
                $maxDate = $qTemp->row()->max_date;
            }

            if ($maxDate && date('Y-m-d', strtotime($maxDate)) > date('Y-m-d', strtotime($end))) {
                $result['response'] = false;
            } else {
                $this->db->select("id");
                $this->db->from("payroll.payroll_sheet");
                $this->db->where_in("emp_id", $employee_ids);
                $this->db->where("company_id", $company_id);
                $this->db->where("payroll_sched", $payout_sched);
                $this->db->where("payroll_seq", $sequence);
                $this->db->where("date_start", $start);
                $this->db->where("date_end", $end);
                $this->db->where("pay_date", $pay_date);
                $this->db->where("posted", 1);
                $this->db->where("printed_payslip", 1);

                $_qTemp = $this->db->get();

                if ($_qTemp->num_rows() > 0) {
                    $countPrinted = $_qTemp->num_rows();
                }

                $result['count_printed'] = $countPrinted;
                $result['response'] = true;
            }
        }
        return $result;
    }

    function selectPayrollGroupByStatus(){
        $get = $this->input->get();
        $arrData = array();
        $resultset = array();
        $companyId = (isset($get["company_id"]) && $get["company_id"])? $get["company_id"]: 0;
        $status = (isset($get['status']) && $get['status']) ? $get['status'] : false;
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

                    // added to filtered out by employee status
                    if ($status){ 
                        if ($status != 'All') {
                            $this->db->where('employee_status', $status);
                        }

                        if ($status == 'All' || $status == 'Active') {
                            $this->db->where_not_in('work_status', ['NO CONTRACT', 'CONSULTANT', 'PART-TIME']);  //added to generate only the regular and probi work status
                        }
                    }
                    // added to filtered out by employee status

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

    function getPayrollGroupMultipleByStatus(){
        $post = $this->input->post();
        $resultset = array();
        $employees = array();
        $status = (isset($post['status']) && $post['status']) ? $post['status'] : false;

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

                // added to filtered out by employee status
                if ($status){ 
                    if ($status != 'All') {
                        $this->db->where('employee_status', $status);
                    }

                    if ($status == 'All' || $status == 'Active') {
                        $this->db->where_not_in('work_status', ['NO CONTRACT', 'CONSULTANT', 'PART-TIME']);  //added to generate only the regular and probi work status
                    }
                }
                // added to filtered out by employee status
                
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

    function selectEmployeeByStatus($type=null) {
        $get = $this->input->get();
        $resultarray = array();
        $companyId = (isset($get["company_id"]) && $get["company_id"])? $get["company_id"]: 0;
        $status = (isset($get['status']) && $get['status']) ? $get['status'] : false;

        $this->db->select("a.id, UPPER(TRIM(CONCAT(a.firstname, ' ',
                CASE WHEN UPPER(TRIM(a.middlename)) != 'N/A' AND UPPER(TRIM(a.middlename)) != 'NONE' AND
                        TRIM(a.middlename) !='' AND a.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(a.middlename, 1, 1), '.') ELSE ''
                END,' ', a.lastname,
                CASE WHEN UPPER(TRIM(a.suffix)) != 'N/A' AND
                    UPPER(TRIM(a.suffix !='NONE')) AND a.suffix !='' AND
                        a.suffix IS NOT NULL THEN CONCAT(' ', a.suffix) ELSE ''
                END))) as text");
        $this->db->from("gccmaster.tblemployees a");
        $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id", "LEFT");

        $this->db->where("b.id", $companyId);

        // added to filtered out by employee status
        if ($status){ 
            if ($status != 'All') {
                $this->db->where('a.employee_status', $status);
            }

            if ($status == 'All' || $status == 'Active') {
                $this->db->where_not_in('a.work_status', ['NO CONTRACT', 'CONSULTANT', 'PART-TIME']); //added to generate only the regular and probi work status
            }
        }
        // added to filtered out by employee status

        $tempLimit = 10;
        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
                $this->db->like("a.firstname", $get['q'], "both");
                $this->db->or_like("a.lastname", $get['q'], "both");
                $this->db->or_like("CONCAT(a.firstname, ' ', a.lastname)", $get['q'], "both");
                $this->db->or_like("CONCAT(a.firstname, ' ', CONCAT(SUBSTR(a.middlename, 1, 1), '.'), ' ', a.lastname)", $get['q'], "both");
            $this->db->group_end();
            $tempLimit = 20;
        }
        
        $this->db->limit($tempLimit);
        $this->db->order_by("trim(a.firstname)", "ASC");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $resultarray = $query->result();
        }
        return array("results" => $resultarray);
    }

    function selectEmployeeByCompany($type=null)
    {
        $get = $this->input->get();
        $resultarray = array();
        $companyIds = (isset($get["company_ids"]) && $get["company_ids"])? $get["company_ids"]: array();
        //$this->db->select("a.id, trim(a.firstname) as firstname, a.lastname, a.middlename, a.suffix");

        $this->db->select("a.id, UPPER(TRIM(CONCAT(a.firstname, ' ',
                CASE WHEN UPPER(TRIM(a.middlename)) != 'N/A' AND UPPER(TRIM(a.middlename)) != 'NONE' AND
                        TRIM(a.middlename) !='' AND a.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(a.middlename, 1, 1), '.') ELSE ''
                END,' ', a.lastname,
                CASE WHEN UPPER(TRIM(a.suffix)) != 'N/A' AND
                    UPPER(TRIM(a.suffix !='NONE')) AND a.suffix !='' AND
                        a.suffix IS NOT NULL THEN CONCAT(' ', a.suffix) ELSE ''
                END))) as text");
        $this->db->from("gccmaster.tblemployees a");
        $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id", "LEFT");
        
        if($type !== 'all' && $type === null){
            $this->db->where("a.employee_status", "Active");
        } elseif ($type !== 'all' && $type !== null) {
            $this->db->where("a.employee_status", $type);
        }

        $this->db->where_in("b.id", $companyIds);

        $tempLimit = 10;
        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
            $this->db->like("a.firstname", $get['q'], "both");
            $this->db->or_like("a.lastname", $get['q'], "both");
            $this->db->or_like("CONCAT(a.firstname, ' ', a.lastname)", $get['q'], "both");
            $this->db->or_like("CONCAT(a.firstname, ' ', CONCAT(SUBSTR(a.middlename, 1, 1), '.'), ' ', a.lastname)", $get['q'], "both");
            $this->db->group_end();
            $tempLimit = 20;
        }
        $this->db->limit($tempLimit);
        $this->db->order_by("trim(a.firstname)", "ASC");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            /*** foreach ($query->result_array() as $_query) {
                $data = array();
                $display_employee = $this->format_name($_query);

                $data["id"] = $_query["id"];
                $data["text"] = $display_employee;
                $resultarray[] = $data;
            } ***/
            $resultarray = $query->result();
        }
        return array("results" => $resultarray);
    }

    public function getTransferEmployeeGroupApproval(){
        $arrResult = array();
        $this->db->select("pgt.id, pgt.group_id, pgt.reason, pgt.status, pgt.employee_id, UPPER(pg.description) as payroll_group, comp.code as company_code");
        $this->db->join($this->tbl_payroll_group." pg", " pg.id = pgt.group_id", "inner");
        $this->db->join($this->tbl_tblcompanies." comp", "comp.id = pgt.company_id", "left");
        $qpgt = $this->db->get_where($this->tbl_payroll_group_transfer." pgt", array("pgt.status" => 0));
        if($qpgt->num_rows() > 0){
            foreach ($qpgt->result() as $row) {
                $row->employee_id = @unserialize($row->employee_id);
                if(is_array($row->employee_id) && !empty($row->employee_id)){
                    $this->db->select("TRIM(CONCAT(UPPER(emp.firstname), ' ',
                        CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                            THEN CONCAT(SUBSTR(UPPER(emp.middlename), 1, 1), '.') ELSE ''
                        END,' ', UPPER(emp.lastname),
                        CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                            UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                            emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(emp.suffix)) ELSE ''
                        END)) as employee_name");
                    $this->db->from($this->tbl_employees." emp");
                    $this->db->where_in("emp.id", $row->employee_id);
                    $this->db->order_by("emp.firstname", "ASC");
                    $employees = $this->db->get();
                    if($employees->num_rows() > 0){
                        $row->employees = $employees->result();
                    }
                    $this->db->reset_query();
                }
            }
            $arrResult = $qpgt->result();
        }
        $this->db->reset_query();
        return $arrResult;
    }

    public function getTransferEmployeeGroupHistory(){
        $arrResult = array();
        $this->db->select("
            pgt.id,
            pgt.group_id,
            pgt.reason,
            pgt.status,
            pgt.employee_id,
            pg.description as payroll_group,
            comp.code as company_code,
            CASE
                WHEN pgt.status = 1 THEN 'Approved'
                WHEN pgt.status = 2 THEN 'Disapproved'
                ELSE 'Pending'
            END as status_name,
            TRIM(CONCAT(
                UPPER(e_act.firstname), ' ',
                IF(
                    e_act.middlename IS NOT NULL
                    AND TRIM(e_act.middlename) NOT IN ('', 'N/A', 'NONE'),
                    CONCAT(SUBSTR(UPPER(e_act.middlename),1,1), '.'),
                    ''
                ),
                ' ',
                UPPER(e_act.lastname)
            )) as action_by,

            IF(pgt.updated_by > 0, pgt.updated_at, pgt.created_at) as action_at,
            TRIM(CONCAT(
                UPPER(e_stat.firstname), ' ',
                IF(
                    e_stat.middlename IS NOT NULL
                    AND TRIM(e_stat.middlename) NOT IN ('', 'N/A', 'NONE'),
                    CONCAT(SUBSTR(UPPER(e_stat.middlename),1,1), '.'),
                    ''
                ),
                ' ',
                UPPER(e_stat.lastname)
            )) as action_status_by,

            CASE
                WHEN pgt.status = 1 THEN pgt.approved_at
                WHEN pgt.status = 2 THEN pgt.disapproved_at
                ELSE NULL
            END as action_status_at
        ", false);

        $this->db->join($this->tbl_payroll_group." pg", "pg.id = pgt.group_id", "inner");
        $this->db->join($this->tbl_tblcompanies." comp", "comp.id = pgt.company_id", "left");
        $this->db->join($this->tbl_employees." e_act", "e_act.id = COALESCE(NULLIF(pgt.updated_by,0), pgt.created_by)", "left");
        $this->db->join($this->tbl_employees." e_stat", "e_stat.id = CASE
        WHEN pgt.status = 1 THEN pgt.approved_by
        WHEN pgt.status = 2 THEN pgt.disapproved_by
        ELSE NULL END", "left", false);
        $this->db->order_by("pgt.id", "DESC");
        $qpgt = $this->db->get_where($this->tbl_payroll_group_transfer." pgt", array("pgt.status !=" => 0));
        if($qpgt->num_rows() > 0){
            foreach ($qpgt->result() as $row) {
                $row->employee_id = @unserialize($row->employee_id);
                if(is_array($row->employee_id) && !empty($row->employee_id)){
                    $this->db->select("TRIM(CONCAT(UPPER(emp.firstname), ' ',
                        CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                            THEN CONCAT(SUBSTR(UPPER(emp.middlename), 1, 1), '.') ELSE ''
                        END,' ', UPPER(emp.lastname),
                        CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                            UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                            emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(emp.suffix)) ELSE ''
                        END)) as employee_name");
                    $this->db->from($this->tbl_employees." emp");
                    $this->db->where_in("emp.id", $row->employee_id);
                    $this->db->order_by("emp.firstname", "ASC");
                    $employees = $this->db->get();
                    if($employees->num_rows() > 0){
                        $row->employees = $employees->result();
                    }
                    $this->db->reset_query();
                }
            }
            $arrResult = $qpgt->result();
        }
        $this->db->reset_query();
        return $arrResult;
    }
    
    protected function getNoEarnerEmployeeNameById($id=0){
        $this->db->select("UPPER(CONCAT(lastname, ', ', firstname,
            CASE WHEN UPPER(TRIM(middlename)) != 'N/A' AND UPPER(TRIM(middlename)) != 'NONE' AND
                    TRIM(middlename) !='' AND middlename IS NOT NULL
                THEN CONCAT(' ', SUBSTR(middlename, 1, 1), '.') ELSE ''
            END,'',
            CASE WHEN UPPER(TRIM(suffix)) != 'N/A' AND
                UPPER(TRIM(suffix !='NONE')) AND suffix !='' AND
                suffix IS NOT NULL THEN CONCAT(' ', suffix) ELSE ''
            END)) as employee_name");
        $qTemp = $this->db->get_where($this->tbl_employees, array('id' => $id));
        return $qTemp->num_rows() === 1 ? $qTemp->row()->employee_name : $id;
    }

    public function notifSuspended($id, $loanIds = array()) {
        $userId = $this->core_layout->getCurrentEmployeeId();
        $employeeName = $this->getNoEarnerEmployeeNameById($id);
        $userLoggedName = $this->getNoEarnerEmployeeNameById($userId);

        $this->db->select("
            hrl.id,
            CASE
                WHEN hrl.debit_note IS NOT NULL AND hrl.debit_note != ''
                    THEN CONCAT(UPPER(psl.loan_name), ' | ', UPPER(hrl.debit_note))
                WHEN hrl.reference IS NOT NULL AND hrl.reference != ''
                    THEN CONCAT(UPPER(psl.loan_name), ' | ', UPPER(hrl.reference))
                ELSE UPPER(psl.loan_name)
            END AS loan_name
        ", false);

        $this->db->from($this->tbl_hris_loans." hrl");
        $this->db->join($this->tbl_ps_loans." psl", "psl.id = hrl.loan_id", "left");

        if (!empty($loanIds) && count($loanIds) > 0) {
            $this->db->where_not_in('hrl.id', $loanIds);
        }

        $this->db->where('hrl.emp_id', $id);
        $this->db->where('hrl.active', 1);
        $this->db->where('hrl.paid', 0);
        $this->db->where("hrl.is_archived", 0);
        $empLoans = $this->db->get();
        if($empLoans->num_rows() > 0){
            $loanDescriptions = array();
            foreach($empLoans->result() as $loan){
                $this->db->where('id', $loan->id);
                $loanDescriptions[] = $loan->loan_name;
            }

            if(!empty($loanDescriptions)){
                $suspendedLoans = array_unique($loanDescriptions);
                $loanDescriptions = implode(', ', array_unique($loanDescriptions));
                $rawMessage = "Active loan(s) of employee `$employeeName` for the loan(s) `$loanDescriptions` has been automatically suspended due to insufficient gross pay amount.";
                $msg = "System Generated: " . $rawMessage;
                $this->core_layout->setEventLog($msg, "update", "success", "payroll");
                
                $tempLoans = explode(", ", $loanDescriptions);
                $listLoans = "Loan Descriptions: \n";
                foreach($tempLoans as $lnx){
                    $listLoans .= "- " . trim($lnx) . "\n";
                }

                $telegramMessage = "Employee `$employeeName` has active loan(s) with no payroll deductions due to insufficient gross pay.\n\n";
                $telegramMessage .= $listLoans;
                $telegramMessage .= "\nLast Updated By: $userLoggedName";
                $telegramMessage .= "\nDate and Time: " . date("D, F j, Y, g:i a");
                $telegramResponse = $this->sendTelegramPayrollNotificationNoEarners($telegramMessage);
                $logState = $telegramResponse['ok'] ? "success" : "error";
                $message = "Employee `$employeeName` has active loan(s)  `<b>$loanDescriptions</b>` with no payroll deductions due to insufficient gross pay. Last Updated by: `$userLoggedName` at ".date("D, F j, Y, g:i a");
            }
        }

        return true;
    }

    protected function sendTelegramPayrollNotificationNoEarners($message=null){
        if (empty($message)) {
            return [ 'ok' => false, 'error' => 'Message is empty' ];
        }

        $config = $this->telegram_config_if_exist('payroll_notification', null);
        if(!$config){
            return [ 'ok' => false, 'error' => 'Telegram config not found' ];
        }

        $botToken = $config->telegram_bot_token;
        $chatId   = $config->chat_id;

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $payload = [ 'chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'HTML' ];

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($payload),
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ]);

        set_error_handler(function ($severity, $msg) {
            throw new Exception($msg);
        });

        try {
            $response = file_get_contents($url, false, $context);
            restore_error_handler();
            if ($response === false) {
                throw new Exception('Telegram API unreachable');
            }

            $decoded = json_decode($response, true);
            if (isset($decoded['ok']) && $decoded['ok'] === true) {
                return [ 'ok' => true, 'error' => null ];
            }else{
                return [ 'ok' => false, 'error' => $decoded['description'] ];
            }
        } catch (Exception $e) {
            restore_error_handler();
            return [ 'ok' => false, 'error' => "Error sending telegram notification: " . $e->getMessage() ];
        }
    }
}
