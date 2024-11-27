<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Inventory extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("inventory_system");
            $this->authenticate->doRedirect();

            $this->core_layout->setBodyClass("inventory_system");
            $this->core_layout->setPrivilegeName("inventory_system");


            $this->load->model("is_model");
            $this->load->model("items_model");
            $this->load->model("portal/portal_model");
        }

        public function index(){
            $getWarehouseCollection = $this->crud->getCollection(array("status"=>1),"inventory_core.warehouses");
            $this->load->view("core/templates/header");
            $this->load->view("inventory/index",array("data"=>$getWarehouseCollection));
            $this->load->view("core/templates/footer");
        }

        public function dashboard($id){
            if($id):
                $this->session->unset_userdata("warehouseid");
                $this->session->set_userdata("warehouseid", $id);
            endif;

            $this->load->view("core/templates/header");
            $this->load->view("inventory/dashboard/index",array("id"=>$id));
            $this->load->view("core/templates/footer");
        }

        public function stocks(){
            $this->core_layout->setPageTitle("Inventory - Stocks");
            $this->core_layout->setBodyClass("inventory inventory_stocks");
            $this->core_layout->setPrivilegeName("inventory_stocks");

            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");

            $this->load->view("core/templates/header");
            $this->load->view("inventory/stocks/index");
            $this->load->view("core/templates/footer");
        }

        public function warehouseportal(){
            $getWarehouseCollection = $this->crud->getCollection(array("status"=>1),"inventory_core.warehouses");
            $this->load->view("core/templates/header");
            $this->load->view("inventory/index",array("data"=>$getWarehouseCollection));
            $this->load->view("core/templates/footer");
        }

        public function warehouse(){
            
        }
    }