<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Qms extends MY_Controller{
        function __construct(){
            parent::__construct();

            $this->authenticate->setModuleAccess("qms");
            $this->authenticate->doRedirect();

            $this->core_layout->setBodyClass("qms");
            $this->core_layout->setPrivilegeName("qms");

            $this->load->model("qms/Qms_m", "ppm");
            $this->load->model("qms/Category_m", "category");
            $this->load->model("qms/Policy_m", "policy");

            $this->core_layout->addCss("plugins/pdf/pdf_viewer.min.css");
            $this->core_layout->addJs("plugins/pdf/pdf.min.js", true);
            // $this->core_layout->addExternalJs('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js', true);

            // $this->core_layout->addExternalCss('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf_viewer.min.css');
        }

        function index(){
            $this->core_layout->setPrivilegeName("ppm_dashboard");

            $this->core_layout->addJs("global/js/amcharts4/core.js", true);
            $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
            $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
            $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);

            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
            $this->core_layout->addCss("css/qms/dashboard_style.css", true);
            // $this->core_layout->addJs('global/js/googlechart/loader.js', TRUE);
            $this->core_layout->addJs('js/qms/dashboard_script.js', true);

            $this->load->view("core/templates/header");
            $this->load->view('index');
            $this->load->view("core/templates/footer");
        }

        function public_document(){
            $this->core_layout->setPrivilegeName("ppm_public_document");

            $arrData = array();

            $arrData['category'] = $this->category->get_category_list();
            $arrData['employees'] = $this->policy->getEmployeeCollection();
            $arrData['company'] = $this->policy->get_company_list();
            $arrData['department'] = $this->policy->get_department_list();

            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
            $this->core_layout->addCss("css/qms/dashboard_style.css", true);
            $this->core_layout->addJs('js/qms/dashboard_script.js', true, $arrData);


            $this->load->view("core/templates/header");
            $this->load->view('index');
            $this->load->view("core/templates/footer");
        }

        function get_policy_datatable_request(){
            $data = $this->ppm->getPolicyDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_policy_modal_content($content = "add") {
            $data = $this->ppm->getPolicyModalContent($content);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function count_download(){
            $data = $this->ppm->count_download();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function print_document($id){
            $arrData = array();
            $this->core_layout->setPrivilegeName("ppm_dashboard");
            $this->core_layout->setPageTitle("QMS - Print Document");

            $arrData['data'] = $this->ppm->get_document($id);

            $this->load->view('print_template/print_document', $arrData);

        }

        function get_category_analytics(){
            $data = $this->ppm->get_category_analytics();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        
        function get_company_analytics(){
            $data = $this->ppm->get_company_analytics();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_recently_document_datatable(){
            $data = $this->ppm->getRecentlyPolicyDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
    }