<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Dashboard_model extends CI_Model
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->model("Utilities_model", "utilities");
            $this->load->model("core/Core_model", "core");
        }

        public function getAssetDemographics()
        {
            $select = "companies.code, COUNT(assets.id) asset_count";
            $this->db->select($select);
            $this->db->where("assets.status NOT IN('archived', 'lost', 'junk', 'tradein', 'sold', 'damage', 'destructed') OR assets.`status` IS NULL AND assets.isJunk != 1");
            $this->db->join("gccasset.assets assets", "assets.company_code = companies.code", "INNER");
            $this->db->order_by("COUNT(assets.id)", "DESC");
            $this->db->group_by("companies.code");
            $queryAssets = $this->db->get("gcchris.tblcompanies companies");
            $assets = $queryAssets->result();

            $this->db->reset_query();

            $this->db->select($select);
            $this->db->where("assets.status2 NOT IN('archived', 'lost', 'junk', 'tradein', 'sold', 'damage', 'destructed') OR assets.`status2` IS NULL AND assets.isJunk != 1");
            $this->db->join("gccasset.vehicles assets", "assets.company_code = companies.code", "INNER");
            $this->db->order_by("COUNT(assets.id)", "DESC");
            $this->db->group_by("companies.code");
            $queryVehicles = $this->db->get("gcchris.tblcompanies companies");
            $vehicles = $queryVehicles->result();

            $temp = [];
            $data = array_merge_recursive($vehicles, $assets);
            foreach ($data as $item) {
                if (!array_key_exists($item->code, $temp)) {
                    $temp[$item->code] = 0;
                }
                $temp[$item->code] += $item->asset_count;
            }

            $_merged = array();
            foreach ($temp as $key => $value) {
                $item = array("code" => $key, "value" => $value);
                array_push($_merged, $item);
            }

            usort($_merged, function ($a, $b) {
                return $a["value"] - $b["value"];
            });

            return array("vehicles" => $vehicles, "assets" => $assets, "merged" => $_merged);
        }

        public function getRecentlyAddedAssets($recently)
        {
            $date = date("Y-m-d");
            $from = date("Y-m-d",strtotime($date." + 1 days"));
            $to = date("Y-m-d",strtotime($date." - 7 days"));
            $year = date("Y");
            $month = date("m");
            if($recently == "week"){
                $asset_weekly = "SELECT IF(a.status != '', 'ASSETS', 'ASSETS') as status, COUNT(a.id) AS count FROM gccasset.assets as a WHERE a.isComponent = 0 AND a.isJunk = 0 AND DATE_FORMAT(a.dateCreated, '%Y-%m-%d') BETWEEN '$to' AND '$date'";
                $query = $this->db->query($asset_weekly);

                $assetComp_weekly = "SELECT IF(a.status != '', 'ASSETS COMP', 'ASSETS COMP') as status, COUNT(a.id) AS count FROM gccasset.assets as a WHERE a.isComponent = 1 AND a.isJunk = 0 AND DATE_FORMAT(a.dateCreated, '%Y-%m-%d') BETWEEN '$to' AND '$date'";
                $query_comp = $this->db->query($assetComp_weekly);

                $vehicle_weekly = "SELECT IF(a.status != '', 'VEHICLES', 'VEHICLES') as status, COUNT(a.id) AS count FROM gccasset.vehicles as a WHERE a.isCompo = 0 AND a.isJunk = 0 AND DATE_FORMAT(a.created_at, '%Y-%m-%d') BETWEEN '$to' AND '$date'";
                $query_vehicle = $this->db->query($vehicle_weekly);

                $vehicleComp_weekly = "SELECT IF(a.status != '', 'VEHICLES COMP', 'VEHICLES COMP') as status, COUNT(a.id) AS count FROM gccasset.vehicles as a WHERE a.isCompo = 1 AND a.isJunk = 0 AND DATE_FORMAT(a.created_at, '%Y-%m-%d') BETWEEN '$to' AND '$date'";
                $query_vehicleComp = $this->db->query($vehicleComp_weekly);

            }else{

                $asset_weekly = "SELECT IF(a.status != '', 'ASSETS', 'ASSETS') as status, COUNT(a.id) AS count FROM gccasset.assets as a WHERE a.isComponent = 0 AND a.isJunk = 0 AND MONTH(a.dateCreated) = '$month' AND YEAR(a.dateCreated) = '$year'";
                $query = $this->db->query($asset_weekly);

                $assetComp_weekly = "SELECT IF(a.status != '', 'ASSETS COMP', 'ASSETS COMP') as status, COUNT(a.id) AS count FROM gccasset.assets as a WHERE a.isComponent = 1 AND a.isJunk = 0 AND MONTH(a.dateCreated) = '$month' AND YEAR(a.dateCreated) = '$year'";
                $query_comp = $this->db->query($assetComp_weekly);

                $vehicle_weekly = "SELECT IF(a.status != '', 'VEHICLES', 'VEHICLES') as status, COUNT(a.id) AS count FROM gccasset.vehicles as a WHERE a.isCompo = 0 AND a.isJunk = 0 AND MONTH(a.created_at) = '$month' AND YEAR(a.created_at) = '$year'";
                $query_vehicle = $this->db->query($vehicle_weekly);

                $vehicleComp_weekly = "SELECT IF(a.status != '', 'VEHICLES COMP', 'VEHICLES COMP') as status, COUNT(a.id) AS count FROM gccasset.vehicles as a WHERE a.isCompo = 1 AND a.isJunk = 0 AND MONTH(a.created_at) = '$month' AND YEAR(a.created_at) = '$year'";
                $query_vehicleComp = $this->db->query($vehicleComp_weekly);
            }
            
            $arrData_asset = array();
            $arrData_assetComp = array();
            $arrData_vehicle = array();
            $arrData_vehicleComp = array();
            foreach($query->result() as $key => $rs){
                $arrData_asset[$key] = $rs;
            }
            foreach($query_comp->result() as $key => $rs){
                $arrData_assetComp[$key] = $rs;
            }
            foreach($query_vehicle->result() as $key => $rs){
                $arrData_vehicle[$key] = $rs;
            }
            foreach($query_vehicleComp->result() as $key => $rs){
                $arrData_vehicleComp[$key] = $rs;
            }
            $result = array_merge($arrData_vehicleComp, $arrData_vehicle, $arrData_assetComp, $arrData_asset);
            return json_decode(json_encode($result));    
        }

        public function getAssetIncompleteDetails()
        {
            $where = "datepurchased='0000-00-00' OR check_no='' OR po_no='' OR remarks='' OR status='' OR rr_no='' AND isComponent = 0 ";
            $this->db->select("IF(status != '', 'ASSETS', 'ASSETS') as status, COUNT(id) AS count");
            $this->db->from("gccasset.assets");
            $this->db->where($where);
            $query = $this->db->get();

            $where_comp = "datepurchased='0000-00-00' OR check_no='' OR po_no='' OR remarks='' OR status='' OR rr_no='' AND isComponent = 1 ";
            $this->db->select("IF(status != '', 'ASSETS COMP', 'ASSETS COMP') as status, COUNT(id) AS count");
            $this->db->from("gccasset.assets");
            $this->db->where($where_comp);
            $query_component = $this->db->get();


            $where_vehicle = "datepurchased='0000-00-00' OR check_no='' OR po_no='' OR status2='' OR rr_no='' AND isCompo = 0";
            $this->db->select("IF(status2 != '', 'VEHICLES', 'VEHICLES') as status, COUNT(id) AS count");
            $this->db->from("gccasset.vehicles");
            $this->db->where($where_vehicle);
            $query_vehicle = $this->db->get();

            $where_vehicle_comp = "datepurchased='0000-00-00' OR check_no='' OR po_no='' OR status2='' OR rr_no='' AND isCompo = 1";
            $this->db->select("IF(status2 != '', 'VEHICLES COMP', 'VEHICLES COMP') as status, COUNT(id) AS count");
            $this->db->from("gccasset.vehicles");
            $this->db->where($where_vehicle_comp);
            $query_vehicle_component = $this->db->get();

                $arrData_asset = array();
                $arrData_assetComp = array();
                $arrData_vehicle = array();
                $arrData_vehicleComp = array();
                foreach($query->result() as $key => $rs){
                    $arrData_asset[$key] = $rs;
                }
                foreach($query_component->result() as $key => $ss){
                    $arrData_assetComp[$key] = $ss;  
                }
                foreach($query_vehicle->result() as $key => $as){
                    $arrData_vehicle[$key] = $as;
                }
                foreach($query_vehicle_component->result() as $key => $vs){
                    $arrData_vehicleComp[$key] = $vs;  
                }
                // $arrData = $query_component->result();
                $result = array_merge($arrData_vehicle, $arrData_vehicleComp, $arrData_assetComp, $arrData_asset);
                // $result = $arrData_asset + $arrData_assetComp + $arrData_vehicle + $arrData_vehicleComp;
                return json_decode(json_encode($result));
        }

        public function getAssetPerStatus($type)
        {
            if($type == "vehicle"){
                $this->db->select("IF(v.status2 = '', 'NO STATUS' , UPPER(status.name)) as status, COUNT(v.id) AS count");
                $this->db->from("gccasset.vehicles v");
                $this->db->join("gccasset.status status", "status.code=v.status2", "left");
                $this->db->where("v.isCompo", 0);
                $this->db->group_by("v.status2");
                $query = $this->db->get();
            }else if($type == "vehicle_equipment"){
                $this->db->select("IF(v.status2 = '', 'NO STATUS' , UPPER(status.name)) as status, COUNT(v.id) AS count");
                $this->db->from("gccasset.vehicles v");
                $this->db->join("gccasset.status status", "status.code=v.status2", "left");
                $this->db->where("v.isCompo", 1);
                $this->db->group_by("v.status2");
                $query = $this->db->get();
            }else if($type == "asset"){
                $this->db->select("IF(v.status = '', 'NO STATUS' , UPPER(status.name)) as status, COUNT(v.id) AS count");
                $this->db->from("gccasset.assets v");
                $this->db->join("gccasset.status status", "status.code=v.status", "left");
                $this->db->where("v.isComponent", 0);
                $this->db->group_by("v.status");
                $query = $this->db->get();
            }else{
                $this->db->select("IF(v.status = '', 'NO STATUS' , UPPER(status.name)) as status, COUNT(v.id) AS count");
                $this->db->from("gccasset.assets v");
                $this->db->join("gccasset.status status", "status.code=v.status", "left");
                $this->db->where("v.isComponent", 1);
                $this->db->group_by("v.status");
                $query = $this->db->get();
            }
                $arrData_asset = array();
                foreach($query->result() as $key => $rs){
                    $arrData_asset[$key] = $rs;
                }
                return json_decode(json_encode($arrData_asset));
        }

        function getAssetPerLocation($type){
            if($type == "vehicle"){
                $where = "v.status2 NOT IN ('damage', 'lost','junk','sold','destructed','archived','tradein') AND isJunk != 1 and isCompo = 0";
                $this->db->select("IF(v.location = '', 'NO LOCATION', UPPER(v.location)) as location, COUNT(v.id) AS count");
                $this->db->from("gccasset.vehicles v");
                $this->db->where($where);
                $this->db->join("gccasset.location loc","loc.id = v.area_id", "LEFT");
                // $this->db->join("gccasset.")
                $this->db->group_by("loc.id");
                $this->db->order_by("loc.id DESC");
                $query = $this->db->get();
            }else if($type == "vehicle_equipment"){
                $where = "v.status2 NOT IN ('damage', 'lost','junk','sold','destructed','archived') AND isJunk != 1 AND isCompo = 1";
                $this->db->select("IF(v.location = '', 'NO LOCATION', UPPER(v.location)) as location, COUNT(v.id) AS count");
                $this->db->from("gccasset.vehicles v");
                $this->db->where($where);
                $this->db->join("gccasset.location loc","loc.id = v.area_id", "LEFT");
                // $this->db->join("gccasset.")
                $this->db->group_by("loc.id");
                $this->db->order_by("loc.id DESC");
                $query = $this->db->get();
            }else if($type == "asset"){
                $where = "v.status NOT IN ('damage', 'lost','junk','sold','destructed','archived') AND isJunk != 1 AND isComponent = 0";
                $this->db->select("IF(v.location = '', 'NO LOCATION', UPPER(loc.location)) as location, COUNT(v.id) AS count");
                $this->db->from("gccasset.assets v");
                $this->db->where($where);
                $this->db->join("gccasset.location loc","loc.id = v.area_id", "LEFT");
                // $this->db->join("gccasset.")
                $this->db->group_by("loc.id");
                $this->db->order_by("loc.id DESC");
                $query = $this->db->get();
            }else{
                $where = "v.status NOT IN ('damage', 'lost','junk','sold','destructed','archived') AND isJunk != 1 AND isComponent = 1";
                $this->db->select("IF(v.location = '', 'NO LOCATION', UPPER(loc.location)) as location, COUNT(v.id) AS count");
                $this->db->from("gccasset.assets v");
                $this->db->where($where);
                $this->db->join("gccasset.location loc","loc.id = v.area_id", "LEFT");
                // $this->db->join("gccasset.")
                $this->db->group_by("loc.id");
                $this->db->order_by("loc.id DESC");
                $query = $this->db->get();
            }

                $arrData = array();
                foreach($query->result() as $key => $rs){
                    $arrData[$key] = $rs;
                }
    
                return json_decode(json_encode($arrData));
            
        }

        public function getAccountedUnaccountedAssets($type)
        {

            if($type == "accounted_asset"){
                // Asset
                $select = "COUNT(assets.id) as  count1, IF(COUNT(assets.id) > 0, 'UNACCOUNTED\nASSETS', 'UNACCOUNTED\nASSETS') as status";
                $this->db->select($select);
                $this->db->from("gccasset.assets assets");
                $this->db->where("assets.status NOT IN('archived', 'lost', 'junk', 'tradein', 'sold', 'destructed', 'damage') AND assets.isJunk != 1 AND assets.is_archived != 1 AND assets.isComponent != 1");
                $assets = $this->db->get()->row();
                $arrDataAsset = array();
            
                $this->db->select("IF(body.type = 'Asset', 'ACCOUNTED\nASSETS' , 'ACCOUNTED\nASSETS') as status, count(body.asset_id) as count2");
                $this->db->from("gcceforms.accountability_body body");
                $this->db->join("gccasset.assets main", "main.id=body.asset_id", "left");
                $this->db->where("body.is_returned", 0);
                $this->db->where("main.isComponent", 0);
                $accounted = $this->db->get()->row();
                $arrDataAccounted = array();        
                
                $arrDataAccounted['status'] = $accounted->status;
                $arrDataAccounted['count'] = $accounted->count2;

                $arrDataAsset['status'] = $assets->status;
                $arrDataAsset['count'] = $assets->count1 - $accounted->count2;

                // Asset Component
                $select_comp = "COUNT(assets.id) as  count3, IF(COUNT(assets.id) > 0, 'UNACCOUNTED\nCOMPONENTS', 'UNACCOUNTED\nCOMPONENTS') as status";
                $this->db->select($select_comp);
                $this->db->from("gccasset.assets assets");
                $this->db->where("assets.status NOT IN('archived', 'lost', 'junk', 'tradein', 'sold', 'destructed', 'damage') AND assets.isJunk != 1 AND assets.is_archived != 1 AND assets.isComponent = 1");
                $assetcomp = $this->db->get()->row();
                $arrDataAssetComp = array();
            
                $this->db->select("IF(body.type = 'Asset', 'ACCOUNTED\nCOMPONENTS' , 'ACCOUNTED\nCOMPONENTS') as status, count(body.asset_id) as count4");
                $this->db->from("gcceforms.accountability_body body");
                $this->db->join("gccasset.assets main", "main.id=body.asset_id", "left");
                $this->db->where("body.is_returned", 0);
                $this->db->where("main.isComponent", 1);
                $this->db->where("body.type", "Asset");
                $accountedComp = $this->db->get()->row();
                $arrDataAccountedComp = array();    

                $arrDataAccountedComp['status'] = $accountedComp->status;
                $arrDataAccountedComp['count'] = $accountedComp->count4;

                $arrDataAssetComp['status'] = $assetcomp->status;
                $arrDataAssetComp['count'] = $assetcomp->count3 - $accountedComp->count4;    
            }else{
                //Vehicles
                $select = "COUNT(v.id) as count1, IF(COUNT(v.id) > 0, 'UNACCOUNTED\nVEHICLES', 'UNACCOUNTED\nVEHICLES') as status";
                $this->db->select($select);
                $this->db->from("gccasset.vehicles v");
                $this->db->where("v.status2 NOT IN('archived', 'lost', 'junk', 'tradein', 'sold', 'destructed', 'damage') AND v.isJunk != 1 AND v.is_archived != 1 AND v.isCompo != 1");
                $assets = $this->db->get()->row();
                $arrDataAsset = array();
            
                $this->db->select("IF(body.type = 'Asset', 'ACCOUNTED\nVEHICLES' , 'ACCOUNTED\nVEHICLES') as status, count(body.asset_id) as count2");
                $this->db->from("gcceforms.accountability_body body");
                $this->db->join("gccasset.vehicles main", "main.gen_code=body.asset_code", "left");
                $this->db->where("body.is_returned", 0);
                $this->db->where("main.isCompo", 0);
                $this->db->where("body.type", "Vehicle");
                $accounted = $this->db->get()->row();
                $arrDataAccounted = array();        
                
                $arrDataAccounted['status'] = $accounted->status;
                $arrDataAccounted['count'] = $accounted->count2;

                $arrDataAsset['status'] = $assets->status;
                $arrDataAsset['count'] = $assets->count1 - $accounted->count2;

                // Vehicle Components
                $select_comp = "COUNT(v.id) as  count3, IF(COUNT(v.id) > 0, 'UNACCOUNTED\nCOMPONENTS', 'UNACCOUNTED\nCOMPONENTS') as status";
                $this->db->select($select_comp);
                $this->db->from("gccasset.vehicles v");
                $this->db->where("v.status2 NOT IN('archived', 'lost', 'junk', 'tradein', 'sold', 'destructed', 'damage') AND v.isJunk != 1 AND v.is_archived != 1 AND v.isCompo = 1");
                $assetcomp = $this->db->get()->row();
                $arrDataAssetComp = array();
            
                $this->db->select("IF(body.type = 'Asset', 'ACCOUNTED\nCOMPONENTS' , 'ACCOUNTED\nCOMPONENTS') as status, count(body.asset_id) as count4");
                $this->db->from("gcceforms.accountability_body body");
                $this->db->join("gccasset.vehicles main", "main.id=body.asset_id", "left");
                $this->db->where("body.is_returned", 0);
                $this->db->where("main.isCompo", 1);
                $accountedComp = $this->db->get()->row();
                $arrDataAccountedComp = array();    
                
                $arrDataAccountedComp['status'] = $accountedComp->status;
                $arrDataAccountedComp['count'] = $accountedComp->count4;

                $arrDataAssetComp['status'] = $assetcomp->status;
                $arrDataAssetComp['count'] = $assetcomp->count3 - $accountedComp->count4; 
            }
            

            $arrData = array($arrDataAssetComp, $arrDataAccountedComp, $arrDataAsset, $arrDataAccounted);
            return json_decode(json_encode($arrData));
            
        }

        function getEmployeeName($id){
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

        function getRecentlyAddedAssets_email($date=null){
            $tempDateToday = $date ? date("Y-m-d", strtotime($date)): date("Y-m-d");
            $this->db->select("id, assetacode as asset_code, UPPER(name) as asset_name, 
                dateCreated as created_at, createdBy as created_by, check_no, 
                IF(mother_asset = '0' && isComponent = '0', 'ASSET', 'ASSET COMPONENT') as type, 
                dateCreated as added_at, createdBy as added_by");
            $this->db->from("gccasset.assets");
            $this->db->where("DATE(dateCreated)", $tempDateToday);

            $queryAddedAssets = $this->db->get();
            $arrDataAssetsAdded = array();
            if($queryAddedAssets->num_rows() > 0){
                foreach ($queryAddedAssets->result_array() as $key => $value) {
                    if(isset($value["added_by"]) && is_numeric($value["added_by"])){
                        $value["added_by"] = $this->getEmployeeName($value["added_by"]);
                    }else{
                        $value["added_by"] = $value["added_by"];
                    }
                    $arrDataAssetsAdded[$key] = $value;
                }
            }

            $this->db->select("id, gen_code as asset_code, UPPER(name) as asset_name, 
                created_at, created_by, updated_at, updated_by, check_no, 
                IF(motherID = '0' && isCompo = '0', 'VEHICLE', 'VEHICLE COMPONENT') as type, 
                created_at as added_at, created_by as added_by");
            $this->db->from("gccasset.vehicles");
            $this->db->where("DATE(created_at)", $tempDateToday);

            $queryAddedVehicle = $this->db->get();
            $arrDataVehicleAdded = array();
            if($queryAddedVehicle->num_rows() > 0){
                foreach ($queryAddedVehicle->result_array() as $key => $value) {
                    if(isset($value["added_by"]) && is_numeric($value["added_by"])){
                        $value["added_by"] = $this->getEmployeeName($value["added_by"]);
                    }else{
                        $value["added_by"] = "NO ASSIGNED NAME";
                    }
                    $arrDataVehicleAdded[$key] = $value;
                }
            }

            $resultCreatedAssets = array_merge($arrDataAssetsAdded, $arrDataVehicleAdded);
            
            $resultset = array();
            $resultset["data"] = $resultCreatedAssets;
            return $resultset;
        }
    }
