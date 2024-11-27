<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Csf_model extends CI_Model {
    protected $csfTable = "gcccfs";

    function __construct()
    {
        parent::__construct();
            date_default_timezone_set('Asia/Manila');
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
            $this->load->model("ams/Utilities_model", "utilities");
    }

    private function getUserData()
        {
            return $this->core_layout->getUserLoggedIn();
        }


 function getItemCollection()
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
                $rowData = $this->get_all_item($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_item_count();
            }

            if ($search) {
                $rowData = $this->get_searched_item($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_item_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_item_count()
        {

            $this->db->from("gcccfs.items");
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_item($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.name, CONCAT(b.name) AS category";
            $this->db->select($sql);
            $this->db->from("gcccfs.items a");
            $this->db->join("gcccfs.category b", "a.category = b.id", "LEFT");
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


        private function get_searched_item($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.name", "a.category","b.name");
                $sql = "a.id, a.name, CONCAT(b.name) AS category";
                $this->db->select($sql);
                $this->db->from("gcccfs.items a");
                $this->db->join("gcccfs.category b", "a.category = b.id", "LEFT");
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

        private function get_searched_item_count($search = null)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.name", "a.category","b.name");
                $sql = "a.id, a.name, CONCAT(b.name) AS category";
                $this->db->select($sql);
                $this->db->from("gcccfs.items a");
                $this->db->join("gcccfs.category b", "a.category = b.id", "LEFT");

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

        public function save_item($data)
        {
            $this->db->insert('gcccfs.items', $data);
            return $this->db->insert_id();
        }

        public function edit_item($id)
        {
            $sql = "a.id, a.name, CONCAT(b.name) AS category,CONCAT(b.id) AS category_id";
                $this->db->select($sql);
                $this->db->from("gcccfs.items a");
                $this->db->join("gcccfs.category b", "a.category = b.id", "LEFT");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_item($where, $data)
        {
            $this->db->update('gcccfs.items', $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_item($id)
        {
            $this->db->where('id', $id);
            $this->db->delete('gcccfs.items');
        }
        function getCategory()
        {
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $query = $this->db->query("SELECT `id`,`name` FROM gcccfs.category WHERE `name` LIKE '%{$get['q']}%' ORDER BY `name` ASC");
            } else {
                $query = $this->db->query("SELECT `id`,`name` FROM gcccfs.category ORDER BY `name` ASC");
            }

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["name"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }
        function getCategoryCollection()
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
                $rowData = $this->get_all_category($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_category_count();
            }

            if ($search) {
                $rowData = $this->get_searched_category($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_category_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_category_count()
        {

            $this->db->from("gcccfs.category");
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_category($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.name";
            $this->db->select($sql);
            $this->db->from("gcccfs.category a");

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


        private function get_searched_category($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.name");
                $sql = "a.id, a.name";
                $this->db->select($sql);
                $this->db->from("gcccfs.category a");

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

        private function get_searched_category_count($search = null)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.name");
                $sql = "a.id, a.name";
                $this->db->select($sql);
                $this->db->from("gcccfs.category a");

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

        public function save_category($data)
        {
            $this->db->insert('gcccfs.category', $data);
            return $this->db->insert_id();
        }

        public function edit_category($id)
        {
            $sql = "a.id, a.name";

            $this->db->select($sql);
            $this->db->from("gcccfs.category a");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_category($where, $data)
        {
            $this->db->update('gcccfs.category', $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_category($id)
        {
            $this->db->where('id', $id);
            $this->db->delete('gcccfs.category');
        }

        function getProjectCollection()
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
                $rowData = $this->get_all_project($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_project_count();
            }

            if ($search) {
                $rowData = $this->get_searched_project($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_project_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_project_count()
        {

            $this->db->from("gcccfs.project");
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_project($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.name";
            $this->db->select($sql);
            $this->db->from("gcccfs.project a");
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


        private function get_searched_project($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.name");
                $sql = "a.id, a.name";
                $this->db->select($sql);
                $this->db->from("gcccfs.project a");
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

        private function get_searched_project_count($search = null)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.name");
                $sql = "a.id, a.name";
                $this->db->select($sql);
                $this->db->from("gcccfs.project a");

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
}