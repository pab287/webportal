<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Work_order_m extends CI_Model
{
    protected $woTypeTable = "gccpms.sf_wo_type";

    public function __construct()
    {
        parent::__construct();
        $this->user_data = $this->session->userdata("logged_in");
        date_default_timezone_set("Asia/Manila");
    }

    function getWoTypeDatatableRequest()
    {
        $post = $this->input->post();
        if ($post) {
            $orderx = (isset($post["order"]) && $post["order"]) ? $post["order"] : false;
            $columns = array("code", "label", "series", "is_active", "id");
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
            $tempTable = $this->dt_model->dataTable();
            $tempTable->setTable($this->woTypeTable);
            $tempTable->setParameterFields($columns);

            $parameters = array();
            $parameters["status"] = 1;

            $tempTable->setWhereParameters($parameters);
            $totalData = $tempTable->dtAllPostsCount();
            $totalFiltered = $totalData;

            if (empty($searchValue)) {
                $posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
            } else {
                $posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
                $totalFiltered = $tempTable->dtPostSearchCount($searchValue);
            }

            $data = array();
            if (!empty($posts)) {
                foreach ($posts as $pst) {
                    $nestedData['id'] = $pst->id;
                    $nestedData['code'] = $pst->code;
                    $nestedData['label'] = $pst->label;
                    $nestedData['series'] = $pst->series;
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

    function setModalWoType()
    {
        $resultset = array();
        $session = $this->core_layout->getCurrentSession();

        $post = $this->input->post();
        if (isset($post) && $post) {
            unset($post["csrf_token"]);
            $post["code"] = strtolower($post["code"]);
            $post["label"] = strtoupper($post["label"]);
            $allow = $this->checkWoTypeCode($post);
            if ($allow) {
                $insert = $this->db->insert($this->woTypeTable, $post);
                if ($insert) {
                    $resultset["response"] = true;
                    $resultset["toastr_msg"] = "Work order type has been added.";
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed saving work order type data!";
                }
            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Work order type code already exist!";
            }
        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "No post data found!";
        }

        return $resultset;
    }

    public function getWoTypeSelect2Data($field = null)
    {
        $field = ($field) ? "{$field} as id" : "id";
        $resultset = array();
        $arrData = array();
        $where = array("is_active" => 1, "status" => 1);

        $get = $this->input->get();
        $this->db->select("{$field}, label as text");
        $this->db->from($this->woTypeTable);
        $this->db->where($where);
        if (isset($get["term"]) && $get["term"]) {
            $this->db->like("label", $get["term"], "both");
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $arrData = $query->result();
        }

        $resultset["results"] = $arrData;
        return $resultset;
    }

    private function checkWoTypeCode($data = array())
    {
        if ($data) {
            $query = $this->db->get_where($this->woTypeTable, array("code" => $data["code"]));
            if ($query->num_rows() == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function editWorkOrderType($id)
    {
        $post = $this->input->post();
        $this->db->where("id", $id);
        return $this->db->update($this->woTypeTable, $post);
    }

    function archiveWorkOrderType($id)
    {
        $this->db->trans_begin();

        $this->db->where("id", $id);
        $this->db->set("status", 0);
        $this->db->update($this->woTypeTable);

        $this->core_layout->insertArchiveLog($this->woTypeTable, $id);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
}