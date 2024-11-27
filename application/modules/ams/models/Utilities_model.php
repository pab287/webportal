<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Utilities_model extends CI_Model {
    function __construct(){
		parent::__construct();
    }
    
    function parseFormDataToObject($data) {
        $_data = json_encode($data, true);
        return json_decode($_data);
    }

    function getDatatablesConfigForPagination($object) {
        $order_field_idx = property_exists($object, 'order') ? $object->order[0]->column : 0;
        $order_dir = property_exists($object, 'order') ? $object->order[0]->dir : 'ASC';
        $order_column = property_exists($object, 'columns') ? $object->columns[$order_field_idx]->data : null;

        $config = array();
        $config['length'] = property_exists($object, 'length') ? $object->length : 0;
        $config['start'] = property_exists($object, 'start') ? $object->start : 0;
        $config['search'] = property_exists($object, 'search') ? $object->search->value : null;
        $config['order_direction'] = $order_dir;
        $config['order_column'] = $order_column;

        return json_decode(json_encode($config, true));
    }

    function getTableCount($table, $criteria, $search, $joinArr = null, $escapeCriteria = false, $advSearch = null, $groupBy=null) {
        $totalRows = 0;
        if ($joinArr) {
            if ($joinArr) {
                foreach ($joinArr as $_join)
                    $this->db->join($_join["table"], $_join["condition"], $_join["option"]);
            }
        }

        if ($criteria) {
            if ($escapeCriteria) {
                $this->db->where($criteria, NULL, FALSE);
            } else {
                $this->db->where($criteria);
            }
        }

        if (isset($search) && $search !== "" && $search !== null) {
            $this->db->like($search["field"], $search["key"], $search["option"]);
        }

        if (!empty($advSearch)) {
            $this->db->like($advSearch, "both");
        }

        if($groupBy){
            $this->db->group_by($groupBy);
            $qTemp = $this->db->get($table);
            $totalRows = $qTemp->num_rows();
        }else{
            $totalRows = $this->db->count_all_results($table);
        }

        return $totalRows;
        /*** return $this->db->count_all_results(); ***/
    }

    /* dynamic opening of modal form */
    function openModal() {
        $formData = $this->input->post('formData'); // get data from request either json or string, its up to you how you handle the data in the function
        $path = $this->input->post("path"); // get the view path form request
        $function_name = $this->input->post("function_name"); // get the function name from request
        $model = $this->input->post("model");

        /* code below description:
            * using the $function_name variable php will try to look for the function with value inside
            * $function_name(e.g. get_asset_document_details) variable and return the data
            * */
        $get_data = null;

        if ($function_name) {
            if (isset($model)) {
                $this->load->model($model, "model");
                $get_data = $this->model->$function_name($formData);
            } else {
                $get_data = $this->$function_name($formData);
            }
        }

        return $this->load->view($path, $get_data, TRUE);
    }

    function passDataToDialog($data) {
        return $data;
    }
    /* end dynamic opening of modal form */
}