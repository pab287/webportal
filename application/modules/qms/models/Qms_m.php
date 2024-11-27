<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Qms_m extends CI_Model{
        protected $tblPolicy = 'gccppm.tblpolicy';
        protected $tblDocument = 'gccppm.tblpolicy_documents';
        protected $tblCategory = 'gccppm.tblcategory';
        protected $archivedTable = "gccmaster.archived_items";
        protected $tblCompany = 'gcchris.tblcompanies';
        protected $tblDownload = 'gccppm.tbldownload_history';
        protected $tblVersion = 'gccppm.tbldocument_version';
        protected $tblcomp = 'gccppm.tblpolicy_companies';
        protected $tbldept = 'gccppm.tblpolicy_departments';

        private $user_data = array();

        function __construct(){
            parent::__construct();

            $this->core_layout->setPrivilegeName("ppm_dashboard");

            $this->user_data = $this->session->userdata("logged_in");
            $this->current_action = $this->core_layout->getCurrentActions();
            $this->loggedInUsername = $this->user_data["username"];
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

            $company = (isset($post['company']) && $post['company']) ? $post['company'] : null;
            $category = (isset($post['category']) && $post['category']) ? $post['category'] : null;
            $author = (isset($post['author']) && $post['author']) ? $post['author'] : null;
            $title = (isset($post['title']) && $post['title']) ? $post['title'] : null;
            $department = (isset($post['department']) && $post['department']) ? $post['department'] : null;

            $rowCount = 0;
            $rowData = array();

            $rowData = $this->get_item($limit, $offset, $sortBy, $sortOrder, $search, $company, $department, $category, $author, $title);
            $rowCount = $this->get_item_count($search, $company, $department, $category, $author, $title);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function get_item($limit, $offset, $sortBy, $sortOrder, $search, $companies, $departments, $category, $authors, $title){
            $filterFields = array('a.title', 'a.objective', 'a.scope', 'b.name');
            $resultset = array();
            $arrData = array();

            $sql = "a.*, b.name";
            $this->db->select($sql);
            $this->db->join($this->tblCategory.' as b', 'b.id = a.category_id', 'LEFT');
            $this->db->from($this->tblPolicy.' as a');

            if($title){
                $this->db->like('a.title', $title, 'both');
            }

            if($category){
                $this->db->where('a.category_id', $category);
            }

            if($authors){
                $this->db->like('a.author', $authors, 'both');
            }

            if($companies){
                $this->db->like('a.companies', $companies, 'both');
            }

            if($departments){
                $this->db->like('a.departments', $departments, 'both');
            }

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

            $this->db->where('a.is_archived', 0);

            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){
                    $author = @unserialize($rs->author);
                    $company = @unserialize($rs->companies);
                    $department = @unserialize($rs->departments);

                    $rs->author = $this->get_authors($author);
                    $rs->companies = $this->get_companies($company);

                    $path = 'uploads/files/qms/document/'.$rs->id.'/'.$rs->filename;
                    if(file_exists($path) && $rs->filename){
                        $rs->file_exist = true;
                    }else{
                        $rs->file_exist = false;
                    }

                    if($this->user_data['emp_id'] != 1){
                        if(in_array("view_all", $this->current_action)){
                            $arrData[$key] = $rs;
                        }else{
                            if($company && in_array($this->user_data['company'], $company)){
                                $arrData[$key] = $rs;
                            }
                        }
                    }else{
                        $arrData[$key] = $rs;
                    }
                }
                
                foreach ($arrData as $k => $v) {
                    $resultset[] = $v;
                }
            }

            return $resultset;
        }

        function get_item_count($search, $companies, $departments, $category, $authors, $title){
            $arrData = array();
            $result = array();

            $filterFields = array('a.title', 'a.objective', 'a.scope', 'b.name');

            $sql = "a.*, b.name";
            $this->db->select($sql);
            $this->db->join($this->tblCategory.' as b', 'b.id = a.category_id', 'LEFT');
            $this->db->from($this->tblPolicy.' as a');
            
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

            if($title){
                $this->db->like('a.title', $title, 'both');
            }

            if($category){
                $this->db->where('a.category_id', $category);
            }

            if($companies){
                $this->db->like('a.companies', $companies, 'both');
            }

            if($departments){
                $this->db->like('a.departments', $departments, 'both');
            }

            $this->db->where('a.is_archived', 0);

            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){
                    $company = @unserialize($rs->companies);
                    $department = @unserialize($rs->departments);
                    
                    if($this->user_data['emp_id'] != 1){
                        if(in_array("view_all", $this->current_action)){
                            $arrData[$key] = $rs;
                        }else{
                            if($company && in_array($this->user_data['company'], $company)){
                                $arrData[$key] = $rs;
                            }
                        }
                    }else{
                        $arrData[$key] = $rs;
                    }
                }
            }
            return count($arrData);
        }

        function get_authors($arr){
            $arrData = array();
            $result = array();

            $this->db->from('gccmaster.tblemployees');
            $this->db->where_in('id', $arr);
            $this->db->where('employee_status', 'Active');
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach ($query->result_array() as $key => $_query) {
                    $empName = $this->core_layout->getDisplayName($_query);
                    $displayName = $empName["display_name_1"];

                    $arrData[$key] = $displayName;
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            return implode(', ', $result);
        }

        function get_companies($arr){
            $arrData = array();
            $result = array();

            $this->db->from('gcchris.tblcompanies');
            $this->db->where_in('id', $arr);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach ($query->result_array() as $key => $_query) {
                    $code = $_query['code'];

                    $arrData[$key] = $code;
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            return implode(', ', $result);
        }

        function get_category_name($id){
            $this->db->from($this->tblCategory);
            $this->db->where('id', $id);
            $query = $this->db->get();

            return $query->row()->name;
        }

        function getPolicyModalContent($content="add"){
            $resultset = array();
            $html = "";
            $arrData = array();
            $arr = array();
    
            if($content == "add"){
                $html = $this->load->view("qms/masterfile/document/modals/add_content", null, true);
            }
            if($content == "view"){
                $post = $this->input->post();
                if(isset($post) && $post){
                    unset($post["csrf_token"]);

                    $sql = "a.*, b.name";
                    $this->db->select($sql);
                    $this->db->join($this->tblCategory.' as b', 'b.id = a.category_id', 'LEFT');
                    $this->db->from($this->tblPolicy.' as a');
                    $this->db->where('a.id', $post['id']);

                    $tempPolicy = $this->db->get();

                    if($tempPolicy->num_rows() == 1){
                        foreach($tempPolicy->result() as $key => $rs){
                            $author = @unserialize($rs->author);
                            $company = @unserialize($rs->companies);
                            $department = @unserialize($rs->departments);

                            $rs->author_id = $author;
                            $rs->company_ids = $company;
                            $rs->author = $this->get_authors($author);
                            // $rs->category = $this->get_category_name($rs->category_id);
                            $rs->companies = $this->get_companies($company);
                            $rs->reviewed_by = $this->get_emps($rs->reviewed_by);
                            $rs->approved_by = $this->get_emps($rs->approved_by);
                            $rs->total_download = $this->get_total_download($rs->id, 0);
                            $rs->total_print = $this->get_total_download($rs->id, 1);
                            $rs->latest_version_id = $this->get_version($rs->id);
                            $rs->versions = $this->get_version_history($rs->id, $rs->latest_version_id);
                            $rs->added_by = $rs->added_by == 1 ? 'System Generated' : $this->get_emps($rs->added_by);
                            $rs->departments = $this->get_departments($department);
                            $rs->department_id = $department;
                            $rs->dept_id = $this->user_data['department'] ? $this->user_data['department'] : 0;

                            if($rs->updated_by > 0){
                                $rs->updated_by = $rs->updated_by == 1 ? 'System Generated' : $this->get_emps($rs->added_by);
                            }

                            $path = 'uploads/files/qms/document/'.$rs->id.'/'.$rs->filename;

                            if(file_exists($path) && $rs->filename){
                                $rs->file_exist = true;
                            }else{
                                $rs->file_exist = false;
                            }
                            
                            $arrData[$key] = $rs;
                        }

                        foreach ($arrData as $k => $v) {
                            $arr[] = $v;
                        }

                        $arrData = (object) $arr[0];
                    }
                }
                $html = $this->load->view("qms/modals/view_content", array("data"=>$arrData), true);
            }
    
            if($html){
                $resultset["response"] = true;
                $resultset["html"] = $html;
                $resultset["data"] = $arrData;
            }else{
                $resultset["response"] = false;
            }
            
            return $resultset;
        }

        function get_emps($id){
            $arrData = array();
            $result = array();
            
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('id', $id);
            $this->db->where('employee_status', 'Active');
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach ($query->result_array() as $key => $_query) {
                    $empName = $this->core_layout->getDisplayName($_query);

                    $displayName = $empName["display_name_1"];

                    $arrData[$key] = $displayName;
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            return implode(', ', $result);
        }

        function count_download(){
            $arrData = array();
            $result = array();
            $post = $this->input->post();
            
            $type = $post['type'] == 'download' ? 0 : 1;
            $path = 'uploads/files/qms/document/'.$post['id'].'/'.$post['filename'];

            if(file_exists($path) && $path){
                $this->db->select('id');
                $document_id = $this->db->get_where($this->tblDocument, array('policy_id' => $post['id']))->row();
    
                $data = array(
                    'policy_id' => $post['id'],
                    'document_id' => $document_id->id,
                    'download_by' => $this->user_data['emp_id'],
                    'type' => $type,
                    'added_dt' => date('Y-m-d H:i:s')
                );
    
                $query = $this->db->insert($this->tblDownload, $data);
                if($query){
                    $result['state'] = true;
                    $result['total_download'] = $this->get_total_download($post['id'], $type);
                    $result["toastr_msg"] = "Documenty Successfully Downloaded!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has successfully downloaded document","download", "success", "gccppm", "user");
                }else{
                    $result['state'] = false;
                    $result["toastr_msg"] = "Failed to download document data!";
                	$this->core_layout->setEventLog("User ".$this->loggedInUsername. " has error downloading documet","download", "error", "gccppm", "system");
                }
            }else{
                $result['state'] = false;
                $result['total_download'] = $this->get_total_download($post['id'], $type);
                $result["toastr_msg"] = "Document File not Found.";
            }

            return $result;
        }

        function get_total_download($id, $type){
            $this->db->where('policy_id', $id);
            $this->db->where('type', $type);
            $query = $this->db->get($this->tblDownload);

            return $query->num_rows();
        }

        function get_version($id){
            $this->db->select('b.version_number');
            $this->db->where('a.policy_id', $id);
            $this->db->from($this->tblDocument.' as a');
            $this->db->join($this->tblVersion.' as b', 'b.document_id = a.id', 'LEFT');
            $this->db->order_by('b.id', 'DESC');
            $query = $this->db->get();

            return $query->row()->version_number;
        }

        function get_version_history($id, $version){
            $arr = array();

            if($id){
                $this->db->select('b.document_file, b.version_number, a.document_no, a.revision_no, a.effective_date, a.created_at');
                $this->db->where('a.policy_id', $id);
                $this->db->where('b.version_number !=', $version);
                $this->db->from($this->tblDocument.' as a');
                $this->db->join($this->tblVersion.' as b', 'b.document_id = a.id', 'LEFT');
                $this->db->order_by('b.id', 'DESC');
                $query = $this->db->get();

                $arr = $query->result();
            }

            return $arr;
        }

        function get_departments($arr){
            $arrData = array();
            $result = array();

            $this->db->from('gcchris.tbldepartments');
            $this->db->where_in('id', $arr);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach ($query->result_array() as $key => $_query) {
                    $code = $_query['description'];

                    $arrData[$key] = $code;
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            return implode(', ', $result);
        }

        function get_document($id){
            $arrData = array();

            $sql = "a.*, b.name";
            $this->db->select($sql);
            $this->db->join($this->tblCategory.' as b', 'b.id = a.category_id', 'LEFT');
            $this->db->from($this->tblPolicy.' as a');
            $this->db->where('a.id', $id);

            $tempPolicy = $this->db->get();
            
            if($tempPolicy->num_rows() == 1){
                foreach($tempPolicy->result() as $key => $rs){
                    $author = @unserialize($rs->author);
                    $company = @unserialize($rs->companies);
                    $department = @unserialize($rs->departments);

                    $rs->author_id = $author;
                    $rs->company_ids = $company;
                    $rs->author = $this->get_authors($author);
                    // $rs->category = $this->get_category_name($rs->category_id);
                    $rs->companies = $this->get_companies($company);
                    $rs->reviewed_by = $this->get_emps($rs->reviewed_by);
                    $rs->approved_by = $this->get_emps($rs->approved_by);
                    $rs->total_download = $this->get_total_download($rs->id, 0);
                    $rs->total_print = $this->get_total_download($rs->id, 1);
                    $rs->latest_version_id = $this->get_version($rs->id);
                    $rs->versions = $this->get_version_history($rs->id, $rs->latest_version_id);
                    $rs->added_by = $rs->added_by == 1 ? 'System Generated' : $this->get_emps($rs->added_by);
                    $rs->departments = $this->get_departments($department);
                    $rs->department_id = $department;

                    if($rs->updated_by > 0){
                        $rs->updated_by = $rs->updated_by == 1 ? 'System Generated' : $this->get_emps($rs->added_by);
                    }

                    $path = 'uploads/files/qms/document/'.$rs->id.'/'.$rs->filename;

                    if(file_exists($path) && $rs->filename){
                        $rs->file_exist = true;
                    }else{
                        $rs->file_exist = false;
                    }
                    
                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $arr[] = $v;
                }

                $arrData = (object) $arr[0];
            }

            return $arrData;
        }

        function get_category_analytics(){
            $result = array();

            $this->db->from($this->tblPolicy.' as a')
                ->select('count(a.id) as count, UPPER(b.name) as category')
                ->join($this->tblCategory.' as b', 'b.id = a.category_id', 'INNER')
                ->where('a.is_archived', 0)
                ->where('b.is_archived', 0)
                ->group_by('a.category_id');
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $result['data'] = $query->result();
            }

            return $result;
        }

        function get_company_analytics(){
            $result = array();

            $this->db->from($this->tblCompany.' as a')
                ->select('a.code as company, count(b.company_id) as count')
                ->join($this->tblcomp.' as b', 'b.company_id = a.id', 'INNER')
                ->join($this->tblPolicy.' as c', 'c.id = b.policy_id', 'LEFT')
                ->where('c.is_archived', 0)
                ->group_by('b.company_id');
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $result['data'] = $query->result();
            }

            return $result;
        }

        function getRecentlyPolicyDatatableRequest(){
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"0", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

            $rowCount = 0;
            $rowData = array();

            $rowData = $this->get_recently_item($limit, $offset, $sortBy, $sortOrder, $search);
            $rowCount = $this->get_recently_item_count($search);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function get_recently_item($limit, $offset, $sortBy, $sortOrder, $search){
            $filterFields = array('title', 'objective', 'scope', 'name');
            $resultset = array();
            $arrData = array();
            
            $week = date('Y-m-d', strtotime('-7 days', strtotime(date('Y-m-d'))));
            $this->db->select('id, title, document_no, filename, added_dt');
            $this->db->from($this->tblPolicy);
            
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
        
            $this->db->where('DATE(added_dt) >=', $week);
            $this->db->where('is_archived', 0);
        
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
        
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();
        
            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){
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

        function get_recently_item_count($search){
            $arrData = array();
            $result = array();

            $filterFields = array('title', 'objective', 'scope', 'name');
            
            $week = date('Y-m-d', strtotime('-7 days', strtotime(date('Y-m-d'))));
            $this->db->select('title, document_no, filename');
            $this->db->from($this->tblPolicy);
            
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

            $this->db->where('DATE(added_dt) >=', $week);
            $this->db->where('is_archived', 0);

            $query = $this->db->get();

            return $query->num_rows();
        }
    }