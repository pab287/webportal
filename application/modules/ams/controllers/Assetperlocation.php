<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Assetperlocation extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("ams");
            $this->authenticate->doRedirect();
            $this->load->model("Assetperlocation_model", "asset_per_location");
        }

        public function masterfile()
        {
            $this->core_layout->setPrivilegeName("assetperlocation");
            $this->core_layout->addJs("js/ams/asset_vehicle_location.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $this->load->view('core/templates/header');
            $this->load->view('location/asset_per_location_masterfile');
            $this->load->view('core/templates/footer');
        }

        function view_asset($id = NULL)
        {
            $this->core_layout->setPrivilegeName("assetperlocation");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
            $this->core_layout->addJs("js/ams/edit_fixed_asset.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $data = array();
            if ($id) {
                $data['rs'] = $this->asset_per_location->getAssetDetails($id);
                $data['images'] = $this->asset_per_location->getAssetImages($id);
            }

            $this->load->view('core/templates/header');
            $this->load->view('assets/edit_fixed_asset', $data, FALSE);
            $this->load->view('core/templates/footer');
        }

        function asset_collection($export = 0)
        {
            $data = $this->asset_per_location->assetCollection($export);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function vehicle_collection($export = 0)
        {
            $data = $this->asset_per_location->vehicleCollection($export);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_asset_details($id) {
            echo json_encode($this->asset_per_location->getAssetDetails($id));
        }

        
        function get_asset_accountability($id)
        {
            $data = $this->asset_per_location->getAssetAccountability($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_vehicle_accountability($id)
        {
            $data = $this->asset_per_location->getVehicleAccountability($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_location() {
            $data = $this->asset_per_location->getLocation();
            echo json_encode($data);
        }

        function assets_location(){
            $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyD2szEzfIU7_Hec55jNy8JtoNr_uj8R2_M&callback=initMap&libraries=places,drawing";

            $this->core_layout->setPrivilegeName("assetperlocation");
            $this->core_layout->addJs("js/ams/assets_location.js", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);

            $arrData = array();
            $arrData["script_attribute"] = array("async");
            $this->core_layout->addExternalJs($externalUrl, true, $arrData);

            $this->load->view('core/templates/header');
            $this->load->view('location/assets_location');
            $this->load->view('core/templates/footer');
        }

        function get_assets_location(){
            $data = $this->asset_per_location->getAssetsLocation();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
    }