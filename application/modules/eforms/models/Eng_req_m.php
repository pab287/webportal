<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Eng_req_m extends CI_Model {

    protected $projectTable = "gcceforms.eng_projects";
    protected $rfiTable = "gcceforms.eng_rfi_form";
    protected $reqTypeTable = "gcceforms.eng_req_type";
    protected $replyTable = "gcceforms.eng_rfi_form_reply";
    protected $replyAttachmentTable = "gcceforms.eng_rfi_form_attachments";
    protected $telegramConfigTable = "gccmaster.telegram_config";
    protected $usersTable = "gccmaster.tblusers";
    protected $user_data;
    public function __construct() {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        $this->load->model("core/upload_model", "core_upload");
    }

    public function getRFIs(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 0;
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
        $this->db->select("a.*, 
                    CONCAT(
                b.firstname, ' ',
                IF(
                    b.middlename IS NOT NULL AND b.middlename != '',
                    CONCAT(LEFT(b.middlename,1), '. '),
                    ''
                ),
                b.lastname
            ) as created_by_name,
        c.project_name, c.project_location");
        $this->db->from($this->rfiTable.' as a');
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "LEFT");
        $this->db->join($this->projectTable." as c", "c.id = a.project_id", "LEFT");
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

        $resultset['reply'] = $this->getRfiReply($id);
        $resultset['data'] = $rfiData;
        $resultset['attachments'] = $attachments;
        $resultset['reply_attachments'] = $this->getRFIAttachments($resultset['reply']);
        return $resultset;
    }

    public function archiveRequest(){
        $resultSet = array();
        $post = $this->input->post();
        $id = $post['id'];
    
        $this->db->where('id', $id);
        $this->db->update($this->rfiTable, [
            'is_archive' => 1
        ]);
    
        if ($this->db->affected_rows() > 0) {
            $resultSet = array(
                'success' => true,
                'message'=> 'Request archived successfully.'
            );
        } else {
            $resultSet = array(
                'success' => false,
                'message'=> 'Failed to archive request.'
            );
        }
        return $resultSet;
    }

    public function restoreRequest(){
        $resultSet = array();
        $post = $this->input->post();
        $id = $post['id'];
        $this->db->where('id', $id);
        $this->db->update($this->rfiTable, [
            'is_archive' => 0
        ]);
    
        if ($this->db->affected_rows() > 0) {
            $resultSet = array(
                'success' => true,
                'message'=> 'Request restored successfully.'
            );
        } else {
            $resultSet = array(
                'success' => false,
                'message'=> 'Failed to restore request.'
            );
        }
        return $resultSet;
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
        $resultset = []; $data = [];
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
        $data['id'] = $insert_id;
        $data['requested_by_name'] = $post['requested_by_name'];
        $data['project_location'] = $post['project_location'];
        $data['attention'] = $post['consultant'];
        $data['project_name_text'] = $post['project_name_text'];
        $resultset['telegram'] = $this->sendCreateTelegram($data);

        $resultset["file_upload"] = [];

        $insert_reply = $this->db->insert($this->replyTable,
            [
                "rfi_id" => $insert_id,
                "reply"   => "",
                "status"       => "pending",
                "created_by" => $this->user_data['emp_id'],
            ]
        );

        if (!$insert_reply) {
            $this->db->trans_rollback();
            return [
                "success" => false,
                "message" => "Failed to create RFI."
            ];
        }

        if (!empty($_FILES['files']['name'][0])) {
            $filepath = FCPATH . "uploads/files/engineering_request/rfi_" . $insert_id . "/request". "/";
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
                $originalName = str_replace([' ', '(', ')'], ['_', '', ''], $_FILES['file']['name']);
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
    
                } 
                else {
    
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
        $this->db->where('request_type_code', $code);
        $count = $this->db->count_all_results($this->rfiTable);
        $sequence = $count + 1;
        $numberPart = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        return $prefix . $numberPart;
    }

    private function getRfiReply($id){
        $this->db->select("a.id, a.rfi_id, a.status, a.reply, a.created_by, a.created_at, c.description as company_name, d.name as position_name,
            CONCAT(b.firstname, ' ', IF(b.middlename IS NOT NULL AND b.middlename != '', CONCAT(LEFT(b.middlename,1), '. '), ''), b.lastname) as created_by_name,
        ");
        $this->db->from($this->replyTable. ' as a');
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "LEFT");
        $this->db->join("gcchris.tblcompanies as c", "c.id = b.company_id", "LEFT");
        $this->db->join("gcchris.tblposition as d", "d.id = b.position", "LEFT");
        $this->db->where("a.rfi_id", $id);
        $this->db->where("a.is_archived", 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    private function getRFIAttachments($reply){
        if (empty($reply) || empty($reply['rfi_id'])) {
            return [];
        }
        $this->db->select("*");
        $this->db->from("gcceforms.eng_rfi_form_attachments");
        $this->db->where("eng_rfi_id",$reply['rfi_id']);
        $this->db->where("type", "reply");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function updateReply(){
        $resultset = array();
        $post = $this->input->post();
        $this->db->trans_begin();
        $data = array(
            "reply"      => $post['reply'],
            "status" => "for_approve",
        );
        
        $eng_rfi_id = $post['rfi_id'];
        $reply_id = $post['reply_id'];
        $update = $this->db->where('rfi_id', $eng_rfi_id)->where('id',$reply_id)->update($this->replyTable,$data);
        if(!$update){
            $this->db->trans_rollback();
            return [
                "success" => false,
                "message" => "Failed to update reply."
            ];
        }
        $resultset["file_upload"] = [];
        $filesToAdd    = json_decode($post['attachmentsToAdd'],    true) ?? [];
        $filesToRemove = json_decode($post['attachmentsToRemove'], true) ?? [];

        if (!empty($filesToRemove)) {
            foreach ($filesToRemove as $filename) {
                $this->db->where('eng_rfi_id', $eng_rfi_id)->where('filename', $filename)->delete($this->replyAttachmentTable);

                $filePath = FCPATH . "uploads/files/engineering_request/rfi_{$eng_rfi_id}/reply/{$filename}";
                if (file_exists($filePath)) {
                    if (!unlink($filePath)) {
                        $this->db->trans_rollback();
                        return [ "success" => false, "message" => "Failed to delete file from server: $filename"];
                    }
                }
            }
        }

        if (!empty($filesToAdd)) {
            $filepath = FCPATH . "uploads/files/engineering_request/rfi_{$eng_rfi_id}/reply/";
            
            if (!is_dir($filepath)) {
                mkdir($filepath, 0755, true);
            }

        
            foreach ($_FILES['files']['name'] as $i => $fileName) {
                $currentFileName = str_replace(' ', '_', $fileName);

                if (!in_array(strtolower($currentFileName), $filesToAdd)) {
                    continue;
                }
        
                $_FILES['file']['name']     = $_FILES['files']['name'][$i];
                $_FILES['file']['type']     = $_FILES['files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                $_FILES['file']['error']    = $_FILES['files']['error'][$i];
                $_FILES['file']['size']     = $_FILES['files']['size'][$i];
        
                $config = [
                    'upload_path'   => $filepath,
                    'allowed_types' => 'pdf|doc|docx|jpg|jpeg|png',
                    'max_size'      => 51200,
                    'file_name'     => $currentFileName,
                    'remove_spaces' => true
                ];
                $this->load->library("upload", $config);
                $this->upload->initialize($config);
                
                if ($this->upload->do_upload('file')) {
                    $uploadedName = $this->upload->data('file_name');
        
                    $saveAttachment = $this->db->insert(
                        $this->replyAttachmentTable,
                        [
                            "eng_rfi_id" => $eng_rfi_id,
                            "filename"   => $uploadedName,
                            "type"       => "reply",
                            "created_by" => $this->user_data['emp_id'],
                        ]
                    );
        
                    if (!$saveAttachment) {
                        $this->db->trans_rollback();
                        return [
                            "success" => false,
                            "message" => "Failed saving attachment: $uploadedName"
                        ];
                    }
        
                    $resultset["file_upload"][] = [
                        "file_name" => $uploadedName,
                        "fileType"   => $_FILES['file']['type'],
                        "status"    => "success"
                    ];
        
                } else {
                    $resultset["file_upload"][] = [
                        "file_name" => $currentFileName,
                        "status"    => "failed",
                        "message"   => $this->upload->display_errors('', '')
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
        $resultset["message"] = "Reply updated successfully.";
        $resultset["status"] ="for_approve";
        return $resultset;
    }
    public function processReply(){
        $resultset = ['success' => false, 'message' => ''];
    
        $post = $this->input->post();

        $status     = $post['set_status'];
        $eng_rfi_id = $post['rfi_id'];
        $reply_id   = $post['id'];
        $contacts = array(
            "requestor" => $post['requestor'],
            "creator" => $post['creator'],
            "consultant" => $post['consultant'],
        );

        if ($status == "approved") {
            $success = $this->approveReply($status, $eng_rfi_id, $reply_id);
            $resultset['success'] = $success;
            $resultset['message'] = $success ? "Reply approved successfully." : "Failed to approve reply.";
            $resultset['status'] = $status;
    
        } elseif ($status == "pending") {
            $success = $this->disapproveReply($status, $eng_rfi_id, $reply_id);
            $resultset['success'] = $success;
            $resultset['message'] = $success ? "Reply set to pending." : "Failed to update reply.";
        } elseif ($status == "noted") {
            $success = $this->noteReply($status, $eng_rfi_id, $reply_id);
            $resultset['success'] = $success;
            $resultset['message'] = $success ? "Reply noted successfully." : "Failed to note reply.";
            $resultset['telegram'] = $this->sendTelegram($eng_rfi_id,$contacts);
        } else {
            $resultset['message'] = "Invalid status.";
        }
        return $resultset;
    }



    private function approveReply($status,$eng_rfi_id,$reply_id){
        $data = array(
            'status' => $status,
            'approve_by' => $this->user_data['emp_id'],
            'approve_at' => date("Y-m-d H:i:s"),
        );
        return $this->db->where('rfi_id', $eng_rfi_id)->where('id',$reply_id)->update($this->replyTable, $data);
    }

    public function disapproveReply($status,$eng_rfi_id,$reply_id){
        $data = array(
            'status' => $status,
            'disapprove_by' => $this->user_data['emp_id'],
            'disapprove_at' => date("Y-m-d H:i:s"),
        );
        return $this->db->where('rfi_id', $eng_rfi_id)->where('id',$reply_id)->update($this->replyTable, $data);
    }

    public function noteReply($status,$eng_rfi_id,$reply_id){
        $data = array(
            'status' => $status,
            'note_by' => $this->user_data['emp_id'],
            'note_at' => date("Y-m-d H:i:s"),
        );
        return $this->db->where('rfi_id', $eng_rfi_id)->where('id',$reply_id)->update($this->replyTable, $data);
    }

    public function sendTelegram($eng_rfi_id, $contacts){
        $telegram_details = $this->getTelegramBot("gcc notification bot");
        $emp_details      = $this->getEmpTelegramId($contacts);
        $bot_token        = $telegram_details->telegram_bot_token;
        $rfiUrl           = base_url("eforms/engineering_request_forms/view_rfi_request/" . $eng_rfi_id);
    
        $message = "<b>RFI Approved</b>\nRequest for Information has been approved.";
    
        $keyboard = [
            'inline_keyboard' => [[
                ['text' => 'Open RFI', 'url' => $rfiUrl]
            ]]
        ];
    
        $results = [];
        $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
        foreach ($emp_details as $role => $telegramId) {
            if (empty($telegramId)) {
                $results[$role] = ['status' => 'skipped'];
                continue;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'chat_id'      => $telegramId,
                'text'         => $message,
                'parse_mode'   => 'HTML',
                'reply_markup' => json_encode($keyboard)
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response   = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);
            if ($response === false) {
                $results[$role] = ['status' => 'failed', 'telegram_id' => $telegramId, 'messages' => $curl_error];
            } else {
                $results[$role] = ['status' => 'sent', 'telegram_id' => $telegramId];
            }
        }
    
        return $results;
    }

    private function buildCreateMessage($data){
    $needed_info = $data['needed_info'];
    $needed_info = str_replace(
        ['</li>', '</ol>', '</ul>', '</blockquote>', '</p>', '</div>', '<br>', '<br/>', '<br />'],
        "\n",
        $needed_info
    );
    $needed_info = preg_replace('/<li[^>]*>/', '• ', $needed_info);
    $needed_info = strip_tags($needed_info);
    $needed_info = html_entity_decode($needed_info, ENT_QUOTES | ENT_HTML5);
    $needed_info = preg_replace('/\n{3,}/', "\n\n", $needed_info);
    $needed_info = trim($needed_info);
    $msg  = "<b>Request for Information has been created.</b>";
    $msg .= "\n\n<b>Project:</b> " . strtoupper($data['project_name_text']);
    $msg .= "\n<b>Location:</b> " . strtoupper($data['project_location']);
    $msg .= "\n\n<b>ATTENTION:</b> " . strtoupper($data['attention']);
    $msg .= "\n<b>RFI Reference No:</b> " . strtoupper($data['rfi_no']);
    $msg .= "\n<b>Requested By:</b> {$data['requested_by_name']}";
    $msg .= "\n<b>Request Type:</b> ".strtoupper($data['request_type']);
    $msg .= "\n<b>Reply Needed By:</b> {$data['reply_needed']}";
    $msg .= "\n<b>Request Description:</b>\n{$needed_info}";
    return $msg;
}

public function sendCreateTelegram($data){
    $message = $this->buildCreateMessage($data);
    $rfiUrl = base_url(
        "eforms/engineering_request_forms/view_rfi_request/" . $data['id']
    );

    $telegram_details = $this->getTelegramBot("gcc notification bot");

    $sendto = [$data['created_by'],$data['requested_by']];

    $telegramIds = array_merge(
        $this->getEmpTelegramId($sendto),
        [$telegram_details->chat_id]
    );

    $telegramIds = array_values(array_unique($telegramIds));
    return $this->sendTelegramMessage($message,$rfiUrl,$telegram_details->telegram_bot_token,$telegramIds);
}

private function sendTelegramMessage($message, $rfiUrl, $bot_token, $telegram_chat_ids)
{
    $keyboard = [
        'inline_keyboard' => [[
            ['text' => 'Open RFI', 'url' => $rfiUrl]
        ]]
    ];

    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";

    $results = [];

    foreach ($telegram_chat_ids as $chat_id) {
        if (empty($chat_id)) {
            continue;
        }
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'chat_id'      => $chat_id,
                'text'         => $message,
                'parse_mode'   => 'HTML',
                'reply_markup' => json_encode($keyboard)
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15
        ]);

        $response   = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($response === false) {
            $results[] = [
                'chat_id' => $chat_id,
                'status'  => 'failed',
                'error'   => $curl_error
            ];
        } else {
            $results[] = [
                'chat_id' => $chat_id,
                'status'  => ($http_code == 200 ? 'sent' : 'error'),
                'response'=> $response
            ];
        }
    }

    return $results;
}


    private function getTelegramBot($bot_name){
        $this->db->select("telegram_bot_token, chat_id");
        $this->db->from($this->telegramConfigTable);
        $this->db->where('bot_name', $bot_name);
        $this->db->where('is_archive', 0);
        $query = $this->db->get();
        return $query->row();
    }

    private function getEmpTelegramId($ids){
        $empIds = array_unique(array_values($ids));
        $this->db->select('emp_id, telegram_chat_id');
        $this->db->from($this->usersTable);
        $this->db->where_in('emp_id', $empIds);
        $query = $this->db->get()->result_array();
        $lookup = [];
        foreach ($query as $row) {
            $lookup[$row['emp_id']] = $row['telegram_chat_id'];
        }
        $result = [];
        foreach ($ids as $role => $empId) {
            $result[$role] = $lookup[$empId] ?? "";
        }
        return $result;
    }

}