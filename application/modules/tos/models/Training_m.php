<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Training_m extends CI_Model {
    private $user_data = array();
    protected $tickets = "dbtraining";
    public function __construct()
	{
        parent::__construct();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model","dt_model");
        $this->user_data = $this->session->userdata("logged_in"); 
        $this->load->model("core/upload_model", "file_upload");
    }

    function categoryMasterfile($id){
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        
        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->get_all_post($id);
        }

        if($search){
            $rowData = $this->get_searched_item($id, $search);
        }

        $totalNotFiltered = $rowCount;
        $resultset["data"] = $rowData;
        return $resultset;
    }

    private function get_all_post($id){
        $sql = "*, SUM(filesize) as sum";
        $this->db->select($sql);
        $this->db->from("dbtraining.topic");
        $this->db->where("category_id",$id);
        $this->db->group_by("name");
   
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

    private function get_searched_item($search=null){
        if($search){
            $sql = "*";
            $filterFields = array("a.id", "a.name", "a.filename");
            $this->db->select($sql);
            $this->db->from("dbtraining.topic");
            $this->db->where("category_id",$id);
    
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
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

    function addCategory(){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'description' => $this->input->post('description'),
            'created_by' => $this->user_data['id'],
            'created_dt' => $date
        );         
        return $this->db->insert('dbtraining.category', $data);   
    }

    function deleteCategory($id){
        $this->db->query("DELETE FROM dbtraining.topic where category_id = '$id'");
        return $this->db->query("DELETE FROM dbtraining.category where id = '$id'");
    }

    function editCategory($id){
        $query = $this->db->query("SELECT * FROM dbtraining.category WHERE id='$id'");
        return $query->row();
    }

    function updateCategory($id){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'description' => $this->input->post('edit_description'),
            'modify_by' => $this->user_data['id'],
            'modify_dt' => $date,
        );
        if($id){
            $this->db->where('dbtraining.category.id', $id);
            return $this->db->update('dbtraining.category', $data);
        }
    }

    function topicMasterfile($name){
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        
        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->get_all_topic($name);
        }

        if($search){
            $rowData = $this->get_searched_topic($name, $search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_topic($id){
        $sql = "*";
        $this->db->select($sql);
        $this->db->from("dbtraining.topic a");
        $this->db->where("name",$id);
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

    private function get_searched_topic($id, $search=null){
        if($search){
            $sql = "*";
            $filterFields = array("a.name", "a.filename", "a.created_by", "a.created_dt", "a.category_id", "a.modify_dt");
            $this->db->select($sql);
            $this->db->from("dbtraining.topic a");
            $this->db->where("name",$id);
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
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

    function uploadFile(){
        $resultset = array();
        $employeeId = $this->user_data['emp_id'];
        if($employeeId){

        $filePath = "./uploads/files/trainings/empcode_{$employeeId}";

        $createFilePath = false;

        if (!file_exists($filePath)) {
            $mkdir = mkdir($filePath, 0777, true);
            if ($mkdir){ $createFilePath = true; }
        }else{ $createFilePath = true; }

        if($createFilePath == false){
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
            $resultset["toastr_state"] = "warning";
        }else{
            $config = array();
            $config['upload_path']          = $filePath;
            $config['allowed_types']        = '*';
            $config['max_size']             = 100000;
            $config['create_thumbnail']     = true;
            
            $session = $this->core_layout->getCurrentSession();
            $data = $this->file_upload->uploadFile($config);
            if($data["response"] == true){
               
                $files = $data["files"][0];
                $filename = $files["file_name"];
                if($filename){
                    $resultset["response"] = true;
                    $resultset["added_file"] = base_url("uploads/files/trainings/empcode_{$employeeId}/{$filename}");
                    $resultset["filename"] = $filename;
                    $resultset["filesize"] = filesize($filePath.'/'.$filename);
                    $resultset["render_file"] = $filename;
                    $resultset["toastr_msg"] = "Upload file successful.";
                    $resultset["toastr_state"] = "success";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "File upload to specific path failed!";
                    $resultset["toastr_state"] = "error";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "File upload failed!";
                $resultset["toastr_state"] = "error";
            }
        }
    }else{
        $resultset["response"] = false;
        $resultset["toastr_msg"] = "User data not found!";
        $resultset["toastr_state"] = "error";
    }
        return $resultset;
    }

    function addTopic($id){
        $value= $this->input->get();
        $filesize = $value['size']/1000;
        $date = date('Y-m-d H:i:s');
        $data = array(
            'name' => $value['name'],
            'filename' => $value['filename'],
            'filepath' => $value['path'],
            'filesize' => $filesize,
            'category_id' => $id,
            'created_by' => $this->user_data['id'],
            'created_dt' => $date
        );      
        return $this->db->insert('dbtraining.topic', $data);   
    }

    function deleteTopic($id){
        return $this->db->query("DELETE FROM dbtraining.topic where name = '$id'");
    }

    function editTopic($id){
        $query = $this->db->query("SELECT * FROM dbtraining.topic WHERE name='$id'");
        return $query->row();
    }

    function updateTopic($id){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'name' => $this->input->post('edit_topic_desc'),
            'modify_by' => $this->user_data['id'],
            'modify_dt' => $date,
        );
        if($id){
            $this->db->where('dbtraining.topic.name', $id);
            return $this->db->update('dbtraining.topic', $data);
        }
    }

    function updateFile($id){
        $value= $this->input->get();
        $filesize = $value['size']/1000;
        $date = date('Y-m-d H:i:s');
        $data = array(
            'filename' => $value['filename'],
            'filepath' => $value['path'],
            'filesize' => $filesize,
            'modify_by' => $this->user_data['id'],
            'modify_dt' => $date
        );      
        if($id){
            $this->db->where('dbtraining.topic.id', $id);
            return $this->db->update('dbtraining.topic', $data);
        }
    }

    function viewTopic($id){
        $this->db->select("*");
        $this->db->from("dbtraining.topic");
        $this->db->where("id",$id);
        $query = $this->db->get();
        $results = $query->row_array();
        return $results;
    }

    function treeview(){
        $row1 = [];
        $this->db->select("*");
        $this->db->from("dbtraining.category");
        $query = $this->db->get();
        $results = $query->result_array();

        foreach($results as $key => $value)
        {
           $row1[$key]['id'] = $value['id'];
           $row1[$key]['text'] = $value['description'];
           $row1[$key]['type'] = 'root';
          $row1[$key]['children'] = $this->membersTree($value['id']);
        }
  
        return $row1;
    }

    function membersTree($id){
        $row1 = [];
        $this->db->select("*");
        $this->db->from("dbtraining.topic");
        $this->db->where("category_id", $id);
        $this->db->group_by("name");
        $query = $this->db->get();
        $results = $query->result_array();

        foreach($results as $key => $value)
        {  
           $row1[$key]['id'] = $value['id'];
           $row1[$key]['text'] = $value['name'];
           $row1[$key]['type'] = 'root';
           $row1[$key]['children'] = $this->fileTree($value['name']);
        }
        return $row1;
    }

    function fileTree($id){
        
        $row1 = [];
        $this->db->select("*");
        $this->db->from("dbtraining.topic");
        $this->db->where("name", $id);
        $query = $this->db->get();
        $results = $query->result_array();

        foreach($results as $key => $value)
        {
           $id = $value['id'];
           $row1[$key]['subid'] = $value['id'];
           $row1[$key]['text'] = $value['filename'];
           $row1[$key]['type'] = 'child';
           $row1[$key]['data'] = $value['filepath'];
        }
        return $row1;
    }

    function addFile($name){
        $value= $this->input->get();
        $cat_id = $this->getCat_id($name);
        $filesize = $value['size']/1000;
        $date = date('Y-m-d H:i:s');
        $data = array(
            'name' => $name,
            'filename' => $value['filename'],
            'filepath' => $value['path'],
            'filesize' => $filesize,
            'category_id' => $cat_id->category_id,
            'created_by' => $this->user_data['id'],
            'created_dt' => $date
        );      
        return $this->db->insert('dbtraining.topic', $data);   
    }

    function getCat_id($name){
        $this->db->select("*");
        $this->db->from("dbtraining.topic");
        $this->db->where("name",$name);
        $query = $this->db->get();
        $results = $query->row();
        return $results;
    }

    function deleteFile($id){
        return $this->db->query("DELETE FROM dbtraining.topic where id = '$id'");
    }

    function editFile($id){
        $query = $this->db->query("SELECT * FROM dbtraining.topic WHERE id='$id'");
        return $query->row();
    }

    function getDetails($id){
        $query = $this->db->query("SELECT a.*, b.description, CONCAT(c.firstname,' ',c.lastname) as created_name,(SELECT CONCAT(c.firstname,' ',c.lastname) as modify_name FROM gccmaster.tblemployees a, dbtraining.topic b WHERE a.id=b.modify_by && b.id='$id') as modify_name FROM dbtraining.topic a, dbtraining.category b, gccmaster.tblemployees c WHERE c.id=a.created_by && b.id=a.category_id && a.id='$id'");
        return $query->row();
    }

    function getCat($id){
        $query = $this->db->query("SELECT * FROM dbtraining.category WHERE id='$id'");
        return $query->row();
    }

    function chartData(){
        $this->db->select("a.description, COUNT(b.filename) AS topic");  
        $this->db->from("dbtraining.category a");
        $this->db->join("dbtraining.topic b", "a.id=b.category_id");
        $this->db->group_by("a.description");
        $query = $this->db->get()->result();
        return json_decode(json_encode($query));
    }

}