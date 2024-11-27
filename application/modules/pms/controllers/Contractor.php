<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Contractor extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authenticate->setModuleAccess("pms");
        $this->authenticate->doRedirect();
        $this->load->model("contractor_m", "adm_contractor");
        $this->load->model("hris/contractor_model");
    }

    public function index()
    {
        $this->core_layout->setPrivilegeName("pms_contractor_masterfile");
        $this->core_layout->setPageTitle("PMS - Contractor Masterfile");
        $this->core_layout->addJs("js/pms/contractor/contractor_masterfile.js", true);

        $this->load->view("core/templates/header");
        $this->load->view("pms/contractor/index");
        $this->load->view("core/templates/footer");
    }

    function get_contractor_select2_data()
    {
        $data = $this->adm_contractor->getContractorSelect2Data();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_task_incharge_select2_data()
    {
        $data = $this->adm_contractor->getTaskInchargeSelect2Data();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_foreman_select2_data()
    {
        $data = $this->adm_contractor->getForemanSelect2Data();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_contractor_datatable_request()
    {
        $data = $this->contractor_model->getContractorDatatableRequest();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_contractor_modal_content($content = "add")
    {
        $data = $this->adm_contractor->getContractorModalContent($content);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function set_modal_contractor()
    {
        $data = $this->contractor_model->setModalContractor();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_modal_contractor()
    {
        $data = $this->contractor_model->updateModalContractor();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function remove_current_contractor()
    {
        $data = $this->contractor_model->removeCurrentContractor();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function check_if_contractor_is_assigned($id)
    {
        echo json_encode($this->adm_contractor->checkIfContractorIsAssigned($id));
    }
}