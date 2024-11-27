<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Work_order extends MY_Controller {
	public function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("pms");
        $this->authenticate->doRedirect();
        $this->load->model("Work_order_m","adm_wo");
    }

    function wo_type(){
        $this->core_layout->setPrivilegeName("pms_wo_type_masterfile");
        $this->core_layout->addJs("js/pms/work_order/wo_type.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('pms/work_order/wo_type');
        $this->load->view('core/templates/footer');
    }
    
    function get_wo_type_datatable_request(){
        $data = $this->adm_wo->getWoTypeDatatableRequest();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_modal_wo_type(){
        $data = $this->adm_wo->setModalWoType();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_wo_type_select2_data($field=null){
        $data = $this->adm_wo->getWoTypeSelect2Data($field);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_work_order_type($id) {
	    echo json_encode($this->adm_wo->editWorkOrderType($id));
    }

    function archive_work_order_type($id) {
	    echo json_encode($this->adm_wo->archiveWorkOrderType($id));
    }
}