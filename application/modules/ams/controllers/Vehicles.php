<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Vehicles extends MY_Controller {
        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("ams");
            $this->authenticate->doRedirect();
            $this->load->model("Vehicles_model", "vehicles");
            $this->load->model("core/Upload_model", "file_upload");
        }

        public function vehicle_masterfile() {
            $this->core_layout->setPrivilegeName("vehicles_masterfile");
            $this->core_layout->addJs("js/ams/vehicle_masterfile.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $this->load->view('core/templates/header');
            $this->load->view('vehicles/vehicle_masterfile');
            $this->load->view('core/templates/footer');
        }

        public function vehicle_components_masterfile() {
            $this->core_layout->setPrivilegeName("ams_vehicle_components_masterfile");
            $this->core_layout->addJs("js/ams/vehicle_components_masterfile.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $this->load->view('core/templates/header');
            $this->load->view('vehicles/vehicle_components_masterfile');
            $this->load->view('core/templates/footer');
        }

        public function new_vehicle() {
            $this->core_layout->setPrivilegeName("vehicles_masterfile");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
            $this->core_layout->addJs("js/ams/new_vehicle.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $this->load->view('core/templates/header');
            $this->load->view('vehicles/new_vehicle');
            $this->load->view('core/templates/footer');
        }

        public function new_vehicle_component() {
            $this->core_layout->setPrivilegeName("ams_vehicle_components_masterfile");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
            $this->core_layout->addJs("js/ams/new_vehicle_component.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $this->load->view('core/templates/header');
            $this->load->view('vehicles/new_vehicle_component');
            $this->load->view('core/templates/footer');
        }

        public function new_vehicle_bak() {
            $this->core_layout->addJs("js/ams/new_vehicle_bak.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('vehicles/new_vehicle_bak');
            $this->load->view('core/templates/footer');
        }

        public function edit_vehicle($id = null) {
            $this->core_layout->setPrivilegeName("vehicles_masterfile");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
            $this->core_layout->addJs("js/ams/edit_vehicle.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $data = array();
            if ($id) {
                $data['rs'] = $this->vehicles->getVehicleDetails($id);
                $data['images'] = $this->vehicles->getVehicleImages($id);
            }

            $this->load->view('core/templates/header');
            $this->load->view('vehicles/edit_vehicle', $data);
            $this->load->view('core/templates/footer');
        }

        public function edit_vehicle_component($id = null) {
            $this->core_layout->setPrivilegeName("ams_vehicle_components_masterfile");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
            $this->core_layout->addJs("js/ams/edit_vehicle_component.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $data = array();
            if ($id) {
                $data['rs'] = $this->vehicles->getVehicleDetails($id);
                $data['images'] = $this->vehicles->getVehicleImages($id);
            }

            $this->load->view('core/templates/header');
            $this->load->view('vehicles/edit_vehicle_component', $data);
            $this->load->view('core/templates/footer');
        }

        function get_vehicle_collection($export = 0) {
            $data = $this->vehicles->getVehicleCollection($export);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_vehicles_accountability_borrowing($asset_id = null){
            
            $data = $this->vehicles->getVehicleComponentAccountabilityBorrowing($asset_id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_vehicle_components_collection($excludeHasMother = 0, $export = 0) {
            $data = $this->vehicles->getVehicleComponentsCollection($excludeHasMother, $export);
            echo json_encode($data);
        }

        function get_equipment_category_collection() {
            $data = $this->vehicles->getEquipmentCategoryCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function process_new() {
            $data = $this->vehicles->processNew();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function type_lookup() {
            $data = $this->vehicles->getTypeLookup();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        /* dynamic opening of modal form */
        function open_modal() {
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

        function pass_data_to_dialog($data) {
            return $data;
        }

        function get_costing_item($data) {
            $result = $this->vehicles->getCostingItem($data['id']);
            return $result;
        }

        function get_maintenance_item($data) {
            $result = $this->vehicles->getMaintenanceItem($data['id']);
            return $result;
        }

        function get_document_item($data) {
            $result = $this->vehicles->getDocumentItem($data['id']);
            return $result;
        }
        /* dynamic opening of modal form */

        /* LOAD VIEW OF VEHICLES WITH STATUS e,g JUNK, archived, lost, tradein */
        function archive() {
            $this->core_layout->setPrivilegeName("ams_archives_vehicles");
            $_type = isset($_GET["type"]) ? $_GET["type"] : "mother";
            $data = array();

            if ($_type === "mother") {
                $data['title'] = "Archived Vehicles";
            } elseif ($_type === "component") {
                $data['title'] = "Archived Vehicle Components";
            } else {
                $data['title'] = "Archived Vehicles & Components";
            }

            $data['type'] = $_type;
            $this->core_layout->addCss("css/ams/ams_styles.css", true);
            $this->core_layout->addJs("js/ams/archived_vehicles.js", true);

            $this->load->view('core/templates/header');
            $this->load->view("archives/archived_vehicles", $data);
            $this->load->view('core/templates/footer');
        }

        function get_archive_collection() {
            $type = isset($_GET['type']) ? $_GET['type'] : 'mother';
            echo json_encode($this->vehicles->getArchiveCollection($type));
        }
        /* END LOAD VIEW OF VEHICLES WITH STATUS e,g JUNK, archived, lost, tradein */

        /* ARCHIVE VEHICLE */
        function archive_vehicle($isComponent) {
            $data = $this->vehicles->archiveVehicle($isComponent);
            echo json_encode($data);
        }

        /* END ARCHIVE */

        function restore_archived_vehicle($id = null) {
            $data = $this->vehicles->restoreArchivedVehicle($id);
            echo json_encode($data);
        }

        function restore_archived_vehicles_multiple() {
            $data = $this->vehicles->restoreArchivedVehiclesMultiple();
            echo json_encode($data);
        }

        function delete_vehicles($id = null) {
            $data = $this->vehicles->deleteVehicle($id);
            echo json_encode($data);
        }

        function delete_archived_vehicles_multiple() {
            $data = $this->vehicles->deleteArchivedVehiclesMultiple();
            echo json_encode($data);
        }

        function get_sub_category_collection() {
            $cat_id = isset($_GET['cat_id']) ? $_GET['cat_id'] : "";
            $data = $this->vehicles->getSubCategoryCollection($cat_id);
            echo json_encode($data);
        }

        function get_location_collection() {
            $data = $this->vehicles->getLocationCollection();
            echo json_encode($data);
        }

        function get_company_collection() {
            $data = $this->vehicles->getCompanyCollection();
            echo json_encode($data);
        }

        function get_status_collection() {
            $data = $this->vehicles->getStatusCollection();
            echo json_encode($data);
        }

        function save_new_vehicle($isCompo = 0, $motherId = null) {
            $data = $this->vehicles->saveNewVehicle($isCompo, $motherId);
            echo json_encode($data);
        }

        function update_vehicle($isCompo = 0) {
            $data = $this->vehicles->updateVehicle($isCompo);
            echo json_encode($data);
        }

        function generate_asset_code($isCompo = 0) {
            $data = $this->vehicles->generateAssetCode($isCompo);
            echo json_encode($data);
        }

        function get_primary_pic($id) {
            $this->db->where("id", $id);
            $this->db->select("primary_pic");
            $_data = $this->db->get("gccasset.vehicles")->row();
            $filename = $_data->primary_pic;
            $resultSet = array(
                "primary_pic" => ""
            );

            if ($filename) {
                $path = "uploads/files/images/vehicles/" . $id . "/" . $filename;
                if (file_exists($path)) {
                    $resultSet['primary_pic'] = $filename;
                }
            }

            echo json_encode($resultSet);
        }

        function load_view_tab() {
            $view = isset($_GET['view']) ? $_GET['view'] : "";
            $data = $this->load->view("vehicles/tabs/" . $view, '', TRUE);
            echo json_encode(array('view' => $data));
        }

        /**
         * MOST $id parameters are id from vehicles table
         * */

        function get_vehicle_components($id = null) {
            $data = $this->vehicles->getVehicleComponents($id);

            /*foreach ($data["data"] as $datum) {
                $str = explode("-", $datum->asset_code);
                echo end($str);
                // echo $datum->asset_code . "\n";
            }*/

            /*usort($data, function ($a, $b) {
                $a_end = end(explode("-", $a->asset_code));
                $b_end = end(explode("-", $b->asset_code));
                return ($a_end < $b_end) ? -1 : 1;
            });*/
             echo json_encode($data);
        }

        function exclude_component($id) {
            $data = $this->vehicles->excludeComponent($id);
            echo json_encode($data);
        }

        function remove_vehicle_component($id) {
            $data = $this->vehicles->removeVehicleComponent($id);
            echo json_encode($data);
        }

        function save_component_to_vehicle() {
            $data = $this->vehicles->saveComponentToVehicle();
            echo json_encode($data);
        }

        function get_costing($id = null) {
            $data = $this->vehicles->getCosting($id);
            echo json_encode($data);
        }

        function save_costing() {
            $data = $this->vehicles->saveCosting();
            echo json_encode($data);
        }

        function remove_vehicle_cost() {
            $id = isset($_GET['id']) ? $_GET['id'] : "";
            $data = $this->vehicles->removeVehicleCost($id);
            echo json_encode($data);
        }

        function edit_costing() {
            $data = $this->vehicles->editCosting();
            echo json_encode($data);
        }

        function get_vehicle_documents($id = null) {
            $data = $this->vehicles->getVehicleDocuments($id);
            echo json_encode($data);
        }

        function save_document($id = null) {
            $data = $this->vehicles->saveDocument($id);
            echo json_encode($data);
        }

        function remove_document() {
            $id = isset($_GET['id']) ? $_GET['id'] : "";
            $data = $this->vehicles->removeDocument($id);
            echo json_encode($data);
        }

        function update_document() {
            $data = $this->vehicles->updateDocument();
            echo json_encode($data);
        }

        function get_vehicle_accountability($id = null) {
            $data = $this->vehicles->getVehicleAccountability($id);
            echo json_encode($data);
        }

        function get_vehicle_component_borrowing($id = null) {
            $data = $this->vehicles->getVehicleComponentBorrowing($id);
            echo json_encode($data);
        }

        function get_vehicle_maintenance_log($id = null) {
            $data = $this->vehicles->getVehicleMaintenanceLog($id);
            echo json_encode($data);
        }

        function get_preventive_temp_head_collection() {
            $data = $this->vehicles->getPreventiveTempHeadCollection();
            echo json_encode($data);
        }

        function load_preventive_template_body($pth_id = null) {
            $data = $this->vehicles->loadPreventiveTemplateBody($pth_id);
            echo json_encode($data);
        }

        function save_maintenance_template_log() {
            $data = $this->vehicles->saveMaintenanceTemplateLog();
            echo json_encode($data);
        }

        function remove_maintenance_item() {
            $id = isset($_GET['id']) ? $_GET['id'] : "";
            $vh_id = isset($_GET['vh_id']) ? $_GET['vh_id'] : "";
            $all = isset($_GET['all']) ? $_GET['all'] : "";

            $data = $this->vehicles->removeMaintenanceItem($id, $vh_id, $all);
            echo json_encode($data);
        }

        function update_maintenance_log() {
            $data = $this->vehicles->updateMaintenanceLog();
            echo json_encode($data);
        }

        function re_include_component($id = null) {
            $data = $this->vehicles->reIncludeComponent($id);
            echo json_encode($data);
        }

        function check_if_asset_is_borrowed_or_accounted() {
            $data = $this->vehicles->checkIfAssetIsBorrowedOrAccounted();
            echo json_encode($data);
        }

        function relocate_vehicle_images() {
            $data = $this->vehicles->relocateVehicleImages();
            echo json_encode($data);
        }

        function relocate_vehicle_documents() {
            $data = $this->vehicles->relocateVehicleDocuments();
            echo json_encode($data);
        }

        function get_employee_name() {
            $data = $this->vehicles->getEmployeeName();
            echo json_encode($data);
        }

        function get_location() {
            $data = $this->vehicles->getLocation();
            echo json_encode($data);
        }

        function get_category() {
            $data = $this->vehicles->getCategory();
            echo json_encode($data);
        }

        function view_vehicle($id, $status) {
            $this->core_layout->setPrivilegeName("ams_archives_vehicles");
            $this->core_layout->addJs("js/ams/view_vehicles.script.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $data = array();
            if ($id) {
                $data['rs'] = $this->vehicles->getVehicleDetails($id);
                $data['images'] = $this->vehicles->getVehicleImages($id);
                $data['title'] = intval($status) === 0 ? 'VIEW VEHICLE' : 'VIEW VEHICLE COMPONENT';
                $data['status'] = $status;
                $data["id"] = $id;
                $data["sub_category"] = $this->db->get_where("gccasset.asset_sub_cat", array("sub_cat_id" => $data['rs']->sub_cat_code))->row("sub_cat_desc");
                $data["statuses"] = $this->db->group_by("code")->get("gccasset.status")->result();

                $status_name = $this->db->get_where("gccasset.status", array("code" => $data['rs']->status2))->row("name");
                $data["status_name"] = !empty($status) ? $status_name : $data['rs']->status2;
            }

            /*foreach ($data['rs'] as $key => $value) {
                echo $key . " => " . $value;
                echo "<br/>";
            }
            die();*/

            $this->load->view('core/templates/header');
            $this->load->view('ams/archives/viewing/vehicles', $data, FALSE);
            $this->load->view('core/templates/footer');
        }

        function inventory_check() {
            $data = $this->vehicles->inventoryCheck();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
    }