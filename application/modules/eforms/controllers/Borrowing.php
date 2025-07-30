<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Borrowing extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authenticate->setModuleAccess("eforms-borrowing");
        $this->authenticate->doRedirect();
        $this->core_layout->setPrivilegeName("eforms_borrowing");
        $this->user_data = $this->session->userdata("logged_in");
        $this->load->model("Borrowing_m", "borrowing");
    }

    public function index()
    {
        $this->core_layout->addJs("global/js/amcharts4/core.js", true);
        $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
        $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
        $this->core_layout->addJs("js/eforms/borrowing/dashboard.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/borrowing/dashboard');
        $this->load->view('core/templates/footer');
    }

    public function masterfile()
    {
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        
        $this->core_layout->setPageTitle("Borrowing - Masterfile");
        $this->core_layout->setPrivilegeName("borr_masterlist");
        $this->core_layout->addJs("js/eforms/borrowing/borrowing.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/borrowing/index');
        $this->load->view('core/templates/footer');
        $user_id = $this->core_layout->getUserId();
        $this->borrowing->delete_temp_all($user_id);
    }

    public function archive_borrowing()
    {
        $this->core_layout->setPageTitle("Borrowing - Archive");
        $this->core_layout->setPrivilegeName("borr_archive");
        $this->core_layout->addJs("js/eforms/borrowing/archive_borrowing.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/borrowing/archive_borrowing');
        $this->load->view('core/templates/footer');
    }

    public function borrowed_borrowing()
    {
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

        $this->core_layout->setPageTitle("Borrowing - Borrowed Items");
        $this->core_layout->setPrivilegeName("borr_borrowed");
        $this->core_layout->addJs("js/eforms/borrowing/borrowed_borrowing.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/borrowing/borrowed_borrowing');
        $this->load->view('core/templates/footer');
    }

    public function return_borrowing()
    {
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        
        $this->core_layout->setPageTitle("Borrowing - Returned Items");
        $this->core_layout->setPrivilegeName("borr_return");
        $this->core_layout->addJs("js/eforms/borrowing/return_borrowing.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/borrowing/return_borrowing');
        $this->load->view('core/templates/footer');
    }

    public function overdue_borrowing()
    {
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

        $this->core_layout->setPageTitle("Borrowing - Overdued Items");
        $this->core_layout->setPrivilegeName("borr_overdue");
        $this->core_layout->addJs("js/eforms/borrowing/overdue_borrowing.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/borrowing/overdue_borrowing');
        $this->load->view('core/templates/footer');
    }

    function get_datatable_request()
    {
        $this->core_layout->setPrivilegeName("borr_masterlist");
        $data = $this->borrowing->getDatatableRequest();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_archive_request()
    {
        $data = $this->borrowing->getArchiveRequest();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_borrowed_request()
    {
        $this->core_layout->setPrivilegeName("borr_borrowed");
        $data = $this->borrowing->getBorrowedRequest();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_return_request()
    {
        $this->core_layout->setPrivilegeName("borr_return");
        $data = $this->borrowing->getReturnedRequest();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_overdue_request()
    {
        $this->core_layout->setPrivilegeName("borr_overdue");
        $data = $this->borrowing->getOverdueRequest();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_daily()
    {
        $data = $this->borrowing->getDaily();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_weekly()
    {
        $data = $this->borrowing->getWeekly();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function new_borrowing()
    {
        $this->core_layout->setPrivilegeName("borr_masterlist");
        $this->core_layout->addJs("js/eforms/borrowing/new_borrowing.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/borrowing/new_borrowing');
        $this->load->view('core/templates/footer');
        $user_id = $this->core_layout->getUserId();
        $this->borrowing->delete_temp_all($user_id);
    }

    function edit_borrowing()
    {
        $this->core_layout->setPrivilegeName("borr_masterlist");
        $this->core_layout->addJs("js/eforms/borrowing/edit_borrowing.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/borrowing/edit_borrowing');
        $this->load->view('core/templates/footer');
    }

    function view_borrowing(){
        $tempData = array();
        
        $this->core_layout->setPrivilegeName("borr_masterlist");
        $this->core_layout->addJs("js/eforms/borrowing/view_borrowing.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/borrowing/view_borrowing');
        $this->load->view('core/templates/footer');
    }

    function print_borrowing()
    {
        $this->core_layout->setPrivilegeName("borr_masterlist");
        $this->core_layout->addJs("js/eforms/borrowing/print_borrowing.js", true);
        $this->load->view('eforms/borrowing/print_borrowing');
    }

    function get_borrower_collection()
    {
        $data = $this->borrowing->getEmployeeCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_asset_collection()
    {
        $data = $this->borrowing->getAssetCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_vehicle_collection()
    {
        $data = $this->borrowing->getVehicleCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_temp_request()
    {
        $user_id = $this->core_layout->getUserId();
        $data = $this->borrowing->getTempRequest($user_id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_content_request($id)
    {

        $data = $this->borrowing->getContentRequest($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function delete_temp_content($id)
    {
        $this->borrowing->delete_temp($id);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_temp_all_content()
    {
        $user_id = $this->core_layout->getUserId();
        $this->borrowing->delete_temp_all($user_id);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_content($id)
    {
        $this->borrowing->delete_content($id);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_all_content($id)
    {
        $this->borrowing->delete_content_all($id);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_vehicle_details($veh)
    {
        $data = $this->borrowing->vehicle_details($veh);
        echo json_encode($data);
    }

    public function ajax_asset_details($asset)
    {
        $data = $this->borrowing->asset_details($asset);
        echo json_encode($data);
    }

    public function ajax_emp_details($emp)
    {

        $data = $this->borrowing->emp_details($emp);
        echo json_encode($data);
    }

    public function ajax_borrowing_details($id)
    {
        $temp = strtotime("-1 year", time());
        $check = date("Y-m-d", $temp);
        $data = $this->borrowing->borrowing_details($id);
        $body = $this->borrowing->getContentRequest($id);
        echo json_encode(array("data" => $data, "data_body" => $body, "name" => $data->borrower, "check" => $check));
    }

    public function add_temp_content()
    {
        $vehicle = 0;
        $code = null;
        $asset_id = 0;
        $tempQty = 1;

        if ($this->input->post('type') == "asset") {
            $asset_id = $this->input->post('asset');
            $code = $this->input->post('code');
            $asset_name = $this->input->post('name');
        }
        if ($this->input->post('type') == "sample") {
            $code = $this->input->post('sample');
            $asset_name = $this->input->post('desc');
            $tempQty = $this->input->post("quantity");
        }
        if ($this->input->post('type') == "vehicle") {
            $asset_id = $this->input->post('vehicle');
            $code = $this->input->post('code');
            $asset_name = $this->input->post('name');
            $vehicle = 1;
        }

        $tempUnit = (floatval($tempQty) > 1)? "PCS": "PC";
        $user_id = $this->core_layout->getUserId();

        $data = array(
            'user_id' => $user_id,
            'asset_id' => $asset_id,
            'asset_code' => $code,
            'quantity' => $tempQty,
            'type' => $this->input->post('type'),
            'uom' => $tempUnit,
            'asset_name' => $asset_name,
            'date_borrowed' => date("Y-m-d H:i:s", strtotime($this->input->post('borrowed_dt'), time())),
            'date_due' => date("Y-m-d H:i:s", strtotime($this->input->post('due_dt'), time())),
            'remarks' => $this->input->post('remarks'),
            'date_returned' => '',
            'is_returned' => '',
            'is_overdue' => $vehicle,
        );

        $insert = $this->borrowing->save_temp_content($data);
        if($insert){
            if($this->input->post('type') == 'asset'){
                $message = "New Borrowing - Add asset {$code} for borrowing.";
            }elseif($this->input->post('type') == 'vehicle'){
                $message = "New Borrowing - Add vehicle {$code} for borrowing {$code}.";
            }else{
                $message = "New Borrowing - Add sample {$code} item for borrowing {$code}.";
            }
            $type = "success";
            $table = "user";
            $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
        }else{
            $message = "New Borrowing - Failed add {$this->input->post('type')} {$code} for borrowing.";
        }
        echo json_encode(array("status" => TRUE));
    }

    public function update_temp_content()
    {
        $vehicle = 0;
        $code = null;
        $asset_id = 0;
        $tempQty = 1;

        if ($this->input->post('type') == "asset") {
            $asset_id = $this->input->post('asset');
            $code = $this->input->post('code');
            $asset_name = $this->input->post('name');
        }
        if ($this->input->post('type') == "sample") {
            $code = $this->input->post('sample');
            $asset_name = $this->input->post('desc');
            $tempQty = $this->input->post('quantity');

        }
        if ($this->input->post('type') == "vehicle") {
            $asset_name = $this->input->post('name');
            $asset_id = $this->input->post('vehicle');
            $code = $this->input->post('code');
            $vehicle = 1;
        }

        $tempUnit = (floatval($tempQty) > 1)? "PCS": "PC";

        $user_id = $this->core_layout->getUserId();
        $data = array(
            'user_id' => $user_id,
            'asset_id' => $asset_id,
            'asset_code' => $code,
            'quantity' => $tempQty,
            'type' => $this->input->post('type'),
            'uom' => $tempUnit,
            'asset_name' => $asset_name,
            'date_borrowed' => date("Y-m-d H:i:s", strtotime($this->input->post('borrowed_dt'), time())),
            'date_due' => date("Y-m-d H:i:s", strtotime($this->input->post('due_dt'), time())),
            'remarks' => $this->input->post('remarks'),
            'date_returned' => '',
            'is_returned' => '',
            'is_overdue' => $vehicle,
        );
        $this->borrowing->update_temp(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function add_content($id){
        $vehicle = 0;
        $code = null;
        $asset_id = 0;
        $tempQty = 1;

        if ($this->input->post('type') == "asset") {
            $asset_name = $this->input->post('name');
            $asset_id = $this->input->post('asset');
            $code = $this->input->post('code');
        }
        if ($this->input->post('type') == "sample") {
            $code = $this->input->post('sample');
            $asset_name = $this->input->post('desc');
            $tempQty = $this->input->post('quantity');
        }
        if ($this->input->post('type') == "vehicle") {
            $asset_name = $this->input->post('name');
            $asset_id = $this->input->post('vehicle');
            $code = $this->input->post('code');
            $vehicle = 1;
        }

        $tempUnit = (floatval($tempQty) > 1)? "PCS": "PC";

        $tempType = $this->input->post('type');
        $user_id = $this->core_layout->getUserId();
        $data = array(
            'borrowing_id' => $id,
            'asset_id' => $asset_id,
            'asset_code' => $code,
            'quantity' => $tempQty,
            //'price' => $this->input->post('price'),
            'type' => $this->input->post('type'),
            'uom' => $tempUnit,
            'asset_name' => $asset_name,
            'date_borrowed' => date("Y-m-d H:i:s", strtotime($this->input->post('borrowed_dt'), time())),
            'date_due' => date("Y-m-d H:i:s", strtotime($this->input->post('due_dt'), time())),
            'remarks' => $this->input->post('remarks'),
            'date_returned' => '',
            'is_returned' => '',
            'is_overdue' => $vehicle,
        );
        $insert = $this->borrowing->save_content($data);
        if($insert){
            if($insert && $asset_id){
                if($tempType == "asset"){
                    $this->db->update("gccasset.assets", array("is_borrowed"=>1), array("id"=>$asset_id));
                }elseif($tempType == "vehicle_component" || $tempType == "vehicle"){
                    $this->db->update("gccasset.vehicles", array("is_borrowed"=>1), array("id"=>$asset_id));
                }
            }
        }
        echo json_encode(array("status" => TRUE));
    }

    public function update_content(){
        $resultset = array();
        $tempWhere = array();
        $post = $this->input->post();
        $tempWhere["id"] = $post["id"];
        $checkItemExist = $this->db->get_where("gcceforms.borrowing_body", $tempWhere);
        if($checkItemExist->num_rows() == 1){
            $tempRow = $checkItemExist->row();
            $tempType = $post["type"];
            $vehicle = 0;
            $code = null;
            $asset_id = 0;
            $assname = "";
            $tempQty = 1;

            if ($post["type"] == "asset") {
                $asset_id = $post["asset"];
                $code = $post["code"];
                $assname = $post["name"];
            }
            if ($post["type"] == "sample") {
                $code = $post["sample"];
                $assname = $post["desc"];
                $tempQty = $post["quantity"];
            }
            if ($post["type"] == "vehicle") {
                $asset_id = $post["vehicle"];
                $code = $post["code"];
                $assname = $post["name"];
                $vehicle = 1;
            }
    
            $tempUnit = (floatval($tempQty) > 1)? "PCS": "PC";

            $user_id = $this->core_layout->getUserId();
            $data = array(
                'asset_id' => $asset_id,
                'asset_code' => $code,
                'quantity' => $tempQty,
                'type' => $post["type"],
                'uom' => $tempUnit,
                'asset_name' => $assname,
                'date_borrowed' => date("Y-m-d H:i:s", strtotime($post["borrowed_dt"])),
                'date_due' => date("Y-m-d H:i:s", strtotime($post["due_dt"])),
                'remarks' => $post["remarks"],
                'date_returned' => '',
                'is_returned' => '',
                'is_overdue' => $vehicle,
            );
    
            $updated = $this->borrowing->update_content($tempWhere, $data);
            if($updated){
                $resultset["status"] = true;
            }elseif($asset_id){
                if($tempRow->type == "asset"){
                    $this->db->update("gccasset.assets", array("is_borrowed"=>0), array("id"=>$tempRow->asset_id));
                }elseif($tempRow->type == "vehicle_component" || $tempRow->type == "vehicle"){
                    $this->db->update("gccasset.vehicles", array("is_borrowed"=>0), array("id"=>$tempRow->asset_id));
                }

                if($tempType == "asset"){
                    $this->db->update("gccasset.assets", array("is_borrowed"=>1), array("id"=>$asset_id));
                }elseif($tempType == "vehicle_component" || $tempType == "vehicle"){
                    $this->db->update("gccasset.vehicles", array("is_borrowed"=>1), array("id"=>$asset_id));
                }
                $resultset["status"] = true;
            }else{
                $resultset["status"] = false;
                $resultset["updated"] = "Borrowed item has beed updated.";
            }
        }else{
            $resultset["status"] = false;
        }
        echo json_encode($resultset);
    }

    public function edit_temp_content($id)
    {
        $data = $this->borrowing->edit_temp($id);
        echo json_encode($data);
    }

    public function edit_content($id)
    {
        $data = $this->borrowing->edit_content($id);
        echo json_encode($data);
    }

    public function add_borrowing()
    {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $user_id = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        $user_ids = $this->core_layout->getUserId();
        date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');
        $year = substr($date, 2, 2);
        $month = substr($date, 5, 2);
        $list = $this->borrowing->series($year, $month);
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
        $x = explode("\n", $this->input->post('description'));
        $data = array(
            'ref_yr' => $year,
            'ref_series' => $series,
            'ref_month' => $month,
            'reference_no' => 'BF' . $year . '-' . $month . '-' . $series,
            'company' => $x[0],
            'department' => $x[1],
            'position' => $x[2],
            'borrower' => $this->input->post('borrower'),
            'date_trans' => date("Y-m-d h:i:s", strtotime($this->input->post('trans_date'))),
            'date_needed' => date("Y-m-d", strtotime($this->input->post('need_dt'))),
            'status' => 'Pending',
            'purpose' => $this->input->post('purpose'),
            'created_by' => $user_id,
            'created_dt' => $date,
            'created_id' => $user_id,
            /*'last_edited_by' => $session_data['firstname'].' '.$session_data['lastname'],
            'last_edited_dt' => $date,*/
        );
        $insert = $this->borrowing->save($data);
        $last_id = $this->db->insert_id();

        $list3 = $this->borrowing->get_contents($user_ids);
        foreach ($list3 as $arr3) {
            $data = array(
                'borrowing_id' => $last_id,
                'asset_id' => $arr3->asset_id,
                'asset_code' => $arr3->asset_code,
                'quantity' => $arr3->quantity,
                'type' => $arr3->type,
                'uom' => $arr3->uom,
                'asset_name' => $arr3->asset_name,
                'date_borrowed' => $arr3->date_borrowed,
                'date_due' => $arr3->date_due,
                'remarks' => $arr3->remarks,
                'is_overdue' => $arr3->is_vehicle,
            );
            $insertedContent = $this->borrowing->save_content($data);
            if($insertedContent && $arr3->asset_id){
                if($arr3->type == "asset"){
                    $this->db->update("gccasset.assets", array("is_borrowed"=>1), array("id"=>$arr3->asset_id));
                }elseif($arr3->type == "vehicle_component" || $arr3->type == "vehicle"){
                    $this->db->update("gccasset.vehicles", array("is_borrowed"=>1), array("id"=>$arr3->asset_id));
                }
                $this->core_layout->setEventLog("New Borrowing - Add {$arr3->type} {$arr3->asset_code}  for borrowing.", "add", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("New Borrowing - Failed add {$arr3->type} {$arr3->asset_code} for borrowing.", "add", "error", "gcceforms", "system");
            }

        }
        $this->borrowing->delete_temp_all($user_ids);
        if ($insert) {
            $this->temporary_sending_email($last_id);
        }
        echo json_encode(array("status" => TRUE, "last_id" => $last_id));
    }

    public function update_borrowing($id)
    {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $user_id = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');
        $x = explode("\n", $this->input->post('description'));
        $data = array(
            'company' => $x[0],
            'department' => $x[1],
            'position' => $x[2],
            'borrower' => $this->input->post('borrower'),
            'date_trans' => date("Y-m-d h:i:s", strtotime($this->input->post('trans_date'))),
            'date_needed' => date("Y-m-d", strtotime($this->input->post('need_dt'))),
            'purpose' => $this->input->post('purpose'),
            'last_edited_by' => $user_id,
            'last_edited_dt' => $date,
        );
        $this->borrowing->update(array('id' => $id), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function approve_borrowing($id)
    {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $user_id = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');
        $data = array(
            'status' => 'Approved',
            'approved_by' => $user_id,
            'approved_dt' => $date,
        );
        if($this->borrowing->update(array('id' => $id), $data)){
            $message = "View Borrowing - Approve borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "success";
            $table = "user";
        }else{
            $message = "View Borrowing - Failed approve borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);

        echo json_encode(array("status" => TRUE));
    }

    public function cancel_borrowing($id)
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
        if($this->borrowing->update(array('id' => $id), $data)){
            $message = "View Borrowing - Cancel borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "success";
            $table = "user";
        }else{
            $message = "View Borrowing - Failed cancel borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function release_borrowing($id)
    {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $user_id = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');
        $data = array(
            'status' => 'Released',
            'released_by' => $user_id,
            'released_dt' => $date,
            'released_remarks' => $this->input->post('released_remarks'),
        );
        if($this->borrowing->update(array('id' => $id), $data)){
            $message = "View Borrowing - Release borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "success";
            $table = "user";
        }else{
            $message = "View Borrowing - Failed release borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function undo_approve($id)
    {
        $data = array(
            'status' => 'Pending',
            'approved_by' => '',
            'approved_dt' => '',
        );
        if($this->borrowing->update(array('id' => $id), $data)){
            $message = "View Borrowing - Undo approve borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "success";
            $table = "user";
        }else{
            $message = "View Borrowing - Failed undo approve borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function undo_cancel($id)
    {
        $data = array(
            'status' => 'Pending',
            'cancelled_by' => '',
            'cancelled_dt' => '',
            'cancelled_remarks' => '',
        );
        if($this->borrowing->update(array('id' => $id), $data)){
            $message = "View Borrowing - Restore borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "success";
            $table = "user";
        }else{
            $message = "View Borrowing - Failed restore cancel borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function undo_release($id)
    {
        $data = array(
            'status' => 'Approved',
            'released_by' => '',
            'released_dt' => '',
            'released_remarks' => '',

        );
        if($this->borrowing->update(array('id' => $id), $data)){
            $message = "View Borrowing - Undo release borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "success";
            $table = "user";
        }else{
            $message = "View Borrowing - Failed undo release borrowing {$this->borrowing->getBorrowingReference($id)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function populate_head($id)
    {
        $this->db->select("a.*, 
            UCASE(IFNULL(b.description, a.company)) as company, 
            UCASE(IFNULL(c.description, a.department)) as department, 
            UCASE(IFNULL(d.name, a.position)) as position");
            
        $this->db->from('gcceforms.borrowing as a');
        $this->db->join('gcchris.tblcompanies as b', 'b.id = a.company', 'left');
        $this->db->join('gcchris.tbldepartments as c', 'c.id = a.department', 'left');
        $this->db->join('gcchris.tblposition as d','d.id = a.position', 'left');
        $this->db->where('a.id', $id);
        $query = $this->db->get();
        $res = $query->result();
        $emp = $this->borrowing->emp_details($res[0]->borrower);
        $res[] = $emp->display_name;
        echo json_encode($res);
    }

    public function populate_body($id)
    {
        $tempSql = 'a.total_cost as cost, a.purchaseprice as amount, a.brand, a.modelno, b.asset_code, b.type, b.quantity, b.asset_name, b.uom, b.date_due, b.new_due, c.brand, c.model, c.purchaseprice, c.total_cost';
        $this->db->select($tempSql);
        $this->db->from('gcceforms.borrowing_body b');
        $this->db->join('gccasset.assets a', 'b.asset_code = a.assetacode', "LEFT");
        $this->db->join("gccasset.vehicles c", "b.asset_id=c.id", "LEFT");
        $this->db->where('b.borrowing_id', $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $rs->asset_name = mb_strtoupper($rs->asset_name);
                $rs->asset_code = mb_strtoupper($rs->asset_code);
                $rs->cost = ($rs->cost) ? $rs->cost : 0;
                $rs->amount = ($rs->amount) ? $rs->amount : 0;
                $rs->date_due = $rs->date_due;
                $rs->asset_code = ($rs->asset_code) ? $rs->asset_code : "<b>No Asset Code</b>";
                $rs->asset_name = ($rs->asset_name) ? $rs->asset_name : "<b>No Asset Name</b>";
                $tempBrandModel = "---";
                if ($rs->type == "asset") {
                    $rs->asset = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name;

                    if(isset($rs->brand, $rs->modelno) && $rs->brand && $rs->modelno){
                        $tempBrandModel = "{$rs->brand} / {$rs->modelno}";
                    }else if(isset($rs->brand) && $rs->brand){
                        $tempBrandModel = "{$rs->brand}";
                    }else if(isset($rs->modelno) && $rs->modelno){
                        $tempBrandModel = "{$rs->modelno}";
                    }

                    $rs->cost = $rs->cost ? $rs->cost : 0;
                    $rs->amount = $rs->amount ? $rs->amount : 0;
                }

                if ($rs->type == "vehicle") {
                    $rs->asset = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name;

                    if(isset($rs->brand, $rs->model) && $rs->brand && $rs->model){
                        $tempBrandModel = "{$rs->brand} / {$rs->model}";
                    }elseif(isset($rs->brand) && $rs->brand){
                        $tempBrandModel = "{$rs->brand}";
                    }else if(isset($rs->model) && $rs->model){
                        $tempBrandModel = "{$rs->model}";
                    }

                    $rs->cost = $rs->total_cost ? $rs->total_cost : 0;
                    $rs->amount = $rs->purchaseprice ? $rs->purchaseprice : 0;
                }

                if ($rs->type == "sample") {
                    $rs->desc = "";
                    $rs->asset = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name;
                }

                if ($rs->type == "") {
                    $rs->asset = "<b>" . $rs->asset_code . "</b><br>" . $rs->asset_name;
                }
                $rs->brand_model = $tempBrandModel;
                $arrData[$key] = $rs;
            }

            echo json_encode($arrData);
        } else {
            echo array();
        }
    }

    public function return_item()
    {
        $this->db->select("asset_id, type");
        $this->db->from("gcceforms.borrowing_body");
        $this->db->where("id", $this->input->post('id_return'));
        $query = $this->db->get();

        $condition = $query->row_array();

        if($condition['type'] == "asset"){
            $asset = array(
                'is_borrowed' => '0'
            );
            $this->db->where('id', $condition['asset_id']);
            $this->db->update("gccasset.assets", $asset);
        }else{
            $asset = array(
                'is_borrowed' => '0'
            );
            $this->db->where('id', $condition['asset_id']);
            $this->db->update("gccasset.vehicles", $asset);
        }

        $data = array(
            'date_returned' => date('Y-m-d H:i:s', strtotime($this->input->post('date_returned'))),
            'return_remarks' => $this->input->post('return_remarks'),
            'is_overdue' => !empty($this->input->post('is_overdue')) && $this->input->post('is_overdue') ? $this->input->post('is_overdue') : 0,
            'is_returned' => '1',
        );

        $is_overdue = !empty($this->input->post('is_overdue')) && $this->input->post('is_overdue') ? "overdue" : "";

        if($this->borrowing->update_content(array('id' => $this->input->post('id_return')), $data)){

            $message = "Borrowed Borrowing - Return $is_overdue {$this->borrowing->getAssetCodeBorrowingBody($this->input->post('id_return'))}.";
            $type = "success";
            $table = "user";
        }else{
            $message = "Borrowed Borrowing - Failed return $is_overdue {$this->borrowing->getAssetCodeBorrowingBody($this->input->post('id_return'))}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function mass_return_item(){
        $post = $this->input->post();

        $ids = json_decode($post['checked']);
        $this->db->select("asset_id, type");
        $this->db->from("gcceforms.borrowing_body");
        $this->db->where_in("id", $ids);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $key => $rs) {
                if($rs->type == "asset"){
                    $asset = array(
                        'is_borrowed' => '0'
                    );
                    $this->db->where('id', $rs->asset_id);
                    $this->db->update("gccasset.assets", $asset);
                }else{
                    $asset = array(
                        'is_borrowed' => '0'
                    );
                    $this->db->where('id', $rs->asset_id);
                    $this->db->update("gccasset.vehicles", $asset);
                }
            }
        }


        $data = array(
            'date_returned' => date('Y-m-d H:i:s', strtotime($post['date_returned'])),
            'return_remarks' => $post['return_remarks'],
            'is_overdue' => !empty($post['is_overdue']) && $post['is_overdue'] ? $post['is_overdue'] : 0,
            'is_returned' => '1',
        );

        $is_overdue = !empty($post['is_overdue']) && $post['is_overdue'] ? "overdue" : "";

        foreach ($ids as $key => $value) {
            if($this->borrowing->update_content(array('id' => $value), $data)){
    
                $message = "Borrowed Borrowing - Return $is_overdue {$this->borrowing->getAssetCodeBorrowingBody($value)} via mass return.";
                $type = "success";
                $table = "user";
            }else{
                $message = "Borrowed Borrowing - Failed return $is_overdue {$this->borrowing->getAssetCodeBorrowingBody($value)} via mass return.";
                $type = "error";
                $table = "system";
            }

            $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
        }


        echo json_encode(array("status" => TRUE));
    }

    public function extend()
    {

        $data2 = $this->borrowing->edit_content($this->input->post('id_extend'));
        $date = $data2['date_due'];
        $data = array(

            'date_due' => $this->input->post('new_due'),
            'due_reason' => $this->input->post('due_reason'),
            'previous_due' => $date,
        );
        if($this->borrowing->update_content(array('id' => $this->input->post('id_extend')), $data)){
            $message = "Borrowed Borrowing - Extend {$this->borrowing->getAssetCodeBorrowingBody($this->input->post('id_extend'))} until {$this->input->post('new_due')}.";
            $type = "success";
            $table = "user";
        }else{
            $message = "Borrowed Borrowing - Failed extend {$this->borrowing->getAssetCodeBorrowingBody($this->input->post('id_extend'))} until {$this->input->post('new_due')}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "updated", $type, "gcceforms", $table);
        echo json_encode(array("status" => TRUE));
    }

    public function undo_return($id)
    {


        $data = array(

            'date_returned' => '',
            'return_remarks' => '',
            'is_returned' => '0',
        );
        $this->borrowing->update_content(array('id' => $id), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function overdue_count()
    {
        $data = $this->borrowing->overdue_count();
        echo json_encode($data);
    }

    public function get_borrowing_analytics_for_dashboard()
    {
        $data = $this->borrowing->m_get_borrowing_analytics_for_dashboard();
        echo json_encode($data);
    }

    function ajax_sample_name()
    {
        $data = $this->borrowing->list_name();
        echo json_encode($data);
    }

    public function temporary_sending_email($id = null, $sm = "New", $sendEmail = true)
    {
        if ($id) {
            $det = $this->borrowing->get_by_id($id);

            $subject = $det->reference_no;

            $message = "";
            $message .= $this->load->view("eforms/email_templates/email-bf_template", array("id" => $id), true);


            if ($sendEmail) {
                $module = "eforms_borrowing_new";
                $email_title = "Borrowing Form - eForms";
                $content_title = "Borrowing Form - eForms";

                if ($sm == "New") {
                    $content_title = "Borrowing Form - {$subject}";
                } else {
                    $content_title = "Borrowing Form - {$subject} - Edited";
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

    public function get_borrowing_overdue(){
        $tempData = $this->get_overdue_items();
        $this->load->view("eforms/email_templates/email-bf_overdue_template", $tempData);
    }

    function export_event_log($export){
        $data = $this->borrowing->exportData($export);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function export_event_log_archive($export){
        $data = $this->borrowing->exportDataArchive($export);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function add_telegram_config(){
        $data = $this->borrowing->addTelegramConfig();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function mass_fix_action(){
        $data = $this->borrowing->massFixAction();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

}