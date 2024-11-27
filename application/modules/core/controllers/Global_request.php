<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Global_request extends MY_Controller{
    function __construct(){
        parent::__construct();
        $this->authenticate->doRedirect();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model", "dt_model");
    }

    function get_payroll_pending(){
        $data =  $this->core_layout->getPayrollPendings();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
        //echo json_encode($data);
    }

    function check_ca_previ(){
        $result = array();
        //$has_previ = (isset($this->core_layout->generateNotifPrivilegeAction()['ca_masterfile'])) ? $this->core_layout->generateNotifPrivilegeAction()['ca_masterfile'] : $this->core_layout->generateNotifPrivilegeAction();
        $has_previ = $this->core_layout->personal_roles_for_notif();
        if(in_array('ca_payroll_notif', $has_previ) || in_array('ca_acctg_notif', $has_previ) || in_array('ca_acctg_fo_notif', $has_previ) || in_array('ca_approval_notif', $has_previ)){
            $result['response']  = true;
        }else{
            $result['response']  = false;
        }

        $data = $result;

        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_payroll_pending_count(){
        $data = $this->core_layout->get_all_ca();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_acctg_pending_count(){
        $data =  $this->core_layout->get_acctg_pending_count();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
        //echo json_encode($data);
    }

    // for checking of cash advance notification role
    function personal_role(){
        $result = array();
        $data = $this->core_layout->personal_roles_for_notif();
        $target = array('ca_acctg_notif', 'ca_approval_notif', 'ca_acctg_fo_notif', 'ca_payroll_notif');

        if(count(array_intersect($data, $target) > 0)){
            $result['response'] = true;
        }else{
            $result['response'] = false;
        }

        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($result));
    }

    function notification(){
        $this->load->view('templates/header');
        $this->load->view('templates/ca_notif/notif_client');
        $this->load->view('templates/footer');
    }
}