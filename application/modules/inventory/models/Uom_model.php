<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Uom_model extends CI_Model {
        protected $uomTable = "uom";
        protected $loggedUser;
        protected $loggedUserName;

        function __construct() {
            parent::__construct();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->loggedUser = $this->session->userdata('logged_in');
            $this->loggedUserName = $this->loggedUser["firstname"] . " " . $this->loggedUser["lastname"];
        }

        function addInventoryUom() {
            $post = $this->input->post();
            $trailAction = "ADDED NEW UOM ";
            $ctr = 1;
            $count = count($post);
            foreach ($post as $key => $value) {
                $trailAction .= " " . strtoupper($key) . ":" . strtoupper($value) . (intval($ctr) < $count ? ", " : " ");
                $ctr++;
            }

            if ($post) {
                $uomCode = $this->checkUomCode($post);
                if ($uomCode) {
                    if (!isset($post["uom_code"])) {
                        $post["uom_code"] = $this->core_layout->generateCode();
                    }
                    $insert = $this->db->insert($this->uomTable, $post);
                    if ($insert) {
                        $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                        $result["response"] = true;
                        $result["toastr_msg"] = "Unit of measure has been saved.";
                    } else {
                        $result["response"] = false;
                        $result["toastr_msg"] = "Error in saving unit of measure!";
                    }
                } else {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Unit of measure code already exist!";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for saving!";
            }

            return $result;
        }

        function updateInventoryUom() {
            $post = $this->input->post();
            $result = array();
            if (isset($post["id"]) && $post["id"]) {
                $id = $post["id"];
                unset($post["id"]);

                $trailAction = "UPDATED UOM ";
                $ctr = 1;
                $count = count($post) - 1;
                foreach ($post as $key => $value) {
                    if ($key === "id") continue;
                    $trailAction .= " " . strtoupper($key) . ":" . strtoupper($value) . (intval($ctr) < $count ? ", " : " ");
                    $ctr++;
                }

                $update = $this->db->update($this->uomTable, $post, array("id" => $id));
                if ($update) {
                    $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                    $result["response"] = true;
                    $result["toastr_msg"] = "Unit of measure has been updated.";
                } else {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Error in updating unit of measure!";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for saving!";
            }

            return $result;
        }

        function removeInventoryUom() {
            $post = $this->input->post();

            $uom = $this->db->get_where($this->uomTable, array("id" => $post["id"]))->row();

            $result = array();
            if (isset($post["id"]) && $post["id"]) {
                $this->db->delete($this->uomTable, array('id' => $post['id']));
                if (!$this->db->affected_rows()) {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Error! ID [{$post['id']}] not found";
                } else {
                    $trailAction = "DELETED UOM WITH CODE:$uom->uom_code, DESC: $uom->uom_desc";
                    $this->core_layout->saveLog($trailAction, $this->loggedUserName);
                    $result["response"] = true;
                    $result["toastr_msg"] = "Unit of measure has been removed.";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for removal!";
            }

            return $result;
        }

        function getInventoryUomItem() {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"]) && $post["id"]) {
                $query = $this->db->get_where($this->uomTable, $post);
                if ($query->num_rows() == 1) {
                    $resultset["response"] = true;
                    $resultset["value"] = $query->row_array();
                } else {
                    $resultset["response"] = false;
                }
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        function getUomList() {
            $post = $this->input->post();
            if ($post) {
                $columns = array("id", "uom_code", "uom_desc");
                $dir = $post["order"][0]["dir"];
                $order = $columns[$post["order"][0]["column"]];
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtAccessControl = $this->dt_model->dataTable();
                $dtAccessControl->setTable($this->uomTable);
                $dtAccessControl->setParameterFields($columns);

                $totalData = $dtAccessControl->dtAllPostsCount();
                $totalFiltered = $totalData;

                if (empty($searchValue)) {
                    $posts = $dtAccessControl->dtAllPosts($limit, $start, $order, $dir);
                } else {
                    $posts = $dtAccessControl->dtSearch($limit, $start, $searchValue, $order, $dir);
                    $totalFiltered = $dtAccessControl->dtPostSearchCount($searchValue);
                }

                $data = array();
                if (!empty($posts)) {
                    foreach ($posts as $pst) {
                        $nestedData['id'] = $pst->id;
                        $nestedData['uom_code'] = $pst->uom_code;
                        $nestedData['uom_desc'] = $pst->uom_desc;
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

        private function checkUomCode($data = array()) {
            if ($data) {
                $query = $this->db->get_where($this->uomTable, array("uom_code" => $data["uom_code"]));
                if ($query->num_rows() == 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }