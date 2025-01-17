<?php

    function getEmployeeData() {
        $conn = new PDO('mysql:host=localhost;dbname=gccmaster', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $date = date("Y-m-d", strtotime("-1 year", time()));
        $response['ca_array'] = array();

        $sql = "SELECT a.id, a.employee, a.status, a.reference_no, 
                b.firstname, b.middlename, b.lastname, b.suffix, 
                a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt, 
                b.department_id
            FROM gcceforms.cash_advance a
            LEFT JOIN gccmaster.tblemployees b ON a.employee = b.id
            WHERE a.status != 'Cancelled' AND a.created_dt >= :date
            ORDER BY a.id DESC";

        $sth = $conn->prepare($sql);
        $sth->bindParam(':date', $date, PDO::PARAM_STR);
        $sth->execute();

        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $fullname = "{$row['firstname']} {$row['middlename']} {$row['lastname']} {$row['suffix']}";
            $employee_name = utf8_encode($fullname);
            $list = [
                'id' => $row['id'],
                'status' => $row['status'],
                'employee' => $row['employee'],
                'reference_no' => $row['reference_no'],
                'employee_name' => $employee_name ?: "No Assigned Name",
                'amt_applied' => number_format($row['amt_applied'], 2),
                'amt_approved' => number_format($row['amt_approved'], 2),
                'purpose' => $row['purpose'],
                'created_dt' => date('m/d/Y g:i A', strtotime($row['created_dt'])),
                'approved_dt' => date('m/d/Y g:i A', strtotime($row['approved_dt']))
            ];
            array_push($response['ca_array'], $list);
        }

        echo json_encode($response);
    }


    function user_login(){
        $conn = new PDO('mysql:host=localhost;dbname=gccmaster', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
            array_push($response['info_array'], $list);
        } else {
            $list['status'] = 'failed';
            array_push($response['info_array'], $list);
        }

        echo json_encode($response);
    }


	function user_privilege(){
        $conn = new PDO('mysql:host=localhost;dbname=gccmaster', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$employee_id = $_POST['employee_id'];
		$token = $_POST['token'];
		$conn->query("UPDATE gccmaster.tblusers SET mobile_token='$token' WHERE emp_id='$employee_id'");

		$sth = $conn->query("SELECT u.role_id,u.emp_id,e.employee_status FROM tblusers as u
								LEFT JOIN tblemployees AS e 
								ON e.id = u.emp_id
								WHERE emp_id='$employee_id'");
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		$role_id = $data['role_id'];

		$response['info_array'] = array();
		$list['employee_id'] = $data['emp_id'];
		$list['employee_status'] = $data['employee_status'];
		$list['portal_sub_module'] = get_portal_sub_module($role_id);
		$list['privilege'] = getPrivilege($role_id);
		array_push($response['info_array'], $list);
		echo json_encode($response);
	}

	function getPrivilege($role_id){
		$arrData = array();

        $query = get_user_role_acl($role_id);
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

                            $acl = get_access_control_list($aclId);
                            if ($acl->rowCount() > 0) {
                                $rowAcl = $acl->fetch();

                                $privilegeData = get_privilege_list($privilegeId);
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
        	$module = getModules('','0');
        	if ($module->rowCount() > 0) {
                while ($rs = $module->fetch(PDO::FETCH_OBJ)) {
                	$moduleId[] = $rs->id;
                    $modules[] = $rs;
                }
        	}

        	$userRole = get_user_role_acl($roleId);
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
        $data = array();
        $moduleId = array();
        $modules = array();         
        $moduleResource = array();

        if($roleId || $roleId == "0"){
            $submodule = getModules("name='eforms' and", '0');
            if($submodule->rowCount() == 1){
                $row = $submodule->fetch();
                $subModule = getModules('', $row['id']);
                if($subModule->rowCount() > 0){
                    while ($rs = $subModule->fetch(PDO::FETCH_OBJ)) {
                        $moduleId[] = $rs->id;
                        $modules[] = $rs;
                    }
                }

                $userRole = get_user_role_acl($roleId);
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
    }

    function get_user_role_acl($role_id){
        $conn = new PDO('mysql:host=localhost;dbname=gccmaster', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sth = $conn->prepare("SELECT * from user_role_acl where role_id='$role_id'");
        $sth->execute();
        return $sth;
    }

    function get_access_control_list($id){
        $conn = new PDO('mysql:host=localhost;dbname=gccmaster', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sth = $conn->prepare("SELECT name from access_control_list where id='$id'");
        $sth->execute();
        return $sth;
    }

    function get_privilege_list($id){
        $conn = new PDO('mysql:host=localhost;dbname=gccmaster', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sth = $conn->prepare("SELECT name from privilege_list where id='$id'");
        $sth->execute();
        return $sth;
    }

    function getModules($module,$parent_id){
        $conn = new PDO('mysql:host=localhost;dbname=gccmaster', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sth = $conn->prepare("SELECT * from modules where $module parent_id='$parent_id' and is_active='1' and status='1' order by sort asc");
        $sth->execute();
        return $sth;
    }

    function saveLogs($type, $user_action, $user_id, $log_message){
        $conn = new PDO('mysql:host=localhost;dbname=gccmaster', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $current_date = date("Y-m-d H:i:s");
        $ip_address = '';

        $sth = $conn->prepare("INSERT INTO hydra_billing.user_logs_event(`type`, `user_action`, `user_id`, `log_message`, `ip_address`, `created_at`) VALUES ('$type','$user_action','$user_id','$log_message','$ip_address','$current_date')");
        $sth->execute();

        if ($sth) {
            return true;
        } else {
            return false;
        }
    }