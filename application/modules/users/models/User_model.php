<?php defined('BASEPATH') || exit('No direct script access allowed');
class User_model extends CI_Model{
    private $timestamp = null;

    public function __construct(){
        parent::__construct();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model", "dt_model");
        $this->load->model("ams/Utilities_model", "utilities");
        $this->load->model("sms/services/gateway_model","gateway");
        $this->timestamp = new DateTime(null, new DateTimeZone('Asia/Manila'));
    }

    private function getUserData(){
        return $this->core_layout->getUserLoggedIn();
    }

    function getGroup(){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `id`,`group_name` FROM gccmaster.tblgroups WHERE `group_name` LIKE '%{$get['q']}%' ORDER BY `group_name` ASC");
        } else {
            $query = $this->db->query("SELECT `id`,`group_name` FROM gccmaster.tblgroups ORDER BY `group_name` ASC");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["group_name"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    public function getEmployee(){
        $get = $this->input->get();
        $sqlSelect = "CONCAT(UPPER(TRIM(emp.firstname)), ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(UPPER(SUBSTR(emp.middlename, 1, 1)), '.') ELSE ''
            END,' ', UPPER(TRIM(emp.lastname)),
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
            END) as text, emp.id as id";
        $this->db->select($sqlSelect);
        $this->db->from('gccmaster.tblemployees as emp');
        $this->db->join('gccmaster.tblusers as user', 'user.emp_id = emp.id', 'left');
        if (isset($get['q'])) {
            $this->db->group_start();
            $this->db->like('emp.firstname', $get['q'], "BOTH");
            $this->db->or_like('emp.middlename', $get['q'], "BOTH");
            $this->db->or_like('emp.lastname', $get['q'], "BOTH");
            $this->db->group_end();
        }
        $this->db->where('emp.employee_status', 'Active');
        $this->db->where("user.id IS NULL");
        $this->db->order_by('emp.firstname', 'ASC');
        $query =$this->db->get();

        return array("results" => $query->result_array(), "_query" => $this->db->last_query());
    }

    public function save_user($data)
    {
        $this->db->insert('gccmaster.tblusers', $data);
        return $this->db->insert_id();
    }

    public function edit_user($id){
        $sql = "a.id, a.email, a.username, a.password, a.role_id, TRIM(UPPER(b.description)) as role_name, a.telegram_chat_id, a.is_important,
            CASE 
                WHEN a.email LIKE '%@%.%' AND a.email NOT LIKE '%..%' AND a.email NOT LIKE '@%' 
            THEN '1' ELSE '0' END AS has_email,
            CONCAT(UPPER(TRIM(emp.firstname)), ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(UPPER(SUBSTR(emp.middlename, 1, 1)), '.') ELSE ''
            END,' ', UPPER(TRIM(emp.lastname)),
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix)) != 'NONE' AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
            END) as account_name";
        $this->db->select($sql);
        $this->db->from("gccmaster.tblusers a");
        $this->db->join("gccmaster.user_role b", "b.id = a.role_id", "LEFT");
        $this->db->join("gccmaster.tblemployees emp", "a.emp_id = emp.id", "INNER");
        $this->db->where('a.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function update_user($where, $data)
    {
        $this->db->update('gccmaster.tblusers', $data, $where);
        return $this->db->affected_rows();
    }

    public function processSuspendAccount($id)
    {
        $user = $this->core_layout->getUserLoggedIn();
        $employee_id = $user['employee_id'];
        $resultSet = array(
            "success" => false,
            "message" => $this->db->error()
        );

        $data = array("is_suspended" => 1, "suspended_by" => $employee_id, "suspended_dt" => $this->timestamp->format("Y-m-d"));
        $where = array("id" => $id);
        $updateResult = $this->db->update("gccmaster.tblusers", $data, $where);

        $tempData = $this->core_layout->getUserData($id);
        $tempName = (object) $tempData;
        $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No assigned name";
            
        if ($updateResult) {
            $resultSet['success'] = true;
            $resultSet['message'] = "User account of `{$tempName}` has been suspended.";
        }else{
            $resultSet['success'] = false;
            $resultSet['message'] = "Failed to suspend the account of `{$tempName}`!";
        }

        $tempStatus = ($resultSet["success"])? "success": "error";
		$this->core_layout->logNotification($resultSet["message"], $tempStatus, "users");
        return $resultSet;
    }

    public function processUnsuspendAccount($id)
    {
        $resultSet = array(
            "success" => false,
            "message" => $this->db->error()
        );

        $data = array("is_suspended" => 0);
        $where = array("id" => $id);
        $updateResult = $this->db->update("gccmaster.tblusers", $data, $where);

        $tempData = $this->core_layout->getUserData($id);
        $tempName = (object) $tempData;
        $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No assigned name";

        if ($updateResult) {
            $resultSet['success'] = true;
            $resultSet['message'] = "User account of `{$tempName}` has been removed from suspension.";
        }else{
            $resultSet['success'] = false;
            $resultSet['message'] = "Failed to remove the account of `{$tempName}` from suspension!";
        }

        $tempStatus = ($resultSet["success"])? "success": "error";
		$this->core_layout->logNotification($resultSet["message"], $tempStatus, "users");
        return $resultSet;
    }

    public function getSuspendedUsersList(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"1", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $filter = (isset($post['filter']) && $post['filter']) ? $post['filter'] : 0;

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_suspended_list($limit, $offset, $sortBy, $sortOrder, $search, $filter);
        $rowCount = $this->get_suspended_list_count($search, $filter);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_suspended_list($limit, $offset, $sortBy, $sortOrder, $search = null, $filter = 0){
        $filterFields = array("users.email", 'users.username', 'employees.firstname', 'employees.lastname', 'employees.middlename', 'employees2.firstname', 'employees2.lastname', 'employees2.middlename', "DATE_FORMAT(users.suspended_dt, '%M %e, %Y')");
        $resultset = array();

        $sql = "users.id, users.username, users.email, UPPER(CONCAT(employees.lastname,
                CASE WHEN UPPER(TRIM(employees.suffix)) != 'N/A' AND
                    UPPER(TRIM(employees.suffix !='NONE')) AND employees.suffix !='' AND
                    employees.suffix IS NOT NULL THEN CONCAT(' ', employees.suffix) ELSE ''
                END, ', ', employees.firstname, ' ',
                CASE WHEN UPPER(TRIM(employees.middlename)) != 'N/A' AND UPPER(TRIM(employees.middlename)) != 'NONE' AND
                        TRIM(employees.middlename) !='' AND employees.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(employees.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name,
                UPPER(CONCAT(employees2.lastname,
                CASE WHEN UPPER(TRIM(employees2.suffix)) != 'N/A' AND
                    UPPER(TRIM(employees2.suffix !='NONE')) AND employees2.suffix !='' AND
                    employees2.suffix IS NOT NULL THEN CONCAT(' ', employees2.suffix) ELSE ''
                END, ', ', employees2.firstname, ' ',
                CASE WHEN UPPER(TRIM(employees2.middlename)) != 'N/A' AND UPPER(TRIM(employees2.middlename)) != 'NONE' AND
                        TRIM(employees2.middlename) !='' AND employees2.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(employees2.middlename, 1, 1), '.') ELSE ''
                END)) as suspended_by, users.suspended_dt";
        
        $this->db->select($sql);
        $this->db->join('gccmaster.tblemployees as employees', 'users.emp_id = employees.id', 'INNER');
        $this->db->join('gccmaster.tblemployees as employees2', 'users.suspended_by = employees2.id', 'LEFT');
        $this->db->from('gccmaster.tblusers as users');

        $this->db->where('users.is_suspended', 1);

        if ($filter == 1) { $this->db->where('users.suspended_by', 0); }
        if ($filter == 2) { $this->db->where('users.suspended_by >', 0);}

        $this->db->group_start();
            $this->db->where('employees.employee_status', 'Active');
            $this->db->or_where('employees.employee_status', 'Inactive');
        $this->db->group_end();

        if ($search) {
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

        if ($query->num_rows() > 0) {
            $resultset = $query->result();
        }

        return $resultset;
    }

    function get_suspended_list_count($search = null, $filter = 0){
        $filterFields = array("users.email", 'users.username', 'employees.firstname', 'employees.lastname', 'employees.middlename', 'employees2.firstname', 'employees2.lastname', 'employees2.middlename', "DATE_FORMAT(users.suspended_dt, '%M %e, %Y')");

        $sql = "users.id, users.username, users.email, UPPER(CONCAT(employees.lastname,
                CASE WHEN UPPER(TRIM(employees.suffix)) != 'N/A' AND
                    UPPER(TRIM(employees.suffix !='NONE')) AND employees.suffix !='' AND
                    employees.suffix IS NOT NULL THEN CONCAT(' ', employees.suffix) ELSE ''
                END, ', ', employees.firstname, ' ',
                CASE WHEN UPPER(TRIM(employees.middlename)) != 'N/A' AND UPPER(TRIM(employees.middlename)) != 'NONE' AND
                        TRIM(employees.middlename) !='' AND employees.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(employees.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name,
                UPPER(CONCAT(employees2.lastname,
                CASE WHEN UPPER(TRIM(employees2.suffix)) != 'N/A' AND
                    UPPER(TRIM(employees2.suffix !='NONE')) AND employees2.suffix !='' AND
                    employees2.suffix IS NOT NULL THEN CONCAT(' ', employees2.suffix) ELSE ''
                END, ', ', employees2.firstname, ' ',
                CASE WHEN UPPER(TRIM(employees2.middlename)) != 'N/A' AND UPPER(TRIM(employees2.middlename)) != 'NONE' AND
                        TRIM(employees2.middlename) !='' AND employees2.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(employees2.middlename, 1, 1), '.') ELSE ''
                END)) as suspended_by, users.suspended_dt";
        
        $this->db->select($sql);
        $this->db->join('gccmaster.tblemployees as employees', 'users.emp_id = employees.id', 'INNER');
        $this->db->join('gccmaster.tblemployees as employees2', 'users.suspended_by = employees2.id', 'LEFT');
        $this->db->from('gccmaster.tblusers as users');

        if ($filter == 1) { $this->db->where('users.suspended_by', 0); }
        if ($filter == 2) { $this->db->where('users.suspended_by >', 0);}

        $this->db->where('users.is_suspended', 1);
        $this->db->group_start();
            $this->db->where('employees.employee_status', 'Active');
            $this->db->or_where('employees.employee_status', 'Inactive');
        $this->db->group_end();

        if ($search) {
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

    public function getSuspendedUsersListv1()
    {
        $tableConfig = $this->input->post();
        $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
        $table = "gccmaster.tblusers users";
        $searchFields = "CONCAT(users.email, users.username, employees.firstname, employees.lastname, employees.middlename, employees2.firstname, employees2.lastname, employees2.middlename, DATE_FORMAT(users.suspended_dt, '%M %e, %Y'))";

        $joinArr = array(
            array("table" => "gccmaster.tblemployees employees", "condition" => "users.emp_id = employees.id", "option" => "INNER"),
            array("table" => "gccmaster.tblemployees employees2", "condition" => "users.suspended_by = employees2.id", "option" => "LEFT")
        );
        $where = array("users.is_suspended" => 1, "employees.employee_status" => "Active");

        $resultSet = array();
        $this->db->select("users.id, users.username, users.email, UPPER(CONCAT(employees.lastname,
                CASE WHEN UPPER(TRIM(employees.suffix)) != 'N/A' AND
                    UPPER(TRIM(employees.suffix !='NONE')) AND employees.suffix !='' AND
                    employees.suffix IS NOT NULL THEN CONCAT(' ', employees.suffix) ELSE ''
                END, ', ', employees.firstname, ' ',
                CASE WHEN UPPER(TRIM(employees.middlename)) != 'N/A' AND UPPER(TRIM(employees.middlename)) != 'NONE' AND
                        TRIM(employees.middlename) !='' AND employees.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(employees.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name,
                UPPER(CONCAT(employees2.lastname,
                CASE WHEN UPPER(TRIM(employees2.suffix)) != 'N/A' AND
                    UPPER(TRIM(employees2.suffix !='NONE')) AND employees2.suffix !='' AND
                    employees2.suffix IS NOT NULL THEN CONCAT(' ', employees2.suffix) ELSE ''
                END, ', ', employees2.firstname, ' ',
                CASE WHEN UPPER(TRIM(employees2.middlename)) != 'N/A' AND UPPER(TRIM(employees2.middlename)) != 'NONE' AND
                        TRIM(employees2.middlename) !='' AND employees2.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(employees2.middlename, 1, 1), '.') ELSE ''
                END)) as suspended_by, users.suspended_dt");
        $this->db->where($where);
        $this->db->like($searchFields, $pageOptions->search, "both");
        foreach ($joinArr as $join) {
            $this->db->join($join["table"], $join["condition"], $join["option"]);
        }

        if ($pageOptions->length > -1) {
            $this->db->limit($pageOptions->length, $pageOptions->start);
        }

        $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
        $data = $this->db->get($table)->result();

        $lastQ = $this->db->last_query();
        $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
        $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
        $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
        $resultSet["data"] = $data;
        $resultSet["_q"] = $lastQ;
        return $resultSet;
    }

    function getActiveUserList()
    {
        $tableConfig = $this->input->post();
        $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
        $table = "gccmaster.tblusers users";
        $searchFields = "CONCAT(users.email, employees.firstname, employees.lastname, employees.middlename, users.username)";

        $joinArr = array(
            array("table" => "gccmaster.tblemployees employees", "condition" => "users.emp_id = employees.id", "option" => "INNER"),
        );
        $where = "users.is_suspended = 0 AND (employees.employee_status IN('Active') OR employees.employee_status IS NULL)";

        $resultSet = array();
        $this->db->select("users.id, users.email, employees.firstname, employees.lastname,employees.middlename, employees.employee_status, users.username");
        $this->db->where($where);
        $this->db->like($searchFields, $pageOptions->search, "both");
        foreach ($joinArr as $join) {
            $this->db->join($join["table"], $join["condition"], $join["option"]);
        }

        if ($pageOptions->length > -1) {
            $this->db->limit($pageOptions->length, $pageOptions->start);
        }

        $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
        $data = $this->db->get($table)->result();

        $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
        $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
        $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
        $resultSet["data"] = $data;
        return $resultSet;
    }

    function resetSelectedUserPassword($user_id)
    {
        $resultSet = array(
            "success" => false,
            "message" => $this->db->error()
        );
        $username = $this->db
            ->get_where("gccmaster.tblusers users", array("id" => $user_id))
            ->row("username");

        $this->db->reset_query();

        $this->db->set("password", MD5($username));
        $this->db->set("reset_attempts", "reset_attempts+1", FALSE);
        $this->db->where("id", $user_id);
        if ($this->db->update("gccmaster.tblusers users")) {
            $resultSet["success"] = true;
            $resultSet["message"] = "Password reset successful.";
        }

        return $resultSet;
    }

    function processChangePassword($data)
    {
        $id = $data->id;
        $reset_pin = $data->reset_pin;
        unset($data->id, $data->reset_pin);
        $emp_data = $this->db->get_where("gccmaster.tblemployees",array("id"=>$id))->row();
        $emp_name = $emp_data->firstname." ".$emp_data->lastname;
        
        $resultSet = array("success" => false, "message" => $this->db->error());

        $validatePin = $this->db->where(array("emp_id" => $id, "reset_pin" => $reset_pin))->count_all_results("gccmaster.tblusers");
        $this->db->reset_query();

        if ($validatePin >= 1) {
            $field = array("password" => MD5($data->password),"remember_token" => null);
            $this->db->where(array("emp_id" => $id))->update("gccmaster.tblusers", $field);
            $this->core_layout->setEventLog("User ".$emp_name." has changed his/her password.","change webportal password", "success", "gcchris", "user");
            $resultSet["success"] = true;
            $resultSet["message"] = "Password was successfully changed.";
        } else {
            $this->core_layout->setEventLog("User ".$emp_name." has failed in changing his/her webportal password.","change password", "error", "gcchris", "system");
            $resultSet["success"] = false;
            $resultSet["message"] = "Verification failed! PIN entered does not match.";
        }

        return $resultSet;
    }

    function processChangePin($data)
    {
        $id = $data->id;
        $old_pin = $data->old_pin;
        $new_pin = $data->new_pin;
        $emp_data = $this->db->get_where("gccmaster.tblemployees",array("id", $id))->row();
        $emp_name = $emp_data->firstname." ".$emp_data->lastname;
        $resultSet = array("success" => false, "message" => $this->db->error());

        $validatePin = $this->db->where(array("emp_id" => $id, "reset_pin" => $old_pin))->count_all_results("gccmaster.tblusers");
        $this->db->reset_query();
        
        if ($validatePin >= 1) {
            $field = array("reset_pin" => $new_pin);
            $this->db->where(array("emp_id" => $id))->update("gccmaster.tblusers", $field);
            $this->core_layout->setEventLog("User ".$emp_name." has changed his/her pin.","change pin", "success", "gcchris", "user");
            $resultSet["success"] = true;
            $resultSet["message"] = "Pin was successfully changed.";
        } else {
            $this->core_layout->setEventLog("User ".$emp_name." has failed in changing his/her pin.","change pin", "error", "gcchris", "system");
            $resultSet["success"] = false;
            $resultSet["message"] = "Verification failed! Incorrect old PIN.";
        }

        return $resultSet;
    }

    function verifyPin($data)
    {
        $resultSet = array("success" => false, "message" => $this->db->error());
        $where = array("emp_id" => $data->id, "reset_pin" => $data->reset_pin);
        $validatePin = $this->db->where($where)->get("gccmaster.tblusers")->num_rows();

        if ((int)$validatePin >= 1) {
            $resultSet["success"] = true;
            $resultSet["message"] = "PIN Validated.";
        } else {
            $resultSet["success"] = false;
            $resultSet["message"] = "Verification failed! PIN entered does not match.";
        }

        return $resultSet;
    }

    function forgetPin($email=true){
        $post = $this->input->post();
        $resultset = array();
        if(isset($post)){
            $random_pin = mt_rand(000000, 999999);
            $data = array("reset_pin" => $random_pin);
            $where = array("emp_id" => $post['id']);
            $updatePin = $this->db->update("gccmaster.tblusers", $data, $where);
            $reset_pin = $this->db->get_where("gccmaster.tblusers", $where)->row();
            $emp_details = $this->db->get_where("gccmaster.tblemployees", array("id"=>$post['id']))->row();
            if($updatePin){
                    $data = array();
                    $data['reset_pin'] = $reset_pin->reset_pin;
                    $data['email'] = $reset_pin->email;
                    $data['name'] = $emp_details->firstname." ".$emp_details->lastname;
                    $messageContent = $this->load->view("forget_pin", $data, true);
                    $content = $messageContent;
                    if($data){
                        if($email){
                            $module = "forget_pin";
                            $email_title = "Forgot Pin";
                            $content_title = "Forgot Pin Request";
                            $overrideMailer = array();
                            $overrideMailer['send_to'] = array($reset_pin->email);
                            if($content){
                                $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
                                if($sent){
                                    $resultset['result'] = true;
                                }else{
                                    echo "Failed Sending Email!";
                                    show_error($this->email->print_debugger()); 
                                }
                            }else{
                                echo "No email content";
                            }
                        }else{
                            echo $messageContent;
                        }
                    }else{
                        return false;
                    }
                
            }else{
                return false;
            }
        }else{
            return false;
        }

        return $resultset;
    }

    public function activate2FA(){
        try {
            $this->db->trans_start();
            $post = $this->input->post();
            $user = $this->core_layout->getUserLoggedIn();
            $status =  $post['status'];
            $url = site_url('login/logout');
            $response = [
                'success' => false,
                'message' => '',
                'redirect' => $url,
            ];
    
            if ($status == 1) {
                $this->deactivate2FA($user['id']);
                $response['message'] = '2FA Deactivated';
            } else {
                $this->activate2FAWithValidation($user);
                $response['message'] = '2FA Activated';
            }
    
            $this->db->trans_complete();
    
            if ($this->db->trans_status() == FALSE) {
                throw new Exception('Database transaction failed');
            }
    
            $response['success'] = true;
            return $response;
    
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function deactivate2FA($userId){
        $data = ['auth' => 0];
        $this->db->where('id', $userId);
        $this->db->update('gccmaster.tblusers', $data);
        $this->db->reset_query();
        $this->db->where('emp_id', $userId);
        $this->db->delete('trusted_devices');
        $this->db->reset_query();
        $this->core_layout->deleteCookie('device_trust_token');
    }

    private function activate2FAWithValidation($user){
        // Get required user details
        $this->db->select('u.email, u.telegram_chat_id, e.mobile_no');
        $this->db->from('gccmaster.tblusers u');
        $this->db->join('gccmaster.tblemployees e', 'u.emp_id = e.id', 'left'); 
        $this->db->where('u.id', $user['id']);
        
        $query = $this->db->get();
        $userDetails = $query->row_array();
        $this->db->reset_query();
        // Validate communication methods
        if (!$this->validateCommunicationMethods($userDetails)) {
            throw new Exception('You must have at least one communication method (email, mobile, or Telegram) to activate 2FA.');
        }

        // Activate 2FA
        $data = ['auth' => 1];
        $this->db->where('id', $user['id']);
        $this->db->update('gccmaster.tblusers', $data);
        $this->db->reset_query();
    }

    private function validateCommunicationMethods($userDetails){
        return !(
            empty($userDetails['email']) &&
            empty($userDetails['mobile_no']) &&
            empty($userDetails['telegram_chat_id'])
        );
    }

    public function getLockedAccounts(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $rowData = $this->getDatatableRequest($search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->getDatatableRequestCount($search);
        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;
        return $resultset;
    }

    private function getDatatableRequest($search, $limit, $offset, $sortBy, $sortOrder){
        $filterFields = array('employees.firstname', 'employees.lastname', 'employees.middlename', 'users.email', 'users.username', 'DATE_FORMAT(users.lockout_dt, "%b %d, %Y %h:%i %p")');
        $this->db->select("
            users.id,
            users.email, UPPER(CONCAT(employees.lastname,
                CASE WHEN UPPER(TRIM(employees.suffix)) != 'N/A' AND
                    UPPER(TRIM(employees.suffix !='NONE')) AND employees.suffix !='' AND
                    employees.suffix IS NOT NULL THEN CONCAT(' ', employees.suffix) ELSE ''
                END, ', ', employees.firstname, ' ',
                CASE WHEN UPPER(TRIM(employees.middlename)) != 'N/A' AND UPPER(TRIM(employees.middlename)) != 'NONE' AND
                        TRIM(employees.middlename) !='' AND employees.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(employees.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name,
            users.username, users.lockout_dt,
            DATE_FORMAT(users.lockout_dt, '%b %d, %Y %h:%i %p') as formatted_lockedout_date
        ")
        ->from('gccmaster.tblusers as users')
        ->join('gccmaster.tblemployees as employees','users.emp_id = employees.id')
        ->where('users.lockout', 1)
        ->where('users.is_suspended', 0)
        ->where('employees.employee_status', 'Active');

        if ($search) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();
        return $query->result();
    }
    private function getDatatableRequestCount($search){
        $filterFields = array('employees.firstname', 'employees.lastname', 'employees.middlename', 'users.email', 'users.username', 'DATE_FORMAT(users.lockout_dt, "%b %d, %Y %h:%i %p")');
        $this->db->select("users.id, users.email, employees.firstname, employees.lastname,employees.middlename, users.username, users.lockout_dt")
        ->from('gccmaster.tblusers as users')
        ->join('gccmaster.tblemployees as employees','users.emp_id = employees.id')
        ->where('users.lockout', 1)
        ->where('users.is_suspended', 0)
        ->where('employees.employee_status', 'Active');
        if ($search) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function unlockAccount() {
        $resultset = [
            'status'  => false,
            'message' => '',
            'success' => 'error',
            'action'  => 'system',
        ];
        $post = $this->input->post();
        $id = $post['id'];
        $this->db->trans_start();
        $sendOtp = $this->sendOTP($id);
        if (!$sendOtp['sent_sms'] && !$sendOtp['sent_email']) {
            $resultset['message'] = "OTP NOT SENT";
            $this->db->trans_rollback();
            $this->logEvent($resultset, $id);
            return $resultset;
        }
        $update = $this->updatePassword($id, $sendOtp['otp']);
        if (!$update) {
            $resultset['message'] = "Failed to update password.";
            $this->db->trans_rollback();
            $this->logEvent($resultset, $id);
            return $resultset;
        }
        $resultset['status'] = true;
        $resultset['mobile_no'] = $sendOtp['mobile_no'] ?? null;
        $resultset['email_to'] = $sendOtp['email_to'] ?? null;
        $resultset['message'] = "Account unlocked successfully.";
        $resultset['success'] = "success";
        $resultset['action'] = 'user';
        $this->db->trans_commit();
        $this->logEvent($resultset, $id);
    
        return $resultset;
    }
    
    private function logEvent($resultset, $userId) {
        $this->core_layout->setEventLog(
            "{$resultset['message']} User Id: $userId",
            "unlock",
            $resultset['success'],
            "gccmaster",
            $resultset['action']
        );
    }

    private function updatePassword($id,$otp){
        $this->db->where('id', $id);
        $this->db->set('lockout', 0);
        $this->db->set('auth', 0);
        $this->db->set('lockout_dt', NULL);
        $this->db->set('force_update',1);
        $this->db->set('login_attempts', 0);
        $this->db->set('reset_attempts', 0);
        $this->db->set('remember_token', NULL);
        $this->db->set('password', md5($otp));
        $update = $this->db->update('gccmaster.tblusers');
        return $update;
    }

    private function sendOTP($id)
    {
        $response = [
            'sent_email' => false,
            'sent_sms' => false,
            'otp' => null,
            'message' => ''
        ];
    
        $this->db->select('emp.mobile_no, users.email, emp.firstname')
            ->from('gccmaster.tblusers as users')
            ->join('gccmaster.tblemployees as emp', 'users.emp_id = emp.id')
            ->where('users.id', $id);
    
        $result = $this->db->get()->row();
        $this->db->reset_query();
        if (empty($result->mobile_no) && empty($result->email)) {
            $response['message'] = "No communication method found. Update mobile number or email address.";
            return $response;
        }
        $OTP = strtoupper(bin2hex(random_bytes(3)));
        $response['otp'] = $OTP;
        $send_email[] = $result->email;
        $mailer['send_to'] = $send_email;
        $data = ['first_name' => $result->firstname,'key_code' => $OTP];
        $email_content = $this->load->view("recovery_password_email.php",["data" => $data],true);
        if ($result->mobile_no) {
            $message = "[GC&C] Conyxph Temporary Password\n\n" .
            "Use the temporary password to sign in: " .
            "$OTP\n\n" .
            "Security Notice: Do not share this password with anyone. " .
            "If you did not request this, please ignore this message.";
            $sms_result = $this->gateway->sendPlaySMS($result->mobile_no, $message);
            $response['sent_sms'] = $sms_result['status'] ? $sms_result['status'] : false;
            $response['mobile_no'] = $result->mobile_no;
        }
        if($result->email){
            $response['sent_email'] = $this->core_layout->send_email('core','GC & C Conyx PH','Account Recovery',$email_content,$mailer);
            $response['email_to'] = $result->email;
        }
        return $response;
    }

    public function changePasswordLater() {
        $this->db->trans_begin();
        try {
            $id = $this->input->post('id');
            $user = $this->db->select('is_important,waive_password_update')
            ->where('id', $id)
            ->from('gccmaster.tblusers')
            ->get()->row();
            if($user->is_important == 1){
                $new_last_update = ($user->waive_password_update == 0) ? date('Y-m-d H:i:s', strtotime('+60 days')) : date('Y-m-d H:i:s', strtotime('+5 days'));
            }
            else{
                $new_last_update = ($user->waive_password_update == 0) ? date('Y-m-d H:i:s', strtotime('+90 days')) : date('Y-m-d H:i:s', strtotime('+15 days'));
            }
            $this->db->set('waive_password_update', $user->waive_password_update + 1);
            $this->db->set('next_update', $new_last_update);
            $update = $this->db->where('id', $id)->update('gccmaster.tblusers');
            
            if ($update) {
                $this->db->trans_commit();
                $this->session->set_userdata('logged_in', array_merge(
                    $this->session->userdata('logged_in'),
                    ['next_update' => $new_last_update]
                ));
                return $update;
            } else {
                $this->db->trans_rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function getUserRoleSelectData(){
        $this->db->select('id, TRIM(UPPER(description)) as text');
        $this->db->from('gccmaster.user_role');
        $this->db->where('is_active', 1);
        $this->db->where('status', 1);
        return $this->db->get()->result();
    }
}