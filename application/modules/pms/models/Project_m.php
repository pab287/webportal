<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Project_m extends CI_Model{
    protected $itemTable = "gccpms.sf_item";
    protected $woTypeTable = "gccpms.sf_wo_type";
    protected $taskTable = "gccpms.sf_task";
    protected $projectTable = "gccpms.sf_project";
    protected $projectCompanyTable = "gccpms.sf_project_company";
    protected $projectLocationTable = "gccpms.sf_project_location";
    protected $projectUnitTable = "gccpms.sf_project_unit";
	protected $checklistItemTable = "gccpms.sf_checklist_item";
	
    protected $archiveTable = "gccmaster.archived_items";

	public function __construct(){
        parent::__construct();
		$this->load->model("task_m", "adm_task");
		$this->user_data = $this->session->userdata("logged_in");
        date_default_timezone_set("Asia/Manila");
    }

	function getProjectUnitDatatableRequest(){
		$post = $this->input->post();
		if($post){
			$projectId = (isset($post["project_id"]) && $post["project_id"])? $post["project_id"]: 0;
            $orderx = (isset($post["order"]) && $post["order"])? $post["order"]: false;
            $columns = array("a.code", "a.description", "a.block", "a.lot", "a.road_lot", "b.label", "a.sf_status", "a.is_active", "a.id");
            $dir = "DESC";
			$order = "id";
			if($orderx){
				$dir = $orderx[0]["dir"];
				$order = $columns[$orderx[0]["column"]];
			}
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$tempTable = $this->dt_model->dataTable();
			$tempTable->setTable($this->projectUnitTable);
			$tempTable->setTableAlias("a");
			
			$tempTable->setParameterFields($columns);
			
			$joinTable = array();
			$joinTable["table"][$this->checklistItemTable] = "b";
			$joinTable["fields"][] = "b.id=a.checklist_id";
			$joinTable["field_loc"][] = "LEFT";
			
			$tempTable->setJoinTable($joinTable);
			$tempTable->setWhereInField("a.id");
			
			$parameters = array();
			$parameters["a.status"] = 1;
			$parameters["a.project_id"] = $projectId;
			
			$tempTable->setWhereParameters($parameters);
			$totalData = $tempTable->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $tempTable->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
					$nestedData['id'] = $pst->id;
					$nestedData['code'] = $pst->code;
					$nestedData['description'] = $pst->description;
					$nestedData['block'] = $pst->block;
					$nestedData['lot'] = $pst->lot;
					$nestedData['road_lot'] = $pst->road_lot;
					$nestedData['checklist_template'] = $pst->label;
					$nestedData['sf_status'] = $pst->sf_status;
					$nestedData['is_active'] = $pst->is_active;
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
	
	function getProjectData($id=null){
		$resultset = array();
		if($id){
			$this->db->select("a.*, b.description as company_name, c.description as location_name");
			$this->db->from($this->projectTable." a");
			$this->db->join($this->projectCompanyTable." b", "b.id = a.company_id");
			$this->db->join($this->projectLocationTable." c", "c.id = a.location_id");
			$this->db->where("a.status", 1);
			$this->db->where("a.id", $id);
			$query = $this->db->get();
			if($query->num_rows() == 1){
				$resultset["response"] = true;
				$resultset["row"] = $query->row();
			}else{
				$resultset["response"] = false;
			}
		}
		return $resultset;
	}

	function archiveModalProject(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post) && $post){
			$id = $post["id"];
			unset($post["csrf_token"], $post["id"]);

			$this->db->where("id", $id);
			$this->db->set("status", 0);
			$this->db->update($this->projectTable);

			$logged = $this->core_layout->insertArchiveLog($this->projectTable, $id);
			if($logged){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Development site has been archived.";
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to archive development site!";
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}

		return $resultset;
	}

	function updateModalProject(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post) && $post){
			$where = array("id"=>$post["id"]);
			unset($post["csrf_token"], $post["id"]);

			$update = $this->db->update($this->projectTable, $post, $where);
			if($update && $this->db->affected_rows() > 0){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Development site has been updated";
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to update development site!";
			}
			
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}

		return $resultset;
	}
	
    function getProjectDatatableRequest(){
        $post = $this->input->post();
		if($post){
            $orderx = (isset($post["order"]) && $post["order"])? $post["order"]: false;
            $columns = array("UPPER(CONCAT(b.code, '',c.code)) as project_code", "b.description as company", "c.description as location", "a.is_active", "a.id");
            $dir = "DESC";
			$order = "id";
			if($orderx){
				$dir = $orderx[0]["dir"];
				$order = $columns[$orderx[0]["column"]];
			}
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$tempTable = $this->dt_model->dataTable();
			$tempTable->setTable($this->projectTable);
			$tempTable->setTableAlias("a");

			$tempTable->setParameterFields($columns);

			$joinTable = array();
			$joinTable["table"][$this->projectCompanyTable] = "b";
			$joinTable["table"][$this->projectLocationTable] = "c";
			$joinTable["fields"][] = "b.id=a.company_id";
			$joinTable["fields"][] = "c.id=a.location_id";
			$joinTable["field_loc"][] = "LEFT";
			$joinTable["field_loc"][] = "LEFT";
			
			$tempTable->setJoinTable($joinTable);
			$tempTable->setWhereInField("a.id");

			$parameters = array();
			$parameters["a.status"] = 1;
			
			$tempTable->setWhereParameters($parameters);
			$totalData = $tempTable->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $tempTable->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
					$nestedData['id'] = $pst->id;
					$nestedData['project_code'] = $pst->project_code;
					$nestedData['company'] = $pst->company;
					$nestedData['location'] = $pst->location;
					$nestedData['is_active'] = $pst->is_active;
					$nestedData['pst'] = $pst;
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
	
    function getCompanyDatatableRequest(){
        $post = $this->input->post();
		if($post){
            $orderx = (isset($post["order"]) && $post["order"])? $post["order"]: false;
            $columns = array("code", "description", "is_active", "id");
            $dir = "DESC";
			$order = "id";
			if($orderx){
				$dir = $orderx[0]["dir"];
				$order = $columns[$orderx[0]["column"]];
			}
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$tempTable = $this->dt_model->dataTable();
			$tempTable->setTable($this->projectCompanyTable);
			$tempTable->setParameterFields($columns);
			
			$parameters = array();
			$parameters["status"] = 1;
			
			$tempTable->setWhereParameters($parameters);
			$totalData = $tempTable->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $tempTable->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
					$nestedData['id'] = $pst->id;
					$nestedData['code'] = $pst->code;
					$nestedData['description'] = $pst->description;
					$nestedData['is_active'] = $pst->is_active;
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

    function getProjectLocationDatatableRequest(){
        $post = $this->input->post();
		if($post){
            $orderx = (isset($post["order"]) && $post["order"])? $post["order"]: false;
            $columns = array("code", "description", "is_active", "id");
            $dir = "DESC";
			$order = "id";
			if($orderx){
				$dir = $orderx[0]["dir"];
				$order = $columns[$orderx[0]["column"]];
			}
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$tempTable = $this->dt_model->dataTable();
			$tempTable->setTable($this->projectLocationTable);
			$tempTable->setParameterFields($columns);
			
			$parameters = array();
			$parameters["status"] = 1;
			
			$tempTable->setWhereParameters($parameters);
			$totalData = $tempTable->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $tempTable->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
					$nestedData['id'] = $pst->id;
					$nestedData['code'] = $pst->code;
					$nestedData['description'] = $pst->description;
					$nestedData['is_active'] = $pst->is_active;
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

	function getProjectUnitRemarks($id=null){
		$resultset = array();
		if($id){
			$query = $this->db->get_where($this->projectUnitTable, array("id"=>$id, "is_active"=>1, "status"=>1));
			if($query->num_rows() == 1){
				$row = $query->row();
				$html = $this->load->view("pms/task/modal_content/unit_remarks", $row, true);
				$resultset["response"] = true;
				$resultset["row"] = $row;
				$resultset["html"] = $html;
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}
		return $resultset;
	}

	function getProjectUnitStatus($id=null){
		$resultset = array();
		if($id){
			$query = $this->db->get_where($this->projectUnitTable, array("id"=>$id, "is_active"=>1, "status"=>1));
			if($query->num_rows() == 1){
				$row = $query->row();
				$html = $this->load->view("pms/task/modal_content/unit_status", $row, true);
				$resultset["response"] = true;
				$resultset["row"] = $row;
				$resultset["html"] = $html;
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}
		return $resultset;
	}

	function getCurrentData($id=null){
		$resultset = array();
		if($id){
			$where = array("a.id"=>$id, "a.is_active"=>1, "a.status"=>1);
			$this->db->select("a.id, UPPER(CONCAT(b.code, '', c.code)) as project_code, b.description as company, c.description as location");
			$this->db->from($this->projectTable." a");
			$this->db->join($this->projectCompanyTable." b", "b.id=a.company_id");
			$this->db->join($this->projectLocationTable." C", "c.id=a.location_id");
			$this->db->where($where);
			$query = $this->db->get();

			if($query->num_rows() == 1){
				$resultset["response"] = true;
				$resultset["row"] = $query->row();
				$resultset["row_count"] = $query->num_rows();
			}
		}else{
			$resultset["response"] = false;
		}

		return $resultset;
	}

    function setModalProject(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$allow = $this->checkProjectCompanyLocation($post);
			if($allow){
				$insert = $this->db->insert($this->projectTable, $post);
				if($insert){
					$resultset["response"] = true;
					$resultset["toastr_msg"] = "Project has been added.";
				}else{
					$resultset["response"] = false;
					$resultset["toastr_msg"] = "Failed saving project data!";
				}
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Project already exist!";
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}
        
        return $resultset;
	}

    function setModalProjectUnitStatus(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			$where = array("id"=>$post["id"]);
			unset($post["csrf_token"], $post["id"]);
			$update = $this->db->update($this->projectUnitTable, $post, $where);
			if($update){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Unit status has been updated.";

				$tempData = $this->adm_task->getSequenceFormData($where["id"]);
				$row = (isset($tempData["row"]) && $tempData["row"])? $tempData["row"]: array();
				$resultset["row"] = $row;
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed saving unit status!";
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}
        
        return $resultset;
	}

    function setModalProjectUnitRemarks(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			$where = array("id"=>$post["id"]);
			unset($post["csrf_token"], $post["id"]);
			$update = $this->db->update($this->projectUnitTable, $post, $where);
			if($update){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Unit Remarks has been updated.";

				$tempData = $this->adm_task->getSequenceFormData($where["id"]);
				$row = (isset($tempData["row"]) && $tempData["row"])? $tempData["row"]: array();
				$resultset["row"] = $row;
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed saving unit remarks!";
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}
        
        return $resultset;
	}
	
	function setModalProjectUnit(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$post["code"] = strtolower($post["code"]);
			$post["description"] = strtoupper($post["description"]);
			$allow = $this->checkProjectUnitCode($post);
			if($allow){
				$insert = $this->db->insert($this->projectUnitTable, $post);
				if($insert){
					$resultset["response"] = true;
					$resultset["toastr_msg"] = "Unit has been added.";
				}else{
					$resultset["response"] = false;
					$resultset["toastr_msg"] = "Failed saving unit data!";
				}
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Unit name already exist!";
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}
        
        return $resultset;
	}
	
    function setModalProjectCompany(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$post["code"] = strtolower($post["code"]);
			$post["description"] = strtoupper($post["description"]);
			$allow = $this->checkProjectCompanyCode($post);
			if($allow){
				$insert = $this->db->insert($this->projectCompanyTable, $post);
				if($insert){
					$resultset["response"] = true;
					$resultset["toastr_msg"] = "Company has been added.";
				}else{
					$resultset["response"] = false;
					$resultset["toastr_msg"] = "Failed saving company data!";
				}
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Company code already exist!";
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}
        
        return $resultset;
    }

    function setModalProjectLocation(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$post["code"] = strtolower($post["code"]);
			$post["description"] = strtoupper($post["description"]);
			$allow = $this->checkProjectLocationCode($post);
			if($allow){
				$insert = $this->db->insert($this->projectLocationTable, $post);
				if($insert){
					$resultset["response"] = true;
					$resultset["toastr_msg"] = "Project location has been added.";
				}else{
					$resultset["response"] = false;
					$resultset["toastr_msg"] = "Failed saving project location data!";
				}
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Project location code already exist!";
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}
        
        return $resultset;
    }

	private function checkProjectUnitCode($data=array()){
		if($data){
			$query = $this->db->get_where($this->projectUnitTable, array("code"=>$data["code"]));
			if($query->num_rows() == 0){ return true; }
			else{ return false; }
		}else{ return false; }
	}

    private function checkProjectCompanyLocation($data=array()){
		if($data){
			$query = $this->db->get_where($this->projectTable, array("company_id"=>$data["company_id"], "location_id"=>$data["location_id"]));
			if($query->num_rows() == 0){ return true; }
			else{ return false; }
		}else{ return false; }
	}
	
    private function checkProjectCompanyCode($data=array()){
		if($data){
			$query = $this->db->get_where($this->projectCompanyTable, array("code"=>$data["code"]));
			if($query->num_rows() == 0){ return true; }
			else{ return false; }
		}else{ return false; }
    }

    private function checkProjectLocationCode($data=array()){
		if($data){
			$query = $this->db->get_where($this->projectLocationTable, array("code"=>$data["code"]));
			if($query->num_rows() == 0){ return true; }
			else{ return false; }
		}else{ return false; }
	}
	
	public function getProjectCompanySelect2Data(){
		$resultset = array();
		$arrData = array();
        $where = array("is_active"=>1, "status"=>1);

		$get = $this->input->get();
		$this->db->select("id, description as text");
		$this->db->from($this->projectCompanyTable);
		$this->db->where($where);
		if(isset($get["term"]) && $get["term"]){ $this->db->like("description", $get["term"], "both"); }
		$query = $this->db->get();

		if($query->num_rows() > 0){
			$arrData = $query->result();
		}
		
		$resultset["results"] = $arrData;
		return $resultset;
	}

	public function getProjectLocationSelect2Data(){
		$resultset = array();
		$arrData = array();
        $where = array("is_active"=>1, "status"=>1);

		$get = $this->input->get();
		$this->db->select("id, description as text");
		$this->db->from($this->projectLocationTable);
		$this->db->where($where);
		if(isset($get["term"]) && $get["term"]){ $this->db->like("description", $get["term"], "both"); }
		$query = $this->db->get();

		if($query->num_rows() > 0){
			$arrData = $query->result();
		}
		
		$resultset["results"] = $arrData;
		return $resultset;
	}
	
	function generateProjectCodeByTaskId($id=null, $subtask=null){
		$arrData = array();
		if($id){
			$this->db->select("UPPER(CONCAT(d.code, '', e.code)) as project_code, g.series as wo_type, f.wo_code as gen_code");
			$this->db->from($this->taskTable." a");
			$this->db->join($this->projectUnitTable." b", "b.id = a.unit_id");
			$this->db->join($this->projectTable." c", "c.id = b.project_id");
			$this->db->join($this->projectCompanyTable." d", "d.id = c.company_id");
			$this->db->join($this->projectLocationTable." e", "e.id = c.location_id");
			$this->db->join($this->itemTable." f", "f.id = a.task_id");
			$this->db->join($this->woTypeTable." g", "g.id = f.wo_type_id", "LEFT");
			$this->db->where("a.id", $id);
			$queryTask = $this->db->get();
			if($queryTask->num_rows() == 1){
				$arrData = $queryTask->row();
				$data = $this->generateSubTaskCode($subtask);
				if($data && count($data) > 0){
					$arrData = (array) $arrData;
					foreach($data as $key => $value){ $arrData[$key] = $value; }
					$arrData = (object) $arrData;
				}
			}
		}
		
		return $arrData;
	}
	
	function generateProjectCodeById($id=null){
		$arrData = array();
		if($id){
			$this->db->select("UPPER(CONCAT(b.code, '', c.code)) as project_code");
			$this->db->from($this->projectTable." a");
			$this->db->join($this->projectCompanyTable." b", "b.id = a.company_id");
			$this->db->join($this->projectLocationTable." c", "c.id = a.location_id");
			$this->db->where("a.id", $id);
			$query = $this->db->get();
			if($query->num_rows() == 1){
				$arrData = $query->row();
			}
		}
		return $arrData;
	}
	function generateProjectDescriptionById($id=null){
		$arrData = array();
		if($id){
			$this->db->select("UPPER(CONCAT(b.description, ' - ', c.description)) as project_description");
			$this->db->from($this->projectTable." a");
			$this->db->join($this->projectCompanyTable." b", "b.id = a.company_id");
			$this->db->join($this->projectLocationTable." c", "c.id = a.location_id");
			$this->db->where("a.id", $id);
			$query = $this->db->get();
			if($query->num_rows() == 1){
				$arrData = $query->row();
			}
		}
		return $arrData;
	}

	function generateItemTaskCode($id=null){
		$arrData = array();
		if($id){
			$this->db->select("a.wo_code as gen_code, b.series as wo_type");
			$this->db->from($this->itemTable." a");
			$this->db->join($this->woTypeTable." b", "b.id = a.wo_type_id", "LEFT");
			$this->db->where("a.id", $id);
			$queryTaskItem = $this->db->get();
			if($queryTaskItem->num_rows() == 1){
				$arrData = $queryTaskItem->row();
			}
		}

		return $arrData;
	}
	function generateSubTaskCode($subtask=null){
		$arrData = array();
		$this->db->select("c.series as wo_type, a.wo_code as spec_code, b.wo_code as gen_code");
		$this->db->from($this->itemTable." a");
		$this->db->join($this->itemTable." b", "b.id = a.parent_id");
		$this->db->join($this->woTypeTable." c", "c.id = a.wo_type_id", "LEFT");
		$this->db->where("a.id", $subtask);
		$querySubTask = $this->db->get();
		if($querySubTask->num_rows() == 1){
			$arrData = $querySubTask->row();
		}

		return $arrData;
	}

	function editCompany($id) {
	    $post = $this->input->post();
	    $this->db->where("id", $id);
	    return $this->db->update($this->projectCompanyTable, $post);
    }

    function archiveProjectCompany($id) {
        $this->db->trans_begin();

        $this->db->where("id", $id);
        $this->db->set("status", 0);
        $this->db->update($this->projectCompanyTable);

        $this->core_layout->insertArchiveLog($this->projectCompanyTable, $id);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function editLocation($id) {
        $post = $this->input->post();
        $this->db->where("id", $id);
        return $this->db->update($this->projectLocationTable, $post);
    }

    function archiveProjectLocation($id) {
        $this->db->trans_begin();

        $this->db->where("id", $id);
        $this->db->set("status", 0);
        $this->db->update($this->projectLocationTable);

        $this->core_layout->insertArchiveLog($this->projectLocationTable, $id);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
}