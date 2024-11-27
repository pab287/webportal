<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Dashboard extends MX_Controller {
        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("pms");
            $this->authenticate->doRedirect();
            $this->load->model("Dashboard_m", "dashboard");
            $this->load->model("ams/Utilities_model", "utilities");
        }

        public function index() {
            $this->core_layout->addJs("js/pms/dashboard/index.js", true);
            $this->load->view('core/templates/header');
            $this->load->view('dashboard/index');
            $this->load->view('core/templates/footer');
        }

        public function get_unit_task_demographics() {
            $data = $this->dashboard->getUnitTaskDemographics();
            echo json_encode($data);
        }

        public function get_project_unit_code($unit_id) {
            $data = $this->dashboard->getProjectUnitCode($unit_id);
            // echo json_encode($data);
        }

        function open_modal() {
            $data = array();
            $formData = $this->input->post('formData');
            $init_modal_data_function = $this->input->post('init_modal_data_function');
            $model = $this->input->post('model');

            $data['html'] = $this->utilities->openModal();

            if (!empty($init_modal_data_function)) {
                $data['info'] = $this->dashboard->$init_modal_data_function($formData);
            }

            echo json_encode($data);
        }
    }

    /* End of file Dashboard.php */