<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Settings extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->authenticate->doRedirect();
		$this->load->model("settings_model", "settings");
		$this->load->model("datatable_model","dt_model");
		
		$this->core_layout->setPageTitle("Configuration - Settings");
		$this->core_layout->setBodyClass("configuration settings");
		$this->core_layout->setPrivilegeName("settings");
	}
	
	function index(){
		$data = array();
		$data["items"] = $this->settings->getSettingsData();
		
		$this->load->view('core/templates/header');
		$this->load->view('core/settings/index', $data);
		$this->load->view('core/templates/footer');
	}
	
	function update_settings(){
		$data = $this->settings->updateSettings();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	function test_send_email(){
		$module = "mrs_pr_new_resend";
		$email_title = "test sending email";
		$content_title = "test content title";
		$content = "test content message";

		$overrideMailer = array();
		$overrideMailer["email_user"] = "mprocurement01@gmail.com";
		$overrideMailer["email_pass"] = "user#!234";
		$overrideMailer["send_cc"] = array("june1987paul@gmail.com");

		/*** $tempConfig = array();
		$tempConfig["protocol"] = "smtp";
		$tempConfig["smtp_host"] = "smtp.googlemail.com";
		$tempConfig["smtp_port"] = 587;

		$overrideMailer["config"] = $tempConfig; ***/
		
		$sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
		if ($sent) {
			echo "sent";
		}
		else{
			echo "failed!";
			print($this->email->print_debugger());
		}
	}

	public function test_config(){
		$serverName = $_SERVER['SERVER_NAME'];
        $serverName = strtolower($serverName);
		$siteCode = $this->config->item('site_unique_code');
		var_dump($siteCode);
		var_dump($serverName);
	}
}