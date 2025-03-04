<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Accountability_m extends CI_Model {
    protected $eformsTable = "gcceforms";
    protected $acctLogsTable = "gcceforms.accountability_logs";
    protected $acctTable = "gcceforms.accountability";
    protected $acctBodyTable = "gcceforms.accountability_body";
    protected $assetTable = "gccasset.assets";
    protected $vehicleTable = "gccasset.vehicles";
    protected $borrTable = "gcceforms.borrowing";
    protected $borrBodyTable = "gcceforms.borrowing_body";

    public function __construct() {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        date_default_timezone_set("Asia/Manila");
    }

    function masterfileList() {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $advanced_search = (isset($post["advanced_search"]) && $post["advanced_search"]) ? $post["advanced_search"] : null;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql']) ? $post["query_builder"]['sql'] : array();
        $status = (isset($post['status']) && $post['status']) ? ucwords(str_replace("_", " ", $post['status'])) : null; //clicked in portal dashboard
        $company = (isset($post['company']) && $post['company']) ? $post['company'] : null; //clicked in portal dashboard
        $rowCount = 0;
        $rowData = array();

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        $rowData = $this->get_all_items($query_builder, $advanced_search, $search, $limit, $offset, $sortBy, $sortOrder, $status, $company, $view_by_company, $companyDescription);
        $rowCount = $this->get_all_items_count($query_builder, $advanced_search, $search, $status,$company, $view_by_company, $companyDescription);

        // if (!$search) {
        //     $rowData = $this->get_all_postv1($query_builder, $advanced_search, $limit, $offset, $sortBy, $sortOrder, $status,$company);
        //     $rowCount = $this->get_all_post_countv1($query_builder, $advanced_search, $status,$company);
        // }

        // if ($search) {
        //     $rowData = $this->get_searched_itemv1($query_builder, $advanced_search, $search, $limit, $offset, $sortBy, $sortOrder, $status,$company);
        //     $rowCount = $this->get_searched_item_countv1($query_builder, $advanced_search, $search, $status,$company);
        //     $this->core_layout->setEventLog("Accountability Masterfile - Search ".$search." in masterfile.", "search", "success", "gcceforms", "user");
        // }
        
        if ($search) {
            $this->core_layout->setEventLog("Accountability Masterfile - Search ".$search." in masterfile.", "search", "success", "gcceforms", "user");
        }

        if($query_builder){
            $this->core_layout->setEventLog("Accountability Masterfile - Generate masterfile through query builder `{$query_builder}`.", "search", "success", "gcceforms", "user");
        }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_items($query_builder, $advanced_search, $search, $limit, $offset, $sortBy, $sortOrder, $status, $company, $view_by_company = false, $companyDescription = null){
        $date = date("Y-m-d", strtotime("-1 year"));
        $data = array();

        $sqlSelect = "a.id, a.status, a.reference_no, a.company, a.date_issued, a.is_contract, b.asset_id, b.asset_code, b.type, c.firstname, c.lastname, c.middlename, c.suffix, d.contractor";

        $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "b.amount", "b.type", "asset.name", "vehicle.name", "c.company_id", "c.department_id", "c.firstname", "c.lastname", "c.middlename", "c.suffix", "d.contractor", "d.company", "e.description");
        
        $this->db->select($sqlSelect);
        $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
        $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcompanies e', 'e.id = a.company OR e.description = a.company', 'left');
        
        if ($search || $advanced_search) {
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        }

        $this->db->from('gcceforms.accountability a');

        $this->db->where_not_in('a.status', array('Released', 'Cancelled'));
        $this->db->where("DATE(a.date_issued) >= '$date'", NULL, FALSE);

        if ($view_by_company) {
            $this->db->where('a.company', $this->user_data['company']);

            if ($companyDescription) {
                $this->db->or_where('a.company', $companyDescription);
            }
        }

        if ($query_builder) { 
            $this->db->where($query_builder); 
        }

        if (isset($search) && $search) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if($key == 0){
                    $this->db->like($field, $search, "both");
                }else{
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
        }

        if($status){
            $this->db->where('a.status', $status);
        }

        if (isset($advanced_search) && $advanced_search) {
            if(isset($advanced_search['company']) && $advanced_search['company']){
                $this->db->where("a.company", $advanced_search['company']);
            }
            if(isset($advanced_search["company"])){
                unset($advanced_search['company']);
            }

            $advanced_search['a.reference_no'] = $advanced_search['reference_no'];
            unset($advanced_search['reference_no']);

            $advanced_search['a.status'] = $advanced_search['status'];
            unset($advanced_search['status']);

            $advanced_search['b.description'] = $advanced_search['description'];
            unset($advanced_search['description']);

            $this->db->like($advanced_search, "both");
        }

        if ($limit != -1) { 
            $this->db->limit($limit, $offset); 
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();

            foreach ($query->result() as $key => $rs) {
                $rs->company = strtoupper(is_numeric($rs->company) ? $this->getCompany($rs->company) : $rs->company);
                $rs->asset_name = $this->getAssetVehiclename($rs->asset_id, $rs->type);

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;

                $tempname = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->display_name = $rs->is_contract ? $rs->contractor : $tempname;

                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) { 
                $data[] = $v; 
            }
        }

        return $data;
    }

    function get_all_items_count($query_builder, $advanced_search, $search, $status,$company, $view_by_company = false, $companyDescription = null){
        $date = date("Y-m-d", strtotime("-1 year"));
        $sqlSelect = "a.id, a.status, a.reference_no, a.company, a.date_issued, a.is_contract, b.asset_id, b.asset_code, b.type, c.firstname, c.lastname, c.middlename, c.suffix, d.contractor";

        $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "b.amount", "b.type", "asset.name", "vehicle.name", "c.company_id", "c.department_id", "c.firstname", "c.lastname", "c.middlename", "c.suffix", "d.contractor", "d.company", "e.description");

        $this->db->select($sqlSelect);
        $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
        $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');
        
        if ($search || $advanced_search) {
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        }

        $this->db->from('gcceforms.accountability a');

        $this->db->where_not_in('a.status', array('Released', 'Cancelled'));
        $this->db->where("DATE(a.date_issued) >= '$date'", NULL, FALSE);

        if ($view_by_company) {
            $this->db->where('a.company', $this->user_data['company']);

            if ($companyDescription) {
                $this->db->or_where('a.company', $companyDescription);
            }
        }

        if ($query_builder) { 
            $this->db->where($query_builder); 
        }

        if (isset($search) && $search) {
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if($key == 0){
                    $this->db->like($field, $search, "both");
                }else{
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
        }

        if ($status) {
            $this->db->where('a.status', $status);
        }

        if (isset($advanced_search) && $advanced_search) {

            if (isset($advanced_search['company']) && $advanced_search['company']) {
                $this->db->where("a.company", $advanced_search['company']);
            }
            
            if (isset($advanced_search["company"])) {
                unset($advanced_search['company']);
            }

            $advanced_search['a.reference_no'] = $advanced_search['reference_no'];
            unset($advanced_search['reference_no']);

            $advanced_search['a.status'] = $advanced_search['status'];
            unset($advanced_search['status']);

            $advanced_search['b.description'] = $advanced_search['description'];
            unset($advanced_search['description']);

            $this->db->like($advanced_search, "both");
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    /** v1 */
    private function get_all_postv1($query_builder = null, $advanced_search, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null, $company=null) {
        $date = date("Y-m-d", strtotime("-2 year"));
        $sqlSelect = "a.id, UPPER(a.status), a.reference_no, a.company, c.firstname, UPPER(asset.name) as asset_name, a.date_issued, a.*, 
            b.asset_code, UPPER(b.description), b.type, b.amount,  vehicle.name as vehicle_name, c.company_id as temp_company, 
            c.department_id as temp_department, c.lastname, c.middlename, c.suffix, d.contractor, 
            IF(a.is_contract = '1', 
                IFNULL(UPPER(d.company), IFNULL(UPPER(a.company), UPPER(e.description))), 
                IFNULL(UPPER(a.company), UPPER(e.description))) as company";

        $this->db->select($sqlSelect);
        $this->db->from('gcceforms.accountability a');
        $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
        $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
        $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');
        $this->db->where("a.status !=", "Released");
        $this->db->where("a.status !=", "Cancelled");
        $this->db->where("a.date_issued >=", $date);
        if ($query_builder) { $this->db->where($query_builder); }
        if ($limit != -1) { $this->db->limit($limit, $offset); }
        if (isset($advanced_search)) {
            if(isset($advanced_search['company']) && $advanced_search['company']){
                $this->db->where("a.company", $advanced_search['company']);
                unset($advanced_search['company']);
            }

            if(isset($advanced_search["company"])){
                unset($advanced_search['company']);
            }

            $advanced_search['a.reference_no'] = $advanced_search['reference_no'];
            unset($advanced_search['reference_no']);

            $advanced_search['a.status'] = $advanced_search['status'];
            unset($advanced_search['status']);

            $advanced_search['b.description'] = $advanced_search['description'];
            unset($advanced_search['description']);

            $this->db->like($advanced_search, "both");
        }

        if($status){
            $this->db->where('a.status', $status);
        }
        if($company){
            $this->db->where('a.company', $company);
        }

        $i = $sortOrder[0]['column'];
        if ($sortBy[$i]['data'] == "firstname") {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $this->db->order_by("d.contractor", $sortOrder[0]['dir']);
        } else {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                if (is_numeric($rs->company)) {
                    $rs->company = $this->getCompany($rs->company);
                } else {
                    $rs->company = $rs->company;
                }

                if (is_numeric($rs->department)) {
                    $rs->department = $this->getDepartment($rs->department);

                } else {
                    $rs->department = $rs->department;
                }
                $rs->asset_name = $rs->asset_code . " | " . $rs->asset_name;
                $rs->vehicle_name = $rs->asset_code . " | " . $rs->vehicle_name;
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
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

    private function get_all_post_countv1($query_builder = null, $advanced_search, $status = null) {
        $date = date("Y-m-d", strtotime("-1 year"));
        $this->db->from('gcceforms.accountability a');
        $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
        $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
        $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');
        $this->db->where("a.status !=", "Released");
        $this->db->where("a.status !=", "Cancelled");
        $this->db->where("a.date_issued >=", $date);
        if ($query_builder) { $this->db->where($query_builder); }
        if (isset($advanced_search)) {
            if(isset($advanced_search['company']) && $advanced_search['company']){
                $this->db->where("a.company", $advanced_search['company']);
                unset($advanced_search['company']);
            }

            if(isset($advanced_search["company"])){
                unset($advanced_search['company']);
            }

            $advanced_search['a.reference_no'] = $advanced_search['reference_no'];
            unset($advanced_search['reference_no']);
            
            $advanced_search['a.status'] = $advanced_search['status'];
            unset($advanced_search['status']);

            $advanced_search['b.description'] = $advanced_search['description'];
            unset($advanced_search['description']);
            $this->db->like($advanced_search, "both");
        }

        if($status){
            $this->db->where('a.status', $status);
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_itemv1($query_builder = null, $advanced_search, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null) {
        $date = date("Y-m-d", strtotime("-1 year"));
        if ($search) {
            $sqlSelect = "a.id, UPPER(a.status), a.reference_no, a.company, c.firstname, UPPER(asset.name) as asset_name, a.date_issued, a.*, 
            b.asset_code, UPPER(b.description), b.type, b.amount,  UPPER(vehicle.name) as vehicle_name, c.company_id as temp_company, 
            c.department_id as temp_department, c.lastname, c.middlename, c.suffix, d.contractor,
            IF(a.is_contract = '1', 
                IFNULL(UPPER(d.company), IFNULL(UPPER(a.company), UPPER(e.description))), 
                IFNULL(UPPER(a.company), UPPER(e.description))) as company";

            $filterFields = array("a.status", "a.reference_no", "b.asset_code",
                "b.description", "b.amount", "b.type",
                "asset.name", "vehicle.name", "c.company_id",
                "c.department_id", "c.firstname", "c.lastname",
                "c.middlename", "c.suffix", "d.contractor", "d.company", "e.description");

            $this->db->select($sqlSelect);
            $this->db->from('gcceforms.accountability a');
            $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
            $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
            $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
            $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');
            $this->db->where("a.status !=", "Released");
            $this->db->where("a.status !=", "Cancelled");
            $this->db->where("a.date_issued >=", $date);
            if ($query_builder) { $this->db->where($query_builder); }
            if ($limit != -1) { $this->db->limit($limit, $offset); }

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }

            $this->db->group_end();
            if (isset($advanced_search)) {
                if(isset($advanced_search['company']) && $advanced_search['company']){
                    $this->db->where("a.company", $advanced_search['company']);
                }
                if(isset($advanced_search["company"])){
                    unset($advanced_search['company']);
                }

                $advanced_search['a.reference_no'] = $advanced_search['reference_no'];
                unset($advanced_search['reference_no']);

                $advanced_search['a.status'] = $advanced_search['status'];
                unset($advanced_search['status']);

                $advanced_search['b.description'] = $advanced_search['description'];
                unset($advanced_search['description']);

                $this->db->like($advanced_search, "both");
            }

            if($status){
                $this->db->where('a.status', $status);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if (is_numeric($rs->company)) {
                        $rs->company = strtoupper($this->getCompany($rs->company));

                    } else {
                        $rs->company = strtoupper($rs->company);
                    }

                    if (is_numeric($rs->department)) {
                        $rs->department = $this->getDepartment($rs->department);

                    } else {
                        $rs->department = $rs->department;
                    }
                    $rs->asset_name = $rs->asset_code . " | " . $rs->asset_name;
                    $rs->vehicle_name = $rs->asset_code . " | " . $rs->vehicle_name;
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $arrData[$key] = $rs;
                }
                $data = array();
                foreach ($arrData as $k => $v) { $data[] = $v; }
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    private function get_searched_item_countv1($query_builder = null, $advanced_search, $search = null, $status = null) {
        $date = date("Y-m-d", strtotime("-1 year"));
        $rowCount = 0;
        if ($search) {
            $sqlSelect = "a.id, a.status, a.reference_no, a.company, c.firstname, asset.name as asset_name, a.date_issued, a.*, 
            b.asset_code, b.description, b.type, b.amount,  vehicle.name as vehicle_name, c.company_id as temp_company, 
            c.department_id as temp_department, c.lastname, c.middlename, c.suffix, d.contractor,
            IF(a.is_contract = '1', 
                IFNULL(d.company, IFNULL(a.company, e.description)), 
                IFNULL(a.company, e.description)) as company";

            $filterFields = array("a.status", "a.reference_no", "b.asset_code", 
                "b.description", "b.amount", "b.type", "asset.name", 
                "vehicle.name", "c.company_id", "c.department_id", "c.firstname", 
                "c.lastname", "c.middlename", "c.suffix", "d.contractor", "d.company", "e.description");

            $this->db->select($sqlSelect);
            $this->db->from('gcceforms.accountability a');
            $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
            $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
            $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
            $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');
            $this->db->where("a.status !=", "Released");
            $this->db->where("a.status !=", "Cancelled");
            $this->db->where("a.date_issued >=", $date);
            if ($query_builder) {
                $this->db->where($query_builder);
            }
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            if (isset($advanced_search)) {
                if(isset($advanced_search['company']) && $advanced_search['company']){
                    $this->db->where("a.company", $advanced_search['company']);
                    unset($advanced_search['company']);
                }

                if(isset($advanced_search["company"])){
                    unset($advanced_search['company']);
                }

                $advanced_search['a.reference_no'] = $advanced_search['reference_no'];
                unset($advanced_search['reference_no']);

                $advanced_search['a.status'] = $advanced_search['status'];
                unset($advanced_search['status']);

                $advanced_search['b.description'] = $advanced_search['description'];
                unset($advanced_search['description']);

                $this->db->like($advanced_search, "both");
            }

            if($status){
                $this->db->where('a.status', $status);
            }

            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }
    /** v1 */

    /** function to display list of archived accountabilities */
    function archiveList() {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $advanced_search = (isset($post["advanced_search"]) && $post["advanced_search"]) ? $post["advanced_search"] : null;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql']) ? $post["query_builder"]['sql'] : array();

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        $rowData = $this->get_all_archived_items($advanced_search, $query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $view_by_company, $companyDescription);
        $rowCount = $this->get_all_archived_items_count($advanced_search, $query_builder, $search, $view_by_company, $companyDescription);
        // if (!$search) {  // if not been search
        //     $rowData = $this->get_archive_postv1($advanced_search, $query_builder, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_archive_post_countv1($advanced_search, $query_builder);
        // }
        
        // if ($search) {  // if been search 
        //     $rowData = $this->get_archive_itemv1($advanced_search, $query_builder, $search, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_archive_item_countv1($advanced_search, $query_builder, $search);
        //     $this->core_layout->setEventLog("Archive Accountability Masterfile - Search ".$search." in masterfile datatable.","search", "success", "gcceforms", "user");
        // }

        if ($search) {
            $this->core_layout->setEventLog("Archive Accountability Masterfile - Search ".$search." in masterfile datatable.","search", "success", "gcceforms", "user");
        }

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_all_archived_items($advanced_search, $query_builder = null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $view_by_company = false, $companyDescription = null) {
        $data = array();
        $date = date("Y-m-d", strtotime("-1 year"));

        $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "asset.name", "vehicle.name", "c.company_id", "c.department_id", "c.firstname", "c.lastname", "c.middlename", "c.suffix", "d.contractor");

        $sql = "a.id, a.status, a.reference_no, a.is_contract, a.company, a.date_issued, b.asset_id, b.type, c.firstname, c.lastname, c.middlename, c.suffix, d.contractor";
        $this->db->select($sql);
        $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
        $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');

        if (isset($search) && $search) {
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        }

        $this->db->from('gcceforms.accountability a');

        $this->db->group_start();
            $this->db->where('a.status', 'Cancelled');

            if ($search) {
                $this->db->or_where("DATE(a.date_issued) <= '$date'", NULL, FALSE);
            }
        $this->db->group_end();

        if ($view_by_company) {
            $this->db->where('a.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('a.company', $companyDescription);
            }
        }

        if ($query_builder) {
            $this->db->where($query_builder);
        }

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
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

        if (isset($advanced_search)) {
            // $advanced_search['a.status'] = $advanced_search['status'];
            // unset($advanced_search['status']);

            $advanced_search['a.company'] = $advanced_search['company'];
            unset($advanced_search['company']);

            $advanced_search['b.description'] = $advanced_search['description'];
            unset($advanced_search['description']);

            $this->db->like($advanced_search, "both");
        }

        $i = $sortOrder[0]['column'];

        if ($sortBy[$i]['data'] == "firstname") {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $this->db->order_by("d.contractor", $sortOrder[0]['dir']);
        } else {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();

            foreach ($query->result() as $key => $rs) {
                $rs->company = is_numeric($rs->company) ? $this->getCompany($rs->company) : $rs->company;
                $rs->asset_name = $this->getAssetVehiclename($rs->asset_id, $rs->type);
                
                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;

                $tempName = isset($tempFullname->display_name_1) && $tempFullname->display_name_1 ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->display_name = $rs->is_contract ? $rs->contractor : $tempName;
                // $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";

                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    private function get_all_archived_items_count($advanced_search, $query_builder = null, $search = null, $view_by_company = false, $companyDescription = null) {
        $date = date("Y-m-d", strtotime("-1 year"));
        $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "asset.name", "vehicle.name", "c.company_id", "c.department_id", "c.firstname", "c.lastname", "c.middlename", "c.suffix", "d.contractor");

        $sql = "a.id, a.status, a.reference_no, a.is_contract, a.company, a.date_issued, b.type, c.firstname, c.lastname, c.middlename, c.suffix, d.contractor";
        $this->db->select($sql);
        $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
        $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');

        if (isset($search) && $search) {
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        }

        $this->db->from('gcceforms.accountability a');

        $this->db->group_start();
            $this->db->where('a.status', 'Cancelled');

            if($search){
                $this->db->or_where("DATE(a.date_issued) <= '$date'", NULL, FALSE);
            }
        $this->db->group_end();

        if ($view_by_company) {
            $this->db->where('a.company', $view_by_company);

            if ($companyDescription) {
                $this->db->or_where('a.company', $companyDescription);
            }
        }

        if ($query_builder) {
            $this->db->where($query_builder);
        }

        if (isset($advanced_search)) {
            // $advanced_search['a.status'] = $advanced_search['status'];
            // unset($advanced_search['status']);

            $advanced_search['a.company'] = $advanced_search['company'];
            unset($advanced_search['company']);

            $advanced_search['b.description'] = $advanced_search['description'];
            unset($advanced_search['description']);

            $this->db->like($advanced_search, "both");
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    /** v1 */
    /** function to retrieve list of archived accountabilities */
    private function get_archive_postv1($advanced_search, $query_builder = null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
        // $date = date("Y-m-d", strtotime("-1 year"));
        $this->db->select("a.*, b.asset_code, b.description, b.type, b.amount, asset.name as asset_name, vehicle.name as vehicle_name, c.company_id as temp_company, c.department_id as temp_department, c.firstname, c.lastname, c.middlename, c.suffix, d.contractor");
        $this->db->from('gcceforms.accountability a');
        $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
        $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
        $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
        $this->db->group_start();
        // $this->db->where("a.date_issued <=", $date);
        $this->db->or_where_in("a.status", "Cancelled");
        $this->db->group_end();
        if ($query_builder) {
            $this->db->where($query_builder);
        }
        if (isset($advanced_search)) {
            $advanced_search['a.status'] = $advanced_search['status'];
            unset($advanced_search['status']);

            $advanced_search['a.company'] = $advanced_search['company'];
            unset($advanced_search['company']);

            $advanced_search['b.description'] = $advanced_search['description'];
            unset($advanced_search['description']);

            $this->db->like($advanced_search, "both");
        }

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        if ($sortBy[$i]['data'] == "firstname") {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $this->db->order_by("d.contractor", $sortOrder[0]['dir']);
        } else {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                if (is_numeric($rs->company)) {
                    $rs->company = $this->getCompany($rs->company);

                } else {
                    $rs->company = $rs->company;
                }

                if (is_numeric($rs->department)) {
                    $rs->department = $this->getDepartment($rs->department);
                } else {
                    $rs->department = $rs->department;
                }

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
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

    /** function to display list of archived accountabilities */
    private function get_archive_post_countv1($advanced_search, $query_builder = null) {
        // $date = date("Y-m-d", strtotime("-1 year"));
        $this->db->from('gcceforms.accountability a');
        $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
        $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
        $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
        $this->db->group_start();
        // $this->db->where("a.date_issued <=", $date);
        $this->db->or_where_in("a.status", "Cancelled");
        $this->db->group_end();
        if ($query_builder) {
            $this->db->where($query_builder);
        }
        if (isset($advanced_search)) {
            $advanced_search['a.status'] = $advanced_search['status'];
            unset($advanced_search['status']);

            $advanced_search['a.company'] = $advanced_search['company'];
            unset($advanced_search['company']);

            $advanced_search['b.description'] = $advanced_search['description'];
            unset($advanced_search['description']);

            $this->db->like($advanced_search, "both");
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_archive_itemv1($advanced_search, $query_builder = null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
        if ($search) {
            $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "asset.name", "vehicle.name", "c.company_id", "c.department_id", "c.firstname", "c.lastname", "c.middlename", "c.suffix", "d.contractor");
            $this->db->select("a.*, b.asset_code, b.description, b.type, b.amount, b.type, asset.name as asset_name, vehicle.name as vehicle_name, c.company_id as temp_company, c.department_id as temp_department, c.firstname, c.lastname, c.middlename, c.suffix, d.contractor");
            $this->db->from('gcceforms.accountability a');
            $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
            $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
            $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
            $this->db->group_start();
            $this->db->where("(a.status='Cancelled' OR a.date_issued<='$date')");
            $this->db->group_end();
            if ($query_builder) {
                $this->db->where($query_builder);
            }

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            if (isset($advanced_search)) {
                $advanced_search['a.status'] = $advanced_search['status'];
                unset($advanced_search['status']);

                $advanced_search['a.company'] = $advanced_search['company'];
                unset($advanced_search['company']);

                $advanced_search['b.description'] = $advanced_search['description'];
                unset($advanced_search['description']);

                $this->db->like($advanced_search, "both");
            }
            $i = $sortOrder[0]['column'];
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("d.contractor", $sortOrder[0]['dir']);
            } else {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if (is_numeric($rs->company)) {
                        $rs->company = $this->getCompany($rs->company);

                    } else {
                        $rs->company = $rs->company;
                    }

                    if (is_numeric($rs->department)) {
                        $rs->department = $this->getDepartment($rs->department);

                    } else {
                        $rs->department = $rs->department;
                    }

                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
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
        } else {
            return array();
        }
    }

    private function get_archive_item_countv1($advanced_search, $query_builder = null, $search = null) {
        $date = date("Y-m-d", strtotime("-1 year"));
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "b.amount", "b.type", "asset.name", "vehicle.name", "c.company_id", "c.department_id", "c.firstname", "c.lastname", "c.middlename", "c.suffix", "d.contractor");
            $this->db->select("a.*, b.asset_code, b.description, b.type, b.amount, b.type, asset.name as asset_name, vehicle.name as vehicle_name, c.company_id as temp_company, c.department_id as temp_department, c.firstname, c.lastname, c.middlename, c.suffix, d.contractor");
            $this->db->from('gcceforms.accountability a');
            $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
            $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
            $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
            $this->db->group_start();
            $this->db->where("(a.status='Cancelled' OR a.date_issued<='$date')");
            $this->db->group_end();
            if ($query_builder) {
                $this->db->where($query_builder);
            }
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            if (isset($advanced_search)) {
                $advanced_search['a.status'] = $advanced_search['status'];
                unset($advanced_search['status']);

                $advanced_search['a.company'] = $advanced_search['company'];
                unset($advanced_search['company']);

                $advanced_search['b.description'] = $advanced_search['description'];
                unset($advanced_search['description']);

                $this->db->like($advanced_search, "both");
            }
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }
    /** v1 */

    public function issuedToLookup() {
        $get = $this->input->get();
        $resultarray = array();
        // if (isset($get['q'])) {
        //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' AND (firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%') ORDER BY firstname ASC LIMIT 10");
        // } else {
        //     $query = $this->db->query("SELECT id, firstname, lastname, middlename, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC LIMIT 10");
        // }
        $sql = "id, firstname, lastname, middlename, suffix";
        $this->db->select($sql);
        $this->db->where('employee_status', 'Active');

        if (isset($get['q']) && $get['q']) {
            $this->db->group_start();
                $this->db->like('firstname', $get['q'], 'both');
                $this->db->or_like('lastname', $get['q'], 'both');
            $this->db->group_end();
        }

        $this->db->order_by('firstname', 'ASC');
        $this->db->limit(10);
        $this->db->from('gccmaster.tblemployees');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $tempRs = (array)$_query;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;

                $data["id"] = $_query["id"];
                $data["text"] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    public function getFileUnder() {
        // $datap = implode($this->input->get());
        $get = $this->input->get();
        $id = $get['data'];
        $resultarray = array();
        // $query = $this->db->query("SELECT id, company_id, department_id, position FROM gccmaster.tblemployees WHERE id=$id");

        $this->db->select('id, company_id, department_id, position');
        $this->db->where('id', $id);
        $this->db->from('gccmaster.tblemployees');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $resultarray["company"] = $row["company_id"];
            $resultarray["department"] = $row["department_id"];
            $resultarray["position"] = $row["position"];
        }
        return $resultarray;
    }

    public function contractorLookup() {
        $get = $this->input->get();
        $resultarray = array();
        // if (isset($get['q'])) {
        //     $query = $this->db->query("SELECT id, contractor FROM gcchris.tblcontractor WHERE contractor LIKE '%{$get['q']}%' ORDER BY contractor ASC LIMIT 10");
        // } else {
        //     $query = $this->db->query("SELECT id, contractor FROM gcchris.tblcontractor ORDER BY contractor ASC LIMIT 10");
        // }

        $this->db->select("id, contractor");

        if (isset($get['q']) && $get['q']) {
            $this->db->like('contractor', $get['q'], 'both');
        }

        $this->db->order_by('contractor', 'ASC');
        $this->db->limit(10);
        $this->db->from('gcchris.tblcontractor');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["id"] = $_query["id"];
                $data["text"] = $_query["contractor"];
                $resultarray[] = $data;
            }
        }
        return array("results" => $resultarray);
    }

    public function listTemp() {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

        $rowData = $this->get_temp_items($search, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_temp_items_count($search);
        // if (!$search) {
        //     $rowData = $this->get_temp_postv1($limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_temp_post_countv1();
        // }

        // if ($search) {
        //     $rowData = $this->get_temp_searched_itemv1($search, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_temp_searched_item_countv1($search);
        // }


        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_temp_items($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC"){
        $data = array();
        $ndata = array();
        $user_id = $this->user_data;

        $this->db->from('gcceforms.accountability_body_temp');
        $this->db->where('user_id', $user_id['emp_id']);

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        $this->db->reset_query();

        $arrData = array();
        if ($query->num_rows() > 0) {
            foreach($query->result() as $index => $rs){
                $ndata[$index] = (array) $rs;

                $type = (isset($rs->type) && $rs->type) ? $rs->type : "Consumable";
                $assetId = $rs->asset_id;
                if ($assetId) {
                    if (strtolower($type) == 'asset') {
                        $assetSql = "id, name, brand, modelno, serialno, isComponent";
                        $asset_query = $this->db->select($assetSql)->get_where('gccasset.assets', array('id' => $assetId));
                        if ($asset_query->num_rows() > 0) {
                            $asset_row = $asset_query->row();

                            $this->db->reset_query();

                            $rowAssetName = (isset($asset_row->name) && $asset_row->name) ? $asset_row->name : "No asset name";
                            $rowBrand = (isset($asset_row->brand) && $asset_row->brand) ? $asset_row->brand : "---";
                            $rowModel = (isset($asset_row->modelno) && $asset_row->modelno) ? $asset_row->modelno : "---";
                            $rowSerial = (isset($asset_row->serialno) && $asset_row->serialno) ? $asset_row->serialno : "---";

                            $ndata[$index]["name"] = $rowAssetName;
                            $ndata[$index]["brand"] = (isset($ndata[$index]["brand"]) && $ndata[$index]["brand"]) ? $ndata[$index]["brand"] : $rowBrand;
                            $ndata[$index]["modelno"] = (isset($ndata[$index]["modelno"]) && $ndata[$index]["modelno"]) ? $ndata[$index]["modelno"] : $rowModel;
                            $ndata[$index]["serialno"] = (isset($ndata[$index]["serialno"]) && $ndata[$index]["serialno"]) ? $ndata[$index]["serialno"] : $rowSerial;
                            $ndata[$index]["is_component"] = (isset($row->isComponent) && $row->isComponent) ? true : false;
                            $ndata[$index]['desc'] = $this->assetDesc($assetId, $rs->type);

                            $this->db->select("id, company_code, name, assetacode, assetname, brand, modelno, serialno, remarks, purchaseprice");
                            $this->db->from('gccasset.assets');
                            $this->db->where('mother_asset', $asset_row->id);
                            $this->db->where('isComponent', 1);
                            $this->db->where_not_in('status', array('junk', 'archived'));
                            
                            if ($sortOrder[0]['column'] != 0) {
                                $this->db->order_by($sortOrder[0]['column'], $sortOrder[0]['dir']);
                            } else {
                                $this->db->order_by("id", "DESC");
                            }
                            $query3 = $this->db->get();

                            $this->db->reset_query();

                            if ($query3->num_rows() > 0) {
                                $compArrData = array();

                                foreach ($query3->result_array() as $rr) {
                                    $rr["name"] = (isset($rr["name"]) && $rr["name"]) ? $rr["name"] : "No asset name";
                                    $rr["purchaseprice"] = number_format($rr["purchaseprice"], 2, ".", "");
                                    $rr["quantity"] = 1;
                                    $ndata[$index]['components'][] = $rr;
                                }
                            }

                            $rs->amount = number_format($rs->amount, 2);
                            $rs->desc = $this->assetDesc($assetId, $rs->type);
                            $arrData[$index] = $rs;
                        }
                    }

                    if (strtolower($type) == 'vehicle') {
                        $assetSql = "id, name, brand, gen_code, engineno, plateno, chasisno, isCompo";
                        $vehicle_query = $this->db->select($assetSql)->get_where('gccasset.vehicles', array('id' => $assetId));

                        if ($vehicle_query->num_rows() > 0) {
                            $row = $vehicle_query->row();

                            $this->db->reset_query();

                            $rowAssetName = (isset($row->name) && $row->name) ? $row->name : "No asset name";
                            $rowGenCode = (isset($row->gen_code) && $row->gen_code) ? $row->gen_code : "No Asset Code";
                            $rowBrand = (isset($row->brand) && $row->brand) ? $row->brand : "---";
                            $rowPlateNo = (isset($row->plateno) && $row->plateno) ? $row->plateno : "---";
                            $rowChasisNo = (isset($row->chasisno) && $row->chasisno) ? $row->chasisno : "---";
                            $rowEngineNo = (isset($row->engineno) && $row->engineno) ? $row->engineno : "---";

                            $ndata[$index]["name"] = $rowAssetName;
                            $ndata[$index]["asset_code"] = (isset($ndata[$index]["asset_code"]) && $ndata[$index]["asset_code"]) ? $ndata[$index]["asset_code"] : $rowGenCode;
                            $ndata[$index]["is_component"] = (isset($row->isCompo) && $row->isCompo) ? true : false;
                            $ndata[$index]['desc'] = $this->assetDesc($assetId, $rs->type);

                            $this->db->select('b.id as component_id, b.remarks, b.isExcluded as exclude, a.description, a.id, a.name, a.gen_code, a.description, a.purchaseprice');
                            $this->db->from('gccasset.vehicles a');
                            $this->db->join('gccasset.vehicles_components b', 'b.asset_id = a.id', 'left');
                            $this->db->where('b.parent_id', $row->id);

                            if ($sortOrder[0]['column'] != 0) {
                                $this->db->order_by($sortOrder[0]['column'], $sortOrder[0]['dir']);
                            } else {
                                $this->db->order_by("a.id", "DESC");
                            }

                            $query3 = $this->db->get();
                            if ($query3->num_rows() > 0) {
                                foreach ($query3->result() as $rr) {
                                    $rr->name = (isset($rr->name) && $rr->name) ? $rr->name : "No asset name";
                                    $rr->assetacode = (isset($rr->gen_code) && $rr->gen_code) ? $rr->gen_code : "No Asset Code";
                                    $rr->assetname = (isset($rr->description) && $rr->description) ? $rr->description : "No Asset Description";
                                    $rr->purchaseprice = number_format($rr->purchaseprice, 2, ".", "");
                                    $rr->quantity = 1;
                                    $ndata[$index]['components'][] = $rr;
                                }
                            }

                            $rs->amount = number_format($rs->amount, 2);
                            $rs->desc = $this->assetDesc($rs->asset_id, $rs->type);
                            $arrData[$index] = $rs;
                        }
                    }
                }
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }

            if($ndata){
                $data = $ndata;
            }
        }

        return $data;
    }
    function get_temp_items_count($search = null){
        $user_id = $this->user_data;
        $this->db->from('gcceforms.accountability_body_temp');
        $this->db->where('user_id', $user_id['emp_id']);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /** v1 */
    private function get_temp_postv1($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
        $user_id = $this->user_data;
        $this->db->select("a.asset_code, a.description, a.remarks, a.amount, a.*");
        $this->db->from('gcceforms.accountability_body_temp a');
        $this->db->where('user_id', $user_id['emp_id']);
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        $arrData = new stdClass();

        if ($query->num_rows() > 0) {
            $res = $query->result_array();
            $ndata = array();
            foreach ($res as $index => $rs) {
                $ndata[$index] = $rs;
                $type = (isset($rs["type"]) && $rs["type"]) ? $rs["type"] : "Consumable";
                $assetId = $rs['asset_id'];
                if ($assetId) {
                    if ($type == "Asset" || $type == "asset") {
                        $query2 = $this->db->get_where("gccasset.assets", array("id" => $assetId));
                        if ($query2->num_rows() > 0) {
                            $row = $query2->row();

                            $rowAssetName = (isset($row->name) && $row->name) ? $row->name : "No asset name";
                            $rowBrand = (isset($row->brand) && $row->brand) ? $row->brand : "---";
                            $rowModel = (isset($row->modelno) && $row->modelno) ? $row->modelno : "---";
                            $rowSerial = (isset($row->serialno) && $row->serialno) ? $row->serialno : "---";

                            $ndata[$index]["name"] = $rowAssetName;
                            $ndata[$index]["brand"] = (isset($ndata[$index]["brand"]) && $ndata[$index]["brand"]) ? $ndata[$index]["brand"] : $rowBrand;
                            $ndata[$index]["modelno"] = (isset($ndata[$index]["modelno"]) && $ndata[$index]["modelno"]) ? $ndata[$index]["modelno"] : $rowModel;
                            $ndata[$index]["serialno"] = (isset($ndata[$index]["serialno"]) && $ndata[$index]["serialno"]) ? $ndata[$index]["serialno"] : $rowSerial;

                            $ndata[$index]["is_component"] = (isset($row->isComponent) && $row->isComponent) ? true : false;

                            $this->db->select("id, company_code, name, assetacode, assetname, brand, modelno, serialno, remarks, purchaseprice");
                            $this->db->from('gccasset.assets');
                            $this->db->where('mother_asset', $row->id);
                            $this->db->where('isComponent', 1);
                            $this->db->group_start();
                            $this->db->where('status !=', 'junk');
                            $this->db->where('status !=', 'archived');
                            $this->db->group_end();
                            if ($sortOrder[0]['column'] != 0) {
                                $this->db->order_by($sortOrder[0]['column'], $sortOrder[0]['dir']);
                            } else {
                                $this->db->order_by("id", "DESC");
                            }
                            $query3 = $this->db->get();
                            if ($query->num_rows() > 0) {
                                $arrData = array();
                                foreach ($query->result() as $key => $rs) {
                                    $rs->amount = number_format($rs->amount, 2);
                                    $rs->desc = $this->assetDesc($rs->asset_id, $rs->type);
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
                    }

                    if ($type == "Vehicle" || $type == "vehicle") {
                        $query2 = $this->db->get_where("gccasset.vehicles", array("id" => $assetId));
                        if ($query2->num_rows() > 0) {
                            $row = $query2->row();
                            $rowAssetName = (isset($row->name) && $row->name) ? $row->name : "No asset name";
                            $rowGenCode = (isset($row->gen_code) && $row->gen_code) ? $row->gen_code : "No Asset Code";
                            $rowBrand = (isset($row->brand) && $row->brand) ? $row->brand : "---";
                            $rowPlateNo = (isset($row->plateno) && $row->plateno) ? $row->plateno : "---";
                            $rowChasisNo = (isset($row->chasisno) && $row->chasisno) ? $row->chasisno : "---";
                            $rowEngineNo = (isset($row->engineno) && $row->engineno) ? $row->engineno : "---";

                            $ndata[$index]["name"] = $rowAssetName;
                            $ndata[$index]["asset_code"] = (isset($ndata[$index]["asset_code"]) && $ndata[$index]["asset_code"]) ? $ndata[$index]["asset_code"] : $rowGenCode;
                            $ndata[$index]["is_component"] = (isset($row->isCompo) && $row->isCompo) ? true : false;

                            $this->db->select('a.*, b.id as component_id, b.remarks, b.isExcluded as exclude, a.description');
                            $this->db->from('gccasset.vehicles a');
                            $this->db->join('gccasset.vehicles_components b', 'b.asset_id = a.id', 'left');
                            $this->db->where('b.parent_id', $row->id);
                            if ($sortOrder[0]['column'] != 0) {
                                $this->db->order_by($sortOrder[0]['column'], $sortOrder[0]['dir']);
                            } else {
                                $this->db->order_by("a.id", "DESC");
                            }
                            $query3 = $this->db->get();
                            if ($query->num_rows() > 0) {
                                $arrData = array();
                                foreach ($query->result() as $key => $rs) {
                                    $rs->amount = number_format($rs->amount, 2);
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
                    }
                }
            }
            if ($ndata) {
                $arrData = (object)$ndata;
            }
        }
        return $arrData;
    }

    private function get_temp_post_countv1() {
        $user_id = $this->user_data;
        $this->db->from('gcceforms.accountability_body_temp');
        $this->db->where('user_id', $user_id['emp_id']);
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_temp_searched_itemv1($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
        if ($search) {
            $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "b.amount", "b.type", "asset.name", "vehicle.name", "c.company_id", "c.department_id");
            $user_id = $this->user_data;
            $this->db->from('gcceforms.accountability_body_temp');
            $this->db->where('user_id', $user_id['emp_id']);
            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            $arrData = new stdClass();

            if ($query->num_rows() > 0) {
                $res = $query->result_array();
                $ndata = array();
                foreach ($res as $index => $rs) {
                    $ndata[$index] = $rs;
                    $type = (isset($rs["type"]) && $rs["type"]) ? $rs["type"] : "Consumable";
                    $assetId = $rs['asset_id'];
                    if ($assetId) {
                        if ($type == "Asset" || $type == "asset") {
                            $query2 = $this->db->get_where("gccasset.assets", array("id" => $assetId));
                            if ($query2->num_rows() > 0) {
                                $row = $query2->row();

                                $rowAssetName = (isset($row->name) && $row->name) ? $row->name : "No asset name";
                                $rowBrand = (isset($row->brand) && $row->brand) ? $row->brand : "---";
                                $rowModel = (isset($row->modelno) && $row->modelno) ? $row->modelno : "---";
                                $rowSerial = (isset($row->serialno) && $row->serialno) ? $row->serialno : "---";

                                $ndata[$index]["name"] = $rowAssetName;
                                $ndata[$index]["brand"] = (isset($ndata[$index]["brand"]) && $ndata[$index]["brand"]) ? $ndata[$index]["brand"] : $rowBrand;
                                $ndata[$index]["modelno"] = (isset($ndata[$index]["modelno"]) && $ndata[$index]["modelno"]) ? $ndata[$index]["modelno"] : $rowModel;
                                $ndata[$index]["serialno"] = (isset($ndata[$index]["serialno"]) && $ndata[$index]["serialno"]) ? $ndata[$index]["serialno"] : $rowSerial;

                                $ndata[$index]["is_component"] = (isset($row->isComponent) && $row->isComponent) ? true : false;

                                $this->db->select("id, company_code, name, assetacode, assetname, brand, modelno, serialno, remarks, purchaseprice");
                                $this->db->from('gccasset.assets');
                                $this->db->where('mother_asset', $row->id);
                                $this->db->where('isComponent', 1);
                                $this->db->group_start();
                                $this->db->where('status !=', 'junk');
                                $this->db->where('status !=', 'archived');
                                $this->db->group_end();
                                $this->db->limit($limit, $offset);
                                $this->db->group_start();
                                foreach ($filterFields as $key => $field) {
                                    if ($key == 0) {
                                        $this->db->like($field, $search, "both");
                                    } else {
                                        $this->db->or_like($field, $search, "both");
                                    }
                                }
                                $this->db->group_end();
                                if ($sortOrder[0]['column'] != 0) {
                                    $this->db->order_by($sortOrder[0]['column'], $sortOrder[0]['dir']);
                                } else {
                                    $this->db->order_by("id", "DESC");
                                }
                                $query3 = $this->db->get();
                                if ($query3->num_rows() > 0) {
                                    $compArrData = array();
                                    foreach ($query3->result_array() as $rr) {
                                        $rr["name"] = (isset($rr["name"]) && $rr["name"]) ? $rr["name"] : "No asset name";
                                        $rr["purchaseprice"] = number_format($rr["purchaseprice"], 2, ".", "");
                                        $rr["quantity"] = 1;
                                        $ndata[$index]['components'][] = $rr;
                                    }
                                }
                            }
                        }

                        if ($type == "Vehicle" || $type == "vehicle") {
                            $query2 = $this->db->get_where("gccasset.vehicles", array("id" => $assetId));
                            if ($query2->num_rows() > 0) {
                                $row = $query2->row();
                                $rowAssetName = (isset($row->name) && $row->name) ? $row->name : "No asset name";
                                $rowGenCode = (isset($row->gen_code) && $row->gen_code) ? $row->gen_code : "No Asset Code";
                                $rowBrand = (isset($row->brand) && $row->brand) ? $row->brand : "---";
                                $rowPlateNo = (isset($row->plateno) && $row->plateno) ? $row->plateno : "---";
                                $rowChasisNo = (isset($row->chasisno) && $row->chasisno) ? $row->chasisno : "---";
                                $rowEngineNo = (isset($row->engineno) && $row->engineno) ? $row->engineno : "---";

                                $ndata[$index]["name"] = $rowAssetName;
                                $ndata[$index]["asset_code"] = (isset($ndata[$index]["asset_code"]) && $ndata[$index]["asset_code"]) ? $ndata[$index]["asset_code"] : $rowGenCode;
                                $ndata[$index]["is_component"] = (isset($row->isCompo) && $row->isCompo) ? true : false;

                                $this->db->select('a.*, b.id as component_id, b.remarks, b.isExcluded as exclude, a.description');
                                $this->db->from('gccasset.vehicles a');
                                $this->db->join('gccasset.vehicles_components b', 'b.asset_id = a.id', 'left');
                                $this->db->where('b.parent_id', $row->id);
                                $this->db->limit($limit, $offset);
                                $this->db->group_start();
                                foreach ($filterFields as $key => $field) {
                                    if ($key == 0) {
                                        $this->db->like($field, $search, "both");
                                    } else {
                                        $this->db->or_like($field, $search, "both");
                                    }
                                }
                                $this->db->group_end();
                                if ($sortOrder[0]['column'] != 0) {
                                    $this->db->order_by($sortOrder[0]['column'], $sortOrder[0]['dir']);
                                } else {
                                    $this->db->order_by("a.id", "DESC");
                                }
                                $query3 = $this->db->get();
                                if ($query3->num_rows() > 0) {
                                    foreach ($query3->result_array() as $rr) {
                                        $rr["name"] = (isset($rr["name"]) && $rr["name"]) ? $rr["name"] : "No asset name";
                                        $rr["assetacode"] = (isset($rr["gen_code"]) && $rr["gen_code"]) ? $rr["gen_code"] : "No Asset Code";
                                        $rr["assetname"] = (isset($rr["description"]) && $rr["description"]) ? $rr["description"] : "No Asset Description";
                                        $rr["purchaseprice"] = number_format($rr["purchaseprice"], 2, ".", "");
                                        $rr["quantity"] = 1;
                                        $ndata[$index]['components'][] = $rr;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($ndata) {
                    $arrData = (object)$ndata;
                }
            }
            return $arrData;
        }
    }

    private function get_temp_searched_item_countv1($search = null) {
        $rowCount = 0;
        if ($search) {
            $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "b.amount", "b.type", "asset.name", "vehicle.name", "c.company_id", "c.department_id");
            $this->db->select("a.*, b.asset_code, b.description, b.type, b.amount, b.type, asset.name as asset_name, vehicle.name as vehicle_name, c.company_id as temp_company, c.department_id as temp_department, c.firstname, c.lastname, c.middlename, c.suffix, d.contractor");
            $this->db->from('gcceforms.accountability a');
            $this->db->join('gcceforms.accountability_body b', 'b.accountability_id = a.id', 'left');
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
            $this->db->join('gccmaster.tblemployees c', 'c.id = a.issued_to', 'left');
            $this->db->join('gcchris.tblcontractor d', 'd.id = a.issued_to', 'left');
            $this->db->where("a.status !=", "Released");
            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }
    /** v1 */

    function temp_details($id) {
        $this->db->from('gcceforms.accountability_body_temp');
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        return $query->result();
    }

    function newAssetTemp() {
        $resultset = array();
        $post = $this->input->post();
        $code = $post['code'];
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";
        $accountability_id = (isset($post["accountability_id"]) && $post["accountability_id"]) ? $post["accountability_id"] : null;

        // $rowCount = 0;
        // $rowData = array();
        $rowData = $this->get_new_asset_temp_post($code, $limit, $offset, $sortBy, $sortOrder, $accountability_id);
        $rowCount = $this->get_new_asset_temp_post_count($code, $accountability_id);
        // if (!$search) {
        // }

        // if ($search) {
        //     $rowData = $this->get_searched_new_asset_temp($search, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_searched_new_asset_temp_count($search);
        // }

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_new_asset_temp_post($code, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $accountability_id = null) {
        $data = array();

        if ($accountability_id) {
            $this->db->select("GROUP_CONCAT(a.asset_id) as id");
            $this->db->where('a.accountability_id', $accountability_id);
            $this->db->where("a.type", "Asset");
            $asset_ids = $this->db->get('gcceforms.accountability_body a')->row("id");
    
            $asset_ids_array = explode(",", $asset_ids);
        }

        $this->db->reset_query();

        $this->db->select("a.*, TRIM(a.assetacode) as assetacode, b.id as temp_id");
        $this->db->from("gccasset.assets a");
        $this->db->join("gcceforms.accountability_body_temp b", "b.asset_id = a.id AND b.type='Asset'", "left");
        $this->db->where('a.status !=', 'archived');
        $this->db->where('a.status !=', 'junk');
        $this->db->where('a.status !=', 'repair');
        $this->db->where('a.status !=', '');
        $this->db->where("a.is_borrowed", "0");

        if ($accountability_id) {
            $this->db->where_not_in("a.id", $asset_ids_array);
        }

        if ($code) {
            $this->db->group_start();
            $this->db->like('a.assetacode', $code);
            $this->db->or_like('a.assetname', $code);
            $this->db->group_end();
        }

        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    private function get_new_asset_temp_post_count($code, $accountability_id = null) {

        if ($accountability_id) {
            $this->db->select("GROUP_CONCAT(a.asset_id) as id");
            $this->db->where('a.accountability_id', $accountability_id);
            $this->db->where("a.type", "Asset");
            $asset_ids = $this->db->get('gcceforms.accountability_body a')->row("id");
            $asset_ids_array = explode(",", $asset_ids);
        }

        $this->db->reset_query();

        $this->db->from("gccasset.assets a");
        $this->db->join("gcceforms.accountability_body_temp b", "b.asset_id = a.id AND b.type='Asset'", "left");
        $this->db->where('a.status !=', 'archived');
        $this->db->where('a.status !=', 'junk');
        $this->db->where('a.status !=', 'repair');
        $this->db->where('a.status !=', '');
        $this->db->where("a.is_borrowed", "0");

        if ($accountability_id) {
            $this->db->where_not_in("a.id", $asset_ids_array);
        }

        if ($code) {
            $this->db->group_start();
            $this->db->like('a.assetacode', $code);
            $this->db->or_like('a.assetname', $code);
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    function assetCompTemp($code) {
        $post = $this->input->post();
        $resultset = array();
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_asset_comp_temp_post($code, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_asset_comp_temp_post_count($code);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_asset_comp_temp_post($code, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
        $data = array();
        
        $this->db->select("a.*, b.id as temp_id");
        $this->db->from("gccasset.assets a");
        $this->db->join("gcceforms.accountability_body_temp b", "b.asset_id = a.id AND b.type='Asset'", "left");
        $this->db->where('status !=', 'archived');
        $this->db->where('status !=', 'junk');
        $this->db->where("a.is_borrowed", "0");
        $this->db->where("a.status !=", "archived");
        $this->db->where("a.mother_asset", $code);
        $this->db->limit($limit, $offset);

        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    private function get_asset_comp_temp_post_count($code) {
        $this->db->from("gccasset.assets a");
        $this->db->join("gcceforms.accountability_body_temp b", "b.asset_id = a.id AND b.type='Asset'", "left");
        $this->db->where('status !=', 'archived');
        $this->db->where('status !=', 'junk');
        $this->db->where("a.is_borrowed", "0");
        $this->db->where("a.status !=", "archived");
        $this->db->where("a.mother_asset", $code);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function newVehicleTemp() {
        $resultset = array();
        $post = $this->input->post();
        $code = $post['code'];
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";
        $accountability_id = (isset($post["accountability_id"]) && $post["accountability_id"]) ? $post["accountability_id"] : null;

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_new_vehicle_temp_post($code, $limit, $offset, $sortBy, $sortOrder, $accountability_id);
        $rowCount = $this->get_new_vehicle_temp_post_count($code, $accountability_id);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_new_vehicle_temp_post($code, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $accountability_id = null) {
        $data = array();
        $asset_ids = array();
        
        if ($accountability_id) {
            $this->db->select("GROUP_CONCAT(a.asset_id) id");
            $this->db->where('a.accountability_id', $accountability_id);
            $this->db->where("a.type", "Vehicle");
            $asset_ids = $this->db->get('gcceforms.accountability_body a')->row("id");
            $asset_ids_array = explode(",", $asset_ids);
        }

        $this->db->select('a.*, TRIM(gen_code) as gen');
        $this->db->from('gccasset.vehicles a');
        $array = "status2='operational' OR status2='surplus' OR status2='brandnew' AND is_borrowed=0";

        $this->db->where('status2 !=', 'archived');
        $this->db->where('status2 !=', 'repair');
        $this->db->where('status2 !=', 'under repair');
        $this->db->where('status2 !=', '');
        $this->db->where('status2 !=', 'junk');
        $this->db->where('status2 !=', 'sold');
        // $this->db->group_end();
        // $this->db->or_where('status2 IS NULL', NULL, FALSE);
        // $this->db->group_end();

        /* condition that does not allow showing of vehicle components,
            * reason why if you search a component nothing will show */
        // $this->db->where('isCompo', 0);
        /* end condition */
        $this->db->where('is_borrowed', 0);

        if (!empty($asset_ids)) {
            $this->db->where_not_in("a.id", $asset_ids_array);
        }

        $this->db->group_start();
        $this->db->like('assetcode', $code);
        $this->db->or_like('description', $code);
        $this->db->or_like('plateno', $code);
        $this->db->or_like('gen_code', $code);
        $this->db->group_end();

        $this->db->limit($limit, $offset);

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    private function get_new_vehicle_temp_post_count($code, $accountability_id = null) {
        $asset_ids = array();

        if ($accountability_id) {
            $this->db->select("GROUP_CONCAT(a.asset_id) id");
            $this->db->where('a.accountability_id', $accountability_id);
            $this->db->where("a.type", "Vehicle");
            $asset_ids = $this->db->get('gcceforms.accountability_body a')->row("id");
            $asset_ids_array = explode(",", $asset_ids);
        }

        $this->db->select('a.*');
        $this->db->from('gccasset.vehicles a');

        $this->db->group_start();
        $this->db->group_start();
        $this->db->where('status2 !=', 'archived');
        $this->db->where('status2 !=', 'repair');
        $this->db->where('status2 !=', 'under repair');
        $this->db->where('status2 !=', '');
        $this->db->where('status2 !=', 'junk');
        $this->db->where('status2 !=', 'sold');
        $this->db->group_end();
        $this->db->or_where('status2 IS NULL', NULL, FALSE);
        $this->db->group_end();

        /* condition that does not allow showing of vehicle components,
            * reason why if you search a component nothing will show */
        // $this->db->where('isCompo', 0);
        /* end condition */

        $this->db->where('is_borrowed', 0);

        if (!empty($asset_ids)) {
            $this->db->where_not_in("a.id", $asset_ids_array);
        }

        $this->db->group_start();
        $this->db->like('assetcode', $code);
        $this->db->or_like('description', $code);
        $this->db->or_like('plateno', $code);
        $this->db->or_like('gen_code', $code);
        $this->db->group_end();

        $query = $this->db->get();
        return $query->num_rows();
    }

    function getAssetDetail($code) {
        $this->db->from('gccasset.assets');
        $this->db->where('id', $code);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $row = $query->row();
            $this->db->from('gccasset.assets');
            $this->db->where('mother_asset', $code);
            $this->db->where('isComponent', 1);
            $this->db->group_start();
            $this->db->where('status !=', 'junk');
            $this->db->where('status !=', 'archived');
            $this->db->group_end();
            $component = $this->db->get();
            if ($component->num_rows() > 0) {
                $arrComponents = array();
                foreach ($component->result() as $rs) {
                    $rs->assetacode = ($rs->assetacode) ? $rs->assetacode : "<span class='text-red'>No Asset Code</span>";
                    $rs->name = ($rs->name) ? $rs->name : "<span class='text-red'>No Asset Name</span>";
                    if ($rs->is_borrowed == 0) {
                        $arrComponents[] = $rs;
                    }
                }
                $row->components = $arrComponents;
            } else {
                $row->components = array();
            }

            return $row;
        } else {
            return false;
        }
    }

    function getVehicleDetail($code) {
        $this->db->from('gccasset.vehicles');
        $this->db->where('id', $code);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $row = $query->row();
            $check = $this->db->get_where("gccasset.vehicles_components", array("parent_id" => $code));
            if ($check->num_rows() > 0) {
                $arrComponents = array();
                foreach ($check->result() as $rs2) {
                    $component = $this->db->get_where("gccasset.vehicles", array("id" => $rs2->asset_id, "isCompo" => 1));
                    foreach ($component->result() as $rs) {
                        $rs->name = ($rs->name) ? $rs->name : "<span class='text-red'>No Asset Name</span>";
                        $rs->gen_code = ($rs->gen_code) ? $rs->gen_code : "<span class='text-red'>No Asset Code</span>";
                        if ($rs->is_borrowed == 0) {
                            $arrComponents[] = $rs;
                        }
                    }
                }
                $row->components = $arrComponents;
            } else {
                $row->components = array();
            }
            return $row;
        } else {
            return false;
        }
    }

    function vehicleCompTemp($code) {
        $post = $this->input->post();
        $resultset = array();
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["sort"]) && $post["sort"]) ? $post["sort"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_vehicle_comp_temp_post($code, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_vehicle_comp_temp_post_count($code);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_vehicle_comp_temp_post($code, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC") {
        $data = array();
        
        $this->db->select('a.*');
        $this->db->from('gccasset.vehicles a');
        $this->db->join("gcceforms.accountability_body_temp b", "b.asset_id = a.id AND b.type='Vehicle'", "left");
        $this->db->where("a.status !=", "archived");
        $this->db->group_start();
        $this->db->where('a.status2 !=', 'archived');
        $this->db->where('a.status2 !=', 'junk');
        $this->db->or_where('a.status2 IS NULL', NULL, FALSE);
        $this->db->group_end();
        $this->db->where('a.is_borrowed', 0);
        $this->db->where("a.motherID", $code);
        $this->db->limit($limit, $offset);
        
        if ($sortBy) {
            $this->db->order_by($sortBy, $sortOrder);
        } else {
            $this->db->order_by("a.id", "DESC");
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    private function get_vehicle_comp_temp_post_count($code) {
        $this->db->from('gccasset.vehicles a');
        $this->db->join("gcceforms.accountability_body_temp b", "b.asset_id = a.id AND b.type='Vehicle'", "left");
        $this->db->where("a.status !=", "archived");
        $this->db->where('a.status2 !=', 'archived');
        $this->db->where('a.status2 !=', 'junk');
        $this->db->where('a.is_borrowed', 0);
        $this->db->where("a.motherID", $code);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function multipleTemp() {
        $resultset = array();
        $post = $this->input->post();
        $code = $post['code'];
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : null;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : "desc";
        $accountability_id = (isset($post['accountability_id']) && $post['accountability_id']) ? $post['accountability_id'] : null;

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_multiple_temp_post($code, $limit, $offset, $sortBy, $sortOrder, $accountability_id);
        $rowCount = $this->get_multiple_temp_post_count($code, $accountability_id);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function get_multiple_temp_post($code, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $accountability_id = null) {
        $data = array();
        $asset_ids = array();
        $asset_ids_array = "";

        if ($accountability_id){
            $this->db->select("GROUP_CONCAT(a.asset_id) id");
            $this->db->where('a.accountability_id', $accountability_id);
            $this->db->where("a.type", "Asset");
            $asset_ids = $this->db->get('gcceforms.accountability_body a')->row("id");
            $asset_ids_array = explode(",", $asset_ids);
        }

        $this->db->reset_query();

        $this->db->select("a.*, b.id as temp_id");
        $this->db->from("gccasset.assets a");
        $this->db->join("gcceforms.accountability_body_temp b", "b.asset_id = a.id AND b.type='Asset'", "left");
        $this->db->where('status !=', 'archived');
        $this->db->where('status !=', 'junk');
        $this->db->where("a.is_borrowed", "0");
        $this->db->where("a.status !=", "archived");

        if (!empty($asset_ids)){
            $this->db->where_not_in("a.id", $asset_ids_array);
        }

        if ($code) {
            $this->db->group_start();
            $this->db->like('a.assetacode', $code);
            $this->db->or_like('a.assetname', $code);
            $this->db->group_end();
        }

        if ($limit !== -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();

            foreach ($query->result() as $key => $rs) {
                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    private function get_multiple_temp_post_count($code, $accountability_id = null) {
        $asset_ids = array();
        $asset_ids_array = "";

        if ($accountability_id){
            $this->db->select("GROUP_CONCAT(a.asset_id) id");
            $this->db->where('a.accountability_id', $accountability_id);
            $this->db->where("a.type", "Asset");
            $asset_ids = $this->db->get('gcceforms.accountability_body a')->row("id");
            $asset_ids_array = explode(",", $asset_ids);
        }

        $this->db->reset_query();

        $this->db->from("gccasset.assets a");
        $this->db->join("gcceforms.accountability_body_temp b", "b.asset_id = a.id AND b.type='Asset'", "left");
        $this->db->where('status !=', 'archived');
        $this->db->where('status !=', 'junk');
        $this->db->where("a.is_borrowed", "0");
        $this->db->where("a.status !=", "archived");

        if (!empty($asset_ids)){
            $this->db->where_not_in("a.id", $asset_ids_array);
        }

        if ($code) {
            $this->db->group_start();
            $this->db->like('a.assetacode', $code);
            $this->db->or_like('a.assetname', $code);
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    function saveAssetTemp() {
        $resultset = array();
        $post = $this->input->post();
        $resultset["post_data"] = $post;

        $assetId = $this->input->post('asset_id');
        if ($assetId) {
            $hasAssetName = true;
            $component = $this->db->query("SELECT * FROM gccasset.assets where mother_asset='$assetId' && isComponent=1 && is_borrowed=0");
            $tempAssetData = array();

            if ($component->num_rows() > 0) {
                foreach ($component->result() as $rs) {
                    if ($rs->name == null || $rs->assetacode == null || $rs->name == "" || $rs->assetacode == "") {
                        $hasAssetName = false;
                    } else {
                        $session_data = $this->user_data['emp_id'];
                        $det = $this->getAssetDetail($this->input->post('asset_id'));
                        $data = array(
                            'user_id' => $session_data,
                            'asset_id' => $rs->id,
                            'asset_code' => $rs->assetacode,
                            'quantity' => 1,
                            'cost' => $rs->purchaseprice,
                            'description' => $rs->name,
                            'brand' => $rs->brand,
                            'modelno' => $rs->modelno,
                            'serialno' => $rs->serialno,
                            'remarks' => $this->input->post('asset_remarks'),
                            'type' => 'Asset',
                            'amount' => 1 * $rs->purchaseprice,
                        );

                        $tempAssetData[] = $data;
                    }
                }
            }

            if ($hasAssetName) {
                $session_data = $this->user_data['emp_id'];
                $det = $this->getAssetDetail($this->input->post('asset_id'));
                $data = array(
                    'user_id' => $session_data,
                    'asset_id' => $det->id,
                    'asset_code' => $this->input->post('asset_code'),
                    'quantity' => $this->input->post('asset_qty'),
                    'cost' => $this->input->post('asset_cost'),
                    'description' => $det->name,
                    'brand' => $det->brand,
                    'modelno' => $det->modelno,
                    'serialno' => $det->serialno,
                    'remarks' => $this->input->post('asset_remarks'),
                    'type' => 'Asset',
                    'amount' => $this->input->post('asset_qty') * $this->input->post('asset_cost'),
                );

                $noDuplicate = $this->checkDuplicateAsset($session_data, $det->id, "Asset");
                if ($noDuplicate) {
                    $insert = $this->save_content_temp($data);
                    if ($insert) {
                        $resultset["response"] = true;
                        $resultset["data"] = $det;
                        $resultset["id"] = $this->input->post('asset_id');
                        $resultset["message"] = "Item added successfully";
                        $resultset["state"] = "Success!";
                        $this->core_layout->setEventLog("Add Asset for Accountability - Add asset ".$data['asset_code'].".", "add", "success", "gcceforms", "user");
                    } else {
                        $resultset["response"] = false;
                        $resultset["message"] = "Adding of asset/item to accountability failed!";
                        $this->core_layout->setEventLog("Add Asset for Accountability - Failed add asset ".$data['asset_code'].".", "add", "error", "gcceforms", "system");
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["state"] = "Warning!";
                    $resultset["message"] = "Asset/Item `{$det->assetacode}` is already on the list or already accounted!";
                }

                if(is_array($tempAssetData) && count($tempAssetData) > 0){
                    foreach ($tempAssetData as $kk => $vv) {
                        $vv = (object) $vv;
                        $noDuplicate = $this->checkDuplicateAsset($session_data, $vv->asset_id, "Asset");
                        if ($noDuplicate) {
                            $insertedAsset = $this->save_content_temp($vv);
                        }
                    }
                }
            } else {
                $resultset["response"] = false;
                $resultset["state"] = "Warning!";
                $resultset["message"] = "Asset component(s) has no `asset code/asset name` please update!";
            }
        } else {
            $resultset["response"] = false;
            $resultset["message"] = "No data found for accountability!";
            $resultset["state"] = "Error!";
        }

        return $resultset;
    }

    function saveMultipleTemp() {
        $resultset = array();
        $post = $this->input->get();
        $resultset["post_data"] = $post;

        $assetId = $post['id'];
        if ($assetId) {
            $hasAssetName = true;
            $component = $this->db->query("SELECT * FROM gccasset.assets where mother_asset='$assetId' && isComponent=1 && is_borrowed=0");

            if ($component->num_rows() > 0) {
                foreach ($component->result() as $rs) {
                    if ($rs->name == null || $rs->assetacode == null || $rs->name == "" || $rs->assetacode == "") {
                        $hasAssetName = false;
                    } else {
                        $session_data = $this->user_data['emp_id'];
                        $det = $this->getAssetDetail($post['id']);
                        $data = array(
                            'user_id' => $session_data,
                            'asset_id' => $rs->id,
                            'asset_code' => $rs->assetacode,
                            'quantity' => 1,
                            'cost' => $rs->purchaseprice,
                            'description' => $rs->name,
                            'brand' => $rs->brand,
                            'modelno' => $rs->modelno,
                            'serialno' => $rs->serialno,
                            'remarks' => "",
                            'type' => 'Asset',
                            'amount' => 1 * $rs->purchaseprice,
                        );

                        $noDuplicate = $this->checkDuplicateAsset($session_data, $det->id, "Asset");
                        if ($noDuplicate) {
                            $insert = $this->save_content_temp($data);
                            if ($insert) {
                                $resultset["response"] = true;
                                $resultset["data"] = $det;
                                $resultset["id"] = $post['id'];
                                $resultset["message"] = "Item added successfully";
                                $resultset["state"] = "Success!";
                                $this->core_layout->setEventLog("Add Asset for Accountability - Add asset ".$rs->assetacode.".", "add", "success", "gcceforms", "user");
                            } else {
                                $resultset["response"] = false;
                                $resultset["message"] = "Adding of asset/item to accountability failed!";
                                $resultset["state"] = "Warning!";
                                $this->core_layout->setEventLog("Add Asset for Accountability - Failed add asset ".$rs->assetacode.".", "add", "error", "gcceforms", "system");
                            }
                        } else {
                            $resultset["response"] = false;
                            $resultset["message"] = "Asset/Item `{$det->assetacode}` is already on the list or already accounted!";
                            $resultset["state"] = "Warning!";
                        }
                    }
                }
            }

            if ($hasAssetName) {
                $session_data = $this->user_data['emp_id'];
                $det = $this->getAssetDetail($post['id']);
                $data = array(
                    'user_id' => $session_data,
                    'asset_id' => $det->id,
                    'asset_code' => $post['asset_code'],
                    'quantity' => $post['qty'],
                    'cost' => $post['price'],
                    'description' => $det->name,
                    'brand' => $det->brand,
                    'modelno' => $det->modelno,
                    'serialno' => $det->serialno,
                    'remarks' => "",
                    'type' => 'Asset',
                    'amount' => $post['qty'] * $post['price'],
                );

                $noDuplicate = $this->checkDuplicateAsset($session_data, $det->id, "Asset");
                if ($noDuplicate) {
                    $insert = $this->save_content_temp($data);
                    if ($insert) {
                        $resultset["response"] = true;
                        $resultset["data"] = $det;
                        $resultset["id"] = $post['id'];
                        $resultset["message"] = "Item added successfully";
                        $resultset["state"] = "Success!";
                        $this->core_layout->setEventLog("Add Asset for Accountability - Add asset ".$post['asset_code'].".", "add", "success", "gcceforms", "user");
                    } else {
                        $resultset["response"] = false;
                        $resultset["message"] = "Adding of asset/item to accountability failed!";
                        $resultset["state"] = "Warning!";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["message"] = "Asset/Item `{$det->assetacode}` is already on the list or already accounted!";
                    $resultset["state"] = "Warning!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["state"] = "Warning!";
                $resultset["message"] = "Asset component(s) has no `asset code/asset name` please update!";
            }
        } else {
            $resultset["response"] = false;
            $resultset["message"] = "No data found for accountability!";
            $resultset["state"] = "Error!";
        }

        return $resultset;
    }

    function saveMultipleEdit($id) {
        $date = date('Y-m-d H:i:s');
        $resultset = array();
        $post = $this->input->get();
        $resultset["post_data"] = $post;
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $session_data2 = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        $assetId = $post['id'];
        if ($assetId) {
            $hasAssetName = true;
            $component = $this->db->query("SELECT * FROM gccasset.assets where mother_asset='$assetId' && isComponent=1 && is_borrowed=0");

            if ($component->num_rows() > 0) {
                foreach ($component->result() as $rs) {
                    if ($rs->name == null || $rs->assetacode == null || $rs->name == "" || $rs->assetacode == "") {
                        $hasAssetName = false;
                    } else {
                        $session_data = $this->user_data['emp_id'];
                        $det = $this->getAssetDetail($post['id']);
                        $data = array(
                            'accountability_id' => $id,
                            'asset_id' => $rs->id,
                            'asset_code' => $rs->assetacode,
                            'quantity' => 1,
                            'cost' => $rs->purchaseprice,
                            'description' => $rs->name,
                            'brand' => $rs->brand,
                            'modelno' => $rs->modelno,
                            'serialno' => $rs->serialno,
                            'remarks' => "",
                            'type' => 'Asset',
                            'amount' => 1 * $rs->purchaseprice,
                        );
                        $data2 = array(
                            'last_edited_by' => $session_data2,
                            'last_edited_dt' => $date,
                            'last_edited_id' => $this->user_data['emp_id'],
                        );

                        $insert_id = $this->save_accountability_edit($id, $data2);

                        $noDuplicate = $this->checkDuplicateAssetEdit($det->id, "Asset");
                        if ($noDuplicate) {
                            $insert = $this->save_content_edit($data);
                            if ($insert) {
                                $resultset["response"] = true;
                                $resultset["data"] = $det;
                                $resultset["id"] = $post['id'];
                                $resultset["message"] = "Item added successfully!";
                                $resultset["state"] = "Success!";
                            } else {
                                $resultset["response"] = false;
                                $resultset["message"] = "Adding of asset/item to accountability failed!";
                                $resultset["state"] = "Warning!";
                            }
                        } else {
                            $resultset["response"] = false;
                            $resultset["message"] = "Asset/Item `{$det->assetacode}` is already on the list!";
                            $resultset["state"] = "Warning!";
                        }
                    }
                }
            }

            if ($hasAssetName) {
                $session_data = $this->user_data['emp_id'];
                $det = $this->getAssetDetail($post['id']);
                $data = array(
                    'accountability_id' => $id,
                    'asset_id' => $det->id,
                    'asset_code' => $post['asset_code'],
                    'quantity' => $post['qty'],
                    'cost' => $post['price'],
                    'description' => $det->name,
                    'brand' => $det->brand,
                    'modelno' => $det->modelno,
                    'serialno' => $det->serialno,
                    'remarks' => "",
                    'type' => 'Asset',
                    'amount' => $post['qty'] * $post['price'],
                );

                $data2 = array(
                    'last_edited_by' => $session_data2,
                    'last_edited_dt' => $date,
                    'last_edited_id' => $this->user_data['emp_id'],
                );

                $insert_id = $this->save_accountability_edit($id, $data2);

                $noDuplicate = $this->checkDuplicateAssetEdit($det->id, "Asset");
                if ($noDuplicate) {
                    $insert = $this->save_content_edit($data);
                    if ($insert) {
                        $resultset["response"] = true;
                        $resultset["data"] = $det;
                        $resultset["id"] = $post['id'];
                        $resultset["message"] = "Item added successfully";
                        $resultset["state"] = "Success!";
                    } else {
                        $resultset["response"] = false;
                        $resultset["message"] = "Adding of asset/item to accountability failed!";
                        $resultset["state"] = "Warning!";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["message"] = "Asset/Item `{$det->assetacode}` is already on the list!";
                    $resultset["state"] = "Warning!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["message"] = "Asset component(s) has no `asset code/asset name` please update!";
                $resultset["state"] = "Warning!";
            }
        } else {
            $resultset["response"] = false;
            $resultset["message"] = "No data found for accountability!";
            $resultset["state"] = "Error!";
        }

        return $resultset;
    }

    function getAssetCode($id, $type){
        if($type == "asset"){
            $data = "assetacode";
            $table = "gccasset.assets";
        }else{
            $data = "gen_code";
            $table = "gccasset.vehicles";
        }

        $this->db->select($data);
        return $this->db->get_where($table, array("id"=>$id))->row($data);
    }

    function saveAssetEdit($id) {
        $date = date('Y-m-d H:i:s');
        $resultset = array();
        $post = $this->input->post();
        $resultset["post_data"] = $post;
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $session_data2 = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        $assetId = $this->input->post('asset_id');
        if ($assetId) {
            $hasAssetName = true;
            $component = $this->db->query("SELECT * FROM gccasset.assets where mother_asset='$assetId' && isComponent=1 && is_borrowed=0");

            if ($component->num_rows() > 0) {
                foreach ($component->result() as $rs) {
                    if ($rs->name == null || $rs->assetacode == null || $rs->name == "" || $rs->assetacode == "") {
                        $hasAssetName = false;
                    } else {
                        $session_data = $this->user_data['emp_id'];
                        $det = $this->getAssetDetail($this->input->post('asset_id'));
                        $data = array(
                            'accountability_id' => $id,
                            'asset_id' => $rs->id,
                            'asset_code' => $rs->assetacode,
                            'quantity' => 1,
                            'cost' => $rs->purchaseprice,
                            'description' => $rs->name,
                            'brand' => $rs->brand,
                            'modelno' => $rs->modelno,
                            'serialno' => $rs->serialno,
                            'remarks' => $this->input->post('asset_remarks'),
                            'type' => 'Asset',
                            'amount' => 1 * $rs->purchaseprice,
                        );
                        $data2 = array(
                            'last_edited_by' => $session_data2,
                            'last_edited_dt' => $date,
                            'last_edited_id' => $this->user_data['emp_id'],
                        );

                        $insert_id = $this->save_accountability_edit($id, $data2);

                        $noDuplicate = $this->checkDuplicateAssetEdit($det->id, "Asset");

                        /*** if (empty($noDuplicate)) { ***/
                        if ($noDuplicate) {
                            $insert = $this->save_content_edit($data);
                            if ($insert) {
                                $resultset["response"] = true;
                                $resultset["data"] = $det;
                                $resultset["id"] = $this->input->post('asset_id');
                                $resultset["message"] = "Item added successfully!";
                                $resultset["state"] = "Success!";
                            } else {
                                $resultset["response"] = false;
                                $resultset["message"] = "Adding of asset/item to accountability failed!";
                                $resultset["state"] = "Warning!";
                            }
                        } else {
                            $resultset["response"] = false;
                            $resultset["message"] = "Asset/Item `{$det->assetacode}` is already on the list or already accounted!";
                            $resultset["state"] = "Warning!";
                        }
                    }
                }
            }

            if ($hasAssetName) {
                $session_data = $this->user_data['emp_id'];
                $det = $this->getAssetDetail($this->input->post('asset_id'));
                $data = array(
                    'accountability_id' => $id,
                    'asset_id' => $det->id,
                    'asset_code' => $this->input->post('asset_code'),
                    'quantity' => $this->input->post('asset_qty'),
                    'cost' => $this->input->post('asset_cost'),
                    'description' => $det->name,
                    'brand' => $det->brand,
                    'modelno' => $det->modelno,
                    'serialno' => $det->serialno,
                    'remarks' => $this->input->post('asset_remarks'),
                    'type' => 'Asset',
                    'amount' => $this->input->post('asset_qty') * $this->input->post('asset_cost'),
                );
                $data2 = array(
                    'last_edited_by' => $session_data2,
                    'last_edited_dt' => $date,
                    'last_edited_id' => $this->user_data['emp_id'],
                );

                $insert_id = $this->save_accountability_edit($id, $data2);
                $noDuplicate = $this->checkDuplicateAssetEdit($det->id, "Asset");
                if ($noDuplicate) {
                    $insert = $this->save_content_edit($data);
                    if ($insert) {
                        $resultset["response"] = true;
                        $resultset["data"] = $det;
                        $resultset["id"] = $this->input->post('asset_id');
                        $resultset["message"] = "Item added successfully!";
                        $resultset["state"] = "Success!";
                        $this->core_layout->setEventLog("Add Asset for Accountability - Add asset ".$data['asset_code'].".", "add", "success", "gcceforms", "user");
                    } else {
                        $resultset["response"] = false;
                        $resultset["message"] = "Adding of asset/item to accountability failed!";
                        $resultset["state"] = "Warning!";
                        $this->core_layout->setEventLog("Add Asset for Accountability - Failed add asset ".$data['asset_code'].".", "add", "error", "gcceforms", "system");
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["message"] = "Asset/Item `{$det->assetacode}` is already on the list or already accounted!";
                    $resultset["state"] = "Warning!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["state"] = "Warning!";
                $resultset["message"] = "Asset component(s) has no `asset code/asset name` please update!";
            }
        } else {
            $resultset["response"] = false;
            $resultset["message"] = "No data found for accountability!";
            $resultset["state"] = "Error!";
        }

        return $resultset;
    }

    private function checkDuplicateAsset($userId, $assetId, $type) {
        $tempCheck = $this->db->get_where("gcceforms.accountability_body_temp", array("user_id" => $userId, "asset_id" => $assetId, "type" => $type));
        if ($tempCheck->num_rows() == 0) {
            return true;
        } else {
            return false;
        }
    }

    private function checkDuplicateAssetEdit($assetId, $type) {
        $this->db->join("gcceforms.accountability acct", "acct.id = acct_body.accountability_id", "INNER");
        $where = array(
            "acct_body.asset_id" => $assetId,
            "acct_body.type" => $type,
            "acct_body.is_returned" => 0,
            "acct.status !=" => "Cancelled"
        );
        $tempCheck = $this->db->get_where("gcceforms.accountability_body acct_body", $where);
        if ($tempCheck->num_rows() == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function save_content_temp($data) {
        $this->db->insert('gcceforms.accountability_body_temp', $data);
        return $this->db->insert_id();
    }

    public function save_content_edit($_data) {
        $isAdded = false;
        $inserted = $this->db->insert("gcceforms.accountability_body", $_data);
        if($inserted){
            $tempData = (object)$_data;
            $tempType = strtolower($tempData->type);
            if(isset($tempData->asset_id) && $tempData->asset_id && $tempType == "asset"){
                $tempUpdated = $this->db->update("gccasset.assets", array("is_borrowed"=>1), array("id"=>$tempData->asset_id));
                if($tempUpdated){ $isAdded = true; }
            }
            elseif(isset($tempData->asset_id) && $tempData->asset_id && $tempType == "vehicle"){
                $tempUpdated = $this->db->update("gccasset.vehicles", array("is_borrowed"=>1), array("id"=>$tempData->asset_id));
                if($tempUpdated){ $isAdded = true; }
            }
        }
        /*** return $this->db->insert_id(); ***/
        return $isAdded;
    }

    function deleteTemp($id) {
        $session_data = $this->user_data['emp_id'];
        $asset_code = $this->db->get_where("gcceforms.accountability_body_temp", array("id"=>$id))->row();
        // $query = $this->db->query("DELETE FROM gcceforms.accountability_body_temp WHERE id=$id && user_id=$session_data");
        $this->db->where('id', $id);
        $this->db->where('user_id', $session_data);
        $query = $this->db->delete('gcceforms.accountability_body_temp');
        
        if($query){
            $this->core_layout->setEventLog("New Accountability - Remove {$asset_code->type} {$asset_code->asset_code} for accountability.", "delete", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("New Accountability - Failed remove {$asset_code->type} {$asset_code->asset_code} for accountability.", "delete", "error", "gcceforms", "system");
        }
        return $query;
    }

    function delete($id) {
        $resultset = array();
        $qTemp = $this->db->get_where("gcceforms.accountability_body", array("id"=>$id));
        if($qTemp->num_rows() == 1){
            $tempUpdated = false;
            $qTempRow = $qTemp->row();
            $tempType = strtolower($qTempRow->type);
            if(isset($qTempRow->asset_id) && $qTempRow->asset_id && $tempType == "asset"){
                $tempUpdated = $this->db->update("gccasset.assets", array("is_borrowed"=>0), array("id"=>$qTempRow->asset_id));
            }
            elseif(isset($qTempRow->asset_id) && $qTempRow->asset_id && $tempType == "vehicle"){
                $tempUpdated = $this->db->update("gccasset.vehicles", array("is_borrowed"=>0), array("id"=>$qTempRow->asset_id));
            }
            if($tempUpdated){
                $deleted = $this->db->delete("gcceforms.accountability_body", array("id"=>$id));
                if($deleted){
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Accountability `{$qTempRow->type}` with asset name `{$qTempRow->description}` has been removed!";
                    $resultset["state"] = "success";
                    $this->core_layout->setEventLog("Edit Accountability - removed {$qTempRow->type} {$this->getAssetCode($qTempRow->asset_id, $tempType)}.", "add", "success", "gcceforms", "user");
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to remove accountability `{$qTempRow->type}` with asset name `{$qTempRow->description}`, error removing data!";
                    $resultset["state"] = "error";
                    $this->core_layout->setEventLog("Edit Accountability - failed remove of {$qTempRow->type} {$this->getAssetCode($qTempRow->asset_id, $tempType)}.", "add", "error", "gcceforms", "system");
                }
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to remove and update accountability is borrowed status of `{$qTempRow->type}` with asset name `{$qTempRow->description}`!";
                $resultset["state"] = "warning";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No data found, failed to remove accountability!";
            $resultset["state"] = "error";
        }
        return $resultset;
    }

    /** delete assets of accountability_body_temp*/
    function clearTemp() {
        $session_data = $this->user_data['emp_id'];
        $query = $this->db->query("DELETE FROM gcceforms.accountability_body_temp WHERE user_id=$session_data");
        if($query){
            $this->core_layout->setEventLog("New Accountability - Delete all assets.", "delete", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("New Accountability - Failed delete of all assets.", "add", "error", "gcceforms", "system");
        }
        return $query;
    }
    /** end function */

    /**  delete assets of accountability_body*/
    function clear($id) {
        $tempUpdated = false;
        $qTemp = $this->db->get_where("gcceforms.accountability_body", array("accountability_id"=>$id));
        if($qTemp->num_rows() > 0){
            foreach ($qTemp->result() as $key => $value) {
                $tempType = strtolower($value->type);
                if(isset($value->asset_id) && $value->asset_id && $tempType == "asset"){
                    $tempUpdated = $this->db->update("gccasset.assets", array("is_borrowed"=>0), array("id"=>$value->asset_id));
                }
                elseif(isset($value->asset_id) && $value->asset_id && $tempType == "vehicle"){
                    $tempUpdated = $this->db->update("gccasset.vehicles", array("is_borrowed"=>0), array("id"=>$value->asset_id));
                }
            }
        }
        if($tempUpdated){
            $tempUpdated = $this->db->query("DELETE FROM gcceforms.accountability_body where accountability_id=$id");
            if($tempUpdated){
                $this->core_layout->setEventLog("Edit Accountability - Delete all assets in ".$this->getAccountabilityCode($id).".", "delete", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Edit Accountability - Failed delete of all assets in ".$this->getAccountabilityCode($id).".", "delete", "error", "gcceforms", "system");
            }
        }
        return $tempUpdated;
    }
    /** end function */

    function getAccountabilityCode($id){
        $this->db->select("reference_no");
        return $this->db->get_where("gcceforms.accountability", array("id"=>$id))->row("reference_no");
    }

    function editTempDetails($id) {
        $query = $this->db->query("SELECT * FROM gcceforms.accountability_body_temp WHERE id = '$id'");
        return $query->row();
    }

    function editDetails($id) {
        $query = $this->db->query("SELECT * FROM gcceforms.accountability_body WHERE id = '$id'");
        return $query->row();
    }

    /** function to edit/add remarks on assets/vehicles to be accounted for new accountability*/
    function editAssetTemp($get) {
        $this->input->post();
        $data = array(
            'remarks' => $this->input->post('edit_asset_remarks'),
        );
        if ($get) {
            $this->db->where('gcceforms.accountability_body_temp.id', $get);
            return $this->db->update('gcceforms.accountability_body_temp', $data);
        }
    }
    /** end of function */

    /** function to edit/add remarks on assets/vehicles to be accounted for existing accountability*/
    function editAsset($get) {
        $this->input->post();
        $data = array(
            'remarks' => $this->input->post('edit_asset_remarks'),
        );
        $asset_code = $this->db->get_where("gcceforms.accountability_body", array("id"=>$get))->row('asset_code');
        if ($get) {
            $this->db->where('gcceforms.accountability_body.id', $get);
            if($this->db->update('gcceforms.accountability_body', $data)){
                $this->core_layout->setEventLog("Edit Accountability - Edit remarks of asset/vehicle ".$asset_code.".", "add", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Edit Accountability - Failed edit remarks of asset/vehicle ".$asset_code.".", "add", "error", "gcceforms", "system");
            }
        }
    }
    /** end of function */

    /** function save vehicle to be accounted in a temporary table */
    function saveVehicleTemp() {
        $resultset = array();
        $assetId = $this->input->post('asset_id');
        if ($assetId) {
            $hasAssetName = true;
            $component = $this->db->get_where("gccasset.vehicles", array("motherID" => $assetId, "isCompo" => 1, "is_borrowed"=>0));

            if ($component->num_rows() > 0) {
                foreach ($component->result() as $rs) {
                    if (!$rs->name || !$rs->gen_code) {
                        $hasAssetName = false;
                    } else {
                        $session_data = $this->user_data['emp_id'];

                        $data = array(
                            'user_id' => $session_data,
                            'asset_id' => $rs->id,
                            'asset_code' => $rs->gen_code,
                            'quantity' => 1,
                            'cost' => isset($rs->purchaseprice) ? $rs->purchaseprice : 0,
                            'description' => $rs->description,
                            'brand' => $rs->brand,
                            'modelno' => $rs->model,
                            'serialno' => $rs->serialno,
                            'plateno' => $rs->plateno,
                            'engineno' => $rs->engineno,
                            'chasisno' => $rs->chasisno,
                            'remarks' => $this->input->post('asset_remarks'),
                            'type' => 'Vehicle',
                            'amount' => 1 * $rs->purchaseprice,
                        );

                        $noDuplicate = $this->checkDuplicateAsset($session_data, $rs->id, "Asset");
                        if ($noDuplicate) {
                            $insert = $this->save_content_temp($data);
                            if ($insert) {
                                $resultset["response"] = true;
                                $resultset["data"] = $rs;
                                $resultset["id"] = $this->input->post('asset_id');
                                $resultset["message"] = "Item added successfully";
                                $resultset["state"] = "Success!";
                            } else {
                                $resultset["response"] = false;
                                $resultset["message"] = "Adding of asset/item to accountability failed!";
                                $resultset["state"] = "Error!";
                            }
                        } else {
                            $resultset["response"] = false;
                            $resultset["message"] = "Asset/Item `{$rs->assetacode}` is already on the list or already accounted!";
                            $resultset["state"] = "Error!";
                        }
                    }
                }
            }

            if ($hasAssetName) {
                $session_data = $this->user_data['emp_id'];
                $det = $this->getVehicleDetail($this->input->post('asset_id'));

                $data = array(
                    'user_id' => $session_data,
                    'asset_id' => $det->id,
                    'asset_code' => $det->gen_code,
                    'quantity' => 1,
                    'cost' => $det->purchaseprice,
                    'description' => $det->description,
                    'brand' => $det->brand,
                    'modelno' => $det->model,
                    'serialno' => $det->serialno,
                    'plateno' => $det->plateno,
                    'engineno' => $det->engineno,
                    'chasisno' => $det->chasisno,
                    'remarks' => $this->input->post('asset_remarks'),
                    'type' => 'Vehicle',
                    'amount' => 1 * $det->purchaseprice,
                );

                $noDuplicate = $this->checkDuplicateAsset($session_data, $det->id, "Vehicle");
                if ($noDuplicate) {
                    $insert = $this->save_content_temp($data);
                    if ($insert) {
                        $resultset["response"] = true;
                        $resultset["data"] = $det;
                        $resultset["id"] = $this->input->post('asset_id');
                        $resultset["message"] = "Item added successfully";
                        $resultset["state"] = "Success!";
                        $this->core_layout->setEventLog("Add Vehicle for Accountability - Add vehicle ".$data['asset_code'].".", "add", "success", "gcceforms", "user");
                    } else {
                        $resultset["response"] = false;
                        $resultset["message"] = "Adding of asset/item to accountability failed!";
                        $resultset["state"] = "Warning!";
                        $this->core_layout->setEventLog("Add Vehicle for Accountability - Add vehicle ".$data['asset_code'].".", "add", "error", "gcceforms", "system");
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["message"] = "Asset/Item `{$det->gen_code}` is already on the list or already accounted!";
                    $resultset["state"] = "Warning!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["message"] = "Asset Component(s), No `asset code/asset name` please update!";
                $resultset["state"] = "Warning!";
            }
        } else {
            $resultset["response"] = false;
            $resultset["message"] = "No data found for accountability!";
            $resultset["state"] = "Error!";
        }
        return $resultset;
    }
    /** end of function */

    /** function to save vehicle for existing accountability */
    function saveVehicleEdit($id) {
        $date = date('Y-m-d H:i:s');
        $resultset = array();
        $assetId = $this->input->post('asset_id');
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $session_data2 = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";

        if ($assetId) {
            $hasAssetName = true;
            $component = $this->db->get_where("gccasset.vehicles", array("motherID" => $assetId, "isCompo" => 1, "is_borrowed"=>0));

            if ($component->num_rows() > 0) {
                foreach ($component->result() as $rs) {
                    if (!$rs->name || !$rs->gen_code) {
                        $hasAssetName = false;
                    } else {
                        $session_data = $this->user_data['emp_id'];

                        $data = array(
                            'accountability_id' => $id,
                            'asset_id' => $rs->id,
                            'asset_code' => $rs->gen_code,
                            'quantity' => 1,
                            'cost' => $rs->purchaseprice ? $rs->purchaseprice : 0,
                            'amount' => $rs->purchaseprice ? $rs->purchaseprice : 0,
                            'description' => $rs->description,
                            'brand' => $rs->brand,
                            'modelno' => $rs->model,
                            'serialno' => $rs->serialno,
                            'plateno' => $rs->plateno,
                            'engineno' => $rs->engineno,
                            'chasisno' => $rs->chasisno,
                            'remarks' => $this->input->post('asset_remarks'),
                            'type' => 'Vehicle',
                        );

                        $noDuplicate = $this->checkDuplicateAssetEdit($rs->id, "Vehicle");
                        if ($noDuplicate) {
                            $insert = $this->save_content_edit($data);
                            if ($insert) {
                                $resultset["response"] = true;
                                $resultset["data"] = $rs;
                                $resultset["id"] = $this->input->post('asset_id');
                                $resultset["message"] = "Item added successfully";
                                $resultset["state"] = "Success!";
                            } else {
                                $resultset["response"] = false;
                                $resultset["message"] = "Adding of asset/item to accountability failed!";
                                $resultset["state"] = "Error!";
                            }
                        } else {
                            $resultset["response"] = false;
                            $resultset["message"] = "Asset/Item `{$rs->assetacode}` is already on the list or already accounted!";
                            $resultset["state"] = "Error!";
                        }
                    }
                }
            }

            if ($hasAssetName) {
                $session_data = $this->user_data['emp_id'];
                $det = $this->getVehicleDetail($this->input->post('asset_id'));

                $data = array(
                    'accountability_id' => $id,
                    'asset_id' => $det->id,
                    'asset_code' => $det->gen_code,
                    'quantity' => 1,
                    'cost' => ($det->purchaseprice) != 0 ? $det->purchaseprice : 0,
                    'description' => $det->description,
                    'brand' => $det->brand,
                    'modelno' => $det->model,
                    'serialno' => $det->serialno,
                    'plateno' => $det->plateno,
                    'engineno' => $det->engineno,
                    'chasisno' => $det->chasisno,
                    'remarks' => $this->input->post('asset_remarks'),
                    'type' => 'Vehicle',
                    'amount' => ($this->input->post('asset_cost') != 0 ? intval(1) * $this->input->post('asset_cost') : intval(1) * $det->purchaseprice),
                );

                $data2 = array(
                    'last_edited_by' => $session_data2,
                    'last_edited_dt' => $date,
                    'last_edited_id' => $this->user_data['emp_id'],
                );

                $insert_id = $this->save_accountability_edit($id, $data2);

                $noDuplicate = $this->checkDuplicateAssetEdit($det->id, "Vehicle");
                if ($noDuplicate) {
                    $insert = $this->save_content_edit($data);
                    if ($insert) {
                        $resultset["response"] = true;
                        $resultset["data"] = $det;
                        $resultset["id"] = $this->input->post('asset_id');
                        $resultset["message"] = "Item added successfully";
                        $resultset["state"] = "Success!";
                        $this->core_layout->setEventLog("Add Vehicle/Equipment for Accountability - Add vehicle ".$data['asset_code'].".", "add", "success", "gcceforms", "user");
                    } else {
                        $resultset["response"] = false;
                        $resultset["message"] = "Adding of asset/item to accountability failed!";
                        $resultset["state"] = "Warning!";
                        $this->core_layout->setEventLog("Add Vehicle/Equipment for Accountability - Failed add vehicle ".$data['asset_code'].".", "add", "success", "gcceforms", "user");
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["message"] = "Asset/Item `{$det->gen_code}` is already on the list or already accounted!";
                    $resultset["state"] = "Warning!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["message"] = "Asset Component(s), No `asset code/asset name` please update!";
                $resultset["state"] = "Warning!";
            }
        } else {
            $resultset["response"] = false;
            $resultset["message"] = "No data found for accountability!";
            $resultset["state"] = "Error!";
        }
        return $resultset;
    }
    /** end of function */

    function doesNotExistAccountabilityBody($assetId=0, $acctId=0, $acctType="asset"){
        $response = false;
        if($assetId && $acctId && $acctType){
            $qTemp = $this->db->get_where("gcceforms.accountability_body", 
                array(
                    "accountability_id"=>$acctId,
                    "asset_id"=>$assetId,
                    "type"=>$acctType
                )
            );
            
            if($qTemp->num_rows() == 0){
                $response = true;
            }
        }
        return $response;
    }

    function saveTemp() {
        $resultset = array();
        $post = $this->input->post();
        $isUrgent = $this->input->post('urgent');
        $check_content = $this->getContents($this->user_data['emp_id']);

        // $this->db->trans_begin();

        if (isset($check_content) && $check_content) {
            if (isset($post) && $post) {
                $check = 0;
                $companyTo = (isset($post["company_to"]) && $post["company_to"]) ? $post["company_to"] : "";
                $departmentTo = (isset($post["department_to"]) && $post["department_to"]) ? $post["department_to"] : "";
                $dateIssued = (isset($post["issue_dt"]) && $post["issue_dt"]) ? $post["issue_dt"] : "";
                
                if ($post["contract_check"] == 1) {
                    $check = 1;
                    $issuedTo = (isset($post["contractor"]) && $post["contractor"]) ? $post["contractor"] : "";
                } else {
                    $check = 0;
                    $issuedTo = (isset($post["issued_to"]) && $post["issued_to"]) ? $post["issued_to"] : "";
                }
                if ($companyTo && $departmentTo && $issuedTo && $dateIssued) {
                    $tempRs = (array)$this->user_data;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $session_data = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $date = date('Y-m-d H:i:s');
                    $year = substr($date, 2, 2);
                    $month = substr($date, 5, 2);
                    $dateIssued = date("Y-m-d H:i:s", strtotime($dateIssued));
                    $list = $this->series($year, $month);
                    $series = '';
                    $a = sizeof($list) - 1;
                    if (sizeof($list) > 0) {
                        $x = $list[$a]->ref_series;
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

                    if ($isUrgent) {
                        $isUrgent = 1;
                    } else {
                        $isUrgent = 0;
                    }

                    $referenceNumber = "AF{$year}-{$month}-{$series}";
                    $data = array(
                        'ref_yr' => $year,
                        'ref_series' => $series,
                        'ref_month' => $month,
                        'reference_no' => $referenceNumber,
                        'company' => $companyTo,
                        'department' => $departmentTo,
                        'issued_to' => $issuedTo,
                        'is_contract' => $check,
                        'is_urgent' => $isUrgent,
                        'date_issued' => $dateIssued,
                        'status' => 'Pending Accounting Notes',
                        'created_by' => $session_data,
                        'created_dt' => $date,
                        'created_id' => $this->user_data['emp_id'],
                    );

                    $insert_id = $this->save_accountability($data);
                    if ($insert_id) {
                        $dataSaveItems = $this->save_accountability_body($insert_id, $this->user_data['emp_id']);
                        if ($dataSaveItems) {
                            $isContractor = false;
                            $up = $this->update_is_borrowed_temp();
                            $this->clearTemp();

                            $employeeName = "No Employee Name";

                            if ($check) {
                                $query = $this->db->get_where("gcchris.tblcontractor", array("id" => $issuedTo));
                                if ($query->num_rows() == 1) {
                                    $isContractor = true;
                                    $employeeName = $query->row()->contractor;
                                }
                            } else {
                                $list = $this->getIssuedTo($issuedTo);
                                $employeeName = $list->lastname . ', ' . $list->firstname;
                            }

                            $employeeName = mb_strtoupper($employeeName);
                            $dataAcct = $this->contentDetail($insert_id);
                            $sendType = "add";
                            if($this->email_send($employeeName, $referenceNumber, $isUrgent, $sendType, $companyTo, $dataAcct)){
                                $this->core_layout->setEventLog("Email sending - Email sent for accountability reference ".$referenceNumber.".", "add", "success", "gcceforms", "user");
                            }

                        } else {
                            $resultset["status"] = false;
                            $resultset["toastr_msg"] = "Failed to save data items on accountability";
                            $resultset["toastr_status"] = "error";
                        }
                        $this->core_layout->setEventLog("New accountability - Add accountability ".$referenceNumber.".", "add", "success", "gcceforms", "user");
                    } else {
                        $resultset["status"] = false;
                        $resultset["toastr_msg"] = "Failed to save accountability";
                        $resultset["toastr_status"] = "error";
                        $this->core_layout->setEventLog("New accountability - Failed add accountability ".$referenceNumber.".", "add", "error", "gcceforms", "system");
                    }
                } else {
                    $resultset["status"] = false;
                    $resultset["toastr_msg"] = "Failed to save accountability, no data for saving";
                    $resultset["toastr_status"] = "error";
                }
            } else {
                $resultset["status"] = false;
                $resultset["toastr_msg"] = "Failed to save accountability, no data for saving";
                $resultset["toastr_status"] = "error";
            }
        } else {
            $resultset["status"] = false;
            $resultset["toastr_msg"] = "Required atleast 1 item. <br><i style='font-size: small;'>Please refresh the page if problem insists.</i>";
            $resultset["toastr_status"] = "error";
        }
        return $resultset;
    }

    function email_send($employeeName, $referenceNumber, $isUrgent, $sendType, $companyTo, $datar = null, $resendEmail=false) {
        $arrData = $datar[0];
        $resultset = array();
        $serverName = $_SERVER['SERVER_NAME'];

        $coreLogs = $this->core_layout->coreLogs();
        $coreLogs->setLogModule("eforms-accountability");
        $coreLogs->setLogTable("gcceforms.accountability");
        $coreLogs->setLogFieldId($arrData->id);

        $employeeName = mb_strtoupper($employeeName);
        $emailTo = array();
        $emailCcc = array();
        $emailBcc = array("seniordeveloper02@gccaggregates.com");
        //qmsassistant@gccph.com removed from bcc

        $emailTo = $this->sendEmailCompanyTo($companyTo);
        $emailTo = ($emailTo) ? $emailTo : array("cfo@hwcc.ph", "finance@supermix.ph", "proxfin@hwcc.ph", "gccfin@gccph.com");
        //cfo@hwcc.ph, finance@supermix.ph, proxfin@hwcc.ph, gccfin@gccph.com
        $emailCcc = $this->sendEmailCompanyCcc($companyTo);
        $emailCcc = ($emailCcc)? $emailCcc: array();
        $message = "";

        $tempData = array();
        $tempData["accountability_id"] = $arrData->id;
        $tempData["created_by"] = $this->core_layout->getCurrentEmployeeId();
        
        if ($sendType == "add") {
            $data = array("employee_name" => $employeeName, "is_urgent" => $isUrgent, "type" => "add");
            $message .= $this->load->view("eforms/email_templates/email_pan_template", $data, true);

            $module = "eforms_accountability_pan";
            $email_title = "Accountability";
            $content_title = "Pending Accounting Notes - {$referenceNumber}";
            if ($isUrgent) { $email_title = "Accountability - [ URGENT ]"; }
            $content = $message;
            $overrideMailer = array();
            /*** $overrideMailer["email_user"] = "gcceforms@gmail.com";
            $overrideMailer["email_pass"] = "Sc0t2366"; ***/
            if($serverName !== "localhost" && $serverName !== "dev.gccph.com" && $serverName !== "192.168.7.96"):
                if($emailTo){ $overrideMailer["send_to"] = $emailTo; }
                if($emailCcc){ $overrideMailer["send_cc"] = $emailCcc; }
            endif;
            if($emailBcc){ $overrideMailer["send_bcc"] = $emailBcc; }

            $tempData["message"] = "New accountability has been added with reference # {$referenceNumber}";
            $tempData["acct_type"] = 1;
        } elseif ($sendType == "return") {
            $content = $this->contentBodyDetail($arrData->id);
            // $datar["meta"] = $content['data'];
            $data = (array) $datar[0];
            $data['meta'] = $content['data'];
            $message .= $this->load->view("eforms/email_templates/email_returned_template", $data, true);
            $module = "eforms_accountability_returned";
            $email_title = "Accountability";
            $content_title = "Returned Accountability - {$arrData->reference_no}";
            if ($arrData->is_urgent) {
                $email_title = "Accountability - [ URGENT ]";
            }
            $content = $message;

            $overrideMailer = array();
            /*** $overrideMailer["email_user"] = "gcceforms@gmail.com";
            $overrideMailer["email_pass"] = "Sc0t2366"; ***/
            if($serverName !== "localhost" && $serverName !== "dev.gccph.com" && $serverName !== "192.168.7.96"):
                if($emailTo){ $overrideMailer["send_to"] = $emailTo; }
                if($emailCcc){ $overrideMailer["send_cc"] = $emailCcc; }
            endif;
            if($emailBcc){ $overrideMailer["send_bcc"] = $emailBcc; }

            $tempData["message"] = "Return accountability has been saved with reference # {$arrData->reference_no}";
            $tempData["acct_type"] = 2;
        } elseif ($sendType == "hr_note") {
            $data = array("employee_name" => $arrData->display_name, "is_urgent" => $arrData->is_urgent, "type" => "hr_note");
            $message .= $this->load->view("eforms/email_templates/email_pan_template", $data, true);

            $module = "eforms_accountability_hrn";
            $email_title = "Accountability";
            $content_title = "Pending Payroll Notes - {$arrData->reference_no}";
            if ($arrData->is_urgent) { $email_title = "Accountability - [ URGENT ]"; }
            $content = $message;

            $overrideMailer = array();
            $overrideMailer["send_bcc"] = $emailBcc;

            $tempData["message"] = "Accountability has been set to Payroll notes with reference # {$arrData->reference_no}";
            $tempData["acct_type"] = 3;
        }

        $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
        if ($sent) {
            $tempData["email_sent"] = 1;
            $resultset["status"] = true;
            $resultset["ref_no"] = $referenceNumber;
            $resultset["employee"] = $employeeName;
            
            $resultset["toastr_msg"] = ($resendEmail)? "Re-send accountability email, email has been sent" : "Accountability saved, email has been sent";
            $resultset["toastr_status"] = "success";
            
            if($resendEmail){
                $coreLogs->logNotification("Re-send accountability email, email has been sent", "success");
            }else{
                $coreLogs->logNotification("Accountability saved, email has been sent", "success");
            }
        } else {
            $resultset["status"] = false;
            $resultset["toastr_msg"] = ($resendEmail)? "Re-send accountability email, failed to send email!": "Accountability saved, failed to send email!";
            $resultset["toastr_status"] = "error";
            
            if($resendEmail){
                $coreLogs->logNotification("Re-send accountability email, failed to send email!", "error");
            }else{
                $coreLogs->logNotification("Accountability saved, failed to send email!", "error");
            }
        }
        $this->db->insert($this->acctLogsTable, $tempData);
        return $resultset;
    }

    private function sendEmailCompanyCcc($companyTo = null) {
        $email = "";
        $ccTo = null;

        if (is_numeric($companyTo)) {
            $this->db->select('code, cc_to');
            $this->db->from("gcchris.tblcompanies");
            $this->db->where("id", $companyTo);
            $query = $this->db->get();
            $data = $query->row_array();
            $companyTo = $data['code'];
            $ccTo = $data['cc_to'];
        }

        if ($ccTo) {
            $email = $ccTo;
            // $companyTo = strtoupper($companyTo);
            // if ($companyTo == "GC&C" || $companyTo == "GC&C, INC.") {
            //     // $email = "cfo@gccph.com, gccfin@gccph.com";
            //     //update 06-24-2021
            //     $email = "kttorres@gccph.com"; // no update
            // }
            // if ($companyTo == "MTAG" || $companyTo == "MOUNTAIN AGGREGATES") {
            //     $email = "cfo@hwcc.ph, gccfin@gccph.com"; // ms karen cfo@hwcc.ph, danilo eriman no update
            // }
            // if ($companyTo == "MONEYMILL" || $companyTo == "MONEY MILL FINANCING") {
            //     $email = "cfo@hwcc.ph, gccfin@gccph.com"; // ms karen cfo@hwcc.ph, danilo eriman no update
            // }
            // if ($companyTo == "HOUSEHOLD") {
            //     $email = "cfo@hwcc.ph, gccfin@gccph.com"; // ms karen cfo@hwcc.ph, danilo eriman no update
            // }
            // if ($companyTo == "PROXIMA") {
            //     $email = "cfo@hwcc.ph, proxfin@hwcc.ph"; // ms karen cfo@hwcc.ph, ms flor proxfin@hwcc.ph
            // }
            // if ($companyTo == "HOMEWORLD" || $companyTo == "HOME WORLD CONSTRUCTION") {
            //     $email = "cfo@hwcc.ph, finance@supermix.ph"; // ms karen cfo@hwcc.ph, mevie finance@supermix.ph
            // }
            // if ($companyTo == "WEGAPLAS" || $companyTo == "WEGAPLAS CORPORATION") {
            //     $email = "cfo@hwcc.ph, finance@supermix.ph"; // ms karen cfo@hwcc.ph, mevie finance@supermix.ph
            // }
            // if ($companyTo == "CONTRACTOR") {
            //     $email = "cfo@hwcc.ph"; // ms karen cfo@hwcc.ph
            // }
            // if ($companyTo == "ADVERCOM" || $companyTo == "ADVERCOM MEDIA GROUP CO.") {
            //     $email = "cfo@hwcc.ph"; // ms karen cfo@hwcc.ph
            // }
            // if ($companyTo == "ESTANCIA") {
            //     $email = "cfo@hwcc.ph"; // ms karen cfo@hwcc.ph
            // }
            $tempEmail = explode(",", $email);
            $tempEmail = array_map("trim", $tempEmail);
            return $tempEmail;
        }else{
            return false;
        }
    }

    private function sendEmailCompanyTo($companyTo = null, $isContractor = false) {
        $tempEmail = null;
        $sentTo = null;
        $email = "cfo@hwcc.ph, finance@supermix.ph, proxfin@hwcc.ph, gccfin@gccph.com";
        if (is_numeric($companyTo)) {
            $this->db->select('code, email_to');
            $this->db->from("gcchris.tblcompanies");
            $this->db->where("id", $companyTo);
            $query = $this->db->get();
            $data = $query->row_array();
            $companyTo = $data['code'];
            $sentTo = $data['email_to'];
        }

        if ($sentTo) {
            // $companyTo = strtoupper($companyTo);
            // if ($companyTo == "GC&C" || $companyTo == "GC&C, INC.") {
            //     //update 06-24-2021
            //     $email = "financeassociate@gccaggregates.com"; //janry financeassociate@gccaggregates.com
            //     // $email = "ejbuenconsejo@gccph.com";

            // }
            // if ($companyTo == "PROXIMA") {
            //     $email = "kttorres@gccph.com"; //no update
            // }
            // if ($companyTo == "HOMEWORLD" || $companyTo == "HOME WORLD CONSTRUCTION") {
            //     $email = "financeassociate02@homeworldconstruction.com"; //arren financeassociate@homeworldconstruction.com
            // }
            // if ($companyTo == "WEGAPLAS" || $companyTo == "WEGAPLAS CORPORATION") {
            //     $email = "financeassociate02@homeworldconstruction.com";  //arren financeassociate@homeworldconstruction.com
            // }
            // if ($companyTo == "CROSSCAP" || $companyTo == "CROSSCAP FINANCING") {
            //     $email = "ccdelapaz@gccph.com"; //no data
            // }
            // if ($companyTo == "MCDS") {
            //     $email = "chiefcollectionofficer@gccaggregates.com"; // edlyn chiefcollectionofficer@gccaggregates.com
            // }
            // if ($companyTo == "MTAG" || $companyTo == "MOUNTAIN AGGREGATES") {
            //     $email = "chiefcollectionofficer@gccaggregates.com"; // edlyn chiefcollectionofficer@gccaggregates.com
            // }
            // if ($companyTo == "ADVERCOM" || $companyTo == "ADVERCOM MEDIA GROUP CO.") {
            //     $email = "gccfin@gccph.com"; // no update
            // }
            // if ($companyTo == "HOUSEHOLD") {
            //     $email = "chiefcollectionofficer@gccaggregates.com"; // edlyn chiefcollectionofficer@gccaggregates.com
            // }
            // if ($companyTo == "ESTANCIA") {
            //     $email = "financeassociate@gccaggregates.com, gccfin@gccph.com"; //janry financeassociate@gccaggregates.com, danilo eriman gccfin@gccph.com
            // }
            // if ($companyTo == "RETIREE") {
            //     $email = "chiefcollectionofficer@gccaggregates.com, kttorres@gccph.com, proxfinasst01@hwcc.ph, amsartorio@gccph.com, financeassociate@gccaggregates.com";
            // }
            // if ($companyTo == "CONTRACTOR") {
            //     $email = "proxfin@hwcc.ph, finance@supermix.ph"; // proxfin proxfin@hwcc.ph, costaccountant mevie finance@supermix.ph
            // }
            
            $tempEmail = explode(",", $sentTo);
            $tempEmail = array_map("trim", $tempEmail);
        }

        /*** if ($isContractor) {
            $email = "warehouse@gccph.com";
        } ***/
        
        return $tempEmail;
    }

    function saveEdit($id) {
        $resultset = array();
        $post = $this->input->post();
        $isUrgent = $this->input->post('urgent');
        if (isset($post) && $post) {
            $check = 0;
            $companyTo = (isset($post["company_to"]) && $post["company_to"]) ? $post["company_to"] : "";
            $departmentTo = (isset($post["department_to"]) && $post["department_to"]) ? $post["department_to"] : "";
            $dateIssued = (isset($post["issue_dt"]) && $post["issue_dt"]) ? $post["issue_dt"] : "";
            $dateIssued = date("Y-m-d H:i:s", strtotime($dateIssued));
            if ($post["contract_check"] == 1) {
                $check = 1;
                $issuedTo = (isset($post["contractor"]) && $post["contractor"]) ? $post["contractor"] : "";
            } else {
                $check = 0;
                $issuedTo = (isset($post["issued_to"]) && $post["issued_to"]) ? $post["issued_to"] : "";
            }
            if ($companyTo || $departmentTo || $issuedTo || $dateIssued) {

                if ($isUrgent) {
                    $isUrgent = 1;
                } else {
                    $isUrgent = 0;
                }

                $tempRs = (array)$this->user_data;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $session_data = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $date = date('Y-m-d H:i:s');
                $data = array(
                    'company' => $companyTo,
                    'department' => $departmentTo,
                    'issued_to' => $issuedTo,
                    'is_contract' => $check,
                    'is_urgent' => $isUrgent,
                    'date_issued' => $dateIssued,
                    'last_edited_by' => $session_data,
                    'last_edited_dt' => $date,
                    'last_edited_id' => $this->user_data['emp_id'],
                );

                $insert_id = $this->save_accountability_edit($id, $data);
            } else {
                $resultset["status"] = false;
                $resultset["toastr_msg"] = "Failed to save accountability, no data for savings!";
                $resultset["toastr_status"] = "error";

                $this->core_layout->logNotification("Failed to save accountability, no data for saving!", "error");
            }
        } else {
            $resultset["status"] = false;
            $resultset["toastr_msg"] = "Failed to save accountability, no data for saving!";
            $resultset["toastr_status"] = "error";
        }
        return $resultset;
    }

    public function series($year, $month) {
        $this->db->select('ref_series');
        $this->db->from('gcceforms.accountability');
        $this->db->where('ref_yr', $year);
        $this->db->where('ref_month', $month);
        $this->db->order_by('ref_series', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function save_accountability($data) {
        $this->db->insert('gcceforms.accountability', $data);
        return $this->db->insert_id();
    }

    public function save_accountability_edit($id, $data) {
        $this->db->update('gcceforms.accountability', $data, array('id' => $id));
        $this->db->affected_rows();
    }

    public function save_accountability_body($id, $userId) {
        if ($id && $userId) {
            $contents = $this->getContents($userId);
            if (sizeof($contents) > 0) {
                foreach ($contents as $arr) {
                    $arrType = "";
                    if ($arr->type == "Asset") {
                        $arrType = "Asset";
                    }
                    if ($arr->type == "Consumable") {
                        $arrType = "Consumable";
                    }
                    if ($arr->type == "Vehicle") {
                        $arrType = "Vehicle";
                    }

                    $data = array(
                        'accountability_id' => $id,
                        'asset_id' => $arr->asset_id,
                        'asset_code' => $arr->asset_code,
                        'quantity' => $arr->quantity,
                        'cost' => $arr->cost,
                        'description' => $arr->description,
                        'brand' => $arr->brand,
                        'modelno' => $arr->modelno,
                        'serialno' => $arr->serialno,
                        'remarks' => $arr->remarks,
                        'amount' => $arr->amount,
                        'is_returned' => 0,
                        'date_returned' => "",
                        'type' => $arrType,
                    );
                    $this->db->insert('gcceforms.accountability_body', $data);
                }
                return true;
            }
        } else {
            return false;
        }
    }

    public function update_is_borrowed_temp() {
        $session_data = $this->user_data;
        $contents = $this->list_temp($session_data['emp_id']);

        foreach ($contents as $arr) {
            $arr = (object)$arr;
            $data2 = array(
                'is_borrowed' => 1,
            );
            if ($arr->type == "Asset") {
                $this->db->update('gccasset.assets', $data2, array('assetacode' => $arr->asset_code));
                $this->db->affected_rows();
            } else if ($arr->type == "Vehicle") {
                $this->db->update('gccasset.vehicles', $data2, array('gen_code' => $arr->asset_code));
                $this->db->affected_rows();
            }
        }
        return $contents;
    }

    public function list_temp($id) {
        $this->db->from('gcceforms.accountability_body_temp');
        $this->db->where('user_id', $id);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();

        $arrData = new stdClass();

        if ($query->num_rows() > 0) {
            $res = $query->result_array();
            $ndata = array();
            foreach ($res as $index => $rs) {
                $ndata[$index] = $rs;
                $type = (isset($rs["type"]) && $rs["type"]) ? $rs["type"] : "Consumable";
                $assetId = $rs['asset_id'];
                if ($assetId) {
                    if ($type == "Asset" || $type == "asset") {
                        $query2 = $this->db->get_where("gccasset.assets", array("id" => $assetId));
                        if ($query2->num_rows() > 0) {
                            $row = $query2->row();

                            $rowAssetName = (isset($row->name) && $row->name) ? $row->name : "No asset name";
                            $rowBrand = (isset($row->brand) && $row->brand) ? $row->brand : "---";
                            $rowModel = (isset($row->modelno) && $row->modelno) ? $row->modelno : "---";
                            $rowSerial = (isset($row->serialno) && $row->serialno) ? $row->serialno : "---";

                            $ndata[$index]["name"] = $rowAssetName;
                            $ndata[$index]["brand"] = (isset($ndata[$index]["brand"]) && $ndata[$index]["brand"]) ? $ndata[$index]["brand"] : $rowBrand;
                            $ndata[$index]["modelno"] = (isset($ndata[$index]["modelno"]) && $ndata[$index]["modelno"]) ? $ndata[$index]["modelno"] : $rowModel;
                            $ndata[$index]["serialno"] = (isset($ndata[$index]["serialno"]) && $ndata[$index]["serialno"]) ? $ndata[$index]["serialno"] : $rowSerial;

                            $ndata[$index]["is_component"] = (isset($row->isComponent) && $row->isComponent) ? true : false;

                            $this->db->select("id, company_code, name, assetacode, assetname, brand, modelno, serialno, remarks, purchaseprice");
                            $this->db->from('gccasset.assets');
                            $this->db->where('mother_asset', $row->id);
                            $this->db->where('isComponent', 1);
                            $this->db->group_start();
                            $this->db->where('status !=', 'junk');
                            $this->db->where('status !=', 'archived');
                            $this->db->group_end();
                            $query3 = $this->db->get();
                            if ($query3->num_rows() > 0) {
                                $compArrData = array();
                                foreach ($query3->result_array() as $rr) {
                                    $rr["name"] = (isset($rr["name"]) && $rr["name"]) ? $rr["name"] : "No asset name";
                                    $rr["purchaseprice"] = number_format($rr["purchaseprice"], 2, ".", "");
                                    $rr["quantity"] = 1;
                                    $ndata[$index]['components'][] = $rr;
                                }
                            }
                        }
                    }

                    if ($type == "Vehicle" || $type == "vehicle") {
                        $query2 = $this->db->get_where("gccasset.vehicles", array("id" => $assetId));
                        if ($query2->num_rows() > 0) {
                            $row = $query2->row();

                            $rowAssetName = (isset($row->name) && $row->name) ? $row->name : "No asset name";
                            $rowGenCode = (isset($row->gen_code) && $row->gen_code) ? $row->gen_code : "No Asset Code";
                            $rowBrand = (isset($row->brand) && $row->brand) ? $row->brand : "---";
                            $rowPlateNo = (isset($row->plateno) && $row->plateno) ? $row->plateno : "---";
                            $rowChasisNo = (isset($row->chasisno) && $row->chasisno) ? $row->chasisno : "---";
                            $rowEngineNo = (isset($row->engineno) && $row->engineno) ? $row->engineno : "---";

                            $ndata[$index]["name"] = $rowAssetName;
                            $ndata[$index]["asset_code"] = (isset($ndata[$index]["asset_code"]) && $ndata[$index]["asset_code"]) ? $ndata[$index]["asset_code"] : $rowGenCode;
                            $ndata[$index]["is_component"] = (isset($row->isCompo) && $row->isCompo) ? true : false;

                            $this->db->select('a.*, b.id as component_id, b.remarks, b.isExcluded as exclude, a.description');
                            $this->db->from('gccasset.vehicles a');
                            $this->db->join('gccasset.vehicles_components b', 'b.asset_id = a.id', 'left');
                            $this->db->where('b.parent_id', $row->id);
                            $query3 = $this->db->get();

                            if ($query3->num_rows() > 0) {
                                foreach ($query3->result_array() as $rr) {
                                    $rr["name"] = (isset($rr["name"]) && $rr["name"]) ? $rr["name"] : "No asset name";
                                    $rr["assetacode"] = (isset($rr["gen_code"]) && $rr["gen_code"]) ? $rr["gen_code"] : "No Asset Code";
                                    $rr["assetname"] = (isset($rr["description"]) && $rr["description"]) ? $rr["description"] : "No Asset Description";
                                    $rr["purchaseprice"] = number_format($rr["purchaseprice"], 2, ".", "");
                                    $rr["quantity"] = 1;
                                    $ndata[$index]['components'][] = $rr;
                                }
                            }
                        }
                    }
                }
            }
            if ($ndata) {
                $arrData = (object)$ndata;
            }
        }
        return $arrData;
    }

    function getIssuedTo($id) {
        // $query = $this->db->query("SELECT * FROM gccmaster.tblemployees where id='$id'");
        $this->db->select('firstname, lastname');
        $this->db->where('id', $id);
        $this->db->from('gccmaster.tblemployees');
        $query = $this->db->get();
        return $query->row() ? $query->row() : 'No Display Name';
    }

    public function getContents($id) {
        $this->db->from('gcceforms.accountability_body_temp');
        // $this->db->where('user_id = "' . $id . '"');
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        return $query->result();
    }

    function getCompany($id) {
        $this->db->select("id, description");
        $this->db->from("gcchris.tblcompanies");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return isset($data->description) && $data->description ? $data->description : "No Company name";
    }

    function getPosition($id) {
        $this->db->select("id, name");
        $this->db->from("gcchris.tblposition");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return isset($data->name) && $data->name ? $data->name : "No Position name";
    }

    function getDepartment($id) {
        $this->db->select("id, description");
        $this->db->from("gcchris.tbldepartments");
        $this->db->where("id", $id);
        $query = $this->db->get();
        $data = $query->row();
        return isset($data->description) && $data->description ? $data->description : 'No Department name';
    }

    function contentDetail($id) {
        $dataArr = array();
        $totalAmount = array();
        $this->db->select("a.*, IFNULL(d.description, b.company_id) as temp_company, IFNULL(e.description, b.department_id) as temp_department, b.firstname, b.lastname, b.middlename, b.suffix, c.contractor, CONCAT(LEFT(b.firstname, 1), '.', LEFT(b.middlename,1), '.', LEFT(b.lastname, 1),'.') as initials");
        $this->db->from('gcceforms.accountability a');
        $this->db->join('gccmaster.tblemployees b', 'b.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcontractor c', 'c.id = a.issued_to', 'left');
        $this->db->join('gcchris.tblcompanies d', 'd.id = b.company_id', 'left');
        $this->db->join('gcchris.tbldepartments e', 'e.id = b.department_id', 'left');
        $this->db->where('a.id', $id);
        $query = $this->db->get()->row();
        $tempRs = (array)$query;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $query->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        $query->returned_by_detail = $this->returnedByDetails($query->returned_by, $query->is_contract);
        $query->received_by = $this->receivedBy();
        $query->company = is_numeric($query->company) ? $this->getCompany($query->company) : $query->company;
        $query->department = is_numeric($query->department) ? $this->getDepartment($query->department) : $query->department;
        // if (is_numeric($query->company)) {
        //     $query->company = $this->getCompany($query->company);
        // } else {
        //     $query->company = $query->company;
        // }

        // if (is_numeric($query->department)) {
        //     $query->department = $this->getDepartment($query->department);
        // } else {
        //     $query->department = $query->department;
        // }

        $marked_returned = $this->marked_return_by($id);
        if ($marked_returned) {
            $query->marked_returned_by = ($marked_returned->marked_returned_by) ? $marked_returned->marked_returned_by : "";
            $query->marked_returned_dt = ($marked_returned->marked_returned_dt) ? $marked_returned->marked_returned_dt : "";
        }
        $dataArr[] = $query;
        $dataArr['data_body'] = $this->contentBodyDetail($id);

        $total_amount = 0;
        foreach($this->contentBodyDetail($id)['data'] as $amount){
            $total_amount += $amount->amount;
        }
            
        $totalAmount['amount'] = number_format($total_amount,2);
        $dataArr['data_body_total_amount'] = $totalAmount;
        return $dataArr;
    }

    function marked_return_by($id) {
        $this->db->select("marked_returned_by, marked_returned_dt");
        $this->db->from('gcceforms.accountability_body');
        $this->db->where('accountability_id', $id);
        $this->db->where('marked_returned_by !=', "");
        $query = $this->db->get()->row();
        if ($query) {
            return $query;
        } else {
            return null;
        }
    }

    function receivedBy() {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        return $name;
    }

    function returnedByDetails($id, $contract) {
        if ($id) {
            if ($contract == 1) {
                $this->db->select("id, contractor");
                $this->db->from("gcchris.tblcontractor");
                $this->db->where("id", $id);
                $query = $this->db->get()->row();
                return $query->contractor;
            } else {
                $this->db->select("firstname, lastname, middlename, suffix");
                $this->db->from("gccmaster.tblemployees");
                $this->db->where("id", $id);
                $query = $this->db->get()->row();
                $tempRs = (array)$query;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $query->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                return $query->display_name;
            }
        } else {
            return "";
        }
    }

    function contentBodyDetail($code) {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_body_detail_post($code, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->get_body_detail_count($code);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }
    // connected to view accountability
    private function get_body_detail_post($id, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
        $this->db->select("a.asset_code, a.description, a.remarks, a.amount, a.*");
        $this->db->from('gcceforms.accountability_body a');
        $this->db->where('a.accountability_id', $id);
        $i = $sortOrder[0]['column'];
        
        if(isset($sortBy[$i]) && $sortBy[$i]){
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }

        $query = $this->db->get();
        $asset_id = $query->row_array();

        $this->db->select("status");
        $this->db->from("gcceforms.accountability");
        $this->db->where("id",$id);
        $status_query = $this->db->get();
        $status = $status_query->row_array();
        //$status['status']

        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {

                //if item is Asset
                if($rs->type=="Asset"){
                    $this->db->select("purchaseprice, beg_addcost, total_cost");
                    $this->db->from("gccasset.assets");
                    $this->db->where("assetacode",$rs->asset_code);
                    $addcost_query = $this->db->get();
                    $additional_cost = $addcost_query->row_array();
                    $rs->description = $this->assetNames($rs->asset_id, $rs->type);
                    $rs->description = strtoupper($rs->description);
                        if($status['status'] == "Pending Accounting Notes"){
                            if($rs->amount != ($additional_cost['purchaseprice'] + $additional_cost['beg_addcost'])){
                                $rs->amount = $additional_cost['purchaseprice'] + $additional_cost['beg_addcost'];
                            }else{
                                $rs->amount = $rs->cost;
                            }
                        }else{
                            $rs->amount = $rs->amount;
                        }
                        $rs->cost = $rs->cost;
                        $rs->desc = $this->assetDesc($rs->asset_id, $rs->type);
                        $arrData[$key] = $rs;
                }else{
                    //if item is Vehicles
                    $this->db->select("purchaseprice, beg_addcost, total_cost");
                    $this->db->from("gccasset.vehicles");
                    $this->db->where("gen_code",$rs->asset_code);
                    $addcost_query = $this->db->get();
                    $additional_cost = $addcost_query->row_array();
                    $rs->description = $this->assetNames($rs->asset_id, $rs->type);
                    $rs->description = strtoupper($rs->description);
                    if($status['status'] == "Pending Accounting Notes"){
                        if($rs->amount != $additional_cost['total_cost']){
                            $rs->amount = $additional_cost['total_cost'];
                        }else{
                            $rs->amount = $rs->cost;
                        }
                    }else{
                        $rs->amount = $rs->amount;
                    }
                    $rs->cost = $rs->cost;
                    $rs->desc = $this->assetDesc($rs->asset_id, $rs->type);
                    $rs->desc = strtoupper($rs->desc);
                    $arrData[$key] = $rs;    
                }
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
    // counting on view accountability
    private function get_body_detail_count($id) {
        $this->db->select("a.*");
        $this->db->from('gcceforms.accountability_body a');
        $this->db->where('a.accountability_id', $id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function assetDesc($code, $type) {
        if ($type == "Asset") {
            $this->db->select("assetname");
            $this->db->from("gccasset.assets");
            $this->db->where("id", $code);
            $query = $this->db->get();
            $data = $query->row();
            return isset($data->assetname) && $data->assetname? $data->assetname: "";
        } else if($type == "Vehicle"){
            $this->db->select("description");
            $this->db->from("gccasset.vehicles");
            $this->db->where("id", $code);
            $query = $this->db->get();
            $data = $query->row();
            return isset($data->description) && $data->description? $data->description: "";
        }
    }

    function assetNames($code, $type) { 
        if ($type == "Asset") {
            $this->db->select("name");
            $this->db->from("gccasset.assets");
            $this->db->where("id", $code);
            $query = $this->db->get();
            $data = $query->row();
            return (isset($data->name) && $data->name)? $data->name: "";
        } else if ($type == "Vehicle"){
            $this->db->select("name");
            $this->db->from("gccasset.vehicles");
            $this->db->where("id", $code);
            $query = $this->db->get();
            $data = $query->row();
            return (isset($data->name) && $data->name)? $data->name: "";
        }
    }

    function setAcctNote($get) {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $session_data = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";

        $is_contractor = $this->contentDetail($get);

        $status = $is_contractor[0]->is_contract == 0 ? 'Pending Payroll Notes' : 'For Releasing';

        $date = date('Y-m-d H:i:s');
        $this->input->post();
        $data = array(
            'status' => $status,
            'acctg_noted_by' => $session_data,
            'acctg_noted_dt' => $date,
            'acctg_noted_remarks' => $this->input->post('acctg_note'),
        );
        if ($get) {
            $this->db->where('gcceforms.accountability.id', $get);
            $query = $this->db->update('gcceforms.accountability', $data);

            $data = $this->contentDetail($get);
            $companyTo = $data[0]->company;
            $sendType = "hr_note";

            $update = $this->update_assetPrice($get);
            if($query){
                $message = "View Accountability - Set Accounting Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }else{ 
                $message = "View Accountability - Failed set Accounting Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }
            $this->core_layout->setEventLog($message,"update", $type, "gcceforms", $table);
            return $query;
        }
    }

    function undoAcctgNote($get) {
        $session_data = $this->user_data;
        $date = date('Y-m-d H:i:s');
        $this->input->post();
        $data = array(
            'status' => 'Pending Accounting Notes',
            'acctg_noted_by' => '',
            'acctg_noted_dt' => '',
            'acctg_noted_remarks' => '',
        );
        if ($get) {
            $this->db->where('gcceforms.accountability.id', $get);
            $query = $this->db->update('gcceforms.accountability', $data);
            if($query){
                $message = "View Accountability - Undo Accounting Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }else{ 
                $message = "View Accountability - Failed set Accounting Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }
            $this->core_layout->setEventLog($message,"update", $type, "gcceforms", $table);
            return $query;
        }
    }

    function setHrNote($get) {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $session_data = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        $date = date('Y-m-d H:i:s');
        $this->input->post();
        $data = array(
            'status' => 'For Releasing',
            'hr_noted_by' => $session_data,
            'hr_noted_dt' => $date,
            'hr_noted_remarks' => $this->input->post('hr_note'),
        );
        if ($get) {
            $this->db->where('gcceforms.accountability.id', $get);
            $query = $this->db->update('gcceforms.accountability', $data);
            if($query){
                $message = "View Accountability - Set Payroll Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }else{ 
                $message = "View Accountability - Failed Set Payroll Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }
            $this->core_layout->setEventLog($message,"update", $type, "gcceforms", $table);
            return $query;
        }
    }

    function undoHrNote($get) {
        $session_data = $this->user_data;
        $date = date('Y-m-d H:i:s');
        $this->input->post();
        $data = array(
            'status' => 'Pending Payroll Notes',
            'hr_noted_by' => '',
            'hr_noted_dt' => '',
            'hr_noted_remarks' => '',
        );
        if ($get) {
            $this->db->where('gcceforms.accountability.id', $get);
            $query = $this->db->update('gcceforms.accountability', $data);
            if($query){
                $message = "View Accountability - Undo Payroll Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }else{ 
                $message = "View Accountability - Failed Undo Payroll Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }
            $this->core_layout->setEventLog($message,"update", $type, "gcceforms", $table);
            return $query;
        }
    }

    function setReleaseNote($get) {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $session_data = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        $date = date('Y-m-d H:i:s');
        $this->input->post();
        $data = array(
            'status' => 'Released',
            'released_by' => $session_data,
            'released_dt' => $date,
            'release_remarks' => $this->input->post('release_note'),
        );
        if ($get) {
            $this->db->where('gcceforms.accountability.id', $get);
            $query = $this->db->update('gcceforms.accountability', $data);
            if($query){
                $message = "View Accountability - Set Release Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }else{ 
                $message = "View Accountability - Failed Set Release Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }
            $this->core_layout->setEventLog($message,"update", $type, "gcceforms", $table);
            return $query;
        }
    }

    function undoReleaseNote($get) {
        $session_data = $this->user_data;
        $date = date('Y-m-d H:i:s');
        $this->input->post();
        $data = array(
            'status' => 'For Releasing',
            'released_by' => '',
            'released_dt' => '',
        );
        if ($get) {
            $this->db->where('gcceforms.accountability.id', $get);
            $query = $this->db->update('gcceforms.accountability', $data);
            if($query){
                $message = "View Accountability - Undo Release Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }else{ 
                $message = "View Accountability - Failed Undo Release Note of ".$this->getAccountabilityCode($get).".";
                $type = "success";
                $table = "user";
            }
            $this->core_layout->setEventLog($message,"update", $type, "gcceforms", $table);
            return $query;
        }
    }

    function cancelAccountability($id) {
        $tempRs = (array)$this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $session_data = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        $date = date('Y-m-d H:i:s');
        $this->input->post();
        $data = array(
            'status' => 'Cancelled',
            'cancelled_by' => $session_data,
            'cancelled_dt' => $date,
            'cancelled_remarks' => $this->input->post('cancelled_remarks'),
        );
        if ($id) {
            $this->db->where('gcceforms.accountability.id', $id);
            $update = $this->db->update('gcceforms.accountability', $data);

            if ($update) {
                $this->db->update('gcceforms.accountability_body', array('is_returned'=>1), array("accountability_id"=>$id));
                $ac_body = $this->db->get_where("gcceforms.accountability_body", array("accountability_id" => $id));
                if ($ac_body->num_rows() > 0) {
                    foreach ($ac_body->result_array() as $rs) {
                        $assetCode = $rs["asset_code"];
                        $type = $rs["type"];
                        if ($type == "Asset") {
                            $assets = $this->db->get_where("gccasset.assets", array("assetacode" => $assetCode));
                            if ($assets->num_rows() == 1) {
                                $_row = $assets->row();
                                $where = array();
                                $where["id"] = $_row->id;

                                $data = array();
                                $data["is_borrowed"] = "0";
                                $assetUpdate = $this->db->update("gccasset.assets", $data, $where);
                                if ($assetUpdate) {
                                    $assetComponent = $this->db->get_where("gccasset.assets", array("mother_asset" => $_row->id, "isComponent" => 1, "is_borrowed" => 1));
                                    if ($assetComponent->num_rows() > 0) {
                                        foreach ($assetComponent->result() as $rr) {
                                            $_where = array();
                                            $_where = $rr->id;

                                            $_data = array();
                                            $_data["is_borrowed"] = "0";

                                            $this->db->update("gccasset.assets", $_data, $_where);
                                            
                                        }
                                    }
                                }
                            }
                        }

                        if ($type == "Vehicle") {
                            $assets = $this->db->get_where("gccasset.vehicles", array("gen_code" => $assetCode));
                            if ($assets->num_rows() == 1) {
                                $_row = $assets->row();
                                $where = array();
                                $where["id"] = $_row->id;

                                $data = array();
                                $data["is_borrowed"] = "0";
                                $assetUpdate = $this->db->update("gccasset.vehicles", $data, $where);
                                if ($assetUpdate) {
                                    $assetComponent = $this->db->get_where("gccasset.vehicles", array("motherID" => $_row->id, "isCompo" => 1, "is_borrowed" => 1));
                                    if ($assetComponent->num_rows() > 0) {
                                        foreach ($assetComponent->result() as $rr) {
                                            $_where = array();
                                            $_where = $rr->id;

                                            $_data = array();
                                            $_data["is_borrowed"] = "0";

                                            $this->db->update("gccasset.vehicles", $_data, $_where);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $this->core_layout->setEventLog("View Accountability - Cancel accountability ".$this->getAccountabilityCode($id).".","update", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("View Accountability - Failed cancel accountability ".$this->getAccountabilityCode($id).".","update", "error", "gcceforms", "system");
            }
            return $update;
        }
    }

    function releasedAssets($returned, $table1) {
        $resultset = array();
        $post = $this->input->post();
        if ($table1) {
            $order_val = array(array("column" => "2", "dir" => "desc"));
        } else {
            $order_val = array(array("column" => "0", "dir" => "desc"));

        }
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
        $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql']) ? $post["query_builder"]['sql'] : array();

        $privilege = $this->core_layout->getCurrentActions();

        $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
        $companyDescription = null;

        if ($view_by_company) {
            $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
        }

        // $rowCount = 0;
        // $rowData = array();
        // if (!$search) {
        //     $rowData = $this->get_all_assets($query_builder, $returned, $table1, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_all_assets_count($query_builder, $returned, $table1);
        // }

        // if ($search) {
        //     $rowData = $this->get_searched_assets($query_builder, $returned, $table1, $search, $limit, $offset, $sortBy, $sortOrder);
        //     $rowCount = $this->get_searched_assets_count($query_builder, $returned, $table1, $search);
        //     if($returned == 0){
        //         $table_searched = "Returned Assets";
        //     }else{
        //         $table_searched = "Released Assets";
        //     }
        //     $this->core_layout->setEventLog($table_searched." Masterfile - Search ".$search." in datatable.", "search", "success", "gcceforms", "user");
        // }

        // $totalNotFiltered = $rowCount;
        $rowData = $this->get_all_assets($query_builder, $returned, $table1, $search, $limit, $offset, $sortBy, $sortOrder, $view_by_company, $companyDescription);
        $rowCount = $this->get_all_assets_count($query_builder, $returned, $table1, $search, $view_by_company, $companyDescription);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_all_assets($query_builder = null, $returned, $table1, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $view_by_company = false, $companyDescription = null) {
        $date = date("Y-m-d", strtotime("-1 year"));
        $data = array();

        $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "d.contractor", "c.lastname", "asset.name", "vehicle.name", "a.company", "c.firstname", "d.company", "e.description");

        $sqlSelect = "a.id as acc_id, a.reference_no, a.is_contract, a.issued_to, b.asset_code, b.asset_id, b.amount, b.type, c.firstname, c.middlename, c.lastname, c.suffix, d.contractor, 
            IF(a.is_contract = '1', 
                IFNULL(d.company, IFNULL(a.company, e.description)), 
                IFNULL(a.company, e.description)) as company, a.status";

        $this->db->select($sqlSelect);
        $this->db->from('gcceforms.accountability a');
        $this->db->join('gcceforms.accountability_body b', 'a.id = b.accountability_id');
        $this->db->join('gccmaster.tblemployees c', 'a.issued_to = c.id', 'left');
        $this->db->join('gcchris.tblcontractor d', 'a.issued_to = d.id', 'left');
        $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');

        if(isset($search) && $search){
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        }

        if ($view_by_company) {
            $this->db->where('a.company', $this->user_data['company']);

            if ($companyDescription) {
                $this->db->or_where('a.company', $companyDescription);
            }
        }

        if($returned == 0){
            $this->db->group_start();
                $this->db->where("b.is_returned", 0);
                $this->db->or_where("b.is_returned", 2);
            $this->db->group_end();
        }else{
            $this->db->where("b.is_returned", $returned);
        }

        $this->db->where('a.status', 'Released');

        if ($query_builder) {
            $this->db->where($query_builder);
        }

        if(isset($search) && $search){
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

        if ($sortBy[$i]['data'] == "firstname") {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $this->db->order_by("d.contractor", $sortOrder[0]['dir']);
        } else {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = array();

            foreach($query->result() as $key => $rs){
                $rs->company = (is_numeric($rs->company)) ? $this->getCompany($rs->company) : $rs->company;
                $rs->asset_name = $this->getAssetVehiclename($rs->asset_id, $rs->type);

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                // $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $tempname = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->display_name = $rs->is_contract ? $rs->contractor : $tempname;

                $arrData[$key] = $rs;
            }

            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    function get_all_assets_count($query_builder = null, $returned, $table1, $search = null, $view_by_company = false, $companyDescription = null) {
        $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "d.contractor", "c.lastname", "asset.name", "vehicle.name", "a.company", "c.firstname", "d.company", "e.description");

        $sqlSelect = "a.id as acc_id, a.reference_no, a.is_contract, a.issued_to, b.asset_code, b.asset_id, b.amount, b.type, c.firstname, c.middlename, c.lastname, c.suffix, d.contractor, 
            IF(a.is_contract = '1', 
                IFNULL(d.company, IFNULL(a.company, e.description)), 
                IFNULL(a.company, e.description)) as company";

        $this->db->select($sqlSelect);
        $this->db->from('gcceforms.accountability a');
        $this->db->join('gcceforms.accountability_body b', 'a.id = b.accountability_id');
        $this->db->join('gccmaster.tblemployees c', 'a.issued_to = c.id', 'left');
        $this->db->join('gcchris.tblcontractor d', 'a.issued_to = d.id', 'left');
        $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');

        if ($view_by_company) {
            $this->db->where('a.company', $this->user_data['company']);

            if ($companyDescription) {
                $this->db->or_where('a.company', $companyDescription);
            }
        }

        if(isset($search) && $search){
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        }

        if($returned == 0){
            $this->db->group_start();
                $this->db->where("b.is_returned", 0);
                $this->db->or_where("b.is_returned", 2);
            $this->db->group_end();
        }else{
            $this->db->where("b.is_returned", $returned);
        }

        $this->db->where('a.status', 'Released');

        if ($query_builder) {
            $this->db->where($query_builder);
        }

        if(isset($search) && $search){
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
        $rowCount = $query->num_rows();

        return $rowCount;
    }

    private function get_all_assetsv1($query_builder = null, $returned, $table1, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
        $date = date("Y-m-d", strtotime("-1 year"));
        $sqlSelect = "a.id as acc_id, a.reference_no, a.is_contract, a.date_issued, a.issued_to, a.acctg_noted_remarks, b.*, 
            asset.name as asset_name, vehicle.name as vehicle_name, c.firstname, c.middlename, c.lastname, c.suffix, d.contractor, 
            IF(a.is_contract = '1', 
                IFNULL(d.company, IFNULL(a.company, e.description)), 
                IFNULL(a.company, e.description)) as company";

        $this->db->select($sqlSelect);
        $this->db->from('gcceforms.accountability a');
        $this->db->join('gcceforms.accountability_body b', 'a.id = b.accountability_id');
        $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
        $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        $this->db->join('gccmaster.tblemployees c', 'a.issued_to = c.id', 'left');
        $this->db->join('gcchris.tblcontractor d', 'a.issued_to = d.id', 'left');
        $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');
        
        $where = array();
        $where['a.status'] = 'Released';

        $this->db->where($where);
        if($returned == 0){
          $this->db->group_start();
          $this->db->where("b.is_returned", 0);
          $this->db->or_where("b.is_returned", 2);
          $this->db->group_end();
        }else{
          $this->db->where("b.is_returned", $returned);
        }

        if ($query_builder) { $this->db->where($query_builder); }

        if ($limit != -1) { $this->db->limit($limit, $offset); }
        $i = $sortOrder[0]['column'];

        if ($sortBy[$i]['data'] == "firstname") {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $this->db->order_by("d.contractor", $sortOrder[0]['dir']);
        } else {
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                if (is_numeric($rs->company)) {
                    $rs->company = $this->getCompany($rs->company);

                } else {
                    $rs->company = $rs->company;
                }

                $tempRs = (array)$rs;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                $rs->amount = number_format($rs->amount, 2);//"₱ ".
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

    private function get_all_assets_countv1($query_builder = null, $returned, $table1) {
        $date = date("Y-m-d", strtotime("-1 year"));
        $this->db->from('gcceforms.accountability a');
        $this->db->join('gcceforms.accountability_body b', 'a.id = b.accountability_id');
        $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
        $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
        $this->db->join('gccmaster.tblemployees c', 'a.issued_to = c.id', 'left');
        $this->db->join('gcchris.tblcontractor d', 'a.issued_to = d.id', 'left');
        $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');
        $returned = $returned == 2 || $returned == 0 ? 0 : $returned;
        $where = array();
        $where['a.status'] = 'Released';
        $this->db->where($where);
        if($returned == 0){
          $this->db->group_start();
          $this->db->where("b.is_returned", 0);
          $this->db->or_where("b.is_returned", 2);
          $this->db->group_end();
        }else{
          $this->db->where("b.is_returned", $returned);
        }
        if ($query_builder) {
            $this->db->where($query_builder);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    private function get_searched_assetsv1($query_builder = null, $returned, $table1, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
        $date = date("Y-m-d", strtotime("-1 year"));
        if ($search) {
            $sqlSelect = "a.id as acc_id, a.reference_no, a.is_contract, a.date_issued, a.issued_to, a.acctg_noted_remarks, b.*, 
            asset.name as asset_name, vehicle.name as vehicle_name, c.firstname, c.middlename, c.lastname, c.suffix, d.contractor, 
            IF(a.is_contract = '1', 
                IFNULL(d.company, IFNULL(a.company, e.description)), 
                IFNULL(a.company, e.description)) as company";

            $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "d.contractor", "c.lastname", 
                "asset.name", "vehicle.name", "a.company", "c.firstname", "d.company", "e.description");

            $this->db->select($sqlSelect);
            $this->db->from('gcceforms.accountability a');
            $this->db->join('gcceforms.accountability_body b', 'a.id = b.accountability_id');
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
            $this->db->join('gccmaster.tblemployees c', 'a.issued_to = c.id', 'left');
            $this->db->join('gcchris.tblcontractor d', 'a.issued_to = d.id', 'left');
            $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');
            // $returned = $returned == 2 || $returned == 0 ? 0 : $returned;
            $where = array();
            $where['a.status'] = 'Released';
            if($returned == 0){
              $this->db->group_start();
              $this->db->where("b.is_returned", 0);
              $this->db->or_where("b.is_returned", 2);
              $this->db->group_end();
            }else{
              $this->db->where("b.is_returned", $returned);
            }

            $this->db->where($where);
            if ($query_builder) {
                $this->db->where($query_builder);
            }
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            $i = $sortOrder[0]['column'];
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("d.contractor", $sortOrder[0]['dir']);
            } else {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {

                    if (is_numeric($rs->company)) {
                        $rs->company = $this->getCompany($rs->company);

                    } else {
                        $rs->company = $rs->company;
                    }
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
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
        } else {
            return array();
        }
    }

    private function get_searched_assets_countv1($query_builder = null, $returned, $table1, $search = null) {
        $date = date("Y-m-d", strtotime("-1 year"));
        $rowCount = 0;
        if ($search) {
            $sqlSelect = "a.id as acc_id, a.reference_no, a.is_contract, a.date_issued, a.issued_to, a.acctg_noted_remarks, b.*, 
                asset.name as asset_name, vehicle.name as vehicle_name, c.firstname, c.middlename, c.lastname, c.suffix, d.contractor, 
                IF(a.is_contract = '1', 
                    IFNULL(d.company, IFNULL(a.company, e.description)), 
                    IFNULL(a.company, e.description)) as company";
                    
            $filterFields = array("a.status", "a.reference_no", "b.asset_code", "b.description", "d.contractor", "c.lastname", 
                "asset.name", "vehicle.name", "a.company", "c.firstname", "d.company", "e.description");

            $this->db->select($sqlSelect);
            $this->db->from('gcceforms.accountability a');
            $this->db->join('gcceforms.accountability_body b', 'a.id = b.accountability_id');
            $this->db->join('gccasset.assets asset', 'asset.id = b.asset_id AND b.type = "Asset"', 'left');
            $this->db->join('gccasset.vehicles vehicle', 'vehicle.id = b.asset_id AND b.type = "Vehicle"', 'left');
            $this->db->join('gccmaster.tblemployees c', 'a.issued_to = c.id', 'left');
            $this->db->join('gcchris.tblcontractor d', 'a.issued_to = d.id', 'left');
            $this->db->join('gcchris.tblcompanies e', 'e.id = a.company', 'left');
            $returned = $returned == 2 || $returned == 0 ? 0 : $returned;
            $where = array();
            $where['a.status'] = 'Released';
            if($returned == 0){
              $this->db->group_start();
              $this->db->where("b.is_returned", 0);
              $this->db->or_where("b.is_returned", 2);
              $this->db->group_end();
            }else{
              $this->db->where("b.is_returned", $returned);
            }
            $this->db->where($where);
            if ($query_builder) {
                $this->db->where($query_builder);
            }

            $this->db->group_start();
            foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search, "both");
                } else {
                    $this->db->or_like($field, $search, "both");
                }
            }
            $this->db->group_end();
            $query = $this->db->get();
            $rowCount = $query->num_rows();
        }
        return $rowCount;
    }

    function editUnreturnedRemarks($id) {
        // $query = $this->db->query("SELECT * FROM gcceforms.accountability_body WHERE id = '$id'");
        $this->db->where('id', $id);
        $this->db->from('gcceforms.accountability_body');
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->row() : array();
    }

    function unreturnedRemarks($get) {
        $post = $this->input->post();
        if(isset($post['returned'])){
            $data = array(
                'remarks_returned' => $this->input->post('unreturned_remarks'),
            );
        }else{
            $data = array(
                'remarks' => $this->input->post('unreturned_remarks'),
            );
        }

        if ($get) {
            $this->db->where('gcceforms.accountability_body.id', $get);
            return $this->db->update('gcceforms.accountability_body', $data);
        }
    }

    function editReturnedRemarks($id) {
        // $query = $this->db->query("SELECT * FROM gcceforms.accountability_body WHERE id = '$id'");
        $this->db->where('id', $id);
        $this->db->from('gcceforms.accountability_body');
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->row() : array();
    }

    function returnedRemarks($get) {
        $post = $this->input->post();
        if(isset($post['clear_returned'])){
            $data = array(
                'remarks_returned' => $this->input->post('returned_remarks'),
            );
        }else{
            $data = array(
                'remarks' => $this->input->post('returned_remarks'),
            );
        }
        if ($get) {
            $this->db->where('gcceforms.accountability_body.id', $get);
            return $this->db->update('gcceforms.accountability_body', $data);
        }
    }
    
    function returnAccountability($id) {
        $tempRs = (array) $this->user_data;
        $fullname = $this->core_layout->getDisplayName($tempRs);
        $tempFullname = (object)$fullname;
        $session_data = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        $date = date('Y-m-d H:i:s');
        $datas = array(
            'remarks' => $this->input->post('remarks'),
        );

        $xContentData = $this->contentDetail($id);
        $tempData = array();
        $tempData["accountability_id"] = $id;
        $tempData["created_by"] = $this->core_layout->getCurrentEmployeeId();

        $updatedAcct = $this->db->update('gcceforms.accountability', $datas, array('id' => $id));
        if($updatedAcct && $this->db->affected_rows() == 1){
            $tempData["message"] = "Accountability data has been updated, with reference # {$xContentData[0]->reference_no}";
            $tempData["acct_type"] = 4;
            $this->db->insert($this->acctLogsTable, $tempData);
        }

        $data = array(
            'is_returned' => '1',
            'date_returned' => $date,
            //'remarks_returned' => $this->input->post('remarks'),
            'marked_returned_by' => $session_data,
            'marked_returned_dt' => $date,
        );
        $asset_id = array();
        $asset_id = $this->input->post('id');
        for ($i = 0; $i < count($asset_id); $i++) {
            if ($asset_id[$i]) {
                $returnCount = $this->return_accountability(array('id' => $asset_id[$i]), $data);
                if($returnCount > 0){
                    $arr = $this->return_item($asset_id[$i]);
                    $tempWhere = array();
                    $tempWhere["id"] = $arr->asset_id;

                    $data2 = array('is_borrowed' => 0);
                    if ($arr->type == "Asset") {
                        $tempCountAsset = $this->update_asset($tempWhere, $data2);
                        if($tempCountAsset == 0){
                            $this->update_asset(array('assetacode' => $arr->asset_code), $data2);
                        }
                    } else if ($arr->type == "Vehicle") {
                        $tempCountVehicle = $this->update_vehicle($tempWhere, $data2);
                        if($tempCountVehicle == 0){
                            $this->update_vehicle(array('gencode' => $arr->asset_code), $data2);
                        }
                    }
                    $tempData["message"] = "{$arr->type} accountability has been returned, asset code {$arr->asset_code} with reference # {$xContentData[0]->reference_no}";
                    $tempData["acct_type"] = 5;
                    $this->db->insert($this->acctLogsTable, $tempData);
                }
            }
        }
        $data = $this->contentDetail($id);
        $companyTo = $data[0]->company;

        $sendType = "return";
        $this->email_send($employeeName = "", $referenceNumber = "", $isUrgent = "", $sendType, $companyTo, $data);
        return true;
    }

    function undoReturnAccountability() {
        $session_data = $this->user_data;
        date_default_timezone_set('Asia/Singapore');
        $date = date('Y-m-d H:i:s');
        $data = array(
            'is_returned' => '0',
            'date_returned' => "",
            'remarks_returned' => "",
            'marked_returned_by' => "",
            'marked_returned_dt' => "",
        );
        
        $asset_id = array();
        $asset_id = $this->input->post('id');
        for ($i = 0; $i < count($asset_id); $i++) {
            if ($asset_id[$i]) {
                $qBody = $this->db->get_where('gcceforms.accountability_body', array("id"=>$asset_id[$i]));
                if($qBody->num_rows() == 1){
                    $qRow = $qBody->row();
                    $xContentData = $this->contentDetail($qRow->accountability_id);
                    $tempData = array();
                    $tempData["accountability_id"] = $qRow->accountability_id;
                    $tempData["created_by"] = $this->core_layout->getCurrentEmployeeId();
                    
                    $returnCount = $this->return_accountability(array('id' => $asset_id[$i]), $data);
                    if($returnCount > 0){
                        $arr = $this->return_item($asset_id[$i]);
                        $tempWhere = array();
                        $tempWhere["id"] = $asset_id[$i];

                        $data2 = array('is_borrowed' => 1);
                        if ($arr->type == "Asset") {
                            $tempCountAsset = $this->update_asset($tempWhere, $data2);
                            if($tempCountAsset == 0){
                                $this->update_asset(array('assetacode' => $arr->asset_code), $data2);
                            }
                        } else if ($arr->type == "Vehicle") {
                            $tempCountVehicle = $this->update_vehicle($tempWhere, $data2);
                            if($tempCountVehicle == 0){
                                $this->update_vehicle(array('gencode' => $arr->asset_code), $data2);
                            }
                        }

                        $tempData["message"] = "{$arr->type} accountability has been undo returned, asset code {$arr->asset_code} with reference # {$xContentData[0]->reference_no}";
                        $tempData["acct_type"] = 6;
                        $this->db->insert($this->acctLogsTable, $tempData);
                    }
                }

            }
        }
        return true;
    }

    function return_accountability($where, $data) {
        $this->db->update('gcceforms.accountability_body', $data, $where);
        return $this->db->affected_rows();
    }

    function return_item($id) {
        $this->db->from('gcceforms.accountability_body');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->row() : array();
    }

    function update_asset($where, $data) {
        $this->db->update('gccasset.assets', $data, $where);
        return $this->db->affected_rows();
    }

    function update_vehicle($where, $data) {
        $this->db->update('gccasset.vehicles', $data, $where);
        return $this->db->affected_rows();
    }

    function changeReturnBy($get) {
        $this->input->post();
        $data = array(
            'returned_by' => $this->input->post('return_by'),
        );
        if ($get) {
            $this->db->where('gcceforms.accountability.id', $get);
            return $this->db->update('gcceforms.accountability', $data);
        }
    }

    function chartData() {
        $this->db->select("a.status, COUNT(a.id) AS count");
        $this->db->from("gcceforms.accountability a");
        $this->db->group_by("a.status");
        $query = $this->db->get()->result();
        $result = json_decode(json_encode($query));
        
        $count = 0;
        foreach ($result as $key) {
            if($key->status == 'Pending HR Notes' || $key->status == 'Pending Payroll Notes'){
                $count = $count + $key->count;
            }
        }
        $hr_notes = (object) ['status' => 'Pending HR Notes', 'count' => $count];

        $data = array($result[2], $hr_notes, $result[1], $result[0]);
        $results = json_decode(json_encode($data));
        return $results;
    }

    function getCompanyList() {
        $this->db->select("id, code");
        $query = $this->db->get("gcchris.tblcompanies");
        return $query->result();
    }

    // return accountability changes
    function ret_contentBodyDetail($code) {
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column" => "1", "dir" => "desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->ret_body_detail_post($code, $limit, $offset, $sortBy, $sortOrder);
        $rowCount = $this->ret_body_detail_count($code);

        $totalNotFiltered = $rowCount;

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    private function ret_body_detail_post($id, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
        $data = array();

        // $sql = "a.asset_code, a.description, a.remarks, a.amount, a.type, a.brand, a.modelno, a.series, a.is_returned, a.plateno, a.engineno, a.chasisno. a.description, b.created_dt";
        $this->db->select("a.asset_code, a.description, a.remarks, a.amount, a.*, b.created_dt");
        $this->db->from('gcceforms.accountability_body a');
        $this->db->join('gcceforms.accountability b', "a.accountability_id=b.id", "LEFT");
        $this->db->where('a.accountability_id', $id);
        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $arrData = array();
            foreach ($query->result() as $key => $rs) {
                $return_comp = false;
                if($rs->type == "Asset"){
                    $return_comp = $this->getComp($rs->asset_id);
                }else if($rs->type == "Vehicle"){
                    $return_comp = $this->getVehicleComp($rs->asset_id);
                }

                $price = 0;
                $rs->comp_description = "";
                if ($return_comp && count($return_comp) > 0) {
                    foreach ($return_comp as $data) {
                        $price += $data['purchaseprice'];
                    }
                    if($rs->type == "Asset"){
                        $rs->comp_description = $this->getCompdesc($return_comp);
                    }else if($rs->type == "Vehicle"){
                        $rs->comp_description = $this->getVehicleCompdesc($return_comp);
                    }
                }
                
                $tempAssetName = $this->assetNames($rs->asset_id, $rs->type);
                $tempAssetDesc = $this->assetDesc($rs->asset_id, $rs->type);
                $rs->description = ($tempAssetName)? $tempAssetName: $rs->description;
                $rs->amount = number_format($rs->amount + $price, 2);
                $rs->cost = number_format($rs->cost, 2);
                $rs->desc = ($tempAssetDesc)? $tempAssetDesc: "---";
                $arrData[$key] = $rs;
            }
            $data = array();
            foreach ($arrData as $k => $v) {
                $data[] = $v;
            }
        }

        return $data;
    }

    private function ret_body_detail_count($id) {
        $this->db->select("a.*");
        $this->db->from('gcceforms.accountability_body a');
        $this->db->where('a.accountability_id', $id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getComp($id, $rowName = null, $rowDescription = null) {
        $sql = "assetacode, assetname, purchaseprice";

        $this->db->select($sql);
        $this->db->from("gccasset.assets");
        $this->db->where("mother_asset", $id);
        $this->db->where("mother_asset !=", 0);
        $query = $this->db->get();
        $return_comp = $query->result_array();
        return $return_comp;
    }

    function getVehicleComp($id) {
        $sql = "gen_code, name, purchaseprice";
        $this->db->select($sql);
        $this->db->from("gccasset.vehicles");
        $this->db->where("motherID", $id);
        $this->db->where("motherID !=", 0);
        $query = $this->db->get();
        $return_comp = $query->result_array();
        return $return_comp;
    }

    function getVehicleCompdesc($return_comp) {
        if (!empty($return_comp)) {
            $description = "<div>";
            $description .= "<table class='table table-bordered component-table' width='100%' style='font-size:x-small;'>";
            $description .= "<col width='25%'>";
            $description .= "<thead>";
            $description .= "<tr>";
            $description .= "<th><b>Components</b></th>";
            $description .= "<th></th>";
            $description .= "<th></th>";
            $description .= "</tr>";
            $description .= "</thead>";
            $description .= "<tbody>";
            foreach ($return_comp as $data) {
                $description .= "<tr>";
                $description .= "<td>{$data["gen_code"]}</td>";
                $description .= "<td>{$data["name"]}</td>";
                $amount = number_format($data["purchaseprice"], 2);
                $description .= "<td>{$amount}</td>";
                $description .= "</tr>";
            }
            $description .= "</tbody>";
            $description .= "</table>";
            $description .= "</div>";
        }
        return $description;
    }

    function getCompdesc($return_comp) {
        if (!empty($return_comp)) {
            $description = "<div>";
            $description .= "<table class='table table-bordered component-table' width='100%' style='font-size:x-small;'>";
            $description .= "<col width='25%'>";
            $description .= "<thead>";
            $description .= "<tr>";
            $description .= "<th><b>Components</b></th>";
            $description .= "<th></th>";
            $description .= "<th></th>";
            $description .= "</tr>";
            $description .= "</thead>";
            $description .= "<tbody>";
            isset($return_comp['assetacode']) ? $return_comp['assetacode'] : "";
            isset($return_comp['assetname']) ? $return_comp['assetname'] : "";
            foreach ($return_comp as $data) {
                $description .= "<tr>";
                $description .= "<td>".$data['assetacode']."</td>";
                $description .= "<td>".$data['assetname']."</td>";
                $amount = number_format($data["purchaseprice"], 2);
                $description .= "<td>{$amount}</td>";
                $description .= "</tr>";
            }
            $description .= "</tbody>";
            $description .= "</table>";
            $description .= "</div>";
        }
        return $description;
    }

    public function getAccountabilityLogs($id=null){
        $resultset = array();

        if($id){
            $this->db->limit(5);
            $this->db->order_by("id", "DESC");
            $query = $this->db->get_where($this->acctLogsTable, array("accountability_id"=>$id));
            if($query->num_rows() > 0){
                $arrData = array();
                foreach ($query->result() as $key => $value) {
                    $tempState = "alert-info";
                    $tempLabel = "ADDED";
                    $tempShowAlert = false;
                    $currentImage = base_url("assets/images/profile/no_image.jpg");
                    $image = $currentImage;
                    $queryEmp = $this->db->get_where("gccmaster.tblemployees", array("id"=>$value->created_by));
                    if($queryEmp->num_rows() == 1){
                        $row = $queryEmp->row();
                        $imageFile = $row->pic_filename;
                        $imagePath = "uploads/files/images/employee_files/empcode_" . $row->id . "/" . $imageFile;
                        if (file_exists(realpath($imagePath))) {
                            $image = base_url($imagePath);
                        }
                    }
                    
                    $value->image = $image;
                    $value->created_at = date('Y-m-d H:i:s', strtotime($value->created_at));

                    $tempEmployeeData = $this->core_layout->getEmployeeData($value->created_by);
                    $createdBy = (object) $tempEmployeeData;
                    $tempName = (isset($createdBy->display_name_1) && $createdBy->display_name_1)? $createdBy->display_name_1: "No assigned name";
                    $tempLoggedTime = $this->core_layout->getTimeAgo($value->created_at);
                    $value->created_name = strtoupper($tempName);
                    $value->logged_at = strtoupper($tempLoggedTime);

                    switch($value->acct_type){
                        case 1: $tempState = "alert-info"; $tempLabel = "ADDED"; $tempShowAlert = true; break;
                        case 2: $tempState = "alert-success"; $tempLabel = "RETURNED"; $tempShowAlert = true; break;
                        case 3: $tempState = "alert-brand"; $tempLabel = "HR NOTES"; $tempShowAlert = true; break;
                        case 4: $tempState = "alert-info"; $tempLabel = "UPDATED"; break;
                        case 5: $tempState = "alert-success"; $tempLabel = "RETURNED ITEM"; break;
                        case 6: $tempState = "alert-warning"; $tempLabel = "UNDO RETURNED ITEM"; break;
                    }
                    $value->employee_data = $tempEmployeeData;
                    $value->temp_state = $tempState;
                    $value->temp_label = $tempLabel;
                    $value->temp_alert = $tempShowAlert;
                    $value->message = strtoupper($value->message);

                    $arrData[$key] = $value;
                }
                $resultset["response"] = true;
                $resultset["rows"] = $arrData;
                $resultset["count"] = $query->num_rows();
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }
        
        return $resultset;
    }

    function generateFormContentFix(){
        $resultset = array();
        $data = array();
        $post = $this->input->post();
        $tempStart = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
        $this->db->limit(50, $tempStart);
        $query = $this->db->get_where("gcceforms.accountability", array("status !="=>"Cancelled"));
        if($query->num_rows() > 0){
            foreach ($query->result() as $qk => $qv) {
                $queryBody = $this->db->get_where("gcceforms.accountability_body", array("accountability_id" => $qv->id));
                $tempAssetCount = 0;
                $tempMotherAsset = "NO ASSET CODE";
                $tempMotherAssetType = "ASSET NOT FOUND";
                if($queryBody->num_rows() > 0){
                    foreach ($queryBody->result() as $qbk => $qbv) {
                        $tempAssetId = $qbv->asset_id;
                        $tempAssetCode = $qbv->asset_code;
                        $tempAssetType = $qbv->type;
                        $xTempCode = explode("-", $tempAssetCode);
                        if(count($xTempCode) == 3){
                            $tempMotherAsset = $tempAssetCode;
                            $tempMotherAssetType = strtoupper($tempAssetType);
                            if(strtolower($tempAssetType) == "asset"){
                                $tempAssetCount += 1;
                                $this->db->from("gccasset.assets a");
                                $this->db->join("gccasset.asset_components b", "b.asset_id = a.id");
                                $this->db->where("a.mother_asset", $tempAssetId);
                                $this->db->where("a.isComponent", 1);
                                $this->db->where("b.isExcluded", 0);
                                $qAsset = $this->db->get();
                                $tempAssetCount += $qAsset->num_rows();
                            }
                            elseif(strtolower($tempAssetType) == "vehicle"){
                                $tempAssetCount += 1;
                                $this->db->from("gccasset.vehicles a");
                                $this->db->join("gccasset.vehicles_components b", "b.asset_id = a.id");
                                $this->db->where("a.motherID", $tempAssetId);
                                $this->db->where("a.isCompo", 1);
                                $this->db->where("b.isExcluded", 0);
                                $qAsset = $this->db->get();
                                $tempAssetCount += $qAsset->num_rows();
                            }
                        }
                    }
                }
                
                $tempRow["id"] = $qv->id;
                $tempRow["reference_no"] = $qv->reference_no;
                $tempRow["asset_code"] = $tempMotherAsset;
                $tempRow["asset_type"] = $tempMotherAssetType;
                $tempRow["asset_count"] = $tempAssetCount;
                $tempRow["accounted_count"] = $queryBody->num_rows();
                if($queryBody->num_rows() !== $tempAssetCount){ $data[] = $tempRow; }
            }
            if(count($data) > 0){
                $resultset["response"] = true;
                $resultset["data"] = $data;
                $resultset["count"] = count($data);
            }
        }else{
            $resultset["response"] = false;
        }
        
        return $resultset;
    }
    function updateFormContentFix($accountabilityId=null, $isReferenceNo=false){
        $resultset = array();
        if($isReferenceNo){
            $acctMother = $this->db->get_where("gcceforms.accountability", array("reference_no"=>$accountabilityId));
            if($acctMother->num_rows() == 1){
                $accountabilityId = $acctMother->row()->id;
            }
        }
        if($accountabilityId && is_numeric($accountabilityId)){
            $tempCurrentAssets = array();
            $tempAssets = array();
            $tempAssetIds = array();
            $tempCurrentAssetIds = array();
            $tempAssetCount = 0;
            $qTbody = $this->db->get_where("gcceforms.accountability_body", array("accountability_id"=>$accountabilityId));
            if($qTbody->num_rows() > 0){
                $tempData = array();
                foreach ($qTbody->result() as $qbk => $qbv) {
                    if($qbv->asset_id){
                        $tempCurrentAssetIds[] = $qbv->asset_id;
                    }
                    $tempAssetId = $qbv->asset_id;
                    $tempAssetCode = $qbv->asset_code;
                    $tempAssetType = $qbv->type;
                    $xTempCode = explode("-", $tempAssetCode);
                    if(count($xTempCode) == 3){
                        $tempMotherAsset = $tempAssetCode;
                        $tempMotherAssetType = strtoupper($tempAssetType);
                        if(strtolower($tempAssetType) == "asset"){
                            $tempAssetCount += 1;
                            $this->db->select("a.*");
                            $this->db->from("gccasset.assets a");
                            $this->db->join("gccasset.asset_components b", "b.asset_id = a.id");
                            $this->db->where("a.mother_asset", $tempAssetId);
                            $this->db->where("a.isComponent", 1);
                            $this->db->where("b.isExcluded", 0);
                            $qAsset = $this->db->get();
                            $tempAssetCount += $qAsset->num_rows();
                            if($qAsset->num_rows() > 0){
                                foreach ($qAsset->result() as $kkb => $vvb) {
                                    $vvb->acct_id = $qbv->accountability_id;
                                    $vvb->acct_type = $qbv->type;

                                    if(!in_array($vvb->id, $tempAssetIds)){
                                        $tempAssetIds[] = $vvb->id;
                                        $tempAssets[] = $vvb;
                                    }
                                }
                            }
                        }

                        elseif(strtolower($tempAssetType) == "vehicle"){
                            $tempAssetCount += 1;
                            $this->db->select("a.*");
                            $this->db->from("gccasset.vehicles a");
                            $this->db->join("gccasset.vehicles_components b", "b.asset_id = a.id");
                            $this->db->where("a.motherID", $tempAssetId);
                            $this->db->where("a.isCompo", 1);
                            $this->db->where("b.isExcluded", 0);
                            $qAsset = $this->db->get();
                            $tempAssetCount += $qAsset->num_rows();
                            if($qAsset->num_rows() > 0){
                                foreach ($qAsset->result() as $kkb => $vvb) {
                                    $vvb->acct_id = $qbv->accountability_id;
                                    $vvb->acct_type = $qbv->type;
                                    if(!in_array($vvb->id, $tempAssetIds)){
                                        $tempAssetIds[] = $vvb->id;
                                        $tempAssets[] = $vvb;
                                    }
                                }
                            }
                        }
                    }
                    $tempCurrentAssets[] = $qbv;
                }
                $tempDatax = array();
                $tempDatax["asset_components"] = array();
                $tempDatax["acct_body"] = $tempCurrentAssets;

                if($qTbody->num_rows() !== $tempAssetCount){ $tempDatax["asset_components"] = $tempAssets; }
                else{ $tempAssetCount = 0; }

                $resultset["response"] = true;
                $resultset["data"] = $tempDatax;
                $resultset["accounted_id"] = $tempCurrentAssetIds;
                $resultset["cc_count"] = $qTbody->num_rows();
                $resultset["acct_count"] = $tempAssetCount;
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }

        return $resultset;
    }
    function setFormContentData(){
        $post = $this->input->post();
        if(isset($post) && $post){
            if(isset($post["id"], $post["acct_id"], $post["acct_type"]) && $post["id"] && $post["acct_id"] && $post["acct_type"]){
                $tempWhere = array(
                    "asset_id"=>$post["id"],
                    "accountability_id"=>$post["acct_id"],
                    "type"=>$post["acct_type"],
                );

                $tempData = array();
                if(isset($post["acct_type"]) && strtolower($post["acct_type"]) == "asset"){
                    $this->db->select("id as asset_id, assetacode as asset_code, assetname as description, total_cost as cost, total_cost as amount, brand, modelno, serialno");
                    $qAsset = $this->db->get_where("gccasset.assets", array("id"=>$post["id"]));
                    if($qAsset->num_rows() == 1){
                        $tempQRow = $qAsset->row();
                        $tempQRow->quantity = 1;
                        $tempQRow->accountability_id = $post["acct_id"];
                        $tempQRow->type = $post["acct_type"];
                        $tempData = $tempQRow;
                    }
                }

                if(isset($post["acct_type"]) && strtolower($post["acct_type"]) == "vehicle"){
                    $this->db->select("id as asset_id, gen_code as asset_code, description, total_cost as cost, total_cost as amount, plateno, engineno, chasisno");
                    $qAsset = $this->db->get_where("gccasset.vehicles", array("id"=>$post["id"]));
                    if($qAsset->num_rows() == 1){
                        $tempQRow = $qAsset->row();
                        $tempQRow->quantity = 1;
                        $tempQRow->accountability_id = $post["acct_id"];
                        $tempQRow->type = $post["acct_type"];
                        $tempData = $tempQRow;
                    }
                }

                $tempQTBody = $this->db->get_where("gcceforms.accountability_body", $tempWhere);
                if($tempQTBody->num_rows() == 0){
                    $qTempAccountability = $this->db->insert("gcceforms.accountability_body", $tempData);
                    if($qTempAccountability){
                        $qData = $this->updateFormContentFix($post["acct_id"]);
                        unset($qData["response"]);
                        $resultset = $qData;
                        $resultset["response"] = true;

                    }else{
                        $resultset["response"] = false;
                    }
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }
        return $resultset;
    }

    function resendEmail($acctId=null){
        $resultset = array();
        if($acctId){
            $qTemp = $this->db->get_where("gcceforms.accountability", array("id"=>$acctId, "status !="=>"Cancelled"));
            if($qTemp->num_rows() == 1){
                $tempRow = $qTemp->row();
                $tempUser = $this->core_layout->getEmployeeData($tempRow->issued_to);
                $issuedTo = (object) $tempUser;
                $tempName = (isset($issuedTo->display_name_1) && $issuedTo->display_name_1)? $issuedTo->display_name_1: "No assigned name";

                if($tempRow->is_contract){
                    $qCont = $this->db->get_where("gcchris.tblcontractor", array("id"=>$tempRow->issued_to));
                    $tempName = ($qCont->num_rows() == 1)? $qCont->row()->contractor : "No assigned name";
                }

                $referenceNumber = $tempRow->reference_no;
                $isUrgent = intval($tempRow->is_urgent);
                $companyTo = $tempRow->company;

                $tempStatus = strtolower(str_replace(" ","_", $tempRow->status));
                $sendType = "add";
                switch ($tempStatus) {
                    case 'pending_accounting_notes': $sendType = "add"; break;
                    case 'pending_hr_notes': $sendType = "hr_note"; break;
                    default: $sendType = "add"; break;
                }

                $employeeName = mb_strtoupper($tempName);
                $dataAcct = $this->contentDetail($tempRow->id);
                $emailResponse = $this->email_send($employeeName, $referenceNumber, $isUrgent, $sendType, $companyTo, $dataAcct, true);
                $tempStatex = (object) $emailResponse;

                $resultset["response"] = $tempStatex->status;
                $resultset["toastr_msg"] = $tempStatex->toastr_msg;
                $resultset["isued_to"] = $tempName;
                $resultset["email_response"] = $emailResponse;
            }else{
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Accountability record not found!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Accountability not found!";
        }

        return $resultset;
    }

    // return unreturn model function
    function remarks_status(){
        $post = $this->input->post();
        // $this->db->select('id, remarks, remarks_returned');
        $remarks = $this->db->where('id', $post['id'])->get("gcceforms.accountability_body");
        return $remarks->row_array();
    }


    function update_assetPrice($get){
        $this->db->select('cost, amount, asset_id, asset_code, type');
        $this->db->from('gcceforms.accountability_body');
        $this->db->where('accountability_id', $get);
        $accbody_asset_price = $this->db->get();
        $accbody_price = $accbody_asset_price->result_array();

        $val = array();
        foreach($accbody_price as $final){
            
            if($final['type'] == "Asset"){
                $this->db->select('purchaseprice, beg_addcost, total_cost');
                $this->db->from('gccasset.assets');
                $this->db->where('id', $final['asset_id']);
                $asset_price = $this->db->get();
                $price = $asset_price->row_array();
                $total = $price['purchaseprice'] + $price['beg_addcost'];

                if($final['amount'] != $total){
                    $final_price = $total;
                    $this->db->where("asset_id = '$final[asset_id]' AND accountability_id=$get");
                    $update_columns = array(
                        "cost"=>$final_price,
                        "amount"=>$final_price
                    );
                    $update = $this->db->update("gcceforms.accountability_body", $update_columns);
                    $val[] = $final;
                    return $update;
                }else{
                    return false;
                }
            }else{
                $this->db->select('purchaseprice, beg_addcost, total_cost');
                $this->db->from('gccasset.vehicles');
                $this->db->where('gen_code', $final['asset_code']);
                $asset_price = $this->db->get();
                $price = $asset_price->row_array();
                $total = $price['purchaseprice'] + $price['beg_addcost'];

                if($final['amount'] != $total){
                    $final_price = $total;
                    $where = array(
                        "asset_code"=>$final['asset_code'],
                        "accountability_id"=>$get
                    );
                    $this->db->where($where);
                    $update_columns = array(
                        "cost"=>$final_price,
                        "amount"=>$final_price
                    );
                    $update = $this->db->update("gcceforms.accountability_body", $update_columns);
                    $val[] = $final;
                    return $update;
                }else{
                    return false;
                }
            }
            
        }
        
    }

    function getAllAcctIsBorrowedZero($type=1){
        if($type){
            $type = $type == 1? "Asset": "Vehicle";
            $tempSql = "c.is_borrowed, a.asset_id, a.asset_code, a.description, b.reference_no, a.type";
            $this->db->select($tempSql);
            $this->db->from($this->acctBodyTable." a");
            $this->db->join($this->acctTable." b", "b.id = a.accountability_id");
            if($type == "Asset"){ $this->db->join($this->assetTable." c", "c.id = a.asset_id"); }
            if($type == "Vehicle"){ $this->db->join($this->vehicleTable." c", "c.id = a.asset_id"); }
            $this->db->where("a.type", $type);
            $this->db->where("b.status !=", "Cancelled");
            $this->db->where("c.is_borrowed", 0);
            $this->db->where("c.status", "");
            $this->db->group_by("a.asset_id");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() > 0){
                echo "<pre>";
                var_dump($qTemp->result());
            }
        }
    }

    function getAllBorrIsBorrowedZero($type=1){
        if($type){
            $type = $type == 1? "Asset": "Vehicle";
            $tempSql = "c.is_borrowed, a.asset_id, a.asset_code, a.asset_name, b.reference_no, a.type";
            $this->db->select($tempSql);
            $this->db->from($this->borrBodyTable." a");
            $this->db->join($this->borrTable." b", "b.id = a.borrowing_id");
            if($type == "Asset"){ $this->db->join($this->assetTable." c", "c.id = a.asset_id"); }
            if($type == "Vehicle"){ $this->db->join($this->vehicleTable." c", "c.id = a.asset_id"); }
            $this->db->where("a.type", $type);
            $this->db->where("b.status !=", "Cancelled");
            $this->db->where("c.is_borrowed", 0);
            $this->db->where("c.status", "");
            $this->db->group_by("a.asset_id");
            $qTemp = $this->db->get();
            if($qTemp->num_rows() > 0){
                echo "<pre>";
                var_dump($qTemp->result());
            }
        }
    }

    function saveCheckerLogs($data,$filename){
        $this->load->helper('file');
        $dir = "accountability_checker";
        if (!is_dir(FCPATH . 'uploads/files/'.$dir)) {
            mkdir(FCPATH . 'uploads/files/'. $dir, 0777, TRUE);
        }
        $date = date("Y_m_d-his");
        $file = $filename.'_'.$date.".txt";
        if($data){
            $output=implode(',',$data);
            if (!write_file(FCPATH . 'uploads/files/accountability_checker/'.$file, $output)){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }   
    }

    function fixIsBorrowedOne_asset($isComponent){
        $limit = " LIMIT 1000";
        $type = "Asset";
        if($isComponent == 1){
            $component = "AND isComponent=1";
            $asset_file = "asset_1comp";
        }else{
            $component = "AND isComponent=0";
            $asset_file = "asset_1";
        }
        $assets = $this->db->query("SELECT * FROM gccasset.assets WHERE is_borrowed=1 AND isJunk=0 $component AND `status` NOT IN('damage','sold','lost','archived','junk')");
        $data = $assets->result_array();
        $result = array();
        foreach($data as $data_result){
            $res = array();
            $asset_code = $data_result['assetacode'];    
            $code = $asset_code;
            $asset_id = $data_result['id'];
            $accEntry = $this->accountabilityCheckerBorrowed($code,$type)['return'];
            $borEntry = $this->borrowingCheckerBorrowed($code,$type)['return'];
            if(isset($accEntry) && $accEntry == 1 && isset($borEntry) && $borEntry == 1){
                $result[] = $code;
                $update = $this->db->update("gccasset.assets", array("is_borrowed"=>0), array("id"=>$asset_id));
            }else if(isset($accEntry) && $accEntry == 1 && !isset($borEntry)){
                $result[] = $code;
                $update = $this->db->update("gccasset.assets", array("is_borrowed"=>0), array("id"=>$asset_id));
            }else if(!isset($accEntry) && isset($borEntry) && $borEntry == 1){
                $result[] = $code;
                $update = $this->db->update("gccasset.assets", array("is_borrowed"=>0), array("id"=>$asset_id));
            }
        }
        $checker = $this->saveCheckerLogs($result,$asset_file);
        if($checker){
            $this->core_layout->logNotification("Update isBorrowed status of assets by cronjob_reports.(isBorrowed=1)", "success", "ams", "system");
            return true;
        }else{
            $this->core_layout->logNotification("Update isBorrowed status of assets by cronjob_reports.(isBorrowed=1)", "error", "ams", "system");
            return false;
        }
    }

    function fixIsBorrowedOne_vehicle($isComponent){
        $limit = " LIMIT 1000";
        $type = "Vehicle";
        if($isComponent == 1){
            $component = "AND isCompo=1";
            $asset_file = "vehicle_1comp";
        }else{
            $component = "AND isCompo=0";
            $asset_file = "vehicle_1";
        }
        $assets = $this->db->query("SELECT * FROM gccasset.vehicles WHERE is_borrowed=1 AND isJunk=0 $component AND `status` NOT IN('damage','sold','lost','archived','junk')");
        $data = $assets->result_array();
        $result = array();
        foreach($data as $data_result){
            $res = array();
            $asset_code = $data_result['gen_code'];    
            $code = $asset_code;
            $asset_id = $data_result['id'];
            $accEntry = $this->accountabilityCheckerBorrowed($code,$type)['return'];
            $borEntry = $this->borrowingCheckerBorrowed($code,$type)['return'];
            if(isset($accEntry) && $accEntry == 1 && isset($borEntry) && $borEntry == 1){
                $result[] = $code;
                $update = $this->db->update("gccasset.vehicles", array("is_borrowed"=>0), array("id"=>$asset_id));
            }else if(isset($accEntry) && $accEntry == 1 && !isset($borEntry)){
                $result[] = $code;
                $update = $this->db->update("gccasset.vehicles", array("is_borrowed"=>0), array("id"=>$asset_id));
            }else if(!isset($accEntry) && isset($borEntry) && $borEntry == 1){
                $result[] = $code;
                $update = $this->db->update("gccasset.vehicles", array("is_borrowed"=>0), array("id"=>$asset_id));
            }
        }
        $checker = $this->saveCheckerLogs($result,$asset_file);
        if($checker){
            $this->core_layout->logNotification("Update isBorrowed status of vehicles by cronjob_reports.(isBorrowed=1)", "success", "ams", "system");
            return true;
        }else{
            $this->core_layout->logNotification("Update isBorrowed status of vehicles by cronjob_reports.(isBorrowed=1)", "error", "ams", "system");
            return false;
        }
    }

    function fixIsBorrowedZero_asset($isComponent){
        $type = "Asset";
        if($isComponent == 1){
            $component = "AND isComponent=1";
            $asset_file = "asset_0comp";
        }else{
            $component = "AND isComponent=0";
            $asset_file = "asset_0";
        }
        $assets = $this->db->query("SELECT * FROM gccasset.assets WHERE is_borrowed=0 AND isJunk=0 $component AND `status` NOT IN('damage','sold','lost','archived','junk')");
        $data = $assets->result_array();
        $result = array();
        foreach($data as $data_result){
            $asset_id = $data_result['id'];
            $data = $data_result['assetacode'];
            $accEntry = $this->accountabilityChecker($data,$type)['return'];
            $borEntry = $this->borrowingChecker($data,$type)['return'];
            if(isset($accEntry) && $accEntry == 0 && isset($borEntry) && $borEntry == 0){
                $update = $this->db->update("gccasset.assets", array("is_borrowed"=>1), array("id"=>$asset_id));
                $result[] = $data;
            }else if(isset($accEntry) && $accEntry == 0 && !isset($borEntry)){
                $update = $this->db->update("gccasset.assets", array("is_borrowed"=>1), array("id"=>$asset_id));
                $result[] = $data;
            }else if(!isset($accEntry) && isset($borEntry) && $borEntry == 0){
                $update = $this->db->update("gccasset.assets", array("is_borrowed"=>1), array("id"=>$asset_id));
                $result[] = $data;
            }else{

            }
        }
        $checker = $this->saveCheckerLogs($result,$asset_file);
        if($checker){
            $this->core_layout->logNotification("Update isBorrowed status of assets by cronjob_reports.(isBorrowed=0)", "success", "ams", "system");
            return true;
        }else{
            $this->core_layout->logNotification("Update isBorrowed status of assets by cronjob_reports.(isBorrowed=0)", "error", "ams", "system");
            return false;
        }
    }

    function fixIsBorrowedZero_vehicle($isComponent){
        $type = "Vehicle";
        if($isComponent == 1){
            $component = "AND isCompo=1";
            $asset_file = "vehicle_0comp";
        }else{
            $component = "AND isCompo=0";
            $asset_file = "vehicle_0";
        }
        $assets = $this->db->query("SELECT * FROM gccasset.vehicles WHERE is_borrowed=0 AND isJunk=0 $component AND `status` NOT IN('damage','sold','lost','archived','junk')");
        $data = $assets->result_array();
        $result = array();
        foreach($data as $data_result){
            $asset_id = $data_result['id'];
            $data = $data_result['gen_code'];
            $accEntry = $this->accountabilityChecker($data,$type)['return'];
            $borEntry = $this->borrowingChecker($data,$type)['return'];
            if(isset($accEntry) && $accEntry == 0 && isset($borEntry) && $borEntry == 0){
                $update = $this->db->update("gccasset.vehicles", array("is_borrowed"=>1), array("id"=>$asset_id));
                $result[] = $data;
            }else if(isset($accEntry) && $accEntry == 0 && !isset($borEntry)){
                $update = $this->db->update("gccasset.vehicles", array("is_borrowed"=>1), array("id"=>$asset_id));
                $result[] = $data;
            }else if(!isset($accEntry) && isset($borEntry) && $borEntry == 0){
                $update = $this->db->update("gccasset.vehicles", array("is_borrowed"=>1), array("id"=>$asset_id));
                $result[] = $data;
            }else{

            }
        }
        $checker = $this->saveCheckerLogs($result,$asset_file);
        if($checker){
            $this->core_layout->logNotification("Update isBorrowed status of vehicles by cronjob_reports.(isBorrowed=0)", "success", "ams", "system");
            return true;
        }else{
            $this->core_layout->logNotification("Update isBorrowed status of vehicles by cronjob_reports.(isBorrowed=0)", "error", "ams", "system");
            return false;
        }
    }

    function accountabilityCheckerBorrowed($asset_id,$type){
        if($type == "Asset"){
            $type = "Asset";
        }else{
            $type = "Vehicle";
        }
        $arr = array();
        $this->db->select("*");
        $this->db->from("gcceforms.accountability acc");
        $this->db->join("gcceforms.accountability_body body", "acc.id=body.accountability_id", "LEFT");
        $this->db->where("body.asset_code", $asset_id);
        $this->db->where("body.type",$type);
        $this->db->order_by("acc.created_dt","DESC");

        $query = $this->db->get();
    
        $arr['return'] = $query->row('is_returned');
        $arr['accountability'] = $query->row('accountability_id');
        $arr['asset_code'] = $query->row('asset_code');
        return $arr;
    }

    function borrowingCheckerBorrowed($asset_id,$type){
        if($type == "Asset"){
            $type = "asset";
        }else{
            $type = "vehicle";
        }
        $arr = array();
        $this->db->select("body.is_returned, body.borrowing_id, body.asset_code");
        $this->db->from("gcceforms.borrowing acc");
        $this->db->join("gcceforms.borrowing_body body", "acc.id=body.borrowing_id");
        $this->db->where("asset_code", $asset_id);
        $this->db->where("type",$type);
        $this->db->order_by("acc.created_dt","DESC");
        $query = $this->db->get();

        $arr['return'] = $query->row('is_returned');
        $arr['borrowing'] = $query->row('borrowing_id');
        $arr['asset_code'] = $query->row('asset_code');

        return $arr;
    }

    function accountabilityChecker($asset_id,$type){
        if($type == "Asset"){
            $type = "Asset";
        }else{
            $type = "Vehicle";
        }
        $this->db->select("body.is_returned, body.accountability_id, body.asset_code");
        $this->db->from("gcceforms.accountability acc");
        $this->db->join("gcceforms.accountability_body body", "acc.id=body.accountability_id", "LEFT");
        $this->db->where("asset_code", $asset_id);
        $this->db->where("status !=", "Cancelled");
        $this->db->where("type",$type);
        $this->db->order_by("acc.created_dt","DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        $arr['return'] = $query->row('is_returned');
        $arr['borrowing'] = $query->row('borrowing_id');
        $arr['asset_code'] = $query->row('asset_code');
        return $arr;
    }

    function borrowingChecker($asset_id,$type){
        if($type == "Asset"){
            $type = "Asset";
        }else{
            $type = "Vehicle";
        }
        $this->db->select("is_returned, borrowing_id, asset_code");
        $this->db->from("gcceforms.borrowing acc");
        $this->db->join("gcceforms.borrowing_body body", "acc.id=body.borrowing_id");
        $this->db->where("asset_code", $asset_id);
        $this->db->where("type",$type);
        $this->db->where("status !=", "Cancelled");
        $this->db->order_by("acc.created_dt","DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        $arr['return'] = $query->row('is_returned');
        $arr['borrowing'] = $query->row('borrowing_id');
        $arr['asset_code'] = $query->row('asset_code');
        return $arr;
    }

    function exportData($export){
        if($export == 1){
            $this->core_layout->setEventLog("Accountability Masterfile - Export excel file of Accountability Masterfile.","export", "success", "gcceforms", "user");
        }elseif($export == 2){
            $this->core_layout->setEventLog("Accountability Masterfile - Export csv file of Accountability Masterfile.","export", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Accountability Masterfile - Export pdf file of Accountability Masterfile.","export", "success", "gcceforms", "user");
        }
    }

    function exportDataArchive($export){
        if($export == 1){
            $this->core_layout->setEventLog("Archive Accountability Masterfile - Export excel file of Accountability Masterfile.","export", "success", "gcceforms", "user");
        }elseif($export == 2){
            $this->core_layout->setEventLog("Archive Accountability Masterfile - Export csv file of Accountability Masterfile.","export", "success", "gcceforms", "user");
        }else{
            $this->core_layout->setEventLog("Archive Accountability Masterfile - Export pdf file of Accountability Masterfile.","export", "success", "gcceforms", "user");
        }
    }

    function getAssetVehiclename($id = 0, $type = null){
        $tbl = $type == 'Vehicle' ? 'gccasset.vehicles' : 'gccasset.assets';
        $name = null;

        $this->db->select("name");
        $this->db->from($tbl);
        $this->db->where('id', $id);
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $row = $query->row();
            $name = $row->name;
        }

        return $name;
    }
}