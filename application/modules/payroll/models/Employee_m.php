<?php defined('BASEPATH') or exit('No direct script access allowed');

    class Employee_m extends CI_Model {
        protected $employeeTable = "gccmaster.tblemployees";
        protected $companyTable = "gcchris.tblcompanies";
        protected $departmentTable = "gcchris.tbldepartments";
        protected $positionTable = "gcchris.tblposition";
        protected $payrollTypeTable = "payroll.payroll_type";
        protected $personnelTable = "gcctimeutility.personnel";
        protected $payrollGroupTable = "payroll.payroll_group";
        protected $employeeSalaryTable = "gcchris.tblsalaries";

        protected $tbl_timesheet_monthly_employees = "gcctimeutility.timesheet_monthly_employees";
        protected $tbl_payroll_fixed_taxable = "payroll.fixed_taxable_deduction";

        private $db_debug;

        function __construct() {
            parent::__construct();
            $this->load->library('image_lib');
            $this->user_data = $this->session->userdata("logged_in");
            date_default_timezone_set("Asia/Manila");
            $this->load->model("gcctime/Timesheet_model", "ts_model");
            $this->db_debug = $this->db->db_debug;
        }

        function employeeMasterfile($employee_status) {
            $post = $this->input->post();
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

                if($searchValue){ $searchValue  = trim($searchValue); }
                
                $parameters = array();
                $parameters["is_archived"] = 0;

                if ($employee_status !== "All") {
                    $parameters["employee_status"] = $employee_status;
                }

                $dtTemp->setWhereParameters($parameters);

                // commented out as it returns all employee even user searched
                // $totalData = $dtTemp->dtAllPostsCount();
                // $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTemp->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    // $dtTemp->setLike("CONCAT(firstname, ' ', lastname)", $searchValue, "both");
                    $posts = $dtTemp->dtSearch($limit, $start, $searchValue, $order, $dir);
                    // $totalData = $dtTemp->dtPostSearchCount($searchValue, $employee_status);
                    // $totalFiltered = $totalData;
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

                        // $image = "http://bcd.gccph.com/hris/uploads/" . $imageFile;

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

                // get total count of employee based on search and status
                $totalData = $this->employeeCount($searchValue, $employee_status);

                $json_data = array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalData),
                    "data" => $data,
                    "a" => $posts
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

        function employeeCount($search = null, $status) {
            $this->db->select("id, lastname, is_incomplete, work_status, idno, firstname, middlename, suffix, company_id, department_id, position");
            $this->db->from($this->employeeTable);

            if ($status != 'All') {
                $this->db->where('employee_status', $status);
            }

            if ($search) {
                $this->db->like('CONCAT(firstname, " ", lastname)', $search, 'both');
            }

            $query = $this->db->get();
            return $query->num_rows();
        }

        function getEmployeeData($id = null) {
            $data = array();
            if ($id) {
                $this->db->select("a.*, concat(trim(a.latitude), ',', trim(a.longitude)) as map_coordinates");
                $this->db->from("{$this->employeeTable} as a");
                $this->db->where("id", $id);
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
                    $displayName = $this->core_layout->getUserData($data->id);
                    $data->display_name = isset($displayName["display_name_1"]) && $displayName["display_name_1"] ? $displayName["display_name_1"] : "";

                    $displayEmail = strtoupper(trim($data->email));
                    $data->display_email = ($displayEmail !== "NONE" && $displayEmail !== "N/A" && $displayEmail !== "") ? $displayEmail : "";

                    $company = $this->db->where("id", $data->company_id)->get("gcchris.tblcompanies")->row("code");
                    $data->company = empty($company) ? $data->company_id : $company;

                    $position = $this->db->where("id", $data->position)->get("gcchris.tblposition")->row("name");
                    $data->_position = empty($position) ? $data->position : $position;

                    $department = $this->db->where("id", $data->department_id)->get("gcchris.tbldepartments")->row("code");
                    $data->department = empty($department) ? $data->department_id : $department;

                    $data->_status = $data->work_status;

                    $data->current_company_id = $data->company_id;
                    $data->current_department_id = $data->department_id;
                    $data->current_position_id = $data->position;
                    // $data->basic_rate = $data->basic_rate;
                    $data->basic_rate = number_format($data->basic_rate, 2, ".", "");
                    $data->payout_sched = $data->payout_sched;
                    $data->payroll_type = $data->payroll_type;
                    // $data->current_supervisor = $data->supervisor;
                }
            }
            return $data;
        }

        function getDropdownSelectData() {
            $resultset = array();
            $this->db->select("id, description as text");
            $this->db->where("is_archived", 0);
            $this->db->where("exclude", 0); 
            $companies = $this->db->get($this->companyTable);

            $this->db->select("id, description as text");
            $departments = $this->db->order_by("TRIM(text)", "ASC")->get($this->departmentTable);

            $this->db->select("id, name as text");
            $positions = $this->db->order_by("TRIM(text)", "ASC")->get($this->positionTable);

            $this->db->select("code as id, name as text");
            $this->db->where("status", 1);
            $payroll_type = $this->db->get($this->payrollTypeTable);

            $resultset["dropdown_company"] = ($companies->num_rows() > 0) ? $companies->result() : array();
            $resultset["dropdown_department"] = ($departments->num_rows() > 0) ? $departments->result() : array();
            $resultset["dropdown_position"] = ($positions->num_rows() > 0) ? $positions->result() : array();
            $resultset["dropdown_payroll_type"] = ($payroll_type->num_rows() > 0) ? $payroll_type->result() : array();

            return $resultset;
        }

        function saveEmployee() {
            $this->db->db_debug = false;
            $post = $this->convertToStdObject($this->input->post());
            $long_lat_coordinates = isset($post->long_lat_coordinates) ? $post->long_lat_coordinates : null;
            $biometricNoExist = isset($post->biometricNoExist) ? $post->biometricNoExist : "false";
            $existInPersonnelList = isset($post->existInPersonnelList) ? $post->existInPersonnelList : "false";
            $post->employee_status = 'Active';
            $isLaborer = isset($post->isLaborer) ? 1 : 0;
            $post->hris_hidden = intval($isLaborer) === 1;
            $history_id = isset($post->history_id) ? $post->history_id : null;
            $shift_id = isset($post->shift_id) ? $post->shift_id : 0;
            $is_flexi = isset($post->is_flexi) ? (!empty($post->is_flexi) ? 1 : 0) : 0;
            $origin = isset($post->origin) ? $post->origin : NULL;

            $errors = array();
            $created_logs = array();
            $no_shifts = array();
            $biometric_no_array = array();

            unset($post->long_lat_coordinates, $post->biometricNoExist,
                $post->existInPersonnelList, $post->isLaborer, $post->history_id, $post->shift_id, $post->origin,
                $post->is_flexi);
            $resultSet = array();
            $name = $post->firstname . " " . $post->lastname;

            $this->db->trans_begin();

            if (!empty($long_lat_coordinates)) {
                $map_coordinate = explode(",", $long_lat_coordinates);
                if (sizeof($map_coordinate) === 2) {
                    $post->latitude = $map_coordinate[0];
                    $post->longitude = $map_coordinate[1];
                }
            }

            $post->add_date = date("Y-m-d H:i:s");
            $post->pic_filename = !empty($_FILES['image']['name']) ? $_FILES['image']['name'] : 'no_image.jpg';
            $this->db->insert($this->employeeTable, $post);
            $employee_insert_id = array("id" => $this->db->insert_id());

            // START SAVE EMPLOYEE IMAGE
            $defaultImagePath = "./assets/images/profile/no_image.jpg";
            $config['upload_path'] = "./uploads/files/images/employee_files/empcode_" . $employee_insert_id["id"];
            $config['allowed_types'] = 'gif|jpg|png|jpeg';

            $thumb_path = $config['upload_path'] . "/thumbnails";

            if (!file_exists($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }

            if (!file_exists($thumb_path)) {
                mkdir($thumb_path, 0777, TRUE);
            }

            if (!empty($_FILES['image']['name'])) {
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('image')) {
                    $resultSet["image_err"] = $this->upload->display_errors();
                }
            } else {
                copy($defaultImagePath, $config['upload_path'] . "/no_image.jpg");
            }

            $source = "uploads/files/images/employee_files/empcode_" . $employee_insert_id["id"];
            // END SAVE EMPLOYEE IMAGE

            // START SAVE TO PERSONNEL FUNCTION
            // insert to personnel if does not exist
            if ($existInPersonnelList === "false") {
                $personnelData = array(
                    "biometric_id" => $post->biometricno,
                    "biometricno" => $post->biometricno,
                    "name" => strtoupper($name),
                    "department_id" => 0,// $post->department_id,
                    "shift_id" => $shift_id,
                    "location_id" => 0,
                    "role" => 0,
                    "is_flexi" => $is_flexi,
                    "is_active" => 1
                );
                $this->db->insert($this->personnelTable, $personnelData);
            }
            // END SAVE TO PERSONNEL FUNCTION

            if (!empty($history_id) && $history_id !== "null") {
                // remove biometric no from array list
                $this->db->select("imports.*, CONCAT(emp.lastname, ' ', emp.firstname) creator");
                $this->db->join("tblemployees emp", "emp.id = imports.created_by", "LEFT");
                $this->db->where("imports.id", $history_id);

                $query = $this->db->get("gcctimeutility.timesheet_imports imports");
                $row = $query->row();

                if ($origin === "history") {
                    if (!empty($row->no_shifts)) {
                        $no_shifts = unserialize($row->no_shifts);
                        $no_shifts_index = array_search($post->biometricno, $no_shifts);
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
                        $biometric_no_array_index = array_search($post->biometricno, array_column($biometric_no_array, 'biometric'));
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
                        $this->db->set("resolved", 1);
                    }
                    $this->db->update("gcctimeutility.timesheet_imports");
                }

                if (isset($shift_id)) {
                    $interval = DateInterval::createFromDateString('1 day');

                    $dateStart = new DateTime($row->start_date);
                    $dateEnd = new DateTime($row->end_date);
                    $dateEnd->modify("+1 day");

                    $period = new DatePeriod($dateStart, $interval, $dateEnd);
                    foreach ($period as $dt) {
                        $date = $dt->format("Y-m-d");
                        $created = $this->ts_model->create($date, 1, array($employee_insert_id["id"]), $history_id, 1);
                        if (intval($created["k"]) === 2) {
                            array_push($errors, $created);
                        }

                        array_push($created_logs, $created);
                    }
                }
            }

            $resultSet["data"] = $employee_insert_id;
            $resultSet["errors"] = $errors;
            $resultSet["created_logs"] = $created_logs;
            $resultSet["biometricno"] = $post->biometricno;

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $resultSet["success"] = true;
                $resultSet["message"] = "Information's of <strong>" . strtoupper($name) . "</strong> was successfully saved.";
                $resultSet["title"] = "New Employee was saved.";
            } else {
                $this->db->trans_rollback();
                $resultSet["success"] = false;
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "An Error Occurred.";
            }

            $this->db->db_debug = $this->db_debug;
            return $resultSet;
        }

        function convertToStdObject($array) {
            return json_decode(json_encode($array));
        }

        function checkBiometricNo($biometricno) {
            $employee_list = $this->db
                ->where("biometricno", $biometricno)
                ->where("biometricno IS NOT NULL", NULL, FALSE)
                ->get($this->employeeTable)
                ->result();
            $personnel_list = $this->db->where("biometricno", $biometricno)->get("gcctimeutility.personnel")->result();
            return array("employee_list" => $employee_list, "personnel_list" => $personnel_list);
        }

        private function resizeImage2($filename, $source_path, $target_path, $width, $height = 0) {
            $_source_path = rtrim($source_path, '/') . "/" . $filename;
            $_target_path = rtrim($source_path, '/') . $target_path;

            $config = array(
                'image_library' => 'gd2',
                'source_image' => $_source_path,
                'new_image' => $_target_path,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '',
                'width' => $width
            );

            if ($height) {
                $config["height"] = $height;
            }

            $this->image_lib->initialize($config);
            return $this->image_lib->resize();
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
            $data["allowance_id"] = $post["allowance_id"];
            $data["rate"] = $post["rate"];
            $data["frequency"] = $post["frequency"];

            $query = $this->db->insert("gcchris.allowances", $data);
            $lastInsertedId = $this->db->insert_id();

            /*** edited contents logging ***/
            $this->db->select("allw.rate, allw.frequency, allw.is_active, allw.emp_id, pallw.allowance_name, CONCAT(UPPER(TRIM(emp.firstname)), ' ',
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
            $employeeId = 0;

            if($allw->num_rows() === 1){
                $row = $allw->row();
                $tempEmployeeName = $row->employee_name;
                $employeeId = $row->emp_id;
                $rate = $row->rate;
                $frequency = $row->frequency;
                $allowanceName = $row->allowance_name;
            }
            $this->db->reset_query();
            /*** edited contents logging ***/

            $coreHistoryLog = $this->core_layout->coreHistoryLogs();
            $coreHistoryLog->setHistoryLogModule("hris");
            $coreHistoryLog->setHistoryLogTableName("gcchris.allowances");
            $coreHistoryLog->setHistoryLogTableFieldId($lastInsertedId);
            $coreHistoryLog->setHistoryLogEmployeeId($employeeId);
            
            if ($query) {
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

        public function updateEmployeeAllowance() {
            $post = $this->input->post();
            $postStdClass = json_decode(json_encode($post), false);

            $id = $post["id"];
            $hasApprovingAuthority = isset($post["approving_authority"]) ? json_decode($post["approving_authority"]): false;
            unset($post["id"], $post["approving_authority"]);
            $postRate = isset($post['rate']) && $post['rate'] ? floatval($post['rate']): 0;
            $postIsActive = isset($post["is_active"]) && $post['is_active'] ? 1 : 0;
            $resultSet = array();

            $this->db->select("id, is_active");
            $this->db->where("emp_id", $post["emp_id"]);
            $qAllw = $this->db->get("gcchris.allowances");
            if($qAllw->num_rows() > 0){
                $multipleAllowances = false;
                $recordCount = $qAllw->num_rows();
                $checkColumn = array_column($qAllw->result_array(), "is_active");
                $isActiveColumn = array_count_values($checkColumn);
                if(($recordCount > 1 && isset($isActiveColumn[1]) && $isActiveColumn[1] == $recordCount) ||
                 ($recordCount > 1 && $postStdClass->is_active && (isset($isActiveColumn[0]) && $isActiveColumn[0] > 0) && (isset($isActiveColumn[1]) && $isActiveColumn[1] > 0))){
                    $multipleAllowances = true;
                }

                if($multipleAllowances){
                    $resultSet["success"] = false;
                    $resultSet["message"] = "Multiple active allowances is not allowed!";
                    $resultSet["title"] = "Update Allowance Data";
                    $resultSet["toast"] = "error";
                    return $resultSet;
                }
            }

            $this->db->reset_query();
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
                $tempUpdate = $this->db->update("gcchris.allowances");
                $updated = $tempUpdate && $this->db->affected_rows() > 0;
            }

            $coreHistoryLog = $this->core_layout->coreHistoryLogs();
            $coreHistoryLog->setHistoryLogModule("hris");
            $coreHistoryLog->setHistoryLogTableName("gcchris.allowances");
            $coreHistoryLog->setHistoryLogTableFieldId($id);
            $coreHistoryLog->setHistoryLogEmployeeId($employeeId);

            if($updated && $this->db->trans_status() === true){
                $resultSet["success"] = true;
                $resultSet["message"] = "Allowance data has been updated successfully.";
                $resultSet["title"] = "Update Allowance Data";
                $resultSet["toast"] = "success";

                if($hasApprovingAuthority){
                    $historyStatus = $this->set_approved_allowance($post);
                    if($historyStatus){
                        $resultSet['salary_history'] = 'Salary History Generated.';
                    }else{
                        $resultSet['salary_history'] = 'Failed to generate Salary History.';
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

            /*** 
            if ($this->db->trans_status() === FALSE) {
                $coreHistoryLog->setEventLog("Failed to update payroll allowance data of employee named `$tempEmployeeName` with DB id no. `$id`","update", "error", "gcchris", "user");
                $coreHistoryLog->saveLoggedEventHistory();
            }else{
                if(is_array($editedContent) && count($editedContent) > 0){
                    foreach ($editedContent as $key => $value) {
                        if(isset($tempKeys[$key]) && $tempKeys[$key]){
                            $nKey = $tempKeys[$key];
                            if(isset($editedContent[$key]) && $editedContent[$key]){
                                $fromValue = $fromContent[$key];
                                $toValue = $editedContent[$key];
                                
                                if($fromValue !== $toValue){
                                    $logMessage = $fromValue ? 
                                        "Employee named `$tempEmployeeName` with payroll allowance data field `$nKey` has been updated from `$fromValue` to `$toValue`, DB id no. `$id`.": 
                                        "Employee named `$tempEmployeeName` with payroll allowance data field `$nKey` has been updated into `$toValue`, DB id no. `$id`.";
                                    $coreHistoryLog->setEventLog($logMessage, $fromValue ? "update": "insert", "success", "gcchris", "user");
                                    $coreHistoryLog->saveLoggedEventHistory();
                                }
                            }
                        }
                    }
                }
            }  ***/ 
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

            return $resultSet;
        }

        function updateEmployeeLoan($id) {
            $currentCompanyData = $this->getEmployeeLoanById($id);

            $post2 = $this->input->post();
            $fullname =  $this->getEmployeeName($post2['emp_id']);
            $raw_amount = $post2['amount'];
            $formatted_amount = number_format(floatval(str_replace(',', '', $raw_amount)), 2, '.', '');

            $amount = $post2['amount'];
            $cleanAmount = str_replace(',', '', $amount);
            if (!is_numeric($cleanAmount)) {
                $resultSet["status"] = FALSE;
                $resultSet["response"] = "Invalid amount format. Please enter a valid number.";
                $resultSet["message"] = "Invalid amount format. Please enter a valid number.";
                $resultSet["title"] = "Error Occurred.";
                $resultSet["toast"] = "error";
                return $resultSet;
            }

            $post2['amount'] = $formatted_amount;
            $post = $this->arrayToStdClass($this->input->post());
            $post->amount = str_replace(",", "", $post->amount);

            if (intval($post->deduction_type) === 1) {
                $post->fixed_deduction_amt = str_replace(",", "", $post->deduct_type_value);
                $post->percentage = 0;
                $post2['fixed_deduction_amt'] = $post->fixed_deduction_amt;
                $post2['percentage'] = 0;
            } else {
                $post->fixed_deduction_amt = 0;
                $post->percentage = str_replace(",", "", $post->deduct_type_value);
                $post2['fixed_deduction_amt'] = 0;
                $post2['percentage'] = $post->percentage;
            }

            $lastInterestChargeLog = null;

            if(isset($post->last_interest_charge)){
                $this->db->select("loans.*, UPPER(CONCAT(emp.lastname,
                CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                    UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                    emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
                END, ', ', emp.firstname, ' ',
                CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                        TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
                END)) as employee_name");
                $this->db->join("gccmaster.tblemployees as emp", "emp.id = loans.emp_id", "INNER");
                $tempCharge = $this->db->get_where("gcchris.loans as loans", 
                    array("loans.id"=>$id, "loans.interest_percentage >"=> 0));
                if($tempCharge->num_rows() === 1){
                    $tempChargeRow = $tempCharge->row();
                    if(intval($tempChargeRow->last_interest_charge) !== intval($post->last_interest_charge)){
                        $tempOption = intval($post->last_interest_charge) > 0 ? "YES": "NO";
                        $lastInterestChargeLog = "User has updated the loan payroll last interest charge option of employee named `{$tempChargeRow->employee_name}` into `{$tempOption}` with Reference # `{$tempChargeRow->reference}`.";
                    }
                }
                $this->db->reset_query();
            }

            $uploadResult = array();

            if(isset($_FILES['files'])){
                $uploadResult = $this->uploadFiles($id);
            }

            unset($post->deduct_type_value);
            $post->debit_note = isset($post->debit_note) && $post->debit_note ? trim(strtoupper($post->debit_note)): null;
            
            $this->db->where("id", $id);
            $this->db->set($post);
            $update = $this->db->update("gcchris.loans");
            $this->db->reset_query();

            $resultSet = array();

            $resultSet["success"] = $update;
            $changes = $this->logChanges($currentCompanyData ,$post2);
            if ($update) {
                $resultSet["message"] = "Employee Loan was updated successfully.";
                $resultSet["title"] = "Loan Updated";
                $resultSet["toast"] = "success";
                $resultSet["fail_uploads"] = !empty($uploadResult) ? $uploadResult["upload_errors"] : array();
                $resultSet["primary_pic"] = !empty($uploadResult) ? $uploadResult["success_primary_pic"] : null;
                $resultSet["uploading_primary_pic_failed"] = !empty($uploadResult) ? $uploadResult["uploading_primary_pic_failed"] : null;
                $use = "user";
                $q = $this->getLoanRemark($id);

                // $msg = "User updated the loan with the remarks of ".$q->remarks." to ".$post->remarks;
                // $this->core_layout->setEventLog($msg, "update", "success", "payroll");

                if($lastInterestChargeLog){ $this->core_layout->setEventLog($lastInterestChargeLog, "update", "success", "payroll"); }
            } else {
                $resultSet["message"] = $this->db->error()["message"];
                $resultSet["title"] = "Error Occurred.";
                $resultSet["toast"] = "error";
                $use = "system";
            }
            $resultSet["changes"] = $changes;
            $this->core_layout->setEventLog("User updated loans for:  <strong>".$fullname."</strong> ".$changes, "update", $resultSet["toast"], "gcchris",$use);
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
            $this->db->where("ps.posted", 1);
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

        function getLoanRemark($id){
            $this->db->select('remarks');
            $this->db->where('id', $id);
            $this->db->from('gcchris.loans');

            return $this->db->get()->row();
        }

        function getEmployeeGroup(){
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $order_val = array(array("column" => "0", "dir" => "desc"));
            $post = $this->input->post();

            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

            $rowData = $this->get_employee_group_list($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_employee_group_count($search);

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_employee_group_list($search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $arrData = array();
            $filterFields = array("b.code", "b.description", "a.description");
            $this->db->select("a.*, IFNULL(b.description, 'ALL') as company");
            $this->db->from($this->payrollGroupTable." a");
            $this->db->join($this->companyTable." b", "b.id = a.company_id", "LEFT");
            $this->db->where("a.is_archived", 0);
            if(isset($search)){
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) { $this->db->like($field, $search, "both"); }
                    else { $this->db->or_like($field, $search, "both"); }
                }
                $this->db->group_end();
            }

            if($limit != -1){ $this->db->limit($limit, $offset); }
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $query = $this->db->get();
            if($query->num_rows() > 0){
                foreach($query->result() as $kk => $vv){
                    $employees = array();
                    $tempIds = @unserialize($vv->employee_id);
                    $this->db->from($this->employeeTable);
                    $this->db->where_in("id", $tempIds);
                    $qTempEmp = $this->db->get();
                    if($qTempEmp->num_rows() > 0){
                        foreach($qTempEmp->result() as $rs){
                            $tempRs = (array) $rs;
                            $tempName = $this->core_layout->getDisplayName($tempRs);
                            $tempName = isset($tempName["display_name_1"]) && $tempName["display_name_1"] ? $tempName["display_name_1"]: "No assigned name";
                            $employees[] = $tempName;
                        }
                    }
                    $vv->employees = $employees;

                    // get assigned employees when have a privilege of view by company
                    $allowed = array();
                    $tempAssigned = @unserialize($vv->assigned_employee_id);

                    if (!empty($tempAssigned)) {
                        $this->db->select('id, firstname, lastname, middlename, suffix');
                        $this->db->from($this->employeeTable);
                        $this->db->where_in("id", $tempAssigned);
                        $_qTempEmp = $this->db->get();
                        if($_qTempEmp->num_rows() > 0){
                            foreach($_qTempEmp->result() as $rs){
                                $tempRs = (array) $rs;
                                $_tempName = $this->core_layout->getDisplayName($tempRs);
                                $_tempName = isset($_tempName["display_name_1"]) && $_tempName["display_name_1"] ? $_tempName["display_name_1"]: "No assigned name";
                                $allowed[] = $_tempName;
                            }
                        }
                    }
                    $vv->assigned_employees = $allowed;
                    // get assigned employees when have a privilege of view by company


                    $vv->edit_url = site_url("payroll/employee/get_employee_group_data/edit/{$vv->id}");
                    $vv->archive_url = site_url("payroll/employee/get_employee_group_data/archive/{$vv->id}");
                    unset($vv->employee_id, $vv->assigned_employee_id, $vv->is_allow_view);
                    $arrData[$kk] = $vv;
                }
            }

            return $arrData;
        }

        private function get_employee_group_count($search=null) {
            $filterFields = array("b.code", "b.description", "a.description");
            $this->db->from($this->payrollGroupTable." a");
            $this->db->join($this->companyTable." b", "b.id = a.company_id");
            $this->db->where("a.is_archived", 0);
            if(isset($search)){
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) { $this->db->like($field, $search, "both"); }
                    else { $this->db->or_like($field, $search, "both"); }
                }
                $this->db->group_end();
            }

            $query = $this->db->get();
            return $query->num_rows();
        }

        function getEmployeeGroupModal($type="new"){
            $resultset = array();
            if($type){
                $html = "";
                switch($type){
                    case "new":
                        $html = $this->load->view("payroll/payroll/modals/new_employee_group_modal", null, true);
                    break;
                    default:
                        $html = $this->load->view("payroll/payroll/modals/new_employee_group_modal", null, true);
                     break;
                }
                $resultset["response"] = true;
                $resultset["html"] = $html;
            }else{
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getEmployeeGroupData($type="edit", $id=null){
            $resultset = array();
            if($type && $id){
                $this->db->select("a.*, IFNULL(b.description, '') as company, IFNULL(b.code, '') as company_code");
                $this->db->from($this->payrollGroupTable." a");
                $this->db->join($this->companyTable." b", "b.id = a.company_id", "LEFT");
                $this->db->where(array(
                    "a.id"=>$id,
                    "a.is_archived"=>0
                    ));
                $qTemp = $this->db->get();
                if($qTemp->num_rows() == 1){
                    $employees = array();
                    $tempRow = $qTemp->row();
                    $tempRow->employee_id = @unserialize($tempRow->employee_id);
                    $this->db->from($this->employeeTable);
                    $this->db->where_in("id", $tempRow->employee_id);
                    $qTempEmp = $this->db->get();
                    if($qTempEmp->num_rows() > 0){
                        foreach($qTempEmp->result() as $rs){
                            $tempRs = (array) $rs;
                            $tempName = $this->core_layout->getDisplayName($tempRs);
                            $tempName = isset($tempName["display_name_1"]) && $tempName["display_name_1"] ? $tempName["display_name_1"]: "No assigned name";
                            $employees[] = array(
                                "id"=>$rs->id,
                                "text"=>$tempName,
                            );
                        }
                    }
                    $tempRow->employees = $employees;

                    $this->db->reset_query();

                    // get assigned employees when have a privilege of view by company
                    $allowed = array();
                    $tempRow->assigned_employee_id = @unserialize($tempRow->assigned_employee_id);

                    if (!empty($tempRow->assigned_employee_id)) {
                        $this->db->select('id, firstname, lastname, middlename, suffix');
                        $this->db->from($this->employeeTable);
                        $this->db->where_in("id", $tempRow->assigned_employee_id);
                        $_qTempEmp = $this->db->get();
                        if($_qTempEmp->num_rows() > 0){
                            foreach($_qTempEmp->result() as $rs){
                                $tempRs = (array) $rs;
                                $tempName = $this->core_layout->getDisplayName($tempRs);
                                $tempName = isset($tempName["display_name_1"]) && $tempName["display_name_1"] ? $tempName["display_name_1"]: "No assigned name";
                                $allowed[] = array(
                                    "id"=>$rs->id,
                                    "text"=>$tempName,
                                );
                            }
                        }
                    }
                    $tempRow->allowed = $allowed;
                    // get assigned employees when have a privilege of view by company

                    $arrData = array("data" => $tempRow);
                    $html = "";
                    switch($type){
                        case "edit":
                            $html = $this->load->view("payroll/payroll/modals/edit_employee_group_modal", $arrData, true);
                        break;
                        case "archive":
                            $html = $this->load->view("payroll/payroll/modals/archive_employee_group_modal", $arrData, true);
                        break;
                        default:
                            $html = $this->load->view("payroll/payroll/modals/edit_employee_group_modal", $arrData, true);
                        break;
                    }
                    $resultset["response"] = true;
                    $resultset["html"] = $html;
                    $resultset["row"] = $tempRow;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getEmployeeGroupForFilter($id=null){
            $resultset = array();
            $results = array();
            $get = $this->input->get();
            $tempIds = array();
            $allFilter = isset($get["all_filter"]) && $get["all_filter"] == "true" ? true: false;
            $hasCompanySearch = isset($get["company_id"]) && $get["company_id"] ? true: false;

            $idx = array();
            $empIds = array();
            $this->db->from($this->payrollGroupTable);
            if($hasCompanySearch){ $this->db->where("company_id", $get["company_id"]); }
            $this->db->where("is_archived", 0);
            $ps_group = $this->db->get();
            
            if($ps_group->num_rows() > 0){
                foreach ($ps_group->result() as $key => $value) {
                    $temp = @unserialize($value->employee_id);
                    $tempGroup = (isset($value->description) && $value->description)? strtoupper(trim($value->description)): "";
                    if(is_array($temp) && count($temp) > 0){
                        $idx = array_unique(array_merge($idx, $temp));
                    }
                    foreach ($temp as $empIdx) {
                        if($tempGroup){
                            $empIds[$empIdx][] = $tempGroup;
                        }
                    }
                }
            }

            /*** payroll group data ***/
            $tempEmployeeIdx = array();
            $this->db->select("pg.*, UPPER(pg.description) as description, UPPER(IF(comp.code IS NULL, 'ALL FILTER', comp.code)) as company_code");
            $this->db->from($this->payrollGroupTable." as pg");
            $this->db->join($this->companyTable." as comp", "comp.id = pg.company_id", "LEFT");
            $this->db->where("pg.is_archived", 0);
            $this->db->order_by("pg.company_id", "ASC");
            $qTempPG = $this->db->get();
            if($qTempPG->num_rows() > 0){
                foreach ($qTempPG->result() as $tempRow) {
                    $employeeIds = @unserialize($tempRow->employee_id);
                    $companyId = $tempRow->company_id;
                    $pgId = $tempRow->id;
                    if(is_array($employeeIds) && count($employeeIds) > 0){
                        foreach ($employeeIds as $empIdxx) {
                            if(trim($tempRow->description)){
                                $tempEmployeeIdx[$empIdxx][] = $tempRow->description;
                            }
                        }
                    }
                }
            }
            /*** payroll group data ***/
            
            $idx = array_map("intval", $idx);

            $this->db->select("a.id, a.lastname, a.firstname, a.middlename, a.suffix");
            $this->db->from($this->employeeTable." a");
            $this->db->join($this->companyTable." b", "b.id = a.company_id OR b.description = a.company_id OR b.code = a.company_id");
            $this->db->where("a.employee_status", "Active");
            if(isset($get["company_id"]) && $get["company_id"]){
                $this->db->where("b.id", $get["company_id"]);
            }
            if(isset($get["term"]) && $get["term"]){
                $this->db->group_start();
                $this->db->like("a.lastname", $get["term"], "both");
                $this->db->or_like("a.firstname", $get["term"], "both");
                $this->db->or_like("a.middlename", $get["term"], "both");
                $this->db->or_like("a.suffix", $get["term"], "both");
                $this->db->or_like("CONCAT(a.firstname, ' ', a.lastname, ' ', a.suffix)", $get["term"], "both");
                $this->db->group_end();
            }
            $this->db->limit(10);
            $this->db->order_by("a.firstname", "ASC");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() > 0){
                foreach ($qTemp->result() as $kk => $vv) {
                    $tempIdx = intval($vv->id);
                    $optDisabled = (in_array($tempIdx, $idx))? true: false;
                    $tempGroupx = isset($empIds[$tempIdx]) && $empIds[$tempIdx] ? array_unique($empIds[$tempIdx]): false;
                    
                    $tempRsx = (array) $vv;
                    $tempRs = $this->core_layout->getDisplayName($tempRsx);
                    $tempName = (object) $tempRs;
                    $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No Assigned Name";

                    $tempOptionClass = $optDisabled ? "m--font-boldest":"";
                    $tempHtml = "<p class='m--marginless mb-5'>
                    <span class='mr-5 {$tempOptionClass}'>{$tempName}</span>";
                    if(is_array($tempGroupx) && count($tempGroupx) > 0){
                        foreach ($tempGroupx as $key => $psGroup) {
                            $tempHtml .= "<span class='m-badge m-badge--metal m-badge--wide m-badge--rounded mr-1 m--regular-font-size-sm5 pull-right'>{$psGroup}</span>";
                        }
                    }
                    $tempHtml .= "</p>";

                    $arrData = array();
                    $arrData["id"] = $vv->id;
                    $arrData["text"] = $tempName;
                    $arrData["disabled"] = $optDisabled;
                    $arrData["html"] = $tempHtml;
                    $results[] = $arrData;
                }
            }else{
                $this->db->select("a.id, a.lastname, a.firstname, a.middlename, a.suffix");
                $this->db->from($this->employeeTable." a");
                $this->db->where("a.employee_status", "Active");
                if(isset($get["company_id"]) && $get["company_id"]){
                    $this->db->where("a.company_id", $get["company_id"]);
                }
                if(isset($get["term"]) && $get["term"]){
                    $this->db->group_start();
                    $this->db->like("a.lastname", $get["term"], "both");
                    $this->db->or_like("a.firstname", $get["term"], "both");
                    $this->db->or_like("a.middlename", $get["term"], "both");
                    $this->db->or_like("a.suffix", $get["term"], "both");
                    $this->db->or_like("CONCAT(a.firstname, ' ', a.lastname, ' ', a.suffix)", $get["term"], "both");
                    $this->db->group_end();
                }
                $this->db->limit(10);
                $this->db->order_by("a.firstname", "ASC");
                $_qTemp = $this->db->get();
                if($_qTemp->num_rows() > 0){
                    foreach ($_qTemp->result() as $kkx => $vvx) {
                        $tempIdx = intval($vvx->id);
                        $optDisabled = (in_array($tempIdx, $tempEmployeeIdx))? true: false;
                        $tempGroupx = isset($tempEmployeeIdx[$tempIdx]) && $tempEmployeeIdx[$tempIdx] ? array_unique($tempEmployeeIdx[$tempIdx]): false;

                        $tempRsx = (array) $vvx;
                        $tempRs = $this->core_layout->getDisplayName($tempRsx);
                        $tempName = (object) $tempRs;
                        $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No Assigned Name";

                        $tempOptionClass = $optDisabled ? "m--font-boldest":"";
                        $tempHtml = "<p class='m--marginless mb-5'>
                        <span class='mr-5 {$tempOptionClass}'>{$tempName}</span>";
                        if(is_array($tempGroupx) && count($tempGroupx) > 0){
                            foreach ($tempGroupx as $key => $psGroup) {
                                $tempHtml .= "<span class='m-badge m-badge--metal m-badge--wide m-badge--rounded mr-1 m--regular-font-size-sm5 pull-right'>{$psGroup}</span>";
                            }
                        }
                        $tempHtml .= "</p>";

                        $arrData = array();
                        $arrData["id"] = $vvx->id;
                        $arrData["text"] = $tempName;
                        $arrData["disabled"] = $optDisabled;
                        $arrData["html"] = $tempHtml;
                        $results[] = $arrData;
                    }
                }
            }

            if($allFilter == false && $hasCompanySearch == false){
                $results = array();
            }

            $resultset["results"] = $results;
            return $resultset;
        }

        function setNewPayrollEmployeeGroup(){
            $resultset = array();
            $post = $this->input->post();
            if($post){
                $post["employee_id"] = serialize($post["employee_id"]);
                $post["created_by"] = $this->core_layout->getCurrentEmployeeId();
                $post["created_at"] = date("Y-m-d H:i:s");
                $post['is_allow_view'] = isset($post['is_allow_view']) && $post['is_allow_view'] ? 1: 0;
                $post['assigned_employee_id'] = isset($post['is_allow_view']) && $post['is_allow_view'] == 1 ? serialize($post['assigned_employee_id']) : serialize(array());

                $added = $this->db->insert($this->payrollGroupTable, $post);
                if($added){
                    $resultset["response"] = true;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function updatePayrollEmployeeGroup(){
            $resultset = array();
            $post = $this->input->post();
            if($post){
                $tempWhere = array();
                $tempWhere["id"] = $post["id"];
                unset($post["id"]);

                $post["employee_id"] = serialize($post["employee_id"]);
                $post["updated_by"] = $this->core_layout->getCurrentEmployeeId();
                $post["updated_at"] = date("Y-m-d H:i:s");
                $post['is_allow_view'] = isset($post['is_allow_view']) && $post['is_allow_view'] ? 1: 0;
                $post['assigned_employee_id'] = isset($post['is_allow_view']) && $post['is_allow_view'] == 1 ? serialize($post['assigned_employee_id']) : serialize(array());

                $updated = $this->db->update($this->payrollGroupTable, $post, $tempWhere);
                if($updated && $this->db->affected_rows() > 0){
                    $resultset["response"] = true;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function archivePayrollEmployeeGroup(){
            $resultset = array();
            $post = $this->input->post();
            if($post){
                $arrData = array();
                $arrData["updated_by"] = $this->core_layout->getCurrentEmployeeId();
                $arrData["updated_at"] = date("Y-m-d H:i:s");
                $arrData["is_archived"] = 1;

                $updated = $this->db->update($this->payrollGroupTable, $arrData, $post);
                if($updated && $this->db->affected_rows() > 0){
                    $resultset["response"] = true;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getDuplicatePayrollGroup(){
            $resulset = array();
            $data = array();
            $query = $this->db->get_where($this->payrollGroupTable, array("is_archived"=>0));
            if($query->num_rows() > 0){
                $nData = array();
                $arr_employee = array();
                foreach ($query->result() as $key => $value) {
                    $empIds = @unserialize($value->employee_id);
                    if(is_array($empIds) && count($empIds) > 0){
                        foreach ($empIds as $idx) {
                            $arr_employee[$idx][] = "{$value->id}:::{$value->description}";
                        }
                    }
                }

                if(count($arr_employee) > 0){
                    foreach ($arr_employee as $kk => $vv) {
                        $tempArrData = array_unique($vv);
                        if(count($tempArrData) > 1){
                            $tempRow = array();
                            $empRecord = $this->core_layout->getEmployeeData($kk);
                            if(count($empRecord) > 0){
                                $empName = isset($empRecord["display_name_1"]) && $empRecord["display_name_1"] ? strtoupper($empRecord["display_name_1"]): "NO ASSIGNED NAME";
                                $tempRow["id"] = $kk;
                                $tempRow["employee_name"] = $empName;
                                $tempRow["group_count"] = count($tempArrData);
                                foreach ($tempArrData as $datax) {
                                    $tempData = explode(":::", $datax);
                                    $tempRow["payroll_group"][] = array("id"=>$tempData[0], "description"=>strtoupper(trim($tempData[1])));
                                }
                                if(count($tempRow) > 0){
                                    $data[] = $tempRow;
                                }
                            }
                        }
                    }
                }
            }
            if(count($data) > 0){
                $resultset["response"] = true;
                $resultset["data"] = $data;
                $resultset["duplicate_count"] = count($data);
            }else{
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getEmpAttachment($id) {
            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "";

            $rowData = $this->empAttach_list($id, $search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->empAttach_count($id, $search);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function empAttach_list($id = "", $search = null, $limit = 10, $offset = 0, $sortBy = "ASC", $sortOrder = array()) {
            $get = $this->input->get();
            $filterFields = array("b.firstname", "b.lastname");
            $this->db->select('CONCAT(b.firstname, " ", b.lastname) as name, a.uploaded_dt, a.image');
            $this->db->from("gcchris.loans_images a");
            $this->db->join("gccmaster.tblemployees b", "b.id = a.uploaded_by", "LEFT");
            $this->db->where("a.loan_id", $id);

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
            empty($sortOrder) ? $this->db->order_by("a.uploaded_dt", "desc") : $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $rs->thumbnail = site_url()."uploads/files/loans/loancode_{$get['emp_id']}/thumbnails/".$rs->image;
                    $rs->image = site_url()."uploads/files/loans/loancode_{$get['emp_id']}/".$rs->image;
                    $rs->name = $rs->name;
                    $rs->uploaded_dt = date('Y-m-d h:i:s A', strtotime($rs->uploaded_dt));
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

        private function empAttach_count($id, $search = null) {
            $get = $this->input->get();
            $filterFields = array("b.firstname", "b.lastname");
            $this->db->select('CONCAT(b.firstname, " ", b.lastname) as name, a.uploaded_dt, a.image');
            $this->db->from("gcchris.loans_images a");
            $this->db->join("gccmaster.tblemployees b", "b.id = a.uploaded_by", "LEFT");
            $this->db->where("a.loan_id", $id);

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
        private function uploadFiles($id, $primary_pic = NULL) {
            $upload_errors = array();
            $user = $this->core_layout->getUserLoggedIn();
            $user_id = $user["id"];

            $count = count($_FILES['files']['name']);
            $uploaded = array();
            $uploading_primary_pic_failed = false;
            $success_primary_pic = null;

            $this->db->select('emp_id');
            $this->db->where('id', $id);
            $query = $this->db->get('gcchris.loans');
            $q = $query->row();

            for ($i = 0; $i < $count; $i++) {
                if (!empty($_FILES['files']['name'][$i])) {
                    $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                    $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                    $_FILES['file']['size'] = $_FILES['files']['size'][$i];
                    $original_name = $_FILES['files']['name'][$i];

                    $folder = 'uploads/files/loans/loancode_'.$q->emp_id;

                    if (!file_exists($folder)) {
                        mkdir($folder, 0777, true);
                    }

                    $config['upload_path'] = $folder;
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = '10000';
                    $config['file_name'] = $_FILES['file']['name'];

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('file')) {
                        $upload_data = $this->upload->data();
                        $filename = $upload_data['file_name'];
                        array_push($uploaded, $original_name);

                        $timestamp = new DateTime("now", new DateTimeZone('Asia/Manila'));
                        $fields = array("loan_id" => $id, "image" => $filename, "uploaded_by" => $user_id, "uploaded_dt" => $timestamp->format('Y-m-d H:i:s'));
                        $this->db->insert("gcchris.loans_images", $fields);

                        if ($this->file_upload->createThumbnailPathFolder($folder)) {
                            $this->resizeImage($filename, $folder, '/thumbnails', 75, 75);
                        }
                    } else {
                        array_push($upload_errors,
                            array(
                                "filename" => $original_name,
                                "message" => $this->upload->display_errors(),
                            ));
                    }
                }
            }
        }

        private function resizeImage($filename, $source_path, $target_path, $width, $height = 0) {
            $_source_path = rtrim($source_path, '/') . "/" . $filename;
            $_target_path = rtrim($source_path, '/') . $target_path;

            $config = array(
                'image_library' => 'gd2',
                'source_image' => $_source_path,
                'new_image' => $_target_path,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '',
                'width' => $width
            );

            if ($height) {
                $config["height"] = $height;
            }

            $this->image_lib->initialize($config);
            return $this->image_lib->resize();
        }

        public function getCaRef($id){
            $get = $this->input->get();
            $result = array();

            $this->db->select('a.id, a.reference_no as text, b.id as loan_id, a.amt_approved as amount');
            $this->db->from('gcceforms.cash_advance a');
            $this->db->join('gcchris.loans b', 'b.reference_id = a.id AND b.reference = a.reference_no', 'LEFT');
            $this->db->where('a.employee', $id);
            $this->db->where('a.status', 'Approved');

            if (isset($get['q'])) {
                $this->db->like('a.reference_no', $get['q']);
            }

            $query = $this->db->get();

            foreach ($query->result() as $key => $rs) {
                $data = array();

                $data["id"] = $rs->id;
                $rs->id = $rs->id.'-'.$rs->text;

                $data['text'] = $rs->text;

                if($rs->loan_id){
                    $data["text"] = $rs->text.' (in-use)'; 
                    $data['disabled'] = true;
                    $data['html'] = $rs->text.' | '.number_format($rs->amount, 2).' (in-use)';
                }else{
                    $data['html'] = $rs->text.' | '.number_format($rs->amount, 2);
                }

                $data['refnum'] = $rs->text;
                
                $result[] = $data;
            }

            return array('results' => $result);
        }

        public function fieldValueApprovals($module=null, $id=null){
            $data = array();
            if($id && $module){
                $currentLoggedIn = $this->core_layout->getCurrentEmployeeId();
                $this->db->select("a.*, UPPER(CONCAT(b.firstname, ' ',
                CASE WHEN UPPER(TRIM(b.middlename)) != 'N/A' AND UPPER(TRIM(b.middlename)) != 'NONE' AND
                        TRIM(b.middlename) !='' AND b.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(b.middlename, 1, 1), '.') ELSE ''
                END,' ', b.lastname,
                CASE WHEN UPPER(TRIM(b.suffix)) != 'N/A' AND
                    UPPER(TRIM(b.suffix !='NONE')) AND b.suffix !='' AND
                        b.suffix IS NOT NULL THEN CONCAT(' ', b.suffix) ELSE ''
                END)) as created_by_name, UPPER(CONCAT(c.firstname, ' ',
                CASE WHEN UPPER(TRIM(c.middlename)) != 'N/A' AND UPPER(TRIM(c.middlename)) != 'NONE' AND
                        TRIM(c.middlename) !='' AND c.middlename IS NOT NULL
                    THEN CONCAT(SUBSTR(c.middlename, 1, 1), '.') ELSE ''
                END,' ', c.lastname,
                CASE WHEN UPPER(TRIM(c.suffix)) != 'N/A' AND
                    UPPER(TRIM(c.suffix !='NONE')) AND c.suffix !='' AND
                        c.suffix IS NOT NULL THEN CONCAT(' ', c.suffix) ELSE ''
                END)) as approval_by_name, IF(a.created_by = $currentLoggedIn, 1, 0) as is_owner");
                $this->db->from("gccmaster.field_value_approval as a");
                $this->db->join("gccmaster.tblemployees as b", "b.id = a.created_by", "INNER");
                $this->db->join("gccmaster.tblemployees as c", "c.id = a.approval_by", "LEFT");
                $this->db->where("a.unique_id", $id);
                $this->db->where("a.is_approved", 0);
                $this->db->order_by("a.id", "DESC");
                $qTemp = $this->db->get();
                if($qTemp->num_rows() > 0){ $data = $qTemp->result(); }
            }
    
            return $data;
        }

        public function getMonthlyPaidEmployeeSelect2(){
            $result = array();
            $this->db->select("emp.id, UPPER(CONCAT(emp.firstname, ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END,' ', emp.lastname,
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END)) as text, mpe.id as mpe_id");
            $this->db->from($this->employeeTable." as emp");
            $this->db->join($this->tbl_timesheet_monthly_employees." as mpe", "mpe.emp_id = emp.id", "LEFT");
            $this->db->where("emp.payroll_type", "monthly");
            $this->db->where("emp.payout_sched", 1);
            $this->db->where("emp.employee_status", "active");
            $this->db->order_by("firstname", "ASC");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() > 0){
                foreach ($qTemp->result() as $value) {
                    $value->disabled = intval($value->mpe_id) > 0 ? TRUE: false;
                }
            }
            $result = $qTemp->result();

            return $result;
        }

        function set_approved_allowance($arr){
            $user = $this->core_layout->getUserLoggedIn();
            $user_emp_id = $user["employee_id"];
            $data = array();
            $rate = '';
            $rate_fr = '';
            $historyStatus = false;
            $basic = 0;

            if(!isset($arr["is_active"])){ $arr["is_active"] = 0; }
            $isActiveState = intval($arr["is_active"]) == 1;

            $this->db->select('b.name, a.basic_rate, a.payroll_type');
            $this->db->from($this->employeeTable.' as a');
            $this->db->join($this->positionTable.' as b', 'b.id = a.position OR b.name = a.position', 'LEFT');
            $this->db->where('a.id', $arr['emp_id']);
            $query = $this->db->get()->row();
            $this->db->reset_query();
            $basic = $query->basic_rate;

            if($query->payroll_type == 'daily'){ $payroll = 'Basic Daily Rate'; }
            else if($query->payroll_type == 'monthly'){ $payroll = 'Monthly Rate'; }
            else{ $payroll = 'Hourly Rate'; }

            if(isset($arr['frequency']) && $arr['frequency']){
                $rate_fr = $arr['frequency'] == 'day' ? 'Daily Allowance' : 'Monthly Allowance';
            }

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
      
        public function getEmployeeList(){
            $result = array();
            $get = $this->input->get();

            $this->db->select("id, UPPER(CONCAT(firstname, ' ',
            CASE WHEN UPPER(TRIM(middlename)) != 'N/A' AND UPPER(TRIM(middlename)) != 'NONE' AND
                    TRIM(middlename) !='' AND middlename IS NOT NULL
                THEN CONCAT(SUBSTR(middlename, 1, 1), '.') ELSE ''
            END,' ', lastname,
            CASE WHEN UPPER(TRIM(suffix)) != 'N/A' AND
                UPPER(TRIM(suffix !='NONE')) AND suffix !='' AND
                suffix IS NOT NULL THEN CONCAT(' ', suffix) ELSE ''
            END)) as text");
            $this->db->from($this->employeeTable);
            $this->db->where("employee_status", "active");

            if (isset($get['company_id']) && $get['company_id']) {
                $this->db->where('company_id', $get['company_id']);
            }

            if (isset($get['q']) && $get['q']) {
                $this->db->group_start();
                    $this->db->like('firstname', $get['q'], 'both');
                    $this->db->or_like('lastname', $get['q'], 'both');
                $this->db->group_end();
            }

            $this->db->order_by("firstname", "ASC");
            $this->db->limit(10);
            $qTemp = $this->db->get();

            if($qTemp->num_rows() > 0){
                $result = $qTemp->result();
            }

            return array('results' => $result);
        }

        public function getSelect2Employee(){
            $result = array();
            $get = $this->input->get();

            $taxableEmployeeIds = $this->getFixedTaxableEmployeeId();
            $payrollSettingsValue = $this->getPayrollSettings();
            $this->db->select("id, UPPER(CONCAT(firstname, ' ',
            CASE WHEN UPPER(TRIM(middlename)) != 'N/A' AND UPPER(TRIM(middlename)) != 'NONE' AND
                    TRIM(middlename) !='' AND middlename IS NOT NULL
                THEN CONCAT(SUBSTR(middlename, 1, 1), '.') ELSE ''
            END,' ', lastname,
            CASE WHEN UPPER(TRIM(suffix)) != 'N/A' AND
                UPPER(TRIM(suffix !='NONE')) AND suffix !='' AND
                suffix IS NOT NULL THEN CONCAT(' ', suffix) ELSE ''
            END)) as text, basic_rate, payroll_type");
            $this->db->from($this->employeeTable);
            $this->db->where("employee_status", "active");
            if($taxableEmployeeIds){ $this->db->where_not_in("id", explode(',', $taxableEmployeeIds)); }
            if($payrollSettingsValue && $payrollSettingsValue !== 0){
                $this->db->where("basic_rate >", $payrollSettingsValue);
            }

            if (isset($get['q']) && $get['q']) {
                $this->db->group_start();
                    $this->db->like('firstname', $get['q'], 'both');
                    $this->db->or_like('lastname', $get['q'], 'both');
                $this->db->group_end();
            }

            $this->db->order_by("firstname", "ASC");
            $this->db->limit(10);
            $qTemp = $this->db->get();

            if($qTemp->num_rows() > 0){
                $result = $qTemp->result();
            }

            return array("results" => $result);
        }

        protected function getPayrollSettings(){
            $this->db->select("setting_value");
            $taxDeduction = $this->db->get_where("payroll.settings", array("setting_name"=>"fixed_tax_monthly_income_deduction"));
            if($taxDeduction->num_rows() == 1){
                $row = $taxDeduction->row();
                return $row->setting_value;
            }else{ return 0; }
        }

        public function updateStatusTaxableDeduction(){
            $post = $this->input->post();
            $resultset = array();
            $logInfo = null;
            if(isset($post["id"], $post["is_active"]) && $post["id"]){
                $reverseStatus = $post["is_active"] == 1 ? 0 : 1;
                $tempState = $reverseStatus == 1 ? "Activated" : "Deactivated";
                $failedState = $reverseStatus == 1 ? "Failed activating" : "Failed deactivating";

                $arrData["is_active"] = $reverseStatus;
                $arrData['last_updated_at'] = date("Y-m-d H:i:s");
                $arrData['last_updated_by'] = $this->core_layout->getCurrentEmployeeId();
                $currentRecord = $this->getCurrentEmployeeData($post["employee_id"]);
                $getEmpTaxable = $this->db->get_where($this->tbl_payroll_fixed_taxable, array("id"=>$post['id']));
                if($getEmpTaxable->num_rows() == 1){
                    $taxableAmount = number_format($post["taxable_amount"], 2, ".", ",");
                    $updated = $this->db->update($this->tbl_payroll_fixed_taxable, $arrData, array("id"=>$post['id']));
                    if($updated && $this->db->affected_rows() > 0){
                        $resultset["response"] = true;
                        $logInfo = isset($currentRecord->employee_name) && $currentRecord->employee_name ? "Fixed taxable deduction has been <strong>`{$tempState}`</strong> for employee <strong>`{$currentRecord->employee_name}`</strong> with taxable amount of <strong>`{$taxableAmount}`</strong>.": null;
                    }else{
                        $resultset["response"] = false;
                        $logInfo = isset($currentRecord->employee_name) && $currentRecord->employee_name ? "{$failedState} fixed taxable deduction for employee <strong>`{$currentRecord->employee_name}`</strong> with taxable amount of <strong>`{$taxableAmount}`</strong>.": null;
                    }
                }else{
                    $resultset["response"] = false;
                    $logInfo = isset($currentRecord->employee_name) && $currentRecord->employee_name ? "{$failedState} fixed taxable deduction for employee <strong>`{$currentRecord->employee_name}`</strong>, record not found.": null;
                }
            }else{
                $resultset["response"] = false;
                $logInfo = "Failed updating fixed taxable deduction, required parameters missing.";
            }

            if($logInfo){
                $resultset["toastr_msg"] = $logInfo;
                $type = $resultset["response"] ? "success" : "failed";
                $logType = $resultset["response"] ? "user": "system";
                $this->core_layout->setEventLog($logInfo, "update", $type, "payroll", $logType);
            }
            return $resultset;
        }

        public function updateTaxableDeduction(){
            $post = $this->input->post();
            $resultset = array();
            $logInfo = null;
            if(isset($post["id"], $post["taxable_amount"]) && $post["id"] && $post["taxable_amount"]){
                $post['last_updated_at'] = date("Y-m-d H:i:s");
                $post['last_updated_by'] = $this->core_layout->getCurrentEmployeeId();
                $currentRecord = $this->getCurrentEmployeeData($post["employee_id"]);
                $getEmpTaxable = $this->db->get_where($this->tbl_payroll_fixed_taxable, array("id"=>$post['id']));
                if($getEmpTaxable->num_rows() == 1){
                    $currentTaxableAmount = number_format($getEmpTaxable->row()->taxable_amount, 2, ".", ",");
                    $taxableAmount = number_format($post["taxable_amount"], 2, ".", ",");
                    $updated = $this->db->update($this->tbl_payroll_fixed_taxable, $post, array("id"=>$post['id']));
                    if($updated && $this->db->affected_rows() > 0){
                        $resultset["response"] = true;
                        $logInfo = isset($currentRecord->employee_name) && $currentRecord->employee_name ? "Fxied taxable deduction has been <strong>`Updated`</strong> for employee <strong>`{$currentRecord->employee_name}`</strong> with taxable amount from <strong>`{$currentTaxableAmount}`</strong> to <strong>`{$taxableAmount}`</strong>.": null;
                    }else{
                        $resultset["response"] = false;
                        $logInfo = isset($currentRecord->employee_name) && $currentRecord->employee_name ? "Failed updating fixed taxable deduction for employee <strong>`{$currentRecord->employee_name}`</strong> with taxable amount from <strong>`{$currentTaxableAmount}`</strong> to <strong>`{$taxableAmount}`</strong>.": null;
                    }
                }else{
                    $resultset["response"] = false;
                    $logInfo = isset($currentRecord->employee_name) && $currentRecord->employee_name ? "Failed updating fixed taxable deduction for employee <strong>`{$currentRecord->employee_name}`</strong>, record not found.": null;
                }
            }else{
                $resultset["response"] = false;
                $logInfo = "Failed updating fixed taxable deduction, required parameters missing.";
            }

            if($logInfo){
                $resultset["toastr_msg"] = $logInfo;
                $type = $resultset["response"] ? "success" : "failed";
                $logType = $resultset["response"] ? "user": "system";
                $this->core_layout->setEventLog($logInfo, "update", $type, "payroll", $logType);
            }
            return $resultset;
        }

        public function addTaxableDeduction(){
            $post = $this->input->post();
            $resultset = array();
            $logInfo = null;
            if(isset($post["employee_id"], $post["taxable_amount"]) && $post["employee_id"] && $post["taxable_amount"]){
                $post['created_at'] = date("Y-m-d H:i:s");
                $post['created_by'] = $this->core_layout->getCurrentEmployeeId();
                $currentRecord = $this->getCurrentEmployeeData($post["employee_id"]);
                $getEmpTaxable = $this->db->get_where($this->tbl_payroll_fixed_taxable, array("employee_id"=>$post['employee_id']));
                if($getEmpTaxable->num_rows() == 0){
                    $taxableAmount = number_format($post["taxable_amount"], 2, ".", ",");
                    $added = $this->db->insert($this->tbl_payroll_fixed_taxable, $post);
                    if($added && $this->db->affected_rows() > 0){
                        $resultset["response"] = true;
                        $logInfo = isset($currentRecord->employee_name) && $currentRecord->employee_name ? "Fixed taxable deduction of employee named <strong>`{$currentRecord->employee_name}`</strong> has been added with taxable amount of <strong>`{$taxableAmount}`</strong>.": null;
                    }else{
                        $resultset["response"] = false;
                        $logInfo = isset($currentRecord->employee_name) && $currentRecord->employee_name ? "Failed to add new fixed taxable deduction of employee named <strong>`{$currentRecord->employee_name}`</strong> with taxable amount of <strong>`{$taxableAmount}`</strong>.": null;
                    }
                }else{
                    $resultset["response"] = false;
                    $logInfo = isset($currentRecord->employee_name) && $currentRecord->employee_name ? "Failed to add new fixed taxable deduction of employee named <strong>`{$currentRecord->employee_name}`</strong>, employee already has a fixed taxable deduction.": null;
                }
            }else{
                $resultset["response"] = false;
                $logInfo = "Failed updating taxable deduction, required parameters missing.";
            }

            if($logInfo){
                $resultset["toastr_msg"] = $logInfo;
                $type = $resultset["response"] ? "success" : "failed";
                $logType = $resultset["response"] ? "user": "system";
                $this->core_layout->setEventLog($logInfo, "insert", $type, "payroll", $logType);
            }

            return $resultset;
        }

        public function getFixedTaxableDeduction(){
            $resultset = array();
            $post = $this->input->post();

            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;

            $filterFields = array("emp.lastname", "emp.firstname", "emp.middlename", "emp.suffix",
            "cemp.lastname", "cemp.firstname", "cemp.middlename", "cemp.suffix",
            "uemp.lastname", "uemp.firstname", "uemp.middlename", "uemp.suffix",
            "psfx.basic_rate", "psfx.payroll_type", "psfx.taxable_amount");

            $query = $this->getFixedTaxableDeductionQuery($search, $limit, $offset, $sortBy, $sortOrder, $filterFields);
            $ctrFilter = $this->getFixedTaxableDeductionCount($search, $filterFields);
            $resultset["recordsTotal"] = $ctrFilter;
            $resultset["recordsFiltered"] = $ctrFilter;
            $resultset["data"] = $query->result();

            return $resultset;
        }

        protected function getFixedTaxableDeductionCount($search, $filterFields){
            $this->db->select("psfx.id, psfx.employee_id, psfx.is_active");
            $this->db->from($this->tbl_payroll_fixed_taxable." as psfx");
            $this->db->join($this->employeeTable." as emp", "emp.id = psfx.employee_id", "inner");
            $this->db->join($this->employeeTable." as cemp", "cemp.id = psfx.created_by", "left");
            $this->db->join($this->employeeTable." as uemp", "uemp.id = psfx.last_updated_by", "left");

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

        protected function getFixedTaxableDeductionQuery($search, $limit, $offset, $sortBy, $sortOrder, $filterFields){
            $this->db->select("psfx.id, psfx.employee_id, CONCAT(UPPER(TRIM(emp.firstname)), ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(UPPER(SUBSTR(emp.middlename, 1, 1)), '.') ELSE ''
            END,' ', UPPER(TRIM(emp.lastname)),
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(emp.suffix))) ELSE ''
            END) as employee_name, psfx.basic_rate, psfx.payroll_type, psfx.taxable_amount,
            IF(psfx.last_updated_by = 0,
                CONCAT(UPPER(TRIM(cemp.firstname)), ' ',
                CASE WHEN UPPER(TRIM(cemp.middlename)) != 'N/A' AND UPPER(TRIM(cemp.middlename)) != 'NONE' AND
                        TRIM(cemp.middlename) !='' AND cemp.middlename IS NOT NULL
                    THEN CONCAT(UPPER(SUBSTR(cemp.middlename, 1, 1)), '.') ELSE ''
                END,' ', UPPER(TRIM(cemp.lastname)),
                CASE WHEN UPPER(TRIM(cemp.suffix)) != 'N/A' AND
                    UPPER(TRIM(cemp.suffix !='NONE')) AND cemp.suffix !='' AND
                    cemp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(cemp.suffix))) ELSE ''
                END),
                CONCAT(UPPER(TRIM(uemp.firstname)), ' ',
                CASE WHEN UPPER(TRIM(uemp.middlename)) != 'N/A' AND UPPER(TRIM(uemp.middlename)) != 'NONE' AND
                        TRIM(uemp.middlename) !='' AND uemp.middlename IS NOT NULL
                    THEN CONCAT(UPPER(SUBSTR(uemp.middlename, 1, 1)), '.') ELSE ''
                END,' ', UPPER(TRIM(uemp.lastname)),
                CASE WHEN UPPER(TRIM(uemp.suffix)) != 'N/A' AND
                    UPPER(TRIM(uemp.suffix !='NONE')) AND uemp.suffix !='' AND
                    uemp.suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(uemp.suffix))) ELSE ''
                END)
            ) as updated_by,
            IF(psfx.last_updated_at IS NULL, psfx.created_at, psfx.last_updated_at) as updated_at, psfx.is_active,
            IF(emp.basic_rate != psfx.basic_rate, ROUND(emp.basic_rate, 2), ROUND(psfx.basic_rate,2)) as tmp_rate,
            IF(emp.payroll_type != psfx.payroll_type, emp.payroll_type, psfx.payroll_type) as tmp_payroll_type");
            $this->db->from($this->tbl_payroll_fixed_taxable." as psfx");
            $this->db->join($this->employeeTable." as emp", "emp.id = psfx.employee_id", "inner");
            $this->db->join($this->employeeTable." as cemp", "cemp.id = psfx.created_by", "left");
            $this->db->join($this->employeeTable." as uemp", "uemp.id = psfx.last_updated_by", "left");

            if (isset($search)) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    ($key == 0) ? $this->db->like($field, $search, "both") : $this->db->or_like($field, $search, "both");
                }
                $this->db->group_end();
            }

            if ($limit != -1) { $this->db->limit($limit, $offset); }
            if (isset($sortOrder)) {
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            } else { $this->db->order_by('psfx.id', 'desc'); }
            return $this->db->get();
        }

        protected function getFixedTaxableEmployeeId(){
            $this->db->select("GROUP_CONCAT(DISTINCT employee_id) AS employee_id");
            $this->db->from($this->tbl_payroll_fixed_taxable);
            $qTemp = $this->db->get();
            if($qTemp->num_rows() > 0){
                return $qTemp->row()->employee_id;
            }else{
                return false;
            }
        }

        protected function getCurrentEmployeeData($empId=null){
            $this->db->select("CONCAT(UPPER(TRIM(firstname)), '',
            CASE WHEN UPPER(TRIM(middlename)) != 'N/A' AND UPPER(TRIM(middlename)) != 'NONE' AND
                    TRIM(middlename) !='' AND middlename IS NOT NULL
                THEN CONCAT(' ', SUBSTR(middlename, 1, 1), '. ') ELSE ' '
            END,'', UPPER(TRIM(lastname)),
            CASE WHEN UPPER(TRIM(suffix)) != 'N/A' AND
                UPPER(TRIM(suffix !='NONE')) AND suffix !='' AND
                suffix IS NOT NULL THEN CONCAT(' ', UPPER(TRIM(suffix))) ELSE ''
            END) as employee_name, basic_rate, UPPER(payroll_type) as payroll_type");
            $this->db->from($this->employeeTable);
            $this->db->where("id", $empId);
            $qTemp = $this->db->get();
            if ($qTemp->num_rows() > 0){ return $qTemp->row(); }
            else { return false; }
        }
        
        private function getEmployeeLoanById($id) {
            $this->db->select("emp_loans.*, master_loans.loan_name, master_loans.has_ref");
            $this->db->where("emp_loans.id", $id);
            $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id", "INNER");
            return $this->db->get("gcchris.loans emp_loans")->row();
        }

        private function logChanges($currentData, $newData) {
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
                if($change){
                    if($field == 'loan_id'){
                        $changesString.= " Field: $field, from: <strong>".$this->getLoanTypeById($change['old']) ."</strong>, to: <strong>".$this->getLoanTypeById($change['new'])."</strong>\n";
                    }
                    else if($field == 'deduction_type'){
                        $oldStatus = $change['old'] == 1 ? 'fix amount' : 'percentage';
                        $newStatus = $change['new'] == 1 ? 'fix amount' : 'percentage';
                        $changesString .= " Field: $field, from: <strong>$oldStatus</strong>, to: <strong>$newStatus</strong>\n";
                    }
                    else if($field == 'active'){
                        $oldStatus = $change['old'] == 1 ? 'active' : 'suspended';
                        $newStatus = $change['new'] == 1 ? 'active' : 'suspended';
                        $changesString .= " Field: $field, from: <strong>$oldStatus</strong>, to: <strong>$newStatus</strong>\n";
                    }
                    else if (strtolower($field) == 'percentage'){
                        $changesString.= " Field: $field, from: <strong>" . round($change['old']) . "%</strong>, to: <strong>" . round($change['new']) . "%</strong>\n";
                    }
                    else{
                        $changesString.= " Field: $field, from: <strong>$change[old]</strong>, to: <strong>$change[new]</strong>\n";
                    }
                }
            }
            return $changesString;
        }

        private function getEmployeeName($id){
            $this->db->select("id, firstname, middlename, lastname, suffix");
            $this->db->from("gccmaster.tblemployees");
            $this->db->where("id", $id);
            $query = $this->db->get();
            $rs = $query->row();
            $tempRs = (array)$rs;
            $fullname = $this->core_layout->getDisplayName($tempRs);
            $tempFullname = (object)$fullname;
            $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
            return $rs->display_name;
        }

        private function getLoanTypeById($id){
            $this->db->select("loan_name");
            $this->db->from("payroll.loans");
            $this->db->where("is_archive", 0);
            $this->db->where("id", $id);
            $query = $this->db->get();
            $result = $query->row();
            $this->db->reset_query();
            return $result->loan_name;
        }

    }