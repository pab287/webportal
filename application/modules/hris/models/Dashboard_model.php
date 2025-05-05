<?php defined('BASEPATH') OR exit('No direct script access allowed');


    class Dashboard_model extends CI_Model {
        protected $loggedUser;
        protected $loggedUserName;
        protected $tblEmployees = "gccmaster.tblemployees";
        protected $tblCompanies = "gcchris.tblcompanies";
        protected $tblPosition = "gcchris.tblposition";
        protected $tblLoa = "gcceforms.loa";
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

        function __construct() {
            parent::__construct();
            $this->load->model('ams/Utilities_model', 'utilities');
            $this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->loggedinData["username"];

            // commented out for sql mode is edited in variable
	/*** sql_mode ONLY_FULL_GROUP_BY ***/
		// $this->db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
	/*** sql_mode ONLY_FULL_GROUP_BY ***/
        /*** sql_mode NO_ZERO_DATE ***/
		// $this->db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'NO_ZERO_DATE',''));");
        /*** sql_mode NO_ZERO_DATE ***/
            // commented out for sql mode is edited in variable
        }

        public function getGenderDemographics() {
            $this->db->select('UCASE(emp.gender) gender, emp.gender `key`, COUNT(*) cnt');
            $this->db->where("employee_status", 'Active');
            $this->db->group_by('emp.gender');
            $query = $this->db->get($this->tblEmployees . " emp");

            $total = array_reduce($query->result(), function ($carry, $item) {
                return $carry + $item->cnt;
            });

            return array("data" => $query->result(), "total" => $total);
        }

        public function getEachEmployeeStatusDemographics($company_id = 0) {
            // var_dump($company_id);
            $this->db->select("UCASE(IF(emp.employee_status='Black Listed', 'Blacklisted', emp.employee_status)) , UCASE(IF(emp.employee_status='End of Contract', 'Contract End', emp.employee_status))
                               employee_status, emp.employee_status `key`, COUNT(*) cnt");
            $this->db->where("emp.employee_status IS NOT NULL", NULL, FALSE);
            // $this->db->where("emp.work_status IN ('Regular', 'Probationary', 'Service contract', 'No contract')", NULL, FALSE);
            if (isset($company_id) && $company_id != 0) {
                $this->db->join($this->tblCompanies . ' company', 'emp.company_id = company.id', 'LEFT');
                $this->db->where('company.id', $company_id);
            }
            $this->db->group_by('emp.employee_status');
            $this->db->order_by('key', 'DESC');
            $query = $this->db->get($this->tblEmployees . " emp");

            $total = array_reduce($query->result(), function ($carry, $item) {
                return $carry + $item->cnt;
            });

            return array("data" => $query->result(), "total" => number_format($total, 0, '.', ','));
        }

        public function getActiveEmployeesOnEachCompany($sort="DESC") {
            $select = "UCASE(IF(company.id IS NULL, `emp`.`company_id`, company.code)) company, 
                       COUNT(*) cnt,
                       IF(company.id IS NULL, `emp`.`company_id`, company.code) key_str,
                       (SELECT COUNT(*) FROM gccmaster.tblemployees emp
                           LEFT JOIN `gcchris`.`tblcompanies` `company` ON `emp`.`company_id` = `company`.`id` 
                           WHERE 
                           IF(company.id IS NULL, emp.company_id, company.code) IS NOT NULL AND 
                           emp.work_status IN ('Regular', 'Probationary', 'Service contract', 'No contract') AND 
                           (`emp`.`employee_status` = 'Active' AND `emp`.`employee_status` IS NOT NULL)) total,
                       CAST((COUNT(*) / (SELECT COUNT(*) FROM gccmaster.tblemployees emp
                           LEFT JOIN `gcchris`.`tblcompanies` `company` ON `emp`.`company_id` = `company`.`id` 
                           WHERE 
                           IF(company.id IS NULL, emp.company_id, company.code) IS NOT NULL AND 
                           emp.work_status IN ('Regular', 'Probationary', 'Service contract', 'No contract') AND 
                           (`emp`.`employee_status` = 'Active' AND `emp`.`employee_status` IS NOT NULL))) * 100 AS DECIMAL(10,1)) percentage";
            $this->db->select($select, false);
            $this->db->where("IF(company.id IS NULL, emp.company_id, company.code) IS NOT NULL", null, false);
            $this->db->group_start();
            $this->db->where("emp.employee_status='Active' AND emp.employee_status IS NOT NULL", null, false);
            $this->db->group_end();

            // $this->db->where("emp.work_status IN ('Regular', 'Probationary', 'Service contract', 'No contract')", NULL, FALSE);
            // $this->db->where("emp.employee_status", 'Active');
            $this->db->group_by('IF(company.id IS NULL, emp.company_id, company.code)');
            $this->db->join($this->tblCompanies . " company", 'emp.company_id = company.id', 'LEFT');

            /*** Supper Notty Was Here ***/
            // $this->db->order_by('COUNT(*) '.$sort);
            $this->db->order_by('company.code '.$sort);
            /*** Supper Notty Was Here ***/

            $query = $this->db->get($this->tblEmployees . " emp");
            $companies = $query->result_array();
            $sql = $this->db->last_query();

            // CODE GROUPING COMPANIES WITH LESS THAN 1% POPULATION INTO OTHERS
            /*$_companies_above_one_percent = array_filter($companies, function ($company) {
                return (double)$company["percentage"] >= 1;
            });

            $_companies_above_one_percent = array_map(function ($company) {
                $company["subs"][] = $company;
                return $company;
            }, $_companies_above_one_percent, []);

            $_companies_below_one_percent = array_filter($companies, function ($company) {
                return (double)$company["percentage"] < 1;
            });

            $total_below_one_percent = array_reduce($_companies_below_one_percent, function ($carry, $item) {
                return $carry + $item["cnt"];
            });

            $other_subs = array_map(function ($company) {
                return $company;
            }, $_companies_below_one_percent, []);

            $others = array(
                "company" => "OTHER",
                "cnt" => (double)$total_below_one_percent,
                "total" => (double)$_companies_above_one_percent[0]["total"],
                "subs" => $other_subs
            );

            array_push($_companies_above_one_percent, $others);*/
            // CODE GROUPING COMPANIES WITH LESS THAN 1% POPULATION INTO OTHERS

            $total = array_reduce($companies, function ($carry, $item) {
                return $carry + $item["cnt"];
            });

            return array(
                "data" => $companies,
                "total" => number_format($total, 0, '.', ','),
                "sql" => $sql
            );
        }

        public function getActiveEmployeesOnEachCompanyTabular() {
            $select = "MAX(company.id), IF(company.id IS NULL, emp.company_id, company.code) company, 
                       COUNT(*) total, 
                       (SELECT COUNT(*) FROM gccmaster.tblemployees _emp 
                           WHERE _emp.work_status='Regular' AND _emp.employee_status='Active' 
                           AND _emp.company_id = IF(company.id IS NULL, emp.company_id, company.code)) regular,
                       (SELECT COUNT(*) FROM gccmaster.tblemployees _emp 
                           WHERE _emp.work_status='Probationary' AND _emp.employee_status='Active' 
                           AND _emp.company_id = IF(company.id IS NULL, emp.company_id, company.code)) probationary,
                       (SELECT COUNT(*) FROM gccmaster.tblemployees _emp 
                           WHERE _emp.work_status='service contract' AND _emp.employee_status='Active' 
                           AND _emp.company_id = IF(company.id IS NULL, emp.company_id, company.code)) service_contract,
                       (SELECT COUNT(*) FROM gccmaster.tblemployees _emp 
                           WHERE _emp.work_status='no contract' AND _emp.employee_status='Active' 
                           AND _emp.company_id = IF(company.id IS NULL, emp.company_id, company.code)) no_contract";

            $this->db->select($select);
            $this->db->where("IF(company.id IS NULL, emp.company_id, company.code) IS NOT NULL", NULL, FALSE);
            $this->db->where("emp.work_status IN ('Regular', 'Probationary', 'Service contract', 'No contract')", NULL, FALSE);
            $this->db->where("emp.employee_status", 'Active');
            $this->db->join($this->tblCompanies . " company", 'emp.company_id = company.id', 'LEFT');
            $this->db->group_by('IF(company.id IS NULL, emp.company_id, company.code)');
            $this->db->order_by('COUNT(*) DESC');
            $query = $this->db->get($this->tblEmployees . " emp");
            return $query->result();
        }

        public function getEmployeesWithAnniversary() {
            $select = "UCASE(
                          CONCAT(
                              emp.firstname, ' ', 
                              CASE 
                                  WHEN emp.middlename IS NOT NULL AND emp.suffix != '' AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.middlename)
                                  ELSE ''
                              END,
                              ' ', emp.lastname, 
                              CASE 
                                  WHEN emp.suffix IS NOT NULL AND emp.suffix != '' AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                                  ELSE ''
                              END
                          )
                       ) employee_name,
                       emp.date_start,
                       DATE_FORMAT(emp.date_start, '%b %d, %Y') anniversary,
                       IF(DAY(emp.date_start) = DAY(CURDATE()), 1, 0) highlight,
                       IF(TIMESTAMPDIFF(YEAR, emp.date_start, NOW()) <= 0, 
                           '',
                           CONCAT(
                               TIMESTAMPDIFF(YEAR,emp.date_start, NOW()), 
                               CASE
                                   WHEN TIMESTAMPDIFF(YEAR,emp.date_start, NOW()) %100 BETWEEN 11 AND 13 THEN 'th Year'
                                   WHEN TIMESTAMPDIFF(YEAR,emp.date_start, NOW()) %10 = 1 THEN 'st Year'
                                   WHEN TIMESTAMPDIFF(YEAR,emp.date_start, NOW()) %10 = 2 THEN 'nd Year'
                                   WHEN TIMESTAMPDIFF(YEAR,emp.date_start, NOW()) %10 = 3 THEN 'rd Year'
                                   ELSE 'th Year'
                               END
                           )
                       ) years_in_service,
                       emp.pic_filename,
                       emp.id";

            $this->db->select($select);
            $this->db->where("emp.employee_status", 'Active');
            $this->db->where("MONTH(DATE_ADD(emp.date_start, INTERVAL 1 YEAR)) = MONTH(CURDATE())", NULL, FALSE);
            $this->db->where("TIMESTAMPDIFF(YEAR,emp.date_start, NOW()) > 0", NULL, FALSE);
            $this->db->order_by('DAY(emp.date_start) ASC');
            $query = $this->db->get($this->tblEmployees . " emp");
            return $query->result();
        }

        public function getEmployeesWithBirthDay($date = null) {
            $date = empty($date) ? date('Y-m-d') : $date;
            $select = "UCASE(
                           CONCAT(
                               emp.firstname, ' ', 
                               CASE 
                                   WHEN emp.middlename IS NOT NULL AND emp.suffix != '' AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.middlename)
                                   ELSE ''
                               END,
                               ' ', emp.lastname, 
                               CASE 
                                   WHEN emp.suffix IS NOT NULL AND emp.suffix != '' AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                                   ELSE ''
                               END
                           )
                        ) employee_name,
                        DATE_FORMAT(emp.bday, '%M %d') bday,
                        IF(DAY(emp.bday) = DAY(DATE('$date')), 1, 0) highlight,
                        emp.pic_filename,
                        emp.id";

            $this->db->select($select);
            $this->db->where("emp.employee_status", 'Active');
            $this->db->where("MONTH(DATE_ADD(emp.bday, INTERVAL 1 YEAR)) = MONTH(DATE('$date'))", NULL, FALSE);
            $this->db->order_by('DAY(emp.bday) ASC');
            $query = $this->db->get($this->tblEmployees . " emp");
            return $query->result();
        }

        public function getNewlyHiredEmployees() {
            $select = "UCASE(
                         CONCAT(
                             emp.firstname, ' ', 
                             CASE 
                                 WHEN emp.middlename IS NOT NULL AND emp.suffix != '' AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.middlename)
                                 ELSE ''
                             END,
                             ' ', emp.lastname, 
                             CASE 
                                 WHEN emp.suffix IS NOT NULL AND emp.suffix != '' AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                                 ELSE ''
                             END
                         )
                      ) employee_name,
                      DATE_FORMAT(emp.date_start, '%b %d, %Y') date_start,
                      emp.pic_filename,
                      emp.id";
            $this->db->select($select);
            $this->db->where("emp.employee_status", 'Active');
            $this->db->where("MONTH(emp.date_start) = MONTH(CURDATE())", NULL, FALSE);
            $this->db->where("YEAR(emp.date_start) = YEAR(CURDATE())", NULL, FALSE);
            $this->db->order_by('DAY(emp.date_start) ASC');
            $query = $this->db->get($this->tblEmployees . " emp");
            return $query->result();
        }

        public function getOnLeaveEmployees() {
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $select = "UCASE(
                           CONCAT(
                               emp.firstname, ' ', 
                               ' ', emp.lastname, 
                               CASE 
                                   WHEN emp.suffix IS NOT NULL AND emp.suffix != '' AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                                   ELSE ''
                               END
                           )
                        ) employee_name,
                       loa.reference_no, 
                       loa.`status`, 
                       loa.nature, 
                       loa.date_from, 
                       loa.date_to,
                       loa.type leave_type";

            $searchFields = "CONCAT(emp.firstname,emp.middlename,emp.lastname,loa.status,loa.nature,loa.date_from,loa.date_to,loa.reference_no)";
            $where = "loa.`status` IN('Pending', 'Approved') AND DATE(date_from) >= CURDATE()";
            $joinArr = array(
                array("table" => $this->tblEmployees . " emp", "condition" => "emp.id = loa.employee", "option" => "INNER")
            );
            $this->db->select($select);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $this->db->where($where);
            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $this->db->like($search['field'], $search['key'], $search['option']);

            $query = $this->db->get($this->tblLoa . " loa");
            $resultSet['data'] = $query->result();
            $resultSet['recordsTotal'] = $this->utilities->getTableCount($this->tblLoa . " loa", $where, $search, $joinArr);
            $resultSet['recordsFiltered'] = $this->utilities->getTableCount($this->tblLoa . " loa", $where, $search, $joinArr);

            return $resultSet;
        }

        public function getEvaluationList($evaluation) {
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
            $where = null;
            $selectMonth = null;
            $current_date = date('Y-m-d');

            $select = "emp.idno,
                       'first_eval' evaluation_stage, 
                       `emp`.`id` `emp_id`,
                       emp.lastname, emp.firstname, emp.middlename, emp.suffix, 
                       UCASE(CONCAT(
                            emp.firstname, ' ', 
                            IF((emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none'), ' ', CONCAT(SUBSTR(emp.middlename,1,1), '. ')), 
                            emp.lastname, ' ',
                            IF((emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none'), ' ', emp.suffix)
                         )) employee_name,
                       `calendar`.`id` `cal_id`,
                       `emp`.`date_start`, 
                       `emp`.`date_end_prob`, 
                       UCASE(IF(companies.id IS NULL, emp.company_id, companies.code)) company,
                       UCASE(IF(positions.id IS NULL, emp.position, positions.name)) `position`";

            $where = "emp.work_status = 'PROBATIONARY' AND emp.employee_status = 'Active' ";

            // will show all unevaluated employees on first, second or finale evaluation
            switch ($evaluation) {
                case "2nd":
                    $select .= ", DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) `evaluation_date`";
                    $where .= "AND DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) IS NOT NULL AND calendar.first_eval_date IS NULL";
                    break;
                case "final":
                    $select .= ", DATE_ADD(emp.date_start, INTERVAL 5 MONTH) `evaluation_date`";
                    $where .= "AND calendar.date_discontinued IS NULL	AND DATE_ADD(emp.date_start, INTERVAL 5 MONTH) IS NOT NULL";
                    break;
                default:
                    $select .= ", date_add(emp.date_start, interval 3 month) `evaluation_date`";
                    $where .= "AND date_add(emp.date_start, interval 3 month) IS NOT NULL AND calendar.first_eval_date IS NULL";
                    break;
            }

            $searchFields = "CONCAT(emp.firstname, emp.middlename, emp.lastname, emp.date_start, IF(companies.id IS NULL, emp.company_id, companies.code),
                                    IF(positions.id IS NULL, emp.position, positions.name))";

            $joinArr = array(
                array("table" => "`gcchris`.`tblprobicalendar` `calendar`", "condition" => "`calendar`.`emp_id` = `emp`.`id`", "option" => "LEFT"),
                array("table" => $this->tblCompanies . " companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
                array("table" => $this->tblPosition . " positions", "condition" => "positions.id = emp.position", "option" => "LEFT")
            );

            $this->db->select($select);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $this->db->where($where);

            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $this->db->like($search['field'], $search['key'], $search['option']);

            $query = $this->db->get($this->tblEmployees . " emp");

            foreach($query->result() as $row) {
                $res = [];
                $date = date('Y-m-d', strtotime($row->evaluation_date));

                if ($date > $current_date) {
                    $res['cal_id'] = $row->cal_id;
                    $res['company'] = $row->company;
                    $res['date_end_prob'] = $row->date_end_prob;
                    $res['date_start'] = $row->date_start;
                    $res['emp_id'] = $row->emp_id;
                    $res['employee_name'] = $row->employee_name;
                    $res['evaluation_date'] = $row->evaluation_date;
                    $res['evaluation_stage'] = $row->evaluation_stage;
                    $res['firstname'] = $row->firstname;
                    $res['idno'] = $row->idno;
                    $res['lastname'] = $row->lastname;
                    $res['middlename'] = $row->middlename;
                    $res['position'] = $row->position;
                    $res['suffix'] = $row->suffix;
            
                    // Add the result to the $res_data array
                    $res_data[] = $res;
                }
            }

            $resultSet['data'] = $res_data;
            $resultSet['recordsTotal'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $search, $joinArr);
            $resultSet['recordsFiltered'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $search, $joinArr);
            
            /*** remove consumes database records
             * if($search['key'] != "" && $search['key']):
             * $this->core_layout->logNotification("User ".$this->loggedInUsername." has searched ".$search['key']." using dashboard employee evaluation datatable.","success","hris","user");
             * endif; 
            remove consumes database records ***/
            
            return $resultSet;
        }

        public function getEvaluationListOverdue() {
            $current_date = date('Y-m-d');
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
            $where = null;
            $selectMonth = null;
            $employee_data = [];
            $stage = $this->input->post('evaluation_stage');
        
            $select = "emp.idno,
                    
                       `emp`.`id` `emp_id`,
                       emp.lastname, emp.firstname, emp.middlename, emp.suffix, 
                       UCASE(CONCAT(
                            emp.firstname, ' ', 
                            IF((emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none'), ' ', CONCAT(SUBSTR(emp.middlename,1,1), '. ')), 
                            emp.lastname, ' ',
                            IF((emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none'), ' ', emp.suffix)
                         )) employee_name,
                       `calendar`.`id` `cal_id`,
                       `emp`.`date_start`, 
                       `emp`.`date_end_prob`, 
                       UCASE(IF(companies.id IS NULL, emp.company_id, companies.code)) company,
                       UCASE(IF(positions.id IS NULL, emp.position, positions.name)) `position`,";

            $where = "emp.work_status = 'PROBATIONARY' AND emp.employee_status = 'Active' ";

            switch($stage) {
                case 1: // 3rd month
                    $select .= "DATE_ADD(emp.date_start, INTERVAL 3 MONTH) `evaluation_date_first`";
                    $where .= "AND date_add(emp.date_start, interval 3 month) IS NOT NULL AND calendar.first_eval_date IS NULL";
                    break;

                case 2: // 4.5th month
                    $select .= "DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) `evaluation_date_second`";
                    $where .= "AND DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) IS NOT NULL AND calendar.first_eval_date IS NULL";
                    break;

                case 3: // Final
                    $select .= "DATE_ADD(emp.date_start, INTERVAL 5 MONTH) `evaluation_date_final`";
                    $where .= "AND calendar.date_discontinued IS NULL AND DATE_ADD(emp.date_start, INTERVAL 5 MONTH) IS NOT NULL";
                    break;

                default: // All
                    $select .= "DATE_ADD(emp.date_start, INTERVAL 3 MONTH) `evaluation_date_first`,
                               DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) `evaluation_date_second`,
                               DATE_ADD(emp.date_start, INTERVAL 5 MONTH) `evaluation_date_final`";
                    break;
            }

            $searchFields = "CONCAT(emp.firstname, emp.middlename, emp.lastname, emp.date_start, IF(companies.id IS NULL, emp.company_id, companies.code),
                                    IF(positions.id IS NULL, emp.position, positions.name))";

            $joinArr = array(
                array("table" => "`gcchris`.`tblprobicalendar` `calendar`", "condition" => "`calendar`.`emp_id` = `emp`.`id`", "option" => "LEFT"),
                array("table" => $this->tblCompanies . " companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
                array("table" => $this->tblPosition . " positions", "condition" => "positions.id = emp.position", "option" => "LEFT")
            );

            $this->db->select($select);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $this->db->where($where);

            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $this->db->like($search['field'], $search['key'], $search['option']);

            $query = $this->db->get($this->tblEmployees . " emp");
        
            // List all employee first with & without overdue evaluation
            foreach($query->result() as $row) {
                $eval_date = [];

                switch($stage) {
                    case 1: // 3rd month
                        $date = date('Y-m-d', strtotime($row->evaluation_date_first));
                        if ($date < $current_date) {
                            $datediff = strtotime($current_date) - strtotime($date);
        
                            $esd = array(
                                "evaluation_stage" => "3RD MONTH", 
                                "evaluation_date" => $date,
                                "overdue_date" => round($datediff / (60 * 60 * 24))
                            );
                            array_push($eval_date, $esd);
                        }
                        break;
    
                    case 2: // 4.5th month
                        $date = date('Y-m-d', strtotime($row->evaluation_date_second));
                        if ($date < $current_date) {
                            $datediff = strtotime($current_date) - strtotime($date);
        
                            $esd = array(
                                "evaluation_stage" => "4.5TH MONTH", 
                                "evaluation_date" => $date,
                                "overdue_date" => round($datediff / (60 * 60 * 24))
                            );
                            array_push($eval_date, $esd);
                        }
                        break;
    
                    case 3: // Final
                        $date = date('Y-m-d', strtotime($row->evaluation_date_final));
                        if ($date < $current_date) {
                            $datediff = strtotime($current_date) - strtotime($date);
        
                            $esd = array(
                                "evaluation_stage" => "FINAL", 
                                "evaluation_date" => $date,
                                "overdue_date" => round($datediff / (60 * 60 * 24))
                            );
                            array_push($eval_date, $esd);
                        }
                        break;
    
                    default: // All
                        $first_eval = date('Y-m-d', strtotime($row->evaluation_date_first));
                        $sec_eval = date('Y-m-d', strtotime($row->evaluation_date_second));
                        $final_eval = date('Y-m-d', strtotime($row->evaluation_date_final));
        
                        if ($first_eval < $current_date) {
                            $datediff = strtotime($current_date) - strtotime($first_eval);
        
                            $esd = array(
                                "evaluation_stage" => "3RD MONTH", 
                                "evaluation_date" => $first_eval,
                                "overdue_date" => round($datediff / (60 * 60 * 24))
                            );
                            array_push($eval_date, $esd);
                        }
                        if ($sec_eval < $current_date) {
                            $datediff = strtotime($current_date) - strtotime($sec_eval);
        
                            $esd = array(
                                "evaluation_stage" => "4.5TH MONTH", 
                                "evaluation_date" => $sec_eval,
                                "overdue_date" => round($datediff / (60 * 60 * 24))
                            );
                            array_push($eval_date, $esd);
                        }
                        if ($final_eval < $current_date) {
                            $datediff =  strtotime($current_date) - strtotime($final_eval);
        
                            $esd = array(
                                "evaluation_stage" => "FINAL", 
                                "evaluation_date" => $final_eval,
                                "overdue_date" => round($datediff / (60 * 60 * 24))
                            );
                            array_push($eval_date, $esd);
                        }
    
                        break;
                }

                $employee_data['cal_id'] = $row->cal_id;
                $employee_data['idno'] = $row->idno;
                $employee_data['company'] = $row->company;
                $employee_data['date_end_prob'] = $row->date_end_prob;
                $employee_data['date_start'] = $row->date_start;
                $employee_data['emp_id'] = $row->emp_id;
                $employee_data['employee_name'] = $row->employee_name;
                $employee_data['firstname'] = $row->firstname;
                $employee_data['lastname'] = $row->lastname;
                $employee_data['middlename'] = $row->middlename;
                $employee_data['position'] = $row->position;
                $employee_data['suffix'] = $row->suffix;
                $employee_data['eval_stage_date'] = $eval_date;

                $res_emp_data[] = $employee_data;
            }

            $overdue_emp = [];

            // Find employee with overdue evaluation
            foreach($res_emp_data as $emp) {
                if (!empty($emp['eval_stage_date'])) {
                    $emp_data = array(
                        "cal_id" => $emp['cal_id'],
                        "idno" => $emp['idno'],
                        "company" => $emp['company'],
                        "date_end_prob" => $emp['date_end_prob'],
                        "date_start" => $emp['date_start'],
                        "emp_id" => $emp['emp_id'],
                        "employee_name" => $emp['employee_name'],
                        "firstname" => $emp['firstname'],
                        "lastname" => $emp['lastname'],
                        "middlename" => $emp['middlename'],
                        "position" => $emp['position'],
                        "suffix" => $emp['suffix'],
                        "eval_stage_date" => $emp['eval_stage_date']
                    );
                    array_push($overdue_emp, $emp_data);
                }
            }

            $resultSet['data'] = $overdue_emp;
            $resultSet['recordsTotal'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $search, $joinArr);
            $resultSet['recordsFiltered'] = $this->utilities->getTableCount($this->tblEmployees . " emp", $where, $search, $joinArr);
            
            /*** remove consumes database records
             * if($search['key'] != "" && $search['key']):
             * $this->core_layout->logNotification("User ".$this->loggedInUsername." has searched ".$search['key']." using dashboard employee evaluation datatable.","success","hris","user");
             * endif; 
            remove consumes database records ***/
            
            return $resultSet;
        }

        public function getRetentionRate($ctr=0) {
            $items = array();
            $total = $this->getTotalEmployee(); // total employees
            $year = date('Y');
            $month = date('m');

            /*** Start - Supper Notty Was Here ***/
            if($ctr > 0){
                $maxMonth = 12;
                $tempMonth = (int) $month - (int) $ctr;
                $currYear = $year;
                
                if($tempMonth < 0){
                    $tempMonth = $maxMonth - abs($tempMonth);
                    $year = date('Y', strtotime('-1 year'));
                }else if($tempMonth == 0){
                    $tempMonth = $maxMonth;
                    $year = date('Y', strtotime('-1 year'));
                }

                $previousYear = $year < $currYear;

                if($previousYear){
                    foreach ($this->months as $_month) {
                        $loopMonth = (int) $_month["index"];

                        if ((int) $loopMonth >= (int) $tempMonth) {
                            $tempTotal = $this->getTempTotalEmployee($loopMonth, $year);

                            $_new = $this->getNewlyHired($loopMonth, $year);
                            $_resign = $this->getResigned($loopMonth, $year);
                            $date = date("Y-m-d", strtotime("$year-$loopMonth-1"));
                            $monthName = date("F", strtotime($date));
            
                            $data = array();
                            $data["d"] = $monthName;
                            $data["date"] = $date;
                            $data["newly_hired"] = $_new ? (int)$_new->newly_hired : 0;
                            $data["resigned"] = $_resign ? (int)$_resign->resigned : 0;
                            $data["total"] = (int) $tempTotal;
                            array_push($items, $data);
                        }
                    }

                    foreach ($this->months as $_month) {
                        $loopMonth = (int) $_month["index"];
                        $currMonth = (int) $month;
                        if ($loopMonth > $currMonth) { continue; }

                        $tempTotal = $this->getTempTotalEmployee($loopMonth, $currYear);
                        $_new = $this->getNewlyHired($loopMonth, $currYear);
                        $_resign = $this->getResigned($loopMonth, $currYear);
                        $date = date("Y-m-d", strtotime("$currYear-$loopMonth-1"));
                        $monthName = date("F", strtotime($date));
        
                        $data = array();
                        $data["d"] = $monthName;
                        $data["date"] = $date;
                        $data["newly_hired"] = $_new ? (int)$_new->newly_hired : 0;
                        $data["resigned"] = $_resign ? (int)$_resign->resigned : 0;
                        $data["total"] = (int) $tempTotal;
                        array_push($items, $data);
                    }
                }

                foreach ($this->months as $_month) {
                    $loopMonth = (int) $_month["index"];
                    $currMonth = (int) $month;
                    if($loopMonth >= $tempMonth && $loopMonth <= $currMonth){
                        $tempTotal = $this->getTempTotalEmployee($loopMonth, $currYear);

                        $_new = $this->getNewlyHired($loopMonth, $currYear);
                        $_resign = $this->getResigned($loopMonth, $currYear);
                        $date = date("Y-m-d", strtotime("$currYear-$loopMonth-1"));
                        $monthName = date("F", strtotime($date));
        
                        $data = array();
                        $data["d"] = $monthName;
                        $data["date"] = $date;
                        $data["newly_hired"] = $_new ? (int)$_new->newly_hired : 0;
                        $data["resigned"] = $_resign ? (int)$_resign->resigned : 0;
                        $data["total"] = (int) $tempTotal;
                        array_push($items, $data);
                    }
                }

            }else{
                /*** Start - Supper Notty - Code Not Mine ***/
                foreach ($this->months as $_month) {
                    if ((int)$_month["index"] > (int)$month) {
                        continue;
                    }
                    $_new = $this->getNewlyHired($_month["index"], $year);
                    $_resign = $this->getResigned($_month["index"], $year);
                    
                    $date = date_create($year . "-" . $_month["index"] . "-1");
                    $data = array();
                    $data["d"] = $_month["name"];
                    $data["date"] = date_format($date, 'Y-m-d');
                    $data["newly_hired"] = $_new ? (int)$_new->newly_hired : 0;
                    $data["resigned"] = $_resign ? (int)$_resign->resigned : 0;
                    $data["total"] = (int) $total;
                    /*** $total += $_new ? (int)$_new->newly_hired : 0; ***/
                    array_push($items, $data);
                }
                /*** End - Supper Notty - Code Not Mine ***/
            }
            /*** End - Supper Notty Was Here ***/

            return $items;
        }

        private function getResigned($month, $year) {
            $this->db->select("COUNT(*) resigned, DATE_FORMAT(emp_resigned.date_end, '%b') `d`,
                DATE_FORMAT(emp_resigned.date_end,'%Y-%m-01') `date`,
                DATE_SUB(DATE_FORMAT(emp_resigned.date_end,'%Y-%m-01'), INTERVAL 1 MONTH) `previousDate`", FALSE);

            /*** $this->db->where("emp_resigned.date_end IS NOT NULL 
                 AND emp_resigned.date_end != '0000-00-00' 
                AND YEAR(emp_resigned.date_end)=YEAR(CURDATE()) 
                AND ((emp_resigned.employee_status='RESIGN' OR emp_resigned.employee_status='RESIGNED') 
                OR (emp_resigned.work_status = 'RESIGN' OR emp_resigned.work_status = 'RESIGNED')) 
                AND (MONTH(emp_resigned.date_end)=$month AND YEAR(emp_resigned.date_end)=$year)", NULL, FALSE); ***/

            $this->db->where("emp_resigned.date_end IS NOT NULL 
                AND emp_resigned.date_end != '0000-00-00' 
                AND ((emp_resigned.employee_status='RESIGN' OR emp_resigned.employee_status='RESIGNED') 
                OR (emp_resigned.work_status = 'RESIGN' OR emp_resigned.work_status = 'RESIGNED')) 
                AND (MONTH(emp_resigned.date_end)=$month AND YEAR(emp_resigned.date_end)=$year)", NULL, FALSE);

            $this->db->group_by("YEAR(emp_resigned.date_end), MONTH(emp_resigned.date_end)");
            $query = $this->db->get("gccmaster.tblemployees emp_resigned");

            return $query->row();
        }

        private function getNewlyHired($month, $year) {
            $this->db->select("COUNT(*) newly_hired, DATE_FORMAT(emp_new.date_start, '%b') `d`, 
                DATE_FORMAT(emp_new.date_start,'%Y-%m-01') `date`, 
                DATE_SUB(DATE_FORMAT(emp_new.date_start,'%Y-%m-01'), INTERVAL 1 MONTH) `previousDate`", FALSE);
            
            /*** $this->db->where("emp_new.date_start IS NOT NULL 
                 AND emp_new.date_start != '0000-00-00' 
                AND YEAR(emp_new.date_start)=YEAR(CURDATE()) 
                AND emp_new.employee_status='Active' AND (MONTH(emp_new.date_start)=$month AND YEAR(emp_new.date_start)=$year)", NULL, FALSE); ***/

            $this->db->where("emp_new.date_start IS NOT NULL 
                AND emp_new.date_start != '0000-00-00'
                AND emp_new.employee_status='Active' AND (MONTH(emp_new.date_start)=$month AND YEAR(emp_new.date_start)=$year)", NULL, FALSE);
            $this->db->group_by("YEAR(emp_new.date_start), MONTH(emp_new.date_start)");
            $query = $this->db->get("gccmaster.tblemployees emp_new ");

            return $query->row();
        }

        public function getTotalEmployee() {
            $this->db->select("COUNT(*) total");
            $this->db->where("YEAR(emp.date_start) < YEAR(CURDATE() - 1) AND emp.date_end = '0000-00-00' AND emp.employee_status='Active'", NULL, FALSE);
            $query = $this->db->get("gccmaster.tblemployees emp");

            return $query->row("total");
        }

        public function getTempTotalEmployee($month, $year) {
            $lastDay = date("t", strtotime("{$year}-{$month}-1"));
            $nDate = date("Y-m-d", strtotime("{$year}-{$month}-{$lastDay}"));

            $this->db->select("COUNT(*) total");
            $this->db->where("DATE(emp.date_start) <= '$nDate' AND emp.date_end = '0000-00-00' AND emp.employee_status='Active'", NULL, FALSE);
            $query = $this->db->get("gccmaster.tblemployees emp");
            return $query->row("total");
        }

        public function getPersonnelRequestSummary() {
            $resultSet = array();

            array_push($resultSet,
                array(
                    "category" => strtoupper("Unresolved Request"),
                    "value" => $this->getUnresolvedPersonnelRequest(),
                    "data" => array("key" => "unresolved")
                )
            );
            array_push($resultSet,
                array(
                    "category" => strtoupper("Unresolved Overdue"),
                    "value" => $this->getOverdueUnresolvedPersonnelRequest(),
                    "data" => array("key" => "overdue")
                )
            );
            array_push($resultSet,
                array(
                    "category" => strtoupper("Unresolved Overdue, 7 Days"),
                    "value" => $this->getOverdueUnresolvedPersonnelRequest7to30Days(),
                    "data" => array("key" => "overdue-seven-days")
                )
            );
            array_push($resultSet,
                array(
                    "category" => strtoupper("Unresolved Overdue, 30 Days"),
                    "value" => $this->getOverdueUnresolvedPersonnelRequest30to60Days(),
                    "data" => array("key" => "overdue-thirty-days")
                )
            );
            array_push($resultSet,
                array(
                    "category" => strtoupper("Unresolved Overdue, 60 Days"),
                    "value" => $this->getOverdueUnresolvedPersonnelRequest60to900Days(),
                    "data" => array("key" => "overdue-sixty-days")
                )
            );
            array_push($resultSet,
                array(
                    "category" => strtoupper("Unresolved Overdue, 90 Days"),
                    "value" => $this->getOverdueUnresolvedPersonnelRequest90Above(),
                    "data" => array("key" => "overdue-ninety-days")
                )
            );
            array_push($resultSet,
                array(
                    "category" => strtoupper("Personnel still Needed."),
                    "value" => $this->getPersonnelStillNeeded(),
                    "data" => array("key" => "still-needed")
                )
            );

            $total = array_reduce($resultSet, function ($carry, $item) {
                return $carry + $item["value"];
            });

            return array("data" => $resultSet, "total" => $total);
        }

        private function getUnresolvedPersonnelRequest() {
            $this->db->where("status", "Ongoing");
            $this->db->or_where("status", "forApproval");
            return $this->db->count_all_results("gcchris.tbapplication");
        }

        private function getOverdueUnresolvedPersonnelRequest() {
            $this->db->where("status=", "Ongoing");
            $this->db->where("is_archived", 0);
            $this->db->where("need_dt < CURDATE()", NULL, FALSE);
            return $this->db->count_all_results("gcchris.tbapplication");
        }

        private function getOverdueUnresolvedPersonnelRequest7to30Days() {
            $this->db->where("is_archived", 0);
            $this->db->where("status", "Ongoing");
            $this->db->where("DATEDIFF(CURDATE(), need_dt) >= 7 AND DATEDIFF(CURDATE(), need_dt) < 30", NULL, FALSE);
            return $this->db->count_all_results("gcchris.tbapplication");
        }

        private function getOverdueUnresolvedPersonnelRequest30to60Days() {
            $this->db->where("is_archived", 0);
            $this->db->where("status", "Ongoing");
            $this->db->where("DATEDIFF(CURDATE(), need_dt) >= 30 AND DATEDIFF(CURDATE(), need_dt) < 60", NULL, FALSE);
            return $this->db->count_all_results("gcchris.tbapplication");
        }

        private function getOverdueUnresolvedPersonnelRequest60to900Days() {
            $this->db->where("is_archived", 0);
            $this->db->where("status", "Ongoing");
            $this->db->where("DATEDIFF(CURDATE(), need_dt) >= 60 AND DATEDIFF(CURDATE(), need_dt) < 90", NULL, FALSE);
            return $this->db->count_all_results("gcchris.tbapplication");
        }

        private function getOverdueUnresolvedPersonnelRequest90Above() {
            $this->db->where("is_archived", 0);
            $this->db->where("status", "Ongoing");
            $this->db->where("DATEDIFF(CURDATE(), need_dt) > 90", NULL, FALSE);
            return $this->db->count_all_results("gcchris.tbapplication");
        }

        private function getPersonnelStillNeeded() {
            $this->db->where("status", "Ongoing");
            return $this->db->count_all_results("gcchris.tbapplication");
        }

        public function getYearList(){
            $this->db->select("YEAR(`add_date`) as years");
            $this->db->group_by("YEAR(`add_date`)");
            $this->db->order_by("YEAR(`add_date`)", "DESC");
            $query = $this->db->get("gccmaster.tblemployees");
            return $query->result();
        }

        public function getRetentionRateByYear($year=null){
            $items = array();
            if($year){
                $currYear = date("Y");
                $currMonth = date("m");
                $previousYear = $currYear > $year;

                foreach ($this->months as $_month) {
                    $loopMonth = (int) $_month["index"];
                    $nMonth = (int) $currMonth;

                    if ($previousYear == false && $loopMonth > $nMonth) { continue; }
                    $_new = $this->getNewlyHired($loopMonth, $year);
                    $_resign = $this->getResigned($loopMonth, $year);
                    $tempTotal = $this->getTempTotalEmployee($loopMonth, $year);
                    
                    $date = date_create($year . "-" . $loopMonth . "-1");
                    $data = array();
                    $data["d"] = $_month["name"];
                    $data["date"] = date_format($date, 'Y-m-d');
                    $data["newly_hired"] = $_new ? (int) $_new->newly_hired : 0;
                    $data["resigned"] = $_resign ? (int) $_resign->resigned : 0;
                    $data["total"] = (int) $tempTotal;

                    array_push($items, $data);
                }
            }
            return $items;
        }
    }
