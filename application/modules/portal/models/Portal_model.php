<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Portal_model extends CI_Model{
    private $userRoleTable = "user_role_acl";
    private $moduleTable = "modules";

	function __construct(){
        parent::__construct();
        $session_data = $this->session->userdata('logged_in');
    }
    
    function getPortalModules(){
        $roleId = $this->authenticate->getRoleId();
        $data = array();

        $moduleId = array();
        $modules = array();
        $moduleResource = array();

        if($roleId || $roleId == "0"){
            $this->db->order_by("sort", "ASC");
            $module = $this->db->get_where($this->moduleTable, array("parent_id"=>0, "is_active"=>1, "status"=>1));
            if($module->num_rows() > 0){
                foreach($module->result() as $rs){
                    $moduleId[] = $rs->id;
                    $modules[] = $rs;
                }
            }

            $userRole = $this->db->get_where($this->userRoleTable, array("role_id"=>$roleId));
            if($userRole->num_rows() == 1){
                $row = $userRole->row();
                $moduleColumn = isset($row->module_resource) ? unserialize($row->module_resource) : array();
                $moduleResource = is_array($moduleColumn)? $moduleColumn: array();
            }
        }

        $data["module_id"] = $moduleId;
        $data["modules"] = $modules;
        $data["module_resource"] = $moduleResource;
        return $data;
    }

    function getPortalSideNav(){
        $menuItems = array();
        $arrData = $this->getPortalModules();
        $menuItems["aclMenu"] = $arrData;
        $menuItems["roleResource"] = $this->authenticate->getModuleResource();
        $html = $this->load->view("core/access_control/html/portal_side_nav", $menuItems, true);
        // var_dump($this->authenticate->getModuleResource());
        return $html;
    }

    function getPortalSubModule($module=null){
        $roleId = $this->authenticate->getRoleId();
        $data = array();
        $moduleId = array();
        $modules = array();         
        $moduleResource = array();

        if($roleId || $roleId == "0"){
            $submodule = $this->db->get_where($this->moduleTable, array("name"=>$module, "parent_id"=>0, "is_active"=>1, "status"=>1));
            if($submodule->num_rows() == 1){
                $row = $submodule->row();
                $this->db->order_by("sort", "ASC");
                $subModule = $this->db->get_where($this->moduleTable, array("parent_id"=>$row->id, "is_active"=>1, "status"=>1));
                if($subModule->num_rows() > 0){
                    foreach($subModule->result() as $rs){
                        $moduleId[] = $rs->id;
                        $modules[] = $rs;
                    }
                }

                $userRole = $this->db->get_where($this->userRoleTable, array("role_id"=>$roleId));
                if($userRole->num_rows() == 1){
                    $row = $userRole->row();
                    $moduleColumn = unserialize($row->module_resource);
                    $moduleResource = is_array($moduleColumn)? $moduleColumn: array();
                }
            }
        }

        if($moduleId){
            $allowSubPage = false;
            foreach($moduleId as $id){
                if(in_array($id, $moduleResource)){
                    $allowSubPage = true;
                }
            }
            
            if($allowSubPage){
                $data["module_id"] = $moduleId;
                $data["modules"] = $modules;
                $data["module_resource"] = $moduleResource;
                $html = $this->load->view("portal/submodule/portal_content", $data, true);
                return $html;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function getPortalVersion(){
        $data = array();
        $versions = $this->db->order_by("id","DESC")->get_where("gccmaster.versions")->result();
        $data = $versions;
        return $data;
    }

    function addPortalVersion(){
        $post = $this->input->post();
        $data = array(
            "version"=>$post['version'],
            "description"=>$post['description']
        );
        $insert = $this->db->insert("gccmaster.versions", $data);
        return $insert;
    }

    function getUsername(){
        $userData = $this->session->userdata('logged_in');
        $tempName = [];
        $data = $this->core_layout->getEmployeeData($userData["emp_id"]);
        $data = (object) $data;
        $tempName['name'] = isset($data->display_name_1) && $data->display_name_1 ? strtoupper($data->display_name_1)."!": "NO ASSIGNED NAME";
        return $tempName;
    }

    function getUnapprovedLoa(){
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $userData = $this->session->userdata('logged_in');
        $all_loa = $this->db->get_where("gcceforms.loa", array("status"=>"Pending", "DATE(created_dt) >="=>$check))->num_rows();

        $head_dept_arr = array();
        $this->db->select("id");
        $this->db->from("gcchris.tbldepartments");
        $this->db->where("head_id", $userData['emp_id']);
        $this->db->where("is_archived", 0);
        $dept_query = $this->db->get();
        
        $all_loa_under_head = 0;
        
        foreach($dept_query->result() as $dept_data){
            $this->db->select("count(loa.id) as count");
            $this->db->from("gcceforms.loa loa");
            $this->db->join("gccmaster.tblemployees emp", "emp.id=loa.employee");
            $this->db->join("gcchris.tbldepartments dept", "dept.id=emp.department_id");
            $this->db->where("emp.department_id", $dept_data->id);
            $this->db->where("loa.status", "Pending");
            $this->db->where("DATE(loa.created_dt) >=", $check);
            $empData = $this->db->get();
            $result = $empData->row();
            $all_loa_under_head += $result->count;
            $head_dept_arr['head_data'] = $all_loa_under_head;
        }

        $empLoa = $this->db->get_where("gcceforms.loa", array("employee"=>$userData['emp_id'], "status"=>'Pending', "DATE(created_dt) >="=>$check))->num_rows();
        $allEmpLoa = $this->db->get_where("gcceforms.loa", array("employee"=>$userData['emp_id'], "DATE(created_dt) >="=>$check))->num_rows();
        $allLoa = $this->db->get_where("gcceforms.loa", array("employee"=>$userData['emp_id'], "DATE(created_dt) >="=>$check))->num_rows();
        // var_dump($this->db->last_query());
        
        
        $head_dept_arr['all_loa'] = $all_loa;
        if(isset($head_dept_arr['head_data'])){
            $head_dept_arr['employee_count'] = $head_dept_arr['head_data'];
            $head_dept_arr['scroll_width'] = $head_dept_arr['head_data'] > 0 ? number_format(($head_dept_arr['head_data'] / $head_dept_arr['all_loa']) * 100, 2) : 0;
        }else{
            $head_dept_arr['employee_count'] = $empLoa;
            $head_dept_arr['scroll_width'] = $empLoa > 0 ? number_format(($empLoa / $allEmpLoa) * 100, 2) : 0;
        }
        $head_dept_arr['progress_loa_pending'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$head_dept_arr['scroll_width']}%'></div>";

        return $head_dept_arr;
    }

    function getTravelOrderRecommendation(){
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $userData = $this->session->userdata('logged_in');

        $this->db->trans_start();

        $this->db->select("id");
        $this->db->from("gcchris.tbldepartments");
        $this->db->where("head_id", $userData['emp_id']);
        $this->db->where("is_archived", 0);
        $dept_query = $this->db->get();
        $head_dept_to = array();
        $all_to_under_head = 0;
        if(count($dept_query->result()) > 0){
            foreach($dept_query->result() as $dept_data){
                $this->db->select("count(to_p.id) as count");
                $this->db->from("gcceforms.travel_order to");
                $this->db->join("gcceforms.travel_personnel to_p", "to_p.travel_order_id=to.id", "LEFT");
                $this->db->join("gccmaster.tblemployees emp", "emp.id=to_p.employee_id", "LEFT");
                $this->db->join("gcchris.tbldepartments dept", "dept.id=emp.department_id", "LEFT");
                $this->db->where("emp.department_id", $dept_data->id);
                $this->db->where("to.status", "Pending");
                $this->db->where("DATE(created_dt) >=", $check);
                $empData = $this->db->get();
                $result = $empData->row();
                $arrData['count'] = $result->count;
                $all_to_under_head += $result->count;
                $head_dept_to['travel_order'] = $all_to_under_head;
            }
        }else{
            if($userData['department']){
                $this->db->select("count(to_p.id) as count");
                $this->db->from("gcceforms.travel_order to");
                $this->db->join("gcceforms.travel_personnel to_p", "to_p.travel_order_id=to.id", "LEFT");
                $this->db->join("gccmaster.tblemployees emp", "emp.id=to_p.employee_id", "LEFT");
                $this->db->join("gcchris.tbldepartments dept", "dept.id=emp.department_id", "LEFT");
                $this->db->where("emp.department_id", $userData['department']);
                $this->db->where("to.status", "Pending");
                $this->db->where("DATE(created_dt) >=", $check);
                $empData = $this->db->get();
                $result = $empData->row();
                $arrData['count'] = $result->count;
                $all_to_under_head += $result->count;
                $head_dept_to['travel_order'] = $all_to_under_head;
            }
        }

        if(count($dept_query->result()) > 0){
            $head_dept_to['head_data'] = true;
        }else{
            $head_dept_to['head_data'] = false;
        }

        $privUrl = $this->core_layout->generatePrivilegesUrl();
        
        $privAction = $this->core_layout->generatePrivileges();

        $actions = array();
        
        $head_dept_to['travel_order'] = false;
        foreach($privAction as $key_to => $privAction_temp){
            if($key_to == "to_masterfile"){
                $actions[] = $privAction_temp;
            }else{
            }
        }
        if(!empty($actions[0])){
            $head_dept_to['travel_order'] = true;
        }

        if(!in_array('dashboard_recommendation', $actions[0])){
            $all_to_under_head = 0;
        }

        $head_dept_to['recommendation'] = $all_to_under_head;
        
        $all_to = $this->db->get_where("gcceforms.travel_order", array("DATE(created_dt) >="=>$check))->num_rows();
        $all_to_emp = $this->db->get_where("gcceforms.travel_order", array("DATE(created_dt) >="=>$check, "created_id"=>$userData['emp_id']))->num_rows();

        $head_dept_to['scroll_width_recommend'] = $all_to_under_head > 0 ? number_format(($all_to_under_head / $all_to) * 100, 2) : 0;
        

        if(in_array("recommend", $actions[0])){
            $head_dept_to['to_recommendation_count'] = $head_dept_to['travel_order'] > 0 ? number_format(($head_dept_to['travel_order'] / $all_to) * 100, 2) : 0;
        }else if(in_array("new", $actions[0])){
            $head_dept_to['recommendation'] = $this->db->get_where("gcceforms.travel_order", array("status"=>"Pending","DATE(created_dt) >="=>$check, "created_id"=>$userData['emp_id']))->num_rows();
            $head_dept_to['to_recommendation_count'] = $head_dept_to['recommendation'] > 0 ? number_format(($head_dept_to['recommendation'] / $all_to_emp) * 100, 2) : 0;
        }else{
            $head_dept_to['recommendation'] = 0;
            $head_dept_to['to_recommendation_count'] = 0;
        }

        $head_dept_to['progress_to_recommendation'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$head_dept_to['to_recommendation_count']}%'></div>";

        if(in_array("approve_action", $actions[0])){
            $for_approval = $this->db->get_where("gcceforms.travel_order", array("status"=>"Recommend_Approved","DATE(created_dt) >="=>$check))->num_rows();
            $head_dept_to['for_approval'] = $for_approval;
            $head_dept_to['to_approval_count'] = $for_approval > 0 ? number_format(($for_approval / $all_to) * 100, 2) : 0;
        }else if(in_array("new", $actions[0])){
            $for_approval = $this->db->get_where("gcceforms.travel_order", array("status"=>"Recommend_Approved", "DATE(created_dt) >="=>$check, "created_id"=>$userData['emp_id']))->num_rows();
            $head_dept_to['for_approval'] = $for_approval;
            $head_dept_to['to_approval_count'] = $for_approval > 0 ? number_format(($for_approval / $all_to_emp) * 100, 2) : 0;
        }else{
            $head_dept_to['to_approval_count'] = 0;
            $head_dept_to['for_approval'] = 0;
        }
        $head_dept_to['progress_to_approval'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$head_dept_to['to_approval_count']}%'></div>";


        if(in_array("accomplishment", $actions[0])){
            $for_accomplishment = $this->db->get_where("gcceforms.travel_order", array("status"=>"Approved","DATE(created_dt) >="=>$check))->num_rows();
            $head_dept_to['for_accomplishment'] = $for_accomplishment;
            $head_dept_to['to_accomplishment_count'] = $for_accomplishment > 0 ? number_format(($for_accomplishment / $all_to) * 100, 2) : 0;
        }else{
            $head_dept_to['for_accomplishment'] = 0;
            $head_dept_to['to_accomplishment_count'] = 0;
        }
        $head_dept_to['progress_to_accomplishment'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$head_dept_to['to_accomplishment_count']}%'></div>";

        $head_dept_to['privileges'] = $actions;
        $this->db->trans_complete();
        // var_dump($privAction);
        return $head_dept_to;
    }

    public function getCompanyCollection($id=null)
    {
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        $this->db->select('id, code text');
        if($id!=null){
          $this->db->where('id',$id);
        }
        $this->db->like('CONCAT(description, code)', $q, 'both');
        return array('results' => $this->db->get('gcchris.tblcompanies')->result());
    }

    function getAccountability(){
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $userData = $this->session->userdata('logged_in');
        $company = isset($post['company']) && !empty($post['company']) ? $post['company'] : $userData['company'];
        $companyResult = $this->getCompanyCollection($company);
        $companyName = $companyResult['results'][0]->text;
        $companyId = $companyResult['results'][0]->id;
        $this->db->trans_start();
        $privAction = $this->core_layout->generatePrivileges();
        $acct_data = array();
        $actions = array();
        $result = array();
        foreach($privAction as $key_acct => $privAction_temp){
            if($key_acct == "accountability_for_releasing"){
                $actions[] = $privAction_temp;
                $acct_data['accountability'] = true;
            }else{
                $acct_data['accountability'] = false;
            }
        }

        $all_acct = $this->db->get_where("gcceforms.accountability", array("DATE(created_dt) >="=>$check))->num_rows();
        $all_acct_emp = $this->db->get_where("gcceforms.accountability", array("DATE(created_dt) >="=>$check, "created_id"=>$userData['emp_id'],"company"=>$company))->num_rows();
        
        if(!empty($actions[0])){
          if(in_array("dept_head", $actions[0])){
            $companyCollection = $this->getCompanyCollection();
            $companies = $companyCollection['results'];
            if (in_array("acct_note", $actions[0])) {
              $acct_data = array();
              foreach ($companies as $company) {
                  $companyId = $company->id;
                  $companyCode = $company->text;
                  $pending_acct_notes = $this->db->get_where("gcceforms.accountability", array(
                      "status" => "Pending Accounting Notes",
                      "DATE(created_dt) >=" => $check,
                      "company" => $companyId
                  ))->num_rows();
                  // Add data only if num_rows is greater than 0
                  if ($pending_acct_notes > 0) {
                      $acct_data[$companyCode] = array(
                          "company" => $companyId,
                          "accountability_acct_note_count" => number_format(($pending_acct_notes / $all_acct) * 100, 2),
                          "acct_note" => $pending_acct_notes,
                          "progress_accountability_acct_note" => "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: " . number_format(($pending_acct_notes / $all_acct) * 100, 2) . "%'></div>",
                      );
                  }
              }
              if (!empty($acct_data)) {
                  $result['acct_note'] = $acct_data;
              }
              else{$result['acct_note'] = null;
              }
          }

              if (in_array("hr_note", $actions[0])) {
                $hr_data = array();
                foreach ($companies as $company) {
                    $companyId = $company->id;
                    $companyCode = $company->text;
                    $pending_hr_notes = $this->db->or_where_in("status", array("Pending Payroll Notes", "Pending HR Notes"))
                    ->where("DATE(created_dt) >=", $check)
                    ->where("company", $companyId)
                    ->get("gcceforms.accountability")
                    ->num_rows();
                  
                      if($pending_hr_notes > 0){
                        $hr_data[$companyCode] = array(
                          "company" => $companyId,
                          "accountability_hr_note_count" => $pending_hr_notes > 0 ? number_format(($pending_hr_notes / $all_acct) * 100, 2) : 0,
                          "hr_note" => $pending_hr_notes,
                          "progress_accountability_hr_note" => "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: " . ($pending_hr_notes > 0 ? number_format(($pending_hr_notes / $all_acct) * 100, 2) : 0) . "%'></div>",
                      );
                      }
                }
                if (!empty($hr_data)){
                  $result['hr_note'] = $hr_data;
                }
                  else{$result['hr_note'] = null;
                  }
              }
              if(in_array("acct_release", $actions[0])){
                $acct_release_data = array();
                foreach ($companies as $company) {
                  $companyId = $company->id;
                  $companyCode = $company->text;
                  $acct_release = $this->db->get_where("gcceforms.accountability", array("status"=>"For Releasing","DATE(created_dt) >="=>$check,"company" => $companyId))->num_rows();
                  if($acct_release > 0){
                    $acct_release_data[$companyCode] = array(
                      "company" => $companyId,
                      "accountability_releasing_count" => $acct_release > 0 ? number_format(($acct_release / $all_acct) * 100, 2) : 0,
                      "acct_release" => $acct_release,
                      "progress_accountability_releasing" => "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: ".($acct_release > 0? number_format(($acct_release / $all_acct) * 100, 2) : 0) ."%'></div>",
                    ); 
                  }
                }
                if (!empty($acct_release_data)){
                  $result['acct_release'] = $acct_release_data;
                }
                  else{$result['acct_release'] = null;
                  }
              }
              return $result;
          }

          else
          {
            $result = array();
            if(in_array("acct_note", $actions[0])){
              
                $pending_acct_notes = $this->db->get_where("gcceforms.accountability", array("status"=>"Pending Accounting Notes","DATE(created_dt) >="=>$check,"company"=>$company))->num_rows();
                $acct_data['accountability_acct_note_count'] = $pending_acct_notes > 0 ? number_format(($pending_acct_notes / $all_acct) * 100, 2) : 0;
            }else if(in_array("new", $actions[0])){
                $pending_acct_notes = $this->db->get_where("gcceforms.accountability", array("status"=>"Pending Accounting Notes","DATE(created_dt) >="=>$check, "created_id"=>$userData['emp_id'],"company"=>$company))->num_rows();
                $acct_data['accountability_acct_note_count'] = $pending_acct_notes > 0 ? number_format(($pending_acct_notes / $all_acct_emp) * 100, 2) : 0;
            }else{
                $pending_acct_notes = [];
                $acct_data['accountability_acct_note_count'] = null;
                $acct_data['scroll_acct_note'] = "";
            }
            $acct_data['acct_note'] = array(
              $companyName => array(
                "company" => $companyId,
                  "accountability_acct_note_count" => $acct_data['accountability_acct_note_count'],
                  "acct_note" => $pending_acct_notes,
                  "progress_accountability_acct_note" => "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$acct_data['accountability_acct_note_count']}%'></div>"
              )
          );
          if (!$pending_acct_notes == 0){
            $result['acct_note'] = $acct_data['acct_note'];
          }
          else{
            $result['acct_note'] = null;
          }
            
              
            if(in_array("hr_note", $actions[0])){
                $pending_hr_notes = $this->db->get_where("gcceforms.accountability", array("status"=>"Pending HR Notes","DATE(created_dt) >="=>$check))->num_rows();
                $acct_data['accountability_hr_note_count'] = $pending_hr_notes > 0 ? number_format(($pending_hr_notes / $all_acct) * 100, 2) : 0;
            }else if(in_array("new", $actions[0])){
                $pending_hr_notes = $this->db->get_where("gcceforms.accountability", array("status"=>"Pending HR Notes","DATE(created_dt) >="=>$check,"created_id"=>$userData['emp_id']))->num_rows();
                $acct_data['accountability_hr_note_count'] = $pending_hr_notes > 0 ? number_format(($pending_hr_notes / $all_acct_emp) * 100, 2) : 0;
            }else{
                $pending_hr_notes = [];
                $acct_data['accountability_hr_note_count'] = null;
            }
            $acct_data['hr_note'] = array(
              $companyName => array(
                "company" => $companyId,
                "accountability_hr_note_count" => $acct_data['accountability_hr_note_count'],
                "hr_note" => $pending_hr_notes,
                "progress_accountability_hr_note" => "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$acct_data['accountability_hr_note_count']}%'></div>"
              )
            );
            if (!$pending_hr_notes == 0){
              $result['hr_note'] = $acct_data['hr_note'];
            }
            else{
              $result['hr_note'] = null;
            } 
            

            if(in_array("acct_release", $actions[0])){
                $acct_release = $this->db->get_where("gcceforms.accountability", array("status"=>"For Releasing","DATE(created_dt) >="=>$check))->num_rows();
                $acct_data['accountability_releasing_count'] = $acct_release > 0 ? number_format(($acct_release / $all_acct) * 100, 2) : 0;
            }else if(in_array("new", $actions[0])){
                $acct_release = $this->db->get_where("gcceforms.accountability", array("status"=>"For Releasing","DATE(created_dt) >="=>$check,"created_id"=>$userData['emp_id']))->num_rows();
                $acct_data['accountability_releasing_count'] = $acct_release > 0 ? number_format(($acct_release / $all_acct_emp) * 100, 2) : 0;
            }else{
                $acct_release = [];
                $acct_data['accountability_releasing_count'] = null;
            }
            $acct_data['acct_release'] = array(
              $companyName => array(
                "company" => $companyId,
                "accountability_releasing_count" => $acct_data['accountability_releasing_count'],
                "acct_release" => $acct_release,
                "progress_accountability_releasing" => "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$acct_data['accountability_releasing_count']}%'></div>"
              )
            );
            if (!$acct_release == 0){
              $result['acct_release'] = $acct_data['acct_release'];
            }
            else{
              $result['acct_release'] = null;
            }
            
        }
      }else{
            $acct_data = array();
        }
        // echo "<pre>";
        // var_dump($privAction);
        // echo "</pre>";
        $this->db->trans_complete();
        return $result;

    }

    function getBorrowing(){
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $userData = $this->session->userdata('logged_in');
        
        $this->db->trans_start();
        $privAction = $this->core_layout->generatePrivileges();
        $borr_data = array();
        $actions = array();
        $borr_data['borrowing'] = false;
        foreach($privAction as $key_borr => $privAction_temp){
            if($key_borr == "borr_masterlist"){
                $actions[] = $privAction_temp;
            }
        }
        if(!empty($actions) && (in_array("approve_action", $actions[0]) || in_array("acct_release", $actions[0]))){
            $borr_data['borrowing'] = true;
        }

        $all_borrowing = $this->db->get_where("gcceforms.borrowing")->num_rows();
        $all_asset = $this->db->get_where("gcceforms.borrowing_body")->num_rows();
        if(!empty($actions[0])){
            if(in_array("approve_action", $actions[0])){
                $pending = $this->db->get_where("gcceforms.borrowing", array("status"=>"Pending"))->num_rows();
                $borr_data['borr_approval_count'] = $pending > 0 ? number_format(($pending / $all_borrowing) * 100, 2) : 0;
                $borr_data['approval_priv'] = true;
            }else if(in_array("new", $actions[0]) || in_array("edit", $actions[0])){
                $pending = $this->db->get_where("gcceforms.borrowing", array("status"=>"Pending", "borrower"=>$userData['emp_id'],"DATE(created_dt) >="=>$check))->num_rows();
                $borr_data['borr_approval_count'] = $pending > 0 ? number_format(($pending / $all_borrowing) * 100, 2) : 0;
                $borr_data['approval_priv'] = true;
            }else{
                $pending = 0;
                $borr_data['borr_approval_count'] = 0;
            }
            $borr_data['progress_borr_approval'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$borr_data['borr_approval_count']}%'></div>";
            $borr_data['for_approval'] = $pending;

            if(in_array("acct_release", $actions[0])){
                $for_releasing = $this->db->get_where("gcceforms.borrowing", array("status"=>"Approved"))->num_rows();
                $borr_data['borr_releasing_count'] = $for_releasing > 0 ? number_format(($for_releasing / $all_borrowing) * 100, 2) : 0;
                $borr_data['acct_release_priv'] = true;;
            }else if(in_array("new", $actions[0]) || in_array("edit", $actions[0])){
                $for_releasing = $this->db->get_where("gcceforms.borrowing", array("status"=>"Approved", "borrower"=>$userData['emp_id'],"DATE(created_dt) >="=>$check))->num_rows();
                $borr_data['borr_releasing_count'] = $for_releasing > 0 ? number_format(($for_releasing / $all_borrowing) * 100, 2) : 0;
                $borr_data['acct_release_priv'] = true;;
            }else{
                $for_releasing = 0;
                $borr_data['borr_releasing_count'] = 0;
            }
            $borr_data['progress_borr_releasing'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$borr_data['borr_releasing_count']}%'></div>";
            $borr_data['acct_release'] = $for_releasing;

            ///////////////////////////////////////////////////////////////////////////////////////
            if(in_array("acct_return", $actions[0])){
              $for_return = $this->db->get_where("gcceforms.borrowing_body", array("is_returned"=>0))->num_rows();
              $borr_data['borr_return_count'] = $for_return > 0 ? number_format(($for_return / $all_asset) * 100, 2) : 0;
              $borr_data['acct_return_priv'] = true;;
          }else{
            $for_return = 0;
              $borr_data['borr_return_count'] = 0;
          }
            
            $borr_data['progress_borr_return'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$borr_data['borr_return_count']}%'></div>";
            $borr_data['acct_return'] = $for_return;
        }else{
            $borr_data = array();
        }

        $this->db->trans_complete();
        return $borr_data;
    }

    function getOvertime(){
        $userData = $this->session->userdata('logged_in');
        $this->db->trans_start();
        $privAction = $this->core_layout->generatePrivileges();
        $ot_data = array();
        $actions = array();
        $ot_data['overtime'] = false;
        foreach($privAction as $key_ot => $privAction_temp){
            if($key_ot == "overtime_masterfile"){
                $actions[] = $privAction_temp;
            }
        }
        
        $ot_data['ot_approve_priv'] = false;
        $all_ot = $this->db->get_where("gcceforms.overtime")->num_rows();
        $all_emp_ot = $this->db->get_where("gcceforms.overtime", array("created_by"=>$userData['emp_id']))->num_rows();
        if(!empty($actions[0])){
            $ot_data['overtime'] = true;
            if(in_array("approve_action", $actions[0])){
                $pending = $this->db->get_where("gcceforms.overtime", array("status"=>"Pending"))->num_rows();
                $ot_data['pending_count'] = $pending > 0 ? number_format(($pending / $all_ot) * 100, 2) : 0;
                $ot_data['ot_approve_priv'] = true;
            }else if(in_array("new", $actions[0])){
                $pending = $this->db->get_where("gcceforms.overtime", array("created_by"=>$userData['emp_id'],"status"=>"Pending"))->num_rows();
                $ot_data['pending_count'] = $pending > 0 ? number_format(($pending / $all_emp_ot) * 100, 2) : 0;
                $ot_data['ot_approve_priv'] = true;
            }else{
                $pending = 0;
            }
            $ot_data['progress_ot'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$ot_data['pending_count']}%'></div>";
            $ot_data['for_approval'] = $pending;

        }else{
            $ot_data['overtime'] = false;
            $ot_data = array();
        }
        $this->db->trans_complete();
        return $ot_data;
    }

    function getTransmittal(){
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $userData = $this->session->userdata('logged_in');

        $this->db->trans_start();
        $privAction = $this->core_layout->generatePrivileges();
        $transmittal_data = array();
        $actions = array();
        $transmittal_data['transmittal'] = false;
        foreach($privAction as $key_trans => $privAction_temp){
            if($key_trans == "tr_masterfile"){
                $actions[] = $privAction_temp;
            }
        }

        $transmittal_data['tr_approval_priv'] = false;
        $transmittal_data['tr_receive_priv'] = false;
        $all_tr = $this->db->get_where("gcceforms.transmittal", array("ship_date >="=>$check))->num_rows();
        $all_emp_tr = $this->db->get_where("gcceforms.transmittal", array("ship_date >="=>$check, "created_by"=>$userData['emp_id']))->num_rows();

        if(!empty($actions[0])){
            // Pending Approval
            if(in_array("approve_action",$actions[0])){
                $pending = $this->db->get_where("gcceforms.transmittal", array("status"=>"Pending", "ship_date >="=>$check))->num_rows();
                $pending_count = $pending > 0 ? number_format(($pending / $all_tr) * 100, 2) : 0;
                $transmittal_data['tr_approval_priv'] = true;
            }else if(in_array("new", $actions[0])){
                $pending = $this->db->get_where("gcceforms.transmittal", array("status"=>"Pending", "created_by"=>$userData['emp_id'], "ship_date >="=>$check))->num_rows();
                $pending_count = $pending > 0 ? number_format(($pending / $all_emp_tr) * 100, 2) : 0;
                $transmittal_data['tr_approval_priv'] = true;
            }else{
                $transmittal_data['progress_tr'] = 0;
                $pending = 0;
            }
            $transmittal_data['for_approval'] = $pending;
            $transmittal_data['progress_tr'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$pending_count}%'></div>";
            $transmittal_data['pending_count'] = $pending_count;

            // Approved, for releasing
            if(in_array("receive", $actions[0])){
                $receive = $this->db->get_where("gcceforms.transmittal", array("status"=>"Approved", "ship_date >="=>$check))->num_rows();
                $receive_count = $receive > 0 ? number_format(($receive / $all_tr) * 100, 2) : 0;
                $transmittal_data['progress_tr_rec'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$receive_count}%'></div>";
                // $transmittal_data['progress_tr_rec'] = number_format(($receive / $all_tr) * 100, 2);
                $transmittal_data['tr_receive_priv'] = true;
                $transmittal_data['receive_count'] = $receive_count;
            }else{
                $receive = 0;
                $transmittal_data['receive_count'] = 0;
            }
            $transmittal_data['receive'] = $receive;

        }else{
            $transmittal_data = [];
        }
        $this->db->trans_complete();
        return $transmittal_data;
    }

    function getShipping(){
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $userData = $this->session->userdata('logged_in');

        $this->db->trans_start();
        $privUrl = $this->core_layout->generatePrivilegesUrl();
        $privAction = $this->core_layout->generatePrivileges();
        $sa_data = array();
        $actions = array();
        $sa_data['shipping_advice'] = false;
        foreach($privAction as $key_sa => $privAction_temp){
            if($key_sa == "ship_masterfile"){
                $actions[] = $privAction_temp;
            }
        }
        $sa_data['sa_approval_priv'] = false;
        $sa_data['sa_receive_priv'] = false;

        if(!empty($actions) && (in_array("approve_action", $actions[0]) || in_array("receive", $actions[0]))){
            $sa_data['shipping_advice`'] = true;
        }

        $all_sa = $this->db->get_where("gcceforms.shipping", array("ship_date >="=>$check))->num_rows();
        $all_sa_emp = $this->db->get_where("gcceforms.shipping", array("ship_date >="=>$check,"created_by"=>$userData['id']))->num_rows();
        if(!empty($actions[0])){
            if(in_array("approve_action", $actions[0])){
                $pending = $this->db->get_where("gcceforms.shipping", array("status"=>"Pending","ship_date >="=>$check))->num_rows();
                $sa_data['pending_count'] = $pending > 0 ? number_format(($pending / $all_sa) * 100, 2) : 0;
            }else if(in_array("new", $actions[0])){
                $pending = $this->db->get_where("gcceforms.shipping", array("status"=>"Pending","created_by"=>$userData['id'], "ship_date >="=>$check))->num_rows();
                $sa_data['pending_count'] = $pending > 0 ? number_format(($pending / $all_sa_emp) * 100, 2) : 0;
            }else{
                $pending = 0;
                $pending_count = 0;
                $sa_data['pending_count'] = 0;
                $sa_data['scroll_for_approval'] = "";
            }
            
            $sa_data['for_approval'] = $pending;
            $sa_data['progress_sa_pending'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$sa_data['pending_count']}%'></div>";

            if(in_array("receive", $actions[0])){
                $receive = $this->db->get_where("gcceforms.shipping", array("status"=>"Approved","ship_date >="=>$check))->num_rows();
                $sa_data['receive_count'] = $receive > 0 ? number_format(($receive / $all_sa) * 100, 2) : 0;
                $sa_data['sa_receive_priv'] = true;
            }else{
                $receive = 0;
                $sa_data['receive_count'] = 0;
                $sa_data['scroll_receive'] = "";
            }
            $sa_data['receive'] = $receive;
            $sa_data['progress_sa_receive'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$sa_data['receive_count']}%'></div>";

        }else{
            $sa_data = array();
        }

        $this->db->trans_complete();
        return $sa_data;
    }

    function getCashadvance(){
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $userData = $this->session->userdata('logged_in');

        $this->db->trans_start();

        $this->db->select("id");
        $this->db->from("gcchris.tbldepartments");
        $this->db->where("head_id", $userData['emp_id']);
        $this->db->where("is_archived", 0);
        $dept_query = $this->db->get();
        $cash_advance = array();
        $all_ca_under_head = 0;
        if(count($dept_query->result()) > 0){
            foreach($dept_query->result() as $dept_data){
                $this->db->select("count(ca.id) as count");
                $this->db->from("gcceforms.cash_advance ca");
                $this->db->join("gccmaster.tblemployees emp", "emp.id=ca.employee", "LEFT");
                $this->db->join("gcchris.tbldepartments dept", "dept.id=emp.department_id", "LEFT");
                $this->db->where("emp.department_id", $dept_data->id);
                $this->db->where("ca.status", "HR Recommendation Pending");
                $this->db->where("ca.created_dt >=", $check);
                $empData = $this->db->get();
                $result = $empData->row();
                $arrData['count'] = $result->count;
                $all_ca_under_head += $result->count;
               
            }
            $cash_advance['cash_advance_count'] = $all_ca_under_head;
            $cash_advance['head_data'] = true;
        }else{
            $cash_advance['cash_advance_count'] = $this->db->get_where("gcceforms.cash_advance", array("status"=>"HR Recommendation Pending", "employee"=>$userData['emp_id'],"created_dt >="=>$check))->num_rows();
            $cash_advance['head_data'] = true;
        }
        

        $cash_advance['recommendation'] = $all_ca_under_head;
        
        $privAction = $this->core_layout->generatePrivileges();
        
        $actions = array();
        
        $cash_advance['cash_advance'] = false;
        foreach($privAction as $key_to => $privAction_temp){
            if($key_to == "ca_masterfile"){
                $actions[] = $privAction_temp;
            }
        }
        
        $cash_advance['ca_recommendation_priv'] = false;
        $cash_advance['ca_hr_note_priv'] = false;
        $cash_advance['ca_acct_note_priv'] = false;
        $cash_advance['awaiting_approval_priv'] = false;

        if(!empty($actions)){
            $cash_advance['cash_advance'] = true;
        }else{
            $cash_advance['cash_advance'] = false;
        }
        
        $all_ca = $this->db->get_where("gcceforms.cash_advance", array("created_dt >="=>$check))->num_rows();

        $cash_advance['scroll_width_recommend'] = $all_ca_under_head > 0 ? number_format(($all_ca_under_head / $all_ca) * 100, 2) : 0;

        
        // if(in_array("recommendation", $actions[0])){
        //     $cash_advance['ca_recommendation_priv'] = true;
        // }elseif($cash_advance['cash_advance_count'] > 0){
        //     $cash_advance['cash_advance'] = true;
        //     $cash_advance['ca_recommendation_priv'] = true;
        // }else{
        //     $cash_advance['ca_recommendation_priv'] = false;
        // }
        $all_ca_emp = $this->db->get_where("gcceforms.cash_advance", array("created_dt >="=>$check))->num_rows();
        if(in_array("recommendation", $actions[0])){
            $for_approval = $this->db->get_where("gcceforms.cash_advance", array("status"=>"HR Recommendation Pending","created_dt >="=>$check))->num_rows();
            $cash_advance['hr_note'] = $for_approval;
            $cash_advance['ca_approval_count'] = $for_approval > 0 ? number_format(($for_approval / $all_ca) * 100, 2) : 0;
            $cash_advance['ca_hr_note_priv'] = true;
        }else if(in_array("new", $actions[0])){
            $for_approval = $this->db->get_where("gcceforms.cash_advance", array("status"=>"HR Recommendation Pending","created_dt >="=>$check, "employee"=>$userData['emp_id']))->num_rows();
            $cash_advance['hr_note'] = $for_approval;
            $cash_advance['ca_approval_count'] = $for_approval > 0 ? number_format(($for_approval / $all_ca_emp) * 100, 2) : 0;
            $cash_advance['ca_hr_note_priv'] = true;
        }else{
            $cash_advance['hr_note'] = [];
            $cash_advance['ca_approval_count'] = 0;
            $cash_advance['for_approval'] = false;
        }
        $cash_advance['progress_ca_approval'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$cash_advance['ca_approval_count']}%'></div>";

        if(in_array("hr_note", $actions[0])){
            $hr_note = $this->db->get_where("gcceforms.cash_advance", array("status"=>"Payroll Balance Pending","created_dt >="=>$check))->num_rows();
            $cash_advance['hr_note'] = $hr_note;
            $cash_advance['ca_hr_note_count'] = $hr_note > 0 ? number_format(($hr_note / $all_ca) * 100, 2) : 0;
            $cash_advance['ca_hr_note_priv'] = true;
        }else if(in_array("new", $actions[0])){
            $hr_note = $this->db->get_where("gcceforms.cash_advance", array("status"=>"Payroll Balance Pending","created_dt >="=>$check, "employee"=>$userData['emp_id']))->num_rows();
            $cash_advance['hr_note'] = $hr_note;
            $cash_advance['ca_hr_note_count'] = $hr_note > 0 ? number_format(($hr_note / $all_ca_emp) * 100, 2) : 0;
            $cash_advance['ca_hr_note_priv'] = true;
        }else{
            $cash_advance['hr_note'] = [];
            $cash_advance['ca_hr_note_count'] = 0;
            $cash_advance['for_approval'] = false;
        }
        $cash_advance['progress_ca_hr_note'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$cash_advance['ca_hr_note_count']}%'></div>";

        if(in_array("acct_note", $actions[0])){
            $for_accomplishment = $this->db->get_where("gcceforms.cash_advance", array("status"=>"Accounting Balance Pending","created_dt >="=>$check))->num_rows();
            $cash_advance['acct_note'] = $for_accomplishment;
            $cash_advance['ca_acct_note_count'] = $for_accomplishment > 0 ? number_format(($for_accomplishment / $all_ca) * 100, 2) : 0;
            $cash_advance['ca_acct_note_priv'] = true;
        }else if(in_array("new", $actions[0])){
            $for_accomplishment = $this->db->get_where("gcceforms.cash_advance", array("status"=>"Accounting Balance Pending","created_dt >="=>$check, "employee"=>$userData['emp_id']))->num_rows();
            $cash_advance['acct_note'] = $for_accomplishment;
            $cash_advance['ca_acct_note_count'] = $for_accomplishment > 0 ? number_format(($for_accomplishment / $all_ca_emp) * 100, 2) : 0;
            $cash_advance['ca_acct_note_priv'] = true;
        }else{
            $cash_advance['acct_note'] = [];
            $cash_advance['ca_acct_note_count'] = 0;
            $cash_advance['for_accomplishment'] = false;
        }
        $cash_advance['progress_ca_acct_note'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$cash_advance['ca_acct_note_count']}%'></div>";

        if(in_array("approve_action", $actions[0]) OR ($cash_advance['cash_advance_count'] > 0)){
            $awating_approval = $this->db->get_where("gcceforms.cash_advance", array("status"=>"Awaiting Approval","created_dt >="=>$check))->num_rows();
            $cash_advance['awaiting_approval'] = $awating_approval;
            $cash_advance['ca_awaiting_approval_count'] = $awating_approval > 0 ? number_format(($awating_approval / $all_ca) * 100, 2) : 0;
            $cash_advance['awaiting_approval_priv'] = true;
            $cash_advance['cash_advance'] = true;
        }else if(in_array("new", $actions[0])){
            $awating_approval = $this->db->get_where("gcceforms.cash_advance", array("status"=>"Awaiting Approval","created_dt >="=>$check, "employee"=>$userData['emp_id']))->num_rows();
            $cash_advance['awaiting_approval'] = $awating_approval;
            $cash_advance['ca_awaiting_approval_count'] = $awating_approval > 0 ? number_format(($awating_approval / $all_ca_emp) * 100, 2) : 0;
            $cash_advance['awaiting_approval_priv'] = true;
            $cash_advance['cash_advance'] = true;
        }else{
            $cash_advance['awaiting_approval'] = [];
            $cash_advance['for_accomplishment'] = false;
            $cash_advance['cash_advance'] = false;
        }
        $cash_advance['progress_ca_awaiting_approval'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$cash_advance['ca_awaiting_approval_count']}%'></div>";

        // for posting status
        if(in_array('posting_action', $actions[0]) || ($cash_advance['cash_advance_count'] > 0)){
            $for_posting = $this->db->get_where("gcceforms.cash_advance", array("status"=>"For Posting","created_dt >="=>$check))->num_rows();
            $cash_advance['for_posting'] = $for_posting;
            $cash_advance['ca_for_posting_count'] = $for_posting > 0 ? number_format(($for_posting / $all_ca) * 100, 2) : 0;
            $cash_advance['for_posting_priv'] = true;
            $cash_advance['cash_advance'] = true;
        }elseif(in_array("new", $actions[0])){
            $for_posting = $this->db->get_where("gcceforms.cash_advance", array("status"=>"For Posting","created_dt >="=>$check, "employee"=>$userData['emp_id']))->num_rows();
            $cash_advance['for_posting'] = $for_posting;
            $cash_advance['ca_for_posting_count'] = $for_posting > 0 ? number_format(($for_posting / $all_ca_emp) * 100, 2) : 0;
            $cash_advance['for_posting_priv'] = true;
            $cash_advance['cash_advance'] = true;
        }else{
            $cash_advance['for_posting'] = [];
            $cash_advance['for_accomplishment'] = false;
            $cash_advance['cash_advance'] = false;
        }
        $cash_advance['progress_ca_for_posting'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$cash_advance['ca_for_posting_count']}%'></div>";
        // for posting status

        // posted status
        if(in_array('posted_action', $actions[0]) || ($cash_advance['cash_advance_count'] > 0)){
            $posted = $this->db->get_where("gcceforms.cash_advance", array("status"=>"Posted","created_dt >="=>$check))->num_rows();
            $cash_advance['posted'] = $posted;
            $cash_advance['ca_posted_count'] = $posted > 0 ? number_format(($posted / $all_ca) * 100, 2) : 0;
            $cash_advance['posted_priv'] = true;
            $cash_advance['cash_advance'] = true;
        }elseif(in_array("new", $actions[0])){
            $posted = $this->db->get_where("gcceforms.cash_advance", array("status"=>"Posted","created_dt >="=>$check, "employee"=>$userData['emp_id']))->num_rows();
            $cash_advance['posted'] = $posted;
            $cash_advance['ca_posted_count'] = $posted > 0 ? number_format(($posted / $all_ca_emp) * 100, 2) : 0;
            $cash_advance['posted_priv'] = true;
            $cash_advance['cash_advance'] = true;
        }else{
            $cash_advance['posted'] = [];
            $cash_advance['for_accomplishment'] = false;
            $cash_advance['cash_advance'] = false;
        }
        $cash_advance['progress_ca_posted'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$cash_advance['ca_posted_count']}%'></div>";
        // posted status

        // for final approval
        if(in_array('final_approval_action', $actions[0]) || ($cash_advance['cash_advance_count'] > 0)){
            $final = $this->db->get_where("gcceforms.cash_advance", array("status"=>"For Final Approval","created_dt >="=>$check))->num_rows();
            $cash_advance['final_approval'] = $final;
            $cash_advance['ca_final_approval_count'] = $final > 0 ? number_format(($final / $all_ca) * 100, 2) : 0;
            $cash_advance['final_approval_priv'] = true;
            $cash_advance['cash_advance'] = true;
        }elseif(in_array("new", $actions[0])){
            $final = $this->db->get_where("gcceforms.cash_advance", array("status"=>"For Final Approval","created_dt >="=>$check, "employee"=>$userData['emp_id']))->num_rows();
            $cash_advance['final_approval'] = $final;
            $cash_advance['ca_final_approval_count'] = $final > 0 ? number_format(($final / $all_ca_emp) * 100, 2) : 0;
            $cash_advance['final_approval_priv'] = true;
            $cash_advance['cash_advance'] = true;
        }else{
            $cash_advance['final_approval'] = [];
            $cash_advance['for_accomplishment'] = false;
            $cash_advance['cash_advance'] = false;
        }
        $cash_advance['progress_ca_final_approval'] = "<div class='progress-bar m--bg-success' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: {$cash_advance['ca_final_approval_count']}%'></div>";
        // for final approval
        
        $cash_advance['privileges'] = $actions;
        $this->db->trans_complete();

        return $cash_advance;
    }

    public function getPayslip() {
        $userData = $this->session->userdata('logged_in');
        $arrData  = array();
        $id       = $userData['emp_id'];
    
        if ($id) {
            $this->db->select("id, pay_date, date_start, date_end, gross_pay, net_pay, bonus_code, is_bonus");
            $this->db->where("emp_id", $id);
            $this->db->where("posted", 1);
            $this->db->order_by("pay_date", "DESC");
            $this->db->limit(1); 
            $query   = $this->db->get("payroll.payroll_sheet");
    
            $arrData = $query->row_array(); // single row
        }
    
        return $arrData;
    }
    
    public function getDeductions() {
        $userData = $this->session->userdata('logged_in');
        $arrData  = array();
        $id       = $userData['emp_id'];

        if ($id) {
            $ps_id = $this->db->select('id')->order_by('pay_date', 'desc')->limit(1)->get_where('payroll.payroll_sheet', array('emp_id' => $id, 'posted' => 1))->row();

            $sqlSelect = "ps.*, DATE_FORMAT(ps.date_start, '%m/%d/%y') as date_start, DATE_FORMAT(ps.date_end, '%m/%d/%y') as date_end, emp.payroll_type, emp.basic_rate,
            IFNULL(comp.description, emp.company_id) as company_description, IFNULL(dept.description, emp.department_id) as department_description,
            IFNULL(pos.name, emp.position) as position_description, GROUP_CONCAT(DISTINCT(loan_payments.id)) as loan_payments,
            GROUP_CONCAT(DISTINCT(CONCAT(custom_adjustments.particulars,'||',custom_adjustments.amount, '||', custom_adjustments.cadj_type))) custom_adjustments,
            GROUP_CONCAT(DISTINCT(CONCAT(created_adjustments.particulars,'||',created_adjustments.amount, '||', created_adjustments.adj_type, '||', created_adjustments.status))) created_adjustments,
            IFNULL(psa.allowance_total, 0) as psa_total, IFNULL(psa.undertime_deduction, 0) as psa_deduction, ps.basic_rate as basic_pay, ps.total_holiday_amount, ps.total_holiday_minutes";

            $this->db->select($sqlSelect);
            $this->db->join("gccmaster.tblemployees as emp", "emp.id = ps.emp_id");
            $this->db->join("gcchris.tblcompanies as comp", "comp.id = emp.company_id OR comp.code = emp.company_id", "LEFT");
            $this->db->join("gcchris.tbldepartments as dept", "dept.id = emp.department_id OR dept.description = emp.department_id", "LEFT");
            $this->db->join("gcchris.tblposition as pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
            $this->db->join("payroll.payroll_sheet_custom_adjustments custom_adjustments", "custom_adjustments.payroll_sheet_id = ps.id", "LEFT");
            $this->db->join("payroll.payroll_sheet_created_adjustments created_adjustments", "created_adjustments.payroll_sheet_id = ps.id AND created_adjustments.status = 1", "LEFT");
            $this->db->join("payroll.payroll_sheet_loan_payments loan_payments", "loan_payments.payroll_sheet_id = ps.id", "LEFT");
            $this->db->join("payroll.payroll_sheet_allowances psa", "psa.payroll_sheet_id = ps.id", "LEFT");
            $qTemp = $this->db->get_where("payroll.payroll_sheet as ps", array("ps.id"=>$ps_id->id, "ps.posted"=>1));

            if($qTemp->num_rows() == 1){
                $tempDeductionIndexes = array("sss", "sss_prov", "hdmf", "ph", "tax", "total_loans");

                $tempRow = $qTemp->row();
                $payrollType = $tempRow->payroll_type;
                $basicRate = $tempRow->basic_rate;

                $tempData = $this->core_layout->getEmployeeData($tempRow->emp_id);
                $tempData = (object) $tempData;
                $displayName = (isset($tempData->display_name_0) && $tempData->display_name_0)? $tempData->display_name_0: "No assigned name";

                $tempRow->employee_name = $displayName;
                $tempRow->biometricno = isset($tempData->biometricno) && $tempData->biometricno ? $tempData->biometricno: "---";
                $tempRow->idno = isset($tempData->idno) && $tempData->idno ? $tempData->idno: "---";
                $_target_hours_worked = ($tempRow->total_minutes_worked + $tempRow->total_unrendered_minutes) / 60;
                $tempRow->target_hours = $_target_hours_worked;
                $tempRow->target_hours = round($tempRow->target_hours, 2);
                $tempRow->hours_worked = $tempRow->total_minutes_worked / 60;
                $tempRow->hours_worked = round($tempRow->hours_worked, 2);
                $tempEwd = $_target_hours_worked / 8;
                $tempRow->ewd = $tempEwd;
                $tempRow->ewd_decimal = $tempEwd;

                $tempRow->target_payrate = $tempRow->daily * $tempRow->ewd;
                if(strtolower($tempRow->payroll_type) == "monthly"){
                    $tempRow->target_payrate = (intval($tempRow->payroll_sched) == 2)? $tempRow->basic_rate / 2: $tempRow->basic_rate;
                }

                if(intval($tempRow->is_bonus) == 1){
                    $tempRow->target_payrate = $tempRow->basic_pay;
                    $tempRow->ewd = $tempRow->no_of_days;
                    $tempRow->ewd_decimal = $tempRow->no_of_days;
                }

                $tempRow->ewd = round($tempRow->ewd, 2);
                $tempRow->ewd_decimal = round($tempRow->ewd_decimal, 2);

                $tempRow->target_payrate = number_format($tempRow->target_payrate, 2, ".", ",");
                $absent_hours = ($tempRow->total_unrendered_minutes - $tempRow->total_undertime_minutes) / 60;
                $tempRow->absent_hours = number_format($absent_hours, 2, ".", ",");
                $undertime_hours = $tempRow->total_undertime_minutes / 60;
                $tempRow->undertime_hours = number_format($undertime_hours, 2, ".", ",");
                $tempRow->total_unrendered_amount = $tempRow->total_unrendered_amount;
                $tempRow->total_unrendered_amount = number_format($tempRow->total_unrendered_amount, 2, ".", ",");

                /*** added holiday pay */
                $tempRow->total_holiday_amount = number_format($tempRow->total_holiday_amount, 2, '.', ',');
                $holiday_hours = $tempRow->total_holiday_minutes / 60;
                $tempRow->holiday_hours = number_format($holiday_hours, 2, ".", ",");
                /*** added holiday pay */

                $overtime_ndiff_hours = $tempRow->ot_ndiff_minutes / 60;
                $tempRow->ot_ndiff_hours = floatval($overtime_ndiff_hours) > 0 ?
                    is_float($overtime_ndiff_hours)? number_format($overtime_ndiff_hours, 2, ".", ","): $overtime_ndiff_hours
                    : $overtime_ndiff_hours;  

                $overtime_hours = $tempRow->ot_minutes / 60;

                $tempRow->total_ot_hours = floatval($overtime_hours) > 0 ?
                    is_float($overtime_hours)? number_format($overtime_hours, 2, ".", ","): $overtime_hours + $tempRow->ot_ndiff_hours
                : $overtime_hours + $tempRow->ot_ndiff_hours;

                $tempRow->ot_hours = is_float($overtime_hours)? number_format($overtime_hours, 2, ".", ","): $overtime_hours;

                $tempRow->loans = array();
                $tempRow->adjustment_earnings = array();
                $tempRow->adjustment_deductions = array();
                $tempRow->loan_count = 0;
                $tempRow->adjustment_e_count = 0;
                $tempRow->adjustment_d_count = 0;
                $total_adjustment_deductions = 0;

                /*** end computation ***/
                if($tempRow->created_adjustments){
                    $tempCreatedAdjustments = explode(",", $tempRow->created_adjustments);
                    if(is_array($tempCreatedAdjustments) && count($tempCreatedAdjustments) > 0){
                        foreach ($tempCreatedAdjustments as $kk => $vv) {
                            $tempItem = explode("||", $vv);
                            if(is_array($tempItem) && count($tempItem) == 4){
                                $tempKey = strtolower($tempItem[0]);
                                /* update to detect phic adjustment as ph */
                                if($tempKey == 'phic'){ $tempKey = 'ph'; }
                                /* end of update */
                                $tempValue = $tempItem[1];
                                $tempType = $tempItem[2];
                                $tempKey = $tempKey == "loan" ? "total_loans": $tempKey;

                                if(isset($tempRow->{$tempKey}) && floatval($tempRow->{$tempKey}) > 0){
                                    if(intval($tempType) == 1){
                                        $tempRow->{$tempKey} = $tempRow->{$tempKey} + $tempValue;
                                    }else{
                                        $tempRow->{$tempKey} = $tempRow->{$tempKey} - $tempValue;
                                    }
                                }else{
                                    $tempRow->{$tempKey} = $tempValue;
                                    if(!in_array($tempKey, $tempDeductionIndexes)){ $tempDeductionIndexes[] = $tempKey; }
                                }
                            }
                        }
                    }
                }

                if($tempRow->custom_adjustments){
                    $tempCustomAdjustments = explode(",", $tempRow->custom_adjustments);
                    if(is_array($tempCustomAdjustments) && count($tempCustomAdjustments) > 0){
                        foreach ($tempCustomAdjustments as $kk => $vv) {
                            $tempItem = explode("||", $vv);
                            if(is_array($tempItem) && count($tempItem) == 3){
                                $tempAdjustment = new stdClass();
                                $tempKey = strtolower($tempItem[0]);
                                $tempValue = $tempItem[1];
                                $tempType = $tempItem[2];
                                $tempAdjustment->label = $tempKey;
                                $tempAdjustment->value = $tempValue;
                                $tempAdjustment->display_value = $tempValue;
                                $tempAdjustment->adj_type = $tempType;

                                if(intval($tempType) == 1){
                                    $tempRow->adjustment_earnings[] = $tempAdjustment;
                                }else{
                                    $tempRow->adjustment_deductions[] = $tempAdjustment;

                                    $total_adjustment_deductions += $tempValue;
                                }
                            }
                        }
                    }
                }

                $deductInternalLoans = 0;

                if($tempRow->loan_payments){
                    $tempLoanPayments = explode(",", $tempRow->loan_payments);
                    if(is_array($tempLoanPayments) && count($tempLoanPayments) > 0){
                        $this->db->select("c.loan_name, ROUND(SUM(a.amount_due), 2) as amount_due, c.loan_type");
                        $this->db->join("gcchris.loans b", "b.id = a.loan_id AND (b.active != 2 OR b.paid = 0)");
                        $this->db->join("payroll.loans c", "c.id = b.loan_id");
                        $this->db->where_in('a.id', $tempLoanPayments);
                        $this->db->group_by('c.loan_name');
                        $qTempLoans = $this->db->get("payroll.payroll_sheet_loan_payments a");

                        if($qTempLoans->num_rows() > 0){
                            foreach($qTempLoans->result() as $loanRow){
                                $tempLoan = floatval($loanRow->amount_due);
                                $loanRow->loan_name = strtoupper($loanRow->loan_name);
                                if($tempLoan > 0 && $loanRow->loan_type == "0"){ $deductInternalLoans += $tempLoan; }

                                $loanRow->loan_name = strtoupper($loanRow->loan_name);
                                $loanRow->amount_due = $loanRow->amount_due;
                                $tempRow->loans[] = $loanRow;
                            }
                        }
                    }
                }

                $tempRow->adjustment_e_count = count($tempRow->adjustment_earnings);
                $tempRow->adjustment_d_count = count($tempRow->adjustment_deductions);
                $tempRow->loan_count = count($tempRow->loans);
                $tempRow->deductions = 0;
                $tempDeductionValue = 0;
                $_tempLoanValue = 0;
                $total_deductions = 0;

                if(isset($tempDeductionIndexes) && count($tempDeductionIndexes) > 0){
                    foreach ($tempDeductionIndexes as $key => $item) {
                        $tempValue = floatval($tempRow->{$item});
                        if($tempValue > 0){
                            $tempRow->{$item} = number_format($tempRow->{$item}, 2, ".", ",");
                            if($item == 'total_loans'){
                                $_tempLoanValue += $tempValue;
                            }else{
                                $tempDeductionValue += $tempValue;
                                $total_deductions += $tempValue;
                            }
                        }

                        /*** for external loans start ***/
                        $tempItem = "{$item}_loan";
                        if(isset($tempRow->{$tempItem}) && floatval($tempRow->{$tempItem}) !== 0){
                            $tempLoanValue = floatval($tempRow->{$tempItem});
                            $_tempLoanValue += $tempLoanValue;
                        }
                        /*** for external loans end ***/
                    }
                }

                if(is_array($tempRow->adjustment_deductions) && count($tempRow->adjustment_deductions) > 0){
                    foreach($tempRow->adjustment_deductions as $kk => $vv) {
                        if(intval($vv->adj_type) == 0){
                            $tempDeductionValue += floatval($vv->value);
                        }
                    }
                }

                if($deductInternalLoans > 0){
                    $tempLoansx = str_replace(",", "", $tempRow->total_loans);
                    $tempLoansx = floatval($tempLoansx);
                    if($tempLoansx >= $deductInternalLoans){
                        $tempTotalLoanx = $tempLoansx - $deductInternalLoans;
                        $tempRow->total_loans = number_format($tempTotalLoanx, 2, ".", ",");
                    }
                }

                $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                $tempNet = number_format($tempRow->net_pay, 2, ".", "");
                $tempRow->temp_net = $tempNet;
                $tempRow->net_to_text = $f->format($tempNet);

                $xnet = explode(".", $tempNet);
                if(is_array($xnet) && count($xnet) > 0){
                    $tempCents = "";
                    $tempDecimal = end($xnet);
                    if(intval($tempDecimal) > 1){ $tempCents = " centavos"; }
                    else if(intval($tempDecimal) == 1){ $tempCents = " centavo"; }

                    $tempNetx = 0;
                    $tempLabel = array();

                    foreach ($xnet as $key => $value) {
                        if(floatval($value) > 0){
                            $tempLabel[] = $f->format($value);
                            if($tempDecimal !== $value){
                                $tempNetx += intval($value);
                            }
                        }
                    }
                    if(is_array($tempLabel) && count($tempLabel) > 0){
                        $tempPesoSign = "";
                        if(intval($tempNetx) > 1){ $tempPesoSign = " pesos"; }
                        else if(intval($tempNetx) == 1){ $tempPesoSign = " peso"; }
                        $tempEndValue = end($tempLabel);
                        foreach ($tempLabel as $kk => $vv) {
                            if($vv !== $tempEndValue){
                                $tempLabel[$kk] = $vv.$tempPesoSign;
                            }
                        }
                    }
                    $tempRow->net_xx = $tempNetx;
                    $tempRow->net_to_text = implode(' and ', $tempLabel);
                    if(count($tempLabel) > 1){ $tempRow->net_to_text .= $tempCents; }
                }
                $tempRow->net_to_text = strtoupper($tempRow->net_to_text);

                $tempRow->deductions = number_format($tempDeductionValue, 2, ".", ",");
                $tempRow->basic_rate = number_format($tempRow->basic_rate, 2, ".", ",");
                $tempRow->gross_pay = number_format($tempRow->gross_pay, 2, ".", ",");
                $tempRow->net_pay = number_format($tempRow->net_pay, 2, ".", ",");
                $tempRow->totalLoan = number_format($_tempLoanValue, 2, ".", ",");

                $overall = floatval($tempDeductionValue) + floatval($_tempLoanValue) + floatval($tempRow->total_loans_interest);
                $tempRow->overall_total_deductions = $overall;
                $arrData["data"] = $tempRow;
            }
        }

        $privAction = $this->core_layout->generatePrivileges();
        
        $actions = array();
        
        foreach($privAction as $key_to => $privAction_temp){
            if($key_to == "core_profile_employee_data"){
                $actions[] = $privAction_temp;
            }
        }

        return (in_array("view_own_deductions", $actions[0])) ? $arrData : array();
    }
}