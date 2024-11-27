<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Transaction extends MY_Controller {
        function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("hris");
            $this->authenticate->doRedirect();

            $this->core_layout->setBodyClass("hris transaction");
            $this->core_layout->setPrivilegeName("hris_hire");

            $this->load->model("hris/hire_model");

            date_default_timezone_set('Asia/Manila');
        }

        function hire() {
            $this->core_layout->setPageTitle("HRIS - Hire Masterfile");
            $this->core_layout->addJs("js/hris/hire_masterfile_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view("hris/transaction/hire/index");
            $this->load->view("core/templates/footer");
        }

        function hire_employee($id = null) {
            if ($id) {
                $arrData = $this->hire_model->getCurrentHiring($id, "Ongoing");
                if (isset($arrData->status) && $arrData && $arrData->status == "Ongoing") {
                    $this->core_layout->setBodyClass("hris transaction-hire_new_employee");
                    $this->core_layout->setPageTitle("HRIS - Hire Employee");

                    $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
                    $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
                    $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
                    $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
                    $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
                    $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");

                    $this->core_layout->addJs("js/hris/hire_masterfile_script.js", true, $arrData);
                    $this->core_layout->addJs("js/hris/search_employee_script.js", true);
                    $this->core_layout->addCss("css/hris/index.css", true);
                    $this->load->view("core/templates/header");
                    $this->load->view("hris/transaction/hire/add_hire_employee");
                    $this->load->view("core/templates/footer");
                } else {
                    redirect(site_url("hris/transaction/hire"), "refresh");
                }
            } else {
                redirect(site_url("hris/transaction/hire"), "refresh");
            }
        }

        function get_hire_datatable_request() {
            $data = $this->hire_model->getHireDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_hiring_positions_for_select() {
            $data = $this->hire_model->getHiringPositionsForSelect();
            echo json_encode($data);
        }

        function rehire($employee_id) {
            if (empty($employee_id)) {
                redirect(site_url("hris/masterfile/employee"), "refresh");
            } else {
                $post = $this->input->post();
                $data = $this->hire_model->rehireEmployee($employee_id, $post);
                echo json_encode($data);
            }
        }


        public function search_employee()
        {
            $firstname = $this->input->post('firstname');
            $middlename = $this->input->post('middlename');
            $lastname = $this->input->post('lastname');
            $data = $this->hire_model->searchEmployeeHire($firstname, $middlename, $lastname);
            echo json_encode($data);
        }

        public function get_applicants(){
            $data = $this->hire_model->getApplicants();
            echo json_encode($data);
        }

        public function get_selected_applicant(){
            $data = $this->hire_model->get_selected();
            echo json_encode($data); 
        }
    }