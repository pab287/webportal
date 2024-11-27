<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Sss_table extends MY_Controller
    {
        function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("payroll-sss_table");
            $this->authenticate->doRedirect();

            $this->load->model("ams/Utilities_model", "utilities");
            $this->load->model("payroll/sss_table_model", "sss");
            //$this->load->model("Dashboard_model", "dashboard");
            date_default_timezone_set('Asia/Manila');
        }

        function get_datatable_request()
        {
            $data = $this->loa->getDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function index(){
            $this->core_layout->setPrivilegeName("payroll_sss_table");

            $this->load->view("core/templates/header");
            $this->load->view("payroll/sss_table");
            $this->load->view("core/templates/footer");
        }

        function save_range(){
            $data = $this->sss->saveRange();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        
        function get_range(){
            $data = $this->sss->getDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_range_data(){
            $data = $this->sss->getRangeData();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function edit_range(){
            $data = $this->sss->editRange();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
    }