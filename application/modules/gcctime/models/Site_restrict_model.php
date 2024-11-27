<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Site_restrict_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // public function getAllSite(){
    //     $post = $this->input->post();
    //     $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
    //     $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
    //     $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;

    //     $resultset = array();
    //     if($post){
    //         $rowCount = $this->all_count("gcctimeutility.app_location_sites");

    //         if(isset($search) && $search){
    //             $this->db->like("site_name", $search, "both");
    //         }

    //         $this->db->limit($limit, $offset);
    //         $this->db->order_by("id", "DESC");
    //         $allLocation = $this->db->get("gcctimeutility.app_location_sites")->result_array();
    //         $resultset["recordsTotal"] = $rowCount;
	// 		$resultset["recordsFiltered"] = $rowCount;
	// 		$resultset["data"] = $allLocation;
    //         return $resultset;
    //     }
    // }

    function getAllSite(){
        $resultset = array();
        $post = $this->input->post();
        $order_val = array(array("column"=>"0", "dir"=>"desc"));
        $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
        $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
        $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

        $rowCount = 0;
        $rowData = array();

        $rowData = $this->get_item($limit, $offset, $sortBy, $sortOrder, $search);
        $rowCount = $this->get_item_count($search);

        $resultset["recordsTotal"] = $rowCount;
        $resultset["recordsFiltered"] = $rowCount;
        $resultset["data"] = $rowData;

        return $resultset;
    }

    function get_item($limit, $offset, $sortBy, $sortOrder, $search = null){
        $filterFields = array('site_name');
        $resultset = array();

        $this->db->from('gcctimeutility.app_location_sites');
        
        if($search){
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

        if($limit != -1){
            $this->db->limit($limit, $offset);
        }

        $i = $sortOrder[0]['column'];
        $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $resultset = $query->result();
        }

        return $resultset;
    }

    function get_item_count($search){
        $filterFields = array('site_name');

        $this->db->from('gcctimeutility.app_location_sites');
        
        if($search){
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

    public function addNewLocation(){
        $post = $this->input->post();
        $goefence = serialize($post['geofence']);
        $data = array(
            "site_name" => trim($post['siteName']),
            "latitude" => $post['latitude'],
            "longtitude" => $post['longitude'],
            "geofence_polygon" => $goefence,
        );
        // if(isset($post['geofence']) && $post['geofence'] != null){
        //     $goefence = serialize($post['geofence']);
        //     $data = array(
        //         "site_name" => $post['siteName'],
        //         "latitude" => $post['latitude'],
        //         "longtitude" => $post['longitude'],
        //         "geofence_polygon" => $goefence,
        //     );
        // }else{
        //     $data = array(
        //         "site_name" => $post['siteName'],
        //         "latitude" => $post['latitude'],
        //         "longtitude" => $post['longitude']
        //     );
        // }

        
        if($post['id'] == ""){
            $checkname = $this->db->get_where("gcctimeutility.app_location_sites", array('site_name' => trim($post['siteName'])))->num_rows();

            if($checkname > 0){
                return 2;
            }else{
                $inserted = $this->db->insert("gcctimeutility.app_location_sites", $data);
                if($inserted){
                    return 1;
                }else{
                    return 0;
                }
            }
        }else{
            $id = $post['id'];
            $this->db->where("id", $id);
            $editData = $this->db->update("gcctimeutility.app_location_sites", $data);
            if($editData){
                return 1;
            }else{
                return 0;
            }
        }
    }

    public function get_all_site_location(){
        $resultarray = array();
        $query =$this->db->get("gcctimeutility.app_location_sites");
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $_query) {
                $data = array();
                $data["site_id"] = $_query["id"];
                $data["id"] = $_query["site_name"];
                $data["text"] = $_query["site_name"];
                $resultarray[] = $data;
            }
        }
        return  array("results" => $resultarray);
    }

    public function personnelLocations(){
        $post = $this->input->post();
        $id = $post["id"];
        $locations = $post['site_names'];
        
        foreach($locations as $site_names){
            $site_id = $this->siteNames($site_names);
            $this->db->where("personnel_id", $id);
            $this->db->where("location_name", $site_names);
            $confirm = $this->db->get("gcctimeutility.personnel_locations");
            if($confirm->num_rows() == 0){
                $query = $this->crud->insert(array("personnel_id" => $id, "location_name" => $site_names, "site_location_id" => $site_id),"gcctimeutility.personnel_locations");
            }
        }
        if($query){
            return $this->getAllPersonelLocation($id);
        }else{
            return 0;
        }
    }

    public function deleteLocation(){
        $post = $this->input->post();
        $id = $post["id"];
        $this->db->where("id", $id);
        $confirm = $this->db->delete("gcctimeutility.app_location_sites");
        if($confirm){
            return 1;
        }else{
            return 0;
        }
    }

    public function editLocation(){
        $result = array();

        $post = $this->input->post();
        $id = $post["id"];
        $this->db->where("id", $id);
        $editData = $this->db->get("gcctimeutility.app_location_sites");

        if($editData->num_rows() > 0){
            foreach($editData->result() as $row){
                if($row->geofence_polygon != '' && $row->geofence_polygon){
                    $row->geofence_polygon = $this->getLocation($row->geofence_polygon);
                }
                // $row->geofence_polygon = $row->geofence_polygon ? @unserialize($row->geofence_polygon) : "";

                $result[] = $row;
            }
        }
        return $result[0];
    }

    public function allAppointedLocation(){
        $post = $this->input->post();
        $id = $post['id'];
        $this->db->where("personnel_id", $id);
        $person_location = $this->db->get("gcctimeutility.personnel_locations")->result_array();
        return $person_location;
    }

    public function deleteAppointedLocation(){
        $post = $this->input->post();
        $id = $post['id'];
        $personnel_id = $post['personnel_id'];

        $this->db->where("id", $id);
        $this->db->where("personnel_id", $personnel_id);
        $del = $this->db->delete("gcctimeutility.personnel_locations");
        if($del){
            return $this->getAllPersonelLocation($personnel_id);
        }else{
            return 0;
        }
    }

    private function all_count($database = ""){
        $data = $this->db->get($database);
        $num = $data->num_rows();
        return $num;
    }

    private function getAllPersonelLocation($personnel_id){
        $this->db->where("personnel_id", $personnel_id);
        return $this->db->get("gcctimeutility.personnel_locations")->result_array();
    }

    private function siteNames($site_names){
        $this->db->select('id');
        $this->db->where('site_name', $site_names);
        $data = $this->db->get("gcctimeutility.app_location_sites");
        $id = $data->row_array();
        return $id['id'];
    }

    private function getLocation($location){
        $result = array();
        $geolocation = @unserialize($location);
            
        foreach($geolocation as $key => $row){
            $geolocation[$key][] = (float)$geolocation[$key]['lng'];
            $geolocation[$key][] = (float)$geolocation[$key]['lat'];

            unset($geolocation[$key]['lat'], $geolocation[$key]['lng']);
            $result = $geolocation;
        }

        $first_tagged = $result[0]; // added to equal the last coordinate to the first coordinate
        array_push($result, $first_tagged);

        return $result;
    }
}