<?php defined('BASEPATH') || exit('No direct script access allowed');
    class License_model extends CI_Model{
        protected $licenseTable = "gcchris.tbl_license";
        protected $archivedTable = "gccmaster.archived_items";
        
        function __construct(){
            parent::__construct();
            $this->load->model("hris/employee_model", "adm_employee");
            $this->load->model("core/datatable_model","dt_model");
            $this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->loggedinData["username"];
        }

        function getLicenseDatatableRequest(){
            $post = $this->input->post();
            // var_dump($this->core_layout->getCurrentActions());
            if($post){
                $orderx = (isset($post["order"]) && $post["order"])? $post["order"]: false;
                $columns = array("description", "code", "add_date", "add_by", "id", "type", "is_archived");
                $dir = "DESC";
                $order = "id";
                if($orderx){
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                    
                $draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
                $start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
                $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
                $dtTemp = $this->dt_model->dataTable();
                $dtTemp->setTable($this->licenseTable);
                $dtTemp->setParameterFields($columns);
                
                $parameters = array();
                $parameters["is_archived"] = (isset($post['is_archived']) && $post['is_archived'] == 1) ? 1 : 0;
                
                $dtTemp->setWhereParameters($parameters);
    
                $totalData = $dtTemp->dtAllPostsCount();
                $totalFiltered = $totalData;
                
                if(empty($searchValue)){
                    $posts = $dtTemp->dtAllPosts($limit, $start, $order, $dir);
                }else {
                    $posts = $dtTemp->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTemp->dtPostSearchCount($searchValue);
                }
                
                $data = array();
                if(!empty($posts)){
                    foreach ($posts as $pst){
                        $added_by = $this->core_layout->getEmployeeData($pst->add_by);
                        $nestedData = array();
                        $nestedData['id'] = $pst->id;
                        $nestedData['code'] = $pst->code;
                        $nestedData['type'] = $pst->type;
                        $nestedData['description'] = $pst->description;
                        $nestedData['created_by'] = (isset($added_by["display_name_1"]) && $added_by["display_name_1"]) ? $added_by["display_name_1"] : "";;
                        $nestedData['add_date'] = $pst->add_date;
                        $data[] = $nestedData;
                    }
                }
                return array(
                        "draw" => intval($draw),  
                        "recordsTotal" => intval($totalData),  
                        "recordsFiltered" => intval($totalFiltered), 
                        "data"            => $data,   
                        );
            }else{
                return array(
                    "draw"=>1,
                    "recordsTotal"=>0,
                    "recordsFiltered"=>0,
                    "data"=>array(),
                    );
            }
        }

        function getLicenseModalContent($content="add"){
            $resultset = array();
            $html = "";
            $arrData = array();
    
            $employeeList = $this->adm_employee->getCurrentEmployees();
            
            if($content == "add"){
                $html = $this->load->view("hris/masterfile/license/modals/add_content", array("data" => $arrData), true);
            }
            if($content == "edit"){
                $post = $this->input->post();
                if(isset($post) && $post){
                    unset($post["csrf_token"]);
                    $tempCompany = $this->db->get_where($this->licenseTable, $post);
                    if($tempCompany->num_rows() == 1){
                        $arrData = $tempCompany->row();
                    }
                }
                $html = $this->load->view("hris/masterfile/license/modals/edit_content", array("data"=>$arrData), true);
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

        function setModalLicense(){
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            $post['add_date'] = date('Y-m-d H:i:s');
            $post['add_by'] = $session["emp_id"];


            $post = array_map('strtoupper', $post);

            $filter = $this->db->get_where($this->licenseTable, array('description' => $post['description'], 'type' => $post['type']))->num_rows();

            if($filter > 0){
                $resultset['response'] = false;
                $resultset['toastr_msg'] = 'License already exists!';
            }else{
                $query = $this->db->insert($this->licenseTable, $post);
                
                if($query){
                    $resultset['response'] = true;
                    $resultset['toastr_msg'] = 'Saved!';

                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " added new license: ".$post['description'],"insert", "success", "gcchris", "user");
                }else{
                    $resultset['response'] = true;
                    $resultset['toastr_msg'] = 'Failed to save license!';
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed inserting new license: ".$post['description'],"insert", "error", "gcchris", "system");
                }
            }

            return $resultset;
        }

        function removeCurrentLicense(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                unset($post["csrf_token"]);
                $updated = $this->db->update($this->licenseTable, array("is_archived"=>1), $post);
                $currentLicenseData = $this->getLicenseData($post["id"]);
                if($updated){
                    $session = $this->core_layout->getCurrentSession();
                    $data = array(
                        "archived_table"=>$this->licenseTable,
                        "archived_id"=>$post["id"],
                        "archived_by"=>$session["emp_id"]
                    );
    
                    $this->db->insert($this->archivedTable, $data);
                        
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "License has been removed.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has archived license: ".$currentLicenseData->description,"archive", "success", "gcchris", "user");
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to remove license!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has failed archiving license: ".$currentLicenseData->description,"archive", "error", "gcchris", "system");
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("License masterfile - Error, No post data found.","archive", "error", "gcchris", "system");
            }
    
            return $resultset;
        }

        function updateModalLicense(){
            $resultset = array();
            $post = $this->input->post();
            $session = $this->core_layout->getCurrentSession();

            $post = array_map('strtoupper', $post);
            $post['update_date'] = date('Y-m-d H:i:s');
            $post['update_by'] = $session["emp_id"];
            $id = $post['id'];
            unset($post['id']);
            $currentLicenseData = $this->getLicenseData($id);
            $this->db->where('id', $id);
            $query = $this->db->update($this->licenseTable, $post);
            if($query){
                $resultset['response'] = true;
                $resultset['toastr_msg'] = 'Updated!';
                unset($post['update_date']); 
                unset($post['update_by']);
                $changes = $this->logChanges($currentLicenseData ,$post);
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " update license: ".$post['description']." ".$changes,"update", "success", "gcchris", "user");
            }else{
                $resultset['response'] = true;
                $resultset['toastr_msg'] = 'Failed to save license!';
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed to update license: ".$post['description'],"update", "error", "gcchris", "user");
            }

            return $resultset;
        }

        function getLicense(){
            $result = array();
            $get = $this->input->get();

            // if(isset($get['id']) && $get['id']){
            //     $this->db->select('CONCAT(id, "-",type) as id, type as text');
            //     $this->db->from($this->licenseTable);
            //     $this->db->where('is_archived', 0);
    
            //     if(isset($get['q']) && $get['q']){
            //         $this->db->like('type', $get['q']);
            //     }
    
            //     $query = $this->db->get();

            //     if($query->num_rows() > 0){
            //         $result['results'] = $query->result();
            //     }else{
            //         $this->
            //     }
            // }else{
                
            //     // return array('results' => $query->result());
            // }
            $this->db->select('CONCAT(id, "-",description) as id, description as text');
            $this->db->from($this->licenseTable);
            $this->db->where('is_archived', 0);

            if(isset($get['q']) && $get['q']){
                $this->db->like('description', $get['q']);
            }

            $query = $this->db->get();
            $result['results'] = $query->result();

            $newObject = array(
                'id' => 'Certificate',
                'text' => 'Certificate',
            );

            array_unshift($result['results'], $newObject);

            return $result;

        }

        function restoreCurrentLicense(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                unset($post["csrf_token"]);
                $updated = $this->db->update($this->licenseTable, array("is_archived"=>0), $post);
                $currentLicenseData = $this->getLicenseData($post["id"]);
                if($updated){
                    // $session = $this->core_layout->getCurrentSession();
                    // $data = array(
                    //     "archived_table"=>$this->licenseTable,
                    //     "archived_id"=>$post["id"],
                    //     "archived_by"=>$session["emp_id"]
                    // );
    
                    // $this->db->insert($this->archivedTable, $data);
                    
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "License has been restored.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has restored license: ".$currentLicenseData->description,"restore", "success", "gcchris", "user");
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to restore license!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has failed restoring license: ".$currentLicenseData->description,"restore", "error", "gcchris", "system");
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
                $this->core_layout->setEventLog("License masterfile - Error, No post data found.","restore", "error", "gcchris", "system");
            }
    
            return $resultset;
        }

        private function logChanges($currentData, $newData) {
            if (is_object($currentData)) {
                $currentData = get_object_vars($currentData);
            }
            if (is_object($newData)) {
                $newData = get_object_vars($newData);
            }
        
            $changes = array();
            $changesString = '';
            foreach ($currentData as $field => $value) {
                if (isset($newData[$field]) && $newData[$field]!== $value) {
                    $changes[$field] = array(
                        'old' => $value,
                        'new' => $newData[$field]
                    );
                }
            }
            foreach ($changes as $field => $change) {
                $changesString.= " Field: $field, from: $change[old], to: $change[new]\n";
            }
            return $changesString;
        }

        private function getLicenseData($id) {
            $this->db->select("*");
            $this->db->from($this->licenseTable);
            $this->db->where('id', $id);
            $query = $this->db->get(); 
            $result = $query->row();
            $this->db->reset_query();
            return $result;
        }


    }