<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Event_logs_model extends CI_Model
    {
        function __construct()
        {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->load->model('gcctime/shift_management_model', 'shift_management');
            $this->user_data = $this->session->userdata("logged_in");
        }

        private function getUserData()
        {
            return $this->core_layout->getUserLoggedIn();
        }

        function getEventLogs(){
            $resultarray = array();
            $post = $this->input->post();
    
            $order_val = array(array("column"=>"9", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
            $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
            $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
            $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
            
            $filterFields = array("a.log_message","a.user_action","a.type","b.firstname","b.middlename","b.lastname");
    
            $this->db->select("a.log_message, a.user_action, a.type, a.created_at, b.firstname, b.middlename, b.lastname");
            $this->db->from("gccsms.user_logs_event a");
            $this->db->join("gccmaster.tblemployees b", "b.id = a.user_id", "LEFT");
            
            if($search != ""){
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
            }
            
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
    
            $this->db->order_by("a.created_at","DESC");
            
            $query = $this->db->get();
    
            if($query->num_rows() > 0){
                foreach($query->result_array() as $_query){
                    $data = array();
    
                    $data["checkbox"] = '';
                    $data["log_message"] = $_query['log_message'];
                    $data["user"] = $_query["firstname"]." ".$_query["lastname"];
                    $data["user_action"] = $_query["user_action"];
                    $data["type"] = $_query["type"];
                    $data["created_at"] = date('Y-m-d g:i A', strtotime($_query["created_at"]));
                    $resultarray[] = $data;
                }
            }
    
            $total = $this->getEventLogsCount();
            return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
        }
    
        function getEventLogsCount(){
            $this->db->select("a.id");
            $this->db->from("gccsms.user_logs_event a");
            $this->db->join("gccmaster.tblemployees b", "b.id = a.user_id", "LEFT");
            $query = $this->db->get();
            return $query->num_rows();
        }

    }
