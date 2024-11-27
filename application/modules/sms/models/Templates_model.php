<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Templates_model extends CI_Model
    {

        function __construct()
        {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->user_data = $this->session->userdata("logged_in");
        }

        private function getUserData()
        {
            return $this->core_layout->getUserLoggedIn();
        }

        function getTemplatesCollection()
        {
            $resultset = array();
            $post = $this->input->post();
            $search = (isset($post["search"]) && $post["search"]) ? $post["search"] : false;
            $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 10;
            $offset = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
            $sorByColumnIndex = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["column"] : null; // get column index
            $sortBy = (isset($post["order"]) && $post["order"]) ? $post["columns"][$sorByColumnIndex]["data"] : null; // get column name;
            $sortOrder = (isset($post["order"]) && $post["order"]) ? $post["order"][0]["dir"] : "desc";

            $rowCount = 0;
            $rowData = array();
            if (!$search) {
                $rowData = $this->get_all_templates($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_templates_count();
            }

            if ($search) {
                $rowData = $this->get_searched_templates($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_templates_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_templates_count()
        {
            $this->db->from("gccsms.tbltemplates");
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_templates($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {
            $sql = "a.id, a.template_name, a.message, a.user";
            $this->db->select($sql);
            $this->db->from("gccsms.tbltemplates a");

            if ((int)$limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("a.id", "DESC");
            }
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


        private function get_searched_templates($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.template_name", "a.message", "a.user");
                $sql = "a.id, a.template_name, a.message, a.user";
                $this->db->select($sql);
                $this->db->from("gccsms.tbltemplates a");
                $this->db->group_start();

                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search["value"], "both");
                    } else {
                        $this->db->or_like($field, $search["value"], "both");
                    }
                }

                $this->db->group_end();

                if ((int)$limit >= 0) {
                    $this->db->limit($limit, $offset);
                }

                if ($sortBy) {
                    $this->db->order_by($sortBy, $sortOrder);
                } else {
                    $this->db->order_by("a.id", "DESC");
                }
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
            } else {
                return array();
            }
        }

        private function get_searched_templates_count($search = null)
        {
            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.template_name", "a.message", "a.user");
                $sql = "a.id, a.template_name, a.message, a.user";
                $this->db->select($sql);
                $this->db->from("gccsms.tbltemplates a");

                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search["value"], "both");
                    } else {
                        $this->db->or_like($field, $search["value"], "both");
                    }
                }

                $this->db->group_end();
                $query = $this->db->get();

                $rowCount = $query->num_rows();
            }
            return $rowCount;
        }

        function addTemplate()
        {
            $data = array(
                'user' => $this->user_data['username'],
                'template_name' => $this->input->post('temp_name'),
                'message' => $this->input->post('message'),
            );

            $resultarray = array();
            if($this->checkDuplicate($this->input->post('temp_name')) == 0){
                
                $query = $this->db->insert('gccsms.tbltemplates', $data);
                if($query){
                    $resultarray["status"] = TRUE;
                    $resultarray["msg"] = "Successfully saved.";
                    $this->core_layout->setEventLog("Masterfile: Templates - added ".$data['template_name'],"insert", "success", "gccsms", "user");
                }else{
                    $resultarray["status"] = FALSE;
                    $resultarray["msg"] = "Error saving.";
                    $this->core_layout->setEventLog("Masterfile: Templates - tried to add ".$data['template_name'],"insert", "error", "gccsms", "user");
                }
            } else {
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Template name already exist";
            }
            
            return $resultarray;
        }

        function checkDuplicate($template_name){
            $this->db->select("id");
            $this->db->from("gccsms.tbltemplates");
            $this->db->where("template_name", $template_name);
            $query = $this->db->get();
            return $query->num_rows();
        }

        function editTemplate($id)
        {
            $this->db->select("*");
            $this->db->from("gccsms.tbltemplates");
            $this->db->where("id", $id);
            $query = $this->db->get();
            $results = $query->row_array();
            return $results;
        }

        function updateTemplate($id)
        {
            $this->input->post();
            $data = array(
                'user' => $this->user_data['username'],
                'template_name' => $this->input->post('temp_name'),
                'message' => $this->input->post('message'),
            );

            $msg_logs = "";
            $template_details = $this->getTemplateDetails($id);
            if($template_details["template_name"] == $data["template_name"]){
                $msg_logs = $data['template_name'];
            } else {
                $msg_logs = $template_details["template_name"]." into ".$data["template_name"];
            }

            $resultarray = array();
            if ($id) {
                $this->db->where('id', $id);
                $query = $this->db->update('gccsms.tbltemplates', $data);
                if($query){
                    $resultarray["status"] = TRUE;
                    $resultarray["msg"] = "Successfully updated.";
                    $this->core_layout->setEventLog("Masterfile: Templates - updated ".$msg_logs,"update", "success", "gccsms", "user");
                }else{
                    $resultarray["status"] = FALSE;
                    $resultarray["msg"] = "Error updating.";
                    $this->core_layout->setEventLog("Masterfile: Templates - tried to update ".$msg_logs,"update", "error", "gccsms", "user");
                }
            }
            return $resultarray;
        }

        function deleteTemplate($id)
        {
            $template_details = $this->getTemplateDetails($id);

            $resultarray = array();
            $query = $this->db->query("DELETE FROM gccsms.tbltemplates WHERE id=$id");
            if($query){
                $resultarray["status"] = TRUE;
                $resultarray["msg"] = "Successfully deleted.";
                $this->core_layout->setEventLog("Masterfile: Templates - deleted ".$template_details['template_name'],"delete", "success", "gccsms", "user");
            }else{
                $resultarray["status"] = FALSE;
                $resultarray["msg"] = "Error deleting.";
                $this->core_layout->setEventLog("Masterfile: Templates - tried to delete ".$template_details['template_name'],"delete", "error", "gccsms", "user");
            }
            return $resultarray;
        }

        function getTemplateDetails($id){
            $this->db->select("template_name, message");
            $this->db->from("gccsms.tbltemplates");
            $this->db->where("id", $id);
            $query = $this->db->get();
            return $query ? $query->row_array() : array();
        }
    }