<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Loa_m extends CI_Model
    {
        protected $eformsTable = "gcceforms";
        private $current_actions =  array();
        private $user_data = array();
        public function __construct()
        {
            parent::__construct();
            $this->core_layout->setPrivilegeName("eforms_loa");
            $this->user_data = $this->session->userdata("logged_in"); 
            $this->current_action = $this->core_layout->getCurrentActions();
        }

        function getDatatableRequest(){
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"9", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
            $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
            $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
            $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
            $status = (isset($post['status']) && $post['status']) ? ucwords($post['status']) : null; //clicked in portal dashboard

            $view_own_request = (in_array("view_own_request", $this->current_action)) ? true : false;
            $view_own_dept = (in_array("view_by_dept", $this->current_action)) ? true : false;
            $view_by_company = (in_array("view_by_company", $this->current_action)) ? true : false;
            $companyDescription = null;

            if ($view_by_company) {
                $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
            }
            
            $rowData = $this->get_all_items($view_own_request,  $query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $status, $view_own_dept, $view_by_company, $companyDescription);
            $rowCount = $this->get_all_items_count($view_own_request,  $query_builder, $search, $status, $view_own_dept, $view_by_company, $companyDescription);
            
            // if (!$search) {
            //     $rowData = $this->get_all_post($view_own_request,  $query_builder, $limit, $offset, $sortBy, $sortOrder, $status, $view_own_dept);
            //     $rowCount = $this->get_all_post_count($view_own_request,  $query_builder, $status, $view_own_dept);
            // }

            if ($search) {
            //     $rowData = $this->get_searched_item($view_own_request,  $query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $status, $view_own_dept);
            //     $rowCount = $this->get_searched_item_count($view_own_request,  $query_builder, $search, $status, $view_own_dept);
                $this->core_layout->setEventLog("Search ".$search.".","search", "success", "gcceforms", "user");
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;
          // $resultset["action"] =  $this->core_layout->getCurrentActions();
           
            
            return $resultset;
        }

        function get_all_items($view, $query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null, $view_dept, $view_by_company = false, $companyDescription = null) {
            $data = array();
            $check = date("Y-m-d", strtotime("-1 year", time()));

            $filterFields = array("a.id", "a.status", "a.company", "a.department", "a.reference_no", "a.date_from", "a.date_to", "a.nature", "a.reason", "a.position", "b.firstname", "b.middlename", "b.lastname", "a.type");

            $sql = "a.id, a.status, a.company as file, a.company, a.department, TRIM(b.firstname) as firstname, TRIM(b.middlename) as middlename, TRIM(b.lastname) as lastname, b.suffix, a.position, a.nature, a.reason, a.date_from, a.date_to, a.reference_no, a.type";

            $this->db->select($sql);
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            $this->db->where('a.status != ', 'Cancelled');
            $this->db->where('DATE(a.date_from) >= ', $check);
            $this->db->from("gcceforms.loa a");

            if ($view && ($this->user_data['emp_id'] != 1)) {
                $this->db->where('a.employee', $this->user_data['emp_id']);
            }

            if ($view_dept && ($this->user_data['emp_id']!=1)) {
                $this->db->where('b.department_id', $this->user_data['department']);
            }

            if ($view_by_company) {
                $this->db->where('b.company_id', (int)$this->user_data['company']);

                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            }

            if ($query_builder) {
                $this->db->where($query_builder);
            }

            if ($status) {
                $this->db->where('a.status', $status);
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

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();

                foreach ($query->result() as $key => $rs) {
                    $rs->company = (is_numeric($rs->company)) ? $this->getCompany($rs->company) : $rs->company;
                    $rs->department = (is_numeric($rs->department)) ? $this->getDepartment($rs->department) : $rs->department;
                    $rs->position = (is_numeric($rs->position)) ? $this->getPosition($rs->position) : $rs->position;

                    $rs->file = $rs->company ? "<b>".$rs->company."</b><br>".$rs->department : '<b>No Company Name</b>';
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $rs->display_name = "<b>".$rs->display_name."</b><br>".$rs->position;
                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
            }

            return $data;
        }

        function get_all_items_count($view, $query_builder=null, $search = null, $status = null, $view_dept, $view_by_company = false, $companyDescription = null) {
            $check = date("Y-m-d", strtotime("-1 year", time()));

            $filterFields = array("a.id", "a.status", "a.company", "a.department", "a.reference_no", "a.date_from", "a.date_to", "a.nature", "a.reason", "a.position", "b.firstname", "b.middlename", "b.lastname", "a.type");

            $sql = "a.id, a.status, a.company, a.department, b.firstname, b.middlename, b.lastname, b.suffix, a.position, a.nature, a.reason, a.date_from, a.date_to, a.reference_no, a.type";

            $this->db->select($sql);
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            $this->db->where('a.status != ', 'Cancelled');
            $this->db->where('DATE(a.date_from) >= ', $check);
            $this->db->from("gcceforms.loa a");

            if ($view && ($this->user_data['emp_id'] != 1)) {
                $this->db->where('a.employee', $this->user_data['emp_id']);
            }

            if ($view_dept && ($this->user_data['emp_id']!=1)) {
                $this->db->where('b.department_id', $this->user_data['department']);
            }

            if ($view_by_company) {
                $this->db->where('b.company_id', (int)$this->user_data['company']);

                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            }

            if ($query_builder) {
                $this->db->where($query_builder);
            }

            if ($status) {
                $this->db->where('a.status', $status);
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

            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_post_countv1($view, $query_builder=null, $status = null, $view_dept) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $this->db->from("gcceforms.loa a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            if($view && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.employee', $this->user_data['emp_id']);
            }
            if($view_dept && ($this->user_data['emp_id']!=1)){
                $this->db->where('b.department_id', $this->user_data['department']);
            }
            $this->db->where('status != ', 'Cancelled');
            $this->db->where('date_from >= ', $check);

            if($query_builder){
                $this->db->where($query_builder);
            }
            if($status){
                $this->db->where('status', $status);
            }
            $query = $this->db->get();

            return $query->num_rows();
        }

        private function get_all_postv1($view, $query_builder=null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null, $view_dept) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $sql = "a.id, a.status, a.company, a.department, b.firstname, b.middlename, b.lastname, b.suffix, a.position, a.nature, a.reason, a.date_from, a.date_to, a.reference_no, a.type";

            $this->db->select($sql);
            $this->db->from("gcceforms.loa a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
            $this->db->where('status != ', 'Cancelled');
            $this->db->where('date_from >= ', $check);
            if($query_builder){
                $this->db->where($query_builder);
            }

            if($status){
                $this->db->where('status', $status);
            }
        
            if($view && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.employee', $this->user_data['emp_id']);
            }

            if($view_dept && ($this->user_data['emp_id']!=1)){
                $this->db->where('b.department_id', $this->user_data['department']);
            }
            
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if(is_numeric($rs->company)){
                        $rs->company = $this->getCompany($rs->company);
                      
                    }else{
                        $rs->company = $rs->company;
                    }

                    if(is_numeric($rs->department)){
                        $rs->department = $this->getDepartment($rs->department);
                      
                    }else{
                        $rs->department = $rs->department;
                    }
        
                    if(is_numeric($rs->position)){
                        $rs->position = $this->getPosition($rs->position);
                      
                    }else{
                        $rs->position = $rs->position;
                    }

                    $rs->file = "<b>".$rs->company."</b><br>".$rs->department;
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $rs->display_name = "<b>".$rs->display_name."</b><br>".$rs->position;
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

        private function get_searched_itemv1($view, $query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null, $view_dept) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.status", "a.company", "a.department", "a.reference_no", "a.date_from", "a.date_to", "a.nature", "a.reason", "a.position", "b.firstname", "b.middlename", "b.lastname", "a.type");
                $sql = "a.id, a.status, a.company, a.department, b.firstname, b.middlename, b.lastname, b.suffix, a.position, a.nature, a.reason, a.date_from, a.date_to, a.reference_no, a.type";
                $this->db->select($sql);
                $this->db->from("gcceforms.loa a");
                $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
                $this->db->where('status != ', 'Cancelled');
                $this->db->where('date_from >= ', $check);
                if($view && ($this->user_data['emp_id']!=1)){
                    $this->db->where('a.employee', $this->user_data['emp_id']);
                }

                if($view_dept && ($this->user_data['emp_id']!=1)){
                    $this->db->where('b.department_id', $this->user_data['department']);
                }

                if($query_builder){
                    $this->db->where($query_builder);
                }
                if($status){
                    $this->db->where('status', $status);
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
                if($limit != -1){
                    $this->db->limit($limit, $offset);
                }
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {

                    $arrData = array();
                    foreach ($query->result() as $key => $rs) {
                        if(is_numeric($rs->company)){
                            $rs->company = $this->getCompany($rs->company);
                          
                        }else{
                            $rs->company = $rs->company;
                        }
    
                        if(is_numeric($rs->department)){
                            $rs->department = $this->getDepartment($rs->department);
                          
                        }else{
                            $rs->department = $rs->department;
                        }
            
                        if(is_numeric($rs->position)){
                            $rs->position = $this->getPosition($rs->position);
                          
                        }else{
                            $rs->position = $rs->position;
                        }
    
                        $rs->file = "<b>".$rs->company."</b><br>".$rs->department;
                        $tempRs = (array) $rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object) $fullname;
                        $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                        $rs->display_name = "<b>".$rs->display_name."</b><br>".$rs->position;
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

        private function get_searched_item_countv1($view, $query_builder=null, $search = null, $status = null, $view_dept) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.status", "a.company", "a.department", "a.reference_no", "a.date_from", "a.date_to", "a.nature", "a.reason", "a.position", "b.firstname", "b.middlename", "b.lastname", "a.type");
                $sql = "a.id, a.status, CONCAT('<b>',a.company,'</b><br>',a.department) AS file, CONCAT('<b>',b.firstname,' ',b.middlename,' ',b.lastname,'</b><br>',a.position) AS name, a.nature, a.reason, a.date_from, a.date_to, a.reference_no, a.type";
                $this->db->select($sql);
                $this->db->from("gcceforms.loa a");
                $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
                if($view && ($this->user_data['emp_id']!=1)){
                    $this->db->where('a.employee', $this->user_data['emp_id']);
                }

                if($view_dept && ($this->user_data['emp_id']!=1)){
                    $this->db->where('b.department_id', $this->user_data['department']);
                }

                $this->db->where('status != ', 'Cancelled');
                $this->db->where('date_from >= ', $check);
                if($query_builder){
                    $this->db->where($query_builder);
                }
                if($status){
                    $this->db->where('status', $status);
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
                $query = $this->db->get();

                $rowCount = $query->num_rows();
            }

            return $rowCount;
        }

        function getCompany($id){
            $this->db->select("id, description");
            $this->db->from("gcchris.tblcompanies");
            $this->db->where("id", $id);
            $query = $this->db->get();
            $data = $query->row();
            return $data->description;
        }
    
        function getPosition($id){
            $this->db->select("id, name");
            $this->db->from("gcchris.tblposition");
            $this->db->where("id", $id);
            $query = $this->db->get();
            $data = $query->row();
            return $data->name;
        }
    
        function getDepartment($id){
            $this->db->select("id, description");
            $this->db->from("gcchris.tbldepartments");
            $this->db->where("id", $id);
            $query = $this->db->get();
            $data = $query->row();
            return $data->description;
        }
    

        function getArchiveRequest(){
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"8", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
            $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
            $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;

            $view_by_company = (in_array("view_by_company", $this->current_action)) ? true : false;
            $companyDescription = null;

            if ($view_by_company) {
                $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
            }
            
            $rowData = $this->get_all_archive_items($search, $limit, $offset, $sortBy, $sortOrder, $view_by_company, $companyDescription);
            $rowCount = $this->get_all_archive_items_count($search, $view_by_company, $companyDescription);
            // if (!$search) {
            //     $rowData = $this->get_all_archive($limit, $offset, $sortBy, $sortOrder);
            //     $rowCount = $this->get_all_archive_count();
            // }

            // if ($search) {
            //     $rowData = $this->get_searched_archive_item($search, $limit, $offset, $sortBy, $sortOrder);
            //     $rowCount = $this->get_searched_archive_item_count($search);
            // }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function get_all_archive_items($search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $view_by_company = false, $companyDescription = null) {
            $check = date("Y-m-d", strtotime("-1 year", time()));
            $data = array();

            $filterFields = array("a.id", "a.status", "a.company", "a.department", "a.reference_no", "DATE(a.date_from)", "DATE(a.date_to)", "a.nature", "a.reason", "a.position", "b.firstname", "b.middlename", "b.lastname", "a.type");
            $sql = "a.id, a.status, CONCAT('<b>',a.company,'</b><br>',a.department) AS file, b.firstname, b.middlename, b.lastname, b.suffix, a.position, a.nature, a.reason, a.date_from, a.date_to, a.reference_no, a.type";
            $this->db->select($sql);
            $this->db->from("gcceforms.loa a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");

            if ($view_by_company) {
                $this->db->where('b.company_id', $this->user_data['company']);

                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            }
            
            $this->db->group_start();
                $this->db->where('a.status', 'Cancelled');
                $this->db->or_where('DATE(a.date_from) <= ', $check);
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

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            // var_dump($this->db->last_query());

            if ($query->num_rows() > 0) {
                $arrData = array();

                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $rs->display_name = "<b>".$rs->display_name."</b><br>".$rs->position;
                    $arrData[$key] = $rs;
                }

                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
            }

            return $data;
        }

        function get_all_archive_items_count($search = null, $view_by_company = false, $companyDescription = null) {
            $check = date("Y-m-d", strtotime("-1 year", time()));
            $data = array();

            $filterFields = array("a.id", "a.status", "a.company", "a.department", "a.reference_no", "DATE(a.date_from)", "DATE(a.date_to)", "a.nature", "a.reason", "a.position", "b.firstname", "b.middlename", "b.lastname", "a.type");
            $sql = "a.id, a.status, CONCAT('<b>',a.company,'</b><br>',a.department) AS file, b.firstname, b.middlename, b.lastname, b.suffix, a.position, a.nature, a.reason, a.date_from, a.date_to, a.reference_no, a.type";
            $this->db->select($sql);
            $this->db->from("gcceforms.loa a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");

            if ($view_by_company) {
                $this->db->where('b.company_id', $this->user_data['company']);

                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            }
            
            $this->db->group_start();
                $this->db->where('a.status', 'Cancelled');
                $this->db->or_where('DATE(a.date_from) <= ', $check);
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
            $this->db->from("gcceforms.loa");
            $this->db->where('date_from <= ', $check);
            $this->db->or_where_in('status', 'Cancelled');
            $query = $this->db->get();

            return $query->num_rows();
        }

        private function get_all_archive($limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $sql = "a.id, a.status, CONCAT('<b>',a.company,'</b><br>',a.department) AS file, b.firstname, b.middlename, b.lastname, b.suffix, a.position, a.nature, a.reason, a.date_from, a.date_to, a.reference_no, a.type";

            $this->db->select($sql);
            $this->db->from("gcceforms.loa a");
            $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");

            $this->db->where('a.date_from <= ', $check);
            $this->db->or_where_in('a.status', 'Cancelled');
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $rs->display_name = "<b>".$rs->display_name."</b><br>".$rs->position;
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


        private function get_searched_archive_item($search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.status", "a.company", "a.department", "a.reference_no", "a.date_from", "a.date_to", "a.nature", "a.reason", "a.position", "b.firstname", "b.middlename", "b.lastname", "a.type");
                $sql = "a.id, a.status, CONCAT('<b>',a.company,'</b><br>',a.department) AS file, b.firstname, b.middlename, b.lastname, b.suffix, a.position, a.nature, a.reason, a.date_from, a.date_to, a.reference_no, a.type";
                $this->db->select($sql);
                $this->db->from("gcceforms.loa a");
                $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
                // $this->db->where('a.date_from <= ', $check);
                // $this->db->or_where_in('a.status', 'Cancelled');
                $this->db->where("(a.status='Cancelled' OR a.date_from<='$check')");
                $this->db->group_start();

                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
                if($limit != -1){
                    $this->db->limit($limit, $offset);
                }
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {

                    $arrData = array();
                    foreach ($query->result() as $key => $rs) {
                        $tempRs = (array) $rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object) $fullname;
                        $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                        $rs->display_name = "<b>".$rs->display_name."</b><br>".$rs->position;
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

        private function get_searched_archive_item_count($search = null) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.status", "a.company", "a.department", "a.reference_no", "a.date_from", "a.date_to", "a.nature", "a.reason", "a.position", "b.firstname", "b.middlename", "b.lastname", "a.type");
                $sql = "a.id, a.status, CONCAT('<b>',a.company,'</b><br>',a.department) AS file, CONCAT('<b>',b.firstname,' ',b.middlename,' ',b.lastname,'</b><br>',a.position) AS name, a.nature, a.reason, a.date_from, a.date_to, a.reference_no, a.type";
                $this->db->select($sql);
                $this->db->from("gcceforms.loa a");
                $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");

                // $this->db->where('a.date_from <= ', $check);
                // $this->db->or_where_in('a.status', 'Cancelled');
                $this->db->where("(a.status='Cancelled' OR a.date_from<='$check')");
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

        function getEmployeeCollection() {
            $id = $this->user_data['emp_id'];
            $get = $this->input->get();
            $resultarray = array();

            $view_by_company = (in_array("view_by_company", $this->current_action)) ? true : false;

            $this->db->select('id, firstname, lastname, middlename, suffix');
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('gccmaster.tblemployees.employee_status', 'Active');
            $this->db->order_by('firstname', 'asc');

            if ($view_by_company) {
                $this->db->where('company_id', $this->user_data['company']);
            }

            if (isset($get['q']) && $get['q']) {
                $this->db->like('firstname', $get['q'], 'both');
                $this->db->or_like('middlename', $get['q'], 'both');
                $this->db->or_like('lastname', $get['q'], 'both');
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $empName = $this->core_layout->getDisplayName($_query);
                    $displayName = $empName["display_name_1"];

                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $displayName;
                    $resultarray[] = $data;
                }
            }
            // if (isset($get['q'])) {
            //     $this->db->from('gccmaster.tblemployees');
            //     $this->db->where('gccmaster.tblemployees.employee_status', 'Active');
            //     $this->db->order_by('firstname', 'asc');
            //     $this->db->like('firstname', $get['q']);
            //     $this->db->or_like('middlename', $get['q']);
            //     $this->db->or_like('lastname', $get['q']);
            //     $query = $this->db->get();

            //     if ($query->num_rows() > 0) {
            //         foreach ($query->result_array() as $_query) {
            //             $empName = $this->core_layout->getDisplayName($_query);
            //             $displayName = $empName["display_name_1"];

            //             $data = array();
            //             $data["id"] = $_query["id"];
            //             $data["text"] = $displayName;
            //             /*** $data["text"] = $_query["firstname"]." ".$_query["middlename"]." ".$_query["lastname"]; ***/
            //             $resultarray[] = $data;
            //         }
            //     }
            // } else {
            //     $this->db->from('gccmaster.tblemployees');
            //     $this->db->where('gccmaster.tblemployees.employee_status', 'Active');
            //     $this->db->order_by('firstname', 'asc');
            //     $query = $this->db->get();
            //     if ($query->num_rows() > 0) {
            //         foreach ($query->result_array() as $_query) {
            //             $empName = $this->core_layout->getDisplayName($_query);
            //             $displayName = $empName["display_name_1"];

            //             $data = array();
            //             $data["id"] = $_query["id"];
            //             $data["text"] = $displayName;
            //             /*** $data["text"] = $_query["firstname"]." ".$_query["middlename"]." ".$_query["lastname"]; ***/
            //             $resultarray[] = $data;
            //         }
            //     }
            // }

            return array("results" => $resultarray);
        }

        function getUserEmpData() {
            $get = $this->input->get();
            $id = $this->user_data['emp_id'];
            $resultarray = array();

            $this->db->select('id');
            $this->db->from("gcchris.tbldepartments");
            $query_dep = $this->db->where(array("head_id" => $id, "is_archived" => 0))->get();

            $dept_head = $query_dep->num_rows();
            $isDepartmentHead = $dept_head > 0 ? true: false;
            $view_own_request = (in_array("view_own_request", $this->current_action)) ? true : false;
            $allowSearchEmployee = ($isDepartmentHead == true || $view_own_request == false)? true: false;

            $this->db->reset_query();
            
            $this->db->select('id, firstname, lastname, middlename, suffix');
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('employee_status', 'Active');
            $this->db->where('id', $id);
            $query = $this->db->get();

            $resultarray["allow_search_employee"] = $allowSearchEmployee;
            
            if($query->num_rows() == 1){
                $tempRow = $query->row();
                $tempRowArray = $query->row_array();
                $empName = (object) $this->core_layout->getDisplayName($tempRowArray);
                $empName = (isset($empName->display_name_1) && $empName->display_name_1)? $empName->display_name_1: "No assigned name";

                $resultarray["response"] = true;
                $resultarray["id"] = $tempRow->id;
                $resultarray["text"] = $empName;
            }else{
                $resultarray["response"] = false;
            }

            return $resultarray;
        }

        function emp_details($emp){
            $resultset = array();
            // $this->db->select('a.*, b.company_address');
            $this->db->select('a.id, a.firstname, a.lastname, a.middlename, a.suffix, a.department_id, a.company_id, a.position, b.company_address');
            $this->db->from('gccmaster.tblemployees a');
            $this->db->join('gcchris.tblcompanies b', 'b.id = a.company_id OR b.description = a.company_id OR b.code = a.company_id', 'left');
            $this->db->where('a.employee_status', 'Active');
            $this->db->where('a.id', $emp);
            $query = $this->db->get();

            if ($query->num_rows() == 1) {
                $tempRow = $query->row();

                $tempRow->company_id = is_numeric($tempRow->company_id) ? $this->getCompany($tempRow->company_id) : $tempRow->company_id;
                $tempRow->department_id = is_numeric($tempRow->department_id) ? $this->getDepartment($tempRow->department_id) : $tempRow->department_id;
                $tempRow->position = is_numeric($tempRow->position) ? $this->getPosition($tempRow->position) : $tempRow->position;
                // if(is_numeric($tempRow->company_id)){ $tempRow->company_id = $this->getCompany($tempRow->company_id); }
                // else{ $tempRow->company_id = $tempRow->company_id; }

                // if(is_numeric($tempRow->department_id)){ $tempRow->department_id = $this->getDepartment($tempRow->department_id);  }
                // else{ $tempRow->department_id = $tempRow->department_id; }
    
                // if(is_numeric($tempRow->position)){ $tempRow->position = $this->getPosition($tempRow->position); }
                // else{ $tempRow->position = $tempRow->position; }
                $resultset["response"] = true;
                $resultset["row"] = $tempRow;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
            //return $query->row();
        }

        public function save($data)
        {
            $this->db->insert('gcceforms.loa', $data);
            return $this->db->insert_id();
        }

        public function update($where, $data)
        {
            $this->db->update('gcceforms.loa', $data, $where);
            return $this->db->affected_rows();
        }

        public function series($year, $month)
        {
            $this->db->select('ref_series');
            $this->db->from('gcceforms.loa');
            $this->db->where('ref_yr', $year);
            $this->db->where('ref_month', $month);
            $this->db->order_by('ref_series', 'asc');
            $query = $this->db->get();
            return $query->result();
        }

        function getPrevious($emp)
        {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"3", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
            $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
            $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
            $excluded = (isset($post['excluded_id']) && $post['excluded_id'])? $post['excluded_id']: 0;

            $rowCount = 0;
            $rowData = array();

            $rowData = $this->get_all_post_previous($limit, $offset, $sortBy, $sortOrder, $emp, $excluded);


            $totalNotFiltered = $rowCount;


            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_post_previous($limit = 10, $offset = 0, $sortBy, $sortOrder, $emp, $excluded = 0)
        {
            $sql = "a.id, a.nature, a.status, a.type, a.date_from, a.date_to";

            $this->db->select($sql);
            $this->db->from("gcceforms.loa a");
            $this->db->where('employee', $emp);

            if ($excluded) {
                $this->db->where('a.id !=', $excluded);
            }

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

        public function loa_details($id, $type = null) {
            $this->db->from('gcceforms.loa');
            $this->db->where('id', $id);
            $query = $this->db->get();
            if($query->num_rows() > 0){
                $arrData = array();

                foreach($query->result() as $key => $rs){

                    $rs->company = is_numeric($rs->company) ? $this->getCompany($rs->company) : $rs->company;
                    $rs->department = is_numeric($rs->department) ? $this->getDepartment($rs->department) : $rs->department;
                    $rs->position = is_numeric($rs->position) ? $this->getPosition($rs->position) : $rs->position;

                    if ($type == 'edit') { 
                        $rs->phone = strlen($rs->phone) == 11 ? ltrim($rs->phone, '09') : $rs->phone;
                    }
                    // if(is_numeric($rs->company)){
                    //     $rs->company = $this->getCompany($rs->company);
                      
                    // }else{
                    //     $rs->company = $rs->company;
                    // }

                    // if(is_numeric($rs->department)){
                    //     $rs->department = $this->getDepartment($rs->department);
                      
                    // }else{
                    //     $rs->department = $rs->department;
                    // }
        
                    // if(is_numeric($rs->position)){
                    //     $rs->position = $this->getPosition($rs->position);
                      
                    // }else{
                    //     $rs->position = $rs->position;
                    // }

                    $rs->file = "<b>".$rs->company."</b><br>".$rs->department;
                    $arrData[$key] = $rs;
                }
                return $arrData[0];
            }else{
                return array();
            }
        }

        function employee_details($id) {

            $this->db->select('id, firstname, lastname, middlename, suffix');
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('id', $id);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach($query->result() as $key => $rs){  
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $arrData[$key] = $rs;
                
                }
                return $arrData[0];
            }else{
                return array();
            }
        }

        function m_get_loa_analytics_for_dashboard() {
            
            $this->db->select("a.status, COUNT(a.id) AS count");
            $this->db->from("gcceforms.loa a");
            $this->db->group_by("a.status");
            $query = $this->db->get()->result();
            $result = json_decode(json_encode($query));

            $data = array($result[4], $result[3], $result[0], $result[2], $result[1]);
            $results = json_decode(json_encode($data));
            return $results;
        }

        function m_get_loa_for_today() {
            $pagination_data = $this->parseFormDataToObject($this->input->post());
            $length = $pagination_data->length;
            $start = $pagination_data->start;
            $search = $pagination_data->search->value;
            $order_field_idx = $pagination_data->order[0]->column;
            $order_direction = $pagination_data->order[0]->dir;
            $order_column = $pagination_data->columns[$order_field_idx]->data;

            $date = date('Y-m-d');
            $criteria = "eforms_loa.status = 'Approved' AND ((DATE(eforms_loa.date_from) >= '$date' AND DATE(eforms_loa.date_from) <= '$date') OR 
            DATE(eforms_loa.date_to) >= '$date' AND DATE(eforms_loa.date_to) <= '$date')";

            $this->db->select('CONCAT(master_emp.firstname," ", master_emp.middlename," ", master_emp.lastname) employee_name, eforms_loa.*');
            $this->db->join('gccmaster.tblemployees as master_emp', 'master_emp.id = eforms_loa.employee');
            $this->db->where($criteria);
            $this->db->like('employee', $search, 'both');
            if ($length > -1) {
                $this->db->limit($length, $start);
            }

            $data['data'] = $this->db
                ->order_by($order_column, $order_direction)
                ->get("gcceforms.loa eforms_loa")->result();
            $data['recordsFiltered'] = $this->get_count("gcceforms.loa eforms_loa", $criteria);
            $data['recordsTotal'] = $this->get_count("gcceforms.loa eforms_loa", $criteria);

            return $data;
        }

        function m_get_loa_for_the_week()
        {
            $pagination_data = $this->parseFormDataToObject($this->input->post());
            $length = $pagination_data->length;
            $start = $pagination_data->start;
            $search = $pagination_data->search->value;
            $order_field_idx = $pagination_data->order[0]->column;
            $order_direction = $pagination_data->order[0]->dir;
            $order_column = $pagination_data->columns[$order_field_idx]->data;

            $date = date('Y-m-d');
            $criteria = "status ='Approved' AND YEARWEEK(date_from) = YEARWEEK('$date') OR YEARWEEK(date_to) = YEARWEEK('$date')";

            $this->db->select('CONCAT(master_emp.firstname," ", master_emp.middlename," ", master_emp.lastname) employee_name, eforms_loa.*');
            $this->db->join('gccmaster.tblemployees as master_emp', 'master_emp.id = eforms_loa.employee');
            $this->db->where($criteria);
            $this->db->like('employee', $search, 'both');
            if ($length > -1) {
                $this->db->limit($length, $start);
            }

            $data['data'] = $this->db
                ->order_by($order_column, $order_direction)
                ->get("gcceforms.loa eforms_loa")
                ->result();
            $data['recordsFiltered'] = $this->get_count("gcceforms.loa eforms_loa", $criteria);
            $data['recordsTotal'] = $this->get_count("gcceforms.loa eforms_loa", $criteria);

            return $data;
        }

        private function get_count($table, $criteria)
        {
            $this->db->where($criteria);
            return $this->db->count_all_results($table);
        }

        /* function to converted $this->input->post() into STD Object */
        private function parseFormDataToObject($data)
        {
            $_data = json_encode($data, true);
            return json_decode($_data);
        }

        function generateDailyLoaSummary($date=null){
            $currentDate = ($date)? date("Y-m-d", strtotime($date)): date("Y-m-d");
    
            $sqlSelect = "a.*, b.lastname, b.firstname, b.middlename, b.suffix";
            $this->db->select($sqlSelect);
            $this->db->from("gcceforms.loa a");
            $this->db->join("gccmaster.tblemployees b", "b.id = a.employee", "LEFT");
            $this->db->like("a.created_dt", $currentDate);
            $this->db->where("a.status !=", "Cancelled");
            $this->db->where("b.employee_status", "Active");
            $query = $this->db->get();
            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $rs){
                    $tempRs = (array)$rs;

                    $displayName = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $displayName;
                    $rs->employee_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
    
                    $tempDateTime = $this->generateDateTimeLoa($rs);
                    $rs->datetime = $tempDateTime;
                    $arrData[] = $rs;
                }
                return $arrData;
            }else{
                return false;
            }
        }
    
        function generateDailyLoaMorningSummary($date=null, $limitDate=null){
            $currentDate = ($date)? date("Y-m-d", strtotime($date)): date("Y-m-d");
            $currentDatex = date("Y-m-01", strtotime("-1 month", strtotime($currentDate)));
    
            $sqlSelect = "a.*, b.lastname, b.firstname, b.middlename, b.suffix";
            $this->db->select($sqlSelect);
            $this->db->from("gcceforms.loa a");
            $this->db->join("gccmaster.tblemployees b", "b.id = a.employee", "LEFT");
            $this->db->where("a.date_from >=", $currentDatex);
            $this->db->where("a.status !=", "Cancelled");
            $this->db->where("b.employee_status", "Active");
            $query = $this->db->get();
            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $rs){
                    $allowInsert = false;
                    $tempRs = (array)$rs;

                    $displayName = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $displayName;
                    $rs->employee_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
    
                    $tempDateFrom = date("Y-m-d", strtotime($rs->date_from));
                    $tempDateTo = date("Y-m-d", strtotime($rs->date_to));
    
                    if($rs->type != "3" && ($currentDate >= $tempDateFrom  && $currentDate <= $tempDateTo)){ $allowInsert = true; }
                    if($rs->type == "3" && $tempDateFrom == $currentDate){	$allowInsert = true; }
    
                    $tempDateTime = $this->generateDateTimeLoa($rs);
                    $rs->datetime = $tempDateTime;
    
                    if($allowInsert){
                        $arrData[] = $rs;
                    }
                }
                return $arrData;
            }else{
                return false;
            }
        }
    
        function generateDailyApprovedLoaSummary($date=null, $limitDate=null){
            $currentDate = ($date)? date("Y-m-d", strtotime($date)): date("Y-m-d");
            $currentDatex = date("Y-m-01", strtotime("-1 month", strtotime($currentDate)));
    
            $sqlSelect = "a.*, b.lastname, b.firstname, b.middlename, b.suffix";
            $this->db->select($sqlSelect);
            $this->db->from("gcceforms.loa a");
            $this->db->join("gccmaster.tblemployees b", "b.id = a.employee", "LEFT");
            $this->db->where("a.date_from >=", $currentDatex);
            $this->db->where("a.status", "Approved");
            $this->db->where("b.employee_status", "Active");
            $query = $this->db->get();
            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $rs){
                    $allowInsert = false;
                    $tempRs = (array)$rs;
                    $displayName = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $displayName;
                    $rs->employee_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
    
                    $tempDateFrom = date("Y-m-d", strtotime($rs->date_from));
                    $tempDateTo = date("Y-m-d", strtotime($rs->date_to));
    
                    if($rs->type != "3" && ($currentDate >= $tempDateFrom  && $currentDate <= $tempDateTo)){ $allowInsert = true; }
                    if($rs->type == "3" && $tempDateFrom == $currentDate){	$allowInsert = true; }
    
                    $tempDateTime = $this->generateDateTimeLoa($rs);
                    $rs->datetime = $tempDateTime;
    
                    if($allowInsert){
                        $arrData[] = $rs;
                    }
                }
                return $arrData;
            }else{
                return false;
            }
        }
    
        function generateDateTimeLoa($data=array()){
            $dateTime = "---";
    
            if ($data->type == "1") {
                $x = explode(' ', $data->date_from);
                $y = explode(' ', $data->date_to);
                $dateTime = date("M d, Y", strtotime($x[0])).' '.date("g:i A", strtotime($x[1])).' - '.date("g:i A", strtotime($y[1]));
            } else if ($data->type == "2") {
                $ampm = "PM";
                if (substr($data->date_from, 11, 2) == "8") { $ampm = "AM"; }
                $dateTime = date("M d, Y", strtotime(substr($data->date_from, 0, -9))).' '.$ampm;
            } else if ($data->type == "3") {
                $dateTime = date("M d, Y", strtotime(substr($data->date_from, 0, -9)));
            } else if ($data->type == "4") {
                $x = explode(' ', $data->date_from);
                $y = explode(' ', $data->date_to);
                $dateTime = date("M d, Y", strtotime($x[0])).' '.date("g:i A", strtotime($x[1])).' - '.date("M d, Y", strtotime($y[0])).' '.date("g:i A", strtotime($y[1]));
            }
    
            return $dateTime;
        }

        public function telegram_config_if_exist($module, $data){
            $this->db->where("module","loa");
            $this->db->order_by("created_at","DESC");
            $telegram_details = $this->db->get("gcceforms.telegram_config");
            $details = $telegram_details->row();
            $count = $telegram_details->num_rows();
            if($data == 'count'){
                return $count;
            }else{
                return $details;
            }
        }
    
        public function telegram($msg) {
            $data = $this->telegram_config_if_exist('loa', 'data');
    
            $telegrambot=$data->telegram_bot_token;
            $telegramchatid= $data->chat_id;
            $url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';$data=array('chat_id'=>$telegramchatid,'text'=>$msg,'parse_mode'=>'html');
            $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),'ignore_errors'=>true),);
            $context=stream_context_create($options);
            $result=file_get_contents($url,false,$context);
            return $result;
        }

        public function telegram_dept_heads($msg,$dept_head_chat_id) {
            $telegram_data = $this->telegram_config_if_exist('loa', 'data');

            $telegramchatid= $dept_head_chat_id;
            $url='https://api.telegram.org/bot'.$telegram_data->telegram_bot_token.'/sendMessage';$data=array('chat_id'=>$telegramchatid,'text'=>$msg,'parse_mode'=>'html');
            $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),'ignore_errors'=>true),);
            $context=stream_context_create($options);
            $result=file_get_contents($url,false,$context);
            return $result;
        }

        public function getTelegramId($department){
            $department_head = $this->db->get_where("gcchris.tbldepartments",array("description"=>$department))->row("head_id");
            $data = $this->db->get_where("gccmaster.tblusers", array("emp_id"=>$department_head))->row("telegram_chat_id");
            return $data;
        }

        public function getEmpTelegramId($id){
            $data = $this->db->get_where("gccmaster.tblusers", array("emp_id"=>$id))->row("telegram_chat_id");
            return $data;
        }
        
    
        public function addTelegramConfig(){
            $post = $this->input->post();
            $data = array(
                "chat_id"=>$post['chat_id'],
                "telegram_bot_token"=>$post['telegram_bot_token'],
                "module"=>$post['module'],
                "created_at"=>date("Y-m-d H:i:s")
            );
            if($post['id'] == ""){
                $result = $this->db->insert('gcceforms.telegram_config', $data);
                if($result){
                    $message = "Add new telegram configuration.";
                    $user_action = "add";
                    $type = "success";
                    $database = $this->eformsTable;
                    $table = "user";
                }else{
                    $message = "Failed adding telegram configuration.";
                    $user_action = "add";
                    $type = "error";
                    $database = $this->eformsTable;
                    $table = "system";
                }
            }else{
                $result = $this->db->update('gcceforms.telegram_config', $data, array("id"=>$post['id']));
                if($result){
                    $message = "Update telegram configuration.";
                    $user_action = "update";
                    $type = "success";
                    $database = $this->eformsTable;
                    $table = "user";
                }else{
                    $message = "Failed update telegram configuration.";
                    $user_action = "update";
                    $type = "error";
                    $database = $this->eformsTable;
                    $table = "system";
                }
            }
            $this->core_layout->setEventLog($message, $user_action, $type, $database, $table);

            return $result;
        }
    
        public function loadTelegramConfig(){
            $result = array();
            $post = $this->input->post();
            
            $this->db->where("module", $post['module']);
            $this->db->order_by("created_at", "DESC");
            $data = $this->db->get("gcceforms.telegram_config");
            $x = $data->row();
            $count = $data->num_rows();
            if($count > 0){
                $result['chat_id'] = $x->chat_id;
                $result['telegram_bot_token'] = $x->telegram_bot_token;
                $result['id'] = $x->id;
            }
    
            return $result;
        }
        function getEmpName($id){
            $this->db->select("id, firstname, middlename, lastname, suffix");
            $this->db->from("gccmaster.tblemployees");
            $this->db->where("id", $id);
            $this->db->limit(1);
    
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
                return "";
            }
    
        }
        public function check_reason($reason=""){
            if ($reason!="") {
                $this->db->select("*");
                $this->db->from("gcceforms.loa_invalid_reason");
                $this->db->where('status', 1);
                $this->db->like('reason', $reason, 'both');
                $query = $this->db->get();
                if($query->result()){ return $query->result(); }
            }
        }
        public function insert_registry($ajax_data){
            return $this->db->insert('gcceforms.loa_invalid_reason', $ajax_data);
        }

        public function edit_registry($id){
            $this->db->select("*");
            $this->db->from("gcceforms.loa_invalid_reason");
            $this->db->where("id", $id);
            $query = $this->db->get();
            if($query->result()){ return $query->row(); }
        }

        public function update_registry($data,$id){   
            $this->db->where('id', $id);
            return $this->db->update('gcceforms.loa_invalid_reason',$data);
        }

        function exportData($export){
            if($export == 1){
                $this->core_layout->setEventLog("Export excel file of LOA Masterfile.","export", "success", "gcceforms", "user");
            }elseif($export == 2){
                $this->core_layout->setEventLog("Export csv file of LOA Masterfile.","export", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Export pdf file of LOA Masterfile.","export", "success", "gcceforms", "user");
            }
        }

        function exportDataArchive($export){
            if($export == 1){
                $this->core_layout->setEventLog("Export excel file of Archived LOA Masterfile.","export", "success", "gcceforms", "user");
            }elseif($export == 2){
                $this->core_layout->setEventLog("Export csv file of LOA Archived Masterfile.","export", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Export pdf file of LOA Archived Masterfile.","export", "success", "gcceforms", "user");
            }
        }
    }