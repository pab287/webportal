<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Sss_table_model extends CI_Model {
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

            $insert = $this->db->insert("payroll.sss_table", $post);

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
            $filterClassification = (isset($post['filter_classification']) && $post['filter_classification']) ? $post['filter_classification'] : 1;

            $rowCount = 0;
            $rowData = array();
            $view_own_request = (in_array("view_own_request", $this->current_action)) ? true : false;
            
            $rowData = $this->get_all_post($view_own_request, $limit, $offset, $sortBy, $sortOrder, $search, $filterClassification);
            $rowCount = $this->get_all_post_count($view_own_request, $search, $filterClassification);
            
            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;
            return $resultset;
        }

        public function get_all_post_count($view, $search=null, $filterClassification){
            $filterFields = array("er", "ee", "ec_er", "ec_ee", "prov_ee", "prov_er");
            $this->db->from("payroll.sss_table");
            if($search){
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            }

            $this->db->where('classification', $filterClassification);
            $this->db->where('status', '1');
            $query = $this->db->get();
            return $query->num_rows();
        }

        public function get_all_post($view, $limit = 10, $offset = 0, $sortBy, $sortOrder, $search=null, $filterClassification){
            $filterFields = array("er", "ee", "ec_er", "ec_ee", "prov_ee", "prov_er");

            $this->db->select("*");
            $this->db->from("payroll.sss_table");
            if($search){
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            }
            $this->db->where('classification', $filterClassification);
            $this->db->where('status', '1');
            $this->db->order_by('from', 'asc');
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            $i = $sortOrder[0]['column'];
            // $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $result = array();
                foreach ($query->result() as $rs) {
                    $data = array();
                    $data["range"] = number_format($rs->from,2)."-".number_format($rs->to,2);
                    $data["er"]  = number_format($rs->er,2);
                    $data["ee"]  = number_format($rs->ee,2);
                    $data["ec_er"]  = number_format($rs->ec_er,2);
                    $data["ec_ee"]  = number_format($rs->ec_ee,2);
                    $data["prov_er"]  = number_format($rs->prov_er,2);
                    $data["prov_ee"]  = number_format($rs->prov_ee,2);
                    $data["emp_classification"] = ($rs->classification == 1) ? "Employed" : "Household";
                    $data["action"]  = '<div class="dropdown">
                                            <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown">
                                                <i class="la la-ellipsis-h"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
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
            $query = $this->db->query("SELECT * FROM payroll.sss_table WHERE `status` = 1 ORDER BY `to` ASC LIMIT 10");
            if($query->num_rows() > 0){
                foreach($query->result_array() as $_query){
                    $data = array();
                    $data["range"] = number_format($_query["from"],2)."-".number_format($_query["to"],2);
                    $data["er"]  = number_format($_query["er"],2);
                    $data["ee"]  = number_format($_query["ee"],2);
                    $data["ec_er"]  = number_format($_query["ec_er"],2);
                    $data["ec_ee"]  = number_format($_query["ec_ee"],2);
                    $data["prov_er"]  = number_format($_query["prov_er"],2);
                    $data["prov_ee"]  = number_format($_query["prov_ee"],2);
                    $data["action"]  = '<div class="dropdown">
                                            <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown">
                                                <i class="la la-ellipsis-h"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item btnEdit" data-id="'.$_query['id'].'" href="#"><i class="la la-edit"></i> Edit Details</a>
                                            </div>
                                        </div>';

                    $result[] = $data;
                }
            }

            return array("data"=>$result, "recordsTotal"=>$query->num_rows(), "recordsFiltered"=>$query->num_rows());
        }

        function getRangeData(){
            $post = $this->input->post();

            $query = $this->db->query("SELECT * FROM payroll.sss_table WHERE id = {$post['id']}");
            return $query->row_array();
        }

        function editRange(){
            $result = array();
            $post = $this->input->post();
            $id = $post["id"];
            unset($post["id"]);

            $update = $this->db->update("payroll.sss_table", $post, array("id"=>$id));

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