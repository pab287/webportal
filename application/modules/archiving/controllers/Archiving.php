<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Archiving extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("das");
            $this->authenticate->doRedirect();

            $this->load->model("Document_model", "document");
        }

        public function index()
        {
            $this->core_layout->setPrivilegeName("das_archiving_dashboard");
            $this->core_layout->addJs("js/archiving/dashboard.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('archiving/dashboard');
            $this->load->view('core/templates/footer');
        }

        public function tags()
        {
            $this->core_layout->setPrivilegeName("das_archiving_tag");
            $this->core_layout->addJs("js/archiving/tags.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('archiving/tags');
            $this->load->view('core/templates/footer');
        }

        function get_tag_collection()
        {
            $data = $this->document->getTagCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function add_tag()
        {

            $data = array(
                'tag' => $this->input->post('tag')
            );
            $insert = $this->document->save_tag($data);
            echo json_encode(array("status" => TRUE));
        }

        public function edit_tag($id)
        {
            $data = $this->document->edit_tag($id);
            echo json_encode($data);
        }

        public function update_tag()
        {

            $data = array(
                'tag' => $this->input->post('tag')
            );
            $this->document->update_tag(array('id' => $this->input->post('id')), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_tag($id)
        {
            $this->document->delete_tag($id);
            echo json_encode(array("status" => TRUE));
        }

        public function classification()
        {
            $this->core_layout->setPrivilegeName("das_archiving_classification");
            $this->core_layout->addJs("js/archiving/classification.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('archiving/classification');
            $this->load->view('core/templates/footer');
        }

        function get_classification_collection()
        {
            $data = $this->document->getClassificationCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function add_classification()
        {

            $data = array(
                'description' => $this->input->post('description'),
                'type' => $this->input->post('type')
            );
            $insert = $this->document->save_classification($data);
            echo json_encode(array("status" => TRUE));
        }

        public function edit_classification($id)
        {
            $data = $this->document->edit_classification($id);
            echo json_encode($data);
        }

        public function update_classification()
        {

            $data = array(
                'description' => $this->input->post('description'),
                'type' => $this->input->post('type')
            );
            $this->document->update_classification(array('id' => $this->input->post('id')), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_classification($id)
        {
            $this->document->delete_classification($id);
            echo json_encode(array("status" => TRUE));
        }

        public function project()
        {
            $this->core_layout->setPrivilegeName("das_archiving_project");
            $this->core_layout->addJs("js/archiving/project.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('archiving/project');
            $this->load->view('core/templates/footer');
        }

        function get_project_collection()
        {
            $data = $this->document->getProjectCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function add_project()
        {

            $data = array(
                'name' => $this->input->post('name')
            );
            $insert = $this->document->save_project($data);
            echo json_encode(array("status" => TRUE));
        }

        public function edit_project($id)
        {
            $data = $this->document->edit_project($id);
            echo json_encode($data);
        }

        public function update_project()
        {

            $data = array(
                'name' => $this->input->post('name')
            );
            $this->document->update_project(array('id' => $this->input->post('id')), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_project($id)
        {
            $this->document->delete_project($id);
            echo json_encode(array("status" => TRUE));
        }

        public function document()
        {
            $this->core_layout->setPrivilegeName("das_general_documents");
            $this->core_layout->addJs("js/archiving/document.js", true);

            $data = array("title" => "General Documents Masterfile");
            $this->load->view('core/templates/header');
            $this->load->view('archiving/document', $data, FALSE);
            $this->load->view('core/templates/footer');
        }

        public function file()
        {
            $this->core_layout->setPrivilegeName("das_archiving_masterfile");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("js/archiving/file.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('archiving/file');
            $this->load->view('core/templates/footer');
        }

        public function memo()
        {
            $this->core_layout->setPrivilegeName("das_archiving_memorandum");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("js/archiving/memo.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('archiving/memo');
            $this->load->view('core/templates/footer');
        }

        public function favorite()
        {
            $this->core_layout->setPrivilegeName("das_archiving_favorite");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("js/archiving/favorite.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('archiving/favorite');
            $this->load->view('core/templates/footer');
        }

        public function supersede()
        {
            $this->core_layout->setPrivilegeName("das_archiving_supersede");
            $this->core_layout->addCss("global/plugins/uploadui/css/blueimp/blueimp-gallery.min.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload.css", true);
            $this->core_layout->addCss("global/plugins/uploadui/css/jquery.fileupload-ui.css", true);
            $this->core_layout->addJs("js/archiving/supersede.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('archiving/supersede');
            $this->load->view('core/templates/footer');
        }

        public function scanned()
        {
            $this->core_layout->setPrivilegeName("das_scanned_checks");
            $this->core_layout->addJs("js/archiving/scanned.js", true);

            $data = array("title" => "Scanned Check Masterfile");
            $this->load->view('core/templates/header');
            $this->load->view('archiving/document', $data, FALSE);
            $this->load->view('core/templates/footer');
        }

        public function real()
        {
            $this->core_layout->setPrivilegeName("das_real_property");
            $this->core_layout->addJs("js/archiving/real.js", true);
            
            $data = array("title" => "Real Property Masterfile");
            $this->load->view('core/templates/header');
            $this->load->view('archiving/document', $data, FALSE);
            $this->load->view('core/templates/footer');
        }
        
        public function iso()
        {
            $this->core_layout->setPrivilegeName("das_archiving_masterfile");
            $this->core_layout->addJs("js/archiving/iso.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('archiving/document');
            $this->load->view('core/templates/footer');
        }

        public function aggregate()
        {
            $this->core_layout->setPrivilegeName("das_archiving_masterfile");
            $this->core_layout->addJs("js/archiving/aggregate.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('archiving/document');
            $this->load->view('core/templates/footer');
        }

        function get_document_collection($type)
        {
            $data = $this->document->getDocumentCollection($type);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_file_collection($id)
        {
            $data = $this->document->getFileCollection($id);
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function add_document($type)
        {
            $user_id = $this->core_layout->getUserId();
            date_default_timezone_set('Asia/Singapore');
            $data = array(
                'reference' => $this->input->post('reference'),
                'classification_id' => $this->input->post('classification_id'),
                'department_id' => $this->input->post('department_id'),
                'description' => $this->input->post('description'),
                'type' => $type,
                'created_by' => $user_id,
                'created_dt' => date('Y/m/d H:I:s'),
                'document_dt' => $this->input->post('document_dt'),);

            $insert = $this->document->save_document($data);
            mkdir("uploads/module/archiving/" . $this->input->post('reference'), 0755, TRUE);
            $file = 'uploads/module/archiving/index.php';
            $newfile = 'uploads/module/archiving/' . $this->input->post('reference') . '/index.php';

            if (!copy($file, $newfile)) {
            } else {
            }
            $file = 'uploads/module/archiving/UploadHandler.php';
            $newfile = 'uploads/module/archiving/' . $this->input->post('reference') . '/UploadHandler.php';
            if (!copy($file, $newfile)) {
            } else {
            }
            mkdir("uploads/module/archiving/" . $this->input->post('reference') . '/files', 0755, TRUE);
            echo json_encode(array("status" => TRUE));
        }

        public function edit_document($id)
        {
            $data = $this->document->edit_document($id);
            echo json_encode($data);
        }

        public function update_document()
        {
            $user_id = $this->core_layout->getUserId();
            date_default_timezone_set('Asia/Singapore');
            $data = array(

                'classification_id' => $this->input->post('classification_id'),
                'department_id' => $this->input->post('department_id'),
                'description' => $this->input->post('description'),
                'modify_by' => $user_id,
                'modify_dt' => date('Y/m/d H:I:s'),
                'document_dt' => $this->input->post('document_dt'),);
            $this->document->update_document(array('id' => $this->input->post('id')), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function move_document()
        {

            $data = array(
                'type' => $this->input->post('type'));
            $this->document->update_document(array('id' => $this->input->post('move_id')), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_document($id)
        {
            $this->document->delete_document($id);
            echo json_encode(array("status" => TRUE));
        }

        function get_classification()
        {
            $data = $this->document->getClassification();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_department()
        {
            $data = $this->document->getDepartment();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_tag()
        {
            $data = $this->document->getTag();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function add_file($id)
        {
            $filename = $this->input->post('doc_filename');
            $filename = substr($filename, 1);
            $temp2 = null;
            $temp3 = null;
            $temp6 = null;
            $temp4 = null;
            $temp7 = null;
            $temp8 = null;
            $data1 = array();

            $temp = $id;
            $t1 = explode("*", $filename);
            for ($i = 0; $i < count($t1); $i++) {


                $temp2 = substr($t1[$i], -5, 1);
                $temp3 = substr($t1[$i], -6, 1);
                //echo json_encode(array("test" => $temp2,"test2" => $temp3));
                if ($temp2 == ")") {
                    $temp4 = substr_replace($t1[$i], '', -7, -5);
                    $temp4 = substr_replace($temp4, '', -6, -5);
                    $temp4 = substr_replace($temp4, '', -5, -4);
                    $temp4 = str_replace(' ', '', $temp4);

                }
                if ($temp3 == ")") {
                    $temp4 = substr_replace($t1[$i], '', -8, -6);
                    $temp4 = substr_replace($temp4, '', -7, -6);
                    $temp4 = substr_replace($temp4, '', -6, -5);
                    $temp4 = str_replace(' ', '', $temp4);
                }
                //echo json_encode(array("test" => $temp4));
                $list = $this->document->get_datatables($temp);
                foreach ($list as $myList) {

                    $temp6 = $myList->filename;
                    $temp8 = $myList->filename;
                    $temp8 = str_replace(' ', '', $temp8);
                    $temp2 = substr($temp6, -5, 1);
                    $temp3 = substr($temp6, -6, 1);
                    //echo json_encode(array("test" => $temp2,"test2" => $temp3));
                    if ($temp2 == ")") {
                        $temp7 = substr_replace($temp6, '', -7, -5);
                        $temp7 = substr_replace($temp7, '', -6, -5);
                        $temp7 = substr_replace($temp7, '', -5, -4);
                        $temp7 = str_replace(' ', '', $temp7);

                    }
                    if ($temp3 == ")") {
                        $temp7 = substr_replace($temp6, '', -8, -6);
                        $temp7 = substr_replace($temp7, '', -7, -6);
                        $temp7 = substr_replace($temp7, '', -6, -5);
                        $temp7 = str_replace(' ', '', $temp7);
                    }
                    //echo json_encode(array("test" => $temp7));
                    if ($temp8 == $temp4 || $temp7 == $temp4) {
                        $temp5 = $myList->id;
                        $y = $myList->description;
                        $z = $myList->tag1;
                        $this->document->delete_file($temp5);
                    }
                }
            }
            $user_id = $this->core_layout->getUserId();
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            for ($i = 0; $i < count($t1); $i++) {
                $data = array(
                    'document_id' => $id,
                    'description' => $this->input->post('description'),
                    'tag1' => $this->input->post('tag_temp'),
                    'filename' => $t1[$i],
                    'created_by' => $user_id,
                    'created_dt' => $date,
                );
                $insert = $this->document->save_file($data);
            }
            echo json_encode(array("status" => $data));
        }

        public function edit_file($id)
        {
            $data = $this->document->edit_file($id);
            echo json_encode($data);
        }

        public function update_file($id)
        {
            $filename = $this->input->post('doc_filename');
            $filename = substr($filename, 1);
            $temp2 = null;
            $temp3 = null;
            $temp6 = null;
            $temp4 = null;
            $temp7 = null;
            $temp8 = null;
            $data1 = array();

            $temp = $id;
            $t1 = explode("*", $filename);
            for ($i = 0; $i < count($t1); $i++) {


                $temp2 = substr($t1[$i], -5, 1);
                $temp3 = substr($t1[$i], -6, 1);
                //echo json_encode(array("test" => $temp2,"test2" => $temp3));
                if ($temp2 == ")") {
                    $temp4 = substr_replace($t1[$i], '', -7, -5);
                    $temp4 = substr_replace($temp4, '', -6, -5);
                    $temp4 = substr_replace($temp4, '', -5, -4);
                    $temp4 = str_replace(' ', '', $temp4);

                }
                if ($temp3 == ")") {
                    $temp4 = substr_replace($t1[$i], '', -8, -6);
                    $temp4 = substr_replace($temp4, '', -7, -6);
                    $temp4 = substr_replace($temp4, '', -6, -5);
                    $temp4 = str_replace(' ', '', $temp4);
                }
                //echo json_encode(array("test" => $temp4));
                $list = $this->document->get_datatables($temp);
                foreach ($list as $myList) {

                    $temp6 = $myList->filename;
                    $temp8 = $myList->filename;
                    $temp8 = str_replace(' ', '', $temp8);
                    $temp2 = substr($temp6, -5, 1);
                    $temp3 = substr($temp6, -6, 1);
                    //echo json_encode(array("test" => $temp2,"test2" => $temp3));
                    if ($temp2 == ")") {
                        $temp7 = substr_replace($temp6, '', -7, -5);
                        $temp7 = substr_replace($temp7, '', -6, -5);
                        $temp7 = substr_replace($temp7, '', -5, -4);
                        $temp7 = str_replace(' ', '', $temp7);

                    }
                    if ($temp3 == ")") {
                        $temp7 = substr_replace($temp6, '', -8, -6);
                        $temp7 = substr_replace($temp7, '', -7, -6);
                        $temp7 = substr_replace($temp7, '', -6, -5);
                        $temp7 = str_replace(' ', '', $temp7);
                    }
                    //echo json_encode(array("test" => $temp7));
                    if ($temp8 == $temp4 || $temp7 == $temp4) {
                        $temp5 = $myList->id;
                        $y = $myList->description;
                        $z = $myList->tag1;
                        $this->document->delete_file($temp5);
                    }
                }
            }
            $user_id = $this->core_layout->getUserId();
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            for ($i = 0; $i < count($t1); $i++) {
                $data = array(
                    'description' => $this->input->post('description'),
                    'tag1' => $this->input->post('tag_temp'),
                    'filename' => $t1[$i],
                    'modify_by' => $user_id,
                    'modify_dt' => $date,
                );
                $this->document->update_file(array('id' => $id), $data);
            }
            echo json_encode(array("status" => TRUE));
        }

        public function delete_file($id)
        {
            $this->document->delete_file($id);
            echo json_encode(array("status" => TRUE));
        }

        function get_memo_collection()
        {
            $data = $this->document->getMemoCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_favorite_collection()
        {
            $data = $this->document->getFavCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        function get_supersede_collection()
        {
            $data = $this->document->getSuperCollection();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

        public function add_memo()
        {
             echo json_encode($this->document->saveMemo());
        }

        public function edit_memo($id)
        {
            $data = $this->document->edit_memo($id);
            echo json_encode($data);
        }

        public function favorite_memo($id)
        {

            $data = array(
                'is_fav' => 1
            );
            $this->document->update_memo(array('id' => $id), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function unfavorite_memo($id)
        {

            $data = array(
                'is_fav' => 0
            );
            $this->document->update_memo(array('id' => $id), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function supersede_memo($id)
        {

            $data = array(
                'is_super' => 1
            );
            $this->document->update_memo(array('id' => $id), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function unsupersede_memo($id)
        {

            $data = array(
                'is_super' => 0
            );
            $this->document->update_memo(array('id' => $id), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function update_memo($id)
        {
            $user_id = $this->core_layout->getUserId();
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'number' => $this->input->post('number'),
                'year' => $this->input->post('year'),
                'subject' => $this->input->post('subject'),
                'tag1' => $this->input->post('tag_temp'),
                'filename' => $this->input->post('doc_filename'),
                'modify_by' => $user_id,
                'modify_dt' => $date,
            );
            $this->document->update_memo(array('id' => $id), $data);
            echo json_encode(array("status" => TRUE));
        }

        public function delete_memo($id)
        {
            $this->document->delete_memo($id);
            echo json_encode(array("status" => TRUE));
        }

        public function count_document()
        {

            $count = $this->document->count_document();
            $total = $this->document->count_document();


            echo json_encode(array("count" => $count, "total" => $total));
        }

        public function count_recent()
        {

            $count = $this->document->count_recent();
            $total = $this->document->count_document();


            echo json_encode(array("count" => $count, "total" => $total));
        }

        function get_count()
        {
            $data = $this->document->countClass();

            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
        }

    }