<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billing_m extends CI_Model {
    protected $eformsTable = "gcceforms";
    protected $months = array(
        array("index" => 1, "name" => "Jan"),
        array("index" => 2, "name" => "Feb"),
        array("index" => 3, "name" => "Mar"),
        array("index" => 4, "name" => "Apr"),
        array("index" => 5, "name" => "May"),
        array("index" => 6, "name" => "Jun"),
        array("index" => 7, "name" => "Jul"),
        array("index" => 8, "name" => "Aug"),
        array("index" => 9, "name" => "Sep"),
        array("index" => 10, "name" => "Oct"),
        array("index" => 11, "name" => "Nov"),
        array("index" => 12, "name" => "Dec")
    );

    public function __construct() {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        $this->load->model("core/upload_model", "file_upload");
        date_default_timezone_set("Asia/Manila");
    }

    private function getUserdata(){
       return $this->session->userdata('logged_in');
    }

    function createAccount(){
        $current_date = date("Y-m-d H:i:s");
        $post = $this->input->post();
        $post["created_by"] = $this->getUserdata()['emp_id'];
        $post["created_at"] = $current_date;
        $post["meterno_raw"] = str_replace(" ", "", str_replace("-", "", $post["meterno"]));
        $resultarray = array();

        if($this->checkCustomerNameIfExist($post['firstname'],$post['lastname'])){
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Customer name is already exist.";
        }elseif($this->checkmeterifexist($post['meterno'])){
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Meter No. exist for another account.";
        }elseif($this->checkaccountnoifexist($post['accountno'])){
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Account No. already in use.";
        }else{
            $customer_name = $post['firstname'].' '.($post['middlename'] ? $post['middlename'][0] : '').'. '.$post['lastname'];

            $query = $this->db->insert('hydra_billing.accounts', $post);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Account successfully saved.";
                $this->core_layout->setEventLog("Accounts - Created Customer ".$customer_name,"insert", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error saving account.";
                $this->core_layout->setEventLog("Accounts - Created Customer ".$customer_name,"insert", "error", "hydra_billing", "user");
            }
        }
        
        return $resultarray;
    }

    function checkCustomerNameIfExist($name, $lastname){
        $this->db->where('firstname',$name);
        $this->db->where('lastname',$lastname);
        $query = $this->db->get('hydra_billing.accounts');
        if ($query->num_rows() > 0){
            return true;
        } else{
            return false;
        }
    }

    function checkmeterifexist($meter){
        $this->db->where('meterno',$meter);
        $query = $this->db->get('hydra_billing.accounts');
        if ($query->num_rows() > 0){
            return true;
        } else{
            return false;
        }
    }
    
    function checkaccountnoifexist($accountno){
        $this->db->where('accountno',$accountno);
        $query = $this->db->get('hydra_billing.accounts');
        if ($query->num_rows() > 0){
            return true;
        } else{
            return false;
        }
    }

    function getDatatableRequest(){
        $resultarray = array();
        $post = $this->input->post();

        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

        $filterFields = array("b.name","a.accountno", "a.meterno_raw", "a.firstname", "a.lastname",
                "a.lot", "a.block", "a.status","CONCAT('active','inactive')","a.middlename");

        $this->db->select("a.*, b.name as subdivision");
        $this->db->from("hydra_billing.accounts a");
        $this->db->join("hydra_billing.subdivision b", "b.id = a.subdivision_id", "LEFT");
        $this->db->where('a.is_archive',"0");

        if($query_builder){
            $this->db->where($query_builder);
        }

        if($search != ""){
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
        $this->db->order_by('created_at', 'DESC');

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();

                if($_query['is_archive'] == '1'){
                    $status = "Archive";
                } else if($_query['status'] == 0){
                    $status = "Inactive";
                } else {
                    $status = "Active";
                }

                $data["name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["id"] = $_query["id"];
                $data["accountno"] = $_query["accountno"];
                $data["meterno"] = $_query["meterno_raw"];
                $data["block"] = $_query["block"];
                $data["model"] = $_query["model"];
                $data["street"] = $_query["street"];
                $data["is_disconnected"] = $_query["is_disconnected"];
                $data["subdivision"] = $_query["subdivision"];
                $data["lot"] = $_query["lot"];
                $data["status"] = $status;
                $data["previous_reading"] = $this->getRecentReading($_query["id"], $_query["meterno_raw"]);
                $resultarray[] = $data;
            }
        }

        $total = $this->getAccountCount($search, $query_builder);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getDatatableRequestDisconnection(){
      $resultarray = array();
      $finalResultArray = array();
      $post = $this->input->post();

      $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
      $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
      $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
      $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

      $this->db->select("*");
      $this->db->from("hydra_billing.accounts");
      $this->db->where('is_disconnected', 0);
      $this->db->where('is_archive',"0");
      $query = $this->db->get();
      if($query->num_rows() > 0){
          foreach($query->result_array() as $_query){
              $balance = $this->getBalanceForDisconnection($_query['id'])['lastbill']['total_balance'];
              if($balance > 0){
                $resultarray[] = $_query["id"];
              }
          }
      }

      $filterFields = array("b.name","a.accountno", "a.meterno_raw", "a.firstname", "a.lastname",
      "a.lot", "a.block", "a.status","CONCAT('active','inactive')","a.middlename");

      $this->db->select("a.*, b.name as subdivision");
      $this->db->from("hydra_billing.accounts a");
      $this->db->join("hydra_billing.subdivision b", "b.id = a.subdivision_id", "LEFT");
      $this->db->where('a.is_disconnected', 0);
      $this->db->where_in("a.id", $resultarray);
      if($search != ""){
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
      $final_query = $this->db->get();
      if($final_query->num_rows() > 0){
          foreach($final_query->result_array() as $_final_query){
              $final_data = array();
              if($_final_query['is_archive'] == '1'){
                $status = "Archive";
              } else if($_final_query['status'] == 0){
                  $status = "Inactive";
              } else {
                  $status = "Active";
              }
              $final_balance = $this->getBalanceForDisconnection($_final_query['id'])['lastbill']['total_balance'];
              $final_data["name"] = $this->nameFormat($_final_query["firstname"], $_final_query["middlename"], $_final_query["lastname"]);
              $final_data["id"] = $_final_query["id"];
              $final_data["accountno"] = $_final_query["accountno"];
              $final_data["meterno"] = $_final_query["meterno_raw"];
              $final_data["block"] = $_final_query["block"];
              $final_data["model"] = $_final_query["model"];
              $final_data["street"] = $_final_query["street"];
              $final_data["is_disconnected"] = $_final_query["is_disconnected"];
              $final_data["subdivision"] = $_final_query["subdivision"];
              $final_data["lot"] = $_final_query["lot"];
              $final_data["status"] = $status;
              $final_data['balance'] = '<span class="text-danger m--font-boldest">₱ '.number_format($final_balance,2).'</span>';

              $finalResultArray[] = $final_data;
          }
      }

      $total = $this->getAccountForDisconnectionCount($search, $query_builder);
      return array("data"=>$finalResultArray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getAccountForDisconnectionCount($search, $query_builder = NULL){
      $resultarray = array();
      $filterFields = array("b.name","a.accountno", "a.meterno_raw", "a.firstname", "a.lastname", "a.lot", "a.block", "a.status","CONCAT('active','inactive')","a.middlename");
        $this->db->select("a.*, b.name as subdivision");
        $this->db->from("hydra_billing.accounts a");
        $this->db->join("hydra_billing.subdivision b", "b.id = a.subdivision_id", "LEFT");
        $this->db->where('a.is_disconnected', 0);
        $this->db->where('a.is_archive',"0");
        if($query_builder){
            $this->db->where($query_builder);
        }

        if($search != ""){
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
        foreach($query->result_array() as $_query){
          $data = array();
          $balance = $this->getBalanceForDisconnection($_query['id'])['lastbill']['total_balance'];
          $data['balance'] = '<span class="text-success m--font-boldest">₱ '.number_format($balance,2).'</span>';
          if($balance > 0){
            $resultarray[] = $data;
          }
        }
        return count($resultarray);
    }

    function getDatatableRequestReconnection(){
      $resultarray = array();
      $finalResultArray = array();
      $post = $this->input->post();

      $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
      $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
      $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
      $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

      $this->db->select("*");
      $this->db->from("hydra_billing.accounts");
      $this->db->where('is_disconnected', 1);
      $this->db->where('is_archive',"0");
      $query = $this->db->get();
      if($query->num_rows() > 0){
          foreach($query->result_array() as $_query){
              $balance = $this->getBalanceForDisconnection($_query['id'])['lastbill']['total_balance'];
              if($balance <= 0){
                $resultarray[] = $_query["id"];
              }
          }
      }

      $filterFields = array("b.name","a.accountno", "a.meterno_raw", "a.firstname", "a.lastname",
      "a.lot", "a.block", "a.status","CONCAT('active','inactive')","a.middlename");

      $this->db->select("a.*, b.name as subdivision");
      $this->db->from("hydra_billing.accounts a");
      $this->db->join("hydra_billing.subdivision b", "b.id = a.subdivision_id", "LEFT");
      $this->db->where('a.is_disconnected', 1);
      $this->db->where_in("a.id", $resultarray);
      if($search != ""){
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
      $final_query = $this->db->get();
      if($final_query->num_rows() > 0){
          foreach($final_query->result_array() as $_final_query){
              $final_data = array();
              if($_final_query['is_archive'] == '1'){
                $status = "Archive";
              } else if($_final_query['status'] == 0){
                  $status = "Inactive";
              } else {
                  $status = "Active";
              }
              $final_balance = $this->getBalanceForDisconnection($_final_query['id'])['lastbill']['total_balance'];
              $final_data["name"] = $this->nameFormat($_final_query["firstname"], $_final_query["middlename"], $_final_query["lastname"]);
              $final_data["id"] = $_final_query["id"];
              $final_data["accountno"] = $_final_query["accountno"];
              $final_data["meterno"] = $_final_query["meterno_raw"];
              $final_data["block"] = $_final_query["block"];
              $final_data["model"] = $_final_query["model"];
              $final_data["street"] = $_final_query["street"];
              $final_data["is_disconnected"] = $_final_query["is_disconnected"];
              $final_data["subdivision"] = $_final_query["subdivision"];
              $final_data["lot"] = $_final_query["lot"];
              $final_data["status"] = $status;
              $final_data['balance'] = '<span class="text-danger m--font-boldest">₱ '.number_format($final_balance,2).'</span>';

              $finalResultArray[] = $final_data;
          }
      }

      $total = $this->getAccountForReconnectionCount($search, $query_builder);
      return array("data"=>$finalResultArray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getAccountForReconnectionCount($search, $query_builder = NULL){
      $resultarray = array();
      $filterFields = array("b.name","a.accountno", "a.meterno_raw", "a.firstname", "a.lastname", "a.lot", "a.block", "a.status","CONCAT('active','inactive')","a.middlename");
        $this->db->select("a.*, b.name as subdivision");
        $this->db->from("hydra_billing.accounts a");
        $this->db->join("hydra_billing.subdivision b", "b.id = a.subdivision_id", "LEFT");
        $this->db->where('a.is_disconnected', 1);
        $this->db->where('a.is_archive',"0");
        if($query_builder){
            $this->db->where($query_builder);
        }

        if($search != ""){
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
        foreach($query->result_array() as $_query){
          $data = array();
          $balance = $this->getBalanceForDisconnection($_query['id'])['lastbill']['total_balance'];
          $data['balance'] = '<span class="text-success m--font-boldest">₱ '.number_format($balance,2).'</span>';
          if($balance == 0){
            $resultarray[] = $data;
          }
        }
        return count($resultarray);
    }

    function nameFormat($firstname, $middlename, $lastname){
        if($middlename){
            if(strtolower($middlename) == 'n/a'){
                $middle = '';
            } else {
                $middle = $middlename[0].'.';
            }
        } else {
            $middle = '';
        }
        return $firstname." ".$middle." ".$lastname;
    }

    function getAccountCount($search, $query_builder = NULL){
        $filterFields = array("b.name","a.accountno", "a.meterno_raw", "a.firstname", "a.lastname", "a.lot", "a.block", "a.status","CONCAT('active','inactive')","a.middlename");
        $this->db->select("a.*, b.name as subdivision");
        $this->db->from("hydra_billing.accounts a");
        $this->db->join("hydra_billing.subdivision b", "b.id = a.subdivision_id", "LEFT");

        if($query_builder){
            $this->db->where($query_builder);
        }

        if($search != ""){
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

    function getAccountDetails($id = NULL){

        if(isset($id) && $id):
            $this->db->where("a.id",$id);
            $this->db->select("a.*, s.name as subdivision, m.account_id, m.initial_reading");
            $this->db->from("hydra_billing.accounts a");
            $this->db->join("hydra_billing.subdivision s", "a.subdivision_id = s.id", "LEFT");
            $this->db->join("hydra_billing.tbl_meter_history m", "a.id = m.account_id", "LEFT");
            $query = $this->db->get();
        else:
            $this->db->where("a.id",$this->input->post("account_id"));
            $this->db->select("a.*, s.name as subdivision, m.account_id, m.initial_reading");
            $this->db->from("hydra_billing.accounts a");
            $this->db->join("hydra_billing.tbl_meter_history m", "a.id = m.account_id", "LEFT");
            $query = $this->db->get();
        endif;

        $previous_reading = $this->getRecentReading($query->row_array()["id"], $query->row_array()["meterno_raw"]);

        $ir = $this->checkMeterReplace($query->row_array()["id"], $query->row_array()["meterno_raw"]);
        $rmu = $this->resetMeterUsage($query->row_array()["id"], $query->row_array()["meterno_raw"]);

        if($rmu === 1) {
            $current_usage = $previous_reading ? number_format($previous_reading, 2, '.', '') : "0.00";
        } else {
            $current_usage = 0;
        }

        return array(
            "data" => $query->row(), 
            "previous_reading" => $previous_reading ? number_format($previous_reading, 2, '.', '') : "0.00",
            "initial_reading" => $ir,
            "current_usage" => $current_usage,
        );
    }

    function resetMeterUsage($account_id, $meterno) {
        $this->db->select('initial_reading');
        $this->db->from('hydra_billing.tbl_meter_history');
        $this->db->where('account_id', $account_id);
        $this->db->where('old_meterno', $meterno);
        $this->db->where('initial_reading', 0);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row_array();
        return !is_null($result) && is_array($result) && isset($result['initial_reading']) ? $result['initial_reading'] : 1;
    }

    function updateAccount(){
        $current_date = date("Y-m-d H:i:s");
        $resultarray = array();
        $post = $this->input->post();
        $id = $post["id"];
        $post["updated_at"] = $current_date;
        unset($post["id"]);

        // if(!isset($post["status"])){
        //     $post["status"] = "0";
        // }else{
        //     $post["status"] = "1";
        // }

        $customer_name = $this->nameFormat($post["firstname"], $post["middlename"], $post["lastname"]);
        $notification = "Update Customer details ";

        if($this->getDisconnectionStatus($id) != $post["is_disconnected"]){
            if($post["is_disconnected"] == 1){
                $post["disconnect_date"] = $current_date;
                $notification .= "Water Disconnected, ";
            } else {
                $post["disconnect_date"] = '';
                $notification .= "Water Connected, ";
            }
        }

        if($this->getCustomerStatus($id) != $post["status"]){
            if($post["status"] == 1){
                $notification .= "Status Active ";
            } else {
                $notification .= "Status Inactive ";
            }
        }

        $this->db->where("id",$id);
        $query = $this->db->update("hydra_billing.accounts",$post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Account successfully updated.";

            $this->core_layout->setEventLog("Accounts - ".$notification.$customer_name,"update", "success", "hydra_billing", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error updating account.";

            $this->core_layout->setEventLog("Accounts - Error updating account ".$customer_name,"update", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function getDisconnectionStatus($id){
        $this->db->select("is_disconnected");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("id",$id);
        return $this->db->get()->row_array()['is_disconnected'];
    }

    function getCustomerStatus($id){
        $this->db->select("status");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("id",$id);
        return $this->db->get()->row_array()['status'];
    }

    function updateAccountStatus(){
        $resultarray = array();
        $post = $this->input->post();

        $this->db->where("id",$post["id"]);
        $query = $this->db->update("hydra_billing.accounts",array("updated_by"=>$this->getUserdata()['emp_id'],"status"=>$post["status"]));
        
        if($query){
            $resultarray["status"] = TRUE;
            if($post["status"]==1){
                $resultarray["msg"] = "Account successfully activated.";
            }else{
                $resultarray["msg"] = "Account successfully deactivated.";
            }
            
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error updating account.";
        }

        return $resultarray;
    }

    function archiveAccount(){
        $resultarray = array();
        $post = $this->input->post();
        $id = $post["id"];
        unset($post["id"]);

        $post["archived_by"] = $this->getUserdata()['emp_id'];
        $post["is_archive"] = "1";

        $this->db->where("id",$id);
        $query = $this->db->update("hydra_billing.accounts",$post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Account successfully archived.";

            $this->core_layout->setEventLog("Accounts - Archived customer ".$this->getCustomerName($id),"archived", "success", "hydra_billing", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error archiving account.";

            $this->core_layout->setEventLog("Accounts - Error Archiving customer account ".$this->getCustomerName($id),"archived", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function getCustomerName($id){
        $this->db->select("firstname,lastname");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("id",$id);
        $this->db->limit("1");
        $query = $this->db->get()->row_array();
        return $query['firstname'].' '.$query['lastname'];
    }

    function checkPaymentsHasReading($account_id){
        $this->db->select("id");
        $this->db->from("hydra_billing.readings");
        $this->db->where("account_id",$account_id);
        $this->db->where("is_billed",'0');
        $this->db->where("is_archived", "0");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getAccountSelectBilling(){
        $get = $this->input->get();
        $resultarray = array();

        if (isset($get['q'])) {
            $query = $this->db->query("SELECT id, firstname, lastname, middlename, accountno, meterno_raw 
            FROM hydra_billing.accounts
            WHERE status='1' AND is_archive='0' AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%' OR meterno LIKE '%{$get['q']}%' OR accountno LIKE '%{$get['q']}%') ORDER BY accountno ASC");
        }else{
            $query = $this->db->query("SELECT id, firstname, lastname, middlename, accountno, meterno_raw 
            FROM hydra_billing.accounts
            WHERE status='1' AND is_archive='0' ORDER BY accountno ASC");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();

                if($this->checkPaymentsHasReading($_query["id"]) > 0){
                    $data["id"] = $_query["id"];
                    $data["meterno_raw"] = $_query["meterno_raw"];
                    $data["text"] = $_query["accountno"] ." | ". $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                    if($this->computeBalanceLastBill($_query['id'], null) == 0 || $this->computeBalanceLastBill($_query['id'], null) == null){
                      $resultarray[] = $data;
                    }
                }
            }
        }

        return array("results" => $resultarray);
    }

    function checkPaymentsHasBill($account_id){
        $this->db->select("id");
        $this->db->from("hydra_billing.bills");
        $this->db->where("account_id",$account_id);
        $this->db->where("is_paid",'0');
        $this->db->where("status",'1');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getAccountSelectPayments(){
        $get = $this->input->get();
        $resultarray = array();

        if (isset($get['q'])) {
            $query = $this->db->query("SELECT id, CONCAT(accountno, ' | ', firstname, ' ', middlename, ' ', lastname, ' | Meterno: ', meterno) as customer
            FROM hydra_billing.accounts
            WHERE status='1' AND is_archive='0' AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%' OR meterno LIKE '%{$get['q']}%' OR accountno LIKE '%{$get['q']}%') ORDER BY accountno ASC");
        }
        // else{
        //     $query = $this->db->query("SELECT id, CONCAT(accountno, ' | ', firstname, ' ', middlename, ' ', lastname, ' | Meterno: ', meterno) as customer
        //     FROM hydra_billing.accounts
        //     WHERE status='1' AND is_archive='0' ORDER BY accountno ASC LIMIT 10");
        // }

        // ===========================================
        // OLD
        // START
        // if (isset($get['q'])) {
        //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, accountno, meterno 
        //     FROM hydra_billing.accounts
        //     WHERE status='1' AND is_archive='0' AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%' OR meterno LIKE '%{$get['q']}%' OR accountno LIKE '%{$get['q']}%') ORDER BY accountno ASC");
        // }else{
        //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, accountno, meterno 
        //     FROM hydra_billing.accounts
        //     WHERE status='1' AND is_archive='0' ORDER BY accountno ASC");
        // }
        // END
        // ===========================================

        $sql = $this->db->last_query();

        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $_query) {
                $data = array();

                if($this->checkPaymentsHasBill($_query["id"]) > 0){
                    $data["id"] = $_query["id"];
                    // $data["text"] = $_query["accountno"] ." | ". $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]) . " | Meterno: " . $_query["meterno"];
                    $data["text"] = $_query["customer"];
                    $resultarray[] = $data;
                }
            }
        }

        return array("results" => $resultarray);
    }

    function checkReadingDuplicate($account_id, $meterno_raw){
        $current_month = date("m");
        $current_year = date("Y");
        $this->db->select("id");
        $this->db->from("hydra_billing.readings");
        $this->db->where("account_id",$account_id);
        $this->db->where("MONTH(reading_date)",$current_month);
        $this->db->where("YEAR(reading_date)",$current_year);
        $this->db->where("meterno",$meterno_raw);
        $this->db->where("is_archived", "0");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getAccountSelectReading(){
        $get = $this->input->get();
        $resultarray = array();

        if (isset($get['q'])) {
            $query = $this->db->query("SELECT id, meterno, meterno_raw, CONCAT(accountno, ' | ', firstname, ' ', middlename, ' ', lastname) as customer
            FROM hydra_billing.accounts
            WHERE status='1' AND is_archive='0' AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%' OR meterno LIKE '%{$get['q']}%' OR accountno LIKE '%{$get['q']}%') ORDER BY accountno ASC");
        } 
        // else{
        //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, accountno, meterno, meterno_raw
        //     FROM hydra_billing.accounts
        //     WHERE status='1' AND is_archive='0' ORDER BY accountno ASC");
        // }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();

                if($this->checkReadingDuplicate($_query["id"], $_query["meterno_raw"]) == 0){
                    $data["id"] = $_query["id"];
                    // $data["text"] = $_query["accountno"] ." | ". $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                    $data["text"] = $_query["customer"];
                    $resultarray[] = $data;
                }
            }
        }

        return array("results" => $resultarray);
    }

    function checkMeterReplace($account_id, $meterno) {
        $this->db->select('initial_reading');
        $this->db->from('hydra_billing.tbl_meter_history');
        $this->db->where('account_id', $account_id);
        $this->db->where('new_meterno', $meterno);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row_array();
        return !is_null($result) && is_array($result) && isset($result['initial_reading']) ? $result['initial_reading'] : 1;
    }

    function updateInitialReading($account_id, $meterno) {
        $data = array(
            'initial_reading' => 1,
            'initial_reading_by' => $this->getUserdata()['emp_id'],
            'initial_reading_at' => date('Y-m-d H:i:s'),
        );

        $this->db->set($data);
        $this->db->where('account_id', $account_id);
        $this->db->where('new_meterno', $meterno); 
        $query = $this->db->update('hydra_billing.tbl_meter_history');
    }

    function createReading(){
        $current_date = date("Y-m-d H:i:s");
        $post = $this->input->post();
        $code = 'MRR';
        $ref_no = $this->series($current_date, 'hydra_billing.readings', $code);
        $ref_series = explode("-",$ref_no)[2];
        $ref_month = explode("-",$ref_no)[1];
        $ref_yr = explode($code,explode("-",$ref_no)[0])[1];

        $post["reading"] = preg_replace('/[^0-9a-zA-Z.]/', '', $post['reading']);
        $post["pic"] = serialize($post['pic']);
        $post["created_by"] = $this->getUserdata()['emp_id'];
        $post["created_at"] = $current_date;
        $post["ref_no"] = $ref_no;
        $post["ref_series"] = $ref_series;
        $post["ref_yr"] = $ref_yr;
        $post["ref_month"] = $ref_month;
        $post["status"] = "Unbilled";
        $resultarray = array();

        /**
         * Temporary
         * 
         * Message: Allow user to create reading where input reading is less than the last reading
         * 
         * Start
         */

        // Check if the account was about to read is if the meter was replaced
        $m = $this->checkMeterReplace($post["account_id"], $post["meterno"]);

        if($m === "0") {
            $query = $this->db->insert('hydra_billing.readings', $post);

            if ($query) {
                $this->updateInitialReading($post["account_id"], $post["meterno"]);

                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Reading successfully saved.";
                $this->core_layout->setEventLog("Reading - Created new readings ".$post["ref_no"],"insert", "success", "hydra_billing", "user");
            } else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error saving reading.";
                $this->core_layout->setEventLog("Reading - Created new readings ".$post["ref_no"],"insert", "error", "hydra_billing", "user");
            }
        } else {
            $query = $this->db->insert('hydra_billing.readings', $post);

            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Reading successfully saved.";
                $this->core_layout->setEventLog("Reading - Created new readings ".$post["ref_no"],"insert", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error saving reading.";
                $this->core_layout->setEventLog("Reading - Created new readings ".$post["ref_no"],"insert", "error", "hydra_billing", "user");
            }
        }
        // END
        // ================================================================================================================
        

        /**
         * ================================================================================================================
         * 
         * Original Code
         * 
         * Message: Temporarily remove recent reading checking
         * 
         * Start
         */

        // if($this->getRecentReading($post["account_id"], $post["meterno"]) > $post["reading"]){

        //     // Check if the account was about to read is if the meter was replaced
        //     $m = $this->checkMeterReplace($post["account_id"], $post["meterno"]);

        //     if ($m === "0") {
        //         $query = $this->db->insert('hydra_billing.readings', $post);
        //         if($query){
        //             $this->updateInitialReading($post["account_id"], $post["meterno"]);

        //             $resultarray["status"] = TRUE;
        //             $resultarray["msg"] = "Reading successfully saved.";
        //             $this->core_layout->setEventLog("Reading - Created new readings ".$post["ref_no"],"insert", "success", "hydra_billing", "user");
        //         }else{
        //             $resultarray["status"] = FALSE;
        //             $resultarray["msg"] = "Error saving reading.";
        //             $this->core_layout->setEventLog("Reading - Created new readings ".$post["ref_no"],"insert", "error", "hydra_billing", "user");
        //         }
        //     } else {
        //         $resultarray["status"] = FALSE;
        //         $resultarray["msg"] = "Current reading must equal or exceed to previous reading.";
        //     }
        // } else {
        //     $query = $this->db->insert('hydra_billing.readings', $post);
        //     if($query){
        //         $resultarray["status"] = TRUE;
        //         $resultarray["msg"] = "Reading successfully saved.";
        //         $this->core_layout->setEventLog("Reading - Created new readings ".$post["ref_no"],"insert", "success", "hydra_billing", "user");
        //     }else{
        //         $resultarray["status"] = FALSE;
        //         $resultarray["msg"] = "Error saving reading.";
        //         $this->core_layout->setEventLog("Reading - Created new readings ".$post["ref_no"],"insert", "error", "hydra_billing", "user");
        //     }
        // }

        // End
        // ================================================================================================================
    
        return $resultarray;
    }

    function getRecentReading($account_id, $meterno){
        $this->db->select("reading");
        $this->db->from("hydra_billing.readings");
        $this->db->where("account_id",$account_id);
        $this->db->where("meterno",$meterno);
        $this->db->where("is_archived", 0);
        $this->db->order_by('reading_date', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        // $reading = $query->row_array()["reading"]; // --> original
        $reading = !is_null($query->row_array()) ? $query->row_array()["reading"] : 0.00;
        return $reading;
    }

    function series($current_date, $query, $code){
        $year = substr($current_date, 2, 2);
        $month = substr($current_date, 5, 2);

        $this->db->select('ref_series');
        $this->db->from($query);
        $this->db->where('ref_yr', $year);
        $this->db->where('ref_month', $month);
        $this->db->order_by('ref_series', 'asc');
        $query = $this->db->get();
        $list = $query->result();

        $series = '';
        if (sizeof($list) > 0) {
            foreach ($list as $arr) {
                $x = $arr->ref_series;
            }
            $series = intval($x) + 1;
            if (strlen($series) == 1) {
                $series = '000' . $series;
            } else if (strlen($series) == 2) {
                $series = '00' . $series;
            } else if (strlen($series) == 3) {
                $series = '0' . $series;
            } else {
                $series = $series;
            }
        } else {
            $series = '0001';
        }

        return "{$code}{$year}-{$month}-{$series}";
    }

    function payment_ref_series($query, $code){
        $this->db->select('acknowledgement_receipt');
        $this->db->from($query);
        $this->db->where("acknowledgement_receipt !=", "");
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        $list = $query->row();

        $series = '';
        if (count((array)$list) > 0) {
            $temp_x = explode("-", $list->acknowledgement_receipt);
            $series = intval($temp_x[1]) + 1;
            if (strlen($series) == 1) {
                $series = '00' . $series;
            } else if (strlen($series) == 2) {
                $series = '0' . $series;
            } else {
                $series = $series;
            }
        } else {
            $series = '001';
        }
        return "{$code}-{$series}";
    }

    function generateReadingReferenceNo(){
        $qData = $this->db->get("hydra_billing.readings");
        $tempCount = $qData->num_rows();
        $tempCount = ($tempCount == 0)? 1: intval($tempCount) + 1;
        $tempSeries = str_pad($tempCount, 4, "0", STR_PAD_LEFT);
        $tempYear = Date("y");
        $tempMonth = Date("m");
        $tempCode = "MRR{$tempYear}-{$tempMonth}-{$tempSeries}";
        return $tempCode;
    }
    function getReadingCollection(){
        $post = $this->input->post();
        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        
        $count = $this->getReadingCount($search, $sortBy, $sortOrder, $query_builder, $post);
        if($count > 0){
            return $this->getReadingCollection_daterange();
        }else{
            return $this->getReadingCollection_initial();
        }
    }

    function getReadingCollection_daterange(){
        $resultarray = array();
        $post = $this->input->post();
        $to_billed = false;

        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();

        $filterFields = array("a.accountno", "r.meterno", "a.firstname", "a.lastname",
                "a.lot", "a.block", "r.ref_no", "r.reading_date", "r.status", "r.reading", "a.middlename");
        
        $this->db->select("a.middlename, r.id, a.accountno, r.meterno, a.firstname, a.lastname, a.lot, a.block, r.ref_no, r.reading_date, r.status, r.reading, r.status as status, a.model,r.is_billed");
        $this->db->from("hydra_billing.readings r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.is_archived", "0");
        if($query_builder){
            $this->db->where($query_builder);
        }
        if(($post['startDate'] != 'Invalid date') && ($post['endDate'] != 'Invalid date')){
            $this->db->where("r.reading_date >=", $post['startDate']);
            $this->db->where("r.reading_date <=", $post['endDate']);
        }
        
        if($search != ""){
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

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["id"] = $_query["id"];
                $data["accountno"] = $_query["accountno"];
                $data["meterno"] = $_query["meterno"];
                $data["block"] = $_query["block"];
                $data["lot"] = $_query["lot"];
                $data["model"] = $_query["model"];
                $data["ref_no"] = $_query["ref_no"];
                $data["reading_date"] = $_query["reading_date"];
                $data["status"] = $_query["is_billed"];
                $data["reading"] = number_format((float)$_query["reading"], 2, '.', '');

                if($_query["is_billed"] == 0){
                    $to_billed = true;
                }

                $resultarray[] = $data;
            }
        }
        $sql = $this->db->last_query();

        $total = $this->getReadingCount($search, $sortBy, $sortOrder, $query_builder, $post);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total, "to_billed" => $to_billed, "sql"=>$sql);
    }
    
    function getReadingCollection_initial(){
        $resultarray = array();
        $post = $this->input->post();
        $to_billed = false;

        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();

        $filterFields = array("a.accountno", "r.meterno", "a.firstname", "a.lastname",
                "a.lot", "a.block", "r.ref_no", "r.reading_date", "r.status", "r.reading", "a.middlename");
        
        $this->db->select("a.middlename, r.id, a.accountno, r.meterno, a.firstname, a.lastname, a.lot, a.block, r.ref_no, r.reading_date, r.status, r.reading, r.status as status, a.model,r.is_billed");
        $this->db->from("hydra_billing.readings r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.is_archived", "0");
        if($query_builder){
            $this->db->where($query_builder);
        }
        
        if($search != ""){
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

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["id"] = $_query["id"];
                $data["accountno"] = $_query["accountno"];
                $data["meterno"] = $_query["meterno"];
                $data["block"] = $_query["block"];
                $data["lot"] = $_query["lot"];
                $data["model"] = $_query["model"];
                $data["ref_no"] = $_query["ref_no"];
                $data["reading_date"] = $_query["reading_date"];
                $data["status"] = $_query["is_billed"];
                $data["reading"] = number_format((float)$_query["reading"], 2, '.', '');

                if($_query["is_billed"] == 0){
                    $to_billed = true;
                }

                $resultarray[] = $data;
            }
        }
        $sql = $this->db->last_query();

        $total = $this->getReadingCount($search, $sortBy, $sortOrder, $query_builder, $post);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total, "to_billed" => $to_billed, "sql"=>$sql);
    }

    function getReadingCount($search, $sortBy, $sortOrder, $query_builder, $post){
        $filterFields = array("a.accountno", "a.meterno", "a.firstname", "a.lastname", "a.lot", "a.block", "r.ref_no", "r.reading_date", "r.status", "r.reading", "a.middlename");
        $this->db->select("a.middlename,r.id,a.accountno,a.meterno,a.firstname,a.lastname, a.lot,a.block,r.ref_no,r.reading_date,r.status,r.reading,r.status as status");
        $this->db->from("hydra_billing.readings r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.is_archived", "0");
        if($query_builder){
            $this->db->where($query_builder);
        }
        if($post['startDate'] && $post['endDate']){
            $this->db->where("r.reading_date >=", $post['startDate']);
            $this->db->where("r.reading_date <=", $post['endDate']);
        }
        if($search != ""){
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
        //$this->db->order_by('r.ref_no', 'DESC');
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getReadingArchiveCollection(){
        $resultarray = array();
        $post = $this->input->post();

        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

        $filterFields = array("a.accountno", "r.meterno", "a.firstname", "a.lastname",
                "a.lot", "a.block", "r.ref_no", "r.reading_date", "r.status", "r.reading", "a.middlename");

        $this->db->select("a.middlename,r.id,a.accountno,r.meterno,a.firstname,a.lastname,a.lot,a.block,r.ref_no,r.reading_date,r.status,r.reading,r.status as status, a.model,r.is_billed");
        $this->db->from("hydra_billing.readings r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.is_archived", "1");

        if($search != ""){
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
        $this->db->order_by('r.ref_no', 'DESC');

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["id"] = $_query["id"];
                $data["accountno"] = $_query["accountno"];
                $data["meterno"] = $_query["meterno"];
                $data["block"] = $_query["block"];
                $data["lot"] = $_query["lot"];
                $data["model"] = $_query["model"];
                $data["ref_no"] = $_query["ref_no"];
                $data["reading_date"] = $_query["reading_date"];
                $data["status"] = $_query["is_billed"];
                $data["reading"] = number_format((float)$_query["reading"], 2, '.', '');
                $resultarray[] = $data;
            }
        }

        $total = $this->getReadingArchiveCount($search);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getReadingArchiveCount($search){
        $filterFields = array("a.accountno", "a.meterno", "a.firstname", "a.lastname", "a.lot", "a.block", "r.ref_no", "r.reading_date", "r.status", "r.reading", "a.middlename");
        $this->db->select("a.middlename,r.id,a.accountno,a.meterno,a.firstname,a.lastname,a.lot,a.block,r.ref_no,r.reading_date,r.status,r.reading,r.status as status");
        $this->db->from("hydra_billing.readings r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.status", "1");
        if($search != ""){
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
        $this->db->order_by('r.ref_no', 'DESC');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getReadingDetails($id){
        $this->db->select("r.is_billed, r.id, r.ref_no, r.account_id, r.reading_date, r.reading, r.status, r.pic, a.id as acct_id, a.accountno, r.meterno, CONCAT(a.firstname,' ',a.lastname) as name, a.lot, a.block");
        $this->db->from("hydra_billing.readings r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.id",$id);
        $this->db->where("r.is_archived", "0");
        $query = $this->db->get();

        $temp = $query->row_array();
        $temp["pic"] = isset($temp["pic"]) && $temp["pic"] && @unserialize($temp["pic"]) != FALSE ? @unserialize($temp["pic"]) : array();

        return $temp;
    }

    function uploadReadingPhoto(){
        $resultset = array();
            $imagesPath = "./uploads/hydra/reading_attachments";

            $createFilePath = false;

            if (!file_exists($imagesPath)) {
                $mkdir = mkdir($imagesPath, 0777, true);
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
                $config['upload_path'] = $imagesPath;
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG';
                $config['max_size'] = 100000;
                $config['create_thumbnail'] = true;

                $session = $this->core_layout->getCurrentSession();
                $data = $this->file_upload->uploadFile($config);

                if ($data["response"] == true) {
                    $files = $data["files"][0];
                    $filename = $files["file_name"];
                    if ($filename) {
                        $resultset["response"] = true;
                        $resultset["added_image"] = base_url("uploads/hydra/reading_attachments/{$filename}");
                        $resultset["render_image"] = "uploads/hydra/reading_attachments/" . $filename;
                        $resultset["toastr_msg"] = "Upload image successful.";
                        $resultset["toastr_state"] = "success";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Image upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Image upload failed!";
                    $resultset["toastr_state"] = "error";
                }
            }
        return $resultset;
    }

    function updateReading(){
        $current_date = date("Y-m-d H:i:s");
        $post = $this->input->post();
        $resultarray = array();

        $id = $post["id"];
        unset($post["id"]);
        $post["pic"] = serialize($post['pic']);

        $array = array();
        $array["reading"] = $post["reading"];
        $array["updated_by"] = $this->getUserdata()['emp_id'];
        $array["updated_at"] = $current_date;

        $logs_reading = "(Reading from ".$post['old_reading']." into ".$post['reading'].")";
        $logs_reading_date = "(Reading Date from ".$post['old_reading_date']." into ".$post['reading_date'].")";

        $previousReading = $this->getPreviousReadingDetails($post['account_id'],$post['reading_date'],$post['meterno']);
        if($previousReading["reading"] > $post["reading"]){
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Current reading must equal or exceed to previous reading.";
        } else {
            $this->db->where("id",$id);
            $query = $this->db->update("hydra_billing.readings",$array);

            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Reading successfully updated.";
                $this->core_layout->setEventLog("Reading - Change reading of ".$post["ref_no"]." ".$logs_reading." ".$logs_reading_date,"update", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error updating account.";
                $this->core_layout->setEventLog("Reading - Change reading of ".$post["ref_no"]." ".$logs_reading." ".$logs_reading_date,"update", "success", "hydra_billing", "user");
            }
        }
        $resultarray["test"] = $previousReading["reading"];

        return $resultarray;
    }
    
    function approveReading(){
        $resultarray = array();
        $post = $this->input->post();
        $id = $post["id"];
        unset($post["id"]);

        $this->db->where("id",$id);
        $post["approved_by"] = $this->getUserdata()['emp_id'];
        $post["approved_at"] = date("Y-m-d H:i:s");
        $post["status"] = "approved";
        $query = $this->db->update("hydra_billing.readings",$post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Reading successfully approved.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error approving reading.";
        }

        return $resultarray;
    }

    function createRate(){
        $resultarray = array();
        $post = $this->input->post();
        $rate = $post['rate'];
        $post['status'] = 1;

        if($this->checkSetupData("hydra_billing.rate") > 0){ // update
            $id = $post["id"];
            $post["updated_by"] = $this->getUserdata()['emp_id'];
            $post["updated_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "update";

            $this->db->where("id",$id);
            $query = $this->db->update("hydra_billing.rate",$post);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully updated rate.";
                $this->core_layout->setEventLog("Setup - Update rate into ".$rate,"update", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error updating rate.";
                $this->core_layout->setEventLog("Setup - Error updating rate into ".$rate,"update", "error", "hydra_billing", "user");
            }
        } else { // add 
            $post["created_by"] = $this->getUserdata()['emp_id'];
            $post["created_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "add";

            $query = $this->db->insert("hydra_billing.rate",$post);
            if($query){
                $resultarray['id'] = $this->db->insert_id();
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully added rate.";
                $this->core_layout->setEventLog("Setup - Added rate ".$rate,"insert", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error adding rate.";
                $this->core_layout->setEventLog("Setup - Error adding rate ".$rate,"insert", "error", "hydra_billing", "user");
            }
        }
        return $resultarray;
    }

    function generatePaymentAR(){
        $code = 'BH';
        $ref_no = $this->payment_ref_series('hydra_billing.payments', $code);
        return $ref_no;
    }

    function generateBill(){
        $resultarray = array();
        $post = $this->input->post();

        if(count($post["selectedReading"]) > 0){
            foreach($post["selectedReading"] as $reading_id){
                
                $current_date = date("Y-m-d H:i:s");
                $code = 'BHBR';
                $ref_no = $this->series($current_date, 'hydra_billing.bills', $code);
                $ref_series = explode("-",$ref_no)[2];
                $ref_month = explode("-",$ref_no)[1];
                $ref_yr = explode($code,explode("-",$ref_no)[0])[1];

                $dayOf_cutOff = $this->getAppliedCutOff()['day'];
                $dayOf_dueDate = $this->getAppliedDueDate()['day'];
                $rate = $this->getAppliedRate()['rate'];
                $currentReading = $this->getCurrentReadingDetails($reading_id);
                $month_words = date('F Y', strtotime($currentReading["reading_date"]));
                $over_payment = $this->computeOverPayment($currentReading["account_id"]);

                if($this->checkClientIsBilled($currentReading["account_id"], $currentReading["reading_date"]) > 0){
                    $response["status"] = FALSE;
                    $response["ref_no"] = $currentReading["ref_no"];
                    $response["msg"] = "Client is already billed on the month of ".$month_words;
                    $this->core_layout->setEventLog("Reading - tried to generate bill reading of ".$currentReading["ref_no"]." that client is already been billed","insert", "error", "hydra_billing", "user");
                } else {
                    $previousReading = $this->getPreviousReadingDetails(trim($currentReading['account_id']), trim($currentReading['reading_date']), trim($currentReading['meterno']));

                    $prev_reading_id = $previousReading ? $previousReading['reading_id'] : 0;
                    $prev_reading = $previousReading ? $previousReading['reading'] : 0;
                    $prev_reading_date = $previousReading || $previousReading != null ? $previousReading['reading_date'] : false;
                    
                    $totalUsage = $this->computeTotalUsage($currentReading['reading'], $prev_reading);
                    $charges = $this->computeTotalCharges($rate, $totalUsage);

                    $billing_from = $this->getBillingDateFrom($prev_reading_date, $currentReading['reading_date'], $dayOf_cutOff);
                  
                    $billing_to = date('Y-m-d', strtotime($currentReading['reading_date']));
                    $due_date = date('Y-m-d', strtotime("+".$dayOf_dueDate." day", strtotime($billing_to)));
                    
                    $list = array();
                    $list["billing_from"] = $billing_from;
                    $list["billing_to"] = $billing_to;
                    $list["due_date"] = $due_date;
                    $list["ref_no"] = $ref_no;
                    $list["ref_series"] = $ref_series;
                    $list["ref_yr"] = $ref_yr;
                    $list["ref_month"] = $ref_month;
                    $list["created_by"] = $this->getUserdata()['emp_id'];
                    $list['created_at'] = $current_date;
                    $list['status'] = 1;
                    $list["reading_id"] = $reading_id;
                    $list["account_id"] = $currentReading['account_id'];
                    $list["prev_reading_id"] = $prev_reading_id;
                    $list["current"] = $currentReading['reading'];
                    $list["previous"] = $prev_reading;
                    $list["usage"] = $totalUsage;
                    $list["rate"] = $rate;
                    $list["total_charges"] = $charges;
        
                    $query = $this->db->insert('hydra_billing.bills', $list);
                    $bill_id = $this->db->insert_id();

                    $response = array();
        
                    if($query){
                        $this->updateReadingBilled($reading_id, "1", "Billed");

                        if($over_payment >= $charges){
                        /* create payment */
                          $resultarray = array();
                          $code_insert = 'BHP';
                          $ref_no_insert = $this->series($current_date, 'hydra_billing.payments', $code_insert);
                          $ref_series_insert = explode("-",$ref_no_insert)[2];
                          $ref_month_insert = explode("-",$ref_no_insert)[1];
                          $ref_yr_insert = explode($code_insert,explode("-",$ref_no_insert)[0])[1];

                          $insert_payment = array();
                          $insert_payment['ref_no'] = $ref_no_insert;
                          $insert_payment['ref_series'] = $ref_series_insert;
                          $insert_payment['ref_yr'] = $ref_yr_insert;
                          $insert_payment['ref_month'] = $ref_month_insert;
                          $insert_payment["created_by"] = $this->getUserdata()['emp_id'];
                          $insert_payment["created_date"] = $current_date;
                          $insert_payment["balance_covered"] = preg_replace('/[^0-9a-zA-Z.]/', '', $charges);
                          $insert_payment["received_amount"] = 0;
                          $insert_payment["sub_total"] = preg_replace('/[^0-9a-zA-Z.]/', '', $charges);
                          $insert_payment["net_payment"] = preg_replace('/[^0-9a-zA-Z.]/', '', $charges);
                          $insert_payment["balance"] = 0;
                          $insert_payment['acknowledgement_receipt'] = $this->generatePaymentAR();
                          $insert_payment['bill_id'] = $bill_id;
                          $insert_payment['payment_type'] = 'cash';
                          $insert_payment['payment_date'] = date('Y-m-d', strtotime($current_date));
                          $insert_payment['account_id'] = $currentReading['account_id'];
                          
                          $query_insert_payment = $this->db->insert('hydra_billing.payments', $insert_payment);

                          if($query_insert_payment){
                              $this->updateBillingPaidStatus($bill_id, '1');
                              $this->updateDisconnectionStatus($currentReading["account_id"]);
                              $resultarray["status"] = TRUE;
                              $resultarray["ar_code"] = $this->generatePaymentAR();
                              $resultarray["msg"] = "Payment successfully saved.";
                              $this->core_layout->setEventLog("Payments - Created payment ".$insert_payment['ref_no'],"insert", "success", "hydra_billing", "user");
                          }else{
                              $resultarray["status"] = FALSE;
                              $resultarray["ar_code"] = FALSE;
                              $resultarray["msg"] = "Error creating payment.";
                              $this->core_layout->setEventLog("Payments - Error saving payment","insert", "error", "hydra_billing", "user");
                          }
                        }
                        /*  */
                        $response["status"] = TRUE;
                        $response["ref_no"] = $currentReading["ref_no"];
                        $response["msg"] = "Successfully billed on the month of ".$month_words;
                        $this->core_layout->setEventLog("Reading - Generate bill of ".$list["ref_no"],"insert", "success", "hydra_billing", "user");
        
                        try {
                            $email = $_SERVER['SERVER_NAME']=='conyxph.com' ? $this->getAccountEmail($list['account_id']) : "jp05@gccph.com";
                            if($this->billing_statement_email(true, $email, $bill_id)){
                                $this->core_layout->setEventLog("Reading - email send ".$list["ref_no"],"email", "success", "hydra_billing", "user");
                                $response["status_email"] = TRUE;
                            } else {
                                $this->core_layout->setEventLog("Reading - email send ".$list["ref_no"],"email", "error", "hydra_billing", "user");
                                $response["status_email"] = FALSE;
                            }
                        } catch (Exception $e) {
                            $response["status_email"] = FALSE;
                        }
        
                    }else{
                        $response["status"] = FALSE;
                        $response["ref_no"] = $currentReading["ref_no"];
                        $response["msg"] = "Error creating bill.";
                        $this->core_layout->setEventLog("Reading - Generate bill of ".$currentReading["ref_no"],"insert", "error", "hydra_billing", "user");
                    }
                }

                $response["reading_id"] = $reading_id;
                $resultarray[] = $response;
            }
        }

        return $resultarray;
    }

    function checkClientIsBilled($account_id, $reading_date){
        $current_m = date('m', strtotime($reading_date));
        $current_y = date('Y', strtotime($reading_date));
        $this->db->select("id");
        $this->db->from("hydra_billing.bills");
        $this->db->where("account_id",$account_id);
        $this->db->where("MONTH(billing_to)",$current_m);
        $this->db->where("YEAR(billing_to)",$current_y);
        $this->db->where("status","1");
        $query = $this->db->get();
        return $query->num_rows();
    }

    // get the previous reading date else -1 month of current reading date
    function getBillingDateFrom($previousReadingDate, $currentReadingDate, $dayOf_cutOff){
        if($previousReadingDate){
            $countDiff = $this->countMonthDiff($previousReadingDate, $currentReadingDate);
            if($currentReadingDate > $previousReadingDate && $countDiff == 0){
                return $previousReadingDate;
            }
        }
        return date('Y-m', strtotime("-1 month", strtotime($currentReadingDate))).'-'.$dayOf_cutOff;
    }

    function computeTotalUsage($reading, $prev_reading){
        $usage = (float)$reading - (float)$prev_reading;
        return number_format((float)$usage, 2, '.', '');
    }

    function computeTotalCharges($rate, $usage){
        if($usage > 11.46) {
            $charges = (float)$usage * (float)$rate;
            return number_format((float)$charges, 2, '.', '');
        }
        return 300.00;
    }

    function getCurrentReadingDetails($id){
        $this->db->select("reading_date, reading, account_id, meterno, is_billed, ref_no");
        $this->db->from("hydra_billing.readings");
        $this->db->where("id",$id);
        $this->db->where("is_archived", "0");
        $this->db->limit(1);
        $query = $this->db->get();
        $temp = $query->row_array();
        return $temp;
    }

    function getPreviousReadingDetails($account_id,$reading_date,$meterno){
        $this->db->select("id as reading_id, reading_date, reading");
        $this->db->from("hydra_billing.readings");
        $this->db->where("account_id",$account_id);
        $this->db->where("reading_date <",$reading_date);
        $this->db->where("meterno",$meterno);
        $this->db->where("is_archived", "0");
        $this->db->order_by("reading_date","DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        $temp = $query->row_array();
        return $temp;
    }

    function getBillingCollection(){
        $resultarray = array();
        $post = $this->input->post();
        $current_date = date("Y-m-d");

        $order_val = array(array("column"=>"1", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();

        $filterFields = array("a.middlename"," a.accountno", "a.meterno", "a.firstname", "a.lastname", "b.ref_no", "b.billing_from", "b.billing_to", "b.total_charges", "b.status", "b.due_date");

        $this->db->select("b.print_count, b.reading_id, a.middlename, b.is_paid, b.id, a.id as customer_id, a.accountno, a.meterno, a.firstname, a.lastname, a.is_disconnected, b.ref_no, b.billing_from, b.billing_to, b.total_charges, b.status, b.due_date, p.net_payment, p.sub_total, p.penalties, p.reconnection_fee, p.balance_covered, p.is_penalty, p.acknowledgement_receipt");
        $this->db->from("hydra_billing.bills b");
        $this->db->join("hydra_billing.accounts a", "a.id = b.account_id", "LEFT");
        $this->db->join("hydra_billing.payments as p", "p.bill_id = b.id", "LEFT");
        $this->db->where("b.status", "1");
        $this->db->group_by("b.id");
        if($query_builder){
            $this->db->where($query_builder);
        }

        if($search != ""){
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
        //$this->db->order_by('b.ref_no', 'DESC');
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $due_date = $_query["due_date"];

                $balanceLastBill = $this->computeBalanceLastBill($_query["customer_id"], $_query['id'], $due_date);

                if($_query["is_paid"] == 0) {
                    $penalties = $this->getPenalties();
                    $balance = $this->computeOverPayment($_query["customer_id"]);

                    // Start
                    /** 
                     * Date: August 24, 2023
                     * Description: This code was added because it causes undefined variable on $disconnectionFee
                     */
                    $reconnectionFee = $this->getReconnectionFee()['amount']; // --> Specified bcoz this is array result
                    // End

                    $total_amount = $_query["total_charges"];
                    $disconnectionFee = $_query["is_disconnected"] == 1 ? $reconnectionFee : 0.00;

                    if($current_date > $due_date){ // overdue
                        if($penalties['type'] == 'percentage'){
                            $overdue = ($penalties['amount'] / 100) * $total_amount;
                            $total_amount = $overdue + $total_amount;
                        } else {
                            $overdue = $penalties['amount'];
                            $total_amount = $penalties['amount'] + $total_amount;
                        }
                    } else {
                        $overdue = 0.00;
                    }

                    $net_payment = ($total_amount + $disconnectionFee) - $balance;

                    // check if net_payment has remaining balance (ex value -100)
                    // then set the net_payment into zero else remaining payment
                    if($net_payment < 0){
                        $net_payment = 0.00;
                        // $overdue = 0.00;
                    } else {
                        $net_payment = $net_payment;
                    }
                } else {

                    $due_date = $_query["due_date"];
                    $overdue = $_query["is_penalty"] ? unserialize($_query["penalties"])[0]["overdue"] : 0.00;
                    $disconnectionFee = $_query["reconnection_fee"];
                    $balance = $_query["balance_covered"];
                }
                $status = $_query["status"]=='1' ? 'Active' : 'Archive';

                if($status=='Archive'){
                    $paid_status = 'Archive';
                } else if($_query["is_paid"]=='1'){
                    $paid_status = 'Paid';
                } else if($current_date > $_query["due_date"]){
                    $paid_status = 'Overdue'; 
                } else if($current_date == $_query["due_date"]){
                    $paid_status = 'Today due';
                } else {
                    $paid_status = 'On going';
                }

                $data["checkbox"] = "";
                $data["name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["id"] = $_query["id"];
                $data["reading_id"] = $_query["reading_id"];
                $data["accountno"] = $_query["accountno"];
                $data["meterno"] = $_query["meterno"];
                $data["ref_no"] = $_query["ref_no"];
                $data["print_count"] = $_query["print_count"];
                $data["billing_period"] = $_query["billing_from"] ." - ".$_query["billing_to"];
                $data["due_date"] = $_query["due_date"];
                // $data["total_charges"] = '₱ '.number_format((float)$_query["total_charges"], 2, '.', '');
                $data["status"] = $paid_status;

                $data['current_due'] = $_query['total_charges'];
                $data["balance"] = $balance;
                $data["balanceLastBill"] = $balanceLastBill;
                $data["overdue"] = number_format(($overdue + $data["balanceLastBill"]["total_penalty"]),2,".",",");
                $data["disconnection_fee"] = $disconnectionFee;
                // $data["total_charges"] = number_format((($_query['total_charges'] + $balanceLastBill["total_amount"] + $overdue + $disconnectionFee) - $balance),2,".",",");
                $data["total_charges"] = number_format((((int)$_query['total_charges'] + (int)$balanceLastBill["total_balance"] + (int)$disconnectionFee + (int)$data["overdue"]) - (int)$balance),2,".",",");

                $data["solution"] = $_query['total_charges'] . " + " .  $balanceLastBill["total_balance"] . " + " . $disconnectionFee . " + " . $data["overdue"] . " - " . $balance  . " = " . $data["total_charges"];
                $resultarray[] = $data;
            }
        }

        $total = $this->getBillingCount($search,$query_builder);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getBillingCount($search,$query_builder){
        $filterFields = array("a.middlename"," a.accountno", "a.meterno", "a.firstname", "a.lastname", "b.ref_no", "b.billing_from", "b.billing_to", "b.total_charges", "b.status", "b.due_date");
        $this->db->select("a.middlename, b.is_paid, b.id, a.accountno, a.meterno, a.firstname, a.lastname, b.ref_no, b.billing_from, b.billing_to, b.total_charges, b.status, b.due_date");
        $this->db->from("hydra_billing.bills b");
        $this->db->join("hydra_billing.accounts a", "a.id = b.account_id", "LEFT");
        $this->db->where("b.status", "1");
        if($query_builder){
            $this->db->where($query_builder);
        }
        if($search != ""){
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
        $this->db->order_by('b.ref_no', 'DESC');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getReadingbyAccount(){
        $resultarray = array();
        $post = $this->input->post();
        
        $this->db->select("*");
        $this->db->from("hydra_billing.readings");
        $this->db->where("account_id",$post["id"]);
        $this->db->where("meterno",$post["meterno_raw"]);
        $this->db->where("is_billed","0");
        $this->db->where("is_archived", "0");
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["ref_no"];
                $resultarray[] = $data;
            }
        }

        return array("results"=>$resultarray);
    }

    function getAccountPreviousMeterReading(){
        $resultarray = array();
        $resultdata = array();
        $post = $this->input->post();

        if($this->getAccountPreviousMeterReadingCount($post["account_id"]) > 1){
            $this->db->select("*");
            $this->db->from("hydra_billing.readings");
            $this->db->where("account_id",$post["account_id"]);
            $this->db->where("is_archived", "0");
            $this->db->order_by("id","ASC");
            $this->db->limit(1);
            $query = $this->db->get()->row_array();
        } else {
            $query = array();
        }

        $resultarray["data"] = $query;
        $resultarray["status"] = TRUE;

        return $resultarray;
    }

    function getAccountPreviousMeterReadingCount($account_id){
        $this->db->select("id");
        $this->db->from("hydra_billing.readings");
        $this->db->where("account_id",$account_id);
        $this->db->where("is_archived", "0");
        $this->db->order_by("id","ASC");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getAccountCurrentMeterReading(){
        $post = $this->input->post();
        $this->db->select("*");
        $this->db->from("hydra_billing.readings");
        $this->db->where("id",$post["reading_id"]);
        $this->db->where("meterno",$post["meterno_raw"]);
        $this->db->where("is_archived", "0");
        $this->db->order_by("id","DESC");
        $this->db->limit(1);
        $query = $this->db->get()->row_array();
        return array("data_current"=>$query, "data_previous"=>$this->getPreviousMeterReading($query['reading_date'],$post['account_id'],$query['meterno']));
    }

    function getPreviousMeterReading($reading_date,$account_id,$meterno){
        $this->db->select("*");
        $this->db->from("hydra_billing.readings");
        $this->db->where("reading_date <",$reading_date);
        $this->db->where("account_id",$account_id);
        $this->db->where("meterno",$meterno);
        $this->db->where("is_archived", "0");
        $this->db->order_by("reading_date","DESC");
        $this->db->limit(1);
        $query = $this->db->get()->row_array();
        return $query;
    }

    function getAppliedRate(){
        $this->db->select("*");
        $this->db->from("hydra_billing.rate");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAppliedLimit(){
        $this->db->select("*");
        $this->db->from("hydra_billing.limit");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAppliedCutOff(){
        $this->db->select("day");
        $this->db->from("hydra_billing.cut_off_period");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAppliedDueDate(){
        $this->db->select("day");
        $this->db->from("hydra_billing.due_date");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Billing add bill
    function createNewBill(){
        $current_date = date("Y-m-d H:i:s");
        $post = $this->input->post();
        $resultarray = array();
        $code = 'BHBR';
        $ref_no = $this->series($current_date, 'hydra_billing.bills', $code);
        $ref_series = explode("-",$ref_no)[2];
        $ref_month = explode("-",$ref_no)[1];
        $ref_yr = explode($code,explode("-",$ref_no)[0])[1];

        $post["ref_no"] = $ref_no;
        $post["ref_series"] = $ref_series;
        $post["ref_yr"] = $ref_yr;
        $post["ref_month"] = $ref_month;
        $post["created_by"] = $this->getUserdata()['emp_id'];
        $post['created_at'] = $current_date;
        $post["total_charges"] = preg_replace('/[^0-9a-zA-Z.]/', '', $post['total_charges']);
        $post['status'] = 1;

        $query = $this->db->insert('hydra_billing.bills', $post);
        $bill_id = $this->db->insert_id();

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Billing successfully saved.";

            $this->updateReadingBilled($post['reading_id'], "1", "Billed");
            $this->core_layout->setEventLog("Billing - Created bill ".$post["ref_no"],"insert", "success", "hydra_billing", "user");

            try {
                $email = $_SERVER['SERVER_NAME']=='conyxph.com' ? $this->getAccountEmail($post['account_id']) : "jp05@gccph.com";
                if($this->billing_statement_email(true, $email, $bill_id)){
                    $this->core_layout->setEventLog("Billing - Success email send ".$post["ref_no"],"email", "success", "hydra_billing", "user");
                } else {
                    $this->core_layout->setEventLog("Billing - Failed email send ".$post["ref_no"],"email", "error", "hydra_billing", "user");
                }
            } catch (Exception $e) {
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error creating bill.";
            }

        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error creating bill.";
            
            $this->core_layout->setEventLog("Billing - Error saving bill","insert", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function getAccountEmail($account_id){
        $this->db->select("LOWER(email) as email");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("id",$account_id);
        $query = $this->db->get();
        return $query->row_array()["email"];
    }

    function updateReadingBilled($id, $is_billed, $status){
        $this->db->where("id",$id);
        $post["createbill_by"] = $this->getUserdata()['emp_id'];
        $post["createbill_at"] = date("Y-m-d H:i:s");
        $post["is_billed"] = $is_billed;
        $post["status"] = $status;
        $query = $this->db->update("hydra_billing.readings",$post);
        if($query){
            return true;
        } else {
            return false;
        }
    }

    function getBillData(){
        $post = $this->input->post();
        $this->db->select("b.id, a.accountno, a.meterno, a.firstname, a.lastname, a.block, a.lot, a.street, a.brgy, a.city, a.province, b.ref_no, b.billing_from, b.billing_to, b.previous, b.current, b.usage, b.rate, b.total_charges, b.status, b.due_date, r.ref_no as reading_refno, b.print_count, b.is_paid");
        $this->db->from("hydra_billing.bills b");
        $this->db->join("hydra_billing.accounts a","a.id = b.account_id", "LEFT");
        $this->db->join("hydra_billing.readings r","r.id = b.reading_id", "LEFT");
        $this->db->where("b.id",$post["id"]);
        $this->db->where("r.is_archived", "0");
        $query = $this->db->get();
        return array("billdata"=>$query->row_array(),'limit'=>$this->getAppliedLimit()['limit']);
    }
    
    // fetch the data of bill details for print
    function getBillforPrintData($id){
        $this->db->select("b.id, a.accountno, a.meterno, a.firstname, a.lastname, a.block, a.lot, a.street, a.brgy, a.city, a.province, b.ref_no, b.billing_from, b.is_paid,
                           b.billing_to, b.previous, b.current, b.usage, b.rate, b.total_charges, b.status, b.due_date, r.ref_no as reading_refno, a.is_disconnected, a.id as customer_id");
        $this->db->from("hydra_billing.bills b");
        $this->db->join("hydra_billing.accounts a","a.id = b.account_id", "LEFT");
        $this->db->join("hydra_billing.readings r","r.id = b.reading_id", "LEFT");
        $this->db->where("b.id",$id);
        $query = $this->db->get()->row_array();
        $current_date = date("Y-m-d");
        $reconnectionFee = $this->getReconnectionFee()['amount'];
        $charges = $query["total_charges"];
        $due_date = $query["due_date"];
        $balanceLastBill = $this->computeBalanceLastBill($query["customer_id"], $id, $due_date);

        if($query["is_paid"] == 0){ // not paid
            $ar = '';
            $penalties = $this->getPenalties();
            $balance = $this->computeOverPayment($query["customer_id"]);

            $total_amount = $charges;
            $disconnectionFee = $query["is_disconnected"] == 1 ? $reconnectionFee : 0.00;

            if($current_date > $due_date){ // overdue
                if($penalties['type'] == 'percentage'){
                    $overdue = ($penalties['amount'] / 100) * $total_amount;
                    $total_amount = $overdue + $total_amount;
                } else {
                    $overdue = $penalties['amount'];
                    $total_amount = $penalties['amount'] + $total_amount;
                }
            } else {
                $overdue = 0.00;
            }

            $net_payment = ($total_amount + $disconnectionFee) - $balance;

            // check if net_payment has remaining balance (ex value -100)
            // then set the net_payment into zero else remaining payment
            if($net_payment < 0){
                $net_payment = 0.00;
                // $overdue = 0.00;
            } else {
                $net_payment = $net_payment;
            }

        } else { // paid
            $this->db->select("net_payment, sub_total, penalties, reconnection_fee, balance_covered, is_penalty, acknowledgement_receipt");
            $this->db->from("hydra_billing.payments");
            $this->db->where("bill_id",$query["id"]);
            $query_ = $this->db->get()->row_array();

            $balance = $query_["balance_covered"];
            $overdue = $query_["is_penalty"] ? unserialize($query_["penalties"])[0]["overdue"] : 0.00;
            $net_payment = $query_["net_payment"];
            $disconnectionFee = $query_["reconnection_fee"];
            $ar = $query_["acknowledgement_receipt"];
        }

        $data = array();
        $data["id"] = $query["id"];
        $data["accountno"] = $query["accountno"];
        $data["ar"] = $ar ? $ar : '';
        $data["meterno"] = $query["meterno"];
        $data["firstname"] = $query["firstname"];
        $data["lastname"] = $query["lastname"];
        $data["block"] = $query["block"];
        $data["lot"] = $query["lot"];
        $data["street"] = $query["street"];
        $data["brgy"] = $query["brgy"];
        $data["city"] = $query["city"];
        $data["province"] = $query["province"];
        $data["ref_no"] = $query["ref_no"];
        $data["billing_from"] = $query["billing_from"];
        $data["billing_to"] = $query["billing_to"];
        $data["previous"] = $query["previous"];
        $data["current"] = $query["current"];
        $data["usage"] = $query["usage"];
        $data["rate"] = $query["rate"];
        $data["due_date"] = $query["due_date"];
        $data["reading_refno"] = $query["reading_refno"];
        $data["balance"] = $balance;
        $data["balanceLastBill"] = $balanceLastBill;
        $data["current_due"] = $charges;
        $data["total_charges"] = $net_payment;
        $data["overdue"] = $overdue;
        $data["reconnection_fee"] = $reconnectionFee;
        $data["disconnection_fee"] = $disconnectionFee;
        $data["is_paid"] = $query["is_paid"]; 
        return $data;
    }

    function updatebill(){
        $resultarray = array();
        $current_date = date("Y-m-d H:i:s");
        $post = $this->input->post();
        $id = $post["id"];
        $post["total_charges"] = str_replace( ',', '', $post["total_charges"],$a );
        unset($post["id"]);
        $post["updated_by"] = $this->getUserdata()['emp_id'];
        $post["updated_at"] = $current_date;

        $this->db->where("id",$id);
        $query = $this->db->update("hydra_billing.bills",$post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Bill successfully updated.";

            $this->core_layout->setEventLog("Billing - Update bill details of ".$post["ref_no"],"update", "success", "hydra_billing", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error updating bill.";

            $this->core_layout->setEventLog("Billing - Error updating bill details ".$post["ref_no"],"update", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function countPrint(){
        $post = $this->input->post();
        $this->db->where("id",$post['id']);
        $query = $this->db->update("hydra_billing.bills",array("print_count"=>1));
        return $query;
    }   

    function updatePrintLimit(){
        $resultarray = array();
        $current_date = date("Y-m-d H:i:s");
        $post = $this->input->post();
        $post['status'] = 1;

        if($this->checkSetupData("hydra_billing.limit") > 0){ // update
            $id = $post["id"];
            $post["updated_by"] = $this->getUserdata()['emp_id'];
            $post["updated_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "update";

            $this->db->where("id",$id);
            $query = $this->db->update("hydra_billing.limit",$post);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully updated limit.";
                $this->core_layout->setEventLog("Setup - Update print limit into ".$post['limit'],"update", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error updating limit.";
                $this->core_layout->setEventLog("Setup - Error updating print limit into ".$post['limit'],"update", "error", "hydra_billing", "user");
            }
        } else { // add 
            $post["created_by"] = $this->getUserdata()['emp_id'];
            $post["created_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "add";

            $query = $this->db->insert("hydra_billing.limit",$post);
            if($query){
                $resultarray['id'] = $this->db->insert_id();
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully added limit.";
                $this->core_layout->setEventLog("Setup - Added print limit ".$post['limit'],"insert", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error adding limit.";
                $this->core_layout->setEventLog("Setup - Error adding print limit ".$post['limit'],"insert", "error", "hydra_billing", "user");
            }
        }
        return $resultarray;
    }

    function massBillPrint(){
        $post = $this->input->post();
        $result = array();
        $arrData = array();

        if(count($post["ids"]) > 0):
            $html = "";
            foreach($post["ids"] as $_ids){
                $query[] = $this->billing->getBillforPrintData($_ids);
                
                $arrData["data"] = $this->billing->getBillforPrintData($_ids);
                $html .= $this->load->view("eforms/billing/billing/mass_print", $arrData, true);
            }

            foreach($query as $q){
                $this->core_layout->setEventLog("Billing - Print ".$q['ref_no'],"print", "success", "hydra_billing", "user");
                $this->updatePrintCount($q['id']);
            }

            $result["status"] = TRUE;
            $result["html"] = $html;
        else:
            $result["status"] = FALSE;
        endif;

        return $result;
    }

    function updatePrintCount($id){
        $array = array();
        $print_count = $this->fetchPrintCount($id);
        $array['print_count'] = $print_count + 1;
        $this->db->where("id",$id);
        $this->db->update("hydra_billing.bills",$array);
    }

    function fetchPrintCount($id){
        $this->db->select("print_count");
        $this->db->from("hydra_billing.bills");
        $this->db->where("id",$id);
        $query = $this->db->get()->row_array();
        return $query['print_count'];
    }

    function updatePenaltyDetails(){
        $current_date = date("Y-m-d H:i:s");
        $post = $this->input->post();

        if($this->checkSetupData("hydra_billing.penalties") > 0){ // update
            $id = $post["id"];
            $post["updated_by"] = $this->getUserdata()['emp_id'];
            $post["updated_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "update";

            $this->db->where("id",$id);
            $query = $this->db->update("hydra_billing.penalties",$post);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully updated penalty.";
                $this->core_layout->setEventLog("Setup - Update penalty into ".$post["amount"],"update", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error updating penalty.";
                $this->core_layout->setEventLog("Setup - Error updating penalty into ".$post["amount"],"update", "error", "hydra_billing", "user");
            }
        } else { // add 
            $post["created_by"] = $this->getUserdata()['emp_id'];
            $post["created_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "add";

            $query = $this->db->insert("hydra_billing.penalties",$post);
            if($query){
                $resultarray['id'] = $this->db->insert_id();
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully added penalty.";
                $this->core_layout->setEventLog("Setup - Added penalty ".$post["amount"],"insert", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error adding penalty.";
                $this->core_layout->setEventLog("Setup - Adding penalty ".$post["amount"],"insert", "error", "hydra_billing", "user");
            }
        }

        return $resultarray;
    }

    function getPenaltyDetails(){
        $this->db->select("*");
        $this->db->from("hydra_billing.penalties");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getUnpaidBillbyAccountId(){
        $post = $this->input->post();

        $this->db->select("id, ref_no, ref_series");
        $this->db->from("hydra_billing.bills");
        $this->db->where("account_id",$post["id"]);
        $this->db->where("is_paid",'0');
        $this->db->where("status",'1');
        $this->db->order_by("due_date", 'ASC');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();

                $data["id"] = $_query["id"];
                $data["text"] = $_query["ref_no"]."|".$_query["ref_series"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function getCustomerDetails(){
        $post = $this->input->post();
        $this->db->select("*");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("id",$post["customer_id"]);
        $query = $this->db->get();
        return array("data"=>$query->row_array(),"balance"=>$this->computeOverPayment($post["customer_id"]));
    }

    // (sum all received_amount) - (sum all net_payment) = balance
    // balance - balance_covered = over payment
    function computeOverPayment($customer_id){
        $this->db->select("SUM(received_amount - net_payment) as balance, SUM(balance_covered) as balance_covered");
        $this->db->from("hydra_billing.payments");
        $this->db->where("account_id",$customer_id);
        $this->db->where("is_archive","0");
        $query = $this->db->get()->row_array();
        $balance = number_format((float)$query['balance'], 2, '.', '') - number_format((float)$query['balance_covered'], 2, '.', '');
        if($balance < 0){
          $balance = 0;
        }
		return number_format($balance, 2, '.', '');
    }

    function checkOverdue(){
        $post = $this->input->post();

        $this->db->select("*");
        $this->db->from("hydra_billing.bills");
        $this->db->where("id", $post['id']);
        $query = $this->db->get();
        $row = $query->row_array();

        $result = array();
        $array_penalties = array();

        $previous_payments = $this->getBillPayments($post['id']);
        $penalties = $this->getPenalties();
        $isDisconnectionStatus = $this->getCustomerDisconnectionStatus($post['customer_id']);
        $reconnectionFee = $this->getReconnectionFee();
        $billing_amount = $row['total_charges'];
        $due_date = $row['due_date'];
        $total_amount = $billing_amount;
        $current_date = date('Y-m-d', strtotime($post["payment_date"]));

        $countDiff = $this->countMonthDiff($due_date, $current_date);
        $result['countMonthDiff'] = $countDiff;
        $isDisconnection = $isDisconnectionStatus=='1' ? true : false;

        if($current_date > $due_date){

            $result['isPenalty'] = true;

            // if($countDiff > 0){ // more than a month accumulate penalty
            //     for($i=0; $i<=$countDiff; $i++){
                    
            //         $dueDate = $this->dueMonth($due_date, $i);
            //         if($current_date > $dueDate){
            //             $list = array();
    
            //             if($penalties['type'] == 'percentage'){
            //                 $overdue = ($penalties['amount'] / 100) * $total_amount;
            //                 $total_amount = $overdue + $total_amount;
            //             } else {
            //                 $overdue = $penalties['amount'];
            //                 $total_amount = $penalties['amount'] + $total_amount;
            //             }
                        
            //             $list['dueDate'] = $dueDate;
            //             $list['total_amount'] = number_format((float)$total_amount, 2, '.', '');
            //             $list['overdue'] = number_format((float)$overdue, 2, '.', '');
            //             $list['status'] = 'more than a month';
            //             $array_penalties[] = $list;
            //         }
            //     }
            // } else { // 1 month
                $list = array();
                if($penalties['type'] == 'percentage'){
                    $overdue = ($penalties['amount'] / 100) * $total_amount;
                    $total_amount = $overdue + $total_amount;
                } else {
                    $overdue = $penalties['amount'];
                    $total_amount = $penalties['amount'] + $total_amount;
                }

                $list['dueDate'] = $due_date;
                $list['total_amount'] = number_format((float)$total_amount, 2, '.', '');
                $list['overdue'] = number_format((float)$overdue, 2, '.', '');
                $list['status'] = "due date";
                $array_penalties[] = $list;
            //}
        } else { // no penalty
            $result['isPenalty'] = false;
        }

        $net_payment = $isDisconnection ? ($total_amount + $reconnectionFee['amount']) : $total_amount;
        
        $result['isDisconnection'] = $isDisconnection;
        $result['reconnectionFee'] = $isDisconnection ? $reconnectionFee : array();
        $result['net_payment'] = $previous_payments ? (number_format((float)$net_payment, 2, '.', '') - $previous_payments) : number_format((float)$net_payment, 2, '.', '');
        $result['billing_amount'] = $previous_payments ? ($billing_amount - $previous_payments) : $billing_amount;
        $result['array_penalties'] = $array_penalties;
        $result['serialize_penalties'] = serialize($array_penalties);
        
        return $result;
    }
    
    function getBillPayments($bill_id){
      $this->db->select("sum(received_amount) as total_paid");
      $this->db->from("hydra_billing.payments");
      $this->db->where("is_archive", 0);
      $this->db->where("bill_id", $bill_id);
      $query = $this->db->get();
      $data = $query->row();
      if($query->num_rows() > 0){
        $amount = $data->total_paid;
      }else{
        $amount = 0;
      }
      return $amount;
    }

    function getAllBillPayments(){
      $post = $this->input->post();
      $arr_data = array();
      $this->db->select("*");
      $this->db->from("hydra_billing.payments");
      $this->db->where("is_archive", 0);
      $this->db->where("bill_id", $post['id']);
      $this->db->order_by("id", "DESC");
      $query = $this->db->get();
      $data = $query->result();
      if($query->num_rows() > 0){
        foreach($data as $temp_data){
          $arr_data[] = $temp_data;
        }
      }
      return $arr_data;
    }

    function dueMonth($due_date, $i){
        return date('Y-m-d', strtotime('+'.$i.' month', strtotime($due_date)));
    }

    function dayDifference($due_date, $current_date){
        $date1 = new DateTime($due_date);
        $date2 = new DateTime($current_date);
        return $date2->diff($date1)->format('%a');
    }

    function countMonthDiff($due_date, $current_date){
        $d1 = new DateTime($current_date);
        $d2 = new DateTime($due_date);
        $Months = $d2->diff($d1);
        return (($Months->y) * 12) + ($Months->m);
    }

    function getPenalties(){
        $this->db->select("*");
        $this->db->from("hydra_billing.penalties");
        $query = $this->db->get();
        $row = $query->row_array();
        return $row;
    }

    function getCustomerDisconnectionStatus($customer_id){
        $this->db->select("is_disconnected");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("id",$customer_id);
        $query = $this->db->get();
        $row = $query->row_array();
        return $row['is_disconnected'];
    }

    function generatePaymentReferenceNo(){
        $qData = $this->db->get("hydra_billing.payments");
        $tempCount = $qData->num_rows();
        $tempCount = ($tempCount == 0)? 1: intval($tempCount) + 1;
        $tempSeries = str_pad($tempCount, 4, "0", STR_PAD_LEFT);
        $tempYear = Date("y");
        $tempMonth = Date("m");
        $tempCode = "BHP{$tempYear}-{$tempMonth}-{$tempSeries}";
        return $tempCode;
    }

    function createNewPayment(){
        $current_date = date("Y-m-d H:i:s");
        $post = $this->input->post();
        $resultarray = array();
        if(isset($post['received_amount']) <= 0){
          $resultarray["status"] = FALSE;
          $resultarray["msg"] = "Received Amount should not be less than or equal to 0.";
        }else{
          $code = 'BHP';
          $ref_no = $this->series($current_date, 'hydra_billing.payments', $code);
          $ref_series = explode("-",$ref_no)[2];
          $ref_month = explode("-",$ref_no)[1];
          $ref_yr = explode($code,explode("-",$ref_no)[0])[1];

          $post['ref_no'] = $ref_no;
          $post['ref_series'] = $ref_series;
          $post['ref_yr'] = $ref_yr;
          $post['ref_month'] = $ref_month;
          $post["created_by"] = $this->getUserdata()['emp_id'];
          $post["created_date"] = $current_date;
          $post["balance_covered"] = preg_replace('/[^0-9a-zA-Z.]/', '', $post['balance_covered']);
          $post["received_amount"] = preg_replace('/[^0-9a-zA-Z.]/', '', $post['received_amount']);
          $post["sub_total"] = preg_replace('/[^0-9a-zA-Z.]/', '', $post['sub_total']);
          $post["net_payment"] = preg_replace('/[^0-9a-zA-Z.]/', '', $post['net_payment']);
          $post["balance"] = preg_replace('/[^0-9a-zA-Z.]/', '', $post['balance']);
          $post['acknowledgement_receipt'] = $this->generatePaymentAR();
          $query = $this->db->insert('hydra_billing.payments', $post);

          if($query){
            if($post['received_amount'] >= $post['net_payment']){
              $this->updateBillingPaidStatus($post['bill_id'], '1');
              $this->updateDisconnectionStatus($post['account_id']);
            }
            $resultarray["status"] = TRUE;
            $resultarray["ar_code"] = $this->generatePaymentAR();
            $resultarray["msg"] = "Payment successfully saved.";
            $this->core_layout->setEventLog("Payments - Created payment ".$post['ref_no'],"insert", "success", "hydra_billing", "user");
          }else{
            $resultarray["status"] = FALSE;
            $resultarray["ar_code"] = FALSE;
            $resultarray["msg"] = "Error creating payment.";

            $this->core_layout->setEventLog("Payments - Error saving payment","insert", "error", "hydra_billing", "user");
          }
          
        }

        return $resultarray;
    }

    function updateBillingPaidStatus($id, $status){
        $post["is_paid"] = $status;
        $this->db->where("id",$id);
        $query = $this->db->update('hydra_billing.bills', $post);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function updateDisconnectionStatus($id){
        $post["is_disconnected"] = '0';
        $this->db->where("id",$id);
        $this->db->where("is_disconnected",'1');
        $query = $this->db->update('hydra_billing.accounts', $post);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function getBillingPayment(){
        $resultarray = array();
        $post = $this->input->post();
        $current_date = date("Y-m-d");

        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        
        $filterFields = array("b.payment_details"," b.ref_no"," a.firstname", "a.lastname", "b.payment_type", "b.received_amount", "b.payment_date", "b.net_payment", 
        "b.created_by", "b.created_date", "c.ref_no", "d.firstname", "d.lastname","a.middlename", "b.acknowledgement_receipt");

        $this->db->select("a.middlename, b.reconnection_fee, b.payment_details, b.is_penalty, b.ref_no as payment_ref_no, b.penalties, b.id, a.firstname, a.lastname, 
        b.payment_type, b.received_amount, b.payment_date, b.net_payment, b.created_by, b.created_date, c.ref_no, d.firstname as created_firstname, 
        d.lastname as created_lastname, c.due_date, b.acknowledgement_receipt");
        $this->db->from("hydra_billing.payments b");
        $this->db->join("hydra_billing.accounts a", "a.id = b.account_id", "LEFT");
        $this->db->join("hydra_billing.bills c", "c.id = b.bill_id", "LEFT");
        $this->db->join("gccmaster.tblemployees d", "d.id = b.created_by", "LEFT");
        $this->db->where("b.is_archive",'0');
        
        if($search != ""){
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

        $this->db->order_by("b.created_date","DESC");
        
        $query = $this->db->get();
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();

                $data["checkbox"] = '';
                $data["penalties"] = unserialize($_query["penalties"]);
                $data["payment_ref_no"] = $_query['payment_ref_no'];
                $data["due_date"] = $_query['due_date'];
                $data["account_name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["id"] = $_query["id"];
                $data["bill"] = $_query["ref_no"];
                $data["payment_details"] = $_query["payment_details"];
                $data["is_penalty"] = $_query["is_penalty"];
                $data["reconnection_fee"] = $_query["reconnection_fee"];
                $data["payment_type"] = $_query["payment_type"];
                $data["received_amount"] = '₱ '.number_format((float)$_query["received_amount"], 2, '.', '');
                $data["payment_date"] = $_query["payment_date"];
                $data["net_payment"] = '₱ '.number_format((float)$_query["net_payment"], 2, '.', '');
                $data["created_by"] = $_query['created_firstname'].' '.$_query['created_lastname'];
                $data["created_date"] = date('Y-m-d g:i A', strtotime($_query["created_date"]));
                $data['acknowledgement_receipt'] = $_query["acknowledgement_receipt"];
              
                $data["isArchiveHide"] = false;
                // if($this->authenticate->getRoleId() == "1"){
                //     $data["isArchiveHide"] = false;
                // } else {
                //     $data["isArchiveHide"] = $current_date > date('Y-m-d', strtotime($_query["created_date"])) ? true : false;
                // }
                
                $resultarray[] = $data;
            }
        }

        $total = $this->getPaymentCount($search);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getPaymentCount($search){
        $filterFields = array("b.payment_details"," b.ref_no"," a.firstname", "a.lastname", "b.payment_type", "b.received_amount", "b.payment_date", "b.net_payment", "b.created_by", "b.created_date", "c.ref_no", "d.firstname", "d.lastname", "a.middlename", "b.acknowledgement_receipt");
        $this->db->select("a.middlename, b.reconnection_fee, b.payment_details, b.is_penalty, b.ref_no as payment_ref_no, b.penalties, b.id, a.firstname, a.lastname, b.payment_type, b.received_amount, b.payment_date, b.net_payment, b.created_by, b.created_date, c.ref_no, d.firstname as created_firstname, d.lastname as created_lastname");
        $this->db->from("hydra_billing.payments b");
        $this->db->join("hydra_billing.accounts a", "a.id = b.account_id", "LEFT");
        $this->db->join("hydra_billing.bills c", "c.id = b.bill_id", "LEFT");
        $this->db->join("gccmaster.tblemployees d", "d.id = b.created_by", "LEFT");
        $this->db->where("b.is_archive",'0');
        if($search != ""){
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

    function getPaymentDetails($id){
        if(isset($id) && $id){
            $this->db->where("id",$id);
            $query = $this->db->get("hydra_billing.payments");
            return $query->row_array();
        }
    }

    function getBillingDetails($id){
        if(isset($id) && $id){
            $this->db->where("id",$id);
            $query = $this->db->get("hydra_billing.bills");
            return $query->row_array();
        }
    }

    function account_details($id){
        $this->db->from('hydra_billing.accounts');
        $this->db->where('id', $id);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){  
                $arrData[$key] = $rs;
            }
            return $arrData[0];
        }else{
            return array();
        }
    }

    function updatePayment(){
        $post = $this->input->post();
        $resultarray = array();
        $id = $post["id"];
        $post["updated_by"] = $this->getUserdata()['emp_id'];
        $post["updated_date"] = date("Y-m-d H:i:s");

        $this->db->where("id",$id);
        $query = $this->db->update('hydra_billing.payments', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Payment successfully saved.";
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error creating payment.";
        }

        return $resultarray;
    }

    function archivePayment(){
        $resultarray = array();
        $data = array();
        $post = $this->input->post();
        $id = $post["id"];
        $data["is_archive"] = '1';

        $this->db->where("id",$id);
        $query = $this->db->update('hydra_billing.payments', $data);

        if($query){

            $bill_id = $this->getPaymentBill_id($id);
            $this->updateBillingPaidStatus($bill_id, '0');

            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Successfully archived.";

            $this->core_layout->setEventLog("Payments - Archived payment ".$post["payment_ref_no"],"archived", "success", "hydra_billing", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error creating payment.";

            $this->core_layout->setEventLog("Payments - Error archiving payment ".$post["payment_ref_no"],"archived", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function getPaymentBill_id($id){
        $this->db->select("bill_id");
        $this->db->from("hydra_billing.payments");
        $this->db->where("id", $id);
        return $this->db->get()->row_array()['bill_id'];
    }

    function massPaymentPrint(){
        $post = $this->input->post();
        $result = array();
        $arrData = array();

        if(count($post["selectedPayment"]) > 0):
            $html = "";
            $query = array();
            
            foreach($post["selectedPayment"] as $_id):
                $query[] = $this->billing->getPaymentforPrintData($_id);
            endforeach;

            $arrData["selectedPayment"] = $query;
            $html .= $this->load->view("eforms/billing/payment/print", $arrData, true);

            $result["status"] = TRUE;
            $result["html"] = $html;
        else:
            $result["status"] = FALSE;
        endif;

        return $result;
    }

    function getPaymentforPrintData($id){
        $this->db->select("*");
        $this->db->from("hydra_billing.payments");
        $this->db->where("id",$id);
        $query = $this->db->get();
        return $query->row_array();
    }

    function viewPenalties(){
        $post = $this->input->post();
        $this->db->select("penalties,ref_no");
        $this->db->from("hydra_billing.payments");
        $this->db->where("id",$post['id']);
        $query = $this->db->get();

        $array = array();
        $array['penalties'] = unserialize($query->row_array()['penalties']);
        $array['ref_no'] = $query->row_array()['ref_no'];
        return $array;
    }

    function updateReconnectionFee(){
        $resultarray = array();
        $data = array();
        $post = $this->input->post();
        $data['amount'] = $post['reconnection_amount'];
    
        if($this->checkSetupData("hydra_billing.reconnection_fee") > 0){ // update
            $id = $post["id"];
            $data["updated_by"] = $this->getUserdata()['emp_id'];
            $data["updated_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "update";

            $this->db->where("id",$id);
            $query = $this->db->update("hydra_billing.reconnection_fee",$data);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully updated reconnection fee.";
                $this->core_layout->setEventLog("Setup - Update reconnection fee into ".$post['reconnection_amount'],"update", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error updating reconnection fee.";
                $this->core_layout->setEventLog("Setup - Error update reconnection fee into ".$post['reconnection_amount'],"update", "error", "hydra_billing", "user");
            }
        } else { // add 
            $data["created_by"] = $this->getUserdata()['emp_id'];
            $data["created_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "add";

            $query = $this->db->insert("hydra_billing.reconnection_fee",$data);
            if($query){
                $resultarray['id'] = $this->db->insert_id();
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully added reconnection fee.";
                $this->core_layout->setEventLog("Setup - Added reconnection fee ".$post['reconnection_amount'],"insert", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error adding reconnection fee.";
                $this->core_layout->setEventLog("Setup - Error adding reconnection fee ".$post['reconnection_amount'],"insert", "error", "hydra_billing", "user");
            }
        }
        return $resultarray;
    }

    function getReconnectionFee(){
        $this->db->select("*");
        $this->db->from("hydra_billing.reconnection_fee");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function numbersToWords($net_pay){
        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $tempNet = number_format($net_pay, 2, ".", "");
        $temp_net = $tempNet;
        $net_to_text = $f->format($tempNet);

        $xnet = explode(".", $tempNet);
        if(is_array($xnet) && count($xnet) > 0){
            $tempCents = "";
            $tempDecimal = end($xnet);
            $tempNetx = 0;
            $tempLabel = array();
            $tempPesoSign = "";

            foreach ($xnet as $key => $value) {
                if(floatval($value) > 0){
                    $tempLabel[] = $f->format($value);
                    if($tempDecimal !== $value){
                        $tempNetx += intval($value);
                    }
                }
            }

            if(intval($tempDecimal) > 1){ $tempCents = " centavos"; }
            else if(intval($tempDecimal) == 1){ $tempCents = " centavo"; }
            else if(intval($tempDecimal) == 0){ 
                if(intval($tempNetx) > 1){ $tempPesoSign = " pesos"; }
                else if(intval($tempNetx) == 1){ $tempPesoSign = " peso"; }
            }
            
            if(is_array($tempLabel) && count($tempLabel) > 0){
                if(intval($tempNetx) > 1){ $tempPesoSign = " pesos"; }
                else if(intval($tempNetx) == 1){ $tempPesoSign = " peso"; }
                $tempEndValue = end($tempLabel);
                foreach ($tempLabel as $kk => $vv) {
                    if($vv !== $tempEndValue){
                        $tempLabel[$kk] = $vv.$tempPesoSign;
                    }
                }
            }
            
            $net_to_text = $net_to_text.$tempPesoSign;
            
            $net_xx = $tempNetx;
            $net_to_text = implode(' and ', $tempLabel);
            
            if(!$tempCents){
                $net_to_text = $net_to_text.$tempPesoSign;
            }
            
            if(count($tempLabel) > 1){ $net_to_text .= $tempCents; }
        }

        return strtoupper($net_to_text);
    }

    function getDueForToday(){
        $resultarray = array();
        $post = $this->input->post();
        $current_date = date("Y-m-d");

        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        
        $this->db->select("b.accountno, a.total_charges, b.firstname, b.lastname, b.middlename, b.id, a.due_date");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("a.is_paid",'0');
        $this->db->where("b.is_disconnected", 0);
        $this->db->where("a.due_date <",$current_date);
        
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }
        
        $this->db->group_by("b.id");
        $this->db->order_by("a.due_date","DESC");
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();

                $data["id"] = $_query['id'];
                $data["accountno"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["total_charges"] = number_format((float)$_query["total_charges"], 2, '.', '');
                $data['due_date'] = $_query['due_date'];
                $resultarray[] = $data;
            }
        }

        $total = $this->getDueForTodayCount($current_date);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getDueForTodayCount($current_date){
        $this->db->select("b.accountno, a.total_charges");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("a.is_paid",'0');
        $this->db->where("a.due_date <",$current_date);
        $this->db->where("b.is_disconnected", 0);
        $this->db->group_by("b.id");
        $this->db->order_by("a.due_date","DESC");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function archiveBill(){
        $post = $this->input->post();
        $id = $post["id"];
        $post["status"] = '0';

        $this->db->where("id",$id);
        $query = $this->db->update('hydra_billing.bills', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Archive successfully saved.";

            $this->updateReadingBilled($post['reading_id'], "0", "Unbilled");
            $this->core_layout->setEventLog("Billing - Archived billing of ".$post["ref_no"],"archived", "success", "hydra_billing", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error archiving bill.";

            $this->core_layout->setEventLog("Billing - Error archiving billing of".$post["ref_no"],"archived", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function getSubdivisionSelect(){
        $get = $this->input->get();
        $resultarray = array();

        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `id`, `name` FROM hydra_billing.subdivision WHERE status='1' and (`name` LIKE '%{$get['q']}%') ORDER BY date_added ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT `id`, `name` FROM hydra_billing.subdivision WHERE status='1' ORDER BY date_added ASC LIMIT 10");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();

                $data["id"] = $_query["id"];
                $data["text"] = $_query["name"];
                $resultarray[] = $data;
            }
        }

        return array("results" => $resultarray);
    }

    function getSubdivisionSelectDistribution(){
        $get = $this->input->get();
        $resultarray = array();

        if (isset($get['q'])) {
            $query = $this->db->query("SELECT `id`, `name`, `meterno_raw` FROM hydra_billing.subdivision WHERE status='1' and (`name` LIKE '%{$get['q']}%') ORDER BY date_added ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT `id`, `name`, `meterno_raw` FROM hydra_billing.subdivision WHERE status='1' ORDER BY date_added ASC LIMIT 10");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();

                if($this->checkDistributionEntry($_query["id"],$_query["meterno_raw"]) == 0){
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["name"];
                    $data["meterno_raw"] = $_query["meterno_raw"];
                    $resultarray[] = $data;
                }
            }
        }

        return array("results" => $resultarray);
    }

    function checkDistributionEntry($id,$meterno_raw){
        $current_y = date("Y");
        $current_m = date("m");
        $this->db->select("id");
        $this->db->from("hydra_billing.distribution");
        $this->db->where("subdivision_id",$id);
        $this->db->where("meterno",$meterno_raw);
        $this->db->where("YEAR(reading_date)",$current_y);
        $this->db->where("MONTH(reading_date)",$current_m);
        $this->db->where("is_archive","0");
        return $this->db->get()->num_rows();
    }

    function getSubdivisionData(){
        $post = $this->input->post();
        $resultarray = array();
        $this->db->select("name,id");
        $this->db->from("hydra_billing.subdivision");
        $this->db->where("status","1");
        $this->db->order_by("date_added","DESC");
        $query = $this->db->get();
        foreach ($query->result_array() as $_query) {
            $data = array();
            $data["id"] = $_query["id"];
            $data["name"] = $_query["name"];
            $resultarray[] = $data;
        }
        return $resultarray;
    }

    function dashboardLineGraph_totalPayment(){
        $post = $this->input->post();
        $subdivision_id = $post['subdivision_id'];
        $year = $post["filter_year"];
        $items = array();
        foreach ($this->months as $_month) {
            $data = array();
            $data["id"] = $subdivision_id;
            $data["total_payment"] = $this->getPaymentData($_month["index"], $year, $subdivision_id);
            array_push($items, $data);
        }
        return $items;
    }

    function getPaymentData($month, $year, $subd_id){
        $this->db->select("SUM(a.received_amount) AS total_payment");
        $this->db->from("hydra_billing.payments a");
        $this->db->join("hydra_billing.accounts b", "a.account_id = b.id", "LEFT");
        $this->db->where("a.is_archive",'0');
        $this->db->where("b.is_archive",'0');
        $this->db->where("b.subdivision_id",$subd_id);
        $this->db->where("MONTH(a.payment_date)",$month);
        $this->db->where("YEAR(a.payment_date)",$year);
        $query = $this->db->get()->row_array();
        return $query['total_payment'] ? number_format($query['total_payment'],2, '.', '') : 0;
    }

    function dashboardAnalytics_TopConsumer(){
        $post = $this->input->post();
        $year = $post['year'];
        $month = $post['month'];
        $resultarray = array();

        $this->db->select("UPPER(CONCAT(b.firstname,' ', b.lastname)) AS name, a.reading, a.id AS reading_id, a.account_id, a.reading_date, a.meterno");
        $this->db->from("hydra_billing.readings a");
        $this->db->join("hydra_billing.accounts b", "a.account_id = b.id", "LEFT");
        $this->db->where("b.is_archive",'0');
        $this->db->where("a.is_archived", "0");
        $this->db->where("MONTH(a.reading_date)",$month);
        $this->db->where("YEAR(a.reading_date)",$year);
        $this->db->group_by("name");
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["name"] = $_query['name'];
                $data["total_usage"] = number_format($this->calculateUsage($_query['account_id'], $_query['reading_date'], $_query['reading'], $_query['meterno']),2, '.', '');
                $resultarray[] = $data;
            }
            $arr = array_column($resultarray, 'total_usage');
            array_multisort($arr, SORT_DESC, $resultarray);
            $resultarray = array_slice($resultarray, 0, 5);
        }
        
        return $resultarray;
    }

    function calculateUsage($account_id, $reading_date, $reading, $meterno){
        $this->db->select("reading");
        $this->db->from("hydra_billing.readings");
        $this->db->where("reading_date <",$reading_date);
        $this->db->where("account_id",$account_id);
        $this->db->where("meterno",$meterno);
        $this->db->where("is_archived", "0");
        $this->db->order_by("reading_date","DESC");
        $this->db->limit(1);
        $query = $this->db->get()->row_array();
        $previous_reading = (is_array($query) && array_key_exists('reading', $query) && $query['reading'] != null) ? $query['reading'] : 0;
        return $reading - $previous_reading;
    }

    function dashboardConsumerVsSupplier(){
        $post = $this->input->post();
        $subdivision_id = $post['subdivision_id'];
        $year = $post["filter_year"];
        $name = $post["name"];
        $items = array();
        foreach ($this->months as $_month) {
            $data = array();
            $data["id"] = $subdivision_id;
            $data["total_usage"] = $name=='Supplier' ? $this->getSupplierUsage($_month["index"], $year, $subdivision_id) : $this->getConsumerUsage($_month["index"], $year, $subdivision_id);
            array_push($items, $data);
        }

        return $items;
    }

    function getSupplierUsage($month, $year, $subd_id){
        $total_reading = 0;
        $this->db->select("distribute, subdivision_id, id, reading_date, meterno");
        $this->db->from("hydra_billing.distribution");
        $this->db->where("MONTH(reading_date)",$month);
        $this->db->where("YEAR(reading_date)",$year);
        $this->db->where("is_archive", "0");
        if($subd_id != 'all'){ $this->db->where("subdivision_id",$subd_id); }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $_query = $query->row_array();
            $total_reading = $this->calculateUsageDistribution($_query['subdivision_id'], $_query['reading_date'], $_query['distribute'], $_query['meterno']);
        }
        return $total_reading;
    }

    function calculateUsageDistribution($subd_id, $reading_date, $distribute, $meterno){
        $this->db->select("distribute");
        $this->db->from("hydra_billing.distribution");
        $this->db->where("reading_date <", $reading_date);
        $this->db->where("subdivision_id",$subd_id);
        $this->db->where("meterno",$meterno);
        $this->db->where("is_archive", "0");
        $this->db->order_by("reading_date","DESC");
        $query = $this->db->get()->row_array();
        $previous_distribute = $query['distribute'];
        return $distribute - $previous_distribute;
    }

    function getConsumerUsage($month, $year, $subd_id){
        $total_reading = 0;
        $this->db->select("a.reading, a.id as reading_id, a.account_id, a.reading_date, a.meterno");
        $this->db->from("hydra_billing.readings a");
        $this->db->join("hydra_billing.accounts b", "a.account_id = b.id", "LEFT");
        $this->db->where("b.is_archive",'0');
        $this->db->where("a.is_archived", "0");
        $this->db->where("MONTH(a.reading_date)",$month);
        $this->db->where("YEAR(a.reading_date)",$year);
        if($subd_id != 'all'){ $this->db->where("b.subdivision_id",$subd_id); }
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $total_reading = $total_reading + $this->calculateUsage($_query['account_id'], $_query['reading_date'], $_query['reading'], $_query['meterno']);
            }
        }
        return number_format($total_reading,2, '.', '');
    }

    function dashboardLineGraph_TotalUsagePerSubdivision(){
        $post = $this->input->post();
        $subdivision_id = $post['subdivision_id'];
        $year = $post["filter_year"];
        $items = array();
        foreach ($this->months as $_month) {
            $data = array();
            $data["id"] = $subdivision_id;
            $data["total_usage"] = $this->getUsageData($_month["index"], $year, $subdivision_id);
            array_push($items, $data);
        }
        return $items;
    }

    function getUsageData($month, $year, $subd_id){
        $total_reading = 0;
        $this->db->select("a.reading, a.id as reading_id, a.account_id, a.reading_date, a.meterno");
        $this->db->from("hydra_billing.readings a");
        $this->db->join("hydra_billing.accounts b", "a.account_id = b.id", "LEFT");
        $this->db->where("b.is_archive",'0');
        $this->db->where("a.is_archived", "0");
        $this->db->where("MONTH(a.reading_date)",$month);
        $this->db->where("YEAR(a.reading_date)",$year);
        $this->db->where("b.subdivision_id",$subd_id);
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $total_reading = $total_reading + $this->calculateUsage($_query['account_id'], $_query['reading_date'], $_query['reading'], $_query['meterno']);
            }
        }
        return number_format($total_reading,2, '.', '');
    }

    function dashboardAnalytics_TotalUsagePerSubdivision(){
        $post = $this->input->post();
        $resultarray = array();
        $this->db->select("a.name, SUM(c.usage) AS total_usage, a.id AS subd_id");
        $this->db->from("hydra_billing.subdivision a");
        $this->db->join("hydra_billing.accounts b", "b.subdivision_id = a.id AND b.status='1'", "LEFT");
        $this->db->join("hydra_billing.bills c", "b.id = c.account_id AND c.status='1'", "LEFT");
        $this->db->where("a.status","1");
        $this->db->where("YEAR(c.created_at)",$post["filter_year"]);
        $this->db->group_by("a.name");
        $query = $this->db->get();
        foreach ($query->result_array() as $_query) {
            $data = array();
            $data["name"] = $_query["name"];
            $data["subd_id"] = $_query["subd_id"];
            $data["total_usage"] = $_query["total_usage"] ? $_query["total_usage"] : 0;
            $resultarray[] = $data;
        }
        return $resultarray;
    }

    function dashboardAnalytics(){
        $post = $this->input->post();
        $current_date = $post['current_date'];
        $current_month = $post['filter_month'];
        $current_year = $post['filter_year'];

        $upcoming_duedate = array("status"=>"Upcoming Due", "count"=>$this->getUpcomingDueBillCount($current_date, $current_year, $current_month), "color"=>"#2196f3");
        $overdue = array("status"=>"Overdue", "count"=>$this->getOverdueBillCount($current_date, $current_year, $current_month), "color"=>"#f44336");
        $total_number_entries = array("status"=>"Bill Entries", "count"=>$this->getEntriesBillCount($current_year, $current_month), "color"=>"#673ab7");
        $total_disconnected = array("status"=>"Disconnected", "count"=>$this->getDisconnectedCustomerCount($current_year, $current_month), "color"=>"#607d8b");

        $data = array($upcoming_duedate, $overdue, $total_number_entries, $total_disconnected);
        $results = json_decode(json_encode($data));
        return $results;
    }

    function getDisconnectedCustomer(){
        $resultarray = array();
        $post = $this->input->post();
        $current_date = date("Y-m-d");
        $current_year = date("Y");
        $current_month = date("m");

        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        
        $this->db->select("firstname, middlename, lastname, disconnect_date, accountno");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("is_disconnected",'1');
        $this->db->where("MONTH(disconnect_date)", $current_month);
        $this->db->where("YEAR(disconnect_date)", $current_year);
        
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();

                $data["customer_name"] = $_query['firstname'].' '.$_query['middlename'][0].' '.$_query['lastname'];
                $data["disconnect_date"] = date('Y-m-d g:i A', strtotime($_query['disconnect_date']));
                $data["accountno"] = $_query['accountno'];
                $resultarray[] = $data;
            }
        }

        $total = $this->getDisconnectedCustomerCount($current_year, $current_month);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getDisconnectedCustomerCount($current_year, $current_month){
        $this->db->select("firstname, middlename, lastname, disconnect_date");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("is_disconnected",'1');
        // $this->db->where("MONTH(disconnect_date)", $current_month);
        $this->db->where("YEAR(disconnect_date)", $current_year);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    function getEntriesBill(){
        $resultarray = array();
        $post = $this->input->post();
        $current_date = date("Y-m-d");
        $current_year = date("Y");
        $current_month = date("m");

        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        
        $this->db->select("b.firstname, b.middlename, b.lastname, b.accountno, a.created_at, a.ref_no");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("b.is_disconnected",'0');
        $this->db->where("MONTH(a.created_at)", $current_month);
        $this->db->where("YEAR(a.created_at)", $current_year);
        
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();

                $data["customer_name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["created_at"] = date('Y-m-d g:i A', strtotime($_query['created_at']));
                $data["accountno"] = $_query['accountno'];
                $data["ref_no"] = $_query['ref_no'];
                $resultarray[] = $data;
            }
        }

        $total = $this->getEntriesBillCount($current_year, $current_month);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getEntriesBillCount($current_year, $current_month){
        $this->db->select("b.firstname, b.middlename, b.lastname, b.accountno, a.created_at, a.ref_no");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("b.is_disconnected",'0');
        // $this->db->where("MONTH(a.created_at)", $current_month);
        $this->db->where("YEAR(a.created_at)", $current_year);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getOverdueBill(){
        $resultarray = array();
        $post = $this->input->post();
        $current_date = date("Y-m-d");
        $current_year = date("Y");
        $current_month = date("m");

        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

        $this->db->select("b.firstname, b.middlename, b.lastname, b.accountno, a.due_date, a.ref_no");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("a.is_paid", "0");
        $this->db->where("due_date <", $current_date);
        $this->db->where("MONTH(due_date)", $current_month);
        $this->db->where("YEAR(due_date)", $current_year);

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();

                $data["customer_name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["due_date"] = date('Y-m-d g:i A', strtotime($_query['due_date']));
                $data["accountno"] = $_query['accountno'];
                $data["ref_no"] = $_query['ref_no'];
                $resultarray[] = $data;
            }
        }

        $total = $this->getOverdueBillCount($current_date, $current_year, $current_month);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getOverdueBillCount($current_date, $current_year, $current_month){
        $this->db->select("b.firstname, b.middlename, b.lastname, b.accountno, a.due_date, a.ref_no");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("a.is_paid", "0");
        $this->db->where("due_date <", $current_date);
        // $this->db->where("MONTH(due_date)", $current_month);
        $this->db->where("YEAR(due_date)", $current_year);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getBillUsage(){
        $resultarray = array();
        $post = $this->input->post();
        $previous_month = date("Y-m", strtotime("-1 months")).'-15';
        $previous2_month = date("Y-m", strtotime("-2 months")).'-15';

        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

        $this->db->select("b.firstname, b.middlename, b.lastname, b.accountno, a.due_date, a.ref_no, a.usage");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("a.status",'1');
        $this->db->where("a.billing_from >= '$previous2_month'");
        $this->db->where("a.billing_to <= '$previous_month'");

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();

                $data["customer_name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["due_date"] = date('Y-m-d g:i A', strtotime($_query['due_date']));
                $data["accountno"] = $_query['accountno'];
                $data["ref_no"] = $_query['ref_no'];
                $data["usage"] = $_query['usage'];
                $resultarray[] = $data;
            }
        }

        $total = $this->getBillUsageCount($previous2_month, $previous_month);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getBillUsageCount($previous2_month, $previous_month){
        $this->db->select("b.firstname, b.middlename, b.lastname, b.accountno, a.due_date, a.ref_no, a.usage");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("a.status",'1');
        $this->db->where("a.billing_from >= '$previous2_month'");
        $this->db->where("a.billing_to <= '$previous_month'");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getUpcomingDueBill(){
        $resultarray = array();
        $post = $this->input->post();
        $current_date = date("Y-m-d");
        $current_year = date("Y");
        $current_month = date("m");

        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

        $this->db->select("b.firstname, b.middlename, b.lastname, b.accountno, a.due_date, a.ref_no");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("a.is_paid", "0");
        $this->db->where("due_date >", $current_date);
        $this->db->where("MONTH(due_date)", $current_month);
        $this->db->where("YEAR(due_date)", $current_year);

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();

                if($this->dayDifference($_query['due_date'], $current_date) <= 5){
                    $data["customer_name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                    $data["due_date"] = date('Y-m-d g:i A', strtotime($_query['due_date']));
                    $data["accountno"] = $_query['accountno'];
                    $data["ref_no"] = $_query['ref_no'];
                    $resultarray[] = $data;
                }
            }
        }

        $total = $this->getUpcomingDueBillCount($current_date, $current_year, $current_month);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getUpcomingDueBillCount($current_date, $current_year, $current_month){
        $resultarray = array();
        $this->db->select("b.firstname, b.middlename, b.lastname, b.accountno, a.due_date, a.ref_no");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("a.is_paid", "0");
        $this->db->where("due_date >", $current_date);
        // $this->db->where("MONTH(due_date)", $current_month);
        $this->db->where("YEAR(due_date)", $current_year);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                if($this->dayDifference($_query['due_date'], $current_date) <= 5){
                    $data["ref_no"] = $_query['ref_no'];
                    $resultarray[] = $data;
                }
            }
        }
        return count($resultarray);
    }

    function savePrintLogs(){
        $post = $this->input->post();
        $resultarray = array();

        if(count($post["selectedPayment"]) > 0){

            foreach($post["selectedPayment"] as $_id){
                $query[] = $this->billing->getPaymentforPrintData($_id);
                $resultarray["status"] = TRUE;
            }

            foreach($query as $q){
                $this->core_layout->setEventLog("Payments - Print ".$q['ref_no'],"print", "success", "hydra_billing", "user");
            }
        }

        return $resultarray;
    }

    function getEventLogs(){
        $resultarray = array();
        $post = $this->input->post();

        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        
        $filterFields = array("a.log_message","a.user_action","a.type","b.firstname","b.middlename","b.lastname");

        $this->db->select("a.log_message, a.user_action, a.type, a.created_at, b.firstname, b.middlename, b.lastname");
        $this->db->from("hydra_billing.user_logs_event a");
        $this->db->join("gccmaster.tblemployees b", "b.id = a.user_id", "LEFT");
        
        if($search != ""){
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

        $this->db->order_by("a.created_at","DESC");
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();

                $data["checkbox"] = '';
                $data["log_message"] = $_query['log_message'];
                $data["user"] = $_query["firstname"]." ".$_query["lastname"];
                $data["user_action"] = $_query["user_action"];
                $data["type"] = $_query["type"];
                $data["created_at"] = date('Y-m-d g:i A', strtotime($_query["created_at"]));
                $resultarray[] = $data;
            }
        }

        $total = $this->getEventLogsCount();
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getEventLogsCount(){
        $this->db->select("a.id");
        $this->db->from("hydra_billing.user_logs_event a");
        $this->db->join("gccmaster.tblemployees b", "b.id = a.user_id", "LEFT");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getSubdivision(){
        $resultarray = array();
        $post = $this->input->post();

        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        
        $filterFields = array("a.name","a.address","a.description","a.created_by","a.updated_by","a.updated_date","a.status","a.date_added", "b.firstname", "b.middlename", "b.lastname");

        $this->db->select("a.*, b.firstname, b.middlename, b.lastname");
        $this->db->from("hydra_billing.subdivision a");
        $this->db->where("a.status", "1");
        $this->db->join("gccmaster.tblemployees b", "b.id = a.created_by", "LEFT");
        
        if($search != ""){
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

        $this->db->order_by("date_added","DESC");
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["checkbox"] = '';
                $data["id"] = $_query["id"];
                $data["name"] = $_query['name'];
                $data["meterno"] = $_query['meterno'];
                $data["address"] = $_query["address"];
                $data["description"] = $_query["description"];
                $data["created_by"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
                $data["updated_by"] = $_query["updated_by"];
                $data["updated_date"] = $_query["updated_date"];
                $data["status"] = $_query["status"];
                $data["date_added"] = date('Y-m-d g:i A', strtotime($_query["date_added"]));
                $resultarray[] = $data;
            }
        }

        $total = $this->getSubdivisionCount();
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getSubdivisionCount(){
        $this->db->select("a.*, b.firstname, b.middlename, b.lastname");
        $this->db->from("hydra_billing.subdivision a");
        $this->db->join("gccmaster.tblemployees b", "b.id = a.created_by", "LEFT");
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    function saveSubdivision(){
        $post = $this->input->post();
        $resultarray = array();
        $post["created_by"] = $this->getUserdata()['emp_id'];
        $post["date_added"] = date("Y-m-d H:i:s");
        $post["meterno_raw"] = str_replace(" ", "", str_replace("-", "", $post["meterno"]));

        if($this->checkSubdivisionExist($post['name'])){
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Name is already exist.";
        } else {
            $query = $this->db->insert("hydra_billing.subdivision",$post);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully Save.";
                $this->core_layout->setEventLog("Subdivision - Added ".$post['name'],"insert", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error saving subdivision.";
                $this->core_layout->setEventLog("Subdivision - Added ".$post['name'],"insert", "error", "hydra_billing", "user");
            }
        }

        return $resultarray;
    }

    function checkSubdivisionExist($name){
        $this->db->where('name',$name);
        $this->db->where('status',"1");
        $query = $this->db->get('hydra_billing.subdivision');
        if ($query->num_rows() > 0){
            return true;
        } else{
            return false;
        }
    }

    function getSubdivisionDetails(){
        $post = $this->input->post();
        $this->db->select("a.*, b.firstname, b.middlename, b.lastname");
        $this->db->from("hydra_billing.subdivision a");
        $this->db->join("gccmaster.tblemployees b", "b.id = a.created_by", "LEFT");
        $this->db->where("a.id",$post["id"]);
        $query = $this->db->get();
        return array("data"=>$query->row_array());
    }

    function updateSubdivisionDetails(){
        $resultarray = array();
        $post = $this->input->post();
        $id = $post["id"];
        $post["updated_by"] = $this->getUserdata()['emp_id'];
        $post["updated_date"] = date("Y-m-d H:i:s");

        $this->db->where("id",$id);
        $query = $this->db->update("hydra_billing.subdivision",$post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Subdivision successfully updated.";
            $this->core_layout->setEventLog("Subdivision - Updated subdivision details","update", "success", "hydra_billing", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error updating bill.";
            $this->core_layout->setEventLog("Subdivision - Error updating subdivision details","update", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function archiveSubdivision(){
        $post = $this->input->post();
        $id = $post["id"];
        $post["status"] = '0';

        $this->db->where("id",$id);
        $query = $this->db->update('hydra_billing.subdivision', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Archive successfully saved.";
            $this->core_layout->setEventLog("Subdivision - Archived ".$post['name'],"archived", "success", "hydra_billing", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error archiving bill.";
            $this->core_layout->setEventLog("Subdivision - Archived ".$post['name'],"archived", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function saveExportLogs(){
        $post = $this->input->post();
        $this->core_layout->setEventLog($post['export_'],"export", "success", "hydra_billing", "user");
    }

    function water_usage(){
        $post = $this->input->post();
        $current_year = date("Y");
        $current_month = date("m");
        return array("monthly"=>$this->getWaterUsageMonth($post['id'], $current_month, $current_year), "yearly"=>$this->getWaterUsageYear($post['id'], $current_year), 
                    "current_month"=>$current_month, "current_year"=>$current_year);
    }

    function getWaterUsageMonth($id, $current_month, $current_year){
        $total_usage = 0;
        $this->db->select("a.reading, a.id AS reading_id, a.account_id, a.reading_date, a.meterno");
        $this->db->from("hydra_billing.readings a");
        $this->db->join("hydra_billing.accounts b", "a.account_id = b.id", "LEFT");
        $this->db->where("b.is_archive",'0');
        $this->db->where("a.is_archived", "0");
        $this->db->where("MONTH(a.reading_date)",$current_month);
        $this->db->where("YEAR(a.reading_date)",$current_year);
        if($id != 'all'){ $this->db->where("b.subdivision_id",$id); }
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $total_usage = $total_usage + $this->calculateUsage($_query['account_id'], $_query['reading_date'], $_query['reading'], $_query['meterno']);
            }
        }
        return number_format($total_usage,2, '.', '');
    }

    function getWaterUsageYear($id, $current_year){
        $total_usage = 0;
        $this->db->select("a.reading, a.id AS reading_id, a.account_id, a.reading_date, a.meterno");
        $this->db->from("hydra_billing.readings a");
        $this->db->join("hydra_billing.accounts b", "a.account_id = b.id", "LEFT");
        $this->db->where("b.is_archive",'0');
        $this->db->where("a.is_archived", "0");
        $this->db->where("YEAR(a.reading_date)",$current_year);
        if($id != 'all'){ $this->db->where("b.subdivision_id",$id); }
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $total_usage = $total_usage + $this->calculateUsage($_query['account_id'], $_query['reading_date'], $_query['reading'], $_query['meterno']);
            }
        }
        return number_format($total_usage,2, '.', '');
    }

    function water_usage_subdivision(){
        $this->db->select("*");
        $this->db->from("hydra_billing.subdivision");
        $this->db->where("status",'1');
        $query = $this->db->get()->result();
        return $query;
    }

    function get_PaymentDetails(){
        $post = $this->input->post();
        $this->db->select("a.*, b.firstname, b.middlename, b.lastname, b.meterno, b.block, b.lot, b.accountno, c.total_charges, c.ref_no as bill_ref_no,
                            CONCAT(d.firstname, ' ',d.lastname) as created_by, a.acknowledgement_receipt as acknowledgement_receipt");
        $this->db->from("hydra_billing.payments a");
        $this->db->join("hydra_billing.accounts b","b.id = a.account_id", "LEFT");
        $this->db->join("hydra_billing.bills c","c.id = a.bill_id", "LEFT");
        $this->db->join("gccmaster.tblemployees d","d.id = a.created_by", "LEFT");
        $this->db->where("a.id",$post["payment_id"]);
        $query = $this->db->get()->row_array();

        $data = array();
        $data["id"] = $query["id"];
        $data["ref_no"] = $query["ref_no"];
        $data["payment_type"] = $query["payment_type"];
        $data["payment_details"] = $query["payment_details"];
        $data["received_amount"] = $query["received_amount"];
        $data["payment_date"] = $query["payment_date"];
        $data["net_payment"] = $query["net_payment"];
        $data["sub_total"] = $query["sub_total"];
        $data["penalties"] = unserialize($query["penalties"]);
        $data["bill_ref_no"] = $query["bill_ref_no"];
        $data["accountno"] = $query["accountno"];
        $data["balance_covered"] = $query["balance_covered"];
        $data["reconnection_fee"] = $query["reconnection_fee"];
        $data["total_charges"] = $query["total_charges"];
        $data["lot_no"] = $query["lot"];
        $data["meter_no"] = $query["meterno"];
        $data["block_no"] = $query["block"];
        $data["is_penalty"] = $query["is_penalty"];
        $data["created_by"] = $query["created_by"];
        $data["acknowledgement_receipt"] = $query["acknowledgement_receipt"];
        $data["customer_name"] = $this->nameFormat($query["firstname"], $query["middlename"], $query["lastname"]);
        $data["created_date"] = date('Y-m-d g:i A', strtotime($query["created_date"]));

        return $data;
    }

    function getDistribution(){
        $resultarray = array(); 
        $post = $this->input->post();
        $current_date = date("Y-m-d");

        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        
        $filterFields = array("a.distribute","a.reading_date", "b.firstname", "b.lastname", "c.name");

        $this->db->select("a.*, CONCAT(b.firstname,' ',b.lastname) as name, c.name as subdivision_name, a.created_date");
        $this->db->from("hydra_billing.distribution a");
        $this->db->join("gccmaster.tblemployees b", "b.id = a.created_by", "LEFT");
        $this->db->join("hydra_billing.subdivision c", "c.id = a.subdivision_id", "LEFT");
        $this->db->where("a.is_archive", "0");
        
        if($search != ""){
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

        $this->db->order_by("a.created_date","DESC");
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["id"];
                $data["meterno"] = $_query["meterno"];
                $data["distribute"] = number_format($_query["distribute"],2, '.', '');
                $data["name"] = $_query['name'];
                $data["subdivision_name"] = $_query['subdivision_name'];
                $data["reading_date"] = $_query["reading_date"];

                if($this->authenticate->getRoleId() == "1"){
                    $data["isArchiveHide"] = false;
                } else {
                    $data["isArchiveHide"] = $current_date > date('Y-m-d', strtotime($_query["created_date"])) ? true : false;
                }

                $resultarray[] = $data;
            }
        }

        $total = $this->getDistributionCount();
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getDistributionCount(){
        $this->db->select("id");
        $this->db->from("hydra_billing.distribution");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function get_subdv_total_comsume_per_mos($year) {
        $res = [];
        $mos = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        $this->db->select('id, name');
        $this->db->from('hydra_billing.subdivision');
        $query = $this->db->get();
        
        foreach ($query->result() as $row) {
            $subdv_id = $row->id;
            $res[$subdv_id]['subdv_name'] = $row->name;
            $res[$subdv_id]['subdv_id'] = $row->id;
            $res[$subdv_id]['total_reading_per_mos'] = $this->get_total_by_subdv_mos($subdv_id, $mos, $year);
        }

        return $res;
    }

    function get_total_by_subdv_mos($subdv_id, $mos, $year) {
        $report = [];
        
        foreach($mos as $m) {
            $report[$m] = '0.00';
        }

        $this->db->select("MONTH(r.reading_date) as month, SUM(r.reading) as total_reading");
        $this->db->from("hydra_billing.readings as r");
        $this->db->join("hydra_billing.accounts as a", "r.account_id = a.id", "LEFT");
        $this->db->where("r.is_archived", 0);
        $this->db->where("a.subdivision_id", $subdv_id);
        $this->db->where("YEAR(r.reading_date)", $year);
        $this->db->group_by("MONTH(r.reading_date)");
        $this->db->order_by("MONTH(r.reading_date)");
        $query = $this->db->get();
        $result = $query->result();

        foreach($result as $row) {
            $month = $mos[$row->month - 1];

            if(isset($row->total_reading) && $row->total_reading) {
                $report[$month] = number_format(floatval($row->total_reading), 2, '.' , '');
            }
        }

        return $report;
    }

    function get_total_reading_per_mos($year, $mos) {
        $report = [];

        foreach($mos as $m) {
            $report[$m] = '0.00';
        }

        $this->db->select("MONTH(r.reading_date) as month, SUM(r.reading) as total_reading");
        $this->db->from("hydra_billing.readings as r");
        $this->db->join("hydra_billing.accounts as a", "r.account_id = a.id", "LEFT");
        $this->db->where("r.is_archived", 0);
        $this->db->where("YEAR(r.reading_date)", $year);
        $this->db->group_by("MONTH(r.reading_date)");
        $this->db->order_by("MONTH(r.reading_date)");
        $query = $this->db->get();
        $result = $query->result();

        foreach($result as $row) {
            $month = $mos[$row->month - 1];

            if(isset($row->total_reading) && $row->total_reading !== '') {
                $report[$month] = number_format(floatval($row->total_reading), 2, '.' , '');
            }
        }

        return $report;
    }

    function get_monthly_differences($year, $mos) {
        $report = [];

        foreach($mos as $m) {
            $report[$m] = '0.00';
        }

        $this->db->select("MONTH(r.reading_date) as month, SUM(r.reading) as total_reading");
        $this->db->from("hydra_billing.readings as r");
        $this->db->join("hydra_billing.accounts as a", "r.account_id = a.id", "LEFT");
        $this->db->where("r.is_archived", 0);
        $this->db->where("YEAR(r.reading_date)", $year);
        $this->db->group_by("MONTH(r.reading_date)");
        $this->db->order_by("MONTH(r.reading_date)"); // Ensure data is ordered by month
        $query = $this->db->get();
        $result = $query->result();
      
        $previous_reading = 0;
        
        foreach($result as $row) {
            $month = $mos[$row->month - 1];
            $current_reading = floatval($row->total_reading);
            $difference = number_format($current_reading - $previous_reading, 2, '.', '');
            
            if(isset($current_reading) && $current_reading !== '') {
                $report[$month] = $difference;
            }
      
            $previous_reading = $current_reading;
        }
      
        return $report;
    }

    function get_subdv_percentage($total_reading_per_mos, $total_consumption, $mos) {
        $res = [];

        foreach ($mos as $m) {
            $r = isset($total_reading_per_mos[$m]) ? $total_reading_per_mos[$m] : 0;
            $c = isset($total_consumption[$m]) ? $total_consumption[$m] : 0;

            if ($c != 0) {
                $res[$m] = number_format(($r / $c) * 100, 2, '.', '') . '%';
            } else {
                $res[$m] = '0.00%';
            }
        }

        return $res;
    }

    function get_distribution_reports2() {
        $mos = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        $post = $this->input->post();
        $year = (isset($post['year']) && $post['year']) ? $post['year'] : date("Y");

        $total_mos_subdv = $this->get_subdv_total_comsume_per_mos($year);
        $total_reading_per_mos = $this->get_total_reading_per_mos($year, $mos);
        $monthly_differences = $this->get_monthly_differences($year, $mos);
        $total_consumption = $this->get_total_per_mos($year, $mos);
        $percentage = $this->get_subdv_percentage($total_reading_per_mos, $total_consumption, $mos);

        return array(
            "total_mos_subdv" => $total_mos_subdv,
            "total_reading_per_mos" => $total_reading_per_mos,
            "monthly_differences" => $monthly_differences,
            "total_consumption" => $total_consumption,
            "percentage" => $percentage
        );
    }



    // ==========================================================================

    function get_distribution_reports() {
        $arrData = [];

        $mos = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        $resultarray = array(); 
        $post = $this->input->post();
        $current_date = date("Y-m-d");

        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        $year = (isset($post["year"]) && $post["year"]) ? $post["year"] : date("Y");
        
        // $filterFields = array("a.distribute","a.reading_date", "b.firstname", "b.lastname", "c.name");

        $this->db->select('id, name');
        $this->db->from('hydra_billing.subdivision');
        $this->db->where('status', 1);

        if($search != ""){
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
    
        $this->db->order_by("date_added","DESC");

        $query = $this->db->get();

        if($query->num_rows() > 0) {
            foreach($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["name"] = $_query["name"];
                $data["report"] = $this->get_distribution_report_per_mos($_query["id"], $year, $mos);

                if ($this->authenticate->getRoleId() == "1") {
                    $data["isArchiveHide"] = false;
                } else {
                    $data["isArchiveHide"] = $current_date > data('Y-m-d', strtotime($_query["date_added"])) ? true : false;
                }

                $resultarray[] = $data;
            }
        }

        $total = $this->getSubdivisionCount();
        return array(
            "data" => $resultarray, 
            "total_per_mos" => $this->get_total_per_mos($year, $mos),
            "recordsTotal" => $total, 
            "recordsFiltered" => $total
        );
    }

    function get_distribution_report_per_mos($id, $year, $mos) {
        $this->db->select("MONTH(reading_date) as month, distribute");
        $this->db->from("hydra_billing.distribution");
        $this->db->where('is_archive', 0);
        $this->db->where('subdivision_id', $id);
        $this->db->where('YEAR(reading_date)', $year);
        $query = $this->db->get();
        $result = $query->result_array();

        $report = [];
        foreach ($result as $row) {
            $month = $mos[$row['month'] - 1];
            $report[] = [$month => number_format($row['distribute'], 2, '.', '')];
        }

        return $report;
    }

    function get_total_per_mos($year, $mos) {
        $report = [];

        foreach($mos as $m) {
            $report[$m] = '0.00';
        }

        $this->db->select("MONTH(reading_date) as month, SUM(distribute) as total");
        $this->db->from("hydra_billing.distribution");
        $this->db->where('is_archive', 0);
        $this->db->where('YEAR(reading_date)', $year);
        $this->db->group_by('MONTH(reading_date)');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->result_array();

            foreach ($result as $row) {
                $month = $mos[$row['month'] - 1];
    
                if(isset($row['total']) && $row['total'] !== '') {
                    $report[$month] = number_format(floatval($row['total']), 2, '.', '');
                }
            }
        }

        return $report;
    }

    function getCustomerSoaDetails($id){
        $post = $this->input->post();
        $arrData = array();
        $accountData = array();
        $billArr = array();
        $this->db->select("CONCAT(a.firstname,' ',a.lastname) as account_name, a.accountno, a.meterno, a.is_disconnected");
        $this->db->from("hydra_billing.accounts a");
        $this->db->where("a.id", $id);
        $query = $this->db->get();
        $accountData['acctData'] = $query->result_array();
        $arrData['acctData'] = $accountData;

        $this->db->select("bill.total_charges, payments.net_payment, bill.ref_no as ref_no, payments.payment_date ");
        $this->db->from("hydra_billing.bills bill");
        $this->db->join("hydra_billing.payments payments","payments.bill_id = bill.id", "LEFT");
        $this->db->where("bill.account_id", $id);
        if(isset($post['startDate'])){
            $this->db->where("bill.billing_from >=", $post['startDate']);
            $this->db->where("bill.billing_to <=", $post['endDate']);
        }
        $this->db->order_by("bill.billing_to", "DESC");
        $billQuery = $this->db->get();
        foreach($billQuery->result() as $billData){
            
            $billingData = array();
            $billingData['ref_no'] = $billData->ref_no;
            $billingData['total_charges'] = ($billData->total_charges)? "₱ ".$billData->total_charges : "";
            $billingData['net_payment'] = ($billData->net_payment)? "₱ ".($billData->net_payment) : "";
            $billingData['payment_date'] = $billData->payment_date;
            $billArr[] = $billingData;
        }
        $arrData['billData'] = $billArr;
         
        return $arrData;
    }

    function saveDistribution(){
        $current_date = date("Y-m-d H:i:s");
        $post = $this->input->post();
        $resultarray = array();
        $distribute = preg_replace('/[^0-9a-zA-Z.]/', '', $post['distribute']);

        $array = array();
        $array["subdivision_id"] = $post['subdivision_id'];
        $array["meterno"] = $post['meterno_raw'];
        $array["reading_date"] = $post['reading_date'];
        $array["distribute"] = $distribute;
        $array["created_date"] = $current_date;
        $array["created_by"] = $this->getUserdata()['emp_id'];

        $year = date("Y", strtotime($post['reading_date']));
        $month = date("m", strtotime($post['reading_date']));
        $subdv_id = $post['subdivision_id'];

        $validate = $this->validate_duplicate_distribution($subdv_id, $year, $month); // Return True or False

        if ($validate) {
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "There's already a reading this month for this subdivision";
        } else {
            if($this->getRecentDistribution($post['subdivision_id'], $post['meterno_raw']) > $distribute){
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Current reading must equal or exceed to previous reading.";
            } else {
                $query = $this->db->insert("hydra_billing.distribution",$array);
                if($query){
    
                    $resultarray["status"] = TRUE;
                    $resultarray["msg"] = "Successfully Save.";
                    $this->core_layout->setEventLog("Distribution - Added ".$post['subd_name']." distribution of ".$post['distribute'],"insert", "success", "hydra_billing", "user");
                }else{
                    $resultarray["status"] = FALSE;
                    $resultarray["msg"] = "Error saving distribution.";
                    $this->core_layout->setEventLog("Distribution - Added ".$post['subd_name']." distribution of ".$post['distribute'],"insert", "error", "hydra_billing", "user");
                }
            }
        }

        return $resultarray;
    }

    function validate_duplicate_distribution($subdv_id, $year, $month) {
        $this->db->select('subdivision_id, reading_date');
        $this->db->from('hydra_billing.distribution as d');
        $this->db->where('YEAR(reading_date)', $year);
        $this->db->where('MONTH(reading_date)', $month);
        $this->db->where('subdivision_id', $subdv_id);
        $this->db->where('is_archive', 0);
        $query = $this->db->get();

        if($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getRecentDistribution($subdivision_id, $meterno){
        $this->db->select("distribute");
        $this->db->from("hydra_billing.distribution");
        $this->db->where("subdivision_id",$subdivision_id);
        $this->db->where("meterno",$meterno);
        $this->db->where("is_archive","0");
        $this->db->order_by('reading_date', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        $reading = $query->row_array()["distribute"];
        return $reading ? $reading : "0";
    }

    function getDistributionDetails(){
        $post = $this->input->post();
        $this->db->select("a.*, b.name as subdivision");
        $this->db->from("hydra_billing.distribution a");
        $this->db->join("hydra_billing.subdivision b", "b.id = a.subdivision_id", "LEFT");
        $this->db->where("a.id",$post["id"]);
        $query = $this->db->get();
        return $query->row_array();
    }

    function updateDistributionDetails(){
        $resultarray = array();
        $post = $this->input->post();
        $id = $post["id"];

        $array = array();
        $array["updated_by"] = $this->getUserdata()['emp_id'];
        $array["updated_date"] = date("Y-m-d H:i:s");
        $array["reading_date"] = $post["reading_date"];
        $array["distribute"] = $post["distribute"];

        $str_logs_destribute = ($post['old_distribute'] == $post['distribute']) ? " (Distribute none)" : " (Distribute from ".$post['old_distribute']." into ".$post['distribute'].")";
        $str_logs_reading_date = ($post['old_reading_date'] == $post['reading_date']) ? " (Reading date none)" : " (Reading date from ".$post['old_reading_date']." into ".$post['reading_date'].")";

        $distribute = $this->getPreviousDistribution($post["subdivision_id"],$post["reading_date"],$post["meterno"]);
        if($distribute > $post["distribute"]){
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Current reading must equal or exceed to previous reading..";
        } else {
            $this->db->where("id",$id);
            $query = $this->db->update("hydra_billing.distribution",$array);
    
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully updated destribution.";
                $this->core_layout->setEventLog("Distribution - Update details of ".$post["subd_name"].$str_logs_destribute.$str_logs_reading_date,"update", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error updating destribution.";
                $this->core_layout->setEventLog("Distribution - Update details of ".$post["subd_name"].$str_logs_destribute.$str_logs_reading_date,"update", "error", "hydra_billing", "user");
            }
        }

        return $resultarray;
    }

    function getPreviousDistribution($subdivision_id,$reading_date,$meterno){
        $this->db->select("distribute");
        $this->db->from("hydra_billing.distribution");
        $this->db->where("subdivision_id",$subdivision_id);
        $this->db->where("reading_date <",$reading_date);
        $this->db->where("meterno",$meterno);
        $this->db->order_by("reading_date","DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        $distribute = $query->row_array()["distribute"];
        return $distribute ? $distribute : "0";
    }

    function getReportsSOA(){
        $resultarray = array();
        $post = $this->input->post();
        $reconnectionFee = $this->getReconnectionFee()['amount'];

        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        
        $filterFields = array("a.firstname","a.lastname", "a.accountno", "a.meterno","b.name");

        $this->db->select("a.id, CONCAT(a.firstname,' ',a.lastname) as customer_name, a.accountno, a.meterno, b.name as subdivision_name, a.is_disconnected");
        $this->db->from("hydra_billing.accounts a");
        $this->db->join("hydra_billing.subdivision b", "b.id = a.subdivision_id", "LEFT");
        $this->db->where("a.is_archive","0");
        
        if($search != ""){
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

        $this->db->order_by("a.firstname","ASC");
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){

                $balance = $this->computeBalanceLastBill($_query["id"], null, null);
                $reconnection = $_query['is_disconnected']==1 ? $reconnectionFee : 0;

                $data = array();
                $data["id"] = $_query["id"];
                $data["customer_name"] = $_query['customer_name'];
                $data["accountno"] = $_query['accountno'];
                $data["meterno"] = $_query['meterno'];
                $data["subdivision_name"] = $_query['subdivision_name'];
                $data["overPayment"] = $this->computeOverPayment($_query["id"]);
                $data["total_penalty"] = number_format(($balance["total_penalty"] + $reconnection),2, '.', '');
                $data["total_balance"] = number_format($balance["total_balance"],2, '.', '');
                $resultarray[] = $data;
            }
        }

        $total = $this->getReportsSOACount();
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }
    
    function getTotalBalanceEtc(){
        $post = $this->input->post();
        $data = array();
        $balance = $this->computeBalanceLastBill($post["id"], null, null);
        $overpayment = $this->computeOverPayment($post["id"]);
      
        $data['lastbill'] = $balance;
        $data['overpayment'] = $overpayment;
        $data['totol_balance'] = number_format(($balance['total_amount'] - $overpayment),2);
        return $data;
        
    }

    function getBalanceForDisconnection($id){
        $data = array();
        $balance = $this->computeBalanceLastBill($id, null, null);
        $overpayment = $this->computeOverPayment($id);

        $data['lastbill'] = $balance;
        $data['overpayment'] = $overpayment;
        return $data;
    }

    function getReportsSOACount(){
        $this->db->select("id");
        $this->db->from("hydra_billing.accounts");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function computeBalanceLastBill($account_id, $bill_id = -0, $bill_date){
		$array = array();
		$current_date = date("Y-m-d");
    $penalties = $this->getPenalties();
    $total_balance = 0;
		$total_penalty = 0;
    $bills_payments = 0;
        $this->db->select("id, total_charges, due_date, billing_to");
        $this->db->from("hydra_billing.bills");
        $this->db->where("account_id", $account_id);
        if($bill_id != -0){ $this->db->where("id !=", $bill_id); }
        $this->db->where("is_paid", "0");
        $this->db->where("status", "1");
        if($bill_date){
          $this->db->where("due_date <", $bill_date);
        }
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $row){
                $total_charges = $row['total_charges'];
                $overdue_charges = 0;

                if ($current_date > $row['due_date']) {
                    if($penalties['type'] == 'percentage'){
                        $overdue_charges = ($penalties['amount'] / 100) * $total_charges;
                    } else {
                        $overdue_charges = $penalties['amount'];
                    }
                }
                $total_penalty = $total_penalty + $overdue_charges;
                $total_balance = $total_balance + $total_charges;
                $bills_payments = $this->computeBillsPaid($row['id']);
            }
        }

    $array['total_penalty'] = floor(($total_penalty*100))/100;
		$array['total_balance'] = floor(($total_balance*100))/100;
		$array['total_amount'] = ($array['total_balance'] + $array['total_penalty']) - floor(($bills_payments*100))/100;
    $array['bills_payment'] = $bills_payments;
		return $array;
    }

    function computeBillsPaid($bill_id){
      $arrData = array();
      $this->db->select("*");
      $this->db->from("hydra_billing.payments");
      $this->db->where("bill_id", $bill_id);
      $this->db->where("is_archive", 0);
      $query = $this->db->get();

      $partialAmount = 0;
      foreach($query->result_array() as $tempData){
        $actual_amount = $tempData['sub_total'];
        $partialAmount += $actual_amount;
      }
      return $partialAmount;
    }

    function getReportsSOA_details(){
        $resultarray = array();
        $post = $this->input->post();
        $this->db->select("ref_no, created_date, payment_type, payment_details, received_amount, balance_covered, sub_total, net_payment");
        $this->db->from("hydra_billing.payments");
        $this->db->where("account_id",$post['id']);
        $this->db->where("is_archive","0");

        if($post['selectedDate'] != 'all' AND $post['selectedDate'] != ""){ 
            $this->db->where("year(created_date)",$post['selectedDate']); 
        }else{
            if($post['startDate'] != "" && $post['endDate'] != ""){
                $start_date = date("Y-m-d", strtotime($post['startDate']));
                $end_date = date("Y-m-d", strtotime($post['endDate'])); 

                $this->db->where("created_date >=",$start_date);
                $this->db->where("created_date <=",$end_date);
            }
        }  
        $this->db->order_by("created_date","DESC");
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["created_date"] = date('Y-m-d g:i A', strtotime($_query["created_date"]));
                $data["payment_type"] = $_query['payment_type'];
                $data["payment_details"] = $_query['payment_details'];
                $data["sub_total"] = $_query["sub_total"];
                $data["received_amount"] = $_query["received_amount"];
                $data["balance_covered"] = $_query["balance_covered"];
                $data["net_payment"] = $_query["net_payment"];
                $data["ref_no"] = $_query["ref_no"];
                $resultarray[] = $data;
            }
        }

        return array("data"=>$resultarray);
    }

    function getReportsSOA_billing(){
        $resultarray = array();
        $post = $this->input->post();
        $this->db->select("ref_no, created_at, total_charges, billing_from, billing_to, usage");
        $this->db->from("hydra_billing.bills");
        $this->db->where("account_id",$post['id']);

        if($post['selectedDate'] != 'all' AND $post['selectedDate'] != ""){ 
            $this->db->where("year(created_at)",$post['selectedDate']); 
        }else{
            if($post['startDate'] != "" && $post['endDate'] != ""){
                $start_date = date("Y-m-d", strtotime($post['startDate']));
                $end_date = date("Y-m-d", strtotime($post['endDate'])); 

                $this->db->where("billing_from >=",$start_date);
                $this->db->where("billing_to <=",$end_date);
            }
        }  
        $this->db->order_by("created_at","DESC");
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["created_date"] = date('Y-m-d g:i A', strtotime($_query["created_at"]));
                $data["billing_from"] = $_query['billing_from'];
                $data["billing_to"] = $_query['billing_to'];
                $data["total_charges"] = $_query["total_charges"];
                $data["usage"] = $_query["usage"];
                $data["ref_no"] = $_query["ref_no"];
                $resultarray[] = $data;
            }
        }

        return array("data"=>$resultarray);
    }

    function getReportsSOA_readings(){
        $resultarray = array();
        $post = $this->input->post();
        $this->db->select("ref_no, reading_date, reading");
        $this->db->from("hydra_billing.readings");
        $this->db->where("account_id",$post['id']);

        if($post['selectedDate'] != 'all' AND $post['selectedDate'] != ""){ 
            $this->db->where("year(reading_date)",$post['selectedDate']); 
        }else{
            if($post['startDate'] != "" && $post['endDate'] != ""){
                $start_date = date("Y-m-d", strtotime($post['startDate']));
                $end_date = date("Y-m-d", strtotime($post['endDate'])); 

                $this->db->where("reading_date >=",$start_date);
                $this->db->where("reading_date <=",$end_date);
            }
        }  
        $this->db->order_by("reading_date","DESC");
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["reading_date"] = date('Y-m-d', strtotime($_query["reading_date"]));
                $data["reading"] = $_query["reading"];
                $data["ref_no"] = $_query["ref_no"];
                $resultarray[] = $data;
            }
        }

        return array("data"=>$resultarray);
    }

    function getReportsSOA_ledger(){
        $resultarray = array();
        $current_date = date("Y-m-d");
        $post = $this->input->post();
    
        $this->db->select("bill.ref_no, bill.total_charges, bill.is_paid, bill.id, bill.created_at, bill.due_date");
        $this->db->from("hydra_billing.bills bill");
        $this->db->where("bill.account_id",$post['id']);
        $this->db->where("bill.status", 1);
    
        if($post['selectedDate'] != 'all' && $post['selectedDate'] != 'custom'){ 
            $this->db->where("year(bill.created_at)",$post['selectedDate']); 
        }else{
            if($post['startDate'] != "" && $post['endDate'] != "" && $post['selectedDate'] != 'all'){
                $start_date = date("Y-m-d", strtotime($post['startDate']));
                $end_date = date("Y-m-d", strtotime($post['endDate'])); 
    
                $this->db->where("bill.billing_from >=",$start_date);
                $this->db->where("bill.billing_to <=",$end_date);
            }
        }  
        $this->db->order_by("bill.due_date","ASC");
        $query = $this->db->get();
        
        $final_bal = 0;
        $temp_penalties = $this->getPenalties();
        if($query->num_rows() > 0){
            $balance_forwarded = 0;
            foreach($query->result_array() as $_query){
                $balance_covered = 0;
                $data = array();
                $data['due_date'] = date("Y-m-d", strtotime($_query["due_date"]));
                $data["ref_no"] = $_query["ref_no"];
    
                $total_charges = $_query["total_charges"];
                
                
                $temp_credit = $this->getBillPayment($_query['id']);
                
                if(!empty($temp_credit)){
    
                    $data['reconnection_fee'] = $this->getBillPayment($_query['id'])['reconnection_fee'];
                    if($temp_credit['payment_date'] > $_query['due_date']){
                      $penalties = $total_charges * ($temp_penalties['amount'] / 100);
                      $total_charges = $total_charges + $penalties + $data['reconnection_fee'];
                    }else{
                        $penalties = 0;
                        $total_charges = $total_charges;
                    }
                    $data['payment_date'] = '<span class="m--text-muted" style="font-size: 11px;"><small>PD: '.date("M d, Y", strtotime($temp_credit['payment_date'])).'</small></span>';
                }else{
                    $data['reconnection_fee'] = 0;
                    if($current_date > $_query['due_date']){
                      $penalties = $total_charges * ($temp_penalties['amount'] / 100);
                      $total_charges = $total_charges + $penalties + $data['reconnection_fee'];
                    }else{
                      $penalties = 0;
                      $total_charges = $total_charges;
                    }
                    $data['payment_date'] = '--';
                }
                $data['penalty'] = $penalties;
                // $data['reconnection_fee'] = $this->getBillPayment($_query['id'])['reconnecton_fee'];
    
                $data['debit'] = $total_charges;
                $data['total_charges'] = $_query["total_charges"];
                
                if(!empty($temp_credit)){
                  $final_credit = $this->getBillPayment($_query['id'])['received_amount'];
                }else{
                  $final_credit = 0;
                }
                $data["credit"] = $final_credit;
                $temp_balance_forwarded = $data['debit'] - $data["credit"];
                if($temp_balance_forwarded > 0){
                    $balance_forwarded = $temp_balance_forwarded;
                }else{
                    $balance_forwarded = 0;
                }
    
                if($data['debit'] < $data['credit']){
                    $balance_covered = $data['credit'] - $data['debit'];
                }else{
                    $balance_covered = 0;
                }
        
    
                $data["balance_covered"] = !empty($temp_credit) ? $this->getBillPayment($_query['id'])['balance_covered'] : 0;
                
                if(!empty($temp_credit) && $this->getBillPayment($_query['id'])['received_amount'] > 0){
                    $final_temp_bal = $this->getBillPayment($_query['id'])['received_amount'];
                }else{
                    $final_temp_bal = 0;
                }
    
                // if($final_temp_bal > $balance_forwarded && $balance_forwarded != 0){
                //     $final_temp_bal = $final_temp_bal + $balance_forwarded;
                // }
    
                $balance = ($data["debit"] - (float)$final_temp_bal);
                
                $final_bal += $balance;
                
                $data['balance'] = floor(($final_bal*100))/100;
                $resultarray[] = $data;
            }
        }
    
        return array("data"=>$resultarray);
    }

    function getBillPayment($bill_id){
        $this->db->select("sum(received_amount) as received_amount, sum(sub_total) as sub_total, sum(net_payment) as net_payment, sum(balance_covered) as balance_covered, payment_date, reconnection_fee as rf");
        $this->db->from("hydra_billing.payments");
        $this->db->where("bill_id", $bill_id);
        $this->db->where("is_archive", 0);
        $this->db->order_by("payment_date", "DESC");
        $this->db->group_by("bill_id");
        $query = $this->db->get();
        $data = $query->result();
        $arrData = array();
        foreach($query->result() as $temp){
            $arrData['balance_covered'] = $temp->balance_covered == 0 ? 0 : (float)$temp->balance_covered;
            $arrData['received_amount'] = $temp->received_amount == 0 ? 0 : (float)$temp->received_amount;
            $arrData['sub_total'] = $temp->sub_total == 0 ? 0 : (float)$temp->sub_total;
            $arrData['net_payment'] = $temp->net_payment == 0 ? 0 : (float)$temp->net_payment;
            $arrData['payment_date'] = $temp->payment_date;
            $arrData['reconnection_fee'] = $temp->rf == 0 ? 0: (float)$temp->rf;
        }
        return $arrData;
    }

    function getReportsSOA_dates(){
        $resultarray = array();
        $post = $this->input->post();
        $this->db->select("year(created_date) as id, year(created_date) as text");
        $this->db->from("hydra_billing.payments");
        $this->db->where("is_archive","0");
        $this->db->group_by("year(created_date)");
        $query = $this->db->get();
        
        $data = array();
        $all = array('id'=>'all', 'text'=>'all');
        array_push($resultarray, $all);

        foreach ($query->result_array() as $_query) {
            $data["id"] = $_query["id"];
            $data["text"] = $_query["text"];
            $resultarray[] = $data;
        }
        
        $custom = array(
            'id'=>"custom",
            'text'=>"custom"
            );
        array_push($resultarray, $custom);

        return array("results" => $resultarray);
    }

    function getReportsSOADetails($id, $selectedDate, $startDate, $endDate, $report_type){
        $this->db->select("*");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("id",$id);
        $query = $this->db->get()->row_array();
        $data = array();
        $data["selectedDate"] = $selectedDate;
        $data["id"] = $query['id'];
        $data["accountno"] = $query['accountno'];
        $data["meterno"] = $query['meterno'];
        $data['model'] = $query['model'];
        $data['block'] = $query['block'];
        $data['lot'] = $query['lot'];
        $data["brgy"] = $query['brgy'];
        $data["province"] = $query['province'];
        $data["street"] = $query['street'];
        $data["customer_name"] = $this->nameFormat($query["firstname"], $query["middlename"], $query["lastname"]);
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['report_type'] = $report_type;
        return $data;
    }

    function printReportsSOA(){
        $post = $this->input->post();
        $reconnectionFee = $this->getReconnectionFee()['amount'];
        $balance = $this->computeBalanceLastBill($post["customer_id"], null, null);
        $reconnection = $this->printReportsSOAreconnection($post['customer_id'], $reconnectionFee);

        $data["overPayment"] = $this->computeOverPayment($post["customer_id"]);
        $data["total_penalty"] = number_format(($balance["total_penalty"] + $reconnection),2, '.', '');
        $data["total_balance"] = number_format($balance["total_balance"],2, '.', '');
        if(!isset($post['endDate'])){
            $post['endDate'] = null;
        }
        $this->core_layout->setEventLog("Reposrts SOA - print account statement of ".$post["account_name"],"print", "success", "hydra_billing", "user");
        $data["data"] = $this->getReportsSOADetails($post["customer_id"], $post["selectedDate"], $post['startDate'], $post['endDate'], $post['report_type']);
        return $this->load->view("eforms/billing/reports_soa/print", $data, true);
    }

    function printReportsSOAreconnection($id, $reconnectionFee){
        $this->db->select('is_disconnected');
        $this->db->where('id', $id);
        $this->db->from('hydra_billing.accounts');
        $q = $this->db->get();
        foreach($q->result_array() as $row){
            $reconnection = $row['is_disconnected'] == 1 ? $reconnectionFee : 0;
        }
        return $reconnection;
    }

    function dashboardCUM(){
        $post = $this->input->post();
        return array("customerUsage"=>$this->getCustomerUsage('all'), "distributionSupply"=>$this->getDistributionSupply('all'));
    }

    function getCustomerUsage($id){
        $year = date("Y");
        $total_reading = 0;

        $this->db->select("a.reading, a.id as reading_id, a.account_id, a.reading_date, a.meterno");
        $this->db->from("hydra_billing.readings a");
        $this->db->join("hydra_billing.accounts b", "a.account_id = b.id", "LEFT");
        $this->db->where("b.is_archive",'0');
        $this->db->where("a.is_archived", "0");
        $this->db->where("YEAR(a.reading_date)",$year);
        if($id != 'all'){ $this->db->where("b.subdivision_id",$subd_id); }
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $total_reading = $total_reading + $this->calculateUsage($_query['account_id'], $_query['reading_date'], $_query['reading'], $_query['meterno']);
            }
        }
        return number_format((float)$total_reading, 2, '.', '');
    }

    function getDistributionSupply($subd_id){
        $year = date("Y");
        $total_reading = 0;
        $this->db->select("distribute, subdivision_id, id, reading_date, meterno");
        $this->db->from("hydra_billing.distribution");
        $this->db->where("YEAR(reading_date)",$year);
        $this->db->where("is_archive", "0");
        if($subd_id != 'all'){ $this->db->where("subdivision_id",$subd_id); }
        $this->db->order_by("reading_date","DESC");
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $total_reading = $total_reading + $this->calculateUsageDistribution($_query['subdivision_id'], $_query['reading_date'], $_query['distribute'], $_query['meterno']);
            }
        }
        return number_format($total_reading,2, '.', '');
    }

    function updateCutOffPeriod(){
        $resultarray = array();
        $post = $this->input->post();

        if($this->checkSetupData("hydra_billing.cut_off_period") > 0){ // update
            $id = $post["id"];
            $post["updated_by"] = $this->getUserdata()['emp_id'];
            $post["updated_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "update";

            $this->db->where("id",$id);
            $query = $this->db->update("hydra_billing.cut_off_period",$post);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully updated cut off period.";
                $this->core_layout->setEventLog("Setup - Update Cut off period to ".$post['day'],"update", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error updating cut off period.";
                $this->core_layout->setEventLog("Setup - Error updating Cut off period to ".$post['day'],"update", "error", "hydra_billing", "user");
            }
        } else { // add 
            $post["created_by"] = $this->getUserdata()['emp_id'];
            $post["created_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "add";

            $query = $this->db->insert("hydra_billing.cut_off_period",$post);
            if($query){
                $resultarray['id'] = $this->db->insert_id();
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully added cut off period.";
                $this->core_layout->setEventLog("Setup - added Cut off period ".$post['day'],"insert", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error adding cut off period.";
                $this->core_layout->setEventLog("Setup - Error adding Cut off period ".$post['day'],"insert", "error", "hydra_billing", "user");
            }
        }

        return $resultarray;
    }

    function getCutOffPeriod(){
        $this->db->select("*");
        $this->db->from("hydra_billing.cut_off_period");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function updateDueDate(){
        $resultarray = array();
        $post = $this->input->post();

        if($this->checkSetupData("hydra_billing.due_date") > 0){ // update
            $id = $post["id"];
            $post["updated_by"] = $this->getUserdata()['emp_id'];
            $post["updated_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "update";

            $this->db->where("id",$id);
            $query = $this->db->update("hydra_billing.due_date",$post);
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully updated due date";
                $this->core_layout->setEventLog("Setup - Update Due Date to ".$post['day'],"update", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error updating due date";
                $this->core_layout->setEventLog("Setup - Error updating Due Date to ".$post['day'],"update", "error", "hydra_billing", "user");
            }
        } else { // add 
            $post["created_by"] = $this->getUserdata()['emp_id'];
            $post["created_at"] = date("Y-m-d H:i:s");
            $resultarray["type"] = "add";

            $query = $this->db->insert("hydra_billing.due_date",$post);
            if($query){
                $resultarray['id'] = $this->db->insert_id();
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully added due date";
                $this->core_layout->setEventLog("Setup - Added Due Date ".$post['day'],"insert", "success", "hydra_billing", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error adding due date";
                $this->core_layout->setEventLog("Setup - Error adding Due Date ".$post['day'],"insert", "error", "hydra_billing", "user");
            }
        }

        return $resultarray;
    }

    function checkSetupData($query){
        $this->db->select("id");
        $this->db->from($query);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getDueDate(){
        $this->db->select("*");
        $this->db->from("hydra_billing.due_date");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    function updateAccountMeter(){
        $resultarray = array();
        $post = $this->input->post();

        $array["type"] = $post['type'];
        $array["remarks"] = $post['remarks'];
        $array["account_id"] = $post['account_id'];
        $array["new_meterno"] = $post['new_meterno'];
        $array["old_meterno"] = $post['old_meterno'];
        $array["last_reading"] = $post['previous_reading'];
        $array["edit_by"] = $this->getUserdata()['emp_id'];
        $array["created_date"] = date("Y-m-d H:i:s");
        

        // type 1 = subdivision, 2 = accounts
        $event_logs_title = $post['type']==1 ? "Subdivision" : "Accounts";
        $query_type = $post['type']==1 ? "hydra_billing.subdivision" : "hydra_billing.accounts";

        if($this->checkDuplicateMeter($query_type, $post['new_meterno'], $post['account_id']) > 0){
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Meter no. is already exist";
        } else {
            $isReadingNotSkip = $this->isSelectedReadingNotSkip(json_decode($post["selectedReading"]), $post['account_id'], $post['type']);

            if($isReadingNotSkip["status"]){
                $query = $this->db->insert("hydra_billing.tbl_meter_history",$array);
                if($query){
                    $this->updateMeter($post['new_meterno'], $post['account_id'], $query_type);
                    $response_msg = $this->updateAccountMeterReading(json_decode($post["selectedReading"]), $post['account_id'], $post['new_meterno'], $post['type'], $post['old_meterno']);
                    
                    $resultarray["status"] = TRUE;
                    $resultarray["new_meterno"] = $post['new_meterno'];
                    $resultarray["msg"] = "Successfully change meter no.";
                    $this->core_layout->setEventLog($event_logs_title." - Change meter no. of ".$post['account_name']." from ".$post['old_meterno'].' to '.$post['new_meterno'].$response_msg,"update", "success", "hydra_billing", "user");
                }else{
                    $resultarray["status"] = FALSE;
                    $resultarray["msg"] = "Failed to change meter no.";
                    $this->core_layout->setEventLog($event_logs_title." - Change meter no. of ".$post['account_name']." from ".$post['old_meterno'].' to '.$post['new_meterno'],"update", "error", "hydra_billing", "user");
                }
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Do not skip the checkbox of <br/>".$isReadingNotSkip["ref_no"];
            }
        }

        return $resultarray;
    }

    function isSelectedReadingNotSkip($selectedReading, $account_id, $type){
        $array = array();
        $array["status"] = true;

        if($type == 1){ // subdivision
            return $array;
        }

        $this->db->select("id, ref_no");
        $this->db->from("hydra_billing.readings");
        $this->db->where("is_archived", "0");
        $this->db->where("account_id", $account_id);
        $this->db->order_by('ref_no', 'DESC');
        $this->db->LIMIT(count((array)$selectedReading));
        $query = $this->db->get();
        for($i=0; $i <$query->num_rows(); $i++){
            $row = $query->result_array()[$i];
            if($selectedReading[$i] != $row["id"]){
                $array["status"] = false;
                $array["ref_no"] = $row["ref_no"];
                return $array;
            }
        }
        return $array;
    }

    function updateAccountMeterReading($selectedReading, $account_id, $meter_no, $type, $old_meterno){
        $affected = "";
        if($type == 2 && count((array)$selectedReading) > 0){
            $affected .= "<br /><br />List of updated reading";
            $affected .= "<ul>";
            foreach($selectedReading as $reading_id){
                $updateReading = $this->updateReadingMeterNo($account_id, $reading_id, $meter_no);
                if($updateReading){
                    $reading = $this->getReadingDetails($reading_id);
                    $affected .= "<li><strong style='color: #525252;'>". $reading["ref_no"]. "</strong> from " .$old_meterno. " to " .$meter_no."</li>";
                }
            }
            $affected .= "</ul>";
        }
        return $affected;
    }

    function updateReadingMeterNo($account_id, $id, $meter_no){
        $post = array();
        $post["meterno"] = str_replace(" ", "", str_replace("-", "", $meter_no));
        $this->db->where("id",$id);
        $this->db->where("account_id",$account_id);
        $query = $this->db->update("hydra_billing.readings",$post);
        if($query){
            return true;
        }
        return false;
    }

    function checkDuplicateMeter($query_type, $meterno, $account_id){
        $meterno_raw = str_replace(" ", "", str_replace("-", "", $meterno));
        $this->db->select("id");
        $this->db->from($query_type);
        $this->db->where("meterno_raw", $meterno_raw);
        $this->db->where("id !=", $account_id);
        return $this->db->get()->num_rows();
    }

    function updateMeter($new_meterno, $id, $query_type){
        $array2 = array();
        $array2['meterno'] = $new_meterno;
        $array2['meterno_raw'] = str_replace(" ", "", str_replace("-", "", $new_meterno));
        $this->db->where("id", $id);
        $query = $this->db->update($query_type, $array2);
        return $query;
    }

    function archiveDestribution(){
        $array = array();
        $resultarray = array();
        $post = $this->input->post();
        $array["archive_by"] = $this->getUserdata()['emp_id'];
        $array["archive_date"] = date("Y-m-d H:i:s");
        $array['is_archive'] = "1";

        $this->db->where("id", $post['id']);
        $query = $this->db->update("hydra_billing.distribution",$array);
        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Successfully archive distribution.";
            $this->core_layout->setEventLog("Distribution - archive distribute of ".$post['archive_subd_name']." date of ".$post['archive_distribute_date'],"archived", "success", "hydra_billing", "user");
        } else {
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Failed to archive distribution.";
            $this->core_layout->setEventLog("Distribution - archive distribute of ".$post['archive_subd_name']." date of ".$post['archive_distribute_date'],"archived", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function archiveReading(){
        $post = $this->input->post();
        $id = $post["id"];
        $post["is_archived"] = '1';

        $this->db->where("id",$id);
        $query = $this->db->update('hydra_billing.readings', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Successfully archived.";
            $this->core_layout->setEventLog("Reading - Archived reading of ".$post["ref_no"],"archived", "success", "hydra_billing", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error archiving reading.";
            $this->core_layout->setEventLog("Reading - Error archiving reading of".$post["ref_no"],"archived", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function restoreReading(){
        $post = $this->input->post();
        $id = $post["id"];
        $post["is_archived"] = '0';

        unset($post['id']);
        $this->db->where("id",$id);
        $query = $this->db->update('hydra_billing.readings', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Successfully Restored.";
            $this->core_layout->setEventLog("Reading - Restored reading of ".$post["ref_no"],"restore", "success", "hydra_billing", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error restoring reading.";
            $this->core_layout->setEventLog("Reading - Error restoring reading of".$post["ref_no"],"restore", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    function restorePayment(){
        $post = $this->input->post();
        $id = $post["id"];
        $post["is_archive"] = '0';

        unset($post['id']);
        $this->db->where("id",$id);
        $query = $this->db->update('hydra_billing.payments', $post);

        if($query){
            $resultarray["status"] = TRUE;
            $resultarray["msg"] = "Successfully Restored.";
            $this->core_layout->setEventLog("Payment - Restored payment of ".$post["ref_no"],"restore", "success", "hydra_billing", "user");
        }else{
            $resultarray["status"] = FALSE;
            $resultarray["msg"] = "Error restoring reading.";
            $this->core_layout->setEventLog("Payment - Error restoring payment of".$post["ref_no"],"restore", "error", "hydra_billing", "user");
        }

        return $resultarray;
    }

    // function restoreBilling(){
    //     $post = $this->input->post();
    //     $id = $post["id"];
    //     $post["status"] = '1';

    //     unset($post['id']);
    //     $this->db->where("id",$id);
    //     $query = $this->db->update('hydra_billing.bills', $post);

    //     if($query){
    //         $resultarray["status"] = true;
    //         $resultarray["msg"] = "Successfully Restored.";
    //         $this->core_layout->setEventLog("Billign - Restored billing of ".$post["ref_no"],"restore", "success", "hydra_billing", "user");
    //     }else{
    //         $resultarray["status"] = false;
    //         $resultarray["msg"] = "Error restoring reading.";
    //         $this->core_layout->setEventLog("Billing - Error restoring billing of".$post["ref_no"],"restore", "error", "hydra_billing", "user");
    //     }

    //     return $resultarray;
    // }

    function getReadingAccounts(){
        $resultarray = array();
        $post = $this->input->post();
        $account_id = $post["account_id"];

        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

        $filterFields = array("r.meterno", "r.ref_no", "r.reading_date", "r.status", "r.reading");

        $this->db->select("r.id, r.meterno, r.ref_no, r.reading_date, r.reading, r.is_billed");
        $this->db->from("hydra_billing.readings r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.is_archived", "0");
        $this->db->where("r.account_id", $account_id);

        if($search != ""){
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
        $this->db->order_by('r.ref_no', 'DESC');

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["id"];
                $data["meterno"] = $_query["meterno"];
                $data["ref_no"] = $_query["ref_no"];
                $data["reading_date"] = $_query["reading_date"];
                $data["status"] = $_query["is_billed"];
                $data["reading"] = number_format((float)$_query["reading"], 2, '.', '');
                $resultarray[] = $data;
            }
        }

        $total = $this->getReadingAccountsCount($search, $account_id);
        return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }

    function getReadingAccountsCount($search, $account_id){
        $filterFields = array("r.meterno", "r.ref_no", "r.reading_date", "r.status", "r.reading");
        $this->db->select("r.id, r.meterno, r.ref_no, r.reading_date, r.reading, r.is_billed");
        $this->db->from("hydra_billing.readings r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.is_archived", "0");
        $this->db->where("r.account_id", $account_id);
        if($search != ""){
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
        $this->db->order_by('r.ref_no', 'DESC');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function test_email(){
        //return $this->billing_statement_email(true, "thechosenweak@gmail.com", "1109");

        // $data["data"] = $this->getReportsSOADetails("31", "2021");
        // echo $this->load->view("eforms/billing/reports_soa/print", $data, true);

        return $this->fixAccountsRawMeter();
    }

    // ------------------------------------ Quick fix database --------------------------------------
    function fixAccountsRawMeter(){
        $result["accounts"] = array();
        $this->db->select("id, meterno, meterno_raw");
        $this->db->from("hydra_billing.accounts");
        $query = $this->db->get();
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $list = array();

                if($_query["meterno_raw"] == ''){
                    $meterno_raw = str_replace(" ", "", str_replace("-", "", $_query["meterno"]));
                    if($this->updateAccountsRawMeter($_query["id"], $meterno_raw)){
                        $list['status'] = true;
                    } else {
                        $list['status'] = false;
                    }

                    $list['id'] = $_query["id"];
                    array_push($result["accounts"], $list);
                }
            }
        }
        return $result;
    }

    function updateAccountsRawMeter($id, $meterno_raw){
        $post = array();
        $post["meterno_raw"] = $meterno_raw;
        $this->db->where("id",$id);
        $query = $this->db->update("hydra_billing.accounts",$post);
        if($query){
            return true;
        }
        return false;
    }
    // ------------------------------------ Closing Quick fix database --------------------------------------

    function checkEmail($email){
        $find1 = strpos($email, '@');
        $find2 = strpos($email, '.');
        return ($find1 !== false && $find2 !== false && $find2 > $find1);
    }

    function billing_statement_email($email=false, $email_address, $bill_id){
        if($this->checkEmail($email_address)){

            $data["data"] = $this->getBillforPrintData($bill_id);
            if($data){
                $messageContent = "";
                $messageContent .= $this->load->view("eforms/email_templates/hydra_billing_templ/billing_statement", $data, true);
                
                if($email){
                    $module = "eforms_hydra_billing";
                    $email_title = "Hydra Billing";
                    $content_title = "Billing Statement";
                    $content = $messageContent;

                    $overrideMailer = array();
                    $overrideMailer["send_to"] = array($email_address);
                    
                    if($content){
                        $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
                        if($sent){
                            return true;
                        }else{
                            //show_error($this->email->print_debugger());
                            return false;
                        }
                    }else{
                        //echo "No email content";
                        return false;
                    }
                }else{
                    echo $messageContent;
                }
            }else{
                return false;
            }
        } else {
            return false;
        }
    }

    function generateReadingReport(){
        $resultarray = array();
        $post = $this->input->post();
        $account_id = $post["account_id"];

        $this->db->select("UPPER(CONCAT(a.firstname,' ', a.lastname)) AS name, r.id, r.meterno, r.ref_no, r.reading_date, r.reading, sum(r.reading) as sum_reading, r.is_billed");
        $this->db->from("hydra_billing.readings r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.is_archived", "0");
        if(isset($post['startDate']) AND isset($post['endDate'])){
            $this->db->where("r.reading_date >=", $post['startDate']);
            $this->db->where("r.reading_date <=", $post['endDate']);
        }
        if(isset($post['account_id']) AND $post['account_id'] != ""){
            $this->db->where("r.account_id", $account_id);
        }
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["id"];
                $data['name'] = $_query['name'];
                $data["meterno"] = $_query["meterno"];
                $data["ref_no"] = $_query["ref_no"];
                $data["reading_date"] = $_query["reading_date"];
                $data["status"] = $_query["is_billed"];
                if(isset($post['account_id'])){
                    $data["reading"] = number_format((float)$_query["reading"], 2, '.', '');
                }else{
                    $data["reading"] = number_format((float)$_query["sum_reading"], 2, '.', '');
                }
                $resultarray[] = $data;
            }
        }
        return array("data"=>$resultarray, "recordsTotal"=>100, "recordsFiltered"=>100);
    }

    function getPaymentArchiveCollection(){
      $resultarray = array();
      $post = $this->input->post();

      $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
      $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
      $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

      $filterFields = array("a.accountno", "a.firstname", "a.lastname",
              "a.lot", "a.block", "r.ref_no", "r.payment_date", "a.middlename");

      $this->db->select("a.middlename,r.id,a.accountno,a.firstname,a.lastname,a.lot,a.block,r.ref_no,r.payment_date, a.model,r.acknowledgement_receipt as ar");
      $this->db->from("hydra_billing.payments r");
      $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
      $this->db->where("r.is_archive", "1");

      if($search != ""){
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
      $this->db->order_by('r.ref_no', 'DESC');

      if($limit != -1){
          $this->db->limit($limit, $offset);
      }

      $query = $this->db->get();

      if($query->num_rows() > 0){
          foreach($query->result_array() as $_query){
              $data = array();
              $data["name"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
              $data["id"] = $_query["id"];
              $data["accountno"] = $_query["accountno"];
              $data["block"] = $_query["block"];
              $data["lot"] = $_query["lot"];
              $data["model"] = $_query["model"];
              $data["ref_no"] = $_query["ref_no"];
              $data["payment_date"] = $_query["payment_date"];
              $data["ar"] = $_query["ar"];
              $resultarray[] = $data;
          }
      }

      $total = $this->getReadingArchiveCount($search);
      return array("data"=>$resultarray, "recordsTotal"=>$total, "recordsFiltered"=>$total);
    }


    public function getBillingArchiveCollection(){
        $resultArray = array();
        $postData = $this->input->post();

        $orderBy = array(array("column" => "1", "dir" => "desc"));
        $search = isset($postData["search"]["value"]) && $postData["search"]["value"] ? $postData["search"]["value"] : false;
        $limit = isset($postData["length"]) && $postData["length"] ? $postData["length"] : 10;
        $offset = isset($postData["start"]) && $postData["start"] ? $postData["start"] : 0;
        $sortColumn = isset($postData["columns"]) && $postData["columns"] ? $postData["columns"] : 1;
        $sortOrder = isset($postData["order"]) && $postData["order"] ? $postData["order"] : $orderBy;

        $filterFields = [
            "a.accountno",
            "a.firstname",
            "a.middlename",
            "a.lastname",
            "CONCAT(TRIM(a.firstname), ' ', LEFT(TRIM(a.middlename), 1), '.', ' ', TRIM(a.lastname))", // John D. Doe
            "CONCAT(TRIM(a.firstname), ' ', TRIM(a.lastname))", // John Doe
            "CONCAT(TRIM(a.firstname), ' ', TRIM(a.middlename), ' ', TRIM(a.lastname))", // John Donegan Doe
            "a.lot",
            "a.block",
            "r.ref_no"
        ];

        // Build the query
        $this->db->select("a.middlename, r.id, a.accountno, CONCAT(TRIM(a.firstname), ' ', LEFT(TRIM(a.middlename), 1), '.', ' ', TRIM(a.lastname)) as name, a.lot, a.block, r.ref_no, r.due_date, a.model, r.status as bill_status");
        $this->db->from("hydra_billing.bills r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.status", "0");

        // Add search filter dynamically
        if (!empty($search)) {
            $this->db->group_start();
            foreach ($filterFields as $field) {
                $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }

        // Add sorting
        if (isset($sortOrder[0]['column'])) {
            $columnIndex = $sortOrder[0]['column'];
            $this->db->order_by($sortColumn[$columnIndex]['data'], $sortOrder[0]['dir']);
        }

        // Add pagination
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        // Execute query
        $query = $this->db->get();

        // Process results
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $resultArray[] = [
                    "name" => $row["name"],
                    "id" => $row["id"],
                    "accountno" => $row["accountno"],
                    "block" => $row["block"],
                    "lot" => $row["lot"],
                    "model" => $row["model"],
                    "ref_no" => $row["ref_no"],
                    "due_date" => $row["due_date"]
                ];
            }
        }

        $total = $this->getBillingArchiveCount($search);
        return array("data" => $resultArray, "recordsTotal" => $total, "recordsFiltered" => $total);
    }

    public function getBillingArchiveCount($search){
        $filterFields = [
            "a.accountno",
            "a.firstname",
            "a.middlename",
            "a.lastname",
            "CONCAT(TRIM(a.firstname), ' ', LEFT(TRIM(a.middlename), 1), '.', ' ', TRIM(a.lastname))", // John D. Doe
            "CONCAT(TRIM(a.firstname), ' ', TRIM(a.lastname))", // John Doe
            "CONCAT(TRIM(a.firstname), ' ', TRIM(a.middlename), ' ', TRIM(a.lastname))", // John Donegan Doe
            "a.lot",
            "a.block",
            "r.ref_no"
        ];

        $this->db->select("a.middlename, r.id, a.accountno, CONCAT(TRIM(a.firstname), ' ', LEFT(TRIM(a.middlename), 1), '.', ' ', TRIM(a.lastname)) as name, a.lot, a.block, r.ref_no, r.due_date, a.model, r.status as bill_status");
        $this->db->from("hydra_billing.bills r");
        $this->db->join("hydra_billing.accounts a", "a.id = r.account_id", "LEFT");
        $this->db->where("r.status", "0");

        // Add search filter dynamically
        if (!empty($search)) {
            $this->db->group_start();
            foreach ($filterFields as $field) {
                $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }
        
        // Add sorting
        if (isset($sortOrder[0]['column'])) {
            $columnIndex = $sortOrder[0]['column'];
            $this->db->order_by($sortColumn[$columnIndex]['data'], $sortOrder[0]['dir']);
        }
        
        $query = $this->db->get();
        return $query->num_rows();
    }

    function disconnectSelected(){
      $current_date = date('Y-m-d');
      $post = $this->input->post();
      $accounts = array();

      if(isset($post['all']) && $post['all'] == 'all'){
        $this->db->select("b.accountno, a.total_charges, b.firstname, b.lastname, b.middlename, b.id");
        $this->db->from("hydra_billing.bills a");
        $this->db->join("hydra_billing.accounts b", "b.id = a.account_id", "LEFT");
        $this->db->where("a.is_paid",'0');
        $this->db->where("b.is_disconnected", 0);
        $this->db->where("a.due_date <",$current_date);
        
        $this->db->group_by("b.id");
        $this->db->order_by("a.due_date","DESC");
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
              $update = $this->db->update("hydra_billing.accounts", array("is_disconnected"=>1, "disconnect_date"=>date("Y-m-d h:i:s")), array("id"=>$_query['id']));
              if($update){
                $accounts[] = $_query['firstname']." ".$_query['lastname'];
                $customer_name = $_query['firstname']." ".$_query['lastname'];
                $this->core_layout->setEventLog("Accounts - ".'Water disconnected '.$customer_name,"update", "success", "hydra_billing", "user");
              }
            }
        }
      }else{
        foreach($post['overdue_accounts'] as $temp_accounts){
          $update = $this->db->update("hydra_billing.accounts", array("is_disconnected"=>1, "disconnect_date"=>date("Y-m-d h:i:s")), array("id"=>$temp_accounts));
          if($update){
            $accounts_query = $this->db->get_where("hydra_billing.accounts", array('id'=>$temp_accounts))->row();
            $accounts[] = $accounts_query->firstname." ".$accounts_query->lastname;
            $customer_name = $accounts_query->firstname." ".$accounts_query->lastname;
            $this->core_layout->setEventLog("Accounts - ".'Water disconnected '.$customer_name,"update", "success", "hydra_billing", "user");
          }
        }
      }

      return $accounts;
    }

    function reconnectSelected(){
      $post = $this->input->post();
      $accounts = array();
      foreach($post['overdue_accounts'] as $temp_accounts){
        $update = $this->db->update("hydra_billing.accounts", array("is_disconnected"=>0), array("id"=>$temp_accounts));
        if($update){
          $accounts_query = $this->db->get_where("hydra_billing.accounts", array('id'=>$temp_accounts))->row();
          $accounts[] = $accounts_query->firstname." ".$accounts_query->lastname;
          $customer_name = $accounts_query->firstname." ".$accounts_query->lastname;
          $this->core_layout->setEventLog("Accounts - ".'Water disconnected '.$customer_name,"update", "success", "hydra_billing", "user");
        }
      }

      return $accounts;
    }

    function disconnectedAccounts(){
      $accounts = array();
      $resultarray = array();
      $post = $this->input->post();
      $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
      $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
      
      $this->db->select("firstname, middlename, lastname, disconnect_date, accountno");
      $this->db->from("hydra_billing.accounts");
      $this->db->where("is_disconnected",'1');
      $this->db->order_by("disconnect_date", 'DESC');
      if($limit != -1){
        $this->db->limit($limit, $offset);
      }
      $query = $this->db->get();

      if($query->num_rows() > 0){
          foreach($query->result_array() as $_query){
              $data = array();

              $data["customer_name"] = $_query['firstname'].' '.$_query['middlename'][0].' '.$_query['lastname'];
              $data["disconnect_date"] = date('M d, Y', strtotime($_query['disconnect_date']));
              $data["accountno"] = $_query['accountno'];
              $resultarray[] = $data;
          }
      }
      return array("data"=>$resultarray, "recordsTotal"=>$this->getDisconnectedAccountCount(), "recordsFiltered"=>$this->getDisconnectedAccountCount());
    }

    function getDisconnectedAccountCount(){
      $this->db->select("firstname, middlename, lastname, disconnect_date");
      $this->db->from("hydra_billing.accounts");
      $this->db->where("is_disconnected",'1');
      $query = $this->db->get();
      return $query->num_rows();
  }

  function getPaymentCollectionReport(){
    $resultarray = array();
    $post = $this->input->post();

    $order_val = array(array("column"=>"6", "dir"=>"desc"));
    $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
    $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;

    if(isset($post['date'])){
      $date = explode("-", $post['date']);
    }else{
      $date = date("Y-m-d");
    }

    $this->db->select("*, bill.ref_no as bill_ref, payment.ref_no as payment_ref, CONCAT(emp.firstname, ' ', emp.lastname) as cashier, UPPER(CONCAT(acct.firstname, ' ', acct.lastname)) as account");
    $this->db->from("hydra_billing.payments payment");
    $this->db->join("hydra_billing.accounts acct", "acct.id=payment.account_id", "LEFT");
    $this->db->join("hydra_billing.bills bill", "bill.id=payment.bill_id", "LEFT");
    $this->db->join("gccmaster.tblemployees emp", "emp.id=payment.created_by", "LEFT");
    $this->db->where("payment.created_by",$post['id']);
    if(date("Y-m-d", strtotime($date[0])) == date("Y-m-d", strtotime($date[1]))){
      $this->db->where("DATE(payment.created_date)", date("Y-m-d", strtotime($date[0])));
    }else{
      $this->db->where("DATE(payment.created_date) >",date("Y-m-d", strtotime($date[0])));
      $this->db->where("DATE(payment.created_date) <",date("Y-m-d", strtotime($date[1])));
    }
    
    $i = $sortOrder[0]['column'];
    $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

    $query = $this->db->get();
    if($query->num_rows() > 0){
        foreach($query->result_array() as $_query){
            $data = array();
            $data["account"] = $_query["account"];
            $data["bill_ref"] = $_query["bill_ref"];
            $data["acknowledgement_receipt"] = $_query["acknowledgement_receipt"];
            $data["payment_ref"] = $_query["payment_ref"];
            $data["type"] = strtoupper($_query["payment_type"]);
            $data["amount"] = $_query["received_amount"] ? $_query['received_amount'] : $_query['balance_covered'];
            $data["created_date"] = date("Y-m-d", strtotime($_query["created_date"]));
            $data["cashier"] = $_query["cashier"];
            $data["total_count"] = $query->num_rows();
            $resultarray[] = $data;
        }
    }
    return array("data"=>$resultarray, "recordsTotal"=>$query->num_rows(), "recordsFiltered"=>$query->num_rows());
  }

  function getSalesReport(){
    $resultarray = array();
    $post = $this->input->post();
    if(isset($post['date'])){
      $date = explode("-", $post['date']);
    }else{
      $date = date("Y-m-d");
    }

    $this->db->select("*");
    $this->db->from("hydra_billing.payments");
    if(date("Y-m-d", strtotime($date[0])) == date("Y-m-d", strtotime($date[1]))){
      $this->db->where("DATE(created_date)", date("Y-m-d", strtotime($date[0])));
    }else{
      $this->db->where("DATE(created_date) >",date("Y-m-d", strtotime($date[0])));
      $this->db->where("DATE(created_date) <",date("Y-m-d", strtotime($date[1])));
    }
    $this->db->group_start();
    $this->db->where("reconnection_fee !=", 0);
    $this->db->or_where("penalties !=", 'a:0:{}');
    $this->db->group_end();
    $query = $this->db->get();
    
    if($query->num_rows() > 0){
        foreach($query->result_array() as $_query){
            $data = array();
            
            $sumPenalty = 0;
            $data["payment_ref"] = $_query["ref_no"];
            $data["payment_date"] = $_query["payment_date"];
            $data["reconnection_fee"] = number_format($_query["reconnection_fee"],2);
            $data["type"] = $_query["payment_type"];
            $data["total_count"] = $query->num_rows();
            $penalties = unserialize($_query['penalties']);
            foreach($penalties as $tempPenalty){
              $sumPenalty += $tempPenalty['overdue'];
            }
            $data["penalty"] = number_format($sumPenalty,2);
            $resultarray[] = $data;
        }
    }
    return array("data"=>$resultarray);
  }

  function getEmployeeCollector(){
    $get = $this->input->get();
    $resultarray = array();
    if (isset($get['q'])) {
        $query = $this->db->query("SELECT id, firstname, lastname, middlename
        FROM gccmaster.tblemployees
        WHERE employee_status='Active' AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%') ORDER BY id ASC");
    }else{
        $query = $this->db->query("SELECT id, firstname, lastname, middlename
        FROM gccmaster.tblemployees
        WHERE employee_status='Active' ORDER BY id ASC");
    }

    if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $_query) {
            $data = array();
            $data["id"] = $_query["id"];
            $data["text"] = $this->nameFormat($_query["firstname"], $_query["middlename"], $_query["lastname"]);
            $resultarray[] = $data;
        }
    }

    return array("results" => $resultarray);
}
}
