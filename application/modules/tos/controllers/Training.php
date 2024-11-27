<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Training extends MY_Controller {
	public function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("tos");
        $this->authenticate->doRedirect();

        $this->core_layout->setPrivilegeName("tos");
        $this->load->model("training_m","training");
        $this->load->helper('download');
	}
    
    public function index(){
        $this->core_layout->addJs("js/tos/amschart/amschart_core.js", true);
        $this->core_layout->addJs("js/tos/amschart/amschart_theme_animated.js", true);
        $this->core_layout->addJs("js/tos/amschart/amschart_chart.js", true);
        $this->core_layout->addJs("js/tos/dashboard.js", true);

		$this->load->view('core/templates/header');
        $this->load->view('tos/index');
        $this->load->view('core/templates/footer');
    }
   
    public function category(){
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addJs("js/tos/treeview/treeview.js", true);
        $this->core_layout->addJs("js/tos/category.js", true);

		$this->load->view('core/templates/header');
        $this->load->view('tos/category');
        $this->load->view('core/templates/footer');
    }
    
    function category_masterfile($id){
        $data =  $this->training->categoryMasterfile($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_category(){
        $data =  $this->training->addCategory();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_category($id){
        $data =  $this->training->deleteCategory($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_category($id){
        $data =  $this->training->editCategory($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_category($id){
        $data =  $this->training->updateCategory($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function topic(){
        $this->core_layout->addJs("js/tos/topic.js", true);

		$this->load->view('core/templates/header');
        $this->load->view('tos/topic');
        $this->load->view('core/templates/footer');
    }

    public function topic_masterfile($id){
        $data =  $this->training->topicMasterfile($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function upload_file(){
        $data =  $this->training->uploadFile();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_topic_detail($id){
        $data =  $this->training->addTopic($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function delete_topic($id){
        $data =  $this->training->deleteTopic($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_topic($id){
        $data =  $this->training->editTopic($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_topic($id){
        $data =  $this->training->updateTopic($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_file($id){
        $data =  $this->training->updateFile($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function view_topic($id){
        $data =  $this->training->viewTopic($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function treeview(){
        $data =  $this->training->treeview();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function add_file($name){
        $data =  $this->training->addFile($name);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function add_upload_file(){
        $data =  $this->training->addUploadFile();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
     
    function delete_file($id){
        $data =  $this->training->deleteFile($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function edit_file($id){
        $data =  $this->training->editFile($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_details($id){
        $data =  $this->training->getDetails($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_cat($id){
        $data =  $this->training->getCat($id);
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function chart_data(){
        $data =  $this->training->chartData();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
}