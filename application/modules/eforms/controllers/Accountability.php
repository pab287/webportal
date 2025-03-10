<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Accountability extends MY_Controller {
	public function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("eforms-accountability");
        $this->authenticate->doRedirect();
        $this->core_layout->setPrivilegeName("eforms_accountability");
    
        $this->load->model("Accountability_m","accountability");
	}
	public function index(){
        $this->core_layout->addJs("global/js/amcharts4/core.js", true);
        $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
        $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
        $this->core_layout->addJs("js/eforms/accountability/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/index');
        $this->load->view('core/templates/footer');
    }

    public function masterfile(){
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

        $this->core_layout->setPageTitle("Accountability - Assets for Releasing");
        $this->core_layout->setPrivilegeName("accountability_for_releasing");
        $this->core_layout->addJs("js/eforms/accountability/masterfile.js", true);

        $data['companies'] = $this->accountability->getCompanyList();

        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/masterfile', $data);
        $this->load->view('core/templates/footer');
    }

    public function archive(){
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

        $this->core_layout->setPageTitle("Accountability - Archive");
        $this->core_layout->setPrivilegeName("accountability_archive");
        $this->core_layout->addJs("js/eforms/accountability/archive.js", true);

        $data['companies'] = $this->accountability->getCompanyList();

        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/archive', $data);
        $this->load->view('core/templates/footer');
    }

    public function new_accountability(){
        $this->core_layout->setPageTitle("Accountability - New Accountability");
        $this->core_layout->setPrivilegeName("accountability_for_releasing");
        $this->core_layout->addJs("js/eforms/accountability/new_accountability.js", true);
        $this->accountability->clearTemp();
        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/new_accountability');
        $this->load->view('core/templates/footer');
    }

    public function view_accountability(){
        $this->core_layout->setPageTitle("Accountability - View Accountability");
        $this->core_layout->setPrivilegeName("accountability_for_releasing");
        $this->core_layout->addJs("js/eforms/accountability/table_footer/sum.js", true);
        $this->core_layout->addJs("js/eforms/accountability/view_accountability.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/view_accountability');
        $this->load->view('core/templates/footer');
    }

    public function edit_accountability(){
        $this->core_layout->setPageTitle("Accountability - Edit Accountability");
        $this->core_layout->setPrivilegeName("accountability_for_releasing");
        $this->core_layout->addJs("js/eforms/accountability/edit_accountability.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/edit_accountability');
        $this->load->view('core/templates/footer');
    }
    
    public function returned_accountability(){
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

        $this->core_layout->setPageTitle("Accountability - Released/Returned Accountability");
        $this->core_layout->setPrivilegeName("accountability_released_returned_assets");
        $this->core_layout->addJs("js/eforms/accountability/returned_accountability.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/returned_accountability');
        $this->load->view('core/templates/footer');
    }

    public function view_returned_accountability(){
        $this->core_layout->setPageTitle("Accountability - View Returned Accountability");
        $this->core_layout->setPrivilegeName("accountability_released_returned_assets");
        $this->core_layout->addJs("js/eforms/accountability/table_footer/sum.js", true);
        $this->core_layout->addJs("js/eforms/accountability/view_returned_accountability.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/view_returned_accountability');
        $this->load->view('core/templates/footer');
    }

    public function view_released_accountability(){
        $this->core_layout->setPageTitle("Accountability - View Released Accountability");
        $this->core_layout->setPrivilegeName("accountability_released_returned_assets");
        $this->core_layout->addJs("js/eforms/accountability/table_footer/sum.js", true);
        $this->core_layout->addJs("js/eforms/accountability/view_returned_accountability.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/view_returned_accountability');
        $this->load->view('core/templates/footer');
    }

    public function print_accountability(){
        $this->core_layout->setPrivilegeName("accountability_for_releasing");
        $this->core_layout->addJs("js/eforms/accountability/print_accountability.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/print_accountability');
        $this->load->view('core/templates/footer');
    }

    public function print_returned_accountability(){
        $this->core_layout->setPrivilegeName("accountability_released_returned_assets");
        $this->core_layout->addJs("js/eforms/accountability/print_returned_accountability.js", true);
        //$this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/print_returned_accountability');
        //$this->load->view('core/templates/footer');
    }

    function masterfile_list(){
        $this->core_layout->setPrivilegeName("accountability_for_releasing");
        $data = $this->accountability->masterfileList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function archive_list(){
        $this->core_layout->setPrivilegeName("accountability_archive");
        $data = $this->accountability->archiveList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function issued_to_lookup(){
        $this->core_layout->setPrivilegeName("accountability_for_releasing");
        $data = $this->accountability->issuedToLookup();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
  
    function get_file_under(){
        $data = $this->accountability->getFileUnder();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function contractor_lookup(){
        $data = $this->accountability->contractorLookup();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function list_temp(){
        $data = $this->accountability->listTemp();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function new_asset_temp(){
        $data = $this->accountability->newAssetTemp();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_asset_detail($id){
        $data = $this->accountability->getAssetDetail($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function asset_comp_temp($id){
        $data = $this->accountability->assetCompTemp($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function new_vehicle_temp(){
        $data = $this->accountability->newVehicleTemp();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function get_vehicle_detail($id){
        $data = $this->accountability->getVehicleDetail($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function vehicle_comp_temp($id){
        $data = $this->accountability->vehicleCompTemp($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function multiple_temp(){
        $data = $this->accountability->multipleTemp();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_asset_temp(){
        $data = $this->accountability->saveAssetTemp();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_multiple_temp(){
        $data = $this->accountability->saveMultipleTemp();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_multiple_edit($id){
        $data = $this->accountability->saveMultipleEdit($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_asset_edit($id){
        $data = $this->accountability->saveAssetEdit($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_temp($id){
        $data = $this->accountability->deleteTemp($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete($id){
        $data = $this->accountability->delete($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function clear_temp(){
        $data = $this->accountability->clearTemp();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function clear($id){
        $data = $this->accountability->clear($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_temp_details($id){
        $data = $this->accountability->editTempDetails($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_details($id){
        $data = $this->accountability->editDetails($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_asset_temp($id){
        $data = $this->accountability->editAssetTemp($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_asset($id){
        $data = $this->accountability->editAsset($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_vehicle_temp(){
        $data = $this->accountability->saveVehicleTemp();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_vehicle_edit($id){
        $data = $this->accountability->saveVehicleEdit($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_temp(){
        $data = $this->accountability->saveTemp();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function save_edit($id){
        $data = $this->accountability->saveEdit($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function content_detail($id){
        $data = $this->accountability->contentDetail($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function content_body_detail($id){
        $data = $this->accountability->contentBodyDetail($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    // return accountability changes
    function ret_content_body_detail($id){
        $data = $this->accountability->ret_contentBodyDetail($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_acct_note($id){
        $data = $this->accountability->setAcctNote($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_acctg_note($id){
        $data = $this->accountability->undoAcctgNote($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_hr_note($id){
        $data = $this->accountability->setHrNote($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_hr_note($id){
        $data = $this->accountability->undoHrNote($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function set_release_note($id){
        $data = $this->accountability->setReleaseNote($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_release_note($id){
        $data = $this->accountability->undoReleaseNote($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function cancel_accountability($id){
        $data = $this->accountability->cancelAccountability($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function released_assets(){
        $this->core_layout->setPrivilegeName("accountability_for_releasing");
        $data = $this->accountability->releasedAssets(0, true);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function returned_assets(){
        $this->core_layout->setPrivilegeName("accountability_for_releasing");
        $data = $this->accountability->releasedAssets(1, false);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_unreturned_remarks($id){
        $data = $this->accountability->editUnreturnedRemarks($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function unreturned_remarks($id){
        $data = $this->accountability->unreturnedRemarks($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_returned_remarks($id){
        $data = $this->accountability->editReturnedRemarks($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function returned_remarks($id){
        $data = $this->accountability->returnedRemarks($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function return_accountability($id){
        $data = $this->accountability->returnAccountability($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_return_accountability($id){
        $data = $this->accountability->undoReturnAccountability($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function change_return_by($id){
        $data = $this->accountability->changeReturnBy($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function chart_data(){
        $data = $this->accountability->chartData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_accountability_logs($id=null){
        $data = $this->accountability->getAccountabilityLogs($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function fix_accountability_body_content(){
        $this->core_layout->setPageTitle("Accountability - Fix Accountability Body");
        $this->core_layout->setPrivilegeName("accountability_for_releasing");
        $this->core_layout->addJs("js/eforms/accountability/fix_accountability_body.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/accountability/fix_accountability_body');
        $this->load->view('core/templates/footer');
    }

    function generate_form_content_fix(){
        $data = $this->accountability->generateFormContentFix();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_form_content_fix($id=null, $isRefenceNo=false){
        $data = $this->accountability->updateFormContentFix($id, $isRefenceNo);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    function set_form_content_data(){
        $data = $this->accountability->setFormContentData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function resend_email($id=null){
        $data = $this->accountability->resendEmail($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    // unreturn return checkbox controller
    function remarks(){
        $data = $this->accountability->remarks_status();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_all_acct_is_borrowed_zero($type=1){
        $data = $this->accountability->getAllAcctIsBorrowedZero($type);
    }

    function export_event_log($export){
        $data = $this->accountability->exportData($export);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function export_event_log_archive($export){
        $data = $this->accountability->exportDataArchive($export);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

}