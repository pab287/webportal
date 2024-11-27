<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Model
{
    protected $usersTable = "tblusers";
    protected $roleAclTable = "user_role_acl";
    protected $modulesTable = "modules";
    private $user_data = array();
    private $moduleId = 0;

    function __construct()
    {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        $this->moduleId = (isset($this->user_data["module_id"]) && $this->user_data["module_id"]) ? intval($this->user_data["module_id"]) : 0;
		$this->load->model("core/Users_model");
    }

    public function doRedirect()
    {
        $loginUrl = login_url();
        if (!$this->user_data && $loginUrl) {
            $uri = $this->uri->uri_string();

            if ($uri) {
                $this->set_cookie_uri(base_url(), $uri);
            } else {
                $this->destroy_cookie_uri();
            }

            redirect($loginUrl, "refresh");
        } else {
            $isPortal = false;
            $requestUri = explode("/", $_SERVER["REQUEST_URI"]);
            if (count($requestUri) >= 2) {
                if (isset($requestUri[2]) && ($requestUri[2] == "portal" || $requestUri[1] == "portal")) {
                    $isPortal = true;
                }
                if (isset($requestUri[3]) && $requestUri[3] == "portal") {
                    $isPortal = true;
                }
            }

            if ($isPortal) {
                $this->moduleId = 0;
            } else {
                $moduleId = $this->getCurrentModuleId();
                if ($moduleId == 0) {
                    $this->updateSessionModuleId($moduleId);
                }
                $modules = $this->getModuleResource();

                if ($moduleId !== 0 && $isPortal == false && !in_array($moduleId, $modules)) {
                    redirect(site_url("portal/index"), "refresh");
                }
            }

            $this->updateSessionModuleAcl();
        }
    }

    public function getCurrentModuleId()
    {
        return $this->moduleId;
    }

    public function setModuleAccess($module = null)
    {
        if ($module) {
            $query = $this->db->get_where($this->modulesTable, array("name" => $module, "status" => 1, "is_active" => 1));
            if ($query->num_rows() == 1) {
                $this->moduleId = intval($query->row()->id);
                return $this->moduleId;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function set_cookie_uri($baseUrl = null, $uriString = null)
    {
        if ($uriString) {
            $cookieUrl = array(
                'name' => 'base_url',
                'value' => $baseUrl,
                'expire' => 10000,
            );
            $cookieUri = array(
                'name' => 'uri_string',
                'value' => $uriString,
                'expire' => 10000
            );
            $this->input->set_cookie($cookieUrl);
            $this->input->set_cookie($cookieUri);
            return true;
        } else {
            return false;
        }
    }

    public function get_cookie_uri()
    {
        $cookieUrl = $this->input->cookie('base_url', true);
        $cookieUri = $this->input->cookie('uri_string', true);
        if ($cookieUrl && $cookieUri) {
            $url = $cookieUrl . "" . $cookieUri;
            return $url;
        } else {
            return false;
        }
    }

    public function destroy_cookie_uri()
    {
        delete_cookie('base_url');
        delete_cookie('uri_string');
        return true;
    }

    public function getCurrentUser()
    {
        return $this->user_data;
    }

    public function getUserData($include = array())
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser) {
            $user = $currentUser["emp_id"];
            if ($include) {
                $selectQuery = implode(",", $include);
                $this->db->select($selectQuery);
            }
            $this->db->where("id", $currentUser["emp_id"]);
            $this->db->from("tblemployees");
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                return $query->row_array();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getUserProfile()
    {
        $include = array(
            "id", "idno", "biometricno", "lastname",
            "firstname", "middlename", "email", "position",
            "employee_status", "curr_addr", "str_addr", "city",
            "postal", "pic_filename"
        );
        $user = $this->getUserData($include);
        if ($user) {
            foreach ($user as $key => $value) {
                if ($key == "pic_filename") {
                    $findImage = realpath("uploads/images/{$value}");
                    $nValue = ($value && file_exists($findImage)) ? $value : "defaultAvatar.png";
                    $user[$key] = base_url("uploads/images/{$nValue}");
                }
            }
            $userName = $this->getUserName($user);
            $user["display_name"] = ($userName) ? $userName : "No Assigned Name";
            return $user;
        } else {
            return false;
        }
    }

    private function getUserName($arrData = array())
    {
        if (isset($arrData["lastname"], $arrData["firstname"], $arrData["middlename"])) {
            $lname = (isset($arrData["lastname"]) && $arrData["lastname"]) ? $arrData["lastname"] : "";
            $fname = (isset($arrData["firstname"]) && $arrData["firstname"]) ? $arrData["firstname"] : "";
            $mname = (isset($arrData["middlename"]) && $arrData["middlename"]) ? $arrData["middlename"] : "";
            $userName = "{$fname} {$mname} {$lname}";
            return ucwords(strtolower($userName));
        } else {
            return "No Assigned Name";
        }
    }

    public function getUserId()
    {
        $userData = $this->user_data;
        if (isset($userData["emp_id"]) && $userData["emp_id"]) {
            return $userData["emp_id"];
        } else {
            return false;
        }
    }

    public function getRoleId()
    {
        $userId = $this->getUserId();
        if ($userId) {
            $where = array("emp_id" => $userId);
            $query = $this->db->get_where($this->usersTable, $where);
            if ($query->num_rows() == 1) {
                $row = $query->row_array();
                return $row["role_id"];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getRoleResource()
    {
        $roleId = $this->getRoleId();
        $arrIds = array();
        if ($roleId || $roleId == "0") {
            $query = $this->db->get_where($this->roleAclTable, array("role_id" => $roleId));
            if ($query->num_rows() == 1) {
                $row = $query->row_array();
                $roleResource = $row["role_resource"];
                if ($roleResource) {
                    $arrIds = unserialize($roleResource);
                }
            }
        }

        $tempArrIds = $this->generateAclResourceIds($arrIds);
        return $tempArrIds;
    }

    private function generateAclResourceIds($arrIds = array())
    {
        $ndata = array();
        if ($arrIds) {
            $userData = (object)$this->user_data;
            $moduleId = (isset($userData->module_id) && $userData->module_id)? $userData->module_id: 0;

            $currentModuleId = $this->getCurrentModuleId();
            $moduleId = (intval($moduleId) !== intval($currentModuleId))? $currentModuleId: $moduleId;
            
            if (intval($moduleId)) {
                foreach ($arrIds as $id) {
                    $arrId = explode("-", $id);
                    if (count($arrId) == 2) {
                        if (intval($arrId[0]) == $moduleId) {
                            $value = intval($arrId[1]);
                            if (!in_array($value, $ndata)) {
                                $ndata[] = $value;
                            }
                        }
                    }
                }
            }
        }
        return $ndata;
    }

    public function getModuleResource()
    {
        $roleId = $this->getRoleId();
        $arrIds = array();
        if ($roleId || $roleId == "0") {
            $query = $this->db->get_where($this->roleAclTable, array("role_id" => $roleId));
            if ($query->num_rows() == 1) {
                $row = $query->row_array();
                $roleResource = $row["module_resource"];
                if ($roleResource) {
                    $arrIds = unserialize($roleResource);
                }
            }
        }
        return $arrIds;
    }

    private function updateSessionModuleId($moduleId = null)
    {
        $session = $this->user_data;
        if ($session) {
            $session["module_id"] = $moduleId;
            $this->session->set_userdata("logged_in", $session);
            return $this;
        }
    }

    function updateSessionModuleAcl()
    {
        $session = $this->user_data;
        if ($session) {
            $tempSession = (object)$this->user_data;
            $tempModuleId = $this->getCurrentModuleId();

            $requestUri = explode("/", $_SERVER["REQUEST_URI"]);
            unset($requestUri[0], $requestUri[1]);
            $tempRequestUri = implode("/", $requestUri);

            if (isset($tempSession->module_id) && $tempSession->module_id) {
                if ($tempModuleId !== $tempSession->module_id) {
                    $this->updateSessionModuleId($tempModuleId);
                    if ($tempRequestUri) {
                        redirect(site_url($tempRequestUri), "refresh");
                    }
                }
            } else {
                $tempSessionModule = isset($tempSession->module_id) ? $tempSession->module_id : 0;
                if ($tempModuleId !== $tempSessionModule) {
                    $this->updateSessionModuleId($tempModuleId);
                    if ($tempRequestUri) {
                        redirect(site_url($tempRequestUri), "refresh");
                    }
                }
            }
        }
    }

    // notification filter on prevelages find user_model at borrow_notif function
    function notif(){ 
        $array = $this->Users_model->borrow_notif();
        return $array;
    }

    // notifation data from database connected to function notif
    function display_data(){
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $sql = "a.id, a.status, b.firstname, 
                b.lastname, b.middlename, b.suffix, c.is_overdue, c.is_returned, c.date_borrowed,
                IF(pos.`name` IS NULL, a.position, pos.`name`) position, 
                IF(comp.`code` IS NULL, a.company, comp.`code`) company, 
                a.reference_no, 
                a.date_trans, c.asset_name";
        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");
        $this->db->where('a.status != ', 'Cancelled');
        $this->db->limit(10, 0);
        $this->db->where('c.is_overdue', '0'); // 1 serve as already overdue
        $this->db->where('c.is_returned', '0'); // 0 serve as not returned
        $this->db->where('c.date_due <= ', $check);
        return $this->db->get()->result_array();
    }

    function getPrivilegeChecker($arrChecker = array(), $module=null){
		$hasPrivilege = false;
		if($module && count($arrChecker) > 0){ 
			$roleId = $this->authenticate->getRoleId();
			$moduleResource = array();
			$tempModules = array();
			if($roleId || $roleId == "0"){
				$submodule = $this->db->get_where($this->modulesTable, array("name"=>$module, "parent_id"=>0, "is_active"=>1, "status"=>1));
				if($submodule->num_rows() == 1){
					$row = $submodule->row();
					$this->db->order_by("sort", "ASC");
					$subModule = $this->db->get_where($this->modulesTable, array("parent_id"=>$row->id, "is_active"=>1, "status"=>1));
					if($subModule->num_rows() > 0){
						foreach($subModule->result() as $rs){
							$moduleId[] = $rs->id;
							$modules[] = $rs;
							$tempModules[$rs->id] = $rs->name;
						}
					}
					
					$userRole = $this->db->get_where($this->roleAclTable, array("role_id"=>$roleId));
					if($userRole->num_rows() == 1){
						$row = $userRole->row();
						$moduleColumn = unserialize($row->module_resource);
						$moduleResource = is_array($moduleColumn)? $moduleColumn: array();
					}
				}
			}
			foreach ($tempModules as $key => $value) {
				if(in_array($value, $arrChecker)){
					if(in_array($key, $moduleResource)){
						$hasPrivilege = true;
					}
				}
			}
		}

		return $hasPrivilege;
	}

    public function getCurrentVersion(){
        $resultset = array();
        $this->db->order_by("id", "DESC");
        $qTemp = $this->db->get("gccmaster.versions");
        if($qTemp->num_rows() > 0){
            $data = $qTemp->result();
            $resultset["response"] = true;
            $resultset["data"] = $data;
            $resultset["current_version"] = $data[0];
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }
}