<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once BASEPATH.'libraries/zklibrary.php';

class Zklib extends zklibrary {
	public function __construct($params=array()){
		if($params){
			return new ZKLibrary($params["ipaddress"], $params["port"]);			
		}else{
			return false;
		}
	}
}