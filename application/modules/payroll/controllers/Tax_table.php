<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Tax_table extends MY_Controller
    {
        function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("tax_table");
            $this->authenticate->doRedirect();

            $this->load->model("ams/Utilities_model", "utilities");
            $this->load->model("payroll/tax_table_model", "tax");
            date_default_timezone_set('Asia/Manila');
        }

        function index(){
            $this->core_layout->setPrivilegeName("payroll_tax_table");

            $this->load->view("core/templates/header");
            $this->load->view("payroll/tax_table");
            $this->load->view("core/templates/footer");
        }

        function save_range(){
            $data = $this->tax->saveRange();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_range(){
            $data = $this->tax->getDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_range_data(){
            $data = $this->tax->getRangeData();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function edit_range(){
            $data = $this->tax->editRange();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

    }