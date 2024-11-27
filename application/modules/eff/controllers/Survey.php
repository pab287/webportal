<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Survey extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->authenticate->setModuleAccess("eff");
		$this->authenticate->doRedirect();

        $this->core_layout->setPrivilegeName("eff");
		$this->load->model("eff/survey_model");
		$this->core_layout->setPageTitle("Employee Feedback");
		$this->core_layout->setCrumbTitle("Employee Feedback");
	}
	public function index(){
		$this->core_layout->setPrivilegeName("employee_feedback_form");
		$this->core_layout->addJs("js/rater/rater.min.js");
		$this->core_layout->addCss("css/rater/font-awesome.min.css");
		
		$this->load->view('core/templates/header');
		$this->load->view('survey/index');
		$this->load->view('core/templates/footer');
	}
	public function items($id=null){
		$this->core_layout->setPrivilegeName("employee_feedback_form_survey_items");
		$this->load->view('core/templates/header');
		$this->load->view('survey/items', array("id"=>$id));
		$this->load->view('core/templates/footer');
	}
	public function summary(){
		$this->core_layout->setPrivilegeName("employee_feedback_form_survey_summary");
		$this->load->view('core/templates/header');
		$this->load->view('survey/summary');
		$this->load->view('core/templates/footer');
	}
	function add_survey_data(){
		$data = $this->survey_model->addSurveyData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_survey_list(){
		$data = $this->survey_model->getSurveyList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_department_list(){
		$data = $this->survey_model->getdepartmentList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
}