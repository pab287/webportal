<?php
    defined("BASEPATH") or exit("No direct script access allowed.");

    class Configuration extends MY_Controller
    {
        function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("ams");
            $this->authenticate->doRedirect();

            $this->load->model("Configuration_model", "asset_config");
        }

        /*  load view functions */
        function asset_category()
        {
            $this->core_layout->addJs("js/ams/category.js", true);

            $this->load->view('core/templates/header');
            $this->load->view("configuration/asset_category");
            $this->load->view('core/templates/footer');
        }

        function asset_sub_category()
        {
            $this->core_layout->addJs("js/ams/sub_category.js", true);

            $this->load->view('core/templates/header');
            $this->load->view("configuration/asset_sub_category");
            $this->load->view('core/templates/footer');
        }

        function station()
        {
            $this->core_layout->addJs("js/ams/station.js", true);

            $this->load->view('core/templates/header');
            $this->load->view("configuration/station");
            $this->load->view('core/templates/footer');
        }

        function location()
        {
            $this->core_layout->addJs("js/ams/location.js", true);

            $this->load->view('core/templates/header');
            $this->load->view("configuration/location");
            $this->load->view('core/templates/footer');
        }

        public function equipment_type()
        {
            $this->core_layout->addJs("js/ams/equipment_type.js", true);

            $this->load->view('core/templates/header');
            $this->load->view("configuration/equipment_type");
            $this->load->view('core/templates/footer');
        }
        /* end load functions */

        /* load data to tables */
        public function get_asset_category($all = 0)
        {
            $result = $this->asset_config->getAssetCategory($all);
            echo json_encode($result);
        }

        function get_asset_sub_category()
        {
            $result = $this->asset_config->getAssetSubCategory();
            echo json_encode($result);
        }

        function get_stations()
        {
            $result = $this->asset_config->getStations();
            echo json_encode($result);
        }

        function get_locations()
        {
            $result = $this->asset_config->getLocations();
            echo json_encode($result);
        }

        function get_equipment_types()
        {
            $result = $this->asset_config->getEquipmentTypes();
            echo json_encode($result);
        }

        function get_equipment_categories($all = 0)
        {
            $result = $this->asset_config->getEquipmentCategories($all);
            echo json_encode($result);
        }
        /* end load data to tables*/

        /* dynamic opening of modal form */
        function open_modal()
        {
            $formData = $this->input->post('formData'); // get data from request either json or string, its up to you how you handle the data in the function
            $path = $this->input->post("path"); // get the view path form request
            $function_name = $this->input->post("function_name"); // get the function name from request

            /* code below description:
             * using the $function_name variable php will try to look for the function with value inside
             * $function_name(e.g. get_asset_document_details) variable and return the data
             * */
            $get_data = null;

            if ($function_name) {
                $get_data = $this->$function_name($formData);
            }

            echo $this->load->view($path, $get_data, TRUE);
        }

        function pass_data_to_dialog($data)
        {
            return $data;
        }

        function get_asset_category_row($data)
        {
            $id = $data['id'];
            return $this->asset_config->getAssetCategoryDetails($id);
        }

        function get_asset_sub_category_row($data)
        {
            $id = $data['id'];
            return $this->asset_config->getAssetSubCategoryDetails($id);
        }

        function get_station_row($data)
        {
            $id = $data['id'];
            return $this->asset_config->getStationDetails($id);
        }

        function get_location_row($data)
        {
            $id = $data['id'];
            return $this->asset_config->getLocationDetails($id);
        }

        function get_equipment_type_row($data)
        {
            $id = $data['id'];
            return $this->asset_config->getEquipmentTypeDetails($id);
        }
        /* dynamic opening of modal form */

        /* SAVE,EDIT,DELETE FUNCTIONS FOR ASSET CATEGORY */
        function save_new_asset_category()
        {
            $result = $this->asset_config->saveNewAssetCategory();
            echo json_encode($result);
        }

        public function update_asset_category()
        {
            $result = $this->asset_config->updateAssetCategory();
            echo json_encode($result);
        }

        function delete_asset_category()
        {
            $result = $this->asset_config->deleteAssetCategory();
            echo json_encode($result);
        }
        /* END SAVE,EDIT,DELETE FUNCTIONS FOR ASSET CATEGORY */

        /* SAVE, EDIT, DELETE FUNCTIONS FOR ASSET SUB CATEGORY */
        function save_new_asset_sub_category()
        {
            $result = $this->asset_config->saveNewAssetSubCategory();
            echo json_encode($result);
        }

        function update_asset_sub_category()
        {
            $result = $this->asset_config->updateAssetSubCategory();
            echo json_encode($result);
        }

        function delete_asset_sub_category()
        {
            $result = $this->asset_config->deleteAssetSubCategory();
            echo json_encode($result);
        }
        /* END SAVE, EDIT, DELETE FUNCTIONS FOR ASSET SUB CATEGORY*/

        /* SAVE, EDIT, DELETE FUNCTIONS FOR STATIONY */
        function save_new_station()
        {
            $result = $this->asset_config->saveNewStation();
            echo json_encode($result);
        }

        function update_station()
        {
            $result = $this->asset_config->updateStation();
            echo json_encode($result);
        }

        function delete_station()
        {
            $result = $this->asset_config->deleteStation();
            echo json_encode($result);
        }
        /* END SAVE, EDIT, DELETE FUNCTIONS FOR STATIONY*/

        /* SAVE, EDIT, DELETE FUNCTIONS FOR LOCATION */
        function save_new_location()
        {
            $result = $this->asset_config->saveNewLocation();
            echo json_encode($result);
        }

        function update_location()
        {
            $result = $this->asset_config->updateLocation();
            echo json_encode($result);
        }

        function delete_location()
        {
            $result = $this->asset_config->deleteLocation();
            echo json_encode($result);
        }
        /* SAVE, EDIT, DELETE FUNCTIONS FOR LOCATION */

        /* SAVE, EDIT, DELETE FUNCTIONS FOR EQUIPMENT TYPE */
        function save_new_equipment_type()
        {
            $result = $this->asset_config->saveNeqEquipmentType();
            echo json_encode($result);
        }

        function update_equipment_type() {
            $result = $this->asset_config->updateEquipmentType();
            echo json_encode($result);
        }

        function delete_equipment_type() {
            $result = $this->asset_config->deleteEquipmentType();
            echo json_encode($result);
        }
        /* END SAVE, EDIT, DELETE FUNCTIONS FOR EQUIPMENT TYPE */
    }