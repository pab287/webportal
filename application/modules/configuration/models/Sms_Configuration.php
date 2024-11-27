<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Sms_Configuration extends CI_Model{

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

    function smsProtocolDatatableRequest(){
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

    private function get_all_protocol_post($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        $this->db->select("*");
        $this->db->from("gccsms.tblsms");
        $this->db->limit($limit, $offset);

        if($sortBy){
            $this->db->order_by($sortBy, $sortOrder);
        }else{
            $this->db->order_by("id", "DESC");
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $resultarray = array();
            foreach($query->result_array() as $_query){
                $data = array();

                $data["id"] = $_query["id"];
                $data["sms_ip"] = $_query["sms_ip"];
                $data["sms_port"] = $_query["sms_port"];
                $data["sms_user"] = $_query["sms_user"];
                $data["sms_pass"] = $_query["sms_pass"];
                $data["exclude"] = $_query["exclude"];
                $data["sms_footer"] = $_query["sms_footer"];
                $data["department_id"] = $_query["department_id"];
                $data["department"] = $this->is_serial($_query["department_id"]) ? $this->getDepartmentListName(unserialize($_query["department_id"])) : "";
                $data["is_connected"] = $_query["is_connected"];
                $data["role"] = $this->authenticate->getRoleId();
                $resultarray[] = $data;
            }
            return $resultarray;
        }else{
            return array();
        }
    }

    public static function is_serial($string) {
        return (@unserialize($string) !== false);
    }

    private function get_all_protocol_post_count(){
        $this->db->from("gccsms.tblsms");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_protocol_item($search=null, $limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        if($search){
            $filterFields = array("sms_ip", "sms_port", "sms_user");
            $this->db->select("*");
            $this->db->from("gccsms.tblsms");
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
                $resultarray = array();
                foreach($query->result_array() as $_query){
                    $data = array();
    
                    $data["id"] = $_query["id"];
                    $data["sms_ip"] = $_query["sms_ip"];
                    $data["sms_port"] = $_query["sms_port"];
                    $data["sms_user"] = $_query["sms_user"];
                    $data["sms_pass"] = $_query["sms_pass"];
                    $data["exclude"] = $_query["exclude"];
                    $data["sms_footer"] = $_query["sms_footer"];
                    $data["department_id"] = $_query["department_id"];
                    $data["department"] = $this->is_serial($_query["department_id"]) ? $this->getDepartmentListName(unserialize($_query["department_id"])) : "";
                    $data["is_connected"] = $_query["is_connected"];
                    $data["role"] = $this->authenticate->getRoleId();
                    $resultarray[] = $data;
                }
                return $resultarray;
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
            $filterFields = array("sms_ip", "sms_port", "sms_user");
            $this->db->select("*");
            $this->db->from("gccsms.tblsms");
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

    function setSmsProtocolSettings(){
        $resultset = array();
        $post = $this->input->post();
        $post["department_id"] = serialize($post["department_id"]);
        if(isset($post) && $post){

            $inserted = $this->db->insert("gccsms.tblsms", $post);
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

    function getSmsProtocolById($id=null){
        $resultset = array();
        if($id){
            $qTemp = $this->db->get_where("gccsms.tblsms", array("id"=>$id));
            if($qTemp->num_rows() > 0){
                foreach($qTemp->result_array() as $_query){
                    $data = array();
    
                    $data["id"] = $_query["id"];
                    $data["sms_ip"] = $_query["sms_ip"];
                    $data["sms_port"] = $_query["sms_port"];
                    $data["sms_user"] = $_query["sms_user"];
                    $data["sms_pass"] = $_query["sms_pass"];
                    $data["sms_footer"] = $_query["sms_footer"];
                    $data["department_id"] = $this->is_serial($_query["department_id"]) ? $this->getDepartmentList(unserialize($_query["department_id"])) : array();
                    $data["is_connected"] = $_query["is_connected"];
                    
                    $resultset["response"] = true;
                    $resultset["row"] = $data;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function getDepartmentList($department_id){
        $array = array();
        foreach($department_id as $value){
            $list = array();
            $list["id"] = $value;
            $list["name"] = $this->getDepartmentName($value);
            array_push($array, $list);
        }
        return $array;
    }

    function getDepartmentListName($department_id){
        $name = "";
        foreach($department_id as $value){
            $name = $name=="" ? $this->getDepartmentName($value) : $name.", ".$this->getDepartmentName($value);
        }
        return $name;
    }

    function getDepartmentName($department_id){
        $this->db->select("code");
        $this->db->from("gcchris.tbldepartments");
        $this->db->where("id",$department_id);
        $this->db->or_where("code",$department_id);
        $query = $this->db->get();
        return $query ? $query->row_array()['code'] : "";
    }

    function updateSmsProtocolSettings(){
        $resultset = array();
        $post = $this->input->post();
        $post["department_id"] = serialize($post["department_id"]);
        if(isset($post) && $post){
            if(isset($post["id"]) && $post["id"]){
                $tempWhere = array();
                $tempWhere["id"] = $post["id"];
                unset($post["id"]);
                
                $updated = $this->db->update("gccsms.tblsms", $post, $tempWhere);
                if($updated){
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

    function removeSmsProtocolSettings(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $deleted = $this->db->delete("gccsms.tblsms", $post);
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

    function smsProtocolConnect(){
        $post = $this->input->post();
        $id = $post['id'];
        $post['is_connected'] = $post['is_connected'] ? 0 : 1;
        $resultarray = array();

        $this->db->where("id",$id);
        $query = $this->db->update("gccsms.tblsms",$post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Successfully updated.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error updating.";
        }

        return $resultarray;
    }

    function smsProtocolExclude(){
        $post = $this->input->post();
        $id = $post['id'];
        $post['exclude'] = $post['exclude'] ? 0 : 1;
        $resultarray = array();

        $this->db->where("id",$id);
        $query = $this->db->update("gccsms.tblsms",$post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Successfully updated.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error updating.";
        }

        return $resultarray;
    }

    function smsFetchDepartment(){
        $get = $this->input->get();
        $resultarray = array();
        $data = array();

        if (isset($get['q'])) {
            $query = $this->db->query("SELECT id, code
            FROM gcchris.tbldepartments
            WHERE is_archived='0' AND (code LIKE '%{$get['q']}%') ORDER BY code ASC");
        }else{
            $query = $this->db->query("SELECT id, code
            FROM gcchris.tbldepartments
            WHERE is_archived='0' ORDER BY code ASC");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {

                if(!$this->checkDepartment($_query["id"])){
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["code"];
                    $resultarray[] = $data;
                }
            }
        }

        return array("results" => $resultarray);
    }

    function checkDepartment($department_id){
        $this->db->select("department_id");
        $this->db->from("gccsms.tblsms");
        $query = $this->db->get();
        foreach($query->result_array() as $_query){
            if($this->is_serial($_query["department_id"])){
                foreach(unserialize($_query["department_id"]) as $id){
                    if($department_id == $id){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function select2DepartmentData(){
        $this->db->select("departments.id, UPPER(CONCAT(departments.`code`,' | ', departments.`description`)) `text`, departments.*");
        $this->db->order_by("`code`", "ASC");
        $results = $this->db->get("gcchris.tbldepartments departments")->result();
        return $results;
    }

}