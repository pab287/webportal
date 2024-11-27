<?php
class Dbase{
private $host = hostname;
private $usern = username;
private $passw = password;
private $database = database;


protected function conn($dbase = ""){
    if($dbase == ""){
        return new PDO('mysql:host='.$this->host.';dbname='.$this->database, $this->usern, $this->passw);
    }else{
        return new PDO('mysql:host='.$this->host.';dbname='.$dbase, $this->usern, $this->passw);
    }
}

}
?>