<?php defined('BASEPATH') || exit('No direct script access allowed');
    class Setting_m extends CI_Model{
        protected $checklistTbl = 'gcchris.tblchecklist';
        protected $employeeTbl = 'gccmaster.tblemployees';
        protected $archivedTable = "gccmaster.archived_items";
        protected $checklistDocTbl = 'gcchris.tblchecklist_documents';
        private $user_data;

        function __construct(){
            parent::__construct();
            $this->user_data = $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->user_data["username"];
        }

        function getChecklistDatatableRequest(){
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

            $rowData = $this->get_item($limit, $offset, $sortBy, $sortOrder, $search);
            $rowCount = $this->get_item_count($search);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function get_item($limit, $offset, $sortBy, $sortOrder, $search = null){
            $filterFields = array('a.name', 'a.description');
            $resultset = array();

            $sql = "CONCAT(UPPER(TRIM(b.firstname)), ' ',
            CASE WHEN UPPER(TRIM(b.middlename)) != 'N/A' AND UPPER(TRIM(b.middlename)) != 'NONE' AND
                    TRIM(b.middlename) !='' AND b.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(b.middlename, 1, 1), '.') ELSE ''
            END,' ', UPPER(TRIM(b.lastname)),
            CASE WHEN UPPER(TRIM(b.suffix)) != 'N/A' AND
                UPPER(TRIM(b.suffix !='NONE')) AND b.suffix !='' AND
                b.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(c.suffix))) ELSE ''
            END) as emp_add, a.*, CONCAT(UPPER(TRIM(c.firstname)), ' ',
            CASE WHEN UPPER(TRIM(c.middlename)) != 'N/A' AND UPPER(TRIM(c.middlename)) != 'NONE' AND
                    TRIM(c.middlename) !='' AND c.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(c.middlename, 1, 1), '.') ELSE ''
            END,' ', UPPER(TRIM(c.lastname)),
            CASE WHEN UPPER(TRIM(c.suffix)) != 'N/A' AND
                UPPER(TRIM(c.suffix !='NONE')) AND c.suffix !='' AND
                c.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(c.suffix))) ELSE ''
            END) as emp_update";

            $this->db->select($sql);
            $this->db->from($this->checklistTbl.' a');
            $this->db->join($this->employeeTbl.' b', 'b.id = a.added_by', 'LEFT');
            $this->db->join($this->employeeTbl.' c', 'c.id = a.updated_by', 'LEFT');
            $this->db->where('a.is_archived', 0);
            
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

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $resultset = $query->result();
            }

            return $resultset;
        }

        function get_item_count($search){
            $filterFields = array('name', 'description');

            $this->db->from($this->checklistTbl);
            $this->db->where('is_archived', 0);
            
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

            $query = $this->db->get();
            return $query->num_rows();
        }

        function getChecklistModalContent($content="add"){
            $resultset = array();
            $html = "";
            $arrData = array();
    
            if($content == "add"){
                $html = $this->load->view("hris/settings/checklist/modals/add_content", null, true);
            }
            if($content == "edit"){
                $post = $this->input->post();
                if(isset($post) && $post){
                    unset($post["csrf_token"]);
                    $tempChecklist = $this->db->get_where($this->checklistTbl, $post);
                    if($tempChecklist->num_rows() == 1){
                        $arrData = $tempChecklist->row();
                    }
                }
                $html = $this->load->view("hris/settings/checklist/modals/edit_content", array("data"=>$arrData), true);
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

        function new_checklist(){
            $post = $this->input->post();
            $result = array();

            if(isset($post) && $post){
                $this->db->like('name', $post['name'], 'both');
                $check_name = $this->db->get($this->checklistTbl);

                $post['added_by'] = $this->user_data['emp_id'];
                $post['added_dt'] = date('Y-m-d H:i:s');

                if($check_name->num_rows() > 0){
                    $result["response"] = false;
                    $result["toastr_msg"] = "Checklist already exist!";
                }else{
                    $query = $this->db->insert($this->checklistTbl, $post);
                    if($query){
                        $result["response"] = true;
                        $result["toastr_msg"] = "Checklist data has been added.";
                        $this->core_layout->setEventLog("User ".$this->loggedInUsername. " inserted new checklist with db id no. ".$this->db->insert_id(),"insert", "success", "gcchris", "user");
                    }else{
                        $result["response"] = false;
                        $result["toastr_msg"] = "Failed saving checklist data!";
                        $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has error inserting checklist","insert", "error", "gcchris", "system");
                    }
                }
            }

            return $result;
        }

        function remove_checklist(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                unset($post["csrf_token"]);

                $updated = $this->db->update($this->checklistTbl, array("is_archived"=>1, 'archived_by' => $this->user_data['emp_id']), $post);
                if($updated){
                    $data = array(
                        "archived_table"=>$this->checklistTbl,
                        "archived_id"=>$post['id'],
                        "archived_by"=>$this->user_data["emp_id"]
                    );
    
                    $this->db->insert($this->archivedTable, $data);
                        
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Checklist has been removed.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " archived checklist with db id no. ".$post['id'],"archive", "success", "gcchris", "user");
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to remove checklist!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " error archiving checklist with db id no. ".$post['id'],"archive", "error", "gcchris", "system");
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Category masterfile - Error, No post data found.","archive", "error", "gcchris", "system");
            }
    
            return $resultset;
        }

        function update_checklist(){
            $result = array();
            $post = $this->input->post();

            if(isset($post) && $post){
                $id = $post['id'];
                unset($post['id'], $post['csrf_token']);

                $post['updated_dt'] = date('Y-m-d H:is');
                $post['updated_by'] = $this->user_data['emp_id'];

                $this->db->where('id', $id);
                $query = $this->db->update($this->checklistTbl, $post);

                if($query){
                    $result["response"] = true;
					$result["toastr_msg"] = "Checklist data has been updated.";
					$this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated checklist with db id no. ".$id,"update", "success", "gcchris", "user");
                }else{
                    $result["response"] = false;
					$result["toastr_msg"] = "Failed to update checklist data!";
					$this->core_layout->setEventLog("User ".$this->loggedInUsername. " has error to update checklist","update", "error", "gcchris", "system");
                }
            }else{
                $result["response"] = false;
                $result["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Checklist masterfile - Error, No post data found.","update", "error", "gcchris", "system");
            }

            return $result;
        }

        function getArchivedChecklistDatatableRequest(){
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

        function get_archived_item($limit, $offset, $sortBy, $sortOrder, $search = null){
            $filterFields = array('a.name', 'a.description');
            $resultset = array();

            $sql = "CONCAT(UPPER(TRIM(b.firstname)), ' ',
            CASE WHEN UPPER(TRIM(b.middlename)) != 'N/A' AND UPPER(TRIM(b.middlename)) != 'NONE' AND
                    TRIM(b.middlename) !='' AND b.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(b.middlename, 1, 1), '.') ELSE ''
            END,' ', UPPER(TRIM(b.lastname)),
            CASE WHEN UPPER(TRIM(b.suffix)) != 'N/A' AND
                UPPER(TRIM(b.suffix !='NONE')) AND b.suffix !='' AND
                b.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(c.suffix))) ELSE ''
            END) as emp_add, a.*, CONCAT(UPPER(TRIM(c.firstname)), ' ',
            CASE WHEN UPPER(TRIM(c.middlename)) != 'N/A' AND UPPER(TRIM(c.middlename)) != 'NONE' AND
                    TRIM(c.middlename) !='' AND c.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(c.middlename, 1, 1), '.') ELSE ''
            END,' ', UPPER(TRIM(c.lastname)),
            CASE WHEN UPPER(TRIM(c.suffix)) != 'N/A' AND
                UPPER(TRIM(c.suffix !='NONE')) AND c.suffix !='' AND
                c.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(c.suffix))) ELSE ''
            END) as emp_update";

            $this->db->select($sql);
            $this->db->from($this->checklistTbl.' a');
            $this->db->join($this->employeeTbl.' b', 'b.id = a.added_by', 'LEFT');
            $this->db->join($this->employeeTbl.' c', 'c.id = a.updated_by', 'LEFT');
            $this->db->where('a.is_archived', 1);
            
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

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $resultset = $query->result();
            }

            return $resultset;
        }

        function get_archived_item_count($search){
            $filterFields = array('name', 'description');

            $this->db->from($this->checklistTbl);
            $this->db->where('is_archived', 1);
            
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

            $query = $this->db->get();
            return $query->num_rows();
        }

        function restoreChecklist(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                unset($post["csrf_token"]);

                $updated = $this->db->update($this->checklistTbl, array("is_archived"=>0), $post);
                if($updated){
                        
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Checklist has been restored.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " restored checklist with db id no. ".$post['id'],"restore", "success", "gcchris", "user");
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to restore checklist!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " error restoring checklist with db id no. ".$post['id'],"restore", "error", "gcchris", "system");
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Checklist masterfile - Error, No post data found.","restore", "error", "gcchris", "system");
            }
    
            return $resultset;
        }

        function getChecklists(){
            $result = array();
            $sql = "name as id, name as text, id as checklistId";

            $this->db->select($sql);
            $this->db->where('is_archived', 0);
            $query = $this->db->get($this->checklistTbl);

            if($query->num_rows() > 0){
                $result['results'] = $query->result();
            }

            return $result;
        }

        function employmentChecklist(){
            $result = array();
            $arrData = array();
            $get = $this->input->get();
            
            $this->db->select('name, id');
            $this->db->from($this->checklistTbl);
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){
                    $rs->name = strtoupper($rs->name);
                    $rs->checked = $this->checkChecklist($rs->id, $get['emp_id']);
                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            return array('data' => $result);
        }

        function checkChecklist($id, $emp){
            $result = false;

            $this->db->where('emp_id', $emp);
            $this->db->where('checklist_id', $id);
            $query = $this->db->get($this->checklistDocTbl);

            if($query->num_rows() > 0){
                $result = true;
            }

            return $result;
        }
        
    }