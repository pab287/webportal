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

        public function new_request(){
            $data = array();
            $data['employee'] = $this->eng_req->select2Employee();
            $data['projects'] = $this->eng_req->select2Projects();
            $data['req_types'] = $this->eng_req->getReqTypes();
            $this->core_layout->setPageTitle("Request For Information");
            // $this->core_layout->setPrivilegeName("eforms_new_rfi_request");
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("global/plugins/ckeditor/build/ckeditor.js", true);
            $this->core_layout->addJs("js/eforms/eng_request/new_request.js", true, $data);
            $this->load->view('core/templates/header');
            $this->load->view('eforms/engineering_request_forms/new_request');
            $this->load->view('core/templates/footer');
        }

        public function req_types(){
            $data = array();
            $data['employee'] = $this->eng_req->select2Employee();
            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
            $this->core_layout->addJs("js/eforms/eng_request/req_type.js", true, $data);
            $this->load->view('core/templates/header');
            $this->load->view('eforms/engineering_request_forms/req_type');
            $this->load->view('core/templates/footer');
        }

        public function get_req_types(){
            $data = $this->eng_req->getReqType();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function save_req_type(){
            $data = $this->eng_req->saveReqType();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function update_req_type(){
            $data = $this->eng_req->updateReqType();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function archive_req_type(){
            $data = $this->eng_req->archiveReqType();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function view_rfi_request($id){
            $data = array();
            $result = $this->eng_req->viewRFIRequest($id);
            $data['request'] = $result['data'];
            $data['attachments'] = $result['attachments'];
            $data['reply'] = $result['reply'];
            $data['reply_attachments'] = $result['reply_attachments'];
            $data['req_types'] = $this->eng_req->getReqTypes();
            // $data['actions'] = $this->core_layout->generatePrivilegeAction();

            $this->core_layout->setPageTitle("Request For Information");
            $this->core_layout->setPrivilegeName("eng_req");
            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("global/plugins/ckeditor/build/ckeditor.js", true);
            $this->core_layout->addJs("js/eforms/eng_request/edit_rfi_request.js", true, $data);
            $this->load->view('core/templates/header');
            $this->load->view('eforms/engineering_request_forms/edit_rfi_request');
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

        public function save_rfi(){
            $data = $this->eng_req->createRFI();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function save_reply(){
            $data = $this->eng_req->saveReply();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function update_reply(){
            $data = $this->eng_req->updateReply();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_rfi_request(){
            $id = $this->input->post('id');
            $data = array();
            $result = $this->eng_req->viewRFIRequest($id);
            $data['request'] = $result['data'];
            $data['attachments'] = $result['attachments'];
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }


        public function process_reply(){
            $data = $this->eng_req->processReply();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

    }
