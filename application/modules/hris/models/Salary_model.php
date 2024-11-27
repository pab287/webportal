<?php defined('BASEPATH') || exit('No direct script access allowed');
class Salary_model extends CI_Model{
	protected $salaryTable = "gcchris.tblsalary";
	protected $archivedTable = "gccmaster.archived_items";
	protected $employeeTable = "gccmaster.tblemployees";
	
	function __construct(){
		parent::__construct();
		$this->load->model("hris/employee_model", "adm_employee");
		$this->load->model("core/datatable_model","dt_model");
		$this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
        $this->loggedInUsername = $this->loggedinData["username"];
    }

    function getSalaryDatatableRequest(){
        $post = $this->input->post();
		if($post){
			$orderx = (isset($post["order"]) && $post["order"])? $post["order"]: false;
			$columns = array("salary.description", "salary.id", "employees.lastname", "employees.firstname", "employees.middlename", "employees.suffix");
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
			$dtTemp = $this->dt_model->dataTable();
            $dtTemp->setTable($this->salaryTable);
            $dtTemp->setTableAlias("salary");

			$dtTemp->setParameterFields($columns);
            
            $joinTable = array();
			$joinTable["table"][$this->employeeTable] = "employees";
			$joinTable["fields"][] = "employees.id=salary.created_by";
			$joinTable["field_loc"][] = "LEFT";
			
			$dtTemp->setJoinTable($joinTable);
            $dtTemp->setWhereInField("salary.id");
            
			$parameters = array();
			$parameters["salary.is_archived"] = (isset($post['is_archived']) && $post['is_archived'] == 1) ? 1 : 0;
			
			$dtTemp->setWhereParameters($parameters);

			$totalData = $dtTemp->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){
				$posts = $dtTemp->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $dtTemp->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $dtTemp->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
                    $tempRs = (array) $pst;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $pst->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";

					$nestedData = array();
					$nestedData['id'] = $pst->id;
					$nestedData['description'] = $pst->description;
					$nestedData['created_by'] = $pst->display_name;
					$data[] = $nestedData;
				}
			}
			return array(
                    "draw" => intval($draw),  
                    "recordsTotal" => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,   
                    );
		}else{
			return array(
				"draw"=>1,
				"recordsTotal"=>0,
				"recordsFiltered"=>0,
				"data"=>array(),
				);
		}
    }

    function getSalaryModalContent($content="add"){
		$resultset = array();
		$html = "";
        $arrData = array();
        
		if($content == "add"){
			$html = $this->load->view("hris/masterfile/salary/modals/add_content", null, true);
        }
        
		if($content == "edit"){
			$post = $this->input->post();
			if(isset($post) && $post){
				unset($post["csrf_token"]);
				$tempData = $this->db->get_where($this->salaryTable, $post);
				if($tempData->num_rows() == 1){
					$arrData = $tempData->row();
				}
			}
			$html = $this->load->view("hris/masterfile/salary/modals/edit_content", array("data"=>$arrData), true);
		}

		if($html){
			$resultset["response"] = true;
			$resultset["html"] = $html;
			$resultset["data"] = $arrData;
		}else{
			$resultset["response"] = false;
		}
		
		return $resultset;
    }

    function setModalSalary(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
            unset($post["csrf_token"]);
            
            $post["created_dt"] = date("Y-m-d H:i:s");
            $post["created_by"] = $session["emp_id"];

			$allow = $this->checkSalaryRange($post);
			if($allow){
				$insert = $this->db->insert($this->salaryTable, $post);
				if($insert){
					$resultset["response"] = true;
					$resultset["toastr_msg"] = "Salary data has been added.";
					$this->core_layout->setEventLog("User ".$this->loggedInUsername. " inserted new salary with db id no. ".$this->db->insert_id(),"insert", "success", "gcchris", "user");
				}else{
					$resultset["response"] = false;
					$resultset["toastr_msg"] = "Failed saving salary data!";
					$this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed inserting new salary","insert", "error", "gcchris", "user");
				}
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Salary data already exist!";
				$this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed inserting existing salary","insert", "error", "gcchris", "user");
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Salary masterfile - Error, No post data found.","insert", "error", "gcchris", "user");
		}
        
        return $resultset;
    }

    function updateModalSalary(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			$id = $post["id"];
            unset($post["csrf_token"], $post["id"]);
            
			$post["modify_dt"] = date("Y-m-d H:i:s");
            $post["modify_by"] = $session["emp_id"];

			$update = $this->db->update($this->salaryTable, $post, array("id"=>$id));
			if($update){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Salary data has been updated.";
				$this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated salary with db id no. ".$id,"update", "success", "gcchris", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed updating salary data!";
				$this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed updating salary with db id no. ".$id,"update", "error", "gcchris", "user");
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Salary masterfile - Error, No post data found.","update", "error", "gcchris", "user");
		}
        
        return $resultset;
    }

    function removeCurrentSalary(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$updated = $this->db->update($this->salaryTable, array("is_archived"=>1), $post);
			if($updated){
				$session = $this->core_layout->getCurrentSession();
				$data = array(
					"archived_table"=>$this->salaryTable,
					"archived_id"=>$post["id"],
					"archived_by"=>$session["emp_id"]
				);

				$this->db->insert($this->archivedTable, $data);

				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Salary has been removed.";
				$this->core_layout->setEventLog("User ".$this->loggedInUsername. " has archived salary with db id no. ".$post["id"],"archive", "success", "gcchris", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to remove salary!";
				$this->core_layout->setEventLog("User ".$this->loggedInUsername. " has failed archiving salary with db id no. ".$post["id"],"archive", "error", "gcchris", "user");
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Salary masterfile - Error, No post data found.","archive", "error", "gcchris", "user");
		}

		return $resultset;
    }

    private function checkSalaryRange($data=array()){
		if($data){
			$query = $this->db->get_where($this->salaryTable, array("description"=>$data["description"]));
			return $query->num_rows() == 0 ? true : false;
		}else{ return false; }
	}

	public function getSalarySelect2Data(){
		$resultset = array();
		$arrData = array();

		$get = $this->input->get();
		$this->db->select("id, description as text");
		$this->db->from($this->salaryTable);
		$this->db->where("is_archived", 0);
		if(isset($get["term"]) && $get["term"]){ $this->db->like("description", $get["term"], "both"); }
		$query = $this->db->get();

		if($query->num_rows() > 0){
			foreach($query->result() as $value){
				$expV = explode("-",$value->text);
				$tosort = str_replace(",","",$expV[0]);
				$tosort = str_replace(" ","",$tosort);
				$resultset[$tosort] = array("id"=> $value->id,"text"=> $value->text);
			}
		}
		ksort($resultset);

		foreach($resultset as $k=>$v){
			$datas = array();
			$datas["id"] = $v["id"];
			$datas["text"] = $v["text"];
			$arrData[] = $datas;
		}
		return array("results"=>$arrData);
	}

	function restoreCurrentSalary(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$updated = $this->db->update($this->salaryTable, array("is_archived"=>0), $post);
			if($updated){
				// $session = $this->core_layout->getCurrentSession();
				// $data = array(
				// 	"archived_table"=>$this->salaryTable,
				// 	"archived_id"=>$post["id"],
				// 	"archived_by"=>$session["emp_id"]
				// );

				// $this->db->insert($this->archivedTable, $data);

				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Salary has been restored.";
				$this->core_layout->setEventLog("User ".$this->loggedInUsername. " has restored salary with db id no. ".$post["id"],"restore", "success", "gcchris", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to restore salary!";
				$this->core_layout->setEventLog("User ".$this->loggedInUsername. " has failed restoring salary with db id no. ".$post["id"],"restore", "error", "gcchris", "user");
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Salary masterfile - Error, No post data found.","archive", "error", "gcchris", "user");
		}

		return $resultset;
    }
}