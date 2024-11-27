<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Shift extends MY_Controller {
	public function __construct()
	{
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->core_layout->setBodyClass("configuration");
		$this->core_layout->setPrivilegeName("gcctime_shifts");
		
		$this->load->model("Shift_schedule_model", "shift_schedule");
	}

	public function index(){
		$this->core_layout->setPrivilegeName("gcctime_shifts");
		$this->load->view('core/templates/header');
		$this->load->view('shift/index');
		$this->load->view('core/templates/footer');
	}
	
	function schedule_calendar(){
		$this->core_layout->setBodyClass("configuration shift_calendar");
		$this->core_layout->setPrivilegeName("gcctime_shift_calendar");
		
		$this->core_layout->addCss("plugins/fullcalendar/fullcalendar.bundle.css");
		$this->core_layout->addJs("plugins/fullcalendar/fullcalendar.bundle.js");
		
		$this->load->view('core/templates/header');
		$this->load->view('shift/schedule/calendar/index');
		$this->load->view('core/templates/footer');
	}
	
	function set_calendar_schedules(){
		$data = $this->shift_schedule->setCalendarSchedule();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_calendar_schedules(){
		$data = $this->shift_schedule->getCalendarSchedules();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_calendar_data(){
		$data = $this->shift_schedule->getCalendarData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	public function getshift()
	{
		$post = $this->input->post();
		$query =  $this->crud->load($post,"gcctimeutility.shifts");

		echo json_encode($query);
	}

	public function getCollection(){
		$actions = $this->core_layout->getCurrentActions();
		$resultarray = array();
		$query = $this->crud->getCollection(array(),"gcctimeutility.shifts");

		if($query){
			foreach($query as $_query)
			{
				$data = array();

				$data[] = $_query["name"];
				$data[] = $_query["am_start"];
				$data[] = $_query["am_end"];
				$data[] = $_query["pm_start"];
				$data[] = $_query["pm_end"];
				
				$active = '<span class="btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm"><i class="la la-check"></i></span>';
				$inactive = '<span class="btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm"><i class="la la-close"></i></span>';
				$data[] = (isset($_query["status"]) && $_query["status"] == 1)? $active: $inactive;
				
				$_actions = "";
				if(in_array('edit', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='edit_shift(".$_query["id"].")' title='Edit'><i class='la la-edit'></i></a>";					
				}
				if(in_array('delete', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btn-delete' data-id='".$_query["id"]."' title='Delete'><i class='la la-trash-o'></i></a>";					
				}
				if(in_array('assign', $actions)){
					$_actions .= "<a href='javascript:void(0)' class='m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btn-dept' data-id='".$_query["id"]."' title='Select Departments'><i class='la la-list'></i></a>";					
				}
				
				$data[] = $_actions;
				$resultarray[] = $data;
			}
		}

		echo json_encode(array("data"=>$resultarray));
	}

	public function selectDeptforshift()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("shift_id"=>$post["shift_id"]),array("id"=>$post["dept_id"]),"gcctimeutility.department");
		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function deselectDeptforshift()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("shift_id"=>0),array("id"=>$post["dept_id"]),"gcctimeutility.department");
		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function selectshift()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("shift_id"=>$post["shift_id"]),array("id"=>$post["id"]),"gcctimeutility.department");

		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function deselectshift()
	{
		$post = $this->input->post();
		$resultarray = array();
		$query = $this->crud->update(array("shift_id"=>0),array("id"=>$post["id"]),"gcctimeutility.department");

		if($query){
			$resultarray["status"] = TRUE;
		}else{
			$resultarray["status"] = FALSE;
		}

		echo json_encode($resultarray);
	}

	public function newshift()
	{
		$post = $this->input->post();
		$resultarray = array();

		$query = $this->crud->insert($post,"gcctimeutility.shifts");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully saved!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);

	}

	public function updateshift()
	{
		$post = $this->input->post();
		$id = $post["id"];
		unset($post["id"]);
		$resultarray = array();

		$query = $this->crud->update($post,array("id"=>$id),"gcctimeutility.shifts");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully updated!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}

	public function deleteshift()
	{
		$post = $this->input->post();

		$query = $this->crud->delete($post,"gcctimeutility.shifts");

		if($query){
			$resultarray["status"] = TRUE;
			$resultarray["message"] = "Data successfully deleted!";
		}else{
			$resultarray["status"] = FALSE;
			$resultarray["message"] = "Error processing request!";
		}

		echo json_encode($resultarray);
	}
	function schedule(){
		$this->core_layout->setPrivilegeName("gcctime_shift");
		$this->load->view('core/templates/header');
		$arrData = array();
		$arrData["list_list"] = $this->shift_schedule->getListLate();
		$arrData["list_undertime"] = $this->shift_schedule->getListUndertime();
		
		$this->load->view('shift/schedule/index', $arrData);
		$this->load->view('core/templates/footer');
	}
	
	function schedule_list(){
		$this->core_layout->setPrivilegeName("gcctime_shift_list");
		$this->load->view('core/templates/header');
		$this->load->view('shift/schedule_list/index');
		$this->load->view('core/templates/footer');
	}
	
	function deselect_personnel(){
		$data = $this->shift_schedule->deselectPersonnel();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function select_personnel(){
		$data = $this->shift_schedule->selectPersonnel();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_employee_shift(){
		$data = $this->shift_schedule->getEmployeeShiftData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_employee_assigned($id=null){
		$data = $this->shift_schedule->getAssigned($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_shift_schedule($id){
		$data = $this->shift_schedule->getShiftSchedule($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	function get_shift_schedule_data(){
		$data = $this->shift_schedule->getShiftScheduleData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_shift_schedule_data_list(){
		$data = $this->shift_schedule->getShiftScheduleDataList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function get_shift_schedule_list(){
		$data = $this->shift_schedule->getShiftScheduleList();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function add_shift_schedule(){
		$data = $this->shift_schedule->addShiftSchedule();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	function edit_shift_schedule(){
		$data = $this->shift_schedule->editShiftSchedule();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	function add_shift_schedule_data(){
		$data = $this->shift_schedule->addShiftScheduleData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_shift_schedule_item(){
		$data = $this->shift_schedule->getShiftScheduleItem();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	
	function save_shift_schedule(){
		$data = $this->shift_schedule->saveShiftSchedule();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function update_shift_schedule_data(){
		$data = $this->shift_schedule->updateShiftScheduleData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function get_shift_schedule_data_items(){
		$data = $this->shift_schedule->getShiftScheduleDataItems();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function update_shift_schedule(){
		$data = $this->shift_schedule->updateShiftSchedule(); 
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}
	function remove_shift_schedule_data(){
		$data = $this->shift_schedule->removeShiftScheduleData(); 
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
		
	}
	function remove_shift_schedule_data_item(){
		$data = $this->shift_schedule->removeShiftScheduleDataItem(); 
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
		
	}

	function get_custom_shift_schedule(){
		$data = $this->shift_schedule->getCustomShiftSchedule(); 
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	function get_personnel_items(){
		$data = $this->shift_schedule->getPersonnelItems(); 
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

	function add_custom_shift_schedule(){
		$data = $this->shift_schedule->addCustomShiftSchedule(); 
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
	}

}