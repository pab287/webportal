<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Travel_order_login extends Dbase{
    private $travel_order_login_m;

    public function __construct(){
        $this->travel_order_login_m = new Travel_order_login_m();
    }

    public function userLogin(){
        echo $this->travel_order_login_m->user_login();
    }
}

?>