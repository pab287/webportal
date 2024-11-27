<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rate_card extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->authenticate->setModuleAccess("pms");
        $this->authenticate->doRedirect();
        $this->load->model('Rate_card_m', 'rate_card');
    }

    public function index()
	{

	}

    function get_rate_card_update_history($checklist_priv_id) {
        echo json_encode($this->rate_card->getRateCardUpdateHistory($checklist_priv_id));
    }

    function get_current_checklist_rate($priv_rate_id) {
        echo json_encode($this->rate_card->getCurrentChecklistRate($priv_rate_id));
    }

    function revert_rate($id) {
        echo json_encode($this->rate_card->revertRate($id));
    }

}

/* End of file Controllername.php */