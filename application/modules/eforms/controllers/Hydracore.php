<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hydracore extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("hydracoremodel","hydracore");
    }
 

    public function get_account_collection(){
        $data = $this->hydracore->getAccountsCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_account_by_id(){
        $data = $this->hydracore->getAccountById();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_bill_by_id(){
        $data = $this->hydracore->getBillbyID();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_account_details_by_account_no(){
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

    public function get_unpaid_bill_collection_by_account_id(){
        $data = $this->hydracore->getUnpaidBillCollectionbyAccoutId();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function insert_payment(){
        $data = $this->hydracore->insertPayment();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
}

?>
