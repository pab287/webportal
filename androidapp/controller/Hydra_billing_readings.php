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

    
    public function test_compute_balance_last_bill(){
        $account_id = 623;
        $res = $this->hydra_billing_readings_m->test_compute_balance_last_bill($account_id);
        var_dump($res);
    }

    public function testGetBilling(){
        // $reading_id = 14760;
        // $account_id = 623;

        $reading_id = 14517;
        $account_id = 111;
        
        // $reading_id = 14753;
        // $account_id = 622;

        $res = $this->hydra_billing_readings_m->testGetBilling($reading_id, $account_id);
        var_dump($res);
    }

    public function test_computeOverPayment(){
        $res = $this->hydra_billing_readings_m->test_computeOverPayment();
        var_dump($res);
    }

    public function main_charges_bill(){
        $account_id = $_POST['account_id'];
        $res = $this->hydra_billing_readings_m->main_charges_bill($account_id);
        var_dump($res);
    }
}

?>