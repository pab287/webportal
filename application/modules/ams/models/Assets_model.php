<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Assets_model extends CI_Model {
        protected $assetTable = "gccasset.assets";
        private $today;
        private $user = array();
        private $db_debug;

        function __construct() {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->load->model("ams/utilities_model", "utilities");
            $this->load->model("core/Upload_model", "core_upload");
            $this->load->model("core/Core_model", "core");
            $this->load->library('image_lib');
            $this->today = new DateTime('now', new DateTimezone('Asia/Manila'));
            if($this->session->userdata()){
                $userData = $this->session->userdata();
                if(isset($userData["logged_in"]) && $userData["logged_in"]){
                    $this->user = $userData["logged_in"];
                }
            }
            $this->db_debug = $this->db->db_debug;
        }

        private function getUserData() {
            return $this->core_layout->getUserLoggedIn();
        }

        function getDatatableRequest($export) {
            ini_set('max_execution_time', '0');
            $resultSet = array();
            $assetIDs = array();
            $advSearch = array();
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            if (isset($tableConfig['accountability_search']) && !empty($tableConfig['accountability_search'])) {
                $assetIDs = $this->accountability_search($tableConfig['accountability_search']);
                $assetIDs = array_map(function ($item) {
                    return $item['asset_id'];
                }, $assetIDs);
                $assetIDs = implode(",", $assetIDs);
            }

            if (isset($tableConfig['advanced_search']) && !empty($tableConfig['advanced_search'])) {
                $params = $tableConfig['advanced_search'];
                foreach ($params as $key => $value) {
                    if ($key === 'description') {
                        unset($params[$key]);
                        $params["`d`.`description`"] = $value;
                    }
                }

                $advSearch = $params;
                if(!empty($advSearch['dateCreated_from'])){
                    $dt_From = $advSearch['dateCreated_from'];
                }
                if(!empty($advSearch['dateCreated_to'])){ 
                    $dt_To = $advSearch['dateCreated_to'];
                }
            }

            // return $advSearch;

            $table = "gccasset.assets a";

            $sql = "a.id, a.id as asset_id, UPPER(a.assetacode) as assetacode, UPPER(a.name) as name, UPPER(a.assetname) as description, UPPER(a.location) as location_id, ";
            $sql .= "a.area_id, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, UPPER(a.department_code) as department_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.modelno) as modelno, UPPER(a.status) as status, ";
            $sql .= "IF(a.location != '', UPPER(c.station), 'N/A') AS station, IF(a.area_id != 0, UPPER( b.location), 'N/A') AS area, a.dateCreated, ";
            $sql .= "IFNULL(null,a.pic_filename) as image, a.datepurchased, b.location, UPPER(d.description) as asset_category";
            $sql .= ", a.is_borrowed, a.serialno, UPPER(IF(comp.id IS NULL, a.company_code, comp.code)) comp_name, ";
            $sql .= "UPPER(IF(dep.id IS NULL, a.department_code, dep.description)) dep_name, a.status stat_name , a.po_no, a.check_no, a.purchaseprice, a.gl_code";

            $searchFields = "CONCAT(IF(a.assetacode IS NULL, '', a.assetacode), IF(a.`name`IS NULL, '', a.name), 
                            IF(a.`assetname`IS NULL, '', a.assetname), IF(a.`location`IS NULL, '', a.location), IF(a.`po_no`IS NULL, '', a.po_no),
                            IF(a.brand IS NULL, '', a.brand), IF(a.modelno IS NULL, '', a.modelno), IF(a.company_code IS NULL, '', a.company_code),
                            IF(a.department_code IS NULL, '', a.department_code), IF(a.status IS NULL, '', a.status),
                            IF(d.description IS NULL, '', d.description), IF(b.location IS NULL, '', b.location), IF(a.check_no IS NULL, '', a.check_no))";

            if(!empty($advSearch['dateCreated_from']) AND !empty($advSearch['dateCreated_to'])){
                $searchFields_where = "AND a.`dateCreated` BETWEEN '$dt_From' AND '$dt_To'";
            }else{
                $searchFields_where = "";
            }
            $where = "a.isComponent = 0 AND (a.status NOT IN('archived', 'lost', 'junk', 'tradein', 'destructed', 'sold') OR `status` IS NULL) AND a.isJunk != 1 AND a.is_archived != 1"." ".$searchFields_where;
            if (!empty($assetIDs)) {
                $where .= " AND a.id in($assetIDs)";
            }

            $joinArr = array(
                array("table" => "gccasset.location b", "condition" => "b.id = a.area_id", "option" => "LEFT"),
                array("table" => "gccasset.station c", "condition" => "c.id = a.location", "option" => "LEFT"),
                array("table" => "gccasset.assetcategory d", "condition" => "d.code = a.asset_category", "option" => "LEFT"),
                array("table" => "gccasset.asset_sub_cat e", "condition" => "e.sub_cat_id = a.sub_cat_code", "option" => "LEFT"),
                array("table" => "gcchris.tblcompanies comp", "condition" => "comp.id = a.company_code", "option" => "LEFT"),
                array("table" => "gcchris.tbldepartments dep", "condition" => "dep.id = a.department_code", "option" => "LEFT"),
            );

            $this->db->select($sql);
            $this->db->from($table);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
            $this->db->where($where);

            if (!empty($advSearch)) {
                unset($advSearch['dateCreated_from'],$advSearch['dateCreated_to']);
                $this->db->like($advSearch, "both");
                $this->db->like($searchFields, $pageOptions->search, "both");
            } else {
                $this->db->like($searchFields, $pageOptions->search, "both");
            }

            if (intval($export) === 0) {
                if ($pageOptions->length > -1) {
                    $this->db->limit($pageOptions->length, $pageOptions->start);
                }
            }

            if ($pageOptions->order_column === "id" || empty($pageOptions->order_column)) {
                $this->db->order_by("a.dateUpdated", "desc");
            } else {
                $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            }


            $queryResult = $this->db->get();

            // $resultSet["sql"] = $this->db->last_query();

            $data = array();
            if ($queryResult->num_rows() > 0) {
                foreach ($queryResult->result() as $key => $rs) {
                    $imageFile = $rs->image;
                    $rs->imageFilename = $rs->image;
                    $id = $rs->asset_id;
                    $imagePath = "uploads/files/images/assets/{$id}/{$imageFile}";
                    if (file_exists($imagePath) && $imageFile) {
                        $rs->image = base_url("uploads/files/images/assets/{$id}/{$imageFile}");
                        $rs->img_thumbnail = base_url("uploads/files/images/assets/{$id}/thumbnail/{$imageFile}");
                    } else {
                        $rs->image = null;
                        $rs->img_thumbnail = null;
                    }

                    $rs->accounted_to = strtoupper($this->getAssetAccountabilityByReturnStatus($id));
                    $rs->check_no = "<div style='text-overflow: ellipsis; overflow: hidden; max-width: 200px; white-space: nowrap;'>".$rs->check_no."</div>";
                    array_push($data, $rs);
                }
            }

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` in Fixed Assets Masterfile.","search", "success", "gccasset", "user");
            }
            
            if(isset($tableConfig['accountability_search']) && !empty($tableConfig['accountability_search'])){
                $this->core_layout->setEventLog("User searched employee with a emp_id of `".$tableConfig['accountability_search']."` in Fixed Assets Masterfile.","search", "success", "gccasset", "user");
            }

            if(isset($tableConfig['advanced_search']) && !empty($tableConfig['advanced_search'])){
                $output = '';
                $output .= ($advSearch['assetacode'] != '')? 'Asset Code: '.$advSearch['assetacode'].', ':'';
                $output .= ($advSearch['name'] != '')? 'Name: '.$advSearch['name'].', ':'';
                $output .= ($advSearch['assetname'] != '')? 'Asset Name: '.$advSearch['assetname'].', ':'';
                $output .= ($advSearch['area'] != '')? 'Area: '.$advSearch['area'].', ':'';
                $output .= ($advSearch['serialno'] != '')? 'Serial #: '.$advSearch['serialno'].', ':'';
                $output .= ($advSearch['po_no'] != '')? 'PO #: '.$advSearch['po_no'].' ':'';

                $this->core_layout->setEventLog("User searched from advanced search with `".$output."` in Fixed Assets Masterfile.","search", "success", "gccasset", "user");
            }

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr, false, $advSearch);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr, false, $advSearch);
            $resultSet["data"] = $data;
            $resultSet["acct"] = 0;

            if ((isset($tableConfig['accountability_search']) && !empty($tableConfig['accountability_search'])) && empty($assetIDs)) {
                $resultSet["acct"] = 1;
            }
            

            return $resultSet;
            // return $this->db->last_query();
        }

        public function getAssetAccountabilityByReturnStatus($asset_id, $type = "Asset", $status = 0) {
            // 0 = no/true, 1 = yes/true
            $this->db->select("CONCAT(employees.firstname, ' ', 
                                        CASE
                                            WHEN employees.middlename != 'N/A' AND employees.middlename != 'NONE'
                                                 AND employees.middlename != '' AND employees.middlename IS NOT NULL
                                            THEN CONCAT(SUBSTR(employees.middlename, 1, 1), '.')
                                            ELSE '' END, ' ', employees.lastname,
                                        CASE
                                            WHEN employees.suffix != 'N/A' AND employees.suffix != 'NONE' AND employees.suffix != '' AND
                                                employees.suffix IS NOT NULL
                                            THEN CONCAT(' ', employees.suffix)
                                            ELSE '' END) employee, IFNULL(contractor.contractor, '') contractor, accountability.is_contract");
            $this->db->join("`gccasset`.`assets` `assets`", "`body`.`asset_id` = `assets`.`id` AND `body`.`type` = '$type'", "INNER");
            $this->db->join("`gcceforms`.`accountability` `accountability`", "`accountability`.`id` = `body`.`accountability_id`", "INNER");
            $this->db->join("`gccmaster`.`tblemployees` `employees`", "`accountability`.`issued_to` = `employees`.`id`", "INNER");
            $this->db->join("`gcchris`.`tblcontractor` `contractor`", "`contractor`.`id` = `accountability`.`issued_to`", "LEFT");
            $this->db->where("`body`.`asset_id`", $asset_id);
            $this->db->where("`body`.`is_returned`", $status);
            $this->db->where("`accountability`.`status` !=", "Cancelled");
            $qAccounted = $this->db->get("`gcceforms`.`accountability_body` `body`");
            $accountedToBy = "---";
            if($qAccounted->num_rows() == 1){
                $tempRow = $qAccounted->row();
                if($tempRow->is_contract == "1"){
                    $accountedToBy = $tempRow->contractor."<br><small class='text-muted'><i>-Accounted</i></small>";
                }else{
                    $accountedToBy = $tempRow->employee."<br><small class='text-muted'><i>-Accounted</i></small>";
                }
            }else{
                $accountedToBy = $this->getAssetBorrowingByReturnStatus($asset_id, $type);
            }
            return $accountedToBy;
        }

        public function getAssetBorrowingByReturnStatus($asset_id, $type, $status = 0){
            $this->db->select("CONCAT(employees.firstname, ' ', 
                                        CASE
                                            WHEN employees.middlename != 'N/A' AND employees.middlename != 'NONE'
                                                 AND employees.middlename != '' AND employees.middlename IS NOT NULL
                                            THEN CONCAT(SUBSTR(employees.middlename, 1, 1), '.')
                                            ELSE '' END, ' ', employees.lastname,
                                        CASE
                                            WHEN employees.suffix != 'N/A' AND employees.suffix != 'NONE' AND employees.suffix != '' AND
                                                employees.suffix IS NOT NULL
                                            THEN CONCAT(' ', employees.suffix)
                                            ELSE '' END) employee");
            if($type == "vehicle"){
                $this->db->join("`gccasset`.`vehicles` `assets`", "`body`.`asset_id` = `assets`.`id` AND `body`.`type` = '$type'", "INNER");
            }else{
                $this->db->join("`gccasset`.`assets` `assets`", "`body`.`asset_id` = `assets`.`id` AND `body`.`type` = '$type'", "INNER");
            }
            $this->db->join("`gcceforms`.`borrowing` `borrowing`", "`borrowing`.`id` = `body`.`borrowing_id`", "INNER");
            $this->db->join("`gccmaster`.`tblemployees` `employees`", "`borrowing`.`borrower` = `employees`.`id`", "INNER");
            $this->db->where("`body`.`asset_id`", $asset_id);
            $this->db->where("`body`.`is_returned`", $status);
            $this->db->where("`borrowing`.`status` !=", "Cancelled");
            $qAccounted = $this->db->get("`gcceforms`.`borrowing_body` `body`");
            $accountedToBy = "---";
            if($qAccounted->num_rows() > 0){
                $tempRow = $qAccounted->row();
                    $accountedToBy = $tempRow->employee."<br><small class='text-muted'><i>-Borrowed</i></small>";
                
            }
            return $accountedToBy;
        }

        private function get_all_post($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
            $sql = "a.id, a.id as asset_id, UPPER(a.assetacode) as assetacode, UPPER(a.name) as name, UPPER(a.assetname) as description, UPPER(a.location) as location_id, ";
            $sql .= "a.area_id, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, UPPER(a.department_code) as department_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.modelno) as modelno, UPPER(a.status) as status, ";
            $sql .= "IF(a.location != '', UPPER(c.station), 'N/A') AS station, IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.dateCreated, ";
            $sql .= "IFNULL(null,a.pic_filename) as image, a.datepurchased, b.location, UPPER(d.description) as asset_category";
            $sql .= ", a.is_borrowed";

            $this->db->select($sql);
            $this->db->from("gccasset.assets a");
            $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
            $this->db->join("gccasset.station c", "c.id = a.location", "LEFT");
            $this->db->join("gccasset.assetcategory d", "d.id = a.asset_category", "LEFT");
            $this->db->join("gccasset.asset_sub_cat e", "e.sub_cat_id = a.sub_cat_code", "LEFT");
            $this->db->where("a.isComponent", 0);
            $this->db->where('a.status !=', 'archived');
            $this->db->where('a.status !=', 'lost');
            $this->db->where('a.status !=', 'junk');
            $this->db->where('a.status !=', 'tradein');
            $this->db->where('a.isJunk !=', '1');
            $this->db->limit($limit, $offset);

            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $data = array();

                foreach ($query->result() as $key => $rs) {
                    $imageFile = $rs->image;
                    $id = $rs->asset_id;
                    $imagePath = realpath("uploads/files/images/{$id}/{$imageFile}");
                    if (file_exists($imagePath) && $imageFile) {
                        $rs->image = base_url("uploads/files/images/{$id}/{$imageFile}");
                        $rs->img_thumbnail = base_url("uploads/files/images/{$id}/thumbnail/{$imageFile}");
                    } else {
                        $rs->image = null;
                        $rs->img_thumbnail = null;
                    }

                    $accountedTo = $this->getAcctabilityByAssetCode($rs->assetacode);
                    $rs->accounted_to = ($accountedTo) ? $accountedTo : "---";
                    $rs->is_accounted = ($accountedTo) ? true : false;
                    $rs->imagePath = $imagePath;
                    array_push($data, $rs);
                }

                return $data;
            } else {
                return array();
            }
        }

        private function get_all_post_count() {
            $this->db->from("gccasset.assets a");
            $this->db->where("a.isComponent", 0);
            $this->db->where('a.status !=', 'archived');
            $this->db->where('a.status !=', 'lost');
            $this->db->where('a.status !=', 'junk');
            $this->db->where('a.status !=', 'tradein');
            $this->db->where('a.isJunk !=', '1');
            $query = $this->db->get();

            return $query->num_rows();
        }

        private function get_searched_item($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
            if ($search) {
                $filterFields = array("a.assetacode", "a.name", "a.assetname", "a.company_code",
                    "a.department_code", "a.brand", "a.serialno", "a.modelno", "a.status", "b.location",
                    "c.station", "d.description");

                $sql = "a.id, a.id as asset_id, UPPER(a.assetacode) as assetacode, UPPER(a.name) as name, UPPER(a.assetname) as description, UPPER(a.location) as location_id, ";
                $sql .= "a.area_id, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, UPPER(a.department_code) as department_code, ";
                $sql .= "UPPER(a.serialno) as serialno, UPPER(a.modelno) as modelno, UPPER(a.status) as status, ";
                $sql .= "IF(a.location != '', UPPER(c.station), 'N/A') AS station, IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.dateCreated, ";
                $sql .= "IFNULL(null,a.pic_filename) as image, a.datepurchased, b.location, UPPER(d.description) as asset_category";
                $sql .= ", a.is_borrowed";

                $this->db->select($sql);
                $this->db->from("gccasset.assets a");
                $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
                $this->db->join("gccasset.station c", "c.id = a.location", "LEFT");
                $this->db->join("gccasset.assetcategory d", "d.id = a.asset_category", "LEFT");
                $this->db->join("gccasset.asset_sub_cat e", "e.sub_cat_id = a.sub_cat_code", "LEFT");
                $this->db->where("a.isComponent", 0);
                $this->db->where('a.status !=', 'archived');
                $this->db->where('a.status !=', 'lost');
                $this->db->where('a.status !=', 'junk');
                $this->db->where('a.status !=', 'tradein');
                $this->db->where('a.isJunk !=', '1');
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
                        $imageFile = $rs->image;
                        $id = $rs->asset_id;
                        $imagePath = realpath("uploads/files/images/{$id}/{$imageFile}");
                        if (file_exists($imagePath) && $imageFile) {
                            $rs->image = base_url("uploads/files/images/{$id}/{$imageFile}");
                            $rs->img_thumbnail = base_url("uploads/files/images/{$id}/thumbnail/{$imageFile}");
                        } else {
                            $rs->image = null;
                            $rs->img_thumbnail = null;
                        }

                        $accountedTo = $this->getAcctabilityByAssetCode($rs->assetacode);
                        $rs->accounted_to = ($accountedTo) ? $accountedTo : "---";
                        $rs->is_accounted = ($accountedTo) ? true : false;
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

        private function get_searched_item_count($search = null) {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.assetacode", "a.name", "a.assetname", "a.company_code",
                    "a.department_code", "a.brand", "a.serialno", "a.modelno", "a.status", "b.location",
                    "c.station", "d.description");

                $this->db->from("gccasset.assets a");
                $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
                $this->db->join("gccasset.station c", "c.id = a.location", "LEFT");
                $this->db->join("gccasset.assetcategory d", "d.code = a.asset_category", "LEFT");
                $this->db->join("gccasset.asset_sub_cat e", "e.sub_cat_id = a.sub_cat_code", "LEFT");
                $this->db->where("a.isComponent", 0);
                $this->db->where('a.status !=', 'archived');
                $this->db->where('a.status !=', 'lost');
                $this->db->where('a.status !=', 'junk');
                $this->db->where('a.status !=', 'tradein');
                $this->db->where('a.isJunk !=', '1');
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

        public function getAcctabilityByAssetCode($asset_code = NULL, $type = "Asset") {
            if ($asset_code) {
                $ids = array();
                $checks = array();
                $this->db->from("gcceforms.accountability a");
                $this->db->join("gcceforms.accountability_body b", "b.accountability_id = a.id", "left");
                $this->db->where("b.asset_code", $asset_code);
                $this->db->where("b.type", $type);
                $this->db->where("b.is_returned", "0");
                $acct = $this->db->get();

                if ($acct->num_rows() > 0) {
                    foreach ($acct->result() as $rx) {
                        if (!in_array($rx->issued_to, $ids)) {
                            $ids[] = $rx->issued_to;
                            $check[] = $rx->is_contract;
                        }
                    }
                }

                if ($ids) {
                    $arrNames = array();
                    for ($i = 0; $i < sizeof($ids); $i++) {
                        if ($check[$i] == "0") {
                            $emp = $this->db->get_where("gccmaster.tblemployees", array("id" => $ids[$i]));
                            if ($emp->num_rows() == 1) {
                                $row = $emp->row();
                                $name = "{$row->firstname} {$row->lastname}";
                                $arrNames = $name;
                            }
                        }
                        if ($check[$i] == "1") {
                            $this->db->from("gcchris.tblcontractor");
                            $this->db->where('id', $ids[$i]);
                            $emp2 = $this->db->get();
                            if ($emp2->num_rows() == 1) {
                                $row2 = $emp2->row();
                                $arrNames = $row2->contractor;
                            }
                        }

                    }
                    if ($arrNames) {
                        return $arrNames;
                    } else {
                        return false;
                    }

                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        public function getBorrowingByAssetCode($asset_code = NULL, $type = "Asset") {
            if ($asset_code) {
                $ids = array();
                $checks = array();
                $this->db->from("gcceforms.borrowing a");
                $this->db->join("gcceforms.borrowing_body b", "b.borrowing_id = a.id", "left");
                $this->db->where("b.asset_code", $asset_code);
                $this->db->where("b.type", $type);
                $this->db->where("b.is_returned", "0");
                $acct = $this->db->get();

                if ($acct->num_rows() > 0) {
                    foreach ($acct->result() as $rx) {
                        if (!in_array($rx->borrower, $ids)) {
                            $ids[] = $rx->borrower;
                        }
                    }
                }

                if ($ids) {
                    $arrNames = array();
                    for ($i = 0; $i < sizeof($ids); $i++) {
                            $emp = $this->db->get_where("gccmaster.tblemployees", array("id" => $ids[$i]));
                            if ($emp->num_rows() == 1) {
                                $row = $emp->row();
                                $name = "{$row->firstname} {$row->lastname}";
                                $arrNames = $name;
                            }
                    }
                    if ($arrNames) {
                        return $arrNames;
                    } else {
                        return false;
                    }

                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getComponentsMasterfileList($export) {
            $resultSet = array();
            $advSearch = array();
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
            $table = "gccasset.assets a";

            if (isset($tableConfig['advanced_search']) && !empty($tableConfig['advanced_search'])) {
                $params = $tableConfig['advanced_search'];
                foreach ($params as $key => $value) {
                    if ($key === 'description') {
                        unset($params[$key]);
                        $params["`d`.`description`"] = $value;
                    }
                }

                $advSearch = $params;

                if(!empty($advSearch['dateCreated_from'])){
                    $dt_From = $advSearch['dateCreated_from'];
                }
                if(!empty($advSearch['dateCreated_to'])){ 
                    $dt_To = $advSearch['dateCreated_to'];
                }
            }

            $sql = "a.id, a.id as asset_id, UPPER(a.assetacode) as assetacode, UPPER(a.name) as name, UPPER(a.assetname) as description, UPPER(a.location) as location_id, ";
            $sql .= "a.area_id, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, UPPER(a.department_code) as department_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.modelno) as modelno, UPPER(a.status) as status, UPPER(a.po_no) as po_no,";
            $sql .= "IF(a.location != '', UPPER(c.station), 'N/A') AS station, IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.dateCreated, ";
            $sql .= "IFNULL(null,a.pic_filename) as image, a.datepurchased, b.location, UPPER(d.description) as asset_category";
            $sql .= ", a.is_borrowed, a.check_no, a.serialno, UPPER(IF(comp.id IS NULL, a.company_code, comp.code)) comp_name, UPPER(IF(dep.id IS NULL, a.department_code, dep.description)) dep_name, a.purchaseprice, a.gl_code";

            $searchFields = "CONCAT(IF(a.assetacode IS NULL, '', a.assetacode), IF(a.`name`IS NULL, '', a.name), IF(a.`check_no`IS NULL, '', a.check_no),
                            IF(a.`assetname`IS NULL, '', a.assetname), IF(a.`location`IS NULL, '', a.location), IF(a.`po_no`IS NULL, '', a.po_no),
                            IF(a.brand IS NULL, '', a.brand), IF(a.modelno IS NULL, '', a.modelno), IF(a.company_code IS NULL, '', a.company_code),
                            IF(a.department_code IS NULL, '', a.department_code))";

            if(!empty($advSearch['dateCreated_from']) AND !empty($advSearch['dateCreated_to'])){
                $searchFields_where = "AND a.`dateCreated` BETWEEN '$dt_From' AND '$dt_To'";
            }else{
                $searchFields_where = "";
            }
                
            $where = "a.isComponent = 1 AND (a.status NOT IN('archived', 'lost', 'junk', 'tradein', 'destructed','sold') OR `status` IS NULL) AND a.isJunk != 1 AND a.is_archived != 1"." ".$searchFields_where;
;
            $joinArr = array(
                array("table" => "gccasset.location b", "condition" => "b.id = a.area_id", "option" => "LEFT"),
                array("table" => "gccasset.station c", "condition" => "c.id = a.location", "option" => "LEFT"),
                array("table" => "gccasset.assetcategory d", "condition" => "d.code = a.asset_category", "option" => "LEFT"),
                array("table" => "gccasset.asset_sub_cat e", "condition" => "e.sub_cat_id = a.sub_cat_code", "option" => "LEFT"),
                array("table" => "gcchris.tblcompanies comp", "condition" => "comp.id = a.company_code", "option" => "LEFT"),
                array("table" => "gcchris.tbldepartments dep", "condition" => "dep.id = a.department_code", "option" => "LEFT"),
            );

            $this->db->select($sql);
            $this->db->from($table);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $this->db->where($where);

            if (!empty($advSearch)) {
                unset($advSearch['dateCreated_from'],$advSearch['dateCreated_to']);
                $this->db->like($advSearch, "both");
                $this->db->like($searchFields, $pageOptions->search, "both");
            } else {
                $this->db->like($searchFields, $pageOptions->search, "both");
            }

            if (intval($export) === 0) {
                if ($pageOptions->length > -1) {
                    $this->db->limit($pageOptions->length, $pageOptions->start);
                }
            }

            if ($pageOptions->order_column === "id") {
                $this->db->order_by("a.dateUpdated", "desc");
            } else {
                $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            }

            $queryResult = $this->db->get();

            // $resultSet["sql"] = $this->db->last_query();

            $data = array();
            if ($queryResult->num_rows() > 0) {
                foreach ($queryResult->result() as $key => $rs) {
                    $imageFile = $rs->image;
                    $rs->imageFilename = $rs->image;
                    $id = $rs->asset_id;
                    $imagePath = "uploads/files/images/assets/{$id}/{$imageFile}";
                    if (file_exists($imagePath) && $imageFile) {
                        $rs->image = base_url("uploads/files/images/assets/{$id}/{$imageFile}");
                        $rs->img_thumbnail = base_url("uploads/files/images/assets/{$id}/thumbnail/{$imageFile}");
                    } else {
                        $rs->image = null;
                        $rs->img_thumbnail = null;
                    }

                    $accountedTo = $this->getAcctabilityByAssetCode($rs->assetacode);
                    $borrowed = $this->getBorrowingByAssetCode($rs->assetacode);
                    if($accountedTo){
                        $rs->accounted_to = $accountedTo."<br><small class='text-muted'><i>-Accounted</i></small>";
                    }else if($borrowed){
                        $rs->accounted_to = $borrowed."<br><small class='text-muted'><i>-Borrowed</i></small>";
                    }else{
                        $rs->accounted_to = "---";
                    }
                    // $rs->accounted_to = ($accountedTo) ? $accountedTo : "---";
                    // $rs->is_accounted = ($accountedTo) ? true : false;
                    $rs->is_accounted = $rs->accounted_to;
                    $rs->imagePath = $imagePath;
                    $rs->check_no = "<div style='text-overflow: ellipsis; overflow: hidden; max-width: 200px; white-space: nowrap;'>".$rs->check_no."</div>";
                    array_push($data, $rs);
                }
            }

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` in Asset Component Masterfile.","search", "success", "gccasset", "user");
            }
            
            if(isset($tableConfig['accountability_search']) && !empty($tableConfig['accountability_search'])){
                $this->core_layout->setEventLog("User searched employee with a emp_id of `".$tableConfig['accountability_search']."` in Asset Component Masterfile.","search", "success", "gccasset", "user");
            }

            if(isset($tableConfig['advanced_search']) && !empty($tableConfig['advanced_search'])){
                $output = '';
                $output .= ($advSearch['assetacode'] != '')? 'Asset Code: '.$advSearch['assetacode'].', ':'';
                $output .= ($advSearch['name'] != '')? 'Name: '.$advSearch['name'].', ':'';
                $output .= ($advSearch['assetname'] != '')? 'Asset Name: '.$advSearch['assetname'].', ':'';
                $output .= ($advSearch['area'] != '')? 'Area: '.$advSearch['area'].', ':'';
                $output .= ($advSearch['serialno'] != '')? 'Serial #: '.$advSearch['serialno'].', ':'';
                $output .= ($advSearch['po_no'] != '')? 'PO #: '.$advSearch['po_no'].' ':'';

                $this->core_layout->setEventLog("User searched from advanced search with `".$output."` in Asset Component Masterfile.","search", "success", "gccasset", "user");
            }

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr, false, $advSearch);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr, false, $advSearch);
            $resultSet["data"] = $data;

            return $resultSet;
        }

        function get_all_component($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
            $sql = "a.id, a.id as asset_id, UPPER(a.assetacode) as assetacode, UPPER(a.name) as name, UPPER(a.assetname) as description, UPPER(a.location) as location_id, ";
            $sql .= "a.area_id, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, UPPER(a.department_code) as department_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.modelno) as modelno, UPPER(a.status) as status, ";
            $sql .= "IF(a.location != '', UPPER(c.station), 'N/A') AS station, IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.dateCreated, ";
            $sql .= "IFNULL(null,a.pic_filename) as image, a.datepurchased, b.location, UPPER(d.description) as asset_category";
            $sql .= ", a.is_borrowed, a.mother_asset";

            $this->db->select($sql);
            $this->db->from("gccasset.assets a");
            $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
            $this->db->join("gccasset.station c", "c.id = a.location", "LEFT");
            $this->db->join("gccasset.assetcategory d", "d.code = a.asset_category", "LEFT");
            $this->db->join("gccasset.asset_sub_cat e", "e.sub_cat_id = a.sub_cat_code", "LEFT");
            $this->db->where("a.isComponent", 1);
            $this->db->where('a.status !=', 'archived');
            $this->db->where('a.status !=', 'lost');
            $this->db->where('a.status !=', 'junk');
            $this->db->where('a.status !=', 'tradein');
            $this->db->where('a.isJunk !=', '1');
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
                    $imageFile = $rs->image;
                    $id = $rs->asset_id;
                    $imagePath = realpath("uploads/files/images/{$id}/{$imageFile}");
                    if (file_exists($imagePath) && $imageFile) {
                        $rs->image = base_url("uploads/files/images/{$id}/{$imageFile}");
                        $rs->img_thumbnail = base_url("uploads/files/images/{$id}/thumbnail/{$imageFile}");
                    } else {
                        $rs->image = null;
                        $rs->img_thumbnail = null;
                    }

                    $arrData[$key] = $rs;
                }

                return $arrData;
            } else {
                return array();
            }
        }

        private function get_all_component_count() {
            $this->db->from("gccasset.assets a");
            $this->db->where("a.isComponent", 1);
            $this->db->where('a.status !=', 'archived');
            $this->db->where('a.status !=', 'lost');
            $this->db->where('a.status !=', 'junk');
            $this->db->where('a.status !=', 'tradein');
            $this->db->where('a.isJunk !=', '1');
            $query = $this->db->get();

            return $query->num_rows();
        }

        private function get_searched_component_count($search = null) {
            $searchFields = "CONCAT(IF(a.assetacode IS NULL, '', a.assetacode), IF(a.name IS NULL, '', a.name), IF(a.assetname IS NULL, '', a.assetname)";
            $searchFields .= ", IF(a.brand IS NULL, '', a.brand), IF(a.company_code IS NULL, '', a.company_code)";
            $searchFields .= ", IF(a.department_code IS NULL, '', a.department_code), IF(a.serialno IS NULL, '', a.serialno)";
            $searchFields .= ", IF(a.modelno IS NULL, '', a.modelno))";

            $rowCount = 0;
            if ($search) {
                $this->db->from("gccasset.assets a");
                $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
                $this->db->join("gccasset.station c", "c.id = a.location", "LEFT");
                $this->db->join("gccasset.assetcategory d", "d.code = a.asset_category", "LEFT");
                $this->db->join("gccasset.asset_sub_cat e", "e.sub_cat_id = a.sub_cat_code", "LEFT");
                $this->db->where("a.isComponent", 1);
                $this->db->where('a.status !=', 'archived');
                $this->db->where('a.status !=', 'lost');
                $this->db->where('a.status !=', 'junk');
                $this->db->where('a.status !=', 'tradein');
                $this->db->where('a.isJunk !=', '1');
                $this->db->like($searchFields, $search["value"], "both");
                $query = $this->db->get();

                $rowCount = $query->num_rows();
            }

            return $rowCount;
        }

        function get_searched_component($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
            if ($search) {
                $sql = "a.id, a.id as asset_id, UPPER(a.assetacode) as assetacode, UPPER(a.name) as name, UPPER(a.assetname) as description, UPPER(a.location) as location_id, ";
                $sql .= "a.area_id, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, UPPER(a.department_code) as department_code, ";
                $sql .= "UPPER(a.serialno) as serialno, UPPER(a.modelno) as modelno, UPPER(a.status) as status, ";
                $sql .= "IF(a.location != '', UPPER(c.station), 'N/A') AS station, IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.dateCreated, ";
                $sql .= "IFNULL(null,a.pic_filename) as image, a.datepurchased, b.location, UPPER(d.description) as asset_category";
                $sql .= ", a.is_borrowed, a.mother_asset";

                $this->db->select($sql);
                $this->db->from("gccasset.assets a");
                $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
                $this->db->join("gccasset.station c", "c.id = a.location", "LEFT");
                $this->db->join("gccasset.assetcategory d", "d.code = a.asset_category", "LEFT");
                $this->db->join("gccasset.asset_sub_cat e", "e.sub_cat_id = a.sub_cat_code", "LEFT");
                $this->db->where("a.isComponent", 1);
                $this->db->where('a.status !=', 'archived');
                $this->db->where('a.status !=', 'lost');
                $this->db->where('a.status !=', 'junk');
                $this->db->where('a.status !=', 'tradein');
                $this->db->where('a.isJunk !=', '1');

                $searchFields = "CONCAT(IF(a.assetacode IS NULL, '', a.assetacode), IF(a.name IS NULL, '', a.name), IF(a.assetname IS NULL, '', a.assetname)";
                $searchFields .= ", IF(a.brand IS NULL, '', a.brand), IF(a.company_code IS NULL, '', a.company_code)";
                $searchFields .= ", IF(a.department_code IS NULL, '', a.department_code), IF(a.serialno IS NULL, '', a.serialno)";
                $searchFields .= ", IF(a.modelno IS NULL, '', a.modelno))";

                $this->db->like($searchFields, $search["value"], "both");
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
                        $imageFile = $rs->image;
                        $id = $rs->asset_id;
                        $mother_asset_id = $rs->mother_asset;
                        $imagePath = realpath("server/php/images/files/thumbnail/{$imageFile}");
                        if (file_exists($imagePath) && $imageFile) {
                            $rs->image = base_url("uploads/files/images/{$id}/{$imageFile}");
                            $rs->img_thumbnail = base_url("uploads/files/images/{$id}/thumbnail/{$imageFile}");
                        } else {
                            $rs->image = base_url("uploads/module/ams/images/no_image.jpg");
                            $rs->img_thumbnail = base_url("uploads/module/ams/images/no_image.jpg");
                        }

                        $rs->mother_asset_is_borrowed = $this->checkIfMotherAssetIsBorrowed($mother_asset_id);
                        $arrData[$key] = $rs;
                    }

                    return $arrData;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        }

        function checkIfMotherAssetIsBorrowed($mother_asset_id) {
            $this->db->where("id", $mother_asset_id);
            $this->db->where("is_borrowed", 1);
            $result = $this->db->count_all_results("gccasset.assets");
            return $result > 0 ? 1 : 0;
        }

        function processNew() {
            $session_data = $this->session->userdata('logged_in');
            $username = $session_data['firstname'] . ' ' . $session_data['lastname'];

            $uploaded_images = $this->input->post("uploaded_image");

            $date = date('Y-m-d h:i:s');

            if ($this->input->post('date_purchased') == '') {
                $datepurchased = '0000-00-00';
            } else {
                $datepurchased = $this->input->post('date_purchased');
            }

            $cat_id_post = $this->input->post('category_id');
            $subcatid = $this->input->post('sub_cat_id');

            $this->db->from('gccasset.assets'); // Generated Series No
            $this->db->where('asset_category', $cat_id_post);
            $this->db->where('sub_cat_code', $subcatid);
            $this->db->order_by("series", "desc");
            $query = $this->db->get();
            $results = $query->result();

            $num = sizeof($results) - 1;

            if (sizeof($num) > 0 && $query->num_rows() > 0) {
                $series = intval($results[0]->series) + 1;
            } else {
                $series = 1;
            }

            $sub_cat_id_post = $this->input->post('sub_cat_id');

            $this->db->from('gccasset.asset_sub_cat'); // Generated Series No
            $this->db->where('sub_cat_id', $sub_cat_id_post);
            $query = $this->db->get();
            $res = $query->row();

            $sub_cat_codes = ($query->num_rows() == 1) ? $res->sub_cat_code : "";

            if ($sub_cat_id_post != null) {
                $sub_cat_id_post;
            } else {
                $sub_cat_id_post = 0;
            }

            if ($cat_id_post != null && $sub_cat_id_post != null) {
                if (strlen($series) == 1) {
                    $gen_code = $cat_id_post . "-" . $sub_cat_codes . "-" . "000" . $series;
                    $bseries = "000" . $series;
                } else if (strlen($series) == 2) {
                    $gen_code = $cat_id_post . "-" . $sub_cat_codes . "-" . "00" . $series;
                    $bseries = "00" . $series;
                } else if (strlen($series) == 3) {
                    $gen_code = $cat_id_post . "-" . $sub_cat_codes . "-" . "0" . $series;
                    $bseries = "0" . $series;
                } else {
                    $gen_code = $cat_id_post . "-" . $sub_cat_codes . "-" . $series;
                    $bseries = $series;
                }
            } else if ($cat_id_post != null && $sub_cat_id_post == 0) {
                if (strlen($series) == 1) {
                    $gen_code = $cat_id_post . "-" . "000" . $series;
                    $bseries = "000" . $series;
                } else if (strlen($series) == 2) {
                    $gen_code = $cat_id_post . "-" . "00" . $series;
                    $bseries = "00" . $series;
                } else if (strlen($series) == 3) {
                    $gen_code = $cat_id_post . "-" . "0" . $series;
                    $bseries = "0" . $series;
                } else {
                    $gen_code = $cat_id_post . "-" . $series;
                    $bseries = $series;
                }
            }

            $this->db->from('gccasset.assetcategory');
            $this->db->where('code', $cat_id_post);
            $queries = $this->db->get();
            $result = $queries->row();
            $cat_code = ($queries->num_rows() == 1) ? $result->id : 0;
            $barcode = $cat_code . $sub_cat_codes . $bseries;

            if ($this->input->post('assetacode') != null) {
                $generated_code = $this->input->post('assetacode');
                $bar_code = $generated_code;
                $isGen = '0';
            } else {
                $generated_code = $gen_code;
                $bar_code = $barcode;
                $isGen = '1';
            }

            if ($this->input->post('remarks') != null) {
                $isOper = "1";
                $remarks = $this->input->post('remarks');
            } else {
                $isOper = "0";
                $remarks = "Operational";
            }

            $locationId = $this->input->post('area');
            $arrLocation = array();
            $loc = $this->db->get_where("gccasset.location", array("id" => $locationId));
            if ($loc->num_rows() == 1) {
                $rowLoc = $loc->row_array();
                $arrLocation["area"] = (isset($rowLoc["location"]) && $rowLoc["location"]) ? $rowLoc["location"] : "";
                $arrLocation["area_id"] = (isset($rowLoc["id"]) && $rowLoc["id"]) ? $rowLoc["id"] : 0;
            }

            $data = array(
                'id' => $this->input->post(0),
                'company_code' => $this->input->post('company_code'),
                'department_code' => $this->input->post('department_code'),
                'asset_category' => $this->input->post('category_id'),
                'sub_cat_code' => $sub_cat_id_post,
                'series' => $series,
                'gen_code' => $generated_code,
                'barcode' => $bar_code,
                'gl_code' => $this->input->post('gl_code'),
                'assetacode' => $generated_code,
                'name' => $this->input->post('name'),
                'assetname' => $this->input->post('assetname'),
                'datepurchased' => $datepurchased,
                'purchaseprice' => $this->input->post('purchaseprice2'),
                'life' => $this->input->post('life') != null ? $this->input->post('life') : "",
                'beg_addcost' => $this->input->post('beg_addcost2'),
                'total_cost' => $this->input->post('total_cost2'),
                'salvage_value' => $this->input->post('salvage_value2') != null ? $this->input->post('salvage_value2') : 0,
                'location' => $this->input->post('location_id'),

                'area' => (isset($arrLocation["area"]) && $arrLocation["area"]) ? $arrLocation["area"] : "",
                'area_id' => (isset($arrLocation["area_id"]) && $arrLocation["area_id"]) ? $arrLocation["area_id"] : 0,

                'serialno' => $this->input->post('serialno'),
                'brand' => $this->input->post('brand'),
                'supplier' => $this->input->post('supplier'),
                'check_no' => $this->input->post('check_no'),
                'po_no' => $this->input->post('po_no'),
                'stockcode' => $this->input->post('stockcode'),
                'modelno' => $this->input->post('modelno'),
                'mother_asset' => 0,
                'isComponent' => 0,
                'createdBy' => $username,
                'dateCreated' => $date,
                'isGen' => $isGen,
                'remarks2' => $remarks,
                'is_borrowed' => 0,
                'status' => $this->input->post('status'),
                'ar' => $this->input->post('ARNo') != null ? $this->input->post('ARNo') : "",
                'rr_no' => $this->input->post('rrno'),
                'date_received' => $this->input->post('date_received'),
            );

            $assetName = $this->input->post('assetname');
            $assetName = (isset($assetName) && $assetName) ? $assetName : "No asset name";

            $insert = $this->assets->save($data);

            $imagePost = $this->input->post();
            $primaryImage = (isset($imagePost["primary_image"]) && $imagePost["primary_image"]) ? $imagePost["primary_image"] : "";
            $altImages = (isset($imagePost["uploaded_image"]) && $imagePost["uploaded_image"]) ? $imagePost["uploaded_image"] : array();

            $this->upload_asset_images($insert, $primaryImage, $altImages);

            //move images
            $moveImages = $this->moveImages($insert, $uploaded_images, $primaryImage);

            if (!in_array(false, $moveImages)) {
                return array("status" => TRUE, "asset_code" => $generated_code,
                    "name" => $assetName, "id" => $insert, "toastr_msg" => "Asset successfully saved!");
            } else {
                return array("status" => FALSE, "toastr_msg" => "Unable to process request.");
            }

        }

        private function save($data) {
            $this->db->insert($this->assetTable, $data);
            return $this->db->insert_id();
        }

        private function upload_asset_images($insert, $primaryImage, $altImages) {
            $session_data = $this->session->userdata('logged_in');
            if ($primaryImage) {
                $data = array();
                $data["asset_id"] = $insert;
                $data["image"] = $primaryImage;
                $data["dateupload"] = date("Y-m-d h:i:s");
                $data["useruploaded"] = $session_data["id"];

                $inserted = $this->db->insert("gccasset.asset_pictures", $data);
                if ($inserted) {
                    $this->db->update("gccasset.assets", array("pic_filename" => $primaryImage), array("id" => $insert));
                }
            }

            if ($altImages) {
                foreach ($altImages as $image) {
                    $data = array();
                    $data["asset_id"] = $insert;
                    $data["image"] = $image;
                    $data["dateupload"] = date("Y-m-d h:i:s");
                    $data["useruploaded"] = $session_data["id"];
                    $this->db->insert("gccasset.asset_image", $data);
                }
            }
        }

        private function moveImages($id = null, $uploaded_images = array(), $primaryImage) {
            $getUserData = $this->getUserData();
            $result = array();
            $uploaded_images[] = $primaryImage;

            $thumbnail_path = "uploads/files/images/temp_images/temp{$getUserData['id']}/thumbnail";
            $new_thumbnail_path = "uploads/files/images/{$id}/thumbnail";

            $temp_path = "uploads/files/images/temp_images/temp{$getUserData['id']}/";
            $new_path = "uploads/files/images/{$id}/";

            if (!file_exists($new_path)):
                mkdir($new_path, 775);
            endif;

            if (isset($id)) {
                foreach ($uploaded_images as $key => $value) {
                    $currentFilePath = $temp_path . $value;
                    $newFilePath = $new_path . $value;

                    $fileMoved = rename($currentFilePath, $newFilePath);
                    $result[] = $fileMoved ? true : false;
                }
            }

            //move thumbnail folder
            rename($thumbnail_path, $new_thumbnail_path);

            return $result;
        }

        function getCompanyCollection() {
            $resultarray = array();

            $get = $this->input->get();
            $q = isset($get['q']) ? $get['q'] : "";
            $this->db->select("id, description text");
            $this->db->like("description", $q, "both");
            $this->db->order_by("description", "asc");
            $resultarray = $this->db->get("gcchris.tblcompanies")->result();

            return array("results" => $resultarray);
        }

        function getDepartmentCollection() {
            $data = array();
            $q = isset($_GET['q']) ? $_GET['q'] : "";

            $this->db->like("CONCAT(description, code)", $q, "both");
            $data['results'] = $this->db->get("gcchris.tbldepartments")->result();
            return $data;
        }

        function getStationCollection() {
            $data = array();
            $q = isset($_GET['q']) ? $_GET['q'] : "";

            $this->db->like("station", $q, "both");
            $data['results'] = $this->db->get("gccasset.station")->result();
            return $data;
        }

        function getCategoryCollection($type = null) {
            $q = isset($_GET['q']) ? $_GET['q'] : "";
            $this->db->select("id, description text, code cat_id");
            $this->db->where("type", $type);
            $this->db->like("CONCAT(code, description)", $q, "both");
            $query = $this->db->get("gccasset.assetcategory");

            return array("results" => $query->result());
        }

        function getLocationCollection() {
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $query = $this->db->query("SELECT `id`,`location` FROM gccasset.location WHERE `location` LIKE '%{$get['q']}%' ORDER BY `location` ASC");
            } else {
                $query = $this->db->query("SELECT `id`,`location` FROM gccasset.location ORDER BY `location` ASC");
            }

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["location"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);

        }

        function getAreaCollection() {
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $query = $this->db->query("SELECT `id`,`station` FROM gccasset.station WHERE `station` LIKE '%{$get['q']}%' ORDER BY `station` ASC");
            } else {
                $query = $this->db->query("SELECT `id`,`station` FROM gccasset.station ORDER BY `station` ASC");
            }

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["station"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);

        }

        function getDataTableStatusCollection() {
            $resultset = array();
            $rowData = array();
            $rowCount = 0;

            $query = $this->db->query("SELECT gccasset.status.id,gccasset.status.name,gccasset.status_category.name AS category FROM gccasset.status LEFT JOIN gccasset.status_category ON gccasset.status_category.id = gccasset.status.category ORDER BY `name` ASC");

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["category"] = $_query["category"] ? $_query["category"] : "--";
                    $data["name"] = $_query["name"];

                    $rowData[] = $data;
                }

                $rowCount = $query->num_rows();
            }

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function getStatusCategoryCollection() {
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $query = $this->db->query("SELECT `id`,`name` FROM gccasset.status_category WHERE `name` LIKE '%{$get['q']}%' ORDER BY `name` ASC");
            } else {
                $query = $this->db->query("SELECT `id`,`name` FROM gccasset.status_category ORDER BY `name` ASC");
            }

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["name"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function getStatusCollection() {
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $query = $this->db->query("SELECT `id`,`name` FROM gccasset.status WHERE `name` LIKE '%{$get['q']}%' AND `category` = 1 ORDER BY `name` ASC");
            } else {
                $query = $this->db->query("SELECT `id`,`name` FROM gccasset.status WHERE `category` = 1 ORDER BY `name` ASC");
            }

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["name"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function insertStatus() {
            $resultarray = array();
            $post = $this->input->post();
            $post["created_by"] = $this->getUserData()["id"];
            $query = $this->db->insert('gccasset.status', $post);

            if ($query) {
                $resultarray["status"] = TRUE;
                $resultarray["toastr_msg"] = "Data successfully saved!";
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["toastr_msg"] = "Error processing request!";
            }

            return $resultarray;

        }

        function getTypeLookup() {
            $resultarray = array();
            $post = $this->input->post();
            $query = $this->db->query("SELECT sub_cat_id,sub_cat_desc FROM gccasset.asset_sub_cat WHERE cat_id = '{$post['cat_id']}'");

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["sub_cat_id"];
                    $data["text"] = $_query["sub_cat_desc"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function generateAssetCode() {
            $post = $this->input->post();

            $queryAssetCategory = $this->db->query("SELECT * FROM gccasset.assetcategory WHERE id = {$post['cat_id']}");
            $cat_id_post = $queryAssetCategory->row_array()['code'];
            $subcatid = (isset($post['sub_cat_id']) && $post['sub_cat_id']) ? $post['sub_cat_id'] : 0;

            $this->db->from('gccasset.assets'); // Generated Series No
            $this->db->where('asset_category', $cat_id_post);
            $this->db->where('sub_cat_code', $subcatid);
            $this->db->order_by("series", "desc");
            $query = $this->db->get();

            $results = $query->result();
            $num = sizeof($results) - 1;
            $series = (sizeof($num) > 0 && $query->num_rows() > 0) ? intval($results[0]->series) + 1 : 1;

            $this->db->from('gccasset.asset_sub_cat'); // Generated Series No
            $this->db->where('sub_cat_id', $subcatid);
            $query = $this->db->get();
            $res = $query->row();

            $sub_cat_codes = ($query->num_rows() == 1) ? $res->sub_cat_code : "";

            if ($cat_id_post != null && $subcatid != null) {
                if (strlen($series) == 1) {
                    $gen_code = $cat_id_post . "-" . $sub_cat_codes . "-" . "000" . $series;
                    $bseries = "000" . $series;
                } else if (strlen($series) == 2) {
                    $gen_code = $cat_id_post . "-" . $sub_cat_codes . "-" . "00" . $series;
                    $bseries = "00" . $series;
                } else if (strlen($series) == 3) {
                    $gen_code = $cat_id_post . "-" . $sub_cat_codes . "-" . "0" . $series;
                    $bseries = "0" . $series;
                } else {
                    $gen_code = $cat_id_post . "-" . $sub_cat_codes . "-" . $series;
                    $bseries = $series;
                }
            } else if ($cat_id_post != null && $subcatid == 0) {
                if (strlen($series) == 1) {
                    $gen_code = $cat_id_post . "-" . "000" . $series;
                    $bseries = "000" . $series;
                } else if (strlen($series) == 2) {
                    $gen_code = $cat_id_post . "-" . "00" . $series;
                    $bseries = "00" . $series;
                } else if (strlen($series) == 3) {
                    $gen_code = $cat_id_post . "-" . "0" . $series;
                    $bseries = "0" . $series;
                } else {
                    $gen_code = $cat_id_post . "-" . $series;
                    $bseries = $series;
                }
            }

            $this->db->from('gccasset.assetcategory');
            $this->db->where('code', $cat_id_post);
            $queries = $this->db->get();
            $result = $queries->row();
            $cat_code = ($queries->num_rows() == 1) ? $result->id : 0;
            $barcode = $cat_code . $sub_cat_codes . $bseries;

            $generated_code = $gen_code;
            $bar_code = $barcode;
            $isGen = '1';

            return array("assetacode" => $generated_code);
        }

        private function checkImagePath($image = null, $id = null) {
            if ($image) {
                $filePath = realpath("uploads/files/images/{$id}");
                $file = $filePath . "/" . $image;

                if (file_exists($file)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        private function checkImageThumbnailPath($image = null, $id = null) {
            if ($image) {
                $filePath = realpath("uploads/files/images/{$id}/thumbnail");
                $file = $filePath . "/" . $image;

                if (file_exists($file)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function AssetImages($type = "assets", $id = null) {
            $resultset = array();
            $getUserData = $this->getUserData();

            $filename = "uploads/files/images/temp_images/temp" . $getUserData['id'];

            if (!file_exists($filename)) {
                mkdir("uploads/files/images/temp_images/temp" . $getUserData['id'], "0775", TRUE);
            }

            $files = (isset($_FILES["files"]) && $_FILES["files"]) ? $_FILES["files"] : false;
            $config = array();
            $config['upload_path'] = "./uploads/files/images/temp_images/temp{$getUserData['id']}";
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = 100000;
            $this->upload->initialize($config);


            if ($files) {
                foreach ($files["name"] as $key => $image) {
                    $_FILES["files"]["name"] = $files["name"][$key];
                    $_FILES["files"]["type"] = $files["type"][$key];
                    $_FILES["files"]["tmp_name"] = $files["tmp_name"][$key];
                    $_FILES["files"]["error"] = $files["error"][$key];
                    $_FILES["files"]["size"] = $files["size"][$key];
                }

                if (!$this->upload->do_upload('files')) {
                    $error = array('error' => $this->upload->display_errors());
                    $resultset["response"] = false;
                    $resultset["data"] = $error;
                } else {
                    $data = $this->upload->data();
                    $clientName = (isset($data["client_name"]) && $data["client_name"]) ? $data["client_name"] : "";
                    $fileName = (isset($data["file_name"]) && $data["file_name"]) ? $data["file_name"] : "";
                    $fileSize = (isset($data["file_size"]) && $data["file_size"]) ? $data["file_size"] : 0;
                    $deleteType = (isset($data["file_name"]) && $data["file_name"]) ? $data["file_name"] : "";

                    $resize = $this->resizeImage($fileName);
                    $_data = array();
                    $_data["resize"] = $resize;
                    $_data["name"] = $clientName;
                    $_data["uploaded"] = $fileName;
                    $_data["size"] = $fileSize;
                    $_data["deleteType"] = "DELETE";

                    if ($type == "assets") {
                        $_data["deleteUrl"] = site_url("asset_upload/asset_image_remove/{$fileName}");
                    }
                    if ($type == "vehicles") {
                        $_data["deleteUrl"] = site_url("asset_upload/vehicle_image_remove/{$fileName}");
                    }

                    $_data["thumbnailUrl"] = base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/thumbnail/{$fileName}");
                    $_data["url"] = base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/{$fileName}");

                    $resultset["files"][] = $_data;
                }
            } else {
                if ($type == "assets") {


                    if ($id) {
                        $prim = $this->db->get_where("gccasset.asset_pictures", array("asset_id" => $id));
                        if ($prim->num_rows() == 1) {
                            $row = $prim->row_array();
                            $fileName = $row["image"];

                            if (isset($fileName) && $fileName) {
                                $imageData = array();
                                $imageData["isPrimary"] = true;
                                $imageData["name"] = $fileName;
                                $imageData["uploaded"] = $fileName;
                                $imageData["deleteType"] = "DELETE";
                                $imageData["deleteUrl"] = site_url("asset_upload/asset_image_remove/{$fileName}/{$id}");

                                if (file_exists(base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/thumbnail/{$fileName}"))):
                                    $imageData["thumbnailUrl"] = base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/thumbnail/{$fileName}");
                                else:
                                    $imageData["thumbnailUrl"] = base_url("uploads/files/images/{$id}/thumbnail/{$fileName}");
                                endif;

                                if (file_exists(base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/{$fileName}"))):
                                    $imageData["url"] = base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/{$fileName}");
                                else:
                                    $imageData["url"] = base_url("uploads/files/images/{$id}/{$fileName}");
                                endif;


                                $resultset["files"][] = $imageData;
                            } else {
                                $resultset["response"] = false;
                            }
                        }

                        $alt = $this->db->get_where("gccasset.asset_image", array("asset_id" => $id));
                        if ($alt->num_rows() > 0) {
                            foreach ($alt->result_array() as $rs) {
                                if (isset($rs["image"]) && $rs["image"]) {
                                    $fileName = $rs["image"];

                                    $fileExist = $this->checkImagePath($fileName, $id);
                                    if ($fileExist) {
                                        $imageData = array();
                                        $imageData["name"] = $fileName;
                                        $imageData["uploaded"] = $fileName;
                                        $imageData["deleteType"] = "DELETE";
                                        $imageData["deleteUrl"] = site_url("asset_upload/asset_image_remove/{$fileName}/{$id}");
                                        $thumbExist = $this->checkImageThumbnailPath($fileName, $id);
                                        if ($thumbExist) {

                                            if (file_exists(base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/thumbnail/{$fileName}"))):
                                                $imageData["thumbnailUrl"] = base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/thumbnail/{$fileName}");
                                            else:
                                                $imageData["thumbnailUrl"] = base_url("uploads/files/images/{$id}/thumbnail/{$fileName}");
                                            endif;
                                        }


                                        if (file_exists(base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/{$fileName}"))):
                                            $imageData["url"] = base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/{$fileName}");
                                        else:
                                            $imageData["url"] = base_url("uploads/files/images/{$id}/{$fileName}");
                                        endif;

                                        $resultset["files"][] = $imageData;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($type == "vehicles") {
                    if ($id) {
                        $arrData = array();
                        $prim = $this->db->get_where("gccasset.vehicles", array("id" => $id));
                        if ($prim->num_rows() == 1) {
                            $row = $prim->row_array();
                            $fileName = $row["primary_pic"];

                            if (isset($fileName) && $fileName) {
                                $imageData = array();
                                $imageData["isPrimary"] = true;
                                $imageData["name"] = $fileName;
                                $imageData["uploaded"] = $fileName;
                                $imageData["deleteType"] = "DELETE";
                                $imageData["deleteUrl"] = site_url("asset_upload/vehicle_image_remove/{$fileName}/{$id}");
                                $imageData["thumbnailUrl"] = base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/thumbnail/{$fileName}");
                                $imageData["url"] = base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/{$fileName}");

                                $resultset["files"][] = $imageData;
                            } else {
                                $resultset["response"] = false;
                            }
                        }

                        $alt = $this->db->get_where("gccasset.vehicles_image", array("asset_id" => $id));
                        if ($alt->num_rows() > 0) {
                            foreach ($alt->result_array() as $rs) {
                                if (isset($rs["image"]) && $rs["image"]) {
                                    $fileName = $rs["image"];

                                    $fileExist = $this->checkImagePath($fileName, $id);
                                    if ($fileExist) {
                                        $imageData = array();
                                        $imageData["name"] = $fileName;
                                        $imageData["uploaded"] = $fileName;
                                        $imageData["deleteType"] = "DELETE";
                                        $imageData["deleteUrl"] = site_url("asset_upload/vehicle_image_remove/{$fileName}/{$id}");
                                        $thumbExist = $this->checkImageThumbnailPath($fileName, $id);
                                        if ($thumbExist) {
                                            $imageData["thumbnailUrl"] = base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/thumbnail/{$fileName}");
                                        }
                                        $imageData["url"] = base_url("uploads/files/images/temp_images/temp{$getUserData['id']}/files/{$fileName}");

                                        $resultset["files"][] = $imageData;
                                    }
                                }
                            }
                        }
                    }
                }

            }

            return $resultset;
        }

        protected function resizeImage($filename) {
            $getUserData = $this->getUserData();
            $path = "./uploads/files/images/temp_images/temp{$getUserData['id']}/";
            $source_path = $path . $filename;
            $target_path = "{$path}/thumbnail/";

            if (!file_exists($target_path)) {
                mkdir($target_path, 775);
            }

            $config_manip = array(
                'image_library' => 'gd2',
                'source_image' => $source_path,
                'new_image' => $target_path,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '',
                'width' => 75,
                'height' => 75
            );

            $this->load->library('image_lib', $config_manip);
            if (!$this->image_lib->resize()) {
                return false;
            } else {
                return true;
            }
            $this->image_lib->clear();
        }

        function setImages($type = "assets", $id = null) {
            $post = $this->input->post();
            $resultset = array();
            $arrData = array();

            $session_data = $this->session->userdata('logged_in');
            $currentDate = date("Y-m-d h:i:s");

            if ($id) {
                $isPrimary = (isset($post["primary"]) && $post["primary"]) ? $post["primary"] : "";
                $images = (isset($post["name"]) && $post["name"]) ? $post["name"] : array();

                $arrData = array();
                if ($images) {
                    foreach ($images as $image) {
                        if ($image == $isPrimary) {
                            $arrData["primary"] = $image;
                        } else {
                            $arrData["alt_image"][] = $image;
                        }
                    }
                }

                if ($arrData) {
                    if (isset($arrData["primary"]) && $arrData["primary"]) {
                        if ($type == "assets") {
                            $prim = $this->db->get_where("gccasset.asset_pictures", array("asset_id" => $id));
                            if ($prim->num_rows() == 1) {
                                $row = $prim->row_array();
                                $moveImage = $row["image"];

                                $data = array();
                                $data["image"] = $arrData["primary"];
                                $data["useruploaded"] = $session_data["id"];
                                $data["dateupload"] = $currentDate;

                                $where = array();
                                $where["id"] = $row["id"];


                                $updatePrim = $this->db->update("gccasset.asset_pictures", $data, $where);
                                if ($updatePrim) {
                                    $this->db->update("gccasset.assets", array("pic_filename" => $arrData["primary"]), array("id" => $id));

                                    $primImage = $this->db->get_where("gccasset.asset_image", array("asset_id" => $id, "image" => $arrData["primary"]));
                                    if ($primImage->num_rows() == 1) {
                                        $rowImage = $primImage->row_array();
                                        $where = array();
                                        $where["id"] = $rowImage["id"];

                                        $data = array();
                                        $data["image"] = $moveImage;
                                        $data["dateupload"] = $currentDate;
                                        $this->db->delete("gccasset.asset_image", $where);
                                    }
                                }
                            } else {
                                $data = array();
                                $data["asset_id"] = $id;
                                $data["image"] = $arrData["primary"];
                                $data["useruploaded"] = $session_data["id"];
                                $data["dateupload"] = $currentDate;

                                $inserted = $this->db->insert("gccasset.asset_pictures", $data);
                                if ($inserted) {
                                    $this->db->update("gccasset.assets", array("pic_filename" => $arrData["primary"]), array("id" => $id));
                                }
                            }
                        }

                        if ($type == "vehicles") {
                            $prim = $this->db->get_where("gccasset.vehicles", array("id" => $id));
                            if ($prim->num_rows() == 1) {
                                $row = $prim->row_array();
                                $moveImage = $row["primary_pic"];

                                $data = array();
                                $data["primary_pic"] = $arrData["primary"];

                                $where = array();
                                $where["id"] = $id;

                                $updatePrim = $this->db->update("gccasset.vehicles", $data, $where);
                                if ($updatePrim) {
                                    $primImage = $this->db->get_where("gccasset.vehicles_image", array("asset_id" => $id, "image" => $arrData["primary"]));
                                    if ($primImage->num_rows() == 1) {
                                        $rowImage = $primImage->row_array();
                                        $where = array();
                                        $where["id"] = $rowImage["id"];

                                        $data = array();
                                        $data["image"] = $moveImage;
                                        $data["dateupload"] = $currentDate;
                                        $this->db->delete("gccasset.vehicles_image", $where);
                                    }
                                }
                            } else {
                                $data = array();
                                $data["primary_pic"] = $arrData["primary"];

                                $where = array();
                                $where["id"] = $id;

                                $this->db->update("gccasset.vehicles", $data, $where);
                            }
                        }
                    }

                    if (isset($arrData["alt_image"]) && $arrData["alt_image"]) {
                        if ($type == "assets") {
                            $altImage = $arrData["alt_image"];
                            $alt = $this->db->get_where("gccasset.asset_image", array("asset_id" => $id));
                            if ($alt->num_rows() > 0) {
                                foreach ($alt->result_array() as $rs) {
                                    if (in_array($rs["image"], $altImage)) {
                                        $index = array_search($rs["image"], $altImage);
                                        unset($altImage[$index]);
                                    }
                                }
                                if (isset($arrData["primary"]) && in_array($arrData["primary"], $altImage)) {
                                    $index = array_search($arrData["primary"], $altImage);
                                    unset($altImage[$index]);
                                    $getPrim = $this->db->get_where("gccasset.asset_image", array("asset_id" => $id, "image" => $arrData["primary"]));
                                    if ($getPrim->num_rows() == 1) {
                                        $primRow = $getPrim->row_array();
                                        $this->db->delete("gccasset.asset_image", array("id" => $primRow["id"]));
                                    }
                                } else if (isset($arrData["primary"]) && $arrData["primary"]) {
                                    $getPrim = $this->db->get_where("gccasset.asset_image", array("asset_id" => $id, "image" => $arrData["primary"]));
                                    if ($getPrim->num_rows() == 1) {
                                        $primRow = $getPrim->row_array();
                                        $this->db->delete("gccasset.asset_image", array("id" => $primRow["id"]));
                                    }
                                }

                                if (isset($altImage) && $altImage) {
                                    foreach ($altImage as $img) {
                                        $alt2 = $this->db->get_where("gccasset.asset_image", array("asset_id" => $id, "image" => $img));
                                        if ($alt2->num_rows() == 0) {
                                            $data = array();
                                            $data["asset_id"] = $id;
                                            $data["image"] = $img;
                                            $data["useruploaded"] = $session_data["id"];
                                            $data["dateupload"] = $currentDate;
                                            $this->db->insert("gccasset.asset_image", $data);
                                        }
                                    }
                                }

                            } else {
                                foreach ($altImage as $index => $value) {
                                    $data = array();
                                    $data["asset_id"] = $id;
                                    $data["image"] = $value;
                                    $data["useruploaded"] = $session_data["id"];
                                    $data["dateupload"] = $currentDate;
                                    $this->db->insert("gccasset.asset_image", $data);
                                }
                            }
                        }

                        if ($type == "vehicles") {
                            $altImage = $arrData["alt_image"];
                            $alt = $this->db->get_where("gccasset.vehicles_image", array("asset_id" => $id));
                            if ($alt->num_rows() > 0) {
                                foreach ($alt->result_array() as $rs) {
                                    if (in_array($rs["image"], $altImage)) {
                                        $index = array_search($rs["image"], $altImage);
                                        unset($altImage[$index]);
                                    }
                                }
                                if (isset($arrData["primary"]) && in_array($arrData["primary"], $altImage)) {
                                    $index = array_search($arrData["primary"], $altImage);
                                    unset($altImage[$index]);
                                    $getPrim = $this->db->get_where("gccasset.vehicles_image", array("asset_id" => $id, "image" => $arrData["primary"]));
                                    if ($getPrim->num_rows() == 1) {
                                        $primRow = $getPrim->row_array();
                                        $this->db->delete("gccasset.vehicles_image", array("id" => $primRow["id"]));
                                    }
                                } else if (isset($arrData["primary"]) && $arrData["primary"]) {
                                    $getPrim = $this->db->get_where("gccasset.vehicles_image", array("asset_id" => $id, "image" => $arrData["primary"]));
                                    if ($getPrim->num_rows() == 1) {
                                        $primRow = $getPrim->row_array();
                                        $this->db->delete("gccasset.vehicles_image", array("id" => $primRow["id"]));
                                    }
                                }

                                if (isset($altImage) && $altImage) {
                                    foreach ($altImage as $img) {
                                        $alt2 = $this->db->get_where("gccasset.vehicles_image", array("asset_id" => $id, "image" => $img));
                                        if ($alt2->num_rows() == 0) {
                                            $data = array();
                                            $data["asset_id"] = $id;
                                            $data["image"] = $img;
                                            $data["useruploaded"] = $session_data["id"];
                                            $data["dateupload"] = $currentDate;
                                            $this->db->insert("gccasset.vehicles_image", $data);
                                        }
                                    }
                                }

                            } else {
                                foreach ($altImage as $index => $value) {
                                    $data = array();
                                    $data["asset_id"] = $id;
                                    $data["image"] = $value;
                                    $data["useruploaded"] = $session_data["id"];
                                    $data["dateupload"] = $currentDate;
                                    $this->db->insert("gccasset.vehicles_image", $data);
                                }
                            }
                        }
                    }
                }
            }

            $arrData["data"] = $post;
            return $arrData["data"];

        }

        function getAssetData() {
            $post = $this->input->post();
            $query = $this->db->query("SELECT
								a.id AS asset_id,
								a.name,
								a.company_code,
								a.department_code,
								a.asset_category,
								a.sub_cat_code,
								a.isGen,
								a.assetacode,
								a.gl_code,
								a.assetname,
								a.remarks,
								a.supplier,
								a.check_no,
								a.po_no,
								a.serialno,
								a.brand,
								a.modelno,
								a.location,
								a.area_id,
								a.datepurchased,
								a.date_received,
								a.rr_no,
								a.purchaseprice,
								a.beg_addcost,
								a.total_cost,
								a.status,
								a.updatedBy,
								a.dateUpdated,
								a.createdBy,
								a.dateCreated,
								a.inventory_check_by,
								a.inventory_check_date,
								b.description AS asset_description,
								c.sub_cat_desc AS sub_cat_description,
								d.description AS company_description,
								e.description AS department_description,
								f.station AS location_description,
								g.location AS station_description,
								h.name AS status_description
							FROM
								gccasset.assets AS a
								LEFT JOIN gccasset.assetcategory AS b ON b.id = a.asset_category
								LEFT JOIN gccasset.asset_sub_cat AS c ON c.sub_cat_id = a.sub_cat_code
								LEFT JOIN gcchris.tblcompanies AS d ON d.id = a.company_code
								LEFT JOIN gcchris.tbldepartments AS e ON e.id = a.department_code
								LEFT JOIN gccasset.station AS f ON f.id = a.location
								LEFT JOIN gccasset.location AS g ON g.id = a.area_id
								LEFT JOIN gccasset.status AS h ON h.id = a.status
  
								WHERE a.id = {$post['id']}");

            return $query->row_array();
        }

        function getAssetComponents($mother_asset_id) {
            $post = $this->input->post();
            $rowData = array();

            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $selectQuery = "assets.assetacode as asset_code, asset_components.asset_id , asset_components.id, 
			asset_components.asset_id, asset_components.mother_asset, assets.name,
			assets.assetname as description, asset_components.remarks, 
			asset_components.isExcluded";

            $where = array();
            $where["asset_components.mother_asset"] = $mother_asset_id;
            $where["assets.isComponent"] = 1;

            $joinArr = array();
            $joinArr[0] = array(
                "table" => "gccasset.assets",
                "condition" => "assets.id = asset_components.asset_id",
                "option" => "left"
            );

            $this->db->select($selectQuery);
            $this->db->from("gccasset.asset_components");
            if ($joinArr) {
                foreach ($joinArr as $_join) {
                    $this->db->join($_join["table"], $_join["condition"], $_join["option"]);
                }
            }
            $this->db->where($where);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $searchFields = "CONCAT(assets.assetacode, assets.assetname)";
            $this->db->like($searchFields, $pageOptions->search, "both");

            if ($pageOptions->order_column !== "asset_code") {
                $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            }

            $query = $this->db->get();
            $data = $query->result();

            // $resultset["sql"] = $this->db->last_query();

            /*if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["asset_code"] = $_query["asset_code"];
                    $data["description"] = $_query["description"];
                    $data["remarks"] = $_query["remarks"];
                    $data["asset_id"] = $_query["asset_id"];

                    $rowData[] = $data;
                }
            }*/

            if ($pageOptions->order_column === "asset_code") {
                usort($data, function ($a, $b) use ($pageOptions) {
                    $strA_arr = explode("-", $a->asset_code);
                    $a_end = end($strA_arr);

                    $strB_arr = explode("-", $b->asset_code);
                    $b_end = end($strB_arr);

                    if (strtolower($pageOptions->order_direction) === "desc") {
                        return ($a_end > $b_end) ? -1 : 1;
                    } else {
                        return ($a_end < $b_end) ? -1 : 1;
                    }
                });
            }

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` in asset components.","search", "success", "gccasset", "user");
            }

            $like = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => 'both');
            $resultset["recordsTotal"] = $this->utilities->getTableCount("gccasset.asset_components", $where, $like, $joinArr);
            $resultset["recordsFiltered"] = $this->utilities->getTableCount("gccasset.asset_components", $where, $like, $joinArr);
            $resultset["data"] = $data;

            return $resultset;

        }

        function getAssetAccountability_() {
            $post = $this->input->post();
            $rowData = array();

            $this->db->select('h.id,h.reference_no, h.is_contract, h.issued_to, h.date_issued, h.status, d.asset_code, d.is_returned');
            $this->db->from('gcceforms.accountability h');
            $this->db->join('gcceforms.accountability_body d', 'h.id = d.accountability_id', 'left');
            $this->db->where('d.asset_id', $post["id"]);
            $this->db->order_by("h.date_issued", "desc");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["date_issued"] = $_query["date_issued"];
                    $data["reference_no"] = $_query["reference_no"];
                    if ($_query["is_contract"] == "1") {
                        $getName = $this->getContractor($_query["issued_to"]);
                        $data["issued_to"] = $getName["contractor"];
                    } else {
                        $getName = $this->getEmployee($_query["issued_to"]);
                        $data["issued_to"] = $getName["fullname"] . " - " . $getName["company_id"];
                    }

                    $data["status"] = $_query["is_returned"] != 1 ? $_query["status"] : "Cleared";

                    $rowData[] = $data;
                }
            }

            $resultset["recordsTotal"] = $query->num_rows();
            $resultset["recordsFiltered"] = $query->num_rows();
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function getEmployee($id) {
            $query = $this->db->query("SELECT CONCAT(firstname,' ',lastname) AS fullname, company_id FROM gccmaster.tblemployees WHERE id = {$id}");
            return $query ? $query->row_array() : "";
        }

        private function getContractor($id) {
            $query = $this->db->query("SELECT contractor FROM gcchris.tblcontractor WHERE id = {$id}");
            return $query ? $query->row_array() : "";
        }

        private function getAsset($id) {
            $query = $this->db->query("SELECT * FROM gccasset.assets WHERE id ={$id}");
            return $query ? $query->row_array() : false;
        }

        function getAssetBorrowing() {
            $post = $this->input->post();
            $getAsset = $this->getAsset($post["id"]);
            $rowData = array();

            $this->db->select('d.id,h.borrower,h.date_trans,h.status,d.asset_code,d.date_borrowed,d.date_returned,d.is_returned,h.released_dt,h.approved_dt,h.cancelled_dt,h.created_dt');
            $this->db->from('gcceforms.borrowing h');
            $this->db->join('gcceforms.borrowing_body d', 'h.id = d.borrowing_id', 'left');
            $this->db->where('d.asset_code =', $getAsset["assetacode"]);
            $this->db->order_by("h.date_trans", "desc");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $getName = $this->getEmployee($_query["borrower"]);

                    $data["id"] = $_query["id"];
                    $data["transaction_date"] = $_query["date_trans"];
                    $data["borrower"] = $getName["fullname"] . " - " . $getName["company_id"];

                    if ($_query["is_returned"] == 1) {
                        $date = $_query["date_returned"];
                        $status = "Returned";
                    } else {
                        switch ($_query["status"]) {
                            case "Released":
                                $date = $_query["released_dt"];
                                $status = $_query["status"];
                                break;
                            case "Approved":
                                $date = $_query["approved_dt"];
                                $status = $_query["status"];
                                break;
                            case "Cancelled":
                                $date = $_query["cancelled_dt"];
                                $status = $_query["status"];
                                break;
                            default:
                                $date = $_query["created_dt"];
                                $status = "Pending";
                                break;
                        }
                    }

                    $data["date"] = $date;
                    $data["status"] = $status;

                    $rowData[] = $data;

                }
            }

            $resultset["recordsTotal"] = $query->num_rows();
            $resultset["recordsFiltered"] = $query->num_rows();
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function getMotherAssetCollection() {
            $rowData = array();
            $post = $this->input->post();
            $id = $this->input->post('id');

            $search = isset($post["search"]["value"]) ? $post["search"]["value"] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";

            $this->db->select("a.id,a.assetacode,a.assetname AS description,a.name,l.location");
            $this->db->from("gccasset.assets a");
            $this->db->join("gccasset.location l", "a.location = l.id");
            $this->db->where("a.mother_asset", 0);
            $this->db->where("a.isComponent", 0);
            $this->db->where("a.is_borrowed", 0);
            $this->db->where("a.isDamage", 0);
            $this->db->where("a.id !=", $id);


            if ($search) {
                $this->db->group_start();
                $this->db->like("a.assetacode", $search, "both");
                $this->db->or_like("a.assetname", $search, "both");
                $this->db->group_end();
            }

            $this->db->limit($limit, $offset);
            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $rowData = $query->result_array();
            }

            $countCollection = $this->getMotherAssetCollectionSub($search);

            $resultset["recordsTotal"] = $countCollection;
            $resultset["recordsFiltered"] = $countCollection;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function getMotherAssetCollectionSub($search = false) {
            $this->db->select("a.id,a.assetacode,a.assetname AS description,a.name,l.location");
            $this->db->from("gccasset.assets a");
            $this->db->join("gccasset.location l", "a.location = l.id");
            $this->db->where("a.mother_asset", 0);
            $this->db->where("a.isComponent", 0);
            $this->db->where("a.is_borrowed", 0);
            $this->db->where("a.isDamage", 0);
            if ($search) {
                $this->db->group_start();
                $this->db->like("a.assetacode", $search, "both");
                $this->db->or_like("a.assetname", $search, "both");
                $this->db->group_end();
            }
            $query = $this->db->get();

            return $query->num_rows();
        }

        function getAssetComponentCollection() {
            $rowData = array();
            $post = $this->input->post();

            $search = isset($post["search"]["value"]) ? $post["search"]["value"] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";

            $this->db->select("a.id,a.assetacode,a.assetname AS description,a.name,l.location");
            $this->db->from("gccasset.assets a");
            $this->db->join("gccasset.location l", "a.location = l.id");
            $this->db->where("a.mother_asset", 0);
            $this->db->where("a.isComponent", 1);
            $this->db->where("a.is_borrowed", 0);
            $this->db->where("a.isDamage", 0);


            if ($search) {
                $this->db->group_start();
                $this->db->like("a.assetacode", $search, "both");
                $this->db->or_like("a.assetname", $search, "both");
                $this->db->group_end();
            }

            $this->db->limit($limit, $offset);
            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $rowData = $query->result_array();
            }

            $countCollection = $this->getAssetComponentCollectionSub($search);

            $resultset["recordsTotal"] = $countCollection;
            $resultset["recordsFiltered"] = $countCollection;
            $resultset["data"] = $rowData;
            $resultset["sql"] = $this->db->last_query();

            return $resultset;
        }

        function getAssetComponentCollectionSub($search = false) {
            $this->db->select("a.id,a.assetacode,a.assetname AS description,a.name,l.location");
            $this->db->from("gccasset.assets a");
            $this->db->join("gccasset.location l", "a.location = l.id");
            $this->db->where("a.mother_asset", 0);
            $this->db->where("a.isComponent", 1);
            $this->db->where("a.is_borrowed", 0);
            $this->db->where("a.isDamage", 0);

            if ($search) {
                $this->db->group_start();
                $this->db->like("a.assetacode", $search, "both");
                $this->db->or_like("a.assetname", $search, "both");
                $this->db->group_end();
            }

            $query = $this->db->get();

            return $query->num_rows();
        }

        function saveInclude() {
            $resultset = array();
            $post = $this->input->post();
            $data = array();
            $data['mother_asset'] = $post['asset_id'];

            $resultset = $this->assetComponentConvertion($post['id']);
            if ($resultset["response"] == true) {
                $this->update_include(array('id' => $post['id']), $data);

                $this->db->from('gccasset.assets');
                $this->db->where('id', $post['id']);
                $query = $this->db->get();
                $res = $query->row();

                $data2 = array(
                    'asset_id' => $post['id'],
                    'mother_asset' => $this->input->post('asset_id'),
                    'description' => $res->assetname,
                    'remarks' => 'current',
                );
                $insert = $this->saveCompo($data2);
            }

            return $resultset;
        }

        private function assetComponentConvertion($id = null) {
            $resultset = array();
            if ($id) {
                $query = $this->db->get_where("gccasset.assets", array("id" => $id, "isComponent" => 0));
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $where = array();
                    $where["id"] = $id;

                    $data = array();
                    $data["isComponent"] = 1;

                    $update = $this->db->update("gccasset.assets", $data, $where);
                    if ($update) {
                        $resultset["is_converted"] = true;
                        $resultset["name"] = ($row->name) ? $row->name : "No asset name";
                        $resultset["code"] = $row->assetacode;
                    } else {
                        $resultset["is_converted"] = false;
                    }
                } else {
                    $resultset["is_converted"] = false;
                }
            } else {
                $resultset["is_converted"] = false;
            }

            $resultset["response"] = true;
            return $resultset;
        }

        private function update_include($where, $data) {
            $this->db->update('gccasset.assets', $data, $where);
            return $this->db->affected_rows();
        }

        private function saveCompo($data) {
            $this->db->insert("gccasset.asset_components", $data);
            return $this->db->insert_id();
        }

        function saveDocument($asset_id) {
            $description = $this->input->post('description');
            $data = null;
            $config = array();
            $config['upload_path'] = 'uploads/files/asset_documents/' . $asset_id;;
            $config['allowed_types'] = '*';
            $config['max_size'] = 10000;
            $this->upload->initialize($config);

            if (!is_dir('uploads/files/asset_documents/' . $asset_id)) {
                mkdir('./uploads/files/asset_documents/' . $asset_id, 0775, TRUE);
            }

            if (!$this->upload->do_upload('file')) {
                $data = array(
                    'success' => true,
                    'message' => $this->upload->display_errors()
                );
            } else {
                $uploaded_data = $this->upload->data();
                $filename = $uploaded_data['file_name'];

                $dataSet = array(
                    'asset_id' => $asset_id,
                    'description' => $description,
                    'filename' => $filename
                );
                if ($this->db->insert('gccasset.asset_document', $dataSet)) {
                    $id = $this->db->insert_id();
                    $data = array(
                        'success' => true,
                        'message' => 'Document upload successful.'
                    );
                    $this->core_layout->setEventLog("User added new document with the the db id of `".$id."` in asset documents.","insert", "success", "gccasset", "user");
                }else{
                    $id = $this->db->insert_id();
                    $this->core_layout->setEventLog("User failed to add new document with the the db id of `".$id."` in asset documents.","insert", "error", "gccasset", "system");
                }
            }



            return $data;
        }

        function updateDocument() {
            $resultSet = array();
            $post = $tableConfigStd = $this->utilities->parseFormDataToObject($this->input->post());
            $id = $post->id;
            $asset_id = $post->asset_id;
            $folder = 'uploads/files/' . $asset_id;
            $upload_data = null;

            if (!file_exists($folder)) {
                mkdir($folder, 0775, true);
            }

            $this->db->trans_begin();

            if ($_FILES['file']['name'] != "" || $_FILES['file']['name'] != null) {
                $config['upload_path'] = $folder;
                $config['allowed_types'] = '*';
                $config['max_size'] = '10000';
                $config['file_name'] = $_FILES['file']['name'];

                $this->upload->initialize($config);

                if ($this->upload->do_upload('file')) {
                    $upload_data = $this->upload->data();
                }

                unlink($folder . "/" . $post->current_filename);
            }

            $field = array(
                "description" => $post->description,
                "filename" => $upload_data ? $upload_data['file_name'] : $post->current_filename
            );
            $this->db->where("id", $id);
            $this->db->update("gccasset.asset_document", $field);

            if ($this->db->trans_status() === FALSE) {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $resultSet['success'] = true;
                $resultSet['message'] = "Document was updated.";
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function processEdit() {
            $session_data = $this->session->userdata('logged_in');

            $_sessData = array();
            $_sessData['username'] = $session_data['firstname'] . ' ' . $session_data['lastname'];

            $date = date('Y-m-d');

            if ($this->input->post('date_purchased') == '') {
                $datepurchased = '0000-00-00';
            } else {
                $datepurchased = $this->input->post('date_purchased');
            }

            $stat_created = '';
            $stat_dated = '';

            if ($this->input->post('status') != 'operational') {
                $stat_created = $_sessData['username'];
                $stat_dated = $date;
            }

            $isJunk = 0;
            $isDamage = 0;

            $statusData = $this->input->post("status");

            switch ($statusData):
                case "junk":
                    $isJunk = 1;
                    $isDamage = 0;
                    break;
                case "damage":
                    $isJunk = 0;
                    $isDamage = 1;
                    break;
                default:
                    $isJunk = 0;
                    $isDamage = 0;
                    break;
            endswitch;

            $locationId = $this->input->post('area');
            $arrLocation = array();
            $loc = $this->db->get_where("gccasset.location", array("id" => $locationId));
            if ($loc->num_rows() == 1) {
                $rowLoc = $loc->row_array();
                $arrLocation["area"] = (isset($rowLoc["location"]) && $rowLoc["location"]) ? $rowLoc["location"] : "";
                $arrLocation["area_id"] = (isset($rowLoc["id"]) && $rowLoc["id"]) ? $rowLoc["id"] : 0;
            }

            $data = array(
                'company_code' => $this->input->post('company_id'),
                'department_code' => $this->input->post('department_id'),
                'gl_code' => $this->input->post('gl_code'),
                'name' => $this->input->post('name'),
                'assetname' => $this->input->post('assetname'),
                'datepurchased' => $datepurchased,
                'purchaseprice' => $this->input->post('purchaseprice2'),
                'life' => $this->input->post('life'),
                'beg_addcost' => $this->input->post('beg_addcost2'),
                'total_cost' => $this->input->post('total_cost2'),
                'salvage_value' => $this->input->post('salvage_value2'),
                'location' => $this->input->post('location_id'),
                'area' => (isset($arrLocation["area"]) && $arrLocation["area"]) ? $arrLocation["area"] : "",
                'area_id' => (isset($arrLocation["area_id"]) && $arrLocation["area_id"]) ? $arrLocation["area_id"] : 0,
                'serialno' => $this->input->post('serialno'),
                'brand' => $this->input->post('brand'),
                'supplier' => $this->input->post('supplier'),
                'check_no' => $this->input->post('check_no'),
                'po_no' => $this->input->post('po_no'),
                'stockcode' => $this->input->post('stockcode'),
                'modelno' => $this->input->post('modelno'),
                'dateUpdated' => date("Y-m-d h:i:s"),
                'updatedBy' => $_sessData['username'],
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'ar' => $this->input->post('ARNo'),
                'stats_created' => $stat_created,
                'stats_dated' => $stat_dated,
                'rr_no' => $this->input->post('rrno'),
                'date_received' => $this->input->post('date_received'),
                'isJunk' => $isJunk,
                'isDamage' => $isDamage,
            );

            $this->fixAssetUpdate(array('id' => $this->input->post('id')), $data);

            return array("status" => TRUE, "toastr_msg" => "Asset successfully updated!", "data" => $data);
        }

        private function fixAssetUpdate($where, $data) {
            $this->db->update($this->assetTable, $data, $where);
            return $this->db->affected_rows();
        }

        function ajaxUploadedImages() {
            $arrData = array();
            $post = $this->input->post();
            if ($post['id']) {
                $prim = $this->db->get_where("gccasset.asset_pictures", array("asset_id" => $post["id"]));
                if ($prim->num_rows() == 1) {
                    $row = $prim->row_array();
                    $arrData["primary"] = $row["image"];
                    $arrData["id"] = $post["id"];
                }

                $alt = $this->db->get_where("gccasset.asset_image", array("asset_id" => $post["id"]));
                if ($alt->num_rows() > 0) {
                    foreach ($alt->result_array() as $rs) {
                        if (isset($rs["image"]) && $rs["image"]) {
                            $thumbImage = $this->checkImageThumbnailPath($rs["image"], $post['id']);
                            if ($thumbImage == false) {
                                $this->core_layout->resizeImage($rs["image"]);
                            }
                            $arrData["thumbnail"][] = base_url("uploads/files/images/{$post["id"]}/thumbnail/{$rs['image']}");
                            $arrData["url"][] = base_url("uploads/files/images/{$post["id"]}/{$rs['image']}");
                            $arrData["name"][] = $rs['image'];
                        }
                    }
                }
            }

            $data = array("data" => $arrData);
            $html = $this->load->view("assets/templates/image_container/content", $data, true);

            $resultset = array();
            $resultset["html"] = $html;
            return $resultset;
        }

        function loadModalUpload() {
            $post = $this->input->post();

            $html = $this->load->view("assets/templates/upload/content", $post, true);
            $resultset = array();
            $resultset["html"] = $html;

            return $resultset;
        }

        function getAssetDocuments($asset_id) {
            $table = 'gccasset.asset_document';

            $paginationData = $this->utilities->parseFormDataToObject($this->input->post());
            $pageConfig = $this->utilities->getDatatablesConfigForPagination($paginationData);
            $length = $pageConfig->length;
            $start = $pageConfig->start;
            $search = $pageConfig->search;
            $direction = $pageConfig->order_direction;
            $column = $pageConfig->order_column;

            $where = array(
                'asset_id' => $asset_id
            );

            $searchFields = 'CONCAT(description, filename)';

            $this->db->where($where);
            $this->db->like($searchFields, $search, "both");
            if ($length > -1) {
                $this->db->limit($length, $start);
            }

            $resultSet = array();
            $resultSet['data'] = $this->db
                ->order_by($column, $direction)
                ->get($table)
                ->result();

            if(!empty($search)){
                $this->core_layout->setEventLog("User searched `".$search."` in asset documents.","search", "success", "gccasset", "user");
            }

            $like = array('field' => $searchFields, 'key' => $search, 'option' => 'both');
            $resultSet['recordsFiltered'] = $this->utilities->getTableCount($table, $where, $like);
            $resultSet['recordsTotal'] = $this->utilities->getTableCount($table, $where, $like);

            return $resultSet;
        }

        function removeAssetComponent($id) {
            $resultSet = array();

            $this->db->trans_begin();

            $asset_id = $this->db->get_where("gccasset.asset_components", array("id" => $id))->row("asset_id");
            $this->db->where("id", $asset_id)->update("gccasset.assets", array("mother_asset" => 0));

            $this->db->reset_query(); // start fresh query

            $this->db->where("id", $id);
            $this->db->delete("gccasset.asset_components");

            if ($this->db->trans_status() === FALSE) {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $resultSet['success'] = true;
                $resultSet['message'] = "Asset component was removed.";
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function getAssetDocumentDetails($formData) {
            $_formData = json_encode($formData, true);
            $objFormData = json_decode($_formData);

            $asset_document_id = $objFormData->asset_document_id;
            $resultSet['data'] = $this->db->get_where("gccasset.asset_document", array("id" => $asset_document_id))->row();
            return $resultSet;
        }

        function deleteAssetDocument($id) {
            if ($this->db->delete("gccasset.asset_document", array("id" => $id))) {
                return array("success" => true, "message" => "Document was deleted.");
            }
        }

        /* ARCHIVE FUNCTIONS */
        function archiveAsset($type) {
            $id = $this->input->get("id");
            $archive_remark = $this->input->post("archive_remark");
            $status = $this->input->post('status');
            $field = array("archive_remark" => $archive_remark, "is_archived" => 1, "status" => $this->input->post('status'));
            $result = array();
            $notif = ($type === "mother asset" ? "Asset" : "Asset component") . " was archived.";

            $this->db->trans_begin();

            $this->db->where("id", $id);
            if ($this->db->update("gccasset.assets", $field)) {
                $result["success"] = true;
                $result["message"] = $notif;

                $msg = 'Succesfully archived assets with db id of <b>'.$id.'</b> with status of '.$status.' remarks of `'.$archive_remark.'`.';
                $this->core_layout->setEventLog($msg, "archive", "success", "gccasset", "user");
            } else {
                $result["success"] = false;
                $result["message"] = $this->db->error();

                $msg = 'Failed to archive assets with db id of <b>'.$id.'</b> with status of '.$status.' remarks of `'.$archive_remark.'`.';
                $this->core_layout->setEventLog($msg, "archive", "error", "gccasset", "system");
            }

            $this->db->reset_query();

            if ($type === "asset component") {
                $this->db->delete("gccasset.asset_components", array("asset_id" => $id));
            }

            /** commented to change logs to gccasset.user_logs_event */
            // archive log, insert to gccmaster.archived_items
            // $this->core->insertArchiveLog("gccasset.assets", $id);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }

            return $result;
        }

        /* END ARCHIVE FUNCTIONS */

        function getArchiveCollection($type) {
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $sql = "a.id, a.id as asset_id, UPPER(a.assetacode) as assetacode, UPPER(a.name) as name, UPPER(a.assetname) as description, UPPER(a.location) as location_id, ";
            $sql .= "a.area_id, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, UPPER(a.department_code) as department_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.modelno) as modelno, UPPER(a.status) as status, ";
            $sql .= "IF(a.location != '', UPPER(c.station), 'N/A') AS station, IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.dateCreated, ";
            $sql .= "IFNULL(null,a.pic_filename) as image, a.datepurchased, b.location, UPPER(d.description) as asset_category";
            $sql .= ", IF(a.isComponent = 1, 'Component', 'Mother Asset') asset_type, a.archive_remark";

            $searchFields = "CONCAT(IF(a.assetacode IS NULL, '', a.assetacode), IF(a.gl_code IS NULL, '', a.gl_code), IF(a.gen_code IS NULL, '', a.gen_code),
            IF(a.name IS NULL, '', a.name), IF(a.assetname IS NULL, '', a.assetname), IF(a.location IS NULL, '', a.location), 
            IF(a.company_code IS NULL, '', a.company_code), IF(a.department_code IS NULL, '', a.department_code), IF(a.modelno IS NULL, '', a.modelno),
            IF(a.archive_remark IS NULL, '', a.archive_remark), IFNULL(a.status, ''))";

            if ($type !== "all") {
                $where = "a.isComponent=" . ($type === "mother" ? 0 : 1) . " AND a.`status` NOT IN('operational', 'brandnew') AND a.`status` IS NOT NULL AND a.`status` != ''";
            } else {
                $filter = $this->input->post('filter');
                if (isset($filter) && $filter !== "all") {
                    $where = "a.isComponent=" . ($filter === "mother" ? 0 : 1) . " AND a.`status` NOT IN('operational', 'brandnew') AND a.`status` IS NOT NULL AND a.`status` != ''";
                } else {
                    $where = "a.`status` NOT IN('operational', 'brandnew', 'new') AND a.`status` IS NOT NULL AND a.`status` != '' AND a.`archive_remark` IS NOT NULL";
                }
            }

            $joinArr = array(
                array(
                    "table" => "gccasset.location b",
                    "condition" => "b.id = a.area_id",
                    "option" => "LEFT"
                ),
                array(
                    "table" => "gccasset.station c",
                    "condition" => "c.id = a.location",
                    "option" => "LEFT"
                ),
                array(
                    "table" => "gccasset.assetcategory d",
                    "condition" => "d.code = a.asset_category",
                    "option" => "LEFT"
                ),
                array(
                    "table" => "gccasset.asset_sub_cat e",
                    "condition" => "e.sub_cat_id = a.sub_cat_code",
                    "option" => "LEFT"
                )
            );

            $this->db->select($sql);
            $this->db->from("gccasset.assets a");
            if ($joinArr) {
                foreach ($joinArr as $join) {
                    $this->db->join($join["table"], $join["condition"], $join["option"]);
                }
            }
            $this->db->where($where);
            $this->db->like($searchFields, $pageOptions->search, "both");
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $data = array();
            $queryResult = $this->db
                ->order_by($pageOptions->order_column, $pageOptions->order_direction)
                ->get()
                ->result();
            
            if($queryResult){
                if(!empty($pageOptions->search)){
                    $this->core_layout->setEventLog("User searched `".$pageOptions->search."` filtered by `".$type."` in Asset datatable.","search", "success", "gccasset", "user");
                }
            }else{
                $this->core_layout->setEventLog("User failed to searched `".$pageOptions->search."` filtered by `".$type."` in Asset datatable.","search", "error", "gccasset", "system");
            }

            $resultSet["data"] = $queryResult;
            $like = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => 'both');
            $resultSet["recordsTotal"] = $this->utilities->getTableCount("gccasset.assets a", $where, $like, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount("gccasset.assets a", $where, $like, $joinArr);
            return $resultSet;
        }

        function restoreArchivedAsset($id) {
            $resultSet = array();
            $post = $this->input->post();

            $this->db->trans_begin();

            $this->db->where("id", $id);
            if ($this->db->update("gccasset.assets", array("is_archived" => 0, "archived_dt" => '0000-00-00', "archive_remark" => "", 'status' => 'operational'))) {
                $this->core->insertArchiveLog("gccasset.assets", $id, 2);
                $this->core_layout->setEventLog("User restored db id `".$id."` in masterfile datatable.","restore", "success", "gccasset", "user");
                $resultSet["success"] = true;
                $resultSet["message"] = "Asset was restored back to masterfile.";
            } else {
                $this->core_layout->setEventLog("User failed to restore db id `".$id."` in masterfile datatable.","restore", "error", "gccasset", "system");
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function deleteAsset($id) {
            $this->db->trans_begin();

            $this->db->select('id, assetacode, name, assetname, isComponent');
            $this->db->where('id', $id);
            $this->db->from('gccasset.assets');
            $row = $this->db->get()->row();

            $this->db->reset_query();

            if ($this->db->delete("gccasset.assets", array("id" => $id))) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Asset was permanently deleted.";

                $msg = "User deleted " . (($row->isComponent == 0) ? 'fixed asset' : 'component asset') . " with db id `{$row->id}`, asset code `<b>{$row->assetacode}</b>`, asset name `<b>{$row->name}</b>` and description of `<b>{$row->assetname}</b>`";

                $this->core_layout->setEventLog($msg, "delete", "success", "gccasset", "user");
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();

                $msg = "User deleted " . (($row->isComponent == 0) ? 'fixed asset' : 'component asset') . " with db id `{$row->id}`, asset code `<b>{$row->assetacode}</b>`, asset name `<b>{$row->name}</b>` and description of `<b>{$row->assetname}</b>`";
                $this->core_layout->setEventLog($msg, "delete", "error", "gccasset", "system");
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function restoreArchivedAssetsMultiple() {
            $post = $this->input->post();
            // $multiple_id = $this->input->post('multiple_id');
            // $multiple_id_arr = explode(",", $multiple_id);
            $multiple_id_arr = explode(",", $post['multiple_id']);
            $resultSet = array();

            // $output = '';
            // foreach($multiple_id_arr as $id){
            //     $output .= $id.', ';
            // }

            // if ($this->db->update("gccasset.assets", array("status" => ""))) { //original source code mhen mass restore
            $this->db->where_in("id", $multiple_id_arr);
            $query = $this->db->update("gccasset.assets", array("status" => 'operational', 'is_archived' => 0, 'archived_dt' => '0000-00-00', 'archive_remark' => ''));
            if ($query) {
                $this->core_layout->setEventLog("User restored db id `".$post['multiple_id']."` in masterfile datatable.","restore", "success", "gccasset", "user");
                $resultSet["success"] = true;
                $resultSet["message"] = "Assets was restored back to masterfile.";
            } else {
                $this->core_layout->setEventLog("User failed to restore db id `".$post['multiple_id']."` in masterfile datatable.","restore", "error", "gccasset", "system");
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
            }

            return $resultSet;
        }

        function deleteArchivedAssetsMultiple() {
            $multiple_id = $this->input->post('multiple_id');
            $multiple_id_arr = explode(",", $multiple_id);
            $resultSet = array();
            $this->db->where_in("id", $multiple_id_arr);
            if ($this->db->delete("gccasset.assets")) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Assets was permanently deleted.";
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
            }

            return $resultSet;
        }

        function saveNewAsset($isComponent, $motherId) {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $post->area = $this->db->where("id", $post->area_id)->get("gccasset.location")->row("location");
            $post->datepurchased = ($post->datepurchased === "" || $post->datepurchased === null) ? "" : date_format(date_create($post->datepurchased), 'Y-m-d');
            $post->date_received = ($post->date_received === "" || $post->date_received === null) ? "" : date_format(date_create($post->date_received), 'Y-m-d');
            $post->isGen = isset($post->isGen) ? 1 : 0;
            $post->isComponent = $isComponent;
            // $post->primary_pic = isset($post->primary_pic) ? preg_replace('/\s+/', '_', $post->primary_pic) : null;
            $post->purchaseprice = str_replace(',', '', $post->purchaseprice);
            $post->beg_addcost = str_replace(',', '', $post->beg_addcost);
            $post->total_cost = str_replace(',', '', $post->total_cost);
            $primary_pic = isset($post->primary_pic) ? $post->primary_pic : ""; // pic_filename == $primary_pic

            // $mode = $post->mode;

            unset($post->primary_pic);
            unset($post->mode);
            $isComponentLog = 0;

            if ($post->isGen == 1) {
                if ($motherId) {
                    $mother = $this->db->where("id", $motherId)->get("gccasset.assets")->row();
                    /*$last_component_gen_code = $this->db->where("mother_asset", $motherId)->order_by("assetacode", "desc")->limit(1)->get("gccasset.assets")->row("assetacode");
                    $gen_code = $mother->gen_code;

                    $parts = explode('-', $last_component_gen_code);
                    $end = (int)array_pop($parts) + 1;

                    $post->mother_asset = $motherId;
                    $post->series = 0;
                    $post->gen_code = $gen_code . "-" . $end;*/

                    $components_gen_code = $this->db->select("assetacode")
                        ->where("isComponent", 1)
                        ->group_start()
                        ->like("gen_code", $mother->gen_code)
                        ->or_like("assetacode", $mother->gen_code)
                        ->or_like("gl_code", $mother->gen_code)
                        ->group_end()
                        ->order_by("assetacode", "desc")
                        ->get("gccasset.assets")
                        ->result();

                    $gen_code = $mother->gen_code;

                    $components_gen_code_end_digit = array_map(function ($row) {
                        $strArr = explode("-", $row->assetacode);
                        return intval(array_pop($strArr));
                    }, $components_gen_code);

                    $end = empty($components_gen_code_end_digit) ? 1 : (intval(max($components_gen_code_end_digit)) + 1);

                    $post->mother_asset = $motherId;
                    $post->series = 0;
                    $post->gen_code = $gen_code . "-" . $end;
                } else {
                    // $post->sub_cade_code is sub_cat_id
                    $generated_code = $this->generateAssetCode2($post->asset_category, $post->sub_cat_code);
                    $code = $generated_code->code;
                    $series = $generated_code->series;

                    $post->series = $series;
                    $post->gen_code = $code;
                }
            }

            $exist = $this->checkIfGenCodeExist($post->gen_code);
            if (intval($exist)) {
                $resultSet["success"] = false;
                $resultSet["status"] = "duplicated";
                $resultSet["message"] = "Unable to save. <span class='m--font-bolder'> Asset Code: " . $post->gen_code . "</span> duplicated.";
                return $resultSet;
            }

            $post->assetacode = $post->gen_code;
            $post->dateCreated = $this->today->format("Y-m-d H:i:s");
            $post->dateUpdated = $this->today->format("Y-m-d H:i:s");
            $post->createdBy = strtoupper($this->getUserData()["display_name"]);

            $resultSet = array();
            $this->db->trans_begin();
            $id = null;

            if ($this->db->insert("gccasset.assets", $post)) {
                $id = $this->db->insert_id();
                $uploadResult = array();

                if (isset($_FILES['files'])) {
                    $uploadResult = $this->uploadFiles($id, $primary_pic); // pic_filename == $primary_pic
                }

                if ($motherId) {
                    $fields = array(
                        "asset_id" => $id,
                        "mother_asset" => $motherId,
                        "description" => $post->assetname // description
                    );

                    $this->db->insert("gccasset.asset_components", $fields);
                }

                $resultSet["success"] = true;
                $isComponentlog = ((int)$isComponent == 0) ? "FIXED ASSET":"ASSET COMPONENT";
                $this->core_layout->setEventLog("User added new asset with the the db id of `".$id."` in ".$isComponentLog." masterfile datatable.","insert", "success", "gccasset", "user");

                $resultSet["message"] = "New " . ((int)$isComponent === 0 ? "asset" : "asset component") .
                    " Named: <span class='m--font-boldest'>" . strtoupper($post->name) .
                    "</span> with Asset Code: <span class='m--font-boldest'>" .
                    $post->gen_code . "</span>  has been added.";

                $resultSet["id"] = $id;
                $resultSet["fail_uploads"] = !empty($uploadResult) ? $uploadResult["upload_errors"] : array();
                $resultSet["primary_pic"] = !empty($uploadResult) ? $uploadResult["success_primary_pic"] : null;
                $resultSet["uploading_primary_pic_failed"] = !empty($uploadResult) ? $uploadResult["uploading_primary_pic_failed"] : null;
            } else {
                $isComponentlog = ((int)$isComponent == 0)? "FIXED ASSET":"ASSET COMPONENT";
                $this->core_layout->setEventLog("User failed to add new asset with the the db id of `".$id."` in ".$isComponentlog." masterfile datatable.","insert", "error", "gccasset", "system");
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        private function checkIfGenCodeExist($gen_code) {
            $this->db->where("gen_code", $gen_code);
            $this->db->or_where("gl_code", $gen_code);
            $this->db->or_where("assetacode", $gen_code);
            return $this->db->count_all_results("gccasset.assets");
        }

        function updateFixedAsset($isComponent) {
            $this->db->trans_begin();
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $post->area = $this->db->where("id", $post->area_id)->get("gccasset.location")->row("location");
            // $post->datepurchased = ($post->datepurchased === "" || $post->datepurchased === null) ? "" : date_format(date_create($post->datepurchased), 'Y-m-d');
            $post->date_received = ($post->date_received === "" || $post->date_received === null) ? "" : date_format(date_create($post->date_received), 'Y-m-d');
            $post->isGen = isset($post->isGen) ? 1 : 0;

            $post->purchaseprice = str_replace(',', '', $post->purchaseprice);
            $post->beg_addcost = str_replace(',', '', $post->beg_addcost);
            $post->total_cost = str_replace(',', '', $post->total_cost);

            $id = $post->id;
            $primary_pic = null;
            $primary_pic_from_uploaded = (isset($post->primary_pic_from_uploaded) && $post->primary_pic_from_uploaded == "true") ? TRUE : FALSE;

            $uploadedImagesToRemove = json_decode($post->uploadedImagesToRemove);

            unset($post->id);
            unset($post->primary_pic_from_uploaded);
            unset($post->uploadedImagesToRemove);

            if (isset($post->pic_filename)) {
                if ($primary_pic_from_uploaded) {
                    $post->pic_filename = preg_replace('/\s+/', '_', $post->pic_filename);
                } else {
                    $primary_pic = $post->pic_filename;
                    unset($post->pic_filename);
                }
            }

            // remove images on submit
            if (!empty($uploadedImagesToRemove)) {
                $images = $this->removeUploadedImages($uploadedImagesToRemove);
            }

            $post->dateUpdated = $this->today->format("Y-m-d H:i:s");
            $post->updatedBy = strtoupper($this->getUserData()["display_name"]);

            $resultSet = array();
            $this->db->where("id", $id);
            if($post->status == 'junk' OR $post->status == 'lost' OR $post->status == 'sold' OR $post->status == 'destructed'){
                
                $post->is_archived = 1;
                $archive_asset = $this->db->update("gccasset.assets", $post);
                    // if($archive_vehicle){
                    //     $this->core->insertArchiveLog("gccasset.vehicles", $id);
                    // }
                $resultSet["success"] = true;
                $resultSet["message"] = ((int)$isComponent === 0 ? "Asset" : "Asset component") . " was archived.";
                $resultSet["fail_uploads"] = "";
                $resultSet["id"] = $id;
                $resultSet["status"] = 'archived';
            }else{
                if ($this->db->update("gccasset.assets", $post)) {
                    $uploadResult = array();

                    if (isset($_FILES['files'])) {
                        $uploadResult = $this->uploadFiles($id, $primary_pic);
                    }
                    $isCom = ($isComponent == 0) ? "fixed asset" : "asset component";
                    $this->core_layout->setEventLog("User updated the ".$isCom." with the the db id of `".$id."` in ".$isCom.".","update", "success", "gccasset", "user");
                    $resultSet["success"] = true;
                    $resultSet["message"] = ((int)$isComponent === 0 ? "Asset" : "Asset component") . " information was updated.";
                    $resultSet["fail_uploads"] = !empty($uploadResult) ? $uploadResult["upload_errors"] : array();
                    $resultSet["pic_filename"] = !empty($uploadResult) ? $uploadResult["success_primary_pic"] : null;
                    $resultSet["uploading_primary_pic_failed"] = !empty($uploadResult) ? $uploadResult["uploading_primary_pic_failed"] : null;
                    $resultSet["uploaded"] = !empty($uploadResult) ? $uploadResult["uploaded"] : array();
                    $resultSet["id"] = $id;
                } else {
                    $isCom = ($isComponent == 0) ? "fixed asset" : "asset component";
                    $this->core_layout->setEventLog("User failed to update the ".$isCom." with the the db id of `".$id."` in ".$isCom.".","update", "error", "gccasset", "system");
                    $resultSet["success"] = false;
                    $resultSet["message"] = $this->db->error();
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            
            // $post->primary_pic_from_uploaded = (isset($primary_pic_from_uploaded) && ($primary_pic_from_uploaded != "") && $primary_pic_from_uploaded === true);
            return $resultSet;
        }

        private function generateAssetCode2($asset_category, $sub_cat_id) {
            $result = array();
            $series = $this->db->select_max("series", "series_max")
                ->where("asset_category", $asset_category)
                ->where("sub_cat_code", $sub_cat_id)
                ->get("gccasset.assets")
                ->row("series_max");

            $next = $series + 1;
            $sub_cat_code = $this->db->get_where("gccasset.asset_sub_cat", array("sub_cat_id" => $sub_cat_id))->row("sub_cat_code");
            $code = $asset_category . "-" . $sub_cat_code . "-" . str_pad($next, 4, "0", STR_PAD_LEFT);

            $result["code"] = $code;
            $result["series"] = $next;

            return json_decode(json_encode($result, true));
        }

        private function uploadFiles($id, $primary_pic) {
            $upload_errors = array();
            $user = $this->getUserData();
            $user_id = $user["id"];

            $count = count($_FILES['files']['name']);
            $uploaded = array();
            $uploading_primary_pic_failed = false;
            $success_primary_pic = null;

            for ($i = 0; $i < $count; $i++) {
                if (!empty($_FILES['files']['name'][$i])) {
                    $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                    $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                    $_FILES['file']['size'] = $_FILES['files']['size'][$i];
                    $original_name = $_FILES['files']['name'][$i];

                    $folder = 'uploads/files/images/assets/' . $id;

                    if (!file_exists($folder)) {
                        mkdir($folder, 0775, true);
                    }

                    $config['upload_path'] = $folder;
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = '5000';
                    $config['file_name'] = $_FILES['file']['name'];

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('file')) {
                        $upload_data = $this->upload->data();
                        $filename = $upload_data['file_name'];
                        array_push($uploaded, $original_name);

                        if ($primary_pic == $original_name) {
                            $this->db->where("id", $id)->update("gccasset.assets", array("pic_filename" => $filename));
                            $success_primary_pic = $filename;
                        }

                        $timestamp = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $fields = array("asset_id" => $id, "image" => $filename, "useruploaded" => $user_id, "dateupload" => $timestamp->format('Y-m-d H:i:s'));
                        $this->db->insert("gccasset.asset_image", $fields);

                        if ($this->core_upload->createThumbnailPathFolder($folder)) {
                            $this->resizeImage2($filename, $folder, '/thumbnails', 75, 75);
                        }
                    } else {
                        array_push($upload_errors,
                            array(
                                "filename" => $original_name,
                                "message" => $this->upload->display_errors(),
                                "isPrimary" => ($original_name == $primary_pic)
                            ));
                    }
                }
            }

            $findFailedPrimaryPic = array_filter($upload_errors,
                function ($e) {
                    return $e["isPrimary"] == true;
                });

            $uploading_primary_pic_failed = empty($findFailedPrimaryPic) ? FALSE : TRUE;
            if ($uploading_primary_pic_failed) {
                if (!empty($uploaded)) {
                    $new_primary = preg_replace('/\s+/', '_', $uploaded[0]);
                    $this->db->where("id", $id)->update("gccasset.assets", array("pic_filename" => $new_primary));
                    $success_primary_pic = $uploaded[0];
                } else {
                    $success_primary_pic = $this->db->select("image")
                        ->get_where("gccasset.asset_image", array("asset_id" => $id))
                        ->row("image");
                    $this->db->where("id", $id)->update("gccasset.assets", array("pic_filename" => $success_primary_pic));
                }
            }

            return array(
                "upload_errors" => $upload_errors,
                "success_primary_pic" => $success_primary_pic,
                "uploading_primary_pic_failed" => $uploading_primary_pic_failed,
                "uploaded" => $this->db->where("asset_id", $id)->get("gccasset.asset_image")->result());
        }

        private function resizeImage2($filename, $source_path, $target_path, $width, $height = 0) {
            $_source_path = rtrim($source_path, '/') . "/" . $filename;
            $_target_path = rtrim($source_path, '/') . $target_path;

            $config = array(
                'image_library' => 'gd2',
                'source_image' => $_source_path,
                'new_image' => $_target_path,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '',
                'width' => $width
            );

            if ($height) {
                $config["height"] = $height;
            }

            $this->image_lib->initialize($config);
            return $this->image_lib->resize();
        }

        function getAssetDetails($id) {
            $this->db->select("asset.*, comp.description company_name, asset_cat.description asset_cat_description, 
                               stat.name status_name, dep.description dep_description, 
                               station.station station_description, location.location location_description,
                               stat.code status_code, IF(emp.id IS NULL, asset.inventory_check_by, CONCAT(emp.firstname, ' ', emp.lastname)) checked_by,
                               IF(emp2.id IS NULL, asset.updatedBy, CONCAT(emp2.firstname, ' ', emp2.lastname)) _updatedBy");
            $this->db->join("gcchris.tblcompanies comp", "comp.code = asset.company_code", "left");
            $this->db->join("gccasset.assetcategory asset_cat", "asset_cat.code = asset.asset_category", "left");
            $this->db->join("gccasset.status stat", "`stat`.`code` = asset.status", "left");
            $this->db->join("gcchris.tbldepartments dep", "dep.id = asset.department_code", "left");
            $this->db->join("gccasset.station station", "station.id = asset.location", "left");
            $this->db->join("gccasset.location location", "location.id = asset.area_id", "left");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = asset.inventory_check_by", "left");
            $this->db->join("gccmaster.tblemployees emp2", "emp2.id = asset.updatedBy", "left");
            $asset = $this->db->get_where("gccasset.assets asset", array("asset.id" => $id))->row();

            if (!is_numeric($asset->department_code)) {
                $first_department = $this->db->order_by("id", "asc")->get_where("gcchris.tbldepartments", array("code" => $asset->department_code))->row();
                $asset->dep_description = empty($first_department) ? null : $first_department->description;
                $asset->department_code = empty($first_department) ? null : $first_department->id;
            }

            if (isset($asset->mother_asset)) {
                $mother_asset = $this->getMotherAsset($asset->mother_asset);

                $asset->mother_code = isset($mother_asset['assetacode']) ? $mother_asset['assetacode'] : "";
                $asset->mother_name = isset($mother_asset['name']) ? $mother_asset['name'] : "";
            } else {
                $asset->mother_code = NULL;
                $asset->mother_name = NULL;
            }

            return $asset;
        }

        function getMotherAsset($id) {
            $this->db->select("assetacode, name");
            $this->db->from("gccasset.assets");
            $this->db->where("id", $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        function getAssetImages($id) {
            $this->db->select("id, image name, null as base64", FALSE);
            return $this->db->get_where("gccasset.asset_image", array("asset_id" => $id))->result();
        }

        function removeUploadedImages($id) {
            $this->db->where_in("id", $id);
            $images = $this->db->get("gccasset.asset_image")->result();

            foreach ($images as $image) {
                $file = "uploads/files/images/assets/" . $image->asset_id . "/" . $image->image;
                $thumb = "uploads/files/images/assets/" . $image->asset_id . "/thumbnails/" . $image->image;

                if (file_exists($file)) {
                    unlink($file);
                }
                if (file_exists($thumb)) {
                    unlink($thumb);
                }

                $this->db->where("id", $image->id)->delete("gccasset.asset_image");
            }
        }

        function getPrimaryPic($id) {
            $this->db->where("id", $id);
            $this->db->select("pic_filename");
            $_data = $this->db->get("gccasset.assets")->row();
            $filename = $_data->pic_filename;
            $resultSet = array(
                "pic_filename" => ""
            );

            if ($filename) {
                $path = "uploads/files/images/assets/" . $id . "/" . $filename;
                if (file_exists($path)) {
                    $resultSet['pic_filename'] = $filename;
                }
            }

            return $resultSet;
        }

        function getAssetList($isMother) {
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
            $table = "gccasset.assets a";

            $sql = "a.id, a.id as asset_id, UPPER(a.assetacode) as assetacode, UPPER(a.name) as name, UPPER(a.assetname) as description, UPPER(a.location) as location_id, ";
            $sql .= "a.area_id, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, UPPER(a.department_code) as department_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.modelno) as modelno, UPPER(a.status) as status, ";
            $sql .= "IF(a.location != '', UPPER(c.station), 'N/A') AS station, IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.dateCreated, ";
            $sql .= "IFNULL(null,a.pic_filename) as image, a.datepurchased, b.location, UPPER(d.description) as asset_category";
            $sql .= ", a.is_borrowed, a.mother_asset mother_asset_id";

            $where = "a.isComponent = " . ($isMother == 1 ? 0 : 1) . " AND (a.status NOT IN('archived', 'lost', 'junk', 'tradein') OR `status` IS NULL OR `status` = '') AND a.isJunk != 1";
            $where .= " AND a.mother_asset = 0";

            $searchFields = "CONCAT(IF(a.assetacode IS NULL, '', a.assetacode), IF(a.`name`IS NULL, '', a.name), 
                            IF(a.`assetname`IS NULL, '', a.assetname), IF(a.`location`IS NULL, '', a.location), 
                            IF(a.brand IS NULL, '', a.brand), IF(a.modelno IS NULL, '', a.modelno), IF(a.company_code IS NULL, '', a.company_code),
                            IF(a.department_code IS NULL, '', a.department_code))";

            $joinArr = array(
                array("table" => "gccasset.location b", "condition" => "b.id = a.area_id", "option" => "LEFT"),
                array("table" => "gccasset.station c", "condition" => "c.id = a.location", "option" => "LEFT"),
                array("table" => "gccasset.assetcategory d", "condition" => "d.id = a.asset_category", "option" => "LEFT"),
                array("table" => "gccasset.asset_sub_cat e", "condition" => "e.sub_cat_id = a.sub_cat_code", "option" => "LEFT"),
            );

            $this->db->select($sql);
            $this->db->from($table);
            if ($joinArr) {
                foreach ($joinArr as $join) {
                    $this->db->join($join["table"], $join["condition"], $join["option"]);
                }
            }

            $this->db->where($where);
            $this->db->like($searchFields, $pageOptions->search, "both");
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $queryResult = $this->db
                ->order_by($pageOptions->order_column, $pageOptions->order_direction)
                ->get()
                ->result();

            $data = array();

            if (count($queryResult) > 0) {
                foreach ($queryResult as $key => $rs) {
                    $id = $rs->asset_id;
                    $imageFile = $rs->image;
                    $imagePath = "uploads/files/images/{$id}";
                    $mother_asset_id = $rs->mother_asset_id;

                    if (file_exists($imagePath) && $imageFile) {
                        $rs->image = base_url("uploads/files/images/{$id}/{$imageFile}");
                        $rs->img_thumbnail = base_url("uploads/files/images/{$id}/thumbnails/{$imageFile}");
                        $rs->primary_pic = $imageFile;
                    } else {
                        $rs->primary_pic = null;
                        $rs->image = null;
                        $rs->img_thumbnail = null;
                    }

                    array_push($data, $rs);
                }
            }

            $resultSet["data"] = $data;

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` in asset list.","search", "success", "gccasset", "user");
            }

            $like = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => 'both');
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $like, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $like, $joinArr);
            return $resultSet;
        }

        function saveComponentToMotherAsset() {
            $resultSet = array();
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $description = $this->db->get_where("gccasset.assets", array("id" => $post->asset_id))->row("assetname");
            $isMother = $post->isMother;

            $fields = array(
                "asset_id" => $post->asset_id,
                "mother_asset" => $post->parent_id,
                "description" => $description
            );

            $this->db->trans_begin();

            $this->db->insert("gccasset.asset_components", $fields);

            // update vehicle/asset
            $this->db->where("id", $post->asset_id)
                ->update("gccasset.assets", array("mother_asset" => $post->parent_id, "isComponent" => 1));

            if ($this->db->trans_status() === FALSE) {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $resultSet['success'] = true;
                $resultSet['message'] = "Component was added.";
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function excludeComponent($id) {
            $resultSet = array();
            $post = $this->input->post();
            $componentField = array(
                "remarks" => isset($post["remarks"]) ? $post["remarks"] : "",
                "isExcluded" => 1
            );

            $this->db->trans_begin();

            $this->db->where("id", $id)
                ->update("gccasset.asset_components", $componentField);

            $this->db->reset_query(); // start fresh query

            $asset_id = $this->db->get_where("gccasset.asset_components", array("id" => $id))->row("asset_id");
            $isDamage = isset($post["isDamage"]) ? 1 : 0;

            $this->db->reset_query(); // start fresh query

            $this->db->where("id", $asset_id)->update("gccasset.assets",
                array(
                    "mother_asset" => 0,
                    "isDamage" => $isDamage,
                    // "is_borrowed" => 0
                ));

            if ($this->db->trans_status() === FALSE) {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $resultSet['success'] = true;
                $resultSet['message'] = "Exclusion successful.";
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function reIncludeComponent($id) {
            $resultSet = array();

            $this->db->where("id", $id);
            if ($this->db->update("gccasset.asset_components", array("isExcluded" => 0))) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Component was re-included.";
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
            }

            return $resultSet;
        }

        function getDocumentItem($id) {
            $resultSet = array();
            $this->db->where("id", $id);
            $resultSet['data'] = $this->db->get("gccasset.asset_document")->row();

            return $resultSet;
        }

        function removeDocument($id) {
            $resultSet = array();
            $document = $this->db->where("id", $id)->get("gccasset.asset_document")->row();
            $file_path = "uploads/files/asset_documents/" . $document->asset_id . "/" . $document->filename;

            if (file_exists($file_path)) {
                unlink($file_path);
            }

            if ($this->db->delete("gccasset.asset_document", array("id" => $id))) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Document was removed.";
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
            }

            return $resultSet;
        }

        function getAssetAccountability($id) {
            $resultSet = array();
            $table = "gccasset.assets assets";
            $searchFields = "CONCAT(accountability.date_issued, accountability.reference_no, 
                             accountability.issued_to, accountability.status, 
                             employees.firstname, employees.lastname, IFNULL(contractor.contractor, ''))";

            $joinArr = array(
                array(
                    "table" => "gcceforms.accountability_body body",
                    "condition" => "body.asset_id = assets.id AND body.type = 'Asset'",
                    "option" => "inner"
                ),
                array(
                    "table" => "gcceforms.accountability accountability",
                    "condition" => "accountability.id = body.accountability_id",
                    "option" => "inner"
                ),
                array(
                    "table" => "gccmaster.tblemployees employees",
                    "condition" => "accountability.issued_to = employees.id",
                    "option" => "inner"
                ),
                array(
                    "table" => "gcchris.tblcontractor contractor",
                    "condition" => "contractor.id = accountability.issued_to",
                    "option" => "left"
                ),
                array(
                    "table" => "gcchris.tblcompanies company",
                    "condition" => "company.id = accountability.company",
                    "option" => "left"
                ),
                array(
                    "table" => "gcceforms.borrowing_body bor_body",
                    "condition" => "bor_body.asset_id = assets.id AND bor_body.type = 'Asset'",
                    "option" => "inner"
                ),
                array(
                    "table" => "gcceforms.borrowing borrowing",
                    "condition" => "borrowing.id = bor_body.borrowing_id",
                    "option" => "left"
                ),
                
            );

            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $where = array(
                "body.asset_id" => $id,
                "body.type" => 'asset'
            );
            $s = array();

            $this->db->select("DATE(accountability.date_issued) date_issued, accountability.reference_no as reference_no, 
            accountability.issued_to as borrower, body.is_returned status, accountability.company, 'ACCOUNTABILITY' as module,
                               CONCAT(employees.firstname, ' ', employees.lastname) employee, 'N/A' as overdue,
                               accountability.status acct_status, accountability.is_contract, contractor.contractor, contractor.company as cont_comp, IF(company.id IS NULL, accountability.company, company.description) as company");
            $this->db->where($where);
            foreach ($joinArr as $join) {
                $this->db->join($join['table'], $join['condition'], $join['option']);
            }
            $this->db->like($searchFields, $pageOptions->search, "both");
            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }
            $results1 = $this->db->get($table);
            $s = array_merge($s, $results1->result());

            $this->db->select("DATE(borrowing.date_trans) date_issued, borrowing.reference_no as reference_no, 
            borrowing.borrower as borrower, bor_body.is_returned status, borrowing.company, 'BORROWING' as module,
                               CONCAT(employees.firstname, ' ', employees.lastname) employee, DATEDIFF(CURDATE(), DATE(bor_body.date_due)) overdue,
                               borrowing.status acct_status, IF(company.id IS NULL, borrowing.company, company.description) as company");
            $this->db->where($where);
            foreach ($joinArr as $join) {
                $this->db->join($join['table'], $join['condition'], $join['option']);
            }
            $this->db->like($searchFields, $pageOptions->search, "both");
            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }
            $results2 = $this->db->get($table);
            $s = array_merge($s, $results2->result());
            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            // $arr = array_merge($data,$data2);
            $resultSet['data'] = $s;
            $resultSet["recordsTotal"] = count($s);
            $resultSet["recordsFiltered"] = count($s);
            $resultSet["sql"] = $this->db->last_query();
            return $resultSet;
        }

        function getAssetAccountabilityBorrowing($id){
            $search = $this->input->post();
            $search_result = $search["search"];
            
            if($search["search"] != NULL OR $search["search"] != ""){
                $like = "AND (reference_no LIKE '%$search_result%' OR date_issued LIKE '%$search_result%')";
            }else{
                $like = "";
            }
            // $query = $this->db->query("SELECT * from (SELECT acc.reference_no as reference_no, DATE(acc.date_issued) as date_issued, acc.issued_to as borrower, 'ACCOUNTABILITY' as module, 'N/A' as days_overdue, acc_body.is_returned as `status`, acc.created_dt as created_dt, acc.company as company, acc_body.asset_id as asset_id, acc_body.`type` as asset_type FROM gcceforms.accountability_body as acc_body LEFT JOIN gcceforms.accountability as acc ON acc.id=acc_body.accountability_id UNION ALL SELECT bor.reference_no as reference_no, DATE(bor.date_trans) as date_issued, bor.borrower as borrower, 'BORROWING' as module, DATEDIFF(CURDATE(), DATE(bor_body.date_due)) days_overdue, bor_body.is_returned as `status`, bor.created_dt as created_dt, bor.company as company, bor_body.asset_id as asset_id, bor_body.`type` as asset_type FROM gcceforms.borrowing_body as bor_body LEFT JOIN gcceforms.borrowing as bor ON bor.id=bor_body.borrowing_id) as table1 WHERE asset_id='$id' AND asset_type='asset' $like order by created_dt desc");

            $query = $this->db->query("SELECT * from (SELECT acc.reference_no as reference_no, DATE(acc.date_issued) as date_issued, acc.issued_to as borrower, 'ACCOUNTABILITY' as module, 'N/A' as days_overdue, acc_body.is_returned as `status`, acc.created_dt as created_dt, acc.company as company, acc_body.asset_id as asset_id, acc_body.`type` as asset_type FROM gcceforms.accountability_body as acc_body LEFT JOIN gcceforms.accountability as acc ON acc.id=acc_body.accountability_id UNION ALL SELECT bor.reference_no as reference_no, DATE(bor.date_trans) as date_issued, bor.borrower as borrower, 'BORROWING' as module, IF(CURDATE() < DATE(bor_body.date_due), 'N/A', DATEDIFF(CURDATE(), DATE(bor_body.date_due))) as days_overdue, IF(bor.status = 'Cancelled', 1, bor_body.is_returned) as `status`, bor.created_dt as created_dt, bor.company as company, bor_body.asset_id as asset_id, bor_body.`type` as asset_type FROM gcceforms.borrowing_body as bor_body LEFT JOIN gcceforms.borrowing as bor ON bor.id=bor_body.borrowing_id) as table1 WHERE asset_id='$id' AND asset_type='asset' $like order by created_dt desc");
            
            $data = $query->result_array();

            $resultSet = array();
            $display = array();
            $data_content = array();
            foreach($data as $result){
                $display['reference_no'] = $result['reference_no'];
                $name = $this->core_layout->getEmployeeData($result['borrower']);
                $name = (object) $name;
                $tempName = isset($name->display_name_1) && $name->display_name_1 ? strtoupper($name->display_name_1): "NO ASSIGNED NAME";
                if(strtolower($result["module"]) === "accountability"){
                    $qAcct = $this->db->get_where("gcceforms.accountability", array("reference_no"=>$result['reference_no']));
                    if($qAcct->num_rows() == 1){
                        if(intval($qAcct->row()->is_contract) == 1){
                            $contractor = $this->db->get_where("gcchris.tblcontractor", array("id"=>$result['borrower']));
                            if($contractor->num_rows() == 1){ $tempName = strtoupper($contractor->row()->contractor); }
                        }
                    }
                }
                $display['borrower'] = $tempName;
                // $this->getEmployementData($result['borrower'],'company');
                $display['company'] = $this->getEmployementData($result['borrower'],'company');
                $display['date_issued'] = $result['date_issued'];
                $display['module'] = $result['module'];
                if($result['days_overdue'] != 'N/A'){
                    if($result['days_overdue'] > 1 AND $result['days_overdue'] != 0){
                        $day = "days";
                    }else{
                        $day = "day";
                    }
                }else{
                    $day = "";
                }    
                $display['over_due'] = $result['days_overdue']." ".$day;
                $display['status'] = $result['status'];
                $data_content[] = $display;
            }

            if(!empty($search['search'])){
                $this->core_layout->setEventLog("User searched `".$search['search']."` in asset accountability.","search", "success", "gccasset", "user");
            }
            $resultSet['data'] = $data_content;
            $resultSet["recordsTotal"] = count($data_content);
            $resultSet["recordsFiltered"] = count($data_content);
            
            return $resultSet;
        }

        function getEmployementData($id,$column=null){
            $this->load->model("eforms/loa_m");
            $this->db->select("*");
            $this->db->where("id",$id);
            $query = $this->db->get("gccmaster.tblemployees");
            $data = $query->row_array();
            if($column == 'department'){
                if(is_numeric($data['department_id'])){
                    return $this->db->get_where("gcchris.tbldepartments", array("id"=>$data['department_id']))->row("description");
                }else{
                    return $data['department_id'];
                }
            }else if($column == 'company'){
                if(is_numeric(trim($data['company_id']))){
                    return $this->db->get_where("gcchris.tblcompanies", array("id"=>$data['company_id']))->row("description");
                }else{
                    return $data['company_id'];
                }
            }else{
                if(is_numeric($data['position'])){
                    return $this->db->get_where("gcchris.tblposition", array("id"=>$data['position']))->row("name");
                }else{
                    return $data['position'];
                }
            }
        }

        function getAssetBorrowingHistory($id) {
            $resultSet = array();
            $table = "gcceforms.borrowing_body body";
            /*$searchFields = "CONCAT(borrowing.date_trans, borrowing.reference_no,
                             borrowing.status, employees.firstname, employees.lastname)";*/

            /* TO FOLLOW EXISTING FUNCTION IN WEB PORTAL, GETS THE RETURN REMARKS ON BORROWING BODY*/
            $searchFields = "CONCAT(borrowing.date_trans, borrowing.reference_no, 
                             body.return_remarks, employees.firstname, employees.lastname)";

            $joinArr = array(
                array(
                    "table" => "gccasset.assets assets",
                    "condition" => "body.asset_id = assets.id AND body.type = 'asset'",
                    "option" => "inner"
                ),
                array(
                    "table" => "gcceforms.borrowing borrowing",
                    "condition" => "borrowing.id = body.borrowing_id",
                    "option" => "inner"
                ),
                array(
                    "table" => "gccmaster.tblemployees employees",
                    "condition" => "borrowing.borrower = employees.id",
                    "option" => "inner"
                ),
                array(
                    "table" => "gcchris.tblcompanies company",
                    "condition" => "company.id = borrowing.company",
                    "option" => "left"
                ),
            );

            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $where = array(
                "asset_id" => $id
            );

            /*$this->db->select("borrowing.date_trans, borrowing.reference_no,
                               borrowing.borrower, borrowing.status, borrowing.company,
                               CONCAT(employees.firstname, ' ', employees.lastname) employee");*/

            /* TO FOLLOW EXISTING FUNCTION IN WEB PORTAL, GETS THE RETURN REMARKS ON BORROWING BODY*/
            $this->db->select("DATE(borrowing.date_trans) date_trans, borrowing.reference_no, 
                               borrowing.borrower borrower, body.is_returned status, borrowing.company company,
                               CONCAT(employees.firstname, ' ', employees.lastname) employee,
                               DATEDIFF(CURDATE(), DATE(body.date_due)) days_overdue, 
                               date_due due_date,IF(company.id IS NULL, borrowing.company, company.description) as company");
            $this->db->where($where);
            foreach ($joinArr as $join) {
                $this->db->join($join['table'], $join['condition'], $join['option']);
            }
            $this->db->like($searchFields, $pageOptions->search, "both");
            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }
            $data = $this->db->get($table)->result();

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");

            $resultSet['data'] = $data;
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);

            return $resultSet;
        }

        function checkIfAssetIsBorrowedOrAccounted() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $asset_id = $post->asset_id;
            $type = $post->type;
            $isComponent = $post->isComponent;
            $accountability = null;
            $borrowing_history = null;
            $has_mother_asset = FALSE;
            $mother_asset_accountability = null;

            $selectAccountabilityFields = "acct_body.asset_id, CONCAT('(',TRIM(assets.assetacode),')', if(assets.`name` IS NULL OR assets.`name`='',";
            $selectAccountabilityFields .= "assets.assetname, assets.`name`)) asset_name, acct_body.is_returned, ";
            $selectAccountabilityFields .= "acct.issued_to, IF(acct.is_contract=1, contractors.contractor, ";
            $selectAccountabilityFields .= "CONCAT(employees.firstname, ' ', employees.lastname)) issued_to, acct.reference_no";

            $selectBorrowingHistory = "CONCAT(employees.firstname, ' ', employees.lastname) borrower_name,";
            $selectBorrowingHistory .= "if(assets.`name` IS NULL OR assets.`name`='', assets.assetname, assets.`name`) asset_name,";
            $selectBorrowingHistory .= "br.borrower borrower_id, br_body.is_returned,";

            if ($isComponent != 0) {
                $mother_asset = $this->db->select("mother_asset")->where("id", $asset_id)->get("gccasset.assets")->row("mother_asset");

                if ($mother_asset != 0) {
                    $this->db->select($selectAccountabilityFields);
                    $this->db->join("gcceforms.accountability acct", "acct_body.accountability_id = acct.id AND LCASE(acct_body.`type`)='$type'", "inner");
                    $this->db->join("gcchris.tblcontractor contractors", "contractors.id = acct.issued_to", "LEFT");
                    $this->db->join("gccmaster.tblemployees employees", "employees.id = acct.issued_to", "LEFT");
                    $this->db->join("gccasset.assets assets", "assets.id = acct_body.asset_id", "inner");
                    $this->db->where("acct_body.asset_id", $mother_asset);
                    $this->db->where("acct_body.is_returned", 0);
                    $this->db->where("acct.status !=", "Cancelled");
                    $mother_asset_accountability = $this->db->get("gcceforms.accountability_body acct_body")->row();
                    $this->db->reset_query();
                }

                $has_mother_asset = $mother_asset != 0;
            }

            $this->db->select($selectAccountabilityFields);
            $this->db->join("gcceforms.accountability acct", "acct_body.accountability_id = acct.id AND LCASE(acct_body.`type`)='$type'", "inner");
            $this->db->join("gcchris.tblcontractor contractors", "contractors.id = acct.issued_to", "LEFT");
            $this->db->join("gccmaster.tblemployees employees", "employees.id = acct.issued_to", "LEFT");
            $this->db->join("gccasset.assets assets", "assets.id = acct_body.asset_id", "inner");
            $this->db->where("acct_body.asset_id", $asset_id);
            $this->db->where("acct_body.is_returned", 0);
            $this->db->where("acct.status !=", "Cancelled");
            $accountability = $this->db->get("gcceforms.accountability_body acct_body")->row();
            // $x = $this->db->last_query();

            $this->db->reset_query();

            $this->db->select($selectBorrowingHistory);
            $this->db->join("gcceforms.borrowing br", "br_body.borrowing_id = br.id AND LCASE(br_body.`type`)='$type'", "inner");
            $this->db->join("gccmaster.tblemployees employees", "employees.id = br.borrower", "INNER");
            $this->db->join("gccasset.assets assets", "assets.id = br_body.asset_id", "INNER");
            $this->db->where("br_body.asset_id", $asset_id);
            $this->db->where("br_body.is_returned", 0);
            $this->db->where("br.status !=", "Cancelled");
            $borrowing_history = $this->db->get("gcceforms.borrowing_body br_body")->row();

            // if current asset is not accounted get data from master file table
            $this->db->reset_query();

            if ((!empty($accountability) && $has_mother_asset && $mother_asset_accountability) ) {
                // $accountability = $this->db
                //     ->select("CONCAT('(',TRIM(assets.assetacode),')', if(`name` IS NULL OR `name`='', assetname, `name`)) asset_name")
                //     ->where("id", $asset_id)
                //     ->get("gccasset.assets")
                //     ->row();
                $accountability = $this->db
                    ->select("CONCAT('(',TRIM(assets.assetacode),')', if(assets.`name` IS NULL OR assets.`name`='', assets.assetname, assets.`name`)) asset_name, acct.reference_no, CONCAT(employees.firstname, ' ', employees.lastname) issued_to")
                    ->join("gcceforms.accountability_body acct_body", "acct_body.asset_id = assets.id", "inner")
                    ->join("gcceforms.accountability acct", "acct_body.accountability_id = acct.id AND LCASE(acct_body.`type`)='$type'", "inner")
                    ->join("gccmaster.tblemployees employees", "employees.id = acct.issued_to", "LEFT")
                    ->where("assets.id", $asset_id)
                    ->where('acct_body.is_returned', 0)
                    ->where("acct.status !=", "Cancelled")
                    ->get("gccasset.assets as assets")
                    ->row();
            }

            return array(
                "accountability" => $accountability,
                "borrowing_history" => $borrowing_history,
                "has_mother_asset" => $has_mother_asset,
                "mother_asset_accountability" => $mother_asset_accountability
            );
        }

        function relocateAssetImages($isComponent = 0) {
            ini_set('max_execution_time', 6000);
            set_time_limit(6000);

            $root_dir = "uploads/files/images";
            $live_image_dir = "http://bcd.gccph.com/ams/server/php/images/files";

            $this->db->select("assets.id asset_id, assets.pic_filename");
            $this->db->where("assets.isComponent = " . $isComponent . " AND (assets.status NOT IN('archived', 'lost', 'junk', 'tradein') OR `status` IS NULL) AND assets.isJunk != 1");
            $list = $this->db->get("gccasset.assets assets")->result();

            $this->db->reset_query();

            foreach ($list as $item) {
                $dir = $root_dir . "/assets/" . $item->asset_id;
                $thumbnail_path = $dir . "/thumbnails";

                $images = $this->db
                    ->where("asset_id", $item->asset_id)
                    ->where("image !=", '')
                    ->get("gccasset.asset_image")
                    ->result();

                if (!empty($item->pic_filename) && $item->pic_filename != '') {
                    $dir_exist = file_exists(realpath($dir));

                    if (!$dir_exist) {
                        mkdir($dir, 0775, true);
                    }

                    $thumbnail_path_exist = file_exists(realpath($thumbnail_path));

                    if (!$thumbnail_path_exist) {
                        mkdir($thumbnail_path, 0775, true);
                    }

                    $main_image_file_path = $dir . "/" . $item->pic_filename;

                    $this->insertMainImageInAssetImage($item->asset_id, $item->pic_filename);

                    $live_main_image_filepath = $live_image_dir . "/" . $item->pic_filename;

                    if (!file_exists(realpath($main_image_file_path))) {
                        $main_image_content = $this->get_contents($live_main_image_filepath);

                        if (!empty($main_image_content)) {
                            if (file_put_contents($main_image_file_path, $main_image_content)) {
                                $thumbnail_file_path = $thumbnail_path . "/" . $item->pic_filename;
                                $this->saveThumbnail($main_image_file_path, $thumbnail_file_path);
                            }
                        }
                    }
                }

                foreach ($images as $image) {
                    $filepath = $dir . "/" . $image->image;

                    $live_image_filepath = $live_image_dir . "/" . $image->image;
                    $file_exist = file_exists(realpath($filepath));

                    if (!$file_exist) {
                        $remote_file_contents = $this->get_contents($live_image_filepath);

                        if (!empty($remote_file_contents)) {
                            if (file_put_contents($filepath, $remote_file_contents)) {
                                $thumbnail_file_path = $thumbnail_path . "/" . $image->image;
                                $this->saveThumbnail($filepath, $thumbnail_file_path);
                            }
                        }
                    }
                }
            }
        }

        function relocateArchivedAssetImages() {
            ini_set('max_execution_time', 6000);
            set_time_limit(6000);

            $root_dir = "uploads/files/images";
            $live_image_dir = "http://bcd.gccph.com/ams/server/php/images/files";

            $this->db->select("assets.id asset_id, assets.pic_filename");
            $this->db->where("status", "archived");
            // $this->db->where("id", 116);
            $list = $this->db->get("gccasset.assets assets")->result();

            $this->db->reset_query();

            foreach ($list as $item) {
                $dir = $root_dir . "/assets/" . $item->asset_id;
                $thumbnail_path = $dir . "/thumbnails";

                $images = $this->db
                    ->where("asset_id", $item->asset_id)
                    ->where("image !=", '')
                    ->get("gccasset.asset_image")
                    ->result();

                if (!empty($item->pic_filename)) {
                    $dir_exist = file_exists(realpath($dir));

                    if (!$dir_exist) {
                        mkdir($dir, 0775, true);
                    }

                    $thumbnail_path_exist = file_exists(realpath($thumbnail_path));

                    if (!$thumbnail_path_exist) {
                        mkdir($thumbnail_path, 0775, true);
                    }

                    $main_image_file_path = $dir . "/" . $item->pic_filename;

                    $this->insertMainImageInAssetImage($item->asset_id, $item->pic_filename);

                    $live_main_image_filepath = $live_image_dir . "/" . $item->pic_filename;

                    if (!file_exists(realpath($main_image_file_path))) {
                        $main_image_content = $this->get_contents($live_main_image_filepath);

                        if (!empty($main_image_content)) {
                            if (file_put_contents($main_image_file_path, $main_image_content)) {
                                $thumbnail_file_path = $thumbnail_path . "/" . $item->pic_filename;
                                $this->saveThumbnail($main_image_file_path, $thumbnail_file_path);
                            }
                        }
                    }
                }

                foreach ($images as $image) {
                    $filepath = $dir . "/" . $image->image;

                    $live_image_filepath = $live_image_dir . "/" . $image->image;
                    $file_exist = file_exists(realpath($filepath));

                    if (!$file_exist) {
                        $remote_file_contents = $this->get_contents($live_image_filepath);

                        if (!empty($remote_file_contents)) {
                            if (file_put_contents($filepath, $remote_file_contents)) {
                                $thumbnail_file_path = $thumbnail_path . "/" . $image->image;
                                $this->saveThumbnail($filepath, $thumbnail_file_path);
                            }
                        }
                    }
                }
            }
        }

        private function insertMainImageInAssetImage($asset_id, $filename) {
            $this->db->where("asset_id", $asset_id);
            $this->db->where("image", $filename);
            $count = $this->db->count_all_results("gccasset.asset_image");

            if ($count <= 0) {
                $data = array(
                    "asset_id" => $asset_id,
                    "image" => $filename,
                    "dateupload" => date('Y-m-d H:i:s'),
                    "useruploaded" => $this->core_layout->getUserId()
                );
                $this->db->insert("gccasset.asset_image", $data);
            }
        }

        function relocateAssetDocuments() {
            ini_set('max_execution_time', 6000);
            set_time_limit(6000);

            $root_dir = "uploads/files";
            $live_image_dir = "http://bcd.gccph.com/ams/server/php/images/files";

            $this->db->select("document.id, document.filename, assets.id asset_id");
            $this->db->join("gccasset.assets assets", "assets.id = document.asset_id", "inner");
            $list = $this->db->get("gccasset.asset_document document")->result();

            foreach ($list as $item) {
                $dir = $root_dir . "/asset_documents/" . $item->asset_id;
                $filepath = $dir . "/" . $item->filename;

                $dir_exist = file_exists(realpath($dir));
                if (!$dir_exist) {
                    mkdir($dir, 0775, true);
                }

                $live_image_filepath = $live_image_dir . "/" . $item->filename;
                $file_exist = file_exists(realpath($filepath));

                if (!$file_exist) {
                    $remote_file_contents = $this->get_contents($live_image_filepath);

                    if (!empty($remote_file_contents)) {
                        file_put_contents($filepath, $remote_file_contents);
                    }
                }
            }
        }

        function relocateVehicleImages() {
            ini_set('max_execution_time', 6000);
            set_time_limit(6000);

            $root_dir = "uploads/files/images";
            $live_image_dir = "http://bcd.gccph.com/ams/server/php/images/files";

            $isComponent = '';

            $this->db->select("assets.id asset_id, assets.pic_filename");
            $this->db->where("assets.isComponent = " . $isComponent . " AND (assets.status NOT IN('archived', 'lost', 'junk', 'tradein') OR `status` IS NULL) AND assets.isJunk != 1");
            $list = $this->db->get("gccasset.assets assets")->result();

            $this->db->reset_query();

            /*foreach ($list as $item) {
                $dir = $root_dir . "/assets/" . $item->asset_id;
                $thumbnail_path = $dir . "/thumbnails";

                $images = $this->db
                    ->where("asset_id", $item->asset_id)
                    ->where("image !=", '')
                    ->get("gccasset.asset_image")
                    ->result();

                if (!empty($item->pic_filename) && $item->pic_filename != '') {
                    $dir_exist = file_exists(realpath($dir));

                    if (!$dir_exist) {
                        mkdir($dir, 0777, true);
                    }

                    $thumbnail_path_exist = file_exists(realpath($thumbnail_path));

                    if (!$thumbnail_path_exist) {
                        mkdir($thumbnail_path, 0777, true);
                    }

                    $main_image_file_path = $dir . "/" . $item->pic_filename;

                    $this->insertMainImageInAssetImage($item->asset_id, $item->pic_filename);

                    $live_main_image_filepath = $live_image_dir . "/" . $item->pic_filename;

                    if (!file_exists(realpath($main_image_file_path))) {
                        $main_image_content = $this->get_contents($live_main_image_filepath);

                        if (!empty($main_image_content)) {
                            if (file_put_contents($main_image_file_path, $main_image_content)) {
                                $thumbnail_file_path = $thumbnail_path . "/" . $item->pic_filename;
                                $this->saveThumbnail($main_image_file_path, $thumbnail_file_path);
                            }
                        }
                    }
                }

                foreach ($images as $image) {
                    $filepath = $dir . "/" . $image->image;

                    $live_image_filepath = $live_image_dir . "/" . $image->image;
                    $file_exist = file_exists(realpath($filepath));

                    if (!$file_exist) {
                        $remote_file_contents = $this->get_contents($live_image_filepath);

                        if (!empty($remote_file_contents)) {
                            if (file_put_contents($filepath, $remote_file_contents)) {
                                $thumbnail_file_path = $thumbnail_path . "/" . $image->image;
                                $this->saveThumbnail($filepath, $thumbnail_file_path);
                            }
                        }
                    }
                }
            }*/
        }

        private function get_contents($url, $u = false, $c = null, $o = null) {
            $headers = get_headers($url);
            $status = substr($headers[0], 9, 3);
            if ($status == '200') {
                return file_get_contents($url, $u, $c, $o);
            }
            return null;
        }

        private function saveThumbnail($source, $target) {
            $config = array(
                'image_library' => 'gd2',
                'source_image' => $source,
                'new_image' => $target,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '',
                'width' => 75,
                'height' => 75
            );
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            $this->image_lib->clear();
        }

        public function accountability_search($id) {
            $this->db->select("b.asset_id");
            $this->db->from("gcceforms.accountability_body b");
            $this->db->join("gcceforms.accountability c", "b.accountability_id = c.id", "INNER");
            $this->db->where("b.type", "Asset");
            $this->db->where("b.is_returned !=", 1);
            $this->db->where("c.status !=", "Cancelled");
            $this->db->where("c.issued_to", $id);
            $query = $this->db->get();
            return $query->result_array();
        }

        public function getEmployeeName() {
            $get = $this->input->get();
            $q = isset($get['q']) ? $get['q'] : '';
            $resultSet = array();

            $employee_name = "CONCAT(emp.firstname, ' ', IF((emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none'), ' ', 
                            CONCAT(SUBSTR(emp.middlename,1,1), '. ')), emp.lastname, ' ', 
                            IF((emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none'), ' ', emp.suffix))";
            $searchFields = "CONCAT(CONCAT(emp.firstname, ' ', IF((emp.middlename = '' OR emp.middlename IS NULL OR LCASE(emp.middlename) = 'n/a' OR LCASE(emp.middlename) = 'none'), ' ', 
                            CONCAT(SUBSTR(emp.middlename,1,1), '. ')), emp.lastname, ' ', 
                            IF((emp.suffix = '' OR emp.suffix IS NULL OR LCASE(emp.suffix) = 'n/a' OR LCASE(emp.suffix) = 'none'), ' ', emp.suffix)), lastname, firstname, middlename, suffix)";

            $this->db->select("emp.id, UCASE($employee_name) text");
            $this->db->like($searchFields, $q, "BOTH");

            $this->db->order_by("TRIM(emp.firstname)", "ASC");
            $resultSet["results"] = $this->db->get("gccmaster.tblemployees emp")->result();

            return $resultSet;
        }

        function getLocation() {
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $query = $this->db->query("SELECT `id`,`location` FROM gccasset.location WHERE `location` LIKE '%{$get['q']}%' ORDER BY `location` ASC");
            } else {
                $query = $this->db->query("SELECT `id`,`location` FROM gccasset.location ORDER BY `location` ASC");
            }

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["location"];
                    $data["text"] = $_query["location"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function getCategory() {
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
                    $data["id"] = $_query["description"];
                    $data["text"] = $_query["description"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function getStation() {
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $query = $this->db->query("SELECT `id`,`station` FROM gccasset.station WHERE `station` LIKE '%{$get['q']}%' ORDER BY `station` ASC");
            } else {
                $query = $this->db->query("SELECT `id`,`station` FROM gccasset.station ORDER BY `station` ASC");
            }

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["station"];
                    $data["text"] = $_query["station"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        public function inventoryCheck() {
            date_default_timezone_set("Asia/Manila");
            $status = "";
            $post = $this->input->post();
            $session_data = $this->session->userdata('logged_in');
            $name = $session_data['firstname'] . ' ' . $session_data['lastname'];

            $state = (isset($post["asset_type"]) && $post["asset_type"]) ? $post["asset_type"] : "verified";
            $invDate = (isset($post["inventory_check_date"]) && $post["inventory_check_date"] !== "1970-01-01") ? $post["inventory_check_date"] : "";

            $nDate = ($invDate) ? date("Y-m-d", strtotime($invDate)) : date("Y-m-d");

            $data = array();
            $data["inventory_check_by"] = $session_data["emp_id"];
            $data["inventory_check_date"] = $nDate;
            $data["updatedBy"] = $name;
            $data["dateUpdated"] = date("Y-m-d h:i:s");

            $this->db->where('id', $post["id"]);
            $doInventory = $this->db->update("gccasset.assets", $data);

            //$doInventory = $this->crud->update($data ,array("id"=>$post["id"]) ,"gccasset.assets");
            $status = $doInventory ? TRUE : FALSE;

            if ($status) {
                $id = $post["id"];
                $query = $this->db->get_where("gccasset.assets", array("id" => $id));
                if ($query->num_rows() == 1) {
                    $currentDate = date("Y-m-d H:i:s");
                    $userId = $session_data["id"];
                    $remarks = (isset($row->remarks2) && $row->remarks2) ? $row->remarks2 : "";
                    $_status = (isset($row->status) && $row->status) ? $row->status : "";

                    $ndata = array();
                    $ndata["asset_id"] = $id;
                    $ndata["asset_type"] = 1;

                    $metaData = array();
                    $metaData["inventory_date"] = $nDate;
                    $metaData["inventory_by"] = $userId;
                    $metaData["date_updated"] = "";
                    $metaData["updated_by"] = "";
                    $metaData["remarks"] = $remarks;
                    $metaData["status"] = $_status;
                    $metaData["inventory_status"] = $state;

                    $this->insertMetaData($ndata, $metaData);
                }
            }
            return array("status" => $status, "checked_by" => $name, "checked_date" => $nDate);
        }

        function insertMetaData($data = array(), $meta = array()) {
            $insert = $this->db->insert("gccasset.inventory_list", $data);
            if ($insert) {
                $insert_id = $this->db->insert_id();
                if ($meta) {
                    foreach ($meta as $key => $value) {
                        $_meta = array();
                        $_meta["meta_id"] = $insert_id;
                        $_meta["meta_field"] = $key;
                        $_meta["meta_value"] = $value;

                        $this->db->insert("gccasset.inventory_list_meta", $_meta);
                    }
                }
                return true;
            } else {
                return false;
            }
        }

        function recoverAsset($id, $type) {
            $post = $this->input->post();
            $d = new DateTime($post["inventory_check_date"]);
            $inventory_check_date = $d->format("Y-m-d");
            $inventory_check_by = $this->user["emp_id"];
            $this->db->db_debug = FALSE;

            $this->db->trans_begin();

            if (intval($type) === 1) {
                $this->db->where("id", $id);
                $this->db->set("inventory_check_date", $inventory_check_date);
                $this->db->set("inventory_check_by", $inventory_check_by);
                $this->db->set("status", $post["status"]);
                $this->db->set("is_archived", 0);
                $this->db->set("archive_remark", "");
                $this->db->set("remarks2", "CONCAT(remarks2, '\n', '" . $post["remarks2"] . "')", false);
                $this->db->set("updatedBy", $this->user["emp_id"]);
                $this->db->set("dateUpdated", $this->today->format("Y-m-d H:i:s"));
                $this->db->update("gccasset.assets");
            } else {
                $this->db->where("id", $id);
                $this->db->set("inventory_check_date", $inventory_check_date);
                $this->db->set("inventory_check_by", $inventory_check_by);
                $this->db->set("is_archived", 0);
                $this->db->set("archive_remark", "");
                $this->db->set("status2", $post["status2"]);
                $this->db->set("status", "CONCAT(status, '\n', '" . $post["status"] . "')", false);
                $this->db->set("updated_by", $this->user["emp_id"]);
                $this->db->set("updated_at", $this->today->format("Y-m-d H:i:s"));
                $this->db->update("gccasset.vehicles");
            }

            $this->db->reset_query();

            $inventory_list = $this->db->insert("gccasset.inventory_list", array("asset_id" => $id, "asset_type" => $type));
            if ($inventory_list) {
                $meta_id = $this->db->insert_id();
                $meta = array(
                    array("meta_id" => $meta_id, "meta_field" => "inventory_date", "meta_value" => $d->format("Y-m-d H:i:s")),
                    array("meta_id" => $meta_id, "meta_field" => "inventory_by", "meta_value" => $inventory_check_by),
                    array("meta_id" => $meta_id, "meta_field" => "date_updated", "meta_value" => ""),
                    array("meta_id" => $meta_id, "meta_field" => "updated_by", "meta_value" => ""),
                    array("meta_id" => $meta_id, "meta_field" => "remarks", "meta_value" => (intval($type) === 1 ? $post["remarks2"] : $post["status"])),
                    array("meta_id" => $meta_id, "meta_field" => "status", "meta_value" => (intval($type) === 1 ? $post["status"] : $post["status2"])),
                    array("meta_id" => $meta_id, "meta_field" => "inventory_status", "meta_value" => "recovered"),
                );
                $this->db->insert_batch("gccasset.inventory_list_meta", $meta);
            }

            $resultSet = array();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $resultSet["success"] = FALSE;
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "DB Error occurred.";
            } else {
                $this->db->trans_commit();
                $resultSet["success"] = TRUE;
                $resultSet["message"] = intval($type) === 1 ? "Asset / Asset Component was successfully recoved." : "Vehicle / Vehicle Component was successfully recoved.";
                $resultSet["title"] = "Inventory Check - Recovery";
            }

            $this->db->db_debug = $this->db_debug;
            $resultSet["id"] = $id;

            return $resultSet;
        }

        function generateAssetWithCheckNo($date=null){
            $results = array();
            $tempDateToday = $date ? date("Y-m-d", strtotime($date)): date("Y-m-d");

            $this->db->select("id, assetacode as asset_code, UPPER(name) as asset_name, 
                dateCreated as created_at, createdBy as created_by, dateUpdated as updated_at, updatedBy as updated_by, check_no, 
                IF(mother_asset = '0' && isComponent = '0', 'ASSET', 'ASSET COMPONENT') as type, 
                dateCreated as added_at, createdBy as added_by");
            $this->db->from("gccasset.assets");
            $this->db->where("DATE(dateCreated)", $tempDateToday);
            $this->db->where_not_in("TRIM(check_no)", array("N/A", "NA", "N", ""));
            $this->db->group_start();
            $this->db->where("dateUpdated IS NULL");
            $this->db->or_where("dateUpdated", "");
            $this->db->or_where("DATE(dateUpdated)", "0000-00-00");
            $this->db->group_end();
            $this->db->group_start();
            $this->db->where("updatedBy IS NULL");
            $this->db->or_where("updatedBy", 0);
            $this->db->group_end();

            $queryAddedAssets = $this->db->get();
            $arrDataAssetsAdded = array();
            $arrDataAssetsAdded = $queryAddedAssets->result_array();

            $this->db->select("id, gen_code as asset_code, UPPER(name) as asset_name, 
                created_at, created_by, updated_at, updated_by, check_no, 
                IF(motherID = '0' && isCompo = '0', 'VEHICLE', 'VEHICLE COMPONENT') as type, 
                created_at as added_at, created_by as added_by");
            $this->db->from("gccasset.vehicles");
            $this->db->where("DATE(created_at)", $tempDateToday);
            $this->db->where_not_in("TRIM(check_no)", array("N/A", "NA", "N", ""));
            $this->db->group_start();
            $this->db->where("updated_at IS NULL");
            $this->db->or_where("updated_at", "");
            $this->db->or_where("DATE(updated_at)", "0000-00-00");
            $this->db->group_end();
            $this->db->group_start();
            $this->db->where("updated_by IS NULL");
            $this->db->or_where("updated_by", 0);
            $this->db->group_end();

            $queryAddedVehicle = $this->db->get();
            $arrDataVehicleAdded = array();
            if($queryAddedVehicle->num_rows() > 0){
                foreach ($queryAddedVehicle->result_array() as $key => $value) {
                    if(isset($value["added_by"]) && is_numeric($value["added_by"])){
                        $data = $this->core_layout->getEmployeeData($value["added_by"]);
                        $data = (object) $data;
                        $tempName = isset($data->display_name_1) && $data->display_name_1 ? strtoupper($data->display_name_1): "NO ASSIGNED NAME";
                        $value["added_by"] = $tempName;
                    }else{
                        $value["added_by"] = "NO ASSIGNED NAME";
                    }
                    $arrDataVehicleAdded[$key] = $value;
                }
            }

            $resultCreatedAssets = array_merge($arrDataAssetsAdded, $arrDataVehicleAdded);

            $this->db->select("id, assetacode as asset_code, UPPER(name) as asset_name, 
            dateCreated as created_at, createdBy as created_by, dateUpdated as updated_at, updatedBy as updated_by, check_no, 
            IF(mother_asset = '0' && isComponent = '0', 'ASSET', 'ASSET COMPONENT') as type, 
            dateUpdated as added_at, updatedBy as added_by");
            $this->db->from("gccasset.assets");
            $this->db->where("DATE(dateUpdated)", $tempDateToday);
            $this->db->where_not_in("TRIM(check_no)", array("N/A", "NA", "N", ""));
            $this->db->group_start();
            $this->db->where("updatedBy IS NOT NULL");
            $this->db->or_where("updatedBy !=", 0);
            $this->db->group_end();
            
            $queryUpdatedAssets = $this->db->get();
            $arrDataAssetsUpdated = array();
            $arrDataAssetsUpdated = $queryUpdatedAssets->result_array();
            
            $this->db->select("id, gen_code as asset_code, UPPER(name) as asset_name, 
            created_at, created_by, updated_at, updated_by, check_no, 
            IF(motherID = '0' && isCompo = '0', 'VEHICLE', 'VEHICLE COMPONENT') as type,
            updated_at as added_at, updated_by as added_by");
            $this->db->from("gccasset.vehicles");
            $this->db->where("DATE(updated_at)", $tempDateToday);
            $this->db->where_not_in("TRIM(check_no)", array("N/A", "NA", "N", ""));
            $this->db->group_start();
            $this->db->where("updated_by IS NOT NULL");
            $this->db->or_where("updated_by !=", 0);
            $this->db->group_end();
            
            $queryUpdatedVehicle = $this->db->get();
            $arrDataVehiclesUpdated = array();
            if($queryUpdatedVehicle->num_rows() > 0){
                foreach ($queryUpdatedVehicle->result_array() as $key => $value) {
                    if(isset($value["added_by"]) && is_numeric($value["added_by"])){
                        $data = $this->core_layout->getEmployeeData($value["added_by"]);
                        $data = (object) $data;
                        $tempName = isset($data->display_name_1) && $data->display_name_1 ? strtoupper($data->display_name_1): "NO ASSIGNED NAME";
                        $value["added_by"] = $tempName;
                    }else{
                        $value["added_by"] = "NO ASSIGNED NAME";
                    }
                    $arrDataVehiclesUpdated[$key] = $value;
                }
            }

            $resultUpdatedAssets = array_merge($arrDataAssetsUpdated, $arrDataVehiclesUpdated);
            $results = array_merge($resultCreatedAssets, $resultUpdatedAssets);
            
            $resultset = array();
            $resultset["data"] = $results;
            return $resultset;
        }
        
        function check_multiple_if_borrowed_or_accounted() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $isComponent = isset($post->isComponent) && $post->isComponent ? $post->isComponent : 0;
            $type = $post->type;
            $accountability = array();
            $borrowing_history = array();
            $has_mother_asset = FALSE;
            $mother_asset_accountability = array();
            $data = array();

            $selectAccountabilityFields = "acct_body.asset_id, CONCAT('(',TRIM(assets.assetacode),')', if(assets.`name` IS NULL OR assets.`name`='',";
            $selectAccountabilityFields .= "assets.assetname, assets.`name`)) asset_name, acct_body.is_returned, ";
            $selectAccountabilityFields .= "acct.issued_to, IF(acct.is_contract=1, contractors.contractor, ";
            $selectAccountabilityFields .= "CONCAT(employees.firstname, ' ', employees.lastname)) issued_to";

            $selectBorrowingHistory = "CONCAT(employees.firstname, ' ', employees.lastname) borrower_name,";
            $selectBorrowingHistory .= "if(assets.`name` IS NULL OR assets.`name`='', assets.assetname, assets.`name`) asset_name,";
            $selectBorrowingHistory .= "br.borrower borrower_id, br_body.is_returned, assets.id as asset_id";

            if ($isComponent == 0) {
                $this->db->select($selectAccountabilityFields);
                $this->db->join("gcceforms.accountability acct", "acct_body.accountability_id = acct.id AND LCASE(acct_body.`type`)='$type'", "inner");
                $this->db->join("gcchris.tblcontractor contractors", "contractors.id = acct.issued_to", "LEFT");
                $this->db->join("gccmaster.tblemployees employees", "employees.id = acct.issued_to", "LEFT");
                $this->db->join("gccasset.assets assets", "assets.id = acct_body.asset_id", "inner");
                $this->db->where_in("acct_body.asset_id", $post->ids);
                $this->db->where("acct_body.is_returned", 0);
                $this->db->where("acct.status !=", "Cancelled");
    
                $accountability = $this->db->get("gcceforms.accountability_body acct_body")->result();
    
                $this->db->reset_query();
    
                $this->db->select($selectBorrowingHistory);
                $this->db->join("gcceforms.borrowing br", "br_body.borrowing_id = br.id AND LCASE(br_body.`type`)='$type'", "inner");
                $this->db->join("gccmaster.tblemployees employees", "employees.id = br.borrower", "INNER");
                $this->db->join("gccasset.assets assets", "assets.id = br_body.asset_id", "INNER");
                $this->db->where_in("br_body.asset_id", $post->ids);
                $this->db->where("br_body.is_returned", 0);
                $this->db->where("br.status !=", "Cancelled");
                $borrowing_history = $this->db->get("gcceforms.borrowing_body br_body")->result();
    
                $this->db->reset_query();
    
                $data = array(
                    "accountability" => $accountability,
                    "borrowing_history" => $borrowing_history,
                    "has_mother_asset" => $has_mother_asset,
                    "mother_asset_accountability" => $mother_asset_accountability
                );
            } else {
                $asset = array();
                $assets = $this->db->select('id, mother_asset')->where_in('id', $post->ids)->get('gccasset.assets');

                if ($assets->num_rows() > 0) {
                    $_data = array();

                    foreach ($assets->result() as $key => $value) {
                        if ($value->mother_asset > 0) {
                            $compIds = $this->db->select('id')
                                ->where('mother_asset', $value->mother_asset)
                                ->where_in('id', $post->ids)
                                ->get('gccasset.assets')
                                ->result();

                            $_temp = array_column($compIds, 'id');

                            $_data[$value->mother_asset] = array(
                                'id' => $value->mother_asset,
                                'components' => $_temp
                            );
                        } else {
                            $_data[$value->id] = array(
                                'id' => $value->id
                            );
                        }

                        $asset = $_data;
                    }
                }
                $this->db->reset_query();

                $mother_asset_accountability = array();
                foreach ($asset as $key => $value) {
                    $selectAccountabilityFields .= ', acct.reference_no';

                    $this->db->select($selectAccountabilityFields);
                    $this->db->join("gcceforms.accountability acct", "acct_body.accountability_id = acct.id AND LCASE(acct_body.`type`)='$type'", "inner");
                    $this->db->join("gcchris.tblcontractor contractors", "contractors.id = acct.issued_to", "LEFT");
                    $this->db->join("gccmaster.tblemployees employees", "employees.id = acct.issued_to", "LEFT");
                    $this->db->join("gccasset.assets assets", "assets.id = acct_body.asset_id", "inner");
                    $this->db->where_in("acct_body.asset_id", $value['id']);
                    $this->db->where("acct_body.is_returned", 0);
                    $this->db->where("acct.status !=", "Cancelled");
                    $_result = $this->db->get("gcceforms.accountability_body acct_body")->row();

                    $this->db->reset_query();
                    
                    if (!empty($_result)) {
                        $_temp = array();
                        $_temp = (array) $_result;

                        if (isset($value['components']) && !empty($value['components'])) {
                            $this->db->select($selectAccountabilityFields);
                            $this->db->join("gcceforms.accountability acct", "acct_body.accountability_id = acct.id AND LCASE(acct_body.`type`)='$type'", "inner");
                            $this->db->join("gcchris.tblcontractor contractors", "contractors.id = acct.issued_to", "LEFT");
                            $this->db->join("gccmaster.tblemployees employees", "employees.id = acct.issued_to", "LEFT");
                            $this->db->join("gccasset.assets assets", "assets.id = acct_body.asset_id", "inner");
                            $this->db->where_in("acct_body.asset_id", $value['components']);
                            $this->db->where("acct_body.is_returned", 0);
                            $this->db->where("acct.status !=", "Cancelled");
                            $component = $this->db->get("gcceforms.accountability_body acct_body")->result();
                            $this->db->reset_query();

                            $_temp['components'] = $component;
                        }

                        $mother_asset_accountability[] = $_temp;
                    }
                }
                
                $_tempArr = array();
                foreach($mother_asset_accountability as $key => $value) {
                    $parentRef = $value['reference_no'];

                    if (!empty($value['components'])) {
                        $matched = array_filter($value['components'], function($comp) use ($parentRef) {
                            return $comp->reference_no === $parentRef;
                        });

                        if (!empty($matched)) {
                            // keep parent with only matched components
                            $value['components'] = array_values($matched);
                            $_tempArr[] = $value;
                        } else {
                            // replace parent with all components
                            foreach ($value['components'] as $comp) {
                                $_tempArr[] = (array) $comp;
                            }
                        }
                    } else {
                        // no components, just keep parent
                        $_tempArr[] = $value;
                    }
                }

                $mother_asset_accountability = $_tempArr;

                $this->db->select($selectBorrowingHistory);
                $this->db->join("gcceforms.borrowing br", "br_body.borrowing_id = br.id AND LCASE(br_body.`type`)='$type'", "inner");
                $this->db->join("gccmaster.tblemployees employees", "employees.id = br.borrower", "INNER");
                $this->db->join("gccasset.assets assets", "assets.id = br_body.asset_id", "INNER");
                $this->db->where_in("br_body.asset_id", $post->ids);
                $this->db->where("br_body.is_returned", 0);
                $this->db->where("br.status !=", "Cancelled");
                $borrowing_history = $this->db->get("gcceforms.borrowing_body br_body")->result();
    
                $this->db->reset_query();

                if (count($borrowing_history) > 0) {
                    foreach ($borrowing_history as $key => $v) {
                        $components = $this->db->select("CONCAT('(',TRIM(assets.assetacode),')', if(`name` IS NULL OR `name`='', assetname, `name`)) asset_name, id as asset_id")
                            ->where('mother_asset', $v->asset_id)
                            ->where_in('id', $post->ids)
                            ->get('gccasset.assets')
                            ->result();
                        $v->components = $components;
                    }
                }

                $data = array(
                    'accountability' => $mother_asset_accountability,
                    'borrowing_history' => $borrowing_history 
                );
            }
            return $data;
        }

        function mass_archive_assets(){
            $post = (object) $this->input->post();
            $ids = explode(',', $post->ids);
            $result = array();

            $this->db->trans_begin();

            $data = array(
                'archive_remark' => $post->mass_archive_remark,
                'status' => $post->status,
                'is_archived' => 1,
                'archived_dt' => date('Y-m-d H:i:s')
            );

            $this->db->where_in('id', $ids);

            if ($post->type == 'component') {
                $this->db->where('gccasset.assets.isComponent', 1);
            }

            $query = $this->db->update('gccasset.assets', $data);

            if ($query) {
                $result['status'] = true;
                $result['msg'] = 'Successfully archived selected assets';
                $msg = 'Succesfully mass archived assets with db id of <b>'.$post->ids.'</b> with status of '.$post->status.' remarks of `'.$post->mass_archive_remark.'`.';

                $this->core_layout->setEventLog($msg,"archive", "success", "gccasset", "user");
            } else {
                $result['status'] = false;
                $result['msg'] = 'Failed to mass archive assets';

                $msg = 'Failed to mass archive assets with db id of <b>'.$post->ids.'</b> with status of '.$post->status.' remarks of `'.$post->mass_archive_remark.'`.';
                $this->core_layout->setEventLog($msg, "archive", "error", "gccasset", "system");
            }

            $this->db->reset_query();

            if ($post->type === "component") {
                $this->db->where_in('asset_id', $ids);
                $this->db->delete("gccasset.asset_components");
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            
            return $result;
        }
    }