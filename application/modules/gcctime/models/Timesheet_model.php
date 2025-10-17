<?php defined('BASEPATH') || exit('No direct script access allowed');
class Timesheet_model extends CI_Model{
    protected $tbl_employees = "gccmaster.tblemployees";
    protected $tbl_tblcompanies = "gcchris.tblcompanies";
    protected $tbl_tbldepartments = "gcchris.tbldepartments";
    protected $tbl_tblposition = "gcchris.tblposition";
    protected $tbl_tblholidays = "gcchris.tblholidays";

    protected $tbl_attendance = "gcctimeutility.attendance";
    protected $tbl_personnel = "gcctimeutility.personnel";
    protected $tbl_shift_schedule = "gcctimeutility.shift_schedule";
    protected $tbl_shift_schedule_resource = "gcctimeutility.shift_schedule_resource";
    protected $tbl_shift_schedule_list = "gcctimeutility.shift_schedule_list";
    protected $tbl_timesheet = "gcctimeutility.timesheet";
    protected $tbl_loa = "gcceforms.loa";
    protected $tbl_time_adjustments = "gcctimeutility.time_adjustments";
    protected $tbl_time_adjustments_meta = "gcctimeutility.time_adjustments_meta";
    protected $tbl_TO = "gcceforms.travel_order";
    protected $tbl_TO_Personnel = "gcceforms.travel_personnel";
    protected $tbl_TO_Destination = "gcceforms.travel_destination";
    protected $tbl_time_adjustments_shift_schedule = "gcctimeutility.time_adjustments_shift_schedule";
    protected $tbl_time_adjustments_eform_refs = "gcctimeutility.time_adjustments_eform_refs";
    protected $tbl_time_adjustments_overtime = "gcctimeutility.time_adjustments_overtime";

    protected $tbl_overtime = "gcceforms.overtime";
    protected $tbl_timesheet_overtime = "gcctimeutility.timesheet_overtime";
    protected $tbl_timesheet_imports = "gcctimeutility.timesheet_imports";

    protected $tbl_time_parameters = "gcctimeutility.time_parameters";
    protected $tbl_timesheet_excluded_employees = "gcctimeutility.timesheet_excluded_employees";
    protected $tbl_timesheet_customized_shift_schedule = "gcctimeutility.time_customized_shift_schedule";
    protected $tbl_timesheet_paid_holiday = "gcctimeutility.timesheet_paid_holiday";
    protected $tbl_time_adjustments_manual_overtime = "gcctimeutility.time_adjustment_manual_overtime";

    protected $tbl_timesheet_monthly_employees = "gcctimeutility.timesheet_monthly_employees";
    protected $tbl_payroll_sheet = "payroll.payroll_sheet";
    protected $tbl_ps_employee_regular_ndiff = "payroll.employee_regular_ndiff";
    protected $tbl_auto_overtime = 'payroll.employee_auto_overtime';

    private $db_debug;
    private $logged_in_user;
    private $today;

    public function __construct()
    {
        parent::__construct();
        include_once APPPATH . 'libraries/PHPExcel/IOFactory.php';

        $this->db_debug = $this->db->db_debug;
        $this->logged_in_user = $this->session->userdata("logged_in");
        $this->today = new DateTime("now", new DateTimeZone('Asia/Manila'));
        $this->load->model('ams/utilities_model', 'mod_util');
    }

    public function create($date, $is_custom, $emp = array(), $timesheet_imports_id = null, $generated_manually = 0)
    {
        $is_custom = ($is_custom == "null")? null: $is_custom;
        $this->db->db_debug = false;
        // $is_custom = identifier if attendance record is direct from device or manual e.g. imported
        $employees = $this->getEmployeesWithAttendance($date, $is_custom, $emp);
        $weekday = strtolower(date("l", strtotime($date)));
        $resultSet = array();
        $hasOT = 0;
        $updatedTimesheets = array();
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $attendance_params = $this->db->get_where($this->tbl_time_parameters, array("param_name" => "TS_OT_PARAMS"))->row();
        $night_diff_cfg = $this->db->get_where($this->tbl_time_parameters, array("param_name" => "NIGHT_DIFF_PARAMS"))->row();
        $tempEmployeeIds = array();
        
        $tempOvertimeRecords = $this->getOvertimeRecordByDateRange($date, $emp);
        if (sizeof($employees) >= 1) {
            $this->db->trans_begin();
            foreach ($employees as $employee) {
                $hasOvertimeRecords = array();
                $hasOvertimeRequest = false;
                $tempKeySearch = "emp_id_{$employee->id}";
                if(isset($tempOvertimeRecords[$tempKeySearch]) && is_array($tempOvertimeRecords[$tempKeySearch]) && count($tempOvertimeRecords[$tempKeySearch]) > 0){
                    $hasOvertimeRecords = $tempOvertimeRecords[$tempKeySearch];
                }

                if(is_array($hasOvertimeRecords) && count($hasOvertimeRecords) > 0){
                    foreach ($hasOvertimeRecords as $otValue) {
                        $otRecord = explode("::", $otValue);
                        if(is_array($otRecord) && count($otRecord) == 2){
                            $tempOtRecord = explode("__", $otRecord[1]);
                            $tempOtDateFrom = strtotime(date("Y-m-d", strtotime($tempOtRecord[0])));
                            $tempOtDateTo = strtotime(date("Y-m-d", strtotime($tempOtRecord[1])));
                            $currentOTDate = strtotime(date("Y-m-d", strtotime($date)));

                            if($currentOTDate >= $tempOtDateFrom && $currentOTDate <= $tempOtDateTo){
                                $hasOvertimeRequest = true;
                                break;
                            }
                        }
                    }
                }

                $updatedSchedule = $this->getCurrentShiftSchedule($date, $employee);
                $schedule = $updatedSchedule->schedule;
                
                /*** detect night shift ***/
                $activeSchedule = [];
                $schedule_props = array("am_start", "am_end", "pm_start", "pm_end");
                foreach ($schedule_props as $nKey => $sProps) {
                    if($schedule->{$sProps}){
                        if($nKey > 0){
                            $prevKey = $schedule_props[$nKey - 1];
                            if($prevKey && $schedule->{$prevKey}){
                                $prevTime = date("H:i:s", strtotime($schedule->{$prevKey}));
                                $tempTime = date("H:i:s", strtotime($schedule->{$sProps}));
                                if(strtotime($prevTime) > strtotime($tempTime)){
                                    $nextDate = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                                    $activeSchedule[$sProps] = date("Y-m-d H:i", strtotime($nextDate ." ".$schedule->{$sProps}));
                                }else{
                                    $activeSchedule[$sProps] = date("Y-m-d H:i", strtotime($date ." ".$schedule->{$sProps}));
                                }
                            }
                        }else{
                            $activeSchedule[$sProps] = date("Y-m-d H:i", strtotime($date ." ".$schedule->{$sProps}));
                        }
                    }
                }

                $tempActiveSchedule = array();
                foreach ($activeSchedule as $sched) { $tempActiveSchedule[] = $sched; }

                $nightShiftLastRecord = null;
                /*** check night shift schedule **/
                $isNightShift = $this->checkNightShiftSchedule($date, $tempActiveSchedule);
                $nshiftParams = $this->nightshiftParams($date);
                /*** check night shift schedule **/

                if($isNightShift){
                    $prevDate = date("Y-m-d", strtotime("-1 day", strtotime($date)));
                    $prev_attendance = array_map(function ($_attendance) {
                        return date("Y-m-d H:i", strtotime($_attendance->datetime));
                    }, $this->getAttendance($prevDate, $employee->id, $nshiftParams["start"]));
                    $nightShiftLastRecord = end($prev_attendance);
                }
                /*** detect night shift ***/

                $payrollType = $employee->payroll_type;
                /*** $alteredShifts = $this->getCustomizedShiftScheduleByDate($date, $employee->id); ***/

                $ts_exist = $this->db
                ->where("emp_id", $employee->id)
                ->where("date", $date)
                ->get($this->tbl_timesheet);

                if($ts_exist->num_rows() == 1 && !in_array($employee->id, $tempEmployeeIds)){ $tempEmployeeIds[] = $employee->id; }
                $timesheet_exist = $ts_exist->row();

                $overtime = $this->db
                    ->get_where($this->tbl_overtime,
                        array(
                            "employee" => $employee->id,
                            "DATE(date_from)" => $date,
                            "status" => "Approved"
                        )
                    )->result();

                $hasOT = sizeof($overtime) >= 1 ? 1 : 0; // true/1 and false/0 if date determined with overtime
                $this->db->reset_query();
                $flexible = intval($employee->is_flexi) !== 0;
                $flexibleEmployee = intval($employee->is_flexi) === 1;
                $shift_id = $employee->shift_id;

                /* CONCATENATE DATE + START TIME OF TIME PARAMS start_time ex. [2020-07-24 06:01] AS START DATE PARAMETER TO DETERMINE ATTENDANCES
                *  and (DATE + 1 DAY) + END TIME OF TIME PARAMS ex. [2020-07-24 + 1 = 2020-07-25 06:00] AS END DATE PARAMETER TO DETERMINE ATTENDANCES */
                $att_curr_day = date('Y-m-d H:i:s', strtotime($date . " " . $attendance_params->start_time));

                $_date = new DateTime($date);
                $att_next_day = $_date->modify("+1 day");
                $att_next_day = date('Y-m-d H:i:s', strtotime($att_next_day->format('Y-m-d') . " " . $attendance_params->end_time));
                /* END CONCATENATE DATE */
                
                /*** nextday shift schedule checker ***/
                $att_next_day = $this->nextDayShiftCheker($date, $employee, $att_next_day);
                /*** nextday shift schedule checker ***/
                
                $att_next_day = $isNightShift ? date("Y-m-d H:i:s", strtotime("+1 day", strtotime($nshiftParams["start"]))) : $att_next_day;
                $attendance = array_map(function ($_attendance) {
                    return date("Y-m-d H:i", strtotime($_attendance->datetime));
                }, $this->getAttendance($att_curr_day, $employee->id, $att_next_day, $nightShiftLastRecord));
                $attendance = array_values(array_unique($attendance));

                /*** 
                 * temporarily disabled
                 * if($ts_exist->num_rows() == 1){
                    $timesheetRow = $ts_exist->row();
                    $attrAttendances = array();
                    $attDate = date("Y-m-d", strtotime($date));
                    $propAttr = array("am_in", "am_out", "pm_in", "pm_out");
                    foreach ($propAttr as $key => $value) {
                        if($key > 0){
                            $tempKey = $propAttr[$key-1];
                            if($timesheetRow->{$value} != null && $timesheetRow->{$tempKey} != null &&
                                strtotime($timesheetRow->{$value}) < strtotime($timesheetRow->{$tempKey})){
                                $attDate = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                            }
                        }

                        if($timesheetRow->{$value} != null){
                            $attrAttendances[] = date("Y-m-d H:i", strtotime($attDate . " " . $timesheetRow->{$value}));
                        }
                    }

                    $attendance = array_values(array_unique(array_merge($attendance, $attrAttendances)));
                }
                 * temporarily disabled
                ***/

                $am_start = !empty($schedule) ? $schedule->am_start : null;
                $am_end = !empty($schedule) ? $schedule->am_end : null;
                $pm_start = !empty($schedule) ? $schedule->pm_start : null;
                $pm_end = !empty($schedule) ? $schedule->pm_end : null;

                $no_shift_schedule = false;
                if (($am_start === null && $am_end === null && $pm_start === null && $pm_end === null) || empty($schedule)) {
                    $no_shift_schedule = true;
                }
                
                $am_shift_only = (($am_start !== null && $am_end !== null) && (($pm_start === null || $pm_start == "00:00:00") && ($pm_end === null || $pm_end == "00:00:00")) && $no_shift_schedule === false);
                $pm_shift_only = ((($am_start === null || $am_start == "00:00:00") && ($am_end === null || $am_end == "00:00:00")) && ($pm_start !== null && $pm_end !== null) && $no_shift_schedule === false);
                $isWholeDay = ($am_shift_only === false && $pm_shift_only === false && $no_shift_schedule === false);

                $tempHoliday = (object) $this->getCurrentDateIsHoliday($date);
                $isHoliday = ($tempHoliday->is_holiday === true)? 1: 0;
                $payRateId = ($tempHoliday->is_holiday === true && $tempHoliday->payrate_id)? $tempHoliday->payrate_id: 0;

                $employee_time_sheet = new StdClass();
                $employee_time_sheet->emp_id = $employee->id;
                $employee_time_sheet->date = $date;
                $employee_time_sheet->weekday = $weekday;
                $employee_time_sheet->is_holiday = $isHoliday;
                $employee_time_sheet->payrate_id = $payRateId;
                $employee_time_sheet->custom_shift_id = $updatedSchedule->custom_shift_id;

                $employee_time_sheet->am_in = null;
                $employee_time_sheet->am_out = null;
                $employee_time_sheet->am_late = 0;
                $employee_time_sheet->am_ut = 0;
                $employee_time_sheet->am_time_rendered = 0;

                $employee_time_sheet->pm_in = null;
                $employee_time_sheet->pm_out = null;
                $employee_time_sheet->pm_late = 0;
                $employee_time_sheet->pm_ut = 0;
                $employee_time_sheet->pm_time_rendered = 0;

                $employee_time_sheet->total_late = 0;
                $employee_time_sheet->total_ut = 0;
                $employee_time_sheet->total_time_rendered = 0;
                
                $employee_time_sheet->is_flexi = $flexible;
                $employee_time_sheet->scrub_status = 0;
                $employee_time_sheet->comments = null;

                /*** $_has_shift = $alteredCustomShiftId !== 0? intval($alteredHasShiftSchedule): 1;
                $employee_time_sheet->has_shift = $_has_shift; ***/
                
                $employee_time_sheet->has_shift = $updatedSchedule->has_shift;
                $employee_time_sheet->shift_am_start = null;
                $employee_time_sheet->shift_am_end = null;
                $employee_time_sheet->shift_pm_start = null;
                $employee_time_sheet->shift_pm_end = null;
                $employee_time_sheet->timesheet_imports_id = $timesheet_imports_id;

                $employee_time_sheet->has_overtime = $hasOT;

                if (intval($generated_manually) === 1) {
                    $employee_time_sheet->is_manual = 1;
                    $employee_time_sheet->manual_mode = "generated";
                    $employee_time_sheet->manual_by = $logged_in_user_emp_id;
                }

                $props = ["am_in", "am_out", "pm_in", "pm_out"];
                $timesheet_id = null;

                // TODO: double check re-generation functionality
                // DETERMINE IF WITH APPROVED AND PENDING TIME ADJUSTMENT THEN SKILL GENERATE MANUALLY

                if (sizeof((array) $timesheet_exist) >= 1 &&
                    intval($generated_manually) === 1) {
                    $hasShiftUpdate = false;
                    $tempShiftProps = array("am_start", "am_end", "pm_start", "pm_end");

                    if($isWholeDay){
                        if($am_start !== $timesheet_exist->shift_am_start){ $hasShiftUpdate = true; }
                        if($am_end !== $timesheet_exist->shift_am_end){ $hasShiftUpdate = true; }
                        if($pm_start !== $timesheet_exist->shift_pm_start){ $hasShiftUpdate = true; }
                        if($pm_end !== $timesheet_exist->shift_pm_end){ $hasShiftUpdate = true; }
                    }elseif ($am_shift_only){
                        if($am_start !== $timesheet_exist->shift_am_start){ $hasShiftUpdate = true; }
                        if($am_end !== $timesheet_exist->shift_am_end){ $hasShiftUpdate = true; }
                    }elseif ($pm_shift_only){
                        if($pm_start !== $timesheet_exist->shift_pm_start){ $hasShiftUpdate = true; }
                        if($pm_end !== $timesheet_exist->shift_pm_end){ $hasShiftUpdate = true; }
                    }

                    if($hasShiftUpdate && intval($timesheet_exist->verified) !== 1){
                        $timesheet_exist->has_shift = $updatedSchedule->has_shift;
                        foreach($tempShiftProps as $vv){
                            $shiftRecord = ${$vv};
                            $tempShiftKey = "shift_{$vv}";
                            if(isset($timesheet_exist->$tempShiftKey) && $timesheet_exist->$tempShiftKey){
                                $timesheet_exist->$tempShiftKey = $shiftRecord;
                            }
                        }
                    }

                    $timesheetUpdate = $this->generateTimesheetComputation($timesheet_exist, $employee_time_sheet, $updatedTimesheets, $attendance, $date,
                    $no_shift_schedule, $am_start, $am_end, $pm_start, $pm_end, $am_shift_only, $pm_shift_only, $props, $flexibleEmployee, $payrollType);

                    if(isset($timesheetUpdate["updated_timesheets"]) && $timesheetUpdate["updated_timesheets"]){
                        $updatedTimesheets = $timesheetUpdate["updated_timesheets"];
                    }
                    if(isset($timesheetUpdate["timesheet_id"]) && $timesheetUpdate["timesheet_id"]){
                        $timesheet_id = $timesheetUpdate["timesheet_id"];
                    }

                    $noBreakOvertime = intval($timesheet_exist->has_overtime) == 2;
                    if(intval($timesheet_exist->has_overtime) == 1 || ($hasOvertimeRequest && $noBreakOvertime === false)){
                        $total_accredited_ot_hrs = 0;
                        $ot_night_diff = 0;
                        $overtime_start = null;
                        $overtime_end = null;

                        $response = $this->generateTimesheetOvertime($timesheet_exist, $generated_manually, $overtime, $date, $am_end, $pm_end, $am_shift_only, $attendance, $night_diff_cfg);
                        $allowOvertimeRequest = $response["allow_overtime_request"] ?? false;

                        if(isset($response["total_accredited_ot_hrs"]) && $response["total_accredited_ot_hrs"]){
                            $total_accredited_ot_hrs = floatval($response["total_accredited_ot_hrs"]);
                        }
                        if(isset($response["ot_night_diff"]) && $response["ot_night_diff"]){
                            $ot_night_diff = floatval($response["ot_night_diff"]);
                        }
                        if(isset($response["overtime_in"]) && $response["overtime_in"]){
                            $overtime_start = $response["overtime_in"];
                        }
                        if(isset($response["overtime_out"]) && $response["overtime_out"]){
                            $overtime_end = $response["overtime_out"];
                        }

                        $this->db->where("id", $timesheet_exist->id);
                        $this->db->where("verified", 0);

                        if($allowOvertimeRequest && intval($timesheet_exist->has_overtime) == 0){ $this->db->set("has_overtime", 1); }
                        $tempHasOvertime = $allowOvertimeRequest ? 1 : 0;
                        $tempHasOvertime = ($overtime_start && $overtime_end) && strtotime($overtime_end) > strtotime($overtime_start) ? 1 : $tempHasOvertime;

                        $this->db->set("has_overtime", $tempHasOvertime);
                        $this->db->set("total_accredited_ot_hrs", $total_accredited_ot_hrs);
                        $this->db->set("total_accredited_ndiff_ot_hrs", $ot_night_diff);
                        $this->db->set("overtime_in", $overtime_start);
                        $this->db->set("overtime_out", $overtime_end);

                        $this->db->update($this->tbl_timesheet);
                        $this->db->reset_query();
                    }
                    
                    if($timesheet_id){
                        $tempTblTimesheet = $this->db->get_where($this->tbl_timesheet, array("id"=>$timesheet_id));
                        if($tempTblTimesheet->num_rows() == 1){
                            $tempRow = $tempTblTimesheet->row();
                            $tempRowId = $tempRow->id;

                            $timesheetHourlyPartimer = $this->generatePerHourSlashPartimer($tempRowId);
                            $toArray = (array) $timesheetHourlyPartimer;
                            if(is_array($toArray) && count($toArray) > 0){ $tempRow->is_tagged_hourly = true; }
                            
                            $updatedRow = $this->updateTimesheetShiftComputation($tempRow, false, $night_diff_cfg);
                            $updatedTimesheet = (array) $updatedRow;
                            if(is_array($toArray) && count($toArray) > 0){
                                $updatedTimesheet = array_merge($updatedTimesheet, $toArray);
                            }
                            
                            $this->db->update($this->tbl_timesheet, $updatedTimesheet, array("id" => $tempRowId));
                            $this->db->reset_query();
                        }
                    }

                    $time_adjustments = $this->db
                        ->where("timesheet_id", $timesheet_exist->id)
                        ->where_in("status", [0, 1])
                        ->get($this->tbl_time_adjustments)
                        ->result();

                    if (sizeof((array) $time_adjustments) >= 1) { continue; }
                    if (intval($timesheet_exist->is_manual) === 1 && $timesheet_exist->manual_mode === "import") { continue; }
                    if (intval($timesheet_exist->verified) === 1) { continue; }
                } /*** end timesheet_exist ***/

                if (intval($shift_id) === 0) { continue; }
                /*** insert timesheet record start ***/
                if(sizeof((array) $timesheet_exist) == 0){
                    $timesheetUpdate = $this->generateTimesheetComputation($timesheet_exist, $employee_time_sheet, $updatedTimesheets, $attendance, $date,
                    $no_shift_schedule, $am_start, $am_end, $pm_start, $pm_end, $am_shift_only, $pm_shift_only, $props, $flexibleEmployee, $payrollType);
                    if(isset($timesheetUpdate["updated_timesheets"]) && $timesheetUpdate["updated_timesheets"]){
                        $updatedTimesheets = $timesheetUpdate["updated_timesheets"];
                    }
                    if(isset($timesheetUpdate["timesheet_id"]) && $timesheetUpdate["timesheet_id"]){
                        $timesheet_id = $timesheetUpdate["timesheet_id"];
                    }

                    if($timesheet_id){
                        $this->db->where("id", $timesheet_id);
                        $this->db->where("verified", 0);
                        $queryTimesheet = $this->db->get($this->tbl_timesheet);
                        if($queryTimesheet->num_rows() == 1){
                            $timesheet_exist = $queryTimesheet->row();

                            $total_accredited_ot_hrs = 0;
                            $ot_night_diff = 0;
                            $overtime_start = null;
                            $overtime_end = null;
                            
                            $noBreakOvertime = intval($timesheet_exist->has_overtime) == 2;
                            if (intval($timesheet_exist->has_overtime) == 1 || ($hasOvertimeRequest && $noBreakOvertime === false)) {
                                $response = $this->generateTimesheetOvertime($timesheet_exist, $generated_manually, $overtime, $date, $am_end, $pm_end, $am_shift_only, $attendance, $night_diff_cfg);
                                if(isset($response["total_accredited_ot_hrs"]) && $response["total_accredited_ot_hrs"]){
                                    $total_accredited_ot_hrs = floatval($response["total_accredited_ot_hrs"]);
                                }
                                if(isset($response["ot_night_diff"]) && $response["ot_night_diff"]){
                                    $ot_night_diff = floatval($response["ot_night_diff"]);
                                }
                                if(isset($response["overtime_in"]) && $response["overtime_in"]){
                                    $overtime_start = $response["overtime_in"];
                                }
                                if(isset($response["overtime_out"]) && $response["overtime_out"]){
                                    $overtime_end = $response["overtime_out"];
                                }

                                $this->db->where("id", $timesheet_exist->id);
                                $this->db->where("verified", 0);
                                if(intval($timesheet_exist->has_overtime) == 0){ $this->db->set("has_overtime", 1); }
                                $this->db->set("total_accredited_ot_hrs", $total_accredited_ot_hrs);
                                $this->db->set("total_accredited_ndiff_ot_hrs", $ot_night_diff);
                                $this->db->set("overtime_in", $overtime_start);
                                $this->db->set("overtime_out", $overtime_end);
                                $this->db->update($this->tbl_timesheet);
                                $this->db->reset_query();
                            }

                            if($timesheet_id){
                                $tempTblTimesheet = $this->db->get_where($this->tbl_timesheet, array("id"=>$timesheet_id));
                                if($tempTblTimesheet->num_rows() == 1){
                                    $tempRow = $tempTblTimesheet->row();
                                    $tempRowId = $tempRow->id;

                                    $timesheetHourlyPartimer = $this->generatePerHourSlashPartimer($tempRowId);
                                    $toArray = (array) $timesheetHourlyPartimer;
                                    if(is_array($toArray) && count($toArray) > 0){ $tempRow->is_tagged_hourly = true; }
                                    
                                    $updatedRow = $this->updateTimesheetShiftComputation($tempRow, false, $night_diff_cfg);
                                    $updatedTimesheet = (array) $updatedRow;
                                    if(is_array($toArray) && count($toArray) > 0){
                                        $updatedTimesheet = array_merge($updatedTimesheet, $toArray);
                                    }
                                    
                                    $this->db->update($this->tbl_timesheet, $updatedTimesheet, array("id" => $tempRowId));
                                    $this->db->reset_query();
                                }
                            }
                        }
                    }
                }
                /*** insert timesheet record end ***/
            }

            if ($this->db->trans_status() === true) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Timesheet for $date successfully created.";
                $resultSet["title"] = "Timesheet created.";
                $resultSet["k"] = 1;
                $this->db->trans_commit();
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "DB Error occurred.";
                $resultSet["k"] = 2;
                $this->db->trans_rollback();
            }
        } else {
            if(is_array($emp) && count($emp) > 0){
                foreach ($emp as $value) {
                    if(!in_array($value, $tempEmployeeIds)){
                        /*** is cutom 3 for default timesheet entry ***/
                        $tempTblTimesheet = $this->db->get_where($this->tbl_timesheet, array("emp_id"=>$value, "date"=>$date));
                        if($tempTblTimesheet->num_rows() == 1){
                            $tempComments = "";
                            $tempRow = $tempTblTimesheet->row();
                            /*** $alteredShifts = $this->getCustomizedShiftScheduleByDate($date, $tempRow->emp_id); ***/
                            /*** $shift_id = 0; ***/

                            $this->db->select("a.id, b.shift_id");
                            $this->db->from($this->tbl_employees." a");
                            $this->db->join($this->tbl_personnel." b", "b.biometric_id = a.biometricno OR b.biometricno = a.biometricno");
                            $this->db->where("a.id", $tempRow->emp_id);
                            $queryTempx = $this->db->get();
                            if($queryTempx->num_rows() == 1){
                                /*** $shift_id = $queryTempx->row()->shift_id; ***/
                                $employee = $queryTempx->row();

                                $updatedSchedule = $this->getCurrentShiftSchedule($date, $employee);
                                $schedule = $updatedSchedule->schedule;
                                $prop_schedule = array("am_start", "am_end", "pm_start", "pm_end");
                                foreach ($prop_schedule as $prop) {
                                    $tempSchedule = "shift_{$prop}";
                                    if(isset($schedule->$prop)){
                                        $tempRow->$tempSchedule = $schedule->$prop !== null ? $schedule->$prop: null;
                                    }
                                }

                                $tempRow->has_shift = $updatedSchedule->has_shift;
                                $tempRow->custom_shift_id = $updatedSchedule->custom_shift_id;
                            }

                            $temp_overtime = $this->db
                            ->get_where($this->tbl_overtime,
                                array(
                                    "employee" => $tempRow->emp_id,
                                    "DATE(date_from)" => $date,
                                    "status" => "Approved"
                                )
                            );
                                
                            $hasApprovedOvertimeRequest = $temp_overtime->num_rows() == 1;

                            if((intval($tempRow->has_overtime) == 1 || $hasApprovedOvertimeRequest) && intval($tempRow->verified) !== 1){
                                $total_accredited_ot_hrs = 0;
                                $ot_night_diff = 0;
                                $overtime_start = null;
                                $overtime_end = null;
                                $response = $this->generateNoShiftOvertime($tempRow, $night_diff_cfg, $date, $generated_manually);
                                $allowOvertimeRequest = $response["allow_overtime_request"] ?? false;

                                if(isset($response["total_accredited_ot_hrs"]) && $response["total_accredited_ot_hrs"]){
                                    $total_accredited_ot_hrs = floatval($response["total_accredited_ot_hrs"]);
                                }
                                if(isset($response["ot_night_diff"]) && $response["ot_night_diff"]){
                                    $ot_night_diff = floatval($response["ot_night_diff"]);
                                }
                                if(isset($response["overtime_in"]) && $response["overtime_in"]){
                                    $overtime_start = $response["overtime_in"];
                                }
                                if(isset($response["overtime_out"]) && $response["overtime_out"]){
                                    $overtime_end = $response["overtime_out"];
                                }

                                $this->db->where("id", $tempRow->id);
                                $this->db->where("verified", 0);
                                
                                if($allowOvertimeRequest && isset($tempRow->has_overtime) && intval($tempRow->has_overtime) == 0){ $this->db->set("has_overtime", 1); }
                                $tempHasOvertime = $hasApprovedOvertimeRequest || $allowOvertimeRequest ? 1 : 0;
                                $tempHasOvertime = ($overtime_start && $overtime_end) && strtotime($overtime_end) > strtotime($overtime_start) ? 1 : $tempHasOvertime;

                                $this->db->set("has_overtime", $tempHasOvertime);
                                $this->db->set("total_accredited_ot_hrs", $total_accredited_ot_hrs);
                                $this->db->set("total_accredited_ndiff_ot_hrs", $ot_night_diff);
                                $this->db->set("overtime_in", $overtime_start);
                                $this->db->set("overtime_out", $overtime_end);
                                $updatedResponse = $this->db->update($this->tbl_timesheet);
                                if($updatedResponse && $this->db->affected_rows() > 0){
                                    $this->db->reset_query();
                                    $queryUpdated = $this->db->get_where($this->tbl_timesheet, array("id"=>$tempRow->emp_id));
                                    if($queryUpdated->num_rows() == 1){ $tempRow = $queryUpdated->row(); }
                                }else{
                                    $this->db->reset_query();
                                }
                                
                                $tempComments = "[System Generated]: No shift schedule, overtime detected.";
                            }

                            if(intval($tempRow->verified) !== 1){
                                $tempRowId = $tempRow->id;
                                $tempDatax = $tempRow;
                                
                                if(isset($tempDatax->shift_am_start, $tempDatax->shift_am_end) && ($tempDatax->shift_am_start === "00:00:00" && $tempDatax->shift_am_end === "00:00:00")){
                                    $tempDatax->shift_am_start = null;
                                    $tempDatax->shift_am_end = null;
                                }
                                if(isset($tempDatax->shift_pm_start , $tempDatax->shift_pm_end) && ($tempDatax->shift_pm_start === "00:00:00" && $tempDatax->shift_pm_end === "00:00:00")){
                                    $tempDatax->shift_pm_start = null;
                                    $tempDatax->shift_pm_end = null;
                                }

                                $timesheetHourlyPartimer = $this->generatePerHourSlashPartimer($tempRowId);
                                $toArray = (array) $timesheetHourlyPartimer;
                                if(is_array($toArray) && count($toArray) > 0){ $tempDatax->is_tagged_hourly = true; }
                                $updatedRow = $this->updateTimesheetShiftComputation($tempDatax, false, $night_diff_cfg);
                                $updatedTimesheet = (array) $updatedRow;
                                if(is_array($toArray) && count($toArray) > 0){
                                    $updatedTimesheet = array_merge($updatedTimesheet, $toArray);
                                }
                                $this->db->update($this->tbl_timesheet, $updatedTimesheet, array("id" => $tempRowId));
                            }

                            if(!$tempRow->has_shift && !$tempRow->has_overtime){
                                $tempComments = "[System Generated]: No record found, absent entry detected.";
                            }

                            if($tempComments){
                                $this->db->update($this->tbl_timesheet,
                                    array("comments"=>$tempComments),
                                    array("id"=>$tempRow->id)
                                );
                            }
                        }else{
                            $hasToRecords = array();
                            $hasApprovedTO = false;
                            
                            $prevDate = Date("Y-m-d", strtotime("-1 week", strtotime($date)));
                            $tempToRecord = $this->getToRecordByDateRange($prevDate);
                            $tempKeySearch = "emp_id_{$value}";
                            if(isset($tempToRecord[$tempKeySearch]) && is_array($tempToRecord[$tempKeySearch]) && count($tempToRecord[$tempKeySearch]) > 0){
                                $hasToRecords = $tempToRecord[$tempKeySearch];
                            }
                            
                            if(is_array($hasToRecords) && count($hasToRecords) > 0){
                                foreach ($hasToRecords as $valueTo) {
                                    $_tempTo = explode("::", $valueTo);
                                    if(is_array($_tempTo) && count($_tempTo) == 2){
                                        $tempDTx = explode("__", $_tempTo[1]);
                                        $tempDate00 = strtotime(date("Y-m-d", strtotime($tempDTx[0])));
                                        $tempDate01 = strtotime(date("Y-m-d", strtotime($tempDTx[1])));
                                        $ccDatex = strtotime(date("Y-m-d", strtotime($date)));
                                        
                                        $hasApprovedTO = $ccDatex >= $tempDate00 && $ccDatex <= $tempDate01;
                                    }
                                }
                            }

                            $this->db->reset_query();

                            $hasOvertimeRecords = array();
                            $hasOvertimeRequest = false;
                            $tempKeySearch = "emp_id_{$value}";
                            if(isset($tempOvertimeRecords[$tempKeySearch]) && is_array($tempOvertimeRecords[$tempKeySearch]) && count($tempOvertimeRecords[$tempKeySearch]) > 0){
                                $hasOvertimeRecords = $tempOvertimeRecords[$tempKeySearch];
                            }

                            if(is_array($hasOvertimeRecords) && count($hasOvertimeRecords) > 0){
                                foreach ($hasOvertimeRecords as $otValue) {
                                    $otRecord = explode("::", $otValue);
                                    if(is_array($otRecord) && count($otRecord) == 2){
                                        $tempOtRecord = explode("__", $otRecord[1]);
                                        $tempOtDateFrom = strtotime(date("Y-m-d", strtotime($tempOtRecord[0])));
                                        $tempOtDateTo = strtotime(date("Y-m-d", strtotime($tempOtRecord[1])));
                                        $currentOTDate = strtotime(date("Y-m-d", strtotime($date)));

                                        if($currentOTDate >= $tempOtDateFrom && $currentOTDate <= $tempOtDateTo){
                                            $hasOvertimeRequest = true;
                                            break;
                                        }
                                    }
                                }
                            }
                            
                            $isOneInOut = $is_custom === 3;
                            if($hasApprovedTO || $isOneInOut){
                                $isFlexibleType = $isOneInOut ? 4 : 3;
                                $select = "emp.id, emp.lastname, emp.firstname, emp.payroll_type, per.shift_id, per.is_flexi, per.biometric_id";
                                $this->db->select($select);
                                $this->db->from($this->tbl_employees." emp");
                                $this->db->join($this->tbl_personnel." per", "per.biometric_id = emp.biometricno OR per.biometricno = emp.biometricno");
                                $this->db->where("emp.id", $value);
                                $this->db->where("per.is_flexi", $isFlexibleType);
                                $this->db->limit(1);
                                $this->db->order_by("emp.id", "DESC");
                                $qTempEmployee = $this->db->get();

                                if($qTempEmployee->num_rows() == 1){
                                    $employee = $qTempEmployee->row();
                                    $payrollType = $employee->payroll_type;
                                    $updatedSchedule = $this->getCurrentShiftSchedule($date, $employee);
                                    $schedule = $updatedSchedule->schedule;

                                    $overtime = $this->db
                                    ->get_where($this->tbl_overtime,
                                        array(
                                            "employee" => $employee->id,
                                            "DATE(date_from)" => $date,
                                            "status" => "Approved"
                                        )
                                    )->result();

                                    $hasOT = sizeof($overtime) >= 1 ? 1 : 0;
                                    $this->db->reset_query();

                                    $flexible = intval($employee->is_flexi) !== 0;
                                    $flexibleEmployee = intval($employee->is_flexi) === 3 || intval($employee->is_flexi) === 4;

                                    $am_start = !empty($schedule) ? $schedule->am_start : null;
                                    $am_end = !empty($schedule) ? $schedule->am_end : null;
                                    $pm_start = !empty($schedule) ? $schedule->pm_start : null;
                                    $pm_end = !empty($schedule) ? $schedule->pm_end : null;

                                    $no_shift_schedule = false;
                                    if (($am_start === null && $am_end === null && $pm_start === null && $pm_end === null) || empty($schedule)) {
                                        $no_shift_schedule = true;
                                    }

                                    $am_shift_only = (($am_start !== null && $am_end !== null) && (($pm_start === null || $pm_start == "00:00:00") && ($pm_end === null || $pm_end == "00:00:00")) && $no_shift_schedule === false);
                                    $pm_shift_only = ((($am_start === null || $am_start == "00:00:00") && ($am_end === null || $am_end == "00:00:00")) && ($pm_start !== null && $pm_end !== null) && $no_shift_schedule === false);

                                    $tempHoliday = (object) $this->getCurrentDateIsHoliday($date);
                                    $isHoliday = ($tempHoliday->is_holiday === true)? 1: 0;
                                    $payRateId = ($tempHoliday->is_holiday === true && $tempHoliday->payrate_id)? $tempHoliday->payrate_id: 0;

                                    $employee_time_sheet = new StdClass();
                                    $employee_time_sheet->emp_id = $employee->id;
                                    $employee_time_sheet->date = $date;
                                    $employee_time_sheet->weekday = $weekday;
                                    $employee_time_sheet->is_holiday = $isHoliday;
                                    $employee_time_sheet->payrate_id = $payRateId;
                                    $employee_time_sheet->custom_shift_id = $updatedSchedule->custom_shift_id;
                                    
                                    $employee_time_sheet->am_in = null;
                                    $employee_time_sheet->am_out = null;
                                    $employee_time_sheet->am_late = 0;
                                    $employee_time_sheet->am_ut = 0;
                                    $employee_time_sheet->am_time_rendered = 0;

                                    $employee_time_sheet->pm_in = null;
                                    $employee_time_sheet->pm_out = null;
                                    $employee_time_sheet->pm_late = 0;
                                    $employee_time_sheet->pm_ut = 0;
                                    $employee_time_sheet->pm_time_rendered = 0;

                                    $employee_time_sheet->total_late = 0;
                                    $employee_time_sheet->total_ut = 0;
                                    $employee_time_sheet->total_time_rendered = 0;

                                    $employee_time_sheet->is_flexi = $flexible;
                                    $employee_time_sheet->scrub_status = 0;
                                    $employee_time_sheet->comments = null;

                                    $employee_time_sheet->has_shift = $updatedSchedule->has_shift;
                                    $employee_time_sheet->shift_am_start = null;
                                    $employee_time_sheet->shift_am_end = null;
                                    $employee_time_sheet->shift_pm_start = null;
                                    $employee_time_sheet->shift_pm_end = null;
                                    $employee_time_sheet->timesheet_imports_id = $timesheet_imports_id;

                                    $employee_time_sheet->has_overtime = $hasOT;

                                    if (intval($generated_manually) === 1) {
                                        $employee_time_sheet->is_manual = 1;
                                        $employee_time_sheet->manual_mode = "generated";
                                        $employee_time_sheet->manual_by = $logged_in_user_emp_id;
                                    }

                                    $props = ["am_in", "am_out", "pm_in", "pm_out"];
                                    $timesheet_id = null;

                                    $timesheet_exist = new stdClass();
                                    $attendance = array();

                                    $hasNextDayDate = false;

                                    $_amStart = $am_start ? date("Y-m-d H:i:s", strtotime($date." ".$am_start)): null;
                                    $_amEnd = $am_end ? date("Y-m-d H:i:s", strtotime($date." ".$am_end)): null;
                                    $_pmStart = $pm_start ? date("Y-m-d H:i:s", strtotime($date." ".$pm_start)): null;
                                    $_pmEnd = $pm_end ? date("Y-m-d H:i:s", strtotime($date." ".$pm_end)): null;

                                    if(($_amStart && $_amEnd) && (strtotime($_amEnd) < strtotime($_amStart))){
                                        $_amEnd = date("Y-m-d H:i:s", strtotime("+1 day", strtotime($_amEnd)));
                                        $hasNextDayDate = true;
                                    }
                                    
                                    if(($_amEnd && $_pmStart) && (strtotime($_pmStart) < strtotime($_amEnd) || $hasNextDayDate)){
                                        $_pmStart = date("Y-m-d H:i:s", strtotime("+1 day", strtotime($_pmStart)));
                                        $hasNextDayDate = true;
                                    }
                                    
                                    if(($_pmStart && $_pmEnd) && (strtotime($_pmEnd) < strtotime($_pmStart) || $hasNextDayDate)){
                                        $_pmEnd = date("Y-m-d H:i:s", strtotime("+1 day", strtotime($_pmEnd)));
                                        $hasNextDayDate = true;
                                    }
                                    
                                    if($_amStart){ $attendance[] = $_amStart; }
                                    if($_amEnd){ $attendance[] = $_amEnd; }
                                    if($_pmStart){ $attendance[] = $_pmStart; }
                                    if($_pmEnd){ $attendance[] = $_pmEnd; }
                                    
                                    $timesheetUpdate = $this->generateTimesheetComputation($timesheet_exist, $employee_time_sheet, $updatedTimesheets, $attendance, $date,
                                    $no_shift_schedule, $am_start, $am_end, $pm_start, $pm_end, $am_shift_only, $pm_shift_only, $props, $flexibleEmployee, $payrollType);
                                    if(isset($timesheetUpdate["updated_timesheets"]) && $timesheetUpdate["updated_timesheets"]){
                                        $updatedTimesheets = $timesheetUpdate["updated_timesheets"];
                                    }
                                    if(isset($timesheetUpdate["timesheet_id"]) && $timesheetUpdate["timesheet_id"]){
                                        $timesheet_id = $timesheetUpdate["timesheet_id"];
                                    }

                                    if($timesheet_id){
                                        $this->db->where("id", $timesheet_id);
                                        $this->db->where("verified", 0);
                                        $queryTimesheet = $this->db->get($this->tbl_timesheet);
                                        if($queryTimesheet->num_rows() == 1){
                                            $timesheet_exist = $queryTimesheet->row();

                                            $total_accredited_ot_hrs = 0;
                                            $ot_night_diff = 0;
                                            $overtime_start = null;
                                            $overtime_end = null;
                        
                                            if (intval($timesheet_exist->has_overtime) == 1 || $hasOvertimeRequest) {
                                                $response = $this->generateTimesheetOvertime($timesheet_exist, $generated_manually, $overtime, $date, $am_end, $pm_end, $am_shift_only, $attendance, $night_diff_cfg);
                                                if(isset($response["total_accredited_ot_hrs"]) && $response["total_accredited_ot_hrs"]){
                                                    $total_accredited_ot_hrs = floatval($response["total_accredited_ot_hrs"]);
                                                }
                                                if(isset($response["ot_night_diff"]) && $response["ot_night_diff"]){
                                                    $ot_night_diff = floatval($response["ot_night_diff"]);
                                                }
                                                if(isset($response["overtime_in"]) && $response["overtime_in"]){
                                                    $overtime_start = $response["overtime_in"];
                                                }
                                                if(isset($response["overtime_out"]) && $response["overtime_out"]){
                                                    $overtime_end = $response["overtime_out"];
                                                }

                                                $this->db->where("id", $timesheet_exist->id);
                                                $this->db->where("verified", 0);
                                                
                                                if(intval($timesheet_exist->has_overtime) == 0){ $this->db->set("has_overtime", 1); }
                                                $this->db->set("total_accredited_ot_hrs", $total_accredited_ot_hrs);
                                                $this->db->set("total_accredited_ndiff_ot_hrs", $ot_night_diff);
                                                $this->db->set("overtime_in", $overtime_start);
                                                $this->db->set("overtime_out", $overtime_end);
                                                $this->db->update($this->tbl_timesheet);
                                                $this->db->reset_query();
                                            }

                                            if($timesheet_id){
                                                $tempTblTimesheet = $this->db->get_where($this->tbl_timesheet, array("id"=>$timesheet_id));
                                                if($tempTblTimesheet->num_rows() == 1){
                                                    $tempRow = $tempTblTimesheet->row();
                                                    $tempRowId = $tempRow->id;

                                                    $timesheetHourlyPartimer = $this->generatePerHourSlashPartimer($tempRowId);
                                                    $toArray = (array) $timesheetHourlyPartimer;
                                                    if(is_array($toArray) && count($toArray) > 0){ $tempRow->is_tagged_hourly = true; }
                                                    
                                                    $updatedRow = $this->updateTimesheetShiftComputation($tempRow, false, $night_diff_cfg);
                                                    $updatedTimesheet = (array) $updatedRow;
                                                    if(is_array($toArray) && count($toArray) > 0){
                                                        $updatedTimesheet = array_merge($updatedTimesheet, $toArray);
                                                    }
                                                    
                                                    $this->db->update($this->tbl_timesheet, $updatedTimesheet, array("id" => $tempRowId));
                                                    $this->db->reset_query();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $resultSet["success"] = true;
                $resultSet["message"] = "Timesheet for $date successfully created.";
                $resultSet["title"] = "Timesheet created.";
                $resultSet["k"] = 1;
            }else{
                $resultSet["success"] = false;
                $resultSet["message"] = "No record found for $date to generate.";
                $resultSet["title"] = "No timesheet record found.";
                $resultSet["k"] = 3;
            }
        }

        $resultSet["updatedTimesheets"] = $updatedTimesheets;
        return $resultSet;
    }

    protected function getCurrentShiftSchedule($date=null, $employee=null){
        $updatedSchedule = new StdClass();
        $updatedSchedule->schedule = array();
        $updatedSchedule->custom_shift_id = 0;
        $updatedSchedule->shift_id = $employee->shift_id;
        $updatedSchedule->has_shift = $employee->shift_id ? 1 : 0;
        $updatedSchedule->date = $date ? date("Y-m-d", strtotime($date)) : null;
        if($date && $employee->id && $employee->shift_id){
            $currentDate = date("Y-m-d", strtotime($date));
            $weekday = strtolower(date("l", strtotime($currentDate)));
            $shift_resource_array = $this->getShiftResources($employee->shift_id);
            $schedule = $this->getScheduleList($weekday, $shift_resource_array);
            $alteredShifts = $this->getCustomizedShiftScheduleByDate($date, $employee->id);
            $updatedSchedule->schedule = $schedule;
            $updatedSchedule = $this->getAlteredShiftSchedule($updatedSchedule, $schedule, $alteredShifts);
        }
        return $updatedSchedule;
    }

    protected function getShiftResources($shift_id){
        $shift_resource_array = array();
        $shift_resource = $this->getShiftResource($shift_id);
        if(isset($shift_resource->shift_resource) && $shift_resource->shift_resource){
            $tempResource = $shift_resource->shift_resource;
            $tempResource = unserialize($tempResource);

            $shift_resource_array = array_map(function ($item) {
                return intval($item);
            }, $tempResource);
        }
        return $shift_resource_array;
    }
    
    protected function getAlteredShiftSchedule($updatedSchedule=null, $schedule=null, $alteredShifts=null){
        if(isset($schedule, $alteredShifts, $alteredShifts->has_shift) && $schedule && $alteredShifts && count(get_object_vars($alteredShifts)) > 0){
            $alteredCustomShiftId = 0;
            $ctrAlteredSchedule = false;
            $alteredHasShiftSchedule = intval($alteredShifts->has_shift);
            foreach ($alteredShifts->schedule as $kkx => $vvx) {
                if(isset($schedule->{$kkx}) && $schedule->{$kkx} && $schedule->{$kkx} !== $vvx && $vvx !== null && $alteredHasShiftSchedule === 1){
                    $schedule->{$kkx} = $vvx;
                    $ctrAlteredSchedule = true;
                }
                if($alteredHasShiftSchedule === 0){
                    $schedule->{$kkx} = null;
                    $ctrAlteredSchedule = true;
                }
            }
            if($ctrAlteredSchedule === true && $alteredShifts->custom_shift_id !== "0"){ $alteredCustomShiftId = $alteredShifts->custom_shift_id; }

            $updatedSchedule->schedule = $schedule;
            $updatedSchedule->has_shift = $alteredHasShiftSchedule;
            $updatedSchedule->custom_shift_id = $alteredCustomShiftId;
        }
        return $updatedSchedule;
    }

    protected function nextDayShiftCheker($date=null, $employee=null, $att_next_day=null){
        if($date && $employee && $att_next_day){
            $nextDate = date("Y-m-d", strtotime("+1 day", strtotime($date)));
            $nextShiftSchedule = $this->getCurrentShiftSchedule($nextDate, $employee);
            if(isset($nextShiftSchedule->has_shift) && intval($nextShiftSchedule->has_shift) == 1 &&
                (isset($nextShiftSchedule->schedule->am_start) && $nextShiftSchedule->schedule->am_start != null)){
                    $tempShiftStart = date("Y-m-d H:i:s", strtotime($nextShiftSchedule->date . " " . $nextShiftSchedule->schedule->am_start));
                    if(strtotime($tempShiftStart) > strtotime($att_next_day)){ $att_next_day = $tempShiftStart; }
            }elseif(isset($nextShiftSchedule->has_shift) && intval($nextShiftSchedule->has_shift) == 0){
                $_nextDate = date("Y-m-d", strtotime("+1 day", strtotime($nextDate)));
                $_nextShiftSchedule = $this->getCurrentShiftSchedule($_nextDate, $employee);
                if(isset($_nextShiftSchedule->has_shift) && intval($_nextShiftSchedule->has_shift) == 1 &&
                    (isset($_nextShiftSchedule->schedule->am_start) && $_nextShiftSchedule->schedule->am_start != null)){
                        $_tempShiftStart = date("Y-m-d H:i:s", strtotime($_nextShiftSchedule->date . " " . $_nextShiftSchedule->schedule->am_start));
                        if(strtotime($_tempShiftStart) > strtotime($att_next_day)){ $att_next_day = $_tempShiftStart; }
                }
            }
        }
        return $att_next_day;
    }
    public function generateTimesheetRecords($tempRow, $date, $weekday){
        $arrData = array();
        $updatedTimesheets = array();
        $timesheet_id = null;

        if(is_array($tempRow) && count($tempRow) > 0){
            $select = "employees.id, employees.lastname, employees.firstname, personnel.shift_id, personnel.is_flexi, attendance.biometric_id";
            $this->db->select($select);
            $this->db->from($this->tbl_employees . " employees");
            $this->db->join($this->tbl_personnel . " personnel", "personnel.biometric_id = employees.biometricno OR personnel.biometricno = employees.biometricno");
            $this->db->where("employees.id", $tempRow->emp_id);
            $qTemp = $this->db->get();

            if($qTemp->num_rows() == 1){
                $employee = $qTemp->row();
                $flexible = intval($employee->is_flexi) !== 0;
                $shift_id = $employee->shift_id;

                $tempResource = array();
                $shift_resource_array = array();

                $shift_resource = $this->getShiftResource($shift_id);
                if(isset($shift_resource->shift_resource) && $shift_resource->shift_resource){
                    $tempResource = $shift_resource->shift_resource;
                    $tempResource = unserialize($tempResource);

                    $shift_resource_array = array_map(function ($item) {
                        return intval($item);
                    }, $tempResource);
                }

                /*** $shift_resource = $this->getShiftResource($shift_id);
                $shift_resource_array = array_map(function ($item) {
                    return intval($item);
                }, unserialize($shift_resource->shift_resource)); ***/

                $schedule = $this->getScheduleList($weekday, $shift_resource_array);
                $am_start = !empty($schedule) ? $schedule->am_start : null;
                $am_end = !empty($schedule) ? $schedule->am_end : null;
                $pm_start = !empty($schedule) ? $schedule->pm_start : null;
                $pm_end = !empty($schedule) ? $schedule->pm_end : null;

                $no_shift_schedule = false;

                if (($am_start === null && $am_end === null && $pm_start === null && $pm_end === null) || empty($schedule)) {
                    $no_shift_schedule = true;
                }

                $am_shift_only = (($am_start !== null && $am_end !== null) && ($pm_start === null && $pm_end === null));
                $pm_shift_only = (($am_start === null && $am_end === null) && ($pm_start !== null && $pm_end !== null));
                $isWholeDay = ($am_shift_only == false && $pm_shift_only == false);

                $tempHoliday = (object) $this->getCurrentDateIsHoliday($date);
                $isHoliday = ($tempHoliday->is_holiday == true)? 1: 0;
                $payRateId = ($tempHoliday->is_holiday == true && $tempHoliday->payrate_id)? $tempHoliday->payrate_id: 0;
            }
        }

        $arrData["updated_timesheets"] = $updatedTimesheets;
        return $arrData;
    }

    public function generateNoShiftOvertime($tempRow, $night_diff_cfg, $date, $generated_manually){
        $arrData = array();
        $hasOvertimeRequest = false;
        $total_accredited_ot_hrs = 0;
        $total_accredited_ot_nightdiff_hrs = 0;
        $ot_night_diff = 0;
        $overtime_start = null;
        $overtime_end = null;

        $nshiftParams = $this->nightshiftParams($date);
        $_nightShiftStart = $nshiftParams["start"];
        $_nightShiftEnd = $nshiftParams["end"];
        $hasShiftSchedule = $tempRow->has_shift == 1;
        
        /** start shift schedule ***/
        $shift_am_start = $tempRow->shift_am_start ? date("H:i:s", strtotime($tempRow->shift_am_start)): null;
        $shift_am_end = $tempRow->shift_am_end ? date("H:i:s", strtotime($tempRow->shift_am_end)): null;
        $shift_pm_start = $tempRow->shift_pm_start ? date("H:i:s", strtotime($tempRow->shift_pm_start)): null;
        $shift_pm_end = $tempRow->shift_pm_end ? date("H:i:s", strtotime($tempRow->shift_pm_end)): null;
        /** end shift schedule ***/
        
        $am_shift_only = (($shift_am_start !== null && $shift_am_end !== null) && ($shift_pm_start === null && $shift_pm_end === null));
        $overtime = $this->db
            ->get_where($this->tbl_overtime,
                array(
                    "employee" => $tempRow->emp_id,
                    "DATE(date_from)" => $date,
                    "status" => "Approved"
                )
            )->result();
        $this->db->reset_query();

        $hasOT = sizeof((array) $overtime) >= 1 ? 1 : 0;
        if (intval($hasOT) >= 1) {
            foreach ($overtime as $_overtime) {
                $regularOTHours = 0;
                $nDiffOTHours = 0;
                $ot_hrs = 0;
                $ot_night_diff = 0;

                $_otStartDate = date("Y-m-d", strtotime($_overtime->date_from));
                $_otStart = date('Y-m-d H:i', strtotime($_overtime->date_from));
                $_otEnd = date('Y-m-d H:i', strtotime($_overtime->date_to));

                $ot_start_dtr = date("Y-m-d H:i", strtotime($tempRow->date." ".$tempRow->shift_pm_end));
                $ot_end_dtr = date("Y-m-d H:i", strtotime($tempRow->date." ".$tempRow->pm_out));

                $ot_start = strtotime($ot_start_dtr) < strtotime(date('Y-m-d H:i', strtotime($_overtime->date_from)))
                ? date('Y-m-d H:i', strtotime($_overtime->date_from)) : $ot_start_dtr;
                $ot_end = strtotime($ot_end_dtr) > strtotime(date('Y-m-d H:i', strtotime($_overtime->date_to)))
                    ? date('Y-m-d H:i', strtotime($_overtime->date_to)) : $ot_end_dtr;

                $_otNdiffStart = date("Y-m-d H:i", strtotime($_otStartDate." 22:00:00"));
                $_otNdiffEnd = date("Y-m-d H:i", strtotime("+1 day", strtotime($_otStartDate." 05:00:00")));

                if(isset($night_diff_cfg->start_time) && $night_diff_cfg->start_time){
                    $_otNdiffStart = date("Y-m-d H:i", strtotime($_otStartDate." ".$night_diff_cfg->start_time));
                }

                if(isset($night_diff_cfg->end_time) && $night_diff_cfg->end_time){
                    $_otNdiffEnd = date("Y-m-d H:i", strtotime("+1 day", strtotime($_otStartDate." ".$night_diff_cfg->end_time)));
                }

                if($_otEnd && strtotime($_otEnd) > 0 && strtotime($_otEnd) > strtotime($_otNdiffStart)){
                    $regularOTHours = strtotime($_otNdiffStart) - strtotime($_otStart);
                    $regularOTHours = ($regularOTHours / 3600);

                    $nDiffOTHours = strtotime($_otEnd) - strtotime($_otNdiffStart);
                    $nDiffOTHours = ($nDiffOTHours / 3600);
                }else{
                    $regularOTHours = strtotime($_otEnd) - strtotime($_otStart);
                    $regularOTHours = ($regularOTHours / 3600);
                }

                $collectionScript = $this->attendanceCollectionScript($tempRow);
                $tempAttrAttendance = $collectionScript["arrAttendance"];
                if(is_array($collectionScript["attendance"]) && !empty($collectionScript["attendance"])){
                    foreach ($collectionScript["attendance"] as $key => $value) {
                        $tempKey = "_attendance_{$key}";
                        ${$tempKey} = $value;
                    }
                }

                $shift_basis = $am_shift_only ? date("Y-m-d H:i", strtotime($_attendance_am_end)) : date("Y-m-d H:i", strtotime($_attendance_pm_end));
                if($hasShiftSchedule === false && count($tempAttrAttendance) > 1 && end($tempAttrAttendance)){
                    $shift_basis = end($tempAttrAttendance);
                }

                $startOfShift = reset($tempAttrAttendance);
                $isNightShift = strtotime($startOfShift) >= strtotime($_nightShiftStart) && strtotime($startOfShift) <= strtotime($_nightShiftEnd);
                if($isNightShift){ $shift_basis = $_otStart; }
                
                $arrShifts = array();
                $shiftProps = array("shift_am_start", "shift_am_end", "shift_pm_start", "shift_pm_end");
                $tempShiftDate = date("Y-m-d", strtotime($date));
                foreach ($shiftProps as $key => $value) {
                    if(${$value}){
                        if($key > 0 ){
                            $prevValue = ${$shiftProps[$key-1]};
                            if(strtotime(${$value}) < strtotime($prevValue)){ $tempShiftDate = date("Y-m-d", strtotime("+1 day", strtotime($tempShiftDate))); }
                        }
                        $nValue = date("Y-m-d H:i", strtotime($tempShiftDate." ".${$value}));
                        $arrShifts[] = $nValue;
                    }
                }

                if($isNightShift === false && is_array($arrShifts) && !empty($arrShifts)){
                    $firstShift = reset($arrShifts);
                    if(strtotime($startOfShift) < strtotime($firstShift)){
                        $shift_basis = $firstShift;
                    }
                }

                if(count($tempAttrAttendance) > 1){
                    $parameters = array("hasShiftSchedule" => $hasShiftSchedule, "overtime" => $_overtime, "timesheetExist" => $tempRow,
                        "otAttendances" => $tempAttrAttendance, "nightDiffCfg" => $night_diff_cfg, "shiftBasis" => $shift_basis, "otNdiffStart" => $_otNdiffStart,
                        "otNdiffEnd" => $_otNdiffEnd, "nDiffOTHours" => $nDiffOTHours, "isNightShift" => $isNightShift,
                        "regularOTHours" => $regularOTHours, "otNightDiff"=>$ot_night_diff);
                    $otScript = $this->overtimeComputationScript($parameters);

                    $regularOTHours = $otScript["regularOTHours"];
                    $nDiffOTHours = $otScript["nDiffOTHours"];
                    $ot_hrs = $otScript["ot_hrs"];
                    $ot_night_diff = number_format($otScript["ot_night_diff"], 2, '.', '');
                    $ot_start = $otScript["ot_start"];
                    $ot_end = $otScript["ot_end"];

                    $overtime_start = date("Y-m-d H:i", strtotime($ot_start));
                    $overtime_end = date("Y-m-d H:i", strtotime($ot_end));
                }else{
                    $overtime_start = date("Y-m-d H:i", strtotime($_overtime->date_from));
                    $overtime_end = date("Y-m-d H:i", strtotime($_overtime->date_to));

                    $tempRegOtHours = $regularOTHours;
                    if($tempRegOtHours >= 5){
                        $tempRegOtHours = $tempRegOtHours - 1;
                    }
                    $ot_hrs = $tempRegOtHours;
                    $ot_night_diff = $nDiffOTHours;
                }

                $regularOTHours = floatval($regularOTHours) > 0 ? $regularOTHours: 0;
                $nDiffOTHours = floatval($nDiffOTHours) > 0 ? $nDiffOTHours: 0;
                $ot_hrs = floatval($ot_hrs) > 0 ? $ot_hrs: 0;
                $ot_night_diff = floatval($ot_night_diff) > 0 ? $ot_night_diff: 0;

                if($ot_hrs > 0){
                    $tempOtHoursToMinutes = $ot_hrs * 60;
                    $divisibleBy30 = $tempOtHoursToMinutes % 30;
                    if($divisibleBy30 !== 0){
                        $tempOtHoursToMinutes = $tempOtHoursToMinutes - $divisibleBy30;
                    }
                    $ot_hrs = $tempOtHoursToMinutes / 60;
                }

                if($ot_night_diff > 0){
                    $tempOtNdiffToMinutes = $ot_night_diff * 60;
                    $divisibleBy30Ndiff = $tempOtNdiffToMinutes % 30;
                    if($divisibleBy30Ndiff !== 0){
                        $tempOtNdiffToMinutes = $tempOtNdiffToMinutes - $divisibleBy30Ndiff;
                    }
                    $ot_night_diff = $tempOtNdiffToMinutes / 60;
                }

                $regularOTHours = round($regularOTHours, 2);
                $nDiffOTHours = round($nDiffOTHours, 2);

                if($ot_end == $ot_start || strtotime($ot_end) < strtotime($ot_start)){
                    $overtime_start = date("Y-m-d H:i", strtotime($_overtime->date_from));
                    $overtime_end = date("Y-m-d H:i", strtotime($_overtime->date_to));
                    $regularOTHours = 0;
                }
                
                if (sizeof((array) $tempRow) >= 1 && intval($generated_manually) <= 0) {
                    if($regularOTHours == 0 && $ot_hrs == 0 && $nDiffOTHours == 0 && $ot_night_diff == 0){
                        $this->db->where("timesheet_id", $tempRow->id);
                        $this->db->delete($this->tbl_timesheet_overtime);
                        $this->db->reset_query();
                        $hasOvertimeRequest = false;
                    }else{
                        $this->db->where("timesheet_id", $tempRow->id);
                        $this->db->update($this->tbl_timesheet_overtime,
                            array(
                                "overtime_id" => $_overtime->id,
                                "overtime_in" => $overtime_start,
                                "overtime_out" => $overtime_end,
                                "total_hrs" => $regularOTHours,
                                "accredited_hrs" => $ot_hrs,
                                "ndiff_hrs" => $nDiffOTHours,
                                "accredited_ndiff_hrs" => $ot_night_diff,
                            ));
                        $this->db->reset_query();
                        $hasOvertimeRequest = true;
                    }
                } else {
                    $ts_ot_exist = $this->db
                        ->where("timesheet_id", $tempRow->id)
                        ->where("overtime_id", $_overtime->id)
                        ->get($this->tbl_timesheet_overtime)
                        ->row();

                    if (sizeof((array) $ts_ot_exist) >= 1) {
                        if($regularOTHours == 0 && $ot_hrs == 0 && $nDiffOTHours == 0 && $ot_night_diff == 0){
                            $this->db->where("id", $ts_ot_exist->id);
                            $this->db->delete($this->tbl_timesheet_overtime);
                            $this->db->reset_query();
                            $hasOvertimeRequest = false;
                        }else{
                            $this->db->where("id", $ts_ot_exist->id);
                            $this->db->update($this->tbl_timesheet_overtime,
                                array(
                                    "overtime_in" => $overtime_start,
                                    "overtime_out" => $overtime_end,
                                    "total_hrs" => $regularOTHours,
                                    "accredited_hrs" => $ot_hrs,
                                    "ndiff_hrs" => $nDiffOTHours,
                                    "accredited_ndiff_hrs" => $ot_night_diff,
                                ));
                            $this->db->reset_query();
                            $hasOvertimeRequest = true;
                        }
                    } else {
                        $this->db->insert($this->tbl_timesheet_overtime,
                            array(
                                "timesheet_id" => $tempRow->id,
                                "overtime_id" => $_overtime->id,
                                "overtime_in" => $overtime_start,
                                "overtime_out" => $overtime_end,
                                "total_hrs" => $regularOTHours,
                                "accredited_hrs" => $ot_hrs,
                                "ndiff_hrs" => $nDiffOTHours,
                                "accredited_ndiff_hrs" => $ot_night_diff,
                            ));
                        $hasOvertimeRequest = true;
                    }
                }

                $total_accredited_ot_hrs += $ot_hrs;
                $total_accredited_ot_nightdiff_hrs += $ot_night_diff;
            }
        }

        $arrData["total_accredited_ot_hrs"] = $total_accredited_ot_hrs;
        $arrData["ot_night_diff"] = $total_accredited_ot_nightdiff_hrs;
        $arrData["overtime_in"] = $overtime_start;
        $arrData["overtime_out"] = $overtime_end;
        $arrData["allow_overtime_request"] = $hasOvertimeRequest; 

        return $arrData;
    }



    public function generateTimesheetComputation($timesheet_exist, $employee_time_sheet, $updatedTimesheets, $attendance, $date,
        $no_shift_schedule, $am_start, $am_end, $pm_start, $pm_end, $am_shift_only, $pm_shift_only, $props, $flexibleEmployee, $payrollType){
        $arrData = array();
        $flexibleEmployee = $flexibleEmployee ? true: false;
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $flexible = $employee_time_sheet->is_flexi;
        $flexibleEmployee = $flexibleEmployee && $flexible ? true: false;

        $timesheet_id = null;
        $hasNextDayDate = false;
        if(($am_start && $am_end) && (strtotime($am_end) < strtotime($am_start)) && $hasNextDayDate === false){ $hasNextDayDate = true; }
        if(($am_end && $pm_start) && (strtotime($pm_start) < strtotime($am_end) && $hasNextDayDate === false)){ $hasNextDayDate = true; }
        if(($pm_start && $pm_end) && (strtotime($pm_end) < strtotime($pm_start) && $hasNextDayDate === false)){ $hasNextDayDate = true; }

        /*** super flexible employee ***/
        $superFlexibleEmployee = false;
        $this->db->select("per.is_flexi");
        $this->db->from($this->tbl_employees." emp");
        $this->db->join($this->tbl_personnel." per", "per.biometric_id = emp.biometricno OR per.biometricno = emp.biometricno");
        $this->db->where("emp.id", $employee_time_sheet->emp_id);
        $this->db->group_start();
        $this->db->where("per.is_flexi", 3);
        $this->db->or_where("per.is_flexi", 4);
        $this->db->group_end();
        $this->db->order_by("emp.id", "DESC");
        $this->db->limit(1);
        $qTempEmployee = $this->db->get();
        if($qTempEmployee->num_rows() == 1){ $superFlexibleEmployee = true; }
        $this->db->reset_query();
        /*** super flexible employee ***/
        foreach ($attendance as $key => $value) {
            $tempDate = date("Y-m-d", strtotime($value));
            if($tempDate !== $date && $hasNextDayDate === false){ unset($attendance[$key]); }
        }

        $attendance_log_ctr = sizeof($attendance);
        /*** super flexible employee script function ***/
        if($attendance_log_ctr === 1 && $superFlexibleEmployee){
            $singleAttRecord = $attendance[0];
            $_hasNextDayDate = false;
            $_amStart = $am_start ? date("Y-m-d H:i:s", strtotime($date." ".$am_start)): null;
            $_amEnd = $am_end ? date("Y-m-d H:i:s", strtotime($date." ".$am_end)): null;
            $_pmStart = $pm_start ? date("Y-m-d H:i:s", strtotime($date." ".$pm_start)): null;
            $_pmEnd = $pm_end ? date("Y-m-d H:i:s", strtotime($date." ".$pm_end)): null;
            
            if(($_amStart && $_amEnd) && (strtotime($_amEnd) < strtotime($_amStart))){
                $_amEnd = date("Y-m-d H:i:s", strtotime("+1 day", strtotime($_amEnd)));
                $_hasNextDayDate = true;
            }
            
            if(($_amEnd && $_pmStart) && (strtotime($_pmStart) < strtotime($_amEnd) || $_hasNextDayDate)){
                $_pmStart = date("Y-m-d H:i:s", strtotime("+1 day", strtotime($_pmStart)));
                $_hasNextDayDate = true;
            }
            
            if(($_pmStart && $_pmEnd) && (strtotime($_pmEnd) < strtotime($_pmStart) || $_hasNextDayDate)){
                $_pmEnd = date("Y-m-d H:i:s", strtotime("+1 day", strtotime($_pmEnd)));
            }

            if(strtotime($singleAttRecord) < strtotime($_amStart)){ $_amStart = date("Y-m-d H:i:s", strtotime($singleAttRecord)); }

            /*** reset attendance record ***/
            $attendance = array();
            if($_amStart){ $attendance[] = $_amStart; }
            if($_amEnd){ $attendance[] = $_amEnd; }
            if($_pmStart){ $attendance[] = $_pmStart; }
            if($_pmEnd){ $attendance[] = $_pmEnd; }
            /*** reset attendance record ***/

            $attendance_log_ctr = sizeof($attendance);
        }
        /*** super flexible employee script function ***/

        $no_shift_schedule = isset($timesheet_exist->has_shift) && $timesheet_exist->has_shift !== null ?
            intval($timesheet_exist->has_shift) == 0 : $no_shift_schedule;

        $hasOT = $employee_time_sheet->has_overtime;
        $isHoliday = (isset($employee_time_sheet->is_holiday) && intval($employee_time_sheet->is_holiday) == 1)? true: false;
        $isWholeDay = ($am_shift_only === false && $pm_shift_only === false && $no_shift_schedule === false);

        if($attendance_log_ctr > 0){
            for($ii = 0; $ii < 4; $ii++){
                $default_prop = $props[$ii];
                $time = isset($attendance[$ii]) && $attendance[$ii] ?
                    date('Y-m-d H:i', strtotime($attendance[$ii])): null;

                $employee_time_sheet->$default_prop = $time;
            }
        }
        
        /*** approved Travel order tagging ***/
        $toDetails = array();
        if(isset($timesheet_exist->id, $timesheet_exist->emp_id) && ($timesheet_exist->id && $timesheet_exist->emp_id)){
            $approvedTravelOrder = false;
            $approvedTravelOrder = $this->hasApprovedTravelOrder($timesheet_exist->date, $timesheet_exist->emp_id);
            if($approvedTravelOrder){
                $toDetails = $this->hasApprovedTravelOrder($timesheet_exist->date, $timesheet_exist->emp_id, true);
                $flexible = $approvedTravelOrder;
            }
        }
        /*** approved Travel order tagging ***/
        if ($attendance_log_ctr === 4) {
            foreach ($attendance as $i => $time) {
                if(isset($props[$i]) && $props[$i]){
                    $prop = $props[$i];
                    $employee_time_sheet->$prop = date('Y-m-d H:i', strtotime($time));
                }
            }
        } else {
            if ($flexible) { // fore employee with 1 in and out
                if ($attendance_log_ctr > 2 || $attendance_log_ctr > 4) {
                    $employee_time_sheet->scrub_status = 2;
                    $employee_time_sheet->comments = "[System Generated]: Multiple entry detected for flexi-time employees.";

                    foreach ($attendance as $i => $time) {
                        if (($i + 1) > 4){ break; }
                        if(isset($props[$i]) && $props[$i]){
                            $prop = $props[$i];
                            $employee_time_sheet->$prop = date('Y-m-d H:i', strtotime($time));
                        }
                    }
                } elseif ($attendance_log_ctr < 2) {
                    $employee_time_sheet->scrub_status = 1;
                    $employee_time_sheet->comments = "[System Generated]: Lacking entry detected.";
                    foreach ($attendance as $i => $time) {
                        if(isset($props[$i]) && $props[$i]){
                            $prop = $props[$i];
                            $employee_time_sheet->$prop = date('Y-m-d H:i', strtotime($time));
                        }
                    }
                } else {
                    if ($no_shift_schedule === false) {
                        $first_record = date('Y-m-d H:i', strtotime($attendance[0]));
                        $end_record = date('Y-m-d H:i', strtotime($attendance[1]));

                        $startMeridian = date("A", strtotime($first_record));
                        $endMeridian = date("A", strtotime($end_record));

                        if (($am_end && $am_end !== null) && strtotime($first_record) <= strtotime($date . " " . $am_end)) {
                            $employee_time_sheet->am_in = $first_record;
                        } elseif (strtotime($first_record) >= strtotime($am_start)) {
                            $employee_time_sheet->pm_in = $first_record;
                        }

                        if (($pm_start && $pm_start !== null) && strtotime($end_record) < strtotime($date . " " . $pm_start)) {
                            $employee_time_sheet->am_out = $end_record;
                        } else {
                            $employee_time_sheet->pm_out = $end_record;
                        }

                        
                        if (empty($employee_time_sheet->am_out) && empty($employee_time_sheet->pm_in) || ($employee_time_sheet->am_out == null && $employee_time_sheet->pm_in == null)) {
                            $employee_time_sheet->am_out = date("Y-m-d H:i", strtotime($date . " " . $am_end));
                            $employee_time_sheet->pm_in = date("Y-m-d H:i", strtotime($date . " " . $pm_start));
                        }

                        if($am_shift_only && (($startMeridian == "AM" && $endMeridian == "AM") || ($startMeridian == "AM" && $endMeridian == "PM"))){
                            $employee_time_sheet->am_in = $first_record;
                            $employee_time_sheet->am_out = $end_record;
                            $employee_time_sheet->pm_in = null;
                            $employee_time_sheet->pm_out = null;
                        }

                        if($pm_shift_only && (($startMeridian == "AM" && $endMeridian == "PM") || ($startMeridian == "PM" && $endMeridian == "PM"))){
                            $employee_time_sheet->am_in = null;
                            $employee_time_sheet->am_out = null;
                            $employee_time_sheet->pm_in = $first_record;
                            $employee_time_sheet->pm_out = $end_record;
                        }

                        if($isWholeDay){
                            if($startMeridian == "AM" && $endMeridian == "AM"){
                                $employee_time_sheet->am_in = $first_record;
                                $employee_time_sheet->am_out = $end_record;
                                $employee_time_sheet->pm_in = null;
                                $employee_time_sheet->pm_out = null;
                            }elseif($startMeridian == "PM" && $endMeridian == "PM"){
                                $employee_time_sheet->am_in = null;
                                $employee_time_sheet->am_out = null;
                                $employee_time_sheet->pm_in = $first_record;
                                $employee_time_sheet->pm_out = $end_record;
                            }elseif($startMeridian == "AM" && $endMeridian == "PM"){
                                $employee_time_sheet->am_in = $first_record;
                                $employee_time_sheet->pm_out = $end_record;

                                $tempPmStart = date("Y-m-d H:i", strtotime($date . " " . $pm_start));
                                if(($date && $pm_start && $end_record) && strtotime($end_record) > strtotime($tempPmStart)){
                                    if (!empty($employee_time_sheet->am_out) || $employee_time_sheet->am_out !== null || $employee_time_sheet->am_out == null){
                                        $employee_time_sheet->am_out = date("Y-m-d H:i", strtotime($date . " " . $am_end));
                                    }
                                    if(!empty($employee_time_sheet->pm_in) || $employee_time_sheet->pm_in !== null || $employee_time_sheet->pm_in == null) {
                                        $employee_time_sheet->pm_in = date("Y-m-d H:i", strtotime($date . " " . $pm_start));
                                    }
                                }else{
                                    $employee_time_sheet->pm_in = null;
                                    $employee_time_sheet->pm_out = null;
                                }
                            }
                        }
                    } else {
                        foreach ($attendance as $i => $time) {
                            if(isset($props[$i]) && $props[$i]){
                                $prop = $props[$i];
                                $employee_time_sheet->$prop = date('Y-m-d H:i', strtotime($time));
                            }
                        }
                    }
                }
            } else { // for employees required for morning in, out and afternoon in & out
                if ($attendance_log_ctr > 4) {
                    if (intval($hasOT) <= 0) {
                        $employee_time_sheet->scrub_status = 2;
                        $employee_time_sheet->comments = "[System Generated]: Multiple entry detected.";
                    }

                    foreach ($attendance as $i => $time) {
                        if (($i + 1) > 4){ break; }
                        if(isset($props[$i]) && $props[$i]){
                            $prop = $props[$i];
                            $employee_time_sheet->$prop = date('Y-m-d H:i', strtotime($time));
                        }
                    }
                } elseif ($attendance_log_ctr < 4) {
                    $employee_time_sheet->scrub_status = 1;
                    $employee_time_sheet->comments = "[System Generated]: Lacking entry detected.";

                    if ($attendance_log_ctr === 2 && $no_shift_schedule === false) {
                        $first_record = date('Y-m-d H:i', strtotime($attendance[0]));
                        $end_record = date('Y-m-d H:i', strtotime($attendance[1]));

                        $tempAmStart = date("Y-m-d H:i", strtotime($date." ".$am_start));
                        $tempAmEnd = date("Y-m-d H:i", strtotime($date." ".$am_end));
                        $tempPmStart = date("Y-m-d H:i", strtotime($date." ".$pm_start));

                        $tempNextDate = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                        $xxHasNextDayShift = false;

                        if(($am_start && $am_end) && strtotime($am_end) < strtotime($am_start)){
                            $tempAmEnd = strtotime(date('Y-m-d H:i', strtotime($tempNextDate . " " . $am_end)));
                            $xxHasNextDayShift = true;
                        }
                        if(($am_end && $pm_start) && (strtotime($pm_start) < strtotime($am_end) || $xxHasNextDayShift)){
                            $tempPmStart = strtotime(date('Y-m-d H:i', strtotime($tempNextDate . " " . $pm_start)));
                        }

                        $temp2hrsAmStart = date("Y-m-d H:i", strtotime("+2 hours", strtotime($tempAmStart)));
                        $temp2hrsPmStart = date("Y-m-d H:i", strtotime("+2 hours", strtotime($tempPmStart)));

                        if (strtotime($first_record) <= strtotime($tempAmEnd)) {
                            $employee_time_sheet->am_in = $first_record;
                        }
                        if ((strtotime($first_record) >= strtotime($temp2hrsAmStart)) && (strtotime($first_record) <= strtotime($tempAmEnd))) {
                            $employee_time_sheet->am_out = $first_record;
                            $employee_time_sheet->am_in = null;
                        }
                        if ((strtotime($first_record) >= strtotime($temp2hrsAmStart))
                            && (strtotime($first_record) >= strtotime($tempAmEnd) || strtotime($first_record) <= strtotime($tempPmStart))) {
                            $employee_time_sheet->pm_in = $first_record;
                            $employee_time_sheet->am_in = null;
                            $employee_time_sheet->am_out = null;
                        }
                        
                        if ($am_shift_only) {
                            // reset scrub_status for with half day schedule only to unmark them for lacking entry
                            $employee_time_sheet->scrub_status = 0;
                            $employee_time_sheet->comments = null;

                            $employee_time_sheet->am_out = $end_record;
                        } elseif ($pm_shift_only) {
                            // reset scrub_status for with half day schedule only to unmark them for lacking entry
                            $employee_time_sheet->scrub_status = 0;
                            $employee_time_sheet->comments = null;

                            $employee_time_sheet->pm_out = $end_record;
                        } else {
                            $hasAmEntry = $employee_time_sheet->am_in || $employee_time_sheet->am_out;
                            if($hasAmEntry){
                                if ((strtotime($end_record) > strtotime($employee_time_sheet->am_out)) &&
                                ((strtotime($end_record) > strtotime($temp2hrsAmStart)) && (strtotime($end_record) < strtotime($temp2hrsPmStart)))) {
                                    $employee_time_sheet->pm_in = $end_record;
                                    $employee_time_sheet->pm_out = null;
                                }elseif((strtotime($end_record) > strtotime($employee_time_sheet->pm_in)) &&
                                ((strtotime($end_record) > strtotime($temp2hrsAmStart)) && (strtotime($end_record) > strtotime($temp2hrsPmStart)))){
                                    $employee_time_sheet->am_out = null;
                                    $employee_time_sheet->pm_in = null;
                                    $employee_time_sheet->pm_out = $end_record;
                                }
                            }else{
                                if (strtotime($end_record) > strtotime($temp2hrsAmStart) && strtotime($end_record) < strtotime($tempPmStart)) {
                                    $employee_time_sheet->am_out = $end_record;
                                    $employee_time_sheet->pm_in = null;
                                    $employee_time_sheet->pm_out = null;
                                } elseif((strtotime($end_record) > strtotime($employee_time_sheet->pm_in)) &&
                                ((strtotime($end_record) > strtotime($temp2hrsAmStart)) && (strtotime($end_record) > strtotime($tempPmStart)))){
                                    $employee_time_sheet->pm_out = $end_record;
                                }
                            }
                        }
                    } else {
                        foreach ($attendance as $i => $time) {
                            if(isset($props[$i]) && $props[$i]){
                                $prop = $props[$i];
                                $employee_time_sheet->$prop = date('Y-m-d H:i', strtotime($time));
                            }
                        }
                    }
                }
            }
        }

        // $employee_time_sheet->scrub_status === 0 &&
        if ($no_shift_schedule === false) {
            $currentAttLogs = 0;
            $am_in = strtotime($employee_time_sheet->am_in);
            $am_out = strtotime($employee_time_sheet->am_out);
            $pm_in = strtotime($employee_time_sheet->pm_in);
            $pm_out = strtotime($employee_time_sheet->pm_out);

            if(is_array($props) && !empty($props)){ foreach ($props as $prop) { if(${$prop}){$currentAttLogs++; } } }

            $nextDayDate = date("Y-m-d", strtotime("+1 day", strtotime($date)));
            $hasNextDayShift = false;

            $_am_start = strtotime(date('Y-m-d H:i', strtotime($date . " " . $am_start)));
            $_am_end = strtotime(date('Y-m-d H:i', strtotime($date . " " . $am_end)));
            $_pm_start = strtotime(date('Y-m-d H:i', strtotime($date . " " . $pm_start)));
            $_pm_end = strtotime(date('Y-m-d H:i', strtotime($date . " " . $pm_end)));

            if($_am_end < $_am_start && ($_am_end !== null && $_am_start !== null)){
                $_am_end = strtotime(date('Y-m-d H:i', strtotime($nextDayDate . " " . $am_end)));
                $hasNextDayShift = true;
            }
            
            if($_pm_start < $_am_end && ($am_end !== null && $pm_start !== null)){
                $_pm_start = strtotime(date('Y-m-d H:i', strtotime($nextDayDate . " " . $pm_start)));
                $hasNextDayShift = true;
            }elseif(($am_end !== null && $pm_start !== null) && $hasNextDayShift === true){
                $_pm_start = strtotime(date('Y-m-d H:i', strtotime($nextDayDate . " " . $pm_start)));
            }

            if($_pm_end < $_pm_start && ($pm_end !== null && $pm_start !== null)){
                $_pm_end = strtotime(date('Y-m-d H:i', strtotime($nextDayDate . " " . $pm_end)));
                $hasNextDayShift = true;
            }elseif(($pm_end !== null && $pm_start !== null) && $hasNextDayShift === true){
                $_pm_end = strtotime(date('Y-m-d H:i', strtotime($nextDayDate . " " . $pm_end)));
            }

            $am2hrsDeduction = strtotime(date("Y-m-d H:i", strtotime("+30 minutes", $_am_start)));
            $amHalfDayAbsent = strtotime(date("Y-m-d H:i", strtotime("+1 hour 1 minute", $_am_start)));
            $pmHalfDayAbsent = strtotime(date("Y-m-d H:i", strtotime("+30 minutes", $_pm_start)));
            
            /*** new feature autofill ***/
            /*** for employees with 1 attendance am / autofill AM and PM for employees with TO ***/
            $tempAttendance = $am_in;
            if($flexible && $currentAttLogs === 1 && $tempAttendance){
                if (($_am_start && $_am_end) && $tempAttendance <= $_am_end) {
                    $employee_time_sheet->am_in = date("Y-m-d H:i", $tempAttendance);
                }
                if ($tempAttendance >= $am2hrsDeduction && $tempAttendance <= $_am_end) {
                    $employee_time_sheet->am_out = date("Y-m-d H:i", $tempAttendance);
                    $employee_time_sheet->am_in = null;
                    $am_in = null;
                    $am_out = $tempAttendance;
                }
                if (($tempAttendance >= $am2hrsDeduction) 
                    && ($tempAttendance >= $_am_end || $tempAttendance <= $_pm_start)) {
                    $employee_time_sheet->pm_in = date("Y-m-d H:i", $tempAttendance);
                    $employee_time_sheet->am_in = null;
                    $employee_time_sheet->am_out = null;
                    $am_in = null;
                    $am_out = null;
                    $pm_in = $tempAttendance;
                }

                $hasAmEntry = $employee_time_sheet->am_in || $employee_time_sheet->am_out;
                if($hasAmEntry === false){
                    if ($am_out && $tempAttendance > $am_out &&
                    ($tempAttendance > $am2hrsDeduction && $tempAttendance < $pmHalfDayAbsent)) {
                        $employee_time_sheet->pm_in = date("Y-m-d H:i", $tempAttendance);
                        $employee_time_sheet->pm_out = null;
                        $pm_out = null;
                        $pm_in = $tempAttendance;
                    } elseif ($pm_in && $tempAttendance > $pm_in &&
                    ($tempAttendance > $am2hrsDeduction && $tempAttendance > $pmHalfDayAbsent)){
                        $employee_time_sheet->pm_out = date("Y-m-d H:i", $tempAttendance);
                        $employee_time_sheet->pm_in = null;
                        $pm_in = null;
                        $pm_out = $tempAttendance;
                    }
                }

                if($am_in && (is_object($toDetails) && count(get_object_vars($toDetails)) > 0)){
                    $tempDates = array();
                    $tempDates[] = $am_in;
                    $_TOuptoDate = strtotime($toDetails->date_to);
                    if(($_TOuptoDate && $_am_end) && $_TOuptoDate < $_am_end){
                        $tempDates[] = $_TOuptoDate;
                        /*** $employee_time_sheet->am_out = date("Y-m-d H:i", $_TOuptoDate);
                        $am_out = $_TOuptoDate; ***/
                    }elseif(($_TOuptoDate && $_am_end) && $_TOuptoDate >= $_am_end){
                        /*** $employee_time_sheet->am_out = date("Y-m-d H:i", $_am_end);
                        $am_out = $_am_end; ***/
                        $tempDates[] = $_am_end;
                    }
                    
                    if(($_TOuptoDate && $_pm_start) && $_TOuptoDate < $_pm_start){
                        $tempDates[] = $_TOuptoDate;
                       /*** $employee_time_sheet->pm_in = date("Y-m-d H:i", $_TOuptoDate);
                        $pm_in = $_TOuptoDate;
                        ***/ 
                    }elseif(($_TOuptoDate && $_pm_start) && $_TOuptoDate >= $_pm_start){
                        $tempDates[] = $_pm_start;
                        /*** $employee_time_sheet->pm_in = date("Y-m-d H:i", $_pm_start);
                        $pm_in = $_pm_start; ***/
                    }
                    
                    if(($_TOuptoDate && $_pm_end && $pmHalfDayAbsent) && ($_TOuptoDate >= $pmHalfDayAbsent && $_TOuptoDate <= $_pm_end)){
                        $tempDates[] = $_TOuptoDate;
                       /*** $employee_time_sheet->pm_out = date("Y-m-d H:i", $_pm_end);
                        $pm_out = $_pm_end; ***/
                    }elseif(($_TOuptoDate && $_pm_end) && $_TOuptoDate >= $_pm_end){
                        $tempDates[] = $_pm_end;
                        /*** $employee_time_sheet->pm_out = date("Y-m-d H:i", $_pm_end);
                        $pm_out = $_pm_end; ***/
                    }

                    if(is_array($tempDates) && count($tempDates) === 4 && (is_array($props) && !empty($props))){
                        foreach ($props as $key => $prop) {
                            if(isset($tempDates[$key]) && $tempDates[$key]){
                                $employee_time_sheet->{$prop} = date("Y-m-d H:i", $tempDates[$key]);
                                ${$prop} = $tempDates[$key];
                            }
                        }
                    }
                }
            }
            /*** for employees with 1 attendance am / autofill AM and PM for employees with TO * ***/
            /*** new feature autofill ***/

            // START AM CALCULATION
            if ($am_in >= $_am_end) {
                $employee_time_sheet->am_late = 0;
            } else {
                if (($am_in > $_am_start) && $am_in) {
                    $employee_time_sheet->am_late = round(($am_in - $_am_start) / 60, 2);
                }
                /*** am 2hrs deduction custom ***/
                $has2hrsDeduction = false;
                if(($am_in > $am2hrsDeduction) && $am_in && $isHoliday === false){
                    $amPlus2hrs = strtotime(date("Y-m-d H:i", strtotime("+2 hours", strtotime($date . " " . $am_start))));
                    $employee_time_sheet->am_late = round(($amPlus2hrs - $_am_start) / 60, 2);
                    $has2hrsDeduction = true;
                }

                if($flexibleEmployee && $has2hrsDeduction === false){
                    $employee_time_sheet->am_late = 0;
                }

                if($isHoliday || $superFlexibleEmployee){ $employee_time_sheet->am_late = 0; }
                /*** am 2hrs deduction custom ***/
            }

            if ($am_in >= $_am_end) {
                $employee_time_sheet->am_ut = round(($_am_end - $_am_start) / 60, 2);
            }else {
                if (($am_out < $_am_end) && $am_out) {
                    $employee_time_sheet->am_ut = round(($_am_end - $am_out) / 60, 2);
                }
                if($am_in == 0 && $am_out == 0 && $_am_start && $_am_end){
                    $employee_time_sheet->am_ut = round(($_am_end - $_am_start) / 60, 2);
                }
                if($isHoliday || $superFlexibleEmployee){ $employee_time_sheet->am_ut = 0; }
            }

            if ($am_in >= $_am_end) {
                $employee_time_sheet->am_time_rendered = 0;
            } else {
                if ($am_in && $am_out) {
                    $has2hrsDeduction = false;
                    $tempAmInx = ($am_in < $_am_start) ? $_am_start : $am_in;
                    $tempAmOutx = ($am_out > $_am_end) ? $_am_end : $am_out;

                    /*** am 2hrs deduction custom ***/
                    if(($am_in > $am2hrsDeduction) && $am_in && $isHoliday === false){
                        $tempAmInx = strtotime(date("Y-m-d H:i", strtotime("+2 hours", strtotime($date . " " . $am_start))));
                        $has2hrsDeduction = true;
                    }
                    if($flexibleEmployee && $has2hrsDeduction === false){ $tempAmInx = $_am_start; }
                    /*** am 2hrs deduction custom ***/

                    /*** super flexible employee ***/
                    if($superFlexibleEmployee){ 
                        $tempAmInx = $_am_start;
                        $tempAmOutx = $_am_end;
                    }
                    /*** super flexible employee ***/

                    $am_time_rendered = $tempAmOutx - $tempAmInx;
                    $employee_time_sheet->am_time_rendered = round(($am_time_rendered) / 60, 2);
                }
            }

            /*** am half day deduction custom ***/
            if(($am_in > $amHalfDayAbsent) && $am_in && $isHoliday === false){
                $employee_time_sheet->am_late = 0;
                $employee_time_sheet->am_time_rendered = 0;
                $employee_time_sheet->am_ut = round(($_am_end - $_am_start) / 60, 2);
            }
            /*** am half day deduction custom ***/

            if($payrollType == "hourly"){
                if ($am_in >= $_am_end) {
                    $employee_time_sheet->am_time_rendered = 0;
                } else {
                    if ($am_in && $am_out) {
                        $tempAmInx = ($am_in < $_am_start) ? $_am_start : $am_in;
                        $tempAmOutx = ($am_out > $_am_end) ? $_am_end : $am_out;
                        $am_time_rendered = $tempAmOutx - $tempAmInx;
                        $employee_time_sheet->am_time_rendered = round(($am_time_rendered) / 60, 2);
                    }
                }
            }
            // END AM CALCULATION
            // START PM CALCULATION
            $hasHalfDayDeduction = false;
            if ($pm_in >= $_pm_end) {
                $employee_time_sheet->pm_late = 0;
            } else {
                if (($pm_in > $_pm_start) && $pm_in && $isHoliday === false) {
                    $employee_time_sheet->pm_late = round(($pm_in - $_pm_start) / 60, 2);
                }
                if($isHoliday){ $employee_time_sheet->pm_late = 0; }
            }

            if(($pm_in > $pmHalfDayAbsent) && $pm_in){ $hasHalfDayDeduction = true; }
            if($flexibleEmployee && $hasHalfDayDeduction === false){ $employee_time_sheet->pm_late = 0; }

            if ($pm_in >= $_pm_end) {
                $employee_time_sheet->pm_ut = round(($_pm_end - $_pm_start) / 60, 2);;
            } else {
                if (($pm_out < $_pm_end) && $pm_out) {
                    $employee_time_sheet->pm_ut = round(($_pm_end - $pm_out) / 60, 2);
                }
                if(($pm_in == 0 && $pm_out == 0) && ($_pm_start && $_pm_end)){
                    $employee_time_sheet->pm_ut = round(($_pm_end - $_pm_start) / 60, 2);
                }
                if($isHoliday){ $employee_time_sheet->pm_ut = 0; }
            }

            if ($pm_in >= $_pm_end) {
                $employee_time_sheet->pm_time_rendered = 0;
            } else {
                if ($pm_in && $pm_out) {
                    $tempPmInx = ($pm_in < $_pm_start) ? $_pm_start : $pm_in;
                    $tempPmOutx = ($pm_out > $_pm_end) ? $_pm_end : $pm_out;

                    if($flexibleEmployee && $hasHalfDayDeduction === false){ $tempPmInx = $_pm_start; }

                    /*** super flexible employee ***/
                    if($superFlexibleEmployee){
                        $tempPmInx = $_pm_start;
                        $tempPmOutx = $_pm_end;
                    }
                    /*** super flexible employee ***/

                    $pm_time_rendered = $tempPmOutx - $tempPmInx;
                    $employee_time_sheet->pm_time_rendered = round(($pm_time_rendered) / 60, 2);
                }
            }

            /*** pm half day deduction custom ***/
            if(($pm_in > $pmHalfDayAbsent) && $pm_in && $isHoliday === false){
                $employee_time_sheet->pm_late = 0;
                $employee_time_sheet->pm_time_rendered = 0;
                $employee_time_sheet->pm_ut = round(($_pm_end - $_pm_start) / 60, 2);
            }
            /*** pm half day deduction custom ***/

            if($payrollType == "hourly"){
                if ($pm_in >= $_pm_end) {
                    $employee_time_sheet->pm_time_rendered = 0;
                } else {
                    if ($pm_in && $pm_out) {
                        $tempPmInx = ($pm_in < $_pm_start) ? $_pm_start : $pm_in;
                        $tempPmOutx = ($pm_out > $_pm_end) ? $_pm_end : $pm_out;

                        $pm_time_rendered = $tempPmOutx - $tempPmInx;
                        $employee_time_sheet->pm_time_rendered = round(($pm_time_rendered) / 60, 2);
                    }
                }
            }

            // END PM CALCULATION

            if($payrollType == "hourly"){
                $employee_time_sheet->am_late = 0;
                $employee_time_sheet->pm_late = 0;
                $employee_time_sheet->am_ut = 0;
                $employee_time_sheet->pm_ut = 0;
            }

            $employee_time_sheet->total_late = $employee_time_sheet->am_late + $employee_time_sheet->pm_late;
            $employee_time_sheet->total_ut = $employee_time_sheet->am_ut + $employee_time_sheet->pm_ut;
            $employee_time_sheet->total_time_rendered = $employee_time_sheet->am_time_rendered + $employee_time_sheet->pm_time_rendered;
        }

        if ($no_shift_schedule === true) {
            $employee_time_sheet->scrub_status = 3;
            $employee_time_sheet->has_shift = 0;
            $employee_time_sheet->comments = "[System Generated]: No shift schedule detected.";
        } else {
            $employee_time_sheet->shift_am_start = $am_start;
            $employee_time_sheet->shift_am_end = $am_end;
            $employee_time_sheet->shift_pm_start = $pm_start;
            $employee_time_sheet->shift_pm_end = $pm_end;
        }

        if($isHoliday && $hasOT){
            $employee_time_sheet->am_late = 0;
            $employee_time_sheet->pm_late = 0;
            $employee_time_sheet->total_late = 0;
            
            $employee_time_sheet->am_ut = 0;
            $employee_time_sheet->pm_ut = 0;
            $employee_time_sheet->total_ut = 0;

            $employee_time_sheet->am_time_rendered = 0;
            $employee_time_sheet->pm_time_rendered = 0;
            $employee_time_sheet->total_time_rendered = 0;
        }

        // CHECK IF TIMESHEET EXSIT THEN UPDATE ELSE INSERT NEW
        if (intval(sizeof((array) $timesheet_exist)) >= 1) {
            if (!in_array($timesheet_exist->id, $updatedTimesheets)) {
                array_push($updatedTimesheets, $timesheet_exist->id);
            }
            $employee_time_sheet->last_updated_by = $logged_in_user_emp_id;
            $employee_time_sheet->last_updated_at = $this->today->format('Y-m-d H:i:s');
            $employee_time_sheet->updated_by_import = 1;

            $this->db->where("id", $timesheet_exist->id);
            $this->db->where("verified", 0);
            $this->db->update($this->tbl_timesheet, $employee_time_sheet);
            $timesheet_id = $timesheet_exist->id;
        } else {
            $this->db->insert($this->tbl_timesheet, $employee_time_sheet);
            $timesheet_id = $this->db->insert_id();
        }
        
        // END OF CHECK IF TIMESHEET EXSIT THEN UPDATE ELSE INSERT NEW

        $arrData["updated_timesheets"] = $updatedTimesheets;
        $arrData["timesheet_id"] = $timesheet_id;

        return $arrData;
    }


    public function updateTimesheetShiftComputation__jessery_update($employee_time_sheet, $allow_late_adjustment=false){
            $this->db->select("*");
            $this->db->from($this->tbl_employees." te");
            $this->db->join($this->tbl_personnel." tp"," tp.biometricno = te.biometricno OR tp.biometric_id = te.biometricno ");
            $this->db->where("te.id",$employee_time_sheet->emp_id);
            $query = $this->db->get();
            if($query->result()){
                $row = $query->row();
            }


        $flexibleEmployee = intval($employee_time_sheet->is_flexi) == 1;
        $hasOT = $employee_time_sheet->has_overtime;
        $isHoliday = (isset($employee_time_sheet->is_holiday) && intval($employee_time_sheet->is_holiday) == 1)? true: false;



        $am_30min       = strtotime("+30 minutes", strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_am_start));
        $am_1hr         = strtotime("+1 hours", strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_am_start));
        $pm_30min       = strtotime("+30 minutes", strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_pm_start));


        $am_start       = strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_am_start);
        $am_end         = strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_am_end);

        $pm_start       = strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_pm_start);
        $pm_end         = strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_pm_end);

        $am_in          = strtotime($employee_time_sheet->date . " " . $employee_time_sheet->am_in);
        $am_out         = strtotime($employee_time_sheet->date . " " . $employee_time_sheet->am_out);

        $pm_in          = strtotime($employee_time_sheet->date . " " . $employee_time_sheet->pm_in);
        $pm_out         = strtotime($employee_time_sheet->date . " " . $employee_time_sheet->pm_out);

        $am_ex_hours    = round((abs($am_start - $am_end)/(60*60))*60 , 2);
        $pm_ex_hours    = round((abs($pm_start - $pm_end)/(60*60))*60 , 2);

        if ($isHoliday !== false && $allow_late_adjustment !== false) {   }

        $am_late        = 0;
        $pm_late        = 0;
        $am_ut          = 0;
        $pm_ut          = 0;

        $am_t_rendered  = $am_ex_hours;
        $pm_t_rendered  = $pm_ex_hours;


        //============ FLEXI (1)============//
        if ($query->row()) {
            $isFlex = $row->is_flexi;
        }else{
            $isFlex = $employee_time_sheet->is_flexi;
        }



         $total_AM_late = 0;
         $total_PM_late = 0;
         $TOTAL_AMPM_LATE = 0;
         $tag_absent_AM = 0;
         $tag_absent_PM = 0;
         if ($employee_time_sheet->am_in !== null || $employee_time_sheet->pm_in !== null) {


                if ($employee_time_sheet->am_in !== null) {
                    if($am_out < $am_end){ $am_ut = round((abs($am_end - $am_out)/(60*60))*60 , 2); }
                }else{
                    $am_ut = $am_ex_hours;
                }
                if ($employee_time_sheet->pm_in !== null) {
                    if($pm_out < $pm_end){ $pm_ut = round((abs($pm_end - $pm_out)/(60*60))*60 , 2); }
                }else{
                    $pm_ut = $pm_ex_hours;
                }
                // if($am_out < $am_end){ $am_ut = round((abs($am_end - $am_out)/(60*60))*60 , 2); }
                // if($pm_out < $pm_end){ $pm_ut = round((abs($pm_end - $pm_out)/(60*60))*60 , 2); }

                if($am_in > $am_30min){
                    $total_AM_late = 2 * 60;
                    if($allow_late_adjustment) { $total_AM_late = round((abs($am_in - $am_start)/(60*60))*60 , 2); /*** normal Late DEDUCTION ***/  }
                    if($isFlex == 1)
                    {
                        $total_AM_late = 0;
                        if ($am_in > $am_30min) {
                            $total_AM_late = 2 * 60;
                            if($allow_late_adjustment) { $total_AM_late = round((abs($am_in - $am_start)/(60*60))*60 , 2); /*** normal Late DEDUCTION ***/  }
                        }
                    }


                    if ($am_in>$am_1hr) {
                        //$total_AM_late = $am_ex_hours;
                        $total_AM_late = 0;
                        $tag_absent_AM = $am_ex_hours;
                        $am_ut = 0;
                        if($allow_late_adjustment) {
                            $total_AM_late = round((abs($am_in - $am_start)/(60*60))*60 , 2); /*** normal Late DEDUCTION ***/
                            $tag_absent_AM = 0;
                        }
                    }
                }else{
                    if ($isFlex == 1) {  $total_AM_late = 0; }
                    else{ $total_AM_late = round((abs($am_in - $am_start)/(60*60))*60 , 2);}
                    if ($am_in < $am_start) { $total_AM_late = 0; }
                }

                if ($pm_in > $pm_30min) {
                    $total_PM_late = 0;
                    $tag_absent_PM = $pm_ex_hours;
                    $pm_ut = 0;
                    if($isFlex == 1){ $total_PM_late = 0; }
                    if($allow_late_adjustment) {
                        $total_PM_late = round((abs($pm_in - $pm_start)/(60*60))*60 , 2); /*** normal Late DEDUCTION ***/
                        $tag_absent_PM = 0;
                    }
                }else{
                    if ($isFlex == 1) {  $total_PM_late = 0; }
                    else{ $total_PM_late = round((abs($pm_in - $pm_start)/(60*60))*60 , 2);}
                    if ($pm_in < $pm_start) { $total_PM_late = 0; }
                }

                // $total_AM_late = 100;
                // $total_PM_late = 200;
         }
         if ($employee_time_sheet->am_in === null || $employee_time_sheet->am_out === null)  {
                $am_ut = 0; $am_ex_hours=0;
            }
         if ($employee_time_sheet->pm_in === null || $employee_time_sheet->pm_out === null)  {
                $pm_ut = 0; $pm_ex_hours=0;
            }

         $TOTAL_AMPM_LATE = round($total_AM_late,2) + round($total_PM_late,2);
         $TOTAL_TAG_ABSENT = round($tag_absent_AM,2) + round($tag_absent_PM,2);
         if ($isHoliday !== false ) { $total_AM_late = 0; $total_PM_late = 0;  }


         $TOTAL_DAY_RENDERED = ($am_ex_hours + $pm_ex_hours) - $TOTAL_TAG_ABSENT ;


        $employee_time_sheet->total_late = $TOTAL_AMPM_LATE;
        //$employee_time_sheet->total_ut =  round($am_ut,2) + round($pm_ut,2) + $TOTAL_AMPM_LATE;
        $employee_time_sheet->total_ut =  round($am_ut,2) + round($pm_ut,2) + $TOTAL_TAG_ABSENT;
        $employee_time_sheet->total_time_rendered =   $TOTAL_DAY_RENDERED - ( $employee_time_sheet->total_ut + $employee_time_sheet->total_late );

        if ($row->is_perhour == 1) {
                        $amrender = $am_ex_hours;
                        $pmrender = $pm_ex_hours;

                        if ($employee_time_sheet->am_in!==null && $employee_time_sheet->am_out!==null) {
                            $amrender = round( (abs($am_out - $am_in)/(60*60))*60,2 ) ;
                            if ($am_out > $am_end) {
                                $amrender = round( (abs($am_end - $am_in)/(60*60))*60,2 ) ;
                            }
                        }

                        if ($employee_time_sheet->pm_in!==null && $employee_time_sheet->pm_out!==null) {
                            if ($pm_in > $pm_start) {
                                $pmrender = round( (abs($pm_out - $pm_in)/(60*60))*60,2 ) ;
                            }else{
                                $pmrender = round( (abs($pm_out - $pm_start)/(60*60))*60,2 ) ;
                            }
                        }
                        $TOTAL_DAY_RENDERED = $amrender + $pmrender;

            $employee_time_sheet->total_late = 0;
            $employee_time_sheet->total_ut =  0;
            $employee_time_sheet->total_time_rendered =   $TOTAL_DAY_RENDERED;
        }
        return $employee_time_sheet;
    }

    public function updateTimesheetShiftComputation($employee_time_sheet, $allow_late_adjustment=false, $night_diff_cfg=null){
        /*** reset late, undertime and time rendered also nightdiff rendered ***/
        $employee_time_sheet->am_ut = 0;
        $employee_time_sheet->pm_ut = 0;
        $employee_time_sheet->total_ut = 0;
        
        $employee_time_sheet->am_late = 0;
        $employee_time_sheet->pm_late = 0;
        $employee_time_sheet->total_late = 0;
        $employee_time_sheet->am_time_rendered = 0;
        $employee_time_sheet->pm_time_rendered = 0;
        $employee_time_sheet->total_time_rendered = 0;

        $employee_time_sheet->am_ndiff_rendered = 0;
        $employee_time_sheet->pm_ndiff_rendered = 0;
        $employee_time_sheet->total_ndiff_rendered = 0;
        /*** reset late, undertime and time rendered also nightdiff rendered ***/

        $isFlexibleEmployee = false;
        $this->db->select("per.is_flexi");
        $this->db->from($this->tbl_employees." emp");
        $this->db->join($this->tbl_personnel." per", "per.biometric_id = emp.biometricno OR per.biometricno = emp.biometricno");
        $this->db->where("emp.id", $employee_time_sheet->emp_id);
        $this->db->where("per.is_flexi", 1);
        $this->db->order_by("emp.id", "DESC");
        $this->db->limit(1);
        $qTempEmployee = $this->db->get();
        if($qTempEmployee->num_rows() == 1){
            $isFlexibleEmployee = true;
        }
        $this->db->reset_query();

        $superFlexibleEmployee = false;
        $isOneInOut = false;
        $this->db->select("per.is_flexi");
        $this->db->from($this->tbl_employees." emp");
        $this->db->join($this->tbl_personnel." per", "per.biometric_id = emp.biometricno OR per.biometricno = emp.biometricno");
        $this->db->where("emp.id", $employee_time_sheet->emp_id);
        $this->db->group_start();
        $this->db->where("per.is_flexi", 3);
        $this->db->or_where("per.is_flexi", 4);
        $this->db->group_end();
        $this->db->order_by("emp.id", "DESC");
        $this->db->limit(1);
        $qTempEmployee = $this->db->get();
        if($qTempEmployee->num_rows() == 1){
            $superFlexibleEmployee = true;
            $isOneInOut = intval($qTempEmployee->row()->is_flexi) === 4;
        }
        $this->db->reset_query();

        /** night diff switch here **/
        $allowNdiff = $this->db->get_where($this->tbl_ps_employee_regular_ndiff, array("employee_id"=>$employee_time_sheet->emp_id, "allow_ndiff"=>1));
        $allowRegularNightDiff = $allowNdiff->num_rows() === 1;
        $this->db->reset_query();
        /** night diff switch here **/

        /*** altered section allowedPaidHoliday ***/
        $tempPayrollType = null;
        $allowedPaidHoliday = false;
        
        $tempEmployeeData = $this->db->get_where($this->tbl_employees, array("id" => $employee_time_sheet->emp_id));
        if($tempEmployeeData->num_rows() == 1){
            $tempPayrollType = $tempEmployeeData->row()->payroll_type;
        }
        $this->db->reset_query();

        $tempResponse = (object) $this->getCurrentDateIsHoliday($employee_time_sheet->date);
        $isSpecialNonWorkingHoliday = isset($tempResponse->classification) && strtolower($tempResponse->classification) === 'special non-working holiday';
        $isRegularHoliday = isset($tempResponse->classification) && strtolower($tempResponse->classification) === 'regular holiday';

        if ($isSpecialNonWorkingHoliday && $tempPayrollType === 'monthly') { $allowedPaidHoliday = true; }
        if ($isRegularHoliday && in_array($tempPayrollType, ['monthly', 'daily', 'project based'])) { $allowedPaidHoliday = true; }
        /*** altered section allowedPaidHoliday ***/

        $isHourlySlashPartimer = false;

        if(isset($employee_time_sheet->is_tagged_hourly) && $employee_time_sheet->is_tagged_hourly === true){
            $isHourlySlashPartimer = $employee_time_sheet->is_tagged_hourly;
            unset($employee_time_sheet->is_tagged_hourly);
        }


        $alteredShiftSchedule = $this->getCustomizedShiftScheduleByDate($employee_time_sheet->date, $employee_time_sheet->emp_id);
        $_altered_shift = array();
        if(isset($alteredShiftSchedule->schedule)){
            foreach ($alteredShiftSchedule->schedule as $key => $value) {
                $tempKey = "shift_{$key}";
                if($value && $alteredShiftSchedule->has_shift == 1){
                    $_altered_shift[$tempKey] = $value;
                    /*** $employee_time_sheet->$tempKey = $value; ***/
                }
                if($alteredShiftSchedule->has_shift == 0){
                    $_altered_shift[$tempKey] = $value;
                    /*** $employee_time_sheet->$tempKey = $value; ***/
                }
            }
        }

        if(is_array($_altered_shift) && !empty($_altered_shift)){
            foreach ($_altered_shift as $kkx => $vvx) { $employee_time_sheet->$kkx = $vvx; }
        }

        if(isset($employee_time_sheet->shift_am_start, $employee_time_sheet->shift_am_end) && ($employee_time_sheet->shift_am_start === "00:00:00" && $employee_time_sheet->shift_am_end === "00:00:00")){
            $employee_time_sheet->shift_am_start = null;
            $employee_time_sheet->shift_am_end = null;
        }
        
        if(isset($employee_time_sheet->shift_pm_start , $employee_time_sheet->shift_pm_end) && ($employee_time_sheet->shift_pm_start === "00:00:00" && $employee_time_sheet->shift_pm_end === "00:00:00")){
            $employee_time_sheet->shift_pm_start = null;
            $employee_time_sheet->shift_pm_end = null;
        }

        $flexibleEmployee = intval($employee_time_sheet->is_flexi) == 1 && $isFlexibleEmployee;
        /*** $hasOT = intval($employee_time_sheet->has_overtime) == 1; ***/
        $isHoliday = (isset($employee_time_sheet->is_holiday) && intval($employee_time_sheet->is_holiday) == 1)? true: false;

        /*** attendance record ***/
        $am_in = ($employee_time_sheet->am_in !== null)?
            strtotime(date('Y-m-d H:i', strtotime($employee_time_sheet->date . " " . $employee_time_sheet->am_in))): null;

        $am_out = ($employee_time_sheet->am_out !== null)?
            strtotime(date('Y-m-d H:i', strtotime($employee_time_sheet->date . " " . $employee_time_sheet->am_out))): null;

        $pm_in = ($employee_time_sheet->pm_in !== null)?
            strtotime(date('Y-m-d H:i', strtotime($employee_time_sheet->date . " " . $employee_time_sheet->pm_in))): null;

        $pm_out = ($employee_time_sheet->pm_out !== null)?
            strtotime(date('Y-m-d H:i', strtotime($employee_time_sheet->date . " " . $employee_time_sheet->pm_out))): null;
        /*** attendance record ***/
        /*** shift schedule ***/
        $_am_start = ($employee_time_sheet->shift_am_start !== null)?
        strtotime(date('Y-m-d H:i', strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_am_start))): null;
        
        $_am_end = ($employee_time_sheet->shift_am_end !== null)?
        strtotime(date('Y-m-d H:i', strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_am_end))): null;
        
        $_pm_start = ($employee_time_sheet->shift_pm_start !== null)?
        strtotime(date('Y-m-d H:i', strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_pm_start))): null;
        
        $_pm_end = ($employee_time_sheet->shift_pm_end !== null)?
        strtotime(date('Y-m-d H:i', strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_pm_end))): null;
        /*** shift schedule ***/
        
        $temp_am_in = $am_in;
        $temp_am_out = $am_out;
        $temp_pm_in = $pm_in;
        $temp_pm_out = $pm_out;

        $hasNextDay = false;
        if(($temp_am_in && $temp_am_out) && ($temp_am_out < $temp_am_in)){
            $date_00 = date("Y-m-d", strtotime("+1 day", strtotime($employee_time_sheet->date)));
            $am_out = strtotime(date('Y-m-d H:i', strtotime($date_00 . " " . $employee_time_sheet->am_out)));
            $hasNextDay = true;
        }

        if(($temp_am_out && $temp_pm_in) && ($temp_pm_in < $temp_am_out) || $hasNextDay){
            $date_01 = date("Y-m-d", strtotime("+1 day", strtotime($employee_time_sheet->date)));
            $pm_in = strtotime(date('Y-m-d H:i', strtotime($date_01 . " " . $employee_time_sheet->pm_in)));
            $hasNextDay = true;
        }

        if(($temp_pm_in && $temp_pm_out) && ($temp_pm_out < $temp_pm_in) || $hasNextDay){
            $date_02 = date("Y-m-d", strtotime("+1 day", strtotime($employee_time_sheet->date)));
            $pm_out = strtotime(date('Y-m-d H:i', strtotime($date_02 . " " . $employee_time_sheet->pm_out)));
            $hasNextDay = true;
        }

        $temp_am_start = $_am_start;
        $temp_am_end = $_am_end;
        $temp_pm_start = $_pm_start;
        $temp_pm_end = $_pm_end;

        $hasNextDayShift = false;
        if(($temp_am_start && $temp_am_end) && ($temp_am_end < $temp_am_start)){
            $_date_00 = date("Y-m-d", strtotime("+1 day", strtotime($employee_time_sheet->date)));
            $_am_end = strtotime(date('Y-m-d H:i', strtotime($_date_00 . " " . $employee_time_sheet->shift_am_end)));
            $hasNextDayShift = true;
        }

        if(($temp_am_end && $temp_pm_start) && ($temp_pm_start < $temp_am_end) || $hasNextDayShift){
            $_date_01 = date("Y-m-d", strtotime("+1 day", strtotime($employee_time_sheet->date)));
            $_pm_start = strtotime(date('Y-m-d H:i', strtotime($_date_01 . " " . $employee_time_sheet->shift_pm_start)));
            $hasNextDayShift = true;
        }

        if(($temp_pm_start && $temp_pm_end) && ($temp_pm_end < $temp_pm_start) || $hasNextDayShift){
            $_date_02 = date("Y-m-d", strtotime("+1 day", strtotime($employee_time_sheet->date)));
            $_pm_end = strtotime(date('Y-m-d H:i', strtotime($_date_02 . " " . $employee_time_sheet->shift_pm_end)));
            $hasNextDayShift = true;
        }

        $am2hrsDeduction = ($employee_time_sheet->shift_am_start !== null)?
            strtotime(date("Y-m-d H:i", strtotime("+30 minutes", $_am_start))): null;

        $amHalfDayAbsent = ($employee_time_sheet->shift_am_start !== null)?
            strtotime(date("Y-m-d H:i", strtotime("+1 hour 1 minute", $_am_start))): null;

        $pmHalfDayAbsent = ($employee_time_sheet->shift_pm_start !== null)?
            strtotime(date("Y-m-d H:i", strtotime("+30 minutes", $_pm_start))): null;

        // START AM CALCULATION
        if (($am_in && $_am_end) && ($am_in >= $_am_end)) {
            $employee_time_sheet->am_late = 0;
        } else {
            if (($am_in && $_am_start) && ($am_in > $_am_start)) {
                $employee_time_sheet->am_late = round(($am_in - $_am_start) / 60, 2);
            }
            /*** am 2hrs deduction custom ***/
            $has2hrsDeduction = false;
            if(($am_in && $am2hrsDeduction) && ($am_in > $am2hrsDeduction) && $isHoliday === false && $allow_late_adjustment === false && $isHourlySlashPartimer === false){
                $amPlus2hrs = strtotime(date("Y-m-d H:i", strtotime("+2 hours", strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_am_start))));
                $employee_time_sheet->am_late = round(($amPlus2hrs - $_am_start) / 60, 2);
                $has2hrsDeduction = true;
            }

            if($flexibleEmployee && $has2hrsDeduction === false && $allow_late_adjustment === false){
                $employee_time_sheet->am_late = 0;
            }

            if($superFlexibleEmployee){ $employee_time_sheet->am_late = 0; }
            /*** am 2hrs deduction custom ***/
        }

        if (($am_in && $_am_start && $_am_end) && ($am_in >= $_am_end)) {
            $employee_time_sheet->am_ut = round(($_am_end - $_am_start) / 60, 2);
        }else {
            if (($am_out && $_am_end) && ($am_out < $_am_end)) {
                $employee_time_sheet->am_ut = round(($_am_end - $am_out) / 60, 2);
            }
            if(($am_in == null || $am_out == null) && ($_am_start && $_am_end)){
                $employee_time_sheet->am_ut = round(($_am_end - $_am_start) / 60, 2);
            }
            if($superFlexibleEmployee){ $employee_time_sheet->am_ut = 0; }
        }

        if (($am_in && $_am_end) && ($am_in >= $_am_end)) {
            $employee_time_sheet->am_time_rendered = 0;
            $employee_time_sheet->am_ndiff_rendered = 0;

        } else {
            if (($am_in && $am_out) && ($_am_start && $_am_end)) {
                $has2hrsDeduction = false;
                $tempAmInx = ($am_in < $_am_start) ? $_am_start : $am_in;
                $tempAmOutx = ($am_out > $_am_end) ? $_am_end : $am_out;

                /*** am 2hrs deduction custom ***/
                if(($am_in && $am2hrsDeduction) && ($am_in > $am2hrsDeduction) && $isHoliday === false && $allow_late_adjustment === false && $isHourlySlashPartimer === false){
                    $tempAmInx = strtotime(date("Y-m-d H:i", strtotime("+2 hours", strtotime($employee_time_sheet->date . " " . $employee_time_sheet->shift_am_start))));
                    $has2hrsDeduction = true;
                }
                if($flexibleEmployee && $has2hrsDeduction === false && $allow_late_adjustment === false){ $tempAmInx = $_am_start; }
                /*** am 2hrs deduction custom ***/

                if($superFlexibleEmployee){
                    $tempAmInx = $_am_start;
                    $tempAmOutx = $_am_end;
                }

                $am_time_rendered = $tempAmOutx - $tempAmInx;
                $employee_time_sheet->am_time_rendered = round(($am_time_rendered) / 60, 2);

                /*** regular ndiff am rendered computation ***/
                if($allowRegularNightDiff){
                    $currentAmDate = date("Y-m-d", strtotime($employee_time_sheet->date));
                    $amNdiffStart = date("Y-m-d H:i", strtotime($currentAmDate." 22:00:00"));
                    $amNdiffEnd = date("Y-m-d H:i", strtotime("+1 day", strtotime($currentAmDate." 05:00:00")));
    
                    if(isset($night_diff_cfg->start_time) && $night_diff_cfg->start_time){
                        $amNdiffStart = date("Y-m-d H:i", strtotime($currentAmDate." ".$night_diff_cfg->start_time));
                    }
                    if(isset($night_diff_cfg->end_time) && $night_diff_cfg->end_time){
                        $amNdiffEnd = date("Y-m-d H:i", strtotime("+1 day", strtotime($currentAmDate." ".$night_diff_cfg->end_time)));
                    }
                    
                    $_amNdiffStart = strtotime($amNdiffStart);
                    $_amNdiffEnd = strtotime($amNdiffStart) <= $_am_end && strtotime($amNdiffEnd) >= $_am_end ? $_am_end : strtotime($amNdiffEnd);
    
                    $tempAmNdiffStart = $tempAmInx >= $_amNdiffStart && $tempAmInx <= $_amNdiffEnd ? $tempAmInx : $_amNdiffStart;
                    $tempAmNdiffEnd = $tempAmOutx >= $_amNdiffStart && $tempAmOutx <= $_amNdiffEnd ? $tempAmOutx : $_amNdiffEnd;
    
                    $am_ndiff_rendered = $tempAmNdiffEnd - $tempAmNdiffStart;
                    $employee_time_sheet->am_ndiff_rendered = round(($am_ndiff_rendered) / 60, 2);
                }
                /*** regular ndiff am rendered computation ***/
            }

            if(($am_in == null || $am_out == null) && ($_am_start && $_am_end)){
                $employee_time_sheet->am_time_rendered = 0;
                $employee_time_sheet->am_ndiff_rendered = 0;
            }
            /*** if($isHoliday){ $employee_time_sheet->am_time_rendered = 0; } ***/
        }

        /*** am half day deduction custom ***/
        if(($am_in && $amHalfDayAbsent) && ($am_in >= $amHalfDayAbsent) && $isHoliday === false && $allow_late_adjustment === false && $isHourlySlashPartimer === false){
            $employee_time_sheet->am_late = 0;
            $employee_time_sheet->am_ut = round(($_am_end - $_am_start) / 60, 2);
            $employee_time_sheet->am_time_rendered = 0;
            $employee_time_sheet->am_ndiff_rendered = 0;
        }
        /*** am half day deduction custom ***/
        // END AM CALCULATION

        // START PM CALCULATION
        $hasHalfDayDeduction = false;
        if (($pm_in && $_pm_end) && ($pm_in >= $_pm_end)) {
            $employee_time_sheet->pm_late = 0;
        } else {
            if (($pm_in && $_pm_start) && ($pm_in > $_pm_start) && $isHoliday === false && $allow_late_adjustment === false && $isHourlySlashPartimer === false) {
                $employee_time_sheet->pm_late = round(($pm_in - $_pm_start) / 60, 2);
            }
            if($superFlexibleEmployee){ $employee_time_sheet->pm_late = 0; }
        }

        if(($pm_in && $pmHalfDayAbsent) && ($pm_in > $pmHalfDayAbsent) && $allow_late_adjustment === false && $isHourlySlashPartimer === false){ $hasHalfDayDeduction = true; }
        if($flexibleEmployee && $hasHalfDayDeduction === false && $allow_late_adjustment === false){ $employee_time_sheet->pm_late = 0; }

        if (($pm_in && $_pm_start && $_pm_end) && $pm_in >= $_pm_end) {
            $employee_time_sheet->pm_ut = round(($_pm_end - $_pm_start) / 60, 2);
        } else {
            if (($pm_out && $_pm_end) && ($pm_out < $_pm_end)) {
                $employee_time_sheet->pm_ut = round(($_pm_end - $pm_out) / 60, 2);
                
            }
            if(($pm_in == null || $pm_out == null) && ($_pm_start && $_pm_end)){
                $employee_time_sheet->pm_ut = round(($_pm_end - $_pm_start) / 60, 2);
            }
            if($superFlexibleEmployee){ $employee_time_sheet->pm_ut = 0; }
        }
        
        if ($pm_in >= $_pm_end) {
            $employee_time_sheet->pm_time_rendered = 0;
            $employee_time_sheet->pm_ndiff_rendered = 0;
        } else {
            if (($pm_in && $pm_out) && ($pm_out > $pm_in) && ($_pm_start && $_pm_end)) {
                $tempPmInx = ($pm_in < $_pm_start) ? $_pm_start : $pm_in;
                $tempPmOutx = ($pm_out > $_pm_end) ? $_pm_end : $pm_out;

                if($flexibleEmployee && $hasHalfDayDeduction === false && $allow_late_adjustment === false){ $tempPmInx = $_pm_start; }

                if($superFlexibleEmployee){
                    $tempPmInx = $_pm_start;
                    $tempPmOutx = $_pm_end;
                }

                $pm_time_rendered = $tempPmOutx - $tempPmInx;
                $employee_time_sheet->pm_time_rendered = round(($pm_time_rendered) / 60, 2);

                /*** regular ndiff pm rendered computation ***/
                if($allowRegularNightDiff){
                    $currentPmDate = date("Y-m-d", strtotime($employee_time_sheet->date));
                    $pmNdiffStart = date("Y-m-d H:i", strtotime($currentPmDate." 22:00:00"));
                    $pmNdiffEnd = date("Y-m-d H:i", strtotime("+1 day", strtotime($currentPmDate." 05:00:00")));
    
                    if(isset($night_diff_cfg->start_time) && $night_diff_cfg->start_time){
                        $pmNdiffStart = date("Y-m-d H:i", strtotime($currentPmDate." ".$night_diff_cfg->start_time));
                    }
                    if(isset($night_diff_cfg->end_time) && $night_diff_cfg->end_time){
                        $pmNdiffEnd = date("Y-m-d H:i", strtotime("+1 day", strtotime($currentPmDate." ".$night_diff_cfg->end_time)));
                    }
    
                    $_pmNdiffStart = $_pm_start >= strtotime($pmNdiffStart) && $_pm_start <= strtotime($pmNdiffEnd) ? $_pm_start : strtotime($pmNdiffStart);
                    $_pmNdiffEnd = $_pm_end >= strtotime($pmNdiffStart) && $_pm_end <= strtotime($pmNdiffEnd) ? $_pm_end : strtotime($pmNdiffEnd);
    
                    $tempPmNdiffStart = $tempPmInx >= $_pmNdiffStart && $tempPmInx <= $_pmNdiffEnd ? $tempPmInx : $_pmNdiffStart;
                    $tempPmNdiffEnd = $tempPmOutx >= $_pmNdiffStart && $tempPmOutx <= $_pmNdiffEnd ? $tempPmOutx : $_pmNdiffEnd;
                    $pm_ndiff_rendered = $tempPmNdiffEnd - $tempPmNdiffStart;
                    $employee_time_sheet->pm_ndiff_rendered = round(($pm_ndiff_rendered) / 60, 2);
                }
                /*** regular ndiff pm rendered computation ***/
            }
            if(($pm_in == null || $pm_out == null) && ($_pm_start && $_pm_end)){
                $employee_time_sheet->pm_time_rendered = 0;
            }
            /*** if($isHoliday){ $employee_time_sheet->pm_time_rendered = 0; } ***/
        }

        /*** pm half day deduction custom ***/
        if(($pm_in && $pmHalfDayAbsent) && ($pm_in > $pmHalfDayAbsent) && $isHoliday === false && $allow_late_adjustment === false && $isHourlySlashPartimer === false){
            $employee_time_sheet->pm_late = 0;
            $employee_time_sheet->pm_time_rendered = 0;
            $employee_time_sheet->pm_ut = round(($_pm_end - $_pm_start) / 60, 2);
        }
        /*** pm half day deduction custom ***/

        // END PM CALCULATION

        if(isset($employee_time_sheet->has_overtime) && intval($employee_time_sheet->has_overtime) == 0){
            $employee_time_sheet->overtime_in = null;
            $employee_time_sheet->overtime_out = null;
        }

        /*** altered section allowedPaidHoliday ***/
        /***if($isHoliday && $hasOT){ ***/
        if($allowedPaidHoliday && $isOneInOut){
        /*** altered section allowedPaidHoliday ***/
            $employee_time_sheet->paid_holiday = 1;
            /*** $employee_time_sheet->am_late = 0;
            $employee_time_sheet->pm_late = 0;

            $employee_time_sheet->am_ut = 0;
            $employee_time_sheet->pm_ut = 0;

            $employee_time_sheet->am_time_rendered = 0;
            $employee_time_sheet->pm_time_rendered = 0; ***/
        }else{
            $employee_time_sheet->paid_holiday = 0;
        }

        $employee_time_sheet->total_late = $employee_time_sheet->am_late + $employee_time_sheet->pm_late;
        $employee_time_sheet->total_ut = $employee_time_sheet->am_ut + $employee_time_sheet->pm_ut;
        $employee_time_sheet->total_time_rendered = $employee_time_sheet->am_time_rendered + $employee_time_sheet->pm_time_rendered;
        $employee_time_sheet->total_ndiff_rendered = $employee_time_sheet->am_ndiff_rendered + $employee_time_sheet->pm_ndiff_rendered;
        
        return $employee_time_sheet;
    }

    public function generateTimesheetOvertime($timesheet_exist, $generated_manually, $overtime, $date, $am_end, $pm_end, $am_shift_only, $attendance, $night_diff_cfg){
        $resultset = array();
        $hasOvertimeRequest = false;
        $overtime_start = null;
        $overtime_end = null;

        $total_accredited_ot_hrs = 0;
        $total_accredited_ot_nightdiff_hrs = 0;
        $hasShiftSchedule = $timesheet_exist->has_shift == 1;

        $nshiftParams = $this->nightshiftParams($date);
        $_nightShiftStart = $nshiftParams["start"];
        $_nightShiftEnd = $nshiftParams["end"];

        /** start shift schedule ***/
        $shift_am_start = $timesheet_exist->shift_am_start ? date("H:i:s", strtotime($timesheet_exist->shift_am_start)): null;
        $shift_am_end = $timesheet_exist->shift_am_end ? date("H:i:s", strtotime($timesheet_exist->shift_am_end)): null;
        $shift_pm_start = $timesheet_exist->shift_pm_start ? date("H:i:s", strtotime($timesheet_exist->shift_pm_start)): null;
        $shift_pm_end = $timesheet_exist->shift_pm_end ? date("H:i:s", strtotime($timesheet_exist->shift_pm_end)): null;
        /** end shift schedule ***/

        $am_shift_only = $am_shift_only || (($shift_am_start !== null && $shift_am_end !== null) &&
        ($shift_pm_start === null || $shift_am_start == "00:00:00" && $shift_pm_end === null || $shift_am_end == "00:00:00"));

        $shift_basis = $am_shift_only ? date("Y-m-d H:i", strtotime($date . " " . $am_end)) : date("Y-m-d H:i", strtotime($date . " " . $pm_end));
        $ot_attendances = array_values(
            array_filter($attendance, function ($_attendance) use ($shift_basis) {
                return strtotime($_attendance) > strtotime($shift_basis);
            })
        );
        
        $temp_ot_attendances = $ot_attendances;
        if(count($temp_ot_attendances) > 1 && $hasShiftSchedule){
            $_ot_attendances = array();
            foreach ($temp_ot_attendances as $value) {
                $tempDate = date("Y-m-d H:i", strtotime($timesheet_exist->date." ".$timesheet_exist->pm_out));
                if(strtotime($value) !== strtotime($tempDate)){
                    $_ot_attendances[] = $value;
                }
            }
            $ot_attendances = $_ot_attendances;
        }
        
        $temp_ot_attendances = $ot_attendances;

        if((isset($timesheet_exist->is_holiday) && intval($timesheet_exist->is_holiday) == 1)
            && count($temp_ot_attendances) == 0  && count($attendance) > 1){
                $ot_attendances = $attendance;
        }

        if($hasShiftSchedule === false && count($ot_attendances) > 1 && end($ot_attendances)){
            $shift_basis = end($ot_attendances);
        }

        foreach ($overtime as $_overtime) {
            $overtime_start = null;
            $overtime_end = null;
            $regularOTHours = 0;
            $nDiffOTHours = 0;

            $ot_hrs = 0;
            $ot_night_diff = 0;

            $_otStartDate = date("Y-m-d", strtotime($_overtime->date_from));
            $_otStart = date('Y-m-d H:i', strtotime($_overtime->date_from));
            $_otEnd = date('Y-m-d H:i', strtotime($_overtime->date_to));

            $_otNdiffStart = date("Y-m-d H:i", strtotime($_otStartDate." 22:00:00"));
            $_otNdiffEnd = date("Y-m-d H:i", strtotime("+1 day", strtotime($_otStartDate." 06:00:00")));

            if(isset($night_diff_cfg->start_time) && $night_diff_cfg->start_time){
                $_otNdiffStart = date("Y-m-d H:i", strtotime($_otStartDate." ".$night_diff_cfg->start_time));
            }

            if(isset($night_diff_cfg->end_time) && $night_diff_cfg->end_time){
                $_otNdiffEnd = date("Y-m-d H:i", strtotime("+1 day", strtotime($_otStartDate." ".$night_diff_cfg->end_time)));
            }

            if($_otEnd && strtotime($_otEnd) > 0 && strtotime($_otEnd) > strtotime($_otNdiffStart)){
                $regularOTHours = strtotime($_otNdiffStart) - strtotime($_otStart);
                $regularOTHours = ($regularOTHours / 3600);

                $nDiffOTHours = strtotime($_otEnd) - strtotime($_otNdiffStart);
                $nDiffOTHours = ($nDiffOTHours / 3600);
            }else{
                $regularOTHours = strtotime($_otEnd) - strtotime($_otStart);
                $regularOTHours = ($regularOTHours / 3600);
            }

            $overtime_start = $_otStart;
            $overtime_end = $_otEnd;

            $startOfShift = reset($ot_attendances);
            $isNightShift = strtotime($startOfShift) >= strtotime($_nightShiftStart) && strtotime($startOfShift) <= strtotime($_nightShiftEnd);
            if($isNightShift){ $shift_basis = $_otStart; }
            $arrShifts = array();
            $shiftProps = array("shift_am_start", "shift_am_end", "shift_pm_start", "shift_pm_end");
            $tempShiftDate = date("Y-m-d", strtotime($date));
            foreach ($shiftProps as $key => $value) {
                if(${$value}){
                    if($key > 0 ){
                        $prevValue = ${$shiftProps[$key-1]};
                        if(strtotime(${$value}) < strtotime($prevValue)){ $tempShiftDate = date("Y-m-d", strtotime("+1 day", strtotime($tempShiftDate))); }
                    }
                    $nValue = date("Y-m-d H:i", strtotime($tempShiftDate." ".${$value}));
                    $arrShifts[] = $nValue;
                }
            }

            if(is_array($arrShifts) && !empty($arrShifts)){
                $firstShift = reset($arrShifts);
                if(strtotime($startOfShift) < strtotime($firstShift)){
                    $shift_basis = $firstShift;
                }
            }

            if (sizeof($ot_attendances) > 1) {
                $parameters = array("hasShiftSchedule" => $hasShiftSchedule, "overtime" => $_overtime, "timesheetExist" => $timesheet_exist,
                    "otAttendances" => $ot_attendances, "nightDiffCfg" => $night_diff_cfg, "shiftBasis" => $shift_basis, "otNdiffStart" => $_otNdiffStart,
                    "otNdiffEnd" => $_otNdiffEnd, "nDiffOTHours" => $nDiffOTHours, "isNightShift" => $isNightShift,
                    "regularOTHours" => $regularOTHours, "otNightDiff"=>$ot_night_diff);
                $otScript = $this->overtimeComputationScript($parameters);
                $regularOTHours = $otScript["regularOTHours"];
                $nDiffOTHours = $otScript["nDiffOTHours"];
                $ot_hrs = $otScript["ot_hrs"];
                $ot_night_diff = number_format($otScript["ot_night_diff"], 2, '.', '');
                $_otStart = $otScript["ot_start"];
                $_otEnd = $otScript["ot_end"];

                $overtime_start = date("Y-m-d H:i", strtotime($_otStart));
                $overtime_end = date("Y-m-d H:i", strtotime($_otEnd));
            }else{
                $tempRegOtHours = $regularOTHours;
                if($tempRegOtHours >= 5){
                    $tempRegOtHours = $tempRegOtHours - 1;
                }
                $ot_hrs = $tempRegOtHours;
                $ot_night_diff = $nDiffOTHours;
            }

            $regularOTHours = floatval($regularOTHours) > 0 ? $regularOTHours: 0;
            $nDiffOTHours = floatval($nDiffOTHours) > 0 ? $nDiffOTHours: 0;
            $ot_hrs = floatval($ot_hrs) > 0 ? $ot_hrs: 0;
            $ot_night_diff = floatval($ot_night_diff) > 0 ? $ot_night_diff: 0;

            if($ot_hrs > 0){
                $tempOtHoursToMinutes = $ot_hrs * 60;
                $divisibleBy30 = $tempOtHoursToMinutes % 30;
                if($divisibleBy30 !== 0){
                    $tempOtHoursToMinutes = $tempOtHoursToMinutes - $divisibleBy30;
                }
                $ot_hrs = $tempOtHoursToMinutes / 60;
            }

            if($ot_night_diff > 0){
                $tempOtNdiffToMinutes = $ot_night_diff * 60;
                $divisibleBy30Ndiff = $tempOtNdiffToMinutes % 30;
                if($divisibleBy30Ndiff !== 0){
                    $tempOtNdiffToMinutes = $tempOtNdiffToMinutes - $divisibleBy30Ndiff;
                }
                $ot_night_diff = $tempOtNdiffToMinutes / 60;
            }

            $regularOTHours = round($regularOTHours, 2);
            $nDiffOTHours = round($nDiffOTHours, 2);

            if($_otEnd == $_otStart || strtotime($_otEnd) < strtotime($_otStart)){
                $overtime_start = date("Y-m-d H:i", strtotime($_overtime->date_from));
                $overtime_end = date("Y-m-d H:i", strtotime($_overtime->date_to));
                $regularOTHours = 0;
            }

            if (sizeof((array) $timesheet_exist) >= 1 && intval($generated_manually) <= 0) {
                if($regularOTHours == 0 && $ot_hrs == 0 && $nDiffOTHours == 0 && $ot_night_diff == 0){
                    $this->db->where("timesheet_id", $timesheet_exist->id);
                    $this->db->delete($this->tbl_timesheet_overtime);
                    $this->db->reset_query();
                    $hasOvertimeRequest = false;
                }else{
                    $this->db->where("timesheet_id", $timesheet_exist->id);
                    $this->db->update($this->tbl_timesheet_overtime,
                        array(
                            "overtime_id" => $_overtime->id,
                            "overtime_in" => $overtime_start,
                            "overtime_out" => $overtime_end,
                            "total_hrs" => $regularOTHours,
                            "accredited_hrs" => $ot_hrs,
                            "ndiff_hrs" => $nDiffOTHours,
                            "accredited_ndiff_hrs" => $ot_night_diff,
                        ));
                    $this->db->reset_query();
                    $hasOvertimeRequest = true;
                }
            } else {
                $ts_ot_exist = $this->db
                    ->where("timesheet_id", $timesheet_exist->id)
                    ->where("overtime_id", $_overtime->id)
                    ->get($this->tbl_timesheet_overtime)
                    ->row();

                if (sizeof((array) $ts_ot_exist) >= 1) {
                    if($regularOTHours == 0 && $ot_hrs == 0 && $nDiffOTHours == 0 && $ot_night_diff == 0){
                        $this->db->where("id", $ts_ot_exist->id);
                        $this->db->delete($this->tbl_timesheet_overtime);
                        $this->db->reset_query();
                        $hasOvertimeRequest = false;
                    }else{
                        $this->db->where("id", $ts_ot_exist->id);
                        $this->db->update($this->tbl_timesheet_overtime,
                            array(
                                "overtime_in" => $overtime_start,
                                "overtime_out" => $overtime_end,
                                "total_hrs" => $regularOTHours,
                                "accredited_hrs" => $ot_hrs,
                                "ndiff_hrs" => $nDiffOTHours,
                                "accredited_ndiff_hrs" => $ot_night_diff,
                            ));
                        $this->db->reset_query();
                        $hasOvertimeRequest = true;
                    }
                } else {
                    $this->db->insert($this->tbl_timesheet_overtime,
                        array(
                            "timesheet_id" => $timesheet_exist->id,
                            "overtime_id" => $_overtime->id,
                            "overtime_in" => $overtime_start,
                            "overtime_out" => $overtime_end,
                            "total_hrs" => $regularOTHours,
                            "accredited_hrs" => $ot_hrs,
                            "ndiff_hrs" => $nDiffOTHours,
                            "accredited_ndiff_hrs" => $ot_night_diff,
                        ));
                    $hasOvertimeRequest = true;
                }
            }

            $total_accredited_ot_hrs += $ot_hrs;
            $total_accredited_ot_nightdiff_hrs += $ot_night_diff;
        }

        $resultset["total_accredited_ot_hrs"] = $total_accredited_ot_hrs;
        $resultset["ot_night_diff"] = $total_accredited_ot_nightdiff_hrs;
        $resultset["overtime_in"] = $overtime_start ? $overtime_start: null;
        $resultset["overtime_out"] = $overtime_end ? $overtime_end: null;
        $resultset["allow_overtime_request"] = $hasOvertimeRequest;
        return $resultset;
    }

    public function getPayrateSettingsById($id=null){
        $resultset = array();
        if($id){
            $qTemp = $this->db
            ->from("payroll.payrate_settings")
            ->where("id", $id)
            ->get();

            if($qTemp->num_rows() == 1){
                $row = $qTemp->row();
                $resultset["response"] = true;
                $resultset["row"] = $row;
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    public function getCurrentDateIsHoliday($date=null){
        $resultset = array();
        $payrate_id = 0;
        $tempClassification = null;
        $row = array();

        if($date){
            $tempCurrentDate = date("Y-m-d", strtotime($date));
            $tempYear = date("Y", strtotime($date));
            $tempYearMonth = date("Y-m", strtotime($date));
            $this->db->from($this->tbl_tblholidays);
            $this->db->where("year", $tempYear);
            $this->db->like("start_date", $tempYearMonth);
            $this->db->like("end_date", $tempYearMonth);
            $qTemp = $this->db->get();
            if($qTemp->num_rows() > 0){
                foreach ($qTemp->result() as $key => $value) {
                    $tempStartDate = date("Y-m-d", strtotime($value->start_date));
                    $tempEndDate = date("Y-m-d", strtotime($value->end_date));

                    if(($tempCurrentDate >= $tempStartDate) && ($tempCurrentDate <= $tempEndDate)){
                        $tempClassification = strtolower($value->classification);

                        $tempPs = $this->db
                        ->where("particulars", $tempClassification)
                        ->where("is_holiday", 1)
                        ->get("payroll.payrate_settings");

                        if($tempPs->num_rows() == 1){
                            $row = $tempPs->row();
                            $payrate_id = $row->id;
                        }
                    }

                }
            }
        }
        if($payrate_id){
            $resultset["is_holiday"] = true;
            $resultset["payrate_id"] = $payrate_id;
            $resultset["classification"] = $tempClassification;
            $resultset["row"] = $row;
        }else{
            $resultset["is_holiday"] = false;
            $resultset["payrate_id"] = 0;
        }

        return $resultset;
    }

    public function updateTimesheetHoliday($date=null){
        if($date){
            $this->db->select("GROUP_CONCAT(DISTINCT(id)) as id");
            $qTemp = $this->db->get_where($this->tbl_timesheet, array("date"=>$date));
            if($qTemp->num_rows()  == 1){
                $tempRow = $qTemp->row();
                $ids = explode(",", $tempRow->id);
                if(is_array($ids) && count($ids) > 0){
                    $tempHoliday = (object) $this->ts_model->getCurrentDateIsHoliday($date);
                    $isHoliday = ($tempHoliday->is_holiday === true)? 1: 0;
                    $payRateId = ($tempHoliday->is_holiday === true && $tempHoliday->payrate_id)? $tempHoliday->payrate_id: 0;

                    foreach ($ids as $key => $value) {
                        $this->db->update($this->tbl_timesheet, array("is_holiday"=>$isHoliday, "payrate_id"=>$payRateId), array("id"=>$value, "verified"=>0));
                    }
                }
            }
        }
    }

    public function updateTimesheetHolidayById($id=null){
        if($id){
            $qTemp = $this->db->get_where($this->tbl_timesheet, array("id"=>$id));
            if($qTemp->num_rows() == 1){
                $tempRow = $qTemp->row();
                $tempHoliday = (object) $this->ts_model->getCurrentDateIsHoliday($tempRow->date);
                $isHoliday = ($tempHoliday->is_holiday == true)? 1: 0;
                $payRateId = ($tempHoliday->is_holiday == true && $tempHoliday->payrate_id)? $tempHoliday->payrate_id: 0;
                $updated = $this->db->update($this->tbl_timesheet, array("is_holiday"=>$isHoliday, "payrate_id"=>$payRateId), array("id"=>$tempRow->id, "verified"=>0));
            }
        }
    }

    private function getAttendance($date=null, $employees=[], $end_date=null, $nightShiftRecord=null){
        $date = date("Y-m-d H:i:s", strtotime("-30 minutes", strtotime($date)));
        $select = "employees.id, employees.lastname, employees.firstname, DATE_FORMAT('$date', '%W') `weekday`,";
        $select .= "DATE_FORMAT('$date', '%w') `day`, attendance.*, personnel.shift_id";

        $this->db->select($select);
        $this->db->join($this->tbl_personnel . " personnel", "personnel.biometric_id = attendance.biometric_id", "INNER");
        $this->db->join($this->tbl_employees . " employees", "employees.biometricno = personnel.biometricno", "INNER");
        $this->db->where("attendance.`datetime` >= ", $date);
        $this->db->where("attendance.`datetime` <= ", $end_date);
        if($nightShiftRecord){ $this->db->where("attendance.`datetime` != ", $nightShiftRecord); }
        if (!empty($employees)) {
            $this->db->where_in("employees.id", $employees);
        }
        $this->db->order_by("employees.lastname, attendance.`datetime`", "ASC");
        $qTemp = $this->db->get($this->tbl_attendance . " attendance");
        return $qTemp->result();
    }

    private function getEmployeesWithAttendance($date, $is_custom, $employees = array())
    {
        $select = "employees.id, employees.lastname, employees.firstname, employees.payroll_type, personnel.shift_id, personnel.is_flexi, attendance.biometric_id";

        $this->db->select($select);
        $this->db->join($this->tbl_personnel . " personnel", "personnel.biometric_id = attendance.biometric_id", "INNER");
        $this->db->join($this->tbl_employees . " employees", "employees.biometricno = personnel.biometricno", "INNER");
        $this->db->where("DATE(attendance.`datetime`)", $date);
        if($is_custom !== null){
            $this->db->where("attendance.is_custom", $is_custom);
        }

        if (!empty($employees)) {
            $this->db->where_in("employees.id", $employees);
        }
        $this->db->group_by("employees.id");
        $this->db->order_by("employees.lastname", "ASC");

        $tempAttendance = $this->db->get($this->tbl_attendance . " attendance");
        return $tempAttendance->result();
    }

    private function getShiftResource($shift_id)
    {
        $arrData = new StdClass();
        $tempQuery = $this->db->get_where($this->tbl_shift_schedule_resource, array("shift_id" => $shift_id));
        if($tempQuery->num_rows() == 1){
            $arrData = $tempQuery->row();
        }
        return $arrData;
    }

    private function getScheduleList($weekday, $ids = array())
    {
        if($ids){ $this->db->where_in("id", $ids); }
        $this->db->where("weekday", $weekday);
        return $this->db->get($this->tbl_shift_schedule_list)->row();
    }

    private function getEmployeeLoa($type, $employee_id, $date)
    {
        $where = array("employee" => $employee_id, "type" => $type, "status" => "Approved", "DATE(date_from) <=" => $date, "DATE(date_to) >=" => $date);
        return $this->db->get_where($this->tbl_loa, $where)->row();
    }

    private function arrayToStdClass($array)
    {
        return json_decode(json_encode($array));
    }

    public function getTimeSheet()
    {
        $post = $this->arrayToStdClass($this->input->post());
        $cut_off = $post->filter->cut_off;
        $dates = $post->filter->dates;
        $employees_filter = isset($post->filter->employees) && !empty($post->filter->employees) ? $post->filter->employees : null;
        $company_id = $post->filter->company;
        $company = $this->db->get_where("gcchris.tblcompanies", array("id" => $company_id))->row();
        $inclusive_filter = $post->inclusive_filter;
        $hasExistingOvertime = array();
        $default_shift_employees = array();
        
        $excluded_employees = array_map(function ($list) {
            return $list->emp_id;
        }, $this->db->select("emp_id")
            ->get("gcctimeutility.timesheet_excluded_employees")
            ->result());

        $status_filter = $post->filter->status;

        $start = date("Y-m-d");
        $end = date("Y-m-d");
        $currentYear = intval(date('Y'));
        $currentMonth = intval(date('m'));

        $this->db->where("emp.employee_status", "Active");
        if (!empty($employees_filter)) {
            $this->db->where_in("emp.id", $employees_filter);
            if (!empty($excluded_employees)) {
                $this->db->where_not_in("emp.id", $excluded_employees);
            }
        } else {
            switch (intval($inclusive_filter)) {
                case 1:
                    if (!empty($excluded_employees)) {
                        $this->db->where_not_in("emp.id", $excluded_employees);
                    }
                    break;
                case 2:
                    $this->db->where_in("emp.id", $excluded_employees);
                    break;
            }
        }

        if (empty($cut_off)) {
            $dates_arr = explode("/", $dates);
            $start = date("Y-m-d", strtotime($dates_arr[0]));
            $end = date("Y-m-d", strtotime($dates_arr[1]));
        } else {
            $cut_off_arr = explode("-", $cut_off);
            $startDate = intval($cut_off_arr[0]);
            $endDate = intval($cut_off_arr[1]);
            $month = $currentMonth;
            $year = $currentYear;

            if ($startDate > $endDate) {
                if ($currentMonth <= 1) {
                    $month = 12;
                    $year -= 1;
                } else {
                    $month -= 1;
                }
                $start = date("Y-m-d", strtotime("$year-$month-$startDate"));
                $end = date("Y-m-d", strtotime("$currentYear-$currentMonth-$endDate"));
            } else {
                $start = date("Y-m-d", strtotime("$currentYear-$currentMonth-$startDate"));
                $end = date("Y-m-d", strtotime("$currentYear-$currentMonth-$endDate"));
            }
        }

        $this->db->select("emp.id, personnel.is_flexi, resource.shift_resource, UCASE(CONCAT(emp.lastname,
            CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''  END, ', ',
            emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
            AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END)) `employee_name`, personnel.biometric_id,
            emp.payroll_type");
        $this->db->join("gcctimeutility.personnel personnel", "personnel.biometricno = emp.biometricno", "INNER");
        $this->db->join("gcctimeutility.shift_schedule_resource resource", "resource.shift_id = personnel.shift_id", "LEFT");
        $this->db->join("gcchris.tblcompanies companies", "companies.id = emp.company_id", "LEFT");

        if (!empty($company_id)) {
            $this->db->group_start();
            $this->db->where("companies.id", $company_id);
            $this->db->or_where_in("emp.company_id", array($company->description, $company->code));
            $this->db->group_end();
        }
        $this->db->order_by("emp.lastname", "asc");
        $this->db->group_by("emp.id");
        $employees = $this->db->get($this->tbl_employees . " emp");
        $sql = $this->db->last_query();
        $timesheets = array();
        $this->db->reset_query();

        $tempToRecord = array();
        $tempLoaRecord = array();
        $tempOvertimeRecord = array();
        $alteredShiftRecords = array();
        $temp_lastQ = null;

        $tempPostedPayrollRecord = array();
        
        $interval = DateInterval::createFromDateString('1 day');
        $_dateStart = new DateTime($start);
        $_dateEnd = new DateTime($end);
        $_dateEnd->modify("+1 day");
        $period = new DatePeriod($_dateStart, $interval, $_dateEnd);
        foreach ($period as $dt) {
            $tempDate = $dt->format("Y-m-d");

            $this->db->select("emp_id");
            $this->db->from("payroll.payroll_sheet");
            $this->db->where("posted", 1);
            $this->db->group_start();
            $this->db->where("DATE(date_start) <=", $tempDate);
            $this->db->where("DATE(date_end) >=", $tempDate);
            $this->db->group_end();
            $payrollSheetRecord = $this->db->get();

            if($payrollSheetRecord->num_rows() > 0){
                foreach ($payrollSheetRecord->result() as $key => $value) {
                    $tempValue = md5($value->emp_id."_".$tempDate);
                    if(!in_array($tempValue, $tempPostedPayrollRecord)){
                        $tempPostedPayrollRecord[] = $tempValue;
                    }
                }
            }
        }

        $arrMonthlyEmployeeIds = array();
        if(is_array($employees_filter) && count($employees_filter) > 0){
            $this->db->select("emp_id");
            $this->db->from($this->tbl_timesheet_monthly_employees);
            $this->db->where_in("emp_id", $employees_filter);
            $qMonthlyEmployees = $this->db->get();
            if($qMonthlyEmployees->num_rows() > 0){
                foreach ($qMonthlyEmployees->result() as $employee) {
                    $arrMonthlyEmployeeIds[] = $employee->emp_id;
                }
            }
        }

        if($employees->num_rows() > 0){
            $empIds = array();
            $shiftResource = array();
            foreach ($employees->result() as $employee) {
                if(!in_array($employee->id, $empIds)){
                    $empIds[] = $employee->id;
                    $schedule_resource = @unserialize($employee->shift_resource);
                    $this->db->where_in("id", $schedule_resource);
                    $queryShift = $this->db->get($this->tbl_shift_schedule_list);
                    if($queryShift->num_rows() > 0){
                        $props = array("am_start", "am_end", "pm_start", "pm_end");
                        foreach ($queryShift->result() as $key => $value) {
                            $tempWeekday = strtolower(trim($value->weekday));
                            $tempRecord = array();
                            foreach ($props as $key => $vx) {
                                $tempRecord[$vx] = $value->$vx;
                            }
                            $empRow = "emp_id_{$employee->id}";
                            $shiftResource[$empRow][$tempWeekday] = $tempRecord;
                        }
                    }
                    $this->db->reset_query();
                }
            }

            $empIds = array_unique($empIds);

            $tempToRecord = $this->getToRecordByDateRange($start);
            $tempLoaRecord = $this->getLoaRecordByDateRange($start);
            $tempOvertimeRecord = $this->getOvertimeRecordByDateRange($start, $empIds);
            $alteredShiftRecords = $this->getAlteredShiftRecordByDateRange($start, $end);
            
            foreach ($employees->result() as $employee) {
                $isNoInOut = intval($employee->is_flexi) === 4;
                $id = $employee->id;
                $employee_name = $employee->employee_name;
                $biometricno = $employee->biometric_id;
                $payroll_type = $employee->payroll_type;
                $isMonthlyPaidEmployee = in_array($id, $arrMonthlyEmployeeIds);
                $hasLoaRecords = array();
                $hasToRecords = array();
                $hasOvertimeRecords = array();
                $tempKeySearch = "emp_id_{$id}";
                if(isset($tempLoaRecord[$tempKeySearch]) && is_array($tempLoaRecord[$tempKeySearch]) && count($tempLoaRecord[$tempKeySearch]) > 0){
                    $hasLoaRecords = $tempLoaRecord[$tempKeySearch];
                }

                if(isset($tempToRecord[$tempKeySearch]) && is_array($tempToRecord[$tempKeySearch]) && count($tempToRecord[$tempKeySearch]) > 0){
                    $hasToRecords = $tempToRecord[$tempKeySearch];
                }
                
                if(isset($tempOvertimeRecord[$tempKeySearch]) && is_array($tempOvertimeRecord[$tempKeySearch]) && count($tempOvertimeRecord[$tempKeySearch]) > 0){
                    $hasOvertimeRecords = $tempOvertimeRecord[$tempKeySearch];
                }

                $queryTimesheet = "SELECT '$employee_name' employee_name, 
                    ts.id AS tsID,
                    adddate('$start', numlist.id) as _date,
                    DATE_FORMAT(ADDDATE('$start', numlist.id), '%a') _weekday,
                    lcase(DATE_FORMAT(ADDDATE('$start', numlist.id), '%W')) _weekday_full,
                    $id _emp_id,
                    '$payroll_type' payroll_type,
                    ts.*, '$biometricno' biometricno FROM
                (SELECT n1.i + n10.i*10 + n100.i*100 AS id
                    FROM gcctimeutility.num n1 cross join gcctimeutility.num as n10 cross join gcctimeutility.num as n100) as numlist
                LEFT JOIN gcctimeutility.timesheet ts ON ts.date = adddate('$start', numlist.id) AND ts.emp_id = $id
                LEFT JOIN gccmaster.tblemployees emp ON emp.id = ts.emp_id
                where adddate('$start', numlist.id) <= '$end' ORDER BY adddate('$start', numlist.id)";

                if (!empty($status_filter)) {
                    switch ($status_filter) {
                        case "lacking":
                            $queryTimesheet .= " AND ts.scrub_status=1 AND ts.verified=0";
                            break;
                        case "multiple":
                            $queryTimesheet .= " AND ts.scrub_status=2";
                            break;
                        case "no-shift":
                            $queryTimesheet .= " AND ts.scrub_status=3";
                            break;
                        case "verified":
                            $queryTimesheet .= " AND ts.verified=1";
                            break;
                        case "unverified":
                            $queryTimesheet .= " AND ts.verified=0";
                            break;
                        default:
                            $queryTimesheet .= " AND ts.verified=0";
                            break;
                    }
                }
                $employeeTimesheet = $this->db->query($queryTimesheet)->result();
                $dtr_count = array_filter($employeeTimesheet, function ($dtr) {
                    return !empty($dtr->id);
                });

                $dtr_count_verified = array_filter($employeeTimesheet, function ($dtr) {
                    return intval($dtr->verified) === 1;
                });

                $default_current_timestamp = strtotime(date('Y-m-d H:i:s'));
                $FORM_SORTBY = "";
                $ARRAY_SORTBY = array();
                $NEW_ARRAY_SORTBY = array();
                foreach ($employeeTimesheet as $timesheet) {
                    $timesheet->has_TO = 0;
                    $timesheet->has_LOA = 0;
                    $timesheet->has_whole_day_LOA = 0;
                    $timesheet->has_pending_adjustment = false;
                    $timesheet->allow_paid_holiday = false;
                    $timesheet->has_loa_records = array();
                    $timesheet->complete_attendance_count = false;
                    $timesheet->is_monthly_paid = $isMonthlyPaidEmployee;
                    $timesheet->is_no_in_out = $isNoInOut;

                    $timesheet->dtr_count = sizeof($dtr_count);
                    $timesheet->dtr_count_verified = sizeof($dtr_count_verified);
                    $timesheet->all_verified = (sizeof($dtr_count) === sizeof($dtr_count_verified)) && sizeof($dtr_count) > 0;

                    $temp_posted_record = md5($timesheet->_emp_id."_".$timesheet->_date);
                    $timesheet->is_posted = false;
                    $timesheet->is_posted = in_array($temp_posted_record, $tempPostedPayrollRecord);

                    $schedule_list = isset($shiftResource["emp_id_{$id}"][$timesheet->_weekday_full]) && $shiftResource["emp_id_{$id}"][$timesheet->_weekday_full] ? 
                        $shiftResource["emp_id_{$id}"][$timesheet->_weekday_full]: array();
                    if (!empty($schedule_list)) {
                        $schedule_list = (object) $schedule_list;
                        $timesheet->has_shift = ($schedule_list->am_start === null && $schedule_list->am_end === null
                            && $schedule_list->pm_start === null && $schedule_list->pm_end === null) ? 0 : 1;
                        
                        if($isMonthlyPaidEmployee && intval($timesheet->has_shift) === 1){
                            $tempArrSchedule = array("am_start"=>"am_in", "am_end"=>"am_out", "pm_start"=>"pm_in", "pm_end"=>"pm_out");
                            $tempArrShiftSchedule = array("am_start"=>"shift_am_start", "am_end"=>"shift_am_end", "pm_start"=>"shift_pm_start", "pm_end"=>"shift_pm_end");
                            foreach ($tempArrSchedule as $key => $value) {
                                if(isset($schedule_list->{$key}) && $schedule_list->{$key} && $schedule_list->{$key} != "00:00:00"){
                                    $timesheet->{$value} = $schedule_list->{$key};
                                } 
                                if(isset($schedule_list->{$key}) && $schedule_list->{$key} && $schedule_list->{$key} != "00:00:00"){
                                    if(isset($tempArrShiftSchedule[$key]) && $tempArrShiftSchedule[$key]){
                                        $tempShiftValue = $tempArrShiftSchedule[$key];
                                        $timesheet->{$tempShiftValue} = $schedule_list->{$key};
                                    }
                                }
                            }
                        }
                    }

                    $tempMd5Date = md5($timesheet->_date);
                    $tempAlteredShift = isset($alteredShiftRecords[$timesheet->_emp_id][$tempMd5Date]) && $alteredShiftRecords[$timesheet->_emp_id][$tempMd5Date] ? 
                        $alteredShiftRecords[$timesheet->_emp_id][$tempMd5Date]: array();
                    if(is_array($tempAlteredShift) && count($tempAlteredShift) > 0){
                        $_tempAlteredShift = (object) $tempAlteredShift;
                        if(isset($_tempAlteredShift->schedule) && $_tempAlteredShift->schedule){
                            foreach ($_tempAlteredShift->schedule as $key => $value) {
                                $timesheet->$key = $value;
                            }
                        }
                        $timesheet->has_shift = $_tempAlteredShift->has_shift;
                    }
                    
                    $timesheet->altered_shift = $tempAlteredShift;

                    if (intval($timesheet->with_adjustment) === 1) {
                        $adjustmentTemp = $this->db->get_where($this->tbl_time_adjustments, array("timesheet_id"=>$timesheet->id, "status"=>0));
                        if($adjustmentTemp->num_rows() > 0){
                            $timesheet->has_pending_adjustment = true;
                        }
                    }
                    
                    if(is_array($hasToRecords) && count($hasToRecords) > 0){
                        $xTo = 0;
                        $hasCurrentTO = false;
                        foreach ($hasToRecords as $valuezzz) {
                            $xxxz123 = explode("::", $valuezzz);
                            if(is_array($xxxz123) && count($xxxz123) == 2){
                                $tempDTxx = explode("__", $xxxz123[1]);
                                $tempDate000 = strtotime(date("Y-m-d", strtotime($tempDTxx[0])));
                                $tempDate001 = strtotime(date("Y-m-d", strtotime($tempDTxx[1])));
                                $ccDatexxx = strtotime(date("Y-m-d", strtotime($timesheet->_date)));

                                if($ccDatexxx >= $tempDate000 && $ccDatexxx <= $tempDate001){
                                    $hasCurrentTO = true;
                                    $xTo++;
                                }
                            }
                        }
                        $timesheet->has_TO = $xTo;
                        if($hasCurrentTO){
                            $timesheet->datelist = array_push($ARRAY_SORTBY, array("TO", $timesheet->_date));
                        }
                    }
                    
                    if ($timesheet->has_shift === 1) {
                        if(is_array($hasLoaRecords) && count($hasLoaRecords) > 0){
                            $xLoa = 0;
                            $hasCurrentLoa = false;
                            foreach ($hasLoaRecords as $valuexxx) {
                                $xx123 = explode("::", $valuexxx);
                                if(is_array($xx123) && count($xx123) == 3){
                                    $tempDTx = explode("__", $xx123[1]);
                                    $tempIsWholeDay = intval($xx123[2]) == 1? 1: 0;
                                    $tempDate00 = strtotime(date("Y-m-d", strtotime($tempDTx[0])));
                                    $tempDate01 = strtotime(date("Y-m-d", strtotime($tempDTx[1])));
                                    $ccDatex = strtotime(date("Y-m-d", strtotime($timesheet->_date)));
    
                                    if($ccDatex >= $tempDate00 && $ccDatex <= $tempDate01){
                                        $timesheet->has_whole_day_LOA = $tempIsWholeDay;
                                        $hasCurrentLoa = true;
                                        $xLoa++;
                                    }
                                }
                            }
                            $timesheet->has_LOA = $xLoa;
                            if($hasCurrentLoa){
                                $timesheet->datelist = array_push($ARRAY_SORTBY, array("LOA", $timesheet->_date));
                            }
                        }

                        $propAttx = ["am_in", "am_out", "pm_in", "pm_out"];
                        $propSchedule = ["shift_am_start", "shift_am_end", "shift_pm_start", "shift_pm_end"];

                        $ctrAttx = 0;
                        $ctrSchedx = 0;
                        $nArr = (array) $timesheet;

                        foreach ($propSchedule as $scx) { if(isset($nArr[$scx]) && $nArr[$scx]){ $ctrSchedx++; } }
                        foreach ($propAttx as $atx) { if(isset($nArr[$atx]) && $nArr[$atx]){ $ctrAttx++; } }

                        $timesheet->complete_attendance_count = $ctrSchedx == $ctrAttx;
                    }

                    $allowPaidEmployee = $timesheet->payroll_type == "monthly" || $timesheet->payroll_type == "daily" || $timesheet->payroll_type == "project based";
                    $tempResponse = (object) $this->getCurrentDateIsHoliday($timesheet->_date);
                    $timesheet->is_holiday = (isset($tempResponse->is_holiday) && $tempResponse->is_holiday === true)? 1: 0;
                    $timesheet->payrate_id = (isset($tempResponse->is_holiday, $tempResponse->payrate_id) && ($tempResponse->is_holiday === true && $tempResponse->payrate_id))? $tempResponse->payrate_id: 0;
                    $timesheet->paid_holiday = (isset($timesheet->paid_holiday) && $timesheet->paid_holiday == null)? 0: intval($timesheet->paid_holiday);
                    if (isset($tempResponse->classification) && strtolower($tempResponse->classification) == "special non-working holiday" && $timesheet->payroll_type == "monthly"){
                        $timesheet->allow_paid_holiday = true;
                    } elseif (isset($tempResponse->classification) && strtolower($tempResponse->classification) == "regular holiday" && $allowPaidEmployee){
                        $timesheet->allow_paid_holiday = true;
                    }

                    if(is_array($hasOvertimeRecords) && count($hasOvertimeRecords) > 0){
                        $xOvertime = 0;
                        $hasCurrentOvertime = false;
                        foreach ($hasOvertimeRecords as $valuezzz1) {
                            $xxxxz1233 = explode("::", $valuezzz1);
                            if(is_array($xxxxz1233) && count($xxxxz1233) == 2){
                                $tempDTxxx = explode("__", $xxxxz1233[1]);
                                $tempDate0000 = strtotime(date("Y-m-d", strtotime($tempDTxxx[0])));
                                $tempDate0001 = strtotime(date("Y-m-d", strtotime($tempDTxxx[1])));
                                $ccDatexxxx = strtotime(date("Y-m-d", strtotime($timesheet->_date)));

                                $diffStart = new DateTime($tempDTxxx[0]);
                                $diffEnd = new DateTime($tempDTxxx[1]);
                                $overtimeDays = $diffStart->diff($diffEnd)->days;
                                $showOtInfo = $overtimeDays === 1 && $ccDatexxxx > $tempDate0000 ? false: true;

                                if($showOtInfo && $ccDatexxxx >= $tempDate0000 && $ccDatexxxx <= $tempDate0001){
                                    if($timesheet->tsID && $timesheet->has_overtime && $timesheet->verified == 0){
                                        $overtimeIn = date("m/d/Y h:i A", strtotime($timesheet->overtime_in));
                                        $overtimeOut = date("m/d/Y h:i A", strtotime($timesheet->overtime_out));
                                        $tempOtDate = date("m/d/Y", strtotime($timesheet->_date));

                                        $tempOvertimeData = array("tsID"=>$timesheet->tsID,"date"=>$tempOtDate,
                                            "employee_name"=>$timesheet->employee_name, "reference_no"=>$xxxxz1233[0],
                                            "overtime_in"=>$overtimeIn, "overtime_out"=>$overtimeOut,
                                            "accredited_ot_hrs"=>$timesheet->total_accredited_ot_hrs,
                                            "accredited_ndiff_ot_hrs"=>$timesheet->total_accredited_ndiff_ot_hrs,
                                        );

                                        $flagOtHrs = false;
                                        $this->db->select("total_hrs, accredited_hrs, ndiff_hrs, accredited_ndiff_hrs");
                                        $this->db->from($this->tbl_timesheet_overtime);
                                        $this->db->where("timesheet_id", $timesheet->tsID);
                                        $tsOt = $this->db->get();
                                        if($tsOt->num_rows() == 1){
                                            $tsRow = $tsOt->row();
                                            $totHrs = floatval($tsRow->total_hrs);
                                            $totAccHrs = floatval($tsRow->accredited_hrs);
                                            $ndiffHrs = floatval($tsRow->ndiff_hrs);
                                            $ndiffAccHrs = floatval($tsRow->accredited_ndiff_hrs);

                                            if($totHrs != 0 && $totAccHrs != 0 && $ndiffHrs != 0 && $ndiffAccHrs != 0){
                                                if(($totHrs > 0 && $totAccHrs >= 0) && ($totHrs != $totAccHrs)){ $flagOtHrs = true; }
                                                if(($ndiffHrs > 0 && $ndiffAccHrs >= 0) && ($ndiffHrs != $ndiffAccHrs)){ $flagOtHrs = true; }
                                            }
                                        }
                                        $this->db->reset_query();

                                        if($flagOtHrs){ $hasExistingOvertime[] = $tempOvertimeData; }
                                    }

                                    $hasCurrentOvertime = true;
                                    $xOvertime++;
                                }
                            }
                        }
                        $timesheet->has_overtime = intval($xOvertime) > 0 ? 1: 0;
                        if($hasCurrentOvertime){
                            $timesheet->datelist = array_push($ARRAY_SORTBY, array("OT", $timesheet->_date));
                        }
                    }

                    usort($ARRAY_SORTBY, function ($one, $two) {
                        $datetime1 = strtotime($one[1]);
                        $datetime2 = strtotime($two[1]);
                        return $datetime1 - $datetime2;
                    });

                    $timesheet->datelist = $ARRAY_SORTBY;

                    if($isMonthlyPaidEmployee && intval($timesheet->has_shift) === 1){
                        $updatedMonthlyPaidTimesheet = (array) $this->updateTimesheetShiftComputation($timesheet);
                        $newTimesheetRecords = array_merge((array) $timesheet, $updatedMonthlyPaidTimesheet);
                        $timesheet = (object) $newTimesheetRecords;
                    }

                    array_push($timesheets, $timesheet);

                    $FORM_SORTBY="";
                    unset($ARRAY_SORTBY);
                    $ARRAY_SORTBY = array();

                    if(!$timesheet->tsID && $timesheet->has_shift === 1 && strtotime($timesheet->_date) < $default_current_timestamp && $isNoInOut){
                        if(!isset($default_shift_employees[$timesheet->_emp_id])){ 
                            $default_shift_employees[$timesheet->_emp_id] = array("employee_name"=>$timesheet->employee_name, "emp_id"=>$timesheet->_emp_id, "ctr"=>0);
                        }
                        $default_shift_employees[$timesheet->_emp_id]["ctr"]++;
                    }
                }
                if (!empty($status_filter)) {
                    switch ($status_filter) {
                        case "completed":
                            $timesheets = array_filter($timesheets, function ($timesheet) {
                                return $timesheet->all_verified === true;
                            });
                        break;
                        case "incomplete":
                            $timesheets = array_filter($timesheets, function ($timesheet) {
                                return $timesheet->all_verified === true;
                            });
                        break;
                        default:
                            $timesheets = array_filter($timesheets, function ($timesheet) {
                                return $timesheet->all_verified === true;
                            });
                        break;
                    }
                }
            }
        }

        return array(
            "data" => $timesheets,
            "sql" => null,
            "shift_resource"=>$alteredShiftRecords,
            "to_record"=>$tempToRecord,
            "loa_record"=>$tempLoaRecord,
            "overtime_record"=>$tempOvertimeRecord,
            "has_existing_overtime"=>$hasExistingOvertime,
            "to_last_query"=>$temp_lastQ,
            "default_shift_employees"=>$default_shift_employees
        );
    }
    
    private function getAlteredShiftRecordByDateRange($startDate=null, $endDate=null){
        $results = array();
        if($startDate && $endDate){
            $this->db->from($this->tbl_timesheet_customized_shift_schedule);
            $this->db->where("DATE(scheduled_date) >=", $startDate);
            $this->db->where("DATE(scheduled_date) <=", $endDate);
            $this->db->order_by("scheduled_date", "ASC");
            $qAltered = $this->db->get();
            if($qAltered->num_rows() > 0){
                foreach ($qAltered->result() as $kkkx => $vvvx) {
                    $hasShift = intval($vvvx->has_shift) == 1;
                    $_currentxDate = $vvvx->scheduled_date;
                    $_md5Date = md5($_currentxDate);
    
                    $weekDay = date("l", strtotime($_currentxDate));
                    $shiftIndexes = array("shift_am_start", "shift_am_end", "shift_pm_start", "shift_pm_end");
                    $alterShiftIndexes = array("am_start", "am_end", "pm_start", "pm_end");
                    $shifts = @unserialize($vvvx->shift_id);
                    $_xemployees = @unserialize($vvvx->employee_id);
    
                    if(is_array($shifts) && count($shifts) > 0){
                        $this->db->select("b.id");
                        $this->db->from($this->tbl_personnel." a");
                        $this->db->join($this->tbl_employees." b", "b.biometricno = a.biometric_id OR b.biometricno = a.biometricno");
                        $this->db->where_in("a.shift_id", $shifts);
                        $qtempEmps = $this->db->get();
                        if($qtempEmps->num_rows() > 0){
                            foreach ($qtempEmps->result() as $keyzc => $valuezc) {
                                if(!in_array($valuezc, $_xemployees)){ $_xemployees[] = $valuezc->id; }
                            }
                        }
                    }
                    
                    if(is_array($_xemployees) && count($_xemployees) > 0){
                        $this->db->select("a.shift_resource, c.id");
                        $this->db->from($this->tbl_shift_schedule_resource." a");
                        $this->db->join($this->tbl_personnel." b", "b.shift_id = a.id");
                        $this->db->join($this->tbl_employees." c", "c.biometricno = b.biometric_id OR c.biometricno = b.biometricno");
                        $this->db->where_in("c.id", $_xemployees);
                        $qResource = $this->db->get();
                        if($qResource->num_rows() > 0){
                            foreach ($qResource->result() as $kxxa => $vxxa) {
                                $_shiftResource = @unserialize($vxxa->shift_resource);
                                if(is_array($_shiftResource) && count($_shiftResource) > 0){
                                    $this->db->select(implode("," ,$alterShiftIndexes));
                                    $this->db->from($this->tbl_shift_schedule_list);
                                    $this->db->where("weekday", strtolower($weekDay));
                                    $this->db->where_in("id", $_shiftResource);
                                    $qshifts = $this->db->get();
                                    if($qshifts->num_rows() > 0){
                                        foreach ($qshifts->result() as $ssx => $ssv) {
                                            $nRowData = array();
                                            foreach ($alterShiftIndexes as $kx1 => $vx1) {
                                                $tempCustomKey = $shiftIndexes[$kx1];
                                                if(isset($vvvx->$tempCustomKey) && $vvvx->$tempCustomKey && $vvvx->$tempCustomKey !== null && $hasShift){
                                                    $nRowData[$tempCustomKey] = $vvvx->$tempCustomKey;
                                                }else{
                                                    $nRowData[$tempCustomKey] = null;
                                                }
                                            }
                                            $results[$vxxa->id][$_md5Date]["schedule"] = $nRowData;
                                            $results[$vxxa->id][$_md5Date]["has_shift"] = intval($vvvx->has_shift);
                                            $results[$vxxa->id][$_md5Date]["altered_date"] = $_currentxDate;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    
        return $results;
    }
    
    private function getOvertimeRecordByDateRange($startDate=null, $empId=[]){
        $results = array();
        if($startDate){
            $this->db->select("date_from, date_to, employee, reference_no");
            $this->db->from($this->tbl_overtime);
            $this->db->group_start();
            $this->db->where("'{$startDate}' BETWEEN DATE(date_from) AND DATE(date_to)", null, false);
            $this->db->or_where("DATE(date_from) >=", $startDate);
            $this->db->group_end();
            if(is_array($empId) && !empty($empId)){ $this->db->where_in("employee", $empId); }
            $this->db->where("status", "Approved");
            $this->db->order_by("date_from", "ASC");
            $qOvertime = $this->db->get();
            if($qOvertime->num_rows() > 0){
                foreach ($qOvertime->result() as $vvx) {
                    $_from = date("Y-m-d", strtotime($vvx->date_from));
                    $_to = date("Y-m-d", strtotime($vvx->date_to));
                    $_reference = $vvx->reference_no;
                    $merged = $_reference."::".$_from."__".$_to;
    
                    if($vvx->employee && intval($vvx->employee) > 0){
                        $temp_xx1 = "emp_id_{$vvx->employee}";
                        if(!isset($results[$temp_xx1])){ $results[$temp_xx1] = array(); }
                        if(!in_array($merged, $results[$temp_xx1])){
                            $results[$temp_xx1][] = $merged;
                        }
                    }
                }
            }
        }
    
        return $results;
    }
    
    private function getToRecordByDateRange($startDate=null){
        $results = array();
        if($startDate){
            $this->db->select("a.date_from, a.date_to, c.employee_id, b.driver_id, b.reference_no");
            $this->db->from($this->tbl_TO_Destination." a");
            $this->db->join($this->tbl_TO." b", "b.id = a.travel_order_id");
            $this->db->join($this->tbl_TO_Personnel." c", "c.travel_order_id = b.id");
            $this->db->group_start();
            $this->db->where("'{$startDate}' BETWEEN DATE(a.date_from) AND DATE(a.date_to)", null, false);
            $this->db->or_where("DATE(a.date_from) >=", $startDate);
            $this->db->group_end();
            $this->db->where("b.status", "Approved");
            $this->db->order_by("a.date_from", "ASC");
            $qTravelOrder = $this->db->get();
            if($qTravelOrder->num_rows() > 0){
                foreach ($qTravelOrder->result() as $vvx) {
                    $_from = date("Y-m-d", strtotime($vvx->date_from));
                    $_to = date("Y-m-d", strtotime($vvx->date_to));
                    $_reference = $vvx->reference_no;
                    $merged = $_reference."::".$_from."__".$_to;
                    
                    if($vvx->employee_id && intval($vvx->employee_id) > 0){
                        $temp_xx1 = "emp_id_{$vvx->employee_id}";
                        if(!isset($results[$temp_xx1])){ $results[$temp_xx1] = array(); }
                        if(!in_array($merged, $results[$temp_xx1])){
                            $results[$temp_xx1][] = $merged;
                        }
                    }
                    if($vvx->driver_id && intval($vvx->driver_id) > 0){
                        $temp_xx2 = "emp_id_{$vvx->driver_id}";
                        if(!isset($results[$temp_xx2])){ $results[$temp_xx2] = array(); }
                        if(!in_array($merged, $results[$temp_xx2])){
                            $results[$temp_xx2][] = $merged;
                        }
                    }
                }
            }
            $this->db->reset_query();
        }
        return $results;
    }
    
    private function getLoaRecordByDateRange($startDate=null){
        $results = array();
        if($startDate){
            $this->db->select("date_from, date_to, employee, reference_no, type");
            $this->db->from($this->tbl_loa);
            $this->db->group_start();
            $this->db->where("'{$startDate}' BETWEEN DATE(date_from) AND DATE(date_to)", null, false);
            $this->db->or_where("DATE(date_from) >=", $startDate);
            $this->db->group_end();
            $this->db->where("status", "Approved");
            $this->db->order_by("date_from", "ASC");
            $qApprovedLoa = $this->db->get();
            if($qApprovedLoa->num_rows() > 0){
                foreach ($qApprovedLoa->result() as $vvx) {
                    $_from = date("Y-m-d", strtotime($vvx->date_from));
                    $vvx->date_to = isset($vvx->date_to) && $vvx->date_to == "0000-00-00 00:00:00" ? $vvx->date_from: $vvx->date_to;
                    $_to = date("Y-m-d", strtotime($vvx->date_to));
                    $_reference = $vvx->reference_no;
                    $isWholeDayLoa = 0;
                    if(intval($vvx->type) >= 3){
                        $dt00 = strtotime($vvx->date_from);
                        $dt01 = strtotime($vvx->date_to);
                        $dtDiff = abs($dt01 - $dt00);
                        $dtDays = $dtDiff / ( 60 * 60 * 24);
                        if(intval(floor($dtDays)) >= 1){
                            $isWholeDayLoa = 1;
                        }else{
                            $dtHours = $dtDiff / ( 60 * 60);
                            $dtHours = ceil($dtHours / 60);
                            if($dtHours >= 8){
                                $isWholeDayLoa = 1;
                            }
                        }
                    }
    
                    $merged = $_reference."::".$_from."__".$_to."::".$isWholeDayLoa;
                    
                    if($vvx->employee && intval($vvx->employee) > 0){
                        $temp_xx1 = "emp_id_{$vvx->employee}";
                        if(!isset($results[$temp_xx1])){ $results[$temp_xx1] = array(); }
                        if(!in_array($merged, $results[$temp_xx1])){
                            $results[$temp_xx1][] = $merged;
                        }
                    }
                }
            }
    
            $this->db->reset_query();
        }
    
        return $results;
    }
    
    public function getTimeSheet_copy()
    {
        $post = $this->arrayToStdClass($this->input->post());
        $cut_off = $post->filter->cut_off;
        $dates = $post->filter->dates;
        $employees_filter = isset($post->filter->employees) && !empty($post->filter->employees) ? $post->filter->employees : null;
        $company_id = $post->filter->company;
        $company = $this->db->get_where("gcchris.tblcompanies", array("id" => $company_id))->row();
        $inclusive_filter = $post->inclusive_filter;

        $excluded_employees = array_map(function ($list) {
            return $list->emp_id;
        }, $this->db->select("emp_id")
            ->get("gcctimeutility.timesheet_excluded_employees")
            ->result());

        $status_filter = $post->filter->status;

        $start = date("Y-m-d");
        $end = date("Y-m-d");
        $currentYear = intval(date('Y'));
        $currentMonth = intval(date('m'));

        $this->db->where("emp.employee_status", "Active");
        if (!empty($employees_filter)) {
            $this->db->where_in("emp.id", $employees_filter);
            if (!empty($excluded_employees)) {
                $this->db->where_not_in("emp.id", $excluded_employees);
            }
        } else {
            switch (intval($inclusive_filter)) {
                case 1:
                    if (!empty($excluded_employees)) {
                        $this->db->where_not_in("emp.id", $excluded_employees);
                    }
                    break;
                case 2:
                    $this->db->where_in("emp.id", $excluded_employees);
                    break;
            }
        }

        if (empty($cut_off)) {
            $dates_arr = explode("/", $dates);
            $start = date("Y-m-d", strtotime($dates_arr[0]));
            $end = date("Y-m-d", strtotime($dates_arr[1]));
        } else {
            $cut_off_arr = explode("-", $cut_off);
            $startDate = intval($cut_off_arr[0]);
            $endDate = intval($cut_off_arr[1]);
            $month = $currentMonth;
            $year = $currentYear;

            if ($startDate > $endDate) {
                if ($currentMonth <= 1) {
                    $month = 12;
                    $year -= 1;
                } else {
                    $month -= 1;
                }
                $start = date("Y-m-d", strtotime("$year-$month-$startDate"));
                $end = date("Y-m-d", strtotime("$currentYear-$currentMonth-$endDate"));
            } else {
                $start = date("Y-m-d", strtotime("$currentYear-$currentMonth-$startDate"));
                $end = date("Y-m-d", strtotime("$currentYear-$currentMonth-$endDate"));
            }
        }

        $this->db->select("resource.shift_resource, emp.*, UCASE(CONCAT(emp.lastname,
                               CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''  END, ', ',
			                   emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
			                   AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END)) `employee_name`, personnel.biometric_id");
        $this->db->join("gcctimeutility.personnel personnel", "personnel.biometricno = emp.biometricno", "INNER");
        $this->db->join("gcctimeutility.shift_schedule_resource resource", "resource.shift_id = personnel.shift_id", "LEFT");
        $this->db->join("gcchris.tblcompanies companies", "companies.id = emp.company_id", "LEFT");

        if (!empty($company_id)) {
            $this->db->group_start();
            $this->db->where("companies.id", $company_id);
            $this->db->or_where_in("emp.company_id", array($company->description, $company->code));
            $this->db->group_end();
        }
        $this->db->order_by("emp.lastname", "asc");
        $employees = $this->db->get($this->tbl_employees . " emp")->result();
        $sql = $this->db->last_query();
        $timesheets = array();
        $this->db->reset_query();

        foreach ($employees as $employee) {
            $schedule_resource = unserialize($employee->shift_resource);
            $id = $employee->id;
            $employee_name = $employee->employee_name;
            $biometricno = $employee->biometric_id;
            $queryTimesheet = "SELECT '$employee_name' employee_name, ts.id AS tsID,
                    adddate('$start', numlist.id) as _date,
                    DATE_FORMAT(ADDDATE('$start', numlist.id), '%a') _weekday,
                    lcase(DATE_FORMAT(ADDDATE('$start', numlist.id), '%W')) _weekday_full,
                    $id _emp_id,
                    ts.*, '$biometricno' biometricno FROM
            (SELECT n1.i + n10.i*10 + n100.i*100 AS id
                FROM gcctimeutility.num n1 cross join gcctimeutility.num as n10 cross join gcctimeutility.num as n100) as numlist
            LEFT JOIN gcctimeutility.timesheet ts ON ts.date = adddate('$start', numlist.id) AND ts.emp_id = $id
            LEFT JOIN gccmaster.tblemployees emp ON emp.id = ts.emp_id
            where adddate('$start', numlist.id) <= '$end'";

            if (!empty($status_filter)) {
                switch ($status_filter) {
                    case "lacking":
                        $queryTimesheet .= " AND ts.scrub_status=1 AND ts.verified=0";
                        break;
                    case "multiple":
                        $queryTimesheet .= " AND ts.scrub_status=2";
                        break;
                    case "no-shift":
                        $queryTimesheet .= " AND ts.scrub_status=3";
                        break;
                    case "verified":
                        $queryTimesheet .= " AND ts.verified=1";
                        break;
                    case "unverified":
                        $queryTimesheet .= " AND ts.verified=0";
                        break;
                }
            }
            $employeeTimesheet = $this->db->query($queryTimesheet)->result();
            $dtr_count = array_filter($employeeTimesheet, function ($dtr) {
                return !empty($dtr->id);
            });

            $dtr_count_verified = array_filter($employeeTimesheet, function ($dtr) {
                return intval($dtr->verified) === 1;
            });

            $FORM_SORTBY = "";
            $ARRAY_SORTBY = array();
            $NEW_ARRAY_SORTBY = array();
            foreach ($employeeTimesheet as $timesheet) {
                $timesheet->has_TO = 0;
                $timesheet->has_LOA = 0;
                $timesheet->has_whole_day_LOA = 0;
                $timesheet->has_pending_adjustment = false;
                $timesheet->allow_paid_holiday = false;
                $timesheet->payroll_type = null;

                $timesheet->dtr_count = sizeof($dtr_count);
                $timesheet->dtr_count_verified = sizeof($dtr_count_verified);
                $timesheet->all_verified = (sizeof($dtr_count) === sizeof($dtr_count_verified)) && sizeof($dtr_count) > 0;

                $qTempEmployee = $this->db->get_where($this->tbl_employees, array("id"=>$timesheet->_emp_id));
                if($qTempEmployee->num_rows() == 1){
                    $timesheet->payroll_type = strtolower($qTempEmployee->row()->payroll_type);
                }
                $schedule_list = $this->db
                    ->where_in("id", $schedule_resource)->where("weekday", $timesheet->_weekday_full)
                    ->get($this->tbl_shift_schedule_list)->row();

                if (!empty($schedule_list)) {
                    $timesheet->has_shift = ($schedule_list->am_start === null && $schedule_list->am_end === null
                        && $schedule_list->pm_start === null && $schedule_list->pm_end === null) ? 0 : 1;
                }
                
                if ((is_null($timesheet->scrub_status) || intval($timesheet->scrub_status) === 1 || intval($timesheet->scrub_status) === 0) && $timesheet->has_shift === 1) {
                    $this->db->select("*, date_from AS F_TO, to.id AS toID ");
                    $this->db->from("gcceforms.travel_destination");
                    $this->db->join($this->tbl_TO_Destination." td", "td.travel_order_id = to.id");
                    $this->db->join($this->tbl_TO_Personnel." tp", "tp.travel_order_id = to.id");
                    $this->db->group_start();
                    $this->db->where("DATE(td.date_from) >=", $timesheet->_date);
                    $this->db->where("DATE(td.date_to) <=", $timesheet->_date);
                    $this->db->group_end();
                    $this->db->group_start();
                    $this->db->where("to.driver_id", $employee->id);
                    $this->db->or_where("tp.employee_id", $employee->id);
                    $this->db->group_end();
                    $timesheet->has_TO = $this->db->count_all_results($this->tbl_TO." to");
                    $this->db->select("*, `travel_destination`.`date_from` AS `F_TO`");
                    $this->db->from($this->tbl_TO_Destination);
                    $this->db->join($this->tbl_TO." to", $this->tbl_TO_Destination.".travel_order_id = to.id");
                    $this->db->join($this->tbl_TO_Destination." td", "td.travel_order_id = to.id");
                    $this->db->join($this->tbl_TO_Personnel." tp", "tp.travel_order_id =  to.id");
                    $this->db->group_start();
                    $this->db->where("DATE(td.date_from) >=", $timesheet->_date);
                    $this->db->where("DATE(td.date_to) <=", $timesheet->_date);
                    $this->db->group_end();
                    $this->db->group_start();
                    $this->db->where("to.driver_id", $employee->id);
                    $this->db->or_where("tp.employee_id", $employee->id);
                    $this->db->group_end();
                    $TO_sql = $this->db->get();
                    if($TO_sql->result()){
                        $row = $TO_sql->row();
                        $arrayTO =  array("TO",$row->F_TO);
                        $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayTO);
                    }else{
                        $arrayTO =  array("TO","0");
                        $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayTO);
                    }
                }else{
                    $arrayTO =  array("TO","0");
                    $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayTO);
                }

                if (intval($timesheet->with_adjustment) === 1) {
                    $adjustmentTemp = $this->db->get_where($this->tbl_time_adjustments, array("timesheet_id"=>$timesheet->id, "status"=>0));
                    if($adjustmentTemp->num_rows() > 0){
                        $timesheet->has_pending_adjustment = true;
                    }
                }

                $alteredShiftSchedule = $this->getCustomizedShiftScheduleByDate($timesheet->_date, $timesheet->_emp_id);
                if(isset($alteredShiftSchedule->has_shift)){
                    $timesheet->has_shift = $alteredShiftSchedule->has_shift;
                }

                if(isset($alteredShiftSchedule->schedule) && $alteredShiftSchedule->schedule){
                    foreach ($alteredShiftSchedule->schedule as $key => $value) {
                        $tempKey = "shift_{$key}";
                        if($value && $alteredShiftSchedule->has_shift == 1){ $timesheet->$tempKey = $value; }
                        if($alteredShiftSchedule->has_shift == 0){ $timesheet->$tempKey = $value; }
                    }
                }

                if ($timesheet->has_shift === 1) {
                    $this->db->where("DATE(date_from) <=", $timesheet->_date);
                    $this->db->where("DATE(date_to) >=", $timesheet->_date);
                    $this->db->where("employee", $employee->id);
                    $this->db->where("status", "Approved");

                    $loa = $this->db->get($this->tbl_loa)->result();
                    $timesheet->has_LOA = sizeof((array) $loa);
                    $ctr = !empty($loa) ? array_filter($loa, function ($_loa) {
                        return intval($_loa->type) === 3;
                    }) : [];
                    $timesheet->has_whole_day_LOA = sizeof($ctr) >= 1 ? 1 : 0;
                    $this->db->select("*, date_from AS F_LOA");
                    $this->db->from($this->tbl_loa);
                    $this->db->where("DATE(date_from) <=", $timesheet->_date);
                    $this->db->where("DATE(date_to) >=", $timesheet->_date);
                    $this->db->where("employee", $employee->id);
                    $this->db->where("status", "Approved");
                    $LOA_res = $this->db->get();
                    if ($loa = $LOA_res->result()) {
                        $row = $LOA_res->row();
                        $arrayLOA =  array("LOA",$row->F_LOA);
                        $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayLOA);
                    }else{
                        $arrayLOA =  array("LOA","0");
                        $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayLOA);
                    }
                }else{
                    $arrayLOA =  array("LOA","0");
                    $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayLOA);
                }
                $tempResponse = (object) $this->getCurrentDateIsHoliday($timesheet->_date);
                $timesheet->is_holiday = (isset($tempResponse->is_holiday) && $tempResponse->is_holiday == true)? 1: 0;
                $timesheet->payrate_id = (isset($tempResponse->is_holiday, $tempResponse->payrate_id) && ($tempResponse->is_holiday == true && $tempResponse->payrate_id))? $tempResponse->payrate_id: 0;
                $timesheet->paid_holiday = (isset($timesheet->paid_holiday) && $timesheet->paid_holiday == null)? 0: intval($timesheet->paid_holiday);
                if(isset($tempResponse->classification) && strtolower($tempResponse->classification) == "special non-working holiday"){
                    if($timesheet->payroll_type == "monthly"){ $timesheet->allow_paid_holiday = true; }
                }
                if(isset($tempResponse->classification) && strtolower($tempResponse->classification) == "regular holiday"){
                    if($timesheet->payroll_type == "monthly" || $timesheet->payroll_type == "daily" || $timesheet->payroll_type == "project based"){
                        $timesheet->allow_paid_holiday = true;
                    }
                }
                $this->db->select("*, date_from AS F_OT");
                $this->db->from( $this->tbl_overtime);
                $this->db->where("approved_by > 0");
                $this->db->where("DATE(date_from)", $timesheet->_date);
                $this->db->where("employee", $employee->id);
                $query = $this->db->get();
                if($query->result()){
                    $row = $query->row();
                    $arrayOT =  array("OT",$row->F_OT);
                    $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayOT);
                }else{
                    $arrayOT =  array("OT","0");
                    $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayOT);
                }

                $timesheet->datelist =  $ARRAY_SORTBY;
                usort($ARRAY_SORTBY, function ($one, $two) {
                    $datetime1 = strtotime($one[1]);
                    $datetime2 = strtotime($two[1]);
                    return $datetime1 - $datetime2;
                });

                $timesheet->datelist =  $ARRAY_SORTBY;
                array_push($timesheets, $timesheet);
                $FORM_SORTBY="";
                unset($ARRAY_SORTBY);
                $ARRAY_SORTBY = array();
            }
            if (!empty($status_filter)) {
                switch ($status_filter) {
                    case "completed":
                        $timesheets = array_filter($timesheets, function ($timesheet) {
                            return $timesheet->all_verified === TRUE;
                        });
                        break;
                    case "incomplete":
                        $timesheets = array_filter($timesheets, function ($timesheet) {
                            return $timesheet->all_verified === FALSE;
                        });
                        break;
                }
            }
        }

        return array("data" => $timesheets, "sql" => null);
    }

    function date_compare($element1, $element2) {
        $datetime1 = strtotime($element1[1]);
        $datetime2 = strtotime($element2[1]);
        return $datetime1 - $datetime2;
    }

    function hasApprovedTravelOrder($date=null, $employee_id=null, $dataOnly=false){
        $response = false;
        if($date && $employee_id){
            $this->db->select("*, `travel_destination`.`date_from` AS `F_TO`");
            $this->db->from($this->tbl_TO_Destination);
            $this->db->join($this->tbl_TO." to", $this->tbl_TO_Destination.".travel_order_id = to.id");
            $this->db->join($this->tbl_TO_Destination." td", "td.travel_order_id = to.id");
            $this->db->join($this->tbl_TO_Personnel." tp", "tp.travel_order_id =  to.id");
            $this->db->group_start();
            $this->db->where("DATE(td.date_from) >=", $date);
            $this->db->where("DATE(td.date_to) <=", $date);
            $this->db->group_end();
            $this->db->group_start();
            $this->db->where("to.driver_id", $employee_id);
            $this->db->or_where("tp.employee_id", $employee_id);
            $this->db->group_end();
            $this->db->order_by("to.id", "DESC");
            $this->db->limit(1);
            $query = $this->db->get();
            $response = $query->num_rows() === 1;
        }
        return $dataOnly ? $query->row() : $response;
    }

    public function getTimesheetRow($timesheet_id, $param_emp_id = null){
        $timesheet = new stdClass();
        if($timesheet_id){
            if (!empty($param_emp_id)) {
                $emp_id = $param_emp_id;
            } else {
                $post = $this->arrayToStdClass($this->input->post());
                $emp_id = $post->emp_id;
            }

            $ARRAY_SORTBY = array();

            $this->db->select("resource.shift_resource, emp.*, CONCAT(emp.lastname,
                                   CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''  END, ', ',
                                   emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                   AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END) `employee_name`, personnel.biometric_id");
            $this->db->join("gcctimeutility.personnel personnel", "personnel.biometricno = emp.biometricno", "INNER");
            $this->db->join("gcctimeutility.shift_schedule_resource resource", "resource.shift_id = personnel.shift_id", "INNER");
            $this->db->join("gcchris.tblcompanies companies", "companies.id = emp.company_id", "LEFT");
            $this->db->order_by("emp.lastname", "asc");
            $this->db->where("emp.id", $emp_id);
            $qTempEmployee = $this->db->get($this->tbl_employees . " emp");
            if($qTempEmployee->num_rows() == 1){
                $employee = $qTempEmployee->row();
                $schedule_resource = unserialize($employee->shift_resource);
                $id = $employee->id;
                $employee_name = $employee->employee_name;
                $biometricno = $employee->biometric_id;

                $timesheet = $this->db
                    ->select("'$employee_name' employee_name,
                                ts.date as _date,
                                DATE_FORMAT(ts.date, '%a') _weekday,
                                lcase(DATE_FORMAT(ts.date, '%W')) _weekday_full,
                                $id _emp_id,
                                ts.*, '$biometricno' biometricno", false)
                    ->get_where($this->tbl_timesheet . " ts", array("ts.id" => $timesheet_id))
                    ->row();

                $this->db->from("payroll.payroll_sheet");
                $this->db->where("posted", 1);
                $this->db->where("emp_id", $timesheet->_emp_id);
                $this->db->group_start();
                $this->db->where("DATE(date_start) <=", $timesheet->_date);
                $this->db->where("DATE(date_end) >=", $timesheet->_date);
                $this->db->group_end();
                $qTempPayroll = $this->db->get();
                $timesheet->is_posted = $qTempPayroll->num_rows() > 0;

                $dtr_count = 1;
                $dtr_count_verified = intval($timesheet->verified) === 1 ? 1 : 0;

                $timesheet->payroll_type = null;
                $qTempEmployee = $this->db->get_where($this->tbl_employees, array("id"=>$timesheet->_emp_id));
                if($qTempEmployee->num_rows() == 1){
                    $timesheet->payroll_type = strtolower($qTempEmployee->row()->payroll_type);
                }
                $timesheet->has_pending_adjustment = false;
                $timesheet->allow_paid_holiday = false;
                $timesheet->has_TO = 0;
                $timesheet->has_LOA = 0;
                $timesheet->has_whole_day_LOA = 0;

                $timesheet->datelist = array();

                $timesheet->dtr_count = $dtr_count;
                $timesheet->dtr_count_verified = $dtr_count_verified;
                $timesheet->all_verified = (intval($dtr_count) === intval($dtr_count_verified)) && intval($dtr_count) > 0;

                // fetch schedule list and add criteria with weekday = weekday of date from list
                $schedule_list = $this->db
                    ->where_in("id", $schedule_resource)->where("weekday", $timesheet->_weekday_full)
                    ->get($this->tbl_shift_schedule_list)->row();

                if (!empty($schedule_list)) {
                    $timesheet->has_shift = ($schedule_list->am_start === null && $schedule_list->am_end === null
                        && $schedule_list->pm_start === null && $schedule_list->pm_end === null) ? 0 : 1;
                }

                if (intval($timesheet->with_adjustment) === 1) {
                    $adjustmentTemp = $this->db->get_where($this->tbl_time_adjustments, array("timesheet_id"=>$timesheet->id, "status"=>0));
                    if($adjustmentTemp->num_rows() > 0){
                        $timesheet->has_pending_adjustment = true;
                    }
                }

                if ((is_null($timesheet->scrub_status) || intval($timesheet->scrub_status) === 1) || (intval($timesheet->scrub_status) === 0 && $timesheet->has_shift === 1)) {
                    /*** $this->db->where("DATE(created_dt)", $timesheet->_date);
                    $this->db->where("driver_id", $employee->id);
                    $timesheet->has_TO = $this->db->count_all_results($this->tbl_TO); ***/
                    $this->db->select("*, date_from AS F_TO, to.id AS toID");
                    $this->db->from("gcceforms.travel_destination");
                    $this->db->join($this->tbl_TO_Destination." td", "td.travel_order_id = to.id");
                    $this->db->join($this->tbl_TO_Personnel." tp", "tp.travel_order_id = to.id");
                    $this->db->group_start();
                    $this->db->where("DATE(td.date_from) >=", $timesheet->_date);
                    $this->db->where("DATE(td.date_to) <=", $timesheet->_date);
                    $this->db->group_end();
                    $this->db->group_start();
                    $this->db->where("to.driver_id", $employee->id);
                    $this->db->or_where("tp.employee_id", $employee->id);
                    $this->db->group_end();
                    $timesheet->has_TO = $this->db->count_all_results($this->tbl_TO." to");
					
                    $this->db->select("*, `travel_destination`.`date_from` AS `F_TO`");
                    $this->db->from($this->tbl_TO_Destination);
                    $this->db->join($this->tbl_TO." to", $this->tbl_TO_Destination.".travel_order_id = to.id");
                    $this->db->join($this->tbl_TO_Destination." td", "td.travel_order_id = to.id");
                    $this->db->join($this->tbl_TO_Personnel." tp", "tp.travel_order_id =  to.id");
                    $this->db->group_start();
                    $this->db->where("DATE(td.date_from) >=", $timesheet->_date);
                    $this->db->where("DATE(td.date_to) <=", $timesheet->_date);
                    $this->db->group_end();
                    $this->db->group_start();
                    $this->db->where("to.driver_id", $employee->id);
                    $this->db->or_where("tp.employee_id", $employee->id);
                    $this->db->group_end();
                    $TO_sql = $this->db->get();
                    if($TO_sql->result()){
                        $row = $TO_sql->row();
                        $arrayTO =  array("TO",$row->F_TO);
                        $timesheet->datelist = array_push($ARRAY_SORTBY, $arrayTO);
                    }else{
                        $arrayTO =  array("TO","0");
                        $timesheet->datelist = array_push($ARRAY_SORTBY, $arrayTO);
                    }
                }else{
                    $arrayTO =  array("TO","0");
                    $timesheet->datelist = array_push($ARRAY_SORTBY, $arrayTO);
                }

                $alteredShiftSchedule = $this->getCustomizedShiftScheduleByDate($timesheet->_date, $timesheet->_emp_id);
                if(isset($alteredShiftSchedule->has_shift)){
                    $timesheet->has_shift = $alteredShiftSchedule->has_shift;
                }

                if(isset($alteredShiftSchedule->schedule) && $alteredShiftSchedule->schedule){
                    foreach ($alteredShiftSchedule->schedule as $key => $value) {
                        $tempKey = "shift_{$key}";
                        if($value && $alteredShiftSchedule->has_shift == 1){ $timesheet->$tempKey = $value; }
                        if($alteredShiftSchedule->has_shift == 0){ $timesheet->$tempKey = $value; }
                    }
                }

                if ($timesheet->has_shift === 1) {
                    $this->db->where("DATE(date_from) <=", $timesheet->_date);
                    $this->db->where("DATE(date_to) >=", $timesheet->_date);
                    $this->db->where("employee", $employee->id);
                    $this->db->where("status", "Approved");

                    $loa = $this->db->get($this->tbl_loa)->result();
                    $timesheet->has_LOA = sizeof((array) $loa);
                    $ctr = !empty($loa) ? array_filter($loa, function ($_loa) {
                        return intval($_loa->type) === 3;
                    }) : [];
                    $timesheet->has_whole_day_LOA = sizeof($ctr) >= 1 ? 1 : 0;

                    $this->db->select("*, date_from AS F_LOA");
                    $this->db->from($this->tbl_loa);
                    $this->db->where("DATE(date_from) <=", $timesheet->_date);
                    $this->db->where("DATE(date_to) >=", $timesheet->_date);
                    $this->db->where("employee", $employee->id);
                    $this->db->where("status", "Approved");
                    $LOA_res = $this->db->get();
                    if ($loa = $LOA_res->result()) {
                        $row = $LOA_res->row();
                        $arrayLOA =  array("LOA",$row->F_LOA);
                        $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayLOA);
                    }else{
                        $arrayLOA =  array("LOA","0");
                        $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayLOA);
                    }
                    $this->db->reset_query();
                }else{
                    $arrayLOA =  array("LOA","0");
                    $timesheet->datelist =  array_push($ARRAY_SORTBY, $arrayLOA);
                }

                $this->db->select("*, date_from AS F_OT");
                $this->db->from( $this->tbl_overtime);
                $this->db->where("approved_by > 0");
                $this->db->where("DATE(date_from)", $timesheet->_date);
                $this->db->where("employee", $employee->id);
                $query = $this->db->get();
                if($query->result()){
                    $row = $query->row();
                    $arrayOT =  array("OT",$row->F_OT);
                    $timesheet->datelist = array_push($ARRAY_SORTBY, $arrayOT);
                }else{
                    $arrayOT =  array("OT","0");
                    $timesheet->datelist = array_push($ARRAY_SORTBY, $arrayOT);
                }

                $timesheet->datelist =  $ARRAY_SORTBY;
                usort($ARRAY_SORTBY, function ($one, $two) {
                    $datetime1 = strtotime($one[1]);
                    $datetime2 = strtotime($two[1]);
                    return $datetime1 - $datetime2;
                });

                $timesheet->datelist =  $ARRAY_SORTBY;

                $tempResponse = (object) $this->getCurrentDateIsHoliday($timesheet->_date);
                $timesheet->is_holiday = (isset($tempResponse->is_holiday) && $tempResponse->is_holiday == true)? 1: 0;
                $timesheet->payrate_id = (isset($tempResponse->is_holiday, $tempResponse->payrate_id) && ($tempResponse->is_holiday == true && $tempResponse->payrate_id))? $tempResponse->payrate_id: 0;
                $timesheet->paid_holiday = (isset($timesheet->paid_holiday) && $timesheet->paid_holiday == null)? 0: intval($timesheet->paid_holiday);
                if(isset($tempResponse->classification) && strtolower($tempResponse->classification) == "special non-working holiday"){
                    if($timesheet->payroll_type == "monthly"){ $timesheet->allow_paid_holiday = true; }
                }
                if(isset($tempResponse->classification) && strtolower($tempResponse->classification) == "regular holiday"){

                    if($timesheet->payroll_type == "monthly" || $timesheet->payroll_type == "daily" || $timesheet->payroll_type == "project based"){
                        $timesheet->allow_paid_holiday = true;
                    }
                }
            }
        }

        return $timesheet;
    }

    public function getEmployeesForFilter($limit = 1, $hide_excluded = 0, $newly = 0)
    {
        $excluded_employees = array_map(function ($list) {
            return $list->emp_id;
        }, $this->db->select("emp_id")
            ->get("gcctimeutility.timesheet_excluded_employees")
            ->result());

        if (intval($hide_excluded) >= 1) {
            $this->db->where_not_in("emp.id", $excluded_employees);
        }

        if (intval($newly) === 1) {
            $this->db->where("emp.add_date >=", "DATE(NOW()) - INTERVAL 7 DAY", FALSE);
        }

        $q = $this->input->post("q") ? $this->input->post("q") : $this->input->get("q");

        $this->db->like("CONCAT(lastname, firstname, middlename, suffix)", $q, "BOTH");
        $this->db->select("emp.id, CONCAT('<div class=\"m--font-boldest\">',
                                    CONCAT(emp.lastname,
                                      CASE
                                          WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != '' AND emp.suffix IS NOT NULL
                                              THEN CONCAT(' ', emp.suffix)
                                          ELSE '' END, ', ',
                                      emp.firstname, ' ',
                                      CASE
                                          WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                              AND emp.middlename != '' AND emp.middlename IS NOT NULL
                                              THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                                          ELSE '' END),
                                    '</div>',
                                    '<div class=\"m--font-bolder m--regular-font-size-sm1 mt-2\">', IF(pos.id IS NULL, emp.position, pos.name) ,'</div>',
                                    '<div class=\"m--font-bolder m--regular-font-size-sm1\">', IF(comp.id IS NULL, emp.company_id, comp.description) ,'</div>') html,
                                    CONCAT('<span class=\"m--font-bolder\">',
                                        CONCAT(emp.lastname,
                                      CASE
                                          WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != '' AND emp.suffix IS NOT NULL
                                              THEN CONCAT(' ', emp.suffix)
                                          ELSE '' END, ', ',
                                      emp.firstname, ' ',
                                      CASE
                                          WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                              AND emp.middlename != '' AND emp.middlename IS NOT NULL
                                              THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                                          ELSE '' END),'</span>') text");
        $this->db->join("gcchris.tblcompanies comp", "comp.id = emp.company_id", "LEFT");
        $this->db->join("gcchris.tblposition pos", "pos.id = emp.position", "LEFT");
        $this->db->order_by("TRIM(emp.lastname)", "asc");

        if (intval($limit) === 1) {
            $this->db->limit(100);
        }

        return array("results" => $this->db->get($this->tbl_employees . " emp")->result(), "sql" => $this->db->last_query());
    }

    public function getTimesheetRecord($timesheet_id, $employee_id)
    {
        $post = $this->arrayToStdClass($this->input->post());
        $record = $this->db->get_where($this->tbl_timesheet, array("id" => $timesheet_id))->row();
        $weekday = strtolower(date('l', strtotime($post->date)));

        $employee = $this->db->select("emp.*, person.biometric_id")
            ->join("gcctimeutility.personnel person", "person.biometricno = emp.biometricno", "INNER")
            ->get_where($this->tbl_employees . " emp", array("emp.id" => $employee_id))
            ->row();

        $attendance_params = $this->db->get_where($this->tbl_time_parameters, array("param_name" => "TS_OT_PARAMS"))->row();
        $att_end_day = new DateTime($post->date . " " . $attendance_params->start_time);
        $att_end_day->modify("+1 day");

        $att_start_day = new DateTime($post->date . " " . $attendance_params->end_time);

        $attendance = !empty($employee) ?
            $this->db->order_by("datetime", "ASC")
                ->get_where($this->tbl_attendance,
                    array(
                        "biometric_id" => $employee->biometric_id,
                        "`datetime` >=" => $att_start_day->format("Y-m-d H:i"),
                        "`datetime` <=" => $att_end_day->format("Y-m-d H:i"),
                    )
                )
                ->result() : null;

        $to = array();
        $loa = array();

        if (!empty($post->has_TO)) {
            $to = $this->getEmployeeDetailedTravelOrderForSelect($employee_id, $post->date);
        }

        if (!empty($post->has_LOA)) {
            $loa = $this->getEmployeeDetailedLoaForSelect($employee_id, $post->date);
        }

        $overtime = $this->db->select("ts_ot.*, ot.reference_no, ot.purpose, ot.date_from, ot.date_to, CONCAT(emp.lastname,
                                      CASE
                                          WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != '' AND emp.suffix IS NOT NULL
                                              THEN CONCAT(' ', emp.suffix)
                                          ELSE '' END, ', ',
                                      emp.firstname, ' ', CASE
                                                              WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                                                  AND emp.middlename != '' AND emp.middlename IS NOT NULL
                                                                  THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                                                              ELSE '' END)                  `requestor`")
            ->join($this->tbl_timesheet . " ts", "ts.id = ts_ot.timesheet_id")
            ->join($this->tbl_overtime . " ot", "ot.id = ts_ot.overtime_id")
            ->join($this->tbl_employees . " emp", "emp.id = ot.requested_by")
            ->where("ts_ot.timesheet_id", $timesheet_id)
            ->where("ts.has_overtime", 1)
            ->where("ot.status", "Approved")
            ->get($this->tbl_timesheet_overtime . " ts_ot")
            ->result();

        $holiday_references = array();
        $temp_holiday = $this->db
            ->where(array(
                "DATE(start_date) <=" => $post->date,
                "DATE(end_date) >=" => $post->date,
            ))
            ->get($this->tbl_tblholidays);

        if($temp_holiday->num_rows() == 1){
            $holiday_references = $temp_holiday->row();
        }

        $schedule = array();

        if (empty($timesheet_id) || $timesheet_id === "null") {
            $shift_resource = $this->db->select("resource.*")
                ->join($this->tbl_personnel . " personnel", "personnel.shift_id = resource.shift_id", "INNER")
                ->join($this->tbl_employees . " emp", "emp.biometricno = personnel.biometricno", "INNER")
                ->where("emp.id", $employee_id)
                ->get($this->tbl_shift_schedule_resource . " resource")
                ->row("shift_resource");

            if (!empty($shift_resource)) {
                $schedule = $this->db->select("am_start shift_am_start, am_end shift_am_end, pm_start shift_pm_start, pm_end shift_pm_end")
                    ->where_in("id", unserialize($shift_resource))
                    ->where("weekday", $weekday)
                    ->get($this->tbl_shift_schedule_list)
                    ->row_array();
            }
        } else {
            $schedule = array(
                "shift_am_start" => $record->shift_am_start,
                "shift_am_end" => $record->shift_am_end,
                "shift_pm_start" => $record->shift_pm_start,
                "shift_pm_end" => $record->shift_pm_end,
            );
        }

        $_schedule = array();
        $alteredShiftSchedule = $this->getCustomizedShiftScheduleByDate($post->date, $employee_id);
        if(isset($alteredShiftSchedule->schedule)){
            $shift_schedule_row = new stdClass();
            $tempProps = array("shift_am_start"=>"am_start", "shift_am_end"=>"am_end", "shift_pm_in"=>"pm_in", "shift_pm_end"=>"pm_end");
            foreach ($alteredShiftSchedule->schedule as $key => $value) {
                $tempKey = "shift_{$key}";
                if($value && $alteredShiftSchedule->has_shift == 1){
                    $_schedule[$tempKey] = $value;
                    $schedule[$tempKey] = $value;
                }
                if($alteredShiftSchedule->has_shift == 0){
                    $_schedule[$tempKey] = $value;
                    $schedule[$tempKey] = $value;
                }
            }
        }

        return array(
            "record" => $record,
            "attendance" => $attendance,
            "to" => $to,
            "loa" => $loa,
            "schedule" => $schedule,
            "altered_schedule" => $_schedule,
            "overtime" => $overtime,
            "holiday_references" => $holiday_references,
        );
    }

    public function saveTimeAdjustmentRequest($timesheet_id)
    {
        if (intval($timesheet_id) > 0) {
            return $this->createUpdateTimeAdjustmentRequest($timesheet_id);
        } else {
            return $this->createNewEntryTimeAdjustmentRequest();
        }
    }

    private function createUpdateTimeAdjustmentRequest($timesheet_id)
    {
        $this->db->db_debug = false;
        $post = $this->arrayToStdClass($this->input->post());
        $props = ["am_in", "am_out", "pm_in", "pm_out"];
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $weekday = strtolower(date("l", strtotime($post->date)));
        $loa = $post->loa;
        $to = $post->travel_order;
        $overtimeUpdates = isset($post->overtimeUpdates) ? $post->overtimeUpdates : array();

        $manualOvertime = new stdClass();
        if(isset($post->manualOvertime) && $post->manualOvertime){
            $manualOvertime = $post->manualOvertime;
            unset($post->manualOvertime);
        }

        $this->db->trans_begin();

        $main_tbl_data = array(
            "timesheet_id" => $timesheet_id,
            "remarks" => $post->remarks,
            "adjustment_override" => $post->adjustment_override,
            "created_by" => $logged_in_user_emp_id,
            "with_shift_adjustment" => intval($post->has_shift) === 0
        );

        $timeAdjustmentSaved = $this->db->insert($this->tbl_time_adjustments, $main_tbl_data);
        $time_adjustments_id = $this->db->insert_id();

        $timeAdjustments = array();
        foreach ($props as $prop) {
            if (intval($post->$prop->modified) === 1) {
                $timeAdjustment = array(
                    "time_adjustments_id" => $time_adjustments_id,
                    "field" => $prop,
                    "value" => !empty($post->$prop->value) ? date("H:i", strtotime($post->$prop->value)) : null,
                    "prev_value" => !empty($post->$prop->prev_value) ? date("H:i", strtotime($post->$prop->prev_value)) : null,
                    "is_manual" => $post->$prop->manual,
                    "created_by" => $logged_in_user_emp_id,
                );

                array_push($timeAdjustments, $timeAdjustment);
            }
        }

        $this->db->insert_batch($this->tbl_time_adjustments_meta, $timeAdjustments);

        // has_shift values, 1 = yes, 0 = no (if has_shift=0 then save shift schedule adjustment request)
        if (intval($post->has_shift) <= 0) {
            $shift_schedule = array(
                "time_adjustments_id" => $time_adjustments_id,
                "am_start" => empty($post->shifts->am_start) ? NULL : $post->shifts->am_start,
                "am_end" => empty($post->shifts->am_end) ? NULL : $post->shifts->am_end,
                "pm_start" => empty($post->shifts->pm_start) ? NULL : $post->shifts->pm_start,
                "pm_end" => empty($post->shifts->pm_end) ? NULL : $post->shifts->pm_end,
                "created_by" => $logged_in_user_emp_id,
            );

            if ((!empty($post->shifts->am_start) && !empty($post->shifts->am_end))
                || (!empty($post->shifts->pm_start) && !empty($post->shifts->pm_end))) {
                $this->db->insert($this->tbl_time_adjustments_shift_schedule, $shift_schedule);
            }
        }

        if (!empty($loa)) {
            foreach ($loa as $_loa) {
                $this->db->insert($this->tbl_time_adjustments_eform_refs, array(
                    "time_adjustments_id" => $time_adjustments_id,
                    "eform_table" => "gcceforms.loa",
                    "eform_ids" => $_loa,
                    "created_by" => $logged_in_user_emp_id,
                ));
            }
        }

        if (!empty($to)) {
            foreach ($to as $_to) {
                $this->db->insert($this->tbl_time_adjustments_eform_refs, array(
                    "time_adjustments_id" => $time_adjustments_id,
                    "eform_table" => "gcceforms.travel_order",
                    "eform_ids" => $_to,
                    "created_by" => $logged_in_user_emp_id,
                ));
            }
        }

        if(isset($manualOvertime) && count(get_object_vars($manualOvertime)) > 0 && $time_adjustments_id){
            if(isset($manualOvertime->overtime_requested_by, $manualOvertime->overtime_in, $manualOvertime->overtime_out) &&
            ($manualOvertime->overtime_in && $manualOvertime->overtime_out && $manualOvertime->overtime_requested_by)){
                $checkManualOvertime = $this->db->get_where($this->tbl_time_adjustments_manual_overtime, array("time_adjustments_id"=>$time_adjustments_id));
                if($checkManualOvertime->num_rows() == 0){
                    $tempOTData = new stdClass();
                    $tempOTData->overtime_in = date("Y-m-d H:i:s", strtotime($manualOvertime->overtime_in));
                    $tempOTData->overtime_out = date("Y-m-d H:i:s", strtotime($manualOvertime->overtime_out));
                    $tempOTData->regular_hrs = $manualOvertime->overtime_reg_hrs ? $manualOvertime->overtime_reg_hrs : 0;
                    $tempOTData->ndiff_hrs = $manualOvertime->overtime_ndiff_hrs ? $manualOvertime->overtime_ndiff_hrs : 0;
                    $tempOTData->requested_by = $manualOvertime->overtime_requested_by ? $manualOvertime->overtime_requested_by : 0;
                    $tempOTData->purpose = $manualOvertime->overtime_purpose ? $manualOvertime->overtime_purpose : "";
                    $tempOTData->created_by = $logged_in_user_emp_id;
                    $tempOTData->created_at = date("Y-m-d H:i:s");
                    $tempOTData->time_adjustments_id = $time_adjustments_id;

                    $this->db->insert($this->tbl_time_adjustments_manual_overtime, $tempOTData);
                }
            }
        }

        if (!empty($overtimeUpdates)) {
            foreach ($overtimeUpdates as $overtimeUpdate) {
                $this->db->insert($this->tbl_time_adjustments_overtime, array(
                    "time_adjustments_id" => $time_adjustments_id,
                    "ts_overtime_id" => $overtimeUpdate->id,
                    "value" => $overtimeUpdate->value,
                    "prev_value" => $overtimeUpdate->prev_val,
                    "n_diff_value" => $overtimeUpdate->n_diff_value,
                    "n_diff_prev_value" => $overtimeUpdate->n_diff_prev_value,
                    "ot_in_value" => $overtimeUpdate->ot_in_value,
                    "ot_in_prev_value" => $overtimeUpdate->ot_in_prev_value,
                    "ot_out_value" => $overtimeUpdate->ot_out_value,
                    "ot_out_prev_value" => $overtimeUpdate->ot_out_prev_value,
                    "created_by" => $logged_in_user_emp_id,
                    "created_at" => date("Y-m-d H:i:s"),
                ));
            }
        }

        if ($timeAdjustmentSaved) {
            $this->db->set("with_adjustment", 1);
            $this->db->where("id", $timesheet_id);
            $this->db->update($this->tbl_timesheet);
            $this->db->reset_query();
        }

        $resultSet = array();
        $resultSet["data"] = array("timesheet_id" => $timesheet_id);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $resultSet["success"] = true;
            $resultSet["message"] = "Time adjustment request successfully saved.";
            $resultSet["title"] = "Request Saved";
            $resultSet["type"] = "update";
        } else {
            $this->db->trans_rollback();
            $resultSet["success"] = false;
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["title"] = "DB Error occurred.";
            $resultSet["type"] = "update";
        }

        // $resultSet["shift_schedule"] = $shift_schedule;
        $this->db->db_debug = $this->db_debug;
        return $resultSet;
    }

    private function createNewEntryTimeAdjustmentRequest()
    {
        $this->db->db_debug = false;
        $post = $this->arrayToStdClass($this->input->post());
        $props = ["am_in", "am_out", "pm_in", "pm_out"];
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $weekday = strtolower(date("l", strtotime($post->date)));
        $loa = $post->loa;
        $to = $post->travel_order;

        $manualOvertime = new stdClass();
        if(isset($post->manualOvertime) && $post->manualOvertime){
            $manualOvertime = $post->manualOvertime;
            unset($post->manualOvertime);
        }

        $this->db->trans_begin();

        $timesheetData = array(
            "emp_id" => $post->emp_id,
            "date" => $post->date,
            "weekday" => $weekday,
            "scrub_status" => 1,
            "comments" => "[System Generated]: Lacking entry detected.",
            "with_adjustment" => 1,
            "has_shift" => $post->has_shift,
            "is_manual" => 1,
            "manual_mode" => "adjustment",
            "manual_by" => $logged_in_user_emp_id
        );

        if (intval($post->has_shift) === 1) {
            $schedule_resource_obj = $this->db->select("personnel.*, resource.shift_resource")
                ->join($this->tbl_employees . " employees", "employees.biometricno = personnel.biometricno", "INNER")
                ->join($this->tbl_shift_schedule_resource . " resource", "resource.shift_id = personnel.shift_id", "INNER")
                ->where("employees.id", $post->emp_id)
                ->get($this->tbl_personnel . " personnel")
                ->row();
            $schedule_resource = unserialize($schedule_resource_obj->shift_resource);
            $schedule_list = $this->db->where_in("id", $schedule_resource)
                ->where("weekday", $weekday)
                ->get($this->tbl_shift_schedule_list)
                ->row();
            $timesheetData["shift_am_start"] = $schedule_list->am_start;
            $timesheetData["shift_am_end"] = $schedule_list->am_end;
            $timesheetData["shift_pm_start"] = $schedule_list->pm_start;
            $timesheetData["shift_pm_end"] = $schedule_list->pm_end;
        }

        $this->db->insert($this->tbl_timesheet, $timesheetData);
        $timesheet_id = $this->db->insert_id();

        $main_tbl_data = array(
            "timesheet_id" => $timesheet_id,
            "entry_type" => "new",
            "remarks" => $post->remarks,
            "adjustment_override" => $post->adjustment_override,
            "created_by" => $logged_in_user_emp_id,
            "with_shift_adjustment" => intval($post->has_shift) === 0
        );

        $this->db->insert($this->tbl_time_adjustments, $main_tbl_data);
        $time_adjustments_id = $this->db->insert_id();

        $timeAdjustments = array();
        foreach ($props as $prop) {
            if (intval($post->$prop->modified) === 1) {
                $timeAdjustment = array(
                    "time_adjustments_id" => $time_adjustments_id,
                    "field" => $prop,
                    "value" => date("H:i", strtotime($post->$prop->value)),
                    "prev_value" => !empty($post->$prop->prev_value) ? date("H:i", strtotime($post->$prop->prev_value)) : null,
                    "is_manual" => $post->$prop->manual,
                    "created_by" => $logged_in_user_emp_id,
                );

                array_push($timeAdjustments, $timeAdjustment);
            }
        }

        $this->db->insert_batch($this->tbl_time_adjustments_meta, $timeAdjustments);

        if (intval($post->has_shift) <= 0) {
            $shift_schedule = array(
                "time_adjustments_id" => $time_adjustments_id,
                "am_start" => $post->shifts->am_start,
                "am_end" => $post->shifts->am_end,
                "pm_start" => $post->shifts->pm_start,
                "pm_end" => $post->shifts->pm_end,
                "created_by" => $logged_in_user_emp_id,
            );
            $this->db->insert($this->tbl_time_adjustments_shift_schedule, $shift_schedule);
        }

        if (!empty($loa)) {
            foreach ($loa as $_loa) {
                $this->db->insert($this->tbl_time_adjustments_eform_refs, array(
                    "time_adjustments_id" => $time_adjustments_id,
                    "eform_table" => "gcceforms.loa",
                    "eform_ids" => $_loa,
                    "created_by" => $logged_in_user_emp_id,
                ));
            }
        }

        if (!empty($to)) {
            foreach ($to as $_to) {
                $this->db->insert($this->tbl_time_adjustments_eform_refs, array(
                    "time_adjustments_id" => $time_adjustments_id,
                    "eform_table" => "gcceforms.travel_order",
                    "eform_ids" => $_to,
                    "created_by" => $logged_in_user_emp_id,
                ));
            }
        }

        if(isset($manualOvertime) && count(get_object_vars($manualOvertime)) > 0 && $time_adjustments_id){
            if(isset($manualOvertime->overtime_requested_by, $manualOvertime->overtime_in, $manualOvertime->overtime_out) &&
            ($manualOvertime->overtime_in && $manualOvertime->overtime_out && $manualOvertime->overtime_requested_by)){
                $tempOTData = new stdClass();
                $tempOTData->overtime_in = date("Y-m-d H:i:s", strtotime($manualOvertime->overtime_in));
                $tempOTData->overtime_out = date("Y-m-d H:i:s", strtotime($manualOvertime->overtime_out));
                $tempOTData->regular_hrs = $manualOvertime->overtime_reg_hrs ? $manualOvertime->overtime_reg_hrs : 0;
                $tempOTData->ndiff_hrs = $manualOvertime->overtime_ndiff_hrs ? $manualOvertime->overtime_ndiff_hrs : 0;
                $tempOTData->requested_by = $manualOvertime->overtime_requested_by ? $manualOvertime->overtime_requested_by : 0;
                $tempOTData->purpose = $manualOvertime->overtime_purpose ? $manualOvertime->overtime_purpose : "";
                $tempOTData->created_by = $logged_in_user_emp_id;
                $tempOTData->created_at = date("Y-m-d H:i:s");
                $tempOTData->time_adjustments_id = $time_adjustments_id;

                $this->db->insert($this->tbl_time_adjustments_manual_overtime, $tempOTData);
            }
        }

        $resultSet = array();
        $resultSet["data"] = array("timesheet_id" => $timesheet_id);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $resultSet["success"] = true;
            $resultSet["message"] = "Time adjustment request successfully saved.";
            $resultSet["title"] = "Request Saved";
            $resultSet["type"] = "new";
        } else {
            $this->db->trans_rollback();
            $resultSet["success"] = false;
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["title"] = "DB Error occurred.";
            $resultSet["type"] = "";
        }

        $this->db->db_debug = $this->db_debug;
        return $resultSet;
    }

    public function verifySelectedTimeRecords()
    {
        $resultSet = array();
        $this->db->trans_begin();

        $this->db->db_debug = false;
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $post = $this->arrayToStdClass($this->input->post());

        if(isset($post->id) && is_array($post->id) && count($post->id) > 0){
            $emp_ids = $this->db->select("emp_id")
            ->where_in("id", $post->id)
            ->group_by("emp_id")
            ->get($this->tbl_timesheet)
            ->result();

            $emp_holidays = $this->db->select("id, is_holiday")
                ->where_in("id", $post->id)
                ->get($this->tbl_timesheet)
                ->result();

            $this->db->reset_query();
            
            $verifiedEmpIds = array();
            $tempHolidays = array();

            $cut_off = $post->filter->cut_off;
            $dates = $post->filter->dates;

            $start = date("Y-m-d");
            $end = date("Y-m-d");
            $currentYear = intval(date('Y'));
            $currentMonth = intval(date('m'));


            if (empty($cut_off)) {
                $dates_arr = explode("/", $dates);
                $start = date("Y-m-d", strtotime($dates_arr[0]));
                $end = date("Y-m-d", strtotime($dates_arr[1]));
            } else {
                $cut_off_arr = explode("-", $cut_off);
                $startDate = intval($cut_off_arr[0]);
                $endDate = intval($cut_off_arr[1]);
                $month = $currentMonth;
                $year = $currentYear;

                if ($startDate > $endDate) {
                    if ($currentMonth <= 1) {
                        $month = 12;
                        $year -= 1;
                    } else {
                        $month -= 1;
                    }
                    $start = date("Y-m-d", strtotime("$year-$month-$startDate"));
                    $end = date("Y-m-d", strtotime("$currentYear-$currentMonth-$endDate"));
                } else {
                    $start = date("Y-m-d", strtotime("$currentYear-$currentMonth-$startDate"));
                    $end = date("Y-m-d", strtotime("$currentYear-$currentMonth-$endDate"));
                }
            }

            $this->db->where_in("id", $post->id);
            $this->db->set("verified", 1);
            $this->db->set("verified_by", $logged_in_user_emp_id);
            $this->db->set("verified_at", $this->today->format("Y-m-d H:i:s"));
            $this->db->update($this->tbl_timesheet);
            $this->db->reset_query();

            foreach ($emp_holidays as $index => $row) {
                $tempHolidays[$row->id] = $row->is_holiday;
            }

            foreach ($emp_ids as $index => $row) {
                $timesheets = $this->db
                    ->where(
                        array(
                            "`date` >=" => $start,
                            "`date` <=" => $end,
                            "emp_id" => $row->emp_id
                        )
                    )
                    ->get($this->tbl_timesheet)
                    ->result();

                $verified = array_filter($timesheets, function ($timesheet) {
                    return intval($timesheet->verified) === 1;
                });

                if (sizeof($verified) === sizeof((array) $timesheets) && sizeof($timesheets) > 0) {
                    array_push($verifiedEmpIds, $row->emp_id);
                }
            }
            
            $resultSet["data"] = array(
                "verifiedEmpIds" => $verifiedEmpIds,
                "verifiedIsHolidays"=>$tempHolidays
            );
            
            if ($this->db->trans_status() === TRUE) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Selected time records was verified successfully.";
                $resultSet["title"] = "Successfully Verified";
                $this->db->trans_commit();
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "DB Error occurred.";
                $this->db->trans_rollback();
            }
        }else{
            $resultSet["success"] = false;
            $resultSet["message"] = "No time record(s) to verify!";
            $resultSet["title"] = "No time sheet data";
        }
        
        $this->db->db_debug = $this->db_debug;
        return $resultSet;
    }

    protected function getTimeAdjustmentRequestData($employee_id, $timesheet_id) {
        if($employee_id && $timesheet_id) {
            $this->db->select("timesheet.emp_id AS t_empID, timesheet.id AS tID,
            mn.id time_adjustment_id,
            mn.timesheet_id,
            mn.entry_type,
            mn.with_shift_adjustment,
            mn.`status`,
            mn.remarks,
            mn.confirmed_at,
            mn.created_at,
            (SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) as adj_am_in,
            (SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) as adj_am_out,
            (SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) as adj_pm_in,
            (SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) as adj_pm_out,
            (SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) as test_am_in,
            COALESCE(
            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id),
                IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.am_in)),
            timesheet.am_in) am_in,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) am_in_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) IS NULL, 0, 1) am_in_is_requested,
            COALESCE(
            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id),
                IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.am_out)),
            timesheet.am_out) am_out,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) am_out_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) IS NULL, 0, 1) am_out_is_requested,
            COALESCE(
            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id),
                IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.pm_in)),
            timesheet.pm_in) pm_in,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) pm_in_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) IS NULL, 0, 1) pm_in_is_requested,
            COALESCE(
            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id),
            IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.pm_out)),
            timesheet.pm_out) pm_out,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) pm_out_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) IS NULL, 0, 1) pm_out_is_requested,
            IF(timesheet.has_shift = 1, timesheet.shift_am_start, shift.am_start) am_start,
            IF(timesheet.has_shift = 1, timesheet.shift_am_end, shift.am_end) am_end,
            IF(timesheet.has_shift = 1, timesheet.shift_pm_start, shift.pm_start) pm_start,
            IF(timesheet.has_shift = 1, timesheet.shift_pm_end, shift.pm_end) pm_end,
            CONCAT(emp.lastname,
                CASE
                    WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != '' AND emp.suffix IS NOT NULL
                        THEN CONCAT(' ', emp.suffix)
                    ELSE '' END, ', ',
                emp.firstname, ' ', CASE
                    WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                        AND emp.middlename != '' AND emp.middlename IS NOT NULL
                        THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                    ELSE '' END) `employee_name`,
            CONCAT(emp2.lastname,
                CASE
                    WHEN emp2.suffix != 'N/A' AND emp2.suffix != 'NONE' AND emp2.suffix != '' AND emp2.suffix IS NOT NULL
                        THEN CONCAT(' ', emp2.suffix)
                    ELSE '' END, ', ',
                emp2.firstname, ' ', CASE
                    WHEN emp2.middlename != 'N/A' AND emp2.middlename != 'NONE'
                        AND emp2.middlename != '' AND emp2.middlename IS NOT NULL
                        THEN CONCAT(SUBSTR(emp2.middlename, 1, 1), '.')
                    ELSE '' END) `_created_by`,
            CONCAT(emp3.lastname,
                CASE
                    WHEN emp3.suffix != 'N/A' AND emp3.suffix != 'NONE' AND emp3.suffix != '' AND emp3.suffix IS NOT NULL
                        THEN CONCAT(' ', emp3.suffix)
                    ELSE '' END, ', ',
                emp3.firstname, ' ', CASE
                    WHEN emp3.middlename != 'N/A' AND emp3.middlename != 'NONE'
                        AND emp3.middlename != '' AND emp3.middlename IS NOT NULL
                        THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                    ELSE '' END) `_confirmed_by`,
            emp.id,
            timesheet.date,
            timesheet.weekday,
            timesheet.has_shift,
            time_adj_m_ot.overtime_in,
            time_adj_m_ot.overtime_out,
            time_adj_m_ot.regular_hrs as mn_ot_reg_hrs,
            time_adj_m_ot.ndiff_hrs as mn_ot_ndiff_hrs,
            time_adj_m_ot.requested_by");
            $this->db->join($this->tbl_time_adjustments_shift_schedule . " shift", "shift.time_adjustments_id = mn.id", "LEFT");
            $this->db->join($this->tbl_timesheet . " timesheet", "timesheet.id = mn.timesheet_id", "INNER");
            $this->db->join($this->tbl_employees . " emp", "emp.id = timesheet.emp_id", "INNER");
            $this->db->join($this->tbl_employees . " emp2", "emp2.id = mn.created_by", "LEFT");
            $this->db->join($this->tbl_employees . " emp3", "emp3.id = mn.confirmed_by", "LEFT");
            $this->db->join($this->tbl_tblcompanies . " companies", "companies.id = emp.company_id", "LEFT");
            $this->db->join($this->tbl_time_adjustments_manual_overtime . " time_adj_m_ot", "time_adj_m_ot.time_adjustments_id = mn.id", "LEFT");
            $this->db->where("emp.id", $employee_id);
            $this->db->where("mn.timesheet_id", $timesheet_id);
            $query = $this->db->get($this->tbl_time_adjustments . " mn");
            return $query->result();
        }else{
            return array();
        }
    }

    public function getTimeAdjustmentRequests($employee_id, $timesheet_id)
    {
        $post = $this->arrayToStdClass($this->input->post());
        $dtCfg = $this->mod_util->getDatatablesConfigForPagination($post);
        $status = isset($post->status) && !empty($post->status) ? $post->status : null;
        $employees = isset($post->employees) && !empty($post->employees) ? $post->employees : array();
        $company_id = isset($post->company) && !empty($post->company) ? $post->company : null;
        $date_range = isset($post->date) && !empty($post->date) ? $post->date : null;
        $requests = array();
        $sql = null;

        $company = $this->db->get_where("gcchris.tblcompanies", array("id" => $company_id))->row();


        $this->db->select("timesheet.emp_id AS t_empID, timesheet.id AS tID,
            mn.id time_adjustment_id,
            mn.timesheet_id,
            mn.entry_type,
            mn.with_shift_adjustment,
            mn.`status`,
            mn.remarks,
            mn.confirmed_at,
            mn.created_at,
            (SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) as adj_am_in,
            (SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) as adj_am_out,
            (SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) as adj_pm_in,
            (SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) as adj_pm_out,
            (SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) as test_am_in,
            COALESCE(
            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id),
                IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.am_in)),
            timesheet.am_in) am_in,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) am_in_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) IS NULL, 0, 1) am_in_is_requested,
            COALESCE(
            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id),
                IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.am_out)),
            timesheet.am_out) am_out,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) am_out_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) IS NULL, 0, 1) am_out_is_requested,
            COALESCE(
            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id),
                IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.pm_in)),
            timesheet.pm_in) pm_in,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) pm_in_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) IS NULL, 0, 1) pm_in_is_requested,
            COALESCE(
            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id),
            IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.pm_out)),
            timesheet.pm_out) pm_out,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) pm_out_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) IS NULL, 0, 1) pm_out_is_requested,
            IF(timesheet.has_shift = 1, timesheet.shift_am_start, shift.am_start) am_start,
            IF(timesheet.has_shift = 1, timesheet.shift_am_end, shift.am_end) am_end,
            IF(timesheet.has_shift = 1, timesheet.shift_pm_start, shift.pm_start) pm_start,
            IF(timesheet.has_shift = 1, timesheet.shift_pm_end, shift.pm_end) pm_end,
            CONCAT(emp.lastname,
                    CASE
                        WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != '' AND emp.suffix IS NOT NULL
                            THEN CONCAT(' ', emp.suffix)
                        ELSE '' END, ', ',
                    emp.firstname, ' ', CASE
                                            WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                                AND emp.middlename != '' AND emp.middlename IS NOT NULL
                                                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                                            ELSE '' END)                  `employee_name`,
            CONCAT(emp2.lastname,
                    CASE
                        WHEN emp2.suffix != 'N/A' AND emp2.suffix != 'NONE' AND emp2.suffix != '' AND emp2.suffix IS NOT NULL
                            THEN CONCAT(' ', emp2.suffix)
                        ELSE '' END, ', ',
                    emp2.firstname, ' ', CASE
                                            WHEN emp2.middlename != 'N/A' AND emp2.middlename != 'NONE'
                                                AND emp2.middlename != '' AND emp2.middlename IS NOT NULL
                                                THEN CONCAT(SUBSTR(emp2.middlename, 1, 1), '.')
                                            ELSE '' END)                  `_created_by`,
            CONCAT(emp3.lastname,
                    CASE
                        WHEN emp3.suffix != 'N/A' AND emp3.suffix != 'NONE' AND emp3.suffix != '' AND emp3.suffix IS NOT NULL
                            THEN CONCAT(' ', emp3.suffix)
                        ELSE '' END, ', ',
                    emp3.firstname, ' ', CASE
                                            WHEN emp3.middlename != 'N/A' AND emp3.middlename != 'NONE'
                                                AND emp3.middlename != '' AND emp3.middlename IS NOT NULL
                                                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                                            ELSE '' END)                  `_confirmed_by`,
            emp.id,
            timesheet.date,
            timesheet.weekday,
            timesheet.has_shift,
            time_adj_m_ot.overtime_in,
            time_adj_m_ot.overtime_out,
            time_adj_m_ot.regular_hrs as mn_ot_reg_hrs,
            time_adj_m_ot.ndiff_hrs as mn_ot_ndiff_hrs,
            time_adj_m_ot.requested_by");

        if (!empty($employees) || !empty($employee_id)) {
            if (!empty($employee_id)) {
                array_push($employees, $employee_id);
            }
            $this->db->where_in("emp.id", $employees);
        }

        if (!empty($date_range)) {
            $date_range_arr = explode("/", $date_range);
            $start = date("Y-m-d", strtotime($date_range_arr[0]));
            $end = date("Y-m-d", strtotime($date_range_arr[1]));
            $this->db->where("timesheet.date >=", $start);
            $this->db->where("timesheet.date <=", $end);
        }

        if (!empty($company_id)) {
            $this->db->group_start();
            $this->db->where("companies.id", $company_id);
            $this->db->or_where_in("emp.company_id", array($company->description, $company->code));
            $this->db->group_end();
        }

        if (!empty($timesheet_id)) {
            $this->db->where("mn.timesheet_id", $timesheet_id);
        }

        $this->db->join($this->tbl_time_adjustments_shift_schedule . " shift", "shift.time_adjustments_id = mn.id", "LEFT");
        $this->db->join($this->tbl_timesheet . " timesheet", "timesheet.id = mn.timesheet_id", "INNER");
        $this->db->join($this->tbl_employees . " emp", "emp.id = timesheet.emp_id", "INNER");
        $this->db->join($this->tbl_employees . " emp2", "emp2.id = mn.created_by", "LEFT");
        $this->db->join($this->tbl_employees . " emp3", "emp3.id = mn.confirmed_by", "LEFT");
        $this->db->join($this->tbl_tblcompanies . " companies", "companies.id = emp.company_id", "LEFT");
        $this->db->join($this->tbl_time_adjustments_manual_overtime . " time_adj_m_ot", "time_adj_m_ot.time_adjustments_id = mn.id", "LEFT");

        $this->db->group_by("mn.id");

        if (!empty($status)) {
            $this->db->where_in("mn.status", $post->status);
        }

        $this->db->order_by("employee_name, date", "asc");

        if (intval($dtCfg->length) >= 1) {
            $this->db->limit($dtCfg->length, $dtCfg->start);
        }

        $query = $this->db->get($this->tbl_time_adjustments . " mn");
        if($query->num_rows() > 0){
            $requests = $query->result();
            $sql = $this->db->last_query();
    
            foreach ($requests as $request) {
                $request->has_perhour = 0;
                $request->is_flex = 0;
    
                $alteredShiftSchedule = $this->getCustomizedShiftScheduleByDate($request->date, $request->t_empID);
                if(isset($alteredShiftSchedule->schedule)){
                    $shift_schedule_row = new stdClass();
                    $tempProps = array("am_start"=>"am_start", "am_end"=>"am_end", "pm_in"=>"pm_in", "pm_end"=>"pm_end");
                    foreach ($alteredShiftSchedule->schedule as $key => $value) {
                        if($value && $alteredShiftSchedule->has_shift == 1){
                            $request->$key = $value;
                        }
                        if($alteredShiftSchedule->has_shift == 0){
                            $request->$key = $value;
                        }
                    }
                }
    
                $this->db->join($this->tbl_loa . " loa", "loa.id = refs.eform_ids AND refs.time_adjustments_id = " . $request->time_adjustment_id . " AND refs.eform_table='gcceforms.loa'", "INNER");
                $request->loa = array_map(function ($loa) {
                    return $loa->reference_no;
                }, $this->db->get($this->tbl_time_adjustments_eform_refs . " refs")->result());
    
                $this->db->join($this->tbl_TO . " to", "to.id = refs.eform_ids AND refs.time_adjustments_id = " . $request->time_adjustment_id . " AND refs.eform_table='gcceforms.travel_order'", "INNER");
                $request->travel_order = array_map(function ($to) {
                    return $to->reference_no;
                }, $this->db->get($this->tbl_time_adjustments_eform_refs . " refs")->result());
    
    
                $has_overtime_request = $this->db->where("time_adjustments_id", $request->time_adjustment_id)
                    ->count_all_results($this->tbl_time_adjustments_overtime);
    
                $request->has_overtime_request = intval($has_overtime_request) >= 1;
                $request->has_manual_overtime = false;
    
                if (intval($has_overtime_request) >= 1) {
                    $overtime = $this->db
                        ->select("ts_ot.*, IF(adj_ot.id IS NULL, ts_ot.accredited_hrs ,adj_ot.value) adj_value,
                                      IF(adj_ot.id IS NULL, ts_ot.accredited_ndiff_hrs , adj_ot.n_diff_value) ndiff_adj_value")
                        ->join($this->tbl_timesheet . " ts", "ts.id = ts_ot.timesheet_id")
                        ->join($this->tbl_time_adjustments_overtime . " adj_ot", "ts_ot.id = adj_ot.ts_overtime_id", "LEFT")
                        ->join($this->tbl_overtime . " ot", "ot.id = ts_ot.overtime_id")
                        ->where("ts_ot.timesheet_id", $request->timesheet_id)
                        ->where("ts.has_overtime", 1)
                        ->where("ot.status", "Approved")
                        ->group_start()
                        ->where("adj_ot.time_adjustments_id", $request->time_adjustment_id)
                        ->or_where("adj_ot.id IS NULL", NULL, FALSE)
                        ->group_end()
                        ->order_by("ot.date_from", "ASC")
                        ->get($this->tbl_timesheet_overtime . " ts_ot")
                        ->result();
    
                    $request->overtime = $overtime;
                    $request->ot_adj_value = array_reduce($overtime, function ($acc, $next) {
                        return $acc + $next->adj_value;
                    }, 0);
    
                    $request->ot_original_value = array_reduce($overtime, function ($acc, $next) {
                        return $acc + $next->accredited_hrs;
                    }, 0);
    
                    $request->ot_ndiff_adj_value = array_reduce($overtime, function ($acc, $next) {
                        return $acc + $next->ndiff_adj_value;
                    }, 0);
    
                    $request->ot_ndiff_original_value = array_reduce($overtime, function ($acc, $next) {
                        return $acc + $next->accredited_ndiff_hrs;
                    }, 0);
    
                    if($request->overtime_in && $request->overtime_out && $request->requested_by){ $request->has_manual_overtime = true; }
                } else {
                    $overtime = $this->db
                        ->join($this->tbl_timesheet . " ts", "ts.id = ts_ot.timesheet_id")
                        ->join($this->tbl_overtime . " ot", "ot.id = ts_ot.overtime_id", "INNER")
                        ->where("ts_ot.timesheet_id", $request->timesheet_id)
                        ->where("ts.has_overtime", 1)
                        ->where("ot.status", "Approved")
                        ->order_by("ot.date_from", "ASC")
                        ->get($this->tbl_timesheet_overtime . " ts_ot")
                        ->result();
    
                    $total_ot_hrs = array_reduce($overtime, function ($acc, $next) {
                        return $acc + $next->accredited_hrs;
                    }, 0);
    
                    $ndiff_total_hrs = array_reduce($overtime, function ($acc, $next) {
                        return $acc + $next->accredited_ndiff_hrs;
                    }, 0);
    
                    $request->overtime = $overtime;
                    $request->ot_adj_value = $total_ot_hrs;
                    $request->ot_original_value = $total_ot_hrs;
                    $request->ot_ndiff_adj_value = $ndiff_total_hrs;
                    $request->ot_ndiff_original_value = $ndiff_total_hrs;
                    if($request->overtime_in && $request->overtime_out && $request->requested_by){ $request->has_manual_overtime = true; }
                }
    
                $this->db->select("tblper.is_flexi");
                $this->db->from($this->tbl_personnel. " tblper");
                $this->db->join($this->tbl_employees . " tblemp","tblemp.biometricno = tblper.biometricno");
                $this->db->where("tblemp.id", $request->t_empID);
                $query_emp = $this->db->get();
                if($query_emp->num_rows() == 1){
                    $prow = $query_emp->row();
                    $request->is_flex = intval($prow->is_flexi);
                }
    
                $this->db->from($this->tbl_timesheet." tbl_she");
                $this->db->join($this->tbl_employees." tbl_emp", "tbl_emp.id=tbl_she.emp_id");
                $this->db->where("tbl_she.id", $request->tID);
                $this->db->group_start();
                $this->db->where("tbl_emp.payroll_type ", "hourly");
                $this->db->or_where("tbl_emp.work_status", "part-time");
                $this->db->group_end();
                $querysh = $this->db->get();
                if($querysh->num_rows() == 1){ $request->has_perhour = 1; }
    
                $this->db->select("adjustment_override");
                $this->db->from($this->tbl_time_adjustments);
                $this->db->where("id", $request->time_adjustment_id);
                $query_time = $this->db->get();
                if($query_time->result()){
                    $trow = $query_time->row();
                }
                $adjustment_override = $trow->adjustment_override;
                $request->adjustment_override = $adjustment_override;
            }
        }

        return array(
            "data" => $requests,
            "dtCfg" => $dtCfg,
            "post" => $post,
            "recordsFiltered" => $this->getTimeAdjustmentsCount($status, $employees, $company_id, $company, $date_range, $timesheet_id),
            "recordsTotal" => $this->getTimeAdjustmentsCount($status, $employees, $company_id, $company, $date_range, $timesheet_id),
            "sql" => $sql
        );
    }

    private function getTimeAdjustmentsCount($status = null, $employee_id, $company_id, $company, $date_range, $timesheet_id)
    {
        if (!empty($employee_id)) {
            $this->db->where_in("emp.id", $employee_id);
        }

        if (!empty($date_range)) {
            $date_range_arr = explode("/", $date_range);
            $start = date("Y-m-d", strtotime($date_range_arr[0]));
            $end = date("Y-m-d", strtotime($date_range_arr[1]));
            $this->db->where("timesheet.date >=", $start);
            $this->db->where("timesheet.date <=", $end);
        }

        if (!empty($company_id) && $company_id) {
            $this->db->group_start();
            $this->db->where("companies.id", $company_id);
            $this->db->or_where_in("emp.company_id", array($company->description, $company->code));
            $this->db->group_end();
        }

        if (!empty($timesheet_id)) {
            $this->db->where("mn.timesheet_id", $timesheet_id);
        }

        $this->db->select("mn.*");
        $this->db->join($this->tbl_time_adjustments_shift_schedule . " shift", "shift.time_adjustments_id = mn.id", "LEFT");
        $this->db->join($this->tbl_timesheet . " timesheet", "timesheet.id = mn.timesheet_id");
        $this->db->join($this->tbl_employees . " emp", "emp.id = timesheet.emp_id");
        $this->db->join($this->tbl_tblcompanies . " companies", "companies.id = emp.company_id OR companies.code = emp.company_id OR companies.description = emp.company_id");
        $this->db->group_by("mn.id");

        if (!empty($status)) {
            $this->db->where_in("mn.status", $status);
        }

        $this->db->order_by("employee_name, date", "asc");

        return $this->db->count_all_results($this->tbl_time_adjustments . " mn");
    }

    public function confirmTimeAdjustmentRequest()
    {
        $this->db->db_debug = false;
        $post = $this->arrayToStdClass($this->input->post());
        $id = explode(",", $post->id);
        $status = isset($post->status) && $post->status ? intval($post->status): 0;
        $remarks = !empty($post->confirmation_remarks) ? $post->confirmation_remarks : null;
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $resultSet = array();
        $this->db->trans_begin();

        foreach ($id as $_id) {
            if ($status === 1) {
                $total_accredited_ot_hrs = 0;
                $total_accredited_ndiff_ot_hrs = 0;
                $timesheet_id = 0;
                $allow_late_adjustment = false;

                $this->db->select("adj.timesheet_id, meta.*");
                $this->db->join($this->tbl_time_adjustments . " adj", "adj.id = meta.time_adjustments_id", "INNER");
                $this->db->where("adj.id", $_id);
                $time_adjustment_metas = $this->db->get($this->tbl_time_adjustments_meta . " meta")->result();

                foreach ($time_adjustment_metas as $meta) {
                    $where = array("id"=>$meta->timesheet_id, "verified"=>0);
                    $datax = array($meta->field=>$meta->value);
                    $updated = $this->db->update($this->tbl_timesheet, $datax, $where);
                }

                $this->db->reset_query();

                $qTempAdjustment = $this->db->get_where($this->tbl_time_adjustments, array("id" => $_id));
                if($qTempAdjustment->num_rows() == 1){
                    $timesheet_id = $qTempAdjustment->row()->timesheet_id;
                    $allow_late_adjustment = intval($qTempAdjustment->row()->adjustment_override) == 1;
                }


                $time_adjustments_shift_schedule = $this->db
                    ->get_where($this->tbl_time_adjustments_shift_schedule, array("time_adjustments_id" => $_id))
                    ->row();
                if (!empty($time_adjustments_shift_schedule)) {
                    $this->db->where("id", $timesheet_id);
                    $this->db->where("verified", 0);
                    $this->db->set("shift_am_start", $time_adjustments_shift_schedule->am_start);
                    $this->db->set("shift_am_end", $time_adjustments_shift_schedule->am_end);
                    $this->db->set("shift_pm_start", $time_adjustments_shift_schedule->pm_start);
                    $this->db->set("shift_pm_end", $time_adjustments_shift_schedule->pm_end);
                    $updatedTimesheet = $this->db->update($this->tbl_timesheet);
                    /*** if($updatedTimesheet && $this->db->affected_rows() > 0){} ***/
                }

                $this->db->reset_query();

                $timesheetCalculation = $this->calculateTimesheet($timesheet_id);
                $timesheetHourlyPartimer = $this->generatePerHourSlashPartimer($timesheet_id);
                
                /*** $this->db->where("id", $timesheet_id);
                 $this->db->update($this->tbl_timesheet, $timesheetCalculation);
                 $this->db->reset_query(); ***/
                 
                 $qData = $this->db->get_where($this->tbl_timesheet, array("id"=>$timesheet_id));
                 if($qData->num_rows() == 1){
                    $qRowData = $qData->row();
                    $toArray = (array) $timesheetHourlyPartimer;
                    if(is_array($toArray) && count($toArray) > 0){ $qRowData->is_tagged_hourly = true; }

                    $timesheetUpdates = $this->updateTimesheetShiftComputation($qRowData, $allow_late_adjustment);

                    $updatedTimesheet = array_merge((array) $timesheetCalculation, (array) $timesheetUpdates);
                    $toArray = (array) $timesheetHourlyPartimer;
                    if(is_array($toArray) && count($toArray) > 0){
                        $updatedTimesheet = array_merge($updatedTimesheet, $toArray);
                    }
                    $updatedTimesheet["verified"] = 1;
                    $updatedTimesheet["verified_by"] = $logged_in_user_emp_id;
                    $updatedTimesheet["verified_at"] = date("Y-m-d H:i:s");
                    $updatedTimesheet["with_adjustment"] = 1;
                    $updatedTimesheet["last_updated_by"] = $logged_in_user_emp_id;
                    $updatedTimesheet["last_updated_at"] = date("Y-m-d H:i:s");

                    $updated = $this->db->update($this->tbl_timesheet, $updatedTimesheet, array("id"=>$timesheet_id));
                    if($updated && $this->db->affected_rows() > 0){
                        $this->db->reset_query();
                    }
                }

                $this->db->query("UPDATE $this->tbl_timesheet_overtime ts_ot
                                        INNER JOIN $this->tbl_time_adjustments_overtime adj_ot ON adj_ot.ts_overtime_id = ts_ot.id
                                            SET ts_ot.accredited_hrs = adj_ot.value, ts_ot.accredited_ndiff_hrs = adj_ot.n_diff_value,
                                            ts_ot.overtime_in = adj_ot.ot_in_value, ts_ot.overtime_out = adj_ot.ot_out_value
                                        WHERE adj_ot.time_adjustments_id = $_id");

                $this->db->reset_query();

                $hasManualOvertime = false;
                $tempManualOvertime = new stdClass();
                $tempManualOvertime->overtime_in = null;
                $tempManualOvertime->overtime_out = null;
                $tempManualOvertime->has_overtime = 1;

                $tempTsOt = $this->db->get_where($this->tbl_timesheet_overtime, array("timesheet_id"=>$timesheet_id));
                if($tempTsOt->num_rows() == 0){
                    $this->db->select("a.*, c.emp_id as employee,
                        IFNULL(comp.description, emp.company_id) as company,
                        IFNULL(dept.description, emp.department_id) as department,
                        IFNULL(pos.name, emp.position) as position");
                    $this->db->from($this->tbl_time_adjustments_manual_overtime." a");
                    $this->db->join($this->tbl_time_adjustments." b", "b.id = a.time_adjustments_id");
                    $this->db->join($this->tbl_timesheet." c", "c.id = b.timesheet_id");
                    $this->db->join($this->tbl_employees." emp", "emp.id = c.emp_id");
                    $this->db->join($this->tbl_tblcompanies." comp", "comp.id = emp.company_id OR comp.description = emp.company_id OR comp.code = emp.company_id", "LEFT");
                    $this->db->join($this->tbl_tbldepartments." dept", "dept.id = emp.department_id OR dept.description = emp.department_id OR dept.code = emp.department_id", "LEFT");
                    $this->db->join($this->tbl_tblposition." pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
                    $this->db->where(array("a.time_adjustments_id"=>$_id, "a.ts_overtime_id"=>0));
                    $this->db->order_by("a.created_at", "DESC");
                    $this->db->limit(1);
                    $tsManualOT = $this->db->get();
                    if($tsManualOT->num_rows() == 1){
                        $tempMotRow = $tsManualOT->row();

                        $response = $this->generateOvertimeReferenceNumber();
                        $tempOTRecord = new StdClass();
                        $tempOTRecord->ref_yr = $response->year;
                        $tempOTRecord->ref_month = $response->month;
                        $tempOTRecord->ref_series = $response->series;
                        $tempOTRecord->reference_no = $response->reference_no;

                        $tempOTRecord->employee = $tempMotRow->employee;
                        $tempOTRecord->company = $tempMotRow->company;
                        $tempOTRecord->department = $tempMotRow->department;
                        $tempOTRecord->position = $tempMotRow->position;
                        $tempOTRecord->purpose = $tempMotRow->purpose;
                        $tempOTRecord->date_from = date("Y-m-d H:i:s", strtotime($tempMotRow->overtime_in));
                        $tempOTRecord->date_to = date("Y-m-d H:i:s", strtotime($tempMotRow->overtime_out));
                        $tempOTRecord->actual_time_start = date("Y-m-d H:i:s", strtotime($tempMotRow->overtime_in));
                        $tempOTRecord->actual_time_end = date("Y-m-d H:i:s", strtotime($tempMotRow->overtime_out));
                        $tempOTRecord->requested_by = $tempMotRow->requested_by;
                        $tempOTRecord->requested_at = date("Y-m-d H:i:s", strtotime($tempMotRow->overtime_in));

                        $tempRegularHours = ($tempMotRow->regular_hrs && floatval($tempMotRow->regular_hrs) > 0)? floatval($tempMotRow->regular_hrs): 0;
                        $tempNdiffHours = ($tempMotRow->ndiff_hrs && floatval($tempMotRow->ndiff_hrs) > 0)? floatval($tempMotRow->ndiff_hrs): 0;
                        $tempTotalActualTime = $tempRegularHours + $tempNdiffHours;

                        $tempOTRecord->actual_time_work = $tempTotalActualTime;
                        $tempOTRecord->created_by = $logged_in_user_emp_id;
                        $tempOTRecord->created_at = date("Y-m-d H:i:s");
                        $tempOTRecord->approved_by = $logged_in_user_emp_id;
                        $tempOTRecord->approved_at = date("Y-m-d H:i:s");
                        $tempOTRecord->status = "Approved";
                        $added = $this->db->insert($this->tbl_overtime, (array) $tempOTRecord);
                        if($added){
                            $tsOvertime = new stdClass();
                            $tsOvertime->timesheet_id = $timesheet_id;
                            $tsOvertime->overtime_id = $this->db->insert_id();
                            $tsOvertime->overtime_in = date("Y-m-d H:i:s", strtotime($tempMotRow->overtime_in));
                            $tsOvertime->overtime_out = date("Y-m-d H:i:s", strtotime($tempMotRow->overtime_out));
                            $tsOvertime->total_hrs = $tempRegularHours;
                            $tsOvertime->accredited_hrs = $tempRegularHours;
                            $tsOvertime->ndiff_hrs = $tempNdiffHours;
                            $tsOvertime->accredited_ndiff_hrs = $tempNdiffHours;

                            $tsOTAdded = $this->db->insert($this->tbl_timesheet_overtime, $tsOvertime);
                            if($tsOTAdded){
                                $this->db->update($this->tbl_timesheet,
                                    array(
                                        "has_overtime"=>1,
                                        "overtime_in"=>$tsOvertime->overtime_in,
                                        "overtime_out"=>$tsOvertime->overtime_out),
                                    array("id"=>$timesheet_id)
                                );
                            }
                        }
                    }
                }

                $hasOvertimeRecord = $this->db
                ->get_where($this->tbl_timesheet, array("id"=>$timesheet_id, "has_overtime"=>1))
                ->num_rows();

                $this->db->reset_query();

                if($hasOvertimeRecord == 1){
                    $total_accredited_ot_hrs = $this->db
                        ->select_sum("ts_ot.accredited_hrs")
                        ->join($this->tbl_overtime." ot", "ot.id = ts_ot.overtime_id")
                        ->where("ts_ot.timesheet_id", $timesheet_id)
                        ->where("ot.status", "Approved")
                        ->get($this->tbl_timesheet_overtime." ts_ot")
                        ->row("accredited_hrs");

                    $total_accredited_ndiff_ot_hrs = $this->db
                        ->select_sum("ts_ot.accredited_ndiff_hrs")
                        ->join($this->tbl_overtime." ot", "ot.id = ts_ot.overtime_id")
                        ->where("ts_ot.timesheet_id", $timesheet_id)
                        ->where("ot.status", "Approved")
                        ->get($this->tbl_timesheet_overtime." ts_ot")
                        ->row("accredited_ndiff_hrs");

                    $this->db->reset_query();
                }

                $this->db->where("id", $timesheet_id);
                $this->db->set("total_accredited_ot_hrs", $total_accredited_ot_hrs);
                $this->db->set("total_accredited_ndiff_ot_hrs", $total_accredited_ndiff_ot_hrs);
                $this->db->update($this->tbl_timesheet);

                $this->updateTimesheetHolidayById($timesheet_id);
            }

            $this->db->where("id", $_id);
            if($status == 0){
                $this->db->group_start();
                $this->db->where("status !=", 0);
                $this->db->or_where("status !=", 3);
                $this->db->group_end();
            }
            $this->db->set("status", $status);
            $this->db->set("confirmed_by", $logged_in_user_emp_id);
            $this->db->set("confirmed_at", $this->today->format("Y-m-d H:i:s"));
            $this->db->set("confirmation_remarks", $remarks);
            $this->db->update($this->tbl_time_adjustments);

            $this->db->reset_query();
        }

        $title = null;
        $message = null;
        switch ($status) {
            case 1:
                $title = "Time adjustment(s) was approved.";
                $message = "Time sheet was successfully updated.";
                break;
            case 2:
                $title = "Time adjustment(s) was declined.";
                $message = "No updates were applied in time sheets.";
                break;
            case 3:
                $title = "Time adjustment(s) was cancelled.";
                $message = "No updates were applied in time sheets.";
                break;
            default:
                $title = "Time adjustment(s) set to pending.";
                $message = "No updates were applied in time sheets.";
                break;
        }
        if ($this->db->trans_status() === FALSE) {
            $resultSet["success"] = false;
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["title"] = "An error occurred.";
            $resultSet["data"] = null;
            $this->db->trans_rollback();
        } else {
            $resultSet["success"] = true;
            $resultSet["message"] = $message;
            $resultSet["title"] = $title;
            $resultSet["data"] = array("status" => $status, "id" => $id);
            $this->db->trans_commit();
        }

        if (isset($timesheetUpdates)) {
            $resultSet["total_calculation"] = $timesheetUpdates ;
        }

        $this->db->db_debug = $this->db_debug;
        return $resultSet;
    }


     public function generatePerHourSlashPartimer($timesheet_id=null){
        $timesheet = new stdClass();
        $props = array("am_late", "pm_late", "am_ut", "pm_ut", "total_late", "total_ut");

        $this->db->select("ts.*");
        $this->db->from($this->tbl_timesheet." ts");
        $this->db->join($this->tbl_employees." emp", "emp.id = ts.emp_id");
        $this->db->where("ts.id", $timesheet_id);
        $this->db->group_start();
        $this->db->where("emp.payroll_type", "hourly");
        $this->db->or_where("emp.work_status", "part-time");
        $this->db->group_end();
        $qTemp = $this->db->get();
        if($qTemp->num_rows() == 1){
            $row = $qTemp->row();
            foreach ($props as $key => $value) {
                $timesheet->$value = intval($row->$value) > 0 ? 0: $row->$value;
            }
        }
        $this->db->reset_query();

        return $timesheet;
     }

     public function getEmployeePerHourShashPartimer($emp_id=null){
        $timesheet = new stdClass();
        $props = array("am_late", "pm_late", "am_ut", "pm_ut", "total_late", "total_ut");
        if($emp_id){
            $this->db->from($this->tbl_employees);
            $this->db->where("id", $emp_id);
            $this->db->group_start();
            $this->db->where("payroll_type", "hourly");
            $this->db->or_where("work_status", "part-time");
            $this->db->group_end();
            $qTemp = $this->db->get();
            if($qTemp->num_rows() == 1){
                foreach ($props as $key => $value) { $timesheet->$value = 0; }
            }
        }

        return $timesheet;
     }

     public function get_calculate_per_hour($timesheet_id=null){
                $timesheet = new stdClass();

                $this->db->select("*, tbl_she.date AS tsDATE");
                $this->db->from( $this->tbl_personnel ." tbl_per");
                $this->db->join( $this->tbl_employees." tbl_emp", "tbl_emp.biometricno=tbl_per.biometric_id");
                $this->db->join( $this->tbl_timesheet." tbl_she", "tbl_she.emp_id=tbl_emp.id");
                $this->db->where("tbl_she.id", $timesheet_id);
                // $this->db->where("tbl_per.is_perhour", 1);
                $this->db->where("tbl_emp.payroll_type", "hourly");
                $querysh = $this->db->get();
                if($querysh->result()){
                        $rowsh = $querysh->row();

                        $t_amin     = strtotime($rowsh->tsDATE.' '.$rowsh->am_in);
                        $t_amout    = strtotime($rowsh->tsDATE.' '.$rowsh->am_out);
                        $t_pmin     = strtotime($rowsh->tsDATE.' '.$rowsh->pm_in);
                        $t_pmout    = strtotime($rowsh->tsDATE.' '.$rowsh->pm_out);

                        $t_shft_amout_end    = strtotime($rowsh->tsDATE.' '.$rowsh->shift_am_end);
                        $t_shft_pmout_end    = strtotime($rowsh->tsDATE.' '.$rowsh->shift_pm_end);

                        $t_shft_am_start    = strtotime($rowsh->tsDATE.' '.$rowsh->shift_am_start);
                        $t_shft_pm_start    = strtotime($rowsh->tsDATE.' '.$rowsh->shift_pm_start);

                        $amrender = "0";
                        $pmrender = "0";

                        if ($rowsh->am_in!==null && $rowsh->am_out!==null) {
                            $amrender = round( (abs($t_amout - $t_amin)/(60*60))*60,2 ) ;
                            if ($t_amout > $t_shft_amout_end) {
                                $amrender = round( (abs($t_shft_amout_end - $t_amin)/(60*60))*60,2 ) ;
                            }
                        }

                        if ($rowsh->pm_in!==null && $rowsh->pm_out!==null) {
                            if ($t_pmin > $t_shft_pm_start) {
                                $pmrender = round( (abs($t_pmout - $t_pmin)/(60*60))*60,2 ) ;
                            }else{
                                $pmrender = round( (abs($t_pmout - $t_shft_pm_start)/(60*60))*60,2 ) ;
                            }
                        }

                            $timesheet->total_time_rendered =  $amrender + $pmrender;
                            $timesheet->total_late =  "0";
                            $timesheet->total_ut =  "0";
                            $timesheet->verified = 1;
                            $timesheet->am_late = "0";
                            $timesheet->am_ut = "0";
                            $timesheet->am_time_rendered = $amrender;
                            $timesheet->pm_late  = "0";
                            $timesheet->pm_ut = "0";
                            $timesheet->pm_time_rendered = $pmrender;
                            return $timesheet;
                }

    }


    public function approvedOvertimeAdjustments($timesheet_id=null){
        $arrData = new stdClass();
        if($timesheet_id){
            $qTimeSheet = $this->db->get_where($this->tbl_timesheet, array("id"=>$timesheet_id));
            if($qTimeSheet->num_rows() == 1){
                $qTempRow = $qTimeSheet->row();
                $date = date("Y-m-d", strtotime($qTempRow->date));

                $overtime = $this->db
                    ->get_where($this->tbl_overtime,
                        array(
                            "employee" => $qTempRow->emp_id,
                            "DATE(date_from)" => $date,
                            "status" => "Approved"
                        ));
                if($overtime->num_rows() == 1){
                    $overtimeRow = $overtime->row();
                    if(($qTempRow->shift_pm_end && $qTempRow->shift_pm_end !== null) &&
                    ($qTempRow->pm_out && $qTempRow->pm_out !== null)){

                        $ot_start_dtr = date("Y-m-d H:i", strtotime($qTempRow->date." ".$qTempRow->shift_pm_end));
                        $ot_end_dtr = date("Y-m-d H:i", strtotime($qTempRow->date." ".$qTempRow->pm_out));

                        $ot_start = strtotime($ot_start_dtr) < strtotime(date('Y-m-d H:i', strtotime($overtimeRow->date_from)))
                        ? date('Y-m-d H:i', strtotime($overtimeRow->date_from)) : $ot_start_dtr;
                        $ot_end = strtotime($ot_end_dtr) > strtotime(date('Y-m-d H:i', strtotime($overtimeRow->date_to)))
                            ? date('Y-m-d H:i', strtotime($overtimeRow->date_to)) : $ot_end_dtr;

                        $night_diff_cfg = $this->db->get_where($this->tbl_time_parameters, array("param_name" => "NIGHT_DIFF_PARAMS"))->row();

                        $init_start_date = new DateTime($ot_start);
                        $start_ndiff_date = $init_start_date->format('Y-m-d');
                        $start_ndiff_time = $night_diff_cfg->start_time;
                        $start_ndiff_date_time = date('Y-m-d H:i', strtotime($start_ndiff_date . " " . $start_ndiff_time));

                        $init_end_date = new DateTime($ot_end);
                        $end_ndiff_date = $init_end_date->format('Y-m-d');
                        $end_ndiff_time = $night_diff_cfg->end_time;
                        $end_ndiff_date_time = date("Y-m-d H:i", strtotime($end_ndiff_date . " " . $end_ndiff_time));

                        $ndiff_start = $start_ndiff_date_time;
                        $ndiff_end = $end_ndiff_date_time;

                        if (strtotime(date('Y-m-d', strtotime($ot_start)))
                            === strtotime(date('Y-m-d', strtotime($ot_end)))) {
                            $init_end_date = new DateTime($ot_start);
                            $end_ndiff_date = $init_end_date->modify("+1 day")->format('Y-m-d');
                            $end_ndiff_time = $night_diff_cfg->end_time;
                            $end_ndiff_date_time = date("Y-m-d H:i", strtotime($end_ndiff_date . " " . $end_ndiff_time));

                            $ndiff_start = $start_ndiff_date_time;
                            $ndiff_end = $end_ndiff_date_time;
                        }

                        if (strtotime($ndiff_start) >= strtotime($ot_start)) {
                            if (strtotime($ot_end) >= strtotime($ndiff_end)) {
                                $ot_night_diff = strtotime($ndiff_end) - strtotime($ndiff_start);
                                $ot_night_diff = $ot_night_diff / 3600;
                            } else {
                                $ot_night_diff = strtotime($ot_end) - strtotime($ndiff_start);
                                $ot_night_diff = $ot_night_diff / 3600;
                            }
                        } else {
                            $ot_night_diff = strtotime($ndiff_end) - strtotime($ot_start);
                            $ot_night_diff = $ot_night_diff / 3600;
                        }
                        // END OVERTIME NIGHT DIFF CALCULATION

                        $ot_night_diff = ($ot_night_diff < 0) ? 0 : $ot_night_diff;
                        $ot_seconds = (strtotime($ot_end) - strtotime($ot_start));
                        $ot_minutes = doubleval($ot_seconds) < 0 ? 0 : (doubleval($ot_seconds) / 60);
                        $ot_hrs = number_format(((doubleval($ot_minutes) / 60) - $ot_night_diff), 2, '.', '');

                        $arrData->total_hrs = $ot_hrs;
                        $arrData->accredited_hrs = $ot_hrs;
                        $arrData->ndiff_hrs = $ot_night_diff;
                        $arrData->accredited_ndiff_hrs = $ot_night_diff;
                    }
                }
            }
        }

        return $arrData;
    }

    public function calculateTimesheet($timesheet_id)
    {
        $this->db->where("id", $timesheet_id);
        $timesheet = $this->db->get($this->tbl_timesheet)->row();
        $date = $timesheet->date;

        $employee_time_sheet = new StdClass();
        $employee_time_sheet->am_late = 0;
        $employee_time_sheet->am_ut = 0;
        $employee_time_sheet->am_time_rendered = 0;
        $employee_time_sheet->pm_late = 0;
        $employee_time_sheet->pm_ut = 0;
        $employee_time_sheet->pm_time_rendered = 0;
        $employee_time_sheet->total_late = 0;
        $employee_time_sheet->total_ut = 0;
        $employee_time_sheet->total_time_rendered = 0;
        $employee_time_sheet->verified = 1;

        if (!empty($timesheet)) {
            $am_in = !empty($timesheet->am_in) && $timesheet->am_in !== null ? strtotime($date . " " . $timesheet->am_in) : null;
            $am_out = !empty($timesheet->am_out) && $timesheet->am_out !== null ? strtotime($date . " " . $timesheet->am_out) : null;
            $pm_in = !empty($timesheet->pm_in) && $timesheet->pm_in !== null ? strtotime($date . " " . $timesheet->pm_in) : null;
            $pm_out = !empty($timesheet->pm_out) && $timesheet->pm_out !== null ? (strtotime($date . " " . $timesheet->pm_out)) : null;

            if(($am_in && $am_out ) && ($am_out < $am_in)){
                $_date00 = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $am_out = $am_out ? strtotime(date('Y-m-d H:i', strtotime($_date00 . " " . $timesheet->am_out))): $am_out;
            }

            if(($pm_in && $am_out) && ($pm_in < $am_out)){
                $_date01 = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $pm_in = $pm_in ? strtotime(date('Y-m-d H:i', strtotime($_date01 . " " . $timesheet->pm_in))): $pm_in;
            }

            if($pm_out < $pm_in){
                $_date02 = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $pm_out = $pm_out ? strtotime(date('Y-m-d H:i', strtotime($_date02 . " " . $timesheet->pm_out))): $pm_out;
            }

            /*** if (($timesheet->am_in && $timesheet->pm_out) && strtotime($timesheet->pm_out) < strtotime($timesheet->am_in)) {
                $next_day = new DateTime($date);
                $next_day->modify("+1 day");
                $pm_out = strtotime($next_day->format("Y-m-d") . " " . $timesheet->pm_out);
            } ***/

            $_am_start = empty($timesheet->shift_am_start) || $timesheet->shift_am_start == null ? null : strtotime($date . " " . $timesheet->shift_am_start);
            $_am_end = empty($timesheet->shift_am_end) || $timesheet->shift_am_end == null ? null : strtotime($date . " " . $timesheet->shift_am_end);
            $_pm_start = empty($timesheet->shift_pm_start) || $timesheet->shift_pm_start == null ? null : strtotime($date . " " . $timesheet->shift_pm_start);
            $_pm_end = empty($timesheet->shift_pm_end) || $timesheet->shift_pm_end == null ? null : strtotime($date . " " . $timesheet->shift_pm_end);
            
            if($_am_end < $_am_start){
                $date_00 = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $_am_end = strtotime(date('Y-m-d H:i', strtotime($date_00 . " " . $timesheet->shift_am_end)));
            }

            if($_pm_start < $_am_end){
                $date_01 = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $_pm_start = strtotime(date('Y-m-d H:i', strtotime($date_01 . " " . $timesheet->shift_pm_start)));
            }

            if($_pm_end < $_pm_start){
                $date_02 = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $_pm_end = strtotime(date('Y-m-d H:i', strtotime($date_02 . " " . $timesheet->shift_pm_end)));
            }

            if (!empty($_am_start) && !empty($_am_end)) {
                if (($am_in > $_am_start) && $am_in) {
                    $employee_time_sheet->am_late = round(($am_in - $_am_start) / 60, 2);
                }

                if (($am_out < $_am_end) && $am_out) {
                    $employee_time_sheet->am_ut = round(($_am_end - $am_out) / 60, 2);
                } else {
                    if (($_am_start && $_am_end) && (!$am_in && !$am_out)) {
                        $amExpectedWorkedHours = $_am_end - $_am_start;
                        $employee_time_sheet->am_ut = round(($amExpectedWorkedHours) / 60, 2);
                    }
                }

                if ($am_in && $am_out) {
                    $am_time_rendered = ($am_out > $_am_end ? $_am_end : $am_out) - ($am_in < $_am_start ? $_am_start : $am_in);
                    $employee_time_sheet->am_time_rendered = round(($am_time_rendered) / 60, 2);
                }
            }


            if (!empty($_pm_start) && !empty($_pm_end)) {
                if (($pm_in > $_pm_start) && $pm_in) {
                    $employee_time_sheet->pm_late = round(($pm_in - $_pm_start) / 60, 2);
                }

                if (($pm_out < $_pm_end) && $pm_out) {
                    $employee_time_sheet->pm_ut = round(($_pm_end - $pm_out) / 60, 2);
                } else {
                    if (($_pm_start && $_pm_end) && (!$pm_in && !$pm_out)) {
                        $pmExpectedWorkedHours = $_pm_end - $_pm_start;
                        $employee_time_sheet->pm_ut = round(($pmExpectedWorkedHours) / 60, 2);
                    }
                }

                if ($pm_in && $pm_out) {
                    $pm_time_rendered = ($pm_out > $_pm_end ? $_pm_end : $pm_out) - ($pm_in < $_pm_start ? $_pm_start : $pm_in);
                    $employee_time_sheet->pm_time_rendered = round(($pm_time_rendered) / 60, 2);
                }
            }

            $employee_time_sheet->total_late = $employee_time_sheet->am_late + $employee_time_sheet->pm_late;
            $employee_time_sheet->total_ut = $employee_time_sheet->am_ut + $employee_time_sheet->pm_ut;
            $employee_time_sheet->total_time_rendered = $employee_time_sheet->am_time_rendered + $employee_time_sheet->pm_time_rendered;
        }

        return $employee_time_sheet;
    }

    public function getTimeAdjustmentDetails($time_adjustment_id, $for_modal_details = 0)
    {
        /*** original select fields ***/
        /*** "COALESCE((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id), timesheet.am_in) am_in,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) am_in_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) IS NULL, 0, 1) am_in_is_requested,
            COALESCE((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id), timesheet.am_out) am_out,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) am_out_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) IS NULL, 0, 1) am_out_is_requested,
            COALESCE((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id), timesheet.pm_in) pm_in,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) pm_in_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) IS NULL, 0, 1) pm_in_is_requested,
            COALESCE((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id), timesheet.pm_out) pm_out,
            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) pm_out_prev,
            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) IS NULL, 0, 1) pm_out_is_requested,"
        ***/
        /*** original select fields ***/

        $this->db->select("mn.id time_adjustment_id, mn.timesheet_id, mn.entry_type, mn.with_shift_adjustment, mn.adjustment_override,
                            mn.`status`, mn.remarks, mn.created_by, mn.created_at, mn.confirmed_by, mn.confirmed_at, mn.confirmation_remarks,
                            COALESCE(
                            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id),
                            IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.am_in))
                            , timesheet.am_in) am_in,
                            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) am_in_prev,
                            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_in' AND time_adjustments_id = mn.id) IS NULL, 0, 1) am_in_is_requested,
                            COALESCE(
                            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id),
                            IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.am_out))
                            , timesheet.am_out) am_out,
                            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) am_out_prev,
                            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='am_out' AND time_adjustments_id = mn.id) IS NULL, 0, 1) am_out_is_requested,
                            COALESCE(
                            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id),
                            IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.pm_in))
                            , timesheet.pm_in) pm_in,
                            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) pm_in_prev,
                            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_in' AND time_adjustments_id = mn.id) IS NULL, 0, 1) pm_in_is_requested,
                            COALESCE(
                            IFNULL((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id),
                            IF((SELECT COUNT(`id`) FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) = '1', 'empty', timesheet.pm_out))
                            , timesheet.pm_out) pm_out,
                            (SELECT `prev_value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) pm_out_prev,
                            IF((SELECT `value` FROM $this->tbl_time_adjustments_meta WHERE field='pm_out' AND time_adjustments_id = mn.id) IS NULL, 0, 1) pm_out_is_requested,
                            IF(timesheet.has_shift = 1, timesheet.shift_am_start, shift.am_start) am_start,
                            IF(timesheet.has_shift = 1, timesheet.shift_am_end, shift.am_end) am_end,
                            IF(timesheet.has_shift = 1, timesheet.shift_pm_start, shift.pm_start) pm_start,
                            IF(timesheet.has_shift = 1, timesheet.shift_pm_end, shift.pm_end) pm_end,
                               CONCAT(emp.lastname,
                                    CASE
                                        WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != '' AND emp.suffix IS NOT NULL
                                            THEN CONCAT(' ', emp.suffix)
                                        ELSE '' END, ', ', emp.firstname, ' ',
                                    CASE
                                        WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                            AND emp.middlename != '' AND emp.middlename IS NOT NULL
                                            THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                                        ELSE '' END) `employee_name`,
                               emp.id,
                               emp.biometricno,
                               timesheet.date,
                               timesheet.weekday,
                               timesheet.has_shift,
                               timesheet.scrub_status,
                               time_adj_m_ot.overtime_in,
                               time_adj_m_ot.overtime_out,
                               time_adj_m_ot.regular_hrs,
                               time_adj_m_ot.ndiff_hrs,
                               time_adj_m_ot.purpose,
                               time_adj_m_ot.requested_by,
                               CONCAT(emp_request.lastname,
                                CASE
                                WHEN emp_request.suffix != 'N/A' AND emp_request.suffix != 'NONE' AND emp_request.suffix != '' AND emp_request.suffix IS NOT NULL
                                    THEN CONCAT(' ', emp_request.suffix)
                                    ELSE '' END, ', ',
                                    emp_request.firstname, ' ', CASE
                                    WHEN emp_request.middlename != 'N/A' AND emp_request.middlename != 'NONE'
                                    AND emp_request.middlename != '' AND emp_request.middlename IS NOT NULL
                                    THEN CONCAT(SUBSTR(emp_request.middlename, 1, 1), '.')
                                    ELSE '' END) as `requestor_name`");
        $this->db->join($this->tbl_time_adjustments_shift_schedule . " shift", "shift.time_adjustments_id = mn.id", "LEFT");
        $this->db->join($this->tbl_timesheet . " timesheet", "timesheet.id = mn.timesheet_id", "INNER");
        $this->db->join($this->tbl_employees . " emp", "emp.id = timesheet.emp_id", "INNER");
        $this->db->join($this->tbl_time_adjustments_overtime . " time_adj_ot", "time_adj_ot.time_adjustments_id = mn.id", "LEFT");
        $this->db->join($this->tbl_time_adjustments_manual_overtime . " time_adj_m_ot", "time_adj_m_ot.time_adjustments_id = mn.id", "LEFT");
        $this->db->join($this->tbl_employees . " emp_request", "emp_request.id = time_adj_m_ot.requested_by", "LEFT");

        $this->db->where("mn.id", $time_adjustment_id);
        $this->db->group_by("mn.id");

        $query = $this->db->get($this->tbl_time_adjustments . " mn");
        $row = $query->row();

        $alteredShiftSchedule = $this->getCustomizedShiftScheduleByDate($row->date, $row->id);
        if(isset($alteredShiftSchedule->schedule)){
            foreach ($alteredShiftSchedule->schedule as $key => $value) {
                if($value && $alteredShiftSchedule->has_shift == 1){
                    $row->$key = $value;
                }
                if($alteredShiftSchedule->has_shift == 0){
                    $row->$key = $value;
                }
            }
        }
        /*** manual overtime ***/
        $row->has_manual_overtime = false;
        if($row->overtime_in && $row->overtime_out && $row->requested_by){ $row->has_manual_overtime = true; }
        /*** manual overtime ***/
        $employee_id = $row->id;
        $biometricno = $row->biometricno;

        $this->db->join($this->tbl_loa . " loa", "loa.id = refs.eform_ids AND refs.time_adjustments_id = " . $time_adjustment_id . " AND refs.eform_table='gcceforms.loa'", "INNER");
        $row->loa = array_map(function ($loa) {
            return $loa->id;
        }, $this->db->get($this->tbl_time_adjustments_eform_refs . " refs")->result());

        $this->db->join($this->tbl_TO . " to", "to.id = refs.eform_ids AND refs.time_adjustments_id = " . $time_adjustment_id . " AND refs.eform_table='gcceforms.travel_order'", "INNER");
        $row->travel_order = array_map(function ($to) {
            return $to->id;
        }, $this->db->get($this->tbl_time_adjustments_eform_refs . " refs")->result());

        $date_loa_list = $this->getEmployeeDetailedLoaForSelect($employee_id, $row->date);

        $date_travel_order_list = $this->getEmployeeDetailedTravelOrderForSelect($employee_id, $row->date);

        $attendance_params = $this->db->get_where($this->tbl_time_parameters, array("param_name" => "TS_OT_PARAMS"))->row();
        /* CONCATENATE DATE + START TIME OF TIME PARAMS start_time ex. [2020-07-24 06:01] AS START DATE PARAMETER TO DETERMINE ATTENDANCES
                                *  and (DATE + 1 DAY) + END TIME OF TIME PARAMS ex. [2020-07-24 + 1 = 2020-07-25 06:00] AS END DATE PARAMETER TO DETERMINE ATTENDANCES */
        $att_curr_day = date('Y-m-d H:i:s', strtotime($row->date . " " . $attendance_params->start_time));

        $_date = new DateTime($row->date);
        $att_next_day = $_date->modify("+1 day");
        $att_next_day = date('Y-m-d H:i:s', strtotime($att_next_day->format('Y-m-d') . " " . $attendance_params->end_time));
        /* END CONCATENATE DATE */

        $attendance = array_map(function ($_attendance) {
            return date("h:i A", strtotime($_attendance->datetime));
        }, $this->getAttendance($att_curr_day, $employee_id, $att_next_day));

        $attendance = array_values(array_unique($attendance));
        $resultSet = array("row" => $row, "loa_list" => $date_loa_list, "travel_order_list" => $date_travel_order_list, "attendance" => $attendance);

        $has_overtime_request = $this->db->where("time_adjustments_id", $row->time_adjustment_id)
            ->count_all_results($this->tbl_time_adjustments_overtime);

        $resultSet["has_overtime_request"] = intval($has_overtime_request) >= 1;

        if (intval($has_overtime_request) >= 1) {
            $resultSet["overtime"] = $this->db
                ->select("ts_ot.*,
                          adj_ot.id adj_id,
                          IF(adj_ot.id IS NULL, ts_ot.accredited_hrs , adj_ot.value) adj_value,
                          IF(adj_ot.id IS NULL, ts_ot.accredited_ndiff_hrs , adj_ot.n_diff_value) ndiff_adj_value,
                          IF(adj_ot.id IS NULL, ts_ot.overtime_in , adj_ot.ot_in_value) adj_ot_in_value,
                          IF(adj_ot.id IS NULL, ts_ot.overtime_out , adj_ot.ot_out_value) adj_ot_out_value,
                          ot.reference_no,
                          ot.date_from,
                          ot.date_to,
                          ot.purpose,
                          CONCAT(emp.lastname,
                            CASE
                                WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != '' AND emp.suffix IS NOT NULL
                                    THEN CONCAT(' ', emp.suffix)
                                ELSE '' END, ', ',
                            emp.firstname, ' ',
                               CASE
                               WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                   AND emp.middlename != '' AND emp.middlename IS NOT NULL
                                   THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                               ELSE '' END) `requestor`")
                ->join($this->tbl_timesheet . " ts", "ts.id = ts_ot.timesheet_id")
                ->join($this->tbl_time_adjustments_overtime . " adj_ot", "ts_ot.id = adj_ot.ts_overtime_id", "LEFT")
                ->join($this->tbl_overtime . " ot", "ot.id = ts_ot.overtime_id")
                ->join($this->tbl_employees . " emp", "emp.id = ot.requested_by")
                ->where("`ts_ot`.`timesheet_id`", $row->timesheet_id)
                ->where("`ts`.`has_overtime`", 1)
                ->where("`ot`.`status`", "Approved")
                ->group_start()
                ->where("adj_ot.time_adjustments_id", $row->time_adjustment_id)
                ->or_where("adj_ot.id IS NULL", NULL, FALSE)
                ->group_end()
                ->order_by("ot.date_from", "ASC")
                ->get($this->tbl_timesheet_overtime . " ts_ot")
                ->result();

            $row->overtime = $resultSet["overtime"];
            $row->ot_adj_value = array_reduce($row->overtime, function ($acc, $next) {
                return $acc + $next->adj_value;
            }, 0);

            $row->ot_original_value = array_reduce($row->overtime, function ($acc, $next) {
                return $acc + $next->accredited_hrs;
            }, 0);
        } else {
            $overtime = $this->db
                ->select("ts_ot.*,
                              NULL adj_id,
                              ts_ot.accredited_hrs adj_value,
                              ts_ot.accredited_ndiff_hrs ndiff_adj_value,
                              ot.date_from adj_ot_in_value,
                              ot.date_to adj_ot_out_value,
                              ot.reference_no,
                              ot.date_from,
                              ot.date_to,
                              ot.purpose,
                              CONCAT(emp.lastname,
                                CASE
                                    WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != '' AND emp.suffix IS NOT NULL
                                        THEN CONCAT(' ', emp.suffix)
                                    ELSE '' END, ', ',
                                emp.firstname, ' ',
                                   CASE
                                   WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                       AND emp.middlename != '' AND emp.middlename IS NOT NULL
                                       THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                                   ELSE '' END) `requestor`", FALSE)
                ->join($this->tbl_timesheet . " ts", "ts.id = ts_ot.timesheet_id")
                ->join($this->tbl_overtime . " ot", "ot.id = ts_ot.overtime_id")
                ->join($this->tbl_employees . " emp", "emp.id = ot.requested_by")
                ->where("ts_ot.timesheet_id", $row->timesheet_id)
                ->where("ts.has_overtime", 1)
                ->order_by("ot.date_from", "ASC")
                ->get($this->tbl_timesheet_overtime . " ts_ot")
                ->result();

            $total_ot_hrs = array_reduce($overtime, function ($acc, $next) {
                return $acc + $next->adj_value;
            }, 0);

            $row->overtime = $overtime;
            $resultSet["overtime"] = $overtime;
            $row->ot_adj_value = $total_ot_hrs;
            $row->ot_original_value = $total_ot_hrs;
        }

        if(is_array($resultSet["overtime"]) && count($resultSet["overtime"]) > 0){
            $row->has_manual_overtime = false;
            $resultSet["row"] = $row;
        }

        if (intval($for_modal_details) === 1) {
            $creator = $this->db->where("id", $row->created_by)->get($this->tbl_employees)->row_array();
            $confirmed_by = $this->db->where("id", $row->confirmed_by)->get($this->tbl_employees)->row_array();

            $resultSet["creator"] = !empty($creator) ? $this->core_layout->getDisplayName($creator)["display_name_1"] : null;

            $resultSet["selected_loa"] = !empty($row->loa) ? $this->db->where_in("id", $row->loa)->get($this->tbl_loa)->result() : [];

            $resultSet["selected_travel_order"] = !empty($row->travel_order) ? $this->db
                ->select("travel_order.*, GROUP_CONCAT(destination.destination) `destination`,	GROUP_CONCAT(destination.purpose) purpose")
                ->join("gcceforms.travel_destination destination", "destination.travel_order_id = travel_order.id", "INNER")
                ->where_in("travel_order.id", $row->travel_order)
                ->group_by("travel_order.id")
                ->get($this->tbl_TO . " travel_order")
                ->result() : [];

            $resultSet["confirmed_by"] = !empty($confirmed_by) ? $this->core_layout->getDisplayName($confirmed_by)["display_name_1"] : null;;
            $resultSet["modal"] = $this->load->view('timesheet/time_adjustments/modals/time_adjustment_details_modal', $resultSet, TRUE);
        }

        return $resultSet;
    }

    private function getEmployeeDetailedLoaForSelect($employee_id, $date)
    {
        $hasLoaRecords = array();
        $tempLoaRecord = $this->getLoaRecordByDateRange($date);
        $tempKeySearch = "emp_id_{$employee_id}";
        if(isset($tempLoaRecord[$tempKeySearch]) && is_array($tempLoaRecord[$tempKeySearch]) && count($tempLoaRecord[$tempKeySearch]) > 0){
            $hasLoaRecords = $tempLoaRecord[$tempKeySearch];
        }
        
        $loaReferenceNo = null;
        if(is_array($hasLoaRecords) && count($hasLoaRecords) > 0){
            foreach ($hasLoaRecords as $keyLoa => $valueLoa) {
                $_tempLoa = explode("::", $valueLoa);
                if(is_array($_tempLoa) && count($_tempLoa) == 3){
                    $tempDTx = explode("__", $_tempLoa[1]);
                    $tempIsWholeDay = intval($_tempLoa[2]) == 1? 1: 0;
                    $tempDate00 = strtotime(date("Y-m-d", strtotime($tempDTx[0])));
                    $tempDate01 = strtotime(date("Y-m-d", strtotime($tempDTx[1])));
                    $ccDatex = strtotime(date("Y-m-d", strtotime($date)));

                    if($ccDatex >= $tempDate00 && $ccDatex <= $tempDate01){
                        $loaReferenceNo = $_tempLoa[0];
                    }
                }
            }
        }
        
        return $this->db->select("id,
                               reference_no `title`,
                               CONCAT('<div class=\"m--font-boldest m--regular-font-size-lg1\">', reference_no, '</div>',
                                      '<span class=\"mt-1 m--regular-font-size-sm1 m--font-boldest text-muted\">',
                                        CASE WHEN type=1 THEN 'UNDERTIME'
                                             WHEN type=2 THEN 'HALF DAY'
                                             WHEN type=3 THEN 'WHOLE DAY'
                                             WHEN type=4 THEN 'OTHER'
                                        END ,
                                      '</span>', ', ',
                                      '<span class=\"m--regular-font-size-sm1 m--font-boldest\">',
                                        CASE WHEN type=1 THEN CONCAT(DATE_FORMAT(date_from, '%b %d,%Y %h:%i %p'), ' - ' ,DATE_FORMAT(date_to, '%h:%i %p'))
                                             WHEN type=2 THEN CONCAT(DATE_FORMAT(date_from, '%b %d,%Y %h:%i %p'), ' - ' ,DATE_FORMAT(date_to, '%h:%i %p'))
                                             WHEN type=3 THEN DATE_FORMAT(date_from, '%b %d,%Y')
                                             WHEN TYPE=4 THEN CONCAT(DATE_FORMAT(date_from, '%b %d, %Y %h:%i %p'),
                                                                     IF(date_to != '00:00:00', CONCAT(' - ', DATE_FORMAT(date_to, '%b %d, %Y %h:%i %p')), ''))
                                        END ,
                                      '</span>',
                                      '<div class=\"m--regular-font-size-sm1 m--font-boldest text-muted\">', reason ,'</div>') `text`")
            ->group_start()
            ->where("DATE(date_from) <=", $date)
            ->where("DATE(date_to) >=", $date)
            ->or_where("reference_no", $loaReferenceNo)
            ->group_end()
            ->get_where($this->tbl_loa, array("employee" => $employee_id, "status"=>"Approved"))
            ->result();
    }

    private function getEmployeeDetailedTravelOrderForSelect($employee_id, $date)
    {
        $hasToRecords = array();
        $tempToRecord = $this->getToRecordByDateRange($date);
        $tempKeySearch = "emp_id_{$employee_id}";
        if(isset($tempToRecord[$tempKeySearch]) && is_array($tempToRecord[$tempKeySearch]) && count($tempToRecord[$tempKeySearch]) > 0){
            $hasToRecords = $tempToRecord[$tempKeySearch];
        }
        
        $toReferenceNo = null;
        if(is_array($hasToRecords) && count($hasToRecords) > 0){
            foreach ($hasToRecords as $valueLoa) {
                $_tempTo = explode("::", $valueLoa);
                if(is_array($_tempTo) && count($_tempTo) == 2){
                    $tempDTx = explode("__", $_tempTo[1]);
                    $tempDate00 = strtotime(date("Y-m-d", strtotime($tempDTx[0])));
                    $tempDate01 = strtotime(date("Y-m-d", strtotime($tempDTx[1])));
                    $ccDatex = strtotime(date("Y-m-d", strtotime($date)));

                    if($ccDatex >= $tempDate00 && $ccDatex <= $tempDate01){
                        $toReferenceNo = $_tempTo[0];
                    }
                }
            }
        }
        $travel_orders = $this->db->select("to.id, to.reference_no `title`")
            ->from($this->tbl_TO." to")
            ->join($this->tbl_TO_Destination." td", "td.travel_order_id = to.id")
            ->join($this->tbl_TO_Personnel." tp", "tp.travel_order_id = to.id", "LEFT")
            ->group_start()
                ->where(array(
                    "DATE(td.date_from) >=" => $date,
                    "DATE(td.date_to) <=" => $date,
                ))
                ->or_where(array(
                    "DATE(td.date_from) <=" => $date,
                ))
                ->where(array(
                    "DATE(td.date_to) >=" => $date,
                ))
                ->or_where(array(
                    "to.reference_no" => $toReferenceNo,
                ))
                /*** ->or_where("DATE(to.created_dt)", $date) ***/
            ->group_end()
            ->group_start()
                ->where("to.driver_id", $employee_id)
                ->or_where("tp.employee_id", $employee_id)
            ->group_end()
            ->where("to.status", "Approved")
            ->group_by("to.id")
            ->get()
            ->result();
            
        foreach ($travel_orders as $travel_order) {
            $travel_order->text = '<div class="mb-1 m--font-boldest m--regular-font-size-lg1">' . $travel_order->title . '</div>';
            $destinations = $this->db->get_where("gcceforms.travel_destination", array("travel_order_id" => $travel_order->id))->result();
            array_map(function ($destination) use ($travel_order) {
                $travel_order->text .= "<div class='mb-1'>
                                                <div class='m--regular-font-size-sm1 m--font-boldest'>" . $destination->destination . "</div>
                                                <div class='m--regular-font-size-sm2 m--font-bolder'><span class='text-muted'>PURPOSE</span>" . $destination->purpose . "</div>
                                            </div>";
            }, $destinations);
        }

        return $travel_orders;
    }

    public function updateTimeAdjustment($time_adjustment_id, $has_shift)
    {
        $this->db->db_debug = false;
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $post = $this->arrayToStdClass($this->input->post());
        $meta_fields = ["am_in_obj", "am_out_obj", "pm_in_obj", "pm_out_obj"];
        $shifts = $post->shifts;
        $travel_order = empty($post->travel_order) ? array() : $post->travel_order;
        $loa = empty($post->loa) ? array() : $post->loa;
        $update_affected_count = 0;
        $overtimeUpdates = isset($post->overtimeUpdates) ? $post->overtimeUpdates : array();

        $manualOvertime = new stdClass();
        if(isset($post->manualOvertime) && $post->manualOvertime){
            $manualOvertime = $post->manualOvertime;
            unset($post->manualOvertime);
        }

        $this->db->trans_begin();

        foreach ($meta_fields as $meta_field) {
            $obj = explode("_", $meta_field);
            $col = join("_", array_slice($obj, 0, sizeof($obj) - 1));

            if (intval($post->$meta_field->modified) === 1) {
                $meta_exist = $this->db->get_where($this->tbl_time_adjustments_meta, array("time_adjustments_id" => $time_adjustment_id, "field" => $col))->row();
                $tempValue = ($post->$meta_field->value)? date("H:i", strtotime($post->$meta_field->value)): null;
                if (!empty($meta_exist)) {
                    $meta_id = $meta_exist->id;
                    $this->db->where("id", $meta_id);
                    $this->db->set("value", $tempValue);
                    $this->db->set("prev_value", $post->$meta_field->prev_value);
                    $this->db->set("is_manual", $post->$meta_field->manual);
                    $this->db->set("last_updated_by", $logged_in_user_emp_id);
                    $this->db->set("last_updated_at", $this->today->format("Y-m-d H:i:s"));
                    $this->db->update($this->tbl_time_adjustments_meta);
                    $this->db->reset_query();

                    $update_affected_count += 1;
                } else {
                    $meta_data = array(
                        "time_adjustments_id" => $time_adjustment_id,
                        "field" => $col,
                        "value" => $tempValue,
                        "prev_value" => !empty($post->$meta_field->prev_value) ? $post->$meta_field->prev_value : NULL,
                        "is_manual" => $post->$meta_field->manual,
                        "created_by" => $logged_in_user_emp_id,
                    );

                    $this->db->insert($this->tbl_time_adjustments_meta, $meta_data);
                    $update_affected_count += 1;
                }
            }
        }

        // START TRAVEL ORDER
        if (!empty($travel_order)) {
            $to_not_in_refs = $this->db
                ->where_not_in("eform_ids", $travel_order)
                ->where("eform_table", "gcceforms.travel_order")
                ->where("time_adjustments_id", $time_adjustment_id)
                ->get($this->tbl_time_adjustments_eform_refs)->result();

            $to_not_in_refs_id = array_map(function ($_to) {
                return $_to->id;
            }, $to_not_in_refs);

            if (!empty($to_not_in_refs_id)) {
                $this->db->where_in("id", $to_not_in_refs_id)->delete($this->tbl_time_adjustments_eform_refs);
                $update_affected_count += 1;
            }

            foreach ($travel_order as $to) {
                $to_exist = $this->db
                    ->where(array("time_adjustments_id" => $time_adjustment_id, "eform_table" => "gcceforms.travel_order", "eform_ids" => $to))
                    ->get($this->tbl_time_adjustments_eform_refs)
                    ->row();

                if (empty($to_exist)) {
                    $this->db->insert($this->tbl_time_adjustments_eform_refs,
                        array(
                            "time_adjustments_id" => $time_adjustment_id,
                            "eform_table" => "gcceforms.travel_order",
                            "eform_ids" => $to,
                            "created_by" => $logged_in_user_emp_id
                        )
                    );
                    $update_affected_count += 1;
                }
            }
        } else {
            $this->db->where(
                array(
                    "time_adjustments_id" => $time_adjustment_id,
                    "eform_table" => "gcceforms.travel_order"
                ))->delete($this->tbl_time_adjustments_eform_refs);
        }
        // END TRAVEL ORDER

        // START LEAVE OF ABSENCE
        if (!empty($loa)) {
            $loa_not_in_refs = $this->db
                ->where_not_in("eform_ids", $loa)
                ->where("eform_table", "gcceforms.loa")
                ->where("time_adjustments_id", $time_adjustment_id)
                ->get($this->tbl_time_adjustments_eform_refs)->result();
            $loa_not_in_refs_id = array_map(function ($_loa) {
                return $_loa->id;
            }, $loa_not_in_refs);

            if (!empty($loa_not_in_refs_id)) {
                $this->db->where_in("id", $loa_not_in_refs_id)->delete($this->tbl_time_adjustments_eform_refs);
                $update_affected_count += 1;
            }

            foreach ($loa as $_loa) {
                $loa_exist = $this->db
                    ->where(array("time_adjustments_id" => $time_adjustment_id, "eform_table" => "gcceforms.loa", "eform_ids" => $_loa))
                    ->get($this->tbl_time_adjustments_eform_refs)
                    ->row();

                if (empty($loa_exist)) {
                    $this->db->insert($this->tbl_time_adjustments_eform_refs,
                        array(
                            "time_adjustments_id" => $time_adjustment_id,
                            "eform_table" => "gcceforms.loa",
                            "eform_ids" => $_loa,
                            "created_by" => $logged_in_user_emp_id
                        )
                    );
                    $update_affected_count += 1;
                }
            }
        } else {
            $this->db->where(array(
                "time_adjustments_id" => $time_adjustment_id,
                "eform_table" => "gcceforms.loa"
            ))->delete($this->tbl_time_adjustments_eform_refs);
        }
        // END LEAVE OF ABSENCE

        // START UPDATE FOR OVERTIME
        if (!empty($overtimeUpdates)) {
            foreach ($overtimeUpdates as $overtimeUpdate) {
                if (!empty($overtimeUpdate->id) && $overtimeUpdate->id !== "null") {
                    $this->db->where("id", $overtimeUpdate->id);
                    $this->db->set("value", $overtimeUpdate->value);
                    $this->db->set("n_diff_value", $overtimeUpdate->n_diff_value);
                    $this->db->set("ot_in_value", $overtimeUpdate->ot_in_value);
                    $this->db->set("ot_out_value", $overtimeUpdate->ot_out_value);
                    $this->db->set("last_updated_by", $logged_in_user_emp_id);
                    $this->db->set("last_updated_at", $this->today->format("Y-m-d H:i:s"));
                    $this->db->update($this->tbl_time_adjustments_overtime);
                } else {
                    $this->db->insert($this->tbl_time_adjustments_overtime, array(
                        "time_adjustments_id" => $time_adjustment_id,
                        "ts_overtime_id" => $overtimeUpdate->ts_overtime_id,
                        "value" => $overtimeUpdate->value,
                        "prev_value" => $overtimeUpdate->prev_val,
                        "n_diff_value" => $overtimeUpdate->n_diff_value,
                        "n_diff_prev_value" => $overtimeUpdate->n_diff_prev_value,
                        "ot_in_value" => $overtimeUpdate->ot_in_value,
                        "ot_in_prev_value" => $overtimeUpdate->ot_in_prev_value,
                        "ot_out_value" => $overtimeUpdate->ot_out_value,
                        "ot_out_prev_value" => $overtimeUpdate->ot_out_prev_value,
                        "created_by" => $logged_in_user_emp_id,
                        "created_at" => $this->today->format("Y-m-d H:i:s"),
                    ));
                }
                $this->db->reset_query();
            }
        }
        // END UPDATE FORM OVERTIME

        // START UPDATE SHIFT SHCEDULE
        if (intval($has_shift) === 0) {
            $shift_schedule = $this->db
                ->where("time_adjustments_id", $time_adjustment_id)
                ->get($this->tbl_time_adjustments_shift_schedule)
                ->row();

            if (!empty($shift_schedule)) {
                if ($shift_schedule->am_start !== $shifts->am_start || $shift_schedule->am_end !== $shifts->am_end ||
                    $shift_schedule->pm_start !== $shifts->pm_start || $shift_schedule->pm_end !== $shifts->pm_end) {
                    $this->db->where("id", $shift_schedule->id);
                    $this->db->set("am_start", $shifts->am_start);
                    $this->db->set("am_end", $shifts->am_end);
                    $this->db->set("pm_start", $shifts->pm_start);
                    $this->db->set("pm_end", $shifts->pm_end);
                    $this->db->set("last_updated_by", $logged_in_user_emp_id);
                    $this->db->set("last_updated_at", $this->today->format("Y-m-d H:i:s"));
                    $this->db->update($this->tbl_time_adjustments_shift_schedule);
                    $this->db->reset_query();
                }
            }
        }
        // END UPDATE SHIFT SCHEDULE

        if(isset($manualOvertime) && count((array)$manualOvertime) > 0 && $time_adjustment_id){
            if(isset($manualOvertime->overtime_requested_by, $manualOvertime->overtime_in, $manualOvertime->overtime_out) &&
            ($manualOvertime->overtime_in && $manualOvertime->overtime_out && $manualOvertime->overtime_requested_by)){
                $tempOTData = new stdClass();
                $tempOTData->overtime_in = date("Y-m-d H:i:s", strtotime($manualOvertime->overtime_in));
                $tempOTData->overtime_out = date("Y-m-d H:i:s", strtotime($manualOvertime->overtime_out));
                $tempOTData->regular_hrs = $manualOvertime->overtime_reg_hrs ? $manualOvertime->overtime_reg_hrs : 0;
                $tempOTData->ndiff_hrs = $manualOvertime->overtime_ndiff_hrs ? $manualOvertime->overtime_ndiff_hrs : 0;
                $tempOTData->requested_by = $manualOvertime->overtime_requested_by ? $manualOvertime->overtime_requested_by : 0;
                $tempOTData->purpose = $manualOvertime->overtime_purpose ? $manualOvertime->overtime_purpose : "";
                $tempOTData->updated_by = $logged_in_user_emp_id;
                $tempOTData->updated_at = date("Y-m-d H:i:s");

                $this->db->update($this->tbl_time_adjustments_manual_overtime, $tempOTData, array("time_adjustments_id"=>$time_adjustment_id));
            }
        }

        $this->db->where("id", $time_adjustment_id);
        $this->db->set("remarks", $post->remarks);
        $this->db->set("adjustment_override", $post->adjustment_override);
        $this->db->set("last_updated_by", $logged_in_user_emp_id);
        $this->db->set("last_updated_at", $this->today->format("Y-m-d H:i:s"));
        $this->db->update($this->tbl_time_adjustments);

        $resultSet = array();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $resultSet["success"] = false;
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["title"] = "DB error occurred.";
        } else {
            $this->db->trans_commit();
            $resultSet["success"] = true;
            $resultSet["message"] = "Time adjustment details was successfully updated.";
            $resultSet["title"] = "Successfully Updated.";
            $resultSet["data"] = $this->getTimeAdjustmentDetails($time_adjustment_id);
        }

        $this->db->db_debug = $this->db_debug;
        return $resultSet;
    }

    public function getTimesheetTimeAdjustmentsList($timesheet_id, $employee_id)
    {
        $resultSet = array();
        $resultSet["timesheet_id"] = $timesheet_id;
        $resultSet["employee_id"] = $employee_id;
        $resultSet["timesheet"] = $this->db->get_where($this->tbl_timesheet, array("id" => $timesheet_id))->row();

        $resultSet["employee"] = $this->db->select("emp.*, CONCAT(emp.lastname,
            CASE WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != ''
            AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE '' END, ', ',
            emp.firstname, ' ', CASE
            WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
            AND emp.middlename != '' AND emp.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
            ELSE '' END) `employee_name`")
            ->get_where($this->tbl_employees . " emp", array("emp.id" => $employee_id))
            ->row();

        $resultSet["data"] = $this->getTimeAdjustmentRequestData($employee_id, $timesheet_id);
        $resultSet["modal"] = $this->load->view('gcctime/timesheet/master/modals/time_adjustments_list_modal', $resultSet, true);
        return $resultSet;
    }

    public function getTimesheetMoreDetails($timesheet_id, $employee_id, $date)
    {
        $alteredShiftSchedule = $this->getCustomizedShiftScheduleByDate($date, $employee_id);
        $resultSet = array();
        $resultSet["shift_count"] = 0;
        $resultSet["shift_schedule"] = array();

        $this->db->select("ts.*, CONCAT(cemp.lastname,
            CASE WHEN cemp.suffix != 'N/A' AND cemp.suffix != 'NONE' AND cemp.suffix != ''
            AND cemp.suffix IS NOT NULL THEN CONCAT(' ', cemp.suffix) ELSE '' END, ', ',
            cemp.firstname, ' ', CASE
            WHEN cemp.middlename != 'N/A' AND cemp.middlename != 'NONE'
            AND cemp.middlename != '' AND cemp.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(cemp.middlename, 1, 1), '.')
            ELSE '' END) as last_updated_by_name, CONCAT(vemp.lastname,
            CASE WHEN vemp.suffix != 'N/A' AND vemp.suffix != 'NONE' AND vemp.suffix != ''
            AND vemp.suffix IS NOT NULL THEN CONCAT(' ', vemp.suffix) ELSE '' END, ', ',
            vemp.firstname, ' ', CASE
            WHEN vemp.middlename != 'N/A' AND vemp.middlename != 'NONE'
            AND vemp.middlename != '' AND vemp.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(vemp.middlename, 1, 1), '.')
            ELSE '' END) as verified_by_name");
        $this->db->from($this->tbl_timesheet . " ts");
        $this->db->join($this->tbl_employees . " cemp", "cemp.id = ts.last_updated_by", "left");
        $this->db->join($this->tbl_employees . " vemp", "vemp.id = ts.verified_by", "left");
        $this->db->where("ts.id", $timesheet_id);
        $timesheetRow = $this->db->get();
        $resultSet["timesheet"] = $timesheetRow->row();
        $resultSet["employee"] = $this->db->select("CONCAT(emp.lastname,
            CASE WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != ''
            AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE '' END, ', ',
            emp.firstname, ' ', CASE
            WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
            AND emp.middlename != '' AND emp.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
            ELSE '' END) `employee_name`")
            ->get_where($this->tbl_employees . " emp", array("emp.id" => $employee_id))
            ->row();

        $this->db->from($this->tbl_loa);
        $this->db->where("employee", $employee_id);
        $this->db->where("status", "Approved");
        $this->db->group_start();
        $this->db->where("DATE(`date_from`) <=", $date);
        $this->db->where("DATE(`date_to`) >=", $date);
        $this->db->or_where("DATE(`date_from`) >=", $date);
        $this->db->where("DATE(`date_to`) <=", $date);
        $this->db->group_end();
        $tempLoaReference = $this->db->get();
        $resultSet["loa_references"] = $tempLoaReference->result();
        $resultSet["to_references"] = $this->db
            ->select("travel_order.reference_no, GROUP_CONCAT(TRIM(destination.destination) SEPARATOR '||') `destination`, GROUP_CONCAT(TRIM(destination.purpose) SEPARATOR '||') purpose, GROUP_CONCAT(CONCAT(DATE_FORMAT(destination.date_from, '%m/%d/%Y %h:%i%p'), ' - ', DATE_FORMAT(destination.date_to, '%m/%d/%Y %h:%i%p')) SEPARATOR '||') as to_dates")
            ->join("gcceforms.travel_destination destination", "destination.travel_order_id = travel_order.id")
            ->join("gcceforms.travel_personnel personnel", "personnel.travel_order_id = travel_order.id", "LEFT")
            ->where("travel_order.status", "Approved")
            ->where("'{$date}' BETWEEN DATE(destination.date_from) AND DATE(destination.date_to)", null, false)
            ->group_start()
                ->where("travel_order.driver_id", $employee_id)
                ->or_where("personnel.employee_id", $employee_id)
            ->group_end()
            ->group_by("travel_order.id")
            ->get($this->tbl_TO . " travel_order")
            ->result();
        $resultSet["date"] = $date;
        $resultSet["overtime"] = array();
        if($timesheet_id && $timesheet_id > 0){
            $resultSet["overtime"] = $this->db
            ->select("ts_ot.*,
                        ot.reference_no,
                        ot.date_from,
                        ot.date_to, ot.purpose,
                        CONCAT(emp.lastname,
                        CASE
                            WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != '' AND emp.suffix IS NOT NULL
                                THEN CONCAT(' ', emp.suffix)
                            ELSE '' END, ', ',
                        emp.firstname, ' ', CASE
                            WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                AND emp.middlename != '' AND emp.middlename IS NOT NULL
                                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                            ELSE '' END) `requestor`")
            ->join($this->tbl_overtime . " ot", "ot.id = ts_ot.overtime_id")
            ->join($this->tbl_employees . " emp", "emp.id = ot.requested_by")
            ->where("ts_ot.timesheet_id", $timesheet_id)
            ->where("ot.status", "Approved")
            ->get($this->tbl_timesheet_overtime . " ts_ot")
            ->result();
        }else{
            $resultSet["overtime"] = $this->db
            ->select("ot.reference_no,
                        ot.date_from,
                        ot.date_to, ot.purpose,
                        CONCAT(emp.lastname,
                        CASE
                            WHEN emp.suffix != 'N/A' AND emp.suffix != 'NONE' AND emp.suffix != '' AND emp.suffix IS NOT NULL
                                THEN CONCAT(' ', emp.suffix)
                            ELSE '' END, ', ',
                        emp.firstname, ' ', CASE
                            WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                AND emp.middlename != '' AND emp.middlename IS NOT NULL
                                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.')
                            ELSE '' END) `requestor`")
            ->join($this->tbl_employees . " emp", "emp.id = ot.requested_by")
            ->where("ot.status", "Approved")
            ->where("ot.employee", $employee_id)
            ->where("'{$date}' BETWEEN DATE(ot.date_from) AND DATE(ot.date_to)", null, false)
            ->get($this->tbl_overtime . " ot")
            ->result();
        }
        
        $this->db->select("shift_resource.shift_resource");
        $this->db->from($this->tbl_employees." emp");
        $this->db->join($this->tbl_personnel." personnel", "personnel.biometric_id = emp.biometricno OR personnel.biometricno = emp.biometricno");
        $this->db->join($this->tbl_shift_schedule_resource." shift_resource", "shift_resource.shift_id = personnel.shift_id");
        $this->db->where("emp.id", $employee_id);
        $shiftResource = $this->db->get();
        if($shiftResource->num_rows() == 1){
            $tempRow = $shiftResource->row();
            $resource_ids = @unserialize($tempRow->shift_resource);
            if(is_array($resource_ids) && count($resource_ids) > 0){
                $weekday = strtolower(date('l', strtotime($date)));
                $this->db->from($this->tbl_shift_schedule_list);
                $this->db->where("weekday", $weekday);
                $this->db->where_in("id", $resource_ids);
                $tempShift = $this->db->get();
                if($tempShift->num_rows() == 1){
                    $shift_schedule_row = $tempShift->row();
                    if(isset($alteredShiftSchedule->schedule)){
                        foreach ($alteredShiftSchedule->schedule as $key => $value) {
                            if($value && $alteredShiftSchedule->has_shift == 1){
                                $shift_schedule_row->$key = $value;
                            }
                            if($alteredShiftSchedule->has_shift == 0){
                                $shift_schedule_row->$key = $value;
                            }
                        }
                    }
                    if(isset($shift_schedule_row->am_start, $shift_schedule_row->am_end) && ($shift_schedule_row->am_start === "00:00:00" && $shift_schedule_row->am_end === "00:00:00")){
                        $shift_schedule_row->am_start = null;
                        $shift_schedule_row->am_end = null;
                    }
                    if(isset($shift_schedule_row->pm_start, $shift_schedule_row->pm_end) && ($shift_schedule_row->pm_start === "00:00:00" && $shift_schedule_row->pm_end === "00:00:00")){
                        $shift_schedule_row->pm_start = null;
                        $shift_schedule_row->pm_end = null;
                    }

                    $resultSet["shift_count"] = $tempShift->num_rows();
                    $resultSet["shift_schedule"] = $shift_schedule_row;
                }
            }
        }

        $this->db->from($this->tbl_timesheet_customized_shift_schedule);
        $this->db->where("scheduled_date", date("Y-m-d", strtotime($date)));
        $qTempCustomShift = $this->db->get();
        if($qTempCustomShift->num_rows() > 0){
            foreach ($qTempCustomShift->result() as $key => $value) {
                $shifts = @unserialize($value->shift_id);
                if(is_array($shifts) && count($shifts) > 0){}
                /*** continue code here ***/
            }
        }

        $resultSet["holiday_references"] = $this->db
            ->where(array(
                "DATE(start_date) <=" => $date,
                "DATE(end_date) >=" => $date,
            ))
            ->get($this->tbl_tblholidays)
            ->row();

        $resultSet["modal"] = $this->load->view('gcctime/timesheet/master/modals/more_details_modal', $resultSet, true);
        return $resultSet;
    }

    public function importAndGenerateTimesheet()
    {
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $post = $this->arrayToStdClass($this->input->post());
        $inclusive_dates = explode("/", $post->inclusive_dates);
        $start = date('Y-m-d', strtotime($inclusive_dates[0]));
        $end = date('Y-m-d', strtotime($inclusive_dates[1]));
        $file = $this->arrayToStdClass($_FILES['file_import']);
        $non_existing = array();

        $not_in_personnel = array();
        $no_shifts = array();
        $emp_id_to_generate = array();
        $exist_in_timesheet = array();
        $this->db_debug = $this->db->db_debug;
        $possible_duplicate = array();

        $import_data = array(
            "filename" => $file->name,
            "mime_type" => $file->type,
            "start_date" => $start,
            "end_date" => $end,
            "remarks" => $post->remarks,
            "created_by" => $logged_in_user_emp_id,
            "import_type" => $post->type
        );

        $resultSet = array();
        $invalidRecords = array();
        $invalidCtr = 0;

        $this->db->trans_begin();
        $this->db->insert($this->tbl_timesheet_imports, $import_data);
        $import_insert_id = $this->db->insert_id();

        $upload = $this->do_upload('./uploads/timesheet/imports/' . $import_insert_id, '*', 'file_import');
        if (!empty($upload->error)) {
            return array("success" => false, "title" => "Upload Error", "message" => $upload->error);
        } else {
            $resultSet["upload_data"] = $upload->upload_data;
            $this->db->where("id", $import_insert_id);
            $this->db->set("filename", $upload->upload_data->file_name);
            $this->db->set("mime_type", $upload->upload_data->file_type);
            $this->db->set("path", "uploads/timesheet/imports/" . $import_insert_id . "/" . $upload->upload_data->file_name);
            $this->db->update($this->tbl_timesheet_imports);
            $this->db->reset_query();
        }

        if ($post->type === "attendance") {
            if ($upload->upload_data->file_ext === ".dat" || $upload->upload_data->file_ext === ".txt") {
                $contents = file_get_contents($upload->upload_data->full_path);
                $lines = explode("\n", $contents);

                foreach ($lines as $line) {
                    $parts = preg_split("/[\t]/", $line);
                    $_parts = array_filter($parts, function ($value) {
                        return !is_null($value) && $value !== '';
                    });

                    if (!empty($_parts)) {
                        $biometric_id = trim($parts[0]);
                        $datetime = date('Y-m-d H:i:s', strtotime(trim($parts[1])));
                        $tempTime = date('H:i', strtotime(trim($parts[1])));
                        $date = date('Y-m-d', strtotime(trim($parts[1])));

                        $resultResponse = $this->setAttendanceLogImportData([ 
                            "biometric_id" => $biometric_id, 
                            "datetime" => $datetime, 
                            "temptime" => $tempTime, 
                            "date" => $date,
                            "start" => $start,
                            "end" => $end,
                            "device_id" => $post->device_id,
                            "insert_id" => $import_insert_id,
                            "user_id" => $logged_in_user_emp_id
                        ]);

                        if($resultResponse !== false && isset($resultResponse["response"]) && $resultResponse["response"] === true){
                            if(isset($resultResponse["shift_id"]) && intval($resultResponse["shift_id"]) > 0){
                                if(!in_array($resultResponse["emp_id"], $emp_id_to_generate)){ array_push($emp_id_to_generate, $resultResponse["emp_id"]); }
                            } else {
                                if (!in_array($biometric_id, $no_shifts)) { array_push($no_shifts, $biometric_id); }
                            }
                        } elseif ($resultResponse !== false && isset($resultResponse["invalid_entries"]) && !empty($resultResponse["invalid_entries"])) {
                            $invalidEntries = (object) $resultResponse["invalid_entries"];
                            $invalidRecords[$invalidEntries->emp_id]["biometric_id"] = $invalidEntries->biometric_id;
                            $invalidRecords[$invalidEntries->emp_id]["employee_name"] = $invalidEntries->employee_name;
                            if(!isset($invalidRecords[$invalidEntries->emp_id]["dates"])){ $invalidRecords[$invalidEntries->emp_id]["dates"] = array(); }
                            $invalidRecords[$invalidEntries->emp_id]["dates"][] = date("Y/m/d H:i", strtotime($datetime));
                            $invalidCtr++;
                        } elseif ($resultResponse !== false && isset($resultResponse["employee_not_found"]) && $resultResponse["employee_not_found"] === true){
                            if (!in_array($biometric_id, $non_existing)) { array_push($non_existing, $biometric_id); }
                        }
                    }
                }
            } elseif (in_array($upload->upload_data->file_ext, [".xls", ".xlsx"])) {
                $file_path = $upload->upload_data->full_path;
                $objPHPExcel = PHPExcel_IOFactory::load($file_path);
                $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $arrayCount = count($allDataInSheet);

                if (intval($arrayCount) >= 1) {
                    for ($i = 2; $i <= $arrayCount; $i++) {
                        $biometric_id = $allDataInSheet[$i]["A"];
                        $datetime = date('Y-m-d H:i', strtotime($allDataInSheet[$i]["D"]));
                        $tempTime = date('H:i', strtotime($allDataInSheet[$i]["D"]));
                        $date = date('Y-m-d', strtotime($allDataInSheet[$i]["D"]));

                        $resultResponse = $this->setAttendanceLogImportData([ 
                            "biometric_id" => $biometric_id, 
                            "datetime" => $datetime, 
                            "temptime" => $tempTime, 
                            "date" => $date,
                            "start" => $start,
                            "end" => $end,
                            "device_id" => $post->device_id,
                            "insert_id" => $import_insert_id,
                            "user_id" => $logged_in_user_emp_id
                        ]);

                        if($resultResponse !== false && isset($resultResponse["response"]) && $resultResponse["response"] === true){
                            if(isset($resultResponse["shift_id"]) && intval($resultResponse["shift_id"]) > 0){
                                if(!in_array($resultResponse["emp_id"], $emp_id_to_generate)){ array_push($emp_id_to_generate, $resultResponse["emp_id"]); }
                            } else {
                                if (!in_array($biometric_id, $no_shifts)) { array_push($no_shifts, $biometric_id); }
                            }
                        } elseif ($resultResponse !== false && isset($resultResponse["invalid_entries"]) && !empty($resultResponse["invalid_entries"])) {
                            $invalidEntries = (object) $resultResponse["invalid_entries"];
                            $invalidRecords[$invalidEntries->emp_id]["biometric_id"] = $invalidEntries->biometric_id;
                            $invalidRecords[$invalidEntries->emp_id]["employee_name"] = $invalidEntries->employee_name;
                            if(!isset($invalidRecords[$invalidEntries->emp_id]["dates"])){ $invalidRecords[$invalidEntries->emp_id]["dates"] = array(); }
                            $invalidRecords[$invalidEntries->emp_id]["dates"][] = date("Y/m/d H:i", strtotime($datetime));
                            $invalidCtr++;
                        } elseif ($resultResponse !== false && isset($resultResponse["employee_not_found"]) && $resultResponse["employee_not_found"] === true){
                            if (!in_array($biometric_id, $non_existing)) { array_push($non_existing, $biometric_id); }
                        }
                    }
                }
            } elseif ($upload->upload_data->file_ext === ".csv") {
                $file_path = $upload->upload_data->full_path;
                $contents = file_get_contents($file_path);
                $lines = explode("\n", $contents);

                if (sizeof($lines) >= 2) {
                    array_splice($lines, 0, 1);
                    foreach ($lines as $line) {
                        if (empty($line)) { continue; }
                        $parts = explode(",", trim($line));

                        if (!empty($parts)) {
                            $biometric_id = trim($parts[0]);
                            $datetime = date('Y-m-d H:i:s', strtotime(trim($parts[3])));
                            $tempTime = date('H:i', strtotime(trim($parts[3])));
                            $date = date('Y-m-d', strtotime(trim($parts[3])));

                            $resultResponse = $this->setAttendanceLogImportData([ 
                                "biometric_id" => $biometric_id, 
                                "datetime" => $datetime, 
                                "temptime" => $tempTime, 
                                "date" => $date,
                                "start" => $start,
                                "end" => $end,
                                "device_id" => $post->device_id,
                                "insert_id" => $import_insert_id,
                                "user_id" => $logged_in_user_emp_id
                            ]);

                            if($resultResponse !== false && isset($resultResponse["response"]) && $resultResponse["response"] === true){
                                if(isset($resultResponse["shift_id"]) && intval($resultResponse["shift_id"]) > 0){
                                    if(!in_array($resultResponse["emp_id"], $emp_id_to_generate)){ array_push($emp_id_to_generate, $resultResponse["emp_id"]); }
                                } else {
                                    if (!in_array($biometric_id, $no_shifts)) { array_push($no_shifts, $biometric_id); }
                                }
                            } elseif ($resultResponse !== false && isset($resultResponse["invalid_entries"]) && !empty($resultResponse["invalid_entries"])) {
                                $invalidEntries = (object) $resultResponse["invalid_entries"];
                                $invalidRecords[$invalidEntries->emp_id]["biometric_id"] = $invalidEntries->biometric_id;
                                $invalidRecords[$invalidEntries->emp_id]["employee_name"] = $invalidEntries->employee_name;
                                if(!isset($invalidRecords[$invalidEntries->emp_id]["dates"])){ $invalidRecords[$invalidEntries->emp_id]["dates"] = array(); }
                                $invalidRecords[$invalidEntries->emp_id]["dates"][] = date("Y/m/d H:i", strtotime($datetime));
                                $invalidCtr++;
                            } elseif ($resultResponse !== false && isset($resultResponse["employee_not_found"]) && $resultResponse["employee_not_found"] === true){
                                if (!in_array($biometric_id, $non_existing)) { array_push($non_existing, $biometric_id); }
                            }
                        }
                    }
                }
            }

            if (!empty($emp_id_to_generate)) {
                $interval = DateInterval::createFromDateString('1 day');

                $dateStart = new DateTime($start);
                $dateEnd = new DateTime($end);
                $dateEnd->modify("+1 day");

                $period = new DatePeriod($dateStart, $interval, $dateEnd);
                foreach ($period as $dt) {
                    $date = $dt->format("Y-m-d");
                    $create = $this->create($date, 1, $emp_id_to_generate, $import_insert_id);
                    if (sizeof($create["updatedTimesheets"]) >= 1) {
                        $exist_in_timesheet = array_unique(array_merge($create["updatedTimesheets"], $exist_in_timesheet));
                    }
                }
            }

            $resultSet["success"] = true;
            $resultSet["message"] = "Attendance successfully imported & Timesheet was successfully generated.";
            $resultSet["title"] = "Import & Generate Successful.";
            $resultSet["invalid_records"] = $invalidRecords;
            $resultSet["invalid_count"] = $invalidCtr;
        } else {
            // import timesheet function from excel template
            $file_path = $upload->upload_data->full_path;
            $objPHPExcel = PHPExcel_IOFactory::load($file_path);
            $rows = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            $rows_count = count($rows);
            $timesheet = array();
            $tempAttendanceData = array();

            $night_diff_cfg = $this->db->get_where($this->tbl_time_parameters, array("param_name" => "NIGHT_DIFF_PARAMS"))->row();
            $this->db->reset_query();

            if (intval($rows_count) >= 2) {
                for ($i = 2; $i <= $rows_count; $i++) {
                    if(isset($rows[$i]["A"], $rows[$i]["B"]) && ($rows[$i]["A"] && $rows[$i]["B"])){
                        $biometric = $rows[$i]["A"];
                        $date = date('Y-m-d', strtotime($rows[$i]["B"]));
                        $am_in = (isset($rows[$i]["C"]) && $rows[$i]["C"])? $rows[$i]["C"]: null;
                        $am_out = (isset($rows[$i]["D"]) && $rows[$i]["D"])? $rows[$i]["D"]: null;
                        $pm_in = (isset($rows[$i]["E"]) && $rows[$i]["E"])? $rows[$i]["E"]: null;
                        $pm_out = (isset($rows[$i]["F"]) && $rows[$i]["F"])? $rows[$i]["F"]: null;
                        $ot = (isset($rows[$i]["G"]) && $rows[$i]["G"])? $rows[$i]["G"]: 0;
                        $ndot = (isset($rows[$i]["H"]) && $rows[$i]["H"])? $rows[$i]["H"]: 0;

                        $maxPayrollDate = $this->getPayrollMaxDate($biometric);
                        $isValidDate = $maxPayrollDate !== false ? strtotime($date) > strtotime($maxPayrollDate) : false;

                        $date_check = $date;
                        $weekday = strtolower(date('l', strtotime($date)));
                        $ot = floatval($ot);
                        $ndot = floatval($ndot);

                        $tempAmIn = ($am_in)? date("H:i:s", strtotime($am_in)): null;
                        $tempAmOut = ($am_out)? date("H:i:s", strtotime($am_out)): null;
                        $tempPmIn = ($pm_in)? date("H:i:s", strtotime($pm_in)): null;
                        $tempPmOut = ($pm_out)? date("H:i:s", strtotime($pm_out)): null;

                        /*** attendance rework ***/
                        $next_day = new DateTime($date);
                        $next_day->modify("+1 day");
                        $tempAlteredDate = $next_day->format("Y-m-d");
                        $isNextDay = false;

                        $_am_in = $tempAmIn ? date("Y-m-d H:i:s", strtotime($date . " " . $tempAmIn)): null;
                        $_am_out = $tempAmOut ? date("Y-m-d H:i:s", strtotime($date . " " . $tempAmOut)): null;
                        $_pm_in = $tempPmIn ? date("Y-m-d H:i:s", strtotime($date . " " . $tempPmIn)): null;
                        $_pm_out = $tempPmOut ? date("Y-m-d H:i:s", strtotime($date . " " . $tempPmOut)): null;

                        if(($tempAmIn && $tempAmOut) && (strtotime($tempAmOut) < strtotime($tempAmIn))){
                            $_am_out = date("Y-m-d H:i", strtotime($tempAlteredDate . " " . $tempAmOut));
                            $isNextDay = true;
                        }
                        
                        if(($tempAmOut && $tempPmIn) && (strtotime($tempPmIn) < strtotime($tempAmOut)) || $isNextDay === true){
                            $_pm_in = date("Y-m-d H:i", strtotime($tempAlteredDate . " " . $tempPmIn));
                            $isNextDay = true;
                        }
                        
                        if(($tempPmIn && $tempPmOut) && (strtotime($tempPmOut) < strtotime($tempPmIn)) || $isNextDay === true){
                            $_pm_out = date("Y-m-d H:i", strtotime($tempAlteredDate . " " . $tempPmOut));
                            $isNextDay = true;
                        }
                        /*** attendance rework ***/

                        $currentDateTime = array();
                        if($tempAmIn && $tempAmIn !== null){  $currentDateTime[] = date("Y-m-d H:i:s", strtotime($_am_in)); }
                        if($tempAmOut && $tempAmOut !== null){ $currentDateTime[] = date("Y-m-d H:i:s", strtotime($_am_out)); }
                        if($tempPmIn && $tempPmIn !== null){ $currentDateTime[] = date("Y-m-d H:i:s", strtotime($_pm_in)); }
                        if($tempPmOut && $tempPmOut !== null){ $currentDateTime[] = date("Y-m-d H:i:s", strtotime($_pm_out)); }

                        if (!(strtotime($date) >= strtotime($start) && strtotime($date) <= strtotime($end))) { continue; }

                        $employee = $this->getExistingEmployeeePersonnel($biometric);
                        if($employee !== false && $employee->num_rows() == 1){
                            $empRow = $employee->row();
                            $alteredShifts = $this->getCustomizedShiftScheduleByDate($date, $empRow->emp_id);
                            $getHourlyTimeSheet = $this->getEmployeePerHourShashPartimer($empRow->emp_id);

                            $timesheet_exist = $this->db
                            ->where("emp_id", $empRow->emp_id)
                            ->where("date", $date_check)
                            ->get($this->tbl_timesheet)
                            ->row();

                            $overtime = $this->db
                                ->get_where($this->tbl_overtime,
                                array(
                                    "employee" => $empRow->emp_id,
                                    "DATE(date_from)" => $date,
                                    "status" => "Approved"
                                    )
                                )->result();

                            $hasOT = sizeof($overtime) >= 1 ? 1 : 0;
                            $this->db->reset_query();

                            $flexible = intval($empRow->is_flexi) == 1 ? 1: 0;
                            $shift_id = $empRow->shift_id;
                            $no_shift_schedule = false;

                            $tempHoliday = (object) $this->getCurrentDateIsHoliday($date);
                            $isHoliday = $tempHoliday->is_holiday ? 1: 0;
                            $payRateId = $tempHoliday->is_holiday && $tempHoliday->payrate_id ? $tempHoliday->payrate_id: 0;

                            $employee_time_sheet = new StdClass();
                            $employee_time_sheet->emp_id = $empRow->emp_id;
                            $employee_time_sheet->date = $date;
                            $employee_time_sheet->weekday = $weekday;
                            
                            $employee_time_sheet->is_holiday = $isHoliday;
                            $employee_time_sheet->payrate_id = $payRateId;

                            $employee_time_sheet->am_in = $tempAmIn;
                            $employee_time_sheet->am_out = $tempAmOut;
                            $employee_time_sheet->am_late = 0;
                            $employee_time_sheet->am_ut = 0;
                            $employee_time_sheet->am_time_rendered = 0;

                            $employee_time_sheet->pm_in = $tempPmIn;
                            $employee_time_sheet->pm_out = $tempPmOut;
                            $employee_time_sheet->pm_late = 0;
                            $employee_time_sheet->pm_ut = 0;
                            $employee_time_sheet->pm_time_rendered = 0;

                            $employee_time_sheet->total_late = 0;
                            $employee_time_sheet->total_ut = 0;
                            $employee_time_sheet->total_time_rendered = 0;

                            $employee_time_sheet->is_flexi = $flexible;
                            $employee_time_sheet->scrub_status = 0;
                            $employee_time_sheet->comments = null;

                            $employee_time_sheet->has_shift = 1;
                            $employee_time_sheet->shift_am_start = null;
                            $employee_time_sheet->shift_am_end = null;
                            $employee_time_sheet->shift_pm_start = null;
                            $employee_time_sheet->shift_pm_end = null;

                            $employee_time_sheet->timesheet_imports_id = $import_insert_id;
                            $employee_time_sheet->has_overtime = $hasOT;

                            $employee_time_sheet->is_manual = 1;
                            $employee_time_sheet->manual_mode = "import";
                            $employee_time_sheet->manual_by = $logged_in_user_emp_id;

                            $employee_time_sheet->total_accredited_ot_hrs = empty($ot) ? 0 : $ot;
                            $employee_time_sheet->total_accredited_ndiff_ot_hrs = empty($ndot) ? 0 : $ndot;

                            $tempResource = array();
                            $shift_resource_array = array();

                            $shift_resource = $this->getShiftResource($shift_id);
                            if(isset($shift_resource->shift_resource) && $shift_resource->shift_resource){
                                $tempResource = $shift_resource->shift_resource;
                                $tempResource = unserialize($tempResource);

                                $shift_resource_array = array_map(function ($item) {
                                    return intval($item);
                                }, $tempResource);
                            }

                            $schedule = $this->getScheduleList($weekday, $shift_resource_array);
                            /*** altered shift schedule from custom shift `start` ***/
                            /*** $tempAlteredIndexId = "shift-id_{$shift_id}"; ***/
                            $alteredCustomShiftId = 0;
                            $alteredHasShiftSchedule = 1;

                            if(isset($alteredShifts) && $alteredShifts && count(get_object_vars($alteredShifts)) > 0 && isset($alteredShifts->has_shift)){
                                $ctrAlteredSchedule = false;
                                $tempHasShift = $alteredShifts->has_shift;
                                $alteredHasShiftSchedule = intval($tempHasShift);

                                foreach ($alteredShifts->schedule as $kkx => $vvx) {
                                    if(isset($schedule->{$kkx}) && $schedule->{$kkx} && $schedule->{$kkx} !== $vvx && $vvx !== null && intval($tempHasShift) == 1){
                                        $schedule->{$kkx} = $vvx;
                                        $ctrAlteredSchedule = true;
                                    }
                                    if(intval($tempHasShift) == 0){
                                        $schedule->{$kkx} = null;
                                        $ctrAlteredSchedule = true;
                                    }
                                }
                                if($ctrAlteredSchedule === true && $alteredShifts->custom_shift_id !== "0"){
                                    $alteredCustomShiftId = $alteredShifts->custom_shift_id;
                                }
                            }

                            $_has_shift = $alteredCustomShiftId !== 0? intval($alteredHasShiftSchedule): 1;
                            $employee_time_sheet->has_shift = $_has_shift;
                            $employee_time_sheet->custom_shift_id = $alteredCustomShiftId;

                            $employee_time_sheet->overtime_in = null;
                            $employee_time_sheet->overtime_out = null;
                            $employee_time_sheet->paid_holiday = 0;
                            /*** altered shift schedule from custom shift `end` ***/

                            $am_start = !empty($schedule) ? $schedule->am_start : null;
                            $am_end = !empty($schedule) ? $schedule->am_end : null;
                            $pm_start = !empty($schedule) ? $schedule->pm_start : null;
                            $pm_end = !empty($schedule) ? $schedule->pm_end : null;

                            if (($am_start === null && $am_end === null && $pm_start === null && $pm_end === null) || empty($schedule)) {
                                $no_shift_schedule = true;
                            }

                            if ($no_shift_schedule === true) {
                                $employee_time_sheet->scrub_status = 3;
                                $employee_time_sheet->has_shift = 0;
                                $employee_time_sheet->comments = "[System Generated]: No shift schedule detected.";
                            } else {
                                $employee_time_sheet->shift_am_start = $am_start;
                                $employee_time_sheet->shift_am_end = $am_end;
                                $employee_time_sheet->shift_pm_start = $pm_start;
                                $employee_time_sheet->shift_pm_end = $pm_end;
                            }

                            $toArray = (array) $getHourlyTimeSheet;
                            if(is_array($toArray) && count($toArray) > 0){ $employee_time_sheet->is_tagged_hourly = true; }

                            $updatedTimeSheet = $this->updateTimesheetShiftComputation($employee_time_sheet, false, $night_diff_cfg);
                            if(is_array($toArray) && count($toArray) > 0){
                                if(isset($employee_time_sheet->is_tagged_hourly)){ unset($employee_time_sheet->is_tagged_hourly); }
                                $updatedTimeSheet = array_merge((array) $updatedTimeSheet, $toArray);
                            }

                            $tempMergedTimeSheet = array_merge((array) $employee_time_sheet, (array) $updatedTimeSheet);
                            $employee_time_sheet = (object) $tempMergedTimeSheet;

                            if (sizeof((array) $timesheet_exist) >= 1 && $isValidDate === true){
                                $tempEmployeeData = (object) $this->core_layout->getEmployeeData($empRow->emp_id);
                                $tempName = isset($tempEmployeeData->display_name_1) && $tempEmployeeData->display_name_1 ? $tempEmployeeData->display_name_1: "No assigned name";
                                $timesheet_exist->emp_name = $tempName;
                                $employee_time_sheet->emp_name = $tempName;

                                array_push($possible_duplicate, array(
                                    "current" => $timesheet_exist,
                                    "changes" => $employee_time_sheet,
                                    "changes_str" => json_encode($employee_time_sheet)
                                ));
                            }else{
                                if(sizeof((array) $employee_time_sheet) >= 1 && !empty($currentDateTime)){
                                    $this->db->from($this->tbl_timesheet);
                                    $this->db->where("emp_id", $employee_time_sheet->emp_id);
                                    $this->db->where("date", $employee_time_sheet->date);
                                    foreach ($currentDateTime as $datetime) {
                                        $this->db->group_start();
                                        $timeAttendance = date("H:i:s", strtotime($datetime));
                                        $this->db->where("am_in", $timeAttendance);
                                        $this->db->or_where("am_out", $timeAttendance);
                                        $this->db->or_where("pm_in", $timeAttendance);
                                        $this->db->or_where("pm_out", $timeAttendance);
                                        $this->db->group_end();
                                    }
                                    $tempQuery = $this->db->get();
                                    if($tempQuery->num_rows() == 0 && $isValidDate === true){
                                        array_push($timesheet, $employee_time_sheet);
                                    }elseif($tempQuery->num_rows() == 0 && $isValidDate === false){
                                        $invalidRecords[$employee_time_sheet->emp_id]["biometric_id"] = $empRow->biometric_id;
                                        $invalidRecords[$employee_time_sheet->emp_id]["employee_name"] = $empRow->employee_name;
                                        $invalidRecords[$employee_time_sheet->emp_id]["dates"][] = date("Y/m/d", strtotime($date));
                                        $invalidCtr++;
                                    }
                                }
                            }

                            if(!empty($currentDateTime)){
                                foreach ($currentDateTime as $value) {
                                    $tempRow = new stdClass();
                                    $tempRow->biometric_id = $empRow->biometric_id;
                                    $tempRow->datetime = $value;
                                    $tempAttendanceData[] = $tempRow;
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($timesheet)) {
                $this->db->insert_batch($this->tbl_timesheet, $timesheet);
            }

            $resultSet["success"] = true;
            $resultSet["message"] = "Timesheet was successfully imported.";
            $resultSet["title"] = "Import Successful.";
            $resultSet["invalid_records"] = $invalidRecords;
            $resultSet["invalid_count"] = $invalidCtr;
        }

        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
            $resultSet["success"] = false;
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["title"] = "An error occurred.";
        }

        $this->db->db_debug = $this->db_debug;

        $not_found_hris = array();
        foreach ($non_existing as $biometric) {
            $in_employees = $this->db->where("emp.biometricno", intval($biometric))->count_all_results($this->tbl_employees . " emp");
            array_push($not_found_hris, array("biometric" => $biometric, "in_employees" => $in_employees));
        }

        $no_shifts_name = array();
        foreach ($no_shifts as $no_shift) {
            $employee = $this->db
                ->select("CONCAT(emp.firstname, ' ', emp.lastname) employee_name")
                ->where("personnel.biometricno", $no_shift)
                ->join($this->tbl_personnel . " personnel", "personnel.biometricno = emp.biometricno", "INNER")
                ->get($this->tbl_employees . " emp")
                ->row();
            if (!empty($employee)) {
                array_push($no_shifts_name, $employee->employee_name);
            }
        }

        if (sizeof($not_found_hris) <= 0 && sizeof($no_shifts) <= 0) {
            $this->db->where("id", $import_insert_id);
            $this->db->set("resolved", 1);
            $this->db->update($this->tbl_timesheet_imports);
            $this->db->reset_query();
        }

        $resultSet["non_existing"] = $not_found_hris;
        $resultSet["no_shifts"] = $no_shifts;
        $resultSet["no_shifts_name"] = $no_shifts_name;
        $resultSet["not_in_personnel"] = $not_in_personnel;
        $resultSet["emp_id_to_generate"] = $emp_id_to_generate;
        $resultSet["exist_in_timesheet"] = $exist_in_timesheet;
        $resultSet["timesheet_imports_id"] = $import_insert_id;
        $resultSet["start"] = $start;
        $resultSet["end"] = $end;
        $resultSet["possible_duplicate"] = $possible_duplicate;
        $resultSet["type"] = $post->type;
        return $resultSet;
    }

    protected function getPayrollMaxDate($biometric_id=null){
        if($biometric_id){
            $this->db->select("MAX(ps.date_end) as max_date");
            $this->db->from($this->tbl_employees." emp");
            $this->db->join($this->tbl_payroll_sheet." ps", "ps.emp_id = emp.id AND ps.posted = 1", "LEFT");
            $this->db->where("emp.biometricno", $biometric_id);
            $this->db->group_by("emp.id");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() == 1){ return $qTemp->row()->max_date; }
            else{ return false; }
        }else{ return false; }
        
    }

    protected function getExistingEmployeeePersonnel($biometric_id=null){
        if($biometric_id){
            return $this->db
            ->select("personnel.biometric_id, personnel.shift_id, personnel.is_flexi, emp.id emp_id, emp.lastname, emp.firstname,
                UCASE(CONCAT(emp.lastname,
                    CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''  END, ', ',
                    emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                    AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END)) as employee_name")
            ->where("personnel.biometric_id", $biometric_id)
            ->join($this->tbl_personnel . " personnel", "emp.biometricno = personnel.biometric_id", "LEFT")
            ->get($this->tbl_employees . " emp");
        }else{ return false; }
    }

    protected function setAttendanceLogImportData(array $parameters){
        if (empty($parameters['biometric_id']) || empty($parameters['datetime']) || empty($parameters['temptime']) || empty($parameters['date']) ||
        empty($parameters['start']) || empty($parameters['end'])){
            return false;
        }
        $result = array();
        $maxPayrollDate = $this->getPayrollMaxDate($parameters['biometric_id']);
        $isValidDate = $maxPayrollDate !== false ? strtotime($parameters['date']) > strtotime($maxPayrollDate) : false;
        $employee = $this->getExistingEmployeeePersonnel($parameters['biometric_id']);
        if ($employee !== false && $employee->num_rows() == 1){
            $empRow = $employee->row();
            if (strtotime($parameters['date']) >= strtotime($parameters['start']) && strtotime($parameters['date']) <= strtotime($parameters['end']) &&
                $isValidDate){
                $attendanceExist = $this->db
                    ->where('biometric_id', $empRow->biometric_id)
                    ->where('DATE(datetime)', $parameters['date'])
                    ->like('TIME(datetime)', $parameters['temptime'], 'after')
                    ->count_all_results($this->tbl_attendance);
                if (intval($attendanceExist) <= 0){
                    $addedAttendance = $this->db->insert($this->tbl_attendance, [
                        'biometric_id' => $empRow->biometric_id,
                        'state' => 1,
                        'device_id' => $parameters['device_id'],
                        'longtitude' => '',
                        'latitude' => '',
                        'temp_id' => 0,
                        'is_custom' => 1,
                        'is_custom_by' => $parameters['user_id'],
                        'approved_by' => 0,
                        'approval_status' => 0,
                        'datetime' => $parameters['datetime'],
                        'timesheet_imports_id' => $parameters['insert_id'],
                    ]);
                    $result['response'] = $addedAttendance && $this->db->affected_rows() > 0;
                    $result["emp_id"] = $empRow->emp_id;
                    $result["shift_id"] = $empRow->shift_id;
                } else { $result['response'] = false; }
            } elseif (strtotime($parameters['date']) >= strtotime($parameters['start']) && strtotime($parameters['date']) <= strtotime($parameters['end']) &&
            $isValidDate === false){
                $result['response'] = false;
                $result['invalid_entries'] = [
                    'emp_id' => $empRow->emp_id,
                    'biometric_id' => $empRow->biometric_id,
                    'employee_name' => $empRow->employee_name,
                    'date' => $parameters['date'],
                ];
            }
        } else {
            $result['response'] = false;
            $result['employee_not_found'] = true;
        }
        return $result;
    }

    private function do_upload($upload_path, $allowed_types, $file)
    {
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = $allowed_types;

        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $this->upload->initialize($config);
        $results = array();

        if (!$this->upload->do_upload($file)) {
            $results = array('error' => $this->upload->display_errors(), 'upload_data' => null);
        } else {
            $results = array('error' => null, 'upload_data' => $this->upload->data());
        }

        return $this->arrayToStdClass($results);
    }

    public function saveNoEmployeeBiometricNo($timesheet_imports_id)
    {
        $post = $this->arrayToStdClass($this->input->post());
        $data = new StdClass();

        if (isset($post->no_employee_biometric_no)) {
            $data->no_employee_biometric_no = serialize($post->no_employee_biometric_no);
        }

        if (isset($post->noShiftsArray)) {
            $data->no_shifts = serialize($post->noShiftsArray);
        }

        if (isset($post->no_employee_biometric_no) || isset($post->noShiftsArray)) {
            if (sizeof($post->no_employee_biometric_no) <= 0 && sizeof($post->noShiftsArray) <= 0) {
                $data->resolved = 1;
            }
        }

        if (!isset($post->no_employee_biometric_no) && !isset($post->noShiftsArray)) {
            $data->resolved = 1;
        }

        $this->db->where("id", $timesheet_imports_id);
        return $this->db->update($this->tbl_timesheet_imports, $data);
    }

    public function getImportHistory()
    {
        $resultSet = array();
        $post = $this->arrayToStdClass($this->input->post());
        $dtCfg = $this->mod_util->getDatatablesConfigForPagination($post);

        $this->db->select("imports.*, CONCAT(emp.lastname, ' ', emp.firstname) creator");
        $this->db->join($this->tbl_employees . " emp", "emp.id = imports.created_by", "LEFT");

        if (intval($dtCfg->length) >= 1) {
            $this->db->limit($dtCfg->length, $dtCfg->start);
        }

        $this->db->order_by($dtCfg->order_column, $dtCfg->order_direction);

        $query = $this->db->get($this->tbl_timesheet_imports . " imports");
        $data = $query->result();

        foreach ($data as $datum) {
            if (!empty($datum->no_employee_biometric_no)) {
                $biometric_no = unserialize($datum->no_employee_biometric_no);
                $datum->biometric_no = array_map(function ($row) use ($datum) {
                    $possible_match_class = intval($row->in_employees) <= 0 ? 'm--hide' : '';
                    $look_up_class = !empty($possible_match_class) ? '' : 'm--hide';
                    return "<div class='dropdown d-inline'>
                                    <span class='m-badge m-badge--wide m--margin-right-5 m--margin-bottom-5 m--font-boldest
                                                 m-badge--clickable'
                                          data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        $row->biometric
                                    </span>
                                    <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                        <h6 class='dropdown-header'
                                            style='padding: 0.5rem 1.3rem;'>
                                            <span class='m--font-boldest2'>$row->biometric</span>
                                        </h6>
                                        <a class='dropdown-item' href='javascript:void(0)'
                                           onclick='openAddEmployeeModal(" . $row->biometric . ",\"history\", " . $datum->id . ")'>
                                            Add Employee
                                        </a>
                                        <a class='dropdown-item $possible_match_class'
                                           href='javascript:void(0)' onclick='openPossibleMatchesModal($row->biometric, \"history\", $datum->id)'>
                                           $row->in_employees Possible Match(es).
                                        </a>
                                        <a class='dropdown-item $look_up_class' href='javascript:void(0)'
                                            onclick='openLookUpAndUpdateModal($row->biometric, \"history\", $datum->id)'>
                                            Look up & Update
                                        </a>
                                    </div>
                                </div>";
                }, $biometric_no);
            } else {
                $datum->biometric_no = array();
            }

            if (!empty($datum->no_shifts)) {
                $no_shifts = unserialize($datum->no_shifts);
                $datum->no_shifts = array_map(function ($biometric) use ($datum) {
                    $employee = $this->db
                        ->select("CONCAT(emp.firstname, ' ', emp.lastname) employee_name")
                        ->where("personnel.biometricno", $biometric)
                        ->join($this->tbl_personnel . " personnel", "personnel.biometricno = emp.biometricno", "INNER")
                        ->get($this->tbl_employees . " emp");

                    $tempHtml = "";
                    if($employee->num_rows() == 1){
                        $tempRow = $employee->row();
                        $tempHtml = "<div class='dropdown d-inline'>
                            <span class='m-badge m-badge--wide m--margin-right-5 m--margin-bottom-5 m--font-boldest
                                        m-badge--clickable' onclick='openAddShiftModal($biometric, \"history\", $datum->id)'
                                title='$tempRow->employee_name'>
                                $biometric
                            </span>
                        </div>";
                    }
                    return $tempHtml;
                }, $no_shifts);
            } else {
                $datum->no_shifts = array();
            }
        }

        $resultSet["data"] = $data;
        $resultSet["recordsFiltered"] = $this->importHistoryCount();
        $resultSet["recordsTotal"] = $this->importHistoryCount();
        $resultSet["dtCfg"] = $dtCfg;
        return $resultSet;
    }

    private function importHistoryCount()
    {
        $this->db->select("imports.*, CONCAT(emp.lastname, ' ', emp.firstname) creator");
        $this->db->join($this->tbl_employees . " emp", "emp.id = imports.created_by", "LEFT");
        return $this->db->count_all_results($this->tbl_timesheet_imports . " imports");
    }

    public function addExcludedEmployeeFromTimesheet()
    {
        $this->db->db_debug = false;
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $post = $this->arrayToStdClass($this->input->post());
        $reason = $post->reason;

        $employees = array_map(function ($employee) use ($reason, $logged_in_user_emp_id) {
            return array("emp_id" => $employee, "reason" => $reason, "created_by" => $logged_in_user_emp_id);
        }, $post->employees);

        $insert = $this->db->insert_batch($this->tbl_timesheet_excluded_employees, $employees);
        if ($insert) {
            $resultSet["success"] = true;
            $resultSet["message"] = "Employee(s) added to exclusion list successfully.";
            $resultSet["title"] = "Successfully Added.";
        } else {
            $resultSet["success"] = false;
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["title"] = "An error occurred.";
        }

        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $resultSet["count"] = $this->getExcludedEmployeesCount();
        $resultSet["count_message"] = $f->format($this->getExcludedEmployeesCount()) . "(<span class='m--font - boldest'>" . $this->getExcludedEmployeesCount() . "</span>) EMPLOYEES EXCLUDED";

        $this->db->db_debug = $this->db_debug;
        return $resultSet;
    }

    public function getExcludedEmployeeList()
    {
        $resultSet = array();
        $table = $this->tbl_timesheet_excluded_employees . " ex_emp";
        $post = $this->arrayToStdClass($this->input->post());
        $dtCfg = $this->mod_util->getDatatablesConfigForPagination($post);

        $this->db->select("ex_emp.*, CONCAT(emp.lastname,
                               CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''  END, ', ',
			                   emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
			                   AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END) `employee_name`,,
			                   IF(companies.id IS NULL, emp.company_id, companies.code) company, IF(position.id IS NULL, emp.position, position.name) position,
			                   emp.pic_filename, emp.id emp_id,
			                   CONCAT(emp2.firstname, ' ', CASE WHEN emp2.middlename != 'N/A' AND emp2.middlename != 'NONE'
			                   AND emp2.middlename !='' AND emp2.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp2.middlename, 1, 1), '.') ELSE '' END, ' ', emp2.lastname,
                               CASE WHEN emp2.suffix != 'N/A' AND emp2.suffix !='NONE' AND emp2.suffix !='' AND emp2.suffix IS NOT NULL THEN CONCAT(' ', emp2.suffix) ELSE ''  END) _created_by");
        $joinArr = array(
            array(
                "table" => $this->tbl_employees . " emp",
                "condition" => "emp.id = ex_emp.emp_id",
                "option" => "INNER",
            ),
            array(
                "table" => $this->tbl_tblcompanies . " companies",
                "condition" => "companies.id = emp.company_id",
                "option" => "LEFT",
            ),
            array(
                "table" => $this->tbl_tblposition . " position",
                "condition" => "position.id = emp.position",
                "option" => "LEFT",
            ),
            array(
                "table" => $this->tbl_employees . " emp2",
                "condition" => "emp2.id = ex_emp.created_by",
                "option" => "LEFT",
            ),
        );

        if (intval($dtCfg->length) >= 1) {
            $this->db->limit($dtCfg->length, $dtCfg->start);
        }

        $this->db->order_by($dtCfg->order_column, $dtCfg->order_direction);

        foreach ($joinArr as $join) {
            $this->db->join($join["table"], $join["condition"], $join["option"]);
        }

        $search = array();
        $search["field"] = "CONCAT(emp.lastname, emp.firstname, emp.middlename, emp2.lastname, emp2.firstname, emp2.middlename, ex_emp.reason)";
        $search["key"] = $dtCfg->search;
        $search["option"] = "BOTH";

        $this->db->like($search["field"], $search["key"], $search["option"]);

        $query = $this->db->get($table);
        $employees = $query->result();

        foreach ($employees as $employee) {
            $pic_path = base_url("uploads/files/images/employee_files/empcode_" . $employee->emp_id . "/" . $employee->pic_filename);
            $pic_url = file_exists($pic_path) ? $pic_path : base_url("assets/images/profile/no_image.jpg");
            $employee->pic_url = $pic_url;
        }

        $resultSet["data"] = $employees;
        $resultSet["recordsFiltered"] = $this->mod_util->getTableCount($table, NULL, $search, $joinArr);
        $resultSet["recordsTotal"] = $this->mod_util->getTableCount($table, NULL, $search, $joinArr);
        $resultSet["dtCfg"] = $dtCfg;

        return $resultSet;
    }

    public function deleteEmployeeFromExclusion($id, $multiple)
    {
        $resultSet = array();
        $this->db->db_debug = false;
        $message = "";

        if (intval($multiple) === 1) {
            $ids = $this->input->post("id");
            $this->db->where_in("id", $ids);
            $message = "Selected employees was successfully removed from exclusion list.";
        } else {
            $this->db->where("id", $id);
            $message = "Employee was successfully removed from exclusion list.";
        }

        if ($this->db->delete($this->tbl_timesheet_excluded_employees)) {
            $resultSet["success"] = true;
            $resultSet["message"] = $message;
            $resultSet["title"] = "Successfully removed.";
        } else {
            $resultSet["success"] = false;
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["title"] = "DB Error occurred.";
        }

        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $resultSet["count"] = $this->getExcludedEmployeesCount();
        $resultSet["count_message"] = $f->format($this->getExcludedEmployeesCount()) . "(<span class='m--font - boldest'>" . $this->getExcludedEmployeesCount() . "</span>) EMPLOYEES EXCLUDED";

        $this->db->db_debug = $this->db_debug;
        return $resultSet;
    }

    private function getExcludedEmployeesCount()
    {
        return $this->db->count_all_results($this->tbl_timesheet_excluded_employees);
    }

    public function getExcludedEmployeeDetail($id)
    {
        $table = $this->tbl_timesheet_excluded_employees . " ex_emp";
        $this->db->select("ex_emp.*, CONCAT(emp.lastname,
                               CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''  END, ', ',
			                   emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
			                   AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END) `employee_name`,,
			                   IF(companies.id IS NULL, emp.company_id, companies.code) company, IF(position.id IS NULL, emp.position, position.name) position,
			                   emp.pic_filename, emp.id emp_id,
			                   CONCAT(emp2.firstname, ' ', CASE WHEN emp2.middlename != 'N/A' AND emp2.middlename != 'NONE'
			                   AND emp2.middlename !='' AND emp2.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp2.middlename, 1, 1), '.') ELSE '' END, ' ', emp2.lastname,
                               CASE WHEN emp2.suffix != 'N/A' AND emp2.suffix !='NONE' AND emp2.suffix !='' AND emp2.suffix IS NOT NULL THEN CONCAT(' ', emp2.suffix) ELSE ''  END) _created_by");
        $joinArr = array(
            array(
                "table" => $this->tbl_employees . " emp",
                "condition" => "emp.id = ex_emp.emp_id",
                "option" => "INNER",
            ),
            array(
                "table" => $this->tbl_tblcompanies . " companies",
                "condition" => "companies.id = emp.company_id",
                "option" => "LEFT",
            ),
            array(
                "table" => $this->tbl_tblposition . " position",
                "condition" => "position.id = emp.position",
                "option" => "LEFT",
            ),
            array(
                "table" => $this->tbl_employees . " emp2",
                "condition" => "emp2.id = ex_emp.created_by",
                "option" => "LEFT",
            ),
        );

        foreach ($joinArr as $join) {
            $this->db->join($join["table"], $join["condition"], $join["option"]);
        }
        $this->db->where("ex_emp.id", $id);
        $query = $this->db->get($table);
        $row = $query->row();

        $pic_path = base_url("uploads/files/images/employee_files/empcode_" . $row->emp_id . "/" . $row->pic_filename);
        $pic_url = file_exists($pic_path) ? $pic_path : base_url("assets/images/profile/no_image.jpg");
        $row->pic_url = $pic_url;

        return $row;
    }

    public function updateExcludedEmployee($id)
    {
        $this->db->db_debug = false;
        $post = $this->arrayToStdClass($this->input->post());
        $exist = $this->db
            ->where("emp_id", $post->emp_id)
            ->where("id !=", $id)
            ->get($this->tbl_timesheet_excluded_employees)->row();
        $resultSet = array();

        if (sizeof((array) $exist) >= 1) {
            $resultSet["success"] = false;
            $resultSet["message"] = "Selected employee already in list.";
            $resultSet["title"] = "Employee Exist";
            $resultSet["toast"] = "warning";
        } else {
            $this->db->where("id", $id);
            $this->db->set($post);
            $updated = $this->db->update($this->tbl_timesheet_excluded_employees);

            if ($updated) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Details was successfully updated.";
                $resultSet["title"] = "Successfully updated.";
                $resultSet["toast"] = "success";
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "An error occurred.";
                $resultSet["toast"] = "error";
            }

            $resultSet["data"] = $this->getExcludedEmployeeDetail($id);
        }

        $this->db->db_debug = $this->db_debug;
        return $resultSet;
    }

    public function getExcludedEmployees()
    {
        $table = $this->tbl_timesheet_excluded_employees . " ex_emp";
        $this->db->select("ex_emp.*, CONCAT(emp.lastname,
                               CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''  END, ', ',
			                   emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
			                   AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END) `employee_name`,,
			                   IF(companies.id IS NULL, emp.company_id, companies.code) company, IF(position.id IS NULL, emp.position, position.name) position,
			                   emp.pic_filename, emp.id emp_id,
			                   CONCAT(emp2.firstname, ' ', CASE WHEN emp2.middlename != 'N/A' AND emp2.middlename != 'NONE'
			                   AND emp2.middlename !='' AND emp2.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp2.middlename, 1, 1), '.') ELSE '' END, ' ', emp2.lastname,
                               CASE WHEN emp2.suffix != 'N/A' AND emp2.suffix !='NONE' AND emp2.suffix !='' AND emp2.suffix IS NOT NULL THEN CONCAT(' ', emp2.suffix) ELSE ''  END) _created_by");
        $joinArr = array(
            array(
                "table" => $this->tbl_employees . " emp",
                "condition" => "emp.id = ex_emp.emp_id",
                "option" => "INNER",
            ),
            array(
                "table" => $this->tbl_tblcompanies . " companies",
                "condition" => "companies.id = emp.company_id",
                "option" => "LEFT",
            ),
            array(
                "table" => $this->tbl_tblposition . " position",
                "condition" => "position.id = emp.position",
                "option" => "LEFT",
            ),
            array(
                "table" => $this->tbl_employees . " emp2",
                "condition" => "emp2.id = ex_emp.created_by",
                "option" => "LEFT",
            ),
        );

        foreach ($joinArr as $join) {
            $this->db->join($join["table"], $join["condition"], $join["option"]);
        }

        $query = $this->db->get($table);
        $employees = $query->result();

        foreach ($employees as $employee) {
            $pic_path = base_url("uploads/files/images/employee_files/empcode_" . $employee->emp_id . "/" . $employee->pic_filename);
            $pic_url = file_exists($pic_path) ? $pic_path : base_url("assets/images/profile/no_image.jpg");
            $employee->pic_url = $pic_url;
        }

        return $employees;
    }

    public function acceptImportUpdates($timesheet_id, $timesheet_imports_id)
    {
        $this->db->db_debug = false;
        $post = $this->arrayToStdClass($this->input->post());
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $changes = $post->changes;
        $changes->updated_by_import = 1;
        $changes->last_updated_by = $logged_in_user_emp_id;
        $changes->last_updated_at = $this->today->format("Y-m-d H:i:s");
        $resultSet = array();
        $emp_name = $changes->emp_name;
        unset($changes->emp_name);

        $tempProps = array("am_in", "am_out", "pm_in", "pm_out", "shift_am_start", "shift_am_end", "shift_pm_start", "shift_pm_end");
        foreach ($tempProps as $value) {
            if(!$changes->$value){ $changes->$value = null; }
        }

        $this->db->where("id", $timesheet_id);
        $this->db->where("verified", 0);
        if ($this->db->update($this->tbl_timesheet, $changes)) {
            $resultSet["success"] = true;
            $resultSet["timesheet_id"] = $timesheet_id;
            $resultSet["changes"] = json_encode($changes);
            $resultSet["message"] = "Timesheet for <strong>$emp_name</strong> on <strong>" . date("m/d/Y", strtotime($changes->date)) . "</strong> was updated.";
            $resultSet["title"] = "Changes was accepted.";
            $resultSet["toast"] = "success";
        } else {
            $resultSet["success"] = false;
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["title"] = "An error occurred.";
            $resultSet["toast"] = "error";
        }

        $this->db->db_debug = $this->db_debug;
        return $resultSet;
    }

    public function test()
    {
        $file_path = "C:/xampp/htdocs/portaldev/uploads/timesheet/imports/1/TIMESHEET_TEMPLATE.xlsx";
        $objPHPExcel = PHPExcel_IOFactory::load($file_path);
        $rows = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        $rows_count = count($rows);
        $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
        $timesheet = array();

        if (intval($rows_count) >= 2) {
            for ($i = 2; $i <= $rows_count; $i++) {
                $biometric = $rows[$i]["A"];
                $date = date('Y-m-d', strtotime($rows[$i]["B"]));
                $weekday = strtolower(date('l', strtotime($date)));
                $date = $rows[$i]["B"];
                $am_in = $rows[$i]["C"];
                $am_out = $rows[$i]["D"];
                $pm_in = $rows[$i]["E"];
                $pm_out = $rows[$i]["F"];
                $ot = $rows[$i]["G"];
                $ndot = $rows[$i]["H"];

                if (strtotime($pm_out) < strtotime($am_in)) {
                    $next_day = new DateTime($date);
                    $next_day->modify("+1 day");
                    $pm_out = date("Y-m-d H:i", strtotime($next_day->format("Y-m-d") . " " . $timesheet->pm_out));
                } else {
                    $am_in = date("Y-m-d H:i", strtotime($date . " " . $am_in));
                    $am_out = date("Y-m-d H:i", strtotime($date . " " . $am_out));
                    $pm_in = date("Y-m-d H:i", strtotime($date . " " . $pm_in));
                    $pm_out = date("Y-m-d H:i", strtotime($date . " " . $pm_out));
                }

                $employee = $this->db
                    ->select("personnel.*, emp.id emp_id, emp.lastname, emp.firstname")
                    ->where("personnel.biometric_id", $biometric)
                    ->join($this->tbl_personnel . " personnel", "emp.biometricno = personnel.biometric_id", "LEFT")
                    ->get($this->tbl_employees . " emp")
                    ->row();

                $flexible = intval($employee->is_flexi) !== 0;
                $shift_id = $employee->shift_id;
                $no_shift_schedule = false;

                $employee_time_sheet = new StdClass();
                $employee_time_sheet->emp_id = $employee->id;
                $employee_time_sheet->date = $date;
                $employee_time_sheet->weekday = $weekday;

                $employee_time_sheet->am_in = $am_in;
                $employee_time_sheet->am_out = $am_out;
                $employee_time_sheet->am_late = 0;
                $employee_time_sheet->am_ut = 0;
                $employee_time_sheet->am_time_rendered = 0;

                $employee_time_sheet->pm_in = $pm_in;
                $employee_time_sheet->pm_out = $pm_out;
                $employee_time_sheet->pm_late = 0;
                $employee_time_sheet->pm_ut = 0;
                $employee_time_sheet->pm_time_rendered = 0;

                $employee_time_sheet->total_late = 0;
                $employee_time_sheet->total_ut = 0;
                $employee_time_sheet->total_time_rendered = 0;

                $employee_time_sheet->is_flexi = $flexible;
                $employee_time_sheet->scrub_status = 0;
                $employee_time_sheet->comments = null;

                $employee_time_sheet->has_shift = 1;
                $employee_time_sheet->shift_am_start = null;
                $employee_time_sheet->shift_am_end = null;
                $employee_time_sheet->shift_pm_start = null;
                $employee_time_sheet->shift_pm_end = null;

                // $employee_time_sheet->timesheet_imports_id = $timesheet_imports_id;
                // $employee_time_sheet->has_overtime = $hasOT;

                $employee_time_sheet->is_manual = 1;
                $employee_time_sheet->manual_mode = "import";
                $employee_time_sheet->manual_by = $logged_in_user_emp_id;

                $tempResource = array();
                $shift_resource_array = array();

                $shift_resource = $this->getShiftResource($shift_id);
                if(isset($shift_resource->shift_resource) && $shift_resource->shift_resource){
                    $tempResource = $shift_resource->shift_resource;
                    $tempResource = unserialize($tempResource);

                    $shift_resource_array = array_map(function ($item) {
                        return intval($item);
                    }, $tempResource);
                }

                /*** $shift_resource = $this->getShiftResource($shift_id);
                $shift_resource_array = array_map(function ($item) {
                    return intval($item);
                }, unserialize($shift_resource->shift_resource)); ***/

                $schedule = $this->getScheduleList($weekday, $shift_resource_array);
                $am_start = !empty($schedule) ? $schedule->am_start : null;
                $am_end = !empty($schedule) ? $schedule->am_end : null;
                $pm_start = !empty($schedule) ? $schedule->pm_start : null;
                $pm_end = !empty($schedule) ? $schedule->pm_end : null;

                if (($am_start === null && $am_end === null && $pm_start === null && $pm_end === null) || empty($schedule)) {
                    $no_shift_schedule = true;
                }

                $am_shift_only = (($am_start !== null && $am_end !== null) && ($pm_start === null && $pm_end === null));
                $pm_shift_only = (($am_start === null && $am_end === null) && ($pm_start !== null && $pm_end !== null));

                if ($no_shift_schedule === false) {
                    $am_in = strtotime($employee_time_sheet->am_in);
                    $am_out = strtotime($employee_time_sheet->am_out);
                    $pm_in = strtotime($employee_time_sheet->pm_in);
                    $pm_out = strtotime($employee_time_sheet->pm_out);

                    $_am_start = strtotime(date('Y-m-d H:i', strtotime($date . " " . $am_start)));
                    $_am_end = strtotime(date('Y-m-d H:i', strtotime($date . " " . $am_end)));
                    $_pm_start = strtotime(date('Y-m-d H:i', strtotime($date . " " . $pm_start)));
                    $_pm_end = strtotime(date('Y-m-d H:i', strtotime($date . " " . $pm_end)));

                    // START AM CALCULATION
                    if ($am_in >= $_am_end) {
                        $employee_time_sheet->am_late = 0;
                    } else {
                        if (($am_in > $_am_start) && $am_in) {
                            $employee_time_sheet->am_late = round(($am_in - $_am_start) / 60, 2);
                        }
                    }

                    if ($am_in >= $_am_end) {
                        $employee_time_sheet->am_ut = round(($_am_end - $_am_start) / 60, 2);
                    } else {
                        if (($am_out < $_am_end) && $am_out) {
                            $employee_time_sheet->am_ut = round(($_am_end - $am_out) / 60, 2);
                        }
                    }

                    if ($am_in >= $_am_end) {
                        $employee_time_sheet->am_time_rendered = 0;
                    } else {
                        if ($am_in && $am_out) {
                            $am_time_rendered = ($am_out > $_am_end ? $_am_end : $am_out) - ($am_in < $_am_start ? $_am_start : $am_in);
                            $employee_time_sheet->am_time_rendered = round(($am_time_rendered) / 60, 2);
                        }
                    }
                    // END AM CALCULATION


                    // START PM CALCULATION
                    if ($pm_in >= $_pm_end) {
                        $employee_time_sheet->pm_late = 0;
                    } else {
                        if (($pm_in > $_pm_start) && $pm_in) {
                            $employee_time_sheet->pm_late = round(($pm_in - $_pm_start) / 60, 2);
                        }
                    }

                    if ($pm_in >= $_pm_end) {
                        $employee_time_sheet->pm_ut = round(($_pm_end - $_pm_start) / 60, 2);;
                    } else {
                        if (($pm_out < $_pm_end) && $pm_out) {
                            $employee_time_sheet->pm_ut = round(($_pm_end - $pm_out) / 60, 2);
                        }
                    }

                    if ($pm_in >= $_pm_end) {
                        $employee_time_sheet->pm_time_rendered = 0;
                    } else {
                        if ($pm_in && $pm_out) {
                            $pm_time_rendered = ($pm_out > $_pm_end ? $_pm_end : $pm_out) - ($pm_in < $_pm_start ? $_pm_start : $pm_in);
                            $employee_time_sheet->pm_time_rendered = round(($pm_time_rendered) / 60, 2);
                        }
                    }
                    // END PM CALCULATION

                    $employee_time_sheet->total_late = $employee_time_sheet->am_late + $employee_time_sheet->pm_late;
                    $employee_time_sheet->total_ut = $employee_time_sheet->am_ut + $employee_time_sheet->pm_ut;
                    $employee_time_sheet->total_time_rendered = $employee_time_sheet->am_time_rendered + $employee_time_sheet->pm_time_rendered;
                }

                if ($no_shift_schedule === true) {
                    $employee_time_sheet->scrub_status = 3;
                    $employee_time_sheet->has_shift = 0;
                    $employee_time_sheet->comments = "[System Generated]: No shift schedule detected.";
                } else {
                    $employee_time_sheet->shift_am_start = $am_start;
                    $employee_time_sheet->shift_am_end = $am_end;
                    $employee_time_sheet->shift_pm_start = $pm_start;
                    $employee_time_sheet->shift_pm_end = $pm_end;
                }

                array_push($timesheet, $employee_time_sheet);
            }

            return $timesheet;
        }
    }

    public function getBiometricPossibleMatchesFromEmployees($biometricno)
    {
        $resultSet = array();
        $this->db->select("emp.*, personnel.shift_id");
        $this->db->where("emp.biometricno", intval($biometricno));
        $this->db->join($this->tbl_personnel . " personnel", "personnel personnel ON personnel.biometricno = emp.biometricno", "LEFT");
        $resultSet["data"] = $this->db->get($this->tbl_employees . " emp")->result();

        return $resultSet;
    }

    public function linkEmployeeToBiometricNoInPersonnel()
    {
        $post = $this->arrayToStdClass($this->input->post());
        $in_personnel = $this->db->where("biometric_id", $post->personnel_biometric_no)->count_all_results($this->tbl_personnel);
        $resultSet = array();
        $query = false;
        $history_id = isset($post->history_id) ? $post->history_id : null;
        $errors = array();
        $is_flexi = isset($post->is_flexi) ? (!empty($post->is_flexi) ? intval($post->is_flexi) : 0) : 0;

        if ($post->mode === "link") {
            $employee = $this->db->where("id", $post->emp_id)->get($this->tbl_employees)->row();

            if (intval($in_personnel) >= 1) {
                $this->db->where("biometric_id", $post->personnel_biometric_no);
                $this->db->set("biometricno", $post->emp_biometric_no);
                $this->db->set("shift_id", $post->shift_id);
                $this->db->set("is_flexi", $is_flexi);
                $query = $this->db->update($this->tbl_personnel);

                $this->db->reset_query();
            } else {
                $query = $this->db->insert($this->tbl_personnel, array(
                    "biometric_id" => $post->personnel_biometric_no,
                    "biometricno" => $post->emp_biometric_no,
                    "name" => $employee->firstname . " " . $employee->lastname,
                    "department_id" => 0,
                    "location_id" => 0,
                    "role" => 0,
                    "shift_id" => $post->shift_id,
                    "is_flexi" => $is_flexi,
                    "is_active" => 1,
                ));
            }
        } else {
            $this->db->where("biometric_id", $post->personnel_biometric_no);
            $this->db->set("shift_id", $post->shift_id);
            $this->db->set("is_flexi", $is_flexi);
            $query = $this->db->update($this->tbl_personnel);
        }

        if (!empty($history_id) && $history_id !== "null") {
            // remove biometric no from array list
            $this->db->select("imports.*, CONCAT(emp.lastname, ' ', emp.firstname) creator");
            $this->db->join("tblemployees emp", "emp.id = imports.created_by", "LEFT");
            $this->db->where("imports.id", $history_id);

            $query = $this->db->get("gcctimeutility.timesheet_imports imports");
            $row = $query->row();
            $no_shifts = array();
            $biometric_no_array = array();

            if ($post->modal_origin === "history") {
                if (!empty($row->no_shifts)) {
                    $no_shifts = unserialize($row->no_shifts);
                    $no_shifts_index = array_search($post->personnel_biometric_no, $no_shifts);
                    if ($no_shifts_index !== false) {
                        array_splice($no_shifts, $no_shifts_index, 1);
                    }

                    $row->no_shifts = array_map(function ($biometric) use ($history_id) {
                        return "<div class='dropdown d-inline'>
                                    <span class='m-badge m-badge--wide m--margin-right-5 m--margin-bottom-5 m--font-boldest
                                                 m-badge--clickable' onclick='openAddShiftModal($biometric, \"history\", $history_id)'>
                                        $biometric
                                    </span>
                                </div>";
                    }, $no_shifts);
                }

                if (!empty($row->no_employee_biometric_no)) {
                    $biometric_no_array = json_decode(json_encode(unserialize($row->no_employee_biometric_no)), true);
                    $biometric_no_array_index = array_search($post->personnel_biometric_no, array_column($biometric_no_array, 'biometric'));
                    if ($biometric_no_array_index !== false) {
                        array_splice($biometric_no_array, $biometric_no_array_index, 1);
                    }
                }

                $row->biometric_no = array_map(function ($row) use ($history_id) {
                    $possible_match_class = intval($row["in_employees"]) <= 0 ? 'm--hide' : '';
                    return "<div class='dropdown d-inline'>
                                    <span class='m-badge m-badge--wide m--margin-right-5 m--margin-bottom-5 m--font-boldest
                                                 m-badge--clickable'
                                          data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        " . $row["biometric"] . "
                                    </span>
                                    <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                        <h6 class='dropdown-header'
                                            style='padding: 0.5rem 1.3rem;'>
                                            <span class='m--font-boldest2'>" . $row["biometric"] . "</span>
                                        </h6>
                                        <a class='dropdown-item' href='javascript:void(0)'
                                           onclick='openAddEmployeeModal(" . $row["biometric"] . ",\"history\", " . $history_id . ")'>
                                            Add Employee
                                        </a>
                                        <a class='dropdown-item $possible_match_class'
                                           href='javascript:void(0)' onclick='openPossibleMatchesModal(" . $row["biometric"] . ", \"history\")'>
                                           " . $row["in_employees"] . " Possible Match(es).
                                        </a>
                                    </div>
                                </div>";
                }, $biometric_no_array);

                $resultSet["import_history"] = $row;
                $this->db->reset_query();

                $this->db->where("id", $history_id);
                if (sizeof($biometric_no_array) >= 1) {
                    $this->db->set("no_employee_biometric_no", serialize(json_decode(json_encode($biometric_no_array))));
                } else {
                    $this->db->set("no_employee_biometric_no", NULL);
                }

                if (sizeof($no_shifts) >= 1) {
                    $this->db->set("no_shifts", serialize($no_shifts));
                } else {
                    $this->db->set("no_shifts", NULL);
                }

                if (sizeof($biometric_no_array) <= 0 && sizeof($no_shifts) <= 0) {
                    $this->db->set("resolved", 1);
                }

                $this->db->update("gcctimeutility.timesheet_imports");
                $this->db->reset_query();
            }

            if (isset($post->shift_id)) {
                $emp_id = $this->db->select("emp.id")
                    ->where("personnel.biometric_id", $post->personnel_biometric_no)
                    ->join($this->tbl_personnel . " personnel", "personnel.biometricno = emp.biometricno", "INNER")
                    ->get($this->tbl_employees . " emp")
                    ->row("id");

                $interval = DateInterval::createFromDateString('1 day');

                $dateStart = new DateTime($row->start_date);
                $dateEnd = new DateTime($row->end_date);
                $dateEnd->modify("+1 day");

                $period = new DatePeriod($dateStart, $interval, $dateEnd);
                foreach ($period as $dt) {
                    $date = $dt->format("Y-m-d");
                    $created = $this->create($date, 1, [$emp_id], $history_id, 1);
                    if (intval($created["k"]) === 2) {
                        array_push($errors, $created);
                    }
                }
            }
        }

        $resultSet["success"] = $query;
        $resultSet["generate_timesheet_errors"] = $errors;
        return $resultSet;
    }

    public function getShiftSchedule()
    {
        $q = isset($_GET['q']) ? $_GET['q'] : '';

        $this->db->like("description", $q, "BOTH");
        $this->db->select("id, description text");
        $this->db->where("is_active", 1);
        $query = $this->db->get($this->tbl_shift_schedule);
        return array("results" => $query->result());
    }

    public function undoTimesheetVerification($timesheet_id)
    {
        $resultSet = array();
        $this->db->trans_begin();
        $this->db->db_debug = false;

        // get adjustment id, query time_adjustments with approved status
        $timesheet = $this->db
            ->select("ts.*, CONCAT(emp.firstname, ' ', emp.lastname) `employee_name`, CONCAT(emp_verified.firstname, ' ', emp_verified.lastname) `verified_by_name`, emp.biometricno")
            ->join($this->tbl_employees . " emp", "emp.id = ts.emp_id", "INNER")
            ->join($this->tbl_employees . " emp_verified", "emp_verified.id = ts.verified_by", "LEFT")
            ->where("ts.id", $timesheet_id)
            ->get($this->tbl_timesheet . " ts")
            ->row();

        $attRecord = new stdClass();
        $attRecord->count = 0;
        if(isset($timesheet->biometricno) && $timesheet->biometricno){
            $attendance = $this->db->get_where($this->tbl_attendance,
                array(
                    "biometric_id"=>$timesheet->biometricno,
                    "DATE(datetime)"=>$timesheet->date,
                )
            );
            if($attendance->num_rows() > 0){ $attRecord->count = $attendance->num_rows(); }
        }

        $time_adjustment = $this->db
            ->select("ta.*, CONCAT(emp.firstname, ' ', emp.lastname) confirmed_by_employee_name")
            ->join($this->tbl_employees . " emp", "emp.id = ta.confirmed_by", "LEFT")
            ->where(array("ta.timesheet_id" => $timesheet_id, "ta.status" => 1))
            ->get($this->tbl_time_adjustments . " ta")
            ->row();

        if (!empty($time_adjustment)) {
            $time_adjustment_meta = $this->db->where("time_adjustments_id", $time_adjustment->id)
                ->get($this->tbl_time_adjustments_meta)
                ->result();

            $time_adjustment_eform_ref = $this->db->where("time_adjustments_id", $time_adjustment->id)
                ->get($this->tbl_time_adjustments_eform_refs)
                ->result();

            $time_adjustment_overtime = $this->db->where("time_adjustments_id", $time_adjustment->id)
                ->get($this->tbl_time_adjustments_overtime)
                ->result();

            $time_adjustment_shift_schedule = $this->db->where("time_adjustments_id", $time_adjustment->id)
                ->get($this->tbl_time_adjustments_shift_schedule)
                ->result();

            $metaFields = array("am_in", "am_out", "pm_in", "pm_out");
            if($attRecord->count == 0){
                foreach ($metaFields as $key => $value) {
                    $this->db->where("time_adjustments_id", $time_adjustment->id);
                    $this->db->where("field", $value);
                    $this->db->where("prev_value !=", NULL);
                    $this->db->set("prev_value", NULL);
                    $this->db->update($this->tbl_time_adjustments_meta);
                    $this->db->reset_query();
                }
            }
        }

        $data = new StdClass();
        if (!empty($time_adjustment_meta) || !empty($time_adjustment_eform_ref)
            || !empty($time_adjustment_overtime) || !empty($time_adjustment_shift_schedule)) {
            if (!empty($time_adjustment_meta)) {
                foreach ($time_adjustment_meta as $meta) {
                    $field = $meta->field;
                    $data->$field = $meta->prev_value;
                }
            }

            if (!empty($time_adjustment_shift_schedule)) {
                $data->shift_am_start = null;
                $data->shift_am_end = null;
                $data->shift_pm_start = null;
                $data->shift_pm_end = null;
            }

            if (!empty($time_adjustment_overtime)) {
                $data->total_accredited_ot_hrs = array_reduce($time_adjustment_overtime,
                    function ($carry, $next) {
                        $carry += $next->prev_value;
                        return $carry;
                    }, 0);
                $data->total_accredited_ndiff_ot_hrs = array_reduce($time_adjustment_overtime,
                    function ($carry, $next) {
                        $carry += $next->n_diff_prev_value;
                        return $carry;
                    }, 0);
            }


            $this->db->update($this->tbl_time_adjustments, array(
                "status" => 0,
                "confirmed_by" => NULL,
                "confirmed_at" => NULL,
                "confirmation_remarks" => NULL
            ), array("id" => $time_adjustment->id));
        }

        $this->db->reset_query();
        $data->verified = 0;
        $data->total_late = null;
        $data->total_ut = null;
        $data->total_time_rendered = null;
        $this->db->update($this->tbl_timesheet, $data, array("id" => $timesheet_id));

        if ($this->db->trans_status() === TRUE) {
            $resultSet["success"] = true;
            $resultSet["message"] = "Undoing verification of <span class='m--font-boldest'>" . date("M. d,Y") . "</span>
                                            record for <span class='m--font-boldest'>" . $timesheet->employee_name . "</span> was successful.";
            $resultSet["title"] = "Undo Verification Successful.";
            $resultSet["toast"] = "success";

            $log_filename = date('Y-m-d') . ".txt";
            $log_file_data = "UNDONE BY: " . strtoupper($timesheet->employee_name) . " at " . date("H:i:s");
            if (intval($timesheet->with_adjustment) === 1) {
                $log_file_data .= "||TIMESHEET ID:$timesheet_id, ";
                $log_file_data .= "ADJUSTMENT ID: $time_adjustment->id and was CONFIRMED BY: $time_adjustment->confirmed_by_employee_name at $time_adjustment->confirmed_at.\n";
            } else {
                $log_file_data .= "||TIMESHEET ID:$timesheet_id and was VERIFIED BY: $timesheet->verified_by_name at $timesheet->verified_at.\n";
            }
            $resultSet["create_log_file"] = $this->core_layout->create_txt_log_file('sys_gen_logs/undo_verification', $log_filename, $log_file_data, true);
            $this->db->trans_commit();
        } else {
            $resultSet["success"] = false;
            $resultSet["message"] = $this->db->error()["message"];
            $resultSet["title"] = "An error occurred.";
            $resultSet["toast"] = "error";
            $this->db->trans_rollback();
        }

        $resultSet["timesheet"] = $timesheet;
        $resultSet["row"] = $this->getTimesheetRow($timesheet_id, $timesheet->emp_id);
        $resultSet["data"] = $data;

        $this->db->db_debug = $this->db_debug;
        return $resultSet;
    }

    public function unresolvedTimesheetImports($biometricno)
    {
        $data = array();
        $unresolved_ts_imports = $this->db
            ->where("resolved", 0)
            ->where("import_type", "attendance")
            ->get("gcctimeutility.timesheet_imports")
            ->result();

        foreach ($unresolved_ts_imports as $row) {
            $start_date = $row->start_date;
            $end_date = $row->end_date;
            $history_id = $row->id;
            $unserialized = !empty($row->no_employee_biometric_no) ? unserialize($row->no_employee_biometric_no) : null;

            if (!empty($unserialized)) {
                // $index = array_search($biometricno, array_column($unserialized, 'biometric'), TRUE);
                $filtered = array_filter($unserialized, function ($item) use ($biometricno) {
                    return $item->biometric === $biometricno;
                });

                if (sizeof($filtered) >= 1) {
                    array_push($data, $row);
                }
            }
        }

        return $data;
    }

    public function getEmployeesForLookUpAndUpdateBiometricNo()
    {
        $resultSet = array();
        $post = $this->arrayToStdClass($this->input->post());
        $dtCfg = $this->mod_util->getDatatablesConfigForPagination($post);

        if (intval($dtCfg->length) >= 1) {
            $this->db->limit($dtCfg->length, $dtCfg->start);
        }

        $this->db->order_by("TRIM(" . $dtCfg->order_column . ")", $dtCfg->order_direction);

        $search = array();
        $search["field"] = "CONCAT(emp.lastname, emp.firstname, emp.middlename, emp.biometricno, CONCAT(emp.lastname, ', ', emp.firstname))";
        $search["key"] = $dtCfg->search;
        $search["option"] = "BOTH";

        $this->db->like($search["field"], $search["key"], $search["option"]);
        $this->db->select("emp.*, per.id personnel_id, per.shift_id");
        $this->db->where("employee_status IS NOT NULL", NULL, FALSE);
        $this->db->join($this->tbl_personnel . " per", "per.biometricno = emp.biometricno", "LEFT");

        $resultSet["data"] = $this->db->get($this->tbl_employees . " emp")->result();
        $resultSet["recordsFiltered"] = $this->getEmployeesForLookUpAndUpdateBiometricNoCount($search);
        $resultSet["recordsTotal"] = $this->getEmployeesForLookUpAndUpdateBiometricNoCount($search);

        return $resultSet;
    }

    private function getEmployeesForLookUpAndUpdateBiometricNoCount($search)
    {
        $this->db->like($search["field"], $search["key"], $search["option"]);
        $this->db->where("employee_status IS NOT NULL", NULL, FALSE);

        return $this->db->count_all_results($this->tbl_employees . " emp");
    }

    public function updateEmployeeBiometricNoFromTimesheetImports()
    {
        $post = $this->arrayToStdClass($this->input->post());
        $in_personnel = $this->db->where("biometric_id", $post->personnel_biometric_no)->count_all_results($this->tbl_personnel);
        $resultSet = array();
        $query = false;
        $history_id = isset($post->history_id) ? $post->history_id : null;
        $errors = array();
        $is_flexi = isset($post->is_flexi) ? (!empty($post->is_flexi) ? intval($post->is_flexi) : 0) : 0;

        $this->db->where("id", $post->emp_id);
        $this->db->set("biometricno", $post->emp_biometric_no);
        $this->db->update($this->tbl_employees);
        $this->db->reset_query();

        if ($post->mode === "look up") {
            $employee = $this->db->where("id", $post->emp_id)->get($this->tbl_employees)->row();

            if (intval($in_personnel) >= 1) {
                $this->db->where("biometric_id", $post->personnel_biometric_no);
                $this->db->set("biometricno", $post->emp_biometric_no);
                $this->db->set("shift_id", $post->shift_id);
                $this->db->set("is_flexi", $is_flexi);
                $query = $this->db->update($this->tbl_personnel);

                $this->db->reset_query();
            } else {
                $query = $this->db->insert($this->tbl_personnel, array(
                    "biometric_id" => $post->personnel_biometric_no,
                    "biometricno" => $post->emp_biometric_no,
                    "name" => $employee->firstname . " " . $employee->lastname,
                    "department_id" => 0,
                    "location_id" => 0,
                    "role" => 0,
                    "shift_id" => $post->shift_id,
                    "is_flexi" => $is_flexi,
                    "is_active" => 1,
                ));
            }
        }

        if (!empty($history_id) && $history_id !== "null") {
            // remove biometric no from array list
            $this->db->select("imports.*, CONCAT(emp.lastname, ' ', emp.firstname) creator");
            $this->db->join("tblemployees emp", "emp.id = imports.created_by", "LEFT");
            $this->db->where("imports.id", $history_id);

            $query = $this->db->get("gcctimeutility.timesheet_imports imports");
            $row = $query->row();
            $no_shifts = array();
            $biometric_no_array = array();

            if ($post->modal_origin === "history") {
                if (!empty($row->no_shifts)) {
                    $no_shifts = unserialize($row->no_shifts);
                    $no_shifts_index = array_search($post->personnel_biometric_no, $no_shifts);
                    if ($no_shifts_index !== false) {
                        array_splice($no_shifts, $no_shifts_index, 1);
                    }

                    $row->no_shifts = array_map(function ($biometric) use ($history_id) {
                        return "<div class='dropdown d-inline'>
                                    <span class='m-badge m-badge--wide m--margin-right-5 m--margin-bottom-5 m--font-boldest
                                                 m-badge--clickable' onclick='openAddShiftModal($biometric, \"history\", $history_id)'>
                                        $biometric
                                    </span>
                                </div>";
                    }, $no_shifts);
                }

                if (!empty($row->no_employee_biometric_no)) {
                    $biometric_no_array = json_decode(json_encode(unserialize($row->no_employee_biometric_no)), true);
                    $biometric_no_array_index = array_search($post->personnel_biometric_no, array_column($biometric_no_array, 'biometric'));
                    if ($biometric_no_array_index !== false) {
                        array_splice($biometric_no_array, $biometric_no_array_index, 1);
                    }
                }

                $row->biometric_no = array_map(function ($row) use ($history_id) {
                    $possible_match_class = intval($row["in_employees"]) <= 0 ? 'm--hide' : '';
                    return "<div class='dropdown d-inline'>
                                    <span class='m-badge m-badge--wide m--margin-right-5 m--margin-bottom-5 m--font-boldest
                                                 m-badge--clickable'
                                          data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        " . $row["biometric"] . "
                                    </span>
                                    <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                        <h6 class='dropdown-header'
                                            style='padding: 0.5rem 1.3rem;'>
                                            <span class='m--font-boldest2'>" . $row["biometric"] . "</span>
                                        </h6>
                                        <a class='dropdown-item' href='javascript:void(0)'
                                           onclick='openAddEmployeeModal(" . $row["biometric"] . ",\"history\", " . $history_id . ")'>
                                            Add Employee
                                        </a>
                                        <a class='dropdown-item $possible_match_class'
                                           href='javascript:void(0)' onclick='openPossibleMatchesModal(" . $row["biometric"] . ", \"history\")'>
                                           " . $row["in_employees"] . " Possible Match(es).
                                        </a>
                                    </div>
                                </div>";
                }, $biometric_no_array);

                $resultSet["import_history"] = $row;
                $this->db->reset_query();

                $this->db->where("id", $history_id);
                if (sizeof($biometric_no_array) >= 1) {
                    $this->db->set("no_employee_biometric_no", serialize(json_decode(json_encode($biometric_no_array))));
                } else {
                    $this->db->set("no_employee_biometric_no", NULL);
                }

                if (sizeof($no_shifts) >= 1) {
                    $this->db->set("no_shifts", serialize($no_shifts));
                } else {
                    $this->db->set("no_shifts", NULL);
                }

                if (sizeof($biometric_no_array) <= 0 && sizeof($no_shifts) <= 0) {
                    $this->db->set("resolved", 1);
                }

                $this->db->update("gcctimeutility.timesheet_imports");
                $this->db->reset_query();
            }

            if (isset($post->shift_id)) {
                $emp_id = $this->db->select("emp.id")
                    ->where("personnel.biometric_id", $post->personnel_biometric_no)
                    ->join($this->tbl_personnel . " personnel", "personnel.biometricno = emp.biometricno", "INNER")
                    ->get($this->tbl_employees . " emp")
                    ->row("id");

                $interval = DateInterval::createFromDateString('1 day');

                $dateStart = new DateTime($row->start_date);
                $dateEnd = new DateTime($row->end_date);
                $dateEnd->modify("+1 day");

                $period = new DatePeriod($dateStart, $interval, $dateEnd);
                foreach ($period as $dt) {
                    $date = $dt->format("Y-m-d");
                    $created = $this->create($date, 1, [$emp_id], $history_id, 1);
                    if (intval($created["k"]) === 2) {
                        array_push($errors, $created);
                    }
                }
            }
        }

        $resultSet["success"] = $query;
        $resultSet["generate_timesheet_errors"] = $errors;
        return $resultSet;
    }

    function getShiftScheduleForEmployeeFilter($id=null){
        $results = array();
        $get = $this->input->get();
        if(isset($get["scheduled_date"]) && $get["scheduled_date"]){
            $shiftFilter = true;
            $tempIds = array();
            $tempDate = date("Y-m-d", strtotime($get["scheduled_date"]));

            if($id){
                $qTempx = $this->db->get_where($this->tbl_timesheet_customized_shift_schedule, array("id"=>$id));
                if($qTempx->num_rows() == 1){
                    if($qTempx->row()->scheduled_date == $tempDate){ $shiftFilter = false; }
                }
            }

            $searchExistingDates = $this->db->get_where($this->tbl_timesheet_customized_shift_schedule, array("scheduled_date"=>$tempDate));
            if($searchExistingDates->num_rows() > 0){
                foreach ($searchExistingDates->result() as $key => $value) {
                    $empIds = @unserialize($value->employee_id);
                    if(is_array($empIds) && count($empIds) > 0){
                        $tempIds = array_merge($tempIds, $empIds);
                    }
                }
            }

            if(count($tempIds) > 0){ $tempIds = array_unique($tempIds); }
            $this->db->select("id, lastname, firstname, middlename, suffix");
            $this->db->from($this->tbl_employees);
            $this->db->where("employee_status", "Active");
            if($tempIds && count($tempIds) > 0 && $shiftFilter == true){
                $this->db->where_not_in("id", $tempIds);
            }
            if(isset($get["term"]) && $get["term"]){
                $this->db->group_start();
                $this->db->like("lastname", $get["term"], "both");
                $this->db->or_like("firstname", $get["term"], "both");
                $this->db->or_like("middlename", $get["term"], "both");
                $this->db->or_like("suffix", $get["term"], "both");
                $this->db->or_like("CONCAT(firstname, ' ', lastname, ' ', suffix)", $get["term"], "both");
                $this->db->group_end();
            }
            $this->db->limit(10);
            $this->db->order_by("firstname", "ASC");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() > 0){
                foreach ($qTemp->result() as $kk => $vv) {
                    $tempRsx = (array) $vv;
                    $tempRs = $this->core_layout->getDisplayName($tempRsx);
                    $tempName = (object) $tempRs;
                    $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No Assigned Name";
                    $arrData = array();
                    $arrData["id"] = $vv->id;
                    $arrData["text"] = $tempName;
                    $arrData["html"] = $tempName;
                    $results[] = $arrData;
                }
            }

            $resultset["id"] = $id;
            $resultset["shiftFilter"] = $shiftFilter;
        }

        $resultset["results"] = $results;
        return $resultset;
    }

    function getShiftScheduleForFilter($id=null){
        $results = array();
        $get = $this->input->get();
        if(isset($get["scheduled_date"]) && $get["scheduled_date"]){
            $shiftFilter = true;
            $tempIds = array();
            $tempShiftIds = array();
            $tempDate = date("Y-m-d", strtotime($get["scheduled_date"]));

            if($id){
                $qTemp = $this->db->get_where($this->tbl_timesheet_customized_shift_schedule, array("id"=>$id));
                if($qTemp->num_rows() == 1){
                    if($qTemp->row()->scheduled_date == $tempDate){ $shiftFilter = false; }
                }
            }
            $searchExistingDates = $this->db->get_where($this->tbl_timesheet_customized_shift_schedule, array("scheduled_date"=>$tempDate));
            if($searchExistingDates->num_rows() > 0){
                foreach ($searchExistingDates->result() as $key => $value) {
                    $shiftIds = @unserialize($value->shift_id);
                    if(is_array($shiftIds) && count($shiftIds) > 0){
                        $tempShiftIds = array_merge($tempShiftIds, $shiftIds);
                    }
                }
            }

            if(count($tempShiftIds) > 0){ $tempShiftIds = array_unique($tempShiftIds); }
            $this->db->select("id, description as text, description as html");
            $this->db->from($this->tbl_shift_schedule);
            $this->db->where("is_active", 1);
            $this->db->where("status", 1);
            if($tempShiftIds && count($tempShiftIds) > 0 && $shiftFilter == true){
                $this->db->where_not_in("id", $tempShiftIds);
            }
            if(isset($get["term"]) && $get["term"]){
                $this->db->like("description", $get["term"], "both");
            }
            $this->db->limit(10);
            $qTemp = $this->db->get();

            if($qTemp->num_rows() > 0){
                $results = $qTemp->result();
            }
        }
        $resultset["results"] = $results;
        return $resultset;
    }

    public function getCustomShiftScheduleRequest()
    {
        $post = $this->arrayToStdClass($this->input->post());
        $dtCfg = $this->mod_util->getDatatablesConfigForPagination($post);
        $status = isset($post->status) && !empty($post->status) ? $post->status : null;

        $this->db->from($this->tbl_timesheet_customized_shift_schedule);
        if($status){ $this->db->where("status", $status); }

        if($dtCfg){
            $limit = isset($dtCfg->length) && intval($dtCfg->length) > 0 ? intval($dtCfg->length): 10;
            $start = isset($dtCfg->start) && intval($dtCfg->start) > 0 ? intval($dtCfg->start): 0;
            $orderBy = isset($dtCfg->order_direction) && $dtCfg->order_direction ? $dtCfg->order_direction: "asc";
            $orderColumn = isset($dtCfg->order_column) && $dtCfg->order_column ? $dtCfg->order_column: "id";
            $this->db->order_by($orderColumn, $orderBy);
            $this->db->limit($limit, $start);
        }

        $qTemp = $this->db->get();
        $sql = $this->db->last_query();

        $requests = new stdClass();

        if($qTemp->num_rows() > 0){
            $requests = $qTemp->result();
            foreach ($requests as $key => $value) {
                $value->shift_resource = array();
                $value->employee_resource = array();
                $value->employee_count = 0;

                $shifts = @unserialize($value->shift_id);
                $employees = @unserialize($value->employee_id);

                if(is_array($employees) && count($employees) > 0){
                    $this->db->select("lastname, firstname, middlename, suffix");
                    $this->db->from($this->tbl_employees);
                    $this->db->where_in("id", $employees);
                    $qTempEmployee = $this->db->get();
                }

                if(is_array($shifts) && count($shifts) > 0){
                    $this->db->select("description");
                    $this->db->from($this->tbl_shift_schedule);
                    $this->db->where("status", 1);
                    $this->db->where("is_active", 1);
                    $this->db->where_in("id", $shifts);
                    $qTempShift = $this->db->get();
                    if($qTempShift->num_rows() > 0){
                        $qResults = $qTempShift->result();
                        $tempShiftDescription = array();
                        foreach ($qResults as $kk => $vv) {
                            $tempShiftDescription[] = $vv->description;
                        }

                        $value->shift_resource = $tempShiftDescription;
                    }
                }

                $value->shift_am_start = (isset($value->shift_am_start) && $value->shift_am_start)? date("H:i", strtotime($value->shift_am_start)): "--:--";
                $value->shift_am_end = (isset($value->shift_am_end) && $value->shift_am_end)? date("H:i", strtotime($value->shift_am_end)): "--:--";
                $value->shift_pm_start = (isset($value->shift_pm_start) && $value->shift_pm_start)? date("H:i", strtotime($value->shift_pm_start)): "--:--";
                $value->shift_pm_end = (isset($value->shift_pm_end) && $value->shift_pm_end)? date("H:i", strtotime($value->shift_pm_end)): "--:--";
                $value->shift_id = $shifts;
                $value->employee_id = $employees;
                $value->employee_count = count((array)$employees);
            }
        }

        return array(
            "data" => $requests,
            "dtCfg" => $dtCfg,
            "post" => $post,
            "recordsFiltered" => $this->getTimeCustomShiftCount($status),
            "recordsTotal" => $this->getTimeCustomShiftCount($status),
            "sql" => $sql
        );
    }

    function updateCustomShiftSchedule(){
        $resultset = array();
        $post = $this->arrayToStdClass($this->input->post());
        if(isset($post) && $post){
            $tempWhere = array();
            $tempWhere["id"] = $post->id;

            $tempData = array();
            $tempShiftData = new StdClass();
            $tempShiftData = isset($post->has_shift) && intval($post->has_shift) == 1? $post->shift: $tempShiftData;
            $post->shift_id = isset($post->shift_id) && $post->shift_id ? serialize($post->shift_id): serialize(array());
            $post->employee_id = isset($post->employee_id) && $post->employee_id ? serialize($post->employee_id): serialize(array());

            if(isset($post->has_shift) && intval($post->has_shift) == 1){ unset($post->shift); }

            $tempData["shift_am_start"] = null;
            $tempData["shift_am_end"] = null;
            $tempData["shift_pm_start"] = null;
            $tempData["shift_pm_end"] = null;

            if(isset($tempShiftData) && $tempShiftData){
                foreach ($tempShiftData as $key => $value) {
                    $tempTime = ($value)? date("H:i:s", strtotime($value)): "";
                    if($value && $tempTime !== null){
                        $tempData["shift_{$key}"] = $tempTime;
                    }else{
                        $tempData["shift_{$key}"] = null;
                    }
                }
            }

            $post->updated_by = $this->core_layout->getCurrentEmployeeId();
            $post->updated_at = date("Y-m-d H:i:s");
            $tempPost = (array) $post;

            $tempPost = array_merge($tempPost, $tempData);
            $tempData = $this->db->update($this->tbl_timesheet_customized_shift_schedule, $tempPost, $tempWhere);
            if($tempData){
                $resultset["response"] = true;
                $resultset["toastr_msg"] = "Custom shift schedule has been updated successfully.";
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to update custom shift schedule!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }

    function removeCustomShiftSchedule(){
        $resultset = array();
        $post = $this->input->post();
        if(isset($post) && $post){
            $remove = $this->db->delete($this->tbl_timesheet_customized_shift_schedule, $post);
            if($remove){
                $resultset["response"] = true;
                $resultset["state"] = "info";
                $resultset["toastr_msg"] = "Custom shift shedule has been removed.";
            }else{
                $resultset["response"] = false;
                $resultset["state"] = "warning";
                $resultset["toastr_msg"] = "Failed to remove custom shift shedule!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["state"] = "error";
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }

    function setCustomShiftSchedule(){
        $resultset = array();
        $post = $this->arrayToStdClass($this->input->post());
        if(isset($post) && $post){
            $tempData = array();
            $tempShiftData = new StdClass();
            $tempShiftData = (isset($post->has_shift) && intval($post->has_shift) == 1)? $post->shift: $tempShiftData;

            $post->shift_id = isset($post->shift_id) && $post->shift_id ? serialize($post->shift_id): serialize(array());
            $post->employee_id = isset($post->employee_id) && $post->employee_id ? serialize($post->employee_id): serialize(array());

            if(isset($post->has_shift) && intval($post->has_shift) == 1){ unset($post->shift); }

            if(isset($tempShiftData) && $tempShiftData){
                foreach ($tempShiftData as $key => $value) {
                    $tempTime = date("H:i:s", strtotime($value));
                    if($value && $tempTime !== null){
                        $tempData["shift_{$key}"] = $tempTime;
                    }
                }
            }

            $post->created_by = $this->core_layout->getCurrentEmployeeId();
            $post->created_at = date("Y-m-d H:i:s");
            $tempPost = (array)$post;

            $tempPost = array_merge($tempPost, $tempData);

            $tempData = $this->db->insert($this->tbl_timesheet_customized_shift_schedule, $tempPost);
            if($tempData){
                $resultset["response"] = true;
                $resultset["toastr_msg"] = "Custom shift schedule has been added successfully.";
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to add custom shift schedule!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }

    function getCustomizedShiftScheduleByDate($date=null, $id=null){
        $arrData = new stdClass();
        if($date && $date !== "0000-00-00"){
            $date = date("Y-m-d", strtotime($date));
            $weekday = date("l", strtotime($date));
            $weekday = strtolower($weekday);
            $singleEmployeeShift = false;

            $_personnel = new stdClass();
            if($id){
                $this->db->select("a.id, b.shift_id");
                $this->db->from("gccmaster.tblemployees a");
                $this->db->join("gcctimeutility.personnel b", "b.biometric_id = a.biometricno OR b.biometricno = a.biometricno");
                $this->db->where("a.id", $id);
                $personnel = $this->db->get();
                if($personnel->num_rows() == 1){
                    $_personnel = $personnel->row();
                    $singleEmployeeShift = true;
                }
            }

            $qTemp = $this->db->get_where($this->tbl_timesheet_customized_shift_schedule, array("scheduled_date"=>$date));
            if($qTemp->num_rows() > 0){
                foreach ($qTemp->result() as $row) {
                    $shiftIndexes = array("shift_am_start", "shift_am_end", "shift_pm_start", "shift_pm_end");
                    $alterShiftIndexes = array("am_start", "am_end", "pm_start", "pm_end");
                    $tempRow = $row;

                    $hasShift = intval($tempRow->has_shift) == 1;
                    $has_shift_value = intval($tempRow->has_shift);
                    $shiftIds = @unserialize($tempRow->shift_id);
                    $employeeIds = @unserialize($tempRow->employee_id);

                    $tempShiftIds = array();
                    if($singleEmployeeShift){
                        if(count(get_object_vars($_personnel)) > 0){
                            if(is_array($employeeIds) && count($employeeIds) > 0 && in_array($_personnel->id, $employeeIds)){
                                if($_personnel->shift_id){ $tempShiftIds[] = intval($_personnel->shift_id); }
                            }else{
                                if($_personnel->shift_id && in_array($_personnel->shift_id, $shiftIds)){
                                    $tempShiftIds[] = intval($_personnel->shift_id);
                                }
                            }
                        }
                        $shiftIds = $tempShiftIds;
                    }else{
                        if(is_array($employeeIds) && count($employeeIds) > 0){
                            $this->db->select("GROUP_CONCAT(DISTINCT(b.shift_id)) as shift_ids");
                            $this->db->from("gccmaster.tblemployees a");
                            $this->db->join("gcctimeutility.personnel b", "b.biometric_id = a.biometricno OR b.biometricno = a.biometricno");
                            $this->db->where_in("a.id", $employeeIds);
                            $this->db->where("b.shift_id !=", 0);
                            $queryShifts = $this->db->get();
                            if($queryShifts->num_rows() == 1){
                                $ids = $queryShifts->row()->shift_ids;
                                $tempShiftIds = explode(",", $ids);
                            }
                            $shiftIds = array_unique(array_merge($shiftIds, $tempShiftIds));
                        }
                    }

                    if(is_array($shiftIds) && count($shiftIds) > 0){
                        $this->db->select("b.shift_id, b.shift_resource");
                        $this->db->join($this->tbl_shift_schedule_resource." b", "b.shift_id = a.id");
                        $this->db->where_in("a.id", $shiftIds);
                        $tempShift = $this->db->get_where($this->tbl_shift_schedule." a", array("a.status"=>1, "a.is_active"=>1));
                        if($tempShift->num_rows() > 0){
                            foreach ($tempShift->result() as $vv) {
                                $tempRowIds = @unserialize($vv->shift_resource);
                                if(is_array($tempRowIds) && count($tempRowIds) > 0){
                                    $this->db->select(implode(",", $alterShiftIndexes));
                                    $this->db->where_in("id", $tempRowIds);
                                    $scheduleWeekday = $this->db->get_where($this->tbl_shift_schedule_list, array("is_active"=>1, "weekday"=>$weekday));
                                    if($scheduleWeekday->num_rows() == 1){
                                        $tempDatax = new stdClass();
                                        $tempShiftRow = $scheduleWeekday->row();
                                        foreach ($shiftIndexes as $kkx => $vvx) {
                                            $tempAlterIndex = $alterShiftIndexes[$kkx];
                                            if($tempShiftRow->{$tempAlterIndex} && $tempRow->{$vvx} && $tempRow->{$vvx} !== null && $hasShift === true){
                                                $tempShiftRow->{$tempAlterIndex} = $tempRow->{$vvx};
                                            }else{
                                                $tempShiftRow->{$tempAlterIndex} = null;
                                            }
                                        }
                                        $tempDatax->schedule = $tempShiftRow;
                                        $tempDatax->custom_shift_id = $tempRow->id;
                                        $tempDatax->has_shift = $has_shift_value;
                                        if($singleEmployeeShift){
                                            $arrData = $tempDatax;
                                        }else{
                                            $tempIndex = "shift-id_{$vv->shift_id}";
                                            $arrData->{$tempIndex} = $tempDatax;
                                        }
                                    }else{
                                        $tempDatax = new stdClass();
                                        $tempShiftRow = new stdClass();
                                        foreach ($shiftIndexes as $kkx => $vvx) {
                                            $tempAlterIndex = $alterShiftIndexes[$kkx];
                                            if($tempRow->{$vvx} && $tempRow->{$vvx} !== null && $hasShift === true){
                                                $tempShiftRow->{$tempAlterIndex} = $tempRow->{$vvx};
                                            }else{
                                                $tempShiftRow->{$tempAlterIndex} = null;
                                            }
                                        }

                                        $tempDatax->schedule = $tempShiftRow;
                                        $tempDatax->custom_shift_id = $tempRow->id;
                                        $tempDatax->has_shift = $has_shift_value;
                                        if($singleEmployeeShift){ $arrData = $tempDatax; }
                                        else{
                                            $tempIndex = "shift-id_{$vv->shift_id}";
                                            $arrData->{$tempIndex} = $tempDatax;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $arrData;
    }

    function getCustomShiftscheduleData($id=null){
        $resultset = array();
        if($id){
            $qTemp = $this->db->get_where($this->tbl_timesheet_customized_shift_schedule, array("id"=>$id));
            if($qTemp->num_rows() == 1){
                $tempRow = $qTemp->row();
                $tempRow->shift_resource = array();

                $shifts = @unserialize($tempRow->shift_id);
                $employees = @unserialize($tempRow->employee_id);
                $tempRow->shift_id = $shifts;
                $tempRow->employee_id = $employees;

                if(is_array($shifts) && count($shifts) > 0){
                    $this->db->select("description");
                    $this->db->from($this->tbl_shift_schedule);
                    $this->db->where("status", 1);
                    $this->db->where("is_active", 1);
                    $this->db->where_in("id", $shifts);
                    $qTempShift = $this->db->get();
                    if($qTempShift->num_rows() > 0){
                        $qResults = $qTempShift->result();
                        $tempShiftDescription = array();
                        foreach ($qResults as $kk => $vv) {
                            $tempShiftDescription[] = $vv->description;
                        }

                        $tempRow->shift_resource = $tempShiftDescription;
                    }
                }
                if(is_array($employees) && count($employees) > 0){
                    $this->db->select("id, lastname, firstname, middlename, suffix");
                    $this->db->from($this->tbl_employees);
                    $this->db->where_in("id", $employees);
                    $qTemp = $this->db->get();
                    if($qTemp->num_rows() > 0){
                        $tempResource = array();
                        $tempIds = array();
                        foreach ($qTemp->result() as $kk => $vv) {
                            $tempRsx = (array) $vv;
                            $tempRs = $this->core_layout->getDisplayName($tempRsx);
                            $tempName = (object) $tempRs;
                            $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No Assigned Name";
                            $tempResource[] = $tempName;
                            $tempIds[] = $vv->id;
                        }
                        $tempRow->employee_resource = $tempResource;
                        $tempRow->employee_id = $tempIds;
                    }
                }

                $resultset["response"] = true;
                $resultset["data"] = $tempRow;
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function getTimeCustomShiftCount($status){
        $this->db->from($this->tbl_timesheet_customized_shift_schedule);
        if($status){ $this->db->where("status", $status); }
        $qTemp = $this->db->get();

        return $qTemp->num_rows();
    }

    function generateShiftScheduleResource($empId=null, $weekday=null){
        $schedule = array();
        if($empId && $weekday){
            $shift_resource = $this->db->select("resource.*")
                ->join($this->tbl_personnel . " personnel", "personnel.shift_id = resource.shift_id", "INNER")
                ->join($this->tbl_employees . " emp", "emp.biometricno = personnel.biometricno", "INNER")
                ->where("emp.id", $empId)
                ->get($this->tbl_shift_schedule_resource . " resource")
                ->row("shift_resource");

            if (!empty($shift_resource)) {
                $schedule = $this->db->select("am_start shift_am_start, am_end shift_am_end, pm_start shift_pm_start, pm_end shift_pm_end")
                    ->where_in("id", unserialize($shift_resource))
                    ->where("weekday", $weekday)
                    ->get($this->tbl_shift_schedule_list)
                    ->row_array();
            }
        }

        return $schedule;
    }

    function setPaidHoliday(){
        $post = $this->input->post();
        $resultset = array();
        if(isset($post) && $post){

            $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
            $qTemp = $this->db->get_where($this->tbl_timesheet, $post);
            if($qTemp->num_rows() === 0){
                $timesheet = $this->arrayToStdClass($this->input->post());
                $weekday = strtolower(date("l", strtotime($timesheet->date)));
                $timesheet->weekday = $weekday;
                $timesheet->total_time_rendered = 8 * 60;
                $timesheet->is_manual = 1;
                $timesheet->manual_mode = "generated";
                $timesheet->manual_by = $logged_in_user_emp_id;
                $timesheet->is_holiday = 1;
                $timesheet->paid_holiday = 1;
                $timesheet->verified = 1;
                $timesheet->verified_by = $logged_in_user_emp_id;
                $timesheet->verified_at = Date("Y-m-d H:i:s");
                $timesheet->has_shift = 0;
                $timesheet->overtime_in = null;
                $timesheet->overtime_out = null;
                $timesheet->total_accredited_ot_hrs = null;
                $timesheet->total_accredited_ndiff_ot_hrs = null;
                $shiftSchedule = $this->generateShiftScheduleResource($timesheet->emp_id, $timesheet->weekday);
                if(isset($shiftSchedule) && $shiftSchedule && count($shiftSchedule) > 0){
                    $shiftSchedule = $this->arrayToStdClass($shiftSchedule);
                    $timesheet->has_shift = 1;
                    $timesheet->shift_am_start = $shiftSchedule->shift_am_start;
                    $timesheet->shift_am_end = $shiftSchedule->shift_am_end;
                    $timesheet->shift_pm_start = $shiftSchedule->shift_pm_start;
                    $timesheet->shift_pm_end = $shiftSchedule->shift_pm_end;
                }

                $inserted = $this->db->insert($this->tbl_timesheet, $timesheet);
                if($inserted){
                    $timesheetId = $this->db->insert_id();
                    $tempData = new stdClass();
                    $tempData->timesheet_id = $timesheetId;
                    $tempData->created_by = $logged_in_user_emp_id;
                    $tempData->created_at = Date("Y-m-d H:i:s");
                    $addedLog = $this->db->insert($this->tbl_timesheet_paid_holiday, $tempData);
                    if($addedLog){
                        $resultset["response"] = true;
                        $resultset["data"] = $timesheet;
                    }else{
                        $resultset["response"] = false;
                    }
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $tempWhere = array();
                $tempWhere["id"] = $qTemp->row()->id;
                $hasOvertime = intval($qTemp->row()->has_overtime) === 1;

                $timesheet = new stdClass();

                $timesheet->am_late = 0;
                $timesheet->pm_late = 0;
                $timesheet->am_ut = 0;
                $timesheet->pm_ut = 0;
                $timesheet->total_late = 0;
                $timesheet->total_ut = 0;
                $timesheet->am_time_rendered = 0;
                $timesheet->pm_time_rendered = 0;
                
                $timesheet->total_time_rendered = 8 * 60;
                $timesheet->paid_holiday = 1;
                $timesheet->verified = 1;
                $timesheet->verified_by = $logged_in_user_emp_id;
                $timesheet->verified_at = Date("Y-m-d H:i:s");

                if($hasOvertime === false){
                    $timesheet->overtime_in = null;
                    $timesheet->overtime_out = null;
                    $timesheet->total_accredited_ot_hrs = null;
                    $timesheet->total_accredited_ndiff_ot_hrs = null;
                }

                $updated = $this->db->update($this->tbl_timesheet, $timesheet, $tempWhere);
                if($updated){
                    $tempWhere = array();
                    $tempWhere["timesheet_id"] = $qTemp->row()->id;
                    $tempData = new stdClass();
                    $tempData->status = 1;
                    $tempData->updated_by = $logged_in_user_emp_id;
                    $tempData->updated_at = Date("Y-m-d H:i:s");
                    $updatedLog = $this->db->update($this->tbl_timesheet_paid_holiday, $tempData, $tempWhere);
                    if($updatedLog){
                        $resultset["response"] = true;
                        $resultset["data"] = $timesheet;
                    }else{
                        $resultset["response"] = false;
                    }
                }else{
                    $resultset["response"] = false;
                }
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    public function undoPaidHoliday(){
        $post = $this->input->post();
        $resultset = array();
        if(isset($post) && $post){
            $logged_in_user_emp_id = $this->logged_in_user["emp_id"];
            $tempWhere = $post;
            $tempWhere["is_holiday"] = 1;
            $tempWhere["paid_holiday"] = 1;

            $qTemp = $this->db->get_where($this->tbl_timesheet, $tempWhere);
            if($qTemp->num_rows() == 1){
                $tempId = $qTemp->row()->id;
                $alteredData = new stdClass();
                $alteredData->paid_holiday = 0;
                $alteredData->total_time_rendered = 0;
                $alteredData->verified = 0;
                $alteredData->verified_by = null;
                $alteredData->verified_at = null;
                $alteredData->last_updated_by = $logged_in_user_emp_id;
                $alteredData->last_updated_at = date("Y-m-d H:i:s");

                $updated = $this->db->update($this->tbl_timesheet, $alteredData, $post);
                if($updated && $this->db->affected_rows() > 0){
                    $resultset["response"] = $this->db->update($this->tbl_timesheet_paid_holiday,
                    array(
                        "status"=>0,
                        "updated_by"=>$logged_in_user_emp_id,
                        "updated_at"=>date("Y-m-d H:i:s"),
                    ),
                    array("timesheet_id"=>$tempId));
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function get_series($year, $month, $data) {
        $this->db->select('*');
        $this->db->from($this->tbl_overtime);
        $this->db->where('ref_yr', $year);
        $this->db->where('ref_month', $month);
        if($data == "old"){
            $this->db->order_by('ref_series','asc');
            $query = $this->db->get();
            return $query->num_rows();
        }else{
            $this->db->order_by('ref_series','asc');
            $query = $this->db->get();
            return $query->result();
        }

    }

    function generateOvertimeReferenceNumber($type=1){
        $tempType = $type == 1 ? "new": "old";
        $date = date('Y-m-d H:i:s');
        $year = substr($date, 2, 2);
        $month = substr($date, 5, 2);
        $series = '';

        $list = $this->get_series($year, $month, $tempType);
        if (sizeof($list) > 0) {
            foreach ($list as $arr) {
                $x = $arr->ref_series;
            }
            $series = intval($x) + 1;
            if (strlen($series) == 1) {
                $series = '000' . $series;
            } else if (strlen($series) == 2) {
                $series = '00' . $series;
            } else if (strlen($series) == 3) {
                $series = '0' . $series;
            } else {
                $series = $series;
            }
        } else {
            $series = '0001';
        }

        $arrData = new stdClass();
        $arrData->reference_no = "OT{$year}-{$month}-{$series}";
        $arrData->year = $year;
        $arrData->month = $month;
        $arrData->series = $series;

        return $arrData;
    }

    public function includeMonthlyEmployeeTimesheet(){
        $this->load->model("payroll/employee_m");
        $post = $this->input->post();
        $resultset = array();
        if(isset($post["emp_id"]) && $post["emp_id"]){
            $post["created_by"] = $this->core_layout->getCurrentEmployeeId();
            $post["created_at"] = Date("Y-m-d H:i:s");
            $added = $this->db->insert($this->tbl_timesheet_monthly_employees, $post);
            if($added){
                $resultset["response"] = true;
                $resultset["data"] = $this->employee_m->getMonthlyPaidEmployeeSelect2();
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        return $resultset;
    }

    function getMonthlyPaidEmployeeList() {
        $rowCount = 0;
        $rowData = array();
        $resultset = array();
        $order_val = array(array("column" => "1", "dir" => "desc"));

        $post = $this->input->post();

        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

        $rowData = $this->monthlyPaidEmployeeList($search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->monthlyPaidEmployeeCount($search);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function monthlyPaidEmployeeList($search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
        $filterFields = array("b.lastname", "b.firstname", "c.lastname", "c.firstname");
        $this->db->select("a.*, UPPER(CONCAT(b.firstname, ' ',
        CASE WHEN UPPER(TRIM(b.middlename)) != 'N/A' AND UPPER(TRIM(b.middlename)) != 'NONE' AND
                TRIM(b.middlename) !='' AND b.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(b.middlename, 1, 1), '.') ELSE ''
        END,' ', b.lastname,
        CASE WHEN UPPER(TRIM(b.suffix)) != 'N/A' AND
            UPPER(TRIM(b.suffix !='NONE')) AND b.suffix !='' AND
            b.suffix IS NOT NULL THEN CONCAT(' ', b.suffix) ELSE ''
        END)) as employee_name, UPPER(CONCAT(c.firstname, ' ',
        CASE WHEN UPPER(TRIM(c.middlename)) != 'N/A' AND UPPER(TRIM(c.middlename)) != 'NONE' AND
                TRIM(c.middlename) !='' AND c.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(c.middlename, 1, 1), '.') ELSE ''
        END,' ', c.lastname,
        CASE WHEN UPPER(TRIM(c.suffix)) != 'N/A' AND
            UPPER(TRIM(c.suffix !='NONE')) AND c.suffix !='' AND
            c.suffix IS NOT NULL THEN CONCAT(' ', c.suffix) ELSE ''
        END)) as created_by_name, UPPER(b.employee_status) as employee_status");
        $this->db->from($this->tbl_timesheet_monthly_employees." a");
        $this->db->join($this->tbl_employees." b", "b.id = a.emp_id", "INNER");
        $this->db->join($this->tbl_employees." c", "c.id = a.created_by", "INNER");

        if(isset($search)){
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
        }

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            $data = array();
            foreach ($query->result() as $key => $rs) { $arrData[$key] = $rs; }
            foreach ($arrData as $k => $v) { $data[] = $v; }
            return $data;
        } else {
            return array();
        }
    }

    private function monthlyPaidEmployeeCount($search=null) {
        $filterFields = array("b.lastname", "b.firstname", "c.lastname", "c.firstname");
        $this->db->select("a.*, UPPER(CONCAT(b.firstname, ' ',
        CASE WHEN UPPER(TRIM(b.middlename)) != 'N/A' AND UPPER(TRIM(b.middlename)) != 'NONE' AND
                TRIM(b.middlename) !='' AND b.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(b.middlename, 1, 1), '.') ELSE ''
        END,' ', b.lastname,
        CASE WHEN UPPER(TRIM(b.suffix)) != 'N/A' AND
            UPPER(TRIM(b.suffix !='NONE')) AND b.suffix !='' AND
            b.suffix IS NOT NULL THEN CONCAT(' ', b.suffix) ELSE ''
        END)) as employee_name, , UPPER(CONCAT(c.firstname, ' ',
        CASE WHEN UPPER(TRIM(c.middlename)) != 'N/A' AND UPPER(TRIM(c.middlename)) != 'NONE' AND
                TRIM(c.middlename) !='' AND c.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(c.middlename, 1, 1), '.') ELSE ''
        END,' ', c.lastname,
        CASE WHEN UPPER(TRIM(c.suffix)) != 'N/A' AND
            UPPER(TRIM(c.suffix !='NONE')) AND c.suffix !='' AND
            c.suffix IS NOT NULL THEN CONCAT(' ', c.suffix) ELSE ''
        END)) as created_by_name, UPPER(b.employee_status) as employee_status");
        $this->db->from($this->tbl_timesheet_monthly_employees." a");
        $this->db->join($this->tbl_employees." b", "b.id = a.emp_id", "INNER");
        $this->db->join($this->tbl_employees." c", "c.id = a.created_by", "INNER");

        if(isset($search)){
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
        }

        $query = $this->db->get();
        $count = $query->num_rows();
        return $count;
    }

    public function removeIncludedMonthlyEmployeeTimesheet(){
        $this->load->model("payroll/employee_m");
        $post = $this->input->post();
        $resultset = array();
        if(isset($post["id"]) && $post["id"]){
            $removed = $this->db->delete($this->tbl_timesheet_monthly_employees, $post);
            if($removed){
                $resultset["response"] = true;
                $resultset["data"] = $this->employee_m->getMonthlyPaidEmployeeSelect2();
                $resultset["toastr_msg"] = "Monthly paid employee has been removed!!";
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to remove monthly paid employee on the list!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }
        return $resultset;
    }

    public function timesheetNoOvertimeBreak(){
        $post = $this->input->post();
        $resultset = array();
        if(isset($post["ts_id"]) && is_array($post["ts_id"]) && count($post["ts_id"]) > 0){
            $tempIds = array_unique($post["ts_id"]);
            
            $this->db->select("id, timesheet_id, total_hrs, accredited_hrs, ndiff_hrs, accredited_ndiff_hrs");
            $this->db->where_in("timesheet_id", $tempIds);
            $qtsOT = $this->db->get($this->tbl_timesheet_overtime);
            if($qtsOT->num_rows() > 0){
                $hasUpdates = false;
                foreach ($qtsOT->result() as $ot) {
                    $coreHistoryLog = $this->core_layout->coreHistoryLogs();
                    $coreHistoryLog->setHistoryLogModule("gcctime");
                    $coreHistoryLog->setHistoryLogTableName("gcctimeutility.timesheet");
                    $coreHistoryLog->setHistoryLogTableFieldId($ot->timesheet_id);

                    $totHrs = floatval($ot->total_hrs);
                    $totAccHrs = floatval($ot->accredited_hrs);
                    $ndiffHrs = floatval($ot->ndiff_hrs);
                    $ndiffAccHrs = floatval($ot->accredited_ndiff_hrs);

                    $toUpdateOvertime = array();
                    $toUpdateTimeSheet = array();
                    if(($totHrs > 0 && $totAccHrs >= 0) && ($totHrs != $totAccHrs)){
                        $toUpdateOvertime["accredited_hrs"] = $totHrs;
                        $toUpdateTimeSheet["total_accredited_ot_hrs"] = $totHrs;
                    }
                    if(($ndiffHrs > 0 && $ndiffAccHrs >= 0) && ($ndiffHrs != $ndiffAccHrs)){ 
                        $toUpdateOvertime["accredited_ndiff_hrs"] = $ndiffHrs;
                        $toUpdateTimeSheet["total_accredited_ndiff_ot_hrs"] = $ndiffHrs;
                    }

                    if(is_array($toUpdateOvertime) && count($toUpdateOvertime) > 0){
                        $toUpdateOvertime["remarks"] = "Overtime with no break";
                        $updatedOt = $this->db->update($this->tbl_timesheet_overtime, $toUpdateOvertime, array("id"=>$ot->id));
                        if($updatedOt && $this->db->affected_rows() > 0){
                            $toUpdateTimeSheet["has_overtime"] = 2;
                            $updateTimesheet = $this->db->update($this->tbl_timesheet, $toUpdateTimeSheet, array("id"=>$ot->timesheet_id));
                            if($updateTimesheet){                                
                                $this->db->select("TRIM(UCASE(CONCAT(emp.lastname,
                                CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''  END, ', ',
                                emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE'
                                AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END))) as employee_name, ts.emp_id");
                                $this->db->join($this->tbl_employees." emp", "emp.id = ts.emp_id", "INNER");
                                $tsLog = $this->db->get_where($this->tbl_timesheet." ts", array("ts.id"=>$ot->timesheet_id));
                                if($tsLog->num_rows() == 1){
                                    $rowLogs = $tsLog->row();
                                    $logMessage = "Timesheet overtime request of employee name `{$rowLogs->employee_name}` has been updated and was set to overtime with no break time record.";
                                    
                                    $coreHistoryLog->setHistoryLogEmployeeId($rowLogs->emp_id);
                                    $coreHistoryLog->setEventLog($logMessage, "update", "success", "gcctimeutility", "user");
                                    $coreHistoryLog->saveLoggedEventHistory();
                                }
                            }

                            $hasUpdates = true;
                        }
                    }
                }

                if($hasUpdates){
                    $resultset["response"] = true;
                    $resultset["message"] = "Timesheet overtime request has been updated successfully.";
                }else{ 
                    $resultset["response"] = false; 
                    $resultset["message"] = "Failed to update timesheet overtime request!";
                }
            }else{ 
                $resultset["response"] = false;
                $resultset["message"] = "Timesheet overtime request not found!";
            }
        }else{ 
            $resultset["response"] = false; 
            $resultset["message"] = "No post data found!";
        }

        return $resultset;
    }

    public function generateDefaultTimesheet(){
        $post = $this->input->post();
        $resultset = array();
        $errors = array();
        if(isset($post) && is_array($post) && !empty($post)){
            $dates = explode("/", $post["dates"]);
            unset($post["dates"]);
            
            if(isset($post["emp_id"]) && is_array($post["emp_id"]) && !empty($post["emp_id"])){
                $tempDateStart = date("Y-m-d", strtotime($dates[0]));
                $tempDateEnd = date("Y-m-d", strtotime($dates[1]));
                $interval = DateInterval::createFromDateString('1 day');

                $dateStart = new DateTime($tempDateStart);
                $dateEnd = new DateTime($tempDateEnd);
                $dateEnd->modify("+1 day");

                $period = new DatePeriod($dateStart, $interval, $dateEnd);
                foreach ($period as $dt) {
                    $date = $dt->format("Y-m-d");
                    $created = $this->create($date, 3, $post["emp_id"]);
                    $this->updateTimesheetHoliday($date);
                    if (intval($created["k"]) === 2) {
                        array_push($errors, $created);
                    }
                }
                
                $resultset["success"] = true;
                $resultset["message"] = "Generate Timesheet Successfully!";
                $resultset["title"] = "Success!";
            }else{
                $resultset["success"] = false;
                $resultset["message"] = "No employee selected!";
                $resultset["title"] = "Error!";
            }
        }else{
            $resultset["success"] = false;
            $resultset["message"] = "No data found!";
            $resultset["title"] = "Error!";
        }
        $resultset["generate_timesheet_errors"] = $errors;
        return $resultset;
    }

    protected function checkNightShiftSchedule($date=null, $schedule=[]){
        if($date && is_array($schedule) && !empty($schedule)){
            $nightShiftParams = $this->db->get_where($this->tbl_time_parameters, array("param_name" => "NIGHT_SHIFT_PARAMS"))->row();
            $nShiftStartTime = isset($nightShiftParams->start_time) ? $nightShiftParams->start_time: "16:00:00";
            $nShiftEndTime = isset($nightShiftParams->end_time) ? $nightShiftParams->end_time: "12:00:00";
            $_nightShiftStart = date("Y-m-d H:i:s", strtotime($date . " " . $nShiftStartTime));
            $_nightShiftEnd = strtotime($nShiftStartTime) > strtotime($nShiftEndTime) ? date("Y-m-d H:i:s", strtotime("+1 day", strtotime($date . " " . $nShiftEndTime))): date("Y-m-d H:i:s", strtotime($date . " " . $nShiftEndTime));
            $startOfShift = reset($schedule);
            return strtotime($startOfShift) >= strtotime($_nightShiftStart) && strtotime($startOfShift) <= strtotime($_nightShiftEnd);
        }else{ return false; }
    }

    protected function nightshiftParams($date=null){
        $date = $date ?? date("Y-m-d");
        $nightShiftParams = $this->db->get_where($this->tbl_time_parameters, array("param_name" => "NIGHT_SHIFT_PARAMS"))->row();
        $nShiftStartTime = isset($nightShiftParams->start_time) ? $nightShiftParams->start_time: "12:01:00";
        $nShiftEndTime = isset($nightShiftParams->end_time) ? $nightShiftParams->end_time: "12:00:00";
        $_nightShiftStart = date("Y-m-d H:i:s", strtotime($date . " " . $nShiftStartTime));
        $_nightShiftEnd = strtotime($nShiftStartTime) > strtotime($nShiftEndTime) ? date("Y-m-d H:i:s", strtotime("+1 day", strtotime($date . " " . $nShiftEndTime))): date("Y-m-d H:i:s", strtotime($date . " " . $nShiftEndTime));
        return array("start" => $_nightShiftStart, "end" => $_nightShiftEnd);
    }

    protected function overtimeComputationScript($parameters=[]){
        $hasShiftSchedule = $parameters["hasShiftSchedule"] ?? false;
        $_overtime = $parameters["overtime"] ?? null;
        $tempRow = $parameters["timesheetExist"] ?? null;
        $tempAttrAttendance = $parameters["otAttendances"] ?? [];
        $night_diff_cfg = $parameters["nightDiffCfg"] ?? null;
        $shift_basis = $parameters["shiftBasis"] ?? null;
        $_otNdiffStart = $parameters["otNdiffStart"] ?? null;
        $_otNdiffEnd = $parameters["otNdiffEnd"] ?? null;
        $nDiffOTHours = $parameters["nDiffOTHours"] ?? 0;
        $isNightShift = $parameters["isNightShift"] ?? false;

        $regularOTHours = $parameters["regularOTHours"] ?? 0;
        $ot_night_diff = $parameters["otNightDiff"] ?? 0;

        $ndiff_end = null;
        $otAfterShift = false;
        $hasPreviousShift = true;

        $otNightDiffOnly = new stdClass();
        $otNightDiffOnly->is_night_diff = false;
        $otNightDiffOnly->ot_regular = 0;
        $otNightDiffOnly->ot_night_diff = 0;

        $_previousNightDiff = date("Y-m-d H:i", strtotime("-1 day", strtotime($_otNdiffStart)));
        $_nextNightDiff = date("Y-m-d H:i", strtotime("-1 day", strtotime($_otNdiffEnd)));

        $ot_start_dtr = date("Y-m-d H:i", strtotime($tempAttrAttendance[0]));
        $ot_end_dtr = date("Y-m-d H:i", strtotime($tempAttrAttendance[sizeof($tempAttrAttendance) - 1]));

        $arrShifts = array();
        $shiftProps = array("shift_am_start", "shift_am_end", "shift_pm_start", "shift_pm_end");
        $tempShiftDate = date("Y-m-d", strtotime($tempRow->date));
        foreach ($shiftProps as $key => $value) {
            if($tempRow->{$value}){
                if($key > 0 ){
                    $prevValue = $tempRow->{$shiftProps[$key-1]};
                    if(strtotime($tempRow->{$value}) < strtotime($prevValue)){ $tempShiftDate = date("Y-m-d", strtotime("+1 day", strtotime($tempShiftDate))); }
                }
                $nValue = date("Y-m-d H:i", strtotime($tempShiftDate." ".$tempRow->{$value}));
                $arrShifts[] = $nValue;
            }
        }

        if(is_array($arrShifts) && !empty($arrShifts)){
            $firstShift = reset($arrShifts);
            if(strtotime($ot_start_dtr) < strtotime($firstShift)){
                $shift_basis = $firstShift;
            }
        }

        $originalShiftBasis = $shift_basis;
        $hasPreviousNightShift = false;
        if($isNightShift){
            $previousDate = date("Y-m-d", strtotime("-1 day", strtotime($tempRow->date)));
            $previousTs = $this->db->get_where($this->tbl_timesheet, array(
                "emp_id" => $tempRow->emp_id,
                "date" => $previousDate
            ));

            if($previousTs->num_rows() == 1){
                $prevRow = $previousTs->row();
                
                $privDate = date("Y-m-d", strtotime($prevRow->date));
                $privNightShiftParams = $this->nightshiftParams($privDate);

                $hasPreviousShift = intval($prevRow->has_shift) === 1;
                $collectionScript = $this->attendanceCollectionScript($prevRow);
                $_tempAttrAttendance = $collectionScript["arrAttendance"];
                $ot_start_dtr = date("Y-m-d H:i", strtotime($_tempAttrAttendance[0]));
                $ot_end_dtr = date("Y-m-d H:i", strtotime($_tempAttrAttendance[sizeof($_tempAttrAttendance) - 1]));

                $hasPreviousNightShift = strtotime($ot_start_dtr) >= strtotime($privNightShiftParams["start"]) && strtotime($ot_end_dtr) <= strtotime($privNightShiftParams["end"]);
            }
        } 

        $ot_start = strtotime($ot_start_dtr) < strtotime(date('Y-m-d H:i', strtotime($_overtime->date_from)))
            ? date('Y-m-d H:i', strtotime($_overtime->date_from)) : $ot_start_dtr;

        $ot_end = strtotime($ot_end_dtr) > strtotime(date('Y-m-d H:i', strtotime($_overtime->date_to)))
            ? date('Y-m-d H:i', strtotime($_overtime->date_to)): $ot_end_dtr;

        if(strtotime($shift_basis) > strtotime($ot_start) && strtotime($shift_basis) < strtotime($ot_end)){
            $ot_end = $shift_basis;
        }
        
        if($hasPreviousNightShift){
            $shift_basis = strtotime($shift_basis) > strtotime($_nextNightDiff) ? date("Y-m-d H:i", strtotime("-1 day", strtotime($shift_basis))) : $shift_basis;
            $ot_end = strtotime($shift_basis) > strtotime($ot_start) && strtotime($shift_basis) > strtotime($_nextNightDiff) ? $shift_basis : $_nextNightDiff;
        }
        
        $isValidOvertime = strtotime($ot_end) > strtotime($ot_start);
        $allowNightDiff = strtotime($ot_end) >= strtotime($_otNdiffStart) && strtotime($ot_end) <= strtotime($_otNdiffEnd);
        if($isNightShift && $isValidOvertime){
            $_ndiffStart = date("Y-m-d H:i", strtotime("-1 day", strtotime($_otNdiffStart)));
            $_ndiffEnd = date("Y-m-d H:i", strtotime("-1 day", strtotime($_otNdiffEnd)));
            $allowNightDiff = strtotime($ot_end) >= strtotime($_ndiffStart) && strtotime($ot_end) <= strtotime($_ndiffEnd);
        }

        if($isNightShift === false && $isValidOvertime && $allowNightDiff){
            $_previousNightDiff = $_otNdiffStart;
            $_nextNightDiff = $_otNdiffEnd;
        }

        $allowPreviousNightDiff = false;
        if($isNightShift === false && $isValidOvertime && $hasPreviousNightShift === false
            && (strtotime($ot_start) < strtotime($shift_basis) && strtotime($ot_start) > strtotime($_previousNightDiff) && strtotime($ot_start) < strtotime($_nextNightDiff))){
            $allowNightDiff = true;
            $allowPreviousNightDiff = true;
        }

        /*** before shift OT ***/
        if($allowPreviousNightDiff === false && strtotime($ot_start) > strtotime($ot_end) && strtotime($ot_start) < strtotime($originalShiftBasis)){
            $ot_end = $originalShiftBasis;
            $isValidOvertime = strtotime($ot_end) > strtotime($ot_start);
        }
        /*** before shift OT ***/

        if($allowNightDiff && $hasPreviousShift && $isValidOvertime && strtotime($ot_start) >= strtotime($_previousNightDiff) && strtotime($ot_start) < strtotime($_nextNightDiff)){
            $tempOTE = 0;
            $tempRegularOT = 0;
            if(strtotime($ot_end) > strtotime($_previousNightDiff) && strtotime($ot_end) <= strtotime($_nextNightDiff)){
                $tempOTE = strtotime($ot_end) - strtotime($ot_start);
            }else{
                $tempOTE = strtotime($_nextNightDiff) - strtotime($ot_start);
                if(strtotime($ot_end) < strtotime($_nextNightDiff) || ($allowPreviousNightDiff && strtotime($ot_end) > strtotime($_nextNightDiff))){
                    $tempRegularOT = strtotime($ot_end) - strtotime($_nextNightDiff);
                }
            }
            $otNightDiffOnly->ot_regular = ($tempRegularOT > 0)? $tempRegularOT / 3600: $tempRegularOT;
            $otNightDiffOnly->ot_night_diff = ($tempOTE > 0)? $tempOTE / 3600: $tempOTE;
            $otNightDiffOnly->is_night_diff = true;
        }elseif($allowNightDiff && $hasPreviousShift && $isValidOvertime && strtotime($ot_end) >= strtotime($_previousNightDiff) && strtotime($ot_end) < strtotime($_nextNightDiff)){
            $tempOTE = 0;
            $tempRegularOT = 0;
            if($ot_start <= $_previousNightDiff && $ot_end > $_previousNightDiff){
                $tempOTE = strtotime($ot_end) - strtotime($_previousNightDiff);
                $tempRegularOT = strtotime($_previousNightDiff) - strtotime($ot_start);
            }

            $otNightDiffOnly->ot_regular = ($tempRegularOT > 0)? $tempRegularOT / 3600: $tempRegularOT;
            $otNightDiffOnly->ot_night_diff = ($tempOTE > 0)? $tempOTE / 3600: $tempOTE;
            $otNightDiffOnly->is_night_diff = true;
        }

        if($isValidOvertime && (strtotime($ot_start) >= strtotime($shift_basis)) && $hasShiftSchedule){ $otAfterShift = true; }
        // START OVERTIME NIGHT DIFF CALCULATION
        $afterShiftParams = array(
            "otAfterShift" => $otAfterShift,
            "otStart" => $ot_start,
            "otEnd" => $ot_end,
            "isNightShift" => $isNightShift,
            "nightDiffCfg" => $night_diff_cfg,
            "otNightDiff" => $ot_night_diff
        );
        $ot_night_diff = $this->otAfterShiftNightDiffScript($afterShiftParams);

        // END OVERTIME NIGHT DIFF CALCULATION

        $ot_seconds = $isValidOvertime ? (strtotime($ot_end) - strtotime($ot_start)) : 0;
        $ot_minutes = doubleval($ot_seconds) < 0 ? 0 : (doubleval($ot_seconds) / 60);
        /** night shift **/
        $tempNdiffStart = $isNightShift ? $_previousNightDiff: $_otNdiffStart;
        /** night shift **/

        /*** START OVERTIME W/ NIGHT DIFF COMPUTATION REG OT ***/
        if($ot_night_diff > 0){
            $_ot_seconds = (strtotime($tempNdiffStart) - strtotime($ot_start));
            $ot_minutes = doubleval($_ot_seconds) < 0 ? 0 : (doubleval($_ot_seconds) / 60);
        }
        /*** END OVERTIME W/ NIGHT DIFF COMPUTATION REG OT ***/

        
        $regularOvertimeTotal = $ot_minutes / 60;
        $otTotal = $regularOvertimeTotal + $ot_night_diff;
        /*** deduct 1 hour on overtime with night differential ***/
        if(floatval($otTotal) >= 5 && $nDiffOTHours > 0){
            $ot_night_diff = $ot_night_diff - 1;
        }
        /*** deduct 1 hour on overtime with night differential ***/

        $ot_night_diff = doubleval($ot_night_diff);
        $ot_hrs = number_format($regularOvertimeTotal, 2, '.', '');

        if($otNightDiffOnly->is_night_diff){
            $ot_hrs = number_format($otNightDiffOnly->ot_regular, 2, '.', '');
            $ot_night_diff = number_format($otNightDiffOnly->ot_night_diff, 2, '.', '');
            $regularOTHours = $ot_hrs;
            $nDiffOTHours = $ot_night_diff;
        }
        
        if($ot_hrs >= 5){ $ot_hrs = $ot_hrs - 1; }
        
        if($regularOTHours > 0 && floatval($ot_hrs) > $regularOTHours && $otAfterShift){
            $tempOTDiff = 0;
            $tempOTDiff = floatval($ot_hrs) - $regularOTHours;
            $ot_hrs = $regularOTHours;
            $ot_night_diff = $ot_night_diff + $tempOTDiff;
        }
        
        
        /*** start after night diff regular hours inclusion ***/
        if(($ot_end && $ndiff_end) && floatval($ot_hrs) > 0 && strtotime($ot_end) > strtotime($ndiff_end)){
            $ext_ot_seconds = strtotime($ot_end) - strtotime($ndiff_end);
            $ext_ot_minutes = doubleval($ext_ot_seconds) < 0 ? 0 : (doubleval($ext_ot_seconds) / 60);
            $ext_regularOvertimeTotal = $ext_ot_minutes / 60;
            $ot_hrs = $ot_hrs + $ext_regularOvertimeTotal;

            if(($regularOTHours > 0 && $nDiffOTHours > 0) && $nDiffOTHours > $ext_regularOvertimeTotal ){
                $regularOTHours = $regularOTHours + $ext_regularOvertimeTotal;
                $nDiffOTHours = $nDiffOTHours - $ext_regularOvertimeTotal;
            }
        }
        /*** end after night diff regular hours inclusion ***/
        
        
        $ot_night_diff = number_format($ot_night_diff, 2, '.', '');
        return array('ot_hrs' => $ot_hrs, 'ot_night_diff' => $ot_night_diff,
        'regularOTHours' => $regularOTHours, 'nDiffOTHours' => $nDiffOTHours,
        "ot_start" => $ot_start, "ot_end" => $ot_end);
    }

    protected function attendanceCollectionScript($prevRow=null){
        $am_start = $prevRow->am_in ? date("H:i:s", strtotime($prevRow->am_in)): null;
        $am_end = $prevRow->am_out ? date("H:i:s", strtotime($prevRow->am_out)): null;
        $pm_start = $prevRow->pm_in ? date("H:i:s", strtotime($prevRow->pm_in)): null;
        $pm_end = $prevRow->pm_out ? date("H:i:s", strtotime($prevRow->pm_out)): null;
        
        $_attendance_am_start = $am_start ? date("Y-m-d H:i", strtotime("{$prevRow->date} {$am_start}")): null;
        $_attendance_am_end = $am_end ? date("Y-m-d H:i", strtotime("{$prevRow->date} {$am_end}")): null;
        $_attendance_pm_start = $pm_start ? date("Y-m-d H:i", strtotime("{$prevRow->date} {$pm_start}")): null;
        $_attendance_pm_end = $pm_end ? date("Y-m-d H:i", strtotime("{$prevRow->date} {$pm_end}")): null;
        
        $_tempAttrAttendance = array();
        $arrAttendance = $this->nextDayAttendanceScript($_attendance_am_start, $_attendance_am_end, $_attendance_pm_start, $_attendance_pm_end);
        if(is_array($arrAttendance) && !empty($arrAttendance)){
            foreach ($arrAttendance as $key => $value) {
                if($value){
                    $tempKey = "_attendance_{$key}";
                    ${$tempKey} = $value;
                    $_tempAttrAttendance[] = $value;
                }
            }
        }
        
        return array("arrAttendance" => $_tempAttrAttendance,
        "attendance" => array("am_start" => $_attendance_am_start, "am_end" => $_attendance_am_end, "pm_start" => $_attendance_pm_start, "pm_end" => $_attendance_pm_end));
    }

    protected function nextDayAttendanceScript($am_start=null, $am_end=null, $pm_start=null, $pm_end=null){
        $_hasNextDayAttendance = false;
        if(($am_start && $am_end) && (strtotime($am_end) < strtotime($am_start))){
            $am_end = date("Y-m-d H:i", strtotime("+1 day", strtotime($am_end)));
            $_hasNextDayAttendance = true;
        }
        if(($am_end && $pm_start) && (strtotime($pm_start) < strtotime($am_end) || ($_hasNextDayAttendance && $pm_start))){
            $pm_start = date("Y-m-d H:i", strtotime("+1 day", strtotime($pm_start)));
            $_hasNextDayAttendance = true;
        }
        if(($pm_start && $pm_end) && (strtotime($pm_end) < strtotime($pm_start) || ($_hasNextDayAttendance && $pm_end))){
            $pm_end = date("Y-m-d H:i", strtotime("+1 day", strtotime($pm_end)));
        }

        return array("am_start" => $am_start, "am_end" => $am_end, "pm_start" => $pm_start, "pm_end" => $pm_end);
    }


    protected function otAfterShiftNightDiffScript($parameters = []){
        $otAfterShift = $parameters['otAfterShift'] ?? false;
        $ot_start = $parameters['otStart'] ?? null;
        $ot_end = $parameters['otEnd'] ?? null;
        $isNightShift = $parameters['isNightShift'] ?? false;
        $night_diff_cfg = $parameters['nightDiffCfg'] ?? null;
        $ot_night_diff = $parameters['otNightDiff'] ?? 0;
        
        if($otAfterShift){
            $init_start_date = new DateTime($ot_start);
            $start_ndiff_date = $isNightShift ? $init_start_date->modify("-1 day")->format('Y-m-d') : $init_start_date->format('Y-m-d');
            $start_ndiff_time = $night_diff_cfg->start_time;
            $start_ndiff_date_time = date('Y-m-d H:i', strtotime($start_ndiff_date . " " . $start_ndiff_time));

            $init_end_date = new DateTime($ot_end);
            $end_ndiff_date = $init_end_date->format('Y-m-d');
            $end_ndiff_time = $night_diff_cfg->end_time;
            $end_ndiff_date_time = date("Y-m-d H:i", strtotime($end_ndiff_date . " " . $end_ndiff_time));

            $ndiff_start = $start_ndiff_date_time;
            $ndiff_end = $end_ndiff_date_time;
            if (strtotime(date('Y-m-d', strtotime($ot_start)))
                === strtotime(date('Y-m-d', strtotime($ot_end)))) {
                $init_end_date = new DateTime($ot_start);
                $end_ndiff_date = $isNightShift ? $init_end_date->modify("-1 day")->format('Y-m-d') : $init_end_date->modify("+1 day")->format('Y-m-d');
                $end_ndiff_time = $night_diff_cfg->end_time;
                $end_ndiff_date_time = date("Y-m-d H:i", strtotime($end_ndiff_date . " " . $end_ndiff_time));
                $ndiff_start = $start_ndiff_date_time;
                $ndiff_end = $end_ndiff_date_time;
            }
            if (strtotime($ndiff_start) >= strtotime($ot_start)) {
                if (strtotime($ot_end) >= strtotime($ndiff_end)) {
                    $ot_night_diff = strtotime($ndiff_end) - strtotime($ndiff_start);
                    $ot_night_diff = $ot_night_diff / 3600;
                } else {
                    $ot_night_diff = strtotime($ot_end) - strtotime($ndiff_start);
                    $ot_night_diff = $ot_night_diff / 3600;
                }
            } elseif (strtotime($ot_end) > strtotime($ot_start)){
                $ot_night_diff = strtotime($ndiff_end) - strtotime($ot_start);
                $ot_night_diff = $ot_night_diff / 3600;
            }
            $ot_night_diff = ($ot_night_diff < 0) ? 0 : $ot_night_diff;
        }

        return $ot_night_diff;
    }

    public function automated_approve_ot($date) {
        $overtimeIds = array();

        $this->db->select('employee_id');
        $this->db->where('allow_auto_overtime', 1);
        $this->db->from($this->tbl_auto_overtime);
        $query = $this->db->get();

        $this->db->reset_query();

        if ($query->num_rows() > 0) {
            $ids = array_column($query->result(), 'employee_id');
            
            if (is_array($ids) && !empty($ids)) {
                $this->db->select('id, employee, date_from, date_to');
                $this->db->where_in('employee', $ids);
                $this->db->where('TIMESTAMPDIFF(HOUR, date_from, date_to) <=', 3); //only gets the record 3hrs and under
                $this->db->where('status', 'Pending');
    
                $this->db->group_start();
                    $this->db->where('DATE(date_from) >= ', date('Y-m-d', strtotime($date . ' -3 days')));
                    $this->db->where('DATE(date_to) <= ', $date);
                $this->db->group_end();
    
                $this->db->from($this->tbl_overtime);
                $q = $this->db->get();
    
                $this->db->reset_query();
    
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $key => $rs) {
                        $maxPayrollDate = $this->getPayrollMaxDate_OT($rs->employee);
                        $isValidDate = $maxPayrollDate !== false ? strtotime($date) > strtotime($maxPayrollDate) : false; //blocks approving of OT when the date approved is greater than the last payroll end date
    
                        if ($isValidDate) {
                            $data = array(
                                'status' => 'Approved',
                                'approved_by' => 0,
                                'approved_at' => date("Y-m-d H:i:s")
                            );
    
                            $this->db->where('id', $rs->id);
                            $_q = $this->db->update($this->tbl_overtime, $data);
    
                            if ($_q) {
                                array_push($overtimeIds, $rs->id);
                            }
                        }
                    }
                }
            }
        }
        return $overtimeIds;
    }

    protected function getPayrollMaxDate_OT($id=null){
        if($id){
            $this->db->select("MAX(ps.date_end) as max_date");
            $this->db->from($this->tbl_employees." emp");
            $this->db->join($this->tbl_payroll_sheet." ps", "ps.emp_id = emp.id AND ps.posted = 1", "LEFT");
            $this->db->where("emp.id", $id);
            $this->db->group_by("emp.id");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() == 1){ return $qTemp->row()->max_date; }
            else{ return false; }
        }else{ return false; }
        
    }

    public function get_automated_approved_ot($ids = array()) {
        $result = array();
        if (count($ids) > 0) {
            $this->db->select("a.*, UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END)) as employee, emp.biometricno");
            $this->db->where_in('a.id', $ids);
            $this->db->join($this->tbl_employees.' as emp', 'emp.id = a.employee', 'LEFT');
            $this->db->from($this->tbl_overtime.' as a');
            $this->db->order_by('a.date_from', 'DESC');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $result = $query->result();
            }
        }

        return $result;
    }
}
