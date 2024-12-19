<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Employee_model extends CI_Model {
        protected $employeeTable = "gccmaster.tblemployees";
        protected $companyTable = "gcchris.tblcompanies";
        protected $departmentTable = "gcchris.tbldepartments";
        protected $positionTable = "gcchris.tblposition";
        protected $employeeDependentsTable = "gcchris.tbldependents";
        protected $employeeEducationTable = "gcchris.tbleducations";
        protected $employeeLicensureTable = "gcchris.tbllicenses";
        protected $employeeDriverLicenseTable = "gcchris.tbldriverlicense";
        protected $employeeWorkExperienceTable = "gcchris.tblworkxps";
        protected $employeeAwardsTable = "gcchris.tblawards";
        protected $employeeSkillsTable = "gcchris.tblskills";
        protected $employeeOrganizationTable = "gcchris.tblorganizations";
        protected $employeeTrainingsTable = "gcchris.tbltrainings";
        protected $employeeReferencesTable = "gcchris.tblreferences";
        protected $employeeMedicalHistoryTable = "gcchris.tblmedrecs";
        protected $employeeLegalHistoryTable = "gcchris.tbllegalrecs";
        protected $employeeOffensesTable = "gcchris.tbloffcoms";
        protected $employeeSalaryTable = "gcchris.tblsalaries";
        protected $employeeCashAdvanceTable = "gcceforms.cash_advance";
        protected $employeeDocumentsTable = "gcchris.tbldocuments";
        protected $employeePerformanceTable = "gcchris.tblperfomance_eval_docs";
        protected $tblArchivedItems = "gccmaster.archived_items";
        protected $tblProbiCalendar = "gcchris.tblprobicalendar";
        protected $tblEmployeesCompanyHistory = "gccmaster.tblemployees_company_history";
        protected $payrollTypeTable = "payroll.payroll_type";
        protected $tblReturnToWork = "gcceforms.return_to_work";
        protected $tblPersonnelLocation = "gcctimeutility.personnel_locations";
        protected $tblPersonnel = "gcctimeutility.personnel";
        protected $tblAppLocationSites = "gcctimeutility.app_location_sites";
        protected $tblAllowances = "gcchris.allowances";
        protected $locationTable = "gcctimeutility.location";
        protected $licenseTable = "gcchris.tbl_license";
        protected $loggedUser;
        protected $loggedUserName;
        protected $tblChecklist = 'gcchris.tblchecklist_documents';
        protected $offCom = 'gcchris.offenses_commendation_history';
        protected $tblUsers = 'gccmaster.tblusers';
        
        protected $defaultStationTable = "gcchris.default_station_location";

        protected $questions = array(
            array("q" => "HAVE YOU EVER BEEN EMPLOYED BY US BEFORE? IN WHAT BRANCH AND WHAT POSITION?", "a" => 1),
            array("q" => "WHO REFERRED YOU TO OUR COMPANY?", "a" => 2),
            array("q" => "NAME OF FRIENDS/RELATIVES EMPLOYED IN THIS COMPANY.", "a" => 3),
            array("q" => "WHERE DID YOU LEARN OF THE VACANCY? ADVERTISING / WALK IN / REFERRAL / SCHOOL PLACEMENT / OTHERS (PLS. SPECIFY)?", "a" => 4),
            array("q" => "DO YOU HAVE ANY CURRENT ILLNESS OR PHYSICAL DEFECTS? IF YES, PLEASE DESCRIBE.", "a" => 5),
            array("q" => "HAVE YOU BEEN HOSPITALIZED FOR THE PAST 12 MONTHS? IF YES, STATE WHAT ILLNESS, DATE OF CONFINEMENT AND NAME OF HOSPITAL.", "a" => 6),
            array("q" => "HAVE YOU BEEN CHARGED OF ANY CRIMINAL, CIVIL, OR ADMINISTRATIVE OFFENSE? IF YES, PLEASE DESCRIBE.", "a" => 7),
            array("q" => "HAVE YOU FILED ANY LABOR CASE AGAINST PREVIOUS EMPLOYERS? IF YES, WHAT TYPE DOLE,NLRC OR OTHER, PLEASE DESCRIBE.", "a" => 8),
            array("q" => "WERE YOU INVOLVED OR HAVE PREVIOUSLY PARTICIPATED IN ANY LABOR STRIKE? IF YES, PLEASE DESCRIBE.", "a" => 9)
        );

        protected $applicationTable = "gcchris.tbapplication";
        protected $now = null;

        function __construct() {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->load->model("core/upload_model", "file_upload");
            $this->load->model("ams/Utilities_model", "utilities");
            $this->load->model("gcctime/Timesheet_model", "ts_model");

            $this->now = new DateTime(null, new DateTimeZone('Asia/Manila'));
            $this->load->library('image_lib');
            
            
            $this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->loggedinData["username"];

        }

        private function addEventLog($status, $notification, $logTable, $tableId, $type = "user"){
            $coreLogs = $this->core_layout->coreLogs();
            $coreLogs->setLogTable($logTable);
            $coreLogs->setLogFieldId($tableId);
            $coreLogs->logNotification($notification, $status, "hris", $type);
        }

        function getDropdownSelectData() {
            $resultset = array();
            $this->db->select("id, description as text");
            $companies = $this->db->get($this->companyTable);
            $this->db->reset_query();

            $this->db->select("id, description as text");
            $departments = $this->db->get($this->departmentTable);
            $this->db->reset_query();

            $this->db->select("id, name as text");
            $positions = $this->db->get($this->positionTable);
            $this->db->reset_query();

            $this->db->select("code as id, name as text");
            $this->db->where("status", 1);
            $payroll_type = $this->db->get($this->payrollTypeTable);
            $this->db->reset_query();

            $this->db->select("site_name as id, site_name as text");
            $station = $this->db->get($this->tblAppLocationSites);
            $this->db->reset_query();

            $this->db->select("id, site_name as text");
            $default_station = $this->db->get($this->tblAppLocationSites);
            $this->db->reset_query();

            $this->db->select("
            id,
            CASE 
                WHEN LENGTH(middlename) > 1 THEN CONCAT(firstname, ' ', SUBSTRING(middlename, 1, 1), '. ', lastname)
                ELSE CONCAT(firstname, ' ', middlename, ' ', lastname)
            END AS text
            ");
            $this->db->group_start();
            $this->db->where_in('lower(level)',['supervisory', 'department head', 'executive', 'managerial']);
            $this->db->where("employee_status",'Active');
            $this->db->group_end();
            $supervisory = $this->db->get($this->employeeTable);
            $this->db->reset_query();
            
            $resultset["dropdown_company"] = ($companies->num_rows() > 0) ? $companies->result() : array();
            $resultset["dropdown_department"] = ($departments->num_rows() > 0) ? $departments->result() : array();
            $resultset["dropdown_position"] = ($positions->num_rows() > 0) ? $positions->result() : array();
            $resultset["dropdown_payroll_type"] = ($payroll_type->num_rows() > 0) ? $payroll_type->result() : array();
            $resultset["dropdown_station"] = ($station->num_rows() > 0) ? $station->result() : array();
            $resultset["dropdown_supervisory"] = ($supervisory->num_rows() > 0) ? $supervisory->result() : array();
            $resultset["dropdown_default_station"] = ($default_station->num_rows() > 0) ? $default_station->result() : array();

            return $resultset;
        }

        // function getEmployeeDatatableRequest($employee_status) {
        //     $resultSet = array();
        //     $post = $this->input->post();
        //     $sex = isset($post['emp_sex']) ? $post["emp_sex"] : 'All';

        //     $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
        //     $dir = "DESC";
        //     $order = "id";

        //     if ($orderx) {
        //         $colIndex = $orderx[0]["column"];
        //         $dir = $orderx[0]["dir"];
        //         $order = $post["columns"][$colIndex]["data"];
        //     }

        //     $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
        //     $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        //     $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
        //     $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";

        //     $searchArray = array(
        //         "field" => "CONCAT(IFNULL(emp.lastname, ''),
        //                            IFNULL(emp.firstname, ''),
        //                            IFNULL(emp.middlename, ''),
        //                            IFNULL(CONCAT(emp.firstname, ' ', emp.lastname), ''),
        //                            IFNULL(CONCAT(emp.lastname, ' ', emp.firstname), ''),
        //                            IFNULL(emp.work_status, ''),
        //                            IFNULL(emp.employee_status, ''),
        //                            IFNULL(IF(companies.id IS NULL, emp.company_id, companies.code), ''),
        //                            IFNULL(IF(departments.id IS NULL, emp.department_id, departments.description), ''),
        //                            IFNULL(IF(positions.id IS NULL, emp.position, positions.name), ''),
        //                            IFNULL(educ.educ_degree, ''))",
        //         "key" => $searchValue,
        //         "option" => "BOTH"
        //     );

        //     $joinArray = array(
        //         array("table" => "gcchris.tblcompanies companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
        //         array("table" => "gcchris.tbldepartments departments", "condition" => "departments.id = emp.department_id", "option" => "LEFT"),
        //         array("table" => "gcchris.tblposition positions", "condition" => "positions.id = emp.position", "option" => "LEFT"),
        //         array("table" => "gcchris.tbleducations educ", "condition" => "educ.emp_id = emp.id", "option" => "LEFT"),
        //     );

        //     foreach ($joinArray as $join) {
        //         $this->db->join($join["table"], $join["condition"], $join["option"]);
        //     }

        //     $where = "emp.is_archived = 0 AND emp.hris_hidden = 0";

        //     if ($employee_status !== "All" && !empty($employee_status)) {
        //         $where .= " AND emp.employee_status = '$employee_status'";
        //     } else {
        //         $where .= " AND employee_status IS NOT NULL";
        //     }

        //     if ($sex !== "All") {
        //         $where .= " AND emp.gender = '$sex'";
        //     }

        //     $this->db->where($where);
        //     $this->db->group_start();
        //     $this->db->like($searchArray["field"], $searchArray["key"], $searchArray["option"]);
        //     $this->db->group_end();

        //     if (intval($limit) >= 1) {
        //         $this->db->limit($limit, $start);
        //     }

        //     $this->db->select("emp.id, emp.idno, emp.lastname, emp.firstname,
        //                        emp.middlename, emp.suffix, emp.pic_filename,
        //                        IF(companies.id IS NULL, emp.company_id, companies.code) company,
        //                        IF(departments.id IS NULL, emp.department_id, departments.description) department,
        //                        IF(positions.id IS NULL, emp.position, positions.name) position,
        //                        emp.is_incomplete, emp.work_status, emp.date_start");
        //     $this->db->order_by($order, $dir);
        //     $this->db->group_by("emp.id");
        //     $query = $this->db->get("gccmaster.tblemployees emp");
        //     $employees = $query->result();
        //     $sql = $this->db->last_query();

        //     $data = array();
        //     foreach ($employees as $pst) {
        //         $tempPost = (array)$pst;
        //         $fullname = $this->core_layout->getDisplayName($tempPost);
        //         $tempFullname = (object)$fullname;

        //         $name = "<div class='custom_content'>";
        //         $name .= "<p class='custom-fullname'>
        //                              <a href='" . base_url('hris/masterfile/view_employee_masterfile/' . $pst->id) . "' target='_blank'>
        //                                 {$tempFullname->display_name_1}
        //                              </a>
        //                           </p>";
        //         $name .= "<small><p>{$pst->position}</p>";
        //         $name .= "<p>{$pst->department}</p>";
        //         $name .= "<p><strong>{$pst->company}</strong></p></small>";
        //         $name .= "</div>";

        //         $currentImage = base_url("assets/images/profile/no_image.jpg");
        //         $imageFile = $pst->pic_filename;
        //         $imagePath = "uploads/files/images/employee_files/empcode_" . $pst->id . "/" . $imageFile;
        //         $image = null;

        //         $this->db->select("prating.*, scale.description");
        //         $this->db->join("gcchris.performance_rating_scale scale", "prating.rating = scale.value", "INNER");
        //         $prating = $this->db->get_where("gcchris.tblperformance_rating prating", array("emp_id" => $pst->id, "current" => 1))->row();
        //         $nestedData["sql"] = $this->db->last_query();
        //         $this->db->reset_query();
        //         $rating_value = !empty($prating) ? $prating->rating : 0;
        //         $rating_description = !empty($prating) ? $prating->description : "";

        //         if (file_exists(realpath($imagePath))) {
        //             $image = base_url($imagePath);
        //         } else {
        //             $image = $currentImage;
        //         }

        //         $nestedData['id'] = $pst->id;
        //         $nestedData['image'] = $image;
        //         $nestedData['name'] = $name;
        //         $nestedData['is_incomplete'] = $pst->is_incomplete;
        //         $nestedData['status_201'] = ($pst->is_incomplete == 0) ? strtoupper("Complete") : strtoupper("Incomplete");
        //         $nestedData['work_status'] = ($pst->work_status) ? strtoupper($pst->work_status) : "---";
        //         $nestedData['date_start'] = date("M d, Y", strtotime($pst->date_start));
        //         $nestedData['img'] = "";
        //         $nestedData['rating'] = "<div class='emp-performance-rating' id='rating-" . $pst->id . "' data-value='" . $rating_value . "' data-emp='" . $pst->id . "'>
        //                                          </div><div class='mt-2 m--regular-font-size-sm1 m--font-boldest text-muted'>$rating_description</div>";
        //         $data[] = $nestedData;
        //     }

        //     $recordsTotalFiltered = $this->utilities->getTableCount("gccmaster.tblemployees emp", $where, $searchArray, $joinArray, false, null, "emp.id");
        //     $resultSet["data"] = $data;
        //     $resultSet["draw"] = $draw;
        //     $resultSet["recordsFiltered"] = $recordsTotalFiltered;
        //     $resultSet["recordsTotal"] = $recordsTotalFiltered;
        //     if($searchArray['key'] != "" && $searchArray['key']):
        //         $this->core_layout->setEventLog("User ".$this->loggedInUsername." has searched ".$searchArray['key']." in employee masterfile datatable.", "search", "success", "gcchris", "user");
        //     endif;

        //     switch($employee_status){
        //         case "Awol":
        //             $this->core_layout->setEventLog("filtered Awol list in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //         case "Active":
        //             $this->core_layout->setEventLog("filtered Active list in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //         case "Black Listed":
        //             $this->core_layout->setEventLog("filtered Black Listed list in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //         case "End of Contract":
        //             $this->core_layout->setEventLog("filtered End of Contractd list in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //         case "Inactive":
        //             $this->core_layout->setEventLog("filtered Inactive list in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //         case "Resign":
        //             $this->core_layout->setEventLog("filtered Resign in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //         case "Retired":
        //             $this->core_layout->setEventLog("filtered Retired in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //         case "Terminated":
        //             $this->core_layout->setEventLog("filtered Terminated in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //         default:
        //             $this->core_layout->setEventLog("filtered All in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //     }

        //     switch($sex){
        //         case "Male":
        //             $this->core_layout->setEventLog("filtered All Male gender in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //         case "Female":
        //             $this->core_layout->setEventLog("filtered All Female gender in employee masterfile datatable.","search", "success", "gcchris", "user");
        //         break;
        //         default:
        //             $this->core_layout->setEventLog("filtered All gender in employee masterfile datatable..","search", "success", "gcchris", "user");
        //         break;
        //     }

        //     return $resultSet;
        // }
        // new function get user session ID and Display Head Department Employees
        //for HR Display all Employees
        function getEmployeeDatatableRequest($employee_status) {
            $resultSet = array();

            $emp_id = $this->loggedinData["emp_id"];
            $this->db->select('dept.*');
            $this->db->where('head_id', $emp_id);
            $query = $this->db->get('gcchris.tbldepartments dept');
            $res = $query->result();
            $dep_id = array();
            foreach($res as $row) {
                $dep_id[] = $row->id;
            }

            $post = $this->input->post();
            $sex = isset($post['emp_sex']) ? $post["emp_sex"] : 'All';

            $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
            $dir = "DESC";
            $order = "id";
            
            if ($orderx) {
                $colIndex = $orderx[0]["column"];                           
                $dir = $orderx[0]["dir"];
                $order = $post["columns"][$colIndex]["data"];
            }
            $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
            $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
            $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
            $searchFlue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";

            if($searchValue){ $searchValue = trim($searchValue); }
            
            $searchArray = array(
                "field" => "CONCAT(IFNULL(emp.lastname, ''),
                                   IFNULL(emp.firstname, ''),
                                   IFNULL(emp.middlename, ''),
                                   IFNULL(CONCAT(emp.firstname, ' ', emp.lastname), ''),
                                   IFNULL(CONCAT(emp.lastname, ' ', emp.firstname), ''),
                                   IFNULL(emp.work_status, ''),
                                   IFNULL(emp.employee_status, ''),
                                   IFNULL(IF(companies.id IS NULL, emp.company_id, companies.code), ''),
                                   IFNULL(IF(departments.id IS NULL, emp.department_id, departments.description), ''),
                                   IFNULL(IF(positions.id IS NULL, emp.position, positions.name), ''),
                                   IFNULL(educ.educ_degree, ''))",
                "key" => $searchValue,
                "option" => "BOTH"
            );
            $joinArray = array(
                array("table" => "gcchris.tblcompanies companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
                array("table" => "gcchris.tbldepartments departments", "condition" => "departments.id = emp.department_id", "option" => "LEFT"),
                array("table" => "gcchris.tblposition positions", "condition" => "positions.id = emp.position", "option" => "LEFT"),
                array("table" => "gcchris.tbleducations educ", "condition" => "educ.emp_id = emp.id", "option" => "LEFT"),
            );
            foreach ($joinArray as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $where = "emp.is_archived = 0 AND emp.hris_hidden = 0";

            if ($employee_status !== "All" && !empty($employee_status)) {
                $where .= " AND emp.employee_status = '$employee_status'";
            } else {
                $where .= " AND employee_status IS NOT NULL";
            }

            if ($sex !== "All") {
                $where .= " AND emp.gender = '$sex'";
            }
            $this->db->where($where);
            $this->db->group_start();
            $this->db->like($searchArray["field"], $searchArray["key"], $searchArray["option"]);
            $this->db->group_end();

            if (intval($limit) >= 1) {
                $this->db->limit($limit, $start);
            }
            $this->db->select("emp.id, emp.idno, emp.lastname, emp.firstname,
                               emp.middlename, emp.suffix, emp.pic_filename,
                               IF(companies.id IS NULL, emp.company_id, companies.code) company,
                               IF(departments.id IS NULL, emp.department_id, departments.description) department,
                               IF(positions.id IS NULL, emp.position, positions.name) position,
                               emp.is_incomplete, emp.work_status, emp.date_start,
                               emp.gender, emp.email, emp.curr_addr as address, emp.mobile_no");
                               
            //HR

            if(!in_array(8, $dep_id) && $dep_id){
            $this->db->where_in('emp.department_id', $dep_id);
            }
            $this->db->order_by($order, $dir);
            $this->db->group_by("emp.id");
            $query = $this->db->get("gccmaster.tblemployees emp");
            $employees = $query->result();
            $sql = $this->db->last_query();

            $data = array();
            foreach ($employees as $pst) {
                $tempPost = (array)$pst;
                $fullname = $this->core_layout->getDisplayName($tempPost);
                $tempFullname = (object)$fullname;

                $name = "<div class='custom_content'>";
                $name .= "<p class='custom-fullname'>
                                     <a href='" . base_url('hris/masterfile/view_employee_masterfile/' . $pst->id) . "' target='_blank'>
                                        {$tempFullname->display_name_1}
                                     </a>
                                  </p>";
                $name .= "<small><p>{$pst->position}</p>";
                $name .= "<p>{$pst->department}</p>";
                $name .= "<p><strong>{$pst->company}</strong></p></small>";
                $name .= "</div>";

                $currentImage = base_url("assets/images/profile/no_image.jpg");
                $imageFile = $pst->pic_filename;
                $imagePath = "uploads/files/images/employee_files/empcode_" . $pst->id . "/" . $imageFile;
                $image = null;

                $this->db->select("prating.*, scale.description");
                $this->db->join("gcchris.performance_rating_scale scale", "prating.rating = scale.value", "INNER");
                $prating = $this->db->get_where("gcchris.tblperformance_rating prating", array("emp_id" => $pst->id, "current" => 1))->row();
                $nestedData["sql"] = $this->db->last_query();
                $this->db->reset_query();
                $rating_value = !empty($prating) ? $prating->rating : 0;
                $rating_description = !empty($prating) ? $prating->description : "";

                if (file_exists(realpath($imagePath))) {
                    $image = base_url($imagePath);
                } else {
                    $image = $currentImage;
                }
                $nestedData['id'] = $pst->id;
                $nestedData['image'] = $image;
                $nestedData['name'] = $name;
                $nestedData['is_incomplete'] = $pst->is_incomplete;
                $nestedData['status_201'] = ($pst->is_incomplete == 0) ? strtoupper("Complete") : strtoupper("Incomplete");
                $nestedData['work_status'] = ($pst->work_status) ? strtoupper($pst->work_status) : "---";
                $nestedData['date_start'] = (isset($pst->date_start) && $pst->date_start) ? date("M d, Y", strtotime($pst->date_start)) : '---';
                $nestedData['img'] = "";
                $nestedData['gender'] = $pst->gender;
                $nestedData['email'] = $pst->email == NULL && $pst->email == '' ? 'NONE' : $pst->email;
                $nestedData['contact'] = $pst->mobile_no == NULL && $pst->mobile_no == '' ? 'NONE' : $pst->mobile_no;
                $nestedData['address'] = $pst->address;
                $nestedData['rating'] = "<div class='emp-performance-rating' id='rating-" . $pst->id . "' data-value='" . $rating_value . "' data-emp='" . $pst->id . "'>
                                                 </div><div class='mt-2 m--regular-font-size-sm1 m--font-boldest text-muted'>$rating_description</div>";
                $data[] = $nestedData;
            }
            // $recordsTotalFiltered = $this->utilities->getTableCount("gccmaster.tblemployees emp", $where, $searchArray, $joinArray, false, null, "emp.id");
            $recordsTotalFiltered = $this->getEmployeeCount($employee_status);
            $resultSet["data"] = $data;
            $resultSet["draw"] = $draw;
            $resultSet["recordsFiltered"] = $recordsTotalFiltered;
            $resultSet["recordsTotal"] = $recordsTotalFiltered;

            /*** remove consumes database records
            if($searchArray['key'] != "" && $searchArray['key']):
                $this->core_layout->setEventLog("User ".$this->loggedInUsername." has searched ".$searchArray['key']." in employee masterfile datatable.", "search", "success", "gcchris", "user");
            endif;
            switch($employee_status){
                case "Awol":
                    $this->core_layout->setEventLog("filtered Awol list in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
                case "Active":
                    $this->core_layout->setEventLog("filtered Active list in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
                case "Black Listed":
                    $this->core_layout->setEventLog("filtered Black Listed list in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
                case "End of Contract":
                    $this->core_layout->setEventLog("filtered End of Contractd list in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
                case "Inactive":
                    $this->core_layout->setEventLog("filtered Inactive list in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
                case "Resign":
                    $this->core_layout->setEventLog("filtered Resign in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
                case "Retired":
                    $this->core_layout->setEventLog("filtered Retired in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
                case "Terminated":
                    $this->core_layout->setEventLog("filtered Terminated in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
                default:
                    $this->core_layout->setEventLog("filtered All in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
            }
            switch($sex){
                case "Male":
                    $this->core_layout->setEventLog("filtered All Male gender in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
                case "Female":
                    $this->core_layout->setEventLog("filtered All Female gender in employee masterfile datatable.","search", "success", "gcchris", "user");
                break;
                default:
                    $this->core_layout->setEventLog("filtered All gender in employee masterfile datatable..","search", "success", "gcchris", "user");
                break;
            } 
            remove consumes database records ***/

            return $resultSet;
        }
        // dupli



        function getEmployeeCount($employee_status) {
            $resultSet = array();

            $emp_id = $this->loggedinData["emp_id"];
            $this->db->select('dept.*');
            $this->db->where('head_id', $emp_id);
            $query = $this->db->get('gcchris.tbldepartments dept');
            $res = $query->result();
            $dep_id = array();
            foreach($res as $row) {
                $dep_id[] = $row->id;
            }

            $post = $this->input->post();
            $sex = isset($post['emp_sex']) ? $post["emp_sex"] : 'All';

            $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
            $dir = "DESC";
            $order = "id";
            
            if ($orderx) {
                $colIndex = $orderx[0]["column"];                           
                $dir = $orderx[0]["dir"];
                $order = $post["columns"][$colIndex]["data"];
            }
            $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
            $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
            $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
            $searchFlue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";

            $searchArray = array(
                "field" => "CONCAT(IFNULL(emp.lastname, ''),
                                   IFNULL(emp.firstname, ''),
                                   IFNULL(emp.middlename, ''),
                                   IFNULL(CONCAT(emp.firstname, ' ', emp.lastname), ''),
                                   IFNULL(CONCAT(emp.lastname, ' ', emp.firstname), ''),
                                   IFNULL(emp.work_status, ''),
                                   IFNULL(emp.employee_status, ''),
                                   IFNULL(IF(companies.id IS NULL, emp.company_id, companies.code), ''),
                                   IFNULL(IF(departments.id IS NULL, emp.department_id, departments.description), ''),
                                   IFNULL(IF(positions.id IS NULL, emp.position, positions.name), ''),
                                   IFNULL(educ.educ_degree, ''))",
                "key" => $searchValue,
                "option" => "BOTH"
            );
            $joinArray = array(
                array("table" => "gcchris.tblcompanies companies", "condition" => "companies.id = emp.company_id", "option" => "LEFT"),
                array("table" => "gcchris.tbldepartments departments", "condition" => "departments.id = emp.department_id", "option" => "LEFT"),
                array("table" => "gcchris.tblposition positions", "condition" => "positions.id = emp.position", "option" => "LEFT"),
                array("table" => "gcchris.tbleducations educ", "condition" => "educ.emp_id = emp.id", "option" => "LEFT"),
            );
            foreach ($joinArray as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }

            $where = "emp.is_archived = 0 AND emp.hris_hidden = 0";

            if ($employee_status !== "All" && !empty($employee_status)) {
                $where .= " AND emp.employee_status = '$employee_status'";
            } else {
                $where .= " AND employee_status IS NOT NULL";
            }

            if ($sex !== "All") {
                $where .= " AND emp.gender = '$sex'";
            }
            $this->db->where($where);
            $this->db->group_start();
            $this->db->like($searchArray["field"], $searchArray["key"], $searchArray["option"]);
            $this->db->group_end();

            $this->db->select("emp.id, emp.idno, emp.lastname, emp.firstname,
                               emp.middlename, emp.suffix, emp.pic_filename,
                               IF(companies.id IS NULL, emp.company_id, companies.code) company,
                               IF(departments.id IS NULL, emp.department_id, departments.description) department,
                               IF(positions.id IS NULL, emp.position, positions.name) position,
                               emp.is_incomplete, emp.work_status, emp.date_start");
                               
            //HR

            if(!in_array(8, $dep_id) && $dep_id){
            $this->db->where_in('emp.department_id', $dep_id);
            }
            $this->db->order_by($order, $dir);
            $this->db->group_by("emp.id");
            $query = $this->db->get("gccmaster.tblemployees emp");
            $employees = $query->result();
            $sql = $this->db->last_query();

            return $query->num_rows();
        }



        function getEmployeeDatatableRequest_v1($employee_status) {
            $post = $this->input->post();
            $sex = $post["emp_sex"];
            if ($post) {
                $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
                $columns = array("id", "lastname",
                    "is_incomplete", "work_status", "idno",
                    "firstname", "middlename", "suffix", "company_id",
                    "department_id", "position", "pic_filename", "CONCAT(firstname, ' ', lastname)");
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
                $dtTemp = $this->dt_model->dataTable();
                $dtTemp->setTable($this->employeeTable);
                $dtTemp->setParameterFields($columns);

                $parameters = array();
                $parameters["is_archived"] = 0;
                $parameters["hris_hidden"] = 0;

                if ($employee_status !== "All") {
                    $parameters["employee_status"] = $employee_status;
                }

                if ($sex !== "All") {
                    $parameters["gender"] = $sex;
                }

                $dtTemp->setWhereParameters($parameters);

                $totalData = $dtTemp->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTemp->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $dtTemp->setLike("CONCAT(firstname, ' ', lastname)", $searchValue, "both");
                    $posts = $dtTemp->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTemp->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $tempPost = (array)$pst;
                        $fullname = $this->core_layout->getDisplayName($tempPost);
                        $tempFullname = (object)$fullname;

                        $_emp = $this->core_layout->getEmployee($pst->id);

                        $name = "<div class='custom_content'>";
                        $name .= "<p class='custom-fullname'>
                                     <a href='" . base_url('hris/masterfile/view_employee_masterfile/' . $pst->id) . "' target='_blank'>
                                        {$tempFullname->display_name_1}
                                     </a>
                                  </p>";
                        $name .= "<small><p>{$_emp->position}</p>";
                        $name .= "<p>{$_emp->department_id}</p>";
                        $name .= "<p><strong>{$_emp->company_id}</strong></p></small>";
                        $name .= "</div>";

                        $currentImage = base_url("assets/images/profile/no_image.jpg");
                        $imageFile = $pst->pic_filename;
                        $imagePath = "uploads/files/images/employee_files/empcode_" . $pst->id . "/" . $imageFile;
                        $image = null;

                        $this->db->select("prating.*, scale.description");
                        $this->db->join("gcchris.performance_rating_scale scale", "prating.rating = scale.value", "INNER");
                        $prating = $this->db->get_where("gcchris.tblperformance_rating prating", array("emp_id" => $pst->id, "current" => 1))->row();
                        $nestedData["sql"] = $this->db->last_query();
                        $this->db->reset_query();
                        $rating_value = !empty($prating) ? $prating->rating : 0;
                        $rating_description = !empty($prating) ? $prating->description : "";

                        if (file_exists(realpath($imagePath))) {
                            $image = base_url($imagePath);
                        } else {
                            $image = $currentImage;
                        }

                        $nestedData['id'] = $pst->id;
                        $nestedData['image'] = $image;
                        $nestedData['name'] = $name;
                        $nestedData['is_incomplete'] = $pst->is_incomplete;
                        $nestedData['status_201'] = ($pst->is_incomplete == 0) ? strtoupper("Complete") : strtoupper("Incomplete");
                        $nestedData['work_status'] = ($pst->work_status) ? strtoupper($pst->work_status) : "---";
                        $nestedData['img'] = "";
                        $nestedData['rating'] = "<div class='emp-performance-rating' id='rating-" . $pst->id . "' data-value='" . $rating_value . "' data-emp='" . $pst->id . "'>
                                                 </div><div class='mt-2 m--regular-font-size-sm1 m--font-boldest text-muted'>$rating_description</div>";
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                    "a" => $posts
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
        }

        function getDependentsList() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("dep_name", "dep_age", "dep_birthdate", "dep_relation", "id", "emp_id");
                $dir = "ASC";
                $order = "dep_birthdate";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeDependentsTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $currentDate = date_create(date("Y-m-d"));
                        $nextDate = date_create(date("Y-m-d", strtotime($pst->dep_birthdate)));
                        $intervalDate = date_diff($currentDate, $nextDate);

                        $tempAge = 0;
                        if($pst->dep_birthdate == "0000-00-00"){
                            $tempAge = "---";
                        }else{
                            if ($intervalDate->y == 0 && $intervalDate->m == 0) {
                                $tempAge = $intervalDate->d;
                                $tempAge = $tempAge > 1 ? $tempAge . " Days Old" : " Day Old";
                            } else if ($intervalDate->y == 0) {
                                $tempAge = $intervalDate->m;
                                $tempAge = $tempAge > 1 ? $tempAge . " Months Old" : " Month Old";
                            } else {
                                $tempAge = $intervalDate->y;
                                $tempAge = $tempAge > 1 ? $tempAge . " Years Old" : " Year Old";
                            }
                        }
                        $dep_birthdate = $pst->dep_birthdate;
                        if (!empty($dep_birthdate)) {
                            if($dep_birthdate == "0000-00-00"){
                                $dep_birthdate = "---";
                            }else{
                                $dep_birthdate = new DateTime($dep_birthdate);
                                $dep_birthdate = $dep_birthdate->format("M d, Y");
                            }
                        } else {
                            echo "None";
                        }

                        $nestedData['id'] = $pst->id;
                        $nestedData['dep_name'] = "$pst->dep_name";
                        $nestedData['dep_age'] = $tempAge;
                        $nestedData['dep_relation'] = $pst->dep_relation;
                        $nestedData['dep_birthdate'] = $dep_birthdate;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEducationalBackgroundList() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("educ_level_type", "educ_school", "educ_degree", "educ_units", "educ_honors", "educ_from", "educ_to", "id", "emp_id");
                $dir = "DESC";
                $order = "educ_to";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeEducationTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['educ_level_type'] = $pst->educ_level_type;
                        $nestedData['educ_school'] = $pst->educ_school;
                        $nestedData['educ_degree'] = $pst->educ_degree;
                        $nestedData['educ_units'] = $pst->educ_units;
                        $nestedData['educ_honors'] = $pst->educ_honors;
                        $nestedData['educ_from'] = $pst->educ_from;
                        $nestedData['educ_to'] = $pst->educ_to;
                        $data[] = $nestedData;
                    }
                }
               return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeLicensure() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("license_type", "exam_place", "rating", "release_date", "exam_date", "license_no", "expiration_date", "license_id", "id", "emp_id");
                $dir = "release_date";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeLicensureTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                $expiration_date = "";
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['license_type'] = $pst->license_id == 0 ? $pst->license_type : $this->getLicense($pst->license_id)->description;
                        $nestedData['exam_place'] = $pst->exam_place;
                        $nestedData['rating'] = $pst->rating ;
                        $nestedData['release_date'] = $pst->release_date;
                        $nestedData['exam_date'] = $pst->exam_date ? $pst->exam_date : "";
                        $nestedData['license_no'] = $pst->license_no ;

                        if($pst->expiration_date && $pst->expiration_date != '0000-00-00'){
                            if(date("Y-m-d") >= $pst->expiration_date){
                                $expiration_date = "<span class='m-badge m-badge--danger m-badge--wide'>$pst->expiration_date</span>";
                            }else{
                                $expiration_date = "<span class='m-badge m-badge--success m-badge--wide'>$pst->expiration_date</span>";
                            }
                        }else{
                            $expiration_date = "No Expiry";
                        }

                        $nestedData['expiration_date'] = $expiration_date;
                        $nestedData['type'] = $pst->license_id == 0 ? null : $this->getLicense($pst->license_id)->type;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeDriverLicense() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("restriction", "license_no", "expiration_date", "id", "emp_id");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeDriverLicenseTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['restriction'] = $pst->restriction;
                        $nestedData['license_no'] = $pst->license_no;
                        if(date("Y-m-d") >= $pst->expiration_date){
                            $expiration_date = "<span class='m-badge m-badge--danger m-badge--wide'>$pst->expiration_date</span>";
                        }else{
                            $expiration_date = "<span class='m-badge m-badge--success m-badge--wide'>$pst->expiration_date</span>";
                        }
                        $nestedData['expiration_date'] = $expiration_date;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeWorkExperience() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("work_company", "work_from", "work_to", "work_position", "work_status", "work_reason", "id", "emp_id", "old_idno");
                $dir = "DESC";
                $order = "work_from";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeWorkExperienceTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        if($pst->old_idno != 0 OR $pst->old_idno != NULL){
                            $old_idno =  $pst->old_idno;
                        }else{
                            $old_idno =  "N/A";
                        }

                        $company = is_numeric($pst->work_company) ?
                            $this->db->get_where($this->companyTable, array("id" => $pst->work_company))->row("description") :
                            $pst->work_company;
                        $nestedData['work_company'] = $company;
                        $nestedData['work_from'] = $pst->work_from;
                        $nestedData['work_to'] = $pst->work_to;
                        $position = is_numeric($pst->work_position) ?
                            $this->db->get_where($this->positionTable, array("id" => $pst->work_position))->row("name") :
                            $pst->work_position;
                        $nestedData['work_position'] = $position;
                        $nestedData['old_idno'] = $old_idno;
                        $nestedData['work_status'] = $pst->work_status;
                        $nestedData['work_reason'] = empty($pst->work_reason) ? "N/A" : $pst->work_reason;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeAwards() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("award", "award_institution", "award_date", "id", "emp_id");
                $dir = "DESC";
                $order = "award_date";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeAwardsTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['award'] = $pst->award;
                        $nestedData['award_institution'] = $pst->award_institution;
                        $nestedData['award_date'] = $pst->award_date;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeOrganization() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("org_institution", "org_membership_title", "org_from", "org_to", "id", "emp_id");
                $dir = "DESC";
                $order = "org_to";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeOrganizationTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['org_institution'] = $pst->org_institution;
                        $nestedData['org_membership_title'] = $pst->org_membership_title;
                        $nestedData['org_from'] = $pst->org_from;
                        $nestedData['org_to'] = $pst->org_to;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeTraining() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("training", "train_from", "train_to", "train_institution", "train_conductor", "train_venue", "attachment", "id", "emp_id");
                $dir = "DESC";
                $order = "train_to";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeTrainingsTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['training'] = $pst->training;
                        $nestedData['train_from'] = $pst->train_from;
                        $nestedData['train_to'] = $pst->train_to;
                        $nestedData['train_institution'] = $pst->train_institution;
                        $nestedData['train_conductor'] = $pst->train_conductor;
                        $nestedData['train_venue'] = $pst->train_venue;
                        $nestedData['attachment'] = $pst->attachment;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeePersonalReference() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("ref_name", "ref_contact_no", "ref_address", "id", "emp_id");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeReferencesTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['ref_name'] = $pst->ref_name;
                        $nestedData['ref_contact_no'] = $pst->ref_contact_no;
                        $nestedData['ref_address'] = $pst->ref_address;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeMedicalHistory() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("med_details", "med_no", "med_date", "med_venue", "med_physician", "med_findings", "remarks", "id", "emp_id");
                $dir = "med_date";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeMedicalHistoryTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['med_details'] = $pst->med_details;
                        $nestedData['med_no'] = $pst->med_no;
                        $nestedData['med_date'] = $pst->med_date;
                        $nestedData['med_venue'] = $pst->med_venue;
                        $nestedData['med_physician'] = $pst->med_physician;
                        $nestedData['med_findings'] = $pst->med_findings;
                        $nestedData['remarks'] = $pst->remarks;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        /*** employment data tab section ***/

        function getEmployeeLegalHistory() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("leg_case_no", "leg_details", "leg_case_date", "leg_court_field", "leg_prosecutor", "leg_status", "id", "emp_id");
                $dir = "DESC";
                $order = "leg_case_date";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeLegalHistoryTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['leg_case_no'] = $pst->leg_case_no;
                        $nestedData['leg_details'] = $pst->leg_details;
                        $nestedData['leg_case_date'] = $pst->leg_case_date;
                        $nestedData['leg_court_field'] = $pst->leg_court_field;
                        $nestedData['leg_prosecutor'] = $pst->leg_prosecutor;
                        $nestedData['leg_status'] = $pst->leg_status;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeOffenses() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("offcom_type", "offcom_date", "offcom_nature", "offcom_action", "id", "emp_id","filename");
                $dir = "DESC";
                $order = "offcom_date";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeOffensesTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['offcom_type'] = $pst->offcom_type;
                        $nestedData['offcom_date'] = $pst->offcom_date;
                        $nestedData['offcom_nature'] = $pst->offcom_nature;
                        $nestedData['offcom_action'] = $pst->offcom_action;
                        $nestedData['filename'] = $pst->filename;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeCashAdvance() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("reference_no", "amt_applied", "purpose", "amt_approved", "created_dt", "status", "id", "employee");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeCashAdvanceTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["employee"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['reference_no'] = $pst->reference_no;
                        $nestedData['amt_applied'] = $pst->amt_applied;
                        $nestedData['purpose'] = $pst->purpose;
                        $nestedData['amt_approved'] = $pst->amt_approved;
                        $nestedData['created_dt'] = $pst->created_dt;
                        $nestedData['status'] = $pst->status;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeDocuments() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("doc_type", "doc_filename", "id", "emp_id");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeDocumentsTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;
                $parameters["doc_type!="] = 'restricted';

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
                        $nestedData['doc_type'] = $pst->doc_type;
                        $nestedData['doc_filename'] = $pst->doc_filename;
                        $data[] = $nestedData;
                    }
                }
                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeBackgroundCheck() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("doc_type", "doc_filename", "id", "emp_id");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeDocumentsTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["doc_type"] = "restricted";
                $parameters["is_archived"] = 0;

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
                        $nestedData['doc_filename'] = $pst->doc_filename;
                        $data[] = $nestedData;
                    }
                }

                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeReturnToWork() {
            $post = $this->input->post();

            if ($post) {
                $columns = array("id", "reference_no", "from_date", "return_type", "approved_by", "reason");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->tblReturnToWork);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["employee_id"] = $post["emp_id"];
                $parameters['status'] = 1;

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
                        $approver = $this->core_layout->getEmployee($pst->approved_by);

                        $nestedData['id'] = $pst->id;
                        $nestedData['reference_no'] = $pst->reference_no;
                        $nestedData['from_date'] = $pst->from_date;
                        if($pst->return_type == 1){
                            $type = "RECALLED";
                        }else if($pst->return_type == 2){
                            $type = "REQUEST TO EXTEND";
                        }else{
                            $type = "ABSENT";
                        }
                        $nestedData['return_type'] = $type;
                        $nestedData['reason'] = $pst->reason;
                        $nestedData['approved_by'] = $approver->firstname." ".$approver->lastname;
                        $data[] = $nestedData;
                    }
                }

                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getCurrentJobDescription($id = null) {
            $resultset = array();
            if ($id) {
                $this->db->select("b.*");
                $this->db->from("{$this->employeeTable} a");
                $this->db->join("{$this->positionTable} b", "b.id = a.position", "LEFT");
                $this->db->where("a.id", $id);
                $this->db->where("b.job_desc !=", null);
                $query = $this->db->get();

                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $resultset["response"] = true;
                    $resultset["position_id"] = $row->id;
                    $resultset["position_description"] = $row->name;
                    $resultset["data"] = $row->job_desc;
                } else {
                    $resultset["response"] = false;
                }
            }

            return $resultset;
        }

        function getEmployeePerformance() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("quarter", "range", "year", "filename", "id", "emp_id");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeePerformanceTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

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
                        $nestedData['quarter'] = $pst->quarter;
                        $nestedData['range'] = $pst->range;
                        $nestedData['year'] = $pst->year;
                        $nestedData['filename'] = $pst->filename;
                        $data[] = $nestedData;
                    }
                }

                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );

            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        /*** employment data tab section ***/

        function getEmployeeData($id = null) {
            $data = array();
            if ($id) {
                /** jp01 updated query starts here **/
                
                /*** modified sqlSelect location name 
                 * GROUP_CONCAT(DISTINCT(`c`.`location_name`)) as location_name
                 * ***/

                $sqlSelect = "a.*,
                concat(trim(a.latitude), ',', trim(a.longitude)) as map_coordinates,
                GROUP_CONCAT(DISTINCT(`c`.`location_name`) ORDER BY `c`.`created_at`, `c`.`id` ASC SEPARATOR '|') as location_name,
                IFNULL(comp.id, IFNULL(a.company_id, 0)) as company_id,
                IFNULL(comp.code, IFNULL(a.company_id, 'No assigned company')) as comp_description,
                IFNULL(dept.id, IFNULL(a.department_id, 0)) as department_id,
                IFNULL(dept.code, IFNULL(a.department_id, 'No assigned department')) as dept_description,
                IFNULL(pos.id, IFNULL(a.position, 0)) as position, IFNULL(pos.name,
                IFNULL(a.position, 'No assigned position')) as pos_description, 
                IFNULL(ds.station_id, 0) as default_station,
                ISNULL(ds.id) as has_default_station,
                IFNULL(UPPER(als.site_name), 'NO DEFAULT STATION') as default_station_description";
                /** jp01 updated query ends here **/
                $this->db->select($sqlSelect);
                $this->db->from("{$this->employeeTable} as a");
                $this->db->join('gcctimeutility.personnel as b', 'b.biometricno = a.biometricno', 'left');
                $this->db->join('gcctimeutility.personnel_locations as c', 'c.personnel_id = b.id', 'left');
                /** jp01 updated query starts here **/
                $this->db->join($this->companyTable." as comp", "comp.id = a.company_id OR comp.code = a.company_id OR comp.description = a.company_id", "left");
                $this->db->join($this->departmentTable." as dept", "dept.id = a.department_id OR dept.code = a.department_id OR dept.description = a.department_id", "left");
                $this->db->join($this->positionTable." as pos", "pos.id = a.position OR pos.name = a.position", "left");
                $this->db->join($this->defaultStationTable." as ds", "ds.employee_id = a.id", "left");
                $this->db->join($this->tblAppLocationSites." as als", "als.id = ds.station_id", "left");
                /** jp01 updated query ends here **/
                $this->db->where("a.id", $id);
                $query = $this->db->get();

                if ($query->num_rows() == 1) {
                    $data = $query->row();
                    $currentImage = base_url("assets/images/profile/no_image.jpg");
                    $imageFile = $data->pic_filename;
                    $imagePath = realpath("uploads/files/images/employee_files/empcode_{$id}/{$imageFile}");
                    if (file_exists($imagePath) && $imageFile) {
                        $currentImage = base_url("uploads/files/images/employee_files/empcode_{$id}/{$imageFile}");
                    }

                    $data->pic_filename = $currentImage;
                    /** jp01 updated query starts here **/
                    /*** $displayName = $this->core_layout->getUserData($data->id); ***/
                    $displayName = $this->core_layout->getEmployeeData($data->id);
                    /** jp01 updated query starts here **/
                    $data->display_name = $displayName["display_name_1"];

                    $displayEmail = strtoupper(trim($data->email));
                    $data->display_email = ($displayEmail !== "NONE" && $displayEmail !== "N/A" && $displayEmail !== "") ? $displayEmail : "";

                    /** jp01 updated query starts here **/
                    /*** $company = $this->db->where("id", $data->company_id)->get("gcchris.tblcompanies")->row("code");
                    $data->company = empty($company) ? $data->company_id : $company;

                    $position = $this->db->where("id", $data->position)->get("gcchris.tblposition")->row("name");
                    $data->_position = empty($position) ? $data->position : $position;

                    $department = $this->db->where("id", $data->department_id)->get("gcchris.tbldepartments")->row("code");
                    $data->department = empty($department) ? $data->department_id : $department; ***/
                    if($data->employee_status == 'Active'){
                        $data->date_end = '0000-00-00';
                    }else{
                        $data->date_end = $data->date_end;
                    }
                    

                    $data->company = (isset($data->comp_description) && $data->comp_description)? $data->comp_description: "No assigned company";
                    $data->department = (isset($data->dept_description) && $data->dept_description)? $data->dept_description: "No assigned department";
                    $data->_position = (isset($data->pos_description) && $data->pos_description)? $data->pos_description: "No assigned position";
                    /** jp01 updated query starts here **/

                    $data->_status = $data->work_status;
                    $data->current_supervisor = $data->supervisor;
                    $data->current_company_id = $data->company_id;
                    $data->current_department_id = $data->department_id;
                    $data->current_position_id = $data->position;
                    $data->basic_rate = number_format($data->basic_rate, 2, ".", "");

                    /*** modified to array data ***/
                    $data->location_name = explode("|", $data->location_name);
                    /*** modified to array data ***/

                    $data->added_by =  (is_numeric($data->add_by)) ? $this->core_layout->getEmployeeData($data->add_by)['display_name_1'] : $data->add_by;
                }
            }
            return $data;
        }

        function uploadEmployeeAvatar() {
            $resultset = array();
            $post = $this->input->post();

            if (isset($post["employee_id"]) && $post["employee_id"]) {
                $imagesPath = "./uploads/files/images/employee_files/empcode_{$post["employee_id"]}";

                $createFilePath = false;

                if (!file_exists($imagesPath)) {
                    $mkdir = mkdir($imagesPath, 0777, true);
                    if ($mkdir) {
                        $createFilePath = true;
                    }
                } else {
                    $createFilePath = true;
                }

                if ($createFilePath == false) {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                    $resultset["toastr_state"] = "warning";
                    $this->core_layout->setEventLog("Failed to create directory folder for the uploaded file!","file upload", "error", "gcchris", "system");
                } else {
                    $config = array();
                    $config['upload_path'] = $imagesPath;
                    $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG';
                    $config['max_size'] = 10000;
                    $config['create_thumbnail'] = true;

                    $data = $this->file_upload->uploadFile($config);
                    if ($data["response"] == true) {
                        $files = $data["files"][0];
                        $filename = $files["file_name"];

                        $dirpath = "./uploads/files/images/employee_files/empcode_" . $post["employee_id"];
                        $thumbnailpath = "./uploads/files/images/employee_files/empcode_" . $post["employee_id"] . "/thumbnails";

                        if (!file_exists(realpath($thumbnailpath))) {
                            mkdir($thumbnailpath, 0777, true);
                        }

                        if ($filename) {
                            $this->saveThumbnail($dirpath . "/" . $filename, $thumbnailpath . "/" . $filename);
                            $update = $this->db->update($this->employeeTable, array("pic_filename" => $filename), array("id" => $post["employee_id"]));
                            if ($update) {
                                $resultset["response"] = true;
                                $resultset["added_image"] = base_url("uploads/files/images/employee_files/empcode_{$post["employee_id"]}/{$filename}");
                                $resultset["render_image"] = "empcode_{$post["employee_id"]}/" . $filename;
                                $resultset["toastr_msg"] = "Upload image successful.";
                                $resultset["toastr_state"] = "success";
                                $this->core_layout->setEventLog("Employee Avatar - Upload image successful.","file upload", "success", "gcchris", "user");
                            } else {
                                $resultset["response"] = false;
                                $resultset["toastr_msg"] = "Error updating employee profile image!";
                                $resultset["toastr_state"] = "error";
                                $this->core_layout->setEventLog("Employee Avatar - Error updating employee profile image.","file upload", "error", "gcchris", "system");
                            }
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Image upload to specific path failed!";
                            $resultset["toastr_state"] = "error";
                            $this->core_layout->setEventLog("Employee Avatar - Image upload to specific path failed.","file upload", "error", "gcchris", "system");
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = $data["message"];
                        $resultset["toastr_state"] = "error";
                        $this->core_layout->setEventLog("Employee Avatar - ".$data["message"],"file upload", "error", "gcchris", "system");
                    }
                }

            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Employee data not found!";
                $resultset["toastr_state"] = "error";
                $this->core_layout->setEventLog("Employee Avatar - Employee data not found.","file upload", "error", "gcchris", "system");
            }

            return $resultset;
        }

        function tempUploadEmployeeAvatar() {
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();

            if (isset($session["emp_id"]) && $session["emp_id"]) {
                $imagesPath = "./uploads/files/images/employee_files/temp_{$session["emp_id"]}";

                $createFilePath = false;

                if (!file_exists($imagesPath)) {
                    $mkdir = mkdir($imagesPath, 0777, true);
                    if ($mkdir) {
                        $createFilePath = true;
                    }
                } else {
                    $createFilePath = true;
                }

                if ($createFilePath === false) {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                    $resultset["toastr_state"] = "warning";
                } else {
                    $config = array();
                    $config['upload_path'] = $imagesPath;
                    $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG';
                    $config['max_size'] = 10000;
                    $config['create_thumbnail'] = false;

                    $data = $this->file_upload->uploadFile($config);
                    if ($data["response"]) {
                        $files = $data["files"][0];
                        $filename = $files["file_name"];
                        if ($filename) {
                            $resultset["response"] = true;

                            $resultset["added_image"] = base_url("uploads/files/images/employee_files/temp_{$session["emp_id"]}/{$filename}");
                            $resultset["temp_image"] = "{$filename}";

                            $resultset["toastr_msg"] = "Upload image successful.";
                            $resultset["toastr_state"] = "success";
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Image upload to specific path failed!";
                            $resultset["toastr_state"] = "error";
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Image upload failed!";
                        $resultset["toastr_state"] = "error";
                    }
                }

            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Employee data not found!";
                $resultset["toastr_state"] = "error";
            }

            return $resultset;
        }

        function uploadEmployeeTraining() {
            $resultset = array();
            $post = $this->input->post();

            if (isset($post["employee_id"]) && $post["employee_id"]) {
                $imagesPath = "./uploads/files/documents/employee_files/empcode_{$post["employee_id"]}/trainings";

                $createFilePath = false;

                if (!file_exists($imagesPath)) {
                    $mkdir = mkdir($imagesPath, 0777, true);
                    if ($mkdir) {
                        $createFilePath = true;
                    }
                } else {
                    $createFilePath = true;
                }

                if ($createFilePath === false) {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                    $resultset["toastr_state"] = "warning";
                    $this->core_layout->setEventLog("Employee Training - Failed to create directory folder for the uploaded file.","file upload", "error", "gcchris", "system");
                } else {
                    $config = array();
                    $config['upload_path'] = $imagesPath;
                    $config['allowed_types'] = 'jpg|jpeg|png|pdf|PNG|JPG|JPEG|PDF';
                    $config['max_size'] = 100000;
                    $config['create_thumbnail'] = false;

                    $data = $this->file_upload->uploadFile($config);
                    if ($data["response"] === true) {
                        $files = $data["files"][0];
                        $filename = $files["file_name"];
                        if ($filename) {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "File upload successful.";
                            $resultset["toastr_state"] = "success";
                            $resultset["filename"] = $filename;
                            $this->core_layout->setEventLog("Employee Training - File upload successful.","file upload", "success", "gcchris", "user");
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "File upload to specific path failed!";
                            $resultset["toastr_state"] = "error";
                            $this->core_layout->setEventLog("Employee Training - File upload to specific path failed.","file upload", "error", "gcchris", "system");
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload failed!";
                        $resultset["toastr_state"] = "error";
                        $this->core_layout->setEventLog("Employee Training - File upload failed.","file upload", "error", "gcchris", "system");
                    }
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Employee data not found!";
                $resultset["toastr_state"] = "error";
                $this->core_layout->setEventLog("Employee Training - Employee data not found.","file upload", "error", "gcchris", "system");
            }

            return $resultset;
        }

        function uploadEmployeeMedicalHistory() {
            $resultset = array();
            $post = $this->input->post();

            if (isset($post["employee_id"]) && $post["employee_id"]) {
                $imagesPath = "./uploads/files/documents/employee_files/empcode_{$post["employee_id"]}/medical";

                $createFilePath = false;

                if (!file_exists($imagesPath)) {
                    $mkdir = mkdir($imagesPath, 0777, true);
                    if ($mkdir) {
                        $createFilePath = true;
                    }
                } else {
                    $createFilePath = true;
                }

                if ($createFilePath) {

                    $config = array();
                    $config['upload_path'] = $imagesPath;
                    $config['allowed_types'] = 'jpg|jpeg|png|pdf|PNG|JPG|JPEG|PDF';
                    $config['max_size'] = 100000;
                    $config['create_thumbnail'] = false;

                    $data = $this->file_upload->uploadFile($config);
                    if ($data["response"]) {
                        $files = $data["files"][0];
                        $filename = $files["file_name"];
                        if ($filename) {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "File upload successful.";
                            $resultset["toastr_state"] = "success";
                            $resultset["filename"] = $filename;
                            $this->core_layout->setEventLog("Medical History - File upload successful.","file upload", "success", "gcchris", "user");
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "File upload to specific path failed!";
                            $resultset["toastr_state"] = "error";
                            $this->core_layout->setEventLog("Medical History - File upload to specific path failed.","file upload", "error", "gcchris", "system");
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload failed!";
                        $resultset["toastr_state"] = "error";
                        $this->core_layout->setEventLog("Medical History - File upload failed.","file upload", "error", "gcchris", "system");
                    }

                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                    $resultset["toastr_state"] = "warning";
                    $this->core_layout->setEventLog("Medical History - Failed to create directory folder for the uploaded file.","file upload", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Employee data not found!";
                $resultset["toastr_state"] = "error";
                $this->core_layout->setEventLog("Medical History - Employee data not found.","file upload", "error", "gcchris", "system");
            }

            return $resultset;
        }

        function uploadEmployeeOffenses() {
            $resultset = array();
            $post = $this->input->post();

            if (isset($post["employee_id"]) && $post["employee_id"]) {
                $imagesPath = "./uploads/files/documents/employee_files/empcode_{$post["employee_id"]}/offenses_commendation";

                $createFilePath = false;

                if (!file_exists($imagesPath)) {
                    $mkdir = mkdir($imagesPath, 0777, true);
                    if ($mkdir) {
                        $createFilePath = true;
                    }
                } else {
                    $createFilePath = true;
                }

                if ($createFilePath == false) {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                    $resultset["toastr_state"] = "warning";
                    $this->core_layout->setEventLog("Employee Offense - Failed to create directory folder for the uploaded file.","file upload", "error", "gcchris", "system");
                } else {
                    $config = array();
                    $config['upload_path'] = $imagesPath;
                    $config['allowed_types'] = 'jpg|jpeg|png|pdf|PNG|JPG|JPEG|PDF';
                    $config['max_size'] = 100000;
                    $config['create_thumbnail'] = false;

                    $data = $this->file_upload->uploadFile($config);
                    if ($data["response"]) {
                        $files = $data["files"][0];
                        $filename = $files["file_name"];
                        if ($filename) {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "File upload successful.";
                            $resultset["toastr_state"] = "success";
                            $resultset["filename"] = $filename;
                            $this->core_layout->setEventLog("Employee Offense - File upload successful.","file upload", "success", "gcchris", "user");
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "File upload to specific path failed!";
                            $resultset["toastr_state"] = "error";
                            $this->core_layout->setEventLog("Employee Offense - File upload to specific path failed.","file upload", "error", "gcchris", "system");
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload failed!";
                        $resultset["toastr_state"] = "error";
                        $this->core_layout->setEventLog("Employee Offense - File upload failed.","file upload", "error", "gcchris", "system");
                    }
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Employee data not found!";
                $resultset["toastr_state"] = "error";
                $this->core_layout->setEventLog("Employee Offense - Employee data not found", "error", "gcchris", "system");
            }

            return $resultset;
        }

        function uploadEmployeeDocuments() {
            $resultset = array();
            $post = $this->input->post();

            if (isset($post["emp_id"]) && $post["emp_id"]) {
                $imagesPath = "./uploads/files/documents/employee_files/empcode_{$post["emp_id"]}/documents";

                $createFilePath = false;

                if (!file_exists($imagesPath)) {
                    $mkdir = mkdir($imagesPath, 0777, true);
                    if ($mkdir) {
                        $createFilePath = true;
                    }
                } else {
                    $createFilePath = true;
                }

                if ($createFilePath) {
                    $config = array();
                    $config['upload_path'] = $imagesPath;
                    $config['allowed_types'] = 'jpg|jpeg|png|pdf|docx|PNG|JPG|JPEG|PDF|DOCX';
                    $config['max_size'] = 100000;
                    $config['create_thumbnail'] = false;

                    $data = $this->file_upload->uploadFile($config);
                    if ($data["response"]) {
                        $files = $data["files"][0];
                        $filename = $files["file_name"];
                        if ($filename) {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "File upload successful.";
                            $resultset["toastr_state"] = "success";
                            $resultset["filename"] = $filename;
                            $this->core_layout->setEventLog("Documents - File upload successful.","file upload", "success", "gcchris", "user");
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "File upload to specific path failed!";
                            $resultset["toastr_state"] = "error";
                            $this->core_layout->setEventLog("Documents - File upload to specific path failed.","file upload", "error", "gcchris", "system");
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload failed!";
                        $resultset["toastr_state"] = "error";
                        $this->core_layout->setEventLog("Documents - File upload failed.","file upload", "error", "gcchris", "system");
                    }

                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                    $resultset["toastr_state"] = "warning";
                    $this->core_layout->setEventLog("Documents - Failed to create directory folder for the uploaded file.","file upload", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Employee data not found!";
                $resultset["toastr_state"] = "error";
                $this->core_layout->setEventLog("Documents - Employee data not found.","file upload", "error", "gcchris", "system");
            }

            return $resultset;
        }

        function uploadEmployeePerformance() {
            $resultset = array();
            $post = $this->input->post();

            if (isset($post["employee_id"]) && $post["employee_id"]) {
                $imagesPath = "./uploads/files/documents/employee_files/empcode_{$post["employee_id"]}/performance_eval";

                $createFilePath = false;

                if (!file_exists($imagesPath)) {
                    $mkdir = mkdir($imagesPath, 0777, true);
                    if ($mkdir) {
                        $createFilePath = true;
                    }
                } else {
                    $createFilePath = true;
                }

                if ($createFilePath === false) {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                    $resultset["toastr_state"] = "warning";
                    $this->core_layout->setEventLog("Performance - Failed to create directory folder for the uploaded file.","file upload", "error", "gcchris", "system");
                } else {
                    $config = array();
                    $config['upload_path'] = $imagesPath;
                    $config['allowed_types'] = 'jpg|jpeg|png|pdf|docx|PNG|JPG|JPEG|PDF|DOCX';
                    $config['max_size'] = 100000;
                    $config['create_thumbnail'] = false;

                    $data = $this->file_upload->uploadFile($config);
                    if ($data["response"] === true) {
                        $files = $data["files"][0];
                        $filename = $files["file_name"];
                        if ($filename) {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "File upload successful.";
                            $resultset["toastr_state"] = "success";
                            $resultset["filename"] = $filename;
                            $this->core_layout->setEventLog("Performance - File upload successful.","file upload", "success", "gcchris", "user");
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "File upload to specific path failed!";
                            $resultset["toastr_state"] = "error";
                            $this->core_layout->setEventLog("Performance - File upload to specific path failed.","file upload", "error", "gcchris", "system");
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload failed!";
                        $resultset["toastr_state"] = "error";
                        $this->core_layout->setEventLog("Performance - File upload failed.","file upload", "error", "gcchris", "system");
                    }
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Employee data not found!";
                $resultset["toastr_state"] = "error";
                $this->core_layout->setEventLog("Performance - Employee data not found.","file upload", "error", "gcchris", "system");
            }

            return $resultset;
        }

        function addEmployeePersonalInformation() {
            $post = $this->input->post();
            $resultset = array();
            $session = $this->core_layout->getCurrentSession();
            $require_clearance = $post['require_clearance'];
            if($require_clearance == 1){
                $clearance_type = $post['clearance_type'];
                $file_path = $post['path'];
                $doc_name = $post['document_names'];
                $tempDocs = explode(",", $doc_name);
            }
            unset($post['require_clearance']);
            if (isset($post) && $post) {
                $isHiring = (isset($post["is_hiring"]) && $post["is_hiring"] == "true") ? true : false;
                $applicationId = (isset($post["application_id"]) && $post["application_id"]) ? $post["application_id"] : 0;

                $longlat = (isset($post["long_lat_coordinates"]) && $post["long_lat_coordinates"]) ? $post["long_lat_coordinates"] : false;
                if ($isHiring === true && $applicationId !== 0) {
                    unset($post["csrf_token"], $post["is_hiring"], $post["application_id"], $post["long_lat_coordinates"]);
                } else {
                    unset($post["csrf_token"], $post["long_lat_coordinates"]);
                }

                if ($longlat) {
                    $tempCoords = explode(",", $longlat);
                    if (isset($tempCoords[0], $tempCoords[1]) && (is_numeric($tempCoords[0]) || is_numeric($tempCoords[1]))) {
                        if (count($tempCoords) == 2) {
                            $post["latitude"] = trim($tempCoords[0]);
                            $post["longitude"] = trim($tempCoords[1]);
                        } else {
                            $post["latitude"] = trim($tempCoords[0]);
                        }
                    }
                }

                $post["bday"] = date("Y-m-d", strtotime($post["bday"]));
                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $session["emp_id"];
                $post["employee_status"] = "Active";

                $check_existing_array = array(
                    "firstname"=>$post['firstname'],
                    "middlename"=>$post['middlename'],
                    "lastname"=>$post['lastname'],
                    "bday"=>$post['bday']
                );

                $body_id = 0;
                if(isset($post['body_id']) && $post['body_id']){
                    $body_id = $post['body_id'];
                }
                unset($post['body_id']);
                
                //check if employee already exists
                $check_existing = $this->db->get_where($this->employeeTable,$check_existing_array)->num_rows();
                if($check_existing > 0){
                    $resultset["response"] = false;
                    $resultset['toastr_msg'] = "Employee already exists in the system.";
                }else{
                    if($require_clearance == 1){
                        if(isset($tempDocs) && count($tempDocs) > 1){
                            unset($post['clearance_type'], $post['document_names'], $post['path'], $post['filename'], $post['require_clearance']);
                            array_map('strtoupper', $post);
                            $added = $this->db->insert($this->employeeTable, $post);
    
                            if ($added) {
                                $tempId = $this->db->insert_id();
                                $pic_filename = $post["pic_filename"];
                                $tempFile = empty($pic_filename) ? "no_image.jpg" : $pic_filename;
                                $moveUploaded = null;
    
                                if (!empty($pic_filename)) {
                                    $tempFileFrom = "./uploads/files/images/employee_files/temp_{$session["emp_id"]}";
                                    $tempDestination = "./uploads/files/images/employee_files/empcode_{$tempId}";
    
                                    $moveUploaded = $this->file_upload->moveUploadedFile($tempFile, $tempFileFrom, $tempDestination, true);
                                } else {
                                    $tempFileFrom = "./assets/images/profile/no_image.jpg";
                                    $tempDestination = "./uploads/files/images/employee_files/empcode_{$tempId}";
    
                                    if (!file_exists(realpath($tempDestination))) {
                                        mkdir($tempDestination, 0777, true);
                                    }
    
                                    if (!file_exists(realpath($tempDestination . "/thumbnails"))) {
                                        mkdir($tempDestination . "/thumbnails", 0777, true);
                                    }
    
                                    copy($tempFileFrom, $tempDestination . "/no_image.jpg");
                                    $this->saveThumbnail($tempDestination . "/no_image.jpg", $tempDestination . "/thumbnails/no_image.jpg");
                                }
    
                                $this->db->update($this->employeeTable, array("pic_filename" => $tempFile), array("id" => $tempId));
    
                                if ($isHiring === true && $applicationId !== 0) {
                                    $this->updatePersonnelRequestRecord($applicationId);
                                }
    
                                $resultset["response"] = true;
                                $resultset["id"] = $tempId;
                                $resultset["moveUploaded"] = $moveUploaded; 
    
    
                                if($doc_name){
                                    foreach($tempDocs as $emp_docs){
                                        $insertArr = array(
                                            "emp_id" => $tempId,
                                            "doc_type" => "Clearances",
                                            "doc_filename" => $emp_docs,
                                            "add_by" => $session["emp_id"]
                                        );
                                        $this->db->insert("gcchris.tbldocuments", $insertArr);
                    
                                        if ($doc_name) {
                                            $tempFileFromDocs = "./uploads/files/documents/employee_files/employee_temp/documents/user_temp_upload_{$session["emp_id"]}";
                                            $tempDestinationDocs = "./uploads/files/documents/employee_files/empcode_{$tempId}/documents";
                    
                                            if (!file_exists(realpath($tempDestinationDocs))) {
                                                mkdir($tempDestinationDocs, 0777, true);
                                            }
                                            $moveUploaded = $this->file_upload->moveUploadedFile($emp_docs, $tempFileFromDocs, $tempDestinationDocs, true);
                                        }
                                    }
                                }

                                // update crs attached employee id to document_body table
                                if($body_id != 0){
                                    $q = $this->db->update('dbhrd.document_body', array('employee_id' => $tempId), array('id' => $body_id));

                                    if($q){
                                        $rs = $this->db->get_where('dbhrd.applicant_informations', array('document_body_id' => $body_id))->row();

                                        $_data = array(
                                            'tin_no' => $rs->tin_no,
                                            'tax_status' => $rs->tax_status,
                                            'phealth_no' => $rs->phealth_no,
                                            'pagibig_no' => $rs->pagibig_no,
                                            'sss_no' => $rs->sss_no,
                                            'fat_deceased' => $rs->fat_deceased,
                                            'fat_name' => $rs->fat_name,
                                            'fat_addr' => $rs->fat_addr,
                                            'fat_company' => $rs->fat_company,
                                            'fat_occupation' => $rs->fat_occupation,
                                            'fat_contact' => $rs->fat_contact,
                                            'mot_deceased' => $rs->mot_deceased,
                                            'mot_name' => $rs->mot_name,
                                            'mot_addr' => $rs->mot_addr,
                                            'mot_company' => $rs->mot_company,
                                            'mot_occupation' => $rs->mot_occupation,
                                            'mot_contact' => $rs->mot_contact,
                                        );

                                        if($rs->partner_type == 1){
                                            $_data['spo_deceased'] = $rs->partners_deceased;
                                            $_data['spo_name'] = $rs->partners_name;
                                            $_data['spo_addr'] = $rs->partners_addr;
                                            $_data['spo_company'] = $rs->partners_company;
                                            $_data['spo_occupation'] = $rs->partners_occupation;
                                            $_data['spo_contact'] = $rs->partners_contact;
                                        }
                                        
                                        if($rs->partner_type == 2){
                                            $_data['partner_deceased'] = $rs->partners_deceased;
                                            $_data['partner_name'] = $rs->partners_name;
                                            $_data['partner_addr'] = $rs->partners_addr;
                                            $_data['partner_company'] = $rs->partners_company;
                                            $_data['partner_occupation'] = $rs->partners_occupation;
                                            $_data['partner_contact'] = $rs->partners_contact;
                                        }

                                        $_data['partner_type'] = $rs->partner_type;
                                        $_data['emer_name'] = $rs->emer_name;
                                        $_data['emer_contact'] = $rs->emer_contact;
                                        $_data['emer_addr'] = $rs->emer_addr;

                                        $this->db->where('id', $tempId);
                                        $this->db->update($this->employeeTable, $_data);

                                        $dependents = $this->db->get_where('dbhrd.tbldependents', array('applicant_id' => $rs->id))->result();
                                        if(!empty($dependents) && $dependents){
                                            foreach($dependents as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tbldependents', $row);
                                            }
                                        }

                                        $education = $this->db->get_where('dbhrd.tbleducation', array('applicant_id' => $rs->id))->result();
                                        if(!empty($education) && $education){
                                            foreach($education as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tbleducations', $row);
                                            }
                                        }

                                        $license = $this->db->get_where('dbhrd.tbllicenses', array('applicant_id' => $rs->id))->result();
                                        if(!empty($license) && $license){
                                            foreach($license as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tbllicenses', $row);
                                            }
                                        }

                                        $driver = $this->db->get_where('dbhrd.tbldriverlicense', array('applicant_id' => $rs->id))->result();
                                        if(!empty($driver) && $driver){
                                            foreach($driver as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tbldriverlicense', $row);
                                            }
                                        }

                                        $work = $this->db->get_where('dbhrd.tblworkexperience', array('applicant_id' => $rs->id))->result();
                                        if(!empty($work) && $work){
                                            foreach($work as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tblworkxps', $row);
                                            }
                                        }

                                        $award = $this->db->get_where('dbhrd.tblawards', array('applicant_id' => $rs->id))->result();
                                        if(!empty($award) && $award){
                                            foreach($award as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tblawards', $row);
                                            }
                                        }

                                        $award = $this->db->get_where('dbhrd.tblawards', array('applicant_id' => $rs->id))->result();
                                        if(!empty($award) && $award){
                                            foreach($award as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tblawards', $row);
                                            }
                                        }

                                        $org = $this->db->get_where('dbhrd.tblorganizations', array('applicant_id' => $rs->id))->result();
                                        if(!empty($org) && $org){
                                            foreach($org as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tblorganizations', $row);
                                            }
                                        }

                                        $training = $this->db->get_where('dbhrd.tbltrainings', array('applicant_id' => $rs->id))->result();
                                        if(!empty($training) && $training){
                                            foreach($training as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tbltrainings', $row);
                                            }
                                        }

                                        $reference = $this->db->get_where('dbhrd.tblreferences', array('applicant_id' => $rs->id))->result();
                                        if(!empty($reference) && $reference){
                                            foreach($reference as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tblreferences', $row);
                                            }
                                        }

                                        $med = $this->db->get_where('dbhrd.tblmedrecs', array('applicant_id' => $rs->id))->result();
                                        if(!empty($med) && $med){
                                            foreach($med as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tblmedrecs', $row);
                                            }
                                        }

                                        $skill = $this->db->get_where('dbhrd.tblskills', array('applicant_id' => $rs->id))->result();
                                        if(!empty($skill) && $skill){
                                            foreach($skill as $row){
                                                unset($row->id);
                                                unset($row->applicant_id);
                                                $row->emp_id = $tempId;
                                                $row->add_date = date('Y-m-d H:i:s');
                                                $row->add_by = $session["emp_id"];

                                                $this->db->insert('gcchris.tblskills', $row);
                                            }
                                        }
                                    }
                                }
                                // update crs attached employee id to document_body table
    
                                $this->core_layout->setEventLog("Updated personal information.","update", "success", "gcchris", "user");
                            } else {
                                $resultset["response"] = false;
                                $this->core_layout->setEventLog("Error updating personal information.","update", "error", "gcchris", "system");
                            }
                        }else{
                            $resultset["response"] = false;
                            $resultset['toastr_msg'] = "This information is important! Please select at least two .pdf file to upload";
                        }
                    }else{
                        $added = $this->db->insert($this->employeeTable, $post);
                        if ($added) {
                            $tempId = $this->db->insert_id();
                            $pic_filename = $post["pic_filename"];
                            $tempFile = empty($pic_filename) ? "no_image.jpg" : $pic_filename;
                            $moveUploaded = null;

                            if (!empty($pic_filename)) {
                                $tempFileFrom = "./uploads/files/images/employee_files/temp_{$session["emp_id"]}";
                                $tempDestination = "./uploads/files/images/employee_files/empcode_{$tempId}";

                                $moveUploaded = $this->file_upload->moveUploadedFile($tempFile, $tempFileFrom, $tempDestination, true);
                            } else {
                                $tempFileFrom = "./assets/images/profile/no_image.jpg";
                                $tempDestination = "./uploads/files/images/employee_files/empcode_{$tempId}";

                                if (!file_exists(realpath($tempDestination))) {
                                    mkdir($tempDestination, 0777, true);
                                }

                                if (!file_exists(realpath($tempDestination . "/thumbnails"))) {
                                    mkdir($tempDestination . "/thumbnails", 0777, true);
                                }

                                copy($tempFileFrom, $tempDestination . "/no_image.jpg");
                                $this->saveThumbnail($tempDestination . "/no_image.jpg", $tempDestination . "/thumbnails/no_image.jpg");
                            }

                            $this->db->update($this->employeeTable, array("pic_filename" => $tempFile), array("id" => $tempId));

                            if ($isHiring === true && $applicationId !== 0) {
                                $this->updatePersonnelRequestRecord($applicationId);
                            }

                            // update crs attached employee id to document_body table
                            if($body_id != 0){
                                $q = $this->db->update('dbhrd.document_body', array('employee_id' => $tempId), array('id' => $body_id));

                                if($q){
                                    $rs = $this->db->get_where('dbhrd.applicant_informations', array('document_body_id' => $body_id))->row();
                                    if ($rs){
                                    $_data = array(
                                        'tin_no' => $rs->tin_no,
                                        'tax_status' => $rs->tax_status,
                                        'phealth_no' => $rs->phealth_no,
                                        'pagibig_no' => $rs->pagibig_no,
                                        'sss_no' => $rs->sss_no,
                                        'fat_deceased' => $rs->fat_deceased,
                                        'fat_name' => $rs->fat_name,
                                        'fat_addr' => $rs->fat_addr,
                                        'fat_company' => $rs->fat_company,
                                        'fat_occupation' => $rs->fat_occupation,
                                        'fat_contact' => $rs->fat_contact,
                                        'mot_deceased' => $rs->mot_deceased,
                                        'mot_name' => $rs->mot_name,
                                        'mot_addr' => $rs->mot_addr,
                                        'mot_company' => $rs->mot_company,
                                        'mot_occupation' => $rs->mot_occupation,
                                        'mot_contact' => $rs->mot_contact,
                                    );

                                    if($rs->partner_type == 1){
                                        $_data['spo_deceased'] = $rs->partners_deceased;
                                        $_data['spo_name'] = $rs->partners_name;
                                        $_data['spo_addr'] = $rs->partners_addr;
                                        $_data['spo_company'] = $rs->partners_company;
                                        $_data['spo_occupation'] = $rs->partners_occupation;
                                        $_data['spo_contact'] = $rs->partners_contact;
                                    }
                                    
                                    if($rs->partner_type == 2){
                                        $_data['partner_deceased'] = $rs->partners_deceased;
                                        $_data['partner_name'] = $rs->partners_name;
                                        $_data['partner_addr'] = $rs->partners_addr;
                                        $_data['partner_company'] = $rs->partners_company;
                                        $_data['partner_occupation'] = $rs->partners_occupation;
                                        $_data['partner_contact'] = $rs->partners_contact;
                                    }

                                    $_data['partner_type'] = $rs->partner_type;
                                    $_data['emer_name'] = $rs->emer_name;
                                    $_data['emer_contact'] = $rs->emer_contact;
                                    $_data['emer_addr'] = $rs->emer_addr;

                                    $this->db->where('id', $tempId);
                                    $this->db->update($this->employeeTable, $_data);

                                    $dependents = $this->db->get_where('dbhrd.tbldependents', array('applicant_id' => $rs->id))->result();
                                    if(!empty($dependents) && $dependents){
                                        foreach($dependents as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tbldependents', $row);
                                        }
                                    }

                                    $education = $this->db->get_where('dbhrd.tbleducation', array('applicant_id' => $rs->id))->result();
                                    if(!empty($education) && $education){
                                        foreach($education as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tbleducations', $row);
                                        }
                                    }

                                    $license = $this->db->get_where('dbhrd.tbllicenses', array('applicant_id' => $rs->id))->result();
                                    if(!empty($license) && $license){
                                        foreach($license as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tbllicenses', $row);
                                        }
                                    }

                                    $driver = $this->db->get_where('dbhrd.tbldriverlicense', array('applicant_id' => $rs->id))->result();
                                    if(!empty($driver) && $driver){
                                        foreach($driver as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tbldriverlicense', $row);
                                        }
                                    }

                                    $work = $this->db->get_where('dbhrd.tblworkexperience', array('applicant_id' => $rs->id))->result();
                                    if(!empty($work) && $work){
                                        foreach($work as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tblworkxps', $row);
                                        }
                                    }

                                    $award = $this->db->get_where('dbhrd.tblawards', array('applicant_id' => $rs->id))->result();
                                    if(!empty($award) && $award){
                                        foreach($award as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tblawards', $row);
                                        }
                                    }

                                    $award = $this->db->get_where('dbhrd.tblawards', array('applicant_id' => $rs->id))->result();
                                    if(!empty($award) && $award){
                                        foreach($award as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tblawards', $row);
                                        }
                                    }

                                    $org = $this->db->get_where('dbhrd.tblorganizations', array('applicant_id' => $rs->id))->result();
                                    if(!empty($org) && $org){
                                        foreach($org as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tblorganizations', $row);
                                        }
                                    }

                                    $training = $this->db->get_where('dbhrd.tbltrainings', array('applicant_id' => $rs->id))->result();
                                    if(!empty($training) && $training){
                                        foreach($training as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tbltrainings', $row);
                                        }
                                    }

                                    $reference = $this->db->get_where('dbhrd.tblreferences', array('applicant_id' => $rs->id))->result();
                                    if(!empty($reference) && $reference){
                                        foreach($reference as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tblreferences', $row);
                                        }
                                    }

                                    $med = $this->db->get_where('dbhrd.tblmedrecs', array('applicant_id' => $rs->id))->result();
                                    if(!empty($med) && $med){
                                        foreach($med as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tblmedrecs', $row);
                                        }
                                    }

                                    $skill = $this->db->get_where('dbhrd.tblskills', array('applicant_id' => $rs->id))->result();
                                    if(!empty($skill) && $skill){
                                        foreach($skill as $row){
                                            unset($row->id);
                                            unset($row->applicant_id);
                                            $row->emp_id = $tempId;
                                            $row->add_date = date('Y-m-d H:i:s');
                                            $row->add_by = $session["emp_id"];

                                            $this->db->insert('gcchris.tblskills', $row);
                                        }
                                    }
                                }
                                }
                            }
                            // update crs attached employee id to document_body table

                            $resultset["response"] = true;
                            $resultset["id"] = $tempId;
                            $resultset["moveUploaded"] = $moveUploaded; 

                            $this->core_layout->setEventLog("Updated personal information.","update", "success", "gcchris", "user");
                        } else {
                            $resultset["response"] = false;
                            $this->core_layout->setEventLog("Error updating personal information.","update", "error", "gcchris", "system");
                        }
                    }
                }
            } else {
                $resultset["response"] = false;
                $this->core_layout->setEventLog("Error updating personal information.","update", "error", "gcchris", "system");
            }
            return $resultset;
        }

        private function updatePersonnelRequestRecord($id = null) {
            if ($id) {
                $loggedIn = $this->core_layout->getCurrentSession();
                $application = $this->db->get_where($this->applicationTable, array("id" => $id, "status" => "Ongoing"));
                if ($application->num_rows() == 1) {
                    $row = $application->row();
                    $neededCount = intval($row->people_no);
                    $currentCount = intval($row->current);

                    $totalCount = $currentCount + 1;
                    $arrData = array();
                    $arrData["current"] = $totalCount;
                    if ($totalCount >= $neededCount) {
                        $arrData["status"] = "Completed";
                        $arrData["completed_by"] = $loggedIn["emp_id"];
                        $arrData["completed_dt"] = date("Y-m-d H:i:s");
                    }

                    $this->db->update($this->applicationTable, $arrData, array("id" => $id));
                    $this->core_layout->setEventLog("Updated personnel request Record.","update", "success", "gcchris", "user");
                }
            }
        }

        function updateEmployeePersonalInfo() {
            $post = $this->input->post();
            $resultset = array();

            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $employeeId = $post["id"];
                if ($employeeId) {
                    $currentEmployeeData = $this->getEmployeeData($employeeId);
                    $longlat = (isset($post["long_lat_coordinates"]) && $post["long_lat_coordinates"]) ? $post["long_lat_coordinates"] : false;
                    unset($post["id"], $post["long_lat_coordinates"]);
                    $where = array("id" => $employeeId);
                    $post["bday"] = date("Y-m-d", strtotime($post["bday"]));
                    if ($longlat) {
                        $tempCoords = explode(",", $longlat);
                        if (count($tempCoords) == 2) {
                            $post["latitude"] = trim($tempCoords[0]);
                            $post["longitude"] = trim($tempCoords[1]);
                        } else {
                            $post["latitude"] = trim($tempCoords[0]);
                        }
                    }

                    $updated = $this->db->update($this->employeeTable, $post, $where);
                    if ($updated) {
                        $this->checkIf201StatusIsComplete($employeeId);
                        $resultset["response"] = true;
                        $resultset["data"] = $this->getEmployeeData($employeeId);
                        $changes = $this->logChanges($currentEmployeeData, $post);
                        $this->core_layout->setEventLog("Updated personal information of employee ".$resultset['data']->firstname." ".$resultset['data']->lastname." ".$changes,"update", "success", "gcchris", "user");
                    } else {
                        $resultset["response"] = false;
                        $this->core_layout->setEventLog("Error updating personal information of employee ".$resultset['data']->firstname." ".$resultset['data']->lastname,"update", "error", "gcchris", "system");
                    }
                } else {
                    $resultset["response"] = false;
                    $this->core_layout->setEventLog("Error updating personal information of employee ".$resultset['data']->firstname." ".$resultset['data']->lastname,"update", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $this->core_layout->setEventLog("Error updating personal information of employee","update", "error", "gcchris", "system");
            }
            return $resultset;
        }

        function updateEmployeeAdditionalInfo() {
            $post = $this->input->post();
            $resultset = array();

            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $employeeId = $post["id"];
                if ($employeeId) {
                    $currentEmployeeData = $this->getEmployeeData($employeeId);
                    unset($post["id"]);
                    $partnerType = $post["partner_type"];
                    $post["partner_type"] = (int)$post["partner_type"];
                    $where = array("id" => $employeeId);

                    $updated = $this->db->update($this->employeeTable, $post, $where);
                    if ($updated) {
                        $this->checkIf201StatusIsComplete($employeeId);
                        if ($partnerType == "0") {
                            $this->removePartnerDetails($employeeId);
                        }
                        $resultset["response"] = true;
                        $changes = $this->logChanges($currentEmployeeData, $post);
                        $resultset["data"] = $this->getEmployeeData($employeeId);
                        $this->core_layout->setEventLog("Updated personal information of employee ".$resultset['data']->firstname." ".$resultset['data']->lastname." ".$changes,"update", "success", "gcchris", "user");
                    } else {
                        $resultset["response"] = false;
                        $this->core_layout->setEventLog("Error updating additional information of employee ".$resultset['data']->firstname." ".$resultset['data']->lastname,"update", "error", "gcchris", "system");
                    }
                } else {
                    $resultset["response"] = false;
                    $this->core_layout->setEventLog("Error updating additional information of employee ".$resultset['data']->firstname." ".$resultset['data']->lastname,"update", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $this->core_layout->setEventLog("Error updating additional information of employee","update", "error", "gcchris", "system");
            }
            return $resultset;
        }

        function removePartnerDetails($id = null) {
            if ($id) {
                $psdata = array(
                    "spo_deceased" => "", "spo_name" => "", "spo_addr" => "", "spo_company" => "",
                    "spo_occupation" => "", "spo_contact" => "", "partners_deceased" => "",
                    "partners_name" => "", "partners_addr" => "", "partners_company" => "",
                    "partners_occupation" => "", "partners_contact" => "");

                $this->db->update($this->employeeTable, $psdata, array("id" => $id));
                $this->core_layout->setEventLog("Removed employee partner details.","update", "success", "gcchris", "user");
            }
        }

        function updateEmployeeEmploymentData() {
            $post = $this->input->post();
            $resultset = array();
            $user = $this->core_layout->getUserLoggedIn();
            $user_emp_id = $user["employee_id"];
            $current_status = $post["current_status"];
            $current_company_id = $post["current_company_id"];
            $current_supervisor = isset($post["current_supervisor"]) && $post['current_supervisor'] ? $post['current_supervisor'] : 0; //fixed in payroll employee employment data
            $getClassification = $this->getClassification($post['id']);
            $getDateHired = $this->getHiredDate($post['id']);

            $default_station = isset($post["default_station"]) && $post["default_station"] ? $post["default_station"]: null;

            $work_station = isset($post["work_station"]) && $post["work_station"] ? $post["work_station"]: array();
            if (isset($post) && $post) {
                unset($post["csrf_token"], $post["current_status"], $post["current_company_id"], $post["current_department_id"], $post["current_position_id"], $post["work_station"],$post["current_supervisor"], $post["default_station"]);
                $employeeId = $post["id"];
                if ($employeeId) {
                    unset($post["id"]);
                    $where = array("id" => $employeeId);

                    if($post['employee_status'] == 'Active' && $getClassification == 'Inactive'){
                        $post['date_end_prob'] = date('Y-m-d', strtotime("+6 months", strtotime($post['date_start'])));
                    }else{
                        if($getDateHired == '' && !isset($getDateHired)){
                            $post['date_end_prob'] = date('Y-m-d', strtotime("+6 months", strtotime($post['date_start'])));
                        }
                    }

                    $post['resignation_effective_date'] = isset($post['resignation_effective_date']) && $post['resignation_effective_date'] ? $post['resignation_effective_date'] : NULL; //fixed in payroll employee employment data

                    if(isset($post['date_end']) && $post['date_end'] == "0000-00-00" && $post['work_status'] == 'RESIGNED'){
                        $resultset['response'] = false;
                        $resultset['toastr_msg'] = "Incorrect date in SEPARATED";
                        $this->core_layout->setEventLog("Incorrect date in SEPARATED.","update", "error", "gcchris", "user");
                    }else{
                        if(($post['biometricno'] != $this->getBioNum($employeeId)) && $this->checkBiometricNoIfExist($post['biometricno'])){
                            $resultset['response'] = false;
                            $resultset['toastr_msg'] = "Biometric Number is already used.";
                            $updated = false;
                        }else{
                            $post['biometricno'] = trim($post['biometricno']);
                            $updated = $this->db->update($this->employeeTable, $post, $where);
                        }

                        $this->core_layout->setEventLog("Updated employment data.","update", "success", "gcchris", "user");
                        if ($updated) {
                            $this->checkIf201StatusIsComplete($employeeId);
                            if ($current_status !== $post["work_status"]) {
                                $status_data = array("emp_id" => $employeeId, "work_status" => $post["work_status"], "created_by" => $user_emp_id);
                                $this->db->insert("gccmaster.tblemployees_status_history", $status_data);
                            }

                            if(!empty($_SESSION['performance_rating_temp'])){
                                $updatePerformanceRating = $this->db->insert('gcchris.tblperformance_rating', $_SESSION['performance_rating_temp']);
                                if($updatePerformanceRating){
                                    unset($_SESSION['performance_rating_temp']);
                                }
                            }


                            if(!empty($_SESSION['salary_temp_data'])){
                                $updateDataSalary = $this->db->insert($this->employeeSalaryTable, $_SESSION['salary_temp_data']);
                                if($updateDataSalary){
                                    unset($_SESSION['salary_temp_data']);
                                }
                            }
                            if(isset($post["company_id"]) && $current_company_id !== $post["company_id"]){
                                $company_data = array(
                                    "emp_id" => $employeeId,
                                    "company_id" => $post["company_id"],
                                    "created_by" => $user_emp_id,
                                    "date_started" => date("Y-m-d")
                                );
                                $this->db->insert("gccmaster.tblemployees_company_history", $company_data);
                            }
                            $biono = $this->getBioNum($employeeId);
                            $personelId = $this->getPersonelID($biono);

                            if($personelId != 0){
                                if(isset($work_station) && !empty($work_station)){
                                    $this->db->where('personnel_id', $personelId);
                                    $del = $this->db->delete($this->tblPersonnelLocation);
                                    $this->db->reset_query();
                                    if($del){
                                        foreach($work_station as $site){
                                            $site_id = $this->getSiteLocationId($site);
                                            $data = array(
                                                'personnel_id' => $personelId,
                                                'location_name' => $site,
                                                'site_location_id' => $site_id
                                            );
                                            $this->db->where('id', $personelId);
                                            $this->db->insert($this->tblPersonnelLocation, $data);
                                        }
                                    }
                                }else{
                                    $this->db->where('personnel_id', $personelId);
                                    $this->db->delete($this->tblPersonnelLocation);
                                }
                            }

                            if ($post["work_status"] === "REGULAR") {
                                $this->db->reset_query();
                                $this->db->where($where);
                                $this->db->set("date_regular", $_POST['date_regular']);
                                $this->db->update($this->employeeTable);
                            } else {
                                $this->db->reset_query();
                                $this->db->where($where);
                                $this->db->set("date_regular", "0000-00-00");
                                $this->db->update($this->employeeTable);
                            }

                            if($post['employee_status'] == 'Active' && $getClassification == 'Inactive'){
                                $this->db->reset_query();
                                $this->db->where($where);
                                $this->db->set("date_end", '0000-00-00');
                                $this->db->set('resignation_effective_date', NULL);
                                $this->db->update($this->employeeTable);
                            }

                            if($default_station){
                                $this->db->reset_query();

                                $tempLocation = $this->db->get_where($this->tblAppLocationSites, array("id"=>$default_station));
                                    if($tempLocation->num_rows() === 1){                                        
                                        $locRow = $tempLocation->row();
                                        $tempData = $this->core_layout->getEmployeeData($employeeId);

                                        $_dStation = $this->db->get_where($this->defaultStationTable, array("employee_id"=>$employeeId));
                                        if($_dStation->num_rows() === 0){
                                            $rawData = array("employee_id"=>$employeeId, "station_id"=>$locRow->id, "station_description"=>$locRow->site_name, "created_at"=>date("Y-m-d H:i:s"));
                                            $added = $this->db->insert($this->defaultStationTable, $rawData);
                                            if($added){ 
                                                if($tempData){
                                                    $tempData = (object) $tempData;
                                                    $tempName = strtoupper($tempData->display_name_1);
                                                    $logData = "Default station of employee named `{$tempName}` has been set to `{$locRow->site_name}` and was added succefully.";
                                                    $this->core_layout->setEventLog($logData, "insert", "success", "gcchris", "user");
                                                }
                                            }
                                        }else{
                                            $dsRow = $_dStation->row();
                                            $updated = $this->db->update($this->defaultStationTable, 
                                                array("station_id"=>$locRow->id, "station_description"=>$locRow->site_name, "updated_at"=>date("Y-m-d H:i:s")), 
                                                array("id"=>$dsRow->id)); 
                                            if($updated){
                                                if($tempData){
                                                    $tempData = (object) $tempData;
                                                    $tempName = strtoupper($tempData->display_name_1);
                                                    $logData = "Default station of employee named `{$tempName}` has been updated from `{$dsRow->station_description}` to `{$locRow->site_name}` succefully.";
                                                    $this->core_layout->setEventLog($logData, "update", "success", "gcchris", "user");
                                                }
                                            }                                           
                                        }
                                        
                                    }

                                
                            }
                            
                            $resultset["response"] = true;
                            $resultset["data"] = $this->getEmployeeData($employeeId);
                        } else {
                            $resultset["response"] = false;
                            $this->core_layout->setEventLog("Error Updating employment data.","update", "success", "gcchris", "user");
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

        function checkBiometricNoIfExist($biometricno){
            $result = $this->db->get_where($this->employeeTable, array("biometricno" => trim($biometricno), "biometricno !=" => ""));
            return $result->num_rows();
        }

        /*** Modal Section ***/
        function getModalContainerContent($id = null, $modalView = null, $data = array()) {
            $id = $id ? $id : 0;
            $resultset = array();
            $modalContent = "";
            $tempData = array();
            $tempData["id"] = $id;

            if ($data) {
                foreach ($data as $key => $value) {
                    $tempData[$key] = $value;
                }
            }

            if ($modalView) {
                $modalContent = $this->load->view("hris/masterfile/employee/modals/{$modalView}", $tempData, true);
            }
            $resultset["html"] = $modalContent;
            $resultset["data"] = $tempData;
            return $resultset;
        }

        function getModalJobDescription($id = null) {
            $id = $id ? $id : 0;
            $resultset = array();
            $modalContent = "";
            $result = $this->getCurrentJobDescription($id);
            if ($result["response"]) {
                $arrData = array();
                $arrData["id"] = $id;
                $arrData["position_id"] = $result["position_id"];
                $arrData["job_title"] = $result["position_description"];
                $arrData["job_description"] = $result["data"];

                $modalContent = $this->load->view("hris/masterfile/employee/modals/job_description", $arrData, true);
            }

            $resultset["html"] = $modalContent;
            return $resultset;
        }

        function getModalQuestionsDescription($id = null) {
            $id = $id ? $id : 0;
            $resultset = array();
            $modalContent = "";
            $this->db->select("id, ques1, ques2, ques3, ques4, ques5, ques6, ques7, ques8, ques9");
            $query = $this->db->get_where($this->employeeTable, array("id" => $id));
            if ($query->num_rows() == 1) {
                $arrData = $query->row();
                $modalContent = $this->load->view("hris/masterfile/employee/modals/questions", $arrData, true);
            }

            $resultset["html"] = $modalContent;
            return $resultset;
        }

        function setPerformanceData($id = null) {
            $arrData = array();

            if ($id) {
                $query = $this->db->get_where($this->employeeTable, array("id" => $id, "employee_status" => "Active"));
                if ($query->num_rows() == 1) {
                    $row = $query->row();
                    $row->is_regular = false;
                    if ($row->work_status == "REGULAR") {
                        $row->is_regular = true;
                    }
                    if ($row->work_status == "PROBATIONARY") {
                        $row->is_regular = false;
                    }

                    $genData = $this->generateQuarter($row->date_start, $row->is_regular);
                    if (isset($genData["quarter"]) && $genData["quarter"]) {
                        $row->quarter = $genData["quarter"];
                    }
                    if (isset($genData["date_range"]) && $genData["date_range"]) {
                        $row->date_range = $genData["date_range"];
                    }

                    $currentDate = date("Y-m-d");
                    $minDate = date("Y-01-01", strtotime("-1 year"));
                    $maxDate = date("Y-12-31");
                    $endDate = date("Y-03-31");

                    if ($currentDate > $endDate) {
                        $minDate = date("Y-01-01");
                    }

                    $arrdates = array();
                    $arrdates["min_date"] = $minDate;
                    $arrdates["max_date"] = $maxDate;

                    $row->range_dates = $arrdates;

                    $arrData = $row;
                }
            }

            return $arrData;
        }

        private function generateQuarter($dateStarted = null, $isRegular = false) {
            $data = array();
            if ($dateStarted) {
                $qtr = array();
                $range = array();

                if ($isRegular) {
                    $qtr["quarter_1"] = "1st Quarter";
                    $qtr["quarter_2"] = "2nd Quarter";
                    $qtr["quarter_3"] = "3rd Quarter";
                    $qtr["quarter_4"] = "4th Quarter";

                    $range["quarter_1"] = "January - March";
                    $range["quarter_2"] = "April - June";
                    $range["quarter_3"] = "July - September";
                    $range["quarter_4"] = "October - December";

                } else {
                    $_3rdMonth = date('F d, Y', strtotime("+3 months", strtotime($dateStarted)));

                    $fifthEvaluationDate = date('Y-m-d', strtotime("+5 months", strtotime($dateStarted)));
                    $_45Month = date('F d, Y', strtotime("-15 days", strtotime($fifthEvaluationDate)));

                    $dateStarted = date('F d, Y', strtotime($dateStarted));
                    $_3rdMonth = "{$dateStarted} - {$_3rdMonth}";
                    $_45Month = "{$dateStarted} - {$_45Month}";

                    $qtr["month_30"] = "3rd Month";
                    $qtr["month_45"] = "4.5 Month";

                    $range["month_30"] = $_3rdMonth;
                    $range["month_45"] = $_45Month;
                }

                $data["quarter"] = $qtr;
                $data["date_range"] = $range;
            }

            return $data;
        }
        /*** Modal Section ***/

        /*** Insert Function Section ***/
        function setModalDependents() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                unset($post["csrf_token"]);
                $loggedIn = $this->core_layout->getCurrentSession();
                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->employeeDependentsTable, $post);
                $empName = $this->getEmployeeName($post['emp_id']);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee Dependent has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "inserted new dependent of employee ".$empName["name"],"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee dependent!";
                    $this->core_layout->setEventLog("Error inserting new dependent of employee ".$empName['name'],"insert", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Error inserting new dependent of employee","insert", "error", "gcchris", "system");
            }
            return $resultset;
        }

        function setModalEducation() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                unset($post["csrf_token"]);
                $loggedIn = $this->core_layout->getCurrentSession();
                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->employeeEducationTable, $post);
                $empName = $this->getEmployeeName($post['emp_id']);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee educational background has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " inserted ".$post['educ_level_type']." educational background for ".$empName['name'],"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee educational background!";
                    $this->core_layout->setEventLog("Error inserting educational background","insert", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->addEventLog("Error","No post data found inserting new employee educational background data.",$this->employeeEducationTable,0);
                $this->core_layout->setEventLog("Error inserting educational background","insert", "error", "gcchris", "system");
            }
            return $resultset;
        }

        function setModalLicensure() {
            $post = $this->input->post();
            $resultset = array();
            $license_no = false;

            if (strtoupper($post['license_no']) !== 'N/A') {
                $license_no = $this->check_license_no($post['license_no']);
            } else {
                $license_no = false;
            }   

            if ($license_no === false) {
                $cert_name = isset($post['certificate_name']) && $post['certificate_name'] ? $post['certificate_name'] : null;

                if($post['license_type'] === 'Certificate') {
                    $check_cert_exist = $this->check_cert_exist($cert_name);
                } else {
                    $check_cert_exist = false;
                }

                if ($check_cert_exist === false) {
                    if ($post) {
                        unset($post["csrf_token"]);
                        $loggedIn = $this->core_layout->getCurrentSession();
                        $post["add_date"] = date("Y-m-d H:i:s");
                        $post["add_by"] = $loggedIn["emp_id"];
                        $type = $post['license_type'];
                        /**
                         * --> START
                         * 
                         * Gin amo ko ni ky nag ubra ko static option sa "Add licences and Certifications" nga modal
                         * which is "Certificate"
                         * Amo ni ang original code before ko gin modifies
                         * 
                         * --> Start ==================================
                         * 
                         * $license = explode('-', $post['license_type']);
                         * $post['license_id'] = $license[0];
                         * $post['license_type'] = $license[1];
                         * 
                         * --> End ====================================
                         */
        
                        if($post['license_type'] !== 'Certificate') {
                            $license = explode('-', $post['license_type']);
                            $post['license_id'] = $license[0];
                            $post['license_type'] = $license[1];
                        } else {
                            $post['license_id'] = 0;
                            $post['license_type'] = $post['license_type'];
                        }
                        // END
        
                        $post = array_map('strtoupper', $post);
                        $saved = $this->db->insert($this->employeeLicensureTable, $post);
                        $empName = $this->getEmployeeName($post['emp_id']);
                        if ($saved) {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "Employee licensure exam and certification has been added successfully.";
                            $this->core_layout->setEventLog("User ".$this->loggedInUsername. " inserted new licensure exam and certification type: ".$type." of employee ".$empName['name'] ." ","insert", "success", "gcchris", "user");
                        } else {
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Failed to save employee licensure exam and certification!";
                            $this->core_layout->setEventLog("Failed to save employee licensure exam and certification.","insert", "error", "gcchris", "system");
                        }
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Error, No post data found!";
                        $this->core_layout->setEventLog("Licensure and certification - Error, No post data found.","insert", "error", "gcchris", "system");
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Certificate name already exist please try other names";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "License No. already exist please try other license no.";
            }

            return $resultset;
        }

        function check_license_no($license_no) {
            $this->db->select('license_no');
            $this->db->where('license_no', $license_no);
            $query = $this->db->get('gcchris.tbllicenses');

            if($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }

        function check_cert_exist($cert_name) {
            $this->db->select('certificate_name');
            $this->db->where('certificate_name', $cert_name);
            $query = $this->db->get('gcchris.tbllicenses');

            if($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }

        function setModalDriverLicense() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                unset($post["csrf_token"]);
                $loggedIn = $this->core_layout->getCurrentSession();
                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->employeeDriverLicenseTable, $post);
                $empName = $this->getEmployeeName($post['emp_id']);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee driver's license has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " Added new employee driver license, license no: ".$post['license_no']. " for employee ".$empName['name'],"insert", "success", "gcchris", "user");

                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee driver's license!";
                    $this->core_layout->setEventLog("Failed to save employee driver's license!","insert", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Driver's License - Error, No post data found","insert", "error", "gcchris", "system");
            }
            return $resultset;
        }

        function setModalWorkExperience() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                unset($post["csrf_token"]);
                $loggedIn = $this->core_layout->getCurrentSession();
                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->employeeWorkExperienceTable, $post);
                $empName = $this->getEmployeeName($post['emp_id']);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee work experience has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " Added new employee work experience. Company: ".$post['work_company']." for employee ".$empName['name'],"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee work experience!";
                    $this->core_layout->setEventLog("Work experience - Failed to save employee work experience.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Work experience - Error, No post data found","insert", "error", "gcchris", "user");
            }
            return $resultset;
        }

        function setModalAwards() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                unset($post["csrf_token"]);
                $loggedIn = $this->core_layout->getCurrentSession();
                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->employeeAwardsTable, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee award and achievement has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Added new employee award and achievement details.".$this->db->insert_id(),"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee award and achievement!";
                    $this->core_layout->setEventLog("Award and achievement - Failed to save employee award and achievement.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Award and achievement - Error, No post data found.","insert", "error", "gcchris", "user");
            }
            return $resultset;
        }

        function setModalOrganization() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                unset($post["csrf_token"]);
                $loggedIn = $this->core_layout->getCurrentSession();
                $post["org_from"] = date("Y-01-01", strtotime($post["org_from"]));
                $post["org_to"] = date("Y-01-01", strtotime($post["org_to"]));
                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->employeeOrganizationTable, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee organization has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Added new employee organization experience details.".$this->db->insert_id(),"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee organization!";
                    $this->core_layout->setEventLog("Organization - Failed to save employee organization.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Organization - Error, No post data found.","insert", "error", "gcchris", "user");
            }
            return $resultset;
        }

        function setModalTrainings() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                $tempAttachment = $post["training_attachment"];
                unset($post["csrf_token"], $post["training_attachment"], $post["files"]);
                $loggedIn = $this->core_layout->getCurrentSession();

                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $post["attachment"] = $tempAttachment;

                $saved = $this->db->insert($this->employeeTrainingsTable, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee training and seminar has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Added new training and seminar details.".$this->db->insert_id(),"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee training and seminar!";
                    $this->core_layout->setEventLog("Trainings - Failed to save employee training and seminar.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Trainings - Error, No post data found.","insert", "error", "gcchris", "user");
            }
            return $resultset;
        }

        function setModalReferences() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                unset($post["csrf_token"]);
                $loggedIn = $this->core_layout->getCurrentSession();

                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->employeeReferencesTable, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee personal reference has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Added new personal reference details.".$this->db->insert_id(),"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee personal reference!";
                    $this->core_layout->setEventLog("Personal reference - Failed to save employee personal reference.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Personal reference - Error, No post data found.","insert", "error", "gcchris", "user");
            }
            return $resultset;
        }

        function setModalMedicalHistory() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                $tempAttachment = $post["medical_attachment"];
                $tempRemarks = $post["med_remarks"];
                unset($post["csrf_token"], $post["medical_attachment"], $post["med_remarks"], $post["files"]);
                $loggedIn = $this->core_layout->getCurrentSession();

                $post["remarks"] = $tempRemarks;
                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $post["filename"] = $tempAttachment;

                $saved = $this->db->insert($this->employeeMedicalHistoryTable, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee medical history/record has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Added new medical history/record details.".$this->db->insert_id(),"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee medical history/record!";
                    $this->core_layout->setEventLog("Medical record - Failed to save employee medical history/record.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Medical record - Error, No post data found.","insert", "error", "gcchris", "user");
            }
            return $resultset;
        }

        function setModalLegalHistory() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                unset($post["csrf_token"]);
                $loggedIn = $this->core_layout->getCurrentSession();

                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $saved = $this->db->insert($this->employeeLegalHistoryTable, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee legal history/record has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Added new legal history/record details.".$this->db->insert_id(),"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee legal history/record!";
                    $this->core_layout->setEventLog("Legal record - Failed to save employee legal history/record.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Legal record - Error, No post data found.","insert", "error", "gcchris", "user");
            }
            return $resultset;
        }

        function setModaloffenses() {
            $post = $this->input->post();
            $resultset = array();
            $post2=$post;
            if ($post) {
                $tempAttachment = $post["offenses_attachment"];
                unset($post["csrf_token"], $post["offenses_attachment"], $post["files"]);
                $loggedIn = $this->core_layout->getCurrentSession();

                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $post = array_map('strtoupper', $post);
                $post["filename"] = $tempAttachment;

                $saved = $this->db->insert($this->employeeOffensesTable, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee offense and commendation has been added successfully.";
                    $insertid = $this->db->insert_id();
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Added new offense and commendation details.".$insertid,"insert", "success", "gcchris", "user");
                    $this->updateOffensesCommendationHistory(false,$post2,$insertid);
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee offense and commendation!";
                    $this->core_layout->setEventLog("Offense & Commendation record - Failed to save employee offense and commendation.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Offense & Commendation record - Error, No post data found.","insert", "error", "gcchris", "user");
            }
            return $resultset;
        }

        function setModalDocuments() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                unset($post["csrf_token"]);
                $loggedIn = $this->core_layout->getCurrentSession();

                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $docnames = explode(",", $post['document_names']);
                foreach($docnames as $docname){
                    $employeeDocuments = array(
                        "emp_id"=>$post['emp_id'],
                        "doc_type"=>(isset($post['is_checklist']) && $post['is_checklist']) ? $post['checklist_type'] : $post['doc_type'],
                        "doc_filename"=>$docname,
                        "add_date"=>$post['add_date'],
                        "add_by"=>$loggedIn["emp_id"]
                    );
                    $saved = $this->db->insert($this->employeeDocumentsTable, $employeeDocuments);
                }
                if ($saved) {

                    if(isset($post['is_checklist']) && $post['is_checklist']){
                        $data = array(
                            'emp_id' => $post['emp_id'],
                            'checklist_id' => $post['checklist_id'],
                            'document_id' => $this->db->insert_id(),
                            'added_by' => $this->loggedinData["emp_id"],
                            'added_dt' => date('Y-m-d H:i:s')
                        );

                        $this->db->insert($this->tblChecklist, $data);
                    }

                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee document has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " Added new document details.".$this->db->insert_id(),"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee document!";
                    $this->core_layout->setEventLog("Documents record - Failed to save employee offense and commendation.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Documents record - Error, No post data found.","insert", "error", "gcchris", "user");
            }

            return $resultset;
        }

        function setModalBackgroundCheck() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                $tempAttachment = $post["background_check_attachment"];
                unset($post["csrf_token"], $post["background_check_attachment"], $post["files"]);
                $loggedIn = $this->core_layout->getCurrentSession();

                $post["doc_type"] = "restricted";
                $post["doc_filename"] = $tempAttachment;
                $post["add_date"] = date("Y-m-d H:i:s");
                $post["add_by"] = $loggedIn["emp_id"];

                $saved = $this->db->insert($this->employeeDocumentsTable, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee background check has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Added new document details.".$this->db->insert_id(),"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee background check!";
                    $this->core_layout->setEventLog("Background check record - Failed to save employee background check.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Background check record - Error, No post data found.","insert", "error", "gcchris", "user");
            }
            return $resultset; 
        }

        function setModalJobDescription() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                $where = array("id" => $post["position_id"]);
                unset($post["csrf_token"], $post["position_id"]);
                $loggedIn = $this->core_layout->getCurrentSession();

                $data = array();
                $data["job_desc"] = $post["job_description"];
                $data["modify_dt"] = date("Y-m-d H:i:s");
                $data["modify_by"] = $loggedIn["emp_id"];

                $saved = $this->db->update($this->positionTable, $data, $where);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee job description has been updated.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Added updated job description details.".$where["position_id"],"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee job description!";
                    $this->addEventLog("error",$this->db->error(),$this->positionTable,0);
                    $this->core_layout->setEventLog("Job description record - Failed to save employee job description.","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->core_layout->setEventLog("Job description record - Error, No post data found.","insert", "error", "gcchris", "user");
            }
            return $resultset;
        }

        function setModalQuestionsDescription() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                $employeeId = $post["id"];
                unset($post["csrf_token"], $post["id"]);

                $where = array("id" => $employeeId);
                $saved = $this->db->update($this->employeeTable, $post, $where);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employment question(s) has been updated.";
                    $resultset["data"] = $this->getEmployeeData($employeeId);
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated question(s) details.".$employeeId,"update", "success", "gcchris", "user");

                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to update employment question(s)!";
                    $this->core_layout->setEventLog("Failed to update employment question(s).".$employeeId,"update", "error", "gcchris", "user");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->addEventLog("error","Error, No post data found!",$this->employeeTable,0);
                $this->core_layout->setEventLog("Questionaires - Error, No post data found","update", "error", "gcchris", "user");
            }
            return $resultset;
        }

        function setModalPerformance() {
            $post = $this->input->post();
            $resultset = array();

            if ($post) {
                $tempAttachment = $post["performance_attachment"];
                unset($post["csrf_token"], $post["performance_attachment"], $post["files"]);
                $loggedIn = $this->core_layout->getCurrentSession();

                $year = (isset($post["year"]) && $post["year"]) ? $post["year"] : date("Y");
                $evalYear = (isset($post["year"]) && $post["year"]) ? $post["year"] : false;

                if ($evalYear === false) {
                    $splitRange = explode(" - ", $post["range"]);
                    if (count($splitRange)  == 2) {
                        $year = date("Y", strtotime($splitRange[1]));
                    }
                }

                $post["year"] = $year;
                $post["filename"] = $tempAttachment;
                $post["created_at"] = date("Y-m-d H:i:s");
                $post["created_by"] = $loggedIn["emp_id"];

                $saved = $this->db->insert($this->employeePerformanceTable, $post);
                if ($saved) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Employee performance evaluation has been added successfully.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated performance evaluation details.".$this->db->insert_id(),"update", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to save employee performance evaluation!";
                    $this->addEventLog("error",$this->db->error(),$this->employeePerformanceTable,0);
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Error, No post data found!";
                $this->addEventLog("error","Error, No post data found!",$this->employeePerformanceTable,0);
            }
            return $resultset;
        }

        /*** Insert Function Section ***/

        function getCurrentEmployees($status = "Active") {
            $arrData = array();
            $this->db->from($this->employeeTable);
            if ($status) {
                $this->db->where('employee_status', $status);
            }
            $this->db->where_in("level", ['MANAGERIAL', 'SUPERVISORY']);
            $this->db->order_by('lastname');
            $this->db->order_by('firstname');
            $this->db->order_by('middlename');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $arrData[] = $rs;
                }
            }

            return $arrData;
        }

        function getEmployeeDataSheetDetails($employee_id) {
            $main = $this->core_layout->getEmployee($employee_id);
            $supervisorId = $main->supervisor;
            $result = $this->db->select("
                CASE 
                    WHEN LENGTH(middlename) > 1 THEN CONCAT(firstname, ' ', SUBSTRING(middlename, 1, 1), '. ', lastname)
                    ELSE CONCAT(firstname, ' ', middlename, ' ', lastname)
                END AS name
            ")->from($this->employeeTable)->where("id", $supervisorId)->get()->result();
            $supervisorName = empty($result)? false : $result[0]->name;
            $this->db->reset_query();
            $dependents = $this->db->get_where($this->employeeDependentsTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $licenses = $this->db->get_where($this->employeeLicensureTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $driverlicenses = $this->db->get_where($this->employeeDriverLicenseTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $experiences = $this->getEmployeeExperiencesForPDS($employee_id);
            $awards = $this->db->order_by("award_date", "desc")->get_where($this->employeeAwardsTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $skills = $this->db->get_where($this->employeeSkillsTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $organizations = $this->db->order_by("org_to","desc")->get_where($this->employeeOrganizationTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $trainings = $this->db->order_by("train_to","desc")->get_where($this->employeeTrainingsTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $references = $this->db->get_where($this->employeeReferencesTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $medicals = $this->db->order_by("med_date","desc")->get_where($this->employeeMedicalHistoryTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $legals = $this->db->order_by("leg_case_date","desc")->get_where($this->employeeLegalHistoryTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $offenses = $this->db->order_by('offcom_date', 'DESC')->get_where($this->employeeOffensesTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $accountability = $this->getEmployeeAccountability(0, $employee_id);
            $educations = $this->db->order_by('educ_to', 'DESC')->get_where($this->employeeEducationTable, array("emp_id" => $employee_id,"is_archived" => 0))->result();
            $this->db->reset_query();

            $personnelId = $this->getEmpLocation($main->biometricno);
            $this->db->reset_query();

            $station = $this->db->order_by('id', 'DESC')->get_where($this->tblPersonnelLocation, array("personnel_id" => $personnelId))->result();
            $this->db->reset_query();

            $default_station = $this->db->select("UPPER(TRIM(station_description)) as description")->order_by('id', 'DESC')->get_where($this->defaultStationTable, array("employee_id" => $employee_id))->row();
            $this->db->reset_query();

            $allowance = $this->db->select('IFNULL(rate, 0) rate')->get_where($this->tblAllowances, array('emp_id' => $main->id, 'is_active' => 1, 'is_archived' => 0))->row();
            $this->db->reset_query();

            $rtw = $this->db
            ->select("rtw.id, rtw.reference_no, rtw.return_type, rtw.reason, rtw.from_date, rtw.approved_by, CONCAT(emp.firstname,' ',emp.lastname) as firstname")
            ->join("gccmaster.tblemployees emp", "emp.id = rtw.approved_by", "LEFT")
            ->get_where($this->tblReturnToWork . " rtw", array("rtw.employee_id" => $employee_id))
            ->result();
            $this->db->reset_query();

            $salaries = $this->db
                ->select("sal.id,sal.add_date, sal.sal_date, sal.sal_rate, sal.sal_remarks, IF(pos.id IS NULL, sal.sal_position, pos.name) sal_position")
                ->join("gcchris.tblposition pos", "pos.id = sal.sal_position", "LEFT")
                ->order_by("sal.add_date", "desc")
                ->get_where($this->employeeSalaryTable . " sal", array("sal.emp_id" => $employee_id, "sal.is_archived" => 0))
                ->result();
            $this->db->reset_query();

            $if_driver = $this->db
                ->select("emp.position as position")
                ->get_where($this->employeeTable . " emp", array("emp.id" => $employee_id))
                ->row_array();
            $this->db->reset_query();

            if(is_numeric($if_driver['position'])){
                $driver = $this->db
                ->group_start()
                ->like("pos.name","driver")
                ->or_like("pos.name","operator")
                ->group_end()
                ->from("gcchris.tblposition pos")
                ->join("gccmaster.tblemployees emp","emp.position = pos.id")
                ->where("emp.id",$employee_id)
                ->count_all_results();
                $this->db->reset_query();

            }else{
                $driver = $this->db
                ->group_start()
                ->like("emp.position","driver")
                ->or_like("emp.position","operator")
                ->group_end()
                ->from("gccmaster.tblemployees emp")
                ->where("emp.id",$employee_id)
                ->count_all_results();
                $this->db->reset_query();
            }

            //     CASE WHEN emp.suffix = 'NONE' THEN '' ";
            // $select .= "        WHEN emp.suffix = 'N/A' THEN '' ";

            $this->db->reset_query();

            $this->db->select("logo");
            $this->db->join($this->companyTable . " companies", "employee.company_id = companies.description", "INNER");
            $this->db->where("employee.id", $employee_id);
            $logo = trim($this->db->get($this->employeeTable . " employee")->row("logo"));
            $this->db->reset_query();

            $company_logo = "assets/images/company/" . $logo;

            if (!file_exists(realpath($company_logo))) {
                $company_logo = "";
            }

            return
                array(
                    "supervisor" => $supervisorName,
                    "main" => $main,
                    "dependents" => $dependents,
                    "questions" => $this->questions,
                    "educations" => $educations,
                    "licenses" => $licenses,
                    "driverlicenses" => $driverlicenses,
                    "experiences" => $experiences,
                    "awards" => $awards,
                    "skills" => $skills,
                    "organizations" => $organizations,
                    "trainings" => $trainings,
                    "references" => $references,
                    "medicals" => $medicals,
                    "legals" => $legals,
                    "offenses" => $offenses,
                    "salaries" => $salaries,
                    "company_logo" => $company_logo,
                    "user" => $this->core_layout->getUserLoggedIn(),
                    "accountability" => $accountability,
                    "return_to_work" => $rtw,
                    "if_driver" => $driver,
                    "station" => $station,
                    "allowance" => $allowance ? $allowance->rate : 0,
                    "default_station" => $default_station,
                );
        }

        private function getEmpLocation($emp_bio){
            $this->db->select('id');
            $this->db->where("biometricno", $emp_bio);
            $data = $this->db->get($this->tblPersonnel);
            $personel_id = $data->row_array();
            return isset($personel_id['id']) && $personel_id['id'] ? $personel_id['id']: 0;
        }

        private function getEmployeeExperiencesForPDS($employee_id) {
            $this->db->get_where($this->employeeWorkExperienceTable, array("emp_id" => $employee_id))->result();
            $this->db->select("xps.id, xps.emp_id, xps.work_from, xps.work_to,
                               xps.work_company, xps.work_status, xps.work_reason, xps.old_idno,
                               IF(com.id IS NULL, xps.work_company, com.`description`) work_company,
                               IF(pos.id IS NULL, xps.work_position, pos.`name`) work_position");
            $this->db->join($this->positionTable . " pos", "pos.id = xps.work_position", "LEFT");
            $this->db->join($this->companyTable . " com", "com.id = xps.work_company", "LEFT");
            $this->db->order_by("work_from, work_to","DESC");
            return $this->db->get_where($this->employeeWorkExperienceTable . " xps", array("xps.emp_id" => $employee_id, "xps.is_archived"=>0))->result();
        }

        function getEmployeeDocumentsForTab($employee_id) {

            $searchKey = $this->input->post('searchKey');

            /* DOCUMENTS */
            $this->db->select("documents.*, DATE_FORMAT(documents.add_date, '%b %d, %Y %h:%i:%s %p') date_uploaded, 
                CONCAT(UPPER(TRIM(emp.firstname)), ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END,' ', UPPER(TRIM(emp.lastname)),
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
                END) as added_by_name");
            $this->db->join("gccmaster.tblemployees as emp", "emp.id = documents.add_by", "LEFT");
            $this->db->where("documents.emp_id", $employee_id);
            $this->db->where("documents.is_archived", 0);
            $this->db->where("documents.doc_type !=", "restricted");
            $this->db->like('CONCAT(documents.doc_type, documents.doc_filename)', $searchKey, 'both');
            $this->db->group_by("documents.doc_filename, documents.doc_type");
            $documents = array();
            $_documents = $this->db->get($this->employeeDocumentsTable . " documents")->result();
            foreach ($_documents as $document) {
                $path = "uploads/files/documents/employee_files/empcode_" . $employee_id . "/documents/" . $document->doc_filename;
                $realpath = realpath($path);
                $fileExist = file_exists($realpath);
                $document->exists = $fileExist;
                $document->filepath = base_url($path);

                $document->to_replace = false;
                $document->to_replace_filename = null;
                $document->to_replace_filepath = null;

                if($fileExist == false){
                    $tempFiles = scandir("uploads/files/documents/employee_files/empcode_{$employee_id}/documents/");
                    foreach ($tempFiles as $file) {
                        if(strstr($file, $document->doc_filename)){
                            $document->to_replace_filename = $file;
                            $document->to_replace = true;
                            $document->to_replace_filepath = base_url("uploads/files/documents/employee_files/empcode_{$employee_id}/documents/{$file}");
                        }
                    }
                }

                array_push($documents, $document);
            }
            /* DOCUMENTS */

            $this->db->reset_query();

            /* BACKGROUND CHECK */
            $this->db->select("documents.*, DATE_FORMAT(documents.add_date, '%b %d, %Y %h:%i:%s %p') date_uploaded, 
                CONCAT(UPPER(TRIM(emp.firstname)), ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END,' ', UPPER(TRIM(emp.lastname)),
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
                END) as added_by_name");
            $this->db->join("gccmaster.tblemployees as emp", "emp.id = documents.add_by", "LEFT");
            $this->db->where("documents.emp_id", $employee_id);
            $this->db->where("documents.is_archived", 0);
            $this->db->where("documents.doc_type", "restricted");
            $this->db->like('CONCAT(documents.doc_type, documents.doc_filename)', $searchKey, 'both');
            $this->db->group_by("documents.doc_filename, documents.doc_type");
            $bgcheck = array();
            $bgcheck_docs = $this->db->get($this->employeeDocumentsTable . " documents")->result();
            foreach ($bgcheck_docs as $document) {
                $path = "uploads/files/documents/employee_files/empcode_" . $employee_id . "/documents/" . $document->doc_filename;
                $realpath = realpath($path);
                $document->exists = file_exists($realpath);
                $document->filepath = base_url($path);

                $document->to_replace = false;
                $document->to_replace_filename = null;
                $document->to_replace_filepath = null;

                if($fileExist == false){
                    $tempFiles = scandir("uploads/files/documents/employee_files/empcode_{$employee_id}/documents/");
                    foreach ($tempFiles as $file) {
                        if(strstr($file, $document->doc_filename)){
                            $document->to_replace_filename = $file;
                            $document->to_replace = true;
                            $document->to_replace_filepath = base_url("uploads/files/documents/employee_files/empcode_{$employee_id}/documents/{$file}");
                        }
                    }
                }

                array_push($bgcheck, $document);
            }
            /* BACKGROUND CHECK */

            $this->db->reset_query();

            /* TRAINING & SEMINARS */
            $this->db->select("trainings.*, DATE_FORMAT(trainings.add_date, '%b %d, %Y %h:%i:%s %p') date_uploaded, 
                'Training & Seminar' as doc_type, trainings.attachment as doc_filename, 
                CONCAT(UPPER(TRIM(emp.firstname)), ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END,' ', UPPER(TRIM(emp.lastname)),
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
                END) as added_by_name", FALSE);

            $this->db->join("gccmaster.tblemployees as emp", "emp.id = trainings.add_by", "LEFT");
            $this->db->where("trainings.emp_id", $employee_id);
            $this->db->where("trainings.is_archived", 0);
            $this->db->where("trainings.attachment !=", "");
            $this->db->where("trainings.attachment IS NOT NULL", NULL, FALSE);
            $this->db->like('CONCAT(trainings.training, trainings.attachment)', $searchKey, 'both');
            $this->db->group_by("trainings.training, trainings.attachment");
            $trainings = array();
            $_trainings = $this->db->get($this->employeeTrainingsTable . " trainings")->result();
            foreach ($_trainings as $training) {
                $path = "uploads/files/documents/employee_files/empcode_" . $employee_id . "/trainings/" . $training->doc_filename;
                $realpath = realpath($path);
                $training->exists = file_exists($realpath);
                $training->filepath = base_url($path);

                $training->to_replace = false;
                $training->to_replace_filename = null;
                $training->to_replace_filepath = null;

                if($fileExist == false){
                    $tempFiles = scandir("uploads/files/documents/employee_files/empcode_{$employee_id}/trainings/");
                    foreach ($tempFiles as $file) {
                        if(strstr($file, $training->doc_filename)){
                            $training->to_replace_filename = $file;
                            $training->to_replace = true;
                            $training->to_replace_filepath = base_url("uploads/files/documents/employee_files/empcode_{$employee_id}/trainings/{$file}");
                        }
                    }
                }

                array_push($trainings, $training);
            }
            /* TRAINING & SEMINARS */

            $this->db->reset_query();

            /* MEDICAL RECORDS */
            $this->db->select("medical.*, DATE_FORMAT(medical.add_date, '%b %d, %Y %h:%i:%s %p') date_uploaded, 
                'Medical Record' as doc_type, medical.filename as doc_filename,
                CONCAT(UPPER(TRIM(emp.firstname)), ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END,' ', UPPER(TRIM(emp.lastname)),
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
                END) as added_by_name", FALSE);

            $this->db->join("gccmaster.tblemployees as emp", "emp.id = medical.add_by", "LEFT");
            $this->db->where("medical.emp_id", $employee_id);
            $this->db->where("medical.is_archived", 0);
            $this->db->where("medical.filename !=", "");
            $this->db->where("medical.filename IS NOT NULL", NULL, FALSE);
            $this->db->like('CONCAT(medical.med_details, medical.filename, medical.med_findings, medical.med_venue, medical.med_physician, DATE(medical.add_date))', $searchKey, 'both');
            $medical_records = array();
            $_medical_records = $this->db->get($this->employeeMedicalHistoryTable . " medical")->result();
            foreach ($_medical_records as $medical) {
                $path = "uploads/files/documents/employee_files/empcode_" . $employee_id . "/medical/" . $medical->doc_filename;
                $realpath = realpath($path);
                $medical->exists = file_exists($realpath);
                $medical->filepath = base_url($path);

                $medical->to_replace = false;
                $medical->to_replace_filename = null;
                $medical->to_replace_filepath = null;

                if($fileExist == false){
                    $tempFiles = scandir("uploads/files/documents/employee_files/empcode_{$employee_id}/medical/");
                    foreach ($tempFiles as $file) {
                        if(strstr($file, $medical->doc_filename)){
                            $medical->to_replace_filename = $file;
                            $medical->to_replace = true;
                            $medical->to_replace_filepath = base_url("uploads/files/documents/employee_files/empcode_{$employee_id}/medical/{$file}");
                        }
                    }
                }

                array_push($medical_records, $medical);
            }
            /* MEDICAL RECORDS */

            $this->db->reset_query();

            /* OFFENSE AND COMMENDATION */
            $this->db->select("offenses.*, DATE_FORMAT(offenses.add_date, '%b %d, %Y %h:%i:%s %p') date_uploaded, 
                offenses.offcom_nature as doc_type, offenses.filename doc_filename, 
                CONCAT(UPPER(TRIM(emp.firstname)), ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END,' ', UPPER(TRIM(emp.lastname)),
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
                END) as added_by_name", FALSE);

            $this->db->join("gccmaster.tblemployees as emp", "emp.id = offenses.add_by", "LEFT");
            $this->db->where("offenses.emp_id", $employee_id);
            $this->db->where("offenses.filename !=", "");
            $this->db->where("offenses.filename IS NOT NULL", NULL, FALSE);
            $this->db->where("(offenses.is_archived=0 OR offenses.is_archived IS NULL)", NULL, FALSE);
            $this->db->like('CONCAT(offenses.offcom_type, offenses.filename, DATE(offenses.add_date), DATE(offenses.offcom_date), offenses.offcom_nature, offenses.offcom_action)', $searchKey, 'both');
            $this->db->order_by("offenses.offcom_date", "desc");
            $offenses = array();
            $_offenses = $this->db->get($this->employeeOffensesTable . " offenses")->result();
            foreach ($_offenses as $offense) {
                $path = "uploads/files/documents/employee_files/empcode_" . $employee_id . "/offenses_commendation/" . $offense->doc_filename;
                $realpath = realpath($path);
                $offense->exists = file_exists($realpath);
                $offense->filepath = base_url($path);

                $offense->to_replace = false;
                $offense->to_replace_filename = null;
                $offense->to_replace_filepath = null;

                if($fileExist == false){
                    $tempFiles = scandir("uploads/files/documents/employee_files/empcode_{$employee_id}/offenses_commendation/");
                    foreach ($tempFiles as $file) {
                        if(strstr($file, $offense->doc_filename)){
                            $offense->to_replace_filename = $file;
                            $offense->to_replace = true;
                            $offense->to_replace_filepath = base_url("uploads/files/documents/employee_files/empcode_{$employee_id}/offenses_commendation/{$file}");
                        }
                    }
                }

                array_push($offenses, $offense);
            }
            /* OFFENSE AND COMMENDATION */

            /* PERFORMANCE EVALUATION */
            $this->db->select("performance.*, DATE_FORMAT(performance.created_at, '%b %d, %Y %h:%i:%s %p') date_uploaded, 
                'Performance Evaluation' as doc_type, performance.filename as doc_filename, 
                CONCAT(UPPER(TRIM(emp.firstname)), ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END,' ', UPPER(TRIM(emp.lastname)),
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
                END) as added_by_name", FALSE);
            
            $this->db->join("gccmaster.tblemployees as emp", "emp.id = performance.created_by", "LEFT");
            $this->db->where("performance.emp_id", $employee_id);
            $this->db->where("performance.is_archived", 0);
            $this->db->where("performance.filename !=", "");
            $this->db->where("performance.filename IS NOT NULL", NULL, FALSE);
            $this->db->like('CONCAT(performance.`year`, performance.`range`, performance.`filename`, DATE(performance.created_at))', $searchKey, 'both');
            $performance = array();
            $_performance = $this->db->get($this->employeePerformanceTable . " performance")->result();
            foreach ($_performance as $p) {
                $path = "uploads/files/documents/employee_files/empcode_" . $employee_id . "/performance_eval/" . $p->doc_filename;
                $realpath = realpath($path);
                $p->exists = file_exists($realpath);
                $p->filepath = base_url($path);

                $p->to_replace = false;
                $p->to_replace_filename = null;
                $p->to_replace_filepath = null;

                if($fileExist == false){
                    $tempFiles = scandir("uploads/files/documents/employee_files/empcode_{$employee_id}/performance_eval/");
                    foreach ($tempFiles as $file) {
                        if(strstr($file, $p->doc_filename)){
                            $p->to_replace_filename = $file;
                            $p->to_replace = true;
                            $p->to_replace_filepath = base_url("uploads/files/documents/employee_files/empcode_{$employee_id}/performance_eval/{$file}");
                        }
                    }
                }

                array_push($performance, $p);
            }
            /* PERFORMANCE EVALUATION */

            return array(
                "documents" => $documents,
                "trainings" => $trainings,
                "medical" => $medical_records,
                "offenses" => $offenses,
                "performance" => $performance,
                "bgcheck" => $bgcheck
            );
        }

        /* EDIT FUNCTION GETTERS */
        function getDocumentInfo($form) {
            $id = $form['id'];

            $sql = 'a.id as docId, a.emp_id as empId, a.*, b.id as checklistedId, b.emp_id as employee_id, b.*';
            $this->db->select($sql);
            $this->db->from($this->employeeDocumentsTable.' as a');
            $this->db->join($this->tblChecklist.' as b', 'b.document_id = a.id', 'LEFT');
            $this->db->where('a.id', $id);
            $query = $this->db->get()->row();
            
            // $query = $this->db->get_where($this->employeeDocumentsTable, array("id" => $id))->row();
            return array("data" => $query);
        }

        function getLegalRecords($form) {
            $id = $form['id'];
            $query = $this->db->get_where($this->employeeLegalHistoryTable, array("id" => $id))->row();
            return array("data" => $query);
        }

        function getOffensesAndCommendations($form) {
            $id = $form['id'];
            $query = $this->db->get_where($this->employeeOffensesTable, array("id" => $id))->row();
            return array("data" => $query);
        }

        function getBackgroundCheck($form) {
            $id = $form['id'];
            $where = array("id" => $id, "doc_type" => "restricted");
            $query = $this->db->get_where($this->employeeDocumentsTable, $where)->row();
            return array("data" => $query);
        }

        function getQuestionAnswers($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeTable, $where)->row();
            return array("data" => $query);
        }

        function getJobDescription($form) {
            $id = $form['id'];
            $this->db->select("b.*");
            $this->db->from($this->employeeTable . " a");
            $this->db->join($this->positionTable . " b", "b.id = a.position", "LEFT");
            $this->db->where("a.id", $id);
            $this->db->where("b.job_desc !=", null);
            $query = $this->db->get()->row();
            return array("data" => $query);
        }

        function getPerformanceEvaluation($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $emp_id = $this->db->where($where)->get($this->employeePerformanceTable)->row("emp_id");
            $query = $this->db->get_where($this->employeePerformanceTable, $where)->row();
            $other = $this->setPerformanceData($emp_id);
            return array("data" => $query, "other" => $other);
        }

        function getCashAdvances($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeCashAdvanceTable, $where)->row();
            return array("data" => $query);
        }

        function getDependentInfo($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeDependentsTable, $where)->row();
            return array("data" => $query);
        }

        function getEducationalInfo($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeEducationTable, $where)->row();
            return array("data" => $query);
        }

        function getLicensure($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeLicensureTable, $where)->row();
            return array("data" => $query);
        }

        function getDriverLicense($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeDriverLicenseTable, $where)->row();
            return array("data" => $query);
        }

        function getWorkExperience($form) {
            $id = $form['id'];
            $where = array("xps.id" => $id);
            $this->db->select("xps.id, xps.emp_id, xps.work_to,
                               xps.work_company, xps.work_status, xps.work_reason, xps.work_from, old_idno,
                               IF(pos.id IS NULL, xps.work_position, pos.`name`) work_position");
            $this->db->join($this->positionTable . " pos", "pos.id = xps.work_position", "LEFT");
            $query = $this->db->get_where($this->employeeWorkExperienceTable . " xps", $where)->row();
            return array("data" => $query);
        }

        function getAward($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeAwardsTable, $where)->row();
            return array("data" => $query);
        }

        function getOrganization($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeOrganizationTable, $where)->row();
            return array("data" => $query);
        }

        function getTraningsAndSeminars($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeTrainingsTable, $where)->row();
            return array("data" => $query);
        }

        function getPersonalReference($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeReferencesTable, $where)->row();
            return array("data" => $query);
        }

        function getMedicalHistory($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $query = $this->db->get_where($this->employeeMedicalHistoryTable, $where)->row();
            return array("data" => $query);
        }
        /* END OF EDIT FUNCTION GETTERS */

        /* UPDATE FUNCTIONS */
        function updateDocument($post) {
            $resultSet = array();

            // added for pre-employment checklist
            $post->doc_type = isset($post->is_checklist) && $post->is_checklist ? $post->checklist_type : $post->doc_type;

            if(isset($post->is_checklist) && $post->is_checklist){
                $checklistId = $post->checklist_id;
                $checklistDocId = $post->checklist_document_id;
                
                unset($post->checklist_id, $post->checklist_document_id, $post->checklist_type);
            }else{
                unset($post->checklist_id, $post->checklist_document_id);
            }
            // added for pre-employment checklist
            
            $this->db->trans_begin();
            $this->db->where("id", $post->id);
            $this->db->update($this->employeeDocumentsTable, array("doc_type" => $post->doc_type));

            $emp_id = $this->db->where("id", $post->id)
                ->get($this->employeeDocumentsTable)->row("emp_id");
            $uploadPath = './uploads/files/documents/employee_files/empcode_' . $emp_id . '/documents';
            $uploaded = null;

            if ($_FILES["files"]["name"]) {
                $current_file_path = $uploadPath . "/" . $post->current_filename;
                if ($post->current_filename && file_exists(realpath($current_file_path))) {
                    unlink(realpath($current_file_path));
                }

                $config = array();
                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG|pdf|doc|docx';
                $config['max_size'] = 10000;
                $config['create_thumbnail'] = false;

                $uploaded = $this->file_upload->uploadFile($config);

                if ($uploaded['response']) {
                    $this->db->where("id", $post->id)
                        ->update($this->employeeDocumentsTable, array("doc_filename" => $uploaded['files'][0]['file_name']));
                }
            }

            if(isset($post->is_checklist) && $post->is_checklist){
                $data = array(
                    'checklist_id' => $checklistId,
                    'updated_by' => $this->loggedinData["emp_id"],
                    'updated_dt' => date('Y-m-d H:i:s')
                );

                $this->db->where('id', $checklistDocId);
                $this->db->update($this->tblChecklist, $data);
            }

            if ($this->db->trans_status() === FALSE) {
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->db->trans_rollback();
                $this->core_layout->setEventLog("Error updating addocuments details.".$post->id,"update", "error", "gcchris", "user");
            } else {
                $resultSet["success"] = "true";
                $resultSet["message"] = "Record was successfully updated.";
                $resultSet["data"] = array(
                    "doc_type" => $post->doc_type,
                    "doc_filename" => $uploaded['response'] ? $uploaded['files'][0]['file_name'] : ""
                );
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated documents details.".$post->id,"update", "success", "gcchris", "user");
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function updateLegalRecords($post) {
            $resultSet = array();
            $id = $post->id;
            unset($post->id);

            $this->db->where("id", $id);
            if ($this->db->update($this->employeeLegalHistoryTable, $post)) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated legal details.".$id,"update", "success", "gcchris", "user");
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->core_layout->setEventLog("Error updating legal details.".$post->id,"update", "error", "gcchris", "user");
            }

            $resultSet['data'] = $post;
            return $resultSet;
        }

        function updateOffensesAndCommendations($post) {
            $id = $post->id;
            $current_filename = $post->current_filename;
            $post2 = $this->input->post();
            $post2['filename'] = "";
            $currentData =  $this->getOffCommById($id);
            unset($post->id, $post->current_filename);
            $resultSet = array();
            $this->db->trans_begin();
            $this->db->where("id", $id);
            $this->db->update($this->employeeOffensesTable, $post);

            $emp_id = $this->db->where("id", $id)->get($this->employeeOffensesTable)->row("emp_id");
            $uploadPath = './uploads/files/documents/employee_files/empcode_' . $emp_id . '/offenses_commendation';
            $uploaded = null;

            if ($_FILES["files"]["name"]) {
                $current_file_path = $uploadPath . "/" . $current_filename;

                if (!file_exists(realpath($uploadPath))) {
                    mkdir($uploadPath, 0777, true);
                }

                if ($current_filename && file_exists(realpath($current_file_path))) {
                    unlink(realpath($current_file_path));
                }

                $config = array();
                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG|pdf|doc|docx';
                $config['max_size'] = 10000;
                $config['create_thumbnail'] = false;

                $uploaded = $this->file_upload->uploadFile($config);

                if ($uploaded['response']) {
                    $this->db->where("id", $id)
                        ->update($this->employeeOffensesTable, array("filename" => $uploaded['files'][0]['file_name']));
                        $post2['filename'] = $uploaded['files'][0]['file_name'];
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error updating offensses and commendation details.".$id,"update", "error", "gcchris", "user");
                $this->db->trans_rollback();
            } else {
                $this->updateOffensesCommendationHistory($currentData,$post2,false);
                $resultSet["success"] = "true";
                $resultSet["message"] = "Record was successfully updated.";
                $resultSet["data"] = $post;
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated offensses and commendation details.".$id,"update", "success", "gcchris", "user");
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function updateBgCheck($post) {
            $id = $post->id;
            $current_filename = $post->current_filename;

            unset($post->id, $post->current_filename);
            $resultSet = array();
            $this->db->trans_begin();

            $this->db->where("id", $id);
            $emp_id = $this->db->get($this->employeeDocumentsTable)->row("emp_id");
            $uploadPath = './uploads/files/documents/employee_files/empcode_' . $emp_id . '/documents';
            $uploaded = null;

            if ($_FILES["files"]["name"]) {
                $current_file_path = $uploadPath . "/" . $current_filename;
                if ($current_filename && file_exists(realpath($current_file_path))) {
                    unlink(realpath($current_file_path));
                }

                $config = array();
                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG|pdf|doc|docx';
                $config['max_size'] = 10000;
                $config['create_thumbnail'] = false;

                $uploaded = $this->file_upload->uploadFile($config);

                if ($uploaded['response']) {
                    $this->db->where("id", $id)
                        ->update($this->employeeDocumentsTable, array("doc_filename" => $uploaded['files'][0]['file_name']));
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->addEventLog("error",$this->db->error(),$this->employeeDocumentsTable,$id);
                $this->core_layout->setEventLog("Error updating background check details.".$id,"update", "error", "gcchris", "user");
                $this->db->trans_rollback();
            } else {
                $resultSet["success"] = "true";
                $resultSet["message"] = "Record was successfully updated.";
                $resultSet["data"] = array(
                    "doc_filename" => $uploaded['response'] ? $uploaded['files'][0]['file_name'] : ""
                );
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated background check details.".$id,"update", "success", "gcchris", "user");
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function updateEmployeeQuestionAnswers($post) {
            $this->db->trans_begin();
            $resultSet = array();
            $data = array();
            foreach ($post->ques as $key => $answer) {
                $data["ques" . ($key + 1)] = $answer;
            }

            $this->db->where("id", $post->id);
            $this->db->update($this->employeeTable, $data);

            if ($this->db->trans_status() === FALSE) {
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating employment questionaires details.".$post->id,"update", "error", "gcchris", "user");
                $this->db->trans_rollback();
            } else {
                $resultSet["success"] = "true";
                $resultSet["message"] = "Record was successfully updated.";
                $resultSet["data"] = $post->ques;
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated employment questionaires details.".$post->id,"update", "success", "gcchris", "user");
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function updateEmployeeJobDescription($post) {
            $resultSet = array();
            $id = $post->id;
            unset($post->id);

            $this->db->where("id", $id);
            if ($this->db->update($this->positionTable, $post)) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Job Description successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated job description details.".$id,"update", "success", "gcchris", "user");
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating job description details.".$id,"update", "error", "gcchris", "user");
            }
            $resultSet['data'] = $post;

            return $resultSet;
        }

        function updatePerformanceEvaluation($post) {
            $this->db->trans_begin();
            $id = $post->id;
            $current_filename = $post->current_filename;
            unset($post->id, $post->current_filename);

            $uploaded = null;

            $resultSet = array();
            $this->db->where("id", $id);
            $this->db->update($this->employeePerformanceTable, $post);

            $emp_id = $this->db->where("id", $id)->get($this->employeePerformanceTable)->row("emp_id");
            $uploadPath = './uploads/files/documents/employee_files/empcode_' . $emp_id . '/performance_eval';
            $uploaded = null;

            if ($_FILES["files"]["name"]) {
                $current_file_path = $uploadPath . "/" . $current_filename;

                if (!file_exists(realpath($uploadPath))) {
                    mkdir($uploadPath, 0777, true);
                }

                if ($current_filename && file_exists(realpath($current_file_path))) {
                    unlink(realpath($current_file_path));
                }

                $config = array();
                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG|pdf|doc|docx';
                $config['max_size'] = 10000;
                $config['create_thumbnail'] = false;

                $uploaded = $this->file_upload->uploadFile($config);

                if ($uploaded['response']) {
                    $this->db->where("id", $id)
                        ->update($this->employeePerformanceTable, array("filename" => $uploaded['files'][0]['file_name']));
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating performance evaluation details.".$id,"update", "error", "gcchris", "user");
                $this->db->trans_rollback();
            } else {
                $resultSet["success"] = "true";
                $resultSet["message"] = "Record was successfully updated.";
                $resultSet["data"] = array(
                    "quarter" => $post->quarter,
                    "range" => $post->range,
                    "year" => $post->year,
                    "filename" => (!empty($uploaded) && $uploaded['response']) ? $uploaded['files'][0]['file_name'] : $current_filename);
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated performance evaluation details.".$id,"update", "success", "gcchris", "user");
                $this->db->trans_commit();
            }

            return $resultSet;
        }

        function updateCashAdvance($post) {
            $resultSet = array();
            $id = $post->id;
            unset($post->id);

            $this->db->where("id", $id);
            if ($this->db->update($this->employeeCashAdvanceTable, $post)) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated cash advance details.".$id,"update", "success", "gcchris", "user");
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating cash advance details.".$id,"update", "error", "gcchris", "user");
            }

            $resultSet['data'] = $post;

            return $resultSet;
        }

        function updateDependent($post) {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error()
            );
            $id = $post->id;
            unset($post->id);

            $post->dep_age = DateTime::createFromFormat('Y-m-d', $post->dep_birthdate)->diff(new DateTime('now'))->y;

            $this->db->where("id", $id);
            if ($this->db->update($this->employeeDependentsTable, $post)) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated dependent details.".$id,"update", "success", "gcchris", "user");
            }else{ 
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating dependent details.".$id,"update", "error", "gcchris", "user");
            }

            $resultSet["data"] = array(
                "dep_name" => $post->dep_name,
                "dep_age" => $post->dep_age . " YEARS OLD",
                "dep_relation" => $post->dep_relation,
                "dep_birthdate" => $post->dep_birthdate
            );

            return $resultSet;
        }

        function updateEducationalBackground($post) {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error(),
                "data" => $post
            );
            $id = $post->id;
            unset($post->id);

            $this->db->where("id", $id);
            if ($this->db->update($this->employeeEducationTable, $post)) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " Updated educational background details.".$id,"update", "success", "gcchris", "user");
            }else{
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating educational background details.".$id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function updateLicensure($post) {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error(),
                "data" => $post
            );
            $id = $post->id;
            $is_active = isset($post->is_active) && $post->is_active == 1 ? $post->is_active : 0;
            unset($post->id);

            $license = explode('-', $post->license_type);
            $post->license_id = $license[0];
            $post->license_type = $license[1];
            $license_type = ($license[0] != 0) ? $this->getLicense($license[0])->type : null;

            if(isset($post->is_active) && $post->is_active == 1){
                unset($post->is_active);
            }else{
                $post->expiration_date = null;
                unset($post->is_active);
            }
            $this->db->where("id", $id);
            if ($this->db->update($this->employeeLicensureTable, $post)) {
                unset($post->license_id);

                if($license_type){
                    $html = "";

                    if($license_type == 'COMPANY SPONSORED - INTERNAL'){
                        $html .= '<p style="margin: 0; font-size: 9px;" class="badge badge-success">'.$license_type.'</p>';
                    }elseif($license_type == 'COMPANY SPONSORED - EXTERNAL'){
                        $html .= '<p style="margin: 0; font-size: 9px;" class="badge badge-danger">'.$license_type.'</p>';
                    }else{
                        $html .= '<p style="margin: 0; font-size: 9px;" class="badge badge-info">'.$license_type.'</p>';
                    }

                    $html .= '<p style="margin: 0">'.$license[1].'</p>';

                    $post->license_type = $html;
                }

                if($is_active == 0){
                    $post->expiration_date = 'No Expiry';
                }else{
                    if(date("Y-m-d") >= $post->expiration_date){
                        $post->expiration_date = "<span class='m-badge m-badge--danger m-badge--wide'>$post->expiration_date</span>";
                    }else{
                        $post->expiration_date = "<span class='m-badge m-badge--success m-badge--wide'>$post->expiration_date</span>";
                    }
                }
                
                $resultSet["success"] = true;
                $resultSet["message"] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated licensure details with db id ".$id,"update", "success", "gcchris", "user");
            }else{
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating licensure details with db id ".$id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function updateDriverLicense($post) {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error(),
                "data" => $post
            );
            $id = $post->id;
            unset($post->id);

            $this->db->where("id", $id);
            if ($this->db->update($this->employeeDriverLicenseTable, $post)) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated Updated drivers license details.".$id,"update", "success", "gcchris", "user");
            }else{
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating drivers license details.".$id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function updateWorkExperience($post) {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error()
            );
            $id = $post->id;
            unset($post->id);

            $this->db->where("id", $id);
            if ($this->db->update($this->employeeWorkExperienceTable, $post)) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated Updated work experience details.".$id,"update", "success", "gcchris", "user");
            }else{
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating work experience details.".$id,"update", "error", "gcchris", "user");
            }

            $resultSet["data"] = array(
                "work_company" => $post->work_company,
                "work_from" => $post->work_from,
                "work_to" => $post->work_to,
                "work_position" => $post->work_position,
                "old_idno" => empty($post->old_idno) ? "N/A" : $post->old_idno,
                "work_status" => $post->work_status,
                "work_reason" => $post->work_reason,
            );

            return $resultSet;
        }

        function updateAward($post) {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error()
            );
            $id = $post->id;
            unset($post->id);

            $this->db->where("id", $id);
            if ($this->db->update($this->employeeAwardsTable, $post)) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated Updated awards details.".$id,"update", "success", "gcchris", "user");
            }else{
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating awards details.".$id,"update", "error", "gcchris", "user");
            }

            $resultSet["data"] = $post;

            return $resultSet;
        }

        function updateOrganization($post) {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error()
            );
            $id = $post->id;
            unset($post->id);

            $post->org_from .= "-01-01";
            $post->org_to .= "-01-01";;

            $this->db->where("id", $id);
            if ($this->db->update($this->employeeOrganizationTable, $post)) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated Updated organization details.".$id,"update", "success", "gcchris", "user");
            }else{
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating organization details.".$id,"update", "error", "gcchris", "user");
            }

            $post->org_from = str_replace("-01-01", "", $post->org_from);
            $post->org_to = str_replace("-01-01", "", $post->org_to);
            $resultSet["data"] = $post;

            return $resultSet;
        }

        function updateTraining($post) {
            $id = $post->id;
            $current_filename = $post->current_filename;

            unset($post->id, $post->current_filename);
            $resultSet = array();
            $this->db->trans_begin();
            $this->db->where("id", $id);
            $this->db->update($this->employeeTrainingsTable, $post);

            $emp_id = $this->db->where("id", $id)->get($this->employeeTrainingsTable)->row("emp_id");
            $uploadPath = './uploads/files/documents/employee_files/empcode_' . $emp_id . '/trainings';
            $uploaded = null;

            if ($_FILES["files"]["name"]) {
                $current_file_path = $uploadPath . "/" . $current_filename;
                if (!file_exists(realpath($uploadPath))) {
                    mkdir($uploadPath, 0777, true);
                }

                if ($current_filename && file_exists(realpath($current_file_path))) {
                    unlink(realpath($current_file_path));
                }

                $config = array();
                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG|pdf|doc|docx';
                $config['max_size'] = 10000;
                $config['create_thumbnail'] = false;

                $uploaded = $this->file_upload->uploadFile($config);

                if ($uploaded['response']) {
                    $this->db->where("id", $id)
                        ->update($this->employeeTrainingsTable, array("attachment" => $uploaded['files'][0]['file_name']));
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating trainings details.".$id,"update", "error", "gcchris", "user");
                $this->db->trans_rollback();
            } else {
                $resultSet["success"] = "true";
                $resultSet["message"] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated trainings details.".$id,"update", "success", "gcchris", "user");
                $this->db->trans_commit();
            }

            $post->attachment = $uploaded['response'] ? $uploaded['files'][0]['file_name'] : $current_filename;
            $resultSet["data"] = array(
                "training" => $post->training,
                "train_from" => $post->train_from,
                "train_to" => $post->train_to,
                "train_institution" => $post->train_institution,
                "train_conductor" => $post->train_conductor,
                "train_venue" => $post->train_venue,
                "attachment" => $post->attachment,
            );
            return $resultSet;
        }

        function updatePersonalReferences($post) {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error()
            );
            $id = $post->id;
            unset($post->id);

            $this->db->where("id", $id);
            if ($this->db->update($this->employeeReferencesTable, $post)) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated presonal references details.".$id,"update", "success", "gcchris", "user");
            }else{
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error Updating presonal references details.".$id,"update", "error", "gcchris", "user");
            }

            $resultSet["data"] = $post;

            return $resultSet;
        }

        function updateMedicalRecord($post) {
            $id = $post->id;
            $current_filename = $post->current_filename;

            unset($post->id, $post->current_filename);
            $resultSet = array();
            $this->db->trans_begin();
            $this->db->where("id", $id);
            $this->db->update($this->employeeMedicalHistoryTable, $post);

            $emp_id = $this->db->where("id", $id)->get($this->employeeMedicalHistoryTable)->row("emp_id");
            $uploadPath = './uploads/files/documents/employee_files/empcode_' . $emp_id . '/medical';
            $uploaded = null;

            if ($_FILES["files"]["name"]) {
                $current_file_path = $uploadPath . "/" . $current_filename;

                if (!file_exists(realpath($uploadPath))) {
                    mkdir($uploadPath, 0777, true);
                }

                if ($current_filename && file_exists(realpath($current_file_path))) {
                    unlink(realpath($current_file_path));
                }

                $config = array();
                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG|pdf|doc|docx';
                $config['max_size'] = 10000;
                $config['create_thumbnail'] = false;

                $uploaded = $this->file_upload->uploadFile($config);

                if ($uploaded['response']) {
                    $this->db->where("id", $id)
                        ->update($this->employeeMedicalHistoryTable, array("filename" => $uploaded['files'][0]['file_name']));
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $resultSet["success"] = "false";
                $resultSet["message"] = $this->db->error();
                $this->core_layout->setEventLog("Error updating medical historys details.".$id,"update", "error", "gcchris", "user");
                $this->db->trans_rollback();
            } else {
                $resultSet["success"] = "true";
                $resultSet["message"] = "Record was successfully updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Updated medical history details.".$id,"update", "success", "gcchris", "user");
                $this->db->trans_commit();
            }

            $resultSet["data"] = $post;
            return $resultSet;
        }
        /* END UPDATE FUNCTIONS */

        /* ARCHIVE FUNCTIONS*/
        function archiveDocument($document_id) {
            $resultSet = array();
            $this->db->where("id", $document_id);
            $query = $this->db->update($this->employeeDocumentsTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Document Archived.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived document details.".$document_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeDocumentsTable, $document_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->core_layout->setEventLog("Error archiving document details.".$document_id,"update", "error", "gcchris", "user");
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function archiveSkill($skill_id) {
            $resultSet = array();
            $this->db->where("id", $skill_id);
            $query = $this->db->update($this->employeeSkillsTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Skill Archived.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived skills details.".$skill_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeDocumentsTable, $skill_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->core_layout->setEventLog("Error archiving skills details.".$skill_id,"update", "error", "gcchris", "user");
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function archiveLegalRecord($legal_id) {
            $resultSet = array();
            $this->db->where("id", $legal_id);
            $query = $this->db->update($this->employeeLegalHistoryTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Legal Record Archived.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived legal record details.".$legal_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeLegalHistoryTable, $legal_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->core_layout->setEventLog("Error archiving legal record details.".$legal_id,"update", "error", "gcchris", "user");
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function archiveOffensesAndCommendations($offense_id) {
            $resultSet = array();
            $this->db->where("id", $offense_id);
            $query = $this->db->update($this->employeeOffensesTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Offenses & Commendation Archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived offenses and commendation details.".$offense_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeOffensesTable, $offense_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->core_layout->setEventLog("Error archiving offenses and commendation details.".$offense_id,"update", "error", "gcchris", "user");
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function archiveBgCheck($bg_id) {
            $resultSet = array();
            $this->db->where("id", $bg_id);
            $query = $this->db->update($this->employeeDocumentsTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Background Check Archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived background check details.".$bg_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeDocumentsTable, $bg_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->core_layout->setEventLog("Error archiving background check details.".$bg_id,"update", "error", "gcchris", "user");
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function archivePerformanceEvaluation($eval_id) {
            $resultSet = array();
            $this->db->where("id", $eval_id);
            $query = $this->db->update($this->employeePerformanceTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Performance Evaluation Archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived performance evaluation details.".$eval_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeePerformanceTable, $eval_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->core_layout->setEventLog("Error archiving performance evaluation details.".$eval_id,"update", "error", "gcchris", "user");
                $resultSet['title'] = "Error";
            }

            return $resultSet;
        }

        function archiveEmployeeCashAdvance($cash_advance_id) {
            $resultSet = array();
            $this->db->where("id", $cash_advance_id);
            $query = $this->db->update($this->employeeCashAdvanceTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Cash Advance Archived.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived cash advance details.".$cash_advance_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeCashAdvanceTable, $cash_advance_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving cash advance details.".$cash_advance_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function archiveDependent($dependent_id) {
            $resultSet = array();
            $this->db->where("id", $dependent_id);
            $query = $this->db->update($this->employeeDependentsTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Dependent Archived.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived dependents details.".$dependent_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeDependentsTable, $dependent_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving dependents details.".$dependent_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function archiveEducationalBackground($educ_id) {
            $resultSet = array();
            $this->db->where("id", $educ_id);
            $query = $this->db->update($this->employeeEducationTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "EducationalBackground Archived.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived educational background details.".$educ_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeEducationTable, $educ_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving educational background details.".$educ_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function archiveLicensure($licensure_id) {
            $resultSet = array();
            $this->db->where("id", $licensure_id);
            $query = $this->db->update($this->employeeLicensureTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Licensure Exams & Certificate Archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived licensure exam & certification details.".$licensure_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeLicensureTable, $licensure_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving licensure exam & certification details.".$licensure_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function archiveDriverLicense($driverlicense_id) {
            $resultSet = array();
            $this->db->where("id", $driverlicense_id);
            $query = $this->db->update($this->employeeDriverLicenseTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Driver's License Archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived driver's license details.".$driverlicense_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeDriverLicenseTable, $driverlicense_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving driver's license details.".$driverlicense_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function archiveWorkExperience($work_exp_id) {
            $resultSet = array();
            $this->db->where("id", $work_exp_id);
            $query = $this->db->update($this->employeeWorkExperienceTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Work Experience Archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived work experience details.".$work_exp_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeWorkExperienceTable, $work_exp_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving work experience details.".$work_exp_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function archiveAward($award_id) {
            $resultSet = array();
            $this->db->where("id", $award_id);
            $query = $this->db->update($this->employeeAwardsTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Award & Achievement archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived awards details.".$award_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeAwardsTable, $award_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving awards details.".$award_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function archiveOrganization($org_id) {
            $resultSet = array();
            $this->db->where("id", $org_id);
            $query = $this->db->update($this->employeeOrganizationTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Organization archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived organization details.".$org_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeOrganizationTable, $org_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving organization details.".$org_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function archiveTraining($training_id) {
            $resultSet = array();
            $this->db->where("id", $training_id);
            $query = $this->db->update($this->employeeTrainingsTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Training & Achievement archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived trainings details.".$training_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeTrainingsTable, $training_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving trainings details.".$training_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function archivePersonalReference($reference_id) {
            $resultSet = array();
            $this->db->where("id", $reference_id);
            $query = $this->db->update($this->employeeReferencesTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Personal references archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived personal reference details.".$reference_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeReferencesTable, $reference_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving personal reference details.".$reference_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function archiveMedicalRecord($med_id) {
            $resultSet = array();
            $this->db->where("id", $med_id);
            $query = $this->db->update($this->employeeMedicalHistoryTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Medical Record archive.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "Archived medical history details.".$med_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeMedicalHistoryTable, $med_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Error archiving medical history details.".$med_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }
        /* END ARCHIVE FUNCTIONS*/


        /* CALENDAR GETTERS */
        function getCalendarOfProbationaryEmployees() {
            $first_eval = $this->getFirstEvalCalendar();
            $second_eval = $this->getSecondEvalCalendar();
            $final_eval = $this->getFinalEvalCalendar();
            return array_merge($first_eval, $second_eval, $final_eval);
        }

        private function getFirstEvalCalendar() {
            $select = "'first_eval' evaluation_stage, emp.id emp_id, emp.lastname, emp.firstname, ";
            $select .= "calendar.id cal_id, '1st Evaluation' string_eval, ";
            $select .= "emp.middlename, emp.suffix, ";
            $select .= "UPPER(CONCAT('1st : ', emp.firstname, ' ', emp.middlename,' ',emp.lastname, ";
            $select .= "    CASE WHEN emp.suffix = 'NONE' THEN '' ";
            $select .= "        WHEN emp.suffix = 'N/A' THEN '' ";
            $select .= "        WHEN emp.suffix IS NULL THEN '' ";
            $select .= "        ELSE CONCAT(' ', emp.suffix) END )) title, ";
            $select .= "'1st Evaluation' description, ";
            $select .= "emp.date_start, emp.date_end_prob, DATE_ADD(emp.date_start, INTERVAL 3 MONTH) `start`, ";
            $select .= "CASE
                            WHEN calendar.`status`='discontinue' THEN '#F44336'
                            WHEN calendar.first_eval = 'pass' THEN '#4CAF50'
                            WHEN calendar.first_eval = 'fail' THEN '#F44336'
                            ELSE
                                IF(CURDATE() >= DATE_ADD(emp.date_start, INTERVAL 3 MONTH), '#FFC107', '#FFEB3B')
                        END backgroundColor,";
            $select .= "'#000000' textColor, ";
            $select .= "IF(DATE_ADD(emp.date_start, INTERVAL 3 MONTH) >= (CURDATE() - INTERVAL 3 DAY), '1', '0') clickable, ";
            $select .= "CASE
                            WHEN calendar.`status`='discontinue' THEN 'm-fc-event--light contrast'
                            WHEN calendar.first_eval = 'pass' THEN 'm-fc-event--light contrast'
                            WHEN calendar.first_eval = 'fail' THEN 'm-fc-event--light contrast'
                            ELSE
                                'm-fc-event--darker'
                        END className";

            $where = "emp.work_status = 'PROBATIONARY' AND emp.employee_status = 'Active' AND ";
            $where .= "DATE_ADD(emp.date_start, INTERVAL 3 MONTH) IS NOT NULL AND ";
            $where .= "(DATE_ADD(calendar.first_eval_date, INTERVAL 15 DAY) >= CURDATE() OR calendar.first_eval_date IS NULL)";

            $this->db->select($select, TRUE);
            $this->db->where($where, NULL, FALSE);
            $this->db->join($this->tblProbiCalendar . " calendar", "calendar.emp_id = emp.id", "LEFT");
            return $this->db->get($this->employeeTable . " emp")->result();
        }

        private function getSecondEvalCalendar() {
            $select = "'second_eval' evaluation_stage, emp.id emp_id, emp.lastname, emp.firstname, ";
            $select .= "calendar.id cal_id, '2nd Evaluation' string_eval, ";
            $select .= "emp.middlename, emp.suffix, ";
            $select .= "UPPER(CONCAT('2nd : ', emp.firstname, ' ', emp.middlename,' ',emp.lastname, ";
            $select .= "    CASE WHEN emp.suffix = 'NONE' THEN '' ";
            $select .= "        WHEN emp.suffix = 'N/A' THEN '' ";
            $select .= "        WHEN emp.suffix IS NULL THEN '' ";
            $select .= "        ELSE CONCAT(' ', emp.suffix) END )) title, ";
            $select .= "'Second Evaluation' description, ";
            $select .= "emp.date_start, emp.date_end_prob, DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) `start`, ";
            $select .= "CASE
                            WHEN calendar.`status`='discontinue' THEN '#F44336'
                            WHEN calendar.second_eval = 'pass' THEN '#4CAF50'
                            WHEN calendar.second_eval = 'fail' THEN '#F44336'
                            ELSE
                                IF(CURDATE() >= DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY), '#2962FF', '#03A9F4')
                        END backgroundColor,";
            $select .= "'#ffffff' textColor, ";
            $select .= "IF(DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) >= (CURDATE() - INTERVAL 3 DAY), '1', '0') clickable, ";
            $select .= "'m-fc-event--light contrast' className";

            $where = "emp.work_status = 'PROBATIONARY' AND emp.employee_status = 'Active' AND ";
            $where .= "DATE_ADD(DATE_ADD(emp.date_start, INTERVAL 4 MONTH), INTERVAL 15 DAY) IS NOT NULL AND";
            $where .= "(DATE_ADD(calendar.second_eval_date, INTERVAL 15 DAY) >= CURDATE() OR calendar.second_eval_date IS NULL)";

            $this->db->select($select, TRUE);
            $this->db->where($where, NULL, FALSE);
            $this->db->join($this->tblProbiCalendar . " calendar", "calendar.emp_id = emp.id", "LEFT");
            return $this->db->get($this->employeeTable . " emp")->result();
        }

        private function getFinalEvalCalendar() {
            $select = "'status' evaluation_stage, emp.id emp_id, emp.lastname, emp.firstname, ";
            $select .= "calendar.id cal_id, 'Final Evaluation' string_eval, ";
            $select .= "emp.middlename, emp.suffix, ";
            $select .= "UPPER(CONCAT('Final : ', emp.firstname, ' ', emp.middlename,' ',emp.lastname, ";
            $select .= "    CASE WHEN emp.suffix = 'NONE' THEN '' ";
            $select .= "        WHEN emp.suffix = 'N/A' THEN '' ";
            $select .= "        WHEN emp.suffix IS NULL THEN '' ";
            $select .= "        ELSE CONCAT(' ', emp.suffix) END )) title, ";
            $select .= "'Final Evaluation' description, ";
            $select .= "emp.date_start, emp.date_end_prob, DATE_ADD(emp.date_start, INTERVAL 5 MONTH) `start`, ";
            $select .= "CASE
                            WHEN (calendar.first_eval IS NULL OR calendar.first_eval = '') AND (calendar.second_eval OR calendar.second_eval = '') IS NULL THEN '#F44336'
                            WHEN calendar.status = 'discontinue' THEN '#F44336'
                            WHEN calendar.`status` = 'regularize' THEN '#4CAF50'
                            ELSE '#BDBDBD'
                        END backgroundColor,";
            $select .= "'#ffffff' textColor, ";
            // $select .= "DATE_ADD(emp.date_start, INTERVAL 3 DAY) >= CURDATE() clickable, ";
            $select .= "1 clickable, "; // always clickable
            $select .= "'m-fc-event--light contrast' className";

            $where = "emp.work_status = 'PROBATIONARY' AND emp.employee_status = 'Active' AND ";
            $where .= "(DATE_ADD(calendar.date_discontinued, INTERVAL 15 DAY) >= CURDATE() OR calendar.date_discontinued IS NULL) AND ";
            $where .= "DATE_ADD(emp.date_start, INTERVAL 5 MONTH) IS NOT NULL";

            $this->db->select($select, TRUE);
            $this->db->where($where, NULL, FALSE);
            $this->db->join($this->tblProbiCalendar . " calendar", "calendar.emp_id = emp.id", "LEFT");
            return $this->db->get($this->employeeTable . " emp")->result();
        }

        /* END CALENDAR GETTERS */

        function addProbeeEvaluation() {
            $resultSet = array();
            $data = array();
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $data["emp_id"] = $post->emp_id;
            $data[$post->field] = $post->status;
            switch ($post->field) {
                case "first_eval":
                    $data['first_eval_date'] = $this->now->format('Y-m-d');
                    break;
                case "second_eval":
                    $data['second_eval_date'] = $this->now->format('Y-m-d');
                    break;
                default:
                    $data['date_discontinued'] = $this->now->format('Y-m-d');
                    break;
            }

            $query = null;

            if (empty($post->probee_cal_id)) {
                $query = $this->db->insert($this->tblProbiCalendar, $data);
                $this->addEventLog("success","Archived document entry.",$this->tblProbiCalendar,$this->db->insert_id());
                /*** $this->addEventLog("success","Archived document entry.",$this->tblProbiCalendar,$this->db->inser_id()); original code for updating probationary ***/ 
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "added probitionary evaluation ".$this->db->insert_id(),"insert", "success", "gcchris", "user");
            } else {
                $this->db->where("id", $post->probee_cal_id);
                $query = $this->db->update($this->tblProbiCalendar, $data);
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "updated probitionary evaluation ".$post->probee_cal_id,"insert", "success", "gcchris", "user");
            }

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Employee evaluation saved.";
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $this->core_layout->setEventLog($this->db->error(),"insert", "error", "gcchris", "user");
                
            }

            return $resultSet;
        }

        public function searchEmployee($searchKey, $filter) { 
            if($searchKey){ $searchKey = trim($searchKey); }

            if(isset($filter)){
                if(in_array("skills", $filter)){
                    $search_filter = ",CONCAT(skills.skills) skills";
                }else{
                    $search_filter = "";
                }
            }else{
                $search_filter = "";
            }
            $select = "emp.id,
                       UCASE(
                           CONCAT(
                               emp.firstname, ' ',
                               CASE
                                   WHEN emp.middlename IS NOT NULL AND emp.middlename != '' THEN CONCAT(' ', substr(emp.middlename,1,1),'.')
                                   ELSE ''
                               END,
                               ' ', emp.lastname,
                               CASE
                                   WHEN emp.suffix IS NOT NULL AND emp.suffix != '' AND emp.suffix != 'N/A' AND emp.suffix != 'NONE' THEN CONCAT(' ', emp.suffix)
                                   ELSE ''
                               END
                           )
                       ) employee_name,
                       IF(companies.id IS NULL, emp.company_id, companies.code)company,
                       IF(departments.id IS NULL, emp.department_id, departments.code) department,
                       IF(positions.id IS NULL, emp.`position`, positions.name) `position`,
                       emp.pic_filename image $search_filter";

            $this->db->select($select);
            $this->db->join($this->companyTable . ' companies', 'companies.id = emp.company_id', 'LEFT');
            $this->db->join($this->departmentTable . ' departments', 'departments.id = emp.department_id', 'LEFT');
            $this->db->join($this->positionTable . ' positions', 'positions.id = emp.`position`', 'LEFT');
            if(isset($filter) && in_array("skills",$filter)){
                 $this->db->join($this->employeeSkillsTable . ' skills', 'skills.emp_id = emp.id', 'LEFT');
            }

            if(isset($filter) && in_array("education",$filter)){
                $this->db->join($this->employeeEducationTable . ' educ', 'educ.emp_id = emp.id', 'LEFT');
            }
            $this->db->like("CASE
                                WHEN emp.middlename IS NULL OR emp.middlename = '' OR emp.middlename = 'NONE' OR emp.middlename = 'N/A' THEN
                                    CONCAT(emp.firstname, ' ',emp.lastname)
                                ELSE
                                    CONCAT(emp.firstname, ' ', emp.middlename,' ' ,emp.lastname) END", $searchKey, 'both');
            $this->db->or_like('IF (companies . id IS NULL, emp . company_id, companies . code)', $searchKey, 'both');
            $this->db->or_like('IF (positions . id IS NULL, emp . `position`, positions . name)', $searchKey, 'both');
            $this->db->or_like('emp.lastname', $searchKey, 'both');
            $this->db->or_like('emp.middlename', $searchKey, 'both');
            $this->db->or_like('emp.firstname', $searchKey, 'both');
            $this->db->or_like("CONCAT(emp.firstname, ' ' ,emp.lastname)", $searchKey, 'both');
            if(isset($filter) && in_array("skills",$filter)){
                $this->db->or_like("skills.skills", $searchKey, 'both');
            }

            if(isset($filter) && in_array("education",$filter)){
                $this->db->or_like("educ.educ_degree", $searchKey, 'both');
            }

            $this->db->order_by("CASE
                WHEN emp.middlename IS NULL OR emp.middlename = '' OR emp.middlename = 'NONE' OR emp.middlename = 'N/A' THEN
                    CONCAT(emp.firstname, ' ',emp.lastname)
                ELSE
                    CONCAT(emp.firstname, ' ', emp.middlename,' ' ,emp.lastname)
                END ASC");

            $this->db->group_by('emp.id');
            $query = $this->db->get($this->employeeTable . ' emp');
            $employees = $query->result();

            $data = array();
            foreach ($employees as $employee) {
                $tempEmployeeData = $this->core_layout->getEmployeeData($employee->id);
                $tempEmployeeData = (object) $tempEmployeeData;
                $tempEmployeeName = (isset($tempEmployeeData->display_name_1) && $tempEmployeeData->display_name_1)? $tempEmployeeData->display_name_1: null;
                if($tempEmployeeName !== null && $tempEmployeeName !== $employee->employee_name){ $employee->employee_name = $tempEmployeeName; }
                $image_path = "uploads/files/images/employee_files/empcode_" . $employee->id . "/" . $employee->image;
                if (file_exists(realpath($image_path))) {
                    $employee->image = base_url($image_path);
                } else {
                    $employee->image = base_url("assets/images/profile/no_image.jpg");
                }

                array_push($data, $employee);
            }
            $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has searched ".$searchKey." using dashboard employee search.","search", "success", "gcchris", "user");
            return $data;
        }

        public function deleteEmployee($emp_id) {
            $resultSet = array();
            $user = $this->core_layout->getUserLoggedIn();

            $this->db->where("id", $emp_id);
            if ($this->db->delete($this->employeeTable)) {
                $this->db->insert("gccmaster.archived_items",
                    array(
                        "archived_table" => "gccmaster.tblemployees",
                        "archived_id" => $emp_id,
                        "archived_by" => $user["employee_id"],
                        "archived_at" => date('Y - m - d H:i:s'),
                        "status" => 3
                    )
                );

                $resultSet["success"] = true;
                $resultSet["message"] = "Employee permanently deleted.";
                $resultSet["title"] = "Employee deleted.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername." has archived employee id no.".$emp_id,"update", "success", "gcchris", "user");
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
                $resultSet["title"] = "Error.";
                $this->addEventLog("error",$this->db->error(),$this->employeeTable,$emp_id);
                $this->core_layout->setEventLog("User ".$this->loggedInUsername." has archived employee id no.".$emp_id,"update", "success", "gcchris", "user");
            }

            return $resultSet;
        }

        /* ARCHIVE FUNCTION */
        public function logArchive($archived_table, $archived_id, $status) {
            $user = $this->core_layout->getUserLoggedIn();
            $employee_id = $user['employee_id'];
            $archived_data = array(
                "archived_table" => $archived_table,
                "archived_id" => $archived_id,
                "archived_by" => $employee_id,
                "archived_at" => date('Y - m - d H:i:s'),
                "status" => $status);
            $this->db->insert($this->tblArchivedItems, $archived_data);
        }

        /* END ARCHIVE FUNCTION */

        function changeEmployeeCompany() {
            $this->db->trans_begin();
            $resultSet = array();

            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $user = $this->core_layout->getUserLoggedIn();
            $employee_id = $user['employee_id'];
            $start_date = date("Y-m-d", strtotime($post->start_date));
            $previous_date = date("Y-m-d", strtotime("-1 day", strtotime($start_date)));
            $previous_date_year = date('Y', strtotime($previous_date));

            $id = $post->emp_id;

            $company_history_latest = $this->db->where("emp_id", $id)
                ->order_by("created_at", "desc")
                ->limit(1)
                ->get($this->tblEmployeesCompanyHistory)->row("date_started");

            /* SAVE WORK EXPERIENCE */
            $work_experience_field = array(
                "emp_id" => $id,
                "work_from" => $company_history_latest ? date('Y', strtotime($company_history_latest)) : $post->work_from,
                "work_to" => $previous_date_year,
                "work_company" => $post->current_company,
                "work_position" => $post->current_position,
                "work_status" => $post->current_status,
                "work_reason" => "TRANSFER COMPANY",
                "add_date" => date('Y - m - d H:i:s'),
                "add_by" => $employee_id
            );
            $this->db->insert($this->employeeWorkExperienceTable, $work_experience_field);
            /* END SAVE WORK EXPERIENCE */

            /* UPDATE COMPANY & OTHERS */
            $update_data = array(
                "company_id" => $post->company_id,
                "department_id" => $post->department_id,
                "position" => $post->position
            );
            $this->db->where("id", $id);
            $this->db->update($this->employeeTable, $update_data);

            $this->db->reset_query();

            $history_data = array(
                "emp_id" => $id,
                "company_id" => $post->company_id,
                "created_by" => $employee_id,
                "date_started" => $start_date
            );
            $this->db->insert($this->tblEmployeesCompanyHistory, $history_data);
            /* END UPDATE COMPANY & OTHERS */

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error();
                $resultSet["data"] = null;
            } else {
                $this->db->trans_commit();
                $resultSet["success"] = true;
                $resultSet["message"] = "Employee was transferred successfully.";
                $resultSet["data"] = $this->getEmployeeData($id);
            }

            return $resultSet;
        }

        public function removeProfilePicture($employee_id) {
            $resultSet = array(
                "success" => false,
                "message" => "Failed in removing employee picture.",
                "data" => null,
                "title" => "Remove Picture Failed."
            );
            $no_image = "./assets/images/profile/no_image.jpg";
            $employee_images_folder = "./uploads/files/images/employee_files/empcode_" . $employee_id;
            $_pic_filename = $this->db->get_where($this->employeeTable, array("id" => $employee_id))->row("pic_filename");
            $pic_filename_arr = explode("/", $_pic_filename);
            $pic_filename = array_pop($pic_filename_arr);

            // check image then remove
            if (file_exists(realpath($employee_images_folder . "/" . $pic_filename))) {
                unlink(realpath($employee_images_folder . "/" . $pic_filename));
            }

            // check thumbnails then remove
            if (file_exists(realpath($employee_images_folder . "/thumbnails/" . $pic_filename))) {
                unlink($employee_images_folder . "/thumbnails/" . $pic_filename);
            }

            if (!file_exists(realpath($employee_images_folder))) {
                mkdir($employee_images_folder, 0777, true);
            }

            $defaultPicture = $employee_images_folder . "/no_image.jpg";
            $reset = copy($no_image, $defaultPicture);

            if ($reset) {
                $this->db->where("id", $employee_id);
                $this->db->set("pic_filename", "empcode_" . $employee_id . "/no_image.jpg");
                $this->db->update($this->employeeTable);

                $resultSet["success"] = true;
                $resultSet["message"] = "Employee picture was successfully removed.";
                $resultSet["data"] = array("image" => base_url(str_replace("./", "", $defaultPicture)));
                $resultSet["title"] = "Successfully Removed.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "removed profile picture.".$employee_id,"update", "success", "gcchris", "user");
            }

            return $resultSet;
        }

        public function relocateProfilePictures($employee_status) {
            ini_set('max_execution_time', 60000);
            set_time_limit(60000);
            $employee_status = str_replace("%20", " ", $employee_status);
            if (empty($employee_status)) {
                $this->db->where("employee_status !=", "Active");
            } else {
                $this->db->where("employee_status", $employee_status);
            }
            $employees = $this->db
                ->order_by('lastname ASC, firstname ASC')
                ->get($this->employeeTable)
                ->result();

            $this->db->reset_query();

            /* COPY PROFILE PICTURE */
            $live_profile_picture_path = "http://bcd.gccph.com/hris/uploads";
            $employee_profile_picture_path = "uploads/files/images/employee_files";
            foreach ($employees as $employee) {
                $emp_id = $employee->id;
                $dir_path = $employee_profile_picture_path . "/empcode_" . $emp_id;
                $filepath = $employee_profile_picture_path . "/empcode_" . $emp_id . "/" . $employee->pic_filename;

                $thumbnail_path = $employee_profile_picture_path . "/empcode_" . $emp_id . "/thumbnails";

                $exist = file_exists(realpath($dir_path));

                if (!$exist) {
                    mkdir($dir_path, 0777, true);
                }

                if (!file_exists(realpath($thumbnail_path))) {
                    mkdir($thumbnail_path, 0777, true);
                }

                if (!file_exists(realpath($filepath))) {
                    $remote_file_contents = $this->get_contents($live_profile_picture_path . "/" . $employee->pic_filename);
                    if (!empty($remote_file_contents)) {
                        if (file_put_contents($filepath, $remote_file_contents)) {
                            $this->saveThumbnail($filepath, $thumbnail_path . "/" . $employee->pic_filename);
                        }
                    }
                }
            }
            /* END COPY PROFILE PICTURE */
        }

        public function relocateDocumentFiles() {
            ini_set('max_execution_time', 600000);
            set_time_limit(600000);

            $this->db->where("employee_status !=", "Active");
            $employees = $this->db
                ->order_by('lastname ASC, firstname ASC')
                ->get($this->employeeTable)
                ->result();

            $this->db->reset_query();

            $live_document_path = "http://bcd.gccph.com/hris/server/php/files";
            foreach ($employees as $employee) {
                $id = $employee->id;
                $documents = $this->db->where("emp_id", $id)->get($this->employeeDocumentsTable)->result();
                foreach ($documents as $document) {
                    $dir_path = "uploads/files/documents/employee_files/empcode_" . $id . "/documents";
                    $doc_filename = $document->doc_filename;

                    $exist = file_exists(realpath($dir_path));

                    if (!$exist) {
                        mkdir($dir_path, 0777, true);
                    }

                    if (!file_exists(realpath($dir_path . "/" . $doc_filename))) {
                        $remote_file_contents = $this->get_contents($live_document_path . "/" . $doc_filename);

                        if (!empty($remote_file_contents)) {
                            file_put_contents($dir_path . "/" . $doc_filename, $remote_file_contents);
                        }
                    }
                }
            }
        }

        public function relocateOffensesFiles($employee_id) {
            ini_set('max_execution_time', 60000);
            set_time_limit(60000);

            if (!empty($employee_id)) {
                $this->db->where("id", $employee_id);
            } else {
                $this->db->where("employee_status !=", "Active");
            }
            $employees = $this->db
                ->order_by('lastname ASC, firstname ASC')
                ->get($this->employeeTable)
                ->result();

            $this->db->reset_query();

            $live_document_path = "http://bcd.gccph.com/hris/server/php/files";
            foreach ($employees as $employee) {
                $id = $employee->id;
                $offenses = $this->db
                    ->where("emp_id", $id)
                    ->where("filename IS NOT NULL")
                    ->where("filename !=", '')
                    ->get($this->employeeOffensesTable)
                    ->result();

                foreach ($offenses as $offense) {
                    $dir_path = "uploads/files/documents/employee_files/empcode_" . $id . "/offenses_commendation";
                    $filename = $offense->filename;

                    $exist = file_exists(realpath($dir_path));

                    if (!$exist) {
                        mkdir($dir_path, 0777, true);
                    }

                    if (!file_exists(realpath($dir_path . "/" . $filename))) {
                        $remote_file_contents = $this->get_contents($live_document_path . "/" . $filename);

                        if (!empty($remote_file_contents)) {
                            file_put_contents($dir_path . "/" . $filename, $remote_file_contents);
                        }
                    }
                }
            }
        }

        public function relocatePerformanceEvalFiles() {
            ini_set('max_execution_time', 60000);
            set_time_limit(60000);

            $this->db->where("employee_status !=", "Active");
            $employees = $this->db
                ->order_by('lastname ASC, firstname ASC')
                ->get($this->employeeTable)
                ->result();

            $this->db->reset_query();

            $live_document_path = "http://bcd.gccph.com/hris/server/php/files";
            foreach ($employees as $employee) {
                $id = $employee->id;
                $list = $this->db
                    ->where("emp_id", $id)
                    ->where("filename IS NOT NULL")
                    ->where("filename !=", '')
                    ->get($this->employeePerformanceTable)
                    ->result();

                foreach ($list as $row) {
                    $dir_path = "uploads/files/documents/employee_files/empcode_" . $id . "/performance_eval";
                    $filename = $row->filename;

                    $exist = file_exists(realpath($dir_path));

                    if (!$exist) {
                        mkdir($dir_path, 0777, true);
                    }

                    if (!file_exists(realpath($dir_path . "/" . $filename))) {
                        $remote_file_contents = $this->get_contents($live_document_path . "/" . $filename);

                        if (!empty($remote_file_contents)) {
                            file_put_contents($dir_path . "/" . $filename, $remote_file_contents);
                        }
                    }
                }
            }
        }

        public function relocateTrainingFiles() {
            ini_set('max_execution_time', 60000);
            set_time_limit(60000);

            $this->db->where("employee_status !=", "Active");
            $employees = $this->db
                ->order_by('lastname ASC, firstname ASC')
                ->get($this->employeeTable)
                ->result();

            $this->db->reset_query();

            $live_document_path = "http://bcd.gccph.com/hris/uploads/trainings";
            foreach ($employees as $employee) {
                $id = $employee->id;
                $list = $this->db
                    ->where("emp_id", $id)
                    ->where("attachment IS NOT NULL")
                    ->where("attachment !=", '')
                    ->get($this->employeeTrainingsTable)
                    ->result();

                foreach ($list as $row) {
                    $dir_path = "uploads/files/documents/employee_files/empcode_" . $id . "/trainings";
                    $filename = $row->attachment;

                    $exist = file_exists(realpath($dir_path));

                    if (!$exist) {
                        mkdir($dir_path, 0777, true);
                    }

                    if (!file_exists(realpath($dir_path . "/" . $filename))) {
                        $remote_file_contents = $this->get_contents($live_document_path . "/" . $filename);

                        if (!empty($remote_file_contents)) {
                            file_put_contents($dir_path . "/" . $filename, $remote_file_contents);
                        }
                    }
                }
            }
        }

        public function relocateMedicalRecordFiles() {
            ini_set('max_execution_time', 60000);
            set_time_limit(60000);

            $this->db->where("employee_status !=", "Active");
            $employees = $this->db
                ->order_by('lastname ASC, firstname ASC')
                ->get($this->employeeTable)
                ->result();

            $this->db->reset_query();

            $live_document_path = "http://bcd.gccph.com/hris/uploads/medical/files";
            foreach ($employees as $employee) {
                $id = $employee->id;
                $list = $this->db
                    ->where("emp_id", $id)
                    ->where("filename IS NOT NULL")
                    ->where("filename !=", '')
                    ->get($this->employeeMedicalHistoryTable)
                    ->result();

                foreach ($list as $row) {
                    $dir_path = "uploads/files/documents/employee_files/empcode_" . $id . "/medical";
                    $filename = $row->filename;

                    $exist = file_exists(realpath($dir_path));

                    if (!$exist) {
                        mkdir($dir_path, 0777, true);
                    }

                    if (!file_exists(realpath($dir_path . "/" . $filename))) {
                        $remote_file_contents = $this->get_contents($live_document_path . "/" . $filename);

                        if (!empty($remote_file_contents)) {
                            file_put_contents($dir_path . "/" . $filename, $remote_file_contents);
                        }
                    }
                }
            }
        }

        function get_contents($url, $u = false, $c = null, $o = null) {
            $headers = get_headers($url);
            $status = substr($headers[0], 9, 3);
            if ($status == '200') {
                return file_get_contents($url, $u, $c, $o);
            }
            return null;
        }

        function saveThumbnail($source, $target) {
            $config = array(
                'image_library' => 'gd2',
                'source_image' => $source,
                'new_image' => $target,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '',
                'width' => 75,
                'height' => 75
            );
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            $this->image_lib->clear();
        }

        function checkIf201StatusIsComplete($employee_id) {
            $where = "biometricno != '' AND biometricno IS NOT NULL AND idno !='' AND idno IS NOT NULL AND ";
            $where .= "tin_no != '' AND tin_no IS NOT NULL AND tax_status != '' AND tax_status IS NOT NULL AND ";
            $where .= "phealth_no != '' AND phealth_no IS NOT NULL AND pagibig_no != '' AND pagibig_no IS NOT NULL AND ";
            $where .= "sss_no != '' AND sss_no IS NOT NULL AND emer_name != '' AND emer_name IS NOT NULL AND ";
            $where .= "emer_addr != '' AND emer_addr IS NOT NULL AND emer_contact != '' AND emer_contact IS NOT NULL AND ";
            $where .= "company_id != '' AND company_id IS NOT NULL AND department_id != '' AND department_id IS NOT NULL AND ";
            $where .= "position != '' AND position IS NOT NULL AND work_status != '' AND work_status IS NOT NULL AND ";
            $where .= "work_mode != '' AND work_mode IS NOT NULL AND payroll_type != '' AND payroll_type IS NOT NULL AND ";
            $where .= "level != '' AND level IS NOT NULL AND date_start AND date_start IS NOT NULL AND ";
            /*** $where .= "level != '' AND level IS NOT NULL AND date_start AND date_start != '' IS NOT NULL AND "; -> original where clause (error in date_start != '') and changed to date_start ***/  
            $where .= "id=$employee_id";

            $this->db->where($where);
            $list = $this->db->get("gccmaster.tblemployees")->row();
            $is_incomplete = !empty($list) ? 0 : 1;

            $this->db->where("id", $employee_id);
            $this->db->set("is_incomplete", $is_incomplete);
            $this->db->update("gccmaster.tblemployees");
            $this->db->reset_query();
        }

        function addEmployeeSkill() {
            $user = $this->core_layout->getUserLoggedIn();
            $post = $this->input->post();
            $post["skills"] = strtoupper($post["skills"]);
            $post["add_date"] = $this->now->format('Y-m-d H:i:s');
            $post["add_by"] = $user["employee_id"];

            $query = $this->db->insert($this->employeeSkillsTable, $post);

            $resultSet = array();
            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "New skill was successfully saved.";
                $resultSet['title'] = "Add Skill Successful.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "added skill details.".$this->db->insert_id(),"insert", "success", "gcchris", "user");
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Employee skills - Error adding new skill.","insert", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function edtiEmployeeSkill() {
            $user = $this->core_layout->getUserLoggedIn();
            $post = $this->input->post();
            $post["skills"] = strtoupper($post["skills"]);
            $post["update_date"] = $this->now->format('Y-m-d H:i:s');
            $post["update_by"] = $user["employee_id"];

            $id = $post["id"];
            unset($post["id"]);
            $this->db->where("id", $id);
            $query = $this->db->update($this->employeeSkillsTable, $post);

            $resultSet = array();
            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Skill was successfully updated.";
                $resultSet['title'] = "Update Successful.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. "updated skill details.".$id,"update", "success", "gcchris", "user");
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("Employee skills - Error updating skill.","update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function getEmployeeSkills() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("id", "skills");
                $dir = "DESC";
                $order = "id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->employeeSkillsTable);
                $dtTable->setParameterFields($columns);

                $parameters = array();
                $parameters["emp_id"] = $post["emp_id"];
                $parameters["is_archived"] = 0;

                $dtTable->setWhereParameters($parameters);

                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                return array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $posts
                );
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
        }

        function getEmployeeSalaryHistory() {
            $post = $this->input->post();
            $emp_id = $post["emp_id"];

            if ($post) {
                $columns = array("id", "sal_date", "sal_rate");
                $dir = "DESC";
                $order = "sal_date";

                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }

                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";

                $this->db->select("sal.id, sal.emp_id, sal.sal_rate,
                                   sal.sal_date, IF(pos.id IS NULL, sal.sal_position, pos.`name`) position,
                                   sal.sal_position, sal.sal_remarks, pos.id position_id");
                $this->db->where("sal.emp_id", $emp_id);
                $this->db->where("sal.is_archived", 0);

                $this->db->join($this->positionTable . " pos", "sal.sal_position=pos.id", "LEFT");

                $this->db->like("CONCAT(sal.id, sal.emp_id,
                                        sal.sal_rate, sal.sal_date,
                                        IF(pos.id IS NULL, sal.sal_position, pos.`name`),
                                        sal.sal_position, sal.sal_remarks)", $searchValue, "BOTH");
                $this->db->order_by($order, $dir);
                $this->db->limit($limit, $start);

                $q = $this->db->get($this->employeeSalaryTable . " sal");

                return array(
                    "data" => $q->result(),
                    "recordsFiltered" => $this->totalEmployeeSalaryHistoryCount($searchValue, $emp_id),
                    "recordsTotal" => $this->totalEmployeeSalaryHistoryCount($searchValue, $emp_id),
                    "draw" => $draw
                );
            }
        }

        private function totalEmployeeSalaryHistoryCount($searchValue, $emp_id) {
            $this->db->where("sal.emp_id", $emp_id);
            $this->db->where("sal.is_archived", 0);

            $this->db->like("CONCAT(sal.id, sal.emp_id,
                                        sal.sal_rate, sal.sal_date,
                                        IF(pos.id IS NULL, sal.sal_position, pos.`name`),
                                        sal.sal_position, sal.sal_remarks)", $searchValue, "BOTH");

            $this->db->join($this->positionTable . " pos", "sal.sal_position=pos.id", "LEFT");
            return $this->db->count_all_results($this->employeeSalaryTable . " sal");
        }

        function getEmployeeAccountability($dataTable = 1, $_emp_id = null) {
            $post = $this->input->post();
            $emp_id = ((int)$dataTable === 1) ? $post["emp_id"] : $_emp_id;

            $columns = array("id", "sal_date", "sal_rate");
            $dir = "DESC";
            $order = "acct_body.id";

            if (isset($post["order"]) && $post["order"]) {
                $dir = $post["order"][0]["dir"];
                $order = $columns[$post["order"][0]["column"]];
            }

            $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
            $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
            $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";

            $this->db->select("`acct_body`.asset_id,
                               `acct`.`reference_no`, `acct_body`.`asset_code`,
                               `acct_body`.`description`, `acct_body`.`amount`, `acct_body`.`is_returned`,`acct_body`.`remarks_returned`,
                               `acct_body`.`date_returned`, `acct`.`status`,
                               `acct_body`.`type`,
                               CASE
                                  WHEN assets.name IS NULL THEN vehicles.name
                                  WHEN vehicles.name IS NULL THEN assets.name
                               END aname", FALSE);

            $this->db->where("acct.issued_to", $emp_id);
            $this->db->where("acct.status", "Released");

            if(isset($post['status']) && $post['status'] <= 1){
                $this->db->where('acct_body.is_returned', $post['status']);
            }

            $this->db->join("gcceforms.accountability_body acct_body", "acct_body.accountability_id = acct.id", "INNER");
            $this->db->join("gccasset.assets assets", "assets.id = acct_body.asset_id AND `acct_body`.`type` = 'Asset'", "LEFT");
            $this->db->join("gccasset.vehicles vehicles", "vehicles.id = acct_body.asset_id AND `acct_body`.`type` = 'Vehicle'", "LEFT");
            $this->db->like("CONCAT(acct.reference_no, acct_body.asset_code, acct_body.description, acct_body.amount, CASE
                                  WHEN assets.name IS NULL THEN vehicles.name
                                  WHEN vehicles.name IS NULL THEN assets.name
                               END)", $searchValue, "BOTH");


            if ((int)$dataTable === 1) {
                if ((int)$limit > 0) {
                    $this->db->limit($limit, $start);
                }

                $this->db->order_by($order, $dir);
                $q = $this->db->get("gcceforms.accountability acct");

                return array(
                    "data" => $q->result(),
                    // "sql" => $this->db->last_query(),
                    "recordsFiltered" => $this->totalEmployeeAccountabilityCount($searchValue, $emp_id),
                    "recordsTotal" => $this->totalEmployeeAccountabilityCount($searchValue, $emp_id),
                    "draw" => $draw
                );
            } else {
                return $this->db->get("gcceforms.accountability acct")->result();
            }
        }

        private function totalEmployeeAccountabilityCount($searchValue = "", $emp_id) {
            $this->db->where("acct.issued_to", $emp_id);
            $this->db->join("gcceforms.accountability_body acct_body", "acct_body.accountability_id = acct.id", "INNER");
            return $this->db->count_all_results("gcceforms.accountability acct");
        }

        function addSalaryHistory() {
            $user = $this->core_layout->getUserLoggedIn();
            $post = $this->input->post();

            $post["sal_date"] = date("Y-m-d", strtotime($post["sal_date"]));
            $post["sal_rate"] = str_replace(",", "", $post["sal_rate"]);
            $post["add_date"] = date("Y-m-d H:i:s");
            $post["add_by"] = $user["employee_id"];

            return $this->db->insert($this->employeeSalaryTable, $post);
        }

        function editSalaryHistory() {
            $user = $this->core_layout->getUserLoggedIn();
            $post = $this->input->post();

            $id = $post["id"];
            unset($post["id"]);
            $post["sal_date"] = date("Y-m-d", strtotime($post["sal_date"]));
            $post["sal_rate"] = str_replace(",", "", $post["sal_rate"]);
            $post["update_date"] = date("Y-m-d H:i:s");
            $post["update_by"] = $user["employee_id"];

            $this->db->where("id", $id);
            return $this->db->update($this->employeeSalaryTable, $post);
        }

        function archiveSalaryHistory($salary_id) {
            $resultSet = array();
            $this->db->where("id", $salary_id);
            $query = $this->db->update($this->employeeSalaryTable, array("is_archived" => 1));

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Record was successfully archived.";
                $resultSet['title'] = "Salary History Archived.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " archived salary history record with db id no. ".$salary_id,"update", "success", "gcchris", "user");
                $this->logArchive($this->employeeSalaryTable, $salary_id, 1);
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed salary history record with db id no. ".$salary_id,"update", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function getPerformanceRating($emp_id = null) {
            $where = array("emp_id" => $emp_id, "current" => 1);
            $this->db->select("prating.*, scale.description, emp.lastname, emp.firstname, emp.middlename, emp.suffix");
            $this->db->join("gcchris.performance_rating_scale scale", "prating.rating = scale.value", "INNER");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = prating.emp_id", "LEFT");
            $data = $this->db->order_by("prating.id","asc")->get_where("gcchris.tblperformance_rating prating", $where)->row();
            if (!empty($data)) {
                $data->fullname = $this->core_layout->getDisplayName(array(
                        "lastname" => $data->lastname,
                        "firstname" => $data->firstname,
                        "middlename" => $data->middlename,
                        "suffix" => $data->suffix)
                )['display_name_1'];
            }
            return $data;
        }

        function getPerformanceRatingScale() {
            return $this->db->get("gcchris.performance_rating_scale scale")->result();
        }

        function addEmployeePerformanceRating() {
            $this->db->db_debug = false;
            $post = $this->input->post();


            $post["current"] = 1;
            $post["created_by"] = $this->session->userdata('logged_in')["emp_id"];
            $post["created_at"] = $this->now->format("Y-m-d H:i:s");

            $emp_data = $this->db->get_where($this->employeeTable, array("id"=>$post['emp_id']))->row_array();

            if($emp_data['employee_status'] == "Active"){

            $this->db->update("gcchris.tblperformance_rating", array("current" => 0), array("emp_id" => $post["emp_id"]));
            $this->db->reset_query();
            }
            $resultSet = array();
            if($post['purpose'] == "1"){
                $empRating_array = array(
                    "emp_id" => $post['emp_id'],
                    "rating" => $post['rating'],
                    "remarks" => $post['remarks'],
                    "current" => $post['current'],
                    "for_rehire" => $post['for_rehire'],
                    "created_by" => $post['created_by'],
                    "created_at" => $post['created_at'],
                );
                $_SESSION['performance_rating_temp'] = $empRating_array;

                $resultSet["success"] = true;
                $resultSet["message"] = "Performance rating temporarily saved!";
                $resultSet["title"] = "Saved";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " temporarily updated performance rating details of employee db id #".$post["emp_id"],"update", "success", "gcchris", "user");
            }else{
                unset($post['purpose'],$post['for_rehire']);
                if ($this->db->insert("gcchris.tblperformance_rating", $post)) {
                    $resultSet["success"] = true;
                    $resultSet["message"] = "Performance rating was successfully saved.";
                    $resultSet["title"] = "Performance Rating Saved.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated performance rating details of employee db id #".$this->db->insert_id(),"update", "success", "gcchris", "user");
                }else{
                    $resultSet["success"] = false;
                    $resultSet["message"] = $this->db->error()["message"];
                    $resultSet["title"] = "DB Error";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed updating performance rating details.","update", "success", "gcchris", "user");
                   
                }
            }


            return $resultSet;
        }

        function updateEmployeePerformanceRating() {
            $this->db->db_debug = false;
            $post = $this->input->post();
            $post["last_updated_by"] = $this->session->userdata('logged_in')["emp_id"];
            $post["last_updated_at"] = $this->now->format("Y-m-d H:i:s");
            $resultSet = array();
            $id = $post["id"];
            unset($post["id"], $post["current"]);

            $this->db->where("id", $id);
            if ($this->db->update("gcchris.tblperformance_rating", $post)) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Performance rating was successfully updated.";
                $resultSet["title"] = "Performance Rating Updated.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated performance rating details of employee db id #".$id,"update", "success", "gcchris", "user");
            } else {
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "DB Error";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed updating performance rating details of employee db id #".$id,"update", "success", "gcchris", "user");
            }

            return $resultSet;
        }

        function getEmployeePerformanceRating() {
            $post = $this->input->post();

            $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
            $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
            $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";

            if ((int)$limit > 0) {
                $this->db->limit($limit, $start);
            }

            $this->db->order_by("current", "desc");

            $searchFields = "CONCAT(rating, IFNULL(remarks, ''))";
            $this->db->like($searchFields, $searchValue, "BOTH");

            $emp_id = $post["emp_id"];
            $this->db->select("prating.*, scale.description");
            $where = array("emp_id" => $emp_id);
            $this->db->join("gcchris.performance_rating_scale scale", "prating.rating = scale.value", "INNER");
            $query = $this->db->get_where("gcchris.tblperformance_rating prating", $where)->result();
            $data = array();

            foreach ($query as $row) {
                $row->rating = "<div class='emp-performance-rating' id='row-rating-" . $row->id . "' data-value='" . $row->rating . "'>
                                                 </div><div class='mt-2 m--regular-font-size-sm1 m--font-boldest text-muted'>$row->description</div>";
                array_push($data, $row);
            }

            return array(
                "data" => $data,
                "draw" => $draw,
                "recordsFiltered" => $this->getEmployeePerformanceRatingCount($searchFields, $searchValue, $post),
                "recordsTotal" => $this->getEmployeePerformanceRatingCount($searchFields, $searchValue, $post)
            );
        }

        private function getEmployeePerformanceRatingCount($searchFields, $searchValue, $post) {
            $this->db->select("prating.*, scale.description");
            $where = array("emp_id" => $post["emp_id"]);
            $this->db->where($where);
            $this->db->join("gcchris.performance_rating_scale scale", "prating.rating = scale.value", "INNER");
            return $this->db->count_all_results("gcchris.tblperformance_rating prating");
        }

        function getPerformanceRatingForUpdate($id) {
            $this->db->select("prating.*, scale.description");
            $where = array("id" => $id);
            $this->db->join("gcchris.performance_rating_scale scale", "prating.rating = scale.value", "INNER");
            return $this->db->get_where("gcchris.tblperformance_rating prating", $where)->row();
        }

        function deletePerformanceRating($id) {
            $resultSet = array();
            $this->db->where("id", $id);
            $query = $this->db->delete("gcchris.tblperformance_rating");

            if ($query) {
                $resultSet['success'] = true;
                $resultSet['message'] = "Performance Rating was successfully removed.";
                $resultSet['title'] = "Performance Rating Removed.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " deleted performance rating details of employee db id #".$id,"delete", "success", "gcchris", "user");
                
            } else {
                $resultSet['success'] = false;
                $resultSet['message'] = $this->db->error();
                $resultSet['title'] = "Error";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed deleting performance rating details of employee db id #".$id,"delete", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function getEmployeeAllowance() {
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $post = $this->input->post();
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "";
            $emp_id = (isset($post["emp_id"]) && $post["emp_id"]) ? $post["emp_id"] : "";

            $rowData = $this->allowance_list($emp_id, $search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->allowance_count($emp_id, $search);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function allowance_list($emp_id = "", $search = null, $limit = 10, $offset = 0, $sortBy = "ASC", $sortOrder = array()) {
            $filterFields = array("ha.id", "pa.allowance_name", "ha.rate");
            $this->db->select("ha.*, pa.allowance_name");
            $this->db->from("gcchris.allowances ha");
            $this->db->join("payroll.allowance pa", "pa.id = ha.allowance_id", "LEFT");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = ha.emp_id", "LEFT");
            $this->db->where("ha.emp_id", $emp_id);
            $this->db->where("ha.is_archived", 0);

            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }

            empty($sortOrder) ? $i = 0 : $i = $sortOrder[0]['column'];
            empty($sortOrder) ? $this->db->order_by("a.created_at", "desc") : $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $query = $this->db->get();
            return $query->result();
        }

        private function allowance_count($emp_id, $search = null) {
            $filterFields = array("ha.id", "pa.allowance_name", "ha.rate");
            $this->db->select("ha.*, pa.allowance_name");
            $this->db->from("gcchris.allowances ha");
            $this->db->join("payroll.allowance pa", "pa.id = ha.allowance_id", "LEFT");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = ha.emp_id", "LEFT");
            $this->db->where("ha.emp_id", $emp_id);
            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }
            $query = $this->db->get();
            return $query->num_rows();
        }

        function getEmployeeBenefits() {
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $post = $this->input->post();
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "";
            $emp_id = (isset($post["emp_id"]) && $post["emp_id"]) ? $post["emp_id"] : "";

            $rowData = $this->benefit_list($emp_id, $search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->benefit_count($emp_id, $search);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function benefit_list($emp_id = "", $search = null, $limit = 10, $offset = 0, $sortBy = "ASC", $sortOrder = array()) {
            $filterFields = array("a.id", "b.benefit_name", "a.rate");
            $this->db->select("a.*, b.benefit_name");
            $this->db->from("gcchris.benefits a");
            $this->db->join("payroll.benefits b", "b.id = a.benefit_id", "LEFT");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = a.emp_id", "LEFT");
            $this->db->where("a.emp_id", $emp_id);
            $this->db->where("a.is_archived", 0);

            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }

            empty($sortOrder) ? $i = 0 : $i = $sortOrder[0]['column'];
            empty($sortOrder) ? $this->db->order_by("a.created_at", "desc") : $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $rs->rate = number_format($rs->rate, 2);
                    $rs->action = '<div class="dropdown">
                                        <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown">
                                            <i class="la la-ellipsis-h"></i>
                                        </a>
                                    <div class="dropdown-menu dropdown-menu-bottom">
                                            <a class="dropdown-item btnDelete" onclick="remove_benefit(' . $rs->id . ')" type="button"><i class="la la-trash"></i> Remove</a>
                                        </div>
                                    </div>';
                    $arrData[$key] = $rs;
                }
                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
                return $data;
            } else {
                return array();
            }
        }

        private function benefit_count($emp_id, $search = null) {
            $filterFields = array("a.id", "b.benefit_name", "a.rate");
            $this->db->select("a.*, b.benefit_name");
            $this->db->from("gcchris.benefits a");
            $this->db->join("payroll.benefits b", "b.id = a.benefit_id", "LEFT");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = a.emp_id", "LEFT");
            $this->db->where("a.emp_id", $emp_id);
            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }
            $query = $this->db->get();
            return $query->num_rows();
        }

        /*** loan deduction tbl ***/
        function getEmpLoans(){
            $resultset = array();
            $post = $this->input->post();
            $emp_id = (isset($post["emp_id"]) && $post["emp_id"]) ? $post["emp_id"] : "";
            $rowCount = 0;
            $rowData = array();
            $order_val = array(array("column" => "1", "dir" => "desc"));

            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

            $rowData = $this->getEmployeeLoan_list($emp_id, $search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->getEmployeeLoan_count($emp_id, $search);

            $ctrData = count($rowData);
            $totalNotFiltered = $ctrData != $rowCount ? $ctrData : $rowCount;

            $resultset["recordsTotal"] = $totalNotFiltered;
            $resultset["recordsFiltered"] = $totalNotFiltered;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        public function getEmployeeLoan_list($emp_id, $search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $arrData = array();
            $filterFields = array("master_loans.loan_name", "emp_loans.reference");
            $this->db->select("emp_loans.*, master_loans.loan_name, ROUND(SUM(IFNULL(psloanpayments.amount_due, 0)),2) as total_amount_paid, 
            GROUP_CONCAT(DISTINCT psloanpayments.amount_due, '||', ps.id) as temp_amount_paid, emp_loans.reference as ref, emp_loans.id as loan_id, 
            emp_loans.loan_id as loan_type_id, IFNULL(COUNT(merged_loans.id), 0) as merged_count");
            $this->db->from("gcchris.loans emp_loans");
            $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id");
            $this->db->join("payroll.payroll_sheet_loan_payments psloanpayments", "psloanpayments.loan_id = emp_loans.id", "LEFT");
            $this->db->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id AND ps.posted = 1", "LEFT");
            $this->db->join("gcchris.loans merged_loans", "merged_loans.merged_id = emp_loans.id", "LEFT");
            $this->db->where("emp_loans.emp_id", $emp_id);
            $this->db->where("emp_loans.is_archived", 0);
            $this->db->where("emp_loans.active !=", 3);
            $this->db->group_by("emp_loans.id, emp_loans.loan_id");
            $this->db->order_by("emp_loans.id", "DESC");

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
                foreach ($query->result() as $key => $value) {
                    $tempTotal = 0;
                    $tempAmount = $value->temp_amount_paid;
                    $tempAmount = explode(",", $tempAmount);
                    foreach ($tempAmount as $kk => $vv) {
                        $tempDD = explode("||", $vv);
                        $tempTotal += floatval($tempDD[0]);
                    }
                    $tempTotal = round($tempTotal, 2);
                    if($tempTotal !== floatval($value->total_amount_paid)){ $value->total_amount_paid = $tempTotal; }
                    $tempCreatedBy = intval($value->created_by) > 0 ? 
                        $this->core_layout->getEmployeeData($value->created_by)['display_name_1'] : "[ System Generated: Cash Advance ]";
                    $value->created_by = $tempCreatedBy;
                    $value->created_at = date('Y-m-d', strtotime($value->created_at));
                    $tempbalance = floatval($value->amount) - floatval($value->total_amount_paid);
                    $value->tempbalance = $tempbalance;
                    $value->image = ($this->checkLoanAttachment($value->loan_id));

                    $value->allow_merge = false;
                    $value->unpaid = new stdClass();

                    $tempTotalAmountPaid = floatval($value->total_amount_paid);
                    $value->total_amount_paid = $tempTotalAmountPaid;
                    if($tempTotalAmountPaid == 0 && intval($value->merged_count) == 0){
                        $this->db->from("gcchris.loans emp_loans");
                        $this->db->where("emp_loans.id !=", $value->loan_id);
                        $this->db->where("emp_loans.emp_id", $emp_id);
                        $this->db->where("emp_loans.loan_id", $value->loan_type_id);
                        $this->db->where("emp_loans.deduction_type", 0);
                        $this->db->where("emp_loans.percentage", $value->percentage);
                        $this->db->where("emp_loans.active <=", 1);
                        $this->db->where("emp_loans.paid", 0);
                        $isNotPaid = $this->db->get();
                        $value->sql = $this->db->last_query();
                        if($isNotPaid->num_rows() > 0){
                            $value->unpaid = $isNotPaid->result();
                            $value->allow_merge = true;
                        }
                        $this->db->reset_query();
                    }

                    if($tempbalance > 0){
                        $arrData[] = $value;
                    }else{
                        $arrData;
                    }
                }
                return $arrData;
            } else {
                return array();
            }

        }

        public function getEmployeeLoan_count($emp_id, $search=null) {
            $filterFields = array("master_loans.loan_name", "emp_loans.reference as ref");
            $this->db->select("emp_loans.*, master_loans.loan_name, ROUND(SUM(IFNULL(psloanpayments.amount_due, 0)),2) as total_amount_paid, GROUP_CONCAT(psloanpayments.amount_due, '||', ps.id) as temp_amount_paid, emp_loans.reference as ref");
            $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id");
            $this->db->join("payroll.payroll_sheet_loan_payments psloanpayments", "psloanpayments.loan_id = emp_loans.id", "LEFT");
            $this->db->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id AND ps.posted = 1", "LEFT");
            $this->db->where("emp_loans.emp_id", $emp_id);
            $this->db->where("emp_loans.is_archived", 0);
            $this->db->where("emp_loans.active !=", 3);
            $this->db->group_by("emp_loans.id, emp_loans.loan_id");
            $this->db->order_by("emp_loans.id", "DESC");
            $query = $this->db->get("gcchris.loans emp_loans");

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
            $count = $query->num_rows();
            return $count;
        }
        /*** loan deduction tbl ***/

        /*** loan history deduction tbl ***/
        function getEmpLoansHistory(){
            $resultset = array();
            $post = $this->input->post();
            $emp_id = (isset($post["emp_id"]) && $post["emp_id"]) ? $post["emp_id"] : "";
            $loanStatus = (isset($post["loan_status"]) && $post["loan_status"]) ? intval($post["loan_status"]) : null;
            $rowCount = 0;
            $rowData = array();
            $order_val = array(array("column" => "1", "dir" => "desc"));

            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

            $isCancelled = intval($loanStatus) == 3;

            $rowData = $this->getEmpLoansHistory_list($emp_id, $search, $limit, $offset, $sortBy, $sortOrder, $isCancelled);
            $rowCount = $this->getEmpLoansHistory_count($emp_id, $search, $isCancelled);

            $ctrData = count($rowData);
            $totalNotFiltered = $ctrData != $rowCount ? $ctrData: $rowCount;

            $resultset["recordsTotal"] = $totalNotFiltered;
            $resultset["recordsFiltered"] = $totalNotFiltered;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        public function getEmpLoansHistory_list($emp_id, $search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $isCancelled=false) {
            $arrData = array();
            $filterFields = array("master_loans.loan_name", "emp_loans.reference");
            $this->db->select("emp_loans.*, master_loans.loan_name, ROUND(SUM(IFNULL(psloanpayments.amount_due, 0)),2) as total_amount_paid, 
            GROUP_CONCAT(DISTINCT psloanpayments.amount_due, '||', ps.id) as temp_amount_paid, emp_loans.reference as ref, emp_loans.id as loan_id, 
            merged_loans.amount as merged_amount, IFNULL(COUNT(mloans.id), 0) as merged_count");
            $this->db->from("gcchris.loans emp_loans");
            $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id");
            $this->db->join("payroll.payroll_sheet_loan_payments psloanpayments", "psloanpayments.loan_id = emp_loans.id", "LEFT");
            $this->db->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id AND ps.posted = 1", "LEFT");
            $this->db->join("gcchris.loans merged_loans", "merged_loans.id = emp_loans.merged_id", "LEFT");
            $this->db->join("gcchris.loans mloans", "mloans.merged_id = emp_loans.id", "LEFT");
            $this->db->where("emp_loans.emp_id", $emp_id);
            /*** cancelled status [active=3] ***/
            if($isCancelled){ $this->db->where("emp_loans.active", 3); }
            /*** cancelled status [active=3] ***/
            $this->db->where("emp_loans.is_archived", 0);
            $this->db->group_by("emp_loans.id, emp_loans.loan_id");
            $this->db->order_by("emp_loans.id", "DESC");

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
                foreach ($query->result() as $key => $value) {
                    $tempTotal = 0;
                    $tempAmount = $value->temp_amount_paid;
                    $tempAmount = explode(",", $tempAmount);
                    foreach ($tempAmount as $kk => $vv) {
                        $tempDD = explode("||", $vv);
                        $tempTotal += floatval($tempDD[0]);
                    }
                    $tempTotal = round($tempTotal, 2);
                    if($tempTotal !== floatval($value->total_amount_paid)){ $value->total_amount_paid = $tempTotal; }
                    $tempCreatedBy = intval($value->created_by) > 0 ? 
                        $this->core_layout->getEmployeeData($value->created_by)['display_name_1']: "[ System Generated: Cash Advance ]";
                    $value->created_by = $tempCreatedBy;
                    $value->created_at = date('Y-m-d', strtotime($value->created_at));
                    $tempbalance = floatval($value->amount) - floatval($value->total_amount_paid);
                    $value->tempbalance = $tempbalance;
                    $value->image = ($this->checkLoanAttachment($value->loan_id));
                    if($isCancelled){
                        $arrData[] = $value;
                    }else if($tempbalance <= 0){
                        $arrData[] = $value;
                    }
                }
                return $arrData;
            } else {
                return array();
            }

        }

        public function getEmpLoansHistory_count($emp_id, $search=null, $isCancelled=false) {
            $filterFields = array("master_loans.loan_name", "emp_loans.reference as ref");
            $this->db->select("emp_loans.*, master_loans.loan_name, ROUND(SUM(IFNULL(psloanpayments.amount_due, 0)),2) as total_amount_paid, GROUP_CONCAT(psloanpayments.amount_due, '||', ps.id) as temp_amount_paid, emp_loans.reference as ref");
            $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id");
            $this->db->join("payroll.payroll_sheet_loan_payments psloanpayments", "psloanpayments.loan_id = emp_loans.id", "LEFT");
            $this->db->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id AND ps.posted = 1", "LEFT");
            $this->db->where("emp_loans.emp_id", $emp_id);
            $this->db->where("emp_loans.is_archived", 0);
            /*** cancelled status [active=3] ***/
            if($isCancelled){ $this->db->where("emp_loans.active", 3); }
            /*** cancelled status [active=3] ***/
            $this->db->group_by("emp_loans.id, emp_loans.loan_id");
            $this->db->order_by("emp_loans.id", "DESC");
            $query = $this->db->get("gcchris.loans emp_loans");

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
            $count = $query->num_rows();
            return $count;
        }
        /*** loan history deduction tbl ***/

        function checkLoanAttachment($id){
            $this->db->where('loan_id', $id);
            $this->db->from('gcchris.loans_images');
            $query = $this->db->get();
            return $query->num_rows();
        }


        /*** broken function please fix ***/
        function getEmployeeLoans() {
            $resultset = array();
            $post = $this->input->post();
            $emp_id = (isset($post["emp_id"]) && $post["emp_id"]) ? $post["emp_id"] : "";
            $offset = isset($post["start"]) && $post["start"] ? $post["start"]: 0;
            $limit = isset($post["length"]) && $post["length"] ? $post["length"]: 10;
            
            $this->db->select("emp_loans.*, master_loans.loan_name, 
                ROUND(SUM(IFNULL(psloanpayments.amount_due, 0)),2) as total_amount_paid, 
                GROUP_CONCAT(DISTINCT psloanpayments.amount_due, '||', ps.id) as temp_amount_paid, emp_loans.reference as ref");
            $this->db->where("emp_loans.emp_id", $emp_id);
            $this->db->where("emp_loans.is_archived", 0);
            $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id");
            $this->db->join("payroll.payroll_sheet_loan_payments psloanpayments", "psloanpayments.loan_id = emp_loans.id", "LEFT");
            $this->db->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id AND ps.posted = 1", "LEFT");
            $this->db->group_by("emp_loans.id, emp_loans.loan_id");
            $this->db->order_by("emp_loans.id", "DESC");

            if ($limit != -1) { $this->db->limit($limit, $offset); }

            $query = $this->db->get("gcchris.loans emp_loans");
            $tempSql = $this->db->last_query();

            if($query->num_rows() > 0){
                foreach ($query->result() as $key => $value) {
                    $tempTotal = 0;
                    $tempAmount = $value->temp_amount_paid;
                    $tempAmount = explode(",", $tempAmount);
                    foreach ($tempAmount as $kk => $vv) {
                        $tempDD = explode("||", $vv);
                        $tempTotal += floatval($tempDD[0]);
                    }
                    $tempTotal = round($tempTotal, 2);
                    if($tempTotal !== floatval($value->total_amount_paid)){ $value->total_amount_paid = $tempTotal; }
                    $tempCreatedBy = $value->created_by ? $this->core_layout->getEmployeeData($value->created_by)['display_name_1']: "[ System Generated: Cash Advance ]"; 
                    $value->created_by = $tempCreatedBy;
                    $value->created_at = date('Y-m-d', strtotime($value->created_at));
                    $arrData[$key] = $value;
                }
            }

            $resultset["recordsTotal"] = $this->getEmployeeLoansCount($emp_id);
            $resultset["recordsFiltered"] = $this->getEmployeeLoansCount($emp_id);
            $resultset["data"] = ($this->getEmployeeLoansCount($emp_id) > 0)? $arrData: array();
            $resultset["sql"] = $tempSql;

            return $resultset;
        }
        /*** broken function please fix ***/

        function getEmployeeLoan($id) {
            $this->db->select("emp_loans.*, master_loans.loan_name, master_loans.has_ref");
            $this->db->where("emp_loans.id", $id);
            $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id", "INNER");
            return $this->db->get("gcchris.loans emp_loans")->row();
        }

        function getEmployeeToMergeLoan($id) {
            $resultset = array();

            $this->db->select("emp_loans.*, master_loans.loan_name, master_loans.has_ref, GROUP_CONCAT(tomerge_loans.id) as loan_type_id");
            $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id", "INNER");
            $this->db->join("gcchris.loans tomerge_loans", "tomerge_loans.emp_id = emp_loans.emp_id AND tomerge_loans.loan_id = emp_loans.loan_id 
                AND tomerge_loans.paid = 0 AND tomerge_loans.merged_id = 0 AND tomerge_loans.active < 2 AND tomerge_loans.id != {$id}", "LEFT");
            $this->db->where("emp_loans.id", $id);
            $query = $this->db->get("gcchris.loans emp_loans");
            if($query->num_rows() == 1){
                $rowData = $query->row();
                $rowData->to_merge_loans = new stdClass();
                $rowData->amount_formatted = number_format($rowData->amount, 2, ".", ",");
                $rowData->percentage_formatted = number_format($rowData->percentage, 2, ".", ",");
                $rowData->fixed_amt_formatted = number_format($rowData->fixed_deduction_amt, 2, ".", ",");

                if($rowData->loan_type_id){
                    $arrIds = explode(",", $rowData->loan_type_id);
                    $this->db->select("emp_loans.*, master_loans.loan_name, ROUND(SUM(IFNULL(psloanpayments.amount_due, 0)),2) as total_amount_paid, GROUP_CONCAT(psloanpayments.amount_due, '||', ps.id) as temp_amount_paid");
                    $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id");
                    $this->db->join("payroll.payroll_sheet_loan_payments psloanpayments", "psloanpayments.loan_id = emp_loans.id", "LEFT");
                    $this->db->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id AND ps.posted = 1", "LEFT");
                    $this->db->where("emp_loans.is_archived", 0);
                    $this->db->where_in("emp_loans.id", $arrIds);
                    $this->db->group_by("emp_loans.id, emp_loans.loan_id");
                    $this->db->order_by("emp_loans.id", "DESC");
                    $queryLoans = $this->db->get("gcchris.loans emp_loans");

                    if($queryLoans->num_rows() > 0){
                        $arrData = array();
                        foreach ($queryLoans->result() as $key => $value) {
                            $tempTotal = 0;
                            $tempAmount = $value->temp_amount_paid;
                            $tempAmount = explode(",", $tempAmount);
                            unset($value->temp_amount_paid);
                            foreach ($tempAmount as $kk => $vv) {
                                $tempDD = explode("||", $vv);
                                $tempTotal += floatval($tempDD[0]);
                            }
                            $tempTotal = round($tempTotal, 2);
                            
                            if($tempTotal !== floatval($value->total_amount_paid)){ $value->total_amount_paid = $tempTotal; }
                            $value->created_by = $this->core_layout->getEmployeeData($value->created_by)['display_name_1'];
                            $value->created_at = date('Y-m-d', strtotime($value->created_at));

                            $value->balance_amt = round(floatval($value->amount) - floatval($value->total_amount_paid), 2);
                            $value->balance_amt_formatted = number_format($value->balance_amt, 2, ".", ",");
                            if($value->balance_amt > 0){ 
                                $arrData[$key] = $value;
                            }else{
                                $tempState = array("id"=>$value->id, "paid"=>0);
                                $this->db->update("gcchris.loans", 
                                array("remarks"=> "[ System Update: Paid Status ] Remaining Balance {$value->balance_amt}", "paid"=>1, "active"=>2), 
                                $tempState);
                            }
                        }
                        $rowData->to_merge_loans = $arrData;
                    }
                }
                $resultset["response"] = true;
                $resultset["data"] = $rowData;
            }else{
                $resultset["response"] = false;
            }
            return $resultset;
        }

        private function getEmployeeLoansCount($emp_id) {
            $this->db->select("emp_loans.*, master_loans.loan_name");
            $this->db->where("emp_loans.emp_id", $emp_id);
            $this->db->where("emp_loans.is_archived", 0);
            $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id");
            return $this->db->count_all_results("gcchris.loans emp_loans");
        }

        private function loans_list($emp_id = "", $search = null, $limit = 10, $offset = 0, $sortBy = array(), $sortOrder = array()) {
            $filterFields = array("a.id", "b.loan_name", "a.rate", "a.term", "a.deduct_type");
            $this->db->select("a.*, b.loan_name");
            $this->db->from("gcchris.loans a");
            $this->db->join("payroll.loans b", "b.id = a.loan_id", "LEFT");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = a.emp_id", "LEFT");
            $this->db->where("a.is_archived", "0");
            $this->db->where("a.emp_id", $emp_id);

            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }

            empty($sortOrder) ? $i = 0 : $i = $sortOrder[0]['column'];
            empty($sortOrder) ? $this->db->order_by("a.created_at", "desc") : $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $rs->rate = number_format($rs->rate, 2);
                    $rs->action = '<div class="dropdown">
                                        <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown">
                                            <i class="la la-ellipsis-h"></i>
                                        </a>
                                    <div class="dropdown-menu dropdown-menu-bottom">
                                            <a class="dropdown-item btnArchive" onclick="remove_loan(' . $rs->id . ')" type="button"><i class="la la-archive"></i> Archive</a>
                                        </div>
                                    </div>';
                    $arrData[$key] = $rs;
                }
                $data = array();
                foreach ($arrData as $k => $v) {
                    $data[] = $v;
                }
                return $data;
            } else {
                return array();
            }
        }

        private function loans_count($emp_id, $search = null) {
            $filterFields = array("a.id", "b.loan_name", "a.rate", "a.term", "a.deduct_type");
            $this->db->select("a.*, b.loan_name");
            $this->db->from("gcchris.loans a");
            $this->db->join("payroll.loans b", "b.id = a.loan_id", "LEFT");
            $this->db->join("gccmaster.tblemployees emp", "emp.id = a.emp_id", "LEFT");
            $this->db->where("a.emp_id", $emp_id);
            $this->db->where("a.is_archived", "0");
            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }
            $query = $this->db->get();
            return $query->num_rows();
        }

        function getAllowanceCollection($id) {
            $get = $this->input->get();
            $resultarray = array();

            $this->db->select("*");
            $this->db->from("payroll.allowance");
            if (isset($get['q'])) {
                $this->db->like('allowance_name', $get['q']);
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    if (!$this->checkSelected($_query["id"], $id, 'allowance_id', 'gcchris.allowances')) {
                        $data = array();
                        $data["id"] = $_query["id"];
                        $data["text"] = $_query["allowance_name"];

                        $resultarray[] = $data;
                    }
                }
            }

            return array("results" => $resultarray);
        }

        function getBenefitCollection($id) {
            $get = $this->input->get();
            $resultarray = array();

            $this->db->select("*");
            $this->db->from("payroll.benefits");
            if (isset($get['q'])) {
                $this->db->like('benefit_name', $get['q']);
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    if (!$this->checkSelected($_query["id"], $id, 'benefit_id', 'gcchris.benefits')) {
                        $data = array();
                        $data["id"] = $_query["id"];
                        $data["text"] = $_query["benefit_name"];

                        $resultarray[] = $data;
                    }
                }
            }

            return array("results" => $resultarray);
        }

        function getLoanCollection($id, $show_all) {
            $get = $this->input->get();
            $resultarray = array();
            $this->db->select("*");
            $this->db->from("payroll.loans");
            if (isset($get['q'])) {
                $this->db->like('loan_name', $get['q']);
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                if (intval($show_all) === 1) {
                    foreach ($query->result_array() as $_query) {
                        $data = array();
                        $data["id"] = $_query["id"];
                        $data["text"] = $_query["loan_name"];
                        $data['has_ref'] = $_query['has_ref'];

                        $resultarray[] = $data;
                    }
                } else {
                    foreach ($query->result_array() as $_query) {
                        $data = array();
                        $data["id"] = $_query["id"];
                        $data["text"] = $_query["loan_name"];
                        $data['has_ref'] = $_query['has_ref'];
                        $resultarray[] = $data;

                        /*** start temporarily remove 
                         * $this->db->select("*, GROUP_CONCAT(DISTINCT(paid)) as is_paid");
                        $this->db->where("emp_id", $id);
                        $this->db->where("loan_id", $_query["id"]);
                        $this->db->group_by("loan_id");
                        $tempQ = $this->db->get("gcchris.loans");
                        if($tempQ->num_rows() == 0){
                            $data["id"] = $_query["id"];
                            $data["text"] = $_query["loan_name"];
                            $resultarray[] = $data;
                        }else{
                            $tempPaid = explode(",", $tempQ->row()->is_paid);
                            $tempPaid = array_search(0, $tempPaid);
                            if($tempPaid == false && $tempPaid !== 0){
                                $data["id"] = $_query["id"];
                                $data["text"] = $_query["loan_name"];
                                $resultarray[] = $data;
                            }
                        } start temporarily remove ***/

                        /*** if (!$this->checkSelected($_query["id"], $id, 'loan_id', 'gcchris.loans')) {
                            $data = array();
                            $data["id"] = $_query["id"];
                            $data["text"] = $_query["loan_name"];

                            $resultarray[] = $data;
                        } ***/
                    }
                }
            }
            return array("results" => $resultarray);
        }

        function loanHasRef($id){
            $this->db->select("has_ref");
            $this->db->where('id', $id);
            $this->db->from("payroll.loans");
            $query = $this->db->get();

            return $query->row();
        }

        function checkSelected($id, $emp_id, $field, $database) {
            $this->db->where($field, $id);
            $this->db->where('emp_id', $emp_id);
            $this->db->where('is_archived', 0);
            $query = $this->db->get($database);
            return $query->num_rows() > 0 ? true : false;
        }

        function saveEmployeeAllowance() {
            $date = date("Y-m-d");
            $resultarray = array();
            $data = array();
            $post = $this->input->post();
            $user = $this->core_layout->getUserLoggedIn();

            $data["created_by"] = $user["employee_id"];
            $data['created_at'] = $date;
            $data["emp_id"] = $post["emp_id"];
            $data['frequency'] = $post['allowance_id'] == 1 ? 'day' : 'month';
            $data["allowance_id"] = $post["allowance_id"];
            $data["rate"] = $post["rate"];

            $query = $this->db->insert("gcchris.allowances", $data);
            $lastInsertedId = $this->db->insert_id();

            /*** edited contents logging ***/
            $this->db->select("allw.rate, allw.frequency, allw.is_active, pallw.allowance_name, CONCAT(UPPER(TRIM(emp.firstname)), ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END,' ', UPPER(TRIM(emp.lastname)),
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
            END) as employee_name");
            $this->db->from("gcchris.allowances as allw");
            $this->db->join("gccmaster.tblemployees as emp", "emp.id = allw.emp_id", "INNER");
            $this->db->join("payroll.allowance as pallw", "pallw.id = allw.allowance_id", "INNER");
            $this->db->where("allw.id", $lastInsertedId);
            $allw = $this->db->get();
            $tempEmployeeName = "No Employee Name"; $rate = 0; $frequency = "day"; $allowanceName = "DAILY ALLOWANCE";

            if($allw->num_rows() === 1){
                $row = $allw->row();
                $tempEmployeeName = $row->employee_name;
                $rate = $row->rate;
                $frequency = $row->frequency;
                $allowanceName = $row->allowance_name;
            }
            $this->db->reset_query();
            /*** edited contents logging ***/

            if ($query) {
                // $this->db->select('sal_remarks, id');
                // $this->db->limit(1);
                // $this->db->order_by('id', 'DESC');
                // $q = $this->db->get_where($this->employeeSalaryTable, array('emp_id' => $post['emp_id'], 'is_archived' => 0));

                // if($q->num_rows() > 0){
                //     $row = $q->row();
                //     $fr = ($frequency == 'day') ? 'Daily Allowance' : 'Monthly Allowance';
                //     $data = array(
                //         'sal_remarks' => $row->sal_remarks . ' + ' . $post["rate"] . ' ' . $fr,
                //         'update_date' => date('Y-m-d H:i:s'),
                //         'update_by' => $user["employee_id"]
                //     );

                //     $this->db->where('id', $row->id);
                //     $this->db->update($this->employeeSalaryTable, $data);
                // }

                $checkAllowance = $this->check_has_no_allowance($post["emp_id"]);
                if($checkAllowance){
                    $historyStatus = $this->set_approved_allowance($data);
    
                    if($historyStatus){
                        $resultarray['salary_history'] = 'Salary History Generated.';
                    }else{
                        $resultarray['salary_history'] = 'Failed to generate Salary History.';
                    }
                }else{
                    $resultarray['salary_history'] = 'No Salary History Generated';
                }

                $resultarray["status"] = TRUE;
                $resultarray["response"] = "Data successfully saved!";
                 /*** edited contents logging ***/
                 $logMessage = "Employee named `$tempEmployeeName` with payroll allowance data named `$allowanceName`, rate of `$rate` and frequency of `$frequency` has been added.";
                 $this->core_layout->setEventLog($logMessage, "insert", "success", "gcchris", "user");
                 /*** edited contents logging ***/
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["response"] = $this->db->error();
                 /*** edited contents logging ***/
                $logMessage = "Failed to add new payroll allowance data of employee named `$tempEmployeeName` with allowance named `$allowanceName`, rate of `$rate` and frequency of `$frequency`.";
                $this->core_layout->setEventLog($logMessage, "insert", "error", "gcchris", "user");
                 /*** edited contents logging ***/
            }

            return $resultarray;
        }

        function saveEmployeeBenefit() {
            $date = date("Y-m-d");
            $resultarray = array();
            $data = array();
            $post = $this->input->post();
            $user = $this->core_layout->getUserLoggedIn();

            $data["created_by"] = $user["employee_id"];
            $data['created_at'] = $date;
            $data["emp_id"] = $post["emp_id"];
            $data["benefit_id"] = $post["benefit_id"];
            $data["rate"] = $post["rate"];

            $query = $this->db->insert("gcchris.benefits", $data);

            if ($query) {
                $resultarray["status"] = TRUE;
                $resultarray["response"] = "Data successfully saved!";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has inserted new benefit for employee with db id ".$this->db->insert_id(),"insert", "success", "gcchris", "user");
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["response"] = $this->db->error();
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has failed inserting new benefit for employee with db id ".$this->db->insert_id(),"insert", "error", "gcchris", "user");
            }

            return $resultarray;
        }

        function saveEmployeeLoan() {
            $date = date("Y-m-d H:i:s");
            $resultarray = array();
            $data = array();
            $post = $this->input->post();
            $user = $this->core_layout->getUserLoggedIn();

            $data["created_by"] = $user["employee_id"];
            $data['created_at'] = $date;
            $data["emp_id"] = $post["emp_id"];
            $data["loan_id"] = $post["loan_id"];
            $data['reference_id'] = (isset($post['reference_id'])) ? $post['reference_id'] : 0;
            $data['reference'] = (isset($post['reference'])) ? trim($post['reference']) : "";
            $data["amount"] = $post["amount"];
            $data["deduction_type"] = $post["deduction_type"];
            $data["percentage"] = 0;
            $data["fixed_deduction_amt"] = 0;

            $data["debit_note"] = isset($post["debit_note"]) && $post["debit_note"] ? trim(strtoupper($post["debit_note"])): NULL;
            $data["remarks"] = isset($post["remarks"]) && $post["remarks"] ? trim($post["remarks"]): NULL;

            if (intval($data["deduction_type"]) === 0) {
                $data["percentage"] = 20;
            } else {
                $data["fixed_deduction_amt"] = $post["deduct_type_value"];
            }

            $query = $this->db->insert("gcchris.loans", $data);

            if ($query) {
                $resultarray["status"] = TRUE;
                $resultarray["response"] = "New loan information was successfully saved!";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has inserted new loan for employee with db id ".$this->db->insert_id(),"insert", "success", "gcchris", "user");
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["response"] = $this->db->error();
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has failed inserting new loan for employee with db id ".$this->db->insert_id(),"insert", "error", "gcchris", "user");
            }

            return $resultarray;
        }

        function removeEmployeeAllowance($id) {
            $resultarray = array();

            /*** edited contents logging ***/
            $this->db->select("allw.rate, allw.frequency, allw.is_active, allw.emp_id, CONCAT(UPPER(TRIM(emp.firstname)), ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END,' ', UPPER(TRIM(emp.lastname)),
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
            END) as employee_name");
            $this->db->from("gcchris.allowances as allw");
            $this->db->join("gccmaster.tblemployees as emp", "emp.id = allw.emp_id", "INNER");
            $this->db->where("allw.id", $id);
            $allw = $this->db->get();
            $tempEmployeeName = "No Employee Name"; $rate = 0; $frequency = "day";
            $employeeId = 0;

            if($allw->num_rows() === 1){
                $row = $allw->row();
                $tempEmployeeName = $row->employee_name;
                $employeeId = $row->emp_id;
                $rate = $row->rate;
                $frequency = $row->frequency;
            }
            $this->db->reset_query();
            /*** edited contents logging ***/

            $date = date("Y-m-d");
            $user = $this->core_layout->getUserLoggedIn();
            $data = array();
            $data['is_archived'] = 1;
            $data['archived_by'] = $user["employee_id"];
            $data['archived_at'] = $date;
            $updated = $this->db->update("gcchris.allowances", $data, array("id" => $id));

            $coreHistoryLog = $this->core_layout->coreHistoryLogs();
            $coreHistoryLog->setHistoryLogModule("hris");
            $coreHistoryLog->setHistoryLogTableName("gcchris.allowances");
            $coreHistoryLog->setHistoryLogTableFieldId($id);
            $coreHistoryLog->setHistoryLogEmployeeId($employeeId);

            if ($updated && $this->db->affected_rows() > 0) {
                $resultarray["status"] = TRUE;
                $resultarray["response"] = "Data has been removed!";
                /*** edited contents logging ***/
                $logMessage = "Employee named `$tempEmployeeName` with payroll allowance data rate of `$rate` and frequency of `$frequency` has been archived.";
                $coreHistoryLog->setEventLog($logMessage, "archive/update", "success", "gcchris", "user");
                $coreHistoryLog->saveLoggedEventHistory();
                /*** edited contents logging ***/
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["response"] = $this->db->error();
                /*** edited contents logging ***/
                $logMessage = "Failed to archive payroll allowance data of employee named `$tempEmployeeName` with a rate of `$rate` and frequency of `$frequency`.";
                $coreHistoryLog->setEventLog($logMessage, "archive/update", "error", "gcchris", "user");
                $coreHistoryLog->saveLoggedEventHistory();
                /*** edited contents logging ***/
            }

            return $resultarray;
        }

        function removeEmployeeBenefit($id) {
            $resultarray = array();
            $date = date("Y-m-d");
            $user = $this->core_layout->getUserLoggedIn();
            $data = array();
            $data['is_archived'] = 1;
            $data['archived_by'] = $user["employee_id"];
            $data['archived_at'] = $date;

            // original query by removing of benefits
            // $this->db->update("gcchris.benefits", $data);
            // $query = $this->db->where("benefits.id", $id);

            $this->db->where("benefits.id", $id);
            $query = $this->db->update("gcchris.benefits as benefits", $data);

            if ($query) {
                $resultarray["status"] = TRUE;
                $resultarray["response"] = "Data has been removed!";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has archived benefit for employee with db id ".$id,"update", "success", "gcchris", "user");
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["response"] = $this->db->error();
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has failed archiving benefit for employee with db id ".$id,"update", "error", "gcchris", "user");
            }

            return $resultarray;
        }

        function removeEmployeeLoan($id) {
            $date = date("Y-m-d H:i:s");
            $resultarray = array();
            $user = $this->core_layout->getUserLoggedIn();
            $data = array();
            $data['is_archived'] = 1;
            $data['archived_by'] = $user["employee_id"];
            $data['archived_at'] = $date;

            $query = $this->db->update("gcchris.loans", $data, array("id"=>$id));
            if ($query) {
                $this->core_layout->insertArchiveLog("gcchris.loans", $id);
                $resultarray["status"] = TRUE;
                $resultarray["response"] = "Data has been archived!";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has archived loan for employee with db id ".$id,"update", "success", "gcchris", "user");
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["response"] = $this->db->error();
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has failed archiving loan for employee with db id ".$id,"update", "error", "gcchris", "user");
            }

            return $resultarray;
        }

        function importCsv() {
            $resultset = array();
            $resultarray = array();
            $heads = array();
            $importPath = "./uploads/imports";
            $config = array();
            $config['upload_path'] = $importPath;
            $config['allowed_types'] = 'csv|CSV';
            $config['max_size'] = 10000;
            $config['create_thumbnail'] = false;
            $config['overwrite'] = TRUE;

            $data = $this->file_upload->uploadFile($config);
            $files = $data["files"][0];
            $csv = array_map('str_getcsv', file($files["full_path"]));

            foreach ($csv as $k => $val) {
                $res = array();
                if ($k != 0) {
                    foreach ($csv[0] as $kkk => $v) {
                        foreach ($val as $kk => $kval) {
                            if ($kk == $kkk) {
                                $res[$v] = $kval;
                            }
                        }
                    }
                    $resultarray[] = $res;
                }
            }

            foreach ($csv[0] as $arrzeroVal) {
                $hdata = array();
                $hdata["title"] = $arrzeroVal;

                $heads[] = $hdata;
            }

            foreach ($resultarray as $_resultarray) {
                $this->db->insert('gccmaster.tblemployees', $_resultarray);
            }

            if ($data["response"] === TRUE) {
                $files = $data["files"][0];
                $filename = $files["file_name"];
                if ($filename) {
                    $resultset["response"] = TRUE;
                    $resultset["toastr_msg"] = "Import successfull successful.";
                    $resultset["toastr_state"] = "success";
                    $resultset["data"] = $resultarray;
                    $resultset["columns"] = $heads;
                } else {
                    $resultset["response"] = FALSE;
                    $resultset["toastr_msg"] = "File upload to specific path failed!";
                    $resultset["toastr_state"] = "error";
                }

            }

            return $resultset;
        }

        function getEmployeeStatusesFromMasterfile($exempt = null) {
            if (!empty($exempt)) {
                $this->db->where_not_in("emp.employee_status", $exempt);
            }

            $this->db->select("emp.employee_status");
            $this->db->where("emp.employee_status IS NOT NULL", NULL, FALSE);
            $this->db->group_by("emp.employee_status");
            $query = $this->db->get("gccmaster.tblemployees emp");
            return $query->result();
        }

        function tempgetEmployeeStatusesFromMasterfile($exempt = null) {
            if (!empty($exempt)) {
                $this->db->where_not_in("emp.employee_status", $exempt);
            }

            $this->db->select("emp.employee_status");
            $this->db->where("emp.employee_status IS NOT NULL", NULL, FALSE);
            $this->db->group_by("emp.employee_status");
            $query = $this->db->get("gccmaster.tblemployees emp");
            return $query->result();
        }

        //update salary history in updating employee position
        function updateEmployeeSalaryHistory(){
            $user = $this->core_layout->getUserLoggedIn();
            $user_emp_id = $user["employee_id"];
            $post = $this->input->post();
            $salary_array = array(
                "emp_id" => $post['salary_employee_id'],
                "sal_date" => $post['salary_effective_date'],
                "sal_rate" => $post['salary_rate'],
                "sal_position" => $post['salary_employee_position'],
                "sal_remarks" => $post['salary_remarks'],
                "add_date" => date("Y-m-d H:i:s"),
                "add_by" => $user_emp_id,
            );
            $_SESSION['salary_temp_data'] = $salary_array;
            return !empty($_SESSION['salary_temp_data']) ? true : false;
        }

        function get_latest_salary_rate(){
            $var = $this->input->post('salary_employee_id');
            $this->db->select("sal_rate, sal_remarks, emp_id");
            $this->db->where("emp_id=$var AND is_archived=0");
            $this->db->order_by("add_date", "DESC");
            $val = $this->db->get("gcchris.tblsalaries");
            $data = $val->row_array();
            return array(
                "sal_rate" => empty($data['sal_rate']) ? "0.00" : $data['sal_rate'],
                "sal_remarks" => empty($data['sal_remarks']) ? " " : $data['sal_remarks']
            );
        }

        function emp_position_details(){
            $name = $this->input->post('a');
            $this->db->select("job_desc, qualification");
            $this->db->where("name", $name);
            $val = $this->db->get("gcchris.tblposition");
            $data = $val->row_array();
            return array(
                "job_desc"=>$data['job_desc'],
                "qualification"=>$data['qualification'],
            );
        }

        function getEmployeePosition($employee_id){
            $if_driver = $this->db
                ->select("emp.position as position")
                ->where("emp.id", $employee_id)
                ->get("gccmaster.tblemployees emp")
                ->row_array();

            if(is_numeric($if_driver['position'])){
                $driver = $this->db
                ->group_start()
                ->like("pos.name","driver")
                ->or_like("pos.name","operator")
                ->group_end()
                ->from("gcchris.tblposition pos")
                ->join("gccmaster.tblemployees emp","emp.position = pos.id")
                ->where("emp.id",$employee_id)
                ->count_all_results();
            }else{
                $driver = $this->db
                ->group_start()
                ->like("emp.position","driver")
                ->or_like("emp.position","operator")
                ->group_end()
                ->from("gccmaster.tblemployees emp")
                ->where("emp.id",$employee_id)
                ->count_all_results();
            }

            return $driver;
        }

        function employeeDocumentUpload(){
            $resultset = array();
            $employeeId = $this->input->post('emp_id');
            if($employeeId){
            //upload filepath
            $filePath = "./uploads/files/documents/employee_files/empcode_{$employeeId}/documents";

            $createFilePath = false;

            if (!file_exists($filePath)) {
                $mkdir = mkdir($filePath, 0777, true);
                if ($mkdir){ $createFilePath = true; }
            }else{ $createFilePath = true; }

            if(!$createFilePath){
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                $resultset["toastr_state"] = "warning";
            }else{
                $config = array();
                $config['upload_path']          = $filePath;
                $config['allowed_types']        = '*';
                $config['max_size']             = 100000;
                $config['create_thumbnail']     = true;

                $data = $this->file_upload->uploadFile($config);

                if($data["response"]){
                    $files = $data["files"][0];
                    $filename = $files["file_name"];
                    $ext = explode(".",  $filename);
                    if($filename){
                        $resultset["response"] = true;
                        $resultset["added_file"] = base_url("uploads/files/hris/employee_documents/empcode_{$employeeId}/{$filename}");
                        $resultset["render_file"] = $filename;
                        $resultset["toastr_msg"] = "Upload file successful.";
                        $resultset["toastr_state"] = "success";
                        $resultset["extension"] = $ext[1];
                        $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has successfully uploaded document for employee with db id #".$employeeId,"upload", "success", "gcchris", "user");
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
                        $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has  failed uploading document to specific path for employee with db id #".$employeeId,"upload", "error", "gcchris", "user");
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "File upload failed!";
                    $resultset["toastr_state"] = "error";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has  failed uploading document for employee with db id #".$employeeId,"upload", "error", "gcchris", "user");
                }
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "User data not found!";
            $resultset["toastr_state"] = "error";
        }
            return $resultset;
        }

        function tempEmployeeDocumentUpload(){
            $user_id = $this->loggedinData['id'];
            $resultset = array();
            $filePath = "./uploads/files/documents/employee_files/employee_temp/documents/user_temp_upload_{$user_id}";

            $createFilePath = false;

            if (!file_exists($filePath)) {
                $mkdir = mkdir($filePath, 0777, true);
                if ($mkdir){ $createFilePath = true; }
            }else{ $createFilePath = true; }

            if(!$createFilePath){
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                $resultset["toastr_state"] = "warning";
            }else{
                $config = array();
                $config['upload_path']          = $filePath;
                $config['allowed_types']        = '*';
                $config['max_size']             = 100000;
                $config['create_thumbnail']     = true;

                $data = $this->file_upload->uploadFile($config);
                if($data["response"]){
                    $files = $data["files"][0];
                    $filename = $files["file_name"];
                    $ext = explode(".",  $filename);
                    if($filename){
                        $resultset["response"] = true;
                        $resultset["added_file"] = base_url("uploads/files/hris/employee_documents/empcode_temp/{$filename}");
                        $resultset["render_file"] = $filename;
                        $resultset["toastr_msg"] = "Upload file successful.";
                        $resultset["toastr_state"] = "success";
                        $resultset["extension"] = $ext[1];
                        // $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has successfully uploaded document for employee with db id #".$employeeId,"upload", "success", "gcchris", "user");
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "File upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
                        // $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has  failed uploading document to specific path for employee with db id #".$employeeId,"upload", "error", "gcchris", "user");
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "File upload failed!";
                    $resultset["toastr_state"] = "error";
                    // $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has  failed uploading document for employee with db id #".$employeeId,"upload", "error", "gcchris", "user");
                }
            }
        // }else{
        //     $resultset["response"] = false;
        //     $resultset["toastr_msg"] = "User data not found!";
        //     $resultset["toastr_state"] = "error";
        // }
            return $resultset;
        }

        function getAllSitePoints(){
            $this->db->select("site_name");
            $sites = $this->db->get("gcctimeutility.app_location_sites");
            $resultarray = array();

            if ($sites->num_rows() > 0) {
                foreach ($sites->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["site_name"];
                    $data["text"] = $_query["site_name"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        private function getBioNum($employeeId){
            $this->db->select('biometricno');
            $this->db->where("id", $employeeId);
            $data = $this->db->get($this->employeeTable);
            $bionum = $data->row_array();
            return $bionum['biometricno'];
        }

        private function getPersonelID($bionum){
            $this->db->select('id');
            $this->db->where("biometricno", $bionum);
            $data = $this->db->get($this->tblPersonnel);
            $id = $data->row_array();
            $count = $data->num_rows();
            return $count != 0 ? $id['id'] : 0;
        }

        private function getSiteLocationId($sitename){
            $this->db->select('id');
            $this->db->where("site_name", $sitename);
            $data = $this->db->get($this->tblAppLocationSites);
            $id = $data->row_array();
            return $id['id'];
        }

        function updateEmployeePayrollData(){
            $post = $this->input->post();
            $resultarray = array();
            $hasApprovingAuthority = isset($post["approving_authority"]) ? json_decode($post["approving_authority"]): false;
            $newEmpDateHired = "";

            /*** edited contents logging ***/
            $postBasicRate = isset($post['basic_rate']) && $post['basic_rate'] ? floatval($post['basic_rate']): 0;
            $checkSalary = $this->check_has_no_salary($post['id']); //checks if new employee has a basic rate encoded

            $tempEmployeeData = (object) $this->core_layout->getEmployeeData($post['id']);
            $editedContent = array();
            $fromContent = array();
            $tempKeys = array("payroll_type"=>"Payroll Type", "payout_sched"=>"Payout Schedule", "basic_rate"=>"Basic Rate");

            $this->db->select("emp.payroll_type, emp.basic_rate, ps.name as payout_sched, emp.date_start as date_hired"); //added date hired for salary history of newly added employees
            $this->db->join("payroll.payout_schedule as ps", "ps.id = emp.payout_sched", "LEFT");
            $qTemp = $this->db->get_where("gccmaster.tblemployees as emp", array("emp.id"=>$post['id']));
            if($qTemp->num_rows() === 1){
                $row = $qTemp->row();

                if(isset($post['payroll_type_desc']) && $post['payroll_type_desc']){
                    if($post['payroll_type_desc'] && $row->payroll_type !== $post['payroll_type_desc']){ 
                        $editedContent["payroll_type"] = $post['payroll_type_desc']; 
                        $fromContent["payroll_type"] = $row->payroll_type;
                    }
                }
                if(isset($post['payout_sched_desc']) && $post['payout_sched_desc']){
                    if($post['payout_sched_desc'] && $row->payout_sched !== strtolower($post['payout_sched_desc'])){ 
                        $editedContent["payout_sched"] = $post['payout_sched_desc']; 
                        $fromContent["payout_sched"] = $row->payout_sched;
                    }
                }

                $currentBasicRate = $row->basic_rate ? floatval($row->basic_rate): 0;
                if($currentBasicRate !== $postBasicRate){
                    $editedContent["basic_rate"] = number_format($post['basic_rate'], 2, ".", ","); 
                    $fromContent["basic_rate"] = number_format($row->basic_rate, 2, ".", ",");
                }

                $newEmpDateHired = $row->date_hired;

                unset($post['payroll_type_desc'], $post['payout_sched_desc']); //added to removed the description that been added to tempData
            }
            $this->db->reset_query();
            /*** edited contents logging ***/

            /*** for approval ***/
            $forApproval = array();
            foreach ($editedContent as $key => $value) {
                if($fromContent[$key]){
                    $isNumeric = false;
                    if(is_numeric($value)){
                        $isNumeric = true;
                        $value = floatval($value);
                        $fromContent[$key] = floatval($fromContent[$key]);
                    }

                    if($isNumeric && $value > 0 && $fromContent[$key] > 0 && $value !== $fromContent[$key]){
                        $forApproval[$key] = $value;
                    }else if($isNumeric === false && $value !== $fromContent[$key]){
                        $forApproval[$key] = $value;
                    }
                }
            }

            $tempData = array();

            foreach ($tempKeys as $key => $value) {
                if((!isset($forApproval[$key]) || $hasApprovingAuthority) && isset($editedContent[$key])){ $tempData[$key] = $post[$key]; }
            }

            /*** for approval ***/
            /*** $data = array(
                'payroll_type' => $post['payroll_type'],
                'basic_rate' => $post['basic_rate'],
                'payout_sched' => $post['payout_sched'],
            ); ***/

            $dbId = $post['id'];
            $updated = false;
            $forApprovalData = is_array($forApproval) && count($forApproval) > 0;
            $tempEmployeeName = $tempEmployeeData->display_name_1 ? $tempEmployeeData->display_name_1: "No employee name";
            $transactionDate = Date("Y-m-d H:i:s");
            $cancelApprovalWhere = array("unique_id"=>$dbId, "table_id"=>$dbId, "database_table"=>"gccmaster.tblemployees", "module"=>"hris", "is_approved"=>0);

            if(is_array($tempData) && count($tempData) > 0){
                $this->db->where('id', $post['id']);
                $tempUpdate = $this->db->update('tblemployees', $tempData);
                $updated = $tempUpdate && $this->db->affected_rows() > 0;
            }

            $coreHistoryLog = $this->core_layout->coreHistoryLogs();
            $coreHistoryLog->setHistoryLogModule("hris");
            $coreHistoryLog->setHistoryLogTableName("gccmaster.tblemployees");
            $coreHistoryLog->setHistoryLogTableFieldId($dbId);
            $coreHistoryLog->setHistoryLogEmployeeId($dbId);

            if($updated && $this->db->trans_status() === TRUE){
                $resultarray["status"] = TRUE;
                $resultarray["response"] = "Data successfully updated!";
                if(is_array($tempData) && count($tempData) > 0){
                    foreach ($tempData as $key => $value) {
                        if(isset($tempKeys[$key]) && $tempKeys[$key]){
                            $cancelApprovalWhere["table_field"] = $key;
                            $this->db->update("gccmaster.field_value_approval", 
                            array("is_approved"=>3, "approval_by"=>$this->core_layout->getCurrentEmployeeId(), "approval_at"=>$transactionDate),
                            $cancelApprovalWhere);
                            $this->db->reset_query();
                            
                            $nKey = $tempKeys[$key];
                            if(isset($tempData[$key]) && $tempData[$key]){
                                $fromValue = isset($fromContent[$key]) && $fromContent[$key] ? $fromContent[$key]: null;
                                $toValue = isset($editedContent[$key]) && $editedContent[$key] ? $editedContent[$key]: null;
                                
                                $logMessage = $fromValue ? 
                                    "Employee named `$tempEmployeeName` with payroll data field `$nKey` has been updated from `$fromValue` to `$toValue`.": 
                                    "Employee named `$tempEmployeeName` with payroll data field `$nKey` has been updated into `$toValue`.";
                                $coreHistoryLog->setEventLog($logMessage, $fromValue ? "update": "insert", "success", "gcchris", "user");
                                $coreHistoryLog->saveLoggedEventHistory();
                            }
                        }
                    }
                }

                if($checkSalary){
                    $post['date_hired'] = $newEmpDateHired;
                    $history = $this->set_approved_salary($post);
                    if($history){
                        $resultarray['salary_history'] = 'Salary History Generated.';
                    }else{
                        $resultarray['salary_history'] = 'Failed to generate Salary History.';
                    }
                }else{
                    if($hasApprovingAuthority){
                        if(count($tempData) == 1 && array_key_exists('payout_sched', $tempData)){
                            $resultarray['salary_history'] = 'No Salary History Generated.';
                        }else{
                            $history = $this->set_approved_salary($post);
                            if($history){
                                $resultarray['salary_history'] = 'Salary History Generated.';
                            }else{
                                $resultarray['salary_history'] = 'Failed to generate Salary History.';
                            }
                        }
                    }
                }
            }else{
                $resultarray["status"] = FALSE;
                if($forApprovalData){
                    $resultarray["response"] = "Updating of payroll data is currently for approval status!";
                }else{
                    $resultarray["response"] = "Error processing request!";
                    $coreHistoryLog->setEventLog("Failed to update payroll data of employee named `$tempEmployeeName`.","update", "error", "gcchris", "user");
                    $coreHistoryLog->saveLoggedEventHistory();
                }
            }

            $resultarray["for_approval"] = false;
            $resultarray["approval_email_notification"] = null;
            if(is_array($forApproval) && count($forApproval) > 0 && $hasApprovingAuthority === false){
                $forApprovalFields = array();
                $forApprovalIds = array();

                foreach ($forApproval as $key => $value) {
                    $originalValue = isset($post[$key]) && $post[$key] ? $post[$key]: null;
                    $tempDescription = isset($tempKeys[$key]) && $tempKeys[$key] ? $tempKeys[$key]: "No description";
                    $_tempData = array("unique_id"=>$dbId,"module"=>"hris", "field_description"=>$tempDescription, 
                    "database_table"=>"gccmaster.tblemployees", "table_id"=>$dbId, 
                    "table_field"=>$key, "table_value"=>$value, "original_value"=>$originalValue,
                    "created_by"=>$this->core_layout->getCurrentEmployeeId(), "created_at"=>$transactionDate);
                    
                    $added = $this->db->insert("gccmaster.field_value_approval", $_tempData);
                    if($added){
                        $forApprovalIds[] = $this->db->insert_id();
                        $forApprovalFields[] = $tempDescription;
                        $fromValue = $fromContent[$key];
                        $toValue = $value;

                        $logMessage = $fromValue ? 
                                "Employee named `$tempEmployeeName` with payroll data field `$tempDescription` and value of from `$fromValue` to `$toValue` is for approval status.": 
                                "Employee named `$tempEmployeeName` with payroll data field `$tempDescription` and value of `$toValue` is for approval status.";
                            $coreHistoryLog->setEventLog($logMessage, $fromValue ? "update": "insert", "success", "gcchris", "user");
                            $coreHistoryLog->saveLoggedEventHistory();
                    }
                }

                $emailSent = false;
                if(is_array($forApprovalIds) && count($forApprovalIds) > 0){
                    $this->db->select("field_description, table_value");
                    $this->db->from("gccmaster.field_value_approval");
                    $this->db->where("is_approved", 0);
                    $this->db->where_in("id", $forApprovalIds);
                    $queryApprovals = $this->db->get();
                    if($queryApprovals->num_rows() > 0){
                        $createdEmployeeData = (object) $this->core_layout->getEmployeeData($this->core_layout->getCurrentEmployeeId());
                        $createdEmployeeName = $createdEmployeeData->display_name_1 ? $createdEmployeeData->display_name_1: "No employee name";

                        $emailData = array("date"=>$transactionDate, "list_header"=>"Payroll Information",
                            "employee_name"=>$tempEmployeeName, "created_by"=>$createdEmployeeName, 
                            "raw_data"=>$queryApprovals->result(), "count"=>$queryApprovals->num_rows());

                        $htmlContent = $this->load->view("hris/email_templates/email-for_approval", $emailData, true);
                        $overrideMailer = array();
                        $module = "hris_payroll_approval";
                        $email_title = "HRIS - Payroll Approval";
                        $content_title = "Payroll Information Approval For ".$tempEmployeeName;
                        try{
                            $sent = $this->core_layout->send_email($module, $email_title, $content_title, $htmlContent, $overrideMailer);
                            if($sent){ $emailSent = true; }
                        }catch(Exception $e){
                            $resultarray["approval_email_notification"] = $e->getMessage();
                        }
                    }
                }

                $explodedApproval = implode(", ", $forApprovalFields);
                $type = count($forApprovalFields) > 1 ? "are": "is";
                $resultarray["for_approval"] = true;
                $resultarray["email_sent"] = $emailSent;
                $resultarray["approval_notification"] = "The following field(s) `{$explodedApproval}` {$type} for approval status.";
            }

            return $resultarray;
        }

        public function updateEmployeeAllowance() {
            $post = $this->input->post();
            $id = $post["id"];
            $hasApprovingAuthority = isset($post["approving_authority"]) ? json_decode($post["approving_authority"]): false;
            unset($post["id"], $post["approving_authority"]);
            $postRate = isset($post['rate']) && $post['rate'] ? floatval($post['rate']): 0;
            $postIsActive = isset($post["is_active"]) && $post['is_active'] ? 1 : 0;
            $resultSet = array();

            /*** edited contents logging ***/
            $editedContent = array();
            $fromContent = array();
            $tempKeys = array("rate"=>"Rate", "frequency"=>"Frequency", "is_active"=>"Status");
            
            $this->db->select("allw.rate, allw.frequency, allw.is_active, allw.emp_id, CONCAT(UPPER(TRIM(emp.firstname)), ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END,' ', UPPER(TRIM(emp.lastname)),
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
            END) as employee_name");
            $this->db->from("gcchris.allowances as allw");
            $this->db->join("gccmaster.tblemployees as emp", "emp.id = allw.emp_id", "INNER");
            $this->db->where("allw.id", $id);
            $allw = $this->db->get();

            $tempEmployeeName = "No Employee Name";
            $transactionDate = Date("Y-m-d H:i:s");
            $employeeId = 0;
            if($allw->num_rows() === 1){
                $row = $allw->row();
                $tempEmployeeName = $row->employee_name;
                $employeeId = $row->emp_id;

                $currentRate = $row->rate ? floatval($row->rate): 0;
                $currentState = $row->is_active ? intval($row->is_active): 0;

                if($currentRate !== $postRate){
                    $editedContent["rate"] = number_format($post['rate'], 2, ".", ","); 
                    $fromContent["rate"] = number_format($row->rate, 2, ".", ",");
                }

                if((isset($post['frequency']) && $post['frequency'] && $row->frequency) && $row->frequency !== $post['frequency']){
                    $editedContent["frequency"] = $post['frequency']; 
                    $fromContent["frequency"] = $row->frequency;
                }

                if($currentState !== $postIsActive){
                    $editedContent["is_active"] = intval($post['is_active']) === 1 ? "Active": "Inactive"; 
                    $fromContent["is_active"] = intval($row->is_active) === 1 ? "Active": "Inactive";
                }
            }
            $this->db->reset_query();
            /*** edited contents logging ***/

            /*** for approval ***/
            $forApproval = array();
            foreach ($editedContent as $key => $value) {
                if($fromContent[$key]){
                    $isNumeric = false;
                    if(is_numeric($value)){
                        $isNumeric = true;
                        $value = floatval($value);
                        $fromContent[$key] = floatval($fromContent[$key]);
                    }

                    if($isNumeric && $value > 0 && $fromContent[$key] > 0 && $value !== $fromContent[$key]){
                        $forApproval[$key] = $value;
                    }else if($isNumeric === false && $value !== $fromContent[$key]){
                        $forApproval[$key] = $value;
                    }
                }
            }

            $tempData = array();
            foreach ($tempKeys as $key => $value) {
                if((!isset($forApproval[$key]) || $hasApprovingAuthority) && isset($editedContent[$key])){ $tempData[$key] = $post[$key]; }
            }

            /*** for approval ***/           

            $updated = false;
            $forApprovalData = is_array($forApproval) && count($forApproval) > 0;
            $cancelApprovalWhere = array("unique_id"=>$employeeId, "table_id"=>$id, "database_table"=>"gcchris.allowances", "module"=>"hris", "is_approved"=>0);

            if(is_array($tempData) && count($tempData) > 0){
                $this->db->where("id", $id);
                $this->db->set($tempData);
                $updated = $this->db->update("gcchris.allowances");
                /*** 
                 * $tempUpdate = $this->db->update("gcchris.allowances");
                 * $updated = $tempUpdate && $this->db->affected_rows() > 0; 
                 * ***/
            }

            $coreHistoryLog = $this->core_layout->coreHistoryLogs();
            $coreHistoryLog->setHistoryLogModule("hris");
            $coreHistoryLog->setHistoryLogTableName("gcchris.allowances");
            $coreHistoryLog->setHistoryLogTableFieldId($id);
            $coreHistoryLog->setHistoryLogEmployeeId($employeeId);

            if($updated && $this->db->trans_status() === TRUE){
                $resultSet["success"] = TRUE;
                $resultSet["message"] = "Allowance data has been updated successfully.";
                $resultSet["title"] = "Update Allowance Data";
                $resultSet["toast"] = "success";

                if($hasApprovingAuthority){
                    $historyStatus = $this->set_approved_allowance($post);

                    if($historyStatus){
                        $resultarray['salary_history'] = 'Salary History Generated.';
                    }else{
                        $resultarray['salary_history'] = 'Failed to generate Salary History.';
                    }
                }

                if(is_array($tempData) && count($tempData) > 0){
                    foreach ($tempData as $key => $value) {
                        if(isset($tempKeys[$key]) && $tempKeys[$key]){
                            $cancelApprovalWhere["table_field"] = $key;
                            $this->db->update("gccmaster.field_value_approval", 
                            array("is_approved"=>3, "approval_by"=>$this->core_layout->getCurrentEmployeeId(), "approval_at"=>$transactionDate),
                            $cancelApprovalWhere);
                            $this->db->reset_query();
                            
                            $nKey = $tempKeys[$key];
                            if(isset($tempData[$key]) && $tempData[$key]){
                                $fromValue = isset($fromContent[$key]) && $fromContent[$key] ? $fromContent[$key]: null;
                                $toValue = isset($editedContent[$key]) && $editedContent[$key] ? $editedContent[$key]: null;
                                
                                $logMessage = $fromValue ? 
                                    "Employee named `$tempEmployeeName` with payroll allowance data field `$nKey` has been updated from `$fromValue` to `$toValue`.": 
                                    "Employee named `$tempEmployeeName` with payroll allowance data field `$nKey` has been updated into `$toValue`.";
                                $coreHistoryLog->setEventLog($logMessage, $fromValue ? "update": "insert", "success", "gcchris", "user");
                                $coreHistoryLog->saveLoggedEventHistory();
                            }
                        }
                    }
                }
            }else{
                $resultSet["success"] = FALSE;
                $resultSet["message"] = "Error processing request!";
                $resultSet["title"] = "Error Updating Allowance Data";
                $resultSet["toast"] = "error";

                if($forApprovalData){
                    $resultSet["message"] = "Updating of payroll allowance data is currently for approval status!";
                    $resultSet["toast"] = "warning";
                }else{
                    $coreHistoryLog->setEventLog("Failed to update payroll allowance data of employee named `$tempEmployeeName`.","update", "error", "gcchris", "user");
                    $coreHistoryLog->saveLoggedEventHistory();
                }
            }
            /*** edited contents logging ***/

        $resultSet["for_approval"] = false;
        $resultSet["approval_email_notification"] = null;
        if(is_array($forApproval) && count($forApproval) > 0 && $hasApprovingAuthority === false){
            $forApprovalFields = array();
            $forApprovalIds = array();
            foreach ($forApproval as $key => $value) {
                if($value === 'day'){ $value = 'daily'; }
                if($value === 'month'){ $value = 'monthly'; }

                $originalValue = isset($post[$key]) && $post[$key] ? $post[$key]: null;
                $originalValue = isset($post[$key]) && is_numeric($post[$key]) ? $post[$key]: $originalValue;

                $tempDescription = isset($tempKeys[$key]) && $tempKeys[$key] ? $tempKeys[$key]: "No description";
                $_tempData = array("unique_id"=>$employeeId, "module"=>"hris", "field_description"=>$tempDescription, 
                "database_table"=>"gcchris.allowances", "table_id"=>$id, 
                "table_field"=>$key, "table_value"=>$value, "original_value"=>$originalValue,
                "created_by"=>$this->core_layout->getCurrentEmployeeId(), "created_at"=>$transactionDate);
                
                $added = $this->db->insert("gccmaster.field_value_approval", $_tempData);
                if($added){
                    $forApprovalIds[] = $this->db->insert_id();
                    $forApprovalFields[] = $tempDescription;
                    $fromValue = $fromContent[$key];
                    $toValue = $value;

                    $logMessage = $fromValue ? 
                            "Employee named `$tempEmployeeName` with payroll allowance data field `$tempDescription` and value of from `$fromValue` to `$toValue` is for approval status.": 
                            "Employee named `$tempEmployeeName` with payroll allowance data field `$tempDescription` and value of `$toValue` is for approval status.";
                        $coreHistoryLog->setEventLog($logMessage, $fromValue ? "update": "insert", "success", "gcchris", "user");
                        $coreHistoryLog->saveLoggedEventHistory();
                }
            }

            $emailSent = false;
            if(is_array($forApprovalIds) && count($forApprovalIds) > 0){
                $this->db->select("field_description, table_value");
                $this->db->from("gccmaster.field_value_approval");
                $this->db->where("is_approved", 0);
                $this->db->where_in("id", $forApprovalIds);
                $queryApprovals = $this->db->get();
                if($queryApprovals->num_rows() > 0){
                    $createdEmployeeData = (object) $this->core_layout->getEmployeeData($this->core_layout->getCurrentEmployeeId());
                    $createdEmployeeName = $createdEmployeeData->display_name_1 ? $createdEmployeeData->display_name_1: "No employee name";

                    $emailData = array("date"=>$transactionDate, "list_header"=>"Allowance Information",
                        "employee_name"=>$tempEmployeeName, "created_by"=>$createdEmployeeName, 
                        "raw_data"=>$queryApprovals->result(), "count"=>$queryApprovals->num_rows());

                    $htmlContent = $this->load->view("hris/email_templates/email-for_approval", $emailData, true);
                    $overrideMailer = array();
                    $module = "hris_payroll_approval";
                    $email_title = "HRIS - Payroll Approval";
                    $content_title = "Payroll Information Approval For ".$tempEmployeeName;
                    try{
                        $sent = $this->core_layout->send_email($module, $email_title, $content_title, $htmlContent, $overrideMailer);
                        if($sent){ $emailSent = true; }
                    }catch(Exception $e){
                        $resultSet["approval_email_notification"] = $e->getMessage();
                    }
                }
            }
            
            $explodedApproval = implode(",", $forApprovalFields);
            $type = count($forApprovalFields) > 1 ? "are": "is";
            $resultSet["for_approval"] = true;
            $resultSet["email_sent"] = $emailSent;
            $resultSet["approval_notification"] = "The following field(s) `{$explodedApproval}` {$type} for approval status.";
        }

            /*** $this->db->where("id", $id);
            $this->db->set($post);
            $update = $this->db->update("gcchris.allowances");
            $resultSet["success"] = $update;
            $resultSet["message"] = $update ? "Allowance data has been updated successfully." : $this->db->error()["message"];
            $resultSet["title"] = $update ? "Update Allowance Data" : "Error Updating Allowance Data";
            $resultSet["toast"] = $update ? "success" : "error"; ***/

            //exec log event for allowance
            //$update ? $this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated employee allowance data with db id no. ".$id,"update", "success", "gcchris", "user") : $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed updating employee allowance data with db id no. ".$id,"insert", "error", "gcchris", "user");
            
            /*** edited contents logging ***/
            
            /*** if ($this->db->trans_status() === FALSE) {
                $this->core_layout->setEventLog("Failed to update payroll allowance data of employee named `$tempEmployeeName`.","update", "error", "gcchris", "user");
            }else{
                if(is_array($editedContent) && count($editedContent) > 0){
                    foreach ($editedContent as $key => $value) {
                        if(isset($tempKeys[$key]) && $tempKeys[$key]){
                            $nKey = $tempKeys[$key];
                            if(isset($editedContent[$key]) && $editedContent[$key]){
                                $fromValue = $fromContent[$key];
                                $toValue = $editedContent[$key];
                                
                                $logMessage = $fromValue ? 
                                    "Employee named `$tempEmployeeName` with payroll allowance data field `$nKey` has been updated from `$fromValue` to `$toValue`. `$id`.": 
                                    "Employee named `$tempEmployeeName` with payroll allowance data field `$nKey` has been updated into `$toValue`. `$id`.";
                                $this->core_layout->setEventLog($logMessage, $fromValue ? "update": "insert", "success", "gcchris", "user");
                            }
                        }
                    }
                }
            } ***/
            /*** edited contents logging ***/

            return $resultSet;
        }

        function updateEmployeeBenefits() {
            $post = $this->input->post();
            $id = $post["id"];
            unset($post["id"]);
            $resultSet = array();

            $this->db->where("id", $id);
            $this->db->set($post);
            $update = $this->db->update("gcchris.benefits");
            $resultSet["success"] = $update;
            $resultSet["message"] = $update ? "Benefits was successfully updated." : $this->db->error()["message"];
            $resultSet["title"] = $update ? "Benefits updated." : "Error";
            $resultSet["toast"] = $update ? "success" : "error";

            //exec log event for benefits
            $update ? $this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated employee benefits data with db id no. ".$id,"update", "success", "gcchris", "user") : $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed updating employee benefits data with db id no. ".$id,"insert", "error", "gcchris", "user");

            return $resultSet;
        }

        function updateEmployeeLoan($id) {
            $post = $this->arrayToStdClass($this->input->post());
            $post->amount = str_replace(",", "", $post->amount);
            $post->reference = trim($post->reference);

            if (intval($post->deduction_type) === 1) {
                $post->fixed_deduction_amt = str_replace(",", "", $post->deduct_type_value);
                $post->percentage = 0;
            } else {
                $post->fixed_deduction_amt = 0;
                $post->percentage = str_replace(",", "", $post->deduct_type_value);
            }

            unset($post->deduct_type_value);
            if(isset($post->remarks) && !$post->remarks){ unset($post->remarks); }

            $this->db->where("id", $id);
            $this->db->set($post);
            $update = $this->db->update("gcchris.loans");

            $resultSet = array();

            $resultSet["success"] = $update;
            if ($update) {
                $resultSet["message"] = "Employee Loan was updated successfully.";
                $resultSet["title"] = "Loan Updated";
                $resultSet["toast"] = "success";
            } else {
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "Error Occurred.";
                $resultSet["toast"] = "error";
            }

            $update ? $this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated employee loan data with db id no. ".$id,"update", "success", "gcchris", "user") : $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed updating employee loan data with db id no. ".$id,"insert", "error", "gcchris", "user");

            return $resultSet;
        }

        private function arrayToStdClass($array) {
            return json_decode(json_encode($array));
        }

        function getEmployeeLoanPaymentHistory($id) {
            $resultSet = array();

            $this->db->select("psloanpayments.*, ps.date_start, ps.date_end, ps.pay_date, ps.posted_by, ps.posted_at, emp.firstname, emp.lastname");
            $this->db->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id", "INNER");
            $this->db->join("gccmaster.tblemployees emp", "ps.posted_by = emp.id", "LEFT");
            $this->db->where("psloanpayments.loan_id", $id);
            $this->db->order_by("ps.pay_date", "DESC");
            $resultSet["data"] = $this->db->get("payroll.payroll_sheet_loan_payments psloanpayments")->result();
            return $resultSet;
        }

        function getEmployeeLoanInterestChargeHistory($id) {
            $resultSet = array();

            $this->db->select("psloaninterest.*, ps.date_start, ps.date_end, ps.pay_date, ps.posted_by, ps.posted_at, emp.firstname, emp.lastname");
            $this->db->join("payroll.payroll_sheet ps", "ps.id = psloaninterest.payroll_sheet_id", "INNER");
            $this->db->join("gccmaster.tblemployees emp", "ps.posted_by = emp.id", "LEFT");
            $this->db->where("psloaninterest.loan_id", $id);
            $this->db->where("ps.posted", 1);
            $this->db->order_by("ps.pay_date", "DESC");
            $resultSet["data"] = $this->db->get("payroll.payroll_sheet_loan_interest_payments psloaninterest")->result();
            return $resultSet;
        }

        function saveOneMonthDaysGapEmployeeSetup(){
            $resultSet = array();
            $post = $this->input->post();
            $post["created_by"] = $this->loggedinData["emp_id"];
            $insert = $this->db->insert("gcchris.tbldaysgapsetup", $post);

            if($insert):
                $resultSet["message"] = "Data successfully saved.";
                $resultSet["status"] = TRUE;
                $this->addEventLog("success","User ".$this->loggedInUsername." has inserted new one month days gap for displaying nearing one month employees with the value of ".$post['days'].".","gcchris.tbldaysgapsetup",$this->db->insert_id());
            else:
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["toast"] = FALSE;
                $this->addEventLog("success","User ".$this->loggedInUsername." has error ".$this->db->error()["message"],"gcchris.tbldaysgapsetup",0);
            endif;

            return $resultSet;
        }

        function getDaysGapNearingOnemonth(){
            $this->db->select("*");
            $this->db->from("gcchris.tbldaysgapsetup");
            $this->db->order_by("id","DESC");
            $this->db->limit(1);
            $query = $this->db->get();
            return $query->row_array()["days"];
        }

        function getNearingOneMonthEmployees(){
            $today = date("Y-m-d");
            $data = array();
            $this->db->select("gccmaster.tblemployees.id,gccmaster.tblemployees.firstname,gccmaster.tblemployees.lastname,gccmaster.tblemployees.suffix,gccmaster.tblemployees.middlename,gccmaster.tblemployees.date_start,gccmaster.tblemployees.pic_filename,gcchris.tblposition.name");
            $this->db->from("gccmaster.tblemployees");
            $this->db->join("gcchris.tblposition","gcchris.tblposition.id = gccmaster.tblemployees.position", "LEFT");
            $this->db->where("employee_status","Active");
            $employeeCollection = $this->db->get();

            if($employeeCollection->num_rows() > 0):
                foreach($employeeCollection->result_array() as $_employeeCollection):
                    $row = array();
                    $monthdate = strtotime($_employeeCollection["date_start"]);
                    $final = date("Y-m-d", strtotime("+1 month", $monthdate));
                    $datetoalert = date("Y-m-d", strtotime("-".$this->getDaysGapNearingOnemonth()." days", strtotime($final)));

                    $currentImage = base_url("assets/images/profile/no_image.jpg");
                    $imageFile = $_employeeCollection["pic_filename"];
                    $imagePath = "uploads/files/images/employee_files/empcode_" . $_employeeCollection["id"] . "/" . $imageFile;
                    $image = null;

                    if (file_exists(realpath($imagePath))) {
                        $image = base_url($imagePath);
                    } else {
                        $image = $currentImage;
                    }

                    $tempPost = (array)$_employeeCollection;
                    $fullname = $this->core_layout->getDisplayName($tempPost);
                    $tempFullname = (object)$fullname;
                    if($datetoalert === $today):
                        $row["id"] = $_employeeCollection["id"];
                        $row["fullname"] = $tempFullname->display_name_1;
                        $row["position"] = strtoupper($_employeeCollection["name"]);
                        $row["profile_pic"] = $image;
                        $data[] = $row;
                    endif;
                    
                endforeach;
            else:
            endif;
            return $data;
        }

        public function getActiveEmployeeContributionData(){
            $this->db->select("UPPER(CONCAT(emp.lastname,
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END, ', ', emp.firstname, ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END)) as employee_name, TRIM(IFNULL(emp.sss_no, '')) as sss_no, TRIM(IFNULL(emp.phealth_no, '')) as phealth_no, 
            TRIM(IFNULL(emp.pagibig_no, '')) as pagibig_no, TRIM(IFNULL(emp.tin_no, '')) as tin_no, 
            TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
            TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, TRIM(emp.mobile_no) as mobile_no");
            $this->db->from($this->employeeTable." as emp");
            $this->db->join($this->companyTable." as comp", "comp.id = emp.company_id", "LEFT");
            $this->db->join($this->departmentTable." as dept", "dept.id = emp.department_id", "LEFT");
            $this->db->where("emp.employee_status", "Active");
            $this->db->order_by("TRIM(emp.lastname)", "ASC");
            $query = $this->db->get();
            return $query->result();
        }

        public function getOnboardingActiveEmployees($month=null, $year=null, $date=null, $isWeekly=false){
            $resultset = array();
            if($month && $year){
                $dateStart = date("Y-m-d", strtotime("{$year}-{$month}-01"));
                $nextDate = date('Y-m-d', strtotime('+1 month', strtotime($dateStart)));
                $dateEnd = date('Y-m-d', strtotime('-1 day', strtotime($nextDate)));

                if($isWeekly && $date){
                    $dateStart = date("Y-m-d", strtotime($date));
                    $nextDate = date("Y-m-d", strtotime("+1 day", strtotime($dateStart)));
                    $dateEnd = date('Y-m-d', strtotime('+4 day', strtotime($nextDate)));
                }

                $resultset["is_weekly"] = $isWeekly;
                $resultset["dateStart"] = $dateStart;
                $resultset["dateEnd"] = $dateEnd;

                $this->db->select("emp.idno, UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name, 
                UPPER(IF(dpthead.id IS NULL, 'No Department Head', CONCAT(dpthead.lastname,
                CASE WHEN UPPER(TRIM(dpthead.suffix)) != 'N/A' AND
                    UPPER(TRIM(dpthead.suffix !='NONE')) AND dpthead.suffix !='' AND
                    dpthead.suffix IS NOT NULL THEN CONCAT(' ', dpthead.suffix) ELSE ''
                END, ', ', dpthead.firstname, ' ',
                CASE WHEN UPPER(TRIM(dpthead.middlename)) != 'N/A' AND UPPER(TRIM(dpthead.middlename)) != 'NONE' AND
                        TRIM(dpthead.middlename) !='' AND dpthead.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(dpthead.middlename, 1, 1), '.') ELSE ''
                END))) as department_head, 
                TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
                TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, 
                TRIM(UPPER(IF(pos.id IS NULL, emp.position, pos.name))) as position, emp.date_start");
                $this->db->from($this->employeeTable." emp");
                $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id", "LEFT");
                $this->db->join($this->positionTable." pos", "pos.id = emp.position", "LEFT");
                $this->db->join($this->employeeTable." dpthead", "dpthead.id = dept.head_id", "LEFT");
                $this->db->where("emp.date_start >=", $dateStart);
                $this->db->where("emp.date_start <=", $dateEnd);
                $this->db->order_by("emp.date_start, emp.lastname, emp.firstname", "ASC");
                $queryEmployees = $this->db->get();
                if($queryEmployees->num_rows() > 0){
                    $resultset["response"] = true;
                    $resultset["data"] = $queryEmployees->result();
                    $resultset["count"] = $queryEmployees->num_rows();
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }

            return $resultset;
        }

        public function getSeparatedInactiveEmployees($month=null, $year=null, $date=null, $isWeekly=false){
            $resultset = array();
            if($month && $year){
                $dateStart = date("Y-m-d", strtotime("{$year}-{$month}-01"));
                $nextDate = date('Y-m-d', strtotime('+1 month', strtotime($dateStart)));
                $dateEnd = date('Y-m-d', strtotime('-1 day', strtotime($nextDate)));

                if($isWeekly && $date){
                    $dateStart = date("Y-m-d", strtotime($date));
                    $nextDate = date("Y-m-d", strtotime("+1 day", strtotime($dateStart)));
                    $dateEnd = date('Y-m-d', strtotime('+4 day', strtotime($nextDate)));
                }

                $resultset["is_weekly"] = $isWeekly;
                $resultset["dateStart"] = $dateStart;
                $resultset["dateEnd"] = $dateEnd;

                $this->db->select("emp.idno, UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name, 
                UPPER(IF(dpthead.id IS NULL, 'No Department Head', CONCAT(dpthead.lastname,
                CASE WHEN UPPER(TRIM(dpthead.suffix)) != 'N/A' AND
                    UPPER(TRIM(dpthead.suffix !='NONE')) AND dpthead.suffix !='' AND
                    dpthead.suffix IS NOT NULL THEN CONCAT(' ', dpthead.suffix) ELSE ''
                END, ', ', dpthead.firstname, ' ',
                CASE WHEN UPPER(TRIM(dpthead.middlename)) != 'N/A' AND UPPER(TRIM(dpthead.middlename)) != 'NONE' AND
                        TRIM(dpthead.middlename) !='' AND dpthead.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(dpthead.middlename, 1, 1), '.') ELSE ''
                END))) as department_head, 
                TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
                TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, 
                TRIM(UPPER(IF(pos.id IS NULL, emp.position, pos.name))) as position, emp.date_end");
                $this->db->from($this->employeeTable." emp");
                $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id", "LEFT");
                $this->db->join($this->positionTable." pos", "pos.id = emp.position", "LEFT");
                $this->db->join($this->employeeTable." dpthead", "dpthead.id = dept.head_id", "LEFT");
                $this->db->where("emp.date_end >=", $dateStart);
                $this->db->where("emp.date_end <=", $dateEnd);
                $this->db->order_by("emp.date_end, emp.lastname, emp.firstname", "ASC");
                $queryEmployees = $this->db->get();
                if($queryEmployees->num_rows() > 0){
                    $resultset["response"] = true;
                    $resultset["data"] = $queryEmployees->result();
                    $resultset["count"] = $queryEmployees->num_rows();
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }

            return $resultset;
        }

        public function getActiveEmployeesFilter($filterOption=null, $dateStart=null, $dateEnd=null, $additionalFilters=array(), $sortOrder=array()){
            if($filterOption && $dateStart && $dateEnd){
                $this->db->select("emp.idno, UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name, 
                UPPER(IF(dpthead.id IS NULL, 'No Department Head', CONCAT(dpthead.lastname,
                CASE WHEN UPPER(TRIM(dpthead.suffix)) != 'N/A' AND
                    UPPER(TRIM(dpthead.suffix !='NONE')) AND dpthead.suffix !='' AND
                    dpthead.suffix IS NOT NULL THEN CONCAT(' ', dpthead.suffix) ELSE ''
                END, ', ', dpthead.firstname, ' ',
                CASE WHEN UPPER(TRIM(dpthead.middlename)) != 'N/A' AND UPPER(TRIM(dpthead.middlename)) != 'NONE' AND
                        TRIM(dpthead.middlename) !='' AND dpthead.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(dpthead.middlename, 1, 1), '.') ELSE ''
                END))) as department_head, 
                TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
                TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, 
                TRIM(UPPER(IF(pos.id IS NULL, emp.position, pos.name))) as position, {$filterOption}");
                $this->db->from($this->employeeTable." emp");
                $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id", "LEFT");
                $this->db->join($this->positionTable." pos", "pos.id = emp.position", "LEFT");
                $this->db->join($this->employeeTable." dpthead", "dpthead.id = dept.head_id", "LEFT");
                $this->db->where("{$filterOption} >=", $dateStart);
                $this->db->where("{$filterOption} <=", $dateEnd);
                if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                $this->db->group_start();
                foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                $this->db->group_end();
                }

                $defaultSortOrder = "ASC";
                $arrSortOrder = array();

                if(isset($sortOrder) && is_array($sortOrder) && count($sortOrder) > 0){
                    $tempSort = (object) $sortOrder;
                    if(!in_array($tempSort->sort_by, $arrSortOrder)){ $arrSortOrder[] = $tempSort->sort_by; }
                    $defaultSortOrder = $tempSort->sort_order;
                }
                
                if(is_array($arrSortOrder) && count($arrSortOrder) > 0){
                    $tempString = implode(",", $arrSortOrder);
                    $this->db->order_by($tempString, $defaultSortOrder);
                }else{
                    $this->db->order_by("{$filterOption}, emp.lastname, emp.firstname", $defaultSortOrder);
                }
                return $this->db->get();
            }else{ return false; }
        }

        public function getAllActiveEmployeesFilter($filterOption=null, $additionalFilters=array(), $sortOrder=array()){
            if($filterOption){
                $this->db->select("emp.idno, UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name, 
                UPPER(IF(dpthead.id IS NULL, 'No Department Head', CONCAT(dpthead.lastname,
                CASE WHEN UPPER(TRIM(dpthead.suffix)) != 'N/A' AND
                    UPPER(TRIM(dpthead.suffix !='NONE')) AND dpthead.suffix !='' AND
                    dpthead.suffix IS NOT NULL THEN CONCAT(' ', dpthead.suffix) ELSE ''
                END, ', ', dpthead.firstname, ' ',
                CASE WHEN UPPER(TRIM(dpthead.middlename)) != 'N/A' AND UPPER(TRIM(dpthead.middlename)) != 'NONE' AND
                        TRIM(dpthead.middlename) !='' AND dpthead.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(dpthead.middlename, 1, 1), '.') ELSE ''
                END))) as department_head, 
                TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
                TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, 
                TRIM(UPPER(IF(pos.id IS NULL, emp.position, pos.name))) as position, {$filterOption}");
                $this->db->from($this->employeeTable." emp");
                $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id", "LEFT");
                $this->db->join($this->positionTable." pos", "pos.id = emp.position", "LEFT");
                $this->db->join($this->employeeTable." dpthead", "dpthead.id = dept.head_id", "LEFT");
                $this->db->where("{$filterOption} !=", NULL);
                $this->db->where("{$filterOption} !=", "0000-00-00");
                $this->db->where("TRIM({$filterOption}) !=", "");
                $this->db->where("YEAR({$filterOption}) !=", "0000");
                if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                $this->db->group_start();
                foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                $this->db->group_end();
                }

                $defaultSortOrder = "ASC";
                $arrSortOrder = array();

                if(isset($sortOrder) && is_array($sortOrder) && count($sortOrder) > 0){
                    $tempSort = (object) $sortOrder;
                    if(!in_array($tempSort->sort_by, $arrSortOrder)){ $arrSortOrder[] = $tempSort->sort_by; }
                    $defaultSortOrder = $tempSort->sort_order;
                }
                
                if(is_array($arrSortOrder) && count($arrSortOrder) > 0){
                    $tempString = implode(",", $arrSortOrder);
                    $this->db->order_by($tempString, $defaultSortOrder);
                }else{
                    $this->db->order_by("{$filterOption}, emp.lastname, emp.firstname", $defaultSortOrder);
                }
                return $this->db->get();
            }else{ return false; }
        }

        public function getActiveManpowerFilter($dateStart=null, $dateEnd=null, $additionalFilters=array()){
            if($dateStart && $dateEnd){
                $this->db->select("comp.id, comp.code, COUNT(emp.id) as total_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'REGULAR', emp.id, null)) as regular_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'PROBATIONARY', emp.id, null)) as probi_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'RETIRED', emp.id, null)) as retired_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'PART-TIME', emp.id, null)) as ptime_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'PROJECT BASED', emp.id, null)) as pbase_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'NO CONTRACT', emp.id, null)) as ncont_count,  
                    GROUP_CONCAT(DISTINCT emp.department_id) as department
                ");
                $this->db->from($this->companyTable." comp");
                $this->db->join($this->employeeTable." emp", "emp.company_id = comp.id", "LEFT");
                $this->db->join($this->departmentTable." dept", "emp.department_id = dept.id", "LEFT");

                /*** if(isset($additionalFilters['loc.location_name']) && $additionalFilters['loc.location_name']){
                    $this->db->join($this->tblPersonnel.' prsnl', 'prsnl.biometric_id = emp.biometricno OR prsnl.biometricno = emp.biometricno', 'LEFT');
                    $this->db->join($this->tblPersonnelLocation." loc", "loc.personnel_id = prsnl.id", "LEFT");
                } ***/
               if(isset($additionalFilters['dsl.station_id']) && $additionalFilters['dsl.station_id']){
                $this->db->join($this->defaultStationTable." dsl", "dsl.employee_id = emp.id", "LEFT");
               }

                $this->db->where("emp.employee_status", "Active");
                $this->db->group_start();
                $this->db->where("emp.date_start >=", $dateStart);
                $this->db->where("emp.date_start <=", $dateEnd);
                $this->db->group_end();
                if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                $this->db->group_start();
                foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                $this->db->group_end();
                }
                $this->db->order_by("comp.code", "ASC");
                $this->db->group_by("comp.code");
                return $this->db->get();
            }else{ return false; }
        }

        public function getAllActiveManpowerFilter($additionalFilters=array()){
            $this->db->select("comp.id, comp.code, COUNT(emp.id) as total_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'REGULAR', emp.id, null)) as regular_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'PROBATIONARY', emp.id, null)) as probi_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'RETIRED', emp.id, null)) as retired_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'PART-TIME', emp.id, null)) as ptime_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'PROJECT BASED', emp.id, null)) as pbase_count, 
                    COUNT(IF(TRIM(UPPER(emp.work_status)) = 'NO CONTRACT', emp.id, null)) as ncont_count,  
                    GROUP_CONCAT(DISTINCT emp.department_id) as department
                ");
                $this->db->from($this->companyTable." comp");
                $this->db->join($this->employeeTable." emp", "emp.company_id = comp.id", "LEFT");
                $this->db->join($this->departmentTable." dept", "emp.department_id = dept.id", "LEFT");

                /*** if(isset($additionalFilters['loc.location_name']) && $additionalFilters['loc.location_name']){
                    $this->db->join($this->tblPersonnel.' prsnl', 'prsnl.biometric_id = emp.biometricno OR prsnl.biometricno = emp.biometricno', 'LEFT');
                    $this->db->join($this->tblPersonnelLocation." loc", "loc.personnel_id = prsnl.id", "LEFT");
                } ***/

                if(isset($additionalFilters['dsl.station_id']) && $additionalFilters['dsl.station_id']){
                    $this->db->join($this->defaultStationTable." dsl", "dsl.employee_id = emp.id", "LEFT");
                }

                $this->db->where("emp.employee_status", "Active");
                if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                $this->db->group_start();
                foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                $this->db->group_end();
                }
                $this->db->order_by("comp.code", "ASC");
                $this->db->group_by("comp.code");
                return $this->db->get();
        }

        public function getActiveManpowerByCompany($dateStart=null, $dateEnd=null, $company=array(), $department = 0, $location = null){
            if($dateStart && $dateEnd && $company){
                $tempData = array();
                foreach ($company as $compId) {
                    $totalCount = 0;
                    $companyData = $this->db->get_where($this->companyTable, array("id"=>$compId));
                    $this->db->reset_query();
                    $tempData[$compId]["company"] = $companyData->num_rows() === 1 ? $companyData->row()->code: "No Assigned Company";

                    /*** $sqlSelect = "comp.id as comp_id, emp.idno, TRIM(UPPER(emp.lastname)) as lastname, TRIM(UPPER(emp.firstname)) as firstname, TRIM(UPPER(emp.middlename)) as middlename, 
                    TRIM(UPPER(dept.description)) as department, TRIM(UPPER(pos.name)) as position, emp.date_start, TRIM(UPPER(emp.work_status)) as work_status,
                    emp.basic_rate, IFNULL(allw.rate, 0) as allw_rate, 
                    GROUP_CONCAT(DISTINCT loc.location_name) as site_location, 
                    sal.sal_date as salary_date"; ***/

                    $sqlSelect = "comp.id as comp_id, emp.idno, TRIM(UPPER(emp.lastname)) as lastname, TRIM(UPPER(emp.firstname)) as firstname, TRIM(UPPER(emp.middlename)) as middlename, 
                    TRIM(UPPER(dept.description)) as department, TRIM(UPPER(pos.name)) as position, emp.date_start, TRIM(UPPER(emp.work_status)) as work_status,
                    emp.basic_rate, IFNULL(allw.rate, 0) as allw_rate, 
                    UPPER(IFNULL(dsl.station_description, '')) as site_location, 
                    sal.sal_date as salary_date";

                    $this->db->select($sqlSelect);
                    $this->db->from($this->employeeTable." emp");
                    $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                    $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id OR dept.code = emp.department_id OR dept.description = emp.department_id", "LEFT");
                    $this->db->join($this->positionTable." pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
                    $this->db->join($this->tblAllowances." allw", "allw.emp_id = emp.id AND allw.is_active = 1 AND allw.is_archived = 0", "LEFT");
                    /*** $this->db->join($this->tblPersonnel." prsnl", "prsnl.biometric_id = emp.biometricno OR prsnl.biometricno = emp.biometricno", "LEFT");
                    $this->db->join($this->tblPersonnelLocation." loc", "loc.personnel_id = prsnl.id", "LEFT"); ***/
                    $this->db->join($this->defaultStationTable." dsl", "dsl.employee_id = emp.id", "LEFT");
                    $this->db->join($this->employeeSalaryTable." sal", "sal.emp_id = emp.id AND sal.is_archived = 0", "LEFT");
                    $this->db->where("emp.employee_status", "Active");
                    $this->db->where("emp.payout_sched", 3);
                    $this->db->where("emp.date_start >=", $dateStart);
                    $this->db->where("emp.date_start <=", $dateEnd);
                    $this->db->where("comp.id", $compId);

                    if($department > 0){
                        $this->db->where('dept.id', $department);
                    }

                    if($location){
                        /*** $this->db->where('loc.location_name', $location); ***/
                        $this->db->where('dsl.station_id', $location);
                    }

                    $this->db->order_by("dept.code, emp.lastname, emp.firstname", "ASC");
                    $this->db->order_by("allw.id, sal.id", "DESC");
                    $this->db->group_by("emp.id");
                    $qWeekly = $this->db->get();
                    $totalCount += $qWeekly->num_rows();
                    $tempData[$compId]["weekly"]["count"] = $qWeekly->num_rows();
                    $tempData[$compId]["weekly"]["rows"] = $qWeekly->result();

                    $this->db->reset_query();

                    $this->db->select($sqlSelect);
                    $this->db->from($this->employeeTable." emp");
                    $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                    $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id OR dept.code = emp.department_id OR dept.description = emp.department_id", "LEFT");
                    $this->db->join($this->positionTable." pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
                    $this->db->join($this->tblAllowances." allw", "allw.emp_id = emp.id AND allw.is_active = 1 AND allw.is_archived = 0", "LEFT");
                    /*** $this->db->join($this->tblPersonnel." prsnl", "prsnl.biometric_id = emp.biometricno OR prsnl.biometricno = emp.biometricno", "LEFT");
                    $this->db->join($this->tblPersonnelLocation." loc", "loc.personnel_id = prsnl.id", "LEFT"); ***/
                    $this->db->join($this->defaultStationTable." dsl", "dsl.employee_id = emp.id", "LEFT");
                    $this->db->join($this->employeeSalaryTable." sal", "sal.emp_id = emp.id AND sal.is_archived = 0", "LEFT");
                    $this->db->where("emp.employee_status", "Active");
                    $this->db->where("emp.payout_sched !=", 3);
                    $this->db->where("emp.date_start >=", $dateStart);
                    $this->db->where("emp.date_start <=", $dateEnd);
                    $this->db->where("comp.id", $compId);

                    if($department > 0){
                        $this->db->where('dept.id', $department);
                    }

                    if($location){
                        /*** $this->db->where('loc.location_name', $location); ***/
                        $this->db->where('dsl.station_id', $location);
                    }

                    $this->db->order_by("dept.code, emp.lastname, emp.firstname", "ASC");
                    $this->db->order_by("allw.id, sal.id", "DESC");
                    $this->db->group_by("emp.id");
                    $qRegular = $this->db->get();
                    $totalCount += $qRegular->num_rows();
                    $tempData[$compId]["regular"]["count"] = $qRegular->num_rows();
                    $tempData[$compId]["regular"]["rows"] = $qRegular->result();

                    $this->db->reset_query();
                    $tempData[$compId]["_total_entries"] = $totalCount;

                    $select = "comp.id as comp_id, emp.idno, TRIM(UPPER(emp.lastname)) as lastname, TRIM(UPPER(emp.firstname)) as firstname, TRIM(UPPER(emp.middlename)) as middlename, 
                    TRIM(UPPER(dept.description)) as department, TRIM(UPPER(pos.name)) as position, YEAR(sal.sal_date) as sal_year, sal.sal_date, sal.sal_rate, sal.sal_remarks";

                    $this->db->select($select);
                    $this->db->from($this->employeeTable." emp");
                    $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                    $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id OR dept.code = emp.department_id OR dept.description = emp.department_id", "LEFT");
                    $this->db->join($this->positionTable." pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
                    $this->db->join($this->employeeSalaryTable." sal", "sal.emp_id = emp.id AND sal.is_archived = 0", "LEFT");
                    // $this->db->join($this->tblPersonnel." prsnl", "prsnl.biometric_id = emp.biometricno OR prsnl.biometricno = emp.biometricno", "LEFT");
                    // $this->db->join($this->tblPersonnelLocation." loc", "loc.personnel_id = prsnl.id", "LEFT");
                    $this->db->join($this->defaultStationTable." dsl", "dsl.employee_id = emp.id", "LEFT");
                    $this->db->where("emp.employee_status", "Active");
                    $this->db->where("emp.date_start >=", $dateStart);
                    $this->db->where("emp.date_start <=", $dateEnd);
                    $this->db->where("comp.id", $compId);

                    if($department > 0){
                        $this->db->where('dept.id', $department);
                    }

                    if($location){
                        /** $this->db->where('loc.location_name', $location); **/
                        $this->db->where('dsl.station_id', $location);
                    }

                    $this->db->order_by("dept.code, emp.lastname, emp.firstname", "ASC");
                    $this->db->order_by('sal.id', "DESC");
                    $qSalaryHistory = $this->db->get();
                    $tempData[$compId]["salary_history"]["count"] = $qSalaryHistory->num_rows();
                    $tempData[$compId]["salary_history"]["rows"] = $qSalaryHistory->result();
                    $tempData[$compId]["salary_history"]["sql"] = $this->db->last_query();

                    $this->db->reset_query();

                }

                return $tempData;
            }else{ return false; }
        }

        public function getAllActiveManpowerByCompany($company=array(), $department = 0, $location = null){
            if($company){
                $tempData = array();
                foreach ($company as $compId) {
                    $totalCount = 0;
                    $companyData = $this->db->get_where($this->companyTable, array("id"=>$compId));
                    $this->db->reset_query();
                    $tempData[$compId]["company"] = $companyData->num_rows() === 1 ? $companyData->row()->code: "No Assigned Company";

                    /*** $sqlSelect = "comp.id as comp_id, emp.idno, TRIM(UPPER(emp.lastname)) as lastname, TRIM(UPPER(emp.firstname)) as firstname, TRIM(UPPER(emp.middlename)) as middlename, 
                    TRIM(UPPER(dept.description)) as department, TRIM(UPPER(pos.name)) as position, emp.date_start, TRIM(UPPER(emp.work_status)) as work_status,
                    emp.basic_rate, IFNULL(allw.rate, 0) as allw_rate, 
                    GROUP_CONCAT(DISTINCT loc.location_name) as site_location, 
                    MAX(sal.sal_date) as salary_date, 
                    ROUND(DATEDIFF(CURDATE(), emp.date_start) / 30) as tenure"; ***/

                    $sqlSelect = "comp.id as comp_id, emp.idno, TRIM(UPPER(emp.lastname)) as lastname, TRIM(UPPER(emp.firstname)) as firstname, TRIM(UPPER(emp.middlename)) as middlename, 
                    TRIM(UPPER(dept.description)) as department, TRIM(UPPER(pos.name)) as position, emp.date_start, TRIM(UPPER(emp.work_status)) as work_status,
                    emp.basic_rate, IFNULL(allw.rate, 0) as allw_rate, 
                    UPPER(IFNULL(dsl.station_description, '')) as site_location, 
                    MAX(sal.sal_date) as salary_date, 
                    ROUND(DATEDIFF(CURDATE(), emp.date_start) / 30) as tenure";

                    $this->db->select($sqlSelect);
                    $this->db->from($this->employeeTable." emp");
                    $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                    $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id OR dept.code = emp.department_id OR dept.description = emp.department_id", "LEFT");
                    $this->db->join($this->positionTable." pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
                    $this->db->join($this->tblAllowances." allw", "allw.emp_id = emp.id AND allw.is_active = 1 AND allw.is_archived = 0", "LEFT"); 
                    /*** $this->db->join($this->tblPersonnel." prsnl", "prsnl.biometric_id = emp.biometricno OR prsnl.biometricno = emp.biometricno", "LEFT");
                    $this->db->join($this->tblPersonnelLocation." loc", "loc.personnel_id = prsnl.id", "LEFT"); ***/
                    $this->db->join($this->defaultStationTable." dsl", "dsl.employee_id = emp.id", "LEFT");
                    $this->db->join($this->employeeSalaryTable." sal", "sal.emp_id = emp.id AND sal.is_archived = 0", "LEFT");
                    $this->db->where("emp.employee_status", "Active");
                    $this->db->where("emp.payout_sched", 3);
                    $this->db->where("comp.id", $compId);

                    if($department > 0){
                        $this->db->where('dept.id', $department);
                    }

                    if($location){
                        /*** $this->db->where('loc.location_name', $location); ***/
                        $this->db->where('dsl.station_id', $location);
                    }

                    $this->db->order_by("dept.code, emp.lastname, emp.firstname", "ASC");
                    $this->db->order_by("allw.id, sal.id", "DESC");
                    $this->db->group_by("emp.id");
                    $qWeekly = $this->db->get();
                    $totalCount += $qWeekly->num_rows();
                    $tempData[$compId]["weekly"]["count"] = $qWeekly->num_rows();
                    $tempData[$compId]["weekly"]["rows"] = $qWeekly->result();

                    $this->db->reset_query();

                    $this->db->select($sqlSelect);
                    $this->db->from($this->employeeTable." emp");
                    $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                    $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id OR dept.code = emp.department_id OR dept.description = emp.department_id", "LEFT");
                    $this->db->join($this->positionTable." pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
                    $this->db->join($this->tblAllowances." allw", "allw.emp_id = emp.id AND allw.is_active = 1 AND allw.is_archived = 0", "LEFT");
                    /*** $this->db->join($this->tblPersonnel." prsnl", "prsnl.biometric_id = emp.biometricno OR prsnl.biometricno = emp.biometricno", "LEFT");
                    $this->db->join($this->tblPersonnelLocation." loc", "loc.personnel_id = prsnl.id", "LEFT"); ***/
                    $this->db->join($this->defaultStationTable." dsl", "dsl.employee_id = emp.id", "LEFT");
                    $this->db->join($this->employeeSalaryTable." sal", "sal.emp_id = emp.id AND sal.is_archived = 0", "LEFT");
                    $this->db->where("emp.employee_status", "Active");
                    $this->db->group_start();
                    $this->db->where("emp.payout_sched !=", 3);
                    $this->db->or_where("emp.payout_sched", NULL);
                    $this->db->group_end();
                    $this->db->where("comp.id", $compId);

                    if($department > 0){
                        $this->db->where('dept.id', $department);
                    }

                    if($location){
                        /*** $this->db->where('loc.location_name', $location); ***/
                        $this->db->where('dsl.station_id', $location);
                    }

                    $this->db->order_by("dept.code, emp.lastname, emp.firstname", "ASC");
                    $this->db->order_by("allw.id, sal.id", "DESC");
                    $this->db->group_by("emp.id");
                    $qRegular = $this->db->get();
                    $totalCount += $qRegular->num_rows();
                    $tempData[$compId]["regular"]["count"] = $qRegular->num_rows();
                    $tempData[$compId]["regular"]["rows"] = $qRegular->result();
                    $this->db->reset_query();
                    $tempData[$compId]["_total_entries"] = $totalCount;

                    $select = "comp.id as comp_id, emp.idno, TRIM(UPPER(emp.lastname)) as lastname, TRIM(UPPER(emp.firstname)) as firstname, TRIM(UPPER(emp.middlename)) as middlename, 
                    TRIM(UPPER(dept.description)) as department, TRIM(UPPER(pos.name)) as position, YEAR(sal.sal_date) as sal_year, sal.sal_date, sal.sal_rate, sal.sal_remarks";

                    $this->db->select($select);
                    $this->db->from($this->employeeTable." emp");
                    $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                    $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id OR dept.code = emp.department_id OR dept.description = emp.department_id", "LEFT");
                    $this->db->join($this->positionTable." pos", "pos.id = emp.position OR pos.name = emp.position", "LEFT");
                    $this->db->join($this->employeeSalaryTable." sal", "sal.emp_id = emp.id AND sal.is_archived = 0", "LEFT");
                    // $this->db->join($this->tblPersonnel." prsnl", "prsnl.biometric_id = emp.biometricno OR prsnl.biometricno = emp.biometricno", "LEFT");
                    // $this->db->join($this->tblPersonnelLocation." loc", "loc.personnel_id = prsnl.id", "LEFT");
                    $this->db->join($this->defaultStationTable." dsl", "dsl.employee_id = emp.id", "LEFT");
                    $this->db->where("emp.employee_status", "Active");
                    $this->db->where("comp.id", $compId);

                    if($department > 0){
                        $this->db->where('dept.id', $department);
                    }

                    if($location){
                        /** $this->db->where('loc.location_name', $location); **/
                        $this->db->where('dsl.station_id', $location);
                    }

                    $this->db->order_by("dept.code, emp.lastname, emp.firstname", "ASC");
                    $this->db->order_by('sal.id', "DESC");
                    $qSalaryHistory = $this->db->get();
                    $tempData[$compId]["salary_history"]["count"] = $qSalaryHistory->num_rows();
                    $tempData[$compId]["salary_history"]["rows"] = $qSalaryHistory->result();
                    $tempData[$compId]["salary_history"]["sql"] = $this->db->last_query();

                    $this->db->reset_query();
                }

                return $tempData;
            }else{ return false; }
        }

        public function getActiveTrainingSeminarFilter($dateStart=null, $dateEnd=null, $additionalFilters=array()){
            if($dateStart && $dateEnd){
                $sqlSelect = "UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name, TRIM(UPPER(trn.training)) as training, TRIM(UPPER(trn.train_institution)) as institution, 
                TRIM(UPPER(trn.train_conductor)) as conductor, TRIM(UPPER(trn.train_venue)) as venue, trn.train_from, trn.train_to, 
                TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
                TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, 
                TRIM(UPPER(IF(pos.id IS NULL, emp.position, pos.name))) as position, 
                UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END, ' | ', 
                TRIM(IF(comp.id IS NULL, emp.company_id, comp.code)), ' | ', 
                TRIM(IF(dept.id IS NULL, emp.department_id, dept.description)), ' | ',
                TRIM(IF(pos.id IS NULL, emp.position, pos.name)))) as employee_header";
                $this->db->select($sqlSelect);
                $this->db->from($this->employeeTrainingsTable." trn");
                $this->db->join($this->employeeTable." emp", "emp.id = trn.emp_id", "inner");
                $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id", "LEFT");
                $this->db->join($this->positionTable." pos", "pos.id = emp.position", "LEFT");
                $this->db->where("emp.employee_status", "Active");
                $this->db->where("trn.train_from >=", $dateStart);
                $this->db->where("trn.train_to <=", $dateEnd);

                if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                    if(array_key_exists("trn.training", $additionalFilters)){
                        $tempTraining = $additionalFilters["trn.training"];
                        unset($additionalFilters["trn.training"]);
                        $this->db->like("trn.training", $tempTraining, "both");
                    }

                    if(isset($additionalFilters) && count($additionalFilters) > 0){
                        $this->db->group_start();
                        foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                        $this->db->group_end();
                    }
                    /** original source code for filtering by date range */
                    // if(in_array("trn.training", $additionalFilters)){
                    //     $tempTraining = $additionalFilters["trn.training"];
                    //     unset($additionalFilters["trn.training"]);
                    //     $this->db->like("trn.training", $tempTraining, "both");
                    // }
                    // $this->db->group_start();
                    // foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                    // $this->db->group_end();
                    /** original source code for filtering by date range */
                }
                $this->db->order_by("emp.lastname, emp.firstname", "ASC");
                $this->db->order_by("trn.train_from", "DESC");
                return $this->db->get();
            }else{
                return false;
            }
        }

        public function getAllActiveTrainingSeminarFilter($additionalFilters=array()){
            $sqlSelect = "UPPER(CONCAT(emp.lastname,
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
            UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
            emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END, ', ', emp.firstname, ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
            TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END)) as employee_name, TRIM(UPPER(trn.training)) as training, TRIM(UPPER(trn.train_institution)) as institution, 
            TRIM(UPPER(trn.train_conductor)) as conductor, TRIM(UPPER(trn.train_venue)) as venue, trn.train_from, trn.train_to, 
            TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
            TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, 
            TRIM(UPPER(IF(pos.id IS NULL, emp.position, pos.name))) as position, 
            UPPER(CONCAT(emp.lastname,
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
            UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
            emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END, ', ', emp.firstname, ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
            TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END, ' | ', 
            TRIM(IF(comp.id IS NULL, emp.company_id, comp.code)), ' | ', 
            TRIM(IF(dept.id IS NULL, emp.department_id, dept.description)), ' | ',
            TRIM(IF(pos.id IS NULL, emp.position, pos.name)))) as employee_header";
            $this->db->select($sqlSelect);
            $this->db->from($this->employeeTrainingsTable." trn");
            $this->db->join($this->employeeTable." emp", "emp.id = trn.emp_id", "inner");
            $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
            $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id", "LEFT");
            $this->db->join($this->positionTable." pos", "pos.id = emp.position", "LEFT");
            $this->db->where("emp.employee_status", "Active");
            if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                if(isset($additionalFilters["trn.training"]) && $additionalFilters["trn.training"]){
                    $tempTraining = $additionalFilters["trn.training"];
                    unset($additionalFilters["trn.training"]);
                    $this->db->like("trn.training", $tempTraining, "both");
                }
            }
            if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                $this->db->group_start();
                foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                $this->db->group_end();
            }
            $this->db->order_by("emp.lastname, emp.firstname", "ASC");
            $this->db->order_by("trn.train_from", "DESC");
            return $this->db->get();
        }

        public function getActiveDriversLicenseFilter($dateStart=null, $dateEnd=null, $additionalFilters=array()){
            if($dateStart && $dateEnd){
                $sqlSelect = "UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name, TRIM(UPPER(dl.license_no)) as license_no, TRIM(UPPER(dl.restriction)) as restriction, dl.expiration_date, 
                TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
                TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, 
                TRIM(UPPER(IF(pos.id IS NULL, emp.position, pos.name))) as position, 
                IF(dl.expiration_date < CURDATE(), 1, 0) as expiration_flag, 
                UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END, ' | ', 
                TRIM(IF(comp.id IS NULL, emp.company_id, comp.code)), ' | ', 
                TRIM(IF(dept.id IS NULL, emp.department_id, dept.description)))) as employee_header";
                $this->db->select($sqlSelect);
                $this->db->from($this->employeeDriverLicenseTable." dl");
                $this->db->join($this->employeeTable." emp", "emp.id = dl.emp_id", "inner");
                $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id", "LEFT");
                $this->db->join($this->positionTable." pos", "pos.id = emp.position", "LEFT");
                $this->db->where("emp.employee_status", "Active");
                $this->db->where("dl.is_archived", 0);
                $this->db->where("dl.expiration_date >=", $dateStart);
                $this->db->where("dl.expiration_date <=", $dateEnd);
                if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                    $this->db->group_start();
                    foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                    $this->db->group_end();
                }
                $this->db->order_by("emp.lastname, emp.firstname", "ASC");
                $this->db->order_by("dl.expiration_date", "DESC");
                return $this->db->get();
            }else{
                return false;
            }
        }

        public function getAllActiveDriversLicenseFilter($additionalFilters=array()){
            $sqlSelect = "UPPER(CONCAT(emp.lastname,
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
            UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
            emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END, ', ', emp.firstname, ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
            TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END)) as employee_name, TRIM(UPPER(dl.license_no)) as license_no, TRIM(UPPER(dl.restriction)) as restriction, dl.expiration_date, 
            TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
            TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, 
            TRIM(UPPER(IF(pos.id IS NULL, emp.position, pos.name))) as position, 
            IF(dl.expiration_date < CURDATE(), 1, 0) as expiration_flag, 
            UPPER(CONCAT(emp.lastname,
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
            UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
            emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END, ', ', emp.firstname, ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
            TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
            THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END, ' | ', 
            TRIM(IF(comp.id IS NULL, emp.company_id, comp.code)), ' | ', 
            TRIM(IF(dept.id IS NULL, emp.department_id, dept.description)))) as employee_header";
            $this->db->select($sqlSelect);
            $this->db->from($this->employeeDriverLicenseTable." dl");
            $this->db->join($this->employeeTable." emp", "emp.id = dl.emp_id", "inner");
            $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
            $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id", "LEFT");
            $this->db->join($this->positionTable." pos", "pos.id = emp.position", "LEFT");
            $this->db->where("emp.employee_status", "Active");
            $this->db->where("dl.is_archived", 0);
            if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                $this->db->group_start();
                foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                $this->db->group_end();
            }
            $this->db->order_by("emp.lastname, emp.firstname", "ASC");
            $this->db->order_by("dl.expiration_date", "DESC");
            return $this->db->get();
        }

        function getLicense($id){
            $this->db->where('id', $id);
            $this->db->from('gcchris.tbl_license');
            $query = $this->db->get()->row();

            return $query;
        }

        function getLicenseInfo($form) {
            $id = $form['id'];
            $where = array("id" => $id);
            $this->db->select('CONCAT(license_id, "-", license_type) as id, license_type as text');
            $query = $this->db->get_where($this->employeeLicensureTable, $where)->row();
            return array("data" => $query);
        }

        public function mergeEmployeeLoan(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post["id"]) && $post["id"]){
                if(isset($post["merge_id"]) && count($post["merge_id"]) > 0){
                    $this->db->select("emp_id, loan_id, reference_id, reference, amount, deduction_type, fixed_deduction_amt, percentage");
                    $this->db->from("gcchris.loans");
                    $this->db->where("id", $post["id"]);
                    $qTemp = $this->db->get();
                    if($qTemp->num_rows() === 1){
                        $ctrUpdated = false;
                        $currentRow = $qTemp->row();
                        $currentAmount = $currentRow->amount;
                        $totalAmount = floatval($currentAmount);
                        foreach ($post["merge_id"] as $loanId) {
                            if(isset($post["balance_amt"][$loanId]) && $post["balance_amt"][$loanId]){
                                $tempBalance = floatval($post["balance_amt"][$loanId]);
                                $totalAmount += $tempBalance;
                            }
                        }

                        $currentRow->amount = $totalAmount;
                        $currentRow->remarks = "Merged Loan";
                        $currentRow->created_by = $this->core_layout->getCurrentEmployeeId();
                        $currentRow->created_at = date("Y-m-d H:i:s");
                        $added = $this->db->insert("gcchris.loans", $currentRow);
                        if($added){
                            $lastId = $this->db->insert_id();
                            $post["merge_id"][] = $post["id"];
                            $this->db->where_in("id", $post["merge_id"]);
                            $updated = $this->db->update("gcchris.loans", array("merged_id"=>$lastId, "active"=>3));
                            if($updated && $this->db->affected_rows() > 0){ $ctrUpdated = true; }
                        }
                        if($ctrUpdated){
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "Loan merge data successful.";
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Failed to merge Loan data!";
                        }
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Loan data not found!";
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "No merged data found!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }
            $resultset["post"] = $post;
            return $resultset;
        }

        public function getActiveCertificateFilter($dateStart=null, $dateEnd=null, $additionalFilters=array()){
            if($dateStart && $dateEnd){
                $sqlSelect = "UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name, TRIM(UPPER(cert.license_type)) as license, TRIM(UPPER(cert.exam_place)) as exam_place, 
                IF(cert.rating IS NULL OR cert.rating = '', 'N/A', cert.rating) as rating, cert.release_date, IF(cert.exam_date IS NULL OR cert.exam_date = 0000-00-00, 'N/A', cert.exam_date) as exam_date, IF(cert.license_no IS NULL OR cert.license_no = '', 'N/A', cert.license_no) as license_no, TRIM(IF(cert.expiration_date IS NULL OR cert.expiration_date = 0000-00-00, 'No Expiry', cert.expiration_date)) as expiration_date,  
                IF(cert.license_id = 0, ' --- ', licenses.type) as types,
                TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
                TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, 
                TRIM(UPPER(IF(pos.id IS NULL, emp.position, pos.name))) as position, 
                UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END, ' | ', 
                TRIM(IF(comp.id IS NULL, emp.company_id, comp.code)), ' | ', 
                TRIM(IF(dept.id IS NULL, emp.department_id, dept.description)), ' | ',
                TRIM(IF(pos.id IS NULL, emp.position, pos.name)))) as employee_header";
                $this->db->select($sqlSelect);
                $this->db->from($this->employeeLicensureTable." cert");
                $this->db->join($this->employeeTable." emp", "emp.id = cert.emp_id", "inner");
                $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
                $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id", "LEFT");
                $this->db->join($this->positionTable." pos", "pos.id = emp.position", "LEFT");
                $this->db->join($this->licenseTable." licenses", "licenses.id = cert.license_id", "LEFT");
                $this->db->where("emp.employee_status", "Active");
                $this->db->where("cert.release_date >=", $dateStart);
                $this->db->where("cert.release_date <=", $dateEnd);
                $this->db->where('cert.is_archived', 0);

                if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                    if(array_key_exists("cert.license_type", $additionalFilters)){
                        $tempTraining = $additionalFilters["cert.license_type"];
                        unset($additionalFilters["cert.license_type"]);
                        $this->db->like("cert.license_type", $tempTraining, "both");
                    }

                    if(isset($additionalFilters) && count($additionalFilters) > 0){
                        $this->db->group_start();
                        foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                        $this->db->group_end();
                    }
                    /** original source code for filtering by date range */
                    // if(in_array("trn.training", $additionalFilters)){
                    //     $tempTraining = $additionalFilters["trn.training"];
                    //     unset($additionalFilters["trn.training"]);
                    //     $this->db->like("trn.training", $tempTraining, "both");
                    // }
                    // $this->db->group_start();
                    // foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                    // $this->db->group_end();
                    /** original source code for filtering by date range */
                }
                $this->db->order_by("emp.lastname, emp.firstname", "ASC");
                // $this->db->order_by("trn.train_from", "DESC");
                return $this->db->get();
            }else{
                return false;
            }
        }

        public function getAllActiveCertificateFilter($additionalFilters=array()){
            $sqlSelect = "UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name, TRIM(UPPER(cert.license_type)) as license, TRIM(UPPER(cert.exam_place)) as exam_place, 
                IF(cert.rating IS NULL OR cert.rating = '', 'N/A', cert.rating) as rating, cert.release_date, IF(cert.exam_date IS NULL OR cert.exam_date = 0000-00-00, 'N/A', cert.exam_date) as exam_date, IF(cert.license_no IS NULL OR cert.license_no = '', 'N/A', cert.license_no) as license_no, TRIM(IF(cert.expiration_date IS NULL OR cert.expiration_date = 0000-00-00, 'No Expiry', cert.expiration_date)) as expiration_date,  
                IF(cert.license_id = 0, ' --- ', licenses.type) as types,
                TRIM(UPPER(IF(comp.id IS NULL, emp.company_id, comp.code))) as company,
                TRIM(UPPER(IF(dept.id IS NULL, emp.department_id, dept.description))) as department, 
                TRIM(UPPER(IF(pos.id IS NULL, emp.position, pos.name))) as position, 
                UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END, ' | ', 
                TRIM(IF(comp.id IS NULL, emp.company_id, comp.code)), ' | ', 
                TRIM(IF(dept.id IS NULL, emp.department_id, dept.description)), ' | ',
                TRIM(IF(pos.id IS NULL, emp.position, pos.name)))) as employee_header";
            $this->db->select($sqlSelect);
            $this->db->from($this->employeeLicensureTable." cert");
            $this->db->join($this->employeeTable." emp", "emp.id = cert.emp_id", "inner");
            $this->db->join($this->companyTable." comp", "comp.id = emp.company_id", "LEFT");
            $this->db->join($this->departmentTable." dept", "dept.id = emp.department_id", "LEFT");
            $this->db->join($this->positionTable." pos", "pos.id = emp.position", "LEFT");
            $this->db->join($this->licenseTable." licenses", "licenses.id = cert.license_id", "LEFT");
            $this->db->where("emp.employee_status", "Active");
            $this->db->where('cert.is_archived', 0);
            if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                if(isset($additionalFilters["cert.license_type"]) && $additionalFilters["cert.license_type"]){
                    $tempCert = $additionalFilters["cert.license_type"];
                    unset($additionalFilters["cert.license_type"]);
                    $this->db->like("cert.license_type", $tempCert, "both");
                }
            }
            if(isset($additionalFilters) && is_array($additionalFilters) && count($additionalFilters) > 0){
                $this->db->group_start();
                foreach ($additionalFilters as $field => $value) { $this->db->where("{$field}", $value); }
                $this->db->group_end();
            }
            $this->db->order_by("emp.lastname, emp.firstname", "ASC");
            $this->db->order_by("cert.release_date", "DESC");
            return $this->db->get();
        }

        function getHistoryPayrollInformation(){
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $post = $this->input->post();

            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
            $employeeId = (isset($post["emp_id"]) && $post["emp_id"]) ? $post["emp_id"] : 0;
            
            $rowData = $this->getHistoryPayrollRequest($search, $limit, $offset, $sortBy, $sortOrder, $employeeId);
            $rowCount = $this->getHistoryPayrollRequestCount($search, $employeeId);

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = isset($rowData["data"]) && $rowData["data"]? $rowData["data"]: array();
            
            return $resultset;
        }

        function getHistoryPayrollRequest($search=null, $limit, $offset, $sortBy, $sortOrder, $employeeId=0){
            $data = array();
            if($employeeId){
                $filterFields = array("b.log_message", "user.lastname", "user.firstname", "CONCAT(user.firstname, ' ', user.lastname)", "DATE_FORMAT(b.created_at, '%M %d, %Y')", "DATE_FORMAT(b.created_at, '%H:%i:%s')");
                $this->db->select("b.*, DATE_FORMAT(b.created_at, '%M %d, %Y %H:%i:%s') as created_at_formatted, CONCAT(UPPER(TRIM(user.firstname)), ' ',
                CASE WHEN UPPER(TRIM(user.middlename)) != 'N/A' AND UPPER(TRIM(user.middlename)) != 'NONE' AND
                        TRIM(user.middlename) !='' AND user.middlename IS NOT NULL
                    THEN CONCAT(UPPER(TRIM(SUBSTR(user.middlename, 1, 1))), '.') ELSE ''
                END,' ', UPPER(TRIM(user.lastname)),
                CASE WHEN UPPER(TRIM(user.suffix)) != 'N/A' AND
                    UPPER(TRIM(user.suffix !='NONE')) AND user.suffix !='' AND
                    user.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(user.suffix))) ELSE ''
                END) as employee_name");
                $this->db->from('gccmaster.logged_event_history a');
                $this->db->join('gcchris.user_logs_event b', 'b.id = a.event_id', "INNER");
                $this->db->join('gccmaster.tblemployees user', 'user.id = b.user_id', "INNER");
                
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
                $this->db->where("a.employee_id", $employeeId);
                if ($limit != -1) {
                    $this->db->limit($limit, $offset);
                }
                $this->db->order_by("b.created_at", "DESC");
                $query = $this->db->get();
                if ($query->num_rows() > 0) { $data = $query->result_array(); }
            }

            $resultset = array();
            $resultset["data"] = $data;
            return $resultset;
        }

        function getHistoryPayrollRequestCount($search=null, $employeeId=0){
            $rowCount = 0;
            if($employeeId){
                $filterFields = array("b.log_message", "user.lastname", "user.firstname", "CONCAT(user.firstname, ' ', user.lastname)", "DATE_FORMAT(b.created_at, '%M %d, %Y')");
                $this->db->select("b.*, DATE_FORMAT(b.created_at, '%M %d, %Y %H:%i:%s') as created_at_formatted, CONCAT(UPPER(TRIM(user.firstname)), ' ',
                CASE WHEN UPPER(TRIM(user.middlename)) != 'N/A' AND UPPER(TRIM(user.middlename)) != 'NONE' AND
                        TRIM(user.middlename) !='' AND user.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(user.middlename, 1, 1), '.') ELSE ''
                END,' ', UPPER(TRIM(user.lastname)),
                CASE WHEN UPPER(TRIM(user.suffix)) != 'N/A' AND
                    UPPER(TRIM(user.suffix !='NONE')) AND user.suffix !='' AND
                    user.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(user.suffix))) ELSE ''
                END) as employee_name");
                $this->db->from('gccmaster.logged_event_history a');
                $this->db->join('gcchris.user_logs_event b', 'b.id = a.event_id', "INNER");
                $this->db->join('gccmaster.tblemployees user', 'user.id = b.user_id', "INNER");
                
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
                $this->db->where("a.employee_id", $employeeId);
                $this->db->order_by("b.created_at", "DESC");
                $query = $this->db->get();
                $rowCount = $query->num_rows();
            }
            return $rowCount;
        }

        function approvalUpdatedPayrollData($type=0){
            $resultset = array();
            $this->load->model("payroll/employee_m");
            
            $post = $this->input->post();
            $responseData = array();
            if($type){
                $approval = "cancelled";
                switch ($type) {
                    case '1': $approval = "approved"; break;
                    case '2': $approval = "disapproved"; break;
                    case '3': $approval = "cancelled"; break;
                    default: $approval = "cancelled"; break;
                }

                if(isset($post["id"]) && $post["id"]){
                    if(is_array($post['id'])){
                        $is_update = true;
                        foreach($post['id'] as $ids){
                            $data = array("is_approved"=>$type, "approval_by"=>$this->core_layout->getCurrentEmployeeId(), "approval_at"=>date("Y-m-d H:i:s"));
                            $is_update = $this->db->update("gccmaster.field_value_approval", $data, array("id"=>$ids));

                            if($is_update && $this->db->affected_rows() === 1){
                                $this->db->select("a.module, a.unique_id, a.field_description, a.database_table, a.table_id, a.table_field, a.table_value, a.original_value, a.is_approved, 
                                    CONCAT(UPPER(TRIM(user.firstname)), ' ',
                                    CASE WHEN UPPER(TRIM(user.middlename)) != 'N/A' AND UPPER(TRIM(user.middlename)) != 'NONE' AND
                                            TRIM(user.middlename) !='' AND user.middlename IS NOT NULL
                                        THEN CONCAT(SUBSTR(user.middlename, 1, 1), '.') ELSE ''
                                    END,' ', UPPER(TRIM(user.lastname)),
                                    CASE WHEN UPPER(TRIM(user.suffix)) != 'N/A' AND
                                        UPPER(TRIM(user.suffix !='NONE')) AND user.suffix !='' AND
                                        user.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(user.suffix))) ELSE ''
                                    END) as employee_name");
                                    $this->db->join("gccmaster.tblemployees as user", "user.id = a.unique_id", "inner");
                                $qData = $this->db->get_where("gccmaster.field_value_approval as a", array("a.id"=>$ids));
                                if($qData->num_rows() === 1){
                                    $rowData = $qData->row();
        
                                    $payrollData = "employee";
                                    switch ($rowData->database_table) {
                                        case 'gccmaster.tblemployees': $payrollData = "employee"; break;
                                        case 'gcchris.allowances': $payrollData = "allowance"; break;
                                        default: $payrollData = "employee"; break;
                                    }
        
                                    $cancelApprovalWhere = array("unique_id"=>$rowData->unique_id, "table_id"=>$rowData->table_id, "table_field"=>$rowData->table_field, 
                                        "database_table"=>$rowData->database_table, "module"=>$rowData->module, "is_approved"=>0);
                                    $approvalDate = date("Y-m-d H:i:s");
                                    if(intval($rowData->is_approved) === 1){
                                        $tempData = array();
                                        $tempData[$rowData->table_field] = $rowData->original_value;
                                        $updatedEmployeeRecord = $this->db->update($rowData->database_table, $tempData, array("id"=>$rowData->table_id));
                                        if($updatedEmployeeRecord && $this->db->affected_rows() === 1){
                                            $this->db->update("gccmaster.field_value_approval", 
                                            array("is_approved"=>3, "approval_by"=>$this->core_layout->getCurrentEmployeeId(), "approval_at"=>$approvalDate),
                                            $cancelApprovalWhere);
                                            $this->db->reset_query();
                                        }
                                    }
                                    $coreHistoryLog = $this->core_layout->coreHistoryLogs();
                                    $coreHistoryLog->setHistoryLogModule($rowData->module);
                                    $coreHistoryLog->setHistoryLogTableName($rowData->database_table);
                                    $coreHistoryLog->setHistoryLogTableFieldId($rowData->table_id);
                                    $coreHistoryLog->setHistoryLogEmployeeId($rowData->unique_id);
        
                                    $logMessage = "Employee named `$rowData->employee_name` with payroll $payrollData data field `$rowData->field_description` has been `$approval` with a value of `$rowData->table_value`.";
                                    $coreHistoryLog->setEventLog($logMessage, $approval, "success", "gcchris", "user");
                                    $coreHistoryLog->saveLoggedEventHistory();
                                    
                                    $responseData = $this->employee_m->fieldValueApprovals("hris", $rowData->unique_id);
                                }
                                
                                $_payrollData = ucwords($payrollData);
                                $resultset["response"] = true;
                                $message = $approval == "approved" ? "Payroll $_payrollData Data has been `$approval` successfully.":"Payroll $_payrollData Data has been `$approval`.";
                                $resultset["toastr_msg"] = $message;
                                $resultset["data"] = $responseData;
                            }else{
                                $resultset["response"] = false;
                                $resultset["toastr_msg"] = "Failed to `$approval` payroll $payrollData data!";
                            }
                        }

                        if($is_update){
                            if($type == 1){
                                $history = $this->set_salary_history($post['id']);
                                if($history){
                                    $resultset['salary_history'] = 'Salary History Generated';
                                }else{
                                    $resultset['salary_history'] = 'No Salary History Generated.';
                                }
                            }
                        }
                    }else{
                        if($type == 1){
                            $history = $this->set_salary_history($post['id']);
                            if($history){
                                $resultset['salary_history'] = 'Salary History Generated';
                            }else{
                                $resultset['salary_history'] = 'No Salary History Generated.';
                            }
                        }
    
                        $data = array("is_approved"=>$type, "approval_by"=>$this->core_layout->getCurrentEmployeeId(), "approval_at"=>date("Y-m-d H:i:s"));
                        $updated = $this->db->update("gccmaster.field_value_approval", $data, array("id"=>$post["id"]));
                        
                        if($updated && $this->db->affected_rows() === 1){
                            $this->db->select("a.module, a.unique_id, a.field_description, a.database_table, a.table_id, a.table_field, a.table_value, a.original_value, a.is_approved, 
                                CONCAT(UPPER(TRIM(user.firstname)), ' ',
                                CASE WHEN UPPER(TRIM(user.middlename)) != 'N/A' AND UPPER(TRIM(user.middlename)) != 'NONE' AND
                                        TRIM(user.middlename) !='' AND user.middlename IS NOT NULL
                                    THEN CONCAT(SUBSTR(user.middlename, 1, 1), '.') ELSE ''
                                END,' ', UPPER(TRIM(user.lastname)),
                                CASE WHEN UPPER(TRIM(user.suffix)) != 'N/A' AND
                                    UPPER(TRIM(user.suffix !='NONE')) AND user.suffix !='' AND
                                    user.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(user.suffix))) ELSE ''
                                END) as employee_name");
                                $this->db->join("gccmaster.tblemployees as user", "user.id = a.unique_id", "inner");
                            $qData = $this->db->get_where("gccmaster.field_value_approval as a", array("a.id"=>$post["id"]));
                            if($qData->num_rows() === 1){
                                $rowData = $qData->row();
    
                                $payrollData = "employee";
                                switch ($rowData->database_table) {
                                    case 'gccmaster.tblemployees': $payrollData = "employee"; break;
                                    case 'gcchris.allowances': $payrollData = "allowance"; break;
                                    default: $payrollData = "employee"; break;
                                }
    
                                $cancelApprovalWhere = array("unique_id"=>$rowData->unique_id, "table_id"=>$rowData->table_id, "table_field"=>$rowData->table_field, 
                                    "database_table"=>$rowData->database_table, "module"=>$rowData->module, "is_approved"=>0);
                                $approvalDate = date("Y-m-d H:i:s");
                                if(intval($rowData->is_approved) === 1){
                                    $tempData = array();
                                    $tempData[$rowData->table_field] = $rowData->original_value;
                                    $updatedEmployeeRecord = $this->db->update($rowData->database_table, $tempData, array("id"=>$rowData->table_id));
                                    if($updatedEmployeeRecord && $this->db->affected_rows() === 1){
                                        $this->db->update("gccmaster.field_value_approval", 
                                        array("is_approved"=>3, "approval_by"=>$this->core_layout->getCurrentEmployeeId(), "approval_at"=>$approvalDate),
                                        $cancelApprovalWhere);
                                        $this->db->reset_query();
                                    }
                                }
                                $coreHistoryLog = $this->core_layout->coreHistoryLogs();
                                $coreHistoryLog->setHistoryLogModule($rowData->module);
                                $coreHistoryLog->setHistoryLogTableName($rowData->database_table);
                                $coreHistoryLog->setHistoryLogTableFieldId($rowData->table_id);
                                $coreHistoryLog->setHistoryLogEmployeeId($rowData->unique_id);
    
                                $logMessage = "Employee named `$rowData->employee_name` with payroll $payrollData data field `$rowData->field_description` has been `$approval` with a value of `$rowData->table_value`.";
                                $coreHistoryLog->setEventLog($logMessage, $approval, "success", "gcchris", "user");
                                $coreHistoryLog->saveLoggedEventHistory();
                                
                                $responseData = $this->employee_m->fieldValueApprovals("hris", $rowData->unique_id);
                            }
                            
                            $_payrollData = ucwords($payrollData);
                            $resultset["response"] = true;
                            $message = $approval == "approved" ? "Payroll $_payrollData Data has been `$approval` successfully.":"Payroll $_payrollData Data has been `$approval`.";
                            $resultset["toastr_msg"] = $message;
                            $resultset["data"] = $responseData;
                        }else{
                            $resultset["response"] = false;
                            $resultset["toastr_msg"] = "Failed to `$approval` payroll $payrollData data!";
                        }
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "No post data found!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Approval type not found!";
            }

            return $resultset;
        }

        function getSitePoints(){
            $this->db->select("site_name");
            $sites = $this->db->get("gcctimeutility.app_location_sites");
            $resultarray = array();

            if ($sites->num_rows() > 0) {
                foreach ($sites->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["site_name"];
                    $data["text"] = $_query["site_name"];
                    $resultarray[] = $data;
                }
            }
            return $resultarray;
        }

        function getSitePointStations(){
            $this->db->select("id, site_name as text");
            $sites = $this->db->get("gcctimeutility.app_location_sites");
            return $sites->result_array();
        }

        function getClassification($id){
            $this->db->select('employee_status');
            $this->db->where("id", $id);
            $query = $this->db->get($this->employeeTable);

            return $query->row()->employee_status;
        }

        function getHiredDate($id){
            $this->db->select('date_start');
            $this->db->where("id", $id);
            $query = $this->db->get($this->employeeTable);

            return $query->row()->date_start;
        }

        function setScheduledEmployeeInactive($date){
            $result = array();
            $now = $date ? date('Y-m-d', strtotime($date)) : date("Y-m-d");
            $temp = array();
            $ids = array();
            $sql = "CONCAT(UPPER(TRIM(firstname)), ' ',
            CASE WHEN UPPER(TRIM(middlename)) != 'N/A' AND UPPER(TRIM(middlename)) != 'NONE' AND
                    TRIM(middlename) !='' AND middlename IS NOT NULL
                THEN CONCAT(SUBSTR(middlename, 1, 1), '.') ELSE ''
            END,' ', UPPER(TRIM(lastname)),
            CASE WHEN UPPER(TRIM(suffix)) != 'N/A' AND
                UPPER(TRIM(suffix !='NONE')) AND suffix !='' AND
                suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(suffix))) ELSE ''
            END) as employee_name, id, biometricno, resignation_effective_date";
            $this->db->select($sql);
            $this->db->where('resignation_effective_date', $now);
            $this->db->where('employee_status', 'Active');
            $this->db->from($this->employeeTable);
            $query = $this->db->get();

            if($query->num_rows() > 0){
                foreach($query->result() as $row){

                    $data = array(
                        'work_status' => 'RESIGNED',
                        'employee_status' => 'Inactive',
                        'date_end' => $now
                    );

                    $this->db->where('id', $row->id);
                    $update = $this->db->update($this->employeeTable, $data);

                    if($update){
                        $temp[] = $row->employee_name. ' with employee ID #'.$row->id;
                        array_push($ids, $row->id);
                    }

                    // if($update){
                    //     $logMessage = "Employee name `$row->employee_name` with employee ID `$row->id` with Resignation effective date `$row->resignation_effective_date` is automatically set status to inactive and resigned.";
                    //     $this->core_layout->setEventLog($logMessage, 'Automated Resignation', "success", "gcchris", "user");
                    // }else{
                    //     $logMessage = "Employee name `$row->employee_name` with employee ID `$row->id` with Resignation effective date `$row->resignation_effective_date` has failed to automatically set status to inactive and resigned.";
                    //     $this->core_layout->setEventLog($logMessage, 'Automated Resignation', "error", "gcchris", "system");
                    // }
                }

                if(!empty($temp) && $temp){
                    $name = implode(', ', $temp);
                    $logMessage = "Employee(s) `$name` with Resignation effective date `$now` is automatically set status to inactive and resigned.";
                    $this->core_layout->setEventLog($logMessage, 'Automated Resignation', "success", "gcchris", "system");

                    if(!empty($ids) && $ids){
                        $masterData = array(
                            'role_id' => 0,
                            'is_suspended' => 1,
                            'suspended_by' => 0,
                            'suspended_dt' => date('Y-m-d H:i:s'),
                            'mobile_token' => NULL,
                            'telegram_chat_id' => NULL
                        );
    
                        $this->db->where_in('emp_id', $ids)->where('is_suspended', 0);
                        $updateUser = $this->db->update($this->tblUsers, $masterData);

                        if($updateUser){
                            $logMessage = "Employee(s) `$name` with user account(s) has been suspended automatically by the system.";
                            $this->core_layout->setEventLog($logMessage, 'Automated User Account Suspension', "success", "gccmaster", "system");
                        }
                    }
                }else{
                    $logMessage = 'Failed to automated set of status to inactive and resigned.';
                    $this->core_layout->setEventLog($logMessage, 'Automated Resignation', "error", "gcchris", "system");
                }

                
            }

            return $query->num_rows() > 0 ? true : false;
        }

        function set_salary_history($id){
            $user = $this->core_layout->getUserLoggedIn();
            $data = array();
            $user_emp_id = $user["employee_id"];
            $historyStatus = null;

            if(is_array($id)){
                $this->db->from('gccmaster.field_value_approval');
                $this->db->where_in('id', $id);
                $salary = $this->db->get();
                $emp_id = $salary->result()[0]->unique_id;

                $html = '';
                $payroll = '';
                $rate = '';
                $rate_fr = '';

                $this->db->select('b.name, a.basic_rate, a.payroll_type');
                $this->db->from($this->employeeTable.' as a');
                $this->db->join($this->positionTable.' as b', 'b.id = a.position OR b.name = a.position', 'LEFT');
                $this->db->where('a.id', $emp_id);
                $query = $this->db->get()->row();

                $basic = $query->basic_rate;

                $this->db->select('frequency, rate');
                $this->db->where('is_active', 1);
                $this->db->where('is_archived', 0);
                $this->db->where('emp_id', $emp_id);
                $this->db->limit(1);
                $this->db->order_by('id', 'DESC');
                $allowance = $this->db->from($this->tblAllowances)->get()->row();

                if($allowance == null){
                    $rate_fr = null;
                    $rate = 0;
                }else{
                    $rate_fr = $allowance->frequency == 'day' ? 'Daily Allowance' : 'Monthly Allowance';
                    $rate = $allowance->rate;
                }

                foreach($salary->result() as $row){
                    if($row->field_description == 'Payroll Type' && $row->table_value == 'monthly'){
                        $payroll = 'Monthly Rate';
                    }else if($row->field_description == 'Payroll Type' && $row->table_value == 'hourly'){
                        $payroll = 'Hourly Rate';
                    }else{
                        if($query->payroll_type == 'daily'){
                            $payroll = 'Basic Daily Rate';
                        }else if($query->payroll_type == 'monthly'){
                            $payroll = 'Monthly Rate';
                        }else{
                            $payroll = 'Hourly Rate';
                        }
                    }

                    /** -- commented as table_value has comma --
                     * if($salary->field_description == 'Basic Rate'){ $basic = $salary->table_value; }
                     * if($salary->field_description == 'Rate'){ $rate = $salary->table_value; }
                     */

                     if($salary->field_description == 'Basic Rate'){ $basic = $salary->original_value; }
                     if($salary->field_description == 'Rate'){ $rate = $salary->original_value; }
                }

                $rate_remark = $rate_fr ? ' + '.$rate.' '.$rate_fr : '';
                $remarks = $basic.' '.$payroll.''.$rate_remark;
                $basic_total = floatval($basic) + floatval($rate);

                $data = array(
                    'emp_id' => $emp_id,
                    'sal_date' => date('Y-m-d'),
                    'sal_rate' => number_format($basic_total, 2, '.', ''),
                    'sal_position' => $query->name,
                    'sal_remarks' => $remarks,
                    'add_date' => date('Y-m-d H:i:s'),
                    'add_by' => $user_emp_id
                );

                $historyStatus = $this->db->insert($this->employeeSalaryTable, $data);
            }else{
                $salary = $this->db->get_where('gccmaster.field_value_approval', array('id' => $id))->row();
                $this->db->reset_query();

                $payroll = '';
                $rate = '';
                $rate_fr = '';
                $basic = '';

                $this->db->select('b.name, a.basic_rate, a.payroll_type');
                $this->db->from($this->employeeTable.' as a');
                $this->db->join($this->positionTable.' as b', 'b.id = a.position OR b.name = a.position', 'LEFT');
                $this->db->where('a.id', $salary->unique_id);
                $query = $this->db->get()->row();
                $this->db->reset_query();
                $basic = $query->basic_rate;

                $this->db->select('frequency, rate');
                $this->db->where('is_active', 1);
                $this->db->where('is_archived', 0);
                $this->db->where('emp_id', $salary->unique_id);
                $this->db->limit(1);
                $this->db->order_by('id', 'DESC');
                $allowance = $this->db->from($this->tblAllowances)->get()->row();

                if($allowance == null){
                    $rate_fr = null;
                    $rate = 0;
                }else{
                    $rate_fr = $allowance->frequency == 'day' ? 'Daily Allowance' : 'Monthly Allowance';
                    $rate = $allowance->rate;
                }


                if($salary->field_description != 'Payout Schedule'){
                    if($salary->field_description == 'Payroll Type' && $salary->table_value == 'monthly'){
                        $payroll = 'Monthly Rate';
                    }else if($salary->field_description == 'Payroll Type' && $salary->table_value == 'hourly'){
                        $payroll = 'Hourly Rate';
                    }else{
                        if($query->payroll_type == 'daily'){
                            $payroll = 'Basic Daily Rate';
                        }else if($query->payroll_type == 'monthly'){
                            $payroll = 'Monthly Rate';
                        }else{
                            $payroll = 'Hourly Rate';
                        }
                    }

                    /** -- commented as table_value has comma --
                     * if($salary->field_description == 'Basic Rate'){ $basic = $salary->table_value; }
                     * if($salary->field_description == 'Rate'){ $rate = $salary->table_value; }
                     */

                    if($salary->field_description == 'Basic Rate'){ $basic = $salary->original_value; }
                    if($salary->field_description == 'Rate'){ $rate = $salary->original_value; }

                    $rate_remark = $rate_fr ? ' + '.$rate.' '.$rate_fr : '';
                    $remarks = $basic.' '.$payroll.' '.$rate_remark;
                    $basic_total = floatval($basic) + floatval($rate);

                    $data = array(
                        'emp_id' => $salary->unique_id,
                        'sal_date' => date('Y-m-d'),
                        'sal_rate' => number_format($basic_total, 2, '.', ''),
                        'sal_position' => $query->name,
                        'sal_remarks' => $remarks,
                        'add_date' => date("Y-m-d H:i:s"),
                        'add_by' => $user_emp_id
                    );

                    $historyStatus = $this->db->insert($this->employeeSalaryTable, $data);
                }else{
                    $historyStatus = false;
                }
            }

            return $historyStatus;
        }

        function set_approved_salary($arr){
            $user = $this->core_layout->getUserLoggedIn();
            $user_emp_id = $user["employee_id"];
            $data = array();
            $payroll = '';
            $rate = '';
            $rate_fr = '';
            $historyStatus = false;

            $this->db->select('b.name, a.basic_rate, a.payroll_type');
            $this->db->from($this->employeeTable.' as a');
            $this->db->join($this->positionTable.' as b', 'b.id = a.position OR b.name = a.position', 'LEFT');
            $this->db->where('a.id', $arr['id']);
            $query = $this->db->get()->row();
            $this->db->reset_query();
            $basic = $query->basic_rate;

            if($arr['payroll_type'] == 'hourly'){
                $payroll = 'Hourly Rate';
            }else if($arr['payroll_type'] == 'monthly'){
                $payroll = 'Monthly Rate';
            }else{
                if($query->payroll_type == 'daily'){
                    $payroll = 'Basic Daily Rate';
                }else if($query->payroll_type == 'monthly'){
                    $payroll = 'Monthly Rate';
                }else{
                    $payroll = 'Hourly Rate';
                }
            }

            $this->db->select('frequency, rate');
            $this->db->where('is_active', 1);
            $this->db->where('is_archived', 0);
            $this->db->where('emp_id', $arr['id']);
            $this->db->limit(1);
            $this->db->order_by('id', 'DESC');
            $allowance = $this->db->from($this->tblAllowances)->get()->row();
    
            if($allowance == null){
                $rate_fr = null;
                $rate = 0;
            }else{
                $rate_fr = $allowance->frequency == 'day' ? ' Daily Allowance' : ' Monthly Allowance';
                $rate = $allowance->rate;
            }

            $rate_remark = $rate_fr ? ' + '.$rate.''.$rate_fr : '';
            $remarks = $arr['basic_rate'].' '.$payroll.' '.$rate_remark;
            $basic = floatval($arr['basic_rate']) + floatval($rate);

            $data = array(
                'emp_id' => $arr['id'],
                'sal_date' => (isset($arr['date_hired']) && $arr['date_hired']) ? $arr['date_hired'] : date('Y-m-d'),
                'sal_rate' => number_format($basic, 2, '.', ''),
                'sal_position' => $query->name,
                'sal_remarks' => $remarks,
                'add_date' => date("Y-m-d H:i:s"),
                'add_by' => $user_emp_id
            );

            $historyStatus = $this->db->insert($this->employeeSalaryTable, $data);

            return $historyStatus;
        }

        function set_approved_allowance($arr){
            $user = $this->core_layout->getUserLoggedIn();
            $user_emp_id = $user["employee_id"];
            $data = array();
            $rate = '';
            $rate_fr = '';
            $historyStatus = false;
            $basic = 0;

            $isActiveState = intval($arr["is_active"]) == 1;

            $this->db->select('b.name, a.basic_rate, a.payroll_type');
            $this->db->from($this->employeeTable.' as a');
            $this->db->join($this->positionTable.' as b', 'b.id = a.position OR b.name = a.position', 'LEFT');
            $this->db->where('a.id', $arr['emp_id']);
            $query = $this->db->get()->row();
            $this->db->reset_query();
            $basic = $query->basic_rate;

            if($query->payroll_type == 'daily'){
                $payroll = 'Basic Daily Rate';
            }else if($query->payroll_type == 'monthly'){
                $payroll = 'Monthly Rate';
            }else{
                $payroll = 'Hourly Rate';
            }

            $rate_fr = $arr['frequency'] == 'day' ? 'Daily Allowance' : 'Monthly Allowance';
            $rate_remark = $isActiveState && $arr['rate'] && $rate_fr ? ' + '.$arr['rate'].' '.$rate_fr : '';
            $remarks = $basic.' '.$payroll.' '.$rate_remark;
            $basic_total = $isActiveState ? floatval($basic) + floatval($arr['rate']) : floatval($basic);

            $data = array(
                'emp_id' => $arr['emp_id'],
                'sal_date' => date('Y-m-d'),
                'sal_rate' => number_format($basic_total, 2, '.', ''),
                'sal_position' => $query->name,
                'sal_remarks' => $remarks,
                'add_date' => date("Y-m-d H:i:s"),
                'add_by' => $user_emp_id
            );

            $historyStatus = $this->db->insert($this->employeeSalaryTable, $data);

            return $historyStatus;
        }

        private function getEmployeeName($id) {
            $this->db->select("id, firstname, middlename, lastname, suffix");
            $this->db->from("gccmaster.tblemployees");
            $this->db->where("id", $id);
            $query = $this->db->get();
            $result = $query->row_array();

            $formattedName = strtoupper($result['firstname']) . ' ';
            if (!empty($result['middlename'])) {
                $initial = strtoupper(substr($result['middlename'], 0, 1)); // Get the first letter
                $formattedName .= $initial . '. '; // Append the initial
            }
            $formattedName .= strtoupper($result['lastname']);
            if (!empty($result['suffix'])) {
                $formattedName .= ', ' . strtoupper($result['suffix']);
            }
            $data['name'] = $formattedName;
            $data['id'] = $result['id'];
            return $data;
        }

        public function getOffensesCommendationTrail(){
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $post = $this->input->post();
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
            $employeeId = (isset($post["id"]) && $post["id"]) ? $post["id"] : 0;
            $rowData = $this->getOffensesCommendationTrailData($search, $limit, $offset, $sortBy, $sortOrder, $employeeId);
            $rowCount = $this->getOffensesCommendationTrailCount($search, $employeeId);
            $totalNotFiltered = $rowCount;
            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = isset($rowData) && $rowData? $rowData: array();
            return $resultset;
        }

        private function getOffensesCommendationTrailData($search, $limit, $offset, $sortBy, $sortOrder, $employeeId){
            $filterFields = array("id");
            $this->db->select("*");
            $this->db->from($this->offCom);
            $this->db->where("emp_id",$employeeId);
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
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $this->db->order_by("id", "DESC");
            $query = $this->db->get();
            return $query->result_array();
        }

        private function getOffensesCommendationTrailCount($search, $employeeId){
            $filterFields = array("id");
            $this->db->from($this->offCom);
            $this->db->where("emp_id",$employeeId);
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
            return $query->num_rows();
        }

        public function getOffCommById($id) {
            $query = $this->db->get_where($this->employeeOffensesTable, array('id' => $id));
            return $query->row();
        }

        function updateOffensesCommendationHistory($currentData,$newData,$insert = false){
            $firstname = $this->loggedinData['firstname'];
            $middlename = $this->loggedinData['middlename'];
            $lastname = $this->loggedinData['lastname'];
            $name = strtoupper($firstname) . ' ';
            if (!empty($middlename)) {
                $initial = strtoupper(substr($middlename, 0, 1));
                $name .= $initial . '. ';
            }
            $name .= strtoupper($lastname);
            if($insert){
                $emp_details = $this->getEmployeeName($newData['emp_id']);
                $emp_name = $emp_details['name'];
                $emp_id = $emp_details['id'];
                $post['logs'] = "Employee: ".$emp_name. ' Added new offense and commendation: ';
                $post['offcom_id'] = $insert;
                $post['user'] = $name;
                $post['emp_id'] = $emp_id;
                $post['action'] = 'insert';
                $this->db->insert($this->offCom, $post);
                return true;
            }
            else{
                $emp_details = $this->getEmployeeName($currentData->emp_id);
                $emp_name = $emp_details['name'];
                $emp_id = $emp_details['id'];
                $id = $newData['id'];
                unset($newData['id']);
                $changes = array();
                $changesString = '';
                foreach ($currentData as $field => $value) {
                    if (isset($newData[$field]) && $newData[$field]!== $value) {
                        $changes[$field] = array(
                            'old' => $value,
                            'new' => $newData[$field]
                        );
                    }
                }
                foreach ($changes as $field => $change) {
                    $changesString .= "Data Field: ";
                    switch(strtolower($field)) {
                        case 'offcom_type': $changesString .= 'Type'; break;
                        case 'offcom_date': $changesString .= 'Date'; break;
                        case 'offcom_nature': $changesString .= 'Nature'; break;
                        case 'offcom_action': $changesString .= 'Action Taken'; break;
                        case 'filename': $changesString .= 'Attachment'; break;
                        default: $changesString .= $field; break;
                    }
                    $changesString .= ", has been updated from: " . $change['old'] . ", to: " . $change['new'] . "\n";
                }
                $logs = "Employee: ".$emp_name. ' Updated offense and commendation: '. $changesString;
                $post['logs'] = $logs;
                $post['offcom_id'] = $id;
                $post['user'] = $name;
                $post['emp_id'] = $emp_id;
                $post['action'] = 'update';
                 if (!empty($changes)) {
                    $this->db->insert($this->offCom, $post);
                    return true;
                }
                else{
                    return false;
                }
            }
            
        }

        function check_has_no_salary($id){
            $this->db->from($this->employeeTable);

            $this->db->group_start();
            $this->db->where('basic_rate', 0);
            $this->db->or_where('basic_rate', NULL);
            $this->db->group_end();

            $this->db->where('id', $id);

            $query = $this->db->get();
            return ($query->num_rows() > 0) ? true : false;
        }

        function check_has_no_allowance($id){
            $this->db->from($this->tblAllowances);
            $this->db->where('is_active', 1);
            $this->db->where('is_archived', 0);
            $this->db->where('emp_id', $id);
            $query = $this->db->get();

            return ($query->num_rows() == 1) ? true : false;
        }

        function update_bank_info(){
            $post = $this->input->post();
            $result = array();

            if(isset($post) && $post){
                $id = $post['id'];
                $data = array(
                    'bank_name' => $post['bank_name'],
                    'atm_info' => $post['atm_info'],
                );

                $displayName = $this->core_layout->getEmployeeData($post['id'])["display_name_1"];
                $bank_name = isset($post['bank_name']) && $post['bank_name'] ? $post['bank_name'] : '';

                $this->db->where('bank_name IS NOT NULL', NULL, FALSE);
                $this->db->where('atm_info IS NOT NULL', NULL, FALSE);
                $bankInfo = $this->db->get_where($this->employeeTable, array('id' => $id))->num_rows();

                $this->db->reset_query();

                $this->db->where('id', $id);
                $update = $this->db->update($this->employeeTable, $data);

                if($update){
                    $result['state'] = true;

                    if($bankInfo > 0){
                        $result['msg'] = 'Succesfully updated Bank Information';
                        $this->core_layout->setEventLog("Payroll Information of `$displayName's` bank name `$bank_name` and account number has been updated.","update", "success", "gcchris", "user");
                    }else{
                        $result['msg'] = 'Succesfully Saved Bank Information';
                        $this->core_layout->setEventLog("Payroll Information of `$displayName's` bank name `$bank_name` and account number has been added.","insert", "success", "gcchris", "user");
                    }
                }else{
                    $result['state'] = false;

                    if($bankInfo > 0){
                        $result['msg'] = 'Failed to update Bank Information';
                        $this->core_layout->setEventLog("Failed to update Payroll Information of `$displayName` with bank name `$bank_name` and its account number.","update", "error", "gcchris", "system");
                    }else{
                        $result['msg'] = 'Failed to save Bank Information';
                        $this->core_layout->setEventLog("Failed to add Payroll Information of `$displayName` with bank name `$bank_name` and its account number.","insert", "error", "gcchris", "system");
                    }
                    
                }
            }else{
                $result['state'] = false;
                $result['msg'] = 'No Data Found.';
                $this->core_layout->setEventLog("No Data found when updating Bank Information.","update", "error", "gcchris", "system");
            }

            return $result;
        }

        private function logChanges($currentData, $newData) {
            // var_dump($currentData, $newData);
                if (is_object($currentData)) {
                    $currentData = get_object_vars($currentData);
                }
                if (is_object($newData)) {
                    $newData = get_object_vars($newData);
                }
                $changes = array();
                $changesString = '';
                foreach ($currentData as $field => $value) {
                    if (isset($newData[$field]) && $newData[$field]!= $value) {
                        $changes[$field] = array(
                            'old' => $value,
                            'new' => $newData[$field]
                        );
                    }
                }
                foreach ($changes as $field => $change) {
                    if (strtolower($field) == 'department_id'){
                        $changesString.= " Field: $field, from: ". $this->getDepartmentById($change['old'])->description. ", to: ". $this->getDepartmentById($change['new'])->description. "\n";
                        }
                    else if (strtolower($field) == 'position'){
                        $changesString.= " Field: $field, from: ". $this->getPositionById($change['old'])->name. ", to: ". $this->getPositionById($change['new'])->name. "\n";
                    }
                    else if ($field != 'work_station'){
                        $changesString.= " Field: $field, from: ". $change['old']. ", to: ". $change['new']. "\n";
                    }
                }
                if (isset($newData['work_station'])) {
                    sort($newData['work_station']);
                    sort($currentData['work_station']);
                    if (empty($newData['work_station'])) {
                        $diff = array_diff($currentData['work_station'], $newData['work_station']);
                    } else {
                        $diff = array_diff($newData['work_station'], $currentData['work_station']);
                    }
                    if (!empty($diff)) {
                        $changesString.= " Field: work_station, from: ' ". implode(',', $currentData['work_station']). " ', to: '". implode(',', $newData['work_station']). "'\n";
                    }
                } 
                return $changesString;
            }

            private function getDepartmentById($id){
                $this->db->select("description");
                $this->db->from($this->departmentTable);
                $this->db->where('id', $id);
                $query = $this->db->get(); 
                $result = $query->row();
                $this->db->reset_query();
                return $result;
            }
    
            private function getPositionById($id){
                $this->db->select("name");
                $this->db->from($this->positionTable);
                $this->db->where('id', $id);
                $query = $this->db->get(); 
                $result = $query->row();
                $this->db->reset_query();
                return $result;
            }

    }