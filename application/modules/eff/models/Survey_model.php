<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Survey_model extends CI_Model{
	protected $surveyItemTable = "gccph_uss.survey_item";
	protected $surveyMetaTable = "gccph_uss.survey_meta";
	protected $employeesTable = "gccmaster.tblemployees";
	protected $departmentTable = "gccph_uss.departments";
	function __construct(){
		parent::__construct();
		$this->load->model("authentication","authenticate");
		$this->load->model("datatable_model","dt_model");
		$this->load->model("department_model");
	}
	
	function addSurveyData(){
		$resultset = array();
		$post = $this->input->post();
		if($post){
			$data = array();
			$data["department_id"] = $post["department"];
			$data["user_id"] = $this->authenticate->getUserId();
			
			$saved = $this->db->insert($this->surveyItemTable, $data);
			if($saved){
				$counter = 0;
				$metaId = $this->db->insert_id();
				foreach($post as $key => $value){
					$metaData = array();
					$metaData["meta_id"] = $metaId;
					$metaData["meta_field"] = $key;
					$metaData["meta_value"] = $value;
					
					$metaInserted = $this->db->insert($this->surveyMetaTable, $metaData);
					if($metaInserted){
						$counter++;
					}
				}
				
				if($counter > 0){
					$resultset["response"] = true;
					$resultset["toastr_msg"] = "Survey data has been saved.";
				}else{
					$resultset["response"] = false;
					$resultset["toastr_msg"] = "Failed to save survey data!";
				}
			}else{
				$resultset["response"] = false;				
				$resultset["toastr_msg"] = "Failed to save survey data!!";
			}
		}else{
			$resultset["response"] = false;			
			$resultset["toastr_msg"] = "No data found!";
		}
		
		return $resultset;
	}
	
	function getSurveyList(){
		$post = $this->input->post();
		if($post){
			$columns = array(
				"survey_item.created_at", "survey_item.id", "department.description", "CONCAT(employees.lastname, ', ', employees.firstname, ' ', employees.middlename) as employee_name");
			$dir = $post["order"][0]["dir"];
			$order = $columns[$post["order"][0]["column"]];
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			
			$departmentId = (isset($post["department_id"]) && $post["department_id"])? $post["department_id"]: 0;
			
			$dtSurvey = $this->dt_model->dataTable();
			$dtSurvey->setTable($this->surveyItemTable);
			$dtSurvey->setParameterFields($columns);
			
			$joinTable = array();
			$joinTable["table"][$this->employeesTable] = "employees";
			$joinTable["table"][$this->departmentTable] = "department";
			$joinTable["fields"][] = "employees.id=survey_item.user_id";
			$joinTable["fields"][] = "department.id=survey_item.department_id";
			$joinTable["field_loc"][] = "LEFT";
			$joinTable["field_loc"][] = "LEFT";
			
			$dtSurvey->setJoinTable($joinTable);
			$dtSurvey->setWhereInField("survey_item.id");
			
			if($departmentId){
				$parameters = array();
				$parameters["department.id"] = $departmentId;
				
				$dtSurvey->setWhereParameters($parameters);				
			}
			
			$totalData = $dtSurvey->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $dtSurvey->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $dtSurvey->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $dtSurvey->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
					$nestedMeta = $this->getSurveyMetaData($pst->id);
					$nestedData['id'] = $pst->id;
					$nestedData['department'] = strtoupper($pst->description);
					$nestedData['employee_name'] = strtoupper($pst->employee_name);
					$nestedData['purpose'] = (isset($nestedMeta["purpose"]) && $nestedMeta["purpose"])? strtoupper($nestedMeta["purpose"]): "---";
					$nestedData['support'] = (isset($nestedMeta["supported"]) && $nestedMeta["supported"] == 1)? strtoupper("yes"): strtoupper("no");
					$nestedData['rating'] = (isset($nestedMeta["rating"]) && $nestedMeta["rating"])? strtoupper($nestedMeta["rating"]): "0";
					$nestedData['remarks'] = (isset($nestedMeta["remarks"]) && $nestedMeta["remarks"])? strtoupper($nestedMeta["remarks"]): "---";
					$nestedData['created_at'] = strtoupper(date("F d, Y", strtotime($pst->created_at)));
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
	function getdepartmentList(){
		$post = $this->input->post();
		if($post){
			$columns = array(
				"survey_item.created_at", "survey_item.id", "department.description", "CONCAT(employees.lastname, ', ', employees.firstname, ' ', employees.middlename) as employee_name");
			$dir = $post["order"][0]["dir"];
			$order = $columns[$post["order"][0]["column"]];
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$dtSurvey = $this->dt_model->dataTable();
			$dtSurvey->setTable($this->surveyItemTable);
			$dtSurvey->setParameterFields($columns);
			
			$joinTable = array();
			$joinTable["table"][$this->employeesTable] = "employees";
			$joinTable["table"][$this->departmentTable] = "department";
			$joinTable["fields"][] = "employees.id=survey_item.user_id";
			$joinTable["fields"][] = "department.id=survey_item.department_id";
			$joinTable["field_loc"][] = "LEFT";
			$joinTable["field_loc"][] = "LEFT";
			
			$dtSurvey->setJoinTable($joinTable);
			$dtSurvey->setWhereInField("survey_item.id");
			
			$totalData = $dtSurvey->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $dtSurvey->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $dtSurvey->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $dtSurvey->dtPostSearchCount($searchValue);
			}
			
			
		$data = array();
		$list = $this->department_model->getDepartmentList();
		
		if(isset($list["data"]) && $list["data"]){
			foreach ($list["data"] as $myList) {
				$num=0;
			$temp=0;
			$num2=0;
				$nestedData['department'] = strtoupper($myList['description']);
				if(!empty($posts)){
					foreach ($posts as $pst){
						$nestedMeta = $this->getSurveyMetaData($pst->id);
						if(strtoupper($myList['description'])==strtoupper($pst->description)){
						$nestedData['rating'] = (isset($nestedMeta["rating"]) && $nestedMeta["rating"])? strtoupper($nestedMeta["rating"]): "0";	
						$temp = $nestedData['rating'];
						$num2=$num2+1;
						$num=$num+$temp;	
						}	
						
					}
				}
				if($num > 0){
					$num=$num/$num2;
					$num=round($num);				
				}
				$nestedData['rating'] = $num;
				$nestedData['surveys'] = $num2;
				$nestedData['action'] = '<a class="btn btn-sm btn-primary" title="View" onclick="view_list('."'".$myList['id']."'".')"><i >View</i></a>';
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
	function getSurveyMetaData($id=null){
		$arrData = array();
		$query = $this->db->get_where($this->surveyMetaTable, array("meta_id"=>$id));
		if($query->num_rows() > 0){
			foreach($query->result() as $rs){
				$arrData[$rs->meta_field] = $rs->meta_value;
			}
		}
		
		return $arrData;
	}
}