<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Assetperlocation_model extends CI_Model {
        protected $assetTable = "gccasset.assets";
        private $today;

        function __construct() {
            parent::__construct();
            $this->load->library('image_lib');
            $this->load->model("Utilities_model", "utilities");
            $this->load->model("Assets_model", "assets_m");
            $this->today = new DateTime('now', new DateTimezone('Asia/Manila'));
        }

        private function getUserData() {
            return $this->core_layout->getUserLoggedIn();
        }

        function assetCollection($export) {
            $resultSet = array();
            $assetIDs = array();
            $advSearch = array();
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            if($tableConfig['accountability_search']){
                $assetIDs = $this->accountability_search($tableConfig['accountability_search']);
                $assetIDs = array_map(function($item){
                    return $item['asset_id'];
                }, $assetIDs);
                $assetIDs =  implode(",", $assetIDs);
            }

            if(isset($tableConfig['advanced_search'])){
                $advSearch = array_map(function($item){
                    return $item;
                }, $tableConfig['advanced_search']);
            }

            $table = "gccasset.assets a";

            $sql = "a.id, a.id as asset_id, UPPER(a.assetacode) as assetacode, UPPER(a.name) as name, UPPER(a.assetname) as description, UPPER(a.location) as location_id, ";
            $sql .= "a.area_id, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, UPPER(a.department_code) as department_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.modelno) as modelno, UPPER(a.status) as status, ";
            $sql .= "IF(a.location != '', UPPER(c.station), 'N/A') AS station, IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.dateCreated, ";
            $sql .= "IFNULL(null,a.pic_filename) as image, a.datepurchased, b.location, UPPER(d.description) as asset_category, a.purchaseprice";
            $sql .= ", a.is_borrowed, a.po_no, UPPER(IF(comp.id IS NULL, a.company_code, comp.code)) comp_name, UPPER(IF(dep.id IS NULL, a.department_code, dep.description)) dep_name";

            $searchFields = "CONCAT(IF(a.assetacode IS NULL, '', a.assetacode), IF(a.`name`IS NULL, '', a.name), 
                            IF(a.`assetname`IS NULL, '', a.assetname), IF(a.`location`IS NULL, '', a.location), 
                            IF(a.brand IS NULL, '', a.brand), IF(a.modelno IS NULL, '', a.modelno), IF(a.company_code IS NULL, '', a.company_code),
                            IF(a.department_code IS NULL, '', a.department_code))";
            $where = "a.isComponent = 0 AND (a.status NOT IN('archived', 'lost', 'junk', 'tradein') OR `status` IS NULL) AND a.isJunk != 1";
            if(!empty($assetIDs)){
                $where .= " AND a.id in($assetIDs)";
            }

            if(!empty($advSearch) && $advSearch['area'] == "0"){
                $where .= " AND (a.area = '')";
            }

            if(isset($tableConfig['locationId']) && $tableConfig['locationId']){
                $where .= " AND (a.area_id = $tableConfig[locationId])";
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

            if(!empty($advSearch) && $advSearch['area'] != "0"){
                $this->db->like($advSearch, "both");
                $this->db->like($searchFields, $pageOptions->search, "both");
            }else{
                if(isset($tableConfig['locationId']) && $tableConfig['locationId']){
                    $where .= " AND (a.area_id = $tableConfig[locationId])";
                }else{
                    $this->db->like($searchFields, $pageOptions->search, "both");
                }
            }
           
            if (intval($export) === 0) {
                if ($pageOptions->length > -1) {
                    $this->db->limit($pageOptions->length, $pageOptions->start);
                }
            }

            if ($pageOptions->order_column === "image") {
                $this->db->order_by("a.dateUpdated", "desc");
            } else {
                $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            }

            $queryResult = $this->db->get();

            //$resultSet["sql"] = $this->db->last_query();

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

                    $rs->purchaseprice = number_format($rs->purchaseprice,2);
                    $rs->accounted_to = strtoupper($this->assets_m->getAssetAccountabilityByReturnStatus($id));
                    $rs->datepurchased = ($rs->datepurchased != "0000-00-00") ? date("Y F d", strtotime(trim($rs->datepurchased))) : "0000-00-00";
                    array_push($data, $rs);
                }
            }

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` on Assetperlocation (Assets & Components) Masterfile.","search", "success", "gccasset", "user");
            }
            
            if(isset($tableConfig['accountability_search']) && !empty($tableConfig['accountability_search'])){
                $this->core_layout->setEventLog("User searched employee with a emp_id of `".$tableConfig['accountability_search']."` on Assetperlocation (Assets & Components) Masterfile.","search", "success", "gccasset", "user");
            }

            if(isset($tableConfig['advanced_search']) && !empty($tableConfig['advanced_search'])){
                $output = '';
                $output .= ($advSearch['area'] != '')? 'Area: '.$advSearch['area'].'':'';

                $this->core_layout->setEventLog("User searched from advanced search with `".$output."` on Assetperlocation (Assets & Components) Masterfile.","search", "success", "gccasset", "user");
            }
          
            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr, false, $advSearch);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr, false, $advSearch);
            $resultSet["data"] = $data;

            return $resultSet;
        }

        function getAssetDetails($id) {
            $this->db->select("asset.*, comp.description company_name, asset_cat.description asset_cat_description, 
                               stat.name status_name, dep.description dep_description, 
                               station.station station_description, location.location location_description,
                               stat.code status_code");
            $this->db->join("gcchris.tblcompanies comp", "comp.code = asset.company_code", "left");
            $this->db->join("gccasset.assetcategory asset_cat", "asset_cat.code = asset.asset_category", "left");
            $this->db->join("gccasset.status stat", "`stat`.`code` = asset.status", "left");
            $this->db->join("gcchris.tbldepartments dep", "dep.id = asset.department_code", "left");
            $this->db->join("gccasset.station station", "station.id = asset.location", "left");
            $this->db->join("gccasset.location location", "location.id = asset.area_id", "left");
            $asset = $this->db->get_where("gccasset.assets asset", array("asset.id" => $id))->row();

            if (!is_numeric($asset->department_code)) {
                $first_department = $this->db->order_by("id", "asc")->get_where("gcchris.tbldepartments", array("code" => $asset->department_code))->row();
                $asset->dep_description = $first_department->description;
                $asset->department_code = $first_department->id;
            }

            if(isset($asset->mother_asset)){
                $mother_asset = $this->getMotherAsset($asset->mother_asset);

                $asset->mother_code = $mother_asset['assetacode'];
                $asset->mother_name = $mother_asset['name'];
            }else{
                $asset->mother_code = NULL;
                $asset->mother_name = NULL;
            }

            return $asset;
        }

        public function accountability_search($id){
            $this->db->select("b.asset_id");
            $this->db->from("gcceforms.accountability_body b");
            $this->db->join("gcceforms.accountability c","b.accountability_id = c.id", "INNER");
            $this->db->where("b.type","Asset");
            $this->db->where("b.is_returned !=",1);
            $this->db->where("c.status !=","Cancelled");
            $this->db->where("c.issued_to",$id);
            $query = $this->db->get();
            return $query->result_array();
        }

        function getMotherAsset($id){
            $this->db->select("a.assetacode, a.name");
            $this->db->from("gccasset.assets a");
            $this->db->where("a.id",$id);
            $query = $this->db->get();
            return $query->row_array();
        }

        function getAssetImages($id) {
            $this->db->select("id, image name, null as base64", FALSE);
            return $this->db->get_where("gccasset.asset_image", array("asset_id" => $id))->result();
        }

        function vehicleCollection($export) {
            $assetIDs = array();
            $advSearch = array();
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);
            $table = "gccasset.vehicles a";
            
            if($tableConfig['accountability_search']){
                $assetIDs = $this->accountability_search_v($tableConfig['accountability_search']);
                $assetIDs = array_map(function($item){
                    return $item['asset_id'];
                }, $assetIDs);
                $assetIDs =  implode(",", $assetIDs);
            }

            if(isset($tableConfig['advanced_search'])){
                $advSearch = array_map(function($item){
                    return $item;
                }, $tableConfig['advanced_search']);

                if(isset($advSearch['assetacode'])){
                    $advSearch['gen_code'] = $advSearch['assetacode'];
                    unset($advSearch['assetacode']);
                }
                
                if(isset($advSearch['assetname'])){
                    $advSearch['a.description'] = $advSearch['assetname'];
                    unset($advSearch['assetname']);
                }

                if(isset($advSearch['area'])){
                    $advSearch['b.location'] = $advSearch['area'];
                    unset($advSearch['area']);
                }
              
                if(isset($advSearch['description'])){
                    $advSearch['asset_category'] = $advSearch['description'];
                    unset($advSearch['description']);
                }
            }

            $sql = "a.id, a.id as asset_id, a.gen_code as assetacode, UPPER(a.name) as name, UPPER(a.description) as description, ";
            $sql .= "UPPER(a.plateno) as plateno, UPPER(a.brand) as brand, UPPER(a.company_code) as company_code, ";
            $sql .= "UPPER(a.serialno) as serialno, UPPER(a.model) as model, UPPER(a.status2) as status, ";
            $sql .= "IF(a.area_id != 0,UPPER( b.location), 'N/A') AS area, a.created_at as dateCreated, ";
            $sql .= "IFNULL(null,a.primary_pic) as image, a.datepurchased, UPPER(c.description) as asset_category";
            $sql .= ", a.is_borrowed, a.motherId, a.purchaseprice, UPPER(IF(comp.id IS NULL, a.company_code, comp.code)) comp_name";

            $searchFields = "CONCAT(IF(a.gen_code IS NULL, '', a.gen_code), IF(a.`name`IS NULL, '', a.name), 
                            IF(a.`plateno`IS NULL, '', a.plateno), IF(a.`serialno`IS NULL, '', a.serialno), 
                            IF(a.model IS NULL, '', a.model), IF(a.description IS NULL, '', a.description))";

            $where = "(a.status2 NOT IN('archived', 'lost', 'junk', 'tradein') OR `status2` IS NULL) AND a.isJunk != 1";
            if(!empty($assetIDs)){
                $where .= " AND a.id in($assetIDs)";
            }

            if(!empty($advSearch) && $advSearch['b.location'] == "0"){
                $where .= " AND (b.location = '')";
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
                array("table" => "gcchris.tblcompanies comp", "condition" => "comp.id = a.company_code", "option" => "LEFT"),
            );

            $this->db->select($sql);
            $this->db->from($table);
            if ($joinArr) {
                foreach ($joinArr as $join) {
                    $this->db->join($join["table"], $join["condition"], $join["option"]);
                }
            }

            $this->db->where($where);

            if(!empty($advSearch) && $advSearch['b.location'] != "0"){
                $this->db->like($advSearch, "both");
                $this->db->like($searchFields, $pageOptions->search, "both");
            }else{
                $this->db->like($searchFields, $pageOptions->search, "both");
            }

            if (intval($export) === 0) {
                if ($pageOptions->length > -1) {
                    $this->db->limit($pageOptions->length, $pageOptions->start);
                }
            }

            if ($pageOptions->order_column === "image") {
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

                    $rs->purchaseprice = number_format($rs->purchaseprice,2);
                    $rs->accounted_to = strtoupper($this->assets_m->getAssetAccountabilityByReturnStatus($id, 'Vehicle'));
                    $rs->datepurchased = ($rs->datepurchased != "0000-00-00") ? date("Y F d", strtotime(trim($rs->datepurchased))) : "0000-00-00";
                    array_push($data, $rs);
                }
            }

            $resultSet["data"] = $data;

            if(!empty($pageOptions->search)){
                $this->core_layout->setEventLog("User searched `".$pageOptions->search."` on Assetperlocation (Vehicles & Equipment) Masterfile.","search", "success", "gccasset", "user");
            }
            
            if(isset($tableConfig['accountability_search']) && !empty($tableConfig['accountability_search'])){
                $this->core_layout->setEventLog("User searched employee with a emp_id of `".$tableConfig['accountability_search']."` on Assetperlocation (Vehicles & Equipment) Masterfile.","search", "success", "gccasset", "user");
            }

            if(isset($tableConfig['advanced_search']) && !empty($tableConfig['advanced_search'])){
                $output = '';
                $output .= ($advSearch['area'] != '')? 'Area: '.$advSearch['area'].'':'';

                $this->core_layout->setEventLog("User searched from advanced search with `".$output."` on Assetperlocation (Vehicles & Equipment) Masterfile.","search", "success", "gccasset", "user");
            }

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr, false, $advSearch);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr, false, $advSearch);
            return $resultSet;
        }

        public function accountability_search_v($id){
            $this->db->select("b.asset_id");
            $this->db->from("gcceforms.accountability_body b");
            $this->db->join("gcceforms.accountability c","b.accountability_id = c.id", "INNER");
            $this->db->where("b.is_returned !=",1);
            $this->db->where("b.type","Vehicle");
            $this->db->where("c.status !=","Cancelled");
            $this->db->where("c.issued_to",$id);
            $query = $this->db->get();
            return $query->result_array();
        }

        function getAssetAccountability($id) {
            $resultSet = array();
            $table = "gcceforms.accountability_body body";
            $searchFields = "CONCAT(accountability.date_issued, accountability.reference_no, 
                             accountability.issued_to, accountability.status, 
                             employees.firstname, employees.lastname)";

            $joinArr = array(
                array(
                    "table" => "gccasset.assets assets",
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
                    "table" => "gcchris.tblcompanies company",
                    "condition" => "company.id = employees.company_id",
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
                               accountability.issued_to, body.is_returned status, company.description company,
                               CONCAT(employees.firstname, ' ', employees.lastname) employee,
                               accountability.status acct_status");
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
                    "condition" => "company.id = employees.company_id",
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
                               accountability.issued_to, `body`.is_returned `status`, company.description company,
                               CONCAT(employees.firstname, ' ', employees.lastname) employee, accountability.status acct_status");
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
            $resultSet['sql'] = $this->db->last_query();
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);

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
                array_push($resultarray,array("id"=>0, "text"=>"No Location"));
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["location"];
                    $data["text"] = $_query["location"];
                
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function getAssetsLocation(){
            $result = array();
            $arrData = array();

            $sql = "id, location, latitude, longitude";
            $this->db->select($sql);
            $this->db->from("gccasset.location");
            $this->db->where('latitude !=', 0);
            $this->db->where('longitude !=', 0);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach($query->result() as $key => $rs){
                    // $rs->assets = $this->db->get_where('gccasset.assets', array('area_id' => $rs->id))->num_rows();
                    $rs->assets = $this->getAssetsInLocation($rs->id);

                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $result[] = $v;
                }
            }

            return $result;
        }

        function getAssetsInLocation($id){
            $this->db->from('gccasset.assets a');
            
            $joinArr = array(
                array("table" => "gccasset.location b", "condition" => "b.id = a.area_id", "option" => "LEFT"),
                array("table" => "gccasset.station c", "condition" => "c.id = a.location", "option" => "LEFT"),
                array("table" => "gccasset.assetcategory d", "condition" => "d.code = a.asset_category", "option" => "LEFT"),
                array("table" => "gccasset.asset_sub_cat e", "condition" => "e.sub_cat_id = a.sub_cat_code", "option" => "LEFT"),
                array("table" => "gcchris.tblcompanies comp", "condition" => "comp.id = a.company_code", "option" => "LEFT"),
                array("table" => "gcchris.tbldepartments dep", "condition" => "dep.id = a.department_code", "option" => "LEFT"),
            );

            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $this->db->where('a.area_id', $id);
            $this->db->group_start();
            $this->db->where('a.isComponent', 0);
            $this->db->where_not_in('a.status', array('archived', 'lost', 'junk', 'tradein'));
            $this->db->or_where('a.status', NULL);
            $this->db->group_end();
            $this->db->where('a.isJunk !=', 1);
            $sql = $this->db->get();
            return $sql->num_rows();
        }
    }