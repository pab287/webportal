<?php defined('BASEPATH') || exit('No direct script access allowed');

    class Settings extends MY_Controller
    {
        function __construct(){
            parent::__construct();

            $this->authenticate->setModuleAccess("hris");
            $this->authenticate->doRedirect();

            $this->core_layout->setPageTitle("HRIS - Checklist");

            $this->load->model('Setting_m', 'setting');
        }

        function checklist(){
            $this->core_layout->setPrivilegeName("hris_pre_employment");
            $this->core_layout->addJs('js/hris/settings/checklist_script.js', TRUE);

            $this->load->view("core/templates/header");
            $this->load->view("settings/checklist/index");
            $this->load->view("core/templates/footer");
        }

        function get_checklist_datatable_request(){
            $data = $this->setting->getChecklistDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_checklist_modal_content($content = "add"){
            $data = $this->setting->getChecklistModalContent($content);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_checklist(){
            $data = $this->setting->new_checklist();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_current_checklist(){
            $data = $this->setting->remove_checklist();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_modal_checklist(){
            $data = $this->setting->update_checklist();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function archived(){
            $this->core_layout->setPrivilegeName("hris_pre_employment");
            $this->core_layout->addJs('js/hris/settings/checklist_script.js', TRUE);

            $this->load->view("core/templates/header");
            $this->load->view("settings/checklist/archived");
            $this->load->view("core/templates/footer");
        }

        function get_archived_checklist_datatable_request(){
            $data = $this->setting->getArchivedChecklistDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function restore_current_checklist(){
            $data = $this->setting->restoreChecklist();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_checklists(){
            $data = $this->setting->getChecklists();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function employment_checklist(){
            $data = $this->setting->employmentChecklist();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
    }