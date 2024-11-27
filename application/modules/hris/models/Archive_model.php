<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Archive_model extends CI_Model
    {
        protected $tblcompanies = "gcchris.tblcompanies";
        protected $tbldepartments = "gcchris.tbldepartments";
        protected $tblposition = "gcchris.tblposition";
        protected $archived_items = "gccmaster.archived_items";
        protected $tblemployees = "gccmaster.tblemployees";
        protected $tbapplication = "gcchris.tbapplication";
        protected $tbl_loans = "gcchris.loans";
        protected $tbl_ps_loans = "payroll.loans";
        protected $tbl_ps_loan_payment = "payroll.payroll_sheet_loan_payments";
        protected $tbl_ps = "payroll.payroll_sheet";

        function __construct()
        {
            parent::__construct();
            $this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->loggedinData["username"];
        }

        public function getArchivedEmployees()
        {
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
            $resultSet = array();

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
                      DATE_ADD(emp.date_start, INTERVAL 3 MONTH) evaluation_date,
                      emp.idno,
                      IF(company.id IS NULL, emp.company_id, company.code) _company,
                      IF(position.id IS NULL, emp.position, position.name) _position,
                      IF(department.id IS NULL, emp.department_id, department.code) _department,
                      emp.archive_remarks,
                      emp.id,
                      emp.pic_filename";

            $joinArr = array(
                array("table" => $this->tblcompanies . " company", "condition" => "company.id = emp.company_id", "option" => "LEFT"),
                array("table" => $this->tbldepartments . " department", "condition" => "department.id = emp.department_id", "option" => "LEFT"),
                array("table" => $this->tblposition . " position", "condition" => "position.id = emp.position", "option" => "LEFT"),
            );

            $where = array(
                "emp.is_archived" => 1
            );

            $searchFields = "CONCAT(emp.firstname, emp.middlename, emp.lastname, IF(company.id IS NULL, emp.company_id, company.code), 
                             IF(position.id IS NULL, emp.position, position.name), 
                             IF(department.id IS NULL, emp.department_id, department.code))";

            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $this->db->like($search['field'], $search['key'], $search['option']);

            $this->db->select($select);
            $this->db->where($where);
            $query = $this->db->get($this->tblemployees . " emp");

            $employees = $query->result();

            foreach ($employees as $employee) {
                $image_path = "uploads/files/images/employee_files/" . $employee->pic_filename;
                if (file_exists(realpath($image_path))) {
                    $employee->image = base_url($image_path);
                } else {
                    $employee->image = base_url("assets/images/profile/no_image.jpg");
                }
            }

            $resultSet['data'] = $employees;
            $resultSet['recordsTotal'] = $this->utilities->getTableCount($this->tblemployees . " emp", $where, $search, $joinArr);
            $resultSet['recordsFiltered'] = $this->utilities->getTableCount($this->tblemployees . " emp", $where, $search, $joinArr);

            return $resultSet;
        }

        public function archiveEmployee($emp_id)
        {
            $resultSet = array();
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $post->is_archived = 1;
            $user = $this->core_layout->getUserLoggedIn();

            $this->db->where("id", $emp_id);
            if ($this->db->update($this->tblemployees, $post)) {

                $this->db->insert("gccmaster.archived_items",
                    array(
                        "archived_table" => "gccmaster.tblemployees",
                        "archived_id" => $emp_id,
                        "archived_by" => $user["employee_id"],
                        "archived_at" => date('Y-m-d H:i:s'),
                        "status" => 1
                    )
                );

                $resultSet["success"] = true;
                $resultSet["message"] = "Employee successfully moved to archived.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " inserted new employee archived record with db id no. ".$this->db->insert_id(),"insert", "success", "gcchris", "user");
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed inserting new employee archived record","insert", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        public function restoreEmployee($emp_id)
        {
            $resultSet = array();
            $user = $this->core_layout->getUserLoggedIn();

            $this->db->set("is_archived", 0);
            $this->db->where("id", $emp_id);
            if ($this->db->update($this->tblemployees)) {
                $this->db->insert("gccmaster.archived_items",
                    array(
                        "archived_table" => "gccmaster.tblemployees",
                        "archived_id" => $emp_id,
                        "archived_by" => $user["employee_id"],
                        "archived_at" => date('Y-m-d H:i:s'),
                        "status" => 2
                    )
                );

                $resultSet["success"] = true;
                $resultSet["message"] = "Employee successfully restored.";
                $resultSet["title"] = "Employee restored.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " restored employee archived record with db id no. ".$emp_id,"update", "success", "gcchris", "user");
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
                $resultSet["title"] = "Error.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed to restor employee archived record with db id no. ".$emp_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        public function getArchivedPersonnelRequest()
        {
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
            $resultSet = array();

            $select = "position.name _position, company.code _company, department.code _department, app.*";

            $joinArr = array(
                array("table" => $this->tblcompanies . " company", "condition" => "company.id = app.company_id", "option" => "INNER"),
                array("table" => $this->tbldepartments . " department", "condition" => "department.id = app.department_id", "option" => "INNER"),
                array("table" => $this->tblposition . " position", "condition" => "position.id = app.position_id", "option" => "INNER"),
            );

            $where = array(
                "app.is_archived" => 1
            );

            $searchFields = "CONCAT(position.name, company.code, department.code, app.status, app.requested_dt, app.need_dt, app.archive_remarks)";

            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $this->db->like($search['field'], $search['key'], $search['option']);

            $this->db->select($select);
            $this->db->where($where);
            $query = $this->db->get($this->tbapplication . " app");

            $data = $query->result();

            $resultSet['data'] = $data;
            $resultSet['recordsTotal'] = $this->utilities->getTableCount($this->tbapplication . " app", $where, $search, $joinArr);
            $resultSet['recordsFiltered'] = $this->utilities->getTableCount($this->tbapplication . " app", $where, $search, $joinArr);

            return $resultSet;
        }

        public function getArchivedLoans()
        {
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
            $resultSet = array();

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
             ) as employee_name,
             UCASE(
                CONCAT(
                    emp_created.firstname, ' ', 
                    CASE 
                        WHEN emp_created.middlename IS NOT NULL AND emp_created.suffix != '' AND emp_created.suffix != 'N/A' AND emp_created.suffix != 'NONE' THEN CONCAT(' ', emp_created.middlename)
                        ELSE ''
                    END,
                    ' ', emp_created.lastname, 
                    CASE 
                        WHEN emp_created.suffix IS NOT NULL AND emp_created.suffix != '' AND emp_created.suffix != 'N/A' AND emp_created.suffix != 'NONE' THEN CONCAT(' ', emp_created.suffix)
                        ELSE ''
                    END
                )
             ) as created_by_name,
             IFNULL(UCASE(
                CONCAT(
                    emp_archived.firstname, ' ', 
                    CASE 
                        WHEN emp_archived.middlename IS NOT NULL AND emp_archived.suffix != '' AND emp_archived.suffix != 'N/A' AND emp_archived.suffix != 'NONE' THEN CONCAT(' ', emp_archived.middlename)
                        ELSE ''
                    END,
                    ' ', emp_archived.lastname, 
                    CASE 
                        WHEN emp_archived.suffix IS NOT NULL AND emp_archived.suffix != '' AND emp_archived.suffix != 'N/A' AND emp_archived.suffix != 'NONE' THEN CONCAT(' ', emp_archived.suffix)
                        ELSE ''
                    END
                )
                ), '---') as archived_by_name,
             loans.*, master_loans.loan_name, 
             ROUND(SUM(IFNULL(psloanpayments.amount_due, 0)),2) as total_amount_paid, 
             GROUP_CONCAT(psloanpayments.amount_due, '||', ps.id) as temp_amount_paid";

            $joinArr = array(
                array("table" => $this->tbl_ps_loans . " master_loans", "condition" => "master_loans.id = loans.loan_id", "option" => "INNER"),
                array("table" => $this->tbl_ps_loan_payment . " psloanpayments", "condition" => "psloanpayments.loan_id = loans.id", "option" => "LEFT"),
                array("table" => $this->tbl_ps . " ps", "condition" => "ps.id = psloanpayments.payroll_sheet_id AND ps.posted = 1", "option" => "LEFT"),
                array("table" => $this->tblemployees . " emp", "condition" => "emp.id = loans.emp_id", "option" => "INNER"),
                array("table" => $this->tblemployees . " emp_created", "condition" => "emp_created.id = loans.created_by", "option" => "INNER"),
                array("table" => $this->tblemployees . " emp_archived", "condition" => "emp_archived.id = loans.archived_by", "option" => "LEFT"),
            );

            $where = array(
                "loans.is_archived" => 1
            );

            $searchFields = "CONCAT(master_loans.loan_name, CONCAT(emp.firstname, ' ', emp.lastname), CONCAT(emp_archived.firstname, ' ', emp_archived.lastname), CONCAT(emp_created.firstname, ' ', emp_created.lastname))";

            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
            $this->db->group_by("loans.id, loans.loan_id");
            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $this->db->like($search['field'], $search['key'], $search['option']);

            $this->db->select($select);
            $this->db->where($where);
            $query = $this->db->get($this->tbl_loans . " loans");
            $data = array();
            if($query->num_rows() > 0){ 
                foreach($query->result() as $key => $value){
                    $tempTotal = 0;
                    $tempAmount = $value->temp_amount_paid;
                    $tempAmount = explode(",", $tempAmount);
                    foreach ($tempAmount as $kk => $vv) {
                        $tempDD = explode("||", $vv);
                        $tempTotal += floatval($tempDD[0]);
                    }
                    $tempTotal = round($tempTotal, 2);
                    if($tempTotal !== floatval($value->total_amount_paid)){ $value->total_amount_paid = $tempTotal; }
                    $data[$key] = $value;
                }
            }

            $resultSet['data'] = $data;
            $resultSet['recordsTotal'] = $this->utilities->getTableCount($this->tbl_loans . " loans", $where, $search, $joinArr, false, null, "loans.id, loans.loan_id");
            $resultSet['recordsFiltered'] = $this->utilities->getTableCount($this->tbl_loans . " loans", $where, $search, $joinArr,  false, null, "loans.id, loans.loan_id");

            return $resultSet;
        }

        public function restorePersonnelRequest($personnel_request_id) {
            $resultSet = array();
            $user = $this->core_layout->getUserLoggedIn();

            $this->db->set("is_archived", 0);
            $this->db->where("id", $personnel_request_id);
            if ($this->db->update($this->tbapplication)) {
                $this->db->insert("gccmaster.archived_items",
                    array(
                        "archived_table" => "gccmaster.tblemployees",
                        "archived_id" => $personnel_request_id,
                        "archived_by" => $user["employee_id"],
                        "archived_at" => date('Y-m-d H:i:s'),
                        "status" => 2
                    )
                );

                $resultSet["success"] = true;
                $resultSet["message"] = "Personnel request successfully restored.";
                $resultSet["title"] = "Personnel Request Restored.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " restored personnel request record with db id no. ".$personnel_request_id,"insert", "success", "gcchris", "user");
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
                $resultSet["title"] = "Error.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed restoring personnel request record.","insert", "error", "gcchris", "user");
            }

            return $resultSet;
        }
    }