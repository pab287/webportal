<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Devices extends MY_Controller {

	function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->core_layout->setPrivilegeName("gcctime_devices");
		$this->load->model("Crudv2_model","crud2");
    }

	public function index()
	{
		$this->load->view('core/templates/header');
		$this->load->view('devices/index');
		$this->load->view('core/templates/footer');
	}

	public function getDeviceCollection(){
		$actions = $this->core_layout->getCurrentActions();
		$resultset = array();
		$tempData = array();

		$this->db->select("a.*, IFNULL(b.name, 'NO ASSIGNED LOCATION') as location");
		$this->db->from("gcctimeutility.devices a");
		$this->db->join("gcctimeutility.location b", "b.id = a.location_id", "left");
		$query = $this->db->get();
		/*** $query = $this->crud->getCollection(array(),"gcctimeutility.devices"); ***/

		if($query->num_rows() > 0){
			$tempData = $query->result();

			/*** foreach($query->result() as $_query){
				if($_query["status"] == 1){
					$status = '<div class="alert alert-success alert-dismissible col-md-6 text-center" role="alert"><strong>Connected</strong></div>';
					$connection = "<a href='javascript:void(0);' class='m-portlet__nav-link btn m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill disconnect_device' title='Disconnect Device'><i class='la la-unlink'></i></a>";
				}else{
					$status = '<div class="alert alert-danger alert-dismissible col-md-6 text-center" role="alert"><strong>Disconnected</strong></div>';
					$connection = "<a href='javascript:void(0);' class='m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill connect_device' onclick='connect_device(".$_query["id"].")' title='Connect Device'><i class='la la-link'></i></a>";
				}
				$data = array();

				$data[] = $_query["device_name"];
				$data[] = $_query["ip_address"];
				$data[] = $status;
				$data[] = $_query["is_active"];
				$data[] = $_query["allow_override"];
				
				$_actions = "";
				if(in_array('edit', $actions)){
					$_actions .= "<a href='javascript:void(0);' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='edit_device(".$_query["id"].")' title='Edit'><i class='la la-edit'></i></a>";					
				}
				if(in_array('delete', $actions)){
					$_actions .= "<a href='javascript:void(0);' class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill' onclick='delete_device(".$_query["id"].")' title='Delete'><i class='la la-trash-o'></i></a>";					
				}
				$_actions .= $connection;
				
				$data[] = $_actions;
				$resultarray[] = $data;
			} ***/
		}

		$resultset["data"] = $tempData;
		$resultset["actions"] = $actions;

		echo json_encode($resultset);

	}

	public function add_device()
	{
		$post = $this->input->post();
		$resultarray = array();
		if(isset($post) && $post){
			$query = $this->crud->insert($post, "gcctimeutility.devices");
			if($query){
				$resultarray["status"] = TRUE;
				$resultarray["message"] = "Successfully saved data!";
			}else{
				$resultarray["status"] = FALSE;
				$resultarray["message"] = "Error processing request!";
			}
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "No post data found!";
		}


		echo json_encode($resultarray);

	}

	public function getDevice(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post["id"]) && $post["id"]){
			$this->db->select("a.*, b.name as location_name");
			$this->db->from("gcctimeutility.devices a");
			$this->db->join("gcctimeutility.location b", "b.id = a.location_id", "left");
			$this->db->where("a.id", $post["id"]);
			$query = $this->db->get();
			if($query->num_rows() == 1){
				$resultset["response"] = true;
				$resultset["row"] = $query->row();
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}

		echo json_encode($resultset);
	}

	public function edit_device()
	{
		$resultarray = array();
		$post = $this->input->post();
		if(isset($post["id"]) && $post["id"]){
			$tempWhere = array();
			$tempWhere["id"] = $post["id"];
			unset($post["id"]);
			$updated = $this->crud->update($post, $tempWhere,"gcctimeutility.devices");
			if($updated && $this->db->affected_rows() > 0){
				$resultarray["status"] = TRUE;
				$resultarray["message"] = "Successfully updated data!";
			}else{
				$resultarray["status"] = FALSE;
				$resultarray["message"] = "Error processing request!";
			}
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "No post data found!";
		}

		echo json_encode($resultarray);
	}

	public function delete_device()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud2->delete($post,"gcctimeutility.devices");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Successfully updated data!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}
		echo json_encode($resultarray);
	}

	public function connect_device()
	{
		$post = $this->input->post();

		//diconnect all device
		$this->disconnectAllDevice();

		$connectdevice = $this->crud->update(array("status"=>1),$post,"gcctimeutility.devices");

		if($connectdevice){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Successfully established device connection!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}
		echo json_encode($resultarray);

	}

	function disconnectAllDevice()
	{
		$query = $this->crud->getCollection(array(),"gcctimeutility.devices");

		if($query){
			foreach($query as $_query)
			{
				$disconnectdevice = $this->crud->update(array("status"=>0),array("id"=>$_query["id"]),"gcctimeutility.devices");
			}
		}
	}
}
