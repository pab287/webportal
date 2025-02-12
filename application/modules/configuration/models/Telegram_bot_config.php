<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Telegram_bot_config extends CI_Model{

    private $telegramConfigTable = "gccmaster.telegram_config";

	function __construct(){
        parent::__construct();
    }

    public function getTelegramDatatableRequest(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"9", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $rowData = $this->getDatatableRequest($search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->getDatatableRequestCount($search);
        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;
        return $resultset;

    }

    private function getDatatableRequest($search, $limit, $offset, $sortBy, $sortOrder){
        $filterFields = array('a.bot_name');
        $this->db->select("a.id, a.bot_name, a.bot_description, a.owner_id, a.status, a.modules, a.chat_id, a.telegram_bot_token, a.created_at");
        $this->db->from($this->telegramConfigTable.' as a');
        $this->db->where('a.is_archive', 0);

        if (isset($search)) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();
        return $query->result();

    }

    private function getDatatableRequestCount($search){
        $filterFields = array('a.bot_name');
        $this->db->select("a.id, a.bot_name, a.bot_description, a.owner_id, a.status, a.modules, a.chat_id, a.telegram_bot_token, a.created_at");
        $this->db->from($this->telegramConfigTable.' as a');
        $this->db->where('a.is_archive', 0);

        if (isset($search)) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
            }
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function setTelegramProtocolSettings() {
        $post = $this->input->post();
        unset($post['csrf_token']);
        $post['status'] = 1;
        $post['modules'] = serialize($post['modules']);
        $added = $this->db->insert($this->telegramConfigTable, $post);
        if ($added) {
            $message = "Added new Telegram bot: {$post['bot_name']}";
            $success = "success";
        } else {
            $message = "Failed to add new Telegram bot: {$post['bot_name']}";
            $success = "error"; // Changed to "error" to reflect the failure
        }
        $result = [
            "response" => $added,
            "user" => "user",
            "success" => $success,
            "message" => $message
        ];
        $this->core_layout->setEventLog($message, "insert", $success, "gccmaster", "user");
        return $result;
    }

    public function select2OwnerData(){
        $query = $this->db->query("SELECT  c.id, CONCAT(c.firstname,' ',c.lastname) as emp_name FROM gccmaster.tblusers b, gccmaster.tblemployees c WHERE b.emp_id=c.id AND b.group_id='1' AND c.employee_status = 'Active' AND b.group_id=1 ORDER BY c.firstname ASC");
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

    public function select2ModuleData(){
        $this->db->select('id as id, label as text');
        $this->db->from('gccmaster.modules');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return  $query->result();
    }

    
}