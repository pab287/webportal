<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Task extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authenticate->setModuleAccess("pms");
        $this->authenticate->doRedirect();
        $this->load->model("Task_m", "task");
        $this->load->model("ams/Utilities_model", "utilities");
        $this->core_layout->addJs("global/js/vue-modal/vue.modal.js");
    }

    public function sequence_form()
    {
        $this->core_layout->setPrivilegeName("pms_task_sequence_form");
        $this->core_layout->addJs("js/pms/task/sequence_form.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('pms/task/sequence_form');
        $this->load->view('core/templates/footer');
    }

    public function items()
    {
        $this->core_layout->setPrivilegeName("pms_task_items");
        $this->core_layout->addJs("js/pms/task/sequence_items.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('pms/task/sequence_item');
        $this->load->view('core/templates/footer');
    }

    public function checklist_items()
    {
        $this->core_layout->setPrivilegeName("pms_task_checklist_items");
        $this->core_layout->addJs("js/pms/task/sequence_checklist.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('pms/task/sequence_checklist');
        $this->load->view('core/templates/footer');
    }

    public function checklist_item_qty($id=null){
        if($id){
            $arrData = array();
            $arrData["id"] = $id;
            $this->core_layout->setPrivilegeName("pms_task_checklist_items");
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js");
            $this->core_layout->addJs("js/pms/task/vue.component/vue.modal.js", true);
            $this->core_layout->addJs("js/pms/task/sequence_checklist_qty.js", true, $arrData);
            $this->load->view('core/templates/header');
            $this->load->view('pms/task/checklist_item_qty');
            $this->load->view('core/templates/footer');
        }else{
            redirect(site_url("pms/task/checklist_items"), "refresh");
        }
    }

    function do_post_event($function = null)
    {
        $data = $this->task->doPostEvent($function);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function render_task_duration_form($itemId = null, $unitId = null)
    {
        $data = $this->task->renderTaskDurationForm($itemId, $unitId);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_task_status()
    {
        $data = $this->task->updateTaskStatus();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_task_remarks()
    {
        $data = $this->task->updateTaskRemarks();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function render_contract_form($id = null, $unitId = null)
    {
        $data = $this->task->renderContractForm($id, $unitId);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function render_task_form($id = null, $unitId = null)
    {
        $data = $this->task->renderTaskForm($id, $unitId);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function render_subtask_form($id = null)
    {
        $data = $this->task->renderSubtaskForm($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_selected_task_data()
    {
        $data = $this->task->getSelectedTaskData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function generate_current_task()
    {
        $data = $this->task->generateCurrentTask();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function generate_parent_node()
    {
        $data = $this->task->generateParentNode();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function save_sequence_form_data()
    {
        $data = $this->task->saveSequenceFormData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_task_codes($id = null)
    {
        $data = $this->task->getTaskCodes($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_child_task_codes($id = null)
    {
        $data = $this->task->getChildTaskCodes($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_select2_task_contractor($id = null, $unitId = null)
    {
        $data = $this->task->getSelect2TaskContractor($id, $unitId);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_current_task_data($id = null)
    {
        $data = $this->task->getCurrentTaskData($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_sequence_form_data($id = null)
    {
        $data = $this->task->getSequenceFormData($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_sequence_form_grid()
    {
        $data = $this->task->getSequenceFormGrid();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_assigned_sequence_items()
    {
        $this->core_layout->setPrivilegeName("pms_task_checklist_items");
        $data = $this->task->getAssignedSequenceItems();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_sequence_item_datatable_request()
    {
        $data = $this->task->getSequenceItemDatatableRequest();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_checklist_item_datatable_request()
    {
        $data = $this->task->getChecklistItemDatatableRequest();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function set_modal_subtask_activity()
    {
        $data = $this->task->setModalSubtaskActivity();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function set_modal_task_item()
    {
        $data = $this->task->setModalTaskItem();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function set_modal_sequence_item()
    {
        $data = $this->task->setModalSequenceItem();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function set_modal_checklist_item()
    {
        $data = $this->task->setModalChecklistItem();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function set_modal_sequence_block()
    {
        $data = $this->task->setModalSequenceBlock();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function set_checklist_json_actions()
    {
        $data = $this->task->setChecklistJsonActions();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function list_sequence_item()
    {
        $this->core_layout->setPrivilegeName("pms_task_items");
        $data = $this->task->listSequenceItem();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_json_sequence_items()
    {
        $data = $this->task->getJsonSequenceItems();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_select2_template_data($id=null)
    {
        $data = $this->task->getSelect2TemplateData($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_select2_requested_task()
    {
        $data = $this->task->getSelect2RequestedTask();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function render_modal_content($function = null, $model = "task_m", $module = "pms")
    {
        $data = $this->core_layout->renderModalContent($module, $model, $function);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function generate_past_due_task($id = null)
    {
        $data = $this->task->generatePastDueItems($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function generate_todo_task($id = null)
    {
        $data = $this->task->generateTodoTask($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function render_task_punchlist_logs($id = null)
    {
        $data = $this->task->renderPunchlistLogs($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function render_punchlisted_items($id = null)
    {
        $data = $this->task->renderPunchlistedItems($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function generate_punchlist_task($id = null)
    {
        $data = $this->task->generatePunchlistTask($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function generate_punchlist_task_log($id = null)
    {
        $data = $this->task->generatePunchlistTaskLog($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function open_modal()
    {
        $data = array();
        $formData = $this->input->post('formData');
        $init_modal_data_function = $this->input->post('init_modal_data_function');
        $model = $this->input->post('model');

        $data['html'] = $this->utilities->openModal();

        if ($init_modal_data_function) {
            $data['info'] = $this->task->$init_modal_data_function($formData);
        }

        echo json_encode($data);
    }

    function edit_task_item($id)
    {
        echo json_encode($this->task->editTaskItem($id));
    }

    function archive_item($id)
    {
        echo json_encode($this->task->archiveItem($id));
    }

    function add_checklist_item()
    {
        echo json_encode($this->task->addChecklistItem());
    }

    function edit_checklist_item($id)
    {
        echo json_encode($this->task->editChecklistItem($id));
    }

    function archive_checklist_item($id) {
        echo json_encode($this->task->archiveChecklistItem($id));
    }

    function update_checklist_rate() {
        echo json_encode($this->task->updateChecklistRate());
    }

    function get_rate_card_updates($checklist_id) {
        echo json_encode($this->task->getRateCardUpdates($checklist_id));
    }

    function clear_temp_requested_qty($id=null){
        $data = $this->task->clearTempRequestedQty($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function set_all_temp_requested_qty($id=null){
        $data = $this->task->setAllTempRequestedQty($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function submit_temp_requested_qty($id=null){
        $data = $this->task->submitTempRequestedQty($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function clear_all_temp_requested_qty($id=null){
        $data = $this->task->clearAllTempRequestedQty($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_checklist_data($id=null){
        $data = $this->task->getChecklistData($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
}