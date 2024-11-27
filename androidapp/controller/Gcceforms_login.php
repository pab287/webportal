<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Gcceforms_login extends Dbase{
    private $gcceforms_login_m;

    public function __construct(){
        $this->gcceforms_login_m = new Gcceforms_login_m();
    }

    public function userLogin(){
        echo $this->gcceforms_login_m->user_login();
    }

    public function getPortalSubModule(){
        echo $this->gcceforms_login_m->get_portal_sub_module();
    }
   
    public function userPrivilege(){
        echo $this->gcceforms_login_m->user_privilege();
    }

    public function test_(){
        echo $this->gcceforms_login_m->test_only();
    }
}

?>