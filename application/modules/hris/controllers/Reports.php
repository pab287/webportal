<?php defined('BASEPATH') || exit('No direct script access allowed');
class Reports extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("hris");
        $this->authenticate->doRedirect();

        $this->load->model("Holiday_model", "holiday");
        $this->load->model("Employee_model", "employee");
        $this->load->model("ams/Utilities_model", "utilities");
        $this->load->model("Reports_model", "report");
        $this->load->model("Company_model", "company");
        $this->load->model("Department_model", "department");
        $this->load->model("Position_model", "position");

        $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
        $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");

        date_default_timezone_set('Asia/Manila');
    }

    public function creator()
    {
        $this->core_layout->setPrivilegeName("hris_report_creator");
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', true);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', true);
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
        /* DATATABLE PRINT CONFIG */
        $this->core_layout->addJs("js/dataTables.buttons.min.js", true);
        $this->core_layout->addJs("js/buttons.flash.min.js", true);
        $this->core_layout->addJs("js/jszip.min.js", true);
        $this->core_layout->addJs("js/pdfmake.min.js", true);
        $this->core_layout->addJs("js/vfs_fonts.js", true);
        $this->core_layout->addJs("js/buttons.html5.min.js", true);
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addCss("css/buttons.dataTables.min.css", true);
        /* END DATATABLE PRINT CONFIG */

        $this->core_layout->addJs('js/hris/reports/report_creator_script.js', true);
        $data = null;
        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/report_creator", $data, false);
        $this->load->view("core/templates/footer");
    }

    public function late_absentee_report(){
        $this->core_layout->setPrivilegeName("hris_late_absentee_report");
        $this->core_layout->setPageTitle("HRIS - Late And Absentee Report");
        $this->core_layout->setHeaderTitle("Late And Absentee Report");
        
        $data = array();
        $data["years"] = $this->report->getVerifiedTimeSheetYearsData();
        $data["company"] = $this->report->select2CompanyData();
        $data["departments"] = $this->report->select2DepartmentData();

        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs('js/hris/reports/late_absentee_report_script.js', true, $data);

        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/late_absentee_report");
        $this->load->view("core/templates/footer");
    }


    public function generate_employee_report($export=0)
    {
        $data = $this->report->generateEmployeeReport($export);
        echo json_encode($data);
    }

    public function open_modal()
    {
        $data = array();
        $formData = $this->input->post('formData');
        $init_modal_data_function = $this->input->post('init_modal_data_function');
        
        $data['html'] = $this->utilities->openModal();

        if ($init_modal_data_function) {
            $data['info'] = $this->report->$init_modal_data_function($formData);
        }
        echo json_encode($data);
    }

    public function save_field_template()
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        unset($post->field);

        $post->items = json_decode($post->templateItems, true);

        $data = $this->report->saveFieldTemplate($post);
        echo json_encode($data);
    }

    public function get_field_templates()
    {
        $data = $this->report->getFieldTempates(); // get table field templates
        echo json_encode($data);
    }

    public function get_template_body($template_id)
    {
        $data = $this->report->getTemplateBody($template_id);
        echo json_encode($data);
    }

    public function update_field_template()
    {
        $post = $this->utilities->parseFormDataToObject($this->input->post());
        unset($post->field);

        $post->items = json_decode($post->templateItems, true);

        $data = $this->report->updateFieldTemplate($post);
        echo json_encode($data);
    }

    public function expiring_probee_employees()
    {
        /* DATATABLE PRINT CONFIG */
        $this->core_layout->addJs("js/dataTables.buttons.min.js", true);
        $this->core_layout->addJs("js/buttons.flash.min.js", true);
        $this->core_layout->addJs("js/jszip.min.js", true);
        $this->core_layout->addJs("js/pdfmake.min.js", true);
        $this->core_layout->addJs("js/vfs_fonts.js", true);
        $this->core_layout->addJs("js/buttons.html5.min.js", true);
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        
        $this->core_layout->addCss("css/buttons.dataTables.min.css", true);
        /* END DATATABLE PRINT CONFIG */

        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);

        $this->core_layout->addJs('js/hris/reports/expiring_probee_employees_script.js', true);

        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/expiring_probee_employees");
        $this->load->view("core/templates/footer");
    }

    public function expiring_project_based_employees()
    {
        /* DATATABLE PRINT CONFIG */
        $this->core_layout->addJs("js/dataTables.buttons.min.js", true);
        $this->core_layout->addJs("js/buttons.flash.min.js", true);
        $this->core_layout->addJs("js/jszip.min.js", true);
        $this->core_layout->addJs("js/pdfmake.min.js", true);
        $this->core_layout->addJs("js/vfs_fonts.js", true);
        $this->core_layout->addJs("js/buttons.html5.min.js", true);
        $this->core_layout->addJs("js/buttons.print.min.js", true);

        $this->core_layout->addCss("css/buttons.dataTables.min.css", true);
        /* END DATATABLE PRINT CONFIG */

        $this->core_layout->addJs('js/hris/reports/expiring_project_based_employees_script.js', true);

        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/expiring_project_based_employees");
        $this->load->view("core/templates/footer");
    }

    public function salary_range()
    {
        /* DATATABLE PRINT CONFIG */
        $this->core_layout->addJs("js/dataTables.buttons.min.js", true);
        $this->core_layout->addJs("js/buttons.flash.min.js", true);
        $this->core_layout->addJs("js/jszip.min.js", true);
        $this->core_layout->addJs("js/pdfmake.min.js", true);
        $this->core_layout->addJs("js/vfs_fonts.js", true);
        $this->core_layout->addJs("js/buttons.html5.min.js", true);
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        
        $dropdown = array(
            'dropdown_company' => $this->report->getSelect2Companies(),
            'dropdown_department' => $this->report->getSelect2Departments(),
            'dropdown_position' => $this->report->getSelect2Positions(),
        );

        // $arrData["dropdown_data"] = $this->report->getDropdownSelectData();
        $arrData['dropdown_data'] = $dropdown;
        $arrData['years'] = $this->report->getSelect2Year();

        $this->core_layout->addCss("css/buttons.dataTables.min.css", true);
        /* END DATATABLE PRINT CONFIG */

        $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
        $this->core_layout->addJs('js/hris/reports/salary_range_script.js', true, $arrData);

        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/salary_range");
        $this->load->view("core/templates/footer");
    }

    public function on_leave()
    {
        $this->core_layout->addJs("vendors/custom/fullcalendar/fullcalendar.bundle.js", true);
        $this->core_layout->addJs("js/hris/reports/on_leave_script.js", true);
        
        $this->core_layout->addCss("vendors/custom/fullcalendar/fullcalendar.bundle.css", true);
        $this->core_layout->addCss("css/hris/calendar.css", true);
        
        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/on_leave");
        $this->load->view("core/templates/footer");
    }
    
    public function comprehensive_report(){
        $this->core_layout->setPageTitle("HRIS - Comprehensive Report");
        $this->core_layout->setPrivilegeName("hris_comprehensive_report");
        $tempData = array();
        $tempData["company"] = $this->company->select2CompanyData();
        $tempData["department"] = $this->department->select2DepartmentData();
        $tempData["position"] = $this->position->select2PositionData();

        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/hris/reports/comprehensive_script.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/comprehensive");
        $this->load->view("core/templates/footer");
    }

    public function manpower(){
        $this->core_layout->setPageTitle("HRIS - Manpower Report");
        $this->core_layout->setPrivilegeName("hris_manpower_report");
        $tempData = array();
        $tempData["company"] = $this->company->select2CompanyData();
        $tempData["department"] = $this->department->select2DepartmentData();
        /*** $tempData['station'] = $this->employee->getSitePoints(); ***/
        $tempData['station'] = $this->employee->getSitePointStations();

        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/hris/reports/manpower_script.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/manpower");
        $this->load->view("core/templates/footer");
    }

    public function salary_pay_information(){
        $this->core_layout->setPageTitle("HRIS - Salary Pay Information Report");
        $this->core_layout->setPrivilegeName("hris_salary_payinfo_report");

        $tempData = array();
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/hris/reports/salary_payinfo_script.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/salary_payinfo");
        $this->load->view("core/templates/footer");
    }

    public function stations(){
        $this->core_layout->setPageTitle("HRIS - Stations");
        $tempData = array();
        $tempData["company"] = $this->company->select2CompanyData();
        $tempData['station'] = $this->employee->getSitePointStations();
        
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);
        $this->core_layout->addJs("js/hris/reports/station_script.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/stations");
        $this->load->view("core/templates/footer");
    }

    public function age(){
        $this->core_layout->setPageTitle("HRIS - Age");
        $tempData = array();
        $tempData["company"] = $this->company->select2CompanyData();
        $tempData['station'] = $this->employee->getSitePointStations();
        $tempData["department"] = $this->department->select2DepartmentData();
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->setPrivilegeName("hris_report_age");
        $this->core_layout->addJs("js/hris/reports/age_report.js", true,$tempData);
        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/age");
        $this->load->view("core/templates/footer");
    }

    public function get_expiring_employees($export=0)
    {
        $work_status = isset($_GET['work_status']) ? $_GET['work_status'] : null;
        $data = $this->report->getExpiringEmployees($export, $work_status);
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function get_company_collection()
    {
        $data = $this->report->getCompanyCollection();
        echo json_encode($data);
    }

    public function get_department_collection()
    {
        $data = $this->report->getDepartmentCollection();
        echo json_encode($data);
    }

    public function get_employees_for_salary_range($export=0)
    {
        $data = $this->report->getEmployeesForSalaryRange($export);
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function get_employee_leaves()
    {
        $data = $this->report->getEmployeeLeaves();
        echo json_encode($data);
    }

    public function get_position_select2_data(){
        $data = $this->position->getPositionSelect2Data();
        echo json_encode($data);
    }

    public function generate_comprehensive_report(){
        $data = $this->report->generateComprehensiveReport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function generate_manpower_report(){
        $data = $this->report->generateManpowerReport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function generate_manpower_by_company_report(){
        $data = $this->report->generateManpowerByCompanyReport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function generate_training_seminars_report(){
        $data = $this->report->generateTrainingSeminarsReport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function generate_drivers_license_report(){
        $data = $this->report->generateDriversLicenseReport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function generate_licenses_certificate_report(){
        $data = $this->report->generateCertificateReport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function get_salary_payinfo_datatable_request(){
        $data = $this->report->getSalaryPayinfoDatatableRequest();
        echo json_encode($data);
    }

    public function get_select2_employee_data(){
        $data = $this->report->getSelect2EmployeeData();
        echo json_encode($data);
    }

    public function get_select2_department_data(){
        $data = $this->report->getSelect2DepartmentData();
        echo json_encode($data);
    }

    public function generate_late_absentee_report(){
        $data = $this->report->generateLateAbsenteeReport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function select_payroll_group(){
        $data = $this->report->selectPayrollGroup();
        echo json_encode($data);
    }

    public function attrition_report(){
        $this->core_layout->setPrivilegeName("hris_attrition_report");
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', true);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', true);

        $this->core_layout->addJs("global/js/amcharts4/core.js", true);
        $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
        $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);

        /* DATATABLE PRINT CONFIG */
        $this->core_layout->addJs("js/dataTables.buttons.min.js", true);
        $this->core_layout->addJs("js/buttons.flash.min.js", true);
        $this->core_layout->addJs("js/jszip.min.js", true);
        $this->core_layout->addJs("js/pdfmake.min.js", true);
        $this->core_layout->addJs("js/vfs_fonts.js", true);
        $this->core_layout->addJs("js/buttons.html5.min.js", true);
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addCss("css/buttons.dataTables.min.css", true);
        /* END DATATABLE PRINT CONFIG */

        $tempData = array();
        $tempData['company'] = $this->report->getSelect2Companies();
        $tempData['departments'] = $this->report->getSelect2Departments();
        $tempData['year'] = $this->report->getSelect2Year();
        
        $this->core_layout->addJs('js/hris/reports/attrition_report_script.js', true, $tempData);
        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/attrition_report", false);
        $this->load->view("core/templates/footer");
    }

    public function no_earners_report(){
        $this->core_layout->setPageTitle("HRIS - No Earners Report");
        $this->core_layout->setPrivilegeName("hris_no_earners_report");
        $tempData = array();
        $tempData['company'] = $this->report->getSelect2Companies();
        $tempData["payout_schedule"] = $this->report->select2PayoutScheduleData();
        
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);

        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/jquery.autocomplete.min.js");
        $this->core_layout->addJs("js/ams/jquery.maskMoney.min.js", true);
        $this->core_layout->addJs('js/hris/reports/no_earners_report_script.js', true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("masterfile/reports/no_earners_report", false);
        $this->load->view("core/templates/footer");
    }

    public function generate_attrition_report(){
        $data = $this->report->generateAttritionReport();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function generate_attrition_chart(){
        $data = $this->report->generateAttritionChartReport();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_employee_no_stations(){
        $data = $this->report->getEmployeeNoStations();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function set_employees_without_stations(){
        $data = $this->report->setEmployeesWithoutStations();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function log_export(){
        $data = $this->report->logExport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function mass_trigger_station_action($id = null){
        $data = $this->report->setlastEmployeeStation($id);

        echo '<pre>';
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        echo '</pre>';
    }

    public function get_age_report(){
        $data = $this->report->getAgeReport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function get_employee_select2_data(){
        $data = $this->report->getSelect2Employee();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function get_employees_history(){
        $data = $this->report->getEmployeeSalaryHistory();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function select_ps_payroll_group() {
        $data = $this->report->selectPsPayrollGroup();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_payroll_group_multiple() {
        $data = $this->report->getPayrollGroupMultiple();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function select_employee() {
        $data = $this->report->selectEmployee();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function no_earners_report_filtered_data() {
        $data = $this->report->noEarnerReportFilteredData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
}
