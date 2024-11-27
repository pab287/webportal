<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Rates extends MY_Controller {
        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("pms");
            $this->authenticate->doRedirect();
            $this->load->model("Rates_m", "adm_rates");
        }

        function items() {
            $this->core_layout->setPrivilegeName("pms_rates_masterfile");
            $this->core_layout->addJs("js/pms/rates/rates_masterfile.js", true);
            $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('pms/rates/index');
            $this->load->view('core/templates/footer');
        }

        function do_post_event($function = null) {
            $data = $this->adm_rates->doPostEvent($function);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function select2_rate_items() {
            $data = $this->adm_rates->select2RateItems();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_task_item($id = null) {
            $data = $this->adm_rates->getTaskItem($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function open_modal() {
            $data = array();
            $formData = $this->input->post('formData');
            $init_modal_data_function = $this->input->post('init_modal_data_function');
            $model = $this->input->post('model');

            $data['html'] = $this->utilities->openModal();

            if ($init_modal_data_function) {
                $data['info'] = $this->adm_rates->$init_modal_data_function($formData);
            }

            echo json_encode($data);
        }

        function add_item_rate() {
            $data = $this->adm_rates->addItemRate();
            echo json_encode($data);
        }

        function edit_item_rate() {
            $data = $this->adm_rates->editItemRate();
            echo json_encode($data);
        }

        function get_rate_update_history($rate_id) {
            $data = $this->adm_rates->getRateUpdateHistory($rate_id);
            echo json_encode($data);
        }

        function set_rate_status($id) {
            echo json_encode($this->adm_rates->setRateStatus($id));
        }

        function units() {
            $this->core_layout->setPrivilegeName("pms_rate_units");
            $this->core_layout->addJs("js/pms/units/script.js", TRUE);

            $this->load->view('core/templates/header');
            $this->load->view('pms/units/index');
            $this->load->view('core/templates/footer');
        }

        function get_uom() {
            echo json_encode($this->adm_rates->getUom());
        }

        function add_unit() {
            echo json_encode($this->adm_rates->addUnit());
        }

        function edit_unit($id) {
            echo json_encode($this->adm_rates->editUnit($id));
        }
    }