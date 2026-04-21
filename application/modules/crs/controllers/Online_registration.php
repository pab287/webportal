<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Online_registration extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->model("Registration_model", "registration");
        }

        public function index()
        {
            $tempData = array();
            $tempData['data']['position'] = $this->registration->select2PositionData();
            $tempData['data']['schools'] = $this->registration->select2SchoolsData();
            $tempData['data']['courses'] = $this->registration->select2CoursesData();
            $this->load->view("core/templates/external/header");
            $this->load->view("crs/registration/index");
            $this->load->view("core/templates/external/footer",$tempData);
        }

        public function hire($id){
            $data = array();
            $data['applicant'] = $this->registration->getApplicantInfo($id);
            $this->core_layout->addJs("js/crs/new_hire.js", true, $data);
            $this->load->view('core/templates/header');
            $this->load->view('crs/nwe_hire');
            $this->load->view('core/templates/footer');
        }

        public function backup()
        {
            $this->load->view("core/templates/external/header");
            $this->load->view("crs/registration/index_backup");
            $this->load->view("core/templates/external/footer");
        }

        public function thank_you()
        {
            $this->load->view("crs/registration/thank_you");
        }

        public function insert_resume(){
            $data = $this->registration->insertResume();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function get_tag(){
            $data = $this->registration->getTag();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function get_school(){
            $data = $this->registration->getSchool();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function get_course(){
            $data = $this->registration->getCourse();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function get_position(){
            $data = $this->registration->getPosition();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function add_temp_file(){
            $data = $this->registration->addTempFile();
            echo json_encode($data);
        }

        public function temp_file_delete(){
            $data = $this->registration->tempFileDel();
            echo json_encode($data);
        }

        public function temp_file_delete_all(){
            $data = $this->registration->tempFileDelAll();
            echo json_encode($data);
        }

        public function insert_application(){
            $data = $this->registration->insert_application();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function insert_information(){
            $data = $this->registration->insert_information();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        public function insert_additional(){
            $data = $this->registration->insert_additional();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function get_data(){
            $data = $this->registration->get_data();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_modal($id = null, $modal = null) {
            $data = $this->registration->getModalContainerContent($id, $modal);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        // saving of modals
        function set_modal_dependents() {
            $data = $this->registration->setModalDependents();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_education() {
            $data = $this->registration->setModalEducation();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_licensure() {
            $data = $this->registration->setModalLicensure();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_driverlicense() {
            $data = $this->registration->setModalDriverLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_work_experience() {
            $data = $this->registration->setModalWorkExperience();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_awards() {
            $data = $this->registration->setModalAwards();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_organization() {
            $data = $this->registration->setModalOrganization();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_trainings() {
            $data = $this->registration->setModalTrainings();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_references() {
            $data = $this->registration->setModalReferences();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_medical_history() {
            $data = $this->registration->setModalMedicalHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        function add_skill() {
            echo json_encode($this->registration->addSkill());
        }
        // saving of modals

        // datatable
        function get_employee_dependents() {
            $data = $this->registration->getDependentsList();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_educational_bg() {
            $data = $this->registration->getEducationalBackgroundList();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_licensure() {
            $data = $this->registration->getEmployeeLicensure();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_driverlicense() {
            $data = $this->registration->getEmployeeDriverLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_work_experience() {
            $data = $this->registration->getEmployeeWorkExperience();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_awards() {
            $data = $this->registration->getEmployeeAwards();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_organization() {
            $data = $this->registration->getEmployeeOrganization();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_training() {
            $data = $this->registration->getEmployeeTraining();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_personal_reference() {
            $data = $this->registration->getEmployeePersonalReference();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_medical_history() {
            $data = $this->registration->getEmployeeMedicalHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_skills() {
            $data = $this->registration->getEmployeeSkills();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        // datatable

        // update modals
        function open_edit_modal() {
            $data = array();
            $formData = $this->input->post('formData');
            $init_modal_data_function = $this->input->post('init_modal_data_function');

            $data['html'] = $this->registration->openModal();

            if ($init_modal_data_function) {
                $data['info'] = $this->registration->$init_modal_data_function($formData);
            }
            echo json_encode($data);
        }

        function update_dependent() {
            $post = $this->input->post();
            $data = $this->registration->updateDependent($post);
            echo json_encode($data);
        }

        function update_educational_background() {
            $post = $this->input->post();
            $data = $this->registration->updateEducationalBackground($post);
            echo json_encode($data);
        }

        function update_licensure() {
            $post = $this->input->post();
            $data = $this->registration->updateLicensure($post);
            echo json_encode($data);
        }

        function update_driverlicense() {
            $post = $this->input->post();
            $data = $this->registration->updateDriverLicense($post);
            echo json_encode($data);
        }

        function update_work_experience() {
            $post = $this->input->post();
            $data = $this->registration->updateWorkExperience($post);
            echo json_encode($data);
        }

        function update_award() {
            $post = $this->input->post();
            $data = $this->registration->updateAward($post);
            echo json_encode($data);
        }

        function update_organization() {
            $post = $this->input->post();
            $data = $this->registration->updateOrganization($post);
            echo json_encode($data);
        }

        function update_training() {
            $post = $this->input->post();
            $data = $this->registration->updateTraining($post);
            echo json_encode($data);
        }

        function update_personal_references() {
            $post = $this->input->post();
            $data = $this->registration->updatePersonalReferences($post);
            echo json_encode($data);
        }

        function update_medical_record() {
            $post = $this->input->post();
            $data = $this->registration->updateMedicalRecord($post);
            echo json_encode($data);
        }

        function update_skill() {
            $post = $this->input->post();
            $data = $this->registration->updateSkill($post);
            echo json_encode($data);
        }
        // update modals

        // remove modal
        function open_confirm_modal() {
            $data = $this->registration->openModal();
            echo $data;
        }
        // remove modal

        // remove
        function remove_dependent($dependent_id) {
            $data = $this->registration->removeDependent($dependent_id);
            echo json_encode($data);
        }

        function remove_educational_background($educ_id) {
            $data = $this->registration->removeEducationalBackground($educ_id);
            echo json_encode($data);
        }

        function remove_licensure($licensure_id) {
            $data = $this->registration->removeLicensure($licensure_id);
            echo json_encode($data);
        }

        function remove_driverlicense($driverlicense_id) {
            $data = $this->registration->removeDriverLicense($driverlicense_id);
            echo json_encode($data);
        }

        function remove_worK_experience($work_exp_id) {
            $data = $this->registration->removeWorkExperience($work_exp_id);
            echo json_encode($data);
        }

        function remove_award($award_id) {
            $data = $this->registration->removeAward($award_id);
            echo json_encode($data);
        }

        function remove_organization($org_id) {
            $data = $this->registration->removeOrganization($org_id);
            echo json_encode($data);
        }

        function remove_training($training_id) {
            $data = $this->registration->removeTraining($training_id);
            echo json_encode($data);
        }

        function remove_personal_reference($references_id) {
            $data = $this->registration->removePersonalReference($references_id);
            echo json_encode($data);
        }

        function remove_medical_record($med_id) {
            $data = $this->registration->removeMedicalRecord($med_id);
            echo json_encode($data);
        }

        function remove_skill($skill_id = null) {
            $data = $this->registration->remove_skill($skill_id);
            echo json_encode($data);
        }

        public function insert_uploaded_resume(){
            $data = $this->registration->insertUploadedResume();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        public function check_personal_reference(){
            $data = $this->registration->check_personal_reference();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function submit_application(){
            $data = $this->registration->submitAppilication();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function validate_application(){
            $data = $this->registration->validateApplication();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

    }