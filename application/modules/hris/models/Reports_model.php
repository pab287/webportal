<?php defined('BASEPATH') || exit('No direct script access allowed');
class Reports_model extends CI_Model{
    protected $tblHolidays = "gcchris.tblholidays";
    protected $tblEmployees = "gccmaster.tblemployees";
    protected $companyTable = "gcchris.tblcompanies";
    protected $departmentTable = "gcchris.tbldepartments";
    protected $positionTable = "gcchris.tblposition";
    protected $employeeTrainingsTable = "gcchris.tbltrainings";
    protected $defaultStationTable = "gcchris.default_station_location";
    protected $tblAppLocationSites = "gcctimeutility.app_location_sites";
    protected $tblPersonnel = "gcctimeutility.personnel";
    protected $tblPersonnelLocation = "gcctimeutility.personnel_locations";
    protected $tblDefaultLocation = "gcchris.default_station_location";
    protected $tblSalaryHistory = 'gcchris.tblsalaries';

    protected $sbrPaymentTable = "gcchris.sss_sbr_payments";
    protected $sbrContributionTable = "gcchris.sss_sbr_contributions";

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

    function generateEmployeeReport($export){
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($post);

        if(in_array("supervisor", $post->fields)) {
            $key = array_search("supervisor", $post->fields);
            $post->fields[$key] = 'IF(UPPER(emp.level) = \'SUPERVISORY\' OR UPPER(emp.level) = \'MANAGERIAL\' OR UPPER(emp.level) = \'EXECUTIVE\', \'CHARLES ANTHONY M. DUMANCAS\',
                (SELECT TRIM(UCASE(
                    CONCAT(firstname, \' \',
                        CASE WHEN middlename IS NOT NULL AND middlename != \'\' THEN CONCAT(\' \', substr(middlename,1,1),\'.\')
                        ELSE \'\' END, \' \', lastname,
                    CASE WHEN suffix IS NOT NULL AND suffix != \'\' AND suffix != \'N/A\' AND suffix != \'NONE\' THEN CONCAT(\' \', suffix)
                        ELSE \'\' END)
                    )) FROM gccmaster.tblemployees WHERE id = IF(REPLACE(
                    SUBSTRING_INDEX(SUBSTRING_INDEX(emp.supervisor_meta, \';\', 1),\':\',-1),
                    \'"\',\'\') = \'supervisory\', REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(emp.supervisor_meta, \';\', 2),\':\',-1),\'"\',\'\'),
                    \'\'
                ))
            ) as supervisor';
        }

        if(in_array("manager", $post->fields)) {
            $key = array_search("manager", $post->fields);
            $post->fields[$key] = '(SELECT TRIM(UCASE(
                CONCAT(firstname, \' \',
                    CASE WHEN middlename IS NOT NULL AND middlename != \'\' THEN CONCAT(\' \', substr(middlename,1,1),\'.\')
                    ELSE \'\' END, \' \', lastname,
                CASE WHEN suffix IS NOT NULL AND suffix != \'\' AND suffix != \'N/A\' AND suffix != \'NONE\' THEN CONCAT(\' \', suffix)
                    ELSE \'\' END)
                )) FROM gccmaster.tblemployees WHERE id = IF(REPLACE(
                SUBSTRING_INDEX(SUBSTRING_INDEX(emp.supervisor_meta, \';\', 3),\':\',-1),
                \'"\',\'\') = \'managerial\', REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(emp.supervisor_meta, \';\', 4),\':\',-1),\'"\',\'\'),
                \'\'
            )) as manager';
        }

        
        if (in_array('personnel.is_flexi', $post->fields)) {
            $key = array_search("personnel.is_flexi", $post->fields);
            $post->fields[$key] = 'CASE
                WHEN personnel.is_flexi = 0 THEN "Regular - 2 IN AND 2 OUT"
                WHEN personnel.is_flexi = 1 THEN "Flexi - 1 IN AND 1 OUT"
                WHEN personnel.is_flexi = 2 THEN "Drivers - 1 IN AND 1 OUT"
                WHEN personnel.is_flexi = 3 THEN "Super Flexi - 1 IN OR 1 OUT"
                WHEN personnel.is_flexi = 4 THEN "Default - NO TIME IN OR OUT"
                END as is_flexi';
        }

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
                "condition" => "company.id = emp.company_id AND company.is_archived = 0 AND company.exclude = 0",
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
                // 'condition' => 'emp.id = salaries.emp_id AND salaries.sal_date = (SELECT MAX( sal_date) latest_date FROM gcchris.tblsalaries WHERE emp_id=emp.id)',
                'condition' => 'emp.id = salaries.emp_id AND salaries.id = (SELECT id FROM gcchris.tblsalaries WHERE emp_id = emp.id AND sal_date = ( SELECT MAX(sal_date) FROM gcchris.tblsalaries WHERE emp_id = emp.id ) ORDER BY id DESC LIMIT 1)',
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
        if(isset($post->exportType)){
            $exportType = str_replace('Html5', '', $post->exportType);
        }
        $messageStart = intval($export) == 1 ? "Exported as <strong>{$exportType}</strong>" : "Generated";
        $action = intval($export) == 1 ? 'export' : 'generate';
        $logMessage = "{$messageStart} employee report with criteria: <strong>$criteria</strong>. Result count: <strong>$recordCount</strong>";
        $this->core_layout->setEventLog($logMessage, $action, 'success', "gcchris", 'user');
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
        $select = "
            emp.id,
            UCASE(IF(company.code IS NULL, emp.company_id ,company.code)) as company,
            UCASE(IF(pos.name IS NULL, emp.position, TRIM(pos.name))) as position,
            CAST(emp.idno AS DECIMAL(10)) as idno,
            UCASE(
                CONCAT(emp.firstname, ' ', emp.middlename, ' ', emp.lastname,
                    CASE
                        WHEN emp.suffix IS NOT NULL AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                    ELSE '' END
                )
            ) as name,
            emp.date_start as date_hired,
            DATE_ADD(emp.date_start, INTERVAL 3 MONTH) as firstEvaluation,
            DATE_ADD(emp.date_start, INTERVAL 5 MONTH) as finalEvaluation,
            emp.date_end_prob as end_of_contract,
            DATEDIFF(DATE_ADD(emp.date_start, INTERVAL 5 MONTH), CURDATE()) daysBeforeEvaluation,
            emp.level,
            emp.supervisor_meta
        ";

        $joinArr = array(
            array('table' => 'gcchris.tblcompanies as company', 'condition' => 'emp.company_id = company.id', 'option' => 'LEFT'),
            array('table' => 'gcchris.tbldepartments as dep', 'condition' => 'emp.department_id = dep.id', 'option' => 'LEFT'),
            array('table' => 'gcchris.tblposition as pos', 'condition' => 'emp.position = pos.id', 'option' => 'LEFT')
        );

        $where = array(
            "emp.work_status" => $work_status,
            "emp.employee_status" => "Active",
            "company.is_archived" => 0,
            "company.exclude" => 0
        );

        $this->db->select($select);
        $this->db->where($where);
        foreach ($joinArr as $join) {
            $this->db->join($join['table'], $join['condition'], $join['option']);
        }

        $query = $this->db->get($this->tblEmployees . " emp")->result_array();

        $res = array();

        foreach($query as $row) {

            if ($row['id'] == 2) { // Charles Anthony M. Dumancas - Final Boss 😎
                $head_name = "N/A";
            } else {
                // Kng indi sa supervisor_meta ko ma look up ky hambal nla sa employee data butungon ang head, indi sa department
                $sup_val = $row['supervisor_meta'];

                // check kng nka serialize or plain ID
                if (is_string($sup_val) && @unserialize($sup_val) !== false || $sup_val === 'a:0:{}') {

                    // Serialized -> unserialize it
                    $supervisory_data = unserialize($sup_val);

                    if (is_array($supervisory_data)) {
                        $priority_head = $supervisory_data['supervisory'] ?? $supervisory_data['managerial'] ?? null;
                        $head_id = (int)$priority_head;

                        $head_name = $this->get_head_by_id($head_id);
                    }
                } else {
                    // Plain ID
                    $head_id = (int)$sup_val;

                    $head_name = $this->get_head_by_id($head_id);
                }
            }

            $row['head'] = $head_name;
            $row['supervisor_meta'] = @unserialize($row['supervisor_meta']);
            $res[] = $row;
        }

        $resultSet['data'] = $res;
        return $resultSet;
    }

    public function getExpiringEmployees_old($export, $work_status){
        $tableConfig = $this->input->post();
        $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
        $search = $pageOptions->search;

        $select = "
            UCASE(IF(company.code IS NULL, emp.company_id ,company.code)) as company,
            UCASE(IF(pos.name IS NULL, emp.position, TRIM(pos.name))) as position,
            CAST(emp.idno AS DECIMAL(10)) as idno,
            UCASE(
                CONCAT(emp.firstname, ' ', emp.middlename, ' ', emp.lastname,
                    CASE
                        WHEN emp.suffix IS NOT NULL AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                    ELSE '' END
                )
            ) as name,
            emp.date_start as date_hired,
            DATE_ADD(emp.date_start, INTERVAL 3 MONTH) as firstEvaluation,
            DATE_ADD(emp.date_start, INTERVAL 5 MONTH) as finalEvaluation,
            emp.date_end_prob as end_of_contract,
            DATEDIFF(DATE_ADD(emp.date_start, INTERVAL 5 MONTH), CURDATE()) daysBeforeEvaluation,
            emp.level,
            emp.supervisor_meta
        ";

        $joinArr = array(
            array('table' => 'gcchris.tblcompanies as company', 'condition' => 'emp.company_id = company.id', 'option' => 'LEFT'),
            array('table' => 'gcchris.tbldepartments as dep', 'condition' => 'emp.department_id = dep.id', 'option' => 'LEFT'),
            array('table' => 'gcchris.tblposition as pos', 'condition' => 'emp.position = pos.id', 'option' => 'LEFT')
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

        $query = $this->db->get($this->tblEmployees . " emp")->result_array();

        $res = array();

        foreach($query as $row) {

            if ($row['level'] == 'EXECUTIVE') {
                // Automatic they're own boss of themselves 😎
                $head_name = $row['name'];
            } else {
                // Kng indi sa supervisor_meta ko ma look up ky hambal nla sa employee data butungon ang head, indi sa department
                $sup_val = $row['supervisor_meta'];

                // check kng nka serialize or plain ID
                if (is_string($sup_val) && @unserialize($sup_val) !== false || $sup_val === 'a:0:{}') {

                    // Serialized -> unserialize it
                    $supervisory_data = unserialize($sup_val);

                    if (is_array($supervisory_data)) {
                        $priority_head = $supervisory_data['supervisory'] ?? $supervisory_data['managerial'] ?? null;
                        $head_id = (int)$priority_head;

                        $head_name = $this->get_head_by_id($head_id);
                    }
                } else {
                    // Plain ID
                    $head_id = (int)$sup_val;

                    $head_name = $this->get_head_by_id($head_id);
                }
            }

            $row['head'] = $head_name;
            $row['supervisor_meta'] = @unserialize($row['supervisor_meta']);
            $res[] = $row;
        }

        $resultSet['data'] = $res;
        $resultSet['recordsTotal'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $searchFields, $joinArr);
        $resultSet['recordsFiltered'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $searchFields, $joinArr);
        if (intval($export) == 1){
            $logMessage = "Exported Expiring ". $work_status. " Employees as <strong>".$tableConfig['exportType']."</strong> with result count: <strong>".$resultSet['recordsTotal']."</strong>";
            $this->core_layout->setEventLog($logMessage, "export", 'success', "gcchris", 'user');
            $search=false;
        }
        if ($search && $search != '') {
            $this->core_layout->setEventLog("User searched for: "."'<strong>".$search."</strong>'"." in <strong>Expiring ".$work_status." Employees</strong>. System found: <strong>".$resultSet['recordsTotal']." results.</strong>", "search", 'success', "gcchris", 'user');
        }
        return $resultSet;
    }

    public function get_head_by_id($head_id) {
        if ($head_id != 0) {
            $this->db->select("
                UCASE(
                    CONCAT(
                        firstname, ' ',
                        CASE 
                            WHEN middlename IS NOT NULL AND middlename != '' 
                                THEN CONCAT(LEFT(middlename, 1), '. ')
                            ELSE ''
                        END,
                        lastname,
                        CASE
                            WHEN suffix IS NOT NULL AND suffix != 'N/A' AND suffix != 'NONE' 
                                THEN CONCAT(' ', suffix)
                            ELSE ''
                        END
                    )
                ) AS name
            ");
            $this->db->from($this->tblEmployees);
            $this->db->where('id', $head_id);
            $query = $this->db->get()->row_array();

            $res = is_array($query) && !empty($query) ? $query['name'] : null;
        } else {
            $res = null;
        }

        return $res;
    }

    public function getCompanyCollection(){
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        $this->db->select('id, code text');
        $this->db->like('CONCAT(description, code)', $q, 'both');
        $this->db->where("is_archived", 0);
        $this->db->where("exclude", 0);
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
        $filtersString = ' Filters applied: ';
        $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
        $search = $pageOptions->search;
        $generate = $tableConfig['generate'];
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
            $filtersString .= 'Company: <strong>' . $company . '</strong>, ';
        }

        if (isset($tableConfigStd->filter->department) && !empty($tableConfigStd->filter->department)) {
            $department = $this->db->where('id', $tableConfigStd->filter->department)->get('gcchris.tbldepartments')->row('code');
            $where['IF(dep.id IS NULL, emp.department_id, dep.code)='] = $department;
            $filtersString .= 'Department: <strong>' . $department . '</strong>, ';
        }

        if (isset($tableConfigStd->filter->position) && $tableConfigStd->filter->position) {
            $position = $this->db->where('id', $tableConfigStd->filter->position)->get('gcchris.tblposition')->row('name');
            $where['IF(pos.id IS NULL, emp.position, pos.name)='] = $position;
            $filtersString .= 'Position: <strong>' . $position . '</strong>, ';
        }

        $select = "IF(comp.id IS NULL, emp.company_id, comp.code) company,
            IF(dep.id IS NULL, emp.department_id, dep.code) department,
            CONCAT(emp.firstname,' ', emp.middlename, ' ', emp.lastname,
                CASE
                    WHEN emp.suffix IS NOT NULL AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                    ELSE ''
                END) employees_name,
            salaries.sal_rate,
            salaries.sal_remarks,
            salaries.sal_date,
            emp.work_status,
            emp.employee_status,
            emp.date_start,
            IF(pos.name IS NULL OR pos.name = '', emp.position, pos.name) AS position,
            ROUND(DATEDIFF(CURDATE(), emp.date_start) / 30) as tenure";
            
        $joinArr = array(
            array('table' => 'gcchris.tblcompanies comp',
                'condition' => 'comp.code = emp.company_id OR comp.id = emp.company_id',
                'option' => 'LEFT'),
            array('table' => 'gcchris.tbldepartments dep',
                'condition' => 'dep.id = emp.department_id',
                'option' => 'LEFT'),
            array('table' => 'gcchris.tblsalaries salaries',
                'condition' => 'emp.id = salaries.emp_id AND salaries.id = (SELECT id FROM gcchris.tblsalaries WHERE emp_id=emp.id AND is_archived = 0 ORDER BY id DESC, DATE(add_date) DESC LIMIT 1)',
                'option' => 'INNER'),
            array('table' => 'gcchris.tblposition pos',
                'condition' => 'pos.id = emp.position',
                'option' => 'LEFT'),
        );

        $this->db->select($select);
        $this->db->where($where);
        $this->db->where("comp.is_archived", 0);
        $this->db->where("comp.exclude", 0);
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
        if (intval($export) === 0 && $pageOptions->length > -1) {
            $this->db->limit($pageOptions->length, $pageOptions->start);
        }
        $this->db->group_by('emp.id');
        $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);

        $query = $this->db->get($this->tblEmployees . " emp");

        if($query->num_rows() > 0){
            $result = $query->result();

            /*** $arrData = array();
            foreach($query->result() as $key => $rs){
                $tenured = (object) $this->getTenureship($rs->date_start);
                $rs->tenureship = $tenured->tenured;

                $arrData[$key] = $rs;
            }
            foreach ($arrData as $v) { $result[] = $v; } ***/
        }

        $resultSet['data'] = $result;
        $resultSet['sql'] = $this->db->last_query();
        $resultSet['recordsTotal'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $searchField, $joinArr);
        $resultSet['recordsFiltered'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $searchField, $joinArr);
       
        if ($export && $export == 1) {
            $logMessage = "Exported <strong>Employee salary range</strong>.{$filtersString} Salary range: <strong>" . number_format($salary_from, 2) . " - " . number_format($salary_to, 2) . "</strong> Export type: <strong>{$tableConfig['exportType']}</strong> with result count: <strong>{$resultSet['recordsTotal']}</strong>";
            $this->core_layout->setEventLog($logMessage, "export", 'success', "gcchris", 'user');
            $search = false;
        }

        if($search && $search !== '') {
            $logMessage = "User searched for: '<strong>{$search}</strong>' in <strong>Employee salary range</strong>.{$filtersString} Salary range:<strong> " . number_format($salary_from, 2) . " - " . number_format($salary_to, 2) . "</strong>. System found: <strong>{$resultSet['recordsTotal']}</strong> results.";
            $this->core_layout->setEventLog($logMessage, "export", 'success', "gcchris", 'user');
        }else{
            if(isset($generate) && $generate == 'true' && !$export && !$export == 1) {
                $logMessage = "Generated <strong>Employee salary range</strong>.{$filtersString} Salary range: <strong>" . number_format($salary_from, 2) . " - " . number_format($salary_to, 2) . "</strong> with result count: <strong>{$resultSet['recordsTotal']}</strong>";
                $this->core_layout->setEventLog($logMessage, "generate", 'success', "gcchris", 'user');
            }
        }
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
        $resultFilter = "Filter applied: ";
        $resultset = array();
        $post = $this->input->post();
        $additionalFilters = array();
        if(isset($post["company"]) && $post["company"]){ $additionalFilters["comp.id"] = $post["company"]; $resultFilter .= "Company: <strong>" . $this->getCompanyById($post["company"])->description."</strong> "; }
        if(isset($post["department"]) && $post["department"]){ $additionalFilters["dept.id"] = $post["department"]; $resultFilter .= "Department: <strong>" . $this->getDepartmentById($post["department"])->description."</strong> "; }
        if(isset($post["position"]) && $post["position"]){ $additionalFilters["pos.id"] = $post["position"]; $resultFilter .= "Position: <strong>" . $this->getPositionById($post["position"])->name."</strong> "; }
        if(isset($post["sort_by"]) && $post["sort_by"]){ $sortOrder["sort_by"] = $post["sort_by"]; }
        if(isset($post["sort_order"]) && $post["sort_order"]){ $sortOrder["sort_order"] = $post["sort_order"]; }
        if($resultFilter == "Filter applied: "){ $resultFilter = ""; }
        if(isset($post["filter_by"]) && $post["filter_by"]){
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
                            $resultset["toastr_msg"] = "Generate comprehensive report for `{$_filteredOption}` employees by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}` {$resultFilter}, a total of <strong>{$numRows}</strong> record(s) found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Generate comprehensive report for `{$_filteredOption}` employees by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}` {$resultFilter}, no filtered data found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Generate comprehensive report for hire/separated employees {$resultFilter}, no data on date range filter option(s) found!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate comprehensive report for hire/separated employees {$resultFilter}, no filtered by option(s) found!";
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
                    $resultset["toastr_msg"] = "Generate all hired/separated employee report for `{$_filteredOption}` {$resultFilter}, a total of <strong>{$numRows}</strong> record(s) found.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate all hired/separated employee report for `{$_filteredOption}` {$resultFilter}, no filtered data found!";
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Generate hired/separated employee report {$resultFilter}, no filter by option data found!";
        }
        

        $logMessage = $resultset["toastr_msg"];
        $logState = $resultset["response"] ? "success":"error";
        $this->core_layout->setEventLog($logMessage, "generate", $logState, "gcchris");

        return $resultset;
    }

    public function generateManpowerReport(){
        $resultFilter = "Filter applied: ";
        $resultset = array();
        $arrData = array();
        $arr = array();
        $post = $this->input->post();
        $additionalFilters = array();
        if(isset($post["company"]) && $post["company"]){ $additionalFilters["comp.id"] = $post["company"]; $resultFilter .= "Company: <strong>" . $this->getCompanyById($post["company"])->description."</strong> "; }
        if(isset($post["department"]) && $post["department"]){ $additionalFilters["dept.id"] = $post["department"]; $resultFilter .= "Department: <strong>" . $this->getDepartmentById($post["department"])->description."</strong> "; }

        /*** if(isset($post["station"]) && $post["station"]){ $additionalFilters["loc.location_name"] = $post["station"]; } ***/
        if(isset($post["station"]) && $post["station"]){ $additionalFilters["dsl.station_id"] = $post["station"]; $resultFilter .= "Station: <strong>" . $this->getStationById($post["station"])->site_name."</strong> "; }
        if($resultFilter == "Filter applied: "){ $resultFilter = ""; }
        $filterType = $post["filter_by"];
        $filteredOptions = array();
        $filteredOptions["filter_by"] = $filterType;
        if(isset($post["filter_by"]) && $post["filter_by"]){
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
                            $resultset["toastr_msg"] = "Generate active manpower report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}` {$resultFilter}, a total of <strong>{$numRows}</strong> record(s) found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Generate active manpower report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}` {$resultFilter}, no filtered data found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Generate active manpower report by date range {$resultFilter}, no data on date range filter option(s) found!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate active manpower report by date range {$resultFilter}, no filtered by data range option(s) found!";
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
                    $resultset["toastr_msg"] = "Generate all active manpower report {$resultFilter}, a total of (<strong>{$numRows}</strong> record(s) found.)";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate all active manpower report {$resultFilter}, no filtered data found!";
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Generate all active manpower report {$resultFilter}, no filter by option data found!";
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
        $resultFilter = "Filter applied: ";
        $resultset = array();
        $post = $this->input->post();
        if(isset($post["filter_by"]) && $post["filter_by"]){
            $additionalFilters = array();
            if(isset($post["company"]) && $post["company"]){ $additionalFilters["comp.id"] = $post["company"]; $resultFilter .= "Company: <strong>" . $this->getCompanyById($post["company"])->description."</strong> "; }
            if(isset($post["department"]) && $post["department"]){ $additionalFilters["dept.id"] = $post["department"]; $resultFilter .= "Department: <strong>" . $this->getDepartmentById($post["department"])->description."</strong> "; }
            if(isset($post["position"]) && $post["position"]){ $additionalFilters["pos.id"] = $post["position"]; $resultFilter .= "Position: <strong>" . $this->getPositionById($post["position"])->name."</strong> "; }
            if(isset($post["training"]) && $post["training"]){ $additionalFilters["trn.training"] = $post["training"]; $resultFilter .= "Training: <strong>" . $post["training"]." </strong> "; }
            if($resultFilter == "Filter applied: "){ $resultFilter = ""; }
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
                            $resultset["toastr_msg"] = "Generate trainings and seminars report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}` {$resultFilter}, a total of <strong>{$numRows}</strong> record(s) found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Generate trainings and seminars report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}` {$resultFilter}, no filtered data found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Generate trainings and seminars report by date range {$resultFilter}, no data on date range filter option(s) found!";
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
                    $resultset["toastr_msg"] = "Generate all trainings and seminars report {$resultFilter}, a total of <strong>{$numRows}</strong> record(s) found.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate all trainings and seminars report {$resultFilter}, no filtered data found!";
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
        $resultFilter = "Filter applied: ";
        if(isset($post["filter_by"]) && $post["filter_by"]){
            $additionalFilters = array();
            if(isset($post["company"]) && $post["company"]){ $additionalFilters["comp.id"] = $post["company"]; $resultFilter .= "Company: <strong>" . $this->getCompanyById($post["company"])->description."</strong> "; }
            if(isset($post["department"]) && $post["department"]){ $additionalFilters["dept.id"] = $post["department"]; $resultFilter .= "Department: <strong>" . $this->getDepartmentById($post["department"])->description."</strong> "; }
            if(isset($post["position"]) && $post["position"]){ $additionalFilters["pos.id"] = $post["position"]; $resultFilter .= "Position: <strong>" . $this->getPositionById($post["position"])->name."</strong> "; }
            if($resultFilter == "Filter applied: "){ $resultFilter = ""; }
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
                            $resultset["toastr_msg"] = "Generate drivers license report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}` {$resultFilter}, a total of <strong>{$numRows}</strong> record(s) found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Generate drivers license report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}` {$resultFilter}, no filtered data found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Generate drivers license report by date range {$resultFilter}, No data on date range filter option(s) found!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate drivers license report by date range {$resultFilter}, No filtered by date range option(s) found!";
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
                    $resultset["toastr_msg"] = "Generate all drivers license report {$resultFilter}, a total of <strong>{$numRows}</strong> record(s) found.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate all drivers license report {$resultFilter}, no filtered data found!";
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
        $resultFilter = "Filter applied: ";
        $post = $this->input->post();
        if(isset($post["filter_by"]) && $post["filter_by"]){
            $additionalFilters = array();
            if(isset($post["company"]) && $post["company"]){ $additionalFilters["comp.id"] = $post["company"]; $resultFilter .= "Company: <strong>" . $this->getCompanyById($post["company"])->description."</strong> "; }
            if(isset($post["department"]) && $post["department"]){ $additionalFilters["dept.id"] = $post["department"]; $resultFilter .= "Department: <strong>" . $this->getDepartmentById($post["department"])->description."</strong> "; }
            if(isset($post["position"]) && $post["position"]){ $additionalFilters["pos.id"] = $post["position"]; $resultFilter .= "Position: <strong>" . $this->getPositionById($post["position"])->name."</strong> "; }
            if(isset($post["type"]) && $post["type"]){ $additionalFilters["licenses.type"] = $post["type"]; $resultFilter .="License type: <strong>" . $post["type"]. "</strong> "; }
            if(isset($post["licenses"]) && $post["licenses"]){ $additionalFilters["cert.license_type"] = $post["licenses"]; $resultFilter .="License: <strong>" . $post["licenses"]. "</strong> "; }
            if($resultFilter == "Filter applied: "){ $resultFilter = ""; }
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
                            $resultset["toastr_msg"] = "Generate license and certification report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}` {$resultFilter}, a total of <strong>{$numRows}</strong> record(s) found.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Generate license and certification report by date range from `{$_filteredStartDate}` to `{$_filteredEndDate}` {$resultFilter}, no filtered data found!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Generate license and certification report by date range {$resultFilter}, no data on date range filter option(s) found!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate license and certification report by date range {$resultFilter}, no filtered by date range option(s) found!";
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
                    $resultset["toastr_msg"] = "Generate all license and certification report {$resultFilter}, a total of <strong>{$numRows}</strong> record(s) found.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Generate all license and certification report, {$resultFilter}, no filtered data found!";
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Generate license and certification report {$resultFilter}, no filter by option data found!";
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
        $this->db->where('companies.is_archived', 0);
        $this->db->where('companies.exclude', 0);
        $this->db->group_by("companies.id");
        $this->db->order_by("`code`", "ASC");
        return $this->db->get("gcchris.tblcompanies companies")->result();
    }

    public function select2DepartmentData(){
        $this->db->select("departments.id, UPPER(IF(departments.`code` = departments.`description`, 
            departments.`description`, 
            CONCAT(departments.`code`,' | ', departments.`description`))) `text`, departments.*");
        $this->db->where('departments.is_archived', 0);
        $this->db->order_by("`code`", "ASC");
        return $this->db->get("gcchris.tbldepartments departments")->result();
    }

    function select2PayoutScheduleData(){
        $this->db->select("id, name as text, occurrence");
        $this->db->order_by("id", "ASC");
        $results = $this->db->get("payroll.payout_schedule")->result();
        return $results;
    }

    public function getReportsSelect2EmployeeData(){
        $get = $this->input->get();
        $this->db->select("id, CONCAT(UPPER(TRIM(firstname)), ' ',
        CASE WHEN UPPER(TRIM(middlename)) != 'N/A' AND UPPER(TRIM(middlename)) != 'NONE' AND TRIM(middlename) !='' AND middlename IS NOT NULL
            THEN CONCAT(SUBSTR(middlename, 1, 1), '.') ELSE '' END,' ', UPPER(TRIM(lastname)),
        CASE WHEN UPPER(TRIM(suffix)) != 'N/A' AND UPPER(TRIM(suffix !='NONE')) AND suffix !='' AND suffix IS NOT NULL
            THEN CONCAT(' ', UPPER(TRIM(suffix))) ELSE '' END) as text");
        $this->db->from("gccmaster.tblemployees");
        if(isset($get["company_id"]) && $get["company_id"]){
            $this->db->where("company_id", $get["company_id"]);
        }
        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
            $this->db->like("firstname", $get['q'], "both");
            $this->db->or_like("lastname", $get['q'], "both");
            $this->db->or_like("CONCAT(firstname, ' ', lastname)", $get['q'], "both");
            $this->db->group_end();
        }
        $this->db->limit(25);
        $this->db->order_by("lastname", "ASC");
        $query = $this->db->get();
        return array("results" => $query->result_array());
    }

    public function getSelect2EmployeeData(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get["company_id"]) && $get["company_id"]){
            $departmentId = (isset($get["department_id"]) && $get["department_id"])? $get["department_id"]: 0;
            $emp_status = (isset($get["employee_status"]) && $get["employee_status"]) ? strtolower($get["employee_status"]) : null;
            $this->db->select("a.id, CONCAT(UPPER(TRIM(a.firstname)), ' ', CASE WHEN UPPER(TRIM(a.middlename)) != 'N/A' AND UPPER(TRIM(a.middlename)) != 'NONE' AND TRIM(a.middlename) !='' AND a.middlename IS NOT NULL
                THEN CONCAT(UPPER(SUBSTR(a.middlename, 1, 1)), '.') ELSE '' END,' ', UPPER(TRIM(a.lastname)), CASE WHEN UPPER(TRIM(a.suffix)) != 'N/A' AND UPPER(TRIM(a.suffix !='NONE')) AND a.suffix !='' AND
                a.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(a.suffix))) ELSE '' END) as employee_name");
            $this->db->from("gccmaster.tblemployees a");
            $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id", "LEFT");

            if ($emp_status != 'all' && $emp_status) {
                $this->db->where("LOWER(a.employee_status) =", $emp_status);
            }
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

    protected function getTimesheetMaxDate($verified = 0){
        $this->db->select("MAX(date) as max_date");
        $this->db->from("gcctimeutility.timesheet");
        $this->db->where("has_shift", 1);
        if(intval($verified) == 1){  $this->db->where("verified", 1); }
        $this->db->limit(1);
        $qTempMax = $this->db->get();
       return $qTempMax->row()->max_date ? $qTempMax->row()->max_date: null;
    }

    protected function generateLateReport($post = array()){
        $resultset = array();
        $arrFilter = array();
        $filter="Filters applied: ";
        $logMessage = "";
        $hasDepartment = isset($post["department"]) && $post["department"];
        if(isset($post["company"]) && $post["company"]){
            $empIds = array();
            $this->db->select("emp.id, dept.code");
            $this->db->from($this->tblEmployees." as emp");
            $this->db->join($this->companyTable." as comp", "comp.id = emp.company_id");
            $this->db->join($this->departmentTable." as dept", "dept.id = emp.department_id", "LEFT");
            $this->db->where("emp.company_id", $post["company"]);
            if($hasDepartment){
                $this->db->where("emp.department_id", $post["department"]);
            }
            $this->db->order_by("emp.id", "ASC");
            $this->db->group_by("emp.id");
            $qData = $this->db->get();
            if($hasDepartment){
                $dRow = $qData->row();
                $filter .= "Department: <strong>{$dRow->code}</strong> ";
            }
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
                    $filter .= "Company: <strong>{$cRow->code}</strong> ";
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
                    $filter .= "Payroll Group: {$pgRow->description} ";
                }
            }

            if($filterBy == "date_range"){
                $filterDates = explode(" - ", $post[$filterBy]);
                if(is_array($filterDates) && count($filterDates) == 2){
                    $startDate = Date("Y-m-d", strtotime($filterDates[0]));
                    $endDate = Date("Y-m-d", strtotime($filterDates[1]));
                }
                $arrFilter["filter_by"] = "Date Range";
                // $filter .= "Date Range: {$post[$filterBy]} ";

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
                $filter .= "Date: {$fsDate} - {$feDate} ";
                $tempMaxDate = $this->getTimesheetMaxDate();
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
                COALESCE(SUM(IF(ts.date >= emp.date_start && ts.am_late > 0, 1, 0))) + COALESCE(SUM(IF(ts.date >= emp.date_start && ts.pm_late > 0, 1, 0))) as reports_total,
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

                if (isset($post['employee_status']) && !empty($post['employee_status']) && strtolower($post['employee_status']) != 'all') {
                    $this->db->where('LOWER(emp.employee_status)', strtolower($post['employee_status']));
                }

                $qAttendance = $this->db->get();
                $ctrCount = $qAttendance->num_rows();
                if($filter == "Filters applied: "){
                    $filter = "";
                }
                if($ctrCount > 0){
                    $maxDate = $qAttendance->row_array()["max_date"];
                    $resultset["data"] = $qAttendance->result_array();
                    $resultset["response"] = true;
                    $resultset["filters"] = $arrFilter;
                    $resultset["toastr_msg"] = "Last verified attendance date on `{$maxDate}`, A total of ({$ctrCount}) employee late attendance record/s found!";
                    $logMessage = "Last verified attendance date on `<strong>{$maxDate}</strong>` {$filter}, A total of (<strong>{$ctrCount}</strong>) employee late attendance record/s found!";
                    $logState="success";
                    $userType="user";
                }else{
                    $resultset["response"] = false;
                    $resultset["filters"] = $arrFilter;
                    $resultset["toastr_msg"] = $tempMaxDate ? "<strong>No data available for the selected date range. Verified data is only up to `<strong>{$tempMaxDate}</strong>`. {$filter}" : "No late attendance record/s found!</strong>";
                    $logMessage = $resultset["toastr_msg"];
                    $logState="success";
                    $userType="user";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Filter option/s with given parameters not found!";
                $logMessage = $resultset["toastr_msg"] + $filter;
                $logState="success";
                $userType="user";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Filter option/s with given parameters, No employee data found!";
            $logMessage = $resultset["toastr_msg"];
            $logState="success";
            $userType="user";
        }
        $this->core_layout->setEventLog($logMessage, "generate", $logState, "gcchris",$userType);
        return $resultset;
    }

    public function generateLateAbsenteeReport(){
        $post = $this->input->post();
        $reportType = $post["report_type"];
        unset($post["report_type"]);
        $arrResponse = array();

        if ($reportType == "late"){
            $arrResponse = $this->generateLateReport($post);
            $arrResponse["filters"]["report_type"] = "Attendance Late Report";
        }
        elseif ($reportType == "absentee"){
            $arrResponse = $this->generateAbsenteeReport($post);
            $arrResponse["filters"]["report_type"] = "Attendance Absentee Report";
        }

        return $arrResponse;
    }

    public function selectPayrollGroup(){
        $get = $this->input->get();
        $arrData = array();
        $resultset = array();
        $companyId = (isset($get["company_id"]) && $get["company_id"])? $get["company_id"]: 0;
        $employeeStatus = (isset($get["employee_status"]) && $get["employee_status"])? strtolower($get["employee_status"]): false;
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
                    if($employeeStatus && $employeeStatus != 'all'){
                        $this->db->where("LOWER(employee_status)", strtolower($employeeStatus));
                    }
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

    public function generateAbsenteeReport($post = array()){ 
        $this->load->model("gcctime/timesheet_model", "ts_model");
        $resultset = array();
        $arrFilter = array();
        $filter = "Filters applied: ";
        $logMessage = "";
        $hasDepartment = isset($post["department"]) && $post["department"];
        if(isset($post["company"]) && $post["company"]){
            $empIds = array();
            $this->db->select("emp.id,c.code");
            $this->db->from($this->tblEmployees." as emp");
            $this->db->join($this->companyTable." as comp", "comp.id = emp.company_id");
            $this->db->join($this->departmentTable.' as c', 'c.id = emp.department_id', 'LEFT');
            $this->db->where("emp.company_id", $post["company"]);
            if (isset($post['employee_status']) && !empty($post['employee_status']) && strtolower($post['employee_status']) != 'all') {
                $this->db->where('LOWER(emp.employee_status)', strtolower($post['employee_status']));
            }
            if($hasDepartment){
                $this->db->where("emp.department_id", $post["department"]);
            }
            $this->db->order_by("emp.id", "ASC");
            $this->db->group_by("emp.id");
            $qData = $this->db->get();
            if($hasDepartment){
                $dRow = $qData->row();
                $filter .= "Department: <strong>{$dRow->code}</strong> ";
            }
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
                    $filter .= "Company: <strong>{$cRow->code}</strong> ";
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
                    $filter .= "Payroll Group: {$pgRow->description} ";
                }
            }

            if($filterBy == "date_range"){
                $filterDates = explode(" - ", $post[$filterBy]);
                if(is_array($filterDates) && count($filterDates) == 2){
                    $startDate = Date("Y-m-d", strtotime($filterDates[0]));
                    $endDate = Date("Y-m-d", strtotime($filterDates[1]));
                }
                
                $arrFilter["filter_by"] = "Date Range";
                // $filter .= "Date Range: {$post[$filterBy]} ";
            }else{
                $tempDatex = $post["filter_year"]."-".$post["filter_month"]."-01";
                $timeStamp = strtotime($tempDatex);
                
                $startDate = Date("Y-m-d", strtotime("first day of this month", $timeStamp));
                $endDate = Date("Y-m-d", strtotime("last day of this month", $timeStamp));
                $arrFilter["filter_by"] = "Month";
            }

            if($startDate && $endDate && (is_array($employeeIds) && count($employeeIds) > 0)){
                $tempMaxDate = $this->getTimesheetMaxDate();
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
                                    $md5Datex = md5($dt);
                                    $updateEmployeeAbsences[$empId]["absentee_total"] = isset($updateEmployeeAbsences[$empId]["absentee_total"]) && $updateEmployeeAbsences[$empId]["absentee_total"] ? $updateEmployeeAbsences[$empId]["absentee_total"] : 0;
                                    $updateEmployeeAbsences[$empId]["absentee_total"] += $newEmployeeRecord[$empId][$md5Datex];
                                    
                                    if(is_array($employeeLogDates[$empId][$md5Datex]) && count($employeeLogDates[$empId][$md5Datex]) > 0){
                                        foreach ($employeeLogDates[$empId][$md5Datex] as $dtx) { $arrLogs[] = $dtx; }
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
                $filter .= "Date: {$fsDate} - {$feDate} ";

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
                )) as reports_total,
                CONCAT(
                    GROUP_CONCAT(DISTINCT
                        IF(ts.date >= emp.date_start && ISNULL(ts.shift_am_start) && ISNULL(ts.shift_am_end), '', IF(ISNULL(ts.am_in) && ISNULL(ts.am_out),
                            CONCAT(ts.date, ' ', ts.shift_am_start, '~', ts.date, ' ', ts.shift_am_end), ''))
                    ),
                    GROUP_CONCAT(DISTINCT
                        IF(ts.date >= emp.date_start && ISNULL(ts.shift_pm_start) && ISNULL(ts.shift_pm_end), '', IF(ISNULL(ts.pm_in) && ISNULL(ts.pm_out),
                            CONCAT(ts.date, ' ', ts.shift_pm_start, '~', ts.date, ' ', ts.shift_pm_end), ''))
                    )
                ) as attendance_logs,
                CONCAT(
                    GROUP_CONCAT(DISTINCT
                        IF(ts.date >= emp.date_start && ISNULL(ts.shift_am_start) && ISNULL(ts.shift_am_end), '', IF(ISNULL(ts.am_in) && ISNULL(ts.am_out),
                            ts.date, ''))
                    ),
                    GROUP_CONCAT(DISTINCT
                        IF(ts.date >= emp.date_start && ISNULL(ts.shift_pm_start) && ISNULL(ts.shift_pm_end), '', IF(ISNULL(ts.pm_in) && ISNULL(ts.pm_out),
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
                if($filter == "Filters applied: "){
                    $filter = "";
                }
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
                                $this->db->where("employee", $attx->emp_id);
                                $this->db->where("status", "Approved");
                                $this->db->group_start();
                                $this->db->where("DATE(date_from) >=", $dt);
                                $this->db->or_where("DATE(date_from) <=", $dt);
                                $this->db->where("DATE(date_to) >=", $dt);
                                $this->db->group_end();
                                $this->db->order_by("date_from", "ASC");
                                $approvedLoa = $this->db->get();

                                if($approvedLoa->num_rows() > 0){
                                    foreach ($approvedLoa->result() as $appLoa) {
                                        $isWholeDay = (intval($appLoa->type) === 3) ? true : false;
                                        $isHalfDay = (intval($appLoa->type) === 2) ? true : false;
                                        $loaType = intval($appLoa->type);
                                        $dateFrom = date("Y-m-d", strtotime($appLoa->date_from));
                                        $dateTo = $isWholeDay ? $dateFrom : date("Y-m-d", strtotime($appLoa->date_to));
                                        $cDate = date("Y-m-d", strtotime($dt));

                                        $meridian = date("A", strtotime($appLoa->date_from));

                                        if(strtotime($cDate) >= strtotime($dateFrom) && strtotime($cDate) <= strtotime($dateTo)){
                                            $loaReference[$appLoa->employee][$cDate]["reference"] = $appLoa->reference_no;
                                            $loaReference[$appLoa->employee][$cDate]["whole_day"] = $isWholeDay;
                                            $loaReference[$appLoa->employee][$cDate]["half_day"] = $isHalfDay;
                                            $loaReference[$appLoa->employee][$cDate]["loa_type"] = $loaType;
                                            $loaReference[$appLoa->employee][$cDate]["_meridian"] = $meridian;
                                        }
                                    }
                                }
                            }
                        }

                        $newLogs00 = explode(",", $attx->attendance_logs);
                        $newLogs00x = array_filter($newLogs00, function($log) use ($qDateStart, $maxDate) {
                            $log = explode("~", $log);
                            $nDatex = date("Y-m-d", strtotime($log[0]));
                            return strtotime($nDatex) >= strtotime($qDateStart) && strtotime($nDatex) <= strtotime($maxDate);
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
                        $attx->reports_total += $tempTotal;
                        $qData[] = $attx;
                    }

                    $resultset["data"] = $qData;
                    $resultset["loa_reference"] = $loaReference;
                    $resultset["response"] = true;
                    $resultset["filters"] = $arrFilter;
                    $resultset["toastr_msg"] = "Last verified attendance date on <strong>`{$maxDate}`</strong>, A total of (<strong>{$ctrCount}</strong>) employee absentee attendance record/s found!";
                    $logMessage = "Last verified attendance date on `<strong>{$maxDate}</strong>` {$filter}, A total of (<strong>{$ctrCount}</strong>) employee absentee attendance record/s found!";
                    $logState="success";
                    $userType="user";
                }else{
                    $resultset["response"] = false;
                    $resultset["filters"] = $arrFilter;
                    $resultset["toastr_msg"] = $tempMaxDate ? "<strong>No data available for the selected date range. Verified data is only up to `<strong>{$tempMaxDate}</strong>`  {$filter}." : "No absentee attendance record/s found!</strong>";
                    $logMessage = $resultset["toastr_msg"];
                    $logState="success";
                    $userType="user";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Filter option/s with given parameters not found!";
                $logMessage = $resultset["toastr_msg"];
                $logState="success";
                $userType="user";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Filter option/s with given parameters, No employee data found!";
            $logMessage = $resultset["toastr_msg"];
            $logState="success";
            $userType="user";
        }
        $this->core_layout->setEventLog($logMessage, "generate", $logState, "gcchris",$userType);
        return $resultset;
    }

    public function getDropdownSelectData() {
        $resultset = array();
        $this->db->select("id, description as text");
        $this->db->where("is_archived", 0);
        $this->db->where("exclude", 0);
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
        $this->db->select('id, code text, description, company_address');
        $this->db->where('is_archived', 0);
        $this->db->where('exclude', 0);
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

                if(isset($department) && $department){
                    $this->db->where('a.department_id', $department);
                }
            }

            $this->db->where('a.is_archived', 0);
            $this->db->where('b.is_archived', 0);
            $this->db->where('b.exclude', 0);
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

                if(isset($department) && $department){
                    $this->db->where('a.department_id', $department);
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
            $_department = isset($department) && $department ? $department : null;

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
        $filterBy = '';
        if (!empty($post['company'])) {
            $filterBy = 'Company: <strong>'. $this->getCompanyById($post['company'])->description."</strong> ";
        }
        if (!empty($post['department'])) {
            $filterBy .= 'Department: <strong>'.$this->getDepartmentById($post['department'])->description."</strong> ";
        }
        $this->core_layout->setEventLog("Generated Attrition Report ".$filterBy."Year: <strong>".$post['filter_year']."</strong> ", "generate", "success", "gcchris", "user");
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
        $this->db->where('b.is_archived', 0);
        $this->db->where('b.exclude', 0);
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
        $this->db->where('b.is_archived', 0);
        $this->db->where('b.exclude', 0);

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
        $filter = "Filters: ";
        $year = isset($post['filter_year']) && $post['filter_year'] ? $post['filter_year'] : '2024';
        $company = isset($post['company']) && $post['company'] ? $this->getGeneratedCompany($post['company']) : null;
        
        $filter .= "Year: <strong>" . $year . "</strong>, ";
        if($company){
            $filter .= "Company: <strong>" . $company . "</strong>, ";
        }
        if ($filter == "Filters: ") {
            $filter = "";
        }
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
        $this->core_layout->setEventLog("Generated Attrition Report Chart $filter", "generate", "success", "gcchris", "user");
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
        $filters="";
        $companyIds = $post["company"];
        if($companyId && $dataOnly ){ $post["company"] = $companyId; }
        if ($companyIds > 0) {
            $filters .= "Company: <strong>" . $this->getCompanyById($companyIds)->description . "</strong> ";
        }
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
            $count = count($resultset["rows"]);
            $resultset["id"] = $post["company"];
        }else{
            $resultset["response"] = false;
        }
        $this->core_layout->setEventLog("Generated list of employees without stations. $filters With result: {$count}", "generate", 'success', "gcchris");
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
        $type="";
        $logState="";
        $employees = "";
        if(isset($post["station"]) && $post["station"]){
            $station = $this->getStationById($post["station"])->site_name;
            if(isset($post["employee_id"]) && is_array($post["employee_id"]) && count($post["employee_id"]) > 0){
                foreach ($post["employee_id"] as $empId) {
                    $employees .= $this->getEmployeeName($empId).", ";
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
            $logState = "success";
            $type = "user";
        }else{
            $resultset["response"] = false;
            $logState = "error";
            $type = "system";
        }
        $this->core_layout->setEventLog("Set station for employee(s): <strong>$employees</strong>"."Station: ".$station, "generate", $logState, "gcchris",$type);
        return $resultset;
    }

    public function exportLog(){
        $post = $this->input->post();
        $action = ['print' => 'Printed', 'excel' => 'Made Excel file'][$post['exportType']] ?? 'Exported';
        $filters = implode(', ', array_map(function($k, $v) { return "$k: $v"; }, array_keys($post['filter']), $post['filter']));
        $this->core_layout->setEventLog("$action {$post['type']} with filters: $filters", "generate", "success", "gcchris", "user");
        return $post;
    }

    private function getPositionById($id){
        $this->db->select("name");
        $this->db->from($this->positionTable);
        $this->db->where('id', $id);
        $query = $this->db->get(); 
        $result = $query->row();
        $this->db->reset_query();
        return $result;
    }

    private function getDepartmentById($id){
        $this->db->select("description");
        $this->db->from($this->departmentTable);
        $this->db->where('id', $id);
        $query = $this->db->get(); 
        $result = $query->row();
        $this->db->reset_query();
        return $result;
    }
    
    private function getCompanyById($id){
        $this->db->select("description");
        $this->db->from($this->companyTable);
        $this->db->where('id', $id);
        $query = $this->db->get(); 
        $result = $query->row();
        $this->db->reset_query();
        return $result;
    }

    private function getStationById($id){
        $this->db->select("site_name");
        $this->db->from($this->tblAppLocationSites);
        $this->db->where('id', $id);
        $query = $this->db->get(); 
        $result = $query->row();
        $this->db->reset_query();
        return $result;
    }


    public function logExport(){
        $post = $this->input->post();
        $filters = "Filters applied: ";
        $company_id = isset($post['filters']['company']) ? intval($post['filters']['company']) : 0;
        $department_id = isset($post['filters']['department']) ? intval($post['filters']['department']) : 0;
        $position_id = isset($post['filters']['position']) ? intval($post['filters']['position']) : 0;
        $station_id = isset($post['filters']['station']) ? intval($post['filters']['station']) : 0;
        $training = isset($post['filters']['training']) ? $post['filters']['training'] : '';
        $title = isset($post['filters']['title']) ? $post['filters']['title'] : '';
        $ageRange = isset($post['filters']['age_range']) ? $post['filters']['age_range'] : '18-25';

        if(!empty($title)){
            $filters .= "LIcense and certificate title: <strong>" . $title . "</strong> ";
        }
        if(!empty($ageRange)){
            $filters .= "Age range: <strong>" . $ageRange . "</strong> ";
        }
        if(!empty($training)){
            $filters .= "Training title: <strong>" . $training . "</strong> ";
        }
        if ($company_id > 0) {
            $filters .= "Company: <strong>" . $this->getCompanyById($company_id)->description . "</strong> ";
        }
        if ($department_id > 0) {
            $filters .= "Department: <strong>" . $this->getDepartmentById($department_id)->description . "</strong> ";
        }
        if ($position_id > 0) {
            $filters .= "Position: <strong>" . $this->getPositionById($position_id)->name . "</strong> ";
        }
        if ($station_id > 0) {
            $filters .= "Station: <strong>" . $this->getStationById($station_id)->site_name . "</strong> ";
        }
        $filter_by = isset($post['filters']['filter_by']) ? $post['filters']['filter_by'] : '';
        $filter_type = isset($post['filters']['filter_type']) ? $post['filters']['filter_type'] : '';
        if ($filters == "Filters applied: ") {
            $filters .= "<strong>NONE. </strong>";
        }
        if ($filter_type == "emp.date_start" || $filter_type == "Hired") {
            $filters .= "Hired Employees, ";
        } elseif ($filter_type) {
            $filters .= "Separated Employees, ";
        }
        if ($filter_by == "date_range") {
            $filters  .= "Between <strong>{$post['filters']['date_range']} </strong> ";
        }
        if ($filter_by == "month") {
            $month_name = date("F", mktime(0, 0, 0, $post['filters']['filter_month'], 1));
            $filters .= "For the month of <strong>{$month_name}</strong> Year: <strong>{$post['filters']['filter_year']}</strong>";
        }
        $this->core_layout->setEventLog("Exported using {$post['name']}. {$post['type']} {$filters} results found: <strong>{$post['count']}</strong>", "export", 'success', "gcchris");
        return true;
    }

    private function getEmployeeName($id) {
        $this->db->select("id, firstname, middlename, lastname, suffix");
        $this->db->from("gccmaster.tblemployees");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $result = $query->row_array();

        $formattedName = strtoupper($result['firstname']) . ' ';
        if (!empty($result['middlename'])) {
            $initial = strtoupper(substr($result['middlename'], 0, 1)); // Get the first letter
            $formattedName .= $initial . '. '; // Append the initial
        }
        $formattedName .= strtoupper($result['lastname']);
        if (!empty($result['suffix'])) {
            $formattedName .= ', ' . strtoupper($result['suffix']);
        }
        return $formattedName;
    }

    public function setlastEmployeeStation($id = null){
        $arrData = array();

        $this->db->select('a.id as employee_id, a.biometricno, b.station_id, b.station_description');
        $this->db->join($this->tblDefaultLocation.' as b', 'b.employee_id = a.id', 'LEFT');
        $this->db->from($this->tblEmployees.' as a');
        
        if ($id) {
            $this->db->where('a.id', $id);
        } else {
            $this->db->where('a.biometricno !=', 1); //excluded sir CMD
            $this->db->where('a.biometricno != " "', null, true);
        }
        $this->db->where('b.station_id', null);

        $query = $this->db->get();

        $this->db->reset_query();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                
                $this->db->select('a.id as personnel_id, a.biometricno, b.site_location_id, b.location_name');
                $this->db->join($this->tblPersonnelLocation.' as b', 'b.personnel_id = a.id', 'INNER');
                $this->db->from($this->tblPersonnel.' as a');
                $this->db->where('a.biometricno', $row->biometricno);
                $this->db->order_by('b.id', 'DESC');
                $this->db->limit(1);
                $q = $this->db->get();

                if ($q->num_rows() > 0) {
                    $personnel = $q->row();

                    $data = array(
                        'employee_id' => $row->employee_id,
                        'station_id' => $personnel->site_location_id,
                        'station_description' => $personnel->location_name,
                        'created_at' => date('Y-m-d H:i:s')
                    );

                    $insert = $this->db->insert($this->tblDefaultLocation, $data);
                    
                    if ($insert) {
                        array_push($arrData, $data);
                    }
                }
            }
        }

        return $arrData;
    }

    public function getAgeReport(){
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
        $company = (isset($post["company"]) && $post["company"]) ? $post["company"] : false;
        $department = (isset($post["department"]) && $post["department"]) ? $post["department"] : false;
        $station = (isset($post["station"]) && $post["station"]) ? $post["station"] : false;
        $ageRange = (isset($post["ageRange"]) && $post["ageRange"]) ? $post["ageRange"] : false;
        $rowData = $this->getAgeReportData($search, $limit, $offset, $sortBy, $sortOrder,$ageRange, $company, $department, $station);
        $total = $this->getAgeReportDataCount($search,$ageRange, $company, $department, $station);
        $resultset["recordsTotal"] = $total;
        $resultset["recordsFiltered"] =  $total;
        $resultset["data"] = isset($rowData) && $rowData ? $rowData: array();
        return $resultset;
    }

    private function getAgeReportData($search, $limit, $offset, $sortBy, $sortOrder, $ageRange, $company, $department, $station){
        $filterFields = array("emp.firstname","emp.lastname");
        $this->db->select('
            emp.id as empid, emp.firstname, emp.lastname, emp.middlename, emp.suffix, emp.date_start as hired_date, dsl.station_id as station_id, 
            emp.bday as birthday, 
            dept.description as department,comp.description as company, sss_no, tin_no, pagibig_no, phealth_no, 
            pos.name as position, dsl.station_description as station
            ');
        $this->db->from($this->tblEmployees.' as emp');
        $this->db->join($this->departmentTable." as dept", "emp.department_id =dept.id", "LEFT");
        $this->db->join($this->companyTable." as comp", "emp.company_id = comp.id", "LEFT");
        $this->db->join($this->positionTable." as pos", "emp.position = pos.id", "LEFT");
        $this->db->join($this->defaultStationTable." as dsl", "emp.id = dsl.employee_id", "LEFT");
        $this->db->where('emp.employee_status', 'Active');
        $this->db->where('comp.is_archived', 0);
        $this->db->where('comp.exclude', 0);
        if($company){
            $this->db->where('emp.company_id', $company);
        }
        if($department){
            $this->db->where('emp.department_id', $department);
        }
        if($station){
            if($station == 'not_assigned'){
                $this->db->where('dsl.station_description', null);
            }else{
                $this->db->where('dsl.station_id', $station);
            }
        }
        if ($ageRange) {
                $rangeParts = explode('-', $ageRange);
                $minAge = (int)$rangeParts[0];
                $maxAge = (int)$rangeParts[1];
                $this->db->where('emp.bday >', date('Y-m-d', strtotime('-' . ($maxAge + 1) . ' years +1 day')));
                $this->db->where('emp.bday <=', date('Y-m-d', strtotime('-' . $minAge . ' years')));
        }
        $this->db->group_by('emp.id');
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
        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();
        return $query->result_array();
    }

    private function getAgeReportDataCount($search,$ageRange, $company, $department, $station){
        $filterFields = array("emp.firstname","emp.lastname");
        $this->db->from($this->tblEmployees.' as emp');
        $this->db->join($this->departmentTable." as dept", "emp.department_id =dept.id", "LEFT");
        $this->db->join($this->companyTable." as comp", "emp.company_id = comp.id", "LEFT");
        $this->db->join($this->positionTable." as pos", "emp.position = pos.id", "LEFT");
        $this->db->join($this->defaultStationTable." as dsl", "emp.id = dsl.employee_id", "LEFT");
        $this->db->where('emp.employee_status', 'Active');
        $this->db->where('comp.is_archived', 0);
        $this->db->where('comp.exclude', 0);

        if($company){
            $this->db->where('emp.company_id', $company);
        }
        if($department){
            $this->db->where('emp.department_id', $department);
        }
        if($station){
            if($station == 'not_assigned'){
                $this->db->where('dsl.station_description', null);
            }else{
                $this->db->where('dsl.station_id', $station);
            }
        }
        if ($ageRange) {
            if ($ageRange == 'above') {
                $this->db->where('emp.bday <=', date('Y-m-d', strtotime('-65 years')));
            } else {
                $rangeParts = explode('-', $ageRange);
                $minAge = (int)$rangeParts[0];
                $maxAge = (int)$rangeParts[1];
                $this->db->where('emp.bday >', date('Y-m-d', strtotime('-' . ($maxAge + 1) . ' years +1 day')));
                $this->db->where('emp.bday <=', date('Y-m-d', strtotime('-' . $minAge . ' years')));
            }
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

    public function getSelect2Positions(){
        $this->db->select('id, name as text');
        $this->db->where('is_archived', 0);
        return $this->db->get($this->positionTable)->result();
    }

    public function getSelect2Employee(){
        $result = array();

        $get = $this->input->get();
        $search = isset($get['search']['term']) ? $get['search']['term'] : null;

        $this->db->select('id, CONCAT(firstname, " ", lastname) as text');
        $this->db->where('is_archived', 0);

        if (isset($get['employee_status']) && $get['employee_status']) {
            $this->db->where('employee_status', $get['employee_status']);
        } else {
            $this->db->where('employee_status', 'Active');
        }

        if (isset($get['company_id']) && $get['company_id']) {
            $this->db->where('company_id', $get['company_id']);
        }

        if (isset($get['department_id']) && $get['department_id']) {
            $this->db->where('department_id', $get['department_id']);
        }

        if (isset($get['position_id']) && $get['position_id']) {
            $this->db->where('position', $get['position_id']);
        }

        if (isset($search) && $search) {
            $this->db->group_start();
                $this->db->like('CONCAT(firstname, " ", lastname)', $search, 'both');
                $this->db->or_like('firstname', $search, 'both');
                $this->db->or_like('lastname', $search, 'both');
            $this->db->group_end();
        }

        $query = $this->db->get($this->tblEmployees);

        if ($query->num_rows() > 0) {
            $result = $query->result();
        }

        return array('results' => $result);
    }

    public function getEmployeeSalaryHistory(){
        $post = $this->input->post()['filter'];
        $resultset = array();
        $arrData = array();

        if (isset($post) && $post) {

            $date_to = isset($post['date_to']) ? $post['date_to'] : date('Y');

            $sql = 'a.id, a.sal_date, a.sal_rate, a.sal_remarks, a.add_date, 
                UPPER(CONCAT(TRIM(b.firstname), " ", TRIM(b.lastname))) as name, b.biometricno, date_format(a.sal_date, "%Y") as year';

            $this->db->select($sql);
            $this->db->join($this->tblEmployees.' as b', 'b.id = a.emp_id', 'LEFT');
            $this->db->from($this->tblSalaryHistory.' as a');
    
            if (isset($post['employee_status']) && $post['employee_status']) {
                $this->db->where('b.employee_status', $post['employee_status']);
            }
    
            if (isset($post['company']) && $post['company']) {
                $this->db->where('b.company_id', $post['company']);
            }
    
            if (isset($post['department']) && $post['department']) {
                $this->db->where('b.department_id', $post['department']);
            }
    
            if (isset($post['position']) && $post['position']) {
                $this->db->where('b.position', $post['position']);
            }

            if (isset($post['employee']) && $post['employee']) {
                $this->db->where_in('b.id', $post['employee']);
            }

            $this->db->group_start();
                $this->db->where('YEAR(a.sal_date) >=', $post['date_from']);
                $this->db->where('YEAR(a.sal_date) <=', $date_to);
            $this->db->group_end();

            $this->db->where('a.is_archived', 0);

            $this->db->order_by('a.sal_date', 'DESC');
            $this->db->order_by('a.id', 'DESC');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $groupedByBiometric = [];
                $array = $query->result_array();
                $_arrData = $this->getGroupedData($array);

                $_test = array();
                foreach($_arrData as $key => $value){
                    foreach($value as $k => $v){
                        $_test[] = $v;
                    }
                }

                $arrData = $_test;
            }
        }

        $__arrData = array_values($arrData); //reverting the index to number
        
        $resultset['generated_years'] = $this->generatedYears($post['date_from'], $date_to);
        $resultset['company'] = isset($post['company']) ? $this->getCompanyCodeById($post['company']) : null;
        $resultset['department'] = isset($post['department']) ? $this->getDepartmentCodeById($post['department']) : null;
        $resultset['position'] = isset($post['position']) ? $this->getPositionNameById($post['position']) : null;
        $resultset['data'] = $__arrData;

        $message = "Employee Salary History has been generated with filters";
        
        if (isset($post['company']) && $post['company']) {
            $message .= ' - Company: '.$resultset['company'];
        }
        
        if (isset($post['department']) && $post['department']) {
            $message .= ' - Department: '.$resultset['department'];
        }

        if (isset($post['position']) && $post['position']) {
            $message .= ' - Position: '.$resultset['position'];
        }

        if (isset($post['employee']) && $post['employee']) {
            if (is_array($post['employee'])) {
                $message .= ' - Employees ID: '.implode(',', $post['employee']);
            } else {
                $message .= ' - Employee ID: '.$post['employee'];
            }
        }

        $message .= ' and date range from '.$post['date_from'].' to '.$date_to;

        $this->core_layout->setEventLog($message,"Salary History", "search", "gcchris", "user");
        return $resultset;
    }

    function generatedYears($from, $to){
        $years = array();

        for($nYear = $to; $nYear >= $from; $nYear--){
            array_push($years, (int)$nYear); //changed first value from string to a number
        }

        return $years;
    }

    function getCompanyCodeById($id){
        $this->db->select('code');
        $this->db->where('id', $id);
        $query = $this->db->get($this->companyTable);
        if ($query->num_rows() > 0) {
            return $query->row()->code;
        }
        return false;
    }

    function getDepartmentCodeById($id){
        $this->db->select('code');
        $this->db->where('id', $id);
        $query = $this->db->get($this->departmentTable);
        if ($query->num_rows() > 0) {
            return $query->row()->code;
        }
        return false;
    }

    function getPositionNameById($id){
        $this->db->select('name');
        $this->db->where('id', $id);
        $query = $this->db->get($this->positionTable);
        if ($query->num_rows() > 0) {
            return $query->row()->name;
        }
        return false;
    }

    function getGroupedData($array){
        $groupedByBiometric = [];

        // Step 1: Group by biometricno
        foreach ($array as $entry) {
            $biometricno = $entry['biometricno'];
            $groupedByBiometric[$biometricno][] = $entry;
        }

        $finalResult = [];

        // Step 2: Process each biometric group to ensure unique years
        foreach ($groupedByBiometric as $biometricno => $entries) {
            $currentGroup = [];
            $processedYears = [];
            $biometricResult = [];

            foreach ($entries as $entry) {
                $year = date('Y', strtotime($entry['sal_date'])); // Extract year

                // If the year already exists in the current group, start a new one
                if (in_array($year, $processedYears)) {
                    // Add name and biometricno to the current group
                    $biometricResult[] = [
                        'name' => $entry['name'],
                        'biometricno' => $biometricno,
                        'id' => $entry['id'],
                        'data' => $currentGroup
                    ];
                    $currentGroup = [];
                    $processedYears = [];
                }

                // Add entry to the current group
                $currentGroup[$year] = $entry;
                $processedYears[] = $year;
            }

            // Add the last group
            if (!empty($currentGroup)) {
                $biometricResult[] = [
                    'name' => $entries[0]['name'], // Keep name consistent
                    'biometricno' => $biometricno,
                    'id' => $entries[0]['id'],
                    'data' => $currentGroup
                ];
            }

            // Add this biometric group to the final result
            $finalResult[] = $biometricResult;
        }

        return $finalResult;
    }

    public function selectPsPayrollGroup(){
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

    public function getPayrollGroupMultiple(){
        $post = $this->input->post();
        $resultset = array();
        $employees = array();
        if(isset($post["group_id"]) && $post["group_id"]){
            $ids = $post["group_id"];
            $tempIdx = array();
            $this->db->select("employee_id");
            $this->db->from("payroll.payroll_group");
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
                $this->db->from("gccmaster.tblemployees");
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

    public function selectEmployee(){
        $get = $this->input->get();
        $resultarray = array();
        $companyIds = (isset($get["company_ids"]) && $get["company_ids"])? $get["company_ids"]: array();
        $this->db->select("a.id, trim(a.firstname) as firstname, a.lastname, a.middlename, a.suffix");
        $this->db->from("gccmaster.tblemployees a");
        $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id", "LEFT");
        $this->db->where("a.employee_status", "Active"); 
        if(is_array($companyIds) && count($companyIds) > 0){ $this->db->where_in("b.id", $companyIds); }
        if(isset($get["company_ids"]) && !is_array($get["company_ids"]) && $get["company_ids"]){
            $this->db->where("b.id", $get["company_ids"]);
        }
        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
            $this->db->like("a.firstname", $get['q'], "both");
            $this->db->or_like("a.lastname", $get['q'], "both");
            $this->db->group_end();
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

    protected function getPayrollGroupEmployeeIds($id = null){
        $arrIds = array();
        if($id){
            $this->db->select("employee_id");
            $this->db->from("payroll.payroll_group");
            $this->db->where_in("id", $id);
            $this->db->where("status", 1);
            $this->db->where("is_archived", 0);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $arrIds = @unserialize($q->row()->employee_id);
            }
        }
        return $arrIds;
    }

    public function noEarnerReportFilteredData(){
        $resultset = array();
        $post = $this->input->post();
        $employeeIds = isset($post["employees"]) && $post["employees"] ? $post["employees"]: array();
        $start = date('Y-m-d', strtotime(date('Y-m-d')));
        $end = date('Y-m-d', strtotime(date('Y-m-d')));
        $payDate = date('Y-m-d', strtotime($post["pay_date"]));

        if(isset($post["date_range"]) && $post["date_range"]){
            $date_range = explode("-", $post["date_range"]);
            $start = date('Y-m-d', strtotime(trim($date_range[0])));
            $end = date('Y-m-d', strtotime(trim($date_range[1])));
        }

        if(isset($post["payroll_group"]) && !empty($post["payroll_group"]) && count($post["payroll_group"]) > 0){
            $arrIds = $this->getPayrollGroupEmployeeIds($post["payroll_group"]);
            if(is_array($arrIds) && count($arrIds) > 0){ $employeeIds = array_merge($employeeIds, $arrIds); }
        }

        $this->db->select("ps.*, emp.lastname, emp.firstname, emp.middlename, emp.suffix, emp.idno, IF(position.name IS NULL, emp.position, position.name) as position,
            GROUP_CONCAT(DISTINCT(CONCAT(custom_adjustments.particulars,'||',custom_adjustments.amount, '||', custom_adjustments.cadj_type))) custom_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(created_adjustments.particulars,'||',created_adjustments.amount, '||', created_adjustments.adj_type, '||', created_adjustments.status))) created_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(ps_loan.code,'||',psl_payment.amount_due, '||', ps_loan.loan_class))) sss_hdmf_loan_deduction, 
            IFNULL(ps_allowance.rate, 0) as allowance_rate, UPPER(TRIM(CONCAT(emp.firstname, ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END,' ', emp.lastname,
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END))) as employee_name");
        $this->db->from("payroll.payroll_sheet as ps");
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
        $this->db->where("ps.date_start", $start);
        $this->db->where("ps.date_end", $end);
        $this->db->where("ps.pay_date", $payDate);
        $this->db->where("ps.company_id", $post["company"]);
        $this->db->where("ps.payroll_sched", $post["payout_schedule"]);
        $this->db->where("ps.payroll_seq", $post["sequence"]);
        $this->db->where("ps.posted", 1);
        $this->db->where("ps.net_pay", 0);
        $this->db->where("ps.is_bonus", 0);
        if(is_array($employeeIds) && count($employeeIds) > 0){ $this->db->where_in("ps.emp_id", $employeeIds); }
        $this->db->order_by("emp.lastname, emp.firstname");
        $this->db->group_by("ps.id");
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $resultset["response"] = true;
            $resultset["data"] = $q->result();
        }else{
            $resultset["response"] = false;
            $resultset["data"] = array();
        }
        return $resultset;
    }

    public function getPayrollSheetFirstEntryDate(){
        $this->db->select("pay_date");
        $this->db->from("payroll.payroll_sheet");
        $this->db->where("posted", 1);
        $this->db->order_by("pay_date", "ASC");
        $this->db->limit(1);
        return $this->db->get()->row()->pay_date;
    }

    public function getSssPremiumContributionYears(){
        $this->db->select("year as id, year as text");
        $this->db->from($this->sbrPaymentTable);
        $this->db->group_by("year");
        $this->db->order_by("year", "DESC");
        return $this->db->get()->result();
    }

    public function sssPremiumContributionReportData(){
        $post = $this->input->post();
        $resultset = array();
        if(isset($post["employee"]) && $post["employee"]){
            $tempFilter = array();
            if(isset($post["filter_by"], $post['filter_month'], $post['filter_year']) && ($post["filter_by"] == "month" && $post['filter_month'] && $post['filter_year'])){
                $monthName = date("F", mktime(0, 0, 0, $post['filter_month'], 1));
                $tempFilter["month_name"] = strtolower(trim($monthName));
                $tempFilter["year"] = $post['filter_year'];
            }elseif(isset($post["filter_by"], $post['filter_year']) && ($post["filter_by"] == "year" && $post['filter_year'])){
                $tempFilter["year"] = $post['filter_year'];
            }

            $this->db->select("sc.id, sp.sbr_no, sp.year, sp.month_name, sp.payment_date, sc.sss_total");
            $this->db->from($this->sbrContributionTable." sc");
            $this->db->join($this->sbrPaymentTable." sp", "sp.id = sc.sbr_id", "INNER");
            $this->db->where("sc.employee_id", $post["employee"]);
            if(isset($post["company"]) && $post["company"]){ $this->db->where("sp.company_id", $post["company"]); }
            if(is_array($tempFilter) && !empty($tempFilter)){
                $this->db->group_start();
                foreach ($tempFilter as $key => $value) { $this->db->where($key, $value); }
                $this->db->group_end();
            }
            $this->db->order_by("sp.year", "ASC");
            $this->db->order_by("FIELD(sp.month_name, 'January','February','March','April','May','June','July','August','September','October','November','December')", null, false);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $tempFilter["employee"] = $this->getEmployeeSssPremiumFilter($post["employee"]);
                $tempFilter["company"] = $this->getCompanySssPremiumFilter($post["company"]);
                $resultset["filter"] = $tempFilter;
                $resultset["response"] = true;
                $resultset["data"] = $q->result();
                $resultset["count"] = $q->num_rows();
                $resultset["toastr_msg"] = "A total of <strong>" . $resultset["count"] . "</strong> record(s) found.";
            }else{
                $resultset["response"] = false;
                $resultset["data"] = array();
                $resultset["count"] = 0;
                $resultset["toastr_msg"] = "No record(s) found.";
            }
        }else{
            $resultset["response"] = false;
            $resultset["data"] = array();
            $resultset["count"] = 0;
            $resultset["toastr_msg"] = "No record(s) found.";
        }
        return $resultset;
    }

    protected function getCompanySssPremiumFilter($id=null){
        if($id){
            $this->db->select("UPPER(description) as company_name, UPPER(company_address) as company_address");
            $qCompanyData = $this->db->get_where($this->companyTable, array("id" => $id));
            return $qCompanyData->row();
        }else{
            return false;
        }
    }
    protected function getEmployeeSssPremiumFilter($id=null){
        if($id){
            $this->db->select("sss_no,
            CONCAT(UPPER(TRIM(firstname)), ' ',
            CASE WHEN UPPER(TRIM(middlename)) != 'N/A' AND UPPER(TRIM(middlename)) != 'NONE' AND TRIM(middlename) !='' AND middlename IS NOT NULL
            THEN CONCAT(UPPER(SUBSTR(middlename, 1, 1)), '.') ELSE '' END,' ', UPPER(TRIM(lastname)),
            CASE WHEN UPPER(TRIM(suffix)) != 'N/A' AND UPPER(TRIM(suffix !='NONE')) AND suffix !='' AND suffix IS NOT NULL 
            THEN CONCAT(' ', UPPER(TRIM(suffix))) ELSE '' END) as employee_name");
            $qEmployeeData = $this->db->get_where($this->tblEmployees, array("id" => $id));
            return $qEmployeeData->row();
        }else{
            return false;
        }
    }
}
