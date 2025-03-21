<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Core_model extends CI_Model{
    private $jsList = array(), $cssList = array(), $jsArrayData = array(), $isFooterJs = array(), $scriptAttribute = array();
    private $jsExternalList = array(), $cssExternalList = array(), $isFooterExternalJs = array(), $scriptOrder = array();
    private $title, $headerTitle, $crumbTitle, $bodyClass, $privilegeName, $module, $table, $field_id;

    private $historyModule, $historyTableName, $historyTableFieldId, $historyEventId, $historyEmployeeId;

    private $userdata = array();
    private $current_data_time = null;

    protected $emailTemplateTable = "email_template";
    protected $emailProtocolTable = "email_protocol_settings";

    function __construct(){
        parent::__construct();
        $this->load->model("core/access_control_model", "acl_model");
        if ($this->session->userdata("logged_in")) {
            $this->userdata = $this->session->userdata("logged_in");
        }

        $this->current_data_time = new DateTime(null, new DateTimeZone('Asia/Manila'));
        date_default_timezone_set('Asia/Manila');
    }

    function getPerformanceRating($emp_id = null) {
        $date_end = $this->db->get_where("gccmaster.tblemployees", array("id"=>$emp_id))->row_array();
        $limit = 1;
        // if($date_end['date_end'] == NULL OR $date_end['date_end'] == "0000-00-00"){
            $where = array("emp_id" => $emp_id, "current" => 1);
        // }else{
        //     $where = array("emp_id" => $emp_id, "current" => 1, "DATE(prating.created_at) >" => $date_end['date_end']);
        // }
        $this->db->select("prating.*, scale.description, emp.lastname, emp.firstname, emp.middlename, emp.suffix");

        $this->db->join("gcchris.performance_rating_scale scale", "prating.rating = scale.value", "INNER");
        $this->db->join("gccmaster.tblemployees emp", "emp.id = prating.emp_id", "LEFT");
        $data = $this->db->order_by("prating.id","asc")->get_where("gcchris.tblperformance_rating prating", $where)->row();
        if (!empty($data)) {
            $data->fullname = $this->core_layout->getDisplayName(array(
                    "lastname" => $data->lastname,
                    "firstname" => $data->firstname,
                    "middlename" => $data->middlename,
                    "suffix" => $data->suffix)
            )['display_name_1'];
        }
        return $data;
    }

    /*** logged history ***/
    function coreHistoryLogs(){
		return clone $this;
    }

    function setHistoryLogModule($module=null){
		if ($module) {
            $this->historyModule = $module;
            return $this;
        }
    }

    function setHistoryLogTableName($tableName=null){
		if ($tableName) {
            $this->historyTableName = $tableName;
            return $this;
        }
    }

    function setHistoryLogTableFieldId($tableFieldId=null){
		if ($tableFieldId) {
            $this->historyTableFieldId = $tableFieldId;
            return $this;
        }
    }

    function setHistoryLogEventId($eventId=null){
		if ($eventId) {
            $this->historyEventId = $eventId;
            return $this;
        }
    }

    function setHistoryLogEmployeeId($employeeId=null){
		if ($employeeId) {
            $this->historyEmployeeId = $employeeId;
            return $this;
        }
    }

    function saveLoggedEventHistory(){
        if($this->historyEventId && $this->historyModule && $this->historyTableName && $this->historyTableFieldId){
            $tempData = array(
                "event_id"=>$this->historyEventId,
                "module"=>$this->historyModule,
                "table_name"=>$this->historyTableName,
                "table_field_id"=>$this->historyTableFieldId,
                "employee_id"=>$this->historyEmployeeId ? $this->historyEmployeeId: 0,
            );

            $added = $this->db->insert("gccmaster.logged_event_history", $tempData);
            return $added;
        }else{
            return false;
        }
    }

     /*** logged history ***/


    function coreLogs(){
		return clone $this;
    }

    function setLogModule($module=null){
		if ($module) {
            $this->module = $module;
            return $this;
        }
    }

    function setLogTable($table=null){
		if ($table) {
            $this->table = $table;
            return $this;
        }
    }

    function setLogFieldId($field_id=null){
		if ($field_id) {
            $this->field_id = $field_id;
            return $this;
        }
    }

    function getLogModule(){
        return ($this->module)? $this->module: false;
    }

    function getLogTable(){
        return ($this->table)? $this->table: false;
    }

    function getLogFieldId(){
        return ($this->field_id)? $this->field_id: false;
    }

    function logNotification($notification = null, $status = "success", $module = "portal", $type = "system"){
        if ($notification) {
            $module = ($this->module)? $this->module: $module;
            $data = array();
            $data["module"] = $module;
            $data["notification"] = $notification;
            $data["status"] = $status;
            $data["user_id"] = $this->getCurrentEmployeeId();
            $data["table"] = ($this->table)? $this->table: "";
            $data["field_id"] = ($this->field_id)? $this->field_id: 0;
            $data["type"] = $type;
            $data["ip_address"] = $_SERVER['REMOTE_ADDR'];

            $save = $this->db->insert("gccmaster.log_notification", $data);
            if ($save) { return true; }
            else { return false; }
        } else { return false; }
    }

    function getLogNotification($module = null){
        $this->db->from("gccmaster.log_notification");
        if ($module) {
            $this->db->where("module", $module);
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function setEventLog($log_message = "", $user_action = "", $type = "success", $database = "", $table = "user", $employeeId=null){
        $tempUserId = isset($this->userdata["emp_id"]) && $this->userdata["emp_id"] ? $this->userdata["emp_id"]: 0;
        if($employeeId){ $tempUserId = $employeeId; }
        $data = array();
        $data["type"] = $type;
        $data["user_action"] = $user_action;
        $data["user_id"] = $tempUserId;
        $data["log_message"] = $log_message;
        $data["ip_address"] = $_SERVER["REMOTE_ADDR"];
        $event_table = $table == "user" ? "user_logs_event" : "system_logs_event";
        $addedLog = false;
        if(is_array($database) && count($database) > 0){
            foreach ($database as $db) {
                $tempAdded = $this->db->insert("{$db}.{$event_table}", $data);
                if($tempAdded){
                    if($table === "user"){ $this->setHistoryLogEventId($this->db->insert_id()); }
                    $addedLog = true; 
                }
            }
        }else{
            $tempAdded = $this->db->insert("{$database}.{$event_table}", $data);
            if($tempAdded){ 
                if($table === "user"){ $this->setHistoryLogEventId($this->db->insert_id()); }
                $addedLog = true; 
            }
        }
        return $addedLog;
    }


    function addJs($path = null, $footer = false, $arrData = array()){
        if ($path) {
            $this->jsList[] = $path;
            $this->scriptOrder[] = md5($path);

            $this->jsArrayData[] = $arrData;
            $this->isFooterJs[] = $footer;
            return $this;
        }
    }

    function addExternalJs($path = null, $footer = false, $arrData = array()){
        if ($path) {
            $this->jsExternalList[] = $path;
            $this->scriptOrder[] = md5($path);

            if(isset($arrData["script_attribute"]) && $arrData["script_attribute"]){
                $scriptAttribute = $arrData["script_attribute"];
                unset($arrData["script_attribute"]);
                if(is_array($arrData) && count($arrData) == 0){ $arrData = array(); }
                $this->scriptAttribute[] = $scriptAttribute;
            }
            $this->jsArrayData[] = $arrData;
            $this->isFooterExternalJs[] = $footer;
            return $this;
        }
    }

    function addJsonData($arrData=array()){
        $this->jsArrayData[] = $arrData;
        return $this;
    }

    function addCss($path = null){
        if ($path) {
            $this->cssList[] = $path;
            return $this;
        }
    }

    function addExternalCss($path = null){
        if ($path) {
            $this->cssExternalList[] = $path;
            return $this;
        }
    }

    function getStoredJs(){
        $html = "";
        if(is_array($this->scriptOrder) && count($this->scriptOrder) > 0){
            foreach ($this->scriptOrder as $key => $md5Data) {
                if ($this->jsList) {
                    foreach ($this->jsList as $key => $list) {
                        if($md5Data == md5($list)){
                            $filePath = realpath("./assets/{$list}");
                            if (file_exists($filePath)) {
                                $currentUrl = base_url("assets/{$list}");
                                if ($this->isFooterJs[$key] == false) {
                                    $html .= "<script src='{$currentUrl}'></script>\n\t\t";
                                }
                            }
                        }
                    }
                }

                if ($this->jsExternalList) {
                    $this->jsExternalList = array_unique($this->jsExternalList);
                    foreach ($this->jsExternalList as $key => $value) {
                        if($md5Data == md5($value)){
                            if ($this->isFooterExternalJs[$key] == false) {
                                $tempAttribute = "";
                                if(isset($this->scriptAttribute[$key]) && count($this->scriptAttribute[$key]) > 0){
                                    foreach ($this->scriptAttribute[$key] as $kk => $vv) {
                                        $tempAttribute .= (is_numeric($kk) == false)? " {$kk}='{$vv}'": " {$vv}";
                                    }
                                }
                                if($tempAttribute){
                                    $html .= "<script src='{$value}'{$tempAttribute}></script>\n\t\t";
                                }else{
                                    $html .= "<script src='{$value}'></script>\n\t\t";
                                }
                            }
                        }
                    }
                }
            }
        }


        return $html;
    }

    function getStoredFooterJs(){
        $html = "";
        if ($this->jsArrayData && count($this->jsArrayData) > 0) {
            foreach ($this->jsArrayData as $dd) {
                $html .= $this->load->view("core/access_control/html/temp_script", array("data" => $dd), true);
            }
        }
        if(is_array($this->scriptOrder) && count($this->scriptOrder) > 0){
            foreach ($this->scriptOrder as $key => $md5Data) {
                if ($this->jsList) {
                    foreach ($this->jsList as $key => $list) {
                        if($md5Data == md5($list)){
                            $filePath = realpath("./assets/{$list}");
                            if (file_exists($filePath)) {
                                $currentUrl = base_url("assets/{$list}");
                                if ($this->isFooterJs[$key] == true) {
                                    $html .= "<script src='{$currentUrl}'></script>\n\t\t";
                                }
                            }
                        }
                    }
                }

                if ($this->jsExternalList) {
                    $this->jsExternalList = array_unique($this->jsExternalList);
                    foreach ($this->jsExternalList as $key => $value) {
                        if($md5Data == md5($value)){
                            if ($this->isFooterExternalJs[$key] == true) {
                                $tempAttribute = "";
                                if(isset($this->scriptAttribute[$key]) && count($this->scriptAttribute[$key]) > 0){
                                    foreach ($this->scriptAttribute[$key] as $kk => $vv) {
                                        $tempAttribute .= (is_numeric($kk) == false)? " {$kk}='{$vv}'": " {$vv}";
                                    }
                                }
                                if($tempAttribute){
                                    $html .= "<script src='{$value}'{$tempAttribute}></script>\n";
                                }else{
                                    $html .= "<script src='{$value}'></script>\n";
                                }
                            }
                        }
                    }
                }
            }

        }

        return $html;
    }

    function getStoredCss(){
        $html = "";
        if ($this->cssList) {
            foreach ($this->cssList as $list) {
                $filePath = realpath("./assets/{$list}");
                if (file_exists($filePath)) {
                    $currentUrl = base_url("assets/{$list}");
                    $html .= "<link href='{$currentUrl}' rel='stylesheet' type='text/css' />\n\t\t";
                }
            }
        }

        if ($this->cssExternalList) {
            $this->cssExternalList = array_unique($this->cssExternalList);
            foreach ($this->cssExternalList as $list) {
                $html .= "<link href='{$list}' rel='stylesheet' type='text/css' />\n\t\t";
            }
        }
        return $html;
    }

    function setPageTitle($title = null){
        if ($title) {
            $this->title = $title;
            return $this;
        }
    }

    function getPageTitle(){
        $html = "";
        if ($this->title) {
            $html .= $this->title;
        }
        return $html;
    }

    function setHeaderTitle($title = null){
        if ($title) {
            $this->headerTitle = $title;
            return $this;
        }
    }

    function setCrumbTitle($title = null){
        if ($title) {
            $this->crumbTitle = $title;
            return $this;
        }
    }

    function getHeaderTitle(){
        $html = "";
        if ($this->headerTitle) {
            $html .= $this->headerTitle;
        }
        return $html;
    }

    function getCrumbTitle(){
        $html = "";
        if ($this->crumbTitle) {
            $html .= $this->crumbTitle;
        }
        return $html;
    }

    function setBodyClass($class = null){
        if ($class) {
            $this->bodyClass = $class;
            return $this;
        }
    }

    function getBodyClass(){
        $html = "";
        if ($this->bodyClass) {
            $html .= $this->bodyClass;
        }
        return $html;
    }

    function hasBodyClass(){
        return ($this->bodyClass !== "") ? true : false;
    }

    function setPrivilegeName($name = null){
        if ($name) {
            $this->privilegeName = $name;
            return $this;
        }
    }

    function getPrivilegeName(){
        if (!$this->privilegeName) return false;
        return $this->privilegeName;
    }

    function getSidebarNavigation($includes = array(), $isActive = 0){
        $menuItems = array();
        $arrData = $this->acl_model->getAccessControlMenu($includes, $isActive);
        $menuItems["aclMenu"] = $arrData;
        $menuItems["roleResource"] = $this->authenticate->getRoleResource();

        $html = $this->load->view("core/access_control/html/side_nav", $menuItems, true);
        return $html;
    }

    function generatePrivileges(){
        $arrData = array();
        $id = $this->authenticate->getRoleId();
        $query = $this->db->get_where("user_role_acl", array("role_id" => $id));
        if ($query->num_rows() > 0) {
            $row = $query->row();

            $privilege = unserialize($row->privilege_resource);
            if ($privilege) {
                foreach ($privilege as $vv) {
                    $isNode = strpos($vv, "-");
                    if ($isNode == true) {
                        $dd = explode("-", $vv);
                        if (count($dd) == 2) {
                            $aclId = $dd[0];
                            $privilegeId = $dd[1];

                            $acl = $this->db->get_where("access_control_list", array("id" => $aclId));
                            if ($acl->num_rows() == 1) {
                                $rowAcl = $acl->row();

                                $privilegeData = $this->db->get_where("privilege_list", array("id" => $privilegeId));
                                if ($privilegeData->num_rows() == 1) {
                                    $rowPriv = $privilegeData->row();
                                    $privName = strtolower($rowPriv->name);
                                    $arrData[$rowAcl->name][] = $privName;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $arrData;
    }

    function generatePrivilegesUrl(){
        $arrData = array();
        $id = $this->authenticate->getRoleId();
        $query = $this->db->get_where("user_role_acl", array("role_id" => $id));
        if ($query->num_rows() > 0) {
            $row = $query->row();

            $privilege = unserialize($row->privilege_resource);
            if ($privilege) {
                foreach ($privilege as $vv) {
                    $isNode = strpos($vv, "-");
                    if ($isNode == true) {
                        $dd = explode("-", $vv);
                        if (count($dd) == 2) {
                            $aclId = $dd[0];
                            $privilegeId = $dd[1];

                            $acl = $this->db->get_where("access_control_list", array("id" => $aclId));
                            if ($acl->num_rows() == 1) {
                                $rowAcl = $acl->row();

                                $privilegeData = $this->db->get_where("privilege_list", array("id" => $privilegeId));
                                if ($privilegeData->num_rows() == 1) {
                                    $rowPriv = $privilegeData->row();
                                    $privName = strtolower($rowPriv->name);
                                    $arrData[$rowAcl->url][] = $privName;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $arrData;
    }

    function generatePrivilegeAction(){
        $arrData = array();
        $id = $this->authenticate->getRoleId();
        $query = $this->db->get_where("user_role_acl", array("role_id" => $id));
        if ($query->num_rows() > 0) {
            $row = $query->row();

            $privilege = unserialize($row->privilege_resource);
            if ($privilege) {
                foreach ($privilege as $vv) {
                    $isNode = strpos($vv, "-");
                    if ($isNode == true) {
                        $dd = explode("-", $vv);
                        if (count($dd) == 2) {
                            $aclId = $dd[0];
                            $privilegeId = $dd[1];

                            $acl = $this->db->get_where("access_control_list", array("id" => $aclId));
                            if ($acl->num_rows() == 1) {
                                $rowAcl = $acl->row();

                                $privilegeData = $this->db->get_where("privilege_list", array("id" => $privilegeId));
                                if ($privilegeData->num_rows() == 1) {
                                    $rowPriv = $privilegeData->row();
                                    $privName = ucwords(strtolower($rowPriv->name));
                                    $arrData[$rowAcl->name][] = "btn{$privName}";
                                }
                            }
                        }
                    }
                }
            }
        }

        return $arrData;
    }

    function getCurrentActions(){
        $actions = array();
        $currentActions = $this->generatePrivileges();
        $privilegeName = $this->getPrivilegeName();
        if ($privilegeName) {
            if (isset($currentActions[$privilegeName]) && $currentActions[$privilegeName]) {
                $actions = $currentActions[$privilegeName];
            }
        }

        if (isset($currentActions["global_privileges"]) && $currentActions["global_privileges"]) {
            foreach ($currentActions["global_privileges"] as $privilege) {
                if (!in_array($privilege, $actions)) {
                    $actions[] = $privilege;
                }
            }
        }

        $defaultPrivileges = $this->core_layout->listXml();
        if ($defaultPrivileges) {
            foreach ($defaultPrivileges as $privilege) {
                if (!in_array($privilege, $actions)) {
                    $actions[] = $privilege;
                }
            }
        }

        $tempActions = array("back", "close");
        //$tempActions = array("back", "cancel", "close");
        foreach ($tempActions as $key => $value) { if(!in_array($value, $actions)){ $actions[] = $value; }}
        return $actions;
    }

    function listXml(){
        $arrData = array();
        $RoleId = $this->authenticate->getRoleId();
        if ($RoleId && $RoleId == 1 || $RoleId == 2) {
            $xmlFile = realpath('assets/static/xml/privileges/admin_privilege.xml');
            if (file_exists($xmlFile)) {
                $xmlstr = file_get_contents($xmlFile);
                $sitemap = new SimpleXMLElement($xmlstr);
                $privileges = $sitemap->privilege->data;
                if ($privileges) {
                    $arrData = (array)$privileges;
                }
            }
        }

        return $arrData;
    }

    function getIdleTimerState(){
        $response = false;
        $xmlFile = realpath('assets/static/xml/privileges/admin_privilege.xml');
        if (file_exists($xmlFile)) {
            $xmlstr = file_get_contents($xmlFile);
            $sitemap = new SimpleXMLElement($xmlstr);
            if(isset($sitemap->idle_timer)){
                $idleTimer = $sitemap->idle_timer->data;
                if ($idleTimer && $idleTimer == "on") {
                    $response = true;
                }
            }
        }
        
        return $response;
    }

    function getCurrentSession(){
        if (!$this->userdata) return false;
        return $this->userdata;
    }

    function getUserLoggedIn(){
        if (!$this->userdata) return false;

        $loggedIn = $this->userdata;
        if ($loggedIn) {

            $lastname = (isset($loggedIn["lastname"]) && $loggedIn["lastname"]) ? $loggedIn["lastname"] : "";
            $firstname = (isset($loggedIn["firstname"]) && $loggedIn["firstname"]) ? $loggedIn["firstname"] : "";
            $middlename = (isset($loggedIn["middlename"]) && $loggedIn["middlename"]) ? $loggedIn["middlename"] : "";

            $currentUserData = (object)$this->getUserData($loggedIn["id"]);
            $currentRoleId = (isset($currentUserData->role_id) && $currentUserData->role_id) ? $currentUserData->role_id : 0;
            $currentDisplayName = (isset($currentUserData->display_name_1) && $currentUserData->display_name_1) ? $currentUserData->display_name_1 : "{$firstname} {$lastname}";

            $roleId = (isset($loggedIn["role_id"]) && $loggedIn["role_id"]) ? $loggedIn["role_id"] : $currentRoleId;
            $groupId = (isset($loggedIn["group_id"]) && $loggedIn["group_id"]) ? $loggedIn["group_id"] : 0;
            $moduleId = (isset($loggedIn["module_id"]) && $loggedIn["module_id"]) ? $loggedIn["module_id"] : 0;

            $arrData = array();
            $arrData["id"] = $loggedIn["id"];
            $arrData["employee_id"] = $loggedIn["emp_id"];
            $arrData["username"] = $loggedIn["username"];
            $arrData["display_name"] = $currentDisplayName;
            $arrData["email"] = $loggedIn["email"];
            $arrData["company"] = $loggedIn["company"];
            $arrData["department"] = $loggedIn["department"];
            $arrData["group_id"] = $groupId;
            $arrData["role_id"] = $roleId;
            $arrData["module_id"] = $moduleId;

            return $arrData;
        }
    }

    public function getUserId(){
        $userData = $this->userdata;
        if (isset($userData["id"]) && $userData["id"]) {
            return $userData["id"];
        } else {
            return false;
        }
    }

    public function getCurrentEmployeeId(){
        $userData = $this->userdata;
        if (isset($userData["emp_id"]) && $userData["emp_id"]) {
            return $userData["emp_id"];
        } else {
            return false;
        }
    }

    /*** new function User Data ***/
    public function getUserData($id = null){
        if (!$this->userdata) return false;

        $session = $this->userdata;
        $userId = ($id) ? $id : $session["id"];
        if ($userId) {
            $arrData = array();
            $tableEmployees = "tblemployees a";
            $tableUsers = "tblusers b";

            $this->db->select("a.id, a.biometricno, a.idno, a.lastname, a.firstname, a.middlename, a.suffix, a.employee_status, b.role_id, b.group_id, b.email, b.username, b.reset_pin, b.is_suspended");
            $this->db->from($tableEmployees);
            $this->db->join($tableUsers, "b.emp_id = a.id", "left");
            $this->db->where("b.id", $userId);
            $this->db->group_by("b.id");
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                $row = $query->row_array();
                $data = $this->getDisplayName($row);
                if ($data) {
                    foreach ($data as $key => $value) {
                        $row[$key] = $value;
                    }
                }

                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getEmployeeData($id = null){
        if (!$this->userdata) return false;

        $session = $this->userdata;
        $employeeId = ($id) ? $id : $session["emp_id"];
        if ($employeeId) {
            $arrData = array();
            $tableEmployees = "tblemployees a";
            $tableUsers = "tblusers b";

            $this->db->select("a.id, a.biometricno, a.idno, a.lastname, a.firstname, a.middlename, a.suffix, a.employee_status, b.role_id, b.group_id, b.email, b.username, b.reset_pin, b.is_suspended");
            $this->db->from($tableEmployees);
            $this->db->join($tableUsers, "b.emp_id = a.id", "left");
            $this->db->where("a.id", $employeeId);
            $this->db->group_by("a.id");
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                $row = $query->row_array();
                $data = $this->getDisplayName($row);
                if ($data) {
                    foreach ($data as $key => $value) {
                        $row[$key] = $value;
                    }
                }

                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getDisplayName($arrData = array()){
        if ($arrData) {
            $lastname = $arrData["lastname"];
            $firstname = $arrData["firstname"];
            $middlename = strtoupper($arrData["middlename"]);
            $suffix = strtoupper($arrData["suffix"]);

            $nSuffix = "";
            $nMiddleName = "";

            if ($suffix !== "" && ($suffix !== "N/A" && $suffix !== "NONE")) {
                $nSuffix = $suffix;
            }
            if ($middlename !== "" && ($middlename !== "N/A" && $middlename !== "NONE")) {
                $nMiddleName = $middlename;
            }

            $displayName1 = "";
            $displayName2 = "";

            $nMiddleName = trim($nMiddleName);
            $nMiddleName = substr($nMiddleName, 0, 1);
            $nMiddleName = ($nMiddleName) ? "{$nMiddleName}." : "";
            if ($nMiddleName && $nSuffix) {
                $displayName1 = "{$lastname}, {$firstname} {$nMiddleName} {$nSuffix}";
                $displayName2 = "{$firstname} {$nMiddleName} {$lastname} {$nSuffix}";
            } else if ($nSuffix) {
                $displayName1 = "{$lastname}, {$firstname} {$nSuffix}";
                $displayName2 = "{$firstname} {$lastname} {$nSuffix}";
            } else if ($nMiddleName) {
                $displayName1 = "{$lastname}, {$firstname} {$nMiddleName}";
                $displayName2 = "{$firstname} {$nMiddleName} {$lastname}";
            } else {
                $displayName1 = "{$lastname}, {$firstname}";
                $displayName2 = "{$firstname} {$lastname}";
            }

            $displayName1 = strtoupper($displayName1);
            $displayName2 = strtoupper($displayName2);

            $data = array();
            $data["display_name_0"] = $displayName1;
            $data["display_name_1"] = $displayName2;

            return $data;
        } else {
            return false;
        }
    }

    /*** new function User Data ***/

    function generateCode($length = 13){
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $genCode = substr(str_shuffle($str), 0, $length);
        return $genCode;
    }

    /*** email function ***/
    public function send_email($module, $email_title, $content_title, $content, $overrideMailer = array()){
        $email_module = $this->getEmailModule($module);
        if ($email_module) {
            $coreLogs = $this->coreLogs();
            $coreLogs->setLogModule("core");
            $coreLogs->setLogTable("gccmaster.email_template");
            $coreLogs->setLogFieldId($email_module->id);

            $sendTo = unserialize($email_module->send_to);
            $sendCc = unserialize($email_module->cc_to);
            $sendBcc = unserialize($email_module->bcc_to);

            $sendTo = (isset($overrideMailer["send_to"]) && $overrideMailer["send_to"]) ? $overrideMailer["send_to"] : $sendTo;
            $sendCc = (isset($overrideMailer["send_cc"]) && $overrideMailer["send_cc"]) ? $overrideMailer["send_cc"] : $sendCc;
            $sendBcc = (isset($overrideMailer["send_bcc"]) && $overrideMailer["send_bcc"]) ? $overrideMailer["send_bcc"] : $sendBcc;

            $sendToData = ($sendTo && is_array($sendTo)) ? implode(",", $sendTo) : "";
            $ccToData = ($sendCc && is_array($sendCc)) ? implode(",", $sendCc) : "";
            $bccToData = ($sendBcc && is_array($sendBcc)) ? implode(",", $sendBcc) : "";
            $sendToData = ($sendToData) ? $sendToData : "seniordeveloper01@gccaggregates.com";
            $emailSender = $this->doMailer($email_title, $overrideMailer);
            if ($emailSender) {
                $emailSender->to($sendToData);
                if ($ccToData) {
                    $emailSender->cc($ccToData);
                }
                if ($bccToData) {
                    $emailSender->bcc($bccToData);
                }

                $content_title = ($content_title) ? $content_title : "This is a sample title";
                $content = ($content) ? $content : "This is a sample Content";
                $emailSender->subject($content_title);
                $emailSender->message($content);
                $sent = $emailSender->send();
                if(!$sent){ $coreLogs->logNotification($emailSender->print_debugger(), "error"); }

                if ($sent) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function doMailer($email_title = null, $overrideMailer = array()){
        $serverName = $_SERVER['SERVER_NAME'];
        $serverName = strtolower($serverName);
        $siteCode = $this->config->item('site_unique_code');
        if($siteCode){
            $arrDevelopmentSite = array("localhost", "dev.gccph.com", "192.168.7.96");
            $qTemp = $this->db->get_where($this->emailProtocolTable, array("unique_code"=>$siteCode, "server_name"=>$serverName));
            if($qTemp->num_rows() == 1){
                $email_title = (in_array($serverName, $arrDevelopmentSite))? $email_title." [ DEVELOPMENT SERVER ] ": $email_title;
                $tempRow = $qTemp->row();

                $smtpUser = $tempRow->smtp_user;
                $smtpPassword = $tempRow->smtp_pass;

                $smtpUser = (isset($overrideMailer["email_user"]) && $overrideMailer["email_user"]) ? $overrideMailer["email_user"] : $smtpUser;
                $smtpPassword = (isset($overrideMailer["email_pass"]) && $overrideMailer["email_pass"]) ? $overrideMailer["email_pass"] : $smtpPassword;

                 /*** protocol: [ smtp ], mail ***/
                 /*** smtp_host: [ smtp.googlemail.com ] ***/
                 /*** smtp_port: [ 587 ], 465 ***/
                 /*** smtp_crypto: [ tls ], ssl ***/

                $config = Array(
                    'protocol' => $tempRow->protocol,
                    'smtp_host' => $tempRow->smtp_host,
                    'smtp_port' => intval($tempRow->smtp_port),
                    'smtp_crypto' => $tempRow->smtp_crypto,
                    'smtp_user' => $smtpUser,
                    'smtp_pass' => $smtpPassword,
                    'smtp_mailtype' => 'html',
                    'charset' => 'utf-8',
                    'wordwrap' => TRUE,
                );

                if(isset($overrideMailer["config"]) && $overrideMailer["config"]){
                    foreach ($overrideMailer["config"] as $key => $value) {
                        $config[$key] = $value;
                    }
                }

                if ($this->email->initialize($config)) {
                    $this->email->set_newline("\r\n");
                    $this->email->set_mailtype("html");
                    $this->email->from($smtpUser, $email_title);

                    return $this->email;
                } else {
                    return false;
                }
            }
        }else{
            return false;
        }

    }

    private function getEmailModule($module = null){
        if ($module) {
            $this->db->from("email_template");
            $this->db->where('name', $module);
            $this->db->where('send_email', 1);
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                $row = $query->row();
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getTimeAgo($timestamp){
        $time_ago = strtotime($timestamp);
        $current_time = time();
        $time_difference = $current_time - $time_ago;
        $seconds = $time_difference;

        $minutes = round($seconds / 60); // value 60 is seconds
        $hours = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec
        $days = round($seconds / 86400); //86400 = 24 * 60 * 60;
        $weeks = round($seconds / 604800); // 7*24*60*60;
        $months = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60
        $years = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60

        if ($seconds <= 60) {
            return "Just Now";
        } else if ($minutes <= 60) {
            if ($minutes == 1) {
                return "one minute ago";
            } else {
                return "$minutes minutes ago";
            }
        } else if ($hours <= 24) {
            if ($hours == 1) {
                return "an hour ago";
            } else {
                return "$hours hrs ago";
            }
        } else if ($days <= 7) {
            if ($days == 1) {
                return "yesterday";
            } else {
                return "$days days ago";
            }
        } else if ($weeks <= 4.3) {
            if ($weeks == 1) {
                return "a week ago";
            } else {
                return "$weeks weeks ago";
            }
        } else if ($months <= 12) {
            if ($months == 1) {
                return "a month ago";
            } else {
                return "$months months ago";
            }
        } else {
            if ($years == 1) {
                return "one year ago";
            } else {
                return "$years years ago";
            }
        }
    }

    /*** email function ***/

    function insertArchiveLog($archived_table, $archived_id, $status = 1) // default > 1=archived, 2=restored
    {
        $user = $this->getUserLoggedIn();
        $employee_id = $user['employee_id'];

        $fields = array(
            "archived_table" => $archived_table,
            "archived_id" => $archived_id, // asset id or item id
            "archived_by" => $employee_id, // employee id, currently logged in
            "archived_at" => $this->current_data_time->format("Y-m-d H:i:s"),
            "status" => $status,
        );

        return $this->db->insert("gccmaster.archived_items", $fields);
    }

    public function getEmployee($employee_id = null){
        if ($employee_id) {
            $sqlSelect = "employees.*, IFNULL(company.id, 0) as temp_company_id, company.code as company_code, company.description as company_description, ";
            $sqlSelect .= "IFNULL(department.id, 0) as temp_department_id, department.code as department_code, department.description department_description, ";
            $sqlSelect .= "IFNULL(position.id, 0) as temp_position_id, position.name as position_name, position.job_desc";
            $this->db->select($sqlSelect);
            $this->db->from("gccmaster.tblemployees employees");
            $this->db->join("gcchris.tblcompanies company", "company.id = employees.company_id", "LEFT");
            $this->db->join("gcchris.tbldepartments department", "department.id = employees.department_id OR department.description = employees.department_id", "LEFT");
            $this->db->join("gcchris.tblposition position", "position.id = employees.position", "LEFT");
            $this->db->where("employees.id", $employee_id);
            $this->db->limit(1);
            $query = $this->db->get();
            $row = null;

            if ($query->num_rows() == 1) {
                $row = $query->row();
                if ($row->temp_company_id) {
                    $row->company_id = $row->company_code;
                }
                if ($row->temp_department_id) {
                    $row->department_id = $row->department_code;
                }
                if ($row->temp_position_id) {
                    $row->position = $row->position_name;
                }

                if (empty($row->job_desc)) {
                    $row->job_desc = $this->db->get_where("gcchris.tblposition position", array("name" => $row->position))->row("job_desc");
                }
            }
        }

        return $row;
    }

    public function renderModalContent($module=null, $model=null, $function=null){
        $resultset = array();
        if($module && $model && $function){
            $temp = $this->load->model("{$module}/{$model}", "{$model}");
            $resultset = $this->$model->$function();
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    /* FUNCTION CREATE BY EDWIN OCT. 21, 2020 09:31 */
    public function create_txt_log_file($path, $filename, $data, $create_dir_recursively = false, $created_dir_permission = 0777) {
        if (!file_exists($path)) {
            mkdir($path, $created_dir_permission, $create_dir_recursively);
        }

        return write_file("$path/$filename",  $data, 'a');
    }
    /* END FUNCTION CREATE BY EDWIN OCT. 21, 2020 09:31 */

	public function adminBackendTemplate($parameters = array()){
		$this->load->view("core/templates/header", $parameters);
		if(isset($$parameters["view"]) && $parameters["view"]){
			$this->load->view($parameters["view"], $paremeters);
		}
		$this->load->view("core/templates/footer", $parameters);
	}

    public function getRecepientEmailByName($name=null){
        $arrData = array();
        if($name){
            $this->db->select("send_to, cc_to as send_cc, bcc_to as send_bcc");
            $this->db->from($this->emailTemplateTable);
            $this->db->where("name", $name);
            $this->db->where("send_email", 1);
            $query = $this->db->get();
            if($query->num_rows() == 1){
                $tempRow = $query->row();
                $tempRow->send_to = @unserialize($tempRow->send_to);
                $tempRow->send_cc = @unserialize($tempRow->send_cc);
                $tempRow->send_bcc = @unserialize($tempRow->send_bcc);
                $arrData = $tempRow;
            }
        }

        return $arrData;
    }

    public function getSessionStatus(){
        $resultset = array();
        if($this->userdata){ $resultset["response"] = true;
        }else{ $resultset["response"] = false; }
        return $resultset;
    }
    // for cash advance notification
    function getPayrollPendings(){
        $result = array();
        $get = $this->input->get();
        $rowData = $this->get_payroll_pending($get['type']);
        $rowPayCount = $this->get_pyrll_pending_count();
        $rowAcctgCount = $this->get_acctg_pending_count();
        $rowApprovalCount = $this->get_approval_pending_count();

        $result['data'] = $rowData;
        $result['pay_count'] = $rowPayCount;
        $result['acctg_count'] = $rowAcctgCount;
        $result['approval_count'] = $rowApprovalCount;

        return $result;
    }
    function get_payroll_pending($type){
        $getPrevilage = $this->personal_roles_for_notif();

        $company = $this->userdata['company'];
        $result = array();
        $date= date("Y-m-d", strtotime("-1 year"));
        $this->db->select('a.id as ca_id, UPPER(CONCAT(b.firstname, " ", b.lastname)) as fullname, a.status as ca_status, b.company_id as comp, UPPER(a.position) as pst, a.reference_no as ca_ref, UPPER(a.department) as dept, FORMAT(a.amt_applied, 2) as amt, a.created_dt as created');
        $this->db->from('gcceforms.cash_advance a');
        $this->db->join('gccmaster.tblemployees b', 'b.id = a.employee', 'left');
        if($type == 'payroll'){
            $this->db->where('a.status', 'Payroll Balance Pending');
        }else if($type == 'acctg'){
            $this->db->where('a.status', 'Accounting Balance Pending');
        }else{
            $this->db->where('a.status', 'Awaiting Approval');
        }
        $this->db->where('DATE(a.created_dt) >=', $date);
        
        if(in_array('ca_acctg_fo_notif', $getPrevilage) && $type == 'acctg' && $company != ''){
            $this->db->where('b.company_id', $company);
        }
        
        $this->db->where('b.employee_status', 'Active');
        $this->db->group_by('a.id');
        $this->db->limit('10');

        $query = $this->db->get();
        $q = $query->result();
        $result = $q;
        return $result;
    }

    function get_payroll_pending_count(){
        $getPrevilage = $this->personal_roles_for_notif();
        if(in_array('ca_payroll_notif', $getPrevilage)){
            $date = date("Y-m-d", strtotime("-1 year"));
            $this->db->from('gcceforms.cash_advance a');
            $this->db->join('gccmaster.tblemployees b', 'b.id = a.employee', 'left');
            $this->db->where('a.status', 'Payroll Balance Pending');
            $this->db->where('DATE(a.created_dt) >=', $date);
            $this->db->where('b.employee_status', 'Active');
            $this->db->group_by('a.id');

            $query = $this->db->get();
            $q = $query->result();
            $result = $query->num_rows(); 
        }else{
            $result = 0;  
        }

        return $result;
    }

    function get_pyrll_pending_count(){
        $date = date("Y-m-d", strtotime("-1 year"));
        $getPrevilage = $this->personal_roles_for_notif();
        if(in_array('ca_payroll_notif', $getPrevilage)){
            $this->db->from('gcceforms.cash_advance a');
            $this->db->join('gccmaster.tblemployees b', 'b.id = a.employee', 'left');
            $this->db->where('a.status', 'Payroll Balance Pending');
            $this->db->where('DATE(a.created_dt) >=', $date);
            $this->db->where('b.employee_status', 'Active');
            $this->db->group_by('a.id');

            $query = $this->db->get();
            $q = $query->result();
            $result = $query->num_rows();
        }else{
            return 0;
        }
        return $result;
    }

    function get_acctg_pending_count(){
        $company = $this->userdata['company'];
        $date= date("Y-m-d", strtotime("-1 year"));
        $getPrevilage = $this->personal_roles_for_notif();

        if(in_array('ca_acctg_notif', $getPrevilage) || in_array('ca_acctg_fo_notif', $getPrevilage)){
            $this->db->from('gcceforms.cash_advance a');
            $this->db->join('gccmaster.tblemployees b', 'b.id = a.employee', 'left');
            $this->db->where('a.status', 'Accounting Balance Pending');
            if(in_array('ca_acctg_fo_notif', $getPrevilage) && $company != ''){
                $this->db->where('b.company_id', $company);
            }
            $this->db->where('DATE(created_dt) >=', $date);
            $this->db->where('b.employee_status', 'Active');
            $this->db->group_by('a.id');

            $query = $this->db->get();
            $q = $query->result();
            $result = $query->num_rows();
        }else{
            $result = 0;
        }

        return $result;
    }

    function get_approval_pending_count(){
        $date = date("Y-m-d", strtotime("-1 year"));
        $getPrevilage = $this->personal_roles_for_notif();
        if(in_array('ca_approval_notif', $getPrevilage)){
            $this->db->from('gcceforms.cash_advance a');
            $this->db->join('gccmaster.tblemployees b', 'b.id = a.employee', 'left');
            $this->db->where('a.status', 'Awaiting Approval');
            $this->db->where('DATE(a.created_dt) >=', $date);
            $this->db->where('b.employee_status', 'Active');
            $this->db->group_by('a.id');

            $query = $this->db->get();
            $q = $query->result();
            $result = $query->num_rows();
        }else{
            return 0;
        }
        return $result;
    }

    // for cash advance notification 
    function personal_roles_for_notif(){
        $arrData = array();
        $id = $this->authenticate->getRoleId();
        $query = $this->db->get_where("user_role_acl", array("role_id" => $id));
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $privilege = unserialize($row->privilege_resource);
            if ($privilege) {
                foreach ($privilege as $vv) {
                    $isNode = strpos($vv, "-");
                    if ($isNode == true) {
                        $dd = explode("-", $vv);
                        if (count($dd) == 2) {
                            $aclId = $dd[0];
                            $privilegeId = $dd[1];

                            $acl = $this->db->get_where("access_control_list", array("id" => $aclId, 'name' => 'ca_masterfile'));
                            if ($acl->num_rows() == 1) {
                                $rowAcl = $acl->row();

                                $privilegeData = $this->db->get_where("privilege_list", array("id" => $privilegeId));
                                if ($privilegeData->num_rows() == 1) {
                                    $rowPriv = $privilegeData->row();
                                    $privName = strtolower($rowPriv->name);
                                    $arrData[] = $privName;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $arrData;
    }

    function get_all_ca(){
        $company = $this->userdata['company'];
        $payroll = 0;
        $acctg = 0;
        $approve = 0;
        $result = array();
        $where = '';

        $has_previ = $this->personal_roles_for_notif();
        $date = date("Y-m-d", strtotime("-1 year"));

        $this->db->from('gcceforms.cash_advance a');
        $this->db->join('gccmaster.tblemployees b', 'b.id = a.employee', 'left');

        if(in_array('ca_payroll_notif', $has_previ)){
            $where .= 'a.status = "Payroll Balance Pending"';
        }else{
            $where .= '';
        }

        if($where != ''){
            if(in_array('ca_acctg_notif', $has_previ) || in_array('ca_acctg_fo_notif', $has_previ)){
                $where .= ' OR ';
            }
        }

        if(in_array('ca_acctg_notif', $has_previ) || in_array('ca_acctg_fo_notif', $has_previ)){
            $where .= 'a.status = "Accounting Balance Pending"';

            if(in_array('ca_acctg_fo_notif', $has_previ) && $company != ''){
                $where .= " AND b.company_id = ".$company.' ';
            }
        }

        if($where != ''){
            if(in_array('ca_approval_notif', $has_previ)){
                $where .= ' OR ';
            }
        }

        if(in_array('ca_approval_notif', $has_previ)){
            $where .= 'a.status = "Awaiting Approval"';
        }
        $to_where = '('.$where.')';
        $this->db->where($to_where);
        $this->db->where('DATE(a.created_dt) >=', $date);
        $this->db->where('b.employee_status', 'Active');
        $this->db->group_by('a.id');
        $query = $this->db->get();
        $total = $query->num_rows();

        $result['total'] = $total;
        return $total;
    }

    public function deleteCookie($cookieName) {
        if (isset($_COOKIE[$cookieName])) {
            // Delete the cookie by setting its expiration to the past
            setcookie($cookieName, '', time() - 3600, '/'); // Expire 1 hour ago
            unset($_COOKIE[$cookieName]); // Remove the cookie from the $_COOKIE array
        }
    }
    
}