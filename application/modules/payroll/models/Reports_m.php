<?php defined('BASEPATH') or exit('No direct script access allowed');
class Reports_m extends CI_Model{
    protected $tbl_employees = "gccmaster.tblemployees";
    protected $tbl_tblcompanies = "gcchris.tblcompanies";
    protected $tbl_tblposition = "gcchris.tblposition";
    protected $tbl_tbldepartment = 'gcchris.tbldepartments';

    protected $tbl_attendance = "gcctimeutility.attendance";
    protected $tbl_personnel = "gcctimeutility.personnel";
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
    protected $tbl_payout_schedule = "payroll.payout_schedule";
    protected $tbl_ps_custom_adjustments = "payroll.payroll_sheet_custom_adjustments";
    protected $tbl_ps_created_adjustments = "payroll.payroll_sheet_created_adjustments";
    protected $tbl_ps_monthly_week_count = "payroll.ps_monthly_week_count";
    protected $tbl_hris_loans = "gcchris.loans";
    protected $tbl_payroll_settings = "payroll.settings";
    protected $tbl_hris_allawances = "gcchris.allowances";
    protected $tbl_ps_allowances = "payroll.payroll_sheet_allowances";
    protected $tbl_default_station = "gcchris.default_station_location";

    function __construct(){
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        date_default_timezone_set("Asia/Manila");
    }

    function getSssRemittanceReportRequest(){
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

        $rowData = $this->sssRemittanceReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->sssRemittanceReportListCount($filteredId, $search);

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

    function getPhicRemittanceReportRequest(){
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

        $rowData = $this->phicRemittanceReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->phicRemittanceReportListCount($filteredId, $search);

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

    function getHdmfRemittanceReportRequest(){
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

        $rowData = $this->hdmfRemittanceReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->hdmfRemittanceReportListCount($filteredId, $search);

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

    function sssRemittanceReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder){
        if(is_array($filteredId) && count($filteredId) > 0){
            $adjustments = $this->generateContributionDeductionAdjustments($filteredId);
            $filterFields = array("b.firstname", "b.lastname", "b.sss_no", "b.idno");
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id,
            b.sss_no, b.idno, ROUND(SUM(a.gross_pay), 2) as gross_pay, ROUND(SUM(a.basic_rate), 2) as basic_rate,
            ROUND(SUM(a.sss), 2) as sss_ee, ROUND(SUM(a.sss_er), 2) as sss_er, ROUND(SUM(a.sss_prov), 2) as sss_prov_ee, ROUND(SUM(a.sss_prov_er), 2) as sss_prov_er,
            IFNULL(comp.description, b.company_id) as company_description, IFNULL(comp.sss_class, 1) as classification, CONCAT(a.emp_id, '|', GROUP_CONCAT(DISTINCT(a.id))) as ps_group";
            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = b.company_id', "LEFT");
            $this->db->where_in("a.id", $filteredId);

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
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
            $this->db->group_by("a.emp_id");
            if (isset($sortOrder)) {
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            } else {
                $this->db->order_by('b.lastname', 'asc');
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $data = array();
                $arrTempAdjustments = array();
                $arrAdjustmentCount = array();
                foreach ($query->result() as $key => $item) {
                    $sssAdjustment = 0;
                    $provAdjustment = 0;

                    $item->ps_data = array();
                    $psGroup = explode('|', $item->ps_group);
                    if(is_array($psGroup) && count($psGroup) == 2 && $psGroup[1]){
                        $tempIds = explode(",", $psGroup[1]);
                        $psQuery = $this->db
                        ->select("a.pay_date, a.date_start, a.date_end, a.payroll_seq, a.sss, a.sss_er, a.sss_prov, a.sss_prov_er, GROUP_CONCAT(CONCAT(b.particulars, '|',b.amount,'|',b.adj_type)) as adjustment")
                        ->from($this->tbl_payroll_sheet." as a")
                        ->join($this->tbl_ps_created_adjustments." as b", "b.payroll_sheet_id = a.id AND b.status = 1", "left")
                        ->where_in('a.id', $tempIds)
                        ->group_by('a.id')
                        ->get();

                        $item->ps_data = $psQuery->result();
                    }

                    $tempAdjustment = $adjustments[$item->emp_id];
                    $item->adjustment = $tempAdjustment;
                    if(isset($tempAdjustment->created_adjustments) && $tempAdjustment->created_adjustments){
                        $createdAdjustment = explode(",", $tempAdjustment->created_adjustments);
                        if(is_array($createdAdjustment) && count($createdAdjustment) > 0){
                            foreach ($createdAdjustment as $kk => $vv) {
                                $_adjustment = explode("||", $vv);
                                if(is_array($_adjustment) && count($_adjustment) > 0){
                                    if(strtolower($_adjustment[0]) == "sss"){
                                        if(floatval($sssAdjustment) >= 0 && $_adjustment[2] == 1){
                                            $sssAdjustment += floatval($_adjustment[1]);
                                        }
                                        if(floatval($sssAdjustment) >= 0 && $_adjustment[2] == 0){
                                            $sssAdjustment -= floatval($_adjustment[1]);
                                        }
                                    }
                                    if(strtolower($_adjustment[0]) == "sss_prov"){
                                        if(floatval($provAdjustment) >= 0 && $_adjustment[2] == 1){
                                            $provAdjustment += floatval($_adjustment[1]);
                                        }
                                        if(floatval($provAdjustment) >= 0 && $_adjustment[2] == 0){
                                            $provAdjustment -= floatval($_adjustment[1]);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $item->_sss_adjustment = $sssAdjustment;
                    $item->_prov_adjustment = $provAdjustment;

                    $alteredKeys = array();
                    $tempRecord = (object) $this->core_layout->getEmployeeData($item->emp_id);
                    $tempName = (isset($tempRecord->display_name_0) && $tempRecord->display_name_0)? $tempRecord->display_name_0: "No assigned name";
                    $item->employee_name = $tempName;
                    $item->sss = array();

                    $_basic_rate = floatval($item->basic_rate);
                    $_gross_pay = floatval($item->gross_pay);
                    $_tempAmount = $_gross_pay;

                    $settings = $this->getSettings();
                    $sss_contribution_basis = $settings->sss_contribution_basis->setting_value;
                    $item->contribution_basis = $sss_contribution_basis;

                    $tempParameter = "_{$sss_contribution_basis}";
                    $sssContributionBasis = ${$tempParameter};
                    $_tempAmount = $sssContributionBasis ? $sssContributionBasis: $_gross_pay;
                    $item->amount_basis = $_tempAmount;
                    /*** insert code here ***/
                    
                    if(floatval($item->sss_ee) >= 0 && floatval($sssAdjustment) >= 0){
                        $item->sss_ee += floatval($sssAdjustment);
                        $item->sss_ee = round($item->sss_ee, 2);
                    }else if(floatval($item->sss_ee) >= 0 && floatval($sssAdjustment) < 0){
                        $item->sss_ee += floatval($sssAdjustment);
                        $item->sss_ee = round($item->sss_ee, 2);
                    }

                    if(floatval($item->sss_prov_ee) >= 0 && floatval($provAdjustment) >= 0){
                        $item->sss_prov_ee += floatval($provAdjustment);
                        $item->sss_prov_ee = round($item->sss_prov_ee, 2);
                    }else if(floatval($item->sss_prov_ee) >= 0 && floatval($provAdjustment) < 0){
                        $item->sss_prov_ee += floatval($provAdjustment);
                        $item->sss_prov_ee = round($item->sss_prov_ee, 2);
                    }

                    $tempProps = array("ee", "er", "ec_ee", "ec_er", "prov_ee", "prov_er");
                    foreach($tempProps as $ii){ $item->$ii = 0; }
                    if(floatval($item->sss_ee) > 0){
                        $sqlSelect = implode(",", $tempProps);
                        $sqlSelect .= ", id, from, to";
                        $this->db->select($sqlSelect);
                        $this->db->where("from <=", $_tempAmount);
                        $this->db->where("to >=", $_tempAmount);
                        $this->db->where("classification", $item->classification);
                        $this->db->where("status", 1);
                        $query = $this->db->get("payroll.sss_table");
                        if($query->num_rows() > 0){
                            $qTempRow = $query->row();
                            $item->sss = $qTempRow;
                            foreach($tempProps as $ii){
                                $item->$ii = $qTempRow->$ii;
                            }
                        }
                        if(isset($item->er) && floatval($item->er) > 0 
                            && ($item->sss_er == 0 || $item->sss_er !== $item->er)){
                            $item->sss_er = floatval($item->er);
                        }
                        if(isset($item->prov_er) && floatval($item->prov_er) > 0 
                            && ($item->sss_prov_er == 0 || $item->sss_prov_er !== $item->prov_er)){
                            $item->sss_prov_er = floatval($item->prov_er);
                        }
                    }

                    if(floatval($item->sss_ee) == 0){ $item->sss_er = 0; }
                    if(floatval($item->sss_prov_ee) == 0){ $item->sss_prov_er = 0; }

                    /*** temporary fix ***/
                    $hasIdentificationNumber = false;
                    if($item->sss_no){
                        $temp = preg_replace("/[^0-9]/", "", $item->sss_no);
                        $xtemp = intval($temp);
                        if($xtemp > 0){
                            $hasIdentificationNumber = true;
                        }
                    }
                    if($hasIdentificationNumber == false){ $item->sss_no = "00-0000000-0"; }
                    $item->has_identification_no = $hasIdentificationNumber;
                    $data[] = $item;
                    /*** $data[$key] = $item; ***/
                    /*** temporary fix ***/
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

    function sssRemittanceReportListCount($filteredId, $search){
        $count = 0;
        if(is_array($filteredId) && count($filteredId) > 0){
            $filterFields = array("b.firstname", "b.lastname", "b.sss_no", "b.idno");
            $this->db->select("a.id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.department_id, b.position, b.sss_no, b.idno");
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->where_in("a.id", $filteredId);

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
            $this->db->group_by("a.id", "desc");
            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }
            $this->db->order_by('b.lastname', 'asc');
            $query = $this->db->get();
            $count = $query->num_rows();
        }

        return $count;
    }

    function hdmfRemittanceReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder){
        if(is_array($filteredId) && count($filteredId) > 0){
            $filterFields = array("b.firstname", "b.lastname", "b.pagibig_no", "b.idno");
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id,
            b.pagibig_no, b.idno, a.rate, ROUND(SUM(a.hdmf), 2) as hdmf_ee, ROUND(SUM(a.hdmf), 2) as hdmf_er,
            IFNULL(comp.description, b.company_id) as company_description, cr_adj.amount, cr_adj.adj_type, 
            CONCAT(a.emp_id, '|', GROUP_CONCAT(DISTINCT(a.id))) as ps_group, 
            GROUP_CONCAT(DISTINCT cr_adj.payroll_sheet_id, '|', cr_adj.adj_type, '|', cr_adj.amount) as created_adjustments";
            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = b.company_id', "LEFT");
            $this->db->join('payroll.payroll_sheet_created_adjustments cr_adj', 
                'cr_adj.payroll_sheet_id = a.id AND cr_adj.particulars = "HDMF" AND cr_adj.status = 1', 
                "LEFT");
            $this->db->where_in("a.id", $filteredId);

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
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
            $this->db->group_by("a.emp_id");
            if (isset($sortOrder)) {
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            } else {
                $this->db->order_by('b.lastname', 'asc');
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $data = array();
                $arrTempAdjustments = array();
                $arrAdjustmentCount = array();
                foreach ($query->result() as $key => $item) {
                    $item->ps_data = array();
                    $psGroup = explode('|', $item->ps_group);
                    if(is_array($psGroup) && count($psGroup) == 2 && $psGroup[1]){
                        $tempIds = explode(",", $psGroup[1]);
                        $psQuery = $this->db
                        ->select("a.pay_date, a.date_start, a.date_end, a.payroll_seq, a.hdmf, GROUP_CONCAT(CONCAT(b.particulars, '|',b.amount,'|',b.adj_type)) as adjustment")
                        ->from($this->tbl_payroll_sheet." as a")
                        ->join($this->tbl_ps_created_adjustments." as b", "b.payroll_sheet_id = a.id AND b.status = 1", "left")
                        ->where_in('a.id', $tempIds)
                        ->group_by('a.id')
                        ->get();

                        $item->ps_data = $psQuery->result();
                    }
                    
                    $alteredKeys = array();
                    $tempRecord = (object) $this->core_layout->getEmployeeData($item->emp_id);
                    $tempName = (isset($tempRecord->display_name_0) && $tempRecord->display_name_0)? $tempRecord->display_name_0: "No assigned name";
                    $item->employee_name = $tempName;
                    $item->hdmf = array();
                    $tempBasicPay = floatval($item->rate);

                    $tempProps = array("ee", "er");
                    foreach($tempProps as $ii){ $item->$ii = 0; }
                    $sqlSelect = implode(",", $tempProps);
                    $sqlSelect .= ", id";
                    $this->db->select($sqlSelect);
                    $query = $this->db->get("payroll.hdmf_amount");
                    if($query->num_rows() > 0){
                        $qTempRow = $query->row();
                        $item->hdmf = $qTempRow;
                        foreach($tempProps as $ii){
                            $item->$ii = $qTempRow->$ii;
                        }
                    }

                    /*** if($item->hdmf_ee > 0){
                        if($item->amount && $item->amount > 0){
                            if($item->adj_type == 0){
                                $item->hdmf_ee = $item->hdmf_ee - $item->amount;
                                $item->hdmf_er = $item->hdmf_er - $item->amount;
                            }else{
                                $item->hdmf_ee = $item->hdmf_ee + $item->amount;
                                $item->hdmf_er = $item->hdmf_er + $item->amount;
                            }
                        }
                    } ***/

                    $tempAdjustmentAmount = 0;
                    $createdAdjustment = explode(",", $item->created_adjustments);
                    if(is_array($createdAdjustment) && count($createdAdjustment) > 0){
                        foreach ($createdAdjustment as $key => $adjustment) {
                            $currentAdjustment = explode("|", $adjustment);
                            if(is_array($currentAdjustment) && count($currentAdjustment) === 3){
                                $adjustmentType = intval($currentAdjustment[1]);
                                $adjustmentAmount = floatval($currentAdjustment[2]);
                                if($adjustmentAmount && $adjustmentAmount > 0){
                                    if($adjustmentType === 0){
                                        $tempAdjustmentAmount = $tempAdjustmentAmount - $adjustmentAmount;
                                    }else{
                                        $tempAdjustmentAmount = $tempAdjustmentAmount + $adjustmentAmount;
                                    }
                                }
                            }
                        }
                    }

                    $item->hdmf_ee += $tempAdjustmentAmount;
                    $item->hdmf_er += $tempAdjustmentAmount;

                    /*** temporary fix ***/
                    $hasIdentificationNumber = false;
                    if($item->pagibig_no){
                        $temp = preg_replace("/[^0-9]/", "", $item->pagibig_no);
                        $xtemp = intval($temp);
                        if($xtemp > 0){
                            $hasIdentificationNumber = true;
                        }
                    }
                    if($hasIdentificationNumber){
                        $data[] = $item;
                    }
                    /*** $data[$key] = $item; ***/
                    /*** temporary fix ***/
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

    function hdmfRemittanceReportListCount($filteredId, $search){
        $count = 0;
        if(is_array($filteredId) && count($filteredId) > 0){
            $filterFields = array("b.firstname", "b.lastname", "b.pagibig_no", "b.idno");
            $this->db->select("a.id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.department_id, b.position, b.pagibig_no, b.idno");
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->where_in("a.id", $filteredId);

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
            $this->db->group_by("a.id", "desc");
            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }
            $this->db->order_by('b.lastname', 'asc');
            $query = $this->db->get();
            $count = $query->num_rows();
        }

        return $count;
    }

    function phicRemittanceReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder){
        if(is_array($filteredId) && count($filteredId) > 0){
            $filterFields = array("b.firstname", "b.lastname", "b.phealth_no", "b.idno");
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id,
            b.phealth_no, b.idno, a.rate, ROUND(SUM(a.ph), 2) as ph_ee, ROUND(SUM(a.ph), 2) as ph_er,
            IFNULL(comp.description, b.company_id) as company_description, cr_adj.amount, cr_adj.adj_type, 
            CONCAT(a.emp_id, '|', GROUP_CONCAT(DISTINCT(a.id))) as ps_group, 
            GROUP_CONCAT(DISTINCT cr_adj.payroll_sheet_id, '|', cr_adj.adj_type, '|', cr_adj.amount) as created_adjustments";
            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = b.company_id', "LEFT");
            $this->db->join('payroll.payroll_sheet_created_adjustments cr_adj', 
                'cr_adj.payroll_sheet_id = a.id AND cr_adj.particulars = "PHIC" AND cr_adj.status = 1', 
                "LEFT");
            $this->db->where_in("a.id", $filteredId);
            // $this->db->where("cr_adj.particulars", "PHIC");
            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
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
            $this->db->group_by("a.emp_id");
            if (isset($sortOrder)) {
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            } else {
                $this->db->order_by('b.lastname', 'asc');
            }

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $data = array();
                $arrTempAdjustments = array();
                $arrAdjustmentCount = array();
                foreach ($query->result() as $key => $item) {
                    $item->ps_data = array();
                    $psGroup = explode('|', $item->ps_group);
                    if(is_array($psGroup) && count($psGroup) == 2 && $psGroup[1]){
                        $tempIds = explode(",", $psGroup[1]);
                        $psQuery = $this->db
                        ->select("a.pay_date, a.date_start, a.date_end, a.payroll_seq, a.ph, GROUP_CONCAT(CONCAT(b.particulars, '|',b.amount,'|',b.adj_type)) as adjustment")
                        ->from($this->tbl_payroll_sheet." as a")
                        ->join($this->tbl_ps_created_adjustments." as b", "b.payroll_sheet_id = a.id AND b.status = 1", "left")
                        ->where_in('a.id', $tempIds)
                        ->group_by('a.id')
                        ->get();

                        $item->ps_data = $psQuery->result();
                    }

                    $alteredKeys = array();
                    $tempRecord = (object) $this->core_layout->getEmployeeData($item->emp_id);
                    $tempName = (isset($tempRecord->display_name_0) && $tempRecord->display_name_0)? $tempRecord->display_name_0: "No assigned name";
                    $item->employee_name = $tempName;
                    $item->phic = array();
                    $tempBasicPay = floatval($item->rate);

                    $tempProps = array("ee", "er");
                    foreach($tempProps as $ii){ $item->$ii = 0; }
                    $sqlSelect = implode(",", $tempProps);
                    $sqlSelect .= ", id, min, max, percentage, fixAmount";
                    $this->db->select($sqlSelect);
                    $this->db->where("min <=", $tempBasicPay);
                    $this->db->where("max >=", $tempBasicPay);
                    $query = $this->db->get("payroll.philhealth_table");
                    if($query->num_rows() > 0){
                        $qTempRow = $query->row();
                        $item->phic = $qTempRow;
                        $tempAmount = 0;
                        if($qTempRow->fixAmount){
                            $tempAmount = floatval($qTempRow->fixAmount) / 2;
                        }else{
                            $tempPercent = floatval($qTempRow->percentage) / 100;
                            if($tempPercent > 0){
                                $tempAmount = $tempBasicPay * $tempPercent;
                                $tempAmount = $tempAmount / 2;
                            }
                        }

                        foreach($tempProps as $ii){
                            $qTempRow->$ii = $tempAmount;
                            $item->$ii = $qTempRow->$ii;
                        }
                    }

                    /*** if($item->ph_ee > 0){
                        if($item->amount && $item->amount > 0){
                            if($item->adj_type == 0){
                                $item->ph_ee = $item->ph_ee - $item->amount;
                                $item->ph_er = $item->ph_er - $item->amount;
                            }else{
                                $item->ph_ee = $item->ph_ee + $item->amount;
                                $item->ph_er = $item->ph_er + $item->amount;
                            }
                        }
                    } ***/

                    $tempAdjustmentAmount = 0;
                    $createdAdjustment = explode(",", $item->created_adjustments);
                    if(is_array($createdAdjustment) && count($createdAdjustment) > 0){
                        foreach ($createdAdjustment as $key => $adjustment) {
                            $currentAdjustment = explode("|", $adjustment);
                            if(is_array($currentAdjustment) && count($currentAdjustment) === 3){
                                $adjustmentType = intval($currentAdjustment[1]);
                                $adjustmentAmount = floatval($currentAdjustment[2]);
                                if($adjustmentAmount && $adjustmentAmount > 0){
                                    if($adjustmentType === 0){
                                        $tempAdjustmentAmount = $tempAdjustmentAmount - $adjustmentAmount;
                                    }else{
                                        $tempAdjustmentAmount = $tempAdjustmentAmount + $adjustmentAmount;
                                    }
                                }
                            }
                        }
                    }

                    $item->ph_ee += $tempAdjustmentAmount;
                    $item->ph_er += $tempAdjustmentAmount;

                    /*** temporary fix ***/
                    $hasIdentificationNumber = false;
                    if($item->phealth_no){
                        $temp = preg_replace("/[^0-9]/", "", $item->phealth_no);
                        $xtemp = intval($temp);
                        if($xtemp > 0){
                            $hasIdentificationNumber = true;
                        }
                    }
                    if($hasIdentificationNumber){
                        $data[] = $item;
                    }
                    /*** $data[$key] = $item; ***/
                    /*** temporary fix ***/
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

    function phicRemittanceReportListCount($filteredId, $search){
        $count = 0;
        if(is_array($filteredId) && count($filteredId) > 0){
            $filterFields = array("b.firstname", "b.lastname", "b.phealth_no", "b.idno");
            $this->db->select("a.id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.department_id, b.position, b.phealth_no, b.idno");
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->where_in("a.id", $filteredId);

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
            $this->db->group_by("a.id", "desc");
            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }
            $this->db->order_by('b.lastname', 'asc');
            $query = $this->db->get();
            $count = $query->num_rows();
        }

        return $count;
    }

    function getRemittanceReportRequest(){
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
        $visibleFields = (isset($post["visible_fields"]) && $post["visible_fields"]) ? $post["visible_fields"] : array();
        $clearTable = (isset($post["clear_table"]) && $post["clear_table"] == "true") ? true : false;

        $rowData = $this->remittanceReportList($filteredId, $visibleFields, $search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->remittanceReportListCount($filteredId, $search);

        $totalNotFiltered = $rowCount;

        if($clearTable == true){
            $rowCount = 0;
            $rowData = array();
        }

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = isset($rowData["data"]) && $rowData["data"]? $rowData["data"]: array();
        $resultset["adjustment_columns"] = isset($rowData["adjustment_columns"]) && $rowData["adjustment_columns"]? $rowData["adjustment_columns"]: 0;
        $resultset["show_adjustments"] = isset($visibleFields) && in_array("adjustment", $visibleFields)? true: false;
        $resultset["clear_table"] = $clearTable;

        return $resultset;
    }

    function generatePayrollSheetAdjustments($filteredId=array()){
        $resultset = array();
        $resultset["data"] = array();
        $resultset["adjustment_columns"] = 0;

        if(is_array($filteredId) && count($filteredId) > 0){
            $remittanceProps = array("sss", "hdmf", "ph", "tax");
            $sqlSelect = "a.emp_id, GROUP_CONCAT(DISTINCT(CONCAT(psca.particulars,'||',psca.amount, '||', psca.adj_type))) created_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(psa.particulars,'||',psa.amount, '||', psa.cadj_type))) custom_adjustments";

            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('payroll.payroll_sheet_created_adjustments psca', 'psca.payroll_sheet_id = a.id AND psca.status = 1', "LEFT");
            $this->db->join('payroll.payroll_sheet_custom_adjustments psa', 'psa.payroll_sheet_id = a.id AND psa.cadj_type = 0', "LEFT");
            $this->db->where_in("a.id", $filteredId);
            $this->db->where("a.posted", 1);
            $this->db->group_by("a.emp_id");
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $data = array();
                $maxAdjustmentColumns = 0;
                $arrTempAdjustments = array();
                $arrAdjustmentCount = array();
                foreach ($query->result() as $key => $item) {
                    $alteredKeys = array();
                    foreach($remittanceProps as $prop){ $item->$prop = 0; }

                    $empId = $item->emp_id;
                    unset($item->emp_id);

                    if(isset($item->custom_adjustments) && $item->custom_adjustments){
                        $tempAdjustment = explode(",", $item->custom_adjustments);
                        if(is_array($tempAdjustment) && count($tempAdjustment) > 0){
                            $tempCtr = 0;
                            $arrIndex = array();
                            foreach ($tempAdjustment as $kk => $vv) {
                                $adjustment = explode("||", $vv);
                                if(is_array($adjustment) && count($adjustment) == 3){
                                    $label = strtolower($adjustment[0]);
                                    if(intval($adjustment[2]) == 0){
                                        $arrTempAdjustment = array();
                                        $arrTempAdjustment["label"] = $label;
                                        $arrTempAdjustment["value"] = $adjustment[1];
                                        $arrIndex[] = $arrTempAdjustment;
                                        $tempCtr++;
                                    }
                                }
                            }
                            $arrTempAdjustments[$empId] = $arrIndex;
                            $arrAdjustmentCount[] = $tempCtr;
                        }
                    }

                    if(isset($item->created_adjustments) && $item->created_adjustments){
                        $tempAdjustment = explode(",", $item->created_adjustments);
                        if(is_array($tempAdjustment) && count($tempAdjustment) > 0){
                            foreach ($tempAdjustment as $kk => $vv) {
                                $adjustment = explode("||", $vv);
                                if(is_array($adjustment) && count($adjustment) == 3){
                                    $tempKey = strtolower($adjustment[0]);
                                    if(isset($item->{$tempKey})){
                                        if(!in_array($tempKey, $alteredKeys)){ $alteredKeys[] = $tempKey; }
                                        if($adjustment[2] == 1){
                                            $item->{$tempKey} += floatval($adjustment[1]);
                                        }else{
                                            $item->{$tempKey} -= floatval($adjustment[1]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $data[$empId] = $item;
                }

                if($data && count($data) > 0){
                    $tempData = array();
                    $maxColumns = (count($arrAdjustmentCount) > 0)? max($arrAdjustmentCount): 0;
                    if($maxColumns > 0){
                        foreach ($data as $key => $value) {
                            if(isset($arrTempAdjustments[$key]) && $arrTempAdjustments[$key]){
                                for($i=0; $i<$maxColumns; $i++){
                                    $x = $i + 1;
                                    $tempKey0 = "adjustment_{$x}_value";
                                    $tempKey1 = "adjustment_{$x}_description";
                                    if(isset($arrTempAdjustments[$key][$i]) && $arrTempAdjustments[$key][$i]){
                                        $arrDataxxx = $arrTempAdjustments[$key][$i];

                                        $ttValue = (isset($arrDataxxx["value"]) && $arrDataxxx["value"])? floatval($arrDataxxx["value"]): 0;
                                        $ttLabel = (isset($arrDataxxx["label"]) && $arrDataxxx["label"])? strtoupper($arrDataxxx["label"]): null;

                                        $tempLabel = ($ttLabel)? "<p class='m--marginless'><small>{$ttLabel}</small></p>{$ttValue}": $ttValue;
                                        $value->{$tempKey0} = $ttValue;
                                        $value->{$tempKey1} = $tempLabel;
                                    }else{
                                        $value->{$tempKey0} = 0;
                                        $value->{$tempKey1} = 0;
                                    }
                                }
                            }else{
                                for($i=1; $i<=$maxColumns; $i++){
                                    $tempKey0 = "adjustment_{$i}_value";
                                    $tempKey1 = "adjustment_{$i}_description";
                                    $value->{$tempKey0} = 0;
                                    $value->{$tempKey1} = 0;
                                }
                            }
                            $value->adjustment_columns = $maxColumns;
                            $tempData[$key] = $value;
                        }
                        $data = $tempData;
                        $maxAdjustmentColumns = $maxColumns;
                    }
                }
                $resultset["data"] = $data;
                $resultset["adjustment_columns"] = $maxAdjustmentColumns;
            }
        }
        return $resultset;
    }

    function remittanceReportList($filteredId, $visibleFields, $search, $limit, $offset, $sortBy, $sortOrder){
        if(is_array($filteredId) && count($filteredId) > 0){
            $maxAdjustmentColumns = 0;
            $tempAdjustments = $this->generatePayrollSheetAdjustments($filteredId);
            if(isset($tempAdjustments["adjustment_columns"]) && $tempAdjustments["adjustment_columns"] > 0){
                $maxAdjustmentColumns = intval($tempAdjustments["adjustment_columns"]);
            }

            $tempDatax = array();
            if(isset($tempAdjustments["data"]) && count($tempAdjustments["data"]) > 0){
                $tempDatax = $tempAdjustments["data"];
            }
            $remittanceFields = array("sss", "hdmf", "ph", "tax");

            $filterFields = array("b.firstname", "b.lastname");
            /*** $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id,
            ROUND(SUM(a.basic_rate), 2) as basic_rate, ROUND(SUM(a.gross_pay), 2) as gross_pay,
            IFNULL(comp.description, b.company_id) as company_description,
            ROUND(SUM(a.sss), 2) as sss, ROUND(SUM(a.hdmf), 2) as hdmf, ROUND(SUM(a.ph), 2) as ph,
            ROUND(SUM(a.tax), 2) as tax,
            GROUP_CONCAT(DISTINCT(CONCAT(psca.particulars,'||',psca.amount, '||', psca.adj_type))) created_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(psa.particulars,'||',psa.amount, '||', psa.cadj_type))) custom_adjustments, "; ***/
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id,
            ROUND(SUM(a.basic_rate), 2) as basic_rate, ROUND(SUM(a.gross_pay), 2) as gross_pay,
            IFNULL(comp.description, b.company_id) as company_description,
            ROUND(SUM(a.sss), 2) as sss, ROUND(SUM(a.hdmf), 2) as hdmf, ROUND(SUM(a.ph), 2) as ph,
            ROUND(SUM(a.tax), 2) as tax";

            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = b.company_id', "LEFT");
            $this->db->where_in("a.id", $filteredId);

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
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
            $this->db->group_by("a.emp_id");
            if (isset($sortOrder)) {
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            } else {
                $this->db->order_by('b.lastname', 'asc');
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $data = array();
                $arrTempAdjustments = array();
                $arrAdjustmentCount = array();
                foreach ($query->result() as $key => $item) {
                    $alteredKeys = array();
                    $tempRecord = (object) $this->core_layout->getEmployeeData($item->emp_id);
                    $tempName = (isset($tempRecord->display_name_1) && $tempRecord->display_name_1)? $tempRecord->display_name_1: "No assigned name";
                    $item->employee_name = $tempName;
                    $tempRowDD = $tempDatax[$item->emp_id];
                    foreach($tempRowDD as $xx => $ii){
                        if(in_array($xx, $remittanceFields)){
                            $tempValue = $ii;
                            $currentValue = $item->$xx;
                            $item->$xx = floatval($currentValue) + $tempValue;
                        }else{
                            $item->$xx = $ii;
                        }
                    }
                    /*** if(isset($item->custom_adjustments) && $item->custom_adjustments){
                        $tempAdjustment = explode(",", $item->custom_adjustments);
                        if(is_array($tempAdjustment) && count($tempAdjustment) > 0){
                            $tempCtr = 0;
                            $arrIndex = array();
                            foreach ($tempAdjustment as $kk => $vv) {
                                $adjustment = explode("||", $vv);
                                if(is_array($adjustment) && count($adjustment) == 3){
                                    $label = strtolower($adjustment[0]);
                                    if(intval($adjustment[2]) == 0){
                                        $arrTempAdjustment = array();
                                        $arrTempAdjustment["label"] = $label;
                                        $arrTempAdjustment["value"] = $adjustment[1];
                                        $arrIndex[] = $arrTempAdjustment;
                                        $tempCtr++;
                                    }
                                }
                            }
                            $arrTempAdjustments[$key] = $arrIndex;
                            $arrAdjustmentCount[] = $tempCtr;
                        }
                    }

                    if(isset($item->created_adjustments) && $item->created_adjustments){
                        $tempAdjustment = explode(",", $item->created_adjustments);
                        if(is_array($tempAdjustment) && count($tempAdjustment) > 0){
                            foreach ($tempAdjustment as $kk => $vv) {
                                $adjustment = explode("||", $vv);
                                if(is_array($adjustment) && count($adjustment) == 3){
                                    $tempKey = strtolower($adjustment[0]);
                                    if(isset($item->{$tempKey}) && $item->{$tempKey}){
                                        if(!in_array($tempKey, $alteredKeys)){ $alteredKeys[] = $tempKey; }
                                        if($adjustment[2] == 1){
                                            $item->{$tempKey} += floatval($adjustment[1]);
                                        }else{
                                            $item->{$tempKey} -= floatval($adjustment[1]);
                                        }
                                    }
                                }
                            }
                        }
                    } ***/
                    if(is_array($remittanceFields) && count($remittanceFields) > 0){
                        foreach ($remittanceFields as $kkx => $vvx) {
                            $item->{$vvx} = number_format($item->{$vvx}, 2, ".", ",");
                        }
                    }
                    $item->visible_fields = $visibleFields;
                    $item->adjustment_columns = $maxAdjustmentColumns;
                    $data[$key] = $item;
                }

                /*** if($data && count($data) > 0){
                    $tempData = array();
                    $maxColumns = (count($arrAdjustmentCount) > 0)? max($arrAdjustmentCount): 0;
                    if($maxColumns > 0){
                        foreach ($data as $key => $value) {
                            if(isset($arrTempAdjustments[$key]) && $arrTempAdjustments[$key]){
                                for($i=0; $i<$maxColumns; $i++){
                                    $x = $i + 1;
                                    $tempKey0 = "adjustment_{$x}_value";
                                    $tempKey1 = "adjustment_{$x}_description";
                                    if(isset($arrTempAdjustments[$key][$i]) && $arrTempAdjustments[$key][$i]){
                                        $arrDataxxx = $arrTempAdjustments[$key][$i];

                                        $ttValue = (isset($arrDataxxx["value"]) && $arrDataxxx["value"])? floatval($arrDataxxx["value"]): 0;
                                        $ttLabel = (isset($arrDataxxx["label"]) && $arrDataxxx["label"])? strtoupper($arrDataxxx["label"]): null;

                                        $tempLabel = ($ttLabel)? "<p class='m--marginless'><small>{$ttLabel}</small></p>{$ttValue}": $ttValue;
                                        $value->{$tempKey0} = $ttValue;
                                        $value->{$tempKey1} = $tempLabel;
                                    }else{
                                        $value->{$tempKey0} = 0;
                                        $value->{$tempKey1} = 0;
                                    }
                                }
                            }else{
                                for($i=1; $i<=$maxColumns; $i++){
                                    $tempKey0 = "adjustment_{$i}_value";
                                    $tempKey1 = "adjustment_{$i}_description";
                                    $value->{$tempKey0} = 0;
                                    $value->{$tempKey1} = 0;
                                }
                            }
                            $value->adjustment_columns = $maxColumns;
                            $tempData[$key] = $value;
                        }
                        $data = $tempData;
                        $maxAdjustmentColumns = $maxColumns;
                    }
                } ***/

                $resultset = array();
                $resultset["data"] = $data;
                $resultset["adjustment_columns"] = $maxAdjustmentColumns;
                return $resultset;
            } else {
                return array();
            }
        }else{
            return array();
        }
    }

    function remittanceReportListCount($filteredId, $search){
        $count = 0;
        if(is_array($filteredId) && count($filteredId) > 0){
            $filterFields = array("b.firstname", "b.lastname");
            $this->db->select("a.id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.department_id, b.position");
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->where_in("a.id", $filteredId);

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
            $this->db->group_by("a.id", "desc");
            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }
            $this->db->order_by('b.lastname', 'asc');
            $query = $this->db->get();
            $count = $query->num_rows();
        }

        return $count;
    }

    function generateRemittanceReport(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $tempGroup = "PAY DATE";
            $arrGroup = array(1=>"PAY DATE", 2=>"MONTH", 3=>"YEAR");
            $group = (isset($post["group"]) && $post["group"])? intval($post["group"]): 1;

            $arrCompany = array();
            $employeeIds = array();
            $tempStartDate = null;
            $tempEndDate = null;
            $hasDataFilter = false;
            $visibleFields = array();

            $tempGroup = $arrGroup[$group];
            $tempArrFilter = array();
            $tempArrFilter["filter_by"] = $tempGroup;

            if(isset($post["visible_fields"]) && $post["visible_fields"]){
                $visibleFields = explode(",", $post["visible_fields"]);
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


            $this->db->select("a.id");
            $this->db->from("gccmaster.tblemployees a");
            if(isset($post["employee"]) && $post["employee"]){
                $hasDataFilter = true;
                /*** $this->db->where("a.employee_status", "Active"); ***/
                $this->db->where_in("a.id", $post["employee"]);
            }
            /*** $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id OR b.description = a.company_id OR b.code = a.company_id", "LEFT");
            if(isset($post["company"], $post["employee"]) && ($post["company"] && $post["employee"])){
                $hasDataFilter = true;
                $this->db->where("a.employee_status", "Active");
                $this->db->where_in("a.id", $post["employee"]);
                $this->db->where_in("b.id", $post["company"]);
            }else if(isset($post["company"]) && $post["company"]){
                $hasDataFilter = true;
                $this->db->where("a.employee_status", "Active");
                $this->db->where_in("b.id", $post["company"]);
            }else if(isset($post["employee"]) && $post["employee"]){
                $hasDataFilter = true;
                $this->db->where("a.employee_status", "Active");
                $this->db->where_in("a.id", $post["employee"]);
            } ***/
            $this->db->group_by("a.id");
            $queryTemp = $this->db->get();
            if($queryTemp->num_rows() > 0 && $hasDataFilter == true){
                foreach ($queryTemp->result() as $key => $value) {
                    if(!in_array($value->id, $employeeIds)){ $employeeIds[] = $value->id; }
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
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("{$post["filter_year"]}-01-01", strtotime("{$post["filter_year"]}"));
                $tempEndDate = date("{$post["filter_year"]}-12-31", strtotime("{$post["filter_year"]}"));
            }

            if($tempStartDate && $tempEndDate){
                $xDateFrom = date("F d, Y", strtotime($tempStartDate));
                $xDateTo = date("F d, Y", strtotime($tempEndDate));
                $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");
                $this->db->select("id");
                $this->db->from("payroll.payroll_sheet");
                $this->db->where("posted", 1);
                $this->db->where("is_bonus", 0);
                $this->db->group_start();
                $this->db->where("DATE(date_start) >=", $tempStartDate);
                $this->db->where("DATE(date_end) <=", $tempEndDate);
                $this->db->or_where("DATE(pay_date) >=", $tempStartDate);
                $this->db->where("DATE(pay_date) <=", $tempEndDate);
                $this->db->group_end();
                if($hasDataFilter && count($employeeIds) > 0){
                    $this->db->where_in("emp_id", $employeeIds);
                }
                if(isset($post["company"]) && $post["company"]){
                    $this->db->where_in("company_id", $post["company"]);
                }

                $query = $this->db->get();
                $tempSql = $this->db->last_query();
                /*** $resultset["sql"] = $tempSql; ***/

                if($query->num_rows() > 0){
                    $ids = array();
                    foreach ($query->result() as $key => $value) {
                        if(!in_array($value->id, $ids)){
                            $ids[] = $value->id;
                        }
                    }
                    $tempCount = count($ids);
                    if(is_array($ids) && $tempCount > 0){
                        $ids = array_map("intval", $ids);
                        $resultset["response"] = true;
                        $resultset["data"] = $ids;
                        $resultset["visible_fields"] = $visibleFields;
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

        return $resultset;
    }

    function generateSssRemittanceReport($type=null){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $tempGroup = "PAY DATE";
            $arrGroup = array(1=>"PAY DATE", 2=>"MONTH", 3=>"YEAR");
            $group = (isset($post["group"]) && $post["group"])? intval($post["group"]): 1;

            $tempCompany = null;
            $employeeIds = array();
            $tempStartDate = null;
            $tempEndDate = null;
            $hasDataFilter = false;
            $visibleFields = array();

            $tempGroup = $arrGroup[$group];
            $tempArrFilter = array();
            $isWeeklyEmployees = array();
            $tempArrFilter["filter_by"] = $tempGroup;
            
            if(isset($post["company"]) && $post["company"]){
                $this->db->select("description");
                $this->db->from("gcchris.tblcompanies");
                $this->db->where("id", $post["company"]);
                $qTemp = $this->db->get();
                if($qTemp->num_rows() == 1){
                    $tempRow = $qTemp->row();
                    $tempCompany = strtoupper($tempRow->description);
                    $tempArrFilter["companies"] = $tempCompany;
                }
            }

            $isPaydate = strtolower($tempGroup) === "pay date";
            /*** altered code section start ***/
            $isMonthFilter = isset($post["filter_month"], $post["filter_year"]) || strtolower($tempGroup) === "month";
            /*** altered code section end ***/

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
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $filter_month = $post['filter_month'];
            } elseif (isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("{$post['filter_year']}-01-01", strtotime("{$post["filter_year"]}"));
                $tempEndDate = date("{$post['filter_year']}-12-31", strtotime("{$post["filter_year"]}"));
                $filter_month = "";
            }

            /*** $this->db->select("a.id, d.name as payout_schedule_name");
            $this->db->from("gccmaster.tblemployees a");
            $this->db->join("payroll.payroll_sheet b", "b.emp_id = a.id", "LEFT");
            $this->db->join("gcchris.tblcompanies c", "c.id = b.company_id", "LEFT");
            $this->db->join("payroll.payout_schedule d", "d.id = b.payroll_sched", "INNER");
            $this->db->where("c.id", $post["company"]); ***/

            $this->db->select("a.id, c.name as payout_schedule_name");
            $this->db->from("gccmaster.tblemployees a");
            $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = a.id", "INNER");
            $this->db->join("gcchris.tblcompanies b", "b.id = ps.company_id", "LEFT");
            /*** $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id OR b.description = a.company_id OR b.code = a.company_id", "LEFT"); ***/
            $this->db->join("payroll.payout_schedule c", "c.id = a.payout_sched", "INNER");
            $this->db->where("b.id", $post["company"]);
            if(isset($post["employee"]) && $post["employee"]){
                $this->db->where_in("a.id", $post["employee"]);
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
                    } elseif (strtolower($value->payout_schedule_name) === "weekly"
                        && !in_array($value->id, $isWeeklyEmployees)){
                        $isWeeklyEmployees[] = $value->id;
                    }
                }
            }

            if($tempStartDate && $tempEndDate){
                $weeklyPsIds = array();
                $employeePsIds = array();

                if(is_array($isWeeklyEmployees) && count($isWeeklyEmployees) > 0){
                    $responseWeeklyRange = $this->generateWeeklyMonthRange($tempStartDate, $tempEndDate);
                    if($responseWeeklyRange){
                        $_tempStartDate = $responseWeeklyRange["date_start"];
                        $_tempEndDate = $responseWeeklyRange["date_end"];
                        $_tempPayDateStart = $responseWeeklyRange["paydate_start"];
                        $_tempPayDateEnd = $responseWeeklyRange["paydate_end"];

                        if(strtotime($_tempEndDate) < strtotime($_tempStartDate)){
                            $_tempEndDate = Date("Y-m-d", strtotime("+1 year", strtotime($_tempEndDate)));
                        }
                        if(strtotime($_tempPayDateEnd) < strtotime($_tempPayDateStart)){
                            $_tempPayDateEnd = Date("Y-m-d", strtotime("+1 year", strtotime($_tempPayDateEnd)));
                        }

                        $this->db->select("id");
                        $this->db->from("payroll.payroll_sheet");
                        $this->db->where("posted", 1);
                        $this->db->where("is_bonus", 0);
                        if($isMonthFilter === true){
                            $_monthStartDate = date("Y-m-d", strtotime("first day of this month", strtotime($tempStartDate)));
                            $_monthEndDate = date("Y-m-d", strtotime("last day of this month", strtotime($_monthStartDate)));
                            $this->db->group_start();
                                $this->db->group_start();
                                    $this->db->where("DATE(date_start) >=", $_tempStartDate);
                                    $this->db->where("DATE(date_end) <=", $_tempEndDate);
                                $this->db->group_end();
                                $this->db->or_group_start();
                                    $this->db->where("DATE(date_start) >=", $_monthStartDate);
                                    $this->db->where("DATE(date_end) <=", $_monthEndDate);
                                $this->db->group_end();
                            $this->db->group_end();
                        }else{
                            $this->db->group_start();
                                $this->db->where("DATE(date_start) >=", $_tempStartDate);
                                $this->db->where("DATE(date_end) <=", $_tempEndDate);
                            $this->db->group_end();
                        }
                        
                        if($isPaydate){
                            $this->db->group_start();
                                $this->db->or_where("DATE(pay_date) >=", $_tempPayDateStart);
                                $this->db->where("DATE(pay_date) <=", $_tempPayDateEnd);
                            $this->db->group_end();
                        }
                        $this->db->where_in("emp_id", $isWeeklyEmployees);
                        if(isset($post["company"]) && $post["company"]){
                            $this->db->where("company_id", $post["company"]);
                        }

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

                /*** altered function here ***/
                $weeklyPsIds = $this->updateWeeklyEmployeesPsId($tempStartDate, $tempEndDate, $isMonthFilter, $weeklyPsIds);
                /*** altered function here ***/

                if(is_array($employeeIds) && count($employeeIds) > 0){
                    if(isset($post["filter_year"])){
                        $tempArrFilter["month"] = strtoupper(date("Y F", strtotime("{$post["filter_year"]}-{$filter_month}")));
                    }
                    
                    $this->db->select("id");
                    $this->db->from("payroll.payroll_sheet");
                    $this->db->where("posted", 1);
                    $this->db->where("is_bonus", 0);
                    $this->db->group_start();
                    $this->db->where("DATE(date_start) >=", $tempStartDate);
                    $this->db->where("DATE(date_end) <=", $tempEndDate);
                    $this->db->or_where("DATE(pay_date) >=", $tempStartDate);
                    $this->db->where("DATE(pay_date) <=", $tempEndDate);
                    $this->db->group_end();
                    $this->db->where_in("emp_id", $employeeIds);
                    if(isset($post["company"]) && $post["company"]){
                        $this->db->where("company_id", $post["company"]);
                    }
    
                    $query = $this->db->get();
                    $tempSql = $this->db->last_query();
                    
                    $resultset["sql_query"] = $tempSql;
                    if($query->num_rows() > 0){
                        foreach ($query->result() as $key => $value) {
                            if(!in_array($value->id, $employeePsIds)){
                                $employeePsIds[] = $value->id;
                            }
                        }
                    }
                }

                $xDateFrom = date("M d, Y", strtotime($tempStartDate));
                $xDateTo = date("M d, Y", strtotime($tempEndDate));
                $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");
                
                if((is_array($employeePsIds) && count($employeePsIds) > 0) || (is_array($weeklyPsIds) && count($weeklyPsIds) > 0)){
                    $employeePsIds = array_unique(array_merge($employeePsIds, $weeklyPsIds));
                    if(is_array($employeePsIds) && count($employeePsIds) > 0){
                        $employeePsIds = array_map("intval", $employeePsIds);

                        $tempParams = array("company_id"=>$post["company"]);

                        if(isset($post["filter_year"]) && $post["filter_year"]){
                            $tempParams["year"] = $post["filter_year"];
                        }

                        if($isMonthFilter){
                            $_tempYear = $post["filter_year"];
                            $_monthIndex = $post["filter_month"];
                            $_monthName = date('F', mktime(0, 0, 0, $_monthIndex, 1, $_tempYear));
                            $_monthName = strtolower($_monthName);
                            $tempParams["month_name"] = $_monthName;
                        }

                        $remittances = array();
                        if($type == "sss"){
                            $remittances = $this->setGeneratedSssRemittances($employeePsIds);
                        }else if($type == "phic"){
                            $remittances = $this->setGeneratedPhicRemittances($employeePsIds);
                        }else if($type == "hdmf"){
                            $remittances = $this->setGeneratedHdmfRemittances($employeePsIds);
                        }

                        $tempType = strtoupper($type);

                        if(is_array($remittances) && count($remittances) > 0){
                            $resultset["response"] = true;
                            $resultset["data"] = $remittances;
                            $resultset["filters"] = $tempArrFilter;
                            $resultset["toastr_msg"] = "{$tempType} Remittances, posted payroll entries found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "No remittances found!";
                        }

                        /*** $resultset["response"] = true;
                        $resultset["data"] = $employeePsIds;
                        $resultset["is_weekly_employees"] = $isWeeklyEmployees;
                        $resultset["filters"] = $tempArrFilter;
                        $resultset["toastr_msg"] = "Posted payroll entries found."; ***/
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

        return $resultset;
    }

    protected function updateWeeklyEmployeesPsId($tempStartDate, $tempEndDate, $isMonthFilter, $weeklyPsIds = []){
        if(!empty($weeklyPsIds) && $tempStartDate && $tempEndDate && $isMonthFilter){
            $arrSeq = array();
            $this->db->select("id, emp_id, payroll_seq, date_start");
            $this->db->from("payroll.payroll_sheet");
            $this->db->where_in("id", $weeklyPsIds);
            $this->db->order_by("emp_id, id", "asc");
            $queryWeekly = $this->db->get();
            if($queryWeekly->num_rows() > 0){
                foreach ($queryWeekly->result() as $qw) {
                    $empId = $qw->emp_id;
                    $seq   = (int) $qw->payroll_seq;
                    
                    $arrSeq[$empId]["date_start"] = $qw->date_start;
                    $arrSeq[$empId]["sequence"][] = $seq;
                    $arrSeq[$empId]["ps_id"][] = (int) $qw->id;
                }
            }
            $toRemovePsId = array();
            $toIncludePsId = [];
            if(is_array($arrSeq) && !empty($arrSeq)){
                foreach ($arrSeq as $kk => $sequence) {
                    $removeKeys = [];
                    $sq = $sequence["sequence"];
                    for($i=1; $i<count($sq); $i++){
                        if ($sq[$i] < $sq[$i - 1]) {
                            $removeKeys = range($i, count($sq) - 1);
                            break;
                        }
                    }

                    if(!empty($removeKeys)){
                        $psIds = $sequence["ps_id"];
                        foreach ($removeKeys as $psKey) {
                            $toRemovePsId[] = $psIds[$psKey];
                        }
                    }


                    if(!empty($sq) && $sq[0] !== 1){
                        $tempEmployeeId = $kk;
                        $prevStartDate = date("Y-m-d", strtotime("-1 month", strtotime($tempStartDate)));
                        $prevEndDate = date("Y-m-d", strtotime("-1 month", strtotime($tempEndDate)));

                        $responseWeeklyRange = $this->generateWeeklyMonthRange($prevStartDate, $prevEndDate);
                        if($responseWeeklyRange){
                            $_tempStartDate = $responseWeeklyRange["date_start"];
                            $_tempEndDate = $responseWeeklyRange["date_end"];
                            $_tempPayDateStart = $responseWeeklyRange["paydate_start"];
                            $_tempPayDateEnd = $responseWeeklyRange["paydate_end"];

                            if(strtotime($_tempEndDate) < strtotime($_tempStartDate)){
                                $_tempEndDate = Date("Y-m-d", strtotime("+1 year", strtotime($_tempEndDate)));
                            }
                            if(strtotime($_tempPayDateEnd) < strtotime($_tempPayDateStart)){
                                $_tempPayDateEnd = Date("Y-m-d", strtotime("+1 year", strtotime($_tempPayDateEnd)));
                            }

                            $this->db->select("id, emp_id, payroll_seq");
                            $this->db->from("payroll.payroll_sheet");
                            $this->db->where("emp_id", $tempEmployeeId);
                            $this->db->where("posted", 1);
                            $this->db->where("is_bonus", 0);
                            if($isMonthFilter === true){
                                $_monthStartDate = date("Y-m-d", strtotime("first day of this month", strtotime($tempStartDate)));
                                $_monthEndDate = date("Y-m-d", strtotime("last day of this month", strtotime($_monthStartDate)));
                                $this->db->group_start();
                                    $this->db->group_start();
                                        $this->db->where("DATE(date_start) >=", $_tempStartDate);
                                        $this->db->where("DATE(date_end) <=", $_tempEndDate);
                                    $this->db->group_end();
                                    $this->db->or_group_start();
                                        $this->db->where("DATE(date_start) >=", $_monthStartDate);
                                        $this->db->where("DATE(date_end) <=", $_monthEndDate);
                                    $this->db->group_end();
                                $this->db->group_end();
                            }

                            $queryWeeklyPrev = $this->db->get();
                            if($queryWeeklyPrev->num_rows() > 0){
                                $arrSeqx = [];
                                foreach ($queryWeeklyPrev->result() as $qwx) {
                                    $empIdx = $qwx->emp_id;
                                    $seqx   = (int) $qwx->payroll_seq;
                                    
                                    $arrSeqx[$empIdx]["sequence"][] = $seqx;
                                    $arrSeqx[$empIdx]["ps_id"][] = (int) $qwx->id;
                                }

                                if(is_array($arrSeqx) && !empty($arrSeqx)){
                                    $includeKey = [];
                                    foreach ($arrSeqx as $sequencex) {
                                        $sqx = $sequencex["sequence"];
                                        for($i=1; $i<count($sqx); $i++){
                                            if ($sqx[$i] < $sqx[$i - 1]) {
                                                $includeKey[] = $i;
                                                break;
                                            }
                                        }
                                        if(!empty($includeKey)){
                                            $psIdsx = $sequencex["ps_id"];
                                            foreach ($includeKey as $psKeyx) {
                                                $toIncludePsId[] = $psIdsx[$psKeyx];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($toRemovePsId)){
                foreach ($toRemovePsId as $tempPsId) {
                    if(in_array($tempPsId, $weeklyPsIds)){
                        $key = array_search($tempPsId, $weeklyPsIds);
                        if($key !== false){ unset($weeklyPsIds[$key]); }
                    }
                }
            }

            if(!empty($toIncludePsId)){
                $weeklyPsIds = array_merge($weeklyPsIds, $toIncludePsId);
            }

            return array_values($weeklyPsIds);
        }else{
            return $weeklyPsIds;
        }
    }

    function generateTaxableIncomeReport(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $includedBonus = isset($post["13th_month"]) && intval($post["13th_month"]) === 1 ? true: false;
            $tempGroup = "MONTH";
            $arrGroup = array(1=>"MONTH", 2=>"YEAR");
            $group = (isset($post["group"]) && $post["group"])? intval($post["group"]): 1;

            $arrCompany = array();
            $employeeIds = array();
            $tempStartDate = null;
            $tempEndDate = null;
            $hasDataFilter = false;
            $visibleFields = array();

            $tempGroup = $arrGroup[$group];
            $tempArrFilter = array();
            $tempArrFilter["filter_by"] = $tempGroup;
            $isWeeklyEmployees = array();
            $isMonthlyFilter = false;

            /*** altered code section start ***/
            $isMonthFilter = isset($post["filter_month"], $post["filter_year"]) || strtolower($tempGroup) === "month";
            /*** altered code section end ***/
            
            if(isset($post["company"]) || is_array($post["company"]) || count($post["company"]) > 0){
                $this->db->select("description");
                $this->db->from("gcchris.tblcompanies");
                $this->db->where("id", $post["company"]);
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

            /*** $this->db->select("a.id, d.name as payout_schedule_name");
            $this->db->from("gccmaster.tblemployees a");
            $this->db->join("payroll.payroll_sheet b", "b.emp_id = a.id", "LEFT");
            $this->db->join("gcchris.tblcompanies c", "c.id = b.company_id", "LEFT");
            $this->db->join("payroll.payout_schedule d", "d.id = b.payroll_sched", "INNER");
            $this->db->where("c.id", $post["company"]); ***/

            $this->db->select("a.id, c.name as payout_schedule_name");
            $this->db->from("gccmaster.tblemployees a");
            $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = a.id", "INNER");
            $this->db->join("gcchris.tblcompanies b", "b.id = ps.company_id", "LEFT");
            /*** $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id OR b.description = a.company_id OR b.code = a.company_id", "LEFT"); ***/
            $this->db->join("payroll.payout_schedule c", "c.id = a.payout_sched", "INNER");
            $this->db->where("b.id", $post["company"]);
            if(isset($post["employee"]) && $post["employee"]){
                $this->db->where_in("a.id", $post["employee"]);
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
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $isMonthlyFilter = true;
                $tempArrFilter["month_year"] = strtoupper(date("{$post["filter_year"]} F", strtotime("{$post["filter_year"]}-{$post["filter_month"]}")));
            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("{$post["filter_year"]}-01-01", strtotime("{$post["filter_year"]}"));
                $tempEndDate = date("{$post["filter_year"]}-12-31", strtotime("{$post["filter_year"]}"));
                $tempArrFilter["month_year"] = "YEAR - ".strtoupper(date("{$post["filter_year"]}", strtotime("{$post["filter_year"]}")));
            }
            
            if($tempStartDate && $tempEndDate){
                $weeklyPsIds = array();
                $employeePsIds = array();

                if(is_array($isWeeklyEmployees) && count($isWeeklyEmployees) > 0){
                    $responseWeeklyRange = $this->generateWeeklyMonthRange($tempStartDate, $tempEndDate);
                    if($responseWeeklyRange){

                        /*** original code start
                         * $_tempStartDate = $responseWeeklyRange["date_start"];
                        $_tempEndDate = $responseWeeklyRange["date_end"];
                        $_tempPayDateStart = $responseWeeklyRange["paydate_start"];
                        $_tempPayDateEnd = $responseWeeklyRange["paydate_end"];

                        $this->db->select("id");
                        $this->db->from("payroll.payroll_sheet");
                        $this->db->where("posted", 1);
                        if($includedBonus === false){ $this->db->where("is_bonus", 0); }
                        $this->db->group_start();
                        $this->db->where("DATE(date_start) >=", $_tempStartDate);
                        $this->db->where("DATE(date_end) <=", $_tempEndDate);
                        $this->db->or_where("DATE(pay_date) >=", $_tempPayDateStart);
                        $this->db->where("DATE(pay_date) <=", $_tempPayDateEnd);
                        $this->db->group_end();
                        $this->db->where_in("emp_id", $isWeeklyEmployees);
                        if(isset($post["company"]) && $post["company"]){
                            $this->db->where("company_id", $post["company"]);
                        }

                        $queryWeekly = $this->db->get();
                        if($queryWeekly->num_rows() > 0){
                            foreach ($queryWeekly->result() as $key => $value) {
                                if(!in_array($value->id, $weeklyPsIds)){
                                    $weeklyPsIds[] = $value->id;
                                }
                            }
                        } original code end
                        ***/

                        $_tempStartDate = $responseWeeklyRange["date_start"];
                        $_tempEndDate = $responseWeeklyRange["date_end"];
                        $_tempPayDateStart = $responseWeeklyRange["paydate_start"];
                        $_tempPayDateEnd = $responseWeeklyRange["paydate_end"];

                        if(strtotime($_tempEndDate) < strtotime($_tempStartDate)){
                            $_tempEndDate = Date("Y-m-d", strtotime("+1 year", strtotime($_tempEndDate)));
                        }
                        if(strtotime($_tempPayDateEnd) < strtotime($_tempPayDateStart)){
                            $_tempPayDateEnd = Date("Y-m-d", strtotime("+1 year", strtotime($_tempPayDateEnd)));
                        }

                        $this->db->select("id");
                        $this->db->from("payroll.payroll_sheet");
                        $this->db->where("posted", 1);
                        if($includedBonus === false){ $this->db->where("is_bonus", 0); }
                        if($isMonthlyFilter === true){
                            $_monthStartDate = date("Y-m-d", strtotime("first day of this month", strtotime($tempStartDate)));
                            $_monthEndDate = date("Y-m-d", strtotime("last day of this month", strtotime($_monthStartDate)));
                            $this->db->group_start();
                                $this->db->group_start();
                                $this->db->where("DATE(date_start) >=", $_tempStartDate);
                                $this->db->where("DATE(date_end) <=", $_tempEndDate);
                                $this->db->group_end();
                                $this->db->or_group_start();
                                $this->db->where("DATE(date_start) >=", $_monthStartDate);
                                $this->db->where("DATE(date_end) <=", $_monthEndDate);
                                $this->db->group_end();
                        $this->db->group_end();
                        }else{
                            $tempYear = date("Y", strtotime($tempStartDate));
                            $_yearStartDate = date("Y-m-d", strtotime($tempYear."-01-01"));
                            $_yearEndDate = date("Y-m-d", strtotime($tempYear."-12-31"));
                            $this->db->group_start();
                                $this->db->group_start();
                                $this->db->where("DATE(date_start) >=", $_tempStartDate);
                                $this->db->where("DATE(date_end) <=", $_tempEndDate);
                                $this->db->group_end();
                                $this->db->or_group_start();
                                $this->db->where("DATE(date_start) >=", $_yearStartDate);
                                $this->db->where("DATE(date_end) <=", $_yearEndDate);
                                $this->db->group_end();
                            $this->db->group_end();
                        }
                        
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

                        /*** bonus filter for weekly ***/
                        if($includedBonus == true){
                            $this->db->select("id");
                            $this->db->from("payroll.payroll_sheet");
                            $this->db->where("posted", 1);
                            $this->db->where("is_bonus", 1);
                            $this->db->group_start();
                            $this->db->where("DATE(pay_date) >=", $tempStartDate);
                            $this->db->where("DATE(pay_date) <=", $tempEndDate);
                            $this->db->group_end();
                            $this->db->where_in("emp_id", $isWeeklyEmployees);
                            if(isset($post["company"]) && $post["company"]){
                                $this->db->where("company_id", $post["company"]);
                            }
                            $this->db->order_by("emp_id", "ASC");
                            $queryWeeklyBonus = $this->db->get();
                            if($queryWeeklyBonus->num_rows() > 0){
                                foreach ($queryWeeklyBonus->result() as $key => $value) {
                                    if(!in_array($value->id, $weeklyPsIds)){
                                        $weeklyPsIds[] = $value->id;
                                    }
                                }
                            }
                        }
                        /*** bonus filter for weekly ***/
                    }
                }
                
                if(is_array($employeeIds) && count($employeeIds) > 0){
                    $xDateFrom = date("F d, Y", strtotime($tempStartDate));
                    $xDateTo = date("F d, Y", strtotime($tempEndDate));
                    $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");
                    if(isset($post["filter_month"]) && $post["filter_month"]){
                        $tempArrFilter["month"] = strtoupper(date("Y F", strtotime("{$post["filter_year"]}-{$post["filter_month"]}")));
                    }
                    $this->db->select("id");
                    $this->db->from("payroll.payroll_sheet");
                    $this->db->where("posted", 1);
                    if($includedBonus === false){ $this->db->where("is_bonus", 0); }
                    $this->db->group_start();
                    $this->db->where("DATE(date_start) >=", $tempStartDate);
                    $this->db->where("DATE(date_end) <=", $tempEndDate);
                    $this->db->or_where("DATE(pay_date) >=", $tempStartDate);
                    $this->db->where("DATE(pay_date) <=", $tempEndDate);
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
                        /*** override here ***/
                        /*** override here ***/
                        $employeePsIds = array_map("intval", $employeePsIds);
                        /*** altered section ***/
                        $_tempYear = $post["filter_year"];
                        $_monthName = null;

                        if($isMonthFilter){
                            $_monthIndex = $post["filter_month"];
                            $_monthName = date('F', mktime(0, 0, 0, $_monthIndex, 1, $_tempYear));
                            $_monthName = strtolower($_monthName);
                        }

                        $tempParams = array(
                            "taxable_type"=>$group,
                            "year"=>$_tempYear,
                            "month_name"=>$_monthName,
                            "company_id"=>$post["company"],
                        );

                        $this->setGeneratedTaxableIncome($employeePsIds, $tempParams);
                        /*** altered section ***/
                                                
                        $resultset["response"] = true;
                        $resultset["data"] = $employeePsIds;
                        $resultset["is_weekly_employees"] = $isWeeklyEmployees;
                        $resultset["filters"] = $tempArrFilter;
                        $resultset["params"] = $tempParams;
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

            /*** if(($tempStartDate && $tempEndDate) && (count($employeeIds) > 0 || (isset($post["company"]) && is_array($post["company"]) && count($post["company"]) > 0))){
                $xDateFrom = date("F d, Y", strtotime($tempStartDate));
                $xDateTo = date("F d, Y", strtotime($tempEndDate));
                $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");
                $this->db->select("id");
                $this->db->from("payroll.payroll_sheet");
                $this->db->where("posted", 1);
                if(!isset($post["thirteenth_month"])){
                    $this->db->where("is_bonus", 0);
                }
                $this->db->group_start();
                $this->db->where("DATE(date_start) >=", $tempStartDate);
                $this->db->where("DATE(date_end) <=", $tempEndDate);
                $this->db->or_where("DATE(pay_date) >=", $tempStartDate);
                $this->db->where("DATE(pay_date) <=", $tempEndDate);
                $this->db->group_end();
                if($hasDataFilter && count($employeeIds) > 0){
                    $this->db->where_in("emp_id", $employeeIds);
                }
                if(isset($post["company"]) && $post["company"]){
                    $this->db->where_in("company_id", $post["company"]);
                }

                $query = $this->db->get();
                $tempSql = $this->db->last_query();
                if($query->num_rows() > 0){
                    $ids = array();
                    foreach ($query->result() as $key => $value) {
                        if(!in_array($value->id, $ids)){
                            $ids[] = $value->id;
                        }
                    }
                    $tempCount = count($ids);
                    if(is_array($ids) && $tempCount > 0){
                        $ids = array_map("intval", $ids);
                        $datax = $this->generatePayrollSheetContribution($ids);
                        $resultset["response"] = true;
                        $resultset["data"] = $ids;
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
                $resultset["toastr_msg"] = "No filtered datessss!";
            } ***/
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }


    function generateTaxableIncomeReport_month(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $tempGroup = "PAY DATE";
            $arrGroup = array(1=>"PAY DATE", 2=>"MONTH", 3=>"YEAR");
            $group = (isset($post["group"]) && $post["group"])? intval($post["group"]): 1;

            $employeeIds = array();
            $tempStartDate = null;
            $tempEndDate = null;
            $hasDataFilter = false;
            $visibleFields = array();

            $tempGroup = $arrGroup[$group];
            $tempArrFilter = array();
            $tempArrFilter["filter_by"] = $tempGroup;
            $isWeeklyEmployees = array();
            $isMonthlyFilter = false;

            $tempCompRow = array();
            if(isset($post["company"]) && $post["company"]){
                $this->db->from("gcchris.tblcompanies");
                $this->db->where("id", $post["company"]);
                $qTemp = $this->db->get();
                if($qTemp->num_rows() == 1){
                    $tempCompRow = $qTemp->row_array();
                }
            }

            $isPaydate = strtolower($tempGroup) === "pay date";
            /*** altered code section start ***/
            $isMonthFilter = isset($post["filter_month"], $post["filter_year"]) || strtolower($tempGroup) === "month";
            /*** altered code section end ***/

            /*** $this->db->select("a.id, d.name as payout_schedule_name");
            $this->db->from("gccmaster.tblemployees a");
            $this->db->join("payroll.payroll_sheet b", "b.emp_id = a.id", "LEFT");
            $this->db->join("gcchris.tblcompanies c", "c.id = b.company_id", "LEFT");
            $this->db->join("payroll.payout_schedule d", "d.id = b.payroll_sched", "INNER");
            $this->db->where("c.id", $post["company"]); ***/

            $this->db->select("a.id, c.name as payout_schedule_name");
            $this->db->from("gccmaster.tblemployees a");
            $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = a.id", "INNER");
            $this->db->join("gcchris.tblcompanies b", "b.id = ps.company_id", "LEFT");
            /*** $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id OR b.description = a.company_id OR b.code = a.company_id", "LEFT"); ***/
            $this->db->join("payroll.payout_schedule c", "c.id = a.payout_sched", "INNER");
            $this->db->where("b.id", $post["company"]);
            if(isset($post["employees"]) && $post["employees"]){
                $this->db->where_in("a.id", $post["employees"]);
            }else{
                if(isset($post["serialized_employees"]) && $post["serialized_employees"]){
                    $this->db->where_in("a.id", explode(",",$post["serialized_employees"]));
                }
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
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $isMonthlyFilter = true;
            }elseif(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}"));
            }

            if($tempStartDate && $tempEndDate){
                $xDateFrom = date("F d, Y", strtotime($tempStartDate));
                $xDateTo = date("F d, Y", strtotime($tempEndDate));
                $weeklyPsIds = array();
                $employeePsIds = array();

                if(is_array($isWeeklyEmployees) && count($isWeeklyEmployees) > 0){
                    $responseWeeklyRange = $this->generateWeeklyMonthRange($tempStartDate, $tempEndDate);
                    if($responseWeeklyRange){
                        /*** $tempStartDate = $responseWeeklyRange["date_start"];
                        $startDate = $this->getStartOfWeek($tempStartDate, $post['filter_year']);

                        $tempEndDate = $responseWeeklyRange["date_end"];
                        $tempPayDateStart = $responseWeeklyRange["paydate_start"];
                        $tempPayDateEnd = $responseWeeklyRange["paydate_end"];

                        $this->db->select("id");
                        $this->db->from("payroll.payroll_sheet");
                        $this->db->where("posted", 1);
                        $this->db->where("is_bonus", 0);
                        $this->db->group_start();
                        $this->db->where("DATE(date_start) >=", $startDate['week_start']);
                        $this->db->where("DATE(date_end) <=", $tempEndDate);
                        $this->db->or_where("DATE(pay_date) >=", $tempPayDateStart);
                        $this->db->where("DATE(pay_date) <=", $tempPayDateEnd);
                        $this->db->group_end();
                        $this->db->where_in("emp_id", $isWeeklyEmployees); ***/

                        $_tempStartDate = $responseWeeklyRange["date_start"];
                        $_tempEndDate = $responseWeeklyRange["date_end"];
                        $_tempPayDateStart = $responseWeeklyRange["paydate_start"];
                        $_tempPayDateEnd = $responseWeeklyRange["paydate_end"];

                        if(strtotime($_tempEndDate) < strtotime($_tempStartDate)){
                            $_tempEndDate = Date("Y-m-d", strtotime("+1 year", strtotime($_tempEndDate)));
                        }
                        if(strtotime($_tempPayDateEnd) < strtotime($_tempPayDateStart)){
                            $_tempPayDateEnd = Date("Y-m-d", strtotime("+1 year", strtotime($_tempPayDateEnd)));
                        }

                        $this->db->select("id");
                        $this->db->from("payroll.payroll_sheet");
                        $this->db->where("posted", 1);
                        $this->db->where("is_bonus", 0);
                        if($isMonthlyFilter === true){
                            $_monthStartDate = date("Y-m-d", strtotime("first day of this month", strtotime($tempStartDate)));
                            $_monthEndDate = date("Y-m-d", strtotime("last day of this month", strtotime($_monthStartDate)));
                            $this->db->group_start();

                                $this->db->group_start();
                                    $this->db->where("DATE(date_start) >=", $_tempStartDate);
                                    $this->db->where("DATE(date_end) <=", $_tempEndDate);
                                $this->db->group_end();
                                $this->db->or_group_start();
                                    $this->db->where("DATE(date_start) >=", $_monthStartDate);
                                    $this->db->where("DATE(date_end) <=", $_monthEndDate);
                                $this->db->group_end();
                            $this->db->group_end(); 
                        }else{
                            $this->db->group_start();
                            $this->db->where("DATE(date_start) >=", $_tempStartDate);
                            $this->db->where("DATE(date_end) <=", $_tempEndDate);
                            $this->db->group_end();
                        }
                        if($isPaydate){
                            $this->db->group_start();
                            $this->db->or_where("DATE(pay_date) >=", $_tempPayDateStart);
                            $this->db->where("DATE(pay_date) <=", $_tempPayDateEnd);
                            $this->db->group_end();
                        }
                        if(isset($post["company"]) && $post["company"]){
                            $this->db->where("company_id", $post["company"]);
                        }
                        $this->db->where_in("emp_id", $isWeeklyEmployees);
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
                    $this->db->group_start();
                    $this->db->where("DATE(date_start) >=", $tempStartDate);
                    $this->db->where("DATE(date_end) <=", $tempEndDate);
                    $this->db->or_where("DATE(pay_date) >=", $tempStartDate);
                    $this->db->where("DATE(pay_date) <=", $tempEndDate);
                    $this->db->group_end();
                    $this->db->where_in("emp_id", $employeeIds);
                    if(isset($post["company"]) && $post["company"]){
                        $this->db->where("company_id", $post["company"]);
                    }
                    $this->db->order_by("emp_id", "ASC");
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

                $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");
                $tempArrFilter["month"] = strtoupper(date("Y F", strtotime("{$post["filter_year"]}-{$post["filter_month"]}")));
                $tempArrFilter["company_description"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? strtoupper(trim($tempCompRow['description'])): "";
                $tempArrFilter["company_address"] = isset($tempCompRow['company_address']) && $tempCompRow['company_address']  ? strtoupper(trim($tempCompRow['company_address'])): "";
                $tempArrFilter["has_comp_desc"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? true: false;

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
        
        return $resultset;
    } 

    public function generateContributionDeduction(){
        $resultset = array();

        $employeeIds = array();
        $isWeeklyEmployees = array();
        $tempStartDate = null;
        $tempEndDate = null;
        $tempSequenceMax = array();
        
        $post = $this->input->post();
        if(isset($post) && $post){
            $payrollGroup = isset($post["payroll_group"]) && $post["payroll_group"] ? strtoupper($post["payroll_group"]): null;
            $includedBonus = isset($post["13th_month"]) && intval($post["13th_month"]) === 1 ? true: false;
            $payDate = date("Y-m-d", strtotime($post["pay_date"]));
            $dates = explode("-", $post["date_range"]);

            if(isset($post["date_range"]) && $post["date_range"]){
                $tempStartDate = date("Y-m-d", strtotime($dates[0]));
                $tempEndDate = date("Y-m-d", strtotime($dates[1]));
            }

            $tempCompRow = array();
            if(isset($post["company"]) && $post["company"]){
                $this->db->from("gcchris.tblcompanies");
                $this->db->where("id", $post["company"]);
                $qTemp = $this->db->get();
                if($qTemp->num_rows() == 1){ $tempCompRow = $qTemp->row_array(); }
            }

            $this->db->select("a.id, c.name as payout_schedule_name, ps.payroll_seq");
            $this->db->from("gccmaster.tblemployees a");
            $this->db->join("payroll.payroll_sheet ps", "ps.emp_id = a.id", "INNER");
            $this->db->join("gcchris.tblcompanies b", "b.id = ps.company_id", "LEFT");
            $this->db->join("payroll.payout_schedule c", "c.id = a.payout_sched", "INNER");
            $this->db->where("b.id", $post["company"]);
            if (isset($post["employees"]) && $post["employees"]){
                $this->db->where_in("a.id", $post["employees"]);
            } elseif (isset($post["serialized_employees"]) && $post["serialized_employees"]){
                $this->db->where_in("a.id", explode(",",$post["serialized_employees"]));
            }

            $this->db->group_by("a.id");
            $queryTemp = $this->db->get();
            if($queryTemp->num_rows() > 0){
                foreach ($queryTemp->result() as $value) {
                    /*** altered code section start ***/
                    $tempPayoutSchedule = strtolower($value->payout_schedule_name);
                    if($value->payout_schedule_name !== $tempPayoutSchedule){ $value->payout_schedule_name = $tempPayoutSchedule; }
                    /*** altered code section end ***/

                    if (strtolower($value->payout_schedule_name) !== "weekly"
                        && !in_array($value->id, $employeeIds)){
                        $employeeIds[] = $value->id;
                    }elseif (strtolower($value->payout_schedule_name) === "weekly"
                        && !in_array($value->id, $isWeeklyEmployees)){
                        $isWeeklyEmployees[] = $value->id;
                    }
                    if($value->payroll_seq && !in_array($value->payroll_seq, $tempSequenceMax)){ $tempSequenceMax[] = $value->payroll_seq; }
                }
            }

            if($tempStartDate && $tempEndDate){
                $weeklyPsIds = array();
                $employeePsIds = array();

                if(is_array($isWeeklyEmployees) && !empty($isWeeklyEmployees)){
                    $responseWeeklyRange = $this->generateWeeklyMonthRange($tempStartDate, $tempEndDate);
                    $_tempStartDate = $post["group"] === "2" && $responseWeeklyRange ? $responseWeeklyRange["date_start"]: $tempStartDate;
                    $_tempEndDate = $post["group"] === "2" && $responseWeeklyRange ? $responseWeeklyRange["date_end"]: $tempEndDate;

                    if(strtotime($_tempEndDate) < strtotime($_tempStartDate)){
                        $_tempEndDate = Date("Y-m-d", strtotime("+1 year", strtotime($_tempEndDate)));
                    }

                    $this->db->select("id");
                    $this->db->from("payroll.payroll_sheet");
                    $this->db->where("posted", 1);
                    if($includedBonus === false){ $this->db->where("is_bonus", 0); }
                    $this->db->group_start();
                    $this->db->where("DATE(date_start) >=", $_tempStartDate);
                    $this->db->where("DATE(date_end) <=", $_tempEndDate);
                    $this->db->group_end();
                    $this->db->where_in("emp_id", $isWeeklyEmployees);
                    if(isset($post["company"]) && $post["company"]){
                        $this->db->where("company_id", $post["company"]);
                    }
                    $this->db->order_by("emp_id", "ASC");
                    $queryWeekly = $this->db->get();
                    if($queryWeekly->num_rows() > 0){
                        foreach ($queryWeekly->result() as $value) {
                            if(!in_array($value->id, $weeklyPsIds)){
                                $weeklyPsIds[] = $value->id;
                            }
                        }
                    }
                }

                if(is_array($employeeIds) && !empty($employeeIds)){
                    $this->db->select("id");
                    $this->db->from("payroll.payroll_sheet");
                    $this->db->where("posted", 1);
                    if($includedBonus === false){ $this->db->where("is_bonus", 0); }
                    $this->db->group_start();
                    $this->db->where("DATE(date_start) >=", $tempStartDate);
                    $this->db->where("DATE(date_end) <=", $tempEndDate);
                    $this->db->where("DATE(pay_date)", $payDate);
                    $this->db->group_end();
                    $this->db->where_in("emp_id", $employeeIds);
                    if(isset($post["company"]) && $post["company"]){
                        $this->db->where("company_id", $post["company"]);
                    }
    
                    $query = $this->db->get();
                    if($query->num_rows() > 0){
                        foreach ($query->result() as $value) {
                            if(!in_array($value->id, $employeePsIds)){
                                $employeePsIds[] = $value->id;
                            }
                        }
                    }
                }

                if((is_array($employeePsIds) && !empty($employeePsIds)) || (is_array($weeklyPsIds) && !empty($weeklyPsIds))){
                    $employeePsIds = array_unique(array_merge($employeePsIds, $weeklyPsIds));
                    $tempSequenceMax = max($tempSequenceMax);
                    if(is_array($employeePsIds) && !empty($employeePsIds)){
                        $employeePsIds = array_map("intval", $employeePsIds);

                        $tempData = $this->generatePayrollSheetContribution($employeePsIds);
                        if(isset($tempData["data"]) && is_array($tempData["data"]) && count($tempData["data"]) > 0){
                            $filterStartDate = date("Y/m/d", strtotime($tempStartDate));
                            $filterEndDate = date("Y/m/d", strtotime($tempEndDate));
                            $filterPayDate = date("Y/m/d", strtotime($payDate));
                            $filterDatex = date("Y-m / ", strtotime($payDate));
                            $resultset["response"] = true;

                            $resultset["filter"] = array(
                                "pay_date"=>"{$filterPayDate}",
                                "pay_coverage"=>"{$filterStartDate} - {$filterEndDate}",
                                "pay_sequence"=>$filterDatex.$tempSequenceMax,
                                "company_description"=> $tempCompRow['description'] ? strtoupper($tempCompRow['description'] ): "GC&C, INC",
                                "company_address"=> $tempCompRow['company_address']  ? strtoupper($tempCompRow['company_address'] ): "",
                                "has_comp_desc"=>$tempCompRow['description'] ? true: false,
                                "payroll_group"=>$payrollGroup
                            );
                                
                            $resultset["data"] = $tempData["data"];
                            $resultset["row_columns"] = $tempData["row_columns"];
                            $resultset["column_count"] = $tempData["column_count"];
                            $resultset["grand_total_footer"] = $tempData["grand_total_footer"];
                            $resultset["grand_total"] = $tempData["grand_total"];
                            $resultset["count"] = count($tempData["data"]);
                            $resultset["raw_data"] = $tempData["_temp"];
                        }else{ $resultset["response"] = false; }
                    }else{ $resultset["response"] = false; }
                }else{ $resultset["response"] = false; }
            }else{ $resultset["response"] = false; }
        }else{ $resultset["response"] = false; }
        
        return $resultset;
    }

    public function generatePayrollSheetContribution($psIds = array()){
        $arrData = array();
        if(is_array($psIds) && count($psIds) > 0){
            $tempArrData = array();
            $contributionCode = array();
            $arrPsData = array();
            $arrKxy = array();
            $arrMaxCount = array();
            $defaultFields = array("id", "emp_id", "idno", "gross_pay", "sss", "sss_prov", "ph", "hdmf", "tax", "net_pay");

            if(is_array($psIds) && count($psIds) > 0){
                $this->db->select("UPPER(GROUP_CONCAT(TRIM(ps_loans.code) ORDER BY ps_loans.code ASC)) as loan_code");
                $this->db->from($this->tbl_payroll_sheet." ps");
                $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.payroll_sheet_id = ps.id", "left");
                $this->db->join($this->tbl_hris_loans." loans", "loans.id = ps_loan_payment.loan_id", "left");
                $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id", "left");
                $this->db->where_in("ps.id", $psIds);
                $this->db->group_by("ps.id");
                $qx = $this->db->get();
                foreach ($qx->result() as $vvv) {
                    if($vvv->loan_code){
                        $arrD = explode(",", $vvv->loan_code);
                        $arx = array_count_values($arrD);
                        foreach ($arx as $kxy => $vxy) {
                            $arrKxy[$kxy][] = $vxy;
                        }
                    }
                }
                foreach ($arrKxy as $key => $value) {
                    $tempKey = strtolower($key);
                    $arrMaxCount[$tempKey] = max($value);
                }

                $this->db->select("UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_loans.code)) ORDER BY ps_loans.code ASC)) as loan_code,
                UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_custom_adj.particulars)) ORDER BY ps_custom_adj.particulars ASC)) as adj_code,
                UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_created_adj.particulars)), '||', ps_loans.code ORDER BY ps_created_adj.particulars ASC)) as adj_created_code");
                $this->db->from($this->tbl_payroll_sheet." ps");
                $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.payroll_sheet_id = ps.id", "left");
                $this->db->join($this->tbl_hris_loans." loans", "loans.id = ps_loan_payment.loan_id", "left");
                $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id", "left");
                $this->db->join($this->tbl_ps_custom_adjustments." ps_custom_adj", "ps_custom_adj.payroll_sheet_id = ps.id AND ps_custom_adj.cadj_type = 0", "left");
                $this->db->join($this->tbl_ps_created_adjustments." ps_created_adj", "ps_created_adj.payroll_sheet_id = ps.id AND ps_created_adj.status = 1 AND
                LOWER(ps_created_adj.description) = LOWER(ps_loans.code) OR LOWER(ps_created_adj.description) = LOWER(ps_loans.loan_name)", "left");
                $this->db->where_in("ps.id", $psIds);
                $q = $this->db->get();
                if($q->num_rows() > 0){
                    $codes = $q->row()->loan_code ?explode(",", $q->row()->loan_code) : array();
                    $adj_codes = $q->row()->adj_code ? explode(",", $q->row()->adj_code): array();
                    $adj_created_code = $q->row()->adj_created_code ? explode(",", $q->row()->adj_created_code): array();

                    if(is_array($adj_codes) && !empty($adj_codes)){
                        foreach ($adj_codes as $key => $value) {
                            if($value){
                                $value = str_replace(" ", "_", $value);
                                if($key && $value){
                                    $adj_codes[$key] = $value;
                                }
                            }else{
                                if(isset($adj_codes[$key])){ unset($adj_codes[$key]); }
                            }
                        }
                    }

                    if(is_array($adj_created_code) && !empty($adj_created_code)){
                        foreach ($adj_created_code as $key => $value) {
                            if($value){
                                $tempAdj = explode("||", $value);
                                if(!in_array($tempAdj[1], $codes)){
                                    $codes[] = $tempAdj[1];
                                }
                            }
                        }
                    }

                    
                    $tempArrData = array();
                    $tempArrData = array_merge($codes, $adj_codes);
                    if(is_array($tempArrData) && !empty($tempArrData)){ $contributionCode = $tempArrData; }
                }

                $arrTempData = array();

                $adjustmentsTotal = 0;
                $otAmountTotal = 0;
                $otAllowanceAmountTotal = 0;
                $otNdiffAmountTotal = 0;
                $regNdiffAmountTotal = 0;
                $holidayAmountTotal = 0;
                $regularNightDiffTotal = 0;
                
                $basicRateTotal = 0;
                $allowancesTotal = 0;
                $grossPayTotal = 0;
                $netPayTotal = 0;

                $arrFields = array("sss", "sss_prov", "ph", "hdmf", "tax");
                $sqlSelect = "ps.id, ps.emp_id, emp.idno, ps.basic_rate, ps.total_allowances, ps.ot_amount, ps.ot_ndiff_amount, ps.total_ndiff_amount, ps.total_holiday_amount, ps.ot_allowance_amount, ps.gross_pay, ps.net_pay, ps.sss, ps.sss_prov, ps.ph, ps.hdmf, ps.tax,
                GROUP_CONCAT(DISTINCT(CONCAT(ps_custom_adj.particulars,'||',ps_custom_adj.amount, '||', ps_custom_adj.cadj_type))) custom_adjustments,
                GROUP_CONCAT(DISTINCT(CONCAT(ps_created_adj.particulars,'||', ps_created_adj.amount, '||', ps_created_adj.adj_type, '||', ps_created_adj.description))) created_adjustments,
                GROUP_CONCAT(DISTINCT(CONCAT(ps_loans.code,'||',ps_loan_payment.amount_due, '||', ps_loans.loan_class,'||',ps_loan_payment.id))) sss_hdmf_loan_deduction";
                $this->db->select($sqlSelect);
                $this->db->from($this->tbl_payroll_sheet." ps");
                $this->db->join($this->tbl_employees." emp", "emp.id = ps.emp_id", "inner");
                $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.payroll_sheet_id = ps.id", "left");
                $this->db->join($this->tbl_hris_loans." loans", "loans.id = ps_loan_payment.loan_id", "left");
                $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id", "left");
                $this->db->join($this->tbl_ps_custom_adjustments." ps_custom_adj", "ps_custom_adj.payroll_sheet_id = ps.id", "left");
                $this->db->join($this->tbl_ps_created_adjustments." ps_created_adj", "ps_created_adj.payroll_sheet_id = ps.id AND ps_created_adj.status = 1", "left");
                $this->db->where_in("ps.id", $psIds);
                $this->db->order_by("emp.lastname", "ASC");
                $this->db->group_by("ps.id");
                $qData = $this->db->get();
                if($qData->num_rows() > 0){
                    foreach ($qData->result() as $key => $value) {
                        $tempRow = array();
                        $tempRow["id"] = $value->id;
                        $tempRow["idno"] = $value->idno;
                        $tempRow["gross_pay"] = $value->gross_pay;
                        $tempRow["net_pay"] = $value->net_pay;
                        $tempRow["emp_id"] = $value->emp_id;
                        $employee = $this->core_layout->getEmployeeData($value->emp_id);
                        $employee = (object) $employee;
                        $tempName = isset($employee->display_name_1) && $employee->display_name_1 ? strtoupper($employee->display_name_0): strtoupper("No Assigned Name");

                        foreach ($arrFields as $kk => $vv) { $tempRow[$vv] = $value->$vv; }
                        foreach ($contributionCode as $kx => $vx) {
                            $tempKey = strtolower($vx);
                            if(isset($arrMaxCount[$tempKey]) && $arrMaxCount[$tempKey] > 1){
                                for ($i=1; $i<=intval($arrMaxCount[$tempKey]); $i++) {
                                    $tempRow["{$tempKey}_{$i}"] = 0;
                                }
                            }else{
                                $tempRow[$tempKey] = 0;
                            }
                        }
                        if($value->sss_hdmf_loan_deduction){
                            $tempDeductions = explode(",", $value->sss_hdmf_loan_deduction);
                            if(is_array($tempDeductions) && !empty($tempDeductions)){
                                $arrTempKeyxx = array();
                                foreach ($tempDeductions as $rowx) {
                                    $tempData = explode("||", $rowx);
                                    if(is_array($tempData) && !empty($tempData)){
                                        $tempKey00 = trim(strtolower($tempData[0]));
                                        $tempValue00 = trim($tempData[1]);
                                        if(isset($arrMaxCount[$tempKey00]) && $arrMaxCount[$tempKey00] > 1){
                                            $arrTempKeyxx[$tempKey00][] = $tempValue00;
                                        }else{
                                            $arrTempKeyxx[$tempKey00] = $tempValue00;
                                        }
                                    }
                                }
                                foreach ($arrTempKeyxx as $kzz => $vzz) {
                                    if(is_array($vzz) && !empty($vzz)){
                                        if(isset($arrMaxCount[$kzz]) && $arrMaxCount[$kzz] > 0){
                                            for ($i=0; $i < intval($arrMaxCount[$kzz]) ; $i++) {
                                                $tempIndex = $i + 1;
                                                if(isset($vzz[$i]) && $vzz[$i]){
                                                    $tempRow["{$kzz}_{$tempIndex}"] = $vzz[$i];
                                                }
                                            }
                                        }
                                    }else{
                                        $tempRow[$kzz] = $vzz;
                                    }
                                }
                            }
                        }
                        if($value->custom_adjustments){
                            $tempCustomAdjustment = explode(",", $value->custom_adjustments);
                            if(is_array($tempCustomAdjustment) && count($tempCustomAdjustment) > 0){
                                foreach ($tempCustomAdjustment as $rowx) {
                                    $_tempData = explode("||", $rowx);
                                    if(is_array($_tempData) && !empty($_tempData) && count($_tempData) === 3){
                                        $entryType = $_tempData[2];
                                        if(intval($entryType) === 1){
                                            $adjustmentsTotal = floatval($adjustmentsTotal) + floatval($_tempData[1]);
                                        }else{
                                            $adjustmentsTotal = floatval($adjustmentsTotal) - floatval($_tempData[1]);
                                        }
                                    }
                                }
                            }
                        }

                        if($value->created_adjustments){
                            $tempCustomAdjustment = explode(",", $value->created_adjustments);
                            if(is_array($tempCustomAdjustment) && count($tempCustomAdjustment) > 0){
                                foreach ($tempCustomAdjustment as $rowx) {
                                    $_tempData = explode("||", $rowx);
                                    if(is_array($_tempData) && !empty($_tempData) && count($_tempData) === 4){
                                        $tempKey00 = trim(strtolower($_tempData[0]));
                                        $tempKey00 = str_replace(" ", "_", $tempKey00);
                                        $tempKey01 = trim(strtolower($_tempData[3]));
                                        $tempOperand = $_tempData[2];
                                        /*** alter phic to ph for philhealth code ***/
                                        if($tempKey00 == "phic"){ $tempKey00 = "ph"; }
                                        /*** alter phic to ph for philhealth code ***/

                                        $this->db->select("code");
                                        $this->db->where("loan_name", $tempKey01);
                                        $this->db->or_where("code", $tempKey01);
                                        $qTemp = $this->db->get($this->tbl_ps_loans);
                                        if($qTemp->num_rows() == 1){
                                            $tempKey00 = trim(strtolower($qTemp->row()->code));
                                        }

                                        $tempValue00 = trim($_tempData[1]);
                                        if(isset($tempRow[$tempKey00]) && $tempRow[$tempKey00]){
                                            $currentValue = $tempRow[$tempKey00];
                                            if($tempOperand && intval($tempOperand) == 1){
                                                $currentValue = $currentValue + $tempValue00;
                                            }else{
                                                $currentValue = $currentValue - $tempValue00;
                                            }
                                            $tempRow[$tempKey00] = $currentValue;
                                        }else{
                                            $pos = strpos($tempKey00, "adj");
                                            if($pos && intval($pos) == 0){
                                                $tempKey00 = "adj_{$tempKey00}";
                                            }
                                            $tempRow[$tempKey00] = $tempValue00;
                                        }
                                    }
                                }
                            }
                        }

                        $allowancesTotal = floatval($allowancesTotal) + floatval($value->total_allowances);
                        $otAmountTotal = floatval($otAmountTotal) + floatval($value->ot_amount);
                        $otNdiffAmountTotal = floatval($otNdiffAmountTotal) + floatval($value->ot_ndiff_amount);
                        $regNdiffAmountTotal = floatval($regNdiffAmountTotal) + floatval($value->total_ndiff_amount);
                        $holidayAmountTotal = floatval($holidayAmountTotal) + floatval($value->total_holiday_amount);
                        $regularNightDiffTotal = floatval($regularNightDiffTotal) + floatval($value->total_ndiff_amount);

                        $otAllowanceAmountTotal = floatval($otAllowanceAmountTotal) + floatval($value->ot_allowance_amount);
                        $basicRateTotal = floatval($basicRateTotal) + floatval($value->basic_rate);
                        $grossPayTotal = floatval($grossPayTotal) + floatval($value->gross_pay);
                        $netPayTotal = floatval($netPayTotal) + floatval($value->net_pay);

                        $arrPsData[$key] = $tempRow;
                        $arrTempData[$key] = $value;
                    }
                }
            }

            $grandTotal = array(
                "basic_rate"=>round($basicRateTotal, 2),
                "allowances"=>round($allowancesTotal, 2),
                "ot_amount"=>round($otAmountTotal, 2),
                "ot_ndiff_amount"=>round($otNdiffAmountTotal, 2),
                "regular_ndiff_amount"=>round($regNdiffAmountTotal, 2),
                "holiday_amount"=>round($holidayAmountTotal, 2),
                "total_ndiff_amount"=>round($regularNightDiffTotal, 2),
                "ot_allowance_amount"=>round($otAllowanceAmountTotal, 2),
                "adjustments"=>round($adjustmentsTotal, 2),
                "gross_pay"=>round($grossPayTotal, 2),
                "net_pay"=>round($netPayTotal, 2),
            );

            $tempColumns = array();
            $tempHeaderColumns = array();
            $grandTotalFooter = array();
            $tempArrData = array();
            if(is_array($arrPsData) && !empty($arrPsData)){
                foreach ($arrPsData as $key => $value) {;
                    $tempKeys = array_keys($value);
                    $employee = (object) $this->core_layout->getEmployeeData($value["emp_id"]);
                    $tempName = isset($employee->display_name_1) && $employee->display_name_1 ? strtoupper($employee->display_name_0): strtoupper("No Assigned Name");
                    $tempColumns = array();
                    foreach ($tempKeys as $vx) {
                        if(!in_array($vx, $defaultFields)){
                            $tempColumns[$vx] = $value[$vx];
                        }
                        if(!in_array($vx, $tempHeaderColumns) && !in_array($vx, $defaultFields)){
                            $tempHeaderColumns[] = $vx;
                        }
                    }
                    $value["row_columns"] = $tempColumns;
                    $value["column_count"] = count($tempColumns);
                    $value["employee_name"] = $tempName;
                    $tempArrData[$key] = $value;
                }

                foreach ($arrPsData as $key => $value) {
                    $tempKeys = array_keys($value);
                    foreach ($tempKeys as $vvx) {
                        $insertFlag = false;
                        if (in_array($vvx, $arrFields)){ $insertFlag = true; }
                        elseif (in_array($vvx, $tempHeaderColumns)){ $insertFlag = true; }
                        if ($insertFlag){
                            $tempValueData = $value[$vvx];
                            if (isset($grandTotalFooter[$vvx]) && $grandTotalFooter[$vvx]){
                                $nTotalValue = floatval($grandTotalFooter[$vvx]) + $tempValueData;
                                $grandTotalFooter[$vvx] = round($nTotalValue, 2);
                            }
                            else { $grandTotalFooter[$vvx] = round(floatval($tempValueData), 2); }
                        }
                    }

                    
                }
            }

            $arrData["data"] = $tempArrData;
            $arrData["row_columns"] = $tempHeaderColumns;
            $arrData["column_count"] = count($tempHeaderColumns);
            $arrData["grand_total_footer"] = $grandTotalFooter;
            $arrData["grand_total"] = $grandTotal;
            $arrData["_temp"] = $arrTempData;
        }

        return $arrData;
    }

    public function generatePayrollSheetContributionMultiple($psIds = array()){
        $arrData = array();
        if(is_array($psIds) && count($psIds) > 0){
            $contributionCode = array();
            $arrPsData = array();
            $arrKxy = array();
            $arrMaxCount = array();
            $defaultFields = array("id", "emp_id", "idno", "regular_pay", "gross_pay", "gross_pay_taxable", "sss", "sss_prov", "ph", "hdmf", "tax", "net_pay");

            $arrResultData = array();

            if(is_array($psIds) && count($psIds) > 0){
                $deductions = array();
                $ctrChunk = count($psIds) > 10000 ? 2500: 500;
                $tempChunk = array_chunk($psIds, $ctrChunk, true);
                foreach ($tempChunk as $key => $tempIds) {
                    $tempDeductions = $this->generateContributionDeductionAdjustments($tempIds);
                    foreach ($tempDeductions as $key => $value) { $deductions[$key] = $value; }

                    $this->db->select("UPPER(GROUP_CONCAT(TRIM(ps_loans.code) ORDER BY ps_loans.code ASC)) as loan_code");
                    $this->db->from($this->tbl_payroll_sheet." ps");
                    $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.payroll_sheet_id = ps.id", "left");
                    $this->db->join($this->tbl_hris_loans." loans", "loans.id = ps_loan_payment.loan_id", "left");
                    $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id", "left");
                    $this->db->where_in("ps.id", $tempIds);
                    $this->db->group_by("ps.id");
                    $qx = $this->db->get();
                    foreach ($qx->result() as $kkk => $vvv) {
                        if($vvv->loan_code){
                            $arrD = explode(",", $vvv->loan_code);
                            $arx = array_count_values($arrD);
                            foreach ($arx as $kxy => $vxy) {
                                $arrKxy[$kxy][] = $vxy;
                            }
                        }
                    }

                    $this->db->select("UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_loans.code)) ORDER BY ps_loans.code ASC)) as loan_code,
                    UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_custom_adj.particulars)) ORDER BY ps_custom_adj.particulars ASC)) as adj_code,
                    UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_created_adj.particulars)), '||', ps_loans.code ORDER BY ps_created_adj.particulars ASC)) as adj_created_code");
                    $this->db->from($this->tbl_payroll_sheet." ps");
                    $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.payroll_sheet_id = ps.id", "left");
                    $this->db->join($this->tbl_hris_loans." loans", "loans.id = ps_loan_payment.loan_id", "left");
                    $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id", "left");
                    $this->db->join($this->tbl_ps_custom_adjustments." ps_custom_adj", "ps_custom_adj.payroll_sheet_id = ps.id AND ps_custom_adj.cadj_type = 0", "left");
                    $this->db->join($this->tbl_ps_created_adjustments." ps_created_adj", "ps_created_adj.payroll_sheet_id = ps.id AND ps_created_adj.status = 1 AND
                    LOWER(ps_created_adj.description) = LOWER(ps_loans.code) OR LOWER(ps_created_adj.description) = LOWER(ps_loans.loan_name)", "left");
                    $this->db->where_in("ps.id", $tempIds);
                    $q = $this->db->get();
                    if($q->num_rows() > 0){
                        $codes = explode(",", $q->row()->loan_code);
                        $adj_codes = explode(",", $q->row()->adj_code);
                        $adj_created_code = explode(",", $q->row()->adj_created_code);

                        if(is_array($adj_codes) && count($adj_codes) > 0){
                            foreach ($adj_codes as $key => $value) {
                                if($value){
                                    $value = str_replace(" ", "_", $value);
                                    $adj_codes[$key] = $value;
                                }else{
                                    if(isset($adj_codes[$key])){ unset($adj_codes[$key]); }
                                }
                            }
                        }

                        if(is_array($adj_created_code) && count($adj_created_code) > 0){
                            foreach ($adj_created_code as $key => $value) {
                                if($value){
                                    $tempAdj = explode("||", $value);
                                    $tempCode = str_replace(" ", "_", $tempAdj[1]);
                                    if(!in_array($tempCode, $codes)){
                                        $codes[] = $tempCode;
                                    }
                                }
                            }
                        }

                        $tempArrData = array();
                        $tempArrData = array_merge($codes, $adj_codes);
                        if(is_array($tempArrData) && count($tempArrData) > 0){
                            foreach ($tempArrData as $value) {
                                $contributionCode[] = $value;
                            }
                        }
                    }

                    $sqlSelect = "ps.id, ps.emp_id, emp.idno, emp.biometricno, comp.code as company, emp.tax_status, emp.tin_no, emp.phealth_no, emp.pagibig_no, emp.sss_no, 
                    ROUND(ps.rate, 2) as rate, ROUND(SUM(IF(ps.is_bonus = 0,ps.basic_rate, 0)), 2) as basic_rate, ps.payroll_type, 
                    ROUND(SUM(IF(ps.is_bonus = 0, ps.gross_pay, 0)), 2) as gross_pay, ROUND(SUM(IF(ps.is_bonus = 1, ps.net_pay, 0)), 2) as netpay_bonus,
                    ROUND(SUM(IF(ps.is_bonus = 0, ps.net_pay, 0)), 2) as net_pay, ROUND(SUM(ps.sss),2) as sss, ROUND(SUM(ps.sss_prov), 2) as sss_prov, ROUND(SUM(ps.ph), 2) as ph,
                    ROUND(SUM(ps.hdmf), 2) as hdmf, ROUND(SUM(ps.tax), 2) as tax, UPPER(emp.employee_status) as employee_status";
                    $this->db->select($sqlSelect);
                    $this->db->from($this->tbl_payroll_sheet." ps");
                    $this->db->join($this->tbl_employees." emp", "emp.id = ps.emp_id");
                    $this->db->join($this->tbl_tblcompanies." comp", "comp.id = ps.company_id");
                    $this->db->where_in("ps.id", $tempIds);
                    $this->db->order_by("emp.lastname", "ASC");
                    $this->db->group_by("ps.emp_id");
                    $qData = $this->db->get();
                    if($qData->num_rows() > 0){
                        foreach ($qData->result() as $key => $value) { $arrResultData[$value->emp_id] = $value; }
                    }
                }
                
                foreach ($arrKxy as $key => $value) {
                    $tempKey = strtolower($key);
                    $arrMaxCount[$tempKey] = max($value);
                }

                $arrFields = array("sss", "sss_prov", "ph", "hdmf", "tax");
                /*** $sqlSelect = "ps.id, ps.emp_id, emp.idno, ps.rate, SUM(ps.gross_pay) as gross_pay,
                SUM(ps.net_pay) as net_pay, SUM(ps.sss) as sss, SUM(ps.sss_prov) as sss_prov, SUM(ps.ph) as ph,
                SUM(ps.hdmf) as hdmf, SUM(ps.tax) as tax,
                GROUP_CONCAT(DISTINCT(CONCAT(ps_custom_adj.particulars,'||',ps_custom_adj.amount, '||', ps_custom_adj.cadj_type))) custom_adjustments,
                GROUP_CONCAT(DISTINCT(CONCAT(ps_created_adj.particulars,'||', ps_created_adj.amount, '||', ps_created_adj.adj_type, '||', ps_created_adj.description))) created_adjustments,
                GROUP_CONCAT(DISTINCT(CONCAT(ps_loans.code,'||',ps_loan_payment.amount_due, '||', ps_loans.loan_class))) sss_hdmf_loan_deduction"; ***/

                if(is_array($arrResultData) && count($arrResultData) > 0){
                    foreach ((object) $arrResultData as $key => $value) {
                        $tempRow = array();
                        $tempRow["id"] = $value->id;
                        $tempRow["idno"] = $value->idno;
                        $tempRow["biometricno"] = $value->biometricno;
                        $tempRow["company"] = $value->company;
                        $tempRow["tax_status"] = $value->tax_status;
                        $tempRow["tin_no"] = $value->tin_no;
                        $tempRow["phealth_no"] = $value->phealth_no;
                        $tempRow["pagibig_no"] = $value->pagibig_no;
                        $tempRow["sss_no"] = $value->sss_no;
                        $tempRow["employee_status"] = $value->employee_status;
                        $tempRow["regular_pay"] = $value->rate;
                        $tempRow["gross_pay"] = $value->gross_pay;
                        $tempRow["gross_pay_taxable"] = $value->basic_rate;
                        $tempRow["net_pay"] = $value->net_pay;
                        $tempRow["netpay_bonus"] = $value->netpay_bonus;
                        $tempRow["emp_id"] = $value->emp_id;
                        if($value->payroll_type !== "monthly"){ $tempRow["regular_pay"] = $value->basic_rate; }
                        $employee = $this->core_layout->getEmployeeData($value->emp_id);
                        $employee = (object) $employee;
                        $tempName = isset($employee->display_name_1) && $employee->display_name_1 ? strtoupper($employee->display_name_0): strtoupper("No Assigned Name");

                        foreach ($arrFields as $kk => $vv) { $tempRow[$vv] = $value->$vv; }
                        foreach ($contributionCode as $kx => $vx) {
                            $tempKey = strtolower($vx);
                            $tempRow[$tempKey] = 0;
                        }

                        if(is_array($deductions) && count($deductions) > 0){
                            $rowDeductions = $deductions[$value->emp_id];
                            $_tempDeductions = (object) $rowDeductions;
                            if(isset($_tempDeductions->sss_hdmf_loan_deduction) && $_tempDeductions->sss_hdmf_loan_deduction){
                                $tempDeductions = explode(",", $_tempDeductions->sss_hdmf_loan_deduction);
                                if(is_array($tempDeductions) && count($tempDeductions) > 0){
                                    $arrTempKeyxx = array();
                                    foreach ($tempDeductions as $xx => $rowx) {
                                        $tempData = explode("||", $rowx);
                                        if(is_array($tempData) && (count($tempData) > 0 && count($tempData) >= 4)){
                                            $tempKey00 = trim(strtolower($tempData[0]));
                                            $tempValue00 = trim($tempData[1]);
                                            if(isset($tempRow[$tempKey00]) && $tempRow[$tempKey00]){
                                                $currentValue = $tempRow[$tempKey00];
                                                $currentValue += $tempValue00;
                                                $tempRow[$tempKey00] = round($currentValue, 2);
                                            }else{
                                                $tempRow[$tempKey00] = round($tempValue00, 2);
                                            }
                                            /*** if(isset($arrMaxCount[$tempKey00]) && $arrMaxCount[$tempKey00] > 1){
                                                $arrTempKeyxx[$tempKey00][] = $tempValue00;
                                            }else{
                                                $arrTempKeyxx[$tempKey00] = $tempValue00;
                                            } ***/
                                        }
                                    }
                                    /**** foreach ($arrTempKeyxx as $kzz => $vzz) {
                                        if(is_array($vzz) && count($vzz) > 0){
                                            if(isset($arrMaxCount[$kzz]) && $arrMaxCount[$kzz] > 0){
                                                for ($i=0; $i < intval($arrMaxCount[$kzz]) ; $i++) {
                                                    $tempIndex = $i + 1;
                                                    if(isset($vzz[$i]) && $vzz[$i]){
                                                        $tempRow["{$kzz}_{$tempIndex}"] = $vzz[$i];
                                                    }
                                                }
                                            }
                                        }else{
                                            $tempRow[$kzz] = $vzz;
                                        }
                                    } ***/
                                }
                            }
                            if(isset($_tempDeductions->custom_adjustments) && $_tempDeductions->custom_adjustments){
                                $tempCustomAdjustment = explode(",", $_tempDeductions->custom_adjustments);
                                if(is_array($tempCustomAdjustment) && count($tempCustomAdjustment) > 0){
                                    foreach ($tempCustomAdjustment as $xx => $rowx) {
                                        $_tempData = explode("||", $rowx);
                                        if(is_array($_tempData) && count($_tempData) > 0){
                                            $tempKey00 = trim(strtolower($_tempData[0]));
                                            $tempKey00 = str_replace(" ", "_", $tempKey00);
                                            $tempValue00 = trim($_tempData[1]);
                                            if(isset($_tempData[2]) && intval($_tempData[2]) == 0){
                                                if(isset($tempRow[$tempKey00]) && $tempRow[$tempKey00]){
                                                    $currentValue = $tempRow[$tempKey00];
                                                    $currentValue += $tempValue00;
                                                    $tempRow[$tempKey00] = round($currentValue, 2);
                                                }else{
                                                    $tempRow[$tempKey00] = round($tempValue00, 2);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
    
                            if(isset($_tempDeductions->created_adjustments) && $_tempDeductions->created_adjustments){
                                $tempCustomAdjustment = explode(",", $_tempDeductions->created_adjustments);
                                if(is_array($tempCustomAdjustment) && count($tempCustomAdjustment) > 0){
                                    foreach ($tempCustomAdjustment as $xx => $rowx) {
                                        $_tempData = explode("||", $rowx);
                                        if(is_array($_tempData) && count($_tempData) > 0 && count($_tempData) >= 4){
                                            $tempKey00 = trim(strtolower($_tempData[0]));
                                            $tempKey00 = str_replace(" ", "_", $tempKey00);
                                            $tempKey01 = trim(strtolower($_tempData[3]));
                                            $tempOperand = $_tempData[2];
                                             /*** alter phic to ph for philhealth code ***/
                                            if($tempKey00 == "phic"){ $tempKey00 = "ph"; }
                                            /*** alter phic to ph for philhealth code ***/

                                            $this->db->select("code");
                                            $this->db->where("loan_name", $tempKey01);
                                            $this->db->or_where("code", $tempKey01);
                                            $qTemp = $this->db->get($this->tbl_ps_loans);
                                            if($qTemp->num_rows() == 1){
                                                $tempKey00 = trim(strtolower($qTemp->row()->code));
                                            }
                                            
                                            $tempValue00 = trim($_tempData[1]);
                                            if(isset($tempRow[$tempKey00]) && $tempRow[$tempKey00]){
                                                $currentValue = $tempRow[$tempKey00];
                                                if($tempOperand && intval($tempOperand) == 1){
                                                    $currentValue = $currentValue + $tempValue00;
                                                }else{
                                                    $currentValue = $currentValue - $tempValue00;
                                                }
                                                $tempRow[$tempKey00] = round($currentValue, 2);
                                            }else{
                                                $pos = strpos($tempKey00, "adj");
                                                if($pos && intval($pos) == 0){
                                                    $tempKey00 = "adj_{$tempKey00}";
                                                }
                                                $tempRow[$tempKey00] = round($tempValue00, 2);
                                            }
                                        }
                                    }
                                }
                            }

                        }

                        $arrPsData[$key] = $tempRow;
                    }
                }
            }

            $tempColumns = array();

            if(is_array($arrPsData) && count($arrPsData) > 0){
                foreach ($arrPsData as $key => $value) {
                    $tempKeys = array_keys($value);
                    $employee = (object) $this->core_layout->getEmployeeData($value["emp_id"]);
                    $tempName = isset($employee->display_name_1) && $employee->display_name_1 ? strtoupper($employee->display_name_0): strtoupper("No Assigned Name");
                    $tempColumns = array();
                    foreach ($tempKeys as $kkxx => $vx) {
                        if(!in_array($vx, $defaultFields) && $vx){ $tempColumns[$vx] = $value[$vx]; }
                    }
                    $value["row_columns"] = $tempColumns;
                    $value["column_count"] = count($tempColumns);
                    $value["employee_name"] = $tempName;
                    $arrData[$key] = $value;
                }
            }
        }
        return $arrData;
    }
    
    public function generatePayrollSheetContributionDeduction($psIds = array()){
        $arrData = array();
        if(is_array($psIds) && count($psIds) > 0){
            $contributionCode = array();
            $arrPsData = array();
            $arrKxy = array();
            $arrMaxCount = array();
            $defaultFields = array("id", "emp_id", "idno");

            $arrResultData = array();

            if(is_array($psIds) && count($psIds) > 0){
                $deductions = array();
                $tempChunk = array_chunk($psIds, 500, true);
                foreach ($tempChunk as $key => $tempIds) {
                    $tempDeductions = $this->generateContributionDeductionAdjustments($tempIds);
                    foreach ($tempDeductions as $key => $value) { $deductions[$key] = $value; }

                    $this->db->select("UPPER(GROUP_CONCAT(TRIM(ps_loans.code) ORDER BY ps_loans.code ASC)) as loan_code");
                    $this->db->from($this->tbl_payroll_sheet." ps");
                    $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.payroll_sheet_id = ps.id", "left");
                    $this->db->join($this->tbl_hris_loans." loans", "loans.id = ps_loan_payment.loan_id", "left");
                    $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id", "left");
                    $this->db->group_start();
                    $this->db->where("ps_loans.code !=", "");
                    $this->db->or_where("ps_loans.code !=", null);
                    $this->db->group_end();
                    $this->db->where_in("ps.id", $tempIds);
                    $this->db->group_by("ps.id");
                    $qx = $this->db->get();
                    foreach ($qx->result() as $vvv) {
                        if($vvv->loan_code){
                            $arrD = explode(",", $vvv->loan_code);
                            $arx = array_count_values($arrD);
                            foreach ($arx as $kxy => $vxy) {
                                $arrKxy[$kxy][] = $vxy;
                            }
                        }
                    }

                    $this->db->select("UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_loans.code)) ORDER BY ps_loans.code ASC)) as loan_code,
                    UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_custom_adj.particulars)) ORDER BY ps_custom_adj.particulars ASC)) as adj_code,
                    UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_created_adj.particulars)), '||', ps_loans.code ORDER BY ps_created_adj.particulars ASC)) as adj_created_code");
                    $this->db->from($this->tbl_payroll_sheet." ps");
                    $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.payroll_sheet_id = ps.id", "left");
                    $this->db->join($this->tbl_hris_loans." loans", "loans.id = ps_loan_payment.loan_id", "left");
                    $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id", "left");
                    $this->db->join($this->tbl_ps_custom_adjustments." ps_custom_adj", "ps_custom_adj.payroll_sheet_id = ps.id AND ps_custom_adj.cadj_type = 0", "left");
                    $this->db->join($this->tbl_ps_created_adjustments." ps_created_adj", "ps_created_adj.payroll_sheet_id = ps.id AND ps_created_adj.status = 1 AND
                    LOWER(ps_created_adj.description) = LOWER(ps_loans.code) OR LOWER(ps_created_adj.description) = LOWER(ps_loans.loan_name)", "left");
                    $this->db->where_in("ps.id", $tempIds);
                    $q = $this->db->get();
                    if($q->num_rows() > 0){
                        $codes = explode(",", $q->row()->loan_code);
                        $adj_codes = explode(",", $q->row()->adj_code);
                        $adj_created_code = explode(",", $q->row()->adj_created_code);

                        if(is_array($adj_codes) && count($adj_codes) > 0){
                            foreach ($adj_codes as $key => $value) {
                                if($value){
                                    $value = str_replace(" ", "_", $value);
                                    $adj_codes[$key] = $value;
                                }else{
                                    if(isset($adj_codes[$key])){ unset($adj_codes[$key]); }
                                }
                            }
                        }

                        if(is_array($adj_created_code) && count($adj_created_code) > 0){
                            foreach ($adj_created_code as $key => $value) {
                                if($value){
                                    $tempAdj = explode("||", $value);
                                    $tempCode = str_replace(" ", "_", $tempAdj[1]);
                                    if(!in_array($tempCode, $codes)){
                                        $codes[] = $tempCode;
                                    }
                                }
                            }
                        }

                        $tempArrData = array();
                        $tempArrData = array_merge($codes, $adj_codes);
                        if(is_array($tempArrData) && count($tempArrData) > 0){
                            foreach ($tempArrData as $value) {
                                $contributionCode[] = $value;
                            }
                        }
                    }

                    $sqlSelect = "ps.id, ps.emp_id, emp.idno, emp.biometricno, ps.payroll_type, ps.basic_rate, ps.rate";
                    $this->db->select($sqlSelect);
                    $this->db->from($this->tbl_payroll_sheet." ps");
                    $this->db->join($this->tbl_employees." emp", "emp.id = ps.emp_id");
                    $this->db->where_in("ps.id", $tempIds);
                    $this->db->order_by("emp.lastname", "ASC");
                    $this->db->group_by("ps.emp_id");
                    $qData = $this->db->get();
                    if($qData->num_rows() > 0){
                        foreach ($qData->result() as $key => $value) { $arrResultData[$value->emp_id] = $value; }
                    }
                }
                
                foreach ($arrKxy as $key => $value) {
                    $tempKey = strtolower($key);
                    $arrMaxCount[$tempKey] = max($value);
                }

                if(is_array($arrResultData) && count($arrResultData) > 0){
                    foreach ((object) $arrResultData as $key => $value) {
                        $totalDeductions = 0;
                        $tempRow = array();
                        $tempRow["id"] = $value->id;
                        $tempRow["idno"] = $value->idno;
                        $tempRow["biometricno"] = $value->biometricno;
                        $tempRow["emp_id"] = $value->emp_id;
                        if($value->payroll_type !== "monthly"){ $tempRow["regular_pay"] = $value->basic_rate; }
                        $employee = $this->core_layout->getEmployeeData($value->emp_id);
                        $employee = (object) $employee;
                        $tempName = isset($employee->display_name_1) && $employee->display_name_1 ? strtoupper($employee->display_name_0): strtoupper("No Assigned Name");

                        $_contCodes = array();
                        $this->db->select("code");
                        $qCodex = $this->db->get_where($this->tbl_ps_loans, array("loan_type" => 1));
                        if($qCodex->num_rows() > 0){
                            foreach ($qCodex->result() as $tempCodex) {
                                $_contCodes[] = strtolower(trim($tempCodex->code));
                            }
                        }

                        foreach ($contributionCode as $kx => $vx) {
                            $tempKey = strtolower($vx);
                            $tempRow[$tempKey] = 0;
                        }

                        if(is_array($deductions) && count($deductions) > 0){
                            $rowDeductions = $deductions[$value->emp_id];
                            $_tempDeductions = (object) $rowDeductions;
                            if(isset($_tempDeductions->sss_hdmf_loan_deduction) && $_tempDeductions->sss_hdmf_loan_deduction){
                                $tempDeductions = explode(",", $_tempDeductions->sss_hdmf_loan_deduction);
                                if(is_array($tempDeductions) && count($tempDeductions) > 0){
                                    $arrTempKeyxx = array();
                                    foreach ($tempDeductions as $xx => $rowx) {
                                        $tempData = explode("||", $rowx);
                                        if(is_array($tempData) && (count($tempData) > 0 && count($tempData) >= 4)){
                                            $tempKey00 = trim(strtolower($tempData[0]));
                                            $tempValue00 = trim($tempData[1]);
                                            if(isset($tempRow[$tempKey00]) && $tempRow[$tempKey00]){
                                                $currentValue = $tempRow[$tempKey00];
                                                $currentValue += $tempValue00;
                                                $tempRow[$tempKey00] = round($currentValue, 2);
                                            }else{
                                                $tempRow[$tempKey00] = round($tempValue00, 2);
                                            }
                                        }
                                    }
                                }
                            }
                            if(isset($_tempDeductions->custom_adjustments) && $_tempDeductions->custom_adjustments){
                                $tempCustomAdjustment = explode(",", $_tempDeductions->custom_adjustments);
                                if(is_array($tempCustomAdjustment) && count($tempCustomAdjustment) > 0){
                                    foreach ($tempCustomAdjustment as $xx => $rowx) {
                                        $_tempData = explode("||", $rowx);
                                        if(is_array($_tempData) && count($_tempData) > 0){
                                            $tempKey00 = trim(strtolower($_tempData[0]));
                                            $tempKey00 = str_replace(" ", "_", $tempKey00);
                                            $tempValue00 = trim($_tempData[1]);
                                            if(isset($_tempData[2]) && intval($_tempData[2]) == 0){
                                                if(isset($tempRow[$tempKey00]) && $tempRow[$tempKey00]){
                                                    $currentValue = $tempRow[$tempKey00];
                                                    $currentValue += $tempValue00;
                                                    $tempRow[$tempKey00] = round($currentValue, 2);
                                                }else{
                                                    $tempRow[$tempKey00] = round($tempValue00, 2);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
    
                            if(isset($_tempDeductions->created_adjustments) && $_tempDeductions->created_adjustments){
                                $tempCustomAdjustment = explode(",", $_tempDeductions->created_adjustments);
                                if(is_array($tempCustomAdjustment) && count($tempCustomAdjustment) > 0){
                                    foreach ($tempCustomAdjustment as $xx => $rowx) {
                                        $_tempData = explode("||", $rowx);
                                        if(is_array($_tempData) && count($_tempData) > 0 && count($_tempData) >= 4){
                                            $tempKey00 = trim(strtolower($_tempData[0]));
                                            $tempKey00 = str_replace(" ", "_", $tempKey00);
                                            $tempKey01 = trim(strtolower($_tempData[3]));
                                            $tempOperand = $_tempData[2];
                                             /*** alter phic to ph for philhealth code ***/
                                            if($tempKey00 == "phic"){ $tempKey00 = "ph"; }
                                            /*** alter phic to ph for philhealth code ***/

                                            $this->db->select("code");
                                            $this->db->group_start();
                                            $this->db->where("loan_name", $tempKey01);
                                            $this->db->or_where("code", $tempKey01);
                                            $this->db->group_end();
                                            $qTemp = $this->db->get($this->tbl_ps_loans);
                                            if($qTemp->num_rows() == 1){
                                                $tempKey00 = trim(strtolower($qTemp->row()->code));
                                            }
                                            
                                            $tempValue00 = trim($_tempData[1]);
                                            if(isset($tempRow[$tempKey00]) && $tempRow[$tempKey00]){
                                                $currentValue = $tempRow[$tempKey00];
                                                if($tempOperand && intval($tempOperand) == 1){
                                                    $currentValue = $currentValue + $tempValue00;
                                                }else{
                                                    $currentValue = $currentValue - $tempValue00;
                                                }
                                                $tempRow[$tempKey00] = round($currentValue, 2);
                                            }else{
                                                $pos = strpos($tempKey00, "adj");
                                                if($pos && intval($pos) == 0){
                                                    $tempKey00 = "adj_{$tempKey00}";
                                                }
                                                $tempRow[$tempKey00] = round($tempValue00, 2);
                                            }
                                        }
                                    }
                                }
                            }

                        }
                        foreach ($tempRow as $kkx => $vvx) {
                            if(count($_contCodes) > 0 && in_array($kkx, $_contCodes)){
                                $totalDeductions += floatval($tempRow[$kkx]);
                            }
                        }

                        if($totalDeductions){
                            $arrPsData[$key] = $tempRow;
                        }
                    }
                }
            }

            $tempColumns = array();

            if(is_array($arrPsData) && count($arrPsData) > 0){
                foreach ($arrPsData as $key => $value) {
                    $tempKeys = array_keys($value);
                    $employee = (object) $this->core_layout->getEmployeeData($value["emp_id"]);
                    $tempName = isset($employee->display_name_1) && $employee->display_name_1 ? strtoupper($employee->display_name_0): strtoupper("No Assigned Name");
                    $tempColumns = array();
                    foreach ($tempKeys as $kkxx => $vx) {
                        if(!in_array($vx, $defaultFields) && $vx){ $tempColumns[$vx] = $value[$vx]; }
                    }
                    $value["row_columns"] = $tempColumns;
                    $value["column_count"] = count($tempColumns);
                    $value["employee_name"] = $tempName;
                    $arrData[$key] = $value;
                }

                if(is_array($arrData) && count($arrData) > 0){
                    $keys = array_column($arrData, "employee_name");
                    array_multisort($keys, SORT_ASC, $arrData);
                }
            }
        }
        return $arrData;
    }

    public function generatePayrollSheetContributionMultiple___original($psIds = array()){
        $arrData = array();
        if(is_array($psIds) && count($psIds) > 0){
            $contributionCode = array();
            $arrPsData = array();
            $arrKxy = array();
            $arrMaxCount = array();
            $defaultFields = array("id", "emp_id", "idno", "regular_pay", "gross_pay", "gross_pay_taxable", "sss", "sss_prov", "ph", "hdmf", "tax", "net_pay");

            if(is_array($psIds) && count($psIds) > 0){
                $deductions = $this->generateContributionDeductionAdjustments($psIds);
                $this->db->select("UPPER(GROUP_CONCAT(TRIM(ps_loans.code) ORDER BY ps_loans.code ASC)) as loan_code");
                $this->db->from($this->tbl_payroll_sheet." ps");
                $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.payroll_sheet_id = ps.id", "left");
                $this->db->join($this->tbl_hris_loans." loans", "loans.id = ps_loan_payment.loan_id", "left");
                $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id", "left");
                $this->db->where_in("ps.id", $psIds);
                $this->db->group_by("ps.id");
                $qx = $this->db->get();
                foreach ($qx->result() as $kkk => $vvv) {
                    if($vvv->loan_code){
                        $arrD = explode(",", $vvv->loan_code);
                        $arx = array_count_values($arrD);
                        foreach ($arx as $kxy => $vxy) {
                            $arrKxy[$kxy][] = $vxy;
                        }
                    }
                }
                foreach ($arrKxy as $key => $value) {
                    $tempKey = strtolower($key);
                    $arrMaxCount[$tempKey] = max($value);
                }

                $this->db->select("UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_loans.code)) ORDER BY ps_loans.code ASC)) as loan_code,
                UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_custom_adj.particulars)) ORDER BY ps_custom_adj.particulars ASC)) as adj_code,
                UPPER(GROUP_CONCAT(DISTINCT(TRIM(ps_created_adj.particulars)), '||', ps_loans.code ORDER BY ps_created_adj.particulars ASC)) as adj_created_code");
                $this->db->from($this->tbl_payroll_sheet." ps");
                $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.payroll_sheet_id = ps.id", "left");
                $this->db->join($this->tbl_hris_loans." loans", "loans.id = ps_loan_payment.loan_id", "left");
                $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id", "left");
                $this->db->join($this->tbl_ps_custom_adjustments." ps_custom_adj", "ps_custom_adj.payroll_sheet_id = ps.id AND ps_custom_adj.cadj_type = 0", "left");
                $this->db->join($this->tbl_ps_created_adjustments." ps_created_adj", "ps_created_adj.payroll_sheet_id = ps.id AND ps_created_adj.status = 1 AND
                LOWER(ps_created_adj.description) = LOWER(ps_loans.code) OR LOWER(ps_created_adj.description) = LOWER(ps_loans.loan_name)", "left");
                $this->db->where_in("ps.id", $psIds);
                $q = $this->db->get();
                if($q->num_rows() > 0){
                    $codes = explode(",", $q->row()->loan_code);
                    $adj_codes = explode(",", $q->row()->adj_code);
                    $adj_created_code = explode(",", $q->row()->adj_created_code);

                    if(is_array($adj_codes) && count($adj_codes) > 0){
                        foreach ($adj_codes as $key => $value) {
                            if($value){
                                $value = str_replace(" ", "_", $value);
                                $adj_codes[$key] = $value;
                            }else{
                                if(isset($adj_codes[$key])){ unset($adj_codes[$key]); }
                            }
                        }
                    }

                    if(is_array($adj_created_code) && count($adj_created_code) > 0){
                        foreach ($adj_created_code as $key => $value) {
                            if($value){
                                $tempAdj = explode("||", $value);
                                $tempCode = str_replace(" ", "_", $tempAdj[1]);
                                if(!in_array($tempCode, $codes)){
                                    $codes[] = $tempCode;
                                }
                            }
                        }
                    }

                    $tempArrData = array();
                    $tempArrData = array_merge($codes, $adj_codes);
                    if(is_array($tempArrData) && count($tempArrData) > 0){
                        $contributionCode = $tempArrData;
                    }
                }

                $arrFields = array("sss", "sss_prov", "ph", "hdmf", "tax");
                /*** $sqlSelect = "ps.id, ps.emp_id, emp.idno, ps.rate, SUM(ps.gross_pay) as gross_pay,
                SUM(ps.net_pay) as net_pay, SUM(ps.sss) as sss, SUM(ps.sss_prov) as sss_prov, SUM(ps.ph) as ph,
                SUM(ps.hdmf) as hdmf, SUM(ps.tax) as tax,
                GROUP_CONCAT(DISTINCT(CONCAT(ps_custom_adj.particulars,'||',ps_custom_adj.amount, '||', ps_custom_adj.cadj_type))) custom_adjustments,
                GROUP_CONCAT(DISTINCT(CONCAT(ps_created_adj.particulars,'||', ps_created_adj.amount, '||', ps_created_adj.adj_type, '||', ps_created_adj.description))) created_adjustments,
                GROUP_CONCAT(DISTINCT(CONCAT(ps_loans.code,'||',ps_loan_payment.amount_due, '||', ps_loans.loan_class))) sss_hdmf_loan_deduction"; ***/

                $sqlSelect = "ps.id, ps.emp_id, emp.idno, emp.biometricno, comp.code as company, emp.tax_status, emp.tin_no, emp.phealth_no, emp.pagibig_no, emp.sss_no, 
                ROUND(ps.rate, 2) as rate, ROUND(SUM(IF(ps.is_bonus = 0,ps.basic_rate, 0)), 2) as basic_rate, ps.payroll_type, 
                ROUND(SUM(IF(ps.is_bonus = 0, ps.gross_pay, 0)), 2) as gross_pay, ROUND(SUM(IF(ps.is_bonus = 1, ps.net_pay, 0)), 2) as netpay_bonus,
                ROUND(SUM(IF(ps.is_bonus = 0, ps.net_pay, 0)), 2) as net_pay, ROUND(SUM(ps.sss),2) as sss, ROUND(SUM(ps.sss_prov), 2) as sss_prov, ROUND(SUM(ps.ph), 2) as ph,
                ROUND(SUM(ps.hdmf), 2) as hdmf, ROUND(SUM(ps.tax), 2) as tax, UPPER(emp.employee_status) as employee_status";
                $this->db->select($sqlSelect);
                $this->db->from($this->tbl_payroll_sheet." ps");
                $this->db->join($this->tbl_employees." emp", "emp.id = ps.emp_id");
                $this->db->join($this->tbl_tblcompanies." comp", "comp.id = ps.company_id");
                $this->db->where_in("ps.id", $psIds);
                $this->db->order_by("emp.lastname", "ASC");
                $this->db->group_by("ps.emp_id");
                $qData = $this->db->get();

                if($qData->num_rows() > 0){
                    foreach ($qData->result() as $key => $value) {
                        $tempRow = array();
                        $tempRow["id"] = $value->id;
                        $tempRow["idno"] = $value->idno;
                        $tempRow["biometricno"] = $value->biometricno;
                        $tempRow["company"] = $value->company;
                        $tempRow["tax_status"] = $value->tax_status;
                        $tempRow["tin_no"] = $value->tin_no;
                        $tempRow["phealth_no"] = $value->phealth_no;
                        $tempRow["pagibig_no"] = $value->pagibig_no;
                        $tempRow["sss_no"] = $value->sss_no;
                        $tempRow["employee_status"] = $value->employee_status;
                        $tempRow["regular_pay"] = $value->rate;
                        $tempRow["gross_pay"] = $value->gross_pay;
                        $tempRow["gross_pay_taxable"] = $value->basic_rate;
                        $tempRow["net_pay"] = $value->net_pay;
                        $tempRow["netpay_bonus"] = $value->netpay_bonus;
                        $tempRow["emp_id"] = $value->emp_id;
                        if($value->payroll_type !== "monthly"){ $tempRow["regular_pay"] = $value->basic_rate; }
                        $employee = $this->core_layout->getEmployeeData($value->emp_id);
                        $employee = (object) $employee;
                        $tempName = isset($employee->display_name_1) && $employee->display_name_1 ? strtoupper($employee->display_name_0): strtoupper("No Assigned Name");

                        foreach ($arrFields as $kk => $vv) { $tempRow[$vv] = $value->$vv; }
                        foreach ($contributionCode as $kx => $vx) {
                            $tempKey = strtolower($vx);
                            $tempRow[$tempKey] = 0;
                        }

                        if(is_array($deductions) && count($deductions) > 0){
                            $rowDeductions = $deductions[$value->emp_id];
                            $_tempDeductions = (object) $rowDeductions;
                            if(isset($_tempDeductions->sss_hdmf_loan_deduction) && $_tempDeductions->sss_hdmf_loan_deduction){
                                $tempDeductions = explode(",", $_tempDeductions->sss_hdmf_loan_deduction);
                                if(is_array($tempDeductions) && count($tempDeductions) > 0){
                                    $arrTempKeyxx = array();
                                    foreach ($tempDeductions as $xx => $rowx) {
                                        $tempData = explode("||", $rowx);
                                        if(is_array($tempData) && (count($tempData) > 0 && count($tempData) > 4)){
                                            $tempKey00 = trim(strtolower($tempData[0]));
                                            $tempValue00 = trim($tempData[1]);
                                            if(isset($tempRow[$tempKey00]) && $tempRow[$tempKey00]){
                                                $currentValue = $tempRow[$tempKey00];
                                                $currentValue += $tempValue00;
                                                $tempRow[$tempKey00] = round($currentValue, 2);
                                            }else{
                                                $tempRow[$tempKey00] = round($tempValue00, 2);
                                            }
                                            /*** if(isset($arrMaxCount[$tempKey00]) && $arrMaxCount[$tempKey00] > 1){
                                                $arrTempKeyxx[$tempKey00][] = $tempValue00;
                                            }else{
                                                $arrTempKeyxx[$tempKey00] = $tempValue00;
                                            } ***/
                                        }
                                    }
                                    /**** foreach ($arrTempKeyxx as $kzz => $vzz) {
                                        if(is_array($vzz) && count($vzz) > 0){
                                            if(isset($arrMaxCount[$kzz]) && $arrMaxCount[$kzz] > 0){
                                                for ($i=0; $i < intval($arrMaxCount[$kzz]) ; $i++) {
                                                    $tempIndex = $i + 1;
                                                    if(isset($vzz[$i]) && $vzz[$i]){
                                                        $tempRow["{$kzz}_{$tempIndex}"] = $vzz[$i];
                                                    }
                                                }
                                            }
                                        }else{
                                            $tempRow[$kzz] = $vzz;
                                        }
                                    } ***/
                                }
                            }
                            if(isset($_tempDeductions->custom_adjustments) && $_tempDeductions->custom_adjustments){
                                $tempCustomAdjustment = explode(",", $_tempDeductions->custom_adjustments);
                                if(is_array($tempCustomAdjustment) && count($tempCustomAdjustment) > 0){
                                    foreach ($tempCustomAdjustment as $xx => $rowx) {
                                        $_tempData = explode("||", $rowx);
                                        if(is_array($_tempData) && count($_tempData) > 0){
                                            $tempKey00 = trim(strtolower($_tempData[0]));
                                            $tempKey00 = str_replace(" ", "_", $tempKey00);
                                            $tempValue00 = trim($_tempData[1]);
                                            if(isset($_tempData[2]) && intval($_tempData[2]) == 0){
                                                if(isset($tempRow[$tempKey00]) && $tempRow[$tempKey00]){
                                                    $currentValue = $tempRow[$tempKey00];
                                                    $currentValue += $tempValue00;
                                                    $tempRow[$tempKey00] = round($currentValue, 2);
                                                }else{
                                                    $tempRow[$tempKey00] = round($tempValue00, 2);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
    
                            if(isset($_tempDeductions->created_adjustments) && $_tempDeductions->created_adjustments){
                                $tempCustomAdjustment = explode(",", $_tempDeductions->created_adjustments);
                                if(is_array($tempCustomAdjustment) && count($tempCustomAdjustment) > 0){
                                    foreach ($tempCustomAdjustment as $xx => $rowx) {
                                        $_tempData = explode("||", $rowx);
                                        if(is_array($_tempData) && count($_tempData) > 0 && count($_tempData) >= 4){
                                            $tempKey00 = trim(strtolower($_tempData[0]));
                                            $tempKey00 = str_replace(" ", "_", $tempKey00);
                                            $tempKey01 = trim(strtolower($_tempData[3]));
                                            $tempOperand = $_tempData[2];
                                             /*** alter phic to ph for philhealth code ***/
                                            if($tempKey00 == "phic"){ $tempKey00 = "ph"; }
                                            /*** alter phic to ph for philhealth code ***/

                                            $this->db->select("code");
                                            $this->db->where("loan_name", $tempKey01);
                                            $this->db->or_where("code", $tempKey01);
                                            $qTemp = $this->db->get($this->tbl_ps_loans);
                                            if($qTemp->num_rows() == 1){
                                                $tempKey00 = trim(strtolower($qTemp->row()->code));
                                            }
                                            
                                            $tempValue00 = trim($_tempData[1]);
                                            if(isset($tempRow[$tempKey00]) && $tempRow[$tempKey00]){
                                                $currentValue = $tempRow[$tempKey00];
                                                if($tempOperand && intval($tempOperand) == 1){
                                                    $currentValue = $currentValue + $tempValue00;
                                                }else{
                                                    $currentValue = $currentValue - $tempValue00;
                                                }
                                                $tempRow[$tempKey00] = round($currentValue, 2);
                                            }else{
                                                $pos = strpos($tempKey00, "adj");
                                                if($pos && intval($pos) == 0){
                                                    $tempKey00 = "adj_{$tempKey00}";
                                                }
                                                $tempRow[$tempKey00] = round($tempValue00, 2);
                                            }
                                        }
                                    }
                                }
                            }

                        }

                        $arrPsData[$key] = $tempRow;
                    }
                }
            }

            $tempColumns = array();

            if(is_array($arrPsData) && count($arrPsData) > 0){
                foreach ($arrPsData as $key => $value) {
                    $tempKeys = array_keys($value);
                    $employee = (object) $this->core_layout->getEmployeeData($value["emp_id"]);
                    $tempName = isset($employee->display_name_1) && $employee->display_name_1 ? strtoupper($employee->display_name_0): strtoupper("No Assigned Name");
                    $tempColumns = array();
                    foreach ($tempKeys as $kkxx => $vx) {
                        if(!in_array($vx, $defaultFields) && $vx){ $tempColumns[$vx] = $value[$vx]; }
                    }
                    $value["row_columns"] = $tempColumns;
                    $value["column_count"] = count($tempColumns);
                    $value["employee_name"] = $tempName;
                    $arrData[$key] = $value;
                }
            }
        }
        return $arrData;
    }

    function generateContributionDeductionAdjustments($psIds=array()){
        $arrResult = array();
        if(is_array($psIds) && count($psIds) > 0){
            $sqlSelect = "ps.emp_id, GROUP_CONCAT(DISTINCT(CONCAT(ps_custom_adj.particulars,'||',ps_custom_adj.amount, '||', ps_custom_adj.cadj_type, '||', ps_custom_adj.id))) custom_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(ps_created_adj.particulars,'||', ps_created_adj.amount, '||', ps_created_adj.adj_type, '||', ps_created_adj.description, '||', ps_created_adj.id))) created_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(ps_loans.code,'||',ps_loan_payment.amount_due, '||', ps_loans.loan_class, '||', ps_loan_payment.id))) sss_hdmf_loan_deduction";
            $this->db->select($sqlSelect);
            $this->db->from($this->tbl_payroll_sheet." ps");
            $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.payroll_sheet_id = ps.id", "left");
            $this->db->join($this->tbl_hris_loans." loans", "loans.id = ps_loan_payment.loan_id", "left");
            $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id", "left");
            $this->db->join($this->tbl_ps_custom_adjustments." ps_custom_adj", "ps_custom_adj.payroll_sheet_id = ps.id AND ps_custom_adj.cadj_type = 0", "left");
            $this->db->join($this->tbl_ps_created_adjustments." ps_created_adj", "ps_created_adj.payroll_sheet_id = ps.id AND ps_created_adj.status = 1", "left");
            $this->db->where_in("ps.id", $psIds);
            $this->db->group_by("ps.emp_id");
            $qData = $this->db->get();
            if($qData->num_rows() > 0){
                foreach ($qData->result() as $key => $value) {
                    $tempId = $value->emp_id;
                    unset($value->emp_id);
                    $arrResult[$tempId] = $value;
                }
            }
        }
        return $arrResult;
    }

    function generatePayrollJournal(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $tempGroup = "PAY DATE";
            $arrGroup = array(1=>"PAY DATE", 2=>"MONTH", 3=>"YEAR");
            $group = (isset($post["group"]) && $post["group"])? intval($post["group"]): 1;

            $arrCompany = array();
            $employeeIds = array();
            $tempStartDate = null;
            $tempEndDate = null;
            $hasDataFilter = false;
            $tempGroup = $arrGroup[$group];
            $tempArrFilter = array();
            $tempArrFilter["filter_by"] = $tempGroup;

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


            $this->db->select("a.id");
            $this->db->from("gccmaster.tblemployees a");
            if(isset($post["employee"]) && $post["employee"]){
                $hasDataFilter = true;
                $this->db->where_in("a.id", $post["employee"]);
            }
            
            $this->db->group_by("a.id");
            $queryTemp = $this->db->get();
            if($queryTemp->num_rows() > 0 && $hasDataFilter == true){
                foreach ($queryTemp->result() as $key => $value) {
                    if(!in_array($value->id, $employeeIds)){ $employeeIds[] = $value->id; }
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
            }
            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}"));
            }

            if(isset($post["pay_date"]) && $post["pay_date"]){
                $tempStartDate = date("Y-m-d", strtotime($post["pay_date"]));
                $tempEndDate = date("Y-m-d", strtotime($post["pay_date"]));
            }

            if($tempStartDate && $tempEndDate){
                $xDateFrom = date("F d, Y", strtotime($tempStartDate));
                $xDateTo = date("F d, Y", strtotime($tempEndDate));
                $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");
                $this->db->select("id");
                $this->db->from("payroll.payroll_sheet");
                $this->db->where("posted", 1);
                $this->db->group_start();
                // $this->db->where("DATE(date_start) <=", $tempStartDate);
                // $this->db->where("DATE(date_end) <=", $tempEndDate);
                // $this->db->or_where("DATE(pay_date) >=", $tempStartDate);
                // $this->db->or_where("DATE(pay_date) <=", $tempEndDate);
                $this->db->where('DATE(date_start) BETWEEN "' . $tempStartDate . '" AND "' . $tempEndDate . '"', NULL, FALSE);
                $this->db->where('DATE(date_end) BETWEEN "' . $tempStartDate . '" AND "' . $tempEndDate . '"', NULL, FALSE);
                $this->db->or_where('DATE(pay_date) BETWEEN "' . $tempStartDate . '" AND "' . $tempEndDate . '"', NULL, FALSE);
                $this->db->group_end();
                if($hasDataFilter && count($employeeIds) > 0){
                    $this->db->where_in("emp_id", $employeeIds);
                }
                if(isset($post["company"]) && $post["company"]){
                    $this->db->where_in("company_id", $post["company"]);
                }
                // $this->db->order_by("emp_id", "desc");
                $this->db->order_by("DATE(pay_date)", "desc");
                $query = $this->db->get();
                $tempSql = $this->db->last_query();

                if($query->num_rows() > 0){
                    $ids = array();
                    foreach ($query->result() as $key => $value) {
                        if(!in_array($value->id, $ids)){
                            $ids[] = $value->id;
                        }
                    }
                    $tempCount = count($ids);
                    if(is_array($ids) && $tempCount > 0){
                        $ids = array_map("intval", $ids);
                        $resultset["response"] = true;
                        $resultset["data"] = $ids;
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
        return $resultset;
    }

    function generateOvertimeReport(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $tempGroup = "PAY DATE";
            $arrGroup = array(1=>"PAY DATE", 2=>"MONTH", 3=>"YEAR");
            $group = (isset($post["group"]) && $post["group"])? intval($post["group"]): 1;
            $payrollGroup = isset($post["payroll_group"]) && $post["payroll_group"] ? $post["payroll_group"]: array(); 

            $arrCompany = array();
            $employeeIds = array();
            $tempStartDate = null;
            $tempEndDate = null;
            $hasDataFilter = false;
            $tempGroup = $arrGroup[$group];
            $tempArrFilter = array();
            $tempArrFilter["filter_by"] = $tempGroup;

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

            $tempCompRow = array();
            if(isset($post["company"]) && $post["company"]){
                $this->db->from("gcchris.tblcompanies");
                $this->db->where("id", $post["company"]);
                $qTemp = $this->db->get();
                if($qTemp->num_rows() == 1){ $tempCompRow = $qTemp->row_array(); }
            }
            
            $filterPayrollGroup = null;
            if(is_array($payrollGroup) && count($payrollGroup) > 0){
                $this->db->select("GROUP_CONCAT(DISTINCT TRIM(UPPER(description))) as payroll_group");
                $this->db->from("payroll.payroll_group");
                $this->db->where_in("id", $payrollGroup);
                $qPG = $this->db->get();

                $filterPayrollGroup = $qPG->row()->payroll_group;
            }

            $this->db->select("a.id");
            $this->db->from("gccmaster.tblemployees a");
            if(isset($post["employee"]) && $post["employee"]){
                $hasDataFilter = true;
                $this->db->where_in("a.id", $post["employee"]);
            }
            
            $this->db->group_by("a.id");
            $queryTemp = $this->db->get();
            if($queryTemp->num_rows() > 0 && $hasDataFilter == true){
                foreach ($queryTemp->result() as $key => $value) {
                    if(!in_array($value->id, $employeeIds)){ $employeeIds[] = $value->id; }
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
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}"));
            }

            if(isset($post["pay_date"]) && $post["pay_date"]){
                $tempStartDate = date("Y-m-d", strtotime($post["pay_date"]));
                $tempEndDate = date("Y-m-d", strtotime($post["pay_date"]));
            }

            if($tempStartDate && $tempEndDate){
                $xDateFrom = date("F d, Y", strtotime($tempStartDate));
                $xDateTo = date("F d, Y", strtotime($tempEndDate));

                $this->db->select("id");
                $this->db->from("payroll.payroll_sheet");
                $this->db->where("posted", 1);
                $this->db->group_start();
                // $this->db->where("DATE(date_start) <=", $tempStartDate);
                // $this->db->where("DATE(date_end) <=", $tempEndDate);
                // $this->db->or_where("DATE(pay_date) >=", $tempStartDate);
                // $this->db->or_where("DATE(pay_date) <=", $tempEndDate);
                $this->db->where('DATE(date_start) BETWEEN "' . $tempStartDate . '" AND "' . $tempEndDate . '"', NULL, FALSE);
                $this->db->where('DATE(date_end) BETWEEN "' . $tempStartDate . '" AND "' . $tempEndDate . '"', NULL, FALSE);
                $this->db->or_where('DATE(pay_date) BETWEEN "' . $tempStartDate . '" AND "' . $tempEndDate . '"', NULL, FALSE);
                $this->db->group_end();
                if($hasDataFilter && count($employeeIds) > 0){
                    $this->db->where_in("emp_id", $employeeIds);
                }
                if(isset($post["company"]) && $post["company"]){
                    $this->db->where_in("company_id", $post["company"]);
                }
                // $this->db->order_by("emp_id", "desc");
                $this->db->order_by("DATE(pay_date)", "desc");
                $query = $this->db->get();
                $tempSql = $this->db->last_query();

                if($query->num_rows() > 0){
                    $ids = array();
                    foreach ($query->result() as $key => $value) {
                        if(!in_array($value->id, $ids)){
                            $ids[] = $value->id;
                        }
                    }
                    $tempCount = count($ids);

                    $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");
                    $tempArrFilter["month"] = strtoupper(date("Y F", strtotime("{$post["filter_year"]}-{$post["filter_month"]}")));
                    $tempArrFilter["company_description"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? strtoupper(trim($tempCompRow['description'])): "";
                    $tempArrFilter["company_address"] = isset($tempCompRow['company_address']) && $tempCompRow['company_address']  ? strtoupper(trim($tempCompRow['company_address'])): "";
                    $tempArrFilter["has_comp_desc"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? true: false;
                    $tempArrFilter["payroll_group"] = $filterPayrollGroup;

                    if(is_array($ids) && $tempCount > 0){
                        $ids = array_map("intval", $ids);
                        $resultset["response"] = true;
                        $resultset["data"] = $ids;
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
        return $resultset;
    }

    function getPayrollJournalRequest(){
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

        $rowData = $this->payrollJournalReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->payrollJournalReportListCount($filteredId, $search);

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

    // function to display data for datatable of payroll journal request
    function payrollJournalReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder){

        if(is_array($filteredId) && count($filteredId) > 0){
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.idno,
            a.gross_pay as gross_pay, IFNULL(comp.description, b.company_id) as company_description, a.basic_rate as basic_rate, a.pay_date as pay_date, b.biometricno, a.rate, a.date_start, a.date_end, a.net_pay";

            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = b.company_id', "LEFT");
            $this->db->where_in("a.id", $filteredId);

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
            // $this->db->group_by("a.emp_id");
            

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $this->db->order_by("b.lastname, b.firstname, a.pay_date","asc");
            $query = $this->db->get();

            $loans = $this->db->get_where("payroll.loans", array("is_archive"=>0))->result_array();
            if ($query->num_rows() > 0) {
                $data = array();
                
                foreach ($query->result() as $key => $item) {
                    
                    $tempRecord = (object) $this->core_layout->getEmployeeData($item->emp_id);
                    $tempName = (isset($tempRecord->display_name_1) && $tempRecord->display_name_1)? $tempRecord->display_name_1: "No assigned name";
                    $item->employee_code = $item->idno;
                    $item->employee_name = $item->idno." - ".$tempName;
                    $item->gross_pay = $item->gross_pay;
                    
                    $total_deduct = 0;
                    foreach($loans as $items){
                        foreach($this->getLoansPayment($item->id, $items['code'])['total'] as $aaa){
                            $total_deduct += $aaa->amount_due;
                        }
                        
                        $total_deduction = md5("total_deduction");
                        $amount_due = md5("amount_due");
                        $code = strtoupper($items['code']);
                        $md5_key = md5($code);
                        $loan_amount = $this->getLoansPayment($item->id, $items['code'])['amount'];
                        if(!empty($loan_amount)){
                            $final_amount = str_replace("/","<br>",$loan_amount);
                        }else{
                            $final_amount = "0.00";
                        }
                        
                        $item->$md5_key = $final_amount;
                        $item->$total_deduction = $total_deduct == 0 ? "0.00" : number_format($total_deduct,2);
                        $final_amountdue = number_format($item->gross_pay - $total_deduct, 2);
                        $item->$amount_due  = $final_amountdue == 0 ? "<label class='text-center'>0.00</label>" : "<label class='text-center'>".$final_amountdue."</label>";
                    }
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

    // function to display number of entries of requested data of payroll journal
    function payrollJournalReportListCount($filteredId, $search){
        $count = 0;
        if(is_array($filteredId) && count($filteredId) > 0){
            $filterFields = array("b.firstname", "b.lastname");
            $this->db->select("a.id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.department_id, b.position");
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->where_in("a.id", $filteredId);

            $this->db->where("a.posted", 1);
            $this->db->group_by("a.id", "desc");
            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }
            $this->db->order_by('b.lastname', 'asc');
            $query = $this->db->get();
            $count = $query->num_rows();
        }

        return $count;
    }

    function generateOvertimeSummary(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $tempGroup = "PAY DATE";
            $arrGroup = array(1=>"PAY DATE", 2=>"MONTH", 3=>"YEAR");
            $group = (isset($post["group"]) && $post["group"])? intval($post["group"]): 1;
            $payrollGroup = isset($post["payroll_group"]) && $post["payroll_group"] ? $post["payroll_group"]: array();

            $arrCompany = array();
            $employeeIds = array();
            $tempStartDate = null;
            $tempEndDate = null;
            $hasDataFilter = false;
            $tempGroup = $arrGroup[$group];
            $tempArrFilter = array();
            $tempArrFilter["filter_by"] = $tempGroup;

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

            
            $tempCompRow = array();
            if(isset($post["company"]) && $post["company"]){
                $this->db->from("gcchris.tblcompanies");
                $this->db->where("id", $post["company"]);
                $qTemp = $this->db->get();
                if($qTemp->num_rows() == 1){ $tempCompRow = $qTemp->row_array(); }
            }
            
            $filterPayrollGroup = null;
            if(is_array($payrollGroup) && count($payrollGroup) > 0){
                $this->db->select("GROUP_CONCAT(DISTINCT TRIM(UPPER(description))) as payroll_group");
                $this->db->from("payroll.payroll_group");
                $this->db->where_in("id", $payrollGroup);
                $qPG = $this->db->get();

                $filterPayrollGroup = $qPG->row()->payroll_group;
            }
            
            $this->db->select("a.id");
            $this->db->from("gccmaster.tblemployees a");
            if(isset($post["employee"]) && $post["employee"]){
                $hasDataFilter = true;
                $this->db->where_in("a.id", $post["employee"]);
            }
            
            $this->db->group_by("a.id");
            $queryTemp = $this->db->get();
            if($queryTemp->num_rows() > 0 && $hasDataFilter === true){
                foreach ($queryTemp->result() as $key => $value) {
                    if(!in_array($value->id, $employeeIds)){ $employeeIds[] = $value->id; }
                }
            }

            
            $isDateRange = isset($post["date_range"]) && $post["date_range"];
            $isFilterMonth = isset($post["filter_month"]) && $post["filter_month"];

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
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
            }elseif (isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
            }

            if(isset($post["pay_date"]) && $post["pay_date"]){
                $tempStartDate = date("Y-m-d", strtotime($post["pay_date"]));
                $tempEndDate = date("Y-m-d", strtotime($post["pay_date"]));
            }

            if($tempStartDate && $tempEndDate){
                $xDateFrom = date("F d, Y", strtotime($tempStartDate));
                $xDateTo = date("F d, Y", strtotime($tempEndDate));
                $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");
                $this->db->select("ts.id");
                $this->db->from("gcctimeutility.timesheet ts");
                $this->db->join("gccmaster.tblemployees emp", "emp.id=ts.emp_id", "LEFT");
                $this->db->join("gcchris.tblcompanies comp", "comp.id=emp.company_id", "LEFT");
                $this->db->where("ts.has_overtime", 1);
                $this->db->group_start();
                $this->db->where('DATE(ts.overtime_in) BETWEEN "' . $tempStartDate . '" AND "' . $tempEndDate . '"', null, false);
                $this->db->where('DATE(ts.overtime_out) BETWEEN "' . $tempStartDate . '" AND "' . $tempEndDate . '"', null, false);
                $this->db->or_where('DATE(ts.date) BETWEEN "' . $tempStartDate . '" AND "' . $tempEndDate . '"', null, false);
                $this->db->group_end();
                if($hasDataFilter && !empty($employeeIds)){
                    $this->db->where_in("emp_id", $employeeIds);
                }
                if(isset($post["company"]) && $post["company"]){
                    $this->db->where_in("comp.id", $post["company"]);
                }
                $this->db->order_by("DATE(ts.overtime_in)");
                $query = $this->db->get();
                $resultset["sql"] = $this->db->last_query();

                if($query->num_rows() > 0){
                    $ids = array();
                    foreach ($query->result() as $key => $value) {
                        if(!in_array($value->id, $ids)){
                            $ids[] = $value->id;
                        }
                    }
                    $tempCount = count($ids);

                    if($isDateRange === false && $isFilterMonth){ $tempArrFilter["month"] = strtoupper(date("Y F", strtotime("{$post["filter_year"]}-{$post["filter_month"]}"))); }
                    $tempArrFilter["company_description"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? strtoupper(trim($tempCompRow['description'])): "";
                    $tempArrFilter["company_address"] = isset($tempCompRow['company_address']) && $tempCompRow['company_address']  ? strtoupper(trim($tempCompRow['company_address'])): "";
                    $tempArrFilter["has_comp_desc"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? true: false;
                    $tempArrFilter["payroll_group"] = $filterPayrollGroup;

                    if(is_array($ids) && $tempCount > 0){
                        $ids = array_map("intval", $ids);
                        $resultset["response"] = true;
                        $resultset["data"] = $ids;
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
        return $resultset;
    }

    public function getOvertimeSummaryRequest(){
        $post = $this->input->post();
        $search = $post['search']['value'] ?? false;
        $limit = $post['length'] ?? 10;
        $offset = $post['start'] ?? 0;
        $sortBy = $post['columns'] ?? 1;
        $sortOrder = $post['order'] ?? null;
        $filteredIds = $post['ids'] ?? [];
        $clearTable = $post['clear_table'] === 'true';
        $coverageDate = $post['filters']['coverage_date'] ?? false;

        $results = $this->overtimeSummaryList($filteredIds, $search, $limit, $offset, $sortBy, $sortOrder, $coverageDate);
        $rowCount = $this->overtimeSummaryListCount($filteredIds, $search);

        if ($clearTable) { $rowCount = 0; $results = []; }

        return [
            'recordsTotal' => $rowCount,
            'recordsFiltered' => $rowCount,
            'data' => $results['data'] ?? [],
            'clear_table' => $clearTable,
        ];
    }
    // end function

    // Function to display data for datatable of payroll journal request
    protected function overtimeSummaryList($filteredIds, $search, $limit, $offset, $sortBy, $sortOrder, $coverageDate){
        if (is_array($filteredIds) && count($filteredIds) > 0) {
            $select = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.idno,
            a.total_accredited_ot_hrs as ot_hrs, a.total_accredited_ndiff_ot_hrs as ot_ndiff_hrs,
            IF(DATE(a.overtime_in) != NULL AND DATE(a.overtime_in) != '0000-00-00', DATE(a.overtime_in), DATE(a.date)) as overtime_in,
            ROUND(IF(LOWER(b.payroll_type) = 'monthly', ROUND( IFNULL(b.basic_rate, 0), 2) * 12 / ROUND( IFNULL(comp.work_days_in_year, 314), 2),
            IFNULL(b.basic_rate, 0)), 2) as basic_rate,
            ROUND(IF(LOWER(allw.frequency) = 'month', ROUND( IFNULL(allw.rate, 0), 2) * 12 / ROUND( IFNULL(comp.work_days_in_year, 314), 2),
            IFNULL(allw.rate, 0)), 2) as allowance_rate,
            a.has_overtime, a.has_shift, IF((a.shift_am_start && a.shift_am_end) || (a.shift_pm_start && a.shift_pm_end), '1', '0') as ampm_shift,
            IF(a.is_holiday = 1 && a.paid_holiday = 1, '1', '0') as is_paid_holiday, a.payrate_id, DATE(a.date) as tsDate";

            $this->db->select($select);
            $this->db->from('gcctimeutility.timesheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = b.company_id', 'LEFT');
            $this->db->join($this->tbl_hris_allawances." allw", "allw.emp_id = b.id AND allw.is_active = 1 AND allw.is_archived = 0", "LEFT");
            $this->db->where_in('a.id', $filteredIds);
            $this->db->where('a.has_overtime', 1);
            $this->db->group_start();
            $this->db->where('a.total_accredited_ot_hrs >', 0);
            $this->db->or_where('a.total_accredited_ndiff_ot_hrs >', 0);
            $this->db->group_end();
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $this->db->order_by('b.lastname, b.firstname, a.date', 'asc');
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $data = array();
                foreach ($query->result() as $key => $item) {
                    $employeeRecord = (object) $this->core_layout->getEmployeeData($item->emp_id);
                    $employeeName = $employeeRecord->display_name_1 ?? 'No assigned name';
                    $totalOtHrs = $item->ot_hrs + $item->ot_ndiff_hrs;

                    $payrateTemp = intval($item->has_shift) === 1 ? "regular" : "rest day";
                    $payrateSetting = $this->getPayrateSetting($payrateTemp);
                    $tempPayrateSetting = intval($item->payrate_id) > 0 ? $this->getPayrateSettingById($item->payrate_id) : $payrateSetting;
                    $allowPaidAllowance = $tempPayrateSetting->particulars !== "regular" || intval($item->has_shift) === 0 || intval($tempPayrateSetting->is_holiday) === 1 ? 1 : 0;
                    /*** paid holiday allowance ***/
                    if(intval($tempPayrateSetting->is_holiday) === 1){ $allowPaidAllowance = intval($item->is_paid_holiday) === 1 ? 1 : 0; }
                    /*** paid holiday allowance ***/

                    $otRate = floatval($tempPayrateSetting->ot_rate) > 0 ? floatval($tempPayrateSetting->ot_rate): 1;
                    $otNightDiffRate = floatval($tempPayrateSetting->ot_night_diff_rate) > 0 ? floatval($tempPayrateSetting->ot_night_diff_rate): 0;
                    
                    $tempOtRate = ($otRate * 100) - 100;
                    $perMinute = $item->basic_rate / 8;
                    $otAllowance = floatval($item->allowance_rate) > 0 && $item->ot_hrs >= 1 ? floatval($item->allowance_rate): 0;
                    $allowancePerMinute = $otAllowance / 8;
                    
                    $totalOtPay = $perMinute * floatval($totalOtHrs);
                    $totalOtNdPay = $perMinute * floatval($item->ot_ndiff_hrs);
                    $totalOtAllowance = $allowPaidAllowance === 1 ? $allowancePerMinute * floatval($totalOtHrs): 0;
                    
                    $tempOtPayWithRate = $otRate > 1 ? ($tempOtRate / 100) * $totalOtPay : 0;
                    $totalOtPayable = $totalOtPay + $tempOtPayWithRate;
                    $nightDiffPay = $otNightDiffRate > 0 ? $totalOtNdPay * $otNightDiffRate: 0;

                    $item->_ot_rate = $tempOtRate;
                    $item->_temp_ot_pay = $tempOtPayWithRate;
                    $item->employee_name = $employeeName;
                    $item->day = date('D', strtotime($item->overtime_in));
                    $item->daily_rate = $item->basic_rate;
                    $item->has_shift = $item->ampm_shift === "0" ? "0": $item->has_shift;
                    $item->pay_info = $tempPayrateSetting;
                    $item->per_minute = $perMinute;
                    $item->ot_hrs = $totalOtHrs > 0 ? $totalOtHrs: '-';
                    $item->allowance = $otAllowance > 0 ? $otAllowance: '';
                    $item->ot_rate = $otRate;
                    $item->ot_allowance = $totalOtAllowance === 0 ? '-' : $totalOtAllowance;
                    $item->ot_pay = $totalOtPay;
                    $item->ot_pay_20 = $otRate > 1 && $tempOtRate == 25 && $tempOtPayWithRate > 0 ? $tempOtPayWithRate: '';
                    $item->ot_pay_30 = $otRate > 1 && $tempOtRate == 30 && $tempOtPayWithRate > 0 ? $tempOtPayWithRate: '';
                    $item->ot_ndiff_hrs = ($item->ot_ndiff_hrs == 0) ? '-' : $item->ot_ndiff_hrs;
                    $item->night_diff = $nightDiffPay > 0 ? $nightDiffPay : '';
                    $item->ot_adj = $this->getOTAdjustment($coverageDate, $item->emp_id);
                    $item->amount = $totalOtPayable + $nightDiffPay + $totalOtAllowance;
                    $item->total_pay = $item->amount;
                    $data[$key] = $item;
                }

                $resultset = array();
                $resultset['data'] = $data;
                return $resultset;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
    // end function

    //function to get overtime adjustment of payroll period
    protected function getOTAdjustment($coverage_date, $emp_id){
        $tempAmount = 0;
        if($coverage_date){
            $dates = explode("-", $coverage_date);
            $start = date("Y-m-d", strtotime($dates[0]));
            $end = date("Y-m-d", strtotime($dates[1]));
            $this->db->select("sum(adj.amount) as amount");
            $this->db->from("payroll.payroll_sheet ps");
            $this->db->join("payroll.payroll_sheet_custom_adjustments adj", "adj.payroll_sheet_id=ps.id", "LEFT");
            $this->db->where("ps.emp_id",$emp_id);
            $this->db->where("ps.posted", 1);
            $this->db->group_start();
            $this->db->where("DATE(ps.date_start) >=", $start);
            $this->db->where("DATE(ps.date_end) <=", $end);
            $this->db->group_end();
            $this->db->like("adj.particulars", "OT");
            $data = $this->db->get();
            $tempAmount = $data->row()->amount;
        }

        return $tempAmount;
    }

    //function to detect if employee has sunday shift
    function getPersonnelSchedule($emp_id){
        $this->db->select("shift_id");
        $this->db->from("gcctimeutility.personnel personnel");
        $this->db->join("gccmaster.tblemployees emp","emp.biometricno=personnel.biometricno", "LEFT");
        $this->db->where("emp.id", $emp_id);
        $data = $this->db->get();

        return $this->getWeekdayOfShift($data->row('shift_id'));
	}

    function getWeekdayOfShift($shift_id){
        $this->db->select("*");
        $this->db->from("gcctimeutility.shift_schedule_resource");
        $this->db->where("shift_id",$shift_id);
        $data = $this->db->get();
        $sched = unserialize($data->row('shift_resource'));
        $temp = array();
        foreach($sched as $item){
            $days = array();
            $days = $this->db->get_where("gcctimeutility.shift_schedule_list", array("id"=>$item))->row('weekday');
            $temp[] = $days;
        }
        return $temp;
    }
    //

    //function to get overtime allowance of employee based on employee daily allowance and if overtime date is restday
    function getOvertimeAllowance($emp_id){
        return $this->db->get_where("gcchris.allowances", array("emp_id"=>$emp_id, "is_archived"=>0))->row('rate');

    }
    //
    // function to display number of entries of requested data of payroll journal
    protected function overtimeSummaryListCount($filteredId, $search){
        $count = 0;
        if(is_array($filteredId) && count($filteredId) > 0){
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.idno,
            a.total_accredited_ot_hrs as ot_hrs, a.total_accredited_ndiff_ot_hrs as ot_ndiff_hrs";

            $this->db->select($sqlSelect);
            $this->db->from('gcctimeutility.timesheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = b.company_id', "LEFT");
            $this->db->where_in("a.id", $filteredId);
            $this->db->where("a.has_overtime", 1);
            $this->db->order_by("b.lastname, b.firstname, a.overtime_in","asc");
            $query = $this->db->get();
            $count = $query->num_rows();
        }

        return $count;
    }

    function getOvertimeRequest(){
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

        $rowData = $this->overtimeReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->overtimeReportListCount($filteredId, $search);

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
    function overtimeReportList($filteredId, $search, $limit, $offset, $sortBy, $sortOrder){
        if(is_array($filteredId) && count($filteredId) > 0){
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.idno,
            (a.ot_minutes/60) as ot_hrs,  a.ot_amount as ot_amount, a.pay_date as pay_date, b.biometricno, a.rate, a.date_start, a.date_end";
            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id', "LEFT");
            $this->db->join('payroll.payout_schedule c', 'c.id = b.payout_sched', "LEFT");
            $this->db->where_in("a.id", $filteredId);
            $this->db->where("a.posted", 1);
            $this->db->where("a.ot_minutes !=", 0);
            $this->db->where("a.is_bonus", 0);
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $this->db->order_by("b.lastname, b.id, a.pay_date","asc");
            $query = $this->db->get();
            $resultset = array();
            if ($query->num_rows() > 0) {
                $data = array();
                foreach ($query->result() as $key => $item) {
                    $tempRecord = (object)$this->core_layout->getEmployeeData($item->emp_id);
                    $tempName = (isset($tempRecord->display_name_1) && $tempRecord->display_name_1)? $tempRecord->display_name_1: "No assigned name";
                    if($item->idno){
                        $idno = $item->idno;
                    }else{
                        $idno = "N/A";
                    }
                    $item->employee_name = $idno." - ".$tempName;
                    $item->date_start = $item->date_start;
                    $item->date_end = $item->date_end;
                    $item->pay_date = $item->pay_date;
                    $item->ot_hrs = $item->ot_hrs;
                    $item->ot_amount = $item->ot_amount;
                    $data[$key] = $item;
                }
                $resultset['data'] = $data;

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
    function overtimeReportListCount($filteredId, $search){
        $count = 0;
        if(is_array($filteredId) && count($filteredId) > 0){
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id, b.idno,
            a.ot_minutes as ot_hrs,  a.ot_amount as ot_amount, a.pay_date as pay_date, b.biometricno, a.rate, a.date_start, a.date_end";

            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id', "LEFT");
            $this->db->where_in("a.id", $filteredId);

            $this->db->where("a.posted", 1);
            $this->db->group_by("a.id", "desc");
            $this->db->order_by('b.lastname', 'asc');
            $this->db->order_by("a.date_end","asc");
            $query = $this->db->get();
            $count = $query->num_rows();
        }
        return $count;
    }

    public function getPostedPayrollSheetYears()
    {
        $arrData = array();
        $this->db->select('`year` id, `year` `text`');
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

        $data['results'] = $arrData;

        return $data;
    }

    function getLoansPayment($id,$loan_type){
        $loan = array();
        $result_data = array();
        $resultSet = array();
        $x = $this->db->order_by("id","ASC")->get_where("payroll.loans", array('is_archive'=>0))->result();
        foreach($x as $y){
            $loan[] = $y->code;
                $where = array(
                    "ps_sheet.id"=>$id,
                    "ps_loans.code"=>$loan_type,
                    "ps_sheet.is_bonus"=>0
                );
        }
        
        $this->db->select("*");
        $this->db->from('payroll.payroll_sheet_loan_payments loan_payments');
        $this->db->join('gcchris.loans hris_loans', 'loan_payments.loan_id=hris_loans.id', 'LEFT');
        $this->db->join('payroll.payroll_sheet ps_sheet', 'ps_sheet.id=loan_payments.payroll_sheet_id', 'LEFT');
        $this->db->join('payroll.loans ps_loans', 'ps_loans.id=hris_loans.loan_id', 'LEFT');
        $this->db->where($where);
        $data = $this->db->get();
        foreach($data->result() as $result){
            if($result->amount_due > 0){
                $result_data[] = number_format($result->amount_due,2);
            }
        }
        $resultSet['amount'] = implode("/",$result_data);
        $resultSet['total'] = $data->result();
        return $resultSet;
    }

    function getEmpLoan($id){
        $loans = array();
        $loans[] = $this->db->get_where($this->tbl_hris_loans, array('emp_id'=>$id))->result();
        return $loans;
    }

    function getEmployeeContributions(){
        $post = $this->input->post();
        $result = $this->generatePayrollSheetContribution($post['id']);
        return $result;
    }

    function getLoanTypes(){
        $data = array();
        $loan = array();
        // $title = array();
        $result = $this->db->order_by("loan_name","ASC")->get_where("payroll.loans", array('is_archive'=>0))->result_array();
        $rows = $this->db->order_by("loan_name","ASC")->get_where("payroll.loans", array('is_archive'=>0))->num_rows();
        if($rows > 0){
            foreach($result as $y){
                $md5_key = md5(strtoupper($y['code']));
                $loan[$md5_key] = $y['code'];
            }
            $loan[md5("total_deduction")] = "total deduction";
            $loan[md5("amount_due")] = "amount due";
        }
        $data["data"] = $loan;
        return $data;
    }

    function getLoanByCode($code){
        return $this->db->get_where("payroll.loans", array("code"=>$code))->row('loan_name');
    }

    function generateWeeklyMonthRange($tempDtStart=null, $tempDtEnd=null){
        if($tempDtStart && $tempDtEnd){
            $tempStartPayDate = $tempDtStart;
            $tempEndPayDate = $tempDtEnd;

            $year = date('Y', strtotime($tempDtStart));
            $monthStart = strtolower(date('F', strtotime($tempDtStart)));
            $monthStartNum = date('m', strtotime($tempDtStart));

            $monthEnd = strtolower(date('F', strtotime($tempDtEnd)));
            $monthEndNum = date('m', strtotime($tempDtEnd));

            $alteredStartDate = $tempDtStart;
            $alteredEndDate = $tempDtEnd;

            $qStartDate = $this->db->get_where($this->tbl_ps_monthly_week_count, array("year"=>$year, "month_name"=>$monthStart));
            if($qStartDate->num_rows() == 1){
                $qStartRow = $qStartDate->row();
                $tempStartWeeks = @unserialize($qStartRow->weeks);
                if(is_array($tempStartWeeks) && count($tempStartWeeks) > 0){
                    $firstWeek = reset($tempStartWeeks);
                    $firstDay = reset($firstWeek);
                    if($firstDay){
                        $alteredStartDate = date("Y-m-d", strtotime("{$year}-{$monthStartNum}-{$firstDay}"));
                    }

                    
                    if($firstWeek){
                        if(count($firstWeek) >= 4 && count($firstWeek) != 7){
                            $tempYearx = date("Y", strtotime($tempDtStart));
                            $alteredStartDate = $this->getStartOfWeek($alteredStartDate, $tempYearx)['week_start'];
                        }
                        /*** $tempStartPayDate = $this->getStartOfWeek($alteredStartDate, date("Y", strtotime($tempDtStart)))['next_week']; ***/
                        $tempStartPayDate = $this->getStartOfWeek($alteredStartDate, date("Y", strtotime($alteredStartDate)))['next_week'];
                    }
                }
            }

            $qEndDate = $this->db->get_where($this->tbl_ps_monthly_week_count, array("year"=>$year, "month_name"=>$monthEnd));
            if($qEndDate->num_rows() == 1){
                $qEndRow = $qEndDate->row();
                $tempEndingWeeks = @unserialize($qEndRow->weeks);
                if(is_array($tempEndingWeeks) && count($tempEndingWeeks) > 0){
                    $lastWeek = end($tempEndingWeeks);
                    $lastDay = end($lastWeek);
                    if($lastDay){
                        $alteredEndDate = date("Y-m-d", strtotime("{$year}-{$monthEndNum}-{$lastDay}"));
                    }
                    if($lastWeek){
                        if(count($lastWeek) >= 4 && count($lastWeek) != 7){
                            $alteredEndDate = $this->getEndOfWeek($alteredEndDate, date("Y", strtotime($tempDtStart)))['week_end'];
                        }
                        /*** $tempEndPayDate = $this->getEndOfWeek($alteredEndDate, date("Y", strtotime($tempDtStart)))['next_week']; ***/
                        $tempEndPayDate = $this->getEndOfWeek($alteredEndDate, date("Y", strtotime($alteredEndDate)))['next_week'];
                    }
                }
            }

            /*** minus 1 day for cut-off weekly SAT-FRI***/
            $alteredStartDate = date("Y-m-d", strtotime("-1 day", strtotime($alteredStartDate)));
            $alteredEndDate = date("Y-m-d", strtotime("+1 day", strtotime($alteredEndDate)));
            /*** minus 1 day for cut-off weekly SAT-FRI***/
            return array(
                "date_start"=>$alteredStartDate,
                "date_end"=>$alteredEndDate,
                "paydate_start"=>$tempStartPayDate,
                "paydate_end"=>$tempEndPayDate,
            );
        }else{
            return false;
        }
    }

    function getStartOfWeek($ddate, $year){
        $duedt = explode("-", $ddate);
        $date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
        $week  = (int)date('W', $date);

        $dtYear = strtotime($duedt[0]);
        $nwYear = strtotime($year);
        if($dtYear != $nwYear){ $year = $duedt[0]; }

        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $day = $dto->format('Y-m-d');
        
        $weekStart = date("Y-m-d", strtotime('last sunday', strtotime($day)));

        $firstDayOfYear = date("Y-m-d", strtotime('first day of January'));
        if($ddate == $firstDayOfYear){
            $lastSunday = date("Y-m-d", strtotime("last sunday", strtotime($day)));
            $weekStart = date("Y-m-d", strtotime($lastSunday));
            /*** $weekStart = date("Y-m-d", strtotime('-1 year', strtotime("last sunday", $lastSunday))); ***/
        }
        $weekEnd = date("Y-m-d", strtotime('+6 days', strtotime($weekStart)));
        $nextWeek = date("Y-m-d", strtotime('+13 days', strtotime($weekStart)));

        $ret['week_start'] = $weekStart;
        $ret['week_end'] = $weekEnd;
        $ret['next_week'] = $nextWeek;

        /*** $ret['week_start'] = date("Y-m-d", strtotime('last sunday', strtotime($day)));
        $firstDayOfYear = date("Y-m-d", strtotime('first day of January'));
        if($ddate == $firstDayOfYear){
            $ret['week_start'] = date("Y-m-d", strtotime('-1 year', strtotime("last sunday", strtotime($day))));
        }else{
            $ret['week_start'] = date("Y-m-d", strtotime('last sunday', strtotime($day)));
        } ***/

        return $ret;
    }

    function getEndOfWeek($ddate, $year){
        $duedt = explode("-", $ddate);
        $date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
        $week  = (int)date('W', $date);

        $dto = new DateTime();
        $dto->setISODate($year, $week);

        /*** altered function ***/
        $day = $dto->format('Y-m-d');
        $weekStart = date("Y-m-d", strtotime('last sunday', strtotime($day)));
        $weekEnd = date("Y-m-d", strtotime('+6 days', strtotime($weekStart)));
        $nextWeek = date("Y-m-d", strtotime('+13 days', strtotime($weekStart)));
        $ret['week_start'] = $weekStart;
        $ret['week_end'] = $weekEnd;
        $ret['next_week'] = $nextWeek;
        /*** altered function ***/

        /*** fix in this section
         * 
        $ret['week_start'] = $dto->format('Y-m-d');
        $dto->modify('+12 days');
        $ret['week_end'] = $dto->format('Y-m-d'); 
         *
        end fix in this section ***/
        return $ret;
    }
    

    public function generatePostedNetpayRecords(){
        $resultset = array();
        $resultset["grand_total"] = 0;

        $post = $this->input->post();
        if(isset($post) && $post){
            $tempFilter = array();
            $tempFilter["is_bonus"] = isset($post['is_bonus']) ? intval($post['is_bonus']) : 0;
            $payrollGroup = isset($post["payroll_group"]) && $post["payroll_group"] ? strtoupper($post["payroll_group"]): null;
            $tempRange = "";
            if(isset($post["group"]) && intval($post["group"]) === 1){
                $tempPayDate = date("Y-m-d", strtotime($post["pay_date"]));
                $tempFilter["pay_date"] = $tempPayDate;
                $tempRange = explode("-", $post["date_range"]);
                if(count($tempRange) === 2){
                    $tempDateStart = date("Y-m-d", strtotime($tempRange[0]));
                    $tempDateEnd = date("Y-m-d", strtotime($tempRange[1]));
                    $tempFilter["date_start"] = $tempDateStart;
                    $tempFilter["date_end"] = $tempDateEnd;
                }
            }else{
                if(isset($post['filter_month'], $post['filter_year']) && ($post['filter_month'] && $post['filter_year'])){
                    $tempMonth = date("F", strtotime("{$post['filter_year']}-{$post['filter_month']}-1"));
                    $tempFilter["month_name"] = strtolower($tempMonth);
                    $tempFilter["year"] = $post['filter_year'];
                }
                if(isset($post['is_bonus']) && $post['is_bonus']){
                    $tempFilter["is_bonus"] = $post['is_bonus'];
                }
            }
            if(isset($post['payroll_sched']) && $post['payroll_sched']){
                $tempFilter["payroll_sched"] = $post['payroll_sched'];
            }

            if(is_array($tempFilter) && count($tempFilter) > 0){
                $filter = array();
                $arrData = array();
                $grandTotal = 0;

                $filteredCompany = null;
                if(isset($post["company"]) && $post["company"]){
                    $tempCompany = $this->db->get_where($this->tbl_tblcompanies, array("id"=>$post["company"]));
                    if($tempCompany->num_rows() == 1){
                        $filteredCompany = trim($tempCompany->row()->code);
                    }
                }
                $sqlSelect = "SUM(a.net_pay) as net_pay, b.lastname, b.firstname, b.middlename, b.suffix, UPPER(c.code) as company_description, 
                    IF(d.name IS NULL, b.position, d.name) as position, UPPER(b.work_status) as work_status, b.date_start, b.bank_name, b.atm_info";
                $this->db->select($sqlSelect);
                $this->db->from($this->tbl_payroll_sheet." a");
                $this->db->join($this->tbl_employees." b", "b.id = a.emp_id");
                $this->db->join($this->tbl_tblcompanies." c", "c.id = a.company_id");
                $this->db->join($this->tbl_tblposition." d", "d.id = b.position", "left");
                $this->db->where("a.posted", 1);
                foreach ($tempFilter as $key => $value) { 
                    if($key == 'date_start' OR $key == 'date_end'){

                    }else{
                        $this->db->where("a.{$key}", $value); 
                    }
                }
                if(count((array)$tempRange) === 2){
                    $this->db->where("a.date_start >=", $tempDateStart); 
                    $this->db->where("a.date_end <=", $tempDateEnd); 
                }

                /** added for payroll_group */
                if(isset($post["company"]) && $post["company"]){ $this->db->where("a.company_id", $post["company"]);  }

                if(isset($post["employees"]) && $post["employees"]){
                    $this->db->where_in("b.id", $post["employees"]);
                }else if(isset($post["serialized_employees"]) && $post["serialized_employees"]){
                    $this->db->where_in("b.id", explode(",",$post["serialized_employees"]));
                }
                /** added for payroll_group */

                $this->db->order_by("b.lastname", "ASC");
                $this->db->group_by("a.emp_id, a.company_id");
                $queryNetpay = $this->db->get();
                
                if($queryNetpay->num_rows() > 0){
                    $ctr = 1;
                    foreach ($queryNetpay->result() as $key => $value) {
                        $tempRs = (array) $value;
                        $tempDisplay = (object) $this->core_layout->getDisplayName($tempRs);
                        $tempName = (isset($tempDisplay->display_name_0) && $tempDisplay->display_name_0)? strtoupper($tempDisplay->display_name_0): strtoupper("no display name");
                        $value->employee_name = $tempName;
                        $value->net_pay_decimal = number_format($value->net_pay, 2, ".", ",");
                        $arrData[$key] = $value;
                        $grandTotal+= floatval($value->net_pay);

                    }
                }
                $payout_schedule = null;
                $qTemp = $this->db->get_where($this->tbl_payout_schedule, array("id"=>$post["payroll_sched"]));
                if($qTemp->num_rows() == 1){ $payout_schedule = $qTemp->row()->name; }
                $tempFilter["payout_schedule"] = $payout_schedule; 
                $tempFilter["group"] = $post["group"]; 
                
                if($filteredCompany){ $tempFilter["company_description"] = $filteredCompany; }

                $tempHtml = $this->load->view("payroll/reports/printable/netpay_print_content", array("filter"=>$tempFilter, "data"=>$arrData, "grand_total"=>$grandTotal), true);
                $resultset["data"] = $arrData;
                $resultset["printable_content"] = $tempHtml;
                $resultset["grand_total"] = $grandTotal;
                $resultset["grand_total_decimal"] = number_format($grandTotal, 2, ".", ",");
            }

            $resultset["filter"] = $tempFilter;
            if(is_array($arrData) && count($arrData) > 0){ $resultset["response"] = true;
            }else{ $resultset["response"] = false; }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    private function getSettings()
    {
        $settings_array = $this->db->get($this->tbl_payroll_settings)->result();
        $settings = array_reduce($settings_array,
            function ($carry, $obj) {
                $key = $obj->setting_name;
                $carry[$key] = $obj;
                return $carry;
            }, []);

        return (object) $settings;
    }

    public function setGeneratedTaxableIncome($psIds=array(), $arrParams=array()){
        if(is_array($psIds) && count($psIds) > 0){
            $data = $this->generatePayrollSheetContributionMultiple($psIds);
            if(is_array($data) && count($data) > 0){
                $truncate = $this->db->truncate("payroll.taxable_income");
                foreach ($data as $taxable) {
                    $_ndata = array();
                    
                    $nTaxable = (object) $taxable;
                    $empIdx = $nTaxable->emp_id;

                    if(is_array($arrParams) && count($arrParams) > 0){
                        foreach ($arrParams as $nkey => $param) { $_ndata[$nkey] = $param; }
                        $arrParams["emp_id"] = $empIdx;
                    }

                    $_ndata["emp_id"] = $empIdx;
                    $_ndata["tax_status"] = $nTaxable->tax_status;
                    $_ndata["idno"] = $nTaxable->idno;
                    $_ndata["biometricno"] = $nTaxable->biometricno;
                    $_ndata["employee_name"] = $nTaxable->employee_name;
                    $_ndata["tin_no"] = $nTaxable->tin_no;
                    $_ndata["phealth_no"] = $nTaxable->phealth_no;
                    $_ndata["pagibig_no"] = $nTaxable->pagibig_no;
                    $_ndata["sss_no"] = $nTaxable->sss_no;
                    $_ndata["company_description"] = $nTaxable->company;
                    $_ndata["regular_pay"] = $nTaxable->regular_pay;
                    $_ndata["gross_pay"] = $nTaxable->gross_pay;
                    $_ndata["sss"] = $nTaxable->sss;
                    $_ndata["sss_prov"] = $nTaxable->sss_prov;
                    $_ndata["ph"] = $nTaxable->ph;
                    $_ndata["hdmf"] = $nTaxable->hdmf;
                    $_ndata["tax"] = $nTaxable->tax;
                    $_ndata["gross_pay_taxable"] = $nTaxable->gross_pay_taxable;
                    $_ndata["net_pay"] = $nTaxable->net_pay;
                    $_ndata["netpay_bonus"] = $nTaxable->netpay_bonus;
                    $_ndata["employee_status"] = $nTaxable->employee_status;

                    if(is_array($arrParams) && count($arrParams) > 0){
                        $qTaxable = $this->db->get_where("payroll.taxable_income", $arrParams);
                        if($qTaxable->num_rows() === 1){
                            $taxableId = $qTaxable->row()->id;
                            $this->db->update("payroll.taxable_income", $_ndata, array("id"=>$taxableId));
                        }else{ 
                            $this->db->insert("payroll.taxable_income", $_ndata);
                        }
                    }
                }
            }
        }
    }

    public function generateLeaveCreditsReport(){
        $resultset = array();
        $post = $this->input->post();
        $status = (isset($post['emp_status']) && $post['emp_status']) ? $post['emp_status'] : false;
        if(isset($post) && $post){
            $filteredCompany = null;
            if(isset($post["company"]) && $post["company"]){
                $tempCompany = $this->db->get_where($this->tbl_tblcompanies, array("id"=>$post["company"]));
                if($tempCompany->num_rows() == 1){
                    $filteredCompany = trim($tempCompany->row()->code);
                }
                $this->db->reset_query();
            }
            $hasEmployeeFilter = isset($post["employee"]) && is_array($post["employee"]) && count($post["employee"]) > 0;
            $hasMonthFilter = false;
            $hasYearFilter = false;

            if(isset($post["filter_by"], $post["month"]) && ($post["filter_by"] == 2 && $post["month"])){
                if($hasEmployeeFilter == false){
                    $this->db->select("id");
                    $this->db->from($this->tbl_employees);
                    $this->db->where("MONTH(date_start)", $post["month"]);
                    $this->db->where("company_id", $post["company"]);
                    
                    if ($status != 'All') {
                        $this->db->where('employee_status', $status);
                    }

                    if ($status == 'All' || $status == 'Active') {
                        $this->db->where_not_in('work_status', ['NO CONTRACT', 'CONSULTANT', 'PART-TIME']); //added to generate only the regular and probi work status
                    }

                    $this->db->group_by("id");
                    $qFilter = $this->db->get();
                    if($qFilter->num_rows() > 0){
                        foreach ($qFilter->result() as $row) {
                            $post["employee"][] = $row->id;
                        }
                    }
                }else{
                    $hasMonthFilter = true;
                }
            }else if(isset($post["filter_by"]) && $post["filter_by"] == 1){
                if($hasEmployeeFilter == false){
                    $this->db->select("id");
                    $this->db->from($this->tbl_employees);
                    $this->db->where("company_id", $post["company"]);
                    
                    if ($status != 'All') {
                        $this->db->where('employee_status', $status);
                    }

                    if ($status == 'All' || $status == 'Active') {
                        $this->db->where_not_in('work_status', ['NO CONTRACT', 'CONSULTANT', 'PART-TIME']); //added to generate only the regular and probi work status
                    }

                    $this->db->group_by("id");
                    $qFilter = $this->db->get();
                    if($qFilter->num_rows() > 0){
                        foreach ($qFilter->result() as $row) {
                            $post["employee"][] = $row->id;
                        }
                    }
                }
            }

            if(isset($post["employee"]) && is_array($post["employee"]) && count($post["employee"]) > 0){
                $arrCharges = array();

                $this->db->select("loans.emp_id, TRIM(UPPER(ps_loans.loan_name)) as loan_name, ROUND(SUM(ps_loan_payment.amount_due), 2) paid_amount, loans.amount as loan_amount");
                $this->db->from($this->tbl_hris_loans." loans");
                $this->db->join($this->tbl_ps_loans." ps_loans", "ps_loans.id = loans.loan_id");
                $this->db->join($this->tbl_ps_loan_payments." ps_loan_payment", "ps_loan_payment.loan_id = loans.id");
                $this->db->join($this->tbl_payroll_sheet." ps", "ps.id = ps_loan_payment.payroll_sheet_id");
                $this->db->where_in("loans.emp_id", $post["employee"]);
                $this->db->group_start();
                $this->db->where("loans.active", 1);
                $this->db->where("loans.paid", 0);
                $this->db->where("loans.is_archived", 0);
                $this->db->where("ps_loans.loan_type", 0);
                $this->db->group_end();
                $this->db->group_start();
                $this->db->where("ps.posted", 1);
                $this->db->where("ps.is_bonus", 0);
                $this->db->group_end();
                $this->db->group_by("loans.id");
                $qLoans = $this->db->get();

                if($qLoans->num_rows() > 0){
                    foreach ($qLoans->result() as $loan) {
                        $empId = $loan->emp_id;
                        $paidAmount = floatval($loan->paid_amount);
                        $loanAmount = floatval($loan->loan_amount);
                        $totalAmount = $loanAmount - $paidAmount;

                        if(!isset($arrCharges[$empId]["amount"])){
                            $arrCharges[$empId]["amount"] = 0;
                        }
                        
                        if($totalAmount > 0){
                            if(isset($arrCharges[$empId]["amount"])){ 
                                $arrCharges[$empId]["amount"] += $totalAmount; 
                                $arrCharges[$empId]["charges"]["label"] = $loan->loan_name;
                                $arrCharges[$empId]["charges"]["balance"] = $totalAmount;
                            }
                        }
                    }
                }
                
                $tempSIL = 5;
                $this->db->select("emp.id, UPPER(TRIM(CONCAT(emp.lastname, 
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END))) as lastname, UPPER(TRIM(CONCAT(emp.firstname, 
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(' ', SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END))) as firstname, UPPER(TRIM(CONCAT(emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END,' ', TRIM(emp.lastname),
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END))) as employee_name, LOWER(emp.payroll_type) as payroll_type, allw.frequency as allowance_type, 
                $tempSIL as sil, comp.work_days_in_year, 
                ROUND(IF(LOWER(emp.payroll_type) = 'monthly', 
                    ROUND( IFNULL(emp.basic_rate, 0), 2) * 12 / ROUND( IFNULL(comp.work_days_in_year, 314), 2)
                    , IFNULL(emp.basic_rate, 0)), 2) as basic_rate, 
                ROUND(IF(LOWER(allw.frequency) = 'month', 
                ROUND( IFNULL(allw.rate, 0), 2) * 12 / ROUND( IFNULL(comp.work_days_in_year, 314), 2)
                , IFNULL(allw.rate, 0)), 2) as allowance_rate, emp.date_start");
                $this->db->from($this->tbl_employees." emp");
                $this->db->join($this->tbl_tblcompanies." comp", "comp.id = emp.company_id", "LEFT");
                $this->db->join($this->tbl_hris_allawances." allw", "allw.emp_id = emp.id AND allw.is_active = 1 AND allw.is_archived = 0", "LEFT");
                $this->db->where_in("emp.id", $post["employee"]);
                if($hasMonthFilter){ 
                    $this->db->where("MONTH(emp.date_start)", $post["month"]); 
                }

                $this->db->where('DATE_ADD(emp.date_start, INTERVAL 1 YEAR) < NOW()'); //added 1 year to date_start of employee and restrict employee if 1year below

                // added to filtered out by employee status
                if ($status){ 
                    if ($status != 'All') {
                        $this->db->where('emp.employee_status', $status);
                    }

                    if ($status == 'All' || $status == 'Active') {
                        $this->db->where_not_in('emp.work_status', ['NO CONTRACT', 'CONSULTANT', 'PART-TIME']); //added to generate only the regular and probi work status
                    }
                }
                // added to filtered out by employee status

                $this->db->order_by("TRIM(emp.lastname), TRIM(emp.firstname)", "ASC");
                $this->db->group_by("emp.id");
                $queryCredits = $this->db->get();
                $ctr = $queryCredits->num_rows();
    
                if($ctr > 0){
                    $nResult = array();
                    $nCharges = array();
                    foreach ($queryCredits->result() as $key => $credits) {
                        $credits->charges = 0;
                        $empDateStart = date('Y-m-d', strtotime($credits->date_start));
                        $empAddYear = date('Y-m-d', strtotime($empDateStart.' +1year'));
                        if(isset($arrCharges[$credits->id]["amount"]) && $arrCharges[$credits->id]["amount"]){
                            $credits->charges = $arrCharges[$credits->id]["amount"];
                            if(isset($arrCharges[$credits->id]["charges"]) && $arrCharges[$credits->id]["charges"]){
                                $arrCharges[$credits->id]["charges"]["id"] = $credits->id;
                                $arrCharges[$credits->id]["charges"]["employee_name"] = $credits->employee_name;
                                $rowCharges = $arrCharges[$credits->id]["charges"];
                                $nCharges[] = $rowCharges;
                            }
                        }
                        $credits->add_year = $empAddYear;
                        $nResult[$key] = $credits;
                    }

                    $resultset["response"] = true;
                    $resultset["data"] = $nResult;
                    $resultset["charges"] = $nCharges;
                    $resultset["toastr_msg"] = "A total of ($ctr) record(s) found.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "No record(s) found!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No employee data found!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return  $resultset;
    }

    public function setGeneratedSssRemittances($psIds=array()){
        $data = array();
        if(is_array($psIds) && count($psIds) > 0){
            $adjustments = $this->generateContributionDeductionAdjustments($psIds);
            
            $sqlSelect = "a.id, a.emp_id, MAX(a.year) as year, MAX(a.month_name) as month_name, b.idno, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id,
            b.sss_no, b.idno, ROUND(SUM(a.gross_pay), 2) as gross_pay, ROUND(SUM(a.basic_rate), 2) as basic_rate,
            ROUND(SUM(a.sss), 2) as sss_ee, ROUND(SUM(a.sss_er), 2) as sss_er, ROUND(SUM(a.sss_prov), 2) as sss_prov_ee, ROUND(SUM(a.sss_prov_er), 2) as sss_prov_er,
            IFNULL(comp.description, b.company_id) as company_description, IFNULL(comp.sss_class, 1) as classification, CONCAT(a.emp_id, '|', GROUP_CONCAT(DISTINCT(a.id))) as ps_group,
            UPPER(TRIM(CONCAT(b.firstname, ' ',
                CASE WHEN UPPER(TRIM(b.middlename)) != 'N/A' AND UPPER(TRIM(b.middlename)) != 'NONE' AND
                        TRIM(b.middlename) !='' AND b.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(b.middlename, 1, 1), '.') ELSE ''
                END,' ', TRIM(b.lastname),
                CASE WHEN UPPER(TRIM(b.suffix)) != 'N/A' AND
                    UPPER(TRIM(b.suffix !='NONE')) AND b.suffix !='' AND
                    b.suffix IS NOT NULL THEN CONCAT(' ', b.suffix) ELSE ''
                END))) as employee_name";
            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = b.company_id', "LEFT");
            $this->db->where_in("a.id", $psIds);

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
            $this->db->group_by("a.emp_id");
            $this->db->order_by('b.lastname', 'asc');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrTempAdjustments = array();
                $arrAdjustmentCount = array();
                foreach ($query->result() as $key => $item) {
                    $sssAdjustment = 0;
                    $provAdjustment = 0;

                    $item->ps_data = array();
                    $psGroup = explode('|', $item->ps_group);
                    if(is_array($psGroup) && count($psGroup) == 2 && $psGroup[1]){
                        $tempIds = explode(",", $psGroup[1]);
                        $psQuery = $this->db
                        ->select("a.pay_date, a.date_start, a.date_end, a.payroll_seq, a.sss, a.sss_er, a.sss_prov, a.sss_prov_er, GROUP_CONCAT(CONCAT(b.particulars, '|',b.amount,'|',b.adj_type)) as adjustment")
                        ->from($this->tbl_payroll_sheet." as a")
                        ->join($this->tbl_ps_created_adjustments." as b", "b.payroll_sheet_id = a.id AND b.status = 1", "left")
                        ->where_in('a.id', $tempIds)
                        ->group_by('a.id')
                        ->get();

                        $item->ps_data = $psQuery->result();
                    }

                    $tempAdjustment = $adjustments[$item->emp_id];
                    $item->adjustment = $tempAdjustment;
                    if(isset($tempAdjustment->created_adjustments) && $tempAdjustment->created_adjustments){
                        $createdAdjustment = explode(",", $tempAdjustment->created_adjustments);
                        if(is_array($createdAdjustment) && count($createdAdjustment) > 0){
                            foreach ($createdAdjustment as $kk => $vv) {
                                $_adjustment = explode("||", $vv);
                                if(is_array($_adjustment) && count($_adjustment) > 0){
                                    if(strtolower($_adjustment[0]) == "sss"){
                                        if(floatval($sssAdjustment) >= 0 && $_adjustment[2] == 1){
                                            $sssAdjustment += floatval($_adjustment[1]);
                                        }
                                        if(floatval($sssAdjustment) >= 0 && $_adjustment[2] == 0){
                                            $sssAdjustment -= floatval($_adjustment[1]);
                                        }
                                    }
                                    if(strtolower($_adjustment[0]) == "sss_prov"){
                                        if(floatval($provAdjustment) >= 0 && $_adjustment[2] == 1){
                                            $provAdjustment += floatval($_adjustment[1]);
                                        }
                                        if(floatval($provAdjustment) >= 0 && $_adjustment[2] == 0){
                                            $provAdjustment -= floatval($_adjustment[1]);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $item->_sss_adjustment = $sssAdjustment;
                    $item->_prov_adjustment = $provAdjustment;

                    $alteredKeys = array();
                    $item->sss = array();

                    $_basic_rate = floatval($item->basic_rate);
                    $_gross_pay = floatval($item->gross_pay);
                    $_tempAmount = $_gross_pay;

                    $settings = $this->getSettings();
                    $sss_contribution_basis = $settings->sss_contribution_basis->setting_value;
                    $item->contribution_basis = $sss_contribution_basis;

                    $tempParameter = "_{$sss_contribution_basis}";
                    $sssContributionBasis = ${$tempParameter};
                    $_tempAmount = $sssContributionBasis ? $sssContributionBasis: $_gross_pay;
                    $item->amount_basis = $_tempAmount;
                    /*** insert code here ***/
                    
                    if(floatval($item->sss_ee) >= 0 && floatval($sssAdjustment) >= 0){
                        $item->sss_ee += floatval($sssAdjustment);
                        $item->sss_ee = round($item->sss_ee, 2);
                    }else if(floatval($item->sss_ee) >= 0 && floatval($sssAdjustment) < 0){
                        $item->sss_ee += floatval($sssAdjustment);
                        $item->sss_ee = round($item->sss_ee, 2);
                    }

                    if(floatval($item->sss_prov_ee) >= 0 && floatval($provAdjustment) >= 0){
                        $item->sss_prov_ee += floatval($provAdjustment);
                        $item->sss_prov_ee = round($item->sss_prov_ee, 2);
                    }else if(floatval($item->sss_prov_ee) >= 0 && floatval($provAdjustment) < 0){
                        $item->sss_prov_ee += floatval($provAdjustment);
                        $item->sss_prov_ee = round($item->sss_prov_ee, 2);
                    }

                    $tempProps = array("ee", "er", "ec_ee", "ec_er", "prov_ee", "prov_er");
                    foreach($tempProps as $ii){ $item->$ii = 0; }
                    if(floatval($item->sss_ee) > 0){
                        $sqlSelect = implode(",", $tempProps);
                        $sqlSelect .= ", id, from, to";
                        $this->db->select($sqlSelect);
                        $this->db->where("from <=", $_tempAmount);
                        $this->db->where("to >=", $_tempAmount);
                        $this->db->where("classification", $item->classification);
                        $this->db->where("status", 1);
                        $query = $this->db->get("payroll.sss_table");
                        if($query->num_rows() > 0){
                            $qTempRow = $query->row();
                            $item->sss = $qTempRow;
                            foreach($tempProps as $ii){
                                $item->$ii = $qTempRow->$ii;
                            }
                        }
                        if(isset($item->er) && floatval($item->er) > 0 
                            && ($item->sss_er == 0 || $item->sss_er !== $item->er)){
                            $item->sss_er = floatval($item->er);
                        }
                        if(isset($item->prov_er) && floatval($item->prov_er) > 0 
                            && ($item->sss_prov_er == 0 || $item->sss_prov_er !== $item->prov_er)){
                            $item->sss_prov_er = floatval($item->prov_er);
                        }
                    }

                    if(floatval($item->sss_ee) == 0){ $item->sss_er = 0; }
                    if(floatval($item->sss_prov_ee) == 0){ $item->sss_prov_er = 0; }

                    $hasIdentificationNumber = false;
                    if($item->sss_no){
                        $temp = preg_replace("/[^0-9]/", "", $item->sss_no);
                        $xtemp = intval($temp);
                        if($xtemp > 0){
                            $hasIdentificationNumber = true;
                        }
                    }
                    if($hasIdentificationNumber == false){ $item->sss_no = "00-0000000-0"; }
                    $item->has_identification_no = $hasIdentificationNumber;

                    $data[] = $item;
                }
            }
        }

        return $data;
    }

    public function setGeneratedPhicRemittances($psIds=array()){
        $data = array();
        if(is_array($psIds) && count($psIds)){
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id,
            b.phealth_no, b.idno, a.rate, ROUND(SUM(a.ph), 2) as ph_ee, ROUND(SUM(a.ph), 2) as ph_er,
            IFNULL(comp.description, b.company_id) as company_description, cr_adj.amount, cr_adj.adj_type, 
            CONCAT(a.emp_id, '|', GROUP_CONCAT(DISTINCT(a.id))) as ps_group, 
            GROUP_CONCAT(DISTINCT cr_adj.payroll_sheet_id, '|', cr_adj.adj_type, '|', cr_adj.amount) as created_adjustments";
            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = b.company_id', "LEFT");
            $this->db->join('payroll.payroll_sheet_created_adjustments cr_adj', 
            'cr_adj.payroll_sheet_id = a.id AND cr_adj.particulars = "PHIC" AND cr_adj.status = 1', 
                "LEFT");
                $this->db->where_in("a.id", $psIds);
                $this->db->where("a.posted", 1);
                $this->db->where("a.is_bonus", 0);
                $this->db->group_by("a.emp_id");
                $this->db->order_by('b.lastname', 'asc');
                
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $arrTempAdjustments = array();
                    $arrAdjustmentCount = array();
                    foreach ($query->result() as $key => $item) {
                        $item->ps_data = array();
                        $psGroup = explode('|', $item->ps_group);
                    if(is_array($psGroup) && count($psGroup) == 2 && $psGroup[1]){
                        $tempIds = explode(",", $psGroup[1]);
                        $psQuery = $this->db
                        ->select("a.pay_date, a.date_start, a.date_end, a.payroll_seq, a.ph, GROUP_CONCAT(CONCAT(b.particulars, '|',b.amount,'|',b.adj_type)) as adjustment")
                        ->from($this->tbl_payroll_sheet." as a")
                        ->join($this->tbl_ps_created_adjustments." as b", "b.payroll_sheet_id = a.id AND b.status = 1", "left")
                        ->where_in('a.id', $tempIds)
                        ->group_by('a.id')
                        ->get();
                        
                        $item->ps_data = $psQuery->result();
                    }
                    
                    $alteredKeys = array();
                    $tempRecord = (object) $this->core_layout->getEmployeeData($item->emp_id);
                    $tempName = (isset($tempRecord->display_name_0) && $tempRecord->display_name_0)? $tempRecord->display_name_0: "No assigned name";
                    $item->employee_name = $tempName;
                    $item->phic = array();
                    $tempBasicPay = floatval($item->rate);
                    
                    $tempProps = array("ee", "er");
                    foreach($tempProps as $ii){ $item->$ii = 0; }
                    $sqlSelect = implode(",", $tempProps);
                    $sqlSelect .= ", id, min, max, percentage, fixAmount";
                    $this->db->select($sqlSelect);
                    $this->db->where("min <=", $tempBasicPay);
                    $this->db->where("max >=", $tempBasicPay);
                    $query = $this->db->get("payroll.philhealth_table");
                    if($query->num_rows() > 0){
                        $qTempRow = $query->row();
                        $item->phic = $qTempRow;
                        $tempAmount = 0;
                        if($qTempRow->fixAmount){
                            $tempAmount = floatval($qTempRow->fixAmount) / 2;
                        }else{
                            $tempPercent = floatval($qTempRow->percentage) / 100;
                            if($tempPercent > 0){
                                $tempAmount = $tempBasicPay * $tempPercent;
                                $tempAmount = $tempAmount / 2;
                            }
                        }
                        
                        foreach($tempProps as $ii){
                            $qTempRow->$ii = $tempAmount;
                            $item->$ii = $qTempRow->$ii;
                        }
                    }

                    $tempAdjustmentAmount = 0;
                    $createdAdjustment = explode(",", $item->created_adjustments);
                    if(is_array($createdAdjustment) && count($createdAdjustment) > 0){
                        foreach ($createdAdjustment as $key => $adjustment) {
                            $currentAdjustment = explode("|", $adjustment);
                            if(is_array($currentAdjustment) && count($currentAdjustment) === 3){
                                $adjustmentType = intval($currentAdjustment[1]);
                                $adjustmentAmount = floatval($currentAdjustment[2]);
                                if($adjustmentAmount && $adjustmentAmount > 0){
                                    if($adjustmentType === 0){
                                        $tempAdjustmentAmount = $tempAdjustmentAmount - $adjustmentAmount;
                                    }else{
                                        $tempAdjustmentAmount = $tempAdjustmentAmount + $adjustmentAmount;
                                    }
                                }
                            }
                        }
                    }
                    
                    $item->ph_ee += $tempAdjustmentAmount;
                    $item->ph_er += $tempAdjustmentAmount;
                    
                    $hasIdentificationNumber = false;
                    if($item->phealth_no){
                        $temp = preg_replace("/[^0-9]/", "", $item->phealth_no);
                        $xtemp = intval($temp);
                        if($xtemp > 0){
                            $hasIdentificationNumber = true;
                        }
                    }
                    if($hasIdentificationNumber){ $data[] = $item; }
                }
            }
        }
        
        return $data;
    }
    public function setGeneratedHdmfRemittances($psIds=array()){
        $data = array();
        if(is_array($psIds) && count($psIds) > 0){
            $sqlSelect = "a.id, a.emp_id, b.firstname, b.lastname, b.middlename, b.suffix, b.company_id,
            b.pagibig_no, b.idno, a.rate, ROUND(SUM(a.hdmf), 2) as hdmf_ee, ROUND(SUM(a.hdmf), 2) as hdmf_er,
            IFNULL(comp.description, b.company_id) as company_description, cr_adj.amount, cr_adj.adj_type, 
            CONCAT(a.emp_id, '|', GROUP_CONCAT(DISTINCT(a.id))) as ps_group, 
            GROUP_CONCAT(DISTINCT cr_adj.payroll_sheet_id, '|', cr_adj.adj_type, '|', cr_adj.amount) as created_adjustments";
            $this->db->select($sqlSelect);
            $this->db->from('payroll.payroll_sheet a');
            $this->db->join('gccmaster.tblemployees b', 'a.emp_id = b.id');
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = b.company_id', "LEFT");
            $this->db->join('payroll.payroll_sheet_created_adjustments cr_adj', 
                'cr_adj.payroll_sheet_id = a.id AND cr_adj.particulars = "HDMF" AND cr_adj.status = 1', 
                "LEFT");
            $this->db->where_in("a.id", $psIds);

            $this->db->where("a.posted", 1);
            $this->db->where("a.is_bonus", 0);
            $this->db->group_by("a.emp_id");
            $this->db->order_by('b.lastname', 'asc');

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $data = array();
                $arrTempAdjustments = array();
                $arrAdjustmentCount = array();
                foreach ($query->result() as $key => $item) {
                    $item->ps_data = array();
                    $psGroup = explode('|', $item->ps_group);
                    if(is_array($psGroup) && count($psGroup) == 2 && $psGroup[1]){
                        $tempIds = explode(",", $psGroup[1]);
                        $psQuery = $this->db
                        ->select("a.pay_date, a.date_start, a.date_end, a.payroll_seq, a.hdmf, GROUP_CONCAT(CONCAT(b.particulars, '|',b.amount,'|',b.adj_type)) as adjustment")
                        ->from($this->tbl_payroll_sheet." as a")
                        ->join($this->tbl_ps_created_adjustments." as b", "b.payroll_sheet_id = a.id AND b.status = 1", "left")
                        ->where_in('a.id', $tempIds)
                        ->group_by('a.id')
                        ->get();

                        $item->ps_data = $psQuery->result();
                    }
                    
                    $alteredKeys = array();
                    $tempRecord = (object) $this->core_layout->getEmployeeData($item->emp_id);
                    $tempName = (isset($tempRecord->display_name_0) && $tempRecord->display_name_0)? $tempRecord->display_name_0: "No assigned name";
                    $item->employee_name = $tempName;
                    $item->hdmf = array();
                    $tempBasicPay = floatval($item->rate);

                    $tempProps = array("ee", "er");
                    foreach($tempProps as $ii){ $item->$ii = 0; }
                    $sqlSelect = implode(",", $tempProps);
                    $sqlSelect .= ", id";
                    $this->db->select($sqlSelect);
                    $query = $this->db->get("payroll.hdmf_amount");
                    if($query->num_rows() > 0){
                        $qTempRow = $query->row();
                        $item->hdmf = $qTempRow;
                        foreach($tempProps as $ii){
                            $item->$ii = $qTempRow->$ii;
                        }
                    }

                    $tempAdjustmentAmount = 0;
                    $createdAdjustment = explode(",", $item->created_adjustments);
                    if(is_array($createdAdjustment) && count($createdAdjustment) > 0){
                        foreach ($createdAdjustment as $key => $adjustment) {
                            $currentAdjustment = explode("|", $adjustment);
                            if(is_array($currentAdjustment) && count($currentAdjustment) === 3){
                                $adjustmentType = intval($currentAdjustment[1]);
                                $adjustmentAmount = floatval($currentAdjustment[2]);
                                if($adjustmentAmount && $adjustmentAmount > 0){
                                    if($adjustmentType === 0){
                                        $tempAdjustmentAmount = $tempAdjustmentAmount - $adjustmentAmount;
                                    }else{
                                        $tempAdjustmentAmount = $tempAdjustmentAmount + $adjustmentAmount;
                                    }
                                }
                            }
                        }
                    }

                    $item->hdmf_ee += $tempAdjustmentAmount;
                    $item->hdmf_er += $tempAdjustmentAmount;

                    /*** temporary fix ***/
                    $hasIdentificationNumber = false;
                    if($item->pagibig_no){
                        $temp = preg_replace("/[^0-9]/", "", $item->pagibig_no);
                        $xtemp = intval($temp);
                        if($xtemp > 0){
                            $hasIdentificationNumber = true;
                        }
                    }
                    if($hasIdentificationNumber){
                        $data[] = $item;
                    }
                }
            }
        }
        return $data;
    }

    protected function getPayrateSetting($particular){
        return $this->db->where("particulars", $particular)->get("payroll.payrate_settings")->row();
    }

    protected function getPayrateSettingById($id=null){
        return $this->db->where("id", $id)->get("payroll.payrate_settings")->row();
    }

    public function generateCustomPostedNetpayRecords(){
        $resultset = array();
        $resultset["grand_total"] = 0;

        $post = $this->input->post();
        if(isset($post) && $post){
            $tempFilter = array();
            $tempFilter["is_bonus"] = isset($post['is_bonus']) ? intval($post['is_bonus']) : 0;
            $tempRange = "";
            $option = isset($post['option']) && $post['option'] ? $post['option'] : 1; // 1 = all; 2 = earners; 3 = no earners

            if(isset($post["group"]) && intval($post["group"]) === 1){
                $tempPayDate = date("Y-m-d", strtotime($post["pay_date"]));
                $tempFilter["pay_date"] = $tempPayDate;
                $tempRange = explode("-", $post["date_range"]);
                if(count($tempRange) === 2){
                    $tempDateStart = date("Y-m-d", strtotime($tempRange[0]));
                    $tempDateEnd = date("Y-m-d", strtotime($tempRange[1]));
                    $tempFilter["date_start"] = $tempDateStart;
                    $tempFilter["date_end"] = $tempDateEnd;
                }
            }else{
                if(isset($post['filter_month'], $post['filter_year']) && ($post['filter_month'] && $post['filter_year'])){
                    $tempMonth = date("F", strtotime("{$post['filter_year']}-{$post['filter_month']}-1"));
                    $tempFilter["month_name"] = strtolower($tempMonth);
                    $tempFilter["year"] = $post['filter_year'];
                }
                if(isset($post['is_bonus']) && $post['is_bonus']){
                    $tempFilter["is_bonus"] = $post['is_bonus'];
                }
            }
            if(isset($post['payroll_sched']) && $post['payroll_sched']){
                $tempFilter["payroll_sched"] = $post['payroll_sched'];
            }

            if(is_array($tempFilter) && !empty($tempFilter)){
                $arrData = array();
                $grossTotal = 0;
                $grandTotal = 0;

                $filteredCompany = null;
                if(isset($post["company"]) && $post["company"]){
                    $tempCompany = $this->db->get_where($this->tbl_tblcompanies, array("id"=>$post["company"]));
                    if($tempCompany->num_rows() == 1){
                        $filteredCompany = trim($tempCompany->row()->code);
                    }
                }
                $sqlSelect = "a.*, SUM(a.basic_rate) as basic_rate, SUM(a.no_of_days) as no_of_days, SUM(a.total_undertime_amount) as total_undertime_amount,
                SUM(a.ot_amount) as ot_amount, SUM(a.total_ndiff_amount) as total_ndiff_amount, SUM(a.ot_ndiff_amount) as ot_ndiff_amount, SUM(a.total_holiday_amount) as total_holiday_amount,
                SUM(a.total_allowances) as total_allowances, SUM(a.gross_pay) as gross_pay, SUM(a.net_pay) as net_pay, b.lastname, b.firstname, b.middlename, b.suffix,
                UPPER(c.code) as company_description, UPPER(IF(d.name IS NULL, b.position, d.name)) as position, UPPER(b.work_status) as work_status, b.date_start,
                UPPER(e.code) as department_description, IFNULL(f.rate, 0) as allowance_rate, g.station_description as station";

                $this->db->select($sqlSelect);
                $this->db->from($this->tbl_payroll_sheet." a");
                $this->db->join($this->tbl_employees." b", "b.id = a.emp_id");
                $this->db->join($this->tbl_tblcompanies." c", "c.id = a.company_id");
                $this->db->join($this->tbl_tblposition." d", "d.id = b.position", "left");
                $this->db->join($this->tbl_tbldepartment.' e', 'e.id = b.department_id OR e.code = b.department_id', 'LEFT');
                $this->db->join($this->tbl_ps_allowances.' f', 'f.payroll_sheet_id = a.id', 'LEFT');
                $this->db->join($this->tbl_default_station.' g', 'g.employee_id = b.id', 'LEFT');

                if ($option == 2) {
                    $this->db->where('a.gross_pay >', 0);
                }

                if ($option == 3) {
                    $this->db->where('a.gross_pay <=', 0);
                }

                $this->db->where("a.posted", 1);
                foreach ($tempFilter as $key => $value) {
                    $this->db->where("a.{$key}", $value);
                }
                if(count((array)$tempRange) === 2){
                    $this->db->group_start();
                    $this->db->where("a.date_start >=", $tempDateStart);
                    $this->db->where("a.date_end <=", $tempDateEnd);
                    $this->db->group_end();
                }

                /** added for payroll_group */
                if(isset($post["company"]) && $post["company"]){ $this->db->where("a.company_id", $post["company"]);  }

                if(isset($post["employees"]) && $post["employees"]){
                    $this->db->where_in("b.id", $post["employees"]);
                } elseif (isset($post["serialized_employees"]) && $post["serialized_employees"]){
                    $this->db->where_in("b.id", explode(",",$post["serialized_employees"]));
                }
                /** added for payroll_group */

                $this->db->order_by("b.lastname", "ASC");
                $this->db->group_by("a.emp_id, a.company_id");
                $queryNetpay = $this->db->get();
                
                if($queryNetpay->num_rows() > 0){
                    foreach ($queryNetpay->result() as $key => $value) {
                        $tempRs = (array) $value;
                        $tempDisplay = (object) $this->core_layout->getDisplayName($tempRs);
                        $tempName = (isset($tempDisplay->display_name_0) && $tempDisplay->display_name_0)? strtoupper($tempDisplay->display_name_0): strtoupper("no display name");
                        $value->employee_name = $tempName;
                        $value->net_pay_decimal = number_format($value->net_pay, 2, ".", ",");
                        $arrData[$key] = $value;
                        $grossTotal+= floatval($value->gross_pay);
                        $grandTotal+= floatval($value->net_pay);
                        $value->payroll_group = $this->get_payroll_group($value->emp_id);
                    }
                }

                $payout_schedule = null;
                $qTemp = $this->db->get_where($this->tbl_payout_schedule, array("id"=>$post["payroll_sched"]));
                if($qTemp->num_rows() == 1){ $payout_schedule = $qTemp->row()->name; }
                $tempFilter["payout_schedule"] = $payout_schedule;
                $tempFilter["group"] = $post["group"];
                
                if($filteredCompany){ $tempFilter["company_description"] = $filteredCompany; }
                $resultset["data"] = $arrData;
                $resultset["gross_total"] = $grossTotal;
                $resultset["gross_total_decimal"] = number_format($grossTotal, 2, ".", ",");
                $resultset["grand_total"] = $grandTotal;
                $resultset["grand_total_decimal"] = number_format($grandTotal, 2, ".", ",");
                $resultset['payroll_option'] = $option == 2 ? 'earners' : ($option == 3 ? 'no earners' : 'all');
            }
            $resultset["filter"] = $tempFilter;
            if(is_array($arrData) && !empty($arrData)){ $resultset["response"] = true;
            }else{ $resultset["response"] = false; }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function get_payroll_group($id) {
        $result = ' --- ';

        $this->db->select('GROUP_CONCAT(DISTINCT f.description SEPARATOR ", ") as payroll_group');
        $this->db->join($this->tbl_payroll_group.' f', 'f.employee_id LIKE CONCAT("%s:", LENGTH(b.id), ' . $this->db->escape(':"') . ', b.id, ' . $this->db->escape('";%') . ')', 'LEFT');
        $this->db->from($this->tbl_employees.' b');
        $this->db->where('b.id', $id);
        $this->db->where('f.is_archived', 0);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $result = $row->payroll_group;
        }

        return $result;
    }

    public function cashAdvanceReport(){
        $rowCount = 0;
        $rowData = array();
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : "";
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
        $filterFields = array(
            "reference", 
            "firstname",
            "middlename",
            "lastname",
            "CONCAT(TRIM(firstname), ' ', LEFT(TRIM(middlename), 1), '.', ' ', TRIM(lastname))",
            "CONCAT(TRIM(firstname), ' ', TRIM(lastname))",
            "CONCAT(TRIM(lastname), ' ', TRIM(firstname))",
            "CONCAT(TRIM(firstname), ' ', TRIM(middlename), ' ', TRIM(lastname))"
        );
        $date_range = isset($post['date_range']) ? $post['date_range'] : null;
        $rowData = $this->getCAReportData($search, $limit, $offset, $sortBy, $sortOrder,$filterFields,$date_range );
        $total = $this->getCAReportDataCount($search,$filterFields,$date_range );
        $resultset["recordsTotal"] =  $total;
        $resultset["recordsFiltered"] =    $total;
        $resultset["data"] = isset($rowData) && $rowData ? $rowData: array();
        return $resultset;
    }

    private function getCAReportData($search, $limit, $offset, $sortBy, $sortOrder, $filterFields, $date_range) {
    $innerSql = $this->db->select("psloanpayments.*,ps.date_start,ps.date_end,ps.pay_date,ps.posted_by,ps.posted_at,d.firstname,d.middlename,d.lastname,
            CONCAT(TRIM(d.firstname),' ',TRIM(d.middlename),' ',TRIM(d.lastname)) AS fullname,
            c.reference, c.remarks, 
            c.amount AS loan_amount,
            SUM(psloanpayments.amount_due)
                OVER (
                    PARTITION BY psloanpayments.loan_id, ps.emp_id
                    ORDER BY ps.pay_date
                ) AS total_deducted,
    
            c.amount -
            SUM(psloanpayments.amount_due)
                OVER (
                    PARTITION BY psloanpayments.loan_id, ps.emp_id
                    ORDER BY ps.pay_date
                ) AS remaining_balance
        ", false)
        ->from("payroll.payroll_sheet_loan_payments psloanpayments")
        ->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id", "INNER")
        ->join("gcchris.loans c", "psloanpayments.loan_id = c.id", "LEFT")
        ->join("gccmaster.tblemployees d", "d.id = ps.emp_id", "LEFT")
        ->where("c.loan_id", 1)
        ->where("ps.posted", 1)
        ->where("ps.is_bonus", 0)
        ->get_compiled_select();
        $this->db->from("($innerSql) t", false);
        $startDate = $date_range['start'] ?? null;
        $endDate   = $date_range['end'] ?? null;
        if (!empty($startDate) && !empty($endDate)) {
            $this->db->where("t.pay_date >=", $startDate);
            $this->db->where("t.pay_date <=", $endDate);
        }
    
        if ($search) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key === 0) {
                    $this->db->like($field, $search, "both", false);
                } else {
                    $this->db->or_like($field, $search, "both", false);
                }
            }
            $this->db->group_end();
        }
    
        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }
    
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
    
        return $this->db->get()->result_array();
    }
    

    private function getCAReportDataCount($search,$filterFields,$date_range){
        $innerSql = $this->db->select("psloanpayments.*,ps.date_start,ps.date_end,ps.pay_date,ps.posted_by,ps.posted_at,d.firstname,d.middlename,d.lastname,
        CONCAT(TRIM(d.firstname),' ',TRIM(d.middlename),' ',TRIM(d.lastname)) AS fullname,
        c.reference,
        c.amount AS loan_amount,
        SUM(psloanpayments.amount_due)
            OVER (
                PARTITION BY psloanpayments.loan_id, ps.emp_id
                ORDER BY ps.pay_date
            ) AS total_deducted,

        c.amount -
        SUM(psloanpayments.amount_due)
            OVER (
                PARTITION BY psloanpayments.loan_id, ps.emp_id
                ORDER BY ps.pay_date
            ) AS remaining_balance
        ", false)
        ->from("payroll.payroll_sheet_loan_payments psloanpayments")
        ->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id", "INNER")
        ->join("gcchris.loans c", "psloanpayments.loan_id = c.id", "LEFT")
        ->join("gccmaster.tblemployees d", "d.id = ps.emp_id", "LEFT")
        ->where("c.loan_id", 1)
        ->where("ps.posted", 1)
        ->where("ps.is_bonus", 0)
        ->get_compiled_select();
        $this->db->from("($innerSql) t", false);
        $startDate = $date_range['start'] ?? null;
        $endDate   = $date_range['end'] ?? null;
        if (!empty($startDate) && !empty($endDate)) {
            $this->db->where("t.pay_date >=", $startDate);
            $this->db->where("t.pay_date <=", $endDate);
        }

        if ($search) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key === 0) {
                    $this->db->like($field, $search, "both", false);
                } else {
                    $this->db->or_like($field, $search, "both", false);
                }
            }
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function update_print_payrollsheet_netpay(){
        $result = array();
        $post = $this->input->post();
        $empId = $this->core_layout->getCurrentEmployeeId();

        if (isset($post) && $post) {
            $tempFilter = array();
            $tempFilter["is_bonus"] = isset($post['is_bonus']) ? intval($post['is_bonus']) : 0;
            $payrollGroup = isset($post["payroll_group"]) && $post["payroll_group"] ? strtoupper($post["payroll_group"]): null;
            $tempRange = "";
            $empIds = isset($post['serialized_employees']) && $post['serialized_employees'] ? explode(",", $post["serialized_employees"]) : $post['employees'];

            if(isset($post["group"]) && intval($post["group"]) === 1){
                $tempPayDate = date("Y-m-d", strtotime($post["pay_date"]));
                $tempFilter["pay_date"] = $tempPayDate;
                $tempRange = explode("-", $post["date_range"]);
                if(count($tempRange) === 2){
                    $tempDateStart = date("Y-m-d", strtotime($tempRange[0]));
                    $tempDateEnd = date("Y-m-d", strtotime($tempRange[1]));
                    $tempFilter["date_start"] = $tempDateStart;
                    $tempFilter["date_end"] = $tempDateEnd;
                }
            }else{
                if(isset($post['filter_month'], $post['filter_year']) && ($post['filter_month'] && $post['filter_year'])){
                    $tempMonth = date("F", strtotime("{$post['filter_year']}-{$post['filter_month']}-1"));
                    $tempFilter["month_name"] = strtolower($tempMonth);
                    $tempFilter["year"] = $post['filter_year'];
                }
                if(isset($post['is_bonus']) && $post['is_bonus']){
                    $tempFilter["is_bonus"] = $post['is_bonus'];
                }
            }
            if(isset($post['payroll_sched']) && $post['payroll_sched']){
                $tempFilter["payroll_sched"] = $post['payroll_sched'];
            }

            if(is_array($tempFilter) && count($tempFilter) > 0){
                $this->db->select("emp_id");
                $this->db->from($this->tbl_payroll_sheet);
                $this->db->where('printed_payslip', 0);
                
                foreach ($tempFilter as $key => $value) {
                    if ($key != 'date_start' || $key != 'date_end') {
                        $this->db->where("{$key}", $value); 
                    }
                }

                if(count((array)$tempRange) === 2){
                    $this->db->where("date_start >=", $tempDateStart); 
                    $this->db->where("date_end <=", $tempDateEnd); 
                }

                if(isset($post["company"]) && $post["company"]){ $this->db->where("company_id", $post["company"]);  }

                if (isset($empIds) && $empIds){
                    $this->db->where_in("emp_id", $empIds);
                }

                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $data = $query->result();
                    $ids = array_column($data, 'emp_id');

                    $_data = array(
                        'printed_payslip' => 1,
                        'printed_payslip_by' => $empId,
                        'printed_payslip_date' => date("Y-m-d H:i:s")
                    );

                    foreach ($tempFilter as $key => $value) {
                        if ($key != 'date_start' || $key != 'date_end') {
                            $this->db->where("{$key}", $value); 
                        }
                    }

                    if(count((array)$tempRange) === 2){
                        $this->db->where("date_start >=", $tempDateStart); 
                        $this->db->where("date_end <=", $tempDateEnd); 
                    }

                    if(isset($post["company"]) && $post["company"]){ $this->db->where("company_id", $post["company"]);  }

                    $this->db->where_in("emp_id", $ids);
                    $this->db->where("printed_payslip", 0);
                    $q = $this->db->update($this->tbl_payroll_sheet, $_data);

                    $empName = $this->getDisplayName($empId);
                    $_result = implode(', ', array_map(
                        function ($key) use ($tempFilter) {
                            $value = $tempFilter[$key];
                            return is_string($value)
                                ? "$key => '$value'"
                                : "$key => $value";
                        },
                        array_keys($tempFilter)
                    ));

                    if ($q) {
                        $logMessage = "Payrollsheet records printed by `$$empName` in netPay Summary with parameters of company id `".$post["company"]."`, `$_result`";
                        $this->core_layout->setEventLog($logMessage, "print", "success", "payroll");
                    } else {
                        $logMessage = "Failed to print Payrollsheet records in netPay Summary with parameters of company id `".$post["company"]."`, `$_result`";
                        $this->core_layout->setEventLog($logMessage, "print", "error", "payroll");
                    }
                }
            }
        }

        return true;
    }

    function getDisplayName($id){
        $this->db->select("firstname, middlename, lastname, suffix");
        $this->db->from("gccmaster.tblemployees");
        $this->db->where("id", $id);
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

    public function generateCustomOvertimeSummary(){
        $post = $this->input->post();
        $search = $post['search']['value'] ?? false;
        $group = $post['group'] ?? 1;
        $filter_month = $post['filter_month'] ?? 0;
        $filter_year = $post['filter_year'] ?? 0;
        $company = $post['company'] ?? 0;
        $filteredIds = $post['employee'] ?? [];
        $coverageDate = $post['date_range'] ?? false;
        $payrollGroup = isset($post["payroll_group"]) && $post["payroll_group"] ? $post["payroll_group"]: array();

        $tempGroup = "PAY DATE";
        $arrGroup = array(1=>"PAY DATE", 2=>"MONTH", 3=>"YEAR");
        $arrCompany = array();
        $employeeIds = array();
        $tempStartDate = null;
        $tempEndDate = null;
        $hasDataFilter = false;
        $tempGroup = $arrGroup[$group];
        $tempArrFilter = array();
        $tempArrFilter["filter_by"] = $tempGroup;

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

        $tempCompRow = array();
        if(isset($company) && $company){
            $this->db->from("gcchris.tblcompanies");
            $this->db->where("id", $company);
            $qTemp = $this->db->get();
            if($qTemp->num_rows() == 1){ $tempCompRow = $qTemp->row_array(); }
        }

        $filterPayrollGroup = null;
        if(is_array($payrollGroup) && count($payrollGroup) > 0){
            $this->db->select("GROUP_CONCAT(DISTINCT TRIM(UPPER(description))) as payroll_group");
            $this->db->from("payroll.payroll_group");
            $this->db->where_in("id", $payrollGroup);
            $qPG = $this->db->get();

            $filterPayrollGroup = $qPG->row()->payroll_group;
        }

        $isDateRange = isset($post["date_range"]) && $post["date_range"];
        $isFilterMonth = isset($post["filter_month"]) && $post["filter_month"];

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
        }

        if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
            $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
            $date = new DateTime($tempStartDate);
            $date->modify('last day of this month');
            $tempEndDate = $date->format('Y-m-d');
        }elseif (isset($post["filter_year"]) && $post["filter_year"]){
            $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
            $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
        }

        if(isset($post["pay_date"]) && $post["pay_date"]){
            $tempStartDate = date("Y-m-d", strtotime($post["pay_date"]));
            $tempEndDate = date("Y-m-d", strtotime($post["pay_date"]));
        }

        $xDateFrom = date("F d, Y", strtotime($tempStartDate));
        $xDateTo = date("F d, Y", strtotime($tempEndDate));
        $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");

        if($isDateRange === false && $isFilterMonth){ $tempArrFilter["month"] = strtoupper(date("Y F", strtotime("{$post["filter_year"]}-{$post["filter_month"]}"))); }
        $tempArrFilter["company_description"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? strtoupper(trim($tempCompRow['description'])): "";
        $tempArrFilter["company_address"] = isset($tempCompRow['company_address']) && $tempCompRow['company_address']  ? strtoupper(trim($tempCompRow['company_address'])): "";
        $tempArrFilter["has_comp_desc"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? true: false;
        $tempArrFilter["payroll_group"] = $filterPayrollGroup;

        $results = $this->customOvertimeSummaryList($filteredIds, $filter_month, $filter_year, $company, $tempArrFilter["coverage_date"]);

        return [
            'data' => $results['data'] ?? [],
            'filters' => $tempArrFilter ?? [],
        ];
    }

    protected function customOvertimeSummaryList($filteredIds, $filter_month, $filter_year, $company, $coverageDate){
        $data = array();
        $tempData = array();

        $merged = $this->chunk_ot_records($filteredIds, $filter_month, $filter_year, $company, $coverageDate);

        if (count($merged) > 0) {
            $tempData = $this->computeBulkOvertime($merged, $coverageDate);

            if (!empty($tempData)){
                $data['data'] = $tempData;
            }
        }

        return $data;
    }

    protected function check_ot_paid($date, $timesheetId){
        $isPaid = 0;

        if ($timesheetId) {
            $date = date("Y-m-d", strtotime($date));

            $this->db->select("a.id, b.emp_id");
            $this->db->from($this->tbl_timesheet_overtime.' as a');
            $this->db->join($this->tbl_timesheet.' as b', 'b.id = a.timesheet_id', 'left');
            $this->db->where('DATE(a.overtime_in)', $date);
            $this->db->where("a.timesheet_id", $timesheetId);
            $this->db->where('b.verified', 1);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
            
                $row = $query->row();

                $this->db->select('daily');
                $this->db->from($this->tbl_payroll_sheet);
                $this->db->where('emp_id', $row->emp_id);
                
                $this->db->group_start();
                    $this->db->where('DATE(date_start) <= ', date('Y-m-d', strtotime($date)));
                    $this->db->where('DATE(date_end) >=', date('Y-m-d', strtotime($date)));
                $this->db->group_end();

                $this->db->where('posted', 1);
                $this->db->where('is_bonus', 0);

                $_q = $this->db->get();
                
                if ($_q->num_rows() > 0) {
                    $isPaid = 1;
                }

            }
        }

        return $isPaid;
    }

    protected function getPayrollSheetBasicRate($date, $id){
        $basicRate = 0;

        if ($id) {
            $this->db->select('daily');
            $this->db->from($this->tbl_payroll_sheet);
            $this->db->where('emp_id', $id);
            
            $this->db->group_start();
                $this->db->where('DATE(date_start) <= ', date('Y-m-d', strtotime($date)));
                $this->db->where('DATE(date_end) >=', date('Y-m-d', strtotime($date)));
            $this->db->group_end();

            $this->db->where('posted', 1);
            $this->db->where('is_bonus', 0);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $row = $query->row();

                $basicRate = $row->daily;
            }
        }

        return $basicRate;
    }

    protected function getPayrollSheetAllowance($date, $id){
        $allowance = 0;

        if ($id) {
            $this->db->select('total_allowances, no_of_days');
            $this->db->from($this->tbl_payroll_sheet);
            $this->db->where('emp_id', $id);
            
            $this->db->group_start();
                $this->db->where('DATE(date_start) <= ', date('Y-m-d', strtotime($date)));
                $this->db->where('DATE(date_end) >=', date('Y-m-d', strtotime($date)));
            $this->db->group_end();

            $this->db->where('posted', 1);
            $this->db->where('is_bonus', 0);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $row = $query->row();

                if ($row->total_allowances > 0) {
                    $allowance = $row->total_allowances / $row->no_of_days;
                } else {
                    $allowance = 0;
                }
            }
        }

        return $allowance;
    }

    protected function chunk_ot_records($filteredIds, $filter_month, $filter_year, $company, $coverageDate){
        $tempData = array();

        $startDate = null;
        $endDate = null;

        $lastId = 0;
        $chunkSize = 100;

        do {
            if ($filter_month && $filter_year) {
                $startDate = date("Y-m-d", strtotime("first day of $filter_year-$filter_month"));
                $endDate = date("Y-m-t", strtotime($startDate));
            }

            if (!$filter_month && $filter_year) {
                $startDate = date("Y-m-d", strtotime("first day of January $filter_year"));
                $endDate = date('Y-m-d', strtotime("last day of December $filter_year"));
            }

            if ($coverageDate) {
                $dateRange = explode("-", $coverageDate);
                $startDate = date("Y-m-d", strtotime(trim($dateRange[0])));
                $endDate = date("Y-m-d", strtotime(trim($dateRange[1])));
            }

            $select = "a.id, a.reference_no, a.status, a.created_at, b.id as emp_id, a.date_from, a.date_to, a.employee, b.firstname, b.middlename, b.lastname, b.suffix";
            $this->db->select($select);
            $this->db->from("gcceforms.overtime a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");

            if ($lastId) {
                $this->db->where('a.id >', $lastId);
            }

            if (is_array($filteredIds) && count($filteredIds) > 0) {
                $this->db->where_in('a.employee', $filteredIds);
            }

            $this->db->where('b.company_id', $company);

            $this->db->group_start();
                $this->db->where('a.date_from >=', $startDate . ' 00:00:00');
                $this->db->where('a.date_from <=', $endDate . ' 23:59:59');
            $this->db->group_end();

            $this->db->where('status !=', 'Cancelled');

            $this->db->order_by('a.id', 'asc');
            $this->db->limit($chunkSize);

            $query = $this->db->get();
            $rows = $query->result();
            $rowsA = $query->result_array();

            if (!$rows) {
                break;
            }

            $lastArray = end($rowsA);
            if (!empty($lastArray)) {
                $lastId = $lastArray['id'];
            }

            $tempData[] = $rows;
        } while(count($rows) === $chunkSize);

        $merged = array_merge(...$tempData);
        
        usort($merged, function ($a, $b) {
            return [$a->lastname, $a->firstname, $a->date_from]
                <=> [$b->lastname, $b->firstname, $b->date_from];
        });

        return $merged;
    }

    protected function computeBulkOvertime(array $tempData, string $coverageDate) {
        if (empty($tempData)) {
            return [];
        }

        $empIds = [];
        $dates  = [];

        foreach ($tempData as $item) {
            if (empty($item->emp_id) || empty($item->date_from)) {
                continue;
            }

            $empIds[] = $item->emp_id;
            $dates[]  = date('Y-m-d', strtotime($item->date_from));
        }

        $empIds = array_unique($empIds);
        $dates  = array_unique($dates);

        $this->db->select(" a.id, a.emp_id, DATE(a.date) as tsDate, a.total_accredited_ot_hrs as ot_hrs, a.total_accredited_ndiff_ot_hrs as ot_ndiff_hrs, IF(DATE(a.overtime_in) != '0000-00-00', DATE(a.overtime_in), DATE(a.date)) as overtime_in, a.has_shift, a.payrate_id, IF(a.is_holiday = 1 AND a.paid_holiday = 1, 1, 0) as is_paid_holiday");
        $this->db->from('gcctimeutility.timesheet a');
        $this->db->where_in('a.emp_id', $empIds);
        $this->db->where_in('DATE(a.date)', $dates);
        $this->db->where('a.has_overtime', 1);
        $this->db->where('verified', 1);
        $this->db->group_start()
            ->where('a.total_accredited_ot_hrs >', 0)
            ->or_where('a.total_accredited_ndiff_ot_hrs >', 0)
        ->group_end();

        // $this->db->group_start();
        //     $this->db->where('DATE(b.date_start) <= ', date('Y-m-d', strtotime($date)));
        //     $this->db->where('DATE(b.date_end) >=', date('Y-m-d', strtotime($date)));
        // $this->db->group_end();

        // $this->db->where('b.is_bonus', 0);

        $rows = $this->db->get()->result();

        //here

        $tsMap = [];
        foreach ($rows as $r) {
            $key = $r->emp_id . '_' . $r->tsDate;
            $tsMap[$key] = $r;
        }

        $basicRateCache = [];
        $allowanceCache = [];
        $payrateCache   = [];
        $otAdjCache     = [];
        $otPaidCache    = [];

        foreach ($tempData as $item) {

            $tempRs = (array) $item;
            $tempDisplay = (object) $this->core_layout->getDisplayName($tempRs);
            $tempName = (isset($tempDisplay->display_name_0) && $tempDisplay->display_name_0)? strtoupper($tempDisplay->display_name_0): strtoupper("no display name");
            $item->employee_name = $tempName;

            $basicRate = 0;
            $otRate = 1;
            $tempOtRate = 0;
            $tempOtPayWithRate = 0;
            $totalOtHrs = 0;
            $totalOtPay = 0;
            $totalOtAllowance = 0;
            $nightDiffPay = 0;
            $perMinute = 0;
            $ot_ndiff_hrs = 0;
            $has_shift = 0;
            $overtime_in = $item->date_from;
            $day = date('D', strtotime($overtime_in));
            $tempPayrateSetting = [];
            $otAllowance = 0;

            $dateKey = date('Y-m-d', strtotime($item->date_from));
            $mapKey  = $item->emp_id . '_' . $dateKey;

            if (!isset($tsMap[$mapKey])) {

                $item->_ot_rate = $tempOtRate;
                $item->_temp_ot_pay = $tempOtPayWithRate;
                $item->day = $day;
                $item->daily_rate = $basicRate;
                $item->has_shift = $has_shift;
                $item->pay_info = $tempPayrateSetting;
                $item->per_minute = $perMinute;
                $item->ot_hrs = $totalOtHrs > 0 ? $totalOtHrs : 0;
                $item->allowance = $otAllowance > 0 ? $otAllowance : 0;
                $item->ot_rate = $otRate;
                $item->ot_allowance = $totalOtAllowance ?: 0;
                $item->ot_pay = $totalOtPay;
                $item->ot_pay_20 = ($otRate > 1 && $tempOtRate == 25) ? $tempOtPayWithRate : 0;
                $item->ot_pay_30 = ($otRate > 1 && $tempOtRate == 30) ? $tempOtPayWithRate : 0;
                $item->ot_ndiff_hrs = $ot_ndiff_hrs ?: 0;
                $item->night_diff = $nightDiffPay ?: 0;
                $item->ot_adj = 0;
                $item->amount = 0;
                $item->total_pay = $item->amount;
                $item->is_paid = 0;
                $item->overtime_in = $overtime_in;

                $overtime_in = $item->date_from;
                $day = date('D', strtotime($overtime_in));
                
                continue;
            }

            $ts = $tsMap[$mapKey];

            $has_shift = (int) $ts->has_shift;
            $overtime_in = $ts->overtime_in ? $ts->overtime_in : $item->date_from;
            $day = date('D', strtotime($overtime_in));

            if (!isset($basicRateCache[$mapKey])) {
                $basicRateCache[$mapKey] = $this->getPayrollSheetBasicRate($overtime_in, $item->emp_id);
                $allowanceCache[$mapKey] = $this->getPayrollSheetAllowance($overtime_in, $item->emp_id);
            }

            $basicRate = (float) $basicRateCache[$mapKey];
            $otAllowance = (float) $allowanceCache[$mapKey];

            $payrateKey = $ts->payrate_id ?: ($ts->has_shift ? 'regular' : 'rest day');

            if (!isset($payrateCache[$payrateKey])) {
                $payrateCache[$payrateKey] = $ts->payrate_id
                    ? $this->getPayrateSettingById($ts->payrate_id)
                    : $this->getPayrateSetting($payrateKey);
            }

            $tempPayrateSetting = $payrateCache[$payrateKey];

            $totalOtHrs = $ts->ot_hrs + $ts->ot_ndiff_hrs;
            $ot_ndiff_hrs = $ts->ot_ndiff_hrs;

            $otRate = max((float) $tempPayrateSetting->ot_rate, 1);
            $otNightDiffRate = (float) $tempPayrateSetting->ot_night_diff_rate;

            $tempOtRate = ($otRate * 100) - 100;
            $perMinute = $basicRate / 8;

            $totalOtPay = $perMinute * $totalOtHrs;
            $tempOtPayWithRate = $otRate > 1 ? ($tempOtRate / 100) * $totalOtPay : 0;

            $nightDiffPay = $otNightDiffRate > 0
                ? ($perMinute * $ot_ndiff_hrs) * $otNightDiffRate
                : 0;

            $allowPaidAllowance =
                $tempPayrateSetting->particulars !== 'regular'
                || !$ts->has_shift
                || $tempPayrateSetting->is_holiday;

            if ((int) $tempPayrateSetting->is_holiday === 1) {
                $allowPaidAllowance = (int) $ts->is_paid_holiday === 1;
            }

            $totalOtAllowance = $allowPaidAllowance
                ? ($otAllowance / 8) * $totalOtHrs
                : 0;

            if (!isset($otAdjCache[$item->emp_id])) {
                $otAdjCache[$item->emp_id] = $this->getOTAdjustment($coverageDate, $item->emp_id);
            }

            if (!isset($otPaidCache[$ts->id])) {
                $otPaidCache[$ts->id] = $this->check_ot_paid($overtime_in, $ts->id);
            }

            $totalOtPayable = $totalOtPay + $tempOtPayWithRate;

            $item->_ot_rate = $tempOtRate;
            $item->_temp_ot_pay = $tempOtPayWithRate;
            $item->day = $day;
            $item->daily_rate = $basicRate;
            $item->has_shift = $has_shift;
            $item->pay_info = $tempPayrateSetting;
            $item->per_minute = $perMinute;
            $item->ot_hrs = $totalOtHrs > 0 ? $totalOtHrs : 0;
            $item->allowance = $otAllowance > 0 ? $otAllowance : 0;
            $item->ot_rate = $otRate;
            $item->ot_allowance = $totalOtAllowance ?: 0;
            $item->ot_pay = $totalOtPay;
            $item->ot_pay_20 = ($otRate > 1 && $tempOtRate == 25) ? $tempOtPayWithRate : 0;
            $item->ot_pay_30 = ($otRate > 1 && $tempOtRate == 30) ? $tempOtPayWithRate : 0;
            $item->ot_ndiff_hrs = $ot_ndiff_hrs ?: 0;
            $item->night_diff = $nightDiffPay ?: 0;
            $item->ot_adj = $otAdjCache[$item->emp_id];
            $item->amount = $totalOtPayable + $nightDiffPay + $totalOtAllowance;
            $item->total_pay = $item->amount;
            $item->is_paid = $otPaidCache[$ts->id];
            $item->overtime_in = $overtime_in;
        }

        return $tempData;
    }

    function generateNightDiffSummary(){
        $post = $this->input->post();
        $search = $post['search']['value'] ?? false;
        $group = $post['group'] ?? 1;
        $filter_month = $post['filter_month'] ?? 0;
        $filter_year = $post['filter_year'] ?? 0;
        $company = $post['company'] ?? 0;
        $filteredIds = $post['employee'] ?? [];
        $coverageDate = $post['date_range'] ?? false;
        $payrollGroup = isset($post["payroll_group"]) && $post["payroll_group"] ? $post["payroll_group"]: array();

        $tempGroup = "PAY DATE";
        $arrGroup = array(1=>"PAY DATE", 2=>"MONTH", 3=>"YEAR");
        $arrCompany = array();
        $employeeIds = array();
        $tempStartDate = null;
        $tempEndDate = null;
        $hasDataFilter = false;
        $tempGroup = $arrGroup[$group];
        $tempArrFilter = array();
        $tempArrFilter["filter_by"] = $tempGroup;

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

        $tempCompRow = array();
        if(isset($company) && $company){
            $this->db->from("gcchris.tblcompanies");
            $this->db->where("id", $company);
            $qTemp = $this->db->get();
            if($qTemp->num_rows() == 1){ $tempCompRow = $qTemp->row_array(); }
        }

        $filterPayrollGroup = null;
        if(is_array($payrollGroup) && count($payrollGroup) > 0){
            $this->db->select("GROUP_CONCAT(DISTINCT TRIM(UPPER(description))) as payroll_group");
            $this->db->from("payroll.payroll_group");
            $this->db->where_in("id", $payrollGroup);
            $qPG = $this->db->get();

            $filterPayrollGroup = $qPG->row()->payroll_group;
        }

        $isDateRange = isset($post["date_range"]) && $post["date_range"];
        $isFilterMonth = isset($post["filter_month"]) && $post["filter_month"];

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
        }

        if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
            $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
            $date = new DateTime($tempStartDate);
            $date->modify('last day of this month');
            $tempEndDate = $date->format('Y-m-d');
        }elseif (isset($post["filter_year"]) && $post["filter_year"]){
            $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
            $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
        }

        if(isset($post["pay_date"]) && $post["pay_date"]){
            $tempStartDate = date("Y-m-d", strtotime($post["pay_date"]));
            $tempEndDate = date("Y-m-d", strtotime($post["pay_date"]));
        }

        $xDateFrom = date("F d, Y", strtotime($tempStartDate));
        $xDateTo = date("F d, Y", strtotime($tempEndDate));
        $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");

        if($isDateRange === false && $isFilterMonth){ $tempArrFilter["month"] = strtoupper(date("Y F", strtotime("{$post["filter_year"]}-{$post["filter_month"]}"))); }
        $tempArrFilter["company_description"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? strtoupper(trim($tempCompRow['description'])): "";
        $tempArrFilter["company_address"] = isset($tempCompRow['company_address']) && $tempCompRow['company_address']  ? strtoupper(trim($tempCompRow['company_address'])): "";
        $tempArrFilter["has_comp_desc"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? true: false;
        $tempArrFilter["payroll_group"] = $filterPayrollGroup;

        $results = $this->nightdiffSummaryList($filteredIds, $filter_month, $filter_year, $company, $tempArrFilter["coverage_date"]);

        return [
            'data' => $results['data'] ?? [],
            'filters' => $tempArrFilter ?? [],
        ];
    }

    protected function nightdiffSummaryList($filteredIds, $filter_month, $filter_year, $company, $coverageDate) {
        $data = array();
        $tempData = array();
        $minutes_per_day = 0;

        $merged = $this->chunk_ndiff_records($filteredIds, $filter_month, $filter_year, $company, $coverageDate);

        if (count($merged) > 0) {
            $settings = $this->getSettings();
            $minutes_per_day = $settings->minutes_in_a_day->setting_value;

            foreach ($merged as $item) {
                $payrateTemp = intval($item->has_shift) === 1 ? "regular" : "rest day";
                $payrateSetting = $this->getPayrateSetting($payrateTemp);
                $tempPayrateSetting = intval($item->payrate_id) > 0 ? $this->getPayrateSettingById($item->payrate_id) : $payrateSetting;

                $dailyRate = 0;
                $basicRate = 0;
                $perMinute = 0;
                $regularNdiffPay = 0;
                $nightDiffPay = 0;
                $totalHrs = 0;
                $amount = 0;
                $isPosted = $item->posted ? $item->posted : 0;
                $night_diff_minutely = 0;

                $tempRs = (array) $item;

                $tempDisplay = (object) $this->core_layout->getDisplayName($tempRs);
                $tempName = (isset($tempDisplay->display_name_0) && $tempDisplay->display_name_0)? strtoupper($tempDisplay->display_name_0): strtoupper("no display name");
                $item->employee_name = $tempName;
                $item->day = date('D', strtotime($item->date));

                $NightDiffRate = floatval($tempPayrateSetting->night_diff_rate) > 0 ? floatval($tempPayrateSetting->night_diff_rate): 0.1;
                $dailyRate = $item->daily ? $item->daily : ($item->basic_rate * 12) / $item->work_days;
                //$totalHrs = $item->total_ndiff_rendered && $item->total_ndiff_rendered > 0 ? intdiv($item->total_ndiff_rendered, 60) : 0;
                $ndiffMinutes = $item->total_ndiff_rendered && $item->total_ndiff_rendered > 0 ? $item->total_ndiff_rendered : 0;
                $totalHrs = $item->total_ndiff_rendered && $item->total_ndiff_rendered > 0 ? floatval($item->total_ndiff_rendered) / 60 : 0;
                $totalHrs = round($totalHrs, 2);
                
                if ($item->daily) {
                    $nightDiffPay = ($item->basic_rate / 8) * $NightDiffRate;
                    $amount = $nightDiffPay * $totalHrs;
                }

                $perMinute = floatval($dailyRate) / floatval($minutes_per_day);
                $ndiffPerMinute = $perMinute * $NightDiffRate;
                $ndiffAmount = floatval($ndiffPerMinute) * floatval($ndiffMinutes);
                $amount = $ndiffAmount;

                $item->daily_rate = $dailyRate;
                $item->per_minute = $perMinute;
                $item->ndiff_hrs = $totalHrs;
                $item->night_diff = $ndiffPerMinute * 60; // minutes to hrs
                // $item->night_diff = $nightDiffPay;
                $item->per_minute = $night_diff_minutely;
                $item->posted = $isPosted;
                $item->amount = $amount;

                $tempData[] = $item;
            }

            if (!empty($tempData)){
                $data['data'] = $tempData;
            }
        }

        return $data;
    }

    protected function chunk_ndiff_records($filteredIds, $filter_month, $filter_year, $company, $coverageDate){
        $tempData = array();

        $startDate = null;
        $endDate = null;

        $lastId = 0;
        $chunkSize = 100;

        do {
            if ($filter_month && $filter_year) {
                $startDate = date("Y-m-d", strtotime("first day of $filter_year-$filter_month"));
                $endDate = date("Y-m-t", strtotime($startDate));
            }

            if (!$filter_month && $filter_year) {
                $startDate = date("Y-m-d", strtotime("first day of January $filter_year"));
                $endDate = date('Y-m-d', strtotime("last day of December $filter_year"));
            }

            if ($coverageDate) {
                $dateRange = explode("-", $coverageDate);
                $startDate = date("Y-m-d", strtotime(trim($dateRange[0])));
                $endDate = date("Y-m-d", strtotime(trim($dateRange[1])));
            }

            $select = 'a.id, b.id as emp_id, a.date, a.emp_id, a.payrate_id, a.has_shift, b.firstname, b.middlename, b.lastname, b.suffix, a.verified, a.total_ndiff_rendered, c.id as payroll_id, c.date_start as payroll_start, c.date_end as payroll_end, c.posted, c.id as payroll_id, c.total_ndiff_minutes, c.total_ndiff_amount as amount, IFNULL(c.rate, b.basic_rate) as basic_rate, c.daily, ROUND(IFNULL(d.work_days_in_year, 314), 2) as work_days';
            $this->db->select($select);
            $this->db->join("gccmaster.tblemployees b", "a.emp_id = b.id", "LEFT");
            $this->db->join('payroll.payroll_sheet c', 'c.emp_id = b.id AND (DATE(c.date_start) <= DATE(a.date) AND DATE(c.date_end) >= DATE(a.date))', 'LEFT');
            $this->db->join('gcchris.tblcompanies d', 'd.id = b.company_id', 'LEFT');
            $this->db->where('a.total_ndiff_rendered > ', 0);
            $this->db->where('a.verified', 1);
            $this->db->where('c.is_bonus', 0);
            $this->db->from('gcctimeutility.timesheet a');

            if ($lastId) {
                $this->db->where('a.id >', $lastId);
            }

            if (is_array($filteredIds) && count($filteredIds) > 0) {
                $this->db->where_in('a.emp_id', $filteredIds); 
            }

            $this->db->where('b.company_id', $company);

            $this->db->group_start();
                $this->db->where('DATE(a.date) >=', $startDate);
                $this->db->where('DATE(a.date) <=', $endDate);
            $this->db->group_end();

            $this->db->order_by('a.id', 'asc');
            $this->db->limit($chunkSize);

            $query = $this->db->get();
            $rows = $query->result();
            $rowsA = $query->result_array();

            if (!$rows) {
                break;
            }

            $lastArray = end($rowsA);
            if (!empty($lastArray)) {
                $lastId = $lastArray['id'];
            }

            $tempData[] = $rows;
        } while(count($rows) === $chunkSize);

        $merged = array_merge(...$tempData);

        usort($merged, function ($a, $b) {
            return [$a->lastname, $a->firstname, $a->date]
                <=> [$b->lastname, $b->firstname, $b->date];
        });

        return $merged;
    }

    public function generate_payrollsheet_summary() {
        $post = $this->input->post();
        $search = $post['search']['value'] ?? false;
        $group = $post['group'] ?? 1;
        $filter_month = $post['filter_month'] ?? 0;
        $filter_year = $post['filter_year'] ?? 0;
        $company = $post['company'] ?? 0;
        $filteredIds = $post['employee'] ?? [];
        $coverageDate = $post['date_range'] ?? false;
        $payrollGroup = isset($post["payroll_group"]) && $post["payroll_group"] ? $post["payroll_group"]: array();

        $tempGroup = "PAY DATE";
        $arrGroup = array(1=>"PAY DATE", 2=>"MONTH", 3=>"YEAR");
        $arrCompany = array();
        $employeeIds = array();
        $tempStartDate = null;
        $tempEndDate = null;
        $hasDataFilter = false;
        $tempGroup = $arrGroup[$group];
        $tempArrFilter = array();
        $tempArrFilter["filter_by"] = $tempGroup;

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

        $tempCompRow = array();
        if(isset($company) && $company){
            $this->db->from("gcchris.tblcompanies");
            $this->db->where("id", $company);
            $qTemp = $this->db->get();
            if($qTemp->num_rows() == 1){ $tempCompRow = $qTemp->row_array(); }
        }

        $filterPayrollGroup = null;
        if(is_array($payrollGroup) && count($payrollGroup) > 0){
            $this->db->select("GROUP_CONCAT(DISTINCT TRIM(UPPER(description))) as payroll_group");
            $this->db->from("payroll.payroll_group");
            $this->db->where_in("id", $payrollGroup);
            $qPG = $this->db->get();

            $filterPayrollGroup = $qPG->row()->payroll_group;
        }

        $isDateRange = isset($post["date_range"]) && $post["date_range"];
        $isFilterMonth = isset($post["filter_month"]) && $post["filter_month"];

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
        }

        if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
            $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
            $date = new DateTime($tempStartDate);
            $date->modify('last day of this month');
            $tempEndDate = $date->format('Y-m-d');
        }elseif (isset($post["filter_year"]) && $post["filter_year"]){
            $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
            $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
        }

        if(isset($post["pay_date"]) && $post["pay_date"]){
            $tempStartDate = date("Y-m-d", strtotime($post["pay_date"]));
            $tempEndDate = date("Y-m-d", strtotime($post["pay_date"]));
        }

        $xDateFrom = date("F d, Y", strtotime($tempStartDate));
        $xDateTo = date("F d, Y", strtotime($tempEndDate));
        $tempArrFilter["coverage_date"] = strtoupper("{$xDateFrom} - {$xDateTo}");

        if($isDateRange === false && $isFilterMonth){ $tempArrFilter["month"] = strtoupper(date("Y F", strtotime("{$post["filter_year"]}-{$post["filter_month"]}"))); }
        $tempArrFilter["company_description"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? strtoupper(trim($tempCompRow['description'])): "";
        $tempArrFilter["company_address"] = isset($tempCompRow['company_address']) && $tempCompRow['company_address']  ? strtoupper(trim($tempCompRow['company_address'])): "";
        $tempArrFilter["has_comp_desc"] = isset($tempCompRow['description']) && $tempCompRow['description'] ? true: false;
        $tempArrFilter["payroll_group"] = $filterPayrollGroup;

        $results = $this->payrollsheetSummaryList($filteredIds, $filter_month, $filter_year, $company, $tempArrFilter["coverage_date"]);

        return [
            'data' => $results['data'] ?? [],
            'filters' => $tempArrFilter ?? [],
        ];
    }

    public function payrollsheetSummaryList($filteredIds, $filter_month, $filter_year, $company, $coverageDate){
        $data = array();
        $tempData = array();

        $merged = $this->chunck_payrollsheet_records($filteredIds, $filter_month, $filter_year, $company, $coverageDate);

        if (count($merged) > 0) {
            $settings = $this->getSettings();

            foreach ($merged as $item) {
                $dailyRate = 0;
                $hourlyRate = 0;
                $total_no_of_hrs_worked = 0;
                $regular_total_no_of_hrs_worked = 0;
                $overtime_total_no_of_hrs_worked = 0;
                $ot_rate = 0;
                $ot_pay_25 = 0;
                $regular_nd_hrs_worked = 0;
                $ot_ndiff_hrs_worked = 0;
                $regular_night_diff = 0;
                $ot_night_diff = 0;
                $amount_paid = 0;
                $bonus_13th = 0;
                $total_pay = 0;

                $payrateTemp = intval($item->has_shift) === 1 ? "regular" : "rest day";
                $payrateSetting = $this->getPayrateSetting($payrateTemp);
                $tempPayrateSetting = intval($item->payrate_id) > 0 ? $this->getPayrateSettingById($item->payrate_id) : $payrateSetting;
                $NightDiffRate = floatval($tempPayrateSetting->night_diff_rate) > 0 ? floatval($tempPayrateSetting->night_diff_rate): 0.1;
                $otRate = floatval($tempPayrateSetting->ot_rate) > 0 ? floatval($tempPayrateSetting->ot_rate): 1;
                $otNightDiffRate = floatval($tempPayrateSetting->ot_night_diff_rate) > 0 ? floatval($tempPayrateSetting->ot_night_diff_rate) : 0;
                $regular_nd_hrs_worked = round(floatval($item->total_ndiff_rendered / 60), 2);

                $tempOtRate = ($otRate * 100) - 100;
                $tempRs = (array) $item;

                $tempDisplay = (object) $this->core_layout->getDisplayName($tempRs);
                $tempName = (isset($tempDisplay->display_name_0) && $tempDisplay->display_name_0)? strtoupper($tempDisplay->display_name_0): strtoupper("no display name");
                $item->employee_name = $tempName;
                $item->day = date('D', strtotime($item->date));

                $dailyRate = $item->daily;
                $regular_total_no_of_hrs_worked = round(floatval($item->total_time_rendered / 60), 2);
                $hourlyRate = floatval($item->daily) / 8;
                $overtime_total_no_of_hrs_worked = $item->ot_hrs;
                $total_no_of_hrs_worked = round(floatval($regular_total_no_of_hrs_worked) + floatval($overtime_total_no_of_hrs_worked), 2);
                $ot_rate = floatval($hourlyRate) * $otRate;

                $ot_pay_25 = floatval($hourlyRate) * floatval($overtime_total_no_of_hrs_worked) * floatval($otRate);
                $ot_ndiff_hrs_worked = $item->ot_ndiff_hrs;

                $regular_night_diff = $hourlyRate * $NightDiffRate * $regular_nd_hrs_worked;
                $ot_night_diff = $ot_rate * $otNightDiffRate * $ot_ndiff_hrs_worked;
                $ot_night_diff = ($ot_rate * $ot_ndiff_hrs_worked) + $ot_night_diff;
                $amount_paid = ($hourlyRate * $regular_total_no_of_hrs_worked) + $ot_pay_25 + $regular_night_diff + $ot_night_diff;
                
                $overtime_total_no_of_hrs_worked = round($overtime_total_no_of_hrs_worked, 2);
                $hourlyRate = round($hourlyRate, 2);
                $ot_rate = round($ot_rate, 2);
                $ot_pay_25 = round($ot_pay_25, 2);
                $regular_night_diff = round($regular_night_diff, 2);
                $ot_night_diff = round($ot_night_diff, 2);
                $amount_paid = round($amount_paid, 2);

                $item->daily_rate = $dailyRate;
                $item->hourly_rate = $hourlyRate;
                $item->total_no_of_hrs_worked = $total_no_of_hrs_worked;
                $item->regular_total_no_of_hrs_worked = $regular_total_no_of_hrs_worked;
                $item->overtime_total_no_of_hrs_worked = $overtime_total_no_of_hrs_worked;
                $item->ot_rate = $ot_rate;
                $item->ot_pay_25 = $ot_pay_25;
                $item->regular_nd_hrs_worked = $regular_nd_hrs_worked;
                $item->ot_ndiff_hrs_worked = $ot_ndiff_hrs_worked;
                $item->regular_night_diff = $regular_night_diff;
                $item->ot_night_diff = $ot_night_diff;
                $item->amount_paid = $amount_paid;
                $item->bonus_13th = $bonus_13th;
                $item->total_pay = $amount_paid;

                $tempData[] = $item;
            }

            if (!empty($tempData)){
                $data['data'] = $tempData;
            }
        }

        return $data;
    }

    public function chunck_payrollsheet_records($filteredIds, $filter_month, $filter_year, $company, $coverageDate){
        $tempData = array();

        $startDate = null;
        $endDate = null;

        $lastId = 0;
        $chunkSize = 100;

        do {
            if ($filter_month && $filter_year) {
                $startDate = date("Y-m-d", strtotime("first day of $filter_year-$filter_month"));
                $endDate = date("Y-m-t", strtotime($startDate));
            }

            if (!$filter_month && $filter_year) {
                $startDate = date("Y-m-d", strtotime("first day of January $filter_year"));
                $endDate = date('Y-m-d', strtotime("last day of December $filter_year"));
            }

            if ($coverageDate) {
                $dateRange = explode("-", $coverageDate);
                $startDate = date("Y-m-d", strtotime(trim($dateRange[0])));
                $endDate = date("Y-m-d", strtotime(trim($dateRange[1])));
            }

            $select = 'a.id, b.id as emp_id, a.date, a.emp_id, a.payrate_id, a.has_shift, b.firstname, b.middlename, b.lastname, b.suffix, a.verified, a.total_ndiff_rendered, a.total_time_rendered, c.id as payroll_id, c.date_start as payroll_start, c.date_end as payroll_end, c.posted, c.id as payroll_id, c.total_ndiff_minutes, IFNULL(c.rate, b.basic_rate) as basic_rate, c.daily, ROUND(IFNULL(d.work_days_in_year, 314), 2) as work_days, c.payroll_type, c.total_minutes_worked, IFNULL(a.total_accredited_ot_hrs, 0) as ot_hrs, IFNULL(a.total_accredited_ndiff_ot_hrs, 0) as ot_ndiff_hrs';
            $this->db->select($select);
            $this->db->join("gccmaster.tblemployees b", "a.emp_id = b.id", "LEFT");
            $this->db->join('payroll.payroll_sheet c', 'c.emp_id = b.id AND (DATE(c.date_start) <= DATE(a.date) AND DATE(c.date_end) >= DATE(a.date))', 'LEFT');
            $this->db->join('gcchris.tblcompanies d', 'd.id = b.company_id', 'LEFT');
            $this->db->where('c.posted', 1);
            $this->db->where('a.verified', 1);
            $this->db->where('c.is_bonus', 0);
            $this->db->from('gcctimeutility.timesheet a');

            if ($lastId) {
                $this->db->where('a.id >', $lastId);
            }

            if (is_array($filteredIds) && count($filteredIds) > 0) {
                $this->db->where_in('a.emp_id', $filteredIds); 
            }

            $this->db->where('b.company_id', $company);

            $this->db->group_start();
                $this->db->where('DATE(a.date) >=', $startDate);
                $this->db->where('DATE(a.date) <=', $endDate);
            $this->db->group_end();

            $this->db->order_by('a.id', 'asc');
            $this->db->limit($chunkSize);

            $query = $this->db->get();
            $rows = $query->result();
            $rowsA = $query->result_array();

            if (!$rows) {
                break;
            }

            $lastArray = end($rowsA);
            if (!empty($lastArray)) {
                $lastId = $lastArray['id'];
            }

            $tempData[] = $rows;
        } while(count($rows) === $chunkSize);

        $merged = array_merge(...$tempData);

        usort($merged, function ($a, $b) {
            return [$a->lastname, $a->firstname, $a->date]
                <=> [$b->lastname, $b->firstname, $b->date];
        });

        return $merged;
    }

    public function print_count() {
        $post = $this->input->post();

        $count = 1;
        $last_printed = null;
        $last_printed_at = null;
        $last_amount = 0;

        $group = (isset($post["group"]) && $post["group"])? intval($post["group"]): 1;
        $payrollGroup = isset($post["payroll_group"]) && $post["payroll_group"] ? $post["payroll_group"]: array();

        $isDateRange = isset($post["date_range"]) && $post["date_range"];
        $isFilterMonth = isset($post["filter_month"]) && $post["filter_month"];

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
        }

        if ($isFilterMonth) {
            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
            }elseif (isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
            }
        }

        if ($isDateRange) {
            if(isset($post["pay_date"]) && $post["pay_date"]){
                $tempStartDate = date("Y-m-d", strtotime($post["pay_date"]));
                $tempEndDate = date("Y-m-d", strtotime($post["pay_date"]));
            }
        }

        $company_id = $post['company'];
        $module = $post['module'];
        $payroll_group = isset($post['payroll_group']) && $post['payroll_group'] ? serialize($post['payroll_group']) : serialize(array());
        $amount = $post['amount'];

        $this->db->where('module', $module);
        $this->db->where('DATE(date_from)', $tempStartDate);
        $this->db->where('DATE(date_to)', $tempEndDate);
        $this->db->where('company_id', $company_id);
        $this->db->where('payroll_group', $payroll_group);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('payroll.print_report_counter');
        $row = $query->row();

        if ($row) {
            $count = ((int) $row->counter) + 1;
            $last_amount = isset($row->printed_amount) ? $row->printed_amount : 0;

            if (!empty($row->printed_by)) {
                $tempRecord = (object) $this->core_layout->getEmployeeData($row->printed_by);
                if (isset($tempRecord->display_name_0) && $tempRecord->display_name_0) {
                    $last_printed = $tempRecord->display_name_0;
                }
            }

            if (!empty($row->printed_at)) {
                $last_printed_at = date('Y-m-d H:i:s', strtotime($row->printed_at));
            }
        }

        $data = array(
            'module' => $module,
            'date_from' => $tempStartDate,
            'date_to' => $tempEndDate,
            'company_id' => $company_id,
            'payroll_group' => $payroll_group,
            'counter' => $count,
            'printed_amount' => $amount,
            'printed_by' => $this->user_data['emp_id'],
            'printed_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('payroll.print_report_counter', $data);

        return array(
            'data' => array(
                'count' => $count,
                'last_printed_by' => $last_printed,
                'last_printed_at' => $last_printed_at,
                'amount' => $last_amount
            )
        );
    }
}
