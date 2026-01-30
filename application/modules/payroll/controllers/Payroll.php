<?php defined('BASEPATH') || exit('No direct script access allowed');
class Payroll extends MY_Controller {

    protected $userData;

    public function __construct() {
        parent::__construct();
        $this->authenticate->setModuleAccess("payroll");
        $this->authenticate->doRedirect();

        $this->load->model("ams/Utilities_model", "utilities");
        $this->load->model("Payroll_m", "payroll");
        $this->load->model("Dashboard_m", "dashboard");
        $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
        $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
        date_default_timezone_set('Asia/Manila');

        $this->userData = $this->session->userdata("logged_in");
    }

    public function dashboard() {
        $this->core_layout->setPageTitle("Payroll - Payroll Dashboard");
        $this->core_layout->setPrivilegeName("payroll_dashboard");
        $this->core_layout->addJs("global/js/amcharts4/core.js", true);
        $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
        $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/material.js", true);
        $this->core_layout->addJs("js/payroll/payroll_dashboard.script.js", true);

        $data['sss'] = $this->dashboard->paidContributionSSS();
        $data['hdmf'] = $this->dashboard->paidContributionHDMF();
        $data['phil'] = $this->dashboard->paidContributionPHIL();
        $this->load->view("core/templates/header");
        $this->load->view("payroll/dashboard", $data);
        $this->load->view("core/templates/footer");
    }

    public function payroll_sheet() {
        $this->core_layout->setPageTitle("Payroll - Payroll Sheet");
        $this->core_layout->setPrivilegeName("payroll_sheet");

        $tempSession = (object) $this->core_layout->getCurrentSession();
        $tempData = array();
        $tempData["company"] = $this->payroll->select2CompanyData();
        $tempData["payout_schedule"] = $this->payroll->select2PayoutScheduleData();
        $tempData["filter_history"] = isset($tempSession->ps_history) ? $tempSession->ps_history: array();
        
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);

        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/jquery.autocomplete.min.js");
        $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
        $this->core_layout->addJs("js/payroll/payroll_sheet.js", true, $tempData);

        $this->load->view('core/templates/header');
        $this->load->view('payroll/payroll/payroll_sheet');
        $this->load->view('core/templates/footer');
    }

    public function payroll_incentive() {
        $this->core_layout->setPageTitle("Payroll - Payroll Incentive");
        $this->core_layout->setPrivilegeName("payroll_sheet_incentive");

        $tempData = array();
        $tempData["company"] = $this->payroll->select2CompanyData();
        $tempData["payout_schedule"] = $this->payroll->select2PayoutScheduleData();
        $tempData["incentive_type"] = $this->payroll->select2IncentiveTypeData();

        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/jquery.autocomplete.min.js");
        $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
        $this->core_layout->addJs("js/payroll/payroll_sheet.incentive.js", true, $tempData);

        $this->load->view('core/templates/header');
        $this->load->view('payroll/payroll/payroll_incentive');
        $this->load->view('core/templates/footer');
    }

    // show history page
    public function history() {
        $this->core_layout->setPageTitle("Payroll - History");
        $this->core_layout->setPrivilegeName("payroll_history");
        $this->core_layout->addJs("js/payroll/history.script.js", true);
        $this->core_layout->addJs("js/buttons.print.min.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('payroll/payroll/payroll_history');
        $this->load->view('core/templates/footer');
    }

    public function payroll_masterlist() {
        $this->core_layout->setPageTitle("Payroll - Payroll Masterfile");
        $this->core_layout->setPrivilegeName("payroll");
        $this->core_layout->addJs("js/payroll/payroll_masterfile.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('payroll/payroll/payroll_masterfile');
        $this->load->view('core/templates/footer');
    }


    public function payslip() {

        $tempData = array();

        $_temp = $this->payroll->select2CompanyData();
        $_company = $this->db->select('IFNULL(company_id, 0) as company_id')->get_where('gccmaster.tblemployees', array('id' => $this->userData['emp_id']))->row();
        $companyId = (int)$_company->company_id;

        $filter = array_filter($_temp, function ($value) use ($companyId) {
            return is_object($value) ? ((int)$value->id === $companyId) : ((int)$value['id'] === $companyId);
        });

        $result = array_values($filter)[0] ?? null;
        $tempData["company"] = $result;
        $tempData["dropdown_company"] = $this->payroll->select2CompanyData();

        $this->core_layout->setPageTitle("Payroll - Payslip");
        $this->core_layout->setPrivilegeName("payroll_payslip");
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/payroll/payslip/payslip.script.js", true, $tempData);
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
        $this->load->view('core/templates/header');
        $this->load->view('payroll/payroll/payslip');
        $this->load->view('core/templates/footer');
    }

    public function payroll_summary() {
        $tempData = array(); 
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();

        $this->core_layout->setPageTitle("Payroll - Summary");
        $this->core_layout->setPrivilegeName("payroll_summary");
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/payroll/payroll_summary.js", true, $tempData);

        $this->load->view('core/templates/header');
        $this->load->view('payroll/payroll/payroll_summary');
        $this->load->view('core/templates/footer');
    }

    public function printable(){
        $data = array();
        $this->load->view("core/templates/printable/header");
        $this->load->view("payroll/payroll/printable/print_content", $data);
        $this->load->view("core/templates/printable/footer");
    }

    public function signatory(){
        $this->core_layout->setPageTitle("Payroll - Signatory");
        $this->core_layout->setPrivilegeName("payroll_signatory");
        $this->core_layout->addJs("vendors/custom/jquery-ui/jquery-ui.bundle.js");
        $this->core_layout->addJs("js/payroll/signatory.script.js", true);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/payroll/signatory");
        $this->load->view("core/templates/footer");
    }

    public function set_printable_payslip() {
        $data = $this->payroll->setPrintablePayslip();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function set_printable_payslip_aknowledgement() {
        $data = $this->payroll->setPrintablePayslipAknowledgement();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function set_printable_payslip_netpay() {
        $data = $this->payroll->setPrintablePayslipNetpay();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    public function send_telegram() {
        $data = $this->payroll->sendTelegram();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function send_email(){
    $data = $this->payroll->sendEmail();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }
    
    public function test_email($id=77194){
        $this->payroll->testEmail($id);
    }

    public function payroll_sheet_masterfile() {
        $data = $this->payroll->payrollSheetMasterfile();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function select_incentive_type() {
        $data = $this->payroll->selectIncentiveType();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function select_employee() {
        $data = $this->payroll->selectEmployee();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function select_employee_pssummary() {
        $data = $this->payroll->selectEmployeePayrollSummary();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function select_company() {
        $data = $this->payroll->selectCompany();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    
    public function select_department() {
        $data = $this->payroll->selectDepartment();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function payroll_masterfile() {
        $data = $this->payroll->payrollMasterfile();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function update_employee_payroll_data() {
        $data = $this->payroll->updateEmployeePayrollData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function generate_payroll_sheet_incentive($_start = null, $_end = null) {
        $start = date('Y-m-d', strtotime($_start));
        $end = date('Y-m-d', strtotime($_end));
        $_post = $this->input->post();

        if (isset($_post) && !empty($_post)) {
            $post = $this->objectToStdClass($_post);
            if(isset($post->date_range) && $post->date_range){
                $date_range = explode("-", $post->date_range);
                $start = date('Y-m-d', strtotime(trim($date_range[0])));
                $end = date('Y-m-d', strtotime(trim($date_range[1])));
            }
        }

        $employees = $this->payroll->generatePayrollSheetIncentive($start, $end, $_post);
        echo json_encode($employees);
    }



    public function generate_payroll_sheet($_start = null, $_end = null) {
        $start = date('Y-m-d', strtotime($_start));
        $end = date('Y-m-d', strtotime($_end));
        $_post = $this->input->post();

        if (isset($_post) && !empty($_post)) {
            $post = $this->objectToStdClass($_post);
            if(isset($post->date_range) && $post->date_range){
                $date_range = explode("-", $post->date_range);
                $start = date('Y-m-d', strtotime(trim($date_range[0])));
                $end = date('Y-m-d', strtotime(trim($date_range[1])));
            }
        }

        $employees = $this->payroll->generatePayrollSheet($start, $end, $_post);
        echo json_encode($employees);
    }

    public function get_payroll_sheet() {
        $_post = $this->input->post();
        $start = null;
        $end = null;
        $show_posted = "_all";
        if (isset($_post) && !empty($_post)) {
            $post = $this->objectToStdClass($_post);
            if (!empty($_post["date_range"])) {
                $date_range = explode("-", $post->date_range);
                $start = date('Y-m-d', strtotime(trim($date_range[0])));
                $end = date('Y-m-d', strtotime(trim($date_range[1])));
            }
            $show_posted = isset($_post["show_posted"]) && $_post["show_posted"] ? $_post["show_posted"]: "_all";
        }

        echo json_encode($this->payroll->getPayrollSheet($start, $end, $show_posted));
    }

    public function get_payroll_sheet_incentive() {
        $_post = $this->input->post();
        $start = null;
        $end = null;
        $show_posted = "_all";
        if (isset($_post) && !empty($_post)) {
            $post = $this->objectToStdClass($_post);
            if (!empty($_post["date_range"])) {
                $date_range = explode("-", $post->date_range);
                $start = date('Y-m-d', strtotime(trim($date_range[0])));
                $end = date('Y-m-d', strtotime(trim($date_range[1])));
            }
            $show_posted = isset($_post["show_posted"]) && $_post["show_posted"] ? $_post["show_posted"]: "_all";
        }

        echo json_encode($this->payroll->getPayrollSheetIncentive($start, $end, $show_posted));
    }

    private function objectToStdClass($object) {
        return (object)(array)$object;
    }

    public function generate_payroll_payslip() {
        $this->core_layout->setPrivilegeName("payroll_payslip");
        echo json_encode($this->payroll->generatePayrollPayslip());
    }

    public function get_payout_schedule() {
        echo json_encode($this->payroll->getPayoutSchedule());
    }

    public function get_payout_schedule_occurrence($payout_schedule_id) {
        echo json_encode($this->payroll->getPayoutScheduleOccurrence($payout_schedule_id));
    }

    public function get_remittance_parameters() {
        echo json_encode($this->payroll->getRemittanceParameters(0));
    }

    public function update_remittance_parameters() {
        echo json_encode($this->payroll->updateRemittanceParameters());
    }

    public function manage_created_adjustments($mode) {
        echo json_encode($this->payroll->manageCreatedAdjustments($mode));
    }

    public function manage_custom_adjustments($mode) {
        echo json_encode($this->payroll->manageCustomAdjustments($mode));
    }

    public function get_custom_adjustment_particulars() {
        echo json_encode($this->payroll->getCustomAdjustmentParticulars());
    }

    public function get_payroll_sheet_custom_adjustments($payroll_sheet_id) {
        echo json_encode($this->payroll->getPayrollSheetCustomAdjustments($payroll_sheet_id));
    }

    public function get_custom_adjustment($cadj_id) {
        echo json_encode($this->payroll->getCustomAdjustment($cadj_id));
    }

    public function get_created_adjustment($adj_id) {
        echo json_encode($this->payroll->getCreatedAdjustment($adj_id));
    }

    public function delete_custom_adjustments($cadj_id) {
        echo json_encode($this->payroll->deleteCustomAdjustments($cadj_id));
    }

    public function delete_created_adjustments($adj_id) {
        echo json_encode($this->payroll->deleteCreatedAdjustments($adj_id));
    }

    public function get_payroll_sheet_created_adjustments($payroll_sheet_id) {
        echo json_encode($this->payroll->getPayrollSheetCreatedAdjustments($payroll_sheet_id));
    }

    public function created_approval_adjustments() {
        echo json_encode($this->payroll->createdApprovalAdjustments());
    }

    public function post_payroll_sheet() {
        echo json_encode($this->payroll->postPayrollSheet());
    }

    public function get_employee_timesheet() {
        $post = (object)$this->input->post();
        $dates = explode("-", $post->date_range);
        $emp_id = $post->emp_id;

        echo json_encode($this->payroll->getEmployeeTimesheet($emp_id, trim($dates[0]), trim($dates[1])));
    }

    public function get_pay_rate_settings() {
        echo json_encode($this->payroll->getPayRateSettings());
    }

    public function update_pay_rate_setting() {
        echo json_encode($this->payroll->updatePayRateSetting());
    }

    public function get_company_info($company_id) {
        echo json_encode($this->db->where("id", $company_id)->get("gcchris.tblcompanies")->row());
    }

    public function confirm_undo_posting($payroll_sheet_id) {
        echo json_encode($this->payroll->confirmUndoPosting($payroll_sheet_id));
    }

    public function get_posted_payroll_sheet_years() {
        echo json_encode($this->payroll->getPostedPayrollSheetYears());
    }

    public function get_payroll_history() {
        echo json_encode($this->payroll->getPayrollHistory());
    }

    public function get_payroll_payslip_table_request() {
        echo json_encode($this->payroll->getPayrollPayslipTableRequest());
    }
    public function get_current_payroll_payslip($id=null) {
        echo json_encode($this->payroll->getCurrentPayrollPayslip($id));
    }
    public function select_payroll_group() {
        $data = $this->payroll->selectPayrollGroup();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    public function get_payroll_group_multiple() {
        $data = $this->payroll->getPayrollGroupMultiple();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    public function get_payroll_settings() {
        $data = $this->payroll->getPayrollSettings();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    public function update_payroll_settings() {
        $data = $this->payroll->updatePayrollSettings();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    public function update_sss_contribution_basis() {
        $data = $this->payroll->updateSssContributionBasis();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    public function update_zeronetpay_parameters() {
        $data = $this->payroll->updateZeroNetpayParameters();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function generate_ps_contribution(){
        $data = $this->payroll->generatePayrollSheetContribution();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function generate_employees_no_cont_acct(){
        $data = $this->payroll->generateEmployeesNoContAcct();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function update_employee_company(){
        $this->payroll->updateEmployeeCompany();
    }
    
    public function create_signatory_content(){
        $html = $this->load->view("payroll/payroll/modals/content/signatory_portlet", null, true);
        $data = array("html"=>$html);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function create_printable_signatory(){
        $data = $this->payroll->createPrintableSignatory();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function update_printable_signatory(){
        $data = $this->payroll->updatePrintableSignatory();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_payroll_signatory(){
        $data = $this->payroll->getPayrollSignatory();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_current_signatory($id=null){
        $data = $this->payroll->getCurrentSignatory($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_current_signatory_by_company_and_type($id=null, $type=null){
        $data = $this->payroll->getCurrentSignatoryByCompanyAndType($id, $type);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function update_printable_signatories(){
        $data = $this->payroll->updatePrintableSignatories();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function reset_printable_signatories(){
        $data = $this->payroll->resetPrintableSignatories();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    
    public function generate_payroll_summary(){
        $data = $this->payroll->generatePayrollSummary();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    public function generate_overtime_report(){
        $data = $this->payroll->generateOvertimeReport();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    public function get_payroll_summary_request(){
        $data = $this->payroll->getPayrollSummaryRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    public function generate_payroll_monthly_week_count($year=null){
        $data = $this->payroll->generatePayrollMonthlyWeekCount($year);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    public function update_payrollsheet_printed_status(){
        $data = $this->payroll->updatePayrollsheetPrintedStatus();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_paid_contributions(){
        $data = $this->dashboard->paidContributions();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_overtime(){
        $data = $this->dashboard->paidOvertime();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_allowances(){
        $data = $this->dashboard->paidAllowance();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_tax(){
        $data = $this->dashboard->paidTax();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function export_excel(){
        $data = $this->dashboard->exportExcel();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_payroll_per_department()
    {
        $data = $this->dashboard->getPayrollPerDepartment();
        echo json_encode($data);
    }

    public function get_payroll_per_position()
    {
        $data = $this->dashboard->getPayrollPerPosition();
        echo json_encode($data);
    }

    public function get_payroll_benefits()
    {
        $data = $this->dashboard->paidContributionGraph();
        echo json_encode($data);
    }

    public function get_retention_rate() {
        $data = $this->dashboard->getRetentionRate();
        echo json_encode($data);
    }

    
    public function get_loans() {
        $data = $this->dashboard->getLoans();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    
    public function get_salary_hike() {
        $data = $this->dashboard->getSalaryHike();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    } 

    public function get_overtime_graph() {
        $data = $this->dashboard->getOvertimeGraph();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_allowance_graph() {
        $data = $this->dashboard->getAllowanceGraph();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_tax_graph() {
        $data = $this->dashboard->getTaxGraph();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_contribution_graph() {
        $data = $this->dashboard->getContributionGraph();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_payroll_incentive(){
        $qTemp = $this->db->get_where("payroll.payroll_sheet", array("is_bonus"=>1, "pay_date"=>"2022-06-10"));
        if($qTemp->num_rows() > 0){
            $tempArray = array(
                base_url('assets/fonts/montserrat/montserrat.css'),
                base_url('assets/fonts/roboto/roboto.css'),
                /*** base_url('assets/plugins/bootstrap/bootstrap.min.css') ***/
            );
            echo "<html>
            <head>";
            foreach ($tempArray as $kk => $vv) {
                echo "<link href='{$vv}' rel='stylesheet' type='text/css' />";
            }
            echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css' rel='stylesheet' type='text/css' />";
            echo "<link href='https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css' />";
            echo "</head>
                <body>
                <table class='dataTable' width='100%'>
                <thead>
                    <tr>
                        <th class='text-center'>#</th>
                        <th class='text-center'>Employee Name</th>
                        <th class='text-center'>Rate</th>
                        <th class='text-center'>Allowance</th>
                        <th class='text-center'>Days</th>
                        <th class='text-center'>Basic</th>
                        <th class='text-center'>Allowance</th>
                        <th class='text-center'>Adjustments</th>
                        <th class='text-center'>Gross</th>
                        <th class='text-center'>Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                <div class='text-center m--regular-font-size-lg2'>test company</div>
                <div class='text-center m--regular-font-size-sm1 text-muted'>test address</div>
                <div class='m--regular-font-size-lg1'>PAYROLL SHEET - INCENTIVE</div>
                <div class='m--regular-font-size-sm1 mt-2'>PAY DATE: test123</div>
                <div class='m--regular-font-size-sm1 mt-1'>PAY COVERAGE: test123</div>
                ";
                foreach ($qTemp->result() as $key => $value) { $tempKey = $key + 1;
                    echo "<tr>";
                    echo "<td class='text-center'>{$tempKey}</td>";
                    echo "<td>{$value->emp_id}</td>";
                    echo "<td class='text-center'>{$value->rate}</td>";
                    echo "<td class='text-center'>{$value->rate}</td>";
                    echo "<td class='text-center'>{$value->no_of_days}</td>";
                    echo "<td class='text-end'>{$value->basic_rate}</td>";
                    echo "<td class='text-center'>{$value->total_allowances}</td>";
                    echo "<td class='text-center'>{$value->total_allowances}</td>";
                    echo "<td class='text-end'>{$value->gross_pay}</td>";
                    echo "<td class='text-end'>{$value->net_pay}</td>";
                    echo "</tr>";
                }
            echo "</tbody>
            <tfoot>
                <tr>
                    <th colspan='5' class='text-end'>Grand Total</th>
                    <th>Basic</th>
                    <th>Allowance</th>
                    <th>Adjustments</th>
                    <th>Gross</th>
                    <th>Net Pay</th>
                </tr>
            </tfoot>
            </table></body></html>";
        }
    }

    // for getting emp with loans
    public function get_emp_with_loans(){
        $data = $this->payroll->getEmpWithLoans();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function merge_employee_ca_loan(){
        $data = $this->payroll->mergeEmployeeCaLoan();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function checking_payroll_sheet_data(){
        $data = $this->payroll->checkingPayrollSheetData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function update_existing_payroll_sheet_data(){
        $data = $this->payroll->updateExistingPayrollSheetData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function select_payroll_group_payslip() {
        $this->core_layout->setPrivilegeName("payroll_payslip");
        $privilege = $this->core_layout->getCurrentActions();

        $data = $this->payroll->selectPayrollGroupPayslip($privilege);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function select_employee_by_privileges() {
        $this->core_layout->setPrivilegeName("payroll_payslip");
        $privilege = $this->core_layout->getCurrentActions();

        $data = $this->payroll->selectEmployeeByPrivileges($privilege);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function set_printable_payslip_option() {
        $data = $this->payroll->setPrintablePayslipOthers();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function undo_printed_payroll_sheet(){
        $data = $this->payroll->undo_printed_payroll_sheet();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    
    public function check_printed_payslip(){
        $data = $this->payroll->check_printed_payslip();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function select_payroll_group_by_status() {
        $data = $this->payroll->selectPayrollGroupByStatus();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_payroll_group_multiple_by_status() {
        $data = $this->payroll->getPayrollGroupMultipleByStatus();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function select_employee_by_status() {
        $data = $this->payroll->selectEmployeeByStatus();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function select_employee_by_company() {
        $data = $this->payroll->selectEmployeeByCompany();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
}
