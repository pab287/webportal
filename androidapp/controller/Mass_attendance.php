<?php
    class Mass_attendance extends Dbase{
        private $massAttendance;
    
        public function __construct(){
            $this->massAttendance = new Mass_attendance_model();
        }

        public function attendance(){
            $data = $this->massAttendance->massAttendance();
            echo $data;
        }

        public function getEmployees(){
            $data = $this->massAttendance->get_Employees();
            echo $data;
        }

        public function attendance_history(){
            $data = $this->massAttendance->attendanceHistory();
            echo $data;
        }

        public function getLatestHist(){
            $data = $this->massAttendance->get_latest_hist();
            echo $data;
        }
        
    }
?>