<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Transmittal_m extends CI_Model {
        protected $eformsTable = "gcceforms";
        protected $transmittal_body_temp = "gcceforms.transmittal_body_temp";
        protected $transmittal_body = "gcceforms.transmittal_body";

        private $current_action =  array();
        private $user_data = array();

        public function __construct() {
            parent::__construct();
            $this->core_layout->setPrivilegeName("tr_masterfile");
            $this->user_data = $this->session->userdata("logged_in"); 
            $this->current_action = $this->core_layout->getCurrentActions();
        }

        function getDatatableRequest() {
            $this->core_layout->setPrivilegeName("tr_masterfile");
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "2", "dir" => "desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $advanceSearch = $post["advanceSearch"];
            $advanceSearchData = $post["advanceSearchData"];
            $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
            $status = (isset($post['status']) && $post['status']) ? ucwords($post['status']) : null; //clicked in portal dashboard

            
            $view_by_dept = (in_array("view_by_dept", $this->current_action)) ? true : false;
            
            $rowData = $this->get_all_transmittal_items($query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $advanceSearch, $advanceSearchData, $status, $view_by_dept);
            $rowCount = $this->get_all_transmittal_items_count($query_builder, $search, $advanceSearch, $advanceSearchData, $status, $view_by_dept);
            
            // if ($advanceSearch === "true") {
            //     $rowData = $this->advanceSearchTransmittal($limit, $offset, $sortBy, $sortOrder, $advanceSearchData, $status, $view_by_dept);
            //     $rowCount = $this->advanceSearchTransmittalTotal($advanceSearchData, $status, $view_by_dept);
            // } else {
            //     if ($search == "") {
            //         $rowData = $this->get_all_post($query_builder, $limit, $offset, $sortBy, $sortOrder, $status, $view_by_dept);
            //         $rowCount = $this->get_all_post_count($query_builder, $status, $view_by_dept);
            //     }
            // }

            if ($search) {
                // $rowData = $this->get_searched_item($query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $status, $view_by_dept);
                // $rowCount = $this->get_searched_item_count($query_builder, $search, $status, $view_by_dept);
                $this->core_layout->setEventLog("Transmittal Mastefile - Search {$search} in datatable.", "search", "success", "gcceforms", "user");
            }

            if($query_builder){
                $this->core_layout->setEventLog("Transmittal Mastefile - Generate masterfile through query builder `{$query_builder}`.", "search", "success", "gcceforms", "user");
            }

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        public function get_all_transmittal_items($query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $isAdvanceSearch = false, $_data = null, $status = null, $view_dept) {
            $check = date("Y-m-d", strtotime("-1 year", time()));
            $data = array();

            $filterFields = array("a.id", " a.status", "a.priority", "a.reference_no", "a.company_from", "a.ship_date", "c.description", "a.created_by", "a.created_dt", "a.ship_to", "a.company_to", "a.department_to", "a.ship_to_address", "e.firstname", "e.lastname", "cr.lastname", "cr.firstname");

            $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc, a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, IF(comp.id IS NULL, a.company_to, comp.code) company_to, IF(dep.id IS NULL, a.department_to, dep.code) department_to, a.ship_to_address, a.ship_to_address";

            if ($isAdvanceSearch) {
                foreach ($_data as $key => $value) {
                    if (!empty($value)) {
                        if ($key === 'description') {
                            $this->db->like($key, $value, 'both');
                        } elseif ($key === 'company_from') {
                            $this->db->select("code");
                            $this->db->where('id', $value);
                            $this->db->from("gcchris.tblcompanies");
                            $company_name = $this->db->get()->row();

                            $this->db->reset_query();

                            $this->db->group_start();
                                $this->db->where($key, $company_name->code);
                                $this->db->or_where($key, $value);
                            $this->db->group_end();
                        } elseif ($key === 'delivered_to') {
                            $this->db->like("CONCAT(ship_to, company_to, department_to, position_to, ship_to_address)", $value, "BOTH");
                        } elseif ($key === 'created_by') {
                            $this->db->select("CONCAT(firstname, ' ', lastname) fname");
                            $this->db->where('id', $value);
                            $this->db->from("gccmaster.tblemployees");
                            $employee_name = $this->db->get()->row();

                            $this->db->reset_query();

                            if (empty($employee_name)) {
                                $this->db->group_start();
                                    $this->db->or_like($key, $value, "BOTH");
                                    $this->db->like($key, $employee_name->fname, "BOTH");
                                $this->db->group_end();
                            } else{
                                $this->db->like($key, $value, "BOTH");
                            }
                        } elseif ($key === 'ship_date' || $key === 'created_dt') {
                            $this->db->where(`DATE($key)`, date('Y-m-d', strtotime($value)));
                        } else {
                            $this->db->where($key, $value);
                            $this->db->where('DATE(a.ship_date) >= ', $check);
                        }
                    }
                }
            }

            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company_to", "LEFT");
            $this->db->join("gcchris.tbldepartments dep", "dep.id = a.department_to", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            $this->db->join("gccmaster.tblemployees cr", "cr.id = a.created_by", "LEFT");
            $this->db->where('status != ', 'Cancelled');
            // $this->db->where('DATE(a.ship_date) >= ', $check);

            if($query_builder){
                $this->db->where($query_builder);
            }

            if (isset($search) && $search) {
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
    
            if($status){
                $this->db->where('a.status', $status);
            }
                
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }

            if ($view_dept && ($this->user_data['emp_id']!=1)) {
                $this->db->where('e.department_id', $this->user_data['department']);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();

                foreach ($query->result() as $key => $rs) {
                    $rs->company_from = (is_numeric($rs->company_from)) ? $this->companyFrom($rs->company_from) : $rs->company_from;
                    $rs->created_by = (is_numeric($rs->created_by)) ? $this->createdBy($rs->created_by) : $rs->created_by;

                    if (is_numeric($rs->ship_to)) {
                        $tempRs = (array)$rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object)$fullname;
                        $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                        $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                    } else {
                        $rs->display_name = "<b>" . $rs->ship_to . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                    }
                    
                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
            }

            return $data;
        }

        public function get_all_transmittal_items_count($query_builder=null, $search = null, $isAdvanceSearch = false, $_data = null, $status = null, $view_dept) {
            $check = date("Y-m-d", strtotime("-1 year", time()));
            
            $filterFields = array("a.id", " a.status", "a.priority", "a.reference_no", "a.company_from", "a.ship_date", "c.description", "a.created_by", "a.created_dt", "a.ship_to", "a.company_to", "a.department_to", "a.ship_to_address", "e.firstname", "e.lastname", "cr.lastname", "cr.firstname");

            $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc, a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, IF(comp.id IS NULL, a.company_to, comp.code) company_to, IF(dep.id IS NULL, a.department_to, dep.code) department_to, a.ship_to_address, a.ship_to_address";

            if ($isAdvanceSearch) {
                foreach ($_data as $key => $value) {
                    if (!empty($value)) {
                        if ($key === 'description') {
                            $this->db->like($key, $value, 'both');
                        } elseif ($key === 'company_from') {
                            $this->db->select("code");
                            $this->db->where('id', $value);
                            $this->db->from("gcchris.tblcompanies");
                            $company_name = $this->db->get()->row();

                            $this->db->reset_query();

                            $this->db->group_start();
                                $this->db->where($key, $company_name->code);
                                $this->db->or_where($key, $value);
                            $this->db->group_end();
                        } elseif ($key === 'delivered_to') {
                            $this->db->like("CONCAT(ship_to, company_to, department_to, position_to, ship_to_address)", $value, "BOTH");
                        } elseif ($key === 'created_by') {
                            $this->db->select("CONCAT(firstname, ' ', lastname) fname");
                            $this->db->where('id', $value);
                            $this->db->from("gccmaster.tblemployees");
                            $employee_name = $this->db->get()->row();

                            $this->db->reset_query();

                            if (empty($employee_name)) {
                                $this->db->group_start();
                                    $this->db->or_like($key, $value, "BOTH");
                                    $this->db->like($key, $employee_name->fname, "BOTH");
                                $this->db->group_end();
                            } else{
                                $this->db->like($key, $value, "BOTH");
                            }
                        } elseif ($key === 'ship_date' || $key === 'created_dt') {
                            $this->db->where(`DATE($key)`, date('Y-m-d', strtotime($value)));
                        } else {
                            $this->db->where($key, $value);
                            $this->db->where('DATE(a.ship_date) >= ', $check);
                        }
                    }
                }
            }

            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company_to", "LEFT");
            $this->db->join("gcchris.tbldepartments dep", "dep.id = a.department_to", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            $this->db->join("gccmaster.tblemployees cr", "cr.id = a.created_by", "LEFT");
            $this->db->where('status != ', 'Cancelled');
            // $this->db->where('DATE(a.ship_date) >= ', $check);

            if($query_builder){
                $this->db->where($query_builder);
            }

            if (isset($search) && $search) {
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
    
            if ($status) {
                $this->db->where('a.status', $status);
            }

            if ($view_dept && ($this->user_data['emp_id'] != 1)) {
                $this->db->where('e.department_id', $this->user_data['department']);
            }

            $query = $this->db->get();
            return $query->num_rows();
        }

        private function advanceSearchTransmittal($limit, $offset, $sortBy, $sortOrder, $_data, $status = null, $view_dept) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);

            foreach ($_data as $key => $datum) {
                if (!empty($datum)) {
                    if ($key === "description") {
                        $this->db->like($key, $datum, "both");
                    } elseif ($key === "company_from") {
                        $company_name = $this->db->query("SELECT code FROM gcchris.tblcompanies WHERE id=$datum")->row("code");

                        $this->db->group_start();
                        $this->db->where($key, $company_name);
                        $this->db->or_where($key, $datum);
                        $this->db->group_end();
                    } elseif ($key === "delivered_to") {
                        $this->db->like("CONCAT(ship_to, company_to, department_to, position_to, ship_to_address)", $datum, "BOTH");
                    } elseif ($key === "created_by") {
                        $employee_name = $this->db->select("CONCAT(firstname, ' ', lastname) fname")->get_where("gccmaster.tblemployees", array("id" => $datum))->row("fname");
                        if (empty($employee_name)) {
                            $this->db->group_start();
                            $this->db->or_like($key, $datum, "BOTH");
                            $this->db->like($key, $employee_name, "BOTH");
                            $this->db->group_end();
                        } else {
                            $this->db->like($key, $datum, "BOTH");
                        }
                    } elseif ($key === "ship_date") {
                        $k = "DATE(" . $key . ")";
                        $date = date("Y-m-d", strtotime($datum));
                        $this->db->where($k, $date);
                    } elseif ($key === "created_dt") {
                        $k = "DATE(" . $key . ")";
                        $date = date("Y-m-d", strtotime($datum));
                        $this->db->where($k, $date);
                    } else {
                        $this->db->where($key, $datum);
                        $this->db->where('ship_date >= ', $check);
                    }
                }
            }

            $data = array();
            $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc,  
                    a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, 
                    a.company_to, a.department_to, a.ship_to_address";
            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            $this->db->where('status != ', 'Cancelled');

            if($status){
                $this->db->where('a.status', $status);
            }

            $i = $sortOrder[0]['column'];
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
            } else {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }

            if($view_dept && ($this->user_data['emp_id']!=1)){
                $this->db->where('e.department_id', $this->user_data['department']);
            }
         
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if (is_numeric($rs->company_from)) {
                        $rs->company_from = $this->companyFrom($rs->company_from);
                    } else {
                        $rs->company_from = $rs->company_from;
                    }
                    if (is_numeric($rs->ship_to)) {
                        $tempRs = (array)$rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object)$fullname;
                        $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                        $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                    } else {
                        $rs->display_name = "<b>" . $rs->ship_to . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                    }
                    if (is_numeric($rs->created_by)) {
                        $rs->created_by = $this->createdBy($rs->created_by);
                    } else {
                        $rs->created_by = $rs->created_by;
                    }

                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
            }

            return $data;
        }

        private function advanceSearchTransmittalTotal($_data, $status = null, $view_dept) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);

            foreach ($_data as $key => $datum) {
                if (!empty($datum)) {
                    if ($key === "description") {
                        $this->db->like($key, $datum, "both");
                    } elseif ($key === "company_from") {
                        $company_name = $this->db->query("SELECT code FROM gcchris.tblcompanies WHERE id=$datum")->row("code");

                        $this->db->group_start();
                        $this->db->where($key, $company_name);
                        $this->db->or_where($key, $datum);
                        $this->db->group_end();
                    } elseif ($key === "delivered_to") {
                        $this->db->like("CONCAT(ship_to, company_to, department_to, position_to, ship_to_address)", $datum, "BOTH");
                    } elseif ($key === "created_by") {
                        $employee_name = $this->db->select("CONCAT(firstname, ' ', lastname) fname")->get_where("gccmaster.tblemployees", array("id" => $datum))->row("fname");
                        if (empty($employee_name)) {
                            $this->db->group_start();
                            $this->db->or_like($key, $datum, "BOTH");
                            $this->db->like($key, $employee_name, "BOTH");
                            $this->db->group_end();
                        } else {
                            $this->db->like($key, $datum, "BOTH");
                        }
                    } elseif ($key === "ship_date") {
                        $k = "DATE(" . $key . ")";
                        $date = date("Y-m-d", strtotime($datum));
                        $this->db->where($k, $date);
                    } elseif ($key === "created_dt") {
                        $k = "DATE(" . $key . ")";
                        $date = date("Y-m-d", strtotime($datum));
                        $this->db->where($k, $date);
                    } else {
                        $this->db->where($key, $datum);
                        $this->db->where('ship_date >= ', $check);
                    }
                }
            }

            $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc,  
                    a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, 
                    a.company_to, a.department_to, a.ship_to_address";
            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            $this->db->where('status != ', 'Cancelled');
            if($status){
                $this->db->where('a.status', $status);
            }

            if($view_dept && ($this->user_data['emp_id']!=1)){
                $this->db->where('e.department_id', $this->user_data['department']);
            }

            return $this->db->count_all_results();
        }

        private function get_all_post_count($query_builder=null, $status = null, $view_dept) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc,  a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, a.company_to, a.department_to, a.ship_to_address";
            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            $this->db->join("gccmaster.tblemployees creator", "creator.id = a.created_by", "LEFT");
            $this->db->where('status != ', 'Cancelled');
            $this->db->where('ship_date >= ', $check);
            if($query_builder){
                $this->db->where($query_builder);
            }
            if($status){
                $this->db->where('a.status', $status);
            }

            if($view_dept && ($this->user_data['emp_id']!=1)){
                $this->db->where('e.department_id', $this->user_data['department']);
            }

            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_post($query_builder=null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null, $view_dept) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc,  
                    a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, 
                    IF(comp.id IS NULL, a.company_to, comp.code) company_to, 
	                IF(dep.id IS NULL, a.department_to, dep.code) department_to, `a`.`ship_to_address`,
                    a.ship_to_address";
            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company_to", "LEFT");
            $this->db->join("gcchris.tbldepartments dep", "dep.id = a.department_to", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            $this->db->where('status != ', 'Cancelled');
            $this->db->where('ship_date >= ', $check);
            if($query_builder){
                $this->db->where($query_builder);
            }

            if($status){
                $this->db->where('a.status', $status);
            }

            $i = $sortOrder[0]['column'];
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
            } else {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }

            if($view_dept && ($this->user_data['emp_id']!=1)){
                $this->db->where('e.department_id', $this->user_data['department']);
            }
              
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if (is_numeric($rs->company_from)) {
                        $rs->company_from = $this->companyFrom($rs->company_from);
                    } else {
                        $rs->company_from = $rs->company_from;
                    }
                    if (is_numeric($rs->ship_to)) {
                        $tempRs = (array)$rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object)$fullname;
                        $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                        $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                    } else {
                        $rs->display_name = "<b>" . $rs->ship_to . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                    }
                    if (is_numeric($rs->created_by)) {
                        $rs->created_by = $this->createdBy($rs->created_by);
                    } else {
                        $rs->created_by = $rs->created_by;
                    }

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

        private function get_searched_item($query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null, $view_dept) {

            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", " a.status", "a.priority", "a.reference_no", "a.company_from", "a.ship_date", "c.description", "a.created_by", "a.created_dt", "a.ship_to", "a.company_to", "a.department_to", "a.ship_to_address", "e.firstname", "e.lastname", "cr.lastname", "cr.firstname");
                $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc,  
                    a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, 
                    IF(comp.id IS NULL, a.company_to, comp.code) company_to, 
	                IF(dep.id IS NULL, a.department_to, dep.code) department_to, `a`.`ship_to_address`,
                    a.ship_to_address";
                $this->db->select($sql);
                $this->db->from("gcceforms.transmittal a");
                $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
                $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company_to", "LEFT");
                $this->db->join("gcchris.tbldepartments dep", "dep.id = a.department_to", "LEFT");
                $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
                $this->db->join("gccmaster.tblemployees cr", "cr.id = a.created_by", "LEFT");
                $this->db->where('status != ', 'Cancelled');
                $this->db->where('ship_date >= ', $check);
                if($query_builder){
                    $this->db->where($query_builder);
                }

                if($status){
                    $this->db->where('a.status', $status);
                }
                    
                if($limit != -1){
                    $this->db->limit($limit, $offset);
                }

                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
                $i = $sortOrder[0]['column'];
                if ($sortBy[$i]['data'] == "firstname") {
                    $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                    $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
                } else {
                    $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                }

                if($view_dept && ($this->user_data['emp_id']!=1)){
                    $this->db->where('e.department_id', $this->user_data['department']);
                }

                $query = $this->db->get();

                if ($query->num_rows() > 0) {

                    $arrData = array();
                    foreach ($query->result() as $key => $rs) {
                        if (is_numeric($rs->company_from)) {
                            $rs->company_from = $this->companyFrom($rs->company_from);
                        } else {
                            $rs->company_from = $rs->company_from;
                        }
                        if (is_numeric($rs->ship_to)) {
                            $tempRs = (array)$rs;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                        } else {
                            $rs->display_name = "<b>" . $rs->ship_to . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                        }
                        if (is_numeric($rs->created_by)) {
                            $rs->created_by = $this->createdBy($rs->created_by);
                        } else {
                            $rs->created_by = $rs->created_by;
                        }
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
            } else {
                return array();
            }
        }

        private function get_searched_item_count($query_builder=null, $search = null, $status = null, $view_dept) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", " a.status", "a.priority", "a.reference_no", "a.company_from", "a.ship_date", "c.description", "a.created_by", "a.created_dt", "a.ship_to", "a.company_to", "a.department_to", "a.ship_to_address","cr.lastname", "cr.firstname");
                $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from, a.ship_date, c.description, a.created_by, a.created_dt, CONCAT('<b>',a.ship_to,'</b><br>',a.company_to,'<br>',a.department_to,'<br>', a.ship_to_address) AS deliver";
                $this->db->select($sql);
                $this->db->from("gcceforms.transmittal a");
                $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
                $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
                $this->db->join("gccmaster.tblemployees cr", "cr.id = a.created_by", "LEFT");
                $this->db->where('status != ', 'Cancelled');
                $this->db->where('ship_date >= ', $check);
                if($query_builder){
                    $this->db->where($query_builder);
                }

                if($status){
                    $this->db->where('a.status', $status);
                }
                
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();

                if($view_dept && ($this->user_data['emp_id']!=1)){
                    $this->db->where('e.department_id', $this->user_data['department']);
                }

                $query = $this->db->get();

                $rowCount = $query->num_rows();
            }

            return $rowCount;
        }

        function createdBy($id) {
            $this->db->select("firstname, middlename, lastname, suffix, id");
            $this->db->from("gccmaster.tblemployees");
            $this->db->where("id", $id);
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";

                    $arrData[$key] = $rs;
                }

                return $arrData[0]->display_name;
            } else {
                return array();
            }
        }

        function companyFrom($id) {
            $this->db->select("description, id");
            $this->db->from("gcchris.tblcompanies");
            $this->db->where("id", $id);
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {

                    $arrData[$key] = $rs;
                }

                return $arrData[0]->description;
            } else {
                return array();
            }
        }


        function getArchiveRequest() {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "2", "dir" => "desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

            $rowData = $this->get_all_archive_items($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_archive_items_count($search);
            // if (!$search) {
            //     $rowData = $this->get_all_archive($limit, $offset, $sortBy, $sortOrder);
            //     $rowCount = $this->get_all_archive_count();
            // }

            if ($search) {
            //     $rowData = $this->get_searched_archive_item($search, $limit, $offset, $sortBy, $sortOrder);
            //     $rowCount = $this->get_searched_archive_item_count($search);
                $this->core_layout->setEventLog("Archive Transmittal Mastefile - Search {$search} in datatable.", "search", "success", "gcceforms", "user");
            }
            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function get_all_archive_items($search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder){
            $data = array();
            $check = date("Y-m-d", strtotime("-1 year", time()));

            $filterFields = array("a.id", "a.status", "a.priority", "a.reference_no", "a.company_from", "a.ship_date", "c.description", "a.created_by", "a.created_dt", "a.ship_to", "a.company_to", "a.department_to", "a.ship_to_address", "e.firstname", "e.lastname");
            $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from, a.ship_to, a.ship_date, c.description as trans_desc, a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, IF(comp.id IS NULL, a.company_to, comp.code) company_to, IF(dep.id IS NULL, a.department_to, dep.code) department_to, `a`.`ship_to_address`, a.ship_to_address";

            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company_to", "LEFT");
            $this->db->join("gcchris.tbldepartments dep", "dep.id = a.department_to", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");

            $this->db->group_start();
                $this->db->where('a.status', 'Cancelled');
                $this->db->or_where('DATE(a.ship_date) <= ', $check);
            $this->db->group_end();

            if (isset($search) && $search) {
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

            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $rs->company_from = (is_numeric($rs->company_from)) ? $this->companyFrom($rs->company_from) : $rs->company_from;
                    $rs->created_by = (is_numeric($rs->created_by)) ? $this->createdBy($rs->created_by) : $rs->created_by;

                    if (is_numeric($rs->ship_to)) {
                        $tempRs = (array)$rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object)$fullname;
                        $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                        $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                    } else {
                        $rs->display_name = "<b>" . $rs->ship_to . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                    }

                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
            }

            return $data;
        }

        function get_all_archive_items_count($search = null){
            $check = date("Y-m-d", strtotime("-1 year", time()));

            $filterFields = array("a.id", "a.status", "a.priority", "a.reference_no", "a.company_from", "a.ship_date", "c.description", "a.created_by", "a.created_dt", "a.ship_to", "a.company_to", "a.department_to", "a.ship_to_address", "e.firstname", "e.lastname");
            $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc, a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, IF(comp.id IS NULL, a.company_to, comp.code) company_to, IF(dep.id IS NULL, a.department_to, dep.code) department_to, `a`.`ship_to_address`, a.ship_to_address";

            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company_to", "LEFT");
            $this->db->join("gcchris.tbldepartments dep", "dep.id = a.department_to", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            
            $this->db->group_start();
                $this->db->where('a.status', 'Cancelled');
                $this->db->or_where('DATE(a.ship_date) <= ', $check);
            $this->db->group_end();

            if (isset($search) && $search) {
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

        private function get_all_archive_count() {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc,  a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, a.company_to, a.department_to, a.ship_to_address";
            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            $this->db->where('status ', 'Cancelled');
            $this->db->or_where('ship_date <= ', $check);
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_archive($limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc,  
                    a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, 
                    IF(comp.id IS NULL, a.company_to, comp.code) company_to, 
	                IF(dep.id IS NULL, a.department_to, dep.code) department_to, `a`.`ship_to_address`,
                    a.ship_to_address";
            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company_to", "LEFT");
            $this->db->join("gcchris.tbldepartments dep", "dep.id = a.department_to", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            $this->db->where('status ', 'Cancelled');
            $this->db->or_where('ship_date <= ', $check);
         
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
            } else {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if (is_numeric($rs->company_from)) {
                        $rs->company_from = $this->companyFrom($rs->company_from);
                    } else {
                        $rs->company_from = $rs->company_from;
                    }
                    if (is_numeric($rs->ship_to)) {
                        $tempRs = (array)$rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object)$fullname;
                        $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                        $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                    } else {
                        $rs->display_name = "<b>" . $rs->ship_to . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                    }
                    if (is_numeric($rs->created_by)) {
                        $rs->created_by = $this->createdBy($rs->created_by);
                    } else {
                        $rs->created_by = $rs->created_by;
                    }

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


        private function get_searched_archive_itemv1($search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {

            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            if ($search) {
                $filterFields = array("a.id", "a.status", "a.priority", "a.reference_no", "a.company_from", "a.ship_date", "c.description", "a.created_by", "a.created_dt", "a.ship_to", "a.company_to", "a.department_to", "a.ship_to_address", "e.firstname", "e.lastname");
                $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc,  
                    a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, 
                    IF(comp.id IS NULL, a.company_to, comp.code) company_to, 
	                IF(dep.id IS NULL, a.department_to, dep.code) department_to, `a`.`ship_to_address`,
                    a.ship_to_address";
                $this->db->select($sql);
                $this->db->from("gcceforms.transmittal a");
                $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
                $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company_to", "LEFT");
                $this->db->join("gcchris.tbldepartments dep", "dep.id = a.department_to", "LEFT");
                $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
                $this->db->where('ship_date <= ', $check);
                if($limit != -1){
                    $this->db->limit($limit, $offset);
                }

                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();

                $i = $sortOrder[0]['column'];
                if ($sortBy[$i]['data'] == "firstname") {
                    $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                    $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
                } else {
                    $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                }
                $this->db->limit($limit, $offset);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {

                    $arrData = array();
                    foreach ($query->result() as $key => $rs) {
                        if (is_numeric($rs->company_from)) {
                            $rs->company_from = $this->companyFrom($rs->company_from);
                        } else {
                            $rs->company_from = $rs->company_from;
                        }
                        if (is_numeric($rs->ship_to)) {
                            $tempRs = (array)$rs;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                        } else {
                            $rs->display_name = "<b>" . $rs->ship_to . "</b><br>" . $rs->company_to . "<br>" . $rs->department_to . "<br>" . $rs->ship_to_address;
                        }
                        if (is_numeric($rs->created_by)) {
                            $rs->created_by = $this->createdBy($rs->created_by);
                        } else {
                            $rs->created_by = $rs->created_by;
                        }
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
            } else {
                return array();
            }
        }

        private function get_searched_archive_item_countv1($search = null) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", " a.status", "a.priority", "a.reference_no", "a.company_from", "a.ship_date", "c.description", "a.created_by", "a.created_dt", "a.ship_to", "a.company_to", "a.department_to", "a.ship_to_address");
                $sql = "a.id, a.status, a.priority, a.reference_no, a.company_from,a.ship_to, a.ship_date, c.description as trans_desc,  a.created_by, a.created_dt, e.firstname, e.middlename, e.lastname,e.suffix, a.company_to, a.department_to, a.ship_to_address";
                $this->db->select($sql);
                $this->db->from("gcceforms.transmittal a");
                $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
                $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
                $this->db->where('ship_date <= ', $check);
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
                $query = $this->db->get();

                $rowCount = $query->num_rows();
            }

            return $rowCount;
        }

        function getCompanyCollection() {
            $get = $this->input->get();
            $resultarray = array();
            // if (isset($get['q'])) {
            //     $query = $this->db->query("SELECT `id`,`description` FROM gcchris.tblcompanies WHERE `description` LIKE '%{$get['q']}%' ORDER BY `description` ASC");
            // } else {
            //     $query = $this->db->query("SELECT `id`,`description` FROM gcchris.tblcompanies ORDER BY `description` ASC");
            // }

            $this->db->select('id, description');
            $this->db->from('gcchris.tblcompanies');
            
            if (isset($get['q']) && $get['q']) {
                $this->db->like('description', $get['q'], 'both');
            }

            $this->db->order_by('description', 'ASC');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["description"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function getDepartmentCollection() {
            $get = $this->input->get();
            $resultarray["results"] = array();
            // if (isset($get['q'])) {
            //     $query = $this->db->query("SELECT `id`,`description` FROM gcchris.tbldepartments WHERE `description` LIKE '%{$get['q']}%' ORDER BY `description` ASC");
            // } else {
            //     $query = $this->db->query("SELECT `id`,`description` FROM gcchris.tbldepartments ORDER BY `description` ASC");
            // }

            // $this->db->select('id, description');
            // $this->db->from('gcchris.tbldepartments');
            
            // if (isset($get['q']) && $get['q']) {
            //     $this->db->like('description', $get['q'], 'both');
            // }

            // $this->db->order_by('description', 'ASC');
            // $query = $this->db->get();

            // if ($query->num_rows() > 0) {
            //     foreach ($query->result_array() as $_query) {
            //         $data = array();
            //         $data["id"] = $_query["id"];
            //         $data["text"] = $_query["description"];
            //         $resultarray[] = $data;
            //     }
            // }
            // return array("results" => $resultarray);

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

        function getEmployeeCollection() {
            $get = $this->input->get();
            $resultarray = array();
            // if (isset($get['q'])) {
            //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE (employee_status='Active') AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%') ORDER BY firstname ASC LIMIT 10");
            // } else {
            //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC LIMIT 10");
            // }

            $this->db->select('id, firstname, lastname, middlename, suffix');
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('employee_status', 'Active');
            
            if (isset($get['q']) && $get['q']) {
                $this->db->like('firstname', $get['q'], 'both');
                $this->db->or_like('lastname', $get['q'], 'both');
            }

            $this->db->order_by('firstname', 'ASC');
            $this->db->limit(10);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $tempRs = (array)$_query;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;

                    $data["id"] = $_query["id"];
                    $data["text"] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function getVehicleCollection() {
            $get = $this->input->get();
            $resultarray = array();
            // if (isset($get['q'])) {
            //     $query = $this->db->query("SELECT  id, CONCAT(gen_code, ' | ', plateno,' | ',name) AS vehicle FROM gccasset.vehicles WHERE  isCompo='0' 
            //     AND status2 NOT IN ('archived', 'damage', 'junk', 'lost', 'sold') 
            //     AND (plateno LIKE '%{$get['q']}%' OR name LIKE '%{$get['q']}%' OR gen_code LIKE '%{$get['q']}%') ORDER BY description ASC");
            // } else {
            //     $query = $this->db->query("SELECT  id, CONCAT(gen_code, ' | ',plateno,' | ',name) AS vehicle FROM gccasset.vehicles WHERE isCompo='0' 
            //     AND status2 NOT IN ('archived', 'damage', 'junk', 'lost', 'sold') ORDER BY plateno ASC");
            // }

            $this->db->select('id, CONCAT(gen_code, " | ", plateno," | ",name) AS vehicle');
            $this->db->from('gccasset.vehicles');
            $this->db->where('isCompo', '0');
            $this->db->where_not_in('status2', array('archived', 'damage', 'junk', 'lost', 'sold'));
            $this->db->order_by('plateno', 'ASC');
            
            if (isset($get['q']) && $get['q']) {    
                $this->db->like('plateno', $get['q'], 'both');
                $this->db->or_like('name', $get['q'], 'both');
                $this->db->or_like('gen_code', $get['q'], 'both');
                $this->db->order_by('description', 'ASC');
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["vehicle"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function driver() {
            $get = $this->input->get();
            $resultarray = array();
            // if (isset($get['q'])) {
            //     $query = $this->db->query("SELECT  id, firstname, middlename, lastname, suffix, position FROM gccmaster.tblemployees WHERE  employee_status='Active' AND position='Driver' AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%') ORDER BY firstname ASC");
            // } else {
            //     $query = $this->db->query("SELECT  id, firstname, middlename, lastname, suffix, position FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC");
            // }

            $this->db->select('id, firstname, middlename, lastname, suffix, position');
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('employee_status', 'Active');
            $this->db->order_by('firstname', 'ASC');
            
            if (isset($get['q']) && $get['q']) {    
                $this->db->like('firstname', $get['q'], 'both');
                $this->db->or_like('lastname', $get['q'], 'both');
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $driver = mb_strtoupper($_query['position']);
                    if (strpos($driver, "DRIVER") !== false || strpos($driver, 'BACKHOE') !== false || strpos($driver, 'TRACTOR') !== false || strpos($driver, 'GRADER') !== false || strpos($driver, 'ROLLER') !== false || strpos($driver, 'PAYLOADER') !== false || strpos($driver, 'BULLDOZER') !== false || strpos($driver, 'CRANE') !== false || strpos($driver, 'MCC') !== false) {
                        $data = array();
                        $tempRs = (array)$_query;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object)$fullname;

                        $data["id"] = $_query["id"];
                        $data["text"] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                        $resultarray[] = $data;
                    }
                }
            }
            return array("results" => $resultarray);
        }


        function emp_details($emp) {
            $this->db->select("emp.id, 
                               UCASE(CONCAT(emp.lastname, ' ', emp.firstname)) emp_name,
                               IFNULL(comp.id, emp.company_id) company_id,
                               IFNULL(dep.id, emp.department_id) department_id,	
                               IFNULL(pos.id, emp.position) `position`,
                               comp.company_address,
                               comp.code comp_str, dep.description dep_str, pos.name pos_str,
                               ISNULL(comp.code) temp_str,
                               ISNULL(dep.description) temp_dep_str,
                               ISNULL(pos.name) temp_pos_str,
                               IF(comp.code IS NULL, 0, 1) use_str");
            $this->db->join("gcchris.tblcompanies comp", "emp.company_id = comp.id", "LEFT");
            $this->db->join("gcchris.tbldepartments dep", "emp.department_id = dep.id", "LEFT");
            $this->db->join("gcchris.tblposition pos", "emp.position = pos.id", "LEFT");
            $this->db->where("emp.id", $emp);
            $this->db->where("emp.employee_status", 'Active');
            $query = $this->db->get("gccmaster.tblemployees emp");
            return $query->row();
        }

        function vehicle_details($veh) {
            $this->db->from('gccasset.vehicles');
            $this->db->where('id', $veh);
            $query = $this->db->get();
            return $query->row();
        }

        function getTempRequest($id) {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "2", "dir" => "desc"));
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $rowData = array();
            $rowData = $this->get_all_post_temp($limit, $offset, $sortBy, $sortOrder, $id);
            $resultset["data"] = $rowData;
            return $resultset;
        }


        private function get_all_post_temp($limit = 10, $offset = 0, $sortBy, $sortOrder, $id) {
            $sql = "a.id, a.description,";

            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal_body_temp a");
            $this->db->where('user_id', $id);
            $this->db->limit($limit, $offset);

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

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

        function getContentRequest($id) {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "0", "dir" => "desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

            $rowData = $this->get_all_post_content($limit, $offset, $sortBy, $sortOrder, $id);
            $rowCount = $this->get_all_post_content_count($id);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }


        private function get_all_post_content($limit, $offset = 0, $sortBy, $sortOrder, $id =0) {
            $data = array();
            $sql = "a.id, a.description, a.remarks";

            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal_body a");
            $this->db->where('transmittal_id', $id);
            
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $rs->description = "&#8226; ". $rs->description;
                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }

            }

            return $data;
        }

        private function get_all_post_content_count($id = 0) {
            $sql = "a.id, a.description, a.remarks";

            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal_body a");
            $this->db->where('transmittal_id', $id);

            $query = $this->db->get();
            return $query->num_rows();
        }

        function getDaily() {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "1", "dir" => "desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;


            $rowCount = 0;
            $rowData = array();
            $rowData = $this->get_all_post_daily($limit, $offset, $sortBy, $sortOrder);
            $totalNotFiltered = $rowCount;


            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_post_daily($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {

            $check = date('Y-m-d');
            $sql = "a.id, a.reference_no, a.company_to,CONCAT(e.firstname,' ',e.lastname) AS requested_by,CONCAT(f.firstname,' ',f.lastname) AS ship_to";
            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join('gccmaster.tblemployees e', 'e.id = a.requested_by');
            $this->db->join('gccmaster.tblemployees f', 'f.id = a.ship_to');
            $this->db->like('created_dt', $check);
            $this->db->limit($limit, $offset);
            $i = $sortOrder[0]['column'];
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
            } else {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
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

        function getWeekly() {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "1", "dir" => "desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

            $rowCount = 0;
            $rowData = array();

            $rowData = $this->get_all_post_weekly($limit, $offset, $sortBy, $sortOrder);


            $totalNotFiltered = $rowCount;


            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_post_weekly($limit = 10, $offset = 0, $sortBy, $sortOrder) {

            $check = date('Y-m-d', strtotime("-7 days"));
            $sql = "a.id, a.reference_no, a.company_to,CONCAT(e.firstname,' ',e.lastname) AS requested_by,  f.firstname, f.lastname, f.middlename, f.suffix, a.ship_to";
            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join('gccmaster.tblemployees e', 'e.id = a.requested_by');
            $this->db->join('gccmaster.tblemployees f', 'f.id = a.ship_to');
            $this->db->where('created_dt >= ', $check);
            $this->db->limit($limit, $offset);

            $i = $sortOrder[0]['column'];
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
            } else {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if (is_numeric($rs->ship_to)) {
                        $tempRs = (array)$rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object)$fullname;
                        $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                        $rs->display_name = $rs->display_name;
                    } else {
                        $rs->display_name = $rs->ship_to;
                    }
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

        function m_get_transmittal_analytics_for_dashboard() {
            $this->db->select("a.status, COUNT(a.id) AS count");
            $this->db->from("gcceforms.transmittal a");
            $this->db->join("gcceforms.transmittal_body c", "a.id = c.transmittal_id", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            $this->db->join("gccmaster.tblemployees creator", "creator.id = a.created_by", "LEFT");
            $this->db->group_by("a.status");
            $this->db->order_by("FIELD(a.status, 'Pending', 'Approved', 'Disapproved', 'Received', 'Cancelled')");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $arrData[$key] = $rs;
                }

                return json_decode(json_encode($arrData));
            } else {
                return array();
            }
        }

        public function save_temp_content($data) {
            $this->db->insert($this->transmittal_body_temp, $data);
            return $this->db->insert_id();
        }
        
        public function getTransmittalContentTemp($id){
            return $this->db->get_where($this->transmittal_body_temp, array("id"=>$id))->row('description');
        }

        public function edit_temp($id) {
            $this->db->from($this->transmittal_body_temp);
            $this->db->where('id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function edit_content($id) {
            $this->db->from($this->transmittal_body);
            $this->db->where('id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_temp($where, $data) {
            $this->db->update($this->transmittal_body_temp, $data, $where);
            return $this->db->affected_rows();
        }

        public function update_content($where, $data) {
            $this->db->update($this->transmittal_body, $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_temp($id) {
            $this->db->where('id', $id);
            $this->db->delete($this->transmittal_body_temp);

            return $this->db->affected_rows();
        }

        public function delete_temp_all($id) {
            $this->db->where('user_id', $id);
            $this->db->delete($this->transmittal_body_temp);
        }

        public function delete_content($id) {
            $this->db->where('id', $id);
            $this->db->delete($this->transmittal_body);
        }

        public function delete_content_all($id) {
            $this->db->where('transmittal_id', $id);
            $this->db->delete($this->transmittal_body);
        }

        public function series($year, $month) {
            $this->db->select('ref_series');
            $this->db->from('gcceforms.transmittal');
            $this->db->where('ref_yr', $year);
            $this->db->where('ref_month', $month);
            $this->db->order_by('ref_series', 'asc');
            $query = $this->db->get();
            return $query->result();
        }

        function getTransmittalReference($id){
            return $this->db->get_where("gcceforms.transmittal", array("id"=>$id))->row('reference_no');
        }

        public function save_transmittal($data) {
            $this->db->insert('gcceforms.transmittal', $data);
            return $this->db->insert_id();
        }

        public function update_transmittal($where, $data) {
            $this->db->update('gcceforms.transmittal', $data, $where);
            return $this->db->affected_rows();
        }

        public function get_contents($id) {
            $this->db->from($this->transmittal_body_temp);
            $this->db->where('user_id', $id);
            $query = $this->db->get();
            return $query->result();
        }

        public function save_content($data) {
            $this->db->insert($this->transmittal_body, $data);
            return $this->db->insert_id();
        }

        public function transmittal_details($id) {
            $sql = "a.*,a.id, b.description AS company_from_d, 
                    c.description AS department_from_d, 
                    IF(comp.id IS NULL, a.company_to, comp.code) company, 
                    IF(dep.id IS NULL, a.department_to, dep.description) department";
            $this->db->select($sql);
            $this->db->from("gcceforms.transmittal a");
            $this->db->join('gcchris.tblcompanies b', 'b.id = a.company_from', "LEFT");
            $this->db->join('gcchris.tbldepartments c', 'c.id = a.department_from', "LEFT");
            $this->db->join("gccmaster.tblemployees d", "d.id = a.created_by", "LEFT");
            $this->db->join("gccmaster.tblemployees e", "e.id = a.ship_to", "LEFT");
            $this->db->join("gccmaster.tblemployees f", "f.id = a.requested_by", "LEFT");
            $this->db->join("gccmaster.tblemployees g", "g.id = a.last_edited_id", "LEFT");
            $this->db->join('gcchris.tblcompanies comp', 'comp.id = a.company_to', "LEFT");
            $this->db->join('gcchris.tbldepartments dep', 'dep.id = a.department_to', "LEFT");
            $this->db->where("a.id", $id);

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    // if (is_numeric($rs->company_from)) {
                    //     $rs->company_from = $rs->company_from_d;
                    // } else {
                    //     $rs->company_from = $rs->company_from;
                    // }

                    // if (is_numeric($rs->department_from)) {
                    //     $rs->department_from = $rs->department_from_d;
                    // } else {
                    //     $rs->department_from = $rs->department_from;
                    // }

                    // if (is_numeric($rs->requested_by)) {
                    //     $rs->requested_by = $this->getDisplayName($rs->requested_by);
                    // } else {
                    //     $rs->requested_by = $rs->requested_by;
                    // }

                    // if (is_numeric($rs->ship_to)) {
                    //     $rs->ship_to = $this->getDisplayName($rs->ship_to);
                    // } else {
                    //     $rs->ship_to = $rs->ship_to;
                    // }

                    // if (is_numeric($rs->created_by)) {
                    //     $rs->created_by = $this->getDisplayName($rs->created_by);
                    // } else {
                    //     $rs->created_by = $rs->created_by;
                    // }

                    // if (is_numeric($rs->approved_by)) {
                    //     $rs->approved_by = $this->getDisplayName($rs->approved_by);
                    // } else {
                    //     $rs->approved_by = $rs->approved_by;
                    // }

                    // if (is_numeric($rs->disapproved_by)) {
                    //     $rs->disapproved_by = $this->getDisplayName($rs->disapproved_by);
                    // } else {
                    //     $rs->disapproved_by = $rs->disapproved_by;
                    // }

                    // if (is_numeric($rs->received_by)) {
                    //     $rs->received_by = $this->getDisplayName($rs->received_by);
                    // } else {
                    //     $rs->received_by = $rs->received_by;
                    // }

                    // if (is_numeric($rs->cancelled_by)) {
                    //     $rs->cancelled_by = $this->getDisplayName($rs->cancelled_by);
                    // } else {
                    //     $rs->cancelled_by = $rs->cancelled_by;
                    // }

                    // if (is_numeric($rs->last_edited_by)) {
                    //     $rs->last_edited_by = $this->getDisplayName($rs->last_edited_by);
                    // } else {
                    //     $rs->last_edited_by = $rs->last_edited_by;
                    // }

                    $rs->company_from = (is_numeric($rs->company_from)) ? $rs->company_from_d : $rs->company_from;
                    $rs->department_from = (is_numeric($rs->department_from)) ? $rs->department_from_d : $rs->department_from;
                    $rs->received_remarks = ($rs->received_remarks) ? $rs->received_remarks : "N/A";
                    $rs->approve_remarks = ($rs->approve_remarks) ? $rs->approve_remarks : "N/A";
                    $rs->requested_by = (is_numeric($rs->requested_by)) ? $this->getDisplayName($rs->requested_by) : $rs->requested_by;
                    $rs->ship_to = (is_numeric($rs->ship_to)) ? $this->getDisplayName($rs->ship_to) : $rs->ship_to;
                    $rs->created_by = (is_numeric($rs->created_by)) ? $this->getDisplayName($rs->created_by) : $rs->created_by;
                    $rs->approved_by = (is_numeric($rs->approved_by)) ? $this->getDisplayName($rs->approved_by) : $rs->approved_by;
                    
                    $rs->approved_name = $rs->approved_by;
                    
                    $rs->disapproved_by = (is_numeric($rs->disapproved_by)) ? $this->getDisplayName($rs->disapproved_by) : $rs->disapproved_by;
                    $rs->received_by = (is_numeric($rs->received_by)) ? $this->getDisplayName($rs->received_by) : $rs->received_by;
                    $rs->cancelled_by = (is_numeric($rs->cancelled_by)) ? $this->getDisplayName($rs->cancelled_by) : $rs->cancelled_by;
                    $rs->last_edited_by = (is_numeric($rs->last_edited_by)) ? $this->getDisplayName($rs->last_edited_by) : $rs->last_edited_by;

                    $arrData[$key] = $rs;
                }
                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
                return $arrData[0];
            } else {
                return array();
            }
        }

        function getDisplayName($id) {
            $this->db->select('firstname, lastname, middlename, suffix, id');
            $this->db->from("gccmaster.tblemployees");
            $this->db->where('id', $id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $arrData[$key] = $rs;
                }
                return $arrData[0]->display_name;
            } else {
                return array();
            }
        }

        function tranmittal_details2($id) {
            $this->db->from('gcceforms.transmittal');
            $this->db->where('id', $id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $rs->company_to_desc = $rs->company_to . "\n" . $rs->department_to . "\n" . $rs->position_to;
                    $arrData[$key] = $rs;
                }
                return $arrData[0];
            } else {
                return array();
            }
        }

        function company_details($id) {
            $this->db->from('gcchris.tblcompanies');
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row();
        }

        function department_details($id) {
            $this->db->from('gcchris.tbldepartments');
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row();
        }

        function employee_details($id) {
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('id', $id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $arrData[$key] = $rs;
                }

                return $arrData[0]->display_name;
            } else {
                return array();
            }
        }


        function getTransmittalCreators() {
            $search = isset($_GET["q"]) ? $_GET["q"] : "";
            $this->db->like("IF(emp.id IS NULL, trans.created_by, CONCAT(emp.firstname, ' ', emp.lastname))", $search, "BOTH");
            $this->db->select("UCASE(IF(emp.id IS NULL, trans.created_by, emp.id)) `id`, UCASE(IF(emp.id IS NULL, trans.created_by, CONCAT(emp.firstname, ' ', emp.lastname))) `text`");
            $this->db->where("trans.created_by IS NOT NULL", NULL, FALSE);
            $this->db->where("trans.created_by !=", "");
            $this->db->group_by("IF(emp.id IS NULL, trans.created_by, emp.id)");
            $this->db->order_by("UCASE(IF(emp.id IS NULL, trans.created_by, CONCAT(emp.firstname, ' ', emp.lastname)))");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = trans.created_by", "LEFT");

            $q = $this->db->get("gcceforms.transmittal trans");
            $data = $q->result();
            $list = array();

            foreach ($data as $datum) {
                $exist = array_filter($list, function ($item) use ($datum) {
                    return str_replace(" ", "", $item->text) === str_replace(" ", "", $datum->text);
                });

                if (empty($exist)) {
                    array_push($list, $datum);
                }
            }

            return $list;
        }

        function exportData($export){
            if($export == 1){
                $this->core_layout->setEventLog("Transmittal Report - Export excel file of Transmittal Report Masterfile.","export", "success", "gcceforms", "user");
            }elseif($export == 2){
                $this->core_layout->setEventLog("Transmittal Report - Export csv file of Transmittal Report Masterfile.","export", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Transmittal Report - Export pdf file of Transmittal Report Masterfile.","export", "success", "gcceforms", "user");
            }
        }

        function exportDataArchived($export){
            if($export == 1){
                $this->core_layout->setEventLog("Archived Transmittal Report - Export excel file of Archived Transmittal Report Masterfile.","export", "success", "gcceforms", "user");
            }elseif($export == 2){
                $this->core_layout->setEventLog("Archived Transmittal Report - Export csv file of Archived Transmittal Report Masterfile.","export", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Archived Transmittal Report - Export pdf file of Archived Transmittal Report Masterfile.","export", "success", "gcceforms", "user");
            }
        }


    }