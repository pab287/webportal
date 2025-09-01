<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Calendar extends MY_Controller
    {
        function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("hris");
            $this->authenticate->doRedirect();

            $this->load->model("Holiday_model", "holiday");
            $this->load->model("Employee_model", "employee");
            $this->load->model("ams/Utilities_model", "utilities");
            $this->load->model('Company_model', 'company');
            $this->load->model('Department_model', 'department');
            date_default_timezone_set('Asia/Manila');
        }

        public function holidays()
        {
            $this->core_layout->addJs("vendors/custom/fullcalendar/fullcalendar.bundle.js", true);
            $this->core_layout->addCss("vendors/custom/fullcalendar/fullcalendar.bundle.css", true);
            $this->core_layout->addJs("js/hris/calendar/calendar_of_holidays_script.js", true);
            $this->core_layout->addCss("css/hris/calendar.css", true);

            $data['years'] = $this->holiday->getYearsOfExistingHolidays();
            $this->core_layout->setPrivilegeName("hris_cal_of_holidays");
            $this->load->view("core/templates/header");
            $this->load->view("masterfile/calendar/calendar_of_holidays/index", $data, FALSE);
            $this->load->view("core/templates/footer");
        }

        public function probationary()
        {
            $this->core_layout->setPrivilegeName("hris_cal_of_probationary");
            $this->core_layout->addJs("vendors/custom/fullcalendar/fullcalendar.bundle.js", true);
            $this->core_layout->addJs("js/hris/calendar/calendar_of_probationary_script.js", true);
            $this->core_layout->addCss("vendors/custom/fullcalendar/fullcalendar.bundle.css", true);
            $this->core_layout->addCss("css/hris/calendar.css", true);

            $this->load->view("core/templates/header");
            $this->load->view("masterfile/calendar/calendar_of_probationary_employees/index");
            $this->load->view("core/templates/footer");
        }

        function open_edit_modal()
        {
            $data = array();
            $formData = $this->input->post('formData');
            $data['html'] = $this->utilities->openModal();

            echo json_encode($data);
        }

        function open_confirm_modal()
        {
            $data = $this->utilities->openModal();
            echo $data;
        }


        public function get_holidays($classification = null)
        {
            $data = $this->holiday->getHolidays($classification);
            echo json_encode($data);
        }

        public function get_holidays_tabular()
        {
            $data = $this->holiday->getHolidaysTabular();
            echo json_encode($data);
        }

        public function save_holiday()
        {
            $data = $this->holiday->saveHoliday();
            echo json_encode($data);
        }

        public function update_holiday()
        {
            $data = $this->holiday->updateHoliday();
            echo json_encode($data);
        }

        public function delete_holiday($holiday_id)
        {
            $data = $this->holiday->deleteHoliday($holiday_id);
            echo json_encode($data);
        }

        public function get_years_of_existing_holidays()
        {
            $data = $this->holiday->getYearsOfExistingHolidays();
            echo json_encode($data);
        }

        public function get_calendar_of_probationary_employees()
        {
            $data = $this->employee->getCalendarOfProbationaryEmployees();
            echo json_encode($data);
        }

        public function add_probee_evaluation() {
            $data = $this->employee->addProbeeEvaluation();
            echo json_encode($data);
        }

        public function get_holiday_classification() {
            echo json_encode($this->holiday->getHolidayClassification());
        }

        public function get_company(){
            $data = $this->company->getCompany();
            echo json_encode($data);
        }

        public function get_department(){
            $data = $this->department->getDepartment();
            echo json_encode($data);
        }

        public function company_events_calendar(){
            $data = array();
            $this->core_layout->setPageTitle("HRIS - Event Calendar");
            $this->core_layout->setPrivilegeName("company_events_calendar");
            $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addJs("vendors/custom/fullcalendar/fullcalendar.bundle.js", true);
            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
            $data['events'] = $this->holiday->getEvents();
            $this->core_layout->addJs("js/hris/calendar/calendar_of_events.js", true,$data);
            $this->core_layout->addCss("vendors/custom/fullcalendar/fullcalendar.bundle.css", true);
            $this->core_layout->addCss("css/hris/calendar.css", true);
            $this->load->view("core/templates/header");
            $this->load->view("masterfile/calendar/calendar_of_events/index");
            $this->load->view("core/templates/footer");
        }

        public function add_participants($id){
            $this->core_layout->setPageTitle("HRIS - Event Calendar");
            $data = array();
            // $data['event_id'] = $id;
            // $data['event_details'] = $this->holiday->getEventDetails($id);
            // $data['employees'] = $this->employee->getActiveEmployees();
            // $data['companies'] = $this->company->getCompany();
            // $data['departments'] = $this->department->getDepartment();
            $this->core_layout->setPrivilegeName("company_events_calendar");
            $this->core_layout->addCss("plugins/select2/select2.css");
            $this->core_layout->addJs("plugins/select2/select2.full.min.js");
            $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.min.js', true);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
            $this->core_layout->addJs("js/hris/calendar/add_participants_script.js", true);
            $this->core_layout->addCss("css/hris/calendar.css", true);
            $this->load->view("core/templates/header");
            $this->load->view("masterfile/calendar/calendar_of_events/participants_page", $data);
            $this->load->view("core/templates/footer");
        }

        public function save_event(){
            $data = $this->holiday->saveEvent();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_events_tabular(){
            $data = $this->holiday->getEventsTabular();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function update_event(){
            $data = $this->holiday->updateEvent();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function archive_event(){
            $data = $this->holiday->archiveEvent();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

    }