<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard_model extends CI_Model{
    private $userdata = array();
    private $current_data_time = null;

    function __construct(){
        parent::__construct();
        if ($this->session->userdata("logged_in")) {
            $this->userdata = $this->session->userdata("logged_in");
        }

        $this->current_data_time = new DateTime(null, new DateTimeZone('Asia/Manila'));
    }
}