<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Travel_order_m extends CI_Model {
        protected $eformsTable = "gcceforms";
        protected $travelOrderTable = "gcceforms.travel_order";
        protected $travelPersonnelTable = "gcceforms.travel_personnel";
        protected $travelDestinationTable = "gcceforms.travel_destination";
        protected $employeeTable = "gccmaster.tblemployees";
        private $user_data = array();

        public function __construct() {
            parent::__construct();
            $this->user_data = $this->session->userdata("logged_in"); 
        }

        function getTravelOrderList() {
            $this->core_layout->setPrivilegeName("to_masterfile");
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "6", "dir" => "desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            /*** edited by ID DESC sort on first load ***/
            /*** $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val; ***/
            /*** edited by ID DESC sort on first load ***/
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : null;
            $start = (isset($post["start_date"]) && $post["start_date"]) ? $post["start_date"] : false;
            $end = (isset($post["end_date"]) && $post["end_date"]) ? $post["end_date"] : false;
            $query_builder = (isset($post["query_builder"]['sql']) && $post["query_builder"]['sql'])? $post["query_builder"]['sql']: array();
            $status = (isset($post['status']) && $post['status']) ? ucwords($post['status']) : null; //clicked in portal dashboard
            
            $privilege = $this->core_layout->getCurrentActions();

            $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
            $companyDescription = null;

            if ($view_by_company) {
                $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
            }
            
            $rowData = $this->get_all_item($privilege, $start, $end, $query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $status, $view_by_company, $companyDescription);
            $rowCount = $this->get_all_item_count($privilege, $start, $end, $query_builder, $search, $status, $view_by_company, $companyDescription);
            // if (!$search) {
            //     $rowData = $this->get_all_post($privilege, $start, $end, $query_builder, $limit, $offset, $sortBy, $sortOrder, $status);
            //     $rowCount = $this->get_all_post_count($privilege, $start, $end, $query_builder, $status);
            // }

            if ($search) {
                // $rowData = $this->get_searched_item($privilege, $start, $end, $query_builder, $search, $limit, $offset, $sortBy, $sortOrder, $status);
                // $rowCount = $this->get_searched_item_count($privilege, $start, $end, $query_builder, $search, $status);
                $this->core_layout->setEventLog("Travel Order Masterfile - Search ".$search.".","search", "success", "gcceforms", "user");
            }
            if($query_builder){
                $this->core_layout->setEventLog("Travel Order Mastefile - Generate masterfile through query builder `{$query_builder}`.", "search", "success", "gcceforms", "user");
            }

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        public function get_all_item($privilege, $start, $end, $query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null, $view_by_company = false, $companyDescription = null) {
            $filterFields1 = array("a.id", "a.status", "a.reference_no", "a.company", "a.driver", "a.others_remarks","te.lastname","te.firstname","td.des_to", 'tod.destination', 'toe.firstname', 'toe.lastname');
            $date = date("Y-m-d", strtotime("-1 year", time()));
            $check = date("Y-m-d", strtotime("-1 year", time()));

            $arrData = array();
            $role_id = $this->authenticate->getRoleId();
            $current_date = date("Y-m-d");

            $sql = "a.id, a.reference_no, a.company,a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks,a.accomplishment_dt, tod.destination, toe.firstname, toe.lastname, a.created_dt, a.accomplished";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_order a");
            $this->db->join("gcceforms.travel_personnel tp","tp.travel_order_id = a.id");
            $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
            $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
            $this->db->join("gcceforms.travel_destination tod","tod.travel_order_id = a.id");
            $this->db->join('gcceforms.travel_personnel top', 'top.travel_order_id = a.id', 'left');
            $this->db->join('gccmaster.tblemployees toe', 'toe.id = top.employee_id', 'left');

            if ($start && $end) {
                $this->db->group_start();
                $this->db->where("DATE(td.date_from) >=", $start);
                $this->db->where("DATE(td.date_from) <=", $end);
                $this->db->group_end();

                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("DATE(a.created_dt) >=", $check);
                $this->db->group_by("a.id");
            }
            else if ($status == 'Accomplished') {
                $this->db->where('a.accomplished', 1);
            }
            else if ($status) {
                $this->db->where('a.status', $status);
                $this->db->where('a.accomplished', 0);
            }
            else {
                $this->db->group_start();
                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("DATE(a.created_dt) >=", $check);
                $this->db->group_end();
            }

            if (isset($query_builder) && $query_builder) {
                $this->db->where($query_builder);
            }


            $view_own_request = (in_array("view_own_request", $privilege)) ? true : false;
            if($view_own_request && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.created_id', $this->user_data['emp_id']);
            }

            $guard = (in_array("guard_edit_to", $privilege)) ? true : false;
            if($guard){
                $this->db->where("a.status =", "Approved");
                $this->db->where("DATE(a.approved_dt)", $current_date);
            }

            if ($view_by_company) {
                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            }

            if (isset($search) && $search) {
                $this->db->group_start();
                foreach ($filterFields1 as $key => $field) {
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
            
            if($sortOrder !== null){
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }else{
                $this->db->order_by("a.id", "DESC");
            }
            $this->db->group_by('a.id');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $rowId = $rs->id;
                    $this->db->select("firstname, lastname, middlename, suffix");
                    $this->db->from("gcceforms.travel_personnel tp");
                    $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                    $this->db->where("tp.travel_order_id", $rowId);
                    $personnel = $this->db->get();
        
                    $rowPersonnel = array();
                    $rowDestination = array();
                    $rowDriver = "";
                    $rowVehiclePlate = "";
                    $rowVehicleDesc = "";
                    $rowFromTo = "";
        
                    if ($personnel->num_rows() > 0) {
                        foreach ($personnel->result() as $key => $value) {
                            $tempRs = (array)$value;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $value->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            $rowPersonnel[] = $value->display_name;
                        }
                    }
        
                    if ($rs->is_service == "1") {
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();
        
                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $tempRs = (array)$rowData;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            if ($rowData->display_name) {
                                $rowDriver = $rowData->display_name;
                            } else {
                                $rowDriver = $rs->driver;
                            }
                        }
        
                        $this->db->select("plateno, description, name");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->name;
                        }
                    }
        
                    $this->db->select("td.destination, td.date_from, td.date_to");
                    $this->db->from("gcceforms.travel_destination td");
                    if ($start && $end) {
                        $this->db->group_start();
                        $this->db->where("DATE(td.date_from) >=", $start);
                        $this->db->where("DATE(td.date_from) <=", $end);
                        $this->db->group_end();
                        $this->db->where("td.travel_order_id", $rowId);
                    } else {
                        $this->db->where("td.travel_order_id", $rowId);
                    }
                    $destination = $this->db->get();
                    $destinationDateTime = array();
                    $tempDates = array();
        
                    if ($destination->num_rows() > 0) {
                        $des_num = 0;
                        foreach ($destination->result() as $key => $vx) {
                            $des_num++;
                            $rowDestination[] = $vx->destination;
        
                            $x = explode(' ', $vx->date_from);
                            $y = explode(' ', $vx->date_to);
                            if ($des_num == 1) {
                                $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                                $z = explode(' ', $vx->date_from);
                            }
        
                            if ($z[0] != $x[0]) {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            } else if ($x[0] == $y[0]) {
                                $date_end = date("g:i A", strtotime($y[1]));
                            } else {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            }
        
                            $date = $date_start . "-" . $date_end;
                            array_push($destinationDateTime, $date);
                            array_push($tempDates, $vx->date_to);
                        }
                    } else {
                        return array();
                    }

                    $rs->personnels = $rowPersonnel;
                    $rs->driver = $rowDriver;
                    $rs->vehicle_plate = $rowVehiclePlate;
                    $rs->vehicle_description = $rowVehicleDesc;
                    $rs->destination = $rowDestination;
                    $rs->from_to = $destinationDateTime;
                    $rs->company_detail = $this->getCompany($rs->company);
                    $rs->user_role_id = $this->authenticate->getRoleId();
                    $rs->created_dt = date("m-d-Y h:i A", strtotime( $rs->created_dt));
                    $rs->tempDates = (Object) $tempDates;
                    $arrData[] = $rs;
                }
            }

            return $arrData;
        }

        public function get_all_item_count($privilege, $start, $end, $query_builder=null, $search = null, $status = null, $view_by_company = false, $companyDescription = null){
            $role_id = $this->authenticate->getRoleId();
            $current_date = date("Y-m-d");

            $filterFields1 = array("a.id", "a.status", "a.reference_no", "a.company", "a.driver", "a.others_remarks","te.lastname","te.firstname","td.des_from","td.des_to", "tod.destination", "toe.firstname", "toe.lastname");
            $date = date("Y-m-d", strtotime("-1 year", time()));
            $check = date("Y-m-d", strtotime("-1 year", time()));
            $arrData = array();

            $sql = "a.id, a.reference_no, a.company,a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks,a.accomplishment_dt, tod.destination, toe.firstname, toe.lastname";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_order a");
            $this->db->join("gcceforms.travel_personnel tp","tp.travel_order_id = a.id");
            $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
            $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
            $this->db->join("gcceforms.travel_destination tod","tod.travel_order_id = a.id");
            $this->db->join('gcceforms.travel_personnel top', 'top.travel_order_id = a.id', 'left');
            $this->db->join('gccmaster.tblemployees toe', 'toe.id = top.employee_id', 'left');

            if ($start && $end) {
                $this->db->group_start();
                $this->db->where("DATE(td.date_from) >=", $start);
                $this->db->where("DATE(td.date_from) <=", $end);
                $this->db->group_end();

                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("DATE(a.created_dt) >=", $check);
                $this->db->group_by("a.id");
            }
            else if ($status == 'Accomplished') {
                $this->db->where('a.accomplished', 1);
            }
            else if ($status) {
                $this->db->where('a.status', $status);
                $this->db->where('a.accomplished', 0);
            }
            else{
                $this->db->group_start();
                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("DATE(a.created_dt) >=", $check);
                $this->db->group_end();
            }

            if(isset($query_builder) && $query_builder){
                $this->db->where($query_builder);
            }
        
        
            $view_own_request = (in_array("view_own_request", $privilege)) ? true : false;
            if($view_own_request && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.created_id', $this->user_data['emp_id']);
            }
            
            $guard = (in_array("guard_edit_to", $privilege)) ? true : false;
            if($guard){
                $this->db->where("a.status =", "Approved");
                $this->db->where("DATE(a.approved_dt)", $current_date);
            }

            if ($view_by_company) {
                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            }
            
            $this->db->group_by("a.id");

            if (isset($search) && $search) {
                $this->db->group_start();
                foreach ($filterFields1 as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
            }

            $this->db->group_by('a.id');
            $query = $this->db->get();
            $rowCount = $query->num_rows();
            return $rowCount;
        }

        private function get_all_postv1($privilege, $start, $end, $query_builder=null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $arrData = array();
            $role_id = $this->authenticate->getRoleId();
            $current_date = date("Y-m-d");

            $this->db->select("a.station, a.type, a.company, a.department, a.id, a.reference_no, a.company,a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks,a.accomplishment_dt, tod.destination, toe.firstname, toe.lastname, a.created_dt, a.accomplished");
            $this->db->from("gcceforms.travel_order a");
            $this->db->join('gcceforms.travel_destination tod', 'tod.travel_order_id = a.id', 'left');
            $this->db->join('gcceforms.travel_personnel top', 'top.travel_order_id = a.id', 'left');
            $this->db->join('gccmaster.tblemployees toe', 'toe.id = top.employee_id', 'left');
            if ($start && $end) {
                $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
                $this->db->group_start();
                $this->db->where("td.date_from >=", $start);
                $this->db->where("td.date_from <=", $end);
                $this->db->group_end();
                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("a.created_dt >=", $check);
                $this->db->group_by("a.id");
            }
            else{
                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("a.created_dt >=", $check);
            }
            if($query_builder){
                $this->db->where($query_builder);
            }



            $view_own_request = (in_array("view_own_request", $privilege)) ? true : false;
            if($view_own_request && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.created_id', $this->user_data['emp_id']);
            }

            $guard = (in_array("guard_edit_to", $privilege)) ? true : false;
            if($guard){
                $this->db->where("a.status =", "Approved");
                $this->db->where("DATE(a.approved_dt)", $current_date);
            }
     
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            if($sortOrder !== null){
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }else{
                $this->db->order_by("a.id", "DESC");
            }
            $this->db->group_by('a.id');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $rowId = $rs->id;
                    $this->db->select("firstname, lastname, middlename, suffix");
                    $this->db->from("gcceforms.travel_personnel tp");
                    $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                    $this->db->where("tp.travel_order_id", $rowId);
                    $personnel = $this->db->get();

                    $rowPersonnel = array();
                    $rowDestination = array();
                    $rowDriver = "";
                    $rowVehiclePlate = "";
                    $rowVehicleDesc = "";
                    $rowFromTo = "";

                    if ($personnel->num_rows() > 0) {
                        foreach ($personnel->result() as $key => $value) {
                            $tempRs = (array)$value;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $value->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            $rowPersonnel[] = $value->display_name;
                        }
                    }

                    if ($rs->is_service == "1") {
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();

                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $tempRs = (array)$rowData;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            if ($rowData->display_name) {
                                $rowDriver = $rowData->display_name;
                            } else {
                                $rowDriver = $rs->driver;
                            }
                        }

                        $this->db->select("plateno, description, name");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->name;
                        }
                    }


                    if ($rs->is_hitch == "1") {
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();

                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $tempRs = (array)$rowData;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            if ($rowData->display_name) {
                                $rowDriver = $rowData->display_name;
                            } else {
                                $rowDriver = $rs->driver;
                            }
                        }

                        $this->db->select("plateno, description, name");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->name;
                        }
                    }


                    $this->db->select("td.destination, td.date_from, td.date_to");
                    $this->db->from("gcceforms.travel_destination td");

                    if ($start && $end) {
                        $this->db->group_start();
                        $this->db->where("td.date_from >=", $start);
                        $this->db->where("td.date_from <=", $end);
                        $this->db->group_end();
                        $this->db->where("td.travel_order_id", $rowId);
                    } else {
                        $this->db->where("td.travel_order_id", $rowId);
                    }

                    $destination = $this->db->get();
                    $destinationDateTime = array();

                    if ($destination->num_rows() > 0) {
                        $des_num = 0;
                        foreach ($destination->result() as $key => $vx) {
                            $des_num++;
                            $rowDestination[] = $vx->destination;
                            $x = explode(' ', $vx->date_from);
                            $y = explode(' ', $vx->date_to);
                            
                            // if ($des_num == 1) {
                            //     $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                            //     $z = explode(' ', $vx->date_from);
                            // } else {
                            //     $date_start = date("M d, Y g:i A", strtotime($x[1]));
                            //     $z = explode(' ', $vx->date_from);
                            // }

                            $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                            $z = explode(' ', $vx->date_from);

                            if ($z[0] != $x[0]) {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            } else if ($x[0] == $y[0]) {
                                $date_end = date("g:i A", strtotime($y[1]));
                            } else {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            }
                            
                            $date = $date_start . "-" . $date_end;
                            array_push($destinationDateTime, $date);
                        }
                    }

                    $final  = array();

                    foreach ($destinationDateTime as $current) {
                        if ( ! in_array($current, $final)) {
                            $final[] = $current;
                        }
                    }

                    $rs->personnels = $rowPersonnel;
                    $rs->driver = $rowDriver;
                    $rs->vehicle_plate = $rowVehiclePlate;
                    $rs->vehicle_description = $rowVehicleDesc;
                    $rs->destination = $rowDestination;
                    $rs->from_to = $final;
                    $rs->company_detail = $this->getCompany($rs->company);
                    $rs->user_role_id = $this->authenticate->getRoleId();
                    $rs->created_dt = date("M d, Y h:i A", strtotime( $rs->created_dt));
                    $arrData[] = $rs;
                }
            }
            return $arrData;
        }

        private function get_all_post_countv1($privilege, $start, $end, $query_builder=null, $status = null) {
            $role_id = $this->authenticate->getRoleId();
            $current_date = date("Y-m-d");
            
            if ($start && $end) {
                $temp = strtotime("-1 year", time());
                $check = date("Y-m-d", $temp);
                $this->db->select("td.destination, td.date_from, td.date_to, tod.destination, toe.firstname, toe.lastname");
                $this->db->from("gcceforms.travel_order a");
                $this->db->join('gcceforms.travel_destination tod', 'tod.travel_order_id = a.id', 'left');
                $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
                $this->db->join('gcceforms.travel_personnel top', 'top.travel_order_id = a.id', 'left');
                $this->db->join('gccmaster.tblemployees toe', 'toe.id = top.employee_id', 'left');
                $this->db->group_start();
                $this->db->where("td.date_from >=", $start);
                $this->db->where("td.date_from <=", $end);
                $this->db->group_end();
                $this->db->group_start();
                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("a.created_dt >=", $check);
                $this->db->group_end();
                $this->db->group_by("td.travel_order_id");
            } else {
                $temp = strtotime("-1 year", time());
                $check = date("Y-m-d", $temp);
                $this->db->from("gcceforms.travel_order a");
                $this->db->join('gcceforms.travel_destination tod', 'tod.travel_order_id = a.id', 'left');
                $this->db->join('gcceforms.travel_personnel top', 'top.travel_order_id = a.id', 'left');
                $this->db->join('gccmaster.tblemployees toe', 'toe.id = top.employee_id', 'left');
                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("a.created_dt >=", $check);
            }
            if($query_builder){
                $this->db->where($query_builder);
            }

            if($status){
                $this->db->where('a.status', $status);
            }

            $view_own_request = (in_array("view_own_request", $privilege)) ? true : false;
            if($view_own_request && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.created_id', $this->user_data['emp_id']);
            }

            $guard = (in_array("guard_edit_to", $privilege)) ? true : false;
            if($guard){
                $this->db->where("a.status =", "Approved");
                $this->db->where("DATE(a.approved_dt)", $current_date);
            }
            $this->db->group_by('a.id');
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_searched_itemv1($privilege, $start, $end, $query_builder=null, $search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $status = null) {
            $filterFields1 = array("a.id", "a.status", "a.reference_no", "a.company", "a.driver", "a.others_remarks","te.lastname","te.firstname","td.des_to", 'tod.destination', 'toe.firstname', 'toe.lastname');
            $date = date("Y-m-d", strtotime("-1 year", time()));
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $arrData = array();
            $role_id = $this->authenticate->getRoleId();
            $current_date = date("Y-m-d");

            $this->db->select("a.id, a.reference_no, a.company,a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks,a.accomplishment_dt, tod.destination, toe.firstname, toe.lastname, a.created_dt, a.accomplished");
            $this->db->from("gcceforms.travel_order a");
            $this->db->join("gcceforms.travel_personnel tp","tp.travel_order_id = a.id");
            $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
            $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
            // added for querybuilder
            $this->db->join("gcceforms.travel_destination tod","tod.travel_order_id = a.id");
            $this->db->join('gcceforms.travel_personnel top', 'top.travel_order_id = a.id', 'left');
            $this->db->join('gccmaster.tblemployees toe', 'toe.id = top.employee_id', 'left');
            if ($start && $end) {
                $this->db->group_start();
                $this->db->where("td.date_from >=", $start);
                $this->db->where("td.date_from <=", $end);
                $this->db->group_end();
                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("a.created_dt >=", $check);
                $this->db->group_by("a.id");
            }else{
                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("a.created_dt >=", $check);
            }
            if($query_builder){
                $this->db->where($query_builder);
            }

            if($status){
                $this->db->where('a.status', $status);
            }

            $view_own_request = (in_array("view_own_request", $privilege)) ? true : false;
            if($view_own_request && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.created_id', $this->user_data['emp_id']);
            }

            $guard = (in_array("guard_edit_to", $privilege)) ? true : false;
            if($guard){
                $this->db->where("a.status =", "Approved");
                $this->db->where("DATE(a.approved_dt)", $current_date);
            }

            $this->db->group_by("a.id");
            if ($search) {
                $this->db->group_start();
                foreach ($filterFields1 as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            }
            $this->db->group_end();
                 
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }
            
            if($sortOrder !== null){
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }else{
                $this->db->order_by("a.id", "DESC");
            }
            $this->db->group_by('a.id');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $rowId = $rs->id;
                    $this->db->select("firstname, lastname, middlename, suffix");
                    $this->db->from("gcceforms.travel_personnel tp");
                    $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                    $this->db->where("tp.travel_order_id", $rowId);
                    $personnel = $this->db->get();

                    $rowPersonnel = array();
                    $rowDestination = array();
                    $rowDriver = "";
                    $rowVehiclePlate = "";
                    $rowVehicleDesc = "";
                    $rowFromTo = "";

                    if ($personnel->num_rows() > 0) {
                        foreach ($personnel->result() as $key => $value) {
                            $tempRs = (array)$value;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $value->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            $rowPersonnel[] = $value->display_name;
                        }
                    }

                    if ($rs->is_service == "1") {
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();

                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $tempRs = (array)$rowData;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            if ($rowData->display_name) {
                                $rowDriver = $rowData->display_name;
                            } else {
                                $rowDriver = $rs->driver;
                            }
                        }

                        $this->db->select("plateno, description, name");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->name;
                        }
                    }

                    $this->db->select("td.destination, td.date_from, td.date_to");
                    $this->db->from("gcceforms.travel_destination td");
                    if ($start && $end) {
                        $this->db->group_start();
                        $this->db->where("td.date_from >=", $start);
                        $this->db->where("td.date_from <=", $end);
                        $this->db->group_end();
                        $this->db->where("td.travel_order_id", $rowId);
                    } else {
                        $this->db->where("td.travel_order_id", $rowId);
                    }
                    $destination = $this->db->get();
                    $destinationDateTime = array();

                    if ($destination->num_rows() > 0) {
                        $des_num = 0;
                        foreach ($destination->result() as $key => $vx) {
                            $des_num++;
                            $rowDestination[] = $vx->destination;

                            $x = explode(' ', $vx->date_from);
                            $y = explode(' ', $vx->date_to);
                            if ($des_num == 1) {
                                $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                                $z = explode(' ', $vx->date_from);
                            }

                            if ($z[0] != $x[0]) {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            } else if ($x[0] == $y[0]) {
                                $date_end = date("g:i A", strtotime($y[1]));
                            } else {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            }

                            $date = $date_start . "-" . $date_end;
                            array_push($destinationDateTime, $date);
                        }
                    } else {
                        return array();
                    }
                    $rs->personnels = $rowPersonnel;
                    $rs->driver = $rowDriver;
                    $rs->vehicle_plate = $rowVehiclePlate;
                    $rs->vehicle_description = $rowVehicleDesc;
                    $rs->destination = $rowDestination;
                    $rs->from_to = $destinationDateTime;
                    $rs->company_detail = $this->getCompany($rs->company);
                    $rs->user_role_id = $this->authenticate->getRoleId();
                    $rs->created_dt = date("m-d-Y h:i A", strtotime( $rs->created_dt));
                    $arrData[] = $rs;
                }
            }
            return $arrData;
        }

        private function get_searched_item_countv1($privilege, $start, $end, $query_builder=null, $search = null, $status = null) {
            $role_id = $this->authenticate->getRoleId();
            $current_date = date("Y-m-d");

            $filterFields1 = array("a.id", "a.status", "a.reference_no", "a.company", "a.driver", "a.others_remarks","te.lastname","te.firstname","td.des_from","td.des_to", "tod.destination", "toe.firstname", "toe.lastname");
            $date = date("Y-m-d", strtotime("-1 year", time()));
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $arrData = array();
            $this->db->select("a.id, a.reference_no, a.company,a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks,a.accomplishment_dt, tod.destination, toe.firstname, toe.lastname");
            $this->db->from("gcceforms.travel_order a");
            $this->db->join("gcceforms.travel_personnel tp","tp.travel_order_id = a.id");
            $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
            $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
            // added for querybuilder
            $this->db->join("gcceforms.travel_destination tod","tod.travel_order_id = a.id");
            $this->db->join('gcceforms.travel_personnel top', 'top.travel_order_id = a.id', 'left');
            $this->db->join('gccmaster.tblemployees toe', 'toe.id = top.employee_id', 'left');
            if ($start && $end) {
                $this->db->group_start();
                $this->db->where("td.date_from >=", $start);
                $this->db->where("td.date_from <=", $end);
                $this->db->group_end();
                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("a.created_dt >=", $check);
                $this->db->group_by("a.id");
            }else{
                $this->db->where("a.status !=", "Cancelled");
                $this->db->where("a.created_dt >=", $check);
            }
            if($query_builder){
                $this->db->where($query_builder);
            }

            if($status){
                $this->db->where('a.status', $status);
            }

            $view_own_request = (in_array("view_own_request", $privilege)) ? true : false;
            if($view_own_request && ($this->user_data['emp_id']!=1)){
                $this->db->where('a.created_id', $this->user_data['emp_id']);
            }
            
            $guard = (in_array("guard_edit_to", $privilege)) ? true : false;
            if($guard){
                $this->db->where("a.status =", "Approved");
                $this->db->where("DATE(a.approved_dt)", $current_date);
            }
            
            $this->db->group_by("a.id");
            if ($search) {
                $this->db->group_start();
                foreach ($filterFields1 as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
            }
            $this->db->group_end();
            $this->db->group_by('a.id');
            $query = $this->db->get();
            $rowCount = $query->num_rows();
            return $rowCount;
        }

        function getCompany($company) {
            $this->db->select('description');
            $this->db->from('gcchris.tblcompanies');
            $this->db->where('id', $company);
            $query = $this->db->get();
            // return $query->row_array()['description'];

            return is_array($query->row_array()) && iseet($query->row_array()['description']) ? $query->row_array()['description'] : "No Data Found!";
        }

        /**function getFilteredPostTo($arrIds = array()) { } */ 

        function driver() {
            $get = $this->input->get();
            $resultarray = array();
            // if (isset($get['q'])) {
            //     $query = $this->db->query("SELECT  e.id, e.firstname, e.middlename, e.lastname, e.suffix, e.position, c.description as company FROM gccmaster.tblemployees as e LEFT JOIN gcchris.tblcompanies as c ON c.id=e.company_id WHERE  e.employee_status='Active' AND (e.firstname LIKE '%{$get['q']}%' OR e.lastname LIKE '%{$get['q']}%' OR c.description LIKE '%{$get['q']}%') ORDER BY e.firstname ASC");
            // } else {
            //     $query = $this->db->query("SELECT  e.id, e.firstname, e.middlename, e.lastname, e.suffix, e.position, c.description as company FROM gccmaster.tblemployees as e LEFT JOIN gcchris.tblcompanies as c ON c.id=e.company_id WHERE e.employee_status='Active' ORDER BY e.firstname ASC");
            // }

            $sql = "e.id, e.firstname, e.middlename, e.lastname, e.suffix, e.position, c.description as company";

            $this->db->select($sql);
            $this->db->join('gcchris.tblcompanies as c', 'c.id=e.company_id OR c.id = c.description', 'left');
            $this->db->where("employee_status", "Active");

            if(isset($get['q']) && $get['q']){
                $this->db->like('e.firstname', $get['q'], 'both');
                $this->db->or_like('e.lastname', $get['q'], 'both');
                $this->db->or_like('c.description', $get['q'], 'both');
            }

            $this->db->from("gccmaster.tblemployees as e");
            $this->db->order_by("e.firstname", 'ASC');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
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
                    if (strpos($driver, "DRIVER") !== false || strpos($driver, 'BACKHOE') !== false || strpos($driver, 'TRACTOR') !== false || strpos($driver, 'GRADER') !== false || strpos($driver, 'ROLLER') !== false || strpos($driver, 'PAYLOADER') !== false || strpos($driver, 'BULLDOZER') !== false || strpos($driver, 'CRANE') !== false || strpos($driver, 'MCC') !== false || strpos($driver, 'OPERATOR') !== false) {
                        $data = array();
                        $tempRs = (array)$_query;
                        $fullname = $this->core_layout->getDisplayName($tempRs);
                        $tempFullname = (object)$fullname;
                        $tempCompany = $_query['company'];

                        $data["id"] = $_query["id"];
                        $data["text"] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1." - ".$tempCompany : "No Assigned Name";
                        $resultarray[] = $data;
                    }
                }
            }
            return array("results" => $resultarray);
        }

        function getArchiveLists() {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "6", "dir" => "desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $begin = (isset($post["start_date"]) && $post["start_date"]) ? $post["start_date"] : false;
            $end = (isset($post["end_date"]) && $post["end_date"]) ? $post["end_date"] : false;

            $privilege = $this->core_layout->getCurrentActions();

            $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;
            $companyDescription = null;

            if ($view_by_company) {
                $companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
            }

            $rowData = $this->get_all_archive($search, $limit, $offset, $sortBy, $sortOrder, $begin, $end, $view_by_company, $companyDescription);
            $rowCount = $this->get_all_archive_count($search, $begin, $end, $view_by_company, $companyDescription);
            // if (!$search) {
            //     $rowData = $this->get_all_archive($limit, $offset, $sortBy, $sortOrder);
            //     $rowCount = $this->get_all_archive_count();
            // }

            // if ($search) {
            //     $rowData = $this->get_searched_archive_item($search, $limit, $offset, $sortBy, $sortOrder);
            //     $rowCount = $this->get_searched_archive_count($search);
            // }

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        public function get_all_archive($search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder, $begin = null, $end = null, $view_by_company = false, $companyDescription = null) {
            $filterFields = array("a.id", "a.status", "a.reference_no", "a.company", "a.driver");
            $date = date("Y-m-d", strtotime("-1 year", time()));
            $check = date("Y-m-d", strtotime("-1 year", time()));
            $arrData = array();

            $sql = "a.id, a.reference_no, a.company,a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks, a.accomplishment_dt";
            
            $this->db->select($sql);
            $this->db->from("gcceforms.travel_order a");

            $this->db->group_start();
            $this->db->where('a.status', 'Cancelled');
            $this->db->or_where('DATE(a.created_dt) <=', $check);
            $this->db->group_end();

            if ($view_by_company) {
                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            }

            if($begin && $end) {
                $this->db->select("td.destination, td.date_from, td.date_to");
                $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
        
                $this->db->group_start();
                    $this->db->where("DATE(td.date_from) >=", $begin);
                    $this->db->where("DATE(td.date_from) <=", $end);
                $this->db->group_end();
        
                $this->db->group_by("td.travel_order_id");
            }

            if (isset($search) && $search) {
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

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $rowId = $rs->id;
                    $this->db->select("firstname, lastname, middlename, suffix");
                    $this->db->from("gcceforms.travel_personnel tp");
                    $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                    $this->db->where("tp.travel_order_id", $rowId);
                    $personnel = $this->db->get();
    
                    $rowPersonnel = array();
                    $rowDestination = array();
                    $rowDriver = "";
                    $rowVehiclePlate = "";
                    $rowVehicleDesc = "";
                    $rowFromTo = "";
    
                    if ($personnel->num_rows() > 0) {
                        foreach ($personnel->result() as $key => $value) {
                            $tempRs = (array)$value;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $value->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            $rowPersonnel[] = $value->display_name;
                        }
                    }
    
                    if ($rs->is_service == "1") {
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();
    
                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $tempRs = (array)$rowData;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            if ($rowData->display_name) {
                                $rowDriver = $rowData->display_name;
                            } else {
                                $rowDriver = $rs->driver;
                            }
                        }
    
                        $this->db->select("plateno, description");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->description;
                        }
                    }
    
                    $this->db->select("td.destination, td.date_from, td.date_to");
                    $this->db->from("gcceforms.travel_destination td");
                    $this->db->where("td.travel_order_id", $rowId);
                    $destination = $this->db->get();
    
                    if ($destination->num_rows() > 0) {
                        $des_num = 0;
                        foreach ($destination->result() as $key => $vx) {
                            $des_num++;
                            $rowDestination[] = $vx->destination;
    
                            $x = explode(' ', $vx->date_from);
                            $y = explode(' ', $vx->date_to);
                            if ($des_num == 1) {
                                $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                                $z = explode(' ', $vx->date_from);
                            }
    
                            if ($z[0] != $x[0]) {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            } else if ($x[0] == $y[0]) {
                                $date_end = date("g:i A", strtotime($y[1]));
                            } else {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            }
    
                            $rowFromTo = "{$date_start} - {$date_end}";
                        }
                    }
                    $rs->personnels = $rowPersonnel;
                    $rs->driver = $rowDriver;
                    $rs->vehicle_plate = $rowVehiclePlate;
                    $rs->vehicle_description = $rowVehicleDesc;
                    $rs->destination = $rowDestination;
                    $rs->from_to = $rowFromTo;
                    $rs->company_detail = $this->getCompany($rs->company);
                    $arrData[] = $rs;
                }
            }
    
            return $arrData;
        }

        public function get_all_archive_count($search = null, $begin = null, $end = null, $view_by_company = false, $companyDescription = null) {
            $filterFields = array("a.id", "a.status", "a.reference_no", "a.company", "a.driver");
            $date = date("Y-m-d", strtotime("-1 year", time()));
            $check = date("Y-m-d", strtotime("-1 year", time()));

            $sql = "a.id, a.reference_no, a.company,a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks, a.accomplishment_dt";
            
            $this->db->select($sql);
            $this->db->from("gcceforms.travel_order a");
            
            $this->db->group_start();
            $this->db->where('a.status', 'Cancelled');
            $this->db->or_where('DATE(a.created_dt) <=', $check);
            $this->db->group_end();

            if ($view_by_company) {
                if ($companyDescription) {
                    $this->db->where('a.company', $companyDescription);
                }
            }

            if($begin && $end) {
                $this->db->select("td.destination, td.date_from, td.date_to");
                $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
        
                $this->db->group_start();
                    $this->db->where("DATE(td.date_from) >=", $begin);
                    $this->db->where("DATE(td.date_from) <=", $end);
                $this->db->group_end();
        
                $this->db->group_by("td.travel_order_id");
            }

            if (isset($search) && $search) {
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

        private function get_all_archivev1($limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $arrData = array();
            $this->db->select("a.id, a.reference_no, a.company,a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks,a.accomplishment_dt");
            $this->db->from("gcceforms.travel_order a");
            $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
              
            if($limit != -1){
                $this->db->limit($limit, $offset);
            }

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $rowId = $rs->id;
                    $this->db->select("firstname, lastname, middlename, suffix");
                    $this->db->from("gcceforms.travel_personnel tp");
                    $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                    $this->db->where("tp.travel_order_id", $rowId);
                    $personnel = $this->db->get();

                    $rowPersonnel = array();
                    $rowDestination = array();
                    $rowDriver = "";
                    $rowVehiclePlate = "";
                    $rowVehicleDesc = "";
                    $rowFromTo = "";

                    if ($personnel->num_rows() > 0) {
                        foreach ($personnel->result() as $key => $value) {
                            $tempRs = (array)$value;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $value->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            $rowPersonnel[] = $value->display_name;
                        }
                    }

                    if ($rs->is_service == "1") {
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();

                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $tempRs = (array)$rowData;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            if ($rowData->display_name) {
                                $rowDriver = $rowData->display_name;
                            } else {
                                $rowDriver = $rs->driver;
                            }
                        }

                        $this->db->select("plateno, description");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->description;
                        }
                    }

                    $this->db->select("td.destination, td.date_from, td.date_to");
                    $this->db->from("gcceforms.travel_destination td");
                    $this->db->where("td.travel_order_id", $rowId);
                    $destination = $this->db->get();

                    if ($destination->num_rows() > 0) {
                        $des_num = 0;
                        foreach ($destination->result() as $key => $vx) {
                            $des_num++;
                            $rowDestination[] = $vx->destination;

                            $x = explode(' ', $vx->date_from);
                            $y = explode(' ', $vx->date_to);
                            if ($des_num == 1) {
                                $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                                $z = explode(' ', $vx->date_from);
                            }

                            if ($z[0] != $x[0]) {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            } else if ($x[0] == $y[0]) {
                                $date_end = date("g:i A", strtotime($y[1]));
                            } else {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            }

                            $rowFromTo = "{$date_start} - {$date_end}";
                        }
                    }
                    $rs->personnels = $rowPersonnel;
                    $rs->driver = $rowDriver;
                    $rs->vehicle_plate = $rowVehiclePlate;
                    $rs->vehicle_description = $rowVehicleDesc;
                    $rs->destination = $rowDestination;
                    $rs->from_to = $rowFromTo;
                    $rs->company_detail = $this->getCompany($rs->company);
                    $arrData[] = $rs;
                }
            }
            return $arrData;
        }

        private function get_all_archive_countv1() {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $this->db->from("gcceforms.travel_order a");
            $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_searched_archive_itemv1($search = null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $filterFields = array("a.id", "a.status", "a.reference_no", "a.company", "a.driver");
            $date = date("Y-m-d", strtotime("-1 year", time()));
            if ($search) {
                $temp = strtotime("-1 year", time());
                $check = date("Y-m-d", $temp);
                $arrData = array();
                $this->db->select("a.id, a.reference_no, a.company,a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks,a.accomplishment_dt");
                $this->db->from("gcceforms.travel_order a");
                $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
                    
                if($limit != -1){
                    $this->db->limit($limit, $offset);
                }
                
                $i = $sortOrder[0]['column'];
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $rs) {
                        $rowId = $rs->id;
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gcceforms.travel_personnel tp");
                        $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                        $this->db->where("tp.travel_order_id", $rowId);
                        $personnel = $this->db->get();

                        $rowPersonnel = array();
                        $rowDestination = array();
                        $rowDriver = "";
                        $rowVehiclePlate = "";
                        $rowVehicleDesc = "";
                        $rowFromTo = "";

                        if ($personnel->num_rows() > 0) {
                            foreach ($personnel->result() as $key => $value) {
                                $tempRs = (array)$value;
                                $fullname = $this->core_layout->getDisplayName($tempRs);
                                $tempFullname = (object)$fullname;
                                $value->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                                $rowPersonnel[] = $value->display_name;
                            }
                        }

                        if ($rs->is_service == "1") {
                            $this->db->select("firstname, lastname, middlename, suffix");
                            $this->db->from("gccmaster.tblemployees te");
                            $this->db->where("te.id", $rs->driver_id);
                            $driver = $this->db->get();

                            if ($driver->num_rows() == 1) {
                                $rowData = $driver->row();
                                $tempRs = (array)$rowData;
                                $fullname = $this->core_layout->getDisplayName($tempRs);
                                $tempFullname = (object)$fullname;
                                $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                                if ($rowData->display_name) {
                                    $rowDriver = $rowData->display_name;
                                } else {
                                    $rowDriver = $rs->driver;
                                }
                            }

                            $this->db->select("plateno, description");
                            $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                            if ($queryVehicle->num_rows() == 1) {
                                $rowVehicle = $queryVehicle->row();
                                $rowVehiclePlate = $rowVehicle->plateno;
                                $rowVehicleDesc = $rowVehicle->description;
                            }
                        }

                        $this->db->select("td.destination, td.date_from, td.date_to");
                        $this->db->from("gcceforms.travel_destination td");
                        $this->db->where("td.travel_order_id", $rowId);
                        $destination = $this->db->get();

                        if ($destination->num_rows() > 0) {
                            $des_num = 0;
                            foreach ($destination->result() as $key => $vx) {
                                $des_num++;
                                $rowDestination[] = $vx->destination;

                                $x = explode(' ', $vx->date_from);
                                $y = explode(' ', $vx->date_to);
                                if ($des_num == 1) {
                                    $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                                    $z = explode(' ', $vx->date_from);
                                }

                                if ($z[0] != $x[0]) {
                                    $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                                } else if ($x[0] == $y[0]) {
                                    $date_end = date("g:i A", strtotime($y[1]));
                                } else {
                                    $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                                }

                                $rowFromTo = "{$date_start} - {$date_end}";
                            }
                        }
                        $rs->personnels = $rowPersonnel;
                        $rs->driver = $rowDriver;
                        $rs->vehicle_plate = $rowVehiclePlate;
                        $rs->vehicle_description = $rowVehicleDesc;
                        $rs->destination = $rowDestination;
                        $rs->from_to = $rowFromTo;
                        $rs->company_detail = $this->getCompany($rs->company);
                        $arrData[] = $rs;
                    }
                }

                return $arrData;
            }
        }

        private function get_searched_archive_countv1($search = null) {
            $date = date("Y-m-d", strtotime("-1 year", time()));
            $rowCount = 0;
            if ($search) {
                $sql = "a.id, a.status, a.reference_no, b.firstname, b.middlename, b.lastname, b.suffix, a.amt_applied, a.purpose, a.amt_approved, a.created_dt, a.approved_dt";
                $filterFields = array("a.id", "a.status", "a.reference_no", "b.firstname", "b.lastname", "a.amt_applied", "a.purpose", "a.amt_approved", "a.created_dt", "a.approved_dt");
                $this->db->select($sql);
                $this->db->from("gcceforms.cash_advance a");
                $this->db->join("gccmaster.tblemployees b", "a.employee = b.id", "LEFT");
                $this->db->where("a.created_dt >=", $date);
                $this->db->where("a.status !=", "Cancelled");

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

        function getArchiveList() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("a.status", "a.reference_no", "a.company");
                $dir = "DESC";
                $order = "a.id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }
        
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $begin = (isset($post["start_date"]) && $post["start_date"]) ? $post["start_date"] : false;
                $end = (isset($post["end_date"]) && $post["end_date"]) ? $post["end_date"] : false;
        
                $totalData = $this->getAllPostToCountArchive($begin, $end);
                $totalFiltered = $totalData;
                
                if (empty($searchValue)) {
                    $posts = $this->getAllPostToArchive($begin, $end, $limit, $start, $order, $dir);
                } else {
                    $ids = $this->dtPostToSearchArchive($begin, $end, $limit, $start, $searchValue, $order, $dir);
                    $posts = $this->getFilteredPostToArchive($begin, $end, $ids);
                    $totalFiltered = $this->dtPostToSearchCountArchive($begin, $end, $searchValue);
                }
        
                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData = array();
                        $nestedData['id'] = $pst->id;
                        $nestedData['status'] = $pst->status;
                        $nestedData['reference_no'] = $pst->reference_no;
                        $nestedData['company'] = $pst->company;
                        $nestedData['driver'] = $pst->driver;
                        $nestedData['vehicle_plate'] = $pst->vehicle_plate;
                        $nestedData['vehicle_description'] = $pst->vehicle_description;
                        $nestedData['accomplishment_dt'] = $pst->accomplishment_dt;
                        $nestedData['is_service'] = $pst->is_service;
                        $nestedData['is_commute'] = $pst->is_commute;
                        $nestedData['is_personal'] = $pst->is_personal;
                        $nestedData['is_others'] = $pst->is_others;
                        $nestedData['others_remarks'] = $pst->others_remarks;
                        $nestedData['personnels'] = $pst->personnels;
                        $nestedData['destination'] = $pst->destination;
                        $nestedData['from_to'] = $pst->from_to;
                        $data[] = $nestedData;
                    }
                }
                $json_data = array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
                );
                return $json_data;
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array()
                );
            }
            
        }

        function getArchiveListv1() {
            $post = $this->input->post();
            $json_data = array();

            $columns = array("a.status", "a.reference_no", "a.company");
            $dir = "DESC";
            $order = "a.id";
            if (isset($post["order"]) && $post["order"]) {
                $dir = $post["order"][0]["dir"];
                $order = $columns[$post["order"][0]["column"]];
            }

            $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
            $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
            $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
            $begin = (isset($post["start_date"]) && $post["start_date"]) ? $post["start_date"] : false;
            $end = (isset($post["end_date"]) && $post["end_date"]) ? $post["end_date"] : false;

            $totalData = $this->getAllPostToCountArchive($begin, $end);
            $totalFiltered = $totalData;
            
            if (empty($searchValue)) {
                $posts = $this->getAllPostToArchive($begin, $end, $limit, $start, $order, $dir);
            } else {
                $ids = $this->dtPostToSearchArchive($begin, $end, $limit, $start, $searchValue, $order, $dir);
                $posts = $this->getFilteredPostToArchive($begin, $end, $ids);
                $totalFiltered = $this->dtPostToSearchCountArchive($begin, $end, $searchValue, $ids);
            }

            $data = array();
            if (!empty($posts)) {
                foreach ($posts as $pst) {
                    $nestedData = array();
                    $nestedData['id'] = $pst->id;
                    $nestedData['status'] = $pst->status;
                    $nestedData['reference_no'] = $pst->reference_no;
                    $nestedData['company'] = $pst->company;
                    $nestedData['driver'] = $pst->driver;
                    $nestedData['vehicle_plate'] = $pst->vehicle_plate;
                    $nestedData['vehicle_description'] = $pst->vehicle_description;
                    $nestedData['accomplishment_dt'] = $pst->accomplishment_dt;
                    $nestedData['is_service'] = $pst->is_service;
                    $nestedData['is_commute'] = $pst->is_commute;
                    $nestedData['is_personal'] = $pst->is_personal;
                    $nestedData['is_others'] = $pst->is_others;
                    $nestedData['others_remarks'] = $pst->others_remarks;
                    $nestedData['personnels'] = $pst->personnels;
                    $nestedData['destination'] = $pst->destination;
                    $nestedData['from_to'] = $pst->from_to;
                    $data[] = $nestedData;
                }
            }
            
            return $json_data = array(
                'draw' => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );
        }

        function getAllPostToCountArchive($begin=false, $end=false) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);

            // if ($begin && $end) {
            //     $temp = strtotime("-1 year", time());
            //     $check = date("Y-m-d", $temp);
            //     $this->db->select("td.destination, td.date_from, td.date_to");
            //     $this->db->from("gcceforms.travel_order a");
            //     $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
            //     $this->db->group_start();
            //     $this->db->where("td.date_from >=", $begin);
            //     $this->db->where("td.date_from <=", $end);
            //     $this->db->group_end();
            //     $this->db->group_start();
            //     $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
            //     $this->db->group_end();
            //     $this->db->group_by("td.travel_order_id");
            // } else {
            //     $temp = strtotime("-1 year", time());
            //     $check = date("Y-m-d", $temp);
            //     $this->db->from("gcceforms.travel_order a");
            //     $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
            // }

            if($begin && $end) {
                $this->db->select("td.destination, td.date_from, td.date_to");
                $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");

                $this->db->group_start();
                    $this->db->where("DATE(td.date_from) >=", $begin);
                    $this->db->where("DATE(td.date_from) <=", $end);
                $this->db->group_end();

                $this->db->group_by("td.travel_order_id");
            }
            
            $this->db->group_start();
                $this->db->where('a.status', 'Cancelled');
                $this->db->or_where('DATE(a.created_dt)', $check);
            $this->db->group_end();

            $this->db->from("gcceforms.travel_order a");
            $query = $this->db->get();
            return $query->num_rows();
        }

        function dtPostToSearchCountArchive($begin=false, $end=false, $searchValue = null, $arrIds = array()) {
            $check = date("Y-m-d", strtotime("-1 year", time()));

            $this->db->select("a.id");
            $this->db->from("gcceforms.travel_order a");
            $this->db->join("gcceforms.travel_personnel b", "b.travel_order_id = a.id", "left");
            $this->db->join("gcceforms.travel_destination c", "c.travel_order_id = a.id", "left");
            $this->db->join("gccmaster.tblemployees d", "d.id = a.driver_id", "left");
            $this->db->join("gccmaster.tblemployees e", "e.id = b.employee_id", "left");

            if (!empty($arrIds)) {
                $this->db->where_in("a.id", $arrIds);
            }

            if ($begin && $end) {
                $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
                $this->db->group_start();
                $this->db->where("DATE(td.date_from) >=", $begin);
                $this->db->where("DATE(td.date_from) <=", $end);
                $this->db->group_end();
                // $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
                $this->db->group_by("a.id");
            }
            // else{
            //     $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
            // }

            $this->db->where('a.status', 'Canelled');
            $this->db->where('DATE(a.created_dt) <= ', $check);
            // $this->db->group_start();
            // $this->db->like("a.status", $searchValue, "both");
            // $this->db->or_like("a.reference_no", $searchValue, "both");
            // $this->db->or_like("a.company", $searchValue, "both");
            // $this->db->group_end();
            $this->db->group_by("a.id");

            $query = $this->db->get();
            return $query->num_rows();
        }

        function dtPostToSearchArchive($begin=false, $end=false, $limit = 10, $start = 0, $searchValue = null, $order = "a.id", $dir = "DESC") {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $arrIds = array();
            $this->db->select("a.id");
            $this->db->from("gcceforms.travel_order a");
            $this->db->join("gcceforms.travel_personnel b", "b.travel_order_id = a.id", "left");
            $this->db->join("gcceforms.travel_destination c", "c.travel_order_id = a.id", "left");
            $this->db->join("gccmaster.tblemployees d", "d.id = a.driver_id", "left");
            $this->db->join("gccmaster.tblemployees e", "e.id = b.employee_id", "left");
            if ($begin && $end) {
                $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
                $this->db->group_start();
                $this->db->where("td.date_from >=", $begin);
                $this->db->where("td.date_from <=", $end);
                $this->db->group_end();
                $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
                $this->db->group_by("a.id");
            }else{
                $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
            }
            $this->db->group_start();
            $this->db->like("a.status", $searchValue, "both");
            $this->db->or_like("a.reference_no", $searchValue, "both");
            $this->db->or_like("a.company", $searchValue, "both");
            $this->db->or_like("d.lastname", $searchValue, "both");
            $this->db->or_like("d.firstname", $searchValue, "both");
            $this->db->or_like("e.lastname", $searchValue, "both");
            $this->db->or_like("e.firstname", $searchValue, "both");
            $this->db->group_end();
            if($limit != -1){
                $this->db->limit($limit, $start);
            }
            $this->db->group_by("a.id");
            $this->db->order_by($order, $dir);

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $key => $value) {
                    $arrIds[] = intval($value->id);
                }
            }

            return $arrIds;
        }

        function getAllPostToArchive($begin=false, $end=false, $limit = 10, $start = 0, $order = "a.id", $dir = "DESC") {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $arrData = array();
            $this->db->select("a.id, a.reference_no, a.company, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks, a.accomplishment_dt");
            $this->db->from("gcceforms.travel_order a");

            if ($begin && $end) {
                $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
                $this->db->group_start();
                $this->db->where("DATE(td.date_from) >=", $begin);
                $this->db->where("DATE(td.date_from) <=", $end);
                $this->db->group_end();
                // $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
                $this->db->group_by("a.id");
            }
            // else{
            //     $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
            // }

            $this->db->group_start();
            $this->db->where('a.status', 'Canelled');
            $this->db->where('DATE(a.created_dt) <= ', $check);
            $this->db->group_end();

            if($limit != -1){
                $this->db->limit($limit, $start);
            }
            
            $this->db->order_by($order, $dir);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $rowId = $rs->id;
                    $this->db->select("firstname, lastname, middlename, suffix");
                    $this->db->from("gcceforms.travel_personnel tp");
                    $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                    $this->db->where("tp.travel_order_id", $rowId);
                    $personnel = $this->db->get();

                    $rowPersonnel = array();
                    $rowDestination = array();
                    $rowDriver = "";
                    $rowVehiclePlate = "";
                    $rowVehicleDesc = "";
                    $rowFromTo = "";


                    if ($personnel->num_rows() > 0) {
                        foreach ($personnel->result() as $key => $value) {
                            $tempRs = (array)$value;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $value->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            $rowPersonnel[] = $value->display_name;
                        }
                    }

                    if ($rs->is_service == "1") {
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();

                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $tempRs = (array)$rowData;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            if ($rowData->display_name) {
                                $rowDriver = $rowData->display_name;
                            } else {
                                $rowDriver = $rs->driver;
                            }
                        }

                        $this->db->select("plateno, description");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->description;
                        }
                    }

                    $this->db->select("td.destination, td.date_from, td.date_to");
                    $this->db->from("gcceforms.travel_destination td");
                    $this->db->where("td.travel_order_id", $rowId);
                    $destination = $this->db->get();

                    if ($destination->num_rows() > 0) {
                        $des_num = 0;
                        foreach ($destination->result() as $key => $vx) {
                            $des_num++;
                            $rowDestination[] = $vx->destination;

                            $x = explode(' ', $vx->date_from);
                            $y = explode(' ', $vx->date_to);
                            if ($des_num == 1) {
                                $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                                $z = explode(' ', $vx->date_from);
                            }

                            if ($z[0] != $x[0]) {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            } else if ($x[0] == $y[0]) {
                                $date_end = date("g:i A", strtotime($y[1]));
                            } else {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            }

                            $rowFromTo = "{$date_start} - {$date_end}";
                        }
                    }

                    $rs->personnels = $rowPersonnel;
                    $rs->driver = $rowDriver;
                    $rs->vehicle_plate = $rowVehiclePlate;
                    $rs->vehicle_description = $rowVehicleDesc;
                    $rs->destination = $rowDestination;
                    $rs->from_to = $rowFromTo;

                    $arrData[] = $rs;
                }
            }

            return $arrData;
        }

        function getFilteredPostToArchive($begin=false, $end=false, $arrIds = array()) {
            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);
            $arrData = array();
            if ($arrIds) {
                $this->db->select("a.id, a.reference_no, a.company, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks, a.accomplishment_dt");
                $this->db->from("gcceforms.travel_order a");
                $this->db->where_in("a.id", $arrIds);
                if ($begin && $end) {
                    $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
                    $this->db->group_start();
                    $this->db->where("DATE(td.date_from) >=", $begin);
                    $this->db->where("DATE(td.date_from) <=", $end);
                    $this->db->group_end();
                    // $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
                    $this->db->group_by("a.id");
                }
                // else{
                //     $this->db->where("(a.status='Cancelled' OR a.created_dt<='$check')");
                // }

                $this->db->where('a.status', 'Canelled');
                $this->db->where('DATE(a.created_dt) <= ', $check);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $rs) {
                        $rowId = $rs->id;
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gcceforms.travel_personnel tp");
                        $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                        $this->db->where("tp.travel_order_id", $rowId);
                        $personnel = $this->db->get();

                        $rowPersonnel = array();
                        $rowDestination = array();
                        $rowDriver = "";
                        $rowVehiclePlate = "";
                        $rowVehicleDesc = "";
                        $rowFromTo = "";


                        if ($personnel->num_rows() > 0) {
                            foreach ($personnel->result() as $key => $value) {
                                $tempRs = (array)$value;
                                $fullname = $this->core_layout->getDisplayName($tempRs);
                                $tempFullname = (object)$fullname;
                                $value->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                                $rowPersonnel[] = $value->display_name;
                            }
                        }

                        if ($rs->is_service == "1") {
                            $this->db->select("firstname, lastname, middlename, suffix");
                            $this->db->from("gccmaster.tblemployees te");
                            $this->db->where("te.id", $rs->driver_id);
                            $driver = $this->db->get();

                            if ($driver->num_rows() == 1) {
                                $rowData = $driver->row();
                                $tempRs = (array)$rowData;
                                $fullname = $this->core_layout->getDisplayName($tempRs);
                                $tempFullname = (object)$fullname;
                                $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                                if ($rowData->display_name) {
                                    $rowDriver = $rowData->display_name;
                                } else {
                                    $rowDriver = $rs->driver;
                                }
                            }

                            $this->db->select("plateno, description");
                            $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                            if ($queryVehicle->num_rows() == 1) {
                                $rowVehicle = $queryVehicle->row();
                                $rowVehiclePlate = $rowVehicle->plateno;
                                $rowVehicleDesc = $rowVehicle->description;
                            }
                        }

                        $this->db->select("td.destination, td.date_from, td.date_to");
                        $this->db->from("gcceforms.travel_destination td");
                        $this->db->where("td.travel_order_id", $rowId);
                        $destination = $this->db->get();

                        if ($destination->num_rows() > 0) {
                            $des_num = 0;
                            foreach ($destination->result() as $key => $vx) {
                                $des_num++;
                                $rowDestination[] = $vx->destination;

                                $x = explode(' ', $vx->date_from);
                                $y = explode(' ', $vx->date_to);
                                if ($des_num == 1) {
                                    $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                                    $z = explode(' ', $vx->date_from);
                                }

                                if ($z[0] != $x[0]) {
                                    $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                                } else if ($x[0] == $y[0]) {
                                    $date_end = date("g:i A", strtotime($y[1]));
                                } else {
                                    $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                                }

                                $rowFromTo = "{$date_start} - {$date_end}";
                            }
                        }

                        $rs->personnels = $rowPersonnel;
                        $rs->driver = $rowDriver;
                        $rs->vehicle_plate = $rowVehiclePlate;
                        $rs->vehicle_description = $rowVehicleDesc;
                        $rs->destination = $rowDestination;
                        $rs->from_to = $rowFromTo;

                        $arrData[] = $rs;
                    }
                }
            }

            return $arrData;
        }

        function getTempDestination($id) {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "1", "dir" => "desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $rowData = array();

            $rowData = $this->get_all_destination_temp($limit, $offset, $sortBy, $sortOrder, $id);

            $resultset["data"] = $rowData;

            return $resultset;
        }


        private function get_all_destination_temp($limit = 10, $offset = 0, $sortBy, $sortOrder, $id) {
            $sql = "a.id, a.instructions,a.date_from,a.date_to,CONCAT('<b>',a.des_from,'-',a.des_to,'</b><br>',a.purpose,'<br>Remarks:',a.remarks) AS destination, b.firstname, b.middlename, b.lastname, b.suffix";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_destination_temp a");
            $this->db->join("gccmaster.tblemployees b", "a.requested_by = b.id", "LEFT");
            $this->db->where('user_id', $id);
            $this->db->limit($limit, $offset);

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $rs->destination = $rs->destination . '<br>Req By: ' . $rs->display_name;
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

        function getTempPersonnel($id) {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "0", "dir" => "asc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $rowCount = 0;
            $rowData = array();
            $rowData = $this->get_all_personnel_temp($limit, $offset, $sortBy, $sortOrder, $id);
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_personnel_temp($limit = 10, $offset = 0, $sortBy, $sortOrder, $id) {
            $sql = "a.id, IFNULL(c.name, IFNULL(b.position, '---')) as position, b.firstname, b.middlename, b.lastname, b.suffix";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_personnel_temp a");
            $this->db->join("gccmaster.tblemployees b", "a.employee_id = b.id", "LEFT");
            $this->db->join("gcchris.tblposition c", "c.id = b.position", "LEFT");
            $this->db->where('user_id', $id);
            $this->db->limit($limit, $offset);

            $i = $sortOrder[0]['column'];
            $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
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

        function getDestination($id) {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "0", "dir" => "desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;

            $rowData = array();

            $rowData = $this->get_all_destination($limit, $offset, $sortBy, $sortOrder, $id);

            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_destination($limit = 10, $offset = 0, $sortBy, $sortOrder, $id) {
            $sql = "a.id, a.instructions,a.date_from,a.date_to,CONCAT('<b>',a.des_from,'-',a.des_to,'</b><br>',a.purpose,'<br>Remarks:',a.remarks) AS destination, a.travel_order_status, b.firstname, b.middlename, b.lastname, b.suffix, a.accomplished, c.status";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_destination a");
            $this->db->join("gccmaster.tblemployees b", "a.requested_by = b.id", "LEFT");
            $this->db->join("gcceforms.travel_order c", "a.travel_order_id = c.id", "LEFT");
            $this->db->where('travel_order_id', $id);
            // $this->db->limit($limit, $offset); - Removed limit to show all destinations

            $i = $sortOrder[0]['column'];
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("b.lastname", $sortOrder[0]['dir']);
            } else {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $rs->destination = $rs->destination . '<br>Req By: ' . $rs->display_name;
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

        function getPersonnel($id) {
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column" => "0", "dir" => "desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $rowData = array();
            $rowData = $this->get_all_personnel($limit, $offset, $sortBy, $sortOrder, $id);

            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_personnel($limit = 10, $offset = 0, $sortBy, $sortOrder, $id) {
            $sql = "a.id, IFNULL(c.name, IFNULL(b.position, '---')) as position, b.firstname, b.middlename, b.lastname, b.suffix";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_personnel a");
            $this->db->join("gccmaster.tblemployees b", "a.employee_id = b.id", "LEFT");
            $this->db->join("gcchris.tblposition c", "c.id = b.position", "LEFT");
            $this->db->where('travel_order_id', $id);
            // $this->db->limit($limit, $offset); - Removed limit for personnel

            $i = $sortOrder[0]['column'];
            if ($sortBy[$i]['data'] == "firstname") {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
                $this->db->order_by("b.lastname", $sortOrder[0]['dir']);
            } else {
                $this->db->order_by($sortBy[$i]['data'], $sortOrder[0]['dir']);
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    if(is_numeric($rs->position)){
                        $rs->position = $this->getPosition($rs->position);
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

        function getPosition($id){
            $this->db->select('name');
            $this->db->from('gcchris.tblposition');
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row_array()['name'];
        }

        function getCompanyCollection() {
            $this->core_layout->setPrivilegeName("to_masterfile");
            $get = $this->input->get();
            $resultarray = array();
            // if (isset($get['q'])) {
            //     $query = $this->db->query("SELECT `id`,`description` FROM gcchris.tblcompanies WHERE `description` LIKE '%{$get['q']}%' ORDER BY `description` ASC");
            // } else {
            //     $query = $this->db->query("SELECT `id`,`description` FROM gcchris.tblcompanies ORDER BY `description` ASC");
            // }
            $privilege = $this->core_layout->getCurrentActions();

            $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;

            $sql = "id, description";
            $this->db->select($sql);

            if ($view_by_company) {
                $this->db->where('id', $this->user_data['company']);
            }

            if (isset($get['q']) && $get['q']){
                $this->db->like('description', $get['q'], 'both');
            }

            $this->db->from('gcchris.tblcompanies');
            $this->db->limit(10);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["description"];
                    $data["text"] = $_query["description"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function getDepartmentCollection() {
            $get = $this->input->get();
            $resultarray = array();
            // if (isset($get['q'])) {
            //     $query = $this->db->query("SELECT `id`,`description` FROM gcchris.tbldepartments WHERE `description` LIKE '%{$get['q']}%' ORDER BY `description` ASC");
            // } else {
            //     $query = $this->db->query("SELECT `id`,`description` FROM gcchris.tbldepartments ORDER BY `description` ASC");
            // }

            $sql = "id, description";

            $this->db->select($sql);
            
            if (isset($get['q']) && $get['q']) {
                $this->db->like('description', $get['q'], 'both');
            } else {
                $this->db->limit(10);
            }

            $this->db->from("gcchris.tbldepartments");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["description"];
                    $data["text"] = $_query["description"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);

            // if(isset($get["company"]) && $get["company"]){
            //     $this->db->select("a.description as id, UPPER(IF(a.`code` = a.`description`, TRIM(a.`description`), TRIM(CONCAT(a.`code`,' | ', a.`description`)))) as text");
            //     $this->db->from("gcchris.tbldepartments a");
            //     $this->db->join("gccmaster.tblemployees b", "b.department_id = a.id", "INNER");
            //     $this->db->join("gcchris.tblcompanies c", "c.id = b.company_id", "INNER");
            //     $this->db->where("b.employee_status", "Active");
            //     $this->db->where("c.description", $get["company"]);

            //     if (isset($get['q']) && $get['q']) {
            //         $this->db->group_start();
            //         $this->db->like("a.code", $get['q'], "both");
            //         $this->db->or_like("a.description", $get['q'], "both");
            //         $this->db->group_end();
            //     }

            //     $this->db->limit(25);
            //     $this->db->group_by("a.id");
            //     $this->db->order_by("trim(a.code)", "ASC");
            //     $query = $this->db->get();
        
            //     if ($query->num_rows() > 0) { 
            //         $resultarray = $query->result_array(); 
            //     } else {
            //         $resultarray = array();
            //     }
            // }

            // return $resultarray;
        }
        
        function getAllDepartments() {
            $get = $this->input->get();
            $resultarray = array();

            $sql = "id, description";

            $this->db->select($sql);
            
            if (isset($get['q']) && $get['q']) {
                $this->db->like('description', $get['q'], 'both');
            } else {
                $this->db->limit(10);
            }

            $this->db->from("gcchris.tbldepartments");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["description"];
                    $data["text"] = $_query["description"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function getEmployeeCollection() {
            $get = $this->input->get();
            $resultarray = array();
            // if (isset($get['q'])) {
            //     $query = $this->db->query("SELECT  id, firstname, middlename, lastname, suffix FROM gccmaster.tblemployees WHERE  (employee_status='Active') AND firstname LIKE '%{$get['q']}%' OR lastname LIKE '%{$get['q']}%' ORDER BY firstname ASC LIMIT 10");
            // } else {
            //     $query = $this->db->query("SELECT  id, firstname, middlename, lastname, suffix FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC LIMIT 10");
            // }

            $privilege = $this->core_layout->getCurrentActions();
            $view_by_company = (in_array("view_by_company", $privilege)) ? true : false;

            $sql = "id, firstname, middlename, lastname, suffix ";
            $this->db->select($sql);
            $this->db->from("gccmaster.tblemployees");
            
            if (isset($get['q']) && $get['q']) {
                $this->db->group_start();
                    $this->db->like('firstname', $get['q'], 'both');
                    $this->db->or_like('lastname', $get['q'], 'both');
                $this->db->group_end();
            }

            $this->db->where('employee_status', 'Active');

            // if ($view_by_company) {
            //     $this->db->where('company_id', $this->user_data['company']);
            // }

            $this->db->limit(10);
            $this->db->order_by('firstname', 'ASC');
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

        function check_employee($id) {
            $this->db->select('*');
            $this->db->from('gcceforms.travel_personnel_temp');
            $this->db->where('employee_id', $id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }

        function check_employee2($id, $empid) {
            $this->db->select('*');
            $this->db->from('gcceforms.travel_personnel');
            $this->db->where('employee_id', $empid);
            $this->db->where('travel_order_id', $id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }

        function getVehicleCollection() {
            $get = $this->input->get();
            $resultarray = array();
            // if (isset($get['q'])) {
            //     /*** $query = $this->db->query("SELECT  id, CONCAT(gen_code, ' | ', plateno,' | ',name) AS vehicle FROM gccasset.vehicles WHERE  (isCompo='0' AND is_borrowed='0') AND plateno LIKE '%{$get['q']}%' OR name LIKE '%{$get['q']}%' ORDER BY description ASC"); ***/
            //     $query = $this->db->query("SELECT  id, CONCAT(gen_code, ' | ', plateno,' | ',name) AS vehicle FROM gccasset.vehicles WHERE  isCompo='0' 
            //     AND status2 NOT IN ('archived', 'damage', 'junk', 'lost', 'sold') 
            //     AND (plateno LIKE '%{$get['q']}%' OR name LIKE '%{$get['q']}%' OR gen_code LIKE '%{$get['q']}%') ORDER BY description ASC");
            // } else {
            //     /*** $query = $this->db->query("SELECT  id, CONCAT(gen_code, ' | ',plateno,' | ',name) AS vehicle FROM gccasset.vehicles WHERE isCompo='0' AND is_borrowed='0' ORDER BY plateno ASC"); ***/
            //     $query = $this->db->query("SELECT  id, CONCAT(gen_code, ' | ',plateno,' | ',name) AS vehicle FROM gccasset.vehicles WHERE isCompo='0' 
            //     AND status2 NOT IN ('archived', 'damage', 'junk', 'lost', 'sold') ORDER BY plateno ASC");
            // }

            $sql = "id, CONCAT(gen_code, ' | ',plateno,' | ',name) AS vehicle";

            $this->db->select($sql);
            $this->db->where('isCompo', 0);
            $this->db->where_not_in('status2', array('archived', 'damage', 'junk', 'lost', 'sold'));

            if(isset($get['q']) && $get['q']) {
                $this->db->group_start();
                    $this->db->like('plateno', $get['q'], 'both');
                    $this->db->or_like('name', $get['q'], 'both');
                    $this->db->or_like('gen_code', $get['q'], 'both');
                $this->db->group_end();

                $this->db->order_by('description', 'ASC');
            }else{
                $this->db->order_by('plateno', 'ASC');
            }

            $this->db->order_by('plateno', 'ASC');
            $this->db->from('gccasset.vehicles');
            $query = $this->db->get();

            $sqlQuery = $this->db->last_query();

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["vehicle"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray, "sql"=>$sqlQuery);
        }

        function vehicle_details($veh) {
            $this->db->from('gccasset.vehicles');
            $this->db->where('id', $veh);
            $query = $this->db->get();
            return $query->row();
        }

        public function series($year, $month) {
            $this->db->select('ref_series');
            $this->db->from('gcceforms.travel_order');
            $this->db->where('ref_yr', $year);
            $this->db->where('ref_month', $month);
            $this->db->order_by('ref_series', 'asc');
            $query = $this->db->get();
            return $query->result();
        }

        public function save_travel_order($data) {
            $this->db->insert('gcceforms.travel_order', $data);
            return $this->db->insert_id();
        }

        public function update_travel_order($where, $data) {
            $this->db->update('gcceforms.travel_order', $data, $where);
            return $this->db->affected_rows();
        }

        public function save_temp_personnel($data) {
            $this->db->insert('gcceforms.travel_personnel_temp', $data);
            return $this->db->insert_id();
        }

        public function edit_temp_personnel($id) {
            $sql = "a.id, a.employee_id,CONCAT( b.firstname,' ',b.middlename,' ',b.lastname) AS name";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_personnel_temp a");
            $this->db->join("gccmaster.tblemployees b", "a.employee_id = b.id", "LEFT");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_temp_personnel($where, $data) {
            $this->db->update('gcceforms.travel_personnel_temp', $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_temp_personnel($id) {
            $emp_id = $this->db->get_where("gcceforms.travel_personnel_temp", array("id"=>$id))->row('employee_id');
            $this->db->where('id', $id);
            if($this->db->delete('gcceforms.travel_personnel_temp')){
                $this->core_layout->setEventLog("Delete ".$this->getPersonnelName($emp_id)." in travel order personnel on temporary table.","delete", "success", "gcceforms", "user");
                return true;
            }else{
                $this->core_layout->setEventLog("Failed delete ".$this->getPersonnelName($emp_id)." in travel order personnel on temporary table.","delete", "error", "gcceforms", "system");
            }
        }

        public function delete_temp_all_personnel($id) {
            $this->db->where('user_id', $id);
            if($this->db->delete('gcceforms.travel_personnel_temp')){
                $this->core_layout->setEventLog("Delete all personnel of travel order on temporary table.","delete", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed delete all personnel of travel order on temporary table.","delete", "success", "gcceforms", "user");
            }
        }

        public function get_temp_personnel($id) {
            $this->db->from('gcceforms.travel_personnel_temp');
            $this->db->where('user_id', $id);
            $query = $this->db->get();
            return $query->result();
        }

        public function save_personnel($data) {
            $this->db->insert('gcceforms.travel_personnel', $data);
            $this->core_layout->setEventLog("Added Personnel `{$this->core_layout->getEmployeeData($data['employee_id'])['display_name_0']}` of Travel Order ID `{$data['travel_order_id']}`.","insert", "success", "gcceforms", "user");
            return $this->db->insert_id();
        }

        public function edit_personnel($id) {
            $sql = "a.id, a.employee_id,CONCAT( b.firstname,' ',b.middlename,' ',b.lastname) AS name";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_personnel a");
            $this->db->join("gccmaster.tblemployees b", "a.employee_id = b.id", "LEFT");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_personnel($where, $data) {
            $this->db->select('employee_id');
            $this->db->from('gcceforms.travel_personnel');
            $this->db->where('id', $where['id']);
            $row = $this->db->get()->row();

            $this->db->update('gcceforms.travel_personnel', $data, $where);
            $this->core_layout->setEventLog("Updated Personnel from `{$this->core_layout->getEmployeeData($row->employee_id)['display_name_0']}` to `{$this->core_layout->getEmployeeData($data['employee_id'])['display_name_0']}` of Travel Order ID `{$data['travel_order_id']}`.","insert", "success", "gcceforms", "user");

            return $this->db->affected_rows();
        }

        public function delete_personnel($id) {
            $this->db->select('travel_order_id, employee_id');
            $this->db->where('id', $id);
            $row = $this->db->get('gcceforms.travel_personnel')->row();

            $this->db->where('id', $id);
            $this->db->delete('gcceforms.travel_personnel');

            $this->core_layout->setEventLog("Deleted Personnel `{$this->core_layout->getEmployeeData($row->employee_id)['display_name_0']}` of Travel Order ID `{$row->travel_order_id}`.","delete", "success", "gcceforms", "user");
        }

        public function delete_all_personnel($id) {
            $this->db->where('travel_order_id', $id);
            $this->db->delete('gcceforms.travel_personnel');

            $this->core_layout->setEventLog("Deleted all personnel of Travel Order ID `{$id}`.","delete", "success", "gcceforms", "user");
        }

        public function get_personnel($id) {
            $this->db->from('gcceforms.travel_personnel');
            $this->db->where('travel_order_id', $id);
            $query = $this->db->get();
            return $query->result();
        }

        public function save_temp_destination($data) {
            $this->db->insert('gcceforms.travel_destination_temp', $data);
            return $this->db->insert_id();
        }

        public function edit_temp_destination($id) {
            $sql = "a.id, a.requested_by, a.des_from, a.des_to, a.purpose, a.date_from, a.date_to, a.instructions, a.remarks, a.coords_from, a.coords_to, CONCAT( b.firstname,' ',b.middlename,' ',b.lastname) AS name";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_destination_temp a");
            $this->db->join("gccmaster.tblemployees b", "a.requested_by = b.id", "LEFT");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_temp_destination($where, $data) {
            $this->db->update('gcceforms.travel_destination_temp', $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_temp_destination($id) {
            $destination = $this->getDestinationDetails($id);
            $this->db->where('id', $id);
            if($this->db->delete('gcceforms.travel_destination_temp')){
                $this->core_layout->setEventLog("Delete destination ".$destination." of travel order.","delete", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed delete destination ".$destination." of travel order.","delete", "error", "gcceforms", "system");
            }
        }

        public function delete_temp_all_destination($id) {
            $this->db->where('user_id', $id);
            if($this->db->delete('gcceforms.travel_destination_temp')){
                $this->core_layout->setEventLog("Delete all destination of travel order on temporary table.","delete", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed delete all destination of travel order  on temporary table.","delete", "error", "gcceforms", "system");
            }
        }

        public function get_temp_destination($id) {
            $this->db->from('gcceforms.travel_destination_temp');
            $this->db->where('user_id', $id);
            $query = $this->db->get();
            return $query->result();
        }

        public function edit_destination($id) {
            
            $sql = "a.id, a.requested_by, a.travel_from, a.travel_to, a.purpose, a.date_from, a.date_to, a.instructions, a.remarks, a.coords_from, a.coords_to, CONCAT( b.firstname,' ',b.middlename,' ',b.lastname) AS name";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_destination a");
            $this->db->join("gccmaster.tblemployees b", "a.requested_by = b.id", "LEFT");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function edit_destination_marker(){
            $post = $this->input->post();
            $this->db->where('id', $post['travelOrder_id']);
            $query = $this->db->get("gcceforms.travel_destination");

            return $query->row_array();
        }

        public function new_destination_marker(){
            $post = $this->input->post();
            $this->db->where('id', $post['travelOrder_id']);
            $query = $this->db->get("gcceforms.travel_destination_temp");

            return $query->row_array();
        }

        public function update_destination($where, $data, $id) {
            $this->db->update('gcceforms.travel_destination', $data, $where);


            $this->db->from('gcceforms.travel_destination');
            if(isset($where['travel_order_id']) && $where['travel_order_id']){
                $this->db->where('travel_order_id', $where['travel_order_id']);
            }else{
                $this->db->where('id', $where['id']);
            }
            
            if(isset($where['travel_order_id']) && $where['travel_order_id']){
                $destination = $this->db->get()->result();
                $temp = array();
                foreach($destination as $rows){
                    $temp[] = "`{$rows->destination}`, `{$rows->date_from}-{$rows->date_to}` purpose of `{$rows->purpose}`";
                }

                $tempLogs = implode('; ', $temp);
                $this->core_layout->setEventLog("Updated destination with {$tempLogs} of Travel Order ID `{$id}`.","update", "success", "gcceforms", "user");
            }else{
                $destination = $this->db->get()->row();
                $this->core_layout->setEventLog("Updated destination with `{$destination->destination}`, `{$destination->date_from}-{$destination->date_to}`, purpose of `{$destination->purpose}` of Travel Order ID `{$id}`.","update", "success", "gcceforms", "user");
            }

            return $this->db->affected_rows();
        }

        public function delete_destination($id) {
            $this->db->select('travel_order_id, destination');
            $this->db->where('id', $id);
            $row = $this->db->get('gcceforms.travel_destination')->row();

            $this->db->where('id', $id);
            if($this->db->delete('gcceforms.travel_destination')){
                $this->core_layout->setEventLog("Deleted destination `{$row->destination}` of Travel Order ID `{$row->travel_order_id}`.","delete", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Failed to delete the destination `{$row->destination}` of Travel Order ID `{$row->travel_order_id}`.","delete", "error", "gcceforms", "system");
            }
        }

        public function delete_all_destination($id) {
            $this->db->where('travel_order_id', $id);
            $this->db->delete('gcceforms.travel_destination');
            $this->core_layout->setEventLog("Deleted all destination of Travel Order ID `{$id}`.","delete", "success", "gcceforms", "user");
        }

        public function get_destination($id) {
            $this->db->from('gcceforms.travel_destination');
            $this->db->where('travel_order_id', $id);
            $query = $this->db->get();
            return $query->result();
        }

        public function save_destination($data) {
            $this->db->insert('gcceforms.travel_destination', $data);

            $this->core_layout->setEventLog("Added destination `{$data['destination']}` of Travel Order ID `{$data['travel_order_id']}`.","insert", "success", "gcceforms", "user");

            return $this->db->insert_id();
        }

        public function travel_order_details($id) {
            $arrData = array();
            $newPersonnel = array();
            $newDestination = array();
            $this->db->from('gcceforms.travel_order');
            $this->db->where('id', $id);
            $query = $this->db->get();
            $travel_order_data = $query->row();

            if($travel_order_data->vehicle_id == "0"){
                $plateno = "";
            }else{
                if ($travel_order_data->vehicle_id) {
                    $vehicle_data = $this->vehicle_details($travel_order_data->vehicle_id);
                    $gen_code = $vehicle_data->gen_code;
                    $plateno = $gen_code . " | " . $vehicle_data->plateno . " | " . $vehicle_data->name;
                } else {
                    $plateno = $travel_order_data->vehicle_id;
                }
            }

            $travel_order_data->plateno = $plateno;

            if($travel_order_data->accomplished_by > 0){
                $travel_order_data->accomplished_by_name = $this->getPersonnelName($travel_order_data->accomplished_by);
            }


            $personnel = $this->db->get_where("gcceforms.travel_personnel", array("travel_order_id"=>$id))->result_array();
            $destination = $this->db->get_where("gcceforms.travel_destination", array("travel_order_id"=>$id))->result_array();
            foreach($personnel as $personnels){
                $personnels['employee_id'] = $this->getPersonnelName($personnels['employee_id']);
                $newPersonnel[] = $personnels;
            }
            
            foreach($destination as $destinations){
                $destinations['requested_by'] = $this->getPersonnelName($destinations['requested_by']);
                $destinations['date_from'] = date("M d, Y g:i A", strtotime($destinations['date_from']));
                $destinations['date_to'] = date("M d, Y g:i A", strtotime($destinations['date_to']));
                $newDestination[] = $destinations;
            }

            $end_travel_order_time = end($destination);

            if(isset($destination[0]['date_from']) && $destination[0]['date_from']){
                $travel_order_data->duration = date("M d, Y g:i A", strtotime($destination[0]['date_from']))." - ".date("M d, Y g:i A", strtotime($end_travel_order_time['date_to']));
            }

            $arrData['data'] = $travel_order_data;
            $arrData['personnel'] = $newPersonnel;
            $arrData['destination'] = $newDestination;
            return $arrData;
        }

        function company_details($id) {
            $this->db->from('gcchris.tblcompanies');
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row();
        }

        function department_details($id) {
            $this->db->from('gcchris.tbldepartments');
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row();
        }

        function emp_details($id) {
            $this->db->from('gccmaster.tblemployees');
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row();
        }

        function getCreated() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("a.status", "a.reference_no", "a.company");
                $dir = "DESC";
                $order = "a.id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }

                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $date = (isset($post["date"]) && $post["date"]) ? $post["date"] : null;
                $posts = $this->get_created_to($limit, $start, $order, $dir, $date);

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData = array();
                        $nestedData['id'] = $pst->id;
                        $nestedData['status'] = $pst->status;
                        $nestedData['reference_no'] = $pst->reference_no;
                        $nestedData['company'] = $pst->company;
                        $nestedData['driver'] = $pst->driver;
                        $nestedData['vehicle_plate'] = $pst->vehicle_plate;
                        $nestedData['vehicle_description'] = $pst->vehicle_description;
                        $nestedData['accomplishment_dt'] = $pst->accomplishment_dt;
                        $nestedData['is_service'] = $pst->is_service;
                        $nestedData['is_commute'] = $pst->is_commute;
                        $nestedData['is_personal'] = $pst->is_personal;
                        $nestedData['is_others'] = $pst->is_others;
                        $nestedData['others_remarks'] = $pst->others_remarks;
                        $nestedData['personnels'] = $pst->personnels;
                        $nestedData['destination'] = $pst->destination;
                        $nestedData['from_to'] = $pst->from_to;
                        $data[] = $nestedData;
                    }
                }
                $json_data = array(

                    "data" => $data
                );

                return $json_data;
            } else {
                return array(

                    "data" => array()
                );
            }
        }

        private function get_created_to($limit = 10, $start = 0, $order = "a.id", $dir = "DESC", $date) {
            $arrData = array();
            $this->db->select("a.id, a.reference_no, a.company, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks,a.accomplishment_dt");
            $this->db->from("gcceforms.travel_order a");
            if (!empty($date['start']) && !empty($date['end'])) {
                $s = date('Y-m-d H:i:s', strtotime($date['start'] . ' 00:00:00'));
                $e = date('Y-m-d H:i:s', strtotime($date['end'] . ' 23:59:59'));
                $this->db->where("a.created_dt >=", $s);
                $this->db->where("a.created_dt <=", $e);
            }else{
                $todayStart = date('Y-m-d 00:00:00');
                $todayEnd = date('Y-m-d 23:59:59');
                $this->db->where("a.created_dt >=", $todayStart);
                $this->db->where("a.created_dt <=", $todayEnd);
            }
            $this->db->limit($limit, $start);
            $this->db->order_by($order, $dir);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $rowId = $rs->id;
                    $this->db->select("UPPER(CONCAT(te.firstname, ' ', te.lastname)) as employee_name");
                    $this->db->from("gcceforms.travel_personnel tp");
                    $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                    $this->db->where("tp.travel_order_id", $rowId);
                    $personnel = $this->db->get();

                    $rowPersonnel = array();
                    $rowDestination = array();
                    $rowDriver = "";
                    $rowVehiclePlate = "";
                    $rowVehicleDesc = "";
                    $rowFromTo = "";

                    if ($personnel->num_rows() > 0) {
                        foreach ($personnel->result() as $key => $value) {
                            $rowPersonnel[] = $value->employee_name;
                        }
                    }

                    if ($rs->is_service == "1") {
                        $this->db->select("UPPER(CONCAT(te.firstname, ' ', te.lastname)) as driver_name");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();

                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $rowDriver = $rowData->driver_name;
                        }

                        $this->db->select("plateno, description");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->description;
                        }
                    }

                    $this->db->select("td.destination, td.date_from, td.date_to");
                    $this->db->from("gcceforms.travel_destination td");
                    $this->db->where("td.travel_order_id", $rowId);
                    $destination = $this->db->get();

                    if ($destination->num_rows() > 0) {
                        $des_num = 0;
                        foreach ($destination->result() as $key => $vx) {
                            $des_num++;
                            $rowDestination[] = $vx->destination;

                            $x = explode(' ', $vx->date_from);
                            $y = explode(' ', $vx->date_to);
                            if ($des_num == 1) {
                                $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                                $z = explode(' ', $vx->date_from);
                            }

                            if ($z[0] != $x[0]) {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            } else if ($x[0] == $y[0]) {
                                $date_end = date("g:i A", strtotime($y[1]));
                            } else {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            }

                            $rowFromTo = "{$date_start} - {$date_end}";
                        }
                    }

                    $rs->personnels = $rowPersonnel;
                    $rs->driver = $rowDriver;
                    $rs->vehicle_plate = $rowVehiclePlate;
                    $rs->vehicle_description = $rowVehicleDesc;
                    $rs->destination = $rowDestination;
                    $rs->from_to = $rowFromTo;

                    $arrData[] = $rs;
                }
            }

            return $arrData;

        }

        function getDeparting() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("a.status", "a.reference_no", "a.company");
                $dir = "DESC";
                $order = "a.id";
                if (isset($post["order"]) && $post["order"]) {
                    $dir = $post["order"][0]["dir"];
                    $order = $columns[$post["order"][0]["column"]];
                }

                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $date = (isset($post["date"]) && $post["date"]) ? $post["date"] : null;

                $posts = $this->get_departing_to($limit, $start, $order, $dir, $date);


                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData = array();
                        $nestedData['id'] = $pst->id;
                        $nestedData['status'] = $pst->status;
                        $nestedData['reference_no'] = $pst->reference_no;
                        $nestedData['company'] = $pst->company;
                        $nestedData['driver'] = $pst->driver;
                        $nestedData['vehicle_plate'] = $pst->vehicle_plate;
                        $nestedData['vehicle_description'] = $pst->vehicle_description;
                        $nestedData['accomplishment_dt'] = $pst->accomplishment_dt;
                        $nestedData['is_service'] = $pst->is_service;
                        $nestedData['is_commute'] = $pst->is_commute;
                        $nestedData['is_personal'] = $pst->is_personal;
                        $nestedData['is_others'] = $pst->is_others;
                        $nestedData['others_remarks'] = $pst->others_remarks;
                        $nestedData['personnels'] = $pst->personnels;
                        $nestedData['destination'] = $pst->destination;
                        $nestedData['from_to'] = $pst->from_to;
                        $data[] = $nestedData;
                    }
                }
                $json_data = array(

                    "data" => $data
                );

                return $json_data;
            } else {
                return array(

                    "data" => array()
                );
            }
        }

        private function get_departing_to($limit = 10, $start = 0, $order = "a.id", $dir = "DESC", $date) {
            $check = date('Y-m-d', strtotime("-7 days"));
            $arrData = array();
            $this->db->select("a.id, a.reference_no, a.company, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks,a.accomplishment_dt");
            $this->db->from("gcceforms.travel_order a");
            $this->db->join("gcceforms.travel_destination td", "td.travel_order_id = a.id", "left");
            if (!empty($date['start']) && !empty($date['end'])) {
                $startDate = date('Y-m-d H:i:s', strtotime($date['start'] . ' 00:00:00'));
                $endDate = date('Y-m-d H:i:s', strtotime($date['end'] . ' 23:59:59'));
            } else {
                $startDate = date('Y-m-d 00:00:00');
                $endDate = date('Y-m-d 23:59:59');
            }
            $this->db->where("(
                (td.date_from <= '$endDate' AND td.date_to >= '$startDate')
            )");
            $this->db->where("a.status", "Approved");
            $this->db->limit($limit, $start);
            $this->db->order_by($order, $dir);
            $this->db->group_by("a.id");
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $rowId = $rs->id;
                    $this->db->select("UPPER(CONCAT(te.firstname, ' ', te.lastname)) as employee_name");
                    $this->db->from("gcceforms.travel_personnel tp");
                    $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                    $this->db->where("tp.travel_order_id", $rowId);
                    $personnel = $this->db->get();

                    $rowPersonnel = array();
                    $rowDestination = array();
                    $rowDriver = "";
                    $rowVehiclePlate = "";
                    $rowVehicleDesc = "";
                    $rowFromTo = "";

                    if ($personnel->num_rows() > 0) {
                        foreach ($personnel->result() as $key => $value) {
                            $rowPersonnel[] = $value->employee_name;
                        }
                    }

                    if ($rs->is_service == "1") {
                        $this->db->select("UPPER(CONCAT(te.firstname, ' ', te.lastname)) as driver_name");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();

                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $rowDriver = $rowData->driver_name;
                        }

                        $this->db->select("plateno, description");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->description;
                        }
                    }

                    $this->db->select("td.destination, td.date_from, td.date_to");
                    $this->db->from("gcceforms.travel_destination td");
                    $this->db->where("td.travel_order_id", $rowId);
                    $destination = $this->db->get();

                    if ($destination->num_rows() > 0) {
                        $des_num = 0;
                        foreach ($destination->result() as $key => $vx) {
                            $des_num++;
                            $rowDestination[] = $vx->destination;

                            $x = explode(' ', $vx->date_from);
                            $y = explode(' ', $vx->date_to);
                            if ($des_num == 1) {
                                $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                                $z = explode(' ', $vx->date_from);
                            }

                            if ($z[0] != $x[0]) {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            } else if ($x[0] == $y[0]) {
                                $date_end = date("g:i A", strtotime($y[1]));
                            } else {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            }

                            $rowFromTo = "{$date_start} - {$date_end}";
                        }
                    }

                    $rs->personnels = $rowPersonnel;
                    $rs->driver = $rowDriver;
                    $rs->vehicle_plate = $rowVehiclePlate;
                    $rs->vehicle_description = $rowVehicleDesc;
                    $rs->destination = $rowDestination;
                    $rs->from_to = $rowFromTo;

                    $arrData[] = $rs;
                }
            }

            return $arrData;

        }

        function m_get_travel_analytics_for_dashboard() {
            $post = $this->input->post();
            $this->db->reset_query();
            $this->db->select("a.status, COUNT(a.id) AS count, (select count(status) from gcceforms.travel_order where status='Approved' AND accomplishment_dt != '0000-00-00 00:00:00') as accom");
            $this->db->from("gcceforms.travel_order a");
            if (!empty($post['date']['start']) && !empty($post['date']['end'])) {
                $start = date('Y-m-d H:i:s', strtotime($post['date']['start'] . ' 00:00:00'));
                $end = date('Y-m-d H:i:s', strtotime($post['date']['end'] . ' 23:59:59'));
                $this->db->where("DATE(a.created_dt) >=", $start);
                $this->db->where("DATE(a.created_dt) <=", $end);
            }
            $this->db->group_by("a.status");
            $this->db->order_by("FIELD(a.status, 'Pending', 'Approved', 'Hr Noted', 'Disapproved', 'Cancelled')");

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $accomplish = array("status" => "Accomplished", "count" => $rs->accom, "accom" => $rs->accom);
                    $arrData[$key] = $rs;
                    $arrData[] = $accomplish;
                }
                return json_decode(json_encode($arrData));
            } else {
                return array();
            }
        }

        function mostTraveledPerson() {
            $arrData = array();
            
            $this->db->select("*,count(employee_id) c, b.firstname, b.lastname, b.middlename, b.suffix");
            $this->db->from("gcceforms.travel_personnel a");
            $this->db->join("gccmaster.tblemployees b", "a.employee_id=b.id", "left");
            $this->db->where("employee_id !=", "");
            $this->db->group_by("employee_id");
            $this->db->order_by("c", "DESC");
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                
                foreach ($query->result() as $key => $rs) {
                    $tempRs = (array)$rs;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $rs->sum = $this->sumDetails("travel_personnel", "employee_id");
                    $arrData[] = $rs;
                }
                // return $arrData[0];
            } 
            // else {
            //     return array();
            // }

            return $arrData[0];
        }

        function mostTraveledVehicle() {
            $this->db->select("*,count(vehicle) c");
            $this->db->from("gcceforms.travel_order");
            $this->db->where("vehicle !=", "");
            $this->db->group_by("vehicle");
            $this->db->order_by("c", "DESC");
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $rs->sum = $this->sumDetails("travel_order", "vehicle");
                    $arrData[] = $rs;
                }
                return $arrData[0];
            } else {
                return array();
            }
        }

        function mostTraveledDestination() {
            $this->db->select("*,count(destination) c");
            $this->db->from("gcceforms.travel_destination");
            $this->db->where("destination !=", "");
            $this->db->group_by("destination");
            $this->db->order_by("c", "DESC");
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
                    $rs->sum = $this->sumDetails("travel_destination", "destination");
                    $arrData[] = $rs;
                }
                return $arrData[0];
            } else {
                return array();
            }
        }

        function sumDetails($table, $where) {
            $this->db->select("count(*) c");
            $this->db->from("gcceforms." . $table . "");
            $this->db->where("" . $where . " !=", "");
            $query = $this->db->get();
            return $query->row_array()['c'];
        }

        public function generateDailyTravelOrderSummary($date = null) {
            $currentDate = ($date) ? date("Y-m-d", strtotime($date)) : date("Y-m-d");
            $sqlSelect = "a.id, a.reference_no, a.station, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks, a.vehicle_id, ";
            $sqlSelect .= "a.driver_id, a.status, a.created_by, a.approved_by, b.lastname, b.firstname, b.middlename, b.suffix, ";
            $sqlSelect .= "IFNULL(pos.name, IFNULL(b.position, '---')) as position, c.name as vehicle_name, c.plateno";

            $this->db->select($sqlSelect);
            $this->db->from("gcceforms.travel_order a");
            $this->db->join("gccmaster.tblemployees b", "b.id = a.driver_id", "LEFT");
            $this->db->join("gccasset.vehicles c", "c.id = a.vehicle_id", "LEFT");
            $this->db->join("gcceforms.travel_destination d", "a.id = d.travel_order_id");
            $this->db->join("gcchris.tblposition pos", "pos.id = b.position", "left");
            $this->db->group_by("d.travel_order_id");
            $this->db->like("DATE(d.date_from)", $currentDate, "both");
            $this->db->order_by("a.id", "ASC");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $rs) {
                    $tempRs = (array)$rs;

                    $displayName = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$displayName;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";

                    $this->db->select("b.lastname, b.firstname, b.middlename, b.suffix");
                    $this->db->from("gcceforms.travel_personnel a");
                    $this->db->join("gccmaster.tblemployees b", "b.id = a.employee_id", "LEFT");
                    $this->db->where("a.travel_order_id", $rs->id);
                    $this->db->where("b.employee_status", "Active");
                    $this->db->order_by("a.id", "ASC");
                    $personnel = $this->db->get();

                    if ($personnel->num_rows() > 0) {
                        $tempPersonnelData = array();
                        foreach ($personnel->result() as $rsx) {
                            $tempRsx = (array)$rsx;
                            $temp_display_name = $this->core_layout->getDisplayName($tempRsx);
                            $tempFullname = (object)$temp_display_name;
                            $tempPersonnelData[] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                        }

                        $rs->personnels = $tempPersonnelData;
                    }

                    $this->db->select("a.destination, a.purpose, a.date_from, a.date_to, a.remarks, b.lastname, b.firstname, b.middlename, b.suffix");
                    $this->db->from("gcceforms.travel_destination a");
                    $this->db->join("gccmaster.tblemployees b", "b.id = a.requested_by", "LEFT");
                    $this->db->where("a.travel_order_id", $rs->id);
                    $this->db->where("b.employee_status", "Active");
                    $this->db->order_by("a.id", "ASC");
                    $destination = $this->db->get();

                    if ($destination->num_rows() > 0) {
                        $tempDestinationData = array();
                        foreach ($destination->result() as $key => $valuex) {
                            $tempRsxx = (array)$valuex;

                            $temp_display_name = $this->core_layout->getDisplayName($tempRsxx);
                            $tempFullname = (object)$temp_display_name;
                            $valuex->requested_by = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";

                            $tempFrom = explode(" ", $valuex->date_from);
                            $tempTo = explode(" ", $valuex->date_to);

                            $tempDateTime = "---";
                            if ($tempFrom[0] == $tempTo[0]) {
                                $tempDateTime = date("M d, Y g:i A", strtotime($valuex->date_from)) . ' - ' . date("g:i A", strtotime($tempTo[1]));
                            } else {
                                $tempDateTime = date("M d, Y g:i A", strtotime($valuex->date_from)) . ' - ' . date("M d, Y g:i A", strtotime($valuex->date_to));
                            }

                            $valuex->duration = $tempDateTime;
                            $tempDestinationData[] = $valuex;
                        }
                        $rs->destinations = $tempDestinationData;
                    }

                    $arrData[] = $rs;
                }
                return $arrData;
            } else {
                return false;
            }
        }

        public function generateNextDayTravelOrderSummary() {
            $date = date("Y-m-d");
            $currentDate = date("Y-m-d", strtotime($date . "+1 day"));
            $sqlSelect = "a.id, a.reference_no, a.station, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks, a.vehicle_id, ";
            $sqlSelect .= "a.driver_id, a.status, a.created_by, a.approved_by, b.lastname, b.firstname, b.middlename, b.suffix, ";
            $sqlSelect .= "IFNULL(pos.name, IFNULL(b.position, '---')) as position, c.name as vehicle_name, c.plateno";

            $this->db->select($sqlSelect);
            $this->db->from("gcceforms.travel_order a");
            $this->db->join("gccmaster.tblemployees b", "b.id = a.driver_id", "LEFT");
            $this->db->join("gccasset.vehicles c", "c.id = a.vehicle_id", "LEFT");
            $this->db->join("gcceforms.travel_destination d", "a.id = d.travel_order_id");
            $this->db->join("gcchris.tblposition pos", "pos.id = b.position", "left");
            $this->db->group_by("d.travel_order_id");
            $this->db->like("DATE(d.date_from)", $currentDate, "both");
            $this->db->order_by("a.id", "ASC");
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $rs) {
                    $tempRs = (array)$rs;

                    $displayName = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$displayName;
                    $rs->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";

                    $this->db->select("b.lastname, b.firstname, b.middlename, b.suffix");
                    $this->db->from("gcceforms.travel_personnel a");
                    $this->db->join("gccmaster.tblemployees b", "b.id = a.employee_id", "LEFT");
                    $this->db->where("a.travel_order_id", $rs->id);
                    $this->db->where("b.employee_status", "Active");
                    $this->db->order_by("a.id", "ASC");
                    $personnel = $this->db->get();

                    if ($personnel->num_rows() > 0) {
                        $tempPersonnelData = array();
                        foreach ($personnel->result() as $rsx) {
                            $tempRsx = (array)$rsx;
                            $temp_display_name = $this->core_layout->getDisplayName($tempRsx);
                            $tempFullname = (object)$temp_display_name;
                            $tempPersonnelData[] = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                        }

                        $rs->personnels = $tempPersonnelData;
                    }

                    $this->db->select("a.destination, a.purpose, a.date_from, a.date_to, a.remarks, b.lastname, b.firstname, b.middlename, b.suffix");
                    $this->db->from("gcceforms.travel_destination a");
                    $this->db->join("gccmaster.tblemployees b", "b.id = a.requested_by", "LEFT");
                    $this->db->where("a.travel_order_id", $rs->id);
                    $this->db->where("b.employee_status", "Active");
                    $this->db->order_by("a.id", "ASC");
                    $destination = $this->db->get();

                    if ($destination->num_rows() > 0) {
                        $tempDestinationData = array();
                        foreach ($destination->result() as $key => $valuex) {
                            $tempRsxx = (array)$valuex;

                            $temp_display_name = $this->core_layout->getDisplayName($tempRsxx);
                            $tempFullname = (object)$temp_display_name;
                            $valuex->requested_by = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";

                            $tempFrom = explode(" ", $valuex->date_from);
                            $tempTo = explode(" ", $valuex->date_to);

                            $tempDateTime = "---";
                            if ($tempFrom[0] == $tempTo[0]) {
                                $tempDateTime = date("M d, Y g:i A", strtotime($valuex->date_from)) . ' - ' . date("g:i A", strtotime($tempTo[1]));
                            } else {
                                $tempDateTime = date("M d, Y g:i A", strtotime($valuex->date_from)) . ' - ' . date("M d, Y g:i A", strtotime($valuex->date_to));
                            }

                            $valuex->duration = $tempDateTime;
                            $tempDestinationData[] = $valuex;
                        }
                        $rs->destinations = $tempDestinationData;
                    }

                    $arrData[] = $rs;
                }
                return $arrData;
            } else {
                return false;
            }
        }

        public function confirmTravelDestination(){
            $post = $this->input->post();
            $travel_order_id = $post['travel_order_id'];
            $this->db->where("travel_order_id", $travel_order_id);
            $data = $this->db->get("gcceforms.travel_destination");
            $count = $data->num_rows();
            if($count == $this->numTOAccomplish($travel_order_id)){
                return 1;
            }else{
                return 0;
            }
        }

        public function destinationStatus(){
            $datetime = date("Y-m-d H:i:s");
            $post = $this->input->post();
            if(isset($post['destination_id']) && $post['destination_id'] != null){
                $status = $post['status'];
                $post = $this->input->post();
                $destination_id = $post['destination_id'];
                $data = $this->db->update("gcceforms.travel_destination", array("travel_order_status" => $status, "travel_order_status_date" => $datetime) ,array("id" => $destination_id));
                if($data){
                    return 1;
                }else{
                    return 0;
                }
            }
        }

        public function verifyTOStatus(){
            $post = $this->input->post();
            if(isset($post['travel_order_id']) && $post['travel_order_id'] != null){
                $TO_id = $post['travel_order_id'];
                $this->db->select("status, accomplished");
                $this->db->where("id", $TO_id);
                $data = $this->db->get("gcceforms.travel_order");
                return $data->row_array();
            }
        }

        public function setAllStatus(){
            $post = $this->input->post();
            if(isset($post['travel_order_id']) && $post['travel_order_id'] != null){
                $travel_order_id = $post['travel_order_id'];
                $status = $post['status'];
                $data = $this->db->update("gcceforms.travel_destination", array("travel_order_status" => $status), array("travel_order_id" => $travel_order_id));
                if($data){
                    return 1;
                }else{
                    return 0;
                }
            }
        }

        public function sitesOption(){
            $post = $this->input->post();
            // if(isset($post['sites_id']) && $post['sites_id'] != ""){
            //     $this->db->select("site_name, latitude, longtitude");
            //     $this->db->where("id", $post['sites_id']);
            //     $all_data = $this->db->get("gcctimeutility.app_location_sites");
            //     return $all_data->row_array();
            // }else{
            //     $this->db->select("id, site_name, latitude, longtitude");
            //     $all_data = $this->db->get("gcctimeutility.app_location_sites")->result_array();
            //     return $all_data;
            // }

            $this->db->select("id, site_name, latitude, longtitude");

            if(isset($post['sites_id']) && $post['sites_id'] != null){
                $this->db->where("id", $post['sites_id']);
            }

            $query = $this->db->get("gcctimeutility.app_location_sites");

            $all_data = isset($post['sites_id']) && $post['sites_id'] != null ? $query->row_array() : $query->result_array();
            return $all_data;
        }

        private function numTOAccomplish($travel_id){
            $this->db->where("travel_order_id", $travel_id);
            $this->db->where("travel_order_status", 1);
            $data = $this->db->get("gcceforms.travel_destination");
            $count = $data->num_rows();
            return $count;
        }

        function getDepartmentHeads(){
            $this->db->select('user.email');
            $this->db->from('gccmaster.tblusers user');
            $this->db->join('gcchris.tbldepartments dept','user.emp_id=dept.head_id');
            $this->db->where('dept.is_archived', 0);
            $this->db->where('user.email !=', 'NO EMAIL ADDRESS');
            $this->db->where('user.email !=', '');
            $this->db->group_by('user.email');
            $data = $this->db->get("gccmaster.tblusers")->result();
            foreach($data as $emails){
                $dept_head[] = $emails->email;
            }
            return $dept_head;
        }

        public function telegram_config_if_exist($module, $data){
            $this->db->where("module","travel_order");
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
            try {
                $data = $this->telegram_config_if_exist('travel_order', 'data');
                $telegrambot=$data->telegram_bot_token;
                $telegramchatid= $data->chat_id;
                $url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';$data=array('chat_id'=>$telegramchatid,'text'=>$msg,'parse_mode'=>'html');
                $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),'ignore_errors'=>true),);
                $context=stream_context_create($options);
                $result=file_get_contents($url,false,$context);
                return $result;
            } catch (Exception $e) {
                return false;
            }
        }

        public function telegram_dept_heads($msg,$dept_head_chat_id) {
            $telegram_data = $this->telegram_config_if_exist('travel_order', 'data');

            $telegramchatid= $dept_head_chat_id;
            $url='https://api.telegram.org/bot'.$telegram_data->telegram_bot_token.'/sendMessage';$data=array('chat_id'=>$telegramchatid,'text'=>$msg,'parse_mode'=>'html');
            $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),'ignore_errors'=>true),);
            $context=stream_context_create($options);
            $result=file_get_contents($url,false,$context);
            return $result;
        }

        public function getTelegramId($emp_id){
            
            $department = $this->db->get_where("gccmaster.tblemployees", array("id"=>$emp_id))->row("department_id");

            $this->db->reset_query();

            $where = rtrim(is_numeric($department)) ? array("id" => $department) : array("description" => $department);

            $this->db->select('head_id');
            $department_head = $this->db->get_where("gcchris.tbldepartments", $where)->row("head_id");
            // if(rtrim(is_numeric($department))){
            //     $department_head = $this->db->get_where("gcchris.tbldepartments",array("id"=>$department))->row("head_id");
            // }else{
            //     $department_head = $this->db->get_where("gcchris.tbldepartments",array("description"=>$department))->row("head_id");
            // }  

            $this->db->reset_query();

            $this->db->select('telegram_chat_id');
            $data = $this->db->get_where("gccmaster.tblusers", array("emp_id"=>$department_head))->row("telegram_chat_id");
            return $data;
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
                $this->core_layout->setEventLog("Trave Order Masterfile - Export excel file of Travel Order Masterfile.","export", "success", "gcceforms", "user");
            }elseif($export == 2){
                $this->core_layout->setEventLog("Trave Order Masterfile - Export csv file of Travel Order Masterfile.","export", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Trave Order Masterfile - Export pdf file of Travel Order Masterfile.","export", "success", "gcceforms", "user");
            }
        }

        function exportDataArchive($export){
            if($export == 1){
                $this->core_layout->setEventLog("Archive Travel Order - Export excel file of Archived Travel Order Masterfile.","export", "success", "gcceforms", "user");
            }elseif($export == 2){
                $this->core_layout->setEventLog("Archive Travel Order - Export csv file of Archived Travel Order Masterfile.","export", "success", "gcceforms", "user");
            }else{
                $this->core_layout->setEventLog("Archive Travel Order - Export pdf file of Archived Travel Order Masterfile.","export", "success", "gcceforms", "user");
            }
        }

        function getPersonnelName($id){
            $this->db->select('firstname, middlename, lastname');
            $data = $this->db->get_where("gccmaster.tblemployees", array("id"=>$id))->row();
            return $data->firstname." ".$data->middlename." ".$data->lastname;
        }

        function getDestinationDetails($id){
            $this->db->select('destination');
            return $this->db->get_where("gcceforms.travel_destination_temp", array("id"=>$id))->row('destination');            
        }

        function seriesV2($current_date, $code){
            $year = substr($current_date, 2, 2);
            $month = substr($current_date, 5, 2);
    
            $this->db->select('ref_series');
            $this->db->from('gcceforms.travel_order');
            $this->db->where('ref_yr', $year);
            $this->db->where('ref_month', $month);
            $this->db->order_by('ref_series', 'asc');
            $query = $this->db->get();
            $list = $query->result();
    
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
    
            return "{$code}{$year}-{$month}-{$series}";
        }

        public function addTravelOrderV2(){
            date_default_timezone_set('Asia/Singapore');
            $resultarray = array();
            $post = $this->input->post();
            $current_date = date("Y-m-d H:i:s");

            $tempRs = (array)$this->user_data;
            $fullname = $this->core_layout->getDisplayName($tempRs);
            $tempFullname = (object)$fullname;
            $created_by = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";

            $code = 'TO';
            $ref_no = $this->seriesV2($current_date, $code);
            $ref_series = explode("-",$ref_no)[2];
            $ref_month = explode("-",$ref_no)[1];
            $ref_yr = explode($code,explode("-",$ref_no)[0])[1];
            $ref_no = 'TO' . $ref_yr . '-' . $ref_month . '-' . $ref_series;

            $hitch = isset($post['is_hitch']) ? $post['is_hitch'] : 0;
            $service = isset($post['is_service']) ? $post['is_service'] : 0;
            $commute = isset($post['is_commute']) ? $post['is_commute'] : 0;
            $personal = isset($post['is_personal']) ? $post['is_personal'] : 0;
            $other = isset($post['is_other']) ? $post['is_other'] : 0;

            $vehicle = isset($post['vehicle']) ? $post['vehicle'] : 0;
            $driver = isset($post['driver']) ? $post['driver'] : 0;
            $driver_name = isset($post['driver']) ? $this->getName($driver) : '';
            
            $data = array(
                'ref_yr' => $ref_yr,
                'ref_series' => $ref_series,
                'ref_month' => $ref_month,
                'reference_no' => $ref_no,
                'company' => $post['company_id'],
                'department' => $post['dep_id'],
                'station' => $post['station'],
                'type' => $post['type'],
                'is_hitch' => $hitch,
                'is_service' => $service,
                'is_commute' => $commute,
                'is_personal' => $personal,
                'is_others' => $other,
                'others_remarks' => $post['remark'],
                'vehicle_id' => $vehicle,
                'driver_id' => $driver,
                'driver' => $driver_name,
                'created_by' => $created_by,
                'created_dt' => $current_date,
                'created_id' => $this->user_data['emp_id'],
                'status' => 'Pending',
            );

            // commented for rollback
            // $checkPersonnel = $this->checkPersonnelTO($this->user_data['id']);

            $query = $this->db->insert('gcceforms.travel_order', $data);
            if($query){

                $to_last_id = $this->db->insert_id();

                // save personnel
                $temp_personnel = $this->get_temp_personnel($this->user_data['id']);
                foreach ($temp_personnel as $row) {
                    $data = array(
                        'travel_order_id' => $to_last_id,
                        'employee_id' => $row->employee_id
                    );
                    $this->save_personnel($data);
                }
                $this->delete_temp_all_personnel($this->user_data['id']);
                
                // save destination
                $temp_destination = $this->get_temp_destination($this->user_data['id']);
                foreach ($temp_destination as $row) {
                    $data = array(
                        'travel_order_id' => $to_last_id,
                        'des_from' => $row->des_from,
                        'des_to' => $row->des_to,
                        'destination' => $row->destination,
                        'requested_by' => $row->requested_by,
                        'purpose' => $row->purpose,
                        'date_from' => $row->date_from,
                        'date_to' => $row->date_to,
                        'instructions' => $row->instructions,
                        'remarks' => $row->remarks,
                        'travel_from' => $row->travel_from,
                        'travel_to' => $row->travel_to,
                        'coords_from' => $row->coords_from,
                        'coords_to' => $row->coords_to,
                    );
                    $this->save_destination($data);
                }
                $this->delete_temp_all_destination($this->user_data['id']);

                $resultarray['status'] = true;
                $resultarray['msg'] = 'Travel order has been created successfully.';
                $resultarray["redirect"] = site_url("eforms/travel_order/view_travel_order?id={$to_last_id}");
            } else {
                $resultarray['status'] = false;
                $resultarray['msg'] = 'Failed to create new travel order entry!';
            }
            // if (empty($checkPersonnel)) {
            // } else {
            //     $resultarray['data'] = $checkPersonnel;
            //     $resultarray['status'] = false;
            //     $resultarray['msg'] = 'Failed to Create new Travel Order Entries.';
            // }

            return $resultarray;
        }

        public function updateTravelOrderV2(){
            date_default_timezone_set('Asia/Singapore');
            $resultarray = array();
            $post = $this->input->post();
            $current_date = date("Y-m-d H:i:s");
            $user_id = $this->session->userdata('logged_in')['emp_id'];
            $edited_by = $this->getName($user_id);
            $to_id = $post['to_id'];

            $hitch = isset($post['is_hitch']) ? $post['is_hitch'] : 0;
            $service = isset($post['is_service']) ? $post['is_service'] : 0;
            $commute = isset($post['is_commute']) ? $post['is_commute'] : 0;
            $personal = isset($post['is_personal']) ? $post['is_personal'] : 0;
            $other = isset($post['is_other']) ? $post['is_other'] : 0;
            $vehicle = isset($post['vehicle']) ? $post['vehicle'] : 0;
            $driver = isset($post['driver']) ? $post['driver'] : 0;
            $driver_name = isset($post['driver']) ? $this->getName($driver) : '';

            $data = array(
                'company' => $post['company_id'],
                'department' => $post['dep_id'],
                'station' => $post['station'],
                'type' => $post['type'],
                'is_hitch' => $hitch,
                'is_service' => $service,
                'is_commute' => $commute,
                'is_personal' => $personal,
                'is_others' => $other,
                'others_remarks' => $post['remark'],
                'last_edited_by' => $edited_by,
                'last_edited_dt' => $current_date,
                'last_edited_id' => $user_id,
                'vehicle_id' => $vehicle,
                'driver_id' => $driver,
                'driver' => $driver_name,
            );

            // commented for rollback
            // $checkPersonnel = $this->checkPersonnelEditTO($to_id);

            $reference_no = $this->db->get_where("gcceforms.travel_order", array("id"=>$to_id))->row('reference_no');
            if($this->update_travel_order(array('id' => $to_id), $data)){
                $resultarray['status'] = true;
                $resultarray['msg'] = 'Successfully update';
                $this->core_layout->setEventLog("Updated ".$reference_no.".","update", "success", "gcceforms", "user");
            }else{
                $resultarray['status'] = false;
                $resultarray['msg'] = 'Failed to update';
                $this->core_layout->setEventLog("Failed updating ".$reference_no.".","update", "error", "gcceforms", "system");
            }
            // if (empty($checkPersonnel)) {
            // } else {
            //     $resultarray['data'] = $checkPersonnel;
            //     $resultarray['status'] = false;
            //     $resultarray['msg'] = 'Failed to Create new Travel Order Entries.';
            // }

            return $resultarray;
        }

        public function approveTravelV2(){
            date_default_timezone_set('Asia/Singapore');
            $resultarray = array();
            $post = $this->input->post();
            $id = $post['id'];
            $approve_remarks = $post['approve_remarks'];
            $user_id = $this->session->userdata('logged_in')['emp_id'];
            $date = date('Y-m-d H:i:s');

            $data = array(
                'status' => 'Approved',
                'approved_remarks' => $approve_remarks,
                'approved_by' => $this->getName($user_id),
                'approved_dt' => $date,
            );

            if($this->checkAssignTravelType($id)){
                $post = $this->update_travel_order(array('id' => $id), $data);
                if ($post) {
                    $this->sendTelegram($id);

                    $message = "View Travel Order - Approve travel order ".$this->getReferenceNo($id).".";
                    $type = "success";
                    $action = "update";
                    $table = "user";
    
                    $resultarray['status'] = 'success';
                    $resultarray['msg'] = 'Successfully approved';
                }else{
                    $message = "View Travel Order - Failed approving travel order ".$this->getReferenceNo($id).".";
                    $type = "error";
                    $action = "update";
                    $table = "system";
    
                    $resultarray['status'] = 'error';
                    $resultarray['msg'] = 'Failed to approve';
                }
                $this->core_layout->setEventLog($message, $type, $action, "gcceforms", $table);
            } else {
                $resultarray['status'] = 'no_vehicle';
                $resultarray['msg'] = 'You need to assign vehicle & driver before approve.';
            }

            return $resultarray;
        }

        function approveRecommendTravelV2(){
            date_default_timezone_set('Asia/Singapore');
            $resultarray = array();
            $post = $this->input->post();
            $id = $post['id'];
            $approved_recommend_remarks = $post['approved_recommend_remarks'];
            $user_id = $this->session->userdata('logged_in')['emp_id'];
            $date = date('Y-m-d H:i:s');

            $data = array(
                'status' => 'Recommend_Approved',
                'approved_recommend_remarks' => $approved_recommend_remarks,
                'approved_recommend_by' => $this->getName($user_id),
                'approved_recommend_by_id' => $user_id,
                'approved_recommend_date' => $date,
            );

            $post = $this->update_travel_order(array('id' => $id), $data);
            if ($post) {
                $message = "View Travel Order - Approve recommend travel order ".$this->getReferenceNo($id).".";
                $type = "success";
                $action = "update";
                $table = "user";

                $resultarray['status'] = 'success';
                $resultarray['msg'] = 'Successfully approved';
            }else{
                $message = "View Travel Order - Failed approving recommend travel order ".$this->getReferenceNo($id).".";
                $type = "error";
                $action = "update";
                $table = "system";

                $resultarray['status'] = 'error';
                $resultarray['msg'] = 'Failed to approve';
            }
            $this->core_layout->setEventLog($message, $type, $action, "gcceforms", $table);

            return $resultarray;
        }

        function getDestinationById($id){
            $this->db->select("*");
            $this->db->from("gcceforms.travel_destination");
            $this->db->where('travel_order_id', $id);
            $query = $this->db->get();

            $array_response = array();
            $telegram = "";
            $sms = "";
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $tg_date_from = date_format(date_create($row->date_from),"F j, Y g:i a");
                    $tg_date_to = date_format(date_create($row->date_to),"F j, Y g:i a");

                    if($row->remarks == " "){
                        $telegram_remarks = "";
                    }else{
                        $telegram_remarks = '<b>REMARKS: </b>'.strtoupper($row->remarks).chr(10);
                    }

                    $telegram .= '<b>DESTINATION: </b>'.strtoupper($row->travel_from).' - '.strtoupper($row->travel_to).chr(10).'<b>DATE: </b>'.$tg_date_from.' - '.$tg_date_to.chr(10).$telegram_remarks.'<b>REQ BY: </b>'.strtoupper($this->getName($row->requested_by)).chr(10).'<b>PURPOSE: </b>'.strtoupper($row->purpose).chr(10).chr(10)."=";
                    $sms .= '- '.strtoupper($row->travel_from).' - '.strtoupper($row->travel_to).chr(10).'  DATE: '.$tg_date_from.' - '.$tg_date_to.chr(10);
                }
            }
            $array_response['telegram'] = $telegram;
            $array_response['sms'] = $sms;
            return $array_response;
        }

        function getPersonnelById($id){
            // $this->db->select("*");
            $this->db->select("employee_id");
            $this->db->from("gcceforms.travel_personnel");
            $this->db->where('travel_order_id', $id);
            $query = $this->db->get();

            $travel_personnel = "";
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $travel_personnel .= chr(10)."- ".strtoupper($this->getName($row->employee_id));
                    $this->sendDepartmentHeadEmail($row->employee_id, $id);
                }
            }
            return $travel_personnel;
        }

        function sendTelegramToPersonnelHeads($id, $telegram_msg){
            // $this->db->select("*");
            $this->db->select("employee_id");
            $this->db->from("gcceforms.travel_personnel");
            $this->db->where('travel_order_id', $id);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $head_id = $this->getTelegramId($row->employee_id);
                    if($head_id != 2){
                        $this->telegram_dept_heads($telegram_msg, $head_id);
                    }
                }
            }
        }

        function sendSMStoDriver($id, $reference_no, $destination, $pers){
            $this->db->select('mobile_no');
            $query = $this->db->get_where("gccmaster.tblemployees", array("id"=>$id));
            $msg = "This is to inform you that you have an assigned Travel Order (".$reference_no.") with personnel/s and destination/s as follows:\n\nPersonnel/s:".$pers."\n\nDestination/s:\n".$destination;
            return $this->contacts->sendSMS($query->row('mobile_no'), $msg);
        }

        function sendTelegram($id){
            $personnel = $this->getPersonnelById($id);
            $destination = $this->getDestinationById($id);
            $query = $this->db->get_where("gcceforms.travel_order", array("id"=>$id));
            $vehicle_details = '';

            if($query->row('is_service') > 0){
                $vehicle_name = $this->vehicle_details($query->row('vehicle_id'));
                $veh_name = $vehicle_name->name." | ".$vehicle_name->plateno;
                $vehicle_details = '<b>VEHICLE</b>: '.strtoupper($veh_name).chr(10).'<b>DRIVER</b>: '.strtoupper($query->row('driver')).chr(10).chr(10);
            }
            if($query->row('is_hitch') > 0){
                $vehicle_name = $this->vehicle_details($query->row('vehicle_id'));
                $veh_name = $vehicle_name->name." | ".$vehicle_name->plateno;
                $vehicle_details = '<b>Vehicle</b>: '.strtoupper($veh_name).chr(10).'<b>Driver</b>: '.strtoupper($query->row('driver')).chr(10).chr(10);
            }
            if($query->row('is_commute') > 0){
                $vehicle_details = '<b>VEHICLE</b>: COMMUTE'.chr(10);
            }
            if($query->row('is_personal') > 0){
                $vehicle_details = '<b>VEHICLE</b>: PERSONAL VEHICLE'.chr(10);
            }
            if($query->row('is_others') > 0){
                if($query->row('others_remarks') == ""){
                    $vehicle_details = "".chr(10);
                }else{
                    $vehicle_details = '<b>REMARKS</b>: '.strtoupper($query->row('others_remarks')).chr(10).chr(10);
                }
            }

            $dest = implode("=", (array)$destination['telegram']);
            $pers = implode(" ", (array)$personnel);
            $telegram_msg = '';
            $telegram_msg .= '<b>TO #</b>: '.$query->row('reference_no').chr(10);
            $telegram_msg .= '<b>FILE: </b>'.strtoupper($query->row('company')).chr(10);
            $telegram_msg .= '<b>PREP BY: </b>'.strtoupper($query->row('created_by')).chr(10);
            $telegram_msg .= '<b>PERSONNEL: </b>'.$pers.chr(10);
            $telegram_msg .= $vehicle_details;
            $telegram_msg .= str_replace("=","",$dest);
            if($this->telegram_config_if_exist('travel_order', 'count') > 0){
                $this->telegram($telegram_msg);

                $this->sendTelegramToPersonnelHeads($id, $telegram_msg);
            }

            // $this->sendSMStoDriver($query->row('driver_id'), $query->row('reference_no'), $destination['sms'][0], $pers);
        }

        function getPersonnelByIdDetails($id){
            $this->db->select("employee_id");
            $this->db->from("gcceforms.travel_personnel");
            $this->db->where('travel_order_id', $id);
            $query = $this->db->get();

            $affected = "";
            $affected .= "<ul>";

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $affected .= "<li>". strtoupper($this->getName($row->employee_id)). "</li>";
                }
            }
            $affected .= "</ul>";

            return $affected;
        }

        function getDestinationByIdDetails($id){
            $this->db->select("date_from, date_to, remarks, travel_from, travel_to");
            $this->db->from("gcceforms.travel_destination");
            $this->db->where('travel_order_id', $id);
            $query = $this->db->get();

            $affected = "";
            $affected .= "<ul>";

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $tg_date_from = date_format(date_create($row->date_from),"m-d H:i");
                    $tg_date_to = date_format(date_create($row->date_to),"m-d H:i");

                    if($row->remarks == " "){
                        $telegram_remarks = "";
                    }else{
                        $telegram_remarks = '<b>REMARKS: </b>'.strtoupper($row->remarks).chr(10);
                    }

                    $affected .= "<li>". strtoupper($row->travel_from).' - '.strtoupper($row->travel_to). "</li>";
                }
            }
            $affected .= "</ul>";

            return $affected;
        }

        public function getTravelOrderDetails(){
            $post = $this->input->post();
            $id = $post["id"];
            
            $personnel = $this->getPersonnelByIdDetails($id);
            $destination = $this->getDestinationByIdDetails($id);

            $this->db->select("*");
            $this->db->from("gcceforms.travel_order");
            $this->db->where("id", $id);
            $query = $this->db->get()->row();

            if($query->is_service > 0 || $query->is_hitch > 0){
                $vehicle_name = $this->vehicle_details($query->vehicle_id);
                
                $query->vehicle_name = $vehicle_name ? $vehicle_name->name." | ".$vehicle_name->plateno : 'NO ASSIGNED VEHICLE';
                $query->driver_name = $vehicle_name ? $query->driver : 'NO ASSIGNED DRIVER';
            }
            else if($query->is_others > 0){
                $query->others_remarks = $query->others_remarks=="" ? '' : strtoupper($query->others_remarks).chr(10).chr(10);
            }

            $query->personnel_list = $personnel;
            $query->destination_list = $destination;

            return $query;
        }

        function sendDepartmentHeadEmail($emp_id, $to_id){
            if($this->checkUserIsHead($emp_id) == 0){
                try {
                    $head_id = $this->getEmployeeDepartmentHead($emp_id);
                    $email = $this->db->get_where("gccmaster.tblemployees", array("id"=>$head_id))->row_array()['email'];
                    $this->sendEmailv2(true, $email, $to_id); // change email for live
                } catch (Exception $e) {

                }
            }
        }

        function checkUserIsHead($head_id){
            return $this->db->get_where("gcchris.tbldepartments", array("head_id"=>$head_id))->num_rows();
        }

        function getEmployeeDepartmentHead($emp_id){
            $this->db->select("b.head_id");
            $this->db->from("gccmaster.tblemployees a");
            $this->db->join("gcchris.tbldepartments b", "b.id = a.department_id", "LEFT");
            $this->db->where("a.id", $emp_id);
            return $this->db->get()->row_array()['head_id'];
        }

        function checkEmail($email){
            $find1 = strpos($email, '@');
            $find2 = strpos($email, '.');
            return ($find1 !== false && $find2 !== false && $find2 > $find1);
        }

        function sendEmail($email, $email_address, $id){
            if($this->checkEmail($email_address)){
                $data = $this->travel_order_details($id)['data'];
                $data->destination = $this->travel_order_details($id)['destination'];
                $data->personnel = $this->travel_order_details($id)['personnel'];
    
                if (is_numeric($data->vehicle_id)) {
                    $vehicle_data = $this->vehicle_details($data->vehicle_id);
                    // $data->plateno = $vehicle_data['plateno']; -> original source code that causing error 'Cannot use object of type stdClass as array'
                    $data->plateno = $vehicle_data->plateno;
                } else {
                    $data->plateno = $data->vehicle_id;
                }

                if($data){
                    $messageContent = "";
                    $messageContent .= $this->load->view("eforms/email_templates/email_to_request_template", $data, true);

                    if($email){
                        $module = "eforms_to_request";
                        $email_title = "Travel Order Request";
                        $content_title = "Travel Order Statement";
                        $content = $messageContent;

                        $overrideMailer = array();
                        $overrideMailer["send_to"] = array($email_address);

                        if($content){
                            $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
                            if($sent){
                                return true;
                            }else{
                                return false;
                            }
                        }else{
                            return false;
                        }
                    }else{
                        echo $messageContent;
                    }
                }
            } else {
                return false;
            }
        }

        public function accomplishTravelV2(){
            $response_array['response'] = array();
            $post = $this->input->post();
            $id = $post["param_id"];
            $accomplishment_dt = $post["accomplishment_dt"];
            $unaccomplished_remarks = isset($post["unacomplish_remarks"]) ? $post["unacomplish_remarks"] : '';
            $accomplishment_remarks = isset($post["accomplishment_remarks"]) ? $post["accomplishment_remarks"] : '';
            $tempaccompished = 1;

            if(isset($post["globalTempSelected"])){
                $_dest = $this->db->get_where('gcceforms.travel_destination', array('travel_order_id' => $id))->num_rows();
                $globalTempSelected = explode(",", $post["globalTempSelected"]);
                $destinationcount = count($globalTempSelected);
                $tempaccompished = ($_dest == $destinationcount) ? 1 : 0;
            }

            $data = array(
                'accomplishment_dt' => $accomplishment_dt,
                'unaccomplished_remarks' => $unaccomplished_remarks,
                'accomplishment_remarks' => $accomplishment_remarks,
                'accomplished' => $tempaccompished,
                'accomplished_by' => $tempaccompished == 1 ? $this->user_data['emp_id'] : 0
            );

            $update_to = $this->update_travel_order(array('id' => $id), $data);
            if($update_to){

                if(isset($post["globalTempSelected"])){ // accomplish from view travel order
                    $globalTempSelected = explode(",", $post["globalTempSelected"]);
                    if(count($globalTempSelected) > 0){
                        foreach($globalTempSelected as $destination_id){
                            $this->update_destination(array('id' => $destination_id), array('accomplished' => 1), $id);
                        }
                    }
                } else { // accomplish from masterfile
                    $this->update_destination(array('travel_order_id' => $id), array('accomplished' => 1), $id);
                }

                if($update_to){
                    $message = "View Travel Order - Accomplish travel order ".$this->getReferenceNo($id).".";
                    $type = "success";
                    $action = "update";
                    $table = "user";
                    $response_array['status'] = true;
                } else {
                    $message = "View Travel Order - Failed accomplish travel order ".$this->getReferenceNo($id).".";
                    $type = "error";
                    $action = "update";
                    $table = "user";
                    $response_array['status'] = false;
                }
                $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);
            }

            return $response_array;
        }

        public function undoAccomplishTravelV2(){
            $response_array['response'] = array();
            $post = $this->input->post();
            $id = $post["param_id"];

            $data = array(
                'accomplishment_dt' => '0000-00-00 00:00:00',
                'unaccomplished_remarks' => '',
                'accomplished' => 0,
            );
            $update_to = $this->update_travel_order(array('id' => $id), $data);
            if($update_to){

                $this->update_destination(array('travel_order_id' => $id), array('accomplished' => 0), $id);

                $message = "View Travel Order - Undo accomplish travel order ".$this->getReferenceNo($id).".";
                $type = "success";
                $action = "update";
                $table = "user";
                $response_array['status'] = true;
            } else {
                $message = "View Travel Order - Failed undo accomplish travel order ".$this->getReferenceNo($id).".";
                $type = "error";
                $action = "update";
                $table = "user";
                $response_array['status'] = false;
            }
            $this->core_layout->setEventLog($message, $action, $type, "gcceforms", $table);

            return $response_array;
        }

        public function testFunction(){
            // $data = $this->travel_order_details('42007');
            // $data->destination = $this->get_destination($data->id);
            // $data->personnel = $this->get_personnel($data->id);

            // if (is_numeric($data->vehicle_id)) {
            //     $vehicle_data = $this->vehicle_details($data->vehicle_id);
            //     $data->plateno = $vehicle_data->plateno;
            // } else {
            //     $data->plateno = $data->vehicle_id;
            // }

            // echo $this->load->view("eforms/email_templates/email_to_request_template", $data, true);
            // //return $data;

            return $this->updateMisingDBfield();
        }

        function updateMisingDBfield(){
            $array_response = array();
            $this->db->select("created_by, id, last_edited_by");
            $this->db->from("gcceforms.travel_order");
            //$this->db->limit(50, 0);
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $list = array();

                $id = $this->getEmployeeID($row->created_by);
                if($id){
                    $status_created = $this->updateTOCreatedID($row->id, $id);
                    $list['created_id'] = $status_created;
                    array_push($array_response, $list);
                }

                $id = $this->getEmployeeID($row->last_edited_by);
                if($id){
                    $status_last_edited = $this->updateTOLastEditID($row->id, $id);
                    $list['edited_id'] = $status_last_edited;
                    array_push($array_response, $list);
                }
            }

            return $array_response;
        }

        function getEmployeeID($created_by){
            $created_by = TRIM($created_by);
            $this->db->select("id");
            $this->db->from("gccmaster.tblemployees");
            $this->db->like("concat(firstname,' ',lastname)", $created_by);
            $this->db->or_like("concat(firstname,' ',SUBSTR(middlename,1,1),'. ',lastname)", $created_by);
            $this->db->or_like("concat(firstname,' ',SUBSTR(middlename,1,1),'. ',lastname,' ',trim(suffix))", $created_by);
            $query = $this->db->get();
            if($query->num_rows() == 1){
                return $query->row()->id;
            } else {
                return false;
            }
        }

        function updateTOCreatedID($to_id, $employee_id){
            $data = array(
                'created_id' => $employee_id,
            );
            return $this->update_travel_order(array('id' => $to_id), $data);
        }

        function updateTOLastEditID($to_id, $employee_id){
            $data = array(
                'last_edited_id' => $employee_id,
            );
            return $this->update_travel_order(array('id' => $to_id), $data);
        }

        // -----------------------------------------------------------------------------

        function checkAssignTravelType($id){
            $this->db->select('is_service, is_hitch, driver_id, vehicle_id, is_others, is_personal, is_commute');
            $query = $this->db->get_where("gcceforms.travel_order", array("id"=>$id));
            if((($query->row('is_service') > 0 || $query->row('is_hitch') > 0) && $query->row('driver_id') > 0 && $query->row('vehicle_id') > 0) || 
                ($query->row('is_others') > 0 || $query->row('is_personal') > 0 || $query->row('is_commute') > 0)){
                return true;
            }
            return false;
        }

        function getReferenceNo($id){
            $this->db->select("reference_no");
            return $this->db->get_where("gcceforms.travel_order", array("id"=>$id))->row('reference_no');
        }

        function getName($id) {
            $this->db->select("id, firstname, middlename, lastname, suffix");
            $this->db->from("gccmaster.tblemployees");
            $this->db->where("id", $id);
            $query = $this->db->get();
            $rs = $query->row();
            $tempRs = (array)$rs;
            $fullname = $this->core_layout->getDisplayName($tempRs);
            $tempFullname = (object)$fullname;
            return isset($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
        }

        function get_today_to(){
            $now = date('Y-m-d');
            $arrData = array();
            $this->db->select("a.station, a.type, a.company, a.department, a.id, a.reference_no, a.company,a.driver, a.status, a.vehicle_id, a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks,a.accomplishment_dt, tod.destination, toe.firstname, toe.lastname, a.created_dt, tod.date_to, tod.date_from, tod.purpose");
            $this->db->from("gcceforms.travel_order a");
            $this->db->join('gcceforms.travel_destination tod', 'tod.travel_order_id = a.id', 'left');
            $this->db->join('gcceforms.travel_personnel top', 'top.travel_order_id = a.id', 'left');
            $this->db->join('gccmaster.tblemployees toe', 'toe.id = top.employee_id', 'left');
            $this->db->where("a.status =", "Approved");
            $this->db->where('DATE(tod.date_from)', $now);
            $this->db->where('DATE(tod.date_to)', $now);
            $this->db->order_by("a.id", "DESC");
            $this->db->group_by('a.id');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rs) {
                    $rowId = $rs->id;
                    $this->db->select("firstname, lastname, middlename, suffix");
                    $this->db->from("gcceforms.travel_personnel tp");
                    $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
                    $this->db->where("tp.travel_order_id", $rowId);
                    $personnel = $this->db->get();

                    $rowPersonnel = array();
                    $rowDestination = array();
                    $rowDriver = "";
                    $rowVehiclePlate = "";
                    $rowVehicleDesc = "";
                    $rowFromTo = "";

                    if ($personnel->num_rows() > 0) {
                        foreach ($personnel->result() as $key => $value) {
                            $tempRs = (array)$value;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $value->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            $rowPersonnel[] = $value->display_name;
                        }
                    }

                    if ($rs->is_service == "1") {
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();

                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $tempRs = (array)$rowData;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            if ($rowData->display_name) {
                                $rowDriver = $rowData->display_name;
                            } else {
                                $rowDriver = $rs->driver;
                            }
                        }

                        $this->db->select("plateno, description, name");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->name;
                        }
                    }


                    if ($rs->is_hitch == "1") {
                        $this->db->select("firstname, lastname, middlename, suffix");
                        $this->db->from("gccmaster.tblemployees te");
                        $this->db->where("te.id", $rs->driver_id);
                        $driver = $this->db->get();

                        if ($driver->num_rows() == 1) {
                            $rowData = $driver->row();
                            $tempRs = (array)$rowData;
                            $fullname = $this->core_layout->getDisplayName($tempRs);
                            $tempFullname = (object)$fullname;
                            $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                            if ($rowData->display_name) {
                                $rowDriver = $rowData->display_name;
                            } else {
                                $rowDriver = $rs->driver;
                            }
                        }

                        $this->db->select("plateno, description, name");
                        $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $rs->vehicle_id));
                        if ($queryVehicle->num_rows() == 1) {
                            $rowVehicle = $queryVehicle->row();
                            $rowVehiclePlate = $rowVehicle->plateno;
                            $rowVehicleDesc = $rowVehicle->name;
                        }
                    }


                    $this->db->select("td.destination, td.date_from, td.date_to");
                    $this->db->from("gcceforms.travel_destination td");
                    $this->db->where("td.travel_order_id", $rowId);

                    $destination = $this->db->get();
                    $destinationDateTime = array();

                    if ($destination->num_rows() > 0) {
                        $des_num = 0;
                        foreach ($destination->result() as $key => $vx) {
                            $des_num++;
                            $rowDestination[] = $vx->destination;
                            $x = explode(' ', $vx->date_from);
                            $y = explode(' ', $vx->date_to);

                            $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                            $z = explode(' ', $vx->date_from);

                            if ($z[0] != $x[0]) {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            } else if ($x[0] == $y[0]) {
                                $date_end = date("g:i A", strtotime($y[1]));
                            } else {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            }
                            
                            $date = $date_start . "-" . $date_end;
                            array_push($destinationDateTime, $date);
                        }
                    }

                    $final  = array();

                    foreach ($destinationDateTime as $current) {
                        if ( ! in_array($current, $final)) {
                            $final[] = $current;
                        }
                    }

                    $rs->personnels = $rowPersonnel;
                    $rs->driver = $rowDriver;
                    $rs->vehicle_plate = $rowVehiclePlate;
                    $rs->vehicle_description = $rowVehicleDesc;
                    $rs->destination = $rowDestination;
                    $rs->from_to = $final;
                    $rs->company_detail = $this->getCompany($rs->company);
                    $rs->user_role_id = $this->authenticate->getRoleId();
                    $rs->created_dt = date("m-d-Y h:i A", strtotime( $rs->created_dt));
                    $arrData[] = $rs;
                }
            }
            return $arrData;
        }

        function send_to_email($result, $email_address = null){
            if($result){
                $messageContent = "";
                $messageContent .= $this->load->view("eforms/email_templates/email_daily_travelorder_template", $result, true);

                $module = "eforms_to_request";
                $email_title = "Travel Order Daily Report";
                $content_title = "Travel Order Report";
                $content = $messageContent;

                $overrideMailer = array();
                $overrideMailer["send_to"] = ($email_address) ? array($email_address) : "";

                if($content){
                    $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
                    if($sent){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }

        function getTravelOrderTempPersonnelList(){
            $post = $this->input->post();

            $rowCount = 0;
            $rowData = array();
            $resultset = array();
            $order = array(array("column" => "1", "dir" => "desc"));

            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy = (isset($post["columns"]) && $post["columns"]) ? $post["columns"] : 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order;

            $rowData = $this->tempPersonnelList($search, $limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->tempPersonnelCount($search);

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function tempPersonnelList($search=null, $limit = 10, $offset = 0, $sortBy, $sortOrder) {
            $filterFields = array("b.firstname", "b.lastname", "d.firstname", "d.lastname");
            $this->db->select("a.id, a.created_at, b.firstname, d.firstname,
            UCASE(CONCAT(b.firstname, ' ',
                    CASE
                        WHEN b.middlename IS NOT NULL AND b.middlename != '' THEN CONCAT(' ', substr(b.middlename,1,1),'.')
                        ELSE ''
                    END,
                    ' ', b.lastname,
                    CASE
                        WHEN b.suffix IS NOT NULL AND b.suffix != '' AND b.suffix != 'N/A' AND b.suffix != 'NONE' THEN CONCAT(' ', b.suffix)
                        ELSE ''
                    END)) as employee_name,
            UCASE(CONCAT(d.firstname, ' ',
                    CASE
                        WHEN d.middlename IS NOT NULL AND d.middlename != '' THEN CONCAT(' ', substr(d.middlename,1,1),'.')
                        ELSE ''
                    END,
                    ' ', d.lastname,
                    CASE
                        WHEN d.suffix IS NOT NULL AND d.suffix != '' AND d.suffix != 'N/A' AND d.suffix != 'NONE' THEN CONCAT(' ', d.suffix)
                        ELSE ''
                    END)) as added_by, DATE_FORMAT(a.created_at, '%M %d, %Y %h:%i:%s %p') as created_at_formatted, 
                    IF(pos.id IS NULL, b.position, pos.name) as position");
            $this->db->from('gcceforms.travel_personnel_temp a');
            $this->db->join('gccmaster.tblemployees b', 'b.id = a.employee_id', 'left');
            $this->db->join('gccmaster.tblusers c', 'c.id = a.user_id', 'left');
            $this->db->join('gccmaster.tblemployees d', 'd.id = c.emp_id', 'left');
            $this->db->join('gcchris.tblposition pos', 'pos.id = b.position', 'left');

            if(isset($search)){
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

            if ($query->num_rows() > 0) {
                $arrData = array();
                foreach ($query->result() as $key => $rs) {
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

        private function tempPersonnelCount($search=null) {
            $filterFields = array("b.firstname", "b.lastname", "d.firstname", "d.lastname");
            $this->db->select("a.id, a.created_at, b.firstname, d.firstname,
            UCASE(CONCAT(b.firstname, ' ',
                    CASE
                        WHEN b.middlename IS NOT NULL AND b.middlename != '' THEN CONCAT(' ', substr(b.middlename,1,1),'.')
                        ELSE ''
                    END,
                    ' ', b.lastname,
                    CASE
                        WHEN b.suffix IS NOT NULL AND b.suffix != '' AND b.suffix != 'N/A' AND b.suffix != 'NONE' THEN CONCAT(' ', b.suffix)
                        ELSE ''
                    END)) as employee_name,
            UCASE(CONCAT(d.firstname, ' ',
                    CASE
                        WHEN d.middlename IS NOT NULL AND d.middlename != '' THEN CONCAT(' ', substr(d.middlename,1,1),'.')
                        ELSE ''
                    END,
                    ' ', d.lastname,
                    CASE
                        WHEN d.suffix IS NOT NULL AND d.suffix != '' AND d.suffix != 'N/A' AND d.suffix != 'NONE' THEN CONCAT(' ', d.suffix)
                        ELSE ''
                    END)) as added_by, DATE_FORMAT(a.created_at, '%M %d, %Y %h:%i:%s %p') as created_at_formatted, 
                    IF(pos.id IS NULL, b.position, pos.name) as position");
            $this->db->from('gcceforms.travel_personnel_temp a');
            $this->db->join('gccmaster.tblemployees b', 'b.id = a.employee_id', 'left');
            $this->db->join('gccmaster.tblusers c', 'c.id = a.user_id', 'left');
            $this->db->join('gccmaster.tblemployees d', 'd.id = c.emp_id', 'left');
            $this->db->join('gcchris.tblposition pos', 'pos.id = b.position', 'left');

            if(isset($search)){
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
            $count = $query->num_rows();
            return $count;
        }

        public function removeTemporaryEmployeeData(){
            $resultset = array();
            $post = $this->input->post();
            if(isset($post["id"]) && $post["id"]){
                $where = array("id" => $post["id"]);
                $removed = $this->db->delete("gcceforms.travel_personnel_temp", $where);
                if($removed && $this->db->affected_rows() > 0){
                    $resultset["response"] = true;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
            return $resultset;
        }

        public function getAccomplishmentReport(){
            $resultset = array();
            $post = $this->input->post();
            $order_val = array(array("column"=>"0", "dir"=>"desc"));
            $search = (isset($post["search"]['value']) && $post["search"]['value']) ? $post["search"]['value'] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sortBy =  (isset($post["columns"]) && $post["columns"])? $post["columns"]: 1;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"] : $order_val;
            $startDate = (isset($post['startDate']) && $post['startDate']) ? $post['startDate'] : null;
            $endDate = (isset($post['endDate']) && $post['endDate']) ? $post['endDate'] : null;
            $department = (isset($post['department']) && $post['department']) ? $post['department'] : null;
            $company = (isset($post['company']) && $post['company']) ? $post['company'] : null;

            $rowCount = 0;
            $rowData = array();

            $rowData = $this->get_accomplished_item($company, $department, $limit, $offset, $sortBy, $sortOrder, $search, $startDate, $endDate);
            $rowCount = $this->get_accomplished_item_count($company, $department, $search, $startDate, $endDate);

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        public function get_accomplished_item($company = null, $department = null, $limit, $offset, $sortBy, $sortOrder, $search = null, $startDate = null, $endDate = null){
            $result = array();

            $filterFields = array('a.station', 'a.type', 'a.company', 'a.department', 'a.reference_no', 'toe.firstname', 'toe.lastname');
            $role_id = $this->authenticate->getRoleId();

            $sql = "a.station, a.type, a.company, a.department, a.id, a.reference_no, a.company, a.driver, a.status, a.vehicle_id, 
            a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks, a.accomplishment_dt, tod.destination, 
            toe.firstname, toe.lastname, a.created_dt, a.accomplished, a.accomplished_by, 
            UPPER(CONCAT(emp.lastname,
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END, ', ', emp.firstname, ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END)) as accomplished_name";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_order a");
            $this->db->join('gcceforms.travel_destination tod', 'tod.travel_order_id = a.id', 'left');
            $this->db->join('gcceforms.travel_personnel top', 'top.travel_order_id = a.id', 'left');
            $this->db->join('gccmaster.tblemployees toe', 'toe.id = top.employee_id', 'left');
            $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
            $this->db->join('gccmaster.tblemployees emp', 'emp.id = a.accomplished_by', 'left');
            $this->db->where("a.status", "Approved");
            $this->db->where("a.accomplished", 1);

            if($startDate == $endDate){
                $this->db->where('DATE(td.date_from)', $startDate);
                $this->db->where('DATE(td.date_to)', $endDate);
            }else{
                $this->db->group_start();
                $this->db->where("DATE(td.date_from) >=", $startDate);
                $this->db->where("DATE(td.date_from) <=", $endDate);
                $this->db->group_end();
            }

            if($company){
                $this->db->where_in('a.company', $company);
            }

            if($department){
                $this->db->where_in('a.department', $department);
            }

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

            $this->db->group_by('a.id');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $personnel = null;
                $service = null;
                $hitch = null;

                foreach ($query->result() as $rs) {
                    $rowDestination = array();
                    $personnel = $this->get_accomplished_personnel($rs->id);

                    if($rs->is_service == "1"){
                        $service = (object) $this->get_accomplished_service($rs->driver_id, $rs->driver, $rs->vehicle_id);

                        $rs->driver = $service->driver;
                        $rs->vehicle_plate = $service->vehicle_plate;
                        $rs->vehicle_description = $service->vehicle_desc;
                        
                    }

                    if($rs->is_hitch == "1") {
                        $hitch = (object) $this->get_accomplished_hitched($rs->driver_id, $rs->driver, $rs->vehicle_id);

                        $rs->driver = $hitch->driver;
                        $rs->vehicle_plate = $hitch->vehicle_plate;
                        $rs->vehicle_description = $hitch->vehicle_desc;
                    }

                    $this->db->select("td.destination, td.date_from, td.date_to");
                    $this->db->from("gcceforms.travel_destination td");

                    if ($startDate && $endDate) {
                        $this->db->group_start();
                        $this->db->where("DATE(td.date_from) >=", $startDate);
                        $this->db->where("DATE(td.date_from) <=", $endDate);
                        $this->db->group_end();
                        $this->db->where("td.travel_order_id", $rs->id);
                    } else {
                        $this->db->where("td.travel_order_id", $rs->id);
                    }

                    $destination = $this->db->get();

                    if ($destination->num_rows() > 0) {
                        $des_num = 0;
                        foreach ($destination->result() as $key => $vx) {
                            $destinationDateTime = array();
                            $des_num++;
                            $rowDestination[] = $vx->destination;
                            $x = explode(' ', $vx->date_from);
                            $y = explode(' ', $vx->date_to);

                            $date_start = date("M d, Y g:i A", strtotime($vx->date_from));
                            $z = explode(' ', $vx->date_from);

                            if ($z[0] != $x[0]) {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            } else if ($x[0] == $y[0]) {
                                $date_end = date("g:i A", strtotime($y[1]));
                            } else {
                                $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                            }
                            
                            $date = $date_start . "-" . $date_end;
                            array_push($destinationDateTime, $date);
                        }
                    }

                    $final  = array();

                    foreach ($destinationDateTime as $current) {
                        if ( ! in_array($current, $final)) {
                            $final[] = $current;
                        }
                    }

                    $rs->personnel = $personnel;
                    $rs->from_to = $final;
                    $rs->destination = $rowDestination;
                    $rs->company_detail = $this->getCompany($rs->company);
                    $rs->created_dt = date("M d, Y h:i A", strtotime( $rs->created_dt));
                    $result[] = $rs;
                }
            }

            return $result;
        }

        public function get_accomplished_item_count($company = null, $department = null, $search = null, $startDate = null, $endDate = null){
            $filterFields = array('a.station', 'a.type', 'a.company', 'a.department', 'a.reference_no', 'toe.firstname', 'toe.lastname');

            $sql = "a.station, a.type, a.company, a.department, a.id, a.reference_no, a.company, a.driver, a.status, a.vehicle_id, 
            a.driver_id, a.is_service, a.is_hitch, a.is_commute, a.is_personal, a.is_others, a.others_remarks, a.accomplishment_dt";

            $this->db->select($sql);
            $this->db->from("gcceforms.travel_order a");
            $this->db->join('gcceforms.travel_personnel top', 'top.travel_order_id = a.id', 'left');
            $this->db->join('gccmaster.tblemployees toe', 'toe.id = top.employee_id', 'left');
            $this->db->join("gcceforms.travel_destination td","td.travel_order_id = a.id");
            $this->db->where("a.status", "Approved");
            $this->db->where("a.accomplished", 1);

            if($startDate == $endDate){
                $this->db->where('DATE(td.date_from)', $startDate);
                $this->db->where('DATE(td.date_to)', $endDate);
            }else{
                $this->db->group_start();
                $this->db->where("DATE(td.date_from) >=", $startDate);
                $this->db->where("DATE(td.date_from) <=", $endDate);
                $this->db->group_end();
            }

            if($company){
                $this->db->where_in('a.company', $company);
            }

            if($department){
                $this->db->where_in('a.department', $department);
            }

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

            $this->db->group_by('a.id');
            $query = $this->db->get();
            
            return $query->num_rows();
        }

        public function get_accomplished_personnel($id){
            $rowPersonnel = array();

            $select = 'firstname, lastname, middlename, suffix';

            $this->db->select($select);
            $this->db->from("gcceforms.travel_personnel tp");
            $this->db->join("gccmaster.tblemployees te", "te.id = tp.employee_id", "left");
            $this->db->where("tp.travel_order_id", $id);
            $personnel = $this->db->get();

            if ($personnel->num_rows() > 0) {
                foreach ($personnel->result() as $key => $value) {
                    $tempRs = (array)$value;
                    $fullname = $this->core_layout->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $value->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $rowPersonnel[] = $value->display_name;
                }
            }
                
            return $rowPersonnel;
        }

        public function get_accomplished_service($id, $driver, $vehicle){
            $result = array();
            $rowDriver = "";
            $rowVehiclePlate = "";
            $rowVehicleDesc = "";

            $select = 'firstname, lastname, middlename, suffix';
            $this->db->select($select);
            $this->db->from("gccmaster.tblemployees te");
            $this->db->where("te.id", $id);
            $driver = $this->db->get();

            if ($driver->num_rows() == 1) {
                $rowData = $driver->row();
                $tempRs = (array)$rowData;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                if ($rowData->display_name) {
                    $rowDriver = $rowData->display_name;
                } else {
                    $rowDriver = $driver;
                }
            }

            $this->db->select("plateno, description, name");
            $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $vehicle));

            if ($queryVehicle->num_rows() == 1) {
                $rowVehicle = $queryVehicle->row();
                $rowVehiclePlate = $rowVehicle->plateno;
                $rowVehicleDesc = $rowVehicle->name;
            }

            $result['driver'] = $rowDriver;
            $result['vehicle_plate'] = $rowVehiclePlate;
            $result['vehicle_desc'] = $rowVehicleDesc;

            return $result;
        }

        public function get_accomplished_hitched($id, $driver, $vehicle){
            $result = array();
            $rowDriver = "";
            $rowVehiclePlate = "";
            $rowVehicleDesc = "";

            $select = "firstname, lastname, middlename, suffix";
            $this->db->select($select);
            $this->db->from("gccmaster.tblemployees te");
            $this->db->where("te.id", $id);
            $driver = $this->db->get();

            if ($driver->num_rows() == 1) {
                $rowData = $driver->row();
                $tempRs = (array)$rowData;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object)$fullname;
                $rowData->display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                if ($rowData->display_name) {
                    $rowDriver = $rowData->display_name;
                } else {
                    $rowDriver = $driver;
                }
            }

            $this->db->select("plateno, description, name");
            $queryVehicle = $this->db->get_where("gccasset.vehicles", array("id" => $vehicle));
            if ($queryVehicle->num_rows() == 1) {
                $rowVehicle = $queryVehicle->row();
                $rowVehiclePlate = $rowVehicle->plateno;
                $rowVehicleDesc = $rowVehicle->name;
            }

            $result['driver'] = $rowDriver;
            $result['vehicle_plate'] = $rowVehiclePlate;
            $result['vehicle_desc'] = $rowVehicleDesc;

            return $result;
        }

        function sendEmailv2($email, $email_address, $id){
            if($this->checkEmail($email_address)){
                $data = $this->travel_order_detailsv2($id)['data'];
                $data->destination = $this->travel_order_detailsv2($id)['destination'];
                $data->personnel = $this->travel_order_detailsv2($id)['personnel'];
                $data->personnel = json_decode(json_encode($data->personnel));
                $data->destination = json_decode(json_encode($data->destination));
                if (is_numeric($data->vehicle_id) && $data->vehicle_id != 0 && !empty($data->vehicle_id)) {
                    $vehicle_data = $this->vehicle_details($data->vehicle_id);
                    // $data->plateno = $vehicle_data['plateno']; -> original source code that causing error 'Cannot use object of type stdClass as array'
                    $data->plateno = $vehicle_data->plateno;
                } else {
                    $data->plateno = $data->vehicle_id;
                }

                if($data){
                    $messageContent = "";
                    $messageContent .= $this->load->view("eforms/email_templates/email_to_request_template", $data, true);

                    if($email){
                        $module = "eforms_to_request";
                        $email_title = "Travel Order Request";
                        $content_title = "Travel Order Statement";
                        $content = $messageContent;

                        $overrideMailer = array();
                        $overrideMailer["send_to"] = array($email_address);

                        if($content){
                            $sent = $this->core_layout->send_email($module, $email_title, $content_title, $content, $overrideMailer);
                            if($sent){
                                return true;
                            }else{
                                return false;
                            }
                        }else{
                            return false;
                        }
                    }else{
                        echo $messageContent;
                    }
                }
            } else {
                return false;
            }
        }

        function checkPersonnelTO($userId) {
            $_personnelIds = array();
            $_destinationLocation = array();
            $arrData = array();
            
            $temp_personnel = $this->get_temp_personnel($this->user_data['id']);
            $temp_destination = $this->get_temp_destination($this->user_data['id']);

            foreach($temp_personnel as $row) {
                $_personnelIds[] = $row->employee_id;
            }

            foreach($temp_destination as $row) {
                $data = array(
                    'date_from' => $row->date_from,
                    'date_to' => $row->date_to
                );

                $_destinationLocation[] = $data;
            }

            $travelDates = $this->getFirstAndLastArrValue($temp_destination);

            $this->db->select('a.reference_no, c.date_from, c.date_to, c.destination, UPPER(CONCAT(d.firstname, " ", d.lastname)) as name, a.status, b.employee_id');
            $this->db->join($this->travelPersonnelTable.' as b', 'b.travel_order_id = a.id', 'LEFT');
            $this->db->join($this->travelDestinationTable.' as c', 'c.travel_order_id = a.id', 'LEFT');
            $this->db->join($this->employeeTable.' as d', 'd.id = b.employee_id', 'LEFT');
            $this->db->from($this->travelOrderTable.' as a');
            $this->db->where_in('b.employee_id', $_personnelIds);

            $this->db->where('a.status !=', 'Cancelled');
            $this->db->where('a.status !=', 'Disapproved');

            $this->db->group_start();
            $this->db->where('a.status !=', 'Approved');
            $this->db->where('a.accomplished', 0);
            $this->db->group_end();
            
            $this->db->or_group_start();
            $this->db->where('a.status', 'Approved');
            $this->db->where('a.accomplished', 0);
            $this->db->group_end();

            $this->db->group_start();
                $this->db->where('DATE(c.date_from) >= ', date('Y-m-d', strtotime($travelDates['date_from'])));
                $this->db->where('DATE(c.date_from) <= ', date('Y-m-d', strtotime($travelDates['date_from'])));
                $this->db->or_where('DATE(c.date_to) >=', date('Y-m-d', strtotime($travelDates['date_to'])));
                $this->db->where('DATE(c.date_to) <=', date('Y-m-d', strtotime($travelDates['date_to'])));
            $this->db->group_end();

            $query = $this->db->get();
            
            if ($query->num_rows() > 0){
                foreach($query->result() as $key => $rs) {
                    if (in_array($rs->employee_id, $_personnelIds)) {
                        foreach ($_destinationLocation as $k => $row) {
                            $savedFromDes = date('Y-m-d H:i', strtotime($rs->date_from));
                            $savedToDes = date('Y-m-d H:i', strtotime($rs->date_to));
    
                            $toSavedFromDes = date('Y-m-d H:i', strtotime($row['date_from']));
                            $toSavedToDes = date('Y-m-d H:i', strtotime($row['date_to']));
    
                            if (($savedFromDes >= $toSavedFromDes && $savedFromDes <= $toSavedToDes) || ($savedToDes >= $toSavedFromDes && $savedToDes <= $toSavedToDes) || ($savedFromDes <= $toSavedFromDes && $savedToDes >= $toSavedToDes)) {
                               $arrData[$key]['reference_no'] = $rs->reference_no;
                               $arrData[$key]['name'] = $rs->name;
                               $arrData[$key]['status'] = $rs->status;
                               $arrData[$key]['to'][] = ['destination' => $rs->destination, 'date_from' => $rs->date_from, 'date_to' => $rs->date_to];
                            }
                        }
                    }

                }
            }

            $restructuredArray = [];

            foreach ($arrData as $entry) {
                $name = $entry['name'];
                $referenceNo = $entry['reference_no'];
                $status = $entry['status'];

                if (!isset($restructuredArray[$name])) {
                    $restructuredArray[$name] = [
                        'reference_no' => $referenceNo,
                        'name' => $name,
                        'status' => $status,
                        'to' => []
                    ];
                }

                $restructuredArray[$name]['to'] = array_merge($restructuredArray[$name]['to'], $entry['to']);
            }

            // Convert associative array to indexed array
            $restructuredArray = array_values($restructuredArray);

            return $restructuredArray;
        }

        function getFirstAndLastArrValue($arr){
            $first = reset($arr);
            $last = end($arr);

            return ['date_from' => $first->date_from, 'date_to' => $last->date_to];
        }

        function checkPersonnelEditTO($id) {
            $_personnelIds = array();
            $_destinationLocation = array();
            $arrData = array();

            $personnels = $this->db->select('employee_id')->get_where($this->travelPersonnelTable, array('travel_order_id' => $id))->result();
            $destinations = $this->db->select('date_from, date_to')->get_where($this->travelDestinationTable, array('travel_order_id' => $id))->result();

            foreach($personnels as $row) {
                $_personnelIds[] = $row->employee_id;
            }

            foreach($destinations as $row) {
                $data = array(
                    'date_from' => $row->date_from,
                    'date_to' => $row->date_to
                );

                $_destinationLocation[] = $data;
            }

            $travelDates = $this->getFirstAndLastArrValue($destinations);

            $this->db->select('a.reference_no, c.date_from, c.date_to, c.destination, UPPER(CONCAT(d.firstname, " ", d.lastname)) as name, a.status, b.employee_id');
            $this->db->join($this->travelPersonnelTable.' as b', 'b.travel_order_id = a.id', 'LEFT');
            $this->db->join($this->travelDestinationTable.' as c', 'c.travel_order_id = a.id', 'LEFT');
            $this->db->join($this->employeeTable.' as d', 'd.id = b.employee_id', 'LEFT');
            $this->db->from($this->travelOrderTable.' as a');
            $this->db->where('a.id !=', $id);
            $this->db->where_in('b.employee_id', $_personnelIds);

            $this->db->where('a.status !=', 'Cancelled');
            $this->db->where('a.status !=', 'Disapproved');

            $this->db->group_start();
            $this->db->where('a.status !=', 'Approved');
            $this->db->where('a.accomplished', 0);
            $this->db->group_end();
            
            $this->db->or_group_start();
            $this->db->where('a.status', 'Approved');
            $this->db->where('a.accomplished', 0);
            $this->db->group_end();

            $this->db->group_start();
                $this->db->where('DATE(c.date_from) >= ', date('Y-m-d', strtotime($travelDates['date_from'])));
                $this->db->where('DATE(c.date_from) <= ', date('Y-m-d', strtotime($travelDates['date_from'])));
                $this->db->or_where('DATE(c.date_to) >=', date('Y-m-d', strtotime($travelDates['date_to'])));
                $this->db->where('DATE(c.date_to) <=', date('Y-m-d', strtotime($travelDates['date_to'])));
            $this->db->group_end();

            $query = $this->db->get();

            if ($query->num_rows() > 0){
                foreach($query->result() as $key => $rs) {
                    if (in_array($rs->employee_id, $_personnelIds)) {
                        foreach ($_destinationLocation as $k => $row) {
                            $savedFromDes = date('Y-m-d H:i', strtotime($rs->date_from));
                            $savedToDes = date('Y-m-d H:i', strtotime($rs->date_to));
    
                            $toSavedFromDes = date('Y-m-d H:i', strtotime($row['date_from']));
                            $toSavedToDes = date('Y-m-d H:i', strtotime($row['date_to']));
    
                            if (($savedFromDes >= $toSavedFromDes && $savedFromDes <= $toSavedToDes) || ($savedToDes >= $toSavedFromDes && $savedToDes <= $toSavedToDes) || ($savedFromDes <= $toSavedFromDes && $savedToDes >= $toSavedToDes)) {
                               $arrData[$key]['reference_no'] = $rs->reference_no;
                               $arrData[$key]['name'] = $rs->name;
                               $arrData[$key]['status'] = $rs->status;
                               $arrData[$key]['to'][] = ['destination' => $rs->destination, 'date_from' => $rs->date_from, 'date_to' => $rs->date_to];
                            }
                        }
                    }
                }
            }

            $restructuredArray = [];

            foreach ($arrData as $entry) {
                $name = $entry['name'];
                $referenceNo = $entry['reference_no'];
                $status = $entry['status'];

                if (!isset($restructuredArray[$name])) {
                    $restructuredArray[$name] = [
                        'reference_no' => $referenceNo,
                        'name' => $name,
                        'status' => $status,
                        'to' => []
                    ];
                }

                $restructuredArray[$name]['to'] = array_merge($restructuredArray[$name]['to'], $entry['to']);
            }

            // Convert associative array to indexed array
            $restructuredArray = array_values($restructuredArray);

            return $restructuredArray;
        }

        public function travel_order_detailsv2($id) {
            $arrData = array();
            $newPersonnel = array();
            $newDestination = array();
            $this->db->from('gcceforms.travel_order');
            $this->db->where('id', $id);
            $query = $this->db->get();
            $travel_order_data = $query->row();

            if($travel_order_data->vehicle_id == "0"){
                $plateno = "";
            }else{
                if ($travel_order_data->vehicle_id) {
                    $vehicle_data = $this->vehicle_details($travel_order_data->vehicle_id);
                    $gen_code = $vehicle_data->gen_code;
                    $plateno = $gen_code . " | " . $vehicle_data->plateno . " | " . $vehicle_data->name;
                } else {
                    $plateno = $travel_order_data->vehicle_id;
                }
            }

            $travel_order_data->plateno = $plateno;

            if($travel_order_data->accomplished_by > 0){
                $travel_order_data->accomplished_by_name = $this->getPersonnelName($travel_order_data->accomplished_by);
            }


            $personnel = $this->db->get_where("gcceforms.travel_personnel", array("travel_order_id"=>$id))->result_array();
            $destination = $this->db->get_where("gcceforms.travel_destination", array("travel_order_id"=>$id))->result_array();
            foreach($personnel as $personnels){
                $personnels['employee_name'] = $this->getPersonnelName($personnels['employee_id']); 
                $newPersonnel[] = $personnels;
            }
            
            foreach($destination as $destinations){
                $destinations['requested_by'] = $this->getPersonnelName($destinations['requested_by']);
                $destinations['date_from'] = date("M d, Y g:i A", strtotime($destinations['date_from']));
                $destinations['date_to'] = date("M d, Y g:i A", strtotime($destinations['date_to']));
                $newDestination[] = $destinations;
            }

            $end_travel_order_time = end($destination);

            if(isset($destination[0]['date_from']) && $destination[0]['date_from']){
                $travel_order_data->duration = date("M d, Y g:i A", strtotime($destination[0]['date_from']))." - ".date("M d, Y g:i A", strtotime($end_travel_order_time['date_to']));
            }

            $arrData['data'] = $travel_order_data;
            $arrData['personnel'] = $newPersonnel;
            $arrData['destination'] = $newDestination;
            return $arrData;
        }

        public function getApprovedChartData() {
            $post = $this->input->post();
            
            // Extract date range from daterangepicker
            $start_date = isset($post['date']['start']) ? $post['date']['start'] : null;
            $end_date = isset($post['date']['end']) ? $post['date']['end'] : null;
        
            $current_date = date('Y-m-d');
            $escaped_current_date = $this->db->escape($current_date);
            
            $this->db->select("
                COUNT(td.id) as total_approved,
                COUNT(DISTINCT CASE WHEN td.accomplished = 1 THEN td.id END) as accomplished,
                COUNT(DISTINCT CASE WHEN td.accomplished = 0 AND td.date_to < $escaped_current_date THEN td.id END) as Overdue,
                COUNT(DISTINCT CASE WHEN td.accomplished = 0 AND td.date_to >= $escaped_current_date THEN td.id END) as Ongoing
            ");
        
            $this->db->from('gcceforms.travel_destination td');
            $this->db->join('gcceforms.travel_order to', 'to.id = td.travel_order_id', 'left');
            $this->db->where('to.status', 'Approved');
            
            // Apply date range filter if both dates are provided
            if ($start_date && $end_date) {
                // Simple approach: travels that fall within the selected date range
                $this->db->where('td.date_from >=', $start_date);
                $this->db->where('td.date_to <=', $end_date);
            }
            // If no date range provided, show all time data (no additional WHERE clause)
            
            $single_query = $this->db->get();
            $single_result = $single_query->row();
            
            $data['approved'] = array(
                'Accomplished' => (int)$single_result->accomplished,
                'Overdue' => (int)$single_result->Overdue,
                'Ongoing' => (int)$single_result->Ongoing
            );
            $data['total'] = (int)$single_result->total_approved;
            
            return $data;
        }

    }