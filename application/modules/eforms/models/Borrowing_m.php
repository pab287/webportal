<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Borrowing_m extends CI_Model
{
    protected $eformsTable = "gcceforms";
    protected $borrowing  = "gcceforms.borrowing";
    protected $borrowing_body  = "gcceforms.borrowing_body";

    public function __construct()
    {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in"); 
    }

    function getDatatableRequest() {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        $status = (isset($post['status']) && $post['status']) ? ucwords($post['status']) : null; //clicked in portal dashboard

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        $rowData = $this->get_all_items($query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $status, $view_by_company, $companyDescription);
        $rowCount = $this->get_all_items_count($query_builder, $search, $status, $view_by_company, $companyDescription);
        // if (!$search) {
        //     $rowData = $this->get_all_postv1($query_builder, $limit, $offset, $sortBy, $sortOrder, $status);
        //     $rowCount = $this->get_all_post_countv1($query_builder, $status);
        // }

        // if ($search) {
        //     $rowData = $this->get_searched_itemv1($query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $status);
        //     $rowCount = $this->get_searched_item_countv1($query_builder, $search, $status);
        //     $this->core_layout->setEventLog("Borrowing Masterfile - Search {$search} in datatable.", "search", "success", "gcceforms", "user");
        // }

        if (isset($search) && $search) {
            $this->core_layout->setEventLog("Borrowing Masterfile - Search {$search} in datatable.", "search", "success", "gcceforms", "user");
        }

        if (isset($query_builder) && $query_builder) {
            $this->core_layout->setEventLog("Borrowing Masterfile - Generate masterfile through query builder `{$query_builder}`.", "search", "success", "gcceforms", "user");
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_items($query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null, $view_by_company = false, $companyDescription = null) {
        $check = date("Y-m-d", strtotime("-1 year", time()));
        $data = array();

        $filterFields = array("a.id", "a.status", "comp.code", "pos.name", "a.reference_no", "a.date_trans", "c.asset_name", "b.firstname", "b.middlename", "b.lastname", "CONCAT(b.firstname,' ',b.lastname)");
        $sql = "a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, IF(pos.`name` IS NULL, a.position, pos.`name`) position, IF(comp.code IS NULL, a.company, comp.code) company, a.reference_no, a.date_trans, c.asset_name";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company OR comp.code = a.company", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");
        $this->db->where("DATE(a.date_trans) >= ", $check);

        if ($view_by_company) {
            $this->db->where('a.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('a.company', $companyDescription);
            }
        }

        if (isset($query_builder) && $query_builder) {
            $this->db->where($query_builder);
        }

        if ($status) {
            $this->db->where("a.status", $status);
        } else {
            $this->db->where('a.status != ', "Cancelled");
        }

        if (isset($search) && $search) {
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

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();

            foreach ($query->result() as $key => $rs) {
                $rs->company = (is_numeric($rs->company)) ? $this->getCompany($rs->company) : $rs->company;
                $rs->position = (is_numeric($rs->position)) ? $this->getPosition($rs->position) : $rs->position;

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->display_name = $name;
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    function get_all_items_count($query_builder=null, $search = null, $status = null, $view_by_company = false, $companyDescription = null) {
        $check = date("Y-m-d", strtotime("-1 year", time()));

        $filterFields = array("a.id", "a.status", "comp.code", "pos.name", "a.reference_no", "a.date_trans", "c.asset_name", "b.firstname", "b.middlename", "b.lastname", "CONCAT(b.firstname,' ',b.lastname)");
            $sql = "a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, IF(pos.`name` IS NULL, a.position, pos.`name`) position, IF(comp.`code` IS NULL, a.company, comp.`code`) company,a.reference_no, a.date_trans, c.asset_name";
        
        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company OR comp.code = a.company", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");
        $this->db->where("DATE(a.date_trans) >= ", $check);

        if ($status) {
            $this->db->where("a.status", $status);
        } else {
            $this->db->where('a.status != ', "Cancelled");
        }

        if ($view_by_company) {
            $this->db->where('a.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('a.company', $companyDescription);
            }
        }

        if (isset($query_builder) && $query_builder) {
            $this->db->where($query_builder);
        }

        if (isset($search) && $search) {
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

    private function get_all_post_countv1($query_builder=null, $status = null)
    {
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
        $this->db->where('a.status != ', 'Cancelled');
        $this->db->where('a.date_trans >= ', $check);
        if($query_builder){
            $this->db->where($query_builder);
        }

        if($status){
            $this->db->where("a.status", $status);
        }

        $query = $this->db->get();

        return $query->num_rows();
    }

    private function get_all_postv1($query_builder=null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null)
    {
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $sql = "a.id, a.status, b.firstname, 
                b.lastname, b.middlename, b.suffix, 
                IF(pos.`name` IS NULL, a.position, pos.`name`) position, 
                IF(comp.`code` IS NULL, a.company, comp.`code`) company, 
                a.reference_no, 
                a.date_trans, c.asset_name";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body as c", "a.id = c.borrowing_id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");
        $this->db->where('a.status != ', 'Cancelled');
        $this->db->where('a.date_trans >= ', $check);
        if($query_builder){
            $this->db->where($query_builder);
        }

        if($status){
            $this->db->where("a.status", $status);
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->display_name = "<b>" . $name . "</b><br>" . $rs->position;
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

    private function get_searched_itemv1($query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null)
    {
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "comp.code", "pos.name",
                "a.reference_no", "a.date_trans", "c.asset_name", "b.firstname", "b.middlename", "b.lastname",
                "CONCAT(b.firstname,' ',b.lastname)");
            $sql = "a.id, a.status,  b.firstname, 
                    b.lastname, b.middlename, b.suffix, 
                    IF(pos.`name` IS NULL, a.position, pos.`name`) position, 
                    IF(comp.`code` IS NULL, a.company, comp.`code`) company,
                    a.reference_no, 
                    a.date_trans, c.asset_name";

            $this->db->select($sql);
            $this->db->from("gcceforms.borrowing a");
            $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
            $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");
            $this->db->where('a.status != ', 'Cancelled');
            $this->db->where('a.date_trans >= ', $check);
            if($query_builder){
                $this->db->where($query_builder);
            }

            if($status){
                $this->db->where("a.status", $status);
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
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if (is_numeric($rs->company)) {
                        $rs->company = $this->getCompany($rs->company);

                    } else {
                        $rs->company = $rs->company;
                    }

                    if (is_numeric($rs->position)) {
                        $rs->position = $this->getPosition($rs->position);

                    } else {
                        $rs->position = $rs->position;
                    }

                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $rs->display_name = "<b>" . $name . "</b><br>" . $rs->position;
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

    private function get_searched_item_countv1($query_builder=null, $search = null, $status = null)
    {
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "comp.code", "pos.name",
                "a.reference_no", "a.date_trans", "c.asset_name", "b.firstname", "b.middlename", "b.lastname",
                "CONCAT(b.firstname,' ',b.lastname)");
            $sql = "a.id, a.status,  b.firstname, 
                    b.lastname, b.middlename, b.suffix, 
                    IF(pos.`name` IS NULL, a.position, pos.`name`) position, 
                    IF(comp.`code` IS NULL, a.company, comp.`code`) company,
                    a.reference_no, 
                    a.date_trans, c.asset_name";

            $this->db->select($sql);
            $this->db->from("gcceforms.borrowing a");
            $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
            $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");
            $this->db->where('a.status != ', 'Cancelled');
            $this->db->where('a.date_trans >= ', $check);
            if($query_builder){
                $this->db->where($query_builder);
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
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function getCompany($id) {
        $this->db->select("id, description");
        $this->db->from("gcchris.tblcompanies");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->description;
    }

    function getPosition($id) {
        $this->db->select("id, name");
        $this->db->from("gcchris.tblposition");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->name;
    }

    function getDepartment($id) {
        $this->db->select("id, description");
        $this->db->from("gcchris.tbldepartments");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->description;
    }

    function getArchiveRequest() {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        $rowData = $this->get_all_archived_item($search, $limit, $offset, $sortBy, $sortOrder, $view_by_company, $companyDescription);
        $rowCount = $this->get_all_archived_item_count($search, $view_by_company, $companyDescription);
        // if (!$search) {
        //     $rowData = $this->get_all_archive($limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_all_archive_count();
        // }

        // if ($search) {
        //     $rowData = $this->get_searched_archive_item($search, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_searched_archive_item_count($search);
        //     $this->core_layout->setEventLog("Archive Borrowing - Search {$search} in datatable.", "search", "success", "gcceforms", "user");
        // }

        if ($search ) {
            $this->core_layout->setEventLog("Archive Borrowing - Search {$search} in datatable.", "search", "success", "gcceforms", "user");
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_archived_item($search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $view_by_company = false, $companyDescription = null) {
        $check = date("Y-m-d", strtotime("-1 year", time()));
        $data = array();

        $filterFields = array("a.id", "a.status", "comp.code", "pos.name", "a.reference_no", "a.date_trans", "c.asset_name", "b.firstname", "b.middlename", "b.lastname");
        
        $sql = "a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, IF(pos.`name` IS NULL, a.position, pos.`name`) position, IF(comp.`code` IS NULL, a.company, comp.`code`) company, a.reference_no, a.date_trans, c.asset_name";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");

        $this->db->group_start();
            $this->db->where('a.status', 'Cancelled');
            $this->db->or_where('DATE(a.date_trans) <=', $check);
        $this->db->group_end();

        if ($view_by_company) {
            $this->db->where('a.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('a.company', $companyDescription);
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

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();

            foreach ($query->result() as $key => $rs) {
                $rs->company = (is_numeric($rs->company)) ? $this->getCompany($rs->company) : $rs->company;
                $rs->position = (is_numeric($rs->position)) ? $this->getPosition($rs->position) : $rs->position;

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                // $rs->display_name = "<b>" . $name . "</b><br>" . $rs->position;
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    function get_all_archived_item_count($search = null, $view_by_company = false, $companyDescription = null) {
        $check = date("Y-m-d", strtotime("-1 year", time()));
        
        $filterFields = array("a.id", "a.status", "comp.code", "pos.name", "a.reference_no", "a.date_trans", "c.asset_name", "b.firstname", "b.middlename", "b.lastname");
            
        $sql = "a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, IF(pos.`name` IS NULL, a.position, pos.`name`) position, IF(comp.`code` IS NULL, a.company, comp.`code`) company, a.reference_no, a.date_trans, c.asset_name";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");

        $this->db->group_start();
            $this->db->where('a.status', 'Cancelled');
            $this->db->or_where('DATE(a.date_trans) <=', $check);
        $this->db->group_end();

        if ($view_by_company) {
            $this->db->where('a.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('a.company', $companyDescription);
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

        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_archive_countv1()
    {
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
        $this->db->where("(a.status='Cancelled' OR a.date_trans<='$check')");
        $query = $this->db->get();

        return $query->num_rows();
    }

    private function get_all_archivev1($limit = 10, $offset = 0, $sortBy, $sortOrder)
    {

        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);

        $sql = "a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, 
            IF(pos.`name` IS NULL, a.position, pos.`name`) position, 
            IF(comp.`code` IS NULL, a.company, comp.`code`) company, 
            a.reference_no, a.date_trans, c.asset_name";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");

        $this->db->where("(a.status='Cancelled' OR a.date_trans<='$check')");
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                if (is_numeric($rs->company)) {
                    $rs->company = $this->getCompany($rs->company);

                } else {
                    $rs->company = $rs->company;
                }

                if (is_numeric($rs->position)) {
                    $rs->position = $this->getPosition($rs->position);

                } else {
                    $rs->position = $rs->position;
                }

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->display_name = "<b>" . $name . "</b><br>" . $rs->position;
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

    private function get_searched_archive_itemv1($search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder)
    {
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "comp.code", "pos.name", "a.reference_no", 
            "a.date_trans", "c.asset_name", "b.firstname", "b.middlename", "b.lastname");
            
            $sql = "a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, 
            IF(pos.`name` IS NULL, a.position, pos.`name`) position, 
            IF(comp.`code` IS NULL, a.company, comp.`code`) company, 
            a.reference_no, a.date_trans, c.asset_name";

            $this->db->select($sql);
            $this->db->from("gcceforms.borrowing a");
            $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
            $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");
            $this->db->where("(a.status='Cancelled' OR a.date_trans<='$check')");
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if (is_numeric($rs->company)) {
                        $rs->company = $this->getCompany($rs->company);

                    } else {
                        $rs->company = $rs->company;
                    }

                    if (is_numeric($rs->position)) {
                        $rs->position = $this->getPosition($rs->position);

                    } else {
                        $rs->position = $rs->position;
                    }

                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $rs->display_name = "<b>" . $name . "</b><br>" . $rs->position;
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

    private function get_searched_archive_item_countv1($search = null)
    {
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "comp.code", "pos.name", "a.reference_no", 
            "a.date_trans", "c.asset_name", "b.firstname", "b.middlename", "b.lastname");
            
            $sql = "a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, 
            IF(pos.`name` IS NULL, a.position, pos.`name`) position, 
            IF(comp.`code` IS NULL, a.company, comp.`code`) company, 
            a.reference_no, a.date_trans, c.asset_name";

            $this->db->select($sql);
            $this->db->from("gcceforms.borrowing a");
            $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
            $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");
            $this->db->where("(a.status='Cancelled' OR a.date_trans<='$check')");
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

    function getBorrowedRequest() {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        $rowData = $this->get_all_borrowed_item($query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $view_by_company, $companyDescription);
        $rowCount = $this->get_all_borrowed_item_count($query_builder, $search, $view_by_company, $companyDescription);
        // if (!$search) {
        //     $rowData = $this->get_all_borrowed($query_builder, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_all_borrowed_count($query_builder);
        // }

        // if ($search) {
        //     $rowData = $this->get_searched_borrowed_item($query_builder, $search, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_searched_borrowed_item_count($query_builder, $search);
        // }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_borrowed_item($query_builder, $search = null, $limit, $offset, $sortBy, $sortOrder, $view_by_company = false, $companyDescription = null) {
        $data = array();

        $filterFields = array("a.id", "comp.code", "dept.code", "pos.name", "c.reference_no", "a.asset_name", "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname");

        $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, IF(comp.`code` IS NULL, c.company, comp.`code`) company, IF(dept.`code` IS NULL, c.department, dept.`code`) department, IF(pos.`name` IS NULL, c.position, pos.`name`) position, c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, a.date_borrowed, a.date_due, c.status ";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
        $this->db->where('a.is_returned', '0');
        $this->db->where('c.status !=', 'Pending');

        if ($view_by_company) {
            $this->db->where('c.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('c.company', $companyDescription);
            }
        }

        if (isset($query_builder) && $query_builder) {
            $this->db->where($query_builder);
        }

        if (isset($search) && $search){
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

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();

            foreach ($query->result() as $key => $rs) {
                $rs->company = (is_numeric($rs->company)) ? $this->getCompany($rs->company) : $rs->company;
                $rs->department = (is_numeric($rs->department)) ? $this->getDepartment($rs->department) : $rs->department;
                $rs->position = (is_numeric($rs->position)) ? $this->getPosition($rs->position) : $rs->position;

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                // $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company . "<br>" . $rs->department . "<br>" . $rs->position;
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    function get_all_borrowed_item_count($query_builder=null, $search = null, $view_by_company = false, $companyDescription = null) {
        $filterFields = array("a.id", "comp.code", "dept.code", "pos.name", "c.reference_no", "a.asset_name", "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname");
        
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
        $this->db->where('a.is_returned', '0');
        $this->db->where('c.status !=', 'Pending');

        if ($view_by_company) {
            $this->db->where('c.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('c.company', $companyDescription);
            }
        }

        if (isset($query_builder) && $query_builder) {
            $this->db->where($query_builder);
        }

        if (isset($search) && $search){
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

    private function get_all_borrowed_countv1($query_builder=null)
    {
        $sql = "a.id, b.firstname, b.middlename, b.lastname,b.suffix, 
            IF(comp.`code` IS NULL, c.company, comp.`code`) company,
            IF(dept.`code` IS NULL, c.department, dept.`code`) department,
            IF(pos.`name` IS NULL, c.position, pos.`name`) position,
            c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, a.date_borrowed, a.date_due";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
		$this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
        $this->db->where('a.is_returned', '0');
        $this->db->where('c.status !=', 'Pending');

        if($query_builder){
            $this->db->where($query_builder);
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_borrowedv1($query_builder=null, $limit = 10, $offset = 0, $sortBy, $sortOrder)
    {
        $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, 
            IF(comp.`code` IS NULL, c.company, comp.`code`) company,
            IF(dept.`code` IS NULL, c.department, dept.`code`) department,
            IF(pos.`name` IS NULL, c.position, pos.`name`) position,
            c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, a.date_borrowed, a.date_due, c.status ";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
		$this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
        $this->db->where('a.is_returned', '0');
        $this->db->where('c.status !=', 'Pending');

        if($query_builder){
            $this->db->where($query_builder);
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                if (is_numeric($rs->company)) {
                    $rs->company = $this->getCompany($rs->company);

                } else {
                    $rs->company = $rs->company;
                }

                if (is_numeric($rs->department)) {
                    $rs->department = $this->getDepartment($rs->department);

                } else {
                    $rs->department = $rs->department;
                }

                if (is_numeric($rs->position)) {
                    $rs->position = $this->getPosition($rs->position);

                } else {
                    $rs->position = $rs->position;
                }

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company . "<br>" . $rs->department . "<br>" . $rs->position;
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


    private function get_searched_borrowed_itemv1($query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "comp.code", "dept.code", "pos.name", "c.reference_no", 
                "a.asset_name", "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname");

            $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, 
                IF(comp.`code` IS NULL, c.company, comp.`code`) company,
                IF(dept.`code` IS NULL, c.department, dept.`code`) department,
                IF(pos.`name` IS NULL, c.position, pos.`name`) position,
                c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, a.date_borrowed, a.date_due, c.status ";

            $this->db->select($sql);
            $this->db->from("gcceforms.borrowing_body a");
            $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
            $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
            $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
            $this->db->where('a.is_returned', '0');
            $this->db->where('c.status !=', 'Pending');

            if($query_builder){
                $this->db->where($query_builder);
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
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if (is_numeric($rs->company)) {
                        $rs->company = $this->getCompany($rs->company);

                    } else {
                        $rs->company = $rs->company;
                    }

                    if (is_numeric($rs->department)) {
                        $rs->department = $this->getDepartment($rs->department);

                    } else {
                        $rs->department = $rs->department;
                    }

                    if (is_numeric($rs->position)) {
                        $rs->position = $this->getPosition($rs->position);

                    } else {
                        $rs->position = $rs->position;
                    }

                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company . "<br>" . $rs->department . "<br>" . $rs->position;
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

    private function get_searched_borrowed_item_countv1($query_builder=null, $search = null)
    {
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "comp.code", "dept.code", "pos.name", "c.reference_no", 
                "a.asset_name", "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname");

            $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, 
                IF(comp.`code` IS NULL, c.company, comp.`code`) company,
                IF(dept.`code` IS NULL, c.department, dept.`code`) department,
                IF(pos.`name` IS NULL, c.position, pos.`name`) position,
                c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, a.date_borrowed, a.date_due";

            $this->db->select($sql);
            $this->db->from("gcceforms.borrowing_body a");
            $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
            $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
            $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
            $this->db->where('a.is_returned', '0');
            $this->db->where('c.status !=', 'Pending');

            if($query_builder){
                $this->db->where($query_builder);
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
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function getReturnedRequest() {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column" => "0", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        $rowData = $this->get_all_return_item($query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $view_by_company, $companyDescription);
        $rowCount = $this->get_all_return_item_count($query_builder, $search, $view_by_company, $companyDescription);
        // if (!$search) {
        //     $rowData = $this->get_all_return($query_builder, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_all_return_count($query_builder);
        // }

        // if ($search) {
        //     $rowData = $this->get_searched_return_item($query_builder, $search, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_searched_return_item_count($query_builder, $search);
        // }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_return_item($query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $view_by_company = false, $companyDescription = null) {
        $data = array();

        $filterFields = array("a.id", "comp.code", "dept.code", "pos.name", "c.reference_no", "a.asset_name", "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname", "a.date_returned", "a.return_remarks");

        $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, IF(comp.`code` IS NULL, c.company, comp.`code`) company, IF(dept.`code` IS NULL, c.department, dept.`code`) department, IF(pos.`name` IS NULL, c.position, pos.`name`) position, c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, a.date_borrowed, a.date_due, a.date_returned, a.return_remarks";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
        $this->db->where('a.is_returned', '1');

        if ($view_by_company) {
            $this->db->where('c.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('c.company', $companyDescription);
            }
        }

        if (isset($query_builder) && $query_builder) {
            $this->db->where($query_builder);
        }

        if (isset($search) && $search) {
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

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if ($query->num_rows() > 0) { 
            $arrData = array();

            foreach ($query->result() as $key => $rs) {
                $rs->company = (is_numeric($rs->company)) ? $this->getCompany($rs->company) : $rs->company; 
                $rs->company = (is_numeric($rs->department)) ? $this->getDepartment($rs->department) : $rs->department; 
                $rs->company = (is_numeric($rs->position)) ? $this->getPosition($rs->position) : $rs->position; 

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                // $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company . "<br>" . $rs->department . "<br>" . $rs->position;
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    function get_all_return_item_count($query_builder=null, $search = null, $view_by_company = false, $companyDescription = null) {
        $filterFields = array("a.id", "comp.code", "dept.code", "pos.name", "c.reference_no", "a.asset_name", "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname", "a.date_returned", "a.return_remarks");

        $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, IF(comp.`code` IS NULL, c.company, comp.`code`) company, IF(dept.`code` IS NULL, c.department, dept.`code`) department, IF(pos.`name` IS NULL, c.position, pos.`name`) position, c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, a.date_borrowed, a.date_due, a.date_returned, a.return_remarks";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
        $this->db->where('a.is_returned', '1');

        if ($view_by_company) {
            $this->db->where('c.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('c.company', $companyDescription);
            }
        }
        
        if (isset($query_builder) && $query_builder) {
            $this->db->where($query_builder);
        }

        if (isset($search) && $search) {
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

    private function get_all_return_countv1($query_builder=null)
    {

        $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, 
        IF(comp.`code` IS NULL, c.company, comp.`code`) company, 
        IF(dept.`code` IS NULL, c.department, dept.`code`) department,
        IF(pos.`name` IS NULL, c.position, pos.`name`) position, 
        c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, 
        a.date_borrowed, a.date_due, a.date_returned, a.return_remarks";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
        $this->db->where('is_returned', '1');
        if($query_builder){
            $this->db->where($query_builder);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_returnv1($query_builder=null, $limit = 10, $offset = 0, $sortBy, $sortOrder)
    {

        $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, 
        IF(comp.`code` IS NULL, c.company, comp.`code`) company, 
        IF(dept.`code` IS NULL, c.department, dept.`code`) department,
        IF(pos.`name` IS NULL, c.position, pos.`name`) position, 
        c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, 
        a.date_borrowed, a.date_due, a.date_returned, a.return_remarks";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
        $this->db->where('is_returned', '1');
        if($query_builder){
            $this->db->where($query_builder);
        }
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                if (is_numeric($rs->company)) {
                    $rs->company = $this->getCompany($rs->company);

                } else {
                    $rs->company = $rs->company;
                }

                if (is_numeric($rs->department)) {
                    $rs->department = $this->getDepartment($rs->department);

                } else {
                    $rs->department = $rs->department;
                }

                if (is_numeric($rs->position)) {
                    $rs->position = $this->getPosition($rs->position);

                } else {
                    $rs->position = $rs->position;
                }

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company . "<br>" . $rs->department . "<br>" . $rs->position;
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


    private function get_searched_return_itemv1($query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "comp.code", "dept.code", "pos.name", "c.reference_no", "a.asset_name",
                "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname", "a.date_returned", "a.return_remarks");

            $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, 
            IF(comp.`code` IS NULL, c.company, comp.`code`) company, 
            IF(dept.`code` IS NULL, c.department, dept.`code`) department,
            IF(pos.`name` IS NULL, c.position, pos.`name`) position, 
            c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, 
            a.date_borrowed, a.date_due, a.date_returned, a.return_remarks";

            $this->db->select($sql);
            $this->db->from("gcceforms.borrowing_body a");
            $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
            $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
            $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
            $this->db->where('a.is_returned', '1');
            if($query_builder){
                $this->db->where($query_builder);
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
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if (is_numeric($rs->company)) {
                        $rs->company = $this->getCompany($rs->company);

                    } else {
                        $rs->company = $rs->company;
                    }

                    if (is_numeric($rs->department)) {
                        $rs->department = $this->getDepartment($rs->department);

                    } else {
                        $rs->department = $rs->department;
                    }

                    if (is_numeric($rs->position)) {
                        $rs->position = $this->getPosition($rs->position);

                    } else {
                        $rs->position = $rs->position;
                    }

                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $rs->display_name = "<b>" . $rs->display_name . "</b><br>" . $rs->company . "<br>" . $rs->department . "<br>" . $rs->position;
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

    private function get_searched_return_item_countv1($query_builder=null, $search = null)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "comp.code", "dept.code", "pos.name", "c.reference_no", "a.asset_name",
                "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname", "a.date_returned", "a.return_remarks");

            $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, 
                IF(comp.`code` IS NULL, c.company, comp.`code`) company, 
                IF(dept.`code` IS NULL, c.department, dept.`code`) department,
                IF(pos.`name` IS NULL, c.position, pos.`name`) position, 
                c.reference_no, CONCAT('<b>', a.asset_code, '</b><br>', a.asset_name) As asset, 
                a.date_borrowed, a.date_due, a.date_returned, a.return_remarks";

            $this->db->select($sql);
            $this->db->from("gcceforms.borrowing_body a");
            $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
            $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
            $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
            $this->db->where('a.is_returned', '1');
            if($query_builder){
                $this->db->where($query_builder);
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
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function getAssetCodeBorrowingBody($id){
        return $this->db->get_where("gcceforms.borrowing_body", array("id"=>$id))->row('asset_code');
    }

    function overdue_count()
    {
        date_default_timezone_set('Asia/Singapore');
        $check = date('Y-m-d H:i:s');
        $this->db->from($this->borrowing_body);
        $this->db->where("(is_returned='0' OR date_due<='$check')");
        $query = $this->db->get();

        return $query->num_rows();
    }

    function getOverdueRequest(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        $rowData = $this->get_all_overdue_item($query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $view_by_company, $companyDescription);
        $rowCount = $this->get_all_overdue_item_count($query_builder, $search, $view_by_company, $companyDescription);
        // if (!$search) {
        //     $rowData = $this->get_all_overdue($query_builder, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_all_overdue_count($query_builder);
        // }

        // if ($search) {
        //     $rowData = $this->get_searched_overdue_item($query_builder, $search, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_searched_overdue_item_count($query_builder, $search);
        // }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_overdue_item($query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $view_by_company = false, $companyDescription = null){
        date_default_timezone_set('Asia/Singapore');
        $check = date('Y-m-d H:i:s');
        $data = array();

        $filterFields = array("a.id", "comp.code", "c.reference_no", "a.asset_name", "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname");

        $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, IF(comp.`code` IS NULL, c.company, comp.`code`) company, IF(dept.`code` IS NULL, c.department, dept.`code`) department, IF(pos.`name` IS NULL, c.position, pos.`name`) position, c.reference_no, a.asset_code, a.asset_name, a.date_borrowed, a.date_due";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");

        $this->db->group_start();
            $this->db->where('a.is_returned', 0);
            $this->db->where('a.date_due <=', $check);
        $this->db->group_end();

        if ($view_by_company) {
            $this->db->where('c.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('c.company', $companyDescription);
            }
        }

        if (isset($query_builder) && $query_builder) {
            $this->db->group_start();
            $this->db->where($query_builder);
            $this->db->group_end();
        }

        if (isset($search) && $search) {
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

        if ($limit != -1 ){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            
            foreach ($query->result() as $key => $rs) {
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    function get_all_overdue_item_count($query_builder=null, $search = null, $view_by_company = false, $companyDescription = null){
        date_default_timezone_set('Asia/Singapore');
        $check = date('Y-m-d H:i:s');

        $filterFields = array("a.id", "comp.code", "c.reference_no", "a.asset_name", "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname");

        $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, IF(comp.`code` IS NULL, c.company, comp.`code`) company, IF(dept.`code` IS NULL, c.department, dept.`code`) department, IF(pos.`name` IS NULL, c.position, pos.`name`) position, c.reference_no, a.asset_code, a.asset_name, a.date_borrowed, a.date_due";
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");

        $this->db->group_start();
            $this->db->where('a.is_returned', 0);
            $this->db->where('a.date_due <=', $check);
        $this->db->group_end();

        if ($view_by_company) {
            $this->db->where('c.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('c.company', $companyDescription);
            }
        }

        if (isset($query_builder) && $query_builder) {
            $this->db->group_start();
            $this->db->where($query_builder);
            $this->db->group_end();
        }

        if (isset($search) && $search) {
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

    private function get_all_overdue_countv1($query_builder=null) {
        date_default_timezone_set('Asia/Singapore');
        $check = date('Y-m-d H:i:s');

        $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, 
            IF(comp.`code` IS NULL, c.company, comp.`code`) company, 
            IF(dept.`code` IS NULL, c.department, dept.`code`) department,
            IF(pos.`name` IS NULL, c.position, pos.`name`) position, 
            c.reference_no, a.asset_code, a.asset_name, a.date_borrowed, a.date_due";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
        $this->db->where("(a.is_returned='0' AND a.date_due<='$check')");
        if($query_builder){
            $this->db->group_start();
            $this->db->where($query_builder);
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_overduev1($query_builder=null, $limit = 10, $offset = 0, $sortBy, $sortOrder){
        date_default_timezone_set('Asia/Singapore');
        $check = date('Y-m-d H:i:s');
        $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, 
            IF(comp.`code` IS NULL, c.company, comp.`code`) company, 
            IF(dept.`code` IS NULL, c.department, dept.`code`) department,
            IF(pos.`name` IS NULL, c.position, pos.`name`) position, 
            c.reference_no, a.asset_code, a.asset_name, a.date_borrowed, a.date_due";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
        $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
        $this->db->where("(a.is_returned='0' AND a.date_due <= '$check')");

        if($query_builder){
            $this->db->group_start();
            $this->db->where($query_builder);
            $this->db->group_end();
        }
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
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
    }

    private function get_searched_overdue_itemv1($query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
        date_default_timezone_set('Asia/Singapore');
        $check = date('Y-m-d H:i:s');
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "comp.code", "c.reference_no", "a.asset_name", 
            "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname");

            $sql = "a.id, b.firstname, b.middlename, b.lastname, b.suffix, 
                IF(comp.`code` IS NULL, c.company, comp.`code`) company, 
                IF(dept.`code` IS NULL, c.department, dept.`code`) department,
                IF(pos.`name` IS NULL, c.position, pos.`name`) position, 
                c.reference_no, a.asset_code, a.asset_name, a.date_borrowed, a.date_due";

            $this->db->select($sql);
            $this->db->from("gcceforms.borrowing_body a");
            $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
            $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
            $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
            $this->db->where("(a.is_returned='0' AND a.date_due<='$check')");

            if($query_builder){
                $this->db->group_start();
                $this->db->where($query_builder);
                $this->db->group_end();
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
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
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

    private function get_searched_overdue_item_countv1($query_builder=null, $search = null) {
        date_default_timezone_set('Asia/Singapore');
        $check = date('Y-m-d H:i:s');
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "comp.code", "c.reference_no", "a.asset_name", 
            "a.asset_code", "a.date_borrowed", "a.date_due", "b.firstname", "b.middlename", "b.lastname");

            $sql = "a.id, CONCAT( b.firstname,b.middlename,b.lastname) AS name, 
                IF(comp.`code` IS NULL, c.company, comp.`code`) company, 
                IF(dept.`code` IS NULL, c.department, dept.`code`) department,
                IF(pos.`name` IS NULL, c.position, pos.`name`) position, 
                c.reference_no, a.asset_code, a.asset_name, a.date_borrowed, a.date_due";

            $this->db->select($sql);
            $this->db->from("gcceforms.borrowing_body a");
            $this->db->join("gcceforms.borrowing c", "a.borrowing_id = c.id", "LEFT");
            $this->db->join("gccmaster.tblemployees b", "c.borrower = b.id", "LEFT");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = c.company", "LEFT");
            $this->db->join("gcchris.tbldepartments dept", "dept.id = c.department", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = c.position", "LEFT");
            $this->db->where("(a.is_returned='0' AND a.date_due<='$check')");
            if($query_builder){
                $this->db->group_start();
                $this->db->where($query_builder);
                $this->db->group_end();
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
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function getEmployeeCollection() {
        $get = $this->input->get();
        $resultarray = array();
        // if (isset($get['q'])) {
        //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%') ORDER BY firstname ASC LIMIT 10");
        // } else {
        //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC LIMIT 10");
        // }

        $sql = "id, firstname, lastname, middlename, suffix";
        $this->db->select($sql);
        
        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
                $this->db->like('firstname', $get['q'], 'both');
                $this->db->or_like('lastname', $get['q'], 'both');
            $this->db->group_end();
        }

        $this->db->where('employee_status', 'Active');
        $this->db->order_by('firstname', 'ASC');
        $this->db->limit(10);
        $this->db->from('gccmaster.tblemployees');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $tempRs = (array)$_query;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;

                $data["id"] = $_query["id"];
                $data["text"] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    function getTempRequest($id){
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";
        $rowData = array();
        
        $rowData = $this->get_all_post_temp($limit, $offset, $sortBy, $sortOrder, $id);
        $resultset["data"] = $rowData;
        
        return $resultset;
    }


    private function get_all_post_temp($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $id){
        $sql = "a.id, b.purchaseprice, c.purchaseprice, a.asset_id, a.asset_code, 
        a.asset_name, a.type, a.price, CONCAT(a.quantity, ' ', a.uom) AS pieces, 
        a.date_borrowed, a.date_due, a.date_returned, a.is_overdue, a.is_returned, 
        a.remarks, a.is_vehicle, b.assetname, c.description";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body_temp a");
        $this->db->join("gccasset.assets b", "a.asset_id=b.id", "LEFT");
        $this->db->join("gccasset.vehicles c", "a.asset_id=c.id", "LEFT");
        $this->db->where('user_id', $id);
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
                if ($rs->type == "asset") {
                    $rs->desc = $rs->assetname;
                    $rs->item_borrowed = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name . "<br>" . $rs->desc;
                }

                if ($rs->type == "vehicle") {
                    $rs->desc = $rs->description;
                    $rs->item_borrowed = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name . "<br>" . $rs->desc;
                }

                if ($rs->type == "sample") {
                    $rs->desc = "";
                    $rs->item_borrowed = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name;
                }


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

    function getContentRequest($id) {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 50;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_all_post_content($limit=50, $offset, $sortBy, $sortOrder, $id);
        $resultset["data"] = $rowData;

        return $resultset;
    }


    private function get_all_post_content($limit, $offset = 0, $sortBy = null, $sortOrder = "DESC", $id) {
        $sql = "a.id, a.type, a.asset_id, a.asset_code, a.asset_name, 
        CONCAT(a.quantity,' ', a.uom) AS pieces, a.date_borrowed, 
        a.date_due, a.date_returned, a.is_overdue, a.is_returned, 
        a.remarks, a.is_vehicle, a.new_due, a.due_reason, a.asset_name, b.assetname, c.description, b.brand as a_brand, b.modelno as a_model, c.brand as v_brand, c.model as v_model, c.purchaseprice as v_amount, b.purchaseprice as a_amount";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gccasset.assets b", "a.asset_id=b.id", "LEFT");
        $this->db->join("gccasset.vehicles c", "a.asset_id=c.id", "LEFT");
        $this->db->where('borrowing_id', $id);
        $this->db->limit($limit, $offset);

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            $total_amount = 0;
            foreach ($query->result() as $key => $rs) {

                $rs->asset_code = ($rs->asset_code) ? $rs->asset_code : "<b>No Asset Code</b>";
                $rs->asset_name = ($rs->asset_name) ? $rs->asset_name : "<b>No Asset Name</b>";
                if ($rs->type == "asset") {
                    $rs->desc = $rs->assetname;
                    $rs->asset = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name . "<br>" . $rs->desc;
                    if(isset($rs->a_brand, $rs->a_modelno) && $rs->a_brand && $rs->a_modelno){
                        $tempBrandModel = "{$rs->a_brand} / {$rs->a_modelno}";
                    }else if(isset($rs->a_brand) && $rs->a_brand){
                        $tempBrandModel = "{$rs->a_brand}";
                    }else if(isset($rs->a_modelno) && $rs->a_modelno){
                        $tempBrandModel = "{$rs->a_modelno}";
                    }else{
                        $tempBrandModel = "N/A";
                    }    
                    $rs->amount = $rs->a_amount ? number_format($rs->a_amount,2) : 0;
                }

                if ($rs->type == "vehicle") {
                    $rs->desc = $rs->description;
                    $rs->asset = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name . "<br>" . $rs->desc;

                    if(isset($rs->v_brand, $rs->v_model) && $rs->v_brand && $rs->v_model){
                        $tempBrandModel = "{$rs->v_brand} / {$rs->v_model}";
                    }elseif(isset($rs->v_brand) && $rs->v_brand){
                        $tempBrandModel = "{$rs->v_brand}";
                    }else if(isset($rs->v_model) && $rs->v_model){
                        $tempBrandModel = "{$rs->v_model}";
                    }

                    $rs->amount = $rs->v_amount ? number_format($rs->v_amount,2) : 0;
                }

                if ($rs->type == "sample") {
                    $rs->desc = "";
                    $rs->asset = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name;
                    $tempBrandModel = "N/A";
                }

                if ($rs->type == "") {
                    $rs->asset = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name;
                    $tempBrandModel = "N/A";
                }
                $rs->brand_model = $tempBrandModel;
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

    public function delete_temp_all($id) {
        $this->db->where('user_id', $id);
        if($this->db->delete('gcceforms.borrowing_body_temp')){
            $message = "New Borrowing - Delete all item for borrowing.";
            $type = "success";
            $table = "user";
        }else{
            $message = "New Borrowing - Failed delete all item borrowed.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
    }

    function getVehicleCollection(){
        $get = $this->input->get();
        $resultarray = array();

        $this->db->from('gccasset.vehicles');
        $this->db->where('isCompo', 1);
        $this->db->where('is_borrowed', 0);
        $this->db->where('name != ', NULL);

        $this->db->group_start();
            $this->db->where('status2', 'operational');
            $this->db->or_where('status2', 'brandnew');
            $this->db->or_where('status2', 'surplus');
        $this->db->group_end();

        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
                $this->db->like('gen_code', $get['q'], 'both');
                $this->db->or_like('gen_code', $get['q'], 'both');
            $this->db->group_end();
        }

        $this->db->order_by('gen_code', 'asc');
        $this->db->limit(10);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["gen_code"];
                $resultarray[] = $data;
            }
        }

        return array('results' => $resultarray);
    }

    function getVehicleCollectionv1() {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {

            $this->db->from('gccasset.vehicles');
            $this->db->where('isCompo', 1);
            $this->db->where('is_borrowed', 0);
            $this->db->where('name != ', NULL);

            $this->db->group_start();
            $this->db->where('status2', 'operational');
            $this->db->or_where('status2', 'brandnew');
            $this->db->or_where('status2', 'surplus');
            $this->db->group_end();

            $this->db->order_by('gen_code', 'asc');
            $this->db->like('gen_code', $get['q']);

            $this->db->group_start();
            $this->db->or_like('gen_code', $get['q']);
            $this->db->group_end();

            $this->db->limit(10);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["gen_code"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        } else {
            $this->db->from('gccasset.vehicles');
            $this->db->where('isCompo', 1);
            $this->db->where('is_borrowed', 0);
            $this->db->group_start();
            $this->db->where('status2', 'operational');
            $this->db->or_where('status2', 'brandnew');
            $this->db->group_end();
            $this->db->where('name != ', NULL);
            $this->db->order_by('gen_code', 'asc');
            $this->db->limit(10);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["gen_code"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }
    }

    function getAssetCollection(){
        $get = $this->input->get();
        $resultarray = array();

        $this->db->from('gccasset.assets');
        $this->db->where('is_borrowed', 0);
        $this->db->where('name != ', '');
        
        $this->db->group_start();
            $this->db->where('status', 'operational');
            $this->db->or_where('status', 'brandnew');
        $this->db->group_end();

        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
                $this->db->like('assetacode', $get['q'], 'both');
                $this->db->or_like('assetacode', $get['q'], 'both');
            $this->db->group_end();
        }

        $this->db->order_by('assetacode', 'asc');
        $this->db->limit(10);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["assetacode"];
                $resultarray[] = $data;
            }
        }

        return array("results" => $resultarray);
    }

    function getAssetCollectionv1(){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $this->db->from('gccasset.assets');
            $this->db->where('is_borrowed', 0);
            $this->db->group_start();
            $this->db->where('status', 'operational');
            $this->db->or_where('status', 'brandnew');
            $this->db->group_end();
            $this->db->where('name != ', '');
            $this->db->order_by('assetacode', 'asc');
            $this->db->like('assetacode', $get['q']);
            $this->db->group_start();
            $this->db->or_like('assetacode', $get['q']);
            $this->db->group_end();
            $this->db->limit(10);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["assetacode"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);

        } else {

            $this->db->from('gccasset.assets');
            $this->db->where('is_borrowed', 0);
            $this->db->group_start();
            $this->db->where('status', 'operational');
            $this->db->or_where('status', 'brandnew');
            $this->db->group_end();
            $this->db->where('name != ', '');
            $this->db->order_by('assetacode', 'asc');
            $this->db->limit(10);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["assetacode"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }
    }

    function vehicle_details($veh){
        $this->db->from('gccasset.vehicles');
        $this->db->where('id', $veh);
        
        $this->db->group_start();
        $this->db->where('status2', 'brandnew');
        $this->db->or_where('status2', 'operational');
        $this->db->group_end();

        $query = $this->db->get();
        return $query->row();
    }

    function asset_details($asset){
        $this->db->from('gccasset.assets');
        $this->db->where('id', $asset);
        
        $this->db->group_start();
        $this->db->where('status', 'brandnew');
        $this->db->or_where('status', 'operational');
        $this->db->group_end();
        
        $query = $this->db->get();
        return $query->row();
    }

    function emp_details($emp){
        $data = array();

        $this->db->select('firstname, lastname, suffix, middlename, company_id, department_id, position');
        $this->db->from('gccmaster.tblemployees');
        $this->db->where('id', $emp);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data[0];
    }

    public function borrowing_details($id) {
        $this->db->select("br.id, br.ref_yr, br.ref_series, br.ref_month, 
            br.reference_no, br.date_trans, br.purpose, br.`status`, 
            CONCAT(emp.firstname,' ' ,IF(emp.middlename IS NOT NULL AND emp.middlename !='' 
            AND emp.middlename != 'NA' AND emp.middlename != 'N/A' AND emp.middlename != 'NONE', 
            (CONCAT(LEFT(emp.middlename,1), '.')), '') , ' ', emp.lastname) borrower,
            IF(comp.`code` IS NULL, br.company, comp.`code`) company, 
            IF(pos.`name` IS NULL, br.position, pos.`name`) position, 
            IF(dep.`code` IS NULL, br.department, dep.`code`) department, 
            br.created_by, br.created_dt, br.last_edited_by, 
            br.last_edited_dt, br.last_edited_remarks, br.last_edited_id,
            br.approved_by, br.approved_dt, br.released_by, br.released_dt, br.released_remarks,
            br.cancelled_by, br.cancelled_remarks, br.date_needed, br.created_id, br.borrower as borrower_id");

        $this->db->join("gccmaster.tblemployees emp","emp.id = br.borrower","INNER");
        $this->db->join("gcchris.tblcompanies comp","comp.id = br.company","LEFT");
        $this->db->join("gcchris.tblposition pos","pos.id = br.position","LEFT");
        $this->db->join("gcchris.tbldepartments dep","dep.id = br.department","LEFT");

        $this->db->where('br.id', $id);
        $query = $this->db->get("gcceforms.borrowing br");

        return $query->row();
    }

    function getBorrowingReference($id){
        $this->db->select('borrowing_id');
        $borrowing_id = $this->db->get_where($this->borrowing_body, array("id"=>$id))->row('borrowing_id');

        $this->db->reset_query();

        $this->db->select('reference_no');
        return $this->db->get_where($this->borrowing, array("id"=>$borrowing_id))->row('reference_no');
    }

    public function save_temp_content($data) {
        $this->db->insert('gcceforms.borrowing_body_temp', $data);
        return $this->db->insert_id();
    }

    public function update_temp($where, $data) {
        $this->db->update('gcceforms.borrowing_body_temp', $data, $where);
        return $this->db->affected_rows();
    }
        
    public function update_content($where, $data) {
        $this->db->update($this->borrowing_body, $data, $where);
        return $this->db->affected_rows();
    }

    public function save_content($data) {
        $this->db->insert($this->borrowing_body, $data);
        return $this->db->insert_id();
    }

    public function delete_temp($id) {
        $this->db->where('id', $id);
        $removeTempData = $this->db->delete('gcceforms.borrowing_body_temp');
        return $removeTempData;
    }

    public function delete_content($id){
        $qTemp = $this->db->get_where($this->borrowing_body, array("id"=>$id));
        if ($qTemp->num_rows() == 1) {
            $isUpdated = false;
            $tempRow = $qTemp->row();

            if ($tempRow->type == "asset") {
                $isUpdated = $this->db->update("gccasset.assets", array("is_borrowed"=>0), array("id"=>$tempRow->asset_id));
            } elseif ($tempRow->type == "vehicle_component" || $tempRow->type == "vehicle") {
                $isUpdated = $this->db->update("gccasset.vehicles", array("is_borrowed"=>0), array("id"=>$tempRow->asset_id));
            }
            $this->db->where('id', $id);

            if ($this->db->delete($this->borrowing_body)) {
                $this->core_layout->setEventLog("Edit Borrowing - Remove {$tempRow->asset_code} from borrowing {$this->getBorrowingReference($id)}.","delete", "success", "gcceforms", "user");
            } else {
                $this->core_layout->setEventLog("Edit Borrowing - Failed remove {$tempRow->asset_code} from borrowing.","delete", "error", "gcceforms", "system");
            }
        }
    }

    public function delete_content_all($id) {
        $qTemp = $this->db->get_where($this->borrowing_body, array("borrowing_id"=>$id));

        if ($qTemp->num_rows() > 0) {
            foreach ($qTemp->result() as $key => $value) {

                if (isset($value->asset_id) && $value->type == "asset" && $value->asset_id) {
                    $this->db->update("gccasset.assets", array("is_borrowed"=>0), array("id"=>$value->asset_id));
                } elseif (isset($value->asset_id) && ($value->type == "vehicle_component" || $value->type == "vehicle") && $value->asset_id) {
                    $this->db->update("gccasset.vehicles", array("is_borrowed"=>0), array("id"=>$value->asset_id));
                }
            }

            $this->db->where('id', $id);
            $this->db->delete($this->borrowing_body);
        }
        $this->db->where('borrowing_id', $id);
        $this->db->delete($this->borrowing_body);
    }

    public function save($data) {
        $this->db->insert($this->borrowing, $data);
        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->borrowing, $data, $where);
        return $this->db->affected_rows();
    }

    public function get_contents($id) {
        $this->db->from('gcceforms.borrowing_body_temp');
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        return $query->result();
    }

    public function series($year, $month) {
        $this->db->select('ref_series');
        $this->db->from($this->borrowing);
        $this->db->where('ref_yr', $year);
        $this->db->where('ref_month', $month);
        $this->db->order_by('ref_series', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function edit_temp($id) {
        $this->db->from('gcceforms.borrowing_body_temp');
        $this->db->where('id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function edit_content($id) {
        $this->db->from($this->borrowing_body);
        $this->db->where('id', $id);
        $query = $this->db->get();

        return $query->row_array();
    }

    function getDaily() {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";
        $rowCount = 0;
        $rowData = array();
        $rowData = $this->get_all_post_daily($limit, $offset, $sortBy, $sortOrder);
        $resultset["data"] = $rowData;
        return $resultset;
    }

    private function get_all_post_daily($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
        $check = date('Y-m-d');

        $sql = "a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, 
        IF(comp.`code` IS NULL, a.company, comp.`code`) company, 
        IF(dept.`code` IS NULL, a.department, dept.`code`) department,
        IF(pos.`name` IS NULL, a.position, pos.`name`) position,
        a.reference_no, a.date_trans, c.asset_name";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = a.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");
        $this->db->like('DATE(a.created_dt)', $check);
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
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->display_name = "<b>" . $name . "</b><br>" . $rs->position;
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

    function getWeekly() {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";
        $rowCount = 0;
        $rowData = array();
        $rowData = $this->get_all_post_weekly($limit, $offset, $sortBy, $sortOrder);
        $resultset["data"] = $rowData;
        return $resultset;
    }

    private function get_all_post_weekly($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")  {

        $check = date('Y-m-d', strtotime("-7 days"));
        $sql = "a.id, a.status, b.firstname, b.lastname, b.middlename, b.suffix, 
        IF(comp.`code` IS NULL, a.company, comp.`code`) company, 
        IF(dept.`code` IS NULL, a.department, dept.`code`) department,
        IF(pos.`name` IS NULL, a.position, pos.`name`) position,
        a.reference_no, a.date_trans, c.asset_name";

        $this->db->select($sql);
        $this->db->from("gcceforms.borrowing a");
        $this->db->join("gccmaster.tblemployees b", "a.borrower = b.id", "LEFT");
        $this->db->join("gcceforms.borrowing_body c", "a.id = c.borrowing_id", "LEFT");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = a.company", "LEFT");
        $this->db->join("gcchris.tbldepartments dept", "dept.id = a.department", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = a.position", "LEFT");
        $this->db->where('DATE(a.created_dt) >= ', $check);
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
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->display_name = "<b>" . $name . "</b><br>" . $rs->position;
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

    function m_get_borrowing_analytics_for_dashboard() {

        $this->db->select("a.status, COUNT(a.id) AS count");
        $this->db->from("gcceforms.borrowing a");
        $this->db->group_by("a.status");
        $query = $this->db->get()->result();
        $result = json_decode(json_encode($query));

        $data = array($result[2], $result[0], $result[3], $result[1]);
        $results = json_decode(json_encode($data));
        return $results;
    }

    function list_name() {
        $this->db->from('sample_items');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->row();
    }

    public function get_by_id($id) {
        $this->db->from($this->borrowing);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_borrowed_items($id) {
        $this->db->from($this->borrowing_body);
        $this->db->where('borrowing_id', $id);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    public function getCurrentOverdueItems(){
        $arrData = array();
        $tempCount = 0;
        $tempData = array();

        $tempDate = date("Y-m-d H:i:s");
        $tempWhere = array();
        $tempWhere["DATE(a.date_due) != "] = "0000-00-00 00:00:00";
        $tempWhere["DATE(a.date_due) <= "] = $tempDate;
        $tempWhere["a.is_returned"] = 0;
        $tempWhere["b.status != "] = "Cancelled";

        $this->db->select("b.reference_no, upper(a.asset_name) as asset_name, a.date_due, a.quantity, DATEDIFF('{$tempDate}', a.date_due) as days");
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing b", "b.id = a.borrowing_id");
        $this->db->where($tempWhere);
        $this->db->order_by("a.date_due", "ASC");
        $this->db->limit(50);
        $temp = $this->db->get();
        if($temp->num_rows() > 0){
            $this->db->join("gcceforms.borrowing b", "b.id = a.borrowing_id");
            $qTemp = $this->db->get_where("gcceforms.borrowing_body a", $tempWhere);
            $tempCount = $qTemp->num_rows();
            $tempData = $temp->result();
        }
        $arrData["count"] = $tempCount;
        $arrData["data"] = $tempData;
        return $arrData;
    }

    public function getCurrentOverdueItemsDaily(){
        $arrData = array();
        $tempCount = 0;
        $tempData = array();

        $currDate = date("Y-m-d");
        $tempWhere = array();
        $tempWhere["DATE(a.date_due)"] = $currDate;
        $tempWhere["a.is_returned"] = 0;
        $tempWhere["b.status"] = "Released";

        $this->db->select("b.reference_no, upper(a.asset_name) as asset_name, a.date_due, a.quantity, UPPER(CONCAT(emp.lastname,', ',emp.firstname,' ',SUBSTRING(emp.middlename, 1, 1),'.')) as name, emp.mobile_no, user.email, b.borrower");
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing b", "b.id = a.borrowing_id");
        $this->db->join("gccmaster.tblemployees emp", "emp.id = b.borrower");
        $this->db->join("gccmaster.tblusers user", "user.emp_id = b.borrower", "LEFT");
        $this->db->where($tempWhere);
        $this->db->order_by("a.date_due", "ASC");
        $this->db->limit(50);
        $temp = $this->db->get();
        if($temp->num_rows() > 0){
            $this->db->join("gcceforms.borrowing b", "b.id = a.borrowing_id");
            $qTemp = $this->db->get_where("gcceforms.borrowing_body a", $tempWhere);
            $tempCount = $qTemp->num_rows();
            $tempData = $temp->result();
        }
        $arrData["count"] = $tempCount;
        $arrData["data"] = $tempData;
        return $arrData;
    }

    public function getCurrentOverdueItemsSummary(){
        $arrData = array();
        $tempCount = 0;
        $tempData = array();

        $tempWhere = array();
        $tempWhere["DATE(a.date_due) >= "] = date("Y-m-d", strtotime('monday this week'));
        $tempWhere["DATE(a.date_due) <= "] = date("Y-m-d", strtotime('saturday this week'));
        $tempWhere["a.is_returned"] = 0;
        $tempWhere["b.status"] = "Released";

        $this->db->select("b.reference_no, upper(a.asset_name) as asset_name, a.date_due, a.quantity, UPPER(CONCAT(emp.lastname,', ',emp.firstname,' ',SUBSTRING(emp.middlename, 1, 1),'.')) as name, b.borrower, a.asset_code");
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing b", "b.id = a.borrowing_id");
        $this->db->join("gccmaster.tblemployees emp", "emp.id = b.borrower");
        $this->db->where($tempWhere);
        $this->db->order_by("a.date_due", "ASC");
        $this->db->limit(50);
        $temp = $this->db->get();
        if($temp->num_rows() > 0){
            $this->db->join("gcceforms.borrowing b", "b.id = a.borrowing_id");
            $qTemp = $this->db->get_where("gcceforms.borrowing_body a", $tempWhere);
            $tempCount = $qTemp->num_rows();
            $tempData = $temp->result_array();
        }
        $arrData["count"] = $tempCount;
        $arrData["data"] = $tempData;
        return $arrData;
    }

    public function getCurrentOverdueItemsOfEmployee($group){
        $arrData = array();
        $tempCount = 0;
        $tempData = array();
        $currDate = date("Y-m-d");
        $tempWhere = array();
        if($group == 'employee'){
            $tempWhere["DATE(a.date_due)"] = $currDate;
        }else{
            $tempWhere["DATE(a.date_due) >= "] = date("Y-m-d", strtotime('monday this week'));
            $tempWhere["DATE(a.date_due) <= "] = date("Y-m-d", strtotime('saturday this week'));
        }
        $tempWhere["a.is_returned"] = 0;
        
        $this->db->select("b.reference_no, upper(a.asset_name) as asset_name, a.date_due, a.quantity, user.email, UPPER(CONCAT(emp.lastname,', ',emp.firstname,' ',SUBSTRING(emp.middlename, 1, 1),'.')) as name, b.borrower, emp.mobile_no, user.telegram_chat_id");
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing b", "b.id = a.borrowing_id", "LEFT");
        $this->db->join("gccmaster.tblusers user", "user.emp_id = b.borrower", "LEFT");
        $this->db->join("gccmaster.tblemployees emp", "emp.id = b.borrower", "LEFT");
        $this->db->where($tempWhere);
        $this->db->group_by("b.borrower");
        $this->db->order_by("a.date_due", "ASC");
        $this->db->limit(50);
        $temp = $this->db->get();
        if($temp->num_rows() > 0){
            $this->db->join("gcceforms.borrowing b", "b.id = a.borrowing_id");
            $qTemp = $this->db->get_where("gcceforms.borrowing_body a", $tempWhere);
            $tempCount = $qTemp->num_rows();
            $tempData = $temp->result();
        }
        $arrData["count"] = $tempCount;
        $arrData["data"] = $tempData;
        return $arrData;
    }

    function getBorrowedItemByEmployee($id,$group){
        $arrData = array();
        $tempCount = 0;
        $tempData = array();
        $tempAsset = array();

        $currDate = date("Y-m-d");
        $tempWhere = array();
        $tempWhere["b.borrower"] = $id;
        if($group == 'employee'){
            $tempWhere["DATE(a.date_due)"] = $currDate;
        }else{
            $tempWhere["DATE(a.date_due) >= "] = date("Y-m-d", strtotime('monday this week'));
            $tempWhere["DATE(a.date_due) <= "] = date("Y-m-d", strtotime('saturday this week'));
        }
        $tempWhere["a.is_returned"] = 0;
        // $tempWhere["b.status"] = "Released";

        $this->db->select("b.reference_no, upper(a.asset_name) as asset_name, a.date_due, a.quantity, user.email, UPPER(CONCAT(emp.lastname,', ',emp.firstname,' ',SUBSTRING(emp.middlename, 1, 1),'.')) as name, b.borrower, emp.mobile_no");
        $this->db->from("gcceforms.borrowing_body a");
        $this->db->join("gcceforms.borrowing b", "b.id = a.borrowing_id");
        $this->db->join("gccmaster.tblusers user", "user.emp_id = b.borrower");
        $this->db->join("gccmaster.tblemployees emp", "emp.id = b.borrower");
        $this->db->where($tempWhere);
        // $this->db->group_by("b.borrower");
        $this->db->order_by("a.date_due", "ASC");
        $this->db->limit(50);
        $temp = $this->db->get();
        if($temp->num_rows() > 0){
            $this->db->join("gcceforms.borrowing b", "b.id = a.borrowing_id");
            $qTemp = $this->db->get_where("gcceforms.borrowing_body a", $tempWhere);
            $tempCount = $qTemp->num_rows();
            $tempData = $temp->result();
        }
        $arrData['data'] = $tempData;
        return $arrData;
    }

    function exportData($export){
        if($export == 1){
            $this->core_layout->setEventLog("Borrowing Masterfile - Export excel file of Borrowing Masterfile.","export", "success", "gcceforms", "user");
        }elseif($export == 2){
            $this->core_layout->setEventLog("Borrowing Masterfile - Export csv file of Borrowing Masterfile.","export", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Borrowing Masterfile - Export pdf file of Borrowing Masterfile.","export", "success", "gcceforms", "user");
        }
    }

    function exportDataArchive($export){
        if($export == 1){
            $this->core_layout->setEventLog("Archive Borrowing - Export excel file of Archive Borrowing.","export", "success", "gcceforms", "user");
        }elseif($export == 2){
            $this->core_layout->setEventLog("Archive Borrowing - Export csv file of Archive Borrowing.","export", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Archive Borrowing - Export pdf file of Archive Borrowing.","export", "success", "gcceforms", "user");
        }
    }

    public function telegram_config_if_exist($module, $data){
        $this->db->where("module","borrowing");
        $this->db->order_by("created_at","DESC");
        $telegram_details = $this->db->get("gcceforms.telegram_config");
        $details = $telegram_details->row();
        $count = $telegram_details->num_rows();
        if($data == 'count'){
            return $count;
        }else{
            return $details;
        }
    }

    public function addTelegramConfig(){
        $post = $this->input->post();
        $data = array(
            "chat_id"=>$post['chat_id'],
            "telegram_bot_token"=>$post['telegram_bot_token'],
            "module"=>$post['module'],
            "created_at"=>date("Y-m-d H:i:s")
        );

        if ($post['id'] == "") {
            $result = $this->db->insert('gcceforms.telegram_config', $data);
            if ($result) {
                $message = "Add new telegram configuration.";
                $user_action = "add";
                $type = "success";
                $database = $this->eformsTable;
                $table = "user";
            } else {
                $message = "Failed adding telegram configuration.";
                $user_action = "add";
                $type = "error";
                $database = $this->eformsTable;
                $table = "system";
            }
        } else {
            $result = $this->db->update('gcceforms.telegram_config', $data, array("id"=>$post['id']));
            if ($result) {
                $message = "Update telegram configuration.";
                $user_action = "update";
                $type = "success";
                $database = $this->eformsTable;
                $table = "user";
            } else {
                $message = "Failed update telegram configuration.";
                $user_action = "update";
                $type = "error";
                $database = $this->eformsTable;
                $table = "system";
            }
        }
        $this->core_layout->setEventLog($message, $user_action, $type, $database, $table);

        return $result;
    }

    function massFixAction(){
        $this->db->select('id, borrower');
        $this->db->from($this->borrowing);
        $this->db->where('status', 'Released');

        $this->db->group_start();
            $this->db->like('company', 'undefined', 'both');
            $this->db->like('department', 'undefined', 'both');
            $this->db->like('position', 'undefined', 'both');
        $this->db->group_end();

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $this->db->select('a.company_id, c.id as department_id, d.id as position');
                $this->db->join('gcchris.tblcompanies as b', 'a.company_id = b.id', 'LEFT');
                $this->db->join('gcchris.tbldepartments as c', 'a.department_id = c.id OR a.department_id = c.description', 'LEFT');
                $this->db->join('gcchris.tblposition as d', 'a.position = d.id OR a.position = d.name', 'LEFT');
                $this->db->from('gccmaster.tblemployees as a');
                $this->db->where('a.id', $row->borrower);
                $q = $this->db->get()->row();

                $data = array(
                    'company' => $q->company_id,
                    'department' => $q->department_id, 
                    'position' => $q->position
                );

                $this->db->where('id', $row->id);
                $this->db->update($this->borrowing, $data);
            }
        }

        return true;
    }

}