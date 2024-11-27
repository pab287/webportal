<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Gcceforms_ot extends dbase{
    private $Gcceforms_ot_m;

    public function __construct(){
        $this->Gcceforms_ot_m = new Gcceforms_ot_m();
    }

    public function getOData(){
        echo $this->Gcceforms_ot_m->get_ot_data();
    }

    public function getEmployeeData(){
        echo $this->Gcceforms_ot_m->get_employee_data();
    }
    public function viewOtDetails(){
        echo $this->Gcceforms_ot_m->view_ot_details();
    }
    public function viewEditOtDetails(){
        echo $this->Gcceforms_ot_m->view_edit_ot_details();
    }

    public function saveOvertime(){
        echo $this->Gcceforms_ot_m->save_overtime();
    }

    public function getOvertimeRequestDetails(){
        echo $this->Gcceforms_ot_m->get_overtime_request_details();
    }

    public function updateOvertime(){
        echo $this->Gcceforms_ot_m->update_overtime();
    } 

    public function tempUploadFile(){
        echo $this->Gcceforms_ot_m->temp_upload_file();
    }

    public function approveOvertime(){
        echo $this->Gcceforms_ot_m->approve_overtime();
    }

    public function disapproveOvertime(){
        echo $this->Gcceforms_ot_m->disapprove_overtime();
    }

    public function undoDisapproveOvertime(){
        echo $this->Gcceforms_ot_m->undo_disapprove_overtime();
    }

    public function undoApproveOvertime(){
        echo $this->Gcceforms_ot_m->undo_approve_overtime();
    }

    public function cancelOvertime(){
        echo $this->Gcceforms_ot_m->cancel_overtime();
    }
    
}
?>