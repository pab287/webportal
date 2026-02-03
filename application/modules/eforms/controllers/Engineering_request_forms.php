<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Engineering_request_forms extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("eforms-engineering_request_forms");
            $this->core_layout->setPrivilegeName("eforms");
            $this->authenticate->doRedirect();
        }

        public function masterfile(){
            $this->core_layout->addJs("js/eforms/eng_request/eng_req_form.js", true);
            $this->load->view('core/templates/header');
            $this->load->view('eforms/engineering_request_forms/index');
            $this->load->view('core/templates/footer');
        }

        public function projects(){
            $this->core_layout->addJs("js/eforms/eng_request/projects.js", true);
            $this->load->view('core/templates/header');
            $this->load->view('eforms/engineering_request_forms/projects');
            $this->load->view('core/templates/footer');
        }

    }
