<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Return_to_work extends MY_Controller {
public function __construct(){
                parent::__construct();
                $this->authenticate->setModuleAccess("eforms-rtw");
                $this->authenticate->doRedirect();
                $this->core_layout->setPrivilegeName("eforms_rtw_masterfile");
                $this->load->model("Return_work_m","return_work");
        }

	public function masterfile(){
                $this->core_layout->addJs("js/eforms/return_work/masterfile.js", true);
                $this->load->view('core/templates/header');
                $this->load->view('eforms/return_work/masterfile');
                $this->load->view('core/templates/footer');
        }

	public function add_new(){
                $this->core_layout->addJs("js/eforms/return_work/new_rtw_script.js", true);
                $this->load->view('core/templates/header');
                $this->load->view('eforms/return_work/new_rtw');
                $this->load->view('core/templates/footer');
        }

	public function view_return_to_work($id=null){
                if($id){
                        $arrData = array('id'=>$id);
                        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
                        $this->core_layout->addCss("plugins/lightbox/js/lightbox.css");
                        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
                        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
                        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
                        $this->core_layout->addJs("plugins/lightbox/js/lightbox.js");
                        $this->core_layout->addJs("js/eforms/return_work/view_rtw_script.js", true, $arrData);
                        $this->load->view('core/templates/header');
                        $this->load->view('eforms/return_work/view_rtw');
                        $this->load->view('core/templates/footer');
                }else{
                        redirect(site_url("eforms/return_to_work/masterfile"), "refresh");
                }
        }
        
        public function edit_return_to_work($id=null){
                if($id){
                        $arrData = array("id"=>$id);
                        $this->core_layout->addJs("js/eforms/return_work/edit_rtw_script.js", true, $arrData);
                        $this->load->view('core/templates/header');
                        $this->load->view('eforms/return_work/edit_rtw');
                        $this->load->view('core/templates/footer');
                }else{
                        redirect(site_url("eforms/return_to_work/masterfile"), "refresh");
                }
        }

        public function get_rtw_datatable_request(){
                $data = $this->return_work->getRtwDatatableRequest();
		$this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_temp_fields($type=0){
                $data = $this->return_work->getTempFields($type);
		$this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_employee_select2_data(){
                $data = $this->return_work->getEmployeeSelect2Data();
		$this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_employee_select2_company($id=null){
                $data = $this->return_work->getEmployeeSelect2Company($id);
		$this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function set_new_rtw(){
                $data = $this->return_work->setNewRtw();
		$this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function update_rtw_data(){
                $data = $this->return_work->updateRtwData();
		$this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function get_rtw_data($id=null){
                $data = $this->return_work->getRtwData($id);
		$this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function temp_upload_file(){
                $data = $this->return_work->tempUploadFile();
                $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }
        
        function get_current_uploaded_file(){
                $data = $this->return_work->getCurrentUploadedFile();
                $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function approve_rtw($id=null){
                $data = $this->return_work->approveRtw($id);
                $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function disapprove_rtw($id=null){
                $data = $this->return_work->disapproveRtw($id);
                $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function cancelled_rtw($id=null){
                $data = $this->return_work->cancelledRtw($id);
                $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function undo_rtw_approval($id=null){
                $data = $this->return_work->undoRtwApproval($id);
                $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function undo_rtw_disapproval($id=null){
                $data = $this->return_work->undoRtwDispproval($id);
                $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function print_approved_rtw($id=null){
                $data = $this->return_work->printApprovedRtw($id);
                $this->load->view("core/templates/printable/header");
                $this->load->view('eforms/return_work/form_content/printable', $data);
                $this->load->view("core/templates/printable/footer");
        }

        function advanced_search_request(){
                $data = $this->return_work->advancedSearchRequest();
                $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function export_event_log($export){
                $data = $this->return_work->exportData($export);
                $this->output
                    ->set_content_type('json')
                    ->set_output(json_encode($data));
        }
    
}