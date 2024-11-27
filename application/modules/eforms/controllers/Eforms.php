<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Eforms extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("eforms");
            $this->authenticate->doRedirect();

            $this->core_layout->setBodyClass("eforms");
            $this->core_layout->setPrivilegeName("eforms");

            $this->load->model("portal/portal_model");
        }

        public function portal(){
            $portalContent = $this->portal_model->getPortalSubModule("eforms");
            if ($portalContent) {
                $this->core_layout->addJs("js/portal/portal_script.js", true);

                $data = array();
                $data["portal_content"] = $portalContent;
                $this->load->view("core/templates/header");
                $this->load->view("eforms/portal/index", $data);
                $this->load->view("core/templates/footer");
            } else {
                redirect(base_url("portal/index"), "redirect");
            }
        }

        public function test(){
            $post = $this->input->post();
            return $post;
        }
    }