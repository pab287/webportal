<?php
    defined('BASEPATH') || exit('No direct script access allowed');

    class Holiday_model extends CI_Model {
        protected $tblHolidays = "gcchris.tblholidays";
        protected $shiftScheduleCalendarTable = "gcctimeutility.shift_schedule_calendar";
        protected $now = null;
        protected $user = null;
        protected $loggedinData = null;
        protected $loggedInUsername = null;
        protected $user_data = null;

        function __construct() {
            parent::__construct();
            $this->load->model("ams/Utilities_model", "utilities");
            $this->now = new DateTime(null, new DateTimeZone('Asia/Manila'));
            $this->now = $this->now->format('Y-m-d H:i:s');
            $this->user = $this->core_layout->getUserLoggedIn();
            $this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->loggedinData["username"];
        }

        function getHolidays($classification = null) {
            $select = "holidays.id, holidays.description title, holidays.classification description, holidays.meta, ";
            $select .= "CAST(holidays.start_date as date) start, CAST(holidays.end_date as date) end, holidays.start_time, holidays.end_time";
            $this->db->select($select, FALSE);
            $query = $this->db->get($this->tblHolidays . " holidays");
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    if (strpos(strtolower($rs->description), "holiday") === false) {
                        $rs->description = ucwords($rs->description . " holiday");
                    }
                    $rs->description = ucwords($rs->description);

                    if (strpos(strtolower($rs->description), "regular") === false) {
                        $rs->className = "m-fc-event--light m-fc-event--solid-danger";
                    } else {
                        $rs->className = "m-fc-event--accent m-fc-event--solid-danger";
                    }

                    // added for tagging company and department
                    if($rs->meta != null && $rs->meta){
                        $meta = unserialize($rs->meta);
                        $rs->company = ($meta['company'] != 'all') ? $this->getCompanyCode($meta['company']) : 'all';
                        $rs->department = ($meta['department'] != 'all') ? $this->getDeptCode($meta['department']) : 'all';
                    }else{
                        $rs->company = 'all';
                        $rs->department = 'all';
                    }
                    // added for tagging company and department

                    $startDate = $rs->start;
                    $endDate = $rs->end;

                    $startTime = ($rs->start_time && $rs->start_time !== "00:00:00") ? $rs->start_time : "";
                    $endTime = ($rs->end_time && $rs->end_time !== "00:00:00") ? $rs->end_time : "";

                    $dateFrom = date_create($startDate);
                    $dateTo = date_create($endDate);

                    $dateDiff = date_diff($dateFrom, $dateTo);
                    $days = $dateDiff->days;
                    $rs->days = $days;
                    if ($days && $days > 1) {
                        if ($startTime && $endTime) {
                            $toDate = $startDate;
                            $toEnd = $endDate;

                            while ($toDate <= $toEnd):
                                $fromStart = date("Y-m-d", strtotime($toDate));

                                $_fromDate = date("Y-m-d H:i", strtotime("{$fromStart} {$startTime}"));
                                $_toDate = date("Y-m-d H:i", strtotime("{$fromStart} {$endTime}"));
                                $toDate = date("Y-m-d", strtotime("+1 day", strtotime($toDate)));

                                $rs->start = $_fromDate;
                                $rs->end = $_toDate;
                            endwhile;
                        } else {
                            $startDate = date("Y-m-d", strtotime($startDate));
                            $endDate = date("Y-m-d", strtotime("+1 day", strtotime($endDate)));

                            $rs->start = $startDate;
                            $rs->end = $endDate;
                        }
                    } else if ($days == 1) {
                        $startDate = date("Y-m-d", strtotime($startDate));
                        $endDate = date("Y-m-d", strtotime("+1 day", strtotime($endDate)));

                        $rs->start = $startDate;
                        $rs->end = $endDate;
                    } else {
                        $startDate = ($startTime) ? date("Y-m-d H:i", strtotime("{$startDate} {$startTime}")) : date("Y-m-d", strtotime($startDate));
                        $endDate = ($endTime) ? date("Y-m-d H:i", strtotime("{$endDate} {$endTime}")) : date("Y-m-d", strtotime($endDate));

                        $rs->start = $startDate;
                        $rs->end = $endDate;
                    }
                    $result[] = $rs;
                }
                return $result;
            } else {
                return array();
            }
        }

        function getHolidaysTabular_original_function_20230419() {
            $tableConfig = $this->input->post();
            $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
            $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

            $resultSet = array();
            $select = "holidays.*, DATE_FORMAT(CAST(CONCAT(holidays.year, '-', holidays.month, '-', holidays.day) as date), '%M %d, %Y') date, DATE_FORMAT(start_date, '%M %d, %Y') date_from, DATE_FORMAT(end_date, '%M %d, %Y') date_to";

            $searchFields = "CONCAT(DATE_FORMAT(CAST(CONCAT(holidays.year, '-', holidays.month, '-', holidays.day) as date), '%M %d, %Y'), DATE_FORMAT(start_date, '%M %d, %Y'), DATE_FORMAT(end_date, '%M %d, %Y'), ";
            $searchFields .= "holidays.description, holidays.description)";

            $this->db->where("year", $tableConfigStd->search->filter_year);
            $this->db->select($select, FALSE);
            $this->db->like($searchFields, $pageOptions->search, "both");

            if ($pageOptions->order_column == 'date') {
                $this->db->order_by("STR_TO_DATE(`date_from`, '%M %d, %Y') " . $pageOptions->order_direction);
            } else {
                $this->db->order_by($pageOptions->order_column, $pageOptions->order_direction);
            }

            if ($pageOptions->length > -1) {
                $this->db->limit($pageOptions->length, $pageOptions->start);
            }

            $q = $this->db->get($this->tblHolidays . " holidays")->result();
            $arrData = array();
            foreach($q as $key => $rs){
                // added for tagging company and department
                if($rs->meta != null && $rs->meta){
                    $meta = unserialize($rs->meta);
                    $rs->company = ($meta['company'] != 'all') ? $this->getCompanyCode($meta['company']) : 'all';
                    $rs->department = ($meta['department'] != 'all') ? $this->getDeptCode($meta['department']) : 'all';
                }else{
                    $rs->company = 'all';
                    $rs->department = 'all';
                }
                // added for tagging company and department
                $arrData[$key] = $rs;
            }
            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }
            $resultSet['data'] = $data;

            // $resultSet['data'] = $this->db->get($this->tblHolidays . " holidays")->result(); -> original source code
            $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
            $resultSet["recordsTotal"] = $this->utilities->getTableCount($this->tblHolidays . " holidays", null, $search, null);
            $resultSet["recordsFiltered"] = $this->utilities->getTableCount($this->tblHolidays . " holidays", null, $search, null);

            return $resultSet;
        }

        function saveHoliday() {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error()
            );

            $post = $this->utilities->parseFormDataToObject($this->input->post());

            $_date_from = new DateTime($post->date_from);
            $_date_to = new DateTime($post->date_to);

            $post->year = $_date_from->format('Y');
            $post->month = $_date_from->format('m');
            $post->day = $_date_from->format('d');

            $post->start_date = $_date_from->format('Y-m-d');
            $post->start_time = $_date_from->format('H:i:s');

            $post->end_date = $_date_to->format('Y-m-d');
            $post->end_time = $_date_to->format('H:i:s');

            $post->add_date = $this->now;
            $post->add_by = $this->user['display_name'];
            unset($post->date_from);
            unset($post->date_to);

            // added for tagging company and department
            $_company = (isset($post->company)) ? $post->company : "all";
            $_department = (isset($post->department)) ? $post->department : "all";

            $temp_meta = array(
                'company' => $_company,
                'department' => $_department
            );
            $post->meta = serialize($temp_meta);
            // added for tagging company and department

            $query = $this->db->insert($this->tblHolidays, $post);
            $coh_id = $this->db->insert_id();
            if ($query) {
                $postShift = array();

                $postShift["title"] = (isset($post->description) && $post->description) ? $post->description : "No title";
                $postShift["description"] = (isset($post->description) && $post->description) ? $post->description : "No description";
                $postShift["start_date"] = (isset($post->start_date) && $post->start_date) ? $post->start_date : "0000-00-00";
                $postShift["end_date"] = (isset($post->end_date) && $post->end_date) ? $post->end_date : "0000-00-00";
                $postShift["start_time"] = (isset($post->start_time) && $post->start_time) ? $post->start_time : "00:00:00";
                $postShift["end_time"] = (isset($post->end_time) && $post->end_time) ? $post->end_time : "00:00:00";
                $postShift["class_name"] = "m-fc-event--light m-fc-event--solid-danger";
                $postShift["coh_id"] = $coh_id;

                $insert = $this->db->insert($this->shiftScheduleCalendarTable, $postShift);
                if ($insert) {
                    $resultSet["success"] = true;
                    $resultSet["message"] = "Record was successfully saved.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " inserted new holiday record `".$postShift["title"]."` with db id no. ".$this->db->insert_id(),"insert", "success", "gcchris", "user");
                } else {
                    $resultset["success"] = false;
                    $resultset["message"] = "Failed to add event schedule!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed inserting `".$postShift["title"]."` new holiday record","insert", "error", "gcchris", "user");
                }
            } else {
                $resultset["success"] = false;
                $resultset["message"] = "Error, no data found!";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed inserting existing salary","insert", "error", "gcchris", "user");
            }

            return $resultSet;
        }

        function getHolidayItem($form) {
            $id = $form['id'];
            $fromTabular = $form['fromTabular'];
            $select = "holidays.*, DATE_FORMAT(CAST(CONCAT(holidays.year, '-', holidays.month, '-', holidays.day) as date), '%M %d, %Y') date, $fromTabular fromTabular";
            $this->db->select($select, FALSE);
            $this->db->where("id", $id);
            return $this->db->get($this->tblHolidays . " holidays")->row();
        }

        function updateHoliday() {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error()
            );

            $post = $this->utilities->parseFormDataToObject($this->input->post());

            $post->date_to = ($post->drag && $post->drag != "false") ? date("Y-m-d", strtotime("-1 day", strtotime($post->date_to))) : $post->date_to;

            if ($post->classification == "Regular Holiday") {
                $post->classification = "Regular";
            } else if ($post->classification == "Special Holiday") {
                $post->classification = "Special";
            }

            $id = $post->id;
            $_date_from = new DateTime($post->date_from);
            $_date_to = new DateTime($post->date_to);

            $post->year = $_date_from->format('Y');
            $post->month = $_date_from->format('m');
            $post->day = $_date_from->format('d');

            $post->start_date = $_date_from->format('Y-m-d');
            $post->start_time = $_date_from->format('H:i:s');
            $post->end_date = $_date_to->format('Y-m-d');
            $post->end_time = $_date_to->format('H:i:s');

            $post->update_date = $this->now;
            $post->update_by = $this->user['display_name'];
            unset($post->date_from);
            unset($post->date_to);
            unset($post->drag);

            // added for tagging company and department
            $_company = (isset($post->company)) ? $post->company : "all";
            $_department = (isset($post->department)) ? $post->department : "all";

            $temp_meta = array(
                'company' => $_company,
                'department' => $_department
            );
            $post->meta = serialize($temp_meta);
            // added for tagging company and department

            $this->db->where("id", $id);
            $query = $this->db->update($this->tblHolidays, $post);
            if ($query) {
                $scheduleCalendar = $this->db->get_where($this->shiftScheduleCalendarTable, array("coh_id" => $id));
                if ($scheduleCalendar) {
                    $postShift = array();

                    $postShift["title"] = (isset($post->description) && $post->description) ? $post->description : "No title";
                    $postShift["description"] = (isset($post->description) && $post->description) ? $post->description : "No description";
                    $postShift["start_date"] = (isset($post->start_date) && $post->start_date) ? $post->start_date : "0000-00-00";
                    $postShift["end_date"] = (isset($post->end_date) && $post->end_date) ? $post->end_date : "0000-00-00";
                    $postShift["start_time"] = (isset($post->start_time) && $post->start_time) ? $post->start_time : "00:00:00";
                    $postShift["end_time"] = (isset($post->end_time) && $post->end_time) ? $post->end_time : "00:00:00";
                    $postShift["class_name"] = "m-fc-event--light m-fc-event--solid-danger";

                    $this->db->where("coh_id", $id);
                    $update = $this->db->update($this->shiftScheduleCalendarTable, $postShift);
                    if ($update) {
                        $resultSet["success"] = true;
                        $resultSet["message"] = "Record was successfully updated.";
                        $this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated holiday record `".$postShift["title"]."` with db id no. ".$id,"update", "success", "gcchris", "user");
                    } else {
                        $resultset["success"] = false;
                        $resultset["message"] = "Failed to update event schedule!";
                        $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed updating holiday record `".$postShift["title"]."` with db id no. ".$id,"update", "error", "gcchris", "user");
                    }
                }
            }

            return $resultSet;
        }

        function deleteHoliday($holiday_id) {
            $resultSet = array(
                "success" => false,
                "message" => $this->db->error()
            );

            $this->db->where("id", $holiday_id);
            if ($this->db->delete($this->tblHolidays)) {
                $resultSet["success"] = true;
                $resultSet["message"] = "Record was successfully deleted.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has deleted holiday record with db id no. ".$holiday_id,"delete", "success", "gcchris", "user");
            }

            return $resultSet;
        }

        function getYearsOfExistingHolidays() {
            $this->db->select('year');
            $this->db->group_by('year');
            $this->db->order_by('year', 'desc');
            return $this->db->get($this->tblHolidays)->result();
        }

        function getHolidayClassification() {
            $this->db->select("particulars id, particulars text");
            $this->db->where("is_holiday", 1);
            $query = $this->db->get("payroll.payrate_settings");
            return array(
                "results" => $query->result()
            );
        }

        function getCompanyCode($ids = array()){
            $data = array();
            foreach($ids as $row){
                $sql = "id, CASE WHEN code = description THEN description ELSE CONCAT(code, ' | ', description) END as text";
                $this->db->select($sql);
                $this->db->from("gcchris.tblcompanies");
                $this->db->where('id', $row);
                $query = $this->db->get();
                $result = $query->row();
                
                if($result){
                    $data[] = $result;
                }
            }

            return $data;
        }

        function getDeptCode($ids = array()){
            $data = array();
            foreach($ids as $row){
                $sql = "id, CASE WHEN code = description THEN description ELSE CONCAT(code, ' | ', description) END as text";
                $this->db->select($sql);
                $this->db->from("gcchris.tbldepartments");
                $this->db->where('id', $row);
                $query = $this->db->get();
                $result = $query->row();
                
                if($result){
                    $data[] = $result;
                }
            }

            return $data;
        }

        function getHolidaysTabular(){
            $post = $this->input->post();

            $order_val = array(array("column"=>"1", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
            $year = (isset($post["search"]['filter_year']) && $post["search"]['filter_year'])? $post["search"]['filter_year']: false;
            $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
            $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;

            $rowData = $this->tableRequestData($search, $year, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->tableRequestCount($search, $year);

            $response = array(
                "data" => $rowData,
                "recordsTotal" => $rowCount,
                "recordsFiltered" => $rowCount
            );
            return $response;
        }

        function tableRequestData($search = null, $year, $limit, $offset, $sortBy, $sortOrder){
            $arrData = array();
            $result = array();

            $select = "holidays.*, DATE_FORMAT(CAST(CONCAT(holidays.year, '-', holidays.month, '-', holidays.day) as date), '%M %d, %Y') date, DATE_FORMAT(start_date, '%M %d, %Y') date_from, DATE_FORMAT(end_date, '%M %d, %Y') date_to";
            $searchFields = "CONCAT(DATE_FORMAT(CAST(CONCAT(holidays.year, '-', holidays.month, '-', holidays.day) as date), '%M %d, %Y'), DATE_FORMAT(start_date, '%M %d, %Y'), DATE_FORMAT(end_date, '%M %d, %Y'), ";
            $searchFields .= "holidays.description, holidays.description)";

            $this->db->where("year", $year);
            $this->db->select($select, FALSE);
            $this->db->like($searchFields, $search, "both");

            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

            $q = $this->db->get($this->tblHolidays . " holidays")->result();

            foreach($q as $key => $rs){
                if($rs->meta != null && $rs->meta){
                    $meta = unserialize($rs->meta);
                    $rs->company = ($meta['company'] != 'all') ? $this->getCompanyCode($meta['company']) : 'all';
                    $rs->department = ($meta['department'] != 'all') ? $this->getDeptCode($meta['department']) : 'all';
                }else{
                    $rs->company = 'all';
                    $rs->department = 'all';
                }
                $arrData[$key] = $rs;
            }
            $data = array();
            foreach($arrData as $k=>$v){
                $result[] = $v;
            }

            return $result;
        }

        function tableRequestCount($search = null, $year){
            $arrData = array();
            $result = array();

            $select = "holidays.*, DATE_FORMAT(CAST(CONCAT(holidays.year, '-', holidays.month, '-', holidays.day) as date), '%M %d, %Y') date, DATE_FORMAT(start_date, '%M %d, %Y') date_from, DATE_FORMAT(end_date, '%M %d, %Y') date_to";

            $searchFields = "CONCAT(DATE_FORMAT(CAST(CONCAT(holidays.year, '-', holidays.month, '-', holidays.day) as date), '%M %d, %Y'), DATE_FORMAT(start_date, '%M %d, %Y'), DATE_FORMAT(end_date, '%M %d, %Y'), ";
            $searchFields .= "holidays.description, holidays.description)";

            $this->db->where("year", $year);
            $this->db->select($select, FALSE);

            
            $this->db->like($searchFields, $search, "both");

            $q = $this->db->get($this->tblHolidays . " holidays");
            return $q->num_rows();
        }

    }

