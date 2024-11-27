<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Warehouse extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("inventory_warehouse");
            $this->authenticate->doRedirect();

            $this->core_layout->setBodyClass("inventory_warehouse");
            $this->core_layout->setPrivilegeName("inventory_warehouse");

            $this->load->model("portal/portal_model");
            $this->load->model("inventory/warehouse_model");
        }

        public function index(){
            $this->core_layout->setPrivilegeName("inventory_warehouse");

            $getWarehouseCollection = $this->crud->getCollection(array("status"=>1),"inventory_core.warehouses");
            $this->load->view("core/templates/header");
            $this->load->view("inventory/warehouse/index",array("data"=>$getWarehouseCollection));
            $this->load->view("core/templates/footer");
        }

        function create_new(){
            $data = $this->warehouse_model->createNew();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_warehouse_collection(){
            $data = $this->warehouse_model->getWarehouseCollection();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
    } 