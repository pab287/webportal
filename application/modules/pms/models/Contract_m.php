<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Contract_m extends CI_Model {
        protected $checklistItemTable = "gccpms.sf_checklist_item";
        protected $checklistApprovedQtyTable = "gccpms.sf_checklist_approved_qty";
        protected $checklistPrivilegeTable = "gccpms.sf_checklist_privilege";
        protected $checklistPrivilegeRateTable = "gccpms.sf_checklist_privilege_rates";
        protected $itemTable = "gccpms.sf_item";
        protected $itemRateTable = "gccpms.sf_item_rates";
        protected $woTypeTable = "gccpms.sf_wo_type";
        protected $accomplishmentTable = "gccpms.sf_accomplishment";
        protected $accomplishmentItemsTable = "gccpms.sf_accomplishment_items";
        protected $contractTable = "gccpms.sf_contract";
        protected $contractItemTable = "gccpms.sf_contract_items";
        protected $contractUnitTable = "gccpms.sf_contract_units";
        protected $contractExtensionTable = "gccpms.sf_contract_extension";
        protected $taskTable = "gccpms.sf_task";
        protected $taskTimelineTable = "gccpms.sf_task_timeline";
        protected $woCodeHistoryTable = "gccpms.sf_wo_code_history";
        protected $woCodeTempTable = "gccpms.sf_wo_code_temp";
        protected $projectTable = "gccpms.sf_project";
        protected $projectCompanyTable = "gccpms.sf_project_company";
        protected $projectLocationTable = "gccpms.sf_project_location";
        protected $projectUnitTable = "gccpms.sf_project_unit";
        protected $woVersionTable = "gccpms.sf_wo_version";
        protected $changeOrderTable = "gccpms.sf_change_order";
        protected $changeOrderItemsTable = "gccpms.sf_change_order_items";
        protected $changeOrderUnitsTable = "gccpms.sf_change_order_units";
        protected $additionalWoTable = "gccpms.sf_additional_wo";
        protected $contractTempItemsTable = "gccpms.sf_contract_temp_items";
        protected $woTaskHistoryTable = "gccpms.sf_wo_task_history";

        protected $contractorTable = "gcchris.tblcontractor";
        protected $employeeTable = "gccmaster.tblemployees";
        protected $unitTable = "gccmaster.uom";

        protected $privileges;

        public function __construct() {
            parent::__construct();
            $this->load->model("task_m", "adm_task");
            $this->load->model("project_m", "adm_project");
            $this->user_data = $this->session->userdata("logged_in");
            $this->privileges = $this->core_layout->getCurrentActions();
            date_default_timezone_set("Asia/Manila");
        }

        function doPostEvent($function = null) {
            if (!$function && !function_exists($function)) return false;
            return $this->$function();
        }

        function render_generate_lot() {

        }

        function get_multiple_lots() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();
            $post = $this->input->post();
            if (isset($post) && $post) {
                $tempUnits = array();
                unset($post["csrf_token"]);
                if (isset($post["selected_items"]) && $post["selected_items"]) {
                    $selectedItems = $post["selected_items"];
                    $parentId = $this->generateParentId($selectedItems);
                    if (!in_array($parentId, $selectedItems)) {
                        $selectedItems[] = $parentId;
                    }
                    if (isset($post["checklist_id"], $post["project_id"], $post["blocks"]) && $post["checklist_id"] && $post["project_id"] && $post["blocks"]) {
                        $tempAssignedUnits = array();
                        if (isset($post["contract_id"], $post["wo_task_id"]) && ($post["contract_id"] && $post["wo_task_id"])) {
                            $tempContractItems = $this->db->get_where($this->contractItemTable, array("contract_id" => $post["contract_id"], "wo_task_id" => $post["wo_task_id"], "status" => 1));
                            if ($tempContractItems->num_rows() > 0) {
                                foreach ($tempContractItems->result() as $kk => $vv) {
                                    $_tempUnits = unserialize($vv->lots);
                                    $this->db->from($this->projectUnitTable);
                                    $this->db->where("is_active", 1);
                                    $this->db->where("status", 1);
                                    $this->db->where_in("id", $_tempUnits);
                                    $this->db->where_in("block", $post["blocks"]);
                                    $queryTempUnits = $this->db->get();
                                    if ($queryTempUnits->num_rows() > 0) {
                                        foreach ($queryTempUnits->result() as $kxx => $vxx) {
                                            if (!in_array($vxx->id, $tempAssignedUnits)) {
                                                $tempAssignedUnits[] = $vxx->id;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $units = $this->getAssignedTaskUnits($post["checklist_id"], $selectedItems);
                        $this->db->select("id, UPPER(description) as label, block, lot");
                        $this->db->from($this->projectUnitTable);
                        $this->db->where("checklist_id", $post["checklist_id"]);
                        $this->db->where("project_id", $post["project_id"]);
                        $this->db->where("block_type", 1);
                        $this->db->where("is_active", 1);
                        $this->db->where("status", 1);
                        $this->db->where_in("block", $post["blocks"]);

                        if ($units && count($units) > 0) {
                            foreach ($units as $kkk => $vvv) {
                                if (in_array($vvv, $tempAssignedUnits)) {
                                    unset($units[$kkk]);
                                }
                            }

                            $this->db->where_not_in("id", $units);
                        }
                        $queryUnits = $this->db->get();
                        if ($queryUnits->num_rows() > 0) {
                            foreach ($queryUnits->result() as $key => $value) {
                                $tempUnits[$value->block][] = $value;
                            }
                        }
                    }
                }

                if ($tempUnits && count($tempUnits) > 0) {
                    $counter = 0;
                    foreach ($post["blocks"] as $ii => $vv) {
                        if (isset($tempUnits[$vv]) && $tempUnits[$vv]) {
                            $counter++;
                        }
                    }
                    $resultset["response"] = true;
                    $resultset["count"] = $counter;
                    $resultset["units"] = $tempUnits;
                    $resultset["blocks"] = $post["blocks"];
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function update_contract_change_order() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();
            $post = $this->input->post();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $this->db->select("a.id as contract_id, a.wo_code_id as wo_id, b.*");
                $this->db->from($this->contractTable . " a");
                $this->db->join($this->woCodeHistoryTable . " b", "b.id = a.wo_code_id");
                $this->db->where(array("a.id" => $post["id"], "a.status" => 1));
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $tempRow = $query->row();
                    $contractId = $tempRow->contract_id;
                    $woId = $tempRow->wo_id;

                    unset($tempRow->id, $tempRow->created_at, $tempRow->contract_id, $tempRow->wo_id);
                    $tempWoCode = $tempRow->wo_code;
                    $woCode = explode("-", $tempRow->wo_code);
                    $lastItem = end($woCode);
                    $lastKey = key(array_slice($woCode, -1, 1, true));

                    if (is_numeric($lastItem)) {
                        $woCode[] = "A";
                        $tempWoCode = implode("-", $woCode);
                    } else {
                        $lastItem = ord($lastItem) + 1;
                        $charItem = chr($lastItem);
                        $woCode[$lastKey] = strtoupper($charItem);
                        $tempWoCode = implode("-", $woCode);
                    }

                    if ($tempRow->co_version) {
                        $_lastItem = ord($tempRow->co_version) + 1;
                        $_charItem = chr($_lastItem);
                        $tempRow->co_version = $_charItem;
                    } else {
                        $tempRow->co_version = strtoupper("A");
                    }

                    $tempRow->wo_code = $tempWoCode;
                    $woCodeAdded = $this->db->insert($this->woCodeHistoryTable, $tempRow);
                    if ($woCodeAdded) {
                        $insertedId = $this->db->insert_id();
                        $tempData = array();
                        $tempData["contract_id"] = $contractId;
                        $tempData["current_wo_id"] = $insertedId;
                        $tempData["previous_wo_id"] = $woId;
                        $tempData["remarks"] = $post["remarks"];
                        $tempData["created_by"] = $session["emp_id"];
                        $changeOrderAdded = $this->db->insert($this->changeOrderTable, $tempData);
                        if ($changeOrderAdded) {
                            $coId = $this->db->insert_id();
                            $getContractItems = $this->db->get_where($this->contractItemTable, array("contract_id" => $contractId));
                            if ($getContractItems->num_rows() > 0) {
                                foreach ($getContractItems->result() as $key => $value) {
                                    $value->parent_id = $coId;
                                    $tempId = $value->id;
                                    unset($value->id, $value->contract_id, $value->created_at);
                                    if (isset($post["qty"][$tempId]) && $post["qty"][$tempId]) {
                                        $value->current_qty = str_replace(",", "", $post["qty"][$tempId]);
                                    }
                                    if (isset($post["cost"][$tempId]) && $post["cost"][$tempId]) {
                                        $value->current_tariff = str_replace(",", "", $post["cost"][$tempId]);
                                    }
                                    $this->db->insert($this->changeOrderItemsTable, $value);
                                }
                            }
                            $getContractUnits = $this->db->get_where($this->contractUnitTable, array("contract_id" => $contractId));
                            if ($getContractUnits->num_rows() > 0) {
                                foreach ($getContractUnits->result() as $key => $value) {
                                    $value->parent_id = $coId;
                                    unset($value->id, $value->contract_id, $value->created_at);
                                    $this->db->insert($this->changeOrderUnitsTable, $value);
                                }
                            }

                            $where = array();
                            $where["id"] = $contractId;
                            $where["status"] = 1;

                            $data = array();
                            $data["change_order_id"] = $coId;
                            $data["wo_code_id"] = $insertedId;
                            $data["wo_code"] = $tempWoCode;
                            $updateContract = $this->db->update($this->contractTable, $data, $where);
                            if ($updateContract) {
                                if (isset($post["unit_id"]) && count($post["unit_id"]) > 0) {
                                    $this->db->set('contract_status', 3, FALSE);
                                    $this->db->where('contract_id', $contractId);
                                    $this->db->where_not_in('unit_id', $post["unit_id"]);
                                    $updatedItems = $this->db->update($this->contractUnitTable);
                                }
                                if (isset($post["qty"]) && count($post["qty"]) > 0) {
                                    $tempCost = (isset($post["cost"]) && $post["cost"]) ? $post["cost"] : array();
                                    foreach ($post["qty"] as $key => $value) {
                                        $tempQty = str_replace(",", "", $value);
                                        $tempTariff = (isset($tempCost[$key]) && $tempCost[$key]) ? str_replace(",", "", $tempCost[$key]) : "";

                                        $tempData = array();
                                        $tempData["qty"] = $tempQty;
                                        if ($tempTariff) {
                                            $tempData["tariff"] = $tempTariff;
                                        }

                                        $tempWhere = array("id" => $key);
                                        $this->db->update($this->contractItemTable, $tempData, $tempWhere);
                                    }
                                }
                            }
                        }
                        $changeOrderLog = $this->adm_task->activityConfig(array("contract_id" => $contractId));
                        $changeOrderLog->setActivityLog("Change order has been made.");

                        $resultset["response"] = true;
                        $resultset["wo_code"] = $tempWoCode;
                    } else {
                        $resultset = false;
                    }
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function set_new_contract_items() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();
            $post = $this->input->post();
            if ($post) {
                $this->db->select("a.id as contract_id, a.wo_code_id as wo_id, b.*");
                $this->db->from($this->contractTable . " a");
                $this->db->join($this->woCodeHistoryTable . " b", "b.id = a.wo_code_id");
                $this->db->where("a.id", $post["contract_id"]);
                $queryContract = $this->db->get();
                if ($queryContract->num_rows() == 1) {
                    $qtyItems = $post["qty"];
                    $lotItems = $post["lots"];
                    $checklistId = $post["checklist_id"];

                    $itemId = $post["item_id"];
                    $itemId = explode(",", $itemId);

                    $units = $post["units"];
                    $arrUnits = explode(",", $units);
                    $arrQtyKeys = array_keys($qtyItems);
                    $itemRates = $this->getRateItemsByChecklistId($checklistId, $arrQtyKeys);
                    unset($post["csrf_token"], $post["qty"], $post["item_id"], $post["units"], $post["lots"]);

                    $tempRow = $query->row();
                    $contractId = $tempRow->contract_id;
                    $woId = $tempRow->wo_id;

                    unset($tempRow->id, $tempRow->created_at, $tempRow->contract_id, $tempRow->wo_id);
                    $tempWoCode = $tempRow->wo_code;
                    $woCode = explode("-", $tempRow->wo_code);
                    $lastItem = end($woCode);
                    $lastKey = key(array_slice($woCode, -1, 1, true));

                    if (is_numeric($lastItem)) {
                        $woCode[] = "A";
                        $tempWoCode = implode("-", $woCode);
                    } else {
                        $lastItem = ord($lastItem) + 1;
                        $charItem = chr($lastItem);
                        $woCode[$lastKey] = strtoupper($charItem);
                        $tempWoCode = implode("-", $woCode);
                    }

                    if ($tempRow->co_version) {
                        $_lastItem = ord($tempRow->co_version) + 1;
                        $_charItem = chr($_lastItem);
                        $tempRow->co_version = $_charItem;
                    } else {
                        $tempRow->co_version = strtoupper("A");
                    }

                    $tempRow->wo_code = $tempWoCode;
                    $woCodeAdded = $this->db->insert($this->woCodeHistoryTable, $tempRow);
                    if ($woCodeAdded) {
                        $insertedId = $this->db->insert_id();
                        $tempData = array();
                        $tempData["contract_id"] = $contractId;
                        $tempData["current_wo_id"] = $insertedId;
                        $tempData["previous_wo_id"] = $woId;
                        $tempData["remarks"] = $post["remarks"];
                        $tempData["created_by"] = $session["emp_id"];
                        $additionalWo = $this->db->insert($this->additionalWoTable, $tempData);
                        if ($additionalWo) {
                            $coId = $this->db->insert_id();

                            $where = array();
                            $where["id"] = $contractId;
                            $where["status"] = 1;

                            $data = array();
                            $data["change_order_id"] = $coId;
                            $data["wo_code_id"] = $insertedId;
                            $data["wo_code"] = $tempWoCode;
                            $updateContract = $this->db->update($this->contractTable, $data, $where);
                            if ($updateContract) {
                                if ($qtyItems && count($qtyItems) > 0) {
                                    foreach ($qtyItems as $key => $value) {
                                        $tempRow = array();
                                        $tempItemRow["parent_id"] = "";
                                    }
                                }
                            }
                        }
                        $changeOrderLog = $this->adm_task->activityConfig(array("contract_id" => $contractId));
                        $changeOrderLog->setActivityLog("Change order has been made.");

                        $resultset["response"] = true;
                        $resultset["wo_code"] = $tempWoCode;
                    } else {
                        $resultset["response"] = false;
                    }
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function set_new_contract() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();
            $post = $this->input->post();
            if ($post) {
                $tempItems = $this->db->get_where($this->contractTempItemsTable, array("user_id" => $session["emp_id"]));
                if ($tempItems->num_rows() > 0) {
                    $parentId = array();
                    $itemId = array();
                    $taskUnits = array();
                    $tempUnitCount = 0;
                    $tempUnits = array();
                    foreach ($tempItems->result() as $key => $item) {
                        if ($item->parent_id == "0") {
                            $parentId[] = $item->item_id;
                        } else {
                            $itemId[] = $item->item_id;
                            $taskUnits[$item->parent_id] = unserialize($item->lots);
                            foreach (unserialize($item->lots) as $key => $value) {
                                if (!in_array($value, $tempUnits)) {
                                    $tempUnits[] = $value;
                                    $tempUnitCount++;
                                }
                            }
                        }
                    }
                    $checklistId = $post["checklist_id"];

                    /*** $qtyItems = $post["qty"];
                     * $lotItems = $post["lots"];
                     *
                     * $itemId = $post["item_id"];
                     * $itemId = explode(",", $itemId);
                     *
                     * $units = $post["units"];
                     * $arrUnits = explode(",", $units);
                     * $arrQtyKeys = array_keys($qtyItems); ***/

                    $itemRates = $this->getRateItemsByChecklistId($checklistId, $itemId);
                    unset($post["csrf_token"], $post["qty"], $post["item_id"], $post["units"], $post["lots"]);

                    $getTempData = $this->db->get_where($this->woCodeTempTable, array("user_id" => $session["emp_id"]));
                    if ($getTempData->num_rows() == 1) {
                        $tempRow = $getTempData->row();
                        unset($tempRow->id, $tempRow->user_id, $tempRow->created_at);
                        $added = $this->db->insert($this->woCodeHistoryTable, $tempRow);
                        if ($added) {
                            $tempWoCodeId = $this->db->insert_id();
                            $post["wo_code_id"] = $tempWoCodeId;
                            $post["is_multiple"] = ($tempUnitCount > 1) ? 1 : 0;
                            $insertContract = $this->db->insert($this->contractTable, $post);
                            if ($insertContract) {
                                $tempContractId = $this->db->insert_id();
                                if ($tempContractId) {
                                    $taskHistoryData = array();
                                    $taskHistoryData["contract_id"] = $tempContractId;
                                    $taskHistoryData["wo_code_id"] = $tempWoCodeId;
                                    $taskHistoryData["created_by"] = $session["emp_id"];
                                    $woTaskHistory = $this->db->insert($this->woTaskHistoryTable, $taskHistoryData);
                                    if ($woTaskHistory) {
                                        $tempTaskHistory = $this->db->insert_id();
                                        if ($taskUnits && count($taskUnits) > 0) {
                                            foreach ($taskUnits as $parentId => $units) {
                                                foreach ($units as $key => $unit) {
                                                    $queryTasks = $this->db->get_where($this->taskTable, array("unit_id" => $unit, "task_id" => $parentId));
                                                    if ($queryTasks->num_rows() == 0) {
                                                        $arrTaskData = array();
                                                        $arrTaskData["unit_id"] = $unit;
                                                        $arrTaskData["task_id"] = $parentId;
                                                        $arrTaskData["contract_id"] = $tempContractId;
                                                        $arrTaskData["issued_date"] = $post["issued_date"];
                                                        $arrTaskData["due_date"] = $post["due_date"];
                                                        $addedTask = $this->db->insert($this->taskTable, $arrTaskData);
                                                        if ($addedTask) {
                                                            $taskId = $this->db->insert_id();
                                                            $contractUnit = array();
                                                            $contractUnit["wo_task_id"] = $tempTaskHistory;
                                                            $contractUnit["contract_id"] = $tempContractId;
                                                            $contractUnit["unit_id"] = $unit;
                                                            $contractUnit["item_id"] = $parentId;
                                                            $contractUnit["task_id"] = $taskId;
                                                            $addedContractUnit = $this->db->insert($this->contractUnitTable, $contractUnit);
                                                            if ($addedContractUnit) {
                                                                if ($itemId && count($itemId) > 0) {
                                                                    $arrTaskTimeline = array();
                                                                    $arrTaskTimeline["parent_id"] = $taskId;
                                                                    $arrTaskTimeline["contract_id"] = $tempContractId;

                                                                    $this->db->from($this->itemTable);
                                                                    $this->db->where("parent_id", $parentId);
                                                                    $this->db->where_in("id", $itemId);
                                                                    $queryTaskItems = $this->db->get();
                                                                    if ($queryTaskItems->num_rows() > 0) {
                                                                        foreach ($queryTaskItems->result() as $key => $value) {
                                                                            $arrTaskTimeline["task_id"] = $value->id;
                                                                            $checkTaskTimeline = $this->db->get_where($this->taskTimelineTable, array("parent_id" => $taskId, "task_id" => $value->id));
                                                                            if ($checkTaskTimeline->num_rows() == 0) {
                                                                                $this->db->insert($this->taskTimelineTable, $arrTaskTimeline);
                                                                            }
                                                                        }
                                                                    }
                                                                }

                                                                if ($itemRates && count($itemRates) > 0) {
                                                                    foreach ($itemRates as $kk => $vv) {
                                                                        $this->db->select("item_id, qty, unit, unit_cost as tariff, lot_count, lots");
                                                                        $tempWhere = array("user_id" => $session["emp_id"], "item_id" => $vv->item_id, "parent_id !=" => 0);
                                                                        $queryTempItems = $this->db->get_where($this->contractTempItemsTable, $tempWhere);
                                                                        if ($queryTempItems->num_rows() == 1) {
                                                                            $tempRow = $queryTempItems->row();
                                                                            $tempRow->contract_id = $tempContractId;
                                                                            $tempRow->wo_task_id = $tempTaskHistory;
                                                                            $checkContractItem = $this->db->get_where($this->contractItemTable, array("contract_id" => $tempContractId, "item_id" => $tempRow->item_id));
                                                                            if ($checkContractItem->num_rows() == 0) {
                                                                                $this->db->insert($this->contractItemTable, $tempRow);
                                                                            }
                                                                        }
                                                                    }
                                                                    $this->db->delete($this->contractTempItemsTable, array("user_id" => $session["emp_id"], "is_editable" => 0));
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        $queryRowTask = $queryTasks->row();
                                                        $contractUnit = array();
                                                        $contractUnit["wo_task_id"] = $tempTaskHistory;
                                                        $contractUnit["contract_id"] = $tempContractId;
                                                        $contractUnit["unit_id"] = $unit;
                                                        $contractUnit["item_id"] = $parentId;
                                                        $contractUnit["task_id"] = $queryRowTask->id;
                                                        $addedContractUnit = $this->db->insert($this->contractUnitTable, $contractUnit);
                                                        if ($addedContractUnit) {
                                                            if ($itemId && count($itemId) > 0) {
                                                                $arrTaskTimeline = array();
                                                                $arrTaskTimeline["parent_id"] = $queryRowTask->id;
                                                                $arrTaskTimeline["contract_id"] = $tempContractId;

                                                                $this->db->from($this->itemTable);
                                                                $this->db->where("parent_id", $parentId);
                                                                $this->db->where_in("id", $itemId);
                                                                $queryTaskItems = $this->db->get();
                                                                if ($queryTaskItems->num_rows() > 0) {
                                                                    foreach ($queryTaskItems->result() as $key => $value) {
                                                                        $arrTaskTimeline["task_id"] = $value->id;
                                                                        $checkTaskTimeline = $this->db->get_where($this->taskTimelineTable, array("parent_id" => $queryRowTask->id, "task_id" => $value->id));
                                                                        if ($checkTaskTimeline->num_rows() == 0) {
                                                                            $this->db->insert($this->taskTimelineTable, $arrTaskTimeline);
                                                                        }
                                                                    }
                                                                }
                                                            }

                                                            if ($itemRates && count($itemRates) > 0) {
                                                                foreach ($itemRates as $kk => $vv) {
                                                                    $this->db->select("item_id, qty, unit, unit_cost as tariff, lot_count, lots");
                                                                    $tempWhere = array("user_id" => $session["emp_id"], "item_id" => $vv->item_id, "parent_id !=" => 0);
                                                                    $queryTempItems = $this->db->get_where($this->contractTempItemsTable, $tempWhere);
                                                                    if ($queryTempItems->num_rows() == 1) {
                                                                        $tempRow = $queryTempItems->row();
                                                                        $tempRow->contract_id = $tempContractId;
                                                                        $tempRow->wo_task_id = $tempTaskHistory;
                                                                        $checkContractItem = $this->db->get_where($this->contractItemTable, array("contract_id" => $tempContractId, "item_id" => $tempRow->item_id));
                                                                        if ($checkContractItem->num_rows() == 0) {
                                                                            $this->db->insert($this->contractItemTable, $tempRow);
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            $resultset["response"] = true;
                                            $resultset["toastr_msg"] = "Contract task data has been added.";
                                            $resultset["redirect_url"] = site_url("pms/contract/view_work_order/{$tempContractId}");
                                        } else {
                                            $resultset["response"] = false;
                                            $resultset["toastr_msg"] = "No assigned unit/lot!";
                                        }
                                    } else {
                                        $resultset["response"] = false;
                                        $resultset["toastr_msg"] = "Error in creating contract history!";
                                    }
                                    /*** if($arrUnits && count($arrUnits) > 0){
                                     * foreach($arrUnits as $kk => $unitId){
                                     * if($itemId && count($itemId) > 0){
                                     * foreach ($itemId as $key => $tempId) {
                                     * $insertTask = false;
                                     * $tempIds = $this->getContractTaskItem($unitId, $tempId);
                                     * if($tempIds && count($tempIds) > 0){
                                     * foreach ($tempIds as $key => $value) {
                                     * if(!in_array($value, $arrQtyKeys)){ $insertTask = true; }
                                     * }
                                     * }else{
                                     * $insertTask = true;
                                     * }
                                     *
                                     * if($insertTask){
                                     * $queryTasks = $this->db->get_where($this->taskTable, array("unit_id"=>$unitId, "task_id"=>$tempId));
                                     * if($queryTasks->num_rows() == 0){
                                     * $arrTaskData = array();
                                     * $arrTaskData["unit_id"] = $unitId;
                                     * $arrTaskData["task_id"] = $tempId;
                                     * $arrTaskData["contract_id"] = $tempContractId;
                                     * $arrTaskData["issued_date"] = $post["issued_date"];
                                     * $arrTaskData["due_date"] = $post["due_date"];
                                     * $addedTask = $this->db->insert($this->taskTable, $arrTaskData);
                                     * if($addedTask){
                                     * $taskId = $this->db->insert_id();
                                     * $contractUnit = array();
                                     * $contractUnit["contract_id"] = $tempContractId;
                                     * $contractUnit["unit_id"] = $unitId;
                                     * $contractUnit["item_id"] = $tempId;
                                     * $contractUnit["task_id"] = $taskId;
                                     * $addedContractUnit = $this->db->insert($this->contractUnitTable, $contractUnit);
                                     * if($addedContractUnit){
                                     * if($arrQtyKeys && count($arrQtyKeys) > 0){
                                     * $arrTaskTimeline = array();
                                     * $arrTaskTimeline["parent_id"] = $taskId;
                                     * $arrTaskTimeline["contract_id"] = $tempContractId;
                                     *
                                     * $this->db->from($this->itemTable);
                                     * $this->db->where("parent_id", $tempId);
                                     * $this->db->where_in("id", $arrQtyKeys);
                                     * $queryTaskItems = $this->db->get();
                                     * if($queryTaskItems->num_rows() > 0){
                                     * foreach ($queryTaskItems->result() as $key => $value) {
                                     * $arrTaskTimeline["task_id"] = $value->id;
                                     * $checkTaskTimeline = $this->db->get_where($this->taskTimelineTable, array("parent_id"=>$taskId, "task_id"=>$value->id));
                                     * if($checkTaskTimeline->num_rows() == 0){
                                     * $this->db->insert($this->taskTimelineTable, $arrTaskTimeline);
                                     * }
                                     * }
                                     * }
                                     * }
                                     * if($itemRates && count($itemRates) > 0){
                                     * foreach($itemRates as $kk => $vv){
                                     * $tempLots = explode(",", $lotItems[$vv->item_id]);
                                     * $currentQty = $qtyItems[$vv->item_id];
                                     * $vv->qty = $currentQty;
                                     * $vv->contract_id = $tempContractId;
                                     * $vv->lot_count = count($tempLots);
                                     * $vv->lots = serialize($tempLots);
                                     * $checkContractItem = $this->db->get_where($this->contractItemTable, array("contract_id"=>$tempContractId, "item_id"=>$vv->item_id));
                                     * if($checkContractItem->num_rows() == 0){
                                     * $this->db->insert($this->contractItemTable, $vv);
                                     * }
                                     * }
                                     * }
                                     * }
                                     * }
                                     * }else{
                                     * $queryRowTask = $queryTasks->row();
                                     * $contractUnit = array();
                                     * $contractUnit["contract_id"] = $tempContractId;
                                     * $contractUnit["unit_id"] = $unitId;
                                     * $contractUnit["item_id"] = $tempId;
                                     * $contractUnit["task_id"] = $queryRowTask->id;
                                     * $addedContractUnit = $this->db->insert($this->contractUnitTable, $contractUnit);
                                     * if($addedContractUnit){
                                     * if($arrQtyKeys && count($arrQtyKeys) > 0){
                                     * $arrTaskTimeline = array();
                                     * $arrTaskTimeline["parent_id"] = $queryRowTask->id;
                                     * $arrTaskTimeline["contract_id"] = $tempContractId;
                                     *
                                     * $this->db->from($this->itemTable);
                                     * $this->db->where("parent_id", $tempId);
                                     * $this->db->where_in("id", $arrQtyKeys);
                                     * $queryTaskItems = $this->db->get();
                                     * if($queryTaskItems->num_rows() > 0){
                                     * foreach ($queryTaskItems->result() as $key => $value) {
                                     * $arrTaskTimeline["task_id"] = $value->id;
                                     * $checkTaskTimeline = $this->db->get_where($this->taskTimelineTable, array("parent_id"=>$taskId, "task_id"=>$value->id));
                                     * if($checkTaskTimeline->num_rows() == 0){
                                     * $this->db->insert($this->taskTimelineTable, $arrTaskTimeline);
                                     * }
                                     * }
                                     * }
                                     * }
                                     *
                                     * if($itemRates && count($itemRates) > 0){
                                     * foreach($itemRates as $kk => $vv){
                                     * $tempLots = explode(",", $lotItems[$vv->item_id]);
                                     * $currentQty = $qtyItems[$vv->item_id];
                                     * $vv->qty = $currentQty;
                                     * $vv->contract_id = $tempContractId;
                                     * $vv->lot_count = count($tempLots);
                                     * $vv->lots = serialize($tempLots);
                                     * $checkContractItem = $this->db->get_where($this->contractItemTable, array("contract_id"=>$tempContractId, "item_id"=>$vv->item_id));
                                     * if($checkContractItem->num_rows() == 0){
                                     * $this->db->insert($this->contractItemTable, $vv);
                                     * }
                                     * }
                                     * }
                                     * }
                                     * }
                                     * }
                                     * }
                                     * }
                                     * }
                                     *
                                     * $resultset["response"] = true;
                                     * $resultset["toastr_msg"] = "Contract task data has been added.";
                                     * }else{
                                     * $resultset["response"] = false;
                                     * $resultset["toastr_msg"] = "No assigned unit/lot!";
                                     * } ***/
                                } else {
                                    $resultset["response"] = false;
                                    $resultset["toastr_msg"] = "No contract data found!";
                                }
                            } else {
                                $resultset["response"] = false;
                                $resultset["toastr_msg"] = "Failed saving contract data!";
                            }
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Failed saving work order history data!";
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "No work order data found!";
                    }
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function getContractTaskItem($unitId = null, $itemId = null) {
            $arrIds = array();
            $this->db->select("b.item_id");
            $this->db->from($this->contractUnitTable . " a");
            $this->db->join($this->contractItemTable . " b", "b.contract_id = a.contract_id");
            $this->db->where("a.unit_id", $unitId);
            $this->db->where("a.item_id", $itemId);
            $this->db->where("a.contract_status", 1);
            $queryItems = $this->db->get();
            if ($queryItems->num_rows() > 0) {
                foreach ($queryItems->result() as $key => $value) {
                    if (!in_array($value->item_id, $arrIds)) {
                        $arrIds[] = $value->item_id;
                    }
                }
            }
            return $arrIds;
        }

        function generate_additional_task_contract() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                unset($post["csrf_token"]);
                $contractId = $post["contract_id"];
                $queryContract = $this->db->get_where($this->contractTable, array("id" => $contractId));
                if ($queryContract->num_rows() == 1) {
                    $tempRow = $queryContract->row();
                    $checklistId = $tempRow->checklist_id;
                    $selectedItems = explode(",", $post["selected_items"]);
                    $parentIds = $this->generateMultipleParentId($selectedItems);
                    if ($parentIds && count($parentIds) > 0) {
                        $parentId = $this->generateParentId($selectedItems);

                        $units = $post["other_units"];

                        $html = $this->load->view("pms/contract/form_content/additional_contract_preview", null, true);
                        $unitCount = (isset($units) && $units) ? count($units) : 0;
                        $tempDatax = $this->generateTaskContractData($checklistId, $selectedItems, $units);

                        $resultset["response"] = true;
                        $resultset["html"] = $html;
                        $resultset["contract_id"] = $tempRow->id;
                        $resultset["checklist_id"] = $checklistId;
                        $resultset["parent_id"] = $parentIds;
                        $resultset["selected_items"] = $selectedItems;
                        $resultset["units"] = $units;
                        $resultset["unit_count"] = $unitCount;
                        $resultset["_data"] = $tempDatax;
                        $resultset["_data_count"] = count($tempDatax);
                    } else {
                        $resultset["response"] = false;
                    }

                } else {
                    $resultset["response"] = false;
                }
            }
            return $resultset;
        }

        function generate_task_contract() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $isEditable = (isset($post["is_editable"]) && $post["is_editable"] == true) ? true : false;
                unset($post["csrf_token"], $post["is_editable"]);
                $checklistId = $post["checklist_id"];
                $selectedItems = explode(",", $post["selected_items"]);
                $parentIds = $this->generateMultipleParentId($selectedItems);
                if ($parentIds && count($parentIds) > 0) {
                    foreach ($parentIds as $key => $parentId) {
                        if (!in_array($parentId, $selectedItems)) {
                            $selectedItems[] = $parentId;
                            /*** $tempArray = array();
                             * foreach($selectedItems as $kk => $vv){ if($vv !== $parentId){ $tempArray[] = $vv; } }
                             * $selectedItems = $tempArray; ***/
                        }
                    }
                    sort($selectedItems);
                    $parentId = $this->generateParentId($selectedItems);
                    $parentTask = $this->db->get_where($this->itemTable, array("id" => $parentId));
                    $parentLabel = ($parentTask->num_rows() == 1) ? $parentTask->row()->label : "NO TASK LABEL";
                    $parentLabel = strtoupper($parentLabel);

                    $tempFields = array();
                    if (isset($post["unit_id"]) && $post["unit_id"]) {
                        $this->db->select("block as block_no, lot as lot_no");
                        $queryUnit = $this->db->get_where($this->projectUnitTable, array("id" => $post["unit_id"], "is_active" => 1, "status" => 1));
                        if ($queryUnit->num_rows() == 1) {
                            $tempFields = $queryUnit->row_array();
                            $tempFields["units"][] = $post["unit_id"];
                        }
                    }
                    if (isset($post["other_units"]) && $post["other_units"] && count($post["other_units"]) > 0) {
                        foreach ($post["other_units"] as $key => $value) {
                            $tempFields["units"][] = $value;
                        }
                    }

                    if (isset($post["block"], $post["lots"]) && $post["block"] && $post["lots"]) {
                        $tempUnits = array();
                        $this->db->from($this->projectUnitTable);
                        $this->db->where("block", $post["block"]);
                        $this->db->where("is_active", 1);
                        $this->db->where("status", 1);
                        $this->db->where_in("lot", $post["lots"]);
                        $queryUnits = $this->db->get();
                        if ($queryUnits->num_rows() > 0) {
                            foreach ($queryUnits->result() as $key => $rs) {
                                $tempUnits[$key] = $rs->id;
                            }
                            $tempFields["units"] = $tempUnits;
                        }
                        $tempFields["block_no"] = $post["block"];
                    } else if (isset($post["lots"]) && $post["lots"]) {
                        $tempFields["lot_no"] = $post["lots"];
                    } else if (isset($post["blocks"]) && $post["blocks"]) {
                        $tempFields["block_no"] = $post["blocks"];
                    }

                    if (isset($post["block"]) && $post["block"]) {
                        $tempFields["block_no"] = $post["block"];
                    }
                    if (isset($post["lot"]) && $post["lot"]) {
                        $tempFields["lot_no"] = $post["lot"];
                    }

                    $html = $this->load->view("pms/contract/form_content/contract_preview", null, true);

                    $rawData = array();
                    $rawData["wo_version"] = $post["wo_version"];
                    $rawData["project_id"] = $post["project_id"];
                    $rawData["parent_id"] = $parentIds;
                    $rawData["checklist_id"] = $checklistId;
                    $rawData["selected_items"] = $selectedItems;
                    if ($tempFields && count($tempFields) > 0) {
                        foreach ($tempFields as $kk => $vv) {
                            $rawData[$kk] = $vv;
                        }
                    }

                    $unitCount = (isset($tempFields["units"]) && $tempFields["units"]) ? count($tempFields["units"]) : 0;
                    $tempDatax = $this->generateTaskContractData($checklistId, $selectedItems, $tempFields["units"], $isEditable);

                    $resultset["response"] = true;
                    $resultset["html"] = $html;
                    $resultset["title"] = $parentLabel;
                    $resultset["row"] = $rawData;
                    $resultset["wo_data"] = $this->getWoData($rawData);
                    $resultset["parent_id"] = $parentIds;
                    $resultset["checklist_id"] = $checklistId;
                    $resultset["selected_items"] = $selectedItems;
                    $resultset["unit_count"] = $unitCount;
                    $resultset["_data"] = $tempDatax;
                    $resultset["_data_count"] = count($tempDatax);
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function generateTaskContractData($checklistId = null, $selectedItems = array(), $units = array(), $isEditable = false) {
            $arrData = array();
            $isEditable = ($isEditable) ? 1 : 0;
            if ($checklistId && $selectedItems && $units) {
                $employeeId = $this->core_layout->getCurrentEmployeeId();
                $lotCount = $this->getContractLotCount($selectedItems, $units);
                $lotIds = $this->getContractAvailableLot($selectedItems, $units);
                $this->db->from($this->itemTable);
                $this->db->where_in("id", $selectedItems);
                $queryItem = $this->db->get();
                foreach ($queryItem->result() as $key => $value) {
                    $tempLotCount = (isset($lotCount[$value->id]) && $lotCount[$value->id]) ? $lotCount[$value->id] : 0;
                    $tempLots = (isset($lotIds[$value->id]) && $lotIds[$value->id]) ? $lotIds[$value->id] : array();
                    /*** $value->temp_lots = $tempLots; ***/
                    if ($value->parent_id == 0) {
                        $tempItem = $this->db->get_where($this->contractTempItemsTable, array("item_id" => $value->id, "user_id" => $employeeId));
                        if ($tempItem->num_rows() == 0) {
                            $tempRow = array();
                            $tempRow["user_id"] = $employeeId;
                            $tempRow["item_id"] = $value->id;
                            $tempRow["sort"] = $value->sort;
                            $tempRow["is_editable"] = $isEditable;
                            $this->db->insert($this->contractTempItemsTable, $tempRow);
                        }
                        /*** $value->is_parent = true;
                         * $arrData[] = $value; ***/
                    } else {
                        $this->db->select("b.tariff, b.unit, c.qty");
                        $this->db->from($this->checklistPrivilegeTable . " a");
                        $this->db->join($this->checklistPrivilegeRateTable . " b", "b.privilege_id = a.id");
                        $this->db->join($this->checklistApprovedQtyTable . " c", "c.checklist_id = a.checklist_id");
                        $this->db->where("a.checklist_id", $checklistId);
                        $this->db->where("b.item_id", $value->id);
                        $this->db->where("c.item_id", $value->id);
                        $queryChecklist = $this->db->get();
                        if ($queryChecklist->num_rows() == 1) {
                            $xTempRow = $queryChecklist->row();

                            $tempSubItem = $this->db->get_where($this->contractTempItemsTable, array("item_id" => $value->id, "user_id" => $employeeId));
                            if ($tempSubItem->num_rows() == 0) {
                                $tempRow = array();
                                $tempRow["user_id"] = $employeeId;
                                $tempRow["item_id"] = $value->id;
                                $tempRow["qty"] = $xTempRow->qty;
                                $tempRow["unit"] = $xTempRow->unit;
                                $tempRow["unit_cost"] = $xTempRow->tariff;
                                $tempRow["lots"] = serialize($tempLots);
                                $tempRow["lot_count"] = $tempLotCount;
                                $tempRow["parent_id"] = $value->parent_id;
                                $tempRow["sort"] = $value->sort;
                                $tempRow["is_editable"] = $isEditable;
                                $this->db->insert($this->contractTempItemsTable, $tempRow);
                            } else {
                                $rowData = $tempSubItem->row();
                                $xtempLots = unserialize($rowData->lots);
                                if ($tempLots && count($tempLots) > 0) {
                                    foreach ($tempLots as $key => $value) {
                                        if (!in_array($value, $xtempLots)) {
                                            $xtempLots[] = $value;
                                        }
                                    }
                                }
                                $xtempLotCount = count($xtempLots);
                                $xtempLots = serialize($xtempLots);
                                $tempWhere = array();
                                $tempWhere["user_id"] = $rowData->user_id;
                                $tempWhere["item_id"] = $rowData->item_id;

                                $tempData = array();
                                $tempData["lots"] = $xtempLots;
                                $tempData["lot_count"] = $xtempLotCount;

                                $this->db->update($this->contractTempItemsTable, $tempData, $tempWhere);
                            }

                            /*** $value->is_parent = false;
                             * $xTempRow = $queryChecklist->row();
                             *
                             * $tempQty = $xTempRow->qty;
                             * $tempQty = str_replace(",", "", $tempQty);
                             * $explodeQty = explode(".", $tempQty);
                             * if(count($explodeQty) == 2){
                             * if($explodeQty[1] == "00"){ $tempQty = number_format(intval($tempQty)); }
                             * else{ $tempQty = number_format(floatval($tempQty), 2, ".", ","); }
                             * }
                             *
                             * $tempTotal = floatval($xTempRow->qty) * floatval($xTempRow->tariff);
                             * $xTempTotal = $tempTotal;
                             * $tempTotal = number_format($tempTotal, 2, ".", ",");
                             * $value->qty = $tempQty;
                             * $value->temp_qty = $xTempRow->qty;
                             * $value->tariff = number_format($xTempRow->tariff, 2, ".", ",");
                             * $value->unit = $xTempRow->unit;
                             * $value->total = $tempTotal;
                             * $value->temp_total = $xTempTotal;
                             * $arrData[] = $value; ***/
                        }
                    }
                }
            }

            return $arrData;
        }

        function getWoData($arrData = array()) {
            $tempData = array();
            if ($arrData) {
                $tempData = $this->adm_project->generateProjectDescriptionById($arrData["project_id"]);
                $queryVersion = $this->db->get_where($this->woVersionTable, array("code" => $arrData["wo_version"]));
                if ($queryVersion->num_rows() == 1) {
                    $tempData->wo_type = strtoupper($queryVersion->row()->description);
                }
                $this->db->select("UPPER(description) as description, block, lot");
                $this->db->from($this->projectUnitTable);
                $this->db->where_in("id", $arrData["units"]);
                $this->db->order_by("block, lot", "ASC");
                $queryUnits = $this->db->get();
                $tempData->unit_count = $queryUnits->num_rows();
                if ($queryUnits->num_rows() > 0) {
                    $tempData->units = $queryUnits->result();
                }
            }

            return $tempData;
        }

        function generateMultipleParentId($arrData = array()) {
            $arrIds = array();
            if (isset($arrData) && count($arrData) > 0) {
                $this->db->select("parent_id as id");
                $this->db->from($this->itemTable);
                $this->db->where_in("id", $arrData);
                $this->db->where("parent_id !=", 0);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $rs) {
                        if (!in_array($rs->id, $arrIds) && $rs->id) {
                            $arrIds[] = $rs->id;
                        }
                    }
                }
            }
            return $arrIds;
        }

        function generateParentId($arrData = array()) {
            $parentId = 0;
            if (isset($arrData) && count($arrData) > 0) {
                $this->db->select("parent_id as id");
                $this->db->from($this->itemTable);
                $this->db->where_in("id", $arrData);
                $this->db->group_by("parent_id");
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $rs) {
                        if ($rs->id) {
                            $parentId = $rs->id;
                        }
                    }
                }
            }

            return $parentId;
        }

        function generate_contract_code() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $projectId = $post["project_id"];
                $selectedItems = $post["selected_items"];
                unset($post["csrf_token"], $post["project_id"], $post["selected_items"]);
                $post["sub_task"] = explode(",", $selectedItems);

                $arrCode = $this->adm_project->generateProjectCodeById($projectId);
                if ($arrCode) {
                    $tempData = $this->generatePostVersionData($arrCode, $post);
                    $resultset["response"] = true;
                    $resultset["data"] = $tempData;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function render_modal_new_accomplishment() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/new_accomplishment_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_edit_qty_unitcost() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/edit_qty_unitcost_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_edit_redirect() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/edit_redirect_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_adjustment_redirect() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/adjustment_redirect_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_other_block_lot() {
            $resultset = array();
            $html = $this->load->view("pms/contract/form_content/work_order/other_block_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_generate_lot() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/generate_lot_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_edit_block_lot() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/edit_block_lot_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_remove_task() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/remove_task_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_contract_status() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/status_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_contract_code() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/contract_code_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_contract_remarks() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/remarks_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_contract_incharge() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/incharge_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_contract_leadman() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/lead_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_contract_foreman() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/foreman_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_contract_extension() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/extension_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_contract_change_order() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/change_order_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_extension_aprroval() {
            $resultset = array();
            $html = $this->load->view("pms/contract/modal_content/work_order/approval_extension_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function update_contract_extension_approval() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $extensionId = $post["id"];
                $contractId = $post["contract_id"];

                unset($post["csrf_token"], $post["id"], $post["contract_id"]);

                $session = $this->core_layout->getCurrentSession();
                $session = (object)$session;
                $post["extended_at"] = date("Y-m-d H:i:s");
                $post["extended_by"] = $session->emp_id;
                $tempWhere = array("id" => $extensionId);
                $updated = $this->db->update($this->contractExtensionTable, $post, $tempWhere);
                if ($updated) {
                    if ($post["extension_status"] == 1) {
                        $updatedContract = $this->db->update($this->contractTable, array("extension_id" => $extensionId), array("id" => $contractId));
                    }
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Contract exstension has been update.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update contract extension!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function update_contract_extension() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                unset($post["csrf_token"]);
                $session = $this->core_layout->getCurrentSession();
                $session = (object)$session;
                $post["created_at"] = date("Y-m-d H:i:s");
                $post["created_by"] = $session->emp_id;
                if (in_array("approving_authority", $this->privileges)) {
                    $post["extended_at"] = date("Y-m-d H:i:s");
                    $post["extended_by"] = $session->emp_id;
                    $post["extended_remarks"] = "Approved";
                    $post["extension_status"] = 1;
                }

                $inserted = $this->db->insert($this->contractExtensionTable, $post);
                if ($inserted) {
                    $lastId = $this->db->insert_id();
                    $currentData = $this->getCurrentContract($post["contract_id"]);
                    if (in_array("approving_authority", $this->privileges)) {
                        $updated = $this->db->update($this->contractTable, array("extension_id" => $lastId), array("id" => $post["contract_id"]));
                        if ($updated) {
                            $row = ($currentData["response"] == true && $currentData["row"]) ? $currentData["row"] : array();
                            $resultset["response"] = true;
                            $resultset["row"] = $row;
                            $resultset["toastr_msg"] = "Contract exteion has been updated";
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Failed to update contract extension!";
                        }
                    } else {
                        $row = ($currentData["response"] == true && $currentData["row"]) ? $currentData["row"] : array();
                        $resultset["response"] = true;
                        $resultset["row"] = $row;
                        $resultset["toastr_msg"] = "Contract extension is currently pending, please seek supervisor approval!";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed adding extension date!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        function update_contract_status() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $updated = $this->db->update($this->contractTable, $post, $where);
                if ($updated) {
                    $currentData = $this->getCurrentContract($where["id"]);
                    $row = ($currentData["response"] == true && $currentData["row"]) ? $currentData["row"] : array();
                    $resultset["response"] = true;
                    $resultset["row"] = $row;
                    $resultset["toastr_msg"] = "Update Successful";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed update!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        function update_contract_remarks() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $updated = $this->db->update($this->contractTable, $post, $where);
                if ($updated) {
                    $currentData = $this->getCurrentContract($where["id"]);
                    $row = ($currentData["response"] == true && $currentData["row"]) ? $currentData["row"] : array();
                    $resultset["response"] = true;
                    $resultset["row"] = $row;
                    $resultset["toastr_msg"] = "Update contract remarks successful";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed update!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        function update_contract_incharge() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $updated = $this->db->update($this->contractTable, $post, $where);
                if ($updated) {
                    $currentData = $this->getCurrentContract($where["id"]);
                    $row = ($currentData["response"] == true && $currentData["row"]) ? $currentData["row"] : array();
                    $resultset["response"] = true;
                    $resultset["row"] = $row;
                    $resultset["toastr_msg"] = "Update contract task in-charge successful";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed update!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        function update_contract_foreman() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $updated = $this->db->update($this->contractTable, $post, $where);
                if ($updated) {
                    $currentData = $this->getCurrentContract($where["id"]);
                    $row = ($currentData["response"] == true && $currentData["row"]) ? $currentData["row"] : array();
                    $resultset["response"] = true;
                    $resultset["row"] = $row;
                    $resultset["toastr_msg"] = "Update contract task foreman successful";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed update!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        function update_contract_leadman() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $updated = $this->db->update($this->contractTable, $post, $where);
                if ($updated) {
                    $currentData = $this->getCurrentContract($where["id"]);
                    $row = ($currentData["response"] == true && $currentData["row"]) ? $currentData["row"] : array();
                    $resultset["response"] = true;
                    $resultset["row"] = $row;
                    $resultset["toastr_msg"] = "Update contract task leadman successful";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed update!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        function get_change_order_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("b.wo_code as current_wo_code", "c.wo_code as previous_wo_code", "a.remarks", "a.created_by", "a.created_at", "a.id");
                $dir = "ASC";
                $order = "a.id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $tempTable = $this->dt_model->dataTable();
                $tempTable->setTable($this->changeOrderTable);
                $tempTable->setTableAlias("a");

                $tempTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->woCodeHistoryTable] = "b";
                $joinTable["joint_table"][] = $this->woCodeHistoryTable . " as c";
                $joinTable["fields"][] = "b.id=a.current_wo_id";
                $joinTable["fields"][] = "c.id=a.previous_wo_id";
                $joinTable["field_loc"][] = "";
                $joinTable["field_loc"][] = "";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.contract_id"] = $post["contract_id"];
                $tempTable->setWhereParameters($parameters);

                $totalData = $tempTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $tempTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $tempName = $this->core_layout->getEmployeeData($pst->created_by);
                        $tempName = (object)$tempName;
                        $tempCreatedBy = (isset($tempName->display_name_1) && $tempName->display_name_1) ? $tempName->display_name_1 : "No assigned name";
                        $tempCreatedDt = date("F d, Y H:i:s", strtotime($pst->created_at));

                        $nestedData['id'] = $pst->id;
                        $nestedData['current_code'] = $pst->current_wo_code;
                        $nestedData['previous_code'] = $pst->previous_wo_code;
                        $nestedData['remarks'] = $pst->remarks;
                        $nestedData['created_by'] = $tempCreatedBy;
                        $nestedData['created_at'] = $tempCreatedDt;
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

        function get_task_item_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("a.label", "b.unit", "b.tariff", "d.qty", "a.id");
                $dir = "ASC";
                $order = "a.id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $tempTable = $this->dt_model->dataTable();
                $tempTable->setTable($this->itemTable);
                $tempTable->setTableAlias("a");

                $tempTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->checklistPrivilegeRateTable] = "b";
                $joinTable["table"][$this->checklistPrivilegeTable] = "c";
                $joinTable["table"][$this->checklistApprovedQtyTable] = "d";
                $joinTable["fields"][] = "b.item_id=a.id";
                $joinTable["fields"][] = "c.id=b.privilege_id";
                $joinTable["fields"][] = "d.checklist_id=c.checklist_id AND d.item_id = a.id";
                $joinTable["field_loc"][] = "LEFT";
                $joinTable["field_loc"][] = "LEFT";
                $joinTable["field_loc"][] = "LEFT";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.parent_id"] = $post["parent_id"];
                $parameters["c.checklist_id"] = $post["checklist_id"];
                $parameters["a.status"] = 1;
                $tempTable->setWhereParameters($parameters);
                $totalData = $tempTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $tempTable->dtAllPosts("-1", $start, $order, $dir);
                } else {
                    $posts = $tempTable->dtSearch("-1", $start, $searchValue, $order, $dir);
                    $totalFiltered = $tempTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    $tempCount = (isset($post["units"]) && $post["units"]) ? intval($post["units"]) : 0;
                    foreach ($posts as $pst) {
                        $tempQty = $pst->qty;
                        $tempQty = str_replace(",", "", $tempQty);
                        $explodeQty = explode(".", $tempQty);
                        if (count($explodeQty) == 2) {
                            if ($explodeQty[1] == "00") {
                                $tempQty = number_format(intval($tempQty));
                            } else {
                                $tempQty = number_format(floatval($tempQty), 2, ".", ",");
                            }
                        }
                        $tempTotal = floatval($pst->tariff) * floatval($pst->qty) * floatval($tempCount);
                        $nestedData['id'] = $pst->id;
                        $nestedData['item'] = $pst->label;
                        $nestedData['unit'] = $pst->unit;
                        $nestedData['lots'] = $tempCount;
                        $nestedData['qty'] = $tempQty;
                        $nestedData['temp_qty'] = $pst->qty;
                        $nestedData['tariff'] = number_format($pst->tariff, 2, ".", ",");
                        $nestedData['price'] = $pst->tariff;
                        $nestedData['total'] = $tempTotal;
                        $nestedData['temp_total'] = number_format($tempTotal, 2, ".", ",");
                        if (in_array(intval($pst->id), $post["selected_items"])) {
                            $data[] = $nestedData;
                        }
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

        function get_temp_task_item_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $tempGrandTotal = 0;
                $employeeId = $this->core_layout->getCurrentEmployeeId();
                $isEditable = (isset($post["is_editable"]) && $post["is_editable"] == 1) ? 1 : 0;
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("b.label", "a.qty", "a.unit", "a.unit_cost", "a.id", "a.parent_id", "a.lot_count", "a.lots", "a.item_id");
                $dir = "ASC";
                $order = "a.item_id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $tempTable = $this->dt_model->dataTable();
                $tempTable->setTable($this->contractTempItemsTable);
                $tempTable->setTableAlias("a");

                $tempTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->itemTable] = "b";
                $joinTable["fields"][] = "b.id=a.item_id";
                $joinTable["field_loc"][] = "";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.user_id"] = $employeeId;
                $parameters["a.is_editable"] = $isEditable;
                $tempTable->setWhereParameters($parameters);
                $totalData = $tempTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $tempTable->dtAllPosts("-1", $start, $order, $dir);
                } else {
                    $posts = $tempTable->dtSearch("-1", $start, $searchValue, $order, $dir);
                    $totalFiltered = $tempTable->dtPostSearchCount($searchValue);
                }

                $tempData = $posts;
                $data = array();
                if (!empty($posts)) {
                    $tempItems = array();
                    $ids = array();
                    foreach ($posts as $pst) {
                        $tempItems[$pst->item_id] = $pst;
                        $ids[] = $pst->item_id;
                    }

                    $this->db->from($this->itemTable);
                    $this->db->where("parent_id", 0);
                    $this->db->where_in("id", $ids);
                    $this->db->order_by("sort", "ASC");
                    $tempQuery = $this->db->get();
                    if ($tempQuery->num_rows() > 0) {
                        foreach ($tempQuery->result() as $key => $value) {
                            $this->db->from($this->itemTable);
                            $this->db->where("parent_id", $value->id);
                            $this->db->where_in("id", $ids);
                            $this->db->order_by("sort", "ASC");
                            $queryItem = $this->db->get();
                            if ($queryItem->num_rows() > 0) {
                                if (isset($tempItems[$value->id]) && $tempItems[$value->id]) {
                                    $xtempData = $tempItems[$value->id];
                                    $tempRow = array();
                                    $tempRow["id"] = $xtempData->id;
                                    $tempRow["item_id"] = $value->id;
                                    $tempRow["label"] = $value->label;
                                    $tempRow["qty"] = "";
                                    $tempRow["unit"] = "";
                                    $tempRow["unit_cost"] = 0;
                                    $tempRow["total"] = 0;
                                    $tempRow["is_parent"] = 1;
                                    $tempRow["lot_count"] = 0;
                                    $tempRow["lots"] = array();
                                    $data[] = $tempRow;
                                }
                                foreach ($queryItem->result() as $kk => $vv) {
                                    if (isset($tempItems[$vv->id]) && $tempItems[$vv->id]) {
                                        $tempLots = array();
                                        $xtempData = $tempItems[$vv->id];

                                        $xdata = unserialize($xtempData->lots);
                                        if ($xdata && count($xdata) > 0) {
                                            $this->db->select("id, block, lot");
                                            $this->db->from($this->projectUnitTable);
                                            $this->db->where_in("id", $xdata);
                                            $this->db->where("status", 1);
                                            $this->db->where("is_active", 1);
                                            $queryUnit = $this->db->get();
                                            if ($queryUnit->num_rows() > 0) {
                                                foreach ($queryUnit->result() as $key => $value) {
                                                    $tempLots[$value->block][] = $value;
                                                }
                                            }
                                        }

                                        $tempTotal = floatval($xtempData->qty) * intval($xtempData->lot_count) * floatval($xtempData->unit_cost);
                                        $tempGrandTotal += $tempTotal;
                                        $xtempTotal = number_format($tempTotal, 2, ".", ",");
                                        $tempRow = array();
                                        $tempRow["id"] = $xtempData->id;
                                        $tempRow["item_id"] = $vv->id;
                                        $tempRow["label"] = $vv->label;
                                        $tempRow["qty"] = number_format($xtempData->qty, 2, ".", ",");
                                        $tempRow["unit"] = $xtempData->unit;
                                        $tempRow["unit_cost"] = number_format($xtempData->unit_cost, 2, ".", ",");
                                        $tempRow["total"] = $xtempTotal;
                                        $tempRow["is_parent"] = 0;
                                        $tempRow["lot_count"] = $xtempData->lot_count;
                                        $tempRow["lots"] = $tempLots;
                                        $data[] = $tempRow;
                                    }
                                }
                            }
                        }
                    }
                }
                $json_data = array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                    "grand_total" => number_format($tempGrandTotal, 2, ".", ","),
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

        function get_contract_extension_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("b.wo_code", "b.due_date", "a.extension_date", "a.created_by", "a.extended_by", "a.extension_status", "a.id", "a.created_at", "a.extended_at", "a.remarks", "a.extended_remarks", "c.contractor");
                $dir = "DESC";
                $order = "a.id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $tempTable = $this->dt_model->dataTable();
                $tempTable->setTable($this->contractExtensionTable);
                $tempTable->setTableAlias("a");

                $tempTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->contractTable] = "b";
                $joinTable["table"][$this->contractorTable] = "c";
                $joinTable["fields"][] = "b.id=a.contract_id";
                $joinTable["fields"][] = "c.id=b.contractor_id";
                $joinTable["field_loc"][] = "";
                $joinTable["field_loc"][] = "";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                if (isset($post["contract_id"]) && $post["contract_id"]) {
                    $parameters = array();
                    $parameters["a.contract_id"] = $post["contract_id"];
                    $tempTable->setWhereParameters($parameters);
                }

                $totalData = $tempTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $tempTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $tempName0 = $this->core_layout->getEmployeeData($pst->created_by);
                        $createdName = (object)$tempName0;
                        $extendedName = "";
                        if ($pst->extended_by !== "0") {
                            $tempName1 = $this->core_layout->getEmployeeData($pst->extended_by);
                            $extendedName = (object)$tempName1;
                        }

                        $nestedData = array();
                        $nestedData['id'] = $pst->id;
                        $nestedData['wo_code'] = $pst->wo_code;
                        $nestedData['due_date'] = $pst->due_date;
                        $nestedData['extension_date'] = $pst->extension_date;
                        $nestedData['created_name'] = (isset($createdName->display_name_1) && $createdName->display_name_1) ? $createdName->display_name_1 : "No assigned name";
                        $nestedData['extended_name'] = (isset($extendedName->display_name_1) && $extendedName->display_name_1) ? $extendedName->display_name_1 : "No assigned name";
                        $nestedData['contractor'] = $pst->contractor;
                        $nestedData['extended_by'] = $pst->extended_by;
                        $nestedData['remarks'] = $pst->remarks;
                        $nestedData['extended_remarks'] = $pst->extended_remarks;
                        $nestedData['extended_by'] = $pst->extended_by;
                        $nestedData['created_at'] = $pst->created_at;
                        $nestedData['extended_at'] = $pst->extended_at;
                        $nestedData['extension_status'] = $pst->extension_status;
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

        function get_contract_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("a.wo_code", "a.reference_code", "a.issued_date", "a.due_date", "b.contractor", "a.status", "a.id", "a.task_incharge", "a.extension_id", "c.extension_date");
                $dir = "DESC";
                $order = "a.id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $tempTable = $this->dt_model->dataTable();
                $tempTable->setTable($this->contractTable);
                $tempTable->setTableAlias("a");

                $tempTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->contractorTable] = "b";
                $joinTable["table"][$this->contractExtensionTable] = "c";
                $joinTable["fields"][] = "b.id=a.contractor_id";
                $joinTable["fields"][] = "c.id=a.extension_id";
                $joinTable["field_loc"][] = "";
                $joinTable["field_loc"][] = "LEFT";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                /*** $parameters = array();
                 * $parameters["a.status"] = 1;
                 * $tempTable->setWhereParameters($parameters); ***/
                $totalData = $tempTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $tempTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $tempName = $this->core_layout->getEmployeeData($pst->task_incharge);
                        $taskIncharge = (object)$tempName;
                        $tempDueDate = ($pst->extension_id !== 0 && $pst->extension_date) ? $pst->extension_date : $pst->due_date;

                        $nestedData['id'] = $pst->id;
                        $nestedData['wo_code'] = $pst->wo_code;
                        $nestedData['reference_code'] = $pst->reference_code;
                        $nestedData['issued_date'] = $pst->issued_date;
                        $nestedData['due_date'] = $tempDueDate;
                        $nestedData['contractor'] = $pst->contractor;
                        $nestedData['task_incharge'] = (isset($taskIncharge->display_name_1) && $taskIncharge->display_name_1) ? $taskIncharge->display_name_1 : "No assigned name";
                        $nestedData['status'] = $pst->status;
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

        function get_contract_accomplishment_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("a.week_no", "a.week_duration", "a.approval_by", "a.created_by", "a.created_date", "a.approval_date", "a.status", "a.id");
                $dir = "DESC";
                $order = "a.id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $tempTable = $this->dt_model->dataTable();
                $tempTable->setTable($this->accomplishmentTable);
                $tempTable->setTableAlias("a");

                $tempTable->setParameterFields($columns);

                $totalData = $tempTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $tempTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $tempCreatedName = $this->core_layout->getEmployeeData($pst->created_by);
                        $createdBy = (object)$tempCreatedName;
                        $createdBy = (isset($createdBy->display_name_1) && $createdBy->display_name_1) ? $createdBy->display_name_1 : "No assigned name";
                        $approvedBy = "---";
                        if (isset($pst->approval_by) && $pst->approval_by) {
                            $tempApprovalName = $this->core_layout->getEmployeeData($pst->approval_by);
                            $approvedBy = (object)$tempApprovalName;
                            $approvedBy = (isset($approvedBy->display_name_1) && $approvedBy->display_name_1) ? $approvedBy->display_name_1 : "No assigned name";
                        }

                        $nestedData['id'] = $pst->id;
                        $nestedData['week_no'] = $pst->week_no;
                        $nestedData['week_duration'] = $pst->week_duration;
                        $nestedData['created_name'] = $createdBy;
                        $nestedData['approved_name'] = $approvedBy;
                        $nestedData['status'] = $pst->status;
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

        function get_contract_item_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("c.label", "a.qty", "a.unit", "a.tariff", "a.id");
                $dir = "DESC";
                $order = "a.id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $tempTable = $this->dt_model->dataTable();
                $tempTable->setTable($this->contractItemTable);
                $tempTable->setTableAlias("a");

                $tempTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->contractTable] = "b";
                $joinTable["table"][$this->itemTable] = "c";
                $joinTable["fields"][] = "b.id=a.contract_id";
                $joinTable["fields"][] = "c.id=a.item_id";
                $joinTable["field_loc"][] = "";
                $joinTable["field_loc"][] = "";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.contract_id"] = $post["contract_id"];
                $tempTable->setWhereParameters($parameters);

                $totalData = $tempTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $tempTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                $tempGrandTotal = 0;

                if (!empty($posts)) {
                    $units = (isset($post["units"]) && $post["units"]) ? $post["units"] : array();
                    $costs = (isset($post["costs"]) && $post["costs"]) ? $post["costs"] : array();
                    $trggerActiveContract = (isset($post["units"]) && $post["units"]) ? true : false;
                    $unitCount = $this->getContractUnitCount($post["contract_id"], $units, $trggerActiveContract);

                    foreach ($posts as $pst) {
                        $tempQty = $pst->qty;
                        $isFloat0 = is_float($tempQty + 0);
                        $display0 = ($isFloat0 == true) ? number_format($tempQty, 2, ".", ",") : number_format($tempQty);

                        $unitCost = (isset($costs[$pst->id]) && $costs[$pst->id]) ? $costs[$pst->id] : $pst->tariff;
                        $tempTariff = number_format($unitCost, 2, ".", ",");

                        $tempTotal = (floatval($tempQty) * intval($unitCount)) * floatval($unitCost);
                        $tempGrandTotal += $tempTotal;

                        $tempTotal = number_format($tempTotal, 2, ".", ",");

                        $nestedData['id'] = $pst->id;
                        $nestedData['item'] = $pst->label;
                        $nestedData['unit'] = $pst->unit;
                        $nestedData['lots'] = $unitCount;
                        $nestedData['qty'] = $display0;
                        $nestedData['temp_qty'] = $pst->qty;
                        $nestedData['temp_price'] = $unitCost;
                        $nestedData['tariff'] = $tempTariff;
                        $nestedData['total'] = $tempTotal;
                        $data[] = $nestedData;
                    }
                }

                $isFloat3 = is_float($tempGrandTotal + 0);
                $display3 = ($isFloat3 == true) ? number_format($tempGrandTotal, 2, ".", ",") : number_format($tempGrandTotal);

                $json_data = array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                    "grand_total" => $display3,
                );

                return $json_data;
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                    "grand_total" => number_format($tempGrandTotal, 2, ".", ","),
                );
            }
        }

        function getContractExtensionData($id = null) {
            $resultset = array();
            if ($id) {
                $this->db->select("a.*, b.wo_code, b.due_date, c.contractor");
                $this->db->from($this->contractExtensionTable . " a");
                $this->db->join($this->contractTable . " b", "b.id = a.contract_id");
                $this->db->join($this->contractorTable . " c", "c.id = b.contractor_id");
                $this->db->where(array("a.id" => $id, "a.extension_status" => 0, "b.status" => 1));
                $queryExtension = $this->db->get();
                if ($queryExtension->num_rows() == 1) {
                    $row = $queryExtension->row();
                    $resultset["response"] = true;
                    $resultset["row"] = $row;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getContractUnitCount($id = null, $units = array(), $isActive = false) {
            $tempCount = 0;
            if ($id) {
                $this->db->select("COUNT(b.id) as units");
                $this->db->from($this->contractTable . " a");
                $this->db->join($this->contractUnitTable . " b", "b.contract_id = a.id");
                $this->db->where("a.id", $id);
                if ($isActive == true) {
                    $this->db->where("a.status", 1);
                    $this->db->where("b.contract_status ", 1);
                } else {
                    $this->db->where("a.status !=", 3);
                    $this->db->where("b.contract_status !=", 3);
                }

                if ($units && count($units) > 0) {
                    $this->db->where_in("b.unit_id", $units);
                }

                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $tempCount = $query->row()->units;
                    $tempCount = intval($tempCount);
                }
            }
            return $tempCount;
        }

        function setModalContract() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                $taskItems = $post["task_items"];
                unset($post["csrf_token"], $post["task_items"]);
                $getTempData = $this->db->get_where($this->woCodeTempTable, array("user_id" => $session["emp_id"]));
                if ($getTempData->num_rows() == 1) {
                    $tempRow = $getTempData->row();
                    unset($tempRow->id, $tempRow->user_id, $tempRow->created_at);
                    $added = $this->db->insert($this->woCodeHistoryTable, $tempRow);
                    if ($added) {
                        $post["wo_code_id"] = $this->db->insert_id();
                    }
                }

                $post["created_at"] = date("Y-m-d H:i:s");
                $post = $this->checkWoGeneratedCode($post);
                $insert = $this->db->insert($this->contractTable, $post);
                if ($insert) {
                    $lastId = $this->db->insert_id();
                    if ($lastId) {
                        $data = array("contract_id" => $lastId);
                        $where = array("id" => $post["parent_id"]);
                        $updatedContract = $this->db->update($this->taskTable, $data, $where);
                        if ($updatedContract && $this->db->affected_rows() > 0) {
                            $arrTaskItem = explode(",", $taskItems);
                            if ($arrTaskItem && count($arrTaskItem) > 0) {
                                $arrWhereTimeline = array("parent_id" => $post["parent_id"]);
                                foreach ($arrTaskItem as $key => $value) {
                                    $added = $this->db->insert($this->contractItemTable, array("contract_id" => $lastId, "item_id" => $value));
                                    if ($added) {
                                        $arrWhereTimeline["task_id"] = $value;
                                        $queryTimeline = $this->db->get_where($this->taskTimelineTable, $arrWhereTimeline);
                                        if ($queryTimeline->num_rows() == 1) {
                                            $timelineId = $queryTimeline->row()->id;
                                            $this->db->update($this->taskTimelineTable, array("contract_id" => $lastId), array("id" => $timelineId));
                                        } else {
                                            $data = array(
                                                "parent_id" => $post["parent_id"],
                                                "task_id" => $value,
                                                "contract_id" => $lastId,
                                                "created_at" => date("Y-m-d H:i:s")
                                            );
                                            $this->db->insert($this->taskTimelineTable, $data);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $unitId = $this->adm_task->generateCurrentUnitTaskById($post["parent_id"]);
                    $currentRow = $this->adm_task->generateCurrentTaskById($post["parent_id"]);
                    $currentWo = $this->adm_task->getContractorTaskById($post["parent_id"]);
                    $resultset["response"] = true;
                    $resultset["row"] = $currentRow;
                    $resultset["contractor"] = $currentWo;
                    $resultset["contractor_count"] = count($currentWo);
                    $resultset["item_id"] = (isset($currentRow->id) && $currentRow->id) ? intval($currentRow->id) : 0;
                    $resultset["unit_id"] = $unitId ? intval($unitId) : 0;
                    $resultset["toastr_msg"] = "Contract information has been added.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed saving contract information data!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function checkWoGeneratedCode($data = array()) {
            return $data;
        }

        function getChecklistContent($id = null) {
            if ($id) {
                $isEmpty = $this->clearWorkOrderItemData();
                $data = $this->contractChecklistJson($id);
                if ($data) {
                    $resultset["response"] = true;
                    $resultset["data"] = $data;
                    $resultset["is_empty"] = $isEmpty;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getWoVersionTemplateContent($code = null) {
            $resultset = array();
            if ($code) {
                $isEmpty = $this->clearWorkOrderItemData();
                $html = $this->load->view("pms/contract/form_content/{$code}", null, true);
                $resultset["response"] = true;
                $resultset["html"] = $html;
                $resultset["is_empty"] = $isEmpty;
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getSelect2Project() {
            $arrData = array();
            $get = $this->input->get();
            $this->db->select("a.id, UPPER(CONCAT(b.description, ' | ', c.description)) as text");
            $this->db->from($this->projectTable . " a");
            $this->db->join($this->projectCompanyTable . " b", "b.id = a.company_id");
            $this->db->join($this->projectLocationTable . " c", "c.id = a.location_id");
            $this->db->where("a.is_active", 1);
            $this->db->where("a.status", 1);
            if (isset($get["term"]) && $get["term"]) {
                $this->db->group_start();
                $this->db->like("b.code", $get["term"], "both");
                $this->db->or_like("c.code", $get["term"], "both");
                $this->db->or_like("b.description", $get["term"], "both");
                $this->db->or_like("c.description", $get["term"], "both");
                $this->db->group_end();
            }
            $this->db->order_by("b.description, c.description", "ASC");
            $queryItem = $this->db->get();

            if ($queryItem->num_rows() > 0) {
                $arrData = $queryItem->result();
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getSelect2Checklist() {
            $arrData = array();
            $get = $this->input->get();
            if (isset($get["project_id"]) && $get["project_id"]) {
                $this->db->select("id, UPPER(label) as text");
                $this->db->from($this->checklistItemTable);
                $this->db->where("project_id", $get["project_id"]);
                $this->db->where("is_active", 1);
                $this->db->where("status", 1);
                if (isset($get["term"]) && $get["term"]) {
                    $this->db->like("label", $get["term"], "both");
                }
                $this->db->order_by("label", "ASC");
                $queryItem = $this->db->get();

                if ($queryItem->num_rows() > 0) {
                    $arrData = $queryItem->result();
                }
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getSelect2Units() {
            $arrData = array();
            $get = $this->input->get();
            $selectedItems = (isset($get["selected_items"]) && $get["selected_items"]) ? $get["selected_items"] : array();
            $parentId = $this->generateParentId($selectedItems);
            if (!in_array($parentId, $selectedItems)) {
                $selectedItems[] = $parentId;
            }
            if (isset($get["checklist_id"], $get["project_id"]) && $get["checklist_id"] && $get["project_id"]) {
                $units = $this->getAvailableProjectUnit($get["checklist_id"], $selectedItems);
                if ($units && count($units) > 0) {
                    $this->db->select("id, UPPER(CONCAT('BLOCK ', block, ' LOT ', lot, ' | ', description)) as text, checklist_id");
                    $this->db->from($this->projectUnitTable);
                    $this->db->where("checklist_id", $get["checklist_id"]);
                    $this->db->where("project_id", $get["project_id"]);
                    $this->db->where("block_type", 1);
                    $this->db->where("is_active", 1);
                    $this->db->where("status", 1);
                    if (isset($get["unit_id"]) && $get["unit_id"]) {
                        $this->db->where("id", $get["unit_id"]);
                    } else {
                        $this->db->where_in("id", $units);
                        if (isset($get["term"]) && $get["term"]) {
                            $this->db->group_start();
                            $this->db->like("description", $get["term"], "both");
                            $this->db->or_like("block", $get["term"], "both");
                            $this->db->or_like("lot", $get["term"], "both");
                            $this->db->group_end();
                        }
                    }
                    $this->db->order_by("block, lot", "ASC");
                    $queryItem = $this->db->get();

                    if ($queryItem->num_rows() > 0) {
                        $arrData = $queryItem->result();
                    }
                }
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getCoSelect2MultipleUnits() {
            $arrData = array();
            $get = $this->input->get();
            $selectedItems = (isset($get["selected_items"]) && $get["selected_items"]) ? $get["selected_items"] : array();
            if (isset($get["contract_id"]) && $get["contract_id"]) {
                $tempContract = $this->db->get_where($this->contractTable, array("id" => $get["contract_id"], "status" => 1));
                if ($tempContract->num_rows() == 1) {
                    $tempRow = $tempContract->row();
                    $tempItems = $this->db->get_where($this->contractItemTable, array("contract_id" => $tempRow->id));
                    if ($tempItems->num_rows() > 0) {
                        foreach ($tempItems->result() as $key => $value) {
                            if (!in_array($value->item_id, $selectedItems)) {
                                $selectedItems[] = $value->item_id;
                            }
                        }
                    }

                    $units = $this->getAvailableProjectUnit($tempRow->checklist_id, $selectedItems);
                    if ($units && count($units) > 0) {
                        $this->db->select("id, UPPER(description) as text, checklist_id");
                        $this->db->from($this->projectUnitTable);
                        $this->db->where("checklist_id", $tempRow->checklist_id);
                        $this->db->where("project_id", $tempRow->project_id);
                        $this->db->where("block_type", 1);
                        $this->db->where("is_active", 1);
                        $this->db->where("status", 1);

                        $this->db->where_in("id", $units);
                        if (isset($get["term"]) && $get["term"]) {
                            $this->db->group_start();
                            $this->db->like("description", $get["term"], "both");
                            $this->db->or_like("block", $get["term"], "both");
                            $this->db->or_like("lot", $get["term"], "both");
                            $this->db->group_end();
                        }
                        $this->db->order_by("block, lot", "ASC");
                        $queryItem = $this->db->get();

                        if ($queryItem->num_rows() > 0) {
                            $arrData = $queryItem->result();
                        }
                    }
                }
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getSelect2MultipleUnits() {
            $arrData = array();
            $get = $this->input->get();
            $selectedItems = (isset($get["selected_items"]) && $get["selected_items"]) ? $get["selected_items"] : array();
            $parentId = $this->generateParentId($selectedItems);
            if (!in_array($parentId, $selectedItems)) {
                $selectedItems[] = $parentId;
            }
            if (isset($get["checklist_id"], $get["project_id"], $get["unit_id"]) && $get["checklist_id"] && $get["project_id"] && $get["unit_id"]) {
                $units = $this->getAvailableProjectUnit($get["checklist_id"], $selectedItems);
                if ($units && count($units) > 0) {
                    if (!in_array($get["unit_id"], $units)) {
                        $units[] = $get["unit_id"];
                    }
                    $key = array_search($get["unit_id"], $units);
                    if ($key) {
                        unset($units[$key]);
                    }

                    $this->db->select("id, UPPER(description) as text, checklist_id");
                    $this->db->from($this->projectUnitTable);
                    $this->db->where("checklist_id", $get["checklist_id"]);
                    $this->db->where("project_id", $get["project_id"]);
                    $this->db->where("block_type", 1);
                    $this->db->where("is_active", 1);
                    $this->db->where("status", 1);

                    $this->db->where_in("id", $units);
                    if (isset($get["term"]) && $get["term"]) {
                        $this->db->group_start();
                        $this->db->like("description", $get["term"], "both");
                        $this->db->or_like("block", $get["term"], "both");
                        $this->db->or_like("lot", $get["term"], "both");
                        $this->db->group_end();
                    }
                    $this->db->order_by("block, lot", "ASC");
                    $queryItem = $this->db->get();

                    if ($queryItem->num_rows() > 0) {
                        $arrData = $queryItem->result();
                    }
                }
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getSelect2Lots() {
            $arrData = array();
            $get = $this->input->get();
            $selectedItems = $get["selected_items"];
            $parentId = $this->generateParentId($selectedItems);
            if (!in_array($parentId, $selectedItems)) {
                $selectedItems[] = $parentId;
            }

            if (isset($get["block"], $get["checklist_id"]) && $get["block"] && $get["checklist_id"]) {
                $units = $this->getAssignedTaskUnits($get["checklist_id"], $selectedItems);
                $this->db->select("lot as id, UPPER(CONCAT('Lot ', lot)) as text");
                $this->db->from($this->projectUnitTable);
                $this->db->where("checklist_id", $get["checklist_id"]);
                $this->db->where("block", $get["block"]);
                $this->db->where("block_type", 1);
                $this->db->where("is_active", 1);
                $this->db->where("status", 1);
                if ($units && count($units) > 0) {
                    $this->db->where_not_in("id", $units);
                }
                if (isset($get["term"]) && $get["term"]) {
                    $this->db->like("lot", $get["term"], "both");
                }
                $this->db->group_by("lot");
                $this->db->order_by("lot", "ASC");
                $queryItem = $this->db->get();

                if ($queryItem->num_rows() > 0) {
                    $arrData = $queryItem->result();
                }
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getSelect2BlockLot() {
            $arrData = array();
            $get = $this->input->get();
            $selectedItems = $get["selected_items"];
            $parentId = $this->generateParentId($selectedItems);
            if (!in_array($parentId, $selectedItems)) {
                $selectedItems[] = $parentId;
            }

            if (isset($get["checklist_id"]) && $get["checklist_id"]) {
                $units = $this->getAssignedTaskUnits($get["checklist_id"], $selectedItems);
                $this->db->select("id, UPPER(description) as text");
                $this->db->from($this->projectUnitTable);
                $this->db->where("checklist_id", $get["checklist_id"]);
                $this->db->where("block_type", 1);
                $this->db->where("is_active", 1);
                $this->db->where("status", 1);
                if ($units && count($units) > 0) {
                    $this->db->where_not_in("id", $units);
                }
                if (isset($get["term"]) && $get["term"]) {
                    $this->db->group_start();
                    $this->db->like("description", $get["term"], "both");
                    $this->db->or_like("block", $get["term"], "both");
                    $this->db->or_like("lot", $get["term"], "both");
                    $this->db->group_end();
                }
                $this->db->group_by("description");
                $this->db->order_by("description", "ASC");
                $queryItem = $this->db->get();

                if ($queryItem->num_rows() > 0) {
                    $arrData = $queryItem->result();
                }
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getSelect2Blocks() {
            $arrData = array();
            $get = $this->input->get();
            if (isset($get["checklist_id"], $get["project_id"], $get["selected_items"]) && $get["checklist_id"] && $get["project_id"] && $get["selected_items"] && count($get["selected_items"]) > 0) {
                $this->db->select("block as id, UPPER(CONCAT('BLOCK ', block)) as text");
                $this->db->from($this->projectUnitTable);
                $this->db->where("checklist_id", $get["checklist_id"]);
                $this->db->where("project_id", $get["project_id"]);
                $this->db->where("block_type", 1);
                $this->db->where("is_active", 1);
                $this->db->where("status", 1);
                if (isset($get["block"]) && $get["block"] !== "0") {
                    $this->db->where("block", $get["block"]);
                } else {
                    if (isset($get["term"]) && $get["term"]) {
                        $this->db->like("block", $get["term"], "both");
                    }
                }
                $this->db->group_by("block");
                $this->db->order_by("block", "ASC");
                $queryItem = $this->db->get();

                if ($queryItem->num_rows() > 0) {
                    $arrData = $queryItem->result();
                }
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getSelect2WoVersion() {
            $arrData = array();
            $get = $this->input->get();
            $this->db->select("code as id, UPPER(description) as text");
            $this->db->from($this->woVersionTable);
            $this->db->where("is_active", 1);
            $this->db->where("status", 1);
            if (isset($get["term"]) && $get["term"]) {
                $this->db->like("description", $get["term"], "both");
            }
            $this->db->order_by("id", "ASC");
            $queryItem = $this->db->get();

            if ($queryItem->num_rows() > 0) {
                $arrData = $queryItem->result();
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getVersionTemplate() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $html = "";
                $data = $post["data"];
                $versionTempate = (isset($post['version_template']) && $post['version_template']) ? $post['version_template'] : "";
                unset($post["csrf_token"], $post["data"], $post['version_template']);
                if ($versionTempate) {
                    $availableTaskIds = $this->adm_task->getAvailableTask($data["unit_id"], $data["task_id"]);
                    $this->db->from($this->itemTable);
                    $this->db->where_in("id", $availableTaskIds);
                    $queryItems = $this->db->get();
                    $queryUnit = $this->db->get_where($this->projectUnitTable, array("id" => $data["unit_id"]));
                    $post["row"] = $queryUnit->row();
                    $post["items"] = $queryItems;
                    $post["item_id"] = $data["task_id"];
                    $html = $this->load->view("pms/contract/modal_content/version_template/{$versionTempate}", $post, true);
                }
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function generateModalContractData() {
            $post = $this->input->post();
            return $post;
        }

        function generateContractCode() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                $id = $post["id"];
                unset($post["csrf_token"], $post["id"]);
                $data = $this->adm_project->generateProjectCodeByTaskId($id);
                if ($data) {
                    $tempData = $this->generatePostVersionData($data, $post);
                    $resultset["response"] = true;
                    $resultset["data"] = $tempData;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function generatePostVersionData($data1 = array(), $data2 = array()) {
            $session = $this->core_layout->getCurrentSession();
            /*** str_pad($value, 2, "0", STR_PAD_LEFT); ***/
            $tempItemId = (isset($data2["item_id"]) && $data2["item_id"]) ? $data2["item_id"] : 0;
            $tempSubTask = (isset($data2["sub_task"]) && $data2["sub_task"]) ? $data2["sub_task"] : array();

            if ($tempSubTask && count($tempSubTask) == 1) {
                $tempItemId = $tempSubTask[0];
            }

            $data1 = (array)$data1;
            $data2 = (array)$data2;

            $arrData = array_merge($data1, $data2);

            $tempData = $this->generateGenTaskData($tempItemId);
            if ($tempData && count($tempData) > 0) {
                $tempData = (array)$tempData;
                if ($arrData["wo_version"] == "other_works") {
                    unset($tempData["wo_type"]);
                }
                $arrData = array_merge($arrData, $tempData);
            }

            $blockNo = (isset($arrData["block_no"]) && $arrData["block_no"]) ? str_pad($arrData["block_no"], 2, "0", STR_PAD_LEFT) : "";
            $lotNo = (isset($arrData["lot_no"]) && $arrData["lot_no"]) ? str_pad($arrData["lot_no"], 4, "000", STR_PAD_LEFT) : "";
            if (isset($arrData["wo_version"]) && $arrData["wo_version"]) {
                switch ($arrData["wo_version"]) {
                    case "different_blocks":
                        $arrData["block_type"] = "B";
                        break;
                    case "perimeter_fence":
                        $arrData["block_type"] = "C";
                        $lotNo = (isset($arrData["lot_no"]) && $arrData["lot_no"]) ? str_pad($arrData["lot_no"], 2, "0", STR_PAD_LEFT) : "";
                        break;
                    case "road_lots":
                        $arrData["block_type"] = "R";
                        $lotNo = (isset($arrData["lot_no"]) && $arrData["lot_no"]) ? str_pad($arrData["lot_no"], 2, "0", STR_PAD_LEFT) : "";
                        break;
                    case "other_works":
                        unset($arrData["gen_code"], $arrData["spec_code"]);
                        break;
                    default:
                        $arrData["block_type"] = "";
                        break;
                }
            }


            $generatedWoCode = "";
            $currentDate = (isset($arrData["issued_date"]) && $arrData["issued_date"]) ? date("ymd", strtotime($arrData["issued_date"])) : date("ymd");
            $currentBlock = $blockNo;
            $currentLot = $lotNo;
            $currentBlockType = (isset($arrData["block_type"]) && $arrData["block_type"]) ? $arrData["block_type"] : "";
            $currentWoType = (isset($arrData["wo_type"]) && $arrData["wo_type"]) ? $arrData["wo_type"] : "";
            $currentGenCode = (isset($arrData["gen_code"]) && $arrData["gen_code"]) ? $arrData["gen_code"] : "";
            $currentSpecCode = (isset($arrData["spec_code"]) && $arrData["spec_code"]) ? $arrData["spec_code"] : "";
            $currentProjectCode = (isset($arrData["project_code"]) && $arrData["project_code"]) ? $arrData["project_code"] : "NOPROJECT";
            /** $firstOrder = "{$currentProjectCode}{$currentWoType}{$currentGenCode}{$currentSpecCode}"; original v2 wocode ***/
            $firstOrder = "{$currentProjectCode}{$currentWoType}{$currentGenCode}";
            $secondOrder = "{$currentBlockType}{$currentBlock}{$currentLot}";
            if ($firstOrder && $secondOrder) {
                $generatedWoCode = $firstOrder . "-" . $secondOrder;
            } else if ($firstOrder) {
                $generatedWoCode = $firstOrder;
            } else if ($secondOrder) {
                $generatedWoCode = $secondOrder;
            }

            $generatedWoCode .= "-" . $currentDate;
            $this->db->from($this->woCodeHistoryTable);
            $this->db->like("wo_code", $generatedWoCode, "both");
            $this->db->order_by("id", "DESC");
            $queryWoCode = $this->db->get();

            if ($queryWoCode->num_rows() > 0) {
                $results = $queryWoCode->result();
                $row = $results[0];
                $tempVersion = intval($row->version) + 1;
                $arrData["version"] = $tempVersion;
                $tempVersion = str_pad($tempVersion, 2, "0", STR_PAD_LEFT);
                $generatedWoCode .= "-{$tempVersion}";
            } else {
                $generatedWoCode .= "-01";
                $arrData["version"] = 1;


            }

            $where = array("user_id" => $session["emp_id"]);
            $queryTemp = $this->db->get_where($this->woCodeTempTable, $where);
            $tempArrData = array();

            $tempArrData["wo_version"] = (isset($arrData["wo_version"]) && $arrData["wo_version"]) ? $arrData["wo_version"] : "";
            $tempArrData["wo_code"] = $generatedWoCode;
            $tempArrData["project_code"] = $currentProjectCode;
            $tempArrData["wo_type"] = $currentWoType;
            $tempArrData["general_wo"] = $currentGenCode;
            $tempArrData["specific_wo"] = $currentSpecCode;
            $tempArrData["block_type"] = $currentBlockType;
            $tempArrData["block_no"] = $currentBlock;
            $tempArrData["lot_no"] = $currentLot;
            $tempArrData["ymd_code"] = $currentDate;
            $tempArrData["version"] = $arrData["version"];

            if ($queryTemp->num_rows() == 1) {
                $this->db->update($this->woCodeTempTable, $tempArrData, $where);
            } else {
                $tempArrData["user_id"] = $session["emp_id"];
                $this->db->insert($this->woCodeTempTable, $tempArrData);
            }

            $arrData["wo_code"] = $generatedWoCode;
            return $arrData;
        }

        function generateGenTaskData($id = null) {
            $data = array();
            if ($id) {
                $this->db->select("a.wo_code as spec_code, b.wo_code as gen_code, c.series as wo_type");
                $this->db->from($this->itemTable . " a");
                $this->db->join($this->itemTable . " b", "b.id = a.parent_id", "LEFT");
                $this->db->join($this->woTypeTable . " c", "c.id = a.wo_type_id", "LEFT");
                $this->db->where("a.id", $id);
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    if (!$row->gen_code) {
                        $row->gen_code = $row->spec_code;
                        $row->spec_code = "";
                    }
                    $data = $row;
                }
            }
            return $data;
        }

        function getCurrentContract($id = null) {
            $resultset = array();
            $data = array();
            if ($id) {
                $sqlSelect = "a.*, b.contractor, d.label as task_name, e.extension_date, e.remarks as extension_remarks, COUNT(c.id) as units, ";
                $sqlSelect .= "e.created_by as extended_by, e.extension_status, UPPER(DATE_FORMAT(e.created_at, '%M %e, %Y %h:%i %p')) as extended_at, f.wo_version";

                $this->db->select($sqlSelect);
                $this->db->from($this->contractTable . " a");
                $this->db->join($this->contractorTable . " b", "b.id = a.contractor_id");
                $this->db->join($this->contractUnitTable . " c", "c.contract_id = a.id");
                $this->db->join($this->itemTable . " d", "d.id = c.item_id");
                $this->db->join($this->contractExtensionTable . " e", "e.id = a.extension_id", "LEFT");
                $this->db->join($this->woCodeHistoryTable . " f", "f.id = a.wo_code_id");
                $this->db->where("a.id", $id);
                $this->db->group_by("a.id");
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $tempData = $this->getCurrentContractItems($row->id);
                    $tempRs = (array)$row;
                    $tempInchargeName = "---";
                    $tempForemanName = "---";
                    $tempExtendedName = "---";

                    if ($row->task_incharge) {
                        $tempIncharge = $this->core_layout->getEmployeeData($row->task_incharge);
                        $tempIncharge = (object)$tempIncharge;
                        $tempInchargeName = (isset($tempIncharge->display_name_1) && $tempIncharge->display_name_1) ? $tempIncharge->display_name_1 : "No assigned name";
                    }

                    if ($row->foreman_id) {
                        $tempForeman = $this->core_layout->getEmployeeData($row->foreman_id);
                        $tempForeman = (object)$tempForeman;
                        $tempForemanName = (isset($tempForeman->display_name_1) && $tempForeman->display_name_1) ? $tempForeman->display_name_1 : "No assigned name";
                    }

                    if ($row->extended_by) {
                        $tempExtensionName = $this->core_layout->getEmployeeData($row->extended_by);
                        $tempExtensionName = (object)$tempExtensionName;
                        $tempExtendedName = (isset($tempExtensionName->display_name_1) && $tempExtensionName->display_name_1) ? $tempExtensionName->display_name_1 : "No assigned name";
                    }

                    if (!$row->leadman) {
                        $row->leadman = "---";
                    }

                    $row->incharge = $tempInchargeName;
                    $row->extended_name = $tempExtendedName;
                    $row->foreman = $tempForemanName;

                    $resultset["response"] = true;
                    $resultset["row"] = $row;
                    if ($tempData) {
                        foreach ($tempData as $key => $value) {
                            $resultset[$key] = $value;
                        }
                    }
                    $data = $this->contractChecklistJson($row->checklist_id);
                    $resultset["checklist_data"] = $data;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getCurrentContractItems($id = null) {
            $ids = array();
            $adjustmentIds = array();
            $woTaskIds = array();
            $contractResult = array();
            $adjustmentResult = array();
            $this->db->order_by("id", "ASC");
            $queryItems = $this->db->get_where($this->contractItemTable, array("contract_id" => $id, "status" => 1));
            if ($queryItems->num_rows() > 0) {
                foreach ($queryItems->result() as $key => $value) {
                    $woTaskIds[] = $value->wo_task_id;
                    if ($value->co_type == 3) {
                        $adjustmentIds[$value->wo_task_id][] = $value->item_id;
                        $adjustmentResult[$value->wo_task_id][$value->item_id][] = $value;
                    } else {
                        $ids[$value->wo_task_id][] = $value->item_id;
                        $contractResult[$value->wo_task_id][$value->item_id] = $value;
                    }
                }
            }

            $woCount = 0;
            $items = array();
            $adjustmentItems = array();
            $itemResults = array();
            $itemRows = array();
            $itemAdjustmentRows = array();
            $itemWoTotal = array();
            $itemWoCounter = array();

            $grandTotal = 0;

            if ($woTaskIds && count($woTaskIds) > 0) {
                foreach ($woTaskIds as $kx1 => $vx1) {
                    if (isset($ids[$vx1]) && $ids[$vx1]) {
                        $tempIds = $ids[$vx1];
                        $this->db->select("a.id, a.label as sub_task, a.parent_id, b.label as task");
                        $this->db->from($this->itemTable . " a");
                        $this->db->join($this->itemTable . " b", "b.id = a.parent_id");
                        $this->db->where_in("a.id", $tempIds);
                        $query = $this->db->get();
                        if ($query->num_rows() > 0) {
                            foreach ($query->result() as $key => $value) {
                                $items[$vx1][$value->parent_id] = $value->task;
                                $items[$vx1][$value->id] = $value->sub_task;
                            }
                        }
                    }

                    if (isset($adjustmentIds[$vx1]) && $adjustmentIds[$vx1]) {
                        $tempIds = $adjustmentIds[$vx1];
                        $this->db->select("a.id, a.label as sub_task, a.parent_id, b.label as task");
                        $this->db->from($this->itemTable . " a");
                        $this->db->join($this->itemTable . " b", "b.id = a.parent_id");
                        $this->db->where_in("a.id", $tempIds);
                        $query = $this->db->get();
                        if ($query->num_rows() > 0) {
                            foreach ($query->result() as $key => $value) {
                                $adjustmentItems[$vx1][$value->parent_id] = $value->task;
                                $adjustmentItems[$vx1][$value->id] = $value->sub_task;
                            }
                        }
                    }
                }

                $tempWoCounter = 0;
                $woTaskIds = array_unique($woTaskIds);

                foreach ($woTaskIds as $kx1 => $vx1) {
                    $gTempTotal = 0;
                    if (isset($items[$vx1]) && count($items[$vx1] > 0)) {
                        $tempWoCounter++;
                        $xRows = array();

                        foreach ($items[$vx1] as $key => $value) {
                            $tempRow = array();
                            if (isset($contractResult[$vx1][$key]) && $contractResult[$vx1][$key]) {
                                $xtempQty = $contractResult[$vx1][$key]->qty;
                                $xtempLotCount = $contractResult[$vx1][$key]->lot_count;
                                $xtempTariff = $contractResult[$vx1][$key]->tariff;
                                $xtempUnit = $contractResult[$vx1][$key]->unit;

                                $rowTotal = floatval($xtempQty) * intval($xtempLotCount) * floatval($xtempTariff);
                                $gTempTotal += $rowTotal;
                                $grandTotal += $rowTotal;
                                $tempRow["id"] = $key;
                                $tempRow["label"] = $value;
                                $tempRow["qty"] = $xtempQty;
                                $tempRow["lots"] = $xtempLotCount;
                                $tempRow["unit"] = $xtempUnit;
                                $tempRow["tariff"] = number_format($xtempTariff, 2, ".", ",");
                                $tempRow["total"] = number_format($rowTotal, 2, ".", ",");
                                $tempRow["is_parent"] = false;
                            } else {
                                $tempRow["id"] = $key;
                                $tempRow["label"] = $value;
                                $tempRow["is_parent"] = true;
                            }
                            $xRows[] = $tempRow;
                        }

                        $itemRows[$kx1] = $xRows;
                        $itemWoCounter[$kx1] = $tempWoCounter;
                    }

                    if (isset($adjustmentItems[$vx1]) && count($adjustmentItems[$vx1] > 0)) {
                        $xAdjustmentRows = array();
                        foreach ($adjustmentItems[$vx1] as $key => $value) {
                            $tempRow = array();
                            if (isset($adjustmentResult[$vx1][$key]) && count($adjustmentResult[$vx1][$key]) > 0) {
                                $xTempAdjustment = $adjustmentResult[$vx1][$key];
                                foreach ($xTempAdjustment as $kx => $vx) {
                                    $xtempQty = $vx->qty;
                                    $xtempLotCount = $vx->lot_count;
                                    $xtempTariff = $vx->tariff;
                                    $xtempUnit = $vx->unit;

                                    $rowTotal = floatval($xtempQty) * intval($xtempLotCount) * floatval($xtempTariff);
                                    $gTempTotal += $rowTotal;
                                    $grandTotal += $rowTotal;
                                    $tempRow["id"] = $key;
                                    $tempRow["label"] = $value;
                                    $tempRow["qty"] = $xtempQty;
                                    $tempRow["lots"] = $xtempLotCount;
                                    $tempRow["unit"] = $xtempUnit;
                                    $tempRow["tariff"] = number_format($xtempTariff, 2, ".", ",");
                                    $tempRow["total"] = number_format($rowTotal, 2, ".", ",");
                                    $tempRow["is_parent"] = false;
                                    $xAdjustmentRows[] = $tempRow;
                                }
                            } else {
                                $tempRow["id"] = $key;
                                $tempRow["label"] = $value;
                                $tempRow["is_parent"] = true;
                                $xAdjustmentRows[] = $tempRow;
                            }
                        }

                        $itemAdjustmentRows[$kx1] = $xAdjustmentRows;
                    }
                    $itemWoTotal[$kx1] = number_format($gTempTotal, 2, ".", ",");
                }
            }

            $itemResults["items"] = $itemRows;
            $itemResults["adjustment_items"] = $itemAdjustmentRows;
            $itemResults["sub_total"] = $itemWoTotal;
            $itemResults["item_counter"] = $itemWoCounter;
            $itemResults["item_count"] = count($itemRows);
            $itemResults["wo_task_id"] = $woTaskIds;
            $itemResults["grand_total"] = number_format($grandTotal, 2, ".", ",");
            return $itemResults;

        }

        function getContractAccomplishmentItems($id = null) {
            $ids = array();
            $adjustmentIds = array();
            $woTaskIds = array();
            $contractResult = array();
            $adjustmentResult = array();
            $this->db->select("a.*, SUM(IFNULL(c.accomplishment, 0)) as accomplishment");
            $this->db->from($this->contractItemTable . " a");
            $this->db->join($this->accomplishmentTable . " b", "b.contract_id = a.contract_id", "LEFT");
            $this->db->join($this->accomplishmentItemsTable . " c", "c.parent_id = b.id AND c.contract_item_id = a.id", "LEFT");
            $this->db->where("a.contract_id", $id);
            $this->db->where("a.status", 1);
            $this->db->where("b.status", 1);
            $this->db->group_by("a.id");
            $this->db->order_by("a.id", "ASC");
            $queryItems = $this->db->get();
            if ($queryItems->num_rows() > 0) {
                foreach ($queryItems->result() as $key => $value) {
                    $woTaskIds[] = $value->wo_task_id;
                    if ($value->co_type == 3) {
                        $adjustmentIds[$value->wo_task_id][] = $value->item_id;
                        $adjustmentResult[$value->wo_task_id][$value->item_id][] = $value;
                    } else {
                        $ids[$value->wo_task_id][] = $value->item_id;
                        $contractResult[$value->wo_task_id][$value->item_id] = $value;
                    }
                }
            }

            $woCount = 0;
            $items = array();
            $adjustmentItems = array();
            $itemResults = array();
            $itemRows = array();
            $itemAdjustmentRows = array();
            $itemWoTotal = array();
            $itemRetTotal = array();
            $itemWoCounter = array();

            $grandTotal = 0;

            if ($woTaskIds && count($woTaskIds) > 0) {
                foreach ($woTaskIds as $kx1 => $vx1) {
                    if (isset($ids[$vx1]) && $ids[$vx1]) {
                        $tempIds = $ids[$vx1];
                        $this->db->select("a.id, a.label as sub_task, a.parent_id, b.label as task");
                        $this->db->from($this->itemTable . " a");
                        $this->db->join($this->itemTable . " b", "b.id = a.parent_id");
                        $this->db->where_in("a.id", $tempIds);
                        $query = $this->db->get();
                        if ($query->num_rows() > 0) {
                            foreach ($query->result() as $key => $value) {
                                $items[$vx1][$value->parent_id] = $value->task;
                                $items[$vx1][$value->id] = $value->sub_task;
                            }
                        }
                    }

                    if (isset($adjustmentIds[$vx1]) && $adjustmentIds[$vx1]) {
                        $tempIds = $adjustmentIds[$vx1];
                        $this->db->select("a.id, a.label as sub_task, a.parent_id, b.label as task");
                        $this->db->from($this->itemTable . " a");
                        $this->db->join($this->itemTable . " b", "b.id = a.parent_id");
                        $this->db->where_in("a.id", $tempIds);
                        $query = $this->db->get();
                        if ($query->num_rows() > 0) {
                            foreach ($query->result() as $key => $value) {
                                $adjustmentItems[$vx1][$value->parent_id] = $value->task;
                                $adjustmentItems[$vx1][$value->id] = $value->sub_task;
                            }
                        }
                    }
                }

                $tempWoCounter = 0;
                $woTaskIds = array_unique($woTaskIds);

                foreach ($woTaskIds as $kx1 => $vx1) {
                    $gTempTotal = 0;
                    $gTempRetTotal = 0;
                    if (isset($items[$vx1]) && count($items[$vx1] > 0)) {
                        $tempWoCounter++;
                        $xRows = array();

                        foreach ($items[$vx1] as $key => $value) {
                            $tempRow = array();
                            if (isset($contractResult[$vx1][$key]) && $contractResult[$vx1][$key]) {
                                $contractItemId = $contractResult[$vx1][$key]->id;
                                $xtempQty = $contractResult[$vx1][$key]->qty;
                                $xtempLotCount = $contractResult[$vx1][$key]->lot_count;
                                $xtempTariff = $contractResult[$vx1][$key]->tariff;
                                $xtempUnit = $contractResult[$vx1][$key]->unit;
                                $xtempAccomplishment = $contractResult[$vx1][$key]->accomplishment;

                                $tempQty = floatval($xtempQty) * intval($xtempLotCount);
                                $rowTotal = floatval($xtempQty) * intval($xtempLotCount) * floatval($xtempTariff);

                                $tempRetention = floatval($rowTotal) * 0.10;
                                $tempRetention = floatval($rowTotal) - floatval($tempRetention);

                                $tempRemaining = floatval($tempQty) - floatval($xtempAccomplishment);
                                $tempVariance = (floatval($xtempAccomplishment) / floatval($tempQty)) * 100;
                                $gTempRetTotal += $tempRetention;
                                $gTempTotal += $rowTotal;
                                $grandTotal += $rowTotal;
                                $tempRow["id"] = $key;
                                $tempRow["row_id"] = $contractItemId;
                                $tempRow["label"] = $value;
                                $tempRow["qty"] = $xtempQty;
                                $tempRow["lots"] = $xtempLotCount;
                                $tempRow["unit"] = $xtempUnit;
                                $tempRow["tariff"] = number_format($xtempTariff, 2, ".", ",");
                                $tempRow["total"] = number_format($rowTotal, 2, ".", ",");
                                $tempRow["temp_qty"] = $tempQty;
                                $tempRow["previous_qty"] = $xtempAccomplishment;
                                $tempRow["retention"] = number_format($tempRetention, 2, ".", ",");
                                $tempRow["variance"] = number_format($tempVariance, 2, ".", ",");
                                $tempRow["remaining"] = number_format($tempRemaining, 2, ".", ",");
                                $tempRow["is_parent"] = false;
                            } else {
                                $tempRow["id"] = $key;
                                $tempRow["label"] = $value;
                                $tempRow["is_parent"] = true;
                            }
                            $xRows[] = $tempRow;
                        }

                        $itemRows[$kx1] = $xRows;
                        $itemWoCounter[$kx1] = $tempWoCounter;
                    }

                    if (isset($adjustmentItems[$vx1]) && count($adjustmentItems[$vx1] > 0)) {
                        $xAdjustmentRows = array();
                        foreach ($adjustmentItems[$vx1] as $key => $value) {
                            $tempRow = array();
                            if (isset($adjustmentResult[$vx1][$key]) && count($adjustmentResult[$vx1][$key]) > 0) {
                                $xTempAdjustment = $adjustmentResult[$vx1][$key];
                                foreach ($xTempAdjustment as $kx => $vx) {
                                    $contractItemId = $vx->id;
                                    $xtempQty = $vx->qty;
                                    $xtempLotCount = $vx->lot_count;
                                    $xtempTariff = $vx->tariff;
                                    $xtempUnit = $vx->unit;
                                    $xtempAccomplishment = $vx->accomplishment;

                                    $tempQty = floatval($xtempQty) * intval($xtempLotCount);
                                    $rowTotal = floatval($xtempQty) * intval($xtempLotCount) * floatval($xtempTariff);

                                    $tempRetention = floatval($rowTotal) * 0.10;
                                    $tempRetention = floatval($rowTotal) - floatval($tempRetention);

                                    $tempRemaining = floatval($tempQty) - floatval($xtempAccomplishment);
                                    $tempVariance = (floatval($xtempAccomplishment) / floatval($tempQty)) * 100;
                                    $gTempRetTotal += $tempRetention;
                                    $gTempTotal += $rowTotal;
                                    $grandTotal += $rowTotal;
                                    $tempRow["id"] = $key;
                                    $tempRow["row_id"] = $contractItemId;
                                    $tempRow["label"] = $value;
                                    $tempRow["qty"] = $xtempQty;
                                    $tempRow["lots"] = $xtempLotCount;
                                    $tempRow["unit"] = $xtempUnit;
                                    $tempRow["tariff"] = number_format($xtempTariff, 2, ".", ",");
                                    $tempRow["total"] = number_format($rowTotal, 2, ".", ",");
                                    $tempRow["temp_qty"] = $tempQty;
                                    $tempRow["previous_qty"] = $xtempAccomplishment;
                                    $tempRow["retention"] = number_format($tempRetention, 2, ".", ",");
                                    $tempRow["variance"] = number_format($tempVariance, 2, ".", ",");
                                    $tempRow["remaining"] = number_format($tempRemaining, 2, ".", ",");
                                    $tempRow["is_parent"] = false;
                                    $xAdjustmentRows[] = $tempRow;
                                }
                            } else {
                                $tempRow["id"] = $key;
                                $tempRow["label"] = $value;
                                $tempRow["is_parent"] = true;
                                $xAdjustmentRows[] = $tempRow;
                            }
                        }

                        $itemAdjustmentRows[$kx1] = $xAdjustmentRows;
                    }
                    $itemWoTotal[$kx1] = number_format($gTempTotal, 2, ".", ",");
                    $itemRetTotal[$kx1] = number_format($gTempRetTotal, 2, ".", ",");
                }
            }

            $itemResults["items"] = $itemRows;
            $itemResults["adjustment_items"] = $itemAdjustmentRows;
            $itemResults["sub_total"] = $itemWoTotal;
            $itemResults["sub_ret_total"] = $itemRetTotal;
            $itemResults["item_counter"] = $itemWoCounter;
            $itemResults["item_count"] = count($itemRows);
            $itemResults["wo_task_id"] = $woTaskIds;
            $itemResults["grand_total"] = number_format($grandTotal, 2, ".", ",");
            return $itemResults;
        }

        function getCurrentContractAccomplishment($id = null) {
            $resultset = array();
            if ($id) {
                $sqlSelect = "a.*, b.contractor, d.label as task_name, e.extension_date, e.remarks as extension_remarks, COUNT(c.id) as units, ";
                $sqlSelect .= "e.created_by as extended_by, e.extension_status, UPPER(DATE_FORMAT(e.created_at, '%M %e, %Y %h:%i %p')) as extended_at, f.wo_version";

                $this->db->select($sqlSelect);
                $this->db->from($this->contractTable . " a");
                $this->db->join($this->contractorTable . " b", "b.id = a.contractor_id");
                $this->db->join($this->contractUnitTable . " c", "c.contract_id = a.id");
                $this->db->join($this->itemTable . " d", "d.id = c.item_id");
                $this->db->join($this->contractExtensionTable . " e", "e.id = a.extension_id", "LEFT");
                $this->db->join($this->woCodeHistoryTable . " f", "f.id = a.wo_code_id");
                $this->db->where("a.id", $id);
                $this->db->group_by("a.id");
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $tempData = $this->getContractAccomplishmentItems($row->id);
                    $tempRs = (array)$row;
                    $tempInchargeName = "---";
                    $tempForemanName = "---";
                    $tempExtendedName = "---";

                    if ($row->task_incharge) {
                        $tempIncharge = $this->core_layout->getEmployeeData($row->task_incharge);
                        $tempIncharge = (object)$tempIncharge;
                        $tempInchargeName = (isset($tempIncharge->display_name_1) && $tempIncharge->display_name_1) ? $tempIncharge->display_name_1 : "No assigned name";
                    }

                    if ($row->foreman_id) {
                        $tempForeman = $this->core_layout->getEmployeeData($row->foreman_id);
                        $tempForeman = (object)$tempForeman;
                        $tempForemanName = (isset($tempForeman->display_name_1) && $tempForeman->display_name_1) ? $tempForeman->display_name_1 : "No assigned name";
                    }

                    if ($row->extended_by) {
                        $tempExtensionName = $this->core_layout->getEmployeeData($row->extended_by);
                        $tempExtensionName = (object)$tempExtensionName;
                        $tempExtendedName = (isset($tempExtensionName->display_name_1) && $tempExtensionName->display_name_1) ? $tempExtensionName->display_name_1 : "No assigned name";
                    }

                    if (!$row->leadman) {
                        $row->leadman = "---";
                    }

                    $row->incharge = $tempInchargeName;
                    $row->extended_name = $tempExtendedName;
                    $row->foreman = $tempForemanName;

                    $resultset["response"] = true;
                    $resultset["row"] = $row;
                    if ($tempData) {
                        foreach ($tempData as $key => $value) {
                            $resultset[$key] = $value;
                        }
                    }
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getCurrentContractUnitCount($contractId = null) {
            $arrData = array();
            if ($contractId) {
                $this->db->select("a.item_id as aid, b.item_id as bid, c.parent_id as pid");
                $this->db->from($this->contractUnitTable . " a");
                $this->db->join($this->contractItemTable . " b", "b.contract_id = a.contract_id");
                $this->db->join($this->itemTable . " c", "c.id = b.item_id");
                $this->db->where("a.contract_id", $contractId);
                $contractUnit = $this->db->get();
                if ($contractUnit->num_rows() > 0) {
                    foreach ($contractUnit->result() as $key => $value) {
                        if ($value->aid == $value->pid) {
                            if (isset($arrData[$value->bid]) && $arrData[$value->bid]) {
                                $arrData[$value->bid]++;
                            } else {
                                $arrData[$value->bid] = 1;
                            }
                        }
                    }
                }
            }

            return $arrData;
        }

        function getContractProject($id = null) {
            $resultset = array();
            if ($id) {
                $arrData = $this->getCurrentProjectUnitByContractId($id);
                if ($arrData && count($arrData) > 0) {
                    $resultset["response"] = true;
                    $resultset["rows"] = $arrData;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getCurrentProjectUnitByContractId($id = null) {
            $data = array();
            if ($id) {
                $this->db->select("c.id, c.description, c.block, c.lot, b.contract_status");
                $this->db->from($this->contractTable . " a");
                $this->db->join($this->contractUnitTable . " b", "b.contract_id = a.id");
                $this->db->join($this->projectUnitTable . " c", "c.id = b.unit_id");
                $this->db->where("a.id", $id);
                $this->db->group_by("c.id");
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $data = $query->result();
                }
            }
            return $data;
        }

        function contractChecklistJson($id = null, $ids = array()) {
            $arrData = array();
            if ($id || $id == "0") {
                $tempIds = $this->getAvailableItemQty($id);
                $query = $this->db->get_where($this->checklistPrivilegeTable, array("checklist_id" => $id));
                if ($query->num_rows() == 1) {
                    $row = $query->row_array();
                    $checklistResource = unserialize($row["checklist_resource"]);
                    if ($checklistResource) {
                        $tempResourceItem = array();
                        $isChangeOrder = false;

                        $arrRates = $this->getAvailableRateItems($row["id"]);
                        if ($ids) {
                            $isChangeOrder = true;
                            foreach ($checklistResource as $id) {
                                $tempWhere = array("id" => $id, "is_active" => 1, "status" => 1);
                                $this->db->from($this->itemTable);
                                $this->db->where($tempWhere);
                                if ($ids) {
                                    $this->db->where_not_in("id", $ids);
                                }
                                $queryItems = $this->db->get();
                                if ($queryItems->num_rows() == 1) {
                                    $tempRow = $queryItems->row();
                                    if ($tempRow->parent_id !== "0") {
                                        if (isset($tempResourceItem[$tempRow->parent_id]) && $tempResourceItem[$tempRow->parent_id]) {
                                            $tempResourceItem[$tempRow->parent_id] += 1;
                                        } else {
                                            $tempResourceItem[$tempRow->parent_id] = 1;
                                        }
                                    }
                                }
                            }
                        }

                        foreach ($checklistResource as $id) {
                            $tempWhere = array("id" => $id, "is_active" => 1, "status" => 1);
                            $this->db->from($this->itemTable);
                            $this->db->where($tempWhere);
                            if ($ids) {
                                $this->db->where_not_in("id", $ids);
                            }
                            $queryItems = $this->db->get();
                            if ($queryItems->num_rows() == 1) {
                                $row = $queryItems->row_array();
                                $tempRow = (object)$row;
                                $tempId = $tempRow->id;
                                $parentId = $tempRow->parent_id;

                                $flag = false;
                                $type = ($parentId) ? "child" : "root";
                                $templabel = $row["label"];
                                $arrRates = json_decode(json_encode($arrRates), true);

                                $hasRate = false;
                                if (isset($arrRates[$tempId]) && $arrRates[$tempId] && $type == "root") {
                                    /*** $currentRate = $arrRates[$tempId];
                                     * $totalTariff = array_sum($currentRate['tariff']);
                                     * $totalTariff = number_format($totalTariff, 2, ".", ",");
                                     * $templabel .= " - <strong>[ {$totalTariff} ]</strong>"; ***/
                                    $hasRate = true;
                                } else if (isset($arrRates[$tempId]) && $arrRates[$tempId] && $type == "child" && in_array($tempId, $tempIds)) {
                                    /*** $currentRate = $arrRates[$tempId];
                                     * $currentTariff = number_format($currentRate['tariff'], "2", ".", ",");
                                     * $currentUnit = $currentRate['unit'];
                                     * $templabel .= " - <strong>[ {$currentTariff} / {$currentRate['unit']} ]</strong>"; ***/
                                    $hasRate = true;
                                }

                                $data = array();
                                $data["id"] = $row["id"];
                                $data["type"] = $type;
                                $data["parent"] = ($parentId) ? $parentId : "#";
                                $data["text"] = $templabel;
                                $data["a_attr"]["class"] = ($parentId) ? "" : "parent_node";
                                $data["li_attr"]["class"] = ($parentId) ? "" : "jstree-parent_node";

                                $parentId = intval($parentId);
                                if ($parentId !== 0) {
                                    $flag = $this->checkParentExist($parentId);
                                } else {
                                    $flag = true;
                                }

                                if ($flag && $hasRate) {
                                    $arrData[] = $data;

                                    $parent = json_decode($this->getSequenceParent($parentId), true);
                                    $p_id = (int)$parent["id"];
                                    
                                    $filter = array_filter($arrData, function ($value) use ($p_id) {
                                        return (int)$value["id"] === $p_id;
                                    });

                                    if (count($filter) <= 0 && $parent !== NULL) {
                                        array_push($arrData, $parent);
                                    }
                                }
                            }
                        }

                        if ($isChangeOrder && $arrData && count($arrData) > 0) {
                            foreach ($arrData as $key => $value) {
                                $tempRow = (object)$value;
                                if ($tempRow->parent == "#") {
                                    if (isset($tempResourceItem[$tempRow->id]) == false) {
                                        unset($arrData[$key]);
                                    }
                                }
                            }

                            $tempArrData = array();
                            foreach ($arrData as $key => $value) {
                                $tempArrData[] = $value;
                            }
                            $arrData = $tempArrData;
                        }
                    }
                }
            }
            return $arrData;
        }

        private function getSequenceParent($id) {
            $data = new StdClass();
            $this->db->select("id, 'root' `type`, '#' parent, label text");
            $this->db->where("id", $id);
            $data = $this->db->get($this->itemTable)->row();

            if (!empty($data)) {
                $data->a_attr["class"] = "parent_node";
                $data->li_attr["class"] = "jstree-parent_node";
            }

            return json_encode($data);
        }

        function getRateItemsByChecklistId($id = null, $arrIds = array()) {
            $arrData = array();
            if ($id && $arrIds) {
                $this->db->select("b.item_id, b.tariff, b.unit");
                $this->db->from($this->checklistPrivilegeTable . " a");
                $this->db->join($this->checklistPrivilegeRateTable . " b", "b.privilege_id = a.id");
                $this->db->where("a.checklist_id", $id);
                $this->db->where_in("b.item_id", $arrIds);
                $queryItems = $this->db->get();
                if ($queryItems->num_rows() > 0) {
                    $arrData = $queryItems->result();
                }
            }

            return $arrData;
        }

        function getAvailableItemQty($checklistId = null) {
            $arrData = array();
            if ($checklistId) {
                $query = $this->db->get_where($this->checklistApprovedQtyTable, array("checklist_id" => $checklistId));
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $value) {
                        if (floatval($value->qty) !== 0) {
                            $arrData[] = $value->item_id;
                        }
                    }
                }
            }
            return $arrData;
        }

        function getAvailableRateItems($privilegeId = null) {
            $arrData = array();
            if ($privilegeId) {
                $this->db->select("a.item_id, a.tariff, a.unit, b.parent_id");
                $this->db->from($this->checklistPrivilegeRateTable . " a");
                $this->db->join($this->itemTable . " b", "b.id = a.item_id");
                $this->db->where("a.privilege_id", $privilegeId);
                $this->db->where("b.parent_id !=", 0);
                $this->db->group_start();
                $this->db->where("a.tariff !=", "0.00");
                $this->db->or_where("a.tariff !=", "");
                $this->db->group_end();
                $this->db->group_start();
                $this->db->where("a.unit !=", null);
                $this->db->or_where("a.unit !=", "");
                $this->db->group_end();
                $queryRates = $this->db->get();
                if ($queryRates->num_rows() > 0) {
                    foreach ($queryRates->result() as $rs) {
                        $itemId = $rs->item_id;
                        $parentId = $rs->parent_id;
                        $currentTariff = floatval($rs->tariff);
                        unset($rs->item_id, $rs->parent_id);
                        $arrData[$parentId]["tariff"][] = $currentTariff;
                        $arrData[$itemId] = $rs;
                    }
                }
            }

            return $arrData;
        }

        private function checkParentExist($parentId = null) {
            if ($parentId) {
                $query = $this->db->get_where($this->itemTable, array("is_active" => 1, "id" => $parentId));
                if ($query->num_rows() > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getAssignedTaskUnits($checklistId = null, $taskIds = array()) {
            $arrData = array();
            $this->db->select("a.id");
            $this->db->from($this->projectUnitTable . " a");
            $this->db->join($this->taskTable . " b", "b.unit_id = a.id");
            $this->db->join($this->contractUnitTable . " c", "c.task_id = b.id AND c.item_id = b.task_id AND c.unit_id = b.unit_id");
            $this->db->where("c.contract_status !=", 3);
            /*** 3 ***/
            if ($checklistId) {
                $this->db->where("a.checklist_id", $checklistId);
            }
            if ($taskIds && count($taskIds) > 0) {
                $this->db->where_in("c.item_id", $taskIds);
            }
            $this->db->group_by("a.id");
            $this->db->order_by("a.id", "ASC");
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $kk => $rs) {
                    $arrData[] = $rs->id;
                }
            }

            $this->db->select("b.lots");
            $this->db->from($this->contractTable . " a");
            $this->db->join($this->contractItemTable . " b", "b.contract_id = a.id");
            $this->db->where("a.checklist_id", $checklistId);
            $this->db->where("a.status !=", 3);
            $this->db->where_in("b.item_id", $taskIds);
            $queryLots = $this->db->get();
            if ($queryLots->num_rows() > 0) {
                foreach ($queryLots->result() as $rs) {
                    $tempLots = unserialize($rs->lots);
                    if ($tempLots && count($tempLots) > 0) {
                        foreach ($tempLots as $vv) {
                            if (!in_array($vv, $arrData)) {
                                $arrData[] = $vv;
                            }
                        }
                    }
                }
            }

            return $arrData;
        }

        function getContractUnits($id = null) {
            $resultset = array();
            if ($id) {
                $this->db->select("b.id, b.description");
                $this->db->from($this->contractUnitTable . " a");
                $this->db->join($this->projectUnitTable . " b", "b.id = a.unit_id");
                $this->db->where("a.contract_status", 1);
                $this->db->where("a.contract_id", $id);
                $queryUnits = $this->db->get();
                if ($queryUnits->num_rows() > 0) {
                    $units = array();
                    foreach ($queryUnits->result() as $kk => $vv) {
                        $units[] = $vv->id;
                    }
                    $unitCostUpdates = $this->getContractUnitCostUpdates($id, $units);

                    $resultset["response"] = true;
                    $resultset["units"] = $units;
                    $resultset["unit_cost"] = $unitCostUpdates;
                    $resultset["cost_count"] = count($unitCostUpdates);
                    $resultset["rows"] = $queryUnits->result();
                    $resultset["count"] = $queryUnits->num_rows();
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getContractUnitCostUpdates($contract_id = null) {
            $arrData = array();
            $this->db->select("b.id, g.label, b.tariff, f.tariff as rc_tariff");
            $this->db->from($this->contractTable . " a");
            $this->db->join($this->contractItemTable . " b", "b.contract_id = a.id");
            $this->db->join($this->contractUnitTable . " c", "c.contract_id = a.id");
            $this->db->join($this->projectUnitTable . " d", "d.id = c.unit_id");
            $this->db->join($this->checklistPrivilegeTable . " e", "e.checklist_id = d.checklist_id");
            $this->db->join($this->checklistPrivilegeRateTable . " f", "f.privilege_id = e.id AND f.item_id = b.item_id");
            $this->db->join($this->itemTable . " g", "g.id = b.item_id");
            $this->db->where("a.id", $contract_id);
            $this->db->group_by("b.id");
            $queryUnitCost = $this->db->get();
            if ($queryUnitCost->num_rows() > 0) {
                foreach ($queryUnitCost->result() as $key => $value) {
                    $tempTariff0 = number_format($value->tariff, 2, ".", "");
                    $tempTariff1 = number_format($value->rc_tariff, 2, ".", "");
                    if ($tempTariff0 !== $tempTariff1) {
                        $value->temp_format0 = number_format($value->tariff, 2, ".", ",");
                        $value->temp_format1 = number_format($value->rc_tariff, 2, ".", ",");
                        $value->tariff = $tempTariff0;
                        $value->rc_tariff = $tempTariff1;
                        $arrData[] = $value;
                    }
                }
            }

            return $arrData;
        }

        function getContractLotCount($items = array(), $units = array()) {
            $itemCount = array();
            if (($items && $units) && (count($items) > 0 && count($units) > 0)) {
                $tempItems = array();
                foreach ($items as $key => $id) {
                    $queryItems = $this->db->get_where($this->itemTable, array("id" => $id, "parent_id !=" => 0));
                    if ($queryItems->num_rows() == 1) {
                        $tempRow = $queryItems->row();
                        $tempItems[] = $tempRow->id;
                    }
                }

                if ($tempItems && count($tempItems) > 0) {
                    foreach ($tempItems as $key => $vv) {
                        $tempRow = array();

                        $contractItem = $this->db->get_where($this->contractItemTable, array("item_id" => $vv));
                        if ($contractItem->num_rows() > 0) {
                            foreach ($contractItem->result() as $key => $value) {
                                $tempLots = unserialize($value->lots);
                                foreach ($units as $unit) {
                                    if (!in_array($unit, $tempLots) && !in_array($unit, $tempRow)) {
                                        $tempRow[] = $unit;
                                    }
                                }

                                $tempItem = $this->db->get_where($this->itemTable, array("id" => $value->item_id, "parent_id !=" => "0"));
                                if ($tempItem->num_rows() == 1) {
                                    $tempRowx = $tempItem->row();
                                    $this->db->from($this->contractUnitTable);
                                    $this->db->where("item_id", $tempRowx->parent_id);
                                    $this->db->where_in("unit_id", $units);
                                    $contractUnit = $this->db->get();
                                    if ($contractUnit->num_rows() > 0) {
                                        foreach ($contractUnit->result() as $ii => $xx) {
                                            if ($xx->contract_status == "3" && !in_array($xx->unit_id, $tempRow)) {
                                                $tempRow[] = $xx->unit_id;
                                            }
                                        }
                                    } else {
                                        foreach ($units as $unit) {
                                            if (!in_array($unit, $tempRow)) {
                                                $tempRow[] = $unit;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            foreach ($units as $kkk => $vvv) {
                                if (!in_array($vvv, $tempRow)) {
                                    $tempRow[] = $vvv;
                                }
                            }
                        }
                        $itemCount[$vv] = count($tempRow);
                    }
                }
            }
            return $itemCount;
        }

        function __getContractLotCount($items = array(), $units = array()) {
            $itemCount = array();
            if (($items && $units) && (count($items) > 0 && count($units) > 0)) {
                $tempItems = array();
                $tempUnits = array();

                foreach ($items as $key => $id) {
                    /*** $this->db->from($this->contractItemTable);
                     * $this->db->where("item_id", $id);
                     * $query = $this->db->get();
                     * if($query->num_rows() > 0){
                     * foreach ($query->result() as $key => $value) {
                     * if($value->status == "3" && !in_array($value->item_id, $tempItems)){
                     * $tempItems[] = $value->item_id;
                     * }
                     * }
                     * }else{
                     * $queryItems = $this->db->get_where($this->itemTable, array("id"=>$id, "parent_id !=" =>0));
                     * if($queryItems->num_rows() == 1){
                     * $tempRow = $queryItems->row();
                     * $tempItems[] = $tempRow->id;
                     * }
                     * } ***/

                    $queryItems = $this->db->get_where($this->itemTable, array("id" => $id, "parent_id !=" => 0));
                    if ($queryItems->num_rows() == 1) {
                        $tempRow = $queryItems->row();
                        $tempItems[] = $tempRow->id;
                    }
                }
            }

            if ($tempItems && count($tempItems) > 0) {
                /*** foreach ($units as $key => $id) {
                 * $this->db->from($this->contractUnitTable);
                 * $this->db->where("unit_id", $id);
                 * $this->db->where_in("item_id", $tempItems);
                 * $queryUnit = $this->db->get();
                 * if($queryUnit->num_rows() > 0){
                 * var_dump($queryUnit->result());
                 * foreach ($queryUnit->result() as $key => $value) {
                 * if($value->contract_status == "3"){
                 * $tempUnits[] = $value->unit_id;
                 * }
                 * }
                 * }else{
                 * $tempUnits[] = $id;
                 * }
                 * } ***/

                foreach ($tempItems as $key => $vv) {
                    $initCount = 0;
                    if ($units && count($units) > 0) {
                        foreach ($units as $key => $unit) {
                            $this->db->select("a.unit_id, d.item_id, d.status");
                            $this->db->from($this->contractUnitTable . " a");
                            $this->db->join($this->taskTable . " b", "b.id = a.task_id");
                            $this->db->join($this->taskTimelineTable . " c", "c.parent_id = b.id");
                            $this->db->join($this->contractItemTable . " d", "d.contract_id = c.contract_id");
                            $this->db->where("a.unit_id", $unit);
                            $this->db->where("d.item_id", $vv);
                            $query = $this->db->get();
                            if ($query->num_rows() > 0) {
                                foreach ($query->result() as $key => $value) {
                                    if ($value->status == "3") {
                                        $initCount++;
                                    }
                                }
                            } else {
                                $initCount++;
                            }
                        }
                    }

                    $itemCount[$vv] = $initCount;
                }
            }

            return $itemCount;
        }

        function getContractAvailableLot($items = array(), $units = array()) {
            $arrData = array();
            if (($items && $units) && (count($items) > 0 && count($units) > 0)) {
                $tempItems = array();
                foreach ($items as $key => $id) {
                    $queryItems = $this->db->get_where($this->itemTable, array("id" => $id, "parent_id !=" => 0));
                    if ($queryItems->num_rows() == 1) {
                        $tempRow = $queryItems->row();
                        $tempItems[] = $tempRow->id;
                    }
                }

                if ($tempItems && count($tempItems) > 0) {
                    foreach ($tempItems as $key => $vv) {
                        $tempRow = array();

                        $contractItem = $this->db->get_where($this->contractItemTable, array("item_id" => $vv));
                        if ($contractItem->num_rows() > 0) {
                            foreach ($contractItem->result() as $key => $value) {
                                $tempLots = unserialize($value->lots);
                                foreach ($units as $unit) {
                                    if (!in_array($unit, $tempLots) && !in_array($unit, $tempRow)) {
                                        $tempRow[] = $unit;
                                    }
                                }

                                $tempItem = $this->db->get_where($this->itemTable, array("id" => $value->item_id, "parent_id !=" => "0"));
                                if ($tempItem->num_rows() == 1) {
                                    $tempRowx = $tempItem->row();
                                    $this->db->from($this->contractUnitTable);
                                    $this->db->where("item_id", $tempRowx->parent_id);
                                    $this->db->where_in("unit_id", $units);
                                    $contractUnit = $this->db->get();
                                    if ($contractUnit->num_rows() > 0) {
                                        foreach ($contractUnit->result() as $ii => $xx) {
                                            if ($xx->contract_status == "3" && !in_array($xx->unit_id, $tempRow)) {
                                                $tempRow[] = $xx->unit_id;
                                            }
                                        }
                                    } else {
                                        foreach ($units as $unit) {
                                            if (!in_array($unit, $tempRow)) {
                                                $tempRow[] = $unit;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            foreach ($units as $kkk => $vvv) {
                                if (!in_array($vvv, $tempRow)) {
                                    $tempRow[] = $vvv;
                                }
                            }
                        }
                        $arrData[$vv] = $tempRow;
                    }
                }
            }


            return $arrData;
        }

        function __getContractAvailableLot($items = array(), $units = array()) {
            $arrData = array();
            if (($items && $units) && (count($items) > 0 && count($units) > 0)) {
                $tempItems = array();
                $tempUnits = array();

                foreach ($items as $key => $id) {
                    $queryItems = $this->db->get_where($this->itemTable, array("id" => $id, "parent_id !=" => 0));
                    if ($queryItems->num_rows() == 1) {
                        $tempRow = $queryItems->row();
                        $tempItems[] = $tempRow->id;
                    }
                }
            }

            if ($tempItems && count($tempItems) > 0) {
                foreach ($tempItems as $key => $vv) {
                    $tempRow = array();
                    if ($units && count($units) > 0) {
                        foreach ($units as $key => $unit) {
                            $this->db->select("a.unit_id, d.status");
                            $this->db->from($this->contractUnitTable . " a");
                            $this->db->join($this->taskTable . " b", "b.id = a.task_id");
                            $this->db->join($this->taskTimelineTable . " c", "c.parent_id = b.id");
                            $this->db->join($this->contractItemTable . " d", "d.contract_id = c.contract_id");
                            $this->db->where("a.unit_id", $unit);
                            $this->db->where("d.item_id", $vv);
                            $query = $this->db->get();
                            if ($query->num_rows() > 0) {
                                foreach ($query->result() as $key => $value) {
                                    if ($value->status == "3" && !in_array($value->unit_id, $tempRow)) {
                                        $tempRow[] = $value->unit_id;
                                    }
                                }
                            } else {
                                if (!in_array($unit, $tempRow)) {
                                    $tempRow[] = $unit;
                                }
                            }
                        }
                    }

                    $arrData[$vv] = $tempRow;
                }
            }

            return $arrData;
        }

        function getAvailableProjectUnit($checklistId = null, $items = array()) {
            $tempIds = array();
            if ($checklistId && $items) {
                $arrids = array();
                $arrItems = array();
                $arrUnits = array();

                $this->db->from($this->itemTable);
                $this->db->where_in("id", $items);
                $this->db->where("parent_id !=", 0);
                $queryItems = $this->db->get();
                if ($queryItems->num_rows() > 0) {
                    foreach ($queryItems->result() as $key => $value) {
                        $arrItems[] = $value->id;
                    }
                }

                $arrWhere = array();
                $arrWhere["checklist_id"] = $checklistId;
                $arrWhere["is_active"] = 1;
                $arrWhere["status"] = 1;
                $queryLots = $this->db->get_where($this->projectUnitTable, $arrWhere);
                if ($queryLots->num_rows() > 0) {
                    foreach ($queryLots->result() as $key => $value) {
                        $arrUnits[] = $value->id;
                    }
                }

                foreach ($arrItems as $key => $item) {
                    $this->db->select("a.item_id, b.unit_id");
                    $this->db->from($this->contractItemTable . " a");
                    $this->db->join($this->contractUnitTable . " b", "b.contract_id = a.contract_id");
                    $this->db->where("a.status", 1);
                    $this->db->where("a.item_id", $item);
                    $query = $this->db->get();
                    if ($query->num_rows() > 0) {
                        foreach ($query->result() as $key => $value) {
                            if (isset($arrids[$value->item_id]) && $arrids[$value->item_id]) {
                                if (!in_array($value->unit_id, $arrids[$value->item_id])) {
                                    $arrids[$value->item_id][] = intval($value->unit_id);
                                }
                            } else {
                                $arrids[$value->item_id][] = intval($value->unit_id);
                            }
                        }
                    }
                }

                foreach ($arrUnits as $key => $unit) {
                    foreach ($arrItems as $key => $id) {
                        if (isset($arrids[$id]) && $arrids[$id]) {
                            if (!in_array($unit, $arrids[$id]) && !in_array($unit, $tempIds)) {
                                $tempIds[] = $unit;
                            }
                        } else {
                            if (!in_array($unit, $tempIds)) {
                                $tempIds[] = $unit;
                            }
                        }
                    }
                }
            }

            return $tempIds;
        }

        function getCurrentContractCo($id = null) {
            $resultset = array();
            if ($id) {
                $queryContract = $this->db->get_where($this->contractTable, array("id" => $id));
                if ($queryContract->num_rows() == 1) {
                    $tempRow = $queryContract->row();
                    $tempData = $this->getCurrentContractItems($tempRow->id);

                    $arrIds = array();
                    $this->db->select("a.item_id, b.parent_id");
                    $this->db->from($this->contractItemTable . " a");
                    $this->db->join($this->itemTable . " b", "b.id = a.item_id");
                    $this->db->where("a.contract_id", $id);
                    $queryItems = $this->db->get();
                    if ($queryItems->num_rows() > 0) {
                        foreach ($queryItems->result() as $key => $value) {
                            if (!in_array($value->item_id, $arrIds)) {
                                $arrIds[] = intval($value->item_id);
                            }
                        }
                        sort($arrIds);
                    }
                    $data = $this->contractChecklistJson($tempRow->checklist_id, $arrIds);
                    $resultset["response"] = true;
                    $resultset["checklist_id"] = $arrIds;
                    $resultset["checklist_data"] = $data;
                    $resultset["checklist_count"] = count($data);

                    if ($tempData && count($tempData) > 0) {
                        foreach ($tempData as $key => $value) {
                            $resultset[$key] = $value;
                        }
                    }
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getTempTaskItems($isTemp = false) {
            $resultset = array();
            $arrData = array();
            $employeeId = $this->core_layout->getCurrentEmployeeId();
            if ($isTemp == true) {
                $temp = $this->db->get_where($this->contractTempItemsTable, array("user_id" => $employeeId));
                if ($temp->num_rows() > 0) {
                    $ids = array();
                    foreach ($temp->result() as $key => $value) {
                        $ids[] = $value->item_id;
                    }
                    $this->db->from($this->itemTable);
                    $this->db->where_in("id", $ids);
                    $queryItems = $this->db->get();
                    if ($queryItems->num_rows() > 0) {
                        foreach ($queryItems->result() as $key => $value) {
                            $tempRow = array();
                            $tempRow["id"] = $value->id;
                            $tempRow["type"] = ($value->parent_id !== "0") ? "child" : "root";
                            $tempRow["parent"] = ($value->parent_id !== "0") ? $value->parent_id : "#";
                            $tempRow["text"] = $value->label;
                            $tempRow["a_attr"]["class"] = ($value->parent_id !== "0") ? "" : "parent_node";
                            $tempRow["li_attr"]["class"] = ($value->parent_id !== "0") ? "" : "jstree-parent_node";
                            $arrData[] = $tempRow;
                        }
                    }
                }
            }

            if ($arrData && count($arrData) > 0) {
                $resultset["response"] = true;
                $resultset["url"] = $isTemp ? site_url("pms/contract/do_post_event/remove_temp_task_items") : site_url("pms/contract/do_post_event/remove_current_task_items");
                $resultset["data"] = $arrData;
                $resultset["count"] = count($arrData);
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function remove_temp_task_items() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post) && $post) {
                $ctr = 0;
                $employeeId = $this->core_layout->getCurrentEmployeeId();
                unset($post["csrf_token"]);
                $nodes = json_decode($post["nodes"], true);
                if ($nodes && count($nodes) > 0) {
                    foreach ($nodes as $key => $value) {
                        $value = (object)$value;
                        $tempState = $value->state;
                        $tempState = (object)$tempState;
                        if ($tempState->selected == true) {
                            $params = array("user_id" => $employeeId, "item_id" => $value->id);
                            $deleted = $this->db->delete($this->contractTempItemsTable, $params);
                            if ($deleted) {
                                $ctr++;
                            }
                        }
                    }
                }
                if ($ctr > 0) {
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getTempItemBlocks($id = null) {
            if ($id) {
                $query = $this->db->get_where($this->contractTempItemsTable, array("id" => $id));
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $tempLots = unserialize($row->lots);
                    $tempBlock = array();
                    $tempUnits = array();
                    $this->db->from($this->projectUnitTable);
                    $this->db->where("is_active", 1);
                    $this->db->where("status", 1);
                    $this->db->where_in("id", $tempLots);
                    $queryItems = $this->db->get();
                    if ($queryItems->num_rows() > 0) {
                        foreach ($queryItems->result() as $key => $value) {
                            if (!in_array($value->block, $tempBlock)) {
                                $tempBlock[] = $value->block;
                            }
                            $tempUnits[$value->block][] = $value;
                        }
                    }

                    if ($tempBlock && count($tempBlock) > 0) {
                        $resultset["response"] = true;
                        $resultset["id"] = $id;
                        $resultset["count"] = count($tempBlock);
                        $resultset["blocks"] = $tempBlock;
                        $resultset["units"] = $tempUnits;
                        $resultset["url"] = site_url("pms/contract/do_post_event/update_contract_temp_blocks");
                    } else {
                        $resultset["response"] = false;
                    }
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function update_contract_temp_blocks() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post) && $post) {
                $tempData = array();
                foreach ($post["unit"] as $key => $value) {
                    foreach ($value as $kk => $vv) {
                        if (!in_array($vv, $tempData)) {
                            $tempData[] = $vv;
                        }
                    }
                }
                unset($post["csrf_token"], $post["unit"]);

                $data = array();
                $data["lots"] = serialize($tempData);
                $data["lot_count"] = count($tempData);

                $updated = $this->db->update($this->contractTempItemsTable, $data, $post);
                if ($updated) {
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function clearWorkOrderItemData() {
            $employeeId = $this->core_layout->getCurrentEmployeeId();
            $removed = $this->db->delete($this->contractTempItemsTable, array("user_id" => $employeeId));
            return ($removed && $this->db->affected_rows() > 0) ? true : false;
        }

        function renderContractItems($id = null, $wo_task_id = null) {
            $resultset = array();
            if ($id && $wo_task_id) {
                $tempUser = $this->core_layout->getCurrentEmployeeId();
                $removed = $this->db->delete($this->contractTempItemsTable, array("user_id" => $tempUser, "is_editable" => 1));
                if ($removed) {
                    $this->db->select("a.id as temp_id, a.item_id, a.qty, a.unit, a.lots, a.lot_count, a.tariff as unit_cost, b.parent_id, b.sort");
                    $this->db->from($this->contractItemTable . " a");
                    $this->db->join($this->itemTable . " b", "b.id = a.item_id");
                    $this->db->where("a.contract_id", $id);
                    $this->db->where("a.wo_task_id", $wo_task_id);
                    $queryItems = $this->db->get();
                    if ($queryItems->num_rows() > 0) {
                        $addedCount = 0;
                        foreach ($queryItems->result() as $kk => $vv) {
                            $tempWhere = array();
                            $tempWhere["item_id"] = $vv->parent_id;
                            $tempWhere["user_id"] = $tempUser;
                            $isItemExist = $this->db->get_where($this->contractTempItemsTable, $tempWhere);
                            if ($isItemExist->num_rows() == 0) {
                                $tempWhere["is_editable"] = 1;
                                $addedParent = $this->db->insert($this->contractTempItemsTable, $tempWhere);
                                if ($addedParent) {
                                    $addedCount++;
                                }
                            }
                        }

                        foreach ($queryItems->result() as $key => $value) {
                            $value->user_id = $tempUser;
                            $value->is_editable = 1;
                            $added = $this->db->insert($this->contractTempItemsTable, $value);
                            if ($added) {
                                $addedCount++;
                            }
                        }
                        if (count($addedCount) > 0) {
                            $resultset["response"] = true;
                        } else {
                            $resultset["response"] = false;
                        }
                    } else {
                        $resultset["response"] = false;
                    }
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function set_adjustment_current_contract() {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"]) && $post["id"]) {
                $tempArray = array();
                $changeOrderCount = 0;
                $tempId = $post["id"];
                $tempWoTaskId = $post["wo_task_id"];
                $tempWhere = array();
                $tempWhere["id"] = $tempId;

                unset($post["csrf_token"], $post["id"], $post["wo_task_id"], $post["issued_date"], $post["due_date"]);
                $allowChangeOrder = $this->getContractItemData($tempId, $tempWoTaskId);
                if ($allowChangeOrder) {
                    $currentItems = array();
                    $userId = $this->core_layout->getCurrentEmployeeId();
                    $tempSelect = "id, item_id, qty as current_qty, unit as current_unit, ";
                    $tempSelect .= "tariff as current_tariff, lot_count as current_lot_count, ";
                    $tempSelect .= "lots as current_lots";
                    $this->db->select($tempSelect);
                    $queryContractItem = $this->db->get_where($this->contractItemTable, array("contract_id" => $tempId, "wo_task_id" => $tempWoTaskId));
                    if ($queryContractItem->num_rows() > 0) {
                        foreach ($queryContractItem->result() as $key => $item) {
                            $tempContractId = $item->id;
                            unset($item->id);
                            $currentItems[$tempContractId] = $item;
                        }
                    }

                    $nTempSelect = "item_id, parent_id, qty, unit, lots, lot_count, unit_cost as tariff";
                    $this->db->select($nTempSelect);
                    $queryTempItems = $this->db->get_where($this->contractTempItemsTable, array("user_id" => $userId, "is_editable" => 1));
                    if ($queryTempItems->num_rows() > 0) {
                        $woData = array();
                        $woData["contract_id"] = $tempId;
                        $woData["wo_task_type"] = 3;
                        $woData["created_by"] = $userId;

                        $createdHistory = $this->db->insert($this->woTaskHistoryTable, $woData);
                        if ($createdHistory) {
                            $woHistoryId = $this->db->insert_id();
                            $tempWoData = array();
                            $tempWoData["wo_task_id"] = $woHistoryId;
                            $tempWoData["contract_id"] = $tempId;
                            $tempWoData["created_by"] = $userId;

                            $tempCo = $this->db->insert($this->changeOrderTable, $tempWoData);
                            if ($tempCo) {
                                $coId = $this->db->insert_id();
                                $activeUnits = array();
                                foreach ($queryTempItems->result() as $key => $item) {
                                    $tempIdx = $item->item_id;
                                    if ($item->parent_id !== "0") {
                                        $activeUnits[$item->parent_id] = array();
                                        $tempActiveUnits = unserialize($item->lots);
                                        if ($tempActiveUnits && count($tempActiveUnits) > 0 && $item->parent_id) {
                                            foreach ($tempActiveUnits as $xx => $zz) {
                                                if (!in_array($zz, $activeUnits[$item->parent_id])) {
                                                    $activeUnits[$item->parent_id][] = $zz;
                                                }
                                            }
                                        }

                                        $tempData = $item;
                                        unset($item->parent_id, $item->item_id);
                                        if (isset($currentItems[$tempIdx]) && $currentItems[$tempIdx]) {
                                            foreach ($currentItems[$tempIdx] as $kk => $vv) {
                                                $tempData->$kk = $vv;
                                            }
                                        }
                                        $tempData->parent_id = $coId;
                                        $coItems = $this->db->insert($this->changeOrderItemsTable, $tempData);
                                        if ($coItems) {
                                            $changeOrderCount++;
                                        }
                                    }
                                }

                                $generatedCode = $this->generateCoWorkCode($tempId);
                                if ($generatedCode["response"] && $changeOrderCount > 0) {
                                    $post["change_order_id"] = $coId;
                                    $tempData = $generatedCode["data"];
                                    if ($tempData && count($tempData) > 0) {
                                        foreach ($tempData as $key => $wodata) {
                                            $post[$key] = $wodata;
                                        }

                                        unset($tempData["wo_code"]);
                                        $updateTempWoTaskId = $this->db->update($this->woTaskHistoryTable, array("is_active" => 0), array("id" => $tempWoTaskId));
                                        if ($updateTempWoTaskId && $this->db->affected_rows() > 0) {
                                            $updateWoCode = $this->db->update($this->woTaskHistoryTable, $tempData, array("id" => $woHistoryId));
                                            if ($updateWoCode && $this->db->affected_rows() > 0) {
                                                $tempTaskUnits = array();
                                                $tempTaskItems = array();

                                                $updateContractItemTaskId = $this->db->update($this->contractItemTable, array("wo_task_id" => $woHistoryId), array("contract_id" => $tempId, "wo_task_id" => $tempWoTaskId, "status" => 1));
                                                if ($updateContractItemTaskId && $this->db->affected_rows() > 0) {
                                                    $nnTempSelect = "item_id, parent_id, qty, unit, lots, lot_count, unit_cost as tariff";
                                                    $this->db->select($nnTempSelect);
                                                    $__queryTempItems = $this->db->get_where($this->contractTempItemsTable, array("user_id" => $userId, "is_editable" => 1));
                                                    if ($__queryTempItems->num_rows() > 0) {
                                                        foreach ($__queryTempItems->result() as $key => $itemx) {
                                                            $tempDataRender = $itemx;
                                                            if ($itemx->parent_id !== "0") {
                                                                unset($tempDataRender->parent_id);
                                                                $tempDataRender->contract_id = $tempId;
                                                                $tempDataRender->wo_task_id = $woHistoryId;
                                                                $tempDataRender->status = 1;
                                                                $tempDataRender->co_type = 3;
                                                                $added = $this->db->insert($this->contractItemTable, $tempDataRender);
                                                                if ($added) {
                                                                    $addedItemId = $this->db->insert_id();
                                                                    $this->db->select("a.*, b.parent_id");
                                                                    $this->db->from($this->contractItemTable . " a");
                                                                    $this->db->join($this->itemTable . " b", "b.id = a.item_id");
                                                                    $this->db->where("a.id", $addedItemId);
                                                                    $this->db->where("b.parent_id !=", 0);
                                                                    $queryTask = $this->db->get();
                                                                    if ($queryTask->num_rows() == 1) {
                                                                        $tempxRow = $queryTask->row();
                                                                        $tempxLots = unserialize($tempxRow->lots);
                                                                        $tempTaskItems[$tempxRow->parent_id] = array();
                                                                        if (!in_array($tempxRow->item_id, $tempTaskItems[$tempxRow->parent_id])) {
                                                                            $tempTaskItems[$tempxRow->parent_id][] = $tempxRow->item_id;
                                                                        }
                                                                        $tempTaskUnits[$tempxRow->parent_id] = array();
                                                                        foreach ($tempxLots as $kkk => $vvv) {
                                                                            if (!in_array($vvv, $tempTaskUnits[$tempxRow->parent_id])) {
                                                                                $tempTaskUnits[$tempxRow->parent_id][] = $vvv;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                    if ($tempTaskUnits && count($tempTaskUnits) > 0) {
                                                        foreach ($tempTaskUnits as $kitem => $kunits) {
                                                            foreach ($kunits as $kxx => $vxx) {
                                                                $queryTempTask = $this->db->get_where($this->taskTable, array("unit_id" => $vxx, "task_id" => $kitem));
                                                                if ($queryTempTask->num_rows() == 1) {
                                                                    $qtempTask = $queryTempTask->row();
                                                                    if (isset($tempTaskItems[$qtempTask->task_id]) && $tempTaskItems[$qtempTask->task_id]) {
                                                                        foreach ($tempTaskItems[$qtempTask->task_id] as $kxa => $vxa) {
                                                                            $queryTempTaskChild = $this->db->get_where($this->taskTimelineTable, array("parent_id" => $qtempTask->id, "task_id" => $vxa));
                                                                            if ($queryTempTaskChild->num_rows() == 0) {
                                                                                $tempTaskTimeline = array();
                                                                                $tempTaskTimeline["parent_id"] = $qtempTask->id;
                                                                                $tempTaskTimeline["task_id"] = $vxa;
                                                                                $tempTaskTimeline["contract_id"] = $tempId;
                                                                                $tempTaskTimeline["created_at"] = date("Y-m-d H:i:s");
                                                                                $this->db->insert($this->taskTimelineTable, $tempTaskTimeline);
                                                                            }
                                                                        }
                                                                    }
                                                                } else {
                                                                    $queryContract = $this->db->get_where($this->contractTable, array("id" => $tempId, "status" => 1));
                                                                    if ($queryContract->num_rows() == 1) {
                                                                        $contRow = $queryContract->row();
                                                                        $tempTaskData = array();
                                                                        $tempTaskData["unit_id"] = $vxx;
                                                                        $tempTaskData["task_id"] = $kitem;
                                                                        $tempTaskData["contract_id"] = $contRow->id;
                                                                        $tempTaskData["issued_date"] = $contRow->issued_date;
                                                                        $tempTaskData["due_date"] = $contRow->due_date;
                                                                        $addedTask = $this->db->insert($this->taskTable, $tempTaskData);
                                                                        if ($addedTask) {
                                                                            $parentTaskId = $this->db->insert_id();
                                                                            $xtempWhereUnits = array();
                                                                            $xtempWhereUnits["wo_task_id"] = $woHistoryId;
                                                                            $xtempWhereUnits["contract_id"] = $tempId;
                                                                            $xtempWhereUnits["unit_id"] = $vxx;
                                                                            $xtempWhereUnits["item_id"] = $kitem;
                                                                            $xtempWhereUnits["task_id"] = $parentTaskId;

                                                                            $searchTempUnit = $this->db->get_where($this->contractUnitTable, $xtempWhereUnits);
                                                                            if ($searchTempUnit->num_rows() == 0) {
                                                                                $this->db->insert($this->contractUnitTable, $xtempWhereUnits);
                                                                            }

                                                                            if (isset($tempTaskItems[$kitem]) && $tempTaskItems[$kitem]) {
                                                                                foreach ($tempTaskItems[$kitem] as $kxaa => $vxaa) {
                                                                                    $this->db->select("id");
                                                                                    $_queryTempTaskChild = $this->db->get_where($this->taskTimelineTable, array("parent_id" => $parentTaskId, "task_id" => $vxaa));
                                                                                    if ($_queryTempTaskChild->num_rows() == 0) {
                                                                                        $xtempTaskTimeline = array();
                                                                                        $xtempTaskTimeline["parent_id"] = $parentTaskId;
                                                                                        $xtempTaskTimeline["task_id"] = $vxaa;
                                                                                        $xtempTaskTimeline["contract_id"] = $tempId;
                                                                                        $xtempTaskTimeline["created_at"] = date("Y-m-d H:i:s");
                                                                                        $this->db->insert($this->taskTimelineTable, $xtempTaskTimeline);
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                }
                                            }

                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $updated = $this->db->update($this->contractTable, $post, $tempWhere);
                if ($updated && $this->db->affected_rows() > 0) {
                    $resultset["response"] = true;
                    $resultset["redirect"] = site_url("pms/contract/view_work_order/{$tempId}");
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function set_additional_current_contract() {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"]) && $post["id"]) {
                $tempArray = array();
                $changeOrderCount = 0;
                $tempId = $post["id"];
                $tempWoTaskId = $post["wo_task_id"];
                $tempWhere = array();
                $tempWhere["id"] = $tempId;

                $tempAdditionalData = array();
                $tempAdditionalData["issued_date"] = $post["issued_date"];
                $tempAdditionalData["due_date"] = $post["due_date"];

                unset($post["csrf_token"], $post["id"], $post["wo_task_id"], $post["issued_date"], $post["due_date"]);

                $allowChangeOrder = $this->getContractItemData($tempId, $tempWoTaskId);
                if ($allowChangeOrder) {
                    $currentItems = array();
                    $userId = $this->core_layout->getCurrentEmployeeId();
                    $tempSelect = "id, item_id, qty as current_qty, unit as current_unit, ";
                    $tempSelect .= "tariff as current_tariff, lot_count as current_lot_count, ";
                    $tempSelect .= "lots as current_lots";
                    $this->db->select($tempSelect);
                    $queryContractItem = $this->db->get_where($this->contractItemTable, array("contract_id" => $tempId, "wo_task_id" => $tempWoTaskId));
                    if ($queryContractItem->num_rows() > 0) {
                        foreach ($queryContractItem->result() as $key => $item) {
                            $tempContractId = $item->id;
                            unset($item->id);
                            $currentItems[$tempContractId] = $item;
                        }
                    }

                    $nTempSelect = "item_id, parent_id, qty, unit, lots, lot_count, unit_cost as tariff";
                    $this->db->select($nTempSelect);
                    $queryTempItems = $this->db->get_where($this->contractTempItemsTable, array("user_id" => $userId, "is_editable" => 1));
                    if ($queryTempItems->num_rows() > 0) {
                        $woData = array();
                        $woData["contract_id"] = $tempId;
                        $woData["wo_task_type"] = 2;
                        $woData["created_by"] = $userId;


                        $createdHistory = $this->db->insert($this->woTaskHistoryTable, $woData);
                        if ($createdHistory) {
                            $woHistoryId = $this->db->insert_id();
                            $tempWoData = array();
                            $tempWoData["wo_task_id"] = $woHistoryId;
                            $tempWoData["contract_id"] = $tempId;
                            $tempWoData["created_by"] = $userId;

                            if (isset($tempAdditionalData) && count($tempAdditionalData) > 0) {
                                foreach ($tempAdditionalData as $kxq => $vxq) {
                                    $tempWoData[$kxq] = $vxq;
                                }
                            }

                            $tempCo = $this->db->insert($this->changeOrderTable, $tempWoData);
                            if ($tempCo) {
                                $coId = $this->db->insert_id();
                                $activeUnits = array();
                                foreach ($queryTempItems->result() as $key => $item) {
                                    $tempIdx = $item->item_id;
                                    if ($item->parent_id !== "0") {
                                        $activeUnits[$item->parent_id] = array();
                                        $tempActiveUnits = unserialize($item->lots);
                                        if ($tempActiveUnits && count($tempActiveUnits) > 0 && $item->parent_id) {
                                            foreach ($tempActiveUnits as $xx => $zz) {
                                                if (!in_array($zz, $activeUnits[$item->parent_id])) {
                                                    $activeUnits[$item->parent_id][] = $zz;
                                                }
                                            }
                                        }

                                        $tempData = $item;
                                        unset($item->parent_id, $item->item_id);
                                        if (isset($currentItems[$tempIdx]) && $currentItems[$tempIdx]) {
                                            foreach ($currentItems[$tempIdx] as $kk => $vv) {
                                                $tempData->$kk = $vv;
                                            }
                                        }
                                        $tempData->parent_id = $coId;
                                        $coItems = $this->db->insert($this->changeOrderItemsTable, $tempData);
                                        if ($coItems) {
                                            $changeOrderCount++;
                                        }
                                    }
                                }

                                $generatedCode = $this->generateCoWorkCode($tempId);
                                if ($generatedCode["response"] && $changeOrderCount > 0) {
                                    $post["change_order_id"] = $coId;
                                    $tempData = $generatedCode["data"];
                                    if ($tempData && count($tempData) > 0) {
                                        foreach ($tempData as $key => $wodata) {
                                            $post[$key] = $wodata;
                                        }

                                        unset($tempData["wo_code"]);
                                        $updateWoCode = $this->db->update($this->woTaskHistoryTable, $tempData, array("id" => $woHistoryId));
                                        if ($updateWoCode && $this->db->affected_rows() > 0) {
                                            $tempTaskUnits = array();
                                            $tempTaskItems = array();

                                            $nnTempSelect = "item_id, parent_id, qty, unit, lots, lot_count, unit_cost as tariff";
                                            $this->db->select($nnTempSelect);
                                            $__queryTempItems = $this->db->get_where($this->contractTempItemsTable, array("user_id" => $userId, "is_editable" => 1));
                                            if ($__queryTempItems->num_rows() > 0) {
                                                foreach ($__queryTempItems->result() as $key => $itemx) {
                                                    $tempDataRender = $itemx;
                                                    if ($itemx->parent_id !== "0") {
                                                        unset($tempDataRender->parent_id);
                                                        $tempDataRender->contract_id = $tempId;
                                                        $tempDataRender->wo_task_id = $woHistoryId;
                                                        $tempDataRender->status = 1;
                                                        $added = $this->db->insert($this->contractItemTable, $tempDataRender);
                                                        if ($added) {
                                                            $addedItemId = $this->db->insert_id();
                                                            $this->db->select("a.*, b.parent_id");
                                                            $this->db->from($this->contractItemTable . " a");
                                                            $this->db->join($this->itemTable . " b", "b.id = a.item_id");
                                                            $this->db->where("a.id", $addedItemId);
                                                            $this->db->where("b.parent_id !=", 0);
                                                            $queryTask = $this->db->get();
                                                            if ($queryTask->num_rows() == 1) {
                                                                $tempxRow = $queryTask->row();
                                                                $tempxLots = unserialize($tempxRow->lots);
                                                                $tempTaskItems[$tempxRow->parent_id] = array();
                                                                if (!in_array($tempxRow->item_id, $tempTaskItems[$tempxRow->parent_id])) {
                                                                    $tempTaskItems[$tempxRow->parent_id][] = $tempxRow->item_id;
                                                                }
                                                                $tempTaskUnits[$tempxRow->parent_id] = array();
                                                                foreach ($tempxLots as $kkk => $vvv) {
                                                                    if (!in_array($vvv, $tempTaskUnits[$tempxRow->parent_id])) {
                                                                        $tempTaskUnits[$tempxRow->parent_id][] = $vvv;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            if ($tempTaskUnits && count($tempTaskUnits) > 0) {
                                                foreach ($tempTaskUnits as $kitem => $kunits) {
                                                    foreach ($kunits as $kxx => $vxx) {
                                                        $queryTempTask = $this->db->get_where($this->taskTable, array("unit_id" => $vxx, "task_id" => $kitem));
                                                        if ($queryTempTask->num_rows() == 1) {
                                                            $qtempTask = $queryTempTask->row();
                                                            if (isset($tempTaskItems[$qtempTask->task_id]) && $tempTaskItems[$qtempTask->task_id]) {
                                                                foreach ($tempTaskItems[$qtempTask->task_id] as $kxa => $vxa) {
                                                                    $queryTempTaskChild = $this->db->get_where($this->taskTimelineTable, array("parent_id" => $qtempTask->id, "task_id" => $vxa));
                                                                    if ($queryTempTaskChild->num_rows() == 0) {
                                                                        $tempTaskTimeline = array();
                                                                        $tempTaskTimeline["parent_id"] = $qtempTask->id;
                                                                        $tempTaskTimeline["task_id"] = $vxa;
                                                                        $tempTaskTimeline["contract_id"] = $tempId;
                                                                        $tempTaskTimeline["created_at"] = date("Y-m-d H:i:s");
                                                                        $this->db->insert($this->taskTimelineTable, $tempTaskTimeline);
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            $queryContract = $this->db->get_where($this->contractTable, array("id" => $tempId, "status" => 1));
                                                            if ($queryContract->num_rows() == 1) {
                                                                $contRow = $queryContract->row();
                                                                $tempTaskData = array();
                                                                $tempTaskData["unit_id"] = $vxx;
                                                                $tempTaskData["task_id"] = $kitem;
                                                                $tempTaskData["contract_id"] = $contRow->id;
                                                                $tempTaskData["issued_date"] = $contRow->issued_date;
                                                                $tempTaskData["due_date"] = $contRow->due_date;
                                                                $addedTask = $this->db->insert($this->taskTable, $tempTaskData);
                                                                if ($addedTask) {
                                                                    $parentTaskId = $this->db->insert_id();
                                                                    $xtempWhereUnits = array();
                                                                    $xtempWhereUnits["wo_task_id"] = $woHistoryId;
                                                                    $xtempWhereUnits["contract_id"] = $tempId;
                                                                    $xtempWhereUnits["unit_id"] = $vxx;
                                                                    $xtempWhereUnits["item_id"] = $kitem;
                                                                    $xtempWhereUnits["task_id"] = $parentTaskId;

                                                                    $searchTempUnit = $this->db->get_where($this->contractUnitTable, $xtempWhereUnits);
                                                                    if ($searchTempUnit->num_rows() == 0) {
                                                                        $this->db->insert($this->contractUnitTable, $xtempWhereUnits);
                                                                    }

                                                                    if (isset($tempTaskItems[$kitem]) && $tempTaskItems[$kitem]) {
                                                                        foreach ($tempTaskItems[$kitem] as $kxaa => $vxaa) {
                                                                            $this->db->select("id");
                                                                            $_queryTempTaskChild = $this->db->get_where($this->taskTimelineTable, array("parent_id" => $parentTaskId, "task_id" => $vxaa));
                                                                            if ($_queryTempTaskChild->num_rows() == 0) {
                                                                                $xtempTaskTimeline = array();
                                                                                $xtempTaskTimeline["parent_id"] = $parentTaskId;
                                                                                $xtempTaskTimeline["task_id"] = $vxaa;
                                                                                $xtempTaskTimeline["contract_id"] = $tempId;
                                                                                $xtempTaskTimeline["created_at"] = date("Y-m-d H:i:s");
                                                                                $this->db->insert($this->taskTimelineTable, $xtempTaskTimeline);
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $updated = $this->db->update($this->contractTable, $post, $tempWhere);
                if ($updated && $this->db->affected_rows() > 0) {
                    $resultset["response"] = true;
                    $resultset["redirect"] = site_url("pms/contract/view_work_order/{$tempId}");
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;

        }

        function update_current_contract() {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"]) && $post["id"]) {
                $tempArray = array();
                $changeOrderCount = 0;
                $tempId = $post["id"];
                $tempWoTaskId = $post["wo_task_id"];
                $tempWhere = array();
                $tempWhere["id"] = $tempId;
                unset($post["csrf_token"], $post["id"], $post["wo_task_id"]);

                $allowChangeOrder = $this->getContractItemData($tempId, $tempWoTaskId);
                if ($allowChangeOrder) {
                    $currentItems = array();
                    $userId = $this->core_layout->getCurrentEmployeeId();
                    $tempSelect = "id, item_id, qty as current_qty, unit as current_unit, ";
                    $tempSelect .= "tariff as current_tariff, lot_count as current_lot_count, ";
                    $tempSelect .= "lots as current_lots";
                    $this->db->select($tempSelect);
                    $queryContractItem = $this->db->get_where($this->contractItemTable, array("contract_id" => $tempId, "wo_task_id" => $tempWoTaskId));
                    if ($queryContractItem->num_rows() > 0) {
                        foreach ($queryContractItem->result() as $key => $item) {
                            $tempContractId = $item->id;
                            unset($item->id);
                            $currentItems[$tempContractId] = $item;
                        }
                    }

                    $nTempSelect = "item_id, parent_id, qty, unit, lots, lot_count, unit_cost as tariff";
                    $this->db->select($nTempSelect);
                    $queryTempItems = $this->db->get_where($this->contractTempItemsTable, array("user_id" => $userId, "is_editable" => 1));
                    if ($queryTempItems->num_rows() > 0) {
                        $woData = array();
                        $woData["contract_id"] = $tempId;
                        $woData["wo_task_type"] = 1;
                        $woData["created_by"] = $userId;


                        $createdHistory = $this->db->insert($this->woTaskHistoryTable, $woData);
                        if ($createdHistory) {
                            $woHistoryId = $this->db->insert_id();
                            $tempWoData = array();
                            $tempWoData["wo_task_id"] = $woHistoryId;
                            $tempWoData["contract_id"] = $tempId;
                            $tempWoData["created_by"] = $userId;

                            $tempCo = $this->db->insert($this->changeOrderTable, $tempWoData);
                            if ($tempCo) {
                                $coId = $this->db->insert_id();
                                $activeUnits = array();
                                foreach ($queryTempItems->result() as $key => $item) {
                                    $tempIdx = $item->item_id;
                                    if ($item->parent_id !== "0") {
                                        $activeUnits[$item->parent_id] = array();
                                        $tempActiveUnits = unserialize($item->lots);
                                        if ($tempActiveUnits && count($tempActiveUnits) > 0 && $item->parent_id) {
                                            foreach ($tempActiveUnits as $xx => $zz) {
                                                if (!in_array($zz, $activeUnits[$item->parent_id])) {
                                                    $activeUnits[$item->parent_id][] = $zz;
                                                }
                                            }
                                        }

                                        $tempData = $item;
                                        unset($item->parent_id, $item->item_id);
                                        if (isset($currentItems[$tempIdx]) && $currentItems[$tempIdx]) {
                                            foreach ($currentItems[$tempIdx] as $kk => $vv) {
                                                $tempData->$kk = $vv;
                                            }
                                        }
                                        $tempData->parent_id = $coId;
                                        $coItems = $this->db->insert($this->changeOrderItemsTable, $tempData);
                                        if ($coItems) {
                                            $changeOrderCount++;
                                        }
                                    }
                                }


                                $generatedCode = $this->generateCoWorkCode($tempId);
                                if ($generatedCode["response"] && $changeOrderCount > 0) {
                                    $post["change_order_id"] = $coId;
                                    $tempData = $generatedCode["data"];
                                    if ($tempData && count($tempData) > 0) {
                                        foreach ($tempData as $key => $wodata) {
                                            $post[$key] = $wodata;
                                        }
                                        unset($tempData["wo_code"]);
                                        $updateTempWoTaskId = $this->db->update($this->woTaskHistoryTable, array("is_active" => 0), array("id" => $tempWoTaskId));
                                        if ($updateTempWoTaskId && $this->db->affected_rows() > 0) {
                                            $updateWoCode = $this->db->update($this->woTaskHistoryTable, $tempData, array("id" => $woHistoryId));
                                            if ($updateWoCode && $this->db->affected_rows() > 0) {
                                                $tempTaskUnits = array();
                                                $tempTaskItems = array();

                                                $updateContractItemStatus = $this->db->update($this->contractItemTable, array("status" => 0), array("contract_id" => $tempId, "wo_task_id" => $tempWoTaskId));
                                                if ($updateContractItemStatus && $this->db->affected_rows() > 0) {
                                                    $nnTempSelect = "item_id, parent_id, qty, unit, lots, lot_count, unit_cost as tariff";
                                                    $this->db->select($nnTempSelect);
                                                    $__queryTempItems = $this->db->get_where($this->contractTempItemsTable, array("user_id" => $userId, "is_editable" => 1));
                                                    if ($__queryTempItems->num_rows() > 0) {
                                                        foreach ($__queryTempItems->result() as $key => $itemx) {
                                                            $tempDataRender = $itemx;
                                                            if ($itemx->parent_id !== "0") {
                                                                $tempIdx = $itemx->item_id;
                                                                $this->db->select("id");
                                                                $querySearchItem = $this->db->get_where($this->contractItemTable, array("contract_id" => $tempId, "wo_task_id" => $tempWoTaskId, "item_id" => $tempIdx, "status" => 0));
                                                                if ($querySearchItem->num_rows() == 1) {
                                                                    $tempRowData = $querySearchItem->row_array();
                                                                    unset($tempDataRender->item_id, $tempDataRender->parent_id);
                                                                    $tempDataRender->wo_task_id = $woHistoryId;
                                                                    $tempDataRender->status = 1;
                                                                    $this->db->update($this->contractItemTable, $tempDataRender, $tempRowData);
                                                                } else {
                                                                    unset($tempDataRender->parent_id);
                                                                    $tempDataRender->contract_id = $tempId;
                                                                    $tempDataRender->wo_task_id = $woHistoryId;
                                                                    $tempDataRender->status = 1;
                                                                    $added = $this->db->insert($this->contractItemTable, $tempDataRender);
                                                                    if ($added) {
                                                                        $addedItemId = $this->db->insert_id();
                                                                        $this->db->select("a.*, b.parent_id");
                                                                        $this->db->from($this->contractItemTable . " a");
                                                                        $this->db->join($this->itemTable . " b", "b.id = a.item_id");
                                                                        $this->db->where("a.id", $addedItemId);
                                                                        $this->db->where("b.parent_id !=", 0);
                                                                        $queryTask = $this->db->get();
                                                                        if ($queryTask->num_rows() == 1) {
                                                                            $tempxRow = $queryTask->row();
                                                                            $tempxLots = unserialize($tempxRow->lots);
                                                                            $tempTaskItems[$tempxRow->parent_id] = array();
                                                                            if (!in_array($tempxRow->item_id, $tempTaskItems[$tempxRow->parent_id])) {
                                                                                $tempTaskItems[$tempxRow->parent_id][] = $tempxRow->item_id;
                                                                            }
                                                                            $tempTaskUnits[$tempxRow->parent_id] = array();
                                                                            foreach ($tempxLots as $kkk => $vvv) {
                                                                                if (!in_array($vvv, $tempTaskUnits[$tempxRow->parent_id])) {
                                                                                    $tempTaskUnits[$tempxRow->parent_id][] = $vvv;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                                $updateContractUnitStatus = $this->db->update($this->contractUnitTable, array("contract_status" => 3), array("contract_id" => $tempId, "wo_task_id" => $tempWoTaskId));
                                                if ($updateContractUnitStatus && $this->db->affected_rows() > 0) {
                                                    if ($activeUnits && count($activeUnits) > 0) {
                                                        foreach ($activeUnits as $ak => $xunits) {
                                                            if ($xunits && count($xunits) > 0) {
                                                                foreach ($xunits as $iii => $vvv) {
                                                                    $tempActiveWhere = array();
                                                                    $tempActiveWhere["item_id"] = $ak;
                                                                    $tempActiveWhere["unit_id"] = $vvv;
                                                                    $tempActiveWhere["contract_id"] = $tempId;
                                                                    $this->db->update($this->contractUnitTable, array("wo_task_id" => $woHistoryId, "contract_status" => 1), $tempActiveWhere);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                                if ($tempTaskUnits && count($tempTaskUnits) > 0) {
                                                    foreach ($tempTaskUnits as $kitem => $kunits) {
                                                        foreach ($kunits as $kxx => $vxx) {
                                                            $queryTempTask = $this->db->get_where($this->taskTable, array("unit_id" => $vxx, "task_id" => $kitem));
                                                            if ($queryTempTask->num_rows() == 1) {
                                                                $qtempTask = $queryTempTask->row();
                                                                if (isset($tempTaskItems[$qtempTask->task_id]) && $tempTaskItems[$qtempTask->task_id]) {
                                                                    foreach ($tempTaskItems[$qtempTask->task_id] as $kxa => $vxa) {
                                                                        $queryTempTaskChild = $this->db->get_where($this->taskTimelineTable, array("parent_id" => $qtempTask->id, "task_id" => $vxa));
                                                                        if ($queryTempTaskChild->num_rows() == 0) {
                                                                            $tempTaskTimeline = array();
                                                                            $tempTaskTimeline["parent_id"] = $qtempTask->id;
                                                                            $tempTaskTimeline["task_id"] = $vxa;
                                                                            $tempTaskTimeline["contract_id"] = $tempId;
                                                                            $tempTaskTimeline["created_at"] = date("Y-m-d H:i:s");
                                                                            $this->db->insert($this->taskTimelineTable, $tempTaskTimeline);
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                $queryContract = $this->db->get_where($this->contractTable, array("id" => $tempId, "status" => 1));
                                                                if ($queryContract->num_rows() == 1) {
                                                                    $contRow = $queryContract->row();
                                                                    $tempTaskData = array();
                                                                    $tempTaskData["unit_id"] = $vxx;
                                                                    $tempTaskData["task_id"] = $kitem;
                                                                    $tempTaskData["contract_id"] = $contRow->id;
                                                                    $tempTaskData["issued_date"] = $contRow->issued_date;
                                                                    $tempTaskData["due_date"] = $contRow->due_date;
                                                                    $addedTask = $this->db->insert($this->taskTable, $tempTaskData);
                                                                    if ($addedTask) {
                                                                        $parentTaskId = $this->db->insert_id();
                                                                        $xtempWhereUnits = array();
                                                                        $xtempWhereUnits["wo_task_id"] = $woHistoryId;
                                                                        $xtempWhereUnits["contract_id"] = $tempId;
                                                                        $xtempWhereUnits["unit_id"] = $vxx;
                                                                        $xtempWhereUnits["item_id"] = $kitem;
                                                                        $xtempWhereUnits["task_id"] = $parentTaskId;

                                                                        $searchTempUnit = $this->db->get_where($this->contractUnitTable, $xtempWhereUnits);
                                                                        if ($searchTempUnit->num_rows() == 0) {
                                                                            $this->db->insert($this->contractUnitTable, $xtempWhereUnits);
                                                                        }

                                                                        if (isset($tempTaskItems[$kitem]) && $tempTaskItems[$kitem]) {
                                                                            foreach ($tempTaskItems[$kitem] as $kxaa => $vxaa) {
                                                                                $this->db->select("id");
                                                                                $_queryTempTaskChild = $this->db->get_where($this->taskTimelineTable, array("parent_id" => $parentTaskId, "task_id" => $vxaa));
                                                                                if ($_queryTempTaskChild->num_rows() == 0) {
                                                                                    $xtempTaskTimeline = array();
                                                                                    $xtempTaskTimeline["parent_id"] = $parentTaskId;
                                                                                    $xtempTaskTimeline["task_id"] = $vxaa;
                                                                                    $xtempTaskTimeline["contract_id"] = $tempId;
                                                                                    $xtempTaskTimeline["created_at"] = date("Y-m-d H:i:s");
                                                                                    $this->db->insert($this->taskTimelineTable, $xtempTaskTimeline);
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                }

                $updated = $this->db->update($this->contractTable, $post, $tempWhere);
                if ($updated && $this->db->affected_rows() > 0) {
                    $resultset["response"] = true;
                    $resultset["redirect"] = site_url("pms/contract/view_work_order/{$tempId}");
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getContractItemData($id = null, $wo_task_id = null) {
            $changeOrder = false;
            if ($id && $wo_task_id) {
                $userId = $this->core_layout->getCurrentEmployeeId();
                $currentLots = array();
                $taskCount = 0;

                $tempLots = array();
                $tempTaskCount = 0;
                $taskIds = array();
                $queryItems = $this->db->get_where($this->contractItemTable, array("contract_id" => $id, "wo_task_id" => $wo_task_id));
                if ($queryItems->num_rows() > 0) {
                    $taskCount = $queryItems->num_rows();
                    foreach ($queryItems->result() as $key => $item) {
                        $currentLots[$item->item_id] = array();
                        $taskIds[] = $item->item_id;
                        $itemLots = unserialize($item->lots);
                        foreach ($itemLots as $kk => $lot) {
                            if (!in_array($lot, $currentLots[$item->item_id])) {
                                $currentLots[$item->item_id][] = $lot;
                            }
                        }
                    }
                }

                $queryTempItems = $this->db->get_where($this->contractTempItemsTable, array("user_id" => $userId, "is_editable" => 1));
                if ($queryTempItems->num_rows() > 0) {
                    foreach ($queryTempItems->result() as $key => $item) {
                        if ($item->parent_id !== "0") {
                            $tempLots[$item->item_id] = array();
                            $itemLots = unserialize($item->lots);
                            foreach ($itemLots as $kk => $lot) {
                                if (!in_array($lot, $tempLots[$item->item_id])) {
                                    $tempLots[$item->item_id][] = $lot;
                                }
                            }
                        }
                        $tempTaskCount++;
                    }
                }

                if ($taskIds && count($taskIds) > 0) {
                    foreach ($taskIds as $kx => $vx) {
                        if (isset($currentLots[$vx], $tempLots[$vx])) {
                            $tempArr1 = $currentLots[$vx];
                            $tempArr2 = $tempLots[$vx];
                            if ($tempArr1 && $tempArr2 && (count($tempArr2) !== count($tempArr1))) {
                                $changeOrder = true;
                            }
                        }
                    }
                }

                if (count($tempLots) > 0 && count($tempLots) !== count($currentLots)) {
                    $changeOrder = true;
                }

                if ($tempTaskCount > 0 && $tempTaskCount !== $taskCount) {
                    $changeOrder = true;
                }
            }
            return $changeOrder;
        }

        function generateCoWorkCode($tempId = null) {
            $resultset = array();
            if ($tempId) {
                $this->db->select("a.id as contract_id, a.wo_code_id as wo_id, b.*");
                $this->db->from($this->contractTable . " a");
                $this->db->join($this->woCodeHistoryTable . " b", "b.id = a.wo_code_id");
                $this->db->where(array("a.id" => $tempId, "a.status" => 1));
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $tempRow = $query->row();
                    $contractId = $tempRow->contract_id;
                    $woId = $tempRow->wo_id;

                    unset($tempRow->id, $tempRow->created_at, $tempRow->contract_id, $tempRow->wo_id);
                    $tempWoCode = $tempRow->wo_code;
                    $woCode = explode("-", $tempRow->wo_code);
                    $lastItem = end($woCode);
                    $lastKey = key(array_slice($woCode, -1, 1, true));

                    if (is_numeric($lastItem)) {
                        $woCode[] = "A";
                        $tempWoCode = implode("-", $woCode);
                    } else {
                        $lastItem = ord($lastItem) + 1;
                        $charItem = chr($lastItem);
                        $woCode[$lastKey] = strtoupper($charItem);
                        $tempWoCode = implode("-", $woCode);
                    }

                    if ($tempRow->co_version) {
                        $_lastItem = ord($tempRow->co_version) + 1;
                        $_charItem = chr($_lastItem);
                        $tempRow->co_version = $_charItem;
                    } else {
                        $tempRow->co_version = strtoupper("A");
                    }

                    $tempRow->wo_code = $tempWoCode;
                    $woCodeAdded = $this->db->insert($this->woCodeHistoryTable, $tempRow);
                    if ($woCodeAdded) {
                        $tempData = array();
                        $tempData["wo_code_id"] = $this->db->insert_id();
                        $tempData["wo_code"] = $tempWoCode;

                        $resultset["response"] = true;
                        $resultset["data"] = $tempData;
                    } else {
                        $resultset["response"] = false;
                    }
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function checkTaskHistory($id = null, $type = "edit") {
            $resultset = array();
            if ($id) {
                $redirectTo = "edit_work_order";
                switch ($type) {
                    case "edit":
                        $redirectTo = "edit_work_order";
                        break;
                    case "additional":
                        $redirectTo = "additional_work_order";
                        break;
                    case "adjustment":
                        $redirectTo = "adjustment_work_order";
                        break;
                    default:
                        $redirectTo = "edit_work_order";
                        break;
                }

                if ($type == "additional") {
                    $resultset["response"] = true;
                    $resultset["redirect_url"] = site_url("pms/contract/{$redirectTo}/{$id}");
                } else {
                    $this->db->select("a.id, a.contract_id, b.wo_code");
                    $this->db->from($this->woTaskHistoryTable . " a");
                    $this->db->join($this->woCodeHistoryTable . " b", "b.id = a.wo_code_id");
                    $this->db->join($this->contractItemTable . " c", "c.wo_task_id = a.id AND c.contract_id = a.contract_id");
                    $this->db->where("a.contract_id", $id);
                    $this->db->where("a.is_active", 1);
                    $this->db->order_by("c.id", "ASC");
                    $this->db->group_by("a.id");
                    $query = $this->db->get();
                    if ($query->num_rows() > 0) {
                        if ($query->num_rows() == 1) {
                            $row = $query->row();
                            $resultset["redirect_url"] = site_url("pms/contract/{$redirectTo}/{$id}/{$row->id}");
                            $resultset["is_redirect"] = true;
                        } else {
                            $resultset["is_redirect"] = false;
                            $resultset["count"] = $query->num_rows();
                            $resultset["data"] = $query->result();
                        }
                        $resultset["response"] = true;
                    } else {
                        $resultset["response"] = false;
                    }
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function checkActiveWoTaskHistory($contract_id = null, $wo_task_id = null) {
            $response = false;
            if ($contract_id && $wo_task_id) {
                $where = array();
                $where["contract_id"] = $contract_id;
                $where["id"] = $wo_task_id;
                $where["is_active"] = 1;

                $query = $this->db->get_where($this->woTaskHistoryTable, $where);
                if ($query->num_rows() == 1) {
                    $response = true;
                    $userId = $this->core_layout->getCurrentEmployeeId();
                    $this->db->delete($this->contractTempItemsTable, array("user_id" => $userId, "is_editable" => 1));
                }
            }
            return $response;
        }

        function getLastActiveWoTaskHistoryId($contract_id = null) {
            $id = 0;
            if ($contract_id) {
                $where = array();
                $where["contract_id"] = $contract_id;
                $where["is_active"] = 1;
                $this->db->limit(1);
                $this->db->order_by("id", "DESC");
                $query = $this->db->get_where($this->woTaskHistoryTable, $where);
                if ($query->num_rows() == 1) {
                    $id = $query->row()->id;
                    $userId = $this->core_layout->getCurrentEmployeeId();
                    $this->db->delete($this->contractTempItemsTable, array("user_id" => $userId, "is_editable" => 1));
                }
            }

            return $id;
        }

        function update_contract_temp_qty_unit_cost() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $updateCount = 0;
                if (isset($post["qty"]) && $post["qty"]) {
                    foreach ($post["qty"] as $key => $value) {
                        $tempData = array();
                        if (isset($post["unit_cost"][$key]) && $post["unit_cost"][$key] && $value) {
                            $tempData["unit_cost"] = str_replace(",", "", $post["unit_cost"][$key]);
                            $tempData["qty"] = str_replace(",", "", $value);
                        } else {
                            $tempData["qty"] = str_replace(",", "", $value);
                        }
                        $updated = $this->db->update($this->contractTempItemsTable, $tempData, array("id" => $key));
                        if ($updated && $this->db->affected_rows() > 0) {
                            $updateCount++;
                        }
                    }
                }
                if ($updateCount) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Scope of work quantity/unit cost has been updated.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Error updating scope of work quantity/unit cost!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }
    }