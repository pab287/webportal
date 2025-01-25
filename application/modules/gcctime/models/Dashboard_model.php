<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Dashboard_model extends CI_Model {
        private $today;

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
        protected $lastSynced;

        public function __construct() {
            parent::__construct();
            $this->load->model("gcctime/Biometric_model", "biometric");

            date_default_timezone_set("Asia/Manila");
            $this->today = date("Y-m-d");

            $this->lastSynced = $this->getLastSyncDate();
        }

        function getRealTimeAttendances($date = null, $multiple = 0) {
            $resultSet = array();

            $employees = $this->getEmployeeName();
            $this->db->reset_query();

            if ($date) {
                $this->db->where('DATE(date)', $date);
            }
            
            $this->db->select("biometricno, date");
            $this->db->from('zktime_logs.attendance a');
            $zktime_logs_query = $this->db->get_compiled_select();

            $this->db->reset_query();

            if ($date) {
                $this->db->where('DATE(datetime)', $date);
            }

            $this->db->select("biometric_id as biometricno, datetime as date");
            $this->db->from('gcctimeutility.attendance');
            $gcctime_attendance_query = $this->db->get_compiled_select();

            $this->db->reset_query();

            $query = $this->db->query("SELECT date, biometricno FROM ({$zktime_logs_query} UNION {$gcctime_attendance_query}) AS attendance_logs GROUP BY DATE(date), biometricno ORDER BY date DESC, biometricno ASC");
            
            if ($query->num_rows() > 0){
                $arrData = array();

                foreach ($query->result() as $key => $rs) {
                    $rs->employee_name = isset($employees[$rs->biometricno]) ? $employees[$rs->biometricno] : $rs->biometricno;

                    $arrData[$key] = $rs;
                    $resultSet = $query->result();
                }

                foreach($arrData as $k=>$v){
                    $resultSet[] = $v;
                }
            }

            return $resultSet;
        }

        public function getCurrentLate() {
            $resultset = array();
            // $lastSyncDate = $this->getLastSyncDate();
            $lastSyncDate = $this->lastSynced;
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
            $getAttendanceByDateRange = $this->getAttendanceByDateRange($dates, array());

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

            return $resultset;
        }

        function getCurrentAbsentDashboard($limit = null) {
            $resultset = array();
            $data = array();
            $amFlag = false;
            $pmFlag = false;
            $afternoonFlag = false;
            
            $todays = $this->lastSynced;
            $ndate = ($todays) ? date("Y-m-d", strtotime($todays)) : date("Y-m-d");

            $availableLoa = $this->getAvailableLoav3($ndate);
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

                                    $afternoonAttendance = $this->getSingleAttendanceByDateRangeDatav2($biometric_id, $dataTime['start'], $dataTime['end'], $currentAttendance);
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
            return $resultset;
        }

        function getCurrentTodayUndertime($overrideDate = null) {
            $resultset = array();

            $lastSyncDate = $this->lastSynced;
            $_today = ($overrideDate) ? date("Y-m-d", strtotime($overrideDate)) : date("Y-m-d");
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d", strtotime($_today));

            $_weekday = date("l", strtotime($today));
            $_weekday = strtolower($_weekday);
            $dayCount = ($_weekday == "monday") ? 2 : 1;

            $undertimeList = $this->getUndertimev2();
            $this->db->reset_query();

            $yesterday = ($overrideDate) ? date('Y-m-d', strtotime("-{$dayCount} days", strtotime($overrideDate))) : date('Y-m-d', strtotime("-{$dayCount} days", strtotime($today)));
            $yesterdayAttendance = $this->getYesterdayAttendancev2($yesterday);
            $weekday = date("l", strtotime($yesterday));
            $weekday = strtolower($weekday);

            $this->db->reset_query();

            $_loa = $this->getAvailableLoaUndertimeV3($yesterday);
            $this->db->reset_query();

            if (!empty($yesterdayAttendance)) {
                foreach ($yesterdayAttendance as $attendance) {
                    $biometric_id = $attendance->biometric_id;
                    $device_id = $attendance->device_id;
                    $att_datetime = $attendance->datetime;
                    $empId = $attendance->emp_id;
                    $meredien = date('A', strtotime($attendance->datetime));

                    $personnel = $this->getPersonnelShift($biometric_id);
                    $this->db->reset_query();

                    if ($personnel) {
                        $this->db->select('id');
                        $this->db->from("gcctimeutility.location");
                        $this->db->where("id", $personnel->location_id);
                        $this->db->where("allow_notification", 1);
                        $queryLocation = $this->db->get();

                        $this->db->reset_query();

                        if ($queryLocation->num_rows() == 1) {
                            $data = array();

                            if (intval($personnel->is_flexi) !== 1) {
                                $shift = $personnel->shift_id;
                                $personnel_biometricno = $personnel->biometricno;

                                $shiftResource = $this->getShiftResource($shift);
                                $this->db->reset_query();

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

                                        // $undertime = $this->getUndertime($shiftResource->undertime_id, true);
                                        $undertime = isset($undertimeList[$shiftResource->undertime_id]) ? $undertimeList[$shiftResource->undertime_id] : null;
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

                                            $data["biometric_id"] = $biometric_id;
                                            $data["device"] = $device_id;
                                            $data["name"] = $personnel->name;
                                            $data["date"] = date_format(date_create($att_datetime), "n/d/Y");

                                            $dateTime = date_format(date_create($att_datetime), "h:i A");
                                            $data["time"] = $dateTime;

                                            $_dateTime = date_format(date_create($att_datetime), "H:i");
                                            $loggedTimeRecord[] = $dateTime;

                                            $getTime = date_format(date_create($att_datetime), "h:i:s A");
                                            $AMCheck = date_format(date_create($getTime), "A");
                                            $data["mrdn"] = $AMCheck;

                                            if (isset($todayShift->am_start, $todayShift->am_end) && $todayShift->am_start !== "00:00:00" && $todayShift->am_end !== "00:00:00" && $AMCheck == "AM") {
                                                if (($_dateTime >= $amStart) && ($_dateTime <= $amEnd) && ($AMCheck == "AM")) {
                                                    if (isset($_loa[$empId]) && $_loa[$empId]) {
                                                        $reference_no = $_loa[$empId]->reference_no;
                                                    }

                                                    $data["content"] = $reference_no;
                                                    $resultset["check_undertime_am"][$biometric_id] = $data;
                                                }
                                            }

                                            if (isset($todayShift->pm_start, $todayShift->pm_end) && $todayShift->pm_start !== "00:00:00" && $todayShift->pm_end !== "00:00:00" && $AMCheck == "PM") {
                                                if (($_dateTime >= $pmStart) && ($_dateTime <= $pmEnd) && ($AMCheck == "PM")) {
                                                    if (isset($_loa[$empId]) && $_loa[$empId]) {
                                                        $reference_no = $_loa[$empId]->reference_no;
                                                    }

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

        function getLackingDoubleEntries($overrideDate = null, $type = 'double') {
            $resultset = array();

            $lastSyncDate = $this->lastSynced;
            // $today = ($overrideDate) ? date("Y-m-d", strtotime($overrideDate)) : date("Y-m-d");
            $_today = ($overrideDate) ? date("Y-m-d", strtotime($overrideDate)) : date("Y-m-d");
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d", strtotime($_today));

            $_weekday = date("l", strtotime($today));
            $_weekday = strtolower($_weekday);
            $dayCount = ($_weekday == "monday") ? 2 : 1;

            $this->db->reset_query();

            $yesterday = ($overrideDate) ? date('Y-m-d', strtotime("-{$dayCount} days", strtotime($today))) : date('Y-m-d', strtotime("-{$dayCount} days", strtotime($today)));

            $yesterdayAttendance = $this->getYesterdayAttendancev2($yesterday);
            $weekday = date("l", strtotime($yesterday));
            $weekday = strtolower($weekday);

            $count = array();

            $yesterdayAbsent = $this->getAbsentYesterday($yesterday);

            if ($yesterdayAttendance) {
                $loggedTimeRecord = array();
                
                foreach($yesterdayAttendance as $counter) {
                    if (isset($count[$counter->biometric_id])) {
                        $count[$counter->biometric_id]++;
                    } else {
                        $count[$counter->biometric_id] = 1;
                    }
                    
                    $loggedTimeRecord[$counter->biometric_id][] = $counter->datetime;
                }

                foreach ($yesterdayAttendance as $attendance) {
                    $biometric_id = $attendance->biometric_id;
                    $personnel = $this->getPersonnelShift($biometric_id);
                    $empId = $attendance->emp_id;
                    if ($personnel) {
                        $this->db->select('id');
                        $this->db->from("gcctimeutility.location");
                        $this->db->where("id", $personnel->location_id);
                        $this->db->where("allow_notification", 1);
                        $queryLocation = $this->db->get();

                        if ($queryLocation->num_rows() == 1) {
                            $data = array();
                            if (intval($personnel->is_flexi) == 0) {
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

                                        $totalAttendance = $count[$biometric_id];

                                        $data["biometric_id"] = $personnel->biometric_id;
                                        $data["employee_name"] = (isset($personnel->name) && $personnel->name) ? trim($personnel->name) : $personnel->biometric_id;
                                        $data["time_count"] = $totalAttendance;
                                        $data["logged_time_record"] = array_unique($loggedTimeRecord[$biometric_id]);

                                        $absenteeRecord = $this->getAbsentFlag($biometric_id, $yesterdayAbsent, $yesterday, $todayShift->am_start, $todayShift->am_end, $todayShift->pm_start, $todayShift->pm_end, $yesterdayAttendance, $totalAttendance);
                                        
                                        if ($type == 'double') {
                                            if ($countShift !== 0 && $totalAttendance > $countShift) {
                                                $resultset["double_entry"][$biometric_id] = $data;
                                            }
                                        }

                                        if ($type == 'lacking'){
                                            if ($countShift !== 0 && $totalAttendance < $countShift && $absenteeRecord == false) {
                                                $toData = $this->getAvailableToByDateV2($biometric_id, $yesterday, $empId);
                                                
                                                if (isset($toData["response"]) && $toData["response"]) {
                                                    $reference_no = $toData["reference_no"];
                                                    $data["reference_no"] = $reference_no;
                                                }

                                                $data['flexi'] = $personnel->is_flexi;

                                                $resultset["lacking_entry"][$biometric_id] = $data;
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

        public function getAttendanceByDateRange($dates = array(), $biometric_id) {
            //$query = $this->db->query("SELECT * FROM `attendance` WHERE `datetime` BETWEEN '{$dates[0]}' AND '{$dates[1]}' ORDER BY 'datetime' DESC;");

            $this->db->select("a.biometric_id, a.device_id, a.datetime, b.device_name, b.status");
            $this->db->from("gcctimeutility.attendance a");
            $this->db->join("gcctimeutility.devices b", "b.id = a.device_id", "left");
            $this->db->where('a.datetime >=', date_format(date_create($dates[0]), "Y-m-d H:i:s"));
            $this->db->where('a.datetime <=', date_format(date_create($dates[1]), "Y-m-d H:i:s"));

            if ($biometric_id) {
                $this->db->where("a.biometric_id", $biometric_id);
            }
            $this->db->order_by("a.datetime", "DESC");

            $query = $this->db->get();

            return $query;

        }

        function getEmployeeName(){
            $result = array();
            $this->db->select("UPPER(CONCAT(TRIM(lastname),
                CASE WHEN suffix != 'N/A' AND suffix !='NONE' AND suffix !='' AND suffix IS NOT NULL THEN 
                    CONCAT(' ', suffix) ELSE ''  END, ', ',
                    TRIM(firstname), ' ', 
                CASE WHEN middlename != 'N/A' AND middlename != 'NONE' AND middlename !='' AND middlename IS NOT NULL THEN 
                    CONCAT(middlename, '.') ELSE '' END)) as employee_name, biometricno");

            $this->db->from("gccmaster.tblemployees");
            $this->db->where('employee_status', 'Active');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach($query->result() as $row) {
                    $result[$row->biometricno] = $row->employee_name ? $row->employee_name : $row->biometricno;
                }
            }

            return $result;
        }

        function getPersonnelShift($biometric_id = null, $isSpecific = false) {
            if ($biometric_id) {

                if ($isSpecific) {
                    $this->db->select('location_id, biometricno, is_flexi, shift_id, name, department_id');
                }

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

        function getShiftResource($shift = null, $isSpecific = false) {
            if ($shift) {

                if ($isSpecific) {
                    $this->db->select('shift_resource');
                }

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

        function getDailyResource($id = null, $weekday = null, $isSpecific = false) {
            if ($id && $weekday) {

                if ($isSpecific) {
                    $this->db->select('am_start, am_end, pm_start, pm_end');
                }

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

        function getLastSyncDate($dateTime = false) {
            $last = $this->db
                ->where('event_name', 'sync_data')
                ->order_by('id', "desc")
                ->limit(1)
                ->get('gcctimeutility.event');

            if ($last->num_rows() > 0) {
                $row = $last->row();
                $dateCreated = date_create($row->created_at);

                if ($dateTime) {
                    return date_format($dateCreated, "Y-m-d H:i:s");
                } else {
                    return date_format($dateCreated, "Y-m-d");
                }
            } else {
                if ($dateTime) {
                    return date("Y-m-d H:i:s");
                } else {
                    return date("Y-m-d");
                }
            }
        }

        function getAllAttendance($currentDate = null){
            $data = array();

            if ($currentDate) {
                $this->db->select('biometric_id');
                $this->db->from("gcctimeutility.attendance");
                $this->db->where("DATE(datetime)", date("Y-m-d", strtotime($currentDate)));
                $query = $this->db->get();

                if ($query->num_rows() > 0){
                    foreach($query->result() as $row){
                        array_push($data, $row->biometric_id);
                    }
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

        function getAllDepartments() {
            $result = array();

            $this->db->select('id, description');
            $query = $this->db->get($this->departmentTable);
            if ($query->num_rows() > 0) {
                $_result = $query->result();

                $result = array_column($_result, "description", "id");
            }

            return $result;
        }

        function getAttendanceDepartment($dept_id = null) {
            if ($dept_id) {
                $this->db->select('description');
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

        function getAvailableLoa($id = null, $syncDate = null) {
            $resultset = array();
            if ($id && $syncDate) {
                /*** $syncDate = $this->getLastSyncData(); ***/
                $lastSyncDate = date("Y-m-d", strtotime($syncDate));
                $nowMeredian = date("A");

                $this->db->select('id');
                $empData = $this->db->get_where("gccmaster.tblemployees", array("biometricno" => $id));
                if ($empData->num_rows() == 1) {
                    $rowEmp = $empData->row();

                    $this->db->select('date_from, date_to, reference_no, type');
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

        function getAvailableLoav2($id = null, $syncDate = null) {
            $resultset = array();

            if ($id && $syncDate) {
                $lastSyncDate = date("Y-m-d", strtotime($syncDate));
                $nowMeredian = date("A");
                
                $this->db->select('date_from, date_to, reference_no, type');
                $this->db->order_by("id", "desc");
                $this->db->limit(1);
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
                $resultset['response'] = false;
            }

            return $resultset;
        }

        function getAvailableToByDate($id = null, $date = null) {
            $resultset = array();
            $syncDate = ($date) ? $date : $this->getLastSyncData();
            $lastSyncDate = date("Y-m-d", strtotime($syncDate));
            $nowMeredian = date("A");

            $this->db->select('id'); // added as it was only using id
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

        function getEmployeeTo($id = null, $date = null) {
            if ($id && $date) {
                $_date = date("Y-m-d", strtotime($date));
                $_filterDate = date("Y-m", strtotime($date));

                $this->db->select("a.reference_no, c.date_from, c.date_to");
                $this->db->from("{$this->eformsToTable} a");
                $this->db->join("{$this->eformsTpTable} b", "b.travel_order_id = a.id", "left");
                $this->db->join("{$this->eformsTdTable} c", "c.travel_order_id = a.id", "left");
                $this->db->where("b.employee_id", $id);

                $this->db->group_start();
                    $this->db->where('DATE(c.date_from) <=', $_date);
                    $this->db->where('DATE(c.date_to) >=', $_date);
                $this->db->group_end();

                // $this->db->group_start();
                // $this->db->like("c.date_from", $_filterDate, "both");
                // $this->db->like("c.date_to", $_filterDate, "both");
                // $this->db->group_end();

                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $rowData = array();

                    $rowData = $query->row();
                    // foreach ($query->result() as $rs) {
                    //     $fromDate = date("Y-m-d", strtotime($rs->date_from));
                    //     $toDate = date("Y-m-d", strtotime($rs->date_to));

                    //     if (($_date >= $fromDate) && ($_date <= $toDate)) {
                    //         $rowData = $rs;
                    //     }
                    // }

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
                    $this->db->where('DATE(b.date_from) <=', $_date);
                    $this->db->where('DATE(b.date_to) >=', $_date);
                $this->db->group_end();

                // $this->db->group_start();
                // $this->db->like("b.date_from", $_filterDate, "both");
                // $this->db->like("b.date_to", $_filterDate, "both");
                // $this->db->group_end();

                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $rowData = array();

                    $rowData = $query->row();
                    // foreach ($query->result() as $rs) {
                    //     $fromDate = date("Y-m-d", strtotime($rs->date_from));
                    //     $toDate = date("Y-m-d", strtotime($rs->date_to));

                    //     if (($date >= $fromDate) && ($date <= $toDate)) {
                    //         $rowData = $rs;
                    //     }
                    // }

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

        function getYesterdayAttendancev2($date = null) {
            $result = array();
            if ($date) {
                $this->db->select("a.biometric_id, a.datetime, a.device_id, b.id as emp_id");
                $this->db->join('gccmaster.tblemployees as b', 'a.biometric_id = b.biometricno', 'left');
                $this->db->from($this->attendanceTable.' as a');
                $this->db->where("DATE(a.datetime)", $date);
                $this->db->order_by("a.biometric_id", "asc");
                $this->db->order_by("a.datetime", "asc");
                $query = $this->db->get();

                $this->db->reset_query();

                if ($query->num_rows() > 0) {
                    $result = $query->result();
                }
            }

            return $result;
        }

        function getUndertime($id = null, $isSpecific = false) {
            if ($id) {

                if ($isSpecific) {
                    $this->db->select('am_start, am_end, pm_start, pm_end');
                }

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

        function getAvailableLoaUndertimeV2($id = null) {
            $resultset = array();
            $syncDate = $this->lastSynced;
            $lastSyncDate = date("Y-m-d", strtotime("-1 day", strtotime($syncDate)));
            $nowMeredian = date("A");

            $this->db->select('reference_no, date_from, date_to');
            $this->db->where('employee', $id);
            $this->db->where('type', 1);
            $this->db->from('gcceforms.loa');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
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
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function getAbsentYesterday($date = null) {
            $result = array();

            if($date) {
                $this->db->select('biometric_id, meredien, updated_at');
                $this->db->where("DATE(updated_at)", $date);
                $this->db->from($this->absentTable);
                $this->db->order_by("biometric_id", "asc");
                $this->db->group_by('biometric_id');
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $result = $query->result();
                }
            }

            return $result;
        }

        function getAbsentFlag($bio = 0, $absentRecords = array(), $yesterday = null, $shiftAmStart = null, $shiftAmEnd = null, $shiftPmStart = null, $shiftPmEnd = null, $yesterdayAttendance = array(), $totalAttendance = 0) {
            $response = false;
            $ndate = date("Y-m-d", strtotime($yesterday));
            
            foreach($absentRecords as $key => $value) {
                if ($value->biometric_id == $bio) {
                    $biometric = $value->biometric_id;
                    $current_meredien = $value->meredien;
                    $meredien = strtoupper(date("A", strtotime($value->updated_at)));

                    if (($shiftAmStart !== "00:00:00" && $shiftAmStart) && ($shiftAmEnd !== "00:00:00" && $shiftAmEnd) && ($current_meredien == "AM")) {
                        $personnel = $this->getPersonnelAttedanceV2($biometric, $yesterday, $current_meredien, $yesterdayAttendance);

                        if ($personnel == false && $totalAttendance == 0) {
                            $response = true;
                        }
                    }

                    if (($shiftPmStart !== "00:00:00" && $shiftPmStart) && ($shiftPmEnd !== "00:00:00" && $shiftPmEnd) && ($current_meredien == "PM")) {
                        $personnel = $this->getPersonnelAttedanceV2($biometric, $yesterday, $current_meredien, $yesterdayAttendance);

                        if ($personnel == false && $totalAttendance == 0) {
                            $response = true;
                        }

                        $dataTime = array();
                        $datetime_start = date("Y-m-d H:i:s", strtotime("{$ndate} 12:01:00"));
                        $dataTime_end = date("Y-m-d H:i:s", strtotime("{$ndate} 13:00:00"));

                        $afternoonAttendance = $this->getSingleAttendanceByDateRangeDataV2($biometric, $datetime_start, $dataTime_end, $yesterdayAttendance);
                        if ($afternoonAttendance == true && $totalAttendance == 0) {
                            $response = true;
                        }
                    }
                }
            }

            return $response;
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

        function getPersonnelAttedanceV2($biometric_id = null, $date = null, $ampm = 'AM', $yesterdayAttendance = array()) {
            $flag = false;

            if ($biometric_id && $date) {
                foreach($yesterdayAttendance as $key => $value) {
                    if ($value->biometric_id == $biometric_id) {
                        $meredien = strtoupper(date("A", strtotime($value->datetime)));
                        if ($meredien == $ampm) {
                            $flag = true;
                        }
                    }
                }
            }

            return $flag;
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

        public function getPersonalLate($biometricId = null) {
            $resultset = array();

            $lastSyncDate = $this->lastSynced;
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            $startMonth = ($lastSyncDate) ? date("Y-m-01", strtotime($lastSyncDate)) : date("Y-m-01");

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
                $getAttendanceByDateRange = $this->getAttendanceByDateRange($dates, $biometricId);

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

        function getPersonalAbsent($biometricId = null) {
            $resultset = array();

            $lastSyncDate = $this->lastSynced;
            $todays = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            $startDate = ($lastSyncDate) ? date("Y-m-01 H:i:s", strtotime($lastSyncDate)) : date("Y-m-01 H:i:s");

            $this->db->from($this->absentTable);
            $this->db->where("updated_at >=", $startDate);
            $this->db->where("updated_at <=", $todays);
            $this->db->where("biometric_id", $biometricId);
            $this->db->order_by("id", "DESC");
            $queryget = $this->db->get();
            $getAbsentCollection = $queryget->result_array();

            $this->db->reset_query();

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

        function getPersonnelAttendance($biometric_id = null, $datetime = null, $ampm = "AM") {
            $flag = false;
            if ($biometric_id && $datetime) {
                $this->db->from($this->attendanceTable);
                $this->db->where("biometric_id", $biometric_id);
                $this->db->like("datetime", $datetime);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $result = $query->result();
                    $count = $query->num_rows();

                    foreach ($result as $rs) {
                        $meredien = date("A", strtotime($rs->datetime));
                        if ($meredien == $ampm) {
                            $flag = true;
                        }
                    }
                }
            }
            return $flag;
        }

        function getPersonnelCount($biometric_id = null, $datetime = null, $current_meredien = null) {
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

        function getPersonalUndertime($biometricId = null) {
            $resultset = array();

            // $lastSyncDate = $this->adm_attendance->getLastSyncDate();
            // $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            // $startMonth = date("Y-m-01");

            $lastSyncDate = $this->lastSynced;
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            $startMonth = ($lastSyncDate) ? date("Y-m-01 H:i:s", strtotime($lastSyncDate)) : date("Y-m-01 H:i:s");

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
                $getAttendanceByDateRange = $this->getAttendanceByDateRange($dates, $biometricId);

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

        function getPersonalLackingDoubleEntries($biometricId = null, $type = 'lacking') {
            $resultset = array();

            // $lastSyncDate = $this->adm_attendance->getLastSyncDate();
            // $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            // $startMonth = date("Y-m-01");
            $lastSyncDate = $this->lastSynced;
            $today = ($lastSyncDate) ? date("Y-m-d", strtotime($lastSyncDate)) : date("Y-m-d");
            $startMonth = ($lastSyncDate) ? date("Y-m-01 H:i:s", strtotime($lastSyncDate)) : date("Y-m-01 H:i:s");

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
                $getAttendanceByDateRange = $this->getAttendanceByDateRange($dates, $biometricId);

                $this->db->reset_query();

                $getAbsents = $this->getAbsentByDateRange($begin, $newtime, $biometricId);

                if ($getAttendanceByDateRange->num_rows() > 0) {
                    $_AMCheck = date_format(date_create($timeToday), "A");

                    $count = array();
                    $loggedTimeRecord = array();

                    foreach($getAttendanceByDateRange->result() as $counter) {
                        $date = date('Y-m-d', strtotime($counter->datetime));
                        if (isset($count[$counter->biometric_id][$date])){
                            $count[$counter->biometric_id][$date]++;
                        } else {
                            $count[$counter->biometric_id][$date] = 1;
                        }

                        $loggedTimeRecord[$counter->biometric_id][$date][] = $counter->datetime;
                    }

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

                                        $totalAttendance = $count[$biometric_id][$currentDate];

                                        $data["biometric_id"] = $personnel->biometric_id;
                                        $data["employee_name"] = (isset($personnel->name) && $personnel->name) ? trim($personnel->name) : $personnel->biometric_id;
                                        $data["logged_time_record"] = array_unique($loggedTimeRecord[$biometric_id][$currentDate]);
                                        $data["time_count"] = $totalAttendance;
                                        $data["date"] = $currentDate;

                                        $absenteeRecord = $this->getAbsentFlag($biometric_id, $getAbsents, $currentDate, $todayShift->am_start, $todayShift->am_end, $todayShift->pm_start, $todayShift->pm_end, $getAttendanceByDateRange->result(), $totalAttendance);

                                        // $absenteeRecord = $this->getSingleAttendanceAbsent($biometric_id, $currentDate);
                                        
                                        if ($type == 'lacking') {
                                            if ($countShift !== 0 && $totalAttendance < $countShift && $absenteeRecord == false) {
                                                $resultset["lacking_entry"][$att_index] = $data;
                                            } 
                                        }
                                        
                                        if ($type == 'double') {
                                            if ($countShift !== 0 && $totalAttendance > $countShift) {
                                                $resultset["double_entry"][$att_index] = $data;
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

        function getAbsentByDateRange($start = null, $end = null, $biometric_id = 0){
            $result = array();

            if($start && $end) {
                $this->db->select('biometric_id, meredien, updated_at');
                $this->db->where("DATE(updated_at) >=", $start);
                $this->db->where("DATE(updated_at) <=", $end);
                $this->db->where('biometric_id', $biometric_id);
                $this->db->from($this->absentTable);
                $this->db->order_by("updated_at", "asc");
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $result = $query->result();
                }
            }

            return $result;
        }

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

        function getUndertimev2(){
            $result = array();

            $this->db->select('id, am_start, am_end, pm_start, pm_end');
            $this->db->where('status', 1);
            $this->db->from($this->shiftUndertimeTable);
            $late = $this->db->get();

            if ($late->num_rows() > 0) {
                foreach($late->result() as $row) {
                    $result[$row->id] = $row;
                }
            }

            return $result;
        }

        function getAvailableLoaUndertimeV3($date = null) {
            $resultset = array();

            if ($date){
                $this->db->select('date_from, date_to, reference_no, employee');
                $this->db->where('type', 1);
                $this->db->where('DATE(date_from) <=', $date);
                $this->db->where('DATE(date_to) >=', $date);
                $this->db->from("gcceforms.loa");

                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    foreach($query->result() as $row) {
                        $resultset[$row->employee] = $row;
                    }
                }
            }

            return $resultset;
        }

        function getLogNotification($module = null){
            $data = array();
            $this->db->from("gccmaster.log_notification");
            
            if ($module) {
                $check = date("Y-m-d", strtotime("-3 day"));
    
                $this->db->select("status, created_at, notification, type");
                $this->db->where("module", $module);
                $this->db->where("DATE(created_at) >= ", $check);
                $this->db->order_by("created_at", "desc");
            }
    
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach($query->result() as $row){
                    $row->formatted_created_at = $this->core_layout->getTimeAgo($row->created_at);
                    $data[] = $row;
                }
            }

            return $data;
        }
    }