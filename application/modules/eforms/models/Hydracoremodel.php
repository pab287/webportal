<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hydracoremodel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model("eforms/billing_m", "billing");
        date_default_timezone_set("Asia/Manila");
    }

    public function getAccountsCollection(){
        $this->db->select("*");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("status",1);
        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function getAccount(){
        $get = $this->input->get();
        
        $this->db->select("*");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("id",$get["id"]);
        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->row_array() : false;
    }

    public function getBillbyID(){
        $get = $this->input->get();

        $this->db->select("*");
        $this->db->from("hydra_billing.bills");
        $this->db->where("id",$get["id"]);
        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->row_array() : false;
    }


    private function getBillDetailsbyRefno($billRefno = NULL){

        $this->db->select("id, is_paid");
        $this->db->from("hydra_billing.bills");
        $this->db->where("ref_no",$billRefno);
        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->row_array() : false;
    }

    public function getAccountDetailsbyAccountno(){
        $get = $this->input->get();

        $this->db->select("id, accountno, meterno, firstname, lastname, model AS house_model, block, lot, phonenumber");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("accountno",$get["accountno"]);
        $this->db->where("status", 1);
        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->row_array() : false;
    }

    public function validateAccount(){
        $get = $this->input->get();

        $this->db->select("id,accountno, meterno, firstname, lastname, model AS house_model, block, lot, phonenumber");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("accountno",$get["accountno"]);
        $query = $this->db->get();

        return $query->num_rows() > 0 ? array("result" => "true", "data" => $query->row_array()) : false;
    }

    public function getUnpaidBillCollectionbyAccoutId(){
        $get = $this->input->get();

        $this->db->select("*");
        $this->db->from("hydra_billing.bills");
        $this->db->where("account_id",$get["account_id"]);
        $this->db->where("is_paid",0);
        $get = $this->db->get();

        return $get->num_rows() > 0 ? $get->result_array() : false;
    }

    public function insertPayment(){
        $post = $this->input->post();
        $data = array();

        //generate reference no.
        $current_date = date("Y-m-d H:i:s");
        $code = 'BHP';
        $ref_no = $this->billing->series($current_date, 'hydra_billing.payments', $code);
        $ref_series = explode("-",$ref_no)[2];
        $ref_month = explode("-",$ref_no)[1];
        $ref_yr = explode($code,explode("-",$ref_no)[0])[1];

        //get account data by account no.
        $customerAccountDetails = $this->getAccountDetailsbyAccountno($post["accountno"]);
        $billDetails = $this->getBillDetailsbyRefno($post["bill_ref_no"]);

        // prepare payment data sets to insert
        $data["ref_no"] = $ref_no;
        $data["ref_series"] = $ref_series;
        $data["ref_yr"] = $ref_yr;
        $data["ref_month"] = $ref_month;
        $data["account_id"] = $customerAccountDetails["account_id"];
        $data["bill_id"] = $billDetails["id"];
        $data["payment_type"] = "luvpark";
        $data["payment_details"] = $post["luvpark_trans_ref"];
        $data["received_amount"] = $post["received_amount"];
        $data["payment_date"] = $post["payment_date"];
        $data["net_payment"] = $post["received_amount"];
        $data["sub_total"] = $post["received_amount"];
        $data["created_date"] = $current_date;

        //execute insert payment
        $this->db->insert("hydra_billing.payments",$data);

        // Check if insertion was successful
        if ($this->db->affected_rows() > 0) {
            $this->billing->updateBillingPaidStatus($bill_id, '1');
            $this->billing->updateDisconnectionStatus($customerAccountDetails["account_id"]);
            return $this->db->insert_id(); // Return the last inserted ID
            
        } else {
            return false; // Insert failed
        }
    }


}

?>
