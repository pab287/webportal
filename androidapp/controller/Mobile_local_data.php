<?php
    class Mobile_local_data extends Dbase{
        private $locationModel;
        private $TIO;
        private $loginModel;

        public function __construct(){
            $this->locationModel = new Location_model();
            $this->TIO = new Time_in_out_model();
            $this->loginModel = new Login_model();
        }

        public function localData(){
            $post = $_POST;
            if(isset($post['tbl_name'])){
                $tbl_name = $post['tbl_name'];
                unset($_POST['tbl_name']);
                
                switch($tbl_name){
                    case "tbl_location":
                        echo $tbl_name;
                        unset($_POST['coords']);
                        $_POST['coords'] = json_decode($_POST['coords']);
                        $this->locationModel->user_data();
                        break;
                    case "timeinout":
                        $this->TIO->time_log_offline();
                        $this->TIO->time_log();
                        break;
                    default:
                        break;
                }

            }
        }

    }
?>