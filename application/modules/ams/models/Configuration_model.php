<?php
    defined("BASEPATH") or exit("No direct script access allowed.");

    class Configuration_model extends CI_Model
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->model("Utilities_model", "utilities");
        }

        /* load data to tables */
        public function getAssetCategory($all = 0)
        {
            $table = "gccasset.assetcategory";
            if ($all == 1) {
                $q = isset($_GET["q"]) ? $_GET["q"] : "";
                return $this->db
                    ->like("description", $q, "both")
                    ->or_like("code", $q, "both")
                    ->get($table)
                    ->result();
            } else {
                $tableConfig = $this->input->post();
                $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
                $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

                $criteria = "CONCAT(code, description) LIKE '%$pageOptions->search%'";

                if ($pageOptions->length > -1) {
                    $this->db->limit($pageOptions->length, $pageOptions->start);
                }

                $resultSet["data"] = $this->db
                    ->order_by($pageOptions->order_column, $pageOptions->order_direction)
                    ->where($criteria)
                    ->get($table)
                    ->result();
                $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $criteria);
                $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $criteria);
                $resultSet["pageOptions"] = $pageOptions;
                return $resultSet;
            }
        }

        public function getAssetSubCategory()
        {
            $table = "gccasset.asset_sub_cat as sub_cat";

            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $criteria = "CONCAT(cat.description, sub_cat.sub_cat_desc) LIKE '%$pageOptions->search%'";

            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $joinArr = array();
            $joinArr[0] = array(
                "table" => "gccasset.assetcategory as cat",
                "condition" => "cat.code = sub_cat.cat_id",
                "option" => "inner"
            );

            if ($joinArr) {
                foreach ($joinArr as $_join)
                    $this->db->join($_join["table"], $_join["condition"], $_join["option"]);
            }

            $resultSet["data"] = $this->db
                ->order_by($pageOptions->order_column, $pageOptions->order_direction)
                ->where($criteria)
                ->get($table)
                ->result();

            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $criteria, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $criteria, $joinArr);
            $resultSet["pageOptions"] = $pageOptions;
            return $resultSet;
        }

        public function getStations()
        {
            $table = "gccasset.station";

            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $criteria = "station LIKE '%$pageOptions->search%'";

            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $resultSet["data"] = $this->db
                ->order_by($pageOptions->order_column, $pageOptions->order_direction)
                ->where($criteria)
                ->get($table)
                ->result();
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $criteria);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $criteria);
            $resultSet["pageOptions"] = $pageOptions;
            return $resultSet;
        }

        public function getLocations()
        {
            $table = "gccasset.location";

            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $criteria = "location LIKE '%$pageOptions->search%'";

            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $resultSet["data"] = $this->db
                ->order_by($pageOptions->order_column, $pageOptions->order_direction)
                ->where($criteria)
                ->get($table)
                ->result();
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $criteria);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $criteria);
            $resultSet["pageOptions"] = $pageOptions;
            return $resultSet;
        }

        public function getEquipmentTypes()
        {
            $table = "gccasset.equip_type eq_type";

            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $select = "eq_cat.description equipment_category, eq_type.*";
            $criteria = "CONCAT(eq_type.description, eq_type.code, eq_cat.description) LIKE '%$pageOptions->search%'";

            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $joinArr = array();
            $joinArr[0] = array(
                "table" => "gccasset.equipmentcategory eq_cat",
                "condition" => "eq_type.ec_id = eq_cat.id",
                "option" => "inner"
            );

            if ($joinArr) {
                foreach ($joinArr as $_join)
                    $this->db->join($_join["table"], $_join["condition"], $_join["option"]);
            }

            $resultSet["data"] = $this->db
                ->select($select)
                ->order_by($pageOptions->order_column, $pageOptions->order_direction)
                ->where($criteria)
                ->get($table)
                ->result();

            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $criteria, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $criteria, $joinArr);
            $resultSet["pageOptions"] = $pageOptions;
            return $resultSet;
        }

        public function getEquipmentCategories($all)
        {
            if ($all = 1) {
                $q = isset($_GET["q"]) ? $_GET["q"] : "";
                return $this->db
                    ->like("description", $q, "both")
                    ->or_like("code", $q, "both")
                    ->get("gccasset.equipmentcategory")->result();
            }
        }
        /* end load data to tables */

        // get a single category using id
        public function getAssetCategoryDetails($id)
        {
            return array(
                "data" => $this->db
                    ->get_where("gccasset.assetcategory", array("id" => $id))
                    ->row());
        }
        /* end load data to tables */

        /* get single sub category using id */
        public function getAssetSubCategoryDetails($id)
        {
            $data = $this->db->select("*")
                ->where("sub_cat.sub_cat_id", $id)
                ->join("gccasset.assetcategory as cat", "cat.code = sub_cat.cat_id", "inner")
                ->get("gccasset.asset_sub_cat as sub_cat")
                ->row();

            return array(
                "data" => $data
            );
        }
        /* end get single sub category using id */

        /* get station using id */
        function getStationDetails($id)
        {
            $data = $this->db
                ->get_where("gccasset.station", array("id" => $id))
                ->row();
            return array("data" => $data);
        }
        /* end get station using id */

        /* get station using id */
        function getLocationDetails($id)
        {
            $data = $this->db
                ->get_where("gccasset.location", array("id" => $id))
                ->row();
            return array("data" => $data);
        }
        /* end get station using id */

        /* get equipment type using id */
        function getEquipmentTypeDetails($id)
        {
            $data = $this->db
                ->select("eq_cat.description as equipment_category, eq_type.*")
                ->join("gccasset.equipmentcategory as eq_cat", "eq_cat.id = eq_type.ec_id", "inner")
                ->get_where("gccasset.equip_type as eq_type", array("eq_type.id" => $id))
                ->row();
            return array("data" => $data);
        }
        /* end get equipment type using id */

        /* SAVE,EDIT,DELETE ASSET CATEGORY FUNCTIONS */
        public function saveNewAssetCategory()
        {
            $data = array();
            $field = array("code" => $this->input->post("code"), "description" => $this->input->post("description"));
            if ($this->db->insert("gccasset.assetcategory", $field)) {
                $data["success"] = true;
                $data["message"] = "New asset category was saved.";
                $data["data"] = $this->input->post("code");
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }

        public function updateAssetCategory()
        {
            $data = array();
            $id = $this->input->post("id");
            $field = array("description" => $this->input->post("description"));
            $this->db->where("id", $id);
            if ($this->db->update("gccasset.assetcategory", $field)) {
                $data["success"] = true;
                $data["message"] = "New asset category was saved.";
                $data["data"] = $this->input->post("description");
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }

        public function deleteAssetCategory()
        {
            $id = $this->input->get("id");
            $data = array();
            if ($this->db->delete("gccasset.assetcategory", array("id" => $id))) {
                $data["success"] = true;
                $data["message"] = "Category was removed.";
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }
        /* END SAVE,EDIT,DELETE ASSET CATEGORY FUNCTIONS */

        /* SAVE,EDIT,DELETE ASSET SUB CATEGORY FUNCTIONS */
        function saveNewAssetSubCategory()
        {
            $cat_id = $this->input->post("category"); // cat_id in field but code in assetcategory table
            $description = $this->input->post("description");
            $code = $this->input->post("code");

            $field = array(
                'cat_id' => $cat_id,
                'sub_cat_desc' => $description,
                'sub_cat_code' => $code
            );

            if ($this->db->insert("gccasset.asset_sub_cat", $field)) {
                $data["success"] = true;
                $data["message"] = "New asset sub category was saved.";
                $data["data"] = $description;
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }

        function updateAssetSubCategory()
        {
            $sub_cat_id = $this->input->post("sub_cat_id");
            $fields = array(
                'cat_id' => $this->input->post("category"),
                'sub_cat_desc' => $this->input->post("description"),
                'sub_cat_code' => $this->input->post("code")
            );

            $data = array();

            $this->db->where("sub_cat_id", $sub_cat_id);
            if ($this->db->update("gccasset.asset_sub_cat", $fields)) {
                $data["success"] = true;
                $data["message"] = "Asset sub category was updated.";
                $data["data"] = $fields["sub_cat_desc"];
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }

        function deleteAssetSubCategory()
        {
            $id = $this->input->get("id");
            $data = array();
            if ($this->db->delete("gccasset.asset_sub_cat", array("sub_cat_id" => $id))) {
                $data["success"] = true;
                $data["message"] = "Asset sub category was removed.";
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }
        /* END SAVE,EDIT,DELETE ASSET SUB CATEGORY FUNCTIONS */

        /*  SAVE,EDIT,DELETE STATION */
        function saveNewStation()
        {
            $station = $this->input->post("station");
            if ($this->db->insert("gccasset.station", array("station" => $station))) {
                $data["success"] = true;
                $data["message"] = "New station was saved.";
                $data["data"] = $station;
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }

        function updateStation()
        {
            $id = $this->input->post("id");
            $station = $this->input->post("station");
            if ($this->db->update("gccasset.station", array("station" => $station), array("id" => $id))) {
                $data["success"] = true;
                $data["message"] = "Station was updated.";
                $data["data"] = $station;
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }

        function deleteStation()
        {
            $id = $this->input->get("id");
            if ($this->db->delete("gccasset.station", array("id" => $id))) {
                $data["success"] = true;
                $data["message"] = "Station was deleted.";
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }
        /*  END SAVE,EDIT,DELETE STATION */

        /* SAVE,EDIT,DELETE STATION */
        function saveNewLocation()
        {
            $station = $this->input->post("location");
            if ($this->db->insert("gccasset.location", array("location" => $station))) {
                $data["success"] = true;
                $data["message"] = "New location was saved.";
                $data["data"] = $station;
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }

        function updateLocation()
        {
            $id = $this->input->post("id");
            $station = $this->input->post("location");
            if ($this->db->update("gccasset.location", array("location" => $station), array("id" => $id))) {
                $data["success"] = true;
                $data["message"] = "Location was updated.";
                $data["data"] = $station;
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }

        function deleteLocation()
        {
            $id = $this->input->get("id");
            if ($this->db->delete("gccasset.location", array("id" => $id))) {
                $data["success"] = true;
                $data["message"] = "Location was deleted.";
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }
        /* END SAVE,EDIT,DELETE STATION*/

        /* SAVE,EDIT,DELETE STATION */
        function saveNeqEquipmentType()
        {
            $description = $this->input->post("description");
            $equipment_category = $this->input->post("equipment_category");
            $code = $this->input->post("code");

            $fields = array(
                "ec_id" => $equipment_category,
                "description" => $description,
                "code" => $code,
            );

            if ($this->db->insert("gccasset.equip_type", $fields)) {
                $data["success"] = true;
                $data["message"] = "New equipment type was saved.";
                $data["data"] = $description;
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }

        function updateEquipmentType()
        {
            $description = $this->input->post("description");
            $equipment_category = $this->input->post("equipment_category");
            $code = $this->input->post("code");
            $id = $this->input->post("id");

            $fields = array(
                "ec_id" => $equipment_category,
                "description" => $description,
                "code" => $code,
            );

            $this->db->where("id", $id);
            if ($this->db->update("gccasset.equip_type", $fields)) {
                $data["success"] = true;
                $data["message"] = "Equipment type was updated.";
                $data["data"] = $description;
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }

        function deleteEquipmentType()
        {
            $id = $this->input->get("id");
            if ($this->db->delete("gccasset.equip_type", array("id" => $id))) {
                $data["success"] = true;
                $data["message"] = "Equipment type was deleted.";
            } else {
                $data["success"] = false;
                $data["message"] = $this->db->_error_message();
            }

            return $data;
        }
        /* END SAVE,EDIT,DELETE STATION*/
    }