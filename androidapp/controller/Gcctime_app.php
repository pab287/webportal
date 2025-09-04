<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

    class Gcctime_app extends Dbase{
        private $GCCTIME_EFORM;

        public function __construct(){
            $this->GCCTIME_EFORM = new Gcctime_app_m();
        }
            
        public function gcctime_eform_login(){
            $data = $this->GCCTIME_EFORM->gcctimeEformLogin();
            echo $data;
        }

        public function is_signoutv311(){
            $data = $this->GCCTIME_EFORM->isSignoutv311();
            echo $data;
        }
        
        public function portal_modules(){
            $data = $this->GCCTIME_EFORM->portalModules();
            echo $data;
        }

        public function user_privilege(){
            $data = $this->GCCTIME_EFORM->userPrivilege();
            echo $data;
        }

        public function get_geofence(){
            $data = $this->GCCTIME_EFORM->getGeofence();
            echo $data;
        }
        public function get_geofencev311(){
            $data = $this->GCCTIME_EFORM->getGeofencev311();
            echo $data;
        }

        public function fetch_attendance(){
            $data = $this->GCCTIME_EFORM->fetchAttendance();
            echo $data;
        }
        public function fetch_attendancev311(){
            $data = $this->GCCTIME_EFORM->fetchAttendancev311();
            echo $data;

        }
        public function time_log_offline(){
            $data = $this->GCCTIME_EFORM->timeLogOffline();
            echo $data;
        }

        public function time_log_offlinev311(){
            $data = $this->GCCTIME_EFORM->timeLogOfflinev311();
            echo $data;
        }

        public function time_log(){
            $data = $this->GCCTIME_EFORM->timeLog();
            echo $data;
        }
        public function time_logv311(){
            $data = $this->GCCTIME_EFORM->timeLogv311();
            echo $data;
        }

        public function invalid_action_logv311(){
            $data = $this->GCCTIME_EFORM->invalidActLogv311();
            echo $data;
        }
        public function offline_invalid_action_logv311(){
            $data = $this->GCCTIME_EFORM->offlineInvalidActLogv311();
            echo $data;
        }

        public function gcctime_loginv311(){
            $data = $this->GCCTIME_EFORM->gcctimeLoginv311();
            echo $data;
        }
        public function app_versionv311(){
            $data = $this->GCCTIME_EFORM->appVersionv311();
            echo $data;
        }
        public function travel_order_data(){
            $data = $this->GCCTIME_EFORM->getEmployeeTravelOrder();
            echo $data;
        }
    }
?>