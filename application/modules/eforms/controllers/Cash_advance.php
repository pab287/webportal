<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cash_advance extends MY_Controller {
    private $user_data = array();
    private $current_action =  array();
	public function __construct()
	{
        parent::__construct();
        $this->authenticate->setModuleAccess("eforms-cash_advance");
        $this->authenticate->doRedirect();
        $this->load->model('Cash_advance_m','cash_advance');
        $this->core_layout->setPrivilegeName("eforms_cash_advance");
        $this->user_data = $this->session->userdata("logged_in");
        $this->current_action = $this->core_layout->getCurrentActions();
        $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
        $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
        date_default_timezone_set('Asia/Manila');
    }
    
    public function index(){
        $this->core_layout->addJs("global/js/amcharts4/core.js", true);
        $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
        $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
        $this->core_layout->addJs("js/eforms/cash_advance/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/cash_advance/index');
        $this->load->view('core/templates/footer');
    }

    public function masterfile() {
        $this->core_layout->setPageTitle("Cash Advance - Masterfile");
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        $approving_authority = (in_array("approving_authority", $this->current_action)) ? true : false;

        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);

        if($approving_authority && ($this->user_data['emp_id']!=1)){
            $this->core_layout->addJs("js/eforms/cash_advance/approval_cash_advance.js", true);
            $this->core_layout->addJs("js/eforms/cash_advance/inputmask_bundle.js", true);
            $this->core_layout->setPrivilegeName("ca_masterfile");

            $this->load->view('core/templates/header');
            $this->load->view('eforms/cash_advance/approval_cash_advance');
            $this->load->view('core/templates/footer');
        }else{
            $this->core_layout->addJs("js/eforms/cash_advance/masterfile_cash_advance.js", true);
            $this->core_layout->addJs("js/eforms/cash_advance/inputmask_bundle.js", true);
            $this->core_layout->setPrivilegeName("ca_masterfile");
    
            $this->load->view('core/templates/header');
            $this->load->view('eforms/cash_advance/masterfile_cash_advance');
            $this->load->view('core/templates/footer');
        }
    }
    
    public function archive() {
        $this->core_layout->setPageTitle("Cash Advance - Archive");
        $this->core_layout->addJs("js/eforms/cash_advance/archive_cash_advance.js", true);
        $this->core_layout->addJs("js/eforms/cash_advance/inputmask_bundle.js", true);
        $this->core_layout->setPrivilegeName("ca_archive");

        $this->load->view('core/templates/header');
        $this->load->view('eforms/cash_advance/archive_cash_advance');
        $this->load->view('core/templates/footer');
    }

    public function reports(){
        $this->core_layout->setPageTitle("Cash Advance - Reports");
        $this->core_layout->addJs("js/dataTables.buttons.min.js", true);
        $this->core_layout->addJs("js/buttons.flash.min.js", true);
        $this->core_layout->addJs("js/jszip.min.js", true);
        $this->core_layout->addJs("js/pdfmake.min.js", true);
        $this->core_layout->addJs("js/vfs_fonts.js", true);
        $this->core_layout->addJs("js/buttons.html5.min.js", true);
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addCss("css/buttons.dataTables.min.css", true);

        $this->core_layout->addJs("js/eforms/cash_advance/reports_cash_advance.js", true);
        $this->core_layout->setPrivilegeName("ca_reports");

        $this->load->view('core/templates/header');
        $this->load->view('eforms/cash_advance/reports_cash_advance');
        $this->load->view('core/templates/footer');
    }

    function approval_list(){
        $data = $this->cash_advance->approvalList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }    

    function archive_list(){
        $data = $this->cash_advance->archiveList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }    

    function get_datatable_request(){
        $data = $this->cash_advance->getDatatableRequest();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function new_cash_advance(){
        $this->core_layout->setPageTitle("Cash Advance - New Cash Advance");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addCss("plugins/lightbox/js/lightbox.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");

        $this->core_layout->addJs("js/eforms/cash_advance/new_cash_advance.js", true);
        $this->core_layout->addJs("plugins/lightbox/js/lightbox.js");
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);

		$this->load->view('core/templates/header');
        $this->load->view('eforms/cash_advance/new_cash_advance');
        $this->load->view('core/templates/footer');
    }

    
    function get_employee(){
        $data = $this->cash_advance->getEmployee();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_employee_detail(){
        $this->input->get();
        $data =  $this->cash_advance->getEmployeeDetail();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
        
    }

    function save_cash_advance(){
        $this->input->get();
        $data =  $this->cash_advance->saveCashAdvance();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function view_cash_advance(){
        $this->core_layout->setPageTitle("Cash Advance - View Cash Advance");
        $this->core_layout->setPrivilegeName("ca_masterfile");

        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
        $this->core_layout->addJs("js/eforms/cash_advance/view_cash_advance.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('eforms/cash_advance/view_cash_advance');
        $this->load->view('core/templates/footer');
    }

    function get_cash_advance_details($id){
        $data = $this->cash_advance->getCashAdvanceDetail($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_cashadvance_content($id){
        $data = $this->cash_advance->getCashAdvanceContent($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_cash_advance(){
        $this->core_layout->setPageTitle("Cash Advance - Edit Cash Advance");

        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");

        $this->core_layout->addJs("js/eforms/cash_advance/edit_cash_advance.js",true);
		$this->load->view('core/templates/header');
        $this->load->view('eforms/cash_advance/edit_cash_advance');
        $this->load->view('core/templates/footer');
    }

    function update_cash_advance($id){
        $data =  $this->cash_advance->updateCashAdvance($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function recommend_details($id){
        $data = $this->cash_advance->recommendDetails($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function recommend_update($id){
        $data =  $this->cash_advance->recommendUpdate($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_recommend_update($id){
        $data =  $this->cash_advance->undoRecommendUpdate($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function disapprove_update($id){
        $data =  $this->cash_advance->disapproveUpdate($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_disapproval_update($id){
        $data =  $this->cash_advance->undoDisapprovalUpdate($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function cancel_update($id){
        $data =  $this->cash_advance->cancelUpdate($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_hr_update($id){
        $data =  $this->cash_advance->setHrUpdate($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_hr_details($id){
        $data =  $this->cash_advance->getCashAdvanceDetail($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_acctg_update($id){
        $data =  $this->cash_advance->setAcctgUpdate($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_acctg_details($id){
        $data =  $this->cash_advance->getCashAdvanceDetail($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function approve_update($id){
        $data =  $this->cash_advance->approveUpdate($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_approval_update($id){
        $data =  $this->cash_advance->undoApprovalUpdate($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_daily()
    {
        $data = $this->cash_advance->getDaily();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    function get_weekly()
    {
        $data = $this->cash_advance->getWeekly();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    function print_cash_advance(){
        $this->core_layout->addJs("js/eforms/cash_advance/print_cash_advance.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('eforms/cash_advance/print_cash_advance');
        $this->load->view('core/templates/footer');
    }

    function print_cash_advance_details(){
        $id = $this->input->get();
        $data = $this->cash_advance->printCashAdvanceDetails($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_file_content($id){
        $data =  $this->cash_advance->getFileContent($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function upload_file(){
        $data =  $this->cash_advance->uploadFile();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function getAccountingDetails($id){
        $data =  $this->cash_advance->get_ca_accounting_details($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function get_cash_analytics_for_dashboard() {
        $data = $this->cash_advance->m_get_cash_analytics_for_dashboard();
        echo json_encode($data);
    }

    // public function get_ca_status($data){
    //     $data = $this->cash_advance->get_ca_status_details($data);
	// 	$this->output
    //     ->set_content_type('json')
    //     ->set_output(json_encode($data));
    // }
    public function getCA_details($id){
        $data = $this->cash_advance->getCADetail($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function get_cashadvance_history(){
        $post = $this->input->post();
        $data = $this->cash_advance->getEmployeeLoanPaymentHistory($post['ref']);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    public function pending_balance() {
        $this->core_layout->setPageTitle("Cash Advance - Masterfile");
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        $approving_authority = (in_array("approving_authority", $this->current_action)) ? true : false;

        if($approving_authority && ($this->user_data['emp_id']!=1)){
            $this->core_layout->addJs("js/eforms/cash_advance/approval_cash_advance.js", true);
            $this->core_layout->addJs("js/eforms/cash_advance/inputmask_bundle.js", true);
            $this->core_layout->setPrivilegeName("ca_masterfile");

            $this->load->view('core/templates/header');
            $this->load->view('eforms/cash_advance/approval_cash_advance');
            $this->load->view('core/templates/footer');
        }else{
            $this->core_layout->addJs("js/eforms/cash_advance/pending_masterfile.js", true);
            $this->core_layout->addJs("js/eforms/cash_advance/inputmask_bundle.js", true);
            $this->core_layout->setPrivilegeName("ca_masterfile");
    
            $this->load->view('core/templates/header');
            $this->load->view('eforms/cash_advance/view_cash_pending');
            $this->load->view('core/templates/footer');
        }
    }
    function get_pending_datatable_request($type){
        $data = $this->cash_advance->getPendingDatatableRequest($type);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    // test email
    function test_ca_email($state = false){
        if($state){
            $sent = $this->cash_advance->email_send_approve('Mario Dimakulangan', 'CA10-1001-001', '5000', 'test email for approved cash advance', '');
            if($sent['status']){
                echo 'sent!';
            }else{
                echo 'failed to send!';
            }
        }else{
            $data = array("employee_name"=> 'Mario Dimakulangan', 'CA10-1001-001', "amount"=>'5000', "purpose"=>'test email for approved cash advance'); //dummy data
            $this->load->view("eforms/email_templates/email_ca_approve_template", $data);
        }
    }

    function test_ca_activate($state = false){
        if($state){
            $sent = $this->cash_advance->loan_activated_email('Mario Dimakulangan', 'CA10-1001-001', '5000', 'test email for approved cash advance', '');
            if($sent['status']){
                echo 'sent!';
            }else{
                echo 'failed to send!';
            }
        }else{
            $data = array("employee_name"=> 'Mario Dimakulangan', 'CA10-1001-001', "amount"=>'5000', "purpose"=>'test email for approved cash advance'); //dummy data
            $this->load->view("eforms/email_templates/email_ca_loan_deduction", $data);
        }
    }

    function for_posting($id){
        $data = $this->cash_advance->posting_update($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_for_posting($id){
        $data = $this->cash_advance->undo_for_posting($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function for_posted($id){
        $data = $this->cash_advance->posted_update($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_posted($id){
        $data = $this->cash_advance->undo_posted($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function final_approval($id){
        $data = $this->cash_advance->final_approval_update($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_awaiting_approval($id){
        $data = $this->cash_advance->undo_awaiting_approval($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_blacklisted_employee(){
        $data = $this->cash_advance->get_blacklisted();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_for_blacklist_employee(){
        $data = $this->cash_advance->getForBlacklistEmployee();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function blacklist_employee(){
        $data = $this->cash_advance->set_blacklist_emp();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function remove_blacklist_employee(){
        $data = $this->cash_advance->remove_blacklist_emp();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_interest_percentage(){
        $data = $this->cash_advance->setInterestPercentage();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function temp_upload_file(){
        $data = $this->cash_advance->tempUploadFile();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_temp_upload_files(){
        $data = $this->cash_advance->tempUpdateUploadFiles();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_for_final($id){
        $data = $this->cash_advance->undoForFinal($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function get_cash_advance_reports(){
        $data = $this->cash_advance->getCashAdvanceReport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function export_report($type){
        $data = $this->cash_advance->exportReport($type);
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

}
