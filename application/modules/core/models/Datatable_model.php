<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Datatable_model extends CI_Model{
	private $table, $where_in, $group_by;
	private $whereInField, $whereNotInField, $orWhereInField, $orWhereNotInField;
	private $joinTables = array();
	private $fields = array();
	private $whereParameters = array();
	private $whereOrParameters = array();
	private $whereInParameters = array();
	private $orWhereInParameters = array();
	private $whereNotInParameters = array();
	private $orWhereNotInParameters = array();
	private $likeParameters = null;

	function __construct(){
		parent::__construct();
		$this->where_in  = "id";
	}

	function dataTable(){
		return clone $this;
	}

	function setTable($table=null){
		if($table){
			$this->table = $table;
			return $this;
		}else{
			return false;
		}
	}

	function setLike($field, $value, $wildcard_option) {
	    $this->likeParameters = array($field, $value, $wildcard_option);
    }

	function setTableAlias($table = null){
		if(!$this->table) return false;
		if($table){
			$this->table = "{$this->table} as {$table}";
			return $this;
		}else{
			return false;
		}
	}
	function setJoinTable($tableArray=array()){
		if(!$this->table) return false;
		if(isset($tableArray["table"], $tableArray["fields"]) && ($tableArray["table"] && $tableArray["fields"])){
			$arrJoinTable = array();
			$table = $tableArray["table"];
			$fields = $tableArray["fields"];
			$field_loc = $tableArray["field_loc"];

			if(isset($table) && $table){
				foreach($table as $key => $value){
					$arrJoinTable[] = "{$key} as {$value}";
				}
			}

			if(isset($tableArray["joint_table"]) && $tableArray["joint_table"]){
				$joint_table = $tableArray["joint_table"];
				if(isset($joint_table) && $joint_table){
					foreach($joint_table as $key => $value){
						$arrJoinTable[] = $value;
					}
				}
			}

			if($arrJoinTable){
				$joinedTable = array();
				for($i=0; $i<count($arrJoinTable); $i++):
					if($fields[$i]){
						$joinedTable[$i]["table"] = $arrJoinTable[$i];
						$joinedTable[$i]["field"] = $fields[$i];
						$joinedTable[$i]["field_loc"] = $field_loc[$i];
					}
				endfor;

				if($joinedTable){
					$this->joinTables = $joinedTable;
					return $this;
				}
			}
		}else{
			return false;
		}
	}

	function getJoinedTable(){
		if(!$this->joinTables) return false;
		return $this->joinTables;
	}

	function setWhereParameters($where=array()){
		if(!$this->table) return false;
		if($where){
			$this->whereParameters = $where;
			return $this;
		}
	}

    function setOrWhereParameters($where=array()){
		if(!$this->table) return false;
        if($where){
			$this->whereOrParameters = $where;
            return $this;
        }
    }
	
	function setWhereInParameters($field=null, $where=array()){
		if(!$this->table) return false;
		if($field && $where){
			$this->whereInField = $field;
			$this->whereInParameters = $where;
			return $this;
		}
	}

	function setWhereNotInParameters($field=null, $where=array()){
		if(!$this->table) return false;
		if($field && $where){
			$this->whereNotInField = $field;
			$this->whereNotInParameters = $where;
			return $this;
		}
	}

	function setOrWhereInParameters($field=null, $where=array()){
		if(!$this->table) return false;
		if($field && $where){
			$this->orWhereInField = $field;
			$this->orWhereInParameters = $where;
			return $this;
		}
	}

	function setOrWhereNotInParameters($field=null, $where=array()){
		if(!$this->table) return false;
		if($field && $where){
			$this->orWhereNotInField = $field;
			$this->orWhereNotInParameters = $where;
			return $this;
		}
	}

	function setWhereInField($field="id"){
		if(!$this->table) return false;
		if($field){
			$this->where_in = $field;
			return $this;
		}else{
			return false;
		}
	}

	function setGroupByFields($fields=array()){
		if(!$this->table) return false;
		if($fields){
			$this->group_by = implode(",", $fields);
			return $this;
		}else{
			return false;
		}
	}

	function getWhereInField(){
		if(!$this->table) return false;
		return $this->where_in;
	}

	function getGroupByFields(){
		if(!$this->table) return false;
		return $this->group_by;
	}

	function getCollection(){
		if(!$this->table) return false;

		if(isset($this->fields) && $this->fields){
			$selectFields = implode(",", $this->fields);
			$this->db->select($selectFields);
		}

		$this->db->from($this->table);

		if(isset($this->joinTables) && $this->joinTables){
			foreach($this->joinTables as $joined){
				if($joined["field_loc"]){ $this->db->join($joined["table"], $joined["field"], $joined["field_loc"]); }
				else{ $this->db->join($joined["table"], $joined["field"]); }
			}
		}

		if(isset($this->whereParameters) && $this->whereParameters){ $this->db->where($this->whereParameters); }
		if(isset($this->whereOrParameters) && $this->whereOrParameters){ $this->db->or_where($this->whereOrParameters); }
		if(isset($this->whereInParameters, $this->whereInField) && $this->whereInParameters && $this->whereInField){
			$this->db->where("UPPER($this->whereNotInField) IN ('".implode("','", array_map('strtoupper', $this->whereNotInParameters))."')");
		}

		if(isset($this->orWhereInParameters, $this->orWhereInField) && $this->orWhereInParameters && $this->orWhereInField){
			$this->db->or_where_in($this->orWhereInField, $this->orWhereInParameters);
		}

		if(isset($this->whereNotInParameters, $this->whereNotInField) && $this->whereNotInParameters && $this->whereNotInField){
			$this->db->where("UPPER($this->whereNotInField) NOT IN ('".implode("','", array_map('strtoupper', $this->whereNotInParameters))."')");
		}

		if(isset($this->orWhereNotInParameters, $this->orWhereNotInField) && $this->orWhereNotInParameters && $this->orWhereNotInField){
			$this->db->or_where_not_in($this->orWhereNotInField, $this->orWhereNotInParameters);
		}

		if(isset($this->group_by) && $this->group_by){
			$this->db->group_by($this->group_by);
		}

		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return false;
		}
	}
	function getTable(){
		if(!$this->table) return false;

		return $this->table;
	}
	function setParameterFields($fields = array()){
		if(!$this->table) return false;

		if($fields){ $this->fields = $fields; return $this; }
		else{ return false; }
	}
	
	function getParameterFields(){
		if(!$this->table) return false;

		return $this->fields;
	}
	function getAllFields(){
		if(!$this->table) return false;
		$fields = $this->db->list_fields($this->table);
		if($fields){ return $fields; }
		else{ return false; }
	}

	function searchParameterFields($search=null){
		if(!$this->table) return false;
		$fields = ($this->fields) ? $this->fields: $this->getAllFields();
		if($fields){
			$this->db->select($this->where_in);
			if(isset($this->joinTables) && $this->joinTables){
				foreach($this->joinTables as $joined){
					if($joined["field_loc"]){ $this->db->join($joined["table"], $joined["field"], $joined["field_loc"]); }
					else{ $this->db->join($joined["table"], $joined["field"]); }
				}
			}

			foreach($fields as $index => $value){
				$tempField = explode(" as ", $value);
				if(count($tempField) == 2){
					$value = trim($tempField[0]);
				}

				if($index == 0){ $this->db->like($value, $search); }
				else{ $this->db->or_like($value, $search); }
			}

			if(isset($this->whereParameters) && $this->whereParameters){ $this->db->where($this->whereParameters); }
			if(isset($this->whereOrParameters) && $this->whereOrParameters){ $this->db->or_where($this->whereOrParameters); }
			if(isset($this->whereInParameters, $this->whereInField) && $this->whereInParameters && $this->whereInField){
				$this->db->where_in($this->whereInField, $this->whereInParameters);
			}
			if(isset($this->orWhereInParameters, $this->orWhereInField) && $this->orWhereInParameters && $this->orWhereInField){
				$this->db->or_where_in($this->orWhereInField, $this->orWhereInParameters);
			}
			if(isset($this->whereNotInParameters, $this->whereNotInField) && $this->whereNotInParameters && $this->whereNotInField){
				$this->db->where_not_in($this->whereNotInField, $this->whereNotInParameters);
			}
			if(isset($this->orWhereNotInParameters, $this->orWhereNotInField) && $this->orWhereNotInParameters && $this->orWhereNotInField){
				$this->db->or_where_not_in($this->orWhereNotInField, $this->orWhereNotInParameters);
			}
			if(isset($this->group_by) && $this->group_by){ $this->db->group_by($this->group_by); }

			$query = $this->db->get($this->table);
			if($query->num_rows() > 0){	return $query->result_array(); }
			else{ return false; }
		}else{ return false; }
	}

	private function getDtSearchIds($arrData=array()){
		$arrIds = array();
		foreach($arrData as $data){ $arrIds[] = $data["id"]; }
		if($arrIds){ return $arrIds; }
		else{ return false; }
	}

	function dtAllPostsCount(){
		if(!$this->table) return false;
		if(isset($this->joinTables) && $this->joinTables){
			foreach($this->joinTables as $joined){
				if($joined["field_loc"]){ $this->db->join($joined["table"], $joined["field"], $joined["field_loc"]); }
				else{ $this->db->join($joined["table"], $joined["field"]); }
			}
		}
		if(isset($this->whereParameters) && $this->whereParameters){ $this->db->where($this->whereParameters);}
		if(isset($this->whereOrParameters) && $this->whereOrParameters){ $this->db->or_where($this->whereOrParameters); }
		if(isset($this->whereInParameters, $this->whereInField) && $this->whereInParameters && $this->whereInField){
			$this->db->where_in($this->whereInField, $this->whereInParameters);
		}
		if(isset($this->orWhereInParameters, $this->orWhereInField) && $this->orWhereInParameters && $this->orWhereInField){
			$this->db->or_where_in($this->orWhereInField, $this->orWhereInParameters);
		}
		if(isset($this->whereNotInParameters, $this->whereNotInField) && $this->whereNotInParameters && $this->whereNotInField){
			$this->db->where_not_in($this->whereNotInField, $this->whereNotInParameters);
		}
		if(isset($this->orWhereNotInParameters, $this->orWhereNotInField) && $this->orWhereNotInParameters && $this->orWhereNotInField){
			$this->db->or_where_not_in($this->orWhereNotInField, $this->orWhereNotInParameters);
		}
		if(isset($this->group_by) && $this->group_by){ $this->db->group_by($this->group_by); }

        $query = $this->db->get($this->table);
        return $query->num_rows();
	}
	
    function dtAllPosts($limit=5, $start=1, $col=null, $dir="ASC"){
		if(!$this->table) return false;

		if(isset($this->fields) && $this->fields){
			$selectFields = implode(",", $this->fields);
			$this->db->select($selectFields);
		}

		if(isset($this->joinTables) && $this->joinTables){
			foreach($this->joinTables as $joined){
				if($joined["field_loc"]){ $this->db->join($joined["table"], $joined["field"], $joined["field_loc"]); }
				else{ $this->db->join($joined["table"], $joined["field"]); }
			}
		}

		if(isset($this->whereParameters) && $this->whereParameters){ $this->db->where($this->whereParameters); }
        if(isset($this->whereOrParameters) && $this->whereOrParameters){ $this->db->or_where($this->whereOrParameters); }
		if(isset($this->whereInParameters, $this->whereInField) && $this->whereInParameters && $this->whereInField){
			$this->db->where_in($this->whereInField, $this->whereInParameters);
		}
		if(isset($this->orWhereInParameters, $this->orWhereInField) && $this->orWhereInParameters && $this->orWhereInField){
			$this->db->or_where_in($this->orWhereInField, $this->orWhereInParameters);
		}
		if(isset($this->whereNotInParameters, $this->whereNotInField) && $this->whereNotInParameters && $this->whereNotInField){
			$this->db->where_not_in($this->whereNotInField, $this->whereNotInParameters);
		}
		if(isset($this->orWhereNotInParameters, $this->orWhereNotInField) && $this->orWhereNotInParameters && $this->orWhereNotInField){
			$this->db->or_where_not_in($this->orWhereNotInField, $this->orWhereNotInParameters);
		}
		if($limit && $limit !== "-1"){ $this->db->limit($limit, $start); }
		if(isset($this->group_by) && $this->group_by){ $this->db->group_by($this->group_by); }
		if($col && $dir){ $this->db->order_by($col, $dir); }

		$query = $this->db->get($this->table);

		if($query->num_rows() > 0){ return $query->result();
		}else{ return false; }
	}
	
    function dtSearch($limit=5, $start=1, $search=null, $col=null, $dir="ASC"){
		if(!$this->table) return false;

		$ids = $this->searchParameterFields($search);
		if($ids){
			if(isset($this->fields) && $this->fields){
				$selectFields = implode(",", $this->fields);
				$this->db->select($selectFields);
			}
			$searchIds = $this->getDtSearchIds($ids);
			$whereIn = ($searchIds)? $searchIds: array();
			$this->db->where_in($this->where_in, $whereIn);

			if(isset($this->joinTables) && $this->joinTables){
				foreach($this->joinTables as $joined){
					if($joined["field_loc"]){ $this->db->join($joined["table"], $joined["field"], $joined["field_loc"]); }
				else{ $this->db->join($joined["table"], $joined["field"]); }
				}
			}

			if(isset($this->whereParameters) && $this->whereParameters){ $this->db->where($this->whereParameters); }
			if(isset($this->whereOrParameters) && $this->whereOrParameters){ $this->db->or_where($this->whereOrParameters); }
			if(isset($this->whereInParameters, $this->whereInField) && $this->whereInParameters && $this->whereInField){
				$this->db->where_in($this->whereInField, $this->whereInParameters);
			}
			if(isset($this->orWhereInParameters, $this->orWhereInField) && $this->orWhereInParameters && $this->orWhereInField){
				$this->db->or_where_in($this->orWhereInField, $this->orWhereInParameters);
			}
			if(isset($this->whereNotInParameters, $this->whereNotInField) && $this->whereNotInParameters && $this->whereNotInField){
				$this->db->where_not_in($this->whereNotInField, $this->whereNotInParameters);
			}
			if(isset($this->orWhereNotInParameters, $this->orWhereNotInField) && $this->orWhereNotInParameters && $this->orWhereNotInField){
				$this->db->or_where_not_in($this->orWhereNotInField, $this->orWhereNotInParameters);
			}

			if($limit && $limit !== "-1"){ $this->db->limit($limit, $start); }
			if(isset($this->group_by) && $this->group_by){ $this->db->group_by($this->group_by); }
			if($col && $dir){ $this->db->order_by($col,$dir); }

			$query = $this->db->get($this->table);
			if($query->num_rows() > 0){
				return $query->result();
            }else{ return false; }
		}else{ return false; }
    }

    function dtPostSearchCount($search=null){
		if(!$this->table) return false;

		$ids = $this->searchParameterFields($search);
		if($ids){
			$searchIds = $this->getDtSearchIds($ids);
			$whereIn = ($searchIds)? $searchIds: array();
			$this->db->where_in($this->where_in, $whereIn);
			if(isset($this->group_by) && $this->group_by){ $this->db->group_by($this->group_by); }

			$query = $this->db->get($this->table);
			if($query->num_rows() > 0){
				return $query->num_rows();
			}else{
				return intval("0");
			}
		}else{
			return intval("0");
		}
    }
}