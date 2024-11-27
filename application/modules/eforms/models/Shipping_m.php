<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_m extends CI_Model {
    private $user_data = array();
    protected $eformsTable = "gcceforms";
    public function __construct()
	{
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in"); 
        date_default_timezone_set('Asia/Singapore');
    }

    
    function getDatatableRequest(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"7", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
        $status = (isset($post['status']) && $post['status']) ? ucwords($post['status']) : null; //clicked in portal dashboard
        
        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->get_all_post($query_builder, $limit, $offset, $sortBy, $sortOrder, $status);
            $rowCount = $this->get_all_post_count($query_builder, $status);
        }

        if($search){
            $rowData = $this->get_searched_item($query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $status);
            $rowCount = $this->get_searched_item_count($query_builder, $search, $status);
            $this->core_layout->setEventLog("Shipping Masterfile - Search {$search} in datatable.", "search", "success", "gcceforms", "user");
        }

        if($query_builder){
            $this->core_layout->setEventLog("Shipping Masterfile - Generate data using query_builder {$query_builder} in datatable.", "search", "success", "gcceforms", "user");
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_post($query_builder=null, $limit=10, $offset=0, $sortBy, $sortOrder, $status = null){
        $date= date("Y-m-d", strtotime("-1 year"));
        $sql = "a.cat, a.id, a.status, a.priority, a.reference_no,  d.firstname, d.middlename, d.lastname, d.suffix, c.description AS file_under, a.company_to, a.department_to, a.ship_to, b.description, a.ship_date";
        $this->db->select($sql);
        $this->db->from("gcceforms.shipping a");
        $this->db->join("gcchris.tblcompanies c", "a.company_from = c.id", "LEFT");
        $this->db->join("gcceforms.shipping_body b", "a.id = b.shipping_id", "LEFT");
        $this->db->join("gccmaster.tblemployees d", "a.ship_to = d.id", "LEFT");
        $this->db->where("a.created_dt >=", $date);
        $this->db->where_not_in("a.status","Cancelled");
        if($query_builder){
            $this->db->where($query_builder);
        }

        if($status){
            $this->db->where('a.status', $status);
        }
             
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        if($sortBy[$i]['data']=="firstname"){
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
        }else{
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                if($rs->cat=="in"){
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";

                    if(intval($rs->company_to)>0){
                        $rs->company_to =  $this->getCompany($rs->company_to);
                    }

                    if(intval($rs->department_to)>0){
                        $rs->department_to = $this->get_department($rs->department_to);
                    }

                    $arrData[$key] = $rs;
                }else{
                    $rs->display_name = $rs->ship_to;
                    $arrData[$key] = $rs;
                }
            }
            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }
            return $data;
        }else{
            return array();
        }
    }

    private function get_all_post_count($query_builder=null, $status = null){
        $date= date("Y-m-d", strtotime("-1 year"));
        $this->db->from("gcceforms.shipping a");
        $this->db->join("gcchris.tblcompanies c", "a.company_from = c.id", "LEFT");
        $this->db->join("gcceforms.shipping_body b", "a.id = b.shipping_id", "LEFT");
        $this->db->join("gccmaster.tblemployees d", "a.ship_to = d.id", "LEFT");
        $this->db->where("a.created_dt >=", $date);
        $this->db->where_not_in("a.status","Cancelled");
        if($query_builder){
            $this->db->where($query_builder);
        }

        if($status){
            $this->db->where('a.status', $status);
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_item($query_builder=null, $search=null, $limit=10, $offset=0, $sortBy, $sortOrder, $status = null){
        if($search){
            $date= date("Y-m-d", strtotime("-1 year"));
            $sql = "a.cat, a.id, a.status, a.priority, a.reference_no,  d.firstname, d.middlename, d.lastname, d.suffix, c.description AS file_under, a.company_to, a.department_to, a.ship_to, b.description, a.ship_date";
            $filterFields = array("a.id", "a.status", "a.reference_no", "a.priority", "a.ship_to", "b.description", "a.ship_date", "c.description", "d.firstname", "d.lastname", "a.ship_to", "a.department_to");
            $this->db->select($sql);
            $this->db->from("gcceforms.shipping a");
            $this->db->join("gcchris.tblcompanies c", "a.company_from = c.id", "LEFT");
            $this->db->join("gcceforms.shipping_body b", "a.id = b.shipping_id", "LEFT");
            $this->db->join("gccmaster.tblemployees d", "a.ship_to = d.id", "LEFT");
            $this->db->where("a.created_dt >=", $date);
            $this->db->where_not_in("a.status","Cancelled");
            if($query_builder){
                $this->db->where($query_builder);
            }

            if($status){
                $this->db->where('a.status', $status);
            }
                
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();
            $i = $sortOrder[0]['column'];
            if($sortBy[$i]['data']=="firstname"){
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
            }else{
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }
            $query = $this->db->get();

            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $key => $rs){
                    if($rs->cat=="in"){
                        $tempRs = (array) $rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object) $fullname;
                        $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                        
                        if(intval($rs->company_to)>0){
                            $rs->company_to =  $this->getCompany($rs->company_to);
                        }
                        if(intval($rs->department_to)>0){
                            $rs->department_to = $this->get_department($rs->department_to);
                        }

                        $arrData[$key] = $rs;
                    }else{
                        $rs->display_name = $rs->ship_to;
                        $arrData[$key] = $rs;
                    }
                }
                $data = array();
                foreach($arrData as $k=>$v){
                    $data[] = $v;
                }
                return $data;
            }else{
                return array();
            }
        }else{
            return array();
        }
    }

    private function get_searched_item_count($query_builder=null, $search=null, $status = null){
        $rowCount = 0;
        if($search){
            $date= date("Y-m-d", strtotime("-1 year"));
            $sql = "a.id, a.status, a.priority, a.reference_no, c.description AS file_under, a.company_to, a.ship_to, b.description, d.firstname, d.middlename, d.lastname, d.suffix, a.ship_date";
            $filterFields = array("d.firstname", "d.lastname", "a.id", "a.status", "a.reference_no", "a.priority", "a.ship_to", "b.description", "a.ship_date", "c.description");
            $this->db->select($sql);
            $this->db->from("gcceforms.shipping a");
            $this->db->join("gcchris.tblcompanies c", "a.company_from = c.id", "LEFT");
            $this->db->join("gcceforms.shipping_body b", "a.id = b.shipping_id", "LEFT");
            $this->db->join("gccmaster.tblemployees d", "a.ship_to = d.id", "LEFT");
            $this->db->where("a.created_dt >=", $date);
            $this->db->where_not_in("a.status","Cancelled");
            if($query_builder){
                $this->db->where($query_builder);
            }

            if($status){
                $this->db->where('a.status', $status);
            }
            
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    function getCompany($company) {
        $this->db->select('description');
        $this->db->from('gcchris.tblcompanies');
        $this->db->where('id', $company);
        $query = $this->db->get();
        return $query->row_array()['description'];
    }
    
    function get_department($department){
        $this->db->select('id,description');
        $this->db->from('gcchris.tbldepartments');
        $this->db->where('id', $department);
        $query = $this->db->get();
		return $query->row_array()['description'];
    }

    function getPosition($id){
        $this->db->select("id, name");
        $this->db->from("gcchris.tblposition");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->name;
    }

    function mostShippingtoDetails(){
        $this->db->select("*,count(ship_to) c");
        $this->db->from("gcceforms.shipping");
        $this->db->where("ship_to !=","");
        $this->db->where("status","Received");
        $this->db->group_by("ship_to");
        $this->db->order_by("c","DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() > 0){
        $arrData = array();
        foreach ($query->result() as  $key => $rs) { 
            if(is_numeric($rs->ship_to)){
                $rs->ship_to = $this->viewShipto($rs->ship_to);
            }
            $rs->sum =  $this->sumDetails("shipping", "ship_to");
            $arrData[] = $rs;
        }
            return $arrData[0];
        }else{
            return array();
        } 
    }

    function mostShippinglocDetails(){
		$this->db->select("*,count(ship_to_address) c");
 		$this->db->from("gcceforms.shipping");
 		$this->db->where("ship_to_address !=","");
 		$this->db->group_by("ship_to_address");
 		$this->db->order_by("c","DESC");
 		$this->db->limit(1);
		$query = $this->db->get();
        if($query->num_rows() > 0){
        $arrData = array();
        foreach ($query->result() as  $key => $rs) { 
            $rs->sum =  $this->sumDetails("shipping", "ship_to_address");
            $arrData[] = $rs;
        }
            return $arrData[0];
        }else{
            return array();
        } 
    }
    
    function mostShippingitDetails(){
        $this->db->select("*,count(stock_code) c");
        $this->db->from("gcceforms.shipping_body");
        $this->db->where("stock_code !=","");
        $this->db->group_by("stock_code,description");
        $this->db->order_by("c","DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() > 0){
        $arrData = array();
        foreach ($query->result() as  $key => $rs) { 
            $rs->sum =  $this->sumDetails("shipping_body", "stock_code");
            $arrData[] = $rs;
        }
            return $arrData[0];
        }else{
            return array();
        } 
    }

    function sumDetails($table, $where){
        $this->db->select("count(*) c");
        $this->db->from("gcceforms.".$table."");
        $this->db->where("".$where." !=","");
        $query = $this->db->get();
        return $query->row_array()['c'];
    }

    function getFileUnder(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get['q'])){
            $query = $this->db->query("SELECT  id, description FROM gcchris.tblcompanies WHERE description LIKE '%{$get['q']}%' ORDER BY description ASC");
        }else{
            $query = $this->db->query("SELECT  id, description FROM gcchris.tblcompanies ORDER BY description ASC");
        }

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["description"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function getDepartment(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get['q'])){
            $query = $this->db->query("SELECT  id, description FROM gcchris.tbldepartments WHERE description LIKE '%{$get['q']}%' ORDER BY description ASC");
        }else{
            $query = $this->db->query("SELECT  id, description FROM gcchris.tbldepartments ORDER BY description ASC");
        }

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["description"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function getRequestedBy(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get['q'])){
            $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE (employee_status='Active') AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%') ORDER BY firstname ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC LIMIT 10");
        }

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $tempRs = (array) $_query;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
            
                $data["id"] = $_query["id"];
                $data["text"] =   ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function getShipToDetail(){
        $datap = $this->input->get();
        // var_dump($datap['data']);
        $resultset = array();
            $this->db->select('a.*, b.company_address');
            $this->db->from('gccmaster.tblemployees a');
            $this->db->join('gcchris.tblcompanies b', 'b.id = a.company_id OR b.description = a.company_id OR b.code = a.company_id', 'left');
            $this->db->where('a.employee_status', 'Active');
            $this->db->where('a.id', $datap['data']);
            $query = $this->db->get();

            if ($query->num_rows() == 1) {
                $tempRow = $query->row();
                if(is_numeric($tempRow->company_id)){ 
                    $tempRow->company_id = $this->getCompany($tempRow->company_id); 
                }else{ 
                    $tempRow->company_id = $tempRow->company_id; 
                }

                if(is_numeric($tempRow->department_id)){ 
                    $tempRow->department_id = $this->get_department($tempRow->department_id);  
                }else{
                    $tempRow->department_id = $tempRow->department_id; 
                }
    
                if(is_numeric($tempRow->position)){
                    $tempRow->position = $this->getPosition($tempRow->position); 
                }else{ 
                    $tempRow->position = $tempRow->position; 
                }

                $resultset["response"] = true;
                $resultset["row"] = $tempRow;
            } else {
                $resultset["response"] = false;
            }

            return $resultset;
    }

    

    function getLocation(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get['q'])){
            $query = $this->db->query("SELECT  id, location FROM gccasset.location WHERE location LIKE '%{$get['q']}%' ORDER BY location ASC");
        }else{
            $query = $this->db->query("SELECT  id, location FROM gccasset.location ORDER BY location ASC");
        }

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["location"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }


    function serviceVehicle(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get['q'])){
            $query = $this->db->query("SELECT  id, CONCAT(plateno,' | ',name) AS vehicle FROM gccasset.vehicles WHERE  (isCompo='0' AND is_borrowed='0') AND plateno LIKE '%{$get['q']}%' OR name LIKE '%{$get['q']}%' ORDER BY description ASC");
        }else{
            $query = $this->db->query("SELECT  id, CONCAT(plateno,' | ',name) AS vehicle FROM gccasset.vehicles WHERE isCompo='0' AND is_borrowed='0' ORDER BY plateno ASC");
        }

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["vehicle"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function driver(){
        $get = $this->input->get();
        $resultarray = array();
        $q = isset($get['q']) ? $get['q'] : "";
        if($q){
            $query = $this->db->query("SELECT  id, firstname, middlename, lastname, suffix, position FROM gccmaster.tblemployees WHERE  employee_status='Active' AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%') ORDER BY firstname ASC");
        }else{
            $query = $this->db->query("SELECT  id, firstname, middlename, lastname, suffix, position FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC");
        }

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){              
                if(is_numeric($_query['position'])){
                    $this->db->select("name");
                    $this->db->from("gcchris.tblposition");
                    $this->db->where("id",$_query['position']);
                    $query = $this->db->get();
                    $data = $query->row_array();
                    $driver = mb_strtoupper($data["name"]);
                }else{
                    $driver = mb_strtoupper($_query['position']);
                }
                if(strpos($driver, "DRIVER") !== false || strpos($driver, 'BACKHOE') !== false || strpos($driver, 'TRACTOR') !== false || strpos($driver, 'GRADER') !== false || strpos($driver, 'ROLLER') !== false || strpos($driver, 'PAYLOADER') !== false||strpos($driver, 'BULLDOZER') !== false||strpos($driver, 'CRANE') !== false||strpos($driver, 'MCC') !== false || $_query['id']==140){
                    $data = array();
                    $tempRs = (array) $_query;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                
                    $data["id"] = $_query["id"];
                    $data["text"] =   ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $resultarray[] = $data;
                }
            }
        }
        return array("results"=>$resultarray);
    }

    function getShippingContent(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowData = array();
        $rowData = $this->get_shipping_content($sortBy, $sortOrder);
     
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function getShippingReference($id){
        return $this->db->get_where("gcceforms.shipping",array("id"=>$id))->row('reference_no');
    }

    private function get_shipping_content($sortBy, $sortOrder){
        $id = $this->user_data['id'];
        $sql = "id, stock_code, quantity, description,uom, item_purpose";

        $this->db->select($sql);
        $this->db->from("gcceforms.shipping_body_temp");
        $this->db->where('user_id', $id);

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }

            return $data;
        }else{
            return array();
        }
    }

    function addNewItem(){
        $post = $this->input->post();
        $category = explode("-",$post['inventorycode']);
        $item_category = $category[0];
        $array_data = array(
            "inventorycode" => mb_strtoupper($post['inventorycode']),
            "barcode" => mb_strtoupper($post['inventorycode']),
            "item_description" => mb_strtoupper($post['item_description']),
            "uom" => mb_strtoupper($post['uom']),
            "item_category" => mb_strtoupper($item_category)
        );
        $insert = $this->db->insert("gccis.items", $array_data);
        
        return $insert;
    }

    function itemLookup(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get['q'])){
            $query = $this->db->query("SELECT  inventorycode, CONCAT(inventorycode,' | ', item_description) AS item FROM gccis.items WHERE (inventorycode LIKE '%{$get['q']}%' OR item_description LIKE '%{$get['q']}%') ORDER BY inventorycode ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT  inventorycode, CONCAT(inventorycode,' | ', item_description) AS item FROM gccis.items ORDER BY inventorycode ASC LIMIT 10");
        }

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["inventorycode"];
                $data["text"] = $_query["item"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function itemLookupDetails($data){
        $datap = implode($data);
        $resultarray = array();
        $query = $this->db->query("SELECT inventorycode, uom, item_description FROM  gccis.items WHERE inventorycode='".$datap."'");
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $resultarray["uom"] = $_query["uom"];
                $resultarray["description"] = $_query["item_description"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function uomLookup(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get['q'])){
            $query = $this->db->query("SELECT uom_code, CONCAT(uom_code,' | ', uom_desc) AS uom FROM gccis.uom WHERE (uom_code LIKE '%{$get['q']}%' OR uom_desc LIKE '%{$get['q']}%') ORDER BY uom_code ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT uom_code, CONCAT(uom_code,' | ', uom_desc) AS uom FROM gccis.uom ORDER BY uom_code ASC LIMIT 10");
        }

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["uom_code"];
                $data["text"] = $_query["uom_code"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function addNewUom(){
        $post = $this->input->post();
        $array_data = array(
        "uom_code" => strtoupper($post['add_code']),
        "uom_desc" => strtoupper($post['add_description'])
        );
        if($post['add_code'] == NULL OR $post['add_description'] == NULL){
            return false;
        }else{
            $insert = $this->db->insert("gccis.uom", $array_data);
            return $insert;
        }
    }

    function editItemDetails($id){
        $query = $this->db->query("SELECT id,stock_code,quantity,description,uom, item_purpose, cat FROM gcceforms.shipping_body_temp WHERE id=$id");
		return $query->result();
    }

    function updateItemDetails($id){
        $query = $this->db->query("SELECT id,stock_code,quantity,description,uom, item_purpose, cat FROM gcceforms.shipping_body WHERE id=$id");
		return $query->result();
    }

    function updateItem($get){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'user_id' => $this->user_data['id'],
            'stock_code' => $this->input->post('edit_items'),
            'quantity' => $this->input->post('quantity'),
            'uom' => $this->input->post('uom'),
            'description' => $this->input->post('description'),
            'item_purpose' => $this->input->post('purpose'),
        );
        if($get){
            $this->db->where('gcceforms.shipping_body_temp.id', $get);
            return $this->db->update('gcceforms.shipping_body_temp', $data);
        }
    }

    function updateEditItem($get){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'cat' => $this->input->post('cat'),
            'stock_code' => $this->input->post('edit_items'),
            'quantity' => $this->input->post('quantity'),
            'uom' => $this->input->post('uom'),
            'description' => $this->input->post('description'),
            'item_purpose' => $this->input->post('purpose'),
        );
        if($get){
            $this->db->where('gcceforms.shipping_body.id', $get);
            return $this->db->update('gcceforms.shipping_body', $data);
        }
    }

    function assetLookup(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get['q'])){
            $query = $this->db->query("SELECT  id, assetacode, CONCAT(assetacode,' | ', name) AS asset FROM gccasset.assets WHERE assetacode LIKE '%{$get['q']}%' OR name LIKE '%{$get['q']}%' ORDER BY assetacode ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT  id,assetacode,isComponent,isReleased, CONCAT(assetacode,' | ', name) AS asset FROM gccasset.assets WHERE isComponent='0' OR isReleased='0' OR assetacode!=' ' ORDER BY assetacode ASC LIMIT 10");
        }

        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $data["id"] = $_query["assetacode"];
                $data["text"] = $_query["asset"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function assetLookupDetails($data){
        $datap = implode($data);
        $resultarray = array();
        $query = $this->db->query("SELECT id, uom, name FROM  gccasset.assets WHERE assetacode='$datap'");
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $resultarray["uom"] = $_query["uom"];
                $resultarray["description"] = $_query["name"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

    function addItemModal(){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'user_id' => $this->user_data['id'],
            'stock_code' => $this->input->post('items'),
            'quantity' => $this->input->post('quantity'),
            'uom' => $this->input->post('uom'),
            'description' => $this->input->post('description'),
            'item_purpose' => $this->input->post('purpose'),
            'cat' => "item"
        );

        $insert = $this->db->insert('gcceforms.shipping_body_temp', $data);
        if($insert){
            $message = "New Shipping - Add item {$this->input->post('items')} for shipping.";
            $type = "success";
            $table = "user";
        }else{
            $message = "New Shipping - Failed add item {$this->input->post('items')} for shipping.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
        return $insert; 
    }

    function addAssetModal(){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $asset_location = $this->db->get_where("gccasset.assets", array("gen_code"=>$this->input->post('assets')))->row('area_id');
        $data = array(
            'user_id' => $this->user_data['id'],
            'stock_code' => $this->input->post('assets'),
            'asset_location' => $asset_location ? $asset_location : 'N/A',
            'quantity' => $this->input->post('quantity2'),
            'uom' => $this->input->post('uom2'),
            'description' => $this->input->post('description2'),
            'item_purpose' => $this->input->post('purpose2'),
            'cat' => "asset"
        );

        $insert = $this->db->insert('gcceforms.shipping_body_temp', $data);
        if($insert){
            $message = "New Shipping - Add asset {$this->input->post('assets')} for shipping.";
            $type = "success";
            $table = "user";
        }else{
            $message = "New Shipping - Failed add asset {$this->input->post('assets')} for shipping.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
        return $insert; 
    }

    function clearContents(){
            $data = $this->user_data['id'];
            $delete = $this->db->query("DELETE FROM gcceforms.shipping_body_temp WHERE user_id=$data");
            if($delete){
                $message = "New Shipping - Delete all asset/item for shipping.";
                $type = "success";
                $table = "user";
            }else{
                $message = "New Shipping - Failed delete all asset/item for shipping.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "delete", $type, "gcceforms", $table);
            return $delete; 
    }

    
    function deleteContent($id){
        $asset_for_shipping = $this->db->get_where("gcceforms.shipping_body_temp", array("id"=>$id))->row('stock_code');
        $delete = $this->db->query("DELETE FROM gcceforms.shipping_body_temp WHERE id=$id");
        if($delete){
            $message = "New Shipping - Delete {$asset_for_shipping} for shipping.";
            $type = "success";
            $table = "user";
        }else{
            $message = "New Shipping - Failed delete {$asset_for_shipping} for shipping.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "delete", $type, "gcceforms", $table);
        return $delete;
    }

    function getEmploymentDetailsId($employement_details, $id, $column){
        if($employement_details == 'company_to' || $employement_details == 'company_from'){
            if(is_numeric($id)){
                $q = $this->db->query("SELECT * FROM gcchris.tblcompanies WHERE `id` = '$id'");
                $query = $q->row_array();
                if($column == "name"){
                    $data = $query['description'];
                }else{
                    $data = $query['id'];
                }
                return $data;
            }else if(is_string($id)){
                $q = $this->db->query("SELECT * FROM gcchris.tblcompanies WHERE `id` = '$id'");
                $query = $q->row_array();
                if($column == "name"){
                    $data = $query['description'];
                }else{
                    $data = $query['id'];
                }
                return $data;
            }else{
                $q = $this->db->query("SELECT * FROM gcchris.tblcompanies WHERE `description` LIKE '%$id%'");
                $query = $q->row_array();
                if($column == "name"){
                    $data = $query['description'];
                }else{
                    $data = $query['id'];
                }
                return $data;
            }
        }else if($employement_details == 'position_to'){
            if(is_numeric($id)){
                $q = $this->db->query("SELECT * FROM gcchris.tblposition WHERE `id` = '$id'");
                $query = $q->row_array();
                if($column == "name"){
                    $data = $query['name'];
                }else{
                    $data = $query['id'];
                }
                return $data;
            }else{
                $id = rtrim($id," ");
                $q = $this->db->query("SELECT * FROM gcchris.tblposition WHERE `name` LIKE '%$id%'");
                $query = $q->row_array();
                if($column == "name"){
                    $data = $query['name'];
                }else{
                    $data = $query['id'];
                }
                return $data;
            }
        }else if($employement_details == 'department_to' || $employement_details == 'department_from'){
            if(is_numeric($id)){
                $q = $this->db->query("SELECT * FROM gcchris.tbldepartments WHERE `id` = '$id'");
                $query = $q->row_array();
                if($column == "name"){
                    $data = $query['description'];
                }else{
                    $data = $query['id'];
                }
                return $data;
            }else{
                $id = rtrim($id," ");
                $q = $this->db->query("SELECT * FROM gcchris.tbldepartments WHERE `description` LIKE '%$id%'");
                $query = $q->row_array();
                if($column == "name"){
                    $data = $query['description'];
                }else{
                    $data = $query['id'];
                }
                return $data;
            }
        }else{
            return false;
        }
    }

    function save_shipping(){
        $post = $this->input->post();
        $cat = $this->input->post("cat");
        $contents = $this->get_item_content();
        
        if($cat=="in"){
            $date = date('Y-m-d H:i:s');
            $year = substr($date, 2, 2);
            $month = substr($date, 5, 2);
            $list = $this->series($year, $month);
            $series = '';
            if (sizeof($list) > 0) {
                foreach($list as $arr) {
                    $x = $arr->ref_series;
                }
                $series = intval($x) + 1;
                if (strlen($series) == 1) {
                    $series = '000'.$series;
                } else if (strlen($series) == 2) {
                    $series = '00'.$series;
                } else if (strlen($series) == 3) {
                    $series = '0'.$series;
                } else {
                    $series = $series;
                }
            } else {
                $series = '0001';
            }
            if($this->input->post('type')=="service"){
                $serviceType = "1";
            }else{
                $serviceType = "0";
            }
            if($this->input->post('type')=="others"){
                $otherType = "1";
            }else{
                $otherType = "0";
            }

            $emp_details = $this->db->query("SELECT position, company_id, department_id FROM gccmaster.tblemployees WHERE id = '$post[ship_to]' AND employee_status='Active'");
            $emp_data = $emp_details->row_array();
            
            
            if(is_numeric($emp_data['company_id'])){
                $company_to = $emp_data['company_id'];
            }else{
                $company_to = $this->getEmploymentDetailsId("company_to", $emp_data['company_id'], "id");
            }

            if(is_numeric($emp_data['department_id'])){
                $department_to = $emp_data['department_id'];
            }else{
                $department_to = $this->getEmploymentDetailsId("department_to", $emp_data['department_id'], "id");
            }

            if(is_numeric($emp_data['position'])){
                $position_to = $emp_data['position'];
            }else{
                $position_to = $this->getEmploymentDetailsId("position_to", $emp_data['position'], "id");
            }

            $x = explode("\n", $this->input->post('company'));
            $area_id = $this->input->post('location');
            $area_id = (isset($area_id) && $area_id)? $area_id : 0;
            
            $ship_to_address = $this->input->post('ship_to_address');
            $ship_to_address = (isset($ship_to_address) && $ship_to_address)? $ship_to_address : "";
    
            $vehicle = $this->input->post('vehicle');
            $vehicle = (isset($vehicle) && $vehicle)? $vehicle : "";

            $driver = $this->input->post('driver');
            $driver = (isset($driver) && $driver)? $driver : "";
            
            $transporter = $this->input->post('transporter');
            $transporter = (isset($transporter) && $transporter)? $transporter : "";
            
            $other_remarks = $this->input->post('others_remarks');
            $other_remarks = (isset($other_remarks) && $other_remarks)? $other_remarks : "";
            
            $waybill = $this->input->post('waybill');
            $waybill = (isset($waybill) && $waybill)? $waybill : " ";
            $data = array(
                'ref_yr' => $year,
                'ref_series' => $series,
                'ref_month' => $month,
                'reference_no' => 'SA'.$year.'-'.$month.'-'.$series,
                'requested_by' => $this->input->post('requested_by'),
                'status' => 'Pending',
                'priority' => $this->input->post('priority'),
                'created_id' => $this->user_data['id'],
                'created_by' => $this->user_data['firstname'].' '.$this->user_data['lastname'],
                'created_dt' => $date,
                
                'company_from' => $this->input->post('company_from'),
                'department_from' => $this->input->post('department_from'),
                'ship_to' => $this->input->post("ship_to"),
                'company_to' => $company_to,
                'department_to' => $department_to,
                'position_to' => $position_to,
                'ship_date' => date('Y-m-d H:i:s', strtotime($this->input->post('ship_date'))),
                'area_id' => $area_id,
                'ship_to_address' => $this->input->post('ship_to_address'),
                'is_service' => $serviceType,
                'is_others' => $otherType,
                'others_remarks' => $other_remarks,
                'vehicle_id' => $vehicle,
                'driver' => $driver,
                'transporter' => $transporter,
                'waybill' => $waybill,
                'cat' => $this->input->post('cat'),
            );
            $insert = $this->db->insert('gcceforms.shipping', $data);
            $last_id = $this->db->insert_id();
            // $shipping_details = ""; -> original variable
            $shipping_details = array();
            if($insert){
                foreach($contents as $shipping_content){
                    $asset_code = $shipping_content['stock_code'];
                    $qty = $shipping_content['quantity'];
                    $description = $shipping_content['description'];
                    $purpose = $shipping_content['item_purpose'];
                    $shipping_details[] = '<b>ASSET/STOCK CODE:</b> '.$asset_code.' - (<b>QTY: </b>'.$qty.')'.chr(10).'<b>NAME: </b>'.strtoupper($description).chr(10).'<b>PURPOSE: </b>'.strtoupper($purpose).chr(10).chr(10).'=';
                }

                if($serviceType == 1){
                    $telegram_msg_vehicle = '<b>PLATE NO: </b>'.strtoupper($vehicle).chr(10).'<b>DRIVER: </b>'.strtoupper($this->viewDriver($driver)).chr(10);
                }else{
                    $telegram_msg_vehicle = '<b>REMARKS:</b> '.strtoupper($other_remarks).chr(10);
                }
                
                $company_from = $this->input->post('company_from');
                $department_from = $this->input->post('department_from');
                $td = implode("=",$shipping_details);
                $telegram_msg = '';
                $telegram_msg .= '<b>SA #</b>: '.'SA'.$year.'-'.$month.'-'.$series.chr(10);
                $telegram_msg .= '<b>FILE: </b>'.strtoupper($this->getEmploymentDetailsId("company_from", $company_from, "name")).'('.strtoupper($this->getEmploymentDetailsId('department_from',$department_from, 'name')).')'.chr(10);
                $telegram_msg .= '<b>SHIP TO: </b>'.strtoupper($this->viewShipto($this->input->post('ship_to'))).' - ('.strtoupper($this->getEmploymentDetailsId("company_to", $company_to, "name")).')'.chr(10);
                $telegram_msg .= '<b>SHIP DATE: </b>'.date('m-d H:i:', strtotime($this->input->post('ship_date'))).chr(10);
                $telegram_msg .= '<b>PREP BY: </b>'.strtoupper($this->user_data['firstname']).' '.strtoupper($this->user_data['lastname']).chr(10);
                $telegram_msg .= $telegram_msg_vehicle;
                $telegram_msg .= '<b>TRANSPORTER: </b>'.strtoupper($transporter).chr(10).chr(10);
                $telegram_msg .= str_replace("=","",$td);
                if($this->telegram_config_if_exist('shipping_advice','count') > 0){
                    $telegram_send = $this->shipping->telegram($telegram_msg);
                }

                $message = "New Shipping - Add shipping {'SA'.$year.'-'.$month.'-'.$series}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "New Shipping - Failed add shipping {'SA'.$year.'-'.$month.'-'.$series}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
        }else{
            $date = date('Y-m-d H:i:s');
            $year = substr($date, 2, 2);
            $month = substr($date, 5, 2);
            $list = $this->series($year, $month);
            $series = '';
            if (sizeof($list) > 0) {
                foreach($list as $arr) {
                    $x = $arr->ref_series;
                }
                $series = intval($x) + 1;
                if (strlen($series) == 1) {
                    $series = '000'.$series;
                } else if (strlen($series) == 2) {
                    $series = '00'.$series;
                } else if (strlen($series) == 3) {
                    $series = '0'.$series;
                } else {
                    $series = $series;
                }
            } else {
                $series = '0001';
            }
            if($this->input->post('type')=="service"){
                $serviceType = "1";
            }else{
                $serviceType = "0";
            }
            if($this->input->post('type')=="others"){
                $otherType = "1";
            }else{
                $otherType = "0";
            }
            $x = explode("\n", $this->input->post('company'));
            $area_id = $this->input->post('location');
            $area_id = (isset($area_id) && $area_id)? $area_id : 0;
            
            $ship_to_address = $this->input->post('ship_to_address');
            $ship_to_address = (isset($ship_to_address) && $ship_to_address)? $ship_to_address : "";

            $vehicle = $this->input->post('vehicle');
            $vehicle = (isset($vehicle) && $vehicle)? $vehicle : "";
    
            $driver = $this->input->post('driver');
            $driver = (isset($driver) && $driver)? $driver : "";
            
            $transporter = $this->input->post('transporter');
            $transporter = (isset($transporter) && $transporter)? $transporter : "";
            
            $other_remarks = $this->input->post('others_remarks');
            $other_remarks = (isset($other_remarks) && $other_remarks)? $other_remarks : "";
            
            $waybill = $this->input->post('waybill');
            $waybill = (isset($waybill) && $waybill)? $waybill : " ";
            $data = array(
                'ref_yr' => $year,
                'ref_series' => $series,
                'ref_month' => $month,
                'reference_no' => 'SA'.$year.'-'.$month.'-'.$series,
                'requested_by' => $this->input->post('requested_by'),
                'status' => 'Pending',
                'priority' => $this->input->post('priority'),
                'created_id' => $this->user_data['id'],
                'created_by' => $this->user_data['firstname'].' '.$this->user_data['lastname'],
                'created_dt' => $date,
                
                'company_from' => $this->input->post('company_from'),
                'department_from' => $this->input->post('department_from'),
                'ship_to' => $this->input->post("ship_to_ex"),
                'company_to' => $this->input->post("company_ex"),
                'department_to' => $this->input->post("department_ex"),
                'position_to' => "",
                'ship_date' => date('Y-m-d H:i:s', strtotime($this->input->post('date_ex'))),
                'ship_to_address' => $this->input->post('address_ex'),
                'is_service' => $serviceType,
                'is_others' => $otherType,
                'others_remarks' => $other_remarks,
                'vehicle_id' => $vehicle,
                'driver' => $driver,
                'transporter' => $transporter,
                'waybill' => $waybill,
                'cat' => $this->input->post('cat'),
            );
            $insert = $this->db->insert('gcceforms.shipping', $data);
            $last_id = $this->db->insert_id();
            $shipping_details = array();
            if($insert){
                foreach($contents as $shipping_content){
                    $asset_code = $shipping_content['stock_code'];
                    $qty = $shipping_content['quantity'];
                    $description = $shipping_content['description'];
                    $purpose = $shipping_content['item_purpose'];
                    $shipping_details[] = '<b>ASSET/STOCK CODE:</b> '.$asset_code.' - (<b>QTY: </b>'.$qty.')'.chr(10).'<b>NAME: </b>'.strtoupper($description).chr(10).'<b>PURPOSE: </b>'.strtoupper($purpose).chr(10).chr(10).'=';
                }

                if($serviceType == 1){
                    $telegram_msg_vehicle = '<b>PLATE NO: </b>'.strtoupper($vehicle).chr(10).'<b>DRIVER: </b>'.strtoupper($this->viewDriver($driver)).chr(10);
                }else{
                    $telegram_msg_vehicle = '<b>REMARKS:</b> '.strtoupper($other_remarks).chr(10);
                }
                
                $company_from = $this->input->post('company_from');
                $department_from = $this->input->post('department_from');
                $td = implode("=",$shipping_details);
                $telegram_msg = '';
                $telegram_msg .= '<b>SA #</b>: '.'SA'.$year.'-'.$month.'-'.$series.chr(10);
                $telegram_msg .= '<b>FILE: </b>'.strtoupper($this->getEmploymentDetailsId("company_from", $company_from, "name")).'('.strtoupper($this->getEmploymentDetailsId('department_from',$department_from, 'name')).')'.chr(10);
                $telegram_msg .= '<b>SHIP TO: </b>'.strtoupper($this->input->post("ship_to_ex")).' - ('.strtoupper($this->input->post("company_ex")).')'.chr(10);
                $telegram_msg .= '<b>SHIP DATE: </b>'.date('m-d H:i:', strtotime($this->input->post('date_ex'))).chr(10);
                $telegram_msg .= '<b>PREP BY: </b>'.strtoupper($this->user_data['firstname']).' '.strtoupper($this->user_data['lastname']).chr(10);
                $telegram_msg .= $telegram_msg_vehicle;
                $telegram_msg .= '<b>TRANSPORTER: </b>'.strtoupper($transporter).chr(10);
                $telegram_msg .= '<b>COURIER & WAYBILL #: </b>'.strtoupper($waybill).chr(10).chr(10);
                $telegram_msg .= str_replace("=","",$td);

                if($this->telegram_config_if_exist('shipping_advice','count')){
                    $this->shipping->telegram($telegram_msg);
                }
                $message = "New Shipping - Add shipping {'SA'.$year.'-'.$month.'-'.$series}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "New Shipping - Failed add shipping {'SA'.$year.'-'.$month.'-'.$series}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
            
        }
        return $this->saveContentBody($last_id);
    }
   
    function saveContentBody($id){
        $this->input->post();
            $date = date('Y-m-d H:i:s');
            $contents = $this->get_item_content();
            if (count($contents) > 0) {
                foreach($contents as $arr) {
                    
                    $data = array(
                        'shipping_id' => $id,
                        'asset_id' => $arr["asset_id"],
                        'stock_code' => mb_strtoupper($arr["stock_code"]),
                        'asset_location' => $arr["asset_location"],
                        'quantity' => $arr["quantity"],
                        'uom' => mb_strtoupper($arr["uom"]),
                        'description' => mb_strtoupper($arr["description"]),
                        'item_purpose' => mb_strtoupper($arr["item_purpose"]),
                        'cat' => $arr["cat"],
                        );

                        $return = $this->db->insert('gcceforms.shipping_body', $data);
                }
                $this->clearContents();
            }else{
                $return = false;
            }
            return $return;
    }

    function get_item_content(){
        $id = $this->user_data['id'];
        $this->db->from('gcceforms.shipping_body_temp');
		$this->db->where('user_id', $id);
        $query = $this->db->get();
		return $query->result_array();
    }

    public function series($year, $month)
	{
		$this->db->select('ref_series');
		$this->db->from('gcceforms.shipping');
		$this->db->where('ref_yr',$year);
		$this->db->where('ref_month',$month);
		$this->db->order_by('ref_series','asc');
		$query = $this->db->get();
		return $query->result();
    }

    
    function viewItemContent($id){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowData = array();
        $rowData = $this->view_item_content($id, $sortBy, $sortOrder);
     
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function view_item_content($data, $sortBy=null, $sortOrder="DESC"){
        $sql = "a.id, a.stock_code, a.quantity, a.description, a.item_purpose, a.uom, a.cat";
        $this->db->select($sql);
        $this->db->from("gcceforms.shipping_body a");
        $this->db->join("gcceforms.shipping b","b.id=a.shipping_id","left");
        $this->db->where("a.shipping_id",$data);
        $i = $sortOrder[0]['column'];
        // $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']); // --> original

        $sortBy_data = isset($sortBy[$i]['data']) ? $sortBy[$i]['data'] : '';
        $sort_order = isset($sortOrder[0]['dir']) ? $sortOrder[0]['dir'] : 'ASC';
        $this->db->order_by($sortBy_data, $sort_order);
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $rs->stock_code = mb_strtoupper($rs->stock_code);
                $rs->item_purpose = mb_strtoupper($rs->item_purpose);
                $rs->description = mb_strtoupper($rs->description);
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }

            return $data;
        }else{
            return array();
        }
    }

    function viewShippingDetails($id){
        $this->db->select("a.*, f.name AS vehicle_name, d.position, b.description AS company, c.description AS department, d.firstname, d.middlename,d.lastname, d.suffix, e.location, f.plateno");
        $this->db->from("gcceforms.shipping a");
        $this->db->join("gcchris.tblcompanies b","b.id = a.company_from","left");
        $this->db->join("gcchris.tbldepartments c","c.id = a.department_from","left");
        $this->db->join("gccmaster.tblemployees d","d.id = a.requested_by","left");
        $this->db->join("gccasset.location e","e.id = a.area_id","left");
        $this->db->join("gccasset.vehicles f","f.id = a.vehicle_id", "left");
        $this->db->where("a.id",$id);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                $rs->location = mb_strtoupper($rs->location);
                $rs->ship_to_address = mb_strtoupper($rs->ship_to_address);
                $rs->transporter = mb_strtoupper($rs->transporter);
                $rs->ship_to = mb_strtoupper($rs->ship_to);
                $rs->company_to = mb_strtoupper($rs->company);
                $rs->department_to = mb_strtoupper($rs->department);
                $rs->waybill = mb_strtoupper($rs->waybill);
                $rs->others_remarks = mb_strtoupper($rs->others_remarks);
                if($rs->driver){
                    if($rs->cat=="in"){
                        $tempRs = (array) $rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object) $fullname;
                        $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                        $rs->ship_to_name = $this->viewShipto($rs->ship_to);
                        $rs->ship_to_company = $this->viewShiptoCompany($rs->ship_to);
                        $rs->driver_name = $this->viewDriver($rs->driver);
                        $arrData[$key] = $rs;
                    }else{
                        $tempRs = (array) $rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object) $fullname;
                        $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                        $rs->ship_to_name = $rs->ship_to;
                        $rs->location = $rs->company_to.", ".$rs->department_to;
                        $rs->ship_to_company = $rs->company_to."\n".$rs->department_to;
                        $rs->driver_name = $this->viewDriver($rs->driver);
                        $arrData[$key] = $rs;
                    }  
                }else{
                    if($rs->cat=="in"){
                        $tempRs = (array) $rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object) $fullname;
                        $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                        $rs->ship_to_name = $this->viewShipto($rs->ship_to);
                        $rs->ship_to_company = $this->viewShiptoCompany($rs->ship_to);
                        $rs->driver_name = "N/A";
                        $rs->plateno = "N/A";
                        $arrData[$key] = $rs;
                    }else{
                        $tempRs = (array) $rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object) $fullname;
                        $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                        $rs->ship_to_name = $rs->ship_to;
                        $rs->location = $rs->company_to.", ".$rs->department_to;
                        $rs->ship_to_company = $rs->company_to."\n".$rs->department_to;
                        $rs->plateno = "N/A";
                        $rs->driver_name = "N/A";
                        $arrData[$key] = $rs;
                    }  
                }
            }
            $data = array();
            $data['shipping_content'] = $this->viewItemContent($id);
            
            $tempData = array();
            foreach($arrData as $k=>$v){
                $v->company_details = $v->company."\n".$v->department."\n";
                if($v->last_edited_by==null || $v->last_edited_by==""){
                    $v->last_edited_by = "N/A";
                }
                $tempData[] = $v;
                
            }
            $data['shipping_main'] = $tempData;
            return $data;
        }else{
            return array();
        }
    }

    function viewShipto($id){
        $this->db->select("id, firstname, middlename, lastname, suffix, company_id, department_id, position");
        $this->db->from("gccmaster.tblemployees");
        $this->db->where("id",$id);
        $query = $this->db->get();
        $rs = $query->row();
        $tempRs = (array) $rs;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object) $fullname;
        $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
        return  $rs->display_name;
    }

    function viewShiptoCompany($id){
        $this->db->select("company_id, department_id, position");
        $this->db->from("gccmaster.tblemployees");
        $this->db->where("id",$id);
        $query = $this->db->get();
        $rs = $query->row();


        if(is_numeric($rs->company_id)){
            $company = $this->getEmploymentDetailsId("company_to", $rs->company_id, "name");
        }else{
            $company = $rs->company_id;
        }

        if(is_numeric($rs->department_id)){
            $department = $this->getEmploymentDetailsId("department_to", $rs->department_id, "name");
        }else{
            $department = $rs->department_id;
        }

        if(is_numeric($rs->position)){
            $position = $this->getEmploymentDetailsId("position_to", $rs->position, "name");
        }else{
            $position = $rs->position;
        }
        
        return  $company."\n".$department."\n".$position;
    }

    function viewDriver($id){
        $this->db->select("id, firstname, middlename, lastname, suffix");
        $this->db->from("gccmaster.tblemployees");
        $this->db->where("id",$id);
        $query = $this->db->get();
        $rs = $query->row();
        $tempRs = (array) $rs;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object) $fullname;
        $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
        return  $rs->display_name;
    }

    function approveShipping($id){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'approved_by' => $this->user_data['firstname'].' '.$this->user_data['lastname'],
            'approved_dt' => $date,
            'status' => "Approved",
        );
        if($id){
            $this->db->where('gcceforms.shipping.id', $id);
            $update = $this->db->update('gcceforms.shipping', $data);
            if($update){
                $message = "View Shipping - Approve shipping {$this->getShippingReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Shipping - Failed approve shipping {$this->getShippingReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            return $update;
        }
    }

    function disapproveShipping($id){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'disapproved_by' => $this->user_data['firstname'].' '.$this->user_data['lastname'],
            'disapproved_dt' => $date,
            'status' => "Disapproved",
        );
        if($id){
            $this->db->where('gcceforms.shipping.id', $id);
            $update = $this->db->update('gcceforms.shipping', $data);
            if($update){
                $message = "View Shipping - Disapprove shipping {$this->getShippingReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Shipping - Failed disapprove shipping {$this->getShippingReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            return $update;
        }
    }
    
    function receiveShipping($id){
        $this->input->post();
        $type = $this->db->get_where("gcceforms.shipping", array("id"=>$id))->row();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'received_by' => $this->user_data['firstname'].' '.$this->user_data['lastname'],
            'received_dt' => $date,
            'received_remarks' => $this->input->post('received_remarks'),
            'status' => "Received",
        );
        if($id){
            if($type->cat == 'in'){
                $get_assets = $this->db->get_where("gcceforms.shipping_body", array("shipping_id"=>$id, "cat"=>"asset"));
                foreach($get_assets->result() as $assets){
                    $update_data = array(
                        "area_id" => $type->area_id
                    );
                    $this->db->where("assetacode", $assets->stock_code);
                    $this->db->update("gccasset.assets", $update_data);
                }
            }
            
            $this->db->where('gcceforms.shipping.id', $id);
            $update = $this->db->update('gcceforms.shipping', $data);
            if($update){
                $message = "View Shipping - Receive shipping {$this->getShippingReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Shipping - Failed receive shipping {$this->getShippingReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            return $update;
            
        }
    }

    function undoApproveShipping($id){
        $data = array(
            'status' => "Pending",
        );
        if($id){
            $this->db->where('gcceforms.shipping.id', $id);
            $update = $this->db->update('gcceforms.shipping', $data);
            if($update){
                $message = "View Shipping - Undo approve shipping {$this->getShippingReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Shipping - Failed undo approve shipping {$this->getShippingReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            return $update;
        }
    }

    function undoDisapproveShipping($id){
        $data = array(
            'status' => "Pending",
        );
        if($id){
            $this->db->where('gcceforms.shipping.id', $id);
            $update = $this->db->update('gcceforms.shipping', $data);
            if($update){
                $message = "View Shipping - Undo disapprove shipping {$this->getShippingReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Shipping - Failed undo disapprove shipping {$this->getShippingReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            return $update;
        }
    }

    function undoReceiveShipping($id){
        $type = $this->db->get_where("gcceforms.shipping", array("id"=>$id))->row();
        $data = array(
            'status' => "Approved",
        );
        if($id){
            if($type->cat == 'in'){
                $get_assets = $this->db->get_where("gcceforms.shipping_body", array("shipping_id"=>$id, "cat"=>"asset"));
                foreach($get_assets->result() as $assets){
                    $update_data = array(
                        "area_id" => $assets->asset_location
                    );
                    $this->db->where("assetacode", $assets->stock_code);
                    $this->db->update("gccasset.assets", $update_data);
                }
            }
            $this->db->where('gcceforms.shipping.id', $id);
            $update = $this->db->update('gcceforms.shipping', $data);
            if($update){
                $message = "View Shipping - Undo receive shipping {$this->getShippingReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Shipping - Failed undo receive shipping {$this->getShippingReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            return $update;
        }
    }

    function undoCancelShipping($id){
        $data = array(
            'status' => "Pending",
        );
        if($id){
            $this->db->where('gcceforms.shipping.id', $id);
            $update = $this->db->update('gcceforms.shipping', $data);
            if($update){
                $message = "View Shipping - Undo cancel shipping {$this->getShippingReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Shipping - Failed undo cancel shipping {$this->getShippingReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            return $update;
        }
    }

    function cancelShipping($id){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'cancelled_by' => $this->user_data['firstname'].' '.$this->user_data['lastname'],
            'cancelled_dt' => $date,
            'cancelled_remarks' => $this->input->post('cancelled_remarks'),
            'status' => "Cancelled",
        );
        if($id){
            $this->db->where('gcceforms.shipping.id', $id);
            $update = $this->db->update('gcceforms.shipping', $data);
            if($update){
                $message = "View Shipping - Cancel shipping {$this->getShippingReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "View Shipping - Failed cancel shipping {$this->getShippingReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            return $update;
        }
    }

    function editShippingDetails($id){
        $this->db->select("a.*, b.description AS company, c.description AS department, d.firstname, d.middlename,d.lastname, e.location, f.name AS vehicle_name, f.plateno");
        $this->db->from('gcceforms.shipping a');
        $this->db->join("gcchris.tblcompanies b","b.id = a.company_from","left");
        $this->db->join("gcchris.tbldepartments c","c.id = a.company_from","left");
        $this->db->join("gccmaster.tblemployees d","d.id = a.requested_by","left");
        $this->db->join("gccasset.location e","e.id = a.area_id","left");
        $this->db->join("gccasset.vehicles f","f.id = a.vehicle_id");
        $this->db->where('a.id',$id);
        $query = $this->db->get();
        return $query->row();
    }
        
    function updateShipping($id){
        $post = $this->input->post();
        $cat = $this->input->post("cat");
        

            $emp_details = $this->db->query("SELECT position, company_id, department_id FROM gccmaster.tblemployees WHERE id = '$post[ship_to]' AND employee_status='Active'");
            $emp_data = $emp_details->row_array();
            
            
            if(is_numeric($emp_data['company_id'])){
                $company_to = $emp_data['company_id'];
            }else{
                $company_to = $this->getEmploymentDetailsId("company_to", $emp_data['company_id'], "id");
            }

            if(is_numeric($emp_data['department_id'])){
                $department_to = $emp_data['department_id'];
            }else{
                $department_to = $this->getEmploymentDetailsId("department_to", $emp_data['department_id'], "id");
            }

            if(is_numeric($emp_data['position'])){
                $position_to = $emp_data['position'];
            }else{
                $position_to = $this->getEmploymentDetailsId("position_to", $emp_data['position'], "id");
            }

            if($cat=="in"){
                $x = explode("\n", $this->input->post('company'));
                $ship_to = $this->input->post('ship_to');
                $ship_to_address = $this->input->post('ship_to_address');
                $ship_to_address = (isset($ship_to_address) && $ship_to_address)? $ship_to_address : "";
            }else{
                $x[0] = $this->input->post('company_ex');
                $x[1] = $this->input->post('department_ex');
                $x[2] = "";
                
                $company_to = $x[0];
                $department_to = $x[1];

                $ship_to = $this->input->post('ship_to_ex');
                $ship_to_address = $this->input->post('address_ex');
                $ship_to_address = (isset($ship_to_address) && $ship_to_address)? $ship_to_address : "";
            }

        $date = date('Y-m-d H:i:s');
     
        if($this->input->post('type')=="service"){
            $serviceType = "1";
        }else{
            $serviceType = "0";
        }
        if($this->input->post('type')=="others"){
            $otherType = "1";
        }else{
            $otherType = "0";
        }
        $area_id = $this->input->post('location');
        $area_id = (isset($area_id) && $area_id)? $area_id : 0;

		$driver = $this->input->post('driver');
		$driver = (isset($driver) && $driver)? $driver : "";
		
		$transporter = $this->input->post('transporter');
		$transporter = (isset($transporter) && $transporter)? $transporter : "";
		
		$other_remarks = $this->input->post('others_remarks');
        $other_remarks = (isset($other_remarks) && $other_remarks)? $other_remarks : "";
        
        $waybill = $this->input->post('waybill');
		$waybill = (isset($waybill) && $waybill)? $waybill : " ";
        $data = array(
            'requested_by' => $this->input->post('requested_by'),
            'priority' => $this->input->post('priority'),
            'last_edited_id' => $this->user_data['id'],
            'last_edited_by' => $this->user_data['firstname'].' '.$this->user_data['lastname'],
            'last_edited_dt' => $date,        
            'company_from' => $this->input->post('company_from'),
            'department_from' => $this->input->post('department_from'),
            'ship_to' => $ship_to,
            'company_to' => $company_to,
            'department_to' => $department_to,
            'position_to' => $position_to,
            'ship_date' => date('Y-m-d H:i:s', strtotime($this->input->post('ship_date'))),
            'area_id' => $area_id,
            'ship_to_address' => $ship_to_address,
            'is_service' => $serviceType,
            'is_others' => $otherType,
            'others_remarks' => $other_remarks,
            'vehicle_id' => $this->input->post('vehicle'),
            'driver' => $driver,
            'transporter' => $transporter,
            'waybill' => $waybill,
            'cat' => $this->input->post('cat'),
        );
        if($id){
            $this->db->where('gcceforms.shipping.id', $id);
            $update = $this->db->update('gcceforms.shipping', $data);
            if($update){
                $message = "Edit Shipping - Update shipping {$this->getShippingReference($id)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "Edit Shipping - Failed update shipping {$this->getShippingReference($id)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            return $update;
        }
    }

    function printShippingDetails($id){
        $ids=implode($id);
        $this->db->select("a.reference_no, b.description AS company, c.description AS department, d.firstname, d.middlename,d.lastname, a.priority, a.status, a.created_by, a.created_dt, a.last_edited_by, a.last_edited_dt, e.location, a.ship_to_address, a.ship_date, f.plateno, a.transporter, a.approved_by, a.approved_dt, a.disapproved_by, a.disapproved_dt, a.cancelled_by, a.cancelled_dt, a.received_by, a.received_dt, a.received_remarks, g.shipping_id, g.asset_id, g.stock_code, g.quantity, g.uom, g.description, g.item_purpose, g.cat");
        $this->db->from("gcceforms.shipping a");
        $this->db->join("gcchris.tblcompanies b","b.id = a.company_from","left");
        $this->db->join("gcchris.tbldepartments c","c.id = a.company_from","left");
        $this->db->join("gccmaster.tblemployees d","d.id = a.requested_by","left");
        $this->db->join("gccasset.location e","e.id = a.area_id","left");
        $this->db->join("gccasset.vehicles f","f.id = a.vehicle_id");
        $this->db->join("gcceforms.shipping_body g","g.shipping_id = a.id");
        $this->db->where("a.id",$ids);
        $query = $this->db->get();
        return $query->row();
    }

    function updateItemModal($get){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'shipping_id' => $get,
            'stock_code' => $this->input->post('items'),
            'quantity' => $this->input->post('quantity'),
            'uom' => $this->input->post('uom'),
            'description' => $this->input->post('description'),
            'item_purpose' => $this->input->post('purpose'),
            'cat' => "item"
        );
        if($get){
            $update = $this->db->insert('gcceforms.shipping_body', $data);
            if($update){
                $message = "Edit Shipping - Update item {$this->input->post('items')}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "Edit Shipping - Failed update item {$this->input->post('items')}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "update", $type, "gcceforms", $table);
            return $update;
        }
    }

    function deleteEditContent($id){
        $item = $this->db->get_where("gcceforms.shipping_body", array("id"=>$id))->row('stock_code');
        $delete = $this->db->query("DELETE FROM gcceforms.shipping_body WHERE id=$id");
        if($delete){
            $message = "Edit Shipping - Delete item {$item} from shipping.";
            $type = "success";
            $table = "user";
        }else{
            $message = "Edit Shipping - Failed delete item {$item} from shipping.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "delete", $type, "gcceforms", $table);
        return $delete;
    }

    function clearEditContents($data){
        $delete = $this->db->query("DELETE FROM gcceforms.shipping_body WHERE shipping_id=$data");
        if($delete){
            $message = "Edit Shipping - Delete all item from shipping {$this->getShippingReference($data)}.";
            $type = "success";
            $table = "user";
        }else{
            $message = "Edit Shipping - Failed delete all item from shipping {$this->getShippingReference($data)}.";
            $type = "error";
            $table = "system";
        }
        $this->core_layout->setEventLog($message, "delete", $type, "gcceforms", $table);
        return $delete;
    }

    function updateAssetModal($get){
        $this->input->post();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'shipping_id' => $get,
            'stock_code' => $this->input->post('assets'),
            'quantity' => $this->input->post('quantity2'),
            'uom' => $this->input->post('uom2'),
            'description' => $this->input->post('description2'),
            'item_purpose' => $this->input->post('purpose2'),
            'cat' => "asset"
        );
        if($get){
            $insert = $this->db->insert('gcceforms.shipping_body', $data);
            if($insert){
                $message = "Edit Shipping - Add item {$this->input->post('assets')} of shipping {$this->getShippingReference($get)}.";
                $type = "success";
                $table = "user";
            }else{
                $message = "Edit Shipping - Failed add item {$this->input->post('assets')} of shipping {$this->getShippingReference($get)}.";
                $type = "error";
                $table = "system";
            }
            $this->core_layout->setEventLog($message, "add", $type, "gcceforms", $table);
            return $insert;
        }
    }

    function archiveDatatableRequest(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"6", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowCount = 0;
        $rowData = array();
        if(!$search){
            $rowData = $this->archive_post($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->archive_post_count();
        }

        if($search){
            $rowData = $this->archive_searched_item($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->archive_searched_item_count($search);
            $this->core_layout->setEventLog("Archive Shipping - Search {$search} in datatable.", "search", "success", "gcceforms", "user");
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function archive_post($limit=10, $offset=0, $sortBy, $sortOrder){
        $date= date("Y-m-d", strtotime("-1 year"));
        $sql = "a.id, a.status, a.priority, a.reference_no,a.department_to, d.firstname,a.company_from, d.middlename, d.lastname, d.suffix, a.cat, c.description AS file_under, a.company_to, a.ship_to, b.description, a.ship_date, a.created_dt";
        $this->db->select($sql);
        $this->db->from("gcceforms.shipping a");
        $this->db->join("gcchris.tblcompanies c", "a.company_from = c.id", "LEFT");
        $this->db->join("gcceforms.shipping_body b", "a.id = b.shipping_id", "LEFT");
        $this->db->join("gccmaster.tblemployees d", "a.ship_to = d.id", "LEFT");
        $this->db->where("a.created_dt <=", $date);
        $this->db->or_where_in("a.status","Cancelled");      
          
        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        if($sortBy[$i]['data']=="firstname"){
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
        }else{
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                if(is_numeric($rs->company_from)){
                    $rs->file_under = $rs->file_under;
                }else{
                    $rs->file_under = $rs->company_from;
                }
                if($rs->cat=="in"){
                    if(is_numeric($rs->ship_to)){
                        $tempRs = (array) $rs;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object) $fullname;
                        $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    }else{
                        $rs->display_name = $rs->ship_to;
                    }

                    $arrData[$key] = $rs;
                }else{
                    $rs->display_name = $rs->ship_to;
                    $arrData[$key] = $rs;
                }
            }
            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }
            return $data;
        }else{
            return array();
        }
    }

    private function archive_post_count(){
        $date= date("Y-m-d", strtotime("-1 year"));
        $this->db->from("gcceforms.shipping a");
        $this->db->join("gcchris.tblcompanies c", "a.company_from = c.id", "LEFT");
        $this->db->join("gcceforms.shipping_body b", "a.id = b.shipping_id", "LEFT");
        $this->db->join("gccmaster.tblemployees d", "a.ship_to = d.id", "LEFT");
        $this->db->where("a.created_dt <=", $date);
        $this->db->or_where_in("a.status","Cancelled");   
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function archive_searched_item($search=null, $limit=10, $offset=0, $sortBy, $sortOrder){
        $date= date("Y-m-d", strtotime("-1 year"));
        if($search){
            $filterFields = array("a.id", "a.status", "a.reference_no", "a.priority", "a.ship_to", "b.description", "a.ship_date", "c.description");
            $sql = "a.id, a.status, a.priority, a.reference_no,a.department_to, d.firstname,a.company_from, d.middlename, d.lastname, d.suffix, a.cat, c.description AS file_under, a.company_to, a.ship_to, b.description, a.ship_date, a.created_dt";
            $this->db->select($sql);
            $this->db->from("gcceforms.shipping a");
            $this->db->join("gcchris.tblcompanies c", "a.company_from = c.id", "LEFT");
            $this->db->join("gcceforms.shipping_body b", "a.id = b.shipping_id", "LEFT");
            $this->db->join("gccmaster.tblemployees d", "a.ship_to = d.id", "LEFT");
            //$this->db->where("a.created_dt <=", $date);
            //$this->db->or_where_in("a.status","Cancelled");  
            $this->db->where("(a.status='Cancelled' OR a.created_dt<='$date')");
           
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();

            $i = $sortOrder[0]['column'];
            if($sortBy[$i]['data']=="firstname"){
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("a.ship_to", $sortOrder[0]['dir']);
            }else{
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }

            $query = $this->db->get();

            if($query->num_rows() > 0){
                $arrData = array();
                foreach($query->result() as $key => $rs){
                    if(is_numeric($rs->company_from)){
                        $rs->file_under = $rs->file_under;
                    }else{
                        $rs->file_under = $rs->company_from;
                    }
                    if($rs->cat=="in"){
                        if(is_numeric($rs->ship_to)){
                            $tempRs = (array) $rs;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object) $fullname;
                            $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                        }else{
                            $rs->display_name = $rs->ship_to;
                        }
    
                        $arrData[$key] = $rs;
                    }else{
                        $rs->display_name = $rs->ship_to;
                        $arrData[$key] = $rs;
                    }
                }
                $data = array();
                foreach($arrData as $k=>$v){
                    $data[] = $v;
                }
                return $data;
            }else{
                return array();
            }
        }else{
            return array();
        }
    }

    private function archive_searched_item_count($search=null){
        $rowCount = 0;
        if($search){
            $date= date("Y-m-d", strtotime("-1 year"));
            $sql = "a.id, a.status, a.priority, a.reference_no,a.department_to, d.firstname,a.company_from, d.middlename, d.lastname, d.suffix, a.cat, c.description AS file_under, a.company_to, a.ship_to, b.description, a.ship_date, a.created_dt";
            $filterFields = array("a.id", "a.status", "a.reference_no", "a.priority", "a.ship_to", "b.description", "a.ship_date", "c.description");
            $this->db->select($sql);
            $this->db->from("gcceforms.shipping a");
            $this->db->join("gcceforms.shipping_body b", "a.id = b.shipping_id", "LEFT");
            $this->db->join("gcchris.tblcompanies c", "a.company_from = c.code", "LEFT");
            $this->db->join("gccmaster.tblemployees d", "a.ship_to = d.id", "LEFT");
            $this->db->where("(a.status='Cancelled' OR a.created_dt<='$date')");
            $this->db->group_start();
            foreach($filterFields as $key => $field){
                if($key == 0){ $this->db->like($field, $search, "both"); }
                else{ $this->db->or_like($field, $search, "both"); }
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    function getDaily(){
        $resultset = array();
        $order_val = "";
        $post = $this->input->post();
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowCount = 0;
        $rowData = array();
       
            $rowData = $this->get_all_post_daily($limit, $offset, $sortBy, $sortOrder);
           
       

        $totalNotFiltered = $rowCount;

        
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_post_daily($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        
        $check = date('Y-m-d');
        $sql = "a.id, a.reference_no, d.firstname, d.lastname, a.ship_to, d.middlename, d.suffix, c.description AS file_under, b.description";
        $this->db->select($sql);
        $this->db->from("gcceforms.shipping a");
        $this->db->join("gcchris.tblcompanies c", "a.company_from = c.id", "LEFT");
        $this->db->join("gcceforms.shipping_body b", "a.id = b.shipping_id", "LEFT");
        $this->db->join("gccmaster.tblemployees d", "a.ship_to = d.id", "LEFT");
        $this->db->like('a.created_dt', $check);
        $this->db->limit($limit, $offset);

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                if(is_numeric($rs->ship_to)){
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                }else{
                    $rs->display_name = $rs->ship_to;
                }
             
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }

            return $data;
        }else{
            return array();
        }
    }
    function getWeekly(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"1", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
        $limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
        $offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"])? $post["order"]: $order_val;
        
        $rowCount = 0;
        $rowData = array();
       
            $rowData = $this->get_all_post_weekly($limit, $offset, $sortBy, $sortOrder);
           
       

        $totalNotFiltered = $rowCount;

        
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_post_weekly($limit=10, $offset=0, $sortBy=null, $sortOrder="DESC"){
        
        $check =date('Y-m-d',strtotime("-7 days"));
        $sql = "a.id, a.reference_no, d.firstname, d.lastname, a.ship_to, d.middlename, d.suffix, c.description AS file_under, b.description";
        $this->db->select($sql);
        $this->db->from("gcceforms.shipping a");
        $this->db->join("gcchris.tblcompanies c", "a.company_from = c.id", "LEFT");
        $this->db->join("gcceforms.shipping_body b", "a.id = b.shipping_id", "LEFT");
        $this->db->join("gccmaster.tblemployees d", "a.ship_to = d.id", "LEFT");
        $this->db->where('a.created_dt >= ', $check);
        $this->db->limit($limit, $offset);

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if($query->num_rows() > 0){
            $arrData = array();
            foreach($query->result() as $key => $rs){
                if(is_numeric($rs->ship_to)){
                    $tempRs = (array) $rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object) $fullname;
                    $rs->display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                }else{
                    $rs->display_name = $rs->ship_to;
                }
             
                $arrData[$key] = $rs;
            }

            $data = array();
            foreach($arrData as $k=>$v){
                $data[] = $v;
            }

            return $data;
        }else{
            return array();
        }
    }
    function m_get_shipping_analytics_for_dashboard(){
            $this->db->select("a.status, COUNT(a.id) AS count");  
            $this->db->from("gcceforms.shipping a");
            $this->db->group_by("a.status");
            $query = $this->db->get()->result();
            $result = json_decode(json_encode($query));

            $data = array($result[3], $result[0], $result[2], $result[4], $result[1]);
            $results = json_decode(json_encode($data));
            return $results;
        }

    function generateDailyShippingSummary($date=null){
        $currentDate = ($date)? date("Y-m-d", strtotime($date)): date("Y-m-d");

        $sqlSelect = "a.id, a.reference_no, a.status, a.priority, b.lastname as r_lastname, b.firstname as r_firstname, b.middlename as r_middlename, b.suffix as r_suffix, ";
        $sqlSelect .= "ship_to, IFNULL(comp_to.description, IFNULL(a.company_to, '---')) as company_to, IFNULL(dept_to.description, IFNULL(a.department_to, '---')) as department_to, ";
        $sqlSelect .= "IFNULL(pos_to.name, IFNULL(a.position_to, '---')) as position_to, c.lastname as d_lastname, c.firstname as d_firstname, c.middlename as d_middlename, c.suffix as d_suffix, ";
        $sqlSelect .= "a.ship_to_address, a.ship_date, a.is_service, a.is_others, d.name as vehicle_name, d.plateno, e.location, ";
        $sqlSelect .= "a.others_remarks, a.transporter, a.cat, IF(a.cat='ex', 'EXTERNAL SHIPPING', 'INTERNAL SHIPPING') as shipping_type, ";
        $sqlSelect .= "IFNULL(f.description, IFNULL(a.company_from, '---')) as company_from, IFNULL(g.description, IFNULL(a.department_from, '---')) as department_from, ";
        $sqlSelect .= "a.created_by";

        $this->db->select($sqlSelect);
        $this->db->from("gcceforms.shipping a");
        $this->db->join("gccmaster.tblemployees b", "b.id = a.requested_by", "LEFT");
        $this->db->join("gccmaster.tblemployees c", "c.id = a.driver", "LEFT");
        $this->db->join("gccasset.vehicles d", "d.id = a.vehicle_id", "LEFT");
        $this->db->join("gccasset.location e", "e.id = a.area_id", "LEFT");
        $this->db->join("gcchris.tblcompanies f", "f.id = a.company_from", "LEFT");
        $this->db->join("gcchris.tbldepartments g", "g.id = a.department_from", "LEFT");
        $this->db->join("gcchris.tblcompanies comp_to", "comp_to.id = a.company_to", "LEFT");
        $this->db->join("gcchris.tbldepartments dept_to", "dept_to.id = a.department_to", "LEFT");
        $this->db->join("gcchris.tblposition pos_to", "pos_to.id = a.position_to", "LEFT");
        $this->db->like("a.created_dt", $currentDate, "both");
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $arrData = array();

            foreach($query->result() as $rs){
                $tempRequestor = array();
                $tempRequestor["lastname"] = $rs->r_lastname;
                $tempRequestor["firstname"] = $rs->r_firstname;
                $tempRequestor["middlename"] = $rs->r_middlename;
                $tempRequestor["suffix"] = $rs->r_suffix;

                $displayName = $this->core_layout->getDisplayName($tempRequestor);
                $tempFullname = (object) $displayName;
                $rs->requestor_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";

                $tempDriver = array();
                $tempDriver["lastname"] = $rs->d_lastname;
                $tempDriver["firstname"] = $rs->d_firstname;
                $tempDriver["middlename"] = $rs->d_middlename;
                $tempDriver["suffix"] = $rs->d_suffix;

                $displayName = $this->core_layout->getDisplayName($tempDriver);
                $tempFullname = (object) $displayName;
                $rs->driver_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";

                if(is_numeric($rs->ship_to)){
                    $queryShipTo = $this->db->get_where("gccmaster.tblemployees", array("id"=>$rs->ship_to));
                    if($queryShipTo->num_rows() == 1){
                        $tempRow = $queryShipTo->row_array();
                        $displayName = $this->core_layout->getDisplayName($tempRow);
                        $tempFullname = (object) $displayName;
                        $rs->ship_to = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Shipped To";
                    }else{
                        $rs->ship_to = "No Assigned Shipped To";
                    }
                }

                $shippingContent = $this->db->get_where("gcceforms.shipping_body", array("shipping_id"=>$rs->id));
                if($shippingContent->num_rows() > 0){
                    $arrDataContent = array();
                    foreach ($shippingContent->result() as $rsx) {
                        if($rsx->cat == "asset"){
                            $this->db->from("gccasset.assets");
                            if($rsx->asset_id !== 0){
                                $this->db->where("id", $rsx->asset_id);
                            }else{
                                $this->db->where("assetacode", $rsx->asset_id);
                            }
                            $queryAsset = $this->db->get();
                            if($queryAsset->num_rows() == 1){
                                $rowAsset = $queryAsset->row();
                                if($rowAsset->name){
                                    $rsx->description = $rowAsset->name;
                                }
                            }
                        }

                        if($rsx->cat == "vehicles"){
                            $this->db->from("gccasset.vehicles");
                            if($rsx->asset_id !== 0){
                                $this->db->where("id", $rsx->asset_id);
                            }else{
                                $this->db->where("gen_code", $rsx->asset_id);
                            }
                            $queryAsset = $this->db->get();
                            if($queryAsset->num_rows() == 1){
                                $rowAsset = $queryAsset->row();
                                if($rowAsset->name){
                                    $rsx->description = $rowAsset->name;
                                }
                            }
                        }

                        $arrDataContent[] = $rsx;
                    }
                    $rs->shipping_content = $arrDataContent;
                }

                $arrData[] = $rs;
            }

            return $arrData;
        }else{
            return false;
        }
    }

    function massActionShipping(){
        $id = $this->input->post('data');
        $action = $this->input->post('action');
        $date = date('Y-m-d H:i:s');
        $resultSet = array();
        if($action == "Approved"){
            $data = array(
                "status"=>$action,
                'approved_by' => $this->user_data['firstname'].' '.$this->user_data['lastname'],
                'approved_dt' => $date,
            );
        }else if($action == "Disapproved"){
            $data = array(
                "status"=>$action,
                'disapproved_by' => $this->user_data['firstname'].' '.$this->user_data['lastname'],
                'disapproved_dt' => $date,
            );
        }else{
            $data = array(
                "status"=>$action,
                'cancelled_by' => $this->user_data['firstname'].' '.$this->user_data['lastname'],
                'cancelled_dt' => $date,
            );
            
        }
        
        if($id){
            foreach($id as $shipping_id){

                $ref = $this->db->get_where('gcceforms.shipping', array("id"=>$shipping_id))->row('reference_no');
                $shipping_ref[] = $ref;

                $this->db->where('gcceforms.shipping.id', $shipping_id);
                $update = $this->db->update('gcceforms.shipping', $data);

                $resultSet['message'] = "Shipping Advice Entries ".implode(",",$shipping_ref)." has been ".$action."!";
            }
            if($update){
                return $resultSet;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function telegram_config_if_exist($module, $data){
        $this->db->where("module","shipping_advice");
        $this->db->order_by("created_at","DESC");
        $telegram_details = $this->db->get("gcceforms.telegram_config");
        $details = $telegram_details->row();
        $count = $telegram_details->num_rows();
        if($data == 'count'){
            return $count;
        }else{
            return $details;
        }
    }

    public function telegram($msg) {
        $data = $this->telegram_config_if_exist('shipping_advice', 'data');

        $telegrambot=$data->telegram_bot_token;
        $telegramchatid= $data->chat_id;
        $url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';$data=array('chat_id'=>$telegramchatid,'text'=>$msg,'parse_mode'=>'html');
        $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),'ignore_errors'=>true),);
        $context=stream_context_create($options);
        $result=file_get_contents($url,false,$context);
        return $result;
    }

    public function addTelegramConfig(){
        $post = $this->input->post();
        $data = array(
            "chat_id"=>$post['chat_id'],
            "telegram_bot_token"=>$post['telegram_bot_token'],
            "module"=>$post['module'],
            "created_at"=>date("Y-m-d H:i:s")
        );
        if($post['id'] == NULL){
            $result = $this->db->insert('gcceforms.telegram_config', $data);
        }else{
            $result = $this->db->update('gcceforms.telegram_config', $data, array("id"=>$post['id']));
        }
        return $result;
    }

    public function loadTelegramConfig(){
        $result = array();
        $post = $this->input->post();
        
        $this->db->where("module", $post['module']);
        $this->db->order_by("created_at", "DESC");
        $data = $this->db->get("gcceforms.telegram_config");
        $x = $data->row();
        $count = $data->num_rows();
        if($count > 0){
            $result['chat_id'] = $x->chat_id;
            $result['telegram_bot_token'] = $x->telegram_bot_token;
            $result['id'] = $x->id;
        }

        return $result;
    }

    function exportData($export){
        if($export == 1){
            $this->core_layout->setEventLog("Shipping Masterfile - Export excel file of Shipping Masterfile.","export", "success", "gcceforms", "user");
        }elseif($export == 2){
            $this->core_layout->setEventLog("Shipping Masterfile - Export csv file of Shipping Masterfile.","export", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Shipping Masterfile - Export pdf file of Shipping Masterfile.","export", "success", "gcceforms", "user");
        }
    }

    function exportDataArchived($export){
        if($export == 1){
            $this->core_layout->setEventLog("Archived Shipping - Export excel file of Shipping Masterfile.","export", "success", "gcceforms", "user");
        }elseif($export == 2){
            $this->core_layout->setEventLog("Archived Shipping - Export csv file of Shipping Masterfile.","export", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Archived Shipping - Export pdf file of Shipping Masterfile.","export", "success", "gcceforms", "user");
        }
    }

}