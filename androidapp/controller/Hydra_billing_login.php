<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Hydra_billing_login extends Dbase{
    private $hydra_billing_login_m;

    public function __construct(){
        $this->hydra_billing_login_m = new Hydra_billing_login_m();
    }

    public function userLogin(){
        echo $this->hydra_billing_login_m->user_login();
    }

    public function userLogout(){
        echo $this->hydra_billing_login_m->user_logout();
    }

    public function getPortalSubModule(){
        echo $this->hydra_billing_login_m->get_portal_sub_module();
    }
   
    public function userPrivilege(){
        echo $this->hydra_billing_login_m->user_privilege();
    }

    public function test_(){
        echo $this->hydra_billing_login_m->test_only();
    }
}

?>