<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Engineering_request_forms extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("eforms-engineering_request_forms");
            $this->core_layout->setPrivilegeName("eforms");
            $this->authenticate->doRedirect();
            $this->load->model("Eng_req_m","eng_req");
        }

        public function masterfile(){
            $data = array();
            $data['employee'] = $this->eng_req->select2Employee();
            $data['projects'] = $this->eng_req->select2Projects();
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
            $this->core_layout->addJs("js/eforms/eng_request/eng_req_form.js", true, $data);
            $this->load->view('core/templates/header');
            $this->load->view('eforms/engineering_request_forms/index');
            $this->load->view('core/templates/footer');
        }

        public function projects(){
            $data = array();
            $data['employee'] = $this->eng_req->select2Employee();
            // $data['supervisor'] = $this->eng_req->select2Supervisor();
            // $data['installer'] = $this->eng_req->select2Installer();
            $this->core_layout->addJs("js/eforms/eng_request/projects.js", true, $data);
            $this->load->view('core/templates/header');
            $this->load->view('eforms/engineering_request_forms/projects');
            $this->load->view('core/templates/footer');
        }

        public function get_projects(){
            $data = $this->eng_req->getProjects();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function save_project(){
            $data = $this->eng_req->saveProject();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function update_project(){
            $data = $this->eng_req->updateProject();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_rfis(){
            $data = $this->eng_req->getRFIs();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }
        

    }
