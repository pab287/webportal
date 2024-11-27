<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Csf extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("eforms-borrowing");
            $this->core_layout->setPrivilegeName("eforms_borrowing");
            $this->authenticate->doRedirect();
           

            $this->load->model("Csf_model", "csf");
        }
        public function index()
        {

            $this->core_layout->addJs("js/csf/dashboard.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('csf/dashboard');
            $this->load->view('core/templates/footer');
        }
        public function items()
        {

            $this->core_layout->addJs("js/csf/item.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('csf/items');
            $this->load->view('core/templates/footer');
        }
    
   

        function get_item_collection()
        {
            $data = $this->csf->getItemCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        function get_category()
        {
            $data = $this->csf->getCategory();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        public function add_item()
        {

            $data = array(
                'name' => $this->input->post('name'),
                'category' => $this->input->post('category')
            );
            $insert = $this->csf->save_item($data);
            echo json_encode(array("status" => TRUE));
        }

        public function edit_item($id)
        {
            $data = $this->csf->edit_item($id);
            echo json_encode($data);
        }

        public function update_item()
        {

            $data = array(
                'name' => $this->input->post('name'),
                'category' => $this->input->post('category')
            );
            $this->csf->update_item(array('id' => $this->input->post('id')), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_item($id)
        {
            $this->csf->delete_item($id);
            echo json_encode(array("status" => TRUE));
        }

        public function category()
        {

            $this->core_layout->addJs("js/csf/category.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('csf/category');
            $this->load->view('core/templates/footer');
        }
    
   

        function get_category_collection()
        {
            $data = $this->csf->getCategoryCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function add_category()
        {

            $data = array(
                'name' => $this->input->post('name')
                
            );
            $insert = $this->csf->save_category($data);
            echo json_encode(array("status" => TRUE));
        }

        public function edit_category($id)
        {
            $data = $this->csf->edit_category($id);
            echo json_encode($data);
        }

        public function update_category()
        {

            $data = array(
                'name' => $this->input->post('name')
            );
            $this->csf->update_category(array('id' => $this->input->post('id')), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_category($id)
        {
            $this->csf->delete_category($id);
            echo json_encode(array("status" => TRUE));
        }
        public function project()
        {

            $this->core_layout->addJs("js/csf/project.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('csf/project');
            $this->load->view('core/templates/footer');
        }
    
   

        function get_project_collection()
        {
            $data = $this->csf->getProjectCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
}