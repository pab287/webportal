<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Document_model extends CI_Model{
    protected $resumeTable = "dbhrd.document_body";
    private $twoYearsDate, $checkDate;
    function __construct()
    {
        parent::__construct();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model", "dt_model");
        $this->load->model("ams/Utilities_model", "utilities");
        $this->load->model("core/upload_model", "core_upload");
        date_default_timezone_set('Asia/Manila');
        $this->twoYearsDate = strtotime("-2 years", time());
        $this->checkDate = date("Y-m-d", $this->twoYearsDate);
    }

    private function getUserData()
    {
        return $this->core_layout->getUserLoggedIn();
    }

    function getResumeCollection()
    {
      $resultset = array();
      $post = $this->input->post();
      $order_val = array(array("column"=>"0", "dir"=>"desc"));
      $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
      $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
      $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
      $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
      $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
      $year = (isset($post["year"]) && $post["year"]) ? $post["year"] : date("Y");
      $applicationDate = (isset($post["application_date"]) && $post["application_date"]) ? $post["application_date"] : false;
      $rowCount = 0;
      $rowData = array();
      $rowData = $this->get_resume($search, $limit, $offset, $sortBy, $sortOrder, $applicationDate);
      $rowCount = $this->get_resume_count($search,$applicationDate);
      $resultset["recordsTotal"] = $rowCount;
      $resultset["recordsFiltered"] = $rowCount;
      $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_resume($search, $limit, $offset, $sortBy, $sortOrder, $applicationDate){
      $data = array();
      $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt", "a.contact_no", "a.description");
      $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt, b.filename, a.remarks, a.contact_no, a.description";
      $this->db->select($sql);
      $this->db->from("dbhrd.document_body a");
      $this->db->join("dbhrd.file_attachment b", "a.id = b.body_id", "left");
      $this->db->where('vacancy_status != ', 'resolved');
      $this->db->where('vacancy_status != ', 'archived');
      $this->db->where('status != ', 'hired');
      $this->db->where('status != ', 'blacklisted');
    //   $this->db->where("YEAR(applied_dt)", $year);
      if($applicationDate){
        list($startDate, $endDate) = explode(' - ', $applicationDate);
        $startDateFormatted = DateTime::createFromFormat('Y/m/d', $startDate)->format('Y-m-d');
        $endDateFormatted = DateTime::createFromFormat('Y/m/d', $endDate)->format('Y-m-d');
        $this->db->where("a.applied_dt BETWEEN '{$startDateFormatted}' AND '{$endDateFormatted}'");
      }else{
        $this->db->where('YEAR(a.applied_dt)', date('Y'));
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

      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        $arrData = array();
        foreach ($query->result() as $key => $rs) {
            $resume_file_path = "uploads/files/hrd/resume_".$rs->id."/" . $rs->filename;
            if(file_exists(realpath($resume_file_path)) && $rs->filename != ""){
                $rs->filename = '<a href="../'.$resume_file_path.'" target="_blank">'.$rs->filename.'</a>';
            }else if(file_exists(realpath('../uploads/files/hrd/'. $rs->filename)) && $rs->filename != ""){
                $rs->filename = '<a href="../uploads/files/hrd/' . $rs->filename .'" target="_blank">'.$rs->filename.'</a>';
            }else if($rs->filename == ""){
                $rs->filename = "N/A";
            }else{
                $rs->filename = "N/A";
            }
            
            if($rs->status == 'DONEINTERVIEW'){
                $temp_status = 'done interview';
            }else if($rs->status == 'FORINTERVIEW'){
                $temp_status = 'for interview';
            }else{
                $temp_status = $rs->status;
            }
            $rs->status = $temp_status;
            $arrData[$key] = $rs;
        }


        foreach ($arrData as $k => $v) {
            $data[] = $v;
        }
    }
    return $data;
    }

    private function get_resume_count($search,$applicationDate){
      $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt", "a.contact_no", "a.description");
      $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt, b.filename, a.remarks, a.contact_no, a.description";
      $this->db->select($sql);
      $this->db->from("dbhrd.document_body a");
      $this->db->join("dbhrd.file_attachment b", "a.id = b.body_id", "left");
      $this->db->where('vacancy_status != ', 'resolved');
      $this->db->where('vacancy_status != ', 'archived');
      $this->db->where('status != ', 'hired');
      $this->db->where('status != ', 'blacklisted');

      if($applicationDate){
        list($startDate, $endDate) = explode(' - ', $applicationDate);
        $startDateFormatted = DateTime::createFromFormat('Y/m/d', $startDate)->format('Y-m-d');
        $endDateFormatted = DateTime::createFromFormat('Y/m/d', $endDate)->format('Y-m-d');
        $this->db->where("a.applied_dt BETWEEN '{$startDateFormatted}' AND '{$endDateFormatted}'");
      }else{
        $this->db->where('YEAR(a.applied_dt)', date('Y'));
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

    function getHiredCollection()
    {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $rowCount = 0;
        $rowData = array();

        $rowData = $this->getHiredData($limit, $offset, $sortBy, $sortOrder, $search);
        $rowCount = $this->get_hired_data_count($search);
        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }


    private function getHiredData($limit, $offset, $sortBy, $sortOrder, $search = null)
    {
      $data = array();
      $filterFields = array('id','firstname','middlename','lastname', 'school', 'course', 'position', 'tag1', 'recruitment', 'applied_dt'); 
      $this->db->select('hired_dt,id, school, course, position, tag1, recruitment, applied_dt, CONCAT(firstname, " ", lastname) AS name');
      $this->db->from('dbhrd.document_body');
      $this->db->where('vacancy_status != ', 'archived');
      $this->db->where('status', 'hired');
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

  $query = $this->db->get();
  if ($query->num_rows() > 0) {
      $data = $query->result();
  }
  return $data;
    }

  private function get_hired_data_count($search){
    $filterFields = array('id','firstname','middlename','lastname', 'school', 'course', 'position', 'tag1', 'recruitment', 'applied_dt');
    $this->db->select('*');
    $this->db->from('dbhrd.document_body');
    $this->db->where('vacancy_status != ', 'archived');
    $this->db->where('status', 'hired');
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

    private function get_all_resume($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $sql = "a.id, UPPER(a.status) as status, a.vacancy_status, UPPER(CONCAT(a.firstname,' ',a.lastname)) AS name, a.school, a.course, UPPER(a.position) as position, a.tag1, UPPER(a.recruitment) as recruitment, a.applied_dt, b.filename, a.description, a.remarks, a.contact_no";
        $this->db->select($sql);
        $this->db->from("dbhrd.document_body a");
        $this->db->join("dbhrd.file_attachment b", "a.id = b.body_id", "left");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('status != ', 'hired');
        $this->db->where('status != ', 'blacklisted');
        $this->db->where('created_dt >= ', $this->checkDate);
        $this->db->group_by('name');
        $this->db->group_by('position');
        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("c.id", "DESC");
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $resume_file_path = "uploads/files/hrd/resume_".$rs->id."/" . $rs->filename;
                if(file_exists(realpath($resume_file_path)) && $rs->filename != ""){
                    $rs->filename = '<a href="../'.$resume_file_path.'" target="_blank">'.$rs->filename.'</a>';
                }else if(file_exists(realpath('../uploads/files/hrd/'. $rs->filename)) && $rs->filename != ""){
                    $rs->filename = '<a href="../uploads/files/hrd/' . $rs->filename .'" target="_blank">'.$rs->filename.'</a>';
                }else if($rs->filename == ""){
                    $rs->filename = "N/A";
                }else{
                    $rs->filename = "N/A";
                }
                
                if($rs->status == 'DONEINTERVIEW'){
                    $temp_status = 'done interview';
                }else if($rs->status == 'FORINTERVIEW'){
                    $temp_status = 'for interview';
                }else{
                    $temp_status = $rs->status;
                }
                $rs->status = $temp_status;
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }

            return $data;
        } else {
            return array();
        }
    }

    private function get_searched_resume($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt", "a.contact_no", "a.description");
            $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt, b.filename, a.remarks, a.contact_no, a.description";
            $this->db->select($sql);
            $this->db->from("dbhrd.document_body a");
            $this->db->join("dbhrd.file_attachment b", "b.body_id = a.id", "left");
            $this->db->where('vacancy_status != ', 'resolved');
            $this->db->where('vacancy_status != ', 'archived');
            $this->db->where('status != ', 'hired');
            $this->db->where('status != ', 'blacklisted');
            $this->db->where('created_dt >= ', $this->checkDate);
            $this->db->group_by('name');
            $this->db->group_by('position');
            $this->db->group_start();

            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }

            $this->db->or_like("CONCAT(a.firstname, ' ', a.lastname)", $search["value"], "both");
            $this->db->or_like("CONCAT(a.firstname, a.lastname)", $search["value"], "both");
            $this->db->group_end();

            if ((int)$limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                // var_dump($query->result());

                $arrData = array();
                
                foreach ($query->result() as $key => $rs) {
                        
                    $resume_file_path = "uploads/files/hrd/resume_".$rs->id."/" . $rs->filename;
                    if(file_exists(realpath($resume_file_path))){
                        $rs->filename = '<a href="../'.$resume_file_path.'" target="_blank">'.$rs->filename.'</a>';
                    }else if(file_exists(realpath('../uploads/files/hrd/'. $rs->filename))){
                        $rs->filename = '<a href="../uploads/files/hrd/' . $rs->filename .'" target="_blank">'.$rs->filename.'</a>';
                    }else{
                        $rs->filename = "N/A";
                    }
                    if($rs->status === 'forinterview'){
                        $temp_status = 'for interview';
                    }else if($rs->status === 'doneinterview'){
                        $temp_status = 'done interview';
                    }else{
                        $temp_status = $rs->status;
                    }
                    $rs->status = $temp_status;
                    $arrData[$key] = $rs; 
                }

                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }

                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_resume_count($search = null)
    {
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt", "a.description");
            $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt, b.filename, a.description";
            $this->db->select($sql);
            $this->db->from("dbhrd.document_body a");
            $this->db->join("dbhrd.file_attachment b", "b.body_id = a.id");
            $this->db->where('vacancy_status != ', 'resolved');
            $this->db->where('vacancy_status != ', 'archived');
            $this->db->where('status != ', 'hired');
            $this->db->where('created_dt >= ', $this->checkDate);

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }

            $this->db->or_like("CONCAT(a.firstname, ' ', a.lastname)", $search["value"], "both");

            $this->db->group_end();
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function getArchiveCollection()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();
        if (!$search) {
            $rowData = $this->get_all_archive($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_archive_count();
        }

        if ($search) {
            $rowData = $this->get_searched_archive($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_archive_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_archive_count()
    {
        $this->db->from("dbhrd.document_body a");
        $this->db->where("(a.vacancy_status='archived' OR a.created_dt<='$check')");
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_archive($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $temp = strtotime("-2 year", time());
        $check = date("Y-m-d", $temp);
        $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt, a.remarks";
        $this->db->select($sql);
        $this->db->from("dbhrd.document_body a");
        $this->db->where("(a.vacancy_status='archived' OR a.created_dt<='$check')");

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                if($rs->status == 'forinterview'){
                    $temp_status = 'for interview';
                }else if($rs->status == 'doneinterview'){
                    $temp_status = 'done interview';
                }else{
                    $temp_status = $rs->status;
                }
                $rs->status = $temp_status;
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }

            return $data;
        } else {
            return array();
        }
    }


    private function get_searched_archive($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $temp = strtotime("-2 year", time());
            $check = date("Y-m-d", $temp);
            $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt");
            $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt, a.remarks";
            $this->db->select($sql);
            $this->db->from("dbhrd.document_body a");
            $this->db->where("(a.vacancy_status='archived' OR a.created_dt<='$check')");
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->or_like("CONCAT(a.firstname, ' ', a.lastname)", $search["value"], "both");
            $this->db->or_like("CONCAT(a.firstname, a.lastname)", $search["value"], "both");
            $this->db->group_end();

            if ((int)$limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $arrData[$key] = $rs;
                }

                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }

                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_archive_count($search = null)
    {

        $rowCount = 0;
        if ($search) {
            $temp = strtotime("-2 year", time());
            $check = date("Y-m-d", $temp);
            $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt");
            $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt";
            $this->db->select($sql);
            $this->db->from("dbhrd.document_body a");
            $this->db->where("(a.vacancy_status='archived' OR a.created_dt<='$check')");

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function getPendingCollection()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();
        if (!$search) {
            $rowData = $this->get_all_pending($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_pending_count();
        }

        if ($search) {
            $rowData = $this->get_searched_pending($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_pending_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_pending_count()
    {
        $this->db->from("dbhrd.document_body");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('status', 'pending');
        $this->db->where('created_dt >= ', $this->checkDate);
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_pending($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt";
        $this->db->select($sql);
        $this->db->from("dbhrd.document_body a");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('status', 'pending');
        $this->db->where('created_dt >= ', $this->checkDate);

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }

            return $data;
        } else {
            return array();
        }
    }


    private function get_searched_pending($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt");
            $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt";
            $this->db->select($sql);
            $this->db->from("dbhrd.document_body a");
            $this->db->where('vacancy_status != ', 'resolved');
            $this->db->where('vacancy_status != ', 'archived');
            $this->db->where('status', 'pending');
            $this->db->where('created_dt >= ', $this->checkDate);
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->or_like("CONCAT(a.firstname, ' ', a.lastname)", $search["value"], "both");
            $this->db->or_like("CONCAT(a.firstname, a.lastname)", $search["value"], "both");
            $this->db->group_end();

            if ((int)$limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $arrData[$key] = $rs;
                }

                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }

                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_pending_count($search = null)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt");
            $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt";
            $this->db->select($sql);
            $this->db->from("dbhrd.document_body a");
            $this->db->where('vacancy_status != ', 'resolved');
            $this->db->where('vacancy_status != ', 'archived');
            $this->db->where('status', 'pending');
            $this->db->where('created_dt >= ', $this->checkDate);

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function getInterviewCollection()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();
        if (!$search) {
            $rowData = $this->get_all_interview($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_interview_count();
        }

        if ($search) {
            $rowData = $this->get_searched_interview($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_interview_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_interview_count()
    {
        $this->db->from("dbhrd.document_body");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('status', 'forinterview');
        $this->db->where('created_dt >= ', $this->checkDate);
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_interview($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt";
        $this->db->select($sql);
        $this->db->from("dbhrd.document_body a");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('status', 'forinterview');
        $this->db->where('created_dt >= ', $this->checkDate);

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                if($rs->status == 'forinterview'){
                    $temp_status = 'for interview';
                }else if($rs->status == 'doneinterview'){
                    $temp_status = 'done interview';
                }else{
                    $temp_status = $rs->status;
                }
                $rs->status = $temp_status;
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }

            return $data;
        } else {
            return array();
        }
    }


    private function get_searched_interview($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt");
            $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt";
            $this->db->select($sql);
            $this->db->from("dbhrd.document_body a");
            $this->db->where('vacancy_status != ', 'resolved');
            $this->db->where('vacancy_status != ', 'archived');
            $this->db->where('status', 'forinterview');
            $this->db->where('created_dt >= ', $this->checkDate);
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->or_like("CONCAT(a.firstname, ' ', a.lastname)", $search["value"], "both");
            $this->db->or_like("CONCAT(a.firstname, a.lastname)", $search["value"], "both");
            $this->db->group_end();

            if ((int)$limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if($rs->status == 'forinterview'){
                        $temp_status = 'for interview';
                    }else if($rs->status == 'doneinterview'){
                        $temp_status = 'done interview';
                    }else{
                        $temp_status = $rs->status;
                    }
                    $rs->status = $temp_status;
                    $arrData[$key] = $rs;
                }

                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }

                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_interview_count($search = null)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt");
            $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt";
            $this->db->select($sql);
            $this->db->from("dbhrd.document_body a");
            $this->db->where('vacancy_status != ', 'resolved');
            $this->db->where('vacancy_status != ', 'archived');
            $this->db->where('status', 'forinterview');
            $this->db->where('created_dt >= ', $this->checkDate);

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    public function deleteBlacklisted(){
        $post = $this->input->post();

        $id = $post['id'];
        $this->db->where("id", $id);
        $query = $this->db->get("dbhrd.document_body");
        $filename = $query->row_array();

        if(file_exists("uploads/files/hrd/".$filename['filename'])){
            unlink("uploads/files/hrd/".$filename);
        }

        $this->db->where("body_id", $id);
        $attach_files = $this->db->get("dbhrd.file_attachment");
        $attachFiles = $attach_files->result_array();
        if($attach_files->num_rows() != 0){
            foreach($attachFiles as $files){
                unlink("uploads/files/hrd/resume_".$id."/".$files['filename']);
            }
            rmdir("uploads/files/hrd/resume_".$id);
        }

        $this->db->where("id", $id);
        $del = $this->db->delete("dbhrd.document_body");
        if($del){
            return true;
        }else{
            return false;
        }
    }

    public function blacklistedPreviewFile(){
        $post = $this->input->post();
        $id = $post['id'];
        $body_id = $post['body_id'];
        $this->db->where("id", $id);
        $data = $this->db->get("dbhrd.file_attachment");
        $file = $data->row_array();
        if($data->num_rows() != 0){
            return $file;
        }else{
            $this->db->where("id", $body_id);
            $doc_file = $this->db->get("dbhrd.document_body");
            return $doc_file->row_array();
        }
    }

    function getBlacklistCollection()
    {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

        $rowCount = 0;
        $rowData = array();
        $rowData = $this->getBlacklistData($limit, $offset, $sortBy, $sortOrder,$search);
        $rowCount = $this->getBlacklistDataCount($search);
        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function getBlacklistData($limit, $offset, $sortBy, $sortOrder,$search){
      $data = array();
      $filterFields = array('id','firstname','middlename','lastname', 'school', 'course', 'position', 'tag1', 'recruitment', 'applied_dt'); 
      $this->db->select('id, school, course, position, tag1, recruitment, applied_dt, CONCAT(firstname, " ", lastname) AS name,status,blacklisted_dt');
      $this->db->from('dbhrd.document_body');
      $this->db->where('status','blacklisted');
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
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        $data = $query->result();
    }
    return $data;
    }

    private function getBlacklistDataCount($search){
      $filterFields = array('id','firstname','middlename','lastname', 'school', 'course', 'position', 'tag1', 'recruitment', 'applied_dt');
      $this->db->from('dbhrd.document_body');
      $this->db->where('status','blacklisted');
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

    function getForInterviewCollection()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? str_replace(' ', '', strval($post["search"]['value'])): false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";
        $rowCount = 0;
        $rowData = array();
        if (!$search) {
            $rowData = $this->get_all_forinterview($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_forinterview_count();
        }

        if ($search) {
            $rowData = $this->get_searched_forinterview($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_forinterview_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_forinterview_count()
    {
        $this->db->from("dbhrd.document_body");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('status', 'forinterview');
        $this->db->where('created_dt >= ', $this->checkDate);
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_all_forinterview($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt, a.interview_dt, a.remarks, a.contact_no";
        $this->db->select($sql);
        $this->db->from("dbhrd.document_body a");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('status', 'forinterview');
        $this->db->where('created_dt >= ', $this->checkDate);

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }

            return $data;
        } else {
            return array();
        }
    }


    private function get_searched_forinterview($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt", "a.interview_dt", "a.remarks", "a.contact_no");
            $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt, a.interview_dt, a.remarks, a.contact_no";
            $this->db->select($sql);
            $this->db->from("dbhrd.document_body a");
            $this->db->where('vacancy_status != ', 'resolved');
            $this->db->where('vacancy_status != ', 'archived');
            $this->db->where('status', 'forinterview');
            $this->db->where('created_dt >= ', $this->checkDate);
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->or_like("CONCAT(a.firstname,' ', a.lastname)", $search, "both");
            $this->db->or_like("CONCAT(a.firstname, a.lastname)", $search, "both");
            $this->db->group_end();

            if ((int)$limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $arrData[$key] = $rs;
                }

                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }

                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_forinterview_count($search = null)
    {

        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", "a.status", "a.vacancy_status", "a.firstname", "a.lastname", "a.school", "a.course", "a.position", "a.tag1", "a.recruitment", "a.applied_dt","a.interview_dt","a.remarks","a.contact_no");
            $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt, a.interview_dt, a.remarks, a.contact_no";
            $this->db->select($sql);
            $this->db->from("dbhrd.document_body a");
            $this->db->where('vacancy_status != ', 'resolved');
            $this->db->where('vacancy_status != ', 'archived');
            $this->db->where('status', 'forinterview');
            $this->db->where('created_dt >= ', $this->checkDate);

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function getTag()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `tag` FROM dbhrd.tag WHERE `tag` LIKE '%{$get['q']}%' ORDER BY `tag` ASC");
        } else {
            $query = $this->db->query("SELECT `tag` FROM dbhrd.tag ORDER BY `tag` ASC");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["tag"];
                $data["text"] = $_query["tag"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    function getSchool()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `school` FROM dbhrd.school WHERE `school` LIKE '%{$get['q']}%' ORDER BY `school` ASC");
        } else {
            $query = $this->db->query("SELECT `school` FROM dbhrd.school ORDER BY `school` ASC");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["school"];
                $data["text"] = $_query["school"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    function getCourse()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `course` FROM dbhrd.course WHERE `course` LIKE '%{$get['q']}%' ORDER BY `course` ASC");
        } else {
            $query = $this->db->query("SELECT `course` FROM dbhrd.course ORDER BY `course` ASC");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["course"];
                $data["text"] = $_query["course"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    function getPosition()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `name` FROM gcchris.tblposition WHERE `name` LIKE '%{$get['q']}%' ORDER BY `name` ASC");
        } else {
            $query = $this->db->query("SELECT `name` FROM gcchris.tblposition ORDER BY `name` ASC");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["name"];
                $data["text"] = $_query["name"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    public function save_resume($data)
    {
        $this->db->insert('dbhrd.document_body', $data);
        return $this->db->insert_id();
    }

    public function edit_resume($id)
    {
        $this->db->from('dbhrd.document_body');
        $this->db->where('id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function update_resume($where, $data)
    {
        $this->db->update('dbhrd.document_body', $data, $where);
        return $this->db->affected_rows();
    }

    public function archive($where, $data)
    {
        $this->db->update('dbhrd.document_body', $data, $where);
        return $this->db->affected_rows();
    }

    public function delete_resume($id)
    {
        $this->db->where('body_id', $id);
        $query = $this->db->get("dbhrd.file_attachment");
        if($this->db->get("dbhrd.file_attachment")->num_rows() > 1){
            $rows_array = $query->result_array();
            foreach($rows_array as $data){
                unlink("uploads/files/hrd/resume_".$id."/".$data['filename']);
            }
        }else if($this->db->get("dbhrd.file_attachment")->num_rows() == 1){
            $row_array = $query->row_array();
            unlink("uploads/files/hrd/resume_".$id."/".$row_array['filename']);
        }
        rmdir("uploads/files/hrd/resume_".$id);
        $this->db->where('id', $id);
        $this->db->delete('dbhrd.document_body');
        $this->db->where('body_id', $id);
        $this->db->delete('dbhrd.file_attachment');
    }

    function getWeekCollection()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 100;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_searched_week_count($limit, $offset, $sortBy, $sortOrder);

        $totalNotFiltered = $rowCount;

        $resultset["data"] = $rowData;

        return $resultset;
    }


    private function get_searched_week_count($limit = 100, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $check = date('Y-m-d', strtotime("-7 days"));

        $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname,' ',a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.applied_dt";
        $this->db->select($sql);
        $this->db->from("dbhrd.document_body a");
        $this->db->where('created_dt >= ', $check);

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }

            return $data;
        } else {
            return array();
        }
    }

    function count_resume()
    {
        $this->db->from("dbhrd.document_body");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('created_dt >= ', $this->checkDate);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_archive()
    {
        $temp = strtotime("-2 year", time());
        $check = date("Y-m-d", $temp);
        $this->db->from("dbhrd.document_body a");
        $this->db->where("(a.vacancy_status='archived' OR a.created_dt<='$check')");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_total()
    {

        $this->db->from("dbhrd.document_body");

        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_mynimo()
    {
        $this->db->from("dbhrd.document_body");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('created_dt >= ', $this->checkDate);
        $this->db->where('recruitment ', 'Mynimo');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_jobstreet()
    {
        $this->db->from("dbhrd.document_body");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('created_dt >= ', $this->checkDate);
        $this->db->where('recruitment ', 'Jobstreet');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_walk()
    {
        $this->db->from("dbhrd.document_body");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('created_dt >= ', $this->checkDate);
        $this->db->where('recruitment ', 'Walk In');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_referral()
    {
        $this->db->from("dbhrd.document_body");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('created_dt >= ', $this->checkDate);
        $this->db->where('recruitment ', 'REFERRAL');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function get_total_position($name)
    {
        $this->db->from("dbhrd.document_body");
        $this->db->where('vacancy_status != ', 'resolved');
        $this->db->where('vacancy_status != ', 'archived');
        $this->db->where('created_dt >= ', $this->checkDate);
        $this->db->like('position', $name);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getSupervisory()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->getSupervisoryCount($limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_all_Supervisory_count();


        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_Supervisory_count()
    {
        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'SUPERVISORY');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getSupervisoryCount($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'SUPERVISORY');

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("name", "ASC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["name"] = $_query["name"];
                $data["total"] = $this->get_total_position($_query["name"]);
                $resultarray[] = $data;
            }

            return $resultarray;
        } else {
            return array();
        }
        $query = $this->db->get();


    }

    function getManagerial()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->getManagerialCount($limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_all_Managerial_count();


        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_Managerial_count()
    {
        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'MANAGERIAL');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getManagerialCount($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'MANAGERIAL');

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("name", "ASC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["name"] = $_query["name"];
                $data["total"] = $this->get_total_position($_query["name"]);
                $resultarray[] = $data;
            }

            return $resultarray;
        } else {
            return array();
        }
        $query = $this->db->get();
    }

    function getSkilled()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->getSkilledCount($limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_all_Skilled_count();


        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_Skilled_count()
    {
        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'SKILLED RANK AND FILE');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getSkilledCount($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'SKILLED RANK AND FILE');

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("name", "ASC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["name"] = $_query["name"];
                $data["total"] = $this->get_total_position($_query["name"]);
                $resultarray[] = $data;
            }

            return $resultarray;
        } else {
            return array();
        }
        $query = $this->db->get();
    }

    function getRank()
    {
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->getRankCount($limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_all_Rank_count();
        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_Rank_count()
    {
        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'RANK AND FILE');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getRankCount($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'RANK AND FILE');

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("name", "ASC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["name"] = $_query["name"];
                $data["total"] = $this->get_total_position($_query["name"]);
                $resultarray[] = $data;
            }

            return $resultarray;
        } else {
            return array();
        }
        $query = $this->db->get();
    }

    function countSupervisory()
    {
        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'SUPERVISORY');
        $this->db->order_by("name", "ASC");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["name"] = $_query["name"];
                $data["total"] = $this->get_total_position($_query["name"]);
                $resultarray[] = $data;
            }
            return $resultarray;
        } else {
            return array();
        }

    }

    function countManagerial()
    {

        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'MANAGERIAL');
        $this->db->order_by("name", "ASC");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["name"] = $_query["name"];
                $data["total"] = $this->get_total_position($_query["name"]);
                $resultarray[] = $data;
            }

            return $resultarray;
        } else {
            return array();
        }

    }

    function countSkilled()
    {

        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'SKILLED RANK AND FILE');
        $this->db->order_by("name", "ASC");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["name"] = $_query["name"];
                $data["total"] = $this->get_total_position($_query["name"]);
                $resultarray[] = $data;
            }

            return $resultarray;
        } else {
            return array();
        }

    }

    function countRank()
    {
        $this->db->from("gcchris.tblposition");
        $this->db->where('type', 'RANK AND FILE');
        $this->db->order_by("name", "ASC");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["name"] = $_query["name"];
                $data["total"] = $this->get_total_position($_query["name"]);
                $resultarray[] = $data;
            }

            return $resultarray;
        } else {
            return array();
        }

    }

    function get_names($input)
    {
        $this->db->from('dbhrd.document_body');
        $this->db->like('firstname', $input, "both");
        $this->db->or_like('lastname', $input, "both");
        $this->db->or_like('position', $input, "both");
        $this->db->or_like('course', $input, "both");
        $this->db->or_like('school', $input, "both");
        $this->db->or_like('tag1', $input, "both");
        $this->db->or_like('referral', $input, "both");
        $this->db->or_like('recruitment', $input, "both");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                //$row_set[] = htmlentities(stripslashes($row['firstname'].' '.$row['middlename'].' '.$row['lastname'])); //build an array
                $new_row['filename'] = htmlentities(stripslashes($row['filename']));

                $new_row['label'] = htmlentities(stripslashes($row['firstname'] . ' ' . $row['lastname']));
                $new_row['value'] = htmlentities(stripslashes($row['firstname'] . ' ' . $row['lastname']));

                $row_set[] = $new_row; //build an array
            }
            echo json_encode($row_set); //format the array into json data
        }
    }
    // function CrsFiles( $id=null){
    //     $resultset = array();
    //     $getUserData = $this->getUserData();

    //     $filename = "./uploads/module/crs/files/temp_files/temp".$getUserData['id'];

    //     if(!file_exists ( $filename )):
    //         //create temp dir
    //         mkdir("./uploads/module/crs/files/temp_files/temp".$getUserData['id']."/");
    //     endif;

    // 	$files = (isset($_FILES["files"]) && $_FILES["files"])? $_FILES["files"]: false;
    // 	$config = array();
    // 	$config['upload_path']          = "./uploads/module/crs/files/temp_files/temp{$getUserData['id']}";
    // 	$config['allowed_types']        = 'pdf';
    // 	$config['max_size']             = 1000000;
    // 	$this->upload->initialize($config);

    // 	if($files){
    // 		foreach($files["name"] as $key => $image){
    // 			$_FILES["files"]["name"] = $files["name"][$key];
    // 			$_FILES["files"]["type"] = $files["type"][$key];
    // 			$_FILES["files"]["tmp_name"] = $files["tmp_name"][$key];
    // 			$_FILES["files"]["error"] = $files["error"][$key];
    // 			$_FILES["files"]["size"] = $files["size"][$key];
    // 		}

    // 		if (!$this->upload->do_upload('files')){
    // 				$error = array('error' => $this->upload->display_errors());
    // 				$resultset["response"] = false;
    // 				$resultset["data"] = $error;
    // 		}else{
    //             $data = $this->upload->data();

    // 			$clientName = (isset($data["client_name"]) && $data["client_name"])? $data["client_name"]: "";
    // 			$fileName = (isset($data["file_name"]) && $data["file_name"])? $data["file_name"]: "";
    // 			$fileSize = (isset($data["file_size"]) && $data["file_size"])? $data["file_size"]: 0;
    // 			$deleteType = (isset($data["file_name"]) && $data["file_name"])? $data["file_name"]: "";


    // 			$resize = $this->resizeImage($fileName);
    // 			$_data = array();
    // 			$_data["resize"] = $resize;
    // 			$_data["name"] = $clientName;
    // 			$_data["uploaded"] = $fileName;
    // 			$_data["size"] = $fileSize;
    // 			$_data["deleteType"] = "DELETE";

    // 			 $_data["deleteUrl"] = site_url("asset_upload/asset_image_remove/{$fileName}");


    // 			$_data["thumbnailUrl"] = base_url("uploads/module/crs/files/temp_files/temp{$getUserData['id']}/thumbnail/{$fileName}");
    // 			$_data["url"] = base_url("uploads/module/crs/files/temp_files/temp{$getUserData['id']}/{$fileName}");

    // 			$resultset["files"][] = $_data;
    // 		}
    // 	}

    // 	return $resultset;
    // }

    public function searchEmployeeDocument($searchKey)
    {
        $select = "doc.id, UCASE(CONCAT(doc.firstname, ' ', doc.lastname)) applicant, doc.filename";

        $this->db->select($select);
        $this->db->from('dbhrd.document_body doc');
        $this->db->like('firstname', $searchKey, "both");
        $this->db->or_like('lastname', $searchKey, "both");
        $this->db->or_like('position', $searchKey, "both");
        $this->db->or_like('course', $searchKey, "both");
        $this->db->or_like('school', $searchKey, "both");
        $this->db->or_like('tag1', $searchKey, "both");
        $this->db->or_like('referral', $searchKey, "both");
        $this->db->or_like('recruitment', $searchKey, "both");
        $this->db->order_by("CONCAT(doc.firstname, ' ', doc.lastname) asc");
        $query = $this->db->get();
        $applicants = $query->result();

        foreach ($applicants as $applicant) {
            $resume_file_path = "uploads/files/hrd/resume_".$applicant->id."/" . $applicant->filename;
            $applicant->file_exist = file_exists(realpath($resume_file_path));
        }

        return $applicants;
    }

    public function newResume()
    {
        $resultSet = array();
        $post = $this->utilities->parseFormDataToObject($this->input->post());
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
        if($this->verify_applicant($post->firstname, $post->lastname, $post->suffix)){
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
                $this->core_layout->setEventLog("New resume information added: ".$post->firstname." ". $post->lastname."` with db id no. ".$insert_id,"insert", "success", "dbhrd", "user");
                $resultSet["data"] = $post->firstname . " " . $post->lastname;
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Resume insert failed","insert","error","dbhrd", "user");
                $resultSet["data"] = null;
            }
            $resultSet["upload_error"] = $upload_error;

            return $resultSet;
        }else{
            $resultSet["success"] = false;
            $resultSet["message"] = "User is already exist!";
            $resultSet["data"] = null;
            return $resultSet;
        }
    }

    private function verify_applicant($firstname, $lastname, $suffix){
        if($suffix != ""){
            $where = "firstname='$firstname' AND lastname='$lastname' AND suffix='$suffix'";
        }else{
            $where = "firstname='$firstname' AND lastname='$lastname'";
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
        $allTempFile = $this->db->get("dbhrd.file_attachment_temp")->result_array();
        foreach($allTempFile as $tempfiles){
            if(file_exists("uploads/files/hrd/temp/".$tempfiles['filename'])){
                if (!file_exists(realpath("uploads/files/hrd/resume_".$id."/"))) {
                    mkdir("uploads/files/hrd/resume_".$id."/", 0777, true);
                }
                $this->core_upload->moveUploadedFile($tempfiles['filename'], "uploads/files/hrd/temp", "uploads/files/hrd/resume_".$id);
                // rename("uploads/files/hrd/temp/".$tempfiles['filename'], "uploads/files/hrd/resume_".$id."/".$tempfiles['filename']);
            }
            $insertTemp = $this->db->insert("dbhrd.file_attachment", array("filename" => $tempfiles['filename'], "body_id" => $id, "file_size" => $tempfiles['file_size'], "created_by" => $this->core_layout->getUserId()));
            if($insertTemp){
                $this->db->where("id", $tempfiles['id']);
                $this->db->delete("dbhrd.file_attachment_temp");
            }
        }
        return true;
    }

    function updateResume()
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $tags = isset($post->tags) ? $post->tags : [];
        $schools = isset($post->schools) ? $post->schools : [];
        $courses = isset($post->courses) ? $post->courses : [];
        $positions = isset($post->positions) ? $post->positions : [];
        $post->applied_dt = date('Y-m-d', strtotime($post->applied_dt));
        $post->created_dt = date('Y-m-d H:i:s');
        $id = $post->id;
        $current_filename = $post->current_filename;
        if (empty($post->hired_dt) || $post->hired_dt == ""){
          unset($post->hired_dt);
      }
      if (empty($post->interview_dt) || $post->interview_dt == ""){
          unset($post->interview_dt);
      }
        unset($post->tags, $post->schools, $post->courses, $post->positions, $post->id, $post->current_filename);

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

        $post->blacklist_remarks = (isset($post->status) && $post->status == "blacklisted")? $post->blacklist_remarks: "";
        $post->blacklisted_dt = (isset($post->status) && $post->status == "blacklisted")? date("Y-m-d"): "0000-00-00";
        $post->modify_dt = date('Y-m-d H:i:s');
        $getUserData = $this->getUserData();
        $post->modify_by = $getUserData['id'];
        $currentResumeData = $this->getResumeDataById($id);
        $this->db->where("id", $id);
        $insert = $this->db->update("dbhrd.document_body", $post);
        
        if($insert){
            unset($post->created_dt);
            unset($post->modify_dt);
            $changes = $this->logChanges($currentResumeData,$post);
            if($post->status != "blacklisted"){
                $this->core_layout->setEventLog("Updated resume id: ".$id." changes: ".$changes,"update", "success", "dbhrd", "user");
                 return "crs/view_resume/?id=".$id;
            }else{
                return "crs/blacklisted_view/?id=".$id;
            }
            
        }else{
            return false;
        }
        
    }

    public function getCurrentResume($id=null){
        $resultset = array();
        $data = array();
        if($id){
            $qTemp = $this->db->get_where($this->resumeTable, array("id"=>$id));
            if($qTemp->num_rows() == 1){
                $resultset["response"] = true;
                $resultset["data"] = $qTemp->row();
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    public function getSelect2RequestPosition(){
        $resultset = array();
        $resultset["results"] = array();

        $get = $this->input->get();
        $this->db->select("a.position_id as id, CONCAT(d.name, ' | ', b.description) as text, 
        a.id as request_id, a.company_id, a.department_id, IFNULL(IF(a.type = '', IFNULL(d.type, 'NO APPLIED LEVEL'), a.type), IFNULL(d.type, 'NO APPLIED LEVEL')) as level, 
        b.description as company, c.description as department, a.current, a.people_no");

        $this->db->from("gcchris.tbapplication a");
        $this->db->join("gcchris.tblcompanies b", "b.id = a.company_id");
        $this->db->join("gcchris.tbldepartments c", "c.id = a.department_id");
        $this->db->join("gcchris.tblposition d", "d.id = a.position_id");
        $this->db->where("a.status", "Ongoing");
        $this->db->where("a.is_archived", 0);

        if(isset($get["term"]) && $get["term"]){
            $this->db->group_start();
            $this->db->like("d.name", $get["term"], "both");
            $this->db->or_like("b.description", $get["term"], "both");
            $this->db->group_end();
        }

        $query = $this->db->get();
        if($query->num_rows() > 0){
            foreach ($query->result() as $key => $value) {
                if(intval($value->current) < intval($value->people_no)){
                    unset($value->current, $value->people_no);
                    $resultset["results"][] = $value;
                }
            }
        }

        return $resultset;
    }

    public function hireResume(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $applicationId = $post["application_id"];
            $requestId = $post["request_id"];

            if($requestId){
                $longlat = (isset($post["long_lat_coordinates"]) && $post["long_lat_coordinates"]) ? $post["long_lat_coordinates"] : false;
                unset($post["request_id"], $post["application_id"], $post["long_lat_coordinates"]);
                $post["bday"] = date("Y-m-d", strtotime($post["bday"]));
                if ($longlat) {
                    $tempCoords = explode(",", $longlat);
                    if (count($tempCoords) == 2) {
                        $post["latitude"] = trim($tempCoords[0]);
                        $post["longitude"] = trim($tempCoords[1]);
                    } else {
                        $post["latitude"] = trim($tempCoords[0]);
                    }
                }
                $this->db->from("gcchris.tbapplication");
                $this->db->where("id", $requestId);
                $this->db->where("status", "Ongoing");
                $qTemp = $this->db->get();
                if($qTemp->num_rows() == 1){
                    $qRow = $qTemp->row();
                    if($qRow->current < $qRow->people_no && $qRow->current !== $qRow->people_no){
                        $addedRequest = false;
                        $tempCount = intval($qRow->current) + 1;
                        $updated = $this->db->update("gcchris.tbapplication", array("current"=>$tempCount), array("id"=>$qRow->id));
                        if($updated && $this->db->affected_rows() > 0){
                            $qTemp2 = $this->db->get_where("gcchris.tbapplication", array("id"=>$qRow->id, "status"=>"Ongoing"));
                            if($qTemp2->num_rows() == 1){
                                $qRowx = $qTemp2->row();
                                if(intval($qRowx->current) == intval($qRowx->people_no)){
                                    $this->db->update("gcchris.tbapplication", array("status"=>"Completed"), array("id"=>$qRowx->id));
                                }
                            }
                            $post["add_date"] = date("Y-m-d H:i:s");
                            $post["add_by"] = $this->core_layout->getCurrentEmployeeId();
                            $post["employee_status"] = "Active";
                            $inserted = $this->db->insert("gccmaster.tblemployees", $post);
                            if($inserted){
                                $this->db->update($this->resumeTable, array("status"=>"hired", "vacancy_status"=>"resolved"), array("id"=>$applicationId));
                                $addedRequest = true;
                            }
                        }

                        if($addedRequest){
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "New personnel has been added to the employee list.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Failed to add new personnel, error saving data!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Cannot add new personnel, error saving data!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Error saving data, current status is not ongoing!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error saving data, request not found!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Error saving data, no post data found!";
        }

        return $resultset;
    }

    function getHiredResume($id=null){
        $resultset = array();
        if($id){
            $qTemp = $this->db->get_where($this->resumeTable, 
                array("id"=>$id, "status"=>"hired", "vacancy_status"=>"resolved"));

            if($qTemp->num_rows() == 1){
                $tempRow = $qTemp->row();
                $tempCourses = $tempRow->course;
                $arrCourse = explode(",", $tempCourses);
                $arrCourse = array_filter($arrCourse);
                if(is_array($arrCourse) && count($arrCourse) > 0){
                    $tempRow->course = $arrCourse;
                    $tempRow->course_count = count($arrCourse);
                }

                $tempSchool = $tempRow->school;
                $arrSchool = explode(",", $tempSchool);
                $arrSchool = array_filter($arrSchool);
                if(is_array($arrSchool) && count($arrSchool) > 0){
                    $tempRow->school = $arrSchool;
                    $tempRow->school_count = count($arrSchool);
                }

                $tempPosition = $tempRow->position;
                $arrPosition = explode(",", $tempPosition);
                $arrPosition = array_filter($arrPosition);
                if(is_array($arrPosition) && count($arrPosition) > 0){
                    $tempRow->position = $arrPosition;
                    $tempRow->position_count = count($arrPosition);
                }

                $tempTag = $tempRow->tag1;
                $arrTag = explode(",", $tempTag);
                $arrTag = array_filter($arrTag);
                if(is_array($arrTag) && count($arrTag) > 0){
                    $tempRow->tag1 = $arrTag;
                    $tempRow->tag1_count = count($arrTag);
                }

                $resultset["response"] = true;
                $resultset["data"] = $tempRow;
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    public function addTempFile(){
        $post = $this->input->post();
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
            $this->db->set("filename", $filename);
            $this->db->set("file_size", $filesize);
            $this->db->insert("dbhrd.file_attachment_temp");
        }
        return $this->db->get("dbhrd.file_attachment_temp")->result_array();
    }

    public function editAddNewTempFile(){
        $post = $this->input->post();
        $body_id = $post['id'];
        $files = $_FILES['editfiles']['name'];
        $filepath = "uploads/files/hrd/temp/";

        if (!file_exists(realpath($filepath))) {
            mkdir($filepath, 0777, true);
        }

        $config['upload_path'] = $filepath;
        $config['allowed_types'] = 'gif|jpg|png|pdf';
        $config['max_size'] = 10000;
        $config['file_name'] = $this->changeStr($files);
        $this->upload->initialize($config);

        $upload = $this->upload->do_upload('editfiles');
        if (!$upload) {
            $upload_error = $this->upload->display_errors();
        } else {
            $upload_data = $this->upload->data();
            $filename = $upload_data['file_name'];
            $filesize = $upload_data['file_size'];

            $this->db->reset_query();
            $this->db->set("filename", $filename);
            $this->db->set("file_size", $filesize);
            $this->db->set("body_id", $body_id);
            $this->db->insert("dbhrd.file_attachment_temp");
        }
        $this->db->where("body_id", $body_id);
        return $this->db->get("dbhrd.file_attachment_temp")->result_array();
    }

    public function saveFieldTemplate($post)
    {
        $resultSet = array();
        $this->db->trans_begin();

        $this->db->insert('dbhrd.tblrpttemplate', array('fieldname' => $post->fieldname));
        $insertId = $this->db->insert_id();
        $this->db->reset_query();
        $template_body = array();

        $template_body = array_map(function ($item) use ($insertId) {
            $item['template_id'] = $insertId;
            return $item;
        }, $post->items);

        $this->db->insert_batch('dbhrd.tblrpttemplate_body', $template_body);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $resultSet['success'] = false;
            $resultSet['message'] = $this->db->error();
        } else {
            $this->db->trans_commit();
            $resultSet['success'] = true;
            $resultSet['message'] = "Template successfully saved.";
        }

        return $resultSet;
    }

    public function tempFileDel(){
        $post = $this->input->post();
        $id = $post['id'];
        $this->db->where("id", $id);
        $file = $this->db->get("dbhrd.file_attachment_temp");
    
        $theFile = $file->row_array();
        unlink("uploads/files/hrd/temp/".$theFile['filename']);
        $this->db->where("id", $id);
        $del = $this->db->delete("dbhrd.file_attachment_temp");
        if($del){
            return $this->db->get("dbhrd.file_attachment_temp")->result_array();
        }
    }

    public function del_all_temp(){
        $remove = $this->db->get("dbhrd.file_attachment_temp")->result_array();
        foreach($remove as $data){
            unlink("uploads/files/hrd/temp/".$data['filename']);
        }
        $del = $this->db->empty_table("dbhrd.file_attachment_temp");
        if($del){
            return true;
        }
    }

    public function getAllAttachFile(){
        $resultset = array();
        $post = $this->input->post();
        $id = $post['search']['id'];
        $rowCount = 0;
        $rowData = array();
        $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "DESC";
        
        $rowData = $this->get_all_attachment_files($id, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_all_attachment_count($id);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;
        return $resultset;
    }

    public function getFieldTempates()
    {
        $q = isset($_GET['q']) ? $_GET['q'] : '';

        $this->db->select('id, fieldname text');
        $this->db->like('fieldname', $q, 'both');
        $this->db->where('fieldname IS NOT NULL', FALSE, FALSE);
        $this->db->where('fieldname !=', '');
        return array('results' => $this->db->get('dbhrd.tblrpttemplate')->result());
    }

    public function crs_data()
    {
        $crs_array = array();
        $date = date("Y-m-d");
        $minDate = date('Y-m-d', strtotime("-7 days", strtotime($date)));
        $this->db->where("created_dt <", $date);
        $this->db->where("created_dt >", $minDate);
        $data = $this->db->get("dbhrd.document_body")->result_array();
        foreach($data as $crs){
            $crs_data = array();
            $crs_data['fullname'] = ($crs["suffix"] == "" ? $crs['firstname'] . " " .  $crs['lastname'] . " " . $crs['suffix'] : $crs['firstname'] . " " .  $crs['lastname']);
            $crs_data['recruitment'] = $crs['recruitment'];
            $crs_data['status'] = $crs['status'];
            $crs_data['contact_no'] = $crs['contact_no'];
            $crs_data['description'] = $crs['description'];
            $crs_data['tag'] = (count(explode(",", $crs['tag1'])) == 2 ? str_replace(",", "", $crs['tag1']) : $this->unset_array($crs['tag1']));
            $crs_data['school'] = (count(explode(",", $crs['school'])) == 2 ? str_replace(",", "", $crs['school']) : $this->unset_array($crs['school']));
            $crs_data['course'] = (count(explode(",", $crs['course'])) == 2 ? str_replace(",", "", $crs['course']) : $this->unset_array($crs['course']));
            $crs_data['position'] = (count(explode(",", $crs['position'])) == 2 ? str_replace(",", "", $crs['position']) : $this->unset_array($crs['position']));
            $crs_data['created_dt'] = date("Y-m-d", strtotime($crs['created_dt']));
            $crs_array[] = $crs_data;
        }
        return $crs_array;
    }

    private function unset_array($data){
        $tag = explode(",", $data);
        unset($tag[0]);
        return implode(", ", $tag);
    }

    private function get_all_attachment_files($id = 0, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $sql = "a.id, a.filename, a.file_size, a.created_by, a.created_at";
        $this->db->select($sql);
        $this->db->from("dbhrd.file_attachment a");
        $this->db->where('a.body_id', $id);

        if ((int)$limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.created_at", "DESC");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $data_arr = array();
                
                foreach($rs as $sql => $quer){
                    if($sql == "created_by"){
                        /*** created by tagged as user id do not change it ***/
                        $temp = $this->get_emp_firstname_by_userid($quer);
                        $data_arr[$sql] = $temp;
                        /*** created by tagged as user id do not change it ***/
                    }else{
                        $data_arr[$sql] = $quer;
                    }
                }
                $arrData[$key] = $data_arr;
            }
            return $arrData;
        } else {
            return array();
        }
    }

    private function get_all_attachment_count($id)
    {
        $this->db->from("dbhrd.file_attachment");
        $this->db->where('body_id', $id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getTemplateData()
    {
        $formData = $this->input->post('formData');
        $this->db->where('id', $formData['id']);
        return array('data' => $this->db->get('dbhrd.tblrpttemplate')->row());
    }

    public function getTemplateBody($template_id)
    {
        $this->db->where('template_id', $template_id);
        return $this->db->get('dbhrd.tblrpttemplate_body')->result();
    }

    public function previewFile_backup(){
        $post = $this->input->post();
        $id = $post['id'];
        $this->db->where("id", $id);
        $data = $this->db->get("dbhrd.file_attachment");
        $file = $data->row_array();
        if($file['filename'] != ""){
            if(file_exists("uploads/files/hrd/".$file['filename'])){
                return array("status" => 0, "data" => $file);
            }else if(file_exists("uploads/files/hrd/resume_".$file['body_id']."/".$file['filename'])){
                return array("status" => 1, "data" => $file);
            }else{
                return array("status" => 0, "data" => $file);
            }
        }else{
            return array("status" => 0, "data" => $file);
        }
    }

    public function previewFile(){
        $post = $this->input->post();
        $id = $post['id'];
        $this->db->where("body_id", $id);
        $data = $this->db->get("dbhrd.file_attachment");
        $file = $data->row_array();
        if($file['filename'] != ""){
            if(file_exists("uploads/files/hrd/".$file['filename'])){
                return array("status" => 0, "data" => $file);
            }else if(file_exists("uploads/files/hrd/resume_".$file['body_id']."/".$file['filename'])){
                return array("status" => 1, "data" => $file);
            }else{
                return array("status" => 0, "data" => $file);
            }
        }else{
            return array("status" => 0, "data" => $file);
        }
    }

    public function fileDelete(){
        $post = $this->input->post();
        $id = $post['id'];
        $this->db->select("filename, body_id");
        $this->db->where("id", $id);
        $get  = $this->db->get("dbhrd.file_attachment");
        $remove = $get->row_array();
        $filename = $remove['filename'];
        if($get->num_rows === 0){
            rmdir("uploads/files/hrd/resume_".$remove['body_id']);
        }
        if(unlink("uploads/files/hrd/resume_".$remove['body_id']."/".$filename)){
            $this->db->where("id", $id);
            $del = $this->db->delete("dbhrd.file_attachment");
            if($del){
                return true;
            }
        }else{
            return false;
        } 
    }

    public function addNewFileAttach(){
        $post = $this->input->post();
        $id = $post['id'];
        $this->db->where("body_id", $id);
        $num = $this->db->get("dbhrd.file_attachment_temp");
        $query = $num->row_array();
        if($num->num_rows() > 1){
            $all = $this->db->get("dbhrd.file_attachment_temp")->result_array();
            foreach($all as $allData){
                if(file_exists("uploads/files/hrd/temp/".$allData['filename'])){
                    // rename("uploads/files/hrd/temp/".$allData['filename'], "uploads/files/hrd/resume_".$id."/".$allData['filename']);
                    $this->core_upload->moveUploadedFile($allData['filename'], "uploads/files/hrd/temp", "uploads/files/hrd/resume_".$id);
                    /*** do not change user id ***/
                    $insertTemp = $this->db->insert("dbhrd.file_attachment", array("filename" => $allData['filename'], "body_id" => $id, "file_size" => $allData['file_size'], "created_by" => $this->core_layout->getUserId()));
                    /*** do not change user id ***/
                }
            }
        }else{
            if(file_exists("uploads/files/hrd/temp/".$query['filename'])){
                // rename("uploads/files/hrd/temp/".$query['filename'], "uploads/files/hrd/resume_".$id."/".$query['filename']);
                $this->core_upload->moveUploadedFile($query['filename'], "uploads/files/hrd/temp", "uploads/files/hrd/resume_".$id);
                $insertTemp = $this->db->insert("dbhrd.file_attachment", array("filename" => $query['filename'], "body_id" => $id, "file_size" => $query['file_size'], "created_by" => $this->core_layout->getUserId()));
            }
        }
        
        $this->db->where("body_id", $id);
        $dele = $this->db->delete("dbhrd.file_attachment_temp");
        if($dele){
            return true;
        }else{
            return false;
        }
    }

    public function getAllResumeData(){
        $array_data = [];
        $post = $this->input->post();
        $id = $post["id"];
        $this->db->select('a.*, a.id as body_id, b.id as info_id, b.*');
        $this->db->where("a.id", $id);
        $this->db->join('dbhrd.applicant_informations b', 'b.document_body_id = a.id', 'left');
        $data = $this->db->get("dbhrd.document_body a");
        $resume = $data->row_array();

        $array_data["name"] = $resume['firstname']." ".$resume['middlename']." ".$resume['lastname']." ".$resume["suffix"];
        $array_data["recruitment"] = $resume['recruitment'];
        if($resume['status'] == 'forinterview'){
            $status = "for interview";
        }else if($resume['status'] == 'doneinterview'){
            $status = "done interview";
        }else{
            $status = $resume['status'];
        }
        $array_data["status"] = $status;
        $array_data["contact_no"] = $resume['contact_no'];
        $array_data["school"] = $this->removeChar($resume['school']);
        $array_data["course"] = $this->removeChar($resume['course']);
        $array_data["position"] = $this->removeChar($resume['position']);
        $array_data["tags"] = $this->removeChar($resume['tag1']);
        $array_data["date"] = date_format(date_create($resume['created_dt']), "Y-m-d h:i A");
        $array_data["interview_dt"] = $resume['interview_dt'] == '0000-00-00 00:00:00' ? "N/A" : date_format(date_create($resume['interview_dt']), "Y-m-d h:i A");
        $array_data["description"] = $resume['description'];

        $array_data['firstname'] = $resume['firstname'];
        $array_data['lastname'] = $resume['lastname'];
        $array_data['middlename'] = $resume['middlename'];
        $array_data['suffix'] = $resume['suffix'];
        $array_data['current_address'] = $resume['curr_addr'];
        $array_data['permanent_address'] = $resume['prov_addr'];
        $array_data['citizenship'] = $resume['citizenship'];
        $array_data['religion'] = $resume['religion'];
        $array_data['languages'] = $resume['languages'];
        $array_data['gender'] = $resume['gender'];
        $array_data['civil_status'] = $resume['civil_stat'];
        $array_data['date_of_birth'] = date('M d, Y', strtotime($resume['bday']));
        $array_data['birthplace'] = $resume['birthplace'];
        $array_data['bloodtype'] = $resume['bloodtype'];
        $array_data['height'] = $resume['height'];
        $array_data['weight'] = $resume['weight'];
        $array_data['hair_color'] = $resume['hair_color'];
        $array_data['complexion'] = $resume['complexion'];
        $array_data['tel_no'] = $resume['telephone_no'];
        $array_data['email'] = $resume['email'];
        $array_data['tax_status'] = $resume['tax_status'];
        $array_data['tin_no'] = $resume['tin_no'];
        $array_data['phealth_no'] = $resume['phealth_no'];
        $array_data['pagibig_no'] = $resume['pagibig_no'];
        $array_data['sss_no'] = $resume['sss_no'];
        $array_data['filename'] = $resume['filename'];
        $array_data['body_id'] = $resume['body_id'];
        $arra_data['applicant_id'] = $id;

        if($resume['fat_deceased'] == 1){
            $deceased_fat = "m-badge m-badge--primary m-badge--wide";
        }else{
            $deceased_fat = "";
        }

        if($resume['mot_deceased'] == 1){
            $deceased_mot = "m-badge m-badge--primary m-badge--wide";
        }else{
            $deceased_mot = "";
        }

        if($resume['partners_deceased'] == 1){
            $deceased_partner = "m-badge m-badge--primary m-badge--wide";
        }else{
            $deceased_partner = "";
        }

        $array_data['fat_name'] = $resume['fat_name'];
        $array_data['fat_addr'] = $resume['fat_addr'];
        $array_data['fat_company'] = $resume['fat_company'];
        $array_data['fat_occupation'] = $resume['fat_occupation'];
        $array_data['fat_contact'] = $resume['fat_contact'];
        $array_data['fat_deceased'] = $deceased_fat;

        $array_data['mot_name'] = $resume['mot_name'];
        $array_data['mot_addr'] = $resume['mot_addr'];
        $array_data['mot_company'] = $resume['mot_company'];
        $array_data['mot_occupation'] = $resume['mot_occupation'];
        $array_data['mot_contact'] = $resume['mot_contact'];
        $array_data['mot_deceased'] = $deceased_mot;

        $array_data['partners_name'] = $resume['partners_name'];
        $array_data['partners_addr'] = $resume['partners_addr'];
        $array_data['partners_company'] = $resume['partners_company'];
        $array_data['partners_occupation'] = $resume['partners_occupation'];
        $array_data['partners_contact'] = $resume['partners_contact'];
        $array_data['partners_deceased'] = $deceased_partner;
        
        $array_data['emer_name'] = $resume['emer_name'];
        $array_data['emer_contact'] = $resume['emer_contact'];
        $array_data['emer_addr'] = $resume['emer_addr'];

        $array_data['dependents'] = $this->getDependents($resume['info_id']);
        $array_data['education'] = $this->getEducation($resume['info_id']);
        $array_data['license'] = $this->getLicenses($resume['info_id']);
        $array_data['drivers'] = $this->getDriversLicense($resume['info_id']);
        $array_data['work'] = $this->getWorkExperience($resume['info_id']);
        $array_data['award'] = $this->getAwards($resume['info_id']);
        $array_data['skill'] = $this->getSkill($resume['info_id']);
        $array_data['org'] = $this->getOrg($resume['info_id']);
        $array_data['training'] = $this->getTraining($resume['info_id']);
        $array_data['reference'] = $this->getReference($resume['info_id']);
        $array_data['med'] = $this->getMed($resume['info_id']);

        return $array_data;
    }

    public function returnBlacklist(){
        $post = $this->input->post();
        $id = $post['id'];

        $this->db->where("id", $id);
        $update = $this->db->update("dbhrd.document_body", array("status" => "pending"), array("id" => $id));
        if($update){
            return true;
        }else{
            return false;
        }
    }

    public function searchConfirmVal(){
        $post = $this->input->post();
        $search = $post['search_val'];

        if(!empty($search)){
            $this->db->where('status !=', 'blacklisted');
            $this->db->group_start();
            $this->db->like("CONCAT(firstname, lastname)", $search, "both");
            $this->db->or_like("CONCAT(firstname,' ', lastname)", $search, "both");
            $this->db->group_end();
            $count = $this->db->get("dbhrd.document_body")->num_rows();
            if($count == 0){
                $sql = "a.id, a.status, a.vacancy_status, CONCAT(a.firstname, a.lastname) AS name, a.school, a.course, a.position, a.tag1, a.recruitment, a.contact_no";
                $this->db->select($sql);
                $this->db->where('a.status', 'blacklisted');
                $this->db->group_start();
                $this->db->like("CONCAT(a.firstname, a.lastname)", $search, "both");
                $this->db->or_like("CONCAT(a.firstname,' ', a.lastname)", $search, "both");
                $this->db->group_end();
                $data = $this->db->get("dbhrd.document_body a");
                if($data->num_rows() != 0){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
           return false; 
        }
    }

    public function updateFieldTemplate($post)
    {
        $resultSet = array();
        $this->db->trans_begin();

        $id = $post->id;
        unset($post->id);

        $this->db->where('id', $id);
        $updateResult = $this->db->update('dbhrd.tblrpttemplate', array('fieldname' => $post->fieldname));
        $this->db->reset_query();
        $template_body = array();

        $template_body = array_map(function ($item) use ($id) {
            $item['template_id'] = $id;
            return $item;
        }, $post->items);

        $this->db->delete('dbhrd.tblrpttemplate_body', array('template_id' => $id));
        $this->db->insert_batch('dbhrd.tblrpttemplate_body', $template_body);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $resultSet['success'] = false;
            $resultSet['message'] = $this->db->error();
            $resultSet['data'] = null;
        } else {
            $this->db->trans_commit();
            $resultSet['success'] = true;
            $resultSet['message'] = "Template successfully updated.";
            $resultSet['data'] = array('id' => $id, 'text' => $post->fieldname, 'template_body' => $template_body);
        }

        return $resultSet;
    }

    function generateEmployeeReport($export)
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($post);
        $select = implode(", ", $post->fields);
        $order_field = $post->order_field;
        $order_by = $post->order_by;
        /* IF BY DEFAULT HIDE EMPLOYEE WITHOUT IDNO
         * $criteria = "(idno != '' OR idno is null)" . (isset($post->criteria) AND !empty($post->criteria) ? " AND " . $post->criteria : '');
         * */
        $criteria = $post->criteria;

        $this->db->select($select, FALSE);

        if (!empty($criteria)) {
            $this->db->where($criteria, NULL, FALSE);
        }

        if (intval($export) === 0) {
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }
        }

        if (!empty($order_field)) {
            $order = $order_field . " " . $order_by;
            $this->db->order_by($order);
        }
        
        $resultSet['data'] = $this->db->get("dbhrd.document_body")->result();
        /*$resultSet['select'] = $select;
        $resultSet['x'] = $this->db->last_query();*/
        $resultSet['x'] = $this->db->last_query();
        $resultSet["recordsTotal"] = $this->utilities->getTableCount("dbhrd.document_body", $criteria, null, null, true);
        $resultSet["recordsFiltered"] = $this->utilities->getTableCount("dbhrd.document_body", $criteria, null, null, true);
        return $resultSet;
    }

    function generateEmployeeReportForinterview($export)
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($post);
        $select = implode(", ", $post->fields);
        $order_field = $post->order_field;
        $order_by = $post->order_by;
        /* IF BY DEFAULT HIDE EMPLOYEE WITHOUT IDNO
         * $criteria = "(idno != '' OR idno is null)" . (isset($post->criteria) AND !empty($post->criteria) ? " AND " . $post->criteria : '');
         * */
        $criteria = $post->criteria;

        $this->db->select($select, FALSE);
        
        if (!empty($criteria)) {
            $this->db->where($criteria, NULL, FALSE);
            $this->db->where("status","forinterview");
        }
        

        if (intval($export) === 0) {
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }
        }

        if (!empty($order_field)) {
            $order = $order_field . " " . $order_by;
            $this->db->order_by($order);
        }
        
        $resultSet['data'] = $this->db->get("dbhrd.document_body")->result();
        /*$resultSet['select'] = $select;
        $resultSet['x'] = $this->db->last_query();*/
        $resultSet['x'] = $this->db->last_query();
        $resultSet["recordsTotal"] = $this->utilities->getTableCount("dbhrd.document_body", $criteria, null, null, true);
        $resultSet["recordsFiltered"] = $this->utilities->getTableCount("dbhrd.document_body", $criteria, null, null, true);
        return $resultSet;
    }

    function generateEmployeeReportBlacklisted($export)
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($post);
        $select = implode(", ", $post->fields);
        $order_field = $post->order_field;
        $order_by = $post->order_by;
        /* IF BY DEFAULT HIDE EMPLOYEE WITHOUT IDNO
         * $criteria = "(idno != '' OR idno is null)" . (isset($post->criteria) AND !empty($post->criteria) ? " AND " . $post->criteria : '');
         * */
        $criteria = $post->criteria;

        $this->db->select($select, FALSE);
        
        if (!empty($criteria)) {
            $this->db->where($criteria, NULL, FALSE);
            $this->db->where("status","blacklisted");
        }
        

        if (intval($export) === 0) {
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }
        }

        if (!empty($order_field)) {
            $order = $order_field . " " . $order_by;
            $this->db->order_by($order);
        }
        
        $resultSet['data'] = $this->db->get("dbhrd.document_body")->result();
        /*$resultSet['select'] = $select;
        $resultSet['x'] = $this->db->last_query();*/
        $resultSet['x'] = $this->db->last_query();
        $resultSet["recordsTotal"] = $this->utilities->getTableCount("dbhrd.document_body", $criteria, null, null, true);
        $resultSet["recordsFiltered"] = $this->utilities->getTableCount("dbhrd.document_body", $criteria, null, null, true);
        return $resultSet;
    }

    private function get_emp_firstname($emp_id){
        $this->db->where('id', $emp_id);
        $this->db->select('firstname, lastname, suffix');
        $data = $this->db->get("gccmaster.tblemployees");
        return $data->row_array();
    }

    private function get_emp_firstname_by_userid($userId){
        $sqlSelect = "UPPER(CONCAT(emp.lastname,
        CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
            UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
            emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
        END, ', ', emp.firstname, ' ',
        CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
        END)) as employee_name, emp.firstname, emp.lastname, emp.suffix";
        $this->db->select($sqlSelect);
        $this->db->from("gccmaster.tblusers user");
        $this->db->join("gccmaster.tblemployees emp", "emp.id = user.emp_id", "inner");
        $this->db->where('user.id', $userId);
        $data = $this->db->get();
        return $data->row_array();
    }

    private function changeStr($name){
        $new_name = str_replace(str_split(' ()/_0123456789'), "_", $name);
        $str_name = explode("_", $new_name);
        $filtered = array_filter($str_name);
        return implode("_", $filtered);
    }
    private function removeChar($data){
        return substr_replace($data, "", 0, 1);
    }

    public function getDependents($id){
        $result = array();
        $arrData = array();

        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tbldependents');
        $query = $this->db->get();

        $currentDate = date_create(date("Y-m-d"));

        if($query->num_rows() > 0){
            foreach($query->result() as $key => $row){
                $nextDate = date_create(date("Y-m-d", strtotime($row->dep_birthdate)));
                $intervalDate = date_diff($currentDate, $nextDate);
    
                $tempAge = 0;
                if($row->dep_birthdate == "0000-00-00"){
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
                        $tempAge = $tempAge > 1 ? $tempAge . " Years Old" : "1 Year Old";
                    }
                }
                $dep_birthdate = $row->dep_birthdate;
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
    
                $row->age = $tempAge;
                $row->birthdate = $dep_birthdate;
    
                $arrData[$key] = $row;
            }

            foreach ($arrData as $k => $v) {
                $result[] = $v;
            }
        }


        return $result;
    }

    public function getEducation($id){
        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tbleducation');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getLicenses($id){
        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tbllicenses');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getDriversLicense($id){
        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tbldriverlicense');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getWorkExperience($id){
        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tblworkexperience');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getAwards($id){
        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tblawards');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getSkill($id){
        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tblskills');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getOrg($id){
        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tblorganizations');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getTraining($id){
        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tbltrainings');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getReference($id){
        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tblreferences');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getMed($id){
        $this->db->where('applicant_id', $id);
        $this->db->from('dbhrd.tblmedrecs');
        $query = $this->db->get();

        return $query->result_array();
    }

    private function getResumeDataById($id) {
		$this->db->select("*");
		$this->db->from($this->resumeTable);
		$this->db->where('id', $id);
		$query = $this->db->get(); 
		return $query->row();
	}

    private function logChanges($currentData, $newData) {
        if (is_object($currentData)) {
            $currentData = get_object_vars($currentData);
        }
        if (is_object($newData)) {
            $newData = get_object_vars($newData);
        }
        $changes = array();
        $changesString = '';
        foreach ($currentData as $field => $value) {
            if (isset($newData[$field]) && $newData[$field]!== $value) {
                $changes[$field] = array(
                    'old' => $value,
                    'new' => $newData[$field]
                );
            }
        }
        foreach ($changes as $field => $change) {
            if ($field != 'company' && $field != 'department'){
                $changesString.= " Field: $field, from: ". $change['old']. ", to: ". $change['new']. "\n";
            }
        }
        return $changesString;
    }

    public function logExport(){
        $post = $this->input->post();
        $this->core_layout->setEventLog($post['type']." Exported","export", "success", "dbhrd", "user");
        return true;
    }

}
