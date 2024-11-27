<?php 
    class Hris extends Dbase{
        private $hrisModel;

        public function __construct(){
            $this->hrisModel = new Hris_model();
        }

        public function get_employee_data(){
            $data = $this->hrisModel->get201();
            echo $data;
        }

        public function get_searched(){
            $data = $this->hrisModel->get_searched_employees();
            echo $data;
        }

        public function hrisApiget(){
            $data = $this->hrisModel->empInfo();
            echo $data;
        }

    }
?>