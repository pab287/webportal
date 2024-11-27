<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Category_model extends CI_Model {
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

        function getCategoryList() {
            $query = $this->db->query("SELECT * FROM category ORDER BY id DESC");
            return array("data" => $query->result_array());
        }

        function addCategory() {
            $post = $this->input->post();
            $trailAction = "ADDED NEW CATEGORY ";
            $ctr = 1;
            $count = count($post);
            foreach ($post as $key => $value) {
                $trailAction .= " " . strtoupper($key) . ":" . strtoupper($value) . (intval($ctr) < $count ? ", " : " ");
                $ctr++;
            }

            $data = array();
            $data['name'] = $post['name'];
            $data['created_by'] = $this->getLoggedInUser()['id'];
            $data['status'] = 1;

            $insert = $this->db->insert("category", $data);

            if ($insert) {
                $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                $result["response"] = true;
                $result["toastr_msg"] = "Category has been saved.";
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "Error in saving category!";
            }

            return $result;
        }

        function getCategory() {
            $post = $this->input->post();
            $data = array();

            $query = $this->db->query("SELECT * FROM category WHERE id = {$post['id']}");

            if ($query) {
                $result["response"] = true;
                $result["data"] = $query->row_array();
                $result["toastr_msg"] = "Category has been saved.";
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "Error in saving category!";
            }

            return $result;
        }

        function editCategory() {
            $post = $this->input->post();
            $data = array();

            $trailAction = "UPDATED CATEGORY ";
            $ctr = 1;
            $count = count($post) - 1;
            foreach ($post as $key => $value) {
                if ($key === "id") continue;
                $trailAction .= " " . strtoupper($key) . ":" . strtoupper($value) . (intval($ctr) < $count ? ", " : " ");
                $ctr++;
            }

            $data['name'] = $post['name'];
            $data['status'] = $post['status'];
            $data['updated_by'] = $this->getLoggedInUser()['id'];
            $data['updated_at'] = date("Y-m-d H:i:s");

            $this->db->where("id", $post['id']);
            $update = $this->db->update("category", $data);

            if ($update) {
                $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                $result["response"] = true;
                $result["toastr_msg"] = "Category has been updated.";
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "Error in updating category!";
            }

            return $result;
        }

        function assignStocks($id = null) {
            $resultarray = array();
            $this->db->select("items.id, items.sku, items.name, items.category_id, unit.uom_desc");
            $this->db->from("items");
            $this->db->join("uom unit", "unit.id = items.unit", "LEFT");
            $query = $this->db->get();
            if ($query->num_rows() > 1) {
                foreach ($query->result_array() as $_query):

                    $data = array();
                    $data["sku"] = $_query["sku"];
                    $data["name"] = $_query["name"];
                    $data["unit"] = $_query["uom_desc"];

                    if ($_query["category_id"] == 0):
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
            $category = $this->db->get_where("category", array("id" => $post['cat_id']))->row();
            $trailAction = "ASSIGNED CATEGORY:$category->name TO ITEM SKU:$item->sku NAME:$item->name";

            $this->db->set('category_id', $post['cat_id']);
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
            $category = $this->db->select("cat.name category_name")
                ->join("category cat", "cat.id = items.category_id", "INNER")
                ->where("items.id", $post['id'])->get("items items")->row("category_name");
            $trailAction = "REMOVED CATEGORY:$category FROM ITEM SKU:$item->sku NAME:$item->name";

            $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
            $this->db->set('category_id', 0);
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