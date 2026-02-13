<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends MX_Controller {
    public function __construct() {
        parent::__construct();
        $this->authenticate->setModuleAccess("events_module");
        $this->authenticate->doRedirect();
        $this->load->model("Events_model", "em");
        $this->load->model("hris/employee_model");
    }

    public function index() {
        $data = array();
        $this->core_layout->setPageTitle("Training - Training Calendar");
        $this->core_layout->setPrivilegeName("company_events");
        $this->core_layout->setHeaderTitle("schedule");
        $this->core_layout->setCrumbTitle("training");
        $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
        $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
        $this->core_layout->addJs("vendors/custom/fullcalendar/fullcalendar.bundle.js", true);
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
        $data["company"] = $this->em->select2CompanyData();
        $data["department"] = $this->em->select2DepartmentData();
        $data['events'] = $this->em->getEvents();
        $data['options'] = $this->em->getEventsOptions();
        $this->core_layout->addJs("js/events/calendar_of_events.js", true,$data);
        $this->core_layout->addCss("vendors/custom/fullcalendar/fullcalendar.bundle.css", true);
        $this->core_layout->addCss("css/hris/calendar.css", true);
        $this->load->view("core/templates/header");
        $this->load->view("index");
        $this->load->view("core/templates/footer");
    }

    public function add_participants($id){
        $this->core_layout->setPrivilegeName("company_events");
        $actions = $this->core_layout->getCurrentActions();
        if (in_array("view_own_request", $actions)) {
            redirect(base_url("events"), "refresh");
        }
        $this->core_layout->setPageTitle("EVENTS - Event Calendar");
        $data = array();
        $data["company"] = $this->em->select2CompanyData();
        $data['event_details'] = $this->em->getEventDetails($id);
        $data['participants'] = $this->em->getEventParticipants($id);
        $data['employees'] = $this->em->getEmployeeSelection($id);
        $data['attachments'] = $this->em->getEventAttachments($id);
        $data['schedule'] = $this->em->getEventSchedule($id);
        $data['options'] = $this->em->getEventsOptions();
        $this->core_layout->addCss("css/buttons.dataTables.min.css", true);
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
        $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.min.js', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
        $this->core_layout->addJs("global/js/xlsx.full.min.js", true);
        $this->core_layout->addJs("js/events/add_participants_script.js", true, $data);
        $this->core_layout->addCss("css/hris/calendar.css", true);
        $this->load->view("core/templates/header");
        $this->load->view("participants_page");
        $this->load->view("core/templates/footer");
    }

    public function archive(){
        $this->core_layout->setPageTitle("Archive");
        $this->core_layout->setPrivilegeName("company_events");
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
        $this->core_layout->addJs("js/events/events_archive.js",true);
        $this->load->view("core/templates/header");
        $this->load->view("archive_page");
        $this->load->view("core/templates/footer");
    }

    public function settings(){
        $this->core_layout->setPageTitle("Settings");
        $this->core_layout->setPrivilegeName("event_settings");
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
        $this->core_layout->addJs("js/events/event_settings.js",true);
        $this->load->view("core/templates/header");
        $this->load->view("settings_page");
        $this->load->view("core/templates/footer");
    }

    public function get_events_tabular(){
        $data = $this->em->getEventsTabular();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function save_event(){
        $data = $this->em->saveEvent();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function update_event(){
        $data = $this->em->updateEvent();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function archive_event(){
        $data = $this->em->archiveEvent();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function restore_event(){
        $data = $this->em->restoreEvent();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function get_employee_information(){
        $data = $this->em->getEmployeeInformation();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function save_participant(){
        $data = $this->em->saveParticipant();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function update_participant(){
        $data = $this->em->updateParticipant();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function confirm_participant(){
        $data = $this->em->confirmParticipant();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function decline_participant(){
        $data = $this->em->declineParticipant();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function archive_participant(){
        $data = $this->em->archiveParticipant();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function get_modal_training($id = null) {
        $data = $this->employee_model->getModalContainerContent($id, "training");
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function upload_employee_training() {
        $data = $this->em->uploadEmployeeTraining();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function set_modal_trainings() {
        $data = $this->em->setModalTrainings();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function upload_documents(){
        $data = $this->em->uploadDocuments();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function remove_file(){
        $data = $this->em->removeFile();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function new_event_sched(){
        $data = $this->em->newEventSched();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function update_schedule(){
        $data = $this->em->updateSchedule();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function delete_schedule(){
        $data = $this->em->deleteSchedule();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function assign_schedule(){
        $data = $this->em->assignEventSched();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function assign_participant(){
        $data = $this->em->assignParticipant();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function unassign_participant(){
        $data = $this->em->unassignParticipant();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function take_attendance(){
        $data = $this->em->takeAttendance();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function update_attendance(){
        $data = $this->em->updateAttendance();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function check_attendance(){
        $data = $this->em->checkAttendance();
        $data['modal'] = $this->employee_model->getModalContainerContent($data['participant_id'], "training");
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function remove_certificate(){
        $data = $this->em->removeCertificate();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function get_events_settings(){
        $data = $this->em->getEventsSettings();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function new_events_settings(){
        $data = $this->em->newEventsSettings();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function update_events_settings(){
        $data = $this->em->updateEventsSettings();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function archive_event_settings(){
        $data = $this->em->archiveEventSettings();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function update_budget(){
        $data = $this->em->updateBudget();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

}