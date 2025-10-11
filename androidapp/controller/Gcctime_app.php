<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

    class Gcctime_app extends Dbase{
        private $GCCTIME_APP;

        public function __construct(){
            $this->GCCTIME_APP = new Gcctime_app_m();
        }
            
        public function gcctime_eform_login(){
            $data = $this->GCCTIME_APP->gcctimeEformLogin();
            echo $data;
        }

        public function is_signoutv311(){
            $data = $this->GCCTIME_APP->isSignoutv311();
            echo $data;
        }
        
        public function portal_modules(){
            $data = $this->GCCTIME_APP->portalModules();
            echo $data;
        }

        public function user_privilege(){
            $data = $this->GCCTIME_APP->userPrivilege();
            echo $data;
        }

        public function get_geofence(){
            $data = $this->GCCTIME_APP->getGeofence();
            echo $data;
        }
        public function get_geofencev311(){
            $data = $this->GCCTIME_APP->getGeofencev311();
            echo $data;
        }

        public function fetch_attendance(){
            $data = $this->GCCTIME_APP->fetchAttendance();
            echo $data;
        }
        public function fetch_attendancev311(){
            $data = $this->GCCTIME_APP->fetchAttendancev311();
            echo $data;

        }
        public function time_log_offline(){
            $data = $this->GCCTIME_APP->timeLogOffline();
            echo $data;
        }

        public function time_log_offlinev311(){
            $data = $this->GCCTIME_APP->timeLogOfflinev311();
            echo $data;
        }

        public function time_log(){
            $data = $this->GCCTIME_APP->timeLog();
            echo $data;
        }
        public function time_logv311(){
            $data = $this->GCCTIME_APP->timeLogv311();
            echo $data;
        }

        public function invalid_action_logv311(){
            $data = $this->GCCTIME_APP->invalidActLogv311();
            echo $data;
        }
        public function offline_invalid_action_logv311(){
            $data = $this->GCCTIME_APP->offlineInvalidActLogv311();
            echo $data;
        }

        public function gcctime_loginv311(){
            $data = $this->GCCTIME_APP->gcctimeLoginv311();
            echo $data;
        }
        public function app_versionv311(){
            $data = $this->GCCTIME_APP->appVersionv311();
            echo $data;
        }
        public function travel_order_data(){
            $data = $this->GCCTIME_APP->getEmployeeTravelOrder();
            echo $data;
        }

        public function forgot_password(){
            $data = $this->GCCTIME_APP->forgotPassword();
            echo $data;
        }
    }
?>