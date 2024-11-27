
<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Reports_model extends CI_Model
    {
      function __construct()
      {
          parent::__construct();

      }
  public function getCrsReport(){
    $resultset = array();
    $post = $this->input->post();
    $order_val = array(array("column"=>"0", "dir"=>"desc"));
    $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
    $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
    $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
    $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
    $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
    $status = (isset($post["status"]) && $post["status"]) ? $post["status"] : false;
    $recruitment = (isset($post["recruitment"]) && $post["recruitment"]) ? $post["recruitment"] : false;
    $hiredDate = (isset($post["hired_date"]) && $post["hired_date"]) ? $post["hired_date"] : false;
    $interviewDate = (isset($post["interview_date"]) && $post["interview_date"]) ? $post["interview_date"] : false;
    $application =  (isset($post["application_method"]) && $post["application_method"]) ? $post["application_method"] : null;
    $rowCount = 0;
    $rowData = array();
    $rowData = $this->getCrsReportData($limit, $offset, $sortBy, $sortOrder,$search,$status,$recruitment,$hiredDate,$interviewDate,$application);
    $rowCount = $this->getCrsReportDataCount($search,$status,$recruitment,$hiredDate,$interviewDate,$application);
    $resultset["recordsTotal"] = $rowCount;
    $resultset["recordsFiltered"] = $rowCount;
    $resultset["data"] = $rowData;
    return $resultset;
  }

  private function getCrsReportData($limit, $offset, $sortBy, $sortOrder,$search,$status,$recruitment,$hiredDate,$interviewDate,$application){
    $data = array();
    $filterFields = array('id','firstname','middlename','lastname', 'school', 'course', 'position', 'tag1', 'recruitment', 'applied_dt'); 
    $this->db->select('id, school, course, position, tag1, recruitment, applied_dt, CONCAT(firstname, " ", lastname) AS name,hired_dt,interview_dt,');
    $this->db->from('dbhrd.document_body');

    if ($application) {
      if ($application == "Internal") {
          $this->db->where('created_by !=', '');
      } 
      else if($applicaton = "Online") {
          $this->db->where('created_by', '');
      }
  }

    if ($status){
      $this->db->where('status',$status);
    }
    if ($recruitment){
      $this->db->where('recruitment',$recruitment);
    }
    if($hiredDate){
      list($startDate, $endDate) = explode(' - ', $hiredDate);
      $startDateFormatted = DateTime::createFromFormat('Y/m/d', $startDate)->format('Y-m-d');
      $endDateFormatted = DateTime::createFromFormat('Y/m/d', $endDate)->format('Y-m-d');
      $this->db->where("hired_dt BETWEEN '{$startDateFormatted}' AND '{$endDateFormatted}'");
    }

    if($interviewDate){
      list($startDate, $endDate) = explode(' - ', $interviewDate);
      $startDateFormatted = DateTime::createFromFormat('Y/m/d', $startDate)->format('Y-m-d 00:00:00');
      $endDateFormatted = DateTime::createFromFormat('Y/m/d', $endDate)->format('Y-m-d 23:59:59');
      $this->db->where("interview_dt BETWEEN '{$startDateFormatted}' AND '{$endDateFormatted}'");
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
    $data = $query->result();
}
return $data;
  }

  private function getCrsReportDataCount($search,$status,$recruitment,$hiredDate,$interviewDate,$application){
    $filterFields = array('id','firstname','middlename','lastname', 'school', 'course', 'position', 'tag1', 'recruitment', 'applied_dt');
    $this->db->select('*');
    $this->db->from('dbhrd.document_body');

    if ($application) {
      if ($application == "Internal") {
          $this->db->where('created_by !=', '');
      } 
      else if($applicaton = "Online") {
          $this->db->where('created_by', '');
      }
    }

    if ($status){
      $this->db->where('status',$status);
    }
    if ($recruitment){
      $this->db->where('recruitment',$recruitment);
    }
    if($hiredDate){
      list($startDate, $endDate) = explode(' - ', $hiredDate);
      $startDateFormatted = DateTime::createFromFormat('Y/m/d', $startDate)->format('Y-m-d');
      $endDateFormatted = DateTime::createFromFormat('Y/m/d', $endDate)->format('Y-m-d');
      $this->db->where("hired_dt BETWEEN '{$startDateFormatted}' AND '{$endDateFormatted}'");
    }

    if($interviewDate){
      list($startDate, $endDate) = explode(' - ', $interviewDate);
      $startDateFormatted = DateTime::createFromFormat('Y/m/d', $startDate)->format('Y-m-d 00:00:00');
      $endDateFormatted = DateTime::createFromFormat('Y/m/d', $endDate)->format('Y-m-d 23:59:59');
      $this->db->where("interview_dt BETWEEN '{$startDateFormatted}' AND '{$endDateFormatted}'");
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

    }
