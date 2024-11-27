<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Logs_m extends CI_Model{
        private $user_data = array();
        protected $tblHistory  = 'gccppm.tbldownload_history';
        protected $tblPolicy = 'gccppm.tblpolicy';
        protected $tblDocument = 'gccppm.tblpolicy_documents';
        protected $employee = 'gccmaster.tblemployees';

        function __construct(){
            parent::__construct();
            $this->user_data = $this->session->userdata("logged_in");
            $this->loggedInUsername = $this->user_data["username"];
        }

        function getLogsDatatableRequest(){
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
            // $filterFields = array('b.title', 'b.scope', 'b.objective', 'b.effective_date');
            $filterFields = array('b.title', 'b.effective_date');

            $resultset = array();

            $sql = "UPPER(CONCAT(emp.lastname,
            CASE WHEN UPPER(TRIM(emp.suffix)) != 'N/A' AND
                UPPER(TRIM(emp.suffix !='NONE')) AND emp.suffix !='' AND
                emp.suffix IS NOT NULL THEN CONCAT(' ', emp.suffix) ELSE ''
            END, ', ', emp.firstname, ' ',
            CASE WHEN UPPER(TRIM(emp.middlename)) != 'N/A' AND UPPER(TRIM(emp.middlename)) != 'NONE' AND
                    TRIM(emp.middlename) !='' AND emp.middlename IS NOT NULL
                THEN CONCAT(SUBSTR(emp.middlename, 1, 1), '.') ELSE ''
            END)) as employee_name, a.*, b.title, b.scope, b.objective, b.effective_date";

            $this->db->select($sql);
            $this->db->from($this->tblHistory.' a');
            $this->db->join($this->tblPolicy.' b', 'b.id = a.policy_id', 'LEFT');
            $this->db->join($this->tblDocument.' c', 'c.id = a.document_id', 'LEFT');
            $this->db->join($this->employee.' emp', 'emp.id = a.download_by', 'LEFT');

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
                foreach ($query->result() as $key => $rs) {
                    $rs->type = $rs->type == 0 ? 'download' : 'print';

                    $arrData[$key] = $rs;
                }

                foreach ($arrData as $k => $v) {
                    $resultset[] = $v;
                }
            }

            return $resultset;
        }

        function get_item_count($search = null){
            $filterFields = array('b.title', 'b.effective_date');

            $this->db->from($this->tblHistory.' a');
            $this->db->join($this->tblPolicy.' b', 'b.id = a.policy_id', 'LEFT');
            $this->db->join($this->tblDocument.' c', 'c.id = a.document_id', 'LEFT');
            $this->db->join($this->employee.' emp', 'emp.id = a.download_by', 'LEFT');

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
    }