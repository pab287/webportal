<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Contractor_model extends CI_Model
{
    protected $contractorTable = "gcchris.tblcontractor";
    protected $archivedTable = "gccmaster.archived_items";
    protected $employeeTable = "gccmaster.tblemployees";

    function __construct()
    {
        parent::__construct();
        $this->load->model("hris/employee_model", "adm_employee");
        $this->load->model("hris/company_model", "adm_company");
        $this->load->model("core/datatable_model", "dt_model");

        $this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
        $this->loggedInUsername = $this->loggedinData["username"];
    }

    function getContractorDatatableRequest()
    {
        $post = $this->input->post();
        if ($post) {
            $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
            $columns = array("contractor", "company", "representative", "phone", "project", "id", "status");
            $dir = "DESC";
            $order = "id";
            if ($orderx) {
                $dir = $orderx[0]["dir"];
                $order = $columns[$orderx[0]["column"]];
            }

            $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
            $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
            $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
            $dtTemp = $this->dt_model->dataTable();
            $dtTemp->setTable($this->contractorTable);
            $dtTemp->setParameterFields($columns);

            $parameters = array();
            $parameters["is_archived"] = (isset($post['is_archived']) && $post['is_archived'] == 1) ? 1 : 0;
            $dtTemp->setWhereParameters($parameters);

            $totalData = $dtTemp->dtAllPostsCount();
            $totalFiltered = $totalData;

            if (empty($searchValue)) {
                $posts = $dtTemp->dtAllPosts($limit, $start, $order, $dir);
            } else {
                $posts = $dtTemp->dtSearch($limit, $start, $searchValue, $order, $dir);
                $totalFiltered = $dtTemp->dtPostSearchCount($searchValue);
            }

            $data = array();
            if (!empty($posts)) {
                foreach ($posts as $pst) {
                    $nestedData = array();
                    $nestedData['id'] = $pst->id;
                    $nestedData['contractor'] = $pst->contractor;
                    $nestedData['company'] = $pst->company;
                    $nestedData['representative'] = $pst->representative;
                    $nestedData['phone'] = $pst->phone;
                    $nestedData['project'] = $pst->project;
                    $nestedData['status'] = $pst->status;
                    $data[] = $nestedData;
                }
            }
            return array(
                "draw" => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );
        } else {
            return array(
                "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array(),
            );
        }
    }

    function getContractorModalContent($content = "add")
    {
        $resultset = array();
        $html = "";
        $arrData = array();

        $companyCodeList = $this->adm_company->getCompanyCodeList();
        if ($content == "add") {
            $html = $this->load->view("hris/masterfile/contractor/modals/add_content", array("company_code" => $companyCodeList), true);
        }

        if ($content == "edit") {
            $post = $this->input->post();
            if (isset($post) && $post) {
                unset($post["csrf_token"]);
                $tempData = $this->db->get_where($this->contractorTable, $post);
                if ($tempData->num_rows() == 1) {
                    $arrData = $tempData->row();
                }
            }
            $html = $this->load->view("hris/masterfile/contractor/modals/edit_content", array("data" => $arrData, "company_code" => $companyCodeList), true);
        }

        if ($html) {
            $resultset["response"] = true;
            $resultset["html"] = $html;
            $resultset["data"] = $arrData;
        } else {
            $resultset["response"] = false;
        }

        return $resultset;
    }

    function setModalContractor()
    {
        $resultset = array();
        $session = $this->core_layout->getCurrentSession();

        $post = $this->input->post();
        if (isset($post) && $post) {
            unset($post["csrf_token"]);

            $post["created_dt"] = date("Y-m-d H:i:s");
            $post["created_by"] = $session["emp_id"];

            $allow = $this->checkContractorName($post);
            if ($allow) {
                $insert = $this->db->insert($this->contractorTable, $post);
                if ($insert) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Contractor data has been added.";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " inserted new contractor: ".$post['contractor'],"insert", "success", "gcchris", "user");
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed saving contractor data!";
                    $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed inserting new contractor","insert", "error", "gcchris", "system");
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Contractor already exist!";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed inserting existing contractor","insert", "error", "gcchris", "system");
            }
        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
            $this->core_layout->setEventLog("Contractor masterfile - Error, No post data found.","insert", "error", "gcchris", "system");
        }

        return $resultset;
    }

    function updateModalContractor()
    {
        $resultset = array();
        $session = $this->core_layout->getCurrentSession();

        $post = $this->input->post();
        if (isset($post) && $post) {
            $id = $post["id"];
            unset($post["csrf_token"], $post["id"]);

            $post["modify_dt"] = date("Y-m-d H:i:s");
            $post["modify_by"] = $session["emp_id"];
            $currentContractData = $this->getContractorData($id);
            $update = $this->db->update($this->contractorTable, $post, array("id" => $id));
            if ($update) {
                $resultset["response"] = true;
                $resultset["toastr_msg"] = "Contractor data has been updated.";
                unset($post['modify_dt']); 
                unset($post['modify_by']);
                $changes = $this->logChanges($currentContractData ,$post);
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " updated contractor: ".$currentContractData->contractor." ".$changes,"update", "success", "gcchris", "user");
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed updating contractor data!";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " failed updating contractor: ".$currentContractData->contractor,"update", "error", "gcchris", "system");
            }
        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
            $this->core_layout->setEventLog("Contractor masterfile - Error, No post data found.","update", "error", "gcchris", "system");
        }

        return $resultset;
    }

    function removeCurrentContractor()
    {
        $resultset = array();
        $post = $this->input->post();
        if (isset($post) && $post) {
            unset($post["csrf_token"]);
            $updated = $this->db->update($this->contractorTable, array("is_archived" => 1), $post);
            $currentContractData = $this->getContractorData($post["id"]);
            if ($updated) {
                $session = $this->core_layout->getCurrentSession();
                $data = array(
                    "archived_table" => $this->contractorTable,
                    "archived_id" => $post["id"],
                    "archived_by" => $session["emp_id"]
                );

                $this->db->insert($this->archivedTable, $data);

                $resultset["response"] = true;
                $resultset["toastr_msg"] = "Contractor has been removed.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has archived contractor: ".$currentContractData->contractor,"archive", "success", "gcchris", "user");
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to remove contractor!";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has failed archiving contractor:  ".$currentContractData->contractor,"archive", "error", "gcchris", "system");
            }
        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
            $this->core_layout->setEventLog("Contractor masterfile - Error, No post data found.","archive", "error", "gcchris", "system");
        }

        return $resultset;
    }

    private function checkContractorName($data = array())
    {
        if ($data) {
            $query = $this->db->get_where($this->contractorTable, array("contractor" => $data["contractor"]));
            return $query->num_rows() == 0 ? true : false;
        } else {
            return false;
        }
    }

    function restoreCurrentContractor()
    {
        $resultset = array();
        $post = $this->input->post();
        if (isset($post) && $post) {
            unset($post["csrf_token"]);
            $updated = $this->db->update($this->contractorTable, array("is_archived" => 0), $post);
            $currentContractData = $this->getContractorData($post["id"]);
            if ($updated) {
                // $session = $this->core_layout->getCurrentSession();
                // $data = array(
                //     "archived_table" => $this->contractorTable,
                //     "archived_id" => $post["id"],
                //     "archived_by" => $session["emp_id"]
                // );

                // $this->db->insert($this->archivedTable, $data);

                $resultset["response"] = true;
                $resultset["toastr_msg"] = "Contractor has been restored.";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has restored contractor: ".$currentContractData->contractor,"restore", "success", "gcchris", "user");
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to restore contractor!";
                $this->core_layout->setEventLog("User ".$this->loggedInUsername. " has failed restoring contractor: ".$currentContractData->contractor,"restore", "error", "gcchris", "system");
            }
        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
            $this->core_layout->setEventLog("Contractor masterfile - Error, No post data found.","restore", "error", "gcchris", "system");
        }

        return $resultset;
    }

    private function logChanges($currentData, $newData) {
		$changes = array();
		$changesString = '';
		foreach ($currentData as $field => $value) {
			if (isset($newData[$field]) && $newData[$field]!= $value) {
				$changes[$field] = array(
					'old' => $value,
					'new' => $newData[$field]
				);
			}
		}
		foreach ($changes as $field => $change) {
			$changesString.= " Field: $field, from: $change[old], to: $change[new]\n";
		}
		return $changesString;
	}

    private function getContractorData($id) {
		$this->db->select("*");
		$this->db->from($this->contractorTable);
		$this->db->where('id', $id);
		$query = $this->db->get(); 
		return $query->row();
	}


}