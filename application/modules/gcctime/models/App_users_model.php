<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class App_users_model extends CI_Model {
        
        public function all_users_login(){
            $post = $this->input->post();
            $resultarray = array();
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $filterFields1 = array("b.firstname", "b.lastname", "a.device_name", "a.device_id", "a.user_imei");
            
            $this->db->limit($limit, $offset);
            $this->db->group_by("a.id");
            $this->db->order_by('a.id', 'DESC');
            $this->db->select("b.firstname, b.lastname, a.device_name, a.device_id, a.user_imei, a.emp_id, a.status, a.id");
            $this->db->from("gcctimeutility.app_users a");
            $this->db->join("gccmaster.tblemployees b","b.id = a.emp_id", "LEFT");
            if ($search) {
                $this->db->group_start();
                foreach ($filterFields1 as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
            }
            $query = $this->db->get();
            $rowCount = $query->num_rows();
            if($rowCount > 0){
                foreach($query->result_array() as $_query){
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["employee_name"] = mb_strtoupper($this->get_fullname($_query["emp_id"]));
                    $data["device_name"] = $_query["device_name"];
                    $data["device_id"] = $_query["device_id"];
                    $data["status"] = $_query["status"];
                    //$data[] = $this->actions($_query["id"]);
                    $resultarray[] = $data;
                }
            }

            $count = $this->user_count($search, $limit, $offset, $filterFields1);
            $resultset = array();
            $resultset["recordsTotal"] = $count;
            $resultset["recordsFiltered"] = $count;
            $resultset["data"] = $resultarray;
            return $resultset;
        }

        public function delete_app_user(){
            $post = $this->input->post();
            $app_user_id = $post['app_user_id'];
            if(isset($post['app_user_id']) && $app_user_id != null){
                $this->db->where('id', $app_user_id);
                $del = $this->db->delete("gcctimeutility.app_users");
                if($del){
                    return 1;
                }else{
                    return 0;
                }
            }
        }

        public function signout_app_user(){
            $post = $this->input->post();
            $app_user_id = $post['app_user_id'];
            if(isset($post['app_user_id']) && $app_user_id != null){
                $array = array();
                $array["status"] = 1;
                $this->db->where('id', $app_user_id);
                $query = $this->db->update("gcctimeutility.app_users",$array);
                if($query){
                    return 1;
                }else{
                    return 0;
                }
            }
        }

        private function user_count($search=null, $limit = 10, $offset = 0, $filterFields1 = []){
            $this->db->group_by("a.id");
            $this->db->order_by('a.id', 'DESC');
            $this->db->select("b.firstname, b.lastname, a.device_name, a.device_id, a.user_imei, a.emp_id, a.status, a.id");
            $this->db->from("gcctimeutility.app_users a");
            $this->db->join("gccmaster.tblemployees b","b.id = a.emp_id", "LEFT");
            if ($search) {
                $this->db->group_start();
                foreach ($filterFields1 as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
            }
            $query = $this->db->get();
            $rowCount = $query->num_rows();
            return $rowCount;
        }

        private function get_fullname($emp_id){
            $dd = $this->core_layout->getEmployeeData($emp_id);
            if(isset($dd['display_name_1']) && $dd['display_name_1']){
                $result['display_name'] = $dd['display_name_1'];
            }else{
                $result['display_name'] = "No Assigned Name";
            }

            return $result['display_name'];
        }

        private function actions($id){
            $_actions = "";
            $_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDelete btn-delete' onclick='delete_app_user(".$id.")' title='Delete'><i class='la la-trash-o'></i></a>";
            return $_actions;
        }

    } 