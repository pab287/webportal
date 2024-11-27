<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Overtime extends MY_Controller {
    private $user_data = array();
    private $current_action =  array();
	public function __construct()
	{
        parent::__construct();
        $this->authenticate->setModuleAccess("eforms-overtime");
        $this->authenticate->doRedirect();
        $this->load->model('Overtime_m','overtime');
        $this->core_layout->setPrivilegeName("eforms_overtime");
        $this->user_data = $this->session->userdata("logged_in");
        $this->current_action = $this->core_layout->getCurrentActions();
    }
    
    public function index(){
        $this->core_layout->addJs("global/js/amcharts4/core.js", true);
        $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
        $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
        $this->core_layout->addJs("js/eforms/overtime/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/overtime/index');
        $this->load->view('core/templates/footer');
    }

    public function masterfile() {
        $this->core_layout->setPageTitle("Overtime - Masterfile");
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addCss("plugins/lightbox/js/lightbox.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addJs("plugins/lightbox/js/lightbox.js");

        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);

        $this->core_layout->addJs("js/eforms/overtime/overtime_masterfile.js", true);
        $this->core_layout->setPrivilegeName("overtime_masterfile");

        $this->load->view('core/templates/header');
        $this->load->view('eforms/overtime/overtime_masterfile');
        $this->load->view('core/templates/footer');
    }

    public function archive() {
        $this->core_layout->setPageTitle("Overtime - Archive");
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

        $this->core_layout->addJs("js/eforms/overtime/overtime_archive.js", true);
        $this->core_layout->setPrivilegeName("overtime_masterfile");

        $this->load->view('core/templates/header');
        $this->load->view('eforms/overtime/overtime_archive');
        $this->load->view('core/templates/footer');
    }

    public function new_overtime() {
        $this->core_layout->setPageTitle("Overtime - Request Overtime");

        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addCss("plugins/lightbox/js/lightbox.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addJs("plugins/lightbox/js/lightbox.js");
        $this->core_layout->addJs("js/eforms/overtime/new_overtime.js", true);
        $this->core_layout->setPrivilegeName("overtime_masterfile");

        $this->load->view('core/templates/header');
        $this->load->view('eforms/overtime/new_overtime');
        $this->load->view('core/templates/footer');
    }

    function select_company() {
      $data = $this->overtime->selectCompany();
      $this->output
          ->set_content_type('json')
          ->set_output(json_encode($data));
  }

  function select_payroll_group() {
    $data = $this->overtime->selectPayrollGroup();
    $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
}

function select_payroll_group_multiple() {
  $data = $this->overtime->selectPayrollGroupMultiple();
  $this->output
      ->set_content_type('json')
      ->set_output(json_encode($data));
}

function select_employee() {
  $data = $this->overtime->selectEmployee();
  $this->output
      ->set_content_type('json')
      ->set_output(json_encode($data));
}

    public function edit_overtime() {
        $this->core_layout->setPageTitle("Overtime - Edit Overtime Request");

        $this->core_layout->addJs("js/eforms/overtime/edit_overtime.js", true);
        $this->core_layout->setPrivilegeName("overtime_masterfile");

        $this->load->view('core/templates/header');
        $this->load->view('eforms/overtime/edit_overtime');
        $this->load->view('core/templates/footer');
    }

    public function view_overtime() {
        $this->core_layout->setPageTitle("Overtime - Overtime Request Details");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addCss("plugins/lightbox/js/lightbox.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addJs("plugins/lightbox/js/lightbox.js");
        $this->core_layout->addJs("js/eforms/overtime/view_overtime.js", true);
        $this->core_layout->setPrivilegeName("overtime_masterfile");

        $this->load->view('core/templates/header');
        $this->load->view('eforms/overtime/view_overtime');
        $this->load->view('core/templates/footer');
    }

    public function reports(){
      $this->core_layout->setPageTitle("Overtime - Reports");
      $tempData["company"] = $this->overtime->select2CompanyData();
      $this->core_layout->addJs("js/eforms/overtime/overtime_reports.js", true,$tempData);
      $this->core_layout->setPrivilegeName("overtime_masterfile");
      $this->load->view('core/templates/header');
      $this->load->view('eforms/overtime/overtime_reports');
      $this->load->view('core/templates/footer');
    }

    public function get_reports(){
      $data = $this->overtime->getReports();
      $this->output
          ->set_content_type('json')
          ->set_output(json_encode($data));
    }

    function get_analytics_for_dashboard(){
        $data = $this->overtime->getAnalyticsForDashboard();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_daily(){
        $data = $this->overtime->getDaily();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_weekly(){
        $data = $this->overtime->getWeekly();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function overtime_masterfile(){
        $data = $this->overtime->overtimeMasterfile();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function overtime_archive(){
        $data = $this->overtime->overtimeArchive();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_employee(){
        $data = $this->overtime->getEmployee();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    function get_company(){
      $data = $this->overtime->getCompanyList();
  $this->output
      ->set_content_type('json')
      ->set_output(json_encode($data));
  }

    function get_employee_department_head(){
        $data = $this->overtime->getEmployeeDepartmentHead();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function import_approved_overtime(){
        $data = $this->overtime->importApprovedOvertime();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_employee_detail(){
        $data = $this->overtime->getEmployeeDetail();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_overtime(){
        $data = $this->overtime->saveOvertime();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_overtime_request_details($id){
        $data = $this->overtime->getOvertimeRequestDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function update_overtime($id){
        $data = $this->overtime->updateOvertime($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function approve_overtime($id){
        $data = $this->overtime->approveOvertime($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_approve_overtime($id){
        $data = $this->overtime->undoApproveOvertime($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function disapprove_overtime($id){
        $data = $this->overtime->disapproveOvertime($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_disapprove_overtime($id){
        $data = $this->overtime->undoDisapproveOvertime($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function cancel_overtime($id){
        $data = $this->overtime->cancelOvertime($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function print_overtime($id){
        $data = array();
        $query = $this->overtime->getOvertimeRequestDetails($id);
        
        $data["query"] = $query;
        $this->load->view('eforms/overtime/print_overtime', $data);
    }

    function temp_upload_file(){
        $data = $this->overtime->tempUploadFile();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_current_uploaded_file(){
        $data = $this->overtime->getCurrentUploadedFile();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function advanced_search_request(){
        $data = $this->overtime->advancedSearchRequest();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function temp_upload_csv_file(){
        $data = $this->overtime->tempUploadCsvFile();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function generateReferenceNo(){
        $data = $this->overtime->generateReferenceNoExisting();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function generateReferenceDetails(){
        $data = $this->overtime->generateReferenceDetails();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function export_event_log($export){
        $data = $this->overtime->exportData($export);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function export_event_log_archive($export){
        $data = $this->overtime->exportDataArchive($export);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function mass_update(){
        $data = $this->overtime->massUpdate();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function mass_approve(){
        $data = $this->overtime->massApprove();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    
    function mass_disapprove(){
        $data = $this->overtime->massDispprove();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

}