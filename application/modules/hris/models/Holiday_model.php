<?php
    defined('BASEPATH') || exit('No direct script access allowed');

    class Holiday_model extends CI_Model {
        protected $tblHolidays = "gcchris.tblholidays";
        protected $shiftScheduleCalendarTable = "gcctimeutility.shift_schedule_calendar";
        protected $eventsCalendarTable = "gcchris.events_calendar";
        protected $eventsSpeakersTable = "gcchris.events_speakers";
        protected $eventsParticipantsTable = "gcchris.events_participants";
        protected $employeesTable = "gccmaster.tblemployees";
        protected $usersTable = "gccmaster.tblusers";
        protected $positionsTable = "gcchris.tblposition";
        protected $departmentTable = "gcchris.tbldepartments";
        protected $companyTable = "gcchris.tblcompanies";
        protected $now = null;
        protected $user = null;

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

        public function saveEvent(){
            $response = ["success" => false, "message" => "An error occurred while saving the event."];
            $post = $this->input->post();
            $date_range = isset($post['date']) ? explode(" - ", $post['date']) : [];
            $start_date = isset($date_range[0]) ? date("Y-m-d", strtotime($date_range[0])) : null;
            $end_date   = isset($date_range[1]) ? date("Y-m-d", strtotime($date_range[1])) : null;
        
            $event_data = [
                "event_title"   => $post['event_title'] ?? null,
                "description"   => $post['event_description'] ?? null,
                "event_venue"   => $post['event_venue'] ?? null,
                "event_from"    => $start_date,
                "event_to"      => $end_date,
            ];
        
            $this->db->trans_start();
        
            $this->db->insert($this->eventsCalendarTable, $event_data);
            $event_id = $this->db->insert_id();
        
            if (!empty($post['speakers']) && is_array($post['speakers'])) {
                foreach ($post['speakers'] as $row) {
                    $speaker_data = [
                        "event_id" => $event_id,
                        "speaker_name"     => $row['name'] ?? null,
                        "position" => $row['position'] ?? null,
                        "company"  => !empty($row['company']) ? $row['company'] : null,
                    ];
                    $this->db->insert($this->eventsSpeakersTable, $speaker_data);
                }
            }
        
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $response = [
                    "success" => false,
                    "message" => "Company event was not successfully saved."
                ];
                $this->core_layout->setEventLog("Failed to insert company event.","insert", "error", "gcchris", "system");
            } else {
                $response = [
                    "success" => true,
                    "message" => "Company event was successfully saved."
                ];
                $this->core_layout->setEventLog("Added new company event","insert", "success", "gcchris", "user");
            }
            return $response;
        }

        public function getEventsTabular(){
            $post = $this->input->post();
            $order_val = array(array("column"=>"0", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $year = (isset($post["search"]['filter_year']) && $post["search"]['filter_year'])? $post["search"]['filter_year']: false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $rowData = array();
            $rowData = $this->getEventsData($limit, $offset, $sortBy, $sortOrder, $search,$year);
            $rowCount = $this->getEventsDataCount($search,$year);
          
            $data["recordsTotal"] = $rowCount;
            $data["recordsFiltered"] = $rowCount;
            $data["data"] = $rowData;
            return $data;
        }

        private function getEventsData($limit, $offset, $sortBy, $sortOrder, $search , $year){
            $filterFields = array("a.event_title", "a.description", "a.event_venue", "a.event_from", "a.event_to","b.speaker_name","b.position","b.company");
            $this->db->select("a.id, a.event_title, a.description, a.event_venue, a.event_from, a.event_to,
                GROUP_CONCAT(b.speaker_name SEPARATOR '||') as speaker_names,
                GROUP_CONCAT(b.id SEPARATOR '||') as speaker_id,
                GROUP_CONCAT(b.position SEPARATOR '||') as speaker_positions,
                GROUP_CONCAT(b.company SEPARATOR '||') as speaker_companies,
                CONCAT(
                    DATE_FORMAT(a.event_from, '%b %d, %Y'),
                    ' - ',
                    DATE_FORMAT(a.event_to, '%b %d, %Y')
                ) as date
                ");
            $this->db->from($this->eventsCalendarTable . " a");
            $this->db->join($this->eventsSpeakersTable . " b", "a.id = b.event_id", "left");
            $this->db->where("YEAR(a.event_to)", $year);
            $this->db->where("a.is_archive", 0);
            if ($search) {
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
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $this->db->group_by("a.id");
            $query = $this->db->get();
            $result = $query->result_array();
            foreach ($result as &$row) {
                $id = explode("||", $row['speaker_id']);
                $names = explode("||", $row['speaker_names']);
                $positions = explode("||", $row['speaker_positions']);
                $companies = explode("||", $row['speaker_companies']);
            
                $speakers = [];
                foreach ($names as $i => $name) {
                    if ($name) {
                        $speakers[] = [
                            "speaker_name" => $name,
                            "id"           => $id[$i] ?? null,
                            "position"     => $positions[$i] ?? null,
                            "company"      => $companies[$i] ?? null,
                        ];
                    }
                }
                $row['speakers'] = $speakers;
                unset($row['speaker_id'],$row['speaker_names'], $row['speaker_positions'], $row['speaker_companies']);
            }
            return $result;

        }

        private function getEventsDataCount($search,$year){
            $filterFields = array("a.event_title", "a.description", "a.event_venue", "a.event_from", "a.event_to","b.speaker_name","b.position","b.company");
            $this->db->select("a.id, a.event_title, a.description, a.event_venue, a.event_from, a.event_to,
            GROUP_CONCAT(b.speaker_name SEPARATOR '||') as speaker_names,
            GROUP_CONCAT(b.id SEPARATOR '||') as speaker_id,
            GROUP_CONCAT(b.position SEPARATOR '||') as speaker_positions,
            GROUP_CONCAT(b.company SEPARATOR '||') as speaker_companies,
            CONCAT(
                DATE_FORMAT(a.event_from, '%b %d, %Y'),
                ' - ',
                DATE_FORMAT(a.event_to, '%b %d, %Y')
            ) as date
            ");
            $this->db->from($this->eventsCalendarTable . " a");
            $this->db->join($this->eventsSpeakersTable . " b", "a.id = b.event_id", "left");
            $this->db->where("YEAR(a.event_to)", $year);
            $this->db->where("a.is_archive", 0);
            if ($search) {
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
            $this->db->group_by("a.id");
            $query = $this->db->get();
            return $query->num_rows();
        }

        public function updateEvent(){
            $post = $this->input->post();
            $eventId = $post['id'];
            $date_range = explode(" - ", $post['date']);
            $event_from = isset($date_range[0]) ? date("Y-m-d", strtotime($date_range[0])) : null;
            $event_to   = isset($date_range[1]) ? date("Y-m-d", strtotime($date_range[1])) : null;
        
            $eventData = [
                "event_title" => $post['event_title'],
                "description" => $post['event_description'],
                "event_venue" => $post['event_venue'],
                "event_from"  => $event_from,
                "event_to"    => $event_to,
            ];
            $this->db->trans_start();
            $this->db->where("id", $eventId)->update($this->eventsCalendarTable, $eventData);
            $this->db->where("event_id", $eventId)->delete($this->eventsSpeakersTable);
        
            if (!empty($post['speakers'])) {
                $speakerBatch = [];
                foreach ($post['speakers'] as $spk) {
                    $speakerBatch[] = [
                        "event_id"     => $eventId,
                        "speaker_name" => $spk['name'],
                        "position"     => $spk['position'],
                        "company"      => $spk['company']
                    ];
                }
                $this->db->insert_batch($this->eventsSpeakersTable, $speakerBatch);
            }
        
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->core_layout->setEventLog("Failed to update company event.","update", "error", "gcchris", "system");
                return [
                    "status"  => "error",
                    "message" => "Failed to update event. Please try again."
                ];
            } else {
                $this->core_layout->setEventLog("Updated company event","update", "success", "gcchris", "user");
                return [
                    "status"  => "success",
                    "message" => "Event updated successfully."
                ];
            }
        }

        public function archiveEvent(){
            $post = $this->input->post();
            $id = $post['id'];
            $this->db->trans_start();
            $this->db->where("id", $id);
            $this->db->update($this->eventsCalendarTable, ["is_archive" => 1]);

            $this->db->where("event_id", $id);
            $this->db->update($this->eventsSpeakersTable, ["is_archive" => 1]);
            $this->db->trans_complete();
        
            if ($this->db->trans_status() === FALSE) {
                $this->core_layout->setEventLog("Failed to archive company event ID: {$id}.","archive","error","gcchris","system");
                return [
                    "status"  => "error",
                    "message" => "Failed to archive event. Please try again."
                ];
            } else {
                $this->core_layout->setEventLog("Archived company event ID: {$id}.","archive","success","gcchris","user");
                return [
                    "status"  => "success",
                    "message" => "Event archived successfully."
                ];
            }
        }

        public function getEvents(){
            $this->db->select("
                e.id, 
                e.event_title title, 
                e.description, 
                e.event_venue venue, 
                e.event_from start, 
                e.event_to end,
                GROUP_CONCAT(
                    JSON_OBJECT(
                        'id', s.id,
                        'speaker_name', s.speaker_name,
                        'position', s.position,
                        'company', s.company
                    )
                ) as speakers_json
            ");
            $this->db->from($this->eventsCalendarTable . ' e');
            $this->db->join($this->eventsSpeakersTable . ' s', 'e.id = s.event_id', 'left');
            $this->db->where("e.is_archive", 0);
            $this->db->group_by('e.id');
            $events = $this->db->get()->result();
        
            // Process speakers JSON
            foreach($events as &$event) {
                $event->speakers = $event->speakers_json ? 
                    json_decode('[' . $event->speakers_json . ']') : [];
                unset($event->speakers_json);
            }
            
            return $events;
        }

        public function getEventDetails($id) {
            $this->db->select("
                a.id, a.event_title, a.description, a.event_venue, a.event_from, a.event_to,
                GROUP_CONCAT(b.id SEPARATOR '||') as speaker_ids,
                GROUP_CONCAT(b.speaker_name SEPARATOR '||') as speaker_names,
                GROUP_CONCAT(b.position SEPARATOR '||') as speaker_positions,
                GROUP_CONCAT(b.company SEPARATOR '||') as speaker_companies
            ")->from($this->eventsCalendarTable . " as a")
              ->join($this->eventsSpeakersTable . " as b", "a.id = b.event_id", "left")
              ->where("a.id", $id)
              ->group_by("a.id");
        
            $row = $this->db->get()->row();
        
            if ($row) {
                $ids       = explode("||", $row->speaker_ids ?? '');
                $names     = explode("||", $row->speaker_names ?? '');
                $positions = explode("||", $row->speaker_positions ?? '');
                $companies = explode("||", $row->speaker_companies ?? '');
        
                $speakers = [];
                foreach ($names as $i => $name) {
                    if ($name) {
                        $speakers[] = [
                            "id"           => $ids[$i] ?? null,
                            "speaker_name" => $name,
                            "position"     => $positions[$i] ?? null,
                            "company"      => $companies[$i] ?? null,
                        ];
                    }
                }
                $row->speakers = $speakers;
                unset($row->speaker_ids, $row->speaker_names, $row->speaker_positions, $row->speaker_companies);
            }
            return $row;
        }
        
        

        public function getEventParticipants($id){
            $this->db->select("a.id, a.status, a.emp_id, a.invited_by, a.invited_at, b.mobile_no, c.email, d.name as position, e.code as department, f.code as company,
                CONCAT(
                    LOWER(b.firstname),
                    IF(b.middlename IS NOT NULL AND b.middlename != '', 
                        CONCAT(' ', UPPER(LEFT(b.middlename, 1)), '.'), 
                        ''
                    ),
                    ' ',
                    LOWER(b.lastname)
                ) AS fullname
            ");
            $this->db->from($this->eventsParticipantsTable." as a");
            $this->db->join($this->employeesTable." as b", "a.emp_id = b.id", "left");
            $this->db->join($this->usersTable." as c", "a.emp_id = c.emp_id", "left");
            $this->db->join($this->positionsTable." as d", "b.position = d.id", "left");
            $this->db->join($this->departmentTable." as e", "b.department_id = e.id", "left");
            $this->db->join($this->companyTable." as f", "b.company_id = f.id", "left");
            $this->db->where("a.event_id", $id);
            return $this->db->get()->result();
        }

        public function getEmployeeSelection(){
            $this->db->select("
                id,
                CASE
                    WHEN LENGTH(middlename) > 1 
                        THEN CONCAT(firstname, ' ', LEFT(middlename, 1), '. ', lastname)
                    WHEN LENGTH(middlename) = 1 
                        THEN CONCAT(firstname, ' ', middlename, '. ', lastname)
                    ELSE CONCAT(firstname, ' ', lastname)
                END AS text
            ", false);
        
            $this->db->from($this->employeesTable);
            $this->db->where('employee_status', 'Active');
            $this->db->order_by('firstname', 'ASC');
            return $this->db->get()->result();
        }

        public function getEmployeeInformation(){
            $post = $this->input->post();
            $id = $post['emp_id'];

            $this->db->select("a.firstname, a.middlename, a.lastname, a.mobile_no, c.email, d.name as position, e.code as department, f.code as company");
            $this->db->from($this->employeesTable. " as a");
            $this->db->join($this->usersTable." as c", "a.id = c.emp_id", "left");
            $this->db->join($this->positionsTable." as d", "a.position = d.id", "left");
            $this->db->join($this->departmentTable." as e", "a.department_id = e.id", "left");
            $this->db->join($this->companyTable." as f", "a.company_id = f.id", "left");
            $this->db->where('a.id', $id);
            return $this->db->get()->row();
        }

    }