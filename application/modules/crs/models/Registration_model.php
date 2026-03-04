<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Registration_model extends CI_Model{
    private $tbl_document = "dbhrd.document_body";
    private $tbl_tag = "dbhrd.tag";
    private $tbl_school = "dbhrd.school";
    private $tbl_position = "dbhrd.position";
    private $tbl_course = "dbhrd.course";

    protected $tbldependents = "dbhrd.tbldependents";
    protected $tbleducations = "dbhrd.tbleducation";
    protected $tbllicenses = "dbhrd.tbllicenses";
    protected $tbldriverlicense = "dbhrd.tbldriverlicense";
    protected $tblworkxps = "dbhrd.tblworkexperience";
    protected $tblawards = "dbhrd.tblawards";
    protected $tblorganizations = "dbhrd.tblorganizations";
    protected $tbltrainings = "dbhrd.tbltrainings";
    protected $tblreferences = "dbhrd.tblreferences";
    protected $tblmedrecs = "dbhrd.tblmedrecs";
    protected $tblskills = "dbhrd.tblskills";
    
    function __construct()
    {
        parent::__construct();
        $this->load->model("core/upload_model", "core_upload");
        $this->load->model("ams/Utilities_model", "utilities");
        date_default_timezone_set('Asia/Manila');
    }

    public function getTag(){
        $this->db->select('tag');
        $result = $this->db->get($this->tbl_tag)->result_array();
        $tag_data = array();
        foreach($result as $tag){
            $data = array();
            $data["id"] = $tag['tag'];
            $data["text"] = $tag['tag'];
            $tag_data[] = $data;
        }
        return array("results" => $tag_data);
    }

    public function getSchool(){
        $get = $this->input->get();
        $this->db->select('school');
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `school` FROM dbhrd.school WHERE `school` LIKE '%{$get['q']}%' ORDER BY `school` ASC");
        } else {
            $query = $this->db->query("SELECT `school` FROM dbhrd.school ORDER BY `school` ASC");
        }
        $result = $query->result_array();
        $result_data = array();
        foreach($result as $school){
            $data = array();
            $data["id"] = $school['school'];
            $data["text"] = $school['school'];
            $result_data[] = $data;
        }
        if(count($result_data) > 0){
            return array("results" => $result_data);
        }else{
            return array("results" => array(array("id" => $get['q'], "text" => $get['q'])));
        }
        
    }

    public function getCourse(){
        $get = $this->input->get();
        $this->db->select('course');
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `course` FROM dbhrd.course WHERE `course` LIKE '%{$get['q']}%' ORDER BY `course` ASC");
        } else {
            $query = $this->db->query("SELECT `course` FROM dbhrd.course ORDER BY `course` ASC");
        }
        $result = $query->result_array();
        $result_data = array();
        foreach($result as $course){
            $data = array();
            $data["id"] = $course['course'];
            $data["text"] = $course['course'];
            $result_data[] = $data;
        }
        if(count($result_data) > 0){
            return array("results" => $result_data);
        }else{
            return array("results" => array(array("id" => $get['q'], "text" => $get['q'])));
        }
    }

    public function getPosition(){
        $get = $this->input->get();
        $this->db->select('position');
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `position` FROM dbhrd.position WHERE `position` LIKE '%{$get['q']}%' ORDER BY `position` ASC");
        } else {
            $query = $this->db->query("SELECT `position` FROM dbhrd.position ORDER BY `position` ASC");
        }
        $result = $query->result_array();
        $result_data = array();
        foreach($result as $position){
            $data = array();
            $data["id"] = $position['position'];
            $data["text"] = $position['position'];
            $result_data[] = $data;
        }
        if(count($result_data) > 0){
            return array("results" => $result_data);
        }else{
            return array("results" => array(array("id" => $get['q'], "text" => $get['q'])));
        }
    }

    public function insertResume(){
        $resultSet = array();
        $post_data = $this->input->post();
        unset($post_data['g-recaptcha-response']);
        $post = $this->utilities->parseFormDataToObject($post_data);
        $tags = isset($post->tags) ? $post->tags : [];
        $schools = isset($post->schools) ? $post->schools : [];
        $courses = isset($post->courses) ? $post->courses : [];
        $positions = isset($post->positions) ? $post->positions : [];
        $post->applied_dt = date('Y-m-d', strtotime($post->applied_dt));
        $post->created_dt = date('Y-m-d H:i:s');
        unset($post->tags, $post->schools, $post->courses, $post->positions);

        $tag1 = implode(",", $tags);
        while (strpos($tag1, ', ') !== FALSE) {
            $tag1 = str_replace(', ', ',', $tag1);
        }
        $post->tag1 = "," . $tag1;

        $school = implode(",", $schools);
        while (strpos($school, ', ') !== FALSE) {
            $school = str_replace(', ', ',', $school);
        }
        $post->school = empty($school) ? '' : "," . $school;

        $course = implode(",", $courses);
        while (strpos($course, ', ') !== FALSE) {
            $course = str_replace(', ', ',', $course);
        }
        $post->course = empty($course) ? '' : "," . $course;

        $position = implode(",", $positions);
        while (strpos($position, ', ') !== FALSE) {
            $position = str_replace(', ', ',', $position);
        }
 
        $post->position = "," . $position;

        $upload_error = null;
        if($this->verify_applicant($post->firstname, $post->lastname, $post->suffix, $post->position, $post->contact_no)){
            
            $insert = $this->db->insert("dbhrd.document_body", $post);
            if ($insert) {
                $insert_id = $this->db->insert_id();
                $this->temp_files($insert_id);
                
                $filename = $_FILES['files']['name'];
                $filepath = "uploads/files/hrd/resume_".$insert_id;

                if (!file_exists(realpath($filepath))) {
                    mkdir($filepath, 0777, true);
                }

                $config['upload_path'] = $filepath;
                $config['allowed_types'] = 'gif|jpg|png|pdf';
                $config['max_size'] = 10000;
                $this->upload->initialize($config);

                $upload = $this->upload->do_upload('files');
                if (!$upload) {
                    $upload_error = $this->upload->display_errors();
                } else {
                    $upload_data = $this->upload->data();
                    $filename = $upload_data['file_name'];
                    $filesize = $upload_data['file_size'];

                    $this->db->reset_query();
                    $this->db->where("id", $insert_id);
                    $this->db->set("filename", $filename);
                    $this->db->set("filesize", $filesize);
                    $this->db->update("dbhrd.document_body");
                }

                $resultSet["success"] = true;
                $resultSet["message"] = "Resume information successfully saved.";
                $resultSet["data"] = $post->firstname . " " . $post->lastname;
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
                $resultSet["data"] = null;
            }
            $resultSet["upload_error"] = $upload_error;

            return $resultSet;
        }else{
            $resultSet["success"] = false;
            $resultSet["message"] = "User already exist!";
            $resultSet["data"] = null;
            return $resultSet;
        }
    }

    public function addTempFile(){
        $post = $this->input->post();
        $public_user_id = $post['public_user_id'];
        $body_id = $post['body_id'];
        $files = $_FILES['files']['name'];
        $filepath = "uploads/files/hrd/temp/";

        if (!file_exists(realpath($filepath))) {
            mkdir($filepath, 0777, true);
        }

        $config['upload_path'] = $filepath;
        $config['allowed_types'] = 'gif|jpg|png|pdf';
        $config['max_size'] = 10000;
        $config['file_name'] = $this->changeStr($files);
        $this->upload->initialize($config);

        $upload = $this->upload->do_upload('files');
        if (!$upload) {
            $upload_error = $this->upload->display_errors();
            return 0;
        } else {
            $upload_data = $this->upload->data();
            $filename = $upload_data['file_name'];
            $filesize = $upload_data['file_size'];

            $this->db->reset_query();
            $this->db->set("public_user_id", $public_user_id);
            $this->db->set('body_id', $body_id);
            $this->db->set("filename", $filename);
            $this->db->set("file_size", $filesize);
            $this->db->insert("dbhrd.file_attachment_temp");
        }
        $this->db->where("body_id", $body_id);
        return $this->db->get("dbhrd.file_attachment_temp")->result_array();
    }

    public function tempFileDel(){
        $post = $this->input->post();
        $id = $post['id'];
        $public_user_id = $post['public_user_id'];
        $body_id = $post['body_id'];
        $this->db->where("id", $id);
        $this->db->where("public_user_id", $public_user_id);
        $this->db->where("body_id", $body_id);
        $file = $this->db->get("dbhrd.file_attachment_temp");
    
        $theFile = $file->row_array();
        unlink("uploads/files/hrd/temp/".$theFile['filename']);
        $this->db->where("id", $id);
        $del = $this->db->delete("dbhrd.file_attachment_temp");
        if($del){
            $this->db->where('body_id', $body_id);
            return $this->db->get("dbhrd.file_attachment_temp")->result_array();
        }
    }

    public function tempFileDelAll(){
        $post = $this->input->post();
        $public_user_id = $post['public_user_id'];
        $this->db->where("public_user_id", $public_user_id);
        $file = $this->db->get("dbhrd.file_attachment_temp");
    
        $theFile = $file->result_array();
        foreach($theFile as $files){
            unlink("uploads/files/hrd/temp/".$files['filename']);
            $this->db->where("id", $files['id']);
            $this->db->delete("dbhrd.file_attachment_temp");
        }
        $this->db->where("public_user_id", $public_user_id);
        $count = $this->db->get("dbhrd.file_attachment_temp")->num_rows();
        if($count == 0){
            return 1;
        }else{
            return 0;
        }
    }

    private function verify_applicant($firstname, $lastname, $suffix, $position, $contact_no){
        if($suffix != ""){
            $where = "firstname='$firstname' AND lastname='$lastname' AND suffix='$suffix' AND contact_no='$contact_no'";
        }else{
            $where = "firstname='$firstname' AND lastname='$lastname' AND position='$position' AND contact_no='$contact_no'";
        }
        
        $this->db->where($where);
        $num = $this->db->get("dbhrd.document_body")->num_rows();
        if($num != 0){
            return false;
        }else{
            return true;
        }
    }

    private function temp_files($id){
        $this->db->select("public_user_id");
        $docu_body = $this->db->get_where('dbhrd.document_body', array('id' => $id))->result_array();
        $this->db->where("public_user_id", $docu_body[0]['public_user_id']);
        $allTempFile = $this->db->get("dbhrd.file_attachment_temp")->result_array();
        foreach($allTempFile as $tempfiles){
            if(file_exists("uploads/files/hrd/temp/".$tempfiles['filename'])){
                if (!file_exists(realpath("uploads/files/hrd/resume_".$id."/"))) {
                    mkdir("uploads/files/hrd/resume_".$id."/", 0777, true);
                }
                $this->core_upload->moveUploadedFile($tempfiles['filename'], "uploads/files/hrd/temp", "uploads/files/hrd/resume_".$id);
                // rename("uploads/files/hrd/temp/".$tempfiles['filename'], "uploads/files/hrd/resume_".$id."/".$tempfiles['filename']);
            }
            $insertTemp = $this->db->insert("dbhrd.file_attachment", array("filename" => $tempfiles['filename'], "body_id" => $id, "file_size" => $tempfiles['file_size'], "public_user_id" => $tempfiles['public_user_id'], "created_by" => $this->core_layout->getUserId()));
            if($insertTemp){
                $this->db->where("id", $tempfiles['id']);
                $this->db->delete("dbhrd.file_attachment_temp");
            }
        }
        return true;
    }

    private function changeStr($name){
        $new_name = str_replace(str_split(' ()/_0123456789'), "_", $name);
        $str_name = explode("_", $new_name);
        $filtered = array_filter($str_name);
        return implode("_", $filtered);
    }

    public function insert_application(){
        $result = array();
        $post = $this->input->post();

        $schools = isset($post['schools']) ? $post['schools'] : [];
        $tags = isset($post['tags']) ? $post['tags'] : [];
        $courses = isset($post['courses']) ? $post['courses'] : [];
        $positions = isset($post['positions']) ? $post['positions'] : [];
        $post['applied_dt'] = date('Y-m-d', strtotime($post['applied_dt']));
        $post['created_dt'] = date('Y-m-d H:i:s');
        unset($post['tags'], $post['schools'], $post['courses'], $post['positions']);

        $tag1 = implode(",", $tags);
        while (strpos($tag1, ', ') !== FALSE) {
            $tag1 = str_replace(', ', ',', $tag1);
        }
        $post['tag1'] = "," . $tag1;

        $school = implode(",", $schools);
        while (strpos($school, ', ') !== FALSE) {
            $school = str_replace(', ', ',', $school);
        }
        $post['school'] = empty($school) ? '' : "," . $school;

        $course = implode(",", $courses);
        while (strpos($course, ', ') !== FALSE) {
            $course = str_replace(', ', ',', $course);
        }
        $post['course'] = empty($course) ? '' : "," . $course;

        $position = implode(",", $positions);
        while (strpos($position, ', ') !== FALSE) {
            $position = str_replace(', ', ',', $position);
        }

        $post['position'] = "," . $position;

        if($this->verify_applicant($post['app_firstname'], $post['app_lastname'], $post['app_suffix'], $post['position'], $post['contact_no'])){
            if($this->verify_temp_applicant($post['app_firstname'], $post['app_lastname'], $post['app_suffix'], $post['position'], $post['contact_no'])){
                $data = array(
                    'firstname' => $post['app_firstname'],
                    'lastname' => $post['app_lastname'],
                    'middlename' => $post['app_middlename'],
                    'suffix' => $post['app_suffix'],
                    'contact_no' => $post['contact_no'],
                    'school' => $post['school'],
                    'course' => $post['course'],
                    'position' => $post['position'],
                    'tag1' => $post['tag1'],
                    'recruitment' => $post['recruitment'],
                    'applied_dt' => $post['applied_dt'],
                    'created_dt' => $post['created_dt'],
                    'public_user_id' => $post['public_user_id'],
                );
    
                $insert = $this->db->insert("dbhrd.document_body_temp", $data);
                if($insert){
                    $result["success"] = true;
                    $result["message"] = "";
                    $result["data"] = "";
                }
            }else{
                if(isset($post['document_body_id']) && $post['document_body_id']){
                    $data = array(
                        'firstname' => $post['app_firstname'],
                        'lastname' => $post['app_lastname'],
                        'middlename' => $post['app_middlename'],
                        'suffix' => $post['app_suffix'],
                        'contact_no' => $post['contact_no'],
                        'school' => $post['school'],
                        'course' => $post['course'],
                        'position' => $post['position'],
                        'tag1' => $post['tag1'],
                        'recruitment' => $post['recruitment'],
                        'applied_dt' => $post['applied_dt'],
                        'created_dt' => $post['created_dt'],
                    );
    
                    $this->db->where('id', $post['document_body_id']);
                    $query = $this->db->update('dbhrd.document_body_temp', $data);
    
                    if($query){
                        $result["success"] = true;
                        $result["message"] = "";
                        $result["data"] = "";
                    }else{
                        $result["success"] = false;
                        $result["message"] = "Failed to update information.";
                        $result["data"] = "";
                    }
                }else{
                    $result["success"] = false;
                    $result["message"] = "Applicant already exist!";
                    $result["data"] = null;
                }
            }
        }else{
            if(isset($post['document_body_id']) && $post['document_body_id']){
                $data = array(
                    'firstname' => $post['app_firstname'],
                    'lastname' => $post['app_lastname'],
                    'middlename' => $post['app_middlename'],
                    'suffix' => $post['app_suffix'],
                    'contact_no' => $post['contact_no'],
                    'school' => $post['school'],
                    'course' => $post['course'],
                    'position' => $post['position'],
                    'tag1' => $post['tag1'],
                    'recruitment' => $post['recruitment'],
                    'applied_dt' => $post['applied_dt'],
                    'created_dt' => $post['created_dt'],
                );

                $this->db->where('id', $post['document_body_id']);
                $query = $this->db->update('dbhrd.document_body_temp', $data);

                if($query){
                    $result["success"] = true;
                    $result["message"] = "";
                    $result["data"] = "";
                }else{
                    $result["success"] = false;
                    $result["message"] = "Failed to update information.";
                    $result["data"] = "";
                }
            }else{
                $result["success"] = false;
                $result["message"] = "Applicant already exist!";
                $result["data"] = null;
            }
        }

        return $result;
    }

    public function insert_information(){
        $result = array();
        $post = $this->input->post();

        $data = array(
            'temp_id' => $post['document_body_id'],
            'firstname' => $post['firstname'],
            'lastname' => $post['lastname'],
            'middlename' => $post['middlename'],
            'suffix' => $post['suffix'],
            'telephone_no' => $post['telephone_no'],
            'mobile_no' => $post['mobile_no'],
            'email' => $post['email'],
            'curr_addr' => (isset($post['same-to-permanent']) && $post['same-to-permanent'] == 'on') ? $post['prov_addr'] : $post['curr_addr'],
            'prov_addr' => $post['prov_addr'],
            'citizenship' => $post['citizenship'],
            'religion' => $post['religion'],
            'languages' => $post['languages'],
            'gender' => $post['gender'],
            'civil_stat' => $post['civil_stat'],
            'bday' => $post['bday'],
            'birthplace' => $post['birthplace'],
            'bloodtype' => $post['bloodtype'],
            'height' => $post['height'],
            'weight' => $post['weight'],
            'hair_color' => $post['hair_color'],
            'complexion' => $post['complexion'],
        );

        if(isset($post['info_id']) && $post['info_id']){
            $this->db->where('id', $post['info_id']);
            $query = $this->db->update('dbhrd.applicant_informations', $data);
        }else{
            $query = $this->db->insert('dbhrd.applicant_informations', $data);
        }
        
        if($query){
            $_data = array(
                'firstname' => $post['firstname'],
                'lastname' => $post['lastname'],
                'middlename' => $post['middlename'],
                'suffix' => $post['suffix'],
                'contact_no' => (isset($post['mobile_no']) && $post['mobile_no']) ? $post['mobile_no'] : $post['telephone_no'],
            );

            $this->db->where('id', $post['document_body_id']);
            $this->db->update('dbhrd.document_body_temp', $_data);

            $result["success"] = true;
            $result["message"] = "";
            $result["data"] = "";
        }else{
            $result["success"] = false;
            $result["message"] = "Applicant already exist!";
            $result["data"] = null;
        }

        return $result;
    }

    public function insert_additional(){
        $result = array();
        $post = $this->input->post();

        $data = array(
            'tin_no' => $post['tin'],
            'tax_status' => $post['tax_status'],
            'phealth_no' => $post['phil'],
            'pagibig_no' => $post['hdmf'],
            'sss_no' => $post['sss'],
            'fat_deceased' => isset($post['father_deceased']) ? 1 : 0,
            'fat_name' => $post['father_no'],
            'fat_addr' => $post['father_address'],
            'fat_company' => $post['father_company'],
            'fat_occupation' => $post['father_occupation'],
            'fat_contact' => $post['father_no'],
            'mot_deceased' => isset($post['mother_deceased']) ? 1 : 0,
            'mot_name' => $post['mother_name'],
            'mot_addr' => $post['mother_address'],
            'mot_company' => $post['mother_company'],
            'mot_occupation' => $post['mother_occupation'],
            'mot_contact' => $post['mother_no'],
            'partners_deceased' => isset($post['partner_deceased']) ? 1 : 0,
            'partners_name' => $post['partner_name'],
            'partners_addr' => $post['partner_address'],
            'partners_company' => $post['partner_company'],
            'partners_occupation' => $post['partner_occupation'],
            'partners_contact' => $post['partner_no'],
            'partner_type' => isset($post['partner_type']) ? $post['partner_type'] : 0,
            'emer_name' => $post['emer_name'],
            'emer_contact' => $post['emer_contact'],
            'emer_addr' => $post['emer_addr'],
        );

        $this->db->where('id', $post['info_id']);
        $query = $this->db->update('dbhrd.applicant_informations', $data);
        if($query){
            $result["success"] = true;
            $result["message"] = "";
            $result["data"] = "";
        }else{
            $result["success"] = false;
            $result["message"] = "failed to update information";
            $result["data"] = null;
        }
        return $result;
    }

    public function get_data(){
        $get = $this->input->get();
        $date = date('Y-m-d');
        
        $sql = "";
        $sql .= "a.id as body_id, a.*, a.firstname as fname, a.lastname as lname, a.middlename as mname, a.suffix as suff, b.*, b.id as info_id, ";
        $sql .= "IF(CHAR_LENGTH(a.contact_no) <= 7, a.contact_no, '') as telephone_no, IF(CHAR_LENGTH(a.contact_no) >= 11, a.contact_no, '') as mobile_no";

        $this->db->select($sql);
        $this->db->from('dbhrd.document_body_temp as a');
        $this->db->join("dbhrd.applicant_informations as b", 'a.id = b.temp_id', 'left');
        $this->db->where('a.public_user_id', $get['public_user_id']);
        $this->db->where('a.applied_dt', $date);
        $this->db->order_by("a.id", 'desc');
        $this->db->limit(1);
        $query = $this->db->get();

        return $query->result();
    }

    // for modal
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
            $modalContent = $this->load->view("crs/modals/{$modalView}", $tempData, true);
        }
        $resultset["html"] = $modalContent;
        $resultset["data"] = $tempData;
        return $resultset;
    }
    // for modal

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
            $tempRemarks = $post["med_remarks"];
            unset($post["csrf_token"]);

            $post["remarks"] = $tempRemarks;

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
            $columns = array("dep_name", "dep_age", "dep_birthdate", "dep_relation", "id", "applicant_id");
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
            $parameters["applicant_id"] = $post["applicant_id"];

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
            $columns = array("educ_level_type", "educ_school", "educ_degree", "educ_units", "educ_honors", "educ_from", "educ_to", "id", "applicant_id");
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
            $parameters["applicant_id"] = $post["applicant_id"];

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
            $columns = array("license_type", "exam_place", "rating", "release_date", "exam_date", "license_no", "id", "applicant_id");
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
            $parameters["applicant_id"] = $post["applicant_id"];

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
            $columns = array("restriction", "license_no", "expiration_date", "id", "applicant_id");
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
            $parameters["applicant_id"] = $post["applicant_id"];

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
            $columns = array("work_company", "work_from", "work_to", "work_position", "work_status", "work_reason", "id", "applicant_id");
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
            $parameters["applicant_id"] = $post["applicant_id"];

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
            $columns = array("award", "award_institution", "award_date", "id", "applicant_id");
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
            $parameters["applicant_id"] = $post["applicant_id"];

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
            $columns = array("org_institution", "org_membership_title", "org_from", "org_to", "id", "applicant_id");
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
            $parameters["applicant_id"] = $post["applicant_id"];

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
            $columns = array("training", "train_from", "train_to", "train_institution", "train_conductor", "train_venue", "id", "applicant_id");
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
            $parameters["applicant_id"] = $post["applicant_id"];

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
            $columns = array("ref_name", "ref_contact_no", "ref_address", "id", "applicant_id");
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
            $parameters["applicant_id"] = $post["applicant_id"];

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
            $columns = array("med_details", "med_no", "med_date", "med_venue", "med_physician", "med_findings", "remarks", "id", "applicant_id");
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
            $parameters["applicant_id"] = $post["applicant_id"];

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
            $parameters["applicant_id"] = $post["applicant_id"];

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

    public function insertUploadedResume(){
        $resultSet = array();
        $post_data = $this->input->post();
        
        $upload_error = null;

        if($post_data){
            $attachments = json_decode($post_data['attachments'][0]);
    
            foreach($attachments as $row){
                $filename = $row->filename;
                $filesize = $row->file_size;
            }

            $this->db->reset_query();
            $this->db->where("id", $post_data['document_body_id']);
            $this->db->set("filename", $filename);
            $this->db->set("filesize", $filesize);
            $query = $this->db->update("dbhrd.document_body_temp");

            if($query){
                $to_move = $this->db->get_where('dbhrd.document_body_temp', array('id' => $post_data['document_body_id']))->row();

                unset($to_move->id);

                $q = $this->db->insert('dbhrd.document_body', $to_move);
                if($q){
                    $id = $this->db->insert_id();

                    $this->db->where('temp_id', $post_data['document_body_id']);
                    $this->db->update('dbhrd.applicant_informations', array('document_body_id' => $id));

                    $filepath = "uploads/files/hrd/resume_".$id;
    
                    if (!file_exists(realpath($filepath))) {
                        mkdir($filepath, 0777, true);
                    }
            
                    $isMoved = $this->temp_files($id);

                    if($isMoved){
                        $resultSet["result"] = true;
                        $resultSet["message"] = "Resume successfully uploaded.";
                    }else{
                        $resultSet["result"] = false;
                        $resultSet["message"] = 'Failed to upload resume';
                    }
                }else{
                    $resultSet["result"] = false;
                    $resultSet["message"] = $this->db->error();
                }
            }else{
                $resultSet["result"] = false;
                $resultSet["message"] = $this->db->error();
            }
    
            // if($isMoved){
            //     $this->db->reset_query();
            //     $this->db->where("id", $post_data['document_body_id']);
            //     $this->db->set("filename", $filename);
            //     $this->db->set("filesize", $filesize);
            //     $query = $this->db->update("dbhrd.document_body_temp");
    
            //     if($query){
            //         $to_move = $this->db->get_where('dbhrd.document_body_temp', array('id' => $post_data['document_body_id']))->row();

            //         unset($to_move->id);

            //         $q = $this->db->insert('dbhrd.document_body', $to_move);
            //         if($q){
            //             $id = $this->db->insert_id();

            //             $this->db->where('temp_id', $post_data['document_body_id']);
            //             $this->db->update('dbhrd.applicant_informations', array('document_body_id' => $id));
            //         }
                    
            //         $resultSet["result"] = true;
            //         $resultSet["message"] = "Resume successfully uploaded.";
            //     }else{
            //         $resultSet["result"] = false;
            //         $resultSet["message"] = $this->db->error();
            //     }
            // }else{
            //     $resultSet["result"] = false;
            //     $resultSet["message"] = 'Failed to upload resume';
            // }
        }else{
            $resultSet["result"] = false;
            $resultSet["message"] = 'Failed to upload resume';
        }

        return $resultSet;
    }
    // remove

    public function check_personal_reference(){
        $result = array();
        $get = $this->input->get();

        $this->db->from('dbhrd.tblreferences');
        $this->db->where('applicant_id', $get['id']);
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $result['response'] = true;
        }else{
            $result['response'] = false;
        }

        return $result;
    }

    // cloned for temporary table for applicants
    private function docu_temp_files($id){
        $this->db->select("public_user_id");
        $docu_body = $this->db->get_where('dbhrd.document_body_temp', array('id' => $id))->result_array();
        $this->db->where("public_user_id", $docu_body[0]['public_user_id']);
        $allTempFile = $this->db->get("dbhrd.file_attachment_temp")->result_array();
        foreach($allTempFile as $tempfiles){
            if(file_exists("uploads/files/hrd/temp/".$tempfiles['filename'])){
                if (!file_exists(realpath("uploads/files/hrd/resume_".$id."/"))) {
                    mkdir("uploads/files/hrd/resume_".$id."/", 0777, true);
                }
                $this->core_upload->moveUploadedFile($tempfiles['filename'], "uploads/files/hrd/temp", "uploads/files/hrd/resume_".$id);
                // rename("uploads/files/hrd/temp/".$tempfiles['filename'], "uploads/files/hrd/resume_".$id."/".$tempfiles['filename']);
            }
            $insertTemp = $this->db->insert("dbhrd.file_attachment", array("filename" => $tempfiles['filename'], "body_id" => $id, "file_size" => $tempfiles['file_size'], "public_user_id" => $tempfiles['public_user_id'], "created_by" => $this->core_layout->getUserId()));
            if($insertTemp){
                $this->db->where("id", $tempfiles['id']);
                $this->db->delete("dbhrd.file_attachment_temp");
            }
        }
        return true;
    }

    // cloned for temporary applicants
    private function verify_temp_applicant($firstname, $lastname, $suffix, $position, $contact_no){
        if($suffix != ""){
            $where = "firstname='$firstname' AND lastname='$lastname' AND suffix='$suffix' AND contact_no='$contact_no'";
        }else{
            $where = "firstname='$firstname' AND lastname='$lastname' AND position='$position' AND contact_no='$contact_no'";
        }
        
        $this->db->where($where);
        $num = $this->db->get("dbhrd.document_body_temp")->num_rows();
        if($num != 0){
            return false;
        }else{
            return true;
        }
    }

    public function select2PositionData(){
        $this->db->select('id, name as text');
        $this->db->from('gcchris.tblposition');
        $this->db->where('is_archived', 0);
        $this->db->where('name !=', '');
        $this->db->order_by('name', 'ASC');
        $results = $this->db->get()->result();
        return $results;
    }

    public function select2RefferalData(){
        $query = $this->db->query("SELECT  c.id, CONCAT(c.firstname,' ',c.lastname) as emp_name FROM gccmaster.tblusers b, gccmaster.tblemployees c WHERE b.emp_id=c.id AND c.employee_status = 'Active' group by c.id ORDER BY c.firstname ASC ");
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["emp_name"];
                $resultarray[] = $data;
            }
        }
        return  $resultarray;
    }

    public function select2SchoolsData(){
        $this->db->select('id, school as text');
        $this->db->from('dbhrd.school');
        $this->db->order_by('school', 'ASC');
        $results = $this->db->get()->result();
        return $results;
    }

    public function select2CoursesData(){
        $this->db->select('id, course as text');
        $this->db->from('dbhrd.course');
        $this->db->order_by('course', 'ASC');
        $results = $this->db->get()->result();
        return $results;
    }

    public function submitAppilication(){
        $post = $this->input->post('payload');
        $personal_info= array(
            "firstname" => $post['firstname'],
            "middlename" => $post['middlename'],
            "lastname" => $post['lastname'],
            "suffix" => $post['suffix'],
            "contact_no" => $post['contact_no'],
            "email" => $post['email'],
            "position" => $post['position'],
            "status" => 1
        );
    }



}