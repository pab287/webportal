<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Eng_req_m extends CI_Model {

    protected $projectTable = "gcceforms.eng_projects";
    protected $rfiTable = "gcceforms.eng_rfi_form";
    protected $reqTypeTable = "gcceforms.eng_req_type";
    protected $replyTable = "gcceforms.eng_rfi_form_reply";
    protected $user_data;
    public function __construct() {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        $this->load->model("core/upload_model", "core_upload");
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
    
    public function viewRFIRequest($id){
        $resultset = [];
        $this->db->select("a.*,b.firstname,b.middlename,b.lastname, c.project_name as project_name, c.project_location as project_location,
            CONCAT(
                b.firstname, ' ',
                IF(
                    b.middlename IS NOT NULL AND b.middlename != '',
                    CONCAT(LEFT(b.middlename,1), '. '),
                    ''
                ),
                b.lastname
            ) as created_by_name,
            CONCAT(
                d.firstname, ' ',
                IF(
                    d.middlename IS NOT NULL AND d.middlename != '',
                    CONCAT(LEFT(d.middlename,1), '. '),
                    ''
                ),
                d.lastname
            ) as requested_by_name,
            CONCAT(
                e.firstname, ' ',
                IF(
                    e.middlename IS NOT NULL AND e.middlename != '',
                    CONCAT(LEFT(e.middlename,1), '. '),
                    ''
                ),
                e.lastname
            ) as consultant_name,
        ");
        $this->db->from($this->rfiTable . ' as a');
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "LEFT");
        $this->db->join("gcceforms.eng_projects as c", "c.id = a.project_id", "LEFT");
        $this->db->join("gccmaster.tblemployees as d", "d.id = a.requested_by", "LEFT");
        $this->db->join("gccmaster.tblemployees as e", "e.id = a.consultant", "LEFT");
        $this->db->where("a.id", $id);
    
        $rfiData = $this->db->get()->row_array();

        $this->db->select("*");
        $this->db->from("gcceforms.eng_rfi_form_attachments");
        $this->db->where("eng_rfi_id", $id);
        $this->db->where("type", "request");
    
        $attachments = $this->db->get()->result_array();

        $this->db->select("*");
        $this->db->from("gcceforms.eng_rfi_form_attachments");
        $this->db->where("eng_rfi_id", $id);
        $this->db->where("type", "reply");
        $reply_attachments = $this->db->get()->result_array();
        
        $resultset['reply'] = $this->getRfiReply($id);
        $resultset['data'] = $rfiData;
        $resultset['attachments'] = $attachments;
        $resultset['reply_attachments'] = $reply_attachments;
        return $resultset;
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
        $this->load->library('upload');
        $resultset = [];
        $post = $this->input->post();
        $information_needed = $this->input->post('information_needed', false);
        if($post['request_type'] == 'others'){
            $post['request_type'] = $post['other_request_type'];
        }
        $data = [
            "project_id"        => $post['project_name'],
            "requested_by"      => $post['requested_by'],
            "needed_info"       => $information_needed,
            "reply_needed"      => date("Y-m-d", strtotime($post['reply_needed'])),
            "consultant"        => $post['consultant_id'],
            "status"            => "pending",
            "request_type_code" => $post['request_type_code'],
            "rfi_no"            => $this->generateRFINumber($post['request_type_code']),
            "request_type"      => $post['request_type'],
            "created_by"        => $this->user_data['emp_id'],
        ];
    
        $this->db->trans_begin();
        $save = $this->db->insert($this->rfiTable, $data);
        if (!$save) {
            $this->db->trans_rollback();
            return [
                "success" => false,
                "message" => "Failed to create RFI."
            ];
        }
        $insert_id = $this->db->insert_id();
        $resultset["file_upload"] = [];
        if (!empty($_FILES['files']['name'][0])) {
            $filepath = FCPATH . "uploads/files/engineering_request/rfi_" . $insert_id . "/";
            if (!is_dir($filepath)) {
                mkdir($filepath, 0777, true);
            }
            $filesCount = count($_FILES['files']['name']);
            for ($i = 0; $i < $filesCount; $i++) {
                $_FILES['file']['name']     = $_FILES['files']['name'][$i];
                $_FILES['file']['type']     = $_FILES['files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                $_FILES['file']['error']    = $_FILES['files']['error'][$i];
                $_FILES['file']['size']     = $_FILES['files']['size'][$i];
                $originalName = str_replace(' ', '_', $_FILES['file']['name']);
                $config = [
                    'upload_path'   => $filepath,
                    'allowed_types' => 'pdf|doc|docx|jpg|jpeg|png',
                    'max_size'      => 51200,
                    'file_name'     => $originalName,
                    'remove_spaces' => true
                ];
                $this->upload->initialize($config);
                if ($this->upload->do_upload('file')) {
                    $insertAttachment = $this->db->insert("gcceforms.eng_rfi_form_attachments",
                        [
                            "eng_rfi_id" => $insert_id,
                            "filename"   => $originalName,
                            "type"       => "request",
                            "created_by" => $this->user_data['emp_id'],
                        ]
                    );
    
                    if (!$insertAttachment) {
                        $this->db->trans_rollback();
                        return [
                            "success" => false,
                            "message" => "Failed saving attachment record."
                        ];
                    }
    
                    $resultset["file_upload"][] = [
                        "file_name" => $originalName,
                        "status"    => "success"
                    ];
    
                } else {
    
                    $this->db->trans_rollback();
                    return [
                        "success" => false,
                        "message" => strip_tags($this->upload->display_errors())
                    ];
                }
            }
        }
    
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return [
                "success" => false,
                "message" => "Transaction failed."
            ];
        }
        $this->db->trans_commit();
        $resultset["success"] = true;
        $resultset["message"] = "RFI created successfully.";
        $resultset["rfi_id"]  = $insert_id;
        return $resultset;
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

    private function generateRFINumber($code){
        $prefix = "rfi-";
        if ($code) {
            $prefix .= strtolower($code) . "-";
        }
        $this->db->where('request_type', $code);
        $count = $this->db->count_all_results($this->rfiTable);
        $sequence = $count + 1;
        $numberPart = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        return $prefix . $numberPart;
    }

    private function getRfiReply($id){
        $this->db->select("a.id, a.status, a.reply, a.reply_remarks, a.created_by, a.created_at, c.description as company_name, d.name as position_name,
            CONCAT(b.firstname, ' ', IF(b.middlename IS NOT NULL AND b.middlename != '', CONCAT(LEFT(b.middlename,1), '. '), ''), b.lastname) as created_by_name,
        ");
        $this->db->from($this->replyTable. ' as a');
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "LEFT");
        $this->db->join("gcchris.tblcompanies as c", "c.id = b.company_id", "LEFT");
        $this->db->join("gcchris.tblposition as d", "d.id = b.position", "LEFT");
        $this->db->where("a.rfi_id", $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function saveReply(){
        $resultset = array();
        $post = $this->input->post();
        $rfiId = $post['rfi_id'];
        $this->db->trans_begin();
        $data = array(
            "rfi_id"     => $rfiId,
            "reply"      => $post['reply'],
            "created_by" => $this->user_data['emp_id'],
            "created_at" => date("Y-m-d H:i:s"),
        );
        $saveReply = $this->db->insert($this->replyTable, $data);
        if(!$saveReply){
            $this->db->trans_rollback();
            return [
                "success" => false,
                "message" => "Failed to save reply."
            ];
        }
    
        $insert_id = $this->db->insert_id();
        $resultset["file_upload"] = [];
        if (!empty($_FILES['files']['name'][0])) {
            $filepath = FCPATH . "uploads/files/engineering_request/rfi_" . $rfiId . "/reply_" . $insert_id . "/";
            if (!is_dir($filepath)) {
                mkdir($filepath, 0777, true);
            }
    
            $filesCount = count($_FILES['files']['name']);
            for ($i = 0; $i < $filesCount; $i++) {
                $_FILES['file']['name']     = $_FILES['files']['name'][$i];
                $_FILES['file']['type']     = $_FILES['files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                $_FILES['file']['error']    = $_FILES['files']['error'][$i];
                $_FILES['file']['size']     = $_FILES['files']['size'][$i];
                $originalName = str_replace(' ', '_', $_FILES['file']['name']);
                $config = [
                    'upload_path'   => $filepath,
                    'allowed_types' => 'pdf|doc|docx|jpg|jpeg|png',
                    'max_size'      => 51200,
                    'file_name'     => $originalName,
                    'remove_spaces' => true
                ];
    
                $this->upload->initialize($config);
                if ($this->upload->do_upload('file')) {
                    $saveAttachment = $this->db->insert(
                        "gcceforms.eng_rfi_form_attachments",
                        [
                            "eng_rfi_id" => $rfiId,
                            "filename"   => $originalName,
                            "type"       => "reply",
                            "created_by" => $this->user_data['emp_id'],
                        ]
                    );
    
                    if(!$saveAttachment){
                        $this->db->trans_rollback();
                        return [
                            "success" => false,
                            "message" => "Failed saving attachment."
                        ];
                    }
    
                    $resultset["file_upload"][] = [
                        "file_name" => $originalName,
                        "status"    => "success"
                    ];
                } else {
                    $this->db->trans_rollback();
                    return [
                        "success" => false,
                        "message" => strip_tags($this->upload->display_errors())
                    ];
                }
            }
        }
    
        $updateStatus = $this->db->where('id', $rfiId)->update($this->rfiTable, ['status' => 'for_checking']);
    
        if(!$updateStatus){
            $this->db->trans_rollback();
            return [
                "success" => false,
                "message" => "Failed updating RFI status."
            ];
        }
    
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return [
                "success" => false,
                "message" => "Transaction failed."
            ];
        }
    
        $this->db->trans_commit();
        $resultset["success"] = true;
        $resultset["message"] = "Reply saved successfully.";
        return $resultset;
    }


}