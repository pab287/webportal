<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Dashboard_m extends CI_Model {
        protected $months = array(
            array("index" => 1, "name" => "Jan"),
            array("index" => 2, "name" => "Feb"),
            array("index" => 3, "name" => "Mar"),
            array("index" => 4, "name" => "Apr"),
            array("index" => 5, "name" => "May"),
            array("index" => 6, "name" => "Jun"),
            array("index" => 7, "name" => "Jul"),
            array("index" => 8, "name" => "Aug"),
            array("index" => 9, "name" => "Sep"),
            array("index" => 10, "name" => "Oct"),
            array("index" => 11, "name" => "Nov"),
            array("index" => 12, "name" => "Dec")
        );

        protected $years = array(
            array("index" => 2018, "name" => "2018"),
            array("index" => 2019, "name" => "2019"),
            array("index" => 2020, "name" => "2020"),
            array("index" => 2021, "name" => "2021"),
            array("index" => 2022, "name" => "2022"),
        );
        function __construct() {
            parent::__construct();
            $this->load->model("hris/dashboard_model", "hris_dashboard");
            $this->user_data = $this->session->userdata("logged_in");
            date_default_timezone_set("Asia/Manila");
            
        }
        
        function paidContributionSSS() {
            
            $resultSet = array();
            $post = $this->input->post();
            $tempStartDate = null;
            $tempEndDate = null;
            $date_filter = null;
            $company_filter = null;
            if(isset($post['company']) && $post['company']){
                $company = "AND company_id = '$post[company]'";
                $company_filter = $this->db->get_where("gcchris.tblcompanies" , array("id"=>$post['company']))->row('description');
            }else{
                $company = "";
                $company_filter = "ALL COMPANIES";
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $month_filter = DateTime::createFromFormat('!m', $post["filter_month"]);
                $date_filter = $month_filter->format('F')." ".$post['filter_year'];

            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
                $date_filter = date("{$post["filter_year"]}", strtotime("{$post["filter_year"]}"));

            }else if(isset($post["date_range"]) && $post["date_range"]){
                $dates = explode("-", $post["date_range"]);
                if(is_array($dates) && count($dates) == 2){
                    foreach ($dates as $key => $date) {
                        $tempDate = date("Y-m-d", strtotime(trim($date)));
                        $dates[$key] = $tempDate;
                    }
                    $tempStartDate = $dates[0];
                    $tempEndDate = $dates[1];
                }
                $date_filter = $tempStartDate." - ".$tempEndDate;

            }else{
                $tempStartDate = date("Y-m-01");
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $date_filter = date("F Y");
            }

            $filter = array(
                "company"=>strtoupper($company_filter),
                "date"=>"(As of ".$date_filter.")"
            );    

            $sqlSelect = "SELECT sum(sss_er) as amount, 'SSS' as `name` FROM payroll.payroll_sheet WHERE DATE(pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND `is_bonus` = 0 AND `posted` = 1 $company UNION ALL SELECT sum(hdmf) as amount, 'PAGIBIG' as `name` FROM payroll.payroll_sheet WHERE DATE(pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND `is_bonus` = 0 AND `posted` = 1 $company UNION ALL SELECT sum(ph) as amount, 'PHILHEALTH' as `name` FROM payroll.payroll_sheet WHERE DATE(pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND `is_bonus` = 0 AND `posted` = 1 $company";
            $data = $this->db->query($sqlSelect);
            foreach($data->result() as $item){
                $result = array();
                $result['name'] = $item->name;
                $result['amount'] = "₱".number_format($item->amount,2);
                $resultSet[] = $result;
            }
            
            
            $resultSet['filters'] = $filter;
            
            return $resultSet;
        }  

        function paidContributions() {
            
            $resultSet = array();
            $post = $this->input->post();
            
            // var_dump($post);
            $tempStartDate = null;
            $tempEndDate = null;
            $date_filter = null;
            $company_filter = null;
            if(isset($post['company']) && $post['company']){
                $company = "AND company_id = '$post[company]'";
                $company_filter = $this->db->get_where("gcchris.tblcompanies" , array("id"=>$post['company']))->row('description');
                $company_xls = "";
            }else{
                $company = "";
                $company_filter = "ALL COMPANIES";
                $company_xls = "GROUP BY company_id";
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $month_filter = DateTime::createFromFormat('!m', $post["filter_month"]);
                $date_filter = $month_filter->format('F')." ".$post['filter_year'];

            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
                $date_filter = date("{$post["filter_year"]}", strtotime("{$post["filter_year"]}"));

            }else if(isset($post["date_range"]) && $post["date_range"]){
                $dates = explode("-", $post["date_range"]);
                if(is_array($dates) && count($dates) == 2){
                    foreach ($dates as $key => $date) {
                        $tempDate = date("Y-m-d", strtotime(trim($date)));
                        $dates[$key] = $tempDate;
                    }
                    $tempStartDate = $dates[0];
                    $tempEndDate = $dates[1];
                }
                $date_filter = $tempStartDate." - ".$tempEndDate;

            }else{
                $tempStartDate = date("Y-m-01");
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $date_filter = date("F Y");
            }

            $filter = array(
                "company"=>strtoupper($company_filter),
                "date"=>"(As of ".$date_filter.")"
            );    

            $sqlSelect = "SELECT sum(ps.ot_minutes/60) as `times`, sum(ps.ot_amount) as `amounts`, 'HOURS' as `name`, comp.code as `company` FROM payroll.payroll_sheet as ps LEFT JOIN gcchris.tblcompanies as comp ON comp.id=ps.company_id WHERE DATE(ps.pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND ps.is_bonus = 0 AND ps.posted = 1 $company UNION ALL SELECT sum(ps.ot_minutes/60) as `times`, sum(ps.ot_amount) as `amounts`, 'AMOUNT' as `name`, comp.code as `company` FROM payroll.payroll_sheet as ps LEFT JOIN gcchris.tblcompanies as comp ON comp.id=ps.company_id WHERE DATE(ps.pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND ps.is_bonus = 0 AND ps.posted = 1 $company";

            $data = $this->db->query($sqlSelect);
            $vue_data = array();
            foreach($data->result() as $item){
                $result = array();
                $result['company'] = $item->company;
                $result['name'] = $item->name;
                $result['amount'] = ($item->name === 'HOURS') ? $item->times." HRS" : number_format($item->amounts,2);
                $vue_data[] = $result;
            }
            $resultSet['vue_data'] = $vue_data;

            $export_data = "SELECT sum(ps.sss) as `sss`, sum(ps.ph) as `phil`, sum(ps.hdmf) as `hdmf`, comp.code as `company` FROM payroll.payroll_sheet as ps LEFT JOIN gcchris.tblcompanies as comp ON comp.id=ps.company_id WHERE DATE(ps.pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND ps.is_bonus = 0 AND ps.posted = 1 $company $company_xls";

            $exportData = $this->db->query($export_data);
            $resultSet_xls = array();
            foreach($exportData->result_array() as $export_item){
                $result_excel = array();
                $result_excel['company'] = $export_item['company'];
                $result_excel['sss'] = $export_item['sss'];
                $result_excel['phil'] = $export_item['phil'];
                $result_excel['hdmf'] = $export_item['hdmf'];
                $resultSet_xls[] = $result_excel;
            }
            $resultSet['filters'] = $filter;
            $resultSet['data'] = $resultSet_xls;
            
            return $resultSet;
            
        }

        function paidOvertime() {
            
            $resultSet = array();
            $post = $this->input->post();
            
            // var_dump($post);
            $tempStartDate = null;
            $tempEndDate = null;
            $date_filter = null;
            $company_filter = null;
            if(isset($post['company']) && $post['company']){
                $company = "AND company_id = '$post[company]'";
                $company_filter = $this->db->get_where("gcchris.tblcompanies" , array("id"=>$post['company']))->row('description');
                $company_xls = "";
            }else{
                $company = "";
                $company_filter = "ALL COMPANIES";
                $company_xls = "GROUP BY company_id";
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $month_filter = DateTime::createFromFormat('!m', $post["filter_month"]);
                $date_filter = $month_filter->format('F')." ".$post['filter_year'];

            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
                $date_filter = date("{$post["filter_year"]}", strtotime("{$post["filter_year"]}"));

            }else if(isset($post["date_range"]) && $post["date_range"]){
                $dates = explode("-", $post["date_range"]);
                if(is_array($dates) && count($dates) == 2){
                    foreach ($dates as $key => $date) {
                        $tempDate = date("Y-m-d", strtotime(trim($date)));
                        $dates[$key] = $tempDate;
                    }
                    $tempStartDate = $dates[0];
                    $tempEndDate = $dates[1];
                }
                $date_filter = $tempStartDate." - ".$tempEndDate;

            }else{
                $tempStartDate = date("Y-m-01");
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $date_filter = date("F Y");
            }

            $filter = array(
                "company"=>strtoupper($company_filter),
                "date"=>"(As of ".$date_filter.")"
            );    

            $sqlSelect = "SELECT sum(ps.ot_minutes/60) as `times`, sum(ps.ot_amount) as `amounts`, 'HOURS' as `name`, comp.code as `company` FROM payroll.payroll_sheet as ps LEFT JOIN gcchris.tblcompanies as comp ON comp.id=ps.company_id WHERE DATE(ps.pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND ps.is_bonus = 0 AND ps.posted = 1 $company UNION ALL SELECT sum(ps.ot_minutes/60) as `times`, sum(ps.ot_amount) as `amounts`, 'AMOUNT' as `name`, comp.code as `company` FROM payroll.payroll_sheet as ps LEFT JOIN gcchris.tblcompanies as comp ON comp.id=ps.company_id WHERE DATE(ps.pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND ps.is_bonus = 0 AND ps.posted = 1 $company";

            $data = $this->db->query($sqlSelect);
            $vue_data = array();
            foreach($data->result() as $item){
                $result = array();
                $result['company'] = $item->company;
                $result['name'] = $item->name;
                $result['amount'] = ($item->name === 'HOURS') ? $item->times." HRS" : number_format($item->amounts,2);
                $vue_data[] = $result;
            }
            $resultSet['vue_data'] = $vue_data;

            $export_data = "SELECT sum(ps.ot_minutes/60) as `time`, sum(ps.ot_amount) as `amount`, comp.code as `company` FROM payroll.payroll_sheet as ps LEFT JOIN gcchris.tblcompanies as comp ON comp.id=ps.company_id WHERE DATE(ps.pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND ps.is_bonus = 0 AND ps.posted = 1 $company $company_xls";

            $exportData = $this->db->query($export_data);
            $resultSet_xls = array();
            foreach($exportData->result_array() as $export_item){
                $result_excel = array();
                $result_excel['company'] = $export_item['company'];
                $result_excel['time'] = $export_item['time'];
                $result_excel['amount'] = $export_item['amount'];
                $resultSet_xls[] = $result_excel;
            }
            $resultSet['filters'] = $filter;
            $resultSet['data'] = $resultSet_xls;
            
            return $resultSet;
            
        }

        function paidAllowance() {
            $resultSet = array();
            $post = $this->input->post();
            $tempStartDate = null;
            $tempEndDate = null;
            $date_filter = null;
            $company_filter = null;
            if(isset($post['company']) && $post['company']){
                $company = "AND company_id = '$post[company]'";
                $company_filter = $this->db->get_where("gcchris.tblcompanies" , array("id"=>$post['company']))->row('description');
                $company_xls = "";
            }else{
                $company = "";
                $company_filter = "ALL COMPANIES";
                $company_xls = "GROUP BY company_id";
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $month_filter = DateTime::createFromFormat('!m', $post["filter_month"]);
                $date_filter = $month_filter->format('F')." ".$post['filter_year'];

            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
                $date_filter = date("{$post["filter_year"]}", strtotime("{$post["filter_year"]}"));

            }else if(isset($post["date_range"]) && $post["date_range"]){
                $dates = explode("-", $post["date_range"]);
                if(is_array($dates) && count($dates) == 2){
                    foreach ($dates as $key => $date) {
                        $tempDate = date("Y-m-d", strtotime(trim($date)));
                        $dates[$key] = $tempDate;
                    }
                    $tempStartDate = $dates[0];
                    $tempEndDate = $dates[1];
                }
                $date_filter = $tempStartDate." - ".$tempEndDate;

            }else{
                $tempStartDate = date("Y-m-01");
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $date_filter = date("F Y");
            }

            $filter = array(
                "company"=>strtoupper($company_filter),
                "date"=>"(As of ".$date_filter.")"
            );    

            $sqlSelect = "SELECT sum(ps.total_allowances) as `amount`, comp.code as `company` FROM payroll.payroll_sheet as ps LEFT JOIN gcchris.tblcompanies as comp ON comp.id=ps.company_id WHERE DATE(ps.pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND ps.is_bonus = 0 AND ps.posted = 1 $company";

            $data = $this->db->query($sqlSelect);
            $vue_data = array();
            foreach($data->result() as $item){
                $result = array();
                $result['company'] = $item->company;
                $result['amount'] = "";
                $vue_data[] = $result;
            }
            $resultSet['vue_data'] = $vue_data;

            $export_data = "SELECT sum(ps.total_allowances) as `amount`, comp.code as `company` FROM payroll.payroll_sheet as ps LEFT JOIN gcchris.tblcompanies as comp ON comp.id=ps.company_id WHERE DATE(ps.pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND ps.is_bonus = 0 AND ps.posted = 1 $company $company_xls";

            $exportData = $this->db->query($export_data);
            $resultSet_xls = array();
            foreach($exportData->result_array() as $export_item){
                $result_excel = array();
                $result_excel['company'] = $export_item['company'];
                $result_excel['amount'] = $export_item['amount'];
                $resultSet_xls[] = $result_excel;
            }
            $resultSet['filters'] = $filter;
            $resultSet['data'] = $resultSet_xls;
            
            return $resultSet;
            
        }

        function paidTax() {
            $resultSet = array();
            $post = $this->input->post();
            $tempStartDate = null;
            $tempEndDate = null;
            $date_filter = null;
            $company_filter = null;
            if(isset($post['company']) && $post['company']){
                $company = "AND company_id = '$post[company]'";
                $company_filter = $this->db->get_where("gcchris.tblcompanies" , array("id"=>$post['company']))->row('description');
                $company_xls = "";
            }else{
                $company = "";
                $company_filter = "ALL COMPANIES";
                $company_xls = "GROUP BY company_id";
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $month_filter = DateTime::createFromFormat('!m', $post["filter_month"]);
                $date_filter = $month_filter->format('F')." ".$post['filter_year'];

            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
                $date_filter = date("{$post["filter_year"]}", strtotime("{$post["filter_year"]}"));

            }else if(isset($post["date_range"]) && $post["date_range"]){
                $dates = explode("-", $post["date_range"]);
                if(is_array($dates) && count($dates) == 2){
                    foreach ($dates as $key => $date) {
                        $tempDate = date("Y-m-d", strtotime(trim($date)));
                        $dates[$key] = $tempDate;
                    }
                    $tempStartDate = $dates[0];
                    $tempEndDate = $dates[1];
                }
                $date_filter = $tempStartDate." - ".$tempEndDate;

            }else{
                $tempStartDate = date("Y-m-01");
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $date_filter = date("F Y");
            }

            $filter = array(
                "company"=>strtoupper($company_filter),
                "date"=>"(As of ".$date_filter.")"
            );    

            $sqlSelect = "SELECT sum(ps.tax) as `amount`, comp.code as `company` FROM payroll.payroll_sheet as ps LEFT JOIN gcchris.tblcompanies as comp ON comp.id=ps.company_id WHERE DATE(ps.pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND ps.is_bonus = 0 AND ps.posted = 1 $company";

            $data = $this->db->query($sqlSelect);
            $vue_data = array();
            foreach($data->result() as $item){
                $result = array();
                $result['company'] = $item->company;
                $result['amount'] = "";
                $vue_data[] = $result;
            }
            $resultSet['vue_data'] = $vue_data;

            $export_data = "SELECT sum(ps.tax) as `amount`, comp.code as `company` FROM payroll.payroll_sheet as ps LEFT JOIN gcchris.tblcompanies as comp ON comp.id=ps.company_id WHERE DATE(ps.pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND ps.is_bonus = 0 AND ps.posted = 1 $company $company_xls";

            $exportData = $this->db->query($export_data);
            $resultSet_xls = array();
            foreach($exportData->result_array() as $export_item){
                $result_excel = array();
                $result_excel['company'] = $export_item['company'];
                $result_excel['amount'] = $export_item['amount'];
                $resultSet_xls[] = $result_excel;
            }
            $resultSet['filters'] = $filter;
            $resultSet['data'] = $resultSet_xls;
            
            return $resultSet;
            
        }

        // function exportExcel(){
        //     $data = $this->paidOvertime();
        //     $temp = $data['xls'];
        //     function cleanData(&$str)
        //     {
        //         $str = preg_replace("/\t/", "\\t", $str);
        //         $str = preg_replace("/\r?\n/", "\\n", $str);
        //         if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
        //     }

        //     // filename for download
        //     $filename = "website_data_" . date('Ymd') . ".xls";

        //     header("Content-Disposition: attachment; filename=\"$filename\"");
        //     header("Content-Type: application/vnd.ms-excel");

        //     $flag = false;
        //     foreach($temp as $row) {
        //         if(!$flag) {
        //         // display field/column names as first row
        //         echo implode("\t", array_keys($row)) . "\r\n";
        //         $flag = true;
        //         }
        //         array_walk($row, __NAMESPACE__ . '\cleanData');
        //         echo implode("\t", array_values($row)) . "\r\n";
        //     }
        //     exit;
        // }

        function paidContributionHDMF() {
            $this->db->select("sum(hdmf) as amount, 'PAGIBIG' as name");
            $this->db->from("payroll.payroll_sheet p_sheet");
            $this->db->where("year", "2021");
            $this->db->where("month_name", "November");
            $this->db->where("is_bonus", 0);
            $this->db->where("posted", 1);
            $query = $this->db->get();
            $data = $query->row();
            return $data;
        }

        function paidContributionPHIL() {
            $this->db->select("sum(ph) as amount, 'PHILHEALTH' as name");
            $this->db->from("payroll.payroll_sheet p_sheet");
            $this->db->where("year", "2021");
            $this->db->where("month_name", "November");
            $this->db->where("is_bonus", 0);
            $this->db->where("posted", 1);
            $query = $this->db->get();
            $data = $query->row();
            return $data;
        }

        public function getEachEmployeeStatusDemographics_() {
            $this->db->select("UCASE(IF(emp.employee_status='Black Listed', 'Blacklisted', emp.employee_status)) , UCASE(IF(emp.employee_status='End of Contract', 'Contract End', emp.employee_status))
                               employee_status, emp.employee_status `key`, COUNT(*) cnt");
            $this->db->where("emp.employee_status IS NOT NULL", NULL, FALSE);
            // $this->db->where("emp.work_status IN ('Regular', 'Probationary', 'Service contract', 'No contract')", NULL, FALSE);
            $this->db->group_by('emp.employee_status');
            $this->db->order_by('key', 'DESC');
            $query = $this->db->get($this->tblEmployees . " emp");

            $total = array_reduce($query->result(), function ($carry, $item) {
                return $carry + $item->cnt;
            });

            return array("data" => $query->result(), "total" => number_format($total, 0, '.', ','));
        }
        public function getPayrollPerDepartment() {
            $this->db->select("UCASE(dept.code) `key`, sum(psheet.gross_pay) cnt");
            // $this->db->where("emp.work_status IN ('Regular', 'Probationary', 'Service contract', 'No contract')", NULL, FALSE);
            $this->db->from('gcchris.tbldepartments dept');
            $this->db->join('gccmaster.tblemployees emp', 'emp.department_id=dept.id', 'LEFT');
            $this->db->join('payroll.payroll_sheet psheet', 'emp.id=psheet.emp_id', 'LEFT');
            $this->db->where('dept.is_archived', 0);
            $this->db->where('dept.code !=', NULL);
            $this->db->where('dept.code !=', "");
            $this->db->where('psheet.month_name', 'november');
            $this->db->where('psheet.year', 2021);
            $this->db->group_by('emp.department_id');
            $this->db->order_by('cnt', 'ASC');
            $query = $this->db->get();

            return array("data" => $query->result());
        }

        public function getPayrollPerPosition() {
            $this->db->select("UCASE(pos.name) `key`, sum(psheet.gross_pay) cnt");
            // $this->db->where("emp.work_status IN ('Regular', 'Probationary', 'Service contract', 'No contract')", NULL, FALSE);
            $this->db->from('gcchris.tblposition pos');
            $this->db->join('gccmaster.tblemployees emp', 'emp.department_id=pos.id', 'LEFT');
            $this->db->join('payroll.payroll_sheet psheet', 'emp.id=psheet.emp_id', 'LEFT');
            $this->db->where('pos.is_archived', 0);
            $this->db->where('pos.name !=', NULL);
            $this->db->where('pos.name !=', "");
            $this->db->where('psheet.month_name', 'november');
            $this->db->where('psheet.year', 2021);
            $this->db->group_by('emp.department_id');
            $this->db->order_by('cnt', 'ASC');
            $query = $this->db->get();

            return array("data" => $query->result());
        }

        function paidContributionGraph() {
            
            $resultSet = array();
            $post = $this->input->post();
            $tempStartDate = null;
            $tempEndDate = null;
            $date_filter = null;
            $company_filter = null;
            if(isset($post['company']) && $post['company']){
                $company = "AND company_id = '$post[company]'";
                $company_filter = $this->db->get_where("gcchris.tblcompanies" , array("id"=>$post['company']))->row('description');
            }else{
                $company = "";
                $company_filter = "ALL COMPANIES";
            }

            if(isset($post["filter_month"], $post["filter_year"]) && ($post["filter_month"] && $post["filter_year"])){
                $tempStartDate = date("Y-m-d", strtotime("{$post["filter_year"]}-{$post["filter_month"]}-01"));
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $month_filter = DateTime::createFromFormat('!m', $post["filter_month"]);
                $date_filter = $month_filter->format('F')." ".$post['filter_year'];

            }else if(isset($post["filter_year"]) && $post["filter_year"]){
                $tempStartDate = date("Y-01-01", strtotime("{$post["filter_year"]}-01-01"));
                $tempEndDate = date("Y-12-31", strtotime("{$post["filter_year"]}-12-31"));
                $date_filter = date("{$post["filter_year"]}", strtotime("{$post["filter_year"]}"));

            }else if(isset($post["date_range"]) && $post["date_range"]){
                $dates = explode("-", $post["date_range"]);
                if(is_array($dates) && count($dates) == 2){
                    foreach ($dates as $key => $date) {
                        $tempDate = date("Y-m-d", strtotime(trim($date)));
                        $dates[$key] = $tempDate;
                    }
                    $tempStartDate = $dates[0];
                    $tempEndDate = $dates[1];
                }
                $date_filter = $tempStartDate." - ".$tempEndDate;

            }else{
                $tempStartDate = date("Y-m-01");
                $date = new DateTime($tempStartDate);
                $date->modify('last day of this month');
                $tempEndDate = $date->format('Y-m-d');
                $date_filter = date("F Y");
            }

            $filter = array(
                "company"=>strtoupper($company_filter),
                "date"=>"(As of ".$date_filter.")"
            );    

            $sqlSelect = "SELECT sum(sss_er) as amount, 'SSS' as `name` FROM payroll.payroll_sheet WHERE DATE(pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND `is_bonus` = 0 AND `posted` = 1 $company UNION ALL SELECT sum(hdmf) as amount, 'PAGIBIG' as `name` FROM payroll.payroll_sheet WHERE DATE(pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND `is_bonus` = 0 AND `posted` = 1 $company UNION ALL SELECT sum(ph) as amount, 'PHILHEALTH' as `name` FROM payroll.payroll_sheet WHERE DATE(pay_date) BETWEEN '$tempStartDate' AND '$tempEndDate' AND `is_bonus` = 0 AND `posted` = 1 $company";
            $data = $this->db->query($sqlSelect);
            
            return array("data" => $data->result());
        }

        public function getLoans(){
            $post = $this->input->post();
            $items = array();
            if(isset($post['graphsFilter'][1]['value']) && $post['graphsFilter'][1]['value']){
                $year = $post['graphsFilter'][1]['value'];
                $company = $post['graphsFilter'][2]['value'];
            }else{
                $year = date("Y");
                $company = "";
            }
            foreach($this->months as $_month){
                $data = array();
                $data["month"] = $_month["name"];
                $data["internal"] = number_format($this->getInternalLoans($_month["index"],0,$year,$company),2);// == null ? 0 : number_format($this->getInternalLoans($_month["index"],0),2,$year);
                $data["external"] = number_format($this->getInternalLoans($_month["index"],1,$year,$company),2);// == null ? 0 : number_format($this->getInternalLoans($_month["index"],1),2,$year);
                $items[] = $data;
            }
            return $items;
        }

        function getInternalLoans($month,$loan_category,$year,$company){
            $this->db->select("sum(ps_lp.amount_due) as internal");
            $this->db->from("payroll.payroll_sheet ps");
            $this->db->join("payroll.payroll_sheet_loan_payments ps_lp", "ps_lp.payroll_sheet_id=ps.id", "LEFT");
            $this->db->join("payroll.loans loans", "loans.id=ps_lp.loan_id", "LEFT");
            if(isset($company) && $company){
                $this->db->where("ps.company_id", $company);
            }
            $this->db->where("ps.year", $year);
            $this->db->where("MONTH(ps.pay_date)", $month);
            $this->db->where("loans.loan_type", $loan_category);
            $result = $this->db->get();
            return $result->row("internal");
        }

        public function getSalaryHike(){
            $currentYear = date("Y");
            $items = array();
            $minYear = date("Y", strtotime('- 3 year', strtotime($currentYear)));
            $this->db->select("YEAR(sal_date) as years");
            $this->db->from("gcchris.tblsalaries");
            $this->db->where("YEAR(sal_date) >=", $minYear);
            $this->db->where("YEAR(sal_date) <=", $currentYear);
            $this->db->group_by("YEAR(sal_date)");
            $query = $this->db->get();
            foreach($query->result_array() as $query){
                $data = array();
                $data["year"] = $query["years"];
                $data["hike"] = number_format($this->getSalaryHikeCount($query["years"]),2);
                $items[] = $data;
            }
            return $items;
        }

        function getSalaryHikeCount($year){
            $this->db->select("count(sal.id) as internal");
            $this->db->from("gcchris.tblsalaries sal");
            $this->db->where("YEAR(sal.sal_date)", $year);
            $result = $this->db->get();
            return $result->row("internal");
        }

        public function getOvertimeGraph(){
            $currentYear = date("Y");
            $previousYear = date("Y", strtotime('-1 year', strtotime($currentYear))); 
            $items = array();
            foreach($this->months as $_month){
                $data = array();
                $data["month"] = $_month["name"];
                $data[$previousYear] = number_format($this->getOvertimeByYear($_month["index"],$previousYear),2);// == null ? 0 : number_format($this->getInternalLoans($_month["index"],0),2,$year);
                $data[$currentYear] = number_format($this->getOvertimeByYear($_month["index"],$currentYear),2);// == null ? 0 : number_format($this->getInternalLoans($_month["index"],1),2,$year);
                $items[] = $data;
            }
            return $items;
        }

        function getOvertimeByYear($month,$year){
            $this->db->select("sum(ps.ot_minutes/60) as internal");
            $this->db->from("payroll.payroll_sheet ps");
            $this->db->where("ps.year", $year);
            $this->db->where("MONTH(ps.pay_date)", $month);
            $result = $this->db->get();
            return $result->row("internal");
        }

        public function getAllowanceGraph(){
            $currentYear = date("Y");
            $previousYear = date("Y", strtotime('-1 year', strtotime($currentYear))); 
            $items = array();
            foreach($this->months as $_month){
                $data = array();
                $data["month"] = $_month["name"];
                $data[$previousYear] = number_format($this->getAllowanceByYear($_month["index"],$previousYear),2);// == null ? 0 : number_format($this->getInternalLoans($_month["index"],0),2,$year);
                $data[$currentYear] = number_format($this->getAllowanceByYear($_month["index"],$currentYear),2);// == null ? 0 : number_format($this->getInternalLoans($_month["index"],1),2,$year);
                $items[] = $data;
            }
            return $items;
        }

        function getAllowanceByYear($month,$year){
            $this->db->select("sum(ps.total_allowances) as internal");
            $this->db->from("payroll.payroll_sheet ps");
            $this->db->where("ps.year", $year);
            $this->db->where("MONTH(ps.pay_date)", $month);
            $result = $this->db->get();
            return $result->row("internal");
        }

        public function getTaxGraph(){
            $currentYear = date("Y");
            $previousYear = date("Y", strtotime('-1 year', strtotime($currentYear))); 
            $items = array();
            foreach($this->months as $_month){
                $data = array();
                $data["month"] = $_month["name"];
                $data[$previousYear] = number_format($this->getTaxByYear($_month["index"],$previousYear),2);// == null ? 0 : number_format($this->getInternalLoans($_month["index"],0),2,$year);
                $data[$currentYear] = number_format($this->getTaxByYear($_month["index"],$currentYear),2);// == null ? 0 : number_format($this->getInternalLoans($_month["index"],1),2,$year);
                $items[] = $data;
            }
            return $items;
        }

        function getTaxByYear($month,$year){
            $this->db->select("sum(ps.tax) as internal");
            $this->db->from("payroll.payroll_sheet ps");
            $this->db->where("ps.year", $year);
            $this->db->where("MONTH(ps.pay_date)", $month);
            $result = $this->db->get();
            return $result->row("internal");
        }

        public function getContributionGraph(){
            $currentYear = date("Y");
            $items = array();
            $minYear = date("Y", strtotime('- 1 year', strtotime($currentYear)));
            $this->db->select("ps.year as years");
            $this->db->from("payroll.payroll_sheet ps");
            $this->db->where("ps.year >=", $minYear);
            $this->db->where("ps.year <=", $currentYear);
            $this->db->where("ps.posted", 1);
            $this->db->where("ps.is_bonus", 0);
            $this->db->group_by("ps.year");
            $query = $this->db->get();
            foreach($query->result_array() as $query){
                $data = array();
                $data["year"] = $query["years"];
                $data["sss"] = number_format($this->getContributionCount($query["years"], 'sss'),2);
                $data["phic"] = number_format($this->getContributionCount($query["years"], 'phic'),2);
                $data["hdmf"] = number_format($this->getContributionCount($query["years"], 'hdmf'),2);
                $items[] = $data;
            }
            return $items;
        }

        function getContributionCount($year,$type){
            if($type == 'sss'){
                $this->db->select("sum(ps.sss) as internal");
            }elseif($type == 'phic'){
                $this->db->select("sum(ps.ph) as internal");
            }else{
                $this->db->select("sum(ps.hdmf) as internal");
            }
            $this->db->from("payroll.payroll_sheet ps");
            $this->db->where("YEAR(ps.pay_date)", $year);
            $this->db->where("ps.posted", 1);
            $this->db->where("ps.is_bonus", 0);
            $result = $this->db->get();
            return $result->row("internal");
        }
        
    }