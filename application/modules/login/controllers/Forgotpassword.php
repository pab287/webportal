<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forgotpassword extends MY_Controller {
 
 	function __construct()
 	{
   		parent::__construct();
   		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('Login_m');
		$this->load->helper('string');
 	}
 
 	function index()
	{
	    $this->load->helper(array('form'));
	    $this->load->view('forgotpassword');
	}

	//dynamic sending of email for reset password
	function process($email=true){
		$post = $this->input->post();
        $resultset = array();
		$data = array();

		$key = random_string('alnum', 69);
		$rawchangepasslink = base_url("login/forgotpassword/changepassword");
		$insertkey = $this->crud->insert(array("email"=>$post['email'],"redirectlink"=>$key,"status"=>1),"gccmaster.passwordrequest");
		


		if($this->checkEmailIfExist($post['email'])){
		$getUser = $this->db->order_by('id','DESC')->get_where("gccmaster.tblusers", array("email"=>$post['email'], "is_suspended"=>0), 1)->row();
		$getEmployee = $this->db->get_where("gccmaster.tblemployees", array("id"=>$getUser->emp_id,"employee_status"=>"Active"))->row();

		
		$data['firstname'] = $getEmployee->firstname;
		$data['username'] = $getUser->username;
		$data['rawchangepasslink'] = $rawchangepasslink."?key=".$key;
		$messageContent = $this->load->view("reset_password", $data, true);
		$content = $messageContent;
		// var_dump($data['rawchangepasslink']);
        if($data){
            if($email){
                $module = "reset_password";
                $email_title = "Reset Password";
                $content_title = "Password Reset Request";
				$overrideMailer = array();
        		$overrideMailer['send_to'] = array($post['email']);
                if($content){
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
					// var_dump($overrideMailer);
                    if($sent){
						$this->core_layout->setEventLog("User with email ".$post["email"]." has requested to change password using forgotpassword process.","forgot password", "success", "gcchris", "user");
                        redirect('login/forgotpassword/success', 'refresh');
                    }else{
						$this->core_layout->setEventLog("User with email ".$post["email"]." has entered an email that does not exist in the database using forgotpassword process.","forgot password", "error", "gcchris", "system");
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
			$this->session->set_userdata(array("err_msg"=> "Email does not exist."));
			redirect('login/forgotpassword', 'refresh');
		}
	}

	//check email if it exists in db
	function checkEmailIfExist($email = null){
		$query = $this->crud->load(array("email"=>$email),"gccmaster.tblusers");

		return $query;
	}
	//end of function
	
	function success()
	{
		$this->load->view("forgotpasswordsuccess");
	}

	function changepassword()
	{
		$this->load->view("changepassword");
	}

	public function checkkey($key)
	{
		$query = $this->crud->load(array("redirectlink"=>$key,"status"=>1),"gccmaster.passwordrequest");

		return $query;
	}

	function processchangepassword(){
		$result = array();
		$post = $this->input->post();
		$updatepassword = $this->crud->update(array("password"=>md5($post["password"])),array("email"=>$post["email"]),"gccmaster.tblusers");
		if($updatepassword){
			$updateredirectlink = $this->crud->update(array("status"=>0),array("redirectlink"=>$post["redirectlink"]),"gccmaster.passwordrequest");
			if($updateredirectlink){
				$this->changepasswordsuccess();
			}
		}else{
			return false;
		}
	}

	function changepasswordsuccess(){
		$this->load->view("changepasswordsuccess");
	}
}