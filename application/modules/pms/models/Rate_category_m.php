<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rate_category_m extends CI_Model
{
    protected $user;
    protected $rateCategoryTable = "gccpms.sf_item_rates_category";

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->session->userdata("logged_in");
        $this->load->model("ams/Utilities_model", "utilities");
    }

    public function getRateCategory()
    {
        $resultSet = array();
        $table = $this->rateCategoryTable . " ch";
        $tableConfig = $this->input->post();
        $tableConfigStd = $this->utilities->parseFormDataToObject($tableConfig);
        $pageOptions = $this->utilities->getDatatablesConfigForPagination($tableConfigStd);

        $joinArr = array(
            array(
                "table" => $this->rateCategoryTable . " mn",
                "condition" => "mn.id = ch.parent_id",
                "option" => "LEFT"
            ),
        );

        $where = array("ch.is_active" => 1);
        $searchFields = "CONCAT(IFNULL(ch.category, ''), IFNULL(mn.category, ''))";

        $this->db->select("ch.*, mn.category parent_category");
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

    function archiveCategory($id)
    {
        $this->db->where("id", $id);
        $this->db->set("is_active", 0);
        if ($this->db->update($this->rateCategoryTable)) {
            $this->core_layout->insertArchiveLog($this->rateCategoryTable, $id);
            return true;
        }

        return false;
    }

    function addRateCategory()
    {
        $post = $this->input->post();
        $post["sort"] = $this->getSortOrder($this->rateCategoryTable, array("parent_id" => 0), "sort");
        $post["category"] = strtoupper($post["category"]);
        $post["created_by"] = $this->user["emp_id"];
        return $this->db->insert($this->rateCategoryTable, $post);
    }

    private function getSortOrder($table, $criteria = null, $field)
    {
        if ($criteria) {
            $this->db->where($criteria);
        }

        $this->db->select_max($field, "max");
        return intval($this->db->get($table)->row("max")) + 1;
    }

    function getCategoryDetail($form)
    {
        return $form;
    }

    function editRateCategory($id)
    {
        $post = $this->input->post();
        $post["category"] = strtoupper($post["category"]);
        $this->db->where("id", $id);
        return $this->db->update($this->rateCategoryTable, $post);
    }

    function getCategoryForTree($search = null)
    {
        $categories = array();
        $field = "ch.id, IF(ch.parent_id > 0, 'child', 'root') `type`, IF(ch.parent_id > 0, ch.parent_id, '#') 
                           `parent`, ch.category `text`";
        $this->db->select($field);
        $this->db->like("CONCAT(IFNULL(ch.category, ''), IFNULL(mn.category, ''))", $search, "both");
        $this->db->join($this->rateCategoryTable . " mn", "mn.id = ch.parent_id", "left");
        $this->db->order_by("ch.sort", "asc");
        $categories = $this->db->get(($this->rateCategoryTable . " ch"))->result();

        foreach ($categories as $category) {
            $parent = $this->getParentTree(($this->rateCategoryTable . " ch"), $field, array("id" => $category->parent));

            if (is_object($parent)) {
                $parent_id = (int)$parent->id;

                $filter = array_filter($categories, function ($value) use ($parent_id) {
                    return (int)$value->id === $parent_id;
                });

                if (count($filter) <= 0) {
                    array_push($categories, $parent);
                }
            }
        }

        return $categories;
    }

    private function getParentTree($table, $field, $criteria)
    {
        $this->db->select($field);
        $this->db->where($criteria);
        return $this->db->get($table)->row();
    }

    function arrangeCategoryTree()
    {
        $post = $this->input->post();
        $tree = $this->utilities->parseFormDataToObject($post["tree"]);


        $this->db->trans_begin();

        $parent_sort = 1;
        $child_sort = 1;
        foreach ($tree as $node) {
            $id = $node->id;
            $parent_id = intval($node->parent);
            $sort = $parent_id === 0 ? $parent_sort : $child_sort;

            $this->db->where("id", $id);
            $this->db->set("parent_id", $parent_id);
            $this->db->set("sort", $sort);
            $this->db->update($this->rateCategoryTable);

            if ($parent_id === 0) {
                $child_sort = 1;
                $parent_sort++;
            } else {
                $child_sort++;
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
}

/* End of file .php */