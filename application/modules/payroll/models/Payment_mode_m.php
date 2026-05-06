<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_mode_m extends CI_Model {
    protected $tblMode = "payroll.payment_mode";
    protected $user_data;

    function __construct() {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        date_default_timezone_set("Asia/Manila");
    }

    function get_payment_mode_datatable() {
        $rowCount = 0;
        $rowData = array();
        $resultset = array();
        $order_val = array(array("column" => "1", "dir" => "desc"));

        $post = $this->input->post();
    
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
    
        $rowData = $this->get_item_list($search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_item_list_count($search);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_item_list($search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
        $filterFields = array("code", "description");
        
        $this->db->from('payroll.payment_mode');
        $this->db->where('is_archived', 0);

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

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }
        
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        
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

    private function get_item_list_count($search=null) {
        $filterFields = array("code", "description");
        
        $this->db->from('payroll.payment_mode');
        $this->db->where('is_archived', 0);

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
        $count = $query->num_rows();
        return $count;
    }

    public function save_payment_mode() {
        $result = array();
        $post = $this->input->post();
        
        if ($post) {
            $checkExisting = $this->db->get_where($this->tblMode, array("code" => $post["code"], "is_archived" => 0))->num_rows();
            if($checkExisting > 0){
                $result['state'] = false;
                $result['toastr_msg'] = "Payment mode code already exists. Please use a different code.";
                return $result;
            }

            $data = array(
                "code" => trim($post["code"]),
                "description" => trim($post["description"]),
                "created_by" => $this->user_data["emp_id"],
                "created_at" => date("Y-m-d H:i:s")
            );
    
            $query = $this->db->insert($this->tblMode, $data);
            if($query && $this->db->affected_rows() > 0){
                $result['state'] = true;
                $result['toastr_msg'] = "Payment mode successfully saved.";
            }else{
                $result['state'] = false;
                $result['toastr_msg'] = "Error saving payment mode. Please try again.";
            }
        } else {
            $result['state'] = false;
            $result['toastr_msg'] = "No data to process.";
        }

        return $result;
    }

    public function get_mode_data($id = 0){
        $result = array();

        if ($id) {
            $this->db->select('id, code, description');
            $query = $this->db->get_where($this->tblMode, array("id" => $id, "is_archived" => 0));
            if($query && $query->num_rows() > 0){
                $result = $query->row();
            }
        }

        return $result;
    }

    public function updated_payment_mode($id) {
        $result = array();

        if ($id) {
            $post = $this->input->post();

            $this->db->where("id !=", $id);
            $this->db->where("code", $post["code"]);
            $this->db->where("is_archived", 0);
            $checkExisting = $this->db->get($this->tblMode)->num_rows();
            
            if($checkExisting > 0){
                $result['state'] = false;
                $result['toastr_msg'] = "Payment mode code already exists. Please use a different code.";
                return $result;
            }

            $data = array(
                "code" => trim($post["code"]),
                "description" => trim($post["description"]),
                "updated_by" => $this->user_data["emp_id"],
                "updated_at" => date("Y-m-d H:i:s")
            );

            $this->db->where("id", $id);
            $query = $this->db->update($this->tblMode, $data);
            if($query && $this->db->affected_rows() > 0){
                $result['state'] = true;
                $result['toastr_msg'] = "Payment mode successfully updated.";
            }else{
                $result['state'] = false;
                $result['toastr_msg'] = "Error updating payment mode. Please try again.";
            }
        }

        return $result;
    }

    public function delete_payment_mode($id) {
        $result = array();

        if ($id) {
            $data = array(
                "is_archived" => 1,
                "archived_by" => $this->user_data["emp_id"],
                "archived_at" => date("Y-m-d H:i:s")
            );

            $this->db->where("id", $id);
            $query = $this->db->update($this->tblMode, $data);
            if($query && $this->db->affected_rows() > 0){
                $result['state'] = true;
                $result['toastr_msg'] = "Payment mode successfully archived.";
            }else{
                $result['state'] = false;
                $result['toastr_msg'] = "Error archiving payment mode. Please try again.";
            }
        }

        return $result;
    }

    public function select_payout_mode() {
        $result = array();
        $get = $this->input->get();

        $this->db->select('id, description as text');
        $this->db->where('is_archived', 0);

        if (isset($get['q']) && $get['q']) {
            $this->db->like('description', $get['q'], 'both');
        }

        if (!isset($get['q'])) { $this->db->limit(10); }

        $this->db->from($this->tblMode);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->result();
        }

        return array('results' => $result);
    }
}