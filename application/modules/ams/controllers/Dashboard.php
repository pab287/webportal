<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Dashboard extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("ams");
            $this->authenticate->doRedirect();
            $this->load->model("Dashboard_model", "dashboard");
        }

        public function index()
        {
            $this->core_layout->setPrivilegeName("ams_dashboard");
            $this->core_layout->addJs("global/js/amcharts4/core.js", true);
            $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
            $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
            $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
            $this->core_layout->addJs("js/ams/index.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('dashboard');
            $this->load->view('core/templates/footer');
        }

        public function get_asset_demographics()
        {
            $data = $this->dashboard->getAssetDemographics();
            echo json_encode($data);
        }

        public function get_recently_added_assets($recently)
        {
            $data = $this->dashboard->getRecentlyAddedAssets($recently);
            echo json_encode($data);
        }

        public function get_accounted_unaccounted_assets($type)
        {
            $data = $this->dashboard->getAccountedUnaccountedAssets($type);
            echo json_encode($data);
        }

        public function get_asset_per_location($type)
        {
            $data = $this->dashboard->getAssetPerLocation($type);
            echo json_encode($data);
        }
        
        public function get_asset_per_status($type)
        {
            $data = $this->dashboard->getAssetPerStatus($type);
            echo json_encode($data);
        }

        public function get_asset_incomplete_details()
        {
            $data = $this->dashboard->getAssetIncompleteDetails();
            echo json_encode($data);
        }
        
    }