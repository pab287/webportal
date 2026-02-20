<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Eng_req_m extends CI_Model {

    protected $projectTable = "gcceforms.eng_projects";
    protected $rfiTable = "gcceforms.eng_rfi_form";
    protected $reqTypeTable = "gcceforms.eng_req_type";
    protected $user_data;
    public function __construct() {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
    }

    public function getRFIs(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $is_archive =  (isset($post["is_archive"]) && $post["is_archive"])? $post["is_archive"]: '';
        $date_range = (isset($post["date_range"]) && $post["date_range"])? $post["date_range"]: false;
        $filterFields = array(
            "project_name","project_location",
            "b.firstname","b.middlename","b.lastname",
            "CONCAT(b.firstname, ' ', IF(b.middlename IS NOT NULL AND b.middlename != '', CONCAT(LEFT(b.middlename,1), '. '), ''), b.lastname)",
            "CONCAT(b.firstname, ' ', b.lastname)",
            "CONCAT(b.lastname, ' ', b.firstname)"
        );

        $rowData = $this->getRFIsData($search, $limit, $offset, $sortBy, $sortOrder, $filterFields, $is_archive);
        $rowCount = $this->getRFIsDataCount($search,$filterFields, $is_archive);
        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;
        return $resultset;
    }

    private function getRFIsData($search, $limit, $offset, $sortBy, $sortOrder, $filterFields, $is_archive){
        $this->db->select("a.*");
        $this->db->from($this->rfiTable.' as a');
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "LEFT");
        $this->db->where("a.is_archive", $is_archive);
        if ($search) {
            $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            $this->db->group_end();
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $this->db->group_by("a.id");
        $query = $this->db->get();
        return $query->result_array();
    }

    private function getRFIsDataCount($search,$filterFields, $is_archive){
        $this->db->select("a.*");
        $this->db->from($this->rfiTable.' as a');
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "LEFT");
        $this->db->where("a.is_archive", $is_archive);
        if ($search) {
            $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            $this->db->group_end();

        }
        $query = $this->db->get();
        return $query->num_rows();
    }   

    public function getProjects(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $is_archive =  (isset($post["is_archive"]) && $post["is_archive"])? $post["is_archive"]: '';
        $date_range = (isset($post["date_range"]) && $post["date_range"])? $post["date_range"]: false;
        $filterFields = array(
            "project_name","project_location",
            "b.firstname","b.middlename","b.lastname",
            "CONCAT(b.firstname, ' ', IF(b.middlename IS NOT NULL AND b.middlename != '', CONCAT(LEFT(b.middlename,1), '. '), ''), b.lastname)",
            "CONCAT(b.firstname, ' ', b.lastname)",
            "CONCAT(b.lastname, ' ', b.firstname)"
        );

        $rowData = $this->getProjectsData($search, $limit, $offset, $sortBy, $sortOrder, $filterFields, $is_archive);
        $rowCount = $this->getProjectsDataCount($search,$filterFields, $is_archive);
        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;
        return $resultset;
    }

    private function getProjectsData($search, $limit, $offset, $sortBy, $sortOrder, $filterFields, $is_archive){
        $this->db->select("a.*, b.firstname, b.middlename, b.lastname, 
            CONCAT(b.firstname, ' ', IF(b.middlename IS NOT NULL AND b.middlename != '', CONCAT(LEFT(b.middlename,1), '. '), ''), b.lastname) as created_by_name");
        $this->db->from($this->projectTable.' as a');
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "LEFT");
        $this->db->where("a.is_archive", $is_archive);

        if ($search) {
            $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            $this->db->group_end();
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $this->db->group_by("a.id");
        $query = $this->db->get();
        return $query->result_array();
    }

    private function getProjectsDataCount($search,$filterFields, $is_archive){
        $this->db->select("a.*, b.firstname, b.middlename, b.lastname, 
            CONCAT(b.firstname, ' ', IF(b.middlename IS NOT NULL AND b.middlename != '', CONCAT(LEFT(b.middlename,1), '. '), ''), b.lastname) as created_by_name");
        $this->db->from($this->projectTable.' as a');
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "LEFT");
        $this->db->where("a.is_archive", $is_archive);

        if ($search) {
            $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function saveProject(){
        $post = $this->input->post();
        $data = array(
            "project_name" => $post['project_name'],
            "project_location" => $post['project_location'],
            "created_by" => $this->user_data['emp_id'],
        );

        $this->db->insert($this->projectTable, $data);
        if($this->db->affected_rows() > 0){
            return array("success" => true, "message" => "Project saved successfully.");
        } else {
            return array("success" => false, "message" => "Failed to save project.");
        }
    }

    public function updateProject(){
        $post = $this->input->post();
        $data = array(
            "project_name" => $post['project_name'],
            "project_location" => $post['project_location'],
            "updated_by" => $this->user_data['emp_id'],
            "updated_at" => date("Y-m-d H:i:s"),
        );

        $this->db->where("id", $post['id']);
        $this->db->update($this->projectTable, $data);
        if($this->db->affected_rows() > 0){
            return array("success" => true, "message" => "Project updated successfully.");
        } else {
            return array("success" => false, "message" => "Failed to update project.");
        }
    }

    public function select2Employee(){
        $arrData = array();
        $this->db->select("a.id, a.lastname, a.firstname, a.middlename, a.suffix");
        $this->db->from('gccmaster.tblemployees as a');
        $this->db->where("a.employee_status", "Active");
        $this->db->group_by('a.id');
        $this->db->order_by("a.id", "DESC");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $key => $rs) {
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $row = array();
                $row["id"] = $rs->id;
                $row["text"] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                // $row["department"] = $rs->department;
                // $row["position"] = $rs->position;
                // $row["site_locations"] = $rs->location_name;
                // $row["telegram_id"] = $rs->telegram_id;
                // $row["app_name"] = $rs->app_name;
                $arrData[] = $row;
            }
        }

        return $arrData;
    }

    public function select2Projects(){
        $arrData = array();
        $this->db->select("id, project_name, project_location");
        $this->db->from($this->projectTable);
        $this->db->where("is_archive", "N");
        $this->db->order_by("project_name", "ASC");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $key => $rs) {
                $row = array();
                $row["id"] = $rs->id;
                $row["text"] = $rs->project_name;
                $row["project_location"] = $rs->project_location;
                $arrData[] = $row;
            }
        }

        return $arrData;
    }

    public function createRFI(){
        $post = $this->input->post();
        $cc_to = isset($post['cc_to'])? implode(",", $post['cc_to']): '';
        $attachments = isset($post['attachments'])? implode(",", $post['attachments']): '';
        $data = array(
            "project_name" => $post['project_name'],
            "project_location" => $post['project_location'],
            "prepared_dt" => date("Y-m-d", strtotime($post['prepared_dt'])),
            "reply_needed" => date("Y-m-d", strtotime($post['reply_needed'])),
            "request_to" => $post['send_to'],
            "consultant" => $post['consultant'],
            "request_cc" => $cc_to,
            "project_status" => "pending",
            "request_type" => $post['request_type'],
            // "attachments" => $attachments,
            // "remarks" => $post['remarks'],
            // "reply" => $post['reply'],
            "created_by" => $this->user_data['emp_id'],
        );
        $this->db->insert($this->rfiTable, $data);
        if($this->db->affected_rows() > 0){
            return array("success" => true, "message" => "Request saved successfully.");
        } else {
            return array("success" => false, "message" => "Failed to save request.");
        }
    }

    public function getReqType(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $is_archive =  (isset($post["is_archive"]) && $post["is_archive"])? $post["is_archive"]: '';
        $date_range = (isset($post["date_range"]) && $post["date_range"])? $post["date_range"]: false;
        $filterFields = array(
            "project_name","project_location",
            "b.firstname","b.middlename","b.lastname",
            "CONCAT(b.firstname, ' ', IF(b.middlename IS NOT NULL AND b.middlename != '', CONCAT(LEFT(b.middlename,1), '. '), ''), b.lastname)",
            "CONCAT(b.firstname, ' ', b.lastname)",
            "CONCAT(b.lastname, ' ', b.firstname)"
        );

        $rowData = $this->getReqTypeData($search, $limit, $offset, $sortBy, $sortOrder, $filterFields, $is_archive);
        $rowCount = $this->getReqTypeDataCount($search,$filterFields, $is_archive);
        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;
        return $resultset;
    }

    private function getReqTypeData($search, $limit, $offset, $sortBy, $sortOrder, $filterFields, $is_archive){
        $this->db->select("a.*, b.firstname, b.middlename, b.lastname, 
            CONCAT(b.firstname, ' ', IF(b.middlename IS NOT NULL AND b.middlename != '', CONCAT(LEFT(b.middlename,1), '. '), ''), b.lastname) as created_by_name,
            CONCAT(c.firstname, ' ', IF(c.middlename IS NOT NULL AND c.middlename != '', CONCAT(LEFT(c.middlename,1), '. '), ''), c.lastname) as person_in_charge_name
            ");
        $this->db->from($this->reqTypeTable.' as a');
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "LEFT");
        $this->db->join("gccmaster.tblemployees as c", "c.id = a.person_in_charge", "LEFT");
        $this->db->where("a.is_archived", $is_archive);
        if ($search) {
            $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            $this->db->group_end();
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $this->db->group_by("a.id");
        $query = $this->db->get();
        return $query->result_array();
    }

    private function getReqTypeDataCount($search,$filterFields, $is_archive){
        $this->db->select("a.*, b.firstname, b.middlename, b.lastname, 
            CONCAT(b.firstname, ' ', IF(b.middlename IS NOT NULL AND b.middlename != '', CONCAT(LEFT(b.middlename,1), '. '), ''), b.lastname) as created_by_name,
            CONCAT(c.firstname, ' ', IF(c.middlename IS NOT NULL AND c.middlename != '', CONCAT(LEFT(c.middlename,1), '. '), ''), c.lastname) as person_in_charge_name
            ");
        $this->db->from($this->reqTypeTable.' as a');
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "LEFT");
        $this->db->join("gccmaster.tblemployees as c", "c.id = a.person_in_charge", "LEFT");
        $this->db->where("a.is_archived", $is_archive);
        if ($search) {
            $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function saveReqType(){
        $post = $this->input->post();
        $data = array(
            "type_name" => strtolower($post['request_type']),
            "type_code" => strtolower($post['type_code']),
            "person_in_charge" => $post['person_in_charge'],
            "created_by" => $this->user_data['emp_id'],
        );
        $this->db->insert($this->reqTypeTable, $data);
        if($this->db->affected_rows() > 0){
            return array("success" => true, "message" => "Request type saved successfully.");
        } else {
            return array("success" => false, "message" => "Failed to save request type.");
        }
    }

    public function updateReqType(){
        $post = $this->input->post();
        $data = array(
            "type_name" => strtolower($post['request_type']),
            "type_code" => strtolower($post['type_code']),
            "person_in_charge" => $post['person_in_charge'],
            "updated_by" => $this->user_data['emp_id'],
            "updated_at" => date("Y-m-d H:i:s"),
        );
        $this->db->where("id", $post['id']);
        $this->db->update($this->reqTypeTable, $data);
        if($this->db->affected_rows() > 0){
            return array("success" => true, "message" => "Request type updated successfully.");
        } else {
            return array("success" => false, "message" => "Failed to update request type.");
        }
    }

    public function archiveReqType(){
        $post = $this->input->post();
        $data = array(
            "is_archived" => 1,
            "updated_by" => $this->user_data['emp_id'],
            "updated_at" => date("Y-m-d H:i:s"),
        );
        $this->db->where("id", $post['id']);
        $this->db->update($this->reqTypeTable, $data);
        if($this->db->affected_rows() > 0){
            return array("success" => true, "message" => "Request type archived successfully.");
        } else {
            return array("success" => false, "message" => "Failed to archive request type.");
        }
    }

    public function getReqTypes(){
        $this->db->select("id, type_name, person_in_charge, type_code");
        $this->db->from($this->reqTypeTable);
        $this->db->where("is_archived", 0);
        $this->db->order_by("type_name", "ASC");
        $query = $this->db->get();
        return $query->result_array();
    }


}