<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Registry_m extends CI_Model
    {
        protected $eformsTable = "gcceforms";
        private $current_actions =  array();
        private $user_data = array();
        public function __construct()
        {
            parent::__construct();
            $this->core_layout->setPrivilegeName("eforms_loa");
            $this->user_data = $this->session->userdata("logged_in"); 
            $this->current_action = $this->core_layout->getCurrentActions();
        }

        function getDatatableRequest()
        {
  
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"9", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
            $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
            $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
            $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();

            $rowCount = 0;
            $rowData = array();
            
            $view_own_request = (in_array("view_own_request", $this->current_action)) ? true : false;
            if (!$search) {
                $rowData = $this->get_all_post($view_own_request,  $query_builder, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_post_count($view_own_request,  $query_builder);
            }

            if ($search) {
                $rowData = $this->get_searched_item($view_own_request,  $query_builder, $search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_item_count($view_own_request,  $query_builder, $search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;
          // $resultset["action"] =  $this->core_layout->getCurrentActions();
           
            
            return $resultset;
        }

        private function get_all_post_count($view, $query_builder=null)
        { 
            $this->db->from("gcceforms.loa_invalid_reason a");
            if($query_builder){
                $this->db->where($query_builder);
            }
            $query = $this->db->get();

            return $query->num_rows();
        }

        private function get_all_post($view, $query_builder=null, $limit = 10, $offset = 0, $sortBy, $sortOrder)
        { 
            $sql = "a.status, a.reason, a.created_at, a.updated_at,a.created_by, a.updated_by,a.id"; 
            $this->db->select($sql);
            $this->db->from("gcceforms.loa_invalid_reason a");
            if($query_builder){
                $this->db->where($query_builder);
            } 
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            // //$this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $this->db->order_by("a.created_at","DESC");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if($rs->created_at=="0000-00-00 00:00:00"){ $rs->created_at="";}else{$rs->created_at = $this->dateformat($rs->created_at);}
                    if($rs->updated_at=="0000-00-00 00:00:00"){ $rs->updated_at="";}else{$rs->created_at = $this->dateformat($rs->updated_at);}
                    if($rs->updated_by=="0"){
                        $updated_by="";
                    }else{ 
                        $user_data = $this->loa->employee_details($rs->updated_by);
                        $updated_by = $user_data->display_name;
                    }

                    $user_data = $this->loa->employee_details($rs->created_by);
                    $created_by = $user_data->display_name; 

                    $rs->reason = $rs->reason; 
                    $rs->created_at =  $rs->created_at; 
                    $rs->updated_at =  $rs->updated_at; 
                    $rs->created_by = $created_by; 
                    $rs->updated_by = $updated_by;
                    $arrData[$key] = $rs;
                }

                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }

                return $data;
            } else {
                return array();
            }
        }


        private function get_searched_item($view, $query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder)
        { 
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.reason", "a.created_at", "a.created_by", "a.updated_at", "a.updated_by", "a.status");
                $sql = "*";
                $this->db->select($sql);
                $this->db->from("gcceforms.loa_invalid_reason a");
                if($query_builder){
                    $this->db->where($query_builder);
                }
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
                if($limit != -1){
                    $this->db->limit($limit, $offset);
                }
                $i = $sortOrder[0]['column'];
                //$this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {

                    $arrData = array();
                    foreach ($query->result() as $key => $rs) {
                        $rs->reason = $rs->reason; 
                        $rs->created_at = $rs->created_at; 
                        $rs->updated_at = $rs->updated_at; 
                        $rs->created_by = $rs->created_by; 
                        $rs->updated_by = $rs->updated_by; 
                        $arrData[$key] = $rs;
                    }

                    $data = array();
                    foreach ($arrData as $k => $v) {
                        $data[] = $v;
                    }

                    return $data;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        }

        private function get_searched_item_count($view, $query_builder=null, $search = null)
        {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.reason", "a.created_at", "a.created_by", "a.updated_at", "a.updated_by", "a.status");
                $sql = "*";
                $this->db->select($sql);
                $this->db->from("gcceforms.loa_invalid_reason a");
                if($query_builder){
                    $this->db->where($query_builder);
                }
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
                $query = $this->db->get();

                $rowCount = $query->num_rows();
            }

            return $rowCount;
        }
 
    

        function getArchiveRequest()
        {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"8", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
            $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
            $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
            
            $rowCount = 0;
            $rowData = array();
            if (!$search) {
                $rowData = $this->get_all_archive($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_archive_count();
            }

            if ($search) {
                $rowData = $this->get_searched_archive_item($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_archive_item_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_archive_count()
        {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $this->db->from("gcceforms.loa_invalid_reason");
            $this->db->where('date_from <= ', $check);
            $this->db->or_where_in('status', 'Cancelled');
            $query = $this->db->get();

            return $query->num_rows();
        }

        private function get_all_archive($limit = 10, $offset = 0, $sortBy, $sortOrder)
        { 
            $sql = "*";

            $this->db->select($sql);
            $this->db->from("gcceforms.loa_invalid_reason a");  
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            //$this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if($rs->created_at=="0000-00-00 00:00:00"){ $rs->created_at="";}else{$rs->created_at = $this->dateformat($rs->created_at);}
                    if($rs->updated_at=="0000-00-00 00:00:00"){ $rs->updated_at="";}else{$rs->created_at = $this->dateformat($rs->updated_at);}
                    if($rs->updated_by=="0"){
                        $updated_by="";
                    }else{ 
                        $user_data = $this->loa->employee_details($rs->updated_by);
                        $updated_by = $user_data->display_name;
                    }

                    $user_data = $this->loa->employee_details($rs->created_by);
                    $created_by = $user_data->display_name; 

                    $rs->reason = $rs->reason; 
                    $rs->created_at =  $rs->created_at; 
                    $rs->updated_at =  $rs->updated_at; 
                    $rs->created_by = $created_by; 
                    $rs->updated_by = $updated_by;
                    $arrData[$key] = $rs;
                }

                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }

                return $data;
            } else {
                return array();
            }
        }


        private function get_searched_archive_item($search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder)
        {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.reason", "a.created_at", "a.created_by", "a.updated_at", "a.updated_by", "a.status");
                $sql = "*";
                $this->db->select($sql);
                $this->db->from("gcceforms.loa_invalid_reason a");
                $this->db->group_start();

                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
                if($limit != -1){
                    $this->db->limit($limit, $offset);
                }
                $i = $sortOrder[0]['column'];
                //$this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {

                    $arrData = array();
                    foreach ($query->result() as $key => $rs) {
                        $rs->reason = $rs->reason; 
                        $rs->created_at = $rs->created_at; 
                        $rs->updated_at = $rs->updated_at; 
                        $rs->created_by = $rs->created_by; 
                        $rs->updated_by = $rs->updated_by;
                        $rs->updated_by = $rs->status; 
                        $arrData[$key] = $rs;
                    }

                    $data = array();
                    foreach ($arrData as $k => $v) {
                        $data[] = $v;
                    }

                    return $data;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        }

        private function get_searched_archive_item_count($search = null)
        {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.reason", "a.created_at", "a.created_by", "a.updated_at", "a.updated_by", "a.status");
                $sql = "*";
                $this->db->select($sql);
                $this->db->from("gcceforms.loa_invalid_reason a");
                $this->db->group_start();

                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
                $query = $this->db->get();

                $rowCount = $query->num_rows();
            }

            return $rowCount;
        }

         














        public function save($data)
        {
            $this->db->insert('gcceforms.loa_invalid_reason', $data);
            return $this->db->insert_id();
        }

        public function update($where, $data)
        {
            $this->db->update('gcceforms.loa_invalid_reason', $data, $where);
            return $this->db->affected_rows();
        } 
        
        private function get_count($table, $criteria)
        {
            $this->db->where($criteria);
            return $this->db->count_all_results($table);
        }

        /* function to converted $this->input->post() into STD Object */
        private function parseFormDataToObject($data)
        {
            $_data = json_encode($data, true);
            return json_decode($_data);
        } 
        
        private function dateformat($date){
            $date=date_create($date);
            return date_format($date,"Y/m/d h:i A");
        }
    }