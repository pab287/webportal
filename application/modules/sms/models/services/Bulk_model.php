<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Bulk_model extends CI_Model
    {
        protected $user_data;
        function __construct()
        {
            parent::__construct();
            include_once APPPATH . 'libraries/PHPExcel/IOFactory.php';
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->user_data = $this->session->userdata("logged_in");
            $this->load->model("core/upload_model", "file_upload");
            $this->load->library("Csvimport", "csvimport");
            $this->load->model('Contacts_model', 'contacts');
        }

        private function getUserData()
        {
            return $this->core_layout->getUserLoggedIn();
        }

        function bulkOutbox()
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
                $rowData = $this->get_bulk_outbox($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_bulk_outbox_count();
            }

            if ($search) {
                $rowData = $this->get_searched_bulk_outbox($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_bulk_outbox_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_bulk_outbox_count()
        {
            $this->db->from("gccsms.tblmsgs a");
            $this->db->where("a.user", $this->user_data['username']);
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_bulk_outbox($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.user, a.cp_no, a.recipient, a.msg, a.mid, a.status, a.created_dt, a.created_dt";
            $this->db->select($sql);
            $this->db->from("gccsms.tblmsgs a");
            $this->db->where("a.user", $this->user_data['username']);

            if ((int) $limit >= 0) {
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

        private function get_searched_bulk_outbox($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", " a.user", "a.cp_no", "a.recipient", "a.msg", "a.mid", "a.status", "a.created_dt");
                $sql = "a.id, a.user, a.cp_no, a.recipient, a.msg, a.mid, a.status, a.created_dt";
                $this->db->select($sql);
                $this->db->from("gccsms.tblmsgs a");
                $this->db->where("a.user", $this->user_data['username']);
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search["value"], "both");
                    } else {
                        $this->db->or_like($field, $search["value"], "both");
                    }
                }
                $this->db->group_end();

                if ((int) $limit >= 0) {
                    $this->db->limit($limit, $offset);
                }

                //if ($sortBy) {
                    //$this->db->order_by($sortBy, $sortOrder);
                //} else {
                    $this->db->order_by("a.id", "DESC");
                //}
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

        private function get_searched_bulk_outbox_count($search = null)
        {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", " a.user", "a.cp_no", "a.recipient", "a.msg", "a.mid", "a.status");
                $sql = "a.id, a.user, a.cp_no, a.recipient, a.msg, a.mid, a.status";
                $this->db->select($sql);
                $this->db->from("gccsms.tblmsgs a");
                $this->db->where("a.user", $this->user_data['username']);

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

        function uploadRecipients()
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
                    $config['allowed_types'] = 'xlsx|XLSX|xls|XLS';
                    // $config['allowed_types'] = 'csv|CSV|xlsx|XLSX|xls|XLS';
                    $config['max_size'] = 100000;
                    $config['create_thumbnail'] = true;
                    $data = $this->file_upload->uploadFile($config);
                    if ($data["response"] == true) {
                        $filename = is_array($data["files"]) && count($data["files"]) > 0 ? $data["files"][0]["file_name"]: $data["files"];
                            if ($filename) {
                                $resultset["response"] = true;
                                $resultset["added_file"] = base_url("uploads/files/recipients/employee_files/empcode_{$employeeId}/sms/{$filename}");
                                $resultset["render_file"] = $filename;
                                $resultset["full_path"] = $data["files"][0]["full_path"];
                                $resultset["toastr_msg"] = "Upload file successful.";
                                $resultset["toastr_state"] = "success";
                            } else {
                                $resultset["response"] = false;
                                $resultset["toastr_msg"] = "File upload to specific path failed!";
                                $resultset["toastr_state"] = "error";
                            }
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = $data['data']['error'];
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

        function importUploads(){
          $result = array();
          $duplicates = array(); 
          $post = $this->input->post();
          if (empty($post['filePath'])) {
              return "Error: File path is missing or empty.";
          }
          $fileee = explode('.', $post['renderFile']);
          $fileType = strtolower($fileee[1]);
          $result['filetype'] = $fileType;
          if ($fileType == 'xlsx' || $fileType == 'xls'){
            $objPHPExcel = PHPExcel_IOFactory::load($post['full_path']);
            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            array_shift($allDataInSheet);
            $result['success']=true;
            foreach ($allDataInSheet as $row) {
                $name = isset($row['A']) ? trim($row['A']) : '';
                $cp_no = isset($row['B']) ? (string)$row['B'] : '';
                $name = preg_replace('/[,\s]+/', ' ', $name);
                $cp_no = (float)$cp_no;
                if ($this->checkDuplicate($cp_no, $name, $this->user_data['username']) === 0) {
                    $data = array(
                        'user' => $this->user_data['username'],
                        'cp_no' => $cp_no,
                        'name' => $name
                    );
                    $this->db->insert('gccsms.tbltemp', $data);
                }else {
                    $duplicates[] = array(
                        'cp_no' => $cp_no,
                        'name' => $name
                    );
                }
            }
          }
          else{
            $result['success']=false;
            $result['message']="Please upload excel file";
          }
          $result['duplicates'] = $duplicates;
          return $result;
      }

        function bulkRecipients()
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
                $rowData = $this->get_bulk_recipients($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_bulk_recipients_count();
            }

            if ($search) {
                $rowData = $this->get_searched_bulk_recipients($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_bulk_recipients_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_bulk_recipients_count()
        {
            $this->db->from("gccsms.tbltemp a");
            $this->db->where("a.user",$this->user_data['username']);
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_bulk_recipients($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "*";
            $this->db->select($sql);
            $this->db->from("gccsms.tbltemp a");
            $this->db->where("a.user",$this->user_data['username']);
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

        private function get_searched_bulk_recipients($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", " a.user", "a.cp_no", "a.name");
                $sql = "a.id, a.user, a.cp_no, a.name";
                $this->db->select($sql);
                $this->db->from("gccsms.tbltemp a");
                $this->db->where("a.user",$this->user_data['username']);
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

        private function get_searched_bulk_recipients_count($search = null)
        {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", " a.user", "a.cp_no", "a.name");
                $sql = "a.id, a.user, a.cp_no, a.name";
                $this->db->select($sql);
                $this->db->from("gccsms.tbltemp a");
                $this->db->where("a.user",$this->user_data['username']);
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

        function deleteRecipient($id)
        {
            return $this->db->query("DELETE FROM gccsms.tbltemp WHERE id= $id");
        }

        function removeAll()
        {
            return $this->db->query("DELETE FROM gccsms.tbltemp");
        }

        function checkDuplicate($cp_no, $name, $user){
            $this->db->select("id");
            $this->db->from("gccsms.tbltemp");
            $this->db->where("cp_no", $cp_no);
            $this->db->where("name", $name);
            $this->db->where("user", $user);
            $query = $this->db->get();
            return $query->num_rows();
        }

        public function bulkSendMsg()
        {
            $msg = $this->input->post('msg') . ' ';
            $head = $this->input->post('head') . ' ';
            $foot = $this->input->post('foot');
            $list = $this->get_recipients($this->user_data['username']);
            $sms = $this->sms_settings();
            $data = array();

            foreach ($list as $arr) {
                $text_head = $head . $arr['name'] . ', ';
                $url = 'http://' . $sms->sms_ip . ':' . $sms->sms_port . '/sendmsg';
                $user = '?user=' . $sms->sms_user;
                $passwd = '&passwd=' . $sms->sms_pass;
                $cat = '&cat=1';
                $to = '&to=' . $arr['cp_no'];
                $text = '&text=' . rawurlencode($text_head) . rawurlencode($msg) . rawurlencode($foot);
                $mid = file_get_contents($url . $user . $passwd . $cat . $to . $text);
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

        public function bulkSendMsgV2()
        {
            $date = date("Y-m-d H:i:s");
            $head = $this->input->post('head') . ' ';
            $msg = $this->input->post('msg') . ' ';
            $foot = $this->input->post('foot');
            $list = $this->get_recipients($this->user_data['username']);
            $data = array();
            if (empty($list)) {
              $output = array(
                "response" => "No Recipient Found",
            );
              return $output;
            }
            foreach ($list as $arr) {
                
                $text_head = $head . $arr['name'] . ', ';
                $text_msg = $text_head . $msg . "\n\n" . $foot;

                $send = $this->contacts->sendSMS($arr['cp_no'], $text_msg);
                if($send){
                    $this->delete_temp($this->user_data['username'], $arr['name']);
                    $this->core_layout->setEventLog("Messaging: Bulk - send sms to ".($arr['name']=='' ? $arr['cp_no'] : $arr['name']),"send", "success", "gccsms", "user");
                }

                $row = array();
                $row['cp_no'] = $arr['cp_no'];
                $row['name'] = $arr['name'];
                $row["status"] = $send;
                $data[] = $row;

                $data2 = array(
                    'user' => $this->user_data['username'],
                    'cp_no' => $arr['cp_no'],
                    'recipient' => $arr['name'],
                    'msg' => $msg,
                    'status' => $send ? "0" : "",
                    'created_dt' => $date
                );
                $insert = $this->save($data2);
            }
            
            $output = array(
                "data" => $data,
            );

            return $output;
        }

        function get_recipients($username)
        {
            $query = $this->db->query("SELECT * FROM gccsms.tbltemp a WHERE a.user = '$username'");
            return $query->result_array();
        }

        function sms_settings()
        {
            $query = $this->db->query("SELECT * FROM gccsms.tblsms");
            return $query->row();
        }

        function save($data)
        {
            $this->db->insert("gccsms.tblmsgs", $data);
            return $this->db->insert_id();
        }

        function delete_temp($user, $name)
        {
            return $this->db->query("DELETE FROM gccsms.tbltemp WHERE user='$user' AND name='$name'");
        }

        function getFileExtension($filename) {
            $parts = explode('.', $filename);
            if (count($parts) > 1) {
                return end($parts);
            }
            return '';
        }

    }