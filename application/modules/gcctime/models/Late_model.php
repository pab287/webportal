<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Late_model extends CI_Model {
        protected $userdata = array();
        private $personnelTable = "gcctimeutility.personnel";
        private $customShiftTable = "gcctimeutility.custom_personnel_shift";

        function __construct() {
            parent::__construct();
            $this->load->model('ams/Utilities_model', 'mod_util');
            $this->userdata = $this->core_layout->getUserId();
        }

        function getCustomCollection() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("b.biometricno", "b.name", "a.weekday", "a.am_start", "a.am_end", "a.pm_start", "a.pm_end", "a.is_active", "a.id", "a.status", "a.access_type");

                $dir = $post["order"][0]["dir"];
                $order = $columns[$post["order"][0]["column"]];
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtTable = $this->dt_model->dataTable();
                $dtTable->setTable($this->customShiftTable);
                $dtTable->setTableAlias("a");

                $dtTable->setParameterFields($columns);

                $joinTable = array();
                $joinTable["table"][$this->personnelTable] = "b";
                $joinTable["fields"][] = "b.id=a.personnel_id";
                $joinTable["field_loc"][] = "LEFT";

                $dtTable->setJoinTable($joinTable);
                $dtTable->setWhereInField("a.id");

                $parameters = array();
                $parameters["a.status"] = 1;
                $parameters["a.access_type"] = 1;

                $dtTable->setWhereParameters($parameters);
                $totalData = $dtTable->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtTable->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtTable->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['biometricno'] = $pst->biometricno;
                        $nestedData['name'] = $pst->name;
                        $nestedData['weekday'] = $pst->weekday;
                        $nestedData['am_start'] = $pst->am_start;
                        $nestedData['am_end'] = $pst->am_end;
                        $nestedData['pm_start'] = $pst->pm_start;
                        $nestedData['pm_end'] = $pst->pm_end;
                        $nestedData['is_active'] = $pst->is_active;
                        $data[] = $nestedData;
                    }
                }
                $json_data = array(
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data,
                );

                return $json_data;
            } else {
                return array(
                    "draw" => 1,
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
        }

        function getPersonnelItems() {
            $resultset = array();
            $this->db->select("id, CONCAT(biometricno, ' | ', name) as text");
            $this->db->from("gcctimeutility.personnel");
            $query = $this->db->get();

            $resultset["results"] = ($query->num_rows() > 0) ? $query->result() : array();

            return $resultset;
        }

        function addCustomlates() {
            $resultset = array();
            $post = $this->input->post();
            if (isset($post) && $post) {
                if (isset($post["personnel_id"]) && $post["personnel_id"]) {
                    $post["access_type"] = 1;
                    $added = $this->db->insert($this->customShiftTable, $post);
                    if ($added) {
                        $resultset["response"] = true;
                        $resultset["toastr_msg"] = "Custom last settings has been added.";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Failed to set custom last settings!";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "No personnel assigned, please select a personnel first!";
                }

            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "No post data found!";
            }

            return $resultset;
        }

        function getLateReport($start_date, $end_date, $department, $company) {
            $post = $this->arrayToStdClass($this->input->post());
            $dtCfg = $this->mod_util->getDatatablesConfigForPagination($post);
            $resultSet = array();
            $where_dept = isset($department) ? " AND emp.department_id = ".$department : "";
            $where_company = isset($company) ? " AND emp.company_id = ".$company : "";
            $joinArray = array(
                array(
                    "table" => "gccmaster.tblemployees emp",
                    "condition" => "emp.id = lreport.emp_id",
                    "option" => "INNER",
                ),
            );
            $where = "DATE(lreport.date_time) BETWEEN '$start_date' AND '$end_date'".$where_dept . $where_company;

            $search = array();
            $search["field"] = "CONCAT(emp.lastname, emp.firstname, emp.middlename)";
            $search["key"] = $dtCfg->search;
            $search["option"] = "BOTH";

            $this->db->like($search["field"], $search["key"], $search["option"]);
            $this->db->select("lreport.id, lreport.biometric_id, lreport.emp_id, 
                               emp.lastname, emp.firstname, emp.suffix, emp.suffix, 
                               GROUP_CONCAT(lreport.date_time) _late_dates,
                               COUNT(*) late_count,
                               CONCAT(emp.lastname, 
                               CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''  END, ', ',
			                   emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE' 
			                   AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END) `employee_name`");
            foreach ($joinArray as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
            $this->db->where($where, NULL, FALSE);
            $this->db->group_by("lreport.emp_id");

            $this->db->order_by($dtCfg->order_column, $dtCfg->order_direction);
            if (intval($dtCfg->length) >= 1) {
                $this->db->limit($dtCfg->length, $dtCfg->start);
            }

            $query = $this->db->get("gcctimeutility.late_report lreport");
            $data = $query->result();

            foreach ($data as $datum) {
            $datum->employee_name = strtoupper($datum->employee_name);
                if (!empty($datum->_late_dates)) {
                    $dates_arr = explode(",", $datum->_late_dates);
                    usort($dates_arr, function ($a, $b) {
                        return strtotime($a) - strtotime($b);
                    });

                    $datum->late_dates = implode(",", $dates_arr);
                }
            }

            $resultSet["data"] = $data;
            $resultSet["recordsFiltered"] = $this->getLateReportCount($joinArray, $where, $search);
            $resultSet["recordsTotal"] = $this->getLateReportCount($joinArray, $where, $search);

            return $resultSet;
        }

        private function getLateReportCount($joinArray, $where, $search = null) {
            $this->db->like($search["field"], $search["key"], $search["option"]);
            foreach ($joinArray as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
            $this->db->where($where, NULL, FALSE);
            $this->db->group_by("lreport.emp_id");
            $data = $this->db->get("gcctimeutility.late_report lreport");
            return $data->num_rows();
        }

        function getAbsenteeReport($start_date, $end_date, $department, $company) {
            $post = $this->arrayToStdClass($this->input->post());
            $dtCfg = $this->mod_util->getDatatablesConfigForPagination($post);
            $resultSet = array();
            $where_dept = isset($department) ? " AND emp.department_id = ".$department : "";
            $where_company = isset($company) ? " AND emp.company_id = ".$company : "";
            $joinArray = array(
                array(
                    "table" => "gccmaster.tblemployees emp",
                    "condition" => "emp.id = absentee_report.emp_id",
                    "option" => "INNER",
                ),
            );
            $where = "absentee_report.date BETWEEN '$start_date' AND '$end_date'".$where_dept . $where_company;

            $search = array();
            $search["field"] = "CONCAT(emp.lastname, emp.firstname, emp.middlename)";
            $search["key"] = $dtCfg->search;
            $search["option"] = "BOTH";

            $this->db->like($search["field"], $search["key"], $search["option"]);
            $this->db->select("absentee_report.id, absentee_report.biometric_id, absentee_report.emp_id, 
                               emp.lastname, emp.firstname, emp.suffix, emp.suffix, 
                               GROUP_CONCAT(absentee_report.date) _absentee_dates,
                               COUNT(*) absentee_count,
                               CONCAT(emp.lastname, 
                               CASE WHEN emp.suffix != 'N/A' AND emp.suffix !='NONE' AND emp.suffix !='' AND emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''  END, ', ',
			                   emp.firstname, ' ', CASE WHEN emp.middlename != 'N/A' AND emp.middlename != 'NONE' 
			                   AND emp.middlename !='' AND emp.middlename IS NOT NULL THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE '' END) `employee_name`");
            foreach ($joinArray as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
            $this->db->where($where, NULL, FALSE);
            $this->db->group_by("absentee_report.emp_id");

            $this->db->order_by($dtCfg->order_column, $dtCfg->order_direction);
            if (intval($dtCfg->length) >= 1) {
                $this->db->limit($dtCfg->length, $dtCfg->start);
            }

            $query = $this->db->get("gcctimeutility.absentee_report absentee_report");
            $data = $query->result();

            foreach ($data as $datum) {
                $datum->employee_name = strtoupper($datum->employee_name);
                if (!empty($datum->_absentee_dates)) {
                    $with_loa = 0;
                    $whole_day = 0;
                    $half_day = 0;
                    $half_day_count = 0;

                    $dates_arr = explode(",", $datum->_absentee_dates);
                    usort($dates_arr, function ($a, $b) {
                        return strtotime($a) - strtotime($b);
                    });

                    $_dates = array();
                    foreach ($dates_arr as $date) {
                       //$row = $this->db->where("emp_id", $datum->emp_id)
                           // ->where("date", $date)
                           // ->get("gcctimeutility.absentee_report")
                            //->row();

                            $this->db->select("ar.id, ar.biometric_id, ar.emp_id, ar.emp_name, ar.date, ar.am, ar.pm, ar.loa_reference_no, pers.name, pers.is_active, pers.is_flexi, pers.shift_id");
                            $this->db->from("gcctimeutility.absentee_report as ar");
                            $this->db->join("gcctimeutility.personnel as pers","pers.biometric_id = ar.biometric_id");
                            $this->db->where("ar.emp_id",$datum->emp_id);
                            $this->db->where("ar.date",$date);
                            $row = $this->db->get()->row();

                        if (!empty($row)) {
                           if($this->checkUpdatedAttendance($row->is_flexi, $row->biometric_id, $row->date, $row->shift_id) !== "absent"){// check if loa absent or no loa filed
                                if(strpos($row->loa_reference_no,"TO") === false || $row->loa_reference_no === "N/A"){ 
                                    $with_loa += $row->loa_reference_no !== "N/A" ? 1 : 0;

                                    if (intval($row->am) === 1 && intval($row->pm) === 1) {
                                        $whole_day += 1;
                                    } else if (intval($row->am) === 1 || intval($row->pm) === 1) {
                                        $half_day += 1;
                                        $half_day_count += .5;
                                    }
                                    $tooltip = $row->loa_reference_no !== "N/A" ? $row->loa_reference_no : "";
                                    $with_loa_class = $row->loa_reference_no !== "N/A" ? "m-badge--success" : "m-badge--danger";
                                    $copyToClipboard = $row->loa_reference_no !== "N/A" ? 'onclick="' . "copyToClipboard('".$tooltip."')" .'"' : "";
                                    $_hasLOA = $row->loa_reference_no != 'N/A' ? "cursor: pointer" : "cursor: default";
                                    $template = '<span style="border-radius: 3em; '.$_hasLOA.'" id="'.$tooltip.'" '.$copyToClipboard.' title="' . $tooltip . '"
                                                        class="m-badge m-badge--outline m-badge--wide pt-1 pb-1
                                                                m--font-boldest2 m--margin-right-5 ' . $with_loa_class . '">' . date("M. d&#44; Y", strtotime($date)) . '</span>';
                                    array_push($_dates, $template);
                                }
                           }
                            
                        }
                    }

                    $datum->with_loa = $with_loa;
                    $datum->whole_day = $whole_day;
                    $datum->half_day = $half_day;
                    $datum->half_day_count = $half_day_count;
                    $datum->total_absent = $half_day_count + $whole_day;
                    $datum->absentee_dates = implode(",", $_dates);
                }
            }

            $resultSet["data"] = $data;
            $resultSet["recordsFiltered"] = $this->getAbsenteeReportCount($joinArray, $where, $search);
            $resultSet["recordsTotal"] = $this->getAbsenteeReportCount($joinArray, $where, $search);

             //var_dump($this->db->last_query());
            return $resultSet;
        }

        private function checkUpdatedAttendance($is_flexi = 0, $biometric_id = null, $date = null, $shift_id = 0){
            $this->db->select("id");
            $this->db->from("gcctimeutility.attendance");
            $this->db->where("biometric_id",$biometric_id);
            $this->db->like("datetime",$date,"both");
            $query = $this->db->get();

            $dt1 = strtotime($date);
            $dt2 = date("l", $dt1);
            $dt3 = strtolower($dt2);

            if($dt3 == 'saturday'){
                if($is_flexi == "1"){
                    return $query->num_rows() < 2 ? "present" : "absent";
                }else{
                    return $query->num_rows() > 2 ? "present" : "absent";
                }
            }else{
                if($is_flexi == "1"){
                    return $query->num_rows() < 2 ? "present" : "absent";
                }else{
                    return $query->num_rows() < 2 ? "present" : "absent";
                }
            }

        }

        private function checkTravelOrderIfExist($emp, $ref_no){
            $this->db->select("travel_order.id");
            $this->db->from('travel_order');
            $this->db->join('travel_personnel','travel_personnel.travel_order_id = travel_order.id');
            $this->db->where('travel_order.reference_no',$ref_no);
            $this->db->where('travel_personnel.employee_id',$emp);
            return $this->db->count_all_results();
        }

        private function getAbsenteeReportCount($joinArray, $where, $search = null) {
            $this->db->like($search["field"], $search["key"], $search["option"]);
            foreach ($joinArray as $join) {
                $this->db->join($join["table"], $join["condition"], $join["option"]);
            }
            $this->db->where($where, NULL, FALSE);
            $this->db->group_by("absentee_report.emp_id");
            $data = $this->db->get("gcctimeutility.absentee_report absentee_report");
            return $data->num_rows();
        }

        private function arrayToStdClass($array) {
            return json_decode(json_encode($array));
        }
    }
