<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Logs_model extends CI_Model{

	function __construct(){
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
		$this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model","dt_model");
        $this->load->model("core/upload_model", "file_upload");
    }

    function getModuleCollection(){
        $result = array();
        $this->db->select("*");
        $this->db->from("gccmaster.modules");
        $this->db->where("status", 1);
        $this->db->where("database !=", null);
        $query = $this->db->get();

        if($query->num_rows() > 1):
            foreach($query->result_array() as $_moduleCollection):
                $data = array();
                $data["id"] = $_moduleCollection["id"];
                $data["name"] = strtoupper($_moduleCollection["name"]);
                $data["database"] = $_moduleCollection["database"];
                $data["description"] = $_moduleCollection["description"] != "" ? $_moduleCollection["description"] : $_moduleCollection["label"];
                $result[] = $data;
            endforeach;
        endif;

        return $result;
    }

    function getEventLogs(){
        $resultset = array();
        $minusOneYear = date("Y-m-d H:i:s", strtotime("-1 year"));

        $post = $this->input->post();
        if(isset($post["database"]) && $post["database"]):
            $this->db->select("a.*, b.lastname, b.firstname, b.middlename, b.suffix, b.pic_filename");
            $this->db->from($post["database"].".".$post['log_type']."_logs_event a");
            $this->db->join("gccmaster.tblemployees b", "b.id = a.user_id", "LEFT");
            $this->db->where("a.created_at >=", $minusOneYear);
            $this->db->order_by("a.id", "DESC");
            $query = $this->db->get();
            if($query->num_rows() > 0){
                $_tempData = array();
                /*** $resultset["status"] = TRUE;
                $resultset["data"] = array("data"=>$query->result_array()); ***/
                foreach ($query->result_array() as $key => $value) {
                    $tempDisplayName = "NO USER DATA";
                    $tempImage = base_url("assets/images/profile/no_image.jpg");
                    if($value["user_id"]){
                        $tempData = $this->core_layout->getDisplayName($value);
                        $tempDisplayName = $tempData["display_name_0"];
                        if(file_exists(realpath("./uploads/files/images/employee_files/empcode_{$value['user_id']}/thumbnails/{$value['pic_filename']}"))){
                            $tempImage = base_url("uploads/files/images/employee_files/empcode_{$value['user_id']}/thumbnails/{$value['pic_filename']}");
                        }
                    }
                    $value["display_image"] = $tempImage;
                    $value["display_name"] = $tempDisplayName;
                    $value["created_date"] = date("F d, Y h:i:s A", strtotime($value["created_at"]));
                    $_tempData[$key] = $value;
                }
                $resultset["status"] = TRUE;
                $resultset["data"] = array("data"=>$_tempData);
            }else{
                $resultset["status"] = FALSE;
                $resultset["data"] = array("data"=>array());
            }
        else:
            $resultset["status"] = FALSE;
            $resultset["data"] = array("data"=>array());
        endif;
        return $resultset;
    }



}

