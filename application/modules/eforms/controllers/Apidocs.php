<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Apidocs extends MY_Controller {

    public function index() {
        $this->load->view('eforms/swagger/docs');
    }
}

?>
