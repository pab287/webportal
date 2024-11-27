<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Shift_schedule_model extends CI_Model {
        protected $shiftScheduleTable = "gcctimeutility.shift_schedule";
        protected $shiftScheduleCalendarTable = "gcctimeutility.shift_schedule_calendar";
        protected $shiftScheduleListTable = "gcctimeutility.shift_schedule_list";
        protected $shiftScheduleResourceTable = "gcctimeutility.shift_schedule_resource";
        protected $shiftPersonnelTable = "gcctimeutility.personnel";
        protected $shiftLateTable = "gcctimeutility.lates";
        protected $shiftUndertimeTable = "gcctimeutility.undertime";

        private $customShiftTable = "custom_personnel_shift";

        function __construct() {
            parent::__construct();
            $this->load->model("datatable_model", "dt_model");
        }

        function getListLate() {
            $late = $this->db->get_where($this->shiftLateTable, array("status" => 1));
            if ($late->num_rows() > 0) {
                return $late->result();
            } else {
                return false;
            }
        }

        function getListUndertime() {
            $late = $this->db->get_where($this->shiftUndertimeTable, array("status" => 1));
            if ($late->num_rows() > 0) {
                return $late->result();
            } else {
                return false;
            }
        }

        function getShiftScheduleDataList() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("id", "name", "description", "weekday", "am_start", "am_end", "pm_start", "pm_end", "is_active");
                $dir = $post["order"][0]["dir"];
                $order = $columns[$post["order"][0]["column"]];
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtShiftSchedule = $this->dt_model->dataTable();
                $dtShiftSchedule->setTable($this->shiftScheduleListTable);
                $dtShiftSchedule->setParameterFields($columns);

                $totalData = $dtShiftSchedule->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtShiftSchedule->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtShiftSchedule->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtShiftSchedule->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['name'] = $pst->name;
                        $nestedData['description'] = $pst->description;
                        $nestedData['weekday'] = $pst->weekday;

                        $amStart = date("H:i", strtotime($pst->am_start));
                        $amEnd = date("H:i", strtotime($pst->am_end));
                        $pmStart = date("H:i", strtotime($pst->pm_start));
                        $pmEnd = date("H:i", strtotime($pst->pm_end));

                        $nestedData['am_start'] = $amStart;
                        $nestedData['am_end'] = $amEnd;
                        $nestedData['pm_start'] = $pmStart;
                        $nestedData['pm_end'] = $pmEnd;
                        $nestedData['is_active'] = $pst->is_active;
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

        function getShiftScheduleList() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("id", "name", "description", "is_active");
                $dir = $post["order"][0]["dir"];
                $order = $columns[$post["order"][0]["column"]];
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtShiftSchedule = $this->dt_model->dataTable();
                $dtShiftSchedule->setTable($this->shiftScheduleTable);
                $dtShiftSchedule->setParameterFields($columns);

                $totalData = $dtShiftSchedule->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtShiftSchedule->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtShiftSchedule->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtShiftSchedule->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['name'] = $pst->name;
                        $nestedData['description'] = $pst->description;
                        $nestedData['is_active'] = $pst->is_active;
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

        function addShiftSchedule() {
            $post = $this->input->post();
            $result = array();
            if ($post) {
                $shiftName = $this->checkShiftName($post);
                if ($shiftName) {
                    $insert = $this->db->insert($this->shiftScheduleTable, $post);
                    if ($insert) {
                        $result["response"] = true;
                        $result["toastr_msg"] = "Shift detail has been saved.";
                    } else {
                        $result["response"] = false;
                        $result["toastr_msg"] = "Error in saving shift detail!";
                    }
                } else {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Shift detail name already exist!";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for saving!";
            }

            return $result;
        }

        function editShiftSchedule() {
            $post = $this->input->post();
            $result = array();
            if ($post) {
                $shiftName = $this->checkShiftName($post);
                if ($shiftName) {
                    $where = array("id" => $post["id"]);
                    unset($post["csrf_token"], $post["id"], $post["current_name"]);
                    $updated = $this->db->update($this->shiftScheduleTable, $post, $where);
                    if ($updated) {
                        $result["response"] = true;
                        $result["toastr_msg"] = "Shift detail has been updated.";
                    } else {
                        $result["response"] = false;
                        $result["toastr_msg"] = "Error in updating shift detail!";
                    }
                } else {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Shift detail name already exist!";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for saving!";
            }

            return $result;
        }

        function addShiftScheduleData() {
            $post = $this->input->post();
            $result = array();
            if ($post) {
                $shiftName = $this->checkShiftDataName($post);
                if ($shiftName) {
                    $insert = $this->db->insert($this->shiftScheduleListTable, $post);
                    if ($insert) {
                        $result["response"] = true;
                        $result["toastr_msg"] = "Shift data has been saved.";
                    } else {
                        $result["response"] = false;
                        $result["toastr_msg"] = "Error in saving shift data!";
                    }
                } else {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Shift data name already exist!";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for saving!";
            }

            return $result;
        }

        function getShiftScheduleItem() {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"]) && $post["id"]) {
                $query = $this->db->get_where($this->shiftScheduleListTable, $post);
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $row->am_start = date("H:i", strtotime($row->am_start));
                    $row->am_end = date("H:i", strtotime($row->am_end));
                    $row->pm_start = date("H:i", strtotime($row->pm_start));
                    $row->pm_end = date("H:i", strtotime($row->pm_end));


                    $resultset["data"] = $row;
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                    $result["toastr_msg"] = "No available data!";
                }
            } else {
                $resultset["response"] = false;
                $result["toastr_msg"] = "No sent data!";
            }

            return $resultset;
        }

        function deselectPersonnel() {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"], $post["shift_id"]) && ($post["id"] && $post["shift_id"])) {
                $where = array();
                $where["id"] = $post["id"];

                $data = array();
                $data["shift_id"] = 0;

                $update = $this->db->update($this->shiftPersonnelTable, $data, $where);
                if ($update) {
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function selectPersonnel() {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"], $post["shift_id"]) && ($post["id"] && $post["shift_id"])) {
                $where = array();
                $where["id"] = $post["id"];

                $data = array();
                $data["shift_id"] = $post["shift_id"];

                $update = $this->db->update($this->shiftPersonnelTable, $data, $where);
                if ($update) {
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getEmployeeShiftData() {
            $resultset = array();
            $data = array();
            $post = $this->input->post();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $arrIds = array(0, $post["id"]);
                $this->db->from($this->shiftPersonnelTable);
                $this->db->where_in("shift_id", $arrIds);
                $personnel = $this->db->get();
                if ($personnel->num_rows() > 0) {
                    foreach ($personnel->result() as $rs) {
                        $row = array();
                        $action = "";
                        $personnel_id = $rs->id;
                        $shiftId = $rs->shift_id;
                        if ($shiftId == 0) {
                            $action = "<button type='button' class='btn btn-success btn-shift_select btnSelect' data-id='{$personnel_id}'><i class='la la-plus-circle'></i> Select</button>";
                        } else {
                            $action = "<button type='button' class='btn btn-danger btn-shift_deselect btnDeselect' data-id='{$personnel_id}'><i class='la la-minus-circle'></i> Deselect</button>";
                        }

                        $row["chkbox"] = "<input type='checkbox' class='checkSingle' data-id='{$personnel_id}' name='{$personnel_id}' />";
                        $row["name"] = $rs->name;
                        $row["action"] = $action;
                        $data[] = $row;
                    }
                }
            }
            $resultset["data"] = $data;
            return $resultset;
        }

        function getAssigned($id = null) {
            $resultset = array();
            $data = array();
            if ($id) {
                $arrIds = array(0, $id);

                $this->db->from($this->shiftPersonnelTable);
                $this->db->where_in("shift_id", $arrIds);
                $personnel = $this->db->get();
                if ($personnel->num_rows() > 0) {
                    foreach ($personnel->result() as $rs) {
                        $row = array();

                        $shiftId = $rs->shift_id;
                        if ($shiftId != 0) {
                            $row["name"] = $rs->name;
                            $data[] = $row;
                        }
                    }
                }
            }
            $resultset["data"] = $data;
            return $resultset;
        }

        function getShiftSchedule($id) {
            $resultset = array();
            if ($id) {
                $this->db->from($this->shiftScheduleTable);
                $this->db->where("id", $id);
                $this->db->where("status", 1);

                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $resultset["row"] = $row;
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                    $result["toastr_msg"] = "No available data!";
                }
            } else {
                $resultset["response"] = false;
                $result["toastr_msg"] = "No sent data!";
            }

            return $resultset;
        }

        function getShiftScheduleData() {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"]) && $post["id"]) {
                $jsonData = $this->shiftScheduleJson();
                $arrIds = $this->getShiftResource($post["id"]);

                $this->db->select("a.*, b.late_id, b.undertime_id");
                $this->db->from("{$this->shiftScheduleTable} a");
                $this->db->join("{$this->shiftScheduleResourceTable} b", "b.shift_id = a.id", "left");
                $this->db->where("a.id", $post["id"]);

                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $row->is_active = ($row->is_active == 1) ? "Active" : "Inactive";

                    $resultset["data"] = $row;
                    $resultset["json_tree"] = $jsonData;
                    $resultset["shift_resource"] = $arrIds;
                    $resultset["response"] = true;
                } else {
                    $resultset["response"] = false;
                    $result["toastr_msg"] = "No available data!";
                }
            } else {
                $resultset["response"] = false;
                $result["toastr_msg"] = "No sent data!";
            }

            return $resultset;
        }

        function getShiftResource($id = null) {
            $ids = array();
            if ($id) {
                $resource = $this->db->get_where($this->shiftScheduleResourceTable, array("shift_id" => $id));
                if ($resource->num_rows() == 1) {
                    $row = $resource->row();
                    $ids = unserialize($row->shift_resource);
                }
            }

            return $ids;
        }

        function getShiftScheduleDaily() {
            $arrData = array();
            $this->db->where("is_active", 1);
            $query = $this->db->get($this->shiftScheduleListTable);

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $row = array();
                    $row["id"] = $rs->id;
                    $row["description"] = $rs->description;

                    $weekday = $rs->weekday;
                    $arrData[$rs->weekday][] = $row;
                }
            }
            return $arrData;
        }

        function shiftScheduleJson() {
            $arrData = array();
            $daily = $this->getShiftScheduleDaily();
            if ($daily) {
                $arrDays = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
                foreach ($arrDays as $day) {
                    if (isset($daily[$day]) && $daily[$day]) {
                        foreach ($daily[$day] as $dd) {
                            $data = array();
                            $data["id"] = $dd["id"];
                            $data["text"] = $dd["description"];

                            $arrData[$day][] = $data;
                        }
                    }
                }
            }

            return $arrData;
        }

        function saveShiftSchedule() {
            $post = $this->input->post();
            $resultset = array();
            $resultset["eid"] = array();
            $arrDays = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");

            if (isset($post["nodes"], $post["id"]) && ($post["nodes"] && $post["id"])) {
                $nodes = json_decode($post["nodes"], true);
                $late = (isset($post["late_id"]) && $post["late_id"]) ? $post["late_id"] : 1;
                $undertime = (isset($post["undertime_id"]) && $post["undertime_id"]) ? $post["undertime_id"] : 1;

                if ($nodes) {
                    $ids = array();
                    foreach ($arrDays as $day) {
                        if (isset($nodes[$day]) && $nodes[$day]) {
                            $currentNode = $nodes[$day];
                            foreach ($currentNode as $node) {
                                $selected = $node["state"]["selected"];
                                if ($selected) {
                                    $ids[] = $node["id"];
                                }
                            }
                        }
                    }
                    $resource_ids = serialize($ids);
                    $query = $this->db->get_where($this->shiftScheduleResourceTable, array("shift_id" => $post["id"]));
                    if ($query->num_rows() == 1) {
                        $row = $query->row();
                        $where = array();
                        $where["id"] = $row->id;

                        $data = array();
                        $data["shift_resource"] = $resource_ids;
                        $data["late_id"] = $late;
                        $data["undertime_id"] = $undertime;

                        $update = $this->db->update($this->shiftScheduleResourceTable, $data, $where);
                        if ($update) {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "Shift Schedule has been updated.";
                            $resultset["eid"] = $this->checkHasTimesheetAffectedWithShiftUpdate($post["id"]);
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Failed to update shift schedule!";
                        }
                    } else {
                        $data = array();
                        $data["shift_id"] = $post["id"];
                        $data["shift_resource"] = $resource_ids;
                        $data["late_id"] = $late;
                        $data["undertime_id"] = $undertime;

                        $saved = $this->db->insert($this->shiftScheduleResourceTable, $data);
                        if ($saved) {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "Shift Schedule has been saved.";
                        } else {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "Failed to save shift schedule!";
                        }
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "No available data!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        private function checkHasTimesheetAffectedWithShiftUpdate($shift_id) {
            $employees =  $this->db
                ->select("personnel.shift_id, ts.emp_id, emp.lastname, emp.firstname")
                ->join("gcctimeutility.personnel personnel", "personnel.biometricno = emp.biometricno", "INNER")
                ->join("gcctimeutility.timesheet ts", "ts.emp_id = emp.id", "INNER")
                ->where("personnel.shift_id", $shift_id)
                ->where("ts.verified", 0)
                ->group_by("emp.id")
                ->get("gccmaster.tblemployees emp")
                ->result();

            return array_map(function($employee) {
                return $employee->emp_id;
            }, $employees);
        }

        private function checkShiftDataName($data = array()) {
            if ($data) {
                $query = $this->db->get_where($this->shiftScheduleListTable, array("name" => $data["name"]));
                if ($query->num_rows() == 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        private function checkShiftName($data = array()) {
            if ($data) {
                if (isset($data["current_name"], $data["name"]) && $data["current_name"] && $data["name"]) {
                    if ($data["name"] !== $data["current_name"]) {
                        $query = $this->db->get_where($this->shiftScheduleTable, array("name" => $data["name"]));
                        if ($query->num_rows() == 0) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return true;
                    }
                } else {
                    $query = $this->db->get_where($this->shiftScheduleTable, array("name" => $data["name"]));
                    if ($query->num_rows() == 0) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }

        function updateShiftSchedule() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post["id"]) && $post["id"]) {
                $where = array();
                $where["id"] = $post["id"];

                unset($post["id"]);

                $update = $this->db->update($this->shiftScheduleTable, $post, $where);
                if ($update) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Shift schedule has been updated.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update shift schedule!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        function updateShiftScheduleData() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post["id"]) && $post["id"]) {
                $where = array();
                $where["id"] = $post["id"];

                unset($post["id"]);

                $update = $this->db->update($this->shiftScheduleListTable, $post, $where);
                if ($update) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Shift schedule data has been updated.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update shift schedule data!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        function removeShiftScheduleData() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post["id"]) && $post["id"]) {
                $remove = $this->db->delete($this->shiftScheduleTable, $post);
                if ($remove) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Shift schedule data has been removed.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to remove shift schedule data!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        function removeShiftScheduleDataItem() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post["id"]) && $post["id"]) {
                $remove = $this->db->delete($this->shiftScheduleListTable, $post);
                if ($remove) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Shift schedule data has been removed.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to remove shift schedule data!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data!";
            }

            return $resultset;
        }

        function getCalendarSchedules() {
            $data = array();
            $this->db->from($this->shiftScheduleCalendarTable);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $row = array();
                    $row["id"] = $rs->id;
                    $row["title"] = $rs->title;
                    $row["description"] = $rs->description;
                    $row["className"] = $rs->class_name;
                    $row["url"] = $rs->redirect_url;

                    $startDate = $rs->start_date;
                    $endDate = $rs->end_date;


                    $startTime = ($rs->start_time && $rs->start_time !== "00:00:00") ? $rs->start_time : "";
                    $endTime = ($rs->end_time && $rs->end_time !== "00:00:00") ? $rs->end_time : "";

                    $dateFrom = date_create($startDate);
                    $dateTo = date_create($endDate);

                    $dateDiff = date_diff($dateFrom, $dateTo);
                    $days = $dateDiff->days;
                    $row["days"] = $days;

                    if ($days && $days > 1) {
                        if ($startTime && $endTime) {
                            $toDate = $startDate;
                            $toEnd = $endDate;

                            while ($toDate <= $toEnd):
                                $fromStart = date("Y-m-d", strtotime($toDate));

                                $_fromDate = date("Y-m-d H:i", strtotime("{$fromStart} {$startTime}"));
                                $_toDate = date("Y-m-d H:i", strtotime("{$fromStart} {$endTime}"));
                                $toDate = date("Y-m-d", strtotime("+1 day", strtotime($toDate)));

                                $row["start"] = $_fromDate;
                                $row["end"] = $_toDate;
                                $data[] = $row;
                            endwhile;
                        } else {
                            $startDate = date("Y-m-d", strtotime($startDate));
                            $endDate = date("Y-m-d", strtotime("+1 day", strtotime($endDate)));

                            $row["start"] = $startDate;
                            $row["end"] = $endDate;
                            $data[] = $row;
                        }
                    } else if ($days == 1) {
                        $startDate = date("Y-m-d", strtotime($startDate));
                        $endDate = date("Y-m-d", strtotime("+1 day", strtotime($endDate)));

                        $row["start"] = $startDate;
                        $row["end"] = $endDate;
                        $data[] = $row;
                    } else {
                        $startDate = ($startTime) ? date("Y-m-d H:i", strtotime("{$startDate} {$startTime}")) : date("Y-m-d", strtotime($startDate));
                        $endDate = ($endTime) ? date("Y-m-d H:i", strtotime("{$endDate} {$endTime}")) : date("Y-m-d", strtotime($endDate));

                        $row["start"] = $startDate;
                        $row["end"] = $endDate;
                        $data[] = $row;
                    }

                }
            }

            return $data;
        }

        function setCalendarSchedule() {
            $post = $this->input->post();
            $resultset = array();
            if ($post) {
                $dateRangeFrom = explode(" ", $post["daterange_from"]);
                $dateRangeTo = explode(" ", $post["daterange_to"]);

                unset($post["csrf_token"], $post["daterange_from"], $post["daterange_to"]);

                $post["start_date"] = (isset($dateRangeFrom[0]) && $dateRangeFrom[0]) ? date("Y-m-d", strtotime($dateRangeFrom[0])) : "0000-00-00";
                $post["end_date"] = (isset($dateRangeTo[0]) && $dateRangeTo[0]) ? date("Y-m-d", strtotime($dateRangeTo[0])) : "0000-00-00";
                $post["start_time"] = (isset($dateRangeFrom[1]) && $dateRangeFrom[1]) ? date("H:i:s", strtotime($dateRangeFrom[1])) : "00:00:00";
                $post["end_time"] = (isset($dateRangeTo[1]) && $dateRangeTo[1]) ? date("H:i:s", strtotime($dateRangeTo[1])) : "00:00:00";
                $post["class_name"] = "m-fc-event--light m-fc-event--solid-danger";

                $insert = $this->db->insert($this->shiftScheduleCalendarTable, $post);
                if ($insert) {
                    $resultset["response"] = true;
                    $resultset["toast_msg"] = "Event schedule has been added";
                } else {
                    $resultset["response"] = false;
                    $resultset["toast_msg"] = "Failed to add event schedule!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toast_msg"] = "Error, no data found!";
            }

            return $resultset;
        }

        function getCalendarData() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post["id"]) && $post["id"]) {
                $currentDate = date("Y-m-d");

                $query = $this->db->get_where($this->shiftScheduleCalendarTable, $post);
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $dateStart = ($row->start_date) ? date("Y-m-d", strtotime($row->start_date)) : date("Y-m-d");
                    $dateEnd = ($row->end_date) ? date("Y-m-d", strtotime($row->end_date)) : date("Y-m-d");

                    $row->date_started = date("Y-m-d H:i", strtotime("{$dateStart} {$row->start_time}"));
                    $row->date_ended = date("Y-m-d H:i", strtotime("{$dateEnd} {$row->end_time}"));

                    if ($currentDate < $dateStart && $currentDate < $dateEnd) {
                        $row->event_editable = true;
                    } else {
                        $row->event_editable = false;
                    }

                    $resultset["response"] = true;
                    $resultset["data"] = $row;
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getCustomShiftSchedule() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("b.biometricno", "b.name", "a.weekday", "a.am_start", "a.am_end", "a.pm_start", "a.pm_end", "a.is_active", "a.id", "a.status", "a.access_type");

                $dir = $post["order"][0]["dir"];
                $order = $columns[$post["order"][0]["column"]];
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->customShiftTable);
                $dtTable->setTableAlias("a");

                $dtTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->shiftPersonnelTable] = "b";
                $joinTable["fields"][] = "b.id=a.personnel_id";
                $joinTable["field_loc"][] = "LEFT";

                $dtTable->setJoinTable($joinTable);
                $dtTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.status"] = 1;
                $parameters["a.access_type"] = 3;

                $dtTable->setWhereParameters($parameters);
                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['biometricno'] = $pst->biometricno;
                        $nestedData['name'] = $pst->name;
                        $nestedData['weekday'] = ucwords($pst->weekday);
                        $nestedData['am_start'] = $pst->am_start;
                        $nestedData['am_end'] = $pst->am_end;
                        $nestedData['pm_start'] = $pst->pm_start;
                        $nestedData['pm_end'] = $pst->pm_end;
                        $nestedData['is_active'] = $pst->is_active;
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

        function getPersonnelItems() {
            $resultset = array();
            $this->db->select("id, CONCAT(biometricno, ' | ', name) as text");
            $this->db->from($this->shiftPersonnelTable);
            $query = $this->db->get();

            $resultset["results"] = ($query->num_rows() > 0) ? $query->result() : array();

            return $resultset;
        }

        function addCustomShiftSchedule() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post) && $post) {
                if (isset($post["personnel_id"]) && $post["personnel_id"]) {
                    $post["access_type"] = 3;
                    $added = $this->db->insert($this->customShiftTable, $post);
                    if ($added) {
                        $resultset["response"] = true;
                        $resultset["toastr_msg"] = "Custom shift schedule has been added.";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Failed to set custom shift schedule!";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "No personnel assigned, please select a personnel first!";
                }

            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }
    }