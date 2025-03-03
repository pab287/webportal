<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Ticket extends MY_Controller {
    public function __construct(){
		parent::__construct();
		$this->authenticate->setModuleAccess("ticket");
        $this->authenticate->doRedirect();
        
		$this->core_layout->setBodyClass("ticket");
		$this->core_layout->setBodyClass("ticket");
        $this->core_layout->setPrivilegeName("ticket_masterfile");

        $this->load->model('Ticket_m','ticket');
		
        date_default_timezone_set('Asia/Manila');
    }

    function dashboard(){
        $this->core_layout->setPrivilegeName("ticket_dashboard");
        $this->core_layout->setPageTitle("TICKET - Dashboard");
        $this->core_layout->addJs("js/ts/amschart/amschart.js", true);
        $this->core_layout->addJs("js/ts/amschart/amschart_theme.js", true);
        $this->core_layout->addJs("js/ts/amschart/amschart_chart.js", true);
        $this->core_layout->addJs("js/ticket/index.js", true);
        $this->load->view("core/templates/header");
        $this->load->view("ticket/dashboard");
        $this->load->view("core/templates/footer");
    }

    function tickets(){
        $this->core_layout->setPrivilegeName("ticket_masterfile");
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        $this->core_layout->setPageTitle("TICKET - Masterfile");
        $this->core_layout->addJs("js/ticket/ticket.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ticket/tickets');
        $this->load->view('core/templates/footer');
    }

    function archive(){
        $this->core_layout->setPrivilegeName("ticket_masterfile");
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        $this->core_layout->setPageTitle("TICKET - Archives Masterfile");
        $this->core_layout->addJs("js/ticket/archive_ticket.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ticket/archive');
        $this->load->view('core/templates/footer');
    }

    function settings(){
        $this->core_layout->setPrivilegeName("ticket_settings");
        $this->core_layout->setPageTitle("TICKET - Settings");
        $this->core_layout->addJs("js/ticket/category.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ticket/category');
        $this->load->view('core/templates/footer');
    }

    function index(){
        $this->core_layout->setPrivilegeName("ticket");
        $this->core_layout->setPageTitle("TICKET - Add Ticket");
        $tempData["department"] = $this->ticket->select2DepartmentData();
        $tempData["category"] = $this->ticket->select2CategoryData('category');
        $tempData["subcategory"] = $this->ticket->select2CategoryData('sub-category');
        $tempData["responsibility"] = $this->ticket->select2CategoryData('responsibility');
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addJs("js/ticket/new_ticket.js", true,$tempData);
		$this->load->view('core/templates/header');
        $this->load->view('ticket/new_ticket');
        $this->load->view('core/templates/footer');
    }

    function edit_ticket(){
        $this->core_layout->setPrivilegeName("ticket_transaction");
        $this->core_layout->setPageTitle("TICKET - Edit Ticket");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
        $tempData["department"] = $this->ticket->select2DepartmentData();
        $tempData["category"] = $this->ticket->select2CategoryData('category');
        $tempData["subcategory"] = $this->ticket->select2CategoryData('sub-category');
        $tempData["status"] = $this->ticket->select2CategoryData('status');
        $tempData["severity"] = $this->ticket->select2CategoryData('severity');
        $tempData["responsibility"] = $this->ticket->select2CategoryData('responsibility');
        $tempData["performed_by"] = $this->ticket->select2PerformedByData();
        $this->core_layout->addJs("js/ticket/edit_ticket.js", true,$tempData);
		$this->load->view('core/templates/header');
        $this->load->view('ticket/edit_ticket');
        $this->load->view('core/templates/footer');
    }

    function view_ticket(){
        $this->core_layout->setPrivilegeName("ticket_transaction");
        $this->core_layout->setPageTitle("TICKET - View Ticket");
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
        $this->core_layout->addJs("js/ticket/view_ticket.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ticket/view_ticket');
        $this->load->view('core/templates/footer');
    }

    function ticket_details($id){
        $data =  $this->ticket->ticketDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function ticket_masterfile(){
        $data =  $this->ticket->ticketMasterfile();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function ticket_archive_masterfile(){
        $data =  $this->ticket->ticketArchiveMasterfile();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    function category_masterfile(){
        $data =  $this->ticket->categoryMasterfile();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_category(){
        $data = $this->ticket->addCategory();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_category_modal_content($content = "add") {
        $data = $this->ticket->getCategoryModalContent($content);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_modal_category() {
        $data = $this->ticket->updateModalCategory();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function edit_ticket_details($id){
        $data =  $this->ticket->editTicketDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }


    function get_category_collection($type){
        $data = $this->ticket->getCategories($type);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_department_collection(){
        $data = $this->ticket->getDepartments();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function upload_ticket_photo(){
        $data = $this->ticket->uploadTicketPhoto();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_ticket(){
        $data = $this->ticket->saveTicket();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_comments($ticket_id){
        $data = $this->ticket->getComments($ticket_id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_comment(){
        $data = $this->ticket->addComment();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_performed_by(){
        $data = $this->ticket->getPerformedByCollection();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_ticket($id){
        $data =  $this->ticket->updateTicket($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function remove_file(){
        $data = $this->ticket->removeFile();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function delete_ticket($id){
        $data = $this->ticket->deleteTicket($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function restore_ticket($id){
        $data = $this->ticket->restoreTicket($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function restore_archived_ticket_multiple(){
        $data =  $this->ticket->ticket_restore_multiple();
        // var_dump($data);
        echo json_encode($data);
    }

    function chart_data(){
        $data = $this->ticket->chartData();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function all_tickets(){
        $data = $this->ticket->allTickets();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function all_status(){
        $data = $this->ticket->allStatus();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function all_category(){
        $data = $this->ticket->allCategory();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function all_sub_category(){
        $data = $this->ticket->allSubcategory();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_category($id){
        $data = $this->ticket->deleteCategory($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function restore_category($id){
        $data = $this->ticket->restoreCategory($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function test_send(){
        $data = array(
            'ref_yr' => '2023',
            'ref_series' => 'TEST',
            'ref_month' => '08',
            'category' => 'Hardware',
            'sub_category' => '',
            'reference_no' => 'TS23-08-0038',
            'department_id' => '9',
            'message' => 'test only',
            'requestor' => 1762,
            'requested_date' => '2023-08-16',
            'priority' => 'low',
            'status' => 'open',
            'created_at' => '2023-08-16 09:00:00'
        );

        $data = $this->ticket->sendTelegram($data, 455);
        var_dump($data);
    }

    function remove_actionstkn(){
        $data = $this->ticket->removeActionstkn();
        $this->output->set_content_type('json')->set_output(json_encode($data));
      }
    
}