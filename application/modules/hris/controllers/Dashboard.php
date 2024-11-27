<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Dashboard extends MY_Controller
    {
        function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("hris");
            $this->authenticate->doRedirect();

            $this->load->model("Employee_model", "employee");
            $this->load->model("ams/Utilities_model", "utilities");
            $this->load->model("Dashboard_model", "dashboard");
            date_default_timezone_set('Asia/Manila');
        }

        public function search_employee()
        {
            
            $searchKey = $this->input->post('searchKey');
            $filter = $this->input->post('filter');
            $data = $this->employee->searchEmployee($searchKey,$filter);
            echo json_encode($data);
        }

        public function get_gender_demographics()
        {
            $data = $this->dashboard->getGenderDemographics();
            echo json_encode($data);
        }

        public function get_each_employee_status_demographics()
        {
            $data = $this->dashboard->getEachEmployeeStatusDemographics();
            echo json_encode($data);
        }

        public function get_active_employees_on_each_company($sort="DESC")
        {
            $data = $this->dashboard->getActiveEmployeesOnEachCompany($sort);
            echo json_encode($data);
        }

        public function get_on_leave_employees()
        {
            $data = $this->dashboard->getOnLeaveEmployees();
            echo json_encode($data);
        }

        public function get_evaluation_list($evaluation = null) {
            $data = $this->dashboard->getEvaluationList($evaluation);
            echo json_encode($data);
        }

        public function get_retention_rate($ctr=0) {
            $data = $this->dashboard->getRetentionRate($ctr);
            echo json_encode($data);
        }

        public function get_retention_rate_by_year($year=null) {
            $data = $this->dashboard->getRetentionRateByYear($year);
            echo json_encode($data);
        }

        public function get_personnel_request_summary() {
            $data = $this->dashboard->getPersonnelRequestSummary();
            echo json_encode($data);
        }
    }