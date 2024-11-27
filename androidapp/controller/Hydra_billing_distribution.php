<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Hydra_billing_distribution extends Dbase{
    private $hydra_billing_distribution_m;

    public function __construct(){
        $this->hydra_billing_distribution_m = new Hydra_billing_distribution_m();
    }

    public function getSubdivisionDetails(){
        echo $this->hydra_billing_distribution_m->get_subdivision_details();
    }

    public function saveDistribute(){
        echo $this->hydra_billing_distribution_m->save_distribution();
    }

    public function fetchHistory(){
        echo $this->hydra_billing_distribution_m->fetch_history();
    }

    public function updateDistribution(){
        echo $this->hydra_billing_distribution_m->update_distribution();
    }
}

?>