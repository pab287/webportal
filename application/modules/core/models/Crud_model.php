<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Crud_Model extends CI_Model {

		public function __construct()
		{
			parent::__construct();
			$this->load->database();
		}

		public function load($where = array(), $table = "")
		{
			$this->db->select("*");
			$this->db->from($table);

			foreach($where as $k=>$v)
			{
				$this->db->where($k,$v);
			}

			$query = $this->db->get();
			
			return $query ? $query->row_array() : FALSE;
		}

		public function getCollection($where = array(), $table = "", $orderby = array(), $groupby = array(), $limit = NULL)
		{
			$this->db->select("*");
			$this->db->from($table);

			foreach($where as $k=>$v)
			{
				$this->db->where($k,$v);
			}

			foreach($orderby as $k=>$v){
				$this->db->order_by($k,$v);
			}

			foreach($groupby as $_groupby){
				$this->db->group_by($_groupby);
			}

			$this->db->limit($limit);

			$query = $this->db->get();

			return $query->num_rows() > 0 ? $query->result_array() : FALSE;
		}

		public function insert($data,$table)
		{

			$query = $this->db->insert($table,$data);

			return $query;

		}

		public function update($data, $where, $table)
		{

			$this->db->set($data);

			$this->db->where($where);

			$query = $this->db->update($table);

			return $query;

		}

		//borrowing global over due collection
		function getOverdueCollection()
		{

			$sql = $this->db->query("select * from borrowing_body right join borrowing on borrowing_body.borrowing_id = borrowing.id where borrowing.status != 'Cancelled' AND borrowing_body.is_returned = 0 AND DATE_FORMAT(borrowing_body.date_due,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d')");

			return $sql ? $sql : false;
		}

		//multi deminsional array
		function in_array_r($needle, $haystack, $strict = false) {
		    foreach ($haystack as $item) {
		        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
		            return true;
		        }
		    }

		    return false;
		}
	}
?>