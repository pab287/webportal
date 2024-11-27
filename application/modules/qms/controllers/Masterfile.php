<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Masterfile extends MY_Controller{
        function __construct(){
            parent::__construct();

            $this->authenticate->setModuleAccess("qms");
            $this->authenticate->doRedirect();

            $this->core_layout->setBodyClass("qms");
            $this->core_layout->setPrivilegeName("qms");

            $this->load->model("qms/Qms_m", "ppm");
            $this->load->model("qms/Category_m", "category");
            $this->load->model("qms/Policy_m", "policy");
            $this->load->model("qms/Department_m", "dept");
            $this->load->model("qms/Company_m", "comp");

            // $this->core_layout->addExternalJs("https://cdn.ckeditor.com/ckeditor5/12.3.1/classic/ckeditor.js", true);
            $this->core_layout->addJs("global/plugins/ckeditor/build/ckeditor.js", true);
            // $this->core_layout->addExternalJs('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js', true);

            // $this->core_layout->addExternalCss('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf_viewer.min.css');

            $this->core_layout->addCss("plugins/pdf/pdf_viewer.min.css");
            $this->core_layout->addJs("plugins/pdf/pdf.min.js", true);
        }

        // category
        public function category(){
            $this->core_layout->setPrivilegeName("ppm_category");

            $this->core_layout->setPageTitle("QMS - Category Masterfile");
            $this->core_layout->addJs("js/qms/category_masterfile_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view('qms/masterfile/category/index');
            $this->load->view("core/templates/footer");
        }

        public function get_category_datatable_request(){
            $data = $this->category->getCategoryDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_category_modal_content($content = "add") {
            $data = $this->category->getCategoryModalContent($content);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_category(){
            $data = $this->category->add_category();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_modal_category(){
            $data = $this->category->update_category();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_current_category(){
            $data = $this->category->remove_category();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function archived_category(){
            $this->core_layout->setPageTitle("QMS - Archived Category");
            $this->core_layout->addJs("js/qms/category_masterfile_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view('qms/masterfile/category/archive');
            $this->load->view("core/templates/footer");
        }

        public function get_archived_category_datatable_request(){
            $data = $this->category->getArchivedCategoryDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function restore_current_category(){
            $data = $this->category->restore_category();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        // category

        // policy
        public function document(){
            $this->core_layout->setPrivilegeName("ppm_document");

            $arrData = array();

            $this->core_layout->setPageTitle("QMS - Documents");

            $arrData['category'] = $this->category->get_category_list();
            $arrData['employees'] = $this->policy->getEmployeeCollection();
            $arrData['company'] = $this->policy->get_company_list();
            $arrData['department'] = $this->policy->get_department_list();

            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
            $this->core_layout->addJs("js/qms/policy_masterfile_script.js", true, $arrData);

            $this->load->view("core/templates/header");
            $this->load->view('qms/masterfile/document/index');
            $this->load->view("core/templates/footer");
        }

        function get_policy_modal_content($content = "add") {
            $data = $this->policy->getPolicyModalContent($content);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_policy(){
            $data = $this->policy->add_policy();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_policy_datatable_request(){
            $data = $this->policy->getPolicyDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function edit_modal_policy(){
            $data = $this->policy->edit_policy();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_current_policy(){
            $data = $this->policy->remove_policy();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function archived_document(){
            $arrData = array();

            $this->core_layout->setPageTitle("QMS - Archived Policy");

            $this->core_layout->addJs("js/qms/policy_masterfile_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view('qms/masterfile/document/archive');
            $this->load->view("core/templates/footer");
        }

        public function get_archived_policy_datatable_request(){
            $data = $this->policy->getArchivedPolicyDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function restore_current_policy(){
            $data = $this->policy->restore_policy();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_categories(){
            $data = $this->policy->get_all_categories();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_timeline_list(){
            $data = $this->policy->get_timeline();

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        // policy

        // policy by department
        public function department_document(){
            $this->core_layout->setPrivilegeName("ppm_department_document");

            $arrData = array();

            $this->core_layout->setPageTitle("QMS - Documents by Department");

            $arrData['category'] = $this->category->get_category_list();
            $arrData['employees'] = $this->policy->getEmployeeCollection();
            $arrData['company'] = $this->policy->get_company_list();
            $arrData['department'] = $this->policy->get_department_list();

            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
            $this->core_layout->addJs("js/qms/policy_document_masterfile_script.js", true, $arrData);

            $this->load->view("core/templates/header");
            $this->load->view('qms/masterfile/department/index');
            $this->load->view("core/templates/footer");
        }

        function get_department(){
            $data = $this->dept->get_all_department_in_policy();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_department_policy_datatable_request(){
            $data = $this->dept->getPolicyDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        // policy by department

        // policy by company
        public function company_document(){
            $this->core_layout->setPrivilegeName("ppm_company_document");

            $arrData = array();

            $this->core_layout->setPageTitle("QMS - Documents by Company");

            $arrData['category'] = $this->category->get_category_list();
            $arrData['employees'] = $this->policy->getEmployeeCollection();
            $arrData['company'] = $this->policy->get_company_list();
            $arrData['department'] = $this->policy->get_department_list();

            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
            $this->core_layout->addJs("js/qms/policy_companies_masterfile_script.js", true, $arrData);

            $this->load->view("core/templates/header");
            $this->load->view('qms/masterfile/company/index');
            $this->load->view("core/templates/footer");
        }

        function get_companies(){
            $data = $this->comp->get_all_company_in_policy();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_company_policy_datatable_request(){
            $data = $this->comp->getPolicyDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        // policy by company

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
            $this->core_layout->addJs('js/qms/public_script.js', true, $arrData);


            $this->load->view("core/templates/header");
            $this->load->view('qms/masterfile/public/index');
            $this->load->view("core/templates/footer");
        }
    }