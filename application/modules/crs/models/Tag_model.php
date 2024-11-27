<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Tag_model extends CI_Model
    {
        protected $tagTable = "dbhrd.tag";
        function __construct()
        {
            parent::__construct();
            $this->user_id = $this->core_layout->getUserId();
            $this->load->model("access_control_model", "acl_model");
            $this->load->model("datatable_model", "dt_model");
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

            $this->db->from("dbhrd.tag");
            $query = $this->db->get();
            return $query->num_rows();
        }

        private function get_all_tag($limit = 10, $offset = 0, $sortBy = null, $sortOrder = "DESC")
        {

            $sql = "a.id, a.tag";
            $this->db->select($sql);
            $this->db->from("dbhrd.tag a");

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
                $this->db->from("dbhrd.tag a");

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
                $this->db->from("dbhrd.tag a");

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

        public function addTag()
        {
            $post = $this->input->post();
            $resultArray = array();
            $post['created_by'] = $this->user_id;
            $post['created_dt'] = date('Y-m-d H:i'); 
            unset($post['id']);
            $result = $this->db->insert($this->tagTable, $post);
            if ($result) {
                $lastInsertId = $this->db->insert_id();
                $resultArray['success'] = true;
                $resultArray['msg'] = "Tag successfully added";
                $this->core_layout->setEventLog("New tag added: ".$post['tag']." with id: ".$lastInsertId,"insert", "success", "dbhrd", "user");
            } else {
                $resultArray['success'] = false;
                $resultArray['msg'] = "Failed to add tag";
                $this->core_layout->setEventLog("Failed to add tag","insert", "error", "dbhrd", "system");
            }
        
            return $resultArray;
        }

        public function edit_tag($id)
        {
            $sql = "a.id, a.tag";

            $this->db->select($sql);
            $this->db->from("dbhrd.tag a");
            $this->db->where('a.id', $id);
            $query = $this->db->get();

            return $query->row();
        }

        public function updateTag()
        {
            $resultArray = array();
            $post = $this->input->post();
            $id = $post['id'];
            unset($post['id']); 
            $post['modify_by'] = $this->user_id; 
            $post['modify_dt'] = date('Y-m-d H:i'); 
            $currentTagData = $this->getTagDataById($id); 
            $this->db->where("id", $id);
            $result = $this->db->update('dbhrd.tag', $post);
        
            if ($result) {
                $affectedRows = $this->db->affected_rows();
                $resultArray['success'] = true;
                $resultArray['msg'] = "Tag successfully updated";
                unset($post['modify_by']);
                unset($post['modify_dt']);
                $changes = $this->logChanges($currentTagData, $post); 
                $this->core_layout->setEventLog("Updated tag id: ".$id." Changes: ".$changes, "update", "success", "dbhrd", "user");
            } else {
                $resultArray['success'] = false;
                $resultArray['msg'] = "Failed to update tag";
                $this->core_layout->setEventLog("Failed to update tag", "update", "error", "dbhrd", "system");
            }
        
            return $resultArray;
        }

        public function deleteTag()
        {
            $post = $this->input->post();
            $id = $post['id'];
            $this->db->where('id', $id);
            $result = $this->db->delete('dbhrd.Tag');
            $resultArray = array();
            if ($result) {
                $resultArray['success'] = true;
                $resultArray['msg'] = "Tag successfully deleted";
                $this->core_layout->setEventLog("Tag deleted: ".$id,"delete", "success", "dbhrd", "user");
            } else {
                $resultArray['success'] = false;
                $resultArray['msg'] = "Failed to delete Tag";
                $this->core_layout->setEventLog("Failed to delete Tag","delete", "error", "dbhrd", "system");
            }
        
            return $resultArray;
        }

        private function getTagDataById($id) {
            $this->db->select("*");
            $this->db->from($this->tagTable);
            $this->db->where('id', $id);
            $query = $this->db->get(); 
            return $query->row();
        }

        private function logChanges($currentData, $newData) {
            if (is_object($currentData)) {
                $currentData = get_object_vars($currentData);
            }
            if (is_object($newData)) {
                $newData = get_object_vars($newData);
            }
            $changes = array();
            $changesString = '';
            foreach ($currentData as $field => $value) {
                if (isset($newData[$field]) && $newData[$field]!== $value) {
                    $changes[$field] = array(
                        'old' => $value,
                        'new' => $newData[$field]
                    );
                }
            }
            foreach ($changes as $field => $change) {
                if ($field != 'company' && $field != 'department'){
                    $changesString.= " Field: $field, from: ". $change['old']. ", to: ". $change['new']. "\n";
                }
            }
            return $changesString;
        }

    }