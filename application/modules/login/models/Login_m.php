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
        $this->load->model("sms/services/Gateway_model","sms_gateway");
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
                        'next_update' => $row->next_update,
                        'waive_count' => $row->waive_password_update,
                        'is_important' => $row->is_important,
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
            
            $this->db->where('username', $username);
            $current = $this->db->get('gccmaster.tblusers')->row();
            if ($current->password == md5($post['password'])) {
                $response = array(
                    'status' => false,
                    'message' => 'New password cannot be the same as the old password'
                );
                return $response;
            }

            if($current->is_important == 1){
                $next_update = date('Y-m-d H:i:s', strtotime('+60 days'));
            }else{
                $next_update = date('Y-m-d H:i:s', strtotime('+90 days'));
            }

            $data = array(
                'password' => md5($new_password),
                'force_update' => 0,
                'waive_password_update' => 1,
                'remember_token'=> null,
                'next_update' => $next_update,
            );

    
            $result = $this->db->where('username', $username)->update('gccmaster.tblusers', $data);
    
            if (!$result) {
                $this->session->sess_destroy();
                $this->db->trans_rollback();
                $response = array('status' => false, 'message' => 'Failed to update password');
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
                    'next_update' => $res[0]->next_update,
                    'waive_count' => $res[0]->waive_password_update,
                    'is_important' => $res[0]->is_important,
                );
                $this->session->set_userdata('logged_in', $sess_array);
                $this->db->trans_commit();
                $url = site_url('portal/index');
                $loggedIn = $this->session->userdata("logged_in");
                $response = array('status' => true, 'message' => 'Password successfully updated', 'redirect' => $url, "loggedIn" => $loggedIn);
            }
        } else {
            $response = array('status' => false, 'message' => 'No POST data received');
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
            // $this->db->where('emp_id', $id);
            // $this->db->set('expiry', date('Y-m-d H:i:s'));
            // $this->db->update('gccmaster.two_factor_authentication');
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
            if (!$send_result['status']) {
                throw new Exception($send_result['message']);
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
        $this->db->select('key_code, expiry, two_factor_authentication.id,method,two_factor_authentication.send_to');
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
            $response['attempts'] = $current_otp['key_code'];
            $response['send_to'] = $current_otp['send_to'];
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
            $res = $this->login($username, $new_password);
            $this->db->set('confirmed', 1);
            $this->db->where('id', $otp_id);
            $this->db->where('emp_id', $res[0]->emp_id);
            $this->db->update('gccmaster.two_factor_authentication');
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
                'next_update' => $res[0]->next_update,
                'waive_count' => $res[0]->waive_password_update,
                'is_important' => $res[0]->is_important,
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
        
            if ($current_attempts >= 4) {
                $this->db->where('emp_id', $emp_id);
                $this->db->set('lockout', 1, false);
                $this->db->set('lockout_dt', 'NOW()', false);
                $this->db->update('gccmaster.tblusers');
                $url = site_url('login');
                $response = array('status' => 'locked', 'message' => 'Error', 'redirect' => $url);
                $this->session->sess_destroy();
            }else{
                $response['attempts'] = "$current_attempts";
                $response['status'] = "false";
            }
        }
        return $response;
    }


    public function resendOtp() {
        $post = $this->input->post();
        $old_request_id = $post['request_id'];
        $emp_id = $post['emp_id'];
        // Update record with transaction
        $this->db->trans_start();
        $this->db->where('emp_id', $emp_id);
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
        $result = array();
        switch ($method) {
            case 'sms':
                $msg = "NEVER SHARE YOUR OTP especially on social media, SMS, or email links. " .
                       "Your GC&C Conyxph One-Time Password (OTP) is: {$data['key_code']}. " .
                       "If this was not you, please ignore.";
                
                // $result = $this->sms->sendSMS($send_to, $msg); this is for playsms
                $result = $this->sms_gateway->sendTwoFactorSms($send_to, $msg);
                return $result;
                
            case 'email':

                $send_email[] = $send_to;
                $email_content = $this->load->view("two_factor_email_template.php",array("data" => $data), true);
                $mailer['send_to'] = $send_email;
                $result['status'] = $this->core->send_email('core','GC & C Conyx PH','Two Factor Authentication',$email_content,$mailer);
                if($result['status']){
                    $result['message'] = 'Email sent successfully';
                }else{
                    $result['message'] = 'Failed to send email';
                }
                return $result;
                
            case 'telegram':
                $msg = "🔐 *NEVER SHARE YOUR OTP* especially on social media, SMS, or email links.\n\n" .
                        "Your GC&C Conyxph One-Time Password (OTP) is: `{$data['key_code']}`\n\n" .
                        "If this was not you, please ignore this message.";
                
                $result = $this->sendTelegramOTP($send_to, $msg);
                return $result;
        }
    }

    private function sendTelegramOTP($chat_id, $message) {
        $result = array();
        try {
            $bot_token = $_ENV['GCC_NOTIFICATION_BOT'];
            
            if (empty($bot_token) || !isset($bot_token)) {
                $result['status'] = false;
                $result['message'] = 'Telegram bot token not configured';
                return $result;
            }
            
            $telegram_api_url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
            
            $post_data = array(
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true
            );
            
            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $telegram_api_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            if ($curl_error) {
                $result['status'] = false;
                $result['message'] = 'cURL Error: ' . $curl_error;
                return $result;
            }
            
            if ($http_code !== 200) {
                $result['status'] = false;
                $result['message'] = 'HTTP Error: ' . $http_code;
                return $result;
            }
            
            $telegram_response = json_decode($response, true);
            
            if ($telegram_response && $telegram_response['ok']) {
                $result['status'] = true;
                $result['message'] = 'Telegram message sent successfully';
                $result['telegram_response'] = $telegram_response;
            } else {
                $result['status'] = false;
                $result['message'] = 'Telegram API Error: ' . ($telegram_response['description'] ?? 'Unknown error');
                $result['telegram_response'] = $telegram_response;
            }
            
        } catch (Exception $e) {
            $result['status'] = false;
            $result['message'] = 'Exception: ' . $e->getMessage();
        }
        
        return $result;
    }

    private function getEmployeeNameById($id){
        $query = $this->db->query("SELECT CONCAT(firstname, IFNULL(CONCAT(' ', SUBSTRING(middlename, 1, 1), '.'), ''), ' ',lastname) AS employee_name, firstname FROM gccmaster.tblemployees WHERE id = {$id}");
        return $query->row();
    }


    public function getAttempts($username) {
        $this->db->select('login_attempts,lockout');
        $this->db->from('gccmaster.tblusers');
        $this->db->where('username', $username);
        $query = $this->db->get();
        return $query->row();
    }

    public function unlockAccount() {
        $resultset = [
            'status'  => false,
            'message' => '',
            'success' => 'error',
            'action'  => 'system',
        ];
        $post = $this->input->post();
        $id = $this->getId($post['username']);
        if(!$id){
            return $resultset;
        }
        $this->db->trans_start();
        $sendOtp = $this->sendOTPLocked($id);
        if (!$sendOtp['sent_sms'] && !$sendOtp['sent_email']) {
            $resultset['message'] = "OTP NOT SENT";
            $this->db->trans_rollback();
            $this->logEvent($resultset, $id);
            return $resultset;
        }
        $update = $this->updateLockedPassword($id, $sendOtp['otp']);
        if (!$update) {
            $resultset['message'] = "Failed to update password.";
            $this->db->trans_rollback();
            $this->logEvent($resultset, $id);
            return $resultset;
        }
        $resultset['mobile'] = $this->maskMobileNumber($sendOtp['mobile_no']);
        $resultset['email'] = $this->maskEmail($sendOtp['email']);
        $resultset['status'] = true;
        $resultset['message'] = "Account unlocked successfully.";
        $resultset['success'] = "success";
        $resultset['action'] = 'user';
        $this->db->trans_commit();
        $this->logEvent($resultset, $id);
    
        return $resultset;
    }

    private function getId($username){
        $this->db->select('id');
        $this->db->from('gccmaster.tblusers');
        $this->db->where('username', $username);
        $query = $this->db->get();
        return $query->row()->id;
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

    private function updateLockedPassword($id,$otp){
        $this->db->where('id', $id);
        $this->db->set('lockout', 0);
        $this->db->set('auth', 0);
        $this->db->set('lockout_dt', NULL);
        $this->db->set('force_update',1);
        $this->db->set('login_attempts', 0);
        $this->db->set('reset_attempts', 0);
        $this->db->set('password', md5($otp));
        $update = $this->db->update('gccmaster.tblusers');
        return $update;
    }

    private function sendOTPLocked($id)
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
        $email_content = $this->load->view("users/recovery_password_email.php",["data" => $data],true);
        if ($result->mobile_no) {
            $message = "[GC&C] Conyxph Temporary Password\n\n" .
            "Use the temporary password to sign in: " .
            "$OTP\n\n" .
            "Security Notice: Do not share this password with anyone. " .
            "If you did not request this, please ignore this message.";
            $sms_result = $this->sms_gateway->sendPlaySMS($result->mobile_no, $message);
            $response['sent_sms'] = $sms_result['status'] ? $sms_result['status'] : false;
            $response['mobile_no'] = $result->mobile_no;
        }
        if($result->email){
            $response['sent_email'] = @$this->core_layout->send_email('core','GC & C Conyx PH','Account Recovery',$email_content,$mailer);
            $response['email'] = $result->email;
        }
        
        return $response;
    }

    private function maskMobileNumber($mobile) {
        if (empty($mobile) || is_null($mobile)) {
            return "";
        }
        $mobile = preg_replace('/\D/', '', (string)$mobile);
        return (strlen($mobile) > 3)
            ? str_repeat('*', strlen($mobile) - 3) . substr($mobile, -3)
            : $mobile;
    }
    
    private function maskEmail($email) {
        if (empty($email) || is_null($email)) {
            return "";
        }
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email; 
        }
        $localPart = $parts[0];
        $domain = $parts[1];
        $maskedLocalPart = substr($localPart, 0, 1) . str_repeat('*', max(0, strlen($localPart) - 2)) . substr($localPart, -1);
        return $maskedLocalPart . '@' . $domain;
    }

}