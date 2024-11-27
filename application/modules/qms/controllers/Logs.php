<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Logs extends MY_Controller{
        function __construct(){
            parent::__construct();

            $this->authenticate->setModuleAccess("qms");
            $this->authenticate->doRedirect();

            $this->core_layout->setBodyClass("qms");
            $this->core_layout->setPrivilegeName("qms");

            $this->load->model("qms/Logs_m", "logs");
        }

        function index(){
            $this->core_layout->setPrivilegeName("qms_logs");

            $this->core_layout->setPageTitle("QMS - Logs");
            $this->core_layout->addJs("js/qms/logs_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view('qms/logs/index');
            $this->load->view("core/templates/footer");
        }

        function get_logs_datatable_request(){
            $data = $this->logs->getLogsDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
    }