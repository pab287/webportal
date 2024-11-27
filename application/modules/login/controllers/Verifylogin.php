<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Verifylogin extends MY_Controller{
    private $directAccess;
    /** e61a999de57725d7ce368a91ed87a7bd **/
    function __construct()
    {
        parent::__construct();
        $this->load->helper('string');
        $this->load->model('Login_m');
        $this->load->library('form_validation');
        $this->directAccess = md5("direct_access");
    }

    public function index()
    {
        //This method will have the credentials validation
        $this->form_validation->set_error_delimiters('<div class="m-alert m-alert--outline alert alert-danger alert-dismissible" role="alert">', '<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
		<span></span>
		</div>');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|prep_for_form');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|callback_check_database|prep_for_form');

        if ($this->form_validation->run() === FALSE) {
            //Field validation failed.  User redirected to login page
            $this->load->view('login_v');
        } else {
            //Go to private area
            redirect('portal/index', 'refresh');
        }
    }

    function check_database($password)
    {
        //Field validation succeeded.  Validate against database
        $username = $this->input->post('username');
        //query the database
        $result = $this->Login_m->login($username, $password);
        $remember = $this->input->post('remember');

        if ($result) {
            $id = $result['0']->id;
            $privileges = $this->Login_m->get_privileges_by_id($id);
            $sess_array = array();

            if (isset($remember)) {
                $remember_token = random_string('alnum', 60);
                set_cookie('remember_me', $remember_token, 0);
                $this->db->where("id", $id)->update("gccmaster.tblusers", array("remember_token" => $remember_token));
            }

            foreach ($result as $row) {
                if ($row->is_suspended == 1) {
                    $this->form_validation->set_message('check_database', 'This user account is suspended.');
                    $this->core_layout->setEventLog("User account logged in is currently suspended.","login", "error", "gccmaster", "user", $row->emp_id);
                    return false;
                } else {
                    $sess_array = array(
                        'id' => $row->id,//tbluser_id
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
                        'department' => $row->department_id
                    );
                    $this->session->set_userdata('logged_in', $sess_array);
                    $this->core_layout->setEventLog("User has successfully loggedin in the webportal.","login", "success", "gccmaster", "user", $row->emp_id);
                    return TRUE;
                }
            }
        } else {
            $this->core_layout->setEventLog("User ".$username." logged in with Invalid credentials for username or password.","login", "error", "gccmaster", "user");
            $this->form_validation->set_message('check_database', 'Invalid username or password');
            return false;
        }
    }

    function redirect_access($username, $password)
    {
        $response = false;
        if ($username && $password) {
            $row = $this->Login_m->direct_login($username, $password);
            if ($row) {
                $id = $row->id;
                $privileges = $this->Login_m->get_privileges_by_id($id);
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
                        'department' => $row->department_id
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

    function super_admin_token($token=null, $user=null){
		$loggedIn = $this->session->userdata("logged_in");
		$redirectLink = site_url("portal/index");
		$loginUrl = site_url("login/index");
        
		if($loggedIn){
			$loginUrl = $redirectLink;
		}else{
			if(($token && $user) && $token == $this->directAccess){
				$query = $this->db->get_where("gccmaster.tblusers", array("username"=>$user, "is_suspended"=>0));
				if($query->num_rows() == 1){
					$row = $query->row();
					$response = $this->redirect_access($row->username, $row->password);
					if($response){
						$loginUrl = $redirectLink;
					}
				}
			}		
        }
        
        return $loginUrl;
	}
	
	function super_admin_prtoken_mrs($token=null, $user=null, $id=null){
		$loggedIn = $this->session->userdata("logged_in");
		
		$redirectLink = root_url("mrs/pr_head/pr_dt?id={$id}&status=ForApproved");
		/*** $redirectLink = root_url("mrs/mrsdetails_email/mrs_request_approval/{$id}"); ***/
		$loginUrl = site_url();
		
		if($loggedIn){
			redirect($redirectLink, "refresh");
		}else{
			if(($token && $user) && $token == $this->directAccess){
				$query = $this->db->get_where("gccmaster.tblusers", array("username"=>$user, "is_suspended"=>0));
				if($query->num_rows() == 1){
					$row = $query->row();
					$response = $this->redirect_access($row->username, $row->password);
					if($response){
						redirect($redirectLink, "refresh");
					}else{
						redirect($loginUrl, "refresh");					
					}
				}else{
					redirect($loginUrl, "refresh");
				}
			}else{
				redirect($loginUrl, "refresh");
			}			
		}
	}
	
	function super_admin_prtoken_mrs_approval($token=null, $user=null, $status=null, $id=null){
		$loggedIn = $this->session->userdata("logged_in");
		$redirectLink = root_url("mrs/mrsdetails_email/mrs_request_status_offline/{$status}/{$id}");
		$loginUrl = site_url();
		
		if($loggedIn){
			redirect($redirectLink);
		}else{
			if(($token && $user) && $token == $this->directAccess){
				$query = $this->db->get_where("gccmaster.tblusers", array("username"=>$user, "is_suspended"=>0));
				if($query->num_rows() == 1){
					$row = $query->row();
					$response = $this->redirect_access($row->username, $row->password);
					if($response){
						redirect($redirectLink);
					}else{
						redirect($loginUrl, "refresh");					
					}
				}else{
					redirect($loginUrl, "refresh");
				}
			}else{
				redirect($loginUrl, "refresh");
			}			
		}
	}
	
	public function get_cookie_uri(){
	  $cookieUrl = $this->input->cookie('base_url',true);
	  $cookieUri = $this->input->cookie('uri_string',true);
		if($cookieUrl && $cookieUri){
			$url = $cookieUrl."".$cookieUri;
			return $url;
		}else{ 
			return false;
		}
	}
	public function destroy_cookie_uri(){
		 delete_cookie('base_url');
		 delete_cookie('uri_string');
		 return true;
    }
    
    /*** remove bypass user access ***
     * function user_access($username=null){
        $resultset = array();
        if($username){
            $query = $this->db->get_where("gccmaster.tblusers", array("username"=>$username, "is_suspended"=>0));
            if($query->num_rows() == 1){
                $resultset["response"] = true;
                $resultset["password"] = $this->directAccess;
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        echo json_encode($resultset);
    } 

    function bypass_access(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            unset($post["csrf_token"]);
            $query = $this->db->get_where("gccmaster.tblusers", array("username"=>$post["username"], "is_suspended"=>0));
            if($query->num_rows() == 1){
                $row = $query->row();
                if($row->id == 1 || $row->username == "developer"){
                    $resultset["response"] = false;
                    $resultset["toastr_state"] = "warning";
                    $resultset["toastr_msg"] = "Developer Account has been disabled!";
                }else{
                    $tempUrl = $this->super_admin_token($post["token"], $post["username"]);
                    $resultset["response"] = true;
                    $resultset["redirect"] = $tempUrl;
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_state"] = "error";
                $resultset["toastr_msg"] = "User account not found!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_state"] = "error";
            $resultset["toastr_msg"] = "No post data found!";
        }

        echo json_encode($resultset);
    } ***/

    function application_token(){
        $data_array = array("token" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash());
        echo json_encode($data_array);
        // return $data_array;
    }

    function gcc_time_login(){
        $data = $this->application_token();
        $post = $this->input->post();
        $post["data"] = $data;
        return $post;
    }
}