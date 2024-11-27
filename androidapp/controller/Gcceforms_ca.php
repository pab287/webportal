<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Gcceforms_ca extends dbase{
    private $gcceforms_ca_m;

    public function __construct(){
        $this->gcceforms_ca_m = new Gcceforms_ca_m();
    }

    public function getCAData(){
        echo $this->gcceforms_ca_m->get_ca_data();
    }

    public function getEmployeeData(){
        echo $this->gcceforms_ca_m->get_employee_data();
    }

    public function saveCashAdvance(){
        echo $this->gcceforms_ca_m->save_cash_advancev1();
    }

    // public function saveCashAdvance(){
    //     echo $this->gcceforms_ca_m->save_cash_advance();
    // }

    public function viewCADetails(){
        echo $this->gcceforms_ca_m->view_ca_details();
    }

    public function updateCashAdvance(){
        echo $this->gcceforms_ca_m->update_cash_advance();
    }

    public function recommendUpdate(){
        echo $this->gcceforms_ca_m->recommend_update();
    }

    public function undoRecommendUpdate(){
        echo $this->gcceforms_ca_m->undo_recommend_update();
    }

    public function disapproveUpdate(){
        echo $this->gcceforms_ca_m->disapprove_update();
    }

    public function undoDisapprovalUpdate(){
        echo $this->gcceforms_ca_m->undo_disapprove_update();
    }

    public function cancelUpdate(){
        echo $this->gcceforms_ca_m->cancel_update();
    }

    public function setHrUpdate(){
        echo $this->gcceforms_ca_m->set_hr_update();
    }

    public function setAcctgUpdate(){
        echo $this->gcceforms_ca_m->set_acctg_update();
    }

    public function undoApprovalUpdate(){
        echo $this->gcceforms_ca_m->undo_approval_update(); 
    }

    public function approveUpdate(){ 
        echo $this->gcceforms_ca_m->approve_update(); 
    }

    public function setIntrstPrcntg(){
        echo $this->gcceforms_ca_m->set_intrst_prcntge();
    }

    public function setAcctBalance(){
        echo $this->gcceforms_ca_m->set_acct_balance();
    }
    public function undoAwaitApprov(){ 
        echo $this->gcceforms_ca_m->undo_awaiting_approval();
    }
    public function undoPosting(){ 
        echo $this->gcceforms_ca_m->undo_posting();
    }
    public function forPosting(){
        echo $this->gcceforms_ca_m->for_posting();
    } 
    public function updatePosted(){
        echo $this->gcceforms_ca_m->update_posted();
    }
    public function undoPosted(){
        echo $this->gcceforms_ca_m->undo_posted();
    }
    public function forFinalApproval(){
        echo $this->gcceforms_ca_m->for_final_approval();
    }
    // public function test123(){
    //     echo $this->gcceforms_ca_m->test_123();
    //     // echo var_dump($_POST);
    // }
} 

?>