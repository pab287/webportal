<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_model extends CI_Model
{
    protected $employeeTable = "gccmaster.tblemployees";
    protected $companyTable = "gcchris.tblcompanies";
    protected $departmentTable = "gcchris.tbldepartments";
    protected $positionTable = "gcchris.tblposition";
    protected $employeeDependentsTable = "gcchris.tbldependents";
    protected $employeeEducationTable = "gcchris.tbleducations";
    protected $employeeLicensureTable = "gcchris.tbllicenses";
    protected $employeeDriverLicenseTable = "gcchris.tbldriverlicense";
    protected $employeeWorkExperienceTable = "gcchris.tblworkxps";
    protected $employeeAwardsTable = "gcchris.tblawards";
    protected $employeeSkillsTable = "gcchris.tblskills";
    protected $employeeOrganizationTable = "gcchris.tblorganizations";
    protected $employeeTrainingsTable = "gcchris.tbltrainings";
    protected $employeeReferencesTable = "gcchris.tblreferences";
    protected $employeeMedicalHistoryTable = "gcchris.tblmedrecs";
    protected $employeeLegalHistoryTable = "gcchris.tbllegalrecs";
    protected $employeeOffensesTable = "gcchris.tbloffcoms";
    protected $employeeSalaryTable = "gcchris.tblsalaries";
    protected $tblPersonnelLocation = "gcctimeutility.personnel_locations";
    protected $tblPersonnel = "gcctimeutility.personnel";
    protected $multiplePositionTable = 'gcchris.tbl_employee_multi_positions';
    protected $loggedinData;
    protected $loggedInUsername;
    protected $user_data;

    protected $defaultStationTable = "gcchris.default_station_location";

    function __construct() {
        parent::__construct();

        $this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
        $this->loggedInUsername = $this->loggedinData["username"];
    }


    public function getAddtionalInfo($id){
        $data['dependents'] = $this->db->select("dep_name , dep_relation, dep_birthdate")->get_where($this->employeeDependentsTable, array("emp_id" => $id,"is_archived" => 0))->result();
        $this->db->reset_query();
        return $data;
    }

    public function getEducationBackground($id){
        $data['educations'] = $this->db->order_by('educ_to', 'DESC')->get_where($this->employeeEducationTable, array("emp_id" => $id,"is_archived" => 0))->result();
        return $data;
    }

    public function getLicenseAndCerts($id){
        $data['licenses'] = $this->db->get_where($this->employeeLicensureTable, array("emp_id" => $id,"is_archived" => 0))->result();
        $this->db->reset_query();
        $data['driverlicenses'] = $this->db->get_where($this->employeeDriverLicenseTable, array("emp_id" => $id,"is_archived" => 0))->result();
        $this->db->reset_query();
        $if_driver = $this->db->select("emp.position as position")->get_where($this->employeeTable . " emp", array("emp.id" => $id))->row_array();
        $this->db->reset_query();
        if(is_numeric($if_driver['position'])){
            $driver = $this->db
            ->group_start()
            ->like("pos.name","driver")
            ->or_like("pos.name","operator")
            ->group_end()
            ->from("gcchris.tblposition pos")
            ->join("gccmaster.tblemployees emp","emp.position = pos.id")
            ->where("emp.id",$id)
            ->count_all_results();
            $this->db->reset_query();

        }else{
            $driver = $this->db
            ->group_start()
            ->like("emp.position","driver")
            ->or_like("emp.position","operator")
            ->group_end()
            ->from("gccmaster.tblemployees emp")
            ->where("emp.id",$id)
            ->count_all_results();
            $this->db->reset_query();
        }
        $data['if_driver'] = $driver;
        return $data;
    }

    public function getEmpWorkExperience($id){
        $this->db->select("xps.id, xps.emp_id, xps.work_to,
                           xps.work_company, xps.work_status, xps.work_reason, xps.work_from, old_idno,
                           IF(pos.id IS NULL, xps.work_position, pos.`name`) work_position");
        $this->db->from($this->employeeWorkExperienceTable . " xps");
        $this->db->join($this->positionTable . " pos", "pos.id = xps.work_position", "LEFT");
        $this->db->where('xps.emp_id', $id);
        $this->db->order_by("xps.work_from DESC, xps.work_to DESC");
        $query = $this->db->get();
        return ['works' => $query->result()];
    }


    public function getAwardsAndAchievements($id){  
        $data['awards'] = $this->db->order_by("award_date", "desc")->get_where($this->employeeAwardsTable, array("emp_id" => $id,"is_archived" => 0))->result();
        $this->db->reset_query();
        return $data;
    }

    public function getEmpSkills($id){
        $data['skillset'] = $this->db->select('id,skills')->get_where($this->employeeSkillsTable, array("emp_id" => $id,"is_archived" => 0))->result();            
        return $data;
    }

    public function getOrgs($id){
        $data['organizations'] = $this->db->order_by("org_to","desc")->get_where($this->employeeOrganizationTable, array("emp_id" => $id,"is_archived" => 0))->result();
        return $data;
    }

    public function getTrainingsAndSeminars($id){
        $data['trainings'] = $this->db->order_by("train_to","desc")->get_where($this->employeeTrainingsTable, array("emp_id" => $id,"is_archived" => 0))->result();
        return $data;
    }

    public function getPersonalReferences($id){
        $data['references'] = $this->db->get_where($this->employeeReferencesTable, array("emp_id" => $id,"is_archived" => 0))->result();
        return $data;
    }

    public function getEmpMedicalHistory($id){
        $data['medicals'] = $this->db->order_by("med_date","desc")->get_where($this->employeeMedicalHistoryTable, array("emp_id" => $id,"is_archived" => 0))->result();
        return $data;
    }

    public function getLegalHistory($id){
        $data['legals'] = $this->db->order_by("leg_case_date","desc")->get_where($this->employeeLegalHistoryTable, array("emp_id" => $id,"is_archived" => 0))->result();
        return $data;
    }

    public function getAccountability($id){
        $data['accountability'] = $this->getEmployeeAccountability(0, $id);
        return $data;
    }

    function getEmployeeAccountability($dataTable = 1, $_emp_id = null) {
        $post = $this->input->post();
        $emp_id = ((int)$dataTable === 1) ? $post["emp_id"] : $_emp_id;

        $columns = array("id", "sal_date", "sal_rate");
        $dir = "DESC";
        $order = "acct_body.id";

        if (isset($post["order"]) && $post["order"]) {
            $dir = $post["order"][0]["dir"];
            $order = $columns[$post["order"][0]["column"]];
        }

        $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
        $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
        $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
        $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";

        $this->db->select("`acct_body`.asset_id,
                           `acct`.`reference_no`, `acct_body`.`asset_code`, `assets`.`date_received`, `acct_body`.`remarks`,
                           `acct_body`.`description`, `acct_body`.`amount`, `acct_body`.`is_returned`,`acct_body`.`remarks_returned`,
                           `acct_body`.`date_returned`, `acct`.`status`,`acct`.`released_dt`,
                           `acct_body`.`type`,
                           CASE
                              WHEN assets.name IS NULL THEN vehicles.name
                              WHEN vehicles.name IS NULL THEN assets.name
                           END aname", FALSE);

        $this->db->where("acct.issued_to", $emp_id);
        $this->db->where("acct.status", "Released");

        if(isset($post['status']) && $post['status'] <= 1){
            $this->db->where('acct_body.is_returned', $post['status']);
        }

        $this->db->join("gcceforms.accountability_body acct_body", "acct_body.accountability_id = acct.id", "INNER");
        $this->db->join("gccasset.assets assets", "assets.id = acct_body.asset_id AND `acct_body`.`type` = 'Asset'", "LEFT");
        $this->db->join("gccasset.vehicles vehicles", "vehicles.id = acct_body.asset_id AND `acct_body`.`type` = 'Vehicle'", "LEFT");
        $this->db->like("CONCAT(acct.reference_no, acct_body.asset_code, acct_body.description, acct_body.amount, CASE
                              WHEN assets.name IS NULL THEN vehicles.name
                              WHEN vehicles.name IS NULL THEN assets.name
                           END)", $searchValue, "BOTH");


        if ((int)$dataTable === 1) {
            if ((int)$limit > 0) {
                $this->db->limit($limit, $start);
            }

            $this->db->order_by($order, $dir);
            $q = $this->db->get("gcceforms.accountability acct");

            return array(
                "data" => $q->result(),
                // "sql" => $this->db->last_query(),
                "recordsFiltered" => $this->totalEmployeeAccountabilityCount($searchValue, $emp_id),
                "recordsTotal" => $this->totalEmployeeAccountabilityCount($searchValue, $emp_id),
                "draw" => $draw
            );
        } else {
            return $this->db->get("gcceforms.accountability acct")->result();
        }
    }
    
    private function totalEmployeeAccountabilityCount($searchValue = "", $emp_id) {
        $this->db->where("acct.issued_to", $emp_id);
        $this->db->join("gcceforms.accountability_body acct_body", "acct_body.accountability_id = acct.id", "INNER");
        return $this->db->count_all_results("gcceforms.accountability acct");
    }

    public function getEmploymentInformation($id){
        $post = $this->input->post();
        $data['offenses'] =  $this->db->select('*, IFNULL(filename, "---") as filename')->order_by('offcom_date', 'DESC')->get_where($this->employeeOffensesTable, array("emp_id" => $id,"is_archived" => 0))->result();
        $this->db->reset_query();
        $data['salaries'] = $this->db
            ->select("sal.id, sal.add_date, sal.sal_date, sal.sal_rate, sal.sal_remarks, IF(pos.id IS NULL, sal.sal_position, pos.name) sal_position")
            ->join("gcchris.tblposition pos", "pos.id = sal.sal_position", "LEFT")
            ->order_by("CASE WHEN sal.add_date = '0000-00-00 00:00:00' THEN 1 ELSE 0 END", "asc")
            ->order_by("sal.sal_date", "desc")
            ->get_where($this->employeeSalaryTable . " sal", array("sal.emp_id" => $id, "sal.is_archived" => 0))
            ->result();
        $this->db->reset_query();
        $personnelId = $this->getEmpLocation($post['biono']);
        $this->db->reset_query();
        $data['stations'] = $this->db->order_by('id', 'DESC')->get_where($this->tblPersonnelLocation, array("personnel_id" => $personnelId))->result();
        $this->db->reset_query();
        $data['default_station'] = $this->db->select("UPPER(TRIM(station_description)) as description")->order_by('id', 'DESC')->get_where($this->defaultStationTable, array("employee_id" => $id))->row();
        $this->db->reset_query();
        return $data;
    }

    private function getEmpLocation($emp_bio){
        $this->db->select('id');
        $this->db->where("biometricno", $emp_bio);
        $data = $this->db->get($this->tblPersonnel);
        $personel_id = $data->row_array();
        return isset($personel_id['id']) && $personel_id['id'] ? $personel_id['id']: 0;
    }

    public function getEmployee($emp_id){
        $data = array();
        $this->db->select("emp.id, emp.lastname, emp.firstname, emp.middlename, emp.suffix, emp.curr_addr, emp.prov_addr, emp.citizenship, emp.religion, emp.languages, emp.email, emp.gender, emp.civil_stat, emp.bday, emp.birthplace, emp.bloodtype, emp.height, emp.weight, emp.hair_color, emp.complexion, emp.tel_no, emp.mobile_no, 
        emp.pic_filename, emp.idno, emp.biometricno, pos.name as position ,pos.id as position_id, emp.work_status, emp.employee_status, emp.date_start, emp.date_end, com.code as company_id, emp.level, emp.date_regular, emp.date_end_prob, emp.resign_reason, pos.job_desc, emp.supervisor, emp.ques1, emp.ques2, emp.ques3, emp.ques4, emp.ques5, emp.ques6, emp.ques7, emp.ques8, emp.ques9,
        emp.email, emp.tax_status, emp.tin_no, emp.phealth_no, emp.pagibig_no, emp.sss_no,
        emp.fat_name, emp.mot_name, emp.partner_type, emp.spo_deceased, emp.partners_deceased, emp.spo_name, emp.partners_name, emp.fat_addr, emp.mot_addr, emp.spo_addr, emp.partners_addr, emp.fat_company, emp.mot_company, emp.spo_company, emp.partners_company, emp.fat_occupation, emp.mot_occupation, emp.spo_occupation, emp.partners_occupation, emp.fat_contact, emp.mot_contact, emp.spo_contact, emp.partners_contact, emp.emer_addr, emp.emer_contact, emp.emer_name, 
        dept.description as department_description, emp.work_mode, emp.payroll_type
        ");
        $this->db->from($this->employeeTable." as emp");
        $this->db->join($this->positionTable." as pos", "pos.id = emp.position", "LEFT");
        $this->db->join($this->companyTable." as com", "com.id = emp.company_id", "LEFT");
        $this->db->join($this->departmentTable." as dept", "dept.id = emp.department_id", "LEFT");
        $this->db->where("emp.id", $emp_id);
        $data = $this->db->get()->row();
        return $data;
    }

    public function getEmpJobDescription(){
        $get = $this->input->get();
        $position = isset($get['position_id']) && $get['position_id'] ? $get['position_id'] : 0;
        $emp_id = isset($get['emp_id']) && $get['emp_id'] ? $get['emp_id'] : 0;
        $is_multiple_position = isset($get['is_multiple']) && $get['is_multiple'] ? $get['is_multiple'] : 0;
        $data = array();

        if ($is_multiple_position) {
            $this->db->select("a.is_primary, a.sort, b.id as position_id, b.name as position_description, b.job_desc as data");
            $this->db->from($this->multiplePositionTable.' as a');
            $this->db->join($this->positionTable.' as b', 'b.id = a.position', 'LEFT');
            $this->db->where('a.emp_id', $emp_id);
            $q = $this->db->get();

            if ($q->num_rows() > 0) {
                $_temp = array();
                foreach ($q->result() as $r) {
                    if ($r->data) {
                        $_temp[] = array(
                            "is_primary" => $r->is_primary,
                            "sort" => $r->sort,
                            "position_id" => $r->position_id,
                            "position_description" => $r->position_description,
                            "data" => $r->data
                        );
                    }
                }

                if ($_temp) {
                    $data['data'] = $_temp;
                }
            }
        } else {
            $data['data'] = $this->db->select('job_desc')->get_where($this->positionTable, array("id" => $position))->row();
        }

        $data['is_multiple'] = $is_multiple_position;

        return $data;
    }

    public function allow_sms($id) {
        $result = array();
        $post = $this->input->post();

        $data = array(
            'allow_sms_notification' => $post['allow']
        );

        $notif = $post['allow'] ? 'Enabled' : 'Disabled';

        $this->db->where('id', $id);
        $query = $this->db->update($this->employeeTable, $data);

        if ($query) {
            $result['state'] = true;
            $result['msg'] = "Successfully {$notif} SMS Notification";
            $this->core_layout->setEventLog("User ".$this->loggedInUsername . " {$notif} the SMS notification.", "update", "success", "gcchris", "user");
        } else {
            $result['state'] = false;
            $this->core_layout->setEventLog("User `".$this->loggedInUsername . "` failed to 'Enable/Disable' the SMS notification.", "update", "error", "gcchris", "system");
        }

        return $result;
    }

    public function get_employee_deductions($id) {
        $result = array();

        $this->db->select("emp_loans.*, master_loans.loan_name, ROUND(SUM(IFNULL(psloanpayments.amount_due, 0)),2) as total_amount_paid, 
        GROUP_CONCAT(DISTINCT psloanpayments.amount_due, '||', ps.id) as temp_amount_paid, emp_loans.reference as ref, emp_loans.id as loan_id, 
        merged_loans.amount as merged_amount, IFNULL(COUNT(mloans.id), 0) as merged_count");
        $this->db->from("gcchris.loans emp_loans");
        $this->db->join("payroll.loans master_loans", "master_loans.id = emp_loans.loan_id");
        $this->db->join("payroll.payroll_sheet_loan_payments psloanpayments", "psloanpayments.loan_id = emp_loans.id", "LEFT");
        $this->db->join("payroll.payroll_sheet ps", "ps.id = psloanpayments.payroll_sheet_id AND ps.posted = 1", "LEFT");
        $this->db->join("gcchris.loans merged_loans", "merged_loans.id = emp_loans.merged_id", "LEFT");
        $this->db->join("gcchris.loans mloans", "mloans.merged_id = emp_loans.id", "LEFT");
        $this->db->where("emp_loans.emp_id", $id);
        $this->db->where("emp_loans.active", 1);
        $this->db->where('emp_loans.paid', 0);
        $this->db->where("emp_loans.is_archived", 0);
        $this->db->group_by("emp_loans.id, emp_loans.loan_id");
        $this->db->order_by("emp_loans.id", "DESC");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $key => $value) {
                $tempStatus = intval($value->active);
                $tempTotal = 0;
                $tempAmount = $value->temp_amount_paid;
                $tempAmount = explode(",", $tempAmount);
                foreach ($tempAmount as $kk => $vv) {
                    $tempDD = explode("||", $vv);
                    $tempTotal += floatval($tempDD[0]);
                }
                $tempTotal = round($tempTotal, 2);
                if($tempTotal !== floatval($value->total_amount_paid)){ $value->total_amount_paid = $tempTotal; }
                $tempCreatedBy = intval($value->created_by) > 0 ? 
                    $this->core_layout->getEmployeeData($value->created_by)['display_name_1']: "[ System Generated: Cash Advance ]";
                $value->created_by = $tempCreatedBy;
                $value->created_at = date('Y-m-d', strtotime($value->created_at));

                $tempbalance = floatval($value->amount) - floatval($value->total_amount_paid);
                $value->tempbalance = $tempbalance;

                $value->image = ($this->checkLoanAttachment($value->loan_id));

                if (floatval($tempbalance) > 0) {
                    $result[] = $value;
                }
            }
        }
        
        return $result;
    }

    function checkLoanAttachment($id){
        $this->db->where('loan_id', $id);
        $this->db->from('gcchris.loans_images');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function renderLoanActions($row){
        $btn = '';
        $ctrActions = 0;
        $listActions = '';
        $currentActions = $this->core_layout->getCurrentActions();
    
        $isPaid = (int)$row->paid;
        $tempIsPaid = false;
    
        $balance = (float)$row->amount - (float)$row->total_amount_paid;
        if ($balance <= 0) {
            $tempIsPaid = true;
        }

        if (!empty($currentActions) && in_array('view_own_request', $currentActions)) {
            $btn .= '<button title="View payment history"
                            class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-primary"
                            onclick="openLoanPaymentHistoryModal(' . $row->id . ')">
                            <i class="fa fa-list-ol"></i>
                        </button> ';
            $listActions .= '<li class="m-nav__item">
                    <a href="javascript:void(0)" class="m-nav__link"
                    onclick="openLoanPaymentHistoryModal(' . $row->id . ')">
                        <i class="m-nav__link-icon flaticon-list"></i>
                        <span class="m-nav__link-text">PAYMENT HISTORY</span>
                    </a>
                </li>';
            $ctrActions++;
        }
    
        $tempAction = '<div class="m-dropdown m-dropdown--inline m-dropdown--align-right m-dropdown--large"
                data-dropdown-toggle="click" aria-expanded="true">
            <a href="#" class="m-dropdown__toggle btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill"
                data-toggle="m-tooltip" data-original-title="More Options" data-skin="dark"
                data-delay=\'{"show": 500}\'>
                <i class="fa fa-ellipsis-v"></i>
            </a>
            <div class="m-dropdown__wrapper">
                <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                <div class="m-dropdown__inner">
                    <div class="m-dropdown__body">
                        <div class="m-dropdown__content">
                            <ul class="m-nav">
                                <li class="m-nav__section m-nav__section--first">
                                    <span class="m-nav__section-text">OPTIONS</span>
                                </li>'
                                . $listActions .
                            '</ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    
        if ($ctrActions > 1) {
            $btn = $tempAction;
        }
        if ($ctrActions == 0) {
            $btn = '--';
        }
    
        return $btn;
    }
}