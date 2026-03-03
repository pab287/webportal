<?php defined('BASEPATH') || exit('No direct script access allowed');
class App_users extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
        $this->load->model("App_users_model", "app_user");
    }

    public function index() {
        $this->core_layout->setPrivilegeName("app_users");
        $this->core_layout->setPageTitle("Application Users");
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
        $this->load->view('core/templates/header');
        $this->load->view('gcctime/app_users/index');
        $this->load->view('core/templates/footer');
    }

    public function device_logs() {
        $this->core_layout->setPrivilegeName("app_users-device_logs");
        $this->core_layout->setPageTitle("Application Users - Device Logs");
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);

        $path = FCPATH . 'uploads/data/app/';

        // Get only .json files
        $files = glob($path . '*.json');

        $data['files'] = [];

        if (!empty($files)) {
            foreach ($files as $file) {
                $data['files'][] = basename($file);
            }
        }

        $this->load->view('core/templates/header');
        $this->load->view('gcctime/app_users/device_logs', $data);
        $this->load->view('core/templates/footer');
    }

    public function getUsersLogin(){
        //$data = $this->app_user->all_users_login();
        $data = $this->app_user->getAppAttenanceUsersRequest();
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

    public function update_allow_user_access(){
        $data = $this->app_user->updateAllowUserAccess();
        echo json_encode($data);
    }

    public function get_device_log_json(){
        $data = $this->app_user->getDeviceLogJson();
        echo json_encode($data);
    }

    public function get_active_app_users(){
        $data = $this->app_user->getActiveAppUsers();
        echo json_encode($data);
    }

    public function get_device_log_files(){
        $data = $this->app_user->getDeviceLogFiles();
        echo json_encode($data);
    }
}
