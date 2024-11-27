<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Task_m extends CI_Model {
        protected $itemTable = "gccpms.sf_item";
        protected $checklistItemTable = "gccpms.sf_checklist_item";
        protected $checklistPrivilegeTable = "gccpms.sf_checklist_privilege";
        protected $checklistItemQtyTable = "gccpms.sf_checklist_item_qty";
        protected $checklistTempQtyTable = "gccpms.sf_checklist_temp_qty";
        protected $checklistItemQtyHistoryTable = "gccpms.sf_checklist_iq_history";
        protected $checklistApprovedQtyTable = "gccpms.sf_checklist_approved_qty";
        protected $projectUnitTable = "gccpms.sf_project_unit";
        protected $formTable = "gccpms.sf_form";
        protected $formMetaTable = "gccpms.sf_form_meta";
        protected $formLogTable = "gccpms.sf_form_log";
        protected $taskTable = "gccpms.sf_task";
        protected $projectTable = "gccpms.sf_project";
        protected $projectCompanyTable = "gccpms.sf_project_company";
        protected $projectLocationTable = "gccpms.sf_project_location";
        protected $woTypeTable = "gccpms.sf_wo_type";
        protected $contractTable = "gccpms.sf_contract";
        protected $contractUnitTable = "gccpms.sf_contract_units";
        protected $contractItemTable = "gccpms.sf_contract_items";
        protected $contractExtensionTable = "gccpms.sf_contract_extension";
        protected $woCodeTempTable = "gccpms.sf_wo_code_temp";
        protected $taskTimelineTable = "gccpms.sf_task_timeline";
        protected $taskTimelineActivityTable = "gccpms.sf_task_timeline_activity";
        protected $punchlistedTaskTable = "gccpms.sf_punchlisted_task";
        protected $punchlistLogTable = "gccpms.sf_punchlist_logs";

        protected $itemRatesTable = "gccpms.sf_item_rates";
        protected $uomTable = "gccmaster.uom";
        protected $checklistRatesTable = "gccpms.sf_checklist_privilege_rates";

        protected $contractorTable = "gcchris.tblcontractor";
        protected $employeeTable = "gccmaster.tblemployees";

        private $tempConfig = array();
        private $privileges = array();

        public function __construct() {
            parent::__construct();
            $this->user_data = $this->session->userdata("logged_in");
            $this->privileges = $this->core_layout->getCurrentActions();
            date_default_timezone_set("Asia/Manila");
        }

        function doPostEvent($function = null) {
            if (!$function) return false;
            return $this->$function();
        }

        function insert_temp_requested_qty() {
            $resultset = array();
            $post = $this->input->post();
            $session = $this->core_layout->getCurrentSession();
            $this->core_layout->setPrivilegeName("pms_task_checklist_items");
            $this->privileges = $this->core_layout->getCurrentActions();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $post["user_id"] = $session["emp_id"];
                $inserted = $this->db->insert($this->checklistTempQtyTable, $post);
                if ($inserted) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Requested task has been added.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed adding requested task!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }
            return $resultset;
        }

        function set_checklist_item_qty() {
            $resultset = array();
            $post = $this->input->post();
            $session = $this->core_layout->getCurrentSession();
            $this->core_layout->setPrivilegeName("pms_task_checklist_items");
            $this->privileges = $this->core_layout->getCurrentActions();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $post["created_by"] = $session["emp_id"];
                $post["created_at"] = date("Y-m-d H:i:s");

                if (in_array("approve_action", $this->privileges)) {
                    $post["approval_by"] = $session["emp_id"];
                    $post["approval_date"] = date("Y-m-d H:i:s");
                    $post["approval_remarks"] = "Approved";
                    $post["approval_status"] = 1;
                }

                $itemQty = $this->db->insert($this->checklistItemQtyTable, $post);
                if ($itemQty) {
                    $parentId = $this->db->insert_id();
                    $post["parent_id"] = $parentId;
                    $isUpdated = $this->db->insert($this->checklistItemQtyHistoryTable, $post);
                    if ($isUpdated && in_array("approve_action", $this->privileges)) {
                        $this->setApprovedChecklistItemQty($parentId);
                    }
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Task quantity has been added.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to add task quantity!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function set_approval_checklist_item_qty() {
            $resultset = array();
            $post = $this->input->post();
            $session = $this->core_layout->getCurrentSession();
            $this->core_layout->setPrivilegeName("pms_task_checklist_items");
            $this->privileges = $this->core_layout->getCurrentActions();
            if (isset($post) && $post) {
                $parentId = $post["id"];
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $post["approval_by"] = $session["emp_id"];
                $post["approval_date"] = date("Y-m-d H:i:s");
                $tempApprovalCaption = (isset($post["approval_status"]) && $post["approval_status"] == 1) ? "approved" : "declined";
                $updateApproval = $this->db->update($this->checklistItemQtyTable, $post, $where);
                if ($updateApproval && $this->db->affected_rows() > 0) {
                    $tempWhere = array("parent_id" => $parentId, "approval_status" => 0);
                    $updateHistory = $this->db->update($this->checklistItemQtyHistoryTable, $post, $tempWhere);
                    if ($updateHistory && $this->db->affected_rows() > 0 && in_array("approve_action", $this->privileges)) {
                        $this->setApprovedChecklistItemQty($parentId);
                    }
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Checklist item quantity has been {$tempApprovalCaption}";
                    $resultset["state"] = (isset($post["approval_status"]) && $post["approval_status"] == 1) ? "success" : "warning";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update approval status!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }
            return $resultset;
        }

        function update_checklist_item_qty() {
            $resultset = array();
            $post = $this->input->post();
            $session = $this->core_layout->getCurrentSession();
            $this->core_layout->setPrivilegeName("pms_task_checklist_items");
            $this->privileges = $this->core_layout->getCurrentActions();
            if (isset($post) && $post) {
                $parentId = $post["id"];
                $where = array("id" => $post["id"]);
                $tempQtyItems = $this->db->get_where($this->checklistItemQtyTable, $where);
                if ($tempQtyItems->num_rows() == 1) {
                    $tempRowx = $tempQtyItems->row();
                    unset($post["csrf_token"], $post["id"]);
                    $post["created_by"] = $session["emp_id"];
                    $post["created_at"] = date("Y-m-d H:i:s");
                    $post["qty"] = str_replace(",", "", $post["qty"]);

                    if (in_array("approve_action", $this->privileges)) {
                        $post["approval_by"] = $session["emp_id"];
                        $post["approval_date"] = date("Y-m-d H:i:s");
                        $post["approval_remarks"] = "Approved";
                        $post["approval_status"] = 1;
                    } else {
                        $post["approval_status"] = 0;
                        $post["approval_date"] = null;
                        $post["approval_remarks"] = "";
                    }

                    $itemQty = $this->db->update($this->checklistItemQtyTable, $post, $where);
                    if ($itemQty) {
                        $tempWhere = array();
                        $tempWhere["parent_id"] = $parentId;
                        $tempWhere["created_by"] = $session["emp_id"];
                        $tempWhere["approval_status"] = 0;
                        $queryWhereItem = $this->db->get_where($this->checklistItemQtyHistoryTable, $tempWhere);
                        if ($queryWhereItem->num_rows() == 1) {
                            $row = $queryWhereItem->row();
                            $whereHistory = array();
                            $whereHistory["parent_id"] = $parentId;
                            $whereHistory["created_by"] = $row->created_by;
                            $whereHistory["approval_status"] = 0;
                            $isUpdated = $this->db->update($this->checklistItemQtyHistoryTable, $post, $whereHistory);
                            if ($isUpdated && $this->db->affected_rows() > 0 && in_array("approve_action", $this->privileges)) {
                                $this->setApprovedChecklistItemQty($parentId);
                            }
                        } else {
                            $tempwhereHistory = array();
                            $tempwhereHistory["parent_id"] = $parentId;
                            $tempwhereHistory["approval_status"] = 0;
                            $tempData = array("approval_status" => 2, "approval_by" => $session["emp_id"], "approval_date" => date("Y-m-d H:i:s"));

                            $tempUpdated = $this->db->update($this->checklistItemQtyHistoryTable, $tempData, $tempwhereHistory);
                            if ($tempUpdated) {
                                $post["parent_id"] = $parentId;
                                $post["checklist_id"] = $tempRowx->checklist_id;
                                $post["item_id"] = $tempRowx->item_id;
                                $this->db->insert($this->checklistItemQtyHistoryTable, $post);
                                if (in_array("approve_action", $this->privileges)) {
                                    $this->setApprovedChecklistItemQty($parentId);
                                }
                            }
                        }
                        $resultset["response"] = true;
                        $resultset["toastr_msg"] = "Task quantity has been updated.";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Failed to update task quantity!";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "No data found!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function setApprovedChecklistItemQty($id = null) {
            $response = false;
            $this->db->select("id as parent_id, checklist_id, item_id, qty, approval_by as applied_by, approval_date as applied_date");
            $queryItemQty = $this->db->get_where($this->checklistItemQtyTable, array("id" => $id, "approval_status" => 1));
            if ($queryItemQty->num_rows() == 1) {
                $tempItemQty = $queryItemQty->row();
                $this->db->select("id");
                $hasApprovedQty = $this->db->get_where($this->checklistApprovedQtyTable, array("parent_id" => $id, "checklist_id" => $tempItemQty->checklist_id, "item_id" => $tempItemQty->item_id));
                if ($hasApprovedQty->num_rows() == 0) {
                    $isAdded = $this->db->insert($this->checklistApprovedQtyTable, $tempItemQty);
                    if ($isAdded) {
                        $response = true;
                    }
                } else {
                    $approvedWhere = array();
                    $approvedWhere["id"] = $hasApprovedQty->row()->id;
                    $isUpdate = $this->db->update($this->checklistApprovedQtyTable, $tempItemQty, $approvedWhere);
                    if ($isUpdate && $this->db->affected_rows() == 1) {
                        $response = true;
                    }
                }
            }
            return $response;
        }

        function get_current_activities() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                unset($post["csrf_token"]);
                $this->db->select("g.label as task, a.description, f.contractor, e.lastname, e.firstname, e.middlename, e.suffix, DATE_FORMAT(a.created_at, '%M %e, %Y %h:%i %p') as created_at");
                $this->db->from($this->taskTimelineActivityTable . " a");
                $this->db->join($this->taskTimelineTable . " b", "b.id = a.timeline_id");
                $this->db->join($this->contractTable . " c", "c.id = b.contract_id");
                $this->db->join($this->taskTable . " d", "d.id = b.parent_id");
                $this->db->join($this->employeeTable . " e", "e.id = a.user_id");
                $this->db->join($this->contractorTable . " f", "f.id = c.contractor_id");
                $this->db->join($this->itemTable . " g", "g.id = b.task_id");
                $this->db->where("d.unit_id", $post["unit_id"]);
                $this->db->where("a.is_active", 1);
                $this->db->order_by("a.created_at", "DESC");
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $arrData = array();
                    foreach ($query->result() as $index => $rs) {
                        $tempRs = (array)$rs;
                        $tempName = $this->core_layout->getDisplayName($tempRs);
                        $tempName = (object)$tempName;
                        $rs->user_name = (isset($tempName->display_name_1) && $tempName->display_name_1) ? $tempName->display_name_1 : "No assigned name";
                        $arrData[$index] = $rs;
                    }
                    $resultset["response"] = true;
                    $resultset["rows"] = $arrData;
                    $resultset["row_count"] = $query->num_rows();
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function get_current_contractor() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                unset($post["csrf_token"]);
                $this->db->select("d.id, UPPER(d.contractor) as contractor, b.contract_status as status");
                $this->db->from($this->contractTable . " a");
                $this->db->join($this->contractUnitTable . " b", "b.contract_id = a.id");
                $this->db->join($this->taskTable . " c", "c.id = b.task_id");
                $this->db->join($this->contractorTable . " d", "d.id = a.contractor_id");
                $this->db->where("c.unit_id", $post["unit_id"]);
                $this->db->group_by("a.contractor_id");
                $this->db->order_by("d.contractor", "ASC");
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $resultset["response"] = true;
                    $resultset["rows"] = $query->result();
                    $resultset["row_count"] = $query->num_rows();
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getSequenceItemDatatableRequest() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("a.name", "a.label", "b.label as parent_name",
                    "a.wo_code", "c.label as wo_type", "a.sort", "a.is_active", "a.id", "a.wo_type_id");
                $dir = "DESC";
                $order = "id";
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
                $joinTable["table"][$this->itemTable] = "b";
                $joinTable["table"][$this->woTypeTable] = "c";
                $joinTable["fields"][] = "b.id=a.parent_id";
                $joinTable["fields"][] = "c.id=a.wo_type_id";
                $joinTable["field_loc"][] = "LEFT";
                $joinTable["field_loc"][] = "LEFT";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.status"] = 1;

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
                        $nestedData['id'] = $pst->id;
                        $nestedData['name'] = $pst->name;
                        $nestedData['label'] = $pst->label;
                        $nestedData['parent_name'] = $pst->parent_name;
                        $nestedData['wo_code'] = $pst->wo_code;
                        $nestedData['wo_type'] = $pst->wo_type;
                        $nestedData['sort'] = $pst->sort;
                        $nestedData['is_active'] = $pst->is_active;
                        $nestedData['wo_type_id'] = $pst->wo_type_id;
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

        function get_checklist_qty_history_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("c.label as parent_task", "b.label as task", "a.qty", "a.id",
                    "a.created_by", "a.created_at", "a.remarks", "a.approval_by", "a.approval_date", "a.approval_remarks", "a.approval_status");
                $dir = "DESC";
                $order = "id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $tempTable = $this->dt_model->dataTable();
                $tempTable->setTable($this->checklistItemQtyHistoryTable);
                $tempTable->setTableAlias("a");

                $tempTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->itemTable] = "b";
                $joinTable["joint_table"][] = "{$this->itemTable} c";
                $joinTable["fields"][] = "b.id=a.item_id";
                $joinTable["fields"][] = "c.id=b.parent_id";
                $joinTable["field_loc"][] = "";
                $joinTable["field_loc"][] = "";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.checklist_id"] = intval($post["checklist_id"]);
                if (isset($post["item_id"]) && $post["item_id"]) {
                    $parameters["a.parent_id"] = intval($post["item_id"]);
                }
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
                    foreach ($posts as $key => $pst) {
                        $tempEncodedBy = "";
                        $tempApprovalBy = "";
                        $tempRs = $pst;
                        if ($tempRs->created_by) {
                            $tempName0 = $this->core_layout->getEmployeeData($tempRs->created_by);
                            $tempName0 = (object)$tempName0;
                            $tempEncodedBy = (isset($tempName0->display_name_1) && $tempName0->display_name_1) ? $tempName0->display_name_1 : "No assigned name";
                        }
                        if ($tempRs->approval_by) {
                            $tempName1 = $this->core_layout->getEmployeeData($tempRs->approval_by);
                            $tempName1 = (object)$tempName1;
                            $tempApprovalBy = (isset($tempName1->display_name_1) && $tempName1->display_name_1) ? $tempName1->display_name_1 : "No assigned name";
                        }
                        $nestedData = $pst;
                        $nestedData->checklist_id = intval($post["checklist_id"]);
                        $nestedData->encoded_by = $tempEncodedBy;
                        $nestedData->approval_by = $tempApprovalBy;
                        $data[] = $nestedData;
                    }
                }

                $json_data = array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                    "posts" => $posts,
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

        function get_checklist_requested_qty_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("b.label as parent_task", "a.label as task", "c.qty", "a.id", "c.id as qty_id", "c.checklist_id",
                    "c.created_by", "c.created_at", "c.remarks", "c.approval_by", "c.approval_date", "c.approval_remarks", "c.approval_status");
                $dir = "ASC";
                $order = "id";
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
                $joinTable["table"][$this->itemTable] = "b";
                $joinTable["table"][$this->checklistItemQtyTable] = "c";
                $joinTable["fields"][] = "b.id=a.parent_id";
                $joinTable["fields"][] = "c.item_id=a.id";
                $joinTable["field_loc"][] = "";
                $joinTable["field_loc"][] = "LEFT";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.status"] = 1;
                $parameters["a.parent_id !="] = 0;
                $parameters["c.checklist_id"] = $post["checklist_id"];

                $tempTable->setWhereParameters($parameters);
                $checklistPrivilege = $this->db->get_where($this->checklistPrivilegeTable, array("checklist_id" => $post["checklist_id"]));
                $tempResource = array();
                if ($checklistPrivilege->num_rows() == 1) {
                    $tempRow = $checklistPrivilege->row();
                    $tempResource = unserialize($tempRow->checklist_resource);
                }
                $tempTable->setWhereInParameters("a.id", $tempResource);

                $totalData = $tempTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $tempTable->dtPostSearchCount($searchValue);
                }

                $count = $this->getRequestItemQtyCount($post["checklist_id"]);
                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $key => $pst) {
                        $tempEncodedBy = "";
                        $tempApprovalBy = "";
                        $tempRs = $pst;
                        if ($tempRs->created_by) {
                            $tempName0 = $this->core_layout->getEmployeeData($tempRs->created_by);
                            $tempName0 = (object)$tempName0;
                            $tempEncodedBy = (isset($tempName0->display_name_1) && $tempName0->display_name_1) ? $tempName0->display_name_1 : "No assigned name";
                        }
                        if ($tempRs->approval_by) {
                            $tempName1 = $this->core_layout->getEmployeeData($tempRs->approval_by);
                            $tempName1 = (object)$tempName1;
                            $tempApprovalBy = (isset($tempName1->display_name_1) && $tempName1->display_name_1) ? $tempName1->display_name_1 : "No assigned name";
                        }
                        $nestedData = $pst;
                        $nestedData->checklist_id = intval($post["checklist_id"]);
                        $nestedData->encoded_by = $tempEncodedBy;
                        $nestedData->approval_by = $tempApprovalBy;

                        $data[] = $nestedData;
                    }
                }

                $json_data = array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                    "posts" => $posts,
                    "count" => $count
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

        function get_checklist_item_qty_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("b.label as parent_task", "a.label as task", "c.qty", "a.id", "c.id as qty_id",
                    "d.lastname", "d.firstname", "d.middlename", "d.suffix", "c.applied_by", "c.applied_date");
                $dir = "ASC";
                $order = "id";
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
                $joinTable["table"][$this->itemTable] = "b";
                $joinTable["table"][$this->checklistApprovedQtyTable] = "c";
                $joinTable["table"][$this->employeeTable] = "d";
                $joinTable["fields"][] = "b.id=a.parent_id";
                $joinTable["fields"][] = "c.item_id=a.id";
                $joinTable["fields"][] = "d.id = c.applied_by";
                $joinTable["field_loc"][] = "";
                $joinTable["field_loc"][] = "LEFT";
                $joinTable["field_loc"][] = "LEFT";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.status"] = 1;
                $parameters["a.parent_id !="] = 0;
                $parameters["c.checklist_id"] = $post["checklist_id"];

                $tempTable->setWhereParameters($parameters);
                $checklistPrivilege = $this->db->get_where($this->checklistPrivilegeTable, array("checklist_id" => $post["checklist_id"]));
                $tempResource = array();
                if ($checklistPrivilege->num_rows() == 1) {
                    $tempRow = $checklistPrivilege->row();
                    $tempResource = unserialize($tempRow->checklist_resource);
                }
                $tempTable->setWhereInParameters("a.id", $tempResource);

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
                    foreach ($posts as $key => $pst) {
                        $nestedData = $pst;
                        $appliedBy = "";
                        if ($pst->applied_by) {
                            $tempName = $this->core_layout->getEmployeeData($pst->applied_by);
                            $tempName = (object)$tempName;
                            $appliedBy = (isset($tempName->display_name_1) && $tempName->display_name_1) ? $tempName->display_name_1 : "No assigned name";
                        }
                        $nestedData->applied_by = $appliedBy;
                        $nestedData->checklist_id = intval($post["checklist_id"]);
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

        function get_checklist_temp_qty_datatable_request() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("b.label as task_name", "a.qty", "a.id");
                $dir = "DESC";
                $order = "id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $tempTable = $this->dt_model->dataTable();
                $tempTable->setTable($this->checklistTempQtyTable);
                $tempTable->setTableAlias("a");

                $tempTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->itemTable] = "b";
                $joinTable["fields"][] = "b.id=a.item_id";
                $joinTable["field_loc"][] = "";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.user_id"] = $this->core_layout->getCurrentEmployeeId();
                $parameters["a.checklist_id"] = $post["checklist_id"];

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
                    $data = $posts;
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

        function getChecklistItemDatatableRequest() {
            $post = $this->input->post();
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("a.name", "a.label", "a.is_active", "a.id", "CONCAT(c.description, ' | ', d.description) development_site");
                $dir = "DESC";
                $order = "id";
                if ($orderx) {
                    $dir = $orderx[0]["dir"];
                    $order = $columns[$orderx[0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $tempTable = $this->dt_model->dataTable();
                $tempTable->setTable($this->checklistItemTable);
                $tempTable->setTableAlias("a");

                $tempTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->projectTable] = "b";
                $joinTable["fields"][] = "b.id=a.project_id";
                $joinTable["field_loc"][] = "LEFT";

                $joinTable["table"][$this->projectCompanyTable] = "c";
                $joinTable["fields"][] = "c.id=b.company_id";
                $joinTable["field_loc"][] = "LEFT";

                $joinTable["table"][$this->projectLocationTable] = "d";
                $joinTable["fields"][] = "d.id=b.location_id";
                $joinTable["field_loc"][] = "LEFT";

                $tempTable->setJoinTable($joinTable);
                $tempTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.status"] = 1;

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
                    $data = $posts;
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

        function update_temp_requested_qty() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post) && $post) {
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $post["qty"] = str_replace(",", "", $post["qty"]);

                $updated = $this->db->update($this->checklistTempQtyTable, $post, $where);
                if ($updated && $this->db->affected_rows() > 0) {
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function clearAllTempRequestedQty($id = null) {
            $resultset = array();
            if ($id) {
                $session = $this->core_layout->getCurrentSession();
                $deleted = $this->db->delete($this->checklistTempQtyTable, array("checklist_id" => $id, "user_id" => $session["emp_id"]));
                if ($deleted) {
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function submitTempRequestedQty($id = null) {
            $resultset = array();
            if ($id) {
                $session = $this->core_layout->getCurrentSession();
                $this->privileges = $this->core_layout->getCurrentActions();
                $queryTemp = $this->db->get_where($this->checklistTempQtyTable, array("checklist_id" => $id, "user_id" => $session["emp_id"]));
                if ($queryTemp->num_rows() > 0) {
                    $ctr = 0;
                    foreach ($queryTemp->result() as $key => $value) {
                        $tempItems = $value;
                        $tempItems->created_by = $tempItems->user_id;
                        $tempItems->created_at = date("Y-m-d H:i:s");

                        if (in_array("approve_action", $this->privileges)) {
                            $tempItems->approval_by = $tempItems->user_id;
                            $tempItems->approval_date = date("Y-m-d H:i:s");
                            $tempItems->approval_remarks = "Approved";
                            $tempItems->approval_status = 1;
                        }
                        unset($tempItems->id, $tempItems->user_id);
                        $queryItems = $this->db->get_where($this->checklistItemQtyTable, array("checklist_id" => $value->checklist_id, "item_id" => $value->item_id));
                        if ($queryItems->num_rows() == 0) {
                            $inserted = $this->db->insert($this->checklistItemQtyTable, $tempItems);
                            if ($inserted) {
                                $parentId = $this->db->insert_id();
                                $tempItems->parent_id = $parentId;
                                $isUpdated = $this->db->insert($this->checklistItemQtyHistoryTable, $tempItems);
                                if ($isUpdated && in_array("approve_action", $this->privileges)) {
                                    $this->setApprovedChecklistItemQty($parentId);
                                }
                                $ctr++;
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
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function setAllTempRequestedQty($id = null) {
            $resultset = array();
            if ($id) {
                $privilegeItems = $this->db->get_where($this->checklistPrivilegeTable, array("checklist_id" => $id));
                if ($privilegeItems->num_rows() == 1) {
                    $userId = $this->core_layout->getCurrentEmployeeId();
                    $row = $privilegeItems->row();
                    $resourceIds = unserialize($row->checklist_resource);
                    $this->db->select("id, label as text");
                    $this->db->from($this->itemTable);
                    $this->db->where("is_active", 1);
                    $this->db->where("status", 1);
                    $this->db->where("parent_id !=", 0);
                    $this->db->where_in("id", $resourceIds);
                    $queryItems = $this->db->get();
                    if ($queryItems->num_rows() > 0) {
                        $arrData = array();
                        foreach ($queryItems->result() as $key => $value) {
                            $where = array();
                            $where["user_id"] = $userId;
                            $where["checklist_id"] = $id;
                            $where["item_id"] = $value->id;

                            $where2 = array();
                            $where2["checklist_id"] = $id;
                            $where2["item_id"] = $value->id;

                            $queryTemp = $this->db->get_where($this->checklistTempQtyTable, $where);
                            $queryCurrentItem = $this->db->get_where($this->checklistItemQtyTable, $where2);
                            if ($queryTemp->num_rows() == 0 && $queryCurrentItem->num_rows() == 0) {
                                $arrData[] = $where;
                            }
                        }

                        if ($arrData && count($arrData) > 0) {
                            $inserted = $this->db->insert_batch($this->checklistTempQtyTable, $arrData);
                            if ($inserted) {
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
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getRequestItemQtyCount($id = null) {
            $count = 0;
            if ($id) {
                $privilegeItems = $this->db->get_where($this->checklistPrivilegeTable, array("checklist_id" => $id));
                if ($privilegeItems->num_rows() == 1) {
                    $userId = $this->core_layout->getCurrentEmployeeId();
                    $row = $privilegeItems->row();
                    $resourceIds = unserialize($row->checklist_resource);
                    $this->db->select("id, label as text");
                    $this->db->from($this->itemTable);
                    $this->db->where("is_active", 1);
                    $this->db->where("status", 1);
                    $this->db->where("parent_id !=", 0);
                    $this->db->where_in("id", $resourceIds);
                    $queryItems = $this->db->get();
                    if ($queryItems->num_rows() > 0) {
                        $arrData = array();
                        foreach ($queryItems->result() as $key => $value) {
                            $where = array();
                            $where["checklist_id"] = $id;
                            $where["item_id"] = $value->id;
                            $queryTemp = $this->db->get_where($this->checklistItemQtyTable, $where);
                            if ($queryTemp->num_rows() == 0) {
                                $count++;
                            }
                        }
                    }
                }
            }
            return $count;
        }

        function remove_temp_requested_qty() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);

                $removed = $this->db->delete($this->checklistTempQtyTable, $post);
                if ($removed) {
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function update_modal_subtask() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                $tempId = $post["id"];
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $update = $this->db->update($this->taskTimelineTable, $post, $where);
                $affectedRows = $this->db->affected_rows();
                if ($update && count($affectedRows) > 0) {
                    $this->db->select("c.task_status, c.id, a.task_id");
                    $this->db->from($this->taskTimelineTable . " a");
                    $this->db->join($this->taskTable . " b", "b.id = a.parent_id");
                    $this->db->join($this->contractUnitTable . " c", "c.unit_id = b.unit_id AND c.item_id = b.task_id AND c.task_id = b.id AND c.contract_id = a.contract_id");
                    $this->db->where("a.id", $tempId);
                    $this->db->group_by("a.id");
                    $subTask = $this->db->get();
                    if ($subTask->num_rows() == 1) {
                        $row = $subTask->row();
                        $tempStatus = unserialize($row->task_status);
                        $tempStatus[$row->task_id] = $post["task_status"];
                        $tempstatus = serialize($tempStatus);

                        $tempWhere = array("id" => $row->id);
                        $this->db->update($this->contractUnitTable, array("task_status" => $tempstatus), $tempWhere);
                    }
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Sub task data has been updated.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update sub task data!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function updateTaskStatus() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $update = $this->db->update($this->taskTable, $post, $where);
                if ($update) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Task status has been updated.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update task status!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function updateTaskRemarks() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $insert = $this->db->update($this->taskTable, $post, $where);
                if ($insert) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Task remarks has been updated.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update task remarks!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function set_modal_punchlist() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();
            $post = $this->input->post();
            if (isset($post) && $post) {
                $selectedItems = explode(",", $post["selected_items"]);
                unset($post["csrf_token"], $post["selected_items"]);
                if ($selectedItems && count($selectedItems) > 0) {
                    $post["selected_items"] = serialize($selectedItems);
                    $post["user_id"] = $session["emp_id"];
                    $addedLogs = $this->db->insert($this->punchlistLogTable, $post);
                    if ($addedLogs) {
                        foreach ($selectedItems as $key => $value) {
                            $itemExist = $this->db->get_where($this->punchlistedTaskTable, array("unit_id" => $post["unit_id"], "item_id" => $value));
                            if ($itemExist->num_rows() == 1) {
                                $tempRow = $itemExist->row();
                                $this->db->update($this->punchlistedTaskTable, array("status" => $post["status"]), array("id" => $tempRow->id));
                            } else {
                                $tempData = array();
                                $tempData["unit_id"] = $post["unit_id"];
                                $tempData["item_id"] = $value;
                                $tempData["status"] = $post["status"];
                                $this->db->insert($this->punchlistedTaskTable, $tempData);
                            }
                        }
                        $resultset["response"] = true;
                        $resultset["toastr_msg"] = "Punchlist task item(s) has been updated.";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "failed to update punchlist task!";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "No punchlist task item(s) found!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No data found!";
            }

            return $resultset;
        }

        function setModalSubtaskActivity() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $post["user_id"] = $session["emp_id"];
                $insert = $this->db->insert($this->taskTimelineActivityTable, $post);
                if ($insert) {
                    $tempId = $this->getTaskIdByTimelineId($post["timeline_id"]);
                    $tempActivity = $this->generateSubtaskActivity($post["timeline_id"]);
                    $resultset["response"] = true;
                    $resultset["task_id"] = ($tempId) ? intval($tempId) : 0;
                    $resultset["rows"] = $tempActivity;
                    $resultset["row_count"] = count($tempActivity);
                    $resultset["toastr_msg"] = "Task activity has been added.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed saving task activity data!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function getTaskIdByTimelineId($id = null) {
            if ($id) {
                $this->db->select("b.id");
                $this->db->from($this->taskTimelineTable . " a");
                $this->db->join($this->taskTable . " b", "b.id = a.parent_id");
                $this->db->where("a.id", $id);
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    return $query->row()->id;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function set_modal_task_duration() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                $where = array("id" => $post["id"]);
                unset($post["csrf_token"], $post["id"]);
                $update = $this->db->update($this->taskTable, $post, $where);
                if ($update) {
                    $tempQuery = $this->db->get_where($this->taskTable, array("id" => $where["id"]));
                    $row = $tempQuery->row();
                    $resultset["response"] = true;
                    $resultset["unit_id"] = $row->unit_id;
                    $resultset["item_id"] = $row->task_id;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function setModalTaskItem() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $post["created_at"] = date("Y-m-d H:i:s");
                $insert = $this->db->insert($this->taskTimelineTable, $post);
                if ($insert) {
                    $taskData = $this->getCurrentTaskData($post["parent_id"]);
                    $resultset["response"] = true;
                    $resultset["task"] = (isset($taskData["response"]) && $taskData["response"]) ? $taskData["task"] : array();
                    $resultset["task_count"] = (isset($taskData["response"]) && $taskData["response"]) ? $taskData["task_count"] : 0;
                    $resultset["toastr_msg"] = "Task item has been added.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed saving task item data!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function setModalSequenceItem() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $post["name"] = strtolower($post["name"]);
                $post["label"] = strtoupper($post["label"]);
                $post["wo_code"] = strtoupper($post["wo_code"]);
                $allow = $this->checkSequenceItemCode($post);
                if ($allow) {
                    $insert = $this->db->insert($this->itemTable, $post);
                    if ($insert) {
                        $resultset["response"] = true;
                        $resultset["toastr_msg"] = "Sequence item has been added.";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Failed saving sequence item data!";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Sequence item name already exist!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function setModalChecklistItem() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            $post = $this->input->post();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $post["name"] = strtolower($post["name"]);
                $post["label"] = strtoupper($post["label"]);
                $allow = $this->checkChecklistItemCode($post);
                if ($allow) {
                    $insert = $this->db->insert($this->checklistItemTable, $post);
                    if ($insert) {
                        $resultset["response"] = true;
                        $resultset["toastr_msg"] = "Checklist item has been added.";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Failed saving checklist item data!";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Checklist item name already exist!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function listSequenceItem() {
            $get = $this->input->get();
            $search = (isset($get["search"]) && $get["search"]) ? $get["search"] : "";
            $arrData = $this->sequenceItemJsonTree($search);
            $html = $this->load->view("pms/task/modal_content/list", null, true);

            $result = array();
            $result["response"] = true;
            $result["data"] = $arrData;
            $result["html"] = $html;

            return $result;
        }

        function sequenceItemJsonTree($search) {
            $arrData = array();
            if ($search) {
                $this->db->where("is_active", 1);
                $this->db->group_start();
                $this->db->like("name", $search, "both");
                $this->db->or_like("label", $search, "both");
                $this->db->group_end();
                $this->db->order_by("sort", "ASC");

                $query = $this->db->get($this->itemTable);

                foreach ($query->result() as $rs) {
                    $child = array();
                    $child["id"] = $rs->id;
                    $child["type"] = ($rs->parent_id) ? "child" : "root";
                    $child["parent"] = ($rs->parent_id) ? $rs->parent_id : "#";
                    $child["text"] = "<span class='m--font-boldest'>" . $rs->label . "</span>";
                    /** $child["text"] = "<span class='m--font-boldest'>" . $rs->label . "</span>" . " <span class='m--font-boldest text-muted'>(" . $rs->name . ")</span>"; **/
                    array_push($arrData, $child);
                }

                foreach ($arrData as $item) {
                    $flag = $this->checkParentExist($item["parent"]);
                    if ($flag) {
                        $parent = json_decode($this->getSequenceParent($item["parent"]), true);
                        $p_id = (int)$parent["id"];

                        $filter = array_filter($arrData, function ($value) use ($p_id) {
                            return (int)$value["id"] === $p_id;
                        });

                        if (count($filter) <= 0) {
                            array_push($arrData, $parent);
                        }
                    }
                }
            } else {
                $this->db->where("is_active", 1);
                $this->db->order_by("sort", "ASC");
                $query = $this->db->get($this->itemTable);

                if ($query->num_rows() > 0) {
                    foreach ($query->result_array() as $rs) {
                        $flag = false;
                        $data = array();
                        $data["id"] = $rs["id"];
                        $data["type"] = ($rs["parent_id"]) ? "child" : "root";
                        $data["parent"] = ($rs["parent_id"]) ? $rs["parent_id"] : "#";
                        $data["text"] = "<span class='m--font-boldest'>" . $rs["label"] . "</span>";

                        $parentId = intval($rs["parent_id"]);
                        if ($parentId !== 0) {
                            $flag = $this->checkParentExist($parentId);
                        } else {
                            $flag = true;
                        }
                        if ($flag) {
                            $arrData[] = $data;
                        }
                    }
                }
            }

            return $arrData;
        }

        private function getSequenceParent($id) {
            $this->db->select("id, 'root' `type`, '#' parent, label text");
            $this->db->where("id", $id);
            $data = $this->db->get($this->itemTable)->row();
            return json_encode($data);
        }

        function getJsonSequenceItems() {
            $post = $this->input->post();
            $result = array();
            if (isset($post["nodes"]) && $post["nodes"]) {
                $jsonData = json_decode($post["nodes"], true);
                $arrData = array();

                $sort = array();
                $sortParent = 1;
                $sortChild = 1;

                foreach ($jsonData as $key => $value) {
                    $where = array();
                    $where["id"] = intval($value["id"]);

                    $data = array();
                    $data["parent_id"] = intval($value["parent"]);
                    $data["sort"] = (intval($value["parent"]) == 0) ? intval($sortParent) : intval($sortChild);
                    $this->db->update($this->itemTable, $data, $where);

                    if (intval($value["parent"]) == 0) {
                        $sortChild = 1;
                        $sortParent++;
                    } else {
                        $sortChild++;
                    }
                }
                $result["response"] = true;
                $result["toastr_msg"] = "Sequence item list update successful.";
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "Error, no data found!";
            }

            return $result;
        }

        public function getAssignedSequenceItems() {
            $post = $this->input->post();
            $resultset = array();

            if (isset($post["id"]) && ($post["id"] || $post["id"] == "0")) {
                unset($post["csrf_token"]);

                $arrData = array();
                $this->db->from($this->checklistItemTable);
                $this->db->where("id", $post["id"]);
                $this->db->where("status", 1);
                $query = $this->db->get();

                $row = ($query->num_rows() > 0) ? $query->row_array() : array();
                $html = $this->load->view("pms/task/modal_content/list_action", array("row" => $row), true);

                $checklist_id = $this->getAssignedSequenceItemList($post["id"]);
                $data = $this->sequenceItemsJson($post["id"]);

                $resultset["response"] = true;
                $resultset["html"] = $html;
                $resultset["data"] = $data;
                $resultset["checklist_id"] = $checklist_id;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getAssignedSequenceItemList($id = null) {
            if ($id || $id == "0") {
                $query = $this->db->get_where($this->checklistPrivilegeTable, array("checklist_id" => $id));
                if ($query->num_rows() == 1) {
                    $row = $query->row_array();
                    return unserialize($row["checklist_resource"]);
                } else {
                    return array();
                }
            } else {
                return array();
            }
        }

        function sequenceItemsJson($privilege_id) {
            $arrData = array();
            $this->db->select("items.id, items.parent_id, 
                        items.name, items.label");
            $this->db->where("items.is_active", 1);
            $this->db->order_by("items.sort", "ASC");
            $query = $this->db->get($this->itemTable . " items");

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $rs) {
                    $item_rate = $this->getTaskItemRate($rs["id"], $privilege_id);
                    $hasChildren = $this->db->where("parent_id", $rs["id"])->count_all_results($this->itemTable);

                    $tariff = intval($hasChildren) >= 1 ? "" : "(&#8369; " . (number_format((!empty($item_rate) ? $item_rate->tariff : 0), 2, ".", ","));
                    $uom = !empty($item_rate->uom_desc) ? " / " . $item_rate->uom_desc : "";
                    $tariff .= !empty($tariff) ? $uom . ")" : "";

                    $flag = false;
                    $data = array();
                    $data["id"] = $rs["id"];
                    $data["type"] = ($rs["parent_id"]) ? "child" : "root";
                    $data["parent"] = ($rs["parent_id"]) ? $rs["parent_id"] : "#";
                    $rate = (empty($rs["uom_desc"]) ? "" : (" per " . $rs["uom_desc"]));
                    $data["text"] = $rs["label"] .
                        "<span class='jstree-identifier m--font-boldest'> " . $tariff . " </span>";

                    $parentId = intval($rs["parent_id"]);
                    if ($parentId !== 0) {
                        $flag = $this->checkParentExist($parentId);
                    } else {
                        $flag = true;
                    }
                    if ($flag) {
                        $arrData[] = $data;
                    }
                }
            }
            return $arrData;
        }

        private function getTaskItemRate($item_id = null, $privilege_id = null) {
            $this->db->select("tariff, unit as uom_desc");
            $this->db->where("privilege_id", $privilege_id);
            $this->db->where("item_id", $item_id);
            $exist = $this->db->get($this->checklistRatesTable)->row();

            $this->db->reset_query();

            if (count($exist) > 0) {
                return $exist;
            } else {
                $this->db->select("rates.id, rates.tariff, uom.uom_desc, cat.category");
                $this->db->where("rates.item_id", $item_id);
                $this->db->join($this->uomTable . " uom", "rates.unit = uom.id and rates.approved_status=1", "INNER");
                $this->db->join("gccpms.sf_item_rates_category" . " cat", "rates.category_id = cat.id", "LEFT");
                $q = $this->db->get($this->itemRatesTable . " rates");
                return $q->row();
            }
        }

        function setChecklistJsonActions() {
            $post = $this->input->post();
            $resultset = array();

            if (isset($post["nodes"], $post["id"]) && ($post["nodes"] && $post["id"])) {
                $id = $post["id"];
                $priv_id = null;

                $nodes = json_decode($post["nodes"], true);
                if ($nodes) {
                    $ids = array();
                    $unselected_ids = array();
                    foreach ($nodes as $node) {
                        $selected = $node["state"]["selected"];
                        if ($selected) {
                            $ids[] = intval($node["id"]);
                        }
                    }

                    foreach ($nodes as $node) {
                        $selected = $node["state"]["selected"];
                        if (!$selected) {
                            $unselected_ids[] = intval($node["id"]);
                        }
                    }

                    $moduleExist = $this->checklistResourceExist($id);
                    if ($moduleExist) {
                        $where = array();
                        $where["id"] = $moduleExist["id"];

                        $arrData = array();
                        $arrData["checklist_resource"] = serialize($ids);
                        $update = $this->db->update($this->checklistPrivilegeTable, $arrData, $where);
                        $priv_id = $moduleExist["id"];

                        if ($update) {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "Checklist template has been updated.";
                        } else {
                            $resultset["toastr_msg"] = "Data update failed!";
                            $resultset["response"] = false;
                        }
                    } else {
                        $arrData = array();
                        $arrData["checklist_id"] = $id;
                        $arrData["checklist_resource"] = serialize($ids);

                        $saved = $this->db->insert($this->checklistPrivilegeTable, $arrData);
                        $priv_id = $this->db->insert_id();

                        if ($saved) {
                            $resultset["toastr_msg"] = "Checklist template has been saved.";
                            $resultset["response"] = true;
                        } else {
                            $resultset["toastr_msg"] = "Saving of data failed!";
                            $resultset["response"] = false;
                        }
                    }

                    $this->db->reset_query();

                    if (count($unselected_ids) >= 1) {
                        $this->db->where_in("item_id", $unselected_ids);
                        $this->db->where("privilege_id", $priv_id);
                        $this->db->delete($this->checklistRatesTable);
                    }

                    $this->db->reset_query();

                    // loop through selected task item by its id
                    foreach ($ids as $_id) {
                        $this->saveCheckListRateCard($priv_id, $_id);
                    }
                } else {
                    $resultset["toastr_msg"] = "No available nodes!";
                    $resultset["response"] = false;
                }
            } else {
                $resultset["toastr_msg"] = "No data found!";
                $resultset["response"] = false;
            }

            return $resultset;
        }

        private function saveCheckListRateCard($privilege_id, $item_id) {
            $item_rate = $this->getTaskItemRate($item_id);

            $this->db->where("item_id", $item_id);
            $this->db->where("privilege_id", $privilege_id);
            $exist = $this->db->count_all_results($this->checklistRatesTable);

            if ($exist <= 0) {
                $data = array(
                    "privilege_id" => $privilege_id,
                    "item_id" => $item_id,
                    "tariff" => empty($item_rate) ? 0 : $item_rate->tariff,
                    "unit" => empty($item_rate) ? NULL : strtoupper($item_rate->uom_desc),
                    "category" => empty($item_rate) ? NULL : strtoupper($item_rate->category),
                    "created_by" => $this->user_data["emp_id"],
                );

                $this->db->insert($this->checklistRatesTable, $data);
            }
        }

        function getSelect2TemplateData($id = null) {
            $resultset = array();
            $arrData = array();
            if ($id) {
                $this->db->select("id, label as text");
                $query = $this->db->get_where($this->checklistItemTable, array("project_id" => $id, "is_active" => 1, "status" => 1));
                if ($query->num_rows() > 0) {
                    $arrData = $query->result();
                }
            }

            $resultset["data"] = $arrData;
            return $resultset;
        }

        function getSelect2RequestedTask() {
            $resultset = array();
            $arrData = array();
            $get = $this->input->get();
            if (isset($get["checklist_id"]) && $get["checklist_id"]) {
                $privilegeItems = $this->db->get_where($this->checklistPrivilegeTable, array("checklist_id" => $get["checklist_id"]));
                if ($privilegeItems->num_rows() == 1) {
                    $userId = $this->core_layout->getCurrentEmployeeId();
                    $row = $privilegeItems->row();
                    $resourceIds = unserialize($row->checklist_resource);
                    $this->db->select("id, label as text");
                    $this->db->from($this->itemTable);
                    $this->db->where("is_active", 1);
                    $this->db->where("status", 1);
                    $this->db->where("parent_id !=", 0);
                    $this->db->where_in("id", $resourceIds);
                    if (isset($get["term"]) && $get["term"]) {
                        $this->db->like("label", $get["term"], "both");
                    }
                    $queryItems = $this->db->get();
                    if ($queryItems->num_rows() > 0) {
                        foreach ($queryItems->result() as $key => $value) {
                            $where = array();
                            $where["user_id"] = $userId;
                            $where["checklist_id"] = $get["checklist_id"];
                            $where["item_id"] = $value->id;

                            $where2 = array();
                            $where2["checklist_id"] = $get["checklist_id"];
                            $where2["item_id"] = $value->id;
                            $queryTemp = $this->db->get_where($this->checklistTempQtyTable, $where);
                            $queryCurrentItem = $this->db->get_where($this->checklistItemQtyTable, $where2);
                            if ($queryTemp->num_rows() == 0 && $queryCurrentItem->num_rows() == 0) {
                                $arrData[] = $value;
                            }
                        }
                    }
                }
            }

            $resultset["results"] = $arrData;
            return $resultset;
        }

        function getSequenceFormData($id = null) {
            $resultset = array();
            if ($id) {
                $this->db->select("a.id as unit_id, a.description, c.checklist_id, d.id as project_id, a.sf_remarks, a.sf_status");
                $this->db->from($this->projectUnitTable . " a");
                $this->db->join($this->checklistItemTable . " b", "b.id = a.checklist_id");
                $this->db->join($this->checklistPrivilegeTable . " c", "c.checklist_id = b.id");
                $this->db->join($this->projectTable . " d", "d.id = a.project_id");
                $this->db->where("a.is_active", 1);
                $this->db->where("a.status", 1);
                $this->db->where("b.is_active", 1);
                $this->db->where("b.status", 1);
                $this->db->where("a.id", $id);

                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $checklistResource = $this->getChecklistResources($row->unit_id);
                    $punchlistItems = $this->getPunchlistItems($row->unit_id);
                    $completedChecklistIds = $this->getCompletedChecklistResources($row->unit_id);
                    $checklistData = array();
                    $arrData = $this->checklistJson($row->checklist_id, $checklistResource, $punchlistItems);

                    $resultset["response"] = true;
                    $resultset["row"] = $row;
                    $resultset["data"] = $arrData;
                    $resultset["checklist_id"] = $checklistData;
                    $resultset["completed"] = $completedChecklistIds;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getChecklistResources($id = null) {
            $arrData = array();
            $this->db->select("a.task_id as pid, a.status as pstatus, b.task_id as cid, b.task_status as cstatus");
            $this->db->from($this->taskTable . " a");
            $this->db->join($this->taskTimelineTable . " b", "a.id = b.parent_id", "LEFT");
            $this->db->Where("a.unit_id", $id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $arrData[$rs->pid] = $rs->pstatus;
                    $arrData[$rs->cid] = $rs->cstatus;
                }
            }
            return $arrData;
        }

        function getCompletedChecklistResources($id = null) {
            $arrData = array();
            $this->db->select("a.task_id as id");
            $this->db->from($this->taskTable . " a");
            $this->db->join($this->taskTimelineTable . " b", "a.id = b.parent_id", "LEFT");
            $this->db->Where("a.unit_id", $id);
            $this->db->Where("a.status", 4);
            $this->db->group_start();
            $this->db->Where("b.task_status", 4);
            $this->db->or_Where("b.task_status", null);
            $this->db->group_end();
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $index => $rs) {
                    $arrData[$index] = intval($rs->id);
                }
            }
            return $arrData;
        }

        function saveSequenceFormData() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post) && $post) {
                $tempResponse = false;
                $tempState = "empty";

                $formId = (isset($post["id"]) && $post["id"]) ? intval($post["id"]) : 0;

                $nodes = json_decode($post["nodes"], true);
                $ids = array();
                foreach ($nodes as $node) {
                    $selected = $node["state"]["selected"];
                    if ($selected) {
                        $ids[] = intval($node["id"]);
                    }
                }
                $post["checklist_resource"] = serialize($ids);

                $parentData = array();
                $parentData["block_id"] = (isset($post["block_id"]) && $post["block_id"]) ? $post["block_id"] : 0;
                $parentData["remarks"] = (isset($post["remarks"]) && $post["remarks"]) ? $post["remarks"] : "";
                $parentData["status"] = (isset($post["status"]) && $post["status"]) ? intval($post["status"]) : 0;

                $arrKeyUnset = array("nodes", "remarks", "status", "block_id", "id");
                foreach ($arrKeyUnset as $value) {
                    unset($post[$value]);
                }

                $filtered = array_filter($post, function ($k) {
                    return preg_match('#switch-\d#', $k);
                }, ARRAY_FILTER_USE_KEY);

                $tempIds = array();
                if ($filtered) {
                    foreach ($filtered as $key => $value) {
                        $tempIds[] = $value;
                        unset($post[$key]);
                    }
                }

                if ($formId) {
                    $this->logCurrentMetaData($formId, $tempIds);
                    $where = array();
                    $where["id"] = $formId;
                    $updated = $this->db->update($this->formTable, $parentData, $where);
                    if ($updated) {
                        foreach ($post as $field => $value) {
                            $metaWhere = array();
                            $metaWhere["meta_id"] = $formId;
                            $metaWhere["meta_field"] = $field;

                            $metaData = array();
                            $metaData["meta_value"] = $value;

                            $searchExist = $this->db->get_where($this->formMetaTable, $metaWhere);
                            if ($searchExist->num_rows() == 1) {
                                $this->db->update($this->formMetaTable, $metaData, $metaWhere);
                            } else {
                                $metaData["meta_id"] = $formId;
                                $metaData["meta_field"] = $field;
                                $this->db->insert($this->formMetaTable, $metaData);
                            }
                        }
                        $tempResponse = true;
                        $tempState = "updated";
                    }
                } else {
                    $tempWhere = array("block_id" => $parentData["block_id"]);
                    $getForm = $this->db->get_where($this->formTable, $tempWhere);
                    if ($getForm->num_rows() == 0) {
                        $saved = $this->db->insert($this->formTable, $parentData);
                        if ($saved) {
                            $lastId = $this->db->insert_id();
                            foreach ($post as $field => $value) {
                                $metaData = array();
                                $metaData["meta_id"] = $lastId;
                                $metaData["meta_field"] = $field;
                                $metaData["meta_value"] = $value;
                                $this->db->insert($this->formMetaTable, $metaData);
                            }
                            $tempResponse = true;
                            $tempState = "saved";
                        }
                    }
                }

                if ($tempResponse) {
                    $arrGridData = $this->getSequenceFormGrid();
                    $resultset["response"] = true;
                    $resultset["state"] = $tempState;
                    $resultset["data"] = $arrGridData;
                } else {
                    $resultset["response"] = false;
                    $resultset["state"] = $tempState;
                }
            } else {
                $resultset["response"] = false;
            }


            return $resultset;
        }

        function logCurrentMetaData($id = null, $ids = array()) {
            if ($id && count($ids) > 0) {
                $this->db->from($this->formMetaTable);
                $this->db->where("meta_id", $id);
                $this->db->group_start();
                foreach ($ids as $vv) {
                    $arrFields = array("date_{$vv}", "due_{$vv}", "contractor_{$vv}", "contract_{$vv}");
                    foreach ($arrFields as $key => $field) {
                        if ($key == 0) {
                            $this->db->where("meta_field", $field);
                        } else {
                            $this->db->or_where("meta_field", $field);
                        }
                    }
                }
                $this->db->group_end();
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $arrData = array();
                    $arrData["form_id"] = $id;
                    $arrData["description"] = "contractor";
                    $arrData["meta"] = serialize($query->result());
                    $this->db->insert($this->formLogTable, $arrData);
                }
            }

            return $this;
        }

        function checklistJson($id = null, $resources = array(), $punchlist = array()) {
            $arrData = array();
            if ($id || $id == "0") {
                $query = $this->db->get_where($this->checklistPrivilegeTable, array("checklist_id" => $id));
                if ($query->num_rows() == 1) {
                    $row = $query->row_array();
                    $checklistResource = unserialize($row["checklist_resource"]);
                    if ($checklistResource) {
                        foreach ($checklistResource as $id) {
                            $queryItems = $this->db->get_where($this->itemTable, array("id" => $id, "is_active" => 1, "status" => 1));
                            if ($queryItems->num_rows() == 1) {
                                $row = $queryItems->row_array();

                                $tempRow = (object)$row;
                                $tempId = $tempRow->id;
                                $parentId = $tempRow->parent_id;

                                $flag = false;
                                $type = ($parentId) ? "child" : "root";
                                $tempStatus = (isset($resources[$tempId]) && $resources[$tempId]) ? intval($resources[$tempId]) : 0;
                                $tempState = "";
                                switch ($tempStatus) {
                                    case 1:
                                        $tempState = 'm--font-brand m--font-boldest';
                                        break;
                                    case 2:
                                        $tempState = 'm--font-success m--font-boldest';
                                        break;
                                    case 3:
                                        $tempState = 'm--font-custom_dark m--font-boldest';
                                        break;
                                    case 4:
                                        $tempState = 'm--font-danger m--font-boldest';
                                        break;
                                    default:
                                        $tempState = '';
                                        break;
                                }

                                $type = (isset($resources[$tempId]) && $resources[$tempId] == 3) ? "completed" : $type;
                                $type = (isset($resources[$tempId]) && $resources[$tempId] == 4) ? "terminated" : $type;
                                if (isset($punchlist[$tempId]) && $punchlist[$tempId] && $parentId !== "0") {
                                    $xx = strtolower($punchlist[$tempId]);
                                    $type = $xx;

                                    switch ($xx) {
                                        case "punchlist":
                                            $tempState = "m--font-primary m--font-boldest punchlist-state";
                                            break;
                                        case "punchlisted":
                                            $tempState = "m--font-success m--font-boldest punchlisted-state";
                                            break;
                                        case "completed":
                                            $tempState = 'm--font-success m--font-boldest';
                                            break;
                                        default:
                                            $tempState = 'm--font-success m--font-boldest';
                                            break;

                                    }
                                }
                                $data = array();
                                $data["id"] = $row["id"];
                                $data["type"] = $type;
                                $data["parent"] = ($parentId) ? $parentId : "#";
                                $data["text"] = $row["label"];
                                $data["a_attr"]["class"] = ($parentId) ? $tempState : "parent_node {$tempState}";
                                $data["li_attr"]["class"] = ($parentId) ? "" : "jstree-parent_node";

                                $parentId = intval($parentId);
                                if ($parentId !== 0) {
                                    $flag = $this->checkParentExist($parentId);
                                } else {
                                    $flag = true;
                                }
                                if ($flag) {
                                    $arrData[] = $data;
                                }
                            }
                        }
                    }
                }
            }
            return $arrData;
        }

        function renderSubtaskForm($id = null) {
            $resultset = array();
            $arrData = array();

            $subTaskData = $this->generateSubtaskData($id);
            if ($subTaskData && count($subTaskData) > 0) {
                $activityData = $this->generateSubtaskActivity($id);
                $resultset["response"] = true;
                $resultset["row"] = $subTaskData;
                $resultset["activity"] = $activityData;
                $resultset["activity_count"] = count($activityData);
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function generateSubtaskActivity($id = null) {
            $arrData = array();

            if ($id) {
                $this->db->select("a.*, DATE_FORMAT(a.created_at, '%M %e, %Y %h:%i %p') as created_at, b.lastname, b.firstname, b.middlename, b.suffix");
                $this->db->from($this->taskTimelineActivityTable . " a");
                $this->db->join($this->employeeTable . " b", "b.id=a.user_id");
                $this->db->where("a.timeline_id", $id);
                $this->db->where("a.is_active", 1);
                $this->db->order_by("a.created_at", "DESC");
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $index => $rs) {
                        $tempRs = (array)$rs;
                        $tempName = $this->core_layout->getDisplayName($tempRs);
                        $tempDisplayName = (isset($tempName["display_name_1"]) && $tempName["display_name_1"]) ? $tempName["display_name_1"] : "No assigned name";
                        $rs->display_name = $tempDisplayName;
                        $arrData[$index] = $rs;
                    }
                }
            }

            return $arrData;
        }

        function generateSubtaskData($id) {
            $arrData = array();
            if ($id) {
                $this->db->select("a.id, b.unit_id, b.task_id, c.label as task_name, e.contractor, e.id as contractor_id, a.description, d.issued_date, d.due_date, a.task_id, a.task_status");
                $this->db->from($this->taskTimelineTable . " a");
                $this->db->join($this->taskTable . " b", "b.id = a.parent_id");
                $this->db->join($this->itemTable . " c", "c.id = a.task_id");
                $this->db->join($this->contractTable . " d", "d.id = a.contract_id");
                $this->db->join($this->contractorTable . " e", "e.id = d.contractor_id");
                $this->db->where("a.id", $id);
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $row->is_punchlisted = false;
                    $tempData = $this->getPunchlistItems($row->unit_id);
                    switch ($row->task_status) {
                        case "1":
                            $row->state_description = "IN PROGRESS";
                            $row->state = "m-badge--brand m--font-light";
                            break;
                        case "2":
                            $row->state_description = "DEFERRED";
                            $row->state = "m-badge--success m--font-light";
                            break;
                        case "3":
                            $row->state_description = "COMPLETED";
                            $row->state = "";
                            break;
                        case "4":
                            $row->state_description = "TERMINATED";
                            $row->state = "m-badge--danger m--font-light";
                            break;
                        default:
                            $row->state_description = "AWAITING";
                            $row->state = "";
                            break;
                    }

                    if (isset($tempData[$row->task_id]) && $tempData[$row->task_id]) {
                        $xx = strtolower($tempData[$row->task_id]);
                        switch ($xx) {
                            case "completed":
                                $row->state_description = "COMPLETED";
                                $row->state = "";
                                $row->is_punchlisted = false;
                                break;
                            case "punchlist":
                                $row->state_description = "PUNCHLIST";
                                $row->state = "m-badge--primary m--font-light";
                                $row->is_punchlisted = true;
                                break;
                            case "punchlisted":
                                $row->state_description = "PUNCHLISTED";
                                $row->state = "m-badge--success m--font-light";
                                $row->is_punchlisted = true;
                                break;
                            default:
                                $row->state_description = "COMPLETED";
                                $row->state = "";
                                $row->is_punchlisted = false;
                                break;
                        }
                    }

                    $arrData = $row;
                }
            }
            return $arrData;
        }

        function renderTaskForm($itemId = null, $unitId = null) {
            $resultset = array();
            $arrData = array();

            $data = array("task_id" => $itemId, "unit_id" => $unitId);
            $query = $this->db->get_where($this->taskTable, $data);
            if ($query->num_rows() == 1) {
                $arrData["id"] = $query->row()->id;
                $html = $this->load->view("pms/task/modal_content/task_content", $arrData, true);
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }


            return $resultset;
        }

        function renderTaskDurationForm($itemId = null, $unitId = null) {
            $resultset = array();
            if ($itemId && $unitId) {
                $currentId = 0;
                $data = array("task_id" => $itemId, "unit_id" => $unitId);
                $query = $this->db->get_where($this->taskTable, $data);
                if ($query->num_rows() == 1) {
                    $currentId = $query->row()->id;
                } else {
                    $this->db->insert($this->taskTable, $data);
                    $currentId = $this->db->insert_id();
                }

                if ($currentId) {
                    $session = $this->core_layout->getCurrentSession();
                    $query = $this->db->get_where($this->taskTable, array("id" => $currentId));
                    if ($query->num_rows() == 1) {
                        $row = $query->row();
                        $html = $this->load->view("pms/task/modal_content/task_duration", null, true);
                        $resultset["response"] = true;
                        $resultset["row"] = $row;
                        $resultset["html"] = $html;
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

        function renderContractForm($itemId = null, $unitId = null) {
            $resultset = array();
            if ($itemId && $unitId) {
                $contractCount = $this->getTaskContractCount($itemId, $unitId);
                $currentId = 0;
                $data = array("task_id" => $itemId, "unit_id" => $unitId);
                $query = $this->db->get_where($this->taskTable, $data);
                if ($query->num_rows() == 1) {
                    $currentId = $query->row()->id;
                } else {
                    $this->db->insert($this->taskTable, $data);
                    $currentId = $this->db->insert_id();
                }
                if ($currentId) {
                    $session = $this->core_layout->getCurrentSession();
                    $query = $this->db->get_where($this->taskTable, array("id" => $currentId));
                    if ($query->num_rows() == 1) {
                        $row = $query->row();
                        $this->db->delete($this->woCodeTempTable, array("user_id" => $session["emp_id"]));

                        $arrData = array("id" => $currentId, "count" => $contractCount);
                        $html = $this->load->view("pms/task/modal_content/task_contract", $arrData, true);
                        $resultset["response"] = true;
                        $resultset["data"] = $row;
                        $resultset["html"] = $html;
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

        function getTaskContractCount($itemId = null, $unitId = null) {
            $arrCount = 0;
            if ($itemId && $unitId) {
                $where = array("a.task_id" => $itemId, "a.unit_id" => $unitId);
                $this->db->from($this->taskTable . " a");
                $this->db->join($this->contractTable . " b", "b.parent_id = a.id");
                $this->db->where($where);
                $query = $this->db->get();
                $arrCount = $query->num_rows();
            }

            return $arrCount;
        }

        function generateContractCode($itemId = null, $unitId = null) {
            $temporaryCode = "";
            if ($itemId && $unitId) {
                $where = array("a.id" => $unitId, "a.is_active" => 1,
                    "a.status" => 1, "b.is_active" => 1, "b.status" => 1);

                $this->db->select("UPPER(CONCAT(c.code, '',d.code)) as project_code");
                $this->db->from($this->projectUnitTable . " a");
                $this->db->join($this->projectTable . " b", "b.id=a.project_id");
                $this->db->join($this->projectCompanyTable . " c", "c.id=b.company_id");
                $this->db->join($this->projectLocationTable . " d", "d.id=b.location_id");
                $this->db->where($where);
                $queryUnit = $this->db->get();
                if ($queryUnit->num_rows() == 1) {
                    $temporaryCode = $queryUnit->row()->project_code;
                    $where = array("a.id" => $itemId, "a.is_active" => 1, "a.status" => 1,
                        "b.is_active" => 1, "b.status" => 1);
                    $this->db->select("b.series");
                    $this->db->from($this->itemTable . " a");
                    $this->db->join($this->woTypeTable . " b", "b.id = a.wo_type_id");
                    $this->db->where($where);
                    $queryItem = $this->db->get();
                    if ($queryItem->num_rows() == 1) {
                        $temporaryCode .= $queryItem->row()->series;
                    }
                }
            }

            if ($temporaryCode) {
                return $temporaryCode .= "-" . date("ymd");
            } else {
                return false;
            }
        }

        function generateCurrentTask() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                unset($post["csrf_teken"]);
                $resultset = $this->getSequenceFormData($post["id"]);
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getCurrentTaskData($id = null) {
            $resultset = array();
            if ($id) {
                $sqlSelect = "b.id, UPPER(c.label) as task_name, UPPER(e.contractor) as contractor, UPPER(b.description) as description, c.id as item_id, d.issued_date, ";
                $sqlSelect .= "d.due_date, b.task_status, DATE_FORMAT(b.created_at, '%M %e, %Y %h:%i %p') as created_at, ";
                $sqlSelect .= "f.extension_date, d.extension_id, d.task_incharge, ";
                $sqlSelect .= "(SELECT COUNT(id) FROM {$this->taskTimelineActivityTable} as x WHERE x.timeline_id = b.id) as activity_count";

                $this->db->select($sqlSelect);
                $this->db->from($this->taskTable . " a");
                $this->db->join($this->taskTimelineTable . " b", "b.parent_id = a.id");
                $this->db->join($this->itemTable . " c", "c.id = b.task_id");
                $this->db->join($this->contractTable . " d", "d.id = b.contract_id");
                $this->db->join($this->contractorTable . " e", "e.id = d.contractor_id");
                $this->db->join($this->contractExtensionTable . " f", "f.contract_id = d.id", "LEFT");
                $this->db->where("a.id", $id);
                $this->db->group_by("b.id");
                $this->db->order_by("b.id", "DESC");
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $arrTempData = array();
                    $arrIds = array();
                    $queryTask = $this->db->get_where($this->taskTable, array("id" => $id));
                    if ($queryTask->num_rows() == 1) {
                        $row = $queryTask->row();
                        $arrTempData = $this->getPunchlistItems($row->unit_id);
                        $arrIds = $this->getPunchlistedItems($row->unit_id);
                    }

                    $arrData = array();
                    foreach ($query->result() as $key => $rs) {
                        $inchargeName = $this->core_layout->getEmployeeData($rs->task_incharge);
                        $inchargeName = (object)$inchargeName;
                        $rs->incharge = (isset($inchargeName->display_name_1) && $inchargeName->display_name_1) ? $inchargeName->display_name_1 : "No assigned name";

                        if (isset($arrTempData[$rs->item_id]) && $arrTempData[$rs->item_id]) {
                            $xx = strtolower($arrTempData[$rs->item_id]);
                            if ($xx == "completed") {
                                $rs->task_status = "3";
                            } else {
                                $rs->task_status = "5";
                            }
                        }

                        $rs->is_punchlisted = (in_array($rs->item_id, $arrIds)) ? true : false;
                        $arrData[$key] = $rs;
                    }

                    $resultset["response"] = true;
                    $resultset["task"] = $arrData;
                    $resultset["task_count"] = $query->num_rows();
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getSelectedTaskData() {
            $resultset = array();
            $post = $this->input->post();
            if ($post) {
                unset($post["csrf_teken"]);
                $currentRow = $this->generateCurrentTaskData($post);
                $currentWo = (isset($currentRow->task) && $currentRow->task) ? $this->getContractorTaskById($currentRow->task) : array();
                $html = $this->load->view("pms/task/form_content/task_content", null, true);
                $resultset["response"] = true;
                $resultset["row"] = $currentRow;
                $resultset["contractor"] = $currentWo;
                $resultset["contractor_count"] = count($currentWo);
                $resultset["item_id"] = $post["item_id"];
                $resultset["unit_id"] = $post["unit_id"];
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getTaskCodes($id = null) {
            $arrData = array();
            if ($id) {
                $get = $this->input->get();

                $this->db->select("id, label as text");
                $this->db->from($this->itemTable);
                $this->db->group_start();
                $this->db->where("id", $id);
                $this->db->or_where("parent_id", $id);
                $this->db->group_end();
                if (isset($get["term"]) && $get["term"]) {
                    $this->db->like("label", $get["term"], "both");
                }
                $this->db->order_by("sort", "ASC");
                $queryItem = $this->db->get();

                if ($queryItem->num_rows() > 0) {
                    $arrData = $queryItem->result();
                }
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;

        }

        function getChildTaskCodes($id = null) {
            $arrData = array();
            if ($id) {
                $get = $this->input->get();
                $arrIds = $this->generateChildTaskCodesByParentId($get["p1"], $get["p2"]);
                $this->db->select("id, label as text");
                $this->db->from($this->itemTable);
                $this->db->where("parent_id", $id);
                if ($arrIds && count($arrIds) > 0) {
                    $this->db->where_not_in("id", $arrIds);
                }
                if (isset($get["term"]) && $get["term"]) {
                    $this->db->like("label", $get["term"], "both");
                }
                $this->db->order_by("sort", "ASC");
                $queryItem = $this->db->get();

                if ($queryItem->num_rows() > 0) {
                    $arrData = $queryItem->result();
                }
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;

        }

        function generateChildTaskCodesByParentId($item = null, $unit = null) {
            $arrData = array();
            if ($item && $unit) {
                $this->db->select("a.task_id");
                $this->db->from($this->taskTimelineTable . " a");
                $this->db->join($this->taskTable . " b", "b.id = a.parent_id");
                $this->db->where("b.task_id", $item);
                $this->db->where("b.unit_id", $unit);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $rs) {
                        $arrData[] = intval($rs->task_id);
                    }
                }
            }
            return $arrData;
        }

        function getSelect2TaskContractor($id = null, $unitId = null) {
            $arrData = array();
            if ($id && $unitId) {
                $get = $this->input->get();
                $this->db->select("c.id, UPPER(c.contractor) as text, b.issued_date, b.due_date");
                $this->db->from($this->taskTable . " a");
                $this->db->join($this->contractTable . " b", "b.parent_id = a.id AND b.status != 0");
                $this->db->join($this->contractorTable . " c", "c.id = b.contractor_id AND c.is_archived = 0");
                $this->db->where("a.task_id", $id);
                $this->db->where("a.unit_id", $unitId);
                if (isset($get["term"]) && $get["term"]) {
                    $this->db->group_start();
                    $this->db->like("b.wo_code", $get["term"], "both");
                    $this->db->or_like("c.contractor", $get["term"], "both");
                    $this->db->group_end();
                }
                $this->db->group_by("c.id");
                $this->db->order_by("c.contractor", "ASC");
                $queryItem = $this->db->get();

                if ($queryItem->num_rows() > 0) {
                    $arrData = $queryItem->result();
                }
            }

            $resultset = array();
            $resultset["results"] = $arrData;
            return $resultset;
        }

        function generateCurrentUnitTaskById($id = null) {
            if ($id) {
                $query = $this->db->get_where($this->taskTable, array("id" => $id));
                if ($query->num_rows() == 1) {
                    return $query->row()->unit_id;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getContractorTaskById($id = null) {
            $arrData = array();
            if ($id) {
                $this->db->select("a.id, a.task_incharge as incharge_id, a.wo_code, upper(c.contractor) as contractor, a.created_at, b.contract_status as status");
                $this->db->from($this->contractTable . " a");
                $this->db->join($this->contractUnitTable . " b", "b.contract_id = a.id");
                $this->db->join($this->contractorTable . " c", "c.id = a.contractor_id");
                $this->db->where("b.task_id", $id);
                $this->db->order_by("a.id", "DESC");
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $key => $rs) {
                        $tempName = $this->core_layout->getEmployeeData($rs->incharge_id);
                        $displayName = (object)$tempName;
                        $rs->task_incharge = (isset($displayName->display_name_1) && $displayName->display_name_1) ? $displayName->display_name_1 : "No Assigned Name";
                        $arrData[$key] = $rs;
                    }
                }
            }

            return $arrData;
        }

        function generateCurrentTaskById($id = null) {
            $arrData = array();
            if ($id) {
                $this->db->select("a.*, b.id as task, b.remarks, b.status, c.wo_code, c.issued_date, c.due_date, c.remarks as wo_remarks, d.contractor, e.lastname, e.firstname, e.middlename, e.suffix");
                $this->db->from($this->itemTable . " a");
                $this->db->join($this->taskTable . " b", "b.task_id = a.id");
                $this->db->join($this->contractTable . " c", "c.id = b.contract_id", "LEFT");
                $this->db->join($this->contractorTable . " d", "d.id = c.contractor_id", "LEFT");
                $this->db->join($this->employeeTable . " e", "e.id = c.task_incharge", "LEFT");
                $this->db->where("b.id", $id);
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $currentRow = $query->row();
                    $tempRow = (array)$currentRow;
                    $tempName = $this->core_layout->getDisplayName($tempRow);
                    $tempName = (object)$tempName;
                    $currentRow->task_incharge = (isset($tempName->display_name_1) && $tempName->display_name_1) ? $tempName->display_name_1 : "No assigned name";
                    $arrData = $currentRow;
                }
            }

            return $arrData;
        }

        function generateCurrentTaskData($data = array()) {
            $arrData = array();
            if ($data) {
                $this->db->select("a.*, b.issued_date as cdate1, b.due_date as cdate2, b.id as task, b.remarks, b.status, c.wo_code, c.issued_date, c.due_date, c.remarks as wo_remarks, d.contractor, e.lastname, e.firstname, e.middlename, e.suffix");
                $this->db->from($this->itemTable . " a");
                $this->db->join($this->taskTable . " b", "b.task_id = a.id", "LEFT");
                $this->db->join($this->contractTable . " c", "c.id = b.contract_id", "LEFT");
                $this->db->join($this->contractorTable . " d", "d.id = c.contractor_id", "LEFT");
                $this->db->join($this->employeeTable . " e", "e.id = c.task_incharge", "LEFT");
                $this->db->where("a.id", $data["item_id"]);
                $this->db->where("b.unit_id", $data["unit_id"]);
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $currentRow = $query->row();
                    $tempRow = (array)$currentRow;
                    $tempName = $this->core_layout->getDisplayName($tempRow);
                    $tempName = (object)$tempName;
                    $currentRow->task_incharge = (isset($tempName->display_name_1) && $tempName->display_name_1) ? $tempName->display_name_1 : "No assigned name";
                    $arrData = $currentRow;
                }
            }

            $availableTaskIds = $this->getAvailableTask($data["unit_id"], $data["item_id"]);
            if ($arrData) {
                $arrData->task_count = ($availableTaskIds && count($availableTaskIds) > 0) ? count($availableTaskIds) : 0;
            } else {
                $arrData = (object)$arrData;
                $arrData->task_count = ($availableTaskIds && count($availableTaskIds) > 0) ? count($availableTaskIds) : 0;
            }
            return $arrData;
        }

        function generateParentNode() {
            $resultset = array();
            $post = $this->input->post();

            $arrParentNodes = array();
            $arrNodeIds = array();
            if ($post) {
                $selectedNodes = $post["selected_nodes"];
                if ($selectedNodes) {
                    foreach ($selectedNodes as $id) {
                        $this->db->select("id, label, parent_id");
                        $query = $this->db->get_where($this->itemTable, array("id" => $id, "is_active" => 1, "status" => 1));
                        if ($query->num_rows() == 1) {
                            $row = $query->row();
                            if ($row->parent_id == 0) {
                                if (!in_array($row->id, $arrNodeIds)) {
                                    $arrNodeIds[] = $row->id;
                                    $arrParentNodes[$row->id] = $row;
                                }
                            } else {
                                $this->db->select("id, label, parent_id");
                                $query_parent = $this->db->get_where($this->itemTable, array("id" => $row->parent_id, "is_active" => 1, "status" => 1));
                                if ($query_parent->num_rows() == 1) {
                                    $_row = $query_parent->row();
                                    if (!in_array($_row->id, $arrNodeIds)) {
                                        $arrNodeIds[] = $_row->id;
                                        $arrParentNodes[$_row->id] = $_row;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $resultset["parent_nodes"] = $arrParentNodes;
            $resultset["parent_id"] = $arrNodeIds;
            $resultset["node_count"] = count($arrNodeIds);

            return $resultset;
        }

        function getSequenceFormGrid() {
            $arrData = array();
            $rowCount = 0;

            $this->db->select("a.*, upper(b.remarks) as remarks, IFNULL(b.status, 0) as form_status");
            $this->db->from($this->projectUnitTable . " a");
            $this->db->join($this->formTable . " b", "b.block_id = a.id", "left");
            $this->db->where("a.is_active", 1);
            $this->db->where("a.status", 1);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = $query->result();
                $rowCount = $query->num_rows();
            }

            $resultset = array();
            $resultset["rows"] = $arrData;
            $resultset["row_count"] = $rowCount;

            return $resultset;
        }

        protected function checklistResourceExist($id = null) {
            if ($id) {
                $query = $this->db->get_where($this->checklistPrivilegeTable, array("checklist_id" => $id));
                if ($query->num_rows() == 1) {
                    $row = $query->row_array();
                    return $row;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        private function getAclParent($parent_id) {
            $this->db->select("id, 'root' `type`, '#' parent, label text");
            $this->db->where("id", $parent_id);
            $data = $this->db->get($this->itemTable)->row();
            return json_encode($data);
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

        private function checkSequenceItemCode($data = array()) {
            if ($data) {
                $query = $this->db->get_where($this->itemTable, array("name" => $data["name"]));
                if ($query->num_rows() == 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        private function checkChecklistItemCode($data = array()) {
            if ($data) {
                $query = $this->db->get_where($this->checklistItemTable, array("name" => $data["name"]));
                if ($query->num_rows() == 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function render_modal_punchlist_task_log() {
            $resultset = array();
            $html = $this->load->view("pms/task/modal_content/punchlist_log_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_checklist_qty_request() {
            $resultset = array();
            $html = $this->load->view("pms/task/modal_content/checklist_qty_request_modal", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_checklist_qty_history() {
            $resultset = array();
            $html = $this->load->view("pms/task/modal_content/checklist_qty_history_modal", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_punchlist_task() {
            $resultset = array();
            $html = $this->load->view("pms/task/modal_content/punchlist_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_todo_task() {
            $resultset = array();
            $html = $this->load->view("pms/task/modal_content/preview_todo_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_edit_modal_subtask() {
            $resultset = array();
            $html = $this->load->view("pms/task/modal_content/edit_subtask_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function render_modal_subtask_activity() {
            $resultset = array();
            $html = $this->load->view("pms/task/modal_content/sub_task_content", null, true);
            if ($html) {
                $resultset["response"] = true;
                $resultset["html"] = $html;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getAvailableTask($unitId = null, $parentId = null) {
            $arrIds = array();
            $this->db->select("b.checklist_resource");
            $this->db->from($this->projectUnitTable . " a");
            $this->db->join($this->checklistPrivilegeTable . " b", "b.checklist_id = a.checklist_id");
            $this->db->where("a.id", $unitId);
            $this->db->where("a.is_active", 1);
            $this->db->where("a.status", 1);
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                $row = $query->row();
                $checklistResource = $row->checklist_resource;
                $checklistResource = unserialize($checklistResource);

                $items = $this->db->get_where($this->itemTable, array("parent_id" => $parentId, "is_active" => 1, "status" => 1));
                if ($items->num_rows() > 0) {
                    foreach ($items->result() as $key => $rs) {
                        if (in_array($rs->id, $checklistResource)) {
                            $this->db->from($this->taskTimelineTable . " a");
                            $this->db->join($this->taskTable . " b", "b.id = a.parent_id");
                            $this->db->join($this->contractTable . " c", "c.id = a.contract_id");
                            $this->db->where("a.task_id", $rs->id);
                            $this->db->where("b.unit_id", $unitId);
                            $this->db->where("b.task_id", $parentId);
                            $this->db->where("a.task_status !=", 5);
                            $this->db->where("c.status !=", 3);
                            $task = $this->db->get();
                            if ($task->num_rows() == 0) {
                                $this->db->from($this->taskTimelineTable . " a");
                                $this->db->join($this->taskTable . " b", "b.id = a.parent_id");
                                $this->db->where("a.task_id", $rs->id);
                                $this->db->where("b.unit_id", $unitId);
                                $this->db->where("b.task_id", $parentId);
                                $this->db->where("a.task_status", 5);
                                $tempItem = $this->db->get();
                                if ($tempItem->num_rows() == 0) {
                                }

                                $arrIds[] = $rs->id;
                            }
                        }
                    }
                }
            }

            return $arrIds;
        }

        function generatePunchlistTaskLog($id = null) {
            $resultset = array();
            if ($id) {
                $query = $this->db->get_where($this->punchlistLogTable, array("id" => $id));
                if ($query->num_rows() == 1) {
                    $arrData = array();
                    $row = $query->row();
                    $parentId = array();

                    $tempName = $this->core_layout->getEmployeeData($row->user_id);
                    $tempName = (object)$tempName;
                    $row->logged_by = (isset($tempName->display_name_1) && $tempName->display_name_1) ? $tempName->display_name_1 : "No assigned name";
                    $selected = unserialize($row->selected_items);
                    if ($selected && count($selected) > 0) {
                        $this->db->select("id, label, parent_id");
                        $this->db->from($this->itemTable);
                        $this->db->where_in("id", $selected);
                        $this->db->where("parent_id !=", 0);
                        $queryItem = $this->db->get();
                        if ($queryItem->num_rows() > 0) {
                            foreach ($queryItem->result() as $rs) {
                                $nData = array();
                                $queryParent = $this->db->get_where($this->itemTable, array("id" => $rs->parent_id));
                                if ($queryParent->num_rows() == 1) {
                                    $parentRow = $queryParent->row();
                                    if (!in_array($parentRow->id, $parentId)) {
                                        $tempData = array();
                                        $tempData["id"] = $parentRow->id;
                                        $tempData["type"] = "root";
                                        $tempData["parent"] = "#";
                                        $tempData["text"] = $parentRow->label;
                                        $arrData[] = $tempData;
                                        $parentId[] = $parentRow->id;
                                    }
                                }
                                $nData["id"] = $rs->id;
                                $nData["type"] = "child";
                                $nData["parent"] = $rs->parent_id;
                                $nData["text"] = $rs->label;
                                $arrData[] = $nData;
                            }
                        }
                    }

                    switch ($row->status) {
                        case '0':
                            $row->log_status = "COMPLETED";
                            break;
                        case '1':
                            $row->log_status = "PUNCHLIST";
                            break;
                        case '2':
                            $row->log_status = "PUNCHLISTED";
                            break;
                        default:
                            $row->log_status = "COMPLETED";
                            break;
                    }

                    $resultset["response"] = true;
                    $resultset["row"] = $row;
                    $resultset["data"] = $arrData;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }


            return $resultset;
        }

        function generatePunchlistTask($unitId = null) {
            $resultset = array();
            if ($unitId) {
                $arrData = array();
                $filter = array("status !=", 2);
                $arrIds = $this->getPunchlistTaskIds($unitId, $filter);
                if ($arrIds && count($arrIds) > 0) {
                    $tempWhere = array("is_active" => 1, "status" => 1);
                    $this->db->from($this->itemTable);
                    $this->db->where($tempWhere);
                    $this->db->where_in("id", $arrIds);
                    $queryItemsChecklist = $this->db->get();
                    if ($queryItemsChecklist->num_rows() > 0) {
                        foreach ($queryItemsChecklist->result() as $key => $value) {
                            $tempData = array();
                            $tempData["id"] = $value->id;
                            $tempData["type"] = (isset($value->parent_id) && $value->parent_id) ? "child" : "root";
                            $tempData["parent"] = (isset($value->parent_id) && $value->parent_id) ? $value->parent_id : "#";
                            $tempData["text"] = strtoupper($value->label);
                            $tempData["a_attr"]["class"] = (isset($value->parent_id) && $value->parent_id) ? "" : "parent_node";
                            $arrData[] = $tempData;
                        }
                    }
                }

                if ($arrData && count($arrData) > 0) {
                    $resultset["response"] = true;
                    $resultset["data"] = $arrData;
                    $resultset["unit_id"] = $unitId;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getPunchlistTaskCount($unitId = null) {
            $count = 0;
            $this->db->from($this->punchlistedTaskTable . " a");
            $this->db->join($this->itemTable . " b", "b.id = a.item_id");
            $this->db->where("a.status !=", 2);
            $this->db->where("a.unit_id", $unitId);
            $this->db->where("b.parent_id !=", 0);
            $query = $this->db->get();
            $count = $query->num_rows();

            $tempIds = array();
            $punchlisted = $this->db->get_where($this->punchlistedTaskTable, array("status" => 2));
            if ($punchlisted->num_rows() > 0) {
                foreach ($punchlisted->result() as $kk => $vv) {
                    $tempIds[] = $vv->item_id;
                }
            }

            $this->db->from($this->taskTable . " a");
            $this->db->join($this->taskTimelineTable . " b", "b.parent_id = a.id");
            $this->db->where("a.unit_id", $unitId);
            $this->db->where("b.task_status", 3);
            if ($tempIds) {
                $this->db->where_not_in("b.task_id", $tempIds);
            }

            $this->db->group_by("b.task_id");
            $tempTask = $this->db->get();
            $tempCount = $tempTask->num_rows();

            if ($count == 0) {
                return intval($count) + intval($tempCount);
            } else {
                return $count;
            }
        }

        function getPunchlistTaskIds($unitId = null, $filter = array()) {
            $arrIds = array();
            if ($unitId) {
                $punchlistItems = $this->getPunchlistItems($unitId, $filter, true);
                $this->db->select("b.checklist_resource");
                $this->db->from($this->projectUnitTable . " a");
                $this->db->join($this->checklistPrivilegeTable . " b", "b.checklist_id = a.checklist_id");
                $this->db->where("a.id", $unitId);
                $this->db->where("a.is_active", 1);
                $this->db->where("a.status", 1);
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $checklistResource = $row->checklist_resource;
                    $checklistResource = unserialize($checklistResource);
                    $this->db->select("a.id as item_id, b.id as task_id, a.label as task_name");
                    $this->db->from($this->itemTable . " a");
                    $this->db->join($this->taskTable . " b", "b.task_id = a.id");
                    $this->db->where("a.parent_id", 0);
                    $this->db->where("a.is_active", 1);
                    $this->db->where("a.status", 1);
                    $this->db->where("b.unit_id", $unitId);
                    /*** $this->db->where("b.status", 3); ***/
                    $this->db->where("b.status !=", 4);
                    $queryTask = $this->db->get();
                    if ($queryTask->num_rows() > 0) {
                        foreach ($queryTask->result() as $key => $value) {
                            $allowPunchlist = true;
                            $this->db->from($this->itemTable);
                            $this->db->where("parent_id", $value->item_id);
                            $this->db->where_in("id", $checklistResource);
                            $queryItems = $this->db->get();
                            if ($queryItems->num_rows() > 0) {
                                foreach ($queryItems->result() as $kk => $vv) {
                                    /*** $where = array("a.task_id" => $vv->id, "a.parent_id" => $value->task_id, "a.task_status" => 3); ***/
                                    $where = array("a.task_id" => $vv->id, "a.parent_id" => $value->task_id, "a.task_status !=" => 4);
                                    $this->db->select("b.id, b.parent_id");
                                    $this->db->from($this->taskTimelineTable . " a");
                                    $this->db->join($this->itemTable . " b", "b.id = a.task_id");
                                    $this->db->where($where);
                                    $querySubTask = $this->db->get();
                                    if ($querySubTask->num_rows() == 1) {
                                        $row = $querySubTask->row();
                                        if (!in_array($row->id, $arrIds) && in_array($row->id, $punchlistItems)) {
                                            $arrIds[] = $row->id;
                                        }
                                        if (!in_array($row->parent_id, $arrIds) && in_array($row->id, $punchlistItems)) {
                                            $arrIds[] = $row->parent_id;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $arrIds;
        }

        function getPunchlistItems($unitId = null, $filter = array(), $ids = false) {
            $arrData = array();
            if ($unitId) {
                $tempIds = array();
                if ($filter) {
                    $this->db->where($filter);
                }
                $queryTask = $this->db->get_where($this->punchlistedTaskTable, array("unit_id" => $unitId));
                if ($queryTask->num_rows() > 0) {
                    foreach ($queryTask->result() as $key => $value) {
                        $tempIds[] = $value->item_id;
                        if ($ids) {
                            $arrData[] = $value->item_id;
                        } else {
                            switch ($value->status) {
                                case "1":
                                    $arrData[$value->item_id] = "Punchlist";
                                    break;
                                case "2":
                                    $arrData[$value->item_id] = "Punchlisted";
                                    break;
                                default:
                                    $arrData[$value->item_id] = "Completed";
                                    break;
                            }
                        }
                    }
                }

                if ($filter && count($tempIds) == 0) {
                    $punchlisted = $this->db->get_where($this->punchlistedTaskTable, array("status" => 2));
                    if ($punchlisted->num_rows() > 0) {
                        foreach ($punchlisted->result() as $kk => $vv) {
                            $tempIds[] = $vv->item_id;
                        }
                    }
                }

                $this->db->select("a.task_id as parent_task, b.task_id as sub_task");
                $this->db->from($this->taskTable . " a");
                $this->db->join($this->taskTimelineTable . " b", "b.parent_id = a.id");
                $this->db->where("a.unit_id", $unitId);
                $this->db->where("b.task_status", 3);
                if ($tempIds) {
                    $this->db->where_not_in("b.task_id", $tempIds);
                }

                $tempTask = $this->db->get();
                foreach ($tempTask->result() as $kk => $vv) {
                    if ($ids) {
                        $arrData[] = $vv->sub_task;
                    } else {
                        $arrData[$vv->parent_task] = "Completed";
                        $arrData[$vv->sub_task] = "Completed";
                    }
                }
            }
            return $arrData;
        }

        function getPunchlistedItems($unitId = null) {
            $arrIds = array();
            if ($unitId) {
                $queryTask = $this->db->get_where($this->punchlistedTaskTable, array("unit_id" => $unitId, "status" => 2));
                if ($queryTask->num_rows() > 0) {
                    foreach ($queryTask->result() as $key => $value) {
                        $arrIds[$key] = $value->item_id;
                    }
                }
            }

            return $arrIds;
        }


        function renderPunchlistLogs($unitId = null) {
            $resultset = array();
            if ($unitId) {
                $this->db->order_by("created_at", "DESC");
                $queryLogs = $this->db->get_where($this->punchlistLogTable, array("unit_id" => $unitId));
                if ($queryLogs->num_rows() > 0) {
                    $data = array();
                    foreach ($queryLogs->result() as $key => $value) {
                        $tempName = $this->core_layout->getEmployeeData($value->user_id);
                        $tempName = (object)$tempName;
                        $value->user_name = (isset($tempName->display_name_1) && $tempName->display_name_1) ? $tempName->display_name_1 : "No assigned name";
                        $data[] = $value;
                    }
                    $resultset["response"] = true;
                    $resultset["data"] = $data;
                    $resultset["count"] = $queryLogs->num_rows();
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function renderPunchlistedItems($unitId = null) {
            $resultset = array();
            if ($unitId) {
                $arrData = array();
                $ids = $this->getPunchlistItems($unitId, array(), true);
                if ($ids) {
                    $this->db->from($this->itemTable);
                    $this->db->where_in("id", $ids);
                    $query = $this->db->get();
                    if ($query->num_rows() > 0) {
                        $arrIds = array();
                        foreach ($query->result() as $key => $value) {
                            if (!in_array($value->id, $arrIds)) {
                                $arrIds[] = $value->id;
                            }
                            if (!in_array($value->parent_id, $arrIds)) {
                                $arrIds[] = $value->parent_id;
                            }
                        }
                        if ($arrIds && count($arrIds) > 0) {
                            $this->db->from($this->itemTable);
                            $this->db->where_in("id", $arrIds);
                            $this->db->order_by("sort", "ASC");
                            $queryItems = $this->db->get();
                            if ($queryItems->num_rows() > 0) {
                                foreach ($queryItems->result() as $key => $value) {
                                    $tempData["id"] = $value->id;
                                    $tempData["type"] = (isset($value->parent_id) && $value->parent_id) ? "child" : "root";
                                    $tempData["parent"] = (isset($value->parent_id) && $value->parent_id) ? $value->parent_id : "#";
                                    $tempData["text"] = strtoupper($value->label);
                                    $tempData["a_attr"]["class"] = (isset($value->parent_id) && $value->parent_id) ? "punchlisted_node" : "parent_node";
                                    $arrData[] = $tempData;
                                }
                            }
                        }
                    }
                }

                $filter = array("status !=" => 2);
                $tempArrData = $this->getPunchlistTaskIds($unitId, $filter);
                $tempCount = $this->getPunchlistTaskCount($unitId);
                $resultset["response"] = true;
                $resultset["data"] = $arrData;
                $resultset["count"] = count($arrData);
                $resultset["punchlist_count"] = $tempCount;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function generateTodoTask($id = null) {
            $resultset = array();
            if ($id) {
                $sqlSelect = "b.unit_id, b.task_id, f.label as parent, e.label as child, c.due_date, c.extension_id, d.extension_date, c.status as contract_status, a.task_status as status";
                $this->db->select($sqlSelect);
                $this->db->from($this->taskTimelineTable . " a");
                $this->db->join($this->taskTable . " b", "b.id = a.parent_id");
                $this->db->join($this->contractTable . " c", "c.id = a.contract_id");
                $this->db->join($this->contractExtensionTable . " d", "d.contract_id = c.id", "LEFT");
                $this->db->join($this->itemTable . " e", "e.id = a.task_id");
                $this->db->join($this->itemTable . " f", "f.id = b.task_id");
                $this->db->where("b.unit_id", $id);
                $this->db->where("c.status !=", 3);
                $this->db->group_by("a.id");
                $this->db->order_by("b.task_id", "ASC");
                $queryTodo = $this->db->get();

                $arrIds = $this->getPunchlistTaskIds($id);
                if ($queryTodo->num_rows() > 0) {
                    $arrData = array();
                    foreach ($queryTodo->result() as $key => $value) {
                        $isPastDue = false;
                        $currentDate = date("Y-m-d");
                        $dueDate = date("Y-m-d", strtotime($value->due_date));
                        if ($value->extension_id !== "0") {
                            $dueDate = date("Y-m-d", strtotime($value->extension_date));
                        }
                        if ($currentDate > $dueDate) {
                            $isPastDue = true;
                        }

                        if (in_array($value->task_id, $arrIds)) {
                            $isPastDue = false;
                        }

                        if (($value->status !== 3 || $value->status !== 4) && $value->contract_status == 1 && $isPastDue == true) {
                            $arrData[2]["count"] = (isset($arrData[2]["count"]) && $arrData[2]["count"]) ? intval($arrData[2]["count"]) : 0;
                            $arrData[2]["data"][$value->task_id]["parent"] = $value->parent;
                            $arrData[2]["data"][$value->task_id]["child"][] = $value->child;
                            $arrData[2]["label"] = "BACKLOG";
                            $arrData[2]["font_color"] = "m--font-danger";
                            $arrData[2]["count"] += 1;
                        }
                        if (($value->status !== 3 || $value->status !== 4) && $value->contract_status == 1) {
                            if ($value->status == "0") {
                                $arrData[0]["count"] = (isset($arrData[0]["count"]) && $arrData[0]["count"]) ? intval($arrData[0]["count"]) : 0;
                                $arrData[0]["data"][$value->task_id]["parent"] = $value->parent;
                                $arrData[0]["data"][$value->task_id]["child"][] = $value->child;
                                $arrData[0]["label"] = "AWAITING";
                                $arrData[0]["font_color"] = "";
                                $arrData[0]["count"] += 1;
                            } else if ($value->status == "1") {
                                $arrData[1]["count"] = (isset($arrData[1]["count"]) && $arrData[1]["count"]) ? intval($arrData[1]["count"]) : 0;
                                $arrData[1]["data"][$value->task_id]["parent"] = $value->parent;
                                $arrData[1]["data"][$value->task_id]["child"][] = $value->child;
                                $arrData[1]["label"] = "IN PROGRESS";
                                $arrData[1]["font_color"] = "m--font-brand";
                                $arrData[1]["count"] += 1;
                            } else if ($value->status == "2") {
                                $arrData[3]["count"] = (isset($arrData[3]["count"]) && $arrData[3]["count"]) ? intval($arrData[3]["count"]) : 0;
                                $arrData[3]["data"][$value->task_id]["parent"] = $value->parent;
                                $arrData[3]["data"][$value->task_id]["child"][] = $value->child;
                                $arrData[3]["label"] = "DEFERRED";
                                $arrData[3]["font_color"] = "m--font-success";
                                $arrData[3]["count"] += 1;
                            }
                        }
                    }

                    if ($arrData && count($arrData) > 0) {
                        $resultset["response"] = true;
                        $resultset["rows"] = $arrData;
                        $resultset["row_count"] = count($arrData);
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

        function generatePastDueItems($id = null) {
            $arrData = $this->getPastDueItems($id);
            if ($arrData && count($arrData) > 0) {
                $pastDue = array();
                foreach ($arrData as $key => $rs) {
                    $rowData = array();
                    $isExtended = false;
                    $dueDate = $rs->due_date;
                    $tempDate = $rs->task_due;
                    $currentDate = date("Y-m-d");
                    if ($rs->extension_id !== "0") {
                        $tempDate = $rs->extension_date;
                        $isExtended = true;
                    }

                    if ($currentDate >= $dueDate) {
                        $rowData = array();
                        $rowData["label"] = $rs->label;
                        $rowData["due_date"] = $rs->due_date;
                        $rowData["is_extended"] = false;
                        $pastDue[] = $rowData;
                    }

                    if (count($rs->child_task) > 0 && $rs->child_task) {
                        foreach ($rs->child_task as $kk => $vv) {
                            if ($currentDate >= $tempDate) {
                                $rowData = array();
                                $rowData["label"] = $vv->label;
                                $rowData["due_date"] = $tempDate;
                                $rowData["is_extended"] = $isExtended;
                                $pastDue[] = $rowData;
                            }
                        }
                    }
                }
                if (count($pastDue) > 0 && $pastDue) {
                    $resultset["response"] = true;
                    $resultset["rows"] = $pastDue;
                    $resultset["row_count"] = count($pastDue);
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getPastDueItems($unitId = null) {
            $arrData = array();
            if ($unitId) {
                $sqlSelect = "d.id, f.label, a.due_date as task_due, a.extension_id, ";
                $sqlSelect .= "b.extension_date, GROUP_CONCAT(DISTINCT(e.task_id)) as task_id";
                $this->db->select($sqlSelect);
                $this->db->from($this->contractTable . " a");
                $this->db->join($this->contractExtensionTable . " b", "b.contract_id = a.id", "LEFT");
                $this->db->join($this->contractUnitTable . " c", "c.contract_id = a.id");
                $this->db->join($this->taskTable . " d", "d.id = c.task_id AND (d.status != 3 AND d.status != 4)");
                $this->db->join($this->taskTimelineTable . " e", "e.parent_id = d.id AND (e.task_status != 3 AND e.task_status != 4)", "LEFT");
                $this->db->join($this->itemTable . " f", "f.id = d.task_id");
                $this->db->where("d.unit_id", $unitId);
                $this->db->where("a.status !=", 3);
                $this->db->group_by("a.id");
                $this->db->order_by("d.id", "ASC");
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $key => $value) {
                        $childTask = array();
                        $taskIds = explode(",", $value->task_id);
                        if ($taskIds && count($taskIds) > 0) {
                            $this->db->select("id, label");
                            $this->db->from($this->itemTable);
                            $this->db->where_in("id", $taskIds);
                            $queryTask = $this->db->get();
                            if ($queryTask->num_rows() > 0) {
                                $childTask = $queryTask->result();
                            }
                        }
                        $value->child_task = $childTask;
                        unset($value->task_id);
                        $arrData[$key] = $value;
                    }
                }
            }

            return $arrData;
        }

        function getTaskDetails($form) {
            return $form;
        }

        function getCheckListProjectId($form) {
            $this->db->select("project_id");
            $this->db->where("id", $form["id"]);
            $project_id = $this->db->get("gccpms.sf_checklist_item")->row("project_id");
            return array("project_id" => $project_id);
        }

        function editTaskItem($id) {
            $post = $this->input->post();
            $this->db->where("id", $id);
            return $this->db->update($this->itemTable, $post);
        }

        function archiveItem($id) {
            $this->db->trans_begin();

            $this->db->where("id", $id);
            $this->db->set("status", 0);
            $this->db->update($this->itemTable);

            $this->core_layout->insertArchiveLog($this->itemTable, $id);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->db->trans_commit();
                return true;
            }
        }

        function addChecklistItem() {
            $post = $this->input->post();
            $insert = $this->db->insert($this->checklistItemTable, $post);
            return $insert;
        }

        function editChecklistItem($id) {
            $post = $this->input->post();
            $this->db->where("id", $id);
            return $this->db->update($this->checklistItemTable, $post);
        }

        function archiveChecklistItem($id) {
            $this->db->trans_begin();

            $this->db->where("id", $id);
            $this->db->set("status", 0);
            $this->db->update($this->checklistItemTable);

            $this->core_layout->insertArchiveLog($this->checklistItemTable, $id);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->db->trans_commit();
                return true;
            }
        }

        function getInitModalData($form) {
            $arrData = array();
            $checklist_id = $form["id"]; // checklist id
            $id = $this->db->where("checklist_id", $checklist_id)
                ->get($this->checklistPrivilegeTable)->row("id"); // checklist privilege id

            $this->db->reset_query();

            $fields = "priv_rates.*, items.id items_id, items.label, " .
                "items.parent_id, rates.tariff rates_tariff, uom.uom_desc rates_unit";
            $this->db->select($fields);
            $this->db->join($this->itemTable . " items", "items.id = priv_rates.item_id", "INNER");
            $this->db->join($this->itemRatesTable . " rates", "rates.item_id = priv_rates.item_id and rates.approved_status=1", "LEFT");
            $this->db->join($this->uomTable . " uom", "uom.id = rates.unit", "LEFT");
            $this->db->where("priv_rates.privilege_id", $id);
            $query = $this->db->get($this->checklistRatesTable . " priv_rates");

            foreach ($query->result() as $key => $rs) {
                $child = array();
                $child["id"] = $rs->item_id;
                $child["type"] = ($rs->parent_id) ? "child" : "root";
                $child["parent"] = ($rs->parent_id) ? $rs->parent_id : "#";

                if (intval($rs->parent_id) || $this->hasNoChildren($rs->item_id)) {
                    $rates_tariff = number_format(($rs->rates_tariff ? $rs->rates_tariff : 0), 2, ".", ",");
                    $rates_unit = strtoupper($rs->rates_unit ? " / " . $rs->rates_unit : "");

                    $tariff = number_format(($rs->tariff ? $rs->tariff : 0), 2, ".", ",");
                    $unit = strtoupper($rs->unit ? " / " . $rs->unit : "");

                    $privilege_id = $rs->privilege_id;

                    $new["tariff"] = str_replace(",", "", $rates_tariff); // new rate
                    $new["unit"] = strtoupper($rs->rates_unit);
                    $old["tariff"] = str_replace(",", "", $tariff);
                    $old["unit"] = strtoupper($rs->unit);

                    $child["text"] = "<span class='m--font-bolder'>" . $rs->label . " </span>" .
                        "<span class='m--font-boldest'>(&#8369; " . $tariff . $unit . ")</span>";
                } else {
                    $child["text"] = "<span class='m--font-bolder'>" . $rs->label . "</span>";
                }

                array_push($arrData, $child);
            }

            foreach ($arrData as $key => $item) {
                $flag = $this->checkParentExist($item["parent"]);
                if ($flag) {
                    $parent = json_decode($this->getSequenceParent($item["parent"]), true);
                    $p_id = (int)$parent["id"];

                    $filter = array_filter($arrData, function ($value) use ($p_id) {
                        return (int)$value["id"] === $p_id;
                    });

                    if (count($filter) <= 0) {
                        $parent["text"] = "<span class='m--font-bolder'>" . $parent["text"] . "</span>";
                        array_push($arrData, $parent);
                    }
                }
            }

            $parents = array_filter($arrData, function ($value) {
                return intval($value["parent"]) <= 0;
            });

            $index = 0;
            foreach ($parents as $key => $parent) {
                if (intval($index) === 0) {
                    $arrData[$key]["state"]["opened"] = true;
                    $arrData[$key]["state"]["selected"] = false;
                }
                $index++;
            }

            return array("tree" => $arrData, "parents" => $parents);
        }

        private function hasNoChildren($id) {
            $this->db->where("parent_id", $id);
            $ctr = $this->db->count_all_results($this->itemTable);
            return $ctr <= 0 ? true : false;
        }

        function updateChecklistRate() {
            $post = $this->input->post();
            $checklist_id = $post["checklist_id"];;
            $cat_id = $post["category_id"];
            $category = $this->db->get_where("gccpms.sf_item_rates_category", array("id" => $cat_id))->row("category");
            $checklist_priv_id = $this->db->get_where($this->checklistPrivilegeTable, array("checklist_id" => $checklist_id))->row("id");
            $post["category"] = $category;
            $id = $post["id"];
            unset($post["checklist_id"], $post["category_id"], $post["id"]);

            /* GET CURRENT RATE BEFORE UPDATE */
            $prev_rate = $this->db->select("tariff, unit, category")->where("id", $id)->get($this->checklistRatesTable)->row();
            /* GET CURRENT RATE BEFORE UPDATE */

            $this->db->trans_begin();

            $this->db->where("id", $id);
            $this->db->update($this->checklistRatesTable, $post);

            /* SAVE UPDATES TO HISTORY */
            $data = array(
                "checklist_priv_id" => $checklist_priv_id,
                "priv_rates_id" => $id,
                "prev_tariff" => $prev_rate->tariff,
                "prev_unit" => strtoupper($prev_rate->unit),
                "prev_category" => strtoupper($prev_rate->category),
                "new_tariff" => $post["tariff"],
                "new_unit" => strtoupper($post["unit"]),
                "new_category" => strtoupper($category),
                "applied_by" => $this->user_data["emp_id"],
            );
            $this->db->insert("gccpms.sf_checklist_rates_history", $data);
            /* SAVE UPDATES TO HISTORY */

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array("success" => false, "data" => array("tree" => array()));
            } else {
                $this->db->trans_commit();
                return array("success" => true, "data" => $this->getInitModalData(array("id" => $checklist_id)));
            }
        }

        function getRateCardUpdates($checklist_id) {
            $checklist_privilege_id = $this->db->where("checklist_id", $checklist_id)->get($this->checklistPrivilegeTable)->row("id");

            $this->db->reset_query();

            $fields = "`priv_rates`.*, priv_rates.category, `items`.`id` `items_id`, `items`.`label`, 
                    `items`.`parent_id`, `rates`.`id` `rates_id`, `rates`.`tariff` `rates_tariff`, `uom`.`uom_desc` `rates_unit`, 
                    rates.category_id rates_category_id, rcat1.category rates_category,
                    rates.approved_status";
            $this->db->select($fields);
            $this->db->join($this->itemTable . " items", "items.id = priv_rates.item_id", "INNER");
            $this->db->join($this->itemRatesTable . " rates", "rates.item_id = priv_rates.item_id and rates.approved_status = 1", "LEFT");
            $this->db->join($this->uomTable . " uom", "uom.id = rates.unit", "LEFT");
            $this->db->join("gccpms.sf_item_rates_category rcat1", "rcat1.id = rates.category_id", "LEFT");
            $this->db->where("priv_rates.privilege_id", $checklist_privilege_id);
            $query = $this->db->get($this->checklistRatesTable . " priv_rates");

            $resultSet = array();

            $rows = $query->result();
            $data = array();

            foreach ($rows as $row) {
                $row->parent = $this->db->get_where($this->itemTable, array("id" => $row->parent_id))->row("label");
                if (!empty($row->rates_id)) {
                    if ((strtoupper($row->unit) !== strtoupper($row->rates_unit)) ||
                        (doubleval($row->tariff) !== doubleval($row->rates_tariff)) ||
                        (strtolower($row->category) !== strtolower($row->rates_category))) {
                        array_push($data, $row);
                    }
                }
            }

            $resultSet["data"] = $data;
            return $resultSet;
        }

        function activityConfig($config = array()) {
            if ($config && count($config)) {
                $this->tempConfig = $config;
            }
            return clone $this;
        }

        function getActivityConfig() {
            return ($this->tempConfig) ? $this->tempConfig : false;
        }

        function setActivityLog($message = null) {
            if ($message) {
                $tempConf = $this->tempConfig;
                $tempData = array();
                if ($tempConf && count($tempConf) > 0) {
                    foreach ($tempConf as $key => $value) {
                        if ($key == "unit_id" && $value) {
                            $tempData[$key] = $value;
                        }
                        if ($key == "timeline_id" && $value) {
                            $tempData[$key] = $value;
                        }
                        if ($key == "contract_id" && $value) {
                            $tempData[$key] = $value;
                        }
                    }
                }

                if ($tempData && count($tempData) > 0) {
                    $tempData["user_id"] = $this->core_layout->getCurrentEmployeeId();
                    $tempData["description"] = $message;
                    $inserted = $this->db->insert($this->taskTimelineActivityTable, $tempData);
                    if ($inserted) {
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

        function getModalChecklistQty($form = array()) {
            $resultset = array();
            if (isset($form) && $form) {
                $this->db->select("a.id, a.label as task, b.label as parent_task");
                $this->db->from($this->itemTable . " a");
                $this->db->join($this->itemTable . " b", "b.id = a.parent_id");
                $this->db->where("a.id", $form["id"]);
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $resultset["response"] = true;
                    $resultset["title"] = $row->parent_task;
                    $resultset["current_task"] = $row->task;
                    $resultset["item_id"] = $row->id;
                    $resultset["temp_id"] = $form["temp_id"];
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getModalExistingChecklistQty($form = array()) {
            $resultset = array();
            if (isset($form) && $form) {
                $this->db->select("a.id, a.qty, a.remarks, b.label as task, c.label as parent_task");
                $this->db->from($this->checklistItemQtyTable . " a");
                $this->db->join($this->itemTable . " b", "b.id = a.item_id");
                $this->db->join($this->itemTable . " c", "c.id = b.parent_id");
                $this->db->where("a.id", $form["id"]);
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $resultset["response"] = true;
                    $resultset["title"] = $row->parent_task;
                    $resultset["current_task"] = $row->task;
                    $resultset["id"] = $row->id;
                    $resultset["qty"] = $row->qty;
                    $resultset["remarks"] = (isset($row->remarks) && $row->remarks) ? $row->remarks : "";
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getModalApprovalChecklistQty($form = array()) {
            $resultset = array();
            if (isset($form) && $form) {
                $this->db->select("a.id, a.qty, a.remarks, b.label as task, c.label as parent_task");
                $this->db->from($this->checklistItemQtyTable . " a");
                $this->db->join($this->itemTable . " b", "b.id = a.item_id");
                $this->db->join($this->itemTable . " c", "c.id = b.parent_id");
                $this->db->where("a.id", $form["id"]);
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $resultset["response"] = true;
                    $resultset["current_task"] = $row->task;
                    $resultset["id"] = $row->id;
                    $resultset["status"] = $form["label"];
                    $resultset["title"] = ($form["label"] == "approve") ? "Approval" : "Decline";
                    $resultset["approval_status"] = ($form["label"] == "approve") ? "1" : "2";
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getModalChecklistHistory($form = array()) {
            $resultset = array();
            if (isset($form) && $form) {
                $this->db->select("a.id, a.label as task, b.label as parent_task");
                $this->db->from($this->itemTable . " a");
                $this->db->join($this->itemTable . " b", "b.id = a.parent_id");
                $this->db->where("a.id", $form["temp_id"]);
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $resultset["title"] = $row->parent_task;
                    $resultset["current_task"] = $row->task;
                }
            }
            return $resultset;
        }

        function clearTempRequestedQty($id = null) {
            $resultset = array();
            if ($id) {
                $session = $this->user_data;
                $where = array();
                $where["checklist_id"] = $id;
                $where["user_id"] = $session["emp_id"];

                $deleted = $this->db->delete($this->checklistTempQtyTable, $where);
                $resultset["response"] = true;
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getChecklistData($id = null) {
            $resultset = array();
            if ($id) {
                $where = array();
                $where["id"] = $id;
                $query = $this->db->get_where($this->checklistItemTable, $where);
                if ($query->num_rows() == 1) {
                    $resultset["response"] = true;
                    $resultset["row"] = $query->row();
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }
    }