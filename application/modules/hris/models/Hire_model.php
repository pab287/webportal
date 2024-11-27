<?php defined('BASEPATH') || exit('No direct script access allowed');

    class Hire_model extends CI_Model {
        protected $applicationTable = "gcchris.tbapplication";
        protected $companyTable = "gcchris.tblcompanies";
        protected $departmentTable = "gcchris.tbldepartments";
        protected $positionTable = "gcchris.tblposition";
        protected $archivedTable = "gccmaster.archived_items";
        protected $employeeTable = "gccmaster.tblemployees";
        protected $workxpsTable = "gcchris.tblworkxps";
        private $user;

        function __construct() {
            parent::__construct();
            $this->load->model("hris/employee_model", "adm_employee");
            $this->load->model("hris/personnel_model", "adm_personnel");
            $this->load->model("core/datatable_model", "dt_model");
            $this->user = $this->session->userdata()["logged_in"];
            $this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->loggedinData["username"];
        }

        function getHireDatatableRequest() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $filterColumns = array("company.description", "department.description", "position.name", "application.people_no", "application.requested_by", "application.need_dt", "application.current");
                $columns = array("company.description as temp_company", "department.description as temp_department", "position.name  as temp_position", "application.people_no", "application.requested_by", "application.need_dt", "application.current", "application.id", "application.status");
                $dir = "DESC";
                $order = "id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $filterColumns[$orderx[0]["column"]];
                }

                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTemp = $this->dt_model->dataTable();
                $dtTemp->setTable($this->applicationTable);
                $dtTemp->setTableAlias("application");

                $dtTemp->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->companyTable] = "company";
                $joinTable["table"][$this->departmentTable] = "department";
                $joinTable["table"][$this->positionTable] = "position";
                $joinTable["fields"][] = "company.id=application.company_id";
                $joinTable["fields"][] = "department.id=application.department_id";
                $joinTable["fields"][] = "position.id=application.position_id";
                $joinTable["field_loc"][] = "LEFT";
                $joinTable["field_loc"][] = "LEFT";
                $joinTable["field_loc"][] = "LEFT";

                $dtTemp->setJoinTable($joinTable);
                $dtTemp->setWhereInField("application.id");

                $parameters = array();
                $parameters["application.is_archived"] = 0;
                $parameters["application.status"] = "Ongoing";

                $dtTemp->setWhereParameters($parameters);

                $totalData = $dtTemp->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTemp->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTemp->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTemp->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $overdue = "0 Days";
                        $ccDate = date("Y-m-d");
                        $neededDate = date("Y-m-d", strtotime($pst->need_dt));

                        $datetime1 = date_create($ccDate);
                        $datetime2 = date_create($neededDate);
                        $interval = date_diff($datetime1, $datetime2);

                        if ($neededDate < $ccDate) {
                            $overdue = $interval->format('%a days');
                        }

                        if ($pst->requested_by) {
                            $tempRequest = $this->core_layout->getEmployeeData($pst->requested_by);
                            $tempRequest = (object)$tempRequest;
                            $pst->requested_by = (isset($tempRequest->display_name_0) && $tempRequest->display_name_0) ? $tempRequest->display_name_1 : "No Assigned Name";
                        } else {
                            $pst->requested_by = "No Assigned Name";
                        }

                        $position = "";
                        $position .= "<div class='custom_content'>";
                        $position .= "<p class='custom-first_child'>{$pst->temp_position}</p>";
                        $position .= "<p><small>{$pst->temp_department}</small></p>";
                        $position .= "<p><small><strong>{$pst->temp_company}</strong></small></p>";
                        $position .= "</div>";

                        $neededCount = intval($pst->people_no);
                        $currentCount = intval($pst->current);
                        $totalCount = $neededCount - $currentCount;

                        $nestedData = array();
                        $nestedData['id'] = $pst->id;
                        $nestedData['position'] = $position;
                        $nestedData['requested_by'] = $pst->requested_by;
                        $nestedData['needed'] = $totalCount;
                        $nestedData['needed_date'] = date("Y-m-d", strtotime($pst->need_dt));
                        $nestedData['overdue'] = $overdue;
                        
                        $nestedData['status'] = $pst->status;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
        }

        function getCurrentHiring($id = null) {
            if ($id) {
                $arrData = $this->adm_personnel->getCurrentPersonnelRequest($id);
                if (isset($arrData["data"]) && $arrData["data"] && count((array)$arrData["data"]) > 0) {
                    return $arrData["data"];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getHiringPositionsForSelect() {
            $q = isset($_GET['q']) ? $_GET['q'] : "";
            $this->db->select("application.id, CONCAT('<div class=\"m--font-boldest mb-2 mt-2\">', position.name, '</div>
                                       <div class=\"text-muted m--regular-font-size-sm1\">', department.code ,'</div>
                                       <div class=\"m--regular-font-size-sm1 m--font-boldest mb-2\">', company.code ,'</div>') text,
                               application.company_id, application.department_id, application.position_id, application.people_no needed");
            $this->db->like("CONCAT(company.code, department.code, position.name)", $q, "BOTH");
            $this->db->where("application.status", "Ongoing");
            $this->db->where("application.is_archived", 0);
            $this->db->join($this->companyTable . " company", "company.id = application.company_id", "INNER");
            $this->db->join($this->departmentTable . " department", "department.id = application.department_id", "INNER");
            $this->db->join($this->positionTable . " position", "position.id = application.position_id", "INNER");
            $query = $this->db->get($this->applicationTable . " application");
            return array("results" => $query->result());
        }

        function rehireEmployee($employee_id, $post) {
            $session = $this->core_layout->getCurrentSession();
            $employee = $this->db->get_where($this->employeeTable, array("id" => $employee_id))->row();

            $date_rehired = $post["date_rehired"];
            $company_id = $post["company_id"];
            $department_id = $post["department_id"];
            $position = $post["position"];
            $needed = (int)$post["needed"];
            $application_id = $post["application_id"];
            $this->db->trans_begin();
            $this->db->reset_query();

            $employeeData = array(
                "idno" => "",
                "biometricno" => "",
                "company_id" => $company_id,
                "department_id" => $department_id,
                "position" => $position,
                "date_start" => date("Y-m-d", strtotime($date_rehired)),
                "date_regular" => "0000-00-00",
                "date_end" => "0000-00-00",
                "date_end_prob" => "0000-00-00",
                "employee_status" => "Active",
                "work_status" => "PROBATIONARY"
            );
            $this->db->where("id", $employee_id);
            $updateEmployee = $this->db->update($this->employeeTable, $employeeData);

            $this->db->reset_query();

            if ($updateEmployee) {
                $application_details = $this->db->get_where($this->applicationTable, array("id"=>$application_id))->row();
                $current = $application_details->current;
                $total_current = $current + 1;

                $appData = array(
                    "people_no" => $needed
                );

                if ($total_current == $application_details->people_no) {
                    $appData["status"] = "Completed";
                    $appData["completed_by"] = $session["emp_id"];
                    $appData["completed_dt"] = date("Y-m-d H:i:s");
                }else{
                    $appData['current'] = $total_current;
                }

                $this->db->where("id", $application_id);
                $this->db->update($this->applicationTable, $appData);
                $this->db->reset_query();

                $work_experience = array(
                    "emp_id" => $employee_id,
                    "work_from" => date("Y", strtotime($employee->date_start)),
                    "work_to" => date("Y", strtotime($employee->date_end)),
                    "work_company" => $employee->company_id,
                    "work_position" => $employee->position,
                    "work_status" => $employee->work_status,
                    "work_reason" => $employee->resign_reason,
                    "add_date" => date("Y-m-d H:i:s"),
                    "add_by" => $this->user["emp_id"],
                    "old_idno" => $employee->idno
                );
                $this->db->insert($this->workxpsTable, $work_experience);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array("success" => false);
            } else {
                $this->db->trans_commit();
                return array(
                    "success" => true,
                    "company_id" => $company_id,
                    "department_id" => $department_id,
                    "position_id" => $position,
                    "company" => $this->getTableData($company_id, $this->companyTable, "code"),
                    "department" => $this->getTableData($department_id, $this->departmentTable, "code"),
                    "position" => $this->getTableData($position, $this->positionTable, "name"),
                );
            }
        }

        private function getTableData($id, $table, $field) {
            $this->db->where("id", $id);
            return $this->db->get($table)->row($field);
        }

        public function searchEmployeeHire($firstname, $middlename, $lastname) {
            $select = "emp.id,
                       UCASE(
                           CONCAT(
                               emp.firstname, ' ', 
                               CASE 
                                   WHEN emp.middlename IS NOT NULL AND emp.middlename != '' THEN CONCAT(' ', substr(emp.middlename,1,1),'.')
                                   ELSE ''
                               END,
                               ' ', emp.lastname, 
                               CASE 
                                   WHEN emp.suffix IS NOT NULL AND emp.suffix != '' AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                                   ELSE ''
                               END
                           )
                       ) employee_name,
                       IF(companies.id IS NULL, emp.company_id, companies.code) company,
                       IF(departments.id IS NULL, emp.department_id, departments.code) department,
                       IF(positions.id IS NULL, emp.`position`, positions.name) `position`,
                       emp.pic_filename image";

            $where_array = array(
                "firstname"=>$firstname,
                "middlename"=>$middlename,
                "lastname"=>$lastname
            );        
            $this->db->select($select);
            $this->db->join($this->companyTable . ' companies', 'companies.id = emp.company_id', 'LEFT');
            $this->db->join($this->departmentTable . ' departments', 'departments.id = emp.department_id', 'LEFT');
            $this->db->join($this->positionTable . ' positions', 'positions.id = emp.`position`', 'LEFT');
            $this->db->where($where_array);
            $query = $this->db->get($this->employeeTable . ' emp');
            $employees = $query->result();

            $data = array();
            foreach ($employees as $employee) {
                $tempEmployeeData = $this->core_layout->getEmployeeData($employee->id);
                $tempEmployeeData = (object) $tempEmployeeData;
                $tempEmployeeName = (isset($tempEmployeeData->display_name_1) && $tempEmployeeData->display_name_1)? $tempEmployeeData->display_name_1: null;
                if($tempEmployeeName !== null && $tempEmployeeName !== $employee->employee_name){ $employee->employee_name = $tempEmployeeName; }
                $image_path = "uploads/files/images/employee_files/empcode_" . $employee->id . "/" . $employee->image;
                if (file_exists(realpath($image_path))) {
                    $employee->image = base_url($image_path);
                } else {
                    $employee->image = base_url("assets/images/profile/no_image.jpg");
                }

                array_push($data, $employee);
            }
            return $data;
        }

        public function getPersonnelRequestData(){
            $post = $this->input->post();

            $this->db->select("comp.description as company, pos.name as position, dept.description as department, pr.id");
            $this->db->from("gcchris.tbapplication pr");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = pr.company_id", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = pr.position_id", "LEFT");
            $this->db->join("gcchris.tbldepartments dept", "dept.id = pr.department_id", "LEFT");
            $this->db->where("pr.id", $post['id']);

            $query = $this->db->get();
            return $query->result();
        }

        public function saveData(){
            $post = $this->input->post();
            $personnel_request_id = $post['personnel_request_id'];
            unset($post['personnel_request_id']);
            $post['status'] = "For Approval";
            $insert = $this->db->insert("gccmaster.tblemployees", $post);
            return $post;
        }

        public function getApplicants(){
            $result = array();
            $arrData = array();
            $get = $this->input->get();

            $select = "id as id, CONCAT(firstname, ' ', lastname) as text, employee_id";
            $this->db->select($select);
            $this->db->from('dbhrd.document_body');

            if(isset($get['term'])){
                $filterFields1 = array("firstname", "lastname");
                foreach ($filterFields1 as $key => $field1) {
                    if ($key == 0) {
                        $this->db->like($field1, $get['term'], "both");
                    } else {
                        $this->db->or_like($field1, $get['term'], "both");
                    }   
                }
            }

            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){

                    if($rs->employee_id == 0){
                        $arrData[$key] = $rs;
                    }
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            return $result;
        }

        public function get_selected(){
            $result = array();
            $get = $this->input->get();

            $this->db->select('a.*, a.id as body_id, a.firstname as fname, a.lastname as lname, a.middlename as mname, a.suffix as suff, a.contact_no as contact, b.*, b.id as info_id');
            $this->db->from('dbhrd.document_body as a');
            $this->db->join('dbhrd.applicant_informations as b', 'a.id = b.document_body_id', 'left');
            $this->db->where('a.id', $get['id']);
            $query = $this->db->get();

            $result['data'] = array_map('ucwords', $query->result_array()[0]);
            // $result['data'] = $query->result_array()[0];
            $result['count'] = $query->num_rows();
            return $result;
        }
    }