<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Gcceforms_sa extends dbase{
    private $gcceforms_sa_m;

    public function __construct(){
        $this->gcceforms_sa_m = new Gcceforms_sa_m();
    }

    public function getSAData(){
        echo $this->gcceforms_sa_m->get_sa_data();
    }

    public function getEmployeeData(){
        echo $this->gcceforms_sa_m->get_employee_data();
    }

    public function addItemModal(){
        echo $this->gcceforms_sa_m->add_item_modal();
    }

    public function getContent(){
        echo $this->gcceforms_sa_m->get_content();
    }

    public function clearContents(){
        echo $this->gcceforms_sa_m->clear_content();
    }

    public function deleteContent(){
        echo $this->gcceforms_sa_m->delete_content();
    }

    public function updateContent(){
        echo $this->gcceforms_sa_m->update_content();
    }

    public function viewShippingDetails(){
        echo $this->gcceforms_sa_m->view_shipping_details();
    }

    public function disapproveShipping(){
        echo $this->gcceforms_sa_m->disapprove_shipping();
    }

    public function undoDisapproveShipping(){
        echo $this->gcceforms_sa_m->undo_disapprove_shipping();
    }

    public function cancelShipping(){
        echo $this->gcceforms_sa_m->cancel_shipping();
    }

    public function undoCancelShipping(){
        echo $this->gcceforms_sa_m->undo_cancel_shipping();
    }

    public function approveShipping(){
        echo $this->gcceforms_sa_m->approve_shipping();
    }

    public function undoApproveShipping(){
        echo $this->gcceforms_sa_m->undo_approve_shipping();
    }

    public function receiveShipping(){
        echo $this->gcceforms_sa_m->receive_shipping();
    }

    public function undoReceiveShipping(){
        echo $this->gcceforms_sa_m->undo_receive_shipping();
    }

    public function viewItemContent(){
        echo $this->gcceforms_sa_m->shipping_content_table();
    }

    public function qrScannedReceive(){
        echo $this->gcceforms_sa_m->qr_scanned_receive();
    }

    public function saveQrReceived(){
        echo $this->gcceforms_sa_m->save_qr_received();
    }
}

?>