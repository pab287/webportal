<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Hydra_billing_readings extends Dbase{
    private $hydra_billing_readings_m;

    public function __construct(){
        $this->hydra_billing_readings_m = new Hydra_billing_readings_m();
    }

    public function getCustomerDetails(){
        echo $this->hydra_billing_readings_m->get_customer_details();
    }

    public function saveReading(){
        echo $this->hydra_billing_readings_m->save_reading();
    }

    public function fetchHistory(){
        echo $this->hydra_billing_readings_m->fetch_history();
    }

    public function updateReading(){
        echo $this->hydra_billing_readings_m->update_reading();
    }

    public function generateBill(){
        echo $this->hydra_billing_readings_m->generate_bill();
    }

    public function fetchBilling(){
        echo $this->hydra_billing_readings_m->fetch_billing();
    }

    public function updatePrintCount(){
        echo $this->hydra_billing_readings_m->update_print();
    }

    public function test_computeOverPayment(){
        $account_id = $_POST['account_id'];
        $res = $this->hydra_billing_readings_m->test_computeOverPayment($account_id);
        var_dump($res);
    }
}

?>