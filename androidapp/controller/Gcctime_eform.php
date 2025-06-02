<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

    class Gcctime_eform extends Dbase{
        private $GCCTIME_EFORM;

        public function __construct(){
            $this->GCCTIME_EFORM = new Gcctime_eform_m();
        }
            
        public function gcctime_eform_login(){
            $data = $this->GCCTIME_EFORM->gcctimeEformLogin();
            echo $data;
        }

        public function is_suspended(){
            $data = $this->GCCTIME_EFORM->isSuspended();
            echo $data;
        }

        public function is_signout(){
            $data = $this->GCCTIME_EFORM->isSignout();
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
        public function fetch_attendance(){
            $data = $this->GCCTIME_EFORM->fetchAttendance();
            echo $data;
        }
        public function time_log_offline(){
            $data = $this->GCCTIME_EFORM->timeLogOffline();
            echo $data;
        }

        public function time_log(){
            $data = $this->GCCTIME_EFORM->timeLog();
            echo $data;
        }

    }
?>