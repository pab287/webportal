<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Assets extends MY_Controller {
        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("ams");
            $this->authenticate->doRedirect();
            $this->load->model("Assets_model", "assets");
        }

        public function fixed_masterfile() {
            $this->core_layout->setPrivilegeName("fixed_masterfile");
            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
            $this->core_layout->addJs("js/ams/fixed_masterfile.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $this->load->view('core/templates/header');
            $this->load->view('assets/fixed_masterfile');
            $this->load->view('core/templates/footer');
        }

        public function asset_components_masterfile() {
            $this->core_layout->setPrivilegeName("asset_components_masterfile");
            $this->core_layout->addJs("js/ams/asset_components_masterfile.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $this->load->view('core/templates/header');
            $this->load->view('assets/asset_components_masterfile');
            $this->load->view('core/templates/footer');
        }

        public function status() {
            $this->core_layout->addJs("js/ams/status_masterfile.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('assets/status/index');
            $this->load->view('core/templates/footer');
        }

        function get_datatable_status_collection() {
            $data = $this->assets->getDataTableStatusCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function status_new() {
            $data = $this->assets->insertStatus();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_datatable_request($export = 0) {
            $data = $this->assets->getDatatableRequest($export);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_components_masterfile_list($export = 0) {
            $data = $this->assets->getComponentsMasterfileList($export);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function new_fixed_asset() {
            $this->core_layout->setPrivilegeName("fixed_masterfile");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
            $this->core_layout->addJs("js/ams/new_fixed_asset.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $this->load->view('core/templates/header');
            $this->load->view('assets/new_fixed_asset');
            $this->load->view('core/templates/footer');
        }

        function new_asset_component() {
            $this->core_layout->setPrivilegeName("asset_components_masterfile");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
            $this->core_layout->addJs("js/ams/new_asset_component.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $this->load->view('core/templates/header');
            $this->load->view('assets/new_asset_component');
            $this->load->view('core/templates/footer');
        }

        function new_fixed_asset_bak() {
            $this->core_layout->addJs("js/ams/new_fixed_asset_bak.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('assets/new_fixed_asset_bak');
            $this->load->view('core/templates/footer');
        }

        function edit_fixed_asset($id = NULL) {
            $this->core_layout->setPrivilegeName("fixed_masterfile");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
            $this->core_layout->addJs("js/jquery.rotate.1-1.js", true);
            $this->core_layout->addJs("js/ams/edit_fixed_asset.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);
            
            $data = array();
            if ($id) {
                $data['rs'] = $this->assets->getAssetDetails($id);
                $data['images'] = $this->assets->getAssetImages($id);
            }

            $this->load->view('core/templates/header');
            $this->load->view('assets/edit_fixed_asset', $data, FALSE);
            $this->load->view('core/templates/footer');
        }

        function get_asset_details($id) {
            echo json_encode($this->assets->getAssetDetails($id));
        }

        function get_company_collection() {
            $data = $this->assets->getCompanyCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_department_collection() {
            $data = $this->assets->getDepartmentCollection();
            echo json_encode($data);
        }

        function get_category_collection($type = null) {
            $data = $this->assets->getCategoryCollection($type);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_location_collection() {
            $data = $this->assets->getLocationCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_area_collection() {
            $data = $this->assets->getAreaCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_status_collection() {
            $data = $this->assets->getStatusCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_status_category_collection() {
            $data = $this->assets->getStatusCategoryCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function type_lookup() {
            $data = $this->assets->getTypeLookup();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function generate_asset_code() {
            $data = $this->assets->generateAssetCode();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function process_new() {
            $data = $this->assets->processNew();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function process_edit() {
            $data = $this->assets->processEdit();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function asset_images($type = "assets", $id = null) {
            $data = $this->assets->AssetImages($type, $id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_images() {
            $data = $this->assets->setImages();
            $this->output
                ->set_content_type('json');

            $html = $this->load->view("assets/modals/content", array("data" => $data), true);
            $resultset["html"] = $html;
            echo json_encode($resultset);
        }

        function get_asset_data() {
            $data = $this->assets->getAssetData();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_asset_components($mother_asset_id = 0) {
            $data = $this->assets->getAssetComponents($mother_asset_id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_asset_accountability_() {
            $data = $this->assets->getAssetAccountability();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_asset_borrowing() {
            $data = $this->assets->getAssetBorrowing();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_asset_accountability_borrowing($asset_id = null){
            
            $data = $this->assets->getAssetAccountabilityBorrowing($asset_id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_mother_asset_collection() {
            $data = $this->assets->getMotherAssetCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_asset_component_collection() {
            $data = $this->assets->getAssetComponentCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function save_include() {
            $data = $this->assets->saveInclude();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function save_document($asset_id) {
            $data = $this->assets->saveDocument($asset_id);
            echo json_encode($data);
        }

        function update_document() {
            $data = $this->assets->updateDocument();
            echo json_encode($data);
        }

        function ajax_uploaded_images() {
            $data = $this->assets->ajaxUploadedImages();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function load_modal_upload() {
            $data = $this->assets->loadModalUpload();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_asset_documents($asset_id) {
            $data = $this->assets->getAssetDocuments($asset_id);
            echo json_encode($data);
        }

        function remove_asset_component($id) {
            $data = $this->assets->removeAssetComponent($id);
            echo json_encode($data);
        }

        function delete_asset_document() {
            $id = $this->input->get('id');
            $data = $this->assets->deleteAssetDocument($id);
            echo json_encode($data);
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

        function get_document_item($data) {
            $result = $this->assets->getDocumentItem($data['id']);
            return $result;
        }

        function pass_data_to_dialog($data) {
            return $data;
        }

        function get_asset_document_details($formData) {
            return $this->assets->getAssetDocumentDetails($formData);
        }

        /* dynamic opening of modal form */

        /* ARCHIVING FUNCTIONS */
        function archive_fixed_asset() {
            $data = $this->assets->archiveAsset("mother asset");
            echo json_encode($data);
        }

        function archive_asset_component() {
            $data = $this->assets->archiveAsset("asset component");
            echo json_encode($data);
        }
        /* END ARCHIVING FUNCTIONS */

        /* RESTORE ARCHIVED */
        function restore_archived_asset($id = null) {
            $data = $this->assets->restoreArchivedAsset($id);
            echo json_encode($data);
        }

        function restore_archived_assets_multiple() {
            $data = $this->assets->restoreArchivedAssetsMultiple();
            echo json_encode($data);
        }
        /* END RESTORE ARCHIVED */

        /* DELETE ASSET PERMANENTLY */
        function delete_asset($id = null) {
            $data = $this->assets->deleteAsset($id);
            echo json_encode($data);
        }

        function delete_archived_assets_multiple() {
            $data = $this->assets->deleteArchivedAssetsMultiple();
            echo json_encode($data);
        }
        /* END DELETE ASSET PERMANENTLY */

        /* LOAD VIEW FOR ARCHIVED ASSETS */
        function archive() {
            $_type = isset($_GET["type"]) ? $_GET["type"] : "mother";
            $data = array();
            $this->core_layout->setPrivilegeName("ams_archives_assets");

            if ($_type === "mother") {
                $data['title'] = "Archived Fixed Assets";
            } elseif ($_type === "component") {
                $data['title'] = "Archived Asset Components";
            } else {
                $data['title'] = "Archived Asset & Asset Components";
            }

            $data['type'] = $_type;
            $this->core_layout->addJs("js/ams/archived_assets.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $this->load->view('core/templates/header');
            $this->load->view("archives/archived_assets", $data);
            $this->load->view('core/templates/footer');
        }

        function get_archive_collections() {
            $type = isset($_GET['type']) ? $_GET['type'] : 'mother';
            echo json_encode($this->assets->getArchiveCollection($type));
        }

        /* END LOAD VIEW FOR ARCHIVED ASSETS */

        function get_station_collection() {
            $data = $this->assets->getStationCollection();
            echo json_encode($data);
        }

        function save_new_asset($isComponent = 0, $motherId = null) {
            $data = $this->assets->saveNewAsset($isComponent, $motherId);
            echo json_encode($data);
        }

        function load_view_tab() {
            $view = isset($_GET['view']) ? $_GET['view'] : "";
            $data = $this->load->view("assets/tabs/" . $view, '', TRUE);
            echo json_encode(array('view' => $data));
        }

        function update_fixed_asset($isComponent = 0) {
            $data = $this->assets->updateFixedAsset($isComponent);
            echo json_encode($data);
        }

        function get_primary_pic($id) {
            $data = $this->assets->getPrimaryPic($id);
            echo json_encode($data);
        }

        function get_asset_list($isMother) {
            $data = $this->assets->getAssetList($isMother);
            echo json_encode($data);
        }

        function save_component_to_mother_asset() {
            $data = $this->assets->saveComponentToMotherAsset();
            echo json_encode($data);
        }

        function exclude_component($id) {
            $data = $this->assets->excludeComponent($id);
            echo json_encode($data);
        }

        function re_include_component($id) {
            $data = $this->assets->reIncludeComponent($id);
            echo json_encode($data);
        }

        function remove_document() {
            $id = isset($_GET['id']) ? $_GET['id'] : "";
            $data = $this->assets->removeDocument($id);
            echo json_encode($data);
        }

        function get_asset_accountability($asset_id = null) {
            $data = $this->assets->getAssetAccountability($asset_id);
            echo json_encode($data);
        }

        function get_asset_borrowing_history($asset_id = null) {
            $data = $this->assets->getAssetBorrowingHistory($asset_id);
            echo json_encode($data);
        }

        function edit_asset_component($id = NULL) {
            $this->core_layout->setPrivilegeName("asset_components_masterfile");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
            $this->core_layout->addJs("js/ams/edit_asset_component.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $data = array();
            if ($id) {
                $data['rs'] = $this->assets->getAssetDetails($id);
                $data['images'] = $this->assets->getAssetImages($id);
            }

            $this->load->view('core/templates/header');
            $this->load->view('assets/edit_asset_component', $data, FALSE);
            $this->load->view('core/templates/footer');
        }

        function check_if_asset_is_borrowed_or_accounted() {
            $data = $this->assets->checkIfAssetIsBorrowedOrAccounted();
            echo json_encode($data);
        }

        function relocate_asset_images($isComponent = 0) {
            $data = $this->assets->relocateAssetImages($isComponent);
            echo json_encode($data);
        }

        function relocate_archived_asset_images() {
            $data = $this->assets->relocateArchivedAssetImages();
            echo json_encode($data);
        }

        function relocate_asset_documents() {
            $data = $this->assets->relocateAssetDocuments();
            echo json_encode($data);
        }

        function get_employee_name() {
            $data = $this->assets->getEmployeeName();
            echo json_encode($data);
        }

        function get_location() {
            $data = $this->assets->getLocation();
            echo json_encode($data);
        }

        function get_station() {
            $data = $this->assets->getStation();
            echo json_encode($data);
        }

        function get_category() {
            $data = $this->assets->getCategory();
            echo json_encode($data);
        }

        function get_inactive_mother_assets_list() {
            echo json_encode($this->assets->getInactiveMotherAssetsList());
        }

        function view_asset($id, $status) {
            $this->core_layout->setPrivilegeName("ams_archives_assets");
            $this->core_layout->addJs("js/ams/view_assets.script.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $data = array();
            if ($id) {
                $data['rs'] = $this->assets->getAssetDetails($id);
                $data['images'] = $this->assets->getAssetImages($id);
                $data['title'] = intval($status) === 0 ? 'VIEW FIXED ASSET' : 'VIEW ASSET COMPONENT';
                $data['status'] = $status;
                $data["id"] = $id;
                $data["sub_category"] = $this->db->get_where("gccasset.asset_sub_cat", array("sub_cat_id" => $data['rs']->sub_cat_code))->row("sub_cat_desc");
                $data["status_name"] = $data['rs']->status !== "archived" ? $this->db->get_where("gccasset.status", array("code" => $data['rs']->status))->row("name") : $data['rs']->status;
                $data["statuses"] = $this->db->group_by("code")->get("gccasset.status")->result();
            }

            $this->load->view('core/templates/header');
            $this->load->view('ams/archives/viewing/assets', $data, FALSE);
            $this->load->view('core/templates/footer');
        }

        function inventory_check() {
            $data = $this->assets->inventoryCheck();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function recover_asset($id, $type) {
            echo json_encode($this->assets->recoverAsset($id, $type));
        }

        function get_employment_details(){
            $data = $this->assets->getEmploymentDetails();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));

        }
        
        function check_multiple_if_borrowed_or_accounted(){
            $data = $this->assets->check_multiple_if_borrowed_or_accounted();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));

        }
        
        function mass_archive_assets(){
            $data = $this->assets->mass_archive_assets();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        
        function get_selected_for_archive(){
            $data = $this->assets->get_selected_for_archive();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
    }