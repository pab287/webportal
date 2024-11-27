<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class App_users extends MY_Controller {
        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("time");
            $this->authenticate->doRedirect();
            
            $this->load->model("App_users_model", "app_user");
        }

        public function index() {
            $this->core_layout->setPrivilegeName("app_users");
            $this->load->view('core/templates/header');
            $this->load->view('gcctime/app_users/index');
            $this->load->view('core/templates/footer');
        }

        public function getUsersLogin(){
            $data = $this->app_user->all_users_login();
            echo json_encode($data);
        }

        public function deleteAppUser(){
            $data = $this->app_user->delete_app_user();
            echo json_encode($data);
        }

        public function signOutAppUser(){
            $data = $this->app_user->signout_app_user();
            echo json_encode($data);
        }
    }