<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Hris extends MY_Controller
    {
        function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("hris");
            $this->authenticate->doRedirect();

            $this->core_layout->setBodyClass("hris");
            $this->core_layout->setPrivilegeName("hris");

            $this->load->model("Dashboard_model", "dashboard");
            $this->load->model("Employee_model", "employee");

            date_default_timezone_set('Asia/Manila');
        }

        function index()
        {
            $this->core_layout->addJs("global/js/amcharts4/core.js", true);
            $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
            $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
            $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);

            $this->core_layout->addJs('js/hris/search_employee_script.js', TRUE);
            $this->core_layout->addJs('js/hris/index_script.js', TRUE);

            $this->core_layout->addCss('css/hris/index.css', TRUE);

            /*** $nextMonth = date("Y-m-d", strtotime("+1 month"));
            $nextMonthDate = date("Y-m-d", strtotime("first day of this month", strtotime($nextMonth))); ***/
            $tempNextMonth = date("Y-m-d", strtotime("first day of +1 month"));

            $data['company_data'] = $this->dashboard->getActiveEmployeesOnEachCompanyTabular();
            $data['employees_with_anniversary'] = $this->dashboard->getEmployeesWithAnniversary();
            $data['employees_with_birthday'] = $this->dashboard->getEmployeesWithBirthDay();
            $data['next_employees_with_birthday'] = $this->dashboard->getEmployeesWithBirthDay($tempNextMonth);
            $data['newly_hired'] = $this->dashboard->getNewlyHiredEmployees();
            $data["years"] = $this->dashboard->getYearList();
            
            $this->load->view("core/templates/header");
            $this->load->view("hris/index", $data, FALSE);
            $this->load->view("core/templates/footer");
        }

        function notification(){
            $data["nearing_one_month"] = $this->employee->getNearingOneMonthEmployees();
            $this->load->view("core/templates/header");
            $this->load->view("hris/masterfile/employee/notification/index", $data, FALSE);
            $this->load->view("core/templates/footer");
        }

        function active_employee_report(){
            $this->load->view("core/templates/external/header");
            $data = $this->employee->getActiveEmployeeContributionData();
            $this->load->view("hris/masterfile/employee/active_report", array("employees"=>$data));
            $this->load->view("core/templates/external/footer");
        }
    }