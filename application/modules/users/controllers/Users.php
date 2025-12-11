<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Users extends MY_Controller{
    function __construct(){
        parent::__construct();
        $this->authenticate->doRedirect();
        $this->authenticate->setModuleAccess("users");
        $this->core_layout->setBodyClass("users");
        $this->core_layout->setPrivilegeName("manage_users");
		$this->load->model("Authentication", "Auth");
        $this->load->model("User_model", "user");
        date_default_timezone_set('Asia/Manila');

        $this->load->model("ams/Utilities_model", "utilities");
    }

    function index()
    {
        $arrData = array();
        $this->core_layout->setPagetitle("Users - Dashboard");

        $this->load->view('core/templates/header');
        $this->load->view('users/index', $arrData);
        $this->load->view('core/templates/footer');
    }

    function modules()
    {
        $this->core_layout->addJs("js/core/module_script.js", true);
        $arrData = array();
        $this->core_layout->setHeaderTitle("User - <small>Modules</small>");
        $this->load->view('core/templates/header');
        $this->load->view('core/module/index', $arrData);
        $this->load->view('core/templates/footer');
    }

    function access_control()
    {
        $this->core_layout->addJs("js/core/acl_script.js", true);
        $arrData = array();
        $this->core_layout->setHeaderTitle("User - <small>Access Control</small>");
        $this->load->view('core/templates/header');
        $this->load->view('core/access_control/index', $arrData);
        $this->load->view('core/templates/footer');
    }

    function roles()
    {
        $this->core_layout->addJs("js/core/role_script.js", true);
        $arrData = array();
        $this->core_layout->setHeaderTitle("User - <small>Roles</small>");
        $this->load->view('core/templates/header');
        $this->load->view('core/roles/index', $arrData);
        $this->load->view('core/templates/footer');
    }

    function privileges()
    {
        $this->core_layout->addJs("js/core/privilege_script.js", true);
        $arrData = array();
        $this->core_layout->setHeaderTitle("User - <small>Privileges</small>");
        $this->load->view('core/templates/header');
        $this->load->view('core/privilege/index', $arrData);
        $this->load->view('core/templates/footer');
    }

    public function accounts(){
        $this->core_layout->setHeaderTitle("User - <small>Accounts</small>");
        $userRole = $this->user->getUserRoleSelectData();
        $arrData = array("user_role" => $userRole);
        $this->core_layout->addJs("js/core/user_script.js", true, $arrData);
        $this->load->view('core/templates/header');
        $this->load->view('core/users/index', $arrData);
        $this->load->view('core/templates/footer');
    }

    public function locked_accounts(){
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
        $this->core_layout->addJs("js/users/locked_accounts.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('locked_accounts');
        $this->load->view('core/templates/footer');
    }

    public function itmar(){
        $data = array();
        $data['employee'] = $this->user->select2Employee();
        $data['supervisor'] = $this->user->select2Supervisor();
        $data['installer'] = $this->user->select2Installer();
        $this->core_layout->setPrivilegeName("it_mobile_application_request");
        $this->core_layout->setHeaderTitle("IT MOBIILE APPLICATION REQUEST");
        $this->core_layout->addJs("js/users/it_mar.js", true, $data);

        $this->load->view('core/templates/header');
        $this->load->view('users/itmar');
        $this->load->view('core/templates/footer');
    }

    function get_group(){
        $data = $this->user->getGroup();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_employee()
    {
        $data = $this->user->getEmployee();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function add_user(){
        $post = $this->input->post();
        unset($post["id"], $post["csrf_token"]);

        $resultset = array();
        $arrResponse = $this->userAccountChecker($post);
        if(is_array($arrResponse) && !empty($arrResponse)){
            $resultset["status"] = false;
            $resultset["toastr_error"] = $arrResponse;
            $resultset["toastr_msg"] = "Error adding new user account!";
        }else{
            $post["password"] = MD5(TRIM($post["password"]));
            $post["is_important"] = isset($post["is_important"]) && $post["is_important"] == "on" ? 1 : 0;
            $post["force_update"] = 1;
            $post["added_by"] = $this->core_layout->getCurrentEmployeeId();
            $post["added_date"] = date("Y-m-d H:i:s");
    
            $insert = $this->user->save_user($post);
            $tempData = $this->core_layout->getUserData($insert);
            $tempName = (object) $tempData;
            $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No assigned name";
            $tempStatus = ($insert > 0)? "success": "error";
            $tempMessage = ($insert > 0)? "New user account for `{$tempName}` has been added.": "Failed to add new user account!";
            $this->core_layout->logNotification($tempMessage, $tempStatus, "users");
            $resultset["status"] = true;
            $resultset["toastr_msg"] = $tempMessage;
        }

        echo json_encode($resultset);
    }

    protected function userAccountChecker($post=array()){
        $responseError = [];
        if(isset($post["username"]) && $post["username"]){
            $this->db->select("users.username, users.email, users.telegram_chat_id, users.is_suspended, UPPER(emp.employee_status) as employee_status");
            $this->db->select("CASE WHEN users.email LIKE '%@%.%' AND users.email NOT LIKE '%..%' AND users.email NOT LIKE '@%' THEN '1' ELSE '0' END AS has_email");
            $this->db->where("users.username", trim($post["username"]));
            if(isset($post["email"]) && $post["email"]){ $this->db->or_where("users.email", trim($post["email"])); }
            if(isset($post["telegram_chat_id"]) && $post["telegram_chat_id"]){ $this->db->or_where("users.telegram_chat_id", trim($post["telegram_chat_id"])); }
            $this->db->join("gccmaster.tblemployees emp", "emp.id = gccmaster.users.emp_id", "INNER");
            $qUser = $this->db->get_where("gccmaster.tblusers users");
            if($qUser->num_rows() > 0){
                foreach ($qUser->result() as $usr) {
                    $hasEmail = intval($usr->has_email) === 1;
                    $tempResponses = $this->accountCheckerResponses($usr, $post, $hasEmail);
                    $responseError = array_unique(array_merge($responseError, $tempResponses));
                }
            }
        }else{ $responseError[] = "Username is required!"; }
        return $responseError;
    }

    protected function existingUserAccountChecker($post=array()){
        $responseError = [];
        if(is_array($post) && !empty($post)){
            $tempKeys = array("username"=>"users.username", "email"=>"users.email", "telegram_chat_id"=>"users.telegram_chat_id");
            $this->db->select("users.username, users.email, users.telegram_chat_id, users.is_suspended, UPPER(emp.employee_status) as employee_status");
            $this->db->select("CASE WHEN users.email LIKE '%@%.%' AND users.email NOT LIKE '%..%' AND users.email NOT LIKE '@%' THEN '1' ELSE '0' END AS has_email");
            $ctr = 0;
            foreach ($post as $key => $value) {
                if(isset($tempKeys[$key]) && $tempKeys[$key]){
                    $nKey = isset($tempKeys[$key])? $tempKeys[$key]: $key;
                    if($ctr == 0){ $this->db->where($nKey, trim($value)); }
                    else{ $this->db->or_where($nKey, trim($value)); }
                    $ctr++;
                }
            }
            $this->db->join("gccmaster.tblemployees emp", "emp.id = gccmaster.users.emp_id", "INNER");
            $qUser = $this->db->get_where("gccmaster.tblusers users");
            if($qUser->num_rows() > 0){
                foreach ($qUser->result() as $usr) {
                    $hasEmail = intval($usr->has_email) === 1;
                    $tempResponses = $this->accountCheckerResponses($usr, $post, $hasEmail);
                    $responseError = array_unique(array_merge($responseError, $tempResponses));
                }
            }
        }
        return $responseError;
    }

    protected function accountCheckerResponses($result, $post=array(), $hasEmail=false){
        $responseError = [];
        $isSuspended = intval($result->is_suspended) === 1? ", and is currently <strong>`".$result->employee_status."`</strong> Employee Status with <strong>SUSPENDED ACCOUNT!</strong>": "!";
        if(isset($post["username"]) && $post["username"] && $result->username == $post["username"]){
            $responseError[] = "Username <strong>`".$result->username."`</strong> already exists".$isSuspended;
        }
        if(isset($post["email"]) && $post["email"] && $result->email == $post["email"] && $hasEmail){
            $responseError[] = "Email <strong>`".$result->email."`</strong> already exists".$isSuspended;
        }
        if(isset($post["telegram_chat_id"]) && $post["telegram_chat_id"] && $result->telegram_chat_id == $post["telegram_chat_id"]){
            $responseError[] = "Telegram ID <strong>`".$result->telegram_chat_id."`</strong> already exists and was used by <strong>`".$result->username."`</strong>".$isSuspended;
        }
        return $responseError;
    }

    public function edit_user($id){
        $data = $this->user->edit_user($id);
        echo json_encode($data);
    }

    public function update_user(){
        $post = $this->input->post();
        $resultset = array();
        if (isset($post["id"]) && $post["id"]){
            $userId = $post["id"];
            $updatePassword = trim($post["password"]);
            unset($post["id"], $post["csrf_token"]);
            $post["email"] = isset($post["email"]) && $post["email"]? trim($post["email"]): "NO EMAIL ADDRESS";
            $currentPassword = $this->db->get_where("gccmaster.tblusers", array("id"=>$userId))->row('password');
            if($currentPassword == $updatePassword){ unset($post["password"]); }
            else{ $post["password"] = md5($updatePassword); }
            $post["is_important"] = isset($post["is_important"]) && $post["is_important"] == "on" ? 1 : 0;
            
            $arrResponse = [];
            $this->db->select("email, username, telegram_chat_id");
            $getUser = $this->db->get_where("gccmaster.tblusers", array("id"=>$userId, "is_suspended"=>0));
            if($getUser->num_rows() === 1){
                $row = $getUser->row();
                $tempData = array();
                if($row->username != $post["username"]){
                    $tempData["username"] = $post["username"];
                }
                if($row->email != $post["email"]){
                    $tempData["email"] = $post["email"];
                }
                if($row->telegram_chat_id != $post["telegram_chat_id"]){
                    $tempData["telegram_chat_id"] = $post["telegram_chat_id"];
                }
                if(is_array($tempData) && !empty($tempData)){ $arrResponse = $this->existingUserAccountChecker($tempData); }
            }else{ $arrResponse[] = "User account not found!"; }

            if(is_array($arrResponse) && !empty($arrResponse)){
                $resultset["status"] = false;
                $resultset["toastr_msg"] = "Error in updating user account!";
                $resultset["toastr_error"] = $arrResponse;
            }else{
                $updated = $this->user->update_user(array('id' => $userId), $post);
                $tempData = $this->core_layout->getUserData($userId);
                $tempName = (object) $tempData;
                $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No assigned name";
                
                $tempStatus = ($updated > 0)? "success": "error";
                $tempMessage = ($updated > 0)? "User account of `{$tempName}` has been updated.": "Failed to update the user account of `{$tempName}`!";
                $this->core_layout->logNotification($tempMessage, $tempStatus, "users");
                $resultset["status"] = true;
                $resultset["toastr_msg"] = $tempMessage;
            }
        } else {
            $resultset["status"] = false;
            $resultset["toastr_msg"] = "User account not found!";
        }
        echo json_encode($resultset);
    }

    public function suspended_accounts()
    {
        $this->core_layout->addJs("js/users/suspended_accounts.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('suspended_accounts');
        $this->load->view('core/templates/footer');
    }

    public function reset_password()
    {
        $this->core_layout->addJs("js/users/reset_password.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('reset_password');
        $this->load->view('core/templates/footer');
    }

    public function process_suspend_account($id = null)
    {
        $data = $this->user->processSuspendAccount($id);
        echo json_encode($data);
    }

    public function process_unsuspend_account($id = null)
    {
        $data = $this->user->processUnsuspendAccount($id);
        echo json_encode($data);
    }

    public function get_suspended_users_list()
    {
        $data = $this->user->getSuspendedUsersList();
        echo json_encode($data);
    }

    public function get_active_user_list()
    {
        $data = $this->user->getActiveUserList();
        echo json_encode($data);
    }

    public function open_modal()
    {
        $data = $this->utilities->openModal();
        echo $data;
    }

    public function reset_selected_user_password()
    {
        $user_id = isset($_GET['id']) ? $_GET['id'] : "";
        if (!empty($user_id)) {
            $data = $this->user->resetSelectedUserPassword($user_id);
            echo json_encode($data);
        }
    }

    public function process_change_password()
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $data = $this->user->processChangePassword($post);
        echo json_encode($data);
    }

    public function process_change_pin()
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $data = $this->user->processChangePin($post);
        echo json_encode($data);
    }

    public function verify_pin()
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $data = $this->user->verifyPin($post);
        echo json_encode($data);
    }
    // controller for notification on borrowing overdue
	public function get_all_notif(){
		$notifData = $this->Auth->display_data();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($notifData));
	}

    public function forget_pin(){
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $data = $this->user->forgetPin($post);
        echo json_encode($data);
    }

    public function get_session_status(){
        $data = $this->core_layout->getSessionStatus();
        echo json_encode($data);
    }

    public function activate_2FA(){
		$data = $this->user->activate2FA();
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

    public function get_locked_accounts(){
        $data = $this->user->getLockedAccounts();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function unlock_account(){
        $data = $this->user->unlockAccount();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function change_password_later(){
        $data = $this->user->changePasswordLater();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }
}
