<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Telegram_bot_config extends CI_Model{

    private $telegramConfigTable = "gccmaster.telegram_config";
    private $user_data;


	function __construct(){
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
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
        $archive = (isset($post["archive"]) && $post["archive"]) ? $post["archive"] : 0;
        $rowData = $this->getDatatableRequest($search, $limit, $offset, $sortBy, $sortOrder,$archive);
        $rowCount = $this->getDatatableRequestCount($search,$archive);
        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;
        return $resultset;

    }

    private function getDatatableRequest($search, $limit, $offset, $sortBy, $sortOrder,$archive){
        $filterFields = array('a.bot_name','b.firstname','b.lastname','a.modules_array');
        $this->db->select("a.id, a.bot_name, a.bot_description, b.firstname, b.lastname, a.owner_id, a.status, a.modules, a.chat_id, a.telegram_bot_token, a.created_at");
        $this->db->from($this->telegramConfigTable.' as a');
        $this->db->join('gccmaster.tblemployees as b', 'a.owner_id = b.id', 'left');
        $this->db->where('a.is_archive', $archive ? 1 : 0);

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
        if($sortBy[$i]['data'] == "owner"){
            $this->db->order_by("b.firstname", $sortOrder[0]['dir']);
        }
        else{
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }
        $query = $this->db->get();
        $results = $query->result();
        foreach ($results as $row) {
            if (!empty($row->modules)) {
                $modules = unserialize($row->modules);
                
                if (is_array($modules)) {
                    $this->db->select('label');
                    $this->db->from('gccmaster.modules');
                    $this->db->where_in('id', $modules);
                    
                    $row->modules = $this->db->get()->result();
                }
            }
            if (!empty($row->owner_id)) {
                $this->db->select('firstname, lastname, middlename');
                $this->db->from('gccmaster.tblemployees');
                $this->db->where('id', $row->owner_id);
                $row->owner = $this->db->get()->result();
            }
        }

        return $results;
    }

    private function getDatatableRequestCount($search,$archive){
        $filterFields = array('a.bot_name','b.firstname','b.lastname','a.modules_array');
        $this->db->select("a.id, a.bot_name, a.bot_description, a.owner_id, a.status, a.modules, a.chat_id, a.telegram_bot_token, a.created_at");
        $this->db->from($this->telegramConfigTable.' as a');
        $this->db->join('gccmaster.tblemployees as b', 'a.owner_id = b.id', 'left');
        $this->db->where('a.is_archive', $archive ? 1 : 0);

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
            $success = "error";
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

    public function archiveTelegramBot($id) {
        $this->db->where('id', $id);
        $updated = $this->db->update($this->telegramConfigTable, array('is_archive' => 1,'status' => 0));
        $botdata = $this->getTelegramBotById($id);
        if ($updated) {
            $message = "Archived Telegram bot: {$botdata['response']->bot_name}";
            $success = "success";
        } else {
            $message = "Failed to archive Telegram bot: {$botdata['response']->bot_name}";
            $success = "error";
        }
        $result = [
            "response" => $updated,
            "user" => "user",
            "success" => $success,
            "message" => $message
        ];
        $this->core_layout->setEventLog($message, "archive", $success, "gccmaster", "user");
        return $result;
    }

    public function restoreTelegramBot($id){
        $this->db->where('id', $id);
        $updated = $this->db->update($this->telegramConfigTable, array('is_archive' => 0));
        $botdata = $this->getTelegramBotById($id);
        if ($updated) {
            $message = "Restored Telegram bot: {$botdata['response']->bot_name}";
            $success = "success";
        } else {
            $message = "Failed to restore Telegram bot: {$botdata['response']->bot_name}";
            $success = "error"; 
        }
        $result = [
            "response" => $updated,
            "user" => "user",
            "success" => $success,
            "message" => $message
        ];
        $this->core_layout->setEventLog($message, "restore", $success, "gccmaster", "user");
        return $result;
    }

    public function toggleTelegramStatus($id) {
        $post = $this->input->post();
        $post['status'] = ($post['status'] == 1) ? 0 : 1;
        $this->db->where('id', $id);
        $updated = $this->db->update($this->telegramConfigTable, array('status' => $post['status']));
        $botdata = $this->getTelegramBotById($id);
        if ($updated) {
            $message = "Updated status of Telegram bot: {$botdata['response']->bot_name}";
            $success = "success";
        } else {
            $message = "Failed to update status of Telegram bot: {$botdata['response']->bot_name}";
            $success = "error"; 
        }
        $result = [
            "response" => $updated,
            "user" => "user",
            "success" => $success,
            "message" => $message
        ];
        $this->core_layout->setEventLog($message, "update", $success, "gccmaster", "user");
        return $result;
    }

    public function updateTelegramProtocolSettings() {
        $post = $this->input->post();
        $result = array();
        unset($post['csrf_token']);
        $currentData = $this->getTelegramBotById($post['id']);
        $post['modules'] = serialize($post['modules']);
        $post['updated_at'] = date("Y-m-d H:i:s");
        $post['updated_by'] = $this->user_data['emp_id'];
        $this->db->where('id', $post['id']);
        $updated = $this->db->update($this->telegramConfigTable, $post);
        unset($post['updated_at'],$post['id'],$post['updated_at']);
        $post['modules'] = @unserialize($post['modules']);
        $changes = $this->logChanges($currentData['response'],$post);
        if ($updated) {
            $message = "Updated Telegram bot: {$post['bot_name']} . $changes";
            $success = "success";
        } else {
            $message = "Failed to update Telegram bot: {$post['bot_name']}";
            $success = "error";
        }
        $result = [
            "response" => $updated,
            "user" => "user",
            "success" => $success,
            "message" => $message
        ];
        $this->core_layout->setEventLog($message, "update", $success, "gccmaster", "user");
        return $result;
    }

    public function getTelegramBotById($id){
        $this->db->select("a.id, a.bot_name, a.bot_description, a.owner_id, a.status, a.modules, a.chat_id, a.telegram_bot_token, a.created_at");
        $this->db->from($this->telegramConfigTable.' as a');
        $this->db->where('a.id', $id);
        $query = $this->db->get();
        $results = $query->row();
        $results->modules = @unserialize( $results->modules);
        return array('response'=>$results);
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

	private function logChanges($currentData, $newData) {
        if (is_object($currentData)) {
            $currentData = (array) $currentData;
        }
    
        if (is_object($newData)) {
            $newData = (array) $newData;
        }
		$changes = array();
		$changesString = '';
		foreach ($currentData as $field => $value) {
			if (isset($newData[$field]) && $newData[$field]!= $value) {
				$changes[$field] = array(
					'old' => $value,
					'new' => $newData[$field]
				);
			}
		}
        foreach ($changes as $field => $change) {
            if ($field != 'modules' && $field != 'modules_array') {
                $changesString.= " Field: $field, from: <strong>". $change['old']. "</strong>, to: <strong>". $change['new']. "</strong>\n";
            }
        }
        if (isset($newData['modules'])) {
            sort($newData['modules']);
            sort($currentData['modules']);
            if (empty($newData['modules'])) {
                $diff = array_diff($currentData['modules'], $newData['modules']);
            } else {
                $diff = array_diff($newData['modules'], $currentData['modules']);
            }
            if (!empty($diff)) {
                $changesString.= " Field: modules, from: ' <strong>". implode(',', $currentData['modules']). "</strong> ', to: <strong>'". implode(',', $newData['modules']). "'</strong>\n";
            }
        }
		return $changesString;
	}

    
}