<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket_m extends CI_Model
{
    private $user_data = array();
    protected $tickets = "gccticket";
    private $current_actions =  array();

    private $departmentTable = "gcchris.tbldepartments";
    private $category = "gccticket.category";

    public function __construct(){
        parent::__construct();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model", "dt_model");
        $this->user_data = $this->session->userdata("logged_in");
        $this->load->model("core/upload_model", "file_upload");
        $this->current_actions = $this->core_layout->getCurrentActions();
    }

    //function to display all ticketing entries
    function ticketMasterfile($params = null)
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
        $payroll =  (in_array("payroll_ticket", $this->core_layout->getCurrentActions())) ? true : false;
        $rowData = $this->get_ticket_masterfile($limit, $offset, $sortBy, $sortOrder, $search, $query_builder, $view_own_request, $payroll, $params);
        $rowCount = $this->get_ticket_masterfile_count($search, $query_builder, $view_own_request, $payroll, $params);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    public function get_ticket_masterfile($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $search = null, $query_builder = null, $view_own_request, $payroll, $params){
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
        if($params){
            $allowed_fields = ['priority', 'status', 'category'];
            foreach($params as $field => $value) {
                if(in_array($field, $allowed_fields)) {
                    $this->db->where("LOWER(a.$field)", strtolower($value));
                }
            }
        }
        $current_user_id = $this->user_data['emp_id'];
        if($payroll) {
            $this->db->where('cat.name', 'payroll');
        }
        if($view_own_request) {
            if($payroll) {
                $this->db->or_where('a.requestor', $current_user_id);
            } else {
                $this->db->where('a.requestor', $current_user_id);
            }
        }
        $this->db->where('a.is_archived', '0');
        // $this->db->where("LOWER(a.status) != 'cancelled'");
        if ($query_builder) {
            $lower_query = strtolower($query_builder);
            if (
                strpos($lower_query, 'c.firstname') !== false &&
                strpos($lower_query, 'c.lastname') !== false &&
                strpos($lower_query, '%not set%') !== false
            ) {
                $this->db->where('a.performed_by', '0');
            } else {
                $this->db->where($query_builder);
            }
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
        else if($sortBy[$i]['data'] == "performed_by"){
            $this->db->order_by("c.firstname", $sortOrder[0]['dir']);
        }
        else{
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }
        $this->db->group_by("a.id");
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
    
    private function get_ticket_masterfile_count($search = null, $query_builder = null, $view_own_request, $payroll, $params){
        $filterFields = array("a.reference_no",'a.message', 'b.firstname', 'b.middlename', 'b.lastname', 'c.firstname', 'c.middlename', 'c.lastname','cat.name','sub.name','prio.name','stat.name');
        $this->db->from("gccticket.ticket as a");
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.requestor", 'LEFT');
        $this->db->join("gccmaster.tblemployees as c", "c.id = a.performed_by", 'LEFT');
        $this->db->join("gccticket.category as cat" , "cat.name = a.category", 'LEFT');
        $this->db->join("gccticket.category as sub" , "sub.name = a.sub_category", 'LEFT');
        $this->db->join("gccticket.category as prio" , "prio.name = a.priority", 'LEFT');
        $this->db->join("gccticket.category as stat" , "stat.name = a.status", 'LEFT');
        $this->db->where('a.is_archived', '0');
        if($params){
            $allowed_fields = ['priority', 'status', 'category'];
            foreach($params as $field => $value) {
                if(in_array($field, $allowed_fields)) {
                    $this->db->where("LOWER(a.$field)", strtolower($value));
                }
            }
        }
        // $this->db->where("LOWER(a.status) != 'cancelled'", NULL, FALSE);
        $current_user_id = $this->user_data['emp_id']; 
        if($payroll) {
            $this->db->where('cat.name', 'payroll');
        }
        if($view_own_request) {
            if($payroll) {
                $this->db->or_where('a.requestor', $current_user_id);
            } else {
                $this->db->where('a.requestor', $current_user_id);
            }
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
        $this->db->group_by("a.id");
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
        // $this->db->or_where('a.status', "cancelled");
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
        $this->db->group_by("a.id");
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
        // $this->db->or_where('a.status', "cancelled");
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
        $this->db->group_by("a.id");
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
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG|pdf|PDF';
                $config['max_size'] = 25600;
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
                    if($data['message']== "<p>The file you are attempting to upload is larger than the permitted size.</p>"){
                        $data['message'] ="<p>The file you are attempting to upload is larger than the permitted size. The maximum upload size is ". $config['max_size'] / 1024 . " MB</p>";
                    }
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = $data["message"];
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
        $resultArray = array();
        $post = $this->input->post();
        $check = $this->getExistingTicketPerUser();

        if(count($check) >= 5){
            return array("result" => false, "toastr_msg" => "You currently have ". count($check). " pending request(s). Please resolve them or reach out to the IT department for assistance before submitting a new one.");
        }

        if(isset($post['category']) && $post['category'] == "webportal"){
            if(isset($post['sub_category'])){
                $sub_category = $post['sub_category'];
            }
        }
        else{
            $sub_category = 0;
        }

        switch(strtolower($post['category'])) {
            case 'webportal':
            case 'website':
                $responsibility = "SOFTWARE DEVELOPMENT";
                break;
                
            case 'hardware':
            case 'outlook':
                $responsibility = "IT SUPPORT";
                break;
            case 'payroll':
                $responsibility = "PAYROLL";
                break;
                
            case 'software':
                $responsibility = $post['responsibility'];
                break;

            case 'inventory system':
                $responsibility = "SOFTWARE DEVELOPMENT";
                break;

            default:
                $responsibility = "IT SUPPORT";
                break;
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
            'priority' => $post['severity'],
            'status' => 'open',
            'created_at' => $date,
            'responsibility' => $responsibility
        );
        $result = $this->db->insert('gccticket.ticket', $data);
        $last_id = $this->db->insert_id();
        if ($last_id) {
            $this->email_send($last_id, $this->input->post('category'), $this->input->post('issue'), $requested_date, $sub_category, $this->user_data['emp_id'], $date, "Open", "", "", $reference_no);
            $this->sendTelegram($data, $last_id);
        }

        if($result){
            $this->core_layout->setEventLog("User added ticket with Ref. No. `".$reference_no."` on category datatable.","insert", "success", "gccticket", "user");
            $this->addTrailLog($last_id,"new");
            $resultArray['result'] = true;
            $resultArray['toastr_msg'] = "Ticket with Ref. No. `".$reference_no."` has been successfully added.";
        }else{
            $this->core_layout->setEventLog("User failed to add new ticket.","insert", "failed", "gccticket", "system");
            $resultArray['result'] = false;
            $resultArray['toastr_msg'] = "User failed to add new ticket";
        }
        return $resultArray;
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

        $currentTicketData = $this->getTicketByid($id);

        $performed_by = (isset($post["performed_by"]) && $post["performed_by"]) ? $post["performed_by"] : 0;
        if(isset($post['category']) && $post['category'] == "webportal"){
            if(isset($post['sub_category'])){
                $sub_category = $post['sub_category'];
            }
        }
        else{
            $sub_category = 0;
        }

        switch(strtolower($post['category'])) {
            case 'webportal':
            case 'website':
                $responsibility = "SOFTWARE DEVELOPMENT";
                break;
                
            case 'hardware':
            case 'outlook':
                $responsibility = "IT SUPPORT";
                break;

            case 'payroll':
                $responsibility = "PAYROLL";
                break;
                
            case 'software':
                $responsibility = $post['responsibility'];
                break;
            
            case 'inventory system':
                $responsibility = "SOFTWARE DEVELOPMENT";
                break;

            default:
                $responsibility = "IT SUPPORT";
                break;
        }

        $requested_date = date('Y-m-d H:i:s', strtotime($post['date_required']));

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
            'performed_by' => $performed_by,
            'status' => $post['status'],
            'responsibility' => $responsibility,
        );

        if(empty($str_pic)){
            $data['attachment'] = "";
        }
        
        if ($id) {
            $this->db->where('id', $id);
            $update = $this->db->update('gccticket.ticket', $data);
            if($update){
                $changes = $this->logChanges($currentTicketData, $data);
                $this->core_layout->setEventLog("User updated ticket $changes","update", "success", "gccticket", "user");
            }else{
                $this->core_layout->setEventLog("User failed to update ticket with db id of `".$id."` on ticket datatable.","update", "error", "gccticket", "system");
            }
            return $update;
        }
    }

    function removeFile(){
        $post = $this->input->post();
        $result = array();
        $id = isset($post['requested_id']) ? $post['requested_id'] : $this->user_data['emp_id'];
        $file = $post['filename'];
        $file_path = "uploads/files/images/employee_files/empcode_{$id}/ticketing/{$file}";
        $full_path = FCPATH . $file_path;

        if (isset($post['ticket_id'])) {
            $ticket = $post['ticket_id'];
            $ticketData = $this->db->select("attachment")->where('id', $ticket)->get('gccticket.ticket')->row();
            $this->db->reset_query();
            $update = explode(',', $ticketData->attachment);
            $file = "empcode_{$id}/ticketing/{$file}";
            if (in_array($file, $update)) {
                $new_update = array_diff($update, array($file));
                $data = implode(',', $new_update);
                $this->db->where('id', $ticket)->update('gccticket.ticket', array('attachment' => $data));
            }
        }
        $result["result"] = file_exists($full_path) ? unlink($full_path) : false;
        $result["file"] = $file;
        return $result;
    }

    function deleteTicket($id){
        $this->db->where('id', $id);
        $data = array(
            'is_archived' => 1,
        );
        $query = $this->db->update('gccticket.ticket', $data);
        $this->addTrailLog($id,"archive");
        return $query;
    }

    function restoreTicket($id){
        $this->db->where('id', $id);
        $data = array(
            'is_archived' => 0,
            'status' => 'open'
        );
        $query = $this->db->update('gccticket.ticket', $data);
        $this->addTrailLog($id,"restore");
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

    function allTickets() {
        $this->db->select("
            COUNT(*) as total, 
            COUNT(CASE WHEN LOWER(status) = 'open' THEN 1 END) as 'open', 
            COUNT(CASE WHEN LOWER(priority) = 'high' AND LOWER(status) = 'open' THEN 1 END) as high
        ");
        $this->db->from("gccticket.ticket as a");
        $this->db->where('is_archived', 0);
        // $this->db->where("LOWER(a.status) != 'cancelled'");
        $query = $this->db->get();
        $result = $query->row_array();
        
        $avg_completion_query = $this->db->query("
            SELECT AVG(TIMESTAMPDIFF(SECOND, new_logs.created_at, completed_logs.created_at)) as avg_seconds
            FROM gccticket.ticket t
            JOIN (
                SELECT ticket_id, created_at
                FROM gccticket.trail_logs_event
                WHERE type = 'new'
            ) new_logs ON t.id = new_logs.ticket_id
            JOIN (
                SELECT ticket_id, created_at
                FROM gccticket.trail_logs_event
                WHERE type = 'completed'
            ) completed_logs ON t.id = completed_logs.ticket_id
            WHERE t.status = 'completed' AND t.is_archived = 0
        ");
        
        $avg_result = $avg_completion_query->row_array();
        $avg_seconds = isset($avg_result['avg_seconds']) ? $avg_result['avg_seconds'] : 0;
        
        $days = floor($avg_seconds / 86400);
        $hours = floor(($avg_seconds % 86400) / 3600);
        $minutes = floor(($avg_seconds % 3600) / 60);
        $seconds = $avg_seconds % 60;
        
        $formatted_avg_time = '';
        
        if ($days > 0) {
            $formatted_avg_time .= $days . ($days == 1 ? "day " : "days ");
        }
        if ($hours > 0) {
            $formatted_avg_time .= $hours . ($hours == 1 ? "hr " : "hrs ");
        }
        if ($minutes > 0) {
            $formatted_avg_time .= $minutes . ($minutes == 1 ? "min " : "mins ");
        }
        if ($seconds > 0) {
            $formatted_avg_time .= $seconds . ($seconds == 1 ? "sec" : "secs");
        }
        
        if ($formatted_avg_time === '') {
            $formatted_avg_time = "INVALID";
        }
        
        // Trim trailing space
        $formatted_avg_time = rtrim($formatted_avg_time);

        $avg_response_query = $this->db->query("
            SELECT AVG(TIMESTAMPDIFF(SECOND, new_logs.created_at, completed_logs.created_at)) as avg_seconds
            FROM gccticket.ticket t
            JOIN (
                SELECT ticket_id, created_at
                FROM gccticket.trail_logs_event
                WHERE type = 'new'
            ) new_logs ON t.id = new_logs.ticket_id
            JOIN (
                SELECT ticket_id, created_at
                FROM gccticket.trail_logs_event
                WHERE type = 'in progress'
            ) completed_logs ON t.id = completed_logs.ticket_id
            WHERE t.status = 'in progress' AND t.is_archived = 0
        ");

        $avg_response_result = $avg_response_query->row_array();
        $avg_response_seconds = isset($avg_response_result['avg_seconds']) ? $avg_response_result['avg_seconds'] : 0;
        
        $response_days = floor($avg_response_seconds / 86400);
        $response_hours = floor(($avg_response_seconds % 86400) / 3600);
        $response_minutes = floor(($avg_response_seconds % 3600) / 60);
        $response_seconds = $avg_response_seconds % 60;
        
        $formatted_avg_response_time = '';
        
        if ($response_days > 0) {
            $formatted_avg_response_time .= $response_days . ($response_days == 1 ? "day " : "days ");
        }
        if ($response_hours > 0) {
            $formatted_avg_response_time .= $response_hours . ($response_hours == 1 ? "hr " : "hrs ");
        }
        if ($response_minutes > 0) {
            $formatted_avg_response_time .= $response_minutes . ($response_minutes == 1 ? "min " : "mins ");
        }
        if ($response_seconds > 0) {
            $formatted_avg_response_time .= $response_seconds . ($response_seconds == 1 ? "sec" : "secs");
        }
        
        if ($formatted_avg_response_time === '') {
            $formatted_avg_response_time = "INVALID";
        }
        
        $formatted_avg_response_time = rtrim($formatted_avg_response_time);
        
        $data = [
            "widget" => [
                "total" => $result['total'],
                "open" => $result['open'],
                "urgent" => $result['high'],
                "aveResolve" => $formatted_avg_time,
                "aveResponse" => $formatted_avg_response_time,
            ],
        ];
        
        return $data;
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
            $telegram_msg .= '<b>Department Responsible</b>: '.strtoupper($data['responsibility']).chr(10);
            $telegram_msg .= '<b>Issue</b>: '.strtoupper($data['message']).chr(10);
            $telegram_msg .= '<b>Requested By</b>: '.strtoupper($requestor['display_name_1']).chr(10);
            $telegram_msg .= '<b>Date Needed</b>: '.strtoupper($data['requested_date']).chr(10);
        }

        if ($data['responsibility'] == "PAYROLL") {
            $config_key = 'new_ticket_payroll';
        } else {
            $config_key = 'new_ticket';
        }
        
        if ($this->telegram_config_if_exist($config_key, 'count') > 0) {
            $inline_keyboard = [
                [
                    [
                        "text" => "View Ticket",
                        "url" => 'http://58.69.100.66/portaldev/ticket/ticket/edit_ticket?id='.$id //doesnt send message when in development or in local
                        // "url" => site_url('ticket/ticket/edit_ticket?id=') . $id
                    ],
                    [
                        "text" => "Serve Ticket",
                        "url" => 'http://58.69.100.66/portaldev/ticket/ticket/view_ticket?id='.$id.'&serve=true',
                        // "url" => site_url('ticket/ticket/view_ticket?id=') . $id.'&serve=true' 
                    ]
                ]
            ];
            $reply_markup = [
                "inline_keyboard" => $inline_keyboard
            ];
           $this->telegram($telegram_msg, $reply_markup, $config_key);
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

    public function telegram($msg, $reply_markup,$config_key){
		try {
			$data = $this->telegram_config_if_exist($config_key, 'data');
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
        $this->db->where('is_archived', 0);
        $this->db->order_by('description', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function select2CategoryData($type){
        $this->db->select("cat.name as text, cat.name as id");
        $this->db->from($this->category. ' as cat');
        $this->db->where('cat.type', $type);
        $this->db->where('cat.status', 0);
        if($type == 'sub-category'){
            $this->db->order_by("id", "ASC");
        }elseif ($type == 'severity' || $type == 'status' || $type == 'responsibility') {
            $this->db->order_by("cat.id", "ASC");
        }else{
            $this->db->order_by("cat.name", "ASC");
        }
        
        $results = $this->db->get()->result();
        return $results;
    }

    public function select2PerformedByData(){
        $query = $this->db->query("SELECT  c.id, CONCAT(c.firstname,' ',c.lastname) as emp_name FROM gccmaster.tblusers b, gccmaster.tblemployees c WHERE b.emp_id=c.id AND b.group_id='1' AND c.employee_status = 'Active' AND b.group_id=1 ORDER BY c.firstname ASC");
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

      public function select2PerformedByPayrollData() {
        $query = $this->db->query("SELECT c.id, CONCAT(c.firstname,' ',c.lastname) as emp_name 
                                  FROM gccmaster.tblusers b, gccmaster.tblemployees c 
                                  WHERE b.emp_id = c.id 
                                  AND c.employee_status = 'Active' 
                                  AND (b.role_id = 14 OR b.role_id = 124 OR b.role_id = 144) 
                                  ORDER BY c.firstname ASC");
        
        $resultarray = array();
        
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["emp_name"];
                $resultarray[] = $data;
            }
        }
        
        return $resultarray;
    }

    public function getDepartmentID(){
        $this->db->select('department_id');
        $this->db->from('gccmaster.tblemployees');
        $this->db->where('id', $this->user_data['emp_id']);
        $query = $this->db->get();
        return $query->row()->department_id;
    }

    public function addTrailLog($id,$type){
        if($type == "new"){
            $message = "Ticket created";
        }elseif($type == "in progress"){
            $message = "Ticket set to in progress";
        }elseif($type == "completed"){
            $message = "Ticket set to Completed";
        }elseif($type == "Cancelled"){
            $message = "Ticket cancelled";
        }elseif($type == "open"){
            $message = "Ticket set to open";
        }elseif($type == "restore"){
            $message = "Ticket Restored";
        }
        elseif($type == "archive"){
            $message = "Ticket archived";
        }
        elseif($type == "resolved"){
            $message = "Ticket resolved";
        }
        $post = array(
            'log_message' => $message,
            'user_id' => $this->user_data['emp_id'],
            'ticket_id' => $id,
            'type' => $type,
        );
        return $this->db->insert('gccticket.trail_logs_event', $post);
    }

    public function getTrailLog($id){
        $this->db->select("UPPER(a.log_message) as log_message, a.ticket_id as id, a.type,  UPPER(DATE_FORMAT(a.created_at, '%M %d, %Y %I:%i%p')) as created_at, UPPER(CONCAT(b.firstname,' ', b.lastname)) as name");
        $this->db->from('gccticket.trail_logs_event as a');
        $this->db->join('gccmaster.tblemployees as b', 'a.user_id = b.id', 'left');
        $this->db->where('a.ticket_id', $id);
        $this->db->order_by('a.id', 'desc');
        $query = $this->db->get();
        $data['data'] = $query->result();
        return $data;
    }

    private function getTicketByid($id){
        $this->db->select('a.id,a.requested_date,a.priority,a.category,a.sub_category,a.department_id,a.category,a.status,a.message,a.attachment,a.performed_by,a.reference_no');
        $this->db->from('gccticket.ticket as a');
        $this->db->where('a.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    private function getDepartmentById($id){
        $this->db->select("description");
        $this->db->from($this->departmentTable);
        $this->db->where('id', $id);
        $query = $this->db->get(); 
        $result = $query->row();
        $this->db->reset_query();
        return $result->description;
    }

    private function getEmployeeName($id){
        if($id == 0 || $id == "" || $id == null){
            return "No Assigned Name";
        }
        $this->db->select("id, firstname, middlename, lastname, suffix");
        $this->db->from("gccmaster.tblemployees");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $rs = $query->row();
        $tempRs = (array)$rs;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        return ($fullname['display_name_1']) ? $fullname['display_name_1'] : "No Assigned Name";
    }

    private function logChanges($currentData, $newData) {
        $changes = array();
        $changesString = '';
        if (is_object($currentData)) {
            $currentData = get_object_vars($currentData);
        }
        if (is_object($newData)) {
            $newData = get_object_vars($newData);
        }

        foreach ($currentData as $field => $value) {
            if (isset($newData[$field]) && $newData[$field]!= $value) {
                $changes[$field] = array(
                    'old' => $value,
                    'new' => $newData[$field]
                );
            }
        }
        foreach ($changes as $field => $change) {
            if($field == 'status'){
                $this->addTrailLog($currentData['id'],$change['new']);
            }
            if (strtolower($field) == 'department_id'){
                $changesString.= " Field: $field, from: <strong>". $this->getDepartmentById($change['old']). "</strong>, to: <strong>". $this->getDepartmentById($change['new']). "</strong>\n";
            }
            elseif (strtolower($field) == 'performed_by'){
                $changesString.= " Field: $field, from: <strong>". $this->getEmployeeName($change['old']). "</strong>, to: <strong>". $this->getEmployeeName($change['new']). "</strong>\n";
            }
            else{
                $changesString.= " Field: $field, from: <strong>". $change['old']. "</strong>, to: <strong>". $change['new']. "</strong>\n";
            }

        }
        return $changesString;
    }

      public function getOpenTickets() {
        $post = $this->input->post();
        $start_date = isset($post['start']) ? $post['start'] : date('Y-m-d');
        $end_date = isset($post['end']) ? $post['end'] : date('Y-m-d');
        $start_time = $start_date . ' 00:00:00';
        $end_time = $end_date . ' 23:59:59';
        $this->db->from("gccticket.ticket");
        $this->db->where("LOWER(status)", "open");
        $this->db->where("is_archived", 0);
        if(isset($post['all']) && $post['all'] == 'true'){
            return $this->db->count_all_results();
        }
        $this->db->where("created_at >= ", $start_time);
        $this->db->where("created_at <= ", $end_time);
        return $this->db->count_all_results();
    }

    public function getTotalTickets(){
        $post = $this->input->post();
        $start_date = isset($post['start']) ? $post['start'] : date('Y-m-d');
        $end_date = isset($post['end']) ? $post['end'] : date('Y-m-d');
        $start_time = $start_date . ' 00:00:00';
        $end_time = $end_date . ' 23:59:59';
        $this->db->from("gccticket.ticket");
        $this->db->where("is_archived", 0);
        // $this->db->where("LOWER(status) != 'cancelled'");
        if(isset($post['all']) && $post['all'] == 'true'){
            return $this->db->count_all_results();
        }
        $this->db->where("created_at >= ", $start_time);
        $this->db->where("created_at <= ", $end_time);
        return $this->db->count_all_results();
    }

    public function getUrgentTickets(){
        $post = $this->input->post();
        $start_date = isset($post['start']) ? $post['start'] : date('Y-m-d');
        $end_date = isset($post['end']) ? $post['end'] : date('Y-m-d');
        $start_time = $start_date . ' 00:00:00';
        $end_time = $end_date . ' 23:59:59';
        $this->db->from("gccticket.ticket");
        $this->db->where("LOWER(priority)", "high");
        $this->db->where("LOWER(status) !=", "completed");
        $this->db->where("LOWER(status) !=", "resolved");
        $this->db->where("LOWER(status) !=", "cancelled");
        $this->db->where("is_archived", 0);
        if(isset($post['all']) && $post['all'] == 'true'){
            return $this->db->count_all_results();
        }
        $this->db->where("created_at >= ", $start_time);
        $this->db->where("created_at <= ", $end_time);
        return $this->db->count_all_results();
    }

    public function getTotalPerStatus(){
        $post = $this->input->post();
        $start_date = isset($post['start']) ? $post['start'] : date('Y-m-d');
        $end_date = isset($post['end']) ? $post['end'] : date('Y-m-d');
        $start_time = $start_date . ' 00:00:00';
        $end_time = $end_date . ' 23:59:59';
        $this->db->select("
            COUNT(*) as total, 
            COUNT(CASE WHEN LOWER(status) = 'open' THEN 1 END) as 'open', 
            COUNT(CASE WHEN LOWER(status) = 'in progress' THEN 1 END) as 'in progress', 
            COUNT(CASE WHEN LOWER(status) = 'completed' THEN 1 END) as completed, 
            COUNT(CASE WHEN LOWER(status) = 'cancelled' THEN 1 END) as 'cancelled', 
         ");
        $this->db->from("gccticket.ticket");
        $this->db->where("is_archived", 0);
        if(isset($post['start']) && $post['start'] && isset($post['end']) && $post['end']){
            $this->db->where("created_at >= ", $start_time);
            $this->db->where("created_at <= ", $end_time);
        }

        $query = $this->db->get();
        $result = $query->row_array();
        $data =  ["status" => [
            "Open" => $result['open'],
            "In progress" => $result['in progress'],
            "Completed" => $result['completed'],
            "Cancelled" => $result['cancelled']
        ],];
        return $data;
    }

    public function getTotalPerCategory() {
        $post = $this->input->post();
        $start_date = isset($post['start']) ? $post['start'] : date('Y-m-d');
        $end_date = isset($post['end']) ? $post['end'] : date('Y-m-d');
        $start_time = $start_date . ' 00:00:00';
        $end_time = $end_date . ' 23:59:59';
        
        $this->db->select('name');
        $this->db->where('type', 'category');
        $this->db->where('status', 0);
        $query = $this->db->get('gccticket.category');
        $categories = $query->result_array();
        $this->db->reset_query();
        
        $categoryCounts = array();
        
        foreach ($categories as $category) {
            $this->db->where('category', $category['name']);
            $this->db->where("is_archived", 0);
            $this->db->where("LOWER(status) != 'cancelled'");
            if(isset($post['start']) && $post['start'] && isset($post['end']) && $post['end']) {
                $this->db->where("created_at >= ", $start_time);
                $this->db->where("created_at <= ", $end_time);
            }
            $count = $this->db->count_all_results('gccticket.ticket');
            $categoryCounts[$category['name']] = (string)$count;
            
            $this->db->reset_query();
        }
        return array('categories' => $categoryCounts);
    }

    public function getTotalPerPriority() {
        $post = $this->input->post();
        $start_date = isset($post['start']) ? $post['start'] : date('Y-m-d');
        $end_date = isset($post['end']) ? $post['end'] : date('Y-m-d');
        $start_time = $start_date . ' 00:00:00';
        $end_time = $end_date . ' 23:59:59';

        $this->db->select("
            COUNT(*) as total, 
            COUNT(CASE WHEN priority = 'low' THEN 1 END) as 'low', 
            COUNT(CASE WHEN priority = 'medium' THEN 1 END) as 'medium', 
            COUNT(CASE WHEN priority = 'high' THEN 1 END) as high, 
         ");
        $this->db->from("gccticket.ticket");
        $this->db->where("is_archived", 0);
        // $this->db->where("status", "open");
        if(isset($post['start']) && $post['start'] && isset($post['end']) && $post['end']){
            $this->db->where("created_at >= ", $start_time);
            $this->db->where("created_at <= ", $end_time);
        }
    
        $query = $this->db->get();
        $result = $query->row_array();
        return ["priorities" => [
            "Low" => $result['low'],
            "Medium" => $result['medium'],
            "High" => $result['high'],
        ]];
    }

    public function getTotalByAssignee() {
        $post = $this->input->post();
        $start_date = isset($post['start']) ? $post['start'] : date('Y-m-d');
        $end_date = isset($post['end']) ? $post['end'] : date('Y-m-d');
        $start_time = $start_date . ' 00:00:00';
        $end_time = $end_date . ' 23:59:59';
    
        $this->db->select("a.performed_by, 
        COUNT(*) as ticket_count, 
        IF(a.performed_by = 0, 'Unassigned', CONCAT(
            b.firstname, ' ', b.lastname,
            IF(
                (b.suffix IS NOT NULL AND
                LOWER(b.suffix) NOT IN ('n/a', 'none') AND
                b.suffix != ''),
                CONCAT(' ', b.suffix),
                ''
            )
        )) as name,
        SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN a.status = 'open' THEN 1 ELSE 0 END) as open,
        SUM(CASE WHEN a.status = 'in progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN a.status = 'resolved' THEN 1 ELSE 0 END) as resolved");
        $this->db->from("gccticket.ticket as a");
        $this->db->join("gccmaster.tblemployees as b", "b.id = a.performed_by", "LEFT");
        $this->db->where("a.is_archived", 0);
        // $this->db->where("a.status !=", "completed");
        $this->db->where("a.status !=", "cancelled");
        // $this->db->where("a.status !=", "resolved");
        $this->db->where("(b.employee_status = 'Active' OR a.performed_by = 0)");
    
        if (isset($post['start']) && $post['start'] && isset($post['end']) && $post['end']) {
            $this->db->where("a.created_at >= ", $start_time);
            $this->db->where("a.created_at <= ", $end_time);
        }
    
        $this->db->group_by("a.performed_by");
        $query = $this->db->get();
        $result = $query->result_array();
    
        $employee_tickets = [];
        foreach ($result as $row) {
            $employee_tickets[ucwords(strtolower($row['name']))] = ["total" => $row['ticket_count'], "completed" => $row['completed'], "open" => $row['open'], "in_progress" => $row['in_progress'], "resolved" => $row['resolved']];
        }
        return ["assigned" => $employee_tickets];
    }

    public function getCompletionRate() {
        $post = $this->input->post();
        $start_date = isset($post['start']) ? $post['start'] : date('Y-m-d');
        $end_date = isset($post['end']) ? $post['end'] : date('Y-m-d');
        $start_time = $start_date . ' 00:00:00';
        $end_time = $end_date . ' 23:59:59';

        $this->db->select("COUNT(*) as total, SUM(CASE WHEN status = 'completed' OR status = 'resolved' THEN 1 ELSE 0 END) as completed");
        $this->db->from("gccticket.ticket");
        $this->db->where("is_archived", 0);

        if (isset($post['start']) && $post['start'] && isset($post['end']) && $post['end']) {
            $this->db->where("created_at >= ", $start_time);
            $this->db->where("created_at <= ", $end_time);
        }

        $result = $this->db->get()->row();
        
        return ($result->total > 0) 
            ? round(($result->completed / $result->total) * 100, 2)
            : 0.00;
    }

    public function getAverageResolveTime(){
        $post = $this->input->post();
        $start_date = isset($post['start']) ? $post['start'] : date('Y-m-d');
        $end_date = isset($post['end']) ? $post['end'] : date('Y-m-d');
        $this->db->select('AVG(TIMESTAMPDIFF(SECOND, nl.created_at, ip.created_at)) as avg_seconds');
        $this->db->from('gccticket.ticket t');
        $this->db->join('(SELECT ticket_id, created_at FROM gccticket.trail_logs_event WHERE type = "new") nl', 't.id = nl.ticket_id');
        $this->db->join('(SELECT ticket_id, created_at FROM gccticket.trail_logs_event WHERE type = "completed") ip', 't.id = ip.ticket_id');
        $this->db->where('t.status', 'completed');
        $this->db->where('t.is_archived', 0);

        if(!isset($post['all']) || !$post['all'] == 'true'){
            $this->db->where('t.created_at >=', $start_date . ' 00:00:00');
            $this->db->where('t.created_at <=', $end_date . ' 23:59:59');
        }
        $avg_response_result = $this->db->get();
        $avg_result = $avg_response_result->row_array();
        $avg_seconds = isset($avg_result['avg_seconds']) ? $avg_result['avg_seconds'] : 0;
        
        $days = floor($avg_seconds / 86400);
        $hours = floor(($avg_seconds % 86400) / 3600);
        $minutes = floor(($avg_seconds % 3600) / 60);
        $seconds = $avg_seconds % 60;
        
        $formatted_avg_time = '';
           
        if ($days > 0) {
            $formatted_avg_time .= $days . ($days == 1 ? "day " : "days ");
        }
        if ($hours > 0) {
            $formatted_avg_time .= $hours . ($hours == 1 ? "hr " : "hrs ");
        }
        if ($minutes > 0) {
            $formatted_avg_time .= $minutes . ($minutes == 1 ? "min " : "mins ");
        }
        if ($seconds > 0) {
            $formatted_avg_time .= $seconds . ($seconds == 1 ? "sec" : "secs");
        }
        
        if ($formatted_avg_time === '') {
            $formatted_avg_time = "INVALID";
        }
           
        $formatted_avg_time = rtrim($formatted_avg_time);
        return $formatted_avg_time;
    }

    public function getAveResponseTime(){
        $post = $this->input->post();
        $start_date = isset($post['start']) ? $post['start'] : date('Y-m-d');
        $end_date = isset($post['end']) ? $post['end'] : date('Y-m-d');

        $this->db->select('AVG(TIMESTAMPDIFF(SECOND, nl.created_at, ip.created_at)) as avg_seconds');
        $this->db->from('gccticket.ticket t');
        $this->db->join('(SELECT ticket_id, created_at FROM gccticket.trail_logs_event WHERE type = "new") nl', 't.id = nl.ticket_id');
        $this->db->join('(SELECT ticket_id, created_at FROM gccticket.trail_logs_event WHERE type = "in progress") ip', 't.id = ip.ticket_id');
        $this->db->where('t.status', 'in progress');
        $this->db->where('t.is_archived', 0);

        if(!isset($post['all']) || !$post['all'] == 'true'){
            $this->db->where('t.created_at >=', $start_date . ' 00:00:00');
            $this->db->where('t.created_at <=', $end_date . ' 23:59:59');
        }
        
        $avg_response_query = $this->db->get();

        $avg_response_result = $avg_response_query->row_array();
        $avg_response_seconds = isset($avg_response_result['avg_seconds']) ? $avg_response_result['avg_seconds'] : 0;
        
        $response_days = floor($avg_response_seconds / 86400);
        $response_hours = floor(($avg_response_seconds % 86400) / 3600);
        $response_minutes = floor(($avg_response_seconds % 3600) / 60);
        $response_seconds = $avg_response_seconds % 60;
        
        $formatted_avg_response_time = '';
        
        if ($response_days > 0) {
            $formatted_avg_response_time .= $response_days . ($response_days == 1 ? "day " : "days ");
        }
        if ($response_hours > 0) {
            $formatted_avg_response_time .= $response_hours . ($response_hours == 1 ? "hr " : "hrs ");
        }
        if ($response_minutes > 0) {
            $formatted_avg_response_time .= $response_minutes . ($response_minutes == 1 ? "min " : "mins ");
        }
        if ($response_seconds > 0) {
            $formatted_avg_response_time .= $response_seconds . ($response_seconds == 1 ? "sec" : "secs");
        }
        
        if ($formatted_avg_response_time === '') {
            $formatted_avg_response_time = "INVALID";
        }
        
        $formatted_avg_response_time = rtrim($formatted_avg_response_time);
    
        return $formatted_avg_response_time;
    }

    function getExistingTicketPerUser(){
        $this->db->select("a.id,a.reference_no,a.created_at,a.message");
        $this->db->from('gccticket.ticket a');
        $this->db->where('is_archived', 0);
        $this->db->where('requestor', $this->user_data['emp_id']);
        $this->db->where(strtolower('status'), 'open');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function closeTicket() {
        $post = $this->input->post();
        $data = array(
            'status' => "resolved"
        );
        $this->db->where('id', $post['id']);
        $update = $this->db->update('gccticket.ticket', $data);
        if ($update) {
            $this->addTrailLog($post['id'], "resolved");
            $this->core_layout->setEventLog("User closed the ticket with reference no {$post['reference_no']}","update","success","gccticket","user");
        } else {
            $this->core_layout->setEventLog("User failed to close the ticket with reference no {$post['reference_no']}","update","error","gccticket","system");
        }
        return array(
            'status' => $update,
            'tickets'=> $this->getExistingTicketPerUser(),
            'message' => $update ? "Ticket closed successfully." : "Failed to close the ticket."
        );
    }

    public function serveTicket($id) {
        $ticket = $this->getTicketByid($id);
        
        if (!$ticket) {
            return ['status' => false, 'message' => 'Ticket not found.'];
        }
        
        $status = strtolower($ticket->status);
        $statusMessages = [
            'in progress' => 'Ticket is already in progress',
            'completed' => 'Ticket is already completed',
            'resolved' => 'Ticket is already completed',
            'cancelled' => 'Ticket is cancelled'
        ];
        
        if (isset($statusMessages[$status])) {
            return ['status' => false, 'message' => $statusMessages[$status]];
        }
        
        $this->db->where('id', $id);
        $update = $this->db->update('gccticket.ticket', [
            'status' => 'in progress',
            'performed_by' => $this->user_data['emp_id']
        ]);
        
        if ($update) {
            $this->addTrailLog($id, 'in progress');
            $this->core_layout->setEventLog("User served the ticket with reference no {$ticket->reference_no}", 'update', 'success', 'gccticket', 'user');
        } else {
            $this->core_layout->setEventLog("User failed to serve the ticket with reference no {$ticket->reference_no}", 'update', 'error', 'gccticket', 'system');
        }
        
        return [
            'status' => $update,
            'message' => $update ? 'Ticket served successfully.' : 'Failed to serve the ticket.'
        ];
    }


}