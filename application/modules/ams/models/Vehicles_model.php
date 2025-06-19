<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Vehicles_model extends CI_Model {
        protected $vehicleTable = "gccasset.vehicles";
        private $today;

        function __construct() {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->load->model("Utilities_model", "utilities");
            $this->load->model("Assets_model", "asset_m");
            $this->load->model("core/Upload_model", "core_upload");
            $this->load->model("core/Core_model", "core");
            $this->load->library('image_lib');
            $this->today = new DateTime('now', new DateTimezone('Asia/Manila'));
        }

        private function getUserData() {
            return $this->core_layout->getUserLoggedIn();
        }

        function getVehicleCollection($export) {
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
                    if($value == ""){
                        unset($params[$key]);
                    }
                    if ($key === 'description') {
                        unset($params[$key]);
                        $params["`d`.`description`"] = $value;
                    }
                    if ($key === 'created_at') {
                        unset($params[$key]);
                        $params["`a`.`created_at`"] = $value;
                    }
                    if ($key === 'location') {
                        unset($params[$key]);
                        $params["`b`.`location`"] = $value;
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

            $table = "gccasset.vehicles a";

            $sql = "a.id, a.id as asset_id, a.gen_code as assetacode, UPPER(a.name) as name, UPPER(a.description) as description, ";
            $sql .= "UPPER(a.plateno) as plateno, UPPER(a.temp_plateno) as temp_plateno, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.po_no) as po_no, UPPER(a.model) as model, UPPER(a.status2) as status, ";
            $sql .= "IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.created_at as dateCreated, ";
            $sql .= "IFNULL(null,a.primary_pic) as image, a.datepurchased, UPPER(d.description) as asset_category";
            $sql .= ", a.is_borrowed, a.motherId, a.serialno, a.po_no, a.check_no, UPPER(IF(comp.id IS NULL, a.company_code, comp.code)) comp_name, a.purchaseprice, a.assetcode as stock_code";

            $searchFields = "CONCAT(IF(a.gen_code IS NULL, '', a.gen_code), IF(a.`name`IS NULL, '', a.name), IF(a.`created_at`IS NULL, '', a.created_at),
                            IF(a.`plateno`IS NULL, '', a.plateno), IF(a.`serialno`IS NULL, '', a.serialno), IF(a.`po_no`IS NULL, '', a.po_no),
                            IF(a.model IS NULL, '', a.model), IF(d.description IS NULL, '', d.description), IF(b.`location`IS NULL, '', b.location), 
                            IF(a.`check_no`IS NULL, '', a.check_no))";

            if(!empty($advSearch['dateCreated_from']) AND !empty($advSearch['dateCreated_to'])){
                $searchFields_where = "AND a.`created_at` BETWEEN '$dt_From' AND '$dt_To'";
            }else{
                $searchFields_where = "";
            }
                
            $where = "a.isCompo = 0 AND (a.status2 NOT IN('archived', 'lost', 'junk', 'tradein', 'sold', 'destructed') OR `status2` IS NULL) AND a.isJunk != 1 AND a.is_archived != 1"." ".$searchFields_where;

            if (!empty($assetIDs)) {
                $where .= " AND a.id in($assetIDs)";
            }

            $joinArr = array(
                array("table" => "gccasset.location b", "condition" => "b.id = a.area_id", "option" => "LEFT"),
                array("table" => "gccasset.station c", "condition" => "c.id = a.location", "option" => "LEFT"),
                array("table" => "gccasset.assetcategory d", "condition" => "d.code = a.asset_category", "option" => "LEFT"),
                array("table" => "gccasset.asset_sub_cat e", "condition" => "e.sub_cat_id = a.sub_cat_code", "option" => "LEFT"),
                array("table" => "gcchris.tblcompanies comp", "condition" => "comp.id = a.company_code", "option" => "LEFT")
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
                $this->db->order_by("a.updated_at", "desc");
            } else {
                $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            }


            $queryResult = $this->db->get();
            
            // $resultSet["sql"] = $this->db->last_query();

            $data = array();
            if ($queryResult->num_rows() > 0) {
                foreach ($queryResult->result() as $key => $rs) {
                    $id = $rs->asset_id;
                    $imageFile = $rs->image;
                    $imagePath = "uploads/files/images/vehicles/{$id}";
                    $mother_asset_id = $rs->motherId;

                    if (file_exists($imagePath) && $imageFile) {
                        $rs->image = base_url("uploads/files/images/vehicles/{$id}/{$imageFile}");
                        $rs->img_thumbnail = base_url("uploads/files/images/vehicles/{$id}/thumbnails/{$imageFile}");
                        $rs->primary_pic = $imageFile;
                    } else {
                        $rs->primary_pic = null;
                        $rs->image = null;
                        $rs->img_thumbnail = null;
                    }

                    /*$accountedTo = $this->getAcctabilityByAssetCode($rs->assetacode, "Vehicle");
                    $rs->accounted_to = ($accountedTo) ? $accountedTo : "---";
                    $rs->is_accounted = ($accountedTo) ? true : false;

                    $rs->mother_vehicle_is_borrowed = $this->checkIfVehicleMotherIsBorrowed($mother_asset_id);*/
                    $rs->accounted_to = strtoupper($this->asset_m->getAssetAccountabilityByReturnStatus($id, "vehicle"));
                    $rs->check_no = "<div style='text-overflow: ellipsis; overflow: hidden; max-width: 200px; white-space: nowrap;'>".$rs->check_no."</div>";
                    array_push($data, $rs);
                }
            }

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` in Vehicles Masterfile.","search", "success", "gccasset", "user");
            }
            
            if(isset($tableConfig['accountability_search']) && !empty($tableConfig['accountability_search'])){
                $this->core_layout->setEventLog("User searched employee with a emp_id of `".$tableConfig['accountability_search']."` in Vehicles Masterfile.","search", "success", "gccasset", "user");
            }

            if(isset($tableConfig['advanced_search']) && !empty($tableConfig['advanced_search'])){
                // var_dump($tableConfig['advanced_search']);
                $output = '';
                $output .= (isset($params['gen_code']) && $params['gen_code'] != '')? 'Vehicle Code: '.$params['gen_code'].' ':'';
                $output .= (isset($advSearch['name']) && $advSearch['name'] != '')? 'Name: '.$advSearch['name'].', ':'';
                $output .= (isset($advSearch['description']) && $advSearch['description'] != '')? 'Description: '.$advSearch['description'].', ':'';
                $output .= (isset($advSearch['location']) && $advSearch['location'] != '')? 'Area: '.$advSearch['location'].', ':'';
                $output .= (isset($advSearch['asset_category']) && $advSearch['asset_category'] != '')? 'Area: '.$advSearch['asset_category'].', ':'';
                $output .= (isset($params['serialno']) && $params['serialno'] != '')? 'Serial #: '.$params['serialno'].', ':'';
                $output .= (isset($params['po_no']) && $params['po_no'] != '')? 'PO #: '.$params['po_no'].' ':'';

                $this->core_layout->setEventLog("User searched from advanced search with `".$output."` in Vehicles Masterfile.","search", "success", "gccasset", "user");
            }


            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr, false, $advSearch);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr, false, $advSearch);
            $resultSet["data"] = $data;
            $resultSet["acct"] = 0;

            if ((isset($tableConfig['accountability_search']) && !empty($tableConfig['accountability_search'])) && empty($assetIDs)) {
                $resultSet["acct"] = 1;
            }

            // return $this->db->last_query();
            return $resultSet;
        }

        private function get_searched_global($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
            if ($search) {
                $sql = "a.id, a.id as asset_id, UPPER(a.gen_code) as assetacode, UPPER(a.name) as name, UPPER(a.description) as description, ";
                $sql .= "UPPER(a.plateno) as plateno, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, ";
                $sql .= "UPPER(a.serialno) as serialno, UPPER(a.model) as model, UPPER(a.status2) as status, ";
                $sql .= "IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.created_at as dateCreated, ";
                $sql .= "IFNULL(null,a.primary_pic) as image, a.datepurchased, UPPER(c.description) as asset_category";
                $sql .= ", a.is_borrowed";

                $this->db->select($sql);
                $this->db->from("gccasset.vehicles a");
                $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
                $this->db->join("gccasset.assetcategory c", "c.code = a.asset_category", "LEFT");
                $this->db->where("a.isCompo", 0);
                $this->db->where('a.status2 !=', 'archived');
                $this->db->where('a.status2 !=', 'lost');
                $this->db->where('a.status2 !=', 'junk');
                $this->db->where('a.status2 !=', 'tradein');
                $this->db->where('a.isJunk !=', '1');
                $this->db->where($search);
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
                        $imagePath = realpath("server/php/images/files/thumbnail/{$imageFile}");
                        if (file_exists($imagePath) && $imageFile) {
                            $rs->image = base_url("uploads/files/images/{$id}/{$imageFile}");
                            $rs->img_thumbnail = base_url("uploads/files/images/{$id}/thumbnail/{$imageFile}");
                        } else {
                            $rs->image = base_url("uploads/module/ams/images/no_image.jpg");
                            $rs->img_thumbnail = base_url("uploads/module/ams/images/no_image.jpg");
                        }

                        $accountedTo = $this->getAcctabilityByAssetCode($rs->assetacode, "Vehicle");
                        $rs->accounted_to = ($accountedTo) ? $accountedTo : "---";
                        $rs->is_accounted = ($accountedTo) ? true : false;
                        $arrData[$key] = $rs;
                    }

                    $data = array();
                    foreach ($arrData as $k => $v) {
                        $data[] = $v;
                    }
                    $this->core_layout->setEventLog("User Searched `".$search."` in masterfile datatable.","search", "success", "gccasset", "user");
                    return $data;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        }

        private function get_searched_item($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
            if ($search) {
                $filterFields = array("a.gen_code", "a.name", "a.description", "a.company_code", "a.plateno",
                    "a.brand", "a.serialno", "a.model", "a.status2", "b.location", "c.description");

                $sql = "a.id, a.id as asset_id, UPPER(a.gen_code) as assetacode, UPPER(a.name) as name, UPPER(a.description) as description, ";
                $sql .= "UPPER(a.plateno) as plateno, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, ";
                $sql .= "UPPER(a.serialno) as serialno, UPPER(a.model) as model, UPPER(a.status2) as status, ";
                $sql .= "IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.created_at as dateCreated, ";
                $sql .= "IFNULL(null,a.primary_pic) as image, a.datepurchased, UPPER(c.description) as asset_category";
                $sql .= ", a.is_borrowed";

                $this->db->select($sql);
                $this->db->from("gccasset.vehicles a");
                $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
                $this->db->join("gccasset.assetcategory c", "c.code = a.asset_category", "LEFT");
                $this->db->where("a.isCompo", 0);
                $this->db->where('a.status2 !=', 'archived');
                $this->db->where('a.status2 !=', 'lost');
                $this->db->where('a.status2 !=', 'junk');
                $this->db->where('a.status2 !=', 'tradein');
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
                        $id = $rs->asset_id;
                        $imageFile = $rs->image;
                        $imagePath = realpath("server/php/images/files/thumbnail/{$imageFile}");
                        if (file_exists($imagePath) && $imageFile) {
                            $rs->image = base_url("uploads/files/images/{$id}/{$imageFile}");
                            $rs->img_thumbnail = base_url("uploads/files/images/{$id}/thumbnail/{$imageFile}");
                        } else {
                            $rs->image = base_url("uploads/module/ams/images/no_image.jpg");
                            $rs->img_thumbnail = base_url("uploads/module/ams/images/no_image.jpg");
                        }

                        $accountedTo = $this->getAcctabilityByAssetCode($rs->assetacode, "Vehicle");
                        $rs->accounted_to = ($accountedTo) ? $accountedTo : "---";
                        $rs->is_accounted = ($accountedTo) ? true : false;
                        $arrData[$key] = $rs;
                    }

                    $data = array();
                    foreach ($arrData as $k => $v) {
                        $data[] = $v;
                    }
                    $this->core_layout->setEventLog("User searched `".$search."` in masterfile datatable.","search", "success", "gccasset", "user");
                    return $data;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        }

        private function get_searched_global_count($search = null) {
            $rowCount = 0;
            if ($search) {
                $this->db->from("gccasset.vehicles a");
                $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
                $this->db->join("gccasset.assetcategory c", "c.code = a.asset_category", "LEFT");
                $this->db->where("a.isCompo", 0);
                $this->db->where('a.status2 !=', 'archived');
                $this->db->where('a.status2 !=', 'lost');
                $this->db->where('a.status2 !=', 'junk');
                $this->db->where('a.status2 !=', 'tradein');
                $this->db->where('a.isJunk !=', '1');
                $this->db->where($search);
                $query = $this->db->get();

                $rowCount = $query->num_rows();
            }

            return $rowCount;
        }

        private function get_searched_item_count($search = null) {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.gen_code", "a.name", "a.description", "a.company_code", "a.plateno",
                    "a.brand", "a.serialno", "a.model", "a.status2", "b.location", "c.description");

                $this->db->from("gccasset.vehicles a");
                $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
                $this->db->join("gccasset.assetcategory c", "c.code = a.asset_category", "LEFT");
                $this->db->where("a.isCompo", 0);
                $this->db->where('a.status2 !=', 'archived');
                $this->db->where('a.status2 !=', 'lost');
                $this->db->where('a.status2 !=', 'junk');
                $this->db->where('a.status2 !=', 'tradein');
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

        private function get_all_post($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
            $sql = "a.id, a.id as asset_id, UPPER(a.gen_code) as assetacode, UPPER(a.name) as name, UPPER(a.description) as description, ";
            $sql .= "UPPER(a.plateno) as plateno, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.model) as model, UPPER(a.status2) as status, ";
            $sql .= "IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.created_at as dateCreated, ";
            $sql .= "IFNULL(null,a.primary_pic) as image, a.datepurchased, UPPER(c.description) as asset_category";
            $sql .= ", a.is_borrowed";

            $this->db->select($sql);
            $this->db->from("gccasset.vehicles a");
            $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
            $this->db->join("gccasset.assetcategory c", "c.code = a.asset_category", "LEFT");
            $this->db->where("a.isCompo", 0);
            $this->db->where('a.status2 !=', 'archived');
            $this->db->where('a.status2 !=', 'lost');
            $this->db->where('a.status2 !=', 'junk');
            $this->db->where('a.status2 !=', 'tradein');
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
                    $id = $rs->asset_id;
                    $imageFile = $rs->image;
                    $imagePath = realpath("server/php/images/files/thumbnail/{$imageFile}");
                    if (file_exists($imagePath) && $imageFile) {
                        $rs->image = base_url("uploads/files/images/{$id}/{$imageFile}");
                        $rs->img_thumbnail = base_url("uploads/files/images/{$id}/thumbnail/{$imageFile}");
                    } else {
                        $rs->image = base_url("uploads/module/ams/images/no_image.jpg");
                        $rs->img_thumbnail = base_url("uploads/module/ams/images/no_image.jpg");
                    }

                    $accountedTo = $this->getAcctabilityByAssetCode($rs->assetacode, "Vehicle");
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
        }

        private function get_all_post_count() {
            $this->db->from("gccasset.vehicles a");
            $this->db->join("gccasset.location b", "b.id = a.area_id", "LEFT");
            $this->db->join("gccasset.assetcategory c", "c.code = a.asset_category", "LEFT");
            $this->db->where("a.isCompo", 0);
            $this->db->where('a.status2 !=', 'archived');
            $this->db->where('a.status2 !=', 'lost');
            $this->db->where('a.status2 !=', 'junk');
            $this->db->where('a.status2 !=', 'tradein');
            $this->db->where('a.isJunk !=', '1');
            $query = $this->db->get();

            return $query->num_rows();
        }

        private function getAcctabilityByAssetCode($asset_code = NULL, $type = "Asset") {
            $arrNames = null;
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
                    for ($i = 0; $i < sizeof($ids); $i++) {
                        if ($check[$i] == "0") {
                            $emp = $this->db->get_where("gccmaster.tblemployees", array("id" => $ids[$i]));
                            if ($emp->num_rows() == 1) {
                                $row = $emp->row();
                                $name = "{$row->firstname} {$row->middlename} {$row->lastname}";
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

        function getEquipmentCategoryCollection() {
            $get = $this->input->get();

            $q = isset($get['q']) ? $get['q'] : '';
            $this->db->LIKE("CONCAT(code, description)", $q, "both");
            $data = $this->db->get("gccasset.equipmentcategory")->result();
            return array("results" => $data);
        }

        function processNew() {
            $post = $this->input->post();

            return $post;
        }

        function getTypeLookup() {
            $resultarray = array();
            $post = $this->input->post();
            $query = $this->db->query("SELECT `code`,`description` FROM gccasset.equipmentcategory WHERE `asset_cat_id` = '{$post['cat_id']}'");

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

        function getArchiveCollection($type) {
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
            $table = "gccasset.vehicles a";

            $sql = "a.id, a.id as asset_id, UPPER(a.gen_code) as assetacode, UPPER(a.name) as name, UPPER(a.description) as description, ";
            $sql .= "UPPER(a.plateno) as plateno, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.model) as model, UPPER(a.status2) as status, ";
            $sql .= "IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.created_at as dateCreated, ";
            $sql .= "IFNULL(null,a.primary_pic) as image, a.datepurchased, UPPER(c.description) as asset_category";
            $sql .= ", IF(a.isCompo = 1, 'Component', 'Vehicle') type, a.isCompo, a.archive_remark";

            $searchFields = "CONCAT(IF(a.gen_code IS NULL, '', a.gen_code), IF(a.`name`IS NULL, '', a.name), 
                            IF(a.`plateno`IS NULL, '', a.plateno), IF(a.`serialno`IS NULL, '', a.serialno), 
                            IF(a.model IS NULL, '', a.model), IF(a.description IS NULL, '', a.description),
                            IF(a.archive_remark IS NULL, '', a.archive_remark), IFNULL(a.status2, ''))";


            if ($type !== "all") {
                $where = "a.isCompo=" . ($type === "mother" ? 0 : 1) . " AND a.`status2` NOT IN('operational', 'brandnew') AND a.`status2` IS NOT NULL AND a.`status2` != ''";
            } else {
                $filter = $this->input->post('filter');
                if (isset($filter) && $filter !== "all") {
                    $where = "a.isCompo=" . ($filter === "mother" ? 0 : 1) . " AND a.`status2` NOT IN('operational', 'brandnew') AND a.`status2` IS NOT NULL AND a.`status2` != ''";
                } else {
                    $where = "a.`status2` NOT IN('operational', 'brandnew') AND a.`status2` IS NOT NULL AND a.`status2` != ''";
                }
            }

            $joinArr = array(
                array(
                    "table" => "gccasset.location b",
                    "condition" => "b.id = a.area_id",
                    "option" => "LEFT"
                ),
                array(
                    "table" => "gccasset.assetcategory c",
                    "condition" => "c.code = a.asset_category",
                    "option" => "LEFT"
                ),
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

            if($queryResult){
                if(!empty($pageOptions->search)){
                    $this->core_layout->setEventLog("User searched `".$pageOptions->search."` filtered by `".$type."` in vehicle datatable.","search", "success", "gccasset", "user");
                }
            }else{
                $this->core_layout->setEventLog("User failed to searched `".$pageOptions->search."` filtered by `".$type."` in vehicle datatable.","search", "error", "gccasset", "system");
            }

            $resultSet["data"] = $queryResult;
            $like = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => 'both');
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $like, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $like, $joinArr);
            return $resultSet;
        }

        private function checkIfVehicleMotherIsBorrowed($mother_asset_id) {
            $this->db->where("id", $mother_asset_id);
            $this->db->where("is_borrowed", 1);
            $result = $this->db->count_all_results("gccasset.vehicles");
            return $result > 0 ? 1 : 0;
        }

        function archiveVehicle($isComponent) {
            $this->db->trans_begin();
            $id = $this->input->get("id");
            $archive_remark = $this->input->post("archive_remark");
            $data = array();

            $result = $this->db->update("gccasset.vehicles",
                array(
                    "status2" => $this->input->post("status"),
                    "archive_remark" => $archive_remark,
                    "is_archived" => 1,
                ),
                array("id" => $id));

            if ($result) {
                // archive log, insert to gccmaster.archived_items
                $this->core->insertArchiveLog("gccasset.vehicles", $id);

                $this->core_layout->setEventLog("User archived with db id of `".$id."` in Vehicles Masterfile.","archive", "success", "gccasset", "user");

                $data["success"] = true;
                $data["message"] = $isComponent == 0 ? "Vehicle was moved to archive." : "Vehicle component was moved to archive.";
            } else {
                $this->core_layout->setEventLog("User failed to archiv with db id of `".$id."` in Vehicles Masterfile.","archive", "error", "gccasset", "user");
                $data["success"] = false;
                $data["message"] = $this->db->error();
            }

            $this->db->reset_query();

            if ($isComponent == 1) {
                $this->db->delete("gccasset.vehicles_components", array("asset_id" => $id));
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }

            return $data;
        }

        function restoreArchivedVehicle($id) {
            $this->db->where("id", $id);
            if ($this->db->update("gccasset.vehicles", array("status2" => $this->input->post("status"), "archive_remark" => "", "is_archived" => 0))) {
                $this->core->insertArchiveLog("gccasset.vehicles", $id, 2);
                $this->core_layout->setEventLog("User restored db id `".$id."` in vehicle masterfile datatable.","restore", "success", "gccasset", "user");
                $data["success"] = true;
                $data["message"] = "Record was restored to master file.";
            } else {
                $this->core_layout->setEventLog("User failed to restore db id `".$id."` in vehicle masterfile datatable.","restore", "error", "gccasset", "system");
                $data["success"] = false;
                $data["message"] = $this->db->error();
            }

            return $data;
        }

        function restoreArchivedVehiclesMultiple() {
            $multiple_id = $this->input->post('multiple_id');
            $multiple_id_arr = explode(",", $multiple_id);
            $resultSet = array();
            $this->db->where_in("id", $multiple_id_arr);

            $output = '';
            foreach($multiple_id_arr as $id){
                $output .= $id.', ';
            }
            if ($this->db->update("gccasset.vehicles", array("status2" => "", "archive_remark" => "", "is_archived" => 0))) {
                
                $this->core_layout->setEventLog("User restored db id `".$output."` in masterfile datatable.","restore", "success", "gccasset", "user");
                $resultSet["success"] = true;
                $resultSet["message"] = "Vehicles was restored back to master file.";
            } else {
                $this->core_layout->setEventLog("User failed to restore db id `".$output."` in masterfile datatable.","restore", "error", "gccasset", "system");
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
            }

            return $resultSet;
        }

        function deleteVehicle($id) {
            if ($this->db->delete("gccasset.vehicles", array("id" => $id))) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Vehicle record was permanently deleted.";
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
            }

            return $resultSet;
        }

        function deleteArchivedVehiclesMultiple() {
            $multiple_id = $this->input->post('multiple_id');
            $multiple_id_arr = explode(",", $multiple_id);
            $resultSet = array();
            $this->db->where_in("id", $multiple_id_arr);
            if ($this->db->delete("gccasset.vehicles")) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Vehicles was permanently deleted.";
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
            }

            return $resultSet;
        }

        function getVehicleComponentsCollection($excludeHasMother, $export) {
            $tableConfig = $this->input->post();
            $advSearch = array();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
            $table = "gccasset.vehicles a";

            if (isset($tableConfig['advanced_search']) && !empty($tableConfig['advanced_search'])) {
                $params = $tableConfig['advanced_search'];
                foreach ($params as $key => $value) {
                    if($value == ""){
                        unset($params[$key]);
                    }
                    if ($key === 'description') {
                        unset($params[$key]);
                        $params["`a`.`description`"] = $value;
                    }
                    if ($key === 'created_at') {
                        unset($params[$key]);
                        $params["`a`.`created_at`"] = $value;
                    }
                    if ($key === 'location') {
                        unset($params[$key]);
                        $params["`b`.`location`"] = $value;
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

            $sql = "a.id, a.id as asset_id, UPPER(a.gen_code) as assetacode, UPPER(a.name) as name, UPPER(a.description) as description, ";
            $sql .= "UPPER(a.plateno) as plateno, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.po_no) as po_no, a.check_no, UPPER(a.model) as model, UPPER(a.status2) as status, ";
            $sql .= "IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.created_at as dateCreated, ";
            $sql .= "IFNULL(null,a.primary_pic) as image, a.datepurchased, UPPER(c.description) as asset_category";
            $sql .= ", a.is_borrowed as is_borrowed, a.motherID as mother_asset_id, a.serialno, UPPER(IF(comp.id IS NULL, a.company_code, comp.code)) comp_name, a.check_no, a.purchaseprice, a.assetcode as stock_code";

            $searchFields = "CONCAT(IF(a.gen_code IS NULL, '', a.gen_code), IF(a.`name`IS NULL, '', a.name), IF(a.`po_no`IS NULL, '', a.po_no), 
                            IF(a.`plateno`IS NULL, '', a.plateno), IF(a.`serialno`IS NULL, '', a.serialno), IF(a.`created_at`IS NULL, '', a.created_at), 
                            IF(a.model IS NULL, '', a.model), IF(a.description IS NULL, '', a.description), IF(b.`location`IS NULL, '', b.location),
                            IF(a.`check_no`IS NULL, '', a.check_no))";

            if(!empty($advSearch['dateCreated_from']) AND !empty($advSearch['dateCreated_to'])){
                $searchFields_where = "AND a.`created_at` BETWEEN '$dt_From' AND '$dt_To'";
            }else{
                $searchFields_where = "";
            }
                
            $where = "a.isCompo = 1 AND (a.status2 NOT IN('archived', 'lost', 'junk', 'tradein', 'sold', 'destructed') OR `status2` IS NULL) AND a.isJunk != 1 AND a.is_archived != 1 AND a.is_archived != 1"." ".$searchFields_where;
            if ($excludeHasMother == 1) {
                $where .= " AND a.motherID = 0";
            }

            $joinArr = array(
                array(
                    "table" => "gccasset.location b",
                    "condition" => "b.id = a.area_id",
                    "option" => "LEFT"
                ),
                array(
                    "table" => "gccasset.assetcategory c",
                    "condition" => "c.code = a.asset_category",
                    "option" => "LEFT"
                ),
                array(
                    "table" => "gcchris.tblcompanies comp",
                    "condition" => "comp.id = a.company_code",
                    "option" => "LEFT"
                ),
            );

            $this->db->select($sql);
            $this->db->from($table);
            if ($joinArr) {
                foreach ($joinArr as $join) {
                    $this->db->join($join["table"], $join["condition"], $join["option"]);
                }
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
                $this->db->order_by("a.updated_at", "desc");
            } else {
                $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            }

            $queryResult = $this->db->get()->result();

            $data = array();

            if (count($queryResult) > 0) {
                foreach ($queryResult as $key => $rs) {
                    $id = $rs->asset_id;
                    $imageFile = $rs->image;
                    $imagePath = "uploads/files/images/vehicles/{$id}";
                    $mother_asset_id = $rs->mother_asset_id;

                    if (file_exists($imagePath) && $imageFile) {
                        $rs->image = base_url("uploads/files/images/vehicles/{$id}/{$imageFile}");
                        $rs->img_thumbnail = base_url("uploads/files/images/vehicles/{$id}/thumbnails/{$imageFile}");
                        $rs->primary_pic = $imageFile;
                    } else {
                        $rs->primary_pic = null;
                        $rs->image = null;
                        $rs->img_thumbnail = null;
                    }
                    $accountedTo = $this->asset_m->getAcctabilityByAssetCode($rs->assetacode, "Vehicle");
                    $borrowed = $this->asset_m->getBorrowingByAssetCode($rs->assetacode, "Vehicle");
                    if($accountedTo){
                        $rs->accounted_to = $accountedTo."<br><small class='text-muted'><i>-Accounted</i></small>";
                    }else if($borrowed){
                        $rs->accounted_to = $borrowed."<br><small class='text-muted'><i>-Borrowed</i></small>";
                    }else{
                        $rs->accounted_to = "---";
                    }
                    // $rs->mother_vehicle_is_borrowed = $this->checkIfVehicleMotherIsBorrowed($mother_asset_id);
                    $rs->check_no = "<div style='text-overflow: ellipsis; overflow: hidden; max-width: 200px; white-space: nowrap;'>".$rs->check_no."</div>";
                    array_push($data, $rs);
                }
            }

            $resultSet["data"] = $data;

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` in Vehicles Components Masterfile.","search", "success", "gccasset", "user");
            }
            
            if(isset($tableConfig['accountability_search']) && !empty($tableConfig['accountability_search'])){
                $this->core_layout->setEventLog("User searched employee with a emp_id of `".$tableConfig['accountability_search']."` in Vehicles Components Masterfile.","search", "success", "gccasset", "user");
            }

            if(isset($tableConfig['advanced_search']) && !empty($tableConfig['advanced_search'])){
                // var_dump($tableConfig['advanced_search']);
                $output = '';
                $output .= (isset($params['gen_code']) && $params['gen_code'] != '')? 'Vehicle Code: '.$params['gen_code'].' ':'';
                $output .= (isset($advSearch['name']) && $advSearch['name'] != '')? 'Name: '.$advSearch['name'].', ':'';
                $output .= (isset($advSearch['description']) && $advSearch['description'] != '')? 'Description: '.$advSearch['description'].', ':'';
                $output .= (isset($advSearch['location']) && $advSearch['location'] != '')? 'Area: '.$advSearch['location'].', ':'';
                $output .= (isset($advSearch['asset_category']) && $advSearch['asset_category'] != '')? 'Area: '.$advSearch['asset_category'].', ':'';
                $output .= (isset($params['serialno']) && $params['serialno'] != '')? 'Serial #: '.$params['serialno'].', ':'';
                $output .= (isset($params['po_no']) && $params['po_no'] != '')? 'PO #: '.$params['po_no'].' ':'';

                $this->core_layout->setEventLog("User searched from advanced search with `".$output."` in Vehicles Components Masterfile.","search", "success", "gccasset", "user");
            }

            $like = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => 'both');
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $like, $joinArr, false, $advSearch);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $like, $joinArr, false, $advSearch);
            
            // return $this->db->last_query();
            return $resultSet;
        }

        function getSubCategoryCollection($cat_id) {
            $this->db->where("cat_id", $cat_id);
            return $this->db->get("gccasset.asset_sub_cat")->result();
        }

        function getLocationCollection() {
            $q = isset($_GET["q"]) ? $_GET["q"] : "";
            $this->db->like("location", $q, "both");
            $result = $this->db->get("gccasset.location")->result();
            return array("results" => $result);
        }

        function getCompanyCollection() {
            $q = isset($_GET["q"]) ? $_GET["q"] : "";
            $this->db->like("CONCAT(description, code)", $q, "both");
            $result = $this->db->get("gcchris.tblcompanies")->result();
            return array("results" => $result);
        }

        function getStatusCollection() {
            $q = isset($_GET["q"]) ? $_GET["q"] : "";
            $this->db->like("name", $q, "both");
            $this->db->group_by("name");
            $result = $this->db->get("gccasset.status")->result();
            return array("results" => $result);
        }

        function saveNewVehicle($isCompo, $motherId) {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $post->location = $this->db->where("id", $post->area_id)->get("gccasset.location")->row("location");
            $post->datepurchased = ($post->datepurchased === "" || $post->datepurchased === null) ? "" : date_format(date_create($post->datepurchased), 'Y-m-d');
            $post->date_received = ($post->date_received === "" || $post->date_received === null) ? "" : date_format(date_create($post->date_received), 'Y-m-d');
            $post->isOper = isset($post->isOper) ? 1 : 0;
            $post->isCompo = $isCompo;
            // $post->primary_pic = isset($post->primary_pic) ? preg_replace('/\s+/', '_', $post->primary_pic) : null;
            $post->purchaseprice = str_replace(',', '', $post->purchaseprice);
            $post->total_cost = str_replace(',', '', $post->total_cost);
            $primary_pic = isset($post->primary_pic) ? $post->primary_pic : "";
            unset($post->primary_pic);
            unset($post->mode);
            unset($post->vehicle_id);
            if ($motherId) {
                $mother = $this->db->where("id", $motherId)->get("gccasset.vehicles")->row();

                $components_gen_code = $this->db->select("gen_code")
                    ->where("isCompo", 1)
                    ->group_start()
                    ->like("gen_code", $mother->gen_code)
                    ->or_like("assetcode", $mother->gen_code)
                    ->group_end()
                    ->order_by("assetcode", "desc")
                    ->get("gccasset.vehicles")
                    ->result();

                $gen_code = $mother->gen_code;

                $components_gen_code_end_digit = array_map(function ($row) {
                    $strArr = explode("-", $row->gen_code);
                    return intval(array_pop($strArr));
                }, $components_gen_code);

                $end = empty($components_gen_code_end_digit) ? 1 : (intval(max($components_gen_code_end_digit)) + 1);

                $post->motherID = $motherId;
                $post->series = 0;
                $post->gen_code = $gen_code . "-" . $end;
            } else {
                $generated_code = $this->generateAssetCode($post->asset_category, $post->sub_cat_code);
                $code = $generated_code->code;
                $series = $generated_code->series;

                $post->series = $series;
                $post->gen_code = $code;
            }

            $exist = $this->checkIfGenCodeExist($post->gen_code);
            if (intval($exist)) {
                $resultSet["success"] = false;
                $resultSet["status"] = "duplicated";
                $resultSet["message"] = "Unable to save. <span class='m--font-bolder'> Asset Code: " . $post->gen_code . "</span> duplicated.";
                return $resultSet;
            }

            $post->created_at = $this->today->format("Y-m-d H:i:s");
            $post->updated_at = $this->today->format("Y-m-d H:i:s");
            $post->created_by = strtoupper($this->getUserData()["employee_id"]);

            $resultSet = array();
            if ($this->db->insert("gccasset.vehicles", $post)) {
                $id = $this->db->insert_id();
                $uploadResult = array();

                if (isset($_FILES['files'])) {
                    $uploadResult = $this->uploadFiles($id, $primary_pic);
                }

                if ($motherId) {
                    $fields = array(
                        "asset_id" => $id,
                        "parent_id" => $motherId,
                        "description" => $post->description,
                        "date_added" => date("Y-m-d h:i:s")
                    );

                    $q = $this->db->insert("gccasset.vehicles_components", $fields);
                    $components = $this->db->insert_id();

                    if($q){
                        $this->core_layout->setEventLog("User added new vehicle components with the the db id of `".$components."` in vehicle component masterfile datatable.","insert", "success", "gccasset", "user");
                    }else{
                        $this->core_layout->setEventLog("User failed to add new vehicle components with the the db id of `".$components."` in vehicle component masterfile datatable.","insert", "error", "gccasset", "system");
                    }
                }

                $this->core_layout->setEventLog("User added new vehicle with the the db id of `".$id."` in vehicle masterfile datatable.","insert", "success", "gccasset", "user");

                $resultSet["success"] = true;
                $resultSet["message"] = "New " . ((int)$isCompo === 0 ? "vehicle" : "vehicle component") . " information was saved.";
                $resultSet["fail_uploads"] = !empty($uploadResult) ? $uploadResult["upload_errors"] : array();
                $resultSet["primary_pic"] = !empty($uploadResult) ? $uploadResult["success_primary_pic"] : null;
                $resultSet["uploading_primary_pic_failed"] = !empty($uploadResult) ? $uploadResult["uploading_primary_pic_failed"] : null;
                $resultSet["id"] = $id;
            } else {
                // $this->core_layout->setEventLog("User failed to add new vehicle with the the db id of `".$id."` in fixed asset masterfile datatable.","insert", "error", "gccasset", "system");
                $this->core_layout->setEventLog("User failed to add new vehicle in fixed asset masterfile datatable.","insert", "error", "gccasset", "system");
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
            }

            return $resultSet;
        }

        private function checkIfGenCodeExist($gen_code) {
            $this->db->where("gen_code", $gen_code);
            $this->db->or_where("assetcode", $gen_code);
            return $this->db->count_all_results("gccasset.vehicles");
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

                    $folder = 'uploads/files/images/vehicles/' . $id;

                    if (!file_exists($folder)) {
                        mkdir($folder, 0775, true);
                    }

                    $config['upload_path'] = $folder;
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = '10000';
                    $config['file_name'] = $_FILES['file']['name'];

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('file')) {
                        $upload_data = $this->upload->data();
                        $filename = $upload_data['file_name'];
                        array_push($uploaded, $original_name);

                        if ($primary_pic == $original_name) {
                            $this->db->where("id", $id)->update("gccasset.vehicles", array("primary_pic" => $filename));
                            $success_primary_pic = $filename;
                        }

                        $timestamp = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $fields = array("asset_id" => $id, "image" => $filename, "useruploaded" => $user_id, "dateupload" => $timestamp->format('Y-m-d H:i:s'));
                        $this->db->insert("gccasset.vehicles_image", $fields);

                        if ($this->core_upload->createThumbnailPathFolder($folder)) {
                            $this->resizeImage($filename, $folder, '/thumbnails', 75, 75);
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
                    $this->db->where("id", $id)->update("gccasset.vehicles", array("primary_pic" => $new_primary));
                    $success_primary_pic = $uploaded[0];
                } else {
                    $success_primary_pic = $this->db->select("image")
                        ->get_where("gccasset.vehicles_image", array("asset_id" => $id))
                        ->row("image");
                    $this->db->where("id", $id)->update("gccasset.vehicles", array("primary_pic" => $success_primary_pic));
                }
            }

            return array(
                "upload_errors" => $upload_errors,
                "success_primary_pic" => $success_primary_pic,
                "uploading_primary_pic_failed" => $uploading_primary_pic_failed,
                "uploaded" => $this->db->where("asset_id", $id)->get("gccasset.vehicles_image")->result());
        }

        private function resizeImage($filename, $source_path, $target_path, $width, $height = 0) {
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

        private function generateAssetCode($asset_category, $sub_cat_id) {
            $result = array();
            $series = $this->db->select_max("series", "series_max")
                ->where("asset_category", $asset_category)
                ->where("sub_cat_code", $sub_cat_id)
                ->get("gccasset.vehicles")
                ->row("series_max");

            $next = $series + 1;
            $sub_cat_code = $this->db->get_where("gccasset.asset_sub_cat", array("sub_cat_id" => $sub_cat_id))->row("sub_cat_code");
            $code = $asset_category . "-" . $sub_cat_code . "-" . str_pad($next, 4, "0", STR_PAD_LEFT);

            $result["code"] = $code;
            $result["series"] = $next;

            return json_decode(json_encode($result, true));
        }

        public function getVehicleDetails($id) {
            $this->db->select("vh.*, comp.description company_name, asset_cat.description asset_cat_description, equip_cat.description equip_cat_description, 
                sub_cat.sub_cat_desc as subcat_description, stat.name status_name, location.location location_description, stat.code status_code,
                CONCAT(emp1.firstname, ' ', emp1.lastname) created_by, 
                CONCAT(emp2.firstname, ' ', emp2.lastname) updated_by,
                IF(emp3.id IS NULL, vh.inventory_check_by, CONCAT(emp3.firstname, ' ', emp3.lastname)) checked_by");
            $this->db->join("gcchris.tblcompanies comp", "comp.code = vh.company_code", "left");
            $this->db->join("gccasset.assetcategory asset_cat", "asset_cat.code = vh.asset_category", "left");
            $this->db->join("gccasset.equipmentcategory equip_cat", "equip_cat.code = vh.category", "left");
            $this->db->join("gccasset.status stat", "`stat`.`code` = vh.status2", "left");
            $this->db->join("gccasset.location location", "location.id = vh.area_id", "left");
            $this->db->join("gccmaster.tblemployees emp1", "emp1.id = vh.created_by", "left");
            $this->db->join("gccmaster.tblemployees emp2", "emp2.id = vh.updated_by", "left");
            $this->db->join("gccmaster.tblemployees emp3", "emp3.id = vh.inventory_check_by", "left");
            $this->db->join("gccasset.asset_sub_cat sub_cat", "sub_cat.cat_id = vh.asset_category AND sub_cat.sub_cat_code = vh.sub_cat_code", "left");
            $vehicle = $this->db->get_where("gccasset.vehicles vh", array("vh.id" => $id))->row();

            if (isset($vehicle->motherID)) {
                $mother_asset = $this->getMotherAsset($vehicle->motherID);

                $vehicle->mother_code = isset($mother_asset['gen_code']) ? $mother_asset['gen_code'] : "";
                $vehicle->mother_name = isset($mother_asset['name']) ? $mother_asset['name'] : "";
            } else {
                $vehicle->mother_code = NULL;
                $vehicle->mother_name = NULL;
            }

            return $vehicle;
        }

        function getMotherAsset($id) {
            $this->db->select("a.gen_code, a.name");
            $this->db->from("gccasset.vehicles a");
            $this->db->where("a.id", $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        public function getVehicleImages($id) {
            $this->db->select("id, image name, null as base64", FALSE);
            return $this->db->get_where("gccasset.vehicles_image", array("asset_id" => $id))->result();
        }

        public function updateVehicle($isCompo) {
            // $this->db->trans_begin();
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $post->location = $this->db->where("id", $post->area_id)->get("gccasset.location")->row("location");
            if(isset($post->datepurchased)){
                $post->datepurchased = ($post->datepurchased === "" || $post->datepurchased === null || $post->datepurchased == '0000-00-00') ? "" : date_format(date_create($post->datepurchased), 'Y-m-d');
            }else{
                unset($post->datepurchased);
            }
            $post->date_received = ($post->date_received === "" || $post->date_received === null || $post->date_received == '0000-00-00') ? "" : date_format(date_create($post->date_received), 'Y-m-d');
            $post->isOper = isset($post->isOper) ? 1 : 0;
            $post->purchaseprice = str_replace(',', '', $post->purchaseprice);
            $post->total_cost = str_replace(',', '', $post->total_cost);

            $id = $post->id;
            $primary_pic = null;
            $primary_pic_from_uploaded = (isset($post->primary_pic_from_uploaded) && $post->primary_pic_from_uploaded == "true") ? TRUE : FALSE;

            $uploadedImagesToRemove = json_decode($post->uploadedImagesToRemove);
            
            unset($post->id);
            unset($post->primary_pic_from_uploaded);
            unset($post->uploadedImagesToRemove);

            if (isset($post->primary_pic)) {
                if ($primary_pic_from_uploaded) {
                    $post->primary_pic = preg_replace('/\s+/', '_', $post->primary_pic);
                } else {
                    $primary_pic = $post->primary_pic;
                    unset($post->primary_pic);
                }
            }

            // remove images on submit
            if (!empty($uploadedImagesToRemove)) {
                $images = $this->removeUploadedImages($uploadedImagesToRemove);
            }

            $post->updated_at = $this->today->format("Y-m-d H:i:s");
            $post->updated_by = strtoupper($this->getUserData()["employee_id"]);

            $resultSet = array();
            $this->db->where("id", $id);
            if($post->status2 == 'junk' OR $post->status2 == 'lost' OR $post->status2 == 'sold' OR $post->status2 == 'destructed'){
                

                $post->is_archived = 1;
                $archive_vehicle = $this->db->update("gccasset.vehicles", $post);
                    // if($archive_vehicle){
                    //     $this->core->insertArchiveLog("gccasset.vehicles", $id);
                    // }
                $resultSet["success"] = true;
                $resultSet["message"] = ((int)$isCompo === 0 ? "Vehicle" : "Vehicle component") . " was archived.";
                $resultSet["fail_uploads"] = "";
                $resultSet["id"] = $id;
                $resultSet["status"] = 'archived';
            }else{
                if ($this->db->update("gccasset.vehicles", $post)) {
                    $uploadResult = array();

                    if (isset($_FILES['files'])) {
                        $uploadResult = $this->uploadFiles($id, $primary_pic);
                    }

                    $isCom = ($isCompo == 0) ? "Vehicle Masterfile" : "Vehicle component Masterfile";
                    $this->core_layout->setEventLog("User updated the ".$isCom." with the the db id of `".$id."` in ".$isCom.".","update", "success", "gccasset", "user");

                    $resultSet["success"] = true;
                    $resultSet["message"] = ((int)$isCompo === 0 ? "Vehicle" : "Vehicle component") . " information was updated.";
                    $resultSet["fail_uploads"] = !empty($uploadResult) ? $uploadResult["upload_errors"] : array();
                    $resultSet["primary_pic"] = !empty($uploadResult) ? $uploadResult["success_primary_pic"] : null;
                    $resultSet["uploading_primary_pic_failed"] = !empty($uploadResult) ? $uploadResult["uploading_primary_pic_failed"] : null;
                    $resultSet["uploaded"] = !empty($uploadResult) ? $uploadResult["uploaded"] : array();
                    $resultSet["id"] = $id;
                    $resultSet["status"] = $post->status2;
                } else {
                    $isCom = ($isCompo == 0) ? "Vehicle" : "Vehicle component";
                    $this->core_layout->setEventLog("User failed to updat the ".$isCom." with the the db id of `".$id."` in ".$isCom.".","update", "error", "gccasset", "system");
                    $resultSet["success"] = false;
                    $resultSet["message"] = $this->db->error();
                }
            }

            return $resultSet;
        }

        function removeUploadedImages($id) {
            $this->db->where_in("id", $id);
            $images = $this->db->get("gccasset.vehicles_image")->result();

            foreach ($images as $image) {
                $file = "uploads/files/images/vehicles/" . $image->asset_id . "/" . $image->image;
                $thumb = "uploads/files/images/vehicles/" . $image->asset_id . "/thumbnails/" . $image->image;
                if (file_exists($file)) {
                    unlink($file);
                }
                if (file_exists($thumb)) {
                    unlink($thumb);
                }

                $this->db->where("id", $image->id)->delete("gccasset.vehicles_image");
            }
        }

        function getVehicleComponents($id) {
            $resultSet = array();
            $table = "gccasset.vehicles_components components";
            $searchFields = "CONCAT(vehicles.gen_code, components.description)";

            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $joinArr = array(
                array(
                    "table" => "gccasset.vehicles vehicles",
                    "condition" => "vehicles.id = components.asset_id",
                    "option" => "INNER"
                ),
            );

            $this->db->select("components.*, vehicles.gen_code asset_code, components.isExcluded status, ");
            if ($joinArr) {
                foreach ($joinArr as $join) {
                    $this->db->join($join["table"], $join["condition"], $join["option"]);
                }
            }

            $where = array(
                "components.parent_id" => $id
            );

            $this->db->where($where);
            $this->db->like($searchFields, $pageOptions->search, "both");

            if ($pageOptions->order_column !== "asset_code") {
                $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            }

            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $data = $this->db->get($table)->result();

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");

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
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` on Asset components.","search", "success", "gccasset", "user");
            }
            
            $resultSet['data'] = $data;
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);

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
                ->update("gccasset.vehicles_components", $componentField);

            $this->db->reset_query(); // start fresh query

            $vehicle_id = $this->db->get_where("gccasset.vehicles_components", array("id" => $id))->row("asset_id");
            $isDamage = isset($post["isDamage"]) ? 1 : 0;

            $this->db->reset_query(); // start fresh query

            $this->db->where("id", $vehicle_id)->update("gccasset.vehicles", array("motherID" => 0, "isDamage" => $isDamage));

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

        function removeVehicleComponent($id) {
            $resultSet = array();

            $this->db->trans_begin();

            $vehicle_id = $this->db->get_where("gccasset.vehicles_components", array("id" => $id))->row("asset_id");
            $this->db->where("id", $vehicle_id)->update("gccasset.vehicles", array("motherID" => 0));

            $this->db->reset_query(); // start fresh query

            $this->db->where("id", $id);
            $this->db->delete("gccasset.vehicles_components");

            if ($this->db->trans_status() === FALSE) {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $resultSet['success'] = true;
                $resultSet['message'] = "Vehicle component was removed.";
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function saveComponentToVehicle() {
            $resultSet = array();
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $description = $this->db->get_where("gccasset.vehicles", array("id" => $post->vehicle_id))->row("description");

            $fields = array(
                "asset_id" => $post->vehicle_id,
                "parent_id" => $post->parent_id,
                "description" => $description
            );

            $this->db->trans_begin();

            $this->db->insert("gccasset.vehicles_components", $fields);

            // update vehicle/asset
            $this->db->where("id", $post->vehicle_id)
                ->update("gccasset.vehicles", array("motherID" => $post->parent_id));

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

        function getCosting($id) {
            $resultSet = array();
            $table = "gccasset.asset_cost";
            $searchFields = "CONCAT(description, price)";

            $joinArr = array();
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $where = array(
                "asset_id" => $id
            );

            $this->db->where($where);
            $this->db->like($searchFields, $pageOptions->search, "both");
            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }
            $data = $this->db->get($table)->result();

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` in Vehicles costing Masterfile.","search", "success", "gccasset", "user");
            }

            $resultSet['data'] = $data;
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);

            return $resultSet;
        }

        function saveCosting() {
            $resultSet = array();
            $post = $tableConfigStd = $this->utilities->parseFormDataToObject($this->input->post());
            $post->price = str_replace(',', '', $post->price);

            if (!$this->db->insert("gccasset.asset_cost", $post)) {
                $id = $this->db->insert_id();
                $this->core_layout->setEventLog("User added costing with db id of `".$id."` in Vehicles Masterfile.","insert", "success", "gccasset", "user");
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
            } else {
                $id = $this->db->insert_id();
                $this->core_layout->setEventLog("User failed to add costing with db id of `".$id."` in Vehicles Masterfile.","insert", "error", "gccasset", "system");
                $resultSet['success'] = true;
                $resultSet['message'] = "Cost data added.";
            }

            return $resultSet;
        }

        function removeVehicleCost($id) {
            $resultSet = array();
            $this->db->where("id", $id);

            if ($this->db->delete("gccasset.asset_cost")) {
                $this->core_layout->setEventLog("User deleted costing with db id of `".$id."` in Vehicles costing Masterfile.","delete", "success", "gccasset", "user");
                $resultSet['success'] = true;
                $resultSet['message'] = "Cost data has been removed.";
            } else {
                $this->core_layout->setEventLog("User failed to delete costing with db id of `".$id."` in Vehicles costing Masterfile.","delete", "error", "gccasset", "system");
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
            }

            return $resultSet;
        }

        function getCostingItem($id) {
            $resultSet = array();
            $this->db->where("id", $id);
            $resultSet['data'] = $this->db->get("gccasset.asset_cost")->row();

            return $resultSet;
        }

        function editCosting() {
            $resultSet = array();
            $post = $tableConfigStd = $this->utilities->parseFormDataToObject($this->input->post());
            $post->price = str_replace(',', '', $post->price);

            $this->db->where("id", $post->id);
            if (!$this->db->update("gccasset.asset_cost", $post)) {
                $this->core_layout->setEventLog("User updated costing with db id of `".$post->id."` in Vehicles costing Masterfile.","update", "success", "gccasset", "user");
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
            } else {
                $this->core_layout->setEventLog("User failed to update costing with db id of `".$post->id."` in Vehicles costing Masterfile.","update", "error", "gccasset", "system");
                $resultSet['success'] = true;
                $resultSet['message'] = "Cost data was updated.";
            }

            return $resultSet;
        }

        function getVehicleDocuments($id) {
            $resultSet = array();
            $table = "gccasset.vehicles_document";
            $searchFields = "CONCAT(description)";

            $joinArr = array();
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $where = array(
                "asset_id" => $id
            );

            $this->db->where($where);
            $this->db->like($searchFields, $pageOptions->search, "both");
            $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }
            $data = $this->db->get($table)->result();

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` in Vehicles documents Masterfile.","search", "success", "gccasset", "user");
            }

            $resultSet['data'] = $data;
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);

            return $resultSet;
        }

        function saveDocument($id) {
            $resultSet = array();
            $post = $tableConfigStd = $this->utilities->parseFormDataToObject($this->input->post());
            $folder = 'uploads/files/vehicle_documents/' . $id;

            if (!file_exists($folder)) {
                mkdir($folder, 0775, true);
            }

            $this->db->trans_begin();

            $config['upload_path'] = $folder;
            $config['allowed_types'] = '*';
            $config['max_size'] = '10000';
            $config['file_name'] = $_FILES['file']['name'];

            $this->upload->initialize($config);

            $upload_data = null;

            if ($this->upload->do_upload('file')) {
                $upload_data = $this->upload->data();
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->upload->display_errors();
                $this->db->trans_rollback();

                return $resultSet;
            }

            $field = array("asset_id" => $id, "description" => $post->description, "filename" => $upload_data['file_name']);
            $query = $this->db->insert("gccasset.vehicles_document", $field);

            if($query){
                $id = $this->db->insert_id();
                $this->core_layout->setEventLog("User added document with db id of `".$id."` in Vehicles document Masterfile.","insert", "success", "gccasset", "user");
            }else{
                $id = $this->db->insert_id();
                $this->core_layout->setEventLog("User failed to add document with db id of `".$id."` in Vehicles document Masterfile.","insert", "error", "gccasset", "system");
            }

            if ($this->db->trans_status() === FALSE) {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $resultSet['success'] = true;
                $resultSet['message'] = "Document was added.";
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function removeDocument($id) {
            $resultSet = array();
            $document = $this->db->where("id", $id)->get("gccasset.vehicles_document")->row();
            $file_path = "uploads/files/vehicle_documents/" . $document->asset_id . "/" . $document->filename;

            if (file_exists($file_path)) {
                unlink($file_path);
            }

            if ($this->db->delete("gccasset.vehicles_document", array("id" => $id))) {
                $this->core_layout->setEventLog("User deleted document with db id of `".$id."` in Vehicles document Masterfile.","delete", "success", "gccasset", "user");
                $resultSet['success'] = true;
                $resultSet['message'] = "Document was removed.";
            } else {
                $this->core_layout->setEventLog("User failed to delete document with db id of `".$id."` in Vehicles document Masterfile.","delete", "error", "gccasset", "system");
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
            }

            return $resultSet;
        }

        function getDocumentItem($id) {
            $resultSet = array();
            $this->db->where("id", $id);
            $resultSet['data'] = $this->db->get("gccasset.vehicles_document")->row();

            return $resultSet;
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
            $this->db->update("gccasset.vehicles_document", $field);

            if ($this->db->trans_status() === FALSE) {
                $this->core_layout->setEventLog("User failed to update document with db id of `".$id."` in Vehicles document Masterfile.","delete", "error", "gccasset", "user");
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $this->core_layout->setEventLog("User updated document with db id of `".$id."` in Vehicles document Masterfile.","update", "success", "gccasset", "user");
                $resultSet['success'] = true;
                $resultSet['message'] = "Document was updated.";
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function getVehicleAccountability($id) {
            $resultSet = array();
            $table = "gcceforms.accountability_body body";
            $searchFields = "CONCAT(accountability.date_issued, accountability.reference_no, 
                             accountability.issued_to, accountability.status, 
                             employees.firstname, employees.lastname)";

            $joinArr = array(
                array(
                    "table" => "gccasset.vehicles vehicles",
                    "condition" => "body.asset_id = vehicles.id AND body.type = 'Vehicle'",
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
                    "table" => "gcchris.tblcompanies company",
                    "condition" => "company.id = accountability.company",
                    "option" => "left"
                ),
            );

            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $where = array(
                "asset_id" => $id
            );

            $this->db->select("accountability.date_issued, accountability.reference_no, 
                               accountability.issued_to, `body`.is_returned `status`, accountability.company,
                               CONCAT(employees.firstname, ' ', employees.lastname) employee, accountability.status acct_status,
                               IF(company.id IS NULL, accountability.company, company.description) as company, 'N/A' as overdue, 'ACCOUNTABILITY' as module");
            $this->db->where($where);
            foreach ($joinArr as $join) {
                $this->db->join($join['table'], $join['condition'], $join['option']);
            }
            $this->db->like($searchFields, $pageOptions->search, "both");
            $this->db->order_by("accountability.issued_to","ASC");
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }
            $data = $this->db->get($table)->result();

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` in Vehicles accountability Masterfile.","search", "success", "gccasset", "user");
            }

            $resultSet['data'] = $data;
            $resultSet['sql'] = $this->db->last_query();
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);

            return $resultSet;
        }

            function getVehicleComponentBorrowing($id) {
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
                            "condition" => "body.asset_id = assets.id AND body.type = 'vehicle'",
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

        function getVehicleComponentAccountabilityBorrowing($id){
            $search = $this->input->post();
            $search_result = $search["search"];
            
            if($search["search"] != NULL || $search["search"] != ""){
                $like = "AND (reference_no LIKE '%$search_result%' OR date_issued LIKE '%$search_result%')";
            }else{
                $like = "";
            }
            $query = $this->db->query("SELECT * from (SELECT acc.reference_no as reference_no, DATE(acc.date_issued) as date_issued, acc.issued_to as borrower, 'ACCOUNTABILITY' as module, 'N/A' as days_overdue, acc_body.is_returned as `status`, acc.status as parent_status, acc.created_dt as created_dt, acc.company as company, acc_body.asset_id as asset_id, acc_body.`type` as asset_type FROM gcceforms.accountability_body as acc_body LEFT JOIN gcceforms.accountability as acc ON acc.id=acc_body.accountability_id UNION ALL SELECT bor.reference_no as reference_no, DATE(bor.date_trans) as date_issued, bor.borrower as borrower, 'BORROWING' as module, DATEDIFF(CURDATE(), DATE(bor_body.date_due)) days_overdue, bor_body.is_returned as `status`, bor.created_dt as created_dt, bor.company as company, bor_body.asset_id as asset_id, bor_body.`type` as asset_type, bor.status as parent_status FROM gcceforms.borrowing_body as bor_body LEFT JOIN gcceforms.borrowing as bor ON bor.id=bor_body.borrowing_id) as table1 WHERE asset_id='$id' and (asset_type='Vehicle' OR asset_type='vehicle_component') $like order by created_dt desc");
            
            $data = $query->result_array();
            
            $resultSet = array();
            $display = array();
            $data_content = array();
            foreach($data as $result){
                
                $display['reference_no'] = $result['reference_no'];
                $name = $this->core_layout->getEmployeeData($result['borrower']);
                $name = (object) $name;
                $tempName = isset($name->display_name_1) && $name->display_name_1 ? strtoupper($name->display_name_1): "NO ASSIGNED NAME";
                
                $display['borrower'] = $tempName;
                $display['company'] = $this->getEmployementData($result['borrower'],'department');
                // $display['company'] = 'adssad';
                $display['date_issued'] = $result['date_issued'];
                $display['module'] = $result['module'];
                if($result['days_overdue'] != 'N/A'){
                    if($result['days_overdue'] > 1 && $result['days_overdue'] != 0){
                        $day = "days";
                    }else{
                        $day = "day";
                    }
                }else{
                    $day = "";
                }    
                $display['over_due'] = $result['days_overdue']." ".$day;
                $display['parent_status'] = $result['parent_status'];
                $display['status'] = $result['status'];
                $data_content[] = $display;
            }
            $resultSet['data'] = $data_content;
            $resultSet["recordsTotal"] = count($data_content);
            $resultSet["recordsFiltered"] = count($data_content);
            
            if(!empty($search['search'])){
                $this->core_layout->setEventLog("User searched `".$search['search']."` in Vehicle Component Masterfile.","search", "success", "gccasset", "user");
            }
            return $resultSet;
        }

        function getEmployementData($id,$column=null){
            $this->load->model("eforms/loa_m");
            $this->db->select("*");
            $this->db->where("id",$id);
            $query = $this->db->get("gccmaster.tblemployees");
            $html = "NO ". strtoupper($column) ." NAME";
            
            if ($query->num_rows() > 0) {
                $data = $query->row();

                if($column == 'department'){
                    $html = is_numeric($data->department_id) ? $this->db->get_where("gcchris.tbldepartments", array("id"=>$data->department_id))->row("description") : $data->department_id;
                    // if(is_numeric($data->department_id)){
                    //     return $this->db->get_where("gcchris.tbldepartments", array("id"=>$data->department_id))->row("description");
                    // }else{
                    //     return $data->department_id;
                    // }
                }else if($column == 'company'){
                    $html = is_numeric($data->company_id) ? $this->db->get_where("gcchris.tblcompanies", array("id"=>$data->company_id))->row("description") : $data->company_id;

                    // if(is_numeric(trim($data->company_id))){
                    //     return $this->db->get_where("gcchris.tblcompanies", array("id"=>$data->company_id))->row("description");
                    // }else{
                    //     return $data->company_id;
                    // }
                }else{
                    $html = is_numeric($data->position) ? $this->db->get_where("gcchris.tblposition", array("id"=>$data->position))->row("name") : $data->position;
                    // if(is_numeric($data->position)){
                    //     return $this->db->get_where("gcchris.tblposition", array("id"=>$data->position))->row("name");
                    // }else{
                    //     return $data->position;
                    // }
                }
            }

            return $html;
        }

        function getVehicleMaintenanceLog($id) {
            $resultSet = array();
            $table = "gccasset.preventive_trans pt";
            $searchFields = "CONCAT(ptbd.description, pt.last_value, pt.next_value, pt.last_date_perform, pt.next_date_perform)";

            $joinArr = array(
                array(
                    "table" => "gccasset.preventive_temp_body ptbd",
                    "condition" => "pt.ptbd_id = ptbd.id",
                    "option" => "inner"
                ),
            );

            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $where = array(
                "pt.vh_id" => $id
            );

            $this->db->select("ptbd.description, pt.*");
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

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` in Vehicles maintenance Masterfile.","search", "success", "gccasset", "user");
            }

            $resultSet['data'] = $data;
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);

            return $resultSet;
        }

        public function getPreventiveTempHeadCollection() {
            $q = isset($_GET["q"]) ? $_GET["q"] : "";
            $this->db->like("description", $q, "both");
            $result = $this->db->get("gccasset.preventive_temp_head")->result();
            return array("results" => $result);
        }

        public function loadPreventiveTemplateBody($pth_id) {
            $table = "gccasset.preventive_temp_body";
            $resultSet = array();
            $this->db->where("pth_id", $pth_id);
            $resultSet['data'] = $this->db->get($table)->result();

            return $resultSet;
        }

        public function saveMaintenanceTemplateLog() {
            $resultSet = array();
            $this->db->trans_begin();

            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $preventive_temp_head = $post->preventive_temp_head;
            unset($post->preventive_temp_head);

            // format date to Y-m-d
            $post->last_date_perform = array_map(function ($item) {
                $_date = new DateTime($item);
                return $_date->format("Y-m-d");
            }, $post->last_date_perform);

            $post->next_date_perform = array_map(function ($item) {
                $_date = $item ? new DateTime($item) : "";
                return $_date ? $_date->format("Y-m-d") : "";
            }, $post->next_date_perform);

            $this->db->insert_batch("gccasset.preventive_trans", $post);

            if ($this->db->trans_status() === FALSE) {
                $this->core_layout->setEventLog("User failed to add in maintenance templatelog in Vehicles maintenance Masterfile.","insert", "error", "gccasset", "system");
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $this->core_layout->setEventLog("User added in maintenance templatelog in Vehicles maintenance Masterfile.","insert", "success", "gccasset", "user");
                $resultSet['success'] = true;
                $resultSet['message'] = "Preventive maintenance log was saved.";
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function removeMaintenanceItem($id, $vh_id, $all) {
            $resultSet = array();
            $this->db->trans_begin();

            if ((int)$all === 1) {
                $this->db->where("vh_id", $vh_id);
                $this->db->delete("gccasset.preventive_trans");
            } else {
                $this->db->where("id", $id);
                $this->db->delete("gccasset.preventive_trans");
            }

            if ($this->db->trans_status() === TRUE) {
                $resultSet['success'] = true;
                $resultSet['message'] = (int)$all === 1 ? "All Maintenance log items was removed." : "Maintenance log item was removed.";
                $this->core_layout->setEventLog($resultSet['message']." by User","remove", "success", "gccasset", "user");
                $this->db->trans_commit();
            } else {
                $this->core_layout->setEventLog("User failed to remove the mainenance log items in vehicle maintenance masterfile","remove", "error", "gccasset", "system");
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->db->trans_rollback();
            }

            return $resultSet;
        }

        function getMaintenanceItem($id) {
            $resultSet = array();

            $joinArr = array(
                array(
                    "table" => "gccasset.preventive_temp_body ptbd",
                    "condition" => "pt.ptbd_id = ptbd.id",
                    "option" => "inner"
                ),
            );

            $this->db->where("pt.id", $id);
            $this->db->select("ptbd.description, pt.*");
            foreach ($joinArr as $join) {
                $this->db->join($join['table'], $join['condition'], $join['option']);
            }

            return array(
                "data" => $this->db->get("gccasset.preventive_trans pt")->row()
            );
        }

        function updateMaintenanceLog() {
            $resultSet = array();
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $id = $post->id;
            unset($post->id);

            $this->db->where("id", $id);
            if ($this->db->update("gccasset.preventive_trans", $post)) {
                $this->core_layout->setEventLog("User updated the maintenance template log with the db id of `".$id."` in Vehicles maintenance Masterfile.","update", "success", "gccasset", "user");
                $resultSet['success'] = true;
                $resultSet['message'] = "Log item was updated.";
            } else {
                $this->core_layout->setEventLog("User failed to update the maintenance template log with the db id of `".$id."` in Vehicles maintenance Masterfile.","update", "error", "gccasset", "system");
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
            }

            return $resultSet;
        }

        function reIncludeComponent($id) {
            $resultSet = array();

            $this->db->where("id", $id);
            if ($this->db->update("gccasset.vehicles_components", array("isExcluded" => 0))) {
                $this->db->reset_query();
                $asset = $this->db->get_where("gccasset.vehicles_components", array("id" => $id))->row();
                $this->db->reset_query();
                $this->db->where("id", $asset->asset_id)->update("gccasset.vehicles", array("motherId" => $asset->parent_id));

                $resultSet['success'] = true;
                $resultSet['message'] = "Component was re-included.";
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
            }

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

            $selectAccountabilityFields = "acct_body.asset_id, CONCAT('(',TRIM(assets.gen_code),')',if(assets.`name` IS NULL OR assets.`name`='',assets.description, assets.`name`)) asset_name";
            $selectAccountabilityFields .= ", acct_body.is_returned, ";
            $selectAccountabilityFields .= "acct.issued_to, IF(acct.is_contract=1, contractors.contractor, ";
            $selectAccountabilityFields .= "IF(employees.firstname IS NULL,CONCAT('EMPLOYEE ', acct.issued_to),CONCAT(employees.firstname, ' ', employees.lastname))) issued_to";

            $selectBorrowingHistory = "IF(employees.firstname IS NULL,CONCAT('EMPLOYEE ', br.borrower),CONCAT(employees.firstname, ' ', employees.lastname)) borrower_name,";
            $selectBorrowingHistory .= "if(assets.`name` IS NULL OR assets.`name`='', assets.description, assets.`name`) asset_name,";
            $selectBorrowingHistory .= "br.borrower borrower_id, br_body.is_returned,";

            if ($isComponent != 0) {
                $mother_asset = $this->db->select("motherId")->where("id", $asset_id)->get("gccasset.vehicles")->row("motherId");

                if ($mother_asset != 0) {
                    $this->db->select($selectAccountabilityFields);
                    $this->db->join("gcceforms.accountability acct", "acct_body.accountability_id = acct.id AND LCASE(acct_body.`type`)='$type'", "inner");
                    $this->db->join("gcchris.tblcontractor contractors", "contractors.id = acct.issued_to", "LEFT");
                    $this->db->join("gccmaster.tblemployees employees", "employees.id = acct.issued_to", "LEFT");
                    $this->db->join("gccasset.vehicles assets", "assets.id = acct_body.asset_id", "inner");
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
            $this->db->join("gccasset.vehicles assets", "assets.id = acct_body.asset_id", "inner");
            $this->db->where("acct_body.asset_id", $asset_id);
            $this->db->where("acct_body.is_returned", 0);
            $this->db->where("acct.status !=", "Cancelled");
            $accountability = $this->db->get("gcceforms.accountability_body acct_body")->row();

            $this->db->reset_query();

            $this->db->select($selectBorrowingHistory);
            $this->db->join("gcceforms.borrowing br", "br_body.borrowing_id = br.id AND LCASE(br_body.`type`)='$type'", "inner");
            $this->db->join("gccmaster.tblemployees employees", "employees.id = br.borrower", "INNER");
            $this->db->join("gccasset.vehicles assets", "assets.id = br_body.asset_id", "INNER");
            $this->db->where("br_body.asset_id", $asset_id);
            $this->db->where("br_body.is_returned", 0);
            $this->db->where("br.status !=", "Cancelled");
            $borrowing_history = $this->db->get("gcceforms.borrowing_body br_body")->row();

            // if current asset is not accounted get data from master file table
            $this->db->reset_query();

            if (!empty($accountability) && $has_mother_asset && $mother_asset_accountability) {
                $accountability = $this->db
                    ->select("CONCAT('(',TRIM(gen_code),')',if(`name` IS NULL OR `name`='', description, `name`)) asset_name")
                    ->where("id", $asset_id)
                    ->get("gccasset.vehicles")
                    ->row();
            }

            return array(
                "accountability" => $accountability,
                "borrowing_history" => $borrowing_history,
                "has_mother_asset" => $has_mother_asset,
                "mother_asset_accountability" => $mother_asset_accountability
            );
        }

        function relocateVehicleImages() {
            ini_set('max_execution_time', 6000);
            set_time_limit(6000);

            $root_dir = "uploads/files/images";
            $live_image_dir = "http://bcd.gccph.com/ams/server/php/images/files";

            $this->db->select("a.id asset_id, a.primary_pic");
            // $this->db->where("a.isCompo = 1 AND (a.status2 NOT IN('archived', 'lost', 'junk', 'tradein') OR `status2` IS NULL) AND a.isJunk != 1");
            $list = $this->db->get("gccasset.vehicles a")->result();

            $this->db->reset_query();

            foreach ($list as $item) {
                $dir = $root_dir . "/vehicles/" . $item->asset_id;
                $thumbnail_path = $dir . "/thumbnails";

                $images = $this->db
                    ->where("asset_id", $item->asset_id)
                    ->where("image !=", '')
                    ->get("gccasset.vehicles_image")
                    ->result();

                if (!empty($item->primary_pic) && $item->primary_pic != '') {
                    $dir_exist = file_exists(realpath($dir));

                    if (!$dir_exist) {
                        mkdir($dir, 0775, true);
                    }

                    $thumbnail_path_exist = file_exists(realpath($thumbnail_path));

                    if (!$thumbnail_path_exist) {
                        mkdir($thumbnail_path, 0775, true);
                    }

                    $main_image_file_path = $dir . "/" . $item->primary_pic;

                    $insertMain = $this->insertMainVehicleImage($item->asset_id, $item->primary_pic);

                    $live_main_image_filepath = $live_image_dir . "/" . $item->primary_pic;

                    if (!file_exists(realpath($main_image_file_path))) {
                        $main_image_content = $this->get_contents($live_main_image_filepath);

                        if (!empty($main_image_content)) {
                            if (file_put_contents($main_image_file_path, $main_image_content)) {
                                $thumbnail_file_path = $thumbnail_path . "/" . $item->primary_pic;
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

        function relocateVehicleDocuments() {
            ini_set('max_execution_time', 6000);
            set_time_limit(6000);

            $root_dir = "uploads/files";
            $live_image_dir = "http://bcd.gccph.com/ams/server/php/documents/vehicles";

            $this->db->select("document.id, document.filename, vehicles.id asset_id");
            $this->db->join("gccasset.vehicles vehicles", "vehicles.id = document.asset_id", "inner");
            $list = $this->db->get("gccasset.vehicles_document document")->result();

            foreach ($list as $item) {
                $dir = $root_dir . "/vehicle_documents/" . $item->asset_id;
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

        private function insertMainVehicleImage($asset_id, $filename) {
            $this->db->where("asset_id", $asset_id);
            $this->db->where("image", $filename);
            $count = $this->db->count_all_results("gccasset.vehicles_image");

            if ($count <= 0) {
                $data = array(
                    "asset_id" => $asset_id,
                    "image" => $filename,
                    "dateupload" => date('Y-m-d H:i:s'),
                    "useruploaded" => $this->core_layout->getUserId()
                );
                $this->db->insert("gccasset.vehicles_image", $data);
            }
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
            $this->db->where("b.is_returned !=", 1);
            $this->db->where("b.type", "Vehicle");
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
                    $data["id"] = $_query["code"];
                    $data["text"] = $_query["description"];
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
            $data["updated_by"] = $session_data['emp_id'];
            $data["updated_at"] = date("Y-m-d h:i:s");

            $this->db->where('id', $post["id"]);
            $doInventory = $this->db->update("gccasset.vehicles", $data);

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
                    $ndata["asset_type"] = 2;

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
                $this->core_layout->setEventLog("User added metadata with the db id of `".$insert_id."` in Vehicles Metadata Masterfile.","insert", "success", "gccasset", "user");
                return true;
            } else {
                $this->core_layout->setEventLog("User failed to add metadata in Vehicles Metadata Masterfile.","insert", "error", "gccasset", "system");
                return false;
            }
        }
    }