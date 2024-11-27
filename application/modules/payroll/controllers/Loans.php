<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Loans extends MY_Controller {
        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("payroll");
            $this->authenticate->doRedirect();
            $this->load->model("Loans_m", "loans");
        }

        public function index() {
            $this->core_layout->setPageTitle("Payroll - Loans");
            $this->core_layout->setPrivilegeName("loans");
            $this->core_layout->addJs("js/payroll/loans/masterfile.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('loans/masterfile.php');
            $this->load->view('core/templates/footer');
        }

        public function archive() {
            $this->core_layout->setPageTitle("Payroll - Loans (Archive)");
            $this->core_layout->setPrivilegeName("loans");
            $this->core_layout->addJs("js/payroll/loans/archive.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('loans/archive.php');
            $this->load->view('core/templates/footer');
        }
 
        function masterfile(){
            $data = $this->loans->masterfile();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        function archive_list(){
            $data = $this->loans->archiveList();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function save(){
            $data = $this->loans->save();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function edit($id){
            $data = $this->loans->edit($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function update($id){
            $data = $this->loans->update($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function delete($id){
            $data = $this->loans->delete($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function restore($id){
            $data = $this->loans->restore($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        function get_employee(){
            $data = $this->loans->getEmployee();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
    }