<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Crs extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("crs");
            $this->authenticate->doRedirect();

            $this->load->model("tag_model", "tag");
            $this->load->model("school_model", "school");
            $this->load->model("course_model", "course");
            $this->load->model("document_model", "document");
            $this->load->model("ams/Utilities_model", "utilities");
            $this->load->model("reports_model", "reports");
            $this->load->model("Registration_model", "registration");
            $this->user_data = $this->session->userdata("logged_in");
        }

        public function index(){
            $this->core_layout->setPrivilegeName("crs_dashboard");
            $this->core_layout->addJs("js/ts/amschart/amschart.js", true);
            $this->core_layout->addJs("js/ts/amschart/amschart_theme.js", true);
            $this->core_layout->addJs("js/ts/amschart/amschart_chart.js", true);
            $this->core_layout->addJs("global/js/jquery-ui-1.10.1/ui/jquery.ui.core.js", true);
            $this->core_layout->addJs("global/js/jquery-ui-1.10.1/ui/jquery.ui.position.js", true);
            $this->core_layout->addJs("global/js/jquery-ui-1.10.1/ui/jquery.ui.widget.js", true);
            $this->core_layout->addJs("global/js/jquery-ui-1.10.1/ui/jquery.ui.menu.js", true);
            $this->core_layout->addJs("global/js/jquery-ui-1.10.1/ui/jquery.ui.autocomplete.js", true);
            $this->core_layout->addJs("global/js/jquery-migrate-3.0.0.min.js", true);

            $this->core_layout->addJs("js/crs/dashboard.js", true);
            $this->core_layout->addJs("js/crs/search_document_script.js", true);
            $this->core_layout->addCss('css/crs/index.css', TRUE);
            
            $this->load->view('core/templates/header');
            $this->load->view('crs/index');
            $this->load->view('core/templates/footer');
        }

        public function tags(){
            $this->core_layout->setPrivilegeName("crs_tags");
            $this->core_layout->addJs("js/crs/tags.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/tags');
            $this->load->view('core/templates/footer');
        }

        function get_tag_collection()
        {
            $data = $this->tag->getTagCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function school(){
            $this->core_layout->setPrivilegeName("crs_school");
            $this->core_layout->addJs("js/crs/school.js", true);
            
            $this->load->view('core/templates/header');
            $this->load->view('crs/school');
            $this->load->view('core/templates/footer');
        }
        
        public function forinterview(){
            $this->core_layout->setPrivilegeName("crs_forinterview");
            $this->core_layout->addJs("js/crs/for_interview.js", true);
            $this->core_layout->addCss("css/crs/index.css", true);
            $this->core_layout->addJs("js/buttons.html5.min.js", true);
            $this->core_layout->addJs("js/buttons.print.min.js", true);
            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

            $this->load->view('core/templates/header');
            $this->load->view('crs/for_interview');
            $this->load->view('core/templates/footer');
        }

        public function online_application(){
            $tempData = array();
            $tempData['referral'] = $this->registration->select2RefferalData();
            $tempData['position'] = $this->registration->select2PositionData();
            $this->core_layout->setPrivilegeName("crs_masterfile");
            $this->core_layout->addJs("js/crs/online_application.js", true, $tempData);
            
            $this->load->view('core/templates/header');
            $this->load->view('crs/online_application');
            $this->load->view('core/templates/footer',);
        }
        
        function get_school_collection()
        {
            $data = $this->school->getSchoolCollection();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        public function course(){
            $this->core_layout->setPrivilegeName("crs_course");
            $this->core_layout->addJs("js/crs/course.js", true);
            
            $this->load->view('core/templates/header');
            $this->load->view('crs/course');
            $this->load->view('core/templates/footer');
        }
        
        function get_course_collection()
        {
            $data = $this->course->getCourseCollection();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        public function resume(){
            $this->core_layout->setPrivilegeName("crs_resume");
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addCss("css/crs/index.css", true);
            /*$this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-noscript.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui-noscript.css", true);*/
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("js/pdfmake.min.js", true);
            $this->core_layout->addJs("js/vfs_fonts.js", true);
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addJs("js/buttons.html5.min.js", true);
            $this->core_layout->addJs("js/buttons.print.min.js", true);
            $this->core_layout->addJs("js/crs/resume.js", true);
            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

            $this->load->view('core/templates/header');
            $this->load->view('crs/resume');
            $this->load->view('core/templates/footer');
        }

        public function hired(){
            $this->core_layout->setPrivilegeName("crs_hired");
            $this->core_layout->addJs("js/crs/hired.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/hired');
            $this->load->view('core/templates/footer');
        }

        function get_resume_collection(){
            $data = $this->document->getResumeCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function reports(){
            $this->core_layout->setPrivilegeName("crs_reports");
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addCss("css/crs/index.css", true);
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("js/buttons.html5.min.js", true);
            $this->core_layout->addJs("js/buttons.print.min.js", true);
            $this->core_layout->addJs("js/crs/resume.js", true);
            $this->core_layout->addJs("js/crs/reports.js", true);
            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
            $this->load->view('core/templates/header');
            $this->load->view('crs/reports');
            $this->load->view('core/templates/footer');
          }

        public function get_crs_report(){
          $data = $this->reports->getCrsReport();
          $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        function get_hired_collection()
        {
            $data = $this->document->getHiredCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_hired_resume($id=null)
        {
            $data = $this->document->getHiredResume($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_week_collection()
        {
            $data = $this->document->getWeekCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function archive(){
            $this->core_layout->setPrivilegeName("crs_archive");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("js/crs/archive.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/archive');
            $this->load->view('core/templates/footer');
        }

        function get_archive_collection()
        {
            $data = $this->document->getArchiveCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function pending()
        {
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("js/crs/pending.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/pending');
            $this->load->view('core/templates/footer');
        }

        function get_pending_collection()
        {
            $data = $this->document->getPendingCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function interview()
        {
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("js/crs/interview.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/interview');
            $this->load->view('core/templates/footer');
        }

        function get_interview_collection()
        {
            $data = $this->document->getInterviewCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function hire_resume()
        {
            $data = $this->document->hireResume();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function blacklist()
        {
            $this->core_layout->setPrivilegeName("crs_blacklist");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("js/crs/blacklist.js", true);
            $this->core_layout->addCss("css/crs/index.css", true);
            $this->core_layout->addJs("js/buttons.html5.min.js", true);
            $this->core_layout->addJs("js/buttons.print.min.js", true);
            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
            
            $this->load->view('core/templates/header');
            $this->load->view('crs/blacklist');
            $this->load->view('core/templates/footer');
        }

        function get_blacklist_collection()
        {
            $data = $this->document->getBlacklistCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_for_interview_collection()
        {
            $data = $this->document->getForInterviewCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_tag()
        {
            $data = $this->document->getTag();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_school()
        {
            $data = $this->document->getSchool();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_course()
        {
            $data = $this->document->getCourse();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_position()
        {
            $data = $this->document->getPosition();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_supervisory()
        {
            $data = $this->document->getSupervisory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_managerial()
        {
            $data = $this->document->getManagerial();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_skilled()
        {
            $data = $this->document->getSkilled();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_rank()
        {
            $data = $this->document->getRank();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function count_supervisory()
        {
            $data = $this->document->countSupervisory();

            usort($data, function ($a, $b) {
                return $b["total"] - $a["total"];
            });
            $parts = array_slice($data, 0, 11);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($parts));
        }

        function count_managerial()
        {
            $data = $this->document->countManagerial();
            usort($data, function ($a, $b) {
                return $b["total"] - $a["total"];
            });
            $parts = array_slice($data, 0, 11);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($parts));
        }

        function count_skilled()
        {
            $data = $this->document->countSkilled();
            usort($data, function ($a, $b) {
                return $b["total"] - $a["total"];
            });
            $parts = array_slice($data, 0, 11);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($parts));
        }

        function count_rank()
        {
            $data = $this->document->countRank();
            usort($data, function ($a, $b) {
                return $b["total"] - $a["total"];
            });
            $parts = array_slice($data, 0, 11);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($parts));
        }

        function ajax_get_names()
        {
            if (isset($_GET['term'])) {
                $input = strtolower($_GET['term']);
                $this->document->get_names($input);
            }
        }

        public function add_tag()
        {
            $data = $this->tag->addTag();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function edit_tag($id)
        {
            $data = $this->tag->edit_tag($id);
            echo json_encode($data);
        }

        public function update_tag()
        {
            $data = $this->tag->updateTag();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function delete_tag()
        {
            $data = $this->tag->deleteTag();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function add_school()
        {
            $data = $this->school->saveSchool();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function edit_school($id)
        {
            $data = $this->school->edit_school($id);
            echo json_encode($data);
        }

        public function update_school()
        {
            $data = $this->school->updateSchool();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function delete_school()
        {
            $data = $this->school->deleteSchool();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function add_course()
        {
            $data = $this->course->addCourse();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function edit_course($id)
        {
            $data = $this->course->edit_course($id);
            echo json_encode($data);
        }

        public function update_course(){
            $data = $this->course->updateCourse();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function delete_course()
        {
            $data = $this->course->deleteCourse();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function add_resume()
        {
            $user_id = $this->core_layout->getUserId();
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'firstname' => $this->input->post('firstname'),
                'lastname' => $this->input->post('lastname'),
                'status' => $this->input->post('status'),
                'recruitment' => $this->input->post('recruitment'),
                'referral' => $this->input->post('referral'),
                'applied_dt' => $this->input->post('applied_dt'),
                'description' => $this->input->post('description2'),
                'tag1' => $this->input->post('tag_temp'),
                'school' => $this->input->post('school_temp'),
                'course' => $this->input->post('course_temp'),
                'position' => $this->input->post('position_temp'),
                'filename' => $this->input->post('doc_filename'),
                'created_by' => $user_id,
                'created_dt' => $date,
            );
            $insert = $this->document->save_resume($data);
            echo json_encode(array("status" => TRUE));
        }

        public function edit_resume($id)
        {
            $data = $this->document->edit_resume($id);
            echo json_encode($data);
        }

        public function archive_resume($id)
        {
            $data = array(
                'vacancy_status' => 'archived'
            );
            $this->document->archive(array('id' => $id), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function email_template(){
            $crs_data['crs_data'] = $this->document->crs_data();
            $this->load->view('crs/email_template/crs_online_registration', $crs_data);
        }

        /*public function update_resume($id)
        {
            $user_id = $this->core_layout->getUserId();
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'firstname' => $this->input->post('firstname'),
                'lastname' => $this->input->post('lastname'),
                'status' => $this->input->post('status'),
                'recruitment' => $this->input->post('recruitment'),
                'referral' => $this->input->post('referral'),
                'applied_dt' => $this->input->post('applied_dt'),
                'description' => $this->input->post('description2'),
                'tag1' => $this->input->post('tag_temp'),
                'school' => $this->input->post('school_temp'),
                'course' => $this->input->post('course_temp'),
                'position' => $this->input->post('position_temp'),
                'filename' => $this->input->post('doc_filename'),
                'modify_by' => $user_id,
                'modify_dt' => $date,
            );
            $this->document->update_resume(array('id' => $id), $data);
            echo json_encode(array("status" => TRUE));
        }*/

        public function delete_resume($id)
        {
            return $this->document->delete_resume($id);
            
            // echo json_encode(array("status" => TRUE));
        }

        public function count_resume()
        {

            $count = $this->document->count_resume();
            $total = $this->document->count_total();


            echo json_encode(array("count" => $count, "total" => $total));
        }

        public function count_archive()
        {

            $count = $this->document->count_archive();
            $total = $this->document->count_total();


            echo json_encode(array("count" => $count, "total" => $total));
        }

        public function count_recruitment()
        {

            $mynimo = $this->document->count_mynimo();
            $jobstreet = $this->document->count_jobstreet();
            $walk = $this->document->count_walk();
            $referral = $this->document->count_referral();


            echo json_encode(array("mynimo" => $mynimo, "jobstreet" => $jobstreet, "walk" => $walk, "referral" => $referral));
        }
        //  function crs_files(){
        //     $data = $this->document->CrsFiles();
        // 	$this->output
        //     ->set_content_type('json')
        //     ->set_output(json_encode($data));

        // }
        // function set_files(){
        //     $data = $this->document->setFiles();
        // 	$this->output
        //     ->set_content_type('json');

        //     $html = $this->load->view("crs/modals/content", array("data"=>$data), true);
        //     $resultset["html"] = $html;
        //     echo json_encode($resultset);
        // }

        public function search_employee_document() {
            $searchKey = $this->input->post('searchKey');
            $data = $this->document->searchEmployeeDocument($searchKey);
            echo json_encode($data);
        }

        public function view_resume(){
            $this->core_layout->setPrivilegeName("crs_resume");
            $this->core_layout->addCss("css/crs/index.css", true);
            $this->core_layout->addJs("js/crs/view_resume.js", true);
            $this->core_layout->addCss("css/hris/view_employee_masterfile.css", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/view_resume');
            $this->load->view('core/templates/footer');
        }

        public function new_resume() {
            $data = $this->document->newResume();
            echo json_encode($data);
        }

        public function new_hire() {
            $this->core_layout->setPrivilegeName("crs_resume");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addJs("js/crs/new_hire.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/new_hire');
            $this->load->view('core/templates/footer');
        }

        public function update_resume() {
            $data = $this->document->updateResume();
            echo json_encode($data);
        }

        public function get_current_resume($id=null) {
            $data = $this->document->getCurrentResume($id);
            echo json_encode($data);
        }

        public function get_select2_request_position() {
            $data = $this->document->getSelect2RequestPosition();
            echo json_encode($data);
        }

        public function add_temp_file(){
            $data = $this->document->addTempFile();
            echo json_encode($data);
        }

        public function add_new_temp_file(){
            $data = $this->document->addNewTempFile();
            echo json_encode($data);
        }

        public function edit_new_temp_file(){
            $data = $this->document->editAddNewTempFile();
            echo json_encode($data);
        }

        public function temp_file_delete(){
            $data = $this->document->tempFileDel();
            echo json_encode($data);
        }

        public function delAllTempFile(){
            $data = $this->document->del_all_temp();
            echo json_encode($data);
        }

        public function get_all_attach_file(){
            $data = $this->document->getAllAttachFile();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function delete_blacklisted(){
            $data = $this->document->deleteBlacklisted();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function preview_file(){
            $data = $this->document->previewFile();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function blacklist_preview_file(){
            $data = $this->document->blacklistedPreviewFile();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function file_delete(){
            $data = $this->document->fileDelete();
            echo $data;
        }

        public function add_new_file(){
            $data = $this->document->addNewFile();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function add_new_file_attach(){
            $data = $this->document->addNewFileAttach();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_all_resume_data(){
            $data = $this->document->getAllResumeData();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function new_resume_page(){
            $this->core_layout->setPrivilegeName("crs_resume");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addJs("js/crs/new_resume.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/new_resume');
            $this->load->view('core/templates/footer');
        }

        public function edit_application_page(){
            $this->core_layout->setPrivilegeName("crs_resume");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addJs("js/crs/edit_resume.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/edit_resume');
            $this->load->view('core/templates/footer');
        }

        public function addFileEmpty(){
            $data = $this->document->addFileFromStorage();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function blacklisted(){
            $this->core_layout->setPrivilegeName("crs_resume");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            /*$this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-noscript.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui-noscript.css", true);*/
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");

            $this->core_layout->addCss("css/crs/index.css", true);
            $this->core_layout->addJs("js/buttons.html5.min.js", true);
            $this->core_layout->addJs("js/buttons.print.min.js", true);
            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

            $this->core_layout->addJs("js/crs/blacklisted.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/blacklisted');
            $this->load->view('core/templates/footer');
        }

        public function blacklisted_view(){
            $this->core_layout->setPrivilegeName("crs_resume");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            /*$this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-noscript.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui-noscript.css", true);*/
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addJs("js/crs/blacklisted_view.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/blacklisted_view');
            $this->load->view('core/templates/footer');
        }

        public function return_blacklist(){
            $data = $this->document->returnBlacklist();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function forinterview_view(){
            $this->core_layout->setPrivilegeName("crs_resume");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            /*$this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-noscript.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui-noscript.css", true);*/
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addJs("js/crs/blacklisted_view.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('crs/forinterview_view');
            $this->load->view('core/templates/footer');
        }

        public function search_confirm_val(){
            $data = $this->document->searchConfirmVal();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_field_templates()
        {
            $data = $this->document->getFieldTempates(); // get table field templates
            echo json_encode($data);
        }

        public function open_modal()
        {

            $data = array();
            $formData = $this->input->post('formData');
            
            $init_modal_data_function = $this->input->post('init_modal_data_function');
            $data['html'] = $this->utilities->openModal();

            if ($init_modal_data_function) {
                $data['info'] = $this->document->$init_modal_data_function($formData);
            }
            echo json_encode($data);
        }

        public function get_template_body($template_id)
        {
            $data = $this->document->getTemplateBody($template_id);
            echo json_encode($data);
        }

        public function save_field_template()
        {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            unset($post->field);

            $post->items = json_decode($post->templateItems, true);

            $data = $this->document->saveFieldTemplate($post);
            echo json_encode($data);
        }

        public function generate_employee_report($export=0)
        {
            $data = $this->document->generateEmployeeReport($export);
            echo json_encode($data);
        }

        public function generate_employee_report_forinterview($export=0)
        {
            $data = $this->document->generateEmployeeReportForinterview($export);
            echo json_encode($data);
        }

        public function generate_employee_report_blacklisted($export=0)
        {
            $data = $this->document->generateEmployeeReportBlacklisted($export);
            echo json_encode($data);
        }

        public function update_field_template()
        {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            unset($post->field);

            $post->items = json_decode($post->templateItems, true);

            $data = $this->document->updateFieldTemplate($post);
            echo json_encode($data);
        }

        public function log_export(){
            $data = $this->document->logExport();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_candidates(){
            $data = $this->document->getCandidates();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

    }