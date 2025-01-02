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
                $result = "OTHERS";
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
                if($this->input->post('type') == '3'){
                    $loa_date = '<b>DATE </b>: '.$from.chr(10);
                }else{
                    $loa_date = '<b>DATE </b>: '.date_format(date_create($from),"Y-m-d H:i").' - '.date_format(date_create($to),"Y-m-d H:i").chr(10);
                }
    
                $head_id = $this->loa->getTelegramId($department);
                $last_id = $this->db->insert_id();
                $emp_id = $this->loa->getEmpTelegramId($this->input->post('employee'));
    
                if($insert){
                    $telegram_msg = '';
                    $telegram_msg .= '<b>LOA #</b>: '.$referenceNumber.chr(10);
                    $telegram_msg .= '<b>EMPLOYEE: </b>'.strtoupper($this->loa->employee_details($this->input->post('employee'))->display_name).chr(10);
                    $telegram_msg .= '<b>COMPANY: </b>'.strtoupper($company).chr(10);
                    $telegram_msg .= '<b>DEPARTMENT: </b>'.strtoupper($department).chr(10);
                    $telegram_msg .= '<b>TYPE: </b>'.strtoupper($this->leave_type($this->input->post('type'))).chr(10);
                    $telegram_msg .= $loa_date;
                    $telegram_msg .= '<b>NATURE OF LEAVE: </b>'.strtoupper($this->input->post('nature')).chr(10);
                    $telegram_msg .= '<b>REASON: </b>'.strtoupper($this->input->post('reason')).chr(10);
                    $telegram_msg .= '<b>ADDRESS ON LEAVE: </b>'.strtoupper($this->input->post('address')).chr(10);
                    $telegram_msg .= '<b>NUMBER ON LEAVE: </b>'.strtoupper($this->input->post('phone')).chr(10);
                    if($this->loa->telegram_config_if_exist('loa', 'count') > 0){
    
                        if($emp_id){
                            $this->loa->telegram($telegram_msg);
                        }
                        if($head_id){
                            if($head_id != 2){
                                $this->loa->telegram_dept_heads($telegram_msg,$head_id);
                            }
                        }else{
                            
                        }
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
            $phone = preg_replace('/[^a-zA-Z0-9]+/', '', $this->input->post('phone')); //removes special characters caused by inputmask

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
            $name = $name->display_name;

            if(is_numeric($data->created_by)){
                $created_by_data = $this->loa->employee_details($data->created_by);
                $created_by = $created_by_data->display_name;
            }else{
                $created_by = $data->created_by;
            }
            if(is_numeric($data->last_edited_by)){
                $last_edited_by_data = $this->loa->employee_details($data->last_edited_by);
                $last_edited_by = $last_edited_by_data->display_name;
            }else{
                $last_edited_by = $data->last_edited_by;
            }
            if(is_numeric($data->approved_by)){
                $approved_by_data = $this->loa->employee_details($data->approved_by);
                $approved_by = $approved_by_data->display_name;
            }else{
                $approved_by = $data->approved_by;
            }
            if(is_numeric($data->disapproved_by)){
                $disapproved_by_data = $this->loa->employee_details($data->disapproved_by);
                $disapproved_by = $disapproved_by_data->display_name;
            }else{
                $disapproved_by = $data->disapproved_by;
            }
            if(is_numeric($data->cancelled_by)){
                $cancelled_by_data = $this->loa->employee_details($data->cancelled_by);
                $cancelled_by = $cancelled_by_data->display_name;
            }else{
                $cancelled_by = $data->cancelled_by;
            }
            if(is_numeric($data->hr_noted_by)){
                $hr_noted_by_data = $this->loa->employee_details($data->hr_noted_by);
                $hr_noted_by = $hr_noted_by_data->display_name;
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
                $this->core_layout->setEventLog("Approve ".$reference_no.".","update", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed approve ".$reference_no.".","update", "error", "gcceforms", "system");
            }

            echo json_encode(array("status" => TRUE));
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
    }