<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Temp_attendance extends MY_Controller {
	private $devicesTable = "gcctimeutility.devices";
	private $attendanceTable = "gcctimeutility.attendance";
	private $personnelTable = "gcctimeutility.personnel";
	
	public function __construct(){
		parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->load->model('core/upload_model', 'adm_upload');
		
		$this->core_layout->setBodyClass("attendance attendance_masterfile");
		$this->core_layout->setPrivilegeName("attendance_masterfile");
		
		$this->core_layout->addJs("plugins/moment_js/moment.min.js");
		$this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
		$this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
		
		date_default_timezone_set("Asia/Taipei");
	}

	public function index(){
		$this->load->view('core/templates/header');
		$this->load->view("gcctime/temp_attendance/index");
		$this->load->view('core/templates/footer');
	}

	public function getattendance(){
		$post = $this->input->post();
		$resultData = array();
			
		$search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
		$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
		$offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

		$filterFields = array("b.firstname", "b.lastname", "a.date");
		
		$this->db->order_by("id", "DESC");
		$this->db->select("a.*, CONCAT(b.firstname,' ',b.lastname) as name, c.images");
		$this->db->from("gcctimeutility.app_attendance a");
		$this->db->join("gccmaster.tblemployees b","b.biometricno = a.biometric_id", "LEFT");
		$this->db->join("gcctimeutility.mass_attendance_images c","c.app_attendance_id = a.id", "LEFT");

		if ($search) {
			$this->db->group_start();
			foreach ($filterFields as $key => $field) {
				if ($key == 0) {
					$this->db->like($field, $search, "both");
				} else {
					$this->db->or_like($field, $search, "both");
				}
			}
			$this->db->group_end();
		}
		
		if($limit != -1){
			$this->db->limit($limit, $offset);
		}
		
		$this->db->order_by("a.updated_at","DESC");

		$query = $this->db->get();

		foreach($query->result_array() as $row){

			$data = array();
			$data["biometric_id"] = $row["biometric_id"];
			$data["name"] = $row["name"];
			$data["address"] = $row["address"] . ' <a href="https://www.google.com/maps/@' . $row['latitude'] . ',' . $row['longtitude'] . ',21z" target="_blank">' . $row['latitude'] . ',' . $row['longtitude'] . '</a>';
			$data["time"] = date('h:i:s a',strtotime($row["time"]));
			$data["date"] = $row["date"];

			if($row["images"]){
				$data["image"] = "<img style='transform: rotate(90deg);' onclick='imgView(".$row["id"].")' src='".base_url('androidapp/storage/mass_attendance_storage/'.$row['images'])."' height='80' width='80'>";
			}else{
				$data["image"] = "<img onclick='imgView(".$row["id"].")' src='".base_url('androidapp/assets/img/default/user.png')."' height='80' width='80'>";
			}

			$resultData[] = $data;
		}

		$rowCount = $this->getattendanceCount($search, $filterFields);
		echo json_encode(array("data"=>$resultData, "recordsTotal"=>$rowCount, "recordsFiltered"=>$rowCount));
	}

	function getattendanceCount($search, $filterFields){
		$this->db->select("a.id");
		$this->db->from("gcctimeutility.app_attendance a");
		$this->db->join("gccmaster.tblemployees b","b.biometricno = a.biometric_id", "LEFT");

		if ($search) {
			$this->db->group_start();
			foreach ($filterFields as $key => $field) {
				if ($key == 0) {
					$this->db->like($field, $search, "both");
				} else {
					$this->db->or_like($field, $search, "both");
				}
			}
			$this->db->group_end();
		}

		$query = $this->db->get();
		return $query->num_rows();
	}

	public function getImage(){
		$post = $this->input->post();
		$app_attendance_id = $post['img_id'];
		if(isset($post['img_id']) && $app_attendance_id != null){
			$this->db->where('app_attendance_id', $app_attendance_id);
			$img = $this->db->get("gcctimeutility.mass_attendance_images");
			$image = $img->row_array();
			if($img->num_rows() != 0){
				echo "<img style='transform: rotate(90deg); width: 100%;' src='".base_url('androidapp/storage/mass_attendance_storage/'.$image['images'])."'>";
			}else{
				echo "<img style='width: 100%;' src='".base_url('androidapp/assets/img/default/user.png')."'>";
			}
		}
	}

	private function getAllMassImg($id){
		$this->db->select("images");
		$this->db->where('app_attendance_id', $id);
		$img = $this->db->get("gcctimeutility.mass_attendance_images");
		$image = $img->row_array();
		if($img->num_rows() != 0){
			return "<img style='transform: rotate(90deg);' onclick='imgView(".$id.")' src='".base_url('androidapp/storage/mass_attendance_storage/'.$image['images'])."' height='80' width='80'>";
		}else{
			return "<img onclick='imgView(".$id.")' src='".base_url('androidapp/assets/img/default/user.png')."' height='80' width='80'>";
		}
	}
	
}
