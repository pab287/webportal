<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Crudv2_model extends CI_Model {

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

			if(isset($where) && $where){
				foreach($where as $k=>$v)
				{
					$this->db->where($k,$v);
				}
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


		function get_datatables($bio)
		{

			$this->db->from('gcctimeutility.accounts_mobile');
			$this->db->where('biometric_id',$bio);
			$query = $this->db->get();
			return $query->result();
		}

function get_datatables2()
	{
		$this->db->from('gcctimeutility.images');
		$this->db->order_by('id','desc');
		$query = $this->db->get();
        return $query->result();
	}
	function get_datatables3()
	{

		$this->db->from('gcctimeutility.gps');
		$this->db->order_by('id','desc');
		$query = $this->db->get();
        return $query->result();
	}
	public function update2($where, $data)
	{
		$this->db->update('gcctimeutility.images', $data, $where);
		return $this->db->affected_rows();
	}
	public function update3($where, $data)
	{
		$this->db->update('gcctimeutility.gps', $data, $where);
		return $this->db->affected_rows();
	}
	public function update4($where, $data)
	{
		$this->db->update('gcctimeutility.accounts_mobile', $data, $where);
		return $this->db->affected_rows();
	}
	public function select()
	{
		$this->db->from('gcctimeutility.images');
		$this->db->order_by('id','desc');
		$where = "image is  NOT NULL";
		$this->db->where($where);
		$this->db->limit(1); 
		$query = $this->db->get();
		return $query->result();
	}
	public function get_by_id2($id)
	{
		$this->db->from('gcctimeutility.accounts_mobile');
		$this->db->where('id',$id);
		$query = $this->db->get();

		return $query->row();
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

		public function truncate($table = ""){

			$this->db->from($table); 

			$this->db->truncate(); 

		}

		public function delete($data = array(),$table)
		{
			if($data)
			{
				foreach($data as $k=>$v)
				{
					$this->db->where($k, $v);
				}
			}
			$query = $this->db->delete($table);

			return $query;
		}

		public function get_all_images_data(){
			$post = $this->input->post();
			$resultData = array();
			
			$search = (isset($post["search"]['value']) && $post["search"]['value'])? $post["search"]['value']: false;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 10;
			$offset = (isset($post["start"]) && $post["start"])? $post["start"]: 0;

            $filterFields = array("b.firstname", "b.lastname", "a.datetime");
			
			$this->db->order_by("id", "DESC");
			$this->db->select("a.*, CONCAT(b.firstname,' ',b.lastname) as name");
            $this->db->from("gcctimeutility.images a");
            $this->db->join("gccmaster.tblemployees b","b.biometricno = a.biometric_id","LEFT");

			if ($search) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
            }
			
			if($limit != -1){
				$this->db->limit($limit, $offset);
			}
			
			$this->db->order_by("a.datetime","DESC");

            $query = $this->db->get();

			foreach($query->result_array() as $imgData){
				
				$res = array();
				$res['name_with_address'] = '<strong style="color: #383838;">'.$imgData['name'].'</strong> <br/> '.date('F j, Y, g:i a', strtotime($imgData['datetime']));
				$res['name'] = $imgData['name'];
				$res['address'] = $imgData['address'];
				$res['datetime'] = date('F j, Y, g:i a', strtotime($imgData['datetime']));
				$res['image'] = $imgData['image'];
				$res['id'] = $imgData['id'];
				$res['latitude'] = $imgData['latitude'];
				$res['longitude'] = $imgData['longtitude'];
				$resultData[] = $res;
			}

			$rowCount = $this->get_all_images_data_count($search, $filterFields);
			return array("data"=>$resultData, "recordsTotal"=>$rowCount, "recordsFiltered"=>$rowCount);
		}

		function get_all_images_data_count($search, $filterFields){
			$this->db->select("a.id");
            $this->db->from("gcctimeutility.images a");
            $this->db->join("gccmaster.tblemployees b","b.biometricno = a.biometric_id");

			if ($search) {
                $this->db->group_start();
                foreach ($filterFields as $key => $field) {
                    if ($key == 0) {
                        $this->db->like($field, $search, "both");
                    } else {
                        $this->db->or_like($field, $search, "both");
                    }
                }
                $this->db->group_end();
            }

			$query = $this->db->get();
			return $query->num_rows();
		}

		public function get_location_image(){
			$post = $this->input->post();
			$id = $post['id'];
			$this->db->select("image");
			$this->db->where("id", $id);
			$img = $this->db->get("gcctimeutility.images");
			return $img->row_array();
		}

		public function get_location_coords(){
			$post = $this->input->post();
			$id = $post['id'];
			$this->db->select("latitude, longtitude");
			$this->db->where("id", $id);
			$coords = $this->db->get("gcctimeutility.images");
			return $coords->row_array();
		}

		private function personnel_name($bio_num){
			$this->db->select("concat(firstname,' ',lastname) as name");
			$this->db->where("biometricno", $bio_num);
			$name = $this->db->get("gccmaster.tblemployees");
			$data = $name->row_array();
			$lower_name = strtolower($data['name']);
			return ucfirst($lower_name);
		}

		private function search_personel($search){
			$filterFields = array("biomentric_id", "biometricno", "name");
			foreach ($filterFields as $key => $field) {
                if ($key == 0) {
                    $this->db->like($field, $search["value"], "both");
                } else {
                    $this->db->or_like($field, $search["value"], "both");
                }
				return $this->db->get("gcctimeutility.personnel")->result_array();
            }
		}

		private function all_count($database = ""){
			$data = $this->db->get($database);
			$num = $data->num_rows();
			return $num;
		}
	}
	
?>