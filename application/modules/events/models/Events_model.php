<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Events_model extends MX_Controller {
    protected $eventsCalendarTable = "gcchris.events_calendar";
    protected $eventsSpeakersTable = "gcchris.events_speakers";
    protected $eventsParticipantsTable = "gcchris.events_participants";
    protected $eventsAttachmentsTable = "gcchris.events_attachments";
    protected $employeesTable = "gccmaster.tblemployees";
    protected $departmentTable = "gcchris.tbldepartments";
    protected $companyTable = "gcchris.tblcompanies";
    protected $positionsTable = "gcchris.tblposition";
    protected $usersTable = "gccmaster.tblusers";
    protected $tbltrainings = "gcchris.tbltrainings";
    protected $eventsSched = "gcchris.events_schedule";
    protected $events_attendance = "gcchris.events_attendance";
    protected $eventsSettingsTable = "gcchris.events_settings";
    protected $user_data = null;
    protected $actions = null;
    public function __construct() {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        $this->core_layout->setPrivilegeName("company_events");
        $this->actions = $this->core_layout->getCurrentActions();
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
        $is_archived = (isset($post["is_archived"]) && $post["is_archived"]) ? $post["is_archived"] : 0;
        $rowData = array();
        $rowData = $this->getEventsData($limit, $offset, $sortBy, $sortOrder, $search,$year, $is_archived);
        $rowCount = $this->getEventsDataCount($search,$year, $is_archived);
      
        $data["recordsTotal"] = $rowCount;
        $data["recordsFiltered"] = $rowCount;
        $data["data"] = $rowData;
        return $data;
    }

    private function getEventsData($limit, $offset, $sortBy, $sortOrder, $search , $year,$is_archived){
        $filterFields = array("a.event_title", "a.description", "a.event_venue", "a.event_from", "a.event_to","b.speaker_name","b.position","b.company","a.company_array","a.department_array","a.events_by");
        $this->db->select("a.budget, a.company_source, a.expense, a.id, a.event_title, a.description, a.event_venue, a.event_from, a.event_to, a.company_ids, a.department_ids, a.company_array, a.department_array, a.events_by, a.training_type, a.init_type, a.training_category, 
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

        // $this->db->where("YEAR(a.event_to)", $year);
        $this->db->where("a.is_archive", $is_archived ? 1 : 0);
        if (in_array("view_own_request", $this->actions)) {
            $this->db->join($this->eventsParticipantsTable . " c", "a.id = c.event_id", "left");
            $this->db->join($this->employeesTable." d", "c.emp_id = d.id", "left");
            $this->db->select("c.emp_id as employee_id, d.firstname, d.middlename, d.lastname, c.status as participant_status");
            $this->db->where("c.emp_id", $this->user_data['emp_id']);
        }
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

            $row['company_ids'] = (!empty($row['company_ids']) && ($tmp = @unserialize($row['company_ids'])) !== false) ? $tmp: [];
            $row['department_ids'] = (!empty($row['department_ids']) && ($tmp = @unserialize($row['department_ids'])) !== false) ? $tmp: [];
            $row['company_array']    = !empty($row['company_array']) ? explode(",", $row['company_array']) : [];
            $row['department_array'] = !empty($row['department_array']) ? explode(",", $row['department_array']) : [];
        
        }
        return $result;
    }

    private function getEventsDataCount($search,$year,$is_archived){
        $filterFields = array("a.event_title", "a.description", "a.event_venue", "a.event_from", "a.event_to","b.speaker_name","b.position","b.company","a.company_array","a.department_array");
        $this->db->select("a.id, a.event_title, a.description, a.event_venue, a.event_from, a.event_to, a.company_array, a.department_array, a.company_array, a.department_array, a.events_by, a.training_type, a.init_type, a.training_category,
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
        $this->db->where("a.is_archive", $is_archived ? 1 : 0);
        if (in_array("view_own_request", $this->actions)) {
            $this->db->join($this->eventsParticipantsTable . " c", "a.id = c.event_id", "left");
            $this->db->join($this->employeesTable." d", "c.emp_id = d.id", "left");
            $this->db->select("c.emp_id as employee_id, d.firstname, d.middlename, d.lastname");
            $this->db->where("c.emp_id", $this->user_data['emp_id']);
        }
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

    public function getEvents(){
        $this->db->select("
            e.id, 
            e.event_title title, 
            e.events_by,
            e.description, 
            e.event_venue venue, 
            e.event_from start, 
            a.hex_code,
            CONCAT(e.event_to, ' 23:59:59') as end,
            e.company_ids,
            e.department_ids,
            e.company_array,
            e.department_array,
            e.training_type,
            e.init_type,
            e.training_category,
            e.budget,
            e.company_source,
            COUNT(DISTINCT p.id) as total_participants,
            GROUP_CONCAT(
                DISTINCT JSON_OBJECT(
                    'id', s.id,
                    'speaker_name', s.speaker_name,
                    'position', s.position,
                    'company', s.company
                )
            ) as speakers_json,
        ");
        $this->db->from($this->eventsCalendarTable . ' e');
        $this->db->join($this->eventsSpeakersTable . ' s', 'e.id = s.event_id', 'left');
        $this->db->join($this->eventsSettingsTable . ' a', 'e.training_category = a.id', 'left');
        $this->db->join($this->eventsParticipantsTable . ' p', 'e.id = p.event_id AND p.status = "confirmed"', 'left');
        $this->db->where("e.is_archive", 0);
        $this->db->group_by('e.id');
        $events = $this->db->get()->result();
    
        foreach($events as &$event) {
            $event->speakers = $event->speakers_json ? 
                json_decode('[' . $event->speakers_json . ']') : [];
            unset($event->speakers_json);

            $event->company_ids = (!empty($event->company_ids) && ($tmp = @unserialize($event->company_ids)) !== false) ? $tmp: [];
            $event->department_ids = (!empty($event->department_ids) && ($tmp = @unserialize($event->department_ids)) !== false) ? $tmp: [];
        }
        
        return $events;
    }

    public function getEventDetails($id) {
        $this->db->select("
            a.id, a.event_title, a.description, a.event_venue, a.event_from, a.event_to, a.company_ids, a.department_ids, a.company_array, a.department_array, a.events_by, c.name as training_type, d.name as init_type, e.name as training_category, a.budget, a.expense, a.company_source, 
            GROUP_CONCAT(b.id SEPARATOR '||') as speaker_ids,
            GROUP_CONCAT(b.speaker_name SEPARATOR '||') as speaker_names,
            GROUP_CONCAT(b.position SEPARATOR '||') as speaker_positions,
            GROUP_CONCAT(b.company SEPARATOR '||') as speaker_companies
        ")->from($this->eventsCalendarTable . " as a")
          ->join($this->eventsSpeakersTable . " as b", "a.id = b.event_id", "left")
          ->join($this->eventsSettingsTable . " as c", "c.id = a.training_type", "left")
          ->join($this->eventsSettingsTable . " as d", "d.id = a.init_type", "left")
          ->join($this->eventsSettingsTable . " as e", "e.id = a.training_category", "left")
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
            $this->db->select("status, COUNT(*) as total")
            ->from($this->eventsParticipantsTable)
            ->where("event_id", $id)
            ->group_by("status");
            $statusCounts = $this->db->get()->result();
            $counts = [
                "pending"   => 0,
                "declined"  => 0,
                "confirmed" => 0
            ];
            foreach ($statusCounts as $sc) {
                if (isset($counts[$sc->status])) {
                    $counts[$sc->status] = (int)$sc->total;
                }
            }

            $row->participant_counts = $counts;
        }
        return $row;
    }

    public function getEventParticipants($id, $id_only = false){
        $this->db->select("a.id, a.event_id, a.emp_id, a.is_employee, a.status, a.emp_id, a.invited_by, a.invited_at, a.firstname, a.middlename, a.lastname, a.suffix, a.mobile_no, a.email, a.position, a.department, a.company, a.cert_awarded, t.attachment as cert_attachment,
            CONCAT(LOWER(a.firstname), IF(a.middlename IS NOT NULL AND a.middlename != '', CONCAT(' ', UPPER(LEFT(a.middlename, 1)), '.'), ''), ' ', LOWER(a.lastname)) AS fullname,
            CONCAT(
                LOWER(head.firstname), 
                IF(head.middlename IS NOT NULL AND head.middlename != '', 
                    CONCAT(' ', UPPER(LEFT(head.middlename, 1)), '.'), 
                    ''
                ), 
                ' ', 
                LOWER(head.lastname)
            ) AS department_head_fullname");
        $this->db->from($this->eventsParticipantsTable." as a");
        $this->db->join($this->employeesTable." as e", "a.emp_id = e.id", "left");
        $this->db->join($this->departmentTable." as d", "e.department_id = d.id", "left");
        $this->db->join($this->employeesTable." as head", "d.head_id = head.id", "left");
        $this->db->join($this->tbltrainings." as t", "a.cert_awarded = t.id", "left");
        $this->db->where("a.event_id", $id);
        // $this->db->where("a.is_archived", 0);
        return $this->db->get()->result();
    }

    public function getEmployeeSelection($id = null){
        $ids = $this->db->select("emp_id")
        ->from($this->eventsParticipantsTable)
        ->where("event_id", $id)
        ->where("is_employee", 1)
        ->get()
        ->result_array();
        $ids = array_column($ids, 'emp_id');

        $filterIds = $this->db->select("company_ids,department_ids")
        ->from($this->eventsCalendarTable)
        ->where("id", $id)
        ->get()
        ->row();
        $compIds = @unserialize($filterIds->company_ids);
        $deptIds = @unserialize($filterIds->department_ids);

        $this->db->select("
            employee.id,
            CASE
                WHEN LENGTH(employee.middlename) > 1 
                    THEN CONCAT(employee.firstname, ' ', LEFT(employee.middlename, 1), '. ', employee.lastname)
                WHEN LENGTH(employee.middlename) = 1 
                    THEN CONCAT(employee.firstname, ' ', employee.middlename, '. ', employee.lastname)
                ELSE CONCAT(employee.firstname, ' ', employee.lastname)
            END AS text,
            employee.firstname, 
            employee.middlename, 
            employee.lastname, 
            employee.suffix,
            employee.department_id,
            employee.mobile_no,
            user.email,
            department.description as department,
            company.description as company,
            position.name as position
        ", false);
        
        $this->db->from($this->employeesTable . " as employee");
        $this->db->join($this->companyTable . " as company", "company.id = employee.company_id", "left");
        $this->db->join($this->departmentTable . " as department", "department.id = employee.department_id", "left");
        $this->db->join($this->usersTable . " as user", "user.emp_id = employee.id", "left");
        $this->db->join($this->positionsTable." as position", "employee.position = position.id", "left");

        $this->db->where('employee.employee_status', 'Active');
        if (!empty($ids)) {
            $this->db->where_not_in('employee.id', $ids);
        }
        if (!empty($compIds)) {
            $this->db->where_in('employee.company_id', $compIds);
        }
        if (!empty($deptIds)) {
            $this->db->where_in('employee.department_id', $deptIds);
        }

        $this->db->order_by('employee.firstname', 'ASC');
        $this->db->group_by('employee.id');
        $result = $this->db->get()->result();
        return $result;
    }

    public function getEmployeeInformation(){
        $post = $this->input->post();
        $id = $post['emp_id'];

        $this->db->select("a.firstname, a.middlename, a.lastname, a.suffix, a.mobile_no, c.email, d.name as position, e.code as department, f.code as company");
        $this->db->from($this->employeesTable. " as a");
        $this->db->join($this->usersTable." as c", "a.id = c.emp_id", "left");
        $this->db->join($this->positionsTable." as d", "a.position = d.id", "left");
        $this->db->join($this->departmentTable." as e", "a.department_id = e.id", "left");
        $this->db->join($this->companyTable." as f", "a.company_id = f.id", "left");
        $this->db->where('a.id', $id);
        return $this->db->get()->row();
    }

    public function select2DepartmentData(){
        $this->db->select('id, description AS text');
        $this->db->from('gcchris.tbldepartments');
        $this->db->where('description !=', '');
        $this->db->where('is_archived', 0);
        $this->db->order_by('description', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function select2CompanyData(){
        $this->db->select("companies.id, companies.`code` `text`");
        $this->db->where('is_archived', 0);
        $this->db->order_by("`code`", "ASC");
        $results = $this->db->get("gcchris.tblcompanies companies")->result();
        return $results;
    }

    public function saveEvent(){
        $post = $this->input->post();
        $date_range = isset($post['date']) ? explode(" - ", $post['date']) : [];
        $start_date = isset($date_range[0]) ? date("Y-m-d", strtotime($date_range[0])) : null;
        $end_date   = isset($date_range[1]) ? date("Y-m-d", strtotime($date_range[1])) : null;
        $companyIds      = isset($post['company_id']) && is_array($post['company_id']) ? $post['company_id'] : [];
        $departmentIds   = isset($post['department_id']) && is_array($post['department_id']) ? $post['department_id'] : [];
        $companyArray = isset($post['company_array']) ? json_decode($post['company_array'], true) : [];
        $departmentArray = isset($post['department_array']) ? json_decode($post['department_array'], true) : [];
        $event_data = [
            "event_title"       => $post['event_title'] ?? null,
            "description"       => $post['event_description'] ?? null,
            "event_venue"       => $post['event_venue'] ?? null,
            "events_by"         => $post['events_by'] ?? null,
            "training_type"     => $post['training_type'] ?? null,
            "training_category" => $post['training_category'] ?? null,
            "budget"            => $post['budget'] ?? null,
            "company_source"    => $post['company_source'] ?? null,
            // "init_type"         => $post['init_type'] ?? null,
            "event_from"        => $start_date,
            "event_to"          => $end_date,
            "company_ids"       => serialize($companyIds),
            "department_ids"    => serialize($departmentIds), 
            "company_array"     => !empty($companyArray) ? implode(", ", $companyArray) : null,
            "department_array"  => !empty($departmentArray) ? implode(", ", $departmentArray) : null,
            "created_by"        => $this->user_data['emp_id'],
        ];
    
        $this->db->trans_start();
    
        $save = $this->db->insert($this->eventsCalendarTable, $event_data);
        $event_id = $this->db->insert_id();
    
        if (!empty($post['speakers']) && is_array($post['speakers'])) {
            foreach ($post['speakers'] as $row) {
                $speaker_data = [
                    "event_id" => $event_id,
                    "speaker_name"     => $row['name'] ?? "",
                    "position" => $row['position'] ?? "",
                    "company"  => !empty($row['company']) ? $row['company'] : "",
                ];
                $this->db->insert($this->eventsSpeakersTable, $speaker_data);
            }
        }

        if($save && $start_date == $end_date){
            $sched = [
                'title'      => $post['event_title'],
                'event_id'   => $event_id,
                'description'=> $post['event_description'],
                'location'   => $post['event_venue'],
                'event_date' => date("Y-m-d", strtotime($start_date)),
                'start'      => '08:00:00',
                'end'        => '17:00:00',
            ];
            $this->db->insert($this->eventsSched, $sched);
        }

        // if(!empty($post['company_id']) && is_array($post['company_id'])){
        //     foreach ($post['company_id'] as $company_id) {
        //         $company_data = [
        //             "event_id" => $event_id,
        //             "filter_type" => "company",
        //             "filter_id" => $company_id,
        //         ];
        //         $this->db->insert($this->eventFiltersTable, $company_data);
        //     }
        // }

        // if(!empty($post['department_id']) && is_array($post['department_id'])){
        //     foreach ($post['department_id'] as $department_id) {
        //         $department_data = [
        //             "event_id" => $event_id,
        //             "filter_type" => "department",
        //             "filter_id" =>  $department_id,
        //         ];
        //         $this->db->insert($this->eventFiltersTable, $department_data);
        //     }
        // }
    
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
                "message" => "Company event was successfully saved.",
                "events" => $this->getEvents(),
            ];
            $this->core_layout->setEventLog("Added new company event","insert", "success", "gcchris", "user");
        }
        return $response;
    }

    public function updateEvent(){
        $post = $this->input->post();
        $eventId = $post['id'];
        $date_range = explode(" - ", $post['date']);
        $events_by = isset($post['events_by']) ? $post['events_by'] : null;
        $event_from = isset($date_range[0]) ? date("Y-m-d", strtotime($date_range[0])) : null;
        $event_to   = isset($date_range[1]) ? date("Y-m-d", strtotime($date_range[1])) : null;
        $companyIds      = isset($post['company_id']) && is_array($post['company_id']) ? $post['company_id'] : [];
        $departmentIds   = isset($post['department_id']) && is_array($post['department_id']) ? $post['department_id'] : [];
        $companyArray = isset($post['company_array']) ? json_decode($post['company_array'], true) : [];
        $departmentArray = isset($post['department_array']) ? json_decode($post['department_array'], true) : [];

        $eventData = [
            "event_title" => $post['event_title'],
            "description" => $post['event_description'],
            "event_venue" => $post['event_venue'],
            "training_type" => $post['training_type'],
            "training_category" => $post['training_category'],
            // "init_type" => $post['init_type'],
            "event_from"  => $event_from,
            "event_to"    => $event_to,
            "events_by"  => $events_by,
            "budget"     => $post['budget'],
            "company_source" => $post['company_source'],
            "company_ids" => serialize($companyIds),
            "department_ids" => serialize($departmentIds),
            "company_array" => !empty($companyArray) ? implode(", ", $companyArray) : null,
            "department_array" => !empty($departmentArray) ? implode(", ", $departmentArray) : null
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
                "events" => $this->getEvents(),
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

    public function restoreEvent(){
        $post = $this->input->post();
        $id = $post['id'];
        $this->db->trans_start();
        $this->db->where("id", $id);
        $this->db->update($this->eventsCalendarTable, ["is_archive" => 0]);
        $this->db->where("event_id", $id);
        $this->db->update($this->eventsSpeakersTable, ["is_archive" => 0]);
        $this->db->trans_complete();
    
        if ($this->db->trans_status() === FALSE) {
            $this->core_layout->setEventLog("Failed to restore company event ID: {$id}.","restore","error","gcchris","system");
            return [
                "status"  => "error",
                "message" => "Failed to restore event. Please try again."
            ];
        } else {
            $this->core_layout->setEventLog("Restore company event ID: {$id}.","restore","success","gcchris","user");
            return [
                "status"  => "success",
                "message" => "Event restored successfully."
            ];
        }
    }

    public function saveParticipant(){
        $resultArray = array();
        $post = $this->input->post();
        if(isset($post['is_recent']) && $post['is_recent'] == 1){
            $post['status'] = "confirmed";
            unset($post['is_recent']);
        }
        else{
            $post['status'] = "pending";
        }
        $post['invited_by'] = $this->user_data['emp_id'];
        unset($post['csrf_token']);
        $insert = $this->db->insert($this->eventsParticipantsTable, $post);
        if($insert){
            $resultArray['participants'] = $this->getEventParticipants($post['event_id']);
            $resultArray['success'] = true;
            $this->core_layout->setEventLog("Successfully added trainee with id: {$post['emp_id']} to event with id: {$post['event_id']}","insert","success","gcchris","user");
            $resultArray['message'] = "Successfully added trainee.";
        }else{
            $resultArray['success'] = false;
            $this->core_layout->setEventLog("Failure to add trainee with id: {$post['emp_id']} to event with id: {$post['event_id']}","insert","error","gcchris","system");
            $resultArray['message'] = "Failed to add trainee.";
        }
        return $resultArray;
    }

    public function updateParticipant(){
        $post  = $this->input->post();
        $id    = $post['id'];
        $event_title = $post['event_title'];
        unset($post['csrf_token'], $post['fullname'], $post['id'], $post['event_title'], $post['cert_attachment'], $post['department_head_fullname']);
        $update = $this->db->where('id', $id)->update($this->eventsParticipantsTable, $post);
        if($update){
            $resultArray['participants'] = $this->getEventParticipants($post['event_id']);
            $resultArray['success'] = true;
            $resultArray['message'] = "Successfully updated participant.";
            $this->core_layout->setEventLog("Participant added to event $event_title.","update", "success", "gcchris", "user");
        }else{
            $resultArray['success'] = false;
            $resultArray['message'] = "Failed to update participant.";
            $this->core_layout->setEventLog("Failed to add participant to event $event_title.","update", "error", "gcchris", "system");
        }
        return $resultArray;
    }

    public function archiveParticipant(){
        $post  = $this->input->post();
        $id    = $post['id'];
        $name  = $post['emp_name'];
        $event_name = $post['event_title'];
        $delete = $this->db->where('id', $id)->delete($this->eventsParticipantsTable);
        if($delete){
            if (!empty($post['is_employee']) && $post['is_employee'] == 1) {
                $employee = $this->db->select("
                        id,
                        CASE
                            WHEN LENGTH(middlename) > 1 
                                THEN CONCAT(firstname, ' ', LEFT(middlename, 1), '. ', lastname)
                            WHEN LENGTH(middlename) = 1 
                                THEN CONCAT(firstname, ' ', middlename, '. ', lastname)
                            ELSE CONCAT(firstname, ' ', lastname)
                        END AS text
                    ", false)
                    ->from($this->employeesTable)
                    ->where('employee_status', 'Active')
                    ->where('id', $post['emp_id'])
                    ->get()
                    ->row();
    
                $resultArray['employee'] = $employee;
            }
            $resultArray['participants'] = $this->getEventParticipants($post['event_id']);
            $resultArray['success'] = true;
            $resultArray['message'] = "Successfully deleted participant.";
            $this->core_layout->setEventLog("Participant $name deleted from event: { $event_name }.","delete", "success", "gcchris", "user");
        }else{
            $resultArray['success'] = false;
            $this->core_layout->setEventLog("Participant $name failed to be deleted from event: { $event_name }.","delete", "error", "gcchris", "system");
        }
    
        return $resultArray;
    }


    public function confirmParticipant(){
        $post = $this->input->post();
        $id = $post['id'];
        $name = $post['fullname'];
        $event_name = $post['event_title'];
        $this->db->trans_start();
        $this->db->where('id', $id)->update($this->eventsParticipantsTable, ['status' => "confirmed"]);
        if (isset($post['schedule']) && !empty($post['schedule']) && count($post['schedule']) === 1) {
            $schedule = $post['schedule'][0][0] ?? null;
            if ($schedule && isset($schedule['id'])) {
                $schedule_id = $schedule['id'];
                $data = [
                    'participant_id' => $id,
                    'schedule_id'    => $schedule_id,
                    'created_by'     => $this->user_data['emp_id'],
                ];
    
                $this->db->insert($this->events_attendance, $data);
                $resultArray['schedule'] = $this->getEventSchedule($post['event_id']);
            }
        }
    
        $this->db->trans_complete();
    
        if ($this->db->trans_status() === FALSE) {
            $resultArray['success'] = false;
            $resultArray['message'] = "Failed to confirm participant.";
            $this->core_layout->setEventLog(
                "Failed to confirm participant $name for event: $event_name.",
                "update", "error", "gcchris", "system"
            );
        } else {
            $resultArray['participants'] = $this->getEventParticipants($post['event_id']);
            $resultArray['success'] = true;
            $resultArray['message'] = "Participant has been successfully confirmed.";
            $this->core_layout->setEventLog(
                "Participant $name has been confirmed for event: $event_name.",
                "update", "success", "gcchris", "user"
            );
        }
    
        return $resultArray;
    }
    


    public function declineParticipant(){
        $post = $this->input->post();
        $id = $post['id'];
        $event_name = $post['event_title'];
        $name  = $post['fullname'];
        $update = $this->db->where('id', $id)->update($this->eventsParticipantsTable, ['status' => "declined"]);
        if($update){
            $resultArray['participants'] = $this->getEventParticipants($post['event_id']);
            $resultArray['success'] = true;
            $resultArray['message'] = "Participant has been successfully declined.";
            $this->core_layout->setEventLog("Participant $name has been declined for event: $event_name .", "update", "success", "gcchris", "user");
        }else{
            $resultArray['success'] = false;
            $resultArray['message'] = "Failed to decline participant.";
            $this->core_layout->setEventLog("Failed to decline participant $name for event: $event_name .", "update", "error", "gcchris", "system");
        }
        return $resultArray;
    }
    public function setModalTrainings() {
        $post = $this->input->post();
        $resultset = array();
        if ($post) {
            $isEmp = $post['is_employee'];
            $id = $post['is_employee'] ? $post['emp_id'] : $post['applicant_id'];
            $tempAttachment = $post["training_attachment"];
            $eventId = $post['event_id'];
            if(isset($post["sched_id"])){
                unset($post["sched_id"]);
            }
            unset($post["csrf_token"], $post["training_attachment"], $post["files"], $post['event_id'], $post['applicant_id'], $post['is_employee']);
            $loggedIn = $this->core_layout->getCurrentSession();

            $post["add_date"] = date("Y-m-d H:i:s");
            $post["add_by"] = $loggedIn["emp_id"];

            $post = array_map('strtoupper', $post);
            $post["attachment"] = $tempAttachment;
            $fullname = $this->getEmployeeName($id,$isEmp);
            $saved = $this->db->insert($this->tbltrainings, $post);
            if ($saved) {
                $resultset["response"] = true;
                $insertId = $this->db->insert_id();
                $this->db->where($isEmp ? 'emp_id' : 'id', $id);
                $this->db->where('event_id', $eventId);
                $this->db->update('gcchris.events_participants', [
                    'cert_awarded' => $insertId
                ]);
                $resultset['participants'] = $this->getEventParticipants($eventId);
                $resultset["toastr_msg"] = "Employee training and seminar has been added successfully.";
                $this->core_layout->setEventLog("User Added new training and seminar details, Training/Seminar: <strong>".$post['training']."</strong> for employee: <strong>".$fullname."</strong>","insert", "success", "gcchris", "user");
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to save employee training and seminar!";
                $this->core_layout->setEventLog("Trainings - Failed to save employee training and seminar.","insert", "error", "gcchris", "system");
            }
        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Error, No post data found!";
            $this->core_layout->setEventLog("Trainings - Error, No post data found.","insert", "error", "gcchris", "system");
        }
        return $resultset;
    }

    private function getEmployeeName($id, $is_employee){
        $this->db->select("UCASE(
            CONCAT(firstname,
                CASE WHEN middlename IS NOT NULL AND middlename != '' 
                    THEN CONCAT(' ', SUBSTR(middlename, 1, 1), '.') 
                    ELSE '' 
                END, 
                ' ', lastname,
                CASE WHEN suffix IS NOT NULL AND suffix != '' 
                    AND suffix != 'N/A' AND suffix != 'NONE' 
                    THEN CONCAT(' ', suffix) 
                    ELSE '' 
                END)
        ) AS display_name");
    
        if ($is_employee) {
            $this->db->from($this->employeesTable);
        } else {
            $this->db->from($this->eventsParticipantsTable);
        }
    
        $this->db->where("id", $id);
        $query = $this->db->get();
    
        $display_name = ($query->num_rows() === 1 && $query->row()->display_name != '') ? $query->row()->display_name : "No Assigned Name";
    
        return $display_name;
    }
    

    public function uploadDocuments(){
        $resultset = array();
        $post = $this->input->post();
        $event = $post['event_id'];
        $type = $post['attachment_type'];
        $filePath = "./uploads/files/documents/event_{$event}/$type";
        
        if (!file_exists($filePath)) {
            if (!mkdir($filePath, 0777, true)) {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                $resultset["toastr_state"] = "warning";
                return $resultset;
            }
        }
        
        if (empty($_FILES['files']['name'])) {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No files selected!";
            $resultset["toastr_state"] = "warning";
            return $resultset;
        }
        
        $uploaded = array();
        $failed = array();
        
        $config = array(
            'upload_path'   => $filePath,
            'allowed_types' => 'pdf|doc|docx|jpg|jpeg|png',
            'max_size'      => 51200, // 50MB in KB
            'remove_spaces' => false
        );
        
        foreach ($_FILES['files']['name'] as $key => $name) {
            $_FILES['file']['name']     = $_FILES['files']['name'][$key];
            $_FILES['file']['type']     = $_FILES['files']['type'][$key];
            $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$key];
            $_FILES['file']['error']    = $_FILES['files']['error'][$key];
            $_FILES['file']['size']     = $_FILES['files']['size'][$key];
            
            $config['file_name'] = $name;
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('file')) {
                $data = array(
                    'event_id'  => $event,
                    'type'      => $type,
                    'filename' => $name,
                    'created_by' => $this->user_data['emp_id'],
                );
                
                if ($this->db->insert($this->eventsAttachmentsTable, $data)) {
                    $uploaded[] = $name;
                } else {
                    $failed[] = $name . " (database error)";
                    @unlink($filePath . '/' . $name);
                }
            } else {
                $failed[] = $name . " (" . $this->upload->display_errors('', '') . ")";
            }
        }
        
        if (count($uploaded) > 0 && count($failed) == 0) {
            $resultset["success"] = true;
            $resultset["toastr_msg"] = count($uploaded) . " file(s) uploaded successfully!";
            $resultset["uploaded_files"] = $uploaded;
            $resultset["attachments"] = $this->getEventAttachments($event);
            $this->core_layout->setEventLog(implode(", ", $uploaded) . " uploaded successfully", "insert", "success", "gcchris", "user");
        } elseif (count($uploaded) > 0 && count($failed) > 0) {
            $resultset["success"] = true;
            $resultset["toastr_msg"] = count($uploaded) . " uploaded, " . count($failed) . " failed";
            $resultset["uploaded_files"] = $uploaded;
            $resultset["failed_files"] = $failed;
            $this->core_layout->setEventLog(count($uploaded) . " files uploaded, " . count($failed) . " failed", "insert", "error", "gcchris", "system");
        } else {
            $resultset["success"] = false;
            $resultset["toastr_msg"] = "All files failed to upload!";
            $resultset["failed_files"] = $failed;
            $this->core_layout->setEventLog("File upload failed: " . implode(", ", $failed), "insert", "failed", "gcchris", "system");
        }
        
        return $resultset;
    }

    public function getEventAttachments($id){
        $attachments = $this->db
            ->select("a.id, a.filename, a.type, b.name")
            ->from($this->eventsAttachmentsTable . " as a")
            ->join($this->eventsSettingsTable . " as b", "b.id = a.type", "left")
            ->where("a.event_id", $id)
            ->get()
            ->result();
    
        $grouped = [];
    
        foreach ($attachments as $attachment) {
            $grouped[$attachment->name][] = [
                'id'       => $attachment->id,
                'filename' => $attachment->filename,
                'type'     => $attachment->type,
                'name'     => $attachment->name
            ];
        }
    
        return $grouped;
    }

    public function removeFile(){
        $post = $this->input->post();
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        $filePath = isset($post['file_path']) ? $post['file_path'] : '';
    
        $response = ['success' => false, 'message' => 'Invalid request.'];
    
        if ($id && $filePath) {
            $decodedPath = urldecode($filePath);
    
            if (file_exists($decodedPath)) {
                if (!unlink($decodedPath)) {
                    $response['message'] = 'Unable to delete file from server.';
                    return $response;
                    ;
                }
            }
    
            $this->db->where('id', $id);
            if ($this->db->delete($this->eventsAttachmentsTable)) {
                $response = [
                    'success' => true,
                    'message' => 'File deleted successfully.'
                ];
            } else {
                $response['success'] = false;
                $response['message'] = 'Failed to remove file record from database.';
            }
        }
    
       return $response;
    }

    public function getAssignedSchedule($id){
        return $this->db->select('a.participant_id, a.schedule_id, b.event_id, b.title')
            ->from($this->events_attendance . ' a')
            ->join($this->eventsSched . ' b', 'a.schedule_id = b.id', 'left')
            ->where('b.event_id', $id)
            ->where('b.is_archived', 0)
            ->get()
            ->result_array();
    }

    public function getEventSchedule($id){
        $this->db->select('id, start, end, title, description, event_date, location');
        $this->db->where('event_id', $id);
        $this->db->order_by('event_date', 'ASC');
        $result = $this->db->get($this->eventsSched)->result_array();

        $grouped = [];
        foreach ($result as $row) {
            $formatted_date = date('F d, Y', strtotime($row['event_date']));
            $grouped[$formatted_date][] = $row;
        }

        return $grouped;
    }

    public function newEventSched(){
        $resultset = array();
        $post = $this->input->post();
        $post['start'] = date("H:i", strtotime($post['start']));
        $post['end'] = date("H:i", strtotime($post['end']));
        $post['event_date'] = date("Y-m-d", strtotime($post['event_date']));
        $insert = $this->db->insert($this->eventsSched, $post);
        if ($insert){
            $resultset["success"] = true;
            $resultset["schedule"] = $this->getEventSchedule($post['event_id']);
            $resultset["toastr_msg"] = "Event schedule has been added.";
        }
        else{
            $resultset["success"] = false;
            $resultset["toastr_msg"] = "Failed to add event schedule.";
        }
        return $resultset;
    }

    public function updateSchedule(){
        $resultset = array();
        $post = $this->input->post();
        $id = $post['id'];
        $start_24 = date("H:i", strtotime($post['start']));
        $end_24 = date("H:i", strtotime($post['end']));
        $event_date = date("Y-m-d", strtotime(str_replace('-', '/', $post['event_date'])));
        $data = array(
            'start' =>    $start_24,
            'end' =>  $end_24,
            'title' => $post['title'],
            'description' => $post['description'],
            'event_date' =>  $event_date,
            'location' => $post['location'],
        );
        $this->db->where('id', $id);
        $update = $this->db->update($this->eventsSched, $data);
        if ($update){
            $resultset["success"] = true;
            $resultset["schedule"] = $this->getEventSchedule($post['event_id']);
            $resultset["toastr_msg"] = "Event schedule has been updated.";
        }
        else{
            $resultset["success"] = false;
            $resultset["toastr_msg"] = "Failed to update event schedule.";
        }
        return $resultset;
    }

    public function deleteSchedule(){
        $resultset = array();
        $post = $this->input->post();
        $id = $post['id'];
        $this->db->where('id', $id);
        $delete = $this->db->delete($this->eventsSched);
        if ($delete){
            $resultset["success"] = true;
            $resultset["schedule"] = $this->getEventSchedule($post['event_id']);
            $resultset["toastr_msg"] = "Event schedule has been deleted.";
        }
        else{
            $resultset["success"] = false;
            $resultset["toastr_msg"] = "Failed to delete event schedule.";
        }
        return $resultset;
    }

    public function assignEventSched() {
        $post = $this->input->post();
        $events_id = $post['events_id'];
        $id = $post['events_participants_id'];
        
        $this->db->select("a.description, a.start, a.end, a.title, a.event_date, a.event_id, a.location, a.id as schedule_id, b.id as participant_id, IF(c.id IS NULL, 0, 1) AS is_assigned,  c.id as attendance_id");
        $this->db->from($this->eventsSched . ' a');
        $this->db->join($this->eventsParticipantsTable . ' b', 'a.event_id = b.event_id', 'left');
        $this->db->join($this->events_attendance . ' c', 'a.id = c.schedule_id AND b.id = c.participant_id', 'left');
        $this->db->where('a.event_id', $events_id);
        $this->db->where('b.id', $id);
        
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function assignParticipant(){
        $post = $this->input->post();
        $data = array(
            'participant_id' => $post['participant_id'],
            'schedule_id' => $post['schedule_id'],
            'created_by' => $this->user_data['emp_id'],
        );
        $insert = $this->db->insert($this->events_attendance, $data);
        if($insert){
            $resultset["success"] = true;
            $resultset["toastr_msg"] = "Participant has been assigned to schedule.";
            $this->core_layout->setEventLog("Participant has been assigned to schedule","insert", "success", "gcchris", "user");
        }
        else{
            $resultset["success"] = false;
            $resultset["toastr_msg"] = "Failed to assign participant to schedule.";
            $this->core_layout->setEventLog("Failed to assign participant to schedule","insert", "error", "gcchris", "system");
        }
        return $resultset;
    }

    public function unassignParticipant(){
        $post = $this->input->post();
        $this->db->where('schedule_id', $post['schedule_id']);
        $this->db->where('participant_id', $post['participant_id']);
        $delete = $this->db->delete($this->events_attendance);
        if($delete){
            $resultset["success"] = true;
            $resultset["toastr_msg"] = "Participant has been unassigned from schedule.";
            $this->core_layout->setEventLog("Participant has been unassigned from schedule","insert", "success", "gcchris", "user");
        }
        else{
            $resultset["success"] = false;
            $resultset["toastr_msg"] = "Failed to unassign participant from schedule.";
            $this->core_layout->setEventLog("Failed to unassign participant from schedule","insert", "error", "gcchris", "system");
        }
        return $resultset;
    }

    public function takeAttendance($sch = null) {
        $post = $this->input->post();
        if(isset($post['sched_id']) && $post['sched_id'] != null){
            $schedule_id = $post['sched_id'];
        }else{
            $schedule_id = $sch;
        }
        $this->db->select("a.id,a.participant_id,a.is_present, b.firstname, b.middlename, b.lastname, b.cert_awarded, t.attachment as cert_attachment,
        CONCAT(
            LOWER(b.firstname),
            IF(b.middlename IS NOT NULL AND b.middlename != '', CONCAT(' ', UPPER(LEFT(b.middlename, 1)), '.'), ''),
            ' ',
            LOWER(b.lastname)
        ) AS fullname,b.emp_id,b.email,b.mobile_no,b.position,b.company,b.department");
        $this->db->from($this->events_attendance.' a');
        $this->db->where('schedule_id', $schedule_id);
        $this->db->join($this->eventsParticipantsTable.' b', 'a.participant_id = b.id', 'left');
        $this->db->join($this->tbltrainings." as t", "b.cert_awarded = t.id", "left");
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function updateAttendance(){
        $post = $this->input->post();
        $data = array(
            'is_present' => $post['is_present'],
            'updated_by' => $this->user_data['emp_id'],
        );
        $this->db->where('id', $post['id']);
        $update = $this->db->update($this->events_attendance, $data);
        if($update){
            $resultset["success"] = true;
            $resultset["toastr_msg"] = "Successfully updated attendance.";
            $this->core_layout->setEventLog("Successfully updated attendance","update", "success", "gcchris", "user");
        }
        else{
            $resultset["success"] = false;
            $resultset["toastr_msg"] = "Failed to update attendance.";
            $this->core_layout->setEventLog("Failed to update attendance","update", "error", "gcchris", "system");
        }
        return $resultset;
    }

    function uploadEmployeeTraining() {
        $resultset = array();
        $post = $this->input->post();
        $id = $post['is_employee'] ? $post['employee_id'] : $post['applicant_id'];
        $imagesPath = "";
        if($post['is_employee'] == '1'){
            $imagesPath = "./uploads/files/documents/employee_files/empcode_{$post["employee_id"]}/trainings";
        }else{
            $imagesPath = "./uploads/files/documents/applicant_files/appcode_{$post["applicant_id"]}/trainings";
        }

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
                    $fullname = $this->getEmployeeName($id,$post['is_employee']);
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "File upload successful.";
                    $resultset["toastr_state"] = "success";
                    $resultset["filename"] = $filename;
                    $this->core_layout->setEventLog("Employee Training - File upload successfull. filename: <strong>$filename</strong> for employee: <strong>$fullname</strong>","file upload", "success", "gcchris", "user");
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
        
        return $resultset;
    }

    public function checkAttendance() {
        $post = $this->input->post();
        $event_id = $post['events_id'];
        $participant_id = $post['participant_id'];
    
        $this->db->select('a.title, a.description, a.event_date, a.start, a.end, a.location, b.is_present, b.id as attendance_id');
        $this->db->from($this->eventsSched . ' a');
        $this->db->join($this->events_attendance . ' b', 'a.id = b.schedule_id AND b.participant_id = ' . $this->db->escape($participant_id), 'left');
        $this->db->where('a.event_id', $event_id);
        $this->db->where('b.participant_id', $participant_id);
        $this->db->order_by('a.event_date', 'ASC');
        $rows = $this->db->get()->result_array();
        $grouped = [];
        foreach ($rows as $row) {
            $date = $row['event_date'];
            unset($row['event_date']);
            $grouped[$date][] = $row;
        }
        $indexed = [];
        foreach ($grouped as $date => $items) {
            $indexed[] = [
                'event_date' => $date,
                'schedules' => $items
            ];
        }
        $result = [
            'participant_id' => $participant_id,
            'attendance' => $indexed
        ];
        return $result;
    }

    public function removeCertificate(){
        $post = $this->input->post();
        $id = $post['participant_id'] ?? null;
        if (!$id) {
            return ['success' => false,'message' => 'Invalid participant ID.'];
        }
    
        $this->db->trans_begin();
        $this->db->where('id', $id);
        $update = $this->db->update($this->eventsParticipantsTable , ['cert_awarded' => 0]);
    
        if (!$update) {
            $this->db->trans_rollback();
            return ['success' => false,'message' => 'Failed to update participant record.'];
        }
    
        $this->db->where('id', $id);
        $delete = $this->db->update($this->tbltrainings, ['is_archived' => 0]);
    
        if (!$delete) {
            $this->db->trans_rollback();
            return ['success' => false,'message' => 'Failed to delete training record.'];
        }
    
        if (!empty($post['file_path']) && file_exists($post['file_path'])) {
            if (!unlink($post['file_path'])) {
                $this->db->trans_rollback();
                return ['success' => false,'message' => 'Failed to delete certificate file.'];
            }
        }
        $this->db->trans_commit();
        return ['success' => true,'message' => 'Certificate record removed successfully.','participants' => $this->getEventParticipants($post['event_id'])];
    }



    public function newEventsSettings(){
        $response = array();
        $post = $this->input->post();
        $post['created_by'] = $this->user_data['emp_id'];
        unset($post['csrf_token']);
        foreach ($post as $key => $value) {
            if (is_string($value)) {
                $post[$key] = strtolower(trim($value));
            }
        }
        $data = $this->db->insert($this->eventsSettingsTable, $post);
        if($data){
            $response['success'] = true;
            $response['message'] = "Event settings has been saved.";
        }else{
            $response['success'] = false;
            $response['message'] = "Failed to save event settings.";
        }
        return $response;
    }
    
    

    public function getEventsSettings(){
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $is_archived = (isset($post["is_archived"]) && $post["is_archived"]) ? $post["is_archived"] : 0;
        $rowData = array();
        $rowData = $this->getEventsSettingsData($limit, $offset, $sortBy, $sortOrder, $search, $is_archived);
        $rowCount = $this->getEventsSettingsDataCount($search, $is_archived);
      
        $data["recordsTotal"] = $rowCount;
        $data["recordsFiltered"] = $rowCount;
        $data["data"] = $rowData;
        return $data;
    }

    private function getEventsSettingsData($limit, $offset, $sortBy, $sortOrder, $search,$is_archived){
        $filterFields = array('a.name', 'a.type', 'e.first_name', 'e.last_name');
        $this->db->select("a.id,a.name,a.type, a.created_by, a.created_at,
            CONCAT(LOWER(e.firstname),
                IF(
                    e.middlename IS NOT NULL AND e.middlename != '',
                    CONCAT(' ', UPPER(LEFT(e.middlename, 1)), '.'),
                    ''
                ),
                ' ',
                LOWER(e.lastname)
            ) AS fullname, hex_code
        ");
        $this->db->from($this->eventsSettingsTable . " a");
        $this->db->join($this->employeesTable.' as e','e.id = a.created_by','left');
        $this->db->where('a.is_archived', $is_archived);

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
        return  $result;
    }

    private function getEventsSettingsDataCount($search, $is_archived){
        $filterFields = array('a.name', 'a.type', 'e.first_name', 'e.last_name');
        $this->db->select('a.id, a.name, a.type, ');
        $this->db->from($this->eventsSettingsTable . " a");
        $this->db->join($this->employeesTable.' as e','e.id = a.created_by','left');
        $this->db->where('a.is_archived', $is_archived);
        
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

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getEventsOptions(){
        $this->db->select('id, name AS text, type');
        $this->db->from($this->eventsSettingsTable);
        $this->db->where('name !=', '');
        $this->db->where('is_archived', 0);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        $grouped = [];
        foreach ($result as $row) {
            $typeKey = str_replace(' ', '_', strtolower($row['type']));
            if (!isset($grouped[$typeKey])) {
                $grouped[$typeKey] = [];
            }
            $grouped[$typeKey][] = [
                'id' => $row['id'],
                'text' => $row['text']
            ];
        }
    
        return $grouped;
    }

    public function updateEventsSettings(){
        $post = $this->input->post();
        $id = $post['id'];
        unset($post['csrf_token'], $post['id']);
        $this->db->where('id', $id);
        $update = $this->db->update($this->eventsSettingsTable, $post);
        if($update){
            $response['success'] = true;
            $response['message'] = "Event settings has been updated.";
        }else{
            $response['success'] = false;
            $response['message'] = "Failed to update event settings.";
        }
        return $response;
    }

    public function archiveEventSettings(){
        $post = $this->input->post();
        $id = $post['id'];
        $action = $post['action']; 
    
        $this->db->where('id', $id);
        $update = $this->db->update($this->eventsSettingsTable, ['is_archived' => $action]);
    
        if ($update) {
            $message = ($action == 1) ? 'Event settings have been archived successfully.' : 'Event settings have been restored successfully.';
            return ['success' => true, 'message' => $message];
        }
    
        $message = ($action == 1) ? 'Failed to archive event settings.' : 'Failed to restore event settings.';
        return ['success' => false, 'message' => $message];
    }

    public function updateBudget(){
        $post = $this->input->post();
        $id = $post['event_id'];
        unset($post['csrf_token'], $post['event_id'], $post['variance']);
        $this->db->where('id', $id);
        $update = $this->db->update($this->eventsCalendarTable, $post);
        if($update){
            $response['success'] = true;
            $response['message'] = "Budget has been updated.";
        }else{
            $response['success'] = false;
            $response['message'] = "Failed to update budget.";
        }
        return $response;
    }

    // public function massAddParticipants(){
    //     $employee = $this->getEmployeeInformation();

    // }

}