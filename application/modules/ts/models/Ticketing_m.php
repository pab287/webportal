<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ticketing_m extends CI_Model
{
    private $user_data = array();
    protected $tickets = "dbticket";

    public function __construct()
    {
        parent::__construct();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model", "dt_model");
        $this->user_data = $this->session->userdata("logged_in");
        $this->load->model("core/upload_model", "file_upload");
        $this->current_action = $this->core_layout->getCurrentActions();
    }

    function PendingTickets()
    {
        $this->db->select("*,count(id) c, (SELECT count(id) ac from dbticket.ticket WHERE (status='Open' OR status='Inprogress' OR status='Closed')) AS ac");
        $this->db->from("dbticket.ticket");
        $this->db->where("(status='Open' OR status='Inprogress')");
        $this->db->order_by("c", "DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function OverdueTickets()
    {
        $date = date("Y/m/d");
        $this->db->select("*,count(id) c, (SELECT count(id) ac from dbticket.ticket WHERE (status='Open' OR status='Inprogress' OR status='Closed')) AS ac");
        $this->db->from("dbticket.ticket");
        $this->db->where("(status='Open' OR status='Inprogress')");
        $this->db->where("need_dt <", $date);
        $this->db->order_by("c", "DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function ClosedTickets()
    {

        $this->db->select("*,count(id) c, (SELECT count(id) ac from dbticket.ticket WHERE (status='Open' OR status='Inprogress' OR status='Closed')) AS ac");
        $this->db->from("dbticket.ticket");
        $this->db->where("status", "Closed");
        $this->db->order_by("c", "DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function AllTickets()
    {
        $date = date("Y-m-d", strtotime("-1 year"));
        $this->db->select("*,count(id) c, (SELECT count(id) ac from dbticket.ticket) AS ac");
        $this->db->from("dbticket.ticket");
        $this->db->where("(status='Open' OR status='Inprogress' OR status='Closed')");
        $this->db->order_by("c", "DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function chartData()
    {

        $this->db->select("*,count(id) c");
        $this->db->from("dbticket.ticket");
        $this->db->group_by("type");
        $this->db->order_by("c", "DESC");
        $query = $this->db->get();
        return $query->result();
    }

    function getRecentTickets()
    {
        $resultset = array();
        $post = $this->input->post();
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_recent_all_post($limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_recent_all_post_count();

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_recent_all_post($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $firstdate = date('Y-m-d');
        $seconddate = date("Y-m-d", strtotime("+1 week"));
        $sql = "a.id, a.issue, a.created_by, a.created_dt, a.status, a.type, a.need_dt, a.department_id, b.name, a.performed_by";

        $this->db->select($sql);
        $this->db->from("dbticket.ticket a");
        $this->db->join("dbticket.department b", "a.department_id=b.id");
        $this->db->where("a.created_dt >=", $firstdate);
        $this->db->where("a.created_dt <=", $seconddate);
        $this->db->limit($limit, $offset);

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }
            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
            return $data;
        } else {
            return array();
        }
    }

    private function get_recent_all_post_count()
    {
        $firstdate = date('Y-m-d');
        $seconddate = date("Y-m-d", strtotime("+1 week"));
        $this->db->from("dbticket.ticket a");
        $this->db->where("a.created_dt >=", $firstdate);
        $this->db->where("a.created_dt <=", $seconddate);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getMasterfile()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();

        $rowCount = 0;
        $rowData = array();
        if (!$search) {
            $rowData = $this->get_all_post($limit, $offset, $sortBy, $sortOrder, $query_builder);
            $rowCount = $this->get_all_post_count($query_builder);
        }

        if ($search) {
            $rowData = $this->get_searched_item($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_item_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_post($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $query_builder)
    {
        // var_dump($query_builder);
        $date = date("Y-m-d", strtotime("-1 year"));
        $sql = "a.id, a.module, a.issue, a.created_by, a.status, a.type, b.firstname, b.lastname, b.middlename, (SELECT CONCAT(e.firstname, ' ', e.lastname)) AS display_name, (SELECT CONCAT(b.firstname, ' ', b.lastname)) as performed_by_name, b.suffix, a.need_dt, a.department_id, a.performed_by, dept.description, a.requested_dt, a.created_dt";
        $this->db->select($sql);
        $this->db->from("dbticket.ticket a");
        $this->db->join("gccmaster.tblusers d","d.id = a.performed_by", "LEFT");
        $this->db->join("gccmaster.tblemployees b","b.id = d.emp_id", "LEFT");
        $this->db->join("gccmaster.tblusers x","a.created_by = x.id", "LEFT");
        $this->db->join("gccmaster.tblemployees e","e.id = x.emp_id", "LEFT");
        $this->db->join("gcchris.tbldepartments dept","a.department_id = dept.id", "LEFT");
        $this->db->where("(a.status!='Cancelled' OR a.created_dt>'$date')");
        if($query_builder){
            $this->db->having($query_builder);
        }
        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->requested_by = $this->requested_by($rs->created_by);
                $rs->performed_by_name = $this->performedBy($rs->performed_by);
                $arrData[$key] = $rs;
            }
            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
            return $data;
        } else {
            return array();
        }
        
    }

    private function get_all_post_count($query_builder=null)
    {
        $date = date("Y-m-d", strtotime("-1 year"));
        $sql = "a.id, a.module, a.issue, a.created_by, a.status, a.type, b.firstname, b.lastname, b.middlename, (SELECT CONCAT(e.firstname, ' ', e.lastname)) AS display_name, (SELECT CONCAT(b.firstname, ' ', b.lastname)) as performed_by_name, b.suffix, a.need_dt, a.department_id, a.performed_by, dept.description, a.requested_dt, a.created_dt";
        $this->db->select($sql);
        $this->db->from("dbticket.ticket a");
        $this->db->join("gccmaster.tblusers d","d.id = a.performed_by", "LEFT");
        $this->db->join("gccmaster.tblemployees b","b.id = d.emp_id", "LEFT");
        $this->db->join("gccmaster.tblusers x","a.created_by = x.id", "LEFT");
        $this->db->join("gccmaster.tblemployees e","e.id = x.emp_id", "LEFT");
        $this->db->join("gcchris.tbldepartments dept","a.department_id = dept.id", "LEFT");
        $this->db->where("(a.status!='Cancelled' OR a.created_dt>'$date')");
        if($query_builder){
            $this->db->having($query_builder);
        }
        
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_item($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        if ($search) {
            $date = date("Y-m-d", strtotime("-1 year"));
            $sql = "a.id, a.issue, a.created_by, a.status, a.type, b.firstname, b.lastname, b.middlename, b.suffix, a.need_dt, a.department_id, (SELECT CONCAT(x.firstname, ' ', x.lastname) AS performer FROM gccmaster.tblemployees WHERE id = a.performed_by) AS performer,  a.requested_dt,  a.created_dt";
            $filterFields = array("a.id", "a.status", "b.firstname", "b.lastname", "a.type", "a.need_dt", "a.performed_by", "a.issue", "a.department_id", "a.requested_dt", "a.created_dt");
            $this->db->select($sql);
            $this->db->from("dbticket.ticket a");
            $this->db->join("gccmaster.tblusers d","d.id = a.performed_by", "LEFT");
            $this->db->join("gccmaster.tblemployees b", "a.created_by = b.id", "LEFT");
            $this->db->join("gccmaster.tblemployees x", "x.id = d.emp_id", "LEFT");
            $this->db->where("(a.status!='Cancelled' OR a.created_dt>'$date')");

            if ((int)$limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->requested_by = $this->requested_by($rs->created_by);
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $arrData[$key] = $rs;
                }
                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_item_count($search = null)
    {
        $rowCount = 0;
        if ($search) {
            $date = date("Y-m-d", strtotime("-1 year"));
            $sql = "a.id, a.issue, a.created_by, a.status, a.type, CONCAT(b.firstname,' ',b.lastname) AS name, a.need_dt, a.department_id, (SELECT CONCAT(firstname, ' ', lastname) AS performer FROM gccmaster.tblemployees WHERE id = a.performed_by) AS performer, a.requested_dt,  a.created_dt";
            $filterFields = array("a.department_id", "a.id", "a.status", "b.firstname", "b.lastname", "a.type", "a.need_dt", "a.performed_by", "a.issue", "a.requested_dt", "a.created_dt");
            $this->db->select($sql);
            $this->db->from("dbticket.ticket a");
            $this->db->join("gccmaster.tblemployees b", "a.created_by = b.id", "LEFT");
            $this->db->where("(a.status!='Cancelled' OR a.created_dt>'$date')");
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    function performedBy($id)
    {
        $this->db->select("a.firstname, a.lastname, a.middlename, a.suffix");
        $this->db->from("gccmaster.tblemployees a");
        $this->db->join("gccmaster.tblusers b","b.emp_id=a.id");
        $this->db->join(" dbticket.tblgroups c", "c.id = b.group_id ");
        $this->db->where("b.id", $id);
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
            return "<i class='m--font-danger'>Not set</i>";
        }
    }

    function Department()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT  id, description FROM gcchris.tbldepartments WHERE description LIKE '%{$get['q']}%' ORDER BY description ASC");
        } else {
            $query = $this->db->query("SELECT  id, description FROM gcchris.tbldepartments ORDER BY description ASC");
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

    function saveTicket()
    {
        $this->input->post();
        $type = $this->input->post('type');
        $employeeId = $this->user_data['emp_id'];
        if ($type == "WEBPORTAL") {
            $module = $this->input->post('module');
            $module = (isset($module) && $module) ? $module : "";
        } else {
            $module = "";
        }

        $str_pic = implode(",",$this->input->post('pic'));
        $arr_pic = explode(",",$str_pic);
    
        foreach($arr_pic as $img){
            $img_arr[] = "empcode_{$employeeId}/ticketing/".$img;
        }

        $date = date('Y-m-d H:i:s');
        $data = array(
            'department_id' => $this->input->post('department'),
            'type' => $this->input->post('type'),
            'issue' => $this->input->post('issue'),
            'created_by' => $this->user_data['id'],
            'created_dt' => $date,
            'requested_dt' => $date,
            'picture' => implode(",",$img_arr),
            'need_dt' => $this->input->post('need_dt'),
            'module' => $module,
            'status' => "Open"
        );
        $this->db->insert('dbticket.ticket', $data);
        $last_id = $this->db->insert_id();
        if ($last_id) {
            $this->email_send($last_id, $this->input->post('type'), $this->input->post('issue'), $this->input->post('need_dt'), $module, $this->user_data['id'], $date, "Open", "", "");
        }
        return true;
    }

    function deleteTicket($id)
    {
        return $this->db->query("DELETE FROM dbticket.ticket WHERE id=$id");
    }


    //function to display all ticketing entries
    function ticketMasterfile($id)
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();

        $rowCount = 0;
        $rowData = array();
        if (!$search) {
            $rowData = $this->get_ticket_masterfile($id, $limit, $offset, $sortBy, $sortOrder, $query_builder);
            $rowCount = $this->get_ticket_masterfile_count($id, $query_builder);
        }

        if ($search) {
            $rowData = $this->get_ticket_searched_item($id, $search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_ticket_searched_item_count($id, $search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }
    // end fnction

    // function to pull all data from database of ticketing system
    private function get_ticket_masterfile($id, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $sql = "a.id, a.issue, a.created_by, a.status, a.type, b.firstname, b.middlename, b.lastname, b.suffix, a.need_dt, a.department_id, a.performed_by, c.name";
        $date = date("Y/m/d");
        if ($id == "pending") {
            $this->db->select($sql);
            $this->db->from("dbticket.ticket a");
            $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
            $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
            $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
            $this->db->where("(status='Open' OR status='Inprogress')");
            $this->db->limit($limit, $offset);
        } else if ($id == "overdue") {
            $this->db->select($sql);
            $this->db->from("dbticket.ticket a");
            $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
            $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
            $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
            $this->db->where("(status='Open' OR status='Inprogress')");
            $this->db->where("need_dt <=", $date);
            $this->db->limit($limit, $offset);
        } else if ($id == "confirm") {
            $this->db->select($sql);
            $this->db->from("dbticket.ticket a");
            $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
            $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
            $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
            $this->db->where("status", "Closed");
            $this->db->limit($limit, $offset);
        } else {
            $this->db->select($sql);
            $this->db->from("dbticket.ticket a");
            $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
            $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
            $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
            $this->db->where("(status='Open' OR status='Inprogress' OR status='Closed')");
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->emp_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $arrData[$key] = $rs;
            }
            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
            return $data;
        } else {
            return array();
        }
    }
    // end function

    // function to count all ticketing entries
    private function get_ticket_masterfile_count($id)
    {
        $date = date("Y/m/d");
        if ($id == "pending") {
            $this->db->from("dbticket.ticket a");
            $this->db->where("(status='Open' OR status='Inprogress')");
        } else if ($id == "overdue") {
            $this->db->from("dbticket.ticket a");
            $this->db->where("(status='Open' OR status='Inprogress')");
            $this->db->where("need_dt <=", $date);
        } else if ($id == "confirm") {
            $this->db->from("dbticket.ticket a");
            $this->db->where("status", "Closed");
        } else {
            $this->db->from("dbticket.ticket a");
            $this->db->where("(status='Open' OR status='Inprogress' OR status='Closed')");
        }
        $query = $this->db->get();
        return $query->num_rows();
    }
    // end function

    // function to show search results from ticketing search field
    private function get_ticket_searched_item($id, $search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        if ($search) {
            $sql = "a.id, a.issue, a.created_by, a.status, a.type, b.firstname, b.middlename, b.lastname, b.suffix, a.need_dt, a.department_id, a.performed_by, c.name";
            $filterFields = array("a.id", "a.status", "b.firstname", "b.lastname", "a.type", "a.need_dt", "a.performed_by", "a.issue", "a.department_id", "c.name");
            $date = date("Y/m/d");
            if ($id == "pending") {
                $this->db->select($sql);
                $this->db->from("dbticket.ticket a");
                $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
                $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
                $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
                $this->db->where("(status='Open' OR status='Inprogress')");
                $this->db->limit($limit, $offset);
            } else if ($id == "overdue") {
                $this->db->select($sql);
                $this->db->from("dbticket.ticket a");
                $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
                $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
                $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
                $this->db->where("(status='Open' OR status='Inprogress')");
                $this->db->where("need_dt <=", $date);
                $this->db->limit($limit, $offset);
            } else if ($id == "confirm") {
                $this->db->select($sql);
                $this->db->from("dbticket.ticket a");
                $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
                $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
                $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
                $this->db->where("status", "Closed");
                $this->db->limit($limit, $offset);
            } else {
                $this->db->select($sql);
                $this->db->from("dbticket.ticket a");
                $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
                $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
                $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
                $this->db->where("(status='Open' OR status='Inprogress' OR status='Closed')");
                $this->db->limit($limit, $offset);
            };
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->emp_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $arrData[$key] = $rs;
                }
                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
    // end function

    // function to count all results of searched item
    private function get_ticket_searched_item_count($id, $search = null)
    {
        $rowCount = 0;
        if ($search) {
            $sql = "a.id, a.issue, a.created_by, a.status, a.type,  b.firstname, b.middlename, b.lastname, b.suffix, a.need_dt, a.department_id, a.performed_by, c.name";
            $filterFields = array("a.id", "a.status", "b.firstname", "b.lastname", "a.type", "a.need_dt", "a.performed_by", "a.issue", "a.department_id", "c.name");
            $date = date("Y/m/d");
            if ($id == "pending") {
                $this->db->select($sql);
                $this->db->from("dbticket.ticket a");
                $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
                $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
                $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
                $this->db->where("(status='Open' OR status='Inprogress')");
            } else if ($id == "overdue") {
                $this->db->select($sql);
                $this->db->from("dbticket.ticket a");
                $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
                $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
                $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
                $this->db->where("(status='Open' OR status='Inprogress')");
                $this->db->where("need_dt <=", $date);
            } else if ($id == "confirm") {
                $this->db->select($sql);
                $this->db->from("dbticket.ticket a");
                $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
                $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
                $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
                $this->db->where("status", "Closed");
            } else {
                $this->db->select($sql);
                $this->db->from("dbticket.ticket a");
                $this->db->join("gccmaster.tblusers d","a.created_by = d.id", "LEFT");
                $this->db->join("gccmaster.tblemployees b","d.emp_id = b.id", "LEFT");
                $this->db->join("dbticket.department c", "a.department_id = c.id", "LEFT");
                $this->db->where("(status='Open' OR status='Inprogress' OR status='Closed')");
            };
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }
    // end function

    // function for uploading photo
    function uploadTicketPhoto()
    {
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


    // function to display ticket details
    function editTicketDetails($id)
    {
        $result = array();
        $this->db->select(" a.*,b.id as did, b.description");
        $this->db->from("dbticket.ticket a");
        $this->db->join("gcchris.tbldepartments b", "a.department_id=b.id", "LEFT");
        $this->db->where("a.id", $id);
        $query = $this->db->get();
        $results = $query->row_array();
        $result['result'] = $query->row_array();
        $result['requested_by'] = $this->requested_by($results['created_by']);
        return $result;
    }
    // end function

    function updateTicket($id)
    {   
        $this->input->post();
        $employeeId = $this->user_data['emp_id'];
        $module = $this->input->post('module');
        $module = (isset($module) && $module) ? $module : "";
        $date = date('Y-m-d H:i:s');

        $str_pic = implode(",",$this->input->post('pic'));
        $arr_pic = explode(",",$str_pic);
        
        $img_arr = array();
        foreach($arr_pic as $img){
            $img_arr[] = "empcode_{$employeeId}/ticketing/".$img;
        }
        
        
        $data = array(
            'department_id' => $this->input->post('department'),
            'need_dt' => $this->input->post('need_dt'),
            'type' => $this->input->post('type'),
            'module' => $module,
            'issue' => $this->input->post('issue'),
            'picture' => implode(",",$img_arr),
            'modify_by' => $this->user_data['firstname'] . ' ' . $this->user_data['lastname'],
            'modify_dt' => $date,
        );
        if(empty($str_pic)){
            $data['picture'] = "";
        }
        if ($id) {
            $this->db->where('dbticket.ticket.id', $id);
            return $this->db->update('dbticket.ticket', $data);
        }
    }

    function serviceDetails($id)
    {
        $result = array();
        $this->db->select("a.*, b.id as did , b.description, c.id AS emp_id, c.position, c.firstname, c.middlename, c.lastname, c.suffix");
        $this->db->from("dbticket.ticket a");
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
                $rs->requested_by = $this->requested_by($rs->created_by);
                $rs->performed_by_det = $this->performedBy($rs->performed_by);
                $rs->department = $this->getDepartment($rs->department_id);
                if (is_numeric($rs->position)) {
                    $rs->position = $this->getPosition($rs->position);
                } else {
                    $rs->position = $rs->position;
                }

                $rs->picture_exist = file_exists(realpath("uploads/files/images/employee_files/" . $rs->picture));

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
        $this->db->where("b.id", $id);
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

    function getDepartment($id)
    {
        $this->db->select("id, description");
        $this->db->from("gcchris.tbldepartments");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->description;
    }

    function getPosition($id)
    {
        $this->db->select("id, name");
        $this->db->from("gcchris.tblposition");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->name;
    }

    function Performed()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT  b.id, CONCAT(c.firstname,' ',c.lastname) as emp_name FROM dbticket.tblgroups a, gccmaster.tblusers b, gccmaster.tblemployees c WHERE a.id=b.group_id AND b.emp_id=c.id AND b.group_id='1' AND c.employee_status = 'Active' AND (CONCAT(c.firstname,' ',c.lastname) LIKE '%{$get['q']}%') ORDER BY group_name");
        } else {
            $query = $this->db->query("SELECT  b.id, CONCAT(c.firstname,' ',c.lastname) as emp_name FROM dbticket.tblgroups a, gccmaster.tblusers b, gccmaster.tblemployees c WHERE a.id=b.group_id AND b.emp_id=c.id AND b.group_id='1' AND c.employee_status = 'Active' ORDER BY group_name");
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

    function updateService($id)
    {

        $ticket_details = $this->db->get_where("dbticket.ticket", array("id"=>$id))->row();
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $onhold = $this->input->post('onhold');
        $type = $this->input->post('type');
        $employeeId = $this->user_data['emp_id'];
        if ($type == "WEBPORTAL") {
            $module = $this->input->post('module');
            $module = (isset($module) && $module) ? $module : "";
        } else {
            $module = "";
        }
        $data = array(
            'status' => $this->input->post('status'),
            'onhold' => $onhold,
            'performed_by' => $this->input->post('performed_by'),
            'performed_dt' => $date,
            //'picture' => $this->input->post("pic"),
            'remark' => $this->input->post('remarks'),
            'modify_by' => $this->user_data['firstname'] . ' ' . $this->user_data['lastname'],
            'modify_dt' => $date,
        );
        if ($id) {
            $current_status = $this->db->get_where("dbticket.ticket", array("id"=>$id))->row('status'); 
            $this->db->where('dbticket.ticket.id', $id);
            $update = $this->db->update('dbticket.ticket', $data);
            if($current_status != $this->input->post('status') && $this->input->post('status') == 'Onhold'){
                $this->email_send($id, $ticket_details->type, $this->input->post('issue'), $this->input->post('need_dt'), $module, $this->user_data['id'], $date,$this->input->post('status'), $this->input->post('remarks'), $onhold);
            }else if($current_status != $this->input->post('status')){
                $this->email_send($id, $ticket_details->type, $this->input->post('issue'), $this->input->post('need_dt'), $module, $this->user_data['id'], $date,$this->input->post('status'), $this->input->post('remarks'), "");
            }else{
                $this->email_send($id, $ticket_details->type, $this->input->post('issue'), $this->input->post('need_dt'), $module, $this->user_data['id'], $date,$this->input->post('status'), $this->input->post('remarks'), "");
            }
    
            return $update;
        }
    }

    function confirmDetails($id)
    {
        $this->db->select("a.*, b.id as did , b.name, c.id AS emp_id, CONCAT(c.firstname,' ',c.lastname) AS emp_name");
        $this->db->from("dbticket.ticket a");
        $this->db->join("dbticket.department b", "a.department_id=b.id", "LEFT");
        $this->db->join("gccmaster.tblusers d","a.performed_by = d.id", "LEFT");
        $this->db->join("gccmaster.tblemployees c", "d.emp_id=c.id", "LEFT");
        $this->db->where("a.id", $id);
        $query = $this->db->get();
        $data = $query->row_array();
        
        if($data['name'] == NULL){
            $this->db->select("a.*, b.id as did , b.description as name , c.id AS emp_id, CONCAT(c.firstname,' ',c.lastname) AS emp_name");
            $this->db->from("dbticket.ticket a");
            $this->db->join("gcchris.tbldepartments b", "a.department_id=b.id", "LEFT");
            $this->db->join("gccmaster.tblusers d","a.performed_by = d.id", "LEFT");
            $this->db->join("gccmaster.tblemployees c", "d.emp_id=c.id", "LEFT");
            $this->db->where("a.id", $id);
            $query = $this->db->get();
            $data = $query->row_array();
        }
        return $data;

        // $filepath = "uploads/files/";
    }


    function updateConfirm($id)
    {
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'status' => "Confirmed"
        );
        if ($id) {
            $this->db->where('dbticket.ticket.id', $id);
            return $this->db->update('dbticket.ticket', $data);
        }
    }

    function reopenTicket($id)
    {
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'status' => "Open",
            'open' => $this->input->post("reason")
        );
        if ($id) {
            $this->db->where('dbticket.ticket.id', $id);
            return $this->db->update('dbticket.ticket', $data);
        }
    }

    function archivedList()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";

        $rowCount = 0;
        $rowData = array();
        if (!$search) {
            $rowData = $this->archived_all_post($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->archived_all_post_count();
        }

        if ($search) {
            $rowData = $this->archived_searched_item($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->archived_searched_item_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function archived_all_post($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $date = date("Y-m-d", strtotime("-1 year"));
        $sql = "a.id, a.issue, a.created_by, a.status, a.type, CONCAT(b.firstname,' ',b.lastname) AS name, a.need_dt, a.department_id, (SELECT CONCAT(firstname, ' ', lastname) AS performer FROM gccmaster.tblemployees WHERE id = a.performed_by) AS performer";
        $this->db->select($sql);
        $this->db->from("dbticket.ticket a");
        $this->db->join("gccmaster.tblemployees b", "a.created_by = b.id", "LEFT");
        $this->db->where("(a.status='Cancelled' OR a.created_dt<='$date')");
        $this->db->limit($limit, $offset);

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }
            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
            return $data;
        } else {
            return array();
        }
    }

    private function archived_all_post_count()
    {
        $date = date("Y-m-d", strtotime("-1 year"));
        $this->db->from("dbticket.ticket a");
        $this->db->where("(a.status='Cancelled' OR a.created_dt<='$date')");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function archived_searched_item($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $date = date("Y-m-d", strtotime("-1 year"));
        if ($search) {
            $sql = "a.id, a.issue, a.created_by, a.status, a.type, CONCAT(b.firstname,' ',b.lastname) AS name, a.need_dt, a.department_id, (SELECT CONCAT(firstname, ' ', lastname) AS performer FROM gccmaster.tblemployees WHERE id = a.performed_by) AS performer";
            $filterFields = array("a.id", "a.status", "b.firstname", "b.lastname", "a.type", "a.need_dt", "a.performed_by", "a.issue", "a.department_id");
            $this->db->select($sql);
            $this->db->from("dbticket.ticket a");
            $this->db->join("gccmaster.tblemployees b", "a.created_by = b.id", "LEFT");
            $this->db->where("(a.status='Cancelled' OR a.created_dt<='$date')");
            $this->db->limit($limit, $offset);
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $arrData[$key] = $rs;
                }
                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    private function archived_searched_item_count($search = null)
    {
        $date = date("Y-m-d", strtotime("-1 year"));
        $rowCount = 0;
        if ($search) {
            $sql = "a.id, a.issue, a.created_by, a.status, a.type, CONCAT(b.firstname,' ',b.lastname) AS name, a.need_dt, a.department_id, (SELECT CONCAT(firstname, ' ', lastname) AS performer FROM gccmaster.tblemployees WHERE id = a.performed_by) AS performer";
            $filterFields = array("a.department_id", "a.id", "a.status", "b.firstname", "b.lastname", "a.type", "a.need_dt", "a.performed_by", "a.issue");
            $this->db->select($sql);
            $this->db->from("dbticket.ticket a");
            $this->db->join("gccmaster.tblemployees b", "a.created_by = b.id", "LEFT");
            $this->db->where("(a.status='Cancelled' OR a.created_dt<='$date')");
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    function email_send($id, $type, $issue, $need_dt, $module = null, $req, $date, $status, $remarks, $onhold)
    {
        $if_exist = $this->db->get_where("dbticket.ticket", array("id"=>$id))->row();
        $recipient = $this->db->get_where("gccmaster.tblusers", array("id"=>$if_exist->created_by))->row();
        if(count($if_exist) > 0){
            $title = "Ticket Update - {$type}";
        }else{
            $title = "New Ticket - {$type}";
        }
        
        $req = mb_strtoupper($req);

        //$emailTo = $this->sendEmailCompanyTo($companyTo);
        //$emailTo = ($emailTo)? $emailTo: "busysanalyst@gccph.com";
        $data = array("id" => $id, "employee_name" => $req, "type" => $type, "issue" => $issue, "need_dt"=>$need_dt,"module" => $module, "date" => $date, "status" => $status,  "remarks" => $remarks, "onhold"=> $onhold);
        $message = "";
        $message .= $this->load->view("ts/email_template/email-ticketing", $data, true);

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

    public function get_ticket($id)
    {
        $this->db->from('dbticket.ticket');
        $this->db->where('id', $id);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    function getUserEmpData()
        {
            $get = $this->input->get();
            $id = $this->user_data['emp_id'];
            $resultarray = array();

            $this->db->from("gcchris.tbldepartments");
            $query_dep = $this->db->where(array("head_id" => $id, "is_archived" => 0))->get();

            $dept_head = $query_dep->num_rows();
            $isDepartmentHead = $dept_head > 0 ? true: false;
            $view_own_request = (in_array("view_own_request", $this->current_action)) ? true : false;
            $allowSearchEmployee = ($isDepartmentHead == true || $view_own_request == false)? true: false;
            
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('employee_status', 'Active');
            $this->db->where('id', $id);
            $query = $this->db->get();

            $resultarray["allow_search_employee"] = $allowSearchEmployee;
            
            if($query->num_rows() == 1){
                $tempRow = $query->row();
                $tempRowArray = $query->row_array();
                if(is_numeric($tempRow->department_id)){
                    $department = $this->getDepartment($tempRow->department_id);
                }else{
                    $department = $tempRow->department_id;
                }

                $resultarray["response"] = true;
                $resultarray["id"] = $tempRow->department_id;
                $resultarray["text"] = $department;
            }else{
                $resultarray["response"] = false;
            }

            return $resultarray;
        }

        function getDepartmentCollection()
        {
            $id = $this->user_data['emp_id'];
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $this->db->from('gcchris.tbldepartments');
                $this->db->where('gccmaster.tbldepartments.is_archived', '0');
                $this->db->order_by('description', 'asc');
                $this->db->like('description', $get['q']);
                $this->db->or_like('code', $get['q']);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    foreach ($query->result_array() as $_query) {
                        $department = $_query["description"];

                        $data = array();
                        $data["id"] = $_query["id"];
                        $data["text"] = $department;
                        /*** $data["text"] = $_query["firstname"]." ".$_query["middlename"]." ".$_query["lastname"]; ***/
                        $resultarray[] = $data;
                    }
                }
            } else {
                $this->db->from('gcchris.tbldepartments');
                $this->db->where('gcchris.tbldepartments.is_archived', '0');
                $this->db->order_by('code', 'asc');
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    foreach ($query->result_array() as $_query) {
                        $department = $_query["description"];

                        $data = array();
                        $data["id"] = $_query["id"];
                        $data["text"] = $department;
                        /*** $data["text"] = $_query["firstname"]." ".$_query["middlename"]." ".$_query["lastname"]; ***/
                        $resultarray[] = $data;
                    }
                }
            }

            return array("results" => $resultarray);
        }

        function removeFile(){
            $result = array();
            $id = $this->user_data['emp_id'];
            $file = $this->input->post('filename');
            $url = realpath("uploads/files/images/employee_files/empcode_".$id."/ticketing/".$file);
            
            if(!file_exists($url)){
                $result['result'] = false;
            }else{
                unlink($url);
                $result['result'] = true;
                $result['file'] = $file;
            }
            return $result;
        }

        function getRequestedBy(){
            $get = $this->input->get();
            $resultarray = array();
            if(isset($get['q'])){
                $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE (employee_status='Active') AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%') ORDER BY firstname ASC LIMIT 10");
            }else{
                $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC LIMIT 10");
            }
    
            if($query->num_rows() > 0){
                foreach($query->result_array() as $_query){
                    $data = array();
                    $tempRs = (array) $_query;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                
                    $data["id"] = $_query["id"];
                    $data["text"] =   ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $resultarray[] = $data;
                }
            }
            return array("results"=>$resultarray);
        }

}