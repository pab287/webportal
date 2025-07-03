<?php defined('BASEPATH') || exit('No direct script access allowed');

    class Masterfile extends MY_Controller {
        function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("hris");
            $this->authenticate->doRedirect();

            $this->core_layout->setBodyClass("hris");
            $this->core_layout->setPrivilegeName("hris");

            $this->load->model("hris/employee_model");
            $this->load->model("hris/company_model");
            $this->load->model("hris/department_model");
            $this->load->model("hris/position_model");
            $this->load->model("hris/salary_model");
            $this->load->model("hris/contractor_model");
            $this->load->model("hris/personnel_model");
            $this->load->model("ams/Utilities_model", "utilities");
            $this->load->model("gcctime/Timesheet_model", "ts_model");
            $this->load->model("hris/license_model");
            date_default_timezone_set('Asia/Manila');

            $this->core_layout->addExternalJs("https://cdn.ckeditor.com/ckeditor5/12.3.1/classic/ckeditor.js", true);
        }

        function employee() {
            $this->core_layout->addJs("js/hris/employee_masterfile_script.js", true);
            $this->core_layout->addJs("plugins/star-rating/js/jquery.star-rating-svg.min.js", TRUE);
            $this->core_layout->addCss("plugins/star-rating/css/star-rating-svg.css");

            $this->core_layout->setPageTitle("HRIS - Employee Masterfile");
            $this->core_layout->setPrivilegeName("hris_employee_masterfile");

            $data["employee_status"] = $this->employee_model->getEmployeeStatusesFromMasterfile();
            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/employee/index", $data);
            $this->load->view("core/templates/footer");
        }

        function import_employee() {
            $this->core_layout->setPageTitle("HRIS - Import Employee");
            $this->core_layout->setPrivilegeName("hris_import_employee");
            $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addJs("js/hris/import_employee_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/import/index");
            $this->load->view("core/templates/footer");
        }

        function company() {
            $this->core_layout->setPageTitle("HRIS - Company Masterfile");
            $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");

            $this->core_layout->setPrivilegeName("hris_company");
            $this->core_layout->addJs("js/hris/company_masterfile_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/company/index");
            $this->load->view("core/templates/footer");
        }

        function department() {
            $this->core_layout->setPageTitle("HRIS - Department Masterfile");
            $this->core_layout->addJs("js/hris/department_masterfile_script.js", true);
            $this->core_layout->setPrivilegeName("hris_department");

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/department/index");
            $this->load->view("core/templates/footer");
        }

        function position() {
            $this->core_layout->setPageTitle("HRIS - Position Masterfile");
            $this->core_layout->setPrivilegeName("hris_position");
            $this->core_layout->addJs("js/hris/position_masterfile_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/position/index");
            $this->load->view("core/templates/footer");
        }

        function salary() {
            $this->core_layout->setPageTitle("HRIS - Salary Masterfile");
            $this->core_layout->setPrivilegeName("hris_salary_masterfile");
            $this->core_layout->addJs("js/hris/salary_masterfile_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/salary/index");
            $this->load->view("core/templates/footer");
        }

        function contractor() {
            $this->core_layout->setPageTitle("HRIS - Contractor Masterfile");
            $this->core_layout->addJs("js/hris/contractor_masterfile_script.js", true);
            $this->core_layout->setPrivilegeName("hris_contractor_masterfile");

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/contractor/index");
            $this->load->view("core/templates/footer");
        }

        function personnel_request() {
            $this->core_layout->setPageTitle("HRIS - Personnel Request Masterfile");
            $this->core_layout->setPrivilegeName("hris_personnel_request");
            $this->core_layout->addJs("js/hris/personnel_request_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/personnel_request/index");
            $this->load->view("core/templates/footer");
        }

        function questions(){
            $this->core_layout->setPageTitle("HRIS - Questions Masterfile");
            $this->core_layout->setPrivilegeName("hris_questions");
            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
            $this->core_layout->addJs("js/hris/questions_masterfile_script.js", TRUE);

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/questions/index");
            $this->load->view("core/templates/footer");
        }

        function edit_personnel_request($id = null) {
            if ($id) {
                $this->core_layout->setPageTitle("HRIS - Edit Personnel Request");
                $this->core_layout->setBodyClass("hris edit-personnel_request");
                $this->core_layout->setPrivilegeName("hris_personnel_request");

                $arrData = $this->personnel_model->getCurrentPersonnelRequest($id);
                $this->core_layout->addJs("js/hris/personnel_request_script.js", true, $arrData);

                $this->load->view("core/templates/header");
                $this->load->view("hris/masterfile/personnel_request/edit_personnel_request");
                $this->load->view("core/templates/footer");
            } else {
                redirect(base_url("hris/masterfile/personnel_request"), "refresh");
            }
        }

        function approval_personnel_request($id = null) {
            if ($id) {
                $arrData = $this->personnel_model->getCurrentPersonnelRequest($id);
                if (isset($arrData["right_pane"]["status"]) && $arrData["right_pane"]["status"] == "For Approval") {
                    $this->core_layout->setPageTitle("HRIS - Edit Personnel Request");
                    $this->core_layout->setBodyClass("hris approval-personnel_request");
                    $this->core_layout->setPrivilegeName("hris_personnel_request");
                    $this->core_layout->addJs("js/hris/personnel_request_script.js", true, $arrData);

                    $this->load->view("core/templates/header");
                    $this->load->view("hris/masterfile/personnel_request/approval_personnel_request");
                    $this->load->view("core/templates/footer");
                } else {
                    redirect(base_url("hris/masterfile/personnel_request"), "refresh");
                }
            } else {
                redirect(base_url("hris/masterfile/personnel_request"), "refresh");
            }
        }

        function view_employee_masterfile($employee_id = null,$tab = null) {
            // redirect to listing if id is empty
            if (empty($employee_id)) {
                redirect(base_url("hris/masterfile/employee"), "refresh");
                die();
            }
            $data = $this->utilities->parseFormDataToObject(array("data" => $this->employee_model->getEmployeeDataDetails($employee_id)));
            $data->questions_list = $this->employee_model->getQuestionsList();
            $data->tab=$tab;
            $this->core_layout->setPageTitle("HRIS - View Employee Masterfile");
            $this->core_layout->setBodyClass("hris view-employee_masterfile");
            $this->core_layout->setPrivilegeName("hris_employee_masterfile");

            $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");

            $this->core_layout->addJs("plugins/star-rating/js/jquery.star-rating-svg.min.js", TRUE);
            $this->core_layout->addCss("plugins/star-rating/css/star-rating-svg.css", TRUE);

            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
            
            $this->core_layout->addJs("js/hris/employee_view_script.js",true,$data);
            $this->core_layout->addCss("css/hris/view_employee_masterfile.css", true);

            $this->core_layout->addJs('js/hris/search_employee_script.js', TRUE);
            $this->core_layout->addCss('css/hris/index.css', TRUE);


            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/employee/view_employee_masterfile", $data, FALSE);
            $this->load->view("core/templates/footer");
        }

        public function get_print_data($employee_id){
            $data = $this->utilities->parseFormDataToObject(array("data" => $this->employee_model->getEmployeeDataSheetDetails($employee_id)));
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        function add_employee_masterfile() {
            $this->core_layout->setBodyClass("hris masterfile add_employee");
            $this->core_layout->setPageTitle("HRIS - Add Employee");
            $this->core_layout->setPrivilegeName("hris_employee_masterfile");

            $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
            $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
            $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");

            $this->core_layout->addJs("js/hris/employee_new_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/employee/add_employee_masterfile");
            $this->load->view("core/templates/footer");
        }

        function edit_employee_masterfile($id = null) {
            $this->load->model("payroll/employee_m", "employee");
            
            $this->core_layout->setBodyClass("hris masterfile edit_employee");
            $this->core_layout->setPageTitle("HRIS - Edit Employee");
            if ($id) {
                $this->core_layout->setPrivilegeName("hris_employee_masterfile");
                $currentActions = $this->core_layout->getCurrentActions();
                $hasApprovingAuthority = is_array($currentActions) && count($currentActions) > 0 && in_array("approving_authority", $currentActions);

                $arrData = array();
                $arrData["data"] = $this->employee_model->getEmployeeData($id);
                $arrData["approving_authority"] = $hasApprovingAuthority;
                $arrData["dropdown_data"] = $this->employee_model->getDropdownSelectData();
                $arrData["rating_scale"] = $this->employee_model->getPerformanceRatingScale();
                $arrData["payroll_types"] = $this->db->get("payroll.payroll_type")->result();
                $arrData["payout_scheds"] = $this->db->get("payroll.payout_schedule")->result();
                $arrData["for_approval_history"] = $this->employee->fieldValueApprovals("hris", $id);
                $arrData["loans_dropdown"] = $this->employee_model->getLoanCollection($id, 0);
                $arrData["loans_ca_reference"] = $this->employee->getCaRef($id);
                $arrData["questions_list"] = $this->employee_model->getQuestionsList();
                
                $this->core_layout->addJs("plugins/star-rating/js/jquery.star-rating-svg.min.js", TRUE);
                $this->core_layout->addCss("plugins/star-rating/css/star-rating-svg.css");
                $this->core_layout->addJs("plugins/pdf/pdf.min.js", true);
                $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
                $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
                $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
                $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
                $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
                $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
                $this->core_layout->addJs("js/buttons.print.min.js", true);

                $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
                $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
                $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
                $this->core_layout->addJs("js/hris/employee_masterfile_script.js", true, $arrData);
                $this->core_layout->addJs("js/hris/employee_documents_script.js", true);
                $this->core_layout->addJs("js/hris/employee_edit_and_archive_script.js", true);

                $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
                $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
                
                $this->load->view("core/templates/header");
                $this->load->view("hris/masterfile/employee/edit_masterfile", $arrData);
                $this->load->view("core/templates/footer");
            } else {
                redirect(base_url("hris/masterfile/employee"), "refresh");
            }
        }

        function get_employee_datatable_request($employee_status = "Active") {
            $data = $this->employee_model->getEmployeeDatatableRequest($this->input->post('emp_status'));
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_company_datatable_request() {
            $data = $this->company_model->getCompanyDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_department_datatable_request() {
            $data = $this->department_model->getDepartmentDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_position_datatable_request() {
            $data = $this->position_model->getPositionDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_salary_datatable_request() {
            $data = $this->salary_model->getSalaryDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_contractor_datatable_request() {
            $data = $this->contractor_model->getContractorDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_personnel_request_datatable_request($key = null) {
            $data = $this->personnel_model->getPersonnelRequestDatatableRequest($key);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_personnel_request_datatable_request_completed($key = null) {
            $data = $this->personnel_model->getPersonnelRequestDatatableRequestCompleted($key);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_company_modal_content($content = "add") {
            $data = $this->company_model->getCompanyModalContent($content);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_department_modal_content($content = "add") {
            $data = $this->department_model->getDepartmentModalContent($content);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_position_modal_content($content = "add") {
            $data = $this->position_model->getPositionModalContent($content);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_salary_modal_content($content = "add") {
            $data = $this->salary_model->getSalaryModalContent($content);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_contractor_modal_content($content = "add") {
            $data = $this->contractor_model->getContractorModalContent($content);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_personnel_request_modal_content() {
            $data = $this->personnel_model->getPersonnelRequestModalContent();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function upload_employee_avatar() {
            $data = $this->employee_model->uploadEmployeeAvatar();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function temp_upload_employee_avatar() {
            $data = $this->employee_model->tempUploadEmployeeAvatar();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function upload_employee_training() {
            $data = $this->employee_model->uploadEmployeeTraining();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function upload_employee_medical_history() {
            $data = $this->employee_model->uploadEmployeeMedicalHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function upload_employee_offenses() {
            $data = $this->employee_model->uploadEmployeeOffenses();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function upload_employee_documents() {
            $data = $this->employee_model->uploadEmployeeDocuments();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function upload_employee_background_check() {
            $data = $this->employee_model->uploadEmployeeDocuments();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function upload_employee_performance() {
            $data = $this->employee_model->uploadEmployeePerformance();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function temp_upload_company_file($thumbnails = false) {
            $data = $this->company_model->tempUploadCompanyFile($thumbnails);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function add_employee_personal_information() {
            $data = $this->employee_model->addEmployeePersonalInformation();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_employee_personal_info() {
            $data = $this->employee_model->updateEmployeePersonalInfo();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_employee_employment_data() {
            $data = $this->employee_model->updateEmployeeEmploymentData();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_salary_rating() {
            $data = $this->employee_model->updateEmployeeSalaryHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_employee_additional_info() {
            $data = $this->employee_model->updateEmployeeAdditionalInfo();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_personnel_request() {
            $data = $this->personnel_model->updatePersonnelRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_modal_company() {
            $data = $this->company_model->updateModalCompany();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_modal_department() {
            $data = $this->department_model->updateModalDepartment();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_modal_position() {
            $data = $this->position_model->updateModalPosition();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_modal_salary() {
            $data = $this->salary_model->updateModalSalary();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_modal_contractor() {
            $data = $this->contractor_model->updateModalContractor();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_dependents() {
            $data = $this->employee_model->getDependentsList();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_educational_bg() {
            $data = $this->employee_model->getEducationalBackgroundList();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_licensure() {
            $data = $this->employee_model->getEmployeeLicensure();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_driverlicense() {
            $data = $this->employee_model->getEmployeeDriverLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_work_experience() {
            $data = $this->employee_model->getEmployeeWorkExperience();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_awards() {
            $data = $this->employee_model->getEmployeeAwards();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_organization() {
            $data = $this->employee_model->getEmployeeOrganization();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_training() {
            $data = $this->employee_model->getEmployeeTraining();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_personal_reference() {
            $data = $this->employee_model->getEmployeePersonalReference();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_medical_history() {
            $data = $this->employee_model->getEmployeeMedicalHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_skills() {
            $data = $this->employee_model->getEmployeeSkills();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_salary_history() {
            $data = $this->employee_model->getEmployeeSalaryHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_accountability() {
            $data = $this->employee_model->getEmployeeAccountability();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        /** employment data tab section **/
        function get_employee_legal_history() {
            $data = $this->employee_model->getEmployeeLegalHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_offenses($type = null) {
            $data = $this->employee_model->getEmployeeOffenses($type);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_cash_advance() {
            $data = $this->employee_model->getEmployeeCashAdvance();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_documents() {
            $data = $this->employee_model->getEmployeeDocuments();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_background_check() {
            $data = $this->employee_model->getEmployeeBackgroundCheck();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_current_job_description($id = null) {
            $data = $this->employee_model->getCurrentJobDescription($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_position_jdq($id = null) {
            $data = $this->position_model->getPositionJobDescriptionQ($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_performance() {
            $data = $this->employee_model->getEmployeePerformance();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_return_to_work() {
            $data = $this->employee_model->getEmployeeReturnToWork();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_allowance(){
            $data = $this->employee_model->getEmployeeAllowance();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_benefits(){
            $data = $this->employee_model->getEmployeeBenefits();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_loans(){
            $data = $this->employee_model->getEmployeeLoans();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_loan($id) {
            $data = $this->employee_model->getEmployeeLoan($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_allowance_collection($id){
            $data = $this->employee_model->getAllowanceCollection($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_benefit_collection($id){
            $data = $this->employee_model->getBenefitCollection($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_loan_collection($id){
            $data = $this->employee_model->getLoanCollection($id,'1');
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function save_employee_allowance(){
            $data = $this->employee_model->saveEmployeeAllowance();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function save_employee_benefit(){
            $data = $this->employee_model->saveEmployeeBenefit();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function save_employee_loan(){
            $data = $this->employee_model->saveEmployeeLoan();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_employee_allowance($id){
            $data = $this->employee_model->removeEmployeeAllowance($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_employee_benefit($id){
            $data = $this->employee_model->removeEmployeeBenefit($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        
        function remove_employee_loan($id){
            $data = $this->employee_model->removeEmployeeLoan($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        /** employment data tab section **/

        /**** Modal Section****/
        function get_modal_dependents($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "dependents");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_education($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "education");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_licensure($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "licensure");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_driverlicense($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "driverlicense");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_work_experience($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "work_experience");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_awards($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "awards");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_organization($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "organization");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_training($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "training");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_references($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "personal_references");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_medical_history($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "medical_history");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_legal_history($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "legal_history");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_offenses($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "offenses");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_documents($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "documents");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_background_check($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "background_check");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_job_description($id = null) {
            $data = $this->employee_model->getModalJobDescription($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_question_description($id = null) {
            $data = $this->employee_model->getModalQuestionsDescription($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_performance($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "performance", $this->employee_model->setPerformanceData($id));
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_approval_action($status = null, $id = null) {
            $data = $this->personnel_model->getModalApprovalAction($status, $id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_cancel_action($status = null, $id = null) {
            $data = $this->personnel_model->getModalCancelAction($status, $id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_modal_completed_remarks($id = null) {
            $data = $this->personnel_model->getModalCompletedRemarks($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        /**** Modal Section****/

        /**** Insert Function Section****/
        function set_modal_dependents() {
            $data = $this->employee_model->setModalDependents();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_education() {
            $data = $this->employee_model->setModalEducation();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_licensure() {
            $data = $this->employee_model->setModalLicensure();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_driverlicense() {
            $data = $this->employee_model->setModalDriverLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_work_experience() {
            $data = $this->employee_model->setModalWorkExperience();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_awards() {
            $data = $this->employee_model->setModalAwards();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_organization() {
            $data = $this->employee_model->setModalOrganization();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_trainings() {
            $data = $this->employee_model->setModalTrainings();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_references() {
            $data = $this->employee_model->setModalReferences();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_medical_history() {
            $data = $this->employee_model->setModalMedicalHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_legal_history() {
            $data = $this->employee_model->setModalLegalHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_offenses() {
            $data = $this->employee_model->setModalOffenses();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_documents() {
            $data = $this->employee_model->setModalDocuments();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_background_check() {
            $data = $this->employee_model->setModalBackgroundCheck();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_job_description() {
            $data = $this->employee_model->setModalJobDescription();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_questions_description() {
            $data = $this->employee_model->setModalQuestionsDescription();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_performance() {
            $data = $this->employee_model->setModalPerformance();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_company() {
            $data = $this->company_model->setModalCompany();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_department() {
            $data = $this->department_model->setModalDepartment();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_position() {
            $data = $this->position_model->setModalPosition();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_salary() {
            $data = $this->salary_model->setModalSalary();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_contractor() {
            $data = $this->contractor_model->setModalContractor();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_personnel_request() {
            $data = $this->personnel_model->setModalPersonnelRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_approval_personnel_request() {
            $data = $this->personnel_model->setModalApprovalPersonnelRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_status_personnel_request() {
            $data = $this->personnel_model->setModalStatusPersonnelRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_current_company() {
            $data = $this->company_model->removeCurrentCompany();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_current_department() {
            $data = $this->department_model->removeCurrentDepartment();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_current_position() {
            $data = $this->position_model->removeCurrentPosition();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_current_salary() {
            $data = $this->salary_model->removeCurrentSalary();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_current_contractor() {
            $data = $this->contractor_model->removeCurrentContractor();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_company_select2_data() {
            $data = $this->company_model->getCompanySelect2Data();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_department_select2_data() {
            $data = $this->department_model->getDepartmentSelect2Data();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_position_select2_data() {
            $data = $this->position_model->getPositionSelect2Data();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_salary_select2_data() {
            $data = $this->salary_model->getSalarySelect2Data();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_personnel_select2_data() {
            $data = $this->personnel_model->getPersonnelSelect2Data();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function import_csv() {
            $data = $this->employee_model->importCsv();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        /**** Insert Function Section****/

        /*** function get_employee_position($offset=0, $limit=100){
         * $this->db->select("a.id, a.lastname, a.position, b.id as temp_id");
         * $this->db->from("tblemployees a");
         * $this->db->join("gcchris.tblposition b", "b.name = a.position", "left");
         * $this->db->limit($limit, $offset);
         * $query = $this->db->get();
         *
         * echo "<pre>";
         * if($query->num_rows() > 0){
         * $counter = 0;
         * foreach($query->result() as $rs){
         * if(!is_numeric($rs->position) && $rs->temp_id){
         * $tempId = intval($rs->temp_id);
         * $data = array("position"=>$tempId);
         * $where = array("id"=>$rs->id);
         * $updated = $this->db->update("tblemployees", $data, $where);
         * if($updated){
         * var_dump($rs);
         * }
         * $counter++;
         * }
         * }
         * echo "<h1>";
         * var_dump($counter);
         * echo "</h1>";
         * }
         * } ***/

        function get_partner($limit = 100, $offset = 0, $isPartner = false) {
            echo "<pre>";
            $this->db->limit($limit, $offset);
            $query = $this->db->get_where("tblemployees", array("partner_type" => 0));
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $key => $value) {
                    $spo = ($isPartner) ? $value->partners_name : $value->spo_name;
                    if ($spo && strtoupper($spo) !== "NONE" && strtoupper(trim($spo)) !== "N/A" && strtoupper(trim($spo)) !== "X" && strtoupper(trim($spo)) !== "NO") {
                        $updated = $this->db->update("tblemployees", array("partner_type" => 1), array("id" => $value->id));
                        if ($updated) {
                            var_dump($spo);
                        }
                    }
                }
            }
        }

        function move_uploaded() {
            echo "<pre>";
            $session = $this->core_layout->getCurrentSession();
            $imagesPath = "./uploads/files/images/employee_files/temp_{$session["emp_id"]}/Profile_-_Maui5.jpg";
            $destinationPath = "./uploads/files/images/employee_files/empcode_946/Profile_-_Maui5.jpg";
            var_dump($imagesPath);
            var_dump($destinationPath);
            if (rename($imagesPath, $destinationPath)) {
                echo "moved";
            } else {
                echo "failed";
            }
        }

        function get_employee_documents_for_tab($id = null) {
            $data = $this->employee_model->getEmployeeDocumentsForTab($id);
            echo json_encode($data);
        }

        function open_edit_modal() {
            $data = array();
            $formData = $this->input->post('formData');
            $init_modal_data_function = $this->input->post('init_modal_data_function');

            $data['html'] = $this->utilities->openModal();

            if ($init_modal_data_function) {
                $data['info'] = $this->employee_model->$init_modal_data_function($formData);
            }
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        function open_confirm_modal() {
            $data = $this->utilities->openModal();
            echo $data;
        }

        /* UPDATE FUNCTIONS */
        function update_legal_records() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateLegalRecords($post);
            echo json_encode($data);
        }

        function update_document() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateDocument($post);
            echo json_encode($data);
        }

        function update_offenses_and_commendations() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateOffensesAndCommendations($post);
            echo json_encode($data);
        }

        function update_bg_check() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateBgCheck($post);
            echo json_encode($data);
        }

        function update_employee_question_answers() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateEmployeeQuestionAnswers($post);
            echo json_encode($data);
        }

        function update_employee_job_description() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateEmployeeJobDescription($post);
            echo json_encode($data);
        }

        function update_performance_evaluation() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updatePerformanceEvaluation($post);
            echo json_encode($data);
        }

        function update_cash_advance() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateCashAdvance($post);
            echo json_encode($data);
        }

        function update_dependent() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateDependent($post);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        function update_educational_background() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateEducationalBackground($post);
            echo json_encode($data);
        }

        function update_licensure() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateLicensure($post);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        function update_driverlicense() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateDriverLicense($post);
            echo json_encode($data);
        }

        function update_work_experience() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateWorkExperience($post);
            echo json_encode($data);
        }

        function update_award() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateAward($post);
            echo json_encode($data);
        }

        function update_organization() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateOrganization($post);
            echo json_encode($data);
        }

        function update_training() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateTraining($post);
            echo json_encode($data);
        }

        function update_personal_references() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updatePersonalReferences($post);
            echo json_encode($data);
        }

        function update_medical_record() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateMedicalRecord($post);
            echo json_encode($data);
        }
        /* END UPDATE FUNCTIONS */

        /* ARCHIVE FUNCTIONS */
        function archive_document($document_id = null) {
            $data = $this->employee_model->archiveDocument($document_id);
            echo json_encode($data);
        }

        function archive_legal_record($legal_id = null) {
            $data = $this->employee_model->archiveLegalRecord($legal_id);
            echo json_encode($data);
        }

        function archive_offenses_and_commendations($offsense_id) {
            $data = $this->employee_model->archiveOffensesAndCommendations($offsense_id);
            echo json_encode($data);
        }

        function archive_bg_check($bg_id) {
            $data = $this->employee_model->archiveBgCheck($bg_id);
            echo json_encode($data);
        }

        function archive_performance_evaluation($eval_id) {
            $data = $this->employee_model->archivePerformanceEvaluation($eval_id);
            echo json_encode($data);
        }

        function archive_employee_cash_advance($cash_advance_id) {
            $data = $this->employee_model->archiveEmployeeCashAdvance($cash_advance_id);
            echo json_encode($data);
        }

        function archive_dependent($dependent_id) {
            $data = $this->employee_model->archiveDependent($dependent_id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        function archive_educational_background($educ_id) {
            $data = $this->employee_model->archiveEducationalBackground($educ_id);
            echo json_encode($data);
        }

        function archive_licensure($licensure_id) {
            $data = $this->employee_model->archiveLicensure($licensure_id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        function archive_driverlicense($driverlicense_id) {
            $data = $this->employee_model->archiveDriverLicense($driverlicense_id);
            echo json_encode($data);
        }

        function archive_worK_experience($work_exp_id) {
            $data = $this->employee_model->archiveWorkExperience($work_exp_id);
            echo json_encode($data);
        }

        function archive_award($award_id) {
            $data = $this->employee_model->archiveAward($award_id);
            echo json_encode($data);
        }

        function archive_organization($org_id) {
            $data = $this->employee_model->archiveOrganization($org_id);
            echo json_encode($data);
        }

        function archive_training($training_id) {
            $data = $this->employee_model->archiveTraining($training_id);
            echo json_encode($data);
        }

        function archive_personal_reference($references_id) {
            $data = $this->employee_model->archivePersonalReference($references_id);
            echo json_encode($data);
        }

        function archive_medical_record($med_id) {
            $data = $this->employee_model->archiveMedicalRecord($med_id);
            echo json_encode($data);
        }

        function archive_personnel_request() {
            $data = $this->personnel_model->archivePersonnelRequest();
            echo json_encode($data);
        }

        /* END ARCHIVE FUNCTIONS*/

        function delete_employee($emp_id) {
            $data = $this->employee_model->deleteEmployee($emp_id);
            echo json_encode($data);
        }

        function change_employee_company() {
            $data = $this->employee_model->changeEmployeeCompany();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        function remove_profile_picture($employee_id) {
            $data = $this->employee_model->removeProfilePicture($employee_id);
            echo json_encode($data);
        }

        function relocate_profile_pictures($employee_status = null) {
            $this->employee_model->relocateProfilePictures($employee_status);
        }

        function relocate_document_files() {
            $this->employee_model->relocateDocumentFiles();
        }

        function relocate_offenses_files($employee_id = null) {
            $this->employee_model->relocateOffensesFiles($employee_id);
        }

        function relocate_performance_eval_files() {
           $this->employee_model->relocatePerformanceEvalFiles();
        }

        function relocate_training_files() {
           $this->employee_model->relocateTrainingFiles();
        }

        function relocate_medical_record_files() {
            $this->employee_model->relocateMedicalRecordFiles();
        }

        function checkIf201StatusIsComplete($employee_id) {
            echo json_encode($this->employee_model->checkIf201StatusIsComplete($employee_id));
        }

        function add_skill() {
            echo json_encode($this->employee_model->addEmployeeSkill());
        }

        function edit_skill() {
            echo json_encode($this->employee_model->edtiEmployeeSkill());
        }

        function archive_skill($skill_id = null) {
            $data = $this->employee_model->archiveSkill($skill_id);
            echo json_encode($data);
        }

        function add_salary_history() {
            $data = $this->employee_model->addSalaryHistory();
            echo json_encode($data);
        }

        function archive_salary_history($salary_id = null) {
            $data = $this->employee_model->archiveSalaryHistory($salary_id);
            echo json_encode($data);
        }

        function edit_salary_history() {
            $data = $this->employee_model->editSalaryHistory();
            echo json_encode($data);
        }

        function add_employee_performance_rating() {
            echo json_encode($this->employee_model->addEmployeePerformanceRating());
        }

        function update_employee_performance_rating() {
            echo json_encode($this->employee_model->updateEmployeePerformanceRating());
        }

        function get_employee_rating($emp_id) {
            echo json_encode($this->employee_model->getPerformanceRating($emp_id));
        }

        function get_employee_rating_for_update($id) {
            echo json_encode($this->employee_model->getPerformanceRatingForUpdate($id));
        }

        function get_employee_rating_remarks($id) {
            $data = $this->employee_model->getPerformanceRating($id);
            echo json_encode($data);
        }

        function get_employee_performance_rating() {
            echo json_encode($this->employee_model->getEmployeePerformanceRating());
        }

        function delete_performance_rating($id) {
            $data = $this->employee_model->deletePerformanceRating($id);
            echo json_encode($data);
        }

        function get_latestSalaryRate(){
            $data = $this->employee_model->get_latest_salary_rate();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));;
        }

        function email_lookup_company(){
            $data = $this->company_model->emailLookup_company();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function edit_details_company($id){
            $data = $this->company_model->editDetails_company($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_position_details(){
            $data = $this->employee_model->emp_position_details();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_position_details_pr(){
            $data = $this->personnel_model->pr_position_details();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function employee_document_upload(){
            $data =  $this->employee_model->employeeDocumentUpload();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function temp_employee_document_upload(){
            $data =  $this->employee_model->tempEmployeeDocumentUpload();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_employee_position($employee_id){
            $data = $this->employee_model->getEmployeePosition($employee_id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_all_site_points(){
            $data = $this->employee_model->getAllSitePoints();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function update_employee_payroll_data(){
            $data = $this->employee_model->updateEmployeePayrollData();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function update_employee_loan($id) {
            echo json_encode($this->employee_model->updateEmployeeLoan($id));
        }
        function update_employee_allowance() {
            $data = $this->employee_model->updateEmployeeAllowance();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function update_employee_benefits() {
            $data = $this->employee_model->updateEmployeeBenefits();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_employee_loan_payment_history($id) {
            echo json_encode($this->employee_model->getEmployeeLoanPaymentHistory($id));
        }

        function save_one_month_days_gap_setup(){
            $data = $this->employee_model->saveOneMonthDaysGapEmployeeSetup();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_nearing_one_month_employees(){
            $data = $this->employee_model->getNearingOneMonthEmployees();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        // function tempemployees() {
        //     $this->core_layout->addJs("js/hris/employee_masterfile_script_temp.js", true);
        //     $this->core_layout->addJs("plugins/star-rating/js/jquery.star-rating-svg.min.js", TRUE);
        //     $this->core_layout->addCss("plugins/star-rating/css/star-rating-svg.css");

        //     $this->core_layout->setPageTitle("HRIS - Employee Masterfile");
        //     $this->core_layout->setPrivilegeName("hris_employee_masterfile");

        //     $data["employee_status"] = $this->employee_model->tempgetEmployeeStatusesFromMasterfile();
        //     $this->load->view("core/templates/header");
        //     $this->load->view("hris/masterfile/employee/tempindex", $data);
        //     $this->load->view("core/templates/footer");
        // }

        // function tempget_employee_datatable_request($employee_status = "Active") {
        //     $data = $this->employee_model->tempgetEmployeeDatatableRequest($this->input->post('emp_status'));
        //     $this->output
        //         ->set_content_type('json')
        //         ->set_output(json_encode($data));
        // }

        function license(){
            $this->core_layout->setPageTitle("HRIS - License Masterfile");
            $this->core_layout->addJs("js/hris/license_masterfile_script.js", true);
            $this->core_layout->setPrivilegeName("hris_license");

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/license/index");
            $this->load->view("core/templates/footer");
        }

        function get_license_datatable_request() {
            $data = $this->license_model->getLicenseDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_license_modal_content($content = "add") {
            $data = $this->license_model->getLicenseModalContent($content);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_modal_license(){
            $data = $this->license_model->setModalLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_current_license() {
            $data = $this->license_model->removeCurrentLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function update_modal_license() {
            $data = $this->license_model->updateModalLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_license_type(){
            $data = $this->license_model->getLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        // archived masterfile
        function archived_companies(){
            $this->core_layout->addJs("js/hris/company_masterfile_script.js", true);

            $this->core_layout->setPageTitle("HRIS - Archived Companies");
            $this->core_layout->setPrivilegeName("hris_company");

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/company/archive");
            $this->load->view("core/templates/footer");
        }

        function archived_departments(){
            $this->core_layout->setPageTitle("HRIS - Department Masterfile");
            $this->core_layout->addJs("js/hris/department_masterfile_script.js", true);
            $this->core_layout->setPrivilegeName("hris_department");

            $this->core_layout->setPageTitle("HRIS - Archived Departments");

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/department/archive");
            $this->load->view("core/templates/footer");
        }

        function archived_positions(){
            $this->core_layout->setPageTitle("HRIS - Position Masterfile");
            $this->core_layout->setPrivilegeName("hris_position");
            $this->core_layout->addJs("js/hris/position_masterfile_script.js", true);

            $this->core_layout->setPageTitle("HRIS - Archived Departments");

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/position/archive");
            $this->load->view("core/templates/footer");
        }

        function archived_contractors() {
            $this->core_layout->setPageTitle("HRIS - Contractor Masterfile");
            $this->core_layout->addJs("js/hris/contractor_masterfile_script.js", true);
            $this->core_layout->setPrivilegeName("hris_contractor_masterfile");

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/contractor/archive");
            $this->load->view("core/templates/footer");
        }

        function archived_license(){
            $this->core_layout->setPageTitle("HRIS - License Masterfile");
            $this->core_layout->addJs("js/hris/license_masterfile_script.js", true);
            $this->core_layout->setPrivilegeName("hris_license");

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/license/archive");
            $this->load->view("core/templates/footer");
        }

        function archived_salaries() {
            $this->core_layout->setPageTitle("HRIS - Salary Masterfile");
            $this->core_layout->setPrivilegeName("hris_salary_masterfile");
            $this->core_layout->addJs("js/hris/salary_masterfile_script.js", true);

            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/salary/archive");
            $this->load->view("core/templates/footer");
        }
        // archived masterfile

        // restore
        function restore_current_company() {
            $data = $this->company_model->restoreCurrentCompany();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function restore_current_department() {
            $data = $this->department_model->restoreCurrentDepartment();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function restore_current_position() {
            $data = $this->position_model->restoreCurrentPosition();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function restore_current_contractor() {
            $data = $this->contractor_model->restoreCurrentContractor();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function restore_current_license() {
            $data = $this->license_model->restoreCurrentLicense();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function restore_current_salary() {
            $data = $this->salary_model->restoreCurrentSalary();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_history_payroll_information(){
            $data = $this->employee_model->getHistoryPayrollInformation();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        
        public function approval_updated_payroll_data($type=0){
            $data = $this->employee_model->approvalUpdatedPayrollData($type);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        // restore

        // function export_accountability($file, $status){
        //     $data = $this->salary_model->restoreCurrentSalary();
        //     $this->output
        //         ->set_content_type('json')
        //         ->set_output(json_encode($data));
        // }

        public function get_approval_email_template(){
            $this->load->view("hris/email_templates/email-for_approval");
        }

        public function scheduled_resigned_inactive(){
            $data = $this->employee_model->setScheduledEmployeeInactive();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_offenses_commendation_trail(){
            $data = $this->employee_model->getOffensesCommendationTrail();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function test(){
            $data = $this->employee_model->check_has_no_allowance(3257);
            var_dump($data);
        }

        function update_employee_bank_information(){
            $data = $this->employee_model->update_bank_info();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_ca_reference($id){
            $this->load->model("payroll/employee_m", "employee");
            
            $data = $this->employee->getCaRef($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function get_additional_info($id){
            $data = $this->employee_model->getAddtionalInfo($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_education_background($id){
            $data = $this->employee_model->getEducationBackground($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_license_and_cert($id){
            $data = $this->employee_model->getLicenseAndCerts($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_work_experience($id){
            $data = $this->employee_model->getEmpWorkExperience($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_awards_and_achievements($id){
            $data = $this->employee_model->getAwardsAndAchievements($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_emp_skills($id){
            $data = $this->employee_model->getEmpSkills($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_orgs($id){
            $data = $this->employee_model->getOrgs($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_trainings_and_seminars($id){
            $data = $this->employee_model->getTrainingsAndSeminars($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_personal_references($id){
            $data = $this->employee_model->getPersonalReferences($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_medical_history($id){
            $data = $this->employee_model->getEmpMedicalHistory($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_legal_history($id){
            $data = $this->employee_model->getLegalHistory($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_accountability($id){
            $data = $this->employee_model->getAccountability($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_employment_information($id){
            $data = $this->employee_model->getEmploymentInformation($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_job_description($id){
            $data = $this->employee_model->getEmpJobDescription($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        function get_modal_commendation($id = null) {
            $data = $this->employee_model->getModalContainerContent($id, "commendation");
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_employee_allowance_count($id=null){
            $data = $this->employee_model->getEmployeeAllowanceCount($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function get_employee_questions(){
            $data = $this->employee_model->getEmployeeQuestions();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function add_new_question(){
            $data = $this->employee_model->addNewQuestion();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function archive_question(){
            $data = $this->employee_model->archiveQuestion();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function restore_question(){
            $data = $this->employee_model->restoreQuestion();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function update_question(){
            $data = $this->employee_model->updateQuestion();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function update_statement(){
            $data = $this->employee_model->updateStatement();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        public function upload_employee_liscert(){
            $data = $this->employee_model->uploadEmployeeLicenseCert();
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

    }
