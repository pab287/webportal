<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @OA\Info(
 *     title="Hydra payment API",
 *     version="1.0.0"
 * )
 */
class Hydracore extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("hydracoremodel","hydracore");
    }


    public function getAccountsCollection(){
        $data = $this->hydracore->getAccountsCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function getAccountById(){
        $data = $this->hydracore->getAccountById();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
}

?>
