<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Travel_order extends MY_Controller
{
    protected $eformsKey;
    public function __construct()
    {
        parent::__construct();
        // $this->load->model("sms/contacts_model","contacts");
        $this->authenticate->setModuleAccess("eforms-travel_order");
        $this->authenticate->doRedirect();
        $this->load->model("Travel_order_m", "travel_order");
        $this->user_data = $this->session->userdata("logged_in");
        $this->eformsKey = $_ENV['PROD_MAP_KEY']; 
    }

    public function index()
    {
        $this->core_layout->setPrivilegeName("eforms_travel_order");
        $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js", true);
        $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
        $this->core_layout->addJs("global/js/amcharts4/core.js", true);
        $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
        $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
        $this->core_layout->addJs("js/eforms/travel_order/dashboard.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/travel_order/dashboard');
        $this->load->view('core/templates/footer');
    }

    public function masterfile()
    {
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
        $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
        
        $this->core_layout->setPageTitle("Travel Order - Masterfile");
        $this->core_layout->setPrivilegeName("to_masterfile");
        $this->core_layout->addJs("js/eforms/travel_order/employee_to.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/travel_order/index');
        $this->load->view('core/templates/footer');
    }

    public function archive_travel_order()
    {
        $this->core_layout->setPageTitle("Travel Order - Archive");
        $this->core_layout->setPrivilegeName("to_archive");
        $this->core_layout->addJs("js/eforms/travel_order/archive_travel_order.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/travel_order/archive_travel_order');
        $this->load->view('core/templates/footer');
    }

    function most_traveled_person()
    {
        $data = $this->travel_order->mostTraveledPerson();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function most_traveled_vehicle()
    {
        $data = $this->travel_order->mostTraveledVehicle();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function most_traveled_destination()
    {
        $data = $this->travel_order->mostTraveledDestination();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_travel_order_list()
    {
        $data = $this->travel_order->getTravelOrderList();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_travel_order_details()
    {
        $data = $this->travel_order->getTravelOrderDetails();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function driver()
    {
        $data = $this->travel_order->driver();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_travel_order_archive_list()
    {
        $this->core_layout->setPrivilegeName("to_archive");
        $data = $this->travel_order->getArchiveLists();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_temp_personnel()
    {
        $user_id = $this->core_layout->getUserId();
        $data = $this->travel_order->getTempPersonnel($user_id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_temp_destination()
    {
        $user_id = $this->core_layout->getUserId();
        $data = $this->travel_order->getTempDestination($user_id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_personnel($id)
    {
        $data = $this->travel_order->getPersonnel($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_destination($id)
    {
        $data = $this->travel_order->getDestination($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function new_travel_order() {
        $externalUrl = "https://maps.googleapis.com/maps/api/js?key=" . $this->eformsKey . "&callback=initMapTemp&libraries=places,drawing&v=weekly";
        
        /*** service vehicle option for logistics and administrator role ***/
        $allowServiceVehicle = false;
        $empId = $this->core_layout->getCurrentEmployeeId();
        $temp = $this->core_layout->getEmployee($empId);
        $roleId = $this->authenticate->getRoleId();
        $isAllowed = trim(strtolower($temp->department_code)) === "logistics" || $roleId === 1;
        if(count((array) $temp) > 0 && ($isAllowed || $roleId == 1)){ $allowServiceVehicle = true; }
        $tempData = array("allow_service_vehicle"=> $allowServiceVehicle);
        /*** service vehicle option for logistics and administrator role ***/

		$arrData = array();
		$arrData["script_attribute"] = array("async");
		$this->core_layout->addExternalJs($externalUrl, true, $arrData);

        $this->core_layout->setPageTitle("Travel Order - New Travel Order");
        $this->core_layout->setPrivilegeName("to_masterfile");
        $this->core_layout->addJs("js/eforms/travel_order/new_travel_order.js", true, $tempData);
        // $this->core_layout->addJs("js/eforms/travel_order/travel_order_sites.js", true);

        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/travel_order/new_travel_order');
        $this->load->view('core/templates/footer');
        $user_id = $this->core_layout->getUserId();
        $this->travel_order->delete_temp_all_personnel($user_id);
        $this->travel_order->delete_temp_all_destination($user_id);
    }

    function view_travel_order()
    {
        $this->core_layout->setPageTitle("Travel Order - View Travel Order");
        $this->core_layout->setPrivilegeName("to_masterfile");
        $this->core_layout->addJs("js/eforms/travel_order/view_travel_order.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/travel_order/view_travel_order');
        $this->load->view('core/templates/footer');
    }

    function edit_travel_order() {
        $externalUrl = "https://maps.googleapis.com/maps/api/js?key=" . $this->eformsKey . "&callback=initMapTemp&libraries=places,drawing&v=weekly";
		$arrData = array();
		$arrData["script_attribute"] = array("async");
		$this->core_layout->addExternalJs($externalUrl, true, $arrData);
        
        $this->core_layout->setPageTitle("Travel Order - Edit Travel Order");
        $this->core_layout->setPrivilegeName("to_masterfile");
        $this->core_layout->addJs("js/eforms/travel_order/edit_travel_order.js", true);

        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);

        // $this->core_layout->addJs("js/eforms/travel_order/travel_order_sites.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/travel_order/edit_travel_order');
        $this->load->view('core/templates/footer');
    }

    function print_travel_order()
    {
        $this->core_layout->setPrivilegeName("to_masterfile");
        $this->core_layout->addJs("js/eforms/travel_order/print_travel_order.js", true);
        $this->load->view('eforms/travel_order/print_travel_order');
    }

    function print_travel_order_log()
    {
        $this->core_layout->setPrivilegeName("to_masterfile");
        $this->core_layout->addJs("js/eforms/travel_order/print_travel_order_log.js", true);
        $this->load->view('eforms/travel_order/print_travel_order_log');
    }

    function get_company_collection()
    {
        $data = $this->travel_order->getCompanyCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_department_collection()
    {
        $data = $this->travel_order->getDepartmentCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_departments(){
        $data = $this->travel_order->getAllDepartments();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_request_collection()
    {
        $this->core_layout->setPrivilegeName("to_masterfile");
        $data = $this->travel_order->getEmployeeCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_vehicle_collection(){
        $data = $this->travel_order->getVehicleCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_created(){
        $data = $this->travel_order->getCreated();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    function get_departing(){
        $data = $this->travel_order->getDeparting();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function ajax_vehicle_details($veh){
        $data = $this->travel_order->vehicle_details($veh);
        echo json_encode($data);
    }

    public function add_temp_personnel(){
        $check = $this->travel_order->check_employee($this->input->post('employee_id'));
        $user_id = $this->core_layout->getUserId();
        $data = array(
            'user_id' => $user_id,
            'employee_id' => $this->input->post('employee_id'),
        );

        if ($check) {
            echo json_encode(array("message" => "Employee is already on the list!"));
        } else {
            $insert = $this->travel_order->save_temp_personnel($data);
            if($insert){
                $this->core_layout->setEventLog("Add personnel ".$this->travel_order->getPersonnelName($this->input->post('employee_id'))." to travel order on temporary table.","add", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed add personnel ".$this->travel_order->getPersonnelName($this->input->post('employee_id'))." to travel order on temporary table.","add", "success", "gcceforms", "system");
            }
            echo json_encode(array("status" => TRUE));
        }
    }

    public function edit_temp_personnel($id)
    {
        $data = $this->travel_order->edit_temp_personnel($id);
        echo json_encode($data);
    }

    public function update_temp_personnel()
    {
        $check = $this->travel_order->check_employee($this->input->post('employee_id'));
        $data = array(
            'employee_id' => $this->input->post('employee_id')
            );
        if ($check) {
            echo json_encode(array("message" => "Employee is already on the list!"));
        } else {
            if($this->travel_order->update_temp_personnel(array('id' => $this->input->post('id_personnel')), $data)){
                $this->core_layout->setEventLog("Update personnel ".$this->travel_order->getPersonnelName($this->input->post('employee_id'))." to travel order on temporary table.","add", "success", "gcceforms", "user");
                echo json_encode(array("status" => TRUE));
            }else{
                $this->core_layout->setEventLog("Failed update personnel ".$this->travel_order->getPersonnelName($this->input->post('employee_id'))." to travel order on temporary table.","add", "error", "gcceforms", "system");
            }
        }
    }

    public function delete_temp_personnel($id)
    {
        $this->travel_order->delete_temp_personnel($id);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_temp_all_personnel()
    {
        $user_id = $this->core_layout->getUserId();
        $this->travel_order->delete_temp_all_personnel($user_id);
        echo json_encode(array("status" => TRUE));
    }

    public function add_personnel($id)
    {
        $check = $this->travel_order->check_employee2($id, $this->input->post('employee_id'));

        $data = array(
            'travel_order_id' => $id,
            'employee_id' => $this->input->post('employee_id')
        );

        if ($check) {
            echo json_encode(array("message" => "Employee is already on the list!"));
        } else {
            $insert = $this->travel_order->save_personnel($data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function edit_personnel($id)
    {
        $data = $this->travel_order->edit_personnel($id);
        echo json_encode($data);
    }

    public function update_personnel($id)
    {
        $check = $this->travel_order->check_employee2($id, $this->input->post('employee_id'));
        $data = array(
            'travel_order_id' => $id,
            'employee_id' => $this->input->post('employee_id')
        );

        if ($check) {
            echo json_encode(array("message" => "Employee is already on the list!"));
        } else {
            $this->travel_order->update_personnel(array('id' => $this->input->post('id_personnel')), $data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function delete_personnel($id)
    {
        $this->travel_order->delete_personnel($id);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_all_personnel($id)
    {
        $this->travel_order->delete_all_personnel($id);
        echo json_encode(array("status" => TRUE));
    }

    public function add_temp_destination()
    {
        $user_id = $this->core_layout->getUserId();
        $data = array(
            'user_id' => $user_id,
            'des_from' => $this->input->post('from'),
            'des_to' => $this->input->post('to'),
            'destination' => $this->input->post('from') . '-' . $this->input->post('to'),
            'requested_by' => $this->input->post('requested_by'),
            'purpose' => $this->input->post('purpose'),
            'date_from' => $this->input->post('date_from'),
            'date_to' => $this->input->post('date_to'),
            'instructions' => $this->input->post('special'),
            'remarks' => $this->input->post('remarks'),
            'travel_from' => $this->input->post('from'),
            'travel_to' => $this->input->post('to'),
            'coords_from' => $this->input->post('formTravelFrom'),
            'coords_to' => $this->input->post('formTravelTo'),
        );
        $insert = $this->travel_order->save_temp_destination($data);
        if($insert){
            $this->core_layout->setEventLog("Add destination ".$this->input->post('from') . '-' . $this->input->post('to')." in travel order on temporary table.","add", "success", "gcceforms", "user");
            echo json_encode(array("status" => TRUE));
        }else{
            $this->core_layout->setEventLog("Failed add destination ".$this->input->post('from') . '-' . $this->input->post('to')." in travel order on temporary table.","add", "error", "gcceforms", "system");
        }
    }

    public function edit_temp_destination($id)
    {
        $data = $this->travel_order->edit_temp_destination($id);
        echo json_encode($data);
    }

    public function update_temp_destination()
    {
        $data = array(
            'des_from' => $this->input->post('from'),
            'des_to' => $this->input->post('to'),
            'destination' => $this->input->post('from') . '-' . $this->input->post('to'),
            'requested_by' => $this->input->post('requested_by'),
            'purpose' => $this->input->post('purpose'),
            'date_from' => $this->input->post('date_from'),
            'date_to' => $this->input->post('date_to'),
            'instructions' => $this->input->post('special'),
            'remarks' => $this->input->post('remarks'),
            'travel_from' => $this->input->post('from'),
            'travel_to' => $this->input->post('to'),
            'coords_from' => $this->input->post('formTravelFrom'),
            'coords_to' => $this->input->post('formTravelTo'),
        );
        if($this->travel_order->update_temp_destination(array('id' => $this->input->post('id_destination')), $data)){
            $this->core_layout->setEventLog("Update destination ".$this->input->post('from') . '-' . $this->input->post('to')." in travel order on temporary table.","update", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Failed update destination ".$this->input->post('from') . '-' . $this->input->post('to')." in travel order on temporary table.","update", "error", "gcceforms", "system");
        }
        echo json_encode(array("status" => TRUE));
    }

    public function delete_temp_destination($id)
    {
        $this->travel_order->delete_temp_destination($id);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_temp_all_destination()
    {
        $user_id = $this->core_layout->getUserId();
        $this->travel_order->delete_temp_all_destination($user_id);
        echo json_encode(array("status" => TRUE));
    }

    public function add_destination($id)
    {

        $data = array(
            'travel_order_id' => $id,
            'des_from' => $this->input->post('from'),
            'des_to' => $this->input->post('to'),
            'destination' => $this->input->post('from') . '-' . $this->input->post('to'),
            'requested_by' => $this->input->post('requested_by'),
            'purpose' => $this->input->post('purpose'),
            'date_from' => $this->input->post('date_from'),
            'date_to' => $this->input->post('date_to'),
            'instructions' => $this->input->post('special'),
            'remarks' => $this->input->post('remarks'),
            'travel_from' => $this->input->post('from'),
            'travel_to' => $this->input->post('to'),
            'coords_from' => $this->input->post('formTravelFrom'),
            'coords_to' => $this->input->post('formTravelTo'),
        );
        $insert = $this->travel_order->save_destination($data);
        echo json_encode(array("status" => TRUE));
        
    }

    public function edit_destination($id)
    {
        $data = $this->travel_order->edit_destination($id);
        echo json_encode($data);
    }

    public function edit_destination_marker(){
        $data = $this->travel_order->edit_destination_marker();
        echo json_encode($data);
    }

    public function new_destination_marker(){
        $data = $this->travel_order->new_destination_marker();
        echo json_encode($data);
    }

    public function update_destination($id)
    {
        $data = array(
            'des_from' => $this->input->post('from'),
            'des_to' => $this->input->post('to'),
            'destination' => $this->input->post('from') . '-' . $this->input->post('to'),
            'requested_by' => $this->input->post('requested_by'),
            'purpose' => $this->input->post('purpose'),
            'date_from' => $this->input->post('date_from'),
            'date_to' => $this->input->post('date_to'),
            'instructions' => $this->input->post('special'),
            'remarks' => $this->input->post('remarks'),
            'travel_from' => $this->input->post('from'),
            'travel_to' => $this->input->post('to'),
            'coords_from' => $this->input->post('formTravelFrom'),
            'coords_to' => $this->input->post('formTravelTo'),
        );
        $this->travel_order->update_destination(array('id' => $this->input->post('id_destination')), $data, $id);
        echo json_encode(array("status" => TRUE));
    }

    public function update_status_destination($travel_order_id)
    {
        $data = array(
            'travel_order_status' => 1,
        );
        $this->travel_order->update_destination(array('travel_order_id' => $travel_order_id), $data);
    }

    public function delete_destination($id)
    {
        $this->travel_order->delete_destination($id);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_all_destination($id)
    {
        $this->travel_order->delete_all_destination($id);
        echo json_encode(array("status" => TRUE));
    }

    public function add_travel_order_v2(){
        $data = $this->travel_order->addTravelOrderV2();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function add_travel_order()
    {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $user_id = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        // /$user_id=$this->core_layout->getUserId();
        date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');
        $year = substr($date, 2, 2);
        $month = substr($date, 5, 2);
        $list = $this->travel_order->series($year, $month);
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
        $hitch = $this->input->post('is_hitch');
        $commute = $this->input->post('is_commute');
        $personal = $this->input->post('is_personal');
        $other = $this->input->post('is_other');
        $vehicle = $this->input->post('vehicle');
        $remarks = $this->input->post('remark');
        if ($service == true) {
            $service = "1";
            $hitch = "0";
            $personal = "0";
            $commute = "0";
            $other = "0";
            $remarks = "";
            $driver = $this->input->post('driver');
            $driver_name = $this->getDriver($driver);
        } else if ($hitch == true) {
            $service = "0";
            $hitch = "1";
            $personal = "0";
            $commute = "0";
            $other = "0";
            $remarks = "";
            $driver = $this->input->post('driver');
            $driver_name = $this->getDriver($driver);
        }else if ($commute == true) {
            $personal = "0";
            $hitch = "0";
            $service = "0";
            $commute = "1";
            $other = "0";
            $vehicle = "";
            $driver = "";
            $remarks = "";
            $driver_name = "";
        } else if ($personal == true) {
            $personal = "1";
            $commute = "0";
            $service = "0";
            $hitch = "0";
            $other = "0";
            $vehicle = "";
            $driver = "";
            $remarks = "";
            $driver_name = "";
        } else if ($other == true) {
            $service = "0";
            $hitch = "0";
            $commute = "0";
            $personal = "0";
            $other = "1";
            $vehicle = "";
            $driver = "";
            $driver_name = "";
        }

        $data = array(
            'ref_yr' => $year,
            'ref_series' => $series,
            'ref_month' => $month,
            'reference_no' => 'TO' . $year . '-' . $month . '-' . $series,
            'company' => $this->input->post('company_id'),
            'department' => $this->input->post('dep_id'),
            'station' => $this->input->post('station'),
            'origin' => $this->input->post('origin'),
            'type' => $this->input->post('type'),
            'is_hitch' => $hitch,
            'is_service' => $service,
            'is_commute' => $commute,
            'is_personal' => $personal,
            'is_others' => $other,
            'others_remarks' => $remarks,
            'vehicle_id' => $vehicle,
            'driver_id' => $driver,
            'driver' => $driver_name,
            'status' => 'Pending',
            'created_by' => $user_id,
            'created_dt' => $date,
            'created_id' => $user_id,

        );
        $insert = $this->travel_order->save_travel_order($data);
        $last_id = $this->db->insert_id();
        $list2 = $this->travel_order->get_temp_personnel($this->user_data['id']);
        $travel_personnel = "";
        foreach ($list2 as $arr) {
            $data = array(
                'travel_order_id' => $last_id,
                'employee_id' => $arr->employee_id

            );
            $insert = $this->travel_order->save_personnel($data);
            $travel_personnel[] .= chr(10)."- ".strtoupper($this->getDriver($arr->employee_id));
        }
        $this->travel_order->delete_temp_all_personnel($this->user_data['id']);
        $list3 = $this->travel_order->get_temp_destination($this->user_data['id']);
        $travel_details = "";
        foreach ($list3 as $arr2) {
            $data = array(
                'travel_order_id' => $last_id,
                'des_from' => $arr2->des_from,
                'des_to' => $arr2->des_to,
                'destination' => $arr2->destination,
                'requested_by' => $arr2->requested_by,
                'purpose' => $arr2->purpose,
                'date_from' => $arr2->date_from,
                'date_to' => $arr2->date_to,
                'instructions' => $arr2->instructions,
                'remarks' => $arr2->remarks,
                'travel_from' => $arr2->travel_from,
                'travel_to' => $arr2->travel_to,
                'coords_from' => $arr2->coords_from,
                'coords_to' => $arr2->coords_to,
            );
            $insert = $this->travel_order->save_destination($data);
            $tg_date_from = date_format(date_create($arr2->date_from),"m-d H:i");
            $tg_date_to = date_format(date_create($arr2->date_to),"m-d H:i");

            if($arr2->remarks == " "){
                $telegram_remarks = "";
            }else{
                $telegram_remarks = '<b>REMARKS: </b>'.strtoupper($arr2->remarks).chr(10);
            }


            
            $travel_details[] .= '<b>DESTINATION: </b>'.strtoupper($arr2->travel_from).' - '.strtoupper($arr2->travel_to).chr(10).'<b>DATE: </b>'.$tg_date_from.' - '.$tg_date_to.chr(10).$telegram_remarks.'<b>REQ BY: </b>'.strtoupper($this->getDriver($arr2->requested_by)).chr(10).'<b>PURPOSE: </b>'.strtoupper($arr2->purpose).chr(10).chr(10)."=";
        }
    
        if($insert){
            if($service == 1){
                $vehicle_name = $this->travel_order->vehicle_details($vehicle);
                $veh_name = $vehicle_name->name." | ".$vehicle_name->plateno;
                $vehicle_details = '<b>VEHICLE</b>: '.strtoupper($veh_name).chr(10).'<b>DRIVER</b>: '.strtoupper($driver_name).chr(10).chr(10);
            }
            if($hitch == 1){
                $vehicle_name = $this->travel_order->vehicle_details($vehicle);
                $veh_name = $vehicle_name->name." | ".$vehicle_name->plateno;
                $vehicle_details = '<b>Vehicle</b>: '.strtoupper($veh_name).chr(10).'<b>Driver</b>: '.strtoupper($driver_name).chr(10).chr(10);
            }
            if($commute == 1){
                $vehicle_details = '<b>VEHICLE</b>: COMMUTE'.chr(10);
            }
            if($personal == 1){
                $vehicle_details = '<b>VEHICLE</b>: PERSONAL VEHICLE'.chr(10);
            }
            if($other == 1){
                if($remarks == ""){
                    $vehicle_details = "".chr(10);
                }else{
                    $vehicle_details = '<b>REMARKS</b>: '.strtoupper($remarks).chr(10).chr(10);
                }
            }
            
            $td = implode("=",$travel_details);
            $tp = implode(" ",$travel_personnel);
            // $telegram_msg = '';
            // $telegram_msg .= '<b>TO #</b>: '.'TO' . $year . '-' . $month . '-' . $series .chr(10);
            // $telegram_msg .= '<b>FILE: </b>'.strtoupper($this->input->post('company_id')).chr(10);
            // $telegram_msg .= '<b>ORIGIN</b>: '.strtoupper($this->input->post('origin')).chr(10);
            // $telegram_msg .= '<b>PREP BY: </b>'.strtoupper($user_id).chr(10);
            // $telegram_msg .= '<b>PERSONNEL: </b>'.$tp.chr(10);
            // $telegram_msg .= $vehicle_details;
            // $telegram_msg .= str_replace("=","",$td);
            // if($this->travel_order->telegram_config_if_exist('travel_order', 'count') > 0){
            //     $this->travel_order->telegram($telegram_msg);

            //     foreach($list2 as $send_to_depthead){
            //         $head_id = $this->travel_order->getTelegramId($send_to_depthead->employee_id);
            //         if($head_id != 2){
            //             $this->travel_order->telegram_dept_heads($telegram_msg,$head_id);
            //         }
            //     }
            // }
            $this->core_layout->setEventLog("Add travel order.","add", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Failed adding travel order.","add", "error", "gcceforms", "system");
        }
        $this->travel_order->delete_temp_all_destination($this->user_data['id']);
        echo json_encode(array("status" => TRUE));
    }

    public function test_function(){
        $data = $this->travel_order->testFunction();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function update_travel_order_v2($id){
        $data = $this->travel_order->updateTravelOrderV2();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function update_travel_order($id)
    {
        date_default_timezone_set('Asia/Singapore');
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $user_id = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        $date = date('Y-m-d H:i:s');
        $service = $this->input->post('is_service');
        $hitch = $this->input->post('is_hitch');
        $commute = $this->input->post('is_commute');
        $personal = $this->input->post('is_personal');
        $other = $this->input->post('is_other');
        $vehicle = $this->input->post('vehicle');
        $remarks = $this->input->post('remark');
        if ($service == 1) {
            $service = "1";
            $hitch = "0";
            $personal = "0";
            $commute = "0";
            $other = "0";
            $remarks = "";
            $driver = $this->input->post('driver');
            $driver_name = $this->getDriver($driver);
        }else if ($hitch == 1) {
            $service = "0";
            $hitch = "1";
            $personal = "0";
            $commute = "0";
            $other = "0";
            $remarks = "";
            $driver = $this->input->post('driver');
            $driver_name = $this->getDriver($driver);
        } else if ($commute == 1) {
            $personal = "0";
            $service = "0";
            $hitch = "0";
            $commute = "1";
            $other = "0";
            $vehicle = "";
            $driver = "";
            $driver_name = "";
            $remarks = "";
        } else if ($personal == 1) {
            $personal = "1";
            $commute = "0";
            $service = "0";
            $hitch = "0";
            $other = "0";
            $vehicle = "";
            $driver = "";
            $driver_name = "";
            $remarks = "";
        } else if ($other == 1) {
            $service = "0";
            $hitch = "0";
            $commute = "0";
            $personal = "0";
            $other = "1";
            $vehicle = "";
            $driver = "";
            $driver_name = "";
        }

        $data = array(
            'company' => $this->input->post('company_id'),
            'department' => $this->input->post('dep_id'),
            'station' => $this->input->post('station'),
            'origin' => $this->input->post('origin'),
            'type' => $this->input->post('type'),
            'is_hitch' => $hitch,
            'is_service' => $service,
            'is_commute' => $commute,
            'is_personal' => $personal,
            'is_others' => $other,
            'others_remarks' => $remarks,
            'vehicle_id' => $vehicle,
            'driver_id' => $driver,
            'driver' => $driver_name,
            'last_edited_by' => $user_id,
            'last_edited_dt' => $date,

        );

        $reference_no = $this->db->get_where("gcceforms.travel_order", array("id"=>$id))->row('reference_no');
        if($this->travel_order->update_travel_order(array('id' => $id), $data)){
            $this->core_layout->setEventLog("Updated ".$reference_no.".","update", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Failed updating ".$reference_no.".","update", "error", "gcceforms", "system");
        }
        echo json_encode(array("status" => TRUE));


    }

    function getDriver($id)
    {
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

    public function ajax_travel_order_details($id)
    {
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $data = $this->travel_order->travel_order_details($id)['data'];
        if ($data->vehicle_id == "0") {
            $plateno = "";
        } else {
            $vehicle_data = $this->travel_order->vehicle_details($data->vehicle_id);
            $gen_code = $vehicle_data->gen_code;
            $plateno = $gen_code . " | " . $vehicle_data->plateno . " | " . $vehicle_data->description;
        }
        $company_desc = $data->company;
        $department_desc = $data->department;
        

        echo json_encode(array("data" => $data, "plateno" => $plateno, "check" => $check, "company_desc" => $company_desc, "department_desc" => $department_desc));
    }

    public function ajax_travel_order_details2($id){
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $data = $this->travel_order->travel_order_details($id)['data'];
        $personnel = $this->travel_order->travel_order_details($id)['personnel'];
        $destination = $this->travel_order->travel_order_details($id)['destination'];
        if ($data->vehicle_id == "0") {
            $plateno = "";
        } else {
            if (is_numeric($data->vehicle_id)) {
                $vehicle_data = $this->travel_order->vehicle_details($data->vehicle_id);
                $gen_code = $vehicle_data->gen_code;
                $plateno = $gen_code . " | " . $vehicle_data->plateno . " | " . $vehicle_data->name;
            } else {
                $plateno = $data->vehicle_id;
            }
        }
        echo json_encode(array("data" => $data, "plateno" => $plateno, "check" => $check, "destination"=>$destination, "personnel"=>$personnel));
    }


    public function ajax_travel_order_print($id){
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $data = $this->travel_order->travel_order_details($id);
        if ($data->vehicle_id == "0") {
            $plateno = "";
        } else {
            if (is_numeric($data->vehicle_id)) {
                $vehicle_data = $this->travel_order->vehicle_details($data->vehicle_id);
                $plateno = $vehicle_data->plateno;
            } else {
                $plateno = $data->vehicle_id;
            }
        }
        echo json_encode(array("data" => $data, "plateno" => $plateno, "check" => $check));
    }

    function getReferenceNo($id){
        return $this->db->get_where("gcceforms.travel_order", array("id"=>$id))->row('reference_no');
    }

    public function approve_travel_v2(){
        $data = $this->travel_order->approveTravelV2();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function approve_recommend_travel_v2(){
        $data = $this->travel_order->approveRecommendTravelV2();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function approve_travel($id){
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $user_id = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');
        $data = array(
            'status' => 'Approved',
            'approved_remarks' => $this->input->post('approve_remarks'),
            'approved_by' => $user_id,
            'approved_dt' => $date,
        );
        $post = $this->travel_order->update_travel_order(array('id' => $id), $data);
        if ($post) {
            $message = "View Travel Order - Approve travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
            echo json_encode(array("status" => TRUE));
        }else{
            $message = "View Travel Order - Failed approving travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, $type, $action, "gcceforms", $table);
    }

    public function undo_approve_travel($id)
    {
        $data = array(
            'status' => 'Recommend_Approved',
            'approved_remarks' => '',
            'approved_by' => '',
            'approved_dt' => '',
        );

        if($this->travel_order->update_travel_order(array('id' => $id), $data)){
            $message = "View Travel Order - Undo approve travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
        }else{
            $message = "View Travel Order - Failed undo approve travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "system";
        }

        $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function undo_approve_recommend_travel($id)
    {
        $data = array(
            'status' => 'Pending',
            'approved_recommend_remarks' => '',
            'approved_recommend_by' => '',
            'approved_recommend_by_id' => null,
            'approved_recommend_date' => '',
        );

        if($this->travel_order->update_travel_order(array('id' => $id), $data)){
            $message = "View Travel Order - Undo recommend approve travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
        }else{
            $message = "View Travel Order - Failed undo recommend approve travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "system";
        }

        $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function disapprove_travel($id)
    {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $user_id = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');
        $data = array(
            'status' => 'Disapproved',
            'disapproved_remarks' => $this->input->post('disapproved_remarks'),
            'disapproved_by' => $user_id,
            'disapproved_dt' => $date,
        );

        if($this->travel_order->update_travel_order(array('id' => $id), $data)){
            $message = "View Travel Order - Disapprove travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
        }else{
            $message = "View Travel Order - Failed disapprove travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function undo_disapprove_travel($id)
    {
        $data = array(
            'status' => 'Pending',
            'disapproved_remarks' => '',
            'disapproved_by' => '',
            'disapproved_dt' => '',
        );
        if($this->travel_order->update_travel_order(array('id' => $id), $data)){
            $message = "View Travel Order - Undo disapprove travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
        }else{
            $message = "View Travel Order - Failed undo disapprove travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function accomplish_travel_v2(){
        $data = $this->travel_order->accomplishTravelV2();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function accomplish_travel($id="")
    {
        if ($id=="") { $id = $this->input->post('id'); }
        $data = array(
            'accomplishment_dt' => $this->input->post('accomplishment_dt'),
            'accomplishment_remarks' => $this->input->post('accomplishment_remarks'),
            'accomplished' => 1,
        );
        $post = $this->travel_order->update_travel_order(array('id' => $id), $data);
        if ($post) {
            $message = "View Travel Order - Accomplish travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
            $this->update_status_destination($id);
            echo json_encode(array("status" => TRUE,"data"=>array("id"=>$id,  "remarks"=> $this->input->post('accomplishment_remarks')),"reps"=>"success") );
        }else{
            $message = "View Travel Order - Failed accomplish travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "user";
        }
        $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
    }

    public function undo_accomplish_travel_v2(){
        $data = $this->travel_order->undoAccomplishTravelV2();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function undo_accomplish_travel($id)
    {
        $data = array(
            'accomplishment_dt' => '0000-00-00 00:00:00',
            'accomplishment_remarks' => '',
            'accomplished' => 0,
        );
        if($this->travel_order->update_travel_order(array('id' => $id), $data)) {
            $message = "View Travel Order - Undo accomplish travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
        }else{
            $message = "View Travel Order - Failed undo accomplish travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "user";
        }
        $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function note_travel($id)
    {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $user_id = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');
        $data = array(
            'status' => 'HR Noted',
            'hr_noted_remarks' => $this->input->post('hr_noted_remarks'),
            'hr_noted_by' => $user_id,
            'hr_noted_dt' => $date,
        );
        if($this->travel_order->update_travel_order(array('id' => $id), $data)) {
            $message = "View Travel Order - Note travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
        }else{
            $message = "View Travel Order - Failed note travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "user";
        }
        $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
        }

    public function undo_note_travel($id)
    {
        $data = array(
            'status' => 'Approved',
            'hr_noted_remarks' => '',
            'hr_noted_by' => '',
            'hr_noted_dt' => '',
        );
        if($this->travel_order->update_travel_order(array('id' => $id), $data)) {
            $message = "View Travel Order - Undo note travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
        }else{
            $message = "View Travel Order - Failed undo note travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "user";
        }
        $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function cancel_travel($id)
    {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $user_id = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');
        $data = array(
            'status' => 'Cancelled',
            'cancelled_by' => $user_id,
            'cancelled_dt' => $date,
            'cancelled_remarks' => $this->input->post('cancelled_remarks'),
        );
        if($this->travel_order->update_travel_order(array('id' => $id), $data)) {
            $message = "View Travel Order - Cancel travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
        }else{
            $message = "View Travel Order - Failed cancel travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "user";
        }
        $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function undo_cancel_travel($id)
    {
        $data = array(
            'status' => 'Pending',
            'cancelled_by' => '',
            'cancelled_dt' => '',
            'cancelled_remarks' => '',
        );
        if($this->travel_order->update_travel_order(array('id' => $id), $data)) {
            $message = "View Travel Order - Undo cancel travel order ".$this->getReferenceNo($id).".";
            $type = "success";
            $action = "update";
            $table = "user";
        }else{
            $message = "View Travel Order - Failed undo cancel travel order ".$this->getReferenceNo($id).".";
            $type = "error";
            $action = "update";
            $table = "user";
        }
        $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_view_personnels($id)
    {

        $list = $this->travel_order->get_personnel($id);
        $data = array();
        foreach ($list as $arr) {
            $emp = $this->travel_order->emp_details($arr->employee_id);
            $row = array();
            $row[] = $emp->firstname . ' ' . substr($emp->middlename, 0, 1) . '. ' . $emp->lastname;
            $row[] = $emp->position;
            $data[] = $row;
        }
        $output = array("data" => $data);

        echo json_encode($output);
    }

    public function ajax_view_destinations_print($id)
    {

        $list = $this->travel_order->get_destination($id);
        $data = array();
        date_default_timezone_set('Asia/Singapore');
        foreach ($list as $arr) {
            $a = '';
            $row = array();
            $x = explode(' ', $arr->date_from);
            $y = explode(' ', $arr->date_to);
            $a = '<b>' . $arr->destination . '</b>';
            if ($arr->purpose != "" || $arr->purpose != null) {
                $a .= '<br>' . $arr->purpose;
            }
            if ($arr->requested_by != "" || $arr->requested_by != null) {

                if (filter_var($arr->requested_by, FILTER_VALIDATE_INT)) {
                    $getEmp = $this->crud->load(array("id" => $arr->requested_by), "gccmaster.tblemployees");

                    if ($getEmp["suffix"] == "" || $getEmp["suffix"] == "N/A" || $getEmp["suffix"] == "NONE" || $getEmp["suffix"] == NULL) {
                        $a .= '<br>Requested by: ' . $getEmp["firstname"] . ' ' . substr($getEmp["middlename"], 0, 1) . '. ' . $getEmp["lastname"];
                    } else {
                        $a .= '<br>Requested by: ' . $getEmp["firstname"] . ' ' . substr($getEmp["middlename"], 0, 1) . '. ' . $getEmp["lastname"] . ' ' . $getEmp["suffix"];
                    }
                } else {
                    $a .= '<br>Requested by: ' . $arr->requested_by;
                }
            }
            if ($arr->remarks != "" || $arr->remarks != null) {
                $a .= '<br>Remarks: ' . $arr->remarks;
            }
            if ($arr->instructions != "" || $arr->instructions != null) {
                $a .= '<br>Special Instruction: ' . $arr->instructions;
            }
            if ($arr->date_special != "0000-00-00 00:00:00") {
                $a .= ' - ' . date("M d, Y g:i A", strtotime($arr->date_special));
            }
            $row[] = $a;
            if ($x[0] == $y[0]) {
                $row[] = date("M d, Y", strtotime($x[0])) . ' ' . date("g:i A", strtotime($x[1])) . ' - ' . date("g:i A", strtotime($y[1]));
            } else {
                $row[] = date("M d, Y", strtotime($x[0])) . ' ' . date("g:i A", strtotime($x[1])) . ' - ' . date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
            }
            $row[] = date("M d, Y", strtotime($x[0])) . ' ' . date("g:i A", strtotime($x[1]));
            $row[] = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
            $data[] = $row;
        }
        $output = array("data" => $data,);

        echo json_encode($output);
    }

    public function ajax_view_destinations_print_log($id)
    {
        $list = $this->travel_order->get_destination($id);
        $data = array();
        date_default_timezone_set('Asia/Singapore');
        foreach ($list as $arr) {
            $row = array();
            $x = explode(' ', $arr->date_from);
            $y = explode(' ', $arr->date_to);
            $z = explode(' ', $arr->date_special);
            $row[] = $arr->destination;
            if ($x[0] == $y[0]) {
                if ($x[0] == $y[0]) {
                    $row[] = date("M d, Y", strtotime($x[0])) . ' ' . date("g:i A", strtotime($x[1])) . ' - ' . date("g:i A", strtotime($y[1]));
                } else {
                    $row[] = date("M d, Y", strtotime($x[0])) . ' ' . date("g:i A", strtotime($x[1])) . ' - ' . date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                }
            } else {
                if ($x[0] == $y[0]) {
                    $row[] = date("M d, Y", strtotime($x[0])) . ' ' . date("g:i A", strtotime($x[1])) . ' - ' . date("g:i A", strtotime($y[1]));
                } else {
                    $row[] = date("M d, Y", strtotime($x[0])) . ' ' . date("g:i A", strtotime($x[1])) . ' - ' . date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                }
            }
            $data[] = $row;
        }
        $output = array("data" => $data,);
        echo json_encode($output);
    }

    public function get_travel_analytics_for_dashboard()
    {
        $data = $this->travel_order->m_get_travel_analytics_for_dashboard();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    public function confirm_travel_destination(){
        $data = $this->travel_order->confirmTravelDestination();
        echo json_encode($data);
    }

    public function accomplishment_button(){
        $data = $this->travel_order->confirmTravelDestination();
        echo json_encode($data);
    }

    public function destination_status(){
        $data = $this->travel_order->destinationStatus();
        echo json_encode($data);
    }

    public function verify_TO_status(){
        $data = $this->travel_order->verifyTOStatus();
        echo json_encode($data);
    }

    public function set_all_status(){
        $data = $this->travel_order->setAllStatus();
        echo json_encode($data);
    }

    public function sites_options(){
        $data = $this->travel_order->sitesOption();
        echo json_encode($data);
    }

    public function site_selected(){
        $data = $this->travel_order->sitesOption();
        echo json_encode($data);
    }

    public function test_telegram(){
        $data = $this->travel_order->telegram();
        echo json_encode($data);
    }

    public function add_telegram_config(){
        $data = $this->travel_order->addTelegramConfig();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    public function load_telegram_config(){
        $data = $this->travel_order->loadTelegramConfig();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function export_event_log($export){
        $data = $this->travel_order->exportData($export);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function export_event_log_archive($export){
        $data = $this->travel_order->exportDataArchive($export);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function travelorder_email($state = false){
        $result = array();
        $data = $this->travel_order->get_today_to();
        $result['result'] = $data;

        if($state){
            $sent = $this->travel_order->send_to_email($result);
            if($sent){
                return 'email sent!';
            }else{
                return 'email not sent!';
            }

        }else{
            $this->load->view('eforms/email_templates/email_daily_travelorder_template', $result);
        }
    }

    function get_travel_order_temp_personnel_list(){
        $data = $this->travel_order->getTravelOrderTempPersonnelList();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function remove_temporary_employee_data(){
        $data = $this->travel_order->removeTemporaryEmployeeData();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function report(){
        $this->core_layout->setPageTitle("Travel Order - Accomplishments Report");
        $this->core_layout->setPrivilegeName("to_accomplishment_report");
        
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        
        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', TRUE);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', TRUE);
        
        $this->core_layout->addJs("js/eforms/travel_order/accomplishment_report.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/travel_order/report');
        $this->load->view('core/templates/footer');
    }

    function get_accomplishment_report(){
        $data = $this->travel_order->getAccomplishmentReport();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function get_approved_chart(){
        $data = $this->travel_order->getApprovedChartData();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

}