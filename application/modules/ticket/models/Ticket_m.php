<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket_m extends CI_Model
{
    private $user_data = array();
    protected $tickets = "gccticket";
    private $current_actions =  array();
    private $category = "gccticket.category";

    public function __construct(){
        parent::__construct();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model", "dt_model");
        $this->user_data = $this->session->userdata("logged_in");
        $this->load->model("core/upload_model", "file_upload");
        $this->current_action = $this->core_layout->getCurrentActions();
    }

    //function to display all ticketing entries
    function ticketMasterfile()
    {
        
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        $rowCount = 0;
        $rowData = array();

        $view_own_request = (in_array("view_own_request", $this->core_layout->getCurrentActions())) ? true : false;

        $rowData = $this->get_ticket_masterfile($limit, $offset, $sortBy, $sortOrder, $search, $query_builder, $view_own_request);
        $rowCount = $this->get_ticket_masterfile_count($limit, $offset, $sortBy, $sortOrder, $search, $query_builder, $view_own_request);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    public function get_ticket_masterfile($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $search = null, $query_builder = null, $view_own_request){
        // var_dump($query_builder);
        $resultset = array();
        $filterFields = array("a.reference_no",'a.message', 'b.firstname', 'b.middlename', 'b.lastname', 'c.firstname', 'c.middlename', 'c.lastname','cat.name','sub.name','prio.name','stat.name');
        $this->db->select("a.reference_no, cat.name as category, sub.name as sub_category,prio.name as priority, a.status,a.message, a.requested_date, a.requestor,a.performed_by,a.department_id,b.firstname,b.middlename,b.lastname,c.firstname,c.middlename,c.lastname, a.id");
        $this->db->from("gccticket.ticket as a");
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.requestor", 'LEFT');
        $this->db->join("gccmaster.tblemployees as c", "c.id = a.performed_by", 'LEFT');
        $this->db->join("gccticket.category as cat" , "cat.name = a.category", 'LEFT');
        $this->db->join("gccticket.category as sub" , "sub.name = a.sub_category", 'LEFT');
        $this->db->join("gccticket.category as prio" , "prio.name = a.priority", 'LEFT');
        $this->db->join("gccticket.category as stat" , "stat.name = a.status", 'LEFT');
        $this->db->where('a.is_archived', '0');
        $this->db->where('a.status !=', "cancelled");
        if($view_own_request){
            $this->db->where('requestor', $this->user_data['emp_id']);
        }
        if($query_builder){
            $this->db->where($query_builder);
        }
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
        $i = $sortOrder[0]['column'];
        if($sortBy[$i]['data'] == "priority"){
            $this->db->order_by("prio.id", $sortOrder[0]['dir']);
        }
        else{
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $resultset = $query->result();
            foreach ($resultset as $rs) {
                $rs->performed_by = $this->requested_by($rs->performed_by);
                $rs->requestor = $this->requested_by($rs->requestor);
            }
            if(!empty($query_builder)){
                $this->core_layout->setEventLog("User generated ticket masterfile through query builder `".$query_builder."`.","generate", "success", "gcceforms", "user");
            }
            if($search){
                $this->core_layout->setEventLog("User searched `".$search."` on ticket datatable.","search", "success", "gccticket", "user");
            }
        } else {
            $resultset= [];
        }
        return $resultset;
    }
    
    private function get_ticket_masterfile_count($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $search = null, $query_builder = null, $view_own_request){
        $filterFields = array("a.reference_no",'a.message', 'b.firstname', 'b.middlename', 'b.lastname', 'c.firstname', 'c.middlename', 'c.lastname','cat.name','sub.name','prio.name','stat.name');
        $this->db->from("gccticket.ticket as a");
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.requestor", 'LEFT');
        $this->db->join("gccmaster.tblemployees as c", "c.id = a.performed_by", 'LEFT');
        $this->db->join("gccticket.category as cat" , "cat.name = a.category", 'LEFT');
        $this->db->join("gccticket.category as sub" , "sub.name = a.sub_category", 'LEFT');
        $this->db->join("gccticket.category as prio" , "prio.name = a.priority", 'LEFT');
        $this->db->join("gccticket.category as stat" , "stat.name = a.status", 'LEFT');
        $this->db->where('a.is_archived', '0');
        $this->db->where('a.status !=', "cancelled");
        if($view_own_request){
            $this->db->where('requestor', $this->user_data['emp_id']);
        }
        if($query_builder){
            $this->db->where($query_builder);
        }
        if($search){
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
    // end fnction


    function ticketArchiveMasterfile(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        $rowCount = 0;
        $rowData = array();

        $view_own_request = (in_array("view_own_request", $this->core_layout->getCurrentActions())) ? true : false;

        $rowData = $this->get_ticket_archive_masterfile($limit, $offset, $sortBy, $sortOrder, $search, $query_builder, $view_own_request);
        $rowCount = $this->get_ticket_archive_masterfile_count($limit, $offset, $sortBy, $sortOrder, $search, $query_builder, $view_own_request);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    public function get_ticket_archive_masterfile($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $search = null, $query_builder = null, $view_own_request){
        $resultset = array();
        $filterFields = array("a.reference_no",'a.message', 'b.firstname', 'b.middlename', 'b.lastname', 'c.firstname', 'c.middlename', 'c.lastname','cat.name','sub.name','prio.name','stat.name');
        $this->db->select("a.reference_no, cat.name as category, sub.name as sub_category,prio.name as priority, a.status,a.message, a.requested_date, a.requestor,a.performed_by,a.department_id,b.firstname,b.middlename,b.lastname,c.firstname,c.middlename,c.lastname, a.id");
        $this->db->from("gccticket.ticket as a");
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.requestor", 'LEFT');
        $this->db->join("gccmaster.tblemployees as c", "c.id = a.performed_by", 'LEFT');
        $this->db->join("gccticket.category as cat" , "cat.name = a.category", 'LEFT');
        $this->db->join("gccticket.category as sub" , "sub.name = a.sub_category", 'LEFT');
        $this->db->join("gccticket.category as prio" , "prio.name = a.priority", 'LEFT');
        $this->db->join("gccticket.category as stat" , "stat.name = a.status", 'LEFT');
        $this->db->where('a.is_archived', '1');
        $this->db->or_where('a.status', "cancelled");
        if($view_own_request){
            $this->db->where('requestor', $this->user_data['emp_id']);
        }
        if($query_builder){
            $this->db->where($query_builder);
        }
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
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $resultset = $query->result();
            foreach ($resultset as $rs) {
                $rs->performed_by = $this->requested_by($rs->performed_by);
                $rs->requestor = $this->requested_by($rs->requestor);
            }
            if(!empty($query_builder)){
                $this->core_layout->setEventLog("User generated ticket masterfile through query builder `".$query_builder."`.","generate", "success", "gcceforms", "user");
            }
            if($search){
                $this->core_layout->setEventLog("User searched `".$search."` on ticket archive datatable.","search", "success", "gccticket", "user");
            }
        } else {
            $resultset= [];
        }
        return $resultset;
    }
    
    private function get_ticket_archive_masterfile_count($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $search = null, $query_builder = null, $view_own_request){
        $filterFields = array("a.reference_no",'a.message', 'b.firstname', 'b.middlename', 'b.lastname', 'c.firstname', 'c.middlename', 'c.lastname','cat.name','sub.name','prio.name','stat.name');
        $this->db->from("gccticket.ticket as a");
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.requestor", 'LEFT');
        $this->db->join("gccmaster.tblemployees as c", "c.id = a.performed_by", 'LEFT');
        $this->db->join("gccticket.category as cat" , "cat.name = a.category", 'LEFT');
        $this->db->join("gccticket.category as sub" , "sub.name = a.sub_category", 'LEFT');
        $this->db->join("gccticket.category as prio" , "prio.name = a.priority", 'LEFT');
        $this->db->join("gccticket.category as stat" , "stat.name = a.status", 'LEFT');
        $this->db->where('a.is_archived', '1');
        $this->db->or_where('a.status', "cancelled");
        if($search){
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
        if($query_builder){
            $this->db->where($query_builder);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    function categoryMasterfile(){
        $resultset = array();
        $post = $this->input->post();

        $view_own_request = $this->core_layout->getCurrentActions();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;

        $rowCount = 0;
        $rowData = array();
        $rowData = $this->get_category_masterfile($limit, $offset, $sortBy, $sortOrder, $search);
        $rowCount = $this->get_category_masterfile_count();

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }
    // end fnction

    // function to pull all data from database of ticketing system
    private function get_category_masterfile($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $search = null){
        $resultset = array();
        $filterFields = array("type", "name");
        $this->db->select("id,type,name,status");
        $this->db->from("gccticket.category");
        $this->db->where('is_archived', '0');
        if($search){
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
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $resultset = $query->result();
            if(!empty($search)){
                $this->core_layout->setEventLog("User searched `".$search."` on category datatable.","search", "success", "gccticket", "user");
            }
        }
        return $resultset;
    }

    private function get_category_masterfile_count($search = null){
        $filterFields = array("type", "name");
        $this->db->from("gccticket.category");
        $this->db->where('is_archived', '0');
        if($search){
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

    function addComment(){
        $post = $this->input->post();
        $data = array(
            "ticket_id"=>$post['ticket_id'],
            "comment"=>$post['comment'],
            "created_by"=>$this->user_data['emp_id'],
        );
        $insert = $this->db->insert('gccticket.comments', $data);
        return $insert;
    }

    function getComments($ticket_id){
        $this->db->select("*");
        $this->db->from("gccticket.comments");
        $this->db->where("ticket_id", $ticket_id);
        $this->db->order_by("created_at", "DESC");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
                $rs->created_by = $this->requested_by($rs->created_by);
                $rs->created_at = date("F j, Y, h:i:a", strtotime($rs->created_at));
            }

            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }

            return $data;
        } else {
            return null;
        }
    }
    
    function editTicketDetails($id){
        $result = array();
        $this->db->select(" a.*,b.id as did, b.description");
        $this->db->from("gccticket.ticket a");
        $this->db->join("gcchris.tbldepartments b", "a.department_id=b.id", "LEFT");
        $this->db->where("a.id", $id);
        $query = $this->db->get();
        $results = $query->row_array();
        $result['result'] = $query->row_array();
        // $result['requestor'] = $this->requested_by($results['created_by']);
        return $result;
    }

    function ticketDetails($id)
    {
        $result = array();
        $this->db->select("a.*, b.id as did , b.description, c.id AS emp_id, c.position, c.firstname, c.middlename, c.lastname, c.suffix");
        $this->db->from("gccticket.ticket a");
        $this->db->join("gcchris.tbldepartments b", "a.department_id=b.id", "LEFT");
        $this->db->join("gccmaster.tblemployees c", "a.performed_by=c.id", "LEFT");
        $this->db->where("a.id", $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;

                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->requested_by = $this->requested_by($rs->requestor);
                $rs->performed_by_det = $this->requested_by($rs->performed_by);
                $rs->department = $this->getDepartmentName($rs->department_id);
                $rs->category = $this->getCategoryLabel($rs->category);
                $rs->sub_category = $this->getCategoryLabel($rs->sub_category);
                $rs->status = $this->getCategoryLabel($rs->status);
                $rs->requested_date = date("M d, Y h:i:A", strtotime($rs->requested_date));
                
                
                $rs->category_id = $rs->category;
                $rs->sub_category_id = $rs->sub_category;
                $rs->department_id = $rs->department_id;
                $rs->performed_by_id = $rs->performed_by;
                $rs->status_id = $rs->status;
                $rs->severity_id = $rs->priority;
                //$rs->picture_exist = file_exists(realpath("uploads/files/images/employee_files/" . $rs->picture));

                $arrData[$key] = $rs;
            }
            return $arrData[0];
        } else {
            return array();
        }
    }

    function requested_by($id)
    {
        $this->db->select("a.firstname, a.middlename, a.lastname, a.suffix, a.id");
        $this->db->from("gccmaster.tblemployees a");
        $this->db->join("gccmaster.tblusers b","b.emp_id=a.id");
        $this->db->where("a.id", $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
            }
            return $rs->display_name;
        } else {
            return "Not set";
        }
    }

    public function getCategoryLabel($id){
        if($id){
            return $this->db->get_where("gccticket.category", array("name"=>$id))->row('name');
        }else{
            return "Not set";
        }
    }

    public function getDepartmentName($id){
        return $this->db->get_where("gcchris.tbldepartments", array("id"=>$id))->row('description');
    }

    public function getPerformedByCollection(){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT  c.id, CONCAT(c.firstname,' ',c.lastname) as emp_name FROM gccmaster.tblusers b, gccmaster.tblemployees c WHERE b.emp_id=c.id AND b.group_id='1' AND c.employee_status = 'Active' AND b.group_id=1 AND (CONCAT(c.firstname,' ',c.lastname) LIKE '%{$get['q']}%') ");
        } else {
            $query = $this->db->query("SELECT  c.id, CONCAT(c.firstname,' ',c.lastname) as emp_name FROM gccmaster.tblusers b, gccmaster.tblemployees c WHERE b.emp_id=c.id AND b.group_id='1' AND c.employee_status = 'Active' AND b.group_id=1");
        }
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["emp_name"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    // function for uploading photo
    function uploadTicketPhoto(){
        $post = $this->input->post();
        $resultset = array();
        $employeeId = $this->user_data['emp_id'];
        if ($employeeId) {

            $imagesPath = "./uploads/files/images/employee_files/empcode_{$employeeId}/ticketing";

            $createFilePath = false;

            if (!file_exists($imagesPath)) {
                $mkdir = mkdir($imagesPath, 0777, true);
                if ($mkdir) {
                    $createFilePath = true;
                }
            } else {
                $createFilePath = true;
            }

            if ($createFilePath == false) {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                $resultset["toastr_state"] = "warning";
            } else {
                $config = array();
                $config['upload_path'] = $imagesPath;
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG|docx|pdf';
                $config['max_size'] = 100000;
                $config['create_thumbnail'] = true;

                $session = $this->core_layout->getCurrentSession();
                $data = $this->file_upload->uploadFile($config);

                if ($data["response"] == true) {
                    $files = $data["files"][0];
                    $filename = $files["file_name"];
                    $ext = explode(".", $filename);

                    $icon = base_url('assets/images/file_icons/jpg.svg');
                    if ($filename) {
                        if(isset($post['ticket_id'])){
                            $this->db->reset_query();
                            $ticketData = $this->db->select("attachment")->where('id',  $post['ticket_id'] )->get('gccticket.ticket')->row();
                            $this->db->reset_query();
                            $attachments = explode(',', $ticketData->attachment);
                            array_push($attachments, "empcode_{$employeeId}/ticketing/{$filename}");
                            $updated_attachment = implode(',', array_filter(array_unique($attachments)));
                            $this->db->where('id', $post['ticket_id'])->update('gccticket.ticket', array('attachment' => $updated_attachment));
                        }
                        $resultset["response"] = true;
                        $resultset["added_image"] = base_url("uploads/files/images/employee_files/empcode_{$employeeId}/ticketing/{$filename}");
                        $resultset["icon"] = $icon;
                        $resultset["render_image"] = "empcode_{$employeeId}/ticketing/" . $filename;
                        $resultset["toastr_msg"] = "Upload image successful.";
                        $resultset["display_filename"] = $filename;
                        $resultset["file_extension"] = $ext[1];
                        $resultset["toastr_state"] = "success";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Image upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Image upload failed!";
                    $resultset["toastr_state"] = "error";
                }
            }
        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "User data not found!";
            $resultset["toastr_state"] = "error";
        }
        return $resultset;
    }
    // end function

    function addCategory(){
        $post = $this->input->post();
        $insert = $this->db->insert('gccticket.category', $post);

        if($insert){
            $this->core_layout->setEventLog("User added category `".$post['name']."` with a type of `".$post['type']."` on category datatable.","insert", "success", "gccticket", "user");
        }else{
            $this->core_layout->setEventLog("User failed to add category `".$post['name']."` with a type of `".$post['type']."` on category datatable.","insert", "error", "gccticket", "system");
        }
        return $insert;
    }

    function getCategoryModalContent($content="add"){
		$resultset = array();
		$html = "";
        $arrData = array();
		if($content == "add"){
			$html = $this->load->view("ticket/modals/add_ticket", null, true);
        }
		if($content == "edit"){
			$post = $this->input->post();
			if(isset($post) && $post){
				unset($post["csrf_token"]);
				$category = $this->db->get_where("gccticket.category", $post);
				if($category->num_rows() == 1){
					$arrData = $category->row();
				}
			}
			$html = $this->load->view("ticket/modals/edit_ticket", array("data"=>$arrData), true);
		}
		if($html){
			$resultset["response"] = true;
			$resultset["html"] = $html;
			$resultset["data"] = $arrData;
		}else{
			$resultset["response"] = false;
		}
		return $resultset;
    }

    function updateModalCategory(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			$id = $post["id"];
            unset($post["csrf_token"], $post["id"]);
			$update = $this->db->update('gccticket.category', $post, array("id"=>$id));
			if($update){
                $this->core_layout->setEventLog("User updated category with a db id of `".$id."` on category datatable.","update", "success", "gccticket", "user");
				$resultset["response"] = true;
                $resultset["toastr_msg"] = "Update Successfully!";
			}else{
                $this->core_layout->setEventLog("User failed to update category with a db id of `".$id."` on category datatable.","update", "error", "gccticket", "system");
				$resultset["response"] = true;
                $resultset["toastr_msg"] = "Failed updating category data";
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			// $this->core_layout->setEventLog("Position masterfile - Error, No post data found.","update", "error", "gcchris", "user");
		}
        
        return $resultset;
    }

    function getCategories($type){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT  `id`, `name` FROM gccticket.category WHERE `name` LIKE '%{$get['q']}%' AND `type` = '$type' AND `status` = 0 AND `is_archived` = 0");
        } else {
            $query = $this->db->query("SELECT  `id`, `name` FROM gccticket.category WHERE `type` = '$type' AND `status` = 0 AND `is_archived` = 0");
        }
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["name"];
                $data["text"] = $_query["name"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    function getDepartments(){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT  `id`, `description` FROM gcchris.tbldepartments WHERE `description` LIKE '%{$get['q']}%' ORDER BY description ASC");
        } else {
            $query = $this->db->query("SELECT  `id`, `description` FROM gcchris.tbldepartments WHERE `description` != '' ORDER BY `description` ASC");
        }
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["description"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    function saveTicket(){
        $post = $this->input->post();
        if(isset($post['sub_category'])){
            $sub_category = $this->input->post('sub_category');
        }else{
            $sub_category = "";
        }
        $requested_date = date('Y-m-d H:i', strtotime($post['date_required']));
        $date = date('Y-m-d H:i:s');
        $year = substr($date, 2, 2);
        $month = substr($date, 5, 2);
        $list = $this->ticket->series($year, $month);
        $series = '';
        if (sizeof($list) > 0) {
            foreach ($list as $arr) {
                $x = $arr->ref_series;
            }
            $series = intval($x) + 1;
            if (strlen($series) == 1) {
                $series = '000' . $series;
            } else if (strlen($series) == 2) {
                $series = '00' . $series;
            } else if (strlen($series) == 3) {
                $series = '0' . $series;
            } else {
                $series = $series;
            }
        } else {
            $series = '0001';
        }

        $employeeId = $this->user_data['emp_id'];

        $str_pic = implode(",",$this->input->post('pic'));
        $arr_pic = explode(",",$str_pic);
    
        foreach($arr_pic as $img){
            $img_arr[] = "empcode_{$employeeId}/ticketing/".$img;
        }

        $reference_no = 'TS' . $year . '-' . $month . '-' . $series;
        $data = array(
            'ref_yr' => $year,
            'ref_series' => $series,
            'ref_month' => $month,
            'category' => $this->input->post('category'),
            'sub_category' => $sub_category,
            'reference_no' => $reference_no,
            'department_id' => $this->input->post('department'),
            'message' => $this->input->post('issue'),
            'requestor' => $this->user_data['emp_id'],
            'requested_date' => $requested_date,
            'attachment' => implode(",",$img_arr),
            'priority' => 'low',
            'status' => 'open',
            'created_at' => $date
        );
        $result = $this->db->insert('gccticket.ticket', $data);
        $last_id = $this->db->insert_id();
        if ($last_id) {
            $this->email_send($last_id, $this->input->post('category'), $this->input->post('issue'), $requested_date, $sub_category, $this->user_data['emp_id'], $date, "Open", "", "", $reference_no);
            $this->sendTelegram($data, $last_id);
        }

        if($result){
            $this->core_layout->setEventLog("User added ticket with Ref. No. `".$reference_no."` on category datatable.","insert", "success", "gccticket", "user");
        }else{
            $this->core_layout->setEventLog("User failed to add ticket with Ref. No. `".$reference_no."` on category datatable.","insert", "failed", "gccticket", "system");
        }
        return $result;
    }

    public function series($year, $month){
        $this->db->select('ref_series');
        $this->db->from('gccticket.ticket');
        $this->db->where('ref_yr', $year);
        $this->db->where('ref_month', $month);
        $this->db->order_by('ref_series', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    function updateTicket($id){
        $post = $this->input->post();
        if(isset($post['sub_category'])){
            $sub_category = $post['sub_category'];
        }else{
            $sub_category = 0;
        }
        $requested_date = date('Y-m-d H:i', strtotime($post['date_required']));
        $employeeId = $this->user_data['emp_id'];
        $date = date('Y-m-d H:i:s');

        $str_pic = implode(",",$this->input->post('pic'));
        $arr_pic = explode(",",$str_pic);
        
        // $img_arr = array();
        // foreach($arr_pic as $img){
        //     $img_arr[] = "empcode_{$employeeId}/ticketing/".$img;
        // }
        
        $data = array(
            'department_id' => $post['department'],
            'requested_date' => $requested_date,
            'category' => $post['category'],
            'sub_category' => $sub_category,
            'message' => $post['issue'],
            // 'attachment' => implode(",",$img_arr),
            'priority' => $post['severity'],
            'performed_by' => $post['performed_by'],
            'status' => $post['status']
        );
        if(empty($str_pic)){
            $data['attachment'] = "";
        }
        
        if ($id) {
            $this->db->where('id', $id);
            $update = $this->db->update('gccticket.ticket', $data);
            if($update){
                $this->core_layout->setEventLog("User updated ticket with db id of `".$id."` on ticket datatable.","update", "success", "gccticket", "user");
            }else{
                $this->core_layout->setEventLog("User failed to update ticket with db id of `".$id."` on ticket datatable.","update", "error", "gccticket", "system");
            }
            return $update;
        }
    }

    function removeFile(){
        $post = $this->input->post();
        $result = array();
        $id = $post['requested_id'];
        $ticket = $post['ticket_id'];
        $file = $post['filename'];
        $url = realpath("uploads/files/images/employee_files/empcode_".$id."/ticketing/".$file);
        
        if(!file_exists($url)){
            $result['result'] = "true";
        }
        else{
            $ticketData = $this->db->select("attachment")->where('id', $ticket)->get('gccticket.ticket')->row();
            $this->db->reset_query();
            $update = explode(',',$ticketData->attachment);
            if(in_array($file, $update)){
                $new_update = array_diff($update, array($file));
                $data  = implode(',',$new_update);
                $this->db->where('id', $ticket)->update('gccticket.ticket', array('attachment' => $data));
                unlink($url);
            }
            $result["result"] = "true";
            $result["file"] = $file;
        }
        return $result;
    }

    function deleteTicket($id){
        $this->db->where('id', $id);
        $data = array(
            'is_archived' => 1
        );
        $query = $this->db->update('gccticket.ticket', $data);
        return $query;
    }

    function restoreTicket($id){
        $this->db->where('id', $id);
        $data = array(
            'is_archived' => 0,
            'status' => 'open'
        );
        $query = $this->db->update('gccticket.ticket', $data);
        return $query;
    }

    function ticket_restore_multiple() {
        $multiple_id = $this->input->post('multiple_id');
        $multiple_id_arr = explode(",", $multiple_id);
        $resultSet = array();
        $this->db->where_in("id", $multiple_id_arr);

        $output = '';
        foreach($multiple_id_arr as $id){
            $output .= $id.', ';
        }
        if ($this->db->update("gccticket.ticket", array("is_archived" => "0", 'status' => 'open'))) {
            $this->core_layout->setEventLog("User restored db id `".$output."` in ticket masterfile datatable.","restore", "success", "gccticket", "user");
            $resultSet["success"] = true;
            $resultSet["message"] = "Tickets was restored back to master file.";
        } else {
            $this->core_layout->setEventLog("User failed to restore db id `".$output."` in ticket masterfile datatable.","restore", "error", "gccticket", "system");
            $resultSet["success"] = false;
            $resultSet["message"] = $this->db->error();
        }

        return $resultSet;
    }
    
    function deleteCategory($id){
        $data = array(
            'is_archived' => 1
        );
        $this->db->where('id', $id);
        $query = $this->db->update('gccticket.category', $data);
        //$query = $this->db->query("DELETE FROM gccticket.category WHERE id=$id");

        if($query){
            $this->core_layout->setEventLog("User archived category with a db id of `".$id."` on category datatable.","archive", "success", "gccticket", "user");
        }else{
            $this->core_layout->setEventLog("User failed to archive category with a db id of `".$id."` on category datatable.","archive", "error", "gccticket", "system");
        }

        return $query;
    }

    function restoreCategory($id){
        $data = array(
            'is_archived' => 0
        );
        $this->db->where('id', $id);
        $query = $this->db->update('gccticket.category', $data);
        //$query = $this->db->query("DELETE FROM gccticket.category WHERE id=$id");

        if($query){
            $this->core_layout->setEventLog("User restored category with a db id of `".$id."` on category datatable.","restore", "success", "gccticket", "user");
        }else{
            $this->core_layout->setEventLog("User failed to restore category with a db id of `".$id."` on category datatable.","restore", "error", "gccticket", "system");
        }

        return $query;
    }

    function allTickets(){
        $this->db->select("count(id) as count, priority");
        $this->db->from("gccticket.ticket");
        $this->db->where("status !=", "completed");
        $this->db->group_by("priority");
        $query = $this->db->get();
        $result = array();
        foreach($query->result() as $data){
            $result[] = $data;
            
        }

        $this->db->select("*");
        $this->db->from("gccticket.ticket");
        $this->db->where("status !=", "completed");
        $count = $this->db->get();
        $result['count'] = $count->num_rows();
        
        if($query->num_rows() > 0){
            return $result;
        }else{
            return "Not set";
        }
    }

    function allStatus(){
        $this->db->select("count(id) as count, status");
        $this->db->from("gccticket.ticket");
        $this->db->group_by("status");
        $query = $this->db->get();
        $result = array();
        foreach($query->result() as $data){
            $result[] = $data;
            $result['count'] = count($query->result());
        }
        if($query->num_rows() > 0){
            return $result;
        }else{
            return "Not set";
        }
    }

    function allCategory(){
        $this->db->select("count(id) as count, category");
        $this->db->from("gccticket.ticket");
        $this->db->group_by("category");
        $query = $this->db->get();
        $result = array();
        foreach($query->result() as $data){
            $result[] = $data;
            $result['count'] = count($query->result());
        }
        if($query->num_rows() > 0){
            return $result;
        }else{
            return "Not set";
        }
    }

    function allSubcategory(){
        $this->db->select("count(id) as count, sub_category");
        $this->db->from("gccticket.ticket");
        $this->db->group_by("sub_category");
        $query = $this->db->get();
        $result = array();
        foreach($query->result() as $data){
            $result[] = $data;
            $result['count'] = count($query->result());
        }
        if($query->num_rows() > 0){
            return $result;
        }else{
            return false;
        }
    }

    function email_send($id, $type, $issue, $need_dt, $module = null, $req, $date, $status, $remarks, $onhold, $reference_no)
    {
        $if_exist = $this->db->get_where("gccticket.ticket", array("id"=>$id))->row();
        $recipient = $this->db->get_where("gccmaster.tblusers", array("emp_id"=>$if_exist->requestor))->row();
        if(count((array)$if_exist) > 0){
            $title = "Ticket Update - {$type}";
        }else{
            $title = "New Ticket - {$type}";
        }
        
        $req = mb_strtoupper($req);

        //$emailTo = $this->sendEmailCompanyTo($companyTo);
        //$emailTo = ($emailTo)? $emailTo: "busysanalyst@gccph.com";
        $data = array("id" => $id, "employee_name" => $req, "type" => $type, "issue" => $issue, "need_dt"=>$need_dt,"module" => $module, "date" => $date, "status" => $status,  "remarks" => $remarks, "onhold"=> $onhold, "reference_no" => $reference_no);
        $message = "";
        $message .= $this->load->view("ticket/email_template/email-ticketing", $data, true);

        $module = "new_ticketing";
        $email_title = "Ticketing";
        $content_title = $title;
        $content = $message;
        $overrideMailer = array();
        if(isset($if_exist->id) && $if_exist->status != "Open"){
            $overrideMailer["send_to"] = array($recipient->email);
        }else{
            $overrideMailer["send_to"] = null;
        }
        
        // var_dump($if_exist->status);
        // var_dump(isset($if_exist->id));
        // var_dump($overrideMailer["send_to"]);
        $overrideMailer["email_user"] = "gcceforms@gmail.com";
        $overrideMailer["email_pass"] = "Sc0t2366";
        
        $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
        if ($sent) {
            $resultset["status"] = true;
        } else {
            $resultset["status"] = false;
        }
        return $resultset;
    }

    public function sendTelegram($data = null, $id){
        $telegram_msg = "";

        if($data){
            $requestor = $this->core_layout->getEmployeeData($data['requestor']);
            $sub_category = $data['sub_category'] || $data['sub_category'] != 0 ? ' - '.$this->getCategoryLabel($data['sub_category']) : "";
            $category = $this->getCategoryLabel($data['category']) . $sub_category;

            $telegram_msg .= '<b>Reference #</b>: '.strtoupper($data['reference_no']).chr(10);
            $telegram_msg .= '<b>Priority</b>: '.strtoupper($data['priority']).chr(10);
            $telegram_msg .= '<b>Category</b>: '.strtoupper($category).chr(10);
            $telegram_msg .= '<b>Issue</b>: '.strtoupper($data['message']).chr(10);
            $telegram_msg .= '<b>Requested By</b>: '.strtoupper($requestor['display_name_1']).chr(10);
            $telegram_msg .= '<b>Date Needed</b>: '.strtoupper($data['requested_date']).chr(10);
        }

		if($this->telegram_config_if_exist('new_ticket', 'count') > 0){

            $inline_keyboard = [
                [
                    [
                        "text" => "View Ticket",
                        // "url" => 'http://152.69.208.158/web/ticket/ticket/edit_ticket?id=427' //doesnt send message when in development or in local
                        "url" => base_url('ticket/ticket/edit_ticket?id=').$id
                    ]
                ]
            ];

            $reply_markup = [
                "inline_keyboard" => $inline_keyboard
            ];

			$this->telegram($telegram_msg, $reply_markup);
		}

		return $telegram_msg;
	}

    public function telegram_config_if_exist($module, $data){
		$this->db->where("module",$module);
		$telegram_details = $this->db->get("gccticket.telegram_config");
		$details = $telegram_details->row();
		$count = $telegram_details->num_rows();
		if($data == 'count'){
			return $count;
		}else{
			return $details;
		}
	}

    public function telegram($msg, $reply_markup){
		try {
			$data = $this->telegram_config_if_exist('new_ticket', 'data');
			if($data){

				$telegrambot=$data->telegram_bot_token;
				$telegramchatid= $data->chat_id;
				$url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';
                $data=array('chat_id'=>$telegramchatid,'text'=>$msg, 'reply_markup' => json_encode($reply_markup), 'parse_mode'=>'HTML'); // replay_markup send external links
				$options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),'ignore_errors'=>true),);
				$context=stream_context_create($options);
				$result=file_get_contents($url,false,$context);
				return $result;
			}else{
				return false;
			}
			
		} catch (Exception $e) {
			return false;
		}
	}

    public function select2DepartmentData(){
        $this->db->select('id, description AS text');
        $this->db->from('gcchris.tbldepartments');
        $this->db->where('description !=', '');
        $this->db->order_by('description', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function select2CategoryData($type){
        $this->db->select("cat.name as text, cat.name as id");
        $this->db->from($this->category. ' as cat');
        $this->db->where('cat.type', $type);
        $this->db->order_by("id", "ASC");
        $results = $this->db->get()->result();
        return $results;
    }

    public function select2PerformedByData(){
        $query = $this->db->query("SELECT  c.id, CONCAT(c.firstname,' ',c.lastname) as emp_name FROM gccmaster.tblusers b, gccmaster.tblemployees c WHERE b.emp_id=c.id AND b.group_id='1' AND c.employee_status = 'Active' AND b.group_id=1");
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["emp_name"];
                $resultarray[] = $data;
            }
        }
        return  $resultarray;
    }

    function removeActionstkn(){
        $post = $this->input->post();
        $id = $post['id'];
        $comment = $this->db->select("comment")->get_where("gccticket.comments", array("id"=>$id))->row();
        $delete = $this->db->query("DELETE FROM gccticket.comments WHERE id = ?", $id);
        if ($delete){
            $this->core_layout->setEventLog("User deleted comment: {$comment->comment}","search", "success", "gccticket", "user");
        }
        return $delete;
      }

}