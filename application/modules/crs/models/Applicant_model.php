<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Applicant_model extends CI_Model
    {
        protected $candidatesTable = "dbhrd.candidates";
        protected $positionTable = "gcchris.tblposition";
        protected $referencesTable = "dbhrd.candidate_references";
        protected $educationTable = "dbhrd.candidate_educations";
        protected $manpowerRequestTable = "gcceforms.manpower_request_table";
        protected $interviewTable = "dbhrd.candidate_interview";
        protected $user_data;
        function __construct(){
            parent::__construct();
            $this->user_data = $this->session->userdata("logged_in");
            session_write_close();
        }


        public function submitAppilication(){
            $result = array();
            $post = $this->input->post('payload');
            $post = json_decode($post, true);
            $schools = isset($post['schools']) ? implode(',', $post['schools']): '';
            $courses = isset($post['courses']) ? implode(',', $post['courses']): '';
            $positions = isset($post['positions']) ? implode(',', $post['positions']): '';
    
            $contactno = isset($post['contactFormData']['contact_no']) ? $post['contactFormData']['contact_no'] : '';
            $address = isset($post['contactFormData']['address']) ? $post['contactFormData']['address'] : '';
            $permanent_address = isset($post['contactFormData']['permanent_address']) ? $post['contactFormData']['permanent_address'] : '';
            $tel_no = isset($post['contactFormData']['tel_no']) ? $post['contactFormData']['tel_no'] : '';
            $email = isset($post['contactFormData']['email']) ? $post['contactFormData']['email'] : '';
            $weight = isset($post['weight']) ? $post['weight'] : '';
            $height = isset($post['height']) ? $post['height'] : '';

            $personal_info = array(
                "firstname"  => trim($post['firstname']),
                "middlename" => trim($post['middlename']),
                "lastname"   => trim($post['lastname']),
                "suffix"     => trim($post['suffix']),
                "contact_no"         => $contactno,
                "status"             => "pooling",
                "gender"             => $post['gender'],
                "weight"             => $weight,
                "height"             => $height,
                "civil_status"       => $post['civil_status'],
                "religion"           => $post['religion'],
                "birthdate"          => date('Y-m-d', strtotime($post['birthdate'])),
                "citizenship"        => $post['citizenship'],
                // "schools"            => $schools,
                // "courses"            => $courses,
                "positions"          => $positions,
                "recruitment"        => $post['recruitment'],
                "applied_dt"         => date('Y-m-d', strtotime($post['applied_dt'])),
                "created_by"         => $this->user_data['emp_id'],
                "address"            => $address,
                "permanent_address"  => $permanent_address,
                "tel_no"             => $tel_no,
                "email"              => $email,
                "referral"           => $post['referral'],
                "referral_relationship" => $post['referral-relationship'],
                "is_online"          => 0,
            );
        
            $this->db->trans_begin();
        
            $candidate = $this->db->insert($this->candidatesTable, $personal_info);
        
            if (!$candidate) {
                $this->db->trans_rollback();
                $result['insert']  = false;
                $result['success'] = false;
                $result['message'] = 'Failed to insert applicant.';
                return $result;
            }
        
            $candidate_id    = $this->db->insert_id();
            $result['id']    = $candidate_id;
            $result['insert'] = true;
        
            if (!empty($_FILES['files']['name'])) {
                $folder = "uploads/files/hrd/new_resume_{$candidate_id}/";
                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);
                }
                $ext      = pathinfo($_FILES['files']['name'], PATHINFO_EXTENSION);
                $filename = "new_resume_{$candidate_id}.{$ext}";
                $config   = [
                    'upload_path'   => realpath($folder) . DIRECTORY_SEPARATOR,
                    'allowed_types' => 'pdf|jpg|jpeg|png|gif|webp|bmp',
                    'file_name'     => $filename,
                    'overwrite'     => true,
                ];
                $this->load->library('upload');
                $this->upload->initialize($config);
        
                if ($this->upload->do_upload('files')) {
                    $updated = $this->db->where('id', $candidate_id)
                    ->update('dbhrd.candidates', ['resume' => $filename]);
                    if (!$updated) {
                        $this->db->trans_rollback();
                        $result['upload']  = false;
                        $result['success'] = false;
                        $result['message'] = 'Failed to update attachment.';
                        return $result;
                    }
                    $result['upload'] = true;
                    $result['file']   = $filename;
                } else {
                    $this->db->trans_rollback();
                    $result['upload']  = false;
                    $result['success'] = false;
                    $result['message'] = $this->upload->display_errors();
                    return $result;
                }
            }
        
            $references = array();
            foreach ($post['references'] as $ref) {
                $references[] = array(
                    "applicant_id"   => $candidate_id,
                    "ref_name"       => $ref['ref_name'],
                    "ref_contact_no" => $ref['ref_contact_no'],
                    "ref_address"    => $ref['ref_address'],
                    "ref_company"    => $ref['ref_company'],
                    "ref_position"   => $ref['ref_position'],
                    "ref_relationship"      => $ref['ref_relationship'],
                );
            }
            $inserted_references = $this->db->insert_batch($this->referencesTable, $references);
        
            if (!$inserted_references) {
                $this->db->trans_rollback();
                $result['success'] = false;
                $result['message'] = 'Failed to insert references.';
                return $result;
            }
            $educ = array();
            foreach ($post['educational_information_form'] as $edu) {
                $educ[] = array(
                    "applicant_id"   => $candidate_id,
                    "educ_level_type" => $edu['level'],
                    "educ_school"    => $edu['school'],
                    "educ_degree"    => $edu['degree'],
                    "educ_honors"    => $edu['honor'],
                    "educ_from"      => $edu['from'],
                    "educ_to"        => $edu['to'],
                );
            }
            $inserted_educ = $this->db->insert_batch($this->educationTable, $educ);
            if (!$inserted_educ) {
                $this->db->trans_rollback();
                $result['success'] = false;
                $result['message'] = 'Failed to insert education records.';
                return $result;
            }
    
            if (!$post['is_fresh_graduate']) {
                $workexp = array();
    
                foreach ($post['experiences'] as $work) {
                    $workexp[] = array(
                        "applicant_id"   => $candidate_id,
                        "work_company"   => $work['company'],
                        "work_position"  => $work['position'],
                        "work_from"      => $work['from'],
                        "work_to"        => $work['to'],
                        "work_status"    => $work['status'],
                        "work_reason"    => $work['reason'],
                    );
                }
                $inserted_workexp = $this->db->insert_batch("dbhrd.candidate_work_exp", $workexp);
        
                if (!$inserted_workexp) {
                    $this->db->trans_rollback();
                    $result['success'] = false;
                    $result['message'] = 'Failed to insert work experience records.';
                    return $result;
                }
            }
    
        
            $this->db->trans_commit();
            $result['success'] = true;
            $result['message'] = 'Applicant has been registered.';
            return $result;
        }

        public function getCandidates(){
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"0", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $year = (isset($post["year"]) && $post["year"]) ? $post["year"] : date("Y");
            $archive = (isset($post["archive"]) && $post["archive"]) ? $post["archive"] : 0;
            $rowCount = 0;
            $rowData = array();
            $rowData = $this->getCandidatesData($search, $limit, $offset, $sortBy, $sortOrder, $year, $archive);
            $rowCount = $this->getCandidatesDataCount($search,$year, $archive);
            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;
            return $resultset;
        }
    
        private function getCandidatesData($search, $limit, $offset, $sortBy, $sortOrder, $year, $archive){
            $data = array();
            $filterFields = array("a.status", "a.firstname", "a.lastname", "a.schools", "a.courses", "a.positions", "a.recruitment", "a.applied_dt", "a.contact_no", "a.remarks");
            $sql = "a.*, a.resume as attachment, 
                CONCAT(
                    LOWER(a.firstname),
                    IF(a.middlename IS NOT NULL AND a.middlename != '', 
                    CONCAT(' ', UPPER(LEFT(a.middlename, 1)), '.'), 
                    ''),
                    ' ',
                    LOWER(a.lastname)
                ) AS name,
                a.positions, a.recruitment, 
                a.applied_dt, a.remarks, a.contact_no";
                $this->db->select($sql);
                $this->db->from("dbhrd.candidates a");
                $this->db->where("a.is_archive", $archive);
                $this->db->where("a.is_online", 0);
            if ($year && $year != 'All'){
                $this->db->where("YEAR(a.applied_dt)", $year);
            }
      
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
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $this->db->group_by("a.id");
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
    
                foreach ($query->result() as $key => $rs) {
                    if (!empty($rs->positions)) {
                        $positions = explode(',', $rs->positions);
                        $rs->positions = [];
                        foreach ($positions as $position) {
                            $posData = $this->getPositionData(trim($position));
                            if ($posData) {
                                $rs->positions[] = $posData;
                            }
                        }
                    }
                    $rs->resume = (!empty($rs->resume) && file_exists(FCPATH . "uploads/files/hrd/new_resume_{$rs->id}/{$rs->resume}")) ? base_url("uploads/files/hrd/new_resume_{$rs->id}/{$rs->resume}") : null;
                    $rs->education = $this->getEducationData($rs->id);
                    $rs->work_experience = $this->getWorkExperienceData($rs->id);
                    $rs->reference = $this->getReferenceData($rs->id);
    
                    $arrData[$key] = $rs;
                }
        
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
    
            }
            return $data;
          }
    
          private function getCandidatesDataCount($search,$year,$archive){
            $filterFields = array("a.status", "a.firstname", "a.lastname", "a.schools", "a.courses", "a.positions", "a.recruitment", "a.applied_dt", "a.contact_no", "a.remarks");
            $this->db->from("dbhrd.candidates a");
            $this->db->where("a.is_archive", $archive);
            $this->db->where("a.is_online", 0);
            if ($year && $year != 'All'){
                $this->db->where("YEAR(a.applied_dt)", $year);
            }
    
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

        private function getPositionData($id) {
            $this->db->select("name");
            $this->db->from($this->positionTable);
            $this->db->where('id', $id);
            $row = $this->db->get()->row();
            return $row ? $row->name : null;
        }
    
        private function getEducationData($id) {
            $query = $this->db->select("id, REPLACE(educ_level_type, ' ', '_') AS educ_level_type, educ_school, educ_degree, educ_honors, educ_from, educ_to")
                ->from($this->educationTable)
                ->where('applicant_id', $id)
                ->get();
        
            return $query->result_array(); 
        }
    
        private function getWorkExperienceData($id) {
            $query = $this->db->select("id, work_company, work_position, work_from, work_to, work_status, work_reason")
                ->from("dbhrd.candidate_work_exp")
                ->where('applicant_id', $id)
                ->get();
        
            return $query->result_array(); 
        }
    
        private function getReferenceData($id) {
            $query = $this->db->select("id, ref_name, ref_contact_no, ref_address, ref_company, ref_position, ref_relationship")
                ->from($this->referencesTable)
                ->where('applicant_id', $id)
                ->get();
        
            return $query->result_array(); 
        }

        public function getCandidateInformation($id){
            $resultSet = array();
            $this->db->select("a.*, DATE_FORMAT(a.birthdate, '%m/%d/%Y') AS birthdate");
            $this->db->from($this->candidatesTable." a");
            $this->db->where("a.id", $id);
            $candidate = $this->db->get();
            $resultSet['candidate'] = $candidate->row_array();
            
            $resultSet['candidate']['positions'] = !empty($resultSet['candidate']['positions'])
            ? array_map('trim', explode(',', $resultSet['candidate']['positions']))
            : array();

            if($resultSet['candidate']['mrf_assigned_to'] != null && $resultSet['candidate']['mrf_assigned_to'] != 0){
                $resultSet['assigned_manpower_req'] = $this->getAssignedManpowerRequest($resultSet['candidate']['mrf_assigned_to']);
            }
            $resultSet['references'] = $this->getReferenceData($id);
            $resultSet['work_exp'] = $this->getWorkExperienceData($id);
            $resultSet['education'] = $this->getEducationData($id);
            return $resultSet;
        }

        public function getAssignedManpowerRequest($id){
            $this->db->select('a.contract_type, a.relationship_status, a.gender, a.request_nature, a.station, a.age_range, a.people_no, a.mrf_reference_no, a.id, b.name as position, c.description as company, d.description as department, e.firstname, e.middlename, e.lastname');
            $this->db->from('gcceforms.manpower_request_table a');
            $this->db->join('gcchris.tblposition b', 'a.position_id = b.id', 'left');
            $this->db->join('gcchris.tblcompanies c', 'a.company_id = c.id', 'left');
            $this->db->join('gcchris.tbldepartments d', 'a.department_id = d.id', 'left');
            $this->db->join('gccmaster.tblemployees e', 'a.requested_by = e.id', 'left');
            $this->db->where('a.is_archive', 0);
            $this->db->where('a.id', $id);
            $this->db->where('LOWER(a.status)', 'approved');
            $this->db->order_by('a.id', 'DESC');
            $results = $this->db->get()->row();
            return $results;
        }

        public function getAvailableManpowerRequest(){
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"0", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $archive = (isset($post["archive"]) && $post["archive"]) ? $post["archive"] : 0;
            $positions = (isset($post["positions"]) && $post["positions"]) ? $post["positions"] : array();
            $rowCount = 0;
            $rowData = array();
            $rowData = $this->getMrfData($search, $limit, $offset, $sortBy, $sortOrder, $archive, $positions);
            $rowCount = $this->getMrfDataCount($search,$archive,$positions);
            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;
            return $resultset;
        }

        private function getMrfData($search, $limit, $offset, $sortBy, $sortOrder, $archive, $positions){
            $resultset = array();
            $filterFields = array("mrt.status");
            $this->db->select("mrt.type ,mrt.age_range, mrt.gender, mrt.request_nature, mrt.station, mrt.people_no, mrt.mrf_reference_no, mrt.id, mrt.contract_type, dept.code, comp.code as company, pos.name as position");
            $this->db->from('gcceforms.manpower_request_table mrt');
            $this->db->join('gcchris.tbldepartments dept', 'mrt.department_id = dept.id', "LEFT");
            $this->db->join('gcchris.tblcompanies comp', 'mrt.company_id = comp.id', "LEFT");
            $this->db->join('gcchris.tblposition pos', 'mrt.position_id = pos.id', "LEFT");
            $this->db->where('LOWER(mrt.status)','approved');
            $this->db->where_in('mrt.position_id', `[$positions]`);
            $this->db->where('mrt.is_archive',$archive);

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
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $resultset = $query->result(); 
            } else {
                $resultset= []; 
            }
            return $resultset; 
        }

        private function getMrfDataCount($search, $archive, $positions){
            $filterFields = array("mrt.status");
            $this->db->from('gcceforms.manpower_request_table mrt');
            $this->db->join('gcchris.tbldepartments dept', 'mrt.department_id = dept.id', "LEFT");
            $this->db->join('gcchris.tblcompanies comp', 'mrt.company_id = comp.id', "LEFT");
            $this->db->join('gcchris.tblposition pos', 'mrt.position_id = pos.id', "LEFT");
            $this->db->where('LOWER(mrt.status)','approved');
            $this->db->where_in('mrt.position_id', `[$positions]`);
            $this->db->where('mrt.is_archive',$archive);

            if(isset($search)){
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

        public function getCandidateInterview(){
            $post = $this->input->post();
            $candidate_id = $post['candidate_id'];
        
            $this->db->select("
                a.*,
                CONCAT(
                    LOWER(b.firstname),
                    IF(
                        b.middlename IS NOT NULL AND b.middlename != '',
                        CONCAT(' ', UPPER(LEFT(b.middlename, 1)), '.'),
                        ''
                    ),
                    ' ',
                    LOWER(b.lastname)
                ) AS interviewer_name,
                CONCAT(
                    LOWER(c.firstname),
                    IF(
                        c.middlename IS NOT NULL AND c.middlename != '',
                        CONCAT(' ', UPPER(LEFT(c.middlename, 1)), '.'),
                        ''
                    ),
                    ' ',
                    LOWER(c.lastname)
                ) AS created_by_name
            ");
            $this->db->from($this->interviewTable . ' a');
            $this->db->join('gccmaster.tblemployees b', 'a.interviewer_id = b.id', 'left');
            $this->db->join('gccmaster.tblemployees c', 'a.created_by = c.id', 'left');
            $this->db->where('a.candidate_id', $candidate_id);
            $this->db->where('a.is_archive', 0);
            $this->db->order_by('a.id', 'desc');
        
            $query = $this->db->get();
            $result = $query->result_array();
        
            foreach ($result as &$row) {
                $row['assessment'] = array(
                    'status' => $row['status'],
                    'assessment_attachments' => $row['assessment_attachments'],
                    'assessment_remarks' => $row['assessment_remarks'],
                );
        
                // unset($row['status']);
                unset($row['assessment_attachments']);
                unset($row['assessment_remarks']);
            }
        
            return $result;
        }

        public function submitInterviewSchedule(){
            $resultset = array();
            $post = $this->input->post();
            $schedule_dt = !empty($post['schedule_dt']) ? DateTime::createFromFormat('m/d/Y H:i', $post['schedule_dt'])->format('Y-m-d H:i:s') : null;
            $data = array(
                'candidate_id' => $post['candidate_id'],
                'interviewer_id' => $post['interviewer_id'],
                'interview_type	' => $post['interview_type'],
                'schedule_dt' => $schedule_dt,
                'status' => 'pending',
                'platform_id' => $post['platform_id'],
                'remarks' => $post['remarks'],
                'created_by' => $this->user_data['emp_id'],
            );
            $insert = $this->db->insert($this->interviewTable,  $data);

            if($insert){
                $resultset['success'] = true;
                $resultset['toastr_msg'] = 'Interview schedule has been added.';
            }else{
                $resultset['success'] = false;
                $resultset['toastr_msg'] = 'Failed to add interview schedule.';
            }
            return $resultset;
        }
        public function assessCandidateInterview(){
            $post = $this->input->post();
            if (empty($_FILES['files']) || empty($_FILES['files']['name'])) {
                return [
                    'success' => false,
                    'toastr_msg' => 'ATTACHMENT REQUIRED'
                ];
            }
            $candidate_id = $post['candidate_id'];
            $assessment_id = $post['assessment_id'];
        
            $data = array(
                'status' => $post['status'],
                'assessment_remarks' => $post['assessment_remarks'],
            );
        
            $this->db->where('id', $assessment_id);
            $update = $this->db->update($this->interviewTable, $data);
        
            if ($update) {
        
                $uploadedFiles = array();
        
                if (!empty($_FILES['files']['name'][0])) {
                    $uploadPath = FCPATH . 'uploads/files/hrd/new_resume_' . $candidate_id . '/interview_assessment/'.$assessment_id .'/';
        
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
        
                    $filesCount = count($_FILES['files']['name']);
        
                    for ($i = 0; $i < $filesCount; $i++) {
                        if (empty($_FILES['files']['name'][$i])) {
                            continue;
                        }
        
                        $_FILES['file']['name']     = $_FILES['files']['name'][$i];
                        $_FILES['file']['type']     = $_FILES['files']['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                        $_FILES['file']['error']    = $_FILES['files']['error'][$i];
                        $_FILES['file']['size']     = $_FILES['files']['size'][$i];
        
                        $config['upload_path']   = $uploadPath;
                        $config['allowed_types'] = 'pdf|jpg|jpeg|png|gif|webp|bmp';
                        $config['max_size']      = 10240;
                        $config['file_name']     = $_FILES['file']['name'];
        
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
        
                        if ($this->upload->do_upload('file')) {
                            $uploadData = $this->upload->data();
                            $uploadedFiles[] = $uploadData['file_name'];
                        }
                    }
                }
        
                if (!empty($uploadedFiles)) {
                    $attachments = implode(',', $uploadedFiles);
                    $this->db->where('id', $assessment_id);
                    $this->db->update($this->interviewTable, array('assessment_attachments' => $attachments));
                } else {
                    $attachments = null;
                }
        
                $resultset['success'] = true;
                $resultset['uploaded'] = !empty($uploadedFiles);
                $resultset['attachments'] = $attachments;
                $resultset['toastr_msg'] = 'Interview assessment has been updated.';
            } else {
                $resultset['success'] = false;
                $resultset['uploaded'] = false;
                $resultset['attachments'] = null;
                $resultset['toastr_msg'] = 'Failed to update interview assessment.';
            }
        
            return $resultset;
        }

        public function deleteCandidateInterview(){
            $resultset = array();
            $post = $this->input->post();
            $id = $post['id'];
            $data = array(
                'is_archive' => 1,
                'archived_by' => $this->user_data['emp_id'],
                'archived_at' => date('Y-m-d H:i:s'),
            );

            $this->db->where('id', $id);
            $update = $this->db->update($this->interviewTable,  $data);

            if($update){
                $resultset['success'] = true;
                $resultset['toastr_msg'] = 'Candidate has been archived.';
            }else{
                $resultset['success'] = false;
                $resultset['toastr_msg'] = 'Failed to archive candidate.';
            }
            return $resultset;
        }

        public function updateCandidateInformation(){
            $id = $this->input->post('id');
            $updates = $this->input->post('update');
            $to_remove_work_exp = $this->input->post('to_remove_work_exp') ?? [];
            $to_remove_educ_info = $this->input->post('to_remove_educ_info') ?? [];

            $positions = isset($updates['positions']) ? implode(',', $updates['positions']) : null;
            $references = $updates['references'] ?? [];
            $work_exp = $updates['work_experiences'] ?? [];
            $schools = $updates['schools'] ?? [];
            $is_fresh_graduate = $updates['is_fresh_graduate'] ?? [];
            unset($updates['references'], $updates['work_experiences'], $updates['is_fresh_graduate'], $updates['schools'], $updates['positions']) ;
            if($positions !== null){
                $updates['positions'] = $positions;
            }
            if (empty($updates) && empty($references) && empty($work_exp) && empty($to_remove_work_exp) &&  empty($is_fresh_graduate) && empty($to_remove_educ_info) && empty($schools)) {
                return array(
                    'success' => false,
                    'toastr_msg' => 'No changes found.'
                );
            }
        
            $success = true;
        
            if (!empty($updates)) {
                $updates['modify_by'] = $this->user_data['emp_id'];
                $updates['modify_at'] = date('Y-m-d H:i:s');
                $this->db->where('id', $id);
                if (!$this->db->update($this->candidatesTable, $updates)) {
                    $success = false;
                }
            }
        
            $this->db->reset_query();
        
            if (!empty($references)) {
                foreach ($references as $reference) {
                    $referenceId = $reference['id'] ?? null;
                    unset($reference['id']);
        
                    if ($referenceId && !empty($reference)) {
                        $this->db->where('id', $referenceId);
        
                        if (!$this->db->update($this->referencesTable, $reference)) {
                            $success = false;
                        }
        
                        $this->db->reset_query();
                    }
                }
            }

            if ($is_fresh_graduate === 'on') {
                $this->db->where('applicant_id', $id);
            
                if (!$this->db->delete('dbhrd.candidate_work_exp')) {
                    $success = false;
                }
            
                $this->db->reset_query();
            } else {
                if (!empty($work_exp)) {
                    foreach ($work_exp as $work) {
                        $workId = $work['id'] ?? null;
                        unset($work['id']);
            
                        if (!empty($work)) {
                            if (!empty($workId)) {
                                $this->db->where('id', $workId);
                                $this->db->where('applicant_id', $id);
            
                                if (!$this->db->update('dbhrd.candidate_work_exp', $work)) {
                                    $success = false;
                                }
                            } else {
                                $work['applicant_id'] = $id;
            
                                if (!$this->db->insert('dbhrd.candidate_work_exp', $work)) {
                                    $success = false;
                                }
                            }
            
                            $this->db->reset_query();
                        }
                    }
                }
            
                if (!empty($to_remove_work_exp) && is_array($to_remove_work_exp)) {
                    foreach ($to_remove_work_exp as $workId) {
                        if (!empty($workId)) {
                            $this->db->where('id', $workId);
                            $this->db->where('applicant_id', $id);
            
                            if (!$this->db->delete('dbhrd.candidate_work_exp')) {
                                $success = false;
                            }
            
                            $this->db->reset_query();
                        }
                    }
                }
            }

            if (!empty($schools)) {
                foreach ($schools as $school) {
                    $schoolId = $school['id'] ?? null;
                    unset($school['id']);
        
                    if (!empty($school)) {
                        if (!empty($schoolId)) {
                            $this->db->where('id', $schoolId);
                            $this->db->where('applicant_id', $id);
        
                            if (!$this->db->update($this->educationTable, $school)) {
                                $success = false;
                            }
                        } else {
                            $school['applicant_id'] = $id;
        
                            if (!$this->db->insert($this->educationTable, $school)) {
                                $success = false;
                            }
                        }
        
                        $this->db->reset_query();
                    }
                }
            }

            if (!empty($to_remove_educ_info) && is_array($to_remove_educ_info)) {
                foreach ($to_remove_educ_info as $schoolId) {
                    if (!empty($schoolId)) {
                        $this->db->where('id', $schoolId);
                        $this->db->where('applicant_id', $id);
        
                        if (!$this->db->delete($this->educationTable)) {
                            $success = false;
                        }
        
                        $this->db->reset_query();
                    }
                }
            }
        
            return array(
                'success' => $success,
                'toastr_msg' => $success
                    ? 'Candidate information has been updated.'
                    : 'Failed to update personal information.',
            );
        }

        public function updateCandidateAttachment(){
            $candidate_id = $this->input->post('id');
            if (empty($_FILES['file']) || empty($_FILES['file']['name'])) {
                return array(
                    'success'    => false,
                    'toastr_msg' => 'No file attached'
                );
            }
        
            if (empty($candidate_id)) {
                return array(
                    'success'    => false,
                    'toastr_msg' => 'Invalid candidate id'
                );
            }
        
            $uploadPath = FCPATH . 'uploads/files/hrd/new_resume_' . $candidate_id . '/';
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0777, true) && !is_dir($uploadPath)) {
                    return array(
                        'success'    => false,
                        'toastr_msg' => 'Failed to create upload directory'
                    );
                }
            }
        
            $candidate = $this->db->select('resume')->where('id', $candidate_id)->get('dbhrd.candidates')->row_array();
            $oldFile = !empty($candidate['resume']) ? $candidate['resume'] : '';
            if (!empty($oldFile)) {
                $oldFilePath = FCPATH . ltrim($oldFile, '/');
        
                if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }
            
            $originalName = $_FILES['file']['name'];
            $extension    = pathinfo($originalName, PATHINFO_EXTENSION);
            $filenameOnly = pathinfo($originalName, PATHINFO_FILENAME);

            $cleanFileName = str_replace(' ', '_', $filenameOnly);
            $cleanFileName = preg_replace('/[^A-Za-z0-9_\-]/', '', $cleanFileName);

            $config = array(
                'upload_path'   => $uploadPath,
                'allowed_types' => 'pdf|jpg|jpeg|png|gif|webp|bmp',
                'max_size'      => 10240 * 5,
                'encrypt_name'  => false,
                'overwrite'     => true,
                'file_name'     => $cleanFileName . '.' . $extension
            );
        
            $this->load->library('upload');
            $this->upload->initialize($config);
        
            if (!$this->upload->do_upload('file')) {
                return array(
                    'success'    => false,
                    'toastr_msg' => strip_tags($this->upload->display_errors('', ''))
                );
            }
        
            $uploadData = $this->upload->data();
            $fileName   = $uploadData['file_name'];
            $filePath   = 'uploads/files/hrd/new_resume_' . $candidate_id . '/' . $fileName;
        
            $updated = $this->db
                ->where('id', $candidate_id)
                ->update('dbhrd.candidates', array(
                    'resume' => $cleanFileName . '.' . $extension,
                    'modify_at' => date('Y-m-d H:i:s'),
                    'modify_by' => $this->user_data['emp_id'],
                ));
        
            if (!$updated) {
                $newFileFullPath = $uploadPath . $fileName;
        
                if (file_exists($newFileFullPath) && is_file($newFileFullPath)) {
                    @unlink($newFileFullPath);
                }
        
                return array(
                    'success'    => false,
                    'toastr_msg' => 'Failed to update candidate attachment'
                );
            }
        
            return array(
                'success'    => true,
                'toastr_msg' => 'Resume uploaded successfully',
                'file_name'  => $fileName,
                'file_path'  => $filePath
            );
        }

        public function updateInterviewAssessment()
        {
            $post = $this->input->post();
        
            $to_remove = isset($post['to_remove']) ? json_decode($post['to_remove'], true) : [];
            $id = $post['assessment_id'];
            $candidate_id = $post['candidate_id'];
        
            unset($post['to_remove'], $post['assessment_id'], $post['candidate_id']);
        
            $this->db->where('id', $id);
            $this->db->where('candidate_id', $candidate_id);
            $current = $this->db->get($this->interviewTable)->row_array();
        
            if (!$current) {
                return [
                    'success' => false,
                    'toastr_msg' => 'Interview assessment not found.'
                ];
            }
        
            $existingAttachments = [];
            if (!empty($current['assessment_attachments'])) {
                $existingAttachments = array_filter(array_map('trim', explode(',', $current['assessment_attachments'])));
            }
        
            if (!empty($to_remove) && is_array($to_remove)) {
                foreach ($to_remove as $fileName) {
                    $filePath = FCPATH . 'uploads/files/hrd/new_resume_' . $candidate_id . '/interview_assessment/'.$id.'/'. $fileName;
        
                    if (file_exists($filePath) && is_file($filePath)) {
                        @unlink($filePath);
                    }
        
                    $existingAttachments = array_values(array_filter($existingAttachments, function ($item) use ($fileName) {
                        return $item !== $fileName;
                    }));
                }
            }
        
            $uploadedFiles = [];
            if (!empty($_FILES['files']['name']) && count(array_filter($_FILES['files']['name'])) > 0) {
                $uploadPath = FCPATH . 'uploads/files/hrd/new_resume_' . $candidate_id . '/interview_assessment/'.$id . '/';
        
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
        
                $files = $_FILES['files'];
        
                for ($i = 0; $i < count($files['name']); $i++) {
                    if (empty($files['name'][$i])) {
                        continue;
                    }
        
                    $_FILES['temp_file']['name'] = $files['name'][$i];
                    $_FILES['temp_file']['type'] = $files['type'][$i];
                    $_FILES['temp_file']['tmp_name'] = $files['tmp_name'][$i];
                    $_FILES['temp_file']['error'] = $files['error'][$i];
                    $_FILES['temp_file']['size'] = $files['size'][$i];
        
                    $config['upload_path'] = $uploadPath;
                    $config['allowed_types'] = 'pdf|jpg|jpeg|png|gif|webp|bmp';
                    $config['max_size'] = 51200; // 50MB in KB
                    $config['file_name'] = $_FILES['temp_file']['name'];
                    $config['overwrite'] = false;
        
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
        
                    if ($this->upload->do_upload('temp_file')) {
                        $uploadData = $this->upload->data();
                        $uploadedFiles[] = $uploadData['file_name'];
                    } else {
                        return [
                            'success' => false,
                            'toastr_msg' => strip_tags($this->upload->display_errors())
                        ];
                    }
                }
            }
        
            $finalAttachments = array_merge($existingAttachments, $uploadedFiles);
            $finalAttachments = array_values(array_unique(array_filter($finalAttachments)));
            var_dump($existingAttachments, $uploadedFiles);
            $data = [];

            if (isset($post['result'])) {
                $data['status'] = $post['result'];
            }
            
            if (isset($post['assessment_remarks'])) {
                $data['assessment_remarks'] = $post['assessment_remarks'];
            }
            
            if (isset($finalAttachments)) {
                $data['assessment_attachments'] = !empty($finalAttachments)
                    ? implode(',', $finalAttachments)
                    : null;
            }
            var_dump($data);
            if(empty($data)){
                return [
                    'success' => false,
                    'toastr_msg' => 'No changes found.'
                ];
            }
        
            $this->db->where('id', $id);
            $this->db->where('candidate_id', $candidate_id);
            $update = $this->db->update($this->interviewTable, $data);
        
            if ($update) {
                return [
                    'success' => true,
                    'toastr_msg' => 'Interview assessment updated successfully.'
                ];
            }
        
            return [
                'success' => false,
                'toastr_msg' => 'Failed to update interview assessment.'
            ];
        }

        public function hireCandidate(){
            $resultset = array();
            $post = $this->input->post();
            $data = array(
                'employee_status' => 'Active',
                'add_date' => date('Y-m-d H:i:s'),
                'add_by' => $this->user_data['emp_id'],
                'is_incomplete' => 1,
                'company_id' => $post['company_id'] ?? null,
                'department_id' => $post['department_id'] ?? null,
                'position' => $post['position_id'] ?? null,
                'level' => $post['level'] ?? null,
                'firstname' => $post['applicant_data']['firstname'] ?? '',
                'middlename' => $post['applicant_data']['middlename'] ?? '',
                'lastname' => $post['applicant_data']['lastname'] ?? '',
                'suffix' => $post['applicant_data']['suffix'] ?? '',
                'gender' => $post['applicant_data']['gender'] ?? '',
                'civil_stat' => $post['applicant_data']['civil_status'] ?? '',
                'religion' => $post['applicant_data']['religion'] ?? '',
                'height' => $post['applicant_data']['height'] ?? '',
                'weight' => $post['applicant_data']['weight'] ?? '',
                'bday' => !empty($post['applicant_data']['birthdate'])
                    ? date('Y-m-d', strtotime($post['applicant_data']['birthdate']))
                    : null,
                'citizenship' => $post['applicant_data']['citizenship'] ?? '',
                'mobile_no' => $post['applicant_data']['contact_no'] ?? '',
                'email' => $post['applicant_data']['email'] ?? '',
                'tel_no' => $post['applicant_data']['tel_no'] ?? '',
                'curr_addr' => $post['applicant_data']['address'] ?? '',
                'prov_addr' => $post['applicant_data']['permanent_address'] ?? '',
            );

            $this->db->trans_begin();
            $save = $this->db->insert('gccmaster.tblemployees', $data);
            if ($save) {
                $employee_id = $this->db->insert_id();
                $references = $post['applicant_data']['references'] ?? array();
                $work_experiences = $post['applicant_data']['workExperiences'] ?? array();
                $educational_background = $post['applicant_data']['educInfo'] ?? array();
                // $resume = $post['applicant_data']['uploadedFile'] ?? '';
                if (!empty($references)) {
                    $reference_data = array();
                    foreach ($references as $ref) {
                        $reference_data[] = array(
                            'emp_id' => $employee_id,
                            'ref_name' => $ref['ref_name'] ?? '',
                            'ref_contact_no' => $ref['ref_contact_no'] ?? '',
                            'ref_address' => $ref['ref_address'] ?? '',
                            'ref_company' => $ref['ref_company'] ?? '',
                            'ref_position' => $ref['ref_position'] ?? '',
                            'ref_relationship' => $ref['ref_relationship'] ?? '',
                            'add_date' => date('Y-m-d H:i:s'),
                            'add_by' => $this->user_data['emp_id'],
                        );
                    }

                    if (!empty($reference_data)) {
                        $this->db->insert_batch('gcchris.tblreferences', $reference_data);
                    }
                }

                if(!empty($work_experiences)) {
                    $workExpData = array();
                    foreach ($work_experiences as $work) {
                        $workExpData[] = array(
                            'emp_id' => $employee_id,
                            'work_company' => $work['work_company'] ?? '',
                            'work_position' => $work['work_position'] ?? '',
                            'work_from' => $work['work_from'] ?? '',
                            'work_to' => $work['work_to'] ?? '',
                            'work_status' => $work['work_status'] ?? '',
                            'work_reason' => $work['work_reason'] ?? '',
                            'add_date' => date('Y-m-d H:i:s'),
                            'add_by' => $this->user_data['emp_id'],
                        );
                    }
                    if (!empty($workExpData)) {
                        $this->db->insert_batch('gcchris.tblworkxps', $workExpData);
                    }
                }

                if(!empty($educational_background)) {
                    $educData = array();
                    foreach ($educational_background as $educ) {
                        $educData[] = array(
                            'emp_id' => $employee_id,
                            'educ_level_type' => $educ['educ_level_type'] ?? '',
                            'educ_school' => $educ['educ_school'] ?? '',
                            'educ_degree' => $educ['educ_degree'] ?? '',
                            // 'educ_units' => $educ['educ_units'] ?? '',
                            'educ_honors' => $educ['educ_honors'] ?? '',
                            'educ_from' => $educ['educ_from'] ?? '',
                            'educ_to' => $educ['educ_to'] ?? '',
                            'add_date' => date('Y-m-d H:i:s'),
                            'add_by' => $this->user_data['emp_id'],
                        );
                    }
                    if (!empty($educData)) {
                        $this->db->insert_batch('gcchris.tbleducations', $educData);
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $resultset = array(
                    'success' => false,
                    'toastr_msg' => 'Failed to hire candidate. Transaction error.'
                );
            } else {
                $this->db->trans_commit();
                $resultset = array(
                    'success' => true,
                    'employee_id' => $employee_id,
                    'toastr_msg' => 'Candidate hired successfully.'
                );
            }

            return $resultset;
        }


    }