<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Location extends MY_Controller {
	public function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->core_layout->setPrivilegeName("gcctime_location");
	}

	public function index()
	{
		$this->load->view('core/templates/header');
		$this->load->view('location/index');
		$this->load->view('core/templates/footer');
	}

	function getCollection(){
		$actions = $this->core_layout->getCurrentActions();
		$resultarray = array();
		
		$getCollection = $this->crud->getCollection(array('status'=>1),"gcctimeutility.location");
		if($getCollection){
			foreach($getCollection as $_getcollection){
				$data = array();
				$_actions = "";
				$allowNotification = "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-exclamation-triangle'></i></span>";
				if($_getcollection["allow_notification"] == 1){
					$allowNotification = "<span class='btn btn-success m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-exclamation-triangle'></i></span>";
				}

				if($_getcollection["status"] == 1){
					$status = "<span class='btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-check'></i></span>";
				}else{
					$status = "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-remove'></i></span>";
				}

				if(in_array('edit', $actions)){
					$_actions .= "<a href='javascript:void(0);' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='edit_location(".$_getcollection['id'].")' data-toggle='tooltip' title='Edit Profile' data-id='".$_getcollection['id']."'><i class='la la-edit'></i></a>";					
				}
				if(in_array('delete', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDelete btn-delete' data-id='".$_getcollection["id"]."' title='Delete'><i class='la la-trash-o'></i></a>";
				}
				if(in_array('assign', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnAssign btn-late-select-person' data-id='".$_getcollection["id"]."' title='Assign Persons'><i class='la la-users'></i></a>";
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill' onclick='schedule_list(".$_getcollection["id"].")' title='Assigned List'><i class='la la-list-ul'></i></a>";
				}


				$data[] = $_getcollection["name"];
				$data[] = $allowNotification;
				$data[] = $status;
				$data[] = $_actions;

				$resultarray[] = $data;
			}
		}

		echo json_encode(array("data"=>$resultarray));
	}

	function processNew(){
		$post = $this->input->post();
		$resultarray = array();
		$insertQuery = $this->crud->insert($post,"gcctimeutility.location");

		if($insertQuery){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully saved!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request.";
		}

		echo json_encode($resultarray);
	}

	function getLocation(){
		$post = $this->input->post();
		$query =  $this->crud->load($post,"gcctimeutility.location");

		echo json_encode($query);
	}

	function processEdit(){
		$resultarray = array();
		$post = $this->input->post();
		$where = array("id"=>$post["id"]);
		unset($post["csrf_token"], $post["id"]);
		$queryUpdate = $this->db->update("gcctimeutility.location", $post, $where);
		//$queryUpdate = $this->db->query("UPDATE gcctimeutility.location SET name = '{$post['name']}' WHERE id = {$post['id']}");
		if($queryUpdate){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully updated!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request.";
		}

		echo json_encode($resultarray);
	}

	public function selectPersonnelforLoc()
	{
		$post = $this->input->post();

		$query = $this->crud->update(array("location_id"=>$post["location_id"]),array("id"=>$post["personnel_id"]),"gcctimeutility.personnel");

		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function deselectPersonnelforLoc()
	{
		$post = $this->input->post();
		
		$query = $this->crud->update(array("location_id"=>0),array("id"=>$post["personnel_id"]),"gcctimeutility.personnel");
		
		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}
		
		echo json_encode($resultarray);
	}
	public function get_select2_location(){
		$get = $this->input->get();
		$data = array();

		$this->db->select("id, name as text");
		$this->db->from("gcctimeutility.location");
		$this->db->where("status", 1);
		if(isset($get["terms"]) && $get["terms"]){
			$this->db->like("name", $get["terms"], "both");
		}
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$data = $query->result();
		}
		$resultset = array();
		$resultset["results"] = $data;
		echo json_encode($resultset);
	}

}