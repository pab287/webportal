<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hydracore extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("hydracoremodel","hydracore");
    }
 

    public function account_collection(){
        $data = $this->hydracore->getAccountsCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_account(){
        $data = $this->hydracore->getAccount();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function bill(){
        $data = $this->hydracore->getBillbyID();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function account_details(){
        $data = $this->hydracore->getAccountDetailsbyAccountno();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function validate_account(){
        $data = $this->hydracore->validateAccount();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function unpaid_bills(){
        $data = $this->hydracore->getUnpaidBillCollectionbyAccoutId();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function add_payment(){
        $data = $this->hydracore->insertPayment();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
}

?>
