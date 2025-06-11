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
            $current_date = date('Y-m-d');
        
            // DataTable parameters
            $postData = $this->input->post();
            $orderBy = array(array("column" => "1", "dir" => "desc"));
            $search = isset($postData["search"]["value"]) && $postData["search"]["value"] ? $postData["search"]["value"] : false;
            $limit = isset($postData["length"]) && $postData["length"] ? $postData["length"] : 10;
            $offset = isset($postData["start"]) && $postData["start"] ? $postData["start"] : 0;
            $sortColumn = isset($postData["columns"]) && $postData["columns"] ? $postData["columns"] : 1;
            $sortOrder = isset($postData["order"]) && $postData["order"] ? $postData["order"] : $orderBy;

            $filterFields = [
                "emp.firstname",
                "emp.middlename",
                "emp.lastname",
                "CONCAT(TRIM(emp.firstname), ' ', LEFT(TRIM(emp.middlename), 1), '.', ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.lastname), ' ', TRIM(emp.firstname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.middlename), ' ', TRIM(emp.lastname))",  
                // This is for fullname column sort 
                "CONCAT(
                    TRIM(emp.firstname), 
                    ' ',
                    CASE
                        WHEN LOWER(TRIM(emp.middlename)) = 'n/a' THEN ''
                        WHEN TRIM(emp.middlename) != '' THEN CONCAT(LEFT(TRIM(emp.middlename), 1), '. ')
                        ELSE ''
                    END,
                    TRIM(emp.lastname)
                )",  
                // This is for fullname column sort 
                "emp.idno",
                "emp.date_start",
                "first_eval", // evaluation stage
                "code",
                "name",
                // This is for date searching
                "DATE_FORMAT(emp.date_start, '%b %e')", 
                "DATE_FORMAT(emp.date_start, '%M %e')", 
                "DATE_FORMAT(emp.date_start, '%Y-%m-%d')",
                "DATE_FORMAT(emp.date_start, '%b %e %Y')", 
                "DATE_FORMAT(emp.date_start, '%M %e %Y')",
            ];                        
        
            $select = "emp.idno,
                        first_eval AS evaluation_stage, 
                        emp.id AS emp_id,
                        emp.lastname, emp.firstname, emp.middlename, emp.suffix, 
                        UCASE(CONCAT(
                            emp.firstname, ' ', 
                            IF((emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none'), '', CONCAT(SUBSTR(emp.middlename,1,1), '. ')), 
                            emp.lastname, ' ',
                            IF((emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none'), ' ', emp.suffix))) employee_name,
                        calendar.id AS cal_id,
                        emp.date_start,
                        emp.date_end_prob, 
                        UCASE(IF(companies.id IS NULL, emp.company_id, companies.code)) AS company,
                        UCASE(IF(positions.id IS NULL, emp.position, positions.name)) AS position";
        
            $where = "emp.work_status = 'PROBATIONARY' AND emp.employee_status = 'Active' ";
        
            // Will show all unevaluated employees on first, second or finale evaluation
            $evalDateExpr = "";
            switch ($evaluation) {
                case "2nd":
                    $evalDateExpr = "DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY)";
                    $select .= ", $evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr > '$current_date' 
                                AND calendar.second_eval_date IS NULL
                                AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')
                            ";
                    break;
                case "final":
                    $evalDateExpr = "DATE_ADD(emp.date_start, INTERVAL 5 MONTH)";
                    $select .= ", $evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr > '$current_date' 
                                AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    break;
                default:
                    $evalDateExpr = "DATE_ADD(emp.date_start, interval 3 month)";
                    $select .= ", $evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr > '$current_date' 
                                AND (calendar.first_eval_date IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    break;
            }

            /**
             * evaluation_date field aren't in tables
             * this will make the evaluation_date field searchable even though evaluation_date field is not in the table
             */
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%b %e')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%M %e')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%Y-%m-%d')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%b %e %Y')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%M %e %Y')";
        
            $joinArr = array(
                array("table" => "gcchris.tblprobicalendar AS calendar", "condition" => "calendar.emp_id = emp.id", "option" => "LEFT"),
                array("table" => $this->tblCompanies . " companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
                array("table" => $this->tblPosition . " positions", "condition" => "positions.id = emp.position", "option" => "LEFT")
            );
        
            $this->db->select($select);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
        
            $this->db->where($where);
        
            // SEARCH
            if (!empty($search)) {
                $search = preg_replace('/\s+/', ' ', trim($search)); // Normalize spaces!
                $search = preg_replace('/[^a-zA-Z0-9\s.-]/', '', $search); // Remove special characters except for dot & dashses

                $searchTerms = [$search];

                // Date filtering
                $dateInput = date_create_from_format('M j', $search) ?: date_create_from_format('F j', $search);
                if ($dateInput) {
                    $currentYear = date('Y');
                    $dateInput->setDate($currentYear, (int)$dateInput->format('m'), (int)$dateInput->format('d'));

                    // Different versions of the date format searched
                    $searchTerms[] = $dateInput->format('Y-m-d');        // 2025-01-07
                    $searchTerms[] = $dateInput->format('M j');          // Jan 7
                    $searchTerms[] = $dateInput->format('F j');          // January 7
                    $searchTerms[] = $dateInput->format('M j Y');        // Jan 7 2025
                    $searchTerms[] = $dateInput->format('F j Y');        // January 7 2025
                }
        
                $this->db->group_start();
                foreach ($filterFields as $field) {
                    foreach ($searchTerms as $term) {
                        $this->db->or_like($field, $term, "both");
                    }
                }
                $this->db->group_end();
            }
        
            // SORTING / ORDER BY
            if (isset($sortOrder[0]['column'])) {
                $columnIndex = $sortOrder[0]['column'];
                $this->db->order_by($sortColumn[$columnIndex]['data'], $sortOrder[0]['dir']);
            }
            
            // LIMIT
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }
        
            $query = $this->db->get($this->tblEmployees . " emp");
        
            $res_data = array();
            
            foreach($query->result() as $row) {
                $res = (array) $row;
                $res['checkbox'] = '';
                $res_data[] = $res;
            }
        
            $total = $this->getEvaluationList_count($search, $evaluation);
        
            $resultSet['data'] = $res_data;
            $resultSet['recordsTotal'] = $total;
            $resultSet['recordsFiltered'] = $total;
            
            return $resultSet;
        }
        
        public function getEvaluationList_count($search, $evaluation) {
            $current_date = date('Y-m-d');
        
            // DataTable parameters
            $postData = $this->input->post();
            $orderBy = array(array("column" => "1", "dir" => "desc"));
            $search = isset($postData["search"]["value"]) && $postData["search"]["value"] ? $postData["search"]["value"] : false;
            $limit = isset($postData["length"]) && $postData["length"] ? $postData["length"] : 10;
            $offset = isset($postData["start"]) && $postData["start"] ? $postData["start"] : 0;
            $sortColumn = isset($postData["columns"]) && $postData["columns"] ? $postData["columns"] : 1;
            $sortOrder = isset($postData["order"]) && $postData["order"] ? $postData["order"] : $orderBy;

            $filterFields = [
                "emp.firstname",
                "emp.middlename",
                "emp.lastname",
                "CONCAT(TRIM(emp.firstname), ' ', LEFT(TRIM(emp.middlename), 1), '.', ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.lastname), ' ', TRIM(emp.firstname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.middlename), ' ', TRIM(emp.lastname))",  
                // This is for fullname column sort 
                "CONCAT(
                    TRIM(emp.firstname), 
                    ' ',
                    CASE
                        WHEN LOWER(TRIM(emp.middlename)) = 'n/a' THEN ''
                        WHEN TRIM(emp.middlename) != '' THEN CONCAT(LEFT(TRIM(emp.middlename), 1), '. ')
                        ELSE ''
                    END,
                    TRIM(emp.lastname)
                )",  
                // This is for fullname column sort 
                "emp.idno",
                "emp.date_start",
                "first_eval", // evaluation stage
                "code",
                "name",
                // This is for date searching
                "DATE_FORMAT(emp.date_start, '%b %e')", 
                "DATE_FORMAT(emp.date_start, '%M %e')", 
                "DATE_FORMAT(emp.date_start, '%Y-%m-%d')",
                "DATE_FORMAT(emp.date_start, '%b %e %Y')", 
                "DATE_FORMAT(emp.date_start, '%M %e %Y')",
            ];    
        
            $select = "emp.idno,
                        first_eval AS evaluation_stage, 
                        emp.id AS emp_id,
                        emp.lastname, emp.firstname, emp.middlename, emp.suffix, 
                        UCASE(CONCAT(
                            emp.firstname, ' ', 
                            IF((emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none'), ' ', CONCAT(SUBSTR(emp.middlename,1,1), '. ')), 
                            emp.lastname, ' ',
                            IF((emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none'), ' ', emp.suffix))) employee_name,
                        calendar.id AS cal_id,
                        emp.date_start, 
                        emp.date_end_prob, 
                        UCASE(IF(companies.id IS NULL, emp.company_id, companies.code)) AS company,
                        UCASE(IF(positions.id IS NULL, emp.position, positions.name)) AS position";
        
            $where = "emp.work_status = 'PROBATIONARY' AND emp.employee_status = 'Active' ";
        
            // will show all unevaluated employees on first, second or finale evaluation
            $evalDateExpr = "";
            switch ($evaluation) {
                case "2nd":
                    $evalDateExpr = "DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY)";
                    $select .= ", $evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr > '$current_date' 
                                AND calendar.second_eval_date IS NULL
                                AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')
                            ";
                    break;
                case "final":
                    $evalDateExpr = "DATE_ADD(emp.date_start, INTERVAL 5 MONTH)";
                    $select .= ", $evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr > '$current_date' 
                                AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    break;
                default:
                    $evalDateExpr = "DATE_ADD(emp.date_start, interval 3 month)";
                    $select .= ", $evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr > '$current_date' 
                                AND (calendar.first_eval_date IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    break;
            }
        
            /**
             * evaluation_date field aren't in tables
             * this will make the evaluation_date field searchable even though evaluation_date field is not in the table
             */
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%b %e')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%M %e')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%Y-%m-%d')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%b %e %Y')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%M %e %Y')";
        
            $joinArr = array(
                array("table" => "gcchris.tblprobicalendar AS calendar", "condition" => "calendar.emp_id = emp.id", "option" => "LEFT"),
                array("table" => $this->tblCompanies . " companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
                array("table" => $this->tblPosition . " positions", "condition" => "positions.id = emp.position", "option" => "LEFT")
            );
        
            $this->db->select($select);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
        
            $this->db->where($where);
        
            // SEARCH
            if (!empty($search)) {
                $search = preg_replace('/\s+/', ' ', trim($search)); // Normalize spaces!
                $search = preg_replace('/[^a-zA-Z0-9\s.-]/', '', $search); // Remove special characters except for dot & dashses
        
                $searchTerms = [$search];
        
                // Date filtering
                $dateInput = date_create_from_format('M j', $search) ?: date_create_from_format('F j', $search);
                if ($dateInput) {
                    $currentYear = date('Y');
                    $dateInput->setDate($currentYear, (int)$dateInput->format('m'), (int)$dateInput->format('d'));
        
                    // Different versions of the date format searched
                    $searchTerms[] = $dateInput->format('Y-m-d');        // 2025-01-07
                    $searchTerms[] = $dateInput->format('M j');          // Jan 7
                    $searchTerms[] = $dateInput->format('F j');          // January 7
                    $searchTerms[] = $dateInput->format('M j Y');        // Jan 7 2025
                    $searchTerms[] = $dateInput->format('F j Y');        // January 7 2025
                }
        
                $this->db->group_start();
                foreach ($filterFields as $field) {
                    foreach ($searchTerms as $term) {
                        $this->db->or_like($field, $term, "both");
                    }
                }
                $this->db->group_end();
            }
        
            $query = $this->db->get($this->tblEmployees . " emp");
            return $query->num_rows();
        }

        public function evaluation_overdue() {
            $stage = $this->input->post('evaluation_stage');

            if ($stage != 0) {
                $res = $this->getEvaluationListOverdue($stage);
            } else {
                $res = $this->all_evaluation_overdue($stage);
            }

            return $res;
        }

        public function getEvaluationListOverdue($stage) {
            $current_date = date('Y-m-d');
        
            // DataTable parameters
            $postData = $this->input->post();
            $orderBy = array(array("column" => "1", "dir" => "desc"));
            $search = isset($postData["search"]["value"]) && $postData["search"]["value"] ? $postData["search"]["value"] : false;
            $limit = isset($postData["length"]) && $postData["length"] ? $postData["length"] : 10;
            $offset = isset($postData["start"]) && $postData["start"] ? $postData["start"] : 0;
            $sortColumn = isset($postData["columns"]) && $postData["columns"] ? $postData["columns"] : 1;
            $sortOrder = isset($postData["order"]) && $postData["order"] ? $postData["order"] : $orderBy;

            $filterFields = [
                "emp.firstname",
                "emp.middlename",
                "emp.lastname",
                "CONCAT(TRIM(emp.firstname), ' ', LEFT(TRIM(emp.middlename), 1), '.', ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.lastname), ' ', TRIM(emp.firstname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.middlename), ' ', TRIM(emp.lastname))",  
                // This is for fullname column sort 
                "CONCAT(
                    TRIM(emp.firstname), 
                    ' ',
                    CASE
                        WHEN LOWER(TRIM(emp.middlename)) = 'n/a' THEN ''
                        WHEN TRIM(emp.middlename) != '' THEN CONCAT(LEFT(TRIM(emp.middlename), 1), '. ')
                        ELSE ''
                    END,
                    TRIM(emp.lastname)
                )",  
                // This is for fullname column sort 
                "emp.idno",
                "emp.date_start",                
                "code",
                "name",
                // This is for date searching
                "DATE_FORMAT(emp.date_start, '%b %e')", 
                "DATE_FORMAT(emp.date_start, '%M %e')", 
                "DATE_FORMAT(emp.date_start, '%Y-%m-%d')",
                "DATE_FORMAT(emp.date_start, '%b %e %Y')", 
                "DATE_FORMAT(emp.date_start, '%M %e %Y')",
            ];  
        
            $select = "emp.idno,
                        emp.id AS emp_id,
                        emp.lastname, emp.firstname, emp.middlename, emp.suffix, 
                        UCASE(CONCAT(
                            emp.firstname, ' ', 
                            IF((emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none'), ' ', CONCAT(SUBSTR(emp.middlename,1,1), '. ')), 
                            emp.lastname, ' ',
                            IF((emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none'), ' ', emp.suffix))) employee_name,
                        calendar.id AS cal_id,
                        emp.date_start, 
                        emp.date_end_prob, 
                        UCASE(IF(companies.id IS NULL, emp.company_id, companies.code)) AS company,
                        UCASE(IF(positions.id IS NULL, emp.position, positions.name)) AS position,";
        
            $where = "emp.work_status = 'PROBATIONARY' AND emp.employee_status = 'Active' ";

            $evalDateExpr = "";
  
            switch($stage) {
                case 1: // 3rd month overdue
                    $evalDateExpr = "DATE_ADD(emp.date_start, INTERVAL 3 MONTH)";
                    $select .= "$evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr < '$current_date'
                            AND calendar.first_eval_date IS NULL
                            AND (calendar.second_eval IS NULL OR calendar.second_eval = '0000-00-00')
                            AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    $eval_stage = "3rd month";
                    break;

                case 2: // 4.5th month overdue
                    $evalDateExpr = "DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY)";
                    $select .= "$evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr < '$current_date'
                            AND calendar.second_eval_date IS NULL
                            AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    $eval_stage = "4.5th month";
                    break;

                case 3: // Final (5th month) overdue
                    $evalDateExpr = "DATE_ADD(emp.date_start, INTERVAL 5 MONTH)";
                    $select .= "$evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr < '$current_date'
                            AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    $eval_stage = "Final";
                    break;

                default: // fallback to 3rd month
                    $evalDateExpr = "DATE_ADD(emp.date_start, INTERVAL 3 MONTH)";
                    $select .= "$evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr < '$current_date'
                            AND calendar.first_eval_date IS NULL
                            AND (calendar.second_eval IS NULL OR calendar.second_eval = '0000-00-00')
                            AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    $eval_stage = "3rd month";
                    break;
            }

            /**
             * evaluation_date field aren't in tables
             * this will make the evaluation_date field searchable even though evaluation_date field is not in the table
             */
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%b %e')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%M %e')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%Y-%m-%d')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%b %e %Y')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%M %e %Y')";
            
        
            $joinArr = array(
                array("table" => "gcchris.tblprobicalendar AS calendar", "condition" => "calendar.emp_id = emp.id", "option" => "LEFT"),
                array("table" => $this->tblCompanies . " companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
                array("table" => $this->tblPosition . " positions", "condition" => "positions.id = emp.position", "option" => "LEFT")
            );
        
            $this->db->select($select);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
        
            $this->db->where($where);
        
            // SEARCH
            if (!empty($search)) {
                $search = preg_replace('/\s+/', ' ', trim($search)); // Normalize spaces!
                $search = preg_replace('/[^a-zA-Z0-9\s.-]/', '', $search); // Remove special characters except for dot & dashses

                $searchTerms = [$search];

                // Date filtering
                $dateInput = date_create_from_format('M j', $search) ?: date_create_from_format('F j', $search);
                if ($dateInput) {
                    $currentYear = date('Y');
                    $dateInput->setDate($currentYear, (int)$dateInput->format('m'), (int)$dateInput->format('d'));
        
                    // Different versions of the date format searched
                    $searchTerms[] = $dateInput->format('Y-m-d');        // 2025-01-07
                    $searchTerms[] = $dateInput->format('M j');          // Jan 7
                    $searchTerms[] = $dateInput->format('F j');          // January 7
                    $searchTerms[] = $dateInput->format('M j Y');        // Jan 7 2025
                    $searchTerms[] = $dateInput->format('F j Y');        // January 7 2025
                }
        
                $this->db->group_start();
                foreach ($filterFields as $field) {
                    foreach ($searchTerms as $term) {
                        $this->db->or_like($field, $term, "both");
                    }
                }
                $this->db->group_end();
            }
        
            // SORTING / ORDER BY
            if (isset($sortOrder[0]['column'])) {
                $columnIndex = $sortOrder[0]['column'];
                $this->db->order_by($sortColumn[$columnIndex]['data'], $sortOrder[0]['dir']);
            }
        
            // LIMIT
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }
        
            $query = $this->db->get($this->tblEmployees . " emp");
        
            // List all employee first with & without overdue evaluation
            $res_emp_data = array();
            foreach($query->result() as $row) {   
                $employee_data = (array) $row;
                
                // Evaluation stage and date START
                $eval_stage_date = [];
                
                $date = date('Y-m-d', strtotime($row->evaluation_date));
                $datediff = strtotime($current_date) - strtotime($date);
        
                $eval_date = array(
                    "evaluation_stage" => $eval_stage, 
                    "evaluation_date" => $date,
                    "overdue_date" => round($datediff / (60 * 60 * 24))
                );
        
                array_push($eval_stage_date, $eval_date);
                // Evaluation stage and date END
        
                $employee_data['checkbox'] = '';
                $employee_data['eval_stage_date'] = $eval_stage_date;
        
                $res_emp_data[] = $employee_data;
            }

            $total = $this->getEvaluationListOverdue_count($search, $stage);
        
            $resultSet['data'] = $res_emp_data;
            $resultSet['recordsTotal'] = $total;
            $resultSet['recordsFiltered'] = $total;
            
            return $resultSet;
        }

        public function getEvaluationListOverdue_count($search, $stage) {
            $current_date = date('Y-m-d');

            // DataTable parameters
            $postData = $this->input->post();
            $orderBy = array(array("column" => "1", "dir" => "desc"));
            $search = isset($postData["search"]["value"]) && $postData["search"]["value"] ? $postData["search"]["value"] : false;
            $limit = isset($postData["length"]) && $postData["length"] ? $postData["length"] : 10;
            $offset = isset($postData["start"]) && $postData["start"] ? $postData["start"] : 0;
            $sortColumn = isset($postData["columns"]) && $postData["columns"] ? $postData["columns"] : 1;
            $sortOrder = isset($postData["order"]) && $postData["order"] ? $postData["order"] : $orderBy;

            $filterFields = [
                "emp.firstname",
                "emp.middlename",
                "emp.lastname",
                "CONCAT(TRIM(emp.firstname), ' ', LEFT(TRIM(emp.middlename), 1), '.', ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.lastname), ' ', TRIM(emp.firstname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.middlename), ' ', TRIM(emp.lastname))",  
                // This is for fullname column sort 
                "CONCAT(
                    TRIM(emp.firstname), 
                    ' ',
                    CASE
                        WHEN LOWER(TRIM(emp.middlename)) = 'n/a' THEN ''
                        WHEN TRIM(emp.middlename) != '' THEN CONCAT(LEFT(TRIM(emp.middlename), 1), '. ')
                        ELSE ''
                    END,
                    TRIM(emp.lastname)
                )",  
                // This is for fullname column sort 
                "emp.idno",
                "emp.date_start",                
                "code",
                "name",
                // This is for date searching
                "DATE_FORMAT(emp.date_start, '%b %e')", 
                "DATE_FORMAT(emp.date_start, '%M %e')", 
                "DATE_FORMAT(emp.date_start, '%Y-%m-%d')",
                "DATE_FORMAT(emp.date_start, '%b %e %Y')", 
                "DATE_FORMAT(emp.date_start, '%M %e %Y')",
            ]; 

            $select = "emp.idno,
                        emp.id AS emp_id,
                        emp.lastname, emp.firstname, emp.middlename, emp.suffix, 
                        UCASE(CONCAT(
                            emp.firstname, ' ', 
                            IF((emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none'), ' ', CONCAT(SUBSTR(emp.middlename,1,1), '. ')), 
                            emp.lastname, ' ',
                            IF((emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none'), ' ', emp.suffix))) employee_name,
                        calendar.id AS cal_id,
                        emp.date_start, 
                        emp.date_end_prob, 
                        UCASE(IF(companies.id IS NULL, emp.company_id, companies.code)) AS company,
                        UCASE(IF(positions.id IS NULL, emp.position, positions.name)) AS position,";

            $where = "emp.work_status = 'PROBATIONARY' AND emp.employee_status = 'Active' ";

            $evalDateExpr = "";
            switch($stage) {
                case 1: // 3rd month overdue
                    $evalDateExpr = "DATE_ADD(emp.date_start, INTERVAL 3 MONTH)";
                    $select .= "$evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr < '$current_date'
                            AND calendar.first_eval_date IS NULL
                            AND (calendar.second_eval IS NULL OR calendar.second_eval = '0000-00-00')
                            AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    break;

                case 2: // 4.5th month overdue
                    $evalDateExpr = "DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY)";
                    $select .= "$evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr < '$current_date'
                            AND calendar.second_eval_date IS NULL
                            AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    break;

                case 3: // Final (5th month) overdue
                    $evalDateExpr = "DATE_ADD(emp.date_start, INTERVAL 5 MONTH)";
                    $select .= "$evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr < '$current_date'
                            AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    break;

                default: // fallback to 3rd month
                    $evalDateExpr = "DATE_ADD(emp.date_start, INTERVAL 3 MONTH)";
                    $select .= "$evalDateExpr AS evaluation_date";
                    $where .= "AND $evalDateExpr < '$current_date'
                            AND calendar.first_eval_date IS NULL
                            AND (calendar.second_eval IS NULL OR calendar.second_eval = '0000-00-00')
                            AND (calendar.date_discontinued IS NULL OR calendar.date_discontinued = '0000-00-00')";
                    break;
            }
        
            /**
             * evaluation_date field aren't in tables
             * this will make the evaluation_date field searchable even though evaluation_date field is not in the table
             */
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%b %e')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%M %e')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%Y-%m-%d')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%b %e %Y')";
            $filterFields[] = "DATE_FORMAT($evalDateExpr, '%M %e %Y')";

            $joinArr = array(
                array("table" => "gcchris.tblprobicalendar AS calendar", "condition" => "calendar.emp_id = emp.id", "option" => "LEFT"),
                array("table" => $this->tblCompanies . " companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
                array("table" => $this->tblPosition . " positions", "condition" => "positions.id = emp.position", "option" => "LEFT")
            );

            $this->db->select($select);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $this->db->where($where);

            // SEARCH
            if (!empty($search)) {
                $search = preg_replace('/\s+/', ' ', trim($search)); // Normalize spaces!
                $search = preg_replace('/[^a-zA-Z0-9\s.-]/', '', $search); // Remove special characters except for dot & dashses

                $searchTerms = [$search];

                // Date filtering
                $dateInput = date_create_from_format('M j', $search) ?: date_create_from_format('F j', $search);
                if ($dateInput) {
                    $currentYear = date('Y');
                    $dateInput->setDate($currentYear, (int)$dateInput->format('m'), (int)$dateInput->format('d'));
        
                    // Different versions of the date format searched
                    $searchTerms[] = $dateInput->format('Y-m-d');        // 2025-01-07
                    $searchTerms[] = $dateInput->format('M j');          // Jan 7
                    $searchTerms[] = $dateInput->format('F j');          // January 7
                    $searchTerms[] = $dateInput->format('M j Y');        // Jan 7 2025
                    $searchTerms[] = $dateInput->format('F j Y');        // January 7 2025
                }

                $this->db->group_start();
                foreach ($filterFields as $field) {
                    foreach ($searchTerms as $term) {
                        $this->db->or_like($field, $term, "both");
                    }
                }
                $this->db->group_end();
            }

            $query = $this->db->get($this->tblEmployees . " emp");
            return $query->num_rows();
        }

        public function all_evaluation_overdue($stage) {
            $current_date = date('Y-m-d');
        
            // DataTable parameters
            $postData = $this->input->post();
            $orderBy = array(array("column" => "1", "dir" => "desc"));
            $search = isset($postData["search"]["value"]) && $postData["search"]["value"] ? $postData["search"]["value"] : false;
            $limit = isset($postData["length"]) && $postData["length"] ? $postData["length"] : 10;
            $offset = isset($postData["start"]) && $postData["start"] ? $postData["start"] : 0;
            $sortColumn = isset($postData["columns"]) && $postData["columns"] ? $postData["columns"] : 1;
            $sortOrder = isset($postData["order"]) && $postData["order"] ? $postData["order"] : $orderBy;

            $filterFields = [
                "emp.firstname",
                "emp.middlename",
                "emp.lastname",
                "CONCAT(TRIM(emp.firstname), ' ', LEFT(TRIM(emp.middlename), 1), '.', ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.lastname), ' ', TRIM(emp.firstname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.middlename), ' ', TRIM(emp.lastname))",  
                // This is for fullname column sort 
                "CONCAT(
                    TRIM(emp.firstname), 
                    ' ',
                    CASE
                        WHEN LOWER(TRIM(emp.middlename)) = 'n/a' THEN ''
                        WHEN TRIM(emp.middlename) != '' THEN CONCAT(LEFT(TRIM(emp.middlename), 1), '. ')
                        ELSE ''
                    END,
                    TRIM(emp.lastname)
                )",  
                // This is for fullname column sort 
                "emp.idno",
                "emp.date_start",
                "code",
                "name",
                // This is for date searching
                "DATE_FORMAT(emp.date_start, '%b %e')", 
                "DATE_FORMAT(emp.date_start, '%M %e')", 
                "DATE_FORMAT(emp.date_start, '%Y-%m-%d')",
                "DATE_FORMAT(emp.date_start, '%b %e %Y')", 
                "DATE_FORMAT(emp.date_start, '%M %e %Y')",
            ];    
        
            $select = "emp.idno,
                emp.id AS emp_id,
                emp.lastname, emp.firstname, emp.middlename, emp.suffix, 
                UCASE(CONCAT(
                    emp.firstname, ' ', 
                    IF((emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none'), ' ', CONCAT(SUBSTR(emp.middlename,1,1), '. ')), 
                    emp.lastname, ' ',
                    IF((emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none'), ' ', emp.suffix))) employee_name,
                calendar.id AS cal_id,
                emp.date_start, 
                emp.date_end_prob, 
                UCASE(IF(companies.id IS NULL, emp.company_id, companies.code)) company,
                UCASE(IF(positions.id IS NULL, emp.position, positions.name)) position,
                DATE_ADD(emp.date_start, INTERVAL 3 MONTH) evaluation_date_first,
                DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) evaluation_date_second,
                DATE_ADD(emp.date_start, INTERVAL 5 MONTH) evaluation_date_final,
                calendar.first_eval,
                calendar.first_eval_date,
                calendar.second_eval,
                calendar.second_eval_date,
                calendar.date_discontinued,
                (DATE_ADD(emp.date_start, INTERVAL 3 MONTH) < '$current_date' AND calendar.first_eval_date IS NULL) AS is_3rd_overdue,
                (DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) < '$current_date' AND calendar.second_eval_date IS NULL) AS is_4th_overdue,
                (DATE_ADD(emp.date_start, INTERVAL 5 MONTH) < '$current_date' AND calendar.date_discontinued IS NULL) AS is_final_overdue
                ";

            $where = "
                    emp.work_status = 'PROBATIONARY' 
                    AND emp.employee_status = 'Active'
                    AND (
                        (DATE_ADD(emp.date_start, INTERVAL 3 MONTH) < '$current_date' AND calendar.first_eval_date IS NULL)
                        OR (DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) < '$current_date' AND calendar.second_eval_date IS NULL)
                        OR (DATE_ADD(emp.date_start, INTERVAL 5 MONTH) < '$current_date' AND calendar.date_discontinued IS NULL)
                    )
                    ";
        
            $joinArr = array(
                array("table" => "gcchris.tblprobicalendar AS calendar", "condition" => "calendar.emp_id = emp.id", "option" => "LEFT"),
                array("table" => $this->tblCompanies . " companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
                array("table" => $this->tblPosition . " positions", "condition" => "positions.id = emp.position", "option" => "LEFT")
            );
            $this->db->select($select);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
        
            $this->db->where($where);
        
            // SEARCH
            if (!empty($search)) {
                $search = preg_replace('/\s+/', ' ', trim($search)); // Normalize spaces!
                $search = preg_replace('/[^a-zA-Z0-9\s.-]/', '', $search); // Remove special characters except for dot & dashses

                $searchTerms = [$search];

                // Date filtering
                $dateInput = date_create_from_format('M j', $search) ?: date_create_from_format('F j', $search);
                if ($dateInput) {
                    $currentYear = date('Y');
                    $dateInput->setDate($currentYear, (int)$dateInput->format('m'), (int)$dateInput->format('d'));

                    // Different versions of the date format searched
                    $searchTerms[] = $dateInput->format('Y-m-d');        // 2025-01-07
                    $searchTerms[] = $dateInput->format('M j');          // Jan 7
                    $searchTerms[] = $dateInput->format('F j');          // January 7
                    $searchTerms[] = $dateInput->format('M j Y');        // Jan 7 2025
                    $searchTerms[] = $dateInput->format('F j Y');        // January 7 2025
                }
        
                $this->db->group_start();
                foreach ($filterFields as $field) {
                    foreach ($searchTerms as $term) {
                        $this->db->or_like($field, $term, "both");
                    }
                }
                $this->db->group_end();
            }
        
            // SORTING / ORDER BY
            if (isset($sortOrder[0]['column'])) {
                $columnIndex = $sortOrder[0]['column'];
                $this->db->order_by($sortColumn[$columnIndex]['data'], $sortOrder[0]['dir']);
            }
        
            // LIMIT
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }
        
            $query = $this->db->get($this->tblEmployees . " emp");
        
            // List all employee first with & without overdue evaluation
            $res_emp_data_all = [];
            foreach($query->result() as $row) {
                $employee_data = (array) $row;
                $eval_stage_date = [];

                $first_eval = date('Y-m-d', strtotime($row->evaluation_date_first));
                $sec_eval = date('Y-m-d', strtotime($row->evaluation_date_second));
                $final_eval = date('Y-m-d', strtotime($row->evaluation_date_final));

                // Return in boolean
                $is_3rd_done   = !empty($row->first_eval_date);
                $is_4th_done   = !empty($row->second_eval_date);
                $is_final_done = !empty($row->date_discontinued);

                // Return in boolean
                $is_3rd_overdue   = $first_eval < $current_date && !$is_3rd_done;
                $is_4th_overdue   = $sec_eval < $current_date && !$is_4th_done;
                $is_final_overdue = $final_eval < $current_date && !$is_final_done;

                // 3rd month overdue evaluation
                if ($is_3rd_overdue && !$is_4th_done && !$is_final_done) {
                    $datediff = strtotime($current_date) - strtotime($first_eval);

                    $eval_stage_date[] = array(
                        "evaluation_stage" => "3RD MONTH", 
                        "evaluation_date" => $first_eval,
                        "overdue_date" => round($datediff / (60 * 60 * 24))
                    );
                }
                
                // 4.5th month overdue evaluation
                if ($is_4th_overdue && !$is_final_done) {
                    $datediff = strtotime($current_date) - strtotime($sec_eval);

                    $eval_stage_date[] = array(
                        "evaluation_stage" => "4.5TH MONTH", 
                        "evaluation_date" => $sec_eval,
                        "overdue_date" => round($datediff / (60 * 60 * 24))
                    );                
                }

                // Final overdue evaluation
                if ($is_final_overdue) {
                    $datediff =  strtotime($current_date) - strtotime($final_eval);

                    $eval_stage_date[] = array(
                        "evaluation_stage" => "FINAL", 
                        "evaluation_date" => $final_eval,
                        "overdue_date" => round($datediff / (60 * 60 * 24))
                    );
                }
                
                $employee_data['checkbox'] = '';
                $employee_data['eval_stage_date'] = $eval_stage_date;

                $res_emp_data_all[] = $employee_data;
            }

            $total = $this->all_evaluation_overdue_count($search);

            $resultSet['data'] = $res_emp_data_all;
            $resultSet['recordsTotal'] = $total;
            $resultSet['recordsFiltered'] = $total;
            return $resultSet;
        }

        public function all_evaluation_overdue_count($search) {
            $current_date = date('Y-m-d');

            $filterFields = [
                "emp.firstname",
                "emp.middlename",
                "emp.lastname",
                "CONCAT(TRIM(emp.firstname), ' ', LEFT(TRIM(emp.middlename), 1), '.', ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.lastname))",  
                "CONCAT(TRIM(emp.lastname), ' ', TRIM(emp.firstname))",  
                "CONCAT(TRIM(emp.firstname), ' ', TRIM(emp.middlename), ' ', TRIM(emp.lastname))",  
                // This is for fullname column sort 
                "CONCAT(
                    TRIM(emp.firstname), 
                    ' ',
                    CASE
                        WHEN LOWER(TRIM(emp.middlename)) = 'n/a' THEN ''
                        WHEN TRIM(emp.middlename) != '' THEN CONCAT(LEFT(TRIM(emp.middlename), 1), '. ')
                        ELSE ''
                    END,
                    TRIM(emp.lastname)
                )",  
                // This is for fullname column sort 
                "emp.idno",
                "emp.date_start",
                "code",
                "name",
                // This is for date searching
                "DATE_FORMAT(emp.date_start, '%b %e')", 
                "DATE_FORMAT(emp.date_start, '%M %e')", 
                "DATE_FORMAT(emp.date_start, '%Y-%m-%d')",
                "DATE_FORMAT(emp.date_start, '%b %e %Y')", 
                "DATE_FORMAT(emp.date_start, '%M %e %Y')",
            ];

            $select = "emp.idno,
                emp.id AS emp_id,
                emp.lastname, emp.firstname, emp.middlename, emp.suffix, 
                UCASE(CONCAT(
                    emp.firstname, ' ', 
                    IF((emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none'), ' ', CONCAT(SUBSTR(emp.middlename,1,1), '. ')), 
                    emp.lastname, ' ',
                    IF((emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none'), ' ', emp.suffix))) employee_name,
                calendar.id AS cal_id,
                emp.date_start, 
                emp.date_end_prob, 
                UCASE(IF(companies.id IS NULL, emp.company_id, companies.code)) company,
                UCASE(IF(positions.id IS NULL, emp.position, positions.name)) position,
                DATE_ADD(emp.date_start, INTERVAL 3 MONTH) evaluation_date_first,
                DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) evaluation_date_second,
                DATE_ADD(emp.date_start, INTERVAL 5 MONTH) evaluation_date_final,
                calendar.first_eval,
                calendar.first_eval_date,
                calendar.second_eval,
                calendar.second_eval_date,
                calendar.date_discontinued";

            $where = "
                    emp.work_status = 'PROBATIONARY' 
                    AND emp.employee_status = 'Active'
                    AND (calendar.date_discontinued IS NULL)
                    AND (
                        (DATE_ADD(emp.date_start, INTERVAL 3 MONTH) < '$current_date' AND calendar.first_eval_date IS NULL)
                        OR (DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) < '$current_date' AND calendar.second_eval_date IS NULL)
                        OR (DATE_ADD(emp.date_start, INTERVAL 5 MONTH) < '$current_date')
                    )
                    ";

            $joinArr = array(
                array("table" => "gcchris.tblprobicalendar AS calendar", "condition" => "calendar.emp_id = emp.id", "option" => "LEFT"),
                array("table" => $this->tblCompanies . " companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
                array("table" => $this->tblPosition . " positions", "condition" => "positions.id = emp.position", "option" => "LEFT")
            );
        
            $this->db->select($select);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
        
            $this->db->where($where);
        
            // SEARCH
            if (!empty($search)) {
                $search = preg_replace('/\s+/', ' ', trim($search)); // Normalize spaces!
                $search = preg_replace('/[^a-zA-Z0-9\s.-]/', '', $search); // Remove special characters except for dot & dashses
        
                $searchTerms = [$search];

                // Date filtering
                $dateInput = date_create_from_format('M j', $search) ?: date_create_from_format('F j', $search);
                if ($dateInput) {
                    $currentYear = date('Y');
                    $dateInput->setDate($currentYear, (int)$dateInput->format('m'), (int)$dateInput->format('d'));

                    // Different versions of the date format searched
                    $searchTerms[] = $dateInput->format('Y-m-d');        // 2025-01-07
                    $searchTerms[] = $dateInput->format('M j');          // Jan 7
                    $searchTerms[] = $dateInput->format('F j');          // January 7
                    $searchTerms[] = $dateInput->format('M j Y');        // Jan 7 2025
                    $searchTerms[] = $dateInput->format('F j Y');        // January 7 2025
                }

                $this->db->group_start();
                foreach ($filterFields as $field) {
                    foreach ($searchTerms as $term) {
                        $this->db->or_like($field, $term, "both");
                    }
                }
                $this->db->group_end();
            }

            $query = $this->db->get($this->tblEmployees . " emp");
            return $query->num_rows();
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
