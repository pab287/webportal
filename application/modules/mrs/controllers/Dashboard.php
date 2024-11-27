<?php defined('BASEPATH') OR exit('No direct script access allowed');
    class Dashboard extends MY_Controller
    {
		protected $viewParameters = array();
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("mrs");
            //$this->authenticate->doRedirect();
            $this->load->model("mrs/dashboard_model", "dashboard");
			/*** $this->viewParameters["template_nav"] = "admin_backend"; ***/
			$this->viewParameters["has_template_css"] = true;
			$this->viewParameters["has_template_js"] = true;
			$this->viewParameters["template_body_class"] = "m-aside-left--skin-light m-aside-left--minimize m-brand--minimize "; 
        }

        public function index(){
            $this->core_layout->setPrivilegeName("mrs_dashboard");
			$this->core_layout->addJs("demo/demo6/base/scripts.bundle.js");
			$this->core_layout->addCss("demo/demo6/base/style.bundle.css");
			$this->core_layout->addCss("fonts/poppins/poppins.css");
			$this->viewParameters["view"] = "mrs/dashboard/index";
            $this->core_layout->adminBackendTemplate($this->viewParameters);
        }
    }