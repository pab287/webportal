<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Priority_model extends CI_Model {
        protected $loggedUser;
        protected $loggedUserName;

        function __construct() {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->loggedUser = $this->session->userdata('logged_in');
            $this->loggedUserName = $this->loggedUser["firstname"] . " " . $this->loggedUser["lastname"];
        }

        private function getLoggedInUser() {
            $userdata = $this->session->all_userdata();
            return $userdata['logged_in'];
        }

        function getPriorityList() {
            $query = $this->db->query("SELECT * FROM priority ORDER BY id DESC");
            return array("data" => $query->result_array());
        }

        function addPriority() {
            $post = $this->input->post();

            $trailAction = "ADDED NEW PRIORITY ";
            $ctr = 1;
            $count = count($post);
            foreach ($post as $key => $value) {
                $trailAction .= " " . strtoupper($key) . ":" . strtoupper($value) . (intval($ctr) < $count ? ", " : " ");
                $ctr++;
            }

            $data = array();
            $data['name'] = $post['name'];
            $data['description'] = $post['description'];
            $data['created_by'] = $this->getLoggedInUser()['id'];
            $data['status'] = 1;

            $insert = $this->db->insert("priority", $data);

            if ($insert) {
                $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                $result["response"] = true;
                $result["toastr_msg"] = "Priority has been saved.";
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "Error in saving priority!";
            }

            return $result;
        }

        function getPriority() {
            $post = $this->input->post();
            $data = array();

            $query = $this->db->query("SELECT * FROM `priority` WHERE id = {$post['id']}");

            if ($query) {
                $result["response"] = true;
                $result["data"] = $query->row_array();
                $result["toastr_msg"] = "Priority has been saved.";
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "Error in saving category!";
            }

            return $result;
        }

        function editPriority() {
            $post = $this->input->post();
            $data = array();

            $trailAction = "UPDATED PRIORITY ";
            $ctr = 1;
            $count = count($post) - 1;
            foreach ($post as $key => $value) {
                if ($key === "id") continue;
                $trailAction .= " " . strtoupper($key) . ":" . strtoupper($value) . (intval($ctr) < $count ? ", " : " ");
                $ctr++;
            }

            $data['name'] = $post['name'];
            $data['description'] = $post['description'];
            $data['status'] = $post['status'];
            $data['updated_by'] = $this->getLoggedInUser()['id'];
            $data['updated_at'] = date("Y-m-d H:i:s");

            $this->db->where("id", $post['id']);
            $update = $this->db->update("priority", $data);

            if ($update) {
                $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                $result["response"] = true;
                $result["toastr_msg"] = "Priority has been updated.";
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "Error in updating priority!";
            }

            return $result;
        }

        function assignStocksPrio($id = null) {
            $resultarray = array();
            $this->db->select("items.id, items.sku, items.name, items.priority_id, unit.uom_desc");
            $this->db->from("items");
            $this->db->join("uom unit", "unit.id = items.unit", "LEFT");
            $query = $this->db->get();
            if ($query->num_rows() > 1) {
                foreach ($query->result_array() as $_query):

                    $data = array();
                    $data["sku"] = $_query["sku"];
                    $data["name"] = $_query["name"];
                    $data["unit"] = $_query["uom_desc"];

                    if ($_query["priority_id"] == 0):
                        $data["action"] = "<button type='button' class='btn btn-success' data-cat-id='{$id}' data-id='{$_query['id']}' id='selectStock'>Select</button>";
                    else:
                        $data["action"] = "<button type='button' class='btn btn-danger' data-cat-id='{$id}' data-id='{$_query['id']}' id='deselectStock'>Deselect</button>";
                    endif;

                    $resultarray[] = $data;
                endforeach;
            } else {
                $resultarray[] = array();
            }


            return array("data" => $resultarray);
        }

        function selectStocks() {
            $post = $this->input->post();
            $resultarray = array();

            $item = $this->db->get_where("items", array("id" => $post["id"]))->row();
            $priority = $this->db->get_where("priority", array("id" => $post['prio_id']))->row();
            $trailAction = "ASSIGNED PRIORITY:$priority->name TO ITEM SKU:$item->sku NAME:$item->name";

            $this->db->set('priority_id', $post['prio_id']);
            $this->db->where('id', $post['id']);
            $query = $this->db->update('items');

            if ($query):
                $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                $resultarray["response"] = TRUE;
                $resultarray["msg"] = "Stock has been selected.";
            else:
                $resultarray["response"] = FALSE;
                $resultarray["msg"] = "Error processing request.";
            endif;

            return $resultarray;
        }

        function deselectStocks() {
            $post = $this->input->post();
            $resultarray = array();

            $item = $this->db->get_where("items", array("id" => $post["id"]))->row();
            $priority = $this->db->select("prio.name priority_name")
                ->join("priority prio", "prio.id = items.priority_id", "INNER")
                ->where("items.id", $post['id'])->get("items items")->row("priority_name");
            $trailAction = "REMOVED PRIORITY:$priority FROM ITEM SKU:$item->sku NAME:$item->name";

            $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
            $this->db->set('priority_id', 0);
            $this->db->where('id', $post['id']);
            $query = $this->db->update('items');
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

            if ($query):
                $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                $resultarray["response"] = TRUE;
                $resultarray["msg"] = "Stock has been deselected.";
            else:
                $resultarray["response"] = FALSE;
                $resultarray["msg"] = "Error processing request.";
            endif;

            return $resultarray;
        }

    }