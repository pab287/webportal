<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Email_Configuration extends CI_Model{

	function __construct(){
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
		$this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model","dt_model");
        $this->load->model("core/upload_model", "file_upload");
    }

    private function getUserData(){
        return $this->core_layout->getUserLoggedIn();
    }

    function emailProtocolDatatableRequest(){
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy = (isset($post["sort"]) && $post["sort"])? $post["sort"]: null;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: "desc";

        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->get_all_protocol_post($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_protocol_post_count();
        }

        if($search){
            $rowData = $this->get_searched_protocol_item($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_protocol_item_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function emailTemplateList(){
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy = (isset($post["sort"]) && $post["sort"])? $post["sort"]: null;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: "desc";

        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->get_all_post($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_post_count();
        }

        if($search){
            $rowData = $this->get_searched_item($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_item_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_protocol_post($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        $this->db->select("*");
        $this->db->from("gccmaster.email_protocol_settings");
        $this->db->limit($limit, $offset);

        if($sortBy){
            $this->db->order_by($sortBy, $sortOrder);
        }else{
            $this->db->order_by("id", "DESC");
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            return $query->result();
        }else{
            return array();
        }
    }

    private function get_all_protocol_post_count(){
        $this->db->from("gccmaster.email_protocol_settings");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_protocol_item($search=null, $limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        if($search){
            $filterFields = array("server_name", "unique_code", "protocol", "smtp_host", "smtp_user", "smtp_port", "smtp_crypto");
            $this->db->select("*");
            $this->db->from("gccmaster.email_protocol_settings");
            $this->db->limit($limit, $offset);
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();
            if($sortBy){
                $this->db->order_by($sortBy, $sortOrder);
            }else{
                $this->db->order_by("id", "DESC");
            }
            $query = $this->db->get();

            if($query->num_rows() > 0){
                return $query->result();
            }else{
                return array();
            }
        }else{
            return array();
        }
    }

    private function get_searched_protocol_item_count($search=null){
        $rowCount = 0;
        if($search){
            $filterFields = array("server_name", "unique_code", "protocol", "smtp_host", "smtp_user", "smtp_port", "smtp_crypto");
            $this->db->select("*");
            $this->db->from("gccmaster.email_protocol_settings");
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    private function get_all_post($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        $this->db->select("*");
        $this->db->from("gccmaster.email_template");
        $this->db->limit($limit, $offset);

        if($sortBy){
            $this->db->order_by($sortBy, $sortOrder);
        }else{
            $this->db->order_by("id", "DESC");
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $rs->send_to = unserialize($rs->send_to);
                $rs->cc_to = unserialize($rs->cc_to);
                $rs->bcc_to = unserialize($rs->bcc_to);
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                if(isset($v->send_to)){
                    for($i=0; $i<count($v->send_to); $i++){
                        $v->send_to[$i] = $v->send_to[$i]."\n";
                    }
                }
                if(isset($v->cc_to) && $v->cc_to != ""){
                    for($i=0; $i<count($v->cc_to); $i++){
                        $v->cc_to[$i] = $v->cc_to[$i]."\n";
                    }
                }
                if(isset($v->bcc_to) && $v->bcc_to != ""){
                    for($i=0; $i<count($v->bcc_to); $i++){
                        $v->bcc_to[$i] = $v->bcc_to[$i]."\n";
                    }
                }
                $data[] = $v;
            }
            return $data;
        }else{
            return array();
        }
    }

    private function get_all_post_count(){
        $this->db->from("gccmaster.email_template");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_item($search=null, $limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        if($search){
            $filterFields = array("name", "description", "module", "send_to", "cc_to", "bcc_to");
            $this->db->select("*");
            $this->db->from("gccmaster.email_template");
            $this->db->limit($limit, $offset);
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();
            if($sortBy){
                $this->db->order_by($sortBy, $sortOrder);
            }else{
                $this->db->order_by("id", "DESC");
            }
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $key => $rs){
                    $rs->send_to = unserialize($rs->send_to);
                    $rs->cc_to = unserialize($rs->cc_to);
                    $rs->bcc_to = unserialize($rs->bcc_to);
                    $arrData[$key] = $rs;
                }
                $data = array();
                foreach($arrData as $k=>$v){
                    if(isset($v->send_to) && is_array($v->send_to)){
                        for($i=0; $i<count($v->send_to); $i++){
							if($v->send_to[$i] && $v->send_to[$i]){
								$v->send_to[$i] = $v->send_to[$i]."\n";
							}
                        }
                    }
                    if(isset($v->cc_to) && is_array($v->cc_to)){
                        for($i=0; $i<count($v->cc_to); $i++){
							if(isset($v->cc_to[$i]) && $v->cc_to[$i]){
								$v->cc_to[$i] = $v->cc_to[$i]."\n";
							}
                        }
                    }
                    if(isset($v->bcc_to) && is_array($v->bcc_to)){
                        for($i=0; $i<count($v->bcc_to); $i++){
							if($v->bcc_to[$i]){
								$v->bcc_to[$i] = $v->bcc_to[$i]."\n";
							}
                        }
                    }
                    $data[] = $v;
                }
                return $data;
            }else{
                return array();
            }
        }else{
            return array();
        }
    }

    private function get_searched_item_count($search=null){
        $rowCount = 0;
        if($search){
            $filterFields = array("name", "description", "module", "send_to", "cc_to", "bcc_to");
            $this->db->select("*");
            $this->db->from("gccmaster.email_template");
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    function emailLookup(){
        $get = $this->input->get();
        $resultarray = array();
        
        //original
        /*** if(isset($get['q'])){ **/
        if(isset($get['term'])){
            if(!empty($get['email'])){
                $splited = explode(',', $get['email']);
                $_temp = '"'.implode('", "', $splited).'"';
    
                $check_email = (!empty($get['email'])) ? " AND a.email NOT IN (".$_temp.") " : "";
            }else{
                $check_email = '';
            }
            // original
            /*** $query = $this->db->query("SELECT a.id, b.firstname, b.lastname, b.middlename, b.suffix, a.email FROM gccmaster.tblusers a, gccmaster.tblemployees b WHERE b.id=a.emp_id AND (a.email !='NO EMAIL ADDRESS' AND a.email !='NO EMAIL' AND a.email !='' AND b.employee_status = 'Active') AND (b.firstname LIKE '%{$get['q']}%' || b.lastname LIKE '%{$get['q']}%' || a.email LIKE '%{$get['q']}%') {$check_email} ORDER BY b.firstname ASC LIMIT 10"); **/
            $query = $this->db->query("SELECT a.id, b.firstname, b.lastname, b.middlename, b.suffix, a.email FROM gccmaster.tblusers a, gccmaster.tblemployees b WHERE b.id=a.emp_id AND (a.email !='NO EMAIL ADDRESS' AND a.email !='NO EMAIL' AND a.email !='' AND b.employee_status = 'Active') AND (b.firstname LIKE '%{$get['term']}%' || b.lastname LIKE '%{$get['term']}%' || a.email LIKE '%{$get['term']}%') {$check_email} ORDER BY b.firstname ASC LIMIT 10");
        }else{

            if(!empty($get['email'])){
                $splited = explode(',', $get['email']);
                $_temp = '"'.implode('", "', $splited).'"';
    
                $check_email = (!empty($get['email'])) ? " AND a.email NOT IN (".$_temp.") " : "";
            }else{
                $check_email = '';
            }

            $query = $this->db->query("SELECT a.id, b.firstname, b.lastname, b.middlename, b.suffix, a.email FROM gccmaster.tblusers a, gccmaster.tblemployees b WHERE b.id=a.emp_id AND (a.email !='NO EMAIL ADDRESS' AND a.email !='NO EMAIL' AND a.email !='' AND b.employee_status = 'Active') {$check_email} ORDER BY b.firstname ASC LIMIT 10");
        }
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $tempRs = (array) $_query;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $_query['display_name'] = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                $data["id"] = $_query["email"];
                $data["text"] =  $_query["email"]." | ".$_query["display_name"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray, 'q' => (isset($get['q'])) ? $get['q'] : "" );
    }

    function setEmailProtocolSettings(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $exclude = array("smtp_user", "smtp_pass", "unique_code");
            foreach ($post as $key => $value) {
                if(!in_array($key, $exclude)){
                    $post[$key] = strtolower($value);
                }
            }

            $inserted = $this->db->insert("gccmaster.email_protocol_settings", $post);
            if($inserted){
                $resultset["response"] = true;
                $resultset["toastr_msg"] = "Protocol data has been added.";
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to add protocol data!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }

    function updateEmailProtocolSettings(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            if(isset($post["id"]) && $post["id"]){
                $tempWhere = array();
                $tempWhere["id"] = $post["id"];
                unset($post["id"]);
                $exclude = array("smtp_user", "smtp_pass", "unique_code");
                foreach ($post as $key => $value) {
                    if(!in_array($key, $exclude)){
                        $post[$key] = strtolower($value);
                    }
                }
                $updated = $this->db->update("gccmaster.email_protocol_settings", $post, $tempWhere);
                if($updated && $this->db->affected_rows() > 0){
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Protocol data has been updated.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update protocol data!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to update protocol data, no post id found!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }

    function removeEmailProtocolSettings(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $deleted = $this->db->delete("gccmaster.email_protocol_settings", $post);
            if($deleted){
                $resultset["response"] = true;
                $resultset["toastr_msg"] = "Protocol data has been removed.";
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to remove protocol data!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }

    function getEmailProtocolById($id=null){
        $resultset = array();
        if($id){
            $qTemp = $this->db->get_where("gccmaster.email_protocol_settings", array("id"=>$id));
            if($qTemp->num_rows() == 1){
                $resultset["response"] = true;
                $resultset["row"] = $qTemp->row();
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }
    function saveEmail(){
        $post = $this->input->post();

        $name= $post["name"];
        $description= $post["description"];
        $module= $post["module"];
        $sendto = $post["send_to"];
        $sendto2 = serialize($sendto);
        $ccto =  isset($_POST['cc_to'])?$_POST['cc_to']:'';
        $ccto2 = serialize($ccto);
        $bccto =  isset($_POST['bcc_to'])?$_POST['bcc_to']:'';
        $bccto2 = serialize($bccto);

        $this->db->select("*");
        $this->db->from("gccmaster.email_template");
        $query = $this->db->get();
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){

            }
        }
        $results = array();
        if($name != $_query["name"]){
            $this->db->set('name', $name);
            $this->db->set('description', $description);
            $this->db->set('module', $module);
            $this->db->set('send_to', $sendto2);
            $this->db->set('cc_to', $ccto2);
            $this->db->set('bcc_to', $bccto2);
            $this->db->insert('gccmaster.email_template');

            $results['toastr_msg']="Done!";
        }else{
            $results['toastr_msg']="Template name already exist";
        }

        return $results;
     }

     public function editDetails($id){
        $this->db->select("*");
        $this->db->from("gccmaster.email_template");
        $this->db->where("id",$id);
        $query = $this->db->get();

        $query = $query->num_rows() > 0 ? $query->row_array() :  FALSE;
        $query["send_to"]=unserialize($query["send_to"]);
        $query["cc_to"]=unserialize($query["cc_to"]);
        $query["bcc_to"]=unserialize($query["bcc_to"]);
        return $query;
    }

    public function editEmail($id){
        $result = array();
        $post = $this->input->post();

        $edit_send_to = $post['edit_send_to'];
        $values = serialize($edit_send_to);

        $edit_cc_to = isset($post['edit_cc_to'])?$post['edit_cc_to']:'';
        $values2 = serialize($edit_cc_to);

        $edit_bcc_to = isset($post['edit_bcc_to'])?$post['edit_bcc_to']:'';
        $values3 = serialize($edit_bcc_to);


        $data = array(
        'name' => $post['edit_name'],
        'description'  => $post['edit_description'],
        'module' => $post['edit_module'],
        'send_to'  => $values,
        'cc_to'  => $values2,
        'bcc_to'  => $values3);

        $this->db->update('gccmaster.email_template', $data, array('id' => $id));

        $result = "True";

        return $result;
    }

    function deleteEmail($id){
        return $this->db->query("DELETE FROM gccmaster.email_template WHERE id=$id");
    }

    function sendEmail($id){
        $resultset = array();

        $this->db->from("gccmaster.email_template");
        $this->db->where('id', $id);
        $query = $this->db->get();

        if($query->num_rows() == 1){
            $row = $query->row();
            $module = $row->name;

            $email_title = $row->module;
            $content_title = "Test Title";
            $content = $email_title."\n".$content_title; //$this->load->view('templates/email/test_email', array("module"=>$email_title, "title"=>$content_title), true);

            $sendEmail = $this->core_layout->send_email($module, $email_title, $content_title, $content);
            if($sendEmail){
                $resultset["response"] = true;
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }

		return $resultset;
    }
}
