<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Gcceforms_loa extends Dbase{
    private $gcceforms_loa_m;

    public function __construct(){
        $this->gcceforms_loa_m = new Gcceforms_loa_m();
    }

    public function getEmployeeData(){
        echo $this->gcceforms_loa_m->get_employee_data();
    }

    public function getLOAData(){
        echo $this->gcceforms_loa_m->get_loa_data();
    }

    public function saveLOA(){
        echo $this->gcceforms_loa_m->save_loa();
    }

    public function updateLOA(){
        echo $this->gcceforms_loa_m->update_loa();
    }

    public function viewLOADetails(){
        echo $this->gcceforms_loa_m->view_loa_details();
    }
   
    public function approveLOA(){
        echo $this->gcceforms_loa_m->approve_loa();
    }

    public function disapproveLOA(){
        echo $this->gcceforms_loa_m->disapprove_loa();
    }

    public function cancelLOA(){
        echo $this->gcceforms_loa_m->cancel_loa();
    }

    public function noteLOA(){
        echo $this->gcceforms_loa_m->note_loa();
    }

    public function undoCancelLOA(){
        echo $this->gcceforms_loa_m->undo_cancel_loa();
    }

    public function undoDisapproveLOA(){
        echo $this->gcceforms_loa_m->undo_disapprove_loa();
    }

    public function undoApproveLOA(){
        echo $this->gcceforms_loa_m->undo_approve_loa();
    }

    public function undoNoteLOA(){
        echo $this->gcceforms_loa_m->undo_note_loa();
    }

    public function testFunction(){
        echo $this->gcceforms_loa_m->test_function();
    }
}

?>