<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class App_location extends MY_Controller {
        protected $eformsKey;

        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("time");
            $this->authenticate->doRedirect();
            
            $this->load->model("Gcctime_location_model", "location_model");
            $this->eformsKey = $_ENV['PROD_MAP_KEY']; 
        }

        public function index(){
            
            // $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyD2szEzfIU7_Hec55jNy8JtoNr_uj8R2_M&callback=initMapTemp&libraries=places,drawing&v=weekly";
            // $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCTzlKHdtvrOuKv7LEQjW8HVmy1QFFgalM&callback=initMapTemp&libraries=places,drawing&v=weekly";
            // $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCm_pTwQzhaAKspErhW9ptpubv_ATLrpgE&callback=initMapTemp&libraries=places,drawing&v=weekly";
            $externalUrl = "https://maps.googleapis.com/maps/api/js?key=" . $this->eformsKey . "&callback=initMapTemp&libraries=places,drawing&v=weekly";

            $arrData["script_attribute"] = array("async");
            $this->core_layout->addExternalJs($externalUrl, true, $arrData);
            $this->core_layout->setPrivilegeName("app_location");
            date_default_timezone_set("Asia/Taipei");
            
            $this->load->view('core/templates/header');
            $this->load->view('app_location/index');
            $this->load->view('core/templates/footer');
            
        }

        public function get_app_users(){
            $data = $this->location_model->getAppUsers();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function get_users_location(){
            $data = $this->location_model->getUsersLocation();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

    }