<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Portal extends MY_Controller {
	private $moduleTable = "modules";
	function __construct(){
		parent::__construct();
		$this->authenticate->doRedirect();
		$this->load->model("portal_model");
	}

	public function page_not_found(){
		$this->output->set_status_header('404');
			
		$this->load->view("core/templates/header");
		$this->load->view("core/templates/nopage");
		$this->load->view("core/templates/footer");
	}

	public function page_forbidden($arrMessage = array()){
		$this->output->set_status_header('404');
			
		$this->load->view("core/templates/header");
		$this->load->view("core/templates/page_forbidden", $arrMessage);
		$this->load->view("core/templates/footer");
	}

	public function index(){
		$data = array();
		$this->core_layout->addJs("plugins/masonry/masonry.pkgd.min.js");

		$portalContent = $this->portal_model->getPortalModules();
		$this->core_layout->setPrivilegeName("core_profile_employee_data");
		$currentActions = $this->core_layout->getCurrentActions();
		$data["showPayrollPayslip"] = is_array($currentActions) && count($currentActions) > 0 && in_array("view_own_request", $currentActions);

		$data["portal_content"] = $portalContent;
		$this->load->view('portal/index', $data);
	}

	public function version_details(){
		$externalCKEditor = "https://cdn.ckeditor.com/ckeditor5/33.0.0/classic/ckeditor.js";
		$arrData = array();
		$arrData["script_attribute"] = array("async");
		$this->core_layout->addExternalJs($externalCKEditor, false, $arrData);
		$this->core_layout->addJs("js/portal/version_details_script.js", true);
		$portalContent = $this->portal_model->getPortalVersion();
		$data = array();
		$data['result'] = $portalContent;
		$this->load->view("core/templates/header");
	    $this->load->view('portal/version_details', $data);
		$this->load->view("core/templates/footer");
	}

	public function redirect_to_module(){
		$post = $this->input->post();
		$resultset = array();
		if(isset($post["id"]) && $post["id"]){
			$query = $this->db->get_where($this->moduleTable, array("id"=>$post["id"], "status"=>1));
			if($query->num_rows() == 1){
				$row = $query->row();
				$url = $row->url;
				if($url){
					$session = $this->session->userdata("logged_in");
					if($session){
						$session["module_id"] = $post["id"];
						$this->session->set_userdata("logged_in", $session);
						$resultset["response"] = true;
						$resultset["redirect"] = site_url($url);
					}else{
						$resultset["response"] = false;
					}
				}else{
					$resultset["response"] = false;
				}
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}

	function add_version(){
		$data = $this->portal_model->addPortalVersion();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}

	function get_user_name(){
		$data = $this->portal_model->getUsername();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}
	function get_unapproved_loa(){
		$data = $this->portal_model->getUnapprovedLoa();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}
	function get_to_recommendation(){
		$data = $this->portal_model->getTravelOrderRecommendation();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}
	function get_accountability(){
		$data = $this->portal_model->getAccountability();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}
  public function get_company_collection()
  {
      $data = $this->portal_model->getCompanyCollection();
      echo json_encode($data);
  }

	function get_borrowing(){
		$data = $this->portal_model->getBorrowing();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}
	function get_overtime(){
		$data = $this->portal_model->getOvertime();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}
	function get_transmittal(){
		$data = $this->portal_model->getTransmittal();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}
	function get_shipping(){
		$data = $this->portal_model->getShipping();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}
	function get_cashadvance(){
		$data = $this->portal_model->getCashadvance();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}

	public function get_payslip(){
		$data = $this->portal_model->getPayslip();
		$this->output
			->set_content_type('json')
			->set_output(json_encode($data));
	}
	
	// public function get_deductions(){
	// 	$data = $this->portal_model->getDeductions();
	// 	$this->output
	// 		->set_content_type('json')
	// 		->set_output(json_encode($data));
	// }
}
