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
                    'department' => $res[0]->department_id
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

}