<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Items_model extends CI_Model {
        protected $itemsTable = "items";
        protected $uomTable = "uom";
        protected $loggedUser;
        protected $loggedUserName;

        function __construct() {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->load->model("is_model");
            $this->loggedUser = $this->session->userdata('logged_in');
            $this->loggedUserName = $this->loggedUser["firstname"] . " " . $this->loggedUser["lastname"];
        }

        private function getDBset(){
            return $this->is_model->getDBset();
        }

        function addInventoryItems() {
            $post = $this->input->post();
            $uploads_dir = realpath('./uploads/images/stocks');

            $trailAction = "ADDED NEW ITEM ";
            $ctr = 1;
            $count = count($post);
            foreach ($post as $key => $value) {
                $trailAction .= " " . strtoupper($key) . ":" . strtoupper($value) . (intval($ctr) < $count ? ", " : " ");
                $ctr++;
            }

            $post['image'] = $_FILES['image']['name'];

            if ($post) {
                $checksku = $this->checkInventoryCode($post);
                if ($checksku) {
                    if (!isset($post["sku"])) {
                        $post["sku"] = $this->core_layout->generateCode();
                    }

                    if (isset($post["beginning_qty"]) && $post["beginning_qty"] > 0) {
                        $post["qty"] = $post["beginning_qty"];
                    }

                    $session = $this->core_layout->getCurrentSession();
                    $post["created_by"] = $session["emp_id"];
                    $post["initial_max_qty"] = $post["max_qty"];

                    $insert = $this->db->insert($this->getDBset().".".$this->itemsTable, $post);
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploads_dir . '/' . $_FILES['image']['name']);

                    if ($insert) {
                        $this->core_layout->saveLog($trailAction, $this->loggedUserName);

                        $result["response"] = true;
                        $result["toastr_msg"] = "Iventory Item has been saved.";
                    } else {
                        $result["response"] = false;
                        $result["toastr_msg"] = "Error in saving iventory item!";
                    }
                } else {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Iventory Item code already exist!";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for saving!";
            }

            return $result;
        }

        function getInventoryItemData() {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"]) && $post["id"]) {
                $query = $this->db->get_where($this->getDBset().".".$this->itemsTable, $post);
                if ($query->num_rows() == 1) {
                    $resultset["response"] = true;
                    $resultset["value"] = $query->row_array();
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function updateInventoryItems() {
            $post = $this->input->post();
            $result = array();

            $trailAction = "UPDATED ITEM ";
            $ctr = 1;
            $count = count($post) - 1;
            foreach ($post as $key => $value) {
                if ($key === "id") continue;
                $trailAction .= " " . strtoupper($key) . ":" . strtoupper($value) . (intval($ctr) < $count ? ", " : " ");
                $ctr++;
            }

            $uploads_dir = realpath('./uploads/images/stocks');
            $post['image'] = $_FILES['image']['name'];

            if (isset($post["id"]) && $post["id"]) {
                $id = $post["id"];
                unset($post["id"]);

				if(isset($post["default_as_fuel"]) && $post["default_as_fuel"] == "on"){
					$post["default_as_fuel"] = "checked";
				}else{
					$post["default_as_fuel"] = "";
				}

                $session = $this->core_layout->getCurrentSession();
                $post["updated_by"] = $session["emp_id"];
                $post["updated_at"] = date("Y-m-d H:i:s");

                if (doubleval($post["max_qty"]) > 0) {
                    $post["initial_max_qty"] = $post["max_qty"];
                }


                $update = $this->db->update($this->getDBset().".".$this->itemsTable, $post, array("id" => $id));
                move_uploaded_file($_FILES['image']['tmp_name'], $uploads_dir . '/' . $_FILES['image']['name']);
                if ($update) {
                    $this->core_layout->saveLog($trailAction, $this->loggedUserName);

                    $result["response"] = true;
                    $result["toastr_msg"] = "Inventory item has been updated.";
                } else {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Error in updating inventory item!";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for saving!";
            }

            return $result;
        }

        function removeInventoryItem() {
            $post = $this->input->post();
            $result = array();
            if (isset($post["id"]) && $post["id"]) {
                $item = $this->db->get_where($this->getDBset().".".$this->itemsTable, array("id" => $post["id"]))->row();
                $trailAction = "DELETED AN ITEM SKU:" . $item->sku . ", NAME:" . $item->name;
                $this->db->delete($this->getDBset().".".$this->itemsTable, array('id' => $post['id']));
                if (!$this->db->affected_rows()) {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Error! ID [{$post['id']}] not found";
                } else {
                    $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                    $result["response"] = true;
                    $result["toastr_msg"] = "Inventory item has been removed.";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for removal!";
            }

            return $result;
        }

        function archiveInventoryItem() {
            $post = $this->input->post();
            $result = array();

            $item = $this->db->get_where($this->getDBset().".".$this->itemsTable, array("id" => $post["id"]))->row();

            if (isset($post["id"]) && $post["id"]) {
                $session = $this->core_layout->getCurrentSession();
                $post["archive_by"] = $session["emp_id"];
                $post["archive_date"] = date("Y-m-d H:i:s");
                $post["status"] = 0;

                $update = $this->db->update($this->getDBset().".".$this->itemsTable, $post, array("id" => $post["id"]));
                if (!$this->db->affected_rows()) {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Error! ID [{$post['id']}] not found";
                } else {
                    $this->core_layout->saveLog("ARCHIVED ITEM WITH SKU:$item->sku, NAME:$item->name", $this->loggedUserName);
                    $result["response"] = true;
                    $result["toastr_msg"] = "Inventory item has been archived.";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for archiving!";
            }

            return $result;
        }

        function getItemsList() {
            $post = $this->input->post();

            $dir = isset($post["order"][0]["dir"]) ? $post["order"][0]["dir"] : "DESC";
            $cols = isset($post["columns"]) ? $post["columns"] : [];
            $order_index = isset($post["order"]) ? $post["order"][0]["column"] : 0;
            $order = !empty($cols) ? $cols[$order_index]["data"] : "items.id";
            $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
            $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
            $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";

            $resultSet = array(
                "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array(),
            );

            if (!empty($post) && $post) {
                $this->db->select("items.id, items.sku, items.qty, items.image, 
                                   items.name, items.category_id, items.priority_id, uom.uom_code, 
                                   uom.uom_desc, items.reorder_qty, items.reorder_qty_level, items.beginning_qty, 
                                   items.status, items.min_qty, items.max_qty, 
                                   items.critical_level_percentage, IFNULL(cat.name, 'N/A') category, 
                                   IFNULL(prio.name, 'N/A') priority, items.updated_at");
                $this->db->join($this->getDBset().".".$this->uomTable . " uom", "uom.id = items.unit", "LEFT");
                $this->db->join($this->getDBset().".category cat", "cat.id=items.category_id", "LEFT");
                $this->db->join($this->getDBset().".priority prio", "prio.id=items.priority_id", "LEFT");

                if (intval($order_index) === 0) {
                    $this->db->order_by($this->getDBset().".items.updated_at", "DESC");
                } else {
                    $this->db->order_by($order, $dir);
                }

                if (intval($limit) > 0) {
                    $this->db->limit($limit, $start);
                }

                $searchFields = "CONCAT(items.sku, items.name, IFNULL(cat.name, ''), IFNULL(prio.name, ''), IFNULL(uom.uom_code, ''), IFNULL(uom.uom_desc, ''))";
                $this->db->like($searchFields, $searchValue, "BOTH");
                $this->db->where($this->getDBset().".items.status", 1);
                $query = $this->db->get("items items");
                //var_dump($this->db->last_query());
                $data = array();
                $uploads_dir = site_url("uploads/images/stocks");

                foreach ($query->result() as $item) {
                    $item->qty = number_format($item->qty, (($item->qty - floor($item->qty)) > 0 ? 2 : 0), ".", ",");
                    $item->beginning_qty = number_format($item->beginning_qty, (($item->beginning_qty - floor($item->beginning_qty)) > 0 ? 2 : 0), ".", ",");
                    $item->reorder_qty = number_format($item->reorder_qty, (($item->reorder_qty - floor($item->reorder_qty)) > 0 ? 2 : 0), ".", ",");
                    $item->min_qty = number_format($item->min_qty, (($item->min_qty - floor($item->min_qty)) > 0 ? 2 : 0), ".", ",");
                    $item->max_qty = number_format($item->max_qty, (($item->max_qty - floor($item->max_qty)) > 0 ? 2 : 0), ".", ",");

                    $img = !empty($item->image) ? $item->image : "no image.png";
                    $item->image = "<a class='image-link' href='" . $uploads_dir . '/' . $img . "' data-lightbox='" . $img . "'>
                                       <img class='image' src='" . $uploads_dir . '/' . $img . "' alt='" . $img . "' width='50px'/>
                                    </a>";
                    array_push($data, $item);
                }

                $resultSet["data"] = $data;
                $resultSet["draw"] = $draw;
                $resultSet["recordsTotal"] = $this->getItemsListCount($searchFields, $searchValue);
                $resultSet["recordsFiltered"] = $this->getItemsListCount($searchFields, $searchValue);
            }

            return $resultSet;
        }

        private function getItemsListCount($searchFields, $searchValue) {
            $this->db->join($this->uomTable . " uom", "uom.id = items.unit", "INNER");
            $this->db->join("category cat", "cat.id=items.category_id", "LEFT");
            $this->db->join("priority prio", "prio.id=items.priority_id", "LEFT");
            $this->db->like($searchFields, $searchValue, "BOTH");
            $this->db->where("items.status", 1);
            return $this->db->count_all_results("items items");
        }

        function getItemsListOld() {
            $post = $this->input->post();

            if ($post) {
                $columns = array("items.id",
                    "items.sku", "items.qty", "items.image",
                    "items.name", "items.category_id", "items.priority_id", "uom.uom_code",
                    "uom.uom_desc", "items.reorder_qty", "items.reorder_qty_level", "items.beginning_qty",
                    "items.status", "items.min_qty", "items.max_qty", "items.critical_level_percentage");
                // "IFNULL(cat.name, 'N/A') category", "IFNULL(prio.name, 'N/A') priority"

                $dir = $post["order"][0]["dir"];
                $cols = isset($post["columns"]) ? $post["columns"] : [];
                $order_index = $post["order"][0]["column"];
                $order = !empty($cols) ? $cols[$order_index]["data"] : "items.id";
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtInventoryItems = $this->dt_model->dataTable();
                $dtInventoryItems->setTable($this->itemsTable);
                $dtInventoryItems->setTableAlias("items");

                $dtInventoryItems->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->uomTable] = "uom";
                $joinTable["fields"][] = "uom.id=items.unit";
                $joinTable["field_loc"][] = "LEFT";

                /*$joinTable["table"]["category"] = "cat";
                $joinTable["fields"][] = "cat.id=items.category_id";
                $joinTable["field_loc"][] = "LEFT";

                $joinTable["table"]["priority"] = "prio";
                $joinTable["fields"][] = "prio.id=items.priority_id";
                $joinTable["field_loc"][] = "LEFT";*/

                $dtInventoryItems->setJoinTable($joinTable);
                $dtInventoryItems->setWhereInField("items.id");

                $parameters = array();
                $parameters["items.status"] = 1;
                // $parameters["items.sku"] = "CEM-0002";

                $dtInventoryItems->setWhereParameters($parameters);

                $totalData = $dtInventoryItems->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtInventoryItems->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtInventoryItems->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtInventoryItems->dtPostSearchCount($searchValue);
                }
                $uploads_dir = site_url("uploads/images/stocks");
                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $qty = $pst->qty;
                        $beginning_qty = $pst->beginning_qty;
                        $reorderQty = $pst->reorder_qty;

                        $isFloatQty = $this->isFloatValue($qty);
                        $isFloatBeggQty = $this->isFloatValue($beginning_qty);
                        $isFloatReorderQty = $this->isFloatValue($reorderQty);

                        $img = $pst->image ? $pst->image : "no image.png";
                        /*** $totalQty = $qty + $beginning_qty;
                         * $totalQty = (is_float($totalQty))? number_format($totalQty, 2, ".", ","): number_format(intval($totalQty), 0, "", ","); ***/

                        $totalQty = ($isFloatQty) ? number_format($qty, 2, ".", ",") : number_format(intval($qty), 0, "", ",");
                        $totalBegQty = ($isFloatBeggQty) ? number_format($pst->beginning_qty, 2, ".", ",") : number_format(intval($pst->beginning_qty), 0, "", ",");
                        $totalReorderQty = ($isFloatReorderQty) ? number_format($pst->reorder_qty, 2, ".", ",") : number_format(intval($pst->reorder_qty), 0, "", ",");
                        $getCategory = $this->crud->load(array("id" => $pst->category_id), 'category');
                        $getPriority = $this->crud->load(array("id" => $pst->priority_id), 'priority');

                        $isFloat = (is_float($qty) == true) ? "true" : "false";
                        $nestedData['id'] = $pst->id;
                        $nestedData['image'] = "<a class='image-link' href='" . $uploads_dir . '/' . $img . "' data-lightbox='" . $img . "'><img class='image' src='" . $uploads_dir . '/' . $img . "' alt='" . $img . "' width='50px' /></a>";
                        $nestedData['sku'] = $pst->sku;
                        $nestedData['name'] = $pst->name;
                        $nestedData['uom_desc'] = $pst->uom_desc;
                        $nestedData['uom_code'] = $pst->uom_code;

                        // $nestedData['category'] = $pst->category;
                        // $nestedData['priority'] = $pst->priority;

                        $nestedData['category'] = isset($getCategory["name"]) ? $getCategory["name"] : "N/A";
                        $nestedData['priority'] = isset($getPriority["name"]) ? $getPriority["name"] : "N/A";

                        $nestedData['qty'] = $totalQty;
                        $nestedData['reorder_qty'] = $totalReorderQty;
                        $nestedData['min_qty'] = $pst->min_qty;
                        $nestedData['max_qty'] = $pst->max_qty;
                        $nestedData['critical_level_percentage'] = $pst->critical_level_percentage;
                        $nestedData['beginning_qty'] = $totalBegQty;
                        $data[] = $nestedData;
                    }
                }
                $json_data = array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                );

                return $json_data;
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
        }

        public function isFloatValue($value = null) {
            if ($value) {
                $splitValue = explode(".", $value);
                if (count($splitValue) == 2) {
                    $decimal = intval($splitValue[1]);
                    if ($decimal > 0) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getArchivedList() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("items.id", "items.sku", "items.qty", "items.name", "uom.uom_desc", "items.reorder_qty", "items.beginning_qty", "items.status", "items.archive_by", "items.archive_date");
                $dir = $post["order"][0]["dir"];
                $order = $columns[$post["order"][0]["column"]];
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtInventoryItems = $this->dt_model->dataTable();
                $dtInventoryItems->setTable($this->itemsTable);
                $dtInventoryItems->setTableAlias("items");

                $dtInventoryItems->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->uomTable] = "uom";
                $joinTable["fields"][] = "uom.id=items.unit";
                $joinTable["field_loc"][] = "LEFT";

                $dtInventoryItems->setJoinTable($joinTable);
                $dtInventoryItems->setWhereInField("items.id");

                $parameters = array();
                $parameters["items.status"] = 0;

                $dtInventoryItems->setWhereParameters($parameters);

                $totalData = $dtInventoryItems->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtInventoryItems->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtInventoryItems->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtInventoryItems->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $userData = $this->core_layout->getUserData($pst->archive_by);
                        $displayName = (isset($userData["display_name_1"]) && $userData["display_name_1"]) ? $userData["display_name_1"] : "---";

                        $nestedData['id'] = $pst->id;
                        $nestedData['sku'] = $pst->sku;
                        $nestedData['name'] = $pst->name;
                        $nestedData['archived_by'] = $displayName;
                        $nestedData['archived_date'] = date("F d, Y h:i:s", strtotime($pst->archive_date));
                        /*** $nestedData['remarks'] = "dsf"; ***/
                        $data[] = $nestedData;
                    }
                }
                $json_data = array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                );

                return $json_data;
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
        }

        private function checkInventoryCode($data = array()) {
            if ($data) {
                $query = $this->db->get_where($this->itemsTable, array("sku" => $data["sku"]));
                if ($query->num_rows() == 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        private function checkInventoryCode_oldcode($data = array()) {
            $response = array(
                'valid' => false,
                'message' => 'SKU already exist.'
            );

            if ($data) {
                $query = $this->db->get_where($this->itemsTable, array("sku" => $data["sku"]));
                if ($query->num_rows() == 0) {
                    $response = array(
                        'valid' => true,
                    );
                } else {
                    $response = array(
                        'valid' => false,
                        'message' => 'SKU already exist.'
                    );
                }
            } else {
                $response = array(
                    'valid' => false,
                    'message' => 'SKU already exist.'
                );
            }

            return $response;
        }

        function getIssuanceItems() {
            $post = $this->input->post();

            if (isset($post["daterange"])) {
                $dates = explode(" - ", $post["daterange"]);
                $start_date = date("Y-m-d", strtotime($dates[0]));
                $end_date = date("Y-m-d", strtotime($dates[1]));

                if (isset($post['contractor'])) {
                    $query = $this->db->query("SELECT issuance.work_number, issuance.id,issuance.status, 
                                                IF(issuance.approved_by != '',issuance.approved_by, 'N/A') AS approved_by,issuance.reference_no,
                                                issuance.cancel_remarks,contractors.name as contractor_name,contractors.id,issuance.issued_to,DATE_FORMAT(issuance.issued_date, '%m/%d/%y') as issued_date, 
                                                issuance_contents.item,issuance_contents.qty,UPPER(issuance_contents.purpose) AS purpose,UPPER(items.name) AS name,
                                                UPPER(items.sku) AS sku,items.id,items.unit,IF(uom.uom_code != '',uom.uom_code, 'N/A') AS uom_code, issuance.ws_no
                                                FROM ((issuance LEFT JOIN issuance_contents ON issuance.id = issuance_contents.issuance_id) 
                                                    LEFT JOIN items ON issuance_contents.item = items.id) 
                                                    LEFT JOIN uom ON items.unit = uom.id LEFT JOIN contractors ON contractors.id = issuance.issued_to 
                                                WHERE (DATE(issuance.issued_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "') AND 
                                                    issuance.issued_to = " . $post['contractor']);
                } else {
                    $query = $this->db->query("SELECT issuance.work_number, issuance.id,issuance.status, 
                                               IF(issuance.approved_by != '',issuance.approved_by, 'N/A') AS approved_by, issuance.cancel_remarks, 
                                               issuance.reference_no, contractors.name as contractor_name,contractors.id,issuance.issued_to, 
                                               DATE_FORMAT(issuance.issued_date, '%m/%d/%y') as issued_date, issuance_contents.item,issuance_contents.qty, 
                                               UPPER(issuance_contents.purpose) AS purpose,UPPER(items.name) AS name, 
                                               UPPER(items.sku) AS sku,items.id,items.unit,IF(uom.uom_code != '',uom.uom_code, 'N/A') AS uom_code, issuance.ws_no
                                                FROM ((issuance LEFT JOIN issuance_contents ON issuance.id = issuance_contents.issuance_id) 
                                                    LEFT JOIN items ON issuance_contents.item = items.id) 
                                                    LEFT JOIN uom ON items.unit = uom.id LEFT JOIN contractors ON contractors.id = issuance.issued_to 
                                                    WHERE (DATE(issuance.issued_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "')");
                }
                return array("data" => $query->result_array());
            } else {
                return array("data" => array());
            }
        }

        function getReceiveItems() {
            $post = $this->input->post();

            if (isset($post["daterange"])) {
                $dates = explode(" - ", $post["daterange"]);
                $start_date = date("Y-m-d", strtotime($dates[0]));
                $end_date = date("Y-m-d", strtotime($dates[1]));
                $sku = $post["sku"];

                $this->db->select("receiving.remarks, receiving.supplier, receiving.id, receiving.reference_no, 
                                   receiving.invoice_dr_no, receiving.status, DATE_FORMAT(received_date,'%m/%d/%Y') received_date, 
                                   receiving_contents.item, receiving_contents.qty, UPPER(items.name) AS name, UPPER(items.sku) as sku, 
                                   items.id,items.unit,IF(uom.uom_code != '',uom.uom_code, 'N/A') AS uom_code, receiving.po_no");
                $this->db->join("receiving_contents", "receiving.id = receiving_contents.receiving_id", "LEFT");
                $this->db->join("items", "receiving_contents.item = items.id", "LEFT");
                $this->db->join("uom", "items.unit = uom.id", "LEFT");
                $this->db->where("DATE(receiving.received_date) BETWEEN '$start_date' AND '$end_date'", NULL, FALSE);
                if (!empty($sku)) {
                    $this->db->where("items.sku", $sku);
                }

                $query = $this->db->get("receiving");
                return array("data" => $query->result_array());
            } else {
                return array("data" => array());
            }
        }

        function getInventoryData() {
            $post = $this->input->post();
            $resultset = array();
            $data = array();
            $fromDt = date("Y-m-d");
            $toDt = date("Y-m-d", strtotime("+1 day"));
            $BFreferenceDate = date("Y-m-d");

            if (isset($post["date_range"]) && $post["date_range"]) {
                $_dates = explode("-", $post["date_range"]);
                if (count($_dates) == 2) {
                    $_dates[0] = trim($_dates[0]);
                    $_dates[1] = trim($_dates[1]);
                    $fromDate = date("Y-m-d", strtotime($_dates[0]));
                    $toDate = date("Y-m-d", strtotime("+1 day", strtotime($_dates[1])));

                    $BFreferenceDate = date("Y-m-d", strtotime("-1 day", strtotime($_dates[0])));

                    $fromDt = $fromDate;
                    $toDt = $toDate;
                }

                $sqlSelect = "SELECT items.id, items.name, items.sku, items.beginning_qty, IF(uom.uom_code != '',uom.uom_code, 'N/A') AS uom_code, IFNULL((SELECT SUM(receiving_contents.qty) from receiving_contents LEFT JOIN receiving ON receiving.id = receiving_contents.receiving_id WHERE receiving_contents.item = items.id AND receiving.status = 1 AND receiving.received_date BETWEEN '{$fromDt}' AND '{$toDt}'), 0) AS inventoryIn, IFNULL((SELECT SUM(issuance_contents.qty) from issuance_contents LEFT JOIN issuance ON issuance.id = issuance_contents.issuance_id WHERE issuance_contents.item = items.id AND issuance.status = 1 AND issuance.issued_date BETWEEN '{$fromDt}' AND '{$toDt}'), 0) AS inventoryOut, (IFNULL(items.beginning_qty, 0) + IFNULL((SELECT SUM(receiving_contents.qty) from receiving_contents LEFT JOIN receiving ON receiving.id = receiving_contents.receiving_id WHERE receiving_contents.item = items.id AND receiving.status = 1 AND receiving.received_date BETWEEN '{$fromDt}' AND '{$toDt}'), 0) - IFNULL((SELECT SUM(issuance_contents.qty) from issuance_contents LEFT JOIN issuance ON issuance.id = issuance_contents.issuance_id WHERE issuance_contents.item = items.id AND issuance.status = 1 AND issuance.issued_date BETWEEN '{$fromDt}' AND '{$toDt}'), 0)) AS balance FROM items LEFT JOIN uom ON items.unit=uom.id LEFT JOIN issuance_contents ON issuance_contents.item = items.id LEFT JOIN receiving_contents ON receiving_contents.item = items.id GROUP BY items.id";

                $getStockCollection = $this->db->query($sqlSelect);
                if ($getStockCollection->num_rows() > 0) {
                    foreach ($getStockCollection->result() as $rs) {
                        $newBalance = 0;
                        $rs->balance_forwarded = is_float(floatval($this->getBalanceForwarded($fromDt, $rs->sku))) ? number_format(floatval($this->getBalanceForwarded($fromDt, $rs->sku)), 2, ".", ",") : number_format($this->getBalanceForwarded($fromDt, $rs->sku), 0, "", ",");
                        $newBalance = $this->getBalanceForwarded($fromDt, $rs->sku) + $rs->inventoryIn;
                        $newBalance = $newBalance - $rs->inventoryOut;
                        $rs->beginning_qty = is_float(floatval($rs->beginning_qty)) ? number_format(floatval($rs->beginning_qty), 2, ".", ",") : number_format($rs->beginning_qty, 0, "", ",");
                        $rs->inventoryIn = is_float(floatval($rs->inventoryIn)) ? number_format(floatval($rs->inventoryIn), 2, ".", ",") : number_format($rs->inventoryIn, 0, "", ",");
                        $rs->inventoryOut = is_float(floatval($rs->inventoryOut)) ? number_format(floatval($rs->inventoryOut), 2, ".", ",") : number_format($rs->inventoryOut, 0, "", ",");
                        //$rs->balance = is_float(floatval($rs->balance))? number_format(floatval($rs->balance), 2, ".", ","): number_format($rs->balance, 0, "", ",");
                        //$this->updateStockQty($rs->id,$newBalance);
                        $rs->balance = is_float(floatval($newBalance)) ? number_format(floatval($newBalance), 2, ".", ",") : number_format($newBalance, 0, "", ",");
                        $data[] = $rs;
                    }
                }
            }
            $resultset["data"] = $data;
            $resultset["dates"] = array("date_from" => $fromDt, "date_to" => $toDt);

            return $resultset;
        }

        function getInventoryDatarework() {
            $post = $this->input->post();
            $resultarray = array();
            $getItemCollection = $this->crud->getCollection(array("status" => 1), "items");

            if (isset($post["date_range"]) && $post["date_range"]) {
                $dates = $post["date_range"];
                if ($getItemCollection) {
                    foreach ($getItemCollection as $_getItemCollection) {
                        $data = array();
                        // $this->db->select("IF(pi_c.qty<0, SUM(abs(pi_c.qty)+count), SUM(pi_c.variance)) total");
                        $this->db->select("SUM(pi_c.variance) total");
                        $this->db->where("pi_c.item_id", $_getItemCollection["id"]);
                        $this->db->where("pi_c.variance >", 0);
                        $this->db->where("p.status", 1);
                        $this->db->join("physical_inventory p", "p.id = pi_c.pi_id", "INNER");
                        $positiveVariances = $this->db->get("physical_inventory_contents pi_c")->row("total");

                        $this->db->reset_query();

                        $this->db->select("SUM(pi_c.variance) total");
                        $this->db->where("pi_c.item_id", $_getItemCollection["id"]);
                        $this->db->where("pi_c.variance <", 0);
                        $this->db->where("p.status", 1);
                        $this->db->join("physical_inventory p", "p.id = pi_c.pi_id", "INNER");
                        $negativeVariance = $this->db->get("physical_inventory_contents pi_c")->row("total");

                        //$balance = $this->getInventoryIn($_getItemCollection["id"],$dates) - $this->getInventoryOut($_getItemCollection["id"],$dates);
                        $BFbalance = 0;
                        $Curbalance = 0;
                        $inventoryIn = $this->getInventoryIn($_getItemCollection["id"], $dates);
                        $inventoryOut = $this->getInventoryOut($_getItemCollection["id"], $dates);

                        $BFbalance = (($inventoryIn["BFin"] - $inventoryOut["BFout"]) + $_getItemCollection["beginning_qty"] + $positiveVariances) + $negativeVariance;

                        $Curbalance = $BFbalance + $inventoryIn["in"];
                        $Curbalance = $Curbalance - $inventoryOut["out"];

                        $data["id"] = $_getItemCollection["id"];
                        $data["name"] = strtoupper($_getItemCollection["name"]);
                        $data["sku"] = strtoupper($_getItemCollection["sku"]);
                        $data["beginning_qty"] = $_getItemCollection["beginning_qty"];
                        $data["uom_code"] = $this->getUom($_getItemCollection["unit"]);
                        $data["inventoryIn"] = number_format(floatval($inventoryIn["in"]), 2, ".", ",");
                        $data["inventoryOut"] = number_format(floatval($inventoryOut["out"]), 2, ".", ",");
                        $data["balance_forwarded"] = number_format(floatval($BFbalance), 2, ".", ",");
                        $data["balance"] = number_format(floatval($Curbalance), 2, ".", ",");

                        // $this->updateStockQty($_getItemCollection["id"], $Curbalance);

                        $resultarray[] = $data;
                    }
                }
            }

            return array("data" => $resultarray);
        }

        private function getUom($id) {
            $query = $this->db->query("SELECT * FROM uom WHERE id = {$id}");
            $res = $query->row_array();
            return $res["uom_code"] != "" ? $res["uom_code"] : "N/A";
        }

        private function getInventoryIn($id, $dates) {
            $_dates = explode("-", $dates);
            $data = array();
            if (count($_dates) == 2) {
                $_dates[0] = trim($_dates[0]);
                $_dates[1] = trim($_dates[1]);
                $fromDate = date("Y-m-d", strtotime($_dates[0]));
                $toDate = date("Y-m-d", strtotime($_dates[1]));

                $BFreferenceDate = date("Y-m-d", strtotime("-1 day", strtotime($_dates[0])));

                $fromDt = $fromDate;
                //$toDt = $BFreferenceDate;
                $toDt = $toDate;
            }

            $query = $this->db->query("SELECT SUM(receiving_contents.qty) as inventoryin FROM receiving_contents LEFT JOIN receiving ON receiving.id = receiving_contents.receiving_id WHERE receiving.status = 1 AND (DATE(receiving.received_date) BETWEEN '{$fromDt}' AND '{$toDt}') AND receiving_contents.item = {$id} AND (receiving_contents.pi_contents_id IS NULL OR receiving_contents.pi_contents_id = 0)");

            $BFquery = $this->db->query("SELECT SUM(receiving_contents.qty) as inventoryin FROM receiving_contents LEFT JOIN receiving ON receiving.id = receiving_contents.receiving_id WHERE receiving.status = 1 AND (DATE(receiving.received_date) < '{$fromDt}') AND receiving_contents.item = {$id} AND (receiving_contents.pi_contents_id IS NULL OR receiving_contents.pi_contents_id = 0)");

            $res = $query->row_array();
            $BFres = $BFquery->row_array();
            $data["in"] = $res["inventoryin"] ? $res["inventoryin"] : 0;
            $data["BFin"] = $BFres["inventoryin"] ? $BFres["inventoryin"] : 0;

            return $data;
        }

        private function getInventoryOut($id, $dates) {
            $_dates = explode("-", $dates);
            $data = array();
            if (count($_dates) == 2) {
                $_dates[0] = trim($_dates[0]);
                $_dates[1] = trim($_dates[1]);
                $fromDate = date("Y-m-d", strtotime($_dates[0]));
                $toDate = date("Y-m-d", strtotime($_dates[1]));

                $BFreferenceDate = date("Y-m-d", strtotime("-1 day", strtotime($_dates[0])));

                $fromDt = $fromDate;
                $toDt = $toDate;
            }

            $query = $this->db->query("SELECT SUM(issuance_contents.qty) as inventoryout FROM issuance_contents LEFT JOIN issuance ON issuance.id = issuance_contents.issuance_id WHERE issuance.status = 1 AND (DATE(issuance.issued_date) BETWEEN '{$fromDt}' AND '{$toDt}') AND issuance_contents.item = {$id} AND (issuance_contents.pi_contents_id IS NULL OR issuance_contents.pi_contents_id = 0)");

            $BFquery = $this->db->query("SELECT SUM(issuance_contents.qty) as inventoryout FROM issuance_contents LEFT JOIN issuance ON issuance.id = issuance_contents.issuance_id WHERE issuance.status = 1 AND (DATE(issuance.issued_date) < '{$fromDt}') AND issuance_contents.item = {$id} AND (issuance_contents.pi_contents_id IS NULL OR issuance_contents.pi_contents_id = 0)");

            $res = $query->row_array();
            $BFres = $BFquery->row_array();
            $data["out"] = $res["inventoryout"] ? $res["inventoryout"] : 0;
            $data["BFout"] = $BFres["inventoryout"] ? $BFres["inventoryout"] : 0;

            return $data;
        }

        private function updateStockQty($id = NULL, $qty = 0) {
            $this->db->query("UPDATE items SET qty = '{$qty}' WHERE id = '{$id}'");
        }

        private function getBalanceForwarded($date = NULL, $stocksku = NULL) {
            $sqlSelect = "SELECT items.id, items.name,items.beginning_qty, items.sku, IF(uom.uom_code != '', uom.uom_code, 'N/A')  AS uom_code, Ifnull((SELECT SUM(qty) FROM receiving_contents left join receiving  ON receiving.id = receiving_contents.receiving_id WHERE  receiving_contents.item = items.id AND receiving.status = 1 AND receiving.received_date < '{$date}'), 0) AS inventoryIn, Ifnull((SELECT SUM(qty) FROM issuance_contents left join issuance ON issuance.id = issuance_contents.issuance_id WHERE  issuance_contents.item = items.id AND issuance.status = 1 AND issuance.issued_date < '{$date}'), 0)  AS inventoryOut, ( Ifnull((SELECT SUM(qty) FROM  receiving_contents left join receiving ON receiving.id = receiving_contents.receiving_id WHERE  receiving_contents.item = items.id AND receiving.status = 1 AND receiving.received_date < '{$date}'), 0) - Ifnull((SELECT SUM(qty) FROM   issuance_contents left join issuance ON issuance.id = issuance_contents.issuance_id WHERE  issuance_contents.item = items.id AND issuance.status = 1 AND issuance.issued_date < '{$date}'), 0) ) AS balance FROM items left join uom ON items.unit = uom.id left join issuance_contents ON issuance_contents.item = items.id left join receiving_contents ON receiving_contents.item = items.id WHERE items.sku = '{$stocksku}' GROUP BY items.id";

            $query = $this->db->query($sqlSelect);
            $data = $query->row_array();
            return $data["balance"] + $data["beginning_qty"];
        }

        function select2GetCurrentItems() {
            $post = $this->input->post();

            $resultset = array();
            $this->db->select("id, CONCAT(sku, ' | ', name) as text");
            $this->db->from($this->itemsTable);

            $this->db->where("beginning_qty ", 0);

            if (isset($post["term"]) && $post["term"]) {
                $this->db->group_start();
                $this->db->like("sku", $post["term"], "both");
                $this->db->or_like("name", $post["term"], "both");
                $this->db->group_end();
            }

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $resultset["results"] = $query->result_array();
            } else {
                $resultset["results"] = array();
            }

            return $resultset;
        }

        function getSearchedItem() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $query = $this->db->get_where($this->itemsTable, $post);
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $row->current_qty = $row->reorder_qty;
                    $input = "";
                    $input .= "<div class='form-group m-form__group row'>";
                    $input .= "<input type='number' name='needed_qty[{$row->id}]' value='0' class='form-control' style='padding-top: 0; padding-bottom: 0;' />";
                    $input .= "</div>";
                    $row->needed_qty = $input;
                    $row->action = "<button class='btn btn-danger btn-sm btnRemoveRow'><i class='fa fa-trash'></i></button>";
                    $resultset["data"] = $row;
                }
            }

            return $resultset;
        }

        function getSearchedItemBq() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $query = $this->db->get_where($this->itemsTable, $post);
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $row->current_qty = $row->beginning_qty;
                    $input = "";
                    $input .= "<div class='form-group m-form__group row'>";
                    $input .= "<input type='number' id='begin_bal' min='0' name='needed_qty[{$row->id}]' value='0' class='form-control' style='padding-top: 0; padding-bottom: 0;' />";
                    $input .= "</div>";
                    $row->needed_qty = $input;
                    $row->action = "<button class='btn btn-danger btn-sm btnRemoveRow'><i class='fa fa-trash'></i></button>";
                    $resultset["data"] = $row;
                }
            }

            return $resultset;
        }

        function setReorderedItems() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post["needed_qty"]) && $post["needed_qty"]) {
                $hasUpdates = false;
                foreach ($post["needed_qty"] as $id => $value) {
                    $where = array();
                    $where["id"] = $id;

                    $data = array();
                    $data["reorder_qty"] = $value;

                    $row = $this->db->get_where($this->itemsTable, $where)->row();
                    $trailAction = "BATCH SET RE-ORDER QTY, SKU:" . $row->sku . ", NAME:" . $row->name .
                        ", PREVIOUS RE-ORDER QTY:" . $row->reorder_qty . ", RE-ORDER QTY:" . $data["reorder_qty"];
                    $this->db->reset_query();

                    $update = $this->db->update($this->itemsTable, $data, $where);
                    if ($update) {
                        $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                        $hasUpdates = true;
                    }
                }

                if ($hasUpdates) {
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function setBeginningItems() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post["needed_qty"]) && $post["needed_qty"]) {
                $hasUpdates = false;
                foreach ($post["needed_qty"] as $id => $value) {
                    $where = array();
                    $where["id"] = $id;

                    $qty = 0;
                    $query = $this->db->get_where($this->itemsTable, $where);
                    $row = null;
                    if ($query->num_rows() == 1) {
                        $row = $query->row();
                        $qty = $row->qty;
                    }

                    $totalQty = floatval($qty) + floatval($value);

                    $data = array();
                    $data["qty"] = $totalQty;
                    $data["beginning_qty"] = $value;

                    $trailAction = "BATCH SET BEGINNING QTY, SKU:" . $row->sku . ", NAME:" . $row->name . ", PREVIOUS BEGINNING QTY:" . $row->beginning_qty .
                        ", BEGINNING QTY:" . $data["beginning_qty"];

                    $update = $this->db->update($this->itemsTable, $data, $where);
                    if ($update) {
                        $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                        $hasUpdates = true;
                    }
                }

                if ($hasUpdates) {
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function setHistoryList() {
			$post = $this->input->post();
            $resultarray = array();
            
            //return $post["id"];
            // $colSearch = $post["order"][0]["column"];
            // $orderDesc = $post["order"][0]["dir"];
            // $colDesc = $post["columns"][$colSearch]["data"];

            // if ($colSearch != 0) {
            //     $orderSet = "ORDER BY " . $colDesc . " " . $orderDesc;
            // } else {
            //     $orderSet = "ORDER BY issued_date ASC";
            // }
            
            $sqlSelect = "SELECT issuance.id, issuance.reference_no, issuance.issued_date as transaction_date, issuance_contents.qty, issuance_contents.item, 
                issuance_contents.running_balance,items.sku,items.name FROM issuance 
                LEFT JOIN issuance_contents ON issuance.id = issuance_contents.issuance_id 
                LEFT JOIN items ON items.id=issuance_contents.item 
                WHERE issuance_contents.item = {$post['id']} AND issuance.status = 1 
                UNION ALL
                SELECT receiving.id, receiving.reference_no, receiving.received_date as transaction_date, receiving_contents.qty, receiving_contents.item, 
                receiving_contents.running_balance, items.sku, items.name FROM receiving 
                LEFT JOIN receiving_contents ON receiving.id = receiving_contents.receiving_id 
                LEFT JOIN items ON items.id=receiving_contents.item 
                WHERE receiving_contents.item = {$post['id']} AND receiving.status = 1
                UNION ALL
                SELECT physical_inventory.id, physical_inventory.ref_no as reference_no, physical_inventory.date_confirmed as transaction_date, 
                physical_inventory_contents.variance as qty, physical_inventory_contents.item_id as item, physical_inventory_contents.moving_variance, 
                items.sku, items.name FROM physical_inventory 
                LEFT JOIN physical_inventory_contents ON physical_inventory.id = physical_inventory_contents.pi_id 
                LEFT JOIN items ON items.id=physical_inventory_contents.item_id 
                WHERE physical_inventory_contents.item_id = {$post['id']} AND physical_inventory.status = 1 AND physical_inventory_contents.status = 'valid'
                ORDER BY transaction_date ASC";

            $query = $this->db->query($sqlSelect);
            
            $rb = $this->getItemBeginBal($post['id']);
            
            $indexes = array();
			if($query->num_rows() > 0){
				foreach($query->result_array() as $key => $_query){
                    $indexes[$key] = $key;
                    $data = array();
                    $string = $_query["reference_no"];
                    if($string[0] == "P"){
                        $tempQty = $_query["qty"];
                        $absQty = abs($tempQty);
                        if(floatval($tempQty) < 0){ $rb = $rb - $absQty; }
                        else{ $rb = $rb + $absQty; }
                    }else if($string[0] == "I"){
						$rb = $rb - $_query["qty"];
					}else{
                        $rb = $rb + $_query["qty"];
                    }

                    $data["running_balance"] = $rb;
                    $data["id"] = $_query["id"];
                    $data["transaction_date"] = $_query["transaction_date"];
                    $data["item"] = $_query["item"];
                    $data["name"] = $_query["name"];
                    $data["qty"] = $_query["qty"];
                    $data["reference_no"] = $_query["reference_no"];
                    $data["sku"] = $_query["sku"];
                    $data["index"] = $key;
                    
					$resultarray[] = $data;
					
				}
            }
            
            array_multisort($indexes, SORT_DESC, $resultarray);
            //return $query->num_rows() > 0 ? array("data" => $query->result_array()) : array("data" => array());
            return array("data"=>$resultarray);
		}

		function getItemBeginBal($itemid = null){
            $this->db->select("beginning_qty");
            $this->db->from("items");
            $this->db->where("id",$itemid);
            $query = $this->db->get();
			return $query->row_array()["beginning_qty"];
		}

        function updateBeginningQuantity() {
            $count = 0;
            $query = $this->db->get_where("items", array("qty" => 0, "beginning_qty >" => 0));
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $begBal = $rs->beginning_qty;
                    $update = $this->db->update("items", array("qty" => $begBal), array("id" => $rs->id));
                    if ($update) {
                        $count++;
                    }
                }
            }

            return $count;
        }

        function generateStocksReport() {
            $post = $this->input->post();
            $resultarray = array();

            $this->db->select($post['selectedflds']);
            $this->db->from('items');

            if ($post['filters'] != "") {
                $this->db->where($post['filters']);
            }

            if ($post['orderbyfld'] != "") {
                $this->db->order_by($post['orderbyfld'], $post['order_by']);
            }
            $query = $this->db->get();

            foreach ($query->result_array() as $_query) {

                if (isset($_query["status"])) {
                    if ($_query["status"] = "1") {
                        $_query["status"] = '<span class="m-badge m-badge--success m-badge--wide">Active</span>';
                    } else {
                        $_query["status"] = '<span class="m-badge m-badge--danger m-badge--wide">Disabled</span>';
                    }
                }

                if (isset($_query["unit"])) {
                    $uom = $this->crud->load(array("id" => $_query["unit"]), "uom");
                    $_query["unit"] = $uom["uom_code"];
                }

                if (isset($_query["category_id"])) {
                    $data = $this->crud->load(array("id" => $_query["category_id"]), "category");
                    $_query["category_id"] = isset($data) ? $data["name"] : "N/A";
                }

                if (isset($_query["priority_id"])) {
                    $data = $this->crud->load(array("id" => $_query["priority_id"]), "priority");
                    $_query["priority_id"] = isset($data) ? $data["name"] : "N/A";
                }

                $resultarray[] = $_query;
            }


            //return $resultarray;

            return array("data" => $resultarray);
        }


        function categoryList() {
            $this->db->select("id,name");
            $this->db->from($this->getDBset.".category");
            $this->db->where("status", 1);
            $query = $this->db->get();
            $data = "{";
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data .= $_query["id"] . ":'" . $_query['name'] . "', ";
                }
            }
            $data .= "}";
            echo $data;
        }

        function priorityList() {
            $this->db->select("id,name");
            $this->db->from($this->getDBset.".priority");
            $this->db->where("status", 1);
            $query = $this->db->get();
            $data = "{";
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data .= $_query["id"] . ":'" . $_query['name'] . "', ";
                }
            }
            $data .= "}";
            echo $data;
        }

        function uomList() {
            $this->db->select("id,uom_code");
            $this->db->from($this->getDBset.".uom");
            $query = $this->db->get();
            $data = "{";
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data .= $_query["id"] . ":'" . $_query['uom_code'] . "', ";
                }
            }
            $data .= "}";
            echo $data;
        }

        function restoreItem($id) {
            $session = $this->core_layout->getCurrentSession();
            $post = array();
            $post["restored_by"] = $session["emp_id"];
            $post["restored_date"] = date("Y-m-d H:i:s");
            $post["status"] = 1;
            $this->db->set($post);
            $this->db->where("id", $id);
            return $this->db->update($this->getDBset.".items");
        }

        function checkDuplicateSku($sku) {
            $this->db->where("sku", $sku);
            return $this->db->get("items")->row();
        }

		function getstocksCollection(){
			$this->db->select("*");
			$this->db->from("items");
			$this->db->where("status",1);
			$query = $this->db->get();
			return $query;
		}

		function getRunningBalance($id){
            $resultarray = array();
            
            $sqlSelect = "SELECT issuance.id, issuance.reference_no, issuance.issued_date as transaction_date, issuance_contents.qty, issuance_contents.item, 
                issuance_contents.running_balance,items.sku,items.name FROM issuance 
                LEFT JOIN issuance_contents ON issuance.id = issuance_contents.issuance_id 
                LEFT JOIN items ON items.id=issuance_contents.item 
                WHERE issuance_contents.item = {$id} AND issuance.status = 1 
                UNION ALL
                SELECT receiving.id, receiving.reference_no, receiving.received_date as transaction_date, receiving_contents.qty, receiving_contents.item, 
                receiving_contents.running_balance, items.sku, items.name FROM receiving 
                LEFT JOIN receiving_contents ON receiving.id = receiving_contents.receiving_id 
                LEFT JOIN items ON items.id=receiving_contents.item 
                WHERE receiving_contents.item = {$id} AND receiving.status = 1
                UNION ALL
                SELECT physical_inventory.id, physical_inventory.ref_no as reference_no, physical_inventory.date_confirmed as transaction_date, 
                physical_inventory_contents.variance as qty, physical_inventory_contents.item_id as item, physical_inventory_contents.moving_variance, 
                items.sku, items.name FROM physical_inventory 
                LEFT JOIN physical_inventory_contents ON physical_inventory.id = physical_inventory_contents.pi_id 
                LEFT JOIN items ON items.id=physical_inventory_contents.item_id 
                WHERE physical_inventory_contents.item_id = {$id} AND physical_inventory.status = 1 AND physical_inventory_contents.status = 'valid'
                ORDER BY transaction_date ASC";

            $query = $this->db->query($sqlSelect);
            
            $rb = $this->getItemBeginBal($id);
            
            $indexes = array();
			if($query->num_rows() > 0){
				foreach($query->result_array() as $key => $_query){
                    $indexes[$key] = $key;
                    $data = array();
                    $string = $_query["reference_no"];
                    if($string[0] == "P"){
                        $tempQty = $_query["qty"];
                        $absQty = abs($tempQty);
                        if(floatval($tempQty) < 0){ $rb = $rb - $absQty; }
                        else{ $rb = $rb + $absQty; }
                    }else if($string[0] == "I"){
						$rb = $rb - $_query["qty"];
					}else{
                        $rb = $rb + $_query["qty"];
                    }

                    $data["running_balance"] = $rb;
                    $data["id"] = $_query["id"];
                    $data["transaction_date"] = $_query["transaction_date"];
                    $data["item"] = $_query["item"];
                    $data["name"] = $_query["name"];
                    $data["qty"] = $_query["qty"];
                    $data["reference_no"] = $_query["reference_no"];
                    $data["sku"] = $_query["sku"];
                    $data["index"] = $key;
                    
					$resultarray[] = $data;
					
				}
            }
            
            array_multisort($indexes, SORT_DESC, $resultarray);
            //return $query->num_rows() > 0 ? array("data" => $query->result_array()) : array("data" => array());
            return array("data"=>$resultarray);
		}

		public function updateStockQtypublic($id = NULL, $qty = 0) {
            $this->db->query("UPDATE items SET qty = '{$qty}' WHERE id = '{$id}'");
        }

    }
