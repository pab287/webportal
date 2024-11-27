<?php
class Login extends Dbase{
    use Logs_maker;
    private $loginModel;

    public function __construct(){
        $this->loginModel = new Login_model();
        // echo fopen("./storage/logs/location/1140.log", "w");
    }

    public function __getServerTime(){
        echo date("h:i:s");
    }

    public function __changeTime(){
        $data = $this->loginModel->change_time();
        echo $data;
    }
    public function get_server_time(){
        echo date("h:i:s");
        var_dump(realpath('./'));
    }

    public function allData(){
        $loggin = $this->loginModel->getAllData();
        echo $loggin;
    }

    public function verify(){
        $ver = $this->loginModel->verifyingV2();
        echo $ver;
    }

    public function deviceVerify(){
        $ver = $this->loginModel->deviceVer();
        echo $ver;
    }

    public function logout(){
        $ver = $this->loginModel->logout();
        echo $ver;
    }

    public function getBio(){
        $ver = $this->loginModel->get_bio();
        echo $ver;
    }

    public function checkLoginStatus(){
        $ver = $this->loginModel->check_login_status();
        echo $ver;
    }

    public function testlog(){
        $ver = $this->loginModel->test();
        echo $ver;
       // $sample_obj = array(array("name" => "john"), array("name" => "mark"), array("name" => "sam"));
       // $this->template_content = $sample_obj;
       // $this->page = "sample";
       // $this->emp_id = 1123;
       // $this->template();
       // echo $this->responce;
    }

    public function serverTime(){
        $ver = $this->loginModel->server_time();
        echo $ver;
    }

    public function web_login(){
        $ver = $this->loginModel->verifyWeb();
        echo $ver;
    }

}
?>