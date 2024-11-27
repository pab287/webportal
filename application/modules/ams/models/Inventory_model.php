<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Inventory_model extends CI_Model {
        function __construct() {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->load->model("Utilities_model", "utilities");
            $this->load->model("core/Upload_model", "core_upload");
            $this->load->model("core/Core_model", "core");
            $this->load->model("core/upload_model", "file_upload");
            $this->load->library('image_lib');
            date_default_timezone_set("Asia/Manila");
        }

        private function getUserData() {
            return $this->core_layout->getUserLoggedIn();
        }

        function getInventoryReportData() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $date_array = explode(" - ", $post->date);
            $date_start = new DateTime($date_array[0]);
            $date_end = new DateTime($date_array[1]);

            $is_archived = (isset($post->archived) && !empty($post->archived))? " with Archived data":"";
            $output = 'date of '.$post->date.', Company of '.$post->company.' and type of '.$post->type.''.$is_archived;
            $this->core_layout->setEventLog("User searched with the `".$output."` in Inventory logs datatable.","search", "success", "gccasset", "user");
           
            $resultSet = array();
            $resultSet["summary"] = $this->getDataForGraph($date_start, $date_end, $post->type, $post->company, isset($post->is_archived) ? $post->is_archived : "");

            return $resultSet;
        }

        private function getDataForGraph($start, $end, $type, $company, $is_archived) {
            $queryAllVerified = "";
            $queryAllVerified .= "SELECT * FROM ( ";
            $queryAllVerified .= "(SELECT ";
            $queryAllVerified .= "	`inv`.*, ";
            $queryAllVerified .= "	cat.description category, ";
            $queryAllVerified .= "	vh.`name` asset_name, ";
            $queryAllVerified .= "	vh.`description` description, ";
            $queryAllVerified .= "	vh.`company_code` company_code, ";
            $queryAllVerified .= "	vh.gen_code asset_code, ";
            $queryAllVerified .= "	vh.datepurchased date_purchased, ";
            $queryAllVerified .= "	loc.location, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_date') AS datetime) AS inventory_date, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_by') AS decimal) AS inventory_by, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'date_updated') AS datetime)AS date_updated, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'updated_by') AS decimal) AS updated_by, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'remarks') AS remarks, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'status') AS status, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'inventory_status') AS inventory_status ";
            $queryAllVerified .= "FROM `gccasset`.`inventory_list` `inv` ";
            $queryAllVerified .= "INNER JOIN `gccasset`.`vehicles` vh ON `vh`.`id` = `inv`.`asset_id` AND inv.`asset_type`=2 ";
            $queryAllVerified .= "LEFT JOIN  gccasset.location loc ON loc.id = vh.area_id ";
            $queryAllVerified .= "LEFT JOIN gccasset.assetcategory cat ON cat.code = vh.asset_category ";
            if(isset($is_archived)): 
                $queryAllVerified .= "WHERE vh.`status2` NOT IN('operational', 'brandnew') AND vh.`status2` IS NOT NULL AND vh.`status2` != '' ";
                endif;
            $queryAllVerified .= ")UNION ";
            $queryAllVerified .= "(SELECT ";
            $queryAllVerified .= "	`inv`.*, ";
            $queryAllVerified .= "	cat.description category, ";
            $queryAllVerified .= "	assets.`name` asset_name, ";
            $queryAllVerified .= "	assets.`assetname` description, ";
            $queryAllVerified .= "	assets.`company_code` company_code, ";
            $queryAllVerified .= "	assets.gen_code asset_code, ";
            $queryAllVerified .= "	assets.datepurchased date_purchased, ";
            $queryAllVerified .= "	loc.location, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_date') AS datetime) AS inventory_date, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_by') AS decimal) AS inventory_by, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'date_updated') AS datetime)AS date_updated, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'updated_by') AS decimal) AS updated_by, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'remarks') AS remarks, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'status') AS status, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'inventory_status') AS inventory_status ";
            $queryAllVerified .= "FROM `gccasset`.`inventory_list` `inv` ";
            $queryAllVerified .= "INNER JOIN `gccasset`.`assets` assets ON `assets`.`id` = `inv`.`asset_id` AND inv.`asset_type`=1 ";
            $queryAllVerified .= "LEFT JOIN  gccasset.location loc ON loc.id = assets.area_id ";
            $queryAllVerified .= "LEFT JOIN gccasset.assetcategory cat ON cat.code = assets.asset_category ";
            if(isset($is_archived)): 
                $queryAllVerified .= "WHERE assets.`status` NOT IN('operational', 'brandnew') AND assets.`status` IS NOT NULL AND assets.`status` != '' ";
                endif;
            $queryAllVerified .= ")) AS tbl ";
            $queryAllVerified .= "WHERE DATE(tbl.inventory_date) >='" . $start->format('Y-m-d') . "' AND DATE(tbl.inventory_date) <='" . $end->format('Y-m-d') . "'";

            if ($company != 'all') {
                $queryAllVerified .= " AND tbl.company_code='$company'" . ($type != 3 ? " AND tbl.asset_type=$type" : "");
            } else {
                $queryAllVerified .= $type != 3 ? " AND tbl.asset_type=$type" : "";
            }

            $queryVerifiedResult = $this->db->query($queryAllVerified);
            $verified = $queryVerifiedResult->num_rows();

            $this->db->reset_query();

            $whereType = $type != 3 ? "tbl.`type`=$type AND" : "";
            $whereCompany = $company != "all" ? "tbl.company_code='$company' AND" : "";

            $sql_get_unverified = "SELECT COUNT(*) asset_count FROM (SELECT assets.id, assets.company_code, 1 AS `type`, `status` AS `status`, isJunk";
            $sql_get_unverified .= " FROM gccasset.assets assets UNION SELECT vehicles.id, vehicles.company_code, 2 AS `type`, `status2` AS `status`, isJunk";
            $sql_get_unverified .= " FROM gccasset.vehicles vehicles) AS tbl WHERE " . $whereCompany . " " . $whereType . " tbl.isJunk !=1 AND ";
            $sql_get_unverified .= " (tbl.`status` NOT IN('archived', 'lost', 'junk', 'tradein') OR tbl.`status` IS NULL)";
            $this->db->query($sql_get_unverified);

            $queryUnverified = $this->db->query($sql_get_unverified);
            $unverified = $queryUnverified->row("asset_count");

            return array(
                array("cluster" => "Verified", "value" => $verified),
                array("cluster" => "Unverified", "value" => $unverified),
            );
        }

        private function getDataForGraphBackup20230901($start, $end, $type, $company, $is_archived) {
            $queryAllVerified = "";
            $queryAllVerified .= "SELECT * FROM ( ";
            $queryAllVerified .= "(SELECT ";
            $queryAllVerified .= "	`inv`.*, ";
            $queryAllVerified .= "	cat.description category, ";
            $queryAllVerified .= "	vh.`name` asset_name, ";
            $queryAllVerified .= "	vh.`description` description, ";
            $queryAllVerified .= "	vh.`company_code` company_code, ";
            $queryAllVerified .= "	vh.gen_code asset_code, ";
            $queryAllVerified .= "	vh.datepurchased date_purchased, ";
            $queryAllVerified .= "	loc.location, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_date') AS datetime) AS inventory_date, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_by') AS integer) AS inventory_by, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'date_updated') AS datetime)AS date_updated, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'updated_by') AS integer) AS updated_by, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'remarks') AS remarks, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'status') AS status, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'inventory_status') AS inventory_status ";
            $queryAllVerified .= "FROM `gccasset`.`inventory_list` `inv` ";
            $queryAllVerified .= "INNER JOIN `gccasset`.`vehicles` vh ON `vh`.`id` = `inv`.`asset_id` AND inv.`asset_type`=2 ";
            $queryAllVerified .= "LEFT JOIN  gccasset.location loc ON loc.id = vh.area_id ";
            $queryAllVerified .= "LEFT JOIN gccasset.assetcategory cat ON cat.code = vh.asset_category ";
            if(isset($is_archived)): 
                $queryAllVerified .= "WHERE vh.`status2` NOT IN('operational', 'brandnew') AND vh.`status2` IS NOT NULL AND vh.`status2` != '' ";
                endif;
            $queryAllVerified .= ")UNION ";
            $queryAllVerified .= "(SELECT ";
            $queryAllVerified .= "	`inv`.*, ";
            $queryAllVerified .= "	cat.description category, ";
            $queryAllVerified .= "	assets.`name` asset_name, ";
            $queryAllVerified .= "	assets.`assetname` description, ";
            $queryAllVerified .= "	assets.`company_code` company_code, ";
            $queryAllVerified .= "	assets.gen_code asset_code, ";
            $queryAllVerified .= "	assets.datepurchased date_purchased, ";
            $queryAllVerified .= "	loc.location, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_date') AS datetime) AS inventory_date, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_by') AS integer) AS inventory_by, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'date_updated') AS datetime)AS date_updated, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'updated_by') AS integer) AS updated_by, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'remarks') AS remarks, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'status') AS status, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'inventory_status') AS inventory_status ";
            $queryAllVerified .= "FROM `gccasset`.`inventory_list` `inv` ";
            $queryAllVerified .= "INNER JOIN `gccasset`.`assets` assets ON `assets`.`id` = `inv`.`asset_id` AND inv.`asset_type`=1 ";
            $queryAllVerified .= "LEFT JOIN  gccasset.location loc ON loc.id = assets.area_id ";
            $queryAllVerified .= "LEFT JOIN gccasset.assetcategory cat ON cat.code = assets.asset_category ";
            if(isset($is_archived)): 
                $queryAllVerified .= "WHERE assets.`status` NOT IN('operational', 'brandnew') AND assets.`status` IS NOT NULL AND assets.`status` != '' ";
                endif;
            $queryAllVerified .= ")) AS tbl ";
            $queryAllVerified .= "WHERE DATE(tbl.inventory_date) >='" . $start->format('Y-m-d') . "' AND DATE(tbl.inventory_date) <='" . $end->format('Y-m-d') . "'";

            if ($company != 'all') {
                $queryAllVerified .= " AND tbl.company_code='$company'" . ($type != 3 ? " AND tbl.asset_type=$type" : "");
            } else {
                $queryAllVerified .= $type != 3 ? " AND tbl.asset_type=$type" : "";
            }

            $queryVerifiedResult = $this->db->query($queryAllVerified);
            $verified = $queryVerifiedResult->num_rows();

            $this->db->reset_query();

            $whereType = $type != 3 ? "tbl.`type`=$type AND" : "";
            $whereCompany = $company != "all" ? "tbl.company_code='$company' AND" : "";

            $sql_get_unverified = "SELECT COUNT(*) asset_count FROM (SELECT assets.id, assets.company_code, 1 AS `type`, `status` AS `status`, isJunk";
            $sql_get_unverified .= " FROM gccasset.assets assets UNION SELECT vehicles.id, vehicles.company_code, 2 AS `type`, `status2` AS `status`, isJunk";
            $sql_get_unverified .= " FROM gccasset.vehicles vehicles) AS tbl WHERE " . $whereCompany . " " . $whereType . " tbl.isJunk !=1 AND ";
            $sql_get_unverified .= " (tbl.`status` NOT IN('archived', 'lost', 'junk', 'tradein') OR tbl.`status` IS NULL)";
            $this->db->query($sql_get_unverified);

            $queryUnverified = $this->db->query($sql_get_unverified);
            $unverified = $queryUnverified->row("asset_count");

            return array(
                array("cluster" => "Verified", "value" => $verified),
                array("cluster" => "Unverified", "value" => $unverified),
            );
        }

        function getVerifiedInventoryList() {
            $resultSet = array(
                "data" => 0,
                "recordsFiltered" => 0,
                "recordsTotal" => 0
            );
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $tableConfigStd = $this->utilities->parseFormDataToObject($post);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            if (!empty($post->date)) {
                $date_array = explode(" - ", $post->date);
                $date_start = new DateTime($date_array[0]);
                $date_end = new DateTime($date_array[1]);
            }
            $type = $post->type;
            $company = $post->company;

            if (empty($post->date) || empty($type) || empty($company)) {
                return $resultSet;
            }

            $queryAllVerified = "";
            $queryAllVerified .= "SELECT * FROM ( ";
            $queryAllVerified .= "(SELECT ";
            $queryAllVerified .= "	`inv`.*, ";
            $queryAllVerified .= "	'v_table' table_name, ";
            $queryAllVerified .= "	UPPER(cat.description) category, ";
            $queryAllVerified .= "	UPPER(vh.`name`) asset_name, ";
            $queryAllVerified .= "	UPPER(vh.`description`) description, ";
            $queryAllVerified .= "	vh.`company_code` company_code, ";
            $queryAllVerified .= "	UPPER(vh.gen_code) asset_code, ";
            $queryAllVerified .= "	vh.datepurchased date_purchased, ";
            $queryAllVerified .= "	UPPER(loc.location) as location, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_date') AS date) AS inventory_date, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_by') AS decimal) AS inventory_by, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'date_updated') AS datetime)AS date_updated, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'updated_by') AS decimal) AS updated_by, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'remarks') AS remarks, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'status') AS status, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT UPPER(meta_value) ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'inventory_status') AS inventory_status, ";
            $queryAllVerified .= "                  UPPER(vh.status2) as temp_status ";
            $queryAllVerified .= "FROM `gccasset`.`inventory_list` `inv` ";
            $queryAllVerified .= "INNER JOIN `gccasset`.`vehicles` vh ON `vh`.`id` = `inv`.`asset_id` AND inv.`asset_type`=2 ";
            $queryAllVerified .= "LEFT JOIN  gccasset.location loc ON loc.id = vh.area_id ";
            $queryAllVerified .= "LEFT JOIN gccasset.assetcategory cat ON cat.code = vh.asset_category ";
            if(isset($post->is_archived)): 
                $queryAllVerified .= "WHERE vh.`status2` NOT IN('operational', 'brandnew') AND vh.`status2` IS NOT NULL AND vh.`status2` != '' ";
                endif;
            $queryAllVerified .= ")UNION ";
            $queryAllVerified .= "(SELECT ";
            $queryAllVerified .= "	`inv`.*, ";
            $queryAllVerified .= "	'a_table' table_name, ";
            $queryAllVerified .= "	UPPER(cat.description) category, ";
            $queryAllVerified .= "	UPPER(assets.`name`) asset_name, ";
            $queryAllVerified .= "	UPPER(assets.`assetname`) description, ";
            $queryAllVerified .= "	assets.`company_code` company_code, ";
            $queryAllVerified .= "	UPPER(assets.assetacode) asset_code, ";
            $queryAllVerified .= "	assets.datepurchased date_purchased, ";
            $queryAllVerified .= "	UPPER(loc.location) as location, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_date') AS date) AS inventory_date, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_by') AS decimal) AS inventory_by, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'date_updated') AS datetime)AS date_updated, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'updated_by') AS decimal) AS updated_by, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'remarks') AS remarks, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'status') AS status, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT UPPER(meta_value) ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'inventory_status') AS inventory_status, ";
            $queryAllVerified .= "                  UPPER(assets.status) as temp_status ";
            $queryAllVerified .= "FROM `gccasset`.`inventory_list` `inv` ";
            $queryAllVerified .= "INNER JOIN `gccasset`.`assets` assets ON `assets`.`id` = `inv`.`asset_id` AND inv.`asset_type`=1 ";
            $queryAllVerified .= "LEFT JOIN  gccasset.location loc ON loc.id = assets.area_id ";
            $queryAllVerified .= "LEFT JOIN gccasset.assetcategory cat ON cat.code = assets.asset_category ";
            if(isset($post->is_archived)): 
            $queryAllVerified .= "WHERE assets.`status` NOT IN('operational', 'brandnew') AND assets.`status` IS NOT NULL AND assets.`status` != '' ";
            endif;
            $queryAllVerified .= ")) AS tbl ";
            $queryAllVerified .= "WHERE DATE(tbl.inventory_date) >='" . $date_start->format('Y-m-d') . "' AND DATE(tbl.inventory_date) <='" . $date_end->format('Y-m-d') . "'";

            if ($company != 'all') {
                $queryAllVerified .= " AND tbl.company_code='$company'" . ($type != 3 ? " AND tbl.asset_type=$type" : "");
            } else {
                $queryAllVerified .= $type != 3 ? " AND tbl.asset_type=$type" : "";
            }

            $resultSet['recordsTotal'] = $this->getQueryCount($queryAllVerified);
            $resultSet['recordsFiltered'] = $this->getQueryCount($queryAllVerified);

            $queryAllVerified .= " ORDER BY $pageOptions->order_column $pageOptions->order_direction";

            if ($pageOptions->length > -1) {
                $queryAllVerified .= " LIMIT $pageOptions->start, $pageOptions->length";
            }

            $queryVerifiedResult = $this->db->query($queryAllVerified);
            $verified = $queryVerifiedResult->result();
            
            foreach ($verified as $row) {
                if($row->table_name == 'a_table'){
                    $asset_type = 0;
                }else{
                    $asset_type = 1;
                }
                $row->accounted_to = $this->getAccountability($row->asset_id, $type, $asset_type);
            }
            
            $resultSet['data'] = $verified;
            return $resultSet;
        }
        
        function getVerifiedInventoryListBackup20230901() {
            $resultSet = array(
                "data" => 0,
                "recordsFiltered" => 0,
                "recordsTotal" => 0
            );
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $tableConfigStd = $this->utilities->parseFormDataToObject($post);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            if (!empty($post->date)) {
                $date_array = explode(" - ", $post->date);
                $date_start = new DateTime($date_array[0]);
                $date_end = new DateTime($date_array[1]);
            }
            $type = $post->type;
            $company = $post->company;

            if (empty($post->date) || empty($type) || empty($company)) {
                return $resultSet;
            }

            $queryAllVerified = "";
            $queryAllVerified .= "SELECT * FROM ( ";
            $queryAllVerified .= "(SELECT ";
            $queryAllVerified .= "	`inv`.*, ";
            $queryAllVerified .= "	'v_table' table_name, ";
            $queryAllVerified .= "	UPPER(cat.description) category, ";
            $queryAllVerified .= "	UPPER(vh.`name`) asset_name, ";
            $queryAllVerified .= "	UPPER(vh.`description`) description, ";
            $queryAllVerified .= "	vh.`company_code` company_code, ";
            $queryAllVerified .= "	UPPER(vh.gen_code) asset_code, ";
            $queryAllVerified .= "	vh.datepurchased date_purchased, ";
            $queryAllVerified .= "	UPPER(loc.location) as location, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_date') AS date) AS inventory_date, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_by') AS integer) AS inventory_by, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'date_updated') AS datetime)AS date_updated, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'updated_by') AS integer) AS updated_by, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'remarks') AS remarks, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'status') AS status, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT UPPER(meta_value) ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'inventory_status') AS inventory_status, ";
            $queryAllVerified .= "                  UPPER(vh.status2) as temp_status ";
            $queryAllVerified .= "FROM `gccasset`.`inventory_list` `inv` ";
            $queryAllVerified .= "INNER JOIN `gccasset`.`vehicles` vh ON `vh`.`id` = `inv`.`asset_id` AND inv.`asset_type`=2 ";
            $queryAllVerified .= "LEFT JOIN  gccasset.location loc ON loc.id = vh.area_id ";
            $queryAllVerified .= "LEFT JOIN gccasset.assetcategory cat ON cat.code = vh.asset_category ";
            if(isset($post->is_archived)): 
                $queryAllVerified .= "WHERE vh.`status2` NOT IN('operational', 'brandnew') AND vh.`status2` IS NOT NULL AND vh.`status2` != '' ";
                endif;
            $queryAllVerified .= ")UNION ";
            $queryAllVerified .= "(SELECT ";
            $queryAllVerified .= "	`inv`.*, ";
            $queryAllVerified .= "	'a_table' table_name, ";
            $queryAllVerified .= "	UPPER(cat.description) category, ";
            $queryAllVerified .= "	UPPER(assets.`name`) asset_name, ";
            $queryAllVerified .= "	UPPER(assets.`assetname`) description, ";
            $queryAllVerified .= "	assets.`company_code` company_code, ";
            $queryAllVerified .= "	UPPER(assets.assetacode) asset_code, ";
            $queryAllVerified .= "	assets.datepurchased date_purchased, ";
            $queryAllVerified .= "	UPPER(loc.location) as location, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_date') AS date) AS inventory_date, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'inventory_by') AS integer) AS inventory_by, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'date_updated') AS datetime)AS date_updated, ";
            $queryAllVerified .= "           cast( ";
            $queryAllVerified .= "                 ( ";
            $queryAllVerified .= "                 SELECT meta_value ";
            $queryAllVerified .= "                 FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                 WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                 AND    meta.meta_field = 'updated_by') AS integer) AS updated_by, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'remarks') AS remarks, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT meta_value ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'status') AS status, ";
            $queryAllVerified .= "           ( ";
            $queryAllVerified .= "                  SELECT UPPER(meta_value) ";
            $queryAllVerified .= "                  FROM   gccasset.inventory_list_meta meta ";
            $queryAllVerified .= "                  WHERE  meta.meta_id = inv.id ";
            $queryAllVerified .= "                  AND    meta.meta_field = 'inventory_status') AS inventory_status, ";
            $queryAllVerified .= "                  UPPER(assets.status) as temp_status ";
            $queryAllVerified .= "FROM `gccasset`.`inventory_list` `inv` ";
            $queryAllVerified .= "INNER JOIN `gccasset`.`assets` assets ON `assets`.`id` = `inv`.`asset_id` AND inv.`asset_type`=1 ";
            $queryAllVerified .= "LEFT JOIN  gccasset.location loc ON loc.id = assets.area_id ";
            $queryAllVerified .= "LEFT JOIN gccasset.assetcategory cat ON cat.code = assets.asset_category ";
            if(isset($post->is_archived)): 
            $queryAllVerified .= "WHERE assets.`status` NOT IN('operational', 'brandnew') AND assets.`status` IS NOT NULL AND assets.`status` != '' ";
            endif;
            $queryAllVerified .= ")) AS tbl ";
            $queryAllVerified .= "WHERE DATE(tbl.inventory_date) >='" . $date_start->format('Y-m-d') . "' AND DATE(tbl.inventory_date) <='" . $date_end->format('Y-m-d') . "'";

            if ($company != 'all') {
                $queryAllVerified .= " AND tbl.company_code='$company'" . ($type != 3 ? " AND tbl.asset_type=$type" : "");
            } else {
                $queryAllVerified .= $type != 3 ? " AND tbl.asset_type=$type" : "";
            }

            $resultSet['recordsTotal'] = $this->getQueryCount($queryAllVerified);
            $resultSet['recordsFiltered'] = $this->getQueryCount($queryAllVerified);

            $queryAllVerified .= " ORDER BY $pageOptions->order_column $pageOptions->order_direction";

            if ($pageOptions->length > -1) {
                $queryAllVerified .= " LIMIT $pageOptions->start, $pageOptions->length";
            }

            $queryVerifiedResult = $this->db->query($queryAllVerified);
            $verified = $queryVerifiedResult->result();
            
            foreach ($verified as $row) {
                if($row->table_name == 'a_table'){
                    $asset_type = 0;
                }else{
                    $asset_type = 1;
                }
                $row->accounted_to = $this->getAccountability($row->asset_id, $type, $asset_type);
            }
            
            $resultSet['data'] = $verified;
            return $resultSet;
        }

        private function getQueryCount($query) {
            return $this->db->query($query)->num_rows();
        }

        private function getAccountability($asset_id, $type, $asset_type) {
            $select = "acct_body.asset_id, ";
            // $select .= "if(assets.`name` IS NULL OR assets.`name`='', assets." . ($type == 1 ? "assetname" : "description") . ", assets.`name`) ";
            $select .= " acct_body.is_returned, acct.issued_to, IF(acct.is_contract=1, contractors.contractor, ";
            $select .= "CONCAT(UPPER(employees.firstname), ' ', UPPER(employees.lastname))) issued_to";

            $asset_table = $asset_type == 0 ? "gccasset.assets" : "gccasset.vehicles";

            $this->db->select($select);
            $this->db->join("gcceforms.accountability acct", "acct_body.accountability_id = acct.id AND LCASE(acct_body.`type`)='" . ($asset_type == 0 ? "Asset" : "Vehicle") . "'", "INNER");
            $this->db->join("gcchris.tblcontractor contractors", "contractors.id = acct.issued_to", "LEFT");
            $this->db->join("gccmaster.tblemployees employees", "employees.id = acct.issued_to", "LEFT");
            $this->db->join(($asset_table . " assets"), "assets.id = acct_body.asset_id", "INNER");
            $this->db->where("acct_body.asset_id", $asset_id);
            $this->db->where("acct_body.is_returned", 0);
            $this->db->where("acct.`status`!=", "Cancelled");
            $accountability = $this->db->get("gcceforms.accountability_body acct_body ")->row("issued_to");

            return $accountability;
        }

        function inventoryFileupload($type) {
            $resultset = array();

            $files = (isset($_FILES["files"]) && $_FILES["files"]) ? $_FILES["files"] : false;
            $config = array();
            $config['upload_path'] = './uploads/files/ams_inventory';

            if (!file_exists($config['upload_path'])) {
                $mkdir = mkdir($config['upload_path'], 0775, true);
            }

            $config['allowed_types'] = 'txt';
            $config['max_size'] = 100000;
            $this->upload->initialize($config);

            $statuses = $this->db->group_by("code")->get("gccasset.status")->result();
            $this->db->reset_query();

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

                    $_data["url"] = base_url("uploads/files/ams_inventory/{$fileName}");

                    $xfile = file_get_contents("uploads/files/ams_inventory/{$fileName}", false);
                    $assetCode = explode("\n", $xfile);

                    $arrItems = array();
                    $arrKeys = array();
                    $dataCount = 0;
                    $_data["count"] = 0;
                    if ($assetCode) {
                        foreach ($assetCode as $code) {
                            $code = preg_replace('/\s+/', '', $code);
                            if ($code) {
                                if ($type == "assets") {
                                    $query = $this->db->get_where("gccasset.assets", array("assetacode" => $code));

                                    if ($query->num_rows() > 0) {
                                        $row = $query->row();
                                        $updatedBy = ($row->updatedBy) ? $row->updatedBy : "---";
                                        $assetName = ($row->name) ? $row->name : "No asset name";

                                        $htmlData = "";
                                        $htmlData .= "<div class='custom-content'>";
                                        $htmlData .= "<p class='m--font-bolder mb-1'>{$updatedBy}</p>";
                                        $htmlData .= "<p class='m--regular-font-size-sm1'>" . date('M d, Y h:i:s A', strtotime($row->dateUpdated)) . "</p>";
                                        $htmlData .= "</div>";
                                        $row->last_updated = $htmlData;

                                        $htmlDataDescription = "";
                                        $htmlDataDescription .= "<div class='custom-content'>";
                                        $htmlDataDescription .= "<p class='m--font-bolder mb-1'>{$assetName}</p>";
                                        $htmlDataDescription .= "<p class='m--regular-font-size-sm1'>{$row->assetname}</p>";
                                        $htmlDataDescription .= "</div>";

                                        $htmlAssetOption = "";
                                        $htmlAssetOption .= "<div class='position-relative' style='width: 100%;'><input type='hidden' name='id[]' value='" . $row->id . "'>";
                                        $htmlAssetOption .= "<select name='status[]' class='form-control' style='height: auto; width: auto;'>";
                                        $htmlAssetOption .= "<option value=''>Choose an option</option>";

                                        foreach ($statuses as $status) {
                                            $htmlAssetOption .= "<option value='" . $status->code . "'>" . $status->name . "</option>";
                                        }

                                        $htmlAssetOption .= "</select></div>";

                                        $row->n_description = $htmlDataDescription;
                                        $_status = $row->status ? $row->status : "---";
                                        $htmlStatus = "";
                                        $htmlStatus .= "<p>{$_status}</p>";
                                        $htmlStatus .= $htmlAssetOption;

                                        $row->current_status = $htmlStatus;

                                        $row->n_remarks = "<div class='position-relative d-flex' style='width: 100%; resize: none;'><textarea class='form-control' id='n_remarks' name='remarks[]' rows='5' placeholder='Enter Remarks Here'></textarea></div>";
                                        $arrItems["data"][$row->id] = $row;

                                        if (!in_array($row->id, $arrKeys)) {
                                            $arrKeys[] = $row->id;
                                        } else {
                                            $arrItems["duplicate_data"][] = $code;
                                        }

                                        $dataCount++;
                                    } else {
                                        $arrItems["no_data"][] = $code;
                                    }
                                }

                                if ($type == "vehicles") {
                                    $query = $this->db->get_where("gccasset.vehicles", array("gen_code" => $code));

                                    if ($query->num_rows() > 0) {
                                        $row = $query->row();
                                        $dataUser = $this->core_layout->getUserData($row->updated_by);
                                        $row->updatedBy = (isset($dataUser["display_name_1"]) && $dataUser["display_name_1"]) ? $dataUser["display_name_1"] : "";
                                        $row->dateUpdated = ($row->updated_at !== "0000-00-00 00:00:00" && $row->updated_at !== "") ? $row->updated_at : "";
                                        $row->assetname = ($row->description) ? $row->description : "";
                                        $row->assetacode = $row->gen_code;

                                        $updatedBy = ($row->updatedBy) ? $row->updatedBy : "---";
                                        $assetName = ($row->name) ? $row->name : "No asset name";

                                        $htmlData = "";
                                        $htmlData .= "<div class='custom-content'>";
                                        $htmlData .= "<p class='m--font-bolder mb-1'>{$updatedBy}</p>";
                                        $htmlData .= "<p class='m--regular-font-size-sm1'>" . date('M d, Y h:i:s A', strtotime($row->dateUpdated)) . "</p>";
                                        $htmlData .= "</div>";
                                        $row->last_updated = $htmlData;

                                        $htmlDataDescription = "";
                                        $htmlDataDescription .= "<div class='custom-content'>";
                                        $htmlDataDescription .= "<p class='m--font-bolder mb-1'>{$assetName}</p>";
                                        $htmlDataDescription .= "<p class='m--regular-font-size-sm1'>{$row->assetname}</p>";
                                        $htmlDataDescription .= "</div>";

                                        $htmlAssetOption = "";
                                        $htmlAssetOption .= "<div class='position-relative' style='width: 100%;'><input type='hidden' name='id[]' value='" . $row->id . "'>";
                                        $htmlAssetOption .= "<select name='status[]' class='form-control' style='height: auto; width: auto;'>";
                                        $htmlAssetOption .= "<option value=''>Choose an option</option>";

                                        foreach ($statuses as $status) {
                                            $htmlAssetOption .= "<option value='" . $status->code . "'>" . $status->name . "</option>";
                                        }

                                        $htmlAssetOption .= "</select></div>";

                                        $row->n_description = $htmlDataDescription;

                                        $_status = $row->status2 ? $row->status2 : "---";
                                        $htmlStatus = "";
                                        $htmlStatus .= "<p>{$_status}</p>";
                                        $htmlStatus .= $htmlAssetOption;

                                        $row->current_status = $htmlStatus;
                                        $row->n_remarks = "<div class='position-relative d-flex' style='width: 100%; resize: none;'><textarea class='form-control' id='n_remarks' name='remarks[]' rows='5' placeholder='Enter Remarks Here'></textarea></div>";

                                        $arrItems["data"][$row->id] = $row;

                                        if (!in_array($row->id, $arrKeys)) {
                                            $arrKeys[] = $row->id;
                                        } else {
                                            $arrItems["duplicate_data"][] = $code;
                                        }

                                        $dataCount++;
                                    } else {
                                        $arrItems["no_data"][] = $code;
                                    }
                                }
                            }
                        }
                    }

                    if ($dataCount > 0) {
                        $counter = (isset($arrItems["data"]) && $arrItems["data"]) ? count($arrItems["data"]) : 0;
                        $nodata_counter = (isset($arrItems["no_data"]) && $arrItems["no_data"]) ? count($arrItems["no_data"]) : 0;
                        $duplicate_counter = (isset($arrItems["duplicate_data"]) && $arrItems["duplicate_data"]) ? count($arrItems["duplicate_data"]) : 0;

                        $_data["count"] = $counter;
                        $_data["nodata_count"] = $nodata_counter;
                        $_data["duplicate_count"] = $duplicate_counter;

                        $resultset["items"] = $arrItems;
                        $resultset["files"][] = $_data;

                        $resultset["response"] = true;
                        $resultset["toastr_msg"] = "Textfile data has been generated.";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Invalid textfile data or no data found!";
                    }
                }
            }

            return $resultset;
        }

        protected function resizeImage($filename) {
            $source_path = './uploads/files/ams_inventory' . $filename;
            $target_path = './uploads/files/ams_inventory';
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

        function saveInventoryCheck($asset_type) {
            $user = $this->session->userdata()["logged_in"];
            $emp_id = $user["emp_id"];
            $post = $this->input->post();
            $assets = $post['assets'];
            $inventory_status = $post['inventory_status'];

            $this->db->trans_begin();

            foreach ($assets as $asset) {
                $inserted = $this->db->insert("gccasset.inventory_list", array("asset_id" => $asset["id"], "asset_type" => $asset_type));
                if ($inserted) {
                    $meta = array(
                        array("meta_id" => $this->db->insert_id(), "meta_field" => "inventory_date", "meta_value" => date('Y-m-d H:i:s')),
                        array("meta_id" => $this->db->insert_id(), "meta_field" => "inventory_by", "meta_value" => $emp_id),
                        array("meta_id" => $this->db->insert_id(), "meta_field" => "date_updated", "meta_value" => ""),
                        array("meta_id" => $this->db->insert_id(), "meta_field" => "updated_by", "meta_value" => ""),
                        array("meta_id" => $this->db->insert_id(), "meta_field" => "remarks", "meta_value" => $asset["remarks"]),
                        array("meta_id" => $this->db->insert_id(), "meta_field" => "status", "meta_value" => $asset["status"]),
                        array("meta_id" => $this->db->insert_id(), "meta_field" => "inventory_status", "meta_value" => $inventory_status),
                    );

                    $this->db->insert_batch("gccasset.inventory_list_meta", $meta);

                    if (intval($asset_type) === 1) {
                        $this->db->where("id", $asset["id"]);
                        $this->db->set("inventory_check_by", $emp_id);
                        $this->db->set("inventory_check_date", date('Y-m-d H:i:s'));
                        $this->db->set("status", $asset["status"]);
                        $this->db->set("remarks", "CONCAT(remarks, CASE  WHEN remarks IS NULL THEN '' WHEN remarks='' THEN '' ELSE '\n' END, '" . $asset["remarks"] . "')", FALSE);
                        $this->db->update("gccasset.assets");
                        $this->db->reset_query();
                    } else {
                        $this->db->where("id", $asset["id"]);
                        $this->db->set("inventory_check_by", $emp_id);
                        $this->db->set("inventory_check_date", date('Y-m-d H:i:s'));
                        $this->db->set("status2", $asset["status"]);
                        $this->db->set("status", "CONCAT(status, CASE  WHEN status IS NULL THEN '' WHEN status='' THEN '' ELSE '\n' END, '" . $asset["remarks"] . "')", FALSE);
                        $this->db->update("gccasset.vehicles");
                        $this->db->reset_query();
                    }
                }
            }

            $asset_ids = array_map(function ($asset) {
                return $asset["id"];
            }, $assets);

            $notification = strtoupper($user["firstname"] . " " . $user["lastname"])
                . " has made an inventory on " . (intval($asset_type) === 1 ? "Assets " : "Vehicles ")
                . " with a total count of " . sizeof($asset_ids) . " record(s).";
            $log = array("user_id" => $emp_id,
                "notification" => $notification,
                "count" => sizeof($asset_ids),
                "asset_type" => $asset_type,
                "asset_ids" => serialize($asset_ids));

            $this->db->insert("gccasset.inventory_logs", $log);

            $resultSet = array();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $resultSet["success"] = FALSE;
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "DB error occurred.";
            } else {
                $resultSet["success"] = TRUE;
                $resultSet["message"] = (intval($asset_type) === 1 ? "Asset" : "Vehicle") . " inventory was successfully saved.";
                $resultSet["title"] = "Inventory Record Saved.";

                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function inventoryLog() {
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $order_val = array(array("column" => "1", "dir" => "desc"));

            $post = $this->input->post();
          
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
          
            $rowData = $this->log_list($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->log_count($search);

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function log_list($search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $filterFields = array("a.id", "c.firstname", "c.middlename", "c.lastname", "c.suffix");
            $this->db->from("gccasset.inventory_logs a");
            $this->db->join("gccmaster.tblusers b", "b.id = a.user_id", "left");
            $this->db->join("gccmaster.tblemployees c", "b.emp_id = c.id", "left");
    
            if(isset($search)){
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

            $this->db->limit($limit, $offset);
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $rs->created_at = date("F j, Y g:i a", strtotime($rs->created_at));  
                    $arrData[$key] = $rs;
                }
                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
                if(!empty($search)){
                    $this->core_layout->setEventLog("User searched `".$search."` in Inventory logs datatable.","search", "success", "gccasset", "user");
                }
                return $data;
            } else {
                return array();
            }
        }

        private function log_count($search=null) {
            $filterFields = array("a.id", "c.firstname", "c.middlename", "c.lastname", "c.suffix");
            $this->db->from("gccasset.inventory_logs a");
            $this->db->join("gccmaster.tblusers b", "b.id = a.user_id", "left");
            $this->db->join("gccmaster.tblemployees c", "b.emp_id = c.id", "left");

            if(isset($search)){
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
            $count = $query->num_rows();
            return $count;
        }
    }