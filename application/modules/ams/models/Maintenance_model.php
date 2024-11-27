<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance_model extends CI_Model
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

    function getCategoryCollection()
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
            $rowData = $this->get_all_category($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_category_count();
        }

        if ($search) {
            $rowData = $this->get_searched_category($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_category_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_category_count()
    {

        $this->db->from("gccasset.assetcategory");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_category($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $sql = "a.id, a.code, a.description, a.type";
        $this->db->select($sql);
        $this->db->from("gccasset.assetcategory a");

        if ((int)$limit > 0) {
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


    private function get_searched_category($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.code", "a.description", "a.type");
            $sql = "a.id, a.code, a.description, a.type";
            $this->db->select($sql);
            $this->db->from("gccasset.assetcategory a");

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->group_end();

            if ((int)$limit > 0) {
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
                if(!empty($search['value'])){
                    $this->core_layout->setEventLog("User searched `".$search['value']."` in Asset category datatable.","search", "success", "gccasset", "user");
                }
                return $data;
            } else {
                if(!empty($search['value'])){
                    $this->core_layout->setEventLog("User searched `".$search['value']."` in Asset category datatable.","search", "success", "gccasset", "user");
                }
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_category_count($search = null)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.code", "a.description", "a.type");
            $sql = "a.id, a.code, a.description, a.type";
            $this->db->select($sql);
            $this->db->from("gccasset.assetcategory a");

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

    public function save_category($data)
    {
        $q = $this->db->insert('gccasset.assetcategory', $data);
        $id = $this->db->insert_id();
        if($q){
            $this->core_layout->setEventLog("User added new asset category with the the db id of `".$id."` in asset category masterfile datatable.","insert", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to add new asset category with the the db id of `".$id."` in asset category masterfile datatable.","insert", "error", "gccasset", "user");
        }

        return $id;
    }

    public function edit_category($id)
    {
        $sql = "a.id, a.code, a.description, a.type";

        $this->db->select($sql);
        $this->db->from("gccasset.assetcategory a");
        $this->db->where('a.id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function update_category($where, $data)
    {
        $query = $this->db->update('gccasset.assetcategory', $data, $where);
        if($query){
            $this->core_layout->setEventLog("User updated the asset category with the the db id of `".$where['id']."` in asset category masterfile datatable.","update", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to update asset category with the the db id of `".$where['id']."` in asset category masterfile datatable.","update", "error", "gccasset", "user");
        }
        return $this->db->affected_rows();
    }

    public function delete_category($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->delete('gccasset.assetcategory');
        if($query){
            $this->core_layout->setEventLog("User deleted the asset category with the the db id of `".$id."` in asset category masterfile datatable.","delete", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to delete the asset category with the the db id of `".$id."` in asset category masterfile datatable.","delete", "error", "gccasset", "user");
        }
    }

    function getSubCategoryCollection()
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
            $rowData = $this->get_all_sub_category($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_sub_category_count();
        }

        if ($search) {
            $rowData = $this->get_searched_sub_category($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_sub_category_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_sub_category_count()
    {

        $this->db->from("gccasset.asset_sub_cat");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_sub_category($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $sql = "a.sub_cat_id id, a.sub_cat_code, a.sub_cat_desc, b.description";
        $this->db->select($sql);
        $this->db->from("gccasset.asset_sub_cat a");
        $this->db->join("gccasset.assetcategory b", "a.cat_id = b.code", "LEFT");

        if ((int)$limit > 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.sub_cat_id", "DESC");
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


    private function get_searched_sub_category($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.sub_cat_id", "a.sub_cat_code", "a.sub_cat_desc", "a.cat_id", "b.description");
            $sql = "a.sub_cat_id id, a.sub_cat_code, a.sub_cat_desc,  b.description";
            $this->db->select($sql);
            $this->db->from("gccasset.asset_sub_cat a");
            $this->db->join("gccasset.assetcategory b", "a.cat_id = b.code", "LEFT");
            $this->db->group_start();

            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->group_end();

            if ((int)$limit > 0) {
                $this->db->limit($limit, $offset);
            }

            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.sub_cat_id", "DESC");
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

                if(!empty($search['value'])){
                    $this->core_layout->setEventLog("User searched `".$search['value']."` in Asset sub category datatable.","search", "success", "gccasset", "user");
                }

                return $data;
            } else {
                if(!empty($search['value'])){
                    $this->core_layout->setEventLog("User searched `".$search['value']."` in Asset sub category datatable.","search", "success", "gccasset", "user");
                }
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_sub_category_count($search = null)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.sub_cat_id", "a.sub_cat_code", "a.sub_cat_desc", "a.cat_id", "b.description");
            $sql = "a.sub_cat_id id, a.sub_cat_code, a.sub_cat_desc,  b.description";
            $this->db->select($sql);
            $this->db->from("gccasset.asset_sub_cat a");
            $this->db->join("gccasset.assetcategory b", "a.cat_id = b.code", "LEFT");

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

    public function save_sub_category($data)
    {
        $q = $this->db->insert('gccasset.asset_sub_cat', $data);
        $id = $this->db->insert_id();

        if($q){
            $this->core_layout->setEventLog("User added new asset sub category with the the db id of `".$id."` in asset category sub masterfile datatable.","insert", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to add new asset sub category with the the db id of `".$id."` in asset category sub masterfile datatable.","insert", "error", "gccasset", "user");
        }

        return $id;
    }

    public function edit_sub_category($id)
    {
        $sql = "a.sub_cat_id id, a.sub_cat_code, a.sub_cat_desc, a.cat_id, b.description";

        $this->db->select($sql);
        $this->db->from("gccasset.asset_sub_cat a");
        $this->db->join("gccasset.assetcategory b", "a.cat_id = b.code", "LEFT");
        $this->db->where('a.sub_cat_id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function update_sub_category($where, $data)
    {
        $query = $this->db->update('gccasset.asset_sub_cat', $data, $where);

        if($query){
            $this->core_layout->setEventLog("User updated the asset sub category with the the db id of `".$where['sub_cat_id']."` in asset sub category masterfile datatable.","update", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to update asset sub category with the the db id of `".$where['sub_cat_id']."` in asset sub category masterfile datatable.","update", "error", "gccasset", "user");
        }

        return $this->db->affected_rows();
    }

    public function delete_sub_category($id)
    {
        $this->db->where('sub_cat_id', $id);
        $query = $this->db->delete('gccasset.asset_sub_cat');
        if($query){
            $this->core_layout->setEventLog("User deleted the asset sub category with the the db id of `".$id."` in asset sub category masterfile datatable.","delete", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to delete the asset sub category with the the db id of `".$id."` in asset sub category masterfile datatable.","delete", "error", "gccasset", "user");
        }
    }

    function getCategory()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `code`,`description` FROM gccasset.assetcategory WHERE `description` LIKE '%{$get['q']}%' ORDER BY `description` ASC");
        } else {
            $query = $this->db->query("SELECT `code`,`description` FROM gccasset.assetcategory ORDER BY `description` ASC");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["code"];
                $data["text"] = $_query["description"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    function getStationCollection()
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
            $rowData = $this->get_all_station($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_station_count();
        }

        if ($search) {
            $rowData = $this->get_searched_station($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_station_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_station_count()
    {

        $this->db->from("gccasset.station");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_station($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $sql = "a.id, a.station";
        $this->db->select($sql);
        $this->db->from("gccasset.station a");

        if ((int)$limit > 0) {
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


    private function get_searched_station($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.station");
            $sql = "a.id, a.station";
            $this->db->select($sql);
            $this->db->from("gccasset.station a");

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->group_end();

            if ((int)$limit > 0) {
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

                if(!empty($search['value'])){
                    $this->core_layout->setEventLog("User searched `".$search['value']."` in Station datatable.","search", "success", "gccasset", "user");
                }
            } else {
                if(!empty($search['value'])){
                    $this->core_layout->setEventLog("User searched `".$search['value']."` in Station datatable.","search", "success", "gccasset", "user");
                }
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_station_count($search = null)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.station");
            $sql = "a.id, a.station";
            $this->db->select($sql);
            $this->db->from("gccasset.station a");

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

    public function save_station($data)
    {
        $q = $this->db->insert('gccasset.station', $data);
        $id = $this->db->insert_id();

        if($q){
            $this->core_layout->setEventLog("User added new Station with the the db id of `".$id."` in Station datatable.","insert", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to add new Station with the the db id of `".$id."` in Station datatable.","insert", "error", "gccasset", "user");
        }

        return $id;
    }

    public function edit_station($id)
    {
        $sql = "a.id, a.station";

        $this->db->select($sql);
        $this->db->from("gccasset.station a");
        $this->db->where('a.id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function update_station($where, $data)
    {
        $query = $this->db->update('gccasset.station', $data, $where);

        if($query){
            $this->core_layout->setEventLog("User updated the Station with the the db id of `".$where['id']."` in Station datatable.","update", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to update Station with the the db id of `".$where['id']."` in Station datatable.","update", "error", "gccasset", "user");
        }

        return $this->db->affected_rows();
    }

    public function delete_station($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->delete('gccasset.station');

        if($query){
            $this->core_layout->setEventLog("User deleted the Station with the the db id of `".$id."` in Station datatable.","delete", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to delete the Station with the the db id of `".$id."` in Station datatable.","delete", "error", "gccasset", "user");
        }
    }

    function getLocationCollection()
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
            $rowData = $this->get_all_location($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_location_count();
        }

        if ($search) {
            $rowData = $this->get_searched_location($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_location_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_location_count()
    {

        $this->db->from("gccasset.location");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_location($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $sql = "a.id, a.location";
        $this->db->select($sql);
        $this->db->from("gccasset.location a");

        if (int($limit) > 0) {
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


    private function get_searched_location($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.location");
            $sql = "a.id, a.location";
            $this->db->select($sql);
            $this->db->from("gccasset.location a");

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->group_end();

            if ((int)$limit > 0) {
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
                
                if(!empty($search['value'])){
                    $this->core_layout->setEventLog("User searched `".$search['value']."` in Location datatable.","search", "success", "gccasset", "user");
                }

                return $data;
            } else {
                if(!empty($search['value'])){
                    $this->core_layout->setEventLog("User searched `".$search['value']."` in Location datatable.","search", "success", "gccasset", "user");
                }

                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_location_count($search = null)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.location");
            $sql = "a.id, a.location";
            $this->db->select($sql);
            $this->db->from("gccasset.location a");

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

    public function save_location($data)
    {
        $q = $this->db->insert('gccasset.location', $data);
        $id =  $this->db->insert_id();

        if($q){
            $this->core_layout->setEventLog("User added new location with the the db id of `".$id."` in Location datatable.","insert", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to add new location with the the db id of `".$id."` in Location datatable.","insert", "error", "gccasset", "user");
        }

        return $id;
    }

    public function edit_location($id)
    {
        $sql = "a.id, a.location, latitude, longitude";

        $this->db->select($sql);
        $this->db->from("gccasset.location a");
        $this->db->where('a.id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function update_location($where, $data)
    {
        $query = $this->db->update('gccasset.location', $data, $where);

        if($query){
            $this->core_layout->setEventLog("User updated the Location Masterfile with the the db id of `".$where['id']."` in Location datatable.","update", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to update Location Masterfile with the the db id of `".$where['id']."` in Location datatable.","update", "error", "gccasset", "user");
        }

        return $this->db->affected_rows();
    }

    public function delete_location($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->delete('gccasset.location');

        if($query){
            $this->core_layout->setEventLog("User deleted the location with the the db id of `".$id."` in location datatable.","delete", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to delete the location with the the db id of `".$id."` in location datatable.","delete", "error", "gccasset", "user");
        }
    }

    function getEquipmentTypeCollection()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";

        $rowCount = 0;
        $rowData = array();
        if (!$search) {
            $rowData = $this->get_all_equipment_type($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_equipment_type_count();
        }

        if ($search) {
            $rowData = $this->get_searched_equipment_type($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_equipment_type_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_equipment_type_count()
    {

        $this->db->from("gccasset.equip_type");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_equipment_type($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $sql = "a.id, a.code, a.description,  b.description AS desc";
        $this->db->select($sql);
        $this->db->from("gccasset.equip_type a");
        $this->db->join("gccasset.equipmentcategory b", "a.ec_id = b.id", "LEFT");
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


    private function get_searched_equipment_type($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.code", "a.description", "b.description");
            $sql = "a.id, a.code, a.description,  b.description AS desc";
            $this->db->select($sql);
            $this->db->from("gccasset.equip_type a");
            $this->db->join("gccasset.equipmentcategory b", "a.ec_id = b.id", "LEFT");
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->group_end();
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

                if(!empty($search['value'])){
                    $this->core_layout->setEventLog("User searched `".$search['value']."` in Equipement Type datatable.","search", "success", "gccasset", "user");
                }

                return $data;
            } else {

                if(!empty($search['value'])){
                    $this->core_layout->setEventLog("User searched `".$search['value']."` in Equipement Type datatable.","search", "success", "gccasset", "user");
                }
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_equipment_type_count($search = null)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.code", "a.description", "b.description");
            $sql = "a.id, a.code, a.description,  b.description AS desc";
            $this->db->select($sql);
            $this->db->from("gccasset.equip_type a");
            $this->db->join("gccasset.equipmentcategory b", "a.ec_id = b.id", "LEFT");

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

    public function save_equipment_type($data)
    {
        $q = $this->db->insert('gccasset.equip_type', $data);
        $id =  $this->db->insert_id();

        if($q){
            $this->core_layout->setEventLog("User added new Equipement Type with the the db id of `".$id."` in Equipement Type datatable.","insert", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to add new Equipement Type with the the db id of `".$id."` in Equipement Type datatable.","insert", "error", "gccasset", "user");
        }

        return $id;
    }

    public function edit_equipment_type($id)
    {
        $sql = "a.id, a.code, a.description,  b.description AS desc, a.ec_id";

        $this->db->select($sql);
        $this->db->from("gccasset.equip_type a");
        $this->db->join("gccasset.equipmentcategory b", "a.ec_id = b.id", "LEFT");
        $this->db->where('a.id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function update_equipment_type($where, $data)
    {
        $query = $this->db->update('gccasset.equip_type', $data, $where);

        if($query){
            $this->core_layout->setEventLog("User updated the Equipement Type Masterfile with the the db id of `".$where['id']."` in Equipement Type datatable.","update", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to update Equipement Type Masterfile with the the db id of `".$where['id']."` in Equipement Type datatable.","update", "error", "gccasset", "user");
        }

        return $this->db->affected_rows();
    }

    public function delete_equipment_type($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->delete('gccasset.equip_type');

        if($query){
            $this->core_layout->setEventLog("User deleted the Equipement Type with the the db id of `".$id."` in Equipement Type datatable.","delete", "success", "gccasset", "user");
        }else{
            $this->core_layout->setEventLog("User failed to delete the Equipement Type with the the db id of `".$id."` in Equipement Type datatable.","delete", "error", "gccasset", "user");
        }
    }

    function getEquipCategory()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `id`,`description` FROM gccasset.equipmentcategory WHERE `description` LIKE '%{$get['q']}%' ORDER BY `description` ASC");
        } else {
            $query = $this->db->query("SELECT `id`,`description` FROM gccasset.equipmentcategory ORDER BY `description` ASC");
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
}