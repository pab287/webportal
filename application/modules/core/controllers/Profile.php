<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Profile extends MY_Controller {
	function __construct(){
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

	}
	function index(){
		$session = $this->session->userdata();
		$employee_id = $session["logged_in"]["emp_id"];
		
	   if (empty($employee_id)) {
			redirect(base_url(), "refresh");
			die();
	   }

		$this->core_layout->setPageTitle("HRIS - View Employee Masterfile");
		$this->core_layout->setBodyClass("hris view-employee_masterfile");
		$this->core_layout->setPrivilegeName("hris_employee_masterfile");
		$this->core_layout->setCrumbTitle("Profile");
		$this->core_layout->setHeaderTitle("Employee Data");

		$userProfile = $this->authenticate->getUserProfile();

		$this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
		$this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
		$this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
		$this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");

		$this->core_layout->addJs("js/hris/employee_view_script.js" ,true);
		// $this->core_layout->addCss("css/responsiveTable.css", true);
		$this->core_layout->addCss("css/hris/view_employee_masterfile.css", true);
	   
		$this->core_layout->addJs("plugins/star-rating/js/jquery.star-rating-svg.min.js", TRUE);
		$this->core_layout->addCss("plugins/star-rating/css/star-rating-svg.css", TRUE);

		$this->core_layout->addJs('js/hris/search_employee_script.js', TRUE);
		$this->core_layout->addCss('css/hris/index.css', TRUE);

		$this->core_layout->addCss('css/hris/view_profile.css', TRUE);

		$data = $this->utilities->parseFormDataToObject(array("data" => $this->employee_model->getEmployeeDataSheetDetails($employee_id), 
		"profile_payroll_sheet"=>true, "payroll_sheet_data"=>$this->get_employe_payroll_data($employee_id), "payroll_sheet_max_id"=>$this->get_max_employe_payroll_data($employee_id)));

		$this->load->view("core/templates/header");
		$this->load->view("hris/masterfile/employee/view_employee_masterfile", $data, FALSE);
		$this->load->view("core/templates/footer");
	}

	function get_employee_rating_remarks($id) {
		$data = $this->core_layout->getPerformanceRating($id);
		echo json_encode($data);
	}

	private function get_employe_payroll_data($id=null){
		$arrData = array();
		if($id){
			$this->db->select("id, pay_date, date_start, date_end, gross_pay, net_pay, bonus_code, is_bonus");
			$this->db->order_by("pay_date", "DESC");
			$payrollData = $this->db->get_where("payroll.payroll_sheet", array("emp_id"=>$id, "posted"=>1));
			$arrData = $payrollData->result_array();
		}

		return $arrData;
	}

	private function get_max_employe_payroll_data($id=null){
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
}