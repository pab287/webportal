<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Loa extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("eforms-leave_of_absence");
            $this->authenticate->doRedirect();
            $this->core_layout->setPrivilegeName("eforms_loa");

            // $this->authenticate->setModuleAccess("eforms");
            // $this->authenticate->doRedirect();
            $this->load->model("Loa_m", "loa");
            $this->load->model("Registry_m", "registry");
            $this->load->model("sms/contacts_model","contacts");
        }

        public function index()
        {
            $this->core_layout->addJs("global/js/amcharts4/core.js", true);
            $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
            $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
            $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
            $this->core_layout->addJs("js/eforms/loa/dashboard.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('eforms/loa/dashboard');
            $this->load->view('core/templates/footer');
        }

        public function masterfile()
        {
            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
            
            $this->core_layout->setPageTitle("Leave of Absence - Masterfile");
            $this->core_layout->addJs("js/eforms/loa/loa.js", true);
            $this->core_layout->setPrivilegeName("eforms_loa");
            $this->load->view('core/templates/header');
            $this->load->view('eforms/loa/index');
            $this->load->view('core/templates/footer');

        }

        public function archive_loa()
        {
            $this->core_layout->setPrivilegeName("loa_archive");
            $this->core_layout->setPageTitle("Leave of Absence - Archive");
            $this->core_layout->addJs("js/eforms/loa/archive_loa.js", true);
            $this->load->view('core/templates/header');
            $this->load->view('eforms/loa/archive_loa');
            $this->load->view('core/templates/footer');
        }

        function get_datatable_request()
        {
            $data = $this->loa->getDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_archive_request()
        {
            $data = $this->loa->getArchiveRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_previous($emp)
        {
            $data = $this->loa->getPrevious($emp);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function new_loa()
        {
            $this->core_layout->setPrivilegeName("eforms_loa");
            $this->core_layout->setPageTitle("Leave of Absence - New LOA");
            $this->core_layout->addJs("js/eforms/loa/new_loa.js", true);
            // $this->core_layout->addJs("plugins/datetimepicker/src/js/bootstrap-datetimepicker.js");
            $this->load->view('core/templates/header');
            $this->load->view('eforms/loa/new_loa');
            $this->load->view('core/templates/footer');

        }

        function view_loa()
        {
            $this->core_layout->setPageTitle("Leave of Absence - View Loa");
            $this->core_layout->addJs("js/eforms/loa/view_loa.js", true);
            $this->core_layout->setPrivilegeName("eforms_loa");
            // $this->core_layout->addJs("plugins/datetimepicker/src/js/bootstrap-datetimepicker.js");
            $this->load->view('core/templates/header');
            $this->load->view('eforms/loa/view_loa');
            $this->load->view('core/templates/footer');

        }

        function edit_loa()
        {
            $this->core_layout->setPrivilegeName("eforms_loa");
            $this->core_layout->setPageTitle("Leave of Absence - Edit Loa");
            $this->core_layout->addJs("js/eforms/loa/edit_loa.js", true);
            // $this->core_layout->addJs("plugins/datetimepicker/src/js/bootstrap-datetimepicker.js");
            $this->load->view('core/templates/header');
            $this->load->view('eforms/loa/edit_loa');
            $this->load->view('core/templates/footer');

        }

        function print_loa($id)
        {
            $data = array();
            $query = $this->crud->load(array("id" => $id), "gcceforms.loa");

            $data["query"] = $query;
            $this->load->view('eforms/loa/print_loa', $data);
        }

        function get_employee_collection()
        {
            $data = $this->loa->getEmployeeCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_user_emp_data()
        {
            $data = $this->loa->getUserEmpData();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function ajax_emp_details($emp)
        {

            $data = $this->loa->emp_details($emp);
            echo json_encode($data);
        }

        public function leave_type($type){
            if($type == '1'){
                $result = "UNDERTIME";
            }elseif($type == '2'){
                $result = "HALF DAY";
            }elseif($type == '3'){
                $result = "WHOLE DAY";
            }else{
                $result = "CUSTOM";
            }
            return $result;
        }

        public function add_loa() {
            $user_id = $this->core_layout->getCurrentEmployeeId();
            $isValidDate = false;
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $year = substr($date, 2, 2);
            $month = substr($date, 5, 2);
            $list = $this->loa->series($year, $month);
            $series = '';
            if (sizeof($list) > 0) {
                foreach ($list as $arr) {
                    $x = $arr->ref_series;
                }
                $series = intval($x) + 1;
                if (strlen($series) == 1) {
                    $series = '000' . $series;
                } else if (strlen($series) == 2) {
                    $series = '00' . $series;
                } else if (strlen($series) == 3) {
                    $series = '0' . $series;
                } else {
                    $series = $series;
                }
            } else {
                $series = '0001';
            }

            // $phone = str_pad($this->input->post('phone'), 11, '0', STR_PAD_LEFT);
            $phone = preg_replace('/[^a-zA-Z0-9]+/', '', $this->input->post('phone')); //removes special characters caused by inputmask

            $x = explode("\n", $this->input->post('company'));
            $company = trim($x[0]);
            $department = trim($x[1]);
            $position = $x[2];

            if ($this->input->post('type') == "1") {
                $from = $this->input->post('under_from');
                $to = substr($this->input->post('under_from'), 0, -6) . ' ' . $this->input->post('under_to') . ':00';
            } else if ($this->input->post('type') == "2") {
                if ($this->input->post('half') == "1") {
                    $from = $this->input->post('half_from') . ' 08:01:00';
                    $to = $this->input->post('half_from') . ' 12:00:00';
                } else {
                    $from = $this->input->post('half_from') . ' 13:01:00';
                    $to = $this->input->post('half_from') . ' 17:00:00';
                }
            } else if ($this->input->post('type') == "3") {
                $from = $this->input->post('whole_date');
                $to = "0000-00-00 00:00:00";
            } else if ($this->input->post('type') == "4") {
                $from = $this->input->post('date_from');
                $to = $this->input->post('date_to');
            }

            $data = array(
                'ref_yr' => $year,
                'ref_series' => $series,
                'ref_month' => $month,
                'reference_no' => 'LOA' . $year . '-' . $month . '-' . $series,
                'employee' => $this->input->post('employee'),
                'company' => $company,
                'department' => $department,
                'position' => $position,
                'nature' => $this->input->post('nature'),
                'address' => $this->input->post('address'),
                'reason' => $this->input->post('reason'),
                'phone' => $phone,
                'date_from' => $from,
                'date_to' => $to,
                'status' => 'Pending',
                'type' => $this->input->post('type'),
                'created_by' => $user_id,
                'created_dt' => $date,

            );

            if ($this->input->post('type') == "4") {
                if (date('Y-m-d H:i', strtotime($this->input->post('date_from'))) > date('Y-m-d H:i', strtotime($this->input->post('date_to')))) {
                    $isValidDate = false;
                } else {
                    $isValidDate = true;
                }
            } else if ($this->input->post('type') == "1") {
                if (date('Y-m-d H:i', strtotime($from)) > date('Y-m-d H:i', strtotime($to))) {
                    $isValidDate = false;
                } else {
                    $isValidDate = true;
                }
            } else {
                $isValidDate = true;
            }

            $referenceNumber = 'LOA'.$year.'-'.$month.'-'.$series;

            if ($isValidDate) {
                $insert = $this->loa->save($data);
                // $head_id = $this->loa->getTelegramId($department);
                $last_id = $insert;
                // $emp_id = $this->loa->getEmpTelegramId($this->input->post('employee'));
    
                if($insert){
                    $loa_date = $this->get_loa_date_sms($this->input->post('type'), $from, $to);

                    $head_contact = $this->getHeadContact($this->input->post('employee'));
                    
                    $smsContact = $this->getContactDetails($head_contact, 'no');
                    $emailContact = $this->getContactDetails($head_contact, 'email');
                    $contact = $smsContact['contact'] ?? null;
                    $msgName = $smsContact['name'] ?? null;
                    $email = $emailContact['contact'] ?? null;
                    $emailName = $emailContact['name'] ?? null;

                    $details = [
                        'referenceNumber' => trim($referenceNumber),
                        'loa_date' => trim($loa_date),
                        'sms_date' => trim(strip_tags($loa_date)),
                        'employeeDisplayName' => trim(strip_tags($this->loa->employee_details($this->input->post('employee'))->display_name)),
                        'leaveType' => trim(strip_tags($this->leave_type($this->input->post('type')))),
                        'nature' => trim(strip_tags($this->input->post('nature'))),
                        'reason' => trim(strip_tags($this->input->post('reason'))),
                        'address' => trim(strip_tags($this->input->post('address'))),
                        'phone' => trim(strip_tags($this->input->post('phone'))),
                        'supervisor' => $emailName,
                        'head_contact' => $head_contact // Include head contact details in the array
                    ];

                    $msg = "Hi $msgName,\n\n" .
                    "{$details['employeeDisplayName']} filed a leave of absence.\n\n" .
                    "LOA #: {$details['referenceNumber']}\n" .
                    "Type: {$details['leaveType']}\n" .
                    "{$details['sms_date']}\n" .
                    "Nature of Leave: {$details['nature']}\n" .
                    "Reason: {$details['reason']}\n" .
                    "Address: {$details['address']}\n" .
                    "Contact No: {$details['phone']}\n\n" .
                    "This is a computer-generated message. Please do not reply to this number.\n\nThank you!";
                    if($contact){
                        $smsResponse = $this->contacts->sendSMS($contact, $msg);
                        if(isset($smsResponse["data"]) && $smsResponse["data"] !== false){
                            $this->core_layout->setEventLog("Sent SMS to head contact for leave of absence ".$referenceNumber.".","add", "success", "gcceforms", "user");
                        }else{
                            $this->core_layout->setEventLog("Failed in sending SMS to head contact for leave of absence ".$referenceNumber.".","add", "error", "gcceforms", "system");
                        }
                    }
                    if($email){
                        $details['contact_person'] = $phone;
                        $details['email'] = $email;
                        $send_email[] = $email;
                        $mailer['send_to'] = $send_email;
                        $details['url'] = site_url('eforms/loa/view_loa?id=').$last_id;
                        $email_content = $this->load->view("eforms/email_templates/email_loa_for_approval.php", array("data" => $details), true);
                        $this->core_layout->send_email('core', 'GC & C Conyx PH', 'Leave of Absence', $email_content, $mailer);
                    }
                    $reference_no = $this->db->get_where("gcceforms.loa", array("id"=>$last_id))->row('reference_no');
                    $this->core_layout->setEventLog("Filed leave of absence ".$reference_no.".","add", "success", "gcceforms", "user");
                }else{
                    $this->core_layout->setEventLog("Failed in adding leave of absence.","add", "error", "gcceforms", "system");
                }
                echo json_encode(array("status" => TRUE, "test" => $to, "last_id" => $last_id));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        }

        public function update_loa($id)
        {
            $user_id = $this->core_layout->getCurrentEmployeeId();
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $isValidDate = false;

            $x = explode("\n", $this->input->post('company'));
            $company = trim($x[0]);
            $department = trim($x[1]);
            $position = $x[2];
            if ($this->input->post('type') == "1") {
                $from = $this->input->post('under_from');
                if(strlen($this->input->post('under_from')) == 16){
                    $to = substr($this->input->post('under_from'), 0, -6) . ' ' . $this->input->post('under_to') . ':00';
                }else{
                    $to = substr($this->input->post('under_from'), 0, -8) . ' ' . $this->input->post('under_to') . ':00';
                }
                
            } else if ($this->input->post('type') == "2") {
                if ($this->input->post('half') == "1") {
                    $from = $this->input->post('half_from') . ' 08:01:00';
                    $to = $this->input->post('half_from') . ' 12:00:00';
                } else {
                    $from = $this->input->post('half_from') . ' 13:01:00';
                    $to = $this->input->post('half_from') . ' 17:00:00';
                }
            } else if ($this->input->post('type') == "3") {
                $from = $this->input->post('whole_date');
                $to = "0000-00-00 00:00:00";
            } else if ($this->input->post('type') == "4") {
                $from = $this->input->post('date_from');
                $to = $this->input->post('date_to');
            }

            // $phone = str_pad($this->input->post('phone'), 11, '0', STR_PAD_LEFT);
            $phone = preg_replace('/[^a-zA-Z0-9]+/', '', $this->input->post('phone'));

            $data = array(
                'employee' => $this->input->post('employee'),
                'company' => $company,
                'department' => $department,
                'position' => $position,
                'nature' => $this->input->post('nature'),
                'address' => $this->input->post('address'),
                'reason' => $this->input->post('reason'),
                'phone' => $phone,
                'date_from' => $from,
                'date_to' => $to,
                'type' => $this->input->post('type'),
                'last_edited_by' => $user_id,
                'last_edited_dt' => $date,

            );

            if ($this->input->post('type') == "4") {
                if (date('Y-m-d H:i', strtotime($this->input->post('date_from'))) > date('Y-m-d H:i', strtotime($this->input->post('date_to')))) {
                    $isValidDate = false;
                } else {
                    $isValidDate = true;
                }
            } else if ($this->input->post('type') == "1") {
                if (date('Y-m-d H:i', strtotime($from)) > date('Y-m-d H:i', strtotime($to))) {
                    $isValidDate = false;
                } else {
                    $isValidDate = true;
                }
            } else {
                $isValidDate = true;
            }

            $reference_no = $this->db->get_where("gcceforms.loa", array("id"=>$id))->row('reference_no');

            if ($isValidDate) {
                if($this->loa->update(array('id' => $id), $data)){
                    $this->core_layout->setEventLog("Updated ".$reference_no.".","update", "success", "gcceforms", "user");
                }else{
                    $this->core_layout->setEventLog("Failed updating ".$reference_no.".","update", "error", "gcceforms", "system");
                }
                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        }

        public function ajax_loa_details($id, $type = null)
        {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $data = $this->loa->loa_details($id, $type);

            $name = $this->loa->employee_details($data->employee);
            $name = !empty($name) ? $name->display_name : "No Assigned Name";

            if(is_numeric($data->created_by)){
                $created_by_data = $this->loa->employee_details($data->created_by);
                $created_by = !empty($created_by_data) ? $created_by_data->display_name : 'No Assigned Name';
            }else{
                $created_by = $data->created_by;
            }
            if(is_numeric($data->last_edited_by)){
                $last_edited_by_data = $this->loa->employee_details($data->last_edited_by);
                $last_edited_by = !empty($last_edited_by_data) ? $last_edited_by_data->display_name : 'No Assigned Name';
            }else{
                $last_edited_by = $data->last_edited_by;
            }
            if(is_numeric($data->approved_by)){
                $approved_by_data = $this->loa->employee_details($data->approved_by);
                $approved_by = !empty($approved_by_data) ? $approved_by_data->display_name : 'No Assigned Name';
            }else{
                $approved_by = $data->approved_by;
            }
            if(is_numeric($data->disapproved_by)){
                $disapproved_by_data = $this->loa->employee_details($data->disapproved_by);
                $disapproved_by = !empty($disapproved_by_data) ? $disapproved_by_data->display_name : 'No Assigned Name';
            }else{
                $disapproved_by = $data->disapproved_by;
            }
            if(is_numeric($data->cancelled_by)){
                $cancelled_by_data = $this->loa->employee_details($data->cancelled_by);
                $cancelled_by = !empty($cancelled_by_data) ? $cancelled_by_data->display_name : 'No Assigned Name';
            }else{
                $cancelled_by = $data->cancelled_by;
            }
            if(is_numeric($data->hr_noted_by)){
                $hr_noted_by_data = $this->loa->employee_details($data->hr_noted_by);
                $hr_noted_by = !empty($hr_noted_by_data) ? $hr_noted_by_data->display_name : 'No Assigned Name';
            }else{
                $hr_noted_by = $data->hr_noted_by;
            }
            
            echo json_encode(array("data" => $data, "name" => $name, "check" => $check, "created_by" => $created_by, "last_edited_by" => $last_edited_by
            , "approved_by" => $approved_by, "disapproved_by" => $disapproved_by, "cancelled_by" => $cancelled_by, "hr_noted_by" => $hr_noted_by));
        }

        public function disapprove_loa($id)
        {
            $user_id = $this->core_layout->getCurrentEmployeeId();
            $date = date('Y-m-d H:i:s');
            $data = array(
                'status' => 'Disapproved',
                'disapproved_remarks' => $this->input->post('disapproved_remarks'),
                'disapproved_by' => $user_id,
                'disapproved_dt' => $date,
            );


            $reference_no = $this->db->get_where("gcceforms.loa", array("id"=>$id))->row('reference_no');
            if($this->loa->update(array('id' => $id), $data)){
                $this->core_layout->setEventLog("Disapproved ".$reference_no.".","update", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed disapprove ".$reference_no.".","update", "error", "gcceforms", "system");
            }
            echo json_encode(array("status" => TRUE));
        }

        public function undo_disapprove_loa($id)
        {
            $data = array(
                'status' => 'Pending',
                'disapproved_by' => '',
                'disapproved_dt' => '',
            );

            $reference_no = $this->db->get_where("gcceforms.loa", array("id"=>$id))->row('reference_no');
            if($this->loa->update(array('id' => $id), $data)){
                $this->core_layout->setEventLog("Undo disapproval ".$reference_no.".","update", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed undo disapprove ".$reference_no.".","update", "error", "gcceforms", "system");
            }
            echo json_encode(array("status" => TRUE));
        }

        public function approve_loa($id)
        {
            $user_id = $this->core_layout->getCurrentEmployeeId();
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'status' => 'Approved',
                'approved_remarks' => $this->input->post('approved_remarks'),
                'approved_by' => $user_id,
                'approved_dt' => $date,
            );

            $reference_no = $this->db->get_where("gcceforms.loa", array("id"=>$id))->row('reference_no');
            if($this->loa->update(array('id' => $id), $data)){
                $details = $this->getLeaveDetails($id);
                $send_to = $details['email'];
                $loa_date = $this->get_loa_date_sms($details['type'], $details['date_from'], $details['date_to']);
                $sms_date = trim(strip_tags($loa_date));
                $contactPerson = $this->getContactPerson($details['supervisor_meta']);
                if (!empty($details['mobile_no']) && preg_match('/^(\+63|0)[0-9]{10}$/', $details['mobile_no'])) {
                    $message = sprintf(
                        "Hi %s,\n\nYour leave for %s is approved. Contact %s if you have any questions or concerns.\n\nThis is a computer generated message please do not reply to this number.\n\nThank you!",
                        ucwords($details['fullname']),
                        $sms_date,
                        ucwords($contactPerson)
                    );
                    $smsResponse = $this->contacts->sendSMS($details['mobile_no'], $message);
                    if(isset($smsResponse["data"]) && $smsResponse["data"] !== false){
                        $this->core_layout->setEventLog("Sent SMS to head contact for leave of absence ".$reference_no.".","add", "success", "gcceforms", "user");
                    }else{
                        $this->core_layout->setEventLog("Failed in sending SMS to head contact for leave of absence ".$reference_no.".","add", "error", "gcceforms", "system");
                    }
                }
                if (!empty($send_to) && preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $send_to) && !in_array(strtolower($send_to), ['none', 'n/a'])) {
                    $details['contact_person'] = $contactPerson;
                    $send_email[] = $send_to;
                    $email_content = $this->load->view("eforms/email_templates/email_loa_approval.php", array("data" => $details), true);
                    $mailer['send_to'] = $send_email;
                    $this->core_layout->send_email('core', 'GC & C Conyx PH', 'Leave of Absence', $email_content, $mailer);
                }
                $this->core_layout->setEventLog("Approve ".$reference_no.".","update", "success", "gcceforms", "user");
                $status = true;
            }else{
                $this->core_layout->setEventLog("Failed approve ".$reference_no.".","update", "error", "gcceforms", "system");
                $status = false;
            }
            
            echo json_encode(array("status" => $status));
        }

        public function undo_approve_loa($id)
        {
            $data = array(
                'status' => 'Pending',
                'approved_remarks' => '',
                'approved_by' => '',
                'approved_dt' => '',
            );

            $reference_no = $this->db->get_where("gcceforms.loa", array("id"=>$id))->row('reference_no');
            if($this->loa->update(array('id' => $id), $data)){
                $this->core_layout->setEventLog("Undo approve ".$reference_no.".","update", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed undo approve ".$reference_no.".","update", "error", "gcceforms", "system");
            }
            echo json_encode(array("status" => TRUE));
        }

        public function cancel_loa($id)
        {
            $user_id = $this->core_layout->getCurrentEmployeeId();
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'status' => 'Cancelled',
                'cancelled_by' => $user_id,
                'cancelled_dt' => $date,
                'cancelled_remarks' => $this->input->post('cancelled_remarks'),
            );

            $reference_no = $this->db->get_where("gcceforms.loa", array("id"=>$id))->row('reference_no');
            if($this->loa->update(array('id' => $id), $data)){
                $this->core_layout->setEventLog("Cancel ".$reference_no.".","update", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed cancel ".$reference_no.".","update", "error", "gcceforms", "system");
            }
            echo json_encode(array("status" => TRUE));
        }

        public function undo_cancel_loa($id)
        {
            $data = array(
                'status' => 'Pending',
                'cancelled_by' => '',
                'cancelled_dt' => '',
                'cancelled_remarks' => '',
            );

            $reference_no = $this->db->get_where("gcceforms.loa", array("id"=>$id))->row('reference_no');
            if($this->loa->update(array('id' => $id), $data)){
                $this->core_layout->setEventLog("Undo cancel ".$reference_no.".","update", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed undo cancel ".$reference_no.".","update", "error", "gcceforms", "system");
            }
            echo json_encode(array("status" => TRUE));
        }

        public function note_loa($id)
        {
            $user_id = $this->core_layout->getCurrentEmployeeId();
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'status' => 'HR Noted',
                'hr_noted_by' => $user_id,
                'hr_noted_dt' => $date,
                'hr_noted_remarks' => $this->input->post('hr_noted_remarks'),
                'hr_noted_pay' => $this->input->post('hr_noted_pay'),
            );

            $reference_no = $this->db->get_where("gcceforms.loa", array("id"=>$id))->row('reference_no');
            if($this->loa->update(array('id' => $id), $data)){
                $this->core_layout->setEventLog("Added note ".$reference_no.".","update", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed adding note ".$reference_no.".","update", "error", "gcceforms", "system");
            }
            echo json_encode(array("status" => TRUE));
        }

        public function undo_note_loa($id)
        {
            $data = array(
                'status' => 'Approved',
                'hr_noted_by' => '',
                'hr_noted_dt' => '',
                'hr_noted_remarks' => '',
                'hr_noted_pay' => '',
            );

            $reference_no = $this->db->get_where("gcceforms.loa", array("id"=>$id))->row('reference_no');
            if($this->loa->update(array('id' => $id), $data)){
                $this->core_layout->setEventLog("Undo adding note ".$reference_no.".","update", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed undo adding note ".$reference_no.".","update", "error", "gcceforms", "system");
            }
            echo json_encode(array("status" => TRUE));
        }

        public function get_dashboard_data()
        {
            $data = $this->loa->m_get_dashboard_data();
            echo json_encode($data);
        }

        public function get_loa_for_today()
        {
            $data = $this->loa->m_get_loa_for_today();
            echo json_encode($data);
        }

        public function get_loa_for_the_week()
        {
            $data = $this->loa->m_get_loa_for_the_week();
            echo json_encode($data);
        }

        public function get_loa_analytics_for_dashboard() {
            $data = $this->loa->m_get_loa_analytics_for_dashboard();
            echo json_encode($data);
        }

        public function add_telegram_config(){
            $data = $this->loa->addTelegramConfig();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        
        public function load_telegram_config(){
            $data = $this->loa->loadTelegramConfig();
            $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
        }
        function disapprove_overdueLoa(){
            $data = $this->loa->disapproveOverdueLoa();
            echo json_encode($data);
           
        }

        public function check_reason(){
            extract($this->input->post());
            $reason = preg_replace('/\s+/', ' ',$reason);
            $reason = trim($reason);
            $reason = strtolower($reason);            
            $reason = str_replace(".","",$reason);
            if ($post = $this->loa->check_reason($reason)) {
                $data = array("reps"=>"success" );
            }else{
                $data = array("reps"=>"error");
            }
            echo json_encode($data);
        }

        public function registry()
        {
            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
            
            $this->core_layout->setPageTitle("Registry - Masterfile");
            $this->core_layout->addJs("js/eforms/loa/registry.js", true);
            $this->core_layout->setPrivilegeName("eforms_loa");
            $this->load->view('core/templates/header');
            $this->load->view('eforms/loa/registry');
            $this->load->view('core/templates/footer');

        }
        function get_datatable_request_registry()
        {  
            $data = $this->registry->getDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        public function insert_registry(){
            extract($this->input->post());
            $reason = preg_replace('/\s+/', ' ',$reason);
            $reason = trim($reason);
            $reason = strtolower($reason);            
            $reason = str_replace(".","",$reason);
            $user_name = $this->core_layout->getCurrentEmployeeId(); 
            $ajax_data = array(
                "created_at"  => date("Y-m-d H:i:s"),
                "created_by"    => $user_name,
                "status"    => $status,
                "reason"    => $reason
            ); 
            if ($post = $this->loa->insert_registry($ajax_data)) {  $data = array("reps"=>"success" ); }
            else{ $data = array("reps"=>"error"); }
            echo json_encode($data);
        }
        public function edit_registry(){
            extract($this->input->post());
            if ($post = $this->loa->edit_registry($id)) { $data = array("reps"=>"success","post"=>$post ); }
            else{ $data = array("reps"=>"error"); }
            echo json_encode($data);
        }
        public function update_registry(){
            extract($this->input->post());
            $reason = preg_replace('/\s+/', ' ',$reason);
            $reason = trim($reason);
            $reason = strtolower($reason);
            $reason = str_replace(".","",$reason);
            $user_name = $this->core_layout->getCurrentEmployeeId(); 
            $ajax_data = array(
                "updated_at"  => date("Y-m-d H:i:s"),
                "updated_by"    => $user_name,
                "status"    => $status,
                "reason"    => $reason
            ); 
            if ($post = $this->loa->update_registry($ajax_data,$id)) {  $data = array("reps"=>"success" ); }
            else{ $data = array("reps"=>"error"); }
            echo json_encode($data);
        }

        function export_event_log($export){
            $data = $this->loa->exportData($export);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function export_event_log_archive($export){
            $data = $this->loa->exportDataArchive($export);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function mass_action_loa(){
            $post = $this->input->post();

            $type = $post['type'];

            $user_id = $this->core_layout->getCurrentEmployeeId();
            $date = date('Y-m-d H:i:s');
            $ids = json_decode($post['checked']);

            if ($type == 1) {
                $data = array(
                    'status' => 'Approved',
                    'approved_remarks' => $post['remarks'],
                    'approved_by' => $user_id,
                    'approved_dt' => $date,
                );
            } else {
                $data = array(
                    'status' => 'Disapproved',
                    'disapproved_remarks' => $post['remarks'],
                    'disapproved_by' => $user_id,
                    'disapproved_dt' => $date,
                );
            }

            $this->db->where_in('id', $ids);
            $query = $this->db->update('gcceforms.loa', $data);

            $this->db->reset_query();

            $this->db->select('reference_no');
            $this->db->where_in('id', $ids);
            $this->db->from('gcceforms.loa');
            $q = $this->db->get();

            $ref_no = array();
            if ($q->num_rows() > 0) {
                foreach ($q->result() as $row) {
                    array_push($ref_no, $row->reference_no);
                }
            }

            $refs = implode(', ', $ref_no);

            if ($query) {
                $status = true;
                $message = 'Successfully ' . ($type == 1 ? 'Approved' : 'Disapproved') . ' selected LOA request.';

                if ($type == 1){
                    $this->core_layout->setEventLog("Approved selected LOA with reference number of `".$refs."`.","update", "success", "gcceforms", "user");
                } else {
                    $this->core_layout->setEventLog("Disapproved selected LOA with reference number of `".$refs."`.","update", "success", "gcceforms", "user");
                }
            } else {
                $status = false;
                $message = 'Failed to ' . ($type == 1 ? 'Approve' : 'Disapprove') . ' selected LOA request.';

                if ($type == 1){
                    $this->core_layout->setEventLog("Failed to approve selected LOA with reference number of `".$refs."`.","update", "error", "gcceforms", "system");
                } else {
                    $this->core_layout->setEventLog("Failed disapprove selected LOA with reference number of `".$refs."`.","update", "error", "gcceforms", "system");
                }
            }

            echo json_encode(array("status" => $status, 'message' => $message));
        }

        private function formatName($firstname, $lastname) {
            return ucfirst(strtolower($firstname)) . ' ' . ucfirst(strtolower($lastname));
        }

        private function getCorporateHR() {
            $query = $this->db->select("firstname, lastname")
                             ->get_where("gccmaster.tblemployees", [
                                 "position" => 145,
                                 "employee_status" => "Active"
                             ]);
            return $this->formatName($query->row()->firstname, $query->row()->lastname);
        }

        private function getLeaveDetails($id) {
            $query = $this->db->query("
                SELECT
                    l.type,
                    l.reference_no,
                    l.nature,
                    l.approved_remarks,
                    l.reason,
                    l.date_from,
                    l.date_to,
                    e.mobile_no,
                    e.position,
                    e.supervisor_meta,
                    COALESCE(u.email, e.email) as email,
                    CONCAT(e.firstname, ' ', e.lastname) AS fullname,
                    CONCAT(a.firstname, ' ', a.lastname) AS approve_by
                FROM gcceforms.loa l
                JOIN gccmaster.tblemployees e ON l.employee = e.id
                JOIN gccmaster.tblemployees a ON l.approved_by = a.id
                LEFT JOIN gccmaster.tblusers u ON u.emp_id = e.id
                WHERE l.id = ?
            ", [$id]);
            return $query->row_array();
        }

        private function getContactPerson($supervisor_meta) {
            if (!$supervisor_meta || !($managerial = @unserialize($supervisor_meta))) {
                return "HR - ".$this->getCorporateHR();
            }
            $query = $this->db->select("firstname, ' ', lastname")
                             ->get_where("gccmaster.tblemployees", [
                                 "id" => $managerial['supervisory'],
                                 "employee_status" => "Active"
                             ]);
            return "Immediate Supervisor - ".$this->formatName($query->row()->firstname, $query->row()->lastname);
        }

        private function getHeadContact($id) {
            $query = $this->db->select("e.supervisor_meta, e.tl_supervisory, d.head_id")
                ->from("gccmaster.tblemployees e")
                ->join("gcchris.tbldepartments d", "d.id = e.department_id", "left")
                ->where("e.id", $id)
                ->get();
        
            $result = $query->row_array();

            if (empty($result['head_id']) && !empty($result['supervisor_meta'])) {
                $supervisorMeta = unserialize($result['supervisor_meta']);
        
                // If tl_supervisory is 1, fetch details for supervisory and managerial IDs
                if ($result['tl_supervisory'] == 1 && is_array($supervisorMeta)) {
                    // Fetch supervisory details
                    if (!empty($supervisorMeta['supervisory'])) {
                        $supervisoryDetails = $this->getEmployeeDetails($supervisorMeta['supervisory']);
                        if ($supervisoryDetails) {
                            $result['supervisory_no'] = $supervisoryDetails['mobile_no'];
                            $result['supervisory_email'] = $supervisoryDetails['email'];
                            $result['supervisory_name'] = $supervisoryDetails['fullname'];
                        }
                    }
        
                    // Fetch managerial details
                    if (!empty($supervisorMeta['managerial'])) {
                        $managerialDetails = $this->getEmployeeDetails($supervisorMeta['managerial']);
                        if ($managerialDetails) {
                            $result['managerial_no'] = $managerialDetails['mobile_no'];
                            $result['managerial_email'] = $managerialDetails['email'];
                            $result['managerial_name'] = $managerialDetails['fullname'];
                        }
                    }
                }
            }
            else{
                $headDetails = $this->getEmployeeDetails($result['head_id']);
                if ($headDetails) {
                    $result['head_no'] = $headDetails['mobile_no'];
                    $result['head_email'] = $headDetails['email'];
                    $result['head_name'] = $headDetails['fullname'];
                    $result['head_telegram_chat_id'] = $headDetails['telegram_chat_id'];
                }
            }
            return $result;
        }

        private function getEmployeeDetails($id) {
            $query = $this->db->select("e.mobile_no, u.email, u.telegram_chat_id, CONCAT(e.firstname, ' ', e.lastname) AS fullname")
                ->from("gccmaster.tblemployees as e")
                ->join("gccmaster.tblusers u", "u.emp_id = e.id", "left")
                ->where("e.id", $id)
                ->get();
        
            return $query->row_array();
        }

        private function get_loa_date_sms($type, $date_from, $date_to) {
            switch ($type) {
                case 1: // Undertime
                case 2: // Half Day
                    return '<strong>Date:</strong> ' . date('F j, Y', strtotime($date_from))."\n".'Time: ' . date('h:i A', strtotime($date_from)) . ' - ' . date('h:i A', strtotime($date_to)) . "\n";
                    break;
                
                case 3: // Whole Day
                    return '<strong>Date:</strong> ' . date('F j, Y', strtotime($date_from)) . "\n";
                    break;
                
                default: // Custom
                    return '<strong>Date From:</strong> ' . date('F j, Y', strtotime($date_from)) . "\n" .
                           '<br/><strong>Date To:</strong> ' . date('F j, Y', strtotime($date_to)) . "\n";
                    break;
            }
        }

        private function getContactDetails($head_contact, $type) {
            $fields = [
                'head' => ['no' => 'head_no', 'email' => 'head_email', 'name' => 'head_name'],
                'supervisory' => ['no' => 'supervisory_no', 'email' => 'supervisory_email', 'name' => 'supervisory_name'],
                'managerial' => ['no' => 'managerial_no', 'email' => 'managerial_email', 'name' => 'managerial_name']
            ];
        
            foreach ($fields as $key => $field) {
                if (!empty($head_contact[$field[$type]])) {
                    return [
                        'contact' => $head_contact[$field[$type]],
                        'name' => $head_contact[$field['name']]
                    ];
                }
            }
            return null;
        }
    }