<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Gcceforms_to extends dbase{
    private $gcceforms_to_m;

    public function __construct(){
        $this->gcceforms_to_m = new Gcceforms_to_m();
    }

    public function getTOData(){
        echo $this->gcceforms_to_m->get_to_data();
    }

    public function getCollections(){
    	echo $this->gcceforms_to_m->get_collections();
    }
}

?>