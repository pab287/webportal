<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Sms extends MY_Controller {
    
	public function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("sms");
        $this->authenticate->doRedirect();

        $this->core_layout->setPrivilegeName("sms");
        $this->load->model("sms/contacts_model","contacts");
        $this->load->model("sms/event_logs_model","event_logs");
        $this->load->model("sms/groups_model","groups");
        $this->load->model("sms/templates_model","templates");
        $this->load->model("services/bulk_model","bulk");
        $this->load->model("services/corporate_model","corporate");
        $this->load->model("services/client_model","client");
        $this->load->model("services/gateway_model","gateway");
	}
    
    public function index(){
        $this->core_layout->setPrivilegeName("bulk");
        //$this->core_layout->addJs("js/ams/vehicle_masterfile.js", true);

		$this->load->view('core/templates/header');
        $this->load->view('sms/index');
        $this->load->view('core/templates/footer');
    }

    public function bulk(){
        $this->core_layout->setPrivilegeName("sms_dashboard");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addJs("js/sms/services/bulk.js", true);

		$this->load->view('core/templates/header');
        $this->load->view('sms/services/bulk');
        $this->load->view('core/templates/footer');
    }

    public function sms_test(){
        $this->core_layout->setPrivilegeName("sms_testing");
        $this->core_layout->addJs("js/sms/test.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('sms/sms_test');
        $this->load->view('core/templates/footer');
    }

    function bulk_outbox(){
        $data = $this->bulk->bulkOutbox();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function upload_recipients(){
        $data = $this->bulk->uploadRecipients();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function import_uploads(){
        $data = $this->bulk->importUploads();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function bulk_recipients(){
        $data = $this->bulk->bulkRecipients();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_bulk_recipient($id){
        $data = $this->bulk->deleteRecipient($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function remove_bulk_all(){
        $data = $this->bulk->removeAll();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function bulk_send_msg(){
        $data = $this->bulk->bulkSendMsgV2();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function corporate(){
        $this->core_layout->setPrivilegeName("corporate");
        $this->core_layout->addJs("js/sms/services/corporate.js", true);
 
         $this->load->view('core/templates/header');
         $this->load->view('sms/services/corporate');
         $this->load->view('core/templates/footer');
    }

    function corporate_outbox(){
        $data = $this->corporate->corporateOutbox();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function group(){
        $data = $this->corporate->Group();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function contact(){
        $data = $this->corporate->Contact();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
  
    function template(){
        $data = $this->corporate->Template();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_template_message($id){
        $data = $this->corporate->addTemplateMessage($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_group_table($id){
        $data = $this->corporate->addGroupTable($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_contact_table(){
        $id = $this->input->get();
        $data = $this->corporate->addContactTable($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_number_table($id){
        $data = $this->corporate->addNumberTable($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_all_table(){
        $id = $this->input->get();
        $data = $this->corporate->addAllTable();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function corporate_recipients(){
        $data = $this->corporate->corporateRecipients();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_recipient($id){
        $data = $this->corporate->deleteRecipient($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function remove_all(){
        $data = $this->corporate->removeAll();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function corporate_send_msg(){
        $data = $this->corporate->corporateSendMsgV2();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function corporate_resend($id){
        $data = $this->corporate->corporateResend($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function client(){
        $this->core_layout->setPrivilegeName("client");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addJs("js/sms/services/client.js", true);
 
         $this->load->view('core/templates/header');
         $this->load->view('sms/services/client');
         $this->load->view('core/templates/footer');
    }

    function client_outbox(){
        $data = $this->client->clientOutbox();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function client_recipients(){
        $data = $this->client->clientRecipients();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_client_recipient($id){
        $data = $this->client->deleteRecipient($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function remove_client_all(){
        $data = $this->client->removeAll();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function upload_recipients_client(){
        $data = $this->client->uploadRecipients();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function import_uploads_client(){
        $data = $this->client->importUploads();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function client_send_msg(){
        $data = $this->client->clientSendMsgV2();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function client_resend($id){
        $data = $this->client->clientResend($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function contacts(){
        $this->core_layout->setPrivilegeName("sms_masterfile_contacts");
        $this->core_layout->addJs("js/sms/contacts.js", true);
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
 
         $this->load->view('core/templates/header');
         $this->load->view('sms/contacts');
         $this->load->view('core/templates/footer');
     }

    function get_contacts_collection(){
        $data = $this->contacts->getContactsCollection();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function import_uploads_contacts(){
        $data = $this->contacts->importUploadsContacts();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function upload_recipients_contacts(){
        $data = $this->contacts->uploadRecipientsContacts();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function groups(){
        $this->core_layout->setPrivilegeName("sms_masterfile_groups");
        $this->core_layout->addJs("js/sms/groups.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('sms/groups');
        $this->load->view('core/templates/footer');
    }

    public function event_logs(){
        $this->core_layout->setPrivilegeName("sms_event_logs");
        $this->core_layout->addJs("js/sms/event_logs.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('sms/event_logs');
        $this->load->view('core/templates/footer');
    }

    function get_event_logs(){
        $data = $this->event_logs->getEventLogs();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_groups_collection(){
        $data = $this->groups->getGroups();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_contact_select(){
        $data = $this->groups->getContactCollection();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_groups_contact($id){
        $data = $this->groups->getGroupContact($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function add_group()
    {   
        $data = array(
        'group_name' => $this->input->post('group_name'), 
        );
        $insert = $this->groups->save_group($data);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($insert));
    }

    public function edit_group($id)
    {
        $data = $this->groups->edit_group($id);
        echo json_encode($data);
    }

     public function update_group()
     {   
        $data = array(
        'group_name' => $this->input->post('group_name'),
        );
        $update = $this->groups->update_group(array('id' => $this->input->post('id')), $data);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($update));
     }

     public function delete_group($id)
     {
        $del = $this->groups->delete_group($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($del));
     }

     public function delete_group_contact($id)
     {
        $data = array(
            'id' => $id, 
            'name' => $this->input->post('name'),
            'group_name' => $this->input->post('group_name'),
            );
        $del = $this->groups->delete_group_contact($data);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($del));
     }

     public function save_group_contact($id)
     {    
        $data = array(
        'group_id' => $id, 
        'contact_id' => $this->input->post('contact'),
        );
        $insert = $this->groups->save_group_contact($data);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($insert));
     }

     public function add_contact()
     {   
        $data = array(
        'firstname' => $this->input->post('firstname'), 
        'lastname' => $this->input->post('lastname'),
        'category' => "EMPLOYEE",   
        'cp_no' => $this->input->post('cp_no')
        );
        $insert = $this->contacts->save_contact($data);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($insert));
     }

     public function edit_contact($id)
     {
         $data = $this->contacts->edit_contact($id);
         echo json_encode($data);
     }

     public function update_contact()
     {   
        $data = array(
        'firstname' => $this->input->post('firstname'), 
        'lastname' => $this->input->post('lastname'),
        'category' => "EMPLOYEE",   
        'cp_no' => $this->input->post('cp_no')
        );
        $update = $this->contacts->update_contact(array('id' => $this->input->post('id')), $data);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($update));
     }

     public function delete_contact($id)
     {
        $data = array(
            'id' => $id, 
            'name' => $this->input->post('name'),
            );
        $delete = $this->contacts->delete_contact($data);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($delete));
     }

     public function templates(){
        $this->core_layout->setPrivilegeName("sms_masterfile_templates");
        $this->core_layout->addJs("js/sms/templates.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('sms/templates');
        $this->load->view('core/templates/footer');
     }

     function get_templates_collection(){
        $data = $this->templates->getTemplatesCollection();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_template(){
        $data = $this->templates->addTemplate();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_template($id){
        $data = $this->templates->editTemplate($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_template($id){
        $data = $this->templates->updateTemplate($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_template($id){
        $data = $this->templates->deleteTemplate($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function test(){
        $data = $this->contacts->test();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function send_sms(){
        $data = $this->gateway->sendTwoFactorSms();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

}