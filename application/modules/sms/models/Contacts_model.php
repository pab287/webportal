<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Contacts_model extends CI_Model
    {

        function __construct()
        {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->load->model('gcctime/shift_management_model', 'shift_management');
            $this->user_data = $this->session->userdata("logged_in");
        }

        private function getUserData()
        {
            return $this->core_layout->getUserLoggedIn();
        }

        function getContactsCollection()
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
                $rowData = $this->get_all_contacts($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_contacts_count();
            }

            if ($search) {
                $rowData = $this->get_searched_contacts($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_contacts_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_contacts_count()
        {
            $this->db->from("gccsms.tblcontacts");
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_contacts($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {
            $sql = "a.id, CONCAT(a.firstname,' ',a.lastname) AS name, a.cp_no, a.category";
            $this->db->select($sql);
            $this->db->from("gccsms.tblcontacts a");

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

        private function get_searched_contacts($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.firstname", "a.lastname", "a.cp_no", "a.category");
                $sql = "a.id, CONCAT(a.firstname,' ',a.lastname) AS name, a.cp_no, a.category";
                $this->db->select($sql);
                $this->db->from("gccsms.tblcontacts a");

                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search["value"], "both");
                    } else {
                        $this->db->or_like($field, $search["value"], "both");
                    }
                }

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

        private function get_searched_contacts_count($search = null)
        {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.firstname", "a.lastname", "a.cp_no", "a.category");
                $sql = "a.id, CONCAT(a.firstname,' ',a.lastname) AS name, a.cp_no, a.category";
                $this->db->select($sql);
                $this->db->from("gccsms.tblcontacts a");

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

        public function save_contact($data)
        {
            $resultarray = array();
            if($this->checkDuplicate($data['cp_no']) == 0){
                
                $query = $this->db->insert('gccsms.tblcontacts', $data);
                if($query){
                    $resultarray["status"] = TRUE;
                    $resultarray["msg"] = "Successfully saved.";
                    $this->core_layout->setEventLog("Masterfile: Contacts - added ".($data['firstname']." ".$data['lastname']),"insert", "success", "gccsms", "user");
                }else{
                    $resultarray["status"] = FALSE;
                    $resultarray["msg"] = "Error saving.";
                    $this->core_layout->setEventLog("Masterfile: Contacts - tried to add ".($data['firstname']." ".$data['lastname']),"insert", "error", "gccsms", "user");
                }
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Mobile no. already exist";
            }
            
            return $resultarray;
        }

        function checkDuplicate($cp_no, $where=null){
            $this->db->select("id");
            $this->db->from("gccsms.tblcontacts");
            $this->db->where("cp_no", $cp_no);
            if ($where) { $this->db->where("id !=", $where); }
            $query = $this->db->get();
            return $query->num_rows();
        }

        public function edit_contact($id)
        {
            $sql = "a.id, a.firstname, a.lastname, a.cp_no, a.category";

            $this->db->select($sql);
            $this->db->from("gccsms.tblcontacts a");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_contact($where, $data)
        {
            $resultarray = array();

            $contactsDetails = $this->getContactDetails($where["id"]);
            $name = $contactsDetails["name"]." into ".($data['firstname']." ".$data['lastname']);
            $number = $contactsDetails["cp_no"]." into ".$data['cp_no'];

            if($this->checkDuplicate($data['cp_no'], $where["id"]) == 0){
                
                $query = $this->db->update('gccsms.tblcontacts', $data, $where);
                if($query){
                    $resultarray["status"] = TRUE;
                    $resultarray["msg"] = "Successfully saved.";
                    $this->core_layout->setEventLog("Masterfile: Contacts - updated name of ".$name." and number from ".$number,"update", "success", "gccsms", "user");
                }else{
                    $resultarray["status"] = FALSE;
                    $resultarray["msg"] = "Error saving.";
                    $this->core_layout->setEventLog("Masterfile: Contacts - updated name of ".$name." and number from ".$number,"update", "error", "gccsms", "user");
                }
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Mobile no. already exist";
            }
            
            return $resultarray;
        }

        function getContactDetails($id){
            $this->db->select("concat(firstname,' ',lastname) as name, cp_no");
            $this->db->from("gccsms.tblcontacts");
            $this->db->where("id", $id);
            $query = $this->db->get();
            return $query ? $query->row_array() : array();
        }

        public function delete_contact($data)
        {
            $resultarray = array();

            $this->db->where('id', $data['id']);
            $query = $this->db->delete('gccsms.tblcontacts');
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully deleted.";
                $this->core_layout->setEventLog("Masterfile: Contacts - deleted ".$data['name'],"delete", "success", "gccsms", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error deleting.";
                $this->core_layout->setEventLog("Masterfile: Contacts - tried to delete ".$data['name'],"delete", "error", "gccsms", "user");
            }
            return $resultarray;
        }

        function uploadRecipientsContacts()
        {
            $resultset = array();
            $employeeId = $this->user_data['emp_id'];
            if ($employeeId) {

                $filePath = "./uploads/files/recipients/employee_files/empcode_{$employeeId}/sms";

                $createFilePath = false;

                if (!file_exists($filePath)) {
                    $mkdir = mkdir($filePath, 0777, true);
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
                    $config['upload_path'] = $filePath;
                    $config['allowed_types'] = 'csv|CSV';
                    $config['max_size'] = 100000;
                    $config['create_thumbnail'] = true;

                    $session = $this->core_layout->getCurrentSession();
                    $data = $this->file_upload->uploadFile($config);

                    if ($data["response"] == true) {
                      $filename = is_array($data["files"]) && count($data["files"]) > 0 ? $data["files"][0]["file_name"]: $data["files"];
                        if ($filename) {
                            $resultset["response"] = true;
                            $resultset["added_file"] = base_url("uploads/files/recipients/employee_files/empcode_{$employeeId}/sms/{$filename}");
                            $resultset["render_file"] = $filename;
                            $resultset["toastr_msg"] = "Upload file successful.";
                            $resultset["toastr_state"] = "success";
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "File upload to specific path failed!";
                            $resultset["toastr_state"] = "error";
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload failed!";
                        $resultset["toastr_state"] = "error";
                    }
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "User data not found!";
                $resultset["toastr_state"] = "error";
            }
            return $resultset;
        }

        function importUploadsContacts(){
          $data = array(); 
          $duplicate = array();
          $savedContacts = array(); 
          $status = 'failed';
          $missing = array();
          $filepath = $this->input->get();
          if(isset($filepath)){
              $csv = $this->csvreader->parse_file($filepath['filePath']);
              $length = count((array)$csv);
              if($length <= 100){
                  foreach ($csv as $v => $key) {

                    if (empty($key['firstname']) || empty($key['lastname']) || empty($key['cp_no'])) {
                      $missing[] = $key;
                      continue;
                  }

                      $str_firstname = preg_replace('/[^A-Za-z0-9\. -]/', '', $key['firstname']);
                      $str_lastname = preg_replace('/[^A-Za-z0-9\. -]/', '', $key['lastname']);
                      if($this->checkDuplicate($key['cp_no']) == 0){
                          $contactData = array(
                              'firstname' => $str_firstname,
                              'lastname' => $str_lastname,
                              'cp_no' => $key['cp_no'],
                          );
                          $query = $this->db->insert('gccsms.tblcontacts', $contactData);
                          if($query){
                              $this->core_layout->setEventLog("Masterfile: Contacts - added ".($contactData['firstname']." ".$contactData['lastname'])." through import","insert", "success", "gccsms", "user");
                              $savedContacts[] = $contactData; // Add the saved contact to the array
                          }
                      }
                      else {
                          $duplicate[] = $key;
                      }
                  }
                  $status = 'Done'; // Set status to 'done' if the loop completes without errors
              }
              else {
                $data['response'] = 'Data must not exceed by 100';
            }
          }
          else {
            $data['response'] = 'File path not found.';
        }
      
          // Assign the final result to $data
          $data = array(
              'response' => $status,
              'savedContacts' => $savedContacts,
              'duplicates' => $duplicate,
              'missing' => $missing
          );
      
          return $data; // Return the final result as $data
      }

        public function test()
        {
            //$currentAbsent = $this->shift_management->getCurrentAbsent();
            //echo json_encode($currentAbsent["check_absent"]);

            //return $this->user_data;


            //return $this->sendSMS("09564650691", "Test send sms zero !", true);
            
            //return $this->authenticate->getRoleId();

            return $this->sms_settings();
        }

        // function httpRequest($url){
        //     $response = false;
        //     try{
        //         $pattern = "/http...([0-9a-zA-Z-.]*).([0-9]*).(.*)/";
        //         preg_match($pattern, $url, $args);
        //         $in = "";
        //         $ipAddress = $args[1];
        //         $port = $args[2];
        //         $parameters = $args[3];
        
        //         $fp = @fsockopen($ipAddress, $port, $errno, $errstr, 30);
        //         if (!$fp) {
        //             $response = false;
        //         } else {
        //             $out = "GET /$parameters HTTP/1.1\r\n";
        //             $out .= "Host: $ipAddress:$port\r\n";
        //             $out .= "User-agent: Ozeki PHP client\r\n";
        //             $out .= "Accept: */*\r\n";
        //             $out .= "Connection: Close\r\n\r\n";
             
        //             fwrite($fp, $out);
        //             while (!feof($fp)) {
        //                $in .= "<br>".fgets($fp, 128);
        //             }
        //             fclose($fp);
        //             $response = true;
        //         }
        //     }catch(Exception $e){
        //         $response = false;
        //     }
        //     return $response;
        // }
             
        function _sendSMS($phone, $msg, $debug=false){
          $sms = $this->sms_settings();
          $response = false;
          
          if($sms && $phone){
              $sms_no =$sms['sms_no'];
              $user = $sms['sms_user'];
              $password = $sms['sms_pass'];
              $playsms_url = "https://" . $sms['sms_ip'] . ":" . $sms['sms_port'] . "/index.php?app=ws";
              $url = '&u='.$user;
              $url.= '&h='.$password;
              $url.= '&op=pv';
              $url.= '&from='.$sms_no;
              $url.= '&to='.urlencode($phone);
              $url.= '&msg='.urlencode($msg);
          
              $urltouse =  $playsms_url.$url;
              if ($debug) { echo "Request: <br>$urltouse<br><br>"; }
              $arrContextOptions=array(
                "ssl"=>array(
                     "verify_peer"=>false,
                     "verify_peer_name"=>false,
                ),
            );
              $response=file_get_contents($urltouse,false,stream_context_create($arrContextOptions));
              $response['url']=$urltouse;
              if ($debug) {
                  echo "Response: <br><pre>".
                  str_replace(array("<",">"),array("&lt;","&gt;"),$response).
                  "</pre><br>"; }
      
          }else{
              return "wala nagsend";
          }
      
          return($response);
      }

      public function sendSMS($phone, $msg, $debug=false){
        $sms = $this->sms_settings();
        $response[] = array();
        if($sms && $phone){

            if (substr($phone, 0, 1) === '9') {
                $phone = '0' . $phone;
            }

            $user = $sms['sms_user'];
            $password = $sms['sms_pass'];
            $playsms_url = "https://" . $sms['sms_ip'] . ":" . $sms['sms_port'] . "/index.php?app=ws";
            $url = '&u='.$user;
            $url.= '&h='.$password;
            $url.= '&op=pv';
            $url.= '&smsc='.$sms['modem'];
            $url.= '&to='.$phone;
            $url.= '&msg='.urlencode($msg);
            $urltouse =  $playsms_url.$url;
            if ($debug) { echo "Request: <br>$urltouse<br><br>"; }
            $arrContextOptions=array(
              "ssl"=>array(
                   "verify_peer"=>false,
                   "verify_peer_name"=>false,
              ),
          );
            $response['data']=file_get_contents($urltouse,false,stream_context_create($arrContextOptions));
            $response['status']=true;
            // $response['urls']= $urltouse; -> for testing only
            if ($debug) {
                echo "Response: <br><pre>".
                str_replace(array("<",">"),array("&lt;","&gt;"),$response).
                "</pre><br>"; }
    
        }else{
            return false;
        }
    
        return($response);
    }

        private function sms_settings(){
            $this->db->select("modem, sms_ip, sms_port, sms_user, sms_pass, department_id, exclude");
            $this->db->from("gccsms.tblsms");
            $this->db->where("is_connected",'1');
            $query = $this->db->get();
            if($query->num_rows() > 0){
                foreach($query->result_array() as $_query){
                    if($_query['sms_user'] == 'VoP' && !isset($this->user_data) || $this->authenticate->getRoleId() == "1"){
                        return $_query; // for cron job VOP
                    }
                    if($this->is_serial($_query['department_id']) && $_query['exclude'] == 0){
                        foreach(unserialize($_query['department_id']) as $id){
                            if($this->user_data['department'] == $id){
                                return $_query;
                            }
                        }
                    }
                }
            }
            return  array();
        }
        
        public static function is_serial($string) {
            return (@unserialize($string) !== false);
        }
    }
