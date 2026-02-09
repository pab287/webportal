<?php defined('BASEPATH') OR exit('No direct script access allowed');
    class Shift_management_model extends CI_Model {
        protected $absentTable = "gcctimeutility.absent";
        protected $attendanceTable = "gcctimeutility.attendance";
        protected $departmentTable = "gcctimeutility.department";
        protected $eventTable = "gcctimeutility.event";
        protected $shiftLateTable = "gcctimeutility.lates";
        protected $shiftPersonnelTable = "gcctimeutility.personnel";
        protected $shiftScheduleTable = "gcctimeutility.shift_schedule";
        protected $shiftScheduleListTable = "gcctimeutility.shift_schedule_list";
        protected $shiftScheduleResourceTable = "gcctimeutility.shift_schedule_resource";
        protected $shiftScheduleCalendarTable = "gcctimeutility.shift_schedule_calendar";
        protected $shiftUndertimeTable = "gcctimeutility.undertime";

        protected $eformsLoaTable = "gcceforms.loa";
        protected $eformsToTable = "gcceforms.travel_order";
        protected $eformsTpTable = "gcceforms.travel_personnel";
        protected $eformsTdTable = "gcceforms.travel_destination";

        public function __construct() {
            parent::__construct();
            $this->load->model("gcctime/attendance_model", "adm_attendance");

            date_default_timezone_set("Asia/Taipei");
        }

        protected function hasScheduledEvent($dateTime = null, $queryResult = null): bool {
            if (!$queryResult || $queryResult->num_rows() === 0 || !$dateTime) { return false; }
            $response = false;
            $dateToday = date('Y-m-d', strtotime($dateTime));
            $timeToday = date('H:i', strtotime($dateTime));
            foreach ($queryResult->result() as $result) {
                $startDate = $result->start_date ? date('Y-m-d', strtotime($result->start_date)) : '';
                $endDate = $result->end_date ? date('Y-m-d', strtotime($result->end_date)) : '';
                if (($startDate && $endDate) && ($dateToday >= $startDate && $dateToday <= $endDate)) {
                    $startTime = $result->start_time ? date('H:i', strtotime($result->start_time)) : '00:00';
                    $endTime = $result->end_time ? date('H:i', strtotime($result->end_time)) : '24:59';
                    if (($startTime && $endTime) && ($timeToday >= $startTime && $timeToday <= $endTime)) { $response = true; }
                    if ($result->start_time === '00:00:00' && $result->end_time === '00:00:00') { $response = true; }
                }
            }
            return $response;
        }

        public function getScheduledEvent($dateTime = null){
            $startOfTheMonth = date('Y-m-01');
            $endOfTheMonth = date('Y-m-t', strtotime($startOfTheMonth));
            $dateTime = $dateTime ? date('Y-m-d H:i', strtotime($dateTime)) : date('Y-m-d H:i');

            $query = $this->db->select('start_date, start_time, end_date, end_time')
                ->from($this->shiftScheduleCalendarTable)
                ->where('start_date >=', $startOfTheMonth)
                ->where('end_date <=', $endOfTheMonth)
                ->get();

            return $this->hasScheduledEvent($dateTime, $query);
        }

        public function getScheduledEventByDate($_startDate = null, $_endDate = null, $dateTime = null) {
            $dateStart = date('Y-m-d', strtotime($_startDate ?? date('Y-m-1')));
            $dateEnd = date('Y-m-d', strtotime($_endDate ?? date('Y-m-t')));
            $dateTime = date('Y-m-d H:i', strtotime($dateTime ?? date('Y-m-d H:i')));

            $query = $this->db
                ->from($this->shiftScheduleCalendarTable)
                ->where('start_date >=', $dateStart)
                ->where('end_date <=', $dateEnd)
                ->get();

            return $this->hasScheduledEvent($dateTime, $query);
        }

        public function getPersonnelAttendanceCount($biometric_id = null, $datetime = null) {
            $counter = 0;
            if ($biometric_id && $datetime) {
                $this->db->from($this->attendanceTable);
                $this->db->where("biometric_id", $biometric_id);
                $this->db->like("datetime", $datetime);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $counter = $query->num_rows();
                }
            }

            return intval($counter);
        }

        public function getPersonnelCount($biometric_id = null, $datetime = null, $current_meredien = null) {
            $counter = 0;
            if ($biometric_id && $datetime) {
                $this->db->from($this->attendanceTable);
                $this->db->where("biometric_id", $biometric_id);
                $this->db->like("datetime", $datetime);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    if ($current_meredien) {
                        $attCount = 0;
                        /***bug here @pab 041219 xx/xx adriane ** */
                        foreach ($query->result() as $rs) {
                            $ampm = date("A", strtotime($rs->datetime));
                            if ($ampm == $current_meredien) {
                                $attCount++;
                            }
                        }

                        if ($attCount == 1) {
                            $this->db->from($this->attendanceTable);
                            $this->db->where("biometric_id", $biometric_id);
                            $this->db->like("datetime", $datetime);
                            $query2 = $this->db->get();
                            $counterx = $query2->num_rows();
                            $counter = intval($counterx) % 2;
                        }
                        /***bug here @pab 041219 xx/xx adriane ** */
                    } else {
                        $counter = $query->num_rows();
                        $counter = intval($counter) % 2;
                    }
                }
            }

            return $counter;
        }

        public function getPersonnelAttendance($biometric_id = null, $datetime = null, $ampm = "AM") {
            $flag = false;
            if ($biometric_id && $datetime) {
                $this->db->from($this->attendanceTable);
                $this->db->where("biometric_id", $biometric_id);
                $this->db->like("datetime", $datetime);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $result = $query->result();
                    foreach ($result as $rs) {
                        $meredien = date("A", strtotime($rs->datetime));
                        if ($meredien == $ampm) { $flag = true; }
                    }
                }
            }
            return $flag;
        }

        function getPersonnelData($biometric_id = null) {
            if ($biometric_id) {
                $personnel = $this->db->get_where($this->shiftPersonnelTable, array("biometric_id" => $biometric_id));
                if ($personnel->num_rows() == 1) {
                    $row = $personnel->row();
                    return $row;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getShiftResource($shift = null) {
            if ($shift) {
                $resource = $this->db->get_where($this->shiftScheduleResourceTable, array("shift_id" => $shift));
                if ($resource->num_rows() == 1) {
                    $row = $resource->row();
                    $row->shift_resource = ($row->shift_resource) ? unserialize($row->shift_resource) : array();
                    return $row;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getPersonnelShift($biometric_id = null) {
            if ($biometric_id) {
                $arrWhere = array("shift_id !=" => 0, "is_active" => 1);
                $this->db->where($arrWhere);
                $this->db->group_start();
                $this->db->where("biometric_id", $biometric_id);
                $this->db->or_where("biometricno", $biometric_id);
                $this->db->group_end();
                $personnel = $this->db->get($this->shiftPersonnelTable);
                if ($personnel->num_rows() == 1) {
                    $row = $personnel->row();
                    return $row;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getDailyResource($id = null, $weekday = null) {
            if ($id && $weekday) {
                $query = $this->db->get_where($this->shiftScheduleListTable, array("id" => $id, "weekday" => $weekday, "is_active" => 1));
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    return $row;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getUndertime($id = null) {
            if ($id) {
                $late = $this->db->get_where($this->shiftUndertimeTable, array("id" => $id, "status" => 1));
                if ($late->num_rows() == 1) {
                    $row = $late->row();
                    return $row;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getLate($id = null) {
            if ($id) {
                $late = $this->db->get_where($this->shiftLateTable, array("id" => $id, "status" => 1));
                if ($late->num_rows() == 1) {
                    $row = $late->row();
                    return $row;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getAvailableLoaUndertime($id = null) {
            $resultset = array();
            $syncDate = $this->getLastSyncData();
            $lastSyncDate = date("Y-m-d", strtotime("-1 day", strtotime($syncDate)));
            $nowMeredian = date("A");

            $empData = $this->db->get_where("gccmaster.tblemployees", array("biometricno" => $id));
            if ($empData->num_rows() == 1) {
                $rowEmp = $empData->row();

                $this->db->order_by("id", "desc");
                $query = $this->db->get_where("gcceforms.loa", array("employee" => $rowEmp->id));
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $rs) {
                        if ($rs->type == 1) {
                            $dateTimeFF = date("Y-m-d", strtotime($rs->date_from));
                            $dateTimeTT = date("Y-m-d", strtotime($rs->date_to));

                            $dateTime = new DateTime($lastSyncDate);
                            $dateTimeFrom = new DateTime($dateTimeFF);
                            $dateTimeTo = new DateTime($dateTimeTT);

                            $dtStamp = $dateTime->getTimestamp();
                            $dtStampFrom = $dateTimeFrom->getTimestamp();
                            $dtStampTo = $dateTimeTo->getTimestamp();

                            if (($dtStamp >= $dtStampFrom) && ($dtStamp <= $dtStampTo)) {
                                $resultset["response"] = true;
                                $resultset["reference_no"] = $rs->reference_no;
                            }
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

        function getEmployeeLoa($id = null, $date = null) {
            if ($id && $date) {
                $_date = date("Y-m-d", strtotime($date));
                $dateFromx = date("Y-m-d H:i", strtotime($_date));
                $dateTox = date("Y-m-d H:i", strtotime("+1 day", strtotime($_date)));
                $dateTox = date("Y-m-d H:i", strtotime("-1 minute", strtotime($dateTox)));

                $this->db->from($this->eformsLoaTable);
                $this->db->where("employee", $id);
                $this->db->where("date_from >=", $dateFromx);
                $this->db->where("date_to <=", $dateTox);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    return $query->row();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getEmployeeTo($id = null, $date = null) {
            if ($id && $date) {
                $_date = date("Y-m-d", strtotime($date));
                $_filterDate = date("Y-m", strtotime($date));

                /*** $dateFromx = date("Y-m-d H:i", strtotime($_date));
                 * $dateTox = date("Y-m-d H:i", strtotime("+1 day", strtotime($_date)));
                 * $dateTox = date("Y-m-d H:i", strtotime("-1 minute", strtotime($dateTox))); ***/

                $this->db->select("a.reference_no, c.date_from, c.date_to");
                $this->db->from("{$this->eformsToTable} a");
                $this->db->join("{$this->eformsTpTable} b", "b.travel_order_id = a.id", "left");
                $this->db->join("{$this->eformsTdTable} c", "c.travel_order_id = a.id", "left");
                $this->db->where("b.employee_id", $id);
                $this->db->group_start();
                $this->db->like("c.date_from", $_filterDate, "both");
                $this->db->like("c.date_to", $_filterDate, "both");
                $this->db->group_end();

                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $rowData = array();
                    foreach ($query->result() as $rs) {
                        $fromDate = date("Y-m-d", strtotime($rs->date_from));
                        $toDate = date("Y-m-d", strtotime($rs->date_to));

                        if (($_date >= $fromDate) && ($_date <= $toDate)) {
                            $rowData = $rs;
                        }
                    }

                    if ($rowData) {
                        return $rowData;
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

        function getEmployeeToDriver($id = null, $date = null) {
            if ($id && $date) {
                $_date = date("Y-m-d", strtotime($date));
                $_filterDate = date("Y-m", strtotime($date));

                $this->db->select("a.reference_no, b.date_from, b.date_to");
                $this->db->from("{$this->eformsToTable} a");
                $this->db->join("{$this->eformsTdTable} b", "b.travel_order_id = a.id", "left");
                $this->db->where("a.driver_id", $id);
                $this->db->group_start();
                $this->db->like("b.date_from", $_filterDate, "both");
                $this->db->like("b.date_to", $_filterDate, "both");
                $this->db->group_end();

                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $rowData = array();
                    foreach ($query->result() as $rs) {
                        $fromDate = date("Y-m-d", strtotime($rs->date_from));
                        $toDate = date("Y-m-d", strtotime($rs->date_to));

                        if (($date >= $fromDate) && ($date <= $toDate)) {
                            $rowData = $rs;
                        }
                    }

                    if ($rowData) {
                        return $rowData;
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

        function getAvailableLoaByDate($id = null, $date = null, $ampm = null) {
            $resultset = array();
            $syncDate = ($date) ? $date : $this->getLastSyncData();
            $lastSyncDate = date("Y-m-d", strtotime($syncDate));

            $nowMeredian = date("A");

            $empData = $this->db->get_where("gccmaster.tblemployees", array("biometricno" => $id));
            if ($empData->num_rows() == 1) {
                $rowEmp = $empData->row();
                $employeeLoa = $this->getEmployeeLoa($rowEmp->id, $lastSyncDate);
                if ($employeeLoa) {
                    if ($employeeLoa->type == 2 || $employeeLoa->type == 1) {
                        $ampmx = date("A", strtotime($employeeLoa->date_from));
                        if ($ampmx == $ampm) {
                            $resultset["response"] = true;
                            $resultset["reference_no"] = $employeeLoa->reference_no;
                        } else {
                            $resultset["response"] = false;
                        }
                    }

                    if ($employeeLoa->type == 3 || $employeeLoa->type == 4) {
                        $ampmx = date("A", strtotime($employeeLoa->date_from));
                        if ($ampmx == $ampm) {
                            $resultset["response"] = true;
                            $resultset["reference_no"] = $employeeLoa->reference_no;
                        } else {
                            $resultset["response"] = false;
                        }
                    }
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getAvailableToByDate($id = null, $date = null) {
            $resultset = array();
            $syncDate = ($date) ? $date : $this->getLastSyncData();
            $lastSyncDate = date("Y-m-d", strtotime($syncDate));
            $nowMeredian = date("A");

            $empData = $this->db->get_where("gccmaster.tblemployees", array("biometricno" => $id));
            if ($empData->num_rows() == 1) {
                $rowEmp = $empData->row();
                $employeeTo = $this->getEmployeeTo($rowEmp->id, $lastSyncDate);
                if ($employeeTo) {
                    $resultset["response"] = true;
                    $resultset["reference_no"] = $employeeTo->reference_no;
                } else {
                    $driverTo = $this->getEmployeeToDriver($rowEmp->id, $lastSyncDate);
                    if ($driverTo) {
                        $resultset["response"] = true;
                        $resultset["reference_no"] = $driverTo->reference_no;
                    }
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getAvailableLoa($id = null, $syncDate = null) {
            $resultset = array();
            if ($id && $syncDate) {
                /*** $syncDate = $this->getLastSyncData(); ***/
                $lastSyncDate = date("Y-m-d", strtotime($syncDate));
                $nowMeredian = date("A");

                $empData = $this->db->get_where("gccmaster.tblemployees", array("biometricno" => $id));
                if ($empData->num_rows() == 1) {
                    $rowEmp = $empData->row();
                    $this->db->order_by("id", "desc");
                    $query = $this->db->get_where("gcceforms.loa", array("employee" => $rowEmp->id));
                    if ($query->num_rows() > 0) {
                        foreach ($query->result() as $rs) {
                            if ($rs->type == 1) {
                                $dateTimeFF = date("Y-m-d", strtotime($rs->date_from));
                                $dateTimeTT = date("Y-m-d", strtotime($rs->date_to));

                                $dateTime = new DateTime($lastSyncDate);
                                $dateTimeFrom = new DateTime($dateTimeFF);
                                $dateTimeTo = new DateTime($dateTimeTT);

                                $dtStamp = $dateTime->getTimestamp();
                                $dtStampFrom = $dateTimeFrom->getTimestamp();
                                $dtStampTo = $dateTimeTo->getTimestamp();

                                if (($dtStamp >= $dtStampFrom) && ($dtStamp <= $dtStampTo)) {
                                    $resultset["response"] = true;
                                    $resultset["reference_no"] = $rs->reference_no;
                                }
                            }

                            if ($rs->type == 2) {
                                $_lastSyncDate = date("Y-m-d", strtotime($syncDate));
                                $dateTime = new DateTime($_lastSyncDate);
                                $dateTimeFrom = new DateTime(date("Y-m-d", strtotime($rs->date_from)));
                                $dateTimeTo = new DateTime(date("Y-m-d", strtotime($rs->date_to)));

                                $dtStamp = $dateTime->getTimestamp();
                                $dtStampFrom = $dateTimeFrom->getTimestamp();
                                $dtStampTo = $dateTimeTo->getTimestamp();

                                if (($dtStamp >= $dtStampFrom) && ($dtStamp <= $dtStampTo)) {
                                    $resultset["response"] = true;
                                    $resultset["reference_no"] = $rs->reference_no;
                                }
                            }

                            if ($rs->type == 3) {
                                $dtLastSync = date("Y-m-d", strtotime($lastSyncDate));
                                $dateFromMeredian = date("Y-m-d", strtotime($rs->date_from));

                                if ($dtLastSync == $dateFromMeredian) {
                                    $resultset["response"] = true;
                                    $resultset["reference_no"] = $rs->reference_no;
                                }
                            }

                            if ($rs->type == 4) {
                                $dateTime = new DateTime($lastSyncDate);
                                $dateTimeFrom = new DateTime(date("Y-m-d", strtotime($rs->date_from)));
                                $dateTimeTo = new DateTime(date("Y-m-d", strtotime($rs->date_to)));

                                $dtStamp = $dateTime->getTimestamp();
                                $dtStampFrom = $dateTimeFrom->getTimestamp();
                                $dtStampTo = $dateTimeTo->getTimestamp();

                                if (($dtStamp >= $dtStampFrom) && ($dtStamp <= $dtStampTo)) {
                                    $resultset["response"] = true;
                                    $resultset["reference_no"] = $rs->reference_no;
                                }
                            }
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

        function getAvailableLoaV2($id = null, $date = null) {
            $resultset = array();
            $syncDate = $date;
            $lastSyncDate = date("Y-m-d", strtotime($syncDate));
            $nowMeredian = date("A");

            $empData = $this->db->get_where("gccmaster.tblemployees", array("biometricno" => $id));
            if ($empData->num_rows() == 1) {
                $rowEmp = $empData->row();
                $this->db->order_by("id", "desc");
                $query = $this->db->get_where("gcceforms.loa", array("employee" => $rowEmp->id));
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $rs) {
                        if ($rs->type == 2) {
                            $_lastSyncDate = date("Y-m-d H:i:s", strtotime($syncDate));
                            $dateTime = new DateTime($_lastSyncDate);
                            $dateTimeFrom = new DateTime($rs->date_from);
                            $dateTimeTo = new DateTime($rs->date_to);

                            $dtStamp = $dateTime->getTimestamp();
                            $dtStampFrom = $dateTimeFrom->getTimestamp();
                            $dtStampTo = $dateTimeTo->getTimestamp();


                            $dateFromMeredian = date("Y-m-d", strtotime($rs->date_from));
                            $dateToMeredian = date("Y-m-d", strtotime($rs->date_to));

                            if (($dtStampFrom >= $dtStamp) || ($dtStamp <= $dtStampTo)) {
                                $resultset["response"] = true;
                                $resultset["reference_no"] = $rs->reference_no;
                            }
                        }

                        if ($rs->type == 3) {
                            $dtLastSync = date("Y-m-d", strtotime($lastSyncDate));
                            $dateFromMeredian = date("Y-m-d", strtotime($rs->date_from));

                            if ($dtLastSync == $dateFromMeredian) {
                                $resultset["response"] = true;
                                $resultset["reference_no"] = $rs->reference_no;
                            }
                        }

                        if ($rs->type == 4) {
                            $dateTime = new DateTime($lastSyncDate);
                            $dateTimeFrom = new DateTime($rs->date_from);
                            $dateTimeTo = new DateTime($rs->date_to);

                            $dtStamp = $dateTime->getTimestamp();
                            $dtStampFrom = $dateTimeFrom->getTimestamp();
                            $dtStampTo = $dateTimeTo->getTimestamp();


                            $dateFromMeredian = date("Y-m-d", strtotime($rs->date_from));
                            $dateToMeredian = date("Y-m-d", strtotime($rs->date_to));

                            if (($dtStampFrom >= $dtStamp) || ($dtStamp <= $dtStampTo)) {
                                $resultset["response"] = true;
                                $resultset["reference_no"] = $rs->reference_no;
                            }
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


        function getPersonalLackingDoubleEntries($biometricId = null) {
            $resultset = array();

            $lastSyncDate = $this->adm_attendance->getLastSyncDate();
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            $startMonth = date("Y-m-01");

            $currentDateToday = date("Y-m-d H:i:s");
            $timeToday = date_format(date_create($currentDateToday), "h:i:s A");

            $begin = new DateTime($startMonth);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            if ($biometricId) {
                $dates = array($begin, $newtime);
                $getAttendanceByDateRange = $this->adm_attendance->getAttendanceByDateRange($dates, $biometricId);

                if ($getAttendanceByDateRange->num_rows() > 0) {
                    $_AMCheck = date_format(date_create($timeToday), "A");
                    foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                        $biometric_id = $_getAttendanceByDateRange["biometric_id"];
                        $device_id = $_getAttendanceByDateRange["device_id"];
                        $att_datetime = $_getAttendanceByDateRange["datetime"];
                        $attendance_datetime = $_getAttendanceByDateRange["datetime"];

                        $datetime = date("H:i", strtotime($attendance_datetime));
                        $currentDate = date("Y-m-d", strtotime($attendance_datetime));

                        $att_index = date("mdY", strtotime($att_datetime));

                        $weekday = date("l", strtotime($currentDate));
                        $weekday = strtolower($weekday);
                        $personnel = $this->getPersonnelShift($biometric_id);
                        if ($personnel) {
                            $data = array();
                            if (intval($personnel->is_flexi) !== 1) {
                                $shift = $personnel->shift_id;
                                $shiftResource = $this->getShiftResource($shift);
                                if ($shiftResource) {
                                    $ids = $shiftResource->shift_resource;
                                    $todayShift = array();
                                    if ($ids) {
                                        foreach ($ids as $id) {
                                            $todayDR = $this->getDailyResource($id, $weekday);
                                            if ($todayDR) {
                                                $todayShift = $todayDR;
                                                break;
                                            }
                                        }
                                    }

                                    if ($todayShift) {
                                        $countShift = 0;
                                        if ($todayShift->am_start !== "00:00:00" && $todayShift->am_start !== "" && $todayShift->am_start !== null) {
                                            $countShift += 1;
                                        }
                                        if ($todayShift->am_end !== "00:00:00" && $todayShift->am_end !== "" && $todayShift->am_end !== null) {
                                            $countShift += 1;
                                        }
                                        if ($todayShift->pm_start !== "00:00:00" && $todayShift->pm_start !== "" && $todayShift->pm_start !== null) {
                                            $countShift += 1;
                                        }
                                        if ($todayShift->pm_end !== "00:00:00" && $todayShift->pm_end !== "" && $todayShift->pm_end !== null) {
                                            $countShift += 1;
                                        }

                                        $loggedTimeRecord = array();
                                        $personnelAttendanceData = $this->getPersonnelAttendanceByDate($currentDate, $biometric_id);

                                        $totalAttendance = count($personnelAttendanceData);

                                        foreach ($personnelAttendanceData as $index => $datax) {
                                            $dateTime = date_format(date_create($datax["datetime"]), "h:i A");
                                            $loggedTimeRecord[] = $dateTime;
                                        }

                                        $data["biometric_id"] = $personnel->biometric_id;
                                        $data["employee_name"] = (isset($personnel->name) && $personnel->name) ? trim($personnel->name) : $personnel->biometric_id;
                                        $data["logged_time_record"] = $loggedTimeRecord;
                                        $data["time_count"] = $totalAttendance;
                                        $data["date"] = $currentDate;

                                        $absenteeRecord = $this->getSingleAttendanceAbsent($biometric_id, $currentDate);
                                        if ($countShift !== 0 && $totalAttendance < $countShift && $absenteeRecord == false) {
                                            $resultset["lacking_entry"][$att_index] = $data;
                                        } else if ($countShift !== 0 && $totalAttendance > $countShift) {
                                            $resultset["double_entry"][$att_index] = $data;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $resultset;
        }

        function getLackingDoubleEntries($overrideDate = null) {
            $resultset = array();

            $today = ($overrideDate) ? date("Y-m-d", strtotime($overrideDate)) : date("Y-m-d");
            $_weekday = date("l", strtotime($today));
            $_weekday = strtolower($_weekday);
            $dayCount = ($_weekday == "monday") ? 2 : 1;

            $yesterday = ($overrideDate) ? date('Y-m-d', strtotime("-{$dayCount} days", strtotime($today))) : date('Y-m-d', strtotime("-{$dayCount} days"));

            $yesterdayAttendance = $this->getYesterdayAttendance($yesterday);
            $weekday = date("l", strtotime($yesterday));
            $weekday = strtolower($weekday);

            if ($yesterdayAttendance) {
                foreach ($yesterdayAttendance as $attendance) {
                    $biometric_id = $attendance["biometric_id"];
                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $this->db->from("gcctimeutility.location");
                        $this->db->where("id", $personnel->location_id);
                        $this->db->where("allow_notification", 1);
                        $queryLocation = $this->db->get();
                        if ($queryLocation->num_rows() == 1) {
                            $data = array();
                            if (intval($personnel->is_flexi) !== 1) {
                                $shift = $personnel->shift_id;
                                $personnel_biometricno = $personnel->biometricno;
                                $shiftResource = $this->getShiftResource($shift);
                                if ($shiftResource) {
                                    $ids = $shiftResource->shift_resource;
                                    $todayShift = array();
                                    if ($ids) {
                                        foreach ($ids as $id) {
                                            $todayDR = $this->getDailyResource($id, $weekday);
                                            if ($todayDR) {
                                                $todayShift = $todayDR;
                                                break;
                                            }
                                        }
                                    }

                                    if ($todayShift) {
                                        $countShift = 0;
                                        if ($todayShift->am_start !== "00:00:00" && $todayShift->am_start !== "" && $todayShift->am_start !== null) {
                                            $countShift += 1;
                                        }
                                        if ($todayShift->am_end !== "00:00:00" && $todayShift->am_end !== "" && $todayShift->am_end !== null) {
                                            $countShift += 1;
                                        }
                                        if ($todayShift->pm_start !== "00:00:00" && $todayShift->pm_start !== "" && $todayShift->pm_start !== null) {
                                            $countShift += 1;
                                        }
                                        if ($todayShift->pm_end !== "00:00:00" && $todayShift->pm_end !== "" && $todayShift->pm_end !== null) {
                                            $countShift += 1;
                                        }

                                        $loggedTimeRecord = array();
                                        $personnelAttendanceData = $this->getPersonnelAttendanceByDate($yesterday, $biometric_id);

                                        $loggedTime = "<table cellpadding='2' cellspacing='2' border='1' bordercolor='#333333' bgcolor='#ffffff'>";
                                        $loggedTime .= "<tbody><tr>";
                                        foreach ($personnelAttendanceData as $index => $datax) {
                                            $dateTime = date_format(date_create($datax["datetime"]), "h:i A");
                                            $loggedTime .= "<td align='center' style='color: #333333; font-size: 12px; font-family: Work Sans, Calibri, sans-serif; line-height: 18px; padding: 2px 5px; text-align: center;'>{$dateTime}</td>";
                                            $loggedTimeRecord[] = $dateTime;
                                        }
                                        $loggedTime .= "</tr></tbody>";
                                        $loggedTime .= "</table>";

                                        $totalAttendance = count($personnelAttendanceData);

                                        $data["biometric_id"] = $personnel->biometric_id;
                                        $data["employee_name"] = (isset($personnel->name) && $personnel->name) ? trim($personnel->name) : $personnel->biometric_id;
                                        $data["logged_time"] = $loggedTime;
                                        $data["logged_time_record"] = $loggedTimeRecord;
                                        $data["time_count"] = $totalAttendance;

                                        $absenteeRecord = $this->getSingleAttendanceAbsent($biometric_id, $yesterday);
                                        if ($countShift !== 0 && $totalAttendance < $countShift && $absenteeRecord == false) {
                                            $toData = $this->getAvailableToByDate($personnel_biometricno, $yesterday);
                                            if (isset($toData["response"]) && $toData["response"]) {
                                                $reference_no = $toData["reference_no"];
                                                $data["reference_no"] = $reference_no;
                                            }
                                            $resultset["lacking_entry"][] = $data;
                                        } else if ($countShift !== 0 && $totalAttendance > $countShift) {
                                            $resultset["double_entry"][] = $data;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $resultset;
        }

        function getYesterdayAttendance($date = null) {
            if ($date) {
                $this->db->from($this->attendanceTable);
                $this->db->like("datetime", $date);
                $this->db->order_by("biometric_id", "asc");
                $this->db->order_by("datetime", "asc");
                $this->db->group_by('biometric_id');
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    return $query->result_array();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getPersonnelAttendanceByDate($date = NULL, $biometricno = NULL) {
            if (isset($date) && isset($biometricno)) {
                $this->db->from($this->attendanceTable);
                $this->db->where("DATE(datetime)", $date);
                $this->db->where("biometric_id", $biometricno);
                $this->db->order_by("datetime", "asc");
                $this->db->group_by("datetime");
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    return $query->result_array();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getAttendanceDepartment($dept_id = null) {
            if ($dept_id) {
                $query = $this->db->get_where($this->departmentTable, array("id" => $dept_id));
                if ($query->num_rows() == 1) {
                    return $query->row();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getLastSyncData() {
            $this->db->from($this->eventTable);
            $this->db->order_by("id", "desc");
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $row = $query->row();
                return $row->created_at;
            } else {
                return date("Y-m-d");
            }
        }

        public function getPersonalLate($biometricId = null) {
            $resultset = array();

            $lastSyncDate = $this->adm_attendance->getLastSyncDate();
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            $startMonth = date("Y-m-01");

            $currentDateToday = date("Y-m-d H:i:s");
            $timeToday = date_format(date_create($currentDateToday), "h:i:s A");

            $begin = new DateTime($startMonth);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            if ($biometricId) {
                $dates = array($begin, $newtime);
                $getAttendanceByDateRange = $this->adm_attendance->getAttendanceByDateRange($dates, $biometricId);

                if ($getAttendanceByDateRange->num_rows() > 0) {
                    $_AMCheck = date_format(date_create($timeToday), "A");
                    foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                        $biometric_id = $_getAttendanceByDateRange["biometric_id"];
                        $device_id = $_getAttendanceByDateRange["device_id"];
                        $att_datetime = $_getAttendanceByDateRange["datetime"];
                        $attendance_datetime = $_getAttendanceByDateRange["datetime"];

                        $datetime = date("H:i", strtotime($attendance_datetime));
                        $currentDate = date("Y-m-d", strtotime($attendance_datetime));

                        $weekday = date("l", strtotime($currentDate));
                        $weekday = strtolower($weekday);
                        $personnel = $this->getPersonnelShift($biometric_id);
                        if ($personnel) {
                            $data = array();
                            if (intval($personnel->is_flexi) !== 1) {
                                $shift = $personnel->shift_id;
                                $shiftResource = $this->getShiftResource($shift);
                                if ($shiftResource) {
                                    $ids = $shiftResource->shift_resource;
                                    $todayShift = array();
                                    if ($ids) {
                                        foreach ($ids as $id) {
                                            $todayDR = $this->getDailyResource($id, $weekday);
                                            if ($todayDR) {
                                                $todayShift = $todayDR;
                                                break;
                                            }
                                        }
                                    }

                                    if ($todayShift) {
                                        $late = $this->getLate($shiftResource->late_id);
                                        if ($late) {
                                            $amStart = date("H:i", strtotime($late->am_start));
                                            $amEnd = date("H:i", strtotime($late->am_end));
                                            $pmStart = date("H:i", strtotime($late->pm_start));
                                            $pmEnd = date("H:i", strtotime($late->pm_end));

                                            $data["biometric_id"] = $biometric_id;
                                            $data["device"] = $device_id;
                                            $data["name"] = $personnel->name;
                                            $data["date"] = date_format(date_create($att_datetime), "n/d/Y");
                                            $data["time"] = date_format(date_create($att_datetime), "h:i A");

                                            $att_index = date("mdY_His", strtotime($att_datetime));
                                            $currentDate = date("Y-m-d", strtotime($att_datetime));

                                            $cc_time = date("Y-m-d H:i", strtotime($att_datetime));
                                            $am_time = date("Y-m-d H:i", strtotime("{$currentDate} {$late->am_start}"));
                                            $pm_time = date("Y-m-d H:i", strtotime("{$currentDate} {$late->pm_start}"));

                                            $am_timeLimit = date("Y-m-d H:i", strtotime("+14 minutes", strtotime("{$currentDate} {$late->am_start}")));
                                            $pm_timeLimit = date("Y-m-d H:i", strtotime("+14 minutes", strtotime("{$currentDate} {$late->pm_start}")));

                                            $getTime = date_format(date_create($att_datetime), "h:i:s A");
                                            $AMCheck = date_format(date_create($getTime), "A");

                                            $amEndData = (isset($todayShift->am_end) && $todayShift->am_end) ? $todayShift->am_end : "12:00";
                                            $pmEndData = (isset($todayShift->pm_end) && $todayShift->pm_end) ? $todayShift->pm_end : "18:00";

                                            $amEndLimit = date("H:i", strtotime("-1 minute", strtotime($amEndData)));
                                            $pmEndLimit = date("H:i", strtotime("-1 minute", strtotime($pmEndData)));

                                            if (isset($todayShift->am_start, $todayShift->am_end) && $todayShift->am_start !== "00:00:00" && $todayShift->am_end !== "00:00:00" && $AMCheck == "AM") {
                                                if (($datetime >= $amStart) && ($datetime <= $amEnd) && ($AMCheck == "AM")) {
                                                    $xDateStart = date("Y-m-d H:i:s", strtotime("{$currentDate} {$amStart}"));
                                                    $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                    $xDateStart = date_create($xDateStart);
                                                    $xDateEnd = date_create($xDateEnd);
                                                    $diff = date_diff($xDateStart, $xDateEnd);

                                                    $data["minlate"] = $diff->format("%i");
                                                    $data["minlate"] += 1;

                                                    if ($data["minlate"] == 1) {
                                                        $data["minlate"] = $data["minlate"] . " Min";
                                                    } else if ($data["minlate"] > 1) {
                                                        $data["minlate"] = $data["minlate"] . " Mins";
                                                    }

                                                    if (($cc_time >= $am_time) && ($cc_time <= $am_timeLimit)) {
                                                        $data["state"] = "normal";
                                                    } else {
                                                        $data["state"] = "exceed";
                                                    }

                                                    $resultset["lates"][$att_index] = $data;
                                                }

                                                if (($datetime >= $amStart) && ($datetime <= $amEndLimit) && ($AMCheck == "AM")) {
                                                    $response = $this->getAttendanceDataLogs($attendance_datetime, $amEndLimit, $AMCheck);
                                                    if ($response) {
                                                        $xDateStart = date("Y-m-d H:i:s", strtotime("{$currentDate} {$amStart}"));
                                                        $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                        $xDateStart = date_create($xDateStart);
                                                        $xDateEnd = date_create($xDateEnd);
                                                        $diff = date_diff($xDateStart, $xDateEnd);

                                                        $data["minlate"] = $diff->format("%i");
                                                        $data["minlate"] += 1;

                                                        if ($data["minlate"] == 1) {
                                                            $data["minlate"] = $data["minlate"] . " Min";
                                                        } else if ($data["minlate"] > 1) {
                                                            $data["minlate"] = $data["minlate"] . " Mins";
                                                        }

                                                        if (($cc_time >= $am_time) && ($cc_time <= $am_timeLimit)) {
                                                            $data["state"] = "normal";
                                                        } else {
                                                            $data["state"] = "exceed";
                                                        }

                                                        $resultset["lates"][$att_index] = $data;
                                                    }
                                                }
                                            }

                                            if (isset($todayShift->pm_start, $todayShift->pm_end) && $todayShift->pm_start !== "00:00:00" && $todayShift->pm_end !== "00:00:00" && $AMCheck == "PM") {
                                                if (($datetime >= $pmStart) && ($datetime <= $pmEnd) && ($AMCheck == "PM")) {
                                                    $xDateStart = date("Y-m-d H:i:s", strtotime("{$currentDate} {$pmStart}"));
                                                    $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                    $xDateStart = date_create($xDateStart);
                                                    $xDateEnd = date_create($xDateEnd);

                                                    $diff = date_diff($xDateStart, $xDateEnd);

                                                    $data["minlate"] = $diff->format("%i");
                                                    $data["minlate"] += 1;

                                                    if ($data["minlate"] == 1) {
                                                        $data["minlate"] = $data["minlate"] . " Min";
                                                    } else if ($data["minlate"] > 1) {
                                                        $data["minlate"] = $data["minlate"] . " Mins";
                                                    }

                                                    if (($cc_time >= $pm_time) && ($cc_time <= $pm_timeLimit)) {
                                                        $data["state"] = "normal";
                                                    } else {
                                                        $data["state"] = "exceed";
                                                    }

                                                    $resultset["lates"][$att_index] = $data;
                                                }

                                                if (($datetime >= $pmStart) && ($datetime <= $pmEndLimit) && ($AMCheck == "PM")) {
                                                    $response = $this->getAttendanceDataLogs($attendance_datetime, $pmEndLimit, $AMCheck);
                                                    if ($response) {
                                                        $xDateStart = date("Y-m-d H:i:s", strtotime("{$currentDate} {$pmStart}"));
                                                        $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                        $xDateStart = date_create($xDateStart);
                                                        $xDateEnd = date_create($xDateEnd);

                                                        $diff = date_diff($xDateStart, $xDateEnd);

                                                        $data["minlate"] = $diff->format("%i");
                                                        $data["minlate"] += 1;

                                                        if ($data["minlate"] == 1) {
                                                            $data["minlate"] = $data["minlate"] . " Min";
                                                        } else if ($data["minlate"] > 1) {
                                                            $data["minlate"] = $data["minlate"] . " Mins";
                                                        }

                                                        if (($cc_time >= $pm_time) && ($cc_time <= $pm_timeLimit)) {
                                                            $data["state"] = "normal";
                                                        } else {
                                                            $data["state"] = "exceed";
                                                        }

                                                        $resultset["lates"][$att_index] = $data;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($_AMCheck == "AM") {
                        $resultset["current_state"] = $_AMCheck;
                    }
                    if ($_AMCheck == "PM") {
                        $resultset["current_state"] = $_AMCheck;
                    }
                }
            }

            return $resultset;
        }

        public function getCurrentLateTest() {
            $resultset = array();

            /* $lastSyncDate = $this->adm_attendance->getLastSyncDate(); */
            $datex = date("Y-m-d", strtotime("2019-04-10"));
            $lastSyncDate = $datex;
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            $currentDateToday = date("Y-m-d H:i:s");
            $timeToday = date_format(date_create($currentDateToday), "h:i:s A");

            $begin = new DateTime($today);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            $dates = array($begin, $newtime);
            $getAttendanceByDateRange = $this->adm_attendance->getAttendanceByDateRange($dates, array());

            $weekday = date("l", strtotime($today));
            $weekday = strtolower($weekday);

            if ($getAttendanceByDateRange->num_rows() > 0) {
                $_AMCheck = date_format(date_create($timeToday), "A");
                foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                    $biometric_id = $_getAttendanceByDateRange["biometric_id"];
                    $device_id = $_getAttendanceByDateRange["device_id"];
                    $att_datetime = $_getAttendanceByDateRange["datetime"];
                    $attendance_datetime = $_getAttendanceByDateRange["datetime"];

                    $datetime = date("H:i", strtotime($attendance_datetime));
                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $data = array();
                        if (intval($personnel->is_flexi) !== 1) {
                            $shift = $personnel->shift_id;
                            $shiftResource = $this->getShiftResource($shift);
                            if ($shiftResource) {
                                $ids = $shiftResource->shift_resource;
                                $todayShift = array();
                                if ($ids) {
                                    foreach ($ids as $id) {
                                        $todayDR = $this->getDailyResource($id, $weekday);
                                        if ($todayDR) {
                                            $todayShift = $todayDR;
                                            break;
                                        }
                                    }
                                }

                                if ($todayShift) {
                                    $late = $this->getLate($shiftResource->late_id);
                                    if ($late) {
                                        $amStart = date("H:i", strtotime($late->am_start));
                                        $amEnd = date("H:i", strtotime($late->am_end));
                                        $pmStart = date("H:i", strtotime($late->pm_start));
                                        $pmEnd = date("H:i", strtotime($late->pm_end));

                                        $data["biometric_id"] = $biometric_id;
                                        $data["device"] = $device_id;
                                        $data["name"] = $personnel->name;
                                        $data["date"] = date_format(date_create($att_datetime), "n/d/Y");
                                        $data["time"] = date_format(date_create($att_datetime), "h:i A");

                                        $currentDate = date("Y-m-d", strtotime($att_datetime));

                                        $cc_time = date("Y-m-d H:i", strtotime($att_datetime));
                                        $am_time = date("Y-m-d H:i", strtotime("{$currentDate} {$late->am_start}"));
                                        $pm_time = date("Y-m-d H:i", strtotime("{$currentDate} {$late->pm_start}"));

                                        $am_timeLimit = date("Y-m-d H:i", strtotime("+14 minutes", strtotime("{$currentDate} {$late->am_start}")));
                                        $pm_timeLimit = date("Y-m-d H:i", strtotime("+14 minutes", strtotime("{$currentDate} {$late->pm_start}")));

                                        $getTime = date_format(date_create($att_datetime), "h:i:s A");
                                        $AMCheck = date_format(date_create($getTime), "A");

                                        $amEndData = (isset($todayShift->am_end) && $todayShift->am_end) ? $todayShift->am_end : "12:00";
                                        $pmEndData = (isset($todayShift->pm_end) && $todayShift->pm_end) ? $todayShift->pm_end : "18:00";

                                        $amEndLimit = date("H:i", strtotime("-1 minute", strtotime($amEndData)));
                                        $pmEndLimit = date("H:i", strtotime("-1 minute", strtotime($pmEndData)));

                                        if (isset($todayShift->am_start, $todayShift->am_end) && $todayShift->am_start !== "00:00:00" && $todayShift->am_end !== "00:00:00" && $AMCheck == "AM") {

                                            if (($datetime >= $amStart) && ($datetime <= $amEnd) && ($AMCheck == "AM")) {
                                                $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$amStart}"));
                                                $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                $xDateStart = date_create($xDateStart);
                                                $xDateEnd = date_create($xDateEnd);
                                                $diff = date_diff($xDateStart, $xDateEnd);

                                                $data["minlate"] = $diff->format("%i");
                                                $data["minlate"] += 1;

                                                if ($data["minlate"] == 1) {
                                                    $data["minlate"] = $data["minlate"] . " Min";
                                                } else if ($data["minlate"] > 1) {
                                                    $data["minlate"] = $data["minlate"] . " Mins";
                                                }

                                                if ($cc_time >= $am_time && $cc_time <= $am_timeLimit) {
                                                    $data["state"] = "normal";
                                                    $data["cc_from"] = $am_time;
                                                    $data["cc_time"] = $cc_time;
                                                    $data["cc_to"] = $am_timeLimit;
                                                } else {
                                                    $data["state"] = "exceed";
                                                    $data["cc_from"] = $am_time;
                                                    $data["cc_time"] = $cc_time;
                                                    $data["cc_to"] = $am_timeLimit;
                                                }

                                                $resultset["checklate_am"][$biometric_id] = $data;
                                            }

                                            if (($datetime >= $amStart) && ($datetime <= $amEndLimit) && ($AMCheck == "AM")) {
                                                $response = $this->getAttendanceDataLogs($attendance_datetime, $amEndLimit, $AMCheck);
                                                if ($response) {
                                                    $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$amStart}"));
                                                    $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                    $xDateStart = date_create($xDateStart);
                                                    $xDateEnd = date_create($xDateEnd);
                                                    $diff = date_diff($xDateStart, $xDateEnd);

                                                    $data["minlate"] = $diff->format("%i");
                                                    $data["minlate"] += 1;

                                                    if ($data["minlate"] == 1) {
                                                        $data["minlate"] = $data["minlate"] . " Min";
                                                    } else if ($data["minlate"] > 1) {
                                                        $data["minlate"] = $data["minlate"] . " Mins";
                                                    }

                                                    if ($cc_time >= $am_time && $cc_time <= $am_timeLimit) {
                                                        $data["state"] = "normal";
                                                        $data["cc_from"] = $am_time;
                                                        $data["cc_time"] = $cc_time;
                                                        $data["cc_to"] = $am_timeLimit;
                                                    } else {
                                                        $data["state"] = "exceed";
                                                        $data["cc_from"] = $am_time;
                                                        $data["cc_time"] = $cc_time;
                                                        $data["cc_to"] = $am_timeLimit;
                                                    }

                                                    $resultset["checklate_am"][$biometric_id] = $data;
                                                }
                                            }
                                        }

                                        if (isset($todayShift->pm_start, $todayShift->pm_end) && $todayShift->pm_start !== "00:00:00" && $todayShift->pm_end !== "00:00:00" && $AMCheck == "PM") {
                                            if (($datetime >= $pmStart) && ($datetime <= $pmEnd) && ($AMCheck == "PM")) {
                                                $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$pmStart}"));
                                                $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                $xDateStart = date_create($xDateStart);
                                                $xDateEnd = date_create($xDateEnd);

                                                $diff = date_diff($xDateStart, $xDateEnd);

                                                $data["minlate"] = $diff->format("%i");
                                                $data["minlate"] += 1;

                                                if ($data["minlate"] == 1) {
                                                    $data["minlate"] = $data["minlate"] . " Min";
                                                } else if ($data["minlate"] > 1) {
                                                    $data["minlate"] = $data["minlate"] . " Mins";
                                                }

                                                if ($cc_time >= $pm_time && $cc_time <= $pm_timeLimit) {
                                                    $data["state"] = "normal";
                                                    $data["cc_from"] = $pm_time;
                                                    $data["cc_time"] = $cc_time;
                                                    $data["cc_to"] = $pm_timeLimit;
                                                } else {
                                                    $data["state"] = "exceed";
                                                    $data["cc_from"] = $pm_time;
                                                    $data["cc_time"] = $cc_time;
                                                    $data["cc_to"] = $pm_timeLimit;
                                                }

                                                $resultset["checklate_pm"][$biometric_id] = $data;
                                            }
                                            if (($datetime >= $pmStart) && ($datetime <= $pmEndLimit) && ($AMCheck == "PM")) {
                                                $response = $this->getAttendanceDataLogs($attendance_datetime, $pmEndLimit, $AMCheck);
                                                if ($response) {
                                                    $xDateStart = date("Y-m-d H:i:s", strtotime("{$currentDate} {$pmStart}"));
                                                    $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                    $xDateStart = date_create($xDateStart);
                                                    $xDateEnd = date_create($xDateEnd);

                                                    $diff = date_diff($xDateStart, $xDateEnd);

                                                    $data["minlate"] = $diff->format("%i");
                                                    $data["minlate"] += 1;

                                                    if ($data["minlate"] == 1) {
                                                        $data["minlate"] = $data["minlate"] . " Min";
                                                    } else if ($data["minlate"] > 1) {
                                                        $data["minlate"] = $data["minlate"] . " Mins";
                                                    }

                                                    if ($cc_time >= $pm_time && $cc_time <= $pm_timeLimit) {
                                                        $data["state"] = "normal";
                                                        $data["cc_from"] = $pm_time;
                                                        $data["cc_time"] = $cc_time;
                                                        $data["cc_to"] = $pm_timeLimit;
                                                    } else {
                                                        $data["state"] = "exceed";
                                                        $data["cc_from"] = $pm_time;
                                                        $data["cc_time"] = $cc_time;
                                                        $data["cc_to"] = $pm_timeLimit;
                                                    }

                                                    $resultset["checklate_pm"][$biometric_id] = $data;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($_AMCheck == "AM") {
                    $resultset["current_state"] = $_AMCheck;
                }
                if ($_AMCheck == "PM") {
                    $resultset["current_state"] = $_AMCheck;
                }
            }

            return $resultset;
        }

        function getAttendanceDataLogs($attendanceDate = null, $endLimit = null, $AMPM = "AM") {
            if ($attendanceDate && $endLimit) {
                $time = ($AMPM == "AM") ? "00:00:00" : "12:00:00";

                $currentDate = date("Y-m-d", strtotime($attendanceDate));
                $currentDateFrom = date("Y-m-d {$time}", strtotime($attendanceDate));
                $currentDateTo = date("Y-m-d {$endLimit}", strtotime($attendanceDate));


                $this->db->from("gcctimeutility.attendance");
                $this->db->where("datetime >=", $currentDateFrom);
                $this->db->where("datetime <=", $currentDateTo);
                $query = $this->db->get();

                if ($AMPM == "AM") {
                    if ($query->num_rows() == 1) {
                        return true;
                    } else {
                        return false;
                    }
                }

                if ($AMPM == "PM") {
                    if ($query->num_rows() == 2 || $query->num_rows() == 1) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }

        public function getCurrentLate_old() {
            $resultset = array();
            $lastSyncDate = $this->adm_attendance->getLastSyncDate();
            /*** $lastSyncDate = "2020-10-23"; ***/
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            $currentDateToday = date("Y-m-d H:i:s");
            /*** $currentDateToday = "2020-10-23 13:35:00"; ***/
            $timeToday = date_format(date_create($currentDateToday), "h:i:s A");

            $begin = new DateTime($today);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            $dates = array($begin, $newtime);
            $getAttendanceByDateRange = $this->adm_attendance->getAttendanceByDateRange($dates, array());

            $weekday = date("l", strtotime($today));
            $weekday = strtolower($weekday);

            if ($getAttendanceByDateRange->num_rows() > 0) {
                $_AMCheck = date_format(date_create($timeToday), "A");
                foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                    $biometric_id = $_getAttendanceByDateRange["biometric_id"];
                    $device_id = $_getAttendanceByDateRange["device_id"];
                    $att_datetime = $_getAttendanceByDateRange["datetime"];
                    $attendance_datetime = $_getAttendanceByDateRange["datetime"];

                    $datetime = date("H:i", strtotime($attendance_datetime));
                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $this->db->from("gcctimeutility.location");
                        $this->db->where("id", $personnel->location_id);
                        $this->db->where("allow_notification", 1);
                        $queryLocation = $this->db->get();
                        if ($queryLocation->num_rows() == 1) {
                            $data = array();
                            if (intval($personnel->is_flexi) !== 1) {
                                $shift = $personnel->shift_id;
                                $shiftResource = $this->getShiftResource($shift);
                                if ($shiftResource) {
                                    $ids = $shiftResource->shift_resource;
                                    $todayShift = array();
                                    if ($ids) {
                                        foreach ($ids as $id) {
                                            $todayDR = $this->getDailyResource($id, $weekday);
                                            if ($todayDR) {
                                                $todayShift = $todayDR;
                                                break;
                                            }
                                        }
                                    }

                                    if ($todayShift) {
                                        $late = $this->getLate($shiftResource->late_id);
                                        if ($late) {
                                            $amStart = date("H:i", strtotime($late->am_start));
                                            $amEnd = date("H:i", strtotime($late->am_end));
                                            $pmStart = date("H:i", strtotime($late->pm_start));
                                            $pmEnd = date("H:i", strtotime($late->pm_end));

                                            $data["biometric_id"] = $biometric_id;
                                            $data["device"] = $device_id;
                                            $data["name"] = $personnel->name;
                                            $data["date"] = date_format(date_create($att_datetime), "n/d/Y");
                                            $data["time"] = date_format(date_create($att_datetime), "h:i A");

                                            $currentDate = date("Y-m-d", strtotime($att_datetime));

                                            $cc_time = date("Y-m-d H:i", strtotime($att_datetime));
                                            $am_time = date("Y-m-d H:i", strtotime("{$currentDate} {$late->am_start}"));
                                            $pm_time = date("Y-m-d H:i", strtotime("{$currentDate} {$late->pm_start}"));

                                            $am_timeLimit = date("Y-m-d H:i", strtotime("+14 minutes", strtotime("{$currentDate} {$late->am_start}")));
                                            $pm_timeLimit = date("Y-m-d H:i", strtotime("+14 minutes", strtotime("{$currentDate} {$late->pm_start}")));

                                            $getTime = date_format(date_create($att_datetime), "h:i:s A");
                                            $AMCheck = date_format(date_create($getTime), "A");

                                            $amEndData = (isset($todayShift->am_end) && $todayShift->am_end) ? $todayShift->am_end : "12:00";
                                            $pmEndData = (isset($todayShift->pm_end) && $todayShift->pm_end) ? $todayShift->pm_end : "18:00";

                                            $amEndLimit = date("H:i", strtotime("-1 minute", strtotime($amEndData)));
                                            $pmEndLimit = date("H:i", strtotime("-1 minute", strtotime($pmEndData)));

                                            if (isset($todayShift->am_start, $todayShift->am_end) && $todayShift->am_start !== "00:00:00" && $todayShift->am_end !== "00:00:00" && $AMCheck == "AM") {
                                                if (($datetime >= $amStart) && ($datetime <= $amEnd) && ($AMCheck == "AM")) {
                                                    $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$amStart}"));
                                                    $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                    $xDateStart = date_create($xDateStart);
                                                    $xDateEnd = date_create($xDateEnd);
                                                    $diff = date_diff($xDateStart, $xDateEnd);

                                                    $data["minlate"] = $diff->format("%i");
                                                    $data["minlate"] += 1;

                                                    if ($data["minlate"] == 1) {
                                                        $data["minlate"] = $data["minlate"] . " Min";
                                                    } else if ($data["minlate"] > 1) {
                                                        $data["minlate"] = $data["minlate"] . " Mins";
                                                    }

                                                    if (($cc_time >= $am_time) && ($cc_time <= $am_timeLimit)) {
                                                        $data["state"] = "normal";
                                                    } else {
                                                        $data["state"] = "exceed";
                                                    }

                                                    $resultset["checklate_am"][$biometric_id] = $data;
                                                }

                                                if (($datetime >= $amStart) && ($datetime <= $amEndLimit) && ($AMCheck == "AM")) {
                                                    $response = $this->getAttendanceDataLogs($attendance_datetime, $amEndLimit, $AMCheck);
                                                    if ($response) {
                                                        $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$amStart}"));
                                                        $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                        $xDateStart = date_create($xDateStart);
                                                        $xDateEnd = date_create($xDateEnd);
                                                        $diff = date_diff($xDateStart, $xDateEnd);

                                                        $data["minlate"] = $diff->format("%i");
                                                        $data["minlate"] += 1;

                                                        if ($data["minlate"] == 1) {
                                                            $data["minlate"] = $data["minlate"] . " Min";
                                                        } else if ($data["minlate"] > 1) {
                                                            $data["minlate"] = $data["minlate"] . " Mins";
                                                        }

                                                        if (($cc_time >= $am_time) && ($cc_time <= $am_timeLimit)) {
                                                            $data["state"] = "normal";
                                                        } else {
                                                            $data["state"] = "exceed";
                                                        }

                                                        $resultset["checklate_am"][$biometric_id] = $data;
                                                    }
                                                }
                                            }

                                            if (isset($todayShift->pm_start, $todayShift->pm_end) && $todayShift->pm_start !== "00:00:00" && $todayShift->pm_end !== "00:00:00" && $AMCheck == "PM") {
                                                if (($datetime >= $pmStart) && ($datetime <= $pmEnd) && ($AMCheck == "PM")) {
                                                    $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$pmStart}"));
                                                    $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                    $xDateStart = date_create($xDateStart);
                                                    $xDateEnd = date_create($xDateEnd);

                                                    $diff = date_diff($xDateStart, $xDateEnd);

                                                    $data["minlate"] = $diff->format("%i");
                                                    $data["minlate"] += 1;

                                                    if ($data["minlate"] == 1) {
                                                        $data["minlate"] = $data["minlate"] . " Min";
                                                    } else if ($data["minlate"] > 1) {
                                                        $data["minlate"] = $data["minlate"] . " Mins";
                                                    }

                                                    if (($cc_time >= $pm_time) && ($cc_time <= $pm_timeLimit)) {
                                                        $data["state"] = "normal";
                                                    } else {
                                                        $data["state"] = "exceed";
                                                    }

                                                    $resultset["checklate_pm"][$biometric_id] = $data;
                                                }

                                                if (($datetime >= $pmStart) && ($datetime <= $pmEndLimit) && ($AMCheck == "PM")) {
                                                    $response = $this->getAttendanceDataLogs($attendance_datetime, $pmEndLimit, $AMCheck);
                                                    if ($response) {
                                                        $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$pmStart}"));
                                                        $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                        $xDateStart = date_create($xDateStart);
                                                        $xDateEnd = date_create($xDateEnd);

                                                        $diff = date_diff($xDateStart, $xDateEnd);

                                                        $data["minlate"] = $diff->format("%i");
                                                        $data["minlate"] += 1;

                                                        if ($data["minlate"] == 1) {
                                                            $data["minlate"] = $data["minlate"] . " Min";
                                                        } else if ($data["minlate"] > 1) {
                                                            $data["minlate"] = $data["minlate"] . " Mins";
                                                        }

                                                        if (($cc_time >= $pm_time) && ($cc_time <= $pm_timeLimit)) {
                                                            $data["state"] = "normal";
                                                        } else {
                                                            $data["state"] = "exceed";
                                                        }

                                                        $resultset["checklate_pm"][$biometric_id] = $data;
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

                if ($_AMCheck == "AM") {
                    $resultset["current_state"] = $_AMCheck;
                }
                if ($_AMCheck == "PM") {
                    $resultset["current_state"] = $_AMCheck;
                }
            }

            $this->saveCurrentLate($resultset);
            return $resultset;
        }

        public function getCurrentLate() {
            $resultset = array();
            $lastSyncDate = $this->adm_attendance->getLastSyncDate();
            /*** $lastSyncDate = "2020-10-23"; ***/
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            $currentDateToday = date("Y-m-d H:i:s");
            /*** $currentDateToday = "2020-10-23 13:35:00"; ***/
            $timeToday = date_format(date_create($currentDateToday), "h:i:s A");

            $begin = new DateTime($today);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            $dates = array($begin, $newtime);
            $getAttendanceByDateRange = $this->adm_attendance->getAttendanceByDateRange($dates, array());

            $weekday = date("l", strtotime($today));
            $weekday = strtolower($weekday);

            if ($getAttendanceByDateRange->num_rows() > 0) {
                $_AMCheck = date_format(date_create($timeToday), "A");
                foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                    $biometric_id = $_getAttendanceByDateRange["biometric_id"];
                    $device_id = $_getAttendanceByDateRange["device_id"];
                    $att_datetime = $_getAttendanceByDateRange["datetime"];
                    $attendance_datetime = $_getAttendanceByDateRange["datetime"];

                    $datetime = date("H:i", strtotime($attendance_datetime));
                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $department = $this->getPersonnelDepartment($personnel->biometricno);
                        $position = $this->getPersonnelPosition($personnel->biometricno);
                        $station = $this->getPersonnelStation($personnel->biometricno);
                        
                        $this->db->from("gcctimeutility.location");
                        $this->db->where("id", $personnel->location_id);
                        $this->db->where("allow_notification", 1);
                        $queryLocation = $this->db->get();
                        if ($queryLocation->num_rows() == 1) {
                            $data = array();
                            if (intval($personnel->is_flexi) !== 1) {
                                $shift = $personnel->shift_id;
                                $shiftResource = $this->getShiftResource($shift);
                                if ($shiftResource) {
                                    $ids = $shiftResource->shift_resource;
                                    $todayShift = array();
                                    if ($ids) {
                                        foreach ($ids as $id) {
                                            $todayDR = $this->getDailyResource($id, $weekday);
                                            if ($todayDR) {
                                                $todayShift = $todayDR;
                                                break;
                                            }
                                        }
                                    }

                                    if ($todayShift) {
                                        $late = $this->getLate($shiftResource->late_id);
                                        if ($late) {
                                            $amStart = date("H:i", strtotime($late->am_start));
                                            $amEnd = date("H:i", strtotime($late->am_end));
                                            $pmStart = date("H:i", strtotime($late->pm_start));
                                            $pmEnd = date("H:i", strtotime($late->pm_end));

                                            $data["biometric_id"] = $biometric_id;
                                            $data["device"] = $device_id;
                                            $data["name"] = $personnel->name;
                                            $data["department"] = ($department) ? $department : "N/A";
                                            $data["position"] = ($position) ? $position : "N/A";
                                            $data["station"] = ($station) ? $station : "NO STATION";
                                            $data["date"] = date_format(date_create($att_datetime), "n/d/Y");
                                            $data["time"] = date_format(date_create($att_datetime), "h:i A");

                                            $currentDate = date("Y-m-d", strtotime($att_datetime));

                                            $cc_time = date("Y-m-d H:i", strtotime($att_datetime));
                                            $am_time = date("Y-m-d H:i", strtotime("{$currentDate} {$late->am_start}"));
                                            $pm_time = date("Y-m-d H:i", strtotime("{$currentDate} {$late->pm_start}"));

                                            $am_timeLimit = date("Y-m-d H:i", strtotime("+14 minutes", strtotime("{$currentDate} {$late->am_start}")));
                                            $pm_timeLimit = date("Y-m-d H:i", strtotime("+14 minutes", strtotime("{$currentDate} {$late->pm_start}")));

                                            $getTime = date_format(date_create($att_datetime), "h:i:s A");
                                            $AMCheck = date_format(date_create($getTime), "A");

                                            $amEndData = (isset($todayShift->am_end) && $todayShift->am_end) ? $todayShift->am_end : "12:00";
                                            $pmEndData = (isset($todayShift->pm_end) && $todayShift->pm_end) ? $todayShift->pm_end : "18:00";

                                            $amEndLimit = date("H:i", strtotime("-1 minute", strtotime($amEndData)));
                                            $pmEndLimit = date("H:i", strtotime("-1 minute", strtotime($pmEndData)));

                                            if (isset($todayShift->am_start, $todayShift->am_end) && $todayShift->am_start !== "00:00:00" && $todayShift->am_end !== "00:00:00" && $AMCheck == "AM") {
                                                if (($datetime >= $amStart) && ($datetime <= $amEnd) && ($AMCheck == "AM")) {
                                                    $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$amStart}"));
                                                    $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                    $xDateStart = date_create($xDateStart);
                                                    $xDateEnd = date_create($xDateEnd);
                                                    $diff = date_diff($xDateStart, $xDateEnd);

                                                    $data["minlate"] = $diff->format("%i");
                                                    $data["minlate"] += 1;

                                                    if ($data["minlate"] == 1) {
                                                        $data["minlate"] = $data["minlate"] . " Min";
                                                    } else if ($data["minlate"] > 1) {
                                                        $data["minlate"] = $data["minlate"] . " Mins";
                                                    }

                                                    if (($cc_time >= $am_time) && ($cc_time <= $am_timeLimit)) {
                                                        $data["state"] = "normal";
                                                    } else {
                                                        $data["state"] = "exceed";
                                                    }

                                                    $resultset["checklate_am"][$biometric_id] = $data;
                                                }

                                                if (($datetime >= $amStart) && ($datetime <= $amEndLimit) && ($AMCheck == "AM")) {
                                                    $response = $this->getAttendanceDataLogs($attendance_datetime, $amEndLimit, $AMCheck);
                                                    if ($response) {
                                                        $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$amStart}"));
                                                        $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                        $xDateStart = date_create($xDateStart);
                                                        $xDateEnd = date_create($xDateEnd);
                                                        $diff = date_diff($xDateStart, $xDateEnd);

                                                        $data["minlate"] = $diff->format("%i");
                                                        $data["minlate"] += 1;

                                                        if ($data["minlate"] == 1) {
                                                            $data["minlate"] = $data["minlate"] . " Min";
                                                        } else if ($data["minlate"] > 1) {
                                                            $data["minlate"] = $data["minlate"] . " Mins";
                                                        }

                                                        if (($cc_time >= $am_time) && ($cc_time <= $am_timeLimit)) {
                                                            $data["state"] = "normal";
                                                        } else {
                                                            $data["state"] = "exceed";
                                                        }

                                                        $resultset["checklate_am"][$biometric_id] = $data;
                                                    }
                                                }
                                            }

                                            if (isset($todayShift->pm_start, $todayShift->pm_end) && $todayShift->pm_start !== "00:00:00" && $todayShift->pm_end !== "00:00:00" && $AMCheck == "PM") {
                                                if (($datetime >= $pmStart) && ($datetime <= $pmEnd) && ($AMCheck == "PM")) {
                                                    $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$pmStart}"));
                                                    $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                    $xDateStart = date_create($xDateStart);
                                                    $xDateEnd = date_create($xDateEnd);

                                                    $diff = date_diff($xDateStart, $xDateEnd);

                                                    $data["minlate"] = $diff->format("%i");
                                                    $data["minlate"] += 1;

                                                    if ($data["minlate"] == 1) {
                                                        $data["minlate"] = $data["minlate"] . " Min";
                                                    } else if ($data["minlate"] > 1) {
                                                        $data["minlate"] = $data["minlate"] . " Mins";
                                                    }

                                                    if (($cc_time >= $pm_time) && ($cc_time <= $pm_timeLimit)) {
                                                        $data["state"] = "normal";
                                                    } else {
                                                        $data["state"] = "exceed";
                                                    }

                                                    $resultset["checklate_pm"][$biometric_id] = $data;
                                                }

                                                if (($datetime >= $pmStart) && ($datetime <= $pmEndLimit) && ($AMCheck == "PM")) {
                                                    $response = $this->getAttendanceDataLogs($attendance_datetime, $pmEndLimit, $AMCheck);
                                                    if ($response) {
                                                        $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$pmStart}"));
                                                        $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                                        $xDateStart = date_create($xDateStart);
                                                        $xDateEnd = date_create($xDateEnd);

                                                        $diff = date_diff($xDateStart, $xDateEnd);

                                                        $data["minlate"] = $diff->format("%i");
                                                        $data["minlate"] += 1;

                                                        if ($data["minlate"] == 1) {
                                                            $data["minlate"] = $data["minlate"] . " Min";
                                                        } else if ($data["minlate"] > 1) {
                                                            $data["minlate"] = $data["minlate"] . " Mins";
                                                        }

                                                        if (($cc_time >= $pm_time) && ($cc_time <= $pm_timeLimit)) {
                                                            $data["state"] = "normal";
                                                        } else {
                                                            $data["state"] = "exceed";
                                                        }

                                                        $resultset["checklate_pm"][$biometric_id] = $data;
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

                if ($_AMCheck == "AM") {
                    $resultset["current_state"] = $_AMCheck;
                }
                if ($_AMCheck == "PM") {
                    $resultset["current_state"] = $_AMCheck;
                }
            }

            // Save first before grouping
            $this->saveCurrentLate($resultset);

            // Group by station
            $resultset = $this->groupLateByStation($resultset);
            return $resultset;
        }

        private function saveCurrentLate($late_data) {
            if (!empty($late_data)) {
                $meridian = $late_data["current_state"];
                $var = "checklate_" . strtolower($meridian);

                if (isset($late_data[$var]) && $late_data[$var]) {
                    foreach ($late_data[$var] as $datum) {
                        $emp_id = $this->db->select("emp.id")
                            ->join("gcctimeutility.personnel personnel", "personnel.biometricno = emp.biometricno OR personnel.biometric_id = emp.biometricno", "INNER")
                            ->where("personnel.biometric_id", $datum["biometric_id"])
                            ->or_where("personnel.biometricno", $datum["biometric_id"])
                            ->get("gccmaster.tblemployees emp")
                            ->row("id");

                        $minlate = explode(" ", $datum["minlate"]);
                        $date_time = date("Y-m-d H:i:s", strtotime($datum["date"] . " " . $datum["time"]));

                        $exist = $this->db
                            ->where(array(
                                "biometric_id" => $datum["biometric_id"],
                                "date_time" => $date_time
                            ))
                            ->count_all_results("gcctimeutility.late_report");

                        if ($exist <= 0 && $emp_id) {
                            $data = array(
                                "biometric_id" => $datum["biometric_id"],
                                "emp_id" => $emp_id,
                                "emp_name" => $datum["name"],
                                "date_time" => $date_time,
                                "minutes_late" => !empty($minlate) ? $minlate[0] : 0,
                            );

                            $this->db->insert("gcctimeutility.late_report", $data);
                        }
                    }
                }
            }
        }

        function getPersonalUndertime($biometricId = null) {
            $resultset = array();

            $lastSyncDate = $this->adm_attendance->getLastSyncDate();
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            $startMonth = date("Y-m-01");

            $currentDateToday = date("Y-m-d H:i:s");
            $timeToday = date_format(date_create($currentDateToday), "h:i:s A");

            $begin = new DateTime($startMonth);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            if ($biometricId) {
                $dates = array($begin, $newtime);
                $getAttendanceByDateRange = $this->adm_attendance->getAttendanceByDateRange($dates, $biometricId);

                if ($getAttendanceByDateRange->num_rows() > 0) {
                    foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                        $biometric_id = $_getAttendanceByDateRange["biometric_id"];
                        $device_id = $_getAttendanceByDateRange["device_id"];
                        $att_datetime = $_getAttendanceByDateRange["datetime"];
                        $attendance_datetime = $_getAttendanceByDateRange["datetime"];

                        $datetime = date("H:i", strtotime($attendance_datetime));
                        $currentDate = date("Y-m-d", strtotime($attendance_datetime));
                        $att_index = date("mdY_His", strtotime($attendance_datetime));

                        $weekday = date("l", strtotime($currentDate));
                        $weekday = strtolower($weekday);

                        $personnel = $this->getPersonnelShift($biometric_id);
                        if ($personnel) {
                            $data = array();
                            if (intval($personnel->is_flexi) !== 1) {
                                $shift = $personnel->shift_id;
                                $personnel_biometricno = $personnel->biometricno;

                                $shiftResource = $this->getShiftResource($shift);
                                if ($shiftResource) {
                                    $ids = $shiftResource->shift_resource;
                                    $todayShift = array();
                                    if ($ids) {
                                        foreach ($ids as $id) {
                                            $todayDR = $this->getDailyResource($id, $weekday);
                                            if ($todayDR) {
                                                $todayShift = $todayDR;
                                                break;
                                            }
                                        }
                                    }

                                    if ($todayShift) {
                                        $shiftPmEnd = $todayShift->pm_end;
                                        $shiftPmEnd = date("H:i", strtotime($shiftPmEnd));

                                        $undertime = $this->getUndertime($shiftResource->undertime_id);
                                        $reference_no = "N/A";

                                        if ($undertime) {
                                            $amStart = date("H:i", strtotime($undertime->am_start));
                                            $amEnd = date("H:i", strtotime($undertime->am_end));
                                            $pmStart = date("H:i", strtotime($undertime->pm_start));
                                            $pmEnd = date("H:i", strtotime($undertime->pm_end));


                                            if ($pmEnd > $shiftPmEnd) {
                                                $cdate = date("Y-m-d");
                                                $cdate1 = date("Y-m-d H:i", strtotime("{$cdate} {$shiftPmEnd}"));
                                                $cdate2 = date("Y-m-d H:i", strtotime("{$cdate} {$pmEnd}"));

                                                $ccDate1 = date_create($cdate1);
                                                $ccDate2 = date_create($cdate2);

                                                $diff = date_diff($ccDate1, $ccDate2);
                                                $hour = $diff->format("%H");
                                                $min = $diff->format("%i");
                                                if ($hour == 0) {
                                                    $hour += 1;
                                                }

                                                $nn = date("H:i", strtotime($shiftPmEnd . "- {$hour} hour + {$min} minute"));
                                                $pmEnd = $nn;

                                            }

                                            $department = $this->getAttendanceDepartment($personnel->department_id);
                                            $currentDepartment = (isset($department->description) && $department->description) ? $department->description : "N/A";

                                            $data["biometric_id"] = $biometric_id;
                                            $data["device"] = $device_id;
                                            $data["name"] = $personnel->name;
                                            $data["department"] = $currentDepartment;
                                            $data["date"] = date_format(date_create($att_datetime), "n/d/Y");

                                            $dateTime = date_format(date_create($att_datetime), "h:i A");
                                            $data["time"] = $dateTime;

                                            $_dateTime = date_format(date_create($att_datetime), "H:i");

                                            $getTime = date_format(date_create($att_datetime), "h:i:s A");
                                            $AMCheck = date_format(date_create($getTime), "A");

                                            $data["mrdn"] = $AMCheck;

                                            if (isset($todayShift->am_start, $todayShift->am_end) && $todayShift->am_start !== "00:00:00" && $todayShift->am_end !== "00:00:00" && $AMCheck == "AM") {
                                                if (($_dateTime >= $amStart) && ($_dateTime <= $amEnd) && ($AMCheck == "AM")) {
                                                    $loa_data = $this->getAvailableLoaByDate($personnel_biometricno, $currentDate, $AMCheck);
                                                    if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                        $reference_no = $loa_data["reference_no"];
                                                    }
                                                    $toData = $this->getAvailableToByDate($personnel_biometricno, $currentDate);
                                                    if (isset($toData["response"]) && $toData["response"]) {
                                                        $reference_no = $toData["reference_no"];
                                                    }

                                                    $data["content"] = $reference_no;
                                                    $resultset["undertime"][$att_index] = $data;
                                                }
                                            }
                                            if (isset($todayShift->pm_start, $todayShift->pm_end) && $todayShift->pm_start !== "00:00:00" && $todayShift->pm_end !== "00:00:00" && $AMCheck == "PM") {
                                                if (($_dateTime >= $pmStart) && ($_dateTime <= $pmEnd) && ($AMCheck == "PM")) {
                                                    $loa_data = $this->getAvailableLoaByDate($personnel_biometricno, $currentDate, $AMCheck);
                                                    if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                        $reference_no = $loa_data["reference_no"];
                                                    }
                                                    $toData = $this->getAvailableToByDate($personnel_biometricno, $currentDate);
                                                    if (isset($toData["response"]) && $toData["response"]) {
                                                        $reference_no = $toData["reference_no"];
                                                    }

                                                    $data["content"] = $reference_no;
                                                    $resultset["undertime"][$att_index] = $data;
                                                }
                                            }

                                            /*********/
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $resultset;
        }

        function getCurrentUndertime($overrideDate = null) {
            $resultset = array();

            $today = ($overrideDate) ? date("Y-m-d", strtotime($overrideDate)) : date("Y-m-d");

            $_weekday = date("l", strtotime($today));
            $_weekday = strtolower($_weekday);
            $dayCount = ($_weekday == "monday") ? 2 : 1;

            $yesterday = ($overrideDate) ? date('Y-m-d', strtotime("-{$dayCount} days", strtotime($overrideDate))) : date('Y-m-d', strtotime("-{$dayCount} days"));
            $yesterdayAttendance = $this->getYesterdayAttendance($yesterday);
            $weekday = date("l", strtotime($yesterday));
            $weekday = strtolower($weekday);

            if ($yesterdayAttendance) {
                foreach ($yesterdayAttendance as $attendance) {
                    $biometric_id = $attendance["biometric_id"];
                    $device_id = $attendance["device_id"];
                    $att_datetime = $attendance["datetime"];
                    $meredien = date_format(date_create($attendance["datetime"]), "A");

                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $this->db->from("gcctimeutility.location");
                        $this->db->where("id", $personnel->location_id);
                        $this->db->where("allow_notification", 1);
                        $queryLocation = $this->db->get();
                        if ($queryLocation->num_rows() == 1) {
                            $data = array();
                            if (intval($personnel->is_flexi) !== 1) {
                                $shift = $personnel->shift_id;
                                $personnel_biometricno = $personnel->biometricno;

                                $shiftResource = $this->getShiftResource($shift);
                                if ($shiftResource) {
                                    $ids = $shiftResource->shift_resource;
                                    $todayShift = array();
                                    if ($ids) {
                                        foreach ($ids as $id) {
                                            $todayDR = $this->getDailyResource($id, $weekday);
                                            if ($todayDR) {
                                                $todayShift = $todayDR;
                                                break;
                                            }
                                        }
                                    }

                                    if ($todayShift) {
                                        $shiftPmEnd = $todayShift->pm_end;
                                        $shiftPmEnd = date("H:i", strtotime($shiftPmEnd));

                                        $undertime = $this->getUndertime($shiftResource->undertime_id);
                                        $reference_no = "N/A";

                                        if ($undertime) {
                                            $amStart = date("H:i", strtotime($undertime->am_start));
                                            $amEnd = date("H:i", strtotime($undertime->am_end));
                                            $pmStart = date("H:i", strtotime($undertime->pm_start));
                                            $pmEnd = date("H:i", strtotime($undertime->pm_end));


                                            if ($pmEnd > $shiftPmEnd) {
                                                $cdate = date("Y-m-d");
                                                $cdate1 = date("Y-m-d H:i", strtotime("{$cdate} {$shiftPmEnd}"));
                                                $cdate2 = date("Y-m-d H:i", strtotime("{$cdate} {$pmEnd}"));

                                                $ccDate1 = date_create($cdate1);
                                                $ccDate2 = date_create($cdate2);

                                                $diff = date_diff($ccDate1, $ccDate2);
                                                $hour = $diff->format("%H");
                                                $min = $diff->format("%i");
                                                if ($hour == 0) {
                                                    $hour += 1;
                                                }

                                                $nn = date("H:i", strtotime($shiftPmEnd . "- {$hour} hour + {$min} minute"));
                                                $pmEnd = $nn;

                                            }

                                            $department = $this->getAttendanceDepartment($personnel->department_id);
                                            $currentDepartment = (isset($department->description) && $department->description) ? $department->description : "N/A";

                                            $data["biometric_id"] = $biometric_id;
                                            $data["device"] = $device_id;
                                            $data["name"] = $personnel->name;
                                            $data["department"] = $currentDepartment;
                                            $data["date"] = date_format(date_create($att_datetime), "n/d/Y");

                                            $loggedTimeRecord = array();

                                            $personnelAttendanceData = $this->getPersonnelAttendanceByDate($yesterday, $biometric_id);
                                            foreach ($personnelAttendanceData as $index => $datax) {
                                                $dateTime = date_format(date_create($datax["datetime"]), "h:i A");
                                                $data["time"] = $dateTime;

                                                $_dateTime = date_format(date_create($datax["datetime"]), "H:i");
                                                $loggedTimeRecord[] = $dateTime;


                                                $getTime = date_format(date_create($datax["datetime"]), "h:i:s A");
                                                $AMCheck = date_format(date_create($getTime), "A");
                                                $data["mrdn"] = $AMCheck;

                                                if (isset($todayShift->am_start, $todayShift->am_end) && $todayShift->am_start !== "00:00:00" && $todayShift->am_end !== "00:00:00" && $AMCheck == "AM") {
                                                    if (($_dateTime >= $amStart) && ($_dateTime <= $amEnd) && ($AMCheck == "AM")) {
                                                        $loa_data = $this->getAvailableLoaUndertime($personnel_biometricno);
                                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                            $reference_no = $loa_data["reference_no"];
                                                        }
                                                        $data["logged_time"] = $loggedTimeRecord;
                                                        $loggedContent = "";
                                                        if ($loggedTimeRecord) {
                                                            $cc = count($loggedTimeRecord);
                                                            $counter = 1;
                                                            foreach ($loggedTimeRecord as $ii => $vv) {
                                                                if ($counter == $cc) {
                                                                    $loggedContent .= "<span style='color: #ff0000; font-weight: bold;'>{$vv}</span>";
                                                                } else {
                                                                    $loggedContent .= "<span style='color: #888888; margin-right: 15px;'>{$vv}</span>";
                                                                }
                                                                $counter++;
                                                            }
                                                        }
                                                        $data["logged_content"] = $loggedContent;
                                                        $data["content"] = $reference_no;
                                                        $resultset["check_undertime_am"][$biometric_id] = $data;
                                                    }
                                                }
                                                if (isset($todayShift->pm_start, $todayShift->pm_end) && $todayShift->pm_start !== "00:00:00" && $todayShift->pm_end !== "00:00:00" && $AMCheck == "PM") {
                                                    if (($_dateTime >= $pmStart) && ($_dateTime <= $pmEnd) && ($AMCheck == "PM")) {
                                                        $loa_data = $this->getAvailableLoaUndertime($personnel_biometricno);
                                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                            $reference_no = $loa_data["reference_no"];
                                                        }
                                                        $data["logged_time"] = $loggedTimeRecord;
                                                        $loggedContent = "";
                                                        if ($loggedTimeRecord) {
                                                            $cc = count($loggedTimeRecord);
                                                            $counter = 1;
                                                            foreach ($loggedTimeRecord as $ii => $vv) {
                                                                if ($counter == $cc) {
                                                                    $loggedContent .= "<span style='color: #ff0000; font-weight: bold;'>{$vv}</span>";
                                                                } else {
                                                                    $loggedContent .= "<span style='color: #888888; margin-right: 15px;'>{$vv}</span>";
                                                                }
                                                                $counter++;
                                                            }
                                                        }
                                                        $data["logged_content"] = $loggedContent;
                                                        $data["content"] = $reference_no;
                                                        $resultset["check_undertime_pm"][$biometric_id] = $data;
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

            return $resultset;
        }

        function getCurrentUndertimeV2() {
            $resultset = array();

            $today = date("Y-m-d", strtotime("2019-07-01"));
            $_weekday = date("l", strtotime($today));
            $_weekday = strtolower($_weekday);
            $dayCount = ($_weekday == "monday") ? 2 : 1;

            $yesterday = date('Y-m-d', strtotime("-{$dayCount} days", strtotime($today)));
            $yesterdayAttendance = $this->getYesterdayAttendance($yesterday);
            $weekday = date("l", strtotime($yesterday));
            $weekday = strtolower($weekday);
            var_dump($today);
            var_dump($yesterday);

            if ($yesterdayAttendance) {
                foreach ($yesterdayAttendance as $attendance) {
                    $biometric_id = $attendance["biometric_id"];
                    $device_id = $attendance["device_id"];
                    $att_datetime = $attendance["datetime"];
                    $meredien = date_format(date_create($attendance["datetime"]), "A");

                    $personnel = $this->getPersonnelShift($biometric_id);

                    if ($personnel) {
                        $data = array();
                        if (intval($personnel->is_flexi) !== 1) {
                            $shift = $personnel->shift_id;
                            $personnel_biometricno = $personnel->biometricno;

                            $shiftResource = $this->getShiftResource($shift);
                            if ($shiftResource) {
                                $ids = $shiftResource->shift_resource;
                                $todayShift = array();
                                if ($ids) {
                                    foreach ($ids as $id) {
                                        $todayDR = $this->getDailyResource($id, $weekday);
                                        if ($todayDR) {
                                            $todayShift = $todayDR;
                                            break;
                                        }
                                    }
                                }

                                if ($todayShift) {
                                    $shiftPmEnd = $todayShift->pm_end;
                                    $shiftPmEnd = date("H:i", strtotime($shiftPmEnd));

                                    $undertime = $this->getUndertime($shiftResource->undertime_id);
                                    $reference_no = "N/A";

                                    if ($undertime) {
                                        $amStart = date("H:i", strtotime($undertime->am_start));
                                        $amEnd = date("H:i", strtotime($undertime->am_end));
                                        $pmStart = date("H:i", strtotime($undertime->pm_start));
                                        $pmEnd = date("H:i", strtotime($undertime->pm_end));


                                        if ($pmEnd > $shiftPmEnd) {
                                            $cdate = date("Y-m-d");
                                            $cdate1 = date("Y-m-d H:i", strtotime("{$cdate} {$shiftPmEnd}"));
                                            $cdate2 = date("Y-m-d H:i", strtotime("{$cdate} {$pmEnd}"));

                                            $ccDate1 = date_create($cdate1);
                                            $ccDate2 = date_create($cdate2);

                                            $diff = date_diff($ccDate1, $ccDate2);
                                            $hour = $diff->format("%H");
                                            $min = $diff->format("%i");
                                            if ($hour == 0) {
                                                $hour += 1;
                                            }

                                            $nn = date("H:i", strtotime($shiftPmEnd . "- {$hour} hour + {$min} minute"));
                                            $pmEnd = $nn;

                                        }

                                        $department = $this->getAttendanceDepartment($personnel->department_id);
                                        $currentDepartment = (isset($department->description) && $department->description) ? $department->description : "N/A";

                                        $data["biometric_id"] = $biometric_id;
                                        $data["device"] = $device_id;
                                        $data["name"] = $personnel->name;
                                        $data["department"] = $currentDepartment;
                                        $data["date"] = date_format(date_create($att_datetime), "n/d/Y");

                                        $loggedTimeRecord = array();

                                        $personnelAttendanceData = $this->getPersonnelAttendanceByDate($yesterday, $biometric_id);
                                        foreach ($personnelAttendanceData as $index => $datax) {
                                            $dateTime = date_format(date_create($datax["datetime"]), "h:i A");
                                            $data["time"] = $dateTime;

                                            $_dateTime = date_format(date_create($datax["datetime"]), "H:i");
                                            $loggedTimeRecord[] = $dateTime;


                                            $getTime = date_format(date_create($datax["datetime"]), "h:i:s A");
                                            $AMCheck = date_format(date_create($getTime), "A");
                                            $data["mrdn"] = $AMCheck;

                                            if (isset($todayShift->am_start, $todayShift->am_end) && $todayShift->am_start !== "00:00:00" && $todayShift->am_end !== "00:00:00" && $AMCheck == "AM") {
                                                if (($_dateTime >= $amStart) && ($_dateTime <= $amEnd) && ($AMCheck == "AM")) {
                                                    $loa_data = $this->getAvailableLoaUndertime($personnel_biometricno);
                                                    if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                        $reference_no = $loa_data["reference_no"];
                                                    }
                                                    $data["logged_time"] = $loggedTimeRecord;
                                                    $loggedContent = "";
                                                    if ($loggedTimeRecord) {
                                                        $cc = count($loggedTimeRecord);
                                                        $counter = 1;
                                                        foreach ($loggedTimeRecord as $ii => $vv) {
                                                            if ($counter == $cc) {
                                                                $loggedContent .= "<span style='color: #ff0000; font-weight: bold;'>{$vv}</span>";
                                                            } else {
                                                                $loggedContent .= "<span style='color: #888888; margin-right: 15px;'>{$vv}</span>";
                                                            }
                                                            $counter++;
                                                        }
                                                    }
                                                    $data["logged_content"] = $loggedContent;
                                                    $data["content"] = $reference_no;
                                                    $resultset["check_undertime_am"][$biometric_id] = $data;
                                                }
                                            }
                                            if (isset($todayShift->pm_start, $todayShift->pm_end) && $todayShift->pm_start !== "00:00:00" && $todayShift->pm_end !== "00:00:00" && $AMCheck == "PM") {
                                                if (($_dateTime >= $pmStart) && ($_dateTime <= $pmEnd) && ($AMCheck == "PM")) {
                                                    $loa_data = $this->getAvailableLoaUndertime($personnel_biometricno);
                                                    if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                        $reference_no = $loa_data["reference_no"];
                                                    }
                                                    $data["logged_time"] = $loggedTimeRecord;
                                                    $loggedContent = "";
                                                    if ($loggedTimeRecord) {
                                                        $cc = count($loggedTimeRecord);
                                                        $counter = 1;
                                                        foreach ($loggedTimeRecord as $ii => $vv) {
                                                            if ($counter == $cc) {
                                                                $loggedContent .= "<span style='color: #ff0000; font-weight: bold;'>{$vv}</span>";
                                                            } else {
                                                                $loggedContent .= "<span style='color: #888888; margin-right: 15px;'>{$vv}</span>";
                                                            }
                                                            $counter++;
                                                        }
                                                    }
                                                    $data["logged_content"] = $loggedContent;
                                                    $data["content"] = $reference_no;
                                                    $resultset["check_undertime_pm"][$biometric_id] = $data;
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

            return $resultset;
        }

        function getPersonalAbsent($biometricId = null) {
            $resultset = array();

            $startDate = date("Y-m-1 H:i:s");
            $todays = $this->adm_attendance->getLastSyncDate();

            $this->db->from($this->absentTable);
            $this->db->where("updated_at >=", $startDate);
            $this->db->where("updated_at <=", $todays);
            $this->db->where("biometric_id", $biometricId);
            $this->db->order_by("id", "DESC");
            $queryget = $this->db->get();
            $getAbsentCollection = $queryget->result_array();

            if ($getAbsentCollection) {
                foreach ($getAbsentCollection as $_getAbsentCollection) {
                    $biometric_id = $_getAbsentCollection["biometric_id"];
                    $current_meredien = $_getAbsentCollection["meredien"];
                    $ndate = date("Y-m-d", strtotime($_getAbsentCollection["updated_at"]));
                    $meredien = date_format(date_create($_getAbsentCollection["updated_at"]), "A");
                    $ampm = strtolower($meredien);

                    $weekday = date("l", strtotime($_getAbsentCollection["updated_at"]));
                    $weekday = strtolower($weekday);

                    $att_index = date("mdY_His", strtotime($_getAbsentCollection["updated_at"]));

                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $current_data = array();
                        $shift = $personnel->shift_id;
                        $personnel_biometricno = $personnel->biometricno;

                        $shiftResource = $this->getShiftResource($shift);
                        if ($shiftResource) {
                            $ids = $shiftResource->shift_resource;
                            $todayShift = array();
                            if ($ids) {
                                foreach ($ids as $id) {
                                    $today = $this->getDailyResource($id, $weekday);
                                    if ($today) {
                                        $todayShift = $today;
                                        break;
                                    }
                                }
                            }

                            if ($todayShift) {
                                $shiftAmStart = $todayShift->am_start;
                                $shiftAmEnd = $todayShift->am_end;
                                $shiftPmStart = $todayShift->pm_start;
                                $shiftPmEnd = $todayShift->pm_end;
                                $reference_no = "N/A";

                                $department = $this->getAttendanceDepartment($personnel->department_id);
                                $currentDepartment = (isset($department->description) && $department->description) ? $department->description : "N/A";
                                $tempType = "Regular";
                                if(intval($personnel->is_flexi) == 1){ $tempType = "Flexible Time"; }
                                else if(intval($personnel->is_flexi) == 2){ $tempType = "1 IN / 1 OUT ONLY"; }
                                else{ $tempType = "Regular"; }

                                $current_data["biometricno"] = $biometric_id;
                                $current_data["name"] = $personnel->name;
                                $current_data["department"] = $currentDepartment;
                                $current_data["date"] = $ndate;
                                $current_data["mrdn"] = $meredien;
                                $current_data["type"] = $tempType;

                                if (($shiftAmStart !== "00:00:00" && $shiftAmStart) && ($shiftAmEnd !== "00:00:00" && $shiftAmEnd) && ($current_meredien == "AM")) {
                                    $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                    $countx = $this->getPersonnelCount($biometric_id, $ndate, $current_meredien);

                                    if ($personnelAttendance == false && $countx == 0) {
                                        $loa_data = $this->getAvailableLoaByDate($personnel_biometricno, $ndate, $current_meredien);
                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                            $reference_no = $loa_data["reference_no"];
                                        }
                                        $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }
                                        $current_data["content"] = $reference_no;
                                        $resultset["absentee"][$att_index] = $current_data;
                                    }
                                }
                                if (($shiftPmStart !== "00:00:00" && $shiftPmStart) && ($shiftPmEnd !== "00:00:00" && $shiftPmEnd) && ($current_meredien == "PM")) {
                                    $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                    $countx = $this->getPersonnelCount($biometric_id, $ndate, $current_meredien);

                                    if ($personnelAttendance == false && $countx == 0) {
                                        $loa_data = $this->getAvailableLoaByDate($personnel_biometricno, $ndate, $current_meredien);
                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                            $reference_no = $loa_data["reference_no"];
                                        }
                                        $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }
                                        $current_data["content"] = $reference_no;
                                        $resultset["absentee"][$att_index] = $current_data;
                                    }

                                    $dataTime = array();
                                    $dataTime["start"] = date("Y-m-d H:i:s", strtotime("{$ndate} 12:01:00"));
                                    $dataTime["end"] = date("Y-m-d H:i:s", strtotime("{$ndate} 13:00:00"));

                                    $afternoonAttendance = $this->getSingleAttendanceByDateRangeData($biometric_id, $dataTime);

                                    if ($afternoonAttendance == true && $countx == 0) {
                                        $loa_data = $this->getAvailableLoaByDate($personnel_biometricno, $ndate, $current_meredien);
                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                            $reference_no = $loa_data["reference_no"];
                                        }
                                        $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }
                                        $current_data["content"] = $reference_no;
                                        $resultset["absentee"][$att_index] = $current_data;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $resultset;
        }

        function getPersonalAbsentByDate($_startDate = null, $_endDate = null, $biometricId = null) {
            $resultset = array();

            $lastSyncDate = $this->adm_attendance->getLastSyncDate();

            $startDate = (isset($_startDate) && $_startDate) ? date("Y-m-d H:i:s", strtotime($_startDate)) : date("Y-m-1 H:i:s");
            $todays = (isset($_endDate) && $_endDate) ? date("Y-m-d H:i:s", strtotime($_endDate)) : $lastSyncDate;

            $this->db->from($this->absentTable);
            $this->db->where("updated_at >=", $startDate);
            $this->db->where("updated_at <=", $todays);
            if ($biometricId) {
                $this->db->where("biometric_id", $biometricId);
            }
            $this->db->order_by("id", "DESC");
            $queryget = $this->db->get();
            $getAbsentCollection = $queryget->result_array();

            if ($getAbsentCollection) {
                foreach ($getAbsentCollection as $_getAbsentCollection) {
                    $biometric_id = $_getAbsentCollection["biometric_id"];
                    $current_meredien = $_getAbsentCollection["meredien"];
                    $ndate = date("Y-m-d", strtotime($_getAbsentCollection["updated_at"]));
                    $meredien = date_format(date_create($_getAbsentCollection["updated_at"]), "A");
                    $ampm = strtolower($meredien);

                    $weekday = date("l", strtotime($_getAbsentCollection["updated_at"]));
                    $weekday = strtolower($weekday);

                    $att_index = date("mdY_His", strtotime($_getAbsentCollection["updated_at"]));

                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $current_data = array();
                        $shift = $personnel->shift_id;
                        $personnel_biometricno = $personnel->biometricno;

                        $shiftResource = $this->getShiftResource($shift);
                        if ($shiftResource) {
                            $ids = $shiftResource->shift_resource;
                            $todayShift = array();
                            if ($ids) {
                                foreach ($ids as $id) {
                                    $today = $this->getDailyResource($id, $weekday);
                                    if ($today) {
                                        $todayShift = $today;
                                        break;
                                    }
                                }
                            }

                            if ($todayShift) {
                                $shiftAmStart = $todayShift->am_start;
                                $shiftAmEnd = $todayShift->am_end;
                                $shiftPmStart = $todayShift->pm_start;
                                $shiftPmEnd = $todayShift->pm_end;
                                $reference_no = "N/A";

                                $department = $this->getAttendanceDepartment($personnel->department_id);
                                $currentDepartment = (isset($department->description) && $department->description) ? $department->description : "N/A";
                                $tempType = "Regular";
                                if(intval($personnel->is_flexi) == 1){ $tempType = "Flexible Time"; }
                                else if(intval($personnel->is_flexi) == 2){ $tempType = "1 IN / 1 OUT ONLY"; }
                                else{ $tempType = "Regular"; }

                                $current_data["biometricno"] = $biometric_id;
                                $current_data["name"] = $personnel->name;
                                $current_data["department"] = $currentDepartment;
                                $current_data["date"] = $ndate;
                                $current_data["mrdn"] = $meredien;
                                $current_data["type"] = $tempType;

                                $holidayResponse = $this->getScheduledEventByDate($startDate, $todays, $ndate);

                                if (($shiftAmStart !== "00:00:00" && $shiftAmStart) && ($shiftAmEnd !== "00:00:00" && $shiftAmEnd) && ($current_meredien == "AM") && $holidayResponse == false) {
                                    $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                    $countx = $this->getPersonnelCount($biometric_id, $ndate, $current_meredien);

                                    if ($personnelAttendance == false && $countx == 0) {
                                        $loa_data = $this->getAvailableLoaByDate($personnel_biometricno, $ndate, $current_meredien);
                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                            $reference_no = $loa_data["reference_no"];
                                        }
                                        $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }
                                        $current_data["content"] = $reference_no;
                                        $resultset["absentee"][$att_index] = $current_data;
                                    }
                                }
                                if (($shiftPmStart !== "00:00:00" && $shiftPmStart) && ($shiftPmEnd !== "00:00:00" && $shiftPmEnd) && ($current_meredien == "PM") && $holidayResponse == false) {
                                    $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                    $countx = $this->getPersonnelCount($biometric_id, $ndate, $current_meredien);

                                    if ($personnelAttendance == false && $countx == 0) {
                                        $loa_data = $this->getAvailableLoaByDate($personnel_biometricno, $ndate, $current_meredien);
                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                            $reference_no = $loa_data["reference_no"];
                                        }
                                        $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }
                                        $current_data["content"] = $reference_no;
                                        $resultset["absentee"][$att_index] = $current_data;
                                    }

                                    $dataTime = array();
                                    $dataTime["start"] = date("Y-m-d H:i:s", strtotime("{$ndate} 12:01:00"));
                                    $dataTime["end"] = date("Y-m-d H:i:s", strtotime("{$ndate} 13:00:00"));

                                    $afternoonAttendance = $this->getSingleAttendanceByDateRangeData($biometric_id, $dataTime);

                                    if ($afternoonAttendance == true && $countx == 0) {
                                        $loa_data = $this->getAvailableLoaByDate($personnel_biometricno, $ndate, $current_meredien);
                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                            $reference_no = $loa_data["reference_no"];
                                        }
                                        $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }
                                        $current_data["content"] = $reference_no;
                                        $resultset["absentee"][$att_index] = $current_data;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $resultset;
        }

        function getCurrentAbsentV2() {
            $resultset = array();

            $todays = $this->adm_attendance->getLastSyncDate();
            $todays = date("Y-m-d H:i:00", strtotime("2019-05-15 09:31:00"));

            $ndate = date("Y-m-d", strtotime($todays));

            $this->db->from($this->absentTable);
            $this->db->like("updated_at", $ndate);
            $this->db->order_by("id", "DESC");
            $queryget = $this->db->get();
            $getAbsentCollection = $queryget->result_array();

            $data = array();
            $checker = array();

            $weekday = date("l", strtotime($todays));
            $weekday = strtolower($weekday);

            if ($getAbsentCollection) {
                foreach ($getAbsentCollection as $_getAbsentCollection) {
                    $biometric_id = $_getAbsentCollection["biometric_id"];
                    $current_meredien = $_getAbsentCollection["meredien"];
                    $meredien = date_format(date_create($_getAbsentCollection["updated_at"]), "A");
                    $ampm = strtolower($meredien);

                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $current_data = array();
                        $shift = $personnel->shift_id;
                        $personnel_biometricno = $personnel->biometricno;
                        $isFlexibleTime = (intval($personnel->is_flexi) == 1) ? true : false;

                        $shiftResource = $this->getShiftResource($shift);
                        if ($shiftResource) {
                            $ids = $shiftResource->shift_resource;
                            $todayShift = array();
                            if ($ids) {
                                foreach ($ids as $id) {
                                    $today = $this->getDailyResource($id, $weekday);
                                    if ($today) {
                                        $todayShift = $today;
                                        break;
                                    }
                                }
                            }

                            if ($todayShift) {
                                $shiftAmStart = $todayShift->am_start;
                                $shiftAmEnd = $todayShift->am_end;
                                $shiftPmStart = $todayShift->pm_start;
                                $shiftPmEnd = $todayShift->pm_end;
                                $reference_no = "N/A";

                                $department = $this->getAttendanceDepartment($personnel->department_id);
                                $currentDepartment = (isset($department->description) && $department->description) ? $department->description : "N/A";
                                $tempType = "Regular";
                                if(intval($personnel->is_flexi) == 1){ $tempType = "Flexible Time"; }
                                else if(intval($personnel->is_flexi) == 2){ $tempType = "1 IN / 1 OUT ONLY"; }
                                else{ $tempType = "Regular"; }

                                $current_data["biometricno"] = $biometric_id;
                                $current_data["name"] = $personnel->name;
                                $current_data["department"] = $currentDepartment;
                                $current_data["mrdn"] = $meredien;
                                $current_data["type"] = $tempType;

                                if (($shiftAmStart !== "00:00:00" && $shiftAmStart) && ($shiftAmEnd !== "00:00:00" && $shiftAmEnd) && ($current_meredien == "AM")) {
                                    $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                    $countx = $this->getPersonnelCount($biometric_id, $ndate, $current_meredien);
                                    /*** $countx = $this->getPersonnelCount($biometric_id, $ndate); ***/

                                    if ($personnelAttendance == false && $countx == 0) {
                                        $loa_data = $this->getAvailableLoa($personnel_biometricno, $ndate);
                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                            $reference_no = $loa_data["reference_no"];
                                        }
                                        $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }
                                        $current_data["content"] = $reference_no;
                                        $data[$ampm][$biometric_id] = $current_data;
                                    }
                                }
                                if (($shiftPmStart !== "00:00:00" && $shiftPmStart) && ($shiftPmEnd !== "00:00:00" && $shiftPmEnd) && ($current_meredien == "PM")) {
                                    $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                    $countx = $this->getPersonnelCount($biometric_id, $ndate, $current_meredien);
                                    /*** $countx = $this->getPersonnelCount($biometric_id, $ndate); ***/

                                    if ($personnelAttendance == false && $countx == 0) {
                                        $loa_data = $this->getAvailableLoa($personnel_biometricno, $ndate);
                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                            $reference_no = $loa_data["reference_no"];
                                        }
                                        $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }
                                        $current_data["content"] = $reference_no;
                                        $data[$ampm][$biometric_id] = $current_data;
                                    }

                                    $dataTime = array();
                                    $dataTime["start"] = date("Y-m-d H:i:s", strtotime("{$ndate} 12:00:00"));
                                    $dataTime["end"] = date("Y-m-d H:i:s", strtotime("{$ndate} 13:00:00"));

                                    $afternoonAttendance = $this->getSingleAttendanceByDateRangeData($biometric_id, $dataTime);

                                    if ($afternoonAttendance == true && $countx == 0) {
                                        $loa_data = $this->getAvailableLoa($personnel_biometricno, $ndate);
                                        if (isset($loa_data["response"]) && $loa_data["response"]) {
                                            $reference_no = $loa_data["reference_no"];
                                        }
                                        $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }
                                        $current_data["content"] = $reference_no;
                                        $data[$ampm][$biometric_id] = $current_data;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $resultset["check_absent"] = $data;
            return $resultset;
        }

        function getCurrentAbsent($limit = null) {
            $resultset = array();
            $data = array();
            $amFlag = false;
            $pmFlag = false;
            $afternoonFlag = false;
            
            $todays = $this->adm_attendance->getLastSyncDate();
            $ndate = ($todays) ? date("Y-m-d", strtotime($todays)) : date("Y-m-d");

            $availableLoa = $this->getAvailableLoav3($ndate);
            $this->db->reset_query();

            $currentAttendance = $this->getAllAttendance($ndate);
            $this->db->reset_query();

            $personnelTempAttendance = $this->getPersonneCurrentAttendance($ndate);
            $this->db->reset_query();

            $this->db->select('a.biometric_id, a.meredien, a.updated_at, b.id as emp_id');
            $this->db->join('gccmaster.tblemployees as b', 'a.biometric_id = b.biometricno', 'INNER');
            $this->db->from($this->absentTable.' as a');
            $this->db->where("DATE(updated_at)", $ndate);
            $this->db->order_by("a.id", "DESC");
            $queryget = $this->db->get();

            $weekday = strtolower(date('l', strtotime($todays)));
            
            if ($queryget->num_rows() > 0) {
                foreach ($queryget->result() as $_getAbsentCollection) {
                    $biometric_id = $_getAbsentCollection->biometric_id;
                    $current_meredien = $_getAbsentCollection->meredien;
                    $empId = $_getAbsentCollection->emp_id;
                    $meredien = date('A', strtotime($_getAbsentCollection->updated_at));
                    $ampm = strtolower($meredien);

                    $personnel = $this->getPersonnelShift($biometric_id, true);
                    $this->db->reset_query();

                    if ($personnel) {
                        $department = $this->getPersonnelDepartment($personnel->biometricno);
                        $position = $this->getPersonnelPosition($personnel->biometricno);
                        $station = $this->getPersonnelStation($personnel->biometricno);

                        $this->db->select('id');
                        $this->db->from("gcctimeutility.location");
                        $this->db->where("id", $personnel->location_id);
                        $this->db->where("allow_notification", 1);
                        $queryLocation = $this->db->get();

                        if ($queryLocation->num_rows() == 1) {
                            $this->db->reset_query();

                            $shift = $personnel->shift_id;
                            $personnel_biometricno = $personnel->biometricno;
                            $isFlexibleTime = (intval($personnel->is_flexi) == 1) ? true : false;

                            $shiftResource = $this->getShiftResource($shift, true);
                            $ids = $shiftResource->shift_resource;
                            $todayShift = array();

                            if ($ids) {
                                foreach ($ids as $id) {
                                    $today = $this->getDailyResource($id, $weekday, true);
                                    if ($today) {
                                        $todayShift = $today;
                                        break;
                                    }
                                }
                            }

                            $this->db->reset_query();

                            if ($todayShift) {
                                $shiftAmStart = $todayShift->am_start;
                                $shiftAmEnd = $todayShift->am_end;
                                $shiftPmStart = $todayShift->pm_start;
                                $shiftPmEnd = $todayShift->pm_end;
                                $reference_no = "N/A";

                                $isFlexibleEmployeeTime = ($isFlexibleTime && !empty($personnelTempAttendance) && (isset($personnelTempAttendance[$biometric_id]) && $personnelTempAttendance[$biometric_id])) ? true : false;

                                $tempType = "Regular";
                                if(intval($personnel->is_flexi) == 1){ $tempType = "Flexible Time"; }
                                else if(intval($personnel->is_flexi) == 2){ $tempType = "1 IN / 1 OUT ONLY"; }
                                else{ $tempType = "Regular"; }

                                $current_data["biometricno"] = $biometric_id;
                                $current_data["name"] = $personnel->name ? strtoupper($personnel->name) : $biometric_id;
                                $current_data["department"] = ($department) ? $department : "N/A";
                                $current_data["position"] = ($position) ? $position : "N/A";
                                $current_data["station"] = ($station) ? $station : "NO STATION";
                                $current_data["mrdn"] = $meredien;
                                $current_data["type"] = $tempType;

                                if (($shiftAmStart !== "00:00:00" && $shiftAmStart) && ($shiftAmEnd !== "00:00:00" && $shiftAmEnd) && ($current_meredien == "AM")) {
                                    $amFlag = false;
                                    $amCount = 0;

                                    if (isset($personnelTempAttendance[$biometric_id]) && $personnelTempAttendance[$biometric_id]) {
                                        foreach ($personnelTempAttendance[$biometric_id]['meredien'] as $key => $value) {
                                            if ($value == 'am'){
                                                $amFlag = true;
                                            }
                                        }
                                    }

                                    if (isset($personnelTempAttendance[$biometric_id]['count']['am']) && $personnelTempAttendance[$biometric_id]['count']['am']) {
                                        $amCount = $personnelTempAttendance[$biometric_id]['count']['am'] % 2;
                                    }

                                    if ($amFlag == false && $amCount == 0) {
                                        if (isset($availableLoa[$empId]) && $availableLoa[$empId]) {
                                            $reference_no = $availableLoa[$empId]->reference_no;
                                        }
                                
                                        $toData = $this->getAvailableToByDatev2($personnel_biometricno, $ndate);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];    
                                        }
                                
                                        $current_data["content"] = $reference_no;
                                        $data[$ampm][$biometric_id] = $current_data;
                                    }
                                }

                                if (($shiftPmStart !== "00:00:00" && $shiftPmStart) && ($shiftPmEnd !== "00:00:00" && $shiftPmEnd) && ($current_meredien == "PM")) {
                                    $pmFlag = false;
                                    $pmCount = 0;

                                    if (isset($personnelTempAttendance[$biometric_id]) && $personnelTempAttendance[$biometric_id]) {
                                        foreach ($personnelTempAttendance[$biometric_id]['meredien'] as $key => $value) {
                                            if ($value == 'pm'){
                                                $pmFlag = true;
                                            }
                                        }
                                    }

                                    if (isset($personnelTempAttendance[$biometric_id]['count']['pm']) && $personnelTempAttendance[$biometric_id]['count']['pm']) {
                                        $amCount = $personnelTempAttendance[$biometric_id]['count']['pm'] % 2;
                                    }

                                    if ($pmFlag == false && $pmCount == 0) {
                                        if (isset($availableLoa[$empId]) && $availableLoa[$empId]) {
                                            $reference_no = $availableLoa[$empId]->reference_no;
                                        }

                                        $toData = $this->getAvailableToByDatev2($personnel_biometricno, $ndate, $empId);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }

                                        $current_data["content"] = $reference_no;
                                        if ($isFlexibleEmployeeTime){
                                            $data[$ampm][$biometric_id] = $current_data;
                                        }
                                    }

                                    $dataTime = array();
                                    $dataTime["start"] = date("Y-m-d H:i:s", strtotime("{$ndate} 12:00:00"));
                                    $dataTime["end"] = date("Y-m-d H:i:s", strtotime("{$ndate} 13:00:00"));

                                    $afternoonAttendance = $this->getSingleAttendanceByDateRangeDataV2($biometric_id, $dataTime['start'], $dataTime['end'], $currentAttendance);
                                    if ($afternoonAttendance == true && $pmCount == 0) {
                                        if (isset($availableLoa[$empId]) && $availableLoa[$empId]) {
                                            $reference_no = $availableLoa[$empId]->reference_no;
                                        }

                                        $toData = $this->getAvailableToByDatev2($personnel_biometricno, $ndate, $empId);
                                        if (isset($toData["response"]) && $toData["response"]) {
                                            $reference_no = $toData["reference_no"];
                                        }

                                        $current_data["content"] = $reference_no;
                                        if ($isFlexibleEmployeeTime){
                                            $data[$ampm][$biometric_id] = $current_data;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $resultset["check_absent"] = $data;
            $resultset["ndate"] = $ndate;

            // Save first before grouping
            $this->saveAbsenteeReport($resultset);

            /** Group data by station */
            $resultset = $this->groupAbsentByStation($resultset);

            return $resultset;
        }

        function getCurrentAbsent_old($limit = null) {
            $resultset = array();

            // $todays = $this->adm_attendance->getLastSyncDate();
            // $ndate = date("Y-m-d", strtotime($todays));
            $todays = date("Y-m-d H:i:00", strtotime("2026-01-02"));
            $ndate = date("Y-m-d", strtotime($todays));

            // var_dump($ndate);die();

            $this->db->from($this->absentTable);
            $this->db->like("updated_at", $ndate);
            $this->db->order_by("id", "DESC");

            if($limit){
                $this->db->limit($limit);
            }
            
            $queryget = $this->db->get();
            $getAbsentCollection = $queryget->result_array();;

            $data = array();
            $checker = array();

            $weekday = date("l", strtotime($todays));
            $weekday = strtolower($weekday);

            if ($getAbsentCollection) {
                foreach ($getAbsentCollection as $_getAbsentCollection) {
                    $biometric_id = $_getAbsentCollection["biometric_id"];
                    $current_meredien = $_getAbsentCollection["meredien"];
                    $meredien = date_format(date_create($_getAbsentCollection["updated_at"]), "A");
                    $ampm = strtolower($meredien);

                    $personnel = $this->getPersonnelShift($biometric_id);

                    if ($personnel) {
                        $department = $this->getPersonnelDepartment($personnel->biometricno);
                        $position = $this->getPersonnelPosition($personnel->biometricno);
                        $station = $this->getPersonnelStation($personnel->biometricno);
                        
                        $this->db->from("gcctimeutility.location");
                        $this->db->where("id", $personnel->location_id);
                        $this->db->where("allow_notification", 1);
                        $queryLocation = $this->db->get();
                        if ($queryLocation->num_rows() == 1) {
                            $current_data = array();
                            $shift = $personnel->shift_id;
                            $personnel_biometricno = $personnel->biometricno;
                            $isFlexibleTime = (intval($personnel->is_flexi) == 1) ? true : false;

                            $shiftResource = $this->getShiftResource($shift);
                            if ($shiftResource) {
                                $ids = $shiftResource->shift_resource;
                                $todayShift = array();
                                if ($ids) {
                                    foreach ($ids as $id) {
                                        $today = $this->getDailyResource($id, $weekday);
                                        if ($today) {
                                            $todayShift = $today;
                                            break;
                                        }
                                    }
                                }
                                
                                if ($todayShift) {
                                    
                                    /***
                                     * $queryShiftToday = $this->db->get_where("custom_personnel_shift", array("personnel_id"=>$personnel->id, "weekday"=>$weekday));
                                     * if($queryShiftToday->num_rows() == 1){
                                     * $row = $queryShiftToday->row();
                                     * $todayShift->am_start = $row->am_start;
                                     * $todayShift->am_end = $row->am_end;
                                     * $todayShift->pm_start = $row->pm_start;
                                     * $todayShift->pm_end = $row->pm_end;
                                     * }
                                     ***/

                                    $shiftAmStart = $todayShift->am_start;
                                    $shiftAmEnd = $todayShift->am_end;
                                    $shiftPmStart = $todayShift->pm_start;
                                    $shiftPmEnd = $todayShift->pm_end;
                                    $reference_no = "N/A";

                                    // $department = $this->getAttendanceDepartment($personnel->department_id);
                                    // $currentDepartment = (isset($department->description) && $department->description) ? $department->description : "N/A";
                                    // $currentDepartment = (isset($department) && $department) ? $department : "N/A";
                                    $isFlexibleEmployeeTime = $this->isFlexibleAttendance($biometric_id, $ndate, $isFlexibleTime);
                                    $tempType = "Regular";
                                    if(intval($personnel->is_flexi) == 1){ $tempType = "Flexible Time"; }
                                    else if(intval($personnel->is_flexi) == 2){ $tempType = "1 IN / 1 OUT ONLY"; }
                                    else{ $tempType = "Regular"; }

                                    $current_data["biometricno"] = $biometric_id;
                                    $current_data["name"] = $personnel->name;
                                    $current_data["department"] = ($department) ? $department : "N/A";
                                    $current_data["position"] = ($position) ? $position : "N/A";
                                    $current_data["station"] = ($station) ? $station : "NO STATION";
                                    $current_data["mrdn"] = $meredien;
                                    $current_data["type"] = $tempType;

                                    // var_dump($shiftAmStart, $shiftAmEnd, $current_meredien);

                                    if (($shiftAmStart !== "00:00:00" && $shiftAmStart) && ($shiftAmEnd !== "00:00:00" && $shiftAmEnd) && ($current_meredien == "AM")) {
                                        $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                        $countx = $this->getPersonnelCount($biometric_id, $ndate, $current_meredien);
                                        /*** $countx = $this->getPersonnelCount($biometric_id, $ndate); ***/

                                        if ($personnelAttendance == false && $countx == 0) {
                                            $loa_data = $this->getAvailableLoa($personnel_biometricno, $ndate);
                                            if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                $reference_no = $loa_data["reference_no"];
                                            }
                                            $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                            if (isset($toData["response"]) && $toData["response"]) {
                                                $reference_no = $toData["reference_no"];
                                            }
                                            $current_data["content"] = $reference_no;
                                            $data[$ampm][$biometric_id] = $current_data;
                                        }
                                    }
                                    if (($shiftPmStart !== "00:00:00" && $shiftPmStart) && ($shiftPmEnd !== "00:00:00" && $shiftPmEnd) && ($current_meredien == "PM")) {
                                        $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                        $countx = $this->getPersonnelCount($biometric_id, $ndate, $current_meredien);
                                        /*** $countx = $this->getPersonnelCount($biometric_id, $ndate); ***/

                                        if ($personnelAttendance == false && $countx == 0) {
                                            $loa_data = $this->getAvailableLoa($personnel_biometricno, $ndate);
                                            if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                $reference_no = $loa_data["reference_no"];
                                            }
                                            $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                            if (isset($toData["response"]) && $toData["response"]) {
                                                $reference_no = $toData["reference_no"];
                                            }
                                            $current_data["content"] = $reference_no;
                                            if ($isFlexibleEmployeeTime == false) {
                                                $data[$ampm][$biometric_id] = $current_data;
                                            }
                                        }

                                        $dataTime = array();
                                        $dataTime["start"] = date("Y-m-d H:i:s", strtotime("{$ndate} 12:00:00"));
                                        $dataTime["end"] = date("Y-m-d H:i:s", strtotime("{$ndate} 13:00:00"));

                                        $afternoonAttendance = $this->getSingleAttendanceByDateRangeData($biometric_id, $dataTime);

                                        if ($afternoonAttendance == true && $countx == 0) {
                                            $loa_data = $this->getAvailableLoa($personnel_biometricno, $ndate);
                                            if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                $reference_no = $loa_data["reference_no"];
                                            }
                                            $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                            if (isset($toData["response"]) && $toData["response"]) {
                                                $reference_no = $toData["reference_no"];
                                            }
                                            $current_data["content"] = $reference_no;

                                            if ($isFlexibleEmployeeTime == false) {
                                                $data[$ampm][$biometric_id] = $current_data;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $resultset["check_absent"] = $data;
            $resultset["ndate"] = $ndate;

            echo "<pre>";
            print_r($resultset);
            echo "</pre>";
            die();

            // Save first before grouping
            $this->saveAbsenteeReport($resultset);

            /** Group data by station */
            $resultset = $this->groupAbsentByStation($resultset);
            return $resultset;
        }

        function getCurrentAbsent_old_old($limit = null) {
            $resultset = array();

            $todays = $this->adm_attendance->getLastSyncDate();
            $ndate = date("Y-m-d", strtotime($todays));

            $this->db->from($this->absentTable);
            $this->db->like("updated_at", $ndate);
            $this->db->order_by("id", "DESC");

            if($limit){
                $this->db->limit($limit);
            }
            
            $queryget = $this->db->get();
            $getAbsentCollection = $queryget->result_array();

            $data = array();
            $checker = array();

            $weekday = date("l", strtotime($todays));
            $weekday = strtolower($weekday);

            if ($getAbsentCollection) {
                foreach ($getAbsentCollection as $_getAbsentCollection) {
                    $biometric_id = $_getAbsentCollection["biometric_id"];
                    $current_meredien = $_getAbsentCollection["meredien"];
                    $meredien = date_format(date_create($_getAbsentCollection["updated_at"]), "A");
                    $ampm = strtolower($meredien);

                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $this->db->from("gcctimeutility.location");
                        $this->db->where("id", $personnel->location_id);
                        $this->db->where("allow_notification", 1);
                        $queryLocation = $this->db->get();
                        if ($queryLocation->num_rows() == 1) {
                            $current_data = array();
                            $shift = $personnel->shift_id;
                            $personnel_biometricno = $personnel->biometricno;
                            $isFlexibleTime = (intval($personnel->is_flexi) == 1) ? true : false;

                            $shiftResource = $this->getShiftResource($shift);
                            if ($shiftResource) {
                                $ids = $shiftResource->shift_resource;
                                $todayShift = array();
                                if ($ids) {
                                    foreach ($ids as $id) {
                                        $today = $this->getDailyResource($id, $weekday);
                                        if ($today) {
                                            $todayShift = $today;
                                            break;
                                        }
                                    }
                                }

                                if ($todayShift) {
                                    /***
                                     * $queryShiftToday = $this->db->get_where("custom_personnel_shift", array("personnel_id"=>$personnel->id, "weekday"=>$weekday));
                                     * if($queryShiftToday->num_rows() == 1){
                                     * $row = $queryShiftToday->row();
                                     * $todayShift->am_start = $row->am_start;
                                     * $todayShift->am_end = $row->am_end;
                                     * $todayShift->pm_start = $row->pm_start;
                                     * $todayShift->pm_end = $row->pm_end;
                                     * }
                                     ***/

                                    $shiftAmStart = $todayShift->am_start;
                                    $shiftAmEnd = $todayShift->am_end;
                                    $shiftPmStart = $todayShift->pm_start;
                                    $shiftPmEnd = $todayShift->pm_end;
                                    $reference_no = "N/A";

                                    $department = $this->getAttendanceDepartment($personnel->department_id);
                                    $currentDepartment = (isset($department->description) && $department->description) ? $department->description : "N/A";
                                    $isFlexibleEmployeeTime = $this->isFlexibleAttendance($biometric_id, $ndate, $isFlexibleTime);
                                    $tempType = "Regular";
                                    if(intval($personnel->is_flexi) == 1){ $tempType = "Flexible Time"; }
                                    else if(intval($personnel->is_flexi) == 2){ $tempType = "1 IN / 1 OUT ONLY"; }
                                    else{ $tempType = "Regular"; }

                                    $current_data["biometricno"] = $biometric_id;
                                    $current_data["name"] = $personnel->name;
                                    $current_data["department"] = $currentDepartment;
                                    $current_data["mrdn"] = $meredien;
                                    $current_data["type"] = $tempType;

                                    if (($shiftAmStart !== "00:00:00" && $shiftAmStart) && ($shiftAmEnd !== "00:00:00" && $shiftAmEnd) && ($current_meredien == "AM")) {
                                        $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                        $countx = $this->getPersonnelCount($biometric_id, $ndate, $current_meredien);
                                        /*** $countx = $this->getPersonnelCount($biometric_id, $ndate); ***/

                                        if ($personnelAttendance == false && $countx == 0) {
                                            $loa_data = $this->getAvailableLoa($personnel_biometricno, $ndate);
                                            if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                $reference_no = $loa_data["reference_no"];
                                            }
                                            $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                            if (isset($toData["response"]) && $toData["response"]) {
                                                $reference_no = $toData["reference_no"];
                                            }
                                            $current_data["content"] = $reference_no;
                                            $data[$ampm][$biometric_id] = $current_data;
                                        }
                                    }
                                    if (($shiftPmStart !== "00:00:00" && $shiftPmStart) && ($shiftPmEnd !== "00:00:00" && $shiftPmEnd) && ($current_meredien == "PM")) {
                                        $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                        $countx = $this->getPersonnelCount($biometric_id, $ndate, $current_meredien);
                                        /*** $countx = $this->getPersonnelCount($biometric_id, $ndate); ***/

                                        if ($personnelAttendance == false && $countx == 0) {
                                            $loa_data = $this->getAvailableLoa($personnel_biometricno, $ndate);
                                            if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                $reference_no = $loa_data["reference_no"];
                                            }
                                            $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                            if (isset($toData["response"]) && $toData["response"]) {
                                                $reference_no = $toData["reference_no"];
                                            }
                                            $current_data["content"] = $reference_no;
                                            if ($isFlexibleEmployeeTime == false) {
                                                $data[$ampm][$biometric_id] = $current_data;
                                            }
                                        }

                                        $dataTime = array();
                                        $dataTime["start"] = date("Y-m-d H:i:s", strtotime("{$ndate} 12:00:00"));
                                        $dataTime["end"] = date("Y-m-d H:i:s", strtotime("{$ndate} 13:00:00"));

                                        $afternoonAttendance = $this->getSingleAttendanceByDateRangeData($biometric_id, $dataTime);

                                        if ($afternoonAttendance == true && $countx == 0) {
                                            $loa_data = $this->getAvailableLoa($personnel_biometricno, $ndate);
                                            if (isset($loa_data["response"]) && $loa_data["response"]) {
                                                $reference_no = $loa_data["reference_no"];
                                            }
                                            $toData = $this->getAvailableToByDate($personnel_biometricno, $ndate);
                                            if (isset($toData["response"]) && $toData["response"]) {
                                                $reference_no = $toData["reference_no"];
                                            }
                                            $current_data["content"] = $reference_no;

                                            if ($isFlexibleEmployeeTime == false) {
                                                $data[$ampm][$biometric_id] = $current_data;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $resultset["check_absent"] = $data;
            $resultset["ndate"] = $ndate;
            return $resultset;
        }

        public function isFlexibleAttendance($biometric_id = null, $currentDate = null, $isFlexibleTime = false) {
            if ($isFlexibleTime) {
                $this->db->from("gcctimeutility.attendance");
                $this->db->where("biometric_id", $biometric_id);
                $this->db->like("datetime", date("Y-m-d", strtotime($currentDate)), "both");
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        public function getCurrentLateByBiometricNo($biometricNo = null, $device_id = null, $attendanceDate = null) {
            $resultset = array();

            if ($biometricNo) {
                $today = date("Y-m-d", strtotime($attendanceDate));
                $device_id = ($device_id) ? $device_id : 0;
                $att_datetime = date("Y-m-d H:i:s", strtotime($attendanceDate));
                $attendance_datetime = date("Y-m-d H:i:s", strtotime($attendanceDate));
                $weekday = date("l", strtotime($att_datetime));
                $weekday = strtolower($weekday);

                $datetime = date("H:i", strtotime($attendanceDate));
                $personnel = $this->getPersonnelShift($biometricNo);
                if ($personnel) {
                    $data = array();
                    if (intval($personnel->is_flexi) !== 1) {
                        $shift = $personnel->shift_id;
                        $shiftResource = $this->getShiftResource($shift);
                        if ($shiftResource) {
                            $ids = $shiftResource->shift_resource;
                            $todayShift = array();
                            if ($ids) {
                                foreach ($ids as $id) {
                                    $todayDR = $this->getDailyResource($id, $weekday);
                                    if ($todayDR) {
                                        $todayShift = $todayDR;
                                        break;
                                    }
                                }
                            }

                            if ($todayShift) {
                                $late = $this->getLate($shiftResource->late_id);
                                if ($late) {
                                    $amStart = date("H:i", strtotime($late->am_start));
                                    $amEnd = date("H:i", strtotime($late->am_end));
                                    $pmStart = date("H:i", strtotime($late->pm_start));
                                    $pmEnd = date("H:i", strtotime($late->pm_end));

                                    $data["biometric_id"] = $biometricNo;
                                    $data["device"] = $device_id;
                                    $data["name"] = $personnel->name;
                                    $data["date"] = date_format(date_create($att_datetime), "n/d/Y");
                                    $data["time"] = date_format(date_create($att_datetime), "h:i A");

                                    $getTime = date_format(date_create($att_datetime), "h:i:s A");
                                    $AMCheck = date_format(date_create($getTime), "A");

                                    $amEndData = (isset($todayShift->am_end) && $todayShift->am_end) ? $todayShift->am_end : "12:00";
                                    $pmEndData = (isset($todayShift->pm_end) && $todayShift->pm_end) ? $todayShift->pm_end : "18:00";

                                    $amEndLimit = date("H:i", strtotime("-1 minute", strtotime($amEndData)));
                                    $pmEndLimit = date("H:i", strtotime("-1 minute", strtotime($pmEndData)));

                                    if (($datetime >= $amStart) && ($datetime <= $amEnd) && ($AMCheck == "AM")) {
                                        $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$amStart}"));
                                        $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                        $xDateStart = date_create($xDateStart);
                                        $xDateEnd = date_create($xDateEnd);
                                        $diff = date_diff($xDateStart, $xDateEnd);

                                        $data["minlate"] = $diff->format("%i");
                                        $data["minlate"] += 1;

                                        if ($data["minlate"] == 1) {
                                            $data["minlate"] = $data["minlate"] . " Min";
                                        } else if ($data["minlate"] > 1) {
                                            $data["minlate"] = $data["minlate"] . " Mins";
                                        }

                                        $resultset["response"] = true;
                                        $resultset["am"][$biometricNo] = $data;
                                    }

                                    if (($datetime >= $pmStart) && ($datetime <= $pmEnd) && ($AMCheck == "PM")) {
                                        $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$pmStart}"));
                                        $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                        $xDateStart = date_create($xDateStart);
                                        $xDateEnd = date_create($xDateEnd);

                                        $diff = date_diff($xDateStart, $xDateEnd);

                                        $data["minlate"] = $diff->format("%i");
                                        $data["minlate"] += 1;

                                        if ($data["minlate"] == 1) {
                                            $data["minlate"] = $data["minlate"] . " Min";
                                        } else if ($data["minlate"] > 1) {
                                            $data["minlate"] = $data["minlate"] . " Mins";
                                        }

                                        $resultset["response"] = true;
                                        $resultset["pm"][$biometricNo] = $data;
                                    }

                                    if (($datetime >= $amStart) && ($datetime <= $amEndLimit) && ($AMCheck == "AM")) {
                                        $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$amStart}"));
                                        $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                        $xDateStart = date_create($xDateStart);
                                        $xDateEnd = date_create($xDateEnd);
                                        $diff = date_diff($xDateStart, $xDateEnd);

                                        $data["minlate"] = $diff->format("%i");
                                        $data["minlate"] += 1;

                                        if ($data["minlate"] == 1) {
                                            $data["minlate"] = $data["minlate"] . " Min";
                                        } else if ($data["minlate"] > 1) {
                                            $data["minlate"] = $data["minlate"] . " Mins";
                                        }

                                        $resultset["response"] = true;
                                        $resultset["am"][$biometricNo] = $data;
                                    }

                                    if (($datetime >= $pmStart) && ($datetime <= $pmEndLimit) && ($AMCheck == "PM")) {
                                        $xDateStart = date("Y-m-d H:i:s", strtotime("{$today} {$pmStart}"));
                                        $xDateEnd = date("Y-m-d H:i:s", strtotime($attendance_datetime));
                                        $xDateStart = date_create($xDateStart);
                                        $xDateEnd = date_create($xDateEnd);

                                        $diff = date_diff($xDateStart, $xDateEnd);

                                        $data["minlate"] = $diff->format("%i");
                                        $data["minlate"] += 1;

                                        if ($data["minlate"] == 1) {
                                            $data["minlate"] = $data["minlate"] . " Min";
                                        } else if ($data["minlate"] > 1) {
                                            $data["minlate"] = $data["minlate"] . " Mins";
                                        }

                                        $resultset["response"] = true;
                                        $resultset["pm"][$biometricNo] = $data;
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
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getNoAssignedShift() {
            $resultset = array();

            $today = date("Y-m-d");
            $currentDateToday = date("Y-m-d H:i:s");
            $timeToday = date_format(date_create($currentDateToday), "h:i:s A");

            $begin = new DateTime($today);
            $begin = $begin->format('Y-m-d H:i:s');

            $tom = new DateTime($today);
            $tom = $tom->modify('+1 day');

            $newtime = $tom->format('Y-m-d H:i:s');
            $newtime = new DateTime($newtime);
            $newtime = $newtime->modify("-1 second");
            $newtime = $newtime->format('Y-m-d H:i:s');

            $dates = array($begin, $newtime);
            $getAttendanceByDateRange = $this->adm_attendance->getAttendanceByDateRange($dates, array());

            $weekday = date("l", strtotime($today));
            $weekday = strtolower($weekday);

            if ($getAttendanceByDateRange->num_rows() > 0) {
                foreach ($getAttendanceByDateRange->result_array() as $_getAttendanceByDateRange) {
                    $biometric_id = $_getAttendanceByDateRange["biometric_id"];
                    $device_id = $_getAttendanceByDateRange["device_id"];
                    $att_datetime = $_getAttendanceByDateRange["datetime"];
                    $attendance_datetime = $_getAttendanceByDateRange["datetime"];

                    $datetime = date("H:i", strtotime($attendance_datetime));
                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $data = array();
                        $shift = $personnel->shift_id;
                        $shiftResource = $this->getShiftResource($shift);
                        if ($shiftResource) {
                            $ids = $shiftResource->shift_resource;
                            $todayShift = array();
                            if ($ids) {
                                foreach ($ids as $id) {
                                    $todayDR = $this->getDailyResource($id, $weekday);
                                    if ($todayDR) {
                                        $todayShift = $todayDR;
                                        break;
                                    }
                                }
                            }

                            if (empty($todayShift)) {
                                $department = $this->getAttendanceDepartment($personnel->department_id);
                                $currentDepartment = (isset($department->description) && $department->description) ? $department->description : "N/A";

                                $data["biometric_id"] = $personnel->biometric_id;
                                $data["name"] = $personnel->name;
                                $data["department"] = $currentDepartment;
                                $resultset["nas_today"][$biometric_id] = $data;
                            }
                        }
                    } else {
                        $data = $this->getPersonnelData($biometric_id);
                        if ($data) {
                            $department = $this->getAttendanceDepartment($data->department_id);
                            $currentDepartment = (isset($department->description) && $department->description) ? $department->description : "N/A";

                            $nasData = array();
                            $nasData["biometric_id"] = $biometric_id;
                            $nasData["name"] = $data->name;
                            $nasData["department"] = $currentDepartment;
                            $resultset["nas"][$biometric_id] = $nasData;
                        }
                    }
                }
            }

            return $resultset;
        }

        function getSingleAttendanceByDateRangeData($biometric_id, $dates = array()) {
            if ($biometric_id) {
                $this->db->from($this->attendanceTable);
                $this->db->where("biometric_id", $biometric_id);
                $this->db->where('datetime >=', date_format(date_create($dates["start"]), "Y-m-d H:i:s"));
                $this->db->where('datetime <=', date_format(date_create($dates["end"]), "Y-m-d H:i:s"));
                $query = $this->db->get();

                if ($query->num_rows() == 1 && $query->num_rows() !== 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getSingleAttendanceDoubleEntry($biometric_id = null, $attendance_datetime = null) {
            $response = false;
            if ($biometric_id && $attendance_datetime) {
                $weekday = date("l", strtotime($attendance_datetime));
                $datetime = date("Y-m-d H:i", strtotime($attendance_datetime));

                $personnel = $this->getPersonnelShift($biometric_id);
                if ($personnel) {
                    $data = array();
                    if (intval($personnel->is_flexi) !== 1) {
                        $shift = $personnel->shift_id;
                        $shiftResource = $this->getShiftResource($shift);
                        if ($shiftResource) {
                            $ids = $shiftResource->shift_resource;
                            $todayShift = array();
                            if ($ids) {
                                foreach ($ids as $id) {
                                    $todayDR = $this->getDailyResource($id, $weekday);
                                    if ($todayDR) {
                                        $todayShift = $todayDR;
                                        break;
                                    }
                                }
                            }

                            if ($todayShift) {
                                $this->db->from($this->attendanceTable);
                                $this->db->where("biometric_id", $biometric_id);
                                $this->db->like("datetime", $datetime, "both");
                                $query = $this->db->get();
                                if ($query->num_rows() > 1) {
                                    $response = true;
                                }
                            }
                        }
                    }
                }
            }

            return $response;
        }

        function getSingleAttendanceUndertime($biometric_id = null, $attendance_datetime = null) {
            $response = false;
            if ($biometric_id && $attendance_datetime) {
                $weekday = date("l", strtotime($attendance_datetime));
                $datetime = date("H:i", strtotime($attendance_datetime));
                $today = date("Y-m-d", strtotime($attendance_datetime));

                $personnel = $this->getPersonnelShift($biometric_id);
                if ($personnel) {
                    $data = array();
                    if (intval($personnel->is_flexi) !== 1) {
                        $shift = $personnel->shift_id;
                        $shiftResource = $this->getShiftResource($shift);
                        if ($shiftResource) {
                            $ids = $shiftResource->shift_resource;
                            $todayShift = array();
                            if ($ids) {
                                foreach ($ids as $id) {
                                    $todayDR = $this->getDailyResource($id, $weekday);
                                    if ($todayDR) {
                                        $todayShift = $todayDR;
                                        break;
                                    }
                                }
                            }

                            if ($todayShift) {
                                $shiftPmEnd = $todayShift->pm_end;
                                $shiftPmEnd = date("H:i", strtotime($shiftPmEnd));

                                $undertime = $this->getUndertime($shiftResource->undertime_id);
                                if ($undertime) {
                                    $amStart = date("H:i", strtotime($undertime->am_start));
                                    $amEnd = date("H:i", strtotime($undertime->am_end));
                                    $pmStart = date("H:i", strtotime($undertime->pm_start));
                                    $pmEnd = date("H:i", strtotime($undertime->pm_end));


                                    if ($pmEnd > $shiftPmEnd) {
                                        $cdate1 = date("Y-m-d H:i", strtotime("{$today} {$shiftPmEnd}"));
                                        $cdate2 = date("Y-m-d H:i", strtotime("{$today} {$pmEnd}"));

                                        $ccDate1 = date_create($cdate1);
                                        $ccDate2 = date_create($cdate2);

                                        $diff = date_diff($ccDate1, $ccDate2);
                                        $hour = $diff->format("%H");
                                        $min = $diff->format("%i");
                                        if ($hour == 0) {
                                            $hour += 1;
                                        }

                                        $nn = date("H:i", strtotime($shiftPmEnd . "- {$hour} hour + {$min} minute"));
                                        $pmEnd = $nn;
                                    }

                                    $_dateTime = date_format(date_create($datetime), "H:i");
                                    $getTime = date_format(date_create($datetime), "h:i:s A");
                                    $AMCheck = date_format(date_create($getTime), "A");

                                    if (($_dateTime >= $amStart) && ($_dateTime <= $amEnd) && ($AMCheck == "AM")) {
                                        $response = true;
                                    }

                                    if (($_dateTime >= $pmStart) && ($_dateTime <= $pmEnd) && ($AMCheck == "PM")) {
                                        $response = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $response;
        }

        function getSingleAttendanceAbsent($biometric_id = null, $attendance_datetime = null) {
            $response = false;
            if ($biometric_id && $attendance_datetime) {
                $weekday = date("l", strtotime($attendance_datetime));
                $datetime = date("H:i", strtotime($attendance_datetime));
                $todays = date("Y-m-d", strtotime($attendance_datetime));
                $ndate = date("Y-m-d", strtotime($attendance_datetime));

                $this->db->from($this->absentTable);
                $this->db->where("biometric_id", $biometric_id);
                $this->db->where("DATE(updated_at)", $todays);
                $this->db->order_by("id", "DESC");
                $this->db->limit(1);
                $queryget = $this->db->get();

                if ($queryget->num_rows() == 1) {
                    $row = $queryget->row();

                    $biometric_id = $row->biometric_id;
                    $current_meredien = $row->meredien;
                    $meredien = date_format(date_create($row->updated_at), "A");
                    $ampm = strtolower($meredien);

                    $personnel = $this->getPersonnelShift($biometric_id);
                    if ($personnel) {
                        $current_data = array();
                        $shift = $personnel->shift_id;
                        $personnel_biometricno = $personnel->biometricno;

                        $shiftResource = $this->getShiftResource($shift);
                        if ($shiftResource) {
                            $ids = $shiftResource->shift_resource;
                            $todayShift = array();
                            if ($ids) {
                                foreach ($ids as $id) {
                                    $today = $this->getDailyResource($id, $weekday);
                                    if ($today) {
                                        $todayShift = $today;
                                        break;
                                    }
                                }
                            }

                            if ($todayShift) {
                                $current_data = array();
                                $shiftAmStart = $todayShift->am_start;
                                $shiftAmEnd = $todayShift->am_end;
                                $shiftPmStart = $todayShift->pm_start;
                                $shiftPmEnd = $todayShift->pm_end;

                                if (($shiftAmStart !== "00:00:00" && $shiftAmStart) && ($shiftAmEnd !== "00:00:00" && $shiftAmEnd) && ($current_meredien == "AM")) {
                                    $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                    $countx = $this->getPersonnelCount($biometric_id, $ndate);

                                    if ($personnelAttendance == false && $countx == 0) {
                                        $response = true;
                                    }
                                }

                                if (($shiftPmStart !== "00:00:00" && $shiftPmStart) && ($shiftPmEnd !== "00:00:00" && $shiftPmEnd) && ($current_meredien == "PM")) {
                                    $personnelAttendance = $this->getPersonnelAttendance($biometric_id, $ndate, $current_meredien);
                                    $countx = $this->getPersonnelCount($biometric_id, $ndate);

                                    if ($personnelAttendance == false && $countx == 0) {
                                        $response = true;
                                    }

                                    $dataTime = array();
                                    $dataTime["start"] = date("Y-m-d H:i:s", strtotime("{$ndate} 12:01:00"));
                                    $dataTime["end"] = date("Y-m-d H:i:s", strtotime("{$ndate} 13:00:00"));

                                    $afternoonAttendance = $this->getSingleAttendanceByDateRangeData($biometric_id, $dataTime);
                                    if ($afternoonAttendance == true && $countx == 0) {
                                        $response = true;
                                    }
                                }
                            }
                        }
                    }

                }
            }

            return $response;
        }

        function getSingleAttendanceLate($biometric_id = null, $attendance_datetime = null) {
            $response = false;
            if ($biometric_id && $attendance_datetime) {
                $weekday = date("l", strtotime($attendance_datetime));
                $datetime = date("H:i", strtotime($attendance_datetime));

                $personnel = $this->getPersonnelShift($biometric_id);
                if ($personnel) {
                    $data = array();
                    if (intval($personnel->is_flexi) !== 1) {
                        $shift = $personnel->shift_id;
                        $shiftResource = $this->getShiftResource($shift);
                        if ($shiftResource) {
                            $ids = $shiftResource->shift_resource;
                            $todayShift = array();
                            if ($ids) {
                                foreach ($ids as $id) {
                                    $todayDR = $this->getDailyResource($id, $weekday);
                                    if ($todayDR) {
                                        $todayShift = $todayDR;
                                        break;
                                    }
                                }
                            }

                            if ($todayShift) {
                                $late = $this->getLate($shiftResource->late_id);
                                if ($late) {
                                    $amStart = date("H:i", strtotime($late->am_start));
                                    $amEnd = date("H:i", strtotime($late->am_end));
                                    $pmStart = date("H:i", strtotime($late->pm_start));
                                    $pmEnd = date("H:i", strtotime($late->pm_end));

                                    $getTime = date_format(date_create($attendance_datetime), "h:i:s A");
                                    $AMCheck = date_format(date_create($getTime), "A");

                                    $amEndData = (isset($todayShift->am_end) && $todayShift->am_end) ? $todayShift->am_end : "12:00";
                                    $pmEndData = (isset($todayShift->pm_end) && $todayShift->pm_end) ? $todayShift->pm_end : "18:00";

                                    $amEndLimit = date("H:i", strtotime("-1 minute", strtotime($amEndData)));
                                    $pmEndLimit = date("H:i", strtotime("-1 minute", strtotime($pmEndData)));

                                    if (isset($todayShift->am_start, $todayShift->am_end) && $todayShift->am_start !== "00:00:00" && $todayShift->am_end !== "00:00:00" && $AMCheck == "AM") {
                                        if (($datetime >= $amStart) && ($datetime <= $amEnd) && ($AMCheck == "AM")) {
                                            $response = true;
                                        }
                                        if (($datetime >= $amStart) && ($datetime <= $amEndLimit) && ($AMCheck == "AM")) {
                                            $response = true;
                                        }
                                    }
                                    if (isset($todayShift->pm_start, $todayShift->pm_end) && $todayShift->pm_start !== "00:00:00" && $todayShift->pm_end !== "00:00:00" && $AMCheck == "PM") {
                                        if (($datetime >= $pmStart) && ($datetime <= $pmEnd) && ($AMCheck == "PM")) {
                                            $response = true;
                                        }
                                        if (($datetime >= $pmStart) && ($datetime <= $pmEndLimit) && ($AMCheck == "PM")) {
                                            $response = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $response;
        }

        /*****************************/
        /*** email section 122018 ***/
        /***************************/

        function emailLateNotification() {
            $dateToday = date("F d, Y");
            $ampm = date("A");

            $currentLate = $this->getCurrentLate();
            $state = (isset($currentLate["current_state"]) && $currentLate["current_state"]) ? $currentLate["current_state"] : $ampm;

            $currentState = ($state) ? strtolower($state) : strtolower($ampm);
            $data = (isset($currentLate["checklate_{$currentState}"]) && $currentLate["checklate_{$currentState}"]) ? $currentLate["checklate_{$currentState}"] : array();

            $sentCount = 0;
            $totalStations = count($data);

            foreach($data as $station => $emp_per_station) {
                $arrData = array();
                $arrData["station_title"] = strtoupper($station);
                $arrData["data"] = [$station => $emp_per_station];
                $arrData["state"] = $state;

                $message = "";
                $message .= $this->load->view("gcctime/templates/email/email-late_template", $arrData, true);

                $module = "gcctime_late_reports";
                $email_title = "Gcctime - Webportal | " . strtoupper($station);
                $content_title = "Late Report - {$dateToday}";
                $content = $message;

                $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);

                if ($sent) {   
                    $sentCount++;
                }
            }

            return ($sentCount === $totalStations && $totalStations > 0);
        }

        function emailLateNotification_old() {
            $dateToday = date("F d, Y");
            $ampm = date("A");

            $currentLate = $this->getCurrentLate();
            $state = (isset($currentLate["current_state"]) && $currentLate["current_state"]) ? $currentLate["current_state"] : $ampm;

            $currentState = ($state) ? strtolower($state) : strtolower($ampm);
            $data = (isset($currentLate["checklate_{$currentState}"]) && $currentLate["checklate_{$currentState}"]) ? $currentLate["checklate_{$currentState}"] : array();

            $arrData = array();
            $arrData["data"] = $data;
            $arrData["state"] = $state;

            $message = "";
            $message .= $this->load->view("gcctime/templates/email/email-late_template", $arrData, true);

            $module = "gcctime_late_reports";
            $email_title = "Gcctime - Webportal";
            $content_title = "Late Report - {$dateToday}";
            $content = $message;

            $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);

            if ($sent) {
                return true;
            } else {
                return false;
            }
        }

        function emailUndertimeNotification($date = null) {
            $ndate = ($date) ? date("Y-m-d", strtotime($date)) : date("Y-m-d");

            $dateToday = date("F d, Y", strtotime($ndate));

            $currentUndertime = $this->getCurrentUndertime($date);
            if ($currentUndertime) {
                $data = array();
                $data["am"] = (isset($currentUndertime["check_undertime_am"]) && $currentUndertime["check_undertime_am"]) ? $currentUndertime["check_undertime_am"] : array();
                $data["pm"] = (isset($currentUndertime["check_undertime_pm"]) && $currentUndertime["check_undertime_pm"]) ? $currentUndertime["check_undertime_pm"] : array();

                $arrData = array();
                $arrData["data"] = $data;

                $message = "";
                $message .= $this->load->view("gcctime/templates/email/email-undertime_template", $arrData, true);

                $module = "gcctime_reports";
                $email_title = "Gcctime - Webportal";
                $content_title = "Undertime Report - {$dateToday}";
                $content = $message;

                $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);

                if ($sent) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $this->core_layout->logNotification("There are no data found on yesterdays undertime!", "success", "gcctimeV2");
                return false;
            }
        }

        function emailAbsentNotification() {
            $dateToday = date("F d, Y");
            $ampm = date("a");

            $currentAbsent = $this->getCurrentAbsent();

            /**
             * Old code of saving the absentee
             * New code of saving the absentee data is inside the function of getCurrentAbsent()
             */
            // $this->saveAbsenteeReport($currentAbsent); 

            $data = (isset($currentAbsent["check_absent"]) && $currentAbsent["check_absent"]) ? $currentAbsent["check_absent"] : array();
            $firstkey = array_key_first($data); // am or pm
            $_data = $data[$firstkey];
            
            $sentCount = 0;
            $totalStations = count($_data);

            if (isset($_data) && is_array($_data) && !empty($_data)) {
                foreach($_data as $station => $emp_per_station) {
                    $arrData = array();
                    $arrData["station_title"] = strtoupper($station);
                    $arrData["data"] = [$station => $emp_per_station];
                    $arrData["meridiem"] = strtoupper($ampm);

                    $message = "";
                    $message .= $this->load->view("gcctime/templates/email/email-absent_template", $arrData, true);

                    $module = "gcctime_absentee_reports";
                    $email_title = "Gcctime - Webportal | " . strtoupper($station);
                    $content_title = "Absentee Report - {$dateToday}";
                    $content = $message;

                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);

                    if ($sent) {
                        $sentCount++;
                    }
                }

                return ($sentCount === $totalStations && $totalStations > 0);
            }

            return false;
        }

        function emailAbsentNotification_old() {
            $dateToday = date("F d, Y");

            $currentAbsent = $this->getCurrentAbsent();
            $this->saveAbsenteeReport($currentAbsent);
            $data = (isset($currentAbsent["check_absent"]) && $currentAbsent["check_absent"]) ? $currentAbsent["check_absent"] : array();

            $arrData = array();
            $arrData["data"] = $data;

            $message = "";
            $message .= $this->load->view("gcctime/templates/email/email-absent_template", $arrData, true);

            $module = "gcctime_absentee_reports";
            $email_title = "Gcctime - Webportal";
            $content_title = "Absentee Report - {$dateToday}";
            $content = $message;

            $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);

            if ($sent) {
                return true;
            } else {
                return false;
            }
        }

        function saveAbsenteeReport($currentAbsent) {
            $this->db->trans_begin();
            $resultSet = array();

            if (isset($currentAbsent["check_absent"])) {
                $am = (isset($currentAbsent["check_absent"]["am"]) && count($currentAbsent["check_absent"]["am"]) > 0)? $currentAbsent["check_absent"]["am"]: array();
                $pm = (isset($currentAbsent["check_absent"]["pm"]) && count($currentAbsent["check_absent"]["pm"]) > 0)? $currentAbsent["check_absent"]["pm"]: array();
                $date = $currentAbsent["ndate"];

                if($am && count($am) > 0){
                    foreach ($am as $_am) {
                        $getEmpId = $this->db->select("emp.id")
                            ->join("gcctimeutility.personnel personnel", "personnel.biometricno = emp.biometricno OR personnel.biometric_id = emp.biometricno")
                            ->where("personnel.biometric_id", $_am["biometricno"])
                            ->or_where("personnel.biometricno", $_am["biometricno"])
                            ->get("gccmaster.tblemployees emp");

                        if($getEmpId->num_rows() == 1){
                            $emp_id = $getEmpId->row()->id;
                            $row = $this->db
                                ->where("emp_id", $emp_id)
                                ->where("date", $date)
                                ->get("gcctimeutility.absentee_report")
                                ->row();

                            if (empty($row) && $emp_id) {
                                $data = array(
                                    "biometric_id" => $_am["biometricno"],
                                    "emp_id" => $emp_id,
                                    "emp_name" => $_am["name"],
                                    "date" => $date,
                                    "am" => 1,
                                    "loa_reference_no" => $_am["content"]
                                );
                                $this->db->insert("gcctimeutility.absentee_report", $data);
                            } else {
                                $this->db->where("id", $row->id);
                                $this->db->set("am", 1);
                                $this->db->update("gcctimeutility.absentee_report");
                            }
                        }

                    }
                }

                if($pm && count($pm) > 0){
                    foreach ($pm as $_pm) {
                        $getEmpId = $this->db->select("emp.id")
                            ->join("gcctimeutility.personnel personnel", "personnel.biometricno = emp.biometricno OR personnel.biometric_id = emp.biometricno")
                            ->where("personnel.biometric_id", $_pm["biometricno"])
                            ->or_where("personnel.biometricno", $_pm["biometricno"])
                            ->get("gccmaster.tblemployees emp");

                        if($getEmpId->num_rows() == 1){
                            $emp_id = $getEmpId->row()->id;
                            $row = $this->db
                                ->where("emp_id", $emp_id)
                                ->where("date", $date)
                                ->get("gcctimeutility.absentee_report")
                                ->row();

                            if (empty($row) && $emp_id) {
                                $data = array(
                                    "biometric_id" => $_pm["biometricno"],
                                    "emp_id" => $emp_id,
                                    "emp_name" => $_pm["name"],
                                    "date" => $date,
                                    "pm" => 1,
                                    "loa_reference_no" => $_pm["content"]
                                );
                                $this->db->insert("gcctimeutility.absentee_report", $data);
                            } else {
                                $this->db->where("id", $row->id);
                                $this->db->set("pm", 1);
                                $this->db->update("gcctimeutility.absentee_report");
                            }
                        }

                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $resultSet["success"] = false;
                    $resultSet["message"] = $this->db->error()["message"];
                    $this->db->trans_rollback();
                } else {
                    $resultSet["success"] = true;
                    $resultSet["message"] = "Absentee report saved.";
                    $this->db->trans_commit();
                }
            }

            return $resultSet;
        }

        function emailLackingDoubleEntryNotification($date = null) {
            $ndate = ($date) ? date("Y-m-d", strtotime($date)) : date("Y-m-d");

            $dateToday = date("F d, Y", strtotime($ndate));

            $lackDoubleEntry = $this->getLackingDoubleEntries($date);

            $message = "";
            $message .= $this->load->view("gcctime/templates/email/email-lacking_template", $lackDoubleEntry, true);

            $module = "gcctime_reports";
            $email_title = "Gcctime - Webportal";
            $content_title = "Lacking and Double Entry Report - {$dateToday}";
            $content = $message;

            $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);

            if ($sent) {
                return true;
            } else {
                return false;
            }
        }

        function resendEmailLimit() {
            $dateToday = date("F d, Y");

            $contentData = "<p>Please manually resend the email on gcctime, the email sending is not functioning properly. Thank you!</p>";

            $data = array();
            $data["header_title"] = "Resend Email Limit";
            $data["email_content"] = $contentData;

            $message = "";
            $message .= $this->load->view("gcctime/templates/email/email-limit_template", $data, true);

            $module = "gcctime_report_limits";
            $email_title = "Gcctime - Webportal";
            $content_title = "Resend Email Error Limit- {$dateToday}";
            $content = $message;

            $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);

            if ($sent) {
                return true;
            } else {
                return false;
            }
        }

        function resyncDataLimit() {
            $dateToday = date("F d, Y");

            $contentData = "<p>Please manually resync data on gcctime, the syncing of data from biometric device is not functioning properly. Thank you!</p>";

            $data = array();
            $data["header_title"] = "Resync Data Limit";
            $data["email_content"] = $contentData;

            $message = "";
            $message .= $this->load->view("gcctime/templates/email/email-limit_template", $data, true);

            $module = "gcctime_report_limits";
            $email_title = "Gcctime - Webportal";
            $content_title = "Resync Data Error Limit - {$dateToday}";
            $content = $message;

            $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);

            if ($sent) {
                return true;
            } else {
                return false;
            }
        }

        /*** email section 122018 ***/

        /** Functions for getting 
         * Department,
         * Position,
         * Station
         * Via biometric no
         *  */
        function getPersonnelDepartment($biometric_no = null) {
            if (empty($biometric_no)) { return false; }

            // Get department reference from employee
            $employee = $this->db
            ->select('department_id AS department_code')
            ->from('gccmaster.tblemployees e')
            ->where('e.biometricno', $biometric_no)
            ->get()
            ->row();

            if (!$employee) { return false; }

            // If already a department code, return it
            if (!is_numeric($employee->department_code)) {
                return strtoupper($employee->department_code);
            }

            // Otherwise, resolve department ID to code
            $department = $this->db
            ->select('code')
            ->from('gcchris.tbldepartments d')
            ->where('d.id', $employee->department_code)
            ->get()
            ->row();


            return $department ? strtoupper($department->code) : false;
        }

        function getPersonnelPosition($biometric_no = null) {
            if (empty($biometric_no)) { return false; }

            // Get department reference from employee
            $employee = $this->db
            ->select('position')
            ->from('gccmaster.tblemployees e')
            ->where('e.biometricno', $biometric_no)
            ->get()
            ->row();

            if (!$employee) { return false; }

            // If already a position, return it
            if (!is_numeric($employee->position)) {
                return strtoupper($employee->position);
            }

            // Otherwise, resolve department ID to code
            $department = $this->db
            ->select('name')
            ->from('gcchris.tblposition p')
            ->where('p.id', $employee->position)
            ->get()
            ->row();


            return $department ? strtoupper($department->name) : false;
        }

        function getPersonnelStation($biometric_no = null) {
            if (empty($biometric_no)) { return false; }

            // Get department reference from employee
            $station = $this->db
            ->select('s.station_description')
            ->from('gccmaster.tblemployees e')
            ->join('gcchris.default_station_location s', 's.employee_id = e.id', 'LEFT')
            ->where('e.biometricno', $biometric_no)
            ->get()
            ->row();

            if (!$station) { return false; }

            return $station ? strtoupper($station->station_description) : false;
        }
        /** Department, Position, Station END */

        /** 
         * START
         * Ths function is used in getting the Late & Absentee email report
         */
        function groupLateByStation($resultset) {
            $grouped = array();

            foreach ($resultset as $key => $records) {
                if (!is_array($records)) {
                    $grouped[$key] = $records;
                    continue;
                }

                $groupedByStation = array();
                foreach ($records as $biometric_id => $record) {
                    $station = $record['station'];

                    if (!isset($groupedByStation[$station])) {
                        $groupedByStation[$station] = array();
                    }

                    $groupedByStation[$station][$biometric_id] = $record;
                }

                $grouped[$key] = $groupedByStation;
            }

            return $grouped;
        }

        function groupAbsentByStation($resultset) {
            if (!isset($resultset['check_absent'])) {
                return $resultset; // Nothing to group
            }

            foreach ($resultset['check_absent'] as $meridian => $records) {
                $groupedByStation = [];

                foreach ($records as $biometric_id => $record) {
                    $station = $record['station'] ?? 'NO STATION';

                    if (!isset($groupedByStation[$station])) {
                        $groupedByStation[$station] = [];
                    }

                    $groupedByStation[$station][$biometric_id] = $record;
                }

                  // replace AM / PM with grouped data
                $resultset['check_absent'][$meridian] = $groupedByStation;
            }

            return $resultset;
        }
        /** END */

        // For getting the getCurrentAbsent
        function getAvailableLoav3($date = null) {
            $resultset = array();

            if ($date) {
                $lastSyncDate = date("Y-m-d", strtotime($date));
                $nowMeredian = date("A");

                $this->db->select('date_from, date_to, reference_no, type, employee');
                $this->db->order_by("id", "desc");

                $this->db->group_start();
                    $this->db->where('DATE(date_from) >=', $lastSyncDate);
                    $this->db->where('DATE(date_to) <=', $lastSyncDate);
                $this->db->group_end();

                $this->db->group_start();
                    $this->db->or_where('DATE(date_from)', $lastSyncDate);
                    $this->db->where('date_to', '0000-00-00');
                $this->db->group_end();

                $query = $this->db->get("gcceforms.loa");

                if ($query->num_rows() > 0) {
                    foreach($query->result() as $row) {
                        $resultset[$row->employee] = $row;
                    }

                } else {
                    $resultset["response"] = false;
                }
            }

            return $resultset;
        }

        function getAllAttendance($currentDate = null){
            $data = array();

            if ($currentDate) {
                $this->db->select('biometric_id, datetime, device_id');
                $this->db->from("gcctimeutility.attendance");
                $this->db->where("DATE(datetime)", date("Y-m-d", strtotime($currentDate)));
                $query = $this->db->get();

                if ($query->num_rows() > 0){
                    $data = $query->result();
                    // foreach($query->result() as $row){
                    //     array_push($data, $row->biometric_id);
                    // }
                }
            }

            return $data;
        }

        function getPersonneCurrentAttendance($currentDate = null, $biometricId = null) {
            $data = array();

            if ($currentDate) {
                $this->db->select('biometric_id, DATE_FORMAT(datetime, "%p") as meredien, datetime');
                $this->db->from($this->attendanceTable);
                $this->db->where("DATE(datetime)", date("Y-m-d", strtotime($currentDate)));

                if ($biometricId) {
                    $this->db->where("biometric_id", $biometricId);
                }

                $query = $this->db->get();

                if($query->num_rows() > 0){
                    $arrData = array();
                    foreach ($query->result() as $key => $rs){
                        $rs->meredien = strtolower($rs->meredien);

                        $data[$rs->biometric_id]['meredien'][] = $rs->meredien;                        
                        $data[$rs->biometric_id]['count'] = array_count_values($data[$rs->biometric_id]['meredien']);
                        $data[$rs->biometric_id]['dates'][] = $rs->datetime;
                    }

                    foreach($data as $key => $value){
                        $data[$key]['meredien'] = array_unique($value['meredien']);
                    }
                }
            }


            return $data;
        }

        function getAvailableToByDateV2($biometric = null, $date = null, $empId = 0) {
            $resultset = array();
            $lastSyncDate = date("Y-m-d", strtotime($date));
            $nowMeredian = date("A");

            if ($biometric && $empId){
                $employeeTo = $this->getEmployeeTo($empId, $lastSyncDate);

                if ($employeeTo) {
                    $resultset["response"] = true;
                    $resultset["reference_no"] = $employeeTo->reference_no;
                } else {
                    $driverTo = $this->getEmployeeToDriver($empId, $lastSyncDate);
                    if ($driverTo) {
                        $resultset["response"] = true;
                        $resultset["reference_no"] = $driverTo->reference_no;
                    }
                }
            } else{
                $resultset['response'] = false;
            }

            return $resultset;
        }

        function getSingleAttendanceByDateRangeDataV2($biometric_id = null, $start_date = null, $end_date = null, $yesterdayAttendance = array()) {
            $flag = false;

            if ($biometric_id) {
                $count = 0;
                foreach($yesterdayAttendance as $key => $value) {
                    if ($value->biometric_id == $biometric_id) {
                        if (date('Y-m-d H:i:s', strtotime($value->datetime)) >= date('Y-m-d H:i:s', strtotime($start_date)) && date('Y-m-d H:i:s', strtotime($value->datetime)) <= date('Y-m-d H:i:s', strtotime($end_date))) {
                            $count++;
                        }
                    }
                }

                if ($count == 1 && $count !== 0) {
                    $flag = true;
                }
            }

            return $flag;
        }
    }