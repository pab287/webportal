<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Document_model extends CI_Model
    {

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

        function getTagCollection()
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
                $rowData = $this->get_all_tag($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_tag_count();
            }

            if ($search) {
                $rowData = $this->get_searched_tag($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_tag_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_tag_count()
        {

            $this->db->from("dbarchive.tag");
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_tag($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.tag";
            $this->db->select($sql);
            $this->db->from("dbarchive.tag a");

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


        private function get_searched_tag($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.tag");
                $sql = "a.id, a.tag";
                $this->db->select($sql);
                $this->db->from("dbarchive.tag a");

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

        private function get_searched_tag_count($search = null)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.tag");
                $sql = "a.id, a.tag";
                $this->db->select($sql);
                $this->db->from("dbarchive.tag a");

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

        public function save_tag($data)
        {
            $this->db->insert('dbarchive.tag', $data);
            return $this->db->insert_id();
        }

        public function edit_tag($id)
        {
            $sql = "a.id, a.tag";

            $this->db->select($sql);
            $this->db->from("dbarchive.tag a");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_tag($where, $data)
        {
            $this->db->update('dbarchive.tag', $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_tag($id)
        {
            $this->db->where('id', $id);
            $this->db->delete('dbarchive.tag');
        }

        function getClassificationCollection()
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
                $rowData = $this->get_all_classification($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_classification_count();
            }

            if ($search) {
                $rowData = $this->get_searched_classification($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_classification_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_classification_count()
        {

            $this->db->from("dbarchive.classification");
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_classification($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.description, a.type";
            $this->db->select($sql);
            $this->db->from("dbarchive.classification a");

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


        private function get_searched_classification($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.description", "a.type");
                $sql = "a.id, a.description, a.type";
                $this->db->select($sql);
                $this->db->from("dbarchive.classification a");

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

        private function get_searched_classification_count($search = null)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.description", "a.type");
                $sql = "a.id, a.description, a.type";
                $this->db->select($sql);
                $this->db->from("dbarchive.classification a");

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

        public function save_classification($data)
        {
            $this->db->insert('dbarchive.classification', $data);
            return $this->db->insert_id();
        }

        public function edit_classification($id)
        {
            $sql = "a.id, a.description, a.type";

            $this->db->select($sql);
            $this->db->from("dbarchive.classification a");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_classification($where, $data)
        {
            $this->db->update('dbarchive.classification', $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_classification($id)
        {
            $this->db->where('id', $id);
            $this->db->delete('dbarchive.classification');
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

            $this->db->from("dbarchive.company");
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_project($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.name";
            $this->db->select($sql);
            $this->db->from("dbarchive.company a");

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
                $this->db->from("dbarchive.company a");

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
                $this->db->from("dbarchive.company a");

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

        public function save_project($data)
        {
            $this->db->insert('dbarchive.company', $data);
            return $this->db->insert_id();
        }

        public function edit_project($id)
        {
            $sql = "a.id, a.name";

            $this->db->select($sql);
            $this->db->from("dbarchive.company a");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_project($where, $data)
        {
            $this->db->update('dbarchive.company', $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_project($id)
        {
            $this->db->where('id', $id);
            $this->db->delete('dbarchive.company');
        }

        function getDocumentCollection($type)
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
                $rowData = $this->get_all_document($limit, $offset, $sortBy, $sortOrder, $type);
                $rowCount = $this->get_all_document_count($type);
            }

            if ($search) {
                $rowData = $this->get_searched_document($search, $limit, $offset, $sortBy, $sortOrder, $type);
                $rowCount = $this->get_searched_document_count($search, $type);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_document_count($type)
        {

            $this->db->from("dbarchive.documents");
            $this->db->where('type', $type);
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_document($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $type)
        {

            $sql = "a.id, a.reference, CONCAT(b.description) AS classification, CONCAT(c.description) AS department, a.description, a.type, a.document_dt";
            $this->db->select($sql);
            $this->db->from("dbarchive.documents a");
            $this->db->join("dbarchive.classification b", "a.classification_id = b.id", "LEFT");
            $this->db->join("gcchris.tbldepartments c", "a.department_id = c.id", "LEFT");
            $this->db->where('a.type', $type);

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


        private function get_searched_document($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $type)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.reference", "b.description", "c.description", "a.description", "a.type", "a.document_dt");
                $sql = "a.id, a.reference, CONCAT(b.description) AS classification, CONCAT(c.description) AS department, a.description, a.type, a.document_dt";
                $this->db->select($sql);
                $this->db->from("dbarchive.documents a");
                $this->db->join("dbarchive.classification b", "a.classification_id = b.id", "LEFT");
                $this->db->join("gcchris.tbldepartments c", "a.department_id = c.id", "LEFT");
                $this->db->where('a.type', $type);
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

        private function get_searched_document_count($search = null, $type)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.reference", "b.description", "c.description", "a.description", "a.type", "a.document_dt");
                $sql = "a.id, a.reference, CONCAT(b.description) AS classification, CONCAT(c.description) AS department, a.description, a.type, a.document_dt";
                $this->db->select($sql);
                $this->db->from("dbarchive.documents a");
                $this->db->join("dbarchive.classification b", "a.classification_id = b.id", "LEFT");
                $this->db->join("gcchris.tbldepartments c", "a.department_id = c.id", "LEFT");
                $this->db->where('a.type', $type);
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

        public function save_document($data)
        {
            $this->db->insert('dbarchive.documents', $data);
            return $this->db->insert_id();
        }

        public function edit_document($id)
        {
            $sql = "a.id, a.reference, a.classification_id, a.department_id, a.description, a.type, a.document_dt, CONCAT(b.description) AS classification, CONCAT(c.description) AS department";

            $this->db->select($sql);
            $this->db->from("dbarchive.documents a");
            $this->db->join("dbarchive.classification b", "a.classification_id = b.id", "LEFT");
            $this->db->join("gcchris.tbldepartments c", "a.department_id = c.id", "LEFT");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_document($where, $data)
        {
            $this->db->update('dbarchive.documents', $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_document($id)
        {
            $this->db->where('id', $id);
            $this->db->delete('dbarchive.documents');
        }

        function getClassification()
        {
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $query = $this->db->query("SELECT `id`,`description` FROM dbarchive.classification WHERE `description` LIKE '%{$get['q']}%' ORDER BY `description` ASC");
            } else {
                $query = $this->db->query("SELECT `id`,`description` FROM dbarchive.classification ORDER BY `description` ASC");
            }

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["description"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function getDepartment()
        {
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $query = $this->db->query("SELECT `id`,`description` FROM gcchris.tbldepartments WHERE `description` LIKE '%{$get['q']}%' ORDER BY `description` ASC");
            } else {
                $query = $this->db->query("SELECT `id`,`description` FROM gcchris.tbldepartments ORDER BY `description` ASC");
            }

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["id"];
                    $data["text"] = $_query["description"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        function getFileCollection($id)
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
                $rowData = $this->get_all_file($limit, $offset, $sortBy, $sortOrder, $id);
                $rowCount = $this->get_all_file_count($id);
            }

            if ($search) {
                $rowData = $this->get_searched_file($search, $limit, $offset, $sortBy, $sortOrder, $id);
                $rowCount = $this->get_searched_file_count($search, $id);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_file_count($id)
        {

            $this->db->from("dbarchive.document_body");
            $this->db->where('document_id', $id);
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_file($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $id)
        {

            $sql = "a.id, a.tag1, a.description, a.filename, a.modify_dt";
            $this->db->select($sql);
            $this->db->from("dbarchive.document_body a");
            $this->db->where('document_id', $id);

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


        private function get_searched_file($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC", $id)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.tag1", "a.description", "a.filename", "a.modify_dt");
                $sql = "a.id, a.tag1, a.description, a.filename, a.modify_dt";
                $this->db->select($sql);
                $this->db->from("dbarchive.document_body a");
                $this->db->where('document_id', $id);
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

        private function get_searched_file_count($search = null, $id)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.tag1", "a.description", "a.filename", "a.modify_dt");
                $sql = "a.id, a.tag1, a.description, a.filename, a.modify_dt";
                $this->db->select($sql);
                $this->db->from("dbarchive.document_body a");
                $this->db->where('document_id', $id);
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

        function getTag()
        {
            $get = $this->input->get();
            $resultarray = array();
            if (isset($get['q'])) {
                $query = $this->db->query("SELECT `tag` FROM dbarchive.tag WHERE `tag` LIKE '%{$get['q']}%' ORDER BY `tag` ASC");
            } else {
                $query = $this->db->query("SELECT `tag` FROM dbarchive.tag ORDER BY `tag` ASC");
            }

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $_query) {
                    $data = array();
                    $data["id"] = $_query["tag"];
                    $data["text"] = $_query["tag"];
                    $resultarray[] = $data;
                }
            }
            return array("results" => $resultarray);
        }

        public function save_file($data)
        {
            $this->db->insert('dbarchive.document_body', $data);
            return $this->db->insert_id();
        }

        public function edit_file($id)
        {
            $sql = "a.id, a.filename, a.document_id, a.tag1, a.description ";

            $this->db->select($sql);
            $this->db->from("dbarchive.document_body a");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_file($where, $data)
        {
            $this->db->update('dbarchive.document_body', $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_file($id)
        {
            $this->db->where('id', $id);
            $this->db->delete('dbarchive.document_body');
        }

        function get_datatables($id)
        {

            $this->db->from('dbarchive.document_body');
            $this->db->where('document_id', $id);
            $query = $this->db->get();
            return $query->result();
        }

        function getMemoCollection()
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
                $rowData = $this->get_all_memo($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_memo_count();
            }

            if ($search) {
                $rowData = $this->get_searched_memo($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_memo_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_memo_count()
        {

            $this->db->from("dbarchive.memo");
            $this->db->where('is_super', '0');
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_memo($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.filename, a.tag1, a.number, a.year, a.subject, a.is_fav";
            $this->db->select($sql);
            $this->db->from("dbarchive.memo a");
            $this->db->where('a.is_super', '0');

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


        private function get_searched_memo($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.filename", "a.tag1", "a.number", "a.year", "a.subject", "a.is_fav");
                $sql = "a.id, a.filename, a.tag1, a.number, a.year, a.subject, a.is_fav";
                $this->db->select($sql);
                $this->db->from("dbarchive.memo a");
                $this->db->where('a.is_super', '0');

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

        private function get_searched_memo_count($search = null)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.filename", "a.tag1", "a.number", "a.year", "a.subject", "a.is_fav");
                $sql = "a.id, a.filename, a.tag1, a.number, a.year, a.subject, a.is_fav";
                $this->db->select($sql);
                $this->db->from("dbarchive.memo a");
                $this->db->where('a.is_super', '0');

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

        public function save_memo($data)
        {
            $this->db->insert('dbarchive.memo', $data);
            return $this->db->insert_id();
        }

        public function edit_memo($id)
        {
            $sql = "a.id, a.filename, a.tag1, a.number, a.year, a.subject, a.is_fav";

            $this->db->select($sql);
            $this->db->from("dbarchive.memo a");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function update_memo($where, $data)
        {
            $this->db->update('dbarchive.memo', $data, $where);
            return $this->db->affected_rows();
        }

        public function delete_memo($id)
        {
            $this->db->where('id', $id);
            $this->db->delete('dbarchive.memo');
        }

        function getFavCollection()
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
                $rowData = $this->get_all_fav($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_fav_count();
            }

            if ($search) {
                $rowData = $this->get_searched_fav($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_fav_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_fav_count()
        {

            $this->db->from("dbarchive.memo");
            $this->db->where('is_fav', '1');
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_fav($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.filename, a.tag1, a.number, a.year, a.subject, a.is_fav";
            $this->db->select($sql);
            $this->db->from("dbarchive.memo a");
            $this->db->where('a.is_fav', '1');

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


        private function get_searched_fav($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.filename", "a.tag1", "a.number", "a.year", "a.subject", "a.is_fav");
                $sql = "a.id, a.filename, a.tag1, a.number, a.year, a.subject, a.is_fav";
                $this->db->select($sql);
                $this->db->from("dbarchive.memo a");
                $this->db->where('a.is_fav', '1');

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

        private function get_searched_fav_count($search = null)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.filename", "a.tag1", "a.number", "a.year", "a.subject", "a.is_fav");
                $sql = "a.id, a.filename, a.tag1, a.number, a.year, a.subject, a.is_fav";
                $this->db->select($sql);
                $this->db->from("dbarchive.memo a");
                $this->db->where('a.is_fav', '1');

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

        function getSuperCollection()
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
                $rowData = $this->get_all_super($limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_all_super_count();
            }

            if ($search) {
                $rowData = $this->get_searched_super($search, $limit, $offset, $sortBy, $sortOrder);
                $rowCount = $this->get_searched_super_count($search);
            }

            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        private function get_all_super_count()
        {

            $this->db->from("dbarchive.memo");
            $this->db->where('is_super', '1');
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_super($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.filename, a.tag1, a.number, a.year, a.subject, a.is_fav";
            $this->db->select($sql);
            $this->db->from("dbarchive.memo a");
            $this->db->where('a.is_super', '1');

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


        private function get_searched_super($search = null, $limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.filename", "a.tag1", "a.number", "a.year", "a.subject", "a.is_fav");
                $sql = "a.id, a.filename, a.tag1, a.number, a.year, a.subject, a.is_fav";
                $this->db->select($sql);
                $this->db->from("dbarchive.memo a");
                $this->db->where('a.is_super', '1');

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

        private function get_searched_super_count($search = null)
        {

            $rowCount = 0;
            if ($search) {
                $filterFields = array("a.id", "a.filename", "a.tag1", "a.number", "a.year", "a.subject", "a.is_fav");
                $sql = "a.id, a.filename, a.tag1, a.number, a.year, a.subject, a.is_fav";
                $this->db->select($sql);
                $this->db->from("dbarchive.memo a");
                $this->db->where('a.is_super', '1');

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

        function count_document()
        {

            $this->db->from("dbarchive.document_body");

            $query = $this->db->get();
            return $query->num_rows();
        }

        function count_recent()
        {
            $check = date('Y-m-d', strtotime("-7 days"));
            $this->db->from("dbarchive.document_body");
            $this->db->where('created_dt >= ', $check);
            $query = $this->db->get();
            return $query->num_rows();
        }

        function countClass()
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

            $rowData = $this->getCount($limit, $offset, $sortBy, $sortOrder);
            $rowCount = $this->get_all_count();


            $totalNotFiltered = $rowCount;

            $resultset["recordsTotal"] = $rowCount;
            $resultset["recordsFiltered"] = $rowCount;
            $resultset["data"] = $rowData;

            return $resultset;
        }

        function get_all_count()
        {
            $this->db->from("dbarchive.classification");
            $query = $this->db->get();
            return $query->num_rows();
        }

        function getCount($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {
            $this->db->select("docs.reference, class.id, class.description, IF(docs.reference IS NULL, 0, COUNT(class.id)) total");
            $this->db->join("dbarchive.document_body body", "body.document_id = docs.id", "inner");
            $this->db->join("dbarchive.classification class", "docs.classification_id = class.id", "right");
            $this->db->group_by("class.id");

            if ((int)$limit >= 0) {
                $this->db->limit($limit, $offset);
            }

            if ($sortBy) {
                $this->db->order_by($sortBy, $sortOrder);
            } else {
                $this->db->order_by("description", "ASC");
            }

            $query = $this->db->get("dbarchive.documents docs");
            return $query->result();
        }

        function get_total_files($id)
        {
            $this->db->from("dbarchive.documents a");
            $this->db->join("dbarchive.document_body b", "a.id = b.document_id", "LEFT");
            $this->db->where('a.classification_id', $id);

            $query = $this->db->get();
            return $query->num_rows();
        }

        function saveMemo()
        {
            $user_id = $this->core_layout->getUserId();
            $date = date('Y-m-d H:i:s');
            $post = $this->utilities->parseFormDataToObject($this->input->post());
            $post->tag1 = "," . implode(",", $post->tag_id);
            $post->created_by = $user_id;
            $post->created_dt = $date;
            unset($post->id, $post->current_filename, $post->tag_id);

            if ($_FILES['files']['name']) {
                return $_FILES['files']['name'];
            } else {
                return $post;
            }
        }
    }