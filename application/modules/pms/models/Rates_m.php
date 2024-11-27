<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Rates_m extends CI_Model {
        protected $itemRatesTable = "gccpms.sf_item_rates";
        protected $itemTable = "gccpms.sf_item";
        protected $itemRatesHistoryTable = "gccpms.sf_item_rates_history";
        protected $itemRatesCategoryTable = "gccpms.sf_item_rates_category";
        protected $uomTable = "gccmaster.uom";

        public function __construct() {
            parent::__construct();
            $this->load->model("ams/Utilities_model", "utilities");
            $this->load->model('Rate_category_m', 'rate_category');
            $this->user_data = $this->session->userdata("logged_in");
            date_default_timezone_set("Asia/Manila");
        }

        function doPostEvent($function = null) {
            if (!$function) return false;
            return $this->$function();
        }

        function get_rates_datatable_request() {
            $resultSet = array();
            $table = $this->itemTable . " items";
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $joinArr = array(
                array(
                    "table" => $this->itemRatesTable . " rates",
                    "condition" => "items.id = rates.item_id AND rates.is_active = 1",
                    "option" => "LEFT"
                ),
                array(
                    "table" => "gccmaster.uom uom",
                    "condition" => "uom.id = rates.unit",
                    "option" => "LEFT"
                ),
                array(
                    "table" => "gccmaster.tblemployees emp",
                    "condition" => "emp.id = rates.approved_by",
                    "option" => "LEFT"
                ),
                array(
                    "table" => $this->itemRatesCategoryTable . " cat",
                    "condition" => "cat.id = rates.category_id",
                    "option" => "LEFT"
                ),
                array(
                    "table" => $this->itemTable . " items2",
                    "condition" => "items2.id = items.parent_id",
                    "option" => "LEFT"
                )
            );

            $searchFields = "CONCAT(IFNULL(items.label, ''), 
                         IFNULL(rates.tariff,''), IFNULL(uom.uom_desc, ''), 
                         IFNULL(CONCAT(emp.firstname, ' ', IF(emp.middlename IS NOT NULL AND emp.middlename != '' 
                           AND emp.middlename != 'NA' AND emp.middlename != 'N/A' AND emp.middlename != 'NONE', 
                           CONCAT(LEFT(emp.middlename, 1), '.'), ''),' ', emp.lastname), ''), IFNULL(cat.category, ''),
                         (CASE WHEN rates.approved_status = 1 THEN 'APPROVED' WHEN rates.approved_status = 2 
                         THEN 'DECLINED' WHEN rates.approved_status=0 THEN 'PENDING' ELSE '' END), IFNULL(items2.label, ''))";

            $where = "items.parent_id != 0";
            $this->db->select("items.id, items.label, rates.id rate_id, rates.tariff, uom.uom_desc unit, uom.uom_code,
                           UCASE(CONCAT(emp.firstname, ' ', IF(emp.middlename IS NOT NULL AND emp.middlename != '' 
                           AND emp.middlename != 'NA' AND emp.middlename != 'N/A' AND emp.middlename != 'NONE', 
                           CONCAT(LEFT(emp.middlename, 1), '.'), ''),' ', emp.lastname)) approved_by, 
                           rates.is_active, cat.category, rates.approved_status, rates.approved_remarks, items2.label parent");
            $this->db->from($table);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            /*** disable parent on the listing ***/
            $this->db->where($where);
            /*** disable parent on the listing ***/
            $this->db->like($searchFields, $pageOptions->search, "both");
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $this->db->order_by("rates.created_at", "desc");

            $queryResult = $this->db->get();
            $data = $queryResult->result();

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $resultSet["sql"] = $this->db->last_query(); // for debugging only
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["data"] = $data;

            return $resultSet;
        }

        function select2RateItems() {
            $arrData = array();
            $get = $this->input->get();
            $this->db->select("a.id, UPPER(a.label) as text, IF(a.parent_id = 0, 'true', 'false') as is_parent");
            $this->db->from($this->itemTable . " a");
            $this->db->join($this->itemRatesTable . " b", "b.item_id = a.id", "LEFT");
            $this->db->where("a.is_active", 1);
            $this->db->where("a.status", 1);
            $this->db->where("b.id", null);
            if (isset($get["term"]) && $get["term"]) {
                $this->db->like("a.label", $get["term"], "both");
            }
            $this->db->order_by("a.label", "ASC");
            $queryItem = $this->db->get();

            if ($queryItem->num_rows() > 0) {
                $arrData = $queryItem->result();
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getTaskItem($id = null) {
            $resultset = array();
            if ($id) {
                $where = array("id" => $id, "is_active" => 1, "status" => 1);
                $checkParent = $this->db->get_where($this->itemTable, $where);
                if ($checkParent->num_rows() == 1) {
                    $currentRow = $checkParent->row();
                    if ($currentRow->parent_id == 0) {
                        $queryData = $this->db->get_where($this->itemTable, array("parent_id" => $currentRow->id, "is_active" => 1, "status" => 1));
                        if ($queryData->num_rows() > 0) {

                        } else {
                            $resultset[""];
                        }
                    }
                }
                $this->db->from($this->itemTable);
                $this->db->where($where);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {

                } else {

                }
                $resultset["response"] = true;
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function setModalTitle($form) {
            $formData = $this->input->post("formData");
            $rate_id = isset($formData["rate_id"]) ? $formData["rate_id"] : "";

            $form["units"] = $this->db->get("gccmaster.uom")->result();
            $form["approvers"] = $this->getApprovers();

            if (!empty($rate_id)) {
                $form["item"] = $this->getRateDetail($rate_id);
            }

            return $form;
        }

        private function getRateDetail($id) {
            $this->db->where("rates.id", $id);
            $this->db->join($this->itemTable . " items", "items.id = rates.item_id", "INNER");
            return $this->db->get($this->itemRatesTable . " rates")->row();
        }

        function getCategory($form) {
            return array(
                "category" => $this->rate_category->getCategoryForTree(),
            );
        }

        function getRateDetails($form) {
            $form["units"] = $this->db->get("gccmaster.uom")->result();
            $form["approvers"] = $this->getApprovers();
            $form["details"] = $this->db->get_where($this->itemRatesTable, array("id" => $form["rate_id"]))->row();
            return $form;
        }

        function getApprovers() {
            $this->db->select("emp.id, CONCAT(emp.firstname, ' ', IF(emp.middlename IS NOT NULL AND emp.middlename != '' 
                           AND emp.middlename != 'NA' AND emp.middlename != 'N/A' AND emp.middlename != 'NONE', 
                           CONCAT(LEFT(emp.middlename, 1), '.'), ''),' ', emp.lastname) employee_name, 
                           IFNULL(pos.name, emp.position) position");
            $this->db->join("gcchris.tblposition pos", "pos.id = emp.position", "LEFT");
            $this->db->where("IFNULL(pos.name, emp.position) RLIKE 'FINANCE|PRESIDENT|SITE PROJECT ENGINEER|OPERATIONS ENGINEER'", NULL, FALSE);
            $this->db->order_by("employee_name", "asc");
            $query = $this->db->get("gccmaster.tblemployees emp ");
            return $query->result();
        }

        function addItemRate() {
            $post = $this->input->post();
            $emp_id = $this->user_data["emp_id"];
            $post["tariff"] = str_replace(",", "", $post["tariff"]);
            $post["created_by"] = $emp_id;
            unset($post["category_text"]);

            $this->core_layout->setPrivilegeName("pms_rates_masterfile");
            $actions = $this->core_layout->getCurrentActions();
            $hasApprovingAuthority = in_array("approving_authority", $actions);

            if ((int)$hasApprovingAuthority === 1) {
                $post["approved_status"] = 1;
                $post["approved_by"] = $emp_id;
                $post["approved_remarks"] = "Has auto approve authority.";
                $post["date_approved"] = date("Y-m-d H:i:s");
            }

            if ($this->db->insert($this->itemRatesTable, $post)) {
                $insert_id = $this->db->insert_id();
                return $this->insertRateHistory($insert_id, $post, $hasApprovingAuthority);
            } else {
                return false;
            }
        }

        function editItemRate() {
            $post = $this->input->post();
            $emp_id = $this->user_data["emp_id"];
            $id = $post["id"];
            unset($post["id"], $post["category_text"]);
            $post["tariff"] = str_replace(",", "", $post["tariff"]);
            $post["created_by"] = $emp_id;

            $this->core_layout->setPrivilegeName("pms_rates_masterfile");
            $actions = $this->core_layout->getCurrentActions();
            $hasApprovingAuthority = in_array("approving_authority", $actions);

            $this->db->trans_begin();

            $this->db->where(array("tariff" => $post["tariff"], "unit" => $post["unit"], "category_id" => $post["category_id"], "id" => $id));
            $same = $this->db->count_all_results($this->itemRatesTable);

            if (intval($same) > 0) {
                return 2;
            }

            if (!$this->updateOnly($id)) {
                // will amend the current rate to decline and approve the new update
                if ($this->willAmendCurrentPendingRate($id)) {
                    // update status of what is current in rate history table
                    $this->db->where("rate_id", $id);
                    $this->db->where("is_current", 1);
                    $this->db->set("approved_remarks", "Superseded.");
                    $this->db->set("date_approved", date("Y-m-d H:i:s"));
                    $this->db->set("approved_status", 2);
                    $this->db->set("approved_by", $emp_id);
                    $this->db->update($this->itemRatesHistoryTable);

                    $this->db->reset_query();

                    $post["approved_remarks"] = "Superseded updates of " . $this->getCreator($id) . ".";
                    $post["date_approved"] = date("Y-m-d H:i:s");
                    $post["approved_status"] = 1;
                    $post["approved_by"] = $emp_id;

                    if ((int)$hasApprovingAuthority === 1 && !$this->willAmendCurrentPendingRate($id)) {
                        $post["approved_remarks"] = "Updated current. (Has auto approve authority.)";
                        $post["date_approved"] = date("Y-m-d H:i:s");
                        $post["approved_status"] = 1;
                        $post["approved_by"] = $emp_id;
                    }

                    $this->insertRateHistory($id, $post);
                } else {
                    if ((int)$hasApprovingAuthority === 1) {
                        $post["approved_remarks"] = "Updated current. (Has auto approve authority.)";
                        $post["date_approved"] = date("Y-m-d H:i:s");
                        $post["approved_status"] = 1;
                        $post["approved_by"] = $emp_id;

                        $this->insertRateHistory($id, $post);
                    } else {
                        $this->resetApprovedFields($id);
                    }
                }
            }

            // return $this->updateOnly($id);

            // will update only if status is pending & edited by creator
            $this->db->where("id", $id);
            $this->db->update($this->itemRatesTable, $post);

            if ($this->updateOnly($id)) {
                $this->db->reset_query();

                $this->db->where("rate_id", $id);
                $this->db->where("is_current", 1);
                $this->db->update($this->itemRatesHistoryTable, $post);

                $this->db->reset_query();
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return 0;
            } else {
                $this->db->trans_commit();
                return 1;
            }
        }

        private
        function willAmendCurrentPendingRate($id) {
            $data = $this->db->get_where($this->itemRatesTable, array("id" => $id))->row();
            $status = intval($data->approved_status);

            return $status === 0;
        }

        private function getCreator($id) {
            $this->db->select("emp.id, CONCAT(emp.firstname, ' ', IF(emp.middlename IS NOT NULL AND emp.middlename != '' 
                           AND emp.middlename != 'NA' AND emp.middlename != 'N/A' AND emp.middlename != 'NONE', 
                           CONCAT(LEFT(emp.middlename, 1), '.'), ''),' ', emp.lastname) employee_name");
            $this->db->where("item_rates.id", $id);
            $this->db->join("gccmaster.tblemployees emp", "item_rates.created_by = emp.id", "INNER");
            $query = $this->db->get($this->itemRatesTable . " item_rates");
            return $query->row("employee_name");
        }

        private function updateOnly($id) {
            $emp_id = $this->user_data["emp_id"]; // current user
            $data = $this->db->get_where($this->itemRatesTable, array("id" => $id))->row();
            $created_by = $data->created_by;
            $status = $data->approved_status;

            return (intval($emp_id) === intval($created_by)) && intval($status) === 0;
        }

        private
        function resetApprovedFields($id) {
            $this->db->where("id", $id);
            $this->db->set("approved_by", NULL);
            $this->db->set("approved_remarks", NULL);
            $this->db->set("date_approved", NULL);
            $this->db->set("approved_status", 0);
            $this->db->update($this->itemRatesTable);
        }

        function insertRateHistory($rate_id, $post, $hasApprovingAuthority = 0) {
            unset($post["item_id"]);
            $post["rate_id"] = $rate_id;

            if ($this->db->insert($this->itemRatesHistoryTable, $post)) {
                $history_insert_id = $this->db->insert_id();

                /* start::query to mark is current */
                $this->db->set("is_current", 0)
                    ->where("rate_id", $rate_id)
                    ->update($this->itemRatesHistoryTable);

                $this->db->set("is_current", 1)
                    ->where("id", $history_insert_id)
                    ->update($this->itemRatesHistoryTable);
                /* end::query to mark is current */

                return true;
            }

            return false;
        }

        function getRateUpdateHistory($rate_id) {
            $resultSet = array();
            $table = $this->itemRatesHistoryTable . " history_rates";
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $joinArr = array(
                array(
                    "table" => "gccmaster.uom uom",
                    "condition" => "uom.id = history_rates.unit",
                    "option" => "LEFT"
                ),
                array(
                    "table" => "gccmaster.tblemployees emp",
                    "condition" => "emp.id = history_rates.approved_by",
                    "option" => "LEFT"
                ),
                array(
                    "table" => "gccmaster.tblemployees emp1",
                    "condition" => "emp1.id = history_rates.created_by",
                    "option" => "LEFT"
                ),
                array(
                    "table" => $this->itemRatesCategoryTable . " cat",
                    "condition" => "cat.id = history_rates.category_id",
                    "option" => "LEFT"
                ),
            );

            $searchFields = "CONCAT(IFNULL(history_rates.tariff,''), IFNULL(history_rates.unit, ''), 
                         IFNULL(CONCAT(emp.firstname, ' ', IF(emp.middlename IS NOT NULL AND emp.middlename != '' AND 
                         emp.middlename != 'NA' AND emp.middlename != 'N/A' AND emp.middlename != 'NONE', 
                         CONCAT(LEFT(emp.middlename, 1), '.'), ''), ' ', emp.lastname), ''), 
                         IFNULL(CONCAT(emp1.firstname, ' ', IF(emp1.middlename IS NOT NULL AND emp1.middlename != '' 
                         AND emp1.middlename != 'NA' AND emp1.middlename != 'N/A' AND emp1.middlename != 'NONE', 
                         CONCAT(LEFT(emp1.middlename, 1), '.'), ''), ' ', emp1.lastname), ''), 
                         IFNULL(history_rates.created_at, ''), IFNULL(uom.uom_desc, ''), IFNULL(cat.category, ''),
                         (CASE WHEN history_rates.approved_status = 1 THEN 'APPROVED' WHEN history_rates.approved_status = 2 
                         THEN 'DECLINED' WHEN history_rates.approved_status=0 THEN 'PENDING' ELSE '' END))";

            $fields = "history_rates.`id`, history_rates.`tariff`, uom.uom_desc unit, 
                   uom.uom_code, UCASE(CONCAT(emp.firstname, ' ', IF(emp.middlename IS NOT NULL AND emp.middlename != '' 
                   AND emp.middlename != 'NA' AND emp.middlename != 'N/A' AND emp.middlename != 'NONE', 
                   CONCAT(LEFT(emp.middlename, 1), '.'), ''),' ', emp.lastname)) approved_by, 
                   UCASE(CONCAT(emp1.firstname, ' ', IF(emp1.middlename IS NOT NULL AND emp1.middlename != '' AND 
                   emp1.middlename != 'NA' AND emp1.middlename != 'N/A' AND emp1.middlename != 'NONE', 
                   CONCAT(LEFT(emp1.middlename, 1), '.'), ''),' ', emp1.lastname)) created_by, history_rates.created_at,
                   history_rates.is_current, cat.category, history_rates.approved_status, history_rates.approved_remarks, 
                   history_rates.date_approved";
            $this->db->select($fields);
            $this->db->from($table);
            foreach ($joinArr as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $this->db->like($searchFields, $pageOptions->search, "both");
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $where = array("rate_id" => $rate_id);
            $this->db->where($where);
            $queryResult = $this->db
                ->order_by($pageOptions->order_column, $pageOptions->order_direction)
                ->get();
            $data = $queryResult->result();

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            // $resultSet["sql"] = $this->db->last_query(); // for debugging only
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
            $resultSet["data"] = $data;

            return $resultSet;
        }

        function setRateStatus($id) {
            $post = $this->input->post();
            $post["date_approved"] = date("Y-m-d H:i:s");
            $post["approved_by"] = $this->user_data["emp_id"];

            $this->db->trans_begin();

            $this->db->where("id", $id);
            $this->db->update($this->itemRatesTable, $post);

            $this->db->reset_query();

            $this->db->where("rate_id", $id);
            $this->db->where("is_current", 1);
            $this->db->update($this->itemRatesHistoryTable, $post);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array("success" => false, "status" => $post["approved_status"]);
            } else {
                $this->db->trans_commit();
                return array("success" => true, "status" => $post["approved_status"]);
            }
        }

        function getUom() {
            $resultSet = array();
            $table = $this->uomTable . " uom";
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $searchFields = "CONCAT(IFNULL(uom.uom_code, ''), IFNULL(uom.uom_desc, ''))";

            $fields = "uom.*";

            $this->db->select($fields);
            $this->db->from($table);

            $this->db->like($searchFields, $pageOptions->search, "both");
            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $queryResult = $this->db
                ->order_by($pageOptions->order_column, $pageOptions->order_direction)
                ->get();
            $data = $queryResult->result();

            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            // $resultSet["sql"] = $this->db->last_query(); // for debugging only
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, NULL, $search, NULL);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, NULL, $search, NULL);
            $resultSet["data"] = $data;

            return $resultSet;
        }

        function addUnit() {
            $post = $this->input->post();
            $post["uom_code"] = strtoupper($post["uom_code"]);
            $post["uom_desc"] = strtoupper($post["uom_desc"]);
            return $this->db->insert($this->uomTable, $post);
        }

        function editUnit($id) {
            $post = $this->input->post();
            $post["uom_code"] = strtoupper($post["uom_code"]);
            $post["uom_desc"] = strtoupper($post["uom_desc"]);
            $this->db->where("id", $id);
            return $this->db->update($this->uomTable, $post);
        }

        function setUnitData($form) {
            return $form;
        }
    }