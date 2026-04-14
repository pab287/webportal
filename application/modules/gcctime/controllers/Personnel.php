<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Personnel extends MY_Controller {
	public function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->core_layout->setBodyClass("attendance");
		$this->core_layout->setPrivilegeName("gcctime_personnel");
		$this->load->model("site_restrict_model", "restrict");

		include(BASEPATH.'libraries/zklibrary.php');
		date_default_timezone_set("Asia/Taipei");
	}

	public function index(){
		$this->core_layout->setPrivilegeName("gcctime_personnel");
		$this->load->view('core/templates/header');
		$this->load->view('personnel/index', array("device"=>$this->getDevice()));
		$this->load->view('core/templates/footer');
	}

	function payroll_group(){
		$this->load->model("payroll/payroll_m", "payroll");
		$tempData = array();
		$tempData["company"] = $this->payroll->select2CompanyData();
		$this->core_layout->setPageTitle("Gcctime - Employee Group");
		$this->core_layout->setPrivilegeName("gcctime_employee_group");

		$version = filemtime(FCPATH.'assets/js/payroll/employee/payroll.group.js');
		$this->core_layout->addJs("js/payroll/employee/payroll.group.js", true, $tempData, "?v=$version");

		$this->load->view('core/templates/header');
		$this->load->view('payroll/payroll/employee_group');
		$this->load->view('core/templates/footer');
	}

	private function getDevice(){
		$query =  $this->crud->load(array("status"=>1),"gcctimeutility.devices");
		return $query;
	}

	private function getbiometric(){
		$data = $this->db->get("gcctimeutility.appusers");
		$id = $data->row_array();
		$bio = $this->crud->load(array("biometricno"=>$id['emp_id']), "gccmaster.tblemployees");
		return $bio['biometricno'];
	}

	public function getPersonnelCollectionData(){
		$this->db->select("a.*, b.description");
		$this->db->from("gcctimeutility.personnel a");
		$this->db->join("gcctimeutility.shift_schedule b", "b.id = a.shift_id", "left");
		$this->db->order_by("a.id", "DESC");
		$query = $this->db->get();

		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return false;
		}
	}

	private function all_location($id){
		$data_array = $this->crud->getCollection(array("personnel_id" => $id), "gcctimeutility.personnel_locations");
		if($data_array != null){
			$site_name = array();
			foreach($data_array as $sites){
				array_push($site_name, $sites['location_name']);
			}
			return implode(",", $site_name);
		}else{
			return "N/A";
		}
	}

	public function getPersonnel(){
		$actions = $this->core_layout->getCurrentActions();

		/*** $getPersonnelCollection = $this->crud->getCollection(array(),"gcctimeutility.personnel",array("id"=>"DESC")); ***/
		$getPersonnelCollection = $this->getPersonnelCollectionData();
		$resultarray = array();
		$server = $this->input->server("SERVER_NAME");
		if($getPersonnelCollection){
			foreach($getPersonnelCollection as $_getPersonnelCollection){
				$personnelId = $_getPersonnelCollection["id"];
				$getEmployee = $this->crud->load(array("biometricno"=>$_getPersonnelCollection["biometric_id"]),"gccmaster.tblemployees");
				$employeeId = (isset($getEmployee["id"]) && $getEmployee["id"])? $getEmployee["id"]: 0;
				$getDepartmentData = $this->crud->load(array("id"=>$_getPersonnelCollection["department_id"]),"gcctimeutility.department");
				$getLocationData = $this->crud->load(array("id"=>$_getPersonnelCollection["location_id"]),"gcctimeutility.location");
				$getPersonnelSiteLocation = $this->all_location($personnelId);

				$checked = '<span class="btn btn-primary m-btn m-btn--icon m-btn--icon-only btn-sm" data-toggle="tooltip" title="Active"><i class="la la-check"></i></span>';
				$checked2 = '<span class="btn btn-warning m-btn m-btn--icon m-btn--icon-only btn-sm"><i class="la la-clock-o"></i></span>';
				$unchecked = '<span class="btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm" data-toggle="tooltip" title="Suspended"><i class="la la-remove"></i></span>';

				if($_getPersonnelCollection["is_flexi"] == 1){
					$isFlexible = $checked;
				}else if($_getPersonnelCollection["is_flexi"] == 2){
					$isFlexible = $checked2;
				}else{
					$isFlexible = $unchecked;
				}

				//$isFlexible = (isset($_getPersonnelCollection["is_flexi"]) && $_getPersonnelCollection["is_flexi"] == 1)? $checked: $unchecked;
				$isActive = (isset($_getPersonnelCollection["is_active"]) && $_getPersonnelCollection["is_active"] == 1)? $checked: $unchecked;
				$tempState = isset($_getPersonnelCollection["is_active"]) && $_getPersonnelCollection["is_active"] == 1 ? "Active": "Suspended";

				$link = site_url("hris/masterfile/view_employee_masterfile/{$employeeId}");
				$data = array();
				switch ($_getPersonnelCollection["role"]) {
					case '0':
						$role = "USER";
					break;
					case '2':
						$role = "ENROLLER";
					break;
					case '12':
						$role = "MANAGER";
					break;
					case '14':
						$role = "SUPER MANAGER";
					break;
					default:
						$role = "---";
					break;
				}

				$badgeStatus = "<span class='m-badge m-badge--metal m-badge--wide m--regular-font-size-sm4 m--font-boldest'>Regular - 2 In & 2 Out</span>";
				if($_getPersonnelCollection["is_flexi"] == 1){
					$badgeStatus = "<span class='m-badge m-badge--info m-badge--wide m--regular-font-size-sm4 m--font-boldest'>Flexible - 1 In & 1 Out</span>";
				}elseif($_getPersonnelCollection["is_flexi"] == 2){
					$badgeStatus = "<span class='m-badge m-badge--warning m-badge--wide m--regular-font-size-sm4 m--font-boldest'>Drivers - 1 In & Out</span>";
				}elseif($_getPersonnelCollection["is_flexi"] == 3){
					$badgeStatus = "<span class='m-badge m-badge--primary m-badge--wide m--regular-font-size-sm4 m--font-boldest'>Super Flexible - 1 In / 1 Out</span>";
				}elseif($_getPersonnelCollection["is_flexi"] == 4){
					$badgeStatus = "<span class='m-badge m-badge--success m-badge--wide m--regular-font-size-sm4 m--font-boldest'>Default - No In / Out</span>";
				}

				$tempDepartment = isset($getDepartmentData["description"]) && $getDepartmentData["description"] ? $getDepartmentData["description"] : "No Assigned Department";
				$noDepartmentClass= isset($getDepartmentData["description"]) && $getDepartmentData["description"] ? 'm--font-bolder':'m--font-boldest m--font-danger';

				$tempLocation = isset($getLocationData["name"]) && $getLocationData["name"] ? $getLocationData["name"] : "No Assigned Location";
				$noLocationClass = isset($getLocationData["name"]) && $getLocationData["name"] ? 'm--font-bolder':'m--font-boldest m--font-danger';
				$formattedColumnName = "<div>
					<p class='mb-2'>{$_getPersonnelCollection["name"]}</p>
					<p class='m--regular-font-size-sm4 mb-0'>Department: <span class='{$noDepartmentClass}'>{$tempDepartment}</span></p>
					<p class='m--regular-font-size-sm4 mb-0'>Biometric Location: <span class='{$noLocationClass}'>{$tempLocation}</span></p>
					<p class='m--regular-font-size-sm5 mb-0'>Role: <span class='m--font-bolder'>{$role} - {$tempState}</span></p>
				</div>";
				$tempShiftSchedule = isset($_getPersonnelCollection["description"]) && $_getPersonnelCollection["description"]? $_getPersonnelCollection["description"]: "<span class='m--font-danger m--font-boldest'>No Assigned Shift</span>";
				
				$formattedShiftSchedule = "<div>
				<p class='mb-2 m--regular-font-size-sm1 m--font-bolder'>{$tempShiftSchedule}</p>
				<p class='mb-0 m--font-bolder'>{$badgeStatus}</p>
				</div>";

				$tempDeviceName = isset($_getPersonnelCollection["mobile_name"]) && $_getPersonnelCollection["mobile_name"] ? $_getPersonnelCollection["mobile_name"]: null;
				$tempDeviceId = isset($_getPersonnelCollection["mobile_id"]) && $_getPersonnelCollection["mobile_id"] ? $_getPersonnelCollection["mobile_id"]: null;

				$formattedColumnDevice = "<div>
				<p class='mb-2 m--regular-font-size-sm2'>Device Name: <span class='m--font-bolder'>{$tempDeviceName}</span></p>
				<p class='mb-2 m--regular-font-size-sm2'>Device Id: <span class='m--font-bolder'>{$tempDeviceId}</span></p>
				</div>";

				$deviceUI = $tempDeviceName && $tempDeviceId ? $formattedColumnDevice : "<span class='m--regular-font-size-sm4 m--font-boldest m--font-danger'>No Mobile Device App</span>";

				$uiLocations = "";
				$locations = explode(",", $getPersonnelSiteLocation);
				if(is_array($locations) && count($locations) > 0){
					foreach ($locations as $loc) {
						$tempLoc = trim(strtoupper($loc));
						if($tempLoc == "N/A"){
							$uiLocations .= "<span class='m--regular-font-size-sm4 m--font-boldest m--font-danger'>No Assigned Location</span>";
						}else{
							$uiLocations .= "<span class='m-badge m-badge--info m-badge--wide m--regular-font-size-sm5 m--font-bolder mx-1 mb-1'>{$tempLoc}</span>";
						}
					}
				}

				$tempSiteLocation = $uiLocations ? $uiLocations: "No Assigned Location";
				$formattedColumnLocation = "<div>
					<p class='mb-0 m--font-bolder'>{$tempSiteLocation}</p>
				</div>";

				$data[] = $_getPersonnelCollection["biometricno"];
				$data[] = $formattedColumnName;
				$data[] = $formattedShiftSchedule;
				/*** $data[] = $getLocationData ? $getLocationData["name"] : "<span style='color: red;'>N/A</span>"; ***/
				$data[] = $deviceUI;
				/*** $data[] = $getDepartmentData ? $getDepartmentData["description"] : "<span style='color: red;'>N/A</span>";
				$data[] = $role; ***/
				$data[] = $formattedColumnLocation;
				/*** $data[] = $isFlexible; ***/
				$data[] = $isActive;

				$_action = '';
				if(in_array('assign', $actions)){
					$_action .= "<a href='javascript:void(0);' class='m-portlet__nav-link btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill btnAssignPersonnelSchedule' data-toggle='tooltip' title='Assign Personnel Schedule' data-id='{$personnelId}'><i class='la la-calendar'></i></a>";
				}
				if(in_array('edit', $actions)){
					$_action .= "<a href='javascript:void(0);' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditPersonnel' data-toggle='tooltip' title='Edit Profile' data-id='{$personnelId}'><i class='la la-edit'></i></a>";
					$_action .= "<a href='".$link."' target='_blank' class='m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='tooltip' title='View Employee Profile'><i class='la la-user'></i></a>";
					$_action .= "<a href='javascript:void(0);' onClick='addSiteLocation({$personnelId})' class='m-portlet__nav-link btn m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='tooltip' title='View Sites'><i class='la la-map-o'></i></a>";
				}
				if(in_array('delete', $actions)){
					$_action .= "<a href='javascript:void(0);' onclick='deletepersonnel({$personnelId})' ";
					$_action .= "class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='tooltip' title='Delete Profile'><i class='la la-trash'></i></a>";
				}

				$data[] = $_action;
				$resultarray[] = $data;
			}
		}

		echo json_encode(array("data"=>$resultarray));
	}

	function get_personnel_schedule(){
		$actions = $this->core_layout->getCurrentActions();
		$post = $this->input->post();
		$resultset = array();

		if(isset($post) && $post){
			$this->db->select("id, shift_id, department_id, location_id, biometricno, name, IF(is_active > 0, 'Active', 'Inactive') as is_active");
			$this->db->from("gcctimeutility.personnel a");
			$this->db->where("id", $post["id"]);
			$query = $this->db->get();
			if($query->num_rows() == 1){
				$row = $query->row();
				$resultset["response"] = true;
				$resultset["data"] = $row;
				$resultset["dropdown"] = $this->getPersonnelDropdownItems();
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}

		echo json_encode($resultset);
	}

	function set_assigned_shift(){
		$post = $this->input->post();
		$resultset = array();

		if(isset($post) && $post){
			$id = $post["id"];
			unset($post["id"]);
			$where = array("id"=>$id);

			$update = $this->db->update("gcctimeutility.personnel", $post, $where);
			if($update){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Employee Shift data has been updated.";
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to update records, error saving data!";
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}

		echo json_encode($resultset);
	}

	function getPersonnelDropdownItems(){
		$resultset = array();
		$this->db->select("id, UPPER(description) as text");
		$this->db->from("gcctimeutility.department");
		$this->db->order_by("text", "ASC");
		$queryDescription = $this->db->get();
		$resultset["query_description"]["results"] = ($queryDescription->num_rows() > 0)? $queryDescription->result(): array();

		$this->db->select("id,  UPPER(name) as text");
		$this->db->from("gcctimeutility.location");
		$this->db->order_by("text", "ASC");
		$queryLocation = $this->db->get();
		$resultset["query_location"]["results"] = ($queryLocation->num_rows() > 0)? $queryLocation->result(): array();

		$this->db->select("id,  UPPER(description) as text");
		$this->db->from("gcctimeutility.shift_schedule");
		$queryShift = $this->db->get();
		$resultset["query_shift"]["results"] = ($queryShift->num_rows() > 0)? $queryShift->result(): array();

		return $resultset;
	}

	public function getSelectPersonnel(){
		$this->core_layout->setPrivilegeName("manage_departments");
		$actions = $this->core_layout->getCurrentActions();

		$post = $this->input->post();
		$resultset = array();
		$resultarray = array();

		$search = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
		$order = "name";
		$dir = (isset($post["order"][0]["dir"]) && $post["order"][0]["dir"])? $post["order"][0]["dir"]: "ASC";

		/* $this->db->from("gcctimeutility.personnel"); */
		/* if(isset($post["dept_id"]) && $post["dept_id"]){
			$this->db->where("department_id", $post["dept_id"]);
			$this->db->or_where("department_id", 0);
		}else{
			$this->db->where("department_id", 0);
		} */
		/* $this->db->like("name", $search, "both");
		$this->db->order_by($order, $dir);
		$query = $this->db->get(); */

		$querySQL = "";
		if(isset($post["dept_id"]) && $post["dept_id"]){
			$querySQL .= "SELECT * FROM `gcctimeutility`.`personnel` WHERE `name` LIKE '%{$search}%' AND `is_active` = 1 ";
			$querySQL .= "AND (`department_id` = {$post["dept_id"]} OR `department_id` = 0) ";
			$querySQL .= "order by `{$order}` {$dir}";
		}else{
			$querySQL .= "SELECT * FROM `gcctimeutility`.`personnel` WHERE `name` LIKE '%{$search}%' ";
			$querySQL .= "AND `department_id` = 0 order by `{$order}` {$dir}";
		}

		$query = $this->db->query($querySQL);

		$getPersonnelCollection = $query->result_array();
		/*** $getPersonnelCollection = $this->crud->getCollection(array(),"gcctimeutility.personnel"); ***/

		if($getPersonnelCollection){
			foreach($getPersonnelCollection as $_getPersonnelCollection){
				$data = array();
				$btn = '';
				if($_getPersonnelCollection["department_id"] == 0){

						$btn = '<button type="button" class="btn btn-success btn-select-personnel-trigg" data-id="'.$_getPersonnelCollection["id"].'"><i class="la la-plus-circle"></i> Select</button>';

				}else{

						$btn = '<button type="button" class="btn btn-danger btn-deselect-personnel-trigg" data-id="'.$_getPersonnelCollection["id"].'"><i class="la la-minus-circle"></i> Deselect</button>';

				}

				$data["chkbox"] = '<input type="checkbox" class="checkSingle" data-id="'.$_getPersonnelCollection["id"].'" name="'.$_getPersonnelCollection["id"].'">';
				$data["employee_name"] = $_getPersonnelCollection["name"];
				$data["action"] = $btn;

				$resultarray[] = $data;
			}
		}

		$resultset["data"] = $resultarray;
		$resultset["total"] = $query->num_rows();
		echo json_encode($resultset);
	}
	public function getAssignedPersonnel($id){
		$this->core_layout->setPrivilegeName("manage_departments");
		$actions = $this->core_layout->getCurrentActions();

		$post = $this->input->post();
		$resultset = array();
		$resultarray = array();

		$search = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
		$order = "name";
		$dir = (isset($post["order"][0]["dir"]) && $post["order"][0]["dir"])? $post["order"][0]["dir"]: "ASC";

		$querySQL = "";
		if($id){
			$querySQL .= "SELECT * FROM `gcctimeutility`.`personnel` WHERE `name` LIKE '%{$search}%' AND `is_active` = 1 ";
			$querySQL .= "AND (`department_id` = {$id} OR `department_id` = 0) ";
			$querySQL .= "order by `{$order}` {$dir}";
		}else{
			$querySQL .= "SELECT * FROM `gcctimeutility`.`personnel` WHERE `name` LIKE '%{$search}%' ";
			$querySQL .= "AND `department_id` = 0 order by `{$order}` {$dir}";
		}

		$query = $this->db->query($querySQL);

		$getPersonnelCollection = $query->result_array();
		/*** $getPersonnelCollection = $this->crud->getCollection(array(),"gcctimeutility.personnel"); ***/

		if($getPersonnelCollection){
			$row=0;
			foreach($getPersonnelCollection as $_getPersonnelCollection){
				$data = array();
				if($_getPersonnelCollection["department_id"] == 0){

				}else{

					$data["employee_name"] = $_getPersonnelCollection["name"];
					$row++;
					$resultarray[] = $data;

				}


			}
		}

		$resultset["data"] = $resultarray;
		$resultset["total"] = $row;
		echo json_encode($resultset);
	}
	public function getSelectPersonnelLoc(){
		$this->core_layout->setPrivilegeName("manage_departments");
		$actions = $this->core_layout->getCurrentActions();

		$post = $this->input->post();
		$resultset = array();
		$resultarray = array();

		$search = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
		$order = "name";
		$dir = (isset($post["order"][0]["dir"]) && $post["order"][0]["dir"])? $post["order"][0]["dir"]: "ASC";

		$querySQL = "";
		if(isset($post["location_id"]) && $post["location_id"]){
			$querySQL .= "SELECT * FROM `gcctimeutility`.`personnel` WHERE `name` LIKE '%{$search}%' AND `is_active` = 1 ";
			$querySQL .= "AND (`location_id` = {$post["location_id"]} OR `location_id` = 0) ";
			$querySQL .= "order by `{$order}` {$dir}";
		}else{
			$querySQL .= "SELECT * FROM `gcctimeutility`.`personnel` WHERE `name` LIKE '%{$search}%' ";
			$querySQL .= "AND `location_id` = 0 order by `{$order}` {$dir}";
		}

		$query = $this->db->query($querySQL);

		$getPersonnelCollection = $query->result_array();

		if($getPersonnelCollection){
			foreach($getPersonnelCollection as $_getPersonnelCollection){
				$data = array();
				$btn = '';
				if($_getPersonnelCollection["location_id"] == 0){

						$btn = '<button type="button" class="btn btn-success btn-select-personnel-trigg" data-id="'.$_getPersonnelCollection["id"].'"><i class="la la-plus-circle"></i> Select</button>';

				}else{

						$btn = '<button type="button" class="btn btn-danger btn-deselect-personnel-trigg" data-id="'.$_getPersonnelCollection["id"].'"><i class="la la-minus-circle"></i> Deselect</button>';

				}

				$data["chkbox"] = '<input type="checkbox" class="checkSingle" data-id="'.$_getPersonnelCollection["id"].'" name="'.$_getPersonnelCollection["id"].'">';
				$data["employee_name"] = $_getPersonnelCollection["name"];
				$data["action"] = $btn;

				$resultarray[] = $data;
			}
		}

		$resultset["data"] = $resultarray;
		$resultset["total"] = $query->num_rows();
		echo json_encode($resultset);
	}
	public function getSelectAssignedLoc($id){
		$this->core_layout->setPrivilegeName("manage_departments");
		$actions = $this->core_layout->getCurrentActions();

		$post = $this->input->post();
		$resultset = array();
		$resultarray = array();

		$search = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
		$order = "name";
		$dir = (isset($post["order"][0]["dir"]) && $post["order"][0]["dir"])? $post["order"][0]["dir"]: "ASC";

		$querySQL = "";
		if($id){
			$querySQL .= "SELECT * FROM `gcctimeutility`.`personnel` WHERE `name` LIKE '%{$search}%' AND `is_active` = 1 ";
			$querySQL .= "AND (`location_id` = {$id} OR `location_id` = 0) ";
			$querySQL .= "order by `{$order}` {$dir}";
		}else{
			$querySQL .= "SELECT * FROM `gcctimeutility`.`personnel` WHERE `name` LIKE '%{$search}%' ";
			$querySQL .= "AND `location_id` = 0 order by `{$order}` {$dir}";
		}

		$query = $this->db->query($querySQL);

		$getPersonnelCollection = $query->result_array();

		if($getPersonnelCollection){
			$row=0;
			foreach($getPersonnelCollection as $_getPersonnelCollection){
				$data = array();

				if($_getPersonnelCollection["location_id"] == $id){
					$data["employee_name"] = $_getPersonnelCollection["name"];
					$resultarray[] = $data;
						$row++;

				}

			}
		}

		$resultset["data"] = $resultarray;
		$resultset["total"] = $row;
		echo json_encode($resultset);
	}
	public function selectPersonnel(){
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("department_id"=>$post["department_id"]),array("id"=>$post["id"]),"gcctimeutility.personnel");

		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function deselectPersonnel()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("department_id"=>0),array("id"=>$post["id"]),"gcctimeutility.personnel");

		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function selectPersonnelLoc(){
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("location_id"=>$post["location_id"]),array("id"=>$post["id"]),"gcctimeutility.personnel");

		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function deselectPersonnelLoc()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("location_id"=>0),array("id"=>$post["id"]),"gcctimeutility.personnel");

		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function syncdata(){
		$resultset = array();
		$device = $this->getDevice();
		/*** include BASEPATH.'\libraries\zklibrary.php'; ***/
		if($device){
			$zk = new ZKLibrary($device["ip_address"], 4370);
			$connected = $zk->connect();
			if($connected){
				$zk->disableDevice();
				$users = $zk->getUser();
				if($users){
					foreach($users as $key=>$user){
						$data = array();
						$data["biometric_id"] = $user[0];
						$data["biometricno"] = $user[0];
						$data["name"] = strtoupper($user[1]);
						$data["role"] = $user[2];
						$data["is_active"] = 1;

						$toAdd = $this->checkPersonnelExist($user[0]);
						if($toAdd){
							$insert = $this->crud->insert($data, "gcctimeutility.personnel");
						}
					}

					$resultset["status"] = true;
				}else{
					$resultset["status"] = false;
				}
			}else{
				$resultset["status"] = false;
			}
		}else{
			$resultset["status"] = false;
		}

		echo json_encode($resultset);
	}

	function update_personnel_details(){
		$resultset = array();
		$this->db->from("gcctimeutility.personnel");
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$counter = 0;
			foreach($query->result() as $rs){
				if(is_numeric($rs->name)){
					$employee = $this->db->get_where("gccmaster.tblemployees", array("biometricno"=>$rs->biometricno));
					if($employee->num_rows() == 1){
						$rowEmp = $employee->row();
						$displayName = "{$rowEmp->firstname} {$rowEmp->lastname}";
						$displayName = trim($displayName);
						$data = array();
						$data["name"] = strtoupper($displayName);

						$where = array();
						$where["id"] = $rs->id;

						$update = $this->db->update("gcctimeutility.personnel", $data, $where);
						if($update){
							$counter++;
						}
					}
				}
			}

			if($counter > 0){
				$this->core_layout->logNotification("Personnel data has been updated.", "success", "gcctimeV2");
				$resultset["toastr_msg"] = "Personnel data has been updated";
				$resultset["response"] = true;
			}else{
				$this->core_layout->logNotification("Personnel data are up to date!", "info", "gcctimeV2");
				$resultset["toastr_msg"] = "Personnel data are up to date!";
				$resultset["response"] = false;
			}
		}else{
			$this->core_layout->logNotification("No personnel data found!", "success", "gcctimeV2");
			$resultset["toastr_msg"] = "No personnel data found!";
			$resultset["response"] = false;
		}

		echo json_encode($resultset);
	}

	function sync_employee_data(){
		$resultset = array();
		$this->db->select("biometric_id, biometricno");
		$this->db->from("gcctimeutility.personnel");
		$query = $this->db->get();
		$addedRecord = 0;
		if($query->num_rows() > 0){
			$arrBiometric = array();
			foreach ($query->result() as $key => $value) {
				if(!in_array($value->biometric_id, $arrBiometric)){
					$arrBiometric[] = $value->biometric_id;
				}
				if(!in_array($value->biometricno, $arrBiometric)){
					$arrBiometric[] = $value->biometricno;
				}
			}

			if($arrBiometric && count($arrBiometric) > 0){
				$this->db->select("id, biometricno, employee_status");
				$this->db->from("gccmaster.tblemployees");
				$this->db->where_not_in("biometricno", $arrBiometric);
				$qTemp = $this->db->get();
				if($qTemp->num_rows() > 0){
					foreach ($qTemp->result() as $key => $value) {
						if($value->biometricno && intval($value->biometricno) > 0){
							$this->db->from("gcctimeutility.personnel");
							$this->db->where("biometric_id", trim($value->biometricno));
							$this->db->or_where("biometricno", trim($value->biometricno));
							$qTempSearch = $this->db->get();
							if($qTempSearch->num_rows() == 0){
								$temp = $this->core_layout->getEmployeeData($value->id);
								$tempName = (object) $temp;
								$tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No assigned name";

								$data = array();
								$data["biometric_id"] = $value->biometricno;
								$data["biometricno"] = $value->biometricno;
								$data["name"] = $tempName;
								$data["role"] = 0;
								$data["is_active"] = strtolower($value->employee_status) == "active"? 1: 0;
								$added = $this->db->insert("gcctimeutility.personnel", $data);
								if($added){ $addedRecord++; }
							}
						}
					}
				}
			}
		}
		if($addedRecord > 0){
			$resultset["response"] = true;
			$resultset["toastr_msg"] = "New data found and added [ {$addedRecord} ] employee record(s).";
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No new record for personnel data update!";
		}

		echo json_encode($resultset);
	}

	function update_personnel_status(){
		$resultset = array();
		$this->db->from("gcctimeutility.personnel");
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$counter = 0;
			foreach($query->result() as $rs){
				$arrStatus = array("Block Listed", "Inactive", "Resign", "Awol", "Terminated", "End of Contract");
				$this->db->from("gccmaster.tblemployees");
				$this->db->where("biometricno", $rs->biometricno);
				$this->db->where_in("employee_status", $arrStatus);
				$employee = $this->db->get();

				if($employee->num_rows() == 1 && $rs->is_active == 1){
					$where = array();
					$where["id"] = $rs->id;
					$where["is_active"] = 1;

					$data = array();
					$data["is_active"] = 0;

					$update = $this->db->update("gcctimeutility.personnel", $data, $where);
					if($update){
						$counter++;
					}
				}
			}

			if($counter > 0){
				$this->core_layout->logNotification("Personnel status has been updated.", "success", "gcctimeV2");
				$resultset["toastr_msg"] = "Personnel status has been updated";
				$resultset["response"] = true;
			}else{
				$this->core_layout->logNotification("Personnel status are up to date!", "info", "gcctimeV2");
				$resultset["toastr_msg"] = "Personnel status are up to date!";
				$resultset["response"] = false;
			}
		}else{
			$this->core_layout->logNotification("No personnel data found!", "error", "gcctimeV2");
			$resultset["toastr_msg"] = "No personnel data found!";
			$resultset["response"] = false;
		}

		echo json_encode($resultset);
	}

	function checkPersonnelExist($biometric_id=null){
		$query = $this->db->get_where("gcctimeutility.personnel", array("biometric_id"=>$biometric_id));
		if($query->num_rows() == 0){
			return true;
		}else{
			return false;
		}
	}

	function deletepersonnel(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post["id"]) && $post["id"]){
			$removed = $this->db->delete("gcctimeutility.personnel", $post);
			if($removed){
				$resultset["response"] = true;
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}

		echo json_encode($resultset);
	}

	function get_personnel_data(){
		$resultset = array();
		$post = $this->input->post();
		if($post){
			$query = $this->db->get_where("gcctimeutility.personnel", array("id"=> $post["id"]));
			if($query->num_rows() == 1){
				$row = $query->row();
				$resultset["data"] = $row;
				$resultset["response"] = true;
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}

		echo json_encode($resultset);
	}

	function update_personnel_data(){
		$resultset = array();
		$post = $this->input->post();
		if($post){
			$id = $post["id"];
			unset($post["id"]);

			$isFlexible = (isset($post["is_flexi"]) && $post["is_flexi"])? intval($post["is_flexi"]) : 0;
			$isActive = (isset($post["is_active"]) && $post["is_active"] == 1)? 1: 0;

			$post["is_flexi"] = $isFlexible;
			$post["is_active"] = $isActive;
			$post["name"] = strtoupper($post["name"]);

			$update = $this->db->update("gcctimeutility.personnel", $post, array("id"=>$id));
			if($update){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Personnel data has been updated.";
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to update personnel data!";
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No data found!";
		}
		echo json_encode($resultset);
	}

	public function ipIsReachable($ip=null){
		if($ip){
			$url = "{$ip}";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if($httpcode>=200 && $httpcode<300){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function syncPersonnelDataMultiDev(){
		$deviceCollection = $this->getdevices();

		if(count($deviceCollection) > 0){
			foreach($deviceCollection as $_deviceCollection){
				$device = $this->getDeviceById($_deviceCollection["id"]);
				$ipAddress = $device["ip_address"];
				$isReachable = $this->ipIsReachable($ipAddress);
				if($isReachable){
					$zk = new ZKLibrary($ipAddress, 4370);

					$zk->connect();
					$users = $zk->getUser();

					foreach($users as $key=>$user){
						$data = array();
						$data["biometric_id"] = $user[0];
						$data["biometricno"] = $user[0];
						$data["name"] = $user[1];
						$data["role"] = $user[2];

						$toAdd = $this->checkPersonnelExist($user[0]);
						if($toAdd){
							$insert = $this->crud->insert($data,"gcctimeutility.personnel");
						}
					}

					$zk->disconnect();
				}
			}
		}
		echo json_encode(array("status"=>"true"));
	}

	function getdevices(){
		$getDeviceCollection = $this->crud->getCollection(array(),"gcctimeutility.devices");
		return count($getDeviceCollection) > 0? $getDeviceCollection : array();
	}

	function getDeviceById($id = NULL){
		$query = $this->crud->load(array("id"=>$id),"gcctimeutility.devices");
		return $query ? $query : false;
	}

	function importGo(){
		$path = site_url()."uploads/files/personnel/SHPersonnels.csv";
		$file = fopen($path,"r");
		while (!feof($file) && $row = fgetcsv($file)) {
			$bio_id = trim($row[0]);
			$name = trim($row[1]);
			$checkbio = $this->crud->load(array("biometric_id"=>$bio_id),"personnel");
			if(!$checkbio){
				$insertAttendance = $this->db->query("INSERT INTO personnel (`biometric_id`,`biometricno`,`name`,`is_active`) VALUES ({$bio_id},{$bio_id},'{$name}',1)");
			}
		}
	}

	function get_all_site(){
		$data = $this->restrict->get_all_site_location();
		$this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
	}

	function personnel_locations(){
		$data = $this->restrict->personnelLocations();
		$this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
	}
}
