<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Archive extends MY_Controller
    {
        function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("hris");
            $this->authenticate->doRedirect();

            $this->load->model("Employee_model", "employee");
            $this->load->model("ams/Utilities_model", "utilities");
            $this->load->model("Archive_model", "archive_m");

            date_default_timezone_set('Asia/Manila');
        }

        public function employees()
        {
            $this->core_layout->addJs("js/hris/archive/archived_employees_script.js", TRUE);

            $this->load->view("core/templates/header");
            $this->load->view("hris/archive/employees");
            $this->load->view("core/templates/footer");
        }

        public function personnel_request()
        {
            $this->core_layout->addJs("js/hris/archive/archived_personnel_request.js", TRUE);

            $this->load->view("core/templates/header");
            $this->load->view("hris/archive/personnel_request");
            $this->load->view("core/templates/footer");
        }

        public function loans()
        {
            $this->core_layout->addJs("js/hris/archive/archived_loans.js", TRUE);

            $this->load->view("core/templates/header");
            $this->load->view("hris/archive/loans");
            $this->load->view("core/templates/footer");
        }


        public function get_archived_employees()
        {
            $data = $this->archive_m->getArchivedEmployees();
            echo json_encode($data);
        }

        public function archive_employee($emp_id = null)
        {
            $data = $this->archive_m->archiveEmployee($emp_id);
            echo json_encode($data);
        }

        public function restore_employee($emp_id = null)
        {
            $data = $this->archive_m->restoreEmployee($emp_id);
            echo json_encode($data);
        }

        public function get_archived_personnel_request()
        {
            $data = $this->archive_m->getArchivedPersonnelRequest();
            echo json_encode($data);
        }
        
        public function restore_personnel_request($personnel_request_id = null)
        {
            $data = $this->archive_m->restorePersonnelRequest($personnel_request_id);
            echo json_encode($data);
        }

        public function get_archived_loans()
        {
            $data = $this->archive_m->getArchivedLoans();
            echo json_encode($data);
        }
    }