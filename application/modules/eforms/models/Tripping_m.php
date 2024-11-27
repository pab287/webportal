<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tripping_m extends CI_Model {
    protected $eformsTable = "gcceforms";

    public function __construct() {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        $this->load->model("core/upload_model", "file_upload");
        $this->load->library('image_lib');
        date_default_timezone_set("Asia/Manila");
        $this->load->helper('download');
    }

    private function getUserdata(){
       return $this->session->userdata('logged_in');
    }

    function getDriversCollection(){

        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%') ORDER BY firstname ASC LIMIT 10");
        } else {
            $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC LIMIT 10");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $tempRs = (array)$_query;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;

                $data["id"] = $_query["id"];
                $data["text"] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    function getDriversVehicle(){ 
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT id, `name`, gen_code, plateno FROM gccasset.vehicles WHERE  plateno IS NOT NULL AND (status2='brandnew' OR status2='operational') AND (asset_category = 'HE' AND isCompo = 0) AND (motherID = 0 AND isCompo = 0) AND (plateno LIKE '%{$get['q']}%' OR `name` LIKE '%{$get['q']}%') ORDER BY gen_code DESC LIMIT 10");
        } else {
            $query = $this->db->query("SELECT id, `name`, gen_code, plateno FROM gccasset.vehicles WHERE plateno IS NOT NULL AND status2='brandnew' OR status2='operational' AND asset_category = 'HE' AND motherID = 0 AND isCompo = 0 AND (gen_code !='' OR gen_code !=NULL) ORDER BY gen_code DESC LIMIT 10");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data["id"] = $_query["id"];
                $data["text"] = $_query["plateno"] ." | ". $_query["name"];
                $resultarray[] = $data;
            }
        }

        return array("results" => $resultarray);
    }

    function newDriver(){
        $resultarray = array();
        $post = $this->input->post();
        $post["status"] = 1;
        $post["created_by"] = $this->getUserdata()["emp_id"];

        if(!$this->checkDriverCode($post["code"])){
            $query = $this->db->insert('trippings.drivers', $post);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Driver successfully saved.";
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error processing request.";
            }
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Driver code already exist";
        }

        
        return $resultarray;
    }

    function checkDriverCode($code){
        $this->db->where('code',$code);
        $query = $this->db->get('trippings.drivers');
        if ($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function getDriverCollection(){
        $resultarray = array();

        $qry = "SELECT drivers.id, CONCAT(emp.firstname,' ',emp.lastname) AS drivername, drivers.code, drivers.type, vehicle.description, drivers.status 
                FROM trippings.drivers AS drivers 
                LEFT JOIN gccmaster.tblemployees AS emp ON emp.id = drivers.emp_id  
                LEFT JOIN gccasset.vehicles AS vehicle ON vehicle.id = drivers.vehicle_id
                WHERE drivers.status = 1";

        $query = $this->db->query($qry);
        return array("data"=>$query->result_array());
    }

    function getDriverData(){
        $post = $this->input->post();

        $qry = "SELECT drivers.id, CONCAT(emp.firstname,' ',emp.lastname) AS drivername, drivers.code, drivers.type, drivers.vehicle_id, vehicle.description, drivers.status 
                FROM trippings.drivers AS drivers 
                LEFT JOIN gccmaster.tblemployees AS emp ON emp.id = drivers.emp_id  
                LEFT JOIN gccasset.vehicles AS vehicle ON vehicle.id = drivers.vehicle_id
                WHERE drivers.status = 1 AND drivers.id = '{$post['id']}'";

        $query = $this->db->query($qry);
        return $query->row_array();
    }

    function updateDriver(){
        $resultarray= array();
        $post = $this->input->post();
        $id = $post["id"];
        unset($post["id"]);
        $this->db->where('id', $id);
        $query = $this->db->update('trippings.drivers', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Driver successfully updated.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error processing request.";
        }

        return $resultarray;
    }

    function getDriverSelect2(){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT driver.id, driver.code, emp.firstname, emp.lastname  FROM trippings.drivers AS driver LEFT JOIN gccmaster.tblemployees AS emp ON emp.id=driver.emp_id WHERE (emp.firstname LIKE '%{$get['q']}%' OR emp.lastname LIKE '%{$get['q']}%') ORDER BY emp.firstname ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT driver.id, driver.code, emp.firstname, emp.lastname  FROM trippings.drivers AS driver LEFT JOIN gccmaster.tblemployees AS emp ON emp.id=driver.emp_id ORDER BY emp.firstname ASC LIMIT 10");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data["id"] = $_query["code"];
                $data["text"] = $_query["firstname"] . " " .$_query["lastname"];
                $resultarray[] = $data;
            }
        }

        return array("results" => $resultarray);
    }

    function getCompany(){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT * FROM gcchris.tblcompanies WHERE `description` LIKE '%{$get['q']}%' ORDER BY `description` ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT * FROM gcchris.tblcompanies ORDER BY `description` ASC LIMIT 10");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data["id"] = $_query["id"];
                $data["text"] = $_query["description"];
                $resultarray[] = $data;
            }
        }

        return array("results" => $resultarray);
    }

    function newProject(){
        $resultarray = array();
        $post = $this->input->post();
        $post["status"] = 1;
        $post["created_by"] = $this->getUserdata()["emp_id"];


        if(!$this->checkProjectCode($post["name"])){
            $query = $this->db->insert('trippings.projects', $post);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Project successfully saved.";
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error processing request.";
            }
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Project name already exist";
        }

        
        return $resultarray;
    }

    function checkProjectCode($code){
        $this->db->where('code',$code);
        $query = $this->db->get('trippings.projects');
        if ($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function getProjectCollection(){
        $resultarray = array();
        
        $this->db->select("project.id, project.code, project.name, project.status, comp.description, project.company_id");
        $this->db->from("trippings.projects as project");
        $this->db->join("gcchris.tblcompanies as comp","comp.id = project.company_id");
        $this->db->where("project.status", 1);
        $query = $this->db->get();
        return array("data"=>$query->result_array());
    }

    function getProjectData(){
        $post = $this->input->post();
        $this->db->select("project.id, project.code, project.name, project.status, comp.description, project.company_id");
        $this->db->from("trippings.projects as project");
        $this->db->join("gcchris.tblcompanies as comp","comp.id = project.company_id");
        $this->db->where("project.status", 1);
        $this->db->where("project.id", $post["id"]);
        $query = $this->db->get();
        return $query->row_array();
    }

    function updateProject(){
        $resultarray= array();
        $post = $this->input->post();
        $id = $post["id"];
        unset($post["id"]);
        $this->db->where('id', $id);
        $query = $this->db->update('trippings.projects', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Project successfully updated.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error processing request.";
        }

        return $resultarray;
    }

    function newRoute(){
        $resultarray = array();
        $post = $this->input->post();
        $post["status"] = 1;
        $post["created_by"] = $this->getUserdata()["emp_id"];

        if(!$this->checkRouteCode($post["code"])){
            $query = $this->db->insert('trippings.routes', $post);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Route successfully saved.";
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error processing request.";
            }
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Route name already exist";
        }
        
        return $resultarray;
    }

    function checkRouteCode($code){
        $this->db->where('code',$code);
        $query = $this->db->get('trippings.routes');
        if ($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function getRouteCollection(){
        $this->db->select("*");
        $this->db->from("trippings.routes");
        $this->db->where("status",1);
        $query = $this->db->get();
        return array("data"=>$query->result_array());
    }

    function getRouteData(){
        $post = $this->input->post();
        $this->db->select("*");
        $this->db->from("trippings.routes");
        $this->db->where("id",$post["id"]);
        $query = $this->db->get();
        return $query->row_array();
    }

    function updateRoute(){
        $resultarray= array();
        $post = $this->input->post();
        $id = $post["id"];
        unset($post["id"]);
        $this->db->where('id', $id);
        $query = $this->db->update('trippings.routes', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Project successfully updated.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error processing request.";
        }

        return $resultarray;
    }

    function getRoutes(){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT * FROM trippings.routes WHERE `name` LIKE '%{$get['q']}%' ORDER BY `name` ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT * FROM trippings.routes ORDER BY `name` ASC LIMIT 10");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data["id"] = $_query["id"];
                $data["text"] = $_query["name"];
                $resultarray[] = $data;
            }
        }

        return array("results" => $resultarray);
    }

    function getRoutesCode(){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT * FROM trippings.routes WHERE `name` LIKE '%{$get['q']}%' ORDER BY `name` ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT * FROM trippings.routes ORDER BY `name` ASC LIMIT 10");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data["id"] = $_query["code"];
                $data["text"] = $_query["name"];
                $resultarray[] = $data;
            }
        }

        return array("results" => $resultarray);
    }

    function getProjects(){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT * FROM trippings.projects WHERE `name` LIKE '%{$get['q']}%' ORDER BY `name` ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT * FROM trippings.projects ORDER BY `name` ASC LIMIT 10");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data["id"] = $_query["id"];
                $data["text"] = $_query["name"];
                $resultarray[] = $data;
            }
        }

        return array("results" => $resultarray);
    }

    function getProjectsReport(){
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT * FROM trippings.projects WHERE `name` LIKE '%{$get['q']}%' ORDER BY `name` ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT * FROM trippings.projects ORDER BY `name` ASC LIMIT 10");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data["id"] = $_query["code"];
                $data["text"] = $_query["name"];
                $resultarray[] = $data;
            }
        }

        return array("results" => $resultarray);
    }

    function newRate(){
        $resultarray = array();
        $post = $this->input->post();
        $post["status"] = 1;
        $post["created_by"] = $this->getUserdata()["emp_id"];

        $query = $this->db->insert('trippings.rates', $post);
        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Rate successfully saved.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error processing request.";
        }
        
        return $resultarray;
    }

    function getRateCollection(){
        $this->db->select("rates.id,dep.name as from, des.name as to, rates.rate, rates.driver_type, rates.status, projects.name as project_name");
        $this->db->from("trippings.rates");
        $this->db->join("trippings.projects","projects.id = rates.project_id");
        $this->db->join("trippings.routes dep","dep.id = rates.from");
        $this->db->join("trippings.routes des","des.id = rates.to");
        $query = $this->db->get();

        return array("data"=>$query->result_array());
    }

    function getRateData(){
        $post = $this->input->post();
        $this->db->select("rates.id,dep.name as fromdesc, des.name as todesc, rates.from, rates.to, rates.rate, rates.project_id, rates.driver_type, rates.status, projects.name as project_name");
        $this->db->from("trippings.rates");
        $this->db->join("trippings.projects","projects.id = rates.project_id");
        $this->db->join("trippings.routes dep","dep.id = rates.from");
        $this->db->join("trippings.routes des","des.id = rates.to");
        $this->db->where("rates.status",$post["id"]);
        $query = $this->db->get();

        return $query->row_array();
    }

    function updateRate(){
        $resultarray= array();
        $post = $this->input->post();
        $id = $post["id"];
        unset($post["id"]);
        $this->db->where('id', $id);
        $query = $this->db->update('trippings.rates', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Rate successfully updated.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error processing request.";
        }

        return $resultarray;
    }

    function tempUploadFile() {
        $resultset = array();
        $uploadPath = "./uploads/files/trippings";
        $createFilePath = false;

            if (!file_exists($uploadPath)) {
                $mkdir = mkdir($uploadPath, 0777, true);
                if ($mkdir) {
                    $createFilePath = true;
                }
            } else {
                $createFilePath = true;
            }

            if ($createFilePath == false) {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                $resultset["toastr_state"] = "warning";
            } else {
                $config = array();
                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'csv|CSV';
                $config['max_size'] = 10000;

                $data = $this->file_upload->uploadFile($config);
                if ($data["response"] == true) {
                    $files = $data["files"][0];
                    $resultarray = array();
                    $csv = array_map('str_getcsv', file($files['full_path']));

                    foreach($csv as $k=>$v):
                        if($k != 0):
                            $data = array();
                            $data["reference_no"] = $v[0];
                            $data["project"] = $v[1];
                            $data["date"] = date_format(date_create($v[2]),"Y-m-d");
                            $data["time"] = $v[3];
                            $data["driver"] = $v[4];
                            $data["origin"] = $v[5];
                            $data["destination"] = $v[6];
                            if(!$this->checkRefNo($v[0])):
                                $this->db->insert('trippings.trips', $data);
                            endif;
                            $resultarray[] = $v;
                        else:
                            continue;
                        endif;
                    endforeach;

                    $resultset['csv'] = $resultarray;
                    $resultset['data'] = $files;
                    $resultset["response"] = TRUE;
                    $resultset["toastr_msg"] = "Import successful.";
                    $resultset["toastr_state"] = "success";
                } else {
                    $resultset["response"] = FALSE;
                    $resultset["toastr_msg"] = "Import failed!";
                    $resultset["toastr_state"] = "error";
                }
            }
        return $resultset;
    }

    function checkRefNo($refno){
        $this->db->where('reference_no',$refno);
        $query = $this->db->get('trippings.trips');
        if ($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function generateReport(){
        $post = $this->input->post();
        $resultarray = array();
        $resultset = array();
        $dates = explode(" - ", $post["date"]);
        $this->db->select("trips.reference_no, trips.project,trips.date, trips.time, trips.driver,trips.origin, trips.destination, project.id as project_id, driver.id as driver_id, driver.type, from.id as from_id, to.id as to_id, CONCAT(emp.firstname,' ',emp.lastname) as driver_name, from.name as from_name, to.name as to_name, project.name as project_name");
        $this->db->from('trippings.trips as trips');
        $this->db->join("trippings.projects as project","project.code=trips.project", "LEFT");
        $this->db->join("trippings.drivers as driver","driver.code=trips.driver", "LEFT");
        $this->db->join("trippings.routes as from","from.code=trips.origin", "LEFT");
        $this->db->join("trippings.routes as to","to.code=trips.destination", "LEFT");
        $this->db->join("gccmaster.tblemployees as emp","emp.id=driver.emp_id", "LEFT");
        $this->db->where('date >=', $dates[0]);
        $this->db->where('date <=', $dates[1]);
        if(isset($post["driver_code"])){
            $this->db->where('driver', $post["driver_code"]);
        }
        if(isset($post["project_code"])){
            $this->db->where('project', $post["project_code"]);
        }
        $this->db->group_by("trips.reference_no");
        $query = $this->db->get();

        if($query->num_rows() > 1):
            foreach($query->result_array() as $_query):
                $data = array();
                $data[] = $_query["reference_no"];
                $data[] = $_query["project_name"];
                $data[] = $_query["date"];
                $data[] = $_query["time"];
                $data[] = $_query["driver_name"];
                $data[] = $_query["from_name"];
                $data[] = $_query["to_name"];
                $data[] = $this->getRatePerTrip($_query["from_id"],$_query["to_id"],$_query["project_id"],$_query["type"],$_query["time"])["rate"];

                $resultarray[] = $data;
            endforeach;

            $resultset["data"] = $resultarray;
            $resultset["restarray"] = $query->result_array();
            $resultset["response"] = TRUE;
        else:
            $resultset["response"] = FALSE;
            $resultset["toastr_msg"] = "Error requesting data.";
        endif;


        return $resultset;
    }

    function getRatePerTrip($from_id, $to_id, $project_id, $driver_type, $period){
        $this->db->select("rates.rate");
        $this->db->from("trippings.rates");
        
        $this->db->where("rates.project_id",$project_id);
        $this->db->where("rates.driver_type",$driver_type);
        if($driver_type == "new"){
            $this->db->where("rates.period",$period);
        }else{
            $this->db->where("rates.period","na");
            $this->db->where("rates.from",$from_id);
            $this->db->where("rates.to",$to_id);
        }
        
        $query = $this->db->get();
        //var_dump($this->db->last_query());
        return $query->row_array();
    }

    function downloadTemplate(){
        $post = $this->input->post();

        $this->db->query("TRUNCATE TABLE trippings.custom_template");
        
        if(isset($post)){
            $this->db->insert('trippings.custom_template', $post);
        }

        $this->db->select('reference_no,project,date,time,driver,origin,destination');
		$q = $this->db->get('trippings.custom_template');
		$response = $q->result_array();

       return $q;
    }

    function saveRental(){
        $resultarray = array();
        $post = $this->input->post();
        $post['date'] = date_format(date_create($post['date']), "Y-m-d");

        $query = $this->db->insert('trippings.rentals', $post);
        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Rental successfully saved.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error processing request.";
        }

        return $resultarray;
    }

    function newRentalRate(){
        $resultarray = array();
        $post = $this->input->post();

        $query = $this->db->insert('trippings.rental_rates', $post);
        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Rental successfully saved.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error processing request.";
        }

        return $resultarray;
    }

    function getRentalRateCollection(){
        $this->db->select("r.id, v.plateno, r.rate");
        $this->db->from("trippings.rental_rates as r");
        $this->db->join("gccasset.vehicles as v","v.id = r.unit");
        $query = $this->db->get();

        return array("data"=>$query->result_array());
    }

    function getRentalRateData(){
        $post = $this->input->post();
        $this->db->select("r.id as rate_id, v.id as vehicle_id, v.name, v.plateno, r.rate");
        $this->db->from("trippings.rental_rates as r");
        $this->db->join("gccasset.vehicles as v","v.id = r.unit");
        $this->db->where("r.id",$post['id']);
        $query = $this->db->get();

        return $query->row_array();
    }

    function generateRentalReport(){
        $post = $this->input->post();
        $dates = explode(" - ", $post["date"]);
        $resultarray = array();
        $this->db->select("rentals.id, rentals.reference_no, rentals.date, CONCAT(emp.firstname, ' ', emp.lastname) as driver_name, rentals.hours, v.plateno, v.name, rr.rate");
        $this->db->from("trippings.rentals as rentals");
        $this->db->join("trippings.drivers as drivers", "drivers.code = rentals.driver");
        $this->db->join("gccmaster.tblemployees as emp", "emp.id = drivers.emp_id");
        $this->db->join("gccasset.vehicles as v", "v.id = rentals.unit");
        $this->db->join("trippings.rental_rates as rr", "rr.unit = rentals.unit");
        $this->db->where('date >=', $dates[0]);
        $this->db->where('date <=', $dates[1]);
        if(isset($post["driver_code"])){
            $this->db->where('driver', $post["driver"]);
        }
        $this->db->group_by("rentals.reference_no");
        $query = $this->db->get();

        if($query->num_rows() > 1):
            foreach($query->result_array() as $_query):
                $data = array();
                
                $data[] = $_query["plateno"] . " | " .$_query["name"];
                $data[] = $_query["reference_no"];
                $data[] = $_query["date"];
                $data[] = $_query["driver_name"];
                $data[] = $_query["hours"]  ."hrs x ". $_query["rate"]." (rate/hr)";
                $data[] = $_query["rate"] * $_query["hours"];
                $resultarray[] = $data;
            endforeach;

            $resultset["data"] = $resultarray;
            $resultset["restarray"] = $query->result_array();
            $resultset["response"] = TRUE;

        else:
            $resultset["toastr_msg"] = "Error requesting data.";
            $resultset["response"] = FALSE;
        endif;
            
        return $resultset;
    }


}