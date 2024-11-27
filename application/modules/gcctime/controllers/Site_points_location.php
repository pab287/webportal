<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class site_points_location extends MY_Controller {
	public function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
		$this->authenticate->doRedirect();
		$this->load->model('Site_restrict_model','site_points');
	}

	public function index(){
		// $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyD2szEzfIU7_Hec55jNy8JtoNr_uj8R2_M&callback=initMapTemp&libraries=places,drawing&v=weekly";
		// $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCTzlKHdtvrOuKv7LEQjW8HVmy1QFFgalM&callback=initMapTemp&libraries=places,drawing&v=weekly";
		$externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCm_pTwQzhaAKspErhW9ptpubv_ATLrpgE&callback=initMapTemp&libraries=places,drawing&v=weekly";
		$arrData = array();
		$arrData["script_attribute"] = array("async");
		$this->core_layout->addExternalJs($externalUrl, true, $arrData);

		$this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
		
		$this->core_layout->setPrivilegeName("site_points_location");
		$this->load->view('core/templates/header');
		$this->load->view('site_points_location/index');
		$this->load->view('core/templates/footer');
	}

	public function get_all_site(){
		$data = $this->site_points->getAllSite();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	public function add_new_location(){
		$data = $this->site_points->addNewLocation();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	public function delete_location(){
		$data = $this->site_points->deleteLocation();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	public function edit_location(){
		$data = $this->site_points->editLocation();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	public function all_appointed_location(){
		$data = $this->site_points->allAppointedLocation();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	public function delete_appointed_location(){
		$data = $this->site_points->deleteAppointedLocation();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

}