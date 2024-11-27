<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rate_category extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authenticate->setModuleAccess("pms");
        $this->authenticate->doRedirect();
        $this->load->model("Rate_category_m", "rate_category");
    }

    public function index()
    {
        $this->core_layout->setPrivilegeName("pms_rate_category_masterfile");
        $this->core_layout->addJs("js/pms/rate_category/script.js", TRUE);

        $this->load->view('core/templates/header');
        $this->load->view('pms/rate_category/index');
        $this->load->view('core/templates/footer');
    }

    public function get_rate_category()
    {
        echo json_encode($this->rate_category->getRateCategory());
    }

    public function archive_category($id)
    {
        echo json_encode($this->rate_category->archiveCategory($id));
    }

    function open_modal()
    {
        $data = array();
        $formData = $this->input->post('formData');
        $init_modal_data_function = $this->input->post('init_modal_data_function');
        $model = $this->input->post('model');

        $data['html'] = $this->utilities->openModal();

        if ($init_modal_data_function) {
            $data['info'] = $this->rate_category->$init_modal_data_function($formData);
        }

        echo json_encode($data);
    }

    function add_rate_category()
    {
        echo json_encode($this->rate_category->addRateCategory());
    }

    function edit_rate_category($id)
    {
        echo json_encode($this->rate_category->editRateCategory($id));
    }

    function search_tree()
    {
        $search = $this->input->post("key");
        $data = array("info" => $this->rate_category->getCategoryForTree($search));
        echo json_encode($data);
    }

    function arrange_category_tree() {
        echo json_encode($this->rate_category->arrangeCategoryTree());
    }
}

/* End of file Rate_category.php */