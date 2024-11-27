<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rate_card_m extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->model("ams/Utilities_model", "utilities");
        $this->load->model("Task_m", "task");
        $this->user_data = $this->session->userdata("logged_in");
    }

    function getChecklistPrivilegeId($form) {
        $checklist_priv_id = $this->db->get_where("gccpms.sf_checklist_privilege", array("checklist_id" => $form["id"]))->row("id");
        return array("checklist_priv_id" => $checklist_priv_id);
    }

    function getRateCardUpdateHistory($checklist_priv_id) {
        $resultSet = array();
        $table = "gccpms.sf_checklist_rates_history clist_rate_history";
        $tableConfig = $this->input->post();
        $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

        $joinArr = array(
            array(
                "table" => "gccpms.sf_checklist_privilege_rates clist_rates",
                "condition" => "clist_rates.id = clist_rate_history.priv_rates_id",
                "option" => "INNER"
            ),
            array(
                "table" => "gccpms.sf_item items",
                "condition" => "items.id = clist_rates.item_id",
                "option" => "INNER"
            ),
            array(
                "table" => "gccmaster.tblemployees emp",
                "condition" => "emp.id = clist_rate_history.applied_by",
                "option" => "INNER"
            ),
        );

        $searchFields = "CONCAT(clist_rate_history.prev_tariff, clist_rate_history.prev_unit, clist_rate_history.prev_category, items.label, 
                             clist_rate_history.new_tariff, clist_rate_history.new_unit, clist_rate_history.new_category, 
                             UCASE(CONCAT(emp.firstname, ' ', emp.lastname)))";

        $where = array("clist_rate_history.checklist_priv_id" => $checklist_priv_id);
        $this->db->select("clist_rate_history.*, clist_rates.item_id, items.label, UCASE(CONCAT(emp.firstname, ' ', emp.lastname)) `applied_by_name`");
        $this->db->from($table);
        foreach ($joinArr as $join) {
            $this->db->join($join["table"], $join["condition"], $join["option"]);
        }

        $this->db->where($where);
        $this->db->like($searchFields, $pageOptions->search, "both");
        if ($pageOptions->length > -1) {
            $this->db->limit($pageOptions->length, $pageOptions->start);
        }

        $queryResult = $this->db
            ->order_by($pageOptions->order_column, $pageOptions->order_direction)
            ->get();
        $data = $queryResult->result();

        $search = array('field' => $searchFields, 'key' => $pageOptions->search, 'option' => "both");
        // $resultSet["sql"] = $this->db->last_query(); // for debugging only
        $resultSet["recordsTotal"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
        $resultSet["recordsFiltered"] = $this->utilities->getTableCount($table, $where, $search, $joinArr);
        $resultSet["data"] = $data;

        return $resultSet;
    }

    function getCurrentChecklistRate($priv_rate_id) {
        $this->db->select("priv_rates.*, items.label");
        $this->db->where("priv_rates.id", $priv_rate_id);
        $this->db->join("gccpms.sf_item items", "items.id = priv_rates.item_id", "INNER");
        return $this->db->get("gccpms.sf_checklist_privilege_rates priv_rates")->row();
    }

    function revertRate($id) {
        $history = $this->db->get_where("gccpms.sf_checklist_rates_history", array("id" => $id))->row();
        $current = $this->db->get_where("gccpms.sf_checklist_privilege_rates", array("id" => $history->priv_rates_id))->row();
        $checklist_id = $this->db
            ->get_where("gccpms.sf_checklist_privilege", array("id" => $history->checklist_priv_id))->row("checklist_id");

        $this->db->trans_begin();

        /* SAVE UPDATES TO HISTORY */
        $data = array(
            "checklist_priv_id" => $history->checklist_priv_id,
            "priv_rates_id" => $history->priv_rates_id,
            "prev_tariff" => $current->tariff,
            "prev_unit" => strtoupper($current->unit),
            "prev_category" => strtoupper($current->category),
            "new_tariff" => $history->prev_tariff,
            "new_unit" => strtoupper($history->prev_unit),
            "new_category" => strtoupper($history->prev_category),
            "applied_by" => $this->user_data["emp_id"],
            "status" => "reverted"
        );

        $this->db->insert("gccpms.sf_checklist_rates_history", $data);
        /* SAVE UPDATES TO HISTORY */

        $this->db->where("id", $history->priv_rates_id);
        $this->db->set("tariff", $history->prev_tariff);
        $this->db->set("unit", strtoupper($history->prev_unit));
        $this->db->set("category", strtoupper($history->prev_category));
        $this->db->update("gccpms.sf_checklist_privilege_rates");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array("success" => false, "data" => array("tree" => array()));
        } else {
            $this->db->trans_commit();
            return array(
                "success" => true,
                "data" => $this->task->getInitModalData(array("id" => $checklist_id)),);
        }
    }
}

/* End of file .php */