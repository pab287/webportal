<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Transmittal extends MY_Controller
    {
        private $user_data = array();

        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("eforms-transmittal_report");
            $this->authenticate->doRedirect();
            $this->core_layout->setPrivilegeName("eforms-transmittal_report");

            // $this->authenticate->setModuleAccess("eforms");
            // $this->authenticate->doRedirect();
            $this->load->model("Transmittal_m", "transmittal");
            $this->user_data = $this->session->userdata("logged_in");
        }

        public function index()
        {
            $this->core_layout->addJs("global/js/amcharts4/core.js", true);
            $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
            $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
            $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
            $this->core_layout->addJs("js/eforms/transmittal/dashboard.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('eforms/transmittal/dashboard');
            $this->load->view('core/templates/footer');
        }

        public function masterfile()
        {
            $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
            $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
            
            $this->core_layout->setPageTitle("Transmittal - Masterfile");
            $this->core_layout->setPrivilegeName("tr_masterfile");
            $this->core_layout->addJs("js/eforms/transmittal/transmittal.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('eforms/transmittal/index');
            $this->load->view('core/templates/footer');
            $user_id = $this->core_layout->getUserId();
            $this->transmittal->delete_temp_all($user_id);
        }

        public function archive_transmittal()
        {
            $this->core_layout->setPageTitle("Transmittal - Archive");
            $this->core_layout->setPrivilegeName("tr_archive");
            $this->core_layout->addJs("js/eforms/transmittal/archive_transmittal.js", true);
            $this->load->view('core/templates/header');
            $this->load->view('eforms/transmittal/archive_transmittal');
            $this->load->view('core/templates/footer');
        }

        function get_datatable_request()
        {
            $data = $this->transmittal->getDatatableRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_archive_request()
        {
            $data = $this->transmittal->getArchiveRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_temp_request()
        {
            $user_id = $this->user_data['emp_id'];
            $data = $this->transmittal->getTempRequest($user_id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_content_request($id)
        {

            $data = $this->transmittal->getContentRequest($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function new_transmittal()
        {
            $this->core_layout->setPageTitle("Transmittal - New Transmittal");
            $this->core_layout->setPrivilegeName("tr_masterfile");
            $this->core_layout->addJs("js/eforms/transmittal/new_transmittal.js", true);
            // $this->core_layout->addJs("plugins/datetimepicker/src/js/bootstrap-datetimepicker.js");
            $this->load->view('core/templates/header');
            $this->load->view('eforms/transmittal/new_transmittal');
            $this->load->view('core/templates/footer');
            $user_id = $this->core_layout->getUserId();
            $this->transmittal->delete_temp_all($user_id);
        }

        function view_transmittal()
        {
            $this->core_layout->setPageTitle("Transmittal - View Transmittal");
            $this->core_layout->setPrivilegeName("tr_masterfile");
            $this->core_layout->addJs("js/eforms/transmittal/view_transmittal.js", true);
            // $this->core_layout->addJs("plugins/datetimepicker/src/js/bootstrap-datetimepicker.js");
            $this->load->view('core/templates/header');
            $this->load->view('eforms/transmittal/view_transmittal');
            $this->load->view('core/templates/footer');

        }

        function print_transmittal()
        {
            $this->core_layout->setPrivilegeName("tr_masterfile");
            $this->core_layout->addJs("js/eforms/transmittal/print_transmittal.js", true);
            $this->load->view('eforms/transmittal/print_transmittal');
        }

        function edit_transmittal()
        {
            $this->core_layout->setPageTitle("Transmittal - Edit Transmittal");
            $this->core_layout->setPrivilegeName("tr_masterfile");
            $this->core_layout->addJs("js/eforms/transmittal/edit_transmittal.js", true);
            // $this->core_layout->addJs("plugins/datetimepicker/src/js/bootstrap-datetimepicker.js");
            $this->load->view('core/templates/header');
            $this->load->view('eforms/transmittal/edit_transmittal');
            $this->load->view('core/templates/footer');

        }

        function get_company_collection()
        {
            $data = $this->transmittal->getCompanyCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_daily()
        {
            $data = $this->transmittal->getDaily();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_weekly()
        {
            $data = $this->transmittal->getWeekly();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_department_collection()
        {
            $data = $this->transmittal->getDepartmentCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_request_collection()
        {
            $data = $this->transmittal->getEmployeeCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_vehicle_collection()
        {
            $data = $this->transmittal->getVehicleCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function ajax_emp_details($emp)
        {

            $data = $this->transmittal->emp_details($emp);
            echo json_encode($data);
        }

        public function ajax_vehicle_details($veh)
        {
            $data = $this->transmittal->vehicle_details($veh);
            echo json_encode($data);
        }

        public function add_temp_content()
        {
            $user_id = $this->user_data['emp_id'];
            $data = array(
                'user_id' => $user_id,
                'description' => $this->input->post('description')
            );
            $insert = $this->transmittal->save_temp_content($data);
            if($insert){
                $this->core_layout->setEventLog("New Transmittal - Add content `{$this->input->post('description')}`.", "add", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("New Transmittal - Failed add content `{$this->input->post('description')}`.", "add", "error", "gcceforms", "system");
            }
            echo json_encode(array("status" => TRUE));
        }

        public function add_content($id)
        {
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data2 = array(
                'last_edited_by' => $this->user_data['emp_id'],
                'last_edited_dt' => $date
            );
            $this->transmittal->update_transmittal(array('id' => $id), $data2);
            $data = array(
                'transmittal_id' => $id,
                'description' => $this->input->post('description')
            );
            $insert = $this->transmittal->save_content($data);
            if($insert){
                $message = "Edit Transmittal - Add content `{$this->input->post('description')}`.";
                $type = "success";
                $table = "user";
            }else{
                $message = "Edit Transmittal - Failed Add content `{$this->input->post('description')}`.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function edit_temp_content($id)
        {
            $data = $this->transmittal->edit_temp($id);
            echo json_encode($data);
        }

        public function edit_content($id)
        {
            $data = $this->transmittal->edit_content($id);
            echo json_encode($data);
        }

        public function update_temp_content()
        {

            $data = array(

                'description' => $this->input->post('description')
            );
            if($this->transmittal->update_temp(array('id' => $this->input->post('id')), $data)){
                $message = "New Transmittal - Update content `{$this->input->post('description')}`.";
                $type = "success";
                $table = "user";
            }else{
                $message = "New Transmittal - Failed update content `{$this->input->post('description')}`.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function update_content()
        {

            $data = array(

                'description' => $this->input->post('description')
            );
            $this->transmittal->update_content(array('id' => $this->input->post('id')), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_temp_content($id)
        {
            
            if($this->transmittal->delete_temp($id)){
                $message = "New Transmittal - Delete content.";
                $type = "success";
                $table = "user";
            }else{
                $message = "New Transmittal - Failed delete content.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "delete", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_temp_all_content()
        {
            $user_id = $this->user_data['emp_id'];
            
            if($this->transmittal->delete_temp_all($user_id)){
                $message = "New Transmittal - Delete all content.";
                $type = "success";
                $table = "user";
            }else{
                $message = "New Transmittal - Failed delete content.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "delete", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_content($id)
        {
            $this->transmittal->delete_content($id);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_all_content($id)
        {

            $this->transmittal->delete_content_all($id);
            echo json_encode(array("status" => TRUE));
        }

        public function add_transmittal()
        {
            $user_id = $this->user_data;
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $year = substr($date, 2, 2);
            $month = substr($date, 5, 2);
            $list = $this->transmittal->series($year, $month);
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
            $service = $this->input->post('is_service');
            $other = $this->input->post('is_other');
            if ($service == true) {
                $service = "1";
                $other = "0";
                $vehicle = $this->input->post('vehicle');
                $driver = $this->input->post('driver');
            }
            if ($other == true) {
                $service = "0";
                $other = "1";
                $vehicle = "";
                $driver = "";
            }
            if ($this->input->post('type') == "internal") {
                $x = explode("\n", $this->input->post('deliver_company'));
                $data = array(
                    'ref_yr' => $year,
                    'ref_series' => $series,
                    'ref_month' => $month,
                    'reference_no' => 'TR' . $year . '-' . $month . '-' . $series,
                    'requested_by' => $this->input->post('requested_by'),
                    'status' => 'Pending',
                    'priority' => $this->input->post('priority'),
                    'created_id' => $user_id['emp_id'],
                    'created_dt' => $date,
                    'created_by' => $user_id['emp_id'],
                    'company_from' => $this->input->post('company_id'),
                    'department_from' => $this->input->post('dep_id'),
                    'ship_to' => $this->input->post('deliver_to'),
                    'company_to' => $x[0],
                    'department_to' => $x[1],
                    'position_to' => $x[2],
                    'ship_date' => $this->input->post('delivery_date'),
                    'ship_to_address' => $this->input->post('deliver_address'),
                    'purpose' => $this->input->post('info'),
                    'is_service' => $service,
                    'is_others' => $other,
                    'vehicle_id' => $vehicle,
                    'driver' => $driver,
                    'others_remarks' => $this->input->post('remark'),
                    'transporter' => $this->input->post('transporter'),
                    'cat' => 'in',

                );
                $insert = $this->transmittal->save_transmittal($data);
                $last_id = $this->db->insert_id();
                if($insert){
                    $message = "New Transmittal - Add transmittal {$data['reference_no']}.";
                    $type = "success";
                    $table = "user";
                }else{
                    $message = "New Transmittal - Failed add transmittal.";
                    $type = "error";
                    $table = "system";
                }
                $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
                $list2 = $this->transmittal->get_contents($user_id['emp_id']);
                foreach ($list2 as $arr) {
                    $data = array(
                        'transmittal_id' => $last_id,
                        'description' => $arr->description
                    );
                    $insert = $this->transmittal->save_content($data);
                }
                if ($last_id) {
                    // $this->temporary_sending_email($last_id);
                }
                $this->transmittal->delete_temp_all($user_id['emp_id']);
                echo json_encode(array("status" => TRUE));
            }
            if ($this->input->post('type') == "external") {

                $data = array(
                    'ref_yr' => $year,
                    'ref_series' => $series,
                    'ref_month' => $month,
                    'reference_no' => 'TR' . $year . '-' . $month . '-' . $series,
                    'requested_by' => $this->input->post('requested_by'),
                    'status' => 'Pending',
                    'priority' => $this->input->post('priority'),
                    'created_id' => $user_id['emp_id'],
                    'created_dt' => $date,
                    'created_by' => $user_id['emp_id'],
                    'company_from' => $this->input->post('company_id'),
                    'department_from' => $this->input->post('dep_id'),
                    'ship_to' => $this->input->post('delivery_to_ex'),
                    'company_to' => $this->input->post('deliver_company'),
                    'department_to' => $this->input->post('department'),
                    'ship_date' => $this->input->post('delivery_date'),
                    'ship_to_address' => $this->input->post('deliver_address'),
                    'purpose' => $this->input->post('info'),
                    'is_service' => $service,
                    'is_others' => $other,
                    'vehicle_id' => $vehicle,
                    'driver' => $driver,
                    'others_remarks' => $this->input->post('remark'),
                    'transporter' => $this->input->post('transporter'),
                    'waybill' => $this->input->post('courier'),
                    'cat' => 'ex',
                );
                $insert = $this->transmittal->save_transmittal($data);
                $last_id = $this->db->insert_id();
                if($insert){
                    $message = "New Transmittal - Add transmittal {$data['reference_no']}.";
                    $type = "success";
                    $table = "user";
                }else{
                    $message = "New Transmittal - Failed add transmittal.";
                    $type = "error";
                    $table = "system";
                }
                $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
                $list2 = $this->transmittal->get_contents($user_id['emp_id']);
                foreach ($list2 as $arr) {
                    $data = array(
                        'transmittal_id' => $last_id,
                        'description' => $arr->description
                    );
                    $insert = $this->transmittal->save_content($data);
                }
                $this->transmittal->delete_temp_all($user_id['emp_id']);
                if ($last_id) {
                    $this->temporary_sending_email($last_id);
                }
                echo json_encode(array("status" => TRUE));
            }
        }

        public function update_transmittal($id)
        {

            date_default_timezone_set('Asia/Singapore');
            $user_id = $this->user_data;
            $service = $this->input->post('is_service');
            $other = $this->input->post('is_other');
            $date = date('Y-m-d H:i:s');
            if ($service == true) {
                $other = "0";
            }
            if ($other == true) {
                $service = "0";
            }
            if ($this->input->post('type') == "internal") {
                $x = explode("\n", $this->input->post('deliver_company'));
                $data = array(
                    'requested_by' => $this->input->post('requested_by'),
                    'status' => 'Pending',
                    'priority' => $this->input->post('priority'),
                    'created_id' => $user_id['emp_id'],
                    'created_dt' => $date,
                    'created_by' => $user_id['emp_id'],
                    'company_from' => $this->input->post('company_id'),
                    'department_from' => $this->input->post('dep_id'),
                    'ship_to' => $this->input->post('deliver_to'),
                    'company_to' => $x[0],
                    'department_to' => $x[1],
                    'position_to' => $x[2],
                    'ship_date' => $this->input->post('delivery_date'),
                    'ship_to_address' => $this->input->post('deliver_address'),
                    'purpose' => $this->input->post('info'),
                    'is_service' => $service,
                    'is_others' => $other,
                    'vehicle_id' => $this->input->post('vehicle'),
                    'driver' => $this->input->post('driver'),
                    'others_remarks' => $this->input->post('remark'),
                    'transporter' => $this->input->post('transporter'),
                    'cat' => 'in',
                    'last_edited_by' => $this->user_data['emp_id'],
                    'last_edited_dt' => $date,
                );

                $this->transmittal->update_transmittal(array('id' => $id), $data);
                echo json_encode(array("status" => TRUE));
            }
            if ($this->input->post('type') == "external") {

                $data = array(
                    'requested_by' => $this->input->post('requested_by'),
                    'status' => 'Pending',
                    'priority' => $this->input->post('priority'),
                    'created_id' => $user_id['emp_id'],
                    'created_dt' => $date,
                    'created_by' => $user_id['emp_id'],
                    'company_from' => $this->input->post('company_id'),
                    'department_from' => $this->input->post('dep_id'),
                    'ship_to' => $this->input->post('delivery_to_ex'),
                    'company_to' => $this->input->post('deliver_company'),
                    'department_to' => $this->input->post('department'),
                    'ship_date' => $this->input->post('delivery_date'),
                    'ship_to_address' => $this->input->post('deliver_address'),
                    'purpose' => $this->input->post('info'),
                    'is_service' => $service,
                    'is_others' => $other,
                    'vehicle_id' => $this->input->post('vehicle'),
                    'driver' => $this->input->post('driver'),
                    'others_remarks' => $this->input->post('remark'),
                    'transporter' => $this->input->post('transporter'),
                    'waybill' => $this->input->post('courier'),
                    'cat' => 'ex',
                    'last_edited_by' => $this->user_data['emp_id'],
                    'last_edited_dt' => $date

                );
                $this->transmittal->update_transmittal(array('id' => $id), $data);
                echo json_encode(array("status" => TRUE));
            }
        }

        public function ajax_transmittal_details($id){
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $data = $this->transmittal->transmittal_details($id);
            if ($data->vehicle_id == "0") {
                $plateno = "";
            } else {
                $vehicle_data = $this->transmittal->vehicle_details($data->vehicle_id);
                $vehicle_data->plateno = ($vehicle_data->plateno) ? $vehicle_data->plateno : "No plate number";
                $vehicle_data->name = ($vehicle_data->name) ? $vehicle_data->name : "No asset name";
                $plateno = $vehicle_data->plateno . ' | ' . $vehicle_data->name;
            }

            $transmittal_body = $this->transmittal_body($id);

            echo json_encode(array("data" => $data, "transmittal_body" => $transmittal_body, "plateno" => $plateno, "check" => $check));
        }

        public function ajax_transmittal_details2($id){
            $deliver = null;
            $vehicle = null;
            $data = $this->transmittal->tranmittal_details2($id);
            if ($data->is_others == "0") {
                $vehicle_data = $this->transmittal->vehicle_details($data->vehicle_id);
                $vehicle = $vehicle_data->plateno . " | " . $vehicle_data->description;

            }
            $company_data = $this->transmittal->company_details($data->company_from);
            $company_desc = $company_data->description;
            $department_data = $this->transmittal->department_details($data->department_from);
            $department_desc = $department_data->description;
            $name = $this->transmittal->employee_details($data->requested_by);
            // $name = $name->firstname . " " . $name->middlename . " " . $name->lastname;
            if ($data->cat == "in") {
                $deliver = $this->transmittal->employee_details($data->ship_to);
                //$deliver = $deliver->firstname . " " . $deliver->middlename . " " . $deliver->lastname;
            }

            echo json_encode(array("data" => $data, "vehicle" => $vehicle, "company_desc" => $company_desc, "department_desc" => $department_desc
            , "name" => $name, "deliver" => $deliver));
        }

        public function disapprove_transmittal($id){
            $user_id = $this->user_data['emp_id'];
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'status' => 'Disapproved',
                'disapproved_remarks' => $this->input->post('disapproved_remarks'),
                'disapproved_by' => $user_id,
                'disapproved_dt' => $date,
            );
            if($this->transmittal->update_transmittal(array('id' => $id), $data)){
                $message = "View Transmittal - Disapprove transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Transmittal - Failed disapprove transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function undo_disapprove_transmittal($id){
            $data = array(
                'status' => 'Pending',
                'disapproved_by' => '',
                'disapproved_dt' => '',
            );
            if($this->transmittal->update_transmittal(array('id' => $id), $data)){
                $message = "View Transmittal - Undo disapprove transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Transmittal - Failed undo disapprove transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function approve_transmittal($id){
            $user_id = $this->user_data['emp_id'];
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'status' => 'Approved',
                'approve_remarks' => $this->input->post('approve_remarks'),
                'approved_by' => $user_id,
                'approved_dt' => $date,
            );
            if($this->transmittal->update_transmittal(array('id' => $id), $data)){
                $message = "View Transmittal - Approve transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Transmittal - Failed approve transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function undo_approve_transmittal($id){
            $data = array(
                'status' => 'Pending',
                'approve_remarks' => '',
                'approved_by' => '',
                'approved_dt' => '',
            );
            if($this->transmittal->update_transmittal(array('id' => $id), $data)){
                $message = "View Transmittal - Undo approve transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Transmittal - Failed undo approve transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function cancel_transmittal($id)
        {
            $user_id = $this->user_data['emp_id'];
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'status' => 'Cancelled',
                'cancelled_by' => $user_id,
                'cancelled_dt' => $date,
                'cancelled_remarks' => $this->input->post('cancelled_remarks'),
            );
            if($this->transmittal->update_transmittal(array('id' => $id), $data)){
                $message = "View Transmittal - Cancel transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Transmittal - Failed cancel transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function undo_cancel_transmittal($id)
        {
            $data = array(
                'status' => 'Pending',
                'cancelled_by' => '',
                'cancelled_dt' => '',
                'cancelled_remarks' => '',
            );
            if($this->transmittal->update_transmittal(array('id' => $id), $data)){
                $message = "View Transmittal - Undo cancel transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Transmittal - Failed undo cancel transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function receive_transmittal($id)
        {
            $user_id = $this->user_data['emp_id'];
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'status' => 'Received',
                'received_by' => $user_id,
                'received_dt' => $date,
                'received_remarks' => $this->input->post('received_remarks'),
            );
            if($this->transmittal->update_transmittal(array('id' => $id), $data)){
                $message = "View Transmittal - Receive transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Transmittal - Failed receive transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function undo_receive_transmittal($id)
        {
            $data = array(
                'status' => 'Approved',
                'received_by' => '',
                'received_dt' => '',
                'received_remarks' => '',
            );
            if($this->transmittal->update_transmittal(array('id' => $id), $data)){
                $message = "View Transmittal - Undo receive transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Transmittal - Failed undo receive transmittal {$this->transmittal->getTransmittalReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
            echo json_encode(array("status" => TRUE));
        }

        public function populate_head($id)
        {
            $this->db->from('gcceforms.transmittal');
            $this->db->where('id', $id);
            $query = $this->db->get();
            $res = $query->row();

            if ($res->vehicle_id == "0") {
                $plateno = "";
            } else {
                $veh = $this->transmittal->vehicle_details($res->vehicle_id);
                $plateno = $veh->plateno;
            }
            $res->waybill = mb_strtoupper($res->waybill);
            $res->department_to = mb_strtoupper($res->department_to);
            $res->company_to = mb_strtoupper($res->company_to);
            $res->ship_to = mb_strtoupper($res->ship_to);
            $res->driver = mb_strtoupper($res->driver);
            $res->transporter = mb_strtoupper($res->transporter);
            $res->ship_to_address = mb_strtoupper($res->ship_to_address);
            if (is_numeric($res->company_from)) {
                $company = $this->transmittal->company_details($res->company_from);
                $company_det = $company->description;
            } else {
                $company_det = $res->company_from;
            }
            $company_desc = $company_det;
            $approve = $this->transmittal->employee_details($res->approved_by);
            if ($res->cat == "in") {
                $deliver = $this->transmittal->employee_details($res->ship_to);
            } else {
                $deliver = $res->ship_to;
            }

            $populate_body = $this->populate_content_body($id);
            echo json_encode(array("data" => $res, "plateno" => $plateno, "company_desc" => $company_desc, "approved" => $approve, "deliver" => $deliver, "content_body"=>$populate_body));
        }

        public function populate_body($id)
        {
            $this->db->from('gcceforms.transmittal_body');
            $this->db->where('transmittal_id', $id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $rs->description = "<table width='100%'><tr><td>&#8226; </td><td>". mb_strtoupper($rs->description)."</td></tr></table>";
                    $arrData[$key] = $rs;
                }
                echo json_encode($arrData);
            } else {
                return array();
            }
        }

        /*** altered content populate body function return array ***/
        public function populate_content_body($id)
        {
            $arrData = array();
            $this->db->from('gcceforms.transmittal_body');
            $this->db->where('transmittal_id', $id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $key => $rs) {
                    $rs->description = "<table width='100%'><tr><td>&#8226; </td><td>". mb_strtoupper($rs->description)."</td></tr></table>";
                    $arrData[$key] = $rs;
                }
            }
            return $arrData;
        }
        /*** altered content populate body function return array ***/

        public function transmittal_body($id)
        {
            $arrData = array();
            $this->db->from('gcceforms.transmittal_body');
            $this->db->where('transmittal_id', $id);
            $query = $this->db->get();
            // if ($query->num_rows() > 0) {
            //     foreach ($query->result() as $key => $rs) {
            //         $rs->description = "<table width='100%'><tr><td>&#8226; </td><td>". mb_strtoupper($rs->description)."</td></tr></table>";
            //         $arrData[$key] = $rs;
            //     }
            // }
            return $query->result();
        }


        public function get_transmittel_analytics_for_dashboard()
        {
            $data = $this->transmittal->m_get_transmittal_analytics_for_dashboard();
            echo json_encode($data);
        }

        public function temporary_sending_email($id = null, $sm = "New", $sendEmail = true)
        {
            if ($id) {
                $det = $this->transmittal->transmittal_details($id);

                $subject = $det->reference_no;

                $message = "";
                $message = $this->load->view("eforms/email_templates/email-transmittal_template.php", array("id" => $id), true);


                if ($sendEmail) {
                    $module = "eforms_transmittal_new";
                    $email_title = "Transmittal - eForms";
                    $content_title = "Transmittal - eForms";

                    if ($sm == "New") {
                        $content_title = "Transmittal - {$subject}";
                    } else {
                        $content_title = "Transmittal - {$subject} - Edited";
                    }

                    $content = $message;
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content);
                    if ($sent) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    echo $message;
                }
            } else {
                return false;
            }
        }

        public function get_transmittal_creators()
        {
            echo json_encode(array("results" => $this->transmittal->getTransmittalCreators()));
        }

        function export_event_log($export){
            $data = $this->transmittal->exportData($export);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function export_event_log_archived($export){
            $data = $this->transmittal->exportDataArchived($export);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

    }