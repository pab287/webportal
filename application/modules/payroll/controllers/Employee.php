<?php defined('BASEPATH') or exit('No direct script access allowed');

    class Employee extends MY_Controller {
        function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("payroll");
            $this->authenticate->doRedirect();

            $this->load->model("Employee_m", "employee");
            $this->load->model("hris/employee_model");
            $this->load->model("hris/position_model");
            $this->load->model("ams/Utilities_model", "utilities");
            date_default_timezone_set('Asia/Manila');
        }

        public function employee_masterlist() {
            $this->core_layout->setPageTitle("Payroll - Employee List");
            $this->core_layout->setPrivilegeName("payroll_employee");
            $this->core_layout->addJs("js/payroll/employee/masterfile.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('employee_profile/masterfile');
            $this->load->view('core/templates/footer');
        }

        function edit_employee_masterfile($id = null) {
            $this->core_layout->setBodyClass("hris masterfile edit_employee");
            $this->core_layout->setPageTitle("Payroll - Edit Employee");
            if ($id) {
                $this->core_layout->setPrivilegeName("payroll_employee");
                $currentActions = $this->core_layout->getCurrentActions();
                $hasApprovingAuthority = is_array($currentActions) && count($currentActions) > 0 && in_array("approving_authority", $currentActions);
                $arrData = array();
                $arrData["data"] = $this->employee->getEmployeeData($id);
                $arrData["approving_authority"] = $hasApprovingAuthority;
                $arrData["dropdown_data"] = $this->employee->getDropdownSelectData();
                $arrData["payroll_types"] = $this->db->get("payroll.payroll_type")->result();
                $arrData["payout_scheds"] = $this->db->get("payroll.payout_schedule")->result();
                $arrData["for_approval_history"] = $this->employee->fieldValueApprovals("hris", $id);
                $arrData["loans_dropdown"] = $this->employee_model->getLoanCollection($id, 0);
                $arrData["loans_ca_reference"] = $this->employee->getCaRef($id);

                $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
                $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
                $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
                $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
                $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
                $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");

                $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);

                $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
                $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);

                $this->core_layout->addJs("js/payroll/employee/masterfile.js", true, $arrData);
                $this->core_layout->addJs("js/payroll/employee/employee_edit_and_archive_script.js", true);

                $this->load->view("core/templates/header");
                $this->load->view("payroll/employee_profile/edit_masterfile", $arrData);
                $this->load->view("core/templates/footer");
            } else {
                redirect(base_url("payroll/employee/employee_masterfile"), "refresh");
            }
        }

        public function fixed_taxable_deduction(){
            $this->core_layout->setPageTitle("Payroll - Fixed Taxable Deduction");
            $this->core_layout->setPrivilegeName("payroll_fixed_taxable_deduction");

            $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
            $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
            $this->core_layout->addJs("js/payroll/employee/fixed_taxable_deduction.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('payroll/payroll/fixed_taxable_deduction');
            $this->load->view('core/templates/footer');
        }

        function employee_masterfile($employee_status = "Active") {
            $employee_status = str_replace("%20", " ", $employee_status);
            $data = $this->employee->employeeMasterfile($employee_status);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        //hris model
        function get_position_select2_data() {
            $data = $this->position_model->getPositionSelect2Data();
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

        function update_cash_advance() {
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data = $this->employee_model->updateCashAdvance($post);
            echo json_encode($data);
        }

        function change_employee_company() {
            $data = $this->employee_model->changeEmployeeCompany();
            echo json_encode($data);
        }

        function get_employee_cash_advance() {
            $data = $this->employee_model->getEmployeeCashAdvance();
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

        function get_employee_allowance() {
            $data = $this->employee_model->getEmployeeAllowance();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_benefits() {
            $data = $this->employee_model->getEmployeeBenefits();
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

        function get_employee_to_merge_loan($id) {
            $data = $this->employee_model->getEmployeeToMergeLoan($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_loans(){
            $data = $this->employee_model->getEmpLoans();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_employee_loans_history(){
            $data = $this->employee_model->getEmpLoansHistory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_allowance_collection($id) {
            $data = $this->employee_model->getAllowanceCollection($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_benefit_collection($id) {
            $data = $this->employee_model->getBenefitCollection($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_loan_collection($id, $show_all = 0) {
            $data = $this->employee_model->getLoanCollection($id, $show_all);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function save_employee_allowance() {
            $data = $this->employee->saveEmployeeAllowance();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function save_employee_benefit() {
            $data = $this->employee_model->saveEmployeeBenefit();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function save_employee_loan() {
            $data = $this->employee_model->saveEmployeeLoan();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_employee_allowance($id) {
            $data = $this->employee_model->removeEmployeeAllowance($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_employee_benefit($id) {
            $data = $this->employee_model->removeEmployeeBenefit($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function remove_employee_loan($id) {
            $data = $this->employee_model->removeEmployeeLoan($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function open_edit_modal() {
            $data = array();
            $formData = $this->input->post('formData');
            $init_modal_data_function = $this->input->post('init_modal_data_function');

            $data['html'] = $this->utilities->openModal();

            if ($init_modal_data_function) {
                $data['info'] = $this->employee_model->$init_modal_data_function($formData);
            }
            echo json_encode($data);
        }

        function open_confirm_modal() {
            $data = $this->utilities->openModal();
            echo $data;
        }

        function add_employee() {
            $this->core_layout->setPageTitle("Payroll - New Employee");
            $this->core_layout->setPrivilegeName("payroll_employee");
            $this->core_layout->addJs("js/payroll/employee/add_employee.script.js", true);

            $this->load->view('core/templates/header');
            $this->load->view("employee_profile/add_employee", $this->employee->getDropdownSelectData());
            $this->load->view('core/templates/footer');
        }

        function save_employee() {
            echo json_encode($this->employee->saveEmployee());
        }

        function check_biometric_no($biometricno = null) {
            echo json_encode($this->employee->checkBiometricNo($biometricno));
        }

        function update_employee_allowance() {
            echo json_encode($this->employee->updateEmployeeAllowance());
        }

        function update_employee_loan($id) {
            $data = $this->employee->updateEmployeeLoan($id);
            $this->output->set_content_type('json')->set_output(json_encode($data));
        }

        function get_employee_loan_payment_history($id) {
            echo json_encode($this->employee->getEmployeeLoanPaymentHistory($id));
        }

        function get_employee_loan_iterest_charge_history($id) {
            echo json_encode($this->employee->getEmployeeLoanInterestChargeHistory($id));
        }

        function get_employee_attachments($id){
            $data = $this->employee->getEmpAttachment($id);
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

        function payroll_group(){
            $this->load->model("payroll/payroll_m", "payroll");
            $tempData = array(); 
            $tempData["company"] = $this->payroll->select2CompanyData();
            
            $this->core_layout->setPageTitle("Payroll - Employee Group");
            $this->core_layout->setPrivilegeName("payroll_employee_group");
            $this->core_layout->addJs("js/payroll/employee/payroll.group.js", true, $tempData);

            $this->load->view('core/templates/header');
            $this->load->view('payroll/payroll/employee_group');
            $this->load->view('core/templates/footer');
        }

        function get_employee_group() {
            $data = $this->employee->getEmployeeGroup();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_employee_group_modal($type="new") {
            $data = $this->employee->getEmployeeGroupModal($type);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_employee_group_for_filter($id=null) {
            $data = $this->employee->getEmployeeGroupForFilter($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function set_new_payroll_employee_group() {
            $data = $this->employee->setNewPayrollEmployeeGroup();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function update_payroll_employee_group() {
            $data = $this->employee->updatePayrollEmployeeGroup();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function archive_payroll_employee_group() {
            $data = $this->employee->archivePayrollEmployeeGroup();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_employee_group_data($type="edit", $id=null) {
            $data = $this->employee->getEmployeeGroupData($type, $id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        function get_duplicate_payroll_group() {
            $data = $this->employee->getDuplicatePayrollGroup();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function loan_has_ref($id){
            $data = $this->employee_model->loanHasRef($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        function get_employee_loan_remarks($id){
            $data = $this->employee->getLoanRemark($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_ca_reference($id){
            $data = $this->employee->getCaRef($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        public function merge_employee_loan(){
            $data = $this->employee_model->mergeEmployeeLoan();
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

        public function field_value_approvals($module=null, $id=null){
            $data = $this->employee_model->fieldValueApprovals($module, $id);
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

        function update_employee_bank_information(){
            $data = $this->employee_model->update_bank_info();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        
        function get_employee_list(){
            $data = $this->employee->getEmployeeList();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_employee_select2_data(){
            $data = $this->employee->getSelect2Employee();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function add_taxable_deduction(){
            $data = $this->employee->addTaxableDeduction();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function update_taxable_deduction(){
            $data = $this->employee->updateTaxableDeduction();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_fixed_taxable_deduction(){
            $data = $this->employee->getFixedTaxableDeduction();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function update_status_taxable_deduction(){
            $data = $this->employee->updateStatusTaxableDeduction();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
    }