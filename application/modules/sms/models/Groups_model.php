<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Groups_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model", "dt_model");
    }

    private function getUserData()
    {
        return $this->core_layout->getUserLoggedIn();
    }

    function getGroups()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();
        if (!$search) {
            $rowData = $this->get_all_groups($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_groups_count();
        }

        if ($search) {
            $rowData = $this->get_searched_groups($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_groups_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_groups_count()
    {

        $this->db->from("gccsms.tblcontactgroups");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_groups($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $sql = "a.id, a.group_name";
        $this->db->select($sql);
        $this->db->from("gccsms.tblcontactgroups a");

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


    private function get_searched_groups($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.group_name");
            $sql = "a.id, a.group_name";
            $this->db->select($sql);
            $this->db->from("gccsms.tblcontactgroups a");

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }

            $this->db->group_end();

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

    private function get_searched_groups_count($search = null)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.group_name");
            $sql = "a.id, a.group_name";
            $this->db->select($sql);
            $this->db->from("gccsms.tblcontactgroups a");

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function getContactName($id){
        $this->db->select("concat(firstname,' ',lastname) as name");
        $this->db->from("gccsms.tblcontacts");
        $this->db->where("id", $id);
        $query = $this->db->get();
        return $query ? $query->row_array()["name"] : "";
    }

    public function save_group_contact($data)
    {
        $resultarray = array();

        $group_name = $this->getGroupName($data['group_id']);
        $contact_name = $this->getContactName($data['contact_id']);

        if($this->checkDuplicateGroupMem($data['group_id'], $data['contact_id']) == 0){
            
            $query = $this->db->insert('gccsms.tblgroupmembers', $data);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully saved.";
                $this->core_layout->setEventLog("Masterfile: Groups - added ".$contact_name." in the group of ".$group_name,"insert", "success", "gccsms", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error saving.";
                $this->core_layout->setEventLog("Masterfile: Groups - tried to add ".$contact_name." in the group of ".$group_name,"insert", "error", "gccsms", "user");
            }
        } else {
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Group member already exist";
        }
        
        return $resultarray;
    }

    function checkDuplicateGroupMem($group_id, $contact_id){
        $this->db->select("id");
        $this->db->from("gccsms.tblgroupmembers");
        $this->db->where("group_id", $group_id);
        $this->db->where("contact_id", $contact_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function save_group($data)
    {
        $resultarray = array();
        if($this->checkDuplicate($data['group_name']) == 0){
            
            $query = $this->db->insert('gccsms.tblcontactgroups', $data);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully saved.";
                $this->core_layout->setEventLog("Masterfile: Groups - added ".$data['group_name'],"insert", "success", "gccsms", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error saving.";
                $this->core_layout->setEventLog("Masterfile: Groups - adding ".$data['group_name'],"insert", "error", "gccsms", "user");
            }
        } else {
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Group name already exist";
            $this->core_layout->setEventLog("Masterfile: Groups - trying to add ".$data['group_name']." that already exist","insert", "error", "gccsms", "user");
        }
        
        return $resultarray;
    }

    function checkDuplicate($group_name, $where=null){
        $this->db->select("id");
        $this->db->from("gccsms.tblcontactgroups");
        $this->db->where("group_name", $group_name);
        if ($where) { $this->db->where("id !=", $where); }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function edit_group($id)
    {
        $sql = "a.id, a.group_name";

        $this->db->select($sql);
        $this->db->from("gccsms.tblcontactgroups a");
        $this->db->where('a.id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function update_group($where, $data)
    {
        $resultarray = array();
        $old_name = $this->getGroupName($where['id']);
        if($this->checkDuplicate($data['group_name'], $where['id']) == 0){

            $query = $this->db->update('gccsms.tblcontactgroups', $data, $where);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully saved.";
                $this->core_layout->setEventLog("Masterfile: Groups - updated the group name of ".$old_name." into ".$data['group_name'],"update", "success", "gccsms", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error saving.";
                $this->core_layout->setEventLog("Masterfile: Groups - updating the group name of ".$old_name." into ".$data['group_name'],"update", "error", "gccsms", "user");
            }
        } else {
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Group name already exist";
            $this->core_layout->setEventLog("Masterfile: Groups - trying to update the group name of ".$old_name." into ".$data['group_name']." that already exist","update", "error", "gccsms", "user");
        }
        
        return $resultarray;
    }

    function getGroupName($id){
        $this->db->select("group_name");
        $this->db->from("gccsms.tblcontactgroups");
        $this->db->where("id", $id);
        $query = $this->db->get();
        return $query ? $query->row_array()["group_name"] : "";
    }

    public function delete_group($id)
    {
        $resultarray = array();
        $group_name = $this->getGroupName($id);

        $this->db->where('id', $id);
        $query = $this->db->delete('gccsms.tblcontactgroups');
        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Successfully deleted.";
            $this->core_layout->setEventLog("Masterfile: Groups - deleted ".$group_name,"delete", "success", "gccsms", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error deleting.";
            $this->core_layout->setEventLog("Masterfile: Groups - deleting ".$group_name,"delete", "error", "gccsms", "user");
        }
        
        return $resultarray;
    }

    public function delete_group_contact($data)
    {
        $resultarray = array();

        $this->db->where('id', $data['id']);
        $query = $this->db->delete('gccsms.tblgroupmembers');
        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Successfully deleted.";
            $this->core_layout->setEventLog("Masterfile: Groups - deleted ".$data["name"]." in the group of ".$data["group_name"],"delete", "success", "gccsms", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error deleting.";
            $this->core_layout->setEventLog("Masterfile: Groups - deleting ".$data["name"]." in the group of ".$data["group_name"],"delete", "error", "gccsms", "user");
        }
        
        return $resultarray;
    }

    function getContactCollection()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $this->db->from('gccsms.tblcontacts');

            $this->db->order_by('firstname', 'asc');
            $this->db->like('firstname', $get['q']);

            $this->db->or_like('lastname', $get['q']);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["firstname"] . " " . $_query["lastname"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);


        } else {
            $this->db->from('gccsms.tblcontacts');

            $this->db->order_by('firstname', 'asc');
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["firstname"] . " " . $_query["lastname"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }
    }

    function getGroupContact($id)
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 100;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_all_post_contact($limit, $offset, $sortBy, $sortOrder, $id);
        $rowCount = $this->get_all_group_contact_count($id);

        $totalNotFiltered = $rowCount;


        $resultset["data"] = $rowData;
        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;

        return $resultset;
    }

    private function get_all_group_contact_count($id)
    {
        $this->db->join("gccsms.tblgroupmembers b", "a.id = b.group_id", "LEFT");
        $this->db->join("gccsms.tblcontacts c", "c.id = b.contact_id", "LEFT");
        $this->db->where('a.id', $id);
        return $this->db->count_all_results("gccsms.tblcontactgroups a");
    }

    private function get_all_post_contact($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $id)
    {
        $sql = "b.id, CONCAT(c.firstname,' ',c.lastname) AS name, c.cp_no, c.category";

        $this->db->select($sql);
        $this->db->from("gccsms.tblcontactgroups a");
        $this->db->join("gccsms.tblgroupmembers b", "a.id = b.group_id", "LEFT");
        $this->db->join("gccsms.tblcontacts c", "c.id = b.contact_id", "LEFT");
        $this->db->where('a.id', $id);

        if ((int)$limit > 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }

        $query = $this->db->get();
        return $query->result();
    }
}