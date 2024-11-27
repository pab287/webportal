<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Policy_m extends CI_Model{
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

        function getPolicyDatatableRequest(){
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"0", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $categoryId = (isset($post["category"]) && $post["category"]) ? $post["category"] : 1;
            $builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql']) ? $post["query_builder"]['sql'] : null;
            $advance = (isset($post["advance_search"]) && $post["advance_search"]) ? $post["advance_search"] : null;

            $rowCount = 0;
            $rowData = array();

            $rowData = $this->get_item($limit, $offset, $sortBy, $sortOrder, $search, $categoryId, $builder, $advance);
            $rowCount = $this->get_item_count($search, $categoryId, $builder, $advance);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function get_item($limit, $offset, $sortBy, $sortOrder, $search = null, $catId, $builder = null, $advance = null){
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
                if(isset($advance['departments']) && $advance['departments']){
                    $this->db->where_in('f.department_id', $advance['departments']);
                }
                $this->db->group_end();
            }

            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $this->db->where('a.category_id', $catId);
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

        function get_item_count($search = null, $catId, $builder = null, $advance = null){
            $filterFields = array('a.title', 'a.scope', 'a.objective', 'a.effective_date', 'b.name', 'a.document_no', 'a.ref_code', 'a.ref_year', 'a.ref_series', 'a.ref_department');

            $this->db->from($this->tblPolicy.' as a');
            $this->db->join($this->tblCategory.' as b', 'b.id = a.category_id', 'LEFT');
            $this->db->join($this->tblEmployees.' as c', 'c.id = a.author', 'LEFT');
            $this->db->join($this->tblEmployees.' as d', 'd.id = a.approved_by', 'LEFT');

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

            // if($builder){
            //     $this->db->where($builder);
            // }

            $this->db->where('a.category_id', $catId);
            $this->db->where('a.is_archived', 0);
            $query = $this->db->get();
            return $query->num_rows();
        }

        function getPolicyModalContent($content="add"){
            $resultset = array();
            $html = "";
            $arrData = array();
            $arr = array();
    
            if($content == "add"){
                $html = $this->load->view("qms/masterfile/document/modals/add_content", null, true);
            }
            if($content == "edit"){
                $post = $this->input->post();
                if(isset($post) && $post){
                    unset($post["csrf_token"]);
                    $tempPolicy = $this->db->get_where($this->tblPolicy, $post);
                    if($tempPolicy->num_rows() == 1){
                        foreach($tempPolicy->result() as $key => $rs){
                            // $author = @unserialize($rs->author);
                            $company = @unserialize($rs->companies);
                            $department = @unserialize($rs->departments);
                            $reviewed = @unserialize($rs->reviewed_by);

                            $rs->company_ids = $company;
                            $rs->author = $this->get_authors($rs->author, 'edit');
                            $rs->category = $this->get_category_name($rs->category_id);
                            $rs->companies = $this->get_companies($company, 'edit');
                            $rs->departments = $this->get_departments($department, 'edit');
                            $rs->reviewed_by = $this->get_authors($reviewed, 'edit');
                            $rs->approved_by = $this->get_emps($rs->approved_by);
                            
                            $arrData[$key] = $rs;
                        }

                        foreach ($arrData as $k => $v) {
                            $arr[] = $v;
                        }

                        $arrData = (object) $arr[0];
                    }
                }
                $html = $this->load->view("qms/masterfile/document/modals/edit_content", array("data"=>$arrData), true);
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

        function getEmployeeCollection(){
            $id = $this->user_data['emp_id'];
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $this->db->from('gccmaster.tblemployees');
                // $this->db->where('gccmaster.tblemployees.employee_status', 'Active');
                $this->db->order_by('firstname', 'asc');
                $this->db->like('firstname', $get['q']);
                $this->db->or_like('middlename', $get['q']);
                $this->db->or_like('lastname', $get['q']);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    foreach ($query->result_array() as $_query) {
                        $empName = $this->core_layout->getDisplayName($_query);
                        $displayName = $empName["display_name_1"];

                        $data = array();
                        $data["id"] = $_query["id"];
                        $data["text"] = $displayName;
                        /*** $data["text"] = $_query["firstname"]." ".$_query["middlename"]." ".$_query["lastname"]; ***/
                        $resultarray[] = $data;
                    }
                }
            } else {
                $this->db->from('gccmaster.tblemployees');
                // $this->db->where('gccmaster.tblemployees.employee_status', 'Active');
                $this->db->order_by('firstname', 'asc');
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    foreach ($query->result_array() as $_query) {
                        $empName = $this->core_layout->getDisplayName($_query);
                        $displayName = $empName["display_name_1"];

                        $data = array();
                        $data["id"] = $_query["id"];
                        $data["text"] = $displayName;
                        /*** $data["text"] = $_query["firstname"]." ".$_query["middlename"]." ".$_query["lastname"]; ***/
                        $resultarray[] = $data;
                    }
                }
            }

            return $resultarray;
        }

        function add_policy(){
            $result = array();
            $post = $this->input->post();
            $filename = '';
            $type = '';
            $size = '';
            $file = $_FILES['file'];

            if(isset($post) && $post){
                $this->db->where('document_no', $post['document_no']);
                $this->db->where('revision_no', $post['revision_no']);
                $this->db->where('effective_date', $post['effective_date']);
                $this->db->where('title', $post['title']);
                $this->db->where('scope', $post['scope']);
                $this->db->where('objective', $post['objective']);
                $this->db->where('category_id', $post['category']);
                $check_policy = $this->db->get($this->tblPolicy);

                if($check_policy->num_rows() > 0){
                    $result["response"] = false;
                    $result["toastr_msg"] = "Document Already exists!";
                }else{
                    $this->db->reset_query();

                    $category = $post['category'];
                    $company = serialize($post['company']);
                    $department = (isset($post['department']) && $post['department']) ? serialize($post['department']) : null;
                    unset($post['csrf_token'], $post['category'], $post['company'], $post['department']);
                
                    // $post['author'] = isset($post['author']) && $post['author'] ? serialize($post['author']) : 0;
                    $post['author'] = isset($post['author']) && $post['author'] ? $post['author'] : 0;
                    $post['prepared_date'] = isset($post['prepared_date']) && $post['prepared_date'] ? $post['prepared_date'] : null;
                    $post['approved_by'] = isset($post['approved_by']) && $post['approved_by'] ? $post['approved_by'] : 0;
                    $post['approved_date'] = isset($post['approved_date']) && $post['approved_date'] ? $post['approved_date'] : null;
                    // $post['reviewed_by'] = isset($post['reviewed_by']) && $post['reviewed_by'] ? $post['reviewed_by'] : 0;
                    $post['reviewed_by'] = isset($post['reviewed_by']) && $post['reviewed_by'] ? serialize($post['reviewed_by']) : 0;
                    $post['reviewed_date'] = isset($post['reviewed_date']) && $post['reviewed_date'] ? $post['reviewed_date'] : null;
                    
                    $code = $this->get_code($category);

                    if($code == 'Memo No.'){
                        $post['document_no'] = $code.' '.$post['ref_year'].'-'.$post['ref_series'].' '.$post['ref_department'];
                        $post['ref_code'] = $code;
                    }

                    if($code == 'KRA/PI'){
                        $post['document_no'] = $code.' '.$post['ref_year'];
                        $post['ref_code'] = $code;
                    }

                    $post['added_by'] = $this->user_data['emp_id'];
                    $post['added_dt'] = date('Y-m-d H:i:s');
                    $post['category_id'] = $category;
                    $post['companies'] = $company;
                    $post['departments'] = $department;
    
                    $query = $this->db->insert($this->tblPolicy, $post);
                    if($query){
                        $id = $this->db->insert_id();
    
                        $movedFile = (object) $this->moveUploadedFile($id, $file);
                        if($movedFile->response){
                            $data = array(
                                'filename' => $movedFile->filename,
                                'filetype' => 'pdf',
                                'filesize' => $movedFile->size,
                            );
    
                            $this->db->where('id', $id);
                            $q = $this->db->update($this->tblPolicy, $data);
                            if($q){
                                $_data = array(
                                    'policy_id' => $id,
                                    'filename' => $movedFile->filename,
                                    'type' => 'pdf',
                                    'size' => $movedFile->size,
                                    'filepath' => $movedFile->filepath,
                                    'created_at' => date('Y-m-d H:i:s')
                                );
    
                                $qry = $this->db->insert($this->tblDocument, $_data);
                                if($qry){
                                    $docId = $this->db->insert_id();
    
                                    $data = array(
                                        'document_id' => $docId,
                                        'version_number' => '1',
                                        'revision_date' => date('Y-m-d H:i:s'),
                                        'document_file' => $movedFile->filename
                                    );
    
                                    $version = $this->db->insert($this->tblVersion, $data);
    
                                    if($version){
                                        $verId = $this->db->insert_id();
                                        
                                        $docData = array(
                                            'document_id' => $docId,
                                            'version_id' => $verId,
                                            'action_description' => 'insert',
                                            'action_date' => date('Y-m-d H:i:s'),
                                            'action_by' => $this->user_data['emp_id']
                                        );
    
                                        $this->db->insert($this->tblHistory, $docData);
                                    }
                                }
                            }
                        }

                        if($company){
                            $_comptemp = @unserialize($company);
                            foreach($_comptemp as $comp){
                                $data = array(
                                    'policy_id' => $id,
                                    'company_id' => $comp
                                );
    
                                $this->db->insert($this->tblcomp, $data);
                            }
                        }

                        if($department){
                            $_deptTemp = @unserialize($department);
                            foreach($_deptTemp as $dept){
                                $data = array(
                                    'policy_id' => $id,
                                    'department_id' => $dept
                                );
    
                                $this->db->insert($this->tbldept, $data);
                            }
                        }
    
                        $result["response"] = true;
                        $result['code'] = $code;
                        $result['category_id'] = $category;
                        $result["toastr_msg"] = "Document data has been added.";
                        $this->core_layout->setEventLog("User ".$this->loggedInUsername. " inserted new document with db id no. ".$id,"insert", "success", "gccppm", "user");
                    }else{
                        $result["response"] = false;
                        $result["toastr_msg"] = "Failed saving document data!";
                        $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has error inserting document","insert", "error", "gccppm", "system");
                    }
                }

            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Document masterfile - Error, No post data found.","insert", "error", "gccppm", "system");
            }
            
            return $result;
        }

        function moveUploadedFile($id, $file, $filename = null){
            $result = array();

            if($id && $file){
                $tempFilename = $file['name'];
                $tempFileExt = $file['type'];
                $tempFileSize = $file['size'];

                $tempDirectory = "uploads/files/qms/document/{$id}";
                if($filename){
                    unlink($tempDirectory.'/'.$filename);
                }
                $tempFilePath = $tempDirectory.'/'.$tempFilename;

                $createFilePath = false;

                if(!file_exists($tempDirectory)){
                    $mkdir = mkdir($tempDirectory, 0777, true);
                    if ($mkdir){ $createFilePath = true; }
                }else{
                    $createFilePath = true;
                }

                if($createFilePath == false){
                    $result['response'] = false;
                    $result['msg'] = 'Failed to create directory for the uploaded file';
                }else{
                    $config = array();
                    $config['upload_path']          = $tempDirectory;
                    $config['allowed_types']        = '*';
                    $config['max_size']             = 100000;
                    $config['file_name']            = preg_replace("/\s+/", "_", $file['name']);

                    $this->upload->initialize($config);

                    if($this->upload->do_upload('file')){
                        $upload_data = $this->upload->data();

                        $result['response'] = true;
                        $result['filename'] = $config['file_name'];
                        $result['type'] = $tempFileExt;
                        $result['size'] = $tempFileSize;
                        $result['filepath'] = $tempFilePath;
                    }else{
                        $result['response'] = false;
                    }
                }
            }

            return $result;
        }

        function get_category_name($id){
            $this->db->from($this->tblCategory);
            $this->db->where('id', $id);
            $query = $this->db->get();

            return $query->row()->name;
        }

        function get_authors($arr, $status = 'add'){
            $arrData = array();
            $result = array();
            $response = null;

            if($status == 'edit'){
                $this->db->select('id, firstname, lastname, middlename, suffix');
            }

            $this->db->from('gccmaster.tblemployees');
            $this->db->where_in('id', $arr);
            // $this->db->where('employee_status', 'Active');
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach ($query->result_array() as $key => $_query) {
                    $empName = $this->core_layout->getDisplayName($_query);

                    $displayName = $empName["display_name_1"];

                    if($status == 'edit'){
                        unset($_query['firstname'], $_query['lastname'], $_query['middlename'], $_query['suffix']);

                        $_query['id'] = $_query['id'];
                        $_query['name'] = $displayName;

                        $arrData[$key] = $_query;
                    }else{
                        $arrData[$key] = $displayName;
                    }

                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            if($status == 'add'){
                $response = implode(', ', $result);
            }else{
                $response = $result;
            }

            return $response;
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

        function get_departments($arr, $status = 'add'){
            $arrData = array();
            $result = array();
            $response = null;

            if($status == 'edit'){
                $this->db->select('id, description');
            }

            $this->db->from($this->tblDepartment);
            $this->db->where_in('id', $arr);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach ($query->result_array() as $key => $_query) {
                    $code = $_query['description'];

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

            if($status == 'add'){
                $response = implode(', ', $result);
            }else{
                $response = $result;
            }

            return $response;
        }

        function get_company_list(){
            $result = array();
            $sql = 'id, CONCAT(code, " | ", description) as text';

            $this->db->select($sql);
            $this->db->from($this->tblCompany);
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $result = $query->result();
            }

            return $result;
        }

        function get_department_list(){
            $result = array();

            $sql = 'id, description as text';

            $this->db->select($sql);
            $this->db->from($this->tblDepartment);
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $result = $query->result();
            }

            return $result;
        }

        function get_emps($id){
            $arrData = array();
            $result = array();
            
            $this->db->select('id, firstname, lastname, middlename, suffix');
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('id', $id);
            // $this->db->where('employee_status', 'Active');
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach ($query->result_array() as $key => $_query) {
                    $empName = $this->core_layout->getDisplayName($_query);

                    $displayName = $empName["display_name_1"];

                    unset($_query['firstname'], $_query['lastname'], $_query['middlename'], $_query['suffix']);

                    $_query['id'] = $_query['id'];
                    $_query['name'] = $displayName;

                    $arrData[$key] = $_query;
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            return $result;
        }

        function edit_policy(){
            $result = array();
            $post = $this->input->post();
            $filename = '';
            $type = '';
            $size = '';
            $file = isset($_FILES['file']) && $_FILES['file'] ? $_FILES['file'] : null;

            if(isset($post) && $post){

                $id = $post['id'];

                $get_policy = $this->db->get_where($this->tblPolicy, array('id' => $id))->row();
                
                $category = $post['category'];
                $company = serialize($post['company']);
                $department = (isset($post['department']) && $post['department']) ? serialize($post['department']) : null;
                unset($post['csrf_token'], $post['category'], $post['company'], $post['id'], $post['department']);
            
                // $post['author'] = serialize($post['author']);
                $post['updated_by'] = $this->user_data['emp_id'];
                $post['updated_dt'] = date('Y-m-d H:i:s');
                $post['category_id'] = $category;
                $post['companies'] = $company;
                $post['departments'] = $department;

                $post['author'] = isset($post['author']) && $post['author'] ? $post['author'] : 0;
                $post['prepared_date'] = isset($post['prepared_date']) && $post['prepared_date'] ? $post['prepared_date'] : null;
                $post['approved_by'] = isset($post['approved_by']) && $post['approved_by'] ? $post['approved_by'] : 0;
                $post['approved_date'] = isset($post['approved_date']) && $post['approved_date'] ? $post['approved_date'] : null;
                // $post['reviewed_by'] = isset($post['reviewed_by']) && $post['reviewed_by'] ? $post['reviewed_by'] : 0;
                $post['reviewed_by'] = isset($post['reviewed_by']) && $post['reviewed_by'] ? serialize($post['reviewed_by']) : 0;
                $post['reviewed_date'] = isset($post['reviewed_date']) && $post['reviewed_date'] ? $post['reviewed_date'] : null;

                $code = $this->get_code($category);

                if($code == 'Memo No.'){
                    $post['document_no'] = $code.' '.$post['ref_year'].'-'.$post['ref_series'].' '.$post['ref_department'];
                }else if($code == 'KRA/KPI'){
                    $post['document_no'] = $code.' '.$post['ref_year'];
                }else{
                    $post['ref_code'] = null;
                    $post['ref_year'] = null;
                    $post['ref_series'] = null;
                    $post['ref_department'] = null;
                }

                $query = $this->db->update($this->tblPolicy, $post, array('id' => $id));
                if($query){
                    
                    if($file && $file != null){
                        $get_doc = $this->get_document($id);
                        $movedFile = (object) $this->moveUploadedFile($id, $file, $get_doc->filename);

                        if($movedFile->response){
                            $data = array(
                                'filename' => $movedFile->filename,
                                'filetype' => 'pdf',
                                'filesize' => $movedFile->size,
                            );
    
                            $this->db->where('id', $id);
                            $q = $this->db->update($this->tblPolicy, $data);
                            if($q){
                                $_data = array(
                                    'policy_id' => $id,
                                    'document_no' => $get_policy->document_no,
                                    'revision_no' => $get_policy->revision_no,
                                    'effective_date' => $get_policy->effective_date,
                                    'filename' => $movedFile->filename,
                                    'type' => 'pdf',
                                    'size' => $movedFile->size,
                                    'filepath' => $movedFile->filepath,
                                    'created_at' => date('Y-m-d H:i:s')
                                );
    
                                $_q = $this->db->insert($this->tblDocument, $_data);

                                if($_q){
                                    $docId = $this->db->insert_id();
                                    $get_version = $this->get_version($get_doc->id);

                                    $doc_data = array(
                                        'document_id' => $docId,
                                        'version_number' => $get_version['version'],
                                        'revision_date' => date('Y-m-d H:i:s'),
                                        'document_file' => $movedFile->filename
                                    );

                                    $docs = $this->db->insert($this->tblVersion, $doc_data);
                                    if($docs){
                                        $versionId = $this->db->insert_id();
                                        $history_data = array(
                                            // 'document_id' => $get_doc->id,
                                            // 'version_id' => $get_version['id'],
                                            'document_id' => $docId,
                                            'version_id' => $versionId,
                                            'action_description' => 'update',
                                            'action_date' => date('Y-m-d H:i:s'),
                                            'action_by' => $this->user_data['emp_id']
                                        );

                                        $this->db->insert($this->tblHistory, $history_data);
                                    }
                                }
                            }
                        }
                    }

                    if($company){
                        $this->db->delete($this->tblcomp, array('policy_id' => $id));
                        $this->db->reset_query();

                        $_comptemp = @unserialize($company);
                        foreach($_comptemp as $comp){
                            $data = array(
                                'policy_id' => $id,
                                'company_id' => $comp
                            );

                            $this->db->insert($this->tblcomp, $data);
                        }
                    }

                    if($department){
                        $this->db->delete($this->tbldept, array('policy_id' => $id));
                        $this->db->reset_query();

                        $_deptTemp = @unserialize($department);
                        foreach($_deptTemp as $dept){
                            $data = array(
                                'policy_id' => $id,
                                'department_id' => $dept
                            );

                            $this->db->insert($this->tbldept, $data);
                        }
                    }

                    $result["response"] = true;
					$result["toastr_msg"] = "Document data has been updated.";
                    $result['code'] = $code;
                    $result['category_id'] = $category;
					$this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated document with db id no. ".$id,"update", "success", "gccppm", "user");
                }else{
                    $result["response"] = false;
					$result["toastr_msg"] = "Failed to update document data!";
					$this->core_layout->setEventLog("User ".$this->loggedInUsername. " has error updating document","update", "error", "gccppm", "system");
                }
            }else{
                $result["response"] = false;
                $result["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Document masterfile - Error, No post data found.","update", "error", "gccppm", "system");
            }

            return $result;
        }

        function get_document($id){
            $result = array();

            $this->db->order_by('id', 'DESC');
            $this->db->limit(1);
            $this->db->where('policy_id', $id);
            $query = $this->db->get_where($this->tblDocument)->result();

            // if($query){
            //     $result = $query[0];
            // }else{
            //     $this->db->reset_query();

            //     $this->db->order_by('id', 'DESC');
            //     $this->db->where('policy_id', $id);
            //     $query = $this->db->get($this->tblDocument)->result();
            //     $result = $query[0];
            // }

            return $query[0];
        }

        function get_version($id){
            $num = 0;
            $result = array();

            $this->db->select('id, version_number');
            $this->db->where('document_id', $id);
            $this->db->order_by('id', 'DESC');
            $query = $this->db->get($this->tblVersion);

            if($query->num_rows() > 0){
                $row = $query->row();

                $num = $row->version_number + 1;

                $result['id'] = $row->id;
                $result['version'] = $num;
            }else{
                $num = 1;

                $result['id'] = null;
                $result['version'] = $num;
            }

            return $result;
        }

        function remove_policy(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                unset($post["csrf_token"]);

                $updated = $this->db->update($this->tblPolicy, array("is_archived"=>1, 'archived_by' => $this->user_data['emp_id']), $post);
                if($updated){
                    $data = array(
                        "archived_table"=>$this->tblPolicy,
                        "archived_id"=>$post['id'],
                        "archived_by"=>$this->user_data["emp_id"]
                    );
    
                    $this->db->insert($this->archivedTable, $data);
                        
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Document has been removed.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " archived document with db id no. ".$post['id'],"archived", "success", "gccppm", "user");
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to remove document!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " error archiving document with db id no. ".$post['id'],"archived", "error", "gccppm", "system");
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Document masterfile - Error, No post data found.","archived", "error", "gccppm", "system");
            }
    
            return $resultset;
        }

        function getArchivedPolicyDatatableRequest(){
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

            $rowData = $this->get_archived_item($limit, $offset, $sortBy, $sortOrder, $search);
            $rowCount = $this->get_archived_item_count($search);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function get_archived_item($limit, $offset, $sortBy, $sortOrder, $search){
            $filterFields = array('title', 'description');
            $resultset = array();
            $arrData = array();

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

            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $this->db->where('is_archived', 1);

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){
                    // $author = @unserialize($rs->author);
                    $company = @unserialize($rs->companies);

                    $rs->author = $this->get_authors($rs->author);
                    $rs->category = $this->get_category_name($rs->category_id);
                    $rs->companies = $this->get_companies($company);

                    $arrData[$key] = $rs;
                }
                
                foreach ($arrData as $k => $v) {
                    $resultset[] = $v;
                }
            }

            return $resultset;
        }

        function get_archived_item_count($search){
            $filterFields = array('title', 'description');

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

            $this->db->where('is_archived', 1);

            $query = $this->db->get();
            return $query->num_rows();
        }

        function restore_policy(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                unset($post["csrf_token"]);

                $updated = $this->db->update($this->tblPolicy, array("is_archived"=>0), $post);
                if($updated){
                    // $data = array(
                    //     "archived_table"=>$this->tblPolicy,
                    //     "archived_id"=>$post['id'],
                    //     "archived_by"=>$this->user_data["emp_id"]
                    // );
    
                    // $this->db->insert($this->archivedTable, $data);
                        
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Document has been restored.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " restored document with db id no. ".$post['id'],"restore", "success", "gccppm", "user");
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to restore document!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " error restoring document with db id no. ".$post['id'],"restore", "error", "gccppm", "system");
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Document masterfile - Error, No post data found.","restore", "error", "gccppm", "system");
            }
    
            return $resultset;
        }

        function get_all_categories(){
            $result = array();
            $this->db->select('id, name')
                ->from($this->tblCategory)
                ->where('is_archived', 0)
                ->order_by('id', 'asc');
            $query = $this->db->get();
            /** incase for specific word to be ordered as first */
                // ->order_by("
                //     (CASE UPPER(name) WHEN 'MEMORANDUM' THEN 1 ELSE 2 END) ASC
                // ", FALSE);
            /** incase for specific word to be ordered as first */

            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){
                    if($this->check_string($rs->name)){
                        $rs->grouped = strtolower(preg_replace('/[^a-zA-Z0-9_.]/', '_', $rs->name));
                    }else{
                        $rs->grouped = strtolower(str_replace(' ', '_', $rs->name));
                    }
                    
                    $rs->name = strtoupper($rs->name);
                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            return array('data' => $result);
        }

        function get_documents($year = '', $search = null, $builder = null, $advance = null){
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
                )) AS approved_by";

            $this->db->select($sql);
            $this->db->from($this->tblPolicy.' as a');
            $this->db->join($this->tblCategory.' as b', 'b.id = a.category_id', 'LEFT');
            $this->db->join($this->tblEmployees.' as c', 'c.id = a.author', 'LEFT');
            $this->db->join($this->tblEmployees.' as d', 'd.id = a.approved_by', 'LEFT');
            $this->db->join($this->tblcomp.' as e', 'e.policy_id = a.id', 'LEFT');
            $this->db->join($this->tbldept.' as f', 'f.policy_id = a.id', 'LEFT');
            $this->db->where('a.ref_year', $year);
            $this->db->where('a.is_archived', 0);
            $this->db->order_by('a.id', 'desc');

            $filterFields = array('a.title', 'a.scope', 'a.objective', 'a.effective_date', 'b.name', 'a.document_no', 'a.ref_code', 'a.ref_year', 'a.ref_series', 'a.ref_department');
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
                if(isset($advance['departments']) && $advance['departments']){
                    $this->db->where_in('f.department_id', $advance['departments']);
                }
                $this->db->group_end();
            }

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

        function get_timeline(){
            $result = array();
            $arrData = array();
            $post = $this->input->post();

            $id = (isset($post["id"]) && $post["id"]) ? $post["id"] : 1;
            $builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql']) ? $post["query_builder"]['sql'] : null;
            $advance = (isset($post["advance_search"]) && $post["advance_search"]) ? $post["advance_search"] : null;
            $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : null;
            $filterFields = array('a.title', 'a.scope', 'a.objective', 'a.effective_date', 'b.name', 'a.document_no', 'a.ref_code', 'a.ref_year', 'a.ref_series', 'a.ref_department');

            $this->db->select('ref_year')
                ->from($this->tblPolicy.' as a')
                ->where('category_id', $post['id']);
            
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
                if(isset($advance['departments']) && $advance['departments']){
                    $this->db->where_in('f.department_id', $advance['departments']);
                }
                $this->db->group_end();
            }
            
            $this->db->group_by('ref_year')
                ->order_by('ref_year', 'desc');
            
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){
                    $contents = $this->get_documents($rs->ref_year, $search, $builder, $advance);

                    if(count($contents) > 0){
                        $rs->contents = $contents;
                        $arrData[$key] = $rs;
                    }
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }
            return array('data' => $result);
        }

        function check_string($txt){
            $regex = preg_match('[@_!#$%^&*()<>?/|}{~:]', $txt);
            if($regex){
                return true;
            }else{
                if(strstr($txt, '/')){
                    return true;
                }else{
                    return false;
                }
            }
        }

        function get_code($id){
            $code = null;
            $this->db->from($this->tblCategory)
                ->select('code, name')
                ->where('id', $id);
            $query = $this->db->get();

            $row = $query->row();

            if(strtolower($row->name) == 'memorandum'){
                $code = 'Memo No.';
            }else {
                $code = $row->code;
            }

            return $code;
        }
    }
