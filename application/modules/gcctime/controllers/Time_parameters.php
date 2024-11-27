<?php
    defined('BASEPATH') or exit('No direct script access allowed');

    class Time_parameters extends MY_Controller {
        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("time");
            $this->authenticate->doRedirect();

            $this->load->model("Time_parameters_model", "time_params_model");
        }

        public function index() {
            $this->core_layout->setPrivilegeName("time_parameters");
            $data["night_diff"] = $this->time_params_model->getNightDiffConfig();
            $data["ts_ot"] = $this->time_params_model->getTsOtPrams();

            $this->load->view('core/templates/header');
            $this->load->view('time_parameters/index', $data);
            $this->load->view('core/templates/footer');
        }

        public function update_night_diff_config() {
            echo json_encode($this->time_params_model->updateNightDiffConfig());
        }

        public function update_ts_ot_config() {
            echo json_encode($this->time_params_model->updateTsOtConfig());
        }
    }