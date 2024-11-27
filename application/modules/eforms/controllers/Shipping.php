<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Shipping extends MY_Controller {
    private $user_data = array();
	public function __construct()
	{
        parent::__construct();
        $this->authenticate->setModuleAccess("eforms-shipping_advice");
        $this->authenticate->doRedirect();
        $this->load->model('Shipping_m','shipping');
        $this->core_layout->setPrivilegeName("eforms_shipping");
        $this->user_data = $this->session->userdata("logged_in");
    }
    
    public function index(){
        $this->core_layout->addJs("global/js/amcharts4/core.js", true);
        $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
        $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
        $this->core_layout->addJs("js/eforms/shipping/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/shipping/index');
        $this->load->view('core/templates/footer');
    }

    public function masterfile() {
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        
        $this->core_layout->setPageTitle("Shipping Advice - Masterfile");
        $this->core_layout->setPrivilegeName("ship_masterfile");
        $this->core_layout->addJs("js/eforms/shipping/masterfile_shipping.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/shipping/masterfile_shipping');
        $this->load->view('core/templates/footer');
    }

    function get_datatable_request(){
        $data = $this->shipping->getDatatableRequest();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function most_shippingto_details(){
        $data =  $this->shipping->mostShippingtoDetails();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function most_shippingloc_details(){
        $data =  $this->shipping->mostShippinglocDetails();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function most_shippingit_details(){
        $data =  $this->shipping->mostShippingitDetails();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function ave_shippingto_details(){
        $data =  $this->shipping->aveShippingtoDetails();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function new_shipping(){
        $this->core_layout->setPageTitle("Shipping Advice - New Shipping");
        $this->core_layout->setPrivilegeName("ship_masterfile");
        $this->core_layout->addJs("js/eforms/shipping/new_shipping.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('eforms/shipping/new_shipping');
        $this->load->view('core/templates/footer');
    }

    function get_file_under(){
        $data = $this->shipping->getFileUnder();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    
    function get_department(){
        $data = $this->shipping->getDepartment();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_requested_by(){
        $data = $this->shipping->getRequestedBy();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_ship_to_detail(){
        $this->input->get();
        $data =  $this->shipping->getShipToDetail();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_location(){
        $data = $this->shipping->getLocation();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function service_vehicle(){
        $data = $this->shipping->serviceVehicle();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function driver(){
        $data = $this->shipping->driver();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_shipping_content(){
        $data = $this->shipping->getShippingContent();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function item_lookup(){
        $data = $this->shipping->itemLookup();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    
    function clear_contents(){
        //$id = $this->input->get();
        $data =  $this->shipping->clearContents();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
        
    }

    function item_lookup_details(){
        $id = $this->input->get();
        $data =  $this->shipping->itemLookupDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
        
    }

    function asset_lookup_details(){
        $id = $this->input->get();
        $data =  $this->shipping->assetLookupDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
        
    }

    function asset_lookup(){
        $data = $this->shipping->assetLookup();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_item_modal(){
        $data = $this->shipping->addItemModal();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_asset_modal(){
        $data = $this->shipping->addAssetModal();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_content_body(){
        $data = $this->shipping->save_shipping();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    function get_daily(){
        $data = $this->shipping->getDaily();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    function get_weekly(){
        $data = $this->shipping->getWeekly();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    function view_shipping(){
        $this->core_layout->setPageTitle("Shipping Advice - View Shipping");
        $this->core_layout->setPrivilegeName("ship_masterfile");
        $this->core_layout->addJs("js/eforms/shipping/view_shipping.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('eforms/shipping/view_shipping');
        $this->load->view('core/templates/footer');
    }

    function shipping_content_table($id){
        $data = $this->shipping->viewItemContent($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function view_shipping_details($id){
        $data =  $this->shipping->viewShippingDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
 
    function approve_shipping($id){
        $data =  $this->shipping->approveShipping($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function disapprove_shipping($id){
        $data =  $this->shipping->disapproveShipping($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function receive_shipping($id){
        $data =  $this->shipping->receiveShipping($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_approve_shipping($id){
        $data =  $this->shipping->undoApproveShipping($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_disapprove_shipping($id){
        $data =  $this->shipping->undoDisapproveShipping($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_receive_shipping($id){
        $data =  $this->shipping->undoReceiveShipping($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function undo_cancel_shipping($id){
        $data =  $this->shipping->undoCancelShipping($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function cancel_shipping($id){
        $data =  $this->shipping->cancelShipping($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_shipping(){
        $this->core_layout->setPageTitle("Shipping Advice - Edit Shipping");
        $this->core_layout->setPrivilegeName("ship_masterfile");
        $this->core_layout->addJs("js/eforms/shipping/edit_shipping.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('eforms/shipping/edit_shipping');
        $this->load->view('core/templates/footer');
    }

    function edit_shipping_details($id){
        $data =  $this->shipping->editShippingDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_shipping($id){
        $data =  $this->shipping->updateShipping($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function print_shipping(){
        $this->core_layout->setPrivilegeName("ship_masterfile");
        $this->core_layout->addJs("js/eforms/shipping/print_shipping.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('eforms/shipping/print_shipping');
        $this->load->view('core/templates/footer');
    }

    function print_shipping_details(){
        $id=$this->input->get();
        $data =  $this->shipping->printShippingDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_item($id){
        $data =  $this->shipping->editItemDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function update_item($id){
        $data =  $this->shipping->updateItemDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_item_modal($id){
        $data =  $this->shipping->updateItem($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_edit_item($id){
        $data =  $this->shipping->updateEditItem($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_content($id){
        $data =  $this->shipping->deleteContent($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    
    function edit_itemModal($id){
        $data =  $this->shipping->updateItemModal($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_assetModal($id){
        $data =  $this->shipping->updateAssetModal($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_editContent($id){
        $data =  $this->shipping->deleteEditContent($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function clear_editContent($id){
        $data =  $this->shipping->clearEditContents($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function archive_shipping(){
        $this->core_layout->setPageTitle("Shipping Advice - Archive");
        $this->core_layout->setPrivilegeName("ship_archive");
        $this->core_layout->addJs("js/eforms/shipping/archive_shipping.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('eforms/shipping/archive_shipping');
        $this->load->view('core/templates/footer');
    }

    function archive_datatable_request(){
        $data = $this->shipping->archiveDatatableRequest();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    public function get_shipping_analytics_for_dashboard() {
        $data = $this->shipping->m_get_shipping_analytics_for_dashboard();
        echo json_encode($data);
    }

    function add_new_item(){
        $data = $this->shipping->addNewItem();
        echo json_encode($data);
    }

    function uom_lookup(){
        $data = $this->shipping->uomLookup();
        echo json_encode($data);
    }

    function add_new_uom(){
        $data = $this->shipping->addNewUom();
        echo json_encode($data);
    }

    function mass_action_shipping(){
        $data = $this->shipping->massActionShipping();
        echo json_encode($data);
    }

    public function add_telegram_config(){
        $data = $this->shipping->addTelegramConfig();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    public function load_telegram_config(){
        $data = $this->shipping->loadTelegramConfig();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function export_event_log($export){
        $data = $this->shipping->exportData($export);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function export_event_log_archived($export){
        $data = $this->shipping->exportDataArchived($export);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }


}
