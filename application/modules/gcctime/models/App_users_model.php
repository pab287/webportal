<?php defined('BASEPATH') || exit('No direct script access allowed');
class App_users_model extends CI_Model {
    public function all_users_login(){
        $post = $this->input->post();
        $resultarray = array();
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $filterFields1 = array("b.firstname", "b.lastname", "a.device_name", "a.device_id", "a.user_imei");
        
        $this->db->limit($limit, $offset);
        $this->db->group_by("a.id");
        $this->db->order_by('a.id', 'DESC');
        $this->db->select("b.firstname, b.lastname, a.device_name, a.device_id, a.user_imei, a.emp_id, a.status, a.id");
        $this->db->from("gcctimeutility.app_users a");
        $this->db->join("gccmaster.tblemployees b","b.id = a.emp_id", "LEFT");
        if ($search) {
            $this->db->group_start();
            foreach ($filterFields1 as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
        }
        $query = $this->db->get();
        $rowCount = $query->num_rows();
        if($rowCount > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["id"];
                $data["employee_name"] = mb_strtoupper($this->get_fullname($_query["emp_id"]));
                $data["device_name"] = $_query["device_name"];
                $data["device_id"] = $_query["device_id"];
                $data["status"] = $_query["status"];
                //$data[] = $this->actions($_query["id"]);
                $resultarray[] = $data;
            }
        }

        $count = $this->user_count($search, $limit, $offset, $filterFields1);
        $resultset = array();
        $resultset["recordsTotal"] = $count;
        $resultset["recordsFiltered"] = $count;
        $resultset["data"] = $resultarray;
        return $resultset;
    }

    public function delete_app_user(){
        $post = $this->input->post();
        $app_user_id = $post['app_user_id'];
        if(isset($post['app_user_id']) && $app_user_id != null){
            $this->db->where('id', $app_user_id);
            $del = $this->db->delete("gcctimeutility.app_users");
            if($del){
                return 1;
            }else{
                return 0;
            }
        }
    }

    public function signout_app_user(){
        $post = $this->input->post();
        $app_user_id = $post['app_user_id'];
        if(isset($post['app_user_id']) && $app_user_id != null){
            $array = array();
            $array["status"] = 1;
            $this->db->where('id', $app_user_id);
            $query = $this->db->update("gcctimeutility.app_users",$array);
            if($query){
                return 1;
            }else{
                return 0;
            }
        }
    }

    private function user_count($search=null, $limit = 10, $offset = 0, $filterFields1 = []){
        $this->db->group_by("a.id");
        $this->db->order_by('a.id', 'DESC');
        $this->db->select("b.firstname, b.lastname, a.device_name, a.device_id, a.user_imei, a.emp_id, a.status, a.id");
        $this->db->from("gcctimeutility.app_users a");
        $this->db->join("gccmaster.tblemployees b","b.id = a.emp_id", "LEFT");
        if ($search) {
            $this->db->group_start();
            foreach ($filterFields1 as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
        }
        $query = $this->db->get();
        $rowCount = $query->num_rows();
        return $rowCount;
    }

    private function get_fullname($emp_id){
        $dd = $this->core_layout->getEmployeeData($emp_id);
        if(isset($dd['display_name_1']) && $dd['display_name_1']){
            $result['display_name'] = $dd['display_name_1'];
        }else{
            $result['display_name'] = "No Assigned Name";
        }

        return $result['display_name'];
    }

    private function actions($id){
        $_actions = "";
        $_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDelete btn-delete' onclick='delete_app_user(".$id.")' title='Delete'><i class='la la-trash-o'></i></a>";
        return $_actions;
    }

        public function getAppAttenanceUsersRequest() {
        $post = $this->input->post();
        $resultset = array();
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? trim($post["search"]['value']) : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
        $rowData = $this->getAppAttenanceUserData($search, $limit, $offset, $sortBy, $sortOrder);
        $q = $this->db->last_query();
        $rowCount = $this->getAppAttenanceUserDataCount($search);
        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;
        $resultset["q"] = $q;

        return $resultset;
    }

    protected function getAppAttenanceUserQuery($search = null){
        $filterFields = array("a.device_name", "a.device_id", "emp.firstname", "emp.middlename", "emp.lastname", "emp.suffix",
        "CONCAT(emp.firstname, ' ', emp.lastname)", "CONCAT(emp.lastname, ', ', emp.firstname)", "DATE_FORMAT(a.last_logged_in, '%M %e, %Y')", "DATE_FORMAT(a.last_logged_in, '%M %e, %Y %l:%i %p')");
        $this->db->select("CONCAT(UPPER(TRIM(emp.firstname)), ' ',
        CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
            THEN CONCAT(UPPER(SUBSTR(emp.middlename, 1, 1)), '.') ELSE ''
        END,' ', UPPER(TRIM(emp.lastname)),
        CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
            UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
            emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
        END) as employee_name, a.device_name, a.device_id, a.emp_id, a.status, a.last_logged_in, a.allow_app_user, a.id");
        $this->db->from("gcctimeutility.app_users a");
        $this->db->join("gccmaster.tblemployees emp","emp.id = a.emp_id", "UPPER");
        if ($search) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) { $this->db->like($field, $search, "both"); }
                else { $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();
        }
        $this->db->group_by("a.id");
        return $this->db;
    }

    protected function getAppAttenanceUserData($search, $limit, $offset, $sortBy, $sortOrder) {
        $dbQuery = $this->getAppAttenanceUserQuery($search);
        if ($limit != -1) { $dbQuery->limit($limit, $offset); }
        if (isset($sortOrder)) {
            $i = $sortOrder[0]['column'];
            $dbQuery->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        } else { $dbQuery->order_by('a.id', 'desc'); }
        return $dbQuery->get()->result();
    }

    protected function getAppAttenanceUserDataCount($search) {
        $dbQuery = $this->getAppAttenanceUserQuery($search);
        return $dbQuery->get()->num_rows();
    }

    public function updateAllowUserAccess(){
        $post = $this->input->post();
        $resultset = array();
        if(isset($post["id"]) && $post["id"]){
            $allowAppUser = isset($post["allow_app_user"]) && $post["allow_app_user"] ? 1 : 0;
            $arrData = array("allow_app_user" => $allowAppUser);
            if($allowAppUser == 0){ $arrData["status"] = 1; }
            $this->db->where("id", $post["id"]);
            $updated = $this->db->update("gcctimeutility.app_users", $arrData);
            if($updated && $this->db->affected_rows() > 0){
                if($allowAppUser == 0){ $this->db->update("gccmaster.tblusers", array("mobile_token" => ''), array("emp_id" => $post["emp_id"])); }
                $resultset["response"] = true;
                $resultset["toastr_msg"] = "App User Access has been updated";
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to update App User Access";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Failed to update App User Access, no post data found!!";
        }
        return $resultset;
    }
}