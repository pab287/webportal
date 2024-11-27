<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Department_m extends CI_Model{
        protected $tblPolicy = 'gccppm.tblpolicy';
        protected $tblDocument = 'gccppm.tblpolicy_documents';
        protected $tblCategory = 'gccppm.tblcategory';
        protected $archivedTable = "gccmaster.archived_items";
        protected $tblCompany = 'gcchris.tblcompanies';
        protected $tblDepartment = 'gcchris.tbldepartments';
        protected $tblVersion = 'gccppm.tbldocument_version';
        protected $tblHistory = 'gccppm.tbldocument_history';
        protected $tblEmployees = 'gccmaster.tblemployees';
        protected $tblcomp = 'gccppm.tblpolicy_companies';
        protected $tbldept = 'gccppm.tblpolicy_departments';

        private $user_data = array();

        function __construct(){
            parent::__construct();
            $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->user_data["username"];
        }

        function get_all_department_in_policy(){
            $result = array();
            $arrData = array();

            $query = $this->db->select('a.department_id')
                ->from($this->tbldept.' as a')
                ->join($this->tblPolicy.' as b', 'b.id = a.policy_id', 'LEFT')
                ->where('b.is_archived', 0)
                ->group_by('a.department_id')
                ->get();

            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){
                    $department = strtoupper($this->get_department($rs->department_id));
                    $rs->department = $department;

                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            return array('data' => $result);
        }

        // function get_all_deparment_in_policy(){
        //     $result = array();
        //     $arrData = array();
        //     $_tempDept = array();

        //     $this->db->select('departments')
        //         ->from($this->tblPolicy)
        //         ->where('is_archived', 0)
        //         ->order_by('id', 'ASC');
        //     $query = $this->db->get();

        //     if($query->num_rows() > 0){
        //         foreach($query->result() as $key => $rs){
        //             $departments = @unserialize($rs->departments);

        //             foreach($departments as $key => $department){
        //                 array_push($_tempDept, $department);
        //                 // $_tempDept[$key] = $department;
        //             }
        //         }

        //         // $merged = array_unique($_tempDept);
                
        //         // $count = 0;
        //         foreach($_tempDept as $key => $row){
        //             $arrData[$key] = $this->get_department($row);
        //             // $arrData[$count++] = $this->get_department($row);
        //         }

        //         foreach ($arrData as $k => $v) {
        //             $result[] = $v;
        //         }

        //         return array('data' => array_unique($result));
        //     }
        // }

        function get_department($id){
            $code = '';
            $query = $this->db->select('id, code')
                ->from($this->tblDepartment)
                ->where('id', $id)
                ->get()->row();

            if(isset($query->code) && $query->code){
                $code = $query->code;
            }else{
                $code = 'No Department Code';
            }

            return $code;
        }

        function getPolicyDatatableRequest(){
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"0", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $departmentId = (isset($post["department"]) && $post["department"]) ? $post["department"] : 1;
            $builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql']) ? $post["query_builder"]['sql'] : null;
            $advance = (isset($post["advance_search"]) && $post["advance_search"]) ? $post["advance_search"] : null;

            $rowCount = 0;
            $rowData = array();

            $rowData = $this->get_item($limit, $offset, $sortBy, $sortOrder, $search, $departmentId, $builder, $advance);
            $rowCount = $this->get_item_count($search, $departmentId, $builder, $advance);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function get_item($limit, $offset, $sortBy, $sortOrder, $search = null, $deptId, $builder = null, $advance = null){
            $filterFields = array('a.title', 'a.scope', 'a.objective', 'a.effective_date', 'b.name', 'a.document_no', 'a.ref_code', 'a.ref_year', 'a.ref_series', 'a.ref_department');
            $resultset = array();
            $arrData = array();

            $sql = "a.is_archived, a.*, b.name, b.id as catId,
                TRIM(CONCAT(
                        c.firstname,
                        ' ',
                        CASE WHEN c.middlename != 'N/A' AND c.middlename != 'NONE' AND c.middlename != '' AND c.middlename IS NOT NULL THEN CONCAT(SUBSTR(c.middlename, 1, 1),
                        '.') ELSE ''
                    END, ' ', c.lastname, ' ',
                    CASE WHEN c.suffix != 'N/A' AND c.suffix != 'NONE' AND c.suffix != '' AND c.suffix IS NOT NULL THEN CONCAT(' ', c.suffix) ELSE '' END
                )) AS `author`, TRIM(CONCAT(
                        d.firstname,
                        ' ',
                        CASE WHEN d.middlename != 'N/A' AND d.middlename != 'NONE' AND d.middlename != '' AND d.middlename IS NOT NULL THEN CONCAT(SUBSTR(d.middlename, 1, 1),
                        '.') ELSE ''
                    END, ' ', d.lastname, ' ',
                    CASE WHEN d.suffix != 'N/A' AND d.suffix != 'NONE' AND d.suffix != '' AND d.suffix IS NOT NULL THEN CONCAT(' ', d.suffix) ELSE '' END
                )) AS approved_by, ";

            $this->db->select($sql);
            $this->db->from($this->tblPolicy.' as a');
            $this->db->join($this->tblCategory.' as b', 'b.id = a.category_id', 'LEFT');
            $this->db->join($this->tblEmployees.' as c', 'c.id = a.author', 'LEFT');
            $this->db->join($this->tblEmployees.' as d', 'd.id = a.approved_by', 'LEFT');
            $this->db->join($this->tblcomp.' as e', 'e.policy_id = a.id', 'LEFT');
            $this->db->join($this->tbldept.' as f', 'f.policy_id = a.id', 'LEFT');

            if($search){
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

            if($builder){
                $this->db->where($builder);
            }

            if($advance){
                $this->db->group_start();
                if(isset($advance['title']) && $advance['title']){
                    $this->db->or_like('a.title', $advance['title'], 'both');
                }
                if(isset($advance['document_no']) && $advance['document_no']){
                    $this->db->or_like('a.document_no', $advance['document_no'], 'both');
                }
                if(isset($advance['author']) && $advance['author']){
                    $this->db->or_where('a.author', $advance['author']);
                }
                if(isset($advance['objective']) && $advance['objective']){
                    $this->db->or_like('a.objective', $advance['objective'], 'both');
                }
                if(isset($advance['companies']) && $advance['companies']){
                    $this->db->where_in('e.company_id', $advance['companies']);
                }
                if(isset($advance['category']) && $advance['category']){
                    $this->db->where_in('a.category_id', $advance['category']);
                }
                $this->db->group_end();
            }

            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $this->db->where('f.department_id', $deptId);
            $this->db->where('a.is_archived', 0);

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $this->db->group_by('a.id');

            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){
                    $company = @unserialize($rs->companies);
                    $rs->category = $rs->name;
                    $rs->companies = $this->get_companies($company);

                    $path = 'uploads/files/qms/document/'.$rs->id.'/'.$rs->filename;
                    if(file_exists($path) && $rs->filename){
                        $rs->file_exist = true;
                    }else{
                        $rs->file_exist = false;
                    }

                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $resultset[] = $v;
                }
            }

            return $resultset;
        }

        function get_item_count($search = null, $deptId, $builder = null, $advance = null){
            $filterFields = array('a.title', 'a.scope', 'a.objective', 'a.effective_date', 'b.name', 'a.document_no', 'a.ref_code', 'a.ref_year', 'a.ref_series', 'a.ref_department');

            $this->db->from($this->tblPolicy.' as a');
            $this->db->join($this->tblCategory.' as b', 'b.id = a.category_id', 'LEFT');
            $this->db->join($this->tblEmployees.' as c', 'c.id = a.author', 'LEFT');
            $this->db->join($this->tblEmployees.' as d', 'd.id = a.approved_by', 'LEFT');
            $this->db->join($this->tbldept.' as e', 'e.policy_id = a.id', 'LEFT');


            if($search){
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


            $this->db->where('e.department_id', $deptId);
            $this->db->where('a.is_archived', 0);
            $query = $this->db->get();
            return $query->num_rows();
        }

        function get_companies($arr, $status = 'add'){
            $arrData = array();
            $result = array();
            $response = null;

            if($status == 'edit'){
                $this->db->select('id, code');
            }

            $this->db->from('gcchris.tblcompanies');
            $this->db->where_in('id', $arr);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach ($query->result_array() as $key => $_query) {
                    $code = $_query['code'];

                    if($status == 'edit'){
                        $arrData[$key] = $_query;
                    }else{
                        $arrData[$key] = $code;
                    }
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            $response = $result;

            return $response;
        }
    }