<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Time_in_out extends Dbase{
    private $TIO;

    public function __construct(){
        $this->TIO = new Time_in_out_model();
    }
    
    public function verifyLocation(){
        $data = $this->TIO->user_sites();
        echo $data;
    }

    public function timeLog(){
        $data = $this->TIO->time_log();
        echo $data;
    }

    public function timeLog_spam(){
        $data = json_encode(array("status" => 1));
        echo $data;
    }

    public function timeLogOffline(){
        $data = $this->TIO->time_log_offline();
        echo $data;
    }

    public function timeLogOfflineSpam(){
        $data = $this->TIO->time_log_offline_spam();
        echo $data;
    }

    public function getAllLogs(){
        $data = $this->TIO->all_logs();
        echo $data;
    }

    public function getAllTimeInOut(){
        $data = $this->TIO->get_all_time_in_out();
        echo $data;
    }

    public function dateCalendar(){
        $data = $this->TIO->date_calendar();
        echo $data;
    }

    public function sendRemarks(){
        $data = $this->TIO->send_remarks();
        echo $data;
    }

    public function fetchUserAttendance(){
        $data = $this->TIO->fetch_user_attendance();
        echo $data;
    }

    public function geofence(){
        $data = $this->TIO->getLocation();
        echo $data;
    }
    
    public function appVersion(){
        $data = $this->TIO->app_version();
        echo $data;
    }
}
?>