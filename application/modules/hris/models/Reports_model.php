<?php defined('BASEPATH') || exit('No direct script access allowed');
class Reports_model extends CI_Model{
    protected $tblHolidays = "gcchris.tblholidays";
    protected $tblEmployees = "gccmaster.tblemployees";
    protected $companyTable = "gcchris.tblcompanies";
    protected $departmentTable = "gcchris.tbldepartments";
    protected $positionTable = "gcchris.tblposition";

    protected $defaultStationTable = "gcchris.default_station_location";
    protected $tblAppLocationSites = "gcctimeutility.app_location_sites";

    protected $now = null;
    protected $user = null;

    public function __construct(){
        parent::__construct();
        $this->load->model("ams/Utilities_model", "utilities");
        $this->load->model("hris/employee_model", "employee");
        $this->now = new DateTime(null, new DateTimeZone('Asia/Manila'));
        $this->now = $this->now->format('Y-m-d H:i:s');
        $this->user = $this->core_layout->getUserLoggedIn();
    }

    public function getTableColumns($db, $table, $except_columns = array()){
        $this->db->select("CONCAT('$db','.', '$table','.', COLUMN_NAME) AS `field`, COLUMN_NAME description", false);
        $this->db->where("table_schema", $db);
        $this->db->where("table_name", $table);

        foreach ($except_columns as $column) {
            $this->db->where("COLUMN_NAME !=", $column);
        }

        return $this->db->get("information_schema.columns")->result();
    }

    public function getTableColumnsForFilter($db, $table, $except_columns = array()){
        $this->db->select("COLUMN_NAME AS `id`, COLUMN_NAME AS `label`, DATA_TYPE `type`", false);
        $this->db->where("table_schema", $db);
        $this->db->where("table_name", $table);

        foreach ($except_columns as $column) {
            $this->db->where("COLUMN_NAME !=", $column);
        }

        return $this->db->get("information_schema.columns")->result();
    }

    public function generateEmployeeReport($export){
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($post);
        $select = implode(", ", $post->fields);
        $order_field = $post->order_field;
        $order_by = $post->order_by;
        $criteria = $post->criteria;

        $this->db->select($select, false);

        if (!empty($criteria)) {
            $this->db->where($criteria, null, false);
        }

        if (intval($export) === 0 && $pageOptions->length > -1) {
            $this->db->limit($pageOptions->length, $pageOptions->start);
        }

        if (!empty($order_field)) {
            $order = $order_field . " " . $order_by;
            $this->db->order_by($order);
        }

        $joinArr = array(
            array(
                "table" => "gcchris.tblcompanies company",
                "condition" => "company.id = emp.company_id",
                "option" => "LEFT"),
            array(
                "table" => "gcchris.tbldepartments department",
                "condition" => "department.id = emp.department_id OR department.description = emp.department_id",
                "option" => "LEFT"),
            array(
                "table" => "gcchris.tblposition position",
                "condition" => "position.id = emp.position",
                "option" => "LEFT"),
            array(
                'table' => 'gcchris.tblsalaries salaries',
                'condition' => 'emp.id = salaries.emp_id AND salaries.sal_date = (SELECT MAX( sal_date) latest_date FROM gcchris.tblsalaries WHERE emp_id=emp.id)',
                'option' => 'LEFT'),
            array(
                'table' => 'gcchris.tbleducations educ',
                'condition' => 'educ.emp_id = emp.id',
                'option' => 'LEFT'),
            array(
                'table' => 'gcctimeutility.personnel personnel',
                'condition' => 'emp.biometricno = personnel.biometricno',
                'option' => 'LEFT'),
            array(
                'table' => 'gcctimeutility.personnel_locations location',
                'condition' => 'personnel.id = location.personnel_id',
                'option' => 'LEFT'),
            array(
                'table' => 'payroll.payout_schedule as payout_schedule',
                'condition' => 'payout_schedule.id = emp.payout_sched',
                'option' => 'LEFT'),
        );

        foreach ($joinArr as $join) {
            $this->db->join($join["table"], $join["condition"], $join["option"]);
        }
        $this->db->group_by("emp.id");
        $resultSet['data'] = $this->db->get($this->tblEmployees . " emp")->result();
        $resultSet['x'] = $this->db->last_query();

        $recordCount = $this->utilities->getTableCount($this->tblEmployees . " emp", $criteria, null, $joinArr, true, null, "emp.id");
        $resultSet["recordsTotal"] = $recordCount;
        $resultSet["recordsFiltered"] = $recordCount;
        return $resultSet;
    }

    public function saveFieldTemplate($post){
        $resultSet = array();
        $this->db->trans_begin();

        $this->db->insert('gcchris.tblrpttemplate', array('description' => $post->description));
        $insertId = $this->db->insert_id();
        $this->db->reset_query();
        $template_body = array();

        $template_body = array_map(function ($item) use ($insertId) {
            $item['template_id'] = $insertId;
            return $item;
        }, $post->items);

        $this->db->insert_batch('gcchris.tblrpttemplate_body', $template_body);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $resultSet['success'] = false;
            $resultSet['message'] = $this->db->error();
        } else {
            $this->db->trans_commit();
            $resultSet['success'] = true;
            $resultSet['message'] = "Template successfully saved.";
        }

        return $resultSet;
    }

    public function updateFieldTemplate($post)
    {
        $resultSet = array();
        $this->db->trans_begin();

        $id = $post->id;
        unset($post->id);

        $this->db->where('id', $id);
        $this->db->update('gcchris.tblrpttemplate', array('description' => $post->description));
        $this->db->reset_query();
        $template_body = array();

        $template_body = array_map(function ($item) use ($id) {
            $item['template_id'] = $id;
            return $item;
        }, $post->items);

        $this->db->delete('gcchris.tblrpttemplate_body', array('template_id' => $id));
        $this->db->insert_batch('gcchris.tblrpttemplate_body', $template_body);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $resultSet['success'] = false;
            $resultSet['message'] = $this->db->error();
            $resultSet['data'] = null;
        } else {
            $this->db->trans_commit();
            $resultSet['success'] = true;
            $resultSet['message'] = "Template successfully updated.";
            $resultSet['data'] = array('id' => $id, 'text' => $post->description, 'template_body' => $template_body);
        }

        return $resultSet;
    }

    public function getFieldTempates(){
        $q = isset($_GET['q']) ? $_GET['q'] : '';

        $this->db->select('id, description text');
        $this->db->like('description', $q, 'both');
        $this->db->where('description IS NOT NULL', false, false);
        $this->db->where('description !=', '');
        return array('results' => $this->db->get('gcchris.tblrpttemplate')->result());
    }

    public function getTemplateBody($template_id){
        $this->db->where('template_id', $template_id);
        return $this->db->get('gcchris.tblrpttemplate_body')->result();
    }

    public function getTemplateData(){
        $formData = $this->input->post('formData');
        $this->db->where('id', $formData['id']);
        return array('data' => $this->db->get('gcchris.tblrpttemplate')->row());
    }

    public function getExpiringEmployees($export, $work_status){
        $tableConfig = $this->input->post();
        $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
        $search = $pageOptions->search;

        $select = "emp.id, emp.date_start date_hired,";
        $select .= "DATE_ADD(emp.date_start, INTERVAL 3 MONTH) firstEvaluation,";
        $select .= "DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) secondEvaluation,";
        $select .= "DATE_ADD(emp.date_start, INTERVAL 5 MONTH) finalEvaluation,";
        $select .= "emp.date_end_prob end_of_contract,";
        $select .= "DATEDIFF(emp.date_end_prob, CURDATE()) daysBeforeEvaluation,";
        $select .= "emp.idno,";
        $select .= "UCASE(CONCAT(emp.firstname, ' ', emp.middlename, ' ', emp.lastname, ";
        $select .= "    CASE";
        $select .= "        WHEN emp.suffix IS NOT NULL AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)";
        $select .= "    ELSE '' END)) `name`,";
        $select .= "UCASE(IF(company.code IS NULL, emp.company_id ,company.code)) company,";
        $select .= "UCASE(IF(dep.description IS NULL, emp.department_id, dep.description)) department,";
        $select .= "UCASE(IF(pos.name IS NULL, emp.position, pos.name)) `position`";

        $joinArr = array(
            array('table' => 'gcchris.tblcompanies company', 'condition' => 'emp.company_id = company.id', 'option' => 'LEFT'),
            array('table' => 'gcchris.tbldepartments dep', 'condition' => 'emp.department_id = dep.id', 'option' => 'LEFT'),
            array('table' => 'gcchris.tblposition pos', 'condition' => 'emp.position = pos.id', 'option' => 'LEFT')
        );

        $where = array(
            "emp.work_status" => $work_status,
            "emp.employee_status" => "Active",
        );

        $this->db->select($select);
        $this->db->where($where);
        foreach ($joinArr as $join) {
            $this->db->join($join['table'], $join['condition'], $join['option']);
        }

        if (intval($export) === 0 && $pageOptions->length > -1) {
            $this->db->limit($pageOptions->length, $pageOptions->start);
        }

        $searchFields = array(
            "field" => "CONCAT(emp.firstname, emp.lastname, emp.middlename, 
                        CONCAT(emp.firstname, ' ', emp.middlename, ' ', emp.lastname, CASE WHEN emp.suffix IS NOT NULL
                        AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix) ELSE '' END),
                        IF(company.code IS NULL, emp.company_id ,company.code),
                        IF(dep.description IS NULL, emp.department_id, dep.description),
                        IF(pos.name IS NULL, emp.position, pos.name))",
            "key" => $search,
            "option" => "BOTH",
        );

        $this->db->like($searchFields["field"], $searchFields["key"], $searchFields["option"]);

        $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
        $resultSet['data'] = $this->db->get($this->tblEmployees . " emp")->result();
        $resultSet['recordsTotal'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $searchFields, $joinArr);
        $resultSet['recordsFiltered'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $searchFields, $joinArr);

        return $resultSet;
    }

    public function getCompanyCollection(){
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        $this->db->select('id, code text');
        $this->db->like('CONCAT(description, code)', $q, 'both');
        return array('results' => $this->db->get('gcchris.tblcompanies')->result());
    }

    public function getDepartmentCollection(){
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        $this->db->select('id, code text');
        $this->db->like('CONCAT(description, code)', $q, 'both');
        return array('results' => $this->db->get('gcchris.tbldepartments')->result());
    }

    public function getEmployeesForSalaryRange($export){
        $tableConfig = $this->input->post();
        $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
        $search = $pageOptions->search;
        $company = null;
        $department = null;
        $position = null;

        $result = array();

        $salary_from = str_replace(',', '', $tableConfigStd->filter->sal_range_from);
        $salary_to = str_replace(',', '', $tableConfigStd->filter->sal_range_to);

        $where = array(
            "emp.employee_status" => $tableConfigStd->filter->employee_status,
            "CAST(REPLACE(salaries.sal_rate,',','') AS DECIMAL(10,2)) >=" => $salary_from,
            "CAST(REPLACE(salaries.sal_rate,',','') AS DECIMAL(10,2)) <=" => $salary_to,
        );

        if (isset($tableConfigStd->filter->company)&& !empty($tableConfigStd->filter->company)) {
            $company = $this->db->where('id', $tableConfigStd->filter->company)->get('gcchris.tblcompanies')->row('code');
            $where['IF(comp.id IS NULL, emp.company_id, comp.code)='] = $company;
        }

        if (isset($tableConfigStd->filter->department) && !empty($tableConfigStd->filter->department)) {
            $department = $this->db->where('id', $tableConfigStd->filter->department)->get('gcchris.tbldepartments')->row('code');
            $where['IF(dep.id IS NULL, emp.department_id, dep.code)='] = $department;
        }

        if (isset($tableConfigStd->filter->position) && $tableConfigStd->filter->position) {
            $position = $this->db->where('id', $tableConfigStd->filter->position)->get('gcchris.tblposition')->row('name');
            $where['IF(pos.id IS NULL, emp.position, pos.name)='] = $position;
        }

        $select = "IF(comp.id IS NULL, emp.company_id, comp.code) company,
                       IF(dep.id IS NULL, emp.department_id, dep.code) department,
                       CONCAT(emp.firstname,' ', emp.middlename, ' ', emp.lastname,
                           CASE 
                               WHEN emp.suffix IS NOT NULL AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                               ELSE ''
                           END) employees_name,
                       salaries.sal_date,
                       salaries.sal_rate,
                       salaries.sal_remarks,
                       salaries.sal_date,
                       emp.work_status,
                       emp.employee_status,
                       emp.date_start,
                       IF(pos.name IS NULL OR pos.name = '', emp.position, pos.name) AS position,
                       ";
        $joinArr = array(
            array('table' => 'gcchris.tblcompanies comp',
                'condition' => 'comp.code = emp.company_id OR comp.id = emp.company_id',
                'option' => 'LEFT'),
            array('table' => 'gcchris.tbldepartments dep',
                'condition' => 'dep.id = emp.department_id',
                'option' => 'LEFT'),
            array('table' => 'gcchris.tblsalaries salaries',
                'condition' => 'emp.id = salaries.emp_id AND salaries.sal_date = (SELECT MAX( sal_date) latest_date FROM gcchris.tblsalaries WHERE emp_id=emp.id)',
                'option' => 'INNER'),
            array('table' => 'gcchris.tblposition pos',
                'condition' => 'pos.id = emp.position',
                'option' => 'LEFT'),
        );

        if (intval($export) === 0 && $pageOptions->length > -1) {
            $this->db->limit($pageOptions->length, $pageOptions->start);
        }

        $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);

        $this->db->select($select);
        $this->db->where($where);
        foreach ($joinArr as $join) {
            $this->db->join($join['table'], $join['condition'], $join['option']);
        }


        $searchField = array(
            "field" => "CONCAT(IFNULL(salaries.sal_rate, ''),
                               IFNULL(salaries.sal_remarks,''),
                               IFNULL(salaries.sal_date,''),
                               IFNULL(emp.work_status, ''),
                               IFNULL(emp.employee_status, ''),
                               IF(comp.id IS NULL, emp.company_id, comp.code),
                               IF(dep.id IS NULL, emp.department_id, dep.code),
                               IFNULL(emp.lastname, ''), IFNULL(emp.firstname, ''), IFNULL(emp.middlename,''),
                               CONCAT(emp.firstname,' ', emp.middlename, ' ', emp.lastname, 
                                    CASE
                               WHEN emp.suffix IS NOT NULL AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                                    ELSE ''
                               END))",
            "key" => $search,
            "option" => "BOTH"
        );
        $this->db->like($searchField["field"], $search, $searchField["option"]);

        $query = $this->db->get($this->tblEmployees . " emp");

        if($query->num_rows() > 0){

            $arrData = array();
            foreach($query->result() as $key => $rs){
                $tenured = (object) $this->getTenureship($rs->date_start);
                $rs->tenureship = $tenured->tenured;

                $arrData[$key] = $rs;
            }

            foreach ($arrData as $v) { $result[] = $v; }
        }

        $resultSet['data'] = $result;
        $resultSet['sql'] = $this->db->last_query();
        $resultSet['recordsTotal'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $searchField, $joinArr);
        $resultSet['recordsFiltered'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $searchField, $joinArr);
        return $resultSet;
    }

    public function getEmployeeLeaves(){
        $select = "CONCAT(
                        CASE
                            WHEN loa.`type`=1 OR loa.`type`=2 THEN CONCAT(DATE_FORMAT(loa.date_from, '%h:%i%p'), '-', DATE_FORMAT(loa.date_to, '%h:%i%p'), '\n')
                            ELSE ''
                        END,
                        emp.firstname,' ', emp.middlename, ' ', emp.lastname,
                        CASE WHEN emp.suffix IS NOT NULL AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                            ELSE ''
                        END
                        , '\n\n REF No. ' ,loa.reference_no
                       ) title,
                       loa.id,
                       loa.reason,
                       loa.date_from start,
                       loa.date_to end,
                       'm-fc-event--light text-light' className,
                       '#233e6b' backgroundColor";
        $this->db->select($select, false);
        $this->db->where('loa.status', 'Approved');
        $this->db->like('DATE(loa.approved_dt)', date('Y-m'));
        $this->db->join('gccmaster.tblemployees emp', 'emp.id = loa.employee', 'inner');

        return $this->db->get('gcceforms.loa loa')->result();
    }

    public function generateComprehensiveReport(){
        $resultset = array();
        $post = $this->input->post();
        
        if(isset($post["filter_by"]) && $post["filter_by"]){
            $additionalFilters = array();
            if(isset($post["company"]) && $post["company"]){ $additionalFilters["comp.id"] = $post["company"]; }
            if(isset($post["department"]) && $post["department"]){ $additionalFilters["dept.id"] = $post["department"]; }
            if(isset($post["position"]) && $post["position"]){ $additionalFilters["pos.id"] = $post["position"]; }
            if(isset($post["sort_by"]) && $post["sort_by"]){ $sortOrder["sort_by"] = $post["sort_by"]; }
            if(isset($post["sort_order"]) && $post["sort_order"]){ $sortOrder["sort_order"] = $post["sort_order"]; }

            $tempFilterBy = $post["filter_by"];
            $filteredOptions = array();
            $filteredOptions["filter_by"] = $tempFilterBy;
            if($tempFilterBy == "date_range"){
                if(isset($post["filter_type"]) && $post["filter_type"] && $post[$tempFilterBy]){
                    $dateRange = explode(" - ", $post[$tempFilterBy]);
                    if(is_array($dateRange) && count($dateRange) == 2){
                        $filterOption = $post["filter_type"];
                        $option = explode(".", $filterOption);
                        $_filteredOption = "Hired";
                        if(is_array($option) && count($option) == 2){
                            $filteredOptions["filtered_column"] = $option[1];
                            if ($option[1] == "date_start"){ $_filteredOption = "Hired"; }
                            elseif ($option[1] == "date_end"){ $_filteredOption = "Separated"; }
        
                            $filteredOptions["filtered_by"] = $_filteredOption;
                            $filteredOptions["filtered_by_date"] = $_filteredOption;
                        }
        
                        $dateStart = date("Y-m-d", strtotime($dateRange[0]));
                        $dateEnd = date("Y-m-d", strtotime($dateRange[1]));
        
                        $filteredOptions["start_date"] = $dateStart;
                        $filteredOptions["end_date"] = $dateEnd;
        
                        $_filteredStartDate = date("Y/m/d", strtotime($dateStart));
                        $_filteredEndDate = date("Y/m/d", strtotime($dateEnd));
                        $filteredOptions["start_date_formatted"] = $_filteredStartDate;
                        $filteredOptions["end_date_formatted"] = $_filteredEndDate;
        
                        $result = $this->employee->getActiveEmployeesFilter($filterOption, $dateStart, $dateEnd, $additionalFilters, $sortOrder);
                        if($result && $result->num_rows() > 0){
                            $numRows = $result->num_rows();
                            $resultset["response"] = true;
                            $resultset["data"] = $result->result();
                            $resultset["count"] = $numRows;
                            $filteredOptions["total_entries"] = $numRows;
                            $resultset["filtered_options"] = $filteredOptions;
                            $resultset["toastr_msg"] = "Generate comprehensive report for `{$_filteredOption}` employees by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}`, a total of {$numRows} record(s) found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Generate comprehensive report for `{$_filteredOption}` employees by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}`, no filtered data found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Generate comprehensive report for hire/separated employees, no data on date range filter option(s) found!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate comprehensive report for hire/separated employees, no filtered by option(s) found!";
                }
            }else{
                $filterOption = $post["filter_type"];
                $option = explode(".", $filterOption);
                $_filteredOption = "Hired";
                if(is_array($option) && count($option) == 2){
                    $filteredOptions["filtered_column"] = $option[1];
                    if($option[1] == "date_start"){ $_filteredOption = "Hired"; }
                    elseif($option[1] == "date_end"){ $_filteredOption = "Separated"; }

                    $filteredOptions["filtered_by"] = $_filteredOption;
                    $filteredOptions["filtered_by_date"] = $_filteredOption;
                }

                $result = $this->employee->getAllActiveEmployeesFilter($filterOption, $additionalFilters, $sortOrder);
                if($result && $result->num_rows() > 0){
                    $numRows = $result->num_rows();
                    $resultset["response"] = true;
                    $resultset["data"] = $result->result();
                    $resultset["count"] = $numRows;
                    $filteredOptions["total_entries"] = $numRows;
                    $resultset["filtered_options"] = $filteredOptions;
                    $resultset["toastr_msg"] = "Generate all hired/separated employee report for `{$_filteredOption}`, a total of {$numRows} record(s) found.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate all hired/separated employee report for `{$_filteredOption}`, no filtered data found!";
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Generate hired/separated employee report, no filter by option data found!";
        }
        

        $logMessage = $resultset["toastr_msg"];
        $logState = $resultset["response"] ? "success":"error";
        $this->core_layout->setEventLog($logMessage, "generate", $logState, "gcchris");

        return $resultset;
    }

    public function generateManpowerReport(){
        $resultset = array();
        $arrData = array();
        $arr = array();
        $post = $this->input->post();
        if(isset($post["filter_by"]) && $post["filter_by"]){
            $additionalFilters = array();
            if(isset($post["company"]) && $post["company"]){ $additionalFilters["comp.id"] = $post["company"]; }
            if(isset($post["department"]) && $post["department"]){ $additionalFilters["dept.id"] = $post["department"]; }

            /*** if(isset($post["station"]) && $post["station"]){ $additionalFilters["loc.location_name"] = $post["station"]; } ***/
            if(isset($post["station"]) && $post["station"]){ $additionalFilters["dsl.station_id"] = $post["station"]; }

            $filterType = $post["filter_by"];
            $filteredOptions = array();
            $filteredOptions["filter_by"] = $filterType;
            if($filterType == "date_range"){
                if(isset($post[$filterType]) && $post[$filterType]){
                    $dateRange = explode(" - ", $post[$filterType]);
                    if(is_array($dateRange) && count($dateRange) == 2){
                        $dateStart = date("Y-m-d", strtotime($dateRange[0]));
                        $dateEnd = date("Y-m-d", strtotime($dateRange[1]));
        
                        $filteredOptions["date_start"] = $dateStart;
                        $filteredOptions["date_end"] = $dateEnd;

                        $_filteredStartDate = date("Y/m/d", strtotime($dateStart));
                        $_filteredEndDate = date("Y/m/d", strtotime($dateEnd));
                        $filteredOptions["start_date_formatted"] = $_filteredStartDate;
                        $filteredOptions["end_date_formatted"] = $_filteredEndDate;
        
                        $result = $this->employee->getActiveManpowerFilter($dateStart, $dateEnd, $additionalFilters);
                        if($result && $result->num_rows() > 0){
                            $numRows = $result->num_rows();

                            // added for department filter
                            foreach($result->result() as $key => $rs){
                                
                                if(isset($post["department"]) && $post["department"]){
                                    if(strpos($rs->department, ",")){
                                        $rs->department = 0; //stated department to '0' if no department selected in filter
                                    }
                                }else{
                                    $rs->department = 0;
                                }

                                $rs->location = isset($post["station"]) && $post["station"] ? $post['station'] : "";

                                $arrData[$key] = $rs;
                            }

                            foreach($arrData as $v){ $arr[] = $v; }

                            $resultset["response"] = true;
                            $resultset["data"] = $arr;
                            $resultset["count"] = $numRows;
                            $resultset["filtered_options"] = $filteredOptions;
                            $resultset["toastr_msg"] = "Generate active manpower report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}`, a total of {$numRows} record(s) found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Generate active manpower report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}`, no filtered data found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Generate active manpower report by date range, no data on date range filter option(s) found!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate active manpower report by date range, no filtered by data range option(s) found!";
                }
            }else{
                $result = $this->employee->getAllActiveManpowerFilter($additionalFilters);
                if($result && $result->num_rows() > 0){
                    $numRows = $result->num_rows();

                    // added for department filter
                    foreach($result->result() as $key => $rs){

                        if(isset($post["department"]) && $post["department"]){
                            if(strpos($rs->department, ",")){
                                $rs->department = 0; //stated department to '0' if no department selected in filter
                            }
                        }else{
                            $rs->department = 0;
                        }

                        if(isset($post["station"]) && $post["station"]){
                            $rs->location = $post['station'];
                        }

                        $arrData[$key] = $rs;
                    }

                    foreach($arrData as $v){ $arr[] = $v; }

                    $resultset["response"] = true;
                    $resultset["data"] = $arr;
                    $resultset["count"] = $numRows;
                    $resultset["filtered_options"] = $filteredOptions;
                    $resultset["toastr_msg"] = "Generate all active manpower report, a total of ({$numRows} record(s) found.)";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate all active manpower report, no filtered data found!";
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Generate all active manpower report, no filter by option data found!";
        }

        $logMessage = $resultset["toastr_msg"];
        $logState = $resultset["response"] ? "success":"error";
        $this->core_layout->setEventLog($logMessage, "generate", $logState, "gcchris");

        return $resultset;
    }

    public function generateManpowerByCompanyReport(){
        $resultset = array();
        $post = $this->input->post();

        if(isset($post["filter_by"]) && $post["filter_by"]){
            $filterType = $post["filter_by"];
            if($filterType == "date_range"){
                if(isset($post["date_start"], $post["date_end"], $post["company_id"]) &&
                ($post["date_start"] && $post["date_end"] && $post["company_id"])){
                    $data = $this->employee->getActiveManpowerByCompany($post["date_start"], $post["date_end"], $post["company_id"], $post['department_id'], $post['location_name']);
                    if($data){
                        $resultset["response"] = true;
                        $resultset["data"] = $data;
                    }else{ $resultset["response"] = false; }
                }else{ $resultset["response"] = false; }
            }else{
                $data = $this->employee->getAllActiveManpowerByCompany($post["company_id"], $post['department_id'], $post['location_name']);
                if($data){
                    $resultset["response"] = true;
                    $resultset["data"] = $data;
                }else{ $resultset["response"] = false; }
            }
        }

        return $resultset;
    }

    public function generateTrainingSeminarsReport(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post["filter_by"]) && $post["filter_by"]){
            $additionalFilters = array();
            if(isset($post["company"]) && $post["company"]){ $additionalFilters["comp.id"] = $post["company"]; }
            if(isset($post["department"]) && $post["department"]){ $additionalFilters["dept.id"] = $post["department"]; }
            if(isset($post["position"]) && $post["position"]){ $additionalFilters["pos.id"] = $post["position"]; }
            if(isset($post["training"]) && $post["training"]){ $additionalFilters["trn.training"] = $post["training"]; }

            $tempFilterBy = $post["filter_by"];
            $filteredOptions = array();
            $filteredOptions["filter_by"] = $tempFilterBy;
            if($tempFilterBy == "date_range"){
                if(isset($post[$tempFilterBy]) && $post[$tempFilterBy]){
                    $dateRange = explode(" - ", $post[$tempFilterBy]);
                    if(is_array($dateRange) && count($dateRange) == 2){
                        $dateStart = date("Y-m-d", strtotime($dateRange[0]));
                        $dateEnd = date("Y-m-d", strtotime($dateRange[1]));
        
                        $filteredOptions["date_start"] = $dateStart;
                        $filteredOptions["date_end"] = $dateEnd;
        
                        $_filteredStartDate = date("Y/m/d", strtotime($dateStart));
                        $_filteredEndDate = date("Y/m/d", strtotime($dateEnd));
                        $filteredOptions["start_date_formatted"] = $_filteredStartDate;
                        $filteredOptions["end_date_formatted"] = $_filteredEndDate;
                        $result = $this->employee->getActiveTrainingSeminarFilter($dateStart, $dateEnd, $additionalFilters);
                        if($result && $result->num_rows() > 0){
                            $numRows = $result->num_rows();
                            $resultset["response"] = true;
                            $resultset["data"] = $result->result();
                            $resultset["count"] = $numRows;
                            $filteredOptions["total_entries"] = $numRows;
                            $resultset["filtered_options"] = $filteredOptions;
                            $resultset["toastr_msg"] = "Generate trainings and seminars report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}`, a total of {$numRows} record(s) found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Generate trainings and seminars report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}`, no filtered data found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Generate trainings and seminars report by date range, no data on date range filter option(s) found!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate trainings and seminars report by date range, no filtered by date range option(s) found!";
                }
            }else{
                $result = $this->employee->getAllActiveTrainingSeminarFilter($additionalFilters);
                if($result && $result->num_rows() > 0){
                    $numRows = $result->num_rows();
                    $resultset["response"] = true;
                    $resultset["data"] = $result->result();
                    $resultset["count"] = $numRows;
                    $filteredOptions["total_entries"] = $numRows;
                    $resultset["filtered_options"] = $filteredOptions;
                    $resultset["toastr_msg"] = "Generate all trainings and seminars report, a total of {$numRows} record(s) found.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate all trainings and seminars report, no filtered data found!";
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Generate trainings and seminars report, no filter by option data found!";
        }

        $logMessage = $resultset["toastr_msg"];
        $logState = $resultset["response"] ? "success":"error";
        $this->core_layout->setEventLog($logMessage, "generate", $logState, "gcchris");

        return $resultset;
    }

    public function generateDriversLicenseReport(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post["filter_by"]) && $post["filter_by"]){
            $additionalFilters = array();
            if(isset($post["company"]) && $post["company"]){ $additionalFilters["comp.id"] = $post["company"]; }
            if(isset($post["department"]) && $post["department"]){ $additionalFilters["dept.id"] = $post["department"]; }
            if(isset($post["position"]) && $post["position"]){ $additionalFilters["pos.id"] = $post["position"]; }
            
            $tempFilterBy = $post["filter_by"];
            $filteredOptions = array();
            $filteredOptions["filter_by"] = $tempFilterBy;
            if($tempFilterBy == "date_range"){
                if(isset($post[$tempFilterBy]) && $post[$tempFilterBy]){
                    $dateRange = explode(" - ", $post[$tempFilterBy]);
                    if(is_array($dateRange) && count($dateRange) == 2){
                        $dateStart = date("Y-m-d", strtotime($dateRange[0]));
                        $dateEnd = date("Y-m-d", strtotime($dateRange[1]));
        
                        $filteredOptions["date_start"] = $dateStart;
                        $filteredOptions["date_end"] = $dateEnd;
        
                        $_filteredStartDate = date("Y/m/d", strtotime($dateStart));
                        $_filteredEndDate = date("Y/m/d", strtotime($dateEnd));
                        $filteredOptions["start_date_formatted"] = $_filteredStartDate;
                        $filteredOptions["end_date_formatted"] = $_filteredEndDate;
        
                        $result = $this->employee->getActiveDriversLicenseFilter($dateStart, $dateEnd, $additionalFilters);
                        if($result && $result->num_rows() > 0){
                            $numRows = $result->num_rows();
                            $resultset["response"] = true;
                            $resultset["data"] = $result->result();
                            $resultset["count"] = $numRows;
                            $filteredOptions["total_entries"] = $numRows;
                            $resultset["filtered_options"] = $filteredOptions;
                            $resultset["toastr_msg"] = "Generate drivers license report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}`, a total of {$numRows} record(s) found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Generate drivers license report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}`, no filtered data found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Generate drivers license report by date range, No data on date range filter option(s) found!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate drivers license report by date range, No filtered by date range option(s) found!";
                }
            }else{
                $result = $this->employee->getAllActiveDriversLicenseFilter($additionalFilters);
                if($result && $result->num_rows() > 0){
                    $numRows = $result->num_rows();
                    $resultset["response"] = true;
                    $resultset["data"] = $result->result();
                    $resultset["count"] = $numRows;
                    $filteredOptions["total_entries"] = $numRows;
                    $resultset["filtered_options"] = $filteredOptions;
                    $resultset["toastr_msg"] = "Generate all drivers license report, a total of {$numRows} record(s) found.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate all drivers license report, no filtered data found!";
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Generate drivers license report, no filter by option data found!";
        }
        
        $logMessage = $resultset["toastr_msg"];
        $logState = $resultset["response"] ? "success":"error";
        $this->core_layout->setEventLog($logMessage, "generate", $logState, "gcchris");

        return $resultset;
    }

    public function generateCertificateReport(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post["filter_by"]) && $post["filter_by"]){
            $additionalFilters = array();
            if(isset($post["company"]) && $post["company"]){ $additionalFilters["comp.id"] = $post["company"]; }
            if(isset($post["department"]) && $post["department"]){ $additionalFilters["dept.id"] = $post["department"]; }
            if(isset($post["position"]) && $post["position"]){ $additionalFilters["pos.id"] = $post["position"]; }
            if(isset($post["type"]) && $post["type"]){ $additionalFilters["licenses.type"] = $post["type"]; }
            if(isset($post["licenses"]) && $post["licenses"]){ $additionalFilters["cert.license_type"] = $post["licenses"]; }

            $tempFilterBy = $post["filter_by"];
            $filteredOptions = array();
            $filteredOptions["filter_by"] = $tempFilterBy;
            if($tempFilterBy == "date_range"){
                if(isset($post[$tempFilterBy]) && $post[$tempFilterBy]){
                    $dateRange = explode(" - ", $post[$tempFilterBy]);
                    if(is_array($dateRange) && count($dateRange) == 2){
                        $dateStart = date("Y-m-d", strtotime($dateRange[0]));
                        $dateEnd = date("Y-m-d", strtotime($dateRange[1]));
        
                        $filteredOptions["date_start"] = $dateStart;
                        $filteredOptions["date_end"] = $dateEnd;
        
                        $_filteredStartDate = date("Y/m/d", strtotime($dateStart));
                        $_filteredEndDate = date("Y/m/d", strtotime($dateEnd));
                        $filteredOptions["start_date_formatted"] = $_filteredStartDate;
                        $filteredOptions["end_date_formatted"] = $_filteredEndDate;
                        $result = $this->employee->getActiveCertificateFilter($dateStart, $dateEnd, $additionalFilters);
                        if($result && $result->num_rows() > 0){
                            $numRows = $result->num_rows();
                            $resultset["response"] = true;
                            $resultset["data"] = $result->result();
                            $resultset["count"] = $numRows;
                            $filteredOptions["total_entries"] = $numRows;
                            $resultset["filtered_options"] = $filteredOptions;
                            $resultset["toastr_msg"] = "Generate license and certification report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}`, a total of {$numRows} record(s) found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Generate license and certification report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}`, no filtered data found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Generate license and certification report by date range, no data on date range filter option(s) found!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate license and certification report by date range, no filtered by date range option(s) found!";
                }
            }else{
                $result = $this->employee->getAllActiveCertificateFilter($additionalFilters);
                if($result && $result->num_rows() > 0){
                    $numRows = $result->num_rows();
                    $resultset["response"] = true;
                    $resultset["data"] = $result->result();
                    $resultset["count"] = $numRows;
                    $filteredOptions["total_entries"] = $numRows;
                    $resultset["filtered_options"] = $filteredOptions;
                    $resultset["toastr_msg"] = "Generate all license and certification report, a total of {$numRows} record(s) found.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate all license and certification report, no filtered data found!";
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Generate license and certification report, no filter by option data found!";
        }

        $logMessage = $resultset["toastr_msg"];
        $logState = $resultset["response"] ? "success":"error";
        $this->core_layout->setEventLog($logMessage, "generate", $logState, "gcchris");

        return $resultset;
    }

    public function getStations(){
        $this->db->select('id, name as text');
        $this->db->where('is_active', 1);
        $this->db->where('status', 1);
        $this->db->from('gcctimeutility.location');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();

        return $query->result();
    }

    public function getSalaryPayinfoDatatableRequest(){
        $resultset = array();
        $post = $this->input->get();
        
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;

        $rowData = $this->getSalaryPayinfoRequest($limit, $offset);
        $rowCount = $this->getSalaryPayinfoRequestCount();

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;
        
        return $resultset;
    }

    public function getSalaryPayinfoRequest($limit, $offset){
        $this->db->select("UPPER(emp.lastname) as lastname, UPPER(emp.firstname) as firstname, UPPER(comp.description) as company_description, dept.code as dept_code, 
            dept.description as dept_description, pos.name as position, emp.basic_rate as salary_rate, GROUP_CONCAT(DISTINCT UPPER(loc.location_name)) as station");
        $this->db->from('gccmaster.tblemployees emp');
        $this->db->join('gcchris.tblcompanies comp', 'comp.id = emp.company_id', "LEFT");
        $this->db->join('gcchris.tbldepartments dept', 'dept.id = emp.department_id OR (dept.code = emp.department_id OR dept.description = emp.department_id)', "LEFT");
        $this->db->join('gcchris.tblposition pos', 'pos.id = emp.position OR pos.name = emp.position', "LEFT");
        $this->db->join('gcctimeutility.personnel per', 'per.biometric_id = emp.biometricno OR per.biometricno = emp.biometricno', "LEFT");
        $this->db->join('gcctimeutility.personnel_locations loc', 'loc.personnel_id = per.id', "LEFT");
        $this->db->where("emp.employee_status", "Active");
        if ($limit != -1) { $this->db->limit($limit, $offset); }
        $this->db->order_by("emp.lastname, emp.firstname", "ASC");
        $this->db->group_by("emp.id");
        $query = $this->db->get();
        if ($query->num_rows() > 0) { return $query->result(); }
        else { return array(); }
    }

    public function getSalaryPayinfoRequestCount(){
        $this->db->select("UPPER(emp.lastname) as lastname, UPPER(emp.firstname) as firstname, UPPER(comp.description) as company_description, dept.code as dept_code, 
            dept.description as dept_description, pos.name as position, emp.basic_rate as salary_rate, GROUP_CONCAT(DISTINCT UPPER(loc.location_name)) as station");
        $this->db->from('gccmaster.tblemployees emp');
        $this->db->join('gcchris.tblcompanies comp', 'comp.id = emp.company_id', "LEFT");
        $this->db->join('gcchris.tbldepartments dept', 'dept.id = emp.department_id OR (dept.code = emp.department_id OR dept.description = emp.department_id)', "LEFT");
        $this->db->join('gcchris.tblposition pos', 'pos.id = emp.position OR pos.name = emp.position', "LEFT");
        $this->db->join('gcctimeutility.personnel per', 'per.biometric_id = emp.biometricno OR per.biometricno = emp.biometricno', "LEFT");
        $this->db->join('gcctimeutility.personnel_locations loc', 'loc.personnel_id = per.id', "LEFT");
        $this->db->where("emp.employee_status", "Active");
        $this->db->group_by("emp.id");
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getVerifiedTimeSheetYearsData(){
        $arrData = array();
        $this->db->select("YEAR(`date`) as id, YEAR(`date`) as text");
        $this->db->where("verified", 1);
        $this->db->group_by("YEAR(`date`)");
        $this->db->order_by("YEAR(`date`)", 'desc');
        $qTemp = $this->db->get('gcctimeutility.timesheet');
        $currentDate = array("id"=>date("Y"), "text"=>date("Y"));
        if($qTemp->num_rows() > 0){
            $tempYear = array();
            $tempArray = $qTemp->result_array();
            foreach ($qTemp->result() as $value) {
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

    public function select2CompanyData(){
        $this->db->select("companies.id, companies.`code` `text`");
        $this->db->join('gccmaster.tblemployees emp', 'emp.company_id = companies.id', "INNER");
        $this->db->where("emp.employee_status", "Active");
        $this->db->group_by("companies.id");
        $this->db->order_by("`code`", "ASC");
        return $this->db->get("gcchris.tblcompanies companies")->result();
    }

    public function select2DepartmentData(){
        $this->db->select("departments.id, UPPER(IF(departments.`code` = departments.`description`, 
            departments.`description`, 
            CONCAT(departments.`code`,' | ', departments.`description`))) `text`, departments.*");
        $this->db->order_by("`code`", "ASC");
        return $this->db->get("gcchris.tbldepartments departments")->result();
    }

    public function getSelect2EmployeeData(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get["company_id"]) && $get["company_id"]){
            $departmentId = (isset($get["department_id"]) && $get["department_id"])? $get["department_id"]: 0;
            $this->db->select("a.id, CONCAT(UPPER(TRIM(a.firstname)), ' ', CASE WHEN UPPER(TRIM(a.middlename)) != 'N/A' AND UPPER(TRIM(a.middlename)) != 'NONE' AND TRIM(a.middlename) !='' AND a.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(a.middlename, 1, 1), '.') ELSE '' END,' ', UPPER(TRIM(a.lastname)), CASE WHEN UPPER(TRIM(a.suffix)) != 'N/A' AND UPPER(TRIM(a.suffix !='NONE')) AND a.suffix !='' AND
                a.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(a.suffix))) ELSE '' END) as employee_name");
            $this->db->from("gccmaster.tblemployees a");
            $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id", "LEFT");
            $this->db->where("a.employee_status", "Active");
            $this->db->where("a.company_id", $get["company_id"]);
            if($departmentId){ $this->db->where("a.department_id", $departmentId); }

            if (isset($get['q']) && $get['q']) {
                $this->db->group_start();
                $this->db->like("a.firstname", $get['q'], "both");
                $this->db->or_like("a.lastname", $get['q'], "both");
                $this->db->group_end();
            }
            $this->db->limit(25);
            $this->db->order_by("trim(a.firstname)", "ASC");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();

                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["employee_name"];
                    $resultarray[] = $data;
                }
            }
        }
        
        
        return array("results" => $resultarray);
    }

    public function getSelect2DepartmentData(){
        $get = $this->input->get();
        $resultarray = array();
        $resultarray["results"] = array();

        if(isset($get["company_id"]) && $get["company_id"]){
            $this->db->select("a.id, UPPER(IF(a.`code` = a.`description`, TRIM(a.`description`), TRIM(CONCAT(a.`code`,' | ', a.`description`)))) as text");
            $this->db->from("gcchris.tbldepartments a");
            $this->db->join("gccmaster.tblemployees b", "b.department_id = a.id", "INNER");
            $this->db->join("gcchris.tblcompanies c", "c.id = b.company_id", "INNER");
            $this->db->where("b.employee_status", "Active");
            $this->db->where("c.id", $get["company_id"]);
            if (isset($get['q']) && $get['q']) {
                $this->db->group_start();
                $this->db->like("a.code", $get['q'], "both");
                $this->db->or_like("a.description", $get['q'], "both");
                $this->db->group_end();
            }
            $this->db->limit(25);
            $this->db->group_by("a.id");
            $this->db->order_by("trim(a.code)", "ASC");
            $query = $this->db->get();

            if ($query->num_rows() > 0) { $resultarray["results"] = $query->result_array(); }
        }
        return $resultarray;
    }

    public function generateLateReport(){
        $post = $this->input->post();
        $resultset = array();
        $arrFilter = array();

        $hasDepartment = isset($post["department"]) && $post["department"];

        if(isset($post["company"]) && $post["company"]){
            $empIds = array();
            $this->db->select("emp.id");
            $this->db->from($this->tblEmployees." as emp");
            $this->db->join($this->companyTable." as comp", "comp.id = emp.company_id");
            $this->db->where("emp.company_id", $post["company"]);
            if($hasDepartment){
                $this->db->where("emp.department_id", $post["department"]);
            }
            $this->db->order_by("emp.id", "ASC");
            $this->db->group_by("emp.id");
            $qData = $this->db->get();

            if($qData->num_rows() > 0){ foreach ($qData->result() as $emp) { $empIds[] = $emp->id; } }
            if(!empty($empIds) && !isset($post["employee"])){ $post["employee"] = $empIds; }
        }

        if(isset($post["employee"]) && $post["employee"]){
            $filterBy = $post["filter_by"];
            $employeeIds = $post["employee"];

            $startDate = null;
            $endDate = null;
            
            if(isset($post["company"]) && $post["company"]){
                $qCompany = $this->db->get_where("gcchris.tblcompanies", array("id"=>$post["company"]));
                if($qCompany->num_rows() == 1){
                    $cRow = $qCompany->row();
                    $arrFilter["company_code"] = $cRow->code;
                }
            }

            if(isset($post["payroll_group"]) && is_array($post["payroll_group"]) && count($post["payroll_group"]) > 0){
                $this->db->select("GROUP_CONCAT(DISTINCT TRIM(UPPER(REPLACE(description, '\t', '')))) as description");
                $this->db->from("payroll.payroll_group");
                $this->db->where_in("id", $post["payroll_group"]);
                $this->db->order_by("description", "ASC");
                $qPayrollGroup = $this->db->get();

                if($qPayrollGroup->num_rows() == 1){
                    $pgRow = $qPayrollGroup->row();
                    $arrFilter["payroll_group"] = $pgRow->description;
                }
            }

            if($filterBy == "date_range"){
                $filterDates = explode(" - ", $post[$filterBy]);
                if(is_array($filterDates) && count($filterDates) == 2){
                    $startDate = Date("Y-m-d", strtotime($filterDates[0]));
                    $endDate = Date("Y-m-d", strtotime($filterDates[1]));
                }
                
                $arrFilter["filter_by"] = "Date Range";
            }else{
                $tempDatex = $post["filter_year"]."-".$post["filter_month"]."-01";
                $timeStamp = strtotime($tempDatex);
                
                $startDate = Date("Y-m-d", strtotime("first day of this month", $timeStamp));
                $endDate = Date("Y-m-d", strtotime("last day of this month", $timeStamp));
                $arrFilter["filter_by"] = "Month";
            }
            
            if($startDate && $endDate && (is_array($employeeIds) && count($employeeIds) > 0)){
                $fsDate = Date("F d, Y", strtotime($startDate));
                $feDate = Date("F d, Y", strtotime($endDate));
                $arrFilter["filter_date"] = "{$fsDate} - {$feDate}";

                $this->db->select("MAX(date) as max_date");
                $this->db->from("gcctimeutility.timesheet");
                $this->db->where("has_shift", 1);
                /*** $this->db->where("verified", 1); ***/
                $this->db->limit(1);
                $qTempMax = $this->db->get();
                $tempMaxDate = $qTempMax->row()->max_date ? $qTempMax->row()->max_date: null;
                $this->db->reset_query();

                $this->db->select("CONCAT(UPPER(TRIM(emp.firstname)), ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN UPPER(CONCAT(SUBSTR(emp.middlename, 1, 1), '.')) ELSE ''
                END,' ', UPPER(TRIM(emp.lastname)),
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
                END) as employee_name, emp.idno, IFNULL(UPPER(pos.name), 'NO ASSIGNED POSITION') as position,
                COALESCE(SUM(IF(ts.date >= emp.date_start && ts.am_late > 0, 1, 0))) + COALESCE(SUM(IF(ts.date >= emp.date_start && ts.pm_late > 0, 1, 0))) as late_total, 
                CONCAT(
                    GROUP_CONCAT(DISTINCT IF(ts.date >= emp.date_start && ts.am_late > 0, CONCAT(ts.date, ' ', ts.am_in), '')),
                    GROUP_CONCAT(DISTINCT IF(ts.date >= emp.date_start && ts.pm_late > 0, CONCAT(ts.date, ' ', ts.pm_in), ''))
                ) as attendance_logs, MAX(ts.date) as max_date, emp.date_start");
                $this->db->from("gcctimeutility.timesheet as ts");
                $this->db->join("gccmaster.tblemployees as emp", "emp.id = ts.emp_id", "INNER");
                $this->db->join("gcchris.tblposition as pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
                $this->db->where("ts.has_shift", 1);
                /*** $this->db->where("ts.verified", 1); ***/
                $this->db->group_start();
                $this->db->where("DATE(ts.date) >=", $startDate);
                $this->db->where("DATE(ts.date) <=", $endDate);
                $this->db->group_end();
                $this->db->where_in("ts.emp_id", $employeeIds);
                $this->db->order_by("emp.lastname", "ASC");
                $this->db->group_by("ts.emp_id");
                $qAttendance = $this->db->get();
                $ctrCount = $qAttendance->num_rows();

                if($ctrCount > 0){
                    $maxDate = $qAttendance->row_array()["max_date"];
                    $resultset["data"] = $qAttendance->result_array();
                    $resultset["response"] = true;
                    $resultset["filters"] = $arrFilter;
                    $resultset["toastr_msg"] = "Last verified attendance date on `{$maxDate}`, A total of ({$ctrCount}) employee late attendance record/s found!";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = $tempMaxDate ? "No data available for the selected date range. Verified data is only up to `{$tempMaxDate}`." : "No late attendance record/s found!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Filter option/s with given parameters not found!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Filter option/s with given parameters, No employee data found!";
        }

        return $resultset;
    }

    public function selectPayrollGroup(){
        $get = $this->input->get();
        $arrData = array();
        $resultset = array();
        $companyId = (isset($get["company_id"]) && $get["company_id"])? $get["company_id"]: 0;
        if($companyId || $companyId == 0){
            $this->db->select("id, description as text, employee_id");
            $this->db->from("payroll.payroll_group");
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
                    $this->db->from("gccmaster.tblemployees");
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

    public function generateAbsenteeReport(){
        $this->load->model("gcctime/timesheet_model", "ts_model");
        $post = $this->input->post();
        $resultset = array();
        $arrFilter = array();

        $hasDepartment = isset($post["department"]) && $post["department"];

        if(isset($post["company"]) && $post["company"]){
            $empIds = array();
            $this->db->select("emp.id");
            $this->db->from($this->tblEmployees." as emp");
            $this->db->join($this->companyTable." as comp", "comp.id = emp.company_id");
            $this->db->where("emp.company_id", $post["company"]);
            
            if($hasDepartment){
                $this->db->where("emp.department_id", $post["department"]);
            }

            $this->db->order_by("emp.id", "ASC");
            $this->db->group_by("emp.id");
            $qData = $this->db->get();

            if($qData->num_rows() > 0){ foreach ($qData->result() as $emp) { $empIds[] = $emp->id; } }
            if(!empty($empIds) && !isset($post["employee"])){ $post["employee"] = $empIds; }
        }

        if(isset($post["employee"]) && $post["employee"]){
            $filterBy = $post["filter_by"];
            $employeeIds = $post["employee"];

            $startDate = null;
            $endDate = null;

            if(isset($post["company"]) && $post["company"]){
                $qCompany = $this->db->get_where("gcchris.tblcompanies", array("id"=>$post["company"]));
                if($qCompany->num_rows() == 1){
                    $cRow = $qCompany->row();
                    $arrFilter["company_code"] = $cRow->code;
                }
            }

            if(isset($post["payroll_group"]) && is_array($post["payroll_group"]) && count($post["payroll_group"]) > 0){
                $this->db->select("GROUP_CONCAT(DISTINCT TRIM(UPPER(REPLACE(description, '\t', '')))) as description");
                $this->db->from("payroll.payroll_group");
                $this->db->where_in("id", $post["payroll_group"]);
                $this->db->order_by("description", "ASC");
                $qPayrollGroup = $this->db->get();

                if($qPayrollGroup->num_rows() == 1){
                    $pgRow = $qPayrollGroup->row();
                    $arrFilter["payroll_group"] = $pgRow->description;
                }
            }

            if($filterBy == "date_range"){
                $filterDates = explode(" - ", $post[$filterBy]);
                if(is_array($filterDates) && count($filterDates) == 2){
                    $startDate = Date("Y-m-d", strtotime($filterDates[0]));
                    $endDate = Date("Y-m-d", strtotime($filterDates[1]));
                }
                
                $arrFilter["filter_by"] = "Date Range";
            }else{
                $tempDatex = $post["filter_year"]."-".$post["filter_month"]."-01";
                $timeStamp = strtotime($tempDatex);
                
                $startDate = Date("Y-m-d", strtotime("first day of this month", $timeStamp));
                $endDate = Date("Y-m-d", strtotime("last day of this month", $timeStamp));
                $arrFilter["filter_by"] = "Month";
            }
    
            if($startDate && $endDate && (is_array($employeeIds) && count($employeeIds) > 0)){
                $this->db->select("MAX(date) as max_date");
                $this->db->from("gcctimeutility.timesheet");
                $this->db->where("has_shift", 1);
                /*** $this->db->where("verified", 1); ***/
                $this->db->limit(1);
                $qTempMax = $this->db->get();
                $tempMaxDate = $qTempMax->row()->max_date ? $qTempMax->row()->max_date: null;
                $this->db->reset_query();

                $employeeShiftRecord = array();
                $this->db->select("emp.id, ssr.shift_resource");
                $this->db->from("gcctimeutility.shift_schedule_resource as ssr");
                $this->db->join("gcctimeutility.personnel as pr", "pr.shift_id = ssr.shift_id", "left");
                $this->db->join("gccmaster.tblemployees as emp", "emp.biometricno = pr.biometricno OR emp.biometricno = pr.biometric_id", "left");
                $this->db->where_in("emp.id", $employeeIds);
                $qtemp = $this->db->get();
                if ($qtemp->num_rows() > 0){
                    foreach ($qtemp->result() as $kv) {
                        $shiftId = @unserialize($kv->shift_resource);
                        if(is_array($shiftId) && count($shiftId) > 0){
                            $schedules = $this->db
                                ->where_in("id", $shiftId)
                                ->get("gcctimeutility.shift_schedule_list")
                                ->result();
    
                            $schedules_obj = array_reduce($schedules,
                                function ($carry, $obj) {
                                    $key = $obj->weekday;
                                    $carry[$key] = $obj;
                                    return $carry;
                                }, []);
                            
                                $employeeShiftRecord[$kv->id] = $schedules_obj;
                        }
                            
                    }
                }

                $newEmployeeRecord = array();
                $employeeDates = array();
                $employeeLogDates = array();

                if(is_array($employeeShiftRecord) && !empty($employeeShiftRecord)){
                    $interval = DateInterval::createFromDateString('1 day');
                    $dateStart = new DateTime($startDate);
                    $dateEnd = new DateTime($endDate);
                    $dateEnd->modify("+1 day");

                    $period = new DatePeriod($dateStart, $interval, $dateEnd);
                    $propShift = array("am_start", "am_end", "pm_start", "pm_end");

                    foreach ($employeeShiftRecord as $key => $schedule) {
                        foreach ($period as $dt) {
                            $weekday = strtolower($dt->format("l"));
                            $date = $dt->format("Y-m-d");
                            $md5Date = md5($date);

                            $holidayResponse = (object) $this->ts_model->getCurrentDateIsHoliday($date);
                            if($holidayResponse->is_holiday === false && isset($schedule[$weekday]) && $schedule[$weekday]){
                                $weekdaySchedule = $schedule[$weekday];

                                $tempSchedule = new stdClass();
                                foreach ($propShift as $prop) { $tempSchedule->{$prop} = $weekdaySchedule->{$prop}; }

                                $shiftSchedule = $this->ts_model->getCustomizedShiftScheduleByDate($date, $key);
                                if(isset($shiftSchedule->has_shift) && $shiftSchedule->has_shift == 1){
                                    $nSchedule = $shiftSchedule->schedule;
                                    foreach ($propShift as $prop) { $tempSchedule->{$prop} = $nSchedule->{$prop}; }
                                }

                                $isWholeDay = true;
                                $tempSchedule->am_start = $tempSchedule->am_start ? $tempSchedule->am_start : "00:00:00";
                                $tempSchedule->am_end = $tempSchedule->am_end ? $tempSchedule->am_end : "00:00:00";
                                $tempSchedule->pm_start = $tempSchedule->pm_start ? $tempSchedule->pm_start : "00:00:00";
                                $tempSchedule->pm_end = $tempSchedule->pm_end ? $tempSchedule->pm_end : "00:00:00";
                                
                                $amDateTimeLog = $tempSchedule->am_start != "00:00:00" && $tempSchedule->am_end != "00:00:00" ? $date." ".$tempSchedule->am_start."~".$date." ".$tempSchedule->am_end : null;
                                $pmDateTimeLog = $tempSchedule->pm_start != "00:00:00" && $tempSchedule->pm_end != "00:00:00" ? $date." ".$tempSchedule->pm_start."~".$date." ".$tempSchedule->pm_end : null;

                                if (isset($tempSchedule->am_start) && isset($tempSchedule->am_end) && isset($tempSchedule->pm_start) && isset($tempSchedule->pm_end)){
                                    if (($tempSchedule->am_start == null || $tempSchedule->am_start == "00:00:00")
                                        && ($tempSchedule->am_end == null || $tempSchedule->am_end == "00:00:00")){
                                        $isWholeDay = false;
                                    }
                                    if (($tempSchedule->pm_start == null || $tempSchedule->pm_start == "00:00:00")
                                        && ($tempSchedule->pm_end == null || $tempSchedule->pm_end == "00:00:00")){
                                        $isWholeDay = false;
                                    }
                                }
                                if($amDateTimeLog){ $employeeLogDates[$key][$md5Date][] = $amDateTimeLog; }
                                if($pmDateTimeLog){ $employeeLogDates[$key][$md5Date][] = $pmDateTimeLog; }

                                $employeeDates[$key][] = $date;
                                $newEmployeeRecord[$key][$md5Date] = $isWholeDay ? 1: 0.5;
                            }
                        }
                    }
                }
                
                $updateEmployeeAbsences = array();

                if(is_array($employeeDates) && !empty($employeeDates)){
                    foreach ($employeeDates as $empId => $dates) {
                        $this->db->select("GROUP_CONCAT(DISTINCT DATE(ts.date)) as dates, MAX(ts.date) as max_date, emp.date_start");
                        $this->db->from("gcctimeutility.timesheet as ts");
                        $this->db->join("gccmaster.tblemployees as emp", "emp.id = ts.emp_id", "INNER");
                        $this->db->join("gcchris.tblposition as pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
                        $this->db->where("ts.is_holiday", 0);
                        $this->db->where("ts.has_shift", 1);
                        /*** $this->db->where("ts.verified", 1); ***/
                        $this->db->group_start();
                        $this->db->where("DATE(ts.date) >=", $startDate);
                        $this->db->where("DATE(ts.date) <=", $endDate);
                        $this->db->group_end();
                        $this->db->where("ts.emp_id", $empId);
                        $this->db->order_by("ts.date", "ASC");
                        $qdates = $this->db->get();

                        if($qdates->num_rows() > 0){
                            $qdates = $qdates->row();
                            $qMaxDate = $qdates->max_date;
                            $qDateStart = $qdates->date_start;

                            $tsDates = $qdates->dates;
                            $tsDates = explode(",", $tsDates);
                            $tsDates = array_map("trim", $tsDates);
                            $tsDates = array_filter($tsDates);
                            $tsDates = array_unique($tsDates);

                            $arrLogs = array();
                            $updateEmployeeAbsences[$empId]["attendance_logs"] = "";
                            $updateEmployeeAbsences[$empId]["absentee_dates"] = array();
                            foreach ($dates as $dt) {
                                if(strtotime($dt) >= strtotime($qDateStart) && strtotime($dt) <= strtotime($qMaxDate) && !in_array($dt, $tsDates)){
                                    $updateEmployeeAbsences[$empId]["absentee_total"] = isset($updateEmployeeAbsences[$empId]["absentee_total"]) && $updateEmployeeAbsences[$empId]["absentee_total"] ? $updateEmployeeAbsences[$empId]["absentee_total"] : 0;
                                    $updateEmployeeAbsences[$empId]["absentee_total"] += $newEmployeeRecord[$empId][$md5Date];
                                    
                                    if(is_array($employeeLogDates[$empId][$md5Date]) && count($employeeLogDates[$empId][$md5Date]) > 0){
                                        foreach ($employeeLogDates[$empId][$md5Date] as $dtx) { $arrLogs[] = $dtx; }
                                    }
                                    $updateEmployeeAbsences[$empId]["absentee_dates"][] = $dt;
                                }
                            }

                            if(is_array($arrLogs) && !empty($arrLogs)){
                                $updateEmployeeAbsences[$empId]["attendance_logs"] = implode(",", $arrLogs);
                            }
                        }
                    }
                }


                $fsDate = Date("F d, Y", strtotime($startDate));
                $feDate = Date("F d, Y", strtotime($endDate));
                $arrFilter["filter_date"] = "{$fsDate} - {$feDate}";

                $this->db->select("ts.emp_id, CONCAT(UPPER(TRIM(emp.firstname)), ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN UPPER(CONCAT(SUBSTR(emp.middlename, 1, 1), '.')) ELSE ''
                END,' ', UPPER(TRIM(emp.lastname)),
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
                END) as employee_name, emp.idno, IFNULL(UPPER(pos.name), 'NO ASSIGNED POSITION') as position, 
                COALESCE(SUM(
                    IF(ts.date >= emp.date_start,
                        IF(ISNULL(ts.shift_am_start) && ISNULL(ts.shift_am_end), 0, IF(ISNULL(ts.am_in) && ISNULL(ts.am_out), '0.5', 0))
                    , 0)
                )) + COALESCE(SUM(
                    IF(ts.date >= emp.date_start,
                        IF(ISNULL(ts.shift_pm_start) && ISNULL(ts.shift_pm_end), 0, IF(ISNULL(ts.pm_in) && ISNULL(ts.pm_out), '0.5', 0))
                    , 0)
                )) as absentee_total,
                CONCAT(
                    GROUP_CONCAT(DISTINCT
                        IF(ISNULL(ts.shift_am_start) && ISNULL(ts.shift_am_end), '', IF(ISNULL(ts.am_in) && ISNULL(ts.am_out),
                            CONCAT(ts.date, ' ', ts.shift_am_start, '~', ts.date, ' ', ts.shift_am_end), ''))
                    ),
                    GROUP_CONCAT(DISTINCT
                        IF(ISNULL(ts.shift_pm_start) && ISNULL(ts.shift_pm_end), '', IF(ISNULL(ts.pm_in) && ISNULL(ts.pm_out),
                            CONCAT(ts.date, ' ', ts.shift_pm_start, '~', ts.date, ' ', ts.shift_pm_end), ''))
                    )
                ) as attendance_logs,
                CONCAT(
                    GROUP_CONCAT(DISTINCT
                        IF(ISNULL(ts.shift_am_start) && ISNULL(ts.shift_am_end), '', IF(ISNULL(ts.am_in) && ISNULL(ts.am_out),
                            ts.date, ''))
                    ),
                    GROUP_CONCAT(DISTINCT
                        IF(ISNULL(ts.shift_pm_start) && ISNULL(ts.shift_pm_end), '', IF(ISNULL(ts.pm_in) && ISNULL(ts.pm_out),
                            ts.date, ''))
                    )
                ) as attendance_dates,
                MAX(ts.date) as max_date, emp.date_start");
                $this->db->from("gcctimeutility.timesheet as ts");
                $this->db->join("gccmaster.tblemployees as emp", "emp.id = ts.emp_id", "INNER");
                $this->db->join("gcchris.tblposition as pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
                $this->db->where("ts.is_holiday", 0);
                $this->db->where("ts.has_shift", 1);
                /*** $this->db->where("ts.verified", 1); ***/
                $this->db->group_start();
                $this->db->where("DATE(ts.date) >=", $startDate);
                $this->db->where("DATE(ts.date) <=", $endDate);
                $this->db->group_end();
                $this->db->where_in("ts.emp_id", $employeeIds);
                $this->db->order_by("emp.lastname", "ASC");
                $this->db->group_by("ts.emp_id");
                $qAttendance = $this->db->get();
                $ctrCount = $qAttendance->num_rows();

                if($ctrCount > 0){
                    $maxDate = $qAttendance->row()->max_date;
                    $qData = array();
                    $loaReference = array();
                    foreach($qAttendance->result() as $attx){
                        $qDateStart = $attx->date_start;

                        $tempTotal = isset($updateEmployeeAbsences[$attx->emp_id]["absentee_total"]) ? $updateEmployeeAbsences[$attx->emp_id]["absentee_total"] : 0;
                        $tempLogs = isset($updateEmployeeAbsences[$attx->emp_id]["attendance_logs"]) ? $updateEmployeeAbsences[$attx->emp_id]["attendance_logs"] : "";

                        $attDate = array_unique(array_filter(explode(",", $attx->attendance_dates)));
                        $attDatex = array_filter($attDate, function($date) use ($qDateStart) { return strtotime($date) >= strtotime($qDateStart); });

                        if(isset($updateEmployeeAbsences[$attx->emp_id]["absentee_dates"])){
                            $attDatex = array_merge($attDatex, $updateEmployeeAbsences[$attx->emp_id]["absentee_dates"]);
                        }
                        
                        if(is_array($attDatex) && count($attDatex) > 0){
                            foreach ($attDatex as $dt) {
                                $this->db->select("date_from, date_to, employee, reference_no, type");
                                $this->db->from("gcceforms.loa");
                                $this->db->where("DATE(date_from) >=", $dt);
                                $this->db->where("employee", $attx->emp_id);
                                $this->db->where("status", "Approved");
                                $this->db->order_by("date_from", "ASC");
                                $approvedLoa = $this->db->get();

                                if($approvedLoa->num_rows() > 0){
                                    foreach ($approvedLoa->result() as $appLoa) {
                                        $dateFrom = date("Y-m-d", strtotime($appLoa->date_from));
                                        $dateTo = date("Y-m-d", strtotime($appLoa->date_to));
                                        $cDate = date("Y-m-d", strtotime($dt));
                                        if(strtotime($cDate) >= strtotime($dateFrom) && strtotime($cDate) <= strtotime($dateTo)){
                                            $loaReference[$appLoa->employee][$cDate] = $appLoa->reference_no;
                                        }
                                    }
                                }
                            }
                        }

                        $newLogs00 = explode(",", $attx->attendance_logs);
                        $newLogs00x = array_filter($newLogs00, function($log) use ($qDateStart) {
                            $log = explode("~", $log);
                            $nDatex = date("Y-m-d", strtotime($log[0]));
                            return strtotime($nDatex) >= strtotime($qDateStart);
                        });
                        
                        $newLogs00x = array_filter($newLogs00x);
                        $newLogs00x = array_unique($newLogs00x);

                        $newLogs01 = explode(",", $tempLogs);
                        $newLogs01 = array_filter($newLogs01);
                        $newLogs01 = array_unique($newLogs01);

                        $newLogs00x = array_merge($newLogs00x, $newLogs01);
                        $newLogs00x = array_unique($newLogs00x);
                        $newLogs00x = array_filter($newLogs00x);

                        $dateTime = array();
                        foreach ($newLogs00x as $key => $log) {
                            $dtLog = explode("~", $log);
                            $dateTime[$key] = strtotime($dtLog[0]);
                        }

                        $dateTime = array_unique($dateTime);
                        $dateTime = array_filter($dateTime);
                        array_multisort($dateTime, SORT_ASC, SORT_NUMERIC, $newLogs00x);
                        
                        $timestamp = array_map('strtotime', $attDatex); 
                        array_multisort($timestamp, SORT_ASC, $attDatex);

                        $attx->attendance_logs = implode(",", $newLogs00x);
                        $attx->attendance_dates = implode(",", $attDatex);
                        $attx->absentee_total += $tempTotal;
                        $qData[] = $attx;
                    }

                    $resultset["data"] = $qData;
                    $resultset["loa_reference"] = $loaReference;
                    $resultset["response"] = true;
                    $resultset["filters"] = $arrFilter;
                    $resultset["toastr_msg"] = "Last verified attendance date on `{$maxDate}`, A total of ({$ctrCount}) employee absentee attendance record/s found!";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = $tempMaxDate ? "No data available for the selected date range. Verified data is only up to `{$tempMaxDate}`." : "No absentee attendance record/s found!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Filter option/s with given parameters not found!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Filter option/s with given parameters, No employee data found!";
        }

        return $resultset;
    }

    public function getDropdownSelectData() {
        $resultset = array();
        $this->db->select("id, description as text");
        $companies = $this->db->get($this->companyTable);

        $this->db->select("id, description as text");
        $departments = $this->db->get($this->departmentTable);

        $this->db->select("id, name as text");
        $positions = $this->db->get($this->positionTable);

        $resultset["dropdown_company"] = ($companies->num_rows() > 0) ? $companies->result() : array();
        $resultset["dropdown_department"] = ($departments->num_rows() > 0) ? $departments->result() : array();
        $resultset["dropdown_position"] = ($positions->num_rows() > 0) ? $positions->result() : array();

        return $resultset;
    }

    public function getSelect2Companies(){
        $this->db->select('id, code text');
        $this->db->where('is_archived', 0)        ;
        return $this->db->get($this->companyTable)->result();
    }
    
    public function getSelect2Departments(){
        $this->db->select('id, code text');
        $this->db->where('is_archived', 0);
        return $this->db->get($this->departmentTable)->result();
    }

    public function getSelect2Year(){
        $this->db->select('YEAR(date_start) as id, YEAR(date_start) as text');
        $this->db->where('is_archived', 0);
        $this->db->where('employee_status', 'Active');
        $this->db->where('YEAR(date_start) >=', 2017);
        $this->db->group_by('text');
        $this->db->order_by('YEAR(date_start)', 'DESC');
        return $this->db->get($this->tblEmployees)->result();
    }

    public function generateAttritionReport(){
        $post = $this->input->post();

        $months = array(
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

        $hasDepartment = $post['generate_group'] == 2 ? true : false;
        $hasMonth = isset($post['filter_month']) && $post['filter_month'] ? true : false;
        $dateParam = $hasMonth ? $post['filter_year']."-".$post['filter_month'] : $post['filter_year'].'-'.'12';
        $deptSelect = ($hasDepartment) ? "c.id as department_id, UPPER(TRIM(c.code)) as deptcode, UPPER(TRIM(c.description)) as deptdesc, IFNULL(UPPER(c.code), 'No Department name') as department," : "";

        $firstDay = date('Y-m-d', strtotime('first day of January ' . date($post['filter_year'])));
        $lastDay = date("Y-m-t", strtotime($dateParam));

        $tempHired = array();
        $tempSeperated = array();
        $resultSet = array();

        if(in_array('Hired', $post['to_generate_group'])){
            $this->db->from($this->tblEmployees.' a');
            $this->db->select("$deptSelect b.id as company_id, IFNULL(UPPER(b.code), 'No Company Name') as company, MONTH(a.date_start) as month, COUNT(a.id) as total_hired");
            $this->db->join($this->companyTable.' b', 'b.id = a.company_id', 'LEFT');

            if($hasDepartment){
                $this->db->join($this->departmentTable.' c', 'c.id = a.department_id OR c.code = a.department_id', 'LEFT');

                if(isset($post['department']) && $post['department']){
                    $this->db->where('a.department_id', $post['department']);
                }
            }

            $this->db->where('a.is_archived', 0);
            $this->db->where('DATE(a.date_start) >= ', $firstDay);
            $this->db->where('DATE(a.date_start) <= ', $lastDay);

            $this->db->group_start();
            $this->db->where('a.date_start is NOT NULL', null, false);
            $this->db->where('a.date_start != ', '0000-00-00');
            $this->db->group_end();

            if(isset($post['company']) && $post['company']){
                $this->db->where('a.company_id', $post['company']);
            }

            $groupedGenerated = $hasDepartment ? ', c.code' : 'b.code';
            $this->db->group_by("MONTH(a.date_start), $groupedGenerated");
            $this->db->order_by('MONTH(a.date_start)');
            $this->db->order_by('b.code', 'ASC');
            $hiredTemp = $this->db->get();

            $arrHired = array();
            if($hiredTemp->num_rows() > 0){
                foreach ($hiredTemp->result() as $rs) {
                    $rs->company = strtoupper($rs->company);
                    $m = strtolower(date('F', mktime(0, 0, 0, $rs->month, 10)));
                    $rs->$m = $rs->total_hired;
                    $indexLabel = $hasDepartment ? md5(trim(strtolower($rs->department))) : $rs->company_id;

                    $label = "";

                    if($hasDepartment){
                        if($rs->deptcode != $rs->deptdesc){
                            $label = "$rs->deptcode | $rs->deptdesc";
                        }else{
                            $label = $rs->deptcode;
                        }
                    }else{
                        $label = $rs->company;
                    }

                    $arrHired[$indexLabel]['label'] = $label;
                    $arrHired[$indexLabel]['company'] = $rs->company;

                    if($hasDepartment){
                        $arrHired[$indexLabel]['department'] = $rs->department;
                    }

                    $arrHired[$indexLabel]['type'] = 'Hired';
                    $arrHired[$indexLabel][$m] = $rs->total_hired;
                }
                array_push($tempHired, $arrHired);
            }

            $this->db->reset_query();
        }

        if(in_array('Seperated', $post['to_generate_group'])){
            $this->db->from($this->tblEmployees.' a');
            $this->db->select("$deptSelect b.id as company_id, IFNULL(UPPER(b.code), 'No Company Name') as company, MONTH(a.date_end) as month, COUNT(a.id) as total_seperated");
            $this->db->join($this->companyTable.' b', 'b.id = a.company_id', 'LEFT');

            if($hasDepartment){
                $this->db->join($this->departmentTable.' c', 'c.id = a.department_id OR c.code = a.department_id', 'LEFT');

                if(isset($post['department']) && $post['department']){
                    $this->db->where('a.department_id', $post['department']);
                }
            }

            $this->db->where('a.is_archived', 0);
            $this->db->where('DATE(a.date_end) >= ', $firstDay);
            $this->db->where('DATE(a.date_end) <= ', $lastDay);
            $this->db->group_start();
            $this->db->where('a.date_end is NOT NULL', null, false);
            $this->db->where('a.date_end != ', '0000-00-00');
            $this->db->group_end();

            if(isset($post['company']) && $post['company']){
                $this->db->where('a.company_id', $post['company']);
            }

            $groupedGenerated = $hasDepartment ? ', c.code' : 'b.code';
            $this->db->group_by("MONTH(a.date_end), $groupedGenerated");
            $this->db->order_by('MONTH(a.date_end)');
            $this->db->order_by('b.code', 'ASC');
            $seperatedTemp = $this->db->get();

            $arrSeperated = array();
            if($seperatedTemp->num_rows() > 0){
                foreach ($seperatedTemp->result() as $key => $rs) {
                    $rs->company = strtoupper($rs->company);
                    $m = strtolower(date('F', mktime(0, 0, 0, $rs->month, 10)));
                    $rs->$m = $rs->total_seperated;
                    $indexLabel = $hasDepartment ? md5(trim(strtolower($rs->department))) : $rs->company_id;

                    $label = "";

                    if ($hasDepartment){
                        if ($rs->deptcode != $rs->deptdesc){ $label = "$rs->deptcode | $rs->deptdesc"; }
                        else { $label = $rs->deptcode; }
                    } else { $label = $rs->company; }

                    $arrSeperated[$indexLabel]['label'] = $label;
                    $arrSeperated[$indexLabel]['company'] = $rs->company;

                    if($hasDepartment){
                        $arrSeperated[$indexLabel]['department'] = $rs->department;
                    }

                    $arrSeperated[$indexLabel]['type'] = 'Seperated';
                    $arrSeperated[$indexLabel][$m] = $rs->total_seperated;
                }
                array_push($tempSeperated, $arrSeperated);

            }

            $this->db->reset_query();
        }

        $temp = array_merge($tempHired, $tempSeperated);
        $data = array();
        $label = array();
        $type = array();

        // restructured merged array data for hired and seperated
        foreach($temp as $key => $rs){
            foreach($rs as $k => $v){
                array_push($data, $v); //pushed to a single array to be injected to table column
                $label[$k]  = $v['label'];
                $type[$k] = $v['type'];
            }
        }

        $label  = array_column($data, 'label');
        $type = array_column($data, 'type');

        // sorted all hired and seperated report by label and type
        array_multisort($label, SORT_ASC, $type, SORT_ASC, $data);

        $list = array();
        foreach($months as $_month){
            $monthData = array();

            $_dateParam = $post['filter_year'].'-'.$_month['index'];

            $_first = date('Y-m-d', strtotime($_dateParam));
            $_last = date('Y-m-t', strtotime($_dateParam));
            $_company = isset($post['company']) && $post['company'] ? $post['company'] : null;
            $_department = isset($post['department']) && $post['department'] ? $post['department'] : null;

            $monthData['d'] = $_month["name"];
            $monthData['hired'] = (int) $this->getNewlyHired($_first, $_last, $_company, $hasDepartment, $_department);
            $monthData['seperated'] = (int) $this->getSeperated($_first, $_last, $_company, $hasDepartment, $_department);
            $monthData['date'] = $_first;

            array_push($list, $monthData);
        }

        $resultSet['data'] = $data;
        $resultSet['company'] = (isset($post['company']) && $post['company']) ? $this->getGeneratedCompany($post['company']) : 'All';
        $resultSet['coverage'] = date('M d, Y', strtotime($firstDay)) . ' - ' . date('M d, Y', strtotime($lastDay));
        $resultSet['generated'] = $post['to_generate_group'];
        $resultSet['chartData'] = $list;

        return $resultSet;
    }

    public function getGeneratedCompany($id){
        $this->db->select('code');
        $this->db->from($this->companyTable);
        $this->db->where('id', $id);
        $query = $this->db->get()->row();

        return $query->code;
    }

    public function getNewlyHired($first, $last, $company = null, $hasDepartment = false, $department = null){
        $this->db->select('COUNT(a.date_start) as total');
        $this->db->from($this->tblEmployees.' a');
        $this->db->join($this->companyTable.' b', 'b.id = a.company_id', 'LEFT');

        if ($hasDepartment){
            $this->db->join($this->departmentTable.' c', 'c.id = a.department_id OR c.code = a.department_id', 'LEFT');
            if ($department){
                $this->db->where('a.department_id', $department);
            }
        }

        $this->db->where('DATE(a.date_start) >=', $first);
        $this->db->where('DATE(a.date_start) <=', $last);

        if ($company){ $this->db->where('a.company_id', $company); }

        $this->db->where('a.is_archived', 0);
        $groupedGenerated = $hasDepartment ? ', c.code' : 'b.code';
        $this->db->group_by("MONTH(a.date_start), $groupedGenerated");
        $query = $this->db->get();

        $total = 0;
        if ($query->num_rows() > 0){
            foreach($query->result() as $rs){ $total += $rs->total; }
        }

        $this->db->reset_query();
        return $total;
    }

    public function getSeperated($first, $last, $company = null, $hasDepartment = false, $department = null){
        $this->db->select('COUNT(a.date_end) as total');
        $this->db->from($this->tblEmployees.' a');
        $this->db->join($this->companyTable.' b', 'b.id = a.company_id', 'LEFT');

        if ($hasDepartment){
            $this->db->join($this->departmentTable.' c', 'c.id = a.department_id OR c.code = a.department_id', 'LEFT');
            if ($department){ $this->db->where('a.department_id', $department); }
        }

        $this->db->where('DATE(a.date_end) >=', $first);
        $this->db->where('DATE(a.date_end) <=', $last);

        if ($company){ $this->db->where('a.company_id', $company); }

        $this->db->where('a.is_archived', 0);

        $groupedGenerated = $hasDepartment ? ', c.code' : 'b.code';
        $this->db->group_by("MONTH(a.date_end), $groupedGenerated");
        $query = $this->db->get();

        $total = 0;
        if ($query->num_rows() > 0){
            foreach ($query->result() as $rs){ $total += $rs->total; }
        }

        $this->db->reset_query();
        return $total;
    }

    public function generateAttritionChartReport(){
        $post = $this->input->post();
        $resultSet = array();

        $months = array(
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

        $year = isset($post['filter_year']) && $post['filter_year'] ? $post['filter_year'] : '2024';
        $company = isset($post['company']) && $post['company'] ? $post['company'] : null;

        $firstDay = date('Y-m-d', strtotime('first day of January ' . date($post['filter_year'])));
        $lastDay = date("Y-m-t", strtotime($year.'-'.'12'));

        $list = array();
        foreach($months as $_month){
            $monthData = array();

            $_dateParam = $year.'-'.$_month['index'];

            $_first = date('Y-m-d', strtotime($_dateParam));
            $_last = date('Y-m-t', strtotime($_dateParam));

            $monthData['d'] = $_month["name"];
            $monthData['hired'] = (int) $this->getNewlyHired($_first, $_last, $company);
            $monthData['seperated'] = (int) $this->getSeperated($_first, $_last, $company);
            $monthData['date'] = $_first;

            array_push($list, $monthData);
        }

        $resultSet['data'] = $list;
        $resultSet['coverage'] = date('M d, Y', strtotime($firstDay)) . ' - ' . date('M d, Y', strtotime($lastDay));
        $resultSet['company'] = (isset($post['company']) && $post['company']) ? $this->getGeneratedCompany($post['company']) : 'All';;

        return $resultSet;
    }

    public function getTenureship($date){
        $now = date('Y-m-d');
        $html = '';

        $timeStampHired = strtotime($date);
        $timeStampNow = strtotime($now);

        $hiredYear = date('Y', $timeStampHired);
        $nowYear = date('Y', $timeStampNow);
        
        $hiredMonth = date('m', $timeStampHired);
        $nowMonth = date('m', $timeStampNow);

        $totalYear = $nowYear - $hiredYear;
        $totalMonths = $nowMonth - $hiredMonth;
        $totalDiff = (($nowYear - $hiredYear) * 12) + ($nowMonth - $hiredMonth);

        $year = intval($totalYear) > 0 ? $totalYear : '';
        $month = intval($totalMonths > 0) ? $totalMonths : '';

        if($year != '' && $year == 1){ $html .= $year.' year'; }
        elseif ($year != '' && $year > 1) { $html .= $year.' years'; }
        if($year != '' && $month != ''){ $html .= ' and '; }
        if($month != '' && $month == 1){ $html .= $month.' month'; }
        elseif ($month != '' && $month > 1) { $html .= $month.' months'; }

        return array(
            'tenured' => $html,
            'totalMonths' => $totalDiff
        );
    }

    public function getEmployeeNoStations($companyId=null, $dataOnly=false){
        $post = $this->input->post();
        $resultset = array();

        if($companyId && $dataOnly ){ $post["company"] = $companyId; }
        if(isset($post["company"]) && $post["company"]){
            $resultset["response"] = true;
            $this->db->select("emp.id, UCASE(
                           CONCAT(
                               emp.firstname, ' ',
                               CASE
                                   WHEN emp.middlename IS NOT NULL AND emp.middlename != '' AND emp.middlename != 'N/A' AND emp.middlename != 'NONE' THEN CONCAT(' ', substr(emp.middlename,1,1),'.')
                                   ELSE ''
                               END,
                               ' ', emp.lastname,
                               CASE
                                   WHEN emp.suffix IS NOT NULL AND emp.suffix != '' AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                                   ELSE ''
                               END
                           )
                       ) as employee_name, 
                IFNULL(UPPER(pos.name), 'NO ASSIGNED POSITION') as position, 
                IFNULL(UPPER(dept.description), 'NO ASSIGNED DEPARTMENT') as department");
            $this->db->from($this->tblEmployees." as emp");
            $this->db->join($this->defaultStationTable." as dsl", "dsl.employee_id = emp.id", "LEFT");
            $this->db->join($this->departmentTable." as dept", "dept.id = emp.department_id OR (dept.code = emp.department_id OR dept.description = emp.department_id)", "LEFT");
            $this->db->join($this->positionTable." as pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
            $this->db->where("dsl.id", null);
            $this->db->where("emp.employee_status", "Active");
            $this->db->where("emp.company_id", $post["company"]);
            $this->db->order_by("emp.lastname", "ASC");
            $this->db->order_by("emp.firstname", "ASC");
            $qRecords = $this->db->get();

            $resultset["rows"] = $qRecords->result_array();
            $resultset["id"] = $post["company"];
        }else{
            $resultset["response"] = false;
        }

        if($dataOnly){
            if($resultset["response"] === true){ return $resultset["rows"]; }
            else{ return array(); }
        }else{ return $resultset; }
    }

    public function setEmployeesWithoutStations(){
        $post = $this->input->post();
        $resultset = array();
        $records = array();
        $ctrAdded = 0;

        if(isset($post["station"]) && $post["station"]){
            if(isset($post["employee_id"]) && is_array($post["employee_id"]) && count($post["employee_id"]) > 0){
                foreach ($post["employee_id"] as $empId) {
                    $dslTemp = $this->db->get_where($this->defaultStationTable, array("employee_id"=>$empId));
                    if($dslTemp->num_rows() == 0){
                        $siteName = $this->db
                            ->get_where($this->tblAppLocationSites, array("id"=>$post["station"]))
                            ->row('site_name');

                        $tempRow = array();
                        $tempRow["station_id"] = $post["station"];
                        $tempRow["station_description"] = $siteName;
                        $tempRow["employee_id"] = $empId;
                        $tempRow["created_at"] = date("Y-m-d H:i:s");
                        $added = $this->db->insert($this->defaultStationTable, $tempRow);
                        if($added){ $ctrAdded++; }
                    }
                }
            }
        }

        if(isset($post["company"]) && $post["company"]){
            $records = $this->getEmployeeNoStations($post["company"], true);
        }
        
        if($ctrAdded > 0){ 
            $resultset["response"] = true; 
            $resultset["rows"] = $records;
        }else{ 
            $resultset["response"] = false;
        }
        return $resultset;
    }

    public function exportLog(){
        $post = $this->input->post();
        $action = ['print' => 'Printed', 'excel' => 'Made Excel file'][$post['exportType']] ?? 'Exported';
        $filters = implode(', ', array_map(function($k, $v) { return "$k: $v"; }, array_keys($post['filter']), $post['filter']));
        $this->core_layout->setEventLog("$action {$post['type']} with filters: $filters", "generate", "success", "gcchris", "user");
        return $post;
    }
}
