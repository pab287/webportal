<?php defined('BASEPATH') OR exit('No direct script access allowed');

Class Login_m extends CI_Model
{
    private $directAccess;
    function __construct()
    {
        parent::__construct();
        $this->load->helper('string');
        $tempDate = date("Ymd");

        $this->directAccess = sha1("direct_access-{$tempDate}");
        $this->load->model('core/Core_model', 'core');
    }

    function login($username, $password)
    {
        $this->db->select('tblusers.*,tblemployees.id as emp_id ,tblemployees.firstname,tblemployees.lastname, tblemployees.middlename, tblemployees.suffix, tblemployees.company_id, tblemployees.department_id');
        $this->db->from('tblusers');
        $this->db->join('tblemployees', 'tblemployees.id = tblusers.emp_id');
        $this->db->where('tblusers.username', $username);
        $this->db->where('tblusers.password', MD5($password));
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    function direct_login($username, $password){
		$this->db->select('gccmaster.tblusers.*,gccmaster.tblemployees.id as emp_id ,gccmaster.tblemployees.firstname, 
			gccmaster.tblemployees.lastname, gccmaster.tblemployees.middlename, gccmaster.tblemployees.suffix, 
			gccmaster.tblemployees.company_id, gccmaster.tblemployees.department_id');
			
		$this->db->from('gccmaster.tblusers');
		$this->db->join('gccmaster.tblemployees','gccmaster.tblemployees.id = gccmaster.tblusers.emp_id');
		$this->db->where('username', $username);
		$this->db->where('password', $password);
		
		$query = $this->db->get();
		if($query->num_rows() == 1){
			return $query->row();
		}else{
			return false;
		}
	}

    function loginUsingRememberToken($remember_token)
    {
        $this->db->select('tblusers.*,tblemployees.id as emp_id ,tblemployees.firstname,tblemployees.lastname, tblemployees.middlename, tblemployees.suffix, tblemployees.company_id, tblemployees.department_id');
        $this->db->from('tblusers');
        $this->db->join('tblemployees', 'tblemployees.id = tblusers.emp_id');
        $this->db->where('tblusers.remember_token', $remember_token);

        $query = $this->db->get()->row();
        if ($query) {
            $remember_token = random_string('alnum', 60);
            set_cookie('remember_me', $remember_token, 0);
            $this->db->where("id", $query->id)->update("gccmaster.tblusers", array("remember_token" => $remember_token));

            return $query;
        }

        return false;
    }

    function update_log($where, $data)
    {
        $this->db->update('tblusers', $data, $where);
        return $this->db->affected_rows();
    }

    function get_privileges_by_id($id)
    {
        $this->db->select('tblprivilegeusers.privilege_id, tblprivileges.privilege_name');
        $this->db->from('tblprivilegeusers');
        $this->db->join('tblprivileges', 'tblprivileges.id = tblprivilegeusers.privilege_id');
        $this->db->where('tblprivilegeusers.user_id', $id);
        $this->db->order_by('tblprivilegeusers.privilege_id', 'asc');
        $query = $this->db->get();

        return $query->result();
    }

    public function get_by_uname($uname)
    {
        $this->db->from('tblusers');
        $this->db->where('username', $uname);
        $query = $this->db->get();
        return $query->row();
    }

    public function checkkey($key)
	{
		$query = $this->crud->load(array("redirectlink"=>$key,"status"=>1),"gccmaster.passwordrequest");

		return $query;
	}

    function superAdminAccessToken($token=null, $user=null){
		$loggedIn = $this->session->userdata("logged_in");
		$redirectLink = site_url("portal/index");
		$loginUrl = site_url("login/index");
        
		if($loggedIn){
			$loginUrl = $redirectLink;
		}else{
			if(($token && $user) && $token === $this->directAccess){
				$query = $this->db->get_where("gccmaster.tblusers", array("username"=>$user, "is_suspended"=>0));
				if($query->num_rows() == 1){
					$row = $query->row();
					$response = $this->redirectAccess($row->username, $row->password);
					if($response){
						$loginUrl = $redirectLink;
					}
				}
			}		
        }
        
        return $loginUrl;
	}

    function redirectAccess($username, $password){
        $response = false;
        if ($username && $password) {
            $row = $this->direct_login($username, $password);
            if ($row) {
                $id = $row->id;
                $privileges = $this->get_privileges_by_id($id);
                $sess_array = array();

                if ($row->is_suspended == 1) {
                    $response = false;
                } else {
                    $sess_array = array(
                        'id' => $row->id,
                        'emp_id' => $row->emp_id,
                        'username' => $row->username,
                        'firstname' => $row->firstname,
                        'middlename' => $row->middlename,
                        'lastname' => $row->lastname,
                        'privileges' => $privileges,
                        'suffix' => $row->suffix,
                        'group_id' => $row->group_id,
                        'email' => $row->email,
                        'company' => $row->company_id,
                        'department' => $row->department_id,
                        'TwoFactorAuth' => $row->auth,
                    );

                    $this->session->set_userdata('logged_in', $sess_array);
                    $response = true;
                }
            } else {
                $response = false;
            }
        } else {
            $response = false;
        }
        return $response;
    }

    public function updatePassword(){
        $post = $this->input->post();
        $response=array();
        if ($post) {
            $username = $post['username'];
            $new_password = $post['password'];
            $this->db->trans_start();
            $data = array(
                'password' => md5($new_password),
                'force_update' => 0
            );
    
            $result = $this->db->where('username', $username)->update('gccmaster.tblusers', $data);
    
            if (!$result) {
                $this->session->sess_destroy();
                $this->db->trans_rollback();
                $response = array('status' => 'false', 'message' => 'Failed to update password');
            }else{
                $res = $this->Login_m->login($username, $new_password);
                $id = $res[0]->id;
                $privileges = $this->Login_m->get_privileges_by_id($id);
                $sess_array = array(
                    'id' => $id,//tbluser_id
                    'emp_id' => $res[0]->emp_id,
                    'username' => $res[0]->username,
                    'firstname' => $res[0]->firstname,
                    'middlename' => $res[0]->middlename,
                    'lastname' => $res[0]->lastname,
                    'privileges' => $privileges,
                    'suffix' => $res[0]->suffix,
                    'group_id' => $res[0]->group_id,
                    'email' => $res[0]->email,
                    'company' => $res[0]->company_id,
                    'department' => $res[0]->department_id,
                    'TwoFactorAuth' =>  $res[0]->auth,
                );
                $this->session->set_userdata('logged_in', $sess_array);
                $this->db->trans_commit();
                $url = site_url('portal/index');
                $response = array('status' => 'success', 'message' => 'Password successfully updated', 'redirect' => $url);
            } 
        } else {    
            $response = array('status' => 'failure', 'message' => 'No POST data received');
        }
        return $response;
    }

    public function authenticateOtp() {
        $response = [
            'status' => false,
            'message' => 'An error occurred. Please try again.'
        ];
    
        $this->db->trans_start();
    
        try {
            $post = $this->input->post();
            $id = $post['sessionData']['emp_id'];
            $method = $post['method'];
            $otp = rand(100000, 999999);
            $employee = $this->getEmployeeNameById($id);
            $contacts = $post['sessionData']['contacts'];
            $sesh =['method'=> $method];
            $this->session->set_userdata($sesh);
            $send_to = $this->getSendToValue($contacts, $method);
    
            if (empty($send_to)) {
                throw new Exception('No valid communication method found for the selected 2FA method.');
            }
    
            $data = [
                'employee_name' => $employee->employee_name,
                'confirmed' => 0,
                'send_to' => $send_to,
                'key_code' => $otp,
                'method' => $method,
                'emp_id' => $id,
                'expiry' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
            ];
    
            $this->db->insert('gccmaster.two_factor_authentication', $data);
            $request_id = $this->db->insert_id();
            $data['first_name'] = $employee->firstname;
            $send_result = $this->sendOTP($send_to, $data, $method);
            if (!$send_result) {
                throw new Exception('Failed to send OTP.');
            }
    
            $this->db->trans_commit();
    
            $response = [
                'status' => true,
                'message' => 'OTP sent successfully.',
                'request_id' => $request_id,
                'method' => $method,
            ];
    
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function otpCheck(){
        $response = array();
        $post = $this->input->post();
        $id = $post['emp_id'];
        $this->db->select('key_code, expiry, gccmaster.two_factor_authentication.id,method');
        $this->db->join('gccmaster.tblusers', 'gccmaster.tblusers.emp_id = gccmaster.two_factor_authentication.emp_id');
        $this->db->from("gccmaster.two_factor_authentication");
        $this->db->where('gccmaster.two_factor_authentication.emp_id', $id);
        $this->db->where('confirmed', 0);
        $this->db->where('expiry >=', date('Y-m-d H:i:s'));
        $current_otp = $this->db->get()->row_array();
        if ($current_otp) {
            $response['status'] = "true";
            $response['request_id'] = $current_otp['id'];
            $response['expiry'] = $current_otp['expiry'];
            $response['method'] = $current_otp['method'];
        }else {
            $response['status'] = "false";
        }
        return $response;
    }

    public function verifyOtp(){
        $post = $this->input->post();
        $emp_id = $post['emp_id'];
        $username = $post['username'];
        $new_password = $post['password'];
        $key_code = str_replace(' ', '', $post['key_code']);
        $this->db->select('id');
        $this->db->from('gccmaster.two_factor_authentication');
        $this->db->where('emp_id', $emp_id);
        $this->db->where('confirmed', 0);
        $this->db->where('expiry >=', date('Y-m-d H:i:s'));
        $this->db->where('key_code', $key_code);
        $query = $this->db->get();
        if($query->num_rows() == 1){
            $row = $query->row();
            $otp_id = $row->id;
            $this->db->set('confirmed', 1);
            $this->db->where('id', $otp_id);
            $this->db->update('gccmaster.two_factor_authentication');
            $res = $this->login($username, $new_password);
            $id = $res[0]->id;
            $privileges = $this->get_privileges_by_id($id);
            $sess_array = array(
                'id' => $id,//tbluser_id
                'emp_id' => $res[0]->emp_id,
                'username' => $res[0]->username,
                'firstname' => $res[0]->firstname,
                'middlename' => $res[0]->middlename,
                'lastname' => $res[0]->lastname,
                'privileges' => $privileges,
                'suffix' => $res[0]->suffix,
                'group_id' => $res[0]->group_id,
                'email' => $res[0]->email,
                'company' => $res[0]->company_id,
                'department' => $res[0]->department_id,
                'TwoFactorAuth' => $res[0]->auth,
            );
            $this->db->where('emp_id', $emp_id);
            $this->db->set('resend_attempts',0, false);
            $this->db->update('gccmaster.tblusers');
            $this->generateCookie($emp_id);
            $this->session->unset_userdata('auth');
            $this->session->set_userdata('logged_in', $sess_array);
            $url = site_url('portal/index');
            $response = array('status' => 'true', 'message' => 'Success', 'redirect' => $url);
        }
        else {
            $this->db->select('resend_attempts');
            $this->db->where('emp_id', $emp_id);
            $current_attempts = $this->db->get('gccmaster.tblusers')->row()->resend_attempts;
        
            $this->db->where('emp_id', $emp_id);
            $this->db->set('resend_attempts', 'resend_attempts + 1', false);
            $this->db->update('gccmaster.tblusers');
        
            if ($current_attempts + 1 > 3) {
                $this->db->where('emp_id', $emp_id);
                $this->db->set('lockout', 1, false);
                $this->db->update('gccmaster.tblusers');
                $url = site_url('login');
                $response = array('status' => 'locked', 'message' => 'Error', 'redirect' => $url);
                $this->session->sess_destroy();
            }else{
                $response['status'] = "false";
            }
        }
        return $response;
    }


    public function resendOtp() {
        $post = $this->input->post();
        $old_request_id = $post['request_id'];
        
        // Update record with transaction
        $this->db->trans_start();
        $this->db->where('id', $old_request_id);
        $this->db->set('expiry', date('Y-m-d H:i:s'));
        $this->db->update('gccmaster.two_factor_authentication');
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }

    private function generateCookie($id){
        $trust_token = bin2hex(random_bytes(32));
        $user_agent = $this->input->user_agent();
        $device_data = [
            'emp_id'      => $id, 
            'trust_token'  => $trust_token,
            'user_agent'   => $user_agent,
            'expiry'   => date('Y-m-d H:i:s', strtotime('+30 days')) 
        ];
        $this->db->insert('trusted_devices', $device_data);
        $cookie = array(
            'name'   => 'device_trust_token',
            'value'  => $trust_token,
            'expire' => 60 * 60 * 24 * 30, // 30 days
            // 'secure' => TRUE // Set to TRUE if using HTTPS
        );
        $this->input->set_cookie($cookie);
    }

    private function getSendToValue($employee, $method) {
        switch ($method) {
            case 'sms':
                return $employee['mobile_no'];
            case 'email':
                return $employee['email'];
            case 'telegram':
                return $employee['telegram_chat_id'];
            default:
                return null;
        }
    }

    private function sendOTP($send_to, $data, $method) {
        
        switch ($method) {
            case 'sms':
                $msg = "NEVER SHARE YOUR OTP especially on social media, SMS, or email links. " .
                       "Your GC&C Conyxph One Time Password (OTP) is: {$data['key_code']}. " .
                       "If this was not you, please ignore.";
                
                $result = $this->sms->sendSMS($send_to, $msg);
                if ($result['status'] == true) {
                    return true;
                }
                return false;
                
            case 'email':
                $email_content = $this->load->view("two_factor_email_template.php",array("data" => $data), true);
                $mailer = array([
                    'send_to' => $send_to,
                ]);
                $result = $this->core->send_email('core','Two Factor Authentication','Two Factor Authentication',$email_content,$mailer);
                return $result === true;
                
            case 'telegram':
                // Add Telegram implementation here
                return true; // Placeholder
        }
    }

    private function getEmployeeNameById($id){
        $query = $this->db->query("SELECT CONCAT(firstname, IFNULL(CONCAT(' ', SUBSTRING(middlename, 1, 1), '.'), ''), ' ',lastname) AS employee_name, firstname FROM gccmaster.tblemployees WHERE id = {$id}");
        return $query->row();
    }

}