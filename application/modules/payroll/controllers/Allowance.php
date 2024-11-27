<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Allowance extends MY_Controller {
        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("payroll");
            $this->authenticate->doRedirect();
            $this->load->model("Allowance_m", "allowance");
        }

        public function index() {
            $this->core_layout->setPageTitle("Payroll - Allowance");
            $this->core_layout->setPrivilegeName("allowance");
            $this->core_layout->addJs("js/payroll/allowance/masterfile.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('allowance/masterfile.php');
            $this->load->view('core/templates/footer');
        }

        public function archive() {
            $this->core_layout->setPageTitle("Payroll - Allowance (Archive)");
            $this->core_layout->setPrivilegeName("allowance");
            $this->core_layout->addJs("js/payroll/allowance/archive.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('allowance/archive.php');
            $this->load->view('core/templates/footer');
        }
 
        function masterfile(){
            $data = $this->allowance->masterfile();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function archive_list(){
            $data = $this->allowance->archiveList();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function save(){
            $data = $this->allowance->save();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function edit($id){
            $data = $this->allowance->edit($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function update($id){
            $data = $this->allowance->update($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function delete($id){
            $data = $this->allowance->delete($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        function get_employee(){
            $data = $this->allowance->getEmployee();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function restore($id){
            $data = $this->allowance->restore($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
    }