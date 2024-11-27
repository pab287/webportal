<?php defined('BASEPATH') || exit('No direct script access allowed');

    class ExternalForm_model extends CI_Model {

        protected $tbldependents = "gcchris.tbldependents";
        protected $tbleducations = "gcchris.tbleducations";
        protected $tbllicenses = "gcchris.tbllicenses";
        protected $tbldriverlicense = "gcchris.tbldriverlicense";
        protected $tblworkxps = "gcchris.tblworkxps";
        protected $tblawards = "gcchris.tblawards";
        protected $tblorganizations = "gcchris.tblorganizations";
        protected $tbltrainings = "gcchris.tbltrainings";
        protected $tblreferences = "gcchris.tblreferences";
        protected $tblmedrecs = "gcchris.tblmedrecs";
        protected $tblskills = "gcchris.tblskills";

        function __construct() {
            parent::__construct();
        }
        
        public function getPersonnelRequestData(){
            $post = $this->input->post();

            $this->db->select("comp.description as company, pos.name as position, dept.description as department, pr.id, pr.status");
            $this->db->from("gcchris.tbapplication pr");
            $this->db->join("gcchris.tblcompanies comp", "comp.id = pr.company_id", "LEFT");
            $this->db->join("gcchris.tblposition pos", "pos.id = pr.position_id", "LEFT");
            $this->db->join("gcchris.tbldepartments dept", "dept.id = pr.department_id", "LEFT");
            $this->db->where("pr.id", $post['id']);
            $this->db->where("pr.status", "Ongoing");

            $query = $this->db->get();
            return $query->result();
        }

        public function saveDataBackup(){
            $application_details = $this->db->get_where('gcchris.tbapplication', array("id"=>$personnel_request_id))->row();
            unset($post['personnel_request_id']);
            $post['employee_status'] = "For Approval";
            $post['company_id'] = $application_details->company_id;
            $post['department_id'] = $application_details->department_id;
            $post['position'] = $application_details->position_id;
            if($application_details->current > 0){
                $insert = $this->db->insert("gccmaster.tblemployees", $post);
                if($insert){
                    $current = $application_details->current;
                    $needed = $application_details->people_no;
                    $total_current = $current + 1;
                    $emp_id = $this->db->insert_id();
                    
                    if($total_current == $needed){
                        $status = "Completed";
                        $update_arr = array(
                            "current" => $total_current,
                            "status" => $status
                        );
                    }else{
                        $update_arr = array(
                            "current" => $total_current,
                        );
                    }
                    

                    $this->db->update("gcchris.tbapplication", $update_arr, array("id"=>$personnel_request_id));

                }

                $response['message'] = "Success.";
                $response['alert'] = 'success';
                $response['emp_id'] = $emp_id;
            }else{
                $response['message'] = "Position already full.";
                $response['alert'] = 'failed';
                $response['emp_id'] = $emp_id;
            }

            return $response;
        }

        public function saveData(){
            $response = array();
            $post = $this->input->post();
            $personnel_request_id = $post['personnel_request_id'];
            $emp_id = 0;
            
            
            if(isset($post['id']) && $post['id']){
                $emp_id = $post['id'];
                unset($post['personnel_request_id']);
                $post['prov_addr'] = (isset($post['same-to-permanent']) && $post['same-to-permanent'] == 'on') ? $post['curr_addr'] : $post['prov_addr'];
                unset($post['same-to-permanent'], $post['emp_id']);

                $this->db->where('id', $emp_id);
                $query = $this->db->update("gccmaster.tblemployees", $post);
                if($query){
                    $response['message'] = "Updated";
                    $response['alert'] = 'success';
                    $response['emp_id'] = $emp_id;
                }else{
                    $response['message'] = "Position already full.";
                    $response['alert'] = 'failed';
                    $response['emp_id'] = $emp_id;
                }
            }else{
                if($this->verify_employee($post['firstname'], $post['lastname'])){
                    $application_details = $this->db->get_where('gcchris.tbapplication', array("id"=>$personnel_request_id))->row();
                    unset($post['personnel_request_id']);
                    $post['employee_status'] = "For Approval";
                    $post['company_id'] = $application_details->company_id;
                    $post['department_id'] = $application_details->department_id;
                    $post['position'] = $application_details->position_id;
                    $post['prov_addr'] = (isset($post['same-to-permanent']) && $post['same-to-permanent'] == 'on') ? $post['curr_addr'] : $post['prov_addr'];
                    unset($post['same-to-permanent']);
                    if($application_details->current != $application_details->people_no){
                        $insert = $this->db->insert("gccmaster.tblemployees", $post);
                        if($insert){
                            $current = $application_details->current;
                            $needed = $application_details->people_no;
                            $total_current = $current + 1;
                            $emp_id = $this->db->insert_id();
                            
                            if($total_current == $needed){
                                $status = "Completed";
                                $update_arr = array(
                                    "current" => $total_current,
                                    "status" => $status
                                );
                            }else{
                                $update_arr = array(
                                    "current" => $total_current,
                                );
                            }
                            
        
                            $this->db->update("gcchris.tbapplication", $update_arr, array("id"=>$personnel_request_id));
        
                        }
        
                        $response['message'] = "Success.";
                        $response['alert'] = 'success';
                        $response['emp_id'] = $emp_id;
                    }else{
                        $response['message'] = "Position already full.";
                        $response['alert'] = 'failed';
                        $response['emp_id'] = $emp_id;
                    }
                }else{
                    $response['message'] = "Employee already exist!";
                    $response['alert'] = 'failed';
                    $response['emp_id'] = $emp_id;
                }
            }
            
            return $response;
        }

        public function verify_employee($firstname, $lastname){
            // if($suffix != ""){
            //     $where = "firstname='$firstname' AND lastname='$lastname' AND suffix='$suffix' AND contact_no='$contact_no'";
            // }else{
            // }
            $where = "firstname='$firstname' AND lastname='$lastname'";
            
            $this->db->where($where);
            $num = $this->db->get("gccmaster.tblemployees")->num_rows();
            if($num != 0){
                return false;
            }else{
                return true;
            }
        }

        public function update_additional(){
            $response = array();
            $post = $this->input->post();
            $emp_id = $post['id'];

            unset($post['id'], $post['personnel_request_id']);

            $post['fat_deceased'] = (isset($post['fat_deceased']) && $post['fat_deceased'] == 'on') ? 1 : 0;
            $post['mot_deceased'] = (isset($post['mot_deceased']) && $post['mot_deceased'] == 'on') ? 1 : 0;

            if($post['partner_type'] == 1){
                $post['spo_name'] = $post['partners_name'];
                $post['spo_addr'] = $post['partners_addr'];
                $post['spo_company'] = $post['partners_company'];
                $post['spo_occupation'] = $post['partners_occupation'];
                $post['spo_contact'] = $post['partners_contact'];
                $post['spo_deceased'] = (isset($post['partners_deceased']) && $post['partners_deceased'] == 'on') ? 1 : 0;

                unset($post['partners_name'], $post['partners_addr'], $post['partners_company'], $post['partners_occupation'], $post['partners_contact'], $post['partners_deceased']);
            }

            $this->db->where('id', $emp_id);
            $query = $this->db->update('gccmaster.tblemployees', $post);

            if($query){
                $response['message'] = 'Success';
                $response['alert'] = 'success';
                $response['emp_id'] = $emp_id;
            }else{
                $response['message'] = 'Failed to update employee';
                $response['alert'] = 'failed';
                $response['emp_id'] = $emp_id;
            }

            return $response;
        }

        public function get_data(){
            $get = $this->input->get();
            $date = date('Y-m-d');

            $this->db->select('*');
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('id', $get['emp_id']);
            $query = $this->db->get();

            $result['data'] = $query->row();
            $result['count'] = $query->num_rows();
            return $result;
        }

        function getModalContainerContent($id = null, $modalView = null, $data = array()) {
            $id = $id ? $id : 0;
            $resultset = array();
            $modalContent = "";
            $tempData = array();
            $tempData["id"] = $id;
    
            if ($data) {
                foreach ($data as $key => $value) {
                    $tempData[$key] = $value;
                }
            }
    
            if ($modalView) {
                $modalContent = $this->load->view("hris/employee_fillout/modals/{$modalView}", $tempData, true);
            }
            $resultset["html"] = $modalContent;
            $resultset["data"] = $tempData;
            return $resultset;
        }

        function openModal() {
            $formData = $this->input->post('formData'); // get data from request either json or string, its up to you how you handle the data in the function
            $path = $this->input->post("path"); // get the view path form request
            $function_name = $this->input->post("function_name"); // get the function name from request
            $model = $this->input->post("model");
    
            $get_data = null;
    
            if ($function_name) {
                if (isset($model)) {
                    $this->load->model($model, "model");
                    $get_data = $this->model->$function_name($formData);
                } else {
                    $get_data = $this->$function_name($formData);
                }
            }
    
            return $this->load->view($path, $get_data, TRUE);
        }

        function setModalDependents() {
            $post = $this->input->post();
            $resultset = array();
    
            if ($post) {
                unset($post["csrf_token"]);
    
                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->tbldependents, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Dependent has been added successfully.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save dependent!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
            }
            return $resultset;
        }
    
        function setModalEducation() {
            $post = $this->input->post();
            $resultset = array();
    
            if ($post) {
                unset($post["csrf_token"]);
    
                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->tbleducations, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Educational background has been added successfully.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save educational background!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
            }
            return $resultset;
        }
    
        function setModalLicensure() {
            $post = $this->input->post();
            $resultset = array();
    
            if ($post) {
                unset($post["csrf_token"]);
    
                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->tbllicenses, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Licensure exam and certification has been added successfully.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save licensure exam and certification!";
                    
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
            }
            return $resultset;
        }
    
        function setModalDriverLicense() {
            $post = $this->input->post();
            $resultset = array();
    
            if ($post) {
                unset($post["csrf_token"]);
    
                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->tbldriverlicense, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Driver's license has been added successfully.";
    
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee driver's license!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
            }
            return $resultset;
        }
    
        function setModalWorkExperience() {
            $post = $this->input->post();
            $resultset = array();
    
            if ($post) {
                unset($post["csrf_token"]);;
    
                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->tblworkxps, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Work experience has been added successfully.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save work experience!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
            }
            return $resultset;
        }
    
        function setModalAwards() {
            $post = $this->input->post();
            $resultset = array();
    
            if ($post) {
                unset($post["csrf_token"]);
    
                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->tblawards, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Award and achievement has been added successfully.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save award and achievement!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
            }
            return $resultset;
        }
    
        function setModalOrganization() {
            $post = $this->input->post();
            $resultset = array();
    
            if ($post) {
                unset($post["csrf_token"]);
                $post["org_from"] = date("Y-01-01", strtotime($post["org_from"]));
                $post["org_to"] = date("Y-01-01", strtotime($post["org_to"]));
    
                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->tblorganizations, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Organization has been added successfully.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save organization!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
            }
            return $resultset;
        }
    
        function setModalTrainings() {
            $post = $this->input->post();
            $resultset = array();
    
            if ($post) {
                unset($post["csrf_token"]);
    
                $post = array_map('strtoupper', $post);
    
                $saved = $this->db->insert($this->tbltrainings, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Training and seminar has been added successfully.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save training and seminar!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
            }
            return $resultset;
        }
    
        function setModalReferences() {
            $post = $this->input->post();
            $resultset = array();
    
            if ($post) {
                unset($post["csrf_token"]);
    
                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->tblreferences, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Personal reference has been added successfully.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save personal reference!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
            }
            return $resultset;
        }
    
        function setModalMedicalHistory() {
            $post = $this->input->post();
            $resultset = array();
    
            if ($post) {
                unset($post["csrf_token"]);
    
                $post = array_map('strtoupper', $post);
    
                $saved = $this->db->insert($this->tblmedrecs, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Medical history/record has been added successfully.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save medical history/record!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
            }
            return $resultset;
        }
    
        function addSkill() {
            $user = $this->core_layout->getUserLoggedIn();
            $post = $this->input->post();
            $post["skills"] = strtoupper($post["skills"]);
    
            $query = $this->db->insert($this->tblskills, $post);
    
            $resultSet = array();
            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "New skill was successfully saved.";
                $resultSet['title'] = "Add Skill Successful.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }
    
            return $resultSet;
        }

        // datatable
        function getDependentsList() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("dep_name", "dep_age", "dep_birthdate", "dep_relation", "id", "emp_id");
                $dir = "ASC";
                $order = "dep_birthdate";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tbldependents);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {

                    foreach ($posts as $pst) {
                        $currentDate = date_create(date("Y-m-d"));
                        $nextDate = date_create(date("Y-m-d", strtotime($pst->dep_birthdate)));
                        $intervalDate = date_diff($currentDate, $nextDate);

                        $tempAge = 0;
                        if($pst->dep_birthdate == "0000-00-00"){
                            $tempAge = "---";
                        }else{
                            if ($intervalDate->y == 0 && $intervalDate->m == 0) {
                                $tempAge = $intervalDate->d;
                                $tempAge = $tempAge > 1 ? $tempAge . " Days Old" : " Day Old";
                            } else if ($intervalDate->y == 0) {
                                $tempAge = $intervalDate->m;
                                $tempAge = $tempAge > 1 ? $tempAge . " Months Old" : " Month Old";
                            } else {
                                $tempAge = $intervalDate->y;
                                $tempAge = $tempAge > 1 ? $tempAge . " Years Old" : " Year Old";
                            }
                        }
                        $dep_birthdate = $pst->dep_birthdate;
                        if (!empty($dep_birthdate)) {
                            if($dep_birthdate == "0000-00-00"){
                                $dep_birthdate = "---";
                            }else{
                                $dep_birthdate = new DateTime($dep_birthdate);
                                $dep_birthdate = $dep_birthdate->format("M d, Y");
                            }
                        } else {
                            echo "None";
                        }

                        $nestedData['id'] = $pst->id;
                        $nestedData['dep_name'] = "$pst->dep_name";
                        $nestedData['dep_age'] = $tempAge;
                        $nestedData['dep_relation'] = $pst->dep_relation;
                        $nestedData['dep_birthdate'] = $dep_birthdate;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEducationalBackgroundList() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("educ_level_type", "educ_school", "educ_degree", "educ_units", "educ_honors", "educ_from", "educ_to", "id", "emp_id");
                $dir = "DESC";
                $order = "educ_to";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tbleducations);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['educ_level_type'] = $pst->educ_level_type;
                        $nestedData['educ_school'] = $pst->educ_school;
                        $nestedData['educ_degree'] = $pst->educ_degree;
                        $nestedData['educ_units'] = $pst->educ_units;
                        $nestedData['educ_honors'] = $pst->educ_honors;
                        $nestedData['educ_from'] = $pst->educ_from;
                        $nestedData['educ_to'] = $pst->educ_to;
                        $data[] = $nestedData;
                    }
                }
            return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeLicensure() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("license_type", "exam_place", "rating", "release_date", "exam_date", "license_no", "id", "emp_id");
                $dir = "release_date";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tbllicenses);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['license_type'] = $pst->license_type;
                        $nestedData['exam_place'] = $pst->exam_place;
                        $nestedData['rating'] = $pst->rating;
                        $nestedData['release_date'] = $pst->release_date;
                        $nestedData['exam_date'] = $pst->exam_date;
                        $nestedData['license_no'] = $pst->license_no;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeDriverLicense() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("restriction", "license_no", "expiration_date", "id", "emp_id");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tbldriverlicense);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['restriction'] = $pst->restriction;
                        $nestedData['license_no'] = $pst->license_no;
                        if(date("Y-m-d") >= $pst->expiration_date){
                            $expiration_date = "<span class='m-badge m-badge--danger m-badge--wide'>$pst->expiration_date</span>";
                        }else{
                            $expiration_date = "<span class='m-badge m-badge--success m-badge--wide'>$pst->expiration_date</span>";
                        }
                        $nestedData['expiration_date'] = $expiration_date;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeWorkExperience() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("work_company", "work_from", "work_to", "work_position", "work_status", "work_reason", "id", "emp_id");
                $dir = "DESC";
                $order = "work_from";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tblworkxps);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        // if($pst->old_idno != 0 OR $pst->old_idno != NULL){
                        //     $old_idno =  $pst->old_idno;
                        // }else{
                        //     $old_idno =  "N/A";
                        // }

                        $company = is_numeric($pst->work_company) ?
                            $this->db->get_where($this->companyTable, array("id" => $pst->work_company))->row("description") :
                            $pst->work_company;
                        $nestedData['work_company'] = $company;
                        $nestedData['work_from'] = $pst->work_from;
                        $nestedData['work_to'] = $pst->work_to;
                        $position = is_numeric($pst->work_position) ?
                            $this->db->get_where($this->positionTable, array("id" => $pst->work_position))->row("name") :
                            $pst->work_position;
                        $nestedData['work_position'] = $position;
                        // $nestedData['old_idno'] = $old_idno;
                        $nestedData['work_status'] = $pst->work_status;
                        $nestedData['work_reason'] = empty($pst->work_reason) ? "N/A" : $pst->work_reason;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeAwards() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("award", "award_institution", "award_date", "id", "emp_id");
                $dir = "DESC";
                $order = "award_date";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tblawards);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['award'] = $pst->award;
                        $nestedData['award_institution'] = $pst->award_institution;
                        $nestedData['award_date'] = $pst->award_date;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeOrganization() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("org_institution", "org_membership_title", "org_from", "org_to", "id", "emp_id");
                $dir = "DESC";
                $order = "org_to";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tblorganizations);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['org_institution'] = $pst->org_institution;
                        $nestedData['org_membership_title'] = $pst->org_membership_title;
                        $nestedData['org_from'] = $pst->org_from;
                        $nestedData['org_to'] = $pst->org_to;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeTraining() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("training", "train_from", "train_to", "train_institution", "train_conductor", "train_venue", "id", "emp_id");
                $dir = "DESC";
                $order = "train_to";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tbltrainings);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['training'] = $pst->training;
                        $nestedData['train_from'] = $pst->train_from;
                        $nestedData['train_to'] = $pst->train_to;
                        $nestedData['train_institution'] = $pst->train_institution;
                        $nestedData['train_conductor'] = $pst->train_conductor;
                        $nestedData['train_venue'] = $pst->train_venue;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeePersonalReference() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("ref_name", "ref_contact_no", "ref_address", "id", "emp_id");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tblreferences);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['ref_name'] = $pst->ref_name;
                        $nestedData['ref_contact_no'] = $pst->ref_contact_no;
                        $nestedData['ref_address'] = $pst->ref_address;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeMedicalHistory() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("med_details", "med_no", "med_date", "med_venue", "med_physician", "med_findings", "remarks", "id", "emp_id");
                $dir = "med_date";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tblmedrecs);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['med_details'] = $pst->med_details;
                        $nestedData['med_no'] = $pst->med_no;
                        $nestedData['med_date'] = $pst->med_date;
                        $nestedData['med_venue'] = $pst->med_venue;
                        $nestedData['med_physician'] = $pst->med_physician;
                        $nestedData['med_findings'] = $pst->med_findings;
                        $nestedData['remarks'] = $pst->remarks;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeSkills() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("id", "skills");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tblskills);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $posts
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }
        // datatable

        function getDependentInfo($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tbldependents, $where)->row();
            return array("data" => $query);
        }
    
        function getEducationalInfo($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tbleducations, $where)->row();
            return array("data" => $query);
        }
    
        function getLicensure($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tbllicenses, $where)->row();
            return array("data" => $query);
        }
    
        function getDriverLicense($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tbldriverlicense, $where)->row();
            return array("data" => $query);
        }
    
        function getWorkExperience($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tblworkxps, $where)->row();
            return array("data" => $query);
        }
    
        function getAward($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tblawards, $where)->row();
            return array("data" => $query);
        }
    
        function getOrganization($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tblorganizations, $where)->row();
            return array("data" => $query);
        }
    
        function getTraningsAndSeminars($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tbltrainings, $where)->row();
            return array("data" => $query);
        }
    
        function getPersonalReference($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tblreferences, $where)->row();
            return array("data" => $query);
        }
    
        function getMedicalHistory($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tblmedrecs, $where)->row();
            return array("data" => $query);
        }
    
        function getSkill($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->tblskills, $where)->row();
            return array("data" => $query);
        }
    
        // update
        function updateDependent($post) {
            $resultSet = array(
                "response" => false,
                "message" => $this->db->error()
            );
            $id = $post['id'];
            unset($post['id']);
    
            $post['dep_age'] = DateTime::createFromFormat('Y-m-d', $post['dep_birthdate'])->diff(new DateTime('now'))->y;
    
            $post = array_map('strtoupper', $post);
            $this->db->where("id", $id);
            if ($this->db->update($this->tbldependents, $post)) {
                $resultSet["response"] = true;
                $resultSet["message"] = "Record was successfully updated.";
            }else{ 
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
            }
    
            $resultSet["data"] = array(
                "dep_name" => $post['dep_name'],
                "dep_age" => $post['dep_age'] . " YEARS OLD",
                "dep_relation" => $post['dep_relation'],
                "dep_birthdate" => $post['dep_birthdate']
            );
    
            return $resultSet;
        }
    
        function updateEducationalBackground($post) {
            $resultSet = array(
                "response" => false,
                "message" => $this->db->error(),
                "data" => $post
            );
            $id = $post['id'];
            unset($post['id']);
    
            $post = array_map('strtoupper', $post);
            $this->db->where("id", $id);
            if ($this->db->update($this->tbleducations, $post)) {
                $resultSet["response"] = true;
                $resultSet["message"] = "Record was successfully updated.";
            }else{
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
            }
    
            return $resultSet;
        }
    
        function updateLicensure($post) {
            $resultSet = array(
                "response" => false,
                "message" => $this->db->error(),
                "data" => $post
            );
            $id = $post['id'];
            unset($post['id']);
    
            $post = array_map('strtoupper', $post);
            $this->db->where("id", $id);
            if ($this->db->update($this->tbllicenses, $post)) {
                $resultSet["response"] = true;
                $resultSet["message"] = "Record was successfully updated.";
            }else{
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
            }
    
            return $resultSet;
        }
    
        function updateDriverLicense($post) {
            $resultSet = array(
                "response" => false,
                "message" => $this->db->error(),
                "data" => $post
            );
            $id = $post['id'];
            unset($post['id']);
    
            $post = array_map('strtoupper', $post);
            $this->db->where("id", $id);
            if ($this->db->update($this->tbldriverlicense, $post)) {
                $resultSet["response"] = true;
                $resultSet["message"] = "Record was successfully updated.";
            }else{
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
            }
    
            return $resultSet;
        }
    
        function updateWorkExperience($post) {
            $resultSet = array(
                "response" => false,
                "message" => $this->db->error()
            );
            $id = $post['id'];
            unset($post['id']);
    
            $post = array_map('strtoupper', $post);
            $this->db->where("id", $id);
            if ($this->db->update($this->tblworkxps, $post)) {
                $resultSet["response"] = true;
                $resultSet["message"] = "Record was successfully updated.";
            }else{
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
            }
    
            $resultSet["data"] = array(
                "work_company" => $post['work_company'],
                "work_from" => $post['work_from'],
                "work_to" => $post['work_to'],
                "work_position" => $post['work_position'],
                "work_status" => $post['work_status'],
                "work_reason" => $post['work_reason'],
            );
    
            return $resultSet;
        }
    
        function updateAward($post) {
            $resultSet = array(
                "response" => false,
                "message" => $this->db->error()
            );
            $id = $post['id'];
            unset($post['id']);
    
            $post = array_map('strtoupper', $post);
            $this->db->where("id", $id);
            if ($this->db->update($this->tblawards, $post)) {
                $resultSet["response"] = true;
                $resultSet["message"] = "Record was successfully updated.";
            }else{
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
            }
    
            $resultSet["data"] = $post;
    
            return $resultSet;
        }
    
        function updateOrganization($post) {
            $resultSet = array(
                "response" => false,
                "message" => $this->db->error()
            );
            $id = $post['id'];
            unset($post['id']);
    
            $post['org_from'] .= "-01-01";
            $post['org_to'] .= "-01-01";;
    
            $post = array_map('strtoupper', $post);
            $this->db->where("id", $id);
            if ($this->db->update($this->tblorganizations, $post)) {
                $resultSet["response"] = true;
                $resultSet["message"] = "Record was successfully updated.";
            }else{
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
            }
    
            $post['org_from'] = str_replace("-01-01", "", $post['org_from']);
            $post['org_to'] = str_replace("-01-01", "", $post['org_to']);
            $resultSet["data"] = $post;
    
            return $resultSet;
        }
    
        function updateTraining($post) {
            $id = $post['id'];
    
            unset($post['id']);
            $post = array_map('strtoupper', $post);
            $resultSet = array();
            $this->db->trans_begin();
            $this->db->where("id", $id);
            $this->db->update($this->tbltrainings, $post);
    
    
            if ($this->db->trans_status() === FALSE) {
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $resultSet["response"] = "true";
                $resultSet["message"] = "Record was successfully updated.";
                $this->db->trans_commit();
            }
    
            $resultSet["data"] = array(
                "training" => $post['training'],
                "train_from" => $post['train_from'],
                "train_to" => $post['train_to'],
                "train_institution" => $post['train_institution'],
                "train_conductor" => $post['train_conductor'],
                "train_venue" => $post['train_venue'],
            );
            return $resultSet;
        }
    
        function updatePersonalReferences($post) {
            $resultSet = array(
                "response" => false,
                "message" => $this->db->error()
            );
            $id = $post['id'];
            unset($post['id']);
    
            $post = array_map('strtoupper', $post);
            $this->db->where("id", $id);
            if ($this->db->update($this->tblreferences, $post)) {
                $resultSet["response"] = true;
                $resultSet["message"] = "Record was successfully updated.";
            }else{
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
            }
    
            $resultSet["data"] = $post;
    
            return $resultSet;
        }
    
        function updateMedicalRecord($post) {
            $id = $post['id'];
    
            unset($post['id']);
    
            $post = array_map('strtoupper', $post);
            $resultSet = array();
            $this->db->trans_begin();
            $this->db->where("id", $id);
            $this->db->update($this->tblmedrecs, $post);
    
    
            if ($this->db->trans_status() === FALSE) {
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $resultSet["response"] = "true";
                $resultSet["message"] = "Record was successfully updated.";
                $this->db->trans_commit();
            }
    
            $resultSet["data"] = $post;
            return $resultSet;
        }
    
        function updateSkill($post) {
            $id = $post['id'];
    
            unset($post['id']);
    
            $post = array_map('strtoupper', $post);
            $resultSet = array();
            $this->db->trans_begin();
            $this->db->where("id", $id);
            $this->db->update($this->tblskills, $post);
    
    
            if ($this->db->trans_status() === FALSE) {
                $resultSet["response"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->db->trans_rollback();
            } else {
                $resultSet["response"] = "true";
                $resultSet["message"] = "Record was successfully updated.";
                $this->db->trans_commit();
            }
    
            $resultSet["data"] = $post;
            return $resultSet;
        }
        // update

        function passDataToDialog($data) {
            return $data;
        }

        // remove
        function removeDependent($dependent_id) {
            $resultSet = array();
            $this->db->where("id", $dependent_id);
            $query = $this->db->delete($this->tbldependents);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully removed.";
                $resultSet['title'] = "Dependent removed.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function removeEducationalBackground($educ_id) {
            $resultSet = array();
            $this->db->where("id", $educ_id);
            $query = $this->db->delete($this->tbleducations);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully removed.";
                $resultSet['title'] = "EducationalBackground removed.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function removeLicensure($licensure_id) {
            $resultSet = array();
            $this->db->where("id", $licensure_id);
            $query = $this->db->delete($this->tbllicenses);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully removed.";
                $resultSet['title'] = "Licensure Exams & Certificate removed.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function removeDriverLicense($driverlicense_id) {
            $resultSet = array();
            $this->db->where("id", $driverlicense_id);
            $query = $this->db->delete($this->tbldriverlicense);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully removed.";
                $resultSet['title'] = "Driver's License removed.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function removeWorkExperience($work_exp_id) {
            $resultSet = array();
            $this->db->where("id", $work_exp_id);
            $query = $this->db->delete($this->tblworkxps);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully removed.";
                $resultSet['title'] = "Work Experience removed.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function removeAward($award_id) {
            $resultSet = array();
            $this->db->where("id", $award_id);
            $query = $this->db->delete($this->tblawards);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully removed.";
                $resultSet['title'] = "Award & Achievement removed.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function removeOrganization($org_id) {
            $resultSet = array();
            $this->db->where("id", $org_id);
            $query = $this->db->delete($this->tblorganizations);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Organization archive.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function removeTraining($training_id) {
            $resultSet = array();
            $this->db->where("id", $training_id);
            $query = $this->db->delete($this->tbltrainings);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully removed.";
                $resultSet['title'] = "Training & Achievement removed.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function removePersonalReference($reference_id) {
            $resultSet = array();
            $this->db->where("id", $reference_id);
            $query = $this->db->delete($this->tblreferences);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully removed.";
                $resultSet['title'] = "Personal references removed.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function removeMedicalRecord($med_id) {
            $resultSet = array();
            $this->db->where("id", $med_id);
            $query = $this->db->delete($this->tblmedrecs);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully removed.";
                $resultSet['title'] = "Medical Record removed.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function remove_skill($skill_id) {
            $resultSet = array();
            $this->db->where("id", $skill_id);
            $query = $this->db->delete($this->tblskills);

            if ($query) {
                $resultSet['response'] = true;
                $resultSet['message'] = "Record was successfully removed.";
                $resultSet['title'] = "Skill removed.";
            } else {
                $resultSet['response'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }
    }