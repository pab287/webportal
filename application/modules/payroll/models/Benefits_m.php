<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Benefits_m extends CI_Model {
        function __construct() {
            parent::__construct();
            $this->user_data = $this->session->userdata("logged_in");
            date_default_timezone_set("Asia/Manila");
        }

        function masterfile() {
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $order_val = array(array("column" => "1", "dir" => "desc"));

            $post = $this->input->post();
          
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
          
            $rowData = $this->masterfile_list($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->masterfile_count($search);

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function masterfile_list($search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $filterFields = array("a.code", "a.benefit_name");
            $this->db->select("a.*");
            $this->db->from('payroll.benefits a');
            $this->db->where('is_archive',0);
    
            if(isset($search)){
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
            
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
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

        private function masterfile_count($search=null) {
            $filterFields = array("a.code", "a.benefit_name");
            $this->db->select("a.*");
            $this->db->from('payroll.benefits a');
            $this->db->where('is_archive',0);

            if(isset($search)){
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

            $query = $this->db->get();
            $count = $query->num_rows();
            return $count;
        }

        function archiveList() {
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $order_val = array(array("column" => "1", "dir" => "desc"));

            $post = $this->input->post();
          
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
          
            $rowData = $this->archive_masterfile_list($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->archive_count($search);

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function archive_masterfile_list($search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $filterFields = array("a.code", "a.benefit_name");
            $this->db->select("a.*");
            $this->db->from('payroll.benefits a');
            $this->db->where('is_archive',1);
    
            if(isset($search)){
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
            
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
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

        private function archive_count($search=null) {
            $filterFields = array("a.code", "a.benefit_name");
            $this->db->select("a.*");
            $this->db->from('payroll.benefits a');
            $this->db->where('is_archive',1);

            if(isset($search)){
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

            $query = $this->db->get();
            $count = $query->num_rows();
            return $count;
        }

        function save(){
            $result = array();
            $date = date('Y-m-d H:i:s');
            $post = $this->input->post();

            $getCode = $this->getCode($post['code']);
            if($getCode == 0){
                $data = array(
                    "code" => $post['code'],
                    "benefit_name" => $post['name'],
                    "created_by" => $this->user_data['emp_id'],
                    "created_at" => $date     
                );
                $query = $this->db->insert('payroll.benefits', $data);

                if($query){
                    $result['toastr_msg']="Benefit has been created";
                    $result['state'] = true;
                }else{
                    $result['toastr_msg']="Error saving data";
                    $result['state'] = false;
                }
            }else{
                $result['toastr_msg']="Code already exist.";
                $result['state'] = false;
            }
        
            return $result;
        }

        function edit($id){
            $this->db->select("*");
            $this->db->from("payroll.benefits");
            $this->db->where("id", $id);
            $query = $this->db->get();
            $result = $query->row_array();

            return $result;
        }

        function update($id){
            $result = array();
            $date = date('Y-m-d H:i:s');
            $post = $this->input->post();

            $getCode = $this->getCode($post['code'], $id);
            if($getCode == 0){
                $data = array(
                    "code" => $post['code'],
                    "benefit_name" => $post['name'],
                    'updated_by' => $this->user_data['emp_id'],
                    'updated_at' => $date,
                );

                if($id){
                    $this->db->where('benefits.id', $id);
                    $query = $this->db->update('payroll.benefits', $data);

                    if($query){
                        $result['toastr_msg']="Benefit has been updated";
                        $result['state'] = true;
                    }else{
                        $result['toastr_msg']="Error updating data";
                        $result['state'] = false;
                    }
                }else{
                    $result['toastr_msg']="No data found";
                    $result['state'] = false;
                }
           
            }else{
                $result['toastr_msg']="Code already exist.";
                $result['state'] = false;
            }   
            
            return $result;
        }

        function delete($id) {
            $result = array();
            $date = date('Y-m-d H:i:s');
            $data = array(
                "is_archive" => 1,
                'archived_by' => $this->user_data['emp_id'],
                'archived_at' => $date,
            );
            $this->db->where('benefits.id', $id);
            $query = $this->db->update('payroll.benefits', $data);

            if($query){
                $result['toastr_msg']="Benefit has been archived";
                $result['state'] = true;
            }else{
                $result['toastr_msg']="Error removing data";
                $result['state'] = false;
            }

            return $result;
        }

        function restore($id) {
            $result = array();
            $date = date('Y-m-d H:i:s');
            $data = array(
                "is_archive" => 0,
                'archived_by' => "",
                'archived_at' => "",
            );
            $this->db->where('benefits.id', $id);
            $query = $this->db->update('payroll.benefits', $data);

            if($query){
                $result['toastr_msg']="Benefit has been retored";
                $result['state'] = true;
            }else{
                $result['toastr_msg']="Error removing data";
                $result['state'] = false;
            }

            return $result;
        }

        public function getCode($code, $id=null){
            $this->db->select("*");
            $this->db->from("payroll.benefits");
            $this->db->where("code", $code);
            if($id){
                $this->db->where_not_in("id", $id);
            }
            $query = $this->db->get();
            return $query->num_rows();
        }
    }