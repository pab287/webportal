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

    function accounts()
    {
        $this->core_layout->addJs("js/core/user_script.js", true);
        $arrData = array();
        $this->core_layout->setHeaderTitle("User - <small>Accounts</small>");
        $this->load->view('core/templates/header');
        $this->load->view('core/users/index', $arrData);
        $this->load->view('core/templates/footer');
    }

    public function locked_accounts(){
        $this->core_layout->addJs("js/users/locked_accounts.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('locked_accounts');
        $this->load->view('core/templates/footer');
    }

    function get_group()
    {
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
        $data = array(
            'emp_id' => $this->input->post('emp_id'),
            'email' => $this->input->post('email'),
            'username' => $this->input->post('username'),
            'password' => MD5($this->input->post('password')),
            'group_id' => $this->input->post('group_id'),
            'force_update'=> 1,
        );
        $insert = $this->user->save_user($data);
        $tempData = $this->core_layout->getUserData($insert);
        $tempName = (object) $tempData;
        $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No assigned name";
        $tempStatus = ($insert > 0)? "success": "error";
        $tempMessage = ($insert > 0)? "New user account for `{$tempName}` has been added.": "Failed to add new user account!";
		$this->core_layout->logNotification($tempMessage, $tempStatus, "users");
        echo json_encode(array("status" => TRUE));
    }

    public function edit_user($id)
    {
        $data = $this->user->edit_user($id);
        echo json_encode($data);
    }

    public function update_user()
    {
        $old_password = $this->db->get_where("gccmaster.tblusers", array("id"=>$this->input->post('id')))->row('password');
        $new_password = $this->input->post('password');

        $data = array(
            'email' => $this->input->post('email'),
            'username' => $this->input->post('username'),
            'group_id' => $this->input->post('group_id'),
            'telegram_chat_id' => $this->input->post('telegram_chat_id')
        );
        
        if($old_password != $new_password){
            $data['password'] = md5($this->input->post('password'));
        }
        

        
        $updated = $this->user->update_user(array('id' => $this->input->post('id')), $data);

        $tempData = $this->core_layout->getUserData($this->input->post('id'));
        $tempName = (object) $tempData;
        $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No assigned name";
        
        $tempStatus = ($updated > 0)? "success": "error";
        $tempMessage = ($updated > 0)? "User account of `{$tempName}` has been updated.": "Failed to update the user account of `{$tempName}`!";
		$this->core_layout->logNotification($tempMessage, $tempStatus, "users");
        echo json_encode(array("status" => TRUE));
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

    function process_suspend_account($id = null)
    {
        $data = $this->user->processSuspendAccount($id);
        echo json_encode($data);
    }

    function process_unsuspend_account($id = null)
    {
        $data = $this->user->processUnsuspendAccount($id);
        echo json_encode($data);
    }

    function get_suspended_users_list()
    {
        $data = $this->user->getSuspendedUsersList();
        echo json_encode($data);
    }

    function get_active_user_list()
    {
        $data = $this->user->getActiveUserList();
        echo json_encode($data);
    }

    function open_modal()
    {
        $data = $this->utilities->openModal();
        echo $data;
    }

    function reset_selected_user_password()
    {
        $user_id = isset($_GET['id']) ? $_GET['id'] : "";
        if (!empty($user_id)) {
            $data = $this->user->resetSelectedUserPassword($user_id);
            echo json_encode($data);
        }
    }

    function process_change_password()
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $data = $this->user->processChangePassword($post);
        echo json_encode($data);
    }

    function process_change_pin()
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $data = $this->user->processChangePin($post);
        echo json_encode($data);
    }

    function verify_pin()
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $data = $this->user->verifyPin($post);
        echo json_encode($data);
    }
    // controller for notification on borrowing overdue
	public function get_all_notif(){
		$notifData = $this->Auth->display_data();
		// return json_encode($notifData);
		$this->output
			->set_content_type('json')
			->set_output(json_encode($notifData));
	}

    function forget_pin(){
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $data = $this->user->forgetPin($post);
        echo json_encode($data);       
    }

    function get_session_status(){
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

}