<?php

class Gcceforms_login_m extends Dbase{
    
	public function user_login(){
		$conn = $this->conn();

		$response['info_array'] = array();

		$username = $_POST['username'];
		$password = md5($_POST['password']);

		$sth = $conn->prepare("SELECT *, p.name as position_name, e.employee_status 
								FROM tblusers AS u
								LEFT JOIN tblemployees AS e 
									ON e.id = u.emp_id
								LEFT JOIN gcchris.tblposition AS p 
									ON p.id = e.position
								where u.username=:usrname and u.password=:pssword");
		$sth->bindParam(':usrname', $username);
		$sth->bindParam(':pssword', $password);
   		$sth->execute();

   		$count = $sth->rowCount();

   		if ($count > 0) {

   			$row = $sth->fetch(PDO::FETCH_ASSOC);

   			$list['status'] = 'success';
   			$list['position'] = $row['position_name'];
   			$list['employee_pic'] = $row['pic_filename'];
			$list['firstname'] = $row['firstname'];
			$list['middlename'] = $row['middlename'];
			$list['lastname'] = $row['lastname'];
			$list['employee_id'] = $row['emp_id'];
			$list['username'] = $row['username'];
			$list['email'] = $row['email'];
			$list['employee_status'] = $row['employee_status'];
			//$list['privilege'] = $this->getPrivilege($row['role_id']);
			array_push($response['info_array'], $list);
   		} else {
   			$list['status'] = 'failed';
			$list['position'] = '';
			$list['employee_pic'] = '';
			$list['firstname'] = '';
			$list['middlename'] = '';
			$list['lastname'] = '';
			$list['employee_id'] = '';
			$list['username'] = '';
			$list['email'] = '';
			$list['employee_status'] = '';
			//$list['privilege'] = '';
			array_push($response['info_array'], $list);
   		}

   		echo json_encode($response);
	}

	public function user_privilege(){
		$conn = $this->conn();

		$employee_id = $_POST['employee_id'];
		$token = $_POST['token'];

		// update user mobile_token
   		$conn->query("UPDATE gccmaster.tblusers SET mobile_token='$token' WHERE emp_id='$employee_id'");

   		// get module menu
		$sth = $conn->query("SELECT u.role_id,u.emp_id,e.employee_status FROM tblusers as u
							 LEFT JOIN tblemployees AS e 
							 ON e.id = u.emp_id
							 WHERE emp_id='$employee_id'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		$role_id = $data['role_id'];

		$response['info_array'] = array();
		$list['employee_id'] = $data['emp_id'];
		$list['employee_status'] = $data['employee_status'];
		$list['portal_modules'] = $this->getPortalModules($role_id); //9.5.24
		$list['portal_sub_module'] = $this->get_portal_sub_module($role_id);
		$list['privilege'] = $this->getPrivilege($role_id);
		array_push($response['info_array'], $list);
		echo json_encode($response);
	}

	function getPrivilege($role_id){
		$arrData = array();

    	$query = $this->get_user_role_acl($role_id);
    	if ($query->rowCount() > 0) {
    		
    		$privilege = unserialize($query->fetch()['privilege_resource']);
    		if ($privilege) {
    			foreach ($privilege as $vv) {
		            $isNode = strpos($vv, "-");
		            if ($isNode == true) {
		                $dd = explode("-", $vv);
		                if (count($dd) == 2) {
		                    $aclId = $dd[0];
		                    $privilegeId = $dd[1];

		                    $acl = $this->get_access_control_list($aclId);
		                    if ($acl->rowCount() > 0) {
		                    	$rowAcl = $acl->fetch();

		                    	$privilegeData = $this->get_privilege_list($privilegeId);
		                    	if ($privilegeData->rowCount() == 1) {
		                    		$rowPriv = $privilegeData->fetch()['name'];
		                            $privName = strtolower($rowPriv);
		                            $arrData[$rowAcl['name']][] = $privName;
		                    	}
		                    }
		                }
		            }
		        }
    		}
    	}
    	return $arrData;
	}

	function getPortalModules($roleId){
        $data = array();

        $moduleId = array();
        $modules = array();
        $moduleResource = array();
        
        if($roleId || $roleId == "0"){
        	$module = $this->getModules('','0');
        	if ($module->rowCount() > 0) {
                while ($rs = $module->fetch(PDO::FETCH_OBJ)) {
                	$moduleId[] = $rs->id;
                    $modules[] = $rs;
                }
        	}

        	$userRole = $this->get_user_role_acl($roleId);
        	if($userRole->rowCount() == 1){
                $row = $userRole->fetch();
                $moduleColumn = isset($row['module_resource']) ? unserialize($row['module_resource']) : array();
                $moduleResource = is_array($moduleColumn)? $moduleColumn: array();
            }
        }

        $data["module_id"] = $moduleId;
        $data["modules"] = $modules;
        $data["module_resource"] = $moduleResource;

        return $data;
    }

    function get_portal_sub_module($roleId){

    	//$roleId = $_POST['roleId'];

        $data = array();
        $moduleId = array();
        $modules = array();         
        $moduleResource = array();

        if($roleId || $roleId == "0"){
            $submodule = $this->getModules("name='eforms' and", '0');
            if($submodule->rowCount() == 1){
                $row = $submodule->fetch();
                $subModule = $this->getModules('', $row['id']);
                if($subModule->rowCount() > 0){
                    while ($rs = $subModule->fetch(PDO::FETCH_OBJ)) {
	                	$moduleId[] = $rs->id;
	                	$modules[] = $rs;
	                }
                }

                $userRole = $this->get_user_role_acl($roleId);
	        	if($userRole->rowCount() == 1){
	                $row = $userRole->fetch();
	                $moduleColumn = unserialize($row['module_resource']);
	                $moduleResource = is_array($moduleColumn)? $moduleColumn: array();
	            }
            }
        }


        $hasModules = array(); $lockedModules = array();

        if(isset($modules) && $modules){
        	foreach($modules as $module){
        		if(in_array($module->id, $moduleResource)){
        			$hasModules[] = $module;
		        }
		        else{
		        	$lockedModules[] = $module;
		        }
        	}
        }

        return $hasModules;
        //echo json_encode($hasModules);
    }

    public function test_only(){
    	echo json_encode($this->getPrivilege('1'));
    }

	function get_user_role_acl($role_id){
    	$conn = $this->conn();
		$sth = $conn->prepare("SELECT * from user_role_acl where role_id='$role_id'");
		$sth->execute();
		return $sth;
    }

    function get_access_control_list($id){
    	$conn = $this->conn();
		$sth = $conn->prepare("SELECT name from access_control_list where id='$id'");
		$sth->execute();
		return $sth;
    }

    function get_privilege_list($id){
    	$conn = $this->conn();
		$sth = $conn->prepare("SELECT name from privilege_list where id='$id'");
		$sth->execute();
		return $sth;
    }

    function getModules($module,$parent_id){
    	$conn = $this->conn();
		$sth = $conn->prepare("SELECT * from modules where $module parent_id='$parent_id' and is_active='1' and status='1' order by sort asc");
		$sth->execute();
		return $sth;
    }


} // gcceformsloaModel

?>