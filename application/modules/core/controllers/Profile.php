<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Profile extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->authenticate->doRedirect();
		$this->core_layout->setBodyClass("dashboard");

		$this->load->model("hris/employee_model");
		$this->load->model("hris/company_model");
		$this->load->model("hris/department_model");
		$this->load->model("hris/position_model");
		$this->load->model("hris/salary_model");
		$this->load->model("hris/contractor_model");
		$this->load->model("hris/personnel_model");
		$this->load->model("ams/Utilities_model", "utilities");
		$this->load->model("core/Profile_model", "profile");
	}

	public function index(){
		$session = $this->session->userdata();
		$employee_id = $session["logged_in"]["emp_id"];
		
	   if (empty($employee_id)) { redirect(base_url(), "refresh"); die(); }
		$this->core_layout->setPageTitle("Profile - Employee Data");
		$this->core_layout->setBodyClass("profile view-employee_data");
		$this->core_layout->setPrivilegeName("core_profile_employee_data");
		$this->core_layout->setCrumbTitle("Profile");
		$this->core_layout->setHeaderTitle("Employee Data");

		$this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
		$this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
		$this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
		$this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");

		$this->core_layout->addCss("css/hris/view_employee_masterfile.css", true);
		$this->core_layout->addCss("plugins/star-rating/css/star-rating-svg.css", true);
		$this->core_layout->addCss('css/hris/index.css', true);
		$this->core_layout->addCss('css/hris/view_profile.css', true);
		$this->core_layout->addJs("plugins/star-rating/js/jquery.star-rating-svg.min.js", true);

		$currentActions = $this->core_layout->getCurrentActions();
		$showPayrollPayslip = is_array($currentActions) && count($currentActions) > 0 && in_array("view_own_request", $currentActions);

	   $data = $this->utilities->parseFormDataToObject(array("data" => $this->employee_model->getEmployeeDataDetails($employee_id),
	   "profile_payroll_sheet"=>true,
	   "payroll_sheet_data"=>$this->get_employee_payroll_data($employee_id),
	   "payroll_sheet_max_id"=>$this->get_max_employee_payroll_data($employee_id),
	   "show_payroll_payslip"=>$showPayrollPayslip));
	   	$data->tab ='personalInfo';

		$this->core_layout->addJs("js/hris/profile_view_script.js",true, $data);

		$this->load->view("core/templates/header");
		$this->load->view("hris/masterfile/employee/view_employee_masterfile", $data, false);
		$this->load->view("core/templates/footer");
	}

	public function get_employee_rating_remarks($id) {
		$data = $this->core_layout->getPerformanceRating($id);
		echo json_encode($data);
	}

	protected function get_employee_payroll_data($id=null){
		$arrData = array();
		if($id){
			$this->db->select("id, pay_date, date_start, date_end, gross_pay, net_pay, bonus_code, is_bonus");
			$this->db->order_by("pay_date", "DESC");
			$payrollData = $this->db->get_where("payroll.payroll_sheet", array("emp_id"=>$id, "posted"=>1));
			$arrData = $payrollData->result_array();
		}

		return $arrData;
	}

	protected function get_max_employee_payroll_data($id=null){
		$maxId = 0;
		if($id){
			$this->db->select("MAX(id) max_id");
			$this->db->order_by("pay_date", "DESC");
			$payrollData = $this->db->get_where("payroll.payroll_sheet", array("emp_id"=>$id, "posted"=>1));
			$maxId = $payrollData->row()->max_id;
		}

		return $maxId;
	}

	public function get_payroll_sheet_data(){
		$id = $this->input->get("id");
		$this->load->model("payroll/payroll_m");
		$resultset = $this->payroll_m->getCurrentPayrollPayslip($id);
		echo json_encode($resultset);
	}
	
	public function get_additional_info($id){
		$data = $this->profile->getAddtionalInfo($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_education_background($id){
		$data = $this->profile->getEducationBackground($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_license_and_cert($id){
		$data = $this->profile->getLicenseAndCerts($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_work_experience($id){
		$data = $this->profile->getEmpWorkExperience($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_awards_and_achievements($id){
		$data = $this->profile->getAwardsAndAchievements($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_emp_skills($id){
		$data = $this->profile->getEmpSkills($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_orgs($id){
		$data = $this->profile->getOrgs($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_trainings_and_seminars($id){
		$data = $this->profile->getTrainingsAndSeminars($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_personal_references($id){
		$data = $this->profile->getPersonalReferences($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_medical_history($id){
		$data = $this->profile->getEmpMedicalHistory($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_legal_history($id){
		$data = $this->profile->getLegalHistory($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_accountability($id){
		$data = $this->profile->getAccountability($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_employment_information($id){
		$data = $this->profile->getEmploymentInformation($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}

	public function get_job_description($id){
		$data = $this->profile->getEmpJobDescription($id);
		$this->output->set_content_type('json')->set_output(json_encode($data));
	}


}
