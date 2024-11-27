<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Ticketing extends MY_Controller {
    public function __construct(){
		parent::__construct();
		$this->authenticate->setModuleAccess("ts");
        $this->authenticate->doRedirect();
        
		$this->core_layout->setBodyClass("ticketing");
		$this->core_layout->setBodyClass("ticketing");
        $this->core_layout->setPrivilegeName("ticketing");

        $this->load->model('Ticketing_m','ticketing');
		
        date_default_timezone_set('Asia/Manila');
    }

    function index(){
        $this->core_layout->setPrivilegeName("ticketing_transaction");
        $this->core_layout->addJs("js/ts/amschart/amschart.js", true);
        $this->core_layout->addJs("js/ts/amschart/amschart_theme.js", true);
        $this->core_layout->addJs("js/ts/amschart/amschart_chart.js", true);
        $this->core_layout->addJs("js/ts/index.js", true);
        $this->load->view("core/templates/header");
        $this->load->view("ts/index");
        $this->load->view("core/templates/footer");
    }

    function pending_tickets(){
        $data =  $this->ticketing->PendingTickets();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function overdue_tickets(){
        $data =  $this->ticketing->OverdueTickets();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function closed_tickets(){
        $data =  $this->ticketing->ClosedTickets();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function all_tickets(){
        $data =  $this->ticketing->AllTickets();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function chart_data(){
        $data =  $this->ticketing->chartData();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_recent_tickets(){
        $data =  $this->ticketing->getRecentTickets();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function masterfile(){
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        $this->core_layout->setPrivilegeName("ticketing_transaction");
        $this->core_layout->addJs("js/ts/tickets/masterfile.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ts/tickets/masterfile');
        $this->load->view('core/templates/footer');
    }

    function get_masterfile(){
        $data =  $this->ticketing->getMasterfile();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function tickets(){
        $this->core_layout->setPrivilegeName("ticketing_transaction");
        $this->core_layout->addJs("js/ts/tickets/tickets.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ts/tickets/tickets');
        $this->load->view('core/templates/footer');
    }

    function ticket_masterfile($id){
        $data =  $this->ticketing->ticketMasterfile($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_ticket($id){
        $data =  $this->ticketing->deleteTicket($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function new_ticket(){
        $this->core_layout->setPrivilegeName("ticketing_transaction");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        
        $this->core_layout->addJs("js/ts/tickets/new_ticket.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ts/tickets/new_ticket');
        $this->load->view('core/templates/footer');
    }

    function department(){
        $data =  $this->ticketing->Department();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_ticket(){
        $data =  $this->ticketing->saveTicket();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function upload_ticket_photo(){
        $data = $this->ticketing->uploadTicketPhoto();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_ticket(){
        $this->core_layout->setPrivilegeName("ticketing_transaction");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        
        $this->core_layout->addJs("js/ts/tickets/edit_ticket.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ts/tickets/edit_ticket');
        $this->load->view('core/templates/footer');
    }

    function get_user_emp_data(){
        $data = $this->ticketing->getUserEmpData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function edit_ticket_details($id){
        $data =  $this->ticketing->editTicketDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_ticket($id){
        $data =  $this->ticketing->updateTicket($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function service(){
        $this->core_layout->setPrivilegeName("ticketing_transaction");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
         
        $this->core_layout->addJs("js/ts/tickets/service.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ts/tickets/service');
        $this->load->view('core/templates/footer');
    }

    function service_details($id){
        $data =  $this->ticketing->serviceDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function performed(){
        $data =  $this->ticketing->Performed();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_requested_by(){
        $data =  $this->ticketing->getRequestedBy();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_service($id){
        $data =  $this->ticketing->updateService($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    
    function confirm(){
        $this->core_layout->setPrivilegeName("ticketing_transaction");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
         
        $this->core_layout->addJs("js/ts/tickets/confirm.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ts/tickets/confirm');
        $this->load->view('core/templates/footer');
    }

    function confirm_details($id){
        $data =  $this->ticketing->confirmDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
   
    function update_confirm($id){
        $data =  $this->ticketing->updateConfirm($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function reopen_ticket($id){
        $data =  $this->ticketing->reopenTicket($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function archived(){
        $this->core_layout->setPrivilegeName("ticketing_transaction");
        $this->core_layout->addJs("js/ts/tickets/archived.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ts/tickets/archived');
        $this->load->view('core/templates/footer');
    }

    function archived_list(){
        $data =  $this->ticketing->archivedList();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
       
    function view_ticket(){
        $this->core_layout->setPrivilegeName("ticketing_transaction");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        
        $this->core_layout->addJs("js/ts/tickets/view_ticket.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('ts/tickets/view_ticket');
        $this->load->view('core/templates/footer');
    }

    function get_department_collection(){
        $data = $this->ticketing->getDepartmentCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function remove_file(){
        $data = $this->ticketing->removeFile();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

}