<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Masterfile_model extends CI_Model{
	protected $assetTable = "receiving";
	
	function __construct(){
		parent::__construct();
		$this->load->model("access_control_model", "acl_model");
		$this->load->model("datatable_model","dt_model");
    }
    
}