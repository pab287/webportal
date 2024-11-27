<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Inventory_checking_model extends CI_Model {
        private $user;
        private $mailer = "gccphtest@gmail.com";
        private $mailerName = "gccphtest@gmail.com";
        private $loggedUser;
        private $loggedUserName;

        public function __construct() {
            parent::__construct();
            $this->user = $this->session->userdata("logged_in");
            $this->load->model("core/settings_model", "settings");
            $this->load->library('email');
            $this->loggedUser = $this->session->userdata('logged_in');
            $this->loggedUserName = $this->loggedUser["firstname"] . " " . $this->loggedUser["lastname"];

            date_default_timezone_set("Asia/Manila");
        }

        function generateNewPiRefNo() {
            $pi_ref_numbers = $this->db->select("ref_no")->get("physical_inventory")->result();
            $pi_ref_arrays = array();
            foreach ($pi_ref_numbers as $pi_ref_number) {
                $nArr = explode("-", $pi_ref_number->ref_no);
                $n = intval(array_pop($nArr));
                array_push($pi_ref_arrays, $n);
            }

            $max_int = 1;

            if (!empty($pi_ref_numbers)) {
                $max_int = max($pi_ref_arrays) + 1;
            }

            $ref_no = "PI-" . date('mdy') . "-" . str_pad($max_int, 4, "0", STR_PAD_LEFT);

            return $ref_no;
        }

        function getInventoryItems() {
            $term = isset($_POST["term"]) ? $_POST["term"] : "";
            $selectedItems = isset($_POST["selectedItems"]) ? $_POST["selectedItems"] : "";

            if (!empty($selectedItems)) {
                $this->db->where_not_in("id", $selectedItems);
            }

            $this->db->select("id, name as text, qty");
            $this->db->like("CONCAT(name,sku)", $term, "BOTH");
            return $this->db->get("items")->result();
        }

        function getItemListInfiniteScroll($offset, $size) {
            $search = $this->input->post("search");
            $exclude = $this->input->post("exclude");

            $this->db->group_start();
            if (!empty($exclude)) {
                $this->db->where_not_in("id", $exclude);
            }

            $this->db->where("status", 1);
            $this->db->group_end();

            $this->db->group_start();
            $this->db->like("name", $search, "both");
            $this->db->or_like("sku", $search, "both");
            $this->db->group_end();

            $this->db->limit($size, $offset);
            $this->db->order_by("name", "asc");
            $data = $this->db->get("items")->result();
            // return $this->db->last_query();
            return $data;
        }

        function savePhysicalInventory() {
            $id = $this->input->post("id");
            $qty = $this->input->post("qty");
            $count = $this->input->post("count");
            $variance = $this->input->post("variance");
            $ref_no = $this->input->post("ref_no");
            $remarks = $this->input->post("remarks");

            $this->db->trans_begin();

            $created_by = $this->session->userdata("logged_in")["emp_id"];
            $created_at = date("Y-m-d H:i:s");

            $m_data = array(
                "ref_no" => $ref_no,
                "remarks" => $remarks,
                "created_by" => $created_by,
                "created_at" => $created_at
            );
            $this->db->insert("physical_inventory", $m_data);
            $insert_id = $this->db->insert_id();

            if (!empty($id)) {
                foreach ($id as $i => $row) {
                    $c_data = array(
                        "pi_id" => $insert_id,
                        "item_id" => $row,
                        "qty" => $qty[$i],
                        "count" => $count[$i],
                        "variance" => $variance[$i],
                        "moving_variance" => $variance[$i],
                    );
                    $this->db->insert("physical_inventory_contents", $c_data);
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->core_layout->saveLog("CREATED PHYSICAL INVENTORY WITH REFERENCE NO.:$ref_no", $this->loggedUserName);
                $this->db->trans_commit();
                return true;
            }
        }

        function updatePhysicalInventory() {
            $m_id = $this->input->post("m_id");

            $id = $this->input->post("id");
            $content_id = $this->input->post("content_id");
            $qty = $this->input->post("qty");
            $count = $this->input->post("count");
            $variance = $this->input->post("variance");
            $ref_no = $this->input->post("ref_no");
            $remarks = $this->input->post("remarks");

            $this->db->trans_begin();

            $last_updated_by = $this->session->userdata("logged_in")["emp_id"];
            $last_updated_at = date("Y-m-d H:i:s");

            $m_data = array(
                "remarks" => $remarks,
                "last_updated_by" => $last_updated_by,
                "last_updated_at" => $last_updated_at
            );
            $this->db->where("id", $m_id);
            $this->db->update("physical_inventory", $m_data);

            $pi = $this->db->get_where("physical_inventory", array("id" => $m_id))->row();

            if (!empty($id)) {
                foreach ($id as $i => $row) {
                    if (empty($content_id[$i])) {
                        $c_data = array(
                            "pi_id" => $m_id,
                            "item_id" => $row,
                            "qty" => $qty[$i],
                            "count" => $count[$i],
                            "variance" => $variance[$i],
                            "moving_variance" => $variance[$i],
                        );
                        $this->db->insert("physical_inventory_contents", $c_data);
                    } else {
                        $c_data = array(
                            "count" => $count[$i],
                            "variance" => $variance[$i],
                            "moving_variance" => $variance[$i],
                        );

                        $this->db->where("id", $content_id[$i]);
                        $this->db->update("physical_inventory_contents", $c_data);
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->core_layout->saveLog("UPDATED PHYSICAL INVENTORY WITH REFERENCE NO.:$pi->ref_no", $this->loggedUserName);
                $this->db->trans_commit();
                return true;
            }
        }

        function getPhysicalInventoryList() {
            $result = array();
            $order = $this->input->post("order");
            $columns = $this->input->post("columns");
            $search = $this->input->post("search");
            $offset = $this->input->post("start");
            $limit = $this->input->post("length");
            $draw = $this->input->post("draw");
            $searchValue = $search["value"];
            $filter = $this->input->post("filter");

            if (!empty($order)) {
                $order_index = $order[0]["column"];
                $order_dir = $order[0]["dir"];
                $field = $columns[$order_index]["data"];
                $this->db->order_by($field, $order_dir);
            }

            if ((int)$limit >= 1) {
                $this->db->limit($limit, $offset);
            }

            if (intval($filter) >= 0) {
                $this->db->where("pi_inv.status", $filter);
            }

            $searchFields = "CONCAT(ref_no, remarks, CONCAT(emp.firstname, ' ', emp.lastname)," .
                " IF(pi_inv.confirmed_by IS NOT NULL, CONCAT(emp2.firstname, ' ', emp2.lastname), ''))";
            $this->db->like($searchFields, $searchValue, "BOTH");
            $this->db->select("pi_inv.*, CONCAT(emp.firstname, ' ', emp.lastname) created_by_name, 
                               CONCAT(emp2.firstname, ' ', emp2.lastname) verified_by, pi_inv.date_confirmed verified_date");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = pi_inv.created_by", "INNER");
            $this->db->join("gccmaster.tblemployees emp2", "emp2.id = pi_inv.confirmed_by", "LEFT");
            $q = $this->db->get("physical_inventory pi_inv");

            $result["data"] = $q->result();
            // $result["sql"] = $this->db->last_query();
            $result["recordsFiltered"] = $this->getPhysicalInventoryListTotal($searchValue);
            $result["recordsTotal"] = $this->getPhysicalInventoryListTotal($searchValue);
            $result["draw"] = $draw;

            return $result;
        }

        private function getPhysicalInventoryListTotal($searchValue) {
            $searchFields = "CONCAT(ref_no, remarks, CONCAT(emp.firstname, ' ', emp.lastname))";
            $this->db->like($searchFields, $searchValue, "BOTH");
            $this->db->select("pi_inv.*, CONCAT(emp.firstname, ' ', emp.lastname) created_by_name");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = pi_inv.created_by", "INNER");
            return $this->db->count_all_results("physical_inventory pi_inv");
        }

        function getPhysicalInventoryDetails($id) {
            $this->db->where("pi_inv.id", $id);
            $this->db->select("pi_inv.*, CONCAT(emp.firstname, ' ', emp.lastname) created_by_name, 
                               CONCAT(emp2.firstname, ' ', emp2.lastname) last_updated_by_name,
                               CONCAT(emp3.firstname, ' ', emp3.lastname) _confirmed_by");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = pi_inv.created_by", "INNER");
            $this->db->join("gccmaster.tblemployees emp2", "emp2.id = pi_inv.last_updated_by", "LEFT");
            $this->db->join("gccmaster.tblemployees emp3", "emp3.id = pi_inv.confirmed_by", "LEFT");
            $data["main"] = $this->db->get("physical_inventory pi_inv")->row();

            $this->db->reset_query();

            $this->db->select("items.sku, pi_contents.id content_id, pi_contents.item_id id, 
                               items.name name, pi_contents.qty, pi_contents.count, pi_contents.variance, pi_contents.status, 
                               pi_contents.status_remarks");
            $this->db->where("pi_contents.pi_id", $id);
            $this->db->join("items items", "items.id = pi_contents.item_id", "INNER");
            $data["contents"] = $this->db->get("physical_inventory_contents pi_contents")->result();

            return json_decode(json_encode($data));
        }

        function deletePhysicalInventoryContent($content_id) {
            $item = $this->db->select("items.sku, items.name, pi.ref_no")
                ->join("items", "items.id = pi_c.item_id", "INNER")
                ->join("physical_inventory pi", "pi.id = pi_c.pi_id", "INNER")
                ->where("pi_c.id", $content_id)
                ->get("physical_inventory_contents pi_c")->row();

            $this->db->reset_query();

            $this->db->where("id", $content_id);
            if ($this->db->delete("physical_inventory_contents")) {
                $this->core_layout->saveLog("DELETED ITEM SKI:$item->sku NAME:$item->name FORM PHYSICAL INVENTORY WITH REFERENCE NO.:$item->ref_no", $this->loggedUserName);
                return true;
            }
            return false;
        }

        function confirmPhysicalInventory($pi_id, $status) {
            $emp_id = $this->user["emp_id"];
            $mailSent = false;

            $this->db->trans_begin();

            $data = array("status" => $status, "confirmed_by" => $emp_id, "date_confirmed" => date("Y-m-d H:i:s"));
            $this->db->where("id", $pi_id);

            if ($this->db->update("physical_inventory", $data)) {
                if ((int)$status === 1) {
                    $this->db->where("pi_contents.pi_id", $pi_id);
                    $this->db->where("pi_contents.status", "valid");
                    $this->db->select("pi_contents.*, items.sku, items.name");
                    $this->db->join("physical_inventory_contents pi_contents", "items.id = pi_contents.item_id", "INNER");
                    $contents = $this->db->get("items items")->result();

                    $this->db->reset_query();

                    if (count($contents)) {
                        foreach ($contents as $content) {
                            $this->db->where("id", $content->item_id);
                            $this->db->set("qty", $content->count);
                            $this->db->update("items items");
                        }
                    }

                    $this->db->reset_query();
                    $email = $this->sendApprovedPhysicalInventoryEmail($pi_id);
                    $mailSent = $email;
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array("success" => false);
            } else {
                $pi = $this->db->get_where("physical_inventory", array("id" => $pi_id))->row();
                $statusStr = intval($status) === 1 ? "VERIFIED " : "CANCELLED ";
                $this->core_layout->saveLog($statusStr . "PHYSICAL INVENTORY WITH REFERENCE NO.:$pi->ref_no", $this->loggedUserName);
                $this->db->trans_commit();
                return array("success" => true, "mail" => $mailSent);
            }
        }

        function getPhysicalInventoriesForSelect($q) {
            $data = array();
            $this->db->like("ref_no", $q, "both");
            $this->db->where("status", 1);
            $this->db->select("id, ref_no `text`");
            $query = $this->db->get("physical_inventory");

            $data["results"] = $query->result();

            return $data;
        }

        function getItemVarianceFromPi($pi_id, $item_id) {
            $this->db->where("pi_id", $pi_id);
            $this->db->where("item_id", $item_id);
            return $this->db->get("physical_inventory_contents")->row();
        }

        function saveTransQtyUpdateRequest($trans_type, $trans_type_contents_id, $qty_current, $qty_change, $price_current, $price_change, $new_qty_remarks) {
            $this->db->trans_begin();

            $emp_id = $this->user["emp_id"];
            $field = $trans_type === "issuance" ? "issuance_id c_id, pi_contents_id" : "receiving_id c_id, pi_contents_id";
            $db = $trans_type === "issuance" ? "issuance_contents" : "receiving_contents";

            $this->db->select($field);
            $contents = $this->db->get_where($db, array("id" => $trans_type_contents_id))->row();
            $trans_type_id = $contents->c_id;
            $pi_contents_id = $contents->pi_contents_id;

            $this->db->reset_query();
            $diff = ($trans_type === "issuance" || !empty($pi_contents_id)) ? ($qty_current - $qty_change) : ($qty_change - $qty_current);

            $data = array(
                "trans_type" => $trans_type,
                "trans_type_id" => $trans_type_id,
                "trans_type_contents_id" => $trans_type_contents_id,
                "qty_current" => $qty_current,
                "qty_change" => $qty_change,
                "price_current" => $price_current,
                "price_change" => $price_change,
                "difference" => $diff,
                "remarks" => $new_qty_remarks,
                "created_by" => $emp_id
            );

            $this->db->insert("transaction_qty_update_history", $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->db->trans_commit();
                return true;
            }
        }

        function transactionUpdatesList() {
            $result = array();
            $post = $this->input->post();
            $start = $post["start"];
            $length = $post["length"];
            $order = $post["order"];
            $columns = $post["columns"];
            $search = $post["search"];
            $trans_type = $post["trans_type"];
            $where = array();

            if ((int)$length >= 1) {
                $this->db->limit($length, $start);
            }

            if (!empty($order)) {
                $colIndex = $order[0]["column"];
                $dir = $order[0]["dir"];
                $column = $columns[$colIndex]["data"];
                $this->db->order_by($column, $dir);
            }

            $searchField = "CONCAT(IF(ic.id IS NULL, r.reference_no, i.reference_no),
                                IF(ic.id IS NULL, `rc`.`item`, ic.item), 
                                `items`.`sku`, 
                                `items`.`name`,
                                CONCAT(emp.firstname, ' ', emp.lastname),
                                IF(CONCAT(emp2.firstname, ' ', emp2.lastname) IS NOT NULL, CONCAT(emp2.firstname, ' ', emp2.lastname), ''),
                                IF(trans_history.qty_current IS NOT NULL, trans_history.qty_current,''),
                                IF(trans_history.qty_change IS NOT NULL, trans_history.qty_change,''),
                                IF(trans_history.price_current IS NOT NULL, trans_history.price_current,''),
                                IF(trans_history.price_change IS NOT NULL, trans_history.price_change,''),
                                IF(trans_history.remarks IS NOT NULL, trans_history.remarks,''),
                                CASE 
                                    WHEN trans_history.`status`=1 THEN 'approved' 
                                    WHEN trans_history.`status`=2 THEN 'declined'
                                    WHEN trans_history.`status`=3 THEN 'cancelled'
                                    ELSE 'pending'
                                END)";

            $this->db->like($searchField, $search["value"], "both");
            if ($trans_type !== "all") {
                $where["trans_history.trans_type"] = $trans_type;
                $this->db->where($where);
            }

            $this->db->select("IF(ic.id IS NULL, rc.item, ic.item) item, items.sku, items.name, 
                               IF(ic.id IS NULL, rc.pi_contents_id, ic.pi_contents_id) pi_contents_id, 
                               CONCAT(emp.firstname, ' ', emp.lastname) employee,
                               CONCAT(emp2.firstname, ' ', emp2.lastname) _confirmed_by,
                               IF(ic.id IS NULL, r.reference_no, i.reference_no) reference_no,
                               trans_history.*");
            $this->db->join("issuance_contents ic", "ic.id = trans_history.trans_type_contents_id AND trans_history.trans_type='issuance'", "LEFT");
            $this->db->join("receiving_contents rc", "rc.id = trans_history.trans_type_contents_id AND trans_history.trans_type='receiving'", "LEFT");
            $this->db->join("issuance i", "i.id = ic.issuance_id", "LEFT");
            $this->db->join("receiving r", "r.id = rc.receiving_id", "LEFT");
            $this->db->join("tblemployees emp2", "emp2.id = trans_history.confirmed_by", "LEFT");
            $this->db->join("items", "IF(ic.id IS NULL, rc.item, ic.item) = items.id", "INNER", FALSE);
            $this->db->join("tblemployees emp", "emp.id = trans_history.created_by", "INNER", FALSE);
            $query = $this->db->get("transaction_qty_update_history trans_history");

            $result["data"] = $query->result();
            // $result["sql"] = $this->db->last_query();
            $result["recordsTotal"] = $this->transactionUpdatesListCount($search, $searchField, $where);
            $result["recordsFiltered"] = $this->transactionUpdatesListCount($search, $searchField, $where);
            return $result;
        }

        private function transactionUpdatesListCount($search, $searchField, $where) {
            if (!empty($where)) {
                $this->db->where($where);
            }

            $this->db->like($searchField, $search["value"], "both");
            $this->db->join("issuance_contents ic", "ic.id = trans_history.trans_type_contents_id AND trans_history.trans_type='issuance'", "LEFT");
            $this->db->join("receiving_contents rc", "rc.id = trans_history.trans_type_contents_id AND trans_history.trans_type='receiving'", "LEFT");
            $this->db->join("issuance i", "i.id = ic.issuance_id", "LEFT");
            $this->db->join("receiving r", "r.id = rc.receiving_id", "LEFT");
            $this->db->join("tblemployees emp2", "emp2.id = trans_history.confirmed_by", "LEFT");
            $this->db->join("items", "IF(ic.id IS NULL, rc.item, ic.item) = items.id", "INNER", FALSE);
            $this->db->join("tblemployees emp", "emp.id = trans_history.created_by", "INNER", FALSE);
            return $this->db->count_all_results("transaction_qty_update_history trans_history");
        }

        function confirmTransUpdateRequest() {
            $post = $this->input->post();

            $id = $post["id"];
            $item = $post["item"];
            $status = $post["status"];

            $this->db->trans_begin();

            // $trans = $this->db->get_where("transaction_qty_update_history", array("id" => $id))->row();
            $this->db->select("IF(ic.id IS NULL, rc.item, ic.item) item, items.sku, items.name, 
                               IF(ic.id IS NULL, rc.pi_contents_id, ic.pi_contents_id) pi_contents_id, 
                               CONCAT(emp.firstname, ' ', emp.lastname) employee,
                               CONCAT(emp2.firstname, ' ', emp2.lastname) _confirmed_by,
                               IF(ic.id IS NULL, r.reference_no, i.reference_no) reference_no,
                               trans_history.*");
            $this->db->join("issuance_contents ic", "ic.id = trans_history.trans_type_contents_id AND trans_history.trans_type='issuance'", "LEFT");
            $this->db->join("receiving_contents rc", "rc.id = trans_history.trans_type_contents_id AND trans_history.trans_type='receiving'", "LEFT");
            $this->db->join("issuance i", "i.id = ic.issuance_id", "LEFT");
            $this->db->join("receiving r", "r.id = rc.receiving_id", "LEFT");
            $this->db->join("tblemployees emp2", "emp2.id = trans_history.confirmed_by", "LEFT");
            $this->db->join("items", "IF(ic.id IS NULL, rc.item, ic.item) = items.id", "INNER", FALSE);
            $this->db->join("tblemployees emp", "emp.id = trans_history.created_by", "INNER", FALSE);
            $this->db->where("trans_history.id", $id);
            $trans = $this->db->get("transaction_qty_update_history trans_history")->row();

            $this->db->reset_query();

            $difference = $trans->difference;
            $qty_change = $trans->qty_change;
            $qty_current = $trans->qty_current;
            $price_current = $trans->price_current;
            $price_change = $trans->price_change;

            switch (intval($status)) {
                case 1:
                    $trailAction = "APPROVED UPDATE FOR ";
                    break;
                case 2:
                    $trailAction = "DECLINED UPDATE FOR ";
                    break;
                case 3:
                    $trailAction = "CANCELLED UPDATE FOR ";
                    break;
            }

            $trailAction .= strtoupper($post["trans_type"]) . " WITH REFERENCE NO.:$trans->reference_no ON ITEM SKU:$trans->sku NAME:$trans->name";

            if ((int)$status === 1) {
                if ($post["trans_type"] === "issuance") {
                    $new_qty = $trans->qty_change;
                    $issuance_contents_id = $trans->trans_type_contents_id;

                    if (empty($post["pi_contents_id"])) {
                        $this->db->set("qty", "CAST(qty AS DECIMAL(10,2)) + " . $difference, FALSE);
                        $this->db->where("id", $item);
                        $this->db->update("items");
                    } else {
                        $operator = $difference > 0 ? " + " : " - ";
                        $difference = $difference > 0 ? -$difference : $difference;
                        $this->db->set("moving_variance", "CAST(moving_variance AS DECIMAL(10,2))" . $operator . $difference, FALSE);
                        $this->db->where("id", $post["pi_contents_id"]);
                        $this->db->update("physical_inventory_contents");
                    }

                    $this->db->reset_query();

                    $this->db->set("qty", $new_qty);
                    $this->db->where("id", $issuance_contents_id);
                    $this->db->update("issuance_contents");
                } else { // for receiving
                    $receiving_contents_id = $trans->trans_type_contents_id;

                    $d = array();

                    if (!empty($post["pi_contents_id"])) {
                        $this->db->set("moving_variance", "CAST(moving_variance AS DECIMAL(10,2)) + " . $difference, FALSE);
                        $this->db->where("id", $post["pi_contents_id"]);
                        $this->db->update("physical_inventory_contents");
                    }

                    $this->db->reset_query();

                    if (!is_null($qty_current)) {
                        if (empty($post["pi_contents_id"])) {
                            $this->db->set("qty", "CAST(qty AS DECIMAL(10,2)) + " . $difference, FALSE);
                            $this->db->where("id", $item);
                            $this->db->update("items");
                        }

                        $d["qty"] = $qty_change;
                    }

                    $this->db->reset_query();

                    if (!is_null($price_current)) {
                        $d["unit_price"] = $price_change;
                    }

                    $this->db->set($d);
                    $this->db->where("id", $receiving_contents_id);
                    $this->db->update("receiving_contents");
                }
            }

            $this->db->reset_query();
            $this->db->where("id", $id);
            $this->db->set("status", $status);
            $this->db->set("confirmed_by", $this->user["emp_id"]);
            $this->db->set("date_confirmed", date('Y-m-d H:i:s'));

            $this->db->update("transaction_qty_update_history");

            if ($this->db->trans_status() === TRUE) {
                $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                $this->db->trans_commit();
                return true;
            } else {
                $this->db->trans_rollback();
                return false;
            }
        }

        function getInventoryItemScanned() {
            $post = $this->input->post();
            $sku = $post["sku"];
            $this->db->where("sku", $sku);
            return $this->db->get("items")->row();
        }

        function getPhysicalInventoryHistory($item_id) {
            $post = $this->input->post();
            $start = $post["start"];
            $length = $post["length"];
            $order = $post["order"];
            $columns = $post["columns"];
            $search = $post["search"];

            if ((int)$length >= 1) {
                $this->db->limit($length, $start);
            }

            if (!empty($order)) {
                $colIndex = $order[0]["column"];
                $dir = $order[0]["dir"];
                $column = $columns[$colIndex]["data"];
                $this->db->order_by($column, $dir);
            }

            $where = array(
                "pi_c.item_id" => $item_id,
                "p.`status`" => 1
            );

            $searchFields = "CONCAT(pi_c.variance, p.ref_no, p.remarks, 
                               p.created_at, CONCAT(emp.firstname, ' ', emp.lastname), 
                               CONCAT(emp2.firstname, ' ', emp2.lastname),
                               p.created_at, p.date_confirmed)";

            $this->db->select("pi_c.*, p.ref_no, p.remarks, 
                               p.created_at, CONCAT(emp.firstname, ' ', emp.lastname) created_by, 
                               CONCAT(emp2.firstname, ' ', emp2.lastname) confirmed_by,
                               p.created_at, p.date_confirmed");
            $this->db->join("physical_inventory p", "p.id = pi_c.pi_id", "INNER");
            $this->db->join("tblemployees emp", "emp.id = p.created_by", "INNER");
            $this->db->join("tblemployees emp2", "emp2.id = p.confirmed_by", "LEFT");

            $this->db->where($where);
            $this->db->like($searchFields, $search["value"], "both");

            $query = $this->db->get("physical_inventory_contents pi_c");

            $data = array();
            foreach ($query->result() as $pi) {
                $pi_contents_id = $pi->id;
                $trans = array();
                $trans[] = $this->getReceivingRefNosForPIHistory($pi_contents_id);
                $trans[] = $this->getIssuanceRefNosForPIHistory($pi_contents_id);
                $pi->trans = implode("||", array_filter($trans));
                array_push($data, $pi);
            }

            return array(
                "data" => $data,
                "recordsTotal" => $this->getPhysicalInventoryHistoryCount($where),
                "recordsFiltered" => $this->getPhysicalInventoryHistoryCount($where)
            );
        }

        private function getReceivingRefNosForPIHistory($pi_contents_id) {
            $this->db->select("GROUP_CONCAT(r.reference_no SEPARATOR '||') r_ref_no");
            $this->db->join("receiving r", "r.id = rc.receiving_id", "JOIN");
            $this->db->where("rc.pi_contents_id", $pi_contents_id);
            $this->db->where("r.`status`", 1);
            $this->db->order_by("r.received_date", "DESC");
            return $this->db->get("receiving_contents rc")->row("r_ref_no");
        }

        private function getIssuanceRefNosForPIHistory($pi_contents_id) {
            $this->db->select("GROUP_CONCAT(i.reference_no SEPARATOR '||') i_ref_no");
            $this->db->join("issuance i", "i.id = ic.issuance_id", "JOIN");
            $this->db->where("ic.pi_contents_id", $pi_contents_id);
            $this->db->where("i.`status`", 1);
            $this->db->order_by("i.issued_date", "DESC");
            return $this->db->get("issuance_contents ic")->row("i_ref_no");
        }

        function getPhysicalInventoryHistoryCount($where) {
            $this->db->select("pi_c.*, p.ref_no, p.remarks, 
                               p.created_at, CONCAT(emp.firstname, ' ', emp.lastname) created_by, 
                               CONCAT(emp2.firstname, ' ', emp2.lastname) confirmed_by");
            $this->db->join("physical_inventory p", "p.id = pi_c.pi_id", "INNER");
            $this->db->join("tblemployees emp", "emp.id = p.created_by", "INNER");
            $this->db->join("tblemployees emp2", "emp2.id = p.confirmed_by", "LEFT");
            $this->db->where($where);
            return $this->db->count_all_results("physical_inventory_contents pi_c");
        }

        function getItem($item_id) {
            $this->db->where("id", $item_id);
            $query = $this->db->get("items");
            return $query->row();
        }

        function getTransactionDetailsForPiHistory($trans_ref_no, $item_id) {
            $trans_parts = explode("-", $trans_ref_no);
            $trans_id = array_pop($trans_parts);
            $trans_type = $trans_parts[0];

            $select = null;
            $query = null;

            if ($trans_type === "RR") {
                $select = "rc.*, r.reference_no, r.supplier, r.invoice_dr_no,   
                           r.project_name, r.po_no, r.remarks, r.received_date, 
                           r.status, r.created_at, CONCAT(emp.firstname, ' ', emp.lastname) created_by";
                $this->db->select($select);
                $this->db->join("receiving r", "r.id = rc.receiving_id", "INNER");
                $this->db->join("tblemployees emp", "emp.id = r.created_by", "INNER");
                $this->db->where("rc.item", $item_id);
                $this->db->where("rc.receiving_id", $trans_id);
                $query = $this->db->get("receiving_contents rc");
            } else {
                $select = "ic.*, i.reference_no, i.ws_no, i.received_by, i.charge_to, 
                           i.work_number, i.status, i.approved_by, i.created_by, i.updated_by, 
                           i.issued_date, i.created_at, contractors.name issued_to";
                $this->db->select($select);
                $this->db->join("issuance i", "i.id = ic.issuance_id", "INNER");
                $this->db->join("contractors contractors", "contractors.id = i.issued_to");
                $this->db->where("ic.item", $item_id);
                $this->db->where("ic.issuance_id", $trans_id);
                $query = $this->db->get("issuance_contents ic");
            }

            return array(
                "data" => $query->row(),
                "type" => $trans_type
            );
        }

        function getTransactionTabularListing($pi_contents_id, $item_id) {
            $post = $this->input->post();
            $start = $post["start"];
            $length = $post["length"];
            $order = $post["order"];
            $columns = $post["columns"];
            $search = $post["search"];

            $union_queries = array();
            $this->db->select("i.reference_no, ic.qty, NULL AS price, ic.purpose, ic.item, 'IS' type, i.issued_date trans_date", FALSE);
            $this->db->join("issuance i", "i.id = ic.issuance_id", "INNER");
            $this->db->where("ic.pi_contents_id", $pi_contents_id);
            $this->db->where("ic.item", $item_id);
            $this->db->like("CONCAT(i.reference_no, ic.qty, ic.purpose)", $search["value"], "both");
            $union_queries[] = $this->db->get_compiled_select("issuance_contents ic");

            $this->db->reset_query();

            $this->db->select("r.reference_no, rc.qty, rc.unit_price price, NULL AS purpose, rc.item, 'RR' type, r.received_date trans_date", FALSE);
            $this->db->join("receiving r", "r.id = rc.receiving_id", "INNER");
            $this->db->where("rc.pi_contents_id", $pi_contents_id);
            $this->db->where("rc.item", $item_id);
            $this->db->like("CONCAT(r.reference_no, rc.qty, rc.unit_price)", $search["value"], "both");
            $union_queries[] = $this->db->get_compiled_select("receiving_contents rc");

            $u_query = implode(" UNION ALL ", $union_queries);
            $count_query = $u_query;

            if (!empty($order)) {
                $colIndex = $order[0]["column"];
                $dir = $order[0]["dir"];
                $column = $columns[$colIndex]["data"];
                $u_query .= " ORDER BY $column $dir";
            }

            if ((int)$length >= 1) {
                $u_query .= " LIMIT $start, $length";
            }

            $query = $this->db->query($u_query);
            $data = $query->result();

            return array(
                "data" => $data,
                "recordsFiltered" => $this->getTransactionTabularListingCount($count_query),
                "recordsTotal" => $this->getTransactionTabularListingCount($count_query),
            );
        }

        private function getTransactionTabularListingCount($query) {
            return $this->db->query($query)->num_rows();
        }

        function sendApprovedPhysicalInventoryEmail($id) {
            $this->db->where("code", "2711cc2d1b7ec35b5e11e643afb86a45");
            $this->db->where("archived", 0);
            $email_setting = $this->db->get("email_settings")->row();

            if (intval(count($email_setting)) <= 0) {
                return false;
            }

            $this->email->from($this->mailer);
            $this->email->to($email_setting->to);

            if (!empty($email_setting->cc)) {
                $this->email->cc($email_setting->cc);
            }

            if (!empty($email_setting->bcc)) {
                $this->email->bcc($email_setting->bcc);
            }

            if (!empty($email_setting->to)) {
                $this->email->set_newline("\r\n");
                $data = $this->getPhysicalInventoryDetails($id);

                $data = array(
                    "core_data" => $this->settings->getSettingsData(),
                    "data" => $data
                );

                $content = $this->load->view("configuration/mail_template/physical_inventory", $data, TRUE);

                $d_created = date('F j,Y', strtotime($data["data"]->main->created_at));
                $this->email->subject('Physical Inventory - ' . $d_created);
                $this->email->message($content);

                return $this->email->send();
            }

            return false;
        }

        function getMaxQtyRequest($id) {
            $this->db->where("mx_req.id", $id);
            $this->db->select("items.sku, items.name, mx_req.*, CONCAT(emp1.firstname, ' ', emp1.lastname) _created_by, 
                               CONCAT(emp2.firstname, ' ', emp2.lastname) _confirmed_by");
            $this->db->join("items items", "items.id = mx_req.item_id", "INNER");
            $this->db->join("tblemployees emp1", "emp1.id = mx_req.created_by", "INNER");
            $this->db->join("tblemployees emp2", "emp2.id = mx_req.confirmed_by", "LEFT");
            $query = $this->db->get("max_qty_extension_requests mx_req");
            return $query->row();
        }

        function getMaxQtyRequests() {
            $post = $this->input->post();
            $start = $post["start"];
            $length = $post["length"];
            $order = $post["order"];
            $columns = $post["columns"];
            $search = $post["search"];
            $filter = $post["filter"];
            $searchValue = $search["value"];
            $draw = $post["draw"];

            if ((int)$length >= 0) {
                $this->db->limit($length, $start);
            }

            if (!empty($order)) {
                $colIndex = $order[0]["column"];
                $dir = $order[0]["dir"];
                $column = $columns[$colIndex]["data"];
                $this->db->order_by($column, $dir);
            }

            if (intval($filter) >= 0) {
                $this->db->where("mx_req.status", $filter);
            }

            $searchField = "CONCAT(items.sku, items.name, IFNULL(mx_req.remarks,''), IFNULL(mx_req.confirmed_remarks,''),
                                   emp1.firstname, emp1.lastname, IFNULL(emp2.firstname, ''), IFNULL(emp2.lastname, ''),
                                   CASE WHEN mx_req.`status`=0 THEN 'PENDING' WHEN mx_req.`status`=1 THEN 'APPROVED' 
                                   WHEN mx_req.`status`=2 THEN 'DECLINED' WHEN mx_req.`status`=3 THEN 'PENDING' END)";
            $this->db->like($searchField, $searchValue, "BOTH");

            $this->db->select("items.sku, items.name, mx_req.*, CONCAT(emp1.firstname, ' ', emp1.lastname) _created_by, 
                               CONCAT(emp2.firstname, ' ', emp2.lastname) _confirmed_by");
            $this->db->join("items items", "items.id = mx_req.item_id", "INNER");
            $this->db->join("tblemployees emp1", "emp1.id = mx_req.created_by", "INNER");
            $this->db->join("tblemployees emp2", "emp2.id = mx_req.confirmed_by", "LEFT");
            $query = $this->db->get("max_qty_extension_requests mx_req");

            return array(
                "data" => $query->result(),
                // "sql" => $this->db->last_query(),
                "recordsTotal" => $this->getMaxQtyRequestsCount($searchField, $searchValue),
                "recordsFiltered" => $this->getMaxQtyRequestsCount($searchField, $searchValue),
                "draw" => $draw
            );
        }

        function getMaxQtyRequestsCount($searchField, $searchValue) {
            $this->db->like($searchField, $searchValue, "BOTH");
            $this->db->join("items items", "items.id = mx_req.item_id", "INNER");
            $this->db->join("tblemployees emp1", "emp1.id = mx_req.created_by", "INNER");
            $this->db->join("tblemployees emp2", "emp2.id = mx_req.confirmed_by", "LEFT");
            return $this->db->count_all_results("max_qty_extension_requests mx_req");
        }

        function getItemListForSelectOnMaxExtensionRequest() {
            $q = isset($_GET['q']) ? $_GET['q'] : '';
            $this->db->select("items.id, CONCAT('<div class=\"m--font-boldest mt-2 m--regular-font-size-sm1 text-muted\">', items.sku ,'</div>',
                                                 '<div class=\"m--font-bolder mb-2\">', items.name ,'</div>') text,
                               items.max_qty");
            $this->db->where("items.initial_max_qty >", 0);
            $this->db->like("CONCAT(sku, name)", $q, "BOTH");
            $query = $this->db->get("items");

            return array(
                "results" => $query->result()
            );
        }

        function saveMaxQtyExtensionRequest() {
            $post = $this->input->post();
            $emp_id = $this->user["emp_id"];
            $post["created_by"] = $emp_id;
            $post["new_max_qty"] = $post["curr_max_qty"] + $post["qty_increase"];
            $resultSet = array();

            $item = $this->db->get_where("items", array("id" => $post["item_id"]))->row();
            $trailAction = "CREATED REQUEST TO UPDATE MAX QTY FOR ITEM SKU:$item->sku NAME:$item->name";

            if ($this->db->insert("max_qty_extension_requests", $post)) {
                $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                $resultSet["message"] = "New extension request successfully saved.";
                $resultSet["title"] = "Request Saved.";
                $resultSet["success"] = true;
            } else {
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "DB Error";
                $resultSet["success"] = false;
            }

            return $resultSet;
        }

        function editMaxQtyExtensionRequest() {
            $post = $this->input->post();
            $emp_id = $this->user["emp_id"];
            $id = $post["id"];
            unset($post["id"]);
            $post["last_updated_by"] = $emp_id;
            $post["last_updated_at"] = date("Y-m-d H:i:s");
            $post["new_max_qty"] = $post["curr_max_qty"] + $post["qty_increase"];
            $resultSet = array();

            $item = $this->db->select("items.sku, items.name, mx.*")
                ->join("items items", "items.id = mx.item_id", "INNER")
                ->where("mx.id", $id)
                ->get("max_qty_extension_requests mx")->row();
            $trailAction = "UPDATED REQUEST TO UPDATE MAX QTY FOR ITEM SKU:$item->sku NAME:$item->name";

            $this->db->reset_query();

            $this->db->where("id", $id);
            if ($this->db->update("max_qty_extension_requests", $post)) {
                $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                $resultSet["message"] = "Extension request successfully update.";
                $resultSet["title"] = "Request Updated.";
                $resultSet["success"] = true;
            } else {
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "DB Error";
                $resultSet["success"] = false;
            }

            return $resultSet;
        }

        function confirmExtensionRequest($id, $status) {
            $this->db->trans_begin();

            $post = $this->input->post();
            $item_name = $post["item_name"];
            unset($post["item_name"]);

            $post["confirmed_by"] = $this->user["emp_id"];
            $post["confirmed_at"] = date("Y-m-d H:i:s");
            $post["status"] = $status;

            $item = $this->db->select("items.sku, items.name, mx.*")
                ->join("items items", "items.id = mx.item_id", "INNER")
                ->where("mx.id", $id)
                ->get("max_qty_extension_requests mx")->row();
            $this->db->reset_query();
            $trailAction = null;

            $this->db->where("id", $id);
            if ($this->db->update("max_qty_extension_requests", $post)) {
                switch (intval($status)) {
                    case 1:
                        $resultSet["message"] = "Request for <span class='m--font-boldest2'>" . strtoupper($item_name) . "</span> was approved.";
                        $resultSet["title"] = "Request Approved.";
                        $trailAction = "APPROVED ";

                        $resultSet["data"] = $this->updateMaxQty($id);
                        break;
                    case 2:
                        $resultSet["message"] = "Request for <span class='m--font-boldest2'>" . strtoupper($item_name) . "</span> was declined.";
                        $resultSet["title"] = "Request Declined.";
                        $trailAction = "DECLINED ";
                        break;
                    case 3:
                        $resultSet["message"] = "Request for <span class='m--font-boldest2'>" . strtoupper($item_name) . "</span> was cancelled.";
                        $resultSet["title"] = "Request Cancelled.";
                        $trailAction = "CANCELLED ";
                        break;
                }

                $trailAction .= "REQUEST TO UPDATE MAX QTY FOR ITEM SKU:$item->sku NAME:$item->name";
                $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                $resultSet["success"] = true;
            } else {
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "DB Error";
                $resultSet["success"] = false;
            }

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
            } else {
                $this->db->trans_rollback();
            }

            return $resultSet;
        }

        private function updateMaxQty($request_id) {
            $this->db->where("id", $request_id);
            $request = $this->db->get("max_qty_extension_requests")->row();

            $this->db->reset_query();

            $this->db->where("id", $request->item_id);
            $this->db->set("max_qty", $request->new_max_qty);
            return $this->db->update("items");
        }

        public function getPhysicalInventoryListBySku($export) {
            $result = array();
            $order = $this->input->post("order");
            $columns = $this->input->post("columns");
            $search = $this->input->post("search");
            $offset = $this->input->post("start");
            $limit = $this->input->post("length");
            $draw = $this->input->post("draw");
            $searchValue = $search["value"];
            $filter = $this->input->post("filter");

            if (!empty($order)) {
                $order_index = $order[0]["column"];
                $order_dir = $order[0]["dir"];
                $field = $columns[$order_index]["data"];
                $this->db->order_by($field, $order_dir);
            } else {
                $this->db->order_by("p.created_at", "DESC");
            }

            if (intval($export) === 0) {
                if ((int)$limit >= 1) {
                    $this->db->limit($limit, $offset);
                }
            }

            if (intval($filter) >= 0) {
                $this->db->where("p.status", $filter);
            }

            $searchFields = "CONCAT(p.ref_no, items.sku, items.name)";
            $this->db->like($searchFields, $searchValue, "BOTH");
            $this->db->select("pi_c.*, UPPER(items.sku) sku, UPPER(items.name) name, p.ref_no, p.created_at, p.status pi_status");
            $this->db->join("items items", "items.id = pi_c.item_id", "INNER");
            $this->db->join("physical_inventory p", "p.id = pi_c.pi_id", "INNER");
            $q = $this->db->get("physical_inventory_contents pi_c");

            $result["data"] = $q->result();
            // $result["sql"] = $this->db->last_query();
            $result["recordsFiltered"] = $this->getPhysicalInventoryListBySkuTotal($searchFields, $searchValue, $filter);
            $result["recordsTotal"] = $this->getPhysicalInventoryListBySkuTotal($searchFields, $searchValue, $filter);
            $result["draw"] = $draw;

            return $result;
        }

        private function getPhysicalInventoryListBySkuTotal($searchFields, $searchValue, $filter) {
            if (intval($filter) >= 0) {
                $this->db->where("p.status", $filter);
            }

            $this->db->like($searchFields, $searchValue, "BOTH");
            $this->db->select("pi_c.*, UPPER(items.sku) sku, UPPER(items.name) name, p.ref_no, p.created_at");
            $this->db->join("items items", "items.id = pi_c.item_id", "INNER");
            $this->db->join("physical_inventory p", "p.id = pi_c.pi_id", "INNER");
            return $this->db->count_all_results("physical_inventory_contents pi_c");
        }
    }

    /* End of file .php */