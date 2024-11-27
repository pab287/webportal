<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Database_backup extends CI_Model{
	
	function __construct(){
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in"); 
		$this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model","dt_model");
        $this->load->model("core/upload_model", "file_upload");
        $this->dbutil = $this->load->dbutil($this, TRUE);

    }

    private function getUserData(){
        return $this->core_layout->getUserLoggedIn();
    }
    
    function databaseBackup(){
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy = (isset($post["sort"]) && $post["sort"])? $post["sort"]: null;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: "desc";
        
        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->get_database_post($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_database_post_count();
        }

        if($search){
            $rowData = $this->get_searched_database($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_database_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_database_post($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        $this->db->select("*");
        $this->db->from("gccconfiguration.dbbackup");
        $this->db->limit($limit, $offset);

        if($sortBy){
            $this->db->order_by($sortBy, $sortOrder);
        }else{
            $this->db->order_by("id", "DESC");
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $arrData[$key] = $rs;
            }
          
            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }
            return $data;
        }else{
            return array();
        }
    }

    private function get_database_post_count(){
        $this->db->from("gccconfiguration.dbbackup");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_database($search=null, $limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        if($search){
            $filterFields = array("filename", "filepath"); 
            $this->db->select("*");
            $this->db->from("gccconfiguration.dbbackup");
            $this->db->limit($limit, $offset);
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();
            if($sortBy){
                $this->db->order_by($sortBy, $sortOrder);
            }else{
                $this->db->order_by("id", "DESC");
            }
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $key => $rs){
                    $arrData[$key] = $rs;
                }
                $data = array();
                foreach($arrData as $k=>$v){
                    $data[] = $v;
                }
                return $data;
            }else{
                return array();
            }
        }else{
            return array();
        }
    }

    private function get_searched_database_count($search=null){
        $rowCount = 0;
        if($search){
            $filterFields = array("filename", "filepath"); 
            $this->db->select("*");
            $this->db->from("gccconfiguration.dbbackup");
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    function databaseLookup(){
        $get = $this->input->get();
        $dbs = $this->dbutil->list_databases();
        $resultarray = array();
        foreach($dbs as $_query){
            $data = array();
            $data["id"] = $_query;
            $data["text"] =  $_query;
            $resultarray[] = $data;
        }
        return array("results"=>$resultarray);
    }

    function saveManual(){
        $resultarray = array();
		$post = $this->input->post();

		$this->db = $this->db->db_select($post["database_name"]);
        $this->dbutil;
        $con=mysqli_connect("localhost","root","YgtDGdmoEn",$post["database_name"]);
        $sql = "SHOW TABLES FROM ".$post["database_name"];
        $result = mysqli_query($con,$sql);
        while ($row = mysqli_fetch_row($result)) {
            $rows[] = $row[0];
        }
        $prefs = array(
            'tables' => $rows,
            'format' => 'zip',
            'filename' => $post["database_name"],
            'add_drop' => TRUE,
            'add_insert' => TRUE 
        );
        $backup = $this->dbutil->backup($prefs); 
        
        $write=write_file('./uploads/database/'.$post["filename"].'.zip', $backup); 
        
        $query = $this->crud->insert(array("filename"=>$post["filename"],"filepath"=>"./uploads/database/".$post['filename'].".zip","type"=>"manual","status"=>"1"),"gccconfiguration.dbbackup");
        $resultset = array();
        if($query){
            $resultset["status"] = $query;
        }else{
            $resultset["status"] = FALSE;
        }

        if($resultset["status"]){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Backup successful!";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error processing request.";
        }
        return $resultarray;
    }

    function databaseDetails($id){
        $this->db->select('*');
        $this->db->from('gccconfiguration.dbbackup');
        $this->db->where('id',$id);
        $query = $this->db->get();
        return $query->row();
    }

    function deleteFile($id){
        return $this->db->query("DELETE FROM gccconfiguration.dbbackup WHERE id=$id");
    }
}
    