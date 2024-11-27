<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Contractor_m extends CI_Model{
    protected $contractorTable = "gcchris.tblcontractor";
    protected $positionTable = "gcchris.tblposition";
    protected $employeeTable = "gccmaster.tblemployees";
    protected $pmsContractTable = "gccpms.sf_contract";


	public function __construct(){
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in"); 
        date_default_timezone_set("Asia/Manila");
    }

    function getContractorSelect2Data(){
        $resultset = array();
		$arrData = array();
        $where = array("is_archived"=>0);

		$get = $this->input->get();
		$this->db->select("id, contractor as text");
		$this->db->from($this->contractorTable);
		$this->db->where($where);
		if(isset($get["term"]) && $get["term"]){ $this->db->like("contractor", $get["term"], "both"); }
		$query = $this->db->get();

		if($query->num_rows() > 0){
			$arrData = $query->result();
		}
		
		$resultset["results"] = $arrData;
		return $resultset;
	}
	
	function getTaskInchargeSelect2Data(){
        $resultset = array();
		$arrData = array();
		$get = $this->input->get();
		$this->db->select("a.id, a.lastname, a.firstname, a.middlename, a.suffix");
		$this->db->from($this->employeeTable." a");
		$this->db->join($this->positionTable." b", "b.id = a.position", "LEFT");
		$this->db->where("a.employee_status", "Active");
		$this->db->group_start();
		$this->db->where("a.position", "site engineer");
		$this->db->or_where("a.position", "specialty engineer");
		$this->db->or_where("b.name", "site engineer");
		$this->db->or_where("b.name", "specialty engineer");
		$this->db->group_end();
		if(isset($get["term"]) && $get["term"]){
			$this->db->group_start();
			$this->db->like("a.lastname", $get["term"], "both");
			$this->db->or_like("a.firstname", $get["term"], "both");
			$this->db->or_like("a.middlename", $get["term"], "both");
			$this->db->or_like("a.suffix", $get["term"], "both");
			$this->db->group_end();
		}
		$query = $this->db->get();
		if($query->num_rows() > 0){
			foreach($query->result() as $rs){
				$temp = (array) $rs;
				$displayName = $this->core_layout->getDisplayName($temp);
				$displayName = (object) $displayName;
				$row = array();
				$row["id"] = $rs->id;
				$row["text"] = (isset($displayName->display_name_1) && $displayName->display_name_1)? $displayName->display_name_1: "No assigned name";
				$arrData[] = $row;
			}
		}
		
		$resultset["results"] = $arrData;
		return $resultset;
	}
	
	function getForemanSelect2Data(){
        $resultset = array();
		$arrData = array();
		$get = $this->input->get();
		$this->db->select("a.id, a.lastname, a.firstname, a.middlename, a.suffix");
		$this->db->from($this->employeeTable." a");
		$this->db->join($this->positionTable." b", "b.id = a.position OR b.name = a.position", "LEFT");
		$this->db->where("a.employee_status", "Active");
		$this->db->group_start();
		$this->db->like("a.position", "foreman", "both");
		$this->db->or_like("b.name", "foreman", "both");
		$this->db->group_end();
		if(isset($get["term"]) && $get["term"]){
			$this->db->group_start();
			$this->db->like("a.lastname", $get["term"], "both");
			$this->db->or_like("a.firstname", $get["term"], "both");
			$this->db->or_like("a.middlename", $get["term"], "both");
			$this->db->or_like("a.suffix", $get["term"], "both");
			$this->db->group_end();
		}
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$tempRow = array();
			$tempRow["id"] = 0;
			$tempRow["text"] = "NONE";
			$arrData[] = $tempRow;

			foreach($query->result() as $rs){
				$temp = (array) $rs;
				$displayName = $this->core_layout->getDisplayName($temp);
				$displayName = (object) $displayName;
				$row = array();
				$row["id"] = $rs->id;
				$row["text"] = (isset($displayName->display_name_1) && $displayName->display_name_1)? $displayName->display_name_1: "No assigned name";
				$arrData[] = $row;
			}
		}
		
		$resultset["results"] = $arrData;
		return $resultset;
    }

    function getContractorModalContent($content="add"){
        $resultset = array();
        $html = "";
        $arrData = array();

        $companyCodeList = $this->adm_company->getCompanyCodeList();
        if($content == "add"){
            $html = $this->load->view("pms/contractor/modals/add_content", array("company_code"=>$companyCodeList), true);
        }

        if($content == "edit"){
            $post = $this->input->post();
            if(isset($post) && $post){
                unset($post["csrf_token"]);
                $tempData = $this->db->get_where($this->contractorTable, $post);
                if($tempData->num_rows() == 1){
                    $arrData = $tempData->row();
                }
            }
            $html = $this->load->view("pms/contractor/modals/edit_content", array("data"=>$arrData, "company_code"=>$companyCodeList), true);
        }

        if($html){
            $resultset["response"] = true;
            $resultset["html"] = $html;
            $resultset["data"] = $arrData;
        }else{
            $resultset["response"] = false;
        }

        return $resultset;
    }

    function checkIfContractorIsAssigned($id) {
	    $result = array();

	    $this->db->where("contractor_id", $id);
	    $this->db->where("status", 1);
	    $result["count"] = $this->db->count_all_results($this->pmsContractTable);
	    $result["contractor"] = $this->db->get_where($this->contractorTable, array("id" => $id))->row("contractor");

	    return $result;
    }
}