<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Corporate_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("access_control_model", "acl_model");
        $this->load->model("datatable_model", "dt_model");
        $this->load->model('Contacts_model', 'contacts');
        $this->user_data = $this->session->userdata("logged_in");
    }

    private function getUserData()
    {
        return $this->core_layout->getUserLoggedIn();
    }

    function corporateOutbox()
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
            $rowData = $this->get_corporate_outbox($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_corporate_outbox_count();
        }

        if ($search) {
            $rowData = $this->get_searched_corporate_outbox($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_corporate_outbox_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_corporate_outbox_count()
    {
        $this->db->from("gccsms.tblmsgs2 a");
        $this->db->where("a.user", $this->user_data['username']);
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_corporate_outbox($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $sql = "a.id, a.user, a.cp_no, a.recipient, a.msg, a.mid, a.status, a.date_sent";
        $this->db->select($sql);
        $this->db->from("gccsms.tblmsgs2 a");
        $this->db->where("a.user", $this->user_data['username']);
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

    private function get_searched_corporate_outbox($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", " a.user", "a.cp_no", "a.recipient", "a.msg", "a.mid", "a.status", "a.date_sent");
            $sql = "a.id, a.user, a.cp_no, a.recipient, a.msg, a.mid, a.status, a.date_sent";
            $this->db->select($sql);
            $this->db->from("gccsms.tblmsgs2 a");
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

            if ((int)$limit >= 0) {
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

    private function get_searched_corporate_outbox_count($search = null)
    {
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", " a.user", "a.cp_no", "a.recipient", "a.msg", "a.mid", "a.status", "a.date_sent");
            $sql = "a.id, a.user, a.cp_no, a.recipient, a.msg, a.mid, a.status, a.date_sent";
            $this->db->select($sql);
            $this->db->from("gccsms.tblmsgs2 a");
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

    function Group()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT id, group_name FROM gccsms.tblcontactgroups WHERE group_name LIKE '%{$get['q']}%' ORDER BY id ASC");
        } else {
            $query = $this->db->query("SELECT id, group_name FROM gccsms.tblcontactgroups ORDER BY id ASC");
        }
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["group_name"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    function Contact()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT id, CONCAT(firstname,' ',lastname) as name FROM gccsms.tblcontacts WHERE CONCAT(firstname,' ',lastname) LIKE '%{$get['q']}%' ORDER BY CONCAT(firstname,' ',lastname) ASC");
        } else {
            $query = $this->db->query("SELECT id, CONCAT(firstname,' ',lastname) as name FROM gccsms.tblcontacts ORDER BY CONCAT(firstname,' ',lastname) ASC");
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

    function Template()
    {
        $get = $this->input->get();
        $resultarray = array();
        if (isset($get['q'])) {
            $query = $this->db->query("SELECT id, template_name FROM gccsms.tbltemplates WHERE template_name LIKE '%{$get['q']}%' ORDER BY template_name ASC");
        } else {
            $query = $this->db->query("SELECT id, template_name FROM gccsms.tbltemplates ORDER BY template_name ASC");
        }
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["template_name"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    function addGroupTable($id)
    {
        $this->db->select("CONCAT(a.firstname,' ',a.lastname) AS name, a.cp_no");
        $this->db->from("gccsms.tblcontacts a");
        $this->db->join("gccsms.tblgroupmembers b", "a.id=b.contact_id", "LEFT");
        $this->db->join("gccsms.tblcontactgroups c", "b.group_id=c.id", "LEFT");
        $this->db->where("c.id", $id);
        $query = $this->db->get();
        $result = $query->result_array();
        $length = count((array)$result);
        for ($i = 0; $i < $length; $i++) {

            if($this->checkDuplicate($result[$i]['cp_no'], $result[$i]['name'], $this->user_data['username']) == 0){
                $data = array(
                    'user' => $this->user_data['username'],
                    'cp_no' => $result[$i]['cp_no'],
                    'name' => $result[$i]['name']
                );
                $this->db->insert('gccsms.tbltemp2', $data);
            }
        }
        return $query->result_array();
    }

    function checkDuplicate($cp_no, $name, $user){
        $this->db->select("id");
        $this->db->from("gccsms.tbltemp2");
        $this->db->where("cp_no", $cp_no);
        $this->db->where("name", $name);
        $this->db->where("user", $user);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function addContactTable($id)
    {
        $length = count((array)$id['data']);
        foreach ($id as $v => $key) {
            for ($i = 0; $i < $length; $i++) {
                $this->db->select("a.id, CONCAT(a.firstname,' ',a.lastname) AS name, a.cp_no");
                $this->db->from("gccsms.tblcontacts a");
                $this->db->where("a.id", $key[$i]);
                $query = $this->db->get();
                $result[] = $query->row_array();

                if($this->checkDuplicate($result[$i]['cp_no'], $result[$i]['name'], $this->user_data['username']) == 0){
                    $data = array(
                        'user' => $this->user_data['username'],
                        'cp_no' => $result[$i]['cp_no'],
                        'name' => $result[$i]['name']
                    );
                    $this->db->insert('gccsms.tbltemp2', $data);
                }
            }
            return $result;
        }

    }

    function addNumberTable($id)
    {
        if($this->checkDuplicate($id, "", $this->user_data['username']) == 0){
            $data = array(
                'user' => $this->user_data['username'],
                'cp_no' => $id,
                'name' => ""
            );
            return $this->db->insert('gccsms.tbltemp2', $data);
        } else {
            return false;
        }
    }

    function addAllTable()
    {
        $this->db->select("CONCAT(a.firstname,' ',a.lastname) AS name, a.cp_no");
        $this->db->from("gccsms.tblcontacts a");
        $query = $this->db->get();
        $result = $query->result_array();
        $length = count((array)$result);
        for ($i = 0; $i < $length; $i++) {

            if($this->checkDuplicate($result[$i]['cp_no'], $result[$i]['name'], $this->user_data['username']) == 0){
                $data = array(
                    'user' => $this->user_data['username'],
                    'cp_no' => $result[$i]['cp_no'],
                    'name' => $result[$i]['name']
                );
                $this->db->insert('gccsms.tbltemp2', $data);
            }
        }
        return $query->result_array();
    }

    function addTemplateMessage($id)
    {
        $this->db->select("*");
        $this->db->from("gccsms.tbltemplates");
        $this->db->where("id", $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    function corporateRecipients()
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
            $rowData = $this->get_corporate_recipients($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_corporate_recipients_count();
        }

        if ($search) {
            $rowData = $this->get_searched_corporate_recipients($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_searched_corporate_recipients_count($search);
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_corporate_recipients_count()
    {
        $this->db->from("gccsms.tbltemp2 a");
        $this->db->where("a.user",$this->user_data['username']);
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_corporate_recipients($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {

        $sql = "*";
        $this->db->select($sql);
        $this->db->from("gccsms.tbltemp2 a");
        $this->db->where("a.user",$this->user_data['username']);

        $this->db->limit($limit, $offset);

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

    private function get_searched_corporate_recipients($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
    {
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", " a.user", "a.cp_no", "a.name");
            $sql = "a.id, a.user, a.cp_no, a.name";
            $this->db->select($sql);
            $this->db->from("gccsms.tbltemp2 a");
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
            $this->db->limit($limit, $offset);
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

    private function get_searched_corporate_recipients_count($search = null)
    {
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.id", " a.user", "a.cp_no", "a.name");
            $sql = "a.id, a.user, a.cp_no, a.name";
            $this->db->select($sql);
            $this->db->from("gccsms.tbltemp2 a");
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
        return $this->db->query("DELETE FROM gccsms.tbltemp2 WHERE id= $id");
    }

    function removeAll()
    {
        return $this->db->query("DELETE FROM gccsms.tbltemp2");
    }

    public function corporateSendMsg()
    {
        $date = date("Y/m/d H:i:s");
        $msg = $this->input->post('message') . ' ';
        $head = $this->input->post('header') . ' ';
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

    public function corporateSendMsgV2()
    {
        $date = date("Y-m-d H:i:s");
        $msg = $this->input->post('message') . ' ';
        $head = $this->input->post('header') . ' ';
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
                $this->core_layout->setEventLog("Messaging: Corporate - send sms to ".($arr['name']=='' ? $arr['cp_no'] : $arr['name']),"send", "success", "gccsms", "user");
            }

            $row = array();
            $row["cp_no"] = $arr['cp_no'];
            $row["name"] = $arr['name'];
            $row["status"] = $send;
            $data[] = $row;

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
            "data" => $data,
        );
        
        return $output;
    }

    function get_recipients($username)
    {
        $query = $this->db->query("SELECT * FROM gccsms.tbltemp2 a WHERE a.user = '$username'");
        return $query->result_array();
    }

    function sms_settings()
    {
        $query = $this->db->query("SELECT * FROM gccsms.tblsms");
        return $query->row();
    }

    function save($data)
    {
        $this->db->insert("gccsms.tblmsgs2", $data);
        return $this->db->insert_id();
    }

    function delete_temp($user, $name)
    {
        return $this->db->query("DELETE FROM gccsms.tbltemp2 WHERE user='$user' AND name='$name'");
    }

    public function corporateResend($id)
    {
        $date = date("Y/m/d H:i:s");
        $resend = $this->get_message($id);
        $msg = $resend['msg'] . ' ';
        $foot = "This is a computer generated message. Please do not reply";

        $text_head = 'Hi '.$resend['recipient'] . ', ';
        $text_msg = $text_head . $msg . "\n\n" . $foot;

        $send = $this->contacts->sendSMS($resend['cp_no'], $text_msg);
        if($send){
            $this->updateSendStatus($id);
            $this->core_layout->setEventLog("Messaging: Corporate - resend sms to ".($resend['recipient']=='' ? $resend['cp_no'] : $resend['recipient']),"resend", "success", "gccsms", "user");
            return true;
        } else {
            return false;
        }
    }

    function updateSendStatus($id){
        $query = $this->db->query("UPDATE gccsms.tblmsgs2 a SET a.status='0' WHERE a.id='$id'");
        return $query;
    }

    function get_message($id)
    {
        $query = $this->db->query("SELECT * FROM gccsms.tblmsgs2 a WHERE a.id = '$id'");
        return $query->row_array();
    }
}