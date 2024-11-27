<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Payroll_type extends MY_Controller {
        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("payroll");
            $this->authenticate->doRedirect();
            $this->load->model("Payroll_type_m", "payroll_type");
        }

        public function index() {
            $this->core_layout->setPageTitle("Payroll - Payroll Type");
            $this->core_layout->setPrivilegeName("payroll_type");
            $this->core_layout->addJs("js/payroll/payroll_type/masterfile.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('payroll_type/masterfile.php');
            $this->load->view('core/templates/footer');
        }
        
        function incentive(){
            $this->core_layout->setPageTitle("Payroll - Payroll Type / Incentive");
            $this->core_layout->setPrivilegeName("payroll_type_incentive");
            $this->core_layout->addJs("js/payroll/payroll_type/incentive.script.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('payroll_type/incentive.php');
            $this->load->view('core/templates/footer');
        }

        function masterfile(){
            $data = $this->payroll_type->masterfile();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function save(){
            $data = $this->payroll_type->save();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function edit($id){
            $data = $this->payroll_type->edit($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function update($id){
            $data = $this->payroll_type->update($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_incentive_payroll_type_datatable_request(){
            $data = $this->payroll_type->getIncentivePayrollTypeDataTableRequest();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function set_payroll_incentive_type(){
            $data = $this->payroll_type->setPayrollIncentiveType();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        function get_payroll_incentive_type($id=null){
            $data = $this->payroll_type->getPayrollIncentiveType($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        function update_payroll_incentive_type(){
            $data = $this->payroll_type->updatePayrollIncentiveType();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        function archive_payroll_incentive_type(){
            $data = $this->payroll_type->archivePayrollIncentiveType();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
    }