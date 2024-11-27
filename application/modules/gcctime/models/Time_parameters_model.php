<?php
    defined('BASEPATH') or exit('No direct script access allowed');

    class Time_parameters_model extends CI_Model {
        protected $tbl_time_parameters = "gcctimeutility.time_parameters";
        private $db_debug;

        function __construct() {
            parent::__construct();
            $this->db_debug = $this->db->db_debug;
        }

        public function getNightDiffConfig() {
            return $this->db->get_where($this->tbl_time_parameters, array("param_name" => "NIGHT_DIFF_PARAMS"))->row();
        }

        public function updateNightDiffConfig() {
            $this->db->db_debug = false;
            $ctr = $this->db->where("param_name", "NIGHT_DIFF_PARAMS")->count_all_results($this->tbl_time_parameters);
            $resultSet = array();
            $this->db->trans_begin();
            $post = $this->input->post();

            if ($ctr >= 1) {
                $this->db->where("param_name", "NIGHT_DIFF_PARAMS");
                $this->db->set($post);
                $this->db->update($this->tbl_time_parameters);
            } else {
                $post["param_name"] = "NIGHT_DIFF_PARAMS";
                $post["description"] = "DETERMINE NIGHT DIFFERENTIAL";
                $this->db->insert($this->tbl_time_parameters, $post);
            }

            if ($this->db->trans_status() === TRUE) {
                $resultSet["success"] = true;
                $resultSet["title"] = "Config. Updated";
                $resultSet["message"] = "Night Differential config. successfully updated.";
                $this->db->trans_commit();
            } else {
                $resultSet["success"] = false;
                $resultSet["title"] = $this->db->error()["message"];
                $resultSet["message"] = "An error occurred.";
                $this->db->trans_rollback();
            }

            $this->db->db_debug = $this->db_debug;
            return $resultSet;
        }

        public function updateTsOtConfig() {
            $this->db->db_debug = false;
            $ctr = $this->db->where("param_name", "TS_OT_PARAMS")->count_all_results($this->tbl_time_parameters);
            $resultSet = array();
            $this->db->trans_begin();
            $post = $this->input->post();

            if ($ctr >= 1) {
                $this->db->where("param_name", "TS_OT_PARAMS");
                $this->db->set($post);
                $this->db->update($this->tbl_time_parameters);
            } else {
                $post["param_name"] = "TS_OT_PARAMS";
                $post["description"] = "PARAMS FOR TIMESHEET & OVERTIME, TO DETERMINE INCLUSION OF ATTENDANCE TO GENERATE";
                $this->db->insert($this->tbl_time_parameters, $post);
            }

            if ($this->db->trans_status() === TRUE) {
                $resultSet["success"] = true;
                $resultSet["title"] = "Config. Updated";
                $resultSet["message"] = "Overtime & Attendance time parameters successfully updated.";
                $this->db->trans_commit();
            } else {
                $resultSet["success"] = false;
                $resultSet["title"] = $this->db->error()["message"];
                $resultSet["message"] = "An error occurred.";
                $this->db->trans_rollback();
            }

            $this->db->db_debug = $this->db_debug;
            return $resultSet;
        }

        public function getTsOtPrams() {
            return $this->db->get_where($this->tbl_time_parameters, array("param_name" => "TS_OT_PARAMS"))->row();
        }
    }