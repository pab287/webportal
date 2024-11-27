<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Access_control extends MY_Controller
    {
        function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("users");
            $this->authenticate->doRedirect();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");

            $this->core_layout->addJs("js/jstree/jstree.min.js");
            $this->core_layout->addCss("css/jstree/themes/default-dark/style.min.css");
            $this->core_layout->setPageTitle("Access Control");
            $this->core_layout->setBodyClass("configuration");
            $this->core_layout->setPrivilegeName("manage_access_control");
        }

        function index()
        {
            $this->load->view('templates/header');
            $this->load->view('access_control/index');
            $this->load->view('templates/footer');
        }

        function add_acl()
        {
            $resultset = array();
            $resultset = $this->acl_model->addAcessControl();

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($resultset));
        }

        function update_acl()
        {
            $resultset = array();
            $resultset = $this->acl_model->updateAcessControl();

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($resultset));
        }

        function remove_acl()
        {
            $resultset = array();
            $resultset = $this->acl_model->removeAccessControl();

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($resultset));
        }

        function list_acl()
        {
            $search = isset($_GET['search']) ? $_GET["search"] : "";
            $resultset = array();
            $resultset = $this->acl_model->listAccessControl($search);

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($resultset));
        }

        function get_acl_list()
        {
            $data = $this->acl_model->getAccessControlList();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_acl_content_add()
        {
            $html = $this->load->view("core/access_control/modal_content/add", null, true);
            echo $html;
        }

        function get_acl_data()
        {
            $data = $this->acl_model->getAccessControlUpdate();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_json_acl()
        {
            $data = $this->acl_model->getJsonAcl();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_acl_actions()
        {
            $data = $this->acl_model->getAclActions();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function set_acl_json_actions()
        {
            $data = $this->acl_model->setAclJsonActions();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        
    }