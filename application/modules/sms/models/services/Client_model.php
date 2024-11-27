<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Client_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model","dt_model");
        $this->user_data = $this->session->userdata("logged_in"); 
        $this->load->model("core/upload_model", "file_upload");
    }

    private function getUserData(){
        return $this->core_layout->getUserLoggedIn();
    }

    function clientOutbox(){
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"])? $post["search"]: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";
        
        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->get_client_outbox($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_client_outbox_count();
        }

        if($search){
            $rowData = $this->get_searched_client_outbox($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_client_outbox_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_client_outbox_count(){
        $this->db->from("gccsms.tblmsgs3 a");
        $this->db->where("a.user", $this->user_data['username']);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    private function get_client_outbox($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        
        $sql = "a.id, a.user, a.cp_no, a.recipient, a.msg, a.mid, a.status, a.date_sent";
        $this->db->select($sql);
        $this->db->from("gccsms.tblmsgs3 a");
        $this->db->where("a.user", $this->user_data['username']);
        if ((int) $limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if($sortBy){
            $this->db->order_by($sortBy, $sortOrder);
        }else{
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }

            return $data;
        }else{
            return array();
        }
    }

    private function get_searched_client_outbox($search=null, $limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        $rowCount = 0;
        if($search){
            $filterFields = array( "a.id"," a.user","a.cp_no", "a.recipient", "a.msg", "a.mid", "a.status", "a.date_sent");
            $sql = "a.id, a.user, a.cp_no, a.recipient, a.msg, a.mid, a.status, a.date_sent";
            $this->db->select($sql);
            $this->db->from("gccsms.tblmsgs3 a");
            $this->db->where("a.user", $this->user_data['username']);
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search["value"], "both"); }
                else{ $this->db->or_like($field, $search["value"], "both"); }
            }
            $this->db->group_end();

            if ((int) $limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            //if($sortBy){
            //    $this->db->order_by($sortBy, $sortOrder);
            //}else{
                $this->db->order_by("a.id", "DESC");
            //}
            $query = $this->db->get();

            if($query->num_rows() > 0){
                
               $arrData = array();
            foreach($query->result() as $key => $rs){
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }

            return $data;
            }else{
                return array();
            }
        }else{
            return array();
        }
    }

    private function get_searched_client_outbox_count($search=null){
        $rowCount = 0;
        if($search){
            $filterFields = array( "a.id"," a.user","a.cp_no", "a.recipient", "a.msg", "a.mid", "a.status", "a.date_sent");
            $sql = "a.id, a.user, a.cp_no, a.recipient, a.msg, a.mid, a.status, a.date_sent";
            $this->db->select($sql);
            $this->db->from("gccsms.tblmsgs3 a");
            $this->db->where("a.user", $this->user_data['username']);
           $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search["value"], "both"); }
                else{ $this->db->or_like($field, $search["value"], "both"); }
            }
            $this->db->group_end();
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function uploadRecipients(){
        $resultset = array();
        $employeeId = $this->user_data['emp_id'];
        if($employeeId){

        $filePath = "./uploads/files/recipients/employee_files/empcode_{$employeeId}/sms";

        $createFilePath = false;

        if (!file_exists($filePath)) {
            $mkdir = mkdir($filePath, 0777, true);
            if ($mkdir){ $createFilePath = true; }
        }else{ $createFilePath = true; }

        if($createFilePath == false){
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
            $resultset["toastr_state"] = "warning";
        }else{
            $config = array();
            $config['upload_path']          = $filePath;
            $config['allowed_types']        = 'csv|CSV';
            $config['max_size']             = 100000;
            $config['create_thumbnail']     = true;
            
            $session = $this->core_layout->getCurrentSession();
            $data = $this->file_upload->uploadFile($config);
            
            if($data["response"] == true){
              $filename = is_array($data["files"]) && count($data["files"]) > 0 ? $data["files"][0]["file_name"]: $data["files"];
                if($filename){
                    $resultset["response"] = true;
                    $resultset["added_file"] = base_url("uploads/files/recipients/employee_files/empcode_{$employeeId}/sms/{$filename}");
                    $resultset["render_file"] = $filename;
                    $resultset["toastr_msg"] = "Upload file successful.";
                    $resultset["toastr_state"] = "success";
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "File upload to specific path failed!";
                    $resultset["toastr_state"] = "error";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "File upload failed!";
                $resultset["toastr_state"] = "error";
            }
        }
    }else{
        $resultset["response"] = false;
        $resultset["toastr_msg"] = "User data not found!";
        $resultset["toastr_state"] = "error";
    }
        return $resultset;
    }

    function importUploads(){
      $post = $this->input->post();
      if (empty($post['filePath'])) {
          return "Error: File path is missing or empty.";
      }
      $csv = $this->csvreader->parse_file($post['filePath']);
      $length = count((array)$csv);
      $result = $post; // Initialize $result with $post data
      $missing = array();
      $duplicates = array(); // Initialize an array to store duplicate entries
      foreach($csv as $v=>$key){
        
        if (empty($key['acc_no']) || empty($key['cp_no']) || empty($key['name'])) {
            $missing[] = $key;
            continue;
        }

        $str = preg_replace('/[^A-Za-z0-9\. -]/', '', $key['name']);
        $cp_no = substr($key['cp_no'], 0, 1) == '9' ? '0' . $key['cp_no'] : $key['cp_no'];
          if($this->checkDuplicate($key['acc_no']) == 0){
              $data = array(
                  'user' => $this->user_data['username'],
                  'cp_no' => $cp_no, // Use the potentially modified cp_no
                  'name' => $str,
                  'acc_no' => $key['acc_no'],
                  'data1' => $key['data1'],
                  'data2' => $key['data2'],
                  'data3' => $key['data3'],
              );   
              $this->db->insert('gccsms.tbltemp3', $data); 
          } else {
              // Store the duplicate entry in the duplicates array
              $duplicates[] = array(
                  'cp_no' => $cp_no,
                  'name' => $str
              );
          }

      }
      // Add the duplicates array to the $result array
      $result['duplicates'] = $duplicates;
      $result['missing'] = $missing;
      return $result;
  }

    function clientRecipients(){
        $resultset = array();
        $post = $this->input->post();
        $search = (isset($post["search"]) && $post["search"])? $post["search"]: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
        $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";
        
        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->get_client_recipients($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_client_recipients_count();
        }

        if($search){
            $rowData = $this->get_searched_client_recipients($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_client_recipients_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_client_recipients_count(){
        $this->db->from("gccsms.tbltemp3 a");
        $this->db->where("a.user",$this->user_data['username']);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    private function get_client_recipients($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        
        $sql = "*";
        $this->db->select($sql);
        $this->db->from("gccsms.tbltemp3 a");
        $this->db->where('a.user',$this->user_data['username']);
        if ((int) $limit >= 0) {
            $this->db->limit($limit, $offset);
        }

        if($sortBy){
            $this->db->order_by($sortBy, $sortOrder);
        }else{
            $this->db->order_by("a.id", "DESC");
        }
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }

            return $data;
        }else{
            return array();
        }
    }

    private function get_searched_client_recipients($search=null, $limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        $rowCount = 0;
        if($search){
            $filterFields = array( "a.id"," a.user","a.cp_no", "a.name", "acc_no", "data1", "data2", "data3");
            $sql = "a.id, a.user, a.cp_no, a.name, acc_no, data1, data2, data3";
            $this->db->select($sql);
            $this->db->from("gccsms.tbltemp3 a");
            $this->db->where("a.user",$this->user_data['username']);
            $this->db->group_start();

            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search["value"], "both"); }
                else{ $this->db->or_like($field, $search["value"], "both"); }
            }

            $this->db->group_end();

            if ((int) $limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            if($sortBy){
                $this->db->order_by($sortBy, $sortOrder);
            }else{
                $this->db->order_by("a.id", "DESC");
            }
            $query = $this->db->get();

            if($query->num_rows() > 0){
                
               $arrData = array();
            foreach($query->result() as $key => $rs){
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }

            return $data;
            }else{
                return array();
            }
        }else{
            return array();
        }
    }

    private function get_searched_client_recipients_count($search=null){
        $rowCount = 0;
        if($search){
            $filterFields = array( "a.id"," a.user","a.cp_no", "a.name", "acc_no", "data1", "data2", "data3");
            $sql = "a.id, a.user, a.cp_no, a.name, acc_no, data1, data2, data3";
            $this->db->select($sql);
            $this->db->from("gccsms.tbltemp3 a");
            $this->db->where("a.user",$this->user_data['username']);
           $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search["value"], "both"); }
                else{ $this->db->or_like($field, $search["value"], "both"); }
            }
            $this->db->group_end();
            $query = $this->db->get();

            $rowCount = $query->num_rows();
        }

        return $rowCount;
    }

    function deleteRecipient($id){
        return $this->db->query("DELETE FROM gccsms.tbltemp3 WHERE id= $id");
    }

    function removeAll(){
        return $this->db->query("DELETE FROM gccsms.tbltemp3");
    }

    function checkDuplicate($acc_no){
        $this->db->select("id");
        $this->db->from("gccsms.tbltemp3");
        $this->db->where("acc_no", $acc_no);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function clientSendMsg() 
	{
        $date= date("Y/m/d H:i:s");
		$msg = $this->input->post('message').' ';
		$head = $this->input->post('head').' ';
		$foot = $this->input->post('foot');
		$list = $this->get_recipients($this->user_data['username']);
        $sms = $this->sms_settings();
		$data = array();
		
		foreach($list as $arr) {
            $text_head = $head.$arr['name'].', ';
			    $url = 'http://'.$sms->sms_ip.':'.$sms->sms_port.'/sendmsg';
	        $user = '?user='.$sms->sms_user;
	        $passwd = '&passwd='.$sms->sms_pass;
	        $cat = '&cat=1';
	        $to = '&to='.$arr['cp_no'];
	        $text = '&text='.rawurlencode($text_head).rawurlencode($msg).rawurlencode($foot);
            $mid = file_get_contents($url.$user.$passwd.$cat.$to.$text);
	        $mid = substr($mid, 4);

			$row = array();
			$row[] = $arr['cp_no'];
			$row[] = $arr['name'];
			$data[] = $row;

			$data2 = array(
				'user' => $this->user_data['username'],
				'cp_no' => $arr['cp_no'],
				'recipient' => $arr['name'],
				'msg' => $msg,
                'mid' => $mid,
                'date_sent' => $date
				);
			$insert = $this->save($data2);
			$this->delete_temp($this->user_data['username']);
		}

		$output = array(
						"data" => $data,
				);
		//output to json format
		return $output;
    }

    public function clientSendMsgV2() 
	{
    $date = date("Y-m-d H:i:s");
    $template =  $this->input->post('template');
		$msg = $this->input->post('message').' ';;
		$head = $this->input->post('head').' ';
		$foot = $this->input->post('foot');
		$list = $this->get_recipients($this->user_data['username']);
		$sent = array();
        $missing = array();
        $notSent = array();
    if (empty($list)) {
      $output = array(
        "response" => "No Recipient Found",
    );
      return $output;
    }

		foreach($list as $arr) {
      $send="";
      $sendmsg = false;
      $search = array();
      $replace = array();
      $replacing = array();
      if ($template) {
        if (!empty($arr['data1']) && $arr['data1'] !== null) {
            $replace[] = $arr['data1'];
            $replacing[] = '~data1~';
        }
        if (!empty($arr['data2']) && $arr['data2'] !== null) {
            $replace[] = $arr['data2'];
            $replacing[] = '~data2~';
        }
        if (!empty($arr['data3']) && $arr['data3'] !== null) {
            $replace[] = $arr['data3'];
            $replacing[] = '~data3~';
        }
        if (strpos($msg, "~data1~") !== false) {
            $search[] = "~data1~";
        }
        if (strpos($msg, "~data2~") !== false) {
            $search[] = "~data2~";
        }
        if (strpos($msg, "~data3~") !== false) {
            $search[] = "~data3~";
        }
        if ($search == $replacing) {
          $newContent = str_replace($search, $replace, $msg);
          $sendmsg = true;
        }
        else{
          $sendmsg = false;
          $missing[] = $arr;
        }

    } else {
        $sendmsg = true;
        $newContent = $msg;
    }

            if ($sendmsg){
              $text_head = $head . $arr['name'].', ';
              $text_msg = $text_head . $newContent . "\n\n" . $foot;
              $send = $this->contacts->sendSMS($arr['cp_no'],$text_msg);
              
              $row = array();
              $row["cp_no"] = $arr['cp_no'];
              $row["name"] = $arr['name'];

              if($send){
                $this->delete_temp($this->user_data['username'], $arr['name']);
                $this->core_layout->setEventLog("Messaging: Client - send sms to ".($arr['name']=='' ? $arr['cp_no'] : $arr['name']),"send", "success", "gccsms", "user");
                $sent[] = $row;
                }else{
                    $notSent = $row;
                }
            }

			$data2 = array(
				'user' => $this->user_data['username'],
				'cp_no' => $arr['cp_no'],
				'recipient' => $arr['name'],
				'msg' => $msg,
                'status' => $send ? "0" : "",
                'date_sent' => $date
				);
			 $insert = $this->save($data2);
		}
		$output = array(
            "sent" => $sent,
            "missing"=>$missing,
            "not sent"=>$notSent,
            "response" => "Ok",
        );
		return $output;
    }
    
    function get_recipients($username){
        $query = $this->db->query("SELECT * FROM gccsms.tbltemp3 a WHERE a.user = '$username'");
        return $query->result_array();
    }

    function sms_settings(){
        $query = $this->db->query("SELECT * FROM gccsms.tblsms");
        return $query->row();
    }

    function save($data){
        $this->db->insert("gccsms.tblmsgs3", $data);
		return $this->db->insert_id();
    }

    function delete_temp($user, $name){
        return $this->db->query("DELETE FROM gccsms.tbltemp3 WHERE user='$user' AND name='$name'");
    }

    public function clientResend($id) 
	{
        $date= date("Y/m/d H:i:s");
        $resend = $this->get_message($id);
		    $msg = $resend['msg'].' ';
        $foot = "This is a computer generated message. Please do not reply";
        $sms = $this->sms_settings();
		    $data = array();
			
		$text_head = 'Hi '.$resend['recipient'] . ', ';
        $text_msg = $text_head . $msg . "\n\n" . $foot;

        $send = $this->contacts->sendSMS($resend['cp_no'], $text_msg);
        if($send){
            $this->updateSendStatus($id);
            $this->core_layout->setEventLog("Messaging: Client - resend sms to ".($resend['recipient']=='' ? $resend['cp_no'] : $resend['recipient']),"resend", "success", "gccsms", "user");
            return true;
        } else {
            return false;
        }
    }

    function updateSendStatus($id){
        $query = $this->db->query("UPDATE gccsms.tblmsgs3 a SET a.status='0' WHERE a.id='$id'");
        return $query;
    }

    function get_message($id){
        $query = $this->db->query("SELECT * FROM gccsms.tblmsgs3 a WHERE a.id = '$id'");
        return $query->row_array();
    }
}