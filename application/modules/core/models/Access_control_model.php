<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Access_control_model extends CI_Model
    {
        protected $moduleTable = "modules";
        protected $modulePrivTable = "module_privilege";
        protected $roleAclTable = "user_role_acl";
        protected $aclTable = "access_control_list";
        protected $aclPrivilegeTable = "acl_privilege";
        protected $privListTable = "privilege_list";

        function __construct()
        {
            parent::__construct();
            $this->load->model("core/datatable_model", "dt_model");
        }

        function addAcessControl()
        {
            $post = $this->input->post();
            $result = array();
            if ($post) {
                $aclName = $this->checkAclName($post);
                if ($aclName) {
                    if (!isset($post["acl_code"])) {
                        $post["acl_code"] = $this->core_layout->generateCode();
                    }
                    $insert = $this->db->insert($this->aclTable, $post);
                    if ($insert) {
                        $result["response"] = true;
                        $result["toastr_msg"] = "Access control has been saved.";
                    } else {
                        $result["response"] = false;
                        $result["toastr_msg"] = "Error in saving access control!";
                    }
                } else {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Access control name already exist!";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for saving!";
            }

            return $result;
        }

        function updateAcessControl()
        {
            $post = $this->input->post();
            $result = array();
            if (isset($post["id"]) && $post["id"]) {
                $id = $post["id"];
                unset($post["id"]);
                $update = $this->db->update($this->aclTable, $post, array("id" => $id));
                if ($update) {
                    $result["response"] = true;
                    $result["toastr_msg"] = "Access control has been updated.";
                } else {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Error in updating access control!";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for saving!";
            }

            return $result;
        }

        function removeAccessControl()
        {
            $post = $this->input->post();
            $result = array();
            if (isset($post["id"]) && $post["id"]) {
                $this->db->delete($this->aclTable, array('id' => $post['id']));
                if (!$this->db->affected_rows()) {
                    $result["response"] = false;
                    $result["toastr_msg"] = "Error! ID [{$post['id']}] not found";
                } else {
                    $result["response"] = true;
                    $result["toastr_msg"] = "Access control has been removed.";
                }
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "No available data for removal!";
            }

            return $result;
        }

        function listAccessControl($search)
        {
            $arrData = $this->accessControlJsonTree($search);
            $html = $this->load->view("core/access_control/modal_content/list", null, true);

            $result = array();
            $result["response"] = true;
            $result["data"] = $arrData;
            $result["html"] = $html;

            return $result;
        }

        function accessControlJson($id = null, $roleIds = array())
        {
            $arrData = array();
            if ($id || $id == "0") {
                $query = $this->db->get_where($this->roleAclTable, array("role_id" => $id));
                if ($query->num_rows() == 1) {
                    $row = $query->row_array();
                    $moduleResource = unserialize($row["module_resource"]);
                    if ($moduleResource) {

                        foreach ($moduleResource as $id) {
                            $queryModule = $this->db->get_where($this->moduleTable, array("id" => $id, "is_active" => 1));
                            if ($queryModule->num_rows() == 1) {
                                $row = $queryModule->row_array();


                                $data = array();
                                $data["id"] = $row["id"];
                                $data["text"] = $row["label"];
                                $data["parent"] = "#";
                                $data["a_attr"]["class"] = "no_checkbox";

                                $queryModulePriv = $this->db->get_where($this->modulePrivTable, array("module_id" => $row["id"]));
                                if ($queryModulePriv->num_rows() == 1) {
                                    $rowModulePriv = $queryModulePriv->row_array();
                                    $_moduleResource = unserialize($rowModulePriv["module_resource"]);
                                    if ($_moduleResource) {
                                        $arrData[] = $data;
                                        foreach ($_moduleResource as $idx) {
                                            $queryList = $this->db->get_where($this->aclTable, array("id" => $idx, "is_active" => 1));
                                            if ($queryList->num_rows() == 1) {
                                                $rowx = $queryList->row_array();
                                                $datax = array();
                                                $tempItemIdx = $row["id"]."-".$rowx["id"];
                                                $tempStatex = (is_array($roleIds) && in_array($tempItemIdx, $roleIds, true))? true: false;
                                                    
                                                $datax["id"] = $tempItemIdx;
                                                $datax["text"] = $rowx["label"];
                                                $datax["parent"] = $row["id"];
                                                $datax["state"]["selected"] = $tempStatex;
                                                $arrData[] = $datax;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $arrData;
        }

        function accessControlJsonTree($search){
            $arrData = array();

            if ($search) {
                $this->db->where("is_active", 1);
                $this->db->order_by("sort", "ASC");
                $this->db->like("name", $search, "both");

                $query = $this->db->get($this->aclTable);

                foreach ($query->result() as $rs) {
                    $child = array();
                    $child["id"] = $rs->id;
                    $child["type"] = ($rs->parent_id) ? "child" : "root";
                    $child["parent"] = ($rs->parent_id) ? $rs->parent_id : "#";
                    $child["text"] = "<span class='m--font-boldest'>" . $rs->label . "</span>" . " <span class='m--font-boldest text-muted'>(" . $rs->name . ")</span>";
                    array_push($arrData, $child);
                }

                foreach ($arrData as $item) {
                    $flag = $this->checkParentExist($item["parent"]);
                    if ($flag) {
                        $parent = json_decode($this->getAclParent($item["parent"]), true);
                        $p_id = (int)$parent["id"];

                        $filter = array_filter($arrData, function ($value) use ($p_id) {
                            return (int)$value["id"] === $p_id;
                        });

                        if (count($filter) <= 0) {
                            array_push($arrData, $parent);
                        }
                    }
                }
            } else {
                $this->db->where("is_active", 1);
                $this->db->order_by("sort", "ASC");
                $query = $this->db->get($this->aclTable);

                if ($query->num_rows() > 0) {
                    foreach ($query->result_array() as $rs) {
                        $flag = false;
                        $data = array();
                        $data["id"] = $rs["id"];
                        $data["type"] = ($rs["parent_id"]) ? "child" : "root";
                        $data["parent"] = ($rs["parent_id"]) ? $rs["parent_id"] : "#";
                        $data["text"] = "<span class='m--font-boldest'>" . $rs["label"] . "</span>" . " <span class='m--font-boldest text-muted'>(" . $rs["name"] . ")</span>";

                        $parentId = intval($rs["parent_id"]);
                        if ($parentId !== 0) {
                            $flag = $this->checkParentExist($parentId);
                        } else {
                            $flag = true;
                        }
                        if ($flag) {
                            $arrData[] = $data;
                        }
                    }
                }
            }

            return $arrData;
        }

        private function getAclParent($parent_id)
        {
            $this->db->select("id, 'root' `type`, '#' parent, label text");
            $this->db->where("id", $parent_id);
            $data = $this->db->get($this->aclTable)->row();
            return json_encode($data);
        }

        function aclPrivilegeJson()
        {
            $arrData = array();
            $this->db->where("status", 1);
            $this->db->order_by("description", "ASC");
            $query = $this->db->get($this->privListTable);

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $rs) {
                    $data = array();
                    $data["id"] = $rs["id"];
                    $data["type"] = "root";

                    $text = "{$rs["label"]} - {$rs["description"]}";
                    $data["text"] = $text;

                    $arrData[] = $data;
                }
            }
            return $arrData;
        }

        function aclJson()
        {
            $arrData = array();
            $this->db->where("is_active", 1);
            $this->db->order_by("sort", "ASC");
            $query = $this->db->get($this->aclTable);

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $rs) {
                    $flag = false;
                    $data = array();
                    $data["id"] = $rs["id"];
                    $data["type"] = ($rs["parent_id"]) ? "child" : "root";
                    $data["parent"] = ($rs["parent_id"]) ? $rs["parent_id"] : "#";
                    $data["text"] = $rs["label"] . "<small class='jstree-identifier'>({$rs["identifier"]})</small>";

                    $parentId = intval($rs["parent_id"]);
                    if ($parentId !== 0) {
                        $flag = $this->checkParentExist($parentId);
                    } else {
                        $flag = true;
                    }
                    if ($flag) {
                        $arrData[] = $data;
                    }
                }
            }
            return $arrData;
        }

        function setAclJsonActions()
        {
            $post = $this->input->post();
            $resultset = array();

            if (isset($post["nodes"], $post["id"]) && ($post["nodes"] && $post["id"])) {
                $id = $post["id"];
                $nodes = json_decode($post["nodes"], true);
                if ($nodes) {
                    $ids = array();
                    foreach ($nodes as $node) {
                        $selected = $node["state"]["selected"];
                        if ($selected) {
                            $ids[] = $node["id"];
                        }
                    }

                    $aclExist = $this->aclResourceExist($id);
                    if ($aclExist) {
                        $where = array();
                        $where["id"] = $aclExist["id"];

                        $arrData = array();
                        $arrData["acl_resource"] = serialize($ids);
                        $update = $this->db->update($this->aclPrivilegeTable, $arrData, $where);
                        if ($update) {
                            $resultset["response"] = true;
                            $resultset["toastr_msg"] = "Access control privilege has been updated.";
                        } else {
                            $resultset["toastr_msg"] = "Data update failed!";
                            $resultset["response"] = false;
                        }
                    } else {
                        $arrData = array();
                        $arrData["acl_id"] = $id;
                        $arrData["acl_resource"] = serialize($ids);

                        $saved = $this->db->insert($this->aclPrivilegeTable, $arrData);
                        if ($saved) {
                            $resultset["toastr_msg"] = "Access control privilege has been saved.";
                            $resultset["response"] = true;
                        } else {
                            $resultset["toastr_msg"] = "Saving of data failed!";
                            $resultset["response"] = false;
                        }
                    }

                } else {
                    $resultset["toastr_msg"] = "No available nodes!";
                    $resultset["response"] = false;
                }
            } else {
                $resultset["toastr_msg"] = "No data found!";
                $resultset["response"] = false;
            }

            return $resultset;
        }

        protected function aclResourceExist($id = null)
        {
            if ($id) {
                $query = $this->db->get_where($this->aclPrivilegeTable, array("acl_id" => $id));
                if ($query->num_rows() == 1) {
                    $row = $query->row_array();
                    return $row;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getAclActions()
        {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"]) && $post["id"]) {
                $arrData = array();
                $this->db->from($this->aclTable);
                $this->db->where("id", $post["id"]);
                $query = $this->db->get();
                $row = ($query->num_rows() > 0) ? $query->row_array() : array();

                $arrData["row"] = $row;

                $html = $this->load->view("core/access_control/modal_content/list_action", $arrData, true);
                $jsonData = $this->aclPrivilegeJson();
                $aclResource = $this->getAclResource($post["id"]);
                $resultset["response"] = true;
                $resultset["html"] = $html;
                $resultset["data"] = $jsonData;
                $resultset["acl_id"] = $aclResource;
            } else {
                $resultset["response"] = false;
            }
            return $resultset;
        }

        protected function getAclResource($id = null)
        {
            $arrIds = array();
            if ($id) {
                $query = $this->db->get_where($this->aclPrivilegeTable, array("acl_id" => $id));
                if ($query->num_rows() == 1) {
                    $row = $query->row_array();
                    $aclResource = unserialize($row["acl_resource"]);
                    if ($aclResource) {
                        foreach ($aclResource as $id) {
                            $arrIds[] = intval($id);
                        }
                    }
                }
            }

            return $arrIds;
        }

        function checkParentExist($parentId = null)
        {
            if ($parentId) {
                $query = $this->db->get_where("access_control_list", array("is_active" => 1, "id" => $parentId));
                if ($query->num_rows() > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getAccessControlList()
        {
            $post = $this->input->post();
            if ($post) {
                $columns = array("id", "name", "label", "url", "identifier", "is_active");
                $dir = $post["order"][0]["dir"];
                $order = $columns[$post["order"][0]["column"]];
                $draw = (isset($post['draw']) && $post['draw']) ? $post['draw'] : 0;
                $start = (isset($post["start"]) && $post["start"]) ? $post["start"] : 0;
                $limit = (isset($post["length"]) && $post["length"]) ? $post["length"] : 0;
                $searchValue = (isset($post["search"]["value"]) && $post["search"]["value"]) ? $post["search"]["value"] : "";
                $dtAccessControl = $this->dt_model->dataTable();
                $dtAccessControl->setTable($this->aclTable);
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
                        $nestedData['name'] = $pst->name;
                        $nestedData['label'] = $pst->label;
                        $nestedData['url'] = $pst->url;
                        $nestedData['identifier'] = $pst->identifier;
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

        function getJsonAcl()
        {
            $post = $this->input->post();
            $result = array();
            if (isset($post["nodes"]) && $post["nodes"]) {
                $jsonData = json_decode($post["nodes"], true);
                $arrData = array();

                $sort = array();
                $sortParent = 1;
                $sortChild = 1;

                foreach ($jsonData as $key => $value) {
                    $where = array();
                    $where["id"] = intval($value["id"]);

                    $data = array();
                    $data["parent_id"] = intval($value["parent"]);
                    $data["sort"] = (intval($value["parent"]) == 0) ? intval($sortParent) : intval($sortChild);
                    $this->db->update($this->aclTable, $data, $where);

                    if (intval($value["parent"]) == 0) {
                        $sortChild = 1;
                        $sortParent++;
                    } else {
                        $sortChild++;
                    }
                }
                $result["response"] = true;
                $result["toastr_msg"] = "Access control list update successful.";
            } else {
                $result["response"] = false;
                $result["toastr_msg"] = "Error, no data found!";
            }

            return $result;
        }

        function getAccessControlUpdate()
        {
            $post = $this->input->post();
            $resultset = array();
            if (isset($post["id"]) && $post["id"]) {
                $query = $this->db->get_where($this->aclTable, $post);
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

        function getAclData($includes = array(), $isActive = 0, $parentId = 0, $sortOrder = "ASC")
        {
            $filter = ($includes) ? implode(",", $includes) : "*";
            $where = array();
            $where["parent_id"] = $parentId;
            $where["is_active"] = $isActive;

            $this->db->select($filter);
            $this->db->from($this->aclTable);
            $this->db->where($where);
            $this->db->order_by("sort", $sortOrder);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                return $query->result_array();
            } else {
                return false;
            }
        }

        function getAccessControlMenu($includes = array(), $isActive = 0, $sort = "ASC")
        {
            $arrData = array();
            $parent = $this->getAclData($includes, $isActive, 0, $sort);
            if ($parent) {
                foreach ($parent as $pRs) {
                    $children = $this->getAclData($includes, $isActive, $pRs["id"], $sort);
                    if ($children) {
                        $pRs["children"] = $children;
                    } else {
                        $pRs["children"] = array();
                    }
                    $arrData[] = $pRs;
                }
            }

            return $arrData;
        }

        private function checkAclName($data = array())
        {
            if ($data) {
                $query = $this->db->get_where($this->aclTable, array("name" => $data["name"]));
                if ($query->num_rows() == 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        private function generateCode($length = 13)
        {
            $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $genCode = substr(str_shuffle($str), 0, $length);
            return $genCode;
        }

        private function getAclName($data = array())
        {
            if (isset($data["name"]) && $data["name"]) {
                $data = $data["name"];
                $new_data = str_replace("'", "", $data);
                $new_data = preg_replace('/[^\p{L}\p{N}]/u', '_', $new_data);
                return strtolower($new_data);
            } else {
                return false;
            }
        }
    }