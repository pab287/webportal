<?php defined('BASEPATH') OR exit('No direct script access allowed');
    class Payroll_type_m extends CI_Model {
        protected $payrollIncentiveTable = "payroll.payroll_type_incentive";
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
            $filterFields = array("a.code", "a.name", "a.status", "a.created_at", "a.created_by");
            $this->db->select("a.*");
            $this->db->from('payroll.payroll_type a');
    
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
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("d.contractor", $sortOrder[0]['dir']);
            } else {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if($rs->status == 0 ){
                        $rs->status = '<span class="m-badge m-badge--danger m-badge--wide m-badge--rounded">Inactive</span>';
                    }else{
                        $rs->status = '<span class="m-badge m-badge--success m-badge--wide m-badge--rounded">Active</span>';
                    }
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
            $filterFields = array("a.code", "a.name", "a.status", "a.created_at", "a.created_by");
            $this->db->select("a.*");
            $this->db->from('payroll.payroll_type a');

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
                    "name" => $post['name'],
                    "status" => $post['status'],
                    "created_by" => $this->user_data['emp_id'],
                    "created_at" => $date     
                );
                $query = $this->db->insert('payroll.payroll_type', $data);
    
                if($query){
                    $result['toastr_msg']="Payroll type has been created";
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
            $this->db->from("payroll.payroll_type");
            $this->db->where("id", $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        function update($id){
            $result = array();
            $date = date('Y-m-d H:i:s');
            $post = $this->input->post();

            $getCode = $this->getCode($post['code'], $id);
            if($getCode == 0){
                $data = array(
                    "code" => $post['code'],
                    "name" => $post['name'],
                    "status" =>  $post['status'],
                    'updated_by' => $this->user_data['emp_id'],
                    'updated_at' => $date,
                );

                if($id){
                    $this->db->where('payroll_type.id', $id);
                    $query = $this->db->update('payroll.payroll_type', $data);

                    if($query){
                        $result['toastr_msg']="Payroll type has been updated";
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

        public function getCode($code, $id=null){
            $this->db->select("*");
            $this->db->from("payroll.payroll_type");
            $this->db->where("code", $code);
            if($id){
                $this->db->where_not_in("id", $id);
            }
            $query = $this->db->get();
            return $query->num_rows();
        }

        public function getIncentivePayrollTypeDataTableRequest(){
            $post = $this->input->post();
            if($post){
                $columns = array("name", "description", "date_from", "date_to", "is_active", "id");
                $dir = $post["order"][0]["dir"];
                $order = $columns[$post["order"][0]["column"]];
                $draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
                $start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
                $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
                
                $dtTemp = $this->dt_model->dataTable();
                $dtTemp->setTable($this->payrollIncentiveTable);
                $dtTemp->setParameterFields($columns);
                
                $parameters = array();
                $parameters["is_archived"] = 0;
                $dtTemp->setWhereParameters($parameters);
                
                $totalData = $dtTemp->dtAllPostsCount();
                $totalFiltered = $totalData;
                
                if(empty($searchValue)){            
                    $posts = $dtTemp->dtAllPosts($limit, $start, $order, $dir);
                }else {
                    $posts = $dtTemp->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTemp->dtPostSearchCount($searchValue);
                }
                
                $data = array();
                if(!empty($posts)){
                    foreach ($posts as $pst){
                        $nestedData['id'] = $pst->id;
                        $nestedData['name'] = $pst->name;
                        $nestedData['description'] = $pst->description;
                        $nestedData['date_from'] = date("F, d Y", strtotime($pst->date_from));
                        $nestedData['date_to'] = date("F, d Y", strtotime($pst->date_to));
                        $nestedData['is_active'] = $pst->is_active;
                        $data[] = $nestedData;
                    }
                }
                $json_data = array(
                        "draw" => intval($draw),  
                        "recordsTotal" => intval($totalData),  
                        "recordsFiltered" => intval($totalFiltered), 
                        "data" => $data,
                        );
                
                return $json_data;
            }else{
                return array(
                    "draw"=>1,
                    "recordsTotal"=>0,
                    "recordsFiltered"=>0,
                    "data"=>array(),
                    );
            }
        }

        public function setPayrollIncentiveType(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                $tempYear = date("Y");
                $post["name"] = strtolower($post["name"]);
                $post["description"] = strtoupper($post["description"]);
                $tempDateFrom = date("Y-m-d", strtotime("{$post['date_from']}, {$tempYear}"));
                $tempDateTo = date("Y-m-d", strtotime("{$post['date_to']}, {$tempYear}"));
                
                $post["date_from"] = $tempDateFrom;
                $post["date_to"] = $tempDateTo;
                $post["created_by"] = $this->user_data["emp_id"];
                $post["created_at"] = date("Y-m-d H:i:s");
                $added = $this->db->insert($this->payrollIncentiveTable, $post);
                if($added){
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Payroll incentive type has been added successfully.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to add new payroll incentive type!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }
            return $resultset;
        }

        public function updatePayrollIncentiveType(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                $where = array("id"=>$post["id"]);
                unset($post["id"]);

                $tempYear = date("Y");
                $tempDateFrom = date("Y-m-d", strtotime("{$post['date_from']}, {$tempYear}"));
                $tempDateTo = date("Y-m-d", strtotime("{$post['date_to']}, {$tempYear}"));
                
                $post["name"] = strtolower($post["name"]);
                $post["description"] = strtoupper($post["description"]);
                $post["date_from"] = $tempDateFrom;
                $post["date_to"] = $tempDateTo;
                $post["updated_by"] = $this->user_data["emp_id"];
                $post["updated_at"] = date("Y-m-d H:i:s");
                $updated = $this->db->update($this->payrollIncentiveTable, $post, $where);
                if($updated && $this->db->affected_rows() > 0){
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Payroll incentive type has been updated successfully.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update payroll incentive type!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }
            return $resultset;
        }

        public function archivePayrollIncentiveType(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post) && $post){
                $archive = $this->db->update($this->payrollIncentiveTable, array("is_archived"=>1), $post);
                if($archive && $this->db->affected_rows() == 1){
                    $this->core_layout->insertArchiveLog($this->payrollIncentiveTable, $post["id"]);
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Payroll incentive type has been archived successfully.";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to archive payroll incentive type!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }
            return $resultset;
        }
        public function getPayrollIncentiveType($id=null){
            $resultset = array();
            if($id){
                $this->db->select("*, DATE_FORMAT(date_from, '%M %d') as dt_from, DATE_FORMAT(date_to, '%M %d') as dt_to");
                $query = $this->db->get_where($this->payrollIncentiveTable, array("id"=>$id));
                if($query->num_rows() == 1){
                    $resultset["response"] = true;
                    $resultset["data"] = $query->row();
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
            return $resultset;
        }
    }