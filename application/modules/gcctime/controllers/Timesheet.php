<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Timesheet extends MY_Controller {
    private $date;
    public function __construct() {
        parent::__construct();
        include_once APPPATH . 'libraries/PHPExcel/IOFactory.php';

        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();

        $this->load->model('Timesheet_model', 'ts_model');
        $this->load->model('payroll/Employee_m', 'payroll_employee_mod');
        $this->date = new DateTime("now", new DateTimeZone("Asia/Manila"));
    }

    private function arrayToStdClass($array) {
        return json_decode(json_encode($array));
    }

    public function master() {
        $this->core_layout->setPrivilegeName("gcctime_timesheet_master");
        $this->core_layout->addCss("css/time/timesheet/timesheet.styles.css");
        $this->core_layout->addJs("js/buttons.print.min.js", TRUE);

        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);

        $version = filemtime(FCPATH.'assets/js/time/timesheet/master.script.js');
        $arrData = array("monthly_employees" => $this->payroll_employee_mod->getMonthlyPaidEmployeeSelect2());
        $this->core_layout->addJs("js/time/timesheet/master.script.js", TRUE, $arrData, "?v={$version}");

        $data = array(
            "yesterday" => $this->date->modify('-1 day')->format("M. d, Y"),
            "companies" => $this->db->select("id, code `text`")->where(array("is_archived"=>0, "exclude"=>0))->get("gcchris.tblcompanies")->result(),
            "for_select" => $this->payroll_employee_mod->getDropdownSelectData(),
            "excluded_employees" => $this->ts_model->getExcludedEmployees(),
            "biometric_devices" => $this->db->get("gcctimeutility.devices")->result(),
        );

        $this->load->view('core/templates/header');
        $this->load->view('timesheet/master/index', $data);
        $this->load->view('core/templates/footer');
    }

    public function time_adjustments() {
        $this->core_layout->setPrivilegeName("gcctime_timesheet_time_adjustments");
        $this->core_layout->addCss("css/time/timesheet/timesheet.styles.css", TRUE);
        $this->core_layout->addJs("demo/default/custom/components/forms/widgets/bootstrap-switch.js", TRUE);

        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
        
        $this->core_layout->addJs("js/time/timesheet/time_adjustment.script.js", TRUE);

        $data = array();
        $data["companies"] = $this->db->select("id, code `text`")->where(array("is_archived"=>0, "exclude"=>0))->get("gcchris.tblcompanies")->result();

        $arrData = array();
        $this->load->view('core/templates/header');
        $this->load->view('timesheet/time_adjustments/index', $data);
        $this->load->view('core/templates/footer');
    }

    public function custom_shift_schedule() {
        $this->core_layout->setPrivilegeName("gcctime_timesheet_custom_shift_schedule");
        $this->core_layout->addCss("css/time/timesheet/timesheet.styles.css", TRUE);
        $this->core_layout->addJs("js/time/timesheet/custom_shift_schedule.script.js", TRUE);

        $this->load->view('core/templates/header');
        $this->load->view('timesheet/custom_shift_schedule/index');
        $this->load->view('core/templates/footer');
    }

    public function exclusion() {
        $this->core_layout->setPrivilegeName("gcctime_timesheet_excluded_employees");
        $this->core_layout->addCss("css/time/timesheet/timesheet.styles.css", TRUE);
        $this->core_layout->addJs("js/time/timesheet/excluded_employees.script.js", TRUE);

        $this->load->view('core/templates/header');
        $this->load->view('timesheet/excluded_employees/index');
        $this->load->view('core/templates/footer');
    }

    public function parameter_settings() {
        $this->core_layout->setPrivilegeName("gcctime_timesheet_parameter_settings");
        $this->core_layout->addJs("js/time/timesheet/parameter_settings.script.js", TRUE);

        $this->load->view('core/templates/header');
        $this->load->view('timesheet/parameter_settings/index');
        $this->load->view('core/templates/footer');
    }

    public function get_timesheet() {
        $data = $this->ts_model->getTimeSheet();
        echo json_encode($data);
    }

    public function get_timesheet_row($timesheet_id = null) {
        $data = $this->ts_model->getTimesheetRow($timesheet_id);
        echo json_encode($data);
    }

    public function get_employees_for_filter($limit = 1, $hide_excluded = 0, $newly = 0) {
        echo json_encode($this->ts_model->getEmployeesForFilter($limit, $hide_excluded, $newly));
    }

    public function get_timesheet_record($timesheet_id, $employee_id) {
        echo json_encode($this->ts_model->getTimesheetRecord($timesheet_id, $employee_id));
    }

    public function save_time_adjustment_request($timesheet_id) {
        echo json_encode($this->ts_model->saveTimeAdjustmentRequest($timesheet_id));
    }

    public function verify_selected_time_records() {
        echo json_encode($this->ts_model->verifySelectedTimeRecords());
    }

    public function get_time_adjustments_request($employee_id = null, $timesheet_id = null) {
        echo json_encode($this->ts_model->getTimeAdjustmentRequests($employee_id, $timesheet_id));
    }

    public function confirm_time_adjustment_request() {
        echo json_encode($this->ts_model->confirmTimeAdjustmentRequest());
    }

    public function get_time_adjustment_details($time_adjustment_id = null, $for_modal_details = 0) {
        echo json_encode($this->ts_model->getTimeAdjustmentDetails($time_adjustment_id, $for_modal_details));
    }

    public function update_time_adjustment($time_adjustment_id, $has_shift) {
        echo json_encode($this->ts_model->updateTimeAdjustment($time_adjustment_id, $has_shift));
    }

    public function get_timesheet_time_adjustments_list($timesheet_id, $employee_id) {
        echo json_encode($this->ts_model->getTimesheetTimeAdjustmentsList($timesheet_id, $employee_id));
    }

    public function get_timesheet_more_details($timesheet_id, $employee_id, $date) {
        echo json_encode($this->ts_model->getTimesheetMoreDetails($timesheet_id, $employee_id, $date));
    }

    public function get_custom_shift_schedule_data($custom_timesheet_id) {
        echo json_encode($this->ts_model->getCustomShiftscheduleData($custom_timesheet_id));
    }

    public function import_and_generate_timesheet() {
        ini_set('max_execution_time', 3600);
        echo json_encode($this->ts_model->importAndGenerateTimesheet());
    }

    public function save_employee() {
        echo json_encode($this->payroll_employee_mod->saveEmployee());
    }

    public function check_biometric_no($biometric_no) {
        echo json_encode($this->payroll_employee_mod->checkBiometricNo($biometric_no));
    }

    public function save_no_employee_biometric_no($timesheet_imports_id = null) {
        echo json_encode($this->ts_model->saveNoEmployeeBiometricNo($timesheet_imports_id));
    }

    public function get_import_history() {
        echo json_encode($this->ts_model->getImportHistory());
    }

    public function add_excluded_employee_from_timesheet() {
        echo json_encode($this->ts_model->addExcludedEmployeeFromTimesheet());
    }

    public function get_excluded_employee_list() {
        echo json_encode($this->ts_model->getExcludedEmployeeList());
    }

    public function delete_employee_from_exclusion($id, $multiple = 0) {
        echo json_encode($this->ts_model->deleteEmployeeFromExclusion($id, $multiple));
    }

    public function get_excluded_employee_detail($id) {
        echo json_encode($this->ts_model->getExcludedEmployeeDetail($id));
    }

    public function update_excluded_employee($id) {
        echo json_encode($this->ts_model->updateExcludedEmployee($id));
    }

    public function download_timesheet_excel_template() {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', "BIOMETRIC NO");
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', "DATE");
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', "AM IN");
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', "AM OUT");
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', "PM IN");
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', "PM OUT");
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', "OT");
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', "NDOT");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="TIMESHEET_TEMPLATE.xlsx"');
        header('Cache-Control: max-age=0');
        ob_start();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response = array(
            'op' => 'ok',
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );

        echo json_encode($response);
    }

    public function accept_import_updates($timesheet_id = null, $timesheet_imports_id = null) {
        echo json_encode($this->ts_model->acceptImportUpdates($timesheet_id, $timesheet_imports_id));
    }

    public function get_biometric_possible_matches_from_employees($biometricno) {
        echo json_encode($this->ts_model->getBiometricPossibleMatchesFromEmployees($biometricno));
    }

    public function link_employee_to_biometric_no_in_personnel() {
        echo json_encode($this->ts_model->linkEmployeeToBiometricNoInPersonnel());
    }

    public function get_shift_schedules() {
        echo json_encode($this->ts_model->getShiftSchedule());
    }

    public function undo_timesheet_verification($timesheet_id) {
        echo json_encode($this->ts_model->undoTimesheetVerification($timesheet_id));
    }

    public function get_employees_for_look_up_and_update_biometric_no() {
        echo json_encode($this->ts_model->getEmployeesForLookUpAndUpdateBiometricNo());
    }

    public function update_employee_biometric_no_from_timesheet_imports() {
        echo json_encode($this->ts_model->updateEmployeeBiometricNoFromTimesheetImports());
    }

    public function get_shift_schedule_for_filter($id=null) {
        echo json_encode($this->ts_model->getShiftScheduleForFilter($id));
    }

    public function get_shift_schedule_for_employee_filter($id=null) {
        echo json_encode($this->ts_model->getShiftScheduleForEmployeeFilter($id));
    }

    public function get_custom_shift_schedule_request() {
        echo json_encode($this->ts_model->getCustomShiftScheduleRequest());
    }

    public function set_custom_shift_schedule() {
        echo json_encode($this->ts_model->setCustomShiftSchedule());
    }

    public function update_custom_shift_schedule() {
        echo json_encode($this->ts_model->updateCustomShiftSchedule());
    }

    public function remove_custom_shift_schedule() {
        echo json_encode($this->ts_model->removeCustomShiftSchedule());
    }

    public function set_paid_holiday() {
        echo json_encode($this->ts_model->setPaidHoliday());
    }
    
    public function undo_paid_holiday() {
        echo json_encode($this->ts_model->undoPaidHoliday());
    }

    function select_payroll_group() {
        $this->load->model("payroll/payroll_m", "payroll");
        $data = $this->payroll->selectPayrollGroup();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function test($timesheet_id=null) {
        $this->ts_model->approvedOvertimeAdjustments($timesheet_id);
    }
    
    public function test_date(){
        $date = "2021-06-04";
        // $date = "2021-03-31";
        $id = null;

        $data = $this->ts_model->getCustomizedShiftScheduleByDate($date, $id);
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
    }

    public function test_calculate_timesheet($id=null){
        $qTemp = $this->db->get_where("gcctimeutility.timesheet", array("id"=>$id));
        if($qTemp->num_rows() == 1){
            echo "<pre>";
            $data = $this->ts_model->updateTimesheetShiftComputation($qTemp->row());
            var_dump($data);
        }
    }

    public function include_monthly_employee_timesheet(){
        echo json_encode($this->ts_model->includeMonthlyEmployeeTimesheet());
    }

    public function get_monthly_paid_employee_list(){
        echo json_encode($this->ts_model->getMonthlyPaidEmployeeList());
    }

    public function remove_included_monthly_employee_timesheet(){
        echo json_encode($this->ts_model->removeIncludedMonthlyEmployeeTimesheet());
    }

    public function timesheet_no_overtime_break(){
        echo json_encode($this->ts_model->timesheetNoOvertimeBreak());
    }

    public function generate_default_timesheet(){
        ini_set('max_execution_time', 7200);
        echo json_encode($this->ts_model->generateDefaultTimesheet());
    }

    //here
    public function tag_date_restday() {
        echo json_encode($this->ts_model->tag_date_restday());
    }

    public function undo_restday() {
        echo json_encode($this->ts_model->undoRestday());
    }
}
/* End of file Timesheet.php */