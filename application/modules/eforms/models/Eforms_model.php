<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Eforms_model extends CI_Model {
    protected $eformsTable = "gcceforms";
	public function __construct(){
		parent::__construct();
    }
}