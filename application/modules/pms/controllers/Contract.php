<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Contract extends MY_Controller {
	public function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("pms");
        $this->authenticate->doRedirect();
        $this->load->model("Contract_m","adm_contract");
        $this->core_layout->addJs("global/js/vue-modal/vue.modal.js");
    }

    function do_post_event($function=null){
        $data = $this->adm_contract->doPostEvent($function);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function index(){
        $this->core_layout->setPrivilegeName("pms_contract_masterfile");
        $this->core_layout->addJs("js/pms/contract/vue.component/vue.modal.js", true);
        $this->core_layout->addJs("js/pms/contract/contract_masterfile.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('pms/contract/index');
        $this->load->view('core/templates/footer');
    }

    function new_work_order(){
        $this->adm_contract->clearWorkOrderItemData();
        $this->core_layout->setPrivilegeName("pms_contract_masterfile");
        $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js");
        $this->core_layout->addJs("js/pms/contract/vue.component/vue.modal.js", true);
        $this->core_layout->addJs("js/pms/contract/vue.component/vue.instance.js", true);
        $this->core_layout->addJs("js/pms/contract/new_contract_script.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('pms/contract/work_order/new_wo');
        $this->load->view('core/templates/footer');
    }

    function view_work_order($id=null){
        if($id){
            $arrData = array();
            $arrData["id"] = $id;
            $this->core_layout->setPrivilegeName("pms_contract_masterfile");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js");
            $this->core_layout->addJs("js/pms/contract/vue.component/vue.modal.js", true);
            $this->core_layout->addJs("js/pms/contract/preview_contract_script.js", true, $arrData);
            $this->load->view('core/templates/header');
            $this->load->view('pms/contract/work_order/preview');
            $this->load->view('core/templates/footer');
        }else{
            redirect(site_url("pms/contract"), "refresh");
        }
    }

    function work_order_accomplishment($id=null){
        if($id){
            $arrData = array();
            $arrData["id"] = $id;
            $this->core_layout->setPrivilegeName("pms_contract_masterfile");
            $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js");
            $this->core_layout->addJs("js/pms/contract/vue.component/vue.modal.js", true);
            $this->core_layout->addJs("js/pms/contract/accomplishment_contract_script.js", true, $arrData);
            $this->load->view('core/templates/header');
            $this->load->view('pms/contract/accomplishment/index');
            $this->load->view('core/templates/footer');
        }else{
            redirect(site_url("pms/contract"), "refresh");
        }
    }

    function new_accomplishment($id=null){
        if($id){
            $arrData = array();
            $arrData["id"] = $id;
            $this->core_layout->setPrivilegeName("pms_contract_masterfile");
            $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js");
            $this->core_layout->addJs("js/pms/contract/vue.component/vue.modal.js", true);
            $this->core_layout->addJs("js/pms/contract/new_accomplishment_script.js", true, $arrData);
            $this->load->view('core/templates/header');
            $this->load->view('pms/contract/accomplishment/new_accomplishment');
            $this->load->view('core/templates/footer');
        }else{
            redirect(site_url("pms/contract"), "refresh");
        }
    }

    function edit_work_order($id=null, $wo_task_id=null){
        if($id && $wo_task_id){
            $response = $this->adm_contract->checkActiveWoTaskHistory($id, $wo_task_id);
            if($response){
                $arrData = array();
                $arrData["id"] = $id;
                $arrData["wo_task_id"] = $wo_task_id;
                $this->core_layout->setPrivilegeName("pms_contract_masterfile");
                $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js");
                $this->core_layout->addJs("js/pms/contract/vue.component/vue.modal.js", true);
                $this->core_layout->addJs("js/pms/contract/vue.component/vue.instance.js", true);
                $this->core_layout->addJs("js/pms/contract/edit_contract_script.js", true, $arrData);
                $this->load->view('core/templates/header');
                $this->load->view('pms/contract/work_order/new_wo');
                $this->load->view('core/templates/footer');
            }else{
                redirect(site_url("pms/contract/view_work_order/{$id}"), "refresh");
            }
        }else{
            redirect(site_url("pms/contract"), "refresh");
        }
    }

    function adjustment_work_order($id=null, $wo_task_id=null){
        if($id && $wo_task_id){
            $response = $this->adm_contract->checkActiveWoTaskHistory($id, $wo_task_id);
            if($response){
                $arrData = array();
                $arrData["id"] = $id;
                $arrData["wo_task_id"] = $wo_task_id;
                $this->core_layout->setPrivilegeName("pms_contract_masterfile");
                $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js");
                $this->core_layout->addJs("js/pms/contract/vue.component/vue.modal.js", true);
                $this->core_layout->addJs("js/pms/contract/vue.component/vue.instance.js", true);
                $this->core_layout->addJs("js/pms/contract/adjustment_contract_script.js", true, $arrData);
                $this->load->view('core/templates/header');
                $this->load->view('pms/contract/work_order/new_wo');
                $this->load->view('core/templates/footer');
            }else{
                redirect(site_url("pms/contract/view_work_order/{$id}"), "refresh");
            }
        }else{
            redirect(site_url("pms/contract"), "refresh");
        }
    }

    function additional_work_order($id=null){
        if($id){
            $tempId = $this->adm_contract->getLastActiveWoTaskHistoryId($id);
            if($tempId !== 0){
                $arrData = array();
                $arrData["id"] = $id;
                $arrData["wo_task_id"] = $tempId;
                $arrData["is_additional"] = true;
                $this->core_layout->setPrivilegeName("pms_contract_masterfile");
                $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js");
                $this->core_layout->addJs("js/pms/contract/vue.component/vue.modal.js", true);
                $this->core_layout->addJs("js/pms/contract/vue.component/vue.instance.js", true);
                $this->core_layout->addJs("js/pms/contract/contract_additional_script.js", true, $arrData);
                $this->load->view('core/templates/header');
                $this->load->view('pms/contract/work_order/new_wo');
                $this->load->view('core/templates/footer');
            }else{
                redirect(site_url("pms/contract/view_work_order/{$id}"), "refresh");
            }
        }else{
            redirect(site_url("pms/contract"), "refresh");
        }
    }

    function set_modal_contract(){
        $data = $this->adm_contract->setModalContract();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function generate_modal_contract_data(){
        $data = $this->adm_contract->generateModalContractData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_current_contract_co($id=null){
        $data = $this->adm_contract->getCurrentContractCo($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_checklist_content($id=null){
        $data = $this->adm_contract->getChecklistContent($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_wo_version_template_content($code=null){
        $data = $this->adm_contract->getWoVersionTemplateContent($code);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_select2_blocks(){
        $data = $this->adm_contract->getSelect2Blocks();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_select2_lots(){
        $data = $this->adm_contract->getSelect2Lots();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_select2_block_lot(){
        $data = $this->adm_contract->getSelect2BlockLot();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_select2_units(){
        $data = $this->adm_contract->getSelect2Units();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_co_select2_multiple_units(){
        $data = $this->adm_contract->getCoSelect2MultipleUnits();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_select2_multiple_units(){
        $data = $this->adm_contract->getSelect2MultipleUnits();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function change_order_select2_multiple_units(){
        $data = $this->adm_contract->changeOrderselect2MultipleUnits();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_select2_checklist(){
        $data = $this->adm_contract->getSelect2Checklist();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_select2_project(){
        $data = $this->adm_contract->getSelect2Project();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_select2_wo_version(){
        $data = $this->adm_contract->getSelect2WoVersion();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_version_template(){
        $data = $this->adm_contract->getVersionTemplate();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function generate_contract_code(){
        $data = $this->adm_contract->generateContractCode();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_current_contract($id=null){
        $data = $this->adm_contract->getCurrentContract($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_contract_project($id=null){
        $data = $this->adm_contract->getContractProject($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_contract_extension_data($id=null){
        $data = $this->adm_contract->getContractExtensionData($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_contract_units($id=null){
        $data = $this->adm_contract->getContractUnits($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_temp_item_blocks($id=null){
        $data = $this->adm_contract->getTempItemBlocks($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_temp_task_items($isTemp=false){
        $data = $this->adm_contract->getTempTaskItems($isTemp);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function render_modal_content($function=null, $model="contract_m", $module="pms"){
        $data = $this->core_layout->renderModalContent($module, $model, $function);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function render_form_content($function=null, $model="contract_m", $module="pms"){
        $data = $this->core_layout->renderModalContent($module, $model, $function);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function render_contract_items($id=null, $wo_task_id=null){
        $data = $this->adm_contract->renderContractItems($id, $wo_task_id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_available_project_unit($checklistId=null, $items=array()){
        $tempIds = array();
        if($checklistId && $items){
            $arrids = array();
            $arrItems = array();
            $arrUnits = array();

            $this->db->from("gccpms.sf_item");
            $this->db->where_in("id", $items);
            $this->db->where("parent_id !=", 0);
            $queryItems = $this->db->get();
            if($queryItems->num_rows() > 0){
                foreach ($queryItems->result() as $key => $value) {
                    $arrItems[] = $value->id;
                }
            }

            $arrWhere = array();
            $arrWhere["checklist_id"] = $checklistId;
            $arrWhere["is_active"] = 1;
            $arrWhere["status"] = 1;
            $queryLots = $this->db->get_where("gccpms.sf_project_unit", $arrWhere);
            if($queryLots->num_rows() > 0){
                foreach ($queryLots->result() as $key => $value) {
                    $arrUnits[] = $value->id;
                }
            }

            foreach ($arrItems as $key => $item) {
                $this->db->select("a.item_id, b.unit_id");
                $this->db->from("gccpms.sf_contract_items as a");
                $this->db->join("gccpms.sf_contract_units as b", "b.contract_id = a.contract_id");
                $this->db->where("a.status", 1);
                $this->db->where("a.item_id", $item);
                $query = $this->db->get();
                if($query->num_rows() > 0){
                    foreach ($query->result() as $key => $value) {
                        if(isset($arrids[$value->item_id]) && $arrids[$value->item_id]){
                            if(!in_array($value->unit_id, $arrids[$value->item_id])){
                                $arrids[$value->item_id][] = intval($value->unit_id);
                            }
                        }else{
                            $arrids[$value->item_id][] = intval($value->unit_id);
                        }
                    }
                }
            }

            foreach ($arrUnits as $key => $unit) {
                foreach ($arrItems as $key => $id) {
                if(isset($arrids[$id]) && $arrids[$id]){
                        if(!in_array($unit, $arrids[$id]) && !in_array($unit, $tempIds)){
                            $tempIds[] = $unit;
                        }
                }else{
                    if(!in_array($unit, $tempIds)){
                        $tempIds[] = $unit;
                    }
                }
                }
            }
        }
        
        return $tempIds;
    }

    function get_contract_items($id){
        $items = $this->adm_contract->getCurrentContractItems($id);
        $itemCount = $this->adm_contract->getCurrentContractUnitCount($id);
        echo "<pre>";
        var_dump($items);
        var_dump($itemCount);
    }

    function get_temp_contract_items(){
        $tempItems = array();
        $ids = array();
        $arrData = array();

        $employeeId = $this->core_layout->getCurrentEmployeeId();
        $this->db->from("gccpms.sf_contract_temp_items");
        $this->db->where("user_id", $employeeId);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            foreach ($query->result() as $key => $value) {
                $tempItems[$value->item_id] = $value;
                $ids[] = $value->item_id;
            }
        }

        $this->db->from("gccpms.sf_item");
        $this->db->where("parent_id", 0);
        $this->db->where_in("id", $ids);
        $this->db->order_by("sort", "ASC");
        $tempQuery = $this->db->get();
        if($tempQuery->num_rows() > 0){
            foreach ($tempQuery->result() as $key => $value) {
                $this->db->from("gccpms.sf_item");
                $this->db->where("parent_id", $value->id);
                $this->db->where_in("id", $ids);
                $this->db->order_by("sort", "ASC");
                $queryItem = $this->db->get();
                if($queryItem->num_rows() > 0){
                    if(isset($tempItems[$value->id]) && $tempItems[$value->id]){
                        $xtempData = $tempItems[$value->id];
                        $tempRow = array();
                        $tempRow["id"] = $value->id;
                        $tempRow["label"] = $value->label;
                        $tempRow["qty"] = "";
                        $tempRow["unit"] = "";
                        $tempRow["unit_cost"] = 0;
                        $tempRow["total"] = 0;
                        $tempRow["is_parent"] = 1;
                        $tempRow["lot_count"] = 0;
                        $tempRow["lots"] = array();
                        $arrData[] = $tempRow;
                    }
                    foreach ($queryItem->result() as $kk => $vv) {
                        if(isset($tempItems[$vv->id]) && $tempItems[$vv->id]){
                            $tempLots = array();
                            $xtempData = $tempItems[$vv->id];

                            $xdata = unserialize($xtempData->lots);
                            if($xdata && count($xdata) > 0){
                                $this->db->select("id, block, lot");
                                $this->db->from("gccpms.sf_project_unit");
                                $this->db->where_in("id", $xdata);
                                $this->db->where("status", 1);
                                $this->db->where("is_active", 1);
                                $queryUnit = $this->db->get();
                                if($queryUnit->num_rows() > 0){
                                    foreach ($queryUnit->result() as $key => $value) {
                                        $tempLots[$value->block][] = $value;
                                    }
                                }
                            }

                            $tempTotal = floatval($xtempData->qty) * intval($xtempData->lot_count) * floatval($xtempData->unit_cost);
                            $xtempTotal = number_format($tempTotal, 2, ".", ",");
                            $tempRow = array();
                            $tempRow["id"] = $vv->id;
                            $tempRow["label"] = $vv->label;
                            $tempRow["qty"] = number_format($xtempData->qty, 2, ".", ",");
                            $tempRow["unit"] = $xtempData->unit;
                            $tempRow["unit_cost"] = number_format($xtempData->unit_cost, 2, ".", ",");
                            $tempRow["total"] = $xtempTotal;
                            $tempRow["is_parent"] = 0;
                            $tempRow["lot_count"] = $xtempData->lot_count;
                            $tempRow["lots"] = $tempLots;
                            $arrData[] = $tempRow;
                        }
                    }
                }
            }
        }

        var_dump($arrData);
    }

    function get_contract_temp_items($id=null){
        $data = $this->adm_contract->getContractItemData($id);
        var_dump($data);
    }

    function check_task_history($id=null, $type="edit"){
        $data = $this->adm_contract->checkTaskHistory($id, $type);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_current_contract_accomplishment($id=null){
        $data = $this->adm_contract->getCurrentContractAccomplishment($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
}