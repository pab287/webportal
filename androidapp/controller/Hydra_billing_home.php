<?php

class Hydra_billing_home extends Dbase{
    private $hydra_billing_home_m;

    public function __construct(){
        $this->hydra_billing_home_m = new Hydra_billing_home_m();
    }

    public function getListAccounts(){
        echo $this->hydra_billing_home_m->get_list_accounts();
    }

    public function saveLocalData(){
        echo $this->hydra_billing_home_m->save_local_data();
    }
}

?>