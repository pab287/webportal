<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Category_m extends CI_Model{
        protected $tblCategory = 'gccppm.tblcategory';
        protected $archivedTable = "gccmaster.archived_items";
        private $user_data = array();

        function __construct(){
            parent::__construct();
            $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->user_data["username"];
        }

        function getCategoryDatatableRequest(){
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
            $filterFields = array('name', 'description', 'code');
            $resultset = array();

            $this->db->from($this->tblCategory);
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
            $filterFields = array('name', 'description', 'code');

            $this->db->from($this->tblCategory);
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

        function getCategoryModalContent($content="add"){
            $resultset = array();
            $html = "";
            $arrData = array();
    
            if($content == "add"){
                $html = $this->load->view("qms/masterfile/category/modals/add_content", null, true);
            }
            if($content == "edit"){
                $post = $this->input->post();
                if(isset($post) && $post){
                    unset($post["csrf_token"]);
                    $tempCategory = $this->db->get_where($this->tblCategory, $post);
                    if($tempCategory->num_rows() == 1){
                        $arrData = $tempCategory->row();
                    }
                }
                $html = $this->load->view("qms/masterfile/category/modals/edit_content", array("data"=>$arrData), true);
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

        function add_category(){
            $result = array();
            $post = $this->input->post();

            if(isset($post) && $post){
                unset($post["csrf_token"]);

                $post['added_dt'] = date('Y-m-d H:is');
                $post['added_by'] = $this->user_data['emp_id'];
                $post['code'] = strtoupper($post['code']);

                $this->db->where('name', $post['name']);
                $check_category = $this->db->get($this->tblCategory);

                if($check_category->num_rows() > 0){
                    $result["response"] = false;
                    $result["toastr_msg"] = "Category name already exist!";
                }else{
                    
                    $this->db->where('code', $post['code']);
                    $check_code = $this->db->get($this->tblCategory);

                    if($check_code->num_rows() > 0){
                        $result["response"] = false;
                        $result["toastr_msg"] = "Category code already exist!";
                    }else{
                        $query = $this->db->insert($this->tblCategory, $post);
                        if($query){
                            $result["response"] = true;
                            $result["toastr_msg"] = "Category data has been added.";
                            $this->core_layout->setEventLog("User ".$this->loggedInUsername. " inserted new category with db id no. ".$this->db->insert_id(),"insert", "success", "gccppm", "user");
                        }else{
                            $result["response"] = false;
                            $result["toastr_msg"] = "Failed saving category data!";
                            $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has error inserting category","insert", "error", "gccppm", "system");
                        }
                    }
                }

            }else{
                $result["response"] = false;
                $result["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Category masterfile - Error, No post data found.","insert", "error", "gccppm", "system");
            }

            return $result;
        }

        function update_category(){
            $result = array();
            $post = $this->input->post();

            if(isset($post) && $post){
                $id = $post['id'];
                unset($post['id'], $post['csrf_token']);

                $post['updated_dt'] = date('Y-m-d H:is');
                $post['updated_by'] = $this->user_data['emp_id'];
                $post['code'] = strtoupper($post['code']);

                $this->db->where('id', $id);
                $query = $this->db->update($this->tblCategory, $post);

                if($query){
                    $result["response"] = true;
					$result["toastr_msg"] = "Category data has been updated.";
					$this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated category with db id no. ".$id,"update", "success", "gccppm", "user");
                }else{
                    $result["response"] = false;
					$result["toastr_msg"] = "Failed to update category data!";
					$this->core_layout->setEventLog("User ".$this->loggedInUsername. " has error to update category","update", "error", "gccppm", "system");
                }
            }else{
                $result["response"] = false;
                $result["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Category masterfile - Error, No post data found.","update", "error", "gccppm", "system");
            }

            return $result;
        }

        function remove_category(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                unset($post["csrf_token"]);

                $updated = $this->db->update($this->tblCategory, array("is_archived"=>1, 'archived_by' => $this->user_data['emp_id']), $post);
                if($updated){
                    $data = array(
                        "archived_table"=>$this->tblCategory,
                        "archived_id"=>$post['id'],
                        "archived_by"=>$this->user_data["emp_id"]
                    );
    
                    $this->db->insert($this->archivedTable, $data);
                        
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Category has been removed.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " archived category with db id no. ".$post['id'],"archived", "success", "gccppm", "user");
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to remove category!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " error archiving category with db id no. ".$post['id'],"archived", "error", "gccppm", "system");
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Category masterfile - Error, No post data found.","archived", "error", "gccppm", "system");
            }
    
            return $resultset;
        }

        function get_category_list(){
            $result = array();

            $sql = "id, name as text, code";

            $this->db->select($sql);
            $this->db->where('is_archived', 0);
            $query = $this->db->get($this->tblCategory);

            if($query->num_rows() > 0){
                $result = $query->result();
            }

            return $result;
        }

        function getArchivedCategoryDatatableRequest(){
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
            $filterFields = array('name', 'description');
            $resultset = array();

            $this->db->from($this->tblCategory);
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

            $this->db->from($this->tblCategory);
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

        function restore_category(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                unset($post["csrf_token"]);

                $updated = $this->db->update($this->tblCategory, array("is_archived"=>0), $post);
                if($updated){
                    // $data = array(
                    //     "archived_table"=>$this->tblCategory,
                    //     "archived_id"=>$post['id'],
                    //     "archived_by"=>$this->user_data["emp_id"]
                    // );
    
                    // $this->db->insert($this->archivedTable, $data);
                        
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Category has been restored.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " restored category with db id no. ".$post['id'],"restore", "success", "gccppm", "user");
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to restore category!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " error restoring category with db id no. ".$post['id'],"restore", "error", "gccppm", "system");
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("Category masterfile - Error, No post data found.","restore", "error", "gccppm", "system");
            }
    
            return $resultset;
        }
    }