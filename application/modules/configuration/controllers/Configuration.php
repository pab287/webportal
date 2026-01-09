<?php defined('BASEPATH') || exit('No direct script access allowed');
class Configuration extends MY_Controller {
	public function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("configuration");
        $this->authenticate->doRedirect();

        $this->core_layout->setPrivilegeName("configuration");
        $this->load->model("Email_Configuration","email_config");
        $this->load->model("Sms_Configuration","sms_config");
        $this->load->model("Database_backup","database_backup");
        $this->load->model("Logs_model","logs");
        $this->load->model("Telegram_bot_config", "telegram");
	}
    
    public function index(){
		$this->load->view('core/templates/header');
        $this->load->view('configuration/index');
        $this->load->view('core/templates/footer');
    }

    public function email_template(){
        $this->core_layout->setPrivilegeName("cfg_email_template");
        $this->core_layout->addJs("js/configuration/email/email_template.js", true);
        
		$this->load->view('core/templates/header');
        $this->load->view('configuration/email/email_template');
        $this->load->view('core/templates/footer');
    }
    
    public function email_protocol(){
        $this->core_layout->setPrivilegeName("cfg_email_protocol");
        $this->core_layout->addJs("js/configuration/email/protocol.script.js", true);

		$this->load->view('core/templates/header');
        $this->load->view('configuration/email/email_protocol');
        $this->load->view('core/templates/footer');
    }

    public function sms_protocol(){
        $this->core_layout->setPrivilegeName("cfg_sms_protocol");
        $tempData["department"] = $this->sms_config->select2DepartmentData();
        $this->core_layout->addJs("js/configuration/sms/sms_protocol.js", true, $tempData);
		$this->load->view('core/templates/header');
        $this->load->view('configuration/sms/sms_protocol');
        $this->load->view('core/templates/footer');
    }

    public function telegram_protocol(){
        $this->core_layout->setPrivilegeName("cfg_telegram_protocol");
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
        $data['owner'] = $this->telegram->select2OwnerData();
        $data['module'] = $this->telegram->select2ModuleData();
        $this->core_layout->addJs("js/configuration/telegram/telegram_protocol.js", true,$data);
		$this->load->view('core/templates/header');
        $this->load->view('configuration/telegram/telegram_protocol');
        $this->load->view('core/templates/footer');
    }

    public function telegram_protocol_archive(){
        $this->core_layout->setPrivilegeName("cfg_telegram_protocol");
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
        $this->core_layout->addJs("js/configuration/telegram/telegram_protocol.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('configuration/telegram/telegram_bot_archive');
        $this->load->view('core/templates/footer');
    }

    public function manual(){
        $this->core_layout->addJs("js/configuration/database/manual.js", true);
		$this->load->view('core/templates/header');
        $this->load->view('configuration/database/manual');
        $this->load->view('core/templates/footer');
    }

    public function scheduled(){
        $this->core_layout->addJs("js/configuration/database/scheduled.js", true);

		$this->load->view('core/templates/header');
        $this->load->view('configuration/database/scheduled');
        $this->load->view('core/templates/footer');
    }

    public function logs(){
        $this->core_layout->addJs("js/configuration/logs/index.js", true);

		$this->load->view('core/templates/header');
        $this->load->view('configuration/logs/index');
        $this->load->view('core/templates/footer');
    }

    function sms_protocol_datatable_request(){
        $data = $this->sms_config->smsProtocolDatatableRequest();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function email_protocol_datatable_request(){
        $data = $this->email_config->emailProtocolDatatableRequest();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function email_template_list(){
        $data = $this->email_config->emailTemplateList();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function email_lookup(){
        $data = $this->email_config->emailLookup();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_email_protocol_settings(){
        $data = $this->email_config->setEmailProtocolSettings();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function set_sms_protocol_settings(){
        $data = $this->sms_config->setSmsProtocolSettings();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_email_protocol_settings(){
        $data = $this->email_config->updateEmailProtocolSettings();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_sms_protocol_settings(){
        $data = $this->sms_config->updateSmsProtocolSettings();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function remove_email_protocol_settings(){
        $data = $this->email_config->removeEmailProtocolSettings();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function remove_sms_protocol_settings(){
        $data = $this->sms_config->removeSmsProtocolSettings();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_email_protocol_by_id($id=null){
        $data = $this->email_config->getEmailProtocolById($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_sms_protocol_by_id($id=null){
        $data = $this->sms_config->getSmsProtocolById($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_email(){
        $data = $this->email_config->saveEmail();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_details($id){
        $data = $this->email_config->editDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_email($id){
        $data = $this->email_config->editEmail($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_email($id){
        $data = $this->email_config->deleteEmail($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function send_email($id){
        $data = $this->email_config->sendEmail($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function database_backup(){
        $data = $this->database_backup->databaseBackup();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function database_lookup(){
        $data = $this->database_backup->databaseLookup();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_manual(){
        $data = $this->database_backup->saveManual();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function database_details($id){
        $data = $this->database_backup->databaseDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_file($id){
        $data = $this->database_backup->deleteFile($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_module_collection(){
        $data = $this->logs->getModuleCollection();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_event_logs(){
        $data = $this->logs->getEventLogs();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function sms_protocol_connect(){
        $data = $this->sms_config->smsProtocolConnect();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function sms_protocol_exclude(){
        $data = $this->sms_config->smsProtocolExclude();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function sms_fetch_department(){
        $data = $this->sms_config->smsFetchDepartment();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function telegram_protocol_datatable_request(){
        $data = $this->telegram->getTelegramDatatableRequest();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function set_telegram_protocol_settings(){
        $data = $this->telegram->setTelegramProtocolSettings();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function update_telegram_protocol_settings(){
        $data = $this->telegram->updateTelegramProtocolSettings();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function get_telegram_bot_by_id($id=null){
        $data = $this->telegram->getTelegramBotById($id);
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function delete_telegram_bot($id){
        $data = $this->telegram->archiveTelegramBot($id);
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function toggle_telegram_bot_status($id){
        $data = $this->telegram->toggleTelegramStatus($id);
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function restore_telegram_bot($id){
        $data = $this->telegram->restoreTelegramBot($id);
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function test_telegram_protocol(){
        $data = $this->telegram->testTelegramProtocol();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

}