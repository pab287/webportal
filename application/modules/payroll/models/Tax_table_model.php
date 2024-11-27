<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Tax_table_model extends CI_Model {
        protected $loggedUser;
        protected $loggedUserName;

        function __construct() {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->loggedUser = $this->session->userdata('logged_in');
            $this->loggedUserName = $this->loggedUser["firstname"] . " " . $this->loggedUser["lastname"];
            $this->current_action = $this->core_layout->getCurrentActions();
        }

        function saveRange(){
            $post = $this->input->post();
            $result = array();

            $insert = $this->db->insert("payroll.tax_table", $post);
            
            if($insert){
                $result["response"] = true;
                $result["toastr_msg"] = "Range has been saved.";
            }else{
                $result["response"] = false;
                $result["toastr_msg"] = "Error processing request.";
            }

            return $result;
        }

        function getDatatableRequest()
        {
            
            $order_val = array(array("column"=>"0", "dir"=>"desc"));
  
            $resultset = array();
            $post = $this->input->post();
            $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
            $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
            $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;

            $rowCount = 0;
            $rowData = array();
            $view_own_request = (in_array("view_own_request", $this->current_action)) ? true : false;
            
            $rowData = $this->get_all_post($view_own_request, $limit, $offset, $sortBy, $sortOrder, $search);
            $rowCount = $this->get_all_post_count($view_own_request, $search);
            
            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;
            return $resultset;
        }

        public function get_all_post_count($view, $search=null){
            $filterFields = array("payroll_sched", "cr_from", "cr_to", "pwt_initial", "pwt_percentage", "pwt_over");
            $this->db->from("payroll.tax_table");
            if($search){
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            }
            $query = $this->db->get();
            return $query->num_rows();
        }

        public function get_all_post($view, $limit = 10, $offset = 0, $sortBy, $sortOrder, $search=null){
            $filterFields = array("payroll_sched", "cr_from", "cr_to", "pwt_initial", "pwt_percentage", "pwt_over");

            $this->db->select("*");
            $this->db->from("payroll.tax_table");
            if($search){
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            }
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            $i = $sortOrder[0]['column'];
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $result = array();
                foreach ($query->result() as $rs) {
                    $data = array();
                    $data["payroll_sched"] = $rs->payroll_sched;
                    $data["compensation_level"] = $rs->compensation_level;
                    $data["compensation_range"] = "P".number_format($rs->cr_from,2)." - P".number_format($rs->cr_to,2);
                    $data["prescribed_witholding_tax"] = number_format($rs->pwt_initial,2)." + ".$rs->pwt_percentage."% over ".number_format($rs->pwt_over,2);
                    $data["action"] = '<div class="dropdown">
                                            <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown">
                                                <i class="la la-ellipsis-h"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-bottom">
                                                <a class="dropdown-item btnEdit" data-id="'.$rs->id.'" href="#"><i class="la la-edit"></i> Edit Details</a>
                                            </div>
                                        </div>';

                    $result[] = $data;
                }
                return $result;
            } else {
                return array();
            }
        }

        function getRange(){
            $result = array();

            $query = $this->db->query("SELECT * FROM payroll.tax_table");

            if($query->num_rows() > 0){
                foreach($query->result_array() as $_query){
                    $data = array();

                    $data["payroll_sched"] = $_query["payroll_sched"];
                    $data["compensation_level"] = $_query["compensation_level"];
                    $data["compensation_range"] = "P".number_format($_query["cr_from"],2)." - P".number_format($_query["cr_to"],2);
                    $data["prescribed_witholding_tax"] = number_format($_query["pwt_initial"],2)." + ".$_query["pwt_percentage"]."% over ".number_format($_query["pwt_over"],2);
                    $data["action"] = '<div class="dropdown">
                                            <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown">
                                                <i class="la la-ellipsis-h"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-bottom">
                                                <a class="dropdown-item btnEdit" data-id="'.$_query['id'].'" href="#"><i class="la la-edit"></i> Edit Details</a>
                                            </div>
                                        </div>';

                    $result[] = $data;
                }
            }

            return array("data"=>$result);

        }

        function getRangeData(){
            $post = $this->input->post();
            
            $query = $this->db->query("SELECT * FROM payroll.tax_table WHERE id = {$post['id']}");
            return $query->row_array();
        }

        function editRange(){
            $result = array();
            $post = $this->input->post();
            $id = $post["id"];
            unset($post["id"]);
            $update = $this->db->update("payroll.tax_table", $post, array("id"=>$id));

            if($update){
                $result["response"] = true;
                $result["toastr_msg"] = "Range has been saved.";
            }else{
                $result["response"] = false;
                $result["toastr_msg"] = "Error processing request.";
            }

            return $result;
        }

    }