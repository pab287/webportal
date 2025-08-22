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


        public function inout_pin(){
            $pin = ["lat"=> 14.5409213, "lng" => 121.0179794];
            $pin2 = ["lat"=> 14.5776287, "lng" => 121.0514059];
            $pin3 = ["lat"=> 14.5779186, "lng" => 121.0510310];
            $pin4 = ["lat"=> 14.5790956, "lng" => 121.0494040];
            $polygons = [];
            $pol1 = 'a:4:{i:0;a:2:{s:3:"lat";s:18:"14.589899799354106";s:3:"lng";s:18:"121.06197166847988";}i:1;a:2:{s:3:"lat";s:18:"14.589487079899774";s:3:"lng";s:17:"121.0616323690395";}i:2;a:2:{s:3:"lat";s:18:"14.589357293747911";s:3:"lng";s:18:"121.06182012367054";}i:3;a:2:{s:3:"lat";s:17:"14.58978169418048";s:3:"lng";s:18:"121.06214467096135";}}';
            $pol2 = 'a:5:{i:0;a:2:{s:3:"lat";s:18:"14.577932066629783";s:3:"lng";s:18:"121.05158517781739";}i:1;a:2:{s:3:"lat";s:18:"14.577459619880807";s:3:"lng";s:18:"121.05127940598969";}i:2;a:2:{s:3:"lat";s:18:"14.576556257342425";s:3:"lng";s:18:"121.05264196816925";}i:3;a:2:{s:3:"lat";s:18:"14.577002747335811";s:3:"lng";s:18:"121.05295846883301";}i:4;a:2:{s:3:"lat";s:18:"14.577973600361473";s:3:"lng";s:18:"121.05160127107148";}}';
            $p1Data = @unserialize($pol1);
            $p2Data = @unserialize($pol2);
            array_push($polygons, $p1Data, $p2Data);
            $coords = $this->GCCTIME_EFORM->detectPolygonsNearPin($pin3, $polygons);
            var_dump($coords);
        }

    }
?>