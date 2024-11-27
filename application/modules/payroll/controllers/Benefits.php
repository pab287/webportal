<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Benefits extends MY_Controller {
        public function __construct() {
            parent::__construct();
            $this->authenticate->setModuleAccess("payroll");
            $this->authenticate->doRedirect();
            $this->load->model("Benefits_m", "benefits");
        }

        public function index() {
            $this->core_layout->setPageTitle("Payroll - Benefits");
            $this->core_layout->setPrivilegeName("benefits");
            $this->core_layout->addJs("js/payroll/benefits/masterfile.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('benefits/masterfile.php');
            $this->load->view('core/templates/footer');
        }

        public function archive() {
            $this->core_layout->setPageTitle("Payroll - Benefits (Archive)");
            $this->core_layout->setPrivilegeName("benefits");
            $this->core_layout->addJs("js/payroll/benefits/archive.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('benefits/archive.php');
            $this->load->view('core/templates/footer');
        }
 
        function masterfile(){
            $data = $this->benefits->masterfile();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function archive_list(){
            $data = $this->benefits->archiveList();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function save(){
            $data = $this->benefits->save();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function edit($id){
            $data = $this->benefits->edit($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function update($id){
            $data = $this->benefits->update($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function delete($id){
            $data = $this->benefits->delete($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function restore($id){
            $data = $this->benefits->restore($id);
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }

        function get_employee(){
            $data = $this->benefits->getEmployee();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
    }