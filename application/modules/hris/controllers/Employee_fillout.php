<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Employee_fillout extends MY_Controller
    {
        function __construct()
        {
            parent::__construct();
            date_default_timezone_set('Asia/Manila');
            
            $this->load->model('ExternalForm_model', 'external');
        }

        public function index(){
            $this->core_layout->addJs("js/hris/employee_fillout.js", false);
            $this->core_layout->addCss("css/custom.css", false);

            $this->core_layout->setPageTitle("Internal");
            $this->load->view("core/templates/external/header");
            $this->load->view("hris/employee_fillout/index");
            $this->load->view("core/templates/external/footer");
        }

        public function get_personnel_request_data(){
            $data = $this->external->getPersonnelRequestData();
            echo json_encode($data);
        }

        public function save_data(){
            $data = $this->external->saveData();
            echo json_encode($data);
        }

        public function update_additional(){
            $data = $this->external->update_additional();
            echo json_encode($data);
        }

        public function get_data(){
            $data = $this->external->get_data();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function test(){
            $this->core_layout->addJs("js/hris/employee_fillout.js", false);
            $this->core_layout->addCss("css/custom.css", false);

            $this->load->view("core/templates/external/header");
            $this->load->view("hris/employee_fillout/index_backup");
            $this->load->view("core/templates/external/footer");
        }

        function get_modal($id = null, $modal = null) {
            $data = $this->external->getModalContainerContent($id, $modal);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function open_edit_modal() {
            $data = array();
            $formData = $this->input->post('formData');
            $init_modal_data_function = $this->input->post('init_modal_data_function');

            $data['html'] = $this->external->openModal();

            if ($init_modal_data_function) {
                $data['info'] = $this->external->$init_modal_data_function($formData);
            }
            echo json_encode($data);
        }

        // saving of modals
        function set_modal_dependents() {
            $data = $this->external->setModalDependents();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_education() {
            $data = $this->external->setModalEducation();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_licensure() {
            $data = $this->external->setModalLicensure();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_driverlicense() {
            $data = $this->external->setModalDriverLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_work_experience() {
            $data = $this->external->setModalWorkExperience();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_awards() {
            $data = $this->external->setModalAwards();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_organization() {
            $data = $this->external->setModalOrganization();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_trainings() {
            $data = $this->external->setModalTrainings();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_references() {
            $data = $this->external->setModalReferences();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_medical_history() {
            $data = $this->external->setModalMedicalHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        function add_skill() {
            echo json_encode($this->external->addSkill());
        }
        // saving of modals

        // datatable
        function get_employee_dependents() {
            $data = $this->external->getDependentsList();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_educational_bg() {
            $data = $this->external->getEducationalBackgroundList();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_licensure() {
            $data = $this->external->getEmployeeLicensure();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_driverlicense() {
            $data = $this->external->getEmployeeDriverLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_work_experience() {
            $data = $this->external->getEmployeeWorkExperience();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_awards() {
            $data = $this->external->getEmployeeAwards();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_organization() {
            $data = $this->external->getEmployeeOrganization();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_training() {
            $data = $this->external->getEmployeeTraining();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_personal_reference() {
            $data = $this->external->getEmployeePersonalReference();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_medical_history() {
            $data = $this->external->getEmployeeMedicalHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_skills() {
            $data = $this->external->getEmployeeSkills();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        // datatable

        // update modals
        function update_dependent() {
            $post = $this->input->post();
            $data = $this->external->updateDependent($post);
            echo json_encode($data);
        }

        function update_educational_background() {
            $post = $this->input->post();
            $data = $this->external->updateEducationalBackground($post);
            echo json_encode($data);
        }

        function update_licensure() {
            $post = $this->input->post();
            $data = $this->external->updateLicensure($post);
            echo json_encode($data);
        }

        function update_driverlicense() {
            $post = $this->input->post();
            $data = $this->external->updateDriverLicense($post);
            echo json_encode($data);
        }

        function update_work_experience() {
            $post = $this->input->post();
            $data = $this->external->updateWorkExperience($post);
            echo json_encode($data);
        }

        function update_award() {
            $post = $this->input->post();
            $data = $this->external->updateAward($post);
            echo json_encode($data);
        }

        function update_organization() {
            $post = $this->input->post();
            $data = $this->external->updateOrganization($post);
            echo json_encode($data);
        }

        function update_training() {
            $post = $this->input->post();
            $data = $this->external->updateTraining($post);
            echo json_encode($data);
        }

        function update_personal_references() {
            $post = $this->input->post();
            $data = $this->external->updatePersonalReferences($post);
            echo json_encode($data);
        }

        function update_medical_record() {
            $post = $this->input->post();
            $data = $this->external->updateMedicalRecord($post);
            echo json_encode($data);
        }

        function update_skill() {
            $post = $this->input->post();
            $data = $this->external->updateSkill($post);
            echo json_encode($data);
        }
        // update modals

        // remove modal
        function open_confirm_modal() {
            $data = $this->external->openModal();
            echo $data;
        }
        // remove modal

        // remove
        function remove_dependent($dependent_id) {
            $data = $this->external->removeDependent($dependent_id);
            echo json_encode($data);
        }

        function remove_educational_background($educ_id) {
            $data = $this->external->removeEducationalBackground($educ_id);
            echo json_encode($data);
        }

        function remove_licensure($licensure_id) {
            $data = $this->external->removeLicensure($licensure_id);
            echo json_encode($data);
        }

        function remove_driverlicense($driverlicense_id) {
            $data = $this->external->removeDriverLicense($driverlicense_id);
            echo json_encode($data);
        }

        function remove_worK_experience($work_exp_id) {
            $data = $this->external->removeWorkExperience($work_exp_id);
            echo json_encode($data);
        }

        function remove_award($award_id) {
            $data = $this->external->removeAward($award_id);
            echo json_encode($data);
        }

        function remove_organization($org_id) {
            $data = $this->external->removeOrganization($org_id);
            echo json_encode($data);
        }

        function remove_training($training_id) {
            $data = $this->external->removeTraining($training_id);
            echo json_encode($data);
        }

        function remove_personal_reference($references_id) {
            $data = $this->external->removePersonalReference($references_id);
            echo json_encode($data);
        }

        function remove_medical_record($med_id) {
            $data = $this->external->removeMedicalRecord($med_id);
            echo json_encode($data);
        }

        function remove_skill($skill_id = null) {
            $data = $this->external->remove_skill($skill_id);
            echo json_encode($data);
        }

        public function insert_uploaded_resume(){
            $data = $this->external->insertUploadedResume();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        public function check_personal_reference(){
            $data = $this->external->check_personal_reference();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
    }
    