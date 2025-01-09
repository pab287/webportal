<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hydracoremodel extends CI_Model {

    public function getAccountsCollection(){
        $this->db->select("*");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("status",1);
        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function getAccountById(){
        $this->db->select("*");
        $this->db->from("hydra_billing.accounts");
        $this->db->where("id",1);
        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->row_array() : false;
    }

}

?>
