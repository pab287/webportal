<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Gcctime_location_model extends CI_Model {
        private $userAppDB = "gcctimeutility.app_users";
        private $employeeDB = "gccmaster.tblemployees";
        private $positionDB = "gcchris.tblposition";

        public function getAppUsers(){
            $post = $this->input->post();
            $this->db->select("b.id, b.pic_filename, a.status, CONCAT(b.firstname,' ', b.lastname,' ', b.suffix) as name, IFNULL(c.name,b.position) as position");
            $this->db->from($this->userAppDB.' as a');
            $this->db->join($this->employeeDB. ' as b', 'a.emp_id = b.id');
            $this->db->join($this->positionDB. ' as c', 'c.id = b.position', "LEFT");
            if(isset($post['search']) && $post['search'] != null){
                $search_data = $post['search'];
                $search = array();
                $search["field"] = "CONCAT(b.firstname,' ', b.lastname,' ', b.suffix)";
                $search["key"] = $search_data;
                $search["option"] = "BOTH";
                $this->db->like($search["field"], $search["key"], $search["option"]);
            }
            $all_user = $this->db->get();
            $data = $all_user->result_array();
            $count = $all_user->num_rows();
            if($count > 0){
                foreach($data as $key => $result){

                    $tempImage = base_url("assets/images/profile/no_image.jpg");
                    if(file_exists(realpath("./uploads/files/images/employee_files/empcode_{$result['id']}/{$result['pic_filename']}"))){
                        $tempImage = base_url("uploads/files/images/employee_files/empcode_{$result['id']}/{$result['pic_filename']}");
                    }
                    $result['temp_file_name'] = $result['pic_filename'];
                    $result['pic_filename'] = $tempImage;
                    
                    $dd = $this->core_layout->getEmployeeData($result['id']);
                    if(isset($dd['display_name_1']) && $dd['display_name_1']){
                        $result['display_name'] = $dd['display_name_1'];
                    }else{
                        $result['display_name'] = "No Assigned Name";
                    }
                    $data[$key] = $result;
                }
                return $data;
            }else{
                return 0;
            }
        }

        public function getUsersLocation(){
            $post = $this->input->post();
            if(isset($post['emp_id']) && $post['emp_id'] != null){
                $emp_id = $post['emp_id'];
                return $this->user_locations($emp_id);
            } 
        }

        
        private function user_locations($emp_id){
            $_data_res = array();
            $file = FCPATH ."/androidapp/storage/logs/location/".$emp_id.".log";
            if(file_exists($file)){
                $log = file_get_contents(FCPATH ."/androidapp/storage/logs/location/".$emp_id.".log");
            
                if (strpos(file_get_contents(FCPATH ."/androidapp/storage/logs/location/".$emp_id.".log"), 'location')) {
                    $chop = str_replace(array("\n", "\r"), '', $log);
                    $log_data = explode("%", $chop);
                    $data_array = array();
                    foreach($log_data as $logs => $val){
                        $ex_arr = array();
                        $chop1 = str_replace(array("\n", "\r"), '', $val);
                        $exp_data = explode("@",$chop1);
                        foreach($exp_data as $x => $i){
                            if($i != ""){
                                $ex_arr[$x] = $i;
                            }
                        }
                        if(count($ex_arr) > 0){
                            $data_array[] = $ex_arr; 
                        }
                    }
     
                    foreach($data_array as $data_arr){
                        $key_val = array();
                        foreach($data_arr as $_da){
                            $chop2 = str_replace(array("\n", "\r"), '', $_da);
                            $_result = explode("=", $chop2);
                            if($_result[0] == "location"){
                                $remove = substr($_result[1], 0, -1);
                                $key_val[$_result[0]] = json_decode($remove);
                            }else if($_result[0] == "user_locations"){
                                if($_result[1] != ""){
                                    $key_val[$_result[0]] = json_decode($_result[1]);
                                }else{
                                    $key_val[$_result[0]] = null;
                                }
                            }else{
                                $key_val[$_result[0]] = $_result[1];
                            }
                        }
                        $_data_res["data"][] = $key_val;
                    }
                    $_data_res["status"] = 1;
                    return $_data_res;
                }else{
                    $_data_res["status"] = 0;
                    return $_data_res;
                }
            }else{
                $_data_res["status"] = 0;
                return $_data_res;
            }
        }
    } 