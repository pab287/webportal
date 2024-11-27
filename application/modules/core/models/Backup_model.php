<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Backup_model extends CI_Model{
	protected $backupTable = "backup_database";
	
	function __construct(){
		parent::__construct();
		$this->load->model("core/datatable_model","dt_model");
		$this->load->model("core/upload_model","adm_upload");
		
		$this->load->dbutil();
		$this->load->helper('download');
	}
	
	function getBackupDatabseList(){
		$post = $this->input->post();
		if($post){
			$columns = array("id", "filename", "created_by", "created_date");
			$dir = $post["order"][0]["dir"];
			$order = $columns[$post["order"][0]["column"]];
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$dtBackup = $this->dt_model->dataTable();
			$dtBackup->setTable($this->backupTable);
			$dtBackup->setParameterFields($columns);
			
			$totalData = $dtBackup->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $dtBackup->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $dtBackup->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $dtBackup->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
					$userData = $this->core_layout->getUserData($pst->created_by);
					$displayName = (isset($userData["display_name_1"]) && $userData["display_name_1"])? $userData["display_name_1"]: "---";
					
					$nestedData['id'] = $pst->id;
					$nestedData['filename'] = $pst->filename;
					$nestedData['created_by'] = $displayName;
					$nestedData['created_date'] = date("F d, Y h:i A", strtotime($pst->created_date));
					$data[] = $nestedData;
				}
			}
			$json_data = array(
                    "draw" => intval($draw),  
                    "recordsTotal" => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,   
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
	
	function downloadBackup($id=null){
		$fileToDownload = "";
		if($id){
			$query = $this->db->get_where($this->backupTable, array("id"=>$id));
			if($query->num_rows() == 1){
				$row = $query->row();
				
				$filePath = ($row->filepath)? $row->filepath: "./uploads/files/databases/sql/";
				$fileName = $row->filename;
				if($fileName){
					$fileToDownload = "{$filePath}{$fileName}";
				}
			}
		}
			
		return $fileToDownload;
	}
	function createBackup(){
		$resultset = array();
		$dateTime = date("YmdHis");
		$filename = "backup_database-{$dateTime}.sql";
		
		$prefs = array(
			'ignore'=> array(),
			'format'=> 'txt',
			'filename'=> $filename,
			'add_drop'=> TRUE, 
			'add_insert'=> TRUE, 
			'newline'=> "\n"
		);
		
		$backup = $this->dbutil->backup($prefs);
		$written = write_file("./uploads/files/databases/sql/{$filename}", $backup);
		if($written){
			$session = $this->core_layout->getCurrentSession();
			$empId = $session["emp_id"];
			
			$data = array();
			$data["filename"] = $filename;
			$data["filepath"] = "./uploads/files/databases/sql/";
			$data["created_by"] = $empId;
			
			$insert = $this->db->insert($this->backupTable, $data);
			if($insert){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Backup database successful, filename `{$filename}`";				
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Error in saving the database record, filename `{$filename}`!";				
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "failed to backup database!";
		}
		
		return $resultset;
	}
	
	function createBackupStocks(){
		$dateTime = date("YmdHis");
		$filename = "backup_database_stocks-{$dateTime}.sql";
		
		$prefs = array(
			'tables'=> array('items', 'issuance', 'issuance_contents', 'receiving', 'receiving_contents'),
			'ignore'=> array(),
			'format'=> 'txt',
			'filename'=> $filename,
			'add_drop'=> TRUE, 
			'add_insert'=> TRUE, 
			'newline'=> "\n"
		);
		
		$backup = $this->dbutil->backup($prefs);
		$written = write_file("./uploads/files/databases/sql/{$filename}", $backup);
		
		if($written){
			$session = $this->core_layout->getCurrentSession();
			$empId = $session["emp_id"];
			
			$data = array();
			$data["filename"] = $filename;
			$data["filepath"] = "./uploads/files/databases/sql/";
			$data["created_by"] = $empId;
			
			$insert = $this->db->insert($this->backupTable, $data);
			if($insert){
				$uploadFile = $this->adm_upload->uploadFileCurl($this->db->insert_id(), md5("direct_access"));
				$resultset["response"] = true;
				$resultset["uploaded"] = $uploadFile;
				$resultset["toastr_msg"] = "Remote client database successful, filename `{$filename}`";				
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Error remote client database transfer of `{$filename}`!";				
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "failed to remote client database access!";
		}
		
		return $resultset;
	}
}