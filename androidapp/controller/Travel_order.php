<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Travel_order extends Dbase{
    private $travel_order_m;

    public function __construct(){
        $this->travel_order_m = new Travel_order_m();
    }

    public function fetchTravelOrder(){
        echo $this->travel_order_m->fetch_travel_order();
    }

    public function updateDestinationStatus(){
        echo $this->travel_order_m->update_destination_status();
    }

    public function test(){
        echo $this->travel_order_m->test();
    }
    public function travelOrderView(){
        echo $this->travel_order_m->fetch_to_view();
    }
    public function toRecommend(){
        echo $this->travel_order_m->to_recommend();
    }
    public function toApprove(){
        echo $this->travel_order_m->to_approved();
    }
        public function toUndoApproval(){
        echo $this->travel_order_m->to_undo_approval();
    }
        public function toCancel(){
        echo $this->travel_order_m->to_cancel();
    }
        public function toDisapprove(){
        echo $this->travel_order_m->to_disapprove();
    }
        public function toUndoDisapproval(){
        echo $this->travel_order_m->to_undo_disapproval();
    }
        public function toUndoRecommendation(){
        echo $this->travel_order_m->to_undo_recommendation();
    }
        public function toAccomplish(){
        echo $this->travel_order_m->to_accomplish();
    }
        public function toAccomplishAll(){
        echo $this->travel_order_m->to_accomplish_all();
    }
        public function toUndoAccomplishment(){
        echo $this->travel_order_m->to_undo_accomplish();
    }
}

?>