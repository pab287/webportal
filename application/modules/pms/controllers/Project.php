<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Project extends MY_Controller {
	public function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("pms");
        $this->authenticate->doRedirect();
        $this->load->model("project_m","adm_project");
        $this->core_layout->addJs("global/js/vue-modal/vue.modal.js");
    }

    function index(){
        $this->core_layout->setPrivilegeName("pms_project_masterfile");
        $this->core_layout->addJs("js/pms/project/project_masterfile.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('pms/project/index');
        $this->load->view('core/templates/footer');
    }

    function task($id=null){
        if($id){
            $this->core_layout->setPrivilegeName("pms_task_sequence_form");
            $data = array();
            $data["no_sidenav"] = true;

            $arrData = array();
            $arrData["id"] = $id;
            $this->core_layout->addJs("js/pms/task/vue.component/vue.modal.js", true);
            $this->core_layout->addJs("js/pms/task/vue.component/vue.instance.js", true);
            $this->core_layout->addJs("js/pms/task/sequence_form_task.js", true, $arrData);
            $this->load->view('core/templates/header', $data);
            $this->load->view('pms/project/sequence_form/task');
            $this->load->view('core/templates/footer', $data);
        }else{
            redirect(site_url("pms/project/index"), "refresh");
        }
    }

    function units($id=null){
        if($id){
            $data = array("id"=>$id);
            $this->core_layout->setPrivilegeName("pms_project_masterfile");
            $this->core_layout->addJs("js/pms/project/unit_masterfile.js", true, $data);
            $this->load->view('core/templates/header');
            $this->load->view('pms/project/units', $data);
            $this->load->view('core/templates/footer');
        }else{
            redirect(site_url("pms/project/index"), "refresh");
        }
    }

    function company(){
        $this->core_layout->setPrivilegeName("pms_company_masterfile");
        $this->core_layout->addJs("js/pms/project/company_masterfile.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('pms/project/company');
        $this->load->view('core/templates/footer');
    }

    function location(){
        $this->core_layout->setPrivilegeName("pms_location_masterfile");
        $this->core_layout->addJs("js/pms/project/location_masterfile.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('pms/project/location');
        $this->load->view('core/templates/footer');
    }
    
    function get_project_unit_datatable_request(){
        $data = $this->adm_project->getProjectUnitDatatableRequest();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_project_datatable_request(){
        $data = $this->adm_project->getProjectDatatableRequest();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_company_datatable_request(){
        $data = $this->adm_project->getCompanyDatatableRequest();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_project_location_datatable_request(){
        $data = $this->adm_project->getProjectLocationDatatableRequest();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_project_data($id=null){
        $data = $this->adm_project->getProjectData($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_project_unit_status($id=null){
        $data = $this->adm_project->getProjectUnitStatus($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_project_unit_remarks($id=null){
        $data = $this->adm_project->getProjectUnitRemarks($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_current_data($id=null){
        $data = $this->adm_project->getCurrentData($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_modal_project_unit_status(){
        $data = $this->adm_project->setModalProjectUnitStatus();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_modal_project_unit_remarks(){
        $data = $this->adm_project->setModalProjectUnitRemarks();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_modal_project(){
        $data = $this->adm_project->setModalProject();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_modal_project_unit(){
        $data = $this->adm_project->setModalProjectUnit();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_modal_project_company(){
        $data = $this->adm_project->setModalProjectCompany();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_modal_project_location(){
        $data = $this->adm_project->setModalProjectLocation();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_project_name_select2_data(){
        $data = $this->adm_project->getProjectCompanySelect2Data();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_project_location_select2_data(){
        $data = $this->adm_project->getProjectLocationSelect2Data();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_modal_project(){
        $data = $this->adm_project->updateModalProject();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    
        
    }
    function archive_modal_project(){
        $data = $this->adm_project->archiveModalProject();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_company($id) {
	    echo $this->adm_project->editCompany($id);
    }

    function archive_project_company($id) {
	    echo $this->adm_project->archiveProjectCompany($id);
    }

    function edit_location($id) {
	    echo $this->adm_project->editLocation($id);
    }

    function archive_project_location($id) {
        echo $this->adm_project->archiveProjectLocation($id);
    }
} 