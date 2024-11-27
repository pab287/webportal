<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Receive_option extends MY_Controller{

  function __construct() {
    parent::__construct();
    $this->load->model('Receive_option_model', 'rom');
  }

  public function payslip_receive_option(){
    $this->core_layout->setPageTitle("HRIS - Payslip Receive Option");
    $this->core_layout->setPrivilegeName("HRIS-RECEIVE_OPTIONS");
    $this->load->view("core/templates/header");
    $this->load->view("hris/masterfile/receive_option/index");
    $this->load->view("core/templates/footer");
  }

  public function get_payslip_options(){
    $data = $this->rom->getPayslipOptions();
    $this->output->set_content_type('json')->set_output(json_encode($data));
  }

  public function update_payslip_options(){
    $data = $this->rom->updatePayslipOptions();
    $this->output->set_content_type('json')->set_output(json_encode($data));
  }

  public function select_payroll_group() {
    $data = $this->rom->selectPayrollGroup();
    $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
}
public function get_company_list() {
  $data = $this->rom->getCompanySelect2Data();
  $this->output
      ->set_content_type('json')
      ->set_output(json_encode($data));
}

}