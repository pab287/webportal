<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Personnel_model extends CI_Model
    {
        protected $applicationTable = "gcchris.tbapplication";
        protected $applicationMetaTable = "gcchris.tbapplication_meta";
        protected $companyTable = "gcchris.tblcompanies";
        protected $departmentTable = "gcchris.tbldepartments";
        protected $positionTable = "gcchris.tblposition";
        protected $salaryTable = "gcchris.tblsalary";
        protected $archivedTable = "gccmaster.archived_items";
        protected $employeeTable = "gccmaster.tblemployees";

        function __construct()
        {
            parent::__construct();
            $this->load->model("hris/employee_model", "adm_employee");
            $this->load->model("core/datatable_model", "dt_model");
            $this->load->model("ams/Utilities_model", "utilities");
            $this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->loggedinData["username"];
        }

        function getPersonnelRequestDatatableRequest(){
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $post = $this->input->post();

            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
            $clearTable = (isset($post["clear_table"]) && $post["clear_table"] == "true") ? true : false;
            
            $rowData = $this->getPersonnelRequest($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->getPersonnelRequestCount($search);

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

        function getPersonnelRequest($search=null, $limit, $offset, $sortBy, $sortOrder){
            $filterFields = array("pos.name", "app.status");
            $where = "app.is_archived = '0' AND (app.status = 'forApproval' OR app.status = 'Ongoing')";
            $this->db->select("app.id, pos.name as position, app.type as type, app.people_no as needed, app.requested_dt as requested_date, app.need_dt as needed_date, app.status, app.is_archived");
            $this->db->from('gcchris.tbapplication app');
            $this->db->join('gcchris.tbldepartments dept', 'app.department_id = dept.id', "LEFT");
            $this->db->join('gcchris.tblcompanies comp', 'app.company_id = comp.id', "LEFT");
            $this->db->join('gcchris.tblposition pos', 'app.position_id = pos.id', "LEFT");
            
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
            $this->db->where($where);
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $data = array();
                foreach ($query->result() as $key => $item) {
                    $overdue = "0 Days";
                    $ccDate = date("Y-m-d");
                    $neededDate = date("Y-m-d", strtotime($item->needed_date));
                    $datetime1 = date_create($ccDate);
                    $datetime2 = date_create($neededDate);
                    $interval = date_diff($datetime1, $datetime2);
                    if ($item->status == "Ongoing" && ($neededDate < $ccDate)) {
                        $overdue = $interval->format('%a days');
                    }
                    if($item->status == "forApproval"){
                        $status = "For Approval";
                        $overdue = $interval->format('%a days');
                    }else{
                        $status = $item->status;
                    }
                    $item->status = $status;
                    $item->requested_date = date("Y-m-d", strtotime($item->requested_date));
                    $item->needed_date = date("Y-m-d", strtotime($item->needed_date));
                    $item->overdue = $overdue;

                    $data[$key] = $item;
                }
                $resultset = array();
                $resultset["data"] = $data;
                return $resultset;
            } else {
                return array();
            }
        }

        function getPersonnelRequestCount($search=null){
            $filterFields = array("pos.name", "app.status");
            $where = "app.is_archived = '0' AND (app.status = 'forApproval' OR app.status = 'Ongoing')";
            $this->db->select("app.id, pos.name as position, app.type as type, app.people_no as needed, app.requested_dt as requested_date, app.need_dt as needed_date, app.status, app.is_archived as is_archived");
            $this->db->from('gcchris.tbapplication app');
            $this->db->join('gcchris.tbldepartments dept', 'app.department_id = dept.id', "LEFT");
            $this->db->join('gcchris.tblcompanies comp', 'app.company_id = comp.id', "LEFT");
            $this->db->join('gcchris.tblposition pos', 'app.position_id = pos.id', "LEFT");
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
            $this->db->where($where);
            $query = $this->db->get();
            return $query->num_rows();
        }

        function getPersonnelRequestDatatableRequestCompleted(){
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $post = $this->input->post();

            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
            $clearTable = (isset($post["clear_table"]) && $post["clear_table"] == "true") ? true : false;
            
            $rowData = $this->getPersonnelRequestCompleted($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->getPersonnelRequestCompletedCount($search);

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

        function getPersonnelRequestCompleted($search=null, $limit, $offset, $sortBy, $sortOrder){
            $filterFields = array("pos.name", "app.status");
            $this->db->select("app.id, pos.name as position, app.type as type, app.people_no as needed, app.requested_dt as requested_date, app.need_dt as needed_date, app.status");
            $this->db->from('gcchris.tbapplication app');
            $this->db->join('gcchris.tbldepartments dept', 'app.department_id = dept.id', "LEFT");
            $this->db->join('gcchris.tblcompanies comp', 'app.company_id = comp.id', "LEFT");
            $this->db->join('gcchris.tblposition pos', 'app.position_id = pos.id', "LEFT");
            $this->db->where("app.is_archived", 0);
            $this->db->where("app.status", 'Completed');
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
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $data = array();
                foreach ($query->result() as $key => $item) {
                    $overdue = "0 Days";
                    $ccDate = date("Y-m-d");
                    $neededDate = date("Y-m-d", strtotime($item->needed_date));
                    $datetime1 = date_create($ccDate);
                    $datetime2 = date_create($neededDate);
                    $interval = date_diff($datetime1, $datetime2);
                    if ($item->status == "Ongoing" && ($neededDate < $ccDate)) {
                        $overdue = $interval->format('%a days');
                    }
                    if($item->status == "forApproval"){
                        $status = "For Approval";
                    }else{
                        $status = $item->status;
                    }

                    if($item->needed > 0){
                        $overdue = $interval->format('%a days');
                    }

                    $item->overdue = $overdue;
                    $item->status = $status;
                    $item->requested_date = date("Y-m-d", strtotime($item->requested_date));
                    $item->needed_date = date("Y-m-d", strtotime($item->needed_date));
                    $data[$key] = $item;
                }
                $resultset = array();
                $resultset["data"] = $data;
                return $resultset;
            } else {
                return array();
            }
        }

        function getPersonnelRequestCompletedCount($search=null){
            $filterFields = array("pos.name", "app.status");
            $this->db->select("app.id, pos.name as position, app.type as type, app.people_no as needed, app.requested_dt as requested_date, app.need_dt as needed_date, app.status");
            $this->db->from('gcchris.tbapplication app');
            $this->db->join('gcchris.tbldepartments dept', 'app.department_id = dept.id', "LEFT");
            $this->db->join('gcchris.tblcompanies comp', 'app.company_id = comp.id', "LEFT");
            $this->db->join('gcchris.tblposition pos', 'app.position_id = pos.id', "LEFT");
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
            $this->db->where("app.is_archived", 0);
            $this->db->where("app.status", 'Completed');
            $query = $this->db->get();
            return $query->num_rows();
        }

        function getPersonnelRequestDatatableRequest_($key)
        {
            $post = $this->input->post();
            
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $filterColumns = array("company.description", "department.description", "position.name", "application.type", "application.people_no", "application.requested_dt", "application.need_dt", "application.status", "application.current");
                $columns = array(
                    "company.description as temp_company",
                    "department.description as temp_department",
                    "position.name  as temp_position",
                    "application.type",
                    "application.people_no",
                    "application.requested_dt",
                    "application.need_dt",
                    "application.status",
                    "application.current",
                    "application.id");
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
                
                switch (strtolower($searchValue)) {
                    case 'for approval':
                        $searchValue = 'forApproval';
                        break;
                    case 'on going':
                        $searchValue = 'Ongoing';
                        break;
                    case 'on hold':
                        $searchValue = 'onHold';
                        break;
                    default:
                        $searchValue = $searchValue;
                    break;
                }

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
                $or_parameters = array();
                $parameters["application.is_archived"] = 0;
                $parameters["application.status"] = "Ongoing";
                $or_parameters["application.status"] = "forApproval";
                if (!empty($key)) {
                    switch ($key) {
                        case "overdue-seven-days":
                            $parameters["application.status"] = "Ongoing";
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) >="] = 7;
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) <"] = 30;
                            break;
                        case "overdue-thirty-days":
                            $parameters["application.status"] = "Ongoing";
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) >="] = 30;
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) <"] = 60;
                            break;
                        case "overdue-sixty-days":
                            $parameters["application.status"] = "Ongoing";
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) >="] = 60;
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) <"] = 90;
                            break;
                        case "overdue-ninety-days":
                            $parameters["application.status"] = "Ongoing";
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) >"] = 90;
                            break;
                        case "still-needed":
                            $parameters["application.status"] = "Ongoing";
                            break;
                        case "overdue":
                            $parameters["application.status"] = "Ongoing";
                            $parameters["DATE(application.need_dt)<"] = date('Y-m-d');
                            break;
                        default:
                            $parameters["application.status"] = "Ongoing";
                            $or_parameters["application.status"] = "forApproval";
                            break;
                    }
                }

                $dtTemp->setWhereParameters($parameters);
                $dtTemp->setOrWhereParameters($or_parameters);
                
                $totalData = $dtTemp->dtAllPostsCount();
                $totalFiltered = $totalData;
                
                // var_dump($searchValue);
                if (!$searchValue) {
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

                        if ($pst->status == "Ongoing" && ($neededDate < $ccDate)) {
                            $overdue = $interval->format('%a days');
                        }

                        $tempStatus = $pst->status;

                        switch ($pst->status) {
                            case "forApproval":
                                $tempStatus = "For Approval";
                                break;
                            case "onHold":
                                $tempStatus = "On Hold";
                                break;
                            case "Ongoing":
                                $tempStatus = "On Going";
                                break;
                            default:
                            break;
                        }

                        $temp_position = strtoupper($pst->temp_position);
                        $temp_department = strtoupper($pst->temp_department);
                        $temp_company = strtoupper($pst->temp_company);

                        $position = "";
                        $position .= "<div class='custom_content'>";
                        $position .= "<p class='custom-first_child'>{$temp_position}</p>";
                        $position .= "<p><small>{$temp_department}</small></p>";
                        $position .= "<p><small><strong>{$temp_company}</strong></small></p>";
                        $position .= "</div>";

                        if ($pst->status == "Completed" && $pst->current !== "0") {
                            $pst->people_no = $pst->current;
                        }

                        $current = intval($pst->current);
                        $needed = intval($pst->people_no);
                        $total = $needed - $current;
                        $nestedData = array();
                        $nestedData['id'] = $pst->id;
                        $nestedData['position'] = $position;
                        $nestedData['type'] = $pst->type;
                        $nestedData['needed'] = $total;
                        $nestedData['requested_date'] = date("Y-m-d", strtotime($pst->requested_dt));
                        $nestedData['needed_date'] = date("Y-m-d", strtotime($pst->need_dt));
                        $nestedData['overdue'] = $overdue;
                        $nestedData['status'] = $tempStatus;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                    "key" => $key,
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                    "key" => $key
                );
            }
        }

        function getPersonnelRequestDatatableRequestCompleted_($key)
        {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $filterColumns = array("company.description", "department.description", "position.name", "application.type", "application.people_no", "application.requested_dt", "application.need_dt", "application.status", "application.current");
                $columns = array(
                    "company.description as temp_company",
                    "department.description as temp_department",
                    "position.name  as temp_position",
                    "application.type",
                    "application.people_no",
                    "application.requested_dt",
                    "application.need_dt",
                    "application.status",
                    "application.current",
                    "application.id");
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

                switch (strtolower($searchValue)) {
                    case 'for approval':
                        $searchValue = 'forApproval';
                        break;
                    case 'on going':
                        $searchValue = 'Ongoing';
                        break;
                    case 'on hold':
                        $searchValue = 'onHold';
                        break;
                        default:
                        break;
                }

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
                $or_parameters = array();
                $parameters["application.is_archived"] = 0;
                $parameters["application.status"] = "Completed";


                if (!empty($key)) {
                    switch ($key) {
                        case "overdue-seven-days":
                            $parameters["application.status"] = "Ongoing";
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) >="] = 7;
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) <"] = 30;
                            break;
                        case "overdue-thirty-days":
                            $parameters["application.status"] = "Ongoing";
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) >="] = 30;
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) <"] = 60;
                            break;
                        case "overdue-sixty-days":
                            $parameters["application.status"] = "Ongoing";
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) >="] = 60;
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) <"] = 90;
                            break;
                        case "overdue-ninety-days":
                            $parameters["application.status"] = "Ongoing";
                            $parameters["DATEDIFF(CURDATE(), application.need_dt) >"] = 90;
                            break;
                        case "still-needed":
                            $parameters["application.status"] = "Ongoing";
                            break;
                        case "overdue":
                            $parameters["application.status"] = "Ongoing";
                            $parameters["DATE(application.need_dt)<"] = date('Y-m-d');
                            break;
                        default:
                            $parameters["application.status"] = "Ongoing";
                            $or_parameters["application.status"] = "forApproval";
                            break;
                    }
                }

                $dtTemp->setWhereParameters($parameters);
                $dtTemp->setOrWhereParameters($or_parameters);

                $totalData = $dtTemp->dtAllPostsCount();
                $totalFiltered = $totalData;

                if ($searchValue) {
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

                        if ($pst->status == "Ongoing" && ($neededDate < $ccDate)) {
                            $overdue = $interval->format('%a days');
                        }

                        $tempStatus = $pst->status;

                        switch ($pst->status) {
                            case "forApproval":
                                $tempStatus = "For Approval";
                                break;
                            case "onHold":
                                $tempStatus = "On Hold";
                                break;
                            case "Ongoing":
                                $tempStatus = "On Going";
                                break;
                                default:
                                break;
                        }

                        $position = "";
                        $position .= "<div class='custom_content'>";
                        $position .= "<p class='custom-first_child'>{$pst->temp_position}</p>";
                        $position .= "<p><small>{$pst->temp_department}</small></p>";
                        $position .= "<p><small><strong>{$pst->temp_company}</strong></small></p>";
                        $position .= "</div>";

                        if ($pst->status == "Completed" && $pst->current !== "0") {
                            $pst->people_no = $pst->current;
                        }

                        $nestedData = array();
                        $nestedData['id'] = $pst->id;
                        $nestedData['position'] = $position;
                        $nestedData['type'] = $pst->type;
                        $nestedData['needed'] = $pst->people_no;
                        $nestedData['requested_date'] = date("Y-m-d", strtotime($pst->requested_dt));
                        $nestedData['needed_date'] = date("Y-m-d", strtotime($pst->need_dt));
                        $nestedData['overdue'] = $overdue;
                        $nestedData['status'] = $tempStatus;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                    "key" => $key,
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                    "key" => $key
                );
            }
        }

        function getPersonnelRequestModalContent()
        {
            $resultset = array();
            $html = "";
            $html = $this->load->view("hris/masterfile/personnel_request/modals/add_content", null, true);

            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function setModalPersonnelRequest()
        {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);

                $post["created_dt"] = date("Y-m-d H:i:s");
                $post["created_by"] = $session["emp_id"];
                $post["requested_dt"] = date("Y/m/d");
                $post["need_dt"] = date("Y/m/d", strtotime($post["need_dt"]));
                $post["status"] = "forApproval";

                $insert = $this->db->insert($this->applicationTable, $post);
                $position = $this->getPositionById($post['position_id']);
                $requestedBy = $this->getEmployeeNameById($post["requested_by"]);
                $company = $this->getCompanyById($post["company_id"]);
                if ($insert) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Personnel request data has been added.";
                    $this->core_layout->setEventLog("User added new personnel request: <strong>".$position->name."</strong> requested by <strong>".$requestedBy."</strong> for company: <strong>".$company->description."</strong>","insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed saving personnel request data!";
                    $this->core_layout->setEventLog("User failed inserting new personnel request data","insert", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Personnel request - Error, No post data found.","insert", "error", "gcchris", "system");
            }

            return $resultset;
        }

        function updatePersonnelRequest()
        {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                $id = $post["id"];
                unset($post["csrf_token"], $post["id"]);

                $post["modify_dt"] = date("Y-m-d H:i:s");
                $post["modify_by"] = $session["emp_id"];
                $post["need_dt"] = date("Y/m/d", strtotime($post["need_dt"]));
                $currentPersonnelData = $this->getPersonnelData($id);
                $update = $this->db->update($this->applicationTable, $post, array("id" => $id));
                if ($update) {

                    $arrDatax = array();
                    $arrDatax["id"] = $session["emp_id"];
                    $arrDatax["date"] = date("Y-m-d H:i:s");

                    $metaData["meta_id"] = $id;
                    $metaData["meta_field"] = "updated_log";
                    $metaData["meta_value"] = serialize($arrDatax);
                    if ($metaData) {
                        $this->db->insert($this->applicationMetaTable, $metaData);
                    }

                    $data = $this->getCurrentPersonnelRequest($id);
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Personnel request data has been updated.";
                    unset($post['modify_dt']); 
                    unset($post['modify_by']);
                    unset($post['qualification']);
                    unset($post['job_description']);
                    $changes = $this->logChanges($currentPersonnelData ,$post);
                    $this->core_layout->setEventLog("User updated personnel request: <strong>".$currentPersonnelData->position_name."</strong> ".$changes,"update", "success", "gcchris", "user");
                    $resultset["data"] = $data;
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update personnel request data!";
                    $this->core_layout->setEventLog("User failed updating personnel request: <strong>".$currentPersonnelData->position_name."</strong>","update", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Personnel request - Error, No post data found.","update", "error", "gcchris", "system");
            }

            return $resultset;
        }

        public function getPersonnelSelect2Data()
        {
            $resultset = array();
            $arrData = array();

            $get = $this->input->get();
            $this->db->select("id, lastname, firstname, middlename, suffix");
            $this->db->from($this->employeeTable);
            $this->db->where("employee_status", "Active");
            $this->db->group_start();
            $this->db->where("level", "SUPERVISORY");
            $this->db->or_where("level", "MANAGERIAL");
            $this->db->or_where("level", "Top Management");
            $this->db->group_end();
            if (isset($get["term"]) && $get["term"]) {
                $this->db->group_start();
                $this->db->like("lastname", $get["term"], "both");
                $this->db->or_like("firstname", $get["term"], "both");
                $this->db->or_like("middlename", $get["term"], "both");
                $this->db->or_like("suffix", $get["term"], "both");
                $this->db->group_end();
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;

                    $row = array();
                    $row["id"] = $rs->id;
                    $row["text"] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $arrData[] = $row;
                }
            }

            $resultset["results"] = $arrData;
            return $resultset;
        }

        public function getCurrentPersonnelRequest($id = null, $status = null)
        {
            $arrData = array();
            $leftPane = array();
            $rightPane = array();
            if ($id) {
                $this->db->select("a.*, IFNULL(a.type, '---') as type, b.job_desc as job_description, b.qualification, c.description as company, d.description as department, e.name as position, IFNULL(f.description, '---') as salary, d.require_clearance");
                $this->db->from($this->applicationTable . " as a");
                $this->db->join($this->positionTable . " as b", "b.id = a.position_id", "LEFT");
                $this->db->join($this->companyTable . " as c", "c.id = a.company_id", "LEFT");
                $this->db->join($this->departmentTable . " as d", "d.id = a.department_id", "LEFT");
                $this->db->join($this->positionTable . " as e", "e.id = a.position_id", "LEFT");
                $this->db->join($this->salaryTable . " as f", "f.id = a.salary_id", "LEFT");
                $this->db->where("a.id", $id);
                if (isset($status) && $status) {
                    $this->db->where("a.status", $status);
                }

                $query = $this->db->get();

                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $row->need_dt = date("Y-m-d", strtotime($row->need_dt));
                    $row->type = ($row->type) ? $row->type : "---";

                    $jobdesc = new DOMDocument;
                    @$jobdesc->loadHTML($row->job_description);
                    $count_jobdesc = $jobdesc->getElementsByTagName("li");
        
                    $count_d = $count_jobdesc->length;
                    if($count_d > 0){
                        $d_job_description = $row->job_description;
                    }else{
                        $d_job_description = nl2br($row->job_description);
                    }

                    $row->job_description = nl2br($d_job_description);
                    $row->qualification = nl2br($row->qualification);

                    if ($row->requested_by) {
                        $tempRequest = $this->core_layout->getEmployeeData($row->requested_by);
                        $tempRequest = (object)$tempRequest;
                        $row->requested_name = (isset($tempRequest->display_name_0) && $tempRequest->display_name_0) ? $tempRequest->display_name_1 : "---";
                    } else {
                        $row->requested_name = "---";
                    }

                    $tempStatus = (isset($row->status) && $row->status) ? $row->status : "Ongoing";

                    switch ($row->status) {
                        case "forApproval":
                            $tempStatus = "For Approval";
                            break;
                        case "onHold":
                            $tempStatus = "On Hold";
                            break;
                            default:
                            break;
                    }

                    $rightPane["status"] = $tempStatus;
                    $rightPane["current"] = $row->current;

                    $tempCurrent = intval($row->current);
                    $tempNeeded = intval($row->people_no);
                    $tempAvailableSlot = $tempNeeded - $tempCurrent;

                    if ($tempStatus == "Completed") {
                        $tempAvailableSlot = 0;
                        $row->people_no = $tempCurrent;
                    }

                    $rightPane["available_slot"] = $tempAvailableSlot;

                    if ($row->approved_by) {
                        $tempApproved = $this->core_layout->getEmployeeData($row->approved_by);
                        $tempApproved = (object)$tempApproved;
                        $rightPane["approved_by"] = (isset($tempApproved->display_name_0) && $tempApproved->display_name_0) ? $tempApproved->display_name_1 : "";
                    } else {
                        $rightPane["approved_by"] = "";
                    }

                    if ($row->created_by) {
                        $tempCreated = $this->core_layout->getEmployeeData($row->created_by);
                        $tempCreated = (object)$tempCreated;
                        $rightPane["created_by"] = (isset($tempCreated->display_name_0) && $tempCreated->display_name_0) ? $tempCreated->display_name_1 : "---";
                    } else {
                        $rightPane["created_by"] = "---";
                    }

                    if ($row->modify_by) {
                        $tempUpdated = $this->core_layout->getEmployeeData($row->modify_by);
                        $tempUpdated = (object)$tempUpdated;
                        $rightPane["modify_by"] = (isset($tempUpdated->display_name_0) && $tempUpdated->display_name_0) ? $tempUpdated->display_name_1 : "";
                    } else {
                        $rightPane["modify_by"] = "";
                    }

                    if ($row->completed_by) {
                        $tempUpdated = $this->core_layout->getEmployeeData($row->completed_by);
                        $tempUpdated = (object)$tempUpdated;
                        $rightPane["completed_by"] = (isset($tempUpdated->display_name_0) && $tempUpdated->display_name_0) ? $tempUpdated->display_name_1 : "";
                    } else {
                        $rightPane["completed_by"] = "";
                    }

                    $rightPane["created_dt"] = (isset($row->created_dt) && $row->created_dt !== "0000-00-00 00:00:00") ? date("F d, Y H:i A", strtotime($row->created_dt)) : "---";
                    $rightPane["modify_dt"] = (isset($row->modify_dt) && $row->modify_dt !== "0000-00-00 00:00:00") ? date("F d, Y H:i A", strtotime($row->modify_dt)) : "---";
                    $rightPane["approved_dt"] = (isset($row->approved_dt) && $row->approved_dt !== "0000-00-00 00:00:00") ? date("F d, Y H:i A", strtotime($row->approved_dt)) : "---";
                    $rightPane["completed_dt"] = (isset($row->completed_dt) && $row->completed_dt !== "0000-00-00 00:00:00") ? date("F d, Y H:i A", strtotime($row->completed_dt)) : "---";
                    $leftPane = $row;
                }

                $metaData = $this->getPersonnelRequestMetaById($id);
                if (isset($metaData) && count($metaData) > 0) {
                    $rightPane["meta"] = $metaData;
                    $rightPane["meta_count"] = count($metaData);
                }
            }
            $hris = array();
            $hris = $row;
            $arrData["data"] = $leftPane;
            $arrData["left_pane"] = $leftPane;
            $arrData["right_pane"] = $rightPane;
            $arrData["hris"] = $hris;
            return $arrData;
        }

        function getPersonnelRequestMetaById($id = null)
        {
            $arrMeta = array();
            if ($id) {
                $this->db->order_by("id", "DESC");
                $query = $this->db->get_where($this->applicationMetaTable, array("meta_id" => $id));
                if ($query->num_rows() > 0) {
                    $fields = array("cancelled_log", "denied_log", "updated_log", "hold_log", "activated_log", "completed_log");
                    foreach ($query->result() as $key => $value) {
                        if (in_array($value->meta_field, $fields)) {
                            $tempField = explode("_", $value->meta_field);
                            if (isset($tempField[0]) && $tempField[0]) {
                                $tempData = unserialize($value->meta_value);
                                $empName = $this->core_layout->getEmployeeData($tempData["id"]);
                                $tempData["name"] = (isset($empName["display_name_1"]) && $empName["display_name_1"]) ? $empName["display_name_1"] : "---";
                                $tempData["date"] = date("F d, Y H:i A", strtotime($tempData["date"]));
                                $tempData["type"] = $tempField[0];
                                $tempData["meta"] = $value->id;
                                $arrMeta[] = $tempData;
                            }
                        }
                    }
                }
            }

            return $arrMeta;
        }

        function pr_position_details(){
            $name = $this->input->post('a');
            $this->db->select("job_desc, qualification");
            $this->db->where("name", $name);
            $val = $this->db->get("gcchris.tblposition");
            $data = $val->row_array();

            $val = array();

            $jobdesc = new DOMDocument;
            @$jobdesc->loadHTML($data['job_desc']);
            $count_jobdesc = $jobdesc->getElementsByTagName("li");

            $count_d = $count_jobdesc->length;
            if($count_d > 0){
                $d_job_description = $data['job_desc'];
            }else{
                $d_job_description = nl2br($data['job_desc']);
            }
            
            $qualification = new DOMDocument;
            @$qualification->loadHTML($data['qualification']);
            $count_qualification = $qualification->getElementsByTagName("li");

            $count_qualification = $count_qualification->length;
            if($count_qualification > 0){
                $d_qualification = $data['qualification'];
            }else{
                $d_qualification = nl2br($data['qualification']);
            }


            $val["job_description"] = $d_job_description;
            $val["qualification"] = $d_qualification;
            return $val;
        }

        function getModalCompletedRemarks($id = null)
        {
            $resultset = array();
            if ($id) {
                $tempData = array();
                $this->db->select("a.current, a.people_no, b.*");
                $this->db->from($this->applicationTable . " as a");
                $this->db->join($this->applicationMetaTable . " as b", "b.meta_id = a.id", "LEFT");
                $this->db->where("b.id", $id);
                $meta = $this->db->get();

                if ($meta->num_rows() == 1) {
                    $row = $meta->row();
                    $metaValue = unserialize($row->meta_value);
                    $tempData["remarks"] = (isset($metaValue["remarks"]) && $metaValue["remarks"]) ? $metaValue["remarks"] : "No remarks found!";
                    $tempData["current"] = $row->current;
                    $tempData["people_no"] = $row->people_no;

                    $html = $this->load->view("hris/masterfile/personnel_request/modals/completed_content", $tempData, true);

                    $resultset["response"] = true;
                    $resultset["html"] = $html;
                } else {
                    return false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getModalApprovalAction($status = null, $id = null)
        {
            $resultset = array();

            if ($status && $id) {
                $tempData = array();
                $tempData["id"] = $id;
                $tempData["status"] = $status;
                $html = $this->load->view("hris/masterfile/personnel_request/modals/approval_content", $tempData, true);

                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getModalCancelAction($status = null, $id = null)
        {
            $resultset = array();

            if ($status && $id) {
                $tempData = array();
                $tempData["id"] = $id;
                $tempData["status"] = $status;
                $html = $this->load->view("hris/masterfile/personnel_request/modals/cancel_content", $tempData, true);

                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function setModalApprovalPersonnelRequest()
        {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if ($post) {
                $id = $post["id"];
                $where = array();
                $where["id"] = $post["id"];

                $completedRemark = (isset($post["completed_remark"]) && $post["completed_remark"]) ? $post["completed_remark"] : "";
                if ($post["status"] == "Completed") {
                    unset($post["csrf_token"], $post["completed_remark"], $post["id"]);
                } else {
                    unset($post["csrf_token"], $post["id"]);
                }

                if ($post["status"] == "Ongoing") {
                    $post["approved_dt"] = date("Y-m-d H:i:s");
                    $post["approved_by"] = $session["emp_id"];
                }

                $update = $this->db->update($this->applicationTable, $post, $where);
                $personnelData = $this->getPersonnelData($id);
                if ($update) {
                    if ($post["status"] == "Denied") {
                        $arrDatax = array();
                        $arrDatax["id"] = $session["emp_id"];
                        $arrDatax["date"] = date("Y-m-d H:i:s");
                        $arrDatax["remarks"] = $post["remark"];


                        $metaData = array();
                        $metaData["meta_id"] = $id;
                        $metaData["meta_field"] = "denied_log";
                        $metaData["meta_value"] = serialize($arrDatax);

                        if ($metaData) {
                            $this->db->insert($this->applicationMetaTable, $metaData);
                        }
                    }

                    if ($post["status"] == "Completed") {
                        $arrDatax = array();
                        $arrDatax["id"] = $session["emp_id"];
                        $arrDatax["date"] = date("Y-m-d H:i:s");
                        $arrDatax["remarks"] = $completedRemark;

                        $metaData = array();
                        $metaData["meta_id"] = $id;
                        $metaData["meta_field"] = "completed_log";
                        $metaData["meta_value"] = serialize($arrDatax);

                        if ($metaData) {
                            $this->db->insert($this->applicationMetaTable, $metaData);
                        }
                    }

                    $data = $this->getCurrentPersonnelRequest($id);
        
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Personnel request approval has been proccessed successfully.";
                    $this->core_layout->setEventLog("User has approved personnel request: <strong>".$personnelData->position_name."</strong>","insert", "success", "gcchris", "user");
                    $resultset["data"] = $data;
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to proccess personnel request approval!";
                    $this->core_layout->setEventLog("User failed to proccess personnel request approval for <strong>".$personnelData->position_name."</strong>","insert", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Persnonnel request - Error, No post data found.","insert", "error", "gcchris", "system");
            }

            return $resultset;
        }

        function setModalStatusPersonnelRequest()
        {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if ($post) {
                $id = $post["id"];
                $where = array();
                $where["id"] = $post["id"];
                unset($post["csrf_token"], $post["id"]);

                $post["modify_dt"] = date("Y-m-d H:i:s");
                $post["modify_by"] = $session["emp_id"];

                $update = $this->db->update($this->applicationTable, $post, $where);
                $personnelData = $this->getPersonnelData($id);
                if ($update) {
                    $metaData = array();

                    if (isset($post["status"]) && $post["status"] == "Cancelled") {
                        $arrDatax = array();
                        $arrDatax["id"] = $session["emp_id"];
                        $arrDatax["date"] = date("Y-m-d H:i:s");

                        $metaData["meta_id"] = $id;
                        $metaData["meta_field"] = "cancelled_log";
                        $metaData["meta_value"] = serialize($arrDatax);
                    }

                    if (isset($post["status"]) && $post["status"] == "onHold") {
                        $arrDatax = array();
                        $arrDatax["id"] = $session["emp_id"];
                        $arrDatax["date"] = date("Y-m-d H:i:s");

                        $metaData["meta_id"] = $id;
                        $metaData["meta_field"] = "hold_log";
                        $metaData["meta_value"] = serialize($arrDatax);
                    }

                    if (isset($post["status"]) && $post["status"] == "Ongoing") {
                        $arrDatax = array();
                        $arrDatax["id"] = $session["emp_id"];
                        $arrDatax["date"] = date("Y-m-d H:i:s");

                        $metaData["meta_id"] = $id;
                        $metaData["meta_field"] = "activated_log";
                        $metaData["meta_value"] = serialize($arrDatax);
                    }

                    if ($metaData) {
                        $this->db->insert($this->applicationMetaTable, $metaData);
                    }

                    $data = $this->getCurrentPersonnelRequest($id);
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Personnel request status has been proccessed successfully.";
                    $this->core_layout->setEventLog("User has changed the status of personnel request: <strong>".$personnelData->position_name."</strong> to <strong>".$post["status"]."</strong>","update", "success", "gcchris", "user");
                    $resultset["data"] = $data;
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to proccess personnel request status!";
                    $this->core_layout->setEventLog("User failed updating personnel request: <strong>".$personnelData->position_name."</strong>","update", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Personnel request - Error, No post data found.","update", "error", "gcchris", "system");
            }

            return $resultset;
        }

        public function archivePersonnelRequest()
        {
            $resultSet = array();
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $this->db->set("archive_remarks", $post->archive_remarks);
            $this->db->set("is_archived", 1);
            $this->db->where("id", $post->id);
            $personnelData = $this->getPersonnelData($post->id);
            if ($this->db->update($this->applicationTable)) {
                $this->adm_employee->logArchive($this->applicationTable, $post->id, 1);
                $resultSet["success"] = true;
                $resultSet["message"] = "Personnel request was archived.";
                $this->core_layout->setEventLog("User has archived personnel request: <strong>".$personnelData->position_name."</strong>","archive", "success", "gcchris", "user");
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("User has failed archiving personnel request: <strong>".$personnelData->position_name."</strong>","archive", "error", "gcchris", "system");
            }

            return $resultSet;
        }

        private function logChanges($currentData, $newData) {
            $changes = array();
            $changesString = '';
            foreach ($currentData as $field => $value) {
                if (isset($newData[$field]) && $newData[$field]!= $value) {
                    $changes[$field] = array(
                        'old' => $value,
                        'new' => $newData[$field]
                    );
                }
            }
            foreach ($changes as $field => $change) {
                if (strtolower($field) == 'department_id'){
                    $changesString.= " Field: $field, from: <strong>". $this->getDepartmentById($change['old'])->description. "</strong>, to: <strong>". $this->getDepartmentById($change['new'])->description. "</strong>\n";
                }
                else if (strtolower($field) == 'position_id'){
                    $changesString.= " Field: $field, from: <strong>". $this->getPositionById($change['old'])->name. "</strong>, to: <strong>". $this->getPositionById($change['new'])->name. "</strong>\n";
                }
                else if (strtolower($field) == 'salary_id'){
                    $changesString.= " Field: $field, from: <strong>". $this->getSalaryById($change['old'])->description. "</strong>, to: <strong>". $this->getSalaryById($change['new'])->description. "</strong>\n";
                }
                else if (strtolower($field) == 'requested_by'){
                    $changesString.= " Field: $field, from: <strong>". $this->getEmployeeNameById($change['old']). "</strong>, to: <strong>". $this->getEmployeeNameById($change['new']). "</strong>\n";
                }
                else{
                    $changesString.= " Field: $field, from: $change[old], to: $change[new]\n";
                }
            }
            return $changesString;
        }

        private function getPersonnelData($id) {
            $this->db->select("app.*, pos.name as position_name");
            $this->db->from($this->applicationTable.' as app');
            $this->db->join($this->positionTable .' as pos', 'pos.id = app.position_id', 'LEFT');
            $this->db->where('app.id', $id);
            $query = $this->db->get(); 
            return $query->row();
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

        private function getSalaryById($id){
            $this->db->select("description");
            $this->db->from($this->salaryTable);
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

        private function getEmployeeNameById($id){
            $result = null;
            $this->db->select("lastname, firstname, middlename, suffix");
            $this->db->from($this->employeeTable);
            $this->db->where("employee_status", "Active");
            $this->db->where('id', $id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $employeeData = $query->row_array();
                $displayName = $this->core_layout->getDisplayName($employeeData);
                $result = $displayName['display_name_1'] ?? "No Assigned Name";
            } else {
                $result = "No Employee Found";
            }
            $this->db->reset_query();
            return $result;
        }

    }