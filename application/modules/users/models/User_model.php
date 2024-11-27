<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $timestamp = null;

    function __construct()
    {
        parent::__construct();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model", "dt_model");
        $this->load->model("ams/Utilities_model", "utilities");

        $this->timestamp = new DateTime(null, new DateTimeZone('Asia/Manila'));
    }

    private function getUserData()
    {
        return $this->core_layout->getUserLoggedIn();
    }

    function getGroup()
    {
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

    function getEmployee()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('gccmaster.tblemployees.employee_status', 'Active');
            $this->db->order_by('firstname', 'asc');
            $this->db->like('firstname', $get['q']);
            $this->db->or_like('middlename', $get['q']);
            $this->db->or_like('lastname', $get['q']);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["firstname"] . " " . $_query["middlename"] . " " . $_query["lastname"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);


        } else {
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('gccmaster.tblemployees.employee_status', 'Active');
            $this->db->order_by('firstname', 'asc');
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["firstname"] . " " . $_query["middlename"] . " " . $_query["lastname"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }
    }

    public function save_user($data)
    {
        $this->db->insert('gccmaster.tblusers', $data);
        return $this->db->insert_id();
    }

    public function edit_user($id)
    {
        $sql = "a.id, a.email, a.username, a.password, a.group_id, b.group_name, a.telegram_chat_id";

        $this->db->select($sql);
        $this->db->from("gccmaster.tblusers a");
        $this->db->join("gccmaster.tblgroups b", "a.group_id = b.id", "LEFT");
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

    public function getSuspendedUsersList()
    {
        $tableConfig = $this->input->post();
        $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
        $table = "gccmaster.tblusers users";
        $searchFields = "CONCAT(users.email, employees.firstname, employees.lastname, employees.middlename)";

        $joinArr = array(
            array("table" => "gccmaster.tblemployees employees", "condition" => "users.emp_id = employees.id", "option" => "INNER"),
            array("table" => "gccmaster.tblemployees employees2", "condition" => "users.suspended_by = employees2.id", "option" => "LEFT")
        );
        $where = array("users.is_suspended" => 1);

        $resultSet = array();
        $this->db->select("users.id, users.email, employees.firstname, employees.lastname,
                               employees.middlename, users.suspended_dt, 
                               CONCAT(employees2.firstname, ' ', employees2.lastname) suspended_by");
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
            $field = array("password" => MD5($data->password));
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
}