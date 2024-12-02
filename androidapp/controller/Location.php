<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Location extends Dbase{
    private $locationModel;

    public function __construct(){
        $this->locationModel = new Location_model();
    }

    public function userData(){
        $user_data = $this->locationModel->user_dataV2();
        echo $user_data;
    }

    public function userLocationImage(){
        $user_data = $this->locationModel->upload_location_image();
        echo $user_data;
    }

    public function getAllLocation(){
        $user_data = $this->locationModel->get_all_location();
        echo $user_data;
    }

    public function travelOrder(){
        $user_data = $this->locationModel->travel_order();
        echo $user_data;
    }

    public function arrival(){
        $user_data = $this->locationModel->driver_arrival();
        echo $user_data;
    }

    public function getLocations(){
        $user_data = $this->locationModel->get_locations();
        echo $user_data;
    }

    public function userStatus(){
        $user_data = $this->locationModel->user_status();
        echo $user_data;
    }

    public function update_location(){
        $limit = isset($_GET['limit']) && $_GET['limit'] ? $_GET['limit'] : 100;
        $data = $this->locationModel->update_undefined_location($limit);
        echo json_encode($data);
    }
}
?>